<?php

namespace App\Http\Controllers;

use App\Models\Carpeta;
use App\Models\Empresa;
use App\Models\PermisoCarpeta;
use App\Models\RegistroActividad;
use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PermisosGlobalesController extends Controller
{
    /**
     * Vista global de todos los permisos del sistema.
     * Superadmin y Aux_QHSE ven todo.
     * Admin y Gerente ven solo los de su empresa.
     */
    public function index(Request $request): View
    {
        $usuario = Auth::user();

        if (!in_array($usuario->rol, ['Superadmin', 'Aux_QHSE', 'Admin', 'Gerente'])) {
            abort(403, 'No tienes acceso a esta sección.');
        }

        $esAdmin = in_array($usuario->rol, ['Superadmin', 'Aux_QHSE']);

        // ── Filtros ──────────────────────────────────────────────
        $filtroTipo    = $request->query('tipo', 'todos');
        $filtroCarpeta = $request->query('carpeta_id');
        $filtroEmpresa = $request->query('empresa_id');
        $filtroUsuario = $request->query('usuario_id');
        $busqueda      = $request->query('q', '');

        // ── Query base ────────────────────────────────────────────
        // FIX: eager load 'carpeta.empresa' para que nunca sea null
        $query = PermisoCarpeta::with([
            'carpeta.empresa',   // ← carga empresa junto con la carpeta
            'usuario.empresa',
            'empresa',
            'concedidoPor',
        ]);

        // Filtrar por empresa según rol
        if (!$esAdmin) {
            $query->whereHas('carpeta', fn($q) => $q->where('empresa_id', $usuario->empresa_id));
        }

        // Filtro tipo
        if ($filtroTipo === 'usuario') {
            $query->whereNotNull('usuario_id');
        } elseif ($filtroTipo === 'rol') {
            $query->whereNull('usuario_id');
        }

        // Filtro carpeta específica
        if ($filtroCarpeta) {
            $query->where('carpeta_id', $filtroCarpeta);
        }

        // Filtro empresa
        if ($filtroEmpresa) {
            $query->where(function ($q) use ($filtroEmpresa) {
                $q->where('empresa_id', $filtroEmpresa)
                  ->orWhereHas('carpeta', fn($c) => $c->where('empresa_id', $filtroEmpresa))
                  ->orWhereHas('usuario', fn($u) => $u->where('empresa_id', $filtroEmpresa));
            });
        }

        // Filtro usuario específico
        if ($filtroUsuario) {
            $query->where('usuario_id', $filtroUsuario);
        }

        // Búsqueda por nombre de carpeta o usuario
        if ($busqueda) {
            $query->where(function ($q) use ($busqueda) {
                $q->whereHas('carpeta', fn($c) => $c->where('nombre', 'like', "%{$busqueda}%"))
                  ->orWhereHas('usuario', function ($u) use ($busqueda) {
                      $u->where('nombre', 'like', "%{$busqueda}%")
                        ->orWhere('paterno', 'like', "%{$busqueda}%")
                        ->orWhere('email', 'like', "%{$busqueda}%");
                  });
            });
        }

        $permisos = $query->orderBy('carpeta_id')->get();

        // ── Agrupar por carpeta ───────────────────────────────────
        $permisosPorCarpeta = $permisos->groupBy('carpeta_id');

        // ── Datos para filtros ────────────────────────────────────
        $carpetasDisponibles = Carpeta::query()
            ->when(!$esAdmin, fn($q) => $q->where('empresa_id', $usuario->empresa_id))
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'empresa_id', 'path']);

        $empresas = $esAdmin
            ? Empresa::where('activo', true)->orderByDesc('es_corporativo')->orderBy('nombre')->get()
            : Empresa::where('id', $usuario->empresa_id)->get();

        $usuariosDisponibles = Usuario::query()
            ->when(!$esAdmin, fn($q) => $q->where('empresa_id', $usuario->empresa_id))
            ->where('es_activo', true)
            ->orderBy('paterno')
            ->get(['id', 'nombre', 'paterno', 'email']);

        // ── Estadísticas ──────────────────────────────────────────
        $stats = [
            'total'                 => $permisos->count(),
            'por_usuario'           => $permisos->whereNotNull('usuario_id')->count(),
            'por_rol'               => $permisos->whereNull('usuario_id')->count(),
            'carpetas_con_permisos' => $permisos->pluck('carpeta_id')->unique()->count(),
        ];

        RegistroActividad::registrar('ver', 'sistema', null, 'Vista global de permisos');

        return view('permisos.global', compact(
            'permisosPorCarpeta',
            'permisos',
            'stats',
            'carpetasDisponibles',
            'empresas',
            'usuariosDisponibles',
            'filtroTipo',
            'filtroCarpeta',
            'filtroEmpresa',
            'filtroUsuario',
            'busqueda',
            'esAdmin',
        ));
    }

    /**
     * Actualiza un permiso individual desde la vista global.
     */
    public function update(Request $request, PermisoCarpeta $permiso): RedirectResponse
    {
        $this->autorizarEditar($permiso);

        $validated = $request->validate([
            'puede_leer'      => ['boolean'],
            'puede_subir'     => ['boolean'],
            'puede_editar'    => ['boolean'],
            'puede_borrar'    => ['boolean'],
            'puede_descargar' => ['boolean'],
            'heredar'         => ['boolean'],
        ]);

        $permiso->update($validated);

        RegistroActividad::registrar(
            'editar', 'carpeta', $permiso->carpeta_id,
            "Permiso actualizado (id={$permiso->id}) en carpeta: {$permiso->carpeta?->nombre}"
        );

        return back()->with('success', 'Permiso actualizado correctamente.');
    }

    /**
     * Elimina (revoca) un permiso desde la vista global.
     */
    public function destroy(PermisoCarpeta $permiso): RedirectResponse
    {
        $this->autorizarEditar($permiso);

        $nombreCarpeta = $permiso->carpeta?->nombre ?? '—';
        $permiso->delete();

        RegistroActividad::registrar(
            'editar', 'carpeta', $permiso->carpeta_id,
            "Permiso revocado (id={$permiso->id}) en: {$nombreCarpeta}"
        );

        return back()->with('success', 'Permiso revocado.');
    }

    // ── Helpers ────────────────────────────────────────────────────

    private function autorizarEditar(PermisoCarpeta $permiso): void
    {
        $usuario = Auth::user();

        if (in_array($usuario->rol, ['Superadmin', 'Aux_QHSE'])) return;

        if (
            in_array($usuario->rol, ['Admin', 'Gerente']) &&
            $permiso->carpeta?->empresa_id === $usuario->empresa_id
        ) return;

        abort(403, 'No tienes permiso para modificar este acceso.');
    }
}