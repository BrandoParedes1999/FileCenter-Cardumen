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

    {{-- ── Buscador funcional ── --}}
    <form method="GET" action="{{ route('buscar') }}" style="flex:1;max-width:400px">
        <div style="position:relative">
            <svg style="position:absolute;left:11px;top:50%;transform:translateY(-50%);pointer-events:none"
                 width="14" height="14" viewBox="0 0 24 24" fill="none"
                 stroke="#94a3b8" stroke-width="2" stroke-linecap="round">
                <circle cx="11" cy="11" r="6"/><path d="m21 21-4.35-4.35"/>
            </svg>
            <input
                class="fc-search"
                type="text"
                name="q"
                placeholder="Buscar archivos, carpetas..."
                autocomplete="off"
                style="padding-left:34px">
        </div>
    </form>

    <div class="fc-topbar-right">

        {{-- ── Badge de notificaciones dinámico ── --}}
        @php
            $usuario = Auth::user();

            // Para admins/gestores: solicitudes pendientes de revisar
            // Para empleados/auxiliares: sus propias solicitudes procesadas no vistas
            if (in_array($usuario->rol, ['Superadmin', 'Aux_QHSE', 'Admin', 'Gerente'])) {

                $pendientesSubida = \App\Models\SolicitudSubida::where('status', 'Pendiente')
                    ->when(
                        !in_array($usuario->rol, ['Superadmin', 'Aux_QHSE']),
                        fn($q) => $q->whereHas('carpeta', fn($c) => $c->where('empresa_id', $usuario->empresa_id))
                    )
                    ->count();

                $pendientesAcceso = \App\Models\SolicitudAcceso::where('status', 'Pendiente')
                    ->when(
                        !in_array($usuario->rol, ['Superadmin', 'Aux_QHSE']),
                        fn($q) => $q->where('empresa_objetivo_id', $usuario->empresa_id)
                    )
                    ->count();

                $totalNotif = $pendientesSubida + $pendientesAcceso;
                $urlNotif   = $pendientesSubida >= $pendientesAcceso
                    ? route('solicitudes-subida.index')
                    : route('solicitudes.index');

            } else {
                // Empleado/Auxiliar: solicitudes propias resueltas recientemente (últimas 48h)
                $totalNotif = \App\Models\SolicitudAcceso::where('solicitante_id', $usuario->id)
                    ->whereIn('status', ['Aprobado', 'Rechazado'])
                    ->where('updated_at', '>=', now()->subHours(48))
                    ->count()
                    +
                    \App\Models\SolicitudSubida::where('solicitante_id', $usuario->id)
                    ->whereIn('status', ['Aprobado', 'Rechazado'])
                    ->where('updated_at', '>=', now()->subHours(48))
                    ->count();

                $urlNotif = route('solicitudes.index');
            }
        @endphp

        <a href="{{ $urlNotif }}"
           class="fc-notif"
           title="{{ $totalNotif > 0 ? $totalNotif . ' pendiente(s)' : 'Sin notificaciones' }}"
           style="text-decoration:none;position:relative">
            <svg width="20" height="20" viewBox="0 0 24 24"
                 fill="{{ $totalNotif > 0 ? '#6366f1' : '#94a3b8' }}">
                <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/>
            </svg>
            @if($totalNotif > 0)
            <div class="fc-notif-badge"
                 style="{{ $totalNotif > 9 ? 'min-width:18px;border-radius:9px;padding:0 4px' : '' }}">
                {{ $totalNotif > 99 ? '99+' : $totalNotif }}
            </div>
            @endif
        </a>

        {{-- ── Avatar y nombre ── --}}
        <div class="fc-topbar-avatar">
            {{ strtoupper(substr($usuario->nombre, 0, 1)) . strtoupper(substr($usuario->paterno, 0, 1)) }}
        </div>
        <div>
            <div class="fc-topbar-name">{{ $usuario->nombre_completo }}</div>
            <div class="fc-topbar-role">{{ $usuario->rol }}</div>
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

                    {{-- ── Tabla de archivos recientes ── --}}
                    @if($archivosRecientes->isNotEmpty())
                    <div style="margin-top:22px">
                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="#6366f1">
                                <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6z"/>
                            </svg>
                            <span style="font-size:13px;font-weight:700;color:#1e293b">Archivos recientes</span>
                            @if(!$esEmpleado)
                            <a href="{{ route('archivos.index') }}"
                               style="margin-left:auto;font-size:11px;color:#6366f1;text-decoration:none;
                                      padding:3px 10px;border:1px solid rgba(99,102,241,.2);
                                      border-radius:20px;background:rgba(99,102,241,.05)">
                                Ver todos →
                            </a>
                            @endif
                        </div>

                        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;overflow:hidden">
                            <table style="width:100%;border-collapse:collapse;font-size:12px">
                                <thead>
                                    <tr style="background:#fafbfc;border-bottom:1px solid #f1f5f9">
                                        <th style="padding:8px 14px;text-align:left;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em">Archivo</th>
                                        @if($esAdmin)
                                        <th style="padding:8px 14px;text-align:left;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em">Empresa</th>
                                        @endif
                                        <th style="padding:8px 14px;text-align:left;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em">Subido por</th>
                                        <th style="padding:8px 14px;text-align:left;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em">Fecha</th>
                                        <th style="padding:8px 14px;text-align:center;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($archivosRecientes as $af)
                                @php
                                    $extColor = match(strtolower($af->extension ?? '')) {
                                        'pdf'          => ['#dc2626', '#fee2e2'],
                                        'doc', 'docx'  => ['#2563eb', '#dbeafe'],
                                        'xls', 'xlsx'  => ['#059669', '#d1fae5'],
                                        default        => ['#64748b', '#f1f5f9'],
                                    };
                                @endphp
                                <tr style="border-bottom:1px solid #f8fafc;transition:background .1s"
                                    onmouseover="this.style.background='#fafbff'"
                                    onmouseout="this.style.background=''">
                                    <td style="padding:9px 14px;max-width:220px">
                                        <div style="display:flex;align-items:center;gap:8px">
                                            <span style="font-size:10px;font-weight:700;color:{{ $extColor[0] }};
                                                         background:{{ $extColor[1] }};padding:2px 6px;
                                                         border-radius:4px;text-transform:uppercase;flex-shrink:0">
                                                {{ strtoupper($af->extension ?? '?') }}
                                            </span>
                                            <span style="font-size:12px;color:#1e293b;font-weight:500;
                                                         white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                                                {{ $af->nombre_original }}
                                            </span>
                                        </div>
                                        <div style="font-size:10px;color:#94a3b8;margin-top:2px;padding-left:44px">
                                            📁 {{ $af->carpeta?->nombre ?? '—' }}
                                        </div>
                                    </td>
                                    @if($esAdmin)
                                    <td style="padding:9px 14px;font-size:11px;color:#64748b;white-space:nowrap">
                                        {{ $af->carpeta?->empresa?->siglas ?? '—' }}
                                    </td>
                                    @endif
                                    <td style="padding:9px 14px;font-size:11px;color:#64748b;white-space:nowrap">
                                        {{ $af->subidoPor?->nombre ?? '—' }}
                                    </td>
                                    <td style="padding:9px 14px;font-size:11px;color:#94a3b8;white-space:nowrap;font-family:monospace">
                                        {{ $af->created_at?->format('d/m/Y') }}
                                    </td>
                                    <td style="padding:9px 14px;text-align:center">
                                        <div style="display:flex;justify-content:center;gap:4px">
                                            <a href="{{ route('archivos.ver', $af) }}"
                                               title="Ver archivo"
                                               style="width:28px;height:28px;border-radius:7px;border:1px solid #e2e8f0;
                                                      background:#f8fafc;color:#94a3b8;display:flex;align-items:center;
                                                      justify-content:center;text-decoration:none;transition:all .15s"
                                               onmouseover="this.style.background='#ede9fe';this.style.color='#6366f1';this.style.borderColor='#c4b5fd'"
                                               onmouseout="this.style.background='#f8fafc';this.style.color='#94a3b8';this.style.borderColor='#e2e8f0'">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                                                </svg>
                                            </a>
                                            <a href="{{ route('archivos.descargar', $af) }}"
                                               title="Descargar archivo"
                                               style="width:28px;height:28px;border-radius:7px;border:1px solid #e2e8f0;
                                                      background:#f8fafc;color:#94a3b8;display:flex;align-items:center;
                                                      justify-content:center;text-decoration:none;transition:all .15s"
                                               onmouseover="this.style.background='#d1fae5';this.style.color='#059669';this.style.borderColor='#6ee7b7'"
                                               onmouseout="this.style.background='#f8fafc';this.style.color='#94a3b8';this.style.borderColor='#e2e8f0'">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/>
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                </div>{{-- /fc-content-main --}}

                {{-- Panel lateral derecho --}}
                <div class="fc-content-side">

                    {{-- Actividad reciente --}}
                    <x-activity :activities="$actividad" />

                    {{-- Roles --}}
                    <x-roles-sidebar :roles="$usuariosPorRol" :maxRol="$maxRol" />

                    {{-- ── Usuarios en línea (solo Superadmin) ── --}}
                    @if(Auth::user()->rol === 'Superadmin')
                    <div style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;
                                padding:16px;margin-top:16px">
                        <div style="display:flex;align-items:center;gap:7px;margin-bottom:12px">
                            <div style="width:8px;height:8px;border-radius:50%;background:#22c55e;
                                        box-shadow:0 0 0 3px rgba(34,197,94,.2)"></div>
                            <span style="font-size:13px;font-weight:700;color:#1e293b">Usuarios en línea</span>
                            <span style="margin-left:auto;font-size:11px;font-weight:600;
                                         background:rgba(34,197,94,.1);color:#16a34a;
                                         padding:2px 8px;border-radius:20px">
                                {{ $usuariosEnLinea->count() }} activo{{ $usuariosEnLinea->count() != 1 ? 's' : '' }}
                            </span>
                        </div>

                        @forelse($usuariosEnLinea as $ul)
                        <div style="display:flex;align-items:center;gap:8px;padding:7px 0;
                                    border-bottom:1px solid #f8fafc">
                            <div style="width:30px;height:30px;border-radius:50%;flex-shrink:0;
                                        background:linear-gradient(135deg,#4f46e5,#7c3aed);
                                        display:flex;align-items:center;justify-content:center;
                                        color:#fff;font-size:10px;font-weight:700;position:relative">
                                {{ strtoupper(substr($ul->nombre,0,1)) }}{{ strtoupper(substr($ul->paterno,0,1)) }}
                                <div style="position:absolute;bottom:-1px;right:-1px;
                                            width:9px;height:9px;border-radius:50%;
                                            background:#22c55e;border:1.5px solid #fff"></div>
                            </div>
                            <div style="flex:1;min-width:0">
                                <div style="font-size:12px;font-weight:600;color:#1e293b;
                                            white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                                    {{ $ul->nombre }} {{ $ul->paterno }}
                                </div>
                                <div style="font-size:10px;color:#94a3b8">
                                    {{ $ul->rol }}
                                    @if($ul->empresa) · {{ $ul->empresa->siglas }} @endif
                                </div>
                            </div>
                        </div>
                        @empty
                        <div style="text-align:center;padding:16px 0;font-size:12px;color:#94a3b8">
                            Sin usuarios activos en este momento
                        </div>
                        @endforelse

                        <div style="margin-top:8px;font-size:10px;color:#cbd5e1;text-align:center">
                            Activos en los últimos 15 minutos
                        </div>
                    </div>
                    @endif

                </div>{{-- /fc-content-side --}}

            </div>{{-- /fc-content --}}

        </div>{{-- /fc-main --}}

    </div>{{-- /fc-wrapper --}}
</x-app-layout>