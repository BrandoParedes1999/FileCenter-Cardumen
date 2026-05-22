<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'FileCenter') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        {{--
        ╔══════════════════════════════════════════════════════════════╗
        ║  CSS GLOBAL — UN SOLO ARCHIVO                               ║
        ║  dashboard.css y areas.css han sido CONSOLIDADOS aquí.      ║
        ║  No agregar css/dashboard.css ni css/areas.css de nuevo:    ║
        ║  sus reglas ya están en filecenter.css y causaban           ║
        ║  conflictos (fc-hero morado, fc-content sin flex, etc.)     ║
        ╚══════════════════════════════════════════════════════════════╝
        --}}
        <link rel="stylesheet" href="{{ asset('css/filecenter.css') }}">
        <link rel="stylesheet" href="{{ asset('css/areas.css') }}">
        <link rel="stylesheet" href="{{ asset('css/panel.css') }}">

        {{-- Scripts Vite --}}
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        {{-- Stack para CSS adicional específico de vista (ej: vistas con <style> inline) --}}
        @stack('styles')
    </head>
    <body class="font-sans antialiased">

        {{--
        |========================================================
        | El $slot ocupa 100% de la pantalla.
        | Cada vista controla su propio layout (fc-wrapper, etc.)
        |========================================================
        --}}
        {{ $slot }}

        @stack('scripts')


        {{--
    REEMPLAZAR el bloque @auth...@endauth completo al final de app.blade.php
    por este. Tres fixes:
    1. KEY incluye el ID del usuario → cada usuario tiene su propio localStorage
    2. No se pausa cuando la pestaña está en segundo plano
    3. Compara timestamps absolutos del archivo más reciente, no conteos de ventana deslizante
--}}

@auth
<style>
/* ── Tooltips de ayuda ──────────────────────────────────────── */
.fc-help {
    display: inline-flex; align-items: center; justify-content: center;
    width: 16px; height: 16px; border-radius: 50%;
    background: #e2e8f0; color: #64748b;
    font-size: 10px; font-weight: 700; cursor: default;
    vertical-align: middle; margin-left: 5px;
    position: relative; user-select: none; flex-shrink: 0;
    border: 1px solid #cbd5e1;
    transition: background .15s, color .15s;
}
.fc-help:hover { background: #6366f1; color: #fff; border-color: #6366f1; }
.fc-help::after {
    content: attr(data-tip);
    position: absolute;
    bottom: calc(100% + 8px);
    left: 50%; transform: translateX(-50%);
    background: #0f172a; color: #f1f5f9;
    font-size: 11px; font-weight: 400; line-height: 1.5;
    padding: 8px 12px; border-radius: 8px;
    white-space: pre-wrap; max-width: 260px; min-width: 160px;
    pointer-events: none; opacity: 0;
    transition: opacity .15s;
    z-index: 9000;
    box-shadow: 0 4px 16px rgba(0,0,0,.25);
    text-align: left;
}
.fc-help::before {
    content: '';
    position: absolute;
    bottom: calc(100% + 2px);
    left: 50%; transform: translateX(-50%);
    border: 5px solid transparent;
    border-top-color: #0f172a;
    pointer-events: none; opacity: 0;
    transition: opacity .15s;
    z-index: 9000;
}
.fc-help:hover::after,
.fc-help:hover::before { opacity: 1; }

/* Ajuste si el tooltip se sale por la izquierda */
.fc-help.tip-right::after { left: 0; transform: none; }
.fc-help.tip-right::before { left: 8px; transform: none; }

#fc-poll-toast {
    position: fixed;
    bottom: 28px;
    left: 50%;
    transform: translateX(-50%) translateY(120px);
    z-index: 9999;
    display: flex;
    align-items: center;
    gap: 10px;
    background: #0f172a;
    color: #f1f5f9;
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 14px;
    padding: 12px 16px;
    font-family: 'Segoe UI', system-ui, sans-serif;
    font-size: 13px;
    font-weight: 500;
    box-shadow: 0 8px 32px rgba(0,0,0,0.35);
    transition: transform 0.4s cubic-bezier(0.34,1.56,0.64,1);
    white-space: nowrap;
    pointer-events: none;
    max-width: calc(100vw - 48px);
}
#fc-poll-toast.show {
    transform: translateX(-50%) translateY(0);
    pointer-events: auto;
}
#fc-poll-toast .fc-toast-icon {
    width: 28px;
    height: 28px;
    border-radius: 8px;
    background: rgba(99,102,241,0.25);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
#fc-poll-toast .fc-toast-msg {
    flex: 1;
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
}
#fc-poll-toast .fc-toast-msg strong {
    display: block;
    font-size: 13px;
    color: #f1f5f9;
}
#fc-poll-toast .fc-toast-msg span {
    display: block;
    font-size: 11px;
    color: rgba(241,245,249,0.55);
    margin-top: 1px;
}
#fc-poll-toast .fc-toast-reload {
    background: #6366f1;
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 6px 14px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    white-space: nowrap;
    transition: background 0.15s;
}
#fc-poll-toast .fc-toast-reload:hover { background: #4f46e5; }
#fc-poll-toast .fc-toast-dismiss {
    background: transparent;
    border: none;
    color: rgba(241,245,249,0.4);
    cursor: pointer;
    font-size: 18px;
    line-height: 1;
    padding: 2px 4px;
    transition: color 0.15s;
}
#fc-poll-toast .fc-toast-dismiss:hover { color: rgba(241,245,249,0.8); }
</style>

<div id="fc-poll-toast" role="alert" aria-live="polite">
    <div class="fc-toast-icon">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="#a5b4fc">
            <path d="M12 4V1L8 5l4 4V6c3.31 0 6 2.69 6 6s-2.69 6-6 6-6-2.69-6-6H4c0 4.42 3.58 8 8 8s8-3.58 8-8-3.58-8-8-8z"/>
        </svg>
    </div>
    <div class="fc-toast-msg">
        <strong id="fc-toast-titulo">Hay cambios nuevos</strong>
        <span id="fc-toast-detalle"></span>
    </div>
    <button class="fc-toast-reload" onclick="location.reload()">Actualizar</button>
    <button class="fc-toast-dismiss" onclick="FcPoll.cerrarToast()" aria-label="Cerrar">✕</button>
</div>

<script>
// ── Audio Web API — sonido de notificación ───────────────────────
var _fcAudioCtx = null;

function _fcIniciarAudio() {
    if (!_fcAudioCtx) {
        try { _fcAudioCtx = new (window.AudioContext || window.webkitAudioContext)(); } catch (e) {}
    }
}

function _fcSonar() {
    _fcIniciarAudio();
    if (!_fcAudioCtx) return;
    var play = function () {
        try {
            var t = _fcAudioCtx.currentTime;
            // Dos tonos: agudo + grave (efecto "ding-dong" suave)
            [[880, 0, 0.04, 0.28, 0.22], [660, 0.18, 0.22, 0.48, 0.14]].forEach(function (c) {
                var o = _fcAudioCtx.createOscillator();
                var g = _fcAudioCtx.createGain();
                o.connect(g); g.connect(_fcAudioCtx.destination);
                o.type = 'sine';
                o.frequency.value = c[0];
                g.gain.setValueAtTime(0, t + c[1]);
                g.gain.linearRampToValueAtTime(c[4], t + c[2]);
                g.gain.exponentialRampToValueAtTime(0.001, t + c[3]);
                o.start(t + c[1]); o.stop(t + c[3] + 0.05);
            });
        } catch (e) {}
    };
    if (_fcAudioCtx.state === 'suspended') {
        _fcAudioCtx.resume().then(play).catch(function () {});
    } else {
        play();
    }
}

// Desbloquear AudioContext en la primera interacción del usuario
['click', 'keydown', 'touchstart'].forEach(function (ev) {
    document.addEventListener(ev, _fcIniciarAudio, { once: true });
});

// ── Polling de actualizaciones ───────────────────────────────────
window.FcPoll = (function () {
    'use strict';

    var POLL_URL  = '{{ route("actualizaciones.recientes") }}';
    var INTERVALO = 30 * 1000;
    var KEY       = 'fc_poll_v4_{{ Auth::id() }}';

    var timer       = null;
    var toastTimer  = null;
    var toastActivo = false;

    function guardar(d) {
        try { localStorage.setItem(KEY, JSON.stringify(d)); } catch (e) {}
    }
    function cargar() {
        try { var r = localStorage.getItem(KEY); return r ? JSON.parse(r) : null; }
        catch (e) { return null; }
    }

    function mostrarToast(titulo, detalle) {
        if (toastActivo) return;
        toastActivo = true;
        _fcSonar();
        document.getElementById('fc-toast-titulo').textContent  = titulo;
        document.getElementById('fc-toast-detalle').textContent = detalle;
        document.getElementById('fc-poll-toast').classList.add('show');
        if (toastTimer) clearTimeout(toastTimer);
        toastTimer = setTimeout(cerrarToast, 18000);
    }

    function cerrarToast() {
        document.getElementById('fc-poll-toast').classList.remove('show');
        toastActivo = false;
        if (toastTimer) clearTimeout(toastTimer);
    }

    function analizar(nuevo, prev) {
        var partes = [];

        // Solicitudes de subida pendientes
        var sn = nuevo.subidas_pendientes || 0;
        var sp = prev.subidas_pendientes  || 0;
        if (sn > sp) {
            var d = sn - sp;
            partes.push(d + ' solicitud' + (d > 1 ? 'es' : '') + ' de subida nueva' + (d > 1 ? 's' : ''));
        }

        // Solicitudes de acceso pendientes
        var an = nuevo.accesos_pendientes || 0;
        var ap = prev.accesos_pendientes  || 0;
        if (an > ap) {
            var d2 = an - ap;
            partes.push(d2 + ' solicitud' + (d2 > 1 ? 'es' : '') + ' de acceso nueva' + (d2 > 1 ? 's' : ''));
        }

        // Archivo más reciente (evita notificar en el primer poll)
        var uan = nuevo.ultimo_archivo_ts || 0;
        var uap = prev.ultimo_archivo_ts  || 0;
        if (uan > uap && uap > 0) {
            partes.push('hay archivos nuevos disponibles');
        }

        // Revisión de subida propia
        var rn = nuevo.ultima_revision_ts || 0;
        var rp = prev.ultima_revision_ts  || 0;
        if (rn > rp && rp > 0) {
            partes.push('una de tus subidas fue revisada');
        }

        // Revisión de solicitud de acceso propia
        var ran = nuevo.ultima_revision_acceso_ts || 0;
        var rap = prev.ultima_revision_acceso_ts  || 0;
        if (ran > rap && rap > 0) {
            partes.push('tu solicitud de acceso fue revisada');
        }

        return partes;
    }

    function actualizarBadges(datos) {
        // Badge de notificaciones no leídas
        var notifBadge = document.getElementById('fc-notif-badge');
        if (notifBadge) {
            var nn = datos.notif_no_leidas || 0;
            notifBadge.textContent   = nn > 99 ? '99+' : nn;
            notifBadge.style.display = nn > 0 ? '' : 'none';
        }

        // Badge de subidas pendientes
        var subidasBadge = document.getElementById('fc-subidas-badge');
        if (subidasBadge) {
            var sp2 = datos.subidas_pendientes || 0;
            subidasBadge.textContent   = sp2 > 99 ? '99+' : sp2;
            subidasBadge.style.display = sp2 > 0 ? '' : 'none';
        }
    }

    async function poll() {
        try {
            var res = await fetch(POLL_URL, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept':           'application/json',
                    'X-CSRF-TOKEN':     document.querySelector('meta[name="csrf-token"]')?.content || '',
                },
                credentials: 'same-origin',
            });

            if (!res.ok) return;

            var datos = await res.json();
            var prev  = cargar();

            if (prev) {
                var cambios = analizar(datos, prev);
                if (cambios.length > 0) {
                    var titulo = cambios.length === 1
                        ? 'Hay cambios en el sistema'
                        : cambios.length + ' cambios nuevos';
                    mostrarToast(titulo, cambios.join(' · '));
                }
            }

            guardar(datos);
            actualizarBadges(datos);

        } catch (e) {
            // Red caída — silencioso, reintenta en el siguiente ciclo
        }
    }

    function iniciar() {
        poll();
        timer = setInterval(poll, INTERVALO);
        window.addEventListener('online', poll);
    }

    return {
        iniciar:     iniciar,
        cerrarToast: cerrarToast,
        pollAhora:   poll,
    };

})();

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', FcPoll.iniciar);
} else {
    FcPoll.iniciar();
}
</script>
@endauth
    </body>
</html>