<x-app-layout>
@section('title', $empresa->nombre)

@section('content')
@php
    $bg     = $empresa->color_primario   ?? '#1B3A6B';
    $accent = $empresa->color_secundario ?? '#2E5FA3';
    $logoUrl = asset('images/empresas/'.$empresa->logo);
    $totalCarpetas = $carpetas->count();
@endphp

{{-- CSS dinámico por empresa: solo los 8 selectores que cambian por color --}}
<style>
    .view-btn.active                   { color: {{ $accent }}; }
    .btn-nuevo                         { background: linear-gradient(135deg, {{ $bg }}, {{ $accent }}); box-shadow: 0 4px 14px {{ $accent }}55; }
    .btn-nuevo:hover                   { box-shadow: 0 6px 20px {{ $accent }}66; }
    .breadcrumb a:hover,
    .breadcrumb-current                { color: {{ $accent }}; }
    .carpeta-item.active               { background: {{ $accent }}0f; border-left-color: {{ $accent }}; }
    .carpeta-item.active .carpeta-item-icon,
    .carpeta-item.active .carpeta-item-name { color: {{ $accent }}; }
    .carpeta-item-count                { color: {{ $accent }}; background: {{ $accent }}18; }
    .area-banner                       { background: linear-gradient(135deg, {{ $bg }} 0%, {{ $bg }}dd 50%, {{ $accent }} 100%); }
    .carpeta-card:hover                { box-shadow: 0 8px 24px {{ $accent }}22; border-color: {{ $accent }}44; }
    .carpeta-card-icon                 { background: {{ $accent }}12; }
    .carpeta-card-icon svg             { stroke: {{ $accent }}; }
    .carpeta-action-btn.edit           { color: {{ $accent }}; }
    .carpeta-action-btn.edit:hover     { background: {{ $accent }}18; }
    .fc-topbar-avatar-area             { background: linear-gradient(135deg, {{ $bg }}, {{ $accent }}); }
    .fc-topbar-role-area               { color: {{ $accent }}; }
    .topbar-accent-line                { background: linear-gradient(90deg, {{ $accent }}, {{ $bg }}); }
    .area-logo-box                     { box-shadow: 0 2px 8px {{ $accent }}22; }
</style>

<div class="area-layout">

    {{-- ── Sidebar de carpetas ────────────────────────── --}}
    <div class="carpetas-sidebar">
        <div class="carpetas-sidebar-header">
            <div class="breadcrumb">
                <a href="{{ route('areas.index') }}">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                    </svg>
                    Áreas
                </a>
                <span class="breadcrumb-sep">›</span>
                <span class="breadcrumb-current">{{ $empresa->siglas }}</span>
            </div>
        </div>

        <div class="carpetas-label">Carpetas</div>

        @forelse($carpetas as $i => $carpeta)
        <div class="carpeta-item {{ $i === 0 ? 'active' : '' }}"
             onclick="selectCarpeta(this)"
             data-carpeta="{{ $carpeta->id }}">
            <div class="carpeta-item-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"/>
                </svg>
            </div>
            <span class="carpeta-item-name">{{ $carpeta->nombre }}</span>
            <span class="carpeta-item-count">{{ $carpeta->archivos_count }}</span>
        </div>
        @empty
        <div style="padding:20px;font-size:13px;color:#94a3b8;text-align:center">
            Sin carpetas aún.
        </div>
        @endforelse
    </div>

    {{-- ── Contenido principal ─────────────────────────── --}}
    <div class="area-content">

        {{-- Topbar del área --}}
        <header class="area-topbar">
            {{-- Línea de acento --}}
            <div class="topbar-accent-line" style="position:absolute;bottom:0;left:0;right:0;height:2px;opacity:.5"></div>

            <div class="fc-topbar-left">
                <div class="fc-topbar-title" style="display:flex;align-items:center;gap:10px">
                    <div class="area-logo-box">
                        <img src="{{ $logoUrl }}"
                             alt="{{ $empresa->siglas }}"
                             onerror="this.src='{{ asset('images/logo.png') }}'">
                    </div>
                    {{ $empresa->nombre }}
                </div>
                <div class="fc-topbar-sub">
                    {{ $totalCarpetas }} {{ $totalCarpetas === 1 ? 'carpeta' : 'carpetas' }}
                    · {{ $totalArchivos }} archivos
                </div>
            </div>

            <div class="fc-topbar-right">
                {{-- Toggle grid/lista --}}
                <div class="view-btns">
                    <button class="view-btn active" id="btnGrid" onclick="setView('grid')">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M3 3h7v7H3V3zm0 11h7v7H3v-7zm11-11h7v7h-7V3zm0 11h7v7h-7v-7z"/>
                        </svg>
                    </button>
                    <button class="view-btn" id="btnList" onclick="setView('list')">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M3 4h2v2H3V4zm4 0h14v2H7V4zM3 9h2v2H3V9zm4 0h14v2H7V9zm-4 5h2v2H3v-2zm4 0h14v2H7v-2zm-4 5h2v2H3v-2zm4 0h14v2H7v-2z"/>
                        </svg>
                    </button>
                </div>

                @can('create', App\Models\Carpeta::class)
                <a href="{{ route('carpetas.create', ['empresa' => $empresa->id]) }}" class="btn-nuevo">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                    </svg>
                    Nueva carpeta
                </a>
                @endcan

                {{-- Avatar usuario con colores de empresa --}}
                <div class="fc-topbar-avatar-area">
                    {{ strtoupper(substr(Auth::user()->nombre,0,1)) }}{{ strtoupper(substr(Auth::user()->paterno,0,1)) }}
                </div>
                <div>
                    <div class="fc-topbar-name">{{ Auth::user()->nombre_completo }}</div>
                    <div class="fc-topbar-role-area">{{ Auth::user()->rol }}</div>
                </div>
            </div>
        </header>

        {{-- Cuerpo scrolleable --}}
        <div class="area-body-scroll">

            {{-- Banner hero --}}
            <div class="area-banner">
                <p class="area-banner-desc">
                    @if($empresa->es_corporativo)
                        Área corporativa del Grupo Cardumen. Documentación central compartida con todas las empresas.
                    @else
                        Documentación de <strong>{{ $empresa->nombre }}</strong>.
                        Empresa del corporativo Cardumen.
                    @endif
                </p>
                <div class="area-banner-stats">
                    <div>
                        <div class="area-banner-stat-num">{{ $totalCarpetas }}</div>
                        <div class="area-banner-stat-lbl">Carpetas</div>
                    </div>
                    <div>
                        <div class="area-banner-stat-num">{{ $totalArchivos }}</div>
                        <div class="area-banner-stat-lbl">Archivos</div>
                    </div>
                    <div>
                        <div class="area-banner-stat-num">{{ $empresa->usuarios_count ?? $empresa->usuarios()->count() }}</div>
                        <div class="area-banner-stat-lbl">Miembros</div>
                    </div>
                </div>
            </div>

            {{-- Grid de carpetas --}}
            <div class="section-title">
                Selecciona una carpeta
                <div class="section-title-line"></div>
            </div>

            <div class="carpetas-grid" id="carpetasGrid">
                @forelse($carpetas as $carpeta)
                <div class="carpeta-card" onclick="window.location='{{ route('carpetas.show', $carpeta) }}'">
                    <div class="carpeta-card-icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"/>
                        </svg>
                    </div>
                    <div class="carpeta-card-name">{{ $carpeta->nombre }}</div>
                    <div class="carpeta-card-meta">
                        <span class="carpeta-card-archivos">{{ $carpeta->archivos_count }} archivos</span>
                        <span class="carpeta-card-badge {{ $carpeta->es_publico ? 'badge-abierta' : 'badge-restringida' }}">
                            {{ $carpeta->es_publico ? 'Abierta' : 'Restringida' }}
                        </span>
                    </div>
                    <div class="carpeta-card-actions">
                        <a href="{{ route('carpetas.show', $carpeta) }}"
                           class="carpeta-action-btn view"
                           title="Ver carpeta"
                           onclick="event.stopPropagation()">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </a>
                        @can('update', $carpeta)
                        <a href="{{ route('carpetas.edit', $carpeta) }}"
                           class="carpeta-action-btn edit"
                           title="Editar"
                           onclick="event.stopPropagation()">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                                <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                            </svg>
                        </a>
                        @endcan
                    </div>
                </div>
                @empty
                <div style="grid-column:1/-1;text-align:center;padding:48px 20px;color:#94a3b8">
                    Esta área no tiene carpetas aún.
                    @can('create', App\Models\Carpeta::class)
                        <br><a href="{{ route('carpetas.create', ['empresa' => $empresa->id]) }}"
                               style="color:{{ $accent }};font-weight:600">Crear la primera carpeta →</a>
                    @endcan
                </div>
                @endforelse
            </div>

        </div>{{-- /area-body-scroll --}}
    </div>{{-- /area-content --}}
</div>{{-- /area-layout --}}

<script>
function setView(type) {
    const grid    = document.getElementById('carpetasGrid');
    const btnGrid = document.getElementById('btnGrid');
    const btnList = document.getElementById('btnList');
    if (type === 'grid') {
        grid.classList.remove('list-view');
        grid.querySelectorAll('.carpeta-card').forEach(c => c.classList.remove('list-view'));
        btnGrid.classList.add('active');
        btnList.classList.remove('active');
    } else {
        grid.classList.add('list-view');
        grid.querySelectorAll('.carpeta-card').forEach(c => c.classList.add('list-view'));
        btnList.classList.add('active');
        btnGrid.classList.remove('active');
    }
}

function selectCarpeta(el) {
    document.querySelectorAll('.carpeta-item').forEach(i => i.classList.remove('active'));
    el.classList.add('active');
    // Scroll suave a la card correspondiente
    const id = el.dataset.carpeta;
    const card = document.querySelector(`.carpeta-card[data-id="${id}"]`);
    if (card) card.scrollIntoView({ behavior: 'smooth', block: 'center' });
}
</script>
</x-app-layout>
