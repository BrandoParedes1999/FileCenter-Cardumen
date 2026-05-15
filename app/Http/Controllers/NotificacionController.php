<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificacionController extends Controller
{
    public function index(): View
    {
        $usuario       = Auth::user();
        $notificaciones = $usuario->notifications()->paginate(20);

        return view('notificaciones.index', compact('notificaciones'));
    }

    public function marcarLeida(string $id): RedirectResponse
    {
        $notif = Auth::user()->notifications()->findOrFail($id);
        $notif->markAsRead();

        if ($notif->data['url'] ?? false) {
            return redirect($notif->data['url']);
        }

        return back();
    }

    public function marcarTodasLeidas(): RedirectResponse
    {
        Auth::user()->unreadNotifications->markAsRead();

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
