<x-app-layout>
<style>
/* ── Cards 2.0 ─────────────────────────────────────────── */
.fc-folders-grid2 {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 14px;
    margin-bottom: 8px;
}
.fc-folders-list2 {
    display: flex;
    flex-direction: column;
    gap: 6px;
    margin-bottom: 8px;
}

/* CARD */
.fc-folder-card2 {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    overflow: hidden;
    text-decoration: none;
    display: block;
    transition: transform .18s, box-shadow .18s, border-color .18s;
    box-shadow: 0 1px 4px rgba(0,0,0,.04);
    position: relative;
}
.fc-folder-card2:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 28px rgba(0,0,0,.1);
    border-color: var(--fc-empresa, #6366f1);
}
.fc-card2-stripe {
    height: 4px;
    background: var(--fc-empresa, #6366f1);
    transition: height .18s;
}
.fc-folder-card2:hover .fc-card2-stripe { height: 5px; }
.fc-card2-body { padding: 14px 14px 12px; }
.fc-card2-icon {
    width: 42px; height: 42px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 10px;
    transition: transform .18s;
}
.fc-folder-card2:hover .fc-card2-icon { transform: scale(1.08); }
.fc-card2-name {
    font-size: 13px; font-weight: 700; color: #1e293b;
    margin-bottom: 8px;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.fc-card2-counts {
    display: flex; gap: 5px; flex-wrap: wrap; margin-bottom: 6px;
}
.fc-card2-pill {
    display: inline-flex; align-items: center; gap: 3px;
    font-size: 10px; font-weight: 600;
    padding: 2px 7px; border-radius: 20px;
}
.fc-card2-badges { display: flex; gap: 4px; flex-wrap: wrap; }
.fc-card2-status {
    font-size: 9px; font-weight: 700; text-transform: uppercase;
    letter-spacing: .04em; padding: 2px 6px; border-radius: 99px;
}
.fc-card2-status.green  { background: rgba(5,150,105,.1);  color: #059669; }
.fc-card2-status.amber  { background: rgba(217,119,6,.1);  color: #d97706; }
.fc-card2-status.red    { background: rgba(220,38,38,.1);  color: #dc2626; }

/* ROW */
.fc-folder-row2 {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 11px 14px;
    display: flex; align-items: center; gap: 10px;
    text-decoration: none;
    transition: border-color .15s, box-shadow .15s, background .15s;
}
.fc-folder-row2:hover {
    border-color: var(--fc-empresa, #6366f1);
    box-shadow: 0 4px 14px rgba(0,0,0,.07);
    background: #fafbff;
}
.fc-row2-icon {
    width: 36px; height: 36px; border-radius: 9px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
}
.fc-row2-name {
    font-size: 13px; font-weight: 600; color: #1e293b; flex: 1;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    min-width: 0;
}
.fc-row2-meta {
    font-size: 11px; color: #94a3b8; white-space: nowrap; flex-shrink: 0;
}
.fc-row2-chevron { color: #cbd5e1; font-size: 20px; flex-shrink: 0; }

/* GRUPO EMPRESA */
.fc-grupo2 {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 1px 6px rgba(0,0,0,.04);
}
.fc-grupo2-header {
    display: flex; align-items: center; gap: 10px;
    padding: 13px 18px;
    cursor: pointer;
    user-select: none;
    transition: background .15s;
    border-bottom: 1px solid #f1f5f9;
}
.fc-grupo2-header:hover { background: #f8fafc; }
.fc-grupo2-logo {
    width: 32px; height: 32px; border-radius: 8px;
    object-fit: contain; flex-shrink: 0;
    background: rgba(255,255,255,.7);
    padding: 3px;
}
.fc-grupo2-name {
    font-size: 13px; font-weight: 700; color: #1e293b; flex: 1;
}
.fc-grupo2-count {
    font-size: 11px; font-weight: 700;
    padding: 2px 9px; border-radius: 99px;
}
.fc-grupo2-chevron {
    transition: transform .2s; color: #94a3b8; font-size: 18px;
}
.fc-grupo2.collapsed .fc-grupo2-chevron { transform: rotate(-90deg); }
.fc-grupo2-body { padding: 14px 14px 6px; }
.fc-grupo2.collapsed .fc-grupo2-body { display: none; }

/* SORT BAR */
.fc-sort-bar {
    display: flex; align-items: center; gap: 6px;
    margin-left: auto;
}
.fc-sort-btn {
    font-size: 11px; font-weight: 600; color: #64748b;
    padding: 4px 10px; border: 1px solid #e2e8f0;
    border-radius: 8px; background: #fff; cursor: pointer;
    transition: all .15s;
}
.fc-sort-btn.active, .fc-sort-btn:hover {
    background: #f1f0ff; color: #6366f1; border-color: #c7d2fe;
}

/* DARK */
.dark .fc-folder-card2 { background: #13111f; border-color: #1e1b4b; }
.dark .fc-card2-name   { color: #e0e7ff; }
.dark .fc-folder-row2  { background: #13111f; border-color: #1e1b4b; }
.dark .fc-row2-name    { color: #e0e7ff; }
.dark .fc-grupo2       { background: #13111f; border-color: #1e1b4b; }
.dark .fc-grupo2-name  { color: #e0e7ff; }
.dark .fc-grupo2-header:hover { background: #1a1830; }
</style>

<div class="fc-wrapper">
    @include('components.sidebar')

    <div class="fc-main">

        {{-- Topbar --}}
        <header class="fc-topbar">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#6366f1">
                <path d="M20 6h-8l-2-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2z"/>
            </svg>
            <span class="fc-topbar-title">Mis Carpetas</span>
            <div class="fc-topbar-right">
                <div class="fc-topbar-avatar">
                    {{ strtoupper(substr(Auth::user()->nombre,0,1)) }}{{ strtoupper(substr(Auth::user()->paterno,0,1)) }}
                </div>
                <div>
                    <div class="fc-topbar-name">{{ Auth::user()->nombre_completo }}</div>
                    <div class="fc-topbar-role">{{ Auth::user()->rol }}</div>
                </div>
            </div>
        </header>

        {{-- Barra de acciones --}}
        <div class="fc-actionbar">
            <div class="fc-search-wrap">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round">
                    <circle cx="11" cy="11" r="6"/><path d="m21 21-4.35-4.35"/>
                </svg>
                <input type="text" id="buscador" placeholder="Buscar carpetas..." oninput="filtrar(this.value)">
            </div>

            {{-- Sort --}}
            <div class="fc-sort-bar">
                <button class="fc-sort-btn active" onclick="ordenar('nombre', this)">A–Z</button>
                <button class="fc-sort-btn" onclick="ordenar('archivos', this)">Más archivos</button>
            </div>

            @can('create', App\Models\Carpeta::class)
            <a href="{{ route('carpetas.create') }}" class="fc-btn fc-btn-primary">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
                Nueva carpeta
            </a>
            @endcan

            <div class="fc-view-btns" style="margin-left:0">
                <button class="fc-view-btn active" id="btnGrid" onclick="setView('grid')" title="Cuadrícula">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M3 3h8v8H3zm10 0h8v8h-8zM3 13h8v8H3zm10 0h8v8h-8z"/></svg>
                </button>
                <button class="fc-view-btn" id="btnList" onclick="setView('list')" title="Lista">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M3 13h2v-2H3v2zm0 4h2v-2H3v2zm0-8h2V7H3v2zm4 4h14v-2H7v2zm0 4h14v-2H7v2zM7 7v2h14V7H7z"/></svg>
                </button>
            </div>
        </div>

        <div class="fc-content">

            @if(session('success'))
                <div class="fc-flash success">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="#059669"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                    {{ session('success') }}
                </div>
            @endif

            @if($carpetas->isEmpty())
                <div class="fc-empty">
                    <div class="fc-empty-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="#a5b4fc">
                            <path d="M20 6h-8l-2-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2z"/>
                        </svg>
                    </div>
                    <div class="fc-empty-title">No hay carpetas aún</div>
                    <div class="fc-empty-sub">Crea la primera carpeta para empezar a organizar documentos.</div>
                    @can('create', App\Models\Carpeta::class)
                        <a href="{{ route('carpetas.create') }}" class="fc-btn fc-btn-primary">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
                            Crear primera carpeta
                        </a>
                    @endcan
                </div>

            @elseif(in_array(Auth::user()->rol, ['Superadmin', 'Aux_QHSE']))

                {{-- ══ SUPERADMIN: bloques colapsables por empresa ══ --}}
                @php $porEmpresa = $carpetas->groupBy('empresa_id'); @endphp

                <div id="contenedor" style="display:flex;flex-direction:column;gap:16px">
                    @foreach($porEmpresa as $empId => $grupo)
                    @php
                        $empresa  = $grupo->first()->empresa;
                        $color    = $empresa?->color_primario ?? '#4f46e5';
                        $logoPath = $empresa?->logo
                            ? asset('images/empresas/' . $empresa->logo)
                            : asset('images/logo.png');
                    @endphp

                    <div class="fc-grupo2" id="grupo_{{ $empId }}" data-empresa="{{ strtolower($empresa?->nombre ?? '') }}">
                        {{-- Cabecera colapsable --}}
                        <div class="fc-grupo2-header" onclick="toggleGrupo('{{ $empId }}')">
                            <img src="{{ $logoPath }}"
                                 class="fc-grupo2-logo"
                                 style="border: 1px solid {{ $color }}33; background: {{ $color }}10"
                                 onerror="this.style.display='none'">

                            @if($empresa?->es_corporativo)
                                <span style="display:inline-flex;align-items:center;gap:5px">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="#1b3a6b"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/></svg>
                                    <span class="fc-grupo2-name">{{ $empresa->nombre }}</span>
                                    <span style="font-size:9px;background:rgba(27,58,107,.12);color:#1b3a6b;padding:1px 6px;border-radius:10px;font-weight:700">CORP</span>
                                </span>
                            @else
                                <span class="fc-grupo2-name">{{ $empresa?->nombre ?? 'Sin empresa' }}</span>
                            @endif

                            <span class="fc-grupo2-count" style="background:{{ $color }}15;color:{{ $color }}">
                                {{ $grupo->count() }} carpeta{{ $grupo->count() != 1 ? 's' : '' }}
                            </span>

                            @can('create', App\Models\Carpeta::class)
                            <a href="{{ route('carpetas.create', ['empresa_id' => $empId]) }}"
                               onclick="event.stopPropagation()"
                               style="font-size:11px;font-weight:600;color:{{ $color }};text-decoration:none;
                                      padding:4px 10px;border:1px solid {{ $color }}30;border-radius:8px;
                                      background:{{ $color }}08;white-space:nowrap">
                                + Nueva
                            </a>
                            @endcan

                            <span class="fc-grupo2-chevron">›</span>
                        </div>

                        {{-- Contenido --}}
                        <div class="fc-grupo2-body">
                            <div class="fc-folders-grid2 carpetas-grid" id="grid_{{ $empId }}">
                                @foreach($grupo as $carpeta)
                                    @include('carpetas._card', ['carpeta' => $carpeta])
                                @endforeach
                            </div>
                            <div class="fc-folders-list2 carpetas-list" id="list_{{ $empId }}" style="display:none">
                                @foreach($grupo as $carpeta)
                                    @include('carpetas._row', ['carpeta' => $carpeta])
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

            @else

                {{-- ══ USUARIO NORMAL: corporativo + empresa ══ --}}
                @php
                    $usuarioActual   = Auth::user();
                    $carpetasCorp    = $carpetas->filter(fn($c) => $c->empresa?->es_corporativo);
                    $carpetasEmpresa = $carpetas->filter(fn($c) => !$c->empresa?->es_corporativo);
                    $colorEmp        = $usuarioActual->empresa?->color_primario ?? '#4f46e5';
                @endphp

                <div id="contenedor" style="display:flex;flex-direction:column;gap:20px">

                    {{-- Sección corporativa --}}
                    @if($carpetasCorp->isNotEmpty())
                    <div class="fc-grupo2 fc-grupo-seccion" id="grupoCorp" data-empresa="corporativo">
                        <div class="fc-grupo2-header" onclick="toggleGrupo('Corp')">
                            <div style="width:32px;height:32px;border-radius:8px;background:rgba(27,58,107,.1);
                                        display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="#1b3a6b">
                                    <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/>
                                </svg>
                            </div>
                            <span class="fc-grupo2-name">Corporativo</span>
                            <span class="fc-grupo2-count" style="background:rgba(27,58,107,.1);color:#1b3a6b">
                                {{ $carpetasCorp->count() }} carpeta{{ $carpetasCorp->count() != 1 ? 's' : '' }}
                            </span>
                            <span style="font-size:11px;color:#94a3b8">Compartido con todo el Grupo Cardumen</span>
                            <span class="fc-grupo2-chevron">›</span>
                        </div>
                        <div class="fc-grupo2-body">
                            <div class="fc-folders-grid2 carpetas-grid" id="gridCorp">
                                @foreach($carpetasCorp as $carpeta)
                                    @include('carpetas._card', ['carpeta' => $carpeta])
                                @endforeach
                            </div>
                            <div class="fc-folders-list2 carpetas-list" id="listCorp" style="display:none">
                                @foreach($carpetasCorp as $carpeta)
                                    @include('carpetas._row', ['carpeta' => $carpeta])
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Sección empresa propia --}}
                    @if($carpetasEmpresa->isNotEmpty())
                    <div class="fc-grupo2 fc-grupo-seccion" id="grupoEmpresa" data-empresa="{{ strtolower($usuarioActual->empresa?->nombre ?? '') }}">
                        <div class="fc-grupo2-header" onclick="toggleGrupo('Empresa')">
                            <div style="width:32px;height:32px;border-radius:8px;background:{{ $colorEmp }}15;
                                        display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="{{ $colorEmp }}">
                                    <path d="M12 7V3H2v18h20V7H12z"/>
                                </svg>
                            </div>
                            <span class="fc-grupo2-name">{{ $usuarioActual->empresa?->siglas ?? 'Mi empresa' }}</span>
                            <span class="fc-grupo2-count" style="background:{{ $colorEmp }}15;color:{{ $colorEmp }}">
                                {{ $carpetasEmpresa->count() }} carpeta{{ $carpetasEmpresa->count() != 1 ? 's' : '' }}
                            </span>

                            @can('create', App\Models\Carpeta::class)
                            <a href="{{ route('carpetas.create') }}"
                               onclick="event.stopPropagation()"
                               style="font-size:11px;font-weight:600;color:{{ $colorEmp }};text-decoration:none;
                                      padding:4px 10px;border:1px solid {{ $colorEmp }}30;border-radius:8px;
                                      background:{{ $colorEmp }}08;white-space:nowrap;margin-left:auto">
                                + Nueva carpeta
                            </a>
                            @endcan

                            <span class="fc-grupo2-chevron">›</span>
                        </div>
                        <div class="fc-grupo2-body">
                            <div class="fc-folders-grid2 carpetas-grid" id="gridEmpresa">
                                @foreach($carpetasEmpresa as $carpeta)
                                    @include('carpetas._card', ['carpeta' => $carpeta])
                                @endforeach
                            </div>
                            <div class="fc-folders-list2 carpetas-list" id="listEmpresa" style="display:none">
                                @foreach($carpetasEmpresa as $carpeta)
                                    @include('carpetas._row', ['carpeta' => $carpeta])
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($carpetasCorp->isEmpty() && $carpetasEmpresa->isEmpty())
                    <div class="fc-empty">
                        <div class="fc-empty-icon">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="#a5b4fc">
                                <path d="M20 6h-8l-2-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2z"/>
                            </svg>
                        </div>
                        <div class="fc-empty-title">No hay carpetas aún</div>
                        <div class="fc-empty-sub">Crea la primera carpeta para organizar documentos.</div>
                    </div>
                    @endif

                </div>
            @endif

        </div>{{-- /fc-content --}}
    </div>{{-- /fc-main --}}
</div>{{-- /fc-wrapper --}}

<script>
// ── Vista grid / lista ──────────────────────────────────────
let vistaActual = 'grid';

function setView(v) {
    vistaActual = v;
    document.getElementById('btnGrid').classList.toggle('active', v === 'grid');
    document.getElementById('btnList').classList.toggle('active', v === 'list');
    document.querySelectorAll('.carpetas-grid').forEach(el => el.style.display = v === 'grid' ? 'grid' : 'none');
    document.querySelectorAll('.carpetas-list').forEach(el => el.style.display = v === 'list' ? 'flex' : 'none');
}

// ── Colapsar / expandir grupo ───────────────────────────────
function toggleGrupo(id) {
    const grupo = document.getElementById('grupo' + id);
    if (grupo) grupo.classList.toggle('collapsed');
}

// ── Filtrar por nombre ──────────────────────────────────────
function filtrar(q) {
    const t = q.toLowerCase().trim();

    // Carpetas (cards + rows)
    document.querySelectorAll('[data-nombre]').forEach(el => {
        const match = !t || el.dataset.nombre.includes(t);
        el.style.display = match ? '' : 'none';
    });

    // Grupos: ocultar si todas sus carpetas están ocultas
    document.querySelectorAll('.fc-grupo2').forEach(grupo => {
        const visibles = grupo.querySelectorAll('[data-nombre]:not([style*="display: none"])').length;
        const hayQuery = t.length > 0;

        // Auto-expandir grupos que tienen resultados al buscar
        if (hayQuery && visibles > 0) {
            grupo.classList.remove('collapsed');
        }
        grupo.style.display = (visibles === 0 && hayQuery) ? 'none' : '';
    });
}

// ── Ordenar carpetas ────────────────────────────────────────
function ordenar(por, btn) {
    document.querySelectorAll('.fc-sort-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    document.querySelectorAll('.carpetas-grid, .carpetas-list').forEach(container => {
        const items = Array.from(container.children);
        items.sort((a, b) => {
            if (por === 'nombre') {
                return (a.dataset.nombre || '').localeCompare(b.dataset.nombre || '', 'es');
            } else {
                // más archivos primero
                return parseInt(b.dataset.archivos || 0) - parseInt(a.dataset.archivos || 0);
            }
        });
        items.forEach(item => container.appendChild(item));
    });
}
</script>
</x-app-layout>
