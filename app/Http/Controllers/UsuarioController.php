<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\RegistroActividad;
use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UsuarioController extends Controller
{
    // Solo Admin+ puede gestionar usuarios
    private function autorizar(): void
    {
        $rol = Auth::user()->rol;
        if (!in_array($rol, ['Superadmin', 'Aux_QHSE', 'Admin'])) {
            abort(403, 'No tienes permiso para gestionar usuarios.');
        }
    }

    // ── INDEX ────────────────────────────────────────
    public function index(Request $request): View
    {
        $this->autorizar();
        $usuario = Auth::user();

        $query = Usuario::with('empresa')->orderBy('paterno')->orderBy('nombre');

        // Superadmin y Aux_QHSE ven todos
        // Admin solo ve los de su empresa
        if ($usuario->rol === 'Admin') {
            $query->where('empresa_id', $usuario->empresa_id);
        }

        // Filtros opcionales
        if ($request->filled('empresa_id')) {
            $query->where('empresa_id', $request->empresa_id);
        }
        if ($request->filled('rol')) {
            $query->where('rol', $request->rol);
        }
        if ($request->filled('estado')) {
            $query->where('es_activo', $request->estado === 'activo');
        }
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($w) use ($q) {
                $w->where('nombre', 'like', "%{$q}%")
                ->orWhere('paterno', 'like', "%{$q}%")
                ->orWhere('email', 'like', "%{$q}%")
                ->orWhere('departamento', 'like', "%{$q}%");
            });
        }

        $usuarios = $query->paginate(20)->withQueryString();
        $empresas = Empresa::where('activo', true)->orderByDesc('es_corporativo')->orderBy('nombre')->get();

        return view('usuarios.index', compact('usuarios', 'empresas'));
    }

    // ── SHOW ─────────────────────────────────────────
    public function show(Usuario $usuario): View
    {
        $this->autorizar();
        $this->verificarAcceso($usuario);

        $usuario->load('empresa');
        $actividad = $usuario->actividad()->latest('created_at')->take(10)->get();

        return view('usuarios.show', compact('usuario', 'actividad'));
    }

    // ── CREATE ───────────────────────────────────────
    public function create(): View
    {
        $this->autorizar();
        $authUser = Auth::user();

        $empresas = in_array($authUser->rol, ['Superadmin', 'Aux_QHSE'])
            ? Empresa::where('activo', true)->orderByDesc('es_corporativo')->orderBy('nombre')->get()
            : Empresa::where('id', $authUser->empresa_id)->get();

        // Roles que puede asignar según su propio rol
        $roles = $this->rolesDisponibles($authUser->rol);

        return view('usuarios.create', compact('empresas', 'roles'));
    }

    // ── STORE ────────────────────────────────────────
    public function store(Request $request): RedirectResponse
    {
        $this->autorizar();
        $authUser = Auth::user();

        $validated = $request->validate([
            'empresa_id'  => ['required', 'exists:empresas,id'],
            'nombre'      => ['required', 'string', 'max:245'],
            'paterno'     => ['required', 'string', 'max:245'],
            'materno'     => ['nullable', 'string', 'max:245'],
            'email'       => ['required', 'email', 'max:245', 'unique:usuarios,email'],
            'password'    => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
            'rol'         => ['required', Rule::in($this->rolesDisponibles($authUser->rol))],
            'departamento'=> ['nullable', 'string', 'max:245'],
            'es_activo'   => ['boolean'],
        ], [
            'email.unique'        => 'Este correo ya está registrado.',
            'password.confirmed'  => 'Las contraseñas no coinciden.',
            'password.min'        => 'La contraseña debe tener al menos 8 caracteres.',
        ]);

        // Admin no puede crear usuarios en otra empresa
        if ($authUser->rol === 'Admin' && (int)$validated['empresa_id'] !== (int)$authUser->empresa_id) {
            abort(403);
        }

        $usuario = Usuario::create([
            'empresa_id'   => $validated['empresa_id'],
            'nombre'       => $validated['nombre'],
            'paterno'      => $validated['paterno'],
            'materno'      => $validated['materno'] ?? '',
            'email'        => strtolower($validated['email']),
            'password'     => Hash::make($validated['password']),
            'rol'          => $validated['rol'],
            'departamento' => $validated['departamento'] ?? null,
            'es_activo'    => $validated['es_activo'] ?? true,
        ]);

        // Sincronizar rol con Spatie
        $usuario->assignRoleSynced($validated['rol']);

        RegistroActividad::registrar('editar', 'usuario', $usuario->id,
            "Creó usuario: {$usuario->nombre_completo} ({$usuario->rol})");

        return redirect()->route('usuarios.show', $usuario)
            ->with('success', "Usuario \"{$usuario->nombre_completo}\" creado correctamente.");
    }

    // ── EDIT ─────────────────────────────────────────
    public function edit(Usuario $usuario): View
    {
        $this->autorizar();
        $this->verificarAcceso($usuario);

        $authUser = Auth::user();

        $empresas = in_array($authUser->rol, ['Superadmin', 'Aux_QHSE'])
            ? Empresa::where('activo', true)->orderByDesc('es_corporativo')->orderBy('nombre')->get()
            : Empresa::where('id', $authUser->empresa_id)->get();

        $roles = $this->rolesDisponibles($authUser->rol);

        return view('usuarios.edit', compact('usuario', 'empresas', 'roles'));
    }

    // ── UPDATE ───────────────────────────────────────
    public function update(Request $request, Usuario $usuario): RedirectResponse
    {
        $this->autorizar();
        $this->verificarAcceso($usuario);

        $authUser = Auth::user();

        $validated = $request->validate([
            'empresa_id'  => ['required', 'exists:empresas,id'],
            'nombre'      => ['required', 'string', 'max:245'],
            'paterno'     => ['required', 'string', 'max:245'],
            'materno'     => ['nullable', 'string', 'max:245'],
            'email'       => ['required', 'email', 'max:245', Rule::unique('usuarios', 'email')->ignore($usuario->id)],
            'rol'         => ['required', Rule::in($this->rolesDisponibles($authUser->rol))],
            'departamento'=> ['nullable', 'string', 'max:245'],
            'es_activo'   => ['boolean'],
            // Contraseña opcional al editar
            'password'    => ['nullable', 'confirmed', Password::min(8)->letters()->numbers()],
        ], [
            'email.unique' => 'Este correo ya está registrado por otro usuario.',
        ]);

        $data = [
            'empresa_id'   => $validated['empresa_id'],
            'nombre'       => $validated['nombre'],
            'paterno'      => $validated['paterno'],
            'materno'      => $validated['materno'] ?? '',
            'email'        => strtolower($validated['email']),
            'rol'          => $validated['rol'],
            'departamento' => $validated['departamento'] ?? null,
            'es_activo'    => $validated['es_activo'] ?? true,
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $rolAnterior = $usuario->rol;
        $usuario->update($data);

        // Sincronizar rol con Spatie si cambió
        if ($rolAnterior !== $validated['rol']) {
            $usuario->assignRoleSynced($validated['rol']);
        }

        RegistroActividad::registrar('editar', 'usuario', $usuario->id,
            "Editó usuario: {$usuario->nombre_completo}");

        return redirect()->route('usuarios.show', $usuario)
            ->with('success', 'Usuario actualizado correctamente.');
    }

    // ── DESTROY (soft delete) ────────────────────────
    public function destroy(Usuario $usuario): RedirectResponse
    {
        $this->autorizar();
        $this->verificarAcceso($usuario);

        // No puede eliminarse a sí mismo
        if ($usuario->id === Auth::id()) {
            return back()->withErrors(['error' => 'No puedes eliminar tu propia cuenta.']);
        }
        // No puede eliminar a otro Superadmin
        if ($usuario->rol === 'Superadmin') {
            return back()->withErrors(['error' => 'No puedes eliminar una cuenta Superadmin.']);
        }

        $nombre = $usuario->nombre_completo;
        $usuario->update(['es_activo' => false]);
        $usuario->delete(); // SoftDelete

        RegistroActividad::registrar('eliminar', 'usuario', $usuario->id,
            "Desactivó y eliminó usuario: \"{$nombre}\"");

        return redirect()->route('usuarios.index')
            ->with('success', "Usuario \"{$nombre}\" eliminado.");
    }

    // ── TOGGLE ACTIVO ────────────────────────────────
    public function toggleActivo(Usuario $usuario): RedirectResponse
    {
        $this->autorizar();
        $this->verificarAcceso($usuario);

        if ($usuario->id === Auth::id()) {
            return back()->withErrors(['error' => 'No puedes desactivar tu propia cuenta.']);
        }

        $nuevo = !$usuario->es_activo;
        $usuario->update(['es_activo' => $nuevo, 'bloqueado_hasta' => null, 'intentos_login' => 0]);

        $accion = $nuevo ? 'activado' : 'desactivado';

        RegistroActividad::registrar('editar', 'usuario', $usuario->id,
            "Usuario {$accion}: {$usuario->nombre_completo}");

        return back()->with('success', "Usuario {$accion} correctamente.");
    }

    // ── DESBLOQUEAR ──────────────────────────────────
    public function desbloquear(Usuario $usuario): RedirectResponse
    {
        $this->autorizar();
        $this->verificarAcceso($usuario);

        $usuario->update(['bloqueado_hasta' => null, 'intentos_login' => 0]);

        RegistroActividad::registrar('editar', 'usuario', $usuario->id,
            "Desbloqueó cuenta: {$usuario->nombre_completo}");

        return back()->with('success', 'Cuenta desbloqueada correctamente.');
    }

    // ── HELPERS PRIVADOS ─────────────────────────────

    private function verificarAcceso(Usuario $usuario): void
    {
        $authUser = Auth::user();
        // Admin solo puede ver/editar usuarios de su empresa
        if ($authUser->rol === 'Admin' && $usuario->empresa_id !== $authUser->empresa_id) {
            abort(403);
        }
    }

    private function rolesDisponibles(string $rolPropio): array
    {
        return match ($rolPropio) {
            'Superadmin' => ['Superadmin', 'Aux_QHSE', 'Admin', 'Gerente', 'Auxiliar', 'Empleado'],
            'Aux_QHSE'   => ['Admin', 'Gerente', 'Auxiliar', 'Empleado'],
            'Admin'      => ['Gerente', 'Auxiliar', 'Empleado'],
            default      => [],
        };
    }
}