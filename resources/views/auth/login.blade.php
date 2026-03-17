<x-guest-layout>

<style>
/* Inputs con efecto focus suave */
.login-input {
    display: block;
    width: 100%;
    padding: 13px 16px 13px 44px;
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
    border-radius: 14px;
    font-size: 14px;
    color: #1e293b;
    outline: none;
    transition: border-color .2s, box-shadow .2s, background .2s;
}
.login-input:focus {
    background: #fff;
    border-color: #6366f1;
    box-shadow: 0 0 0 4px rgba(99,102,241,0.1);
}
.login-input::placeholder { color: #94a3b8; }

/* Botón con shimmer al hover */
.login-btn {
    position: relative;
    overflow: hidden;
    width: 100%;
    padding: 13px;
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
    color: #fff;
    font-size: 14px;
    font-weight: 700;
    border: none;
    border-radius: 14px;
    cursor: pointer;
    transition: transform .15s, box-shadow .2s;
    box-shadow: 0 6px 20px rgba(79,70,229,0.35);
    letter-spacing: .02em;
}
.login-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 10px 28px rgba(79,70,229,0.45);
}
.login-btn:active { transform: translateY(0); }
.login-btn::after {
    content: '';
    position: absolute;
    top: 0; left: -100%;
    width: 60%; height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.18), transparent);
    transition: left .45s;
}
.login-btn:hover::after { left: 160%; }

/* Card semitransparente */
.login-card {
    background: rgba(255,255,255,0.92);
    border: 1px solid rgba(226,232,240,0.8);
    border-radius: 24px;
    padding: 36px 32px;
    box-shadow:
        0 4px 6px rgba(0,0,0,0.04),
        0 20px 48px rgba(79,70,229,0.08);
    backdrop-filter: blur(12px);
}
</style>

<div class="login-card">

    {{-- Encabezado --}}
    <div style="margin-bottom:28px">
        <div style="
            display:inline-flex; align-items:center; gap:6px;
            background:#f0f4ff; border:1px solid #e0e7ff;
            padding:4px 12px; border-radius:999px;
            margin-bottom:14px;
        ">
            <span style="width:6px;height:6px;border-radius:50%;background:#6366f1;display:inline-block;"></span>
            <span style="font-size:11px;font-weight:600;color:#6366f1;letter-spacing:.06em;">Sistema QHSE</span>
        </div>
        <h2 style="font-size:26px;font-weight:900;color:#0f172a;letter-spacing:-.5px;line-height:1.15;">
            Bienvenido
        </h2>
        <p style="font-size:13px;color:#64748b;margin-top:6px;line-height:1.5;">
            Inicia sesión para acceder al Sistema de Gestión Corporativo
        </p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        {{-- Email --}}
        <div style="margin-bottom:16px">
            <label for="email" style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;letter-spacing:.04em;text-transform:uppercase;">
                Correo electrónico
            </label>
            <div style="position:relative">
                <div style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#94a3b8;display:flex;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                        <polyline points="22,6 12,13 2,6"/>
                    </svg>
                </div>
                <input id="email" class="login-input" type="email" name="email"
                    value="{{ old('email') }}" required autofocus
                    placeholder="usuario@empresa.com" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- Contraseña --}}
        <div style="margin-bottom:20px">
            <label for="password" style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;letter-spacing:.04em;text-transform:uppercase;">
                Contraseña
            </label>
            <div style="position:relative">
                <div style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#94a3b8;display:flex;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0110 0v4"/>
                    </svg>
                </div>
                <input id="password" class="login-input" type="password" name="password"
                    required placeholder="••••••••" />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- Botón --}}
        <button type="submit" class="login-btn">
            <span style="display:flex;align-items:center;justify-content:center;gap:8px;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4"/>
                    <polyline points="10 17 15 12 10 7"/>
                    <line x1="15" y1="12" x2="3" y2="12"/>
                </svg>
                Iniciar Sesión
            </span>
        </button>

        {{-- Recuérdame + Olvidé contraseña --}}
        <div style="display:flex;align-items:center;justify-content:space-between;margin-top:16px;">
            <label for="remember_me" style="display:flex;align-items:center;gap:7px;cursor:pointer;">
                <input id="remember_me" type="checkbox" name="remember"
                    style="width:15px;height:15px;border-radius:4px;accent-color:#6366f1;cursor:pointer;">
                <span style="font-size:12px;color:#64748b;">Recuérdame</span>
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}"
                   style="font-size:12px;font-weight:700;color:#6366f1;text-decoration:none;transition:color .15s;"
                   onmouseover="this.style.color='#4338ca'"
                   onmouseout="this.style.color='#6366f1'">
                    ¿Olvidaste tu contraseña?
                </a>
            @endif
        </div>

    </form>
</div>

</x-guest-layout>