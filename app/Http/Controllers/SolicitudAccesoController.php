<?php

namespace App\Http\Controllers;

use App\Models\Archivo;
use App\Models\Carpeta;
use App\Models\Empresa;
use App\Models\RegistroActividad;
use App\Models\SolicitudAcceso;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SolicitudAccesoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // ─────────────────────────────────────────────
    // INDEX — lista de solicitudes
    // ─────────────────────────────────────────────

    public function index(): View
    {
        $usuario = Auth::user();

        // Superadmin y Aux_QHSE ven todas las solicitudes
        if (in_array($usuario->rol, ['Superadmin', 'Aux_QHSE'])) {
            $solicitudes = SolicitudAcceso::with(['solicitante', 'empresaObjetivo', 'carpeta', 'archivo', 'revisor'])
                ->orderByRaw("FIELD(status, 'Pendiente', 'Aprobado', 'Rechazado')")
                ->orderBy('created_at', 'desc')
                ->paginate(25);

            return view('solicitudes.index', compact('solicitudes'));
        }

        // Admin y Gerente ven solicitudes dirigidas a su empresa
        if (in_array($usuario->rol, ['Admin', 'Gerente'])) {
            $solicitudes = SolicitudAcceso::with(['solicitante', 'carpeta', 'archivo', 'revisor'])
                ->where('empresa_objetivo_id', $usuario->empresa_id)
                ->orderByRaw("FIELD(status, 'Pendiente', 'Aprobado', 'Rechazado')")
                ->orderBy('created_at', 'desc')
                ->paginate(25);

            return view('solicitudes.index', compact('solicitudes'));
        }

        // El resto solo ve sus propias solicitudes
        $solicitudes = SolicitudAcceso::with(['empresaObjetivo', 'carpeta', 'archivo', 'revisor'])
            ->where('solicitante_id', $usuario->id)
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        return view('solicitudes.mis_solicitudes', compact('solicitudes'));
    }

    // ─────────────────────────────────────────────
    // SHOW
    // ─────────────────────────────────────────────

    public function show(SolicitudAcceso $solicitud): View
    {
        $this->autorizarVer($solicitud);

        $solicitud->load(['solicitante', 'empresaObjetivo', 'carpeta', 'archivo', 'revisor']);

        return view('solicitudes.show', compact('solicitud'));
    }

    // ─────────────────────────────────────────────
    // CREATE / STORE — crear solicitud
    // ─────────────────────────────────────────────

    public function create(Request $request): View
    {
        $carpetaId = $request->query('carpeta_id');
        $archivoId = $request->query('archivo_id');

        $carpeta = $carpetaId ? Carpeta::findOrFail($carpetaId) : null;
        $archivo = $archivoId ? Archivo::findOrFail($archivoId) : null;
        $empresas = Empresa::where('activo', true)->orderBy('nombre')->get();

        return view('solicitudes.create', compact('carpeta', 'archivo', 'empresas'));
    }

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

        // No puede solicitar acceso a su propia empresa (ya lo tiene)
        if ((int)$validated['empresa_objetivo_id'] === (int)$usuario->empresa_id) {
            return back()->withErrors(['empresa_objetivo_id' => 'No puedes solicitar acceso a tu propia empresa.']);
        }

        // Verificar que no tenga ya una solicitud pendiente para el mismo recurso
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

        return redirect()
            ->route('solicitudes.show', $solicitud)
            ->with('success', 'Solicitud enviada correctamente. Será revisada por un administrador.');
    }

    // ─────────────────────────────────────────────
    // APROBAR
    // ─────────────────────────────────────────────

    public function aprobar(Request $request, SolicitudAcceso $solicitud): RedirectResponse
    {
        $this->autorizarRevisar($solicitud);

        if (!$solicitud->estaPendiente()) {
            return back()->withErrors(['error' => 'Esta solicitud ya fue procesada.']);
        }

        $request->validate([
            'comentario_revisor' => ['nullable', 'string', 'max:500'],
            'caduca_en'          => ['nullable', 'date', 'after:today'],
        ]);

        $caduca = $request->caduca_en ? \Carbon\Carbon::parse($request->caduca_en) : null;

        $solicitud->aprobar(Auth::id(), $request->comentario_revisor, $caduca);

        RegistroActividad::registrar(
            'aprobar_solicitud', 'solicitud', $solicitud->id,
            "Aprobó solicitud de {$solicitud->solicitante->nombre_completo}"
        );

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

        if (!$solicitud->estaPendiente()) {
            return back()->withErrors(['error' => 'Esta solicitud ya fue procesada.']);
        }

        $request->validate([
            'comentario_revisor' => ['required', 'string', 'max:500'],
        ], [
            'comentario_revisor.required' => 'Debes indicar el motivo del rechazo.',
        ]);

        $solicitud->rechazar(Auth::id(), $request->comentario_revisor);

        RegistroActividad::registrar(
            'rechazar_solicitud', 'solicitud', $solicitud->id,
            "Rechazó solicitud de {$solicitud->solicitante->nombre_completo}"
        );

        return redirect()
            ->route('solicitudes.index')
            ->with('success', 'Solicitud rechazada.');
    }

    // ─────────────────────────────────────────────
    // HELPERS PRIVADOS
    // ─────────────────────────────────────────────

    private function autorizarVer(SolicitudAcceso $solicitud): void
    {
        $usuario = Auth::user();

        $puedeVer = in_array($usuario->rol, ['Superadmin', 'Aux_QHSE'])
            || $solicitud->solicitante_id === $usuario->id
            || (in_array($usuario->rol, ['Admin', 'Gerente']) && $solicitud->empresa_objetivo_id === $usuario->empresa_id);

        if (!$puedeVer) {
            abort(403, 'No tienes permiso para ver esta solicitud.');
        }
    }

    private function autorizarRevisar(SolicitudAcceso $solicitud): void
    {
        $usuario = Auth::user();

        $puedeRevisar = in_array($usuario->rol, ['Superadmin', 'Aux_QHSE'])
            || (in_array($usuario->rol, ['Admin', 'Gerente']) && $solicitud->empresa_objetivo_id === $usuario->empresa_id);

        if (!$puedeRevisar) {
            abort(403, 'No tienes permiso para revisar esta solicitud.');
        }
    }
}
