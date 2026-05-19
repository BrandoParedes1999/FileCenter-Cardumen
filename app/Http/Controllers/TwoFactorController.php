<?php

namespace App\Http\Controllers;

use App\Models\RegistroActividad;
use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class TwoFactorController extends Controller
{
    private Google2FA $g2fa;

    public function __construct()
    {
        $this->g2fa = new Google2FA();
    }

    // ── Setup: mostrar QR para escanear ──────────────────────────────

    public function showSetup(): View|RedirectResponse
    {
        $usuario = Auth::user();

        if ($usuario->tieneDosFactores()) {
            return redirect()->route('profile.edit')->with('info', 'El 2FA ya está habilitado.');
        }

        // Generar secreto si no existe todavía
        if (!$usuario->two_factor_secret) {
            $secret = $this->g2fa->generateSecretKey();
            $usuario->update(['two_factor_secret' => $secret]);
        }

        $qrCode = $this->generarQR($usuario);

        return view('auth.two-factor-setup', [
            'usuario' => $usuario,
            'secret'  => $usuario->two_factor_secret,
            'qrCode'  => $qrCode,
        ]);
    }

    // ── Confirmar y activar 2FA ───────────────────────────────────────

    public function enable(Request $request): RedirectResponse
    {
        $request->validate(['code' => ['required', 'string', 'size:6', 'regex:/^[0-9]+$/']]);

        $usuario = Auth::user();

        if (!$usuario->two_factor_secret) {
            return back()->withErrors(['code' => 'Primero genera el código QR.']);
        }

        $lastTimestamp = Cache::get("2fa_setup_ts_{$usuario->id}", 0);
        $newTimestamp  = $this->g2fa->verifyKeyNewer($usuario->two_factor_secret, $request->code, $lastTimestamp);

        if ($newTimestamp === false) {
            return back()->withErrors(['code' => 'Código incorrecto. Verifica tu app y vuelve a intentar.']);
        }

        Cache::put("2fa_setup_ts_{$usuario->id}", $newTimestamp, now()->addMinutes(5));

        $usuario->update(['two_factor_confirmed_at' => now()]);

        RegistroActividad::registrar('2fa_activado', 'usuario', $usuario->id, '2FA activado correctamente.');

        return redirect()->route('profile.edit')->with('success', 'Autenticación de dos factores activada correctamente.');
    }

    // ── Desactivar 2FA ───────────────────────────────────────────────

    public function disable(Request $request): RedirectResponse
    {
        $request->validate(['password' => ['required', 'current_password']]);

        $usuario = Auth::user();
        $usuario->update([
            'two_factor_secret'       => null,
            'two_factor_confirmed_at' => null,
        ]);

        RegistroActividad::registrar('2fa_desactivado', 'usuario', $usuario->id, '2FA desactivado.');

        return redirect()->route('profile.edit')->with('success', 'Autenticación de dos factores desactivada.');
    }

    // ── Challenge: pedir código después del login ─────────────────────

    public function showChallenge(): View|RedirectResponse
    {
        if (!session()->has('2fa.user_id')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor-challenge');
    }

    public function challenge(Request $request): RedirectResponse
    {
        if (!session()->has('2fa.user_id')) {
            return redirect()->route('login');
        }

        $request->validate(['code' => ['required', 'string']]);

        $usuario = Usuario::find(session('2fa.user_id'));

        if (!$usuario) {
            session()->forget('2fa.user_id');
            return redirect()->route('login');
        }

        $codigo        = preg_replace('/\s+/', '', $request->code);
        $userId        = $usuario->id;
        $lastTimestamp = Cache::get("2fa_ts_{$userId}", 0);
        $newTimestamp  = $this->g2fa->verifyKeyNewer($usuario->two_factor_secret, $codigo, $lastTimestamp);

        if ($newTimestamp === false) {
            return back()->withErrors(['code' => 'Código incorrecto o expirado.']);
        }

        Cache::put("2fa_ts_{$userId}", $newTimestamp, now()->addMinutes(5));

        // Login exitoso: completar autenticación
        session()->forget('2fa.user_id');
        Auth::login($usuario, session()->pull('2fa.remember', false));
        $request->session()->regenerate();

        $usuario->resetearIntentos();

        RegistroActividad::registrar(
            'iniciar_sesion', 'usuario', $usuario->id,
            'Sesión iniciada con 2FA.',
            $usuario->id, $request->ip()
        );

        return redirect()->intended(route('dashboard'));
    }

    // ── Helpers ──────────────────────────────────────────────────────

    private function generarQR(Usuario $usuario): string
    {
        $otpUrl = $this->g2fa->getQRCodeUrl(
            config('app.name', 'FileCenter'),
            $usuario->email,
            $usuario->two_factor_secret
        );

        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);

        return base64_encode($writer->writeString($otpUrl));
    }
}
