<?php

namespace App\Http\Controllers;

use App\Models\Carpeta;
use App\Models\RegistroActividad;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CarpetaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->authorizeResource(Carpeta::class, 'carpeta');
    }

    // ─────────────────────────────────────────────
    // INDEX — árbol de carpetas de la empresa
    // ─────────────────────────────────────────────

    public function index(): View
    {
        $usuario = Auth::user();

        // Superadmin/Aux_QHSE ven todas las empresas
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

    // ─────────────────────────────────────────────
    // SHOW — contenido de una carpeta
    // ─────────────────────────────────────────────

    public function show(Carpeta $carpeta): View
    {
        $usuario = Auth::user();

        // Verificar permiso de lectura usando el modelo
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

    // ─────────────────────────────────────────────
    // CREATE / STORE
    // ─────────────────────────────────────────────

    public function create(Request $request): View
    {
        $usuario   = Auth::user();
        $padreId   = $request->query('padre_id');
        $padre     = $padreId ? Carpeta::findOrFail($padreId) : null;

        return view('carpetas.create', compact('padre', 'usuario'));
    }

    public function store(Request $request): RedirectResponse
    {
        $usuario = Auth::user();

        $validated = $request->validate([
            'nombre'    => ['required', 'string', 'max:245'],
            'padre_id'  => ['nullable', 'exists:carpetas,id'],
            'es_publico' => ['boolean'],
        ], [
            'nombre.required' => 'El nombre de la carpeta es obligatorio.',
            'nombre.max'      => 'El nombre no puede superar 245 caracteres.',
        ]);

        // Calcular path automáticamente
        $path = $this->calcularPath($validated['padre_id'] ?? null, $validated['nombre']);

        $carpeta = Carpeta::create([
            'empresa_id' => $usuario->empresa_id,
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

    // ─────────────────────────────────────────────
    // EDIT / UPDATE
    // ─────────────────────────────────────────────

    public function edit(Carpeta $carpeta): View
    {
        return view('carpetas.edit', compact('carpeta'));
    }

    public function update(Request $request, Carpeta $carpeta): RedirectResponse
    {
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
            "Renombró carpeta: \"{$nombreAnterior}\" → \"{$carpeta->nombre}\""
        );

        return redirect()
            ->route('carpetas.show', $carpeta)
            ->with('success', 'Carpeta actualizada correctamente.');
    }

    // ─────────────────────────────────────────────
    // DESTROY — soft delete
    // ─────────────────────────────────────────────

    public function destroy(Carpeta $carpeta): RedirectResponse
    {
        $padreId = $carpeta->padre_id;
        $nombre  = $carpeta->nombre;

        // No permitir borrar si tiene hijos activos
        if ($carpeta->hijos()->whereNull('deleted_at')->exists()) {
            return back()->withErrors(['error' => 'No puedes eliminar una carpeta que tiene subcarpetas. Elimínalas primero.']);
        }

        // No permitir borrar si tiene archivos activos
        if ($carpeta->archivos()->where('esta_eliminado', false)->exists()) {
            return back()->withErrors(['error' => 'No puedes eliminar una carpeta con archivos activos. Mueve o elimina los archivos primero.']);
        }

        $carpeta->delete();

        RegistroActividad::registrar('eliminar', 'carpeta', $carpeta->id, "Eliminó carpeta: \"{$nombre}\"");

        $redirect = $padreId
            ? route('carpetas.show', $padreId)
            : route('carpetas.index');

        return redirect($redirect)->with('success', "Carpeta \"{$nombre}\" eliminada.");
    }

    // ─────────────────────────────────────────────
    // HELPERS PRIVADOS
    // ─────────────────────────────────────────────

    private function calcularPath(?int $padreId, string $nombre): string
    {
        if (!$padreId) {
            return '/' . $nombre;
        }

        $padre = Carpeta::find($padreId);
        return rtrim($padre->path, '/') . '/' . $nombre;
    }

    private function generarMigas(Carpeta $carpeta): array
    {
        $migas   = [];
        $actual  = $carpeta;

        while ($actual) {
            array_unshift($migas, ['nombre' => $actual->nombre, 'id' => $actual->id]);
            $actual = $actual->padre;
        }

        return $migas;
    }
}
