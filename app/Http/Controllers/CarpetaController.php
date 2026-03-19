<?php

namespace App\Http\Controllers;

use App\Models\Carpeta;
use App\Models\Empresa;
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
                ->whereNull('padre_id')
                ->orderBy('empresa_id')
                ->orderBy('nombre')
                ->get();
        } else {
            $carpetas = Carpeta::with(['hijos', 'creadoPor'])
                ->where('empresa_id', $usuario->empresa_id)
                ->whereNull('padre_id')
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

        RegistroActividad::registrar('ver', 'carpeta', $carpeta->id, "Vista: {$carpeta->nombre}");

        return view('carpetas.show', compact('carpeta', 'archivos', 'migas'));
    }

    public function create(Request $request): View
    {
        $usuario  = Auth::user();
        $padre    = $request->query('padre_id') ? Carpeta::findOrFail($request->query('padre_id')) : null;
        $empresas = collect();

        // Superadmin y Aux_QHSE pueden elegir la empresa destino
        if (in_array($usuario->rol, ['Superadmin', 'Aux_QHSE']) && !$padre) {
            $empresas = Empresa::where('activo', true)
                ->orderByDesc('es_corporativo') // Corporativo primero
                ->orderBy('nombre')
                ->get();
        }

        return view('carpetas.create', compact('padre', 'usuario', 'empresas'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Carpeta::class);

        $usuario = Auth::user();

        $validated = $request->validate([
            'nombre'     => ['required', 'string', 'max:245'],
            'empresa_id' => ['nullable', 'exists:empresas,id'],
            'padre_id'   => ['nullable', 'exists:carpetas,id'],
            'es_publico' => ['boolean'],
        ], [
            'nombre.required' => 'El nombre de la carpeta es obligatorio.',
            'nombre.max'      => 'El nombre no puede superar 245 caracteres.',
        ]);

        $path = $this->calcularPath($validated['padre_id'] ?? null, $validated['nombre']);

        $carpeta = Carpeta::create([
            'empresa_id' => in_array($usuario->rol, ['Superadmin', 'Aux_QHSE'])
                ? ($validated['empresa_id'] ?? $usuario->empresa_id)
                : $usuario->empresa_id,
            'padre_id'   => $validated['padre_id'] ?? null,
            'nombre'     => $validated['nombre'],
            'path'       => $path,
            'es_publico' => $validated['es_publico'] ?? false,
            'creado_por' => $usuario->id,
        ]);

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
            'nombre'     => ['required', 'string', 'max:245'],
            'es_publico' => ['boolean'],
        ]);

        $nombreAnterior = $carpeta->nombre;

        $carpeta->update([
            'nombre'     => $validated['nombre'],
            'es_publico' => $validated['es_publico'] ?? false,
            'path'       => $this->calcularPath($carpeta->padre_id, $validated['nombre']),
        ]);

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

    // ── Helpers privados ──────────────────────────

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
}