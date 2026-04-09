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
window.FcPoll = (function () {
    'use strict';

    var POLL_URL  = '{{ route("actualizaciones.recientes") }}';
    var INTERVALO = 30 * 1000;

    // FIX 1: Key por usuario — evita que dos usuarios en el mismo
    // navegador compartan el mismo localStorage y "contaminen" el baseline.
    var KEY = 'fc_poll_v3_{{ Auth::id() }}';

    var timer      = null;
    var toastTimer = null;
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

    // FIX 3: comparar timestamps absolutos en vez de conteos
    // con ventana deslizante.
    function analizar(nuevo, prev) {
        var partes = [];

        // Solicitudes de subida — comparar conteo (no cambia con el tiempo)
        var sn = nuevo.subidas_pendientes || 0;
        var sp = prev.subidas_pendientes  || 0;
        if (sn > sp) {
            var d = sn - sp;
            partes.push(d + ' solicitud' + (d > 1 ? 'es' : '') + ' de subida nueva' + (d > 1 ? 's' : ''));
        }

        // Solicitudes de acceso — comparar conteo
        var an = nuevo.accesos_pendientes || 0;
        var ap = prev.accesos_pendientes  || 0;
        if (an > ap) {
            var d2 = an - ap;
            partes.push(d2 + ' solicitud' + (d2 > 1 ? 'es' : '') + ' de acceso');
        }

        // FIX 3A: comparar timestamp del archivo más reciente.
        // Si el ts subió, significa que hay un archivo más nuevo que
        // el que conocíamos. Funciona aunque abras la ventana días después.
        var uan = nuevo.ultimo_archivo_ts || 0;
        var uap = prev.ultimo_archivo_ts  || 0;
        if (uan > uap && uap > 0) {
            // uap > 0 evita notificar al primer poll (cuando no hay baseline aún)
            partes.push('hay archivos nuevos disponibles');
        }

        // FIX 3B: igual para revisión de mis subidas
        var rn = nuevo.ultima_revision_ts || 0;
        var rp = prev.ultima_revision_ts  || 0;
        if (rn > rp && rp > 0) {
            partes.push('una de tus subidas fue revisada');
        }

        return partes;
    }

    function actualizarBadges(datos) {
        var badge = document.querySelector('.fc-nav-badge');
        if (!badge) return;
        var total = (datos.subidas_pendientes || 0) + (datos.accesos_pendientes || 0);
        if (total > 0) {
            badge.textContent    = total > 99 ? '99+' : total;
            badge.style.display  = '';
        } else {
            badge.style.display  = 'none';
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
                    var titulo  = cambios.length === 1
                        ? 'Hay cambios en el sistema'
                        : cambios.length + ' cambios nuevos';
                    mostrarToast(titulo, cambios.join(' · '));
                }
            }

            guardar(datos);
            actualizarBadges(datos);

        } catch (e) {
            // Red caída — silencioso, lo reintentará en el siguiente ciclo
        }
    }

    function iniciar() {
        poll(); // primer poll inmediato para establecer baseline

        // FIX 2: NO pausar el polling cuando la pestaña está en
        // segundo plano. El intervalo de 30s ya es poco costoso;
        // pausarlo causaba que el usuario nunca viera la notificación
        // si tenía el foco en la ventana del admin.
        timer = setInterval(poll, INTERVALO);

        // Solo reactivar al volver de offline
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