<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class RegisteredUserController extends Controller
{
    /**
     * El registro público está deshabilitado.
     * Los usuarios son creados por administradores en /usuarios.
     */
    public function create(): RedirectResponse
    {
        return redirect()->route('login')
            ->with('status', 'El registro público no está habilitado. Contacta al administrador para obtener acceso.');
    }

    public function store(): RedirectResponse
    {
        return redirect()->route('login')
            ->with('status', 'El registro público no está habilitado. Contacta al administrador para obtener acceso.');
    }
}
