<?php

namespace App\Http\Controllers;

use App\Models\Archivo;
use App\Models\Carpeta;
use App\Models\Empresa;
use App\Models\RegistroActividad;
use App\Models\SolicitudAcceso;
use App\Models\Usuario;
use App\Notifications\SolicitudAccesoRecibida;
use App\Notifications\SolicitudAccesoResuelta;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SolicitudAccesoController extends Controller
{
    public function index(Request $request): View
    {
        $usuario     = Auth::user();
        $filtroStatus = $request->query('status'); // null = todas
        $statuses    = ['Pendiente', 'Aprobado', 'Rechazado'];

        // ── Closure que construye la query base según el rol ──────────────
        $baseQuery = function () use ($usuario) {
            if (in_array($usuario->rol, ['Superadmin', 'Aux_QHSE'])) {
                // Ven TODAS las solicitudes
                return SolicitudAcceso::with([
                    'solicitante.empresa',
                    'empresaObjetivo',
                    'carpeta',
                    'archivo.carpeta',
                    'revisor',
                ]);
            }

            if (in_array($usuario->rol, ['Admin', 'Gerente'])) {
                // Ven las solicitudes dirigidas a su empresa
                return SolicitudAcceso::with([
                    'solicitante.empresa',
                    'empresaObjetivo',
                    'carpeta',
                    'archivo.carpeta',
                    'revisor',
                ])->where('empresa_objetivo_id', $usuario->empresa_id);
            }

            // Auxiliar / Empleado — solo sus propias solicitudes
            return SolicitudAcceso::with([
                'empresaObjetivo',
                'carpeta',
                'archivo.carpeta',
                'revisor',
            ])->where('solicitante_id', $usuario->id);
        };

        // ── Conteos por status (sin filtro de status aplicado) ────────────
        $conteos = array_combine(
            $statuses,
            array_map(fn($s) => (clone $baseQuery())->where('status', $s)->count(), $statuses)
        );

        // ── Query paginada con filtro opcional ───────────────────────────
        $query = $baseQuery();

        if ($filtroStatus && in_array($filtroStatus, $statuses)) {
            $query->where('status', $filtroStatus);
        }

        // Empleados ven sus solicitudes en orden cronológico inverso;
        // admins ven pendientes primero para facilitar revisión.
        if (in_array($usuario->rol, ['Superadmin', 'Aux_QHSE', 'Admin', 'Gerente'])) {
            $query->orderByRaw("CASE status
                WHEN 'Pendiente' THEN 1
                WHEN 'Aprobado'  THEN 2
                WHEN 'Rechazado' THEN 3
                ELSE 4 END");
        }

        $solicitudes = $query
            ->orderBy('created_at', 'desc')
            ->paginate(25)
            ->withQueryString();

        return view('solicitudes.index', compact('solicitudes', 'filtroStatus', 'conteos'));
    }

    // ─────────────────────────────────────────────
    // SHOW
    // ─────────────────────────────────────────────

    public function show(SolicitudAcceso $solicitud): View
    {
        $this->autorizarVer($solicitud);

        $solicitud->load([
            'solicitante.empresa',
            'empresaObjetivo',
            'carpeta',
            'archivo.carpeta',
            'revisor',
        ]);

        return view('solicitudes.show', compact('solicitud'));
    }

    // ─────────────────────────────────────────────
    // CREATE
    // ─────────────────────────────────────────────

    public function create(Request $request): View
    {
        $carpetaId = $request->query('carpeta_id');
        $archivoId = $request->query('archivo_id');

        $carpeta  = $carpetaId ? Carpeta::findOrFail($carpetaId) : null;
        $archivo  = $archivoId ? Archivo::with('carpeta')->findOrFail($archivoId) : null;
        $empresas = Empresa::where('activo', true)->orderBy('nombre')->get();

        return view('solicitudes.create', compact('carpeta', 'archivo', 'empresas'));
    }

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'empresa_objetivo_id' => ['required', 'exists:empresas,id'],
            'carpeta_id'          => ['nullable', 'exists:carpetas,id'],
            'archivo_id'          => ['nullable', 'exists:archivos,id'],
            'razon'               => ['required', 'string', 'min:10', 'max:1000'],
            'tipo_acceso'         => ['required', 'in:Lectura,Descargar'],
        ], [
            'razon.required' => 'Debes justificar tu solicitud.',
            'razon.min'      => 'La justificación debe tener al menos 10 caracteres.',
        ]);

        $usuario = Auth::user();

        if ((int) $validated['empresa_objetivo_id'] === (int) $usuario->empresa_id) {
            return back()->withErrors(['empresa_objetivo_id' => 'No puedes solicitar acceso a tu propia empresa.']);
        }

        $existente = SolicitudAcceso::where('solicitante_id', $usuario->id)
            ->where('empresa_objetivo_id', $validated['empresa_objetivo_id'])
            ->where('carpeta_id', $validated['carpeta_id'] ?? null)
            ->where('archivo_id', $validated['archivo_id'] ?? null)
            ->where('status', 'Pendiente')
            ->exists();

        if ($existente) {
            return back()->withErrors(['error' => 'Ya tienes una solicitud pendiente para este recurso.']);
        }

        $solicitud = SolicitudAcceso::create([
            'solicitante_id'      => $usuario->id,
            'empresa_objetivo_id' => $validated['empresa_objetivo_id'],
            'carpeta_id'          => $validated['carpeta_id'] ?? null,
            'archivo_id'          => $validated['archivo_id'] ?? null,
            'razon'               => $validated['razon'],
            'tipo_acceso'         => $validated['tipo_acceso'],
            'status'              => 'Pendiente',
        ]);

        RegistroActividad::registrar(
            'solicitar_acceso', 'solicitud', $solicitud->id,
            "Solicitó acceso a empresa_id={$validated['empresa_objetivo_id']}"
        );

        // Notificar a Admin y Gerente de la empresa objetivo
        $solicitud->load(['carpeta', 'archivo', 'solicitante']);
        try {
            Usuario::where('empresa_id', $validated['empresa_objetivo_id'])
                ->whereIn('rol', ['Admin', 'Gerente'])
                ->where('es_activo', true)
                ->each(fn($admin) => $admin->notify(new SolicitudAccesoRecibida($solicitud)));
        } catch (\Throwable) {}

        return redirect()
            ->route('solicitudes.index')
            ->with('success', 'Tu solicitud de acceso fue enviada correctamente y está pendiente de revisión por parte del administrador.');
    }

    // ─────────────────────────────────────────────
    // APROBAR
    // ─────────────────────────────────────────────

    public function aprobar(Request $request, SolicitudAcceso $solicitud): RedirectResponse
    {
        $this->autorizarRevisar($solicitud);

        if (! $solicitud->estaPendiente()) {
            return back()->withErrors(['error' => 'Esta solicitud ya fue procesada.']);
        }

        $request->validate([
            'comentario_revisor' => ['nullable', 'string', 'max:500'],
            'caduca_en'          => ['nullable', 'date', 'after:today'],
        ]);

        $caduca = $request->caduca_en ? \Carbon\Carbon::parse($request->caduca_en) : null;

        $solicitud->aprobar(Auth::id(), $request->comentario_revisor, $caduca);
        $solicitud->load(['carpeta', 'archivo', 'revisor', 'solicitante']);

        // Descartar notificaciones pendientes de todos los admins para esta solicitud
        DB::table('notifications')
            ->whereNull('read_at')
            ->whereJsonContains('data->solicitud_id', $solicitud->id)
            ->update(['read_at' => now()]);

        RegistroActividad::registrar(
            'aprobar_solicitud', 'solicitud', $solicitud->id,
            "Aprobó solicitud de {$solicitud->solicitante->nombre_completo}"
        );

        try {
            $solicitud->solicitante->notify(new SolicitudAccesoResuelta($solicitud));
        } catch (\Throwable) {}

        return redirect()
            ->route('solicitudes.index')
            ->with('success', 'Solicitud aprobada. El permiso fue otorgado automáticamente.');
    }

    // ─────────────────────────────────────────────
    // RECHAZAR
    // ─────────────────────────────────────────────

    public function rechazar(Request $request, SolicitudAcceso $solicitud): RedirectResponse
    {
        $this->autorizarRevisar($solicitud);

        if (! $solicitud->estaPendiente()) {
            return back()->withErrors(['error' => 'Esta solicitud ya fue procesada.']);
        }

        $request->validate([
            'comentario_revisor' => ['required', 'string', 'max:500'],
        ], [
            'comentario_revisor.required' => 'Debes indicar el motivo del rechazo.',
        ]);

        $solicitud->rechazar(Auth::id(), $request->comentario_revisor);
        $solicitud->load(['carpeta', 'archivo', 'revisor', 'solicitante']);

        // Descartar notificaciones pendientes de todos los admins para esta solicitud
        DB::table('notifications')
            ->whereNull('read_at')
            ->whereJsonContains('data->solicitud_id', $solicitud->id)
            ->update(['read_at' => now()]);

        RegistroActividad::registrar(
            'rechazar_solicitud', 'solicitud', $solicitud->id,
            "Rechazó solicitud de {$solicitud->solicitante->nombre_completo}"
        );

        try {
            $solicitud->solicitante->notify(new SolicitudAccesoResuelta($solicitud));
        } catch (\Throwable) {}

        return redirect()
            ->route('solicitudes.index')
            ->with('success', 'Solicitud rechazada.');
    }

    // ─────────────────────────────────────────────
    // CARPETAS DE EMPRESA (AJAX)
    // ─────────────────────────────────────────────

    public function carpetasDeEmpresa(Empresa $empresa): JsonResponse
    {
        $carpetas = Carpeta::where('empresa_id', $empresa->id)
            ->whereNull('deleted_at')
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'path']);

        return response()->json($carpetas);
    }

    // ─────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────

    private function autorizarVer(SolicitudAcceso $solicitud): void
    {
        $usuario = Auth::user();

        $puedeVer = in_array($usuario->rol, ['Superadmin', 'Aux_QHSE'])
            || $solicitud->solicitante_id === $usuario->id
            || (in_array($usuario->rol, ['Admin', 'Gerente'])
                && $solicitud->empresa_objetivo_id === $usuario->empresa_id);

        if (! $puedeVer) {
            abort(403, 'No tienes permiso para ver esta solicitud.');
        }
    }

    private function autorizarRevisar(SolicitudAcceso $solicitud): void
    {
        $usuario = Auth::user();

        $puedeRevisar = in_array($usuario->rol, ['Superadmin', 'Aux_QHSE'])
            || (in_array($usuario->rol, ['Admin', 'Gerente'])
                && $solicitud->empresa_objetivo_id === $usuario->empresa_id);

        if (! $puedeRevisar) {
            abort(403, 'No tienes permiso para revisar esta solicitud.');
        }
    }
}