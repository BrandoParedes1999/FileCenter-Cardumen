<?php

namespace App\Http\Controllers;

use App\Models\Archivo;
use App\Models\Carpeta;
use App\Models\RegistroActividad;
use App\Models\SolicitudSubida;
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
    // ── Tipos MIME permitidos ────────────────────────────────
    const MIMES_PERMITIDOS = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    ];

    const EXTENSIONES_PERMITIDAS = ['pdf', 'doc', 'docx', 'xls', 'xlsx'];

    // ────────────────────────────────────────────────────────

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
            $query->whereHas('carpeta', fn($q) => $q->where('empresa_id', $usuario->empresa_id));
        }

        $archivos = $query->orderBy('nombre_original')->paginate(30);

        return view('archivos.index', compact('archivos', 'carpeta'));
    }

    public function show(Archivo $archivo): View
    {
        $this->authorize('view', $archivo);

        $archivo->load(['carpeta.padre', 'subidoPor', 'versiones.subidoPor']);

        // Construir migas desde la carpeta contenedora
        $migas  = [];
        $actual = $archivo->carpeta;
        while ($actual) {
            array_unshift($migas, ['nombre' => $actual->nombre, 'id' => $actual->id]);
            $actual = $actual->padre;
        }

        RegistroActividad::registrar('ver', 'archivo', $archivo->id, "Vista detalle: {$archivo->nombre_original}");

        return view('archivos.show', compact('archivo', 'migas'));
    }

    public function create(Request $request): View
    {
        $carpeta = Carpeta::findOrFail($request->query('carpeta_id'));
        $this->authorize('uploadTo', $carpeta);

        return view('archivos.create', compact('carpeta'));
    }

    public function store(Request $request): RedirectResponse
    {
        // Sin límite de tamaño en la validación de Laravel.
        // El límite real lo impone php.ini / nginx en el servidor.
        $request->validate([
            'carpeta_id'  => ['required', 'exists:carpetas,id'],
            'archivo'     => [
                'required',
                'file',
                // Solo PDF, Word y Excel
                'mimes:pdf,doc,docx,xls,xlsx',
            ],
            'descripcion' => ['nullable', 'string', 'max:500'],
        ], [
            'archivo.required' => 'Debes seleccionar un archivo.',
            'archivo.mimes'    => 'Solo se permiten archivos PDF, Word (.doc/.docx) y Excel (.xls/.xlsx).',
        ]);

        $usuario = Auth::user();
        $carpeta = Carpeta::findOrFail($request->carpeta_id);
        $file    = $request->file('archivo');

        $this->authorize('uploadTo', $carpeta);

        // Validación extra de extensión (doble capa de seguridad)
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, self::EXTENSIONES_PERMITIDAS)) {
            return back()->withErrors(['archivo' => 'Tipo de archivo no permitido.']);
        }

        // ── ¿La carpeta requiere aprobación para subir? ──────────
        // Si el usuario NO es Admin/Gerente/Superadmin y la carpeta
        // tiene requiere_aprobacion_subida = true, crear solicitud.
        if ($this->requiereAprobacion($carpeta, $usuario)) {
            return $this->crearSolicitudSubida($request, $carpeta, $file, $usuario);
        }

        // ── Proceso normal de subida ─────────────────────────────
        return $this->procesarSubida($request, $carpeta, $file, $usuario);
    }

    public function edit(Archivo $archivo): View
    {
        $this->authorize('update', $archivo);
        return view('archivos.edit', compact('archivo'));
    }

    public function update(Request $request, Archivo $archivo): RedirectResponse
    {
        $this->authorize('update', $archivo);

        $validated = $request->validate([
            'descripcion' => ['nullable', 'string', 'max:500'],
        ]);

        $archivo->update($validated);

        RegistroActividad::registrar('editar', 'archivo', $archivo->id, "Editó metadata: {$archivo->nombre_original}");

        return redirect()
            ->route('archivos.show', $archivo)
            ->with('success', 'Archivo actualizado.');
    }

    public function destroy(Archivo $archivo): RedirectResponse
    {
        $this->authorize('delete', $archivo);

        $carpetaId = $archivo->carpeta_id;
        $nombre    = $archivo->nombre_original;

        $archivo->update(['esta_eliminado' => true]);
        $archivo->delete();

        RegistroActividad::registrar('eliminar', 'archivo', $archivo->id, "Eliminó: \"{$nombre}\"");

        return redirect()
            ->route('carpetas.show', $carpetaId)
            ->with('success', "Archivo \"{$nombre}\" eliminado.");
    }

    public function descargar(Archivo $archivo): StreamedResponse
    {
        $this->authorize('download', $archivo);

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

    public function restaurarVersion(Request $request, Archivo $archivo): RedirectResponse
    {
        $this->authorize('update', $archivo);

        $request->validate([
            'version_id' => ['required', 'exists:versiones_archivos,id'],
        ]);

        $version = VersionArchivo::where('id', $request->version_id)
            ->where('archivo_id', $archivo->id)
            ->firstOrFail();

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

    // ── Helpers privados ────────────────────────────────────────

    /**
     * Determina si la carpeta requiere aprobación para subir archivos.
     * Aplica a roles menores cuando la carpeta tiene restricción de subida pendiente.
     */
    private function requiereAprobacion(Carpeta $carpeta, $usuario): bool
    {
        // Superadmin, Aux_QHSE, Admin y Gerente nunca necesitan aprobación
        if (in_array($usuario->rol, ['Superadmin', 'Aux_QHSE', 'Admin', 'Gerente'])) {
            return false;
        }

        // Si la carpeta requiere aprobación de subida
        return (bool) $carpeta->requiere_aprobacion_subida;
    }

    /**
     * Crea una solicitud de subida pendiente de aprobación.
     * El archivo se guarda temporalmente en disco hasta que un admin lo apruebe.
     */
    private function crearSolicitudSubida(Request $request, Carpeta $carpeta, $file, $usuario): RedirectResponse
    {
        $extension            = strtolower($file->getClientOriginalExtension());
        $nombreAlmacenamiento = Str::uuid() . '.' . $extension;
        $rutaTemporal         = "empresa_{$carpeta->empresa_id}/temp/{$nombreAlmacenamiento}";

        // Guardar temporalmente
        Storage::disk('filecenter')->put($rutaTemporal, file_get_contents($file->getRealPath()));

        // Crear solicitud de subida
        \App\Models\SolicitudSubida::create([
            'carpeta_id'            => $carpeta->id,
            'solicitante_id'        => $usuario->id,
            'nombre_original'       => $file->getClientOriginalName(),
            'nombre_almacenamiento' => $nombreAlmacenamiento,
            'ruta_temporal'         => $rutaTemporal,
            'hash_sha256'           => hash_file('sha256', $file->getRealPath()),
            'tipo_mime'             => $file->getMimeType(),
            'extension'             => $extension,
            'tamanio_bytes'         => $file->getSize(),
            'descripcion'           => $request->descripcion,
            'status'                => 'Pendiente',
        ]);

        RegistroActividad::registrar(
            'solicitar_subida', 'carpeta', $carpeta->id,
            "Solicitó subir: {$file->getClientOriginalName()}"
        );

        return redirect()
            ->route('carpetas.show', $carpeta)
            ->with('success', "Tu archivo \"{$file->getClientOriginalName()}\" está pendiente de aprobación por un administrador.");
    }

    /**
     * Procesa la subida directa de un archivo (sin aprobación).
     */
    private function procesarSubida(Request $request, Carpeta $carpeta, $file, $usuario): RedirectResponse
    {
        // ¿Ya existe un archivo con el mismo nombre? → nueva versión
        $archivoExistente = Archivo::where('carpeta_id', $carpeta->id)
            ->where('nombre_original', $file->getClientOriginalName())
            ->where('esta_eliminado', false)
            ->first();

        if ($archivoExistente) {
            return $this->crearNuevaVersion($archivoExistente, $file, $usuario, $carpeta);
        }

        // Archivo nuevo
        $extension            = strtolower($file->getClientOriginalExtension());
        $nombreAlmacenamiento = Str::uuid() . '.' . $extension;
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
            'extension'             => $extension,
            'tamanio_bytes'         => $file->getSize(),
            'descripcion'           => $request->descripcion,
            'version'               => 1,
        ]);

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
            'activo'                => true,
        ]);

        RegistroActividad::registrar('subir', 'archivo', $archivo->id, "Subió: {$archivo->nombre_original}");

        return redirect()
            ->route('carpetas.show', $carpeta)
            ->with('success', "Archivo \"{$archivo->nombre_original}\" subido correctamente.");
    }

    private function crearNuevaVersion(Archivo $archivo, $file, $usuario, Carpeta $carpeta): RedirectResponse
    {
        $nuevaVersion         = $archivo->version + 1;
        $extension            = strtolower($file->getClientOriginalExtension());
        $nombreAlmacenamiento = Str::uuid() . '.' . $extension;
        $rutaDisco            = "empresa_{$carpeta->empresa_id}/carpeta_{$carpeta->id}/{$nombreAlmacenamiento}";
        $hash                 = hash_file('sha256', $file->getRealPath());

        Storage::disk('filecenter')->put($rutaDisco, file_get_contents($file->getRealPath()));

        VersionArchivo::where('archivo_id', $archivo->id)->update(['activo' => false]);

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