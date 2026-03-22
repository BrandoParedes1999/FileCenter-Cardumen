<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
       <link rel="stylesheet" href="{{ asset('css/filecenter.css') }}">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">

        {{--
        |========================================================
        | IMPORTANTE: Se eliminaron estas líneas que causaban
        | la doble navbar y el padding gris de fondo:
        |
        |   ❌ @include('layouts.navigation')   ← navbar Breeze|
        |   ❌ div.min-h-screen.bg-gray-100     ← fondo gris
        |   ❌ @isset($header) ... @endisset    ← cabecera extra
        |
        | Ahora el $slot ocupa 100% de la pantalla,
        | permitiendo que el dashboard controle su propio layout.
        |========================================================
        --}}

        {{ $slot }}

    </body>
</html>