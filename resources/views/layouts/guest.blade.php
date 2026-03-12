<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'FileCenter') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-gray-900">
        <div class="min-h-screen flex">
            
            <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-[#1e1b4b] via-[#312e81] to-[#1e1b4b] p-16 flex-col justify-between text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 bg-white/5 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 bg-indigo-500/10 rounded-full blur-3xl"></div>

                <div class="relative z-10">
                    <div class="flex items-center gap-3 mb-12">
                        <div class="p-2 bg-white/10 rounded-lg backdrop-blur-md">
                            <x-application-logo class="w-8 h-8 fill-current text-white" />
                        </div>
                        <div>
                            <h2 class="text-xl font-bold tracking-tight">FileCenter Cardumen</h2>
                            <p class="text-[10px] uppercase tracking-widest opacity-60">Corporativo Cardumen</p>
                        </div>
                    </div>
                    
                    <div class="mt-16">
                        <div class="inline-flex items-center gap-2 bg-white/10 border border-white/20 px-3 py-1 rounded-full backdrop-blur-md mb-6">
                            <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                            <span class="text-xs font-medium text-indigo-100">Sistema QHSE Corporativo</span>
                        </div>
                        
                        <h1 class="text-6xl font-black mt-4 leading-[1.1]">Repositorio <br><span class="text-indigo-400">Inteligente</span></h1>
                        
                        <p class="mt-8 text-lg text-indigo-100/80 max-w-md leading-relaxed">
                            Gestiona documentos, organiza áreas y controla el acceso de tu equipo desde un solo lugar de manera eficiente y segura.
                        </p>
                        
                        <ul class="mt-12 space-y-5">
                            <li class="flex items-center gap-4 group">
                                <span class="p-2 bg-white/5 rounded-lg group-hover:bg-white/10 transition">📁</span>
                                <span class="text-indigo-100/90 font-medium text-sm">Repositorios organizados por áreas</span>
                            </li>
                            <li class="flex items-center gap-4 group">
                                <span class="p-2 bg-white/5 rounded-lg group-hover:bg-white/10 transition">🛡️</span>
                                <span class="text-indigo-100/90 font-medium text-sm">Control de acceso por roles</span>
                            </li>
                            <li class="flex items-center gap-4 group">
                                <span class="p-2 bg-white/5 rounded-lg group-hover:bg-white/10 transition">👥</span>
                                <span class="text-indigo-100/90 font-medium text-sm">Gestión de equipos y permisos</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="relative z-10 grid grid-cols-3 gap-8 border-t border-white/10 pt-10">
                    <div class="bg-white/5 p-4 rounded-2xl backdrop-blur-sm">
                        <p class="text-3xl font-black text-white">4</p>
                        <p class="text-[10px] uppercase tracking-tighter opacity-50 font-bold">Empresas</p>
                    </div>
                    <div class="bg-white/5 p-4 rounded-2xl backdrop-blur-sm">
                        <p class="text-3xl font-black text-white">16</p>
                        <p class="text-[10px] uppercase tracking-tighter opacity-50 font-bold">Carpetas</p>
                    </div>
                    <div class="bg-white/5 p-4 rounded-2xl backdrop-blur-sm">
                        <p class="text-3xl font-black text-white">3</p>
                        <p class="text-[10px] uppercase tracking-tighter opacity-50 font-bold">Usuarios QHSE</p>
                    </div>
                </div>
            </div>

            <div class="w-full lg:w-1/2 flex items-center justify-center bg-white p-8 sm:p-12 lg:p-20">
                <div class="w-full max-w-md">
                    {{ $slot }}
                </div>
            </div>

        </div>
    </body>
</html>