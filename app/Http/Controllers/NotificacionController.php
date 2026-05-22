<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificacionController extends Controller
{
    public function index(): View
    {
        $usuario        = Auth::user();
        $notificaciones = $usuario->notifications()->paginate(20);

        return view('notificaciones.index', compact('notificaciones'));
    }

    public function marcarLeida(Request $request, string $id): RedirectResponse|JsonResponse
    {
        $notif = Auth::user()->notifications()->findOrFail($id);
        $notif->markAsRead();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'ok'              => true,
                'notif_no_leidas' => Auth::user()->unreadNotifications()->count(),
            ]);
        }

        $url = $notif->data['url'] ?? null;

        if ($url && str_starts_with($url, url('/'))) {
            return redirect($url);
        }

        return redirect()->route('notificaciones.index');
    }

    public function marcarTodasLeidas(Request $request): RedirectResponse|JsonResponse
    {
        Auth::user()->unreadNotifications->markAsRead();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'ok'              => true,
                'notif_no_leidas' => 0,
            ]);
        }

        return back()->with('success', 'Todas las notificaciones marcadas como leídas.');
    }

    public function destroy(string $id): RedirectResponse
    {
        Auth::user()->notifications()->findOrFail($id)->delete();

        return back()->with('success', 'Notificación eliminada.');
    }

    public function vaciar(): RedirectResponse
    {
        Auth::user()->notifications()->delete();

        return back()->with('success', 'Todas las notificaciones eliminadas.');
    }
}
