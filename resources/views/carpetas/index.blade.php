<x-app-layout>
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
                <input type="text" placeholder="Buscar carpetas..." oninput="filtrarCarpetas(this.value)">
            </div>

            @can('create', App\Models\Carpeta::class)
            <a href="{{ route('carpetas.create') }}" class="fc-btn fc-btn-primary">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
                Nueva carpeta
            </a>
            @endcan

            <div class="fc-view-btns" style="margin-left:auto">
                <button class="fc-view-btn active" id="btnGrid" onclick="setView('grid')" title="Cuadrícula">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M3 3h8v8H3zm10 0h8v8h-8zM3 13h8v8H3zm10 0h8v8h-8z"/></svg>
                </button>
                <button class="fc-view-btn" id="btnList" onclick="setView('list')" title="Lista">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M3 13h2v-2H3v2zm0 4h2v-2H3v2zm0-8h2V7H3v2zm4 4h14v-2H7v2zm0 4h14v-2H7v2zM7 7v2h14V7H7z"/></svg>
                </button>
            </div>
        </div>

        {{-- Contenido --}}
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

            @else

                @if(in_array(Auth::user()->rol, ['Superadmin', 'Aux_QHSE']))
                    {{-- ══════════════════════════════════════════════════════
                         VISTA SUPERADMIN: agrupado por empresa
                         ══════════════════════════════════════════════════════ --}}
                    @php $porEmpresa = $carpetas->groupBy('empresa_id'); @endphp

                    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(340px,1fr));gap:24px;align-items:start">
                        @foreach($porEmpresa as $empresaId => $grupo)
                            @php $empresa = $grupo->first()->empresa; @endphp
                            <div class="fc-empresa-bloque">
                                <div class="fc-empresa-header" style="margin-top:0;margin-bottom:12px;display:flex;justify-content:space-between;align-items:center">
                                    @if($empresa && $empresa->es_corporativo)
                                        <span class="fc-empresa-badge corporativo">
                                            <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/></svg>
                                            {{ $empresa->nombre }}
                                            <span style="font-size:9px;background:rgba(27,58,107,0.15);padding:1px 6px;border-radius:10px;margin-left:4px">CORP</span>
                                        </span>
                                    @else
                                        <span class="fc-empresa-badge empresa"
                                              style="background:{{ $empresa->color_primario ?? '#4f46e5' }}18;color:{{ $empresa->color_primario ?? '#4f46e5' }};border-color:{{ $empresa->color_primario ?? '#4f46e5' }}33">
                                            <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor"><path d="M12 7V3H2v18h20V7H12z"/></svg>
                                            {{ $empresa->nombre ?? 'Empresa' }}
                                        </span>
                                    @endif
                                    @can('create', App\Models\Carpeta::class)
                                    <a href="{{ route('carpetas.create', ['empresa_id' => $empresaId]) }}"
                                       style="font-size:12px;color:#6366f1;text-decoration:none;display:flex;align-items:center;gap:5px">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
                                        Nueva carpeta
                                    </a>
                                    @endcan
                                </div>
                                <div class="fc-folders-grid carpetas-container" id="grid_{{ $empresaId }}"
                                     style="grid-template-columns:repeat(auto-fill,minmax(150px,1fr));margin-bottom:0">
                                    @foreach($grupo as $carpeta)
                                        @include('carpetas._card', ['carpeta' => $carpeta])
                                    @endforeach
                                </div>
                                <div class="fc-folders-list carpetas-container" id="list_{{ $empresaId }}" style="display:none;margin-bottom:0">
                                    @foreach($grupo as $carpeta)
                                        @include('carpetas._row', ['carpeta' => $carpeta])
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                @else
                    {{-- ══════════════════════════════════════════════════════
                         VISTA USUARIO NORMAL: separado en 2 secciones
                         • Corporativo  → carpetas del grupo Cardumen
                         • Mi empresa   → carpetas propias de la empresa
                         ══════════════════════════════════════════════════════ --}}
                    @php
                        $usuarioActual     = Auth::user();
                        $carpetasCorp      = $carpetas->filter(fn($c) => $c->empresa?->es_corporativo);
                        $carpetasEmpresa   = $carpetas->filter(fn($c) => !$c->empresa?->es_corporativo);
                        $colorPrimario     = $usuarioActual->empresa->color_primario   ?? '#4f46e5';
                        $colorSecundario   = $usuarioActual->empresa->color_secundario ?? '#7c3aed';
                    @endphp

                    {{-- ── SECCIÓN: Corporativo ──────────────────────────── --}}
                    @if($carpetasCorp->isNotEmpty())
                    <div class="fc-grupo-seccion" style="margin-bottom:32px">
                        {{-- Cabecera de sección corporativo --}}
                        <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px">
                            <div style="display:flex;align-items:center;gap:8px;padding:6px 14px;
                                        background:rgba(27,58,107,0.07);border:1px solid rgba(27,58,107,0.15);
                                        border-radius:20px">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="#1b3a6b">
                                    <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/>
                                </svg>
                                <span style="font-size:12px;font-weight:700;color:#1b3a6b;letter-spacing:.04em;text-transform:uppercase">
                                    Corporativo
                                </span>
                                <span style="font-size:10px;background:rgba(27,58,107,0.12);color:#1b3a6b;
                                             padding:1px 7px;border-radius:10px;font-weight:600">
                                    {{ $carpetasCorp->count() }} carpeta{{ $carpetasCorp->count() != 1 ? 's' : '' }}
                                </span>
                            </div>
                            <div style="flex:1;height:1px;background:#e2e8f0"></div>
                            <span style="font-size:11px;color:#94a3b8">Acceso compartido con todo el Grupo Cardumen</span>
                        </div>

                        {{-- Grid corporativo --}}
                        <div class="fc-folders-grid carpetas-container" id="gridCorp">
                            @foreach($carpetasCorp as $carpeta)
                                @include('carpetas._card', ['carpeta' => $carpeta])
                            @endforeach
                        </div>
                        <div class="fc-folders-list carpetas-container" id="listCorp" style="display:none">
                            @foreach($carpetasCorp as $carpeta)
                                @include('carpetas._row', ['carpeta' => $carpeta])
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- ── SECCIÓN: Mi empresa ──────────────────────────── --}}
                    @if($carpetasEmpresa->isNotEmpty())
                    <div class="fc-grupo-seccion">
                        {{-- Cabecera de sección empresa propia --}}
                        <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px">
                            <div style="display:flex;align-items:center;gap:8px;padding:6px 14px;
                                        background:{{ $colorPrimario }}10;border:1px solid {{ $colorPrimario }}25;
                                        border-radius:20px">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="{{ $colorPrimario }}">
                                    <path d="M12 7V3H2v18h20V7H12z"/>
                                </svg>
                                <span style="font-size:12px;font-weight:700;color:{{ $colorPrimario }};letter-spacing:.04em;text-transform:uppercase">
                                    {{ $usuarioActual->empresa->siglas ?? 'Mi empresa' }}
                                </span>
                                <span style="font-size:10px;background:{{ $colorPrimario }}15;color:{{ $colorPrimario }};
                                             padding:1px 7px;border-radius:10px;font-weight:600">
                                    {{ $carpetasEmpresa->count() }} carpeta{{ $carpetasEmpresa->count() != 1 ? 's' : '' }}
                                </span>
                            </div>
                            <div style="flex:1;height:1px;background:#e2e8f0"></div>
                            @can('create', App\Models\Carpeta::class)
                            <a href="{{ route('carpetas.create') }}"
                               style="font-size:12px;font-weight:600;color:{{ $colorPrimario }};text-decoration:none;
                                      display:flex;align-items:center;gap:5px">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
                                Nueva carpeta
                            </a>
                            @endcan
                        </div>

                        {{-- Grid empresa propia --}}
                        <div class="fc-folders-grid carpetas-container" id="gridEmpresa">
                            @foreach($carpetasEmpresa as $carpeta)
                                @include('carpetas._card', ['carpeta' => $carpeta])
                            @endforeach
                        </div>
                        <div class="fc-folders-list carpetas-container" id="listEmpresa" style="display:none">
                            @foreach($carpetasEmpresa as $carpeta)
                                @include('carpetas._row', ['carpeta' => $carpeta])
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Mensaje si no hay carpetas de empresa propia --}}
                    @if($carpetasEmpresa->isEmpty() && $carpetasCorp->isEmpty())
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

                @endif {{-- fin if Superadmin --}}

            @endif {{-- fin if carpetas --}}

        </div>{{-- /fc-content --}}
    </div>{{-- /fc-main --}}
</div>{{-- /fc-wrapper --}}

<script>
function setView(v) {
    document.getElementById('btnGrid').classList.toggle('active', v === 'grid');
    document.getElementById('btnList').classList.toggle('active', v === 'list');
    document.querySelectorAll('[id^="grid"]').forEach(el => el.style.display = v === 'grid' ? 'grid' : 'none');
    document.querySelectorAll('[id^="list"]').forEach(el => el.style.display = v === 'list' ? 'flex' : 'none');
}

function filtrarCarpetas(q) {
    const t = q.toLowerCase();
    document.querySelectorAll('[data-nombre]').forEach(el => {
        el.style.display = el.dataset.nombre.toLowerCase().includes(t) ? '' : 'none';
    });
    // Ocultar sección completa si todos sus elementos están ocultos
    document.querySelectorAll('.fc-grupo-seccion').forEach(seccion => {
        const visibles = Array.from(seccion.querySelectorAll('[data-nombre]'))
                              .filter(el => el.style.display !== 'none').length;
        seccion.style.display = visibles > 0 ? '' : 'none';
    });
    // Bloques de empresa (vista superadmin)
    document.querySelectorAll('.fc-empresa-bloque').forEach(bloque => {
        const visibles = Array.from(bloque.querySelectorAll('[data-nombre]'))
                              .filter(el => el.style.display !== 'none').length;
        bloque.style.display = visibles > 0 ? 'block' : 'none';
    });
}
</script>
</x-app-layout>