<x-app-layout>
@section('title', 'Mis Áreas')

@section('content')
@php
    $userEmpresa = Auth::user()->empresa;
    $accentColor = $userEmpresa->color_secundario ?? '#4f46e5';
@endphp

<div class="fc-page-header">
    <div>
        <h1 class="fc-page-title">Mis Áreas</h1>
        <div class="fc-topbar-sub">{{ $empresas->count() }} {{ $empresas->count() === 1 ? 'área accesible' : 'áreas accesibles' }}</div>
    </div>
</div>

{{-- Barra de filtros --}}
<div class="filter-bar">
    <div class="search-wrap">
        <div class="search-icon">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
            </svg>
        </div>
        <input class="search-input" type="text" id="searchAreas" placeholder="Buscar áreas…" oninput="filtrarAreas(this.value)">
    </div>
    <div class="filter-tabs">
        <button class="filter-tab active" onclick="setFiltro(this,'todas')">Todas</button>
        <button class="filter-tab" onclick="setFiltro(this,'corporativo')">Corporativo</button>
        <button class="filter-tab" onclick="setFiltro(this,'empresa')">Mi empresa</button>
    </div>
</div>

{{-- Grid de áreas --}}
<div class="areas-grid" id="areasGrid">

    @forelse($empresas as $emp)
    @php
        $bg     = $emp->color_primario   ?? '#1B3A6B';
        $accent = $emp->color_secundario ?? '#2E5FA3';
        $logoUrl = asset('images/empresas/'.$emp->logo);
        $esCorp = $emp->es_corporativo;
        $tipo   = $esCorp ? 'corporativo' : 'empresa';
    @endphp

    <a href="{{ route('areas.show', $emp) }}"
       class="area-card"
       data-tipo="{{ $tipo }}"
       data-nombre="{{ strtolower($emp->nombre) }}">

        {{-- Banda de color superior --}}
        <div class="area-card-stripe"
             style="background:linear-gradient(90deg,{{ $bg }},{{ $accent }})"></div>

        <div class="area-card-body">

            {{-- Header: logo + nombre + badge --}}
            <div class="area-card-header">
                <div style="display:flex;align-items:center;gap:14px">
                    <div class="area-icon-wrap" style="background:{{ $accent }}18">
                        <img src="{{ $logoUrl }}"
                             alt="{{ $emp->siglas }}"
                             style="width:28px;height:28px;object-fit:contain"
                             onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                        <span style="display:none;font-size:13px;font-weight:800;color:{{ $accent }}">
                            {{ strtoupper(substr($emp->siglas,0,2)) }}
                        </span>
                    </div>
                    <div>
                        <div class="area-name">{{ $emp->nombre }}</div>
                        <span class="area-badge"
                              style="background:{{ $accent }}15;color:{{ $accent }}">
                            <span style="width:5px;height:5px;border-radius:50%;background:{{ $accent }};display:inline-block"></span>
                            {{ $esCorp ? 'Corporativo' : 'Mi empresa' }}
                        </span>
                    </div>
                </div>
                <div class="area-card-chevron" style="--accent:{{ $accent }}">›</div>
            </div>

            {{-- Descripción --}}
            <p class="area-desc">
                @if($esCorp)
                    Área corporativa del Grupo Cardumen. Documentación central compartida con todas las empresas.
                @else
                    Documentación de <strong>{{ $emp->siglas }}</strong> del corporativo Cardumen.
                @endif
            </p>

            {{-- Stats --}}
            <div class="area-stats">
                <div class="area-stat-box" style="background:{{ $accent }}0d">
                    <div class="area-stat-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="{{ $accent }}" stroke-width="2">
                            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                        </svg>
                    </div>
                    <div class="area-stat-num">{{ $emp->carpetas_count }}</div>
                    <div class="area-stat-lbl">Carpetas</div>
                </div>
                <div class="area-stat-box" style="background:#0ea5e908">
                    <div class="area-stat-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0ea5e9" stroke-width="2">
                            <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                        </svg>
                    </div>
                    <div class="area-stat-num">{{ $emp->usuarios_count }}</div>
                    <div class="area-stat-lbl">Miembros</div>
                </div>
                <div class="area-stat-box" style="background:#f59e0b08">
                    <div class="area-stat-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                        </svg>
                    </div>
                    <div class="area-stat-num">{{ $emp->updated_at ? $emp->updated_at->diffForHumans(null, true) : '—' }}</div>
                    <div class="area-stat-lbl">Actualizado</div>
                </div>
            </div>

            {{-- Carpetas preview --}}
            @if($emp->carpetas->count())
            <div class="area-folders">
                @foreach($emp->carpetas->take(3) as $carpeta)
                <div class="area-folder-row">
                    <div class="area-folder-name">
                        <div class="area-folder-dot" style="background:{{ $accent }}"></div>
                        {{ $carpeta->nombre }}
                    </div>
                    <span class="area-folder-count" style="color:{{ $accent }}">
                        {{ $carpeta->archivos_count ?? 0 }}
                    </span>
                </div>
                @endforeach
                @if($emp->carpetas_count > 3)
                    <div class="area-folders-more">+{{ $emp->carpetas_count - 3 }} carpetas más</div>
                @endif
            </div>
            @endif

        </div>
    </a>
    @empty
    <div style="grid-column:1/-1;text-align:center;padding:60px 20px;color:var(--fc-text-muted)">
        No hay áreas accesibles.
    </div>
    @endforelse

</div>

<script>
function filtrarAreas(q) {
    const term = q.toLowerCase();
    document.querySelectorAll('.area-card').forEach(card => {
        const nombre = card.dataset.nombre || '';
        card.style.display = nombre.includes(term) ? '' : 'none';
    });
}

function setFiltro(btn, tipo) {
    document.querySelectorAll('.filter-tab').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.querySelectorAll('.area-card').forEach(card => {
        if (tipo === 'todas') {
            card.style.display = '';
        } else {
            card.style.display = card.dataset.tipo === tipo ? '' : 'none';
        }
    });
}
</script>

</x-app-layout>
