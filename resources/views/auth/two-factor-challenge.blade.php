<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación 2FA — FileCenter</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { min-height:100vh; background:#f1f5f9; display:flex; align-items:center; justify-content:center; font-family:system-ui,sans-serif; }
        .ch-card { width:100%; max-width:400px; background:#fff; border:1px solid #e2e8f0; border-radius:20px; padding:36px; box-shadow:0 8px 32px rgba(0,0,0,.07); }
        .ch-logo { display:flex; align-items:center; justify-content:center; gap:10px; margin-bottom:28px; }
        .ch-title { font-size:20px; font-weight:800; color:#1e293b; text-align:center; margin-bottom:6px; }
        .ch-sub   { font-size:13px; color:#64748b; text-align:center; margin-bottom:28px; }
        .ch-input { width:100%; padding:14px; border-radius:12px; border:1.5px solid #e2e8f0; font-size:22px; text-align:center; letter-spacing:.5em; font-family:monospace; outline:none; transition:border-color .15s; box-sizing:border-box; }
        .ch-input:focus { border-color:#6366f1; box-shadow:0 0 0 3px rgba(99,102,241,.15); }
        .ch-btn   { width:100%; padding:13px; background:#6366f1; color:#fff; border:none; border-radius:12px; font-size:14px; font-weight:700; cursor:pointer; margin-top:18px; transition:background .15s; }
        .ch-btn:hover { background:#4f46e5; }
        .ch-error { font-size:12px; color:#dc2626; margin-top:8px; text-align:center; }
        .ch-back  { text-align:center; margin-top:16px; font-size:12px; color:#94a3b8; }
        .ch-back a { color:#6366f1; text-decoration:none; }
    </style>
</head>
<body>
<div class="ch-card">
    <div class="ch-logo">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="#6366f1">
            <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/>
        </svg>
        <span style="font-size:18px;font-weight:800;color:#1e293b">FileCenter</span>
    </div>

    <div class="ch-title">Verificación de dos factores</div>
    <div class="ch-sub">Abre tu app de autenticación e ingresa el código de 6 dígitos.</div>

    <form method="POST" action="{{ route('two-factor.verify') }}">
        @csrf
        <input type="text"
               name="code"
               class="ch-input"
               maxlength="6"
               inputmode="numeric"
               autocomplete="one-time-code"
               placeholder="000000"
               autofocus>

        @error('code')
        <div class="ch-error">{{ $message }}</div>
        @enderror

        <button type="submit" class="ch-btn">Verificar e ingresar</button>
    </form>

    <div class="ch-back">
        <a href="{{ route('login') }}">← Volver al inicio de sesión</a>
    </div>
</div>
</body>
</html>
