<?php

namespace App\Http\Controllers;

use App\Models\Archivo;
use App\Models\Carpeta;
use App\Models\RegistroActividad;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PapeleraController extends Controller
{
    public function index(): View
    {
        $usuario  = Auth::user();
        $esAdmin  = in_array($usuario->rol, ['Superadmin', 'Aux_QHSE']);

        $archivos = Archivo::onlyTrashed()
            ->with(['carpeta.empresa', 'subidoPor'])
            ->when(!$esAdmin, fn($q) => $q->whereHas('carpeta', fn($c) => $c->where('empresa_id', $usuario->empresa_id)))
            ->latest('deleted_at')
            ->get();

        $carpetas = Carpeta::onlyTrashed()
            ->with(['empresa'])
            ->when(!$esAdmin, fn($q) => $q->where('empresa_id', $usuario->empresa_id))
            ->latest('deleted_at')
            ->get();

        return view('papelera.index', compact('archivos', 'carpetas'));
    }

    public function restaurarArchivo(int $id): RedirectResponse
    {
        $archivo = Archivo::onlyTrashed()->findOrFail($id);
        $this->autorizarArchivo($archivo);

        $archivo->restore();
        $archivo->update(['esta_eliminado' => false]);

        RegistroActividad::registrar('restaurar', 'archivo', $archivo->id, "Archivo restaurado desde papelera: {$archivo->nombre_original}");

        return back()->with('success', "Archivo \"{$archivo->nombre_original}\" restaurado.");
    }

    public function restaurarCarpeta(int $id): RedirectResponse
    {
        $carpeta = Carpeta::onlyTrashed()->findOrFail($id);
        $this->autorizarCarpeta($carpeta);

        $carpeta->restore();

        RegistroActividad::registrar('restaurar', 'carpeta', $carpeta->id, "Carpeta restaurada desde papelera: {$carpeta->nombre}");

        return back()->with('success', "Carpeta \"{$carpeta->nombre}\" restaurada.");
    }

    public function eliminarArchivo(int $id): RedirectResponse
    {
        $archivo = Archivo::onlyTrashed()->findOrFail($id);
        $this->autorizarArchivo($archivo);

        // Borrar físico del disco
        if ($archivo->ruta_disco && Storage::disk('filecenter')->exists($archivo->ruta_disco)) {
            Storage::disk('filecenter')->delete($archivo->ruta_disco);
        }

        $archivo->forceDelete();

        RegistroActividad::registrar('eliminar_permanente', 'archivo', $id, "Archivo eliminado permanentemente: {$archivo->nombre_original}");

        return back()->with('success', "Archivo eliminado permanentemente.");
    }

    public function eliminarCarpeta(int $id): RedirectResponse
    {
        $carpeta = Carpeta::onlyTrashed()->findOrFail($id);
        $this->autorizarCarpeta($carpeta);

        $carpeta->forceDelete();

        RegistroActividad::registrar('eliminar_permanente', 'carpeta', $id, "Carpeta eliminada permanentemente: {$carpeta->nombre}");

        return back()->with('success', "Carpeta eliminada permanentemente.");
    }

    public function vaciar(): RedirectResponse
    {
        $usuario = Auth::user();
        $esAdmin = in_array($usuario->rol, ['Superadmin', 'Aux_QHSE']);

        $archivos = Archivo::onlyTrashed()
            ->when(!$esAdmin, fn($q) => $q->whereHas('carpeta', fn($c) => $c->where('empresa_id', $usuario->empresa_id)))
            ->get();

        foreach ($archivos as $archivo) {
            if ($archivo->ruta_disco && Storage::disk('filecenter')->exists($archivo->ruta_disco)) {
                Storage::disk('filecenter')->delete($archivo->ruta_disco);
            }
            $archivo->forceDelete();
        }

        Carpeta::onlyTrashed()
            ->when(!$esAdmin, fn($q) => $q->where('empresa_id', $usuario->empresa_id))
            ->forceDelete();

        RegistroActividad::registrar('vaciar_papelera', 'sistema', null, "Papelera vaciada. {$archivos->count()} archivos eliminados permanentemente.");

        return back()->with('success', 'Papelera vaciada correctamente.');
    }

    private function autorizarArchivo(Archivo $archivo): void
    {
        $usuario = Auth::user();
        if (in_array($usuario->rol, ['Superadmin', 'Aux_QHSE'])) return;

        if (
            in_array($usuario->rol, ['Admin', 'Gerente']) &&
            $archivo->carpeta?->empresa_id === $usuario->empresa_id
        ) return;

        abort(403);
    }

    private function autorizarCarpeta(Carpeta $carpeta): void
    {
        $usuario = Auth::user();
        if (in_array($usuario->rol, ['Superadmin', 'Aux_QHSE'])) return;

        if (
            in_array($usuario->rol, ['Admin', 'Gerente']) &&
            $carpeta->empresa_id === $usuario->empresa_id
        ) return;

        abort(403);
    }
}
