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

        {{--
        ┌─────────────────────────────────────────────────────┐
        │  SCRIPT ANTI-PARPADEO                               │
        │  Se ejecuta ANTES de que cargue el HTML visible     │
        │  para aplicar el tema guardado sin flash de color   │
        └─────────────────────────────────────────────────────┘
        --}}
        <script>
            (function() {
                var tema = localStorage.getItem('tema');
                if (tema === 'oscuro') {
                    document.documentElement.classList.add('dark');
                }
            })();
        </script>
    </head>

    <body class="font-sans antialiased">
        {{ $slot }}

        {{--
        ┌─────────────────────────────────────────────────────┐
        │  FUNCIÓN GLOBAL DE MODO OSCURO                      │
        │  Disponible en todas las páginas del sistema        │
        └─────────────────────────────────────────────────────┘
        --}}
        <script>
            // Cambia entre modo claro y oscuro
            function toggleDarkMode() {
                var html     = document.documentElement;
                var sol      = document.getElementById('iconSol');
                var luna     = document.getElementById('iconLuna');
                var boton    = document.getElementById('darkToggle');
                var esOscuro = html.classList.contains('dark');

                if (esOscuro) {
                    // Cambiar a modo claro
                    html.classList.remove('dark');
                    localStorage.setItem('tema', 'claro');
                    if (sol)   sol.style.display   = '';
                    if (luna)  luna.style.display  = 'none';
                    if (boton) boton.style.background = '#f8fafc';
                    if (boton) boton.style.borderColor = '#e2e8f0';
                } else {
                    // Cambiar a modo oscuro
                    html.classList.add('dark');
                    localStorage.setItem('tema', 'oscuro');
                    if (sol)   sol.style.display   = 'none';
                    if (luna)  luna.style.display  = '';
                    if (boton) boton.style.background = '#1e1b4b';
                    if (boton) boton.style.borderColor = '#2d2a5e';
                }
            }

            // Al cargar la página, sincronizar el ícono con el tema guardado
            document.addEventListener('DOMContentLoaded', function() {
                var sol   = document.getElementById('iconSol');
                var luna  = document.getElementById('iconLuna');
                var boton = document.getElementById('darkToggle');

                if (localStorage.getItem('tema') === 'oscuro') {
                    if (sol)   sol.style.display   = 'none';
                    if (luna)  luna.style.display  = '';
                    if (boton) boton.style.background  = '#1e1b4b';
                    if (boton) boton.style.borderColor = '#2d2a5e';
                }
            });
        </script>

    </body>
</html>