<?php

namespace App\Http\Controllers;

use App\Models\Archivo;
use App\Models\RegistroActividad;
use App\Models\SolicitudSubida;
use App\Models\VersionArchivo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SolicitudSubidaController extends Controller
{
    /**
     * Lista solicitudes de subida pendientes.
     * Admin/Gerente ven las de su empresa. Superadmin ve todas.
     */
    public function index(Request $request): View
    {
        $usuario      = Auth::user();
        $filtroStatus = $request->query('status', 'Pendiente');

        $query = SolicitudSubida::with(['carpeta.empresa', 'solicitante', 'revisor', 'archivo'])
            ->orderByRaw("CASE status
                WHEN 'Pendiente' THEN 1
                WHEN 'Aprobado'  THEN 2
                WHEN 'Rechazado' THEN 3
                ELSE 4 END")
            ->orderBy('created_at', 'desc');

        // Filtro por empresa según rol
        if (!in_array($usuario->rol, ['Superadmin', 'Aux_QHSE'])) {
            $query->whereHas('carpeta', fn($q) => $q->where('empresa_id', $usuario->empresa_id));
        }

        // Filtro de status
        if ($filtroStatus && in_array($filtroStatus, ['Pendiente', 'Aprobado', 'Rechazado'])) {
            $query->where('status', $filtroStatus);
        }

        $solicitudes = $query->paginate(25)->withQueryString();

        return view('solicitudes_subida.index', compact('solicitudes', 'filtroStatus'));
    }

    /**
     * Detalle de una solicitud.
     */
    public function show(SolicitudSubida $solicitudSubida): View
    {
        $this->autorizarVer($solicitudSubida);
        $solicitudSubida->load(['carpeta.empresa', 'solicitante', 'revisor', 'archivo']);

        return view('solicitudes_subida.show', compact('solicitudSubida'));
    }

    /**
     * Aprobar: mueve el archivo de temp → carpeta definitiva y crea el registro.
     */
    public function aprobar(Request $request, SolicitudSubida $solicitudSubida): RedirectResponse
    {
        $this->autorizarRevisar($solicitudSubida);

        if (!$solicitudSubida->estaPendiente()) {
            return back()->withErrors(['error' => 'Esta solicitud ya fue procesada.']);
        }

        $request->validate([
            'comentario_revisor' => ['nullable', 'string', 'max:500'],
        ]);

        $carpeta  = $solicitudSubida->carpeta;
        $usuario  = $solicitudSubida->solicitante;
        $revisor  = Auth::user();

        // Ruta definitiva
        $rutaDefinitiva = "empresa_{$carpeta->empresa_id}/carpeta_{$carpeta->id}/{$solicitudSubida->nombre_almacenamiento}";

        // Mover de temp a definitivo
        if (Storage::disk('filecenter')->exists($solicitudSubida->ruta_temporal)) {
            $contenido = Storage::disk('filecenter')->get($solicitudSubida->ruta_temporal);
            Storage::disk('filecenter')->put($rutaDefinitiva, $contenido);
            Storage::disk('filecenter')->delete($solicitudSubida->ruta_temporal);
        } else {
            return back()->withErrors(['error' => 'El archivo temporal ya no existe. No se puede aprobar.']);
        }

        // ¿Ya existe archivo con ese nombre? → nueva versión
        $archivoExistente = Archivo::where('carpeta_id', $carpeta->id)
            ->where('nombre_original', $solicitudSubida->nombre_original)
            ->where('esta_eliminado', false)
            ->first();

        if ($archivoExistente) {
            $nuevaVersion = $archivoExistente->version + 1;

            VersionArchivo::where('archivo_id', $archivoExistente->id)->update(['activo' => false]);

            VersionArchivo::create([
                'archivo_id'            => $archivoExistente->id,
                'version'               => $nuevaVersion,
                'nombre_original'       => $solicitudSubida->nombre_original,
                'nombre_almacenamiento' => $solicitudSubida->nombre_almacenamiento,
                'ruta_disco'            => $rutaDefinitiva,
                'hash_sha256'           => $solicitudSubida->hash_sha256,
                'tamanio_bytes'         => $solicitudSubida->tamanio_bytes,
                'subido_por'            => $usuario->id,
                'nota_version'          => "Aprobado por {$revisor->nombre_completo}",
                'activo'                => true,
            ]);

            $archivoExistente->update([
                'nombre_almacenamiento' => $solicitudSubida->nombre_almacenamiento,
                'ruta_disco'            => $rutaDefinitiva,
                'hash_sha256'           => $solicitudSubida->hash_sha256,
                'tamanio_bytes'         => $solicitudSubida->tamanio_bytes,
                'version'               => $nuevaVersion,
            ]);

            $archivoId = $archivoExistente->id;
        } else {
            // Archivo nuevo
            $archivo = Archivo::create([
                'carpeta_id'            => $carpeta->id,
                'subido_por'            => $usuario->id,
                'nombre_original'       => $solicitudSubida->nombre_original,
                'nombre_almacenamiento' => $solicitudSubida->nombre_almacenamiento,
                'ruta_disco'            => $rutaDefinitiva,
                'hash_sha256'           => $solicitudSubida->hash_sha256,
                'tipo_mime'             => $solicitudSubida->tipo_mime,
                'extension'             => $solicitudSubida->extension,
                'tamanio_bytes'         => $solicitudSubida->tamanio_bytes,
                'descripcion'           => $solicitudSubida->descripcion,
                'version'               => 1,
            ]);

            VersionArchivo::create([
                'archivo_id'            => $archivo->id,
                'version'               => 1,
                'nombre_original'       => $archivo->nombre_original,
                'nombre_almacenamiento' => $solicitudSubida->nombre_almacenamiento,
                'ruta_disco'            => $rutaDefinitiva,
                'hash_sha256'           => $solicitudSubida->hash_sha256,
                'tamanio_bytes'         => $solicitudSubida->tamanio_bytes,
                'subido_por'            => $usuario->id,
                'nota_version'          => "Versión inicial — aprobada por {$revisor->nombre_completo}",
                'activo'                => true,
            ]);

            $archivoId = $archivo->id;
        }

        // Actualizar solicitud
        $solicitudSubida->update([
            'status'             => 'Aprobado',
            'revisado_por'       => $revisor->id,
            'revisado_en'        => now(),
            'comentario_revisor' => $request->comentario_revisor,
            'archivo_id'         => $archivoId,
        ]);

        RegistroActividad::registrar(
            'aprobar_solicitud', 'archivo', $archivoId,
            "Aprobó subida de \"{$solicitudSubida->nombre_original}\" de {$usuario->nombre_completo}"
        );

        return redirect()
            ->route('solicitudes-subida.index')
            ->with('success', "Archivo \"{$solicitudSubida->nombre_original}\" aprobado y publicado en la carpeta.");
    }

    /**
     * Rechazar: elimina el archivo temporal y cierra la solicitud.
     */
    public function rechazar(Request $request, SolicitudSubida $solicitudSubida): RedirectResponse
    {
        $this->autorizarRevisar($solicitudSubida);

        if (!$solicitudSubida->estaPendiente()) {
            return back()->withErrors(['error' => 'Esta solicitud ya fue procesada.']);
        }

        $request->validate([
            'comentario_revisor' => ['required', 'string', 'max:500'],
        ], [
            'comentario_revisor.required' => 'Debes indicar el motivo del rechazo.',
        ]);

        // Eliminar archivo temporal
        if (Storage::disk('filecenter')->exists($solicitudSubida->ruta_temporal)) {
            Storage::disk('filecenter')->delete($solicitudSubida->ruta_temporal);
        }

        $solicitudSubida->update([
            'status'             => 'Rechazado',
            'revisado_por'       => Auth::id(),
            'revisado_en'        => now(),
            'comentario_revisor' => $request->comentario_revisor,
        ]);

        RegistroActividad::registrar(
            'rechazar_solicitud', 'carpeta', $solicitudSubida->carpeta_id,
            "Rechazó subida de \"{$solicitudSubida->nombre_original}\""
        );

        return redirect()
            ->route('solicitudes-subida.index')
            ->with('success', 'Solicitud rechazada. El archivo temporal fue eliminado.');
    }

    // ── Helpers ─────────────────────────────────────────────────

    private function autorizarVer(SolicitudSubida $sol): void
    {
        $usuario = Auth::user();

        $ok = in_array($usuario->rol, ['Superadmin', 'Aux_QHSE'])
            || $sol->solicitante_id === $usuario->id
            || (in_array($usuario->rol, ['Admin', 'Gerente'])
                && $sol->carpeta->empresa_id === $usuario->empresa_id);

        if (!$ok) abort(403);
    }

    private function autorizarRevisar(SolicitudSubida $sol): void
    {
        $usuario = Auth::user();

        $ok = in_array($usuario->rol, ['Superadmin', 'Aux_QHSE'])
            || (in_array($usuario->rol, ['Admin', 'Gerente'])
                && $sol->carpeta->empresa_id === $usuario->empresa_id);

        if (!$ok) abort(403, 'No tienes permiso para revisar esta solicitud.');
    }
}