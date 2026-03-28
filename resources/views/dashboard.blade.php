<x-app-layout>
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

                    @if(!$esEmpleado && $solicitudesSubidaPendientes > 0)
                        <div style="display:flex;align-items:center;gap:14px;padding:14px 18px;
                                    background:rgba(245,158,11,.06);
                                    border:1px solid rgba(245,158,11,.25);
                                    border-radius:12px;margin-bottom:18px">
                            <div style="width:38px;height:38px;border-radius:10px;
                                        background:rgba(245,158,11,.15);
                                        display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="#d97706">
                                    <path d="M9 16h6v-6h4l-7-7-7 7h4zm-4 2h14v2H5z"/>
                                </svg>
                            </div>
                            <div style="flex:1">
                                <div style="font-size:13px;font-weight:700;color:#92400e">
                                    {{ $solicitudesSubidaPendientes }} archivo{{ $solicitudesSubidaPendientes != 1 ? 's' : '' }} pendiente{{ $solicitudesSubidaPendientes != 1 ? 's' : '' }} de aprobación
                                </div>
                                <div style="font-size:12px;color:#b45309;margin-top:2px">
                                    Usuarios han solicitado publicar archivos en carpetas con aprobación requerida.
                                </div>
                            </div>
                            <a href="{{ route('solicitudes-subida.index') }}"
                            class="fc-btn fc-btn-warning" style="font-size:12px;padding:7px 14px;white-space:nowrap">
                                Revisar ahora →
                            </a>
                        </div>
                    @endif


                    {{-- Estadísticas --}}
                    <div class="fc-stats">

                        <div class="fc-stat">
                            <div class="fc-stat-icon" style="background:rgba(124,58,237,0.13)">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="#a78bfa">
                                    <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                                </svg>
                            </div>
                            <div class="fc-stat-arrow">↗</div>
                            <div class="fc-stat-num">{{ $totalUsuarios }}</div>
                            <div class="fc-stat-label">Usuarios activos</div>
                            <div class="fc-stat-trend neutral">en el sistema</div>
                        </div>

                        <div class="fc-stat">
                            <div class="fc-stat-icon" style="background:rgba(13,148,136,0.13)">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="#2dd4bf">
                                    <path d="M20 6h-8l-2-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2z"/>
                                </svg>
                            </div>
                            <div class="fc-stat-arrow">↗</div>
                            <div class="fc-stat-num">{{ $totalEmpresas }}</div>
                            <div class="fc-stat-label">Áreas activas</div>
                            <div class="fc-stat-trend neutral">empresas</div>
                        </div>

                        <div class="fc-stat">
                            <div class="fc-stat-icon" style="background:rgba(217,119,6,0.13)">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="#fbbf24">
                                    <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6z"/>
                                </svg>
                            </div>
                            <div class="fc-stat-arrow">↗</div>
                            <div class="fc-stat-num">{{ $totalArchivos }}</div>
                            <div class="fc-stat-label">Total archivos</div>
                            <div class="fc-stat-trend neutral">activos</div>
                        </div>

                        <div class="fc-stat">
                            <div class="fc-stat-icon" style="background:rgba(29,78,216,0.13)">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="#60a5fa">
                                    <path d="M20 6h-8l-2-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2z"/>
                                </svg>
                            </div>
                            <div class="fc-stat-arrow">↗</div>
                            <div class="fc-stat-num">{{ $totalCarpetas }}</div>
                            <div class="fc-stat-label">Total carpetas</div>
                            <div class="fc-stat-trend neutral">en el sistema</div>
                        </div>

                        <div class="fc-stat">
                            <div class="fc-stat-icon" style="background:rgba(245,158,11,0.13)">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="#f59e0b">
                                    <path d="M9 16h6v-6h4l-7-7-7 7h4zm-4 2h14v2H5z"/>
                                </svg>
                            </div>
                            <div class="fc-stat-arrow">↗</div>
                            <div class="fc-stat-num">{{ $solicitudesSubidaPendientes }}</div>
                            <div class="fc-stat-label">Subidas pendientes</div>
                            <div class="fc-stat-trend neutral">
                                <a href="{{ route('solicitudes-subida.index') }}" style="color:#6366f1">revisar</a>
                            </div>
                        </div>
                    </div>

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