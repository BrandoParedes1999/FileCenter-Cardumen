@php
    $empresa = Auth::user()->empresa;
    $colorBg      = $empresa->color_primario   ?? '#13151f';
    $colorAccent  = $empresa->color_secundario ?? '#4f46e5';
    $colorBorder  = $empresa->color_primario   ? $empresa->color_primario.'33' : '#1e2130';
    $logoPath     = $empresa->logo             ? asset('images/empresas/'.$empresa->logo) : asset('images/logo.png');
    $esCorp       = $empresa->es_corporativo   ?? false;

    // Conteo de solicitudes de subida pendientes para badge
    $solSubidaPendientes = 0;
    if (in_array(Auth::user()->rol, ['Superadmin','Aux_QHSE','Admin','Gerente'])) {
        try {
            $solSubidaPendientes = \App\Models\SolicitudSubida::where('status', 'Pendiente')
                ->when(
                    !in_array(Auth::user()->rol, ['Superadmin','Aux_QHSE']),
                    fn($q) => $q->whereHas('carpeta', fn($c) => $c->where('empresa_id', Auth::user()->empresa_id))
                )
                ->count();
        } catch (\Exception $e) {
            $solSubidaPendientes = 0;
        }
    }
@endphp

<style>
    .fc-sidebar-dynamic {
        background: {{ $colorBg }};
        border-right-color: {{ $colorBorder }};
    }
    .fc-sidebar-dynamic .fc-logo-area {
        border-bottom-color: {{ $colorBorder }};
    }
    .fc-sidebar-dynamic .fc-sidebar-footer {
        border-top-color: {{ $colorBorder }};
    }
    .fc-sidebar-dynamic .fc-nav-item:hover {
        background: rgba(255,255,255,0.08);
        color: #fff;
    }
    .fc-sidebar-dynamic .fc-nav-item.active {
        background: {{ $colorAccent }}44;
        color: {{ $colorAccent }};
    }
    .fc-sidebar-dynamic .fc-nav-item.active svg {
        color: {{ $colorAccent }};
    }
    .fc-sidebar-dynamic .fc-nav-label {
        color: rgba(255,255,255,0.35);
    }
    .fc-sidebar-dynamic .fc-logo-text { color: #f1f5f9; }
    .fc-sidebar-dynamic .fc-logo-sub  { color: rgba(255,255,255,0.45); }
    .fc-sidebar-dynamic .fc-user-name { color: #e2e8f0; }
    .fc-sidebar-dynamic .fc-user-role { color: rgba(255,255,255,0.45); }
    .fc-sidebar-dynamic .fc-badge-role {
        background: {{ $colorAccent }}22;
        color: {{ $colorAccent }};
        border-color: {{ $colorAccent }}44;
    }
    .fc-topbar-accent::after {
        content: '';
        position: absolute;
        bottom: 0; left: 0; right: 0;
        height: 2px;
        background: linear-gradient(90deg, {{ $colorAccent }}, {{ $colorBg }});
        opacity: 0.6;
    }
    .fc-badge-empresa {
        background: {{ $colorAccent }}18;
        color: {{ $colorAccent }};
        border: 1px solid {{ $colorAccent }}33;
    }

    /* Badge de notificación en sidebar */
    .fc-nav-badge {
        font-size: 10px;
        font-weight: 700;
        background: #ef4444;
        color: #fff;
        border-radius: 999px;
        padding: 1px 7px;
        margin-left: auto;
        line-height: 1.6;
    }
</style>

<aside class="fc-sidebar fc-sidebar-dynamic">

    <div class="fc-logo-area">
        <div class="fc-logo-wrap">
            <img src="{{ $logoPath }}"
                 alt="{{ $empresa->siglas ?? 'FC' }}"
                 style="width:40px;height:40px;object-fit:contain;border-radius:8px;background:rgba(255,255,255,0.1);padding:4px"
                 onerror="this.src='{{ asset('images/logo.png') }}'">
            <div>
                <div class="fc-logo-text">
                    {{ $esCorp ? 'FileCenter Cardumen' : ($empresa->nombre ?? 'FileCenter') }}
                </div>
                <div class="fc-logo-sub">
                    @if($esCorp)
                        Sistema QHSE · Corporativo
                    @else
                        {{ $empresa->siglas ?? '' }} · Sistema QHSE
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Nav principal --}}
    <div class="fc-nav-section">
        <div class="fc-nav-label">Principal</div>
        <a href="{{ route('dashboard') }}"
           class="fc-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
            </svg>
            Dashboard
        </a>
        <a href="{{ route('carpetas.index') }}"
           class="fc-nav-item {{ request()->routeIs('carpetas.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M20 6h-8l-2-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2z"/>
            </svg>
            Mis Carpetas
        </a>
        <a href="{{ route('solicitudes.index') }}"
           class="fc-nav-item {{ request()->routeIs('solicitudes.*') && !request()->routeIs('solicitudes-subida.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
            </svg>
            Solicitudes
        </a>
        <a href="{{ route('areas.index') }}"
           class="fc-nav-item {{ request()->routeIs('areas.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M10 3H3v7h7V3zm11 0h-7v7h7V3zM10 14H3v7h7v-7zm11 3h-7v4h7v-4z"/>
            </svg>
            Mis Áreas
        </a>
    </div>

    {{-- Solicitudes de subida (solo para gestores) --}}
    @if(in_array(Auth::user()->rol, ['Superadmin', 'Aux_QHSE', 'Admin', 'Gerente']))
    <div class="fc-nav-section">
        <div class="fc-nav-label">Gestión de archivos</div>
        <a href="{{ route('solicitudes-subida.index') }}"
           class="fc-nav-item {{ request()->routeIs('solicitudes-subida.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M9 16h6v-6h4l-7-7-7 7h4zm-4 2h14v2H5z"/>
            </svg>
            Subidas pendientes
            @if($solSubidaPendientes > 0)
                <span class="fc-nav-badge">{{ $solSubidaPendientes }}</span>
            @endif
        </a>
    </div>
    @endif

    {{-- Sección corporativo/empresas --}}
    <div class="fc-nav-section">
        <div class="fc-nav-label">Cardumen</div>
        @php
            $empCorp = \App\Models\Empresa::where('es_corporativo', true)->where('activo', true)->first();
        @endphp
        @if($empCorp)
        <a href="{{ route('areas.show', $empCorp) }}"
           class="fc-nav-item {{ request()->routeIs('areas.show') && request()->route('empresa')?->id === $empCorp->id ? 'active' : '' }}">
            <div class="fc-dot" style="background:#a5b4fc"></div>
            Corporativo
        </a>
        @endif
        <a href="{{ route('nosotros') }}"
           class="fc-nav-item {{ request()->routeIs('nosotros') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
            </svg>
            Nosotros
        </a>
    </div>

    {{-- Administración --}}
    @if(in_array(Auth::user()->rol, ['Superadmin', 'Aux_QHSE', 'Admin', 'Gerente']))
    <div class="fc-nav-section">
        <div class="fc-nav-label">Administración</div>

        <a href="{{ route('usuarios.index') }}"
           class="fc-nav-item {{ request()->routeIs('usuarios.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
            </svg>
            Usuarios
        </a>

        @if(in_array(Auth::user()->rol, ['Superadmin', 'Aux_QHSE']))
        <a href="{{ route('empresas.index') }}"
           class="fc-nav-item {{ request()->routeIs('empresas.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 7V3H2v18h20V7H12zM6 19H4v-2h2v2zm0-4H4v-2h2v2zm0-4H4V9h2v2zm0-4H4V5h2v2zm4 12H8v-2h2v2zm0-4H8v-2h2v2zm0-4H8V9h2v2zm0-4H8V5h2v2zm10 12h-8v-2h2v-2h-2v-2h2v-2h-2V9h8v10zm-2-8h-2v2h2v-2zm0 4h-2v2h2v-2z"/>
            </svg>
            Empresas
        </a>
        @endif
    </div>
    @endif

    {{-- Footer --}}
    <div class="fc-sidebar-footer">
        <div class="fc-user-info">
            <div class="fc-avatar"
                 style="background:linear-gradient(135deg,{{ $colorBg }},{{ $colorAccent }});border:2px solid {{ $colorAccent }}44">
                {{ strtoupper(substr(Auth::user()->nombre,0,1)) }}{{ strtoupper(substr(Auth::user()->paterno,0,1)) }}
            </div>
            <div style="flex:1;min-width:0">
                <div class="fc-user-name">{{ Auth::user()->nombre_completo }}</div>
                <div class="fc-user-role">{{ Auth::user()->email }}</div>
                <div class="fc-badge-role">{{ Auth::user()->rol }}</div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}" style="margin-top:10px">
            @csrf
            <button type="submit" class="fc-nav-item"
                style="width:100%;background:none;border:none;text-align:left;cursor:pointer;color:rgba(255,255,255,0.45);font-size:12px">
                <svg viewBox="0 0 24 24" fill="currentColor" style="width:14px;height:14px">
                    <path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/>
                </svg>
                Cerrar Sesión
            </button>
        </form>
    </div>

</aside>