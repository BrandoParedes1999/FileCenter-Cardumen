<x-app-layout>
@section('title', 'Gestión de Áreas')

@php
    $userEmpresa = Auth::user()->empresa;
    $accent = $userEmpresa->color_secundario ?? '#4f46e5';
@endphp

{{-- Header con stats globales --}}
<div class="fc-page-header">
    <div>
        <h1 class="fc-page-title">Gestión de Áreas</h1>
        <div class="fc-topbar-sub">Vista de administrador — todas las empresas del corporativo</div>
    </div>
    <a href="{{ route('empresas.create') }}" class="fc-btn fc-btn-primary">
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
        Nueva empresa
    </a>
</div>

{{-- Panel de estadísticas globales --}}
<div class="admin-areas-stats">
    <div class="admin-stat-card">
        <div class="admin-stat-icon" style="background:#6366f115">
            <svg viewBox="0 0 24 24" fill="#6366f1"><path d="M12 7V3H2v18h20V7H12zM6 19H4v-2h2v2zm0-4H4v-2h2v2zm0-4H4V9h2v2zm0-4H4V5h2v2zm4 12H8v-2h2v2zm0-4H8v-2h2v2zm0-4H8V9h2v2zm0-4H8V5h2v2zm10 12h-8v-2h2v-2h-2v-2h2v-2h-2V9h8v10zm-2-8h-2v2h2v-2zm0 4h-2v2h2v-2z"/></svg>
        </div>
        <div class="admin-stat-num">{{ $stats['total_empresas'] }}</div>
        <div class="admin-stat-lbl">Empresas</div>
        <div class="admin-stat-sub">{{ $stats['empresas_activas'] }} activas</div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-icon" style="background:#10b98115">
            <svg viewBox="0 0 24 24" fill="#10b981"><path d="M20 6h-8l-2-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2z"/></svg>
        </div>
        <div class="admin-stat-num">{{ $stats['total_carpetas'] }}</div>
        <div class="admin-stat-lbl">Carpetas totales</div>
        <div class="admin-stat-sub">en todas las empresas</div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-icon" style="background:#f59e0b15">
            <svg viewBox="0 0 24 24" fill="#f59e0b"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
        </div>
        <div class="admin-stat-num">{{ $stats['total_usuarios'] }}</div>
        <div class="admin-stat-lbl">Usuarios totales</div>
        <div class="admin-stat-sub">en todas las empresas</div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-icon" style="background:#3b82f615">
            <svg viewBox="0 0 24 24" fill="#3b82f6"><path d="M17 12h-5v5h5v-5zM16 1v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2h-1V1h-2zm3 18H5V8h14v11z"/></svg>
        </div>
        <div class="admin-stat-num">{{ $empresas->where('activo',false)->count() }}</div>
        <div class="admin-stat-lbl">Inactivas</div>
        <div class="admin-stat-sub">requieren atención</div>
    </div>
</div>

{{-- Barra de filtros --}}
<div class="filter-bar" style="margin-top:24px">
    <div class="search-wrap">
        <div class="search-icon">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
            </svg>
        </div>
        <input class="search-input" type="text" id="searchAreas"
               placeholder="Buscar empresa…" oninput="filtrarAreas(this.value)">
    </div>
    <div class="filter-tabs">
        <button class="filter-tab active" onclick="setFiltro(this,'todas')">Todas</button>
        <button class="filter-tab" onclick="setFiltro(this,'corporativo')">Corporativo</button>
        <button class="filter-tab" onclick="setFiltro(this,'activas')">Activas</button>
        <button class="filter-tab" onclick="setFiltro(this,'inactivas')">Inactivas</button>
    </div>
</div>

{{-- Grid de empresas (modo administrador) --}}
<div class="admin-areas-grid" id="areasGrid">
    @foreach($empresas as $emp)
    @php
        $bg     = $emp->color_primario   ?? '#1B3A6B';
        $accent = $emp->color_secundario ?? '#2E5FA3';
        $logo   = asset('images/empresas/'.$emp->logo);
        $tipo   = $emp->es_corporativo ? 'corporativo' : 'empresa';
        $estado = $emp->activo ? 'activas' : 'inactivas';
    @endphp

    <div class="admin-area-card {{ !$emp->activo ? 'admin-area-card--inactiva' : '' }}"
         data-tipo="{{ $tipo }}"
         data-estado="{{ $estado }}"
         data-nombre="{{ strtolower($emp->nombre) }}">

        {{-- Banda + logo --}}
        <div class="admin-area-header" style="background:{{ $bg }}">
            <div style="display:flex;align-items:center;gap:12px">
                <img src="{{ $logo }}"
                     alt="{{ $emp->siglas }}"
                     class="admin-area-logo"
                     onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                <div class="admin-area-logo-fallback" style="background:{{ $accent }}88">
                    {{ strtoupper(substr($emp->siglas,0,2)) }}
                </div>
                <div>
                    <div class="admin-area-nombre">{{ $emp->nombre }}</div>
                    <div class="admin-area-siglas">{{ $emp->siglas }}
                        @if($emp->es_corporativo)
                            <span style="opacity:.7">· Corporativo</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Estado badge --}}
            <div style="display:flex;flex-direction:column;align-items:flex-end;gap:6px">
                @if($emp->activo)
                    <span class="admin-badge-activa">Activa</span>
                @else
                    <span class="admin-badge-inactiva">Inactiva</span>
                @endif

                {{-- Paleta mini --}}
                <div style="display:flex;gap:4px">
                    @if($emp->color_primario)
                        <div style="width:12px;height:12px;border-radius:50%;background:{{ $emp->color_primario }};border:1px solid rgba(255,255,255,0.3)"
                             title="{{ $emp->color_primario }}"></div>
                    @endif
                    @if($emp->color_secundario)
                        <div style="width:12px;height:12px;border-radius:50%;background:{{ $emp->color_secundario }};border:1px solid rgba(255,255,255,0.3)"
                             title="{{ $emp->color_secundario }}"></div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Body con stats y acciones --}}
        <div class="admin-area-body">

            {{-- Stats fila --}}
            <div class="admin-area-stats-row">
                <div class="admin-area-stat">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M20 6h-8l-2-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2z"/></svg>
                    <span>{{ $emp->carpetas_count }}</span>
                    <span class="admin-area-stat-lbl">carpetas</span>
                </div>
                <div class="admin-area-stat">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
                    <span>{{ $emp->usuarios_count }}</span>
                    <span class="admin-area-stat-lbl">usuarios</span>
                </div>
            </div>

            {{-- Carpetas preview --}}
            @if($emp->carpetas->count())
            <div class="admin-area-carpetas">
                @foreach($emp->carpetas->take(3) as $carpeta)
                <div class="admin-carpeta-row">
                    <div class="admin-carpeta-dot" style="background:{{ $accent }}"></div>
                    <span class="admin-carpeta-nombre">{{ $carpeta->nombre }}</span>
                    <span class="admin-carpeta-count" style="color:{{ $accent }}">
                        {{ $carpeta->archivos_count ?? 0 }}
                    </span>
                </div>
                @endforeach
                @if($emp->carpetas_count > 3)
                    <div style="font-size:11px;color:#94a3b8;padding-left:14px;margin-top:2px">
                        +{{ $emp->carpetas_count - 3 }} más
                    </div>
                @endif
            </div>
            @else
                <div style="font-size:12px;color:#94a3b8;padding:8px 0">Sin carpetas aún.</div>
            @endif

            {{-- Acciones de administrador --}}
            <div class="admin-area-actions">
                <a href="{{ route('areas.show', $emp) }}"
                   class="fc-btn fc-btn-outline fc-btn-sm" title="Ver área">
                    <svg viewBox="0 0 24 24" fill="currentColor" style="width:13px;height:13px">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                        <circle cx="12" cy="12" r="3" fill="none" stroke="currentColor" stroke-width="2"/>
                    </svg>
                    Ver área
                </a>
                <a href="{{ route('empresas.edit', $emp) }}"
                   class="fc-btn fc-btn-outline fc-btn-sm" title="Editar empresa">
                    <svg viewBox="0 0 24 24" fill="currentColor" style="width:13px;height:13px">
                        <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04a1 1 0 000-1.41l-2.34-2.34a1 1 0 00-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                    </svg>
                    Editar
                </a>

                @if(!$emp->es_corporativo)
                <form method="POST" action="{{ route('empresas.toggle-activo', $emp) }}"
                      style="display:inline">
                    @csrf
                    <button type="submit"
                            class="fc-btn fc-btn-sm {{ $emp->activo ? 'fc-btn-warning' : 'fc-btn-success' }}">
                        {{ $emp->activo ? 'Desactivar' : 'Activar' }}
                    </button>
                </form>
                @endif
            </div>

        </div>{{-- /admin-area-body --}}
    </div>
    @endforeach
</div>

<script>
function filtrarAreas(q) {
    const term = q.toLowerCase();
    document.querySelectorAll('.admin-area-card').forEach(c => {
        c.style.display = (c.dataset.nombre || '').includes(term) ? '' : 'none';
    });
}
function setFiltro(btn, tipo) {
    document.querySelectorAll('.filter-tab').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.querySelectorAll('.admin-area-card').forEach(c => {
        if (tipo === 'todas') { c.style.display = ''; return; }
        if (tipo === 'activas'   && c.dataset.estado === 'activas')   { c.style.display = ''; return; }
        if (tipo === 'inactivas' && c.dataset.estado === 'inactivas') { c.style.display = ''; return; }
        if (tipo === 'corporativo' && c.dataset.tipo === 'corporativo') { c.style.display = ''; return; }
        c.style.display = 'none';
    });
}
</script>
</x-app-layout>
