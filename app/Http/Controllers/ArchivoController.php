<?php

namespace App\Http\Controllers;

use App\Models\Archivo;
use App\Models\Carpeta;
use App\Models\RegistroActividad;
use App\Models\VersionArchivo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ArchivoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->authorizeResource(Archivo::class, 'archivo');
    }

    // ─────────────────────────────────────────────
    // INDEX — archivos de una carpeta
    // ─────────────────────────────────────────────

    public function index(Request $request): View
    {
        $carpetaId = $request->query('carpeta_id');
        $carpeta   = $carpetaId ? Carpeta::findOrFail($carpetaId) : null;
        $usuario   = Auth::user();

        $query = Archivo::with(['carpeta', 'subidoPor'])
            ->where('esta_eliminado', false);

        if ($carpeta) {
            $query->where('carpeta_id', $carpeta->id);
        } elseif (!in_array($usuario->rol, ['Superadmin', 'Aux_QHSE'])) {
            // Limitar a archivos de la empresa del usuario
            $query->whereHas('carpeta', fn($q) => $q->where('empresa_id', $usuario->empresa_id));
        }

        $archivos = $query->orderBy('nombre_original')->paginate(30);

        return view('archivos.index', compact('archivos', 'carpeta'));
    }

    // ─────────────────────────────────────────────
    // SHOW — detalle del archivo
    // ─────────────────────────────────────────────

    public function show(Archivo $archivo): View
    {
        $archivo->load(['carpeta', 'subidoPor', 'versiones.subidoPor']);

        RegistroActividad::registrar('ver', 'archivo', $archivo->id, "Vista detalle: {$archivo->nombre_original}");

        return view('archivos.show', compact('archivo'));
    }

    // ─────────────────────────────────────────────
    // CREATE / STORE — subir archivo
    // ─────────────────────────────────────────────

    public function create(Request $request): View
    {
        $carpeta = Carpeta::findOrFail($request->query('carpeta_id'));

        return view('archivos.create', compact('carpeta'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'carpeta_id'  => ['required', 'exists:carpetas,id'],
            'archivo'     => ['required', 'file', 'max:102400'], // 100 MB máx
            'descripcion' => ['nullable', 'string', 'max:500'],
        ], [
            'archivo.required' => 'Debes seleccionar un archivo.',
            'archivo.max'      => 'El archivo no puede superar 100 MB.',
        ]);

        $usuario = Auth::user();
        $carpeta = Carpeta::findOrFail($request->carpeta_id);
        $file    = $request->file('archivo');

        // Verificar permiso de subida
        if (!$carpeta->usuarioPuedeSubir($usuario)) {
            abort(403, 'No tienes permiso para subir archivos en esta carpeta.');
        }

        // Verificar si ya existe un archivo con el mismo nombre en la misma carpeta
        $archivoExistente = Archivo::where('carpeta_id', $carpeta->id)
            ->where('nombre_original', $file->getClientOriginalName())
            ->where('esta_eliminado', false)
            ->first();

        if ($archivoExistente) {
            return $this->crearNuevaVersion($archivoExistente, $file, $usuario, $carpeta);
        }

        // Nuevo archivo
        $nombreAlmacenamiento = $this->generarNombreAlmacenamiento($file);
        $rutaDisco            = "empresa_{$carpeta->empresa_id}/carpeta_{$carpeta->id}/{$nombreAlmacenamiento}";
        $hash                 = hash_file('sha256', $file->getRealPath());

        Storage::disk('filecenter')->put($rutaDisco, file_get_contents($file->getRealPath()));

        $archivo = Archivo::create([
            'carpeta_id'            => $carpeta->id,
            'subido_por'            => $usuario->id,
            'nombre_original'       => $file->getClientOriginalName(),
            'nombre_almacenamiento' => $nombreAlmacenamiento,
            'ruta_disco'            => $rutaDisco,
            'hash_sha256'           => $hash,
            'tipo_mime'             => $file->getMimeType(),
            'extension'             => strtolower($file->getClientOriginalExtension()),
            'tamanio_bytes'         => $file->getSize(),
            'descripcion'           => $request->descripcion,
            'version'               => 1,
        ]);

        // Registrar en versiones_archivos
        VersionArchivo::create([
            'archivo_id'            => $archivo->id,
            'version'               => 1,
            'nombre_original'       => $archivo->nombre_original,
            'nombre_almacenamiento' => $nombreAlmacenamiento,
            'ruta_disco'            => $rutaDisco,
            'hash_sha256'           => $hash,
            'tamanio_bytes'         => $archivo->tamanio_bytes,
            'subido_por'            => $usuario->id,
            'nota_version'          => 'Versión inicial',
        ]);

        RegistroActividad::registrar('subir', 'archivo', $archivo->id, "Subió: {$archivo->nombre_original}");

        return redirect()
            ->route('carpetas.show', $carpeta)
            ->with('success', "Archivo \"{$archivo->nombre_original}\" subido correctamente.");
    }

    // ─────────────────────────────────────────────
    // EDIT / UPDATE — editar metadata
    // ─────────────────────────────────────────────

    public function edit(Archivo $archivo): View
    {
        return view('archivos.edit', compact('archivo'));
    }

    public function update(Request $request, Archivo $archivo): RedirectResponse
    {
        $validated = $request->validate([
            'descripcion' => ['nullable', 'string', 'max:500'],
        ]);

        $archivo->update($validated);

        RegistroActividad::registrar('editar', 'archivo', $archivo->id, "Editó metadata: {$archivo->nombre_original}");

        return redirect()
            ->route('archivos.show', $archivo)
            ->with('success', 'Archivo actualizado.');
    }

    // ─────────────────────────────────────────────
    // DESTROY — soft delete
    // ─────────────────────────────────────────────

    public function destroy(Archivo $archivo): RedirectResponse
    {
        $carpetaId = $archivo->carpeta_id;
        $nombre    = $archivo->nombre_original;

        $archivo->update(['esta_eliminado' => true]);
        $archivo->delete();

        RegistroActividad::registrar('eliminar', 'archivo', $archivo->id, "Eliminó: \"{$nombre}\"");

        return redirect()
            ->route('carpetas.show', $carpetaId)
            ->with('success', "Archivo \"{$nombre}\" eliminado.");
    }

    // ─────────────────────────────────────────────
    // DESCARGAR
    // ─────────────────────────────────────────────

    public function descargar(Archivo $archivo): StreamedResponse
    {
        $usuario = Auth::user();
        $carpeta = $archivo->carpeta;

        // Verificar permiso de descarga
        if (!in_array($usuario->rol, ['Superadmin', 'Aux_QHSE'])) {
            if (!$carpeta->usuarioPuedeDescargar($usuario)) {
                abort(403, 'No tienes permiso para descargar este archivo.');
            }
        }

        if (!Storage::disk('filecenter')->exists($archivo->ruta_disco)) {
            abort(404, 'El archivo no se encuentra en el disco.');
        }

        $archivo->incrementarDescargas();

        RegistroActividad::registrar(
            'descargar', 'archivo', $archivo->id,
            "Descargó: {$archivo->nombre_original} (v{$archivo->version})"
        );

        return Storage::disk('filecenter')->download(
            $archivo->ruta_disco,
            $archivo->nombre_original
        );
    }

    // ─────────────────────────────────────────────
    // RESTAURAR VERSIÓN ANTERIOR
    // ─────────────────────────────────────────────

    public function restaurarVersion(Request $request, Archivo $archivo): RedirectResponse
    {
        $this->authorize('update', $archivo);

        $request->validate([
            'version_id' => ['required', 'exists:versiones_archivos,id'],
        ]);

        $version = VersionArchivo::where('id', $request->version_id)
            ->where('archivo_id', $archivo->id)
            ->firstOrFail();

        // Actualizar archivo principal a los datos de la versión
        $archivo->update([
            'nombre_almacenamiento' => $version->nombre_almacenamiento,
            'ruta_disco'            => $version->ruta_disco,
            'hash_sha256'           => $version->hash_sha256,
            'tamanio_bytes'         => $version->tamanio_bytes,
            'version'               => $version->version,
        ]);

        RegistroActividad::registrar(
            'restaurar_version', 'version', $version->id,
            "Restauró \"{$archivo->nombre_original}\" a v{$version->version}"
        );

        return redirect()
            ->route('archivos.show', $archivo)
            ->with('success', "Versión {$version->version} restaurada correctamente.");
    }

    // ─────────────────────────────────────────────
    // HELPERS PRIVADOS
    // ─────────────────────────────────────────────

    private function generarNombreAlmacenamiento($file): string
    {
        return Str::uuid() . '.' . strtolower($file->getClientOriginalExtension());
    }

    private function crearNuevaVersion(Archivo $archivo, $file, $usuario, Carpeta $carpeta): RedirectResponse
    {
        $nuevaVersion         = $archivo->version + 1;
        $nombreAlmacenamiento = $this->generarNombreAlmacenamiento($file);
        $rutaDisco            = "empresa_{$carpeta->empresa_id}/carpeta_{$carpeta->id}/{$nombreAlmacenamiento}";
        $hash                 = hash_file('sha256', $file->getRealPath());

        Storage::disk('filecenter')->put($rutaDisco, file_get_contents($file->getRealPath()));

        // Desactivar versión anterior
        VersionArchivo::where('archivo_id', $archivo->id)->update(['activo' => false]);

        // Crear nueva versión
        VersionArchivo::create([
            'archivo_id'            => $archivo->id,
            'version'               => $nuevaVersion,
            'nombre_original'       => $file->getClientOriginalName(),
            'nombre_almacenamiento' => $nombreAlmacenamiento,
            'ruta_disco'            => $rutaDisco,
            'hash_sha256'           => $hash,
            'tamanio_bytes'         => $file->getSize(),
            'subido_por'            => $usuario->id,
            'nota_version'          => "Resubida v{$nuevaVersion}",
            'activo'                => true,
        ]);

        // Actualizar el archivo principal
        $archivo->update([
            'nombre_almacenamiento' => $nombreAlmacenamiento,
            'ruta_disco'            => $rutaDisco,
            'hash_sha256'           => $hash,
            'tamanio_bytes'         => $file->getSize(),
            'version'               => $nuevaVersion,
        ]);

        RegistroActividad::registrar(
            'subir', 'archivo', $archivo->id,
            "Nueva versión v{$nuevaVersion}: {$archivo->nombre_original}"
        );

        return redirect()
            ->route('carpetas.show', $carpeta)
            ->with('success', "Nueva versión (v{$nuevaVersion}) de \"{$archivo->nombre_original}\" subida.");
    }
}
