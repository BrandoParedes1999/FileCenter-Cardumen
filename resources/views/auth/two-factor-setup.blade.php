<x-app-layout>
<style>
.tf-wrap { max-width: 520px; margin: 48px auto; padding: 0 16px; }
.tf-card  { background:#fff; border:1px solid #e2e8f0; border-radius:20px; padding:32px;
            box-shadow:0 4px 20px rgba(0,0,0,.06); }
.tf-title { font-size:20px; font-weight:800; color:#1e293b; margin-bottom:6px; }
.tf-sub   { font-size:13px; color:#64748b; margin-bottom:28px; }
.tf-steps { counter-reset: step; list-style:none; padding:0; margin:0 0 28px; }
.tf-steps li { display:flex; align-items:flex-start; gap:12px; margin-bottom:14px; font-size:13px; color:#334155; }
.tf-steps li::before { counter-increment:step; content:counter(step);
    width:24px; height:24px; border-radius:50%; background:#6366f1; color:#fff;
    font-size:11px; font-weight:700; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.tf-qr-wrap { text-align:center; padding:20px; background:#f8fafc; border-radius:12px;
              border:1px dashed #cbd5e1; margin-bottom:24px; }
.tf-secret { font-family:monospace; font-size:13px; background:#ede9fe; color:#6d28d9;
             padding:8px 14px; border-radius:8px; letter-spacing:.1em; text-align:center;
             margin-top:10px; word-break:break-all; }
.tf-input { width:100%; padding:12px; border-radius:10px; border:1px solid #e2e8f0;
            font-size:20px; text-align:center; letter-spacing:.4em; font-family:monospace;
            outline:none; transition:border-color .15s; }
.tf-input:focus { border-color:#6366f1; box-shadow:0 0 0 3px rgba(99,102,241,.12); }
.dark .tf-card  { background:#13111f; border-color:#1e1b4b; }
.dark .tf-title { color:#e0e7ff; }
.dark .tf-sub   { color:#a5b4fc; }
.dark .tf-steps li { color:#c7d2fe; }
.dark .tf-qr-wrap { background:#1a1830; border-color:#2d2a5e; }
</style>

<div class="fc-wrapper">
    @include('components.sidebar')
    <div class="fc-main" style="display:flex;flex-direction:column">
        <header class="fc-topbar">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="#6366f1">
                <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/>
            </svg>
            <span class="fc-topbar-title">Configurar 2FA</span>
            <div class="fc-topbar-right">
                <div class="fc-topbar-avatar">
                    {{ strtoupper(substr(Auth::user()->nombre,0,1)) }}{{ strtoupper(substr(Auth::user()->paterno,0,1)) }}
                </div>
                <div>
                    <div class="fc-topbar-name">{{ Auth::user()->nombre_completo }}</div>
                    <div class="fc-topbar-role">{{ Auth::user()->rol }}</div>
                </div>
            </div>
        </header>

        <div class="tf-wrap">
            <div class="tf-card">
                <div class="tf-title">Autenticación de dos factores</div>
                <div class="tf-sub">Aumenta la seguridad de tu cuenta con una app de autenticación (Google Authenticator, Authy, etc.).</div>

                <ol class="tf-steps">
                    <li>Instala una app de autenticación en tu teléfono.</li>
                    <li>Escanea el código QR de abajo o ingresa la clave manualmente.</li>
                    <li>Ingresa el código de 6 dígitos para confirmar y activar 2FA.</li>
                </ol>

                <div class="tf-qr-wrap">
                    <img src="data:image/svg+xml;base64,{{ $qrCode }}"
                         alt="QR Code 2FA"
                         style="width:180px;height:180px;display:block;margin:0 auto">
                    <div style="font-size:11px;color:#94a3b8;margin-top:10px">Código QR</div>
                    <div class="tf-secret">{{ $secret }}</div>
                    <div style="font-size:11px;color:#94a3b8;margin-top:6px">Clave manual (si no puedes escanear)</div>
                </div>

                <form method="POST" action="{{ route('two-factor.enable') }}">
                    @csrf

                    <label style="display:block;font-size:11px;font-weight:700;color:#64748b;
                                  text-transform:uppercase;letter-spacing:.05em;margin-bottom:8px">
                        Código de verificación
                    </label>
                    <input type="text"
                           name="code"
                           class="tf-input"
                           maxlength="6"
                           inputmode="numeric"
                           autocomplete="one-time-code"
                           placeholder="000000"
                           autofocus>

                    @error('code')
                    <div style="font-size:12px;color:#dc2626;margin-top:6px">{{ $message }}</div>
                    @enderror

                    <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:22px">
                        <a href="{{ route('profile.edit') }}" class="fc-btn fc-btn-outline">Cancelar</a>
                        <button type="submit" class="fc-btn fc-btn-primary">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" style="vertical-align:middle;margin-right:4px">
                                <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/>
                            </svg>
                            Activar 2FA
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
