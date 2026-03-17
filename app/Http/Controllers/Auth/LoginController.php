<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\RegistroActividad;
use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

class LoginController extends Controller
{
    // ─────────────────────────────────────────────
    // MOSTRAR FORMULARIO
    // ─────────────────────────────────────────────

    public function create(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    // ─────────────────────────────────────────────
    // PROCESAR LOGIN
    // ─────────────────────────────────────────────

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required'    => 'El correo electrónico es obligatorio.',
            'email.email'       => 'Ingresa un correo válido.',
            'password.required' => 'La contraseña es obligatoria.',
        ]);

        // 1. Rate limiting por IP (capa previa al usuario)
        if ($this->demasiadosIntentosPorIP($request)) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Demasiados intentos. Espera un momento e intenta de nuevo.']);
        }

        // 2. Buscar usuario por email
        $usuario = Usuario::where('email', $request->email)->first();

        // 3. Usuario no existe — respuesta genérica (no revelar si existe)
        if (!$usuario) {
            $this->registrarIpFallida($request);
            return $this->respuestaCredencialesInvalidas($request);
        }

        // 4. Usuario inactivo
        if (!$usuario->es_activo) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Tu cuenta está desactivada. Contacta al administrador.']);
        }

        // 5. Usuario bloqueado por brute-force
        if ($usuario->estaBloqueado()) {
            $minutos = (int) now()->diffInMinutes($usuario->bloqueado_hasta, false);

            RegistroActividad::registrar(
                'usuario_bloqueado', 'usuario', $usuario->id,
                "Intento de acceso a cuenta bloqueada desde {$request->ip()}",
                $usuario->id, $request->ip()
            );

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => "Cuenta bloqueada. Podrás intentarlo en {$minutos} minuto(s)."]);
        }

        // 6. Verificar contraseña
        if (!Auth::attempt(['email' => $request->email, 'password' => $request->password], $request->boolean('remember'))) {

            // Incrementar intentos del usuario
            $usuario->incrementarIntentos();

            // Registrar fallo
            RegistroActividad::registrar(
                'login_fallido', 'usuario', $usuario->id,
                "Contraseña incorrecta. Intentos: {$usuario->fresh()->intentos_login}",
                $usuario->id, $request->ip()
            );

            // Si ahora está bloqueado, dar mensaje específico
            if ($usuario->fresh()->estaBloqueado()) {
                return back()
                    ->withInput($request->only('email'))
                    ->withErrors(['email' => 'Has superado el límite de intentos. Cuenta bloqueada por 15 minutos.']);
            }

            $this->registrarIpFallida($request);
            return $this->respuestaCredencialesInvalidas($request);
        }

        // 7. Login exitoso ✓
        $request->session()->regenerate();

        // Limpiar rate limiter
        RateLimiter::clear($this->throttleKey($request));

        // Resetear intentos y actualizar last_login
        $usuario->resetearIntentos();

        // Registrar actividad
        RegistroActividad::registrar(
            'iniciar_sesion', 'usuario', $usuario->id,
            "Sesión iniciada correctamente.",
            $usuario->id, $request->ip()
        );

        // Redirigir según rol
        return redirect()->intended($this->redireccionPorRol($usuario));
    }

    // LOGOUT

    public function destroy(Request $request): RedirectResponse
    {
        $usuario = Auth::user();

        if ($usuario) {
            RegistroActividad::registrar(
                'cerrar_sesion', 'usuario', $usuario->id,
                'Sesión cerrada correctamente.',
                $usuario->id, $request->ip()
            );
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Sesión cerrada correctamente.');
    }

    // HELPERS PRIVADOS

    /**
     * Redirección según el rol del usuario.
     */
    private function redireccionPorRol(Usuario $usuario): string
    {
        return match ($usuario->rol) {
            'Superadmin' => route('dashboard'),
            'Aux_QHSE'   => route('qhse.dashboard'),
            'Admin'      => route('empresa.dashboard'),
            default      => route('dashboard'),
        };
    }

    /**
     * Respuesta genérica para credenciales inválidas.
     * Nunca revela si el email existe o no.
     */
    private function respuestaCredencialesInvalidas(Request $request): RedirectResponse
    {
        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'Las credenciales no son correctas.']);
    }

    /**
     * Verifica rate limiting por IP (máx 10 intentos / minuto).
     */
    private function demasiadosIntentosPorIP(Request $request): bool
    {
        return RateLimiter::tooManyAttempts(
            'login-ip:' . $request->ip(),
            10  // máximo 10 intentos por minuto por IP
        );
    }

    /**
     * Registra un intento fallido en el rate limiter por IP.
     */
    private function registrarIpFallida(Request $request): void
    {
        RateLimiter::hit(
            'login-ip:' . $request->ip(),
            60  // ventana de 60 segundos
        );
    }

    /**
     * Clave de throttle única por email + IP (para RateLimiter::clear).
     */
    private function throttleKey(Request $request): string
    {
        return Str::transliterate(
            Str::lower($request->email) . '|' . $request->ip()
        );
    }
}
