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
    </body>
</html>