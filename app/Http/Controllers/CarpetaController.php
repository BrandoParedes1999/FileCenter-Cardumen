<?php

namespace App\Http\Controllers;

use App\Models\Carpeta;
use App\Models\Empresa;
use App\Models\PermisoCarpeta;
use App\Models\RegistroActividad;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CarpetaController extends Controller
{
    public function index(): View
    {
        $usuario = Auth::user();

        if (in_array($usuario->rol, ['Superadmin', 'Aux_QHSE'])) {
            $carpetas = Carpeta::with(['empresa', 'hijos', 'creadoPor'])
                ->withCount(['archivos' => fn($q) => $q->where('esta_eliminado', false)])
                ->whereNull('padre_id')
                ->orderByRaw('(SELECT es_corporativo FROM empresas WHERE empresas.id = carpetas.empresa_id) DESC')
                ->orderBy('empresa_id')
                ->orderBy('nombre')
                ->get();
        } else {
            $carpetas = Carpeta::with(['empresa', 'hijos', 'creadoPor'])
                ->withCount(['archivos' => fn($q) => $q->where('esta_eliminado', false)])
                ->whereNull('padre_id')
                ->where(function ($q) use ($usuario) {
                    $q->where('empresa_id', $usuario->empresa_id)
                    ->orWhereHas('empresa', fn($e) => $e->where('es_corporativo', true));
                })
                ->orderByRaw('(SELECT es_corporativo FROM empresas WHERE empresas.id = carpetas.empresa_id) DESC')
                ->orderBy('nombre')
                ->get();
        }

        return view('carpetas.index', compact('carpetas'));
    }

    public function show(Carpeta $carpeta): View
    {
        $usuario = Auth::user();

        if (!$carpeta->usuarioPuedeLeer($usuario)) {
            abort(403, 'No tienes permiso para ver esta carpeta.');
        }

        $carpeta->load(['hijos', 'creadoPor', 'empresa']);

        $archivos = $carpeta->archivos()
            ->where('esta_eliminado', false)
            ->with('subidoPor')
            ->orderBy('nombre_original')
            ->get();

        $migas = $this->generarMigas($carpeta);

        // Solicitudes de subida pendientes en esta carpeta (para Admin/Gerente)
        $solicitudesSubidaPendientes = 0;
        if (in_array($usuario->rol, ['Superadmin', 'Aux_QHSE', 'Admin', 'Gerente'])) {
            $solicitudesSubidaPendientes = $carpeta->solicitudesSubida()
                ->where('status', 'Pendiente')
                ->count();
        }

        RegistroActividad::registrar('ver', 'carpeta', $carpeta->id, "Vista: {$carpeta->nombre}");

        return view('carpetas.show', compact(
            'carpeta', 'archivos', 'migas', 'solicitudesSubidaPendientes'
        ));
    }

    public function create(Request $request): View
    {
        $usuario  = Auth::user();
        $padre    = $request->query('padre_id') ? Carpeta::findOrFail($request->query('padre_id')) : null;
        $empresas = collect();

        // Bug 1 fix: capturar empresa_id del query string para pre-seleccionarla
        // (cuando se llega desde areas.show o carpetas.index con un contexto de empresa)
        $empresaIdPreseleccionada = $request->query('empresa_id');

        if (in_array($usuario->rol, ['Superadmin', 'Aux_QHSE']) && !$padre) {
            $empresas = Empresa::where('activo', true)
                ->orderByDesc('es_corporativo')
                ->orderBy('nombre')
                ->get();
        }

        return view('carpetas.create', compact('padre', 'usuario', 'empresas', 'empresaIdPreseleccionada'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Carpeta::class);

        $usuario = Auth::user();

        $validated = $request->validate([
            'nombre'                     => ['required', 'string', 'max:245'],
            'empresa_id'                 => ['nullable', 'exists:empresas,id'],
            'padre_id'                   => ['nullable', 'exists:carpetas,id'],
            'es_publico'                 => ['boolean'],
            'modo_acceso'                => ['required', 'in:solo_lectura,con_descarga,normal'],
            'requiere_aprobacion_subida' => ['boolean'],
        ], [
            'nombre.required' => 'El nombre de la carpeta es obligatorio.',
            'nombre.max'      => 'El nombre no puede superar 245 caracteres.',
        ]);

        $empresaId = in_array($usuario->rol, ['Superadmin', 'Aux_QHSE'])
            ? ($validated['empresa_id'] ?? $usuario->empresa_id)
            : $usuario->empresa_id;

        $path      = $this->calcularPath($validated['padre_id'] ?? null, $validated['nombre']);
        $esPublico = $validated['es_publico'] ?? false;

        $carpeta = Carpeta::create([
            'empresa_id'                 => $empresaId,
            'padre_id'                   => $validated['padre_id'] ?? null,
            'nombre'                     => $validated['nombre'],
            'path'                       => $path,
            'es_publico'                 => $esPublico,
            'modo_acceso'                => $validated['modo_acceso'],
            'requiere_aprobacion_subida' => $validated['requiere_aprobacion_subida'] ?? false,
            'creado_por'                 => $usuario->id,
        ]);

        // Auto-crear permisos para todos los roles
        if (!$esPublico) {
            $this->crearPermisosIniciales($carpeta, $usuario->id);
        }

        RegistroActividad::registrar('crear_carpeta', 'carpeta', $carpeta->id, "Creó carpeta: {$carpeta->nombre}");

        return redirect()
            ->route('carpetas.show', $carpeta)
            ->with('success', "Carpeta \"{$carpeta->nombre}\" creada correctamente.");
    }

    public function edit(Carpeta $carpeta): View
    {
        $this->authorize('update', $carpeta);
        return view('carpetas.edit', compact('carpeta'));
    }

    public function update(Request $request, Carpeta $carpeta): RedirectResponse
    {
        $this->authorize('update', $carpeta);

        $validated = $request->validate([
            'nombre'                     => ['required', 'string', 'max:245'],
            'es_publico'                 => ['boolean'],
            'modo_acceso'                => ['required', 'in:solo_lectura,con_descarga,normal'],
            'requiere_aprobacion_subida' => ['boolean'],
        ]);

        $nombreAnterior = $carpeta->nombre;
        $eraPublica     = $carpeta->es_publico;
        $ahoraPublica   = $validated['es_publico'] ?? false;

        $carpeta->update([
            'nombre'                     => $validated['nombre'],
            'es_publico'                 => $ahoraPublica,
            'modo_acceso'                => $validated['modo_acceso'],
            'requiere_aprobacion_subida' => $validated['requiere_aprobacion_subida'] ?? false,
            'path'                       => $this->calcularPath($carpeta->padre_id, $validated['nombre']),
        ]);

        if ($eraPublica && !$ahoraPublica) {
            $this->crearPermisosIniciales($carpeta, Auth::id());
        }

        RegistroActividad::registrar(
            'editar', 'carpeta', $carpeta->id,
            "Renombró: \"{$nombreAnterior}\" → \"{$carpeta->nombre}\""
        );

        return redirect()
            ->route('carpetas.show', $carpeta)
            ->with('success', 'Carpeta actualizada correctamente.');
    }

    public function destroy(Carpeta $carpeta): RedirectResponse
    {
        $this->authorize('delete', $carpeta);

        $padreId = $carpeta->padre_id;
        $nombre  = $carpeta->nombre;

        if ($carpeta->hijos()->whereNull('deleted_at')->exists()) {
            return back()->withErrors(['error' => 'No puedes eliminar una carpeta que tiene subcarpetas.']);
        }
        if ($carpeta->archivos()->where('esta_eliminado', false)->exists()) {
            return back()->withErrors(['error' => 'No puedes eliminar una carpeta con archivos activos.']);
        }

        $carpeta->delete();

        RegistroActividad::registrar('eliminar', 'carpeta', $carpeta->id, "Eliminó carpeta: \"{$nombre}\"");

        return redirect(
            $padreId ? route('carpetas.show', $padreId) : route('carpetas.index')
        )->with('success', "Carpeta \"{$nombre}\" eliminada.");
    }

    // ── Helpers privados ──────────────────────────────────────────────────

    private function calcularPath(?int $padreId, string $nombre): string
    {
        if (!$padreId) return '/' . $nombre;
        $padre = Carpeta::find($padreId);
        return rtrim($padre->path, '/') . '/' . $nombre;
    }

    private function generarMigas(Carpeta $carpeta): array
    {
        $migas  = [];
        $actual = $carpeta;
        while ($actual) {
            array_unshift($migas, ['nombre' => $actual->nombre, 'id' => $actual->id]);
            $actual = $actual->padre;
        }
        return $migas;
    }

    /**
     * Crea permisos iniciales para TODOS los roles de la empresa.
     *
     * Bug 3 fix: ahora también crea entradas para Auxiliar y Empleado,
     * de modo que aparecen en la pantalla de permisos de la carpeta
     * y el administrador puede ajustar sus capacidades fácilmente.
     *
     * Permisos por defecto:
     *   - Admin / Gerente: acceso completo
     *   - Auxiliar:        leer + subir + descargar (sin editar ni borrar)
     *   - Empleado:        leer + descargar (solo lectura con descarga)
     */
    private function crearPermisosIniciales(Carpeta $carpeta, int $creadoPor): void
    {
        $esCorporativo = \App\Models\Empresa::where('id', $carpeta->empresa_id)
            ->where('es_corporativo', true)->exists();

        if ($esCorporativo) {
            // Permisos globales para todos los roles (empresa_id = null → aplica a todas)
            $permisosRol = [
                'Admin'    => ['leer' => true,  'subir' => false, 'editar' => false, 'borrar' => false, 'descargar' => true],
                'Gerente'  => ['leer' => true,  'subir' => false, 'editar' => false, 'borrar' => false, 'descargar' => true],
                'Auxiliar' => ['leer' => true,  'subir' => false, 'editar' => false, 'borrar' => false, 'descargar' => true],
                'Empleado' => ['leer' => true,  'subir' => false, 'editar' => false, 'borrar' => false, 'descargar' => true],
            ];

            foreach ($permisosRol as $rol => $caps) {
                PermisoCarpeta::updateOrCreate(
                    ['carpeta_id' => $carpeta->id, 'empresa_id' => null, 'rol' => $rol, 'usuario_id' => null],
                    [
                        'puede_leer'      => $caps['leer'],
                        'puede_subir'     => $caps['subir'],
                        'puede_editar'    => $caps['editar'],
                        'puede_borrar'    => $caps['borrar'],
                        'puede_descargar' => $caps['descargar'],
                        'heredar'         => true,
                        'concedido_por'   => $creadoPor,
                    ]
                );
            }
        } else {
            // Permisos por empresa específica
            $permisosRol = [
                'Admin'    => ['leer' => true,  'subir' => true,  'editar' => true,  'borrar' => true,  'descargar' => true],
                'Gerente'  => ['leer' => true,  'subir' => true,  'editar' => true,  'borrar' => true,  'descargar' => true],
                'Auxiliar' => ['leer' => true,  'subir' => true,  'editar' => false, 'borrar' => false, 'descargar' => true],
                'Empleado' => ['leer' => true,  'subir' => false, 'editar' => false, 'borrar' => false, 'descargar' => true],
            ];

            foreach ($permisosRol as $rol => $caps) {
                PermisoCarpeta::updateOrCreate(
                    ['carpeta_id' => $carpeta->id, 'empresa_id' => $carpeta->empresa_id, 'rol' => $rol, 'usuario_id' => null],
                    [
                        'puede_leer'      => $caps['leer'],
                        'puede_subir'     => $caps['subir'],
                        'puede_editar'    => $caps['editar'],
                        'puede_borrar'    => $caps['borrar'],
                        'puede_descargar' => $caps['descargar'],
                        'heredar'         => true,
                        'concedido_por'   => $creadoPor,
                    ]
                );
            }
        }
    }
}