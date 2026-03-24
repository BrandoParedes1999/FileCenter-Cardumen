<x-app-layout>
    {{-- Cargar estilos específicos del dashboard --}}
    @push('styles')
        <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
    @endpush

    <div class="fc-wrapper">

        {{-- Sidebar --}}
        @include('components.sidebar')

        {{-- Área principal --}}
        <div class="fc-main">

            {{-- Topbar --}}
            <header class="fc-topbar">
                <input class="fc-search" placeholder="Buscar archivos, carpetas..." />
                <div class="fc-topbar-right">
                    <div class="fc-notif">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="#64748b">
                            <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/>
                        </svg>
                        <div class="fc-notif-badge">2</div>
                    </div>
                    <div class="fc-topbar-avatar">
                        {{ strtoupper(substr(Auth::user()->nombre, 0, 1)) . strtoupper(substr(Auth::user()->paterno, 0, 1)) }}
                    </div>
                    <div>
                        <div class="fc-topbar-name">{{ Auth::user()->nombre_completo }}</div>
                        <div class="fc-topbar-role">{{ Auth::user()->rol }}</div>
                    </div>
                </div>
            </header>

            {{-- Contenido --}}
            <div class="fc-content">

                {{-- Columna principal --}}
                <div class="fc-content-main">

                    {{-- Hero --}}
                    <x-hero
                        badge="{{ Auth::user()->rol }}"
                        title="Panel de Control Global"
                        subtitle="Tienes acceso completo a todas las áreas y configuraciones del sistema."
                        buttonLeft="{{ Auth::user()->rol == 'Superadmin' ? 'Gestionar Usuarios' : '' }}"
                        buttonRight="{{ Auth::user()->rol == 'Superadmin' ? 'Ver Permisos' : '' }}"
                    />

                    {{-- Estadísticas --}}
                    <x-stats :stats="$stats"/>

                    {{-- Gráfica --}}
                    <x-chart :empresas="$empresas" :maxArchivos="$maxArchivos" />

                    {{-- Áreas --}}
                    <x-areas-grid :areas="$empresas" link="#" />

                </div>{{-- /fc-content-main --}}

                {{-- Panel lateral derecho --}}
                <div class="fc-content-side">

                    {{-- Actividad reciente --}}
                    <x-activity :activities="$actividad" />

                    {{-- Roles --}}
                    <x-roles-sidebar :roles="$usuariosPorRol" :maxRol="$maxRol" />

                    

                </div>{{-- /fc-content-side --}}

            </div>{{-- /fc-content --}}

        </div>{{-- /fc-main --}}

    </div>{{-- /fc-wrapper --}}
</x-app-layout>