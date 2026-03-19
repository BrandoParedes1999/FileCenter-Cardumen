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

class PermisoCarpetaController extends Controller
{
    public function index(Carpeta $carpeta): View
    {
        $this->autorizarGestionar($carpeta);

        $permisos = $carpeta->permisos()
            ->with(['usuario', 'empresa', 'concedidoPor'])
            ->get();

        $usuarios = Usuario::deEmpresa($carpeta->empresa_id)->activos()->orderBy('paterno')->get();
        $empresas = Empresa::where('activo', true)->orderBy('nombre')->get();

        return view('permisos.index', compact('carpeta', 'permisos', 'usuarios', 'empresas'));
    }

    public function store(Request $request, Carpeta $carpeta): RedirectResponse
    {
        $this->autorizarGestionar($carpeta);

        $validated = $request->validate([
            'usuario_id'      => ['nullable', 'exists:usuarios,id'],
            'empresa_id'      => ['nullable', 'exists:empresas,id'],
            'rol'             => ['nullable', 'in:Admin,Gerente,Auxiliar,Empleado'],
            'puede_leer'      => ['boolean'],
            'puede_subir'     => ['boolean'],
            'puede_editar'    => ['boolean'],
            'puede_borrar'    => ['boolean'],
            'puede_descargar' => ['boolean'],
            'heredar'         => ['boolean'],
        ]);

        if (!$validated['usuario_id'] && !($validated['empresa_id'] && $validated['rol'])) {
            return back()->withErrors(['error' => 'Debes especificar un usuario, o una empresa con rol.']);
        }

        $existe = PermisoCarpeta::where('carpeta_id', $carpeta->id)
            ->when($validated['usuario_id'], fn($q) => $q->where('usuario_id', $validated['usuario_id']))
            ->when(!$validated['usuario_id'], fn($q) =>
                $q->where('empresa_id', $validated['empresa_id'])
                    ->where('rol', $validated['rol'])
            )->exists();

        if ($existe) {
            return back()->withErrors(['error' => 'Ya existe un permiso para este usuario o rol en esta carpeta.']);
        }

        $permiso = PermisoCarpeta::create([
            'carpeta_id'      => $carpeta->id,
            'usuario_id'      => $validated['usuario_id'] ?? null,
            'empresa_id'      => $validated['empresa_id'] ?? null,
            'rol'             => $validated['rol'] ?? null,
            'puede_leer'      => $validated['puede_leer'] ?? false,
            'puede_subir'     => $validated['puede_subir'] ?? false,
            'puede_editar'    => $validated['puede_editar'] ?? false,
            'puede_borrar'    => $validated['puede_borrar'] ?? false,
            'puede_descargar' => $validated['puede_descargar'] ?? false,
            'heredar'         => $validated['heredar'] ?? true,
            'concedido_por'   => Auth::id(),
        ]);

        RegistroActividad::registrar('editar', 'carpeta', $carpeta->id,
            "Permiso otorgado en carpeta: {$carpeta->nombre}");

        return redirect()->route('permisos.index', $carpeta)->with('success', 'Permiso otorgado correctamente.');
    }

    public function update(Request $request, Carpeta $carpeta, PermisoCarpeta $permiso): RedirectResponse
    {
        $this->autorizarGestionar($carpeta);

        $validated = $request->validate([
            'puede_leer'      => ['boolean'],
            'puede_subir'     => ['boolean'],
            'puede_editar'    => ['boolean'],
            'puede_borrar'    => ['boolean'],
            'puede_descargar' => ['boolean'],
            'heredar'         => ['boolean'],
        ]);

        $permiso->update($validated);

        RegistroActividad::registrar('editar', 'carpeta', $carpeta->id,
            "Permiso actualizado (id={$permiso->id}) en: {$carpeta->nombre}");

        return redirect()->route('permisos.index', $carpeta)->with('success', 'Permiso actualizado.');
    }

    public function destroy(Carpeta $carpeta, PermisoCarpeta $permiso): RedirectResponse
    {
        $this->autorizarGestionar($carpeta);

        $permiso->delete();

        RegistroActividad::registrar('editar', 'carpeta', $carpeta->id,
            "Permiso revocado (id={$permiso->id}) en: {$carpeta->nombre}");

        return redirect()->route('permisos.index', $carpeta)->with('success', 'Permiso revocado.');
    }

    private function autorizarGestionar(Carpeta $carpeta): void
    {
        $usuario = Auth::user();

        if (in_array($usuario->rol, ['Superadmin', 'Aux_QHSE'])) return;

        if (in_array($usuario->rol, ['Admin', 'Gerente'])
            && $carpeta->empresa_id === $usuario->empresa_id) return;

        abort(403, 'No tienes permiso para gestionar los permisos de esta carpeta.');
    }
}