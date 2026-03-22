<x-app-layout>
@section('title', 'Dashboard')



@section('content')
@php
    $empresa      = $usuario->empresa;
    $colorBg      = $empresa->color_primario   ?? '#1B3A6B';
    $colorAccent  = $empresa->color_secundario ?? '#4f46e5';
    $logoUrl      = asset('images/empresas/'.($empresa->logo ?? 'logo_default.png'));
@endphp

{{-- Línea de acento superior con el color de la empresa --}}


<div style="height:3px;background:linear-gradient(90deg,{{ $colorAccent }},{{ $colorBg }});
            margin:-24px -28px 24px;opacity:.7"></div>

{{-- ══ HERO ════════════════════════════════════════════════════════════ --}}
<div class="fc-hero" style="border-left:4px solid {{ $colorAccent }}">
    <div>
        <div class="fc-hero-badge" style="background:{{ $colorAccent }}18;color:{{ $colorAccent }};border:1px solid {{ $colorAccent }}33">
            @if($esAdmin)
                <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/>
                </svg>
                {{ $rol === 'Superadmin' ? 'Super Administrador' : 'Auxiliar QHSE' }}
            @elseif($esGestor)
                <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 7V3H2v18h20V7H12z"/>
                </svg>
                {{ $rol }} · {{ $empresa->siglas }}
            @else
                <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                </svg>
                {{ $rol }} · {{ $empresa->siglas }}
            @endif
        </div>

        @if($esAdmin)
            <div class="fc-hero-title">Panel de Control Global</div>
            <div class="fc-hero-sub">
                Acceso completo a todas las áreas y configuraciones del sistema Cardumen.
            </div>
        @elseif($esGestor)
            <div class="fc-hero-title">Panel — {{ $empresa->nombre }}</div>
            <div class="fc-hero-sub">
                Gestión de usuarios, carpetas y actividad de tu empresa.
            </div>
        @else
            <div class="fc-hero-title">Bienvenido, {{ $usuario->nombre }}</div>
            <div class="fc-hero-sub">
                Accede a tus documentos y áreas de trabajo en {{ $empresa->nombre }}.
            </div>
        @endif
    </div>

    <div class="fc-hero-btns">
        @if($esAdmin)
            <a href="{{ route('usuarios.index') }}" class="fc-btn fc-btn-outline">Gestionar usuarios</a>
            <a href="{{ route('empresas.index') }}" class="fc-btn fc-btn-solid" style="background:{{ $colorAccent }}">Gestionar empresas</a>
        @elseif($esGestor)
            <a href="{{ route('usuarios.index') }}" class="fc-btn fc-btn-outline">Mi equipo</a>
            <a href="{{ route('carpetas.create') }}" class="fc-btn fc-btn-solid" style="background:{{ $colorAccent }}">Nueva carpeta</a>
        @else
            <a href="{{ route('areas.index') }}" class="fc-btn fc-btn-outline">Mis áreas</a>
            <a href="{{ route('carpetas.index') }}" class="fc-btn fc-btn-solid" style="background:{{ $colorAccent }}">Mis carpetas</a>
        @endif
    </div>
</div>

{{-- ══ ALERTAS (solicitudes pendientes) ═══════════════════════════════ --}}
@if(!$esEmpleado && $solicitudesPendientes > 0)
<div class="fc-alert-banner" style="background:#fef9c3;border:1px solid #fde047;border-radius:10px;padding:12px 18px;margin-bottom:20px;display:flex;align-items:center;gap:12px">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="#ca8a04">
        <path d="M12 2L1 21h22L12 2zm0 3.5L20.5 19h-17L12 5.5zm-1 5.5v4h2v-4h-2zm0 6v2h2v-2h-2z"/>
    </svg>
    <span style="font-size:13px;color:#713f12;font-weight:500">
        {{ $solicitudesPendientes }} solicitud{{ $solicitudesPendientes > 1 ? 'es' : '' }} de acceso pendiente{{ $solicitudesPendientes > 1 ? 's' : '' }} de aprobación.
    </span>
    <a href="{{ route('solicitudes.index') }}" style="margin-left:auto;font-size:13px;font-weight:600;color:#854d0e">
        Revisar →
    </a>
</div>
@endif

<div class="fc-content">
    <div class="fc-content-main">
        

        {{-- ══ STATS ══════════════════════════════════════════════ --}}
        <div class="fc-stats">

            {{-- Archivos: todos ven --}}
            <div class="fc-stat">
                <div class="fc-stat-icon" style="background:rgba(217,119,6,0.13)">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="#fbbf24">
                        <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6z"/>
                    </svg>
                </div>
                <div class="fc-stat-arrow">↗</div>
                <div class="fc-stat-num">{{ $totalArchivos }}</div>
                <div class="fc-stat-label">
                    {{ $esEmpleado ? 'Mis archivos' : 'Total archivos' }}
                </div>
            </div>

            {{-- Carpetas: todos ven --}}
            <div class="fc-stat">
                <div class="fc-stat-icon" style="background:rgba(29,78,216,0.13)">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="#60a5fa">
                        <path d="M20 6h-8l-2-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2z"/>
                    </svg>
                </div>
                <div class="fc-stat-num">{{ $totalCarpetas }}</div>
                <div class="fc-stat-label">Carpetas</div>
            </div>

            {{-- Usuarios: no para Empleado --}}
            @if(!$esEmpleado)
            <div class="fc-stat">
                <div class="fc-stat-icon" style="background:rgba(124,58,237,0.13)">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="#a78bfa">
                        <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                    </svg>
                </div>
                <div class="fc-stat-arrow">↗</div>
                <div class="fc-stat-num">{{ $totalUsuarios }}</div>
                <div class="fc-stat-label">Usuarios activos</div>
            </div>
            @endif

            {{-- Empresas: solo Admin --}}
            @if($esAdmin)
            <div class="fc-stat">
                <div class="fc-stat-icon" style="background:rgba(13,148,136,0.13)">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="#2dd4bf">
                        <path d="M12 7V3H2v18h20V7H12zM6 19H4v-2h2v2zm0-4H4v-2h2v2zm0-4H4V9h2v2zm0-4H4V5h2v2zm4 12H8v-2h2v2zm0-4H8v-2h2v2zm0-4H8V9h2v2zm0-4H8V5h2v2zm10 12h-8v-2h2v-2h-2v-2h2v-2h-2V9h8v10zm-2-8h-2v2h2v-2zm0 4h-2v2h2v-2z"/>
                    </svg>
                </div>
                <div class="fc-stat-num">{{ $totalEmpresas }}</div>
                <div class="fc-stat-label">Áreas activas</div>
                <div class="fc-stat-trend neutral">Estable</div>
            </div>
            @endif

            {{-- Empleado: stat extra — áreas accesibles --}}
            @if($esEmpleado)
            <div class="fc-stat">
                <div class="fc-stat-icon" style="background:{{ $colorAccent }}18">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="{{ $colorAccent }}">
                        <path d="M10 3H3v7h7V3zm11 0h-7v7h7V3zM10 14H3v7h7v-7zm11 3h-7v4h7v-4z"/>
                    </svg>
                </div>
                <div class="fc-stat-num">
                    {{ \App\Models\Empresa::where('activo',true)->where(fn($q)=>$q->where('id',$usuario->empresa_id)->orWhere('es_corporativo',true))->count() }}
                </div>
                <div class="fc-stat-label">Mis áreas</div>
            </div>
            @endif

        </div>{{-- /fc-stats --}}

        {{-- ══ GRÁFICA — solo Admin y Gestor ════════════════════ --}}
        @if(!$esEmpleado)
        <div class="fc-chart-box">
            <div class="fc-chart-header">
                <div>
                    <div class="fc-chart-title">Archivos por {{ $esAdmin ? 'Área' : 'Empresa' }}</div>
                    <div class="fc-chart-sub">Distribución de contenido</div>
                </div>
            </div>
            <div class="fc-chart-bars">
                <div class="fc-chart-yaxis">
                    @php $top = ceil($maxArchivos / 5) * 5 @endphp
                    @for($i = 4; $i >= 0; $i--)
                    <span>{{ round($top * $i / 4) }}</span>
                    @endfor
                </div>
                <div class="fc-chart-cols">
                    @foreach($empresas as $emp)
                    @php
                        $pct   = $maxArchivos > 0 ? round(($emp->total_archivos / $maxArchivos) * 100) : 0;
                        $color = $emp->color_secundario ?? $emp->color_primario ?? '#6366f1';
                    @endphp
                    <div class="fc-chart-col">
                        <div class="fc-chart-bar-wrap">
                            <div class="fc-chart-bar"
                                 style="height:{{ max($pct, 4) }}%;background:{{ $color }};opacity:{{ $emp->es_corporativo ? 1 : 0.8 }}">
                            </div>
                        </div>
                        <div class="fc-chart-col-label" title="{{ $emp->nombre }}">
                            {{ $emp->siglas }}
                        </div>
                        <div class="fc-chart-col-val">{{ $emp->total_archivos }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- ══ GRID DE ÁREAS ═══════════════════════════════════ --}}
        <div>
            <div class="fc-areas-header">
                <div class="fc-areas-title">
                    {{ $esEmpleado ? 'Mis áreas de trabajo' : 'Todas las áreas' }}
                </div>
                <a href="{{ route('areas.index') }}" class="fc-areas-link">Ver todas →</a>
            </div>
            <div class="fc-areas-grid">
                @foreach($empresas->take($esEmpleado ? 4 : 6) as $emp)
                @php
                    $dot = $emp->color_primario ?? '#6366f1';
                @endphp
                <a href="{{ route('areas.show', $emp) }}" class="fc-area-card">
                    <div style="display:flex;align-items:center;gap:10px">
                        <div class="fc-area-dot" style="background:{{ $dot }}"></div>
                        <div>
                            <div class="fc-area-name">{{ $emp->nombre }}</div>
                            <div class="fc-area-meta">
                                {{ $emp->total_archivos ?? 0 }} archivos
                                · {{ $emp->total_miembros ?? 0 }} miembros
                            </div>
                        </div>
                    </div>
                    <div class="fc-area-chevron">›</div>
                </a>
                @endforeach

                {{-- Empleado: carga sus áreas directamente --}}
                @if($esEmpleado && $empresas->isEmpty())
                @php
                    $misAreas = \App\Models\Empresa::activas()->where(fn($q)=>
                        $q->where('id',$usuario->empresa_id)->orWhere('es_corporativo',true)
                    )->get();
                @endphp
                @foreach($misAreas as $emp)
                <a href="{{ route('areas.show', $emp) }}" class="fc-area-card">
                    <div style="display:flex;align-items:center;gap:10px">
                        <div class="fc-area-dot" style="background:{{ $emp->color_primario ?? '#6366f1' }}"></div>
                        <div>
                            <div class="fc-area-name">{{ $emp->nombre }}</div>
                            <div class="fc-area-meta">
                                {{ $emp->is_corporativo ? 'Corporativo' : 'Mi empresa' }}
                            </div>
                        </div>
                    </div>
                    <div class="fc-area-chevron">›</div>
                </a>
                @endforeach
                @endif
            </div>
        </div>

        {{-- ══ ARCHIVOS RECIENTES — solo Empleado ════════════════ --}}
        @if($esEmpleado && $archivosRecientes->count())
        <div style="margin-top:24px">
            <div class="fc-areas-header">
                <div class="fc-areas-title">Archivos recientes</div>
                <a href="{{ route('carpetas.index') }}" class="fc-areas-link">Ver todos →</a>
            </div>
            <div class="fc-archivos-recientes">
                @foreach($archivosRecientes as $archivo)
                <a href="{{ route('archivos.show', $archivo) }}" class="fc-archivo-item">
                    <div class="fc-archivo-icon" style="background:{{ $colorAccent }}15">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="{{ $colorAccent }}">
                            <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6z"/>
                        </svg>
                    </div>
                    <div style="flex:1;min-width:0">
                        <div class="fc-archivo-nombre">{{ $archivo->nombre_original }}</div>
                        <div class="fc-archivo-meta">{{ $archivo->created_at->diffForHumans() }}</div>
                    </div>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2">
                        <path d="M9 18l6-6-6-6"/>
                    </svg>
                </a>
                @endforeach
            </div>
        </div>
        @endif

    </div>{{-- /fc-content-main --}}

    {{-- ══ PANEL LATERAL ══════════════════════════════════════════ --}}
    <div class="fc-content-side">

        {{-- Actividad reciente --}}
        <div class="fc-activity-card">
            <div class="fc-activity-title">
                <span>Actividad reciente</span>
                <span class="fc-activity-badge">{{ $actividad->count() }}</span>
            </div>

            @forelse($actividad as $evento)
            @php
                $iconBg = match($evento->accion) {
                    'subir'              => 'bg-up',
                    'descargar'          => 'bg-down',
                    'crear_carpeta'      => 'bg-plus',
                    'eliminar'           => 'bg-del',
                    'restaurar_version'  => 'bg-restore',
                    default              => 'bg-up',
                };
                $iconPath = match($evento->accion) {
                    'subir'             => 'M9 16h6v-6h4l-7-7-7 7h4zm-4 2h14v2H5z',
                    'descargar'         => 'M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z',
                    'crear_carpeta'     => 'M19 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-2 10h-4v4h-2v-4H7v-2h4V7h2v4h4v2z',
                    'eliminar'          => 'M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z',
                    'restaurar_version' => 'M12 5V1L7 6l5 5V7c3.31 0 6 2.69 6 6s-2.69 6-6 6-6-2.69-6-6H4c0 4.42 3.58 8 8 8s8-3.58 8-8-3.58-8-8-8z',
                    default             => 'M9 16h6v-6h4l-7-7-7 7h4zm-4 2h14v2H5z',
                };
            @endphp
            <div class="fc-act-item">
                <div class="fc-act-icon {{ $iconBg }}">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor">
                        <path d="{{ $iconPath }}"/>
                    </svg>
                </div>
                <div style="flex:1;min-width:0">
                    <div class="fc-act-name">
                        <strong>{{ $evento->usuario?->nombre ?? 'Sistema' }}</strong>
                        {{ match($evento->accion) {
                            'subir'            => 'subió',
                            'descargar'        => 'descargó',
                            'crear_carpeta'    => 'creó carpeta',
                            'eliminar'         => 'eliminó',
                            'restaurar_version'=> 'restauró versión',
                            default            => $evento->accion,
                        } }}
                    </div>
                    @if($evento->descripcion)
                        <div class="fc-act-file">{{ Str::limit($evento->descripcion, 38) }}</div>
                    @endif
                    <div class="fc-act-time">{{ $evento->created_at->diffForHumans() }}</div>
                </div>
            </div>
            @empty
            <div style="padding:20px 0;text-align:center;font-size:13px;color:#94a3b8">
                Sin actividad reciente.
            </div>
            @endforelse
        </div>

        {{-- Usuarios por rol — solo Admin/Gestor --}}
        @if(!$esEmpleado)
        <div class="fc-roles-card">
            <div class="fc-roles-title">
                Usuarios por rol
                @if($esGestor)
                    <span style="font-size:11px;color:#94a3b8;font-weight:400">· {{ $empresa->siglas }}</span>
                @endif
            </div>
            @forelse($usuariosPorRol as $item)
            <div class="fc-role-row">
                <div class="fc-role-name">{{ $item->rol }}</div>
                <div class="fc-role-bar-bg">
                    <div class="fc-role-bar"
                         style="width:{{ round(($item->total / $maxRol) * 100) }}%;background:{{ $item->color }}">
                    </div>
                </div>
                <div class="fc-role-count">{{ $item->total }}</div>
            </div>
            @empty
            <div style="font-size:13px;color:#94a3b8;padding:10px 0">Sin datos.</div>
            @endforelse
        </div>
        @endif

        {{-- Empleado: panel de acceso rápido --}}
        @if($esEmpleado)
        <div class="fc-roles-card">
            <div class="fc-roles-title">Acciones rápidas</div>
            <div style="display:flex;flex-direction:column;gap:8px;margin-top:12px">
                <a href="{{ route('solicitudes.create') }}"
                   class="fc-btn fc-btn-outline" style="justify-content:center;text-align:center">
                    Solicitar acceso a carpeta
                </a>
                <a href="{{ route('areas.index') }}"
                   class="fc-btn fc-btn-outline" style="justify-content:center;text-align:center">
                    Ver mis áreas
                </a>
                <a href="{{ route('carpetas.index') }}"
                   class="fc-btn fc-btn-outline" style="justify-content:center;text-align:center">
                    Explorar carpetas
                </a>
            </div>
        </div>
        @endif

    </div>{{-- /fc-content-side --}}
</div>{{-- /fc-content --}}
</x-app-layout>