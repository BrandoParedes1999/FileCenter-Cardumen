<x-app-layout>
<div class="fc-wrapper">
    @include('components.sidebar')

    <div class="fc-main">
        <header class="fc-topbar">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#6366f1">
                <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6z"/>
            </svg>
            <span class="fc-topbar-title">
                @if($carpeta) Archivos — {{ $carpeta->nombre }} @else Todos los archivos @endif
            </span>
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
                <input type="text" placeholder="Buscar archivos..." oninput="filtrarArchivos(this.value)">
            </div>

            @if($carpeta)
                @can('create', App\Models\Archivo::class)
                <a href="{{ route('archivos.create', ['carpeta_id' => $carpeta->id]) }}" class="fc-btn fc-btn-primary">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16h6v-6h4l-7-7-7 7h4zm-4 2h14v2H5z"/></svg>
                    Subir archivo
                </a>
                @endcan
            @endif

            <div class="fc-view-btns" style="margin-left:auto">
                <button class="fc-view-btn active" id="btnGrid" onclick="setView('grid')" title="Cuadrícula">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M3 3h8v8H3zm10 0h8v8h-8zM3 13h8v8H3zm10 0h8v8h-8z"/></svg>
                </button>
                <button class="fc-view-btn" id="btnList" onclick="setView('list')" title="Lista">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M3 13h2v-2H3v2zm0 4h2v-2H3v2zm0-8h2V7H3v2zm4 4h14v-2H7v2zm0 4h14v-2H7v2zM7 7v2h14V7H7z"/></svg>
                </button>
            </div>
        </div>

        <div class="fc-content">

            {{-- Breadcrumb --}}
            <div class="fc-breadcrumb">
                <a href="{{ route('carpetas.index') }}" class="fc-bread-item">📁 Inicio</a>
                @if($carpeta)
                    <span class="fc-bread-sep">›</span>
                    <a href="{{ route('carpetas.show', $carpeta) }}" class="fc-bread-item">{{ $carpeta->nombre }}</a>
                    <span class="fc-bread-sep">›</span>
                    <span class="fc-bread-current">Archivos</span>
                @else
                    <span class="fc-bread-sep">›</span>
                    <span class="fc-bread-current">Todos los archivos</span>
                @endif
            </div>

            @if($archivos->isEmpty())
            <div class="fc-empty">
                <div class="fc-empty-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="#a5b4fc">
                        <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6z"/>
                    </svg>
                </div>
                <div class="fc-empty-title">No hay archivos</div>
                <div class="fc-empty-sub">
                    @if($carpeta) Esta carpeta no tiene archivos aún.
                    @else No se encontraron archivos en el sistema.
                    @endif
                </div>
                @if($carpeta)
                    @can('create', App\Models\Archivo::class)
                    <a href="{{ route('archivos.create', ['carpeta_id' => $carpeta->id]) }}" class="fc-btn fc-btn-primary">
                        Subir primer archivo
                    </a>
                    @endcan
                @endif
            </div>
            @else

            {{-- Vista grid --}}
            <div class="fc-files-grid" id="filesGrid">
                @foreach($archivos as $archivo)
                @php
                    $colores = $archivo->colorExtension();
                    $ext     = strtolower($archivo->extension);
                @endphp
                <div class="fc-file-card" data-nombre="{{ strtolower($archivo->nombre_original) }}">
                    <div class="fc-file-preview">
                        <div style="width:52px;height:52px;border-radius:12px;
                                    background:{{ $colores['bg'] }};
                                    display:flex;align-items:center;justify-content:center">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="{{ $colores['color'] }}">
                                <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6z"/>
                            </svg>
                        </div>
                        <span style="position:absolute;top:8px;right:10px;font-size:10px;font-weight:700;
                                     background:#f1f5f9;color:#64748b;padding:2px 7px;border-radius:10px;
                                     text-transform:uppercase">{{ strtoupper($ext) }}</span>
                    </div>
                    <div class="fc-file-body">
                        <div class="fc-file-name" title="{{ $archivo->nombre_original }}">
                            {{ $archivo->nombre_original }}
                        </div>
                        <div class="fc-file-meta">
                            {{ $archivo->tamanioFormateado() }} · v{{ $archivo->version }}
                            @if($archivo->carpeta)
                                · {{ $archivo->carpeta->nombre }}
                            @endif
                        </div>
                        <div class="fc-file-actions">
                            <a href="{{ route('archivos.show', $archivo) }}" class="fc-file-btn">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5z"/></svg>
                                Ver
                            </a>
                            @can('download', $archivo)
                            <a href="{{ route('archivos.descargar', $archivo) }}" class="fc-file-btn download">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor"><path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/></svg>
                                Bajar
                            </a>
                            @endcan
                            @can('delete', $archivo)
                            <button onclick="confirmarEliminar({{ $archivo->id }}, '{{ addslashes($archivo->nombre_original) }}')"
                                    class="fc-file-btn danger">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
                            </button>
                            @endcan
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Vista lista --}}
            <div class="fc-files-list" id="filesList" style="display:none">
                @foreach($archivos as $archivo)
                @php $colores = $archivo->colorExtension(); @endphp
                <div class="fc-file-row" data-nombre="{{ strtolower($archivo->nombre_original) }}">
                    <div class="fc-file-row-icon" style="background:{{ $colores['bg'] }}">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="{{ $colores['color'] }}">
                            <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6z"/>
                        </svg>
                    </div>
                    <div class="fc-file-row-name">{{ $archivo->nombre_original }}</div>
                    <div class="fc-file-row-meta">{{ $archivo->tamanioFormateado() }}</div>
                    <span class="fc-file-row-ver">v{{ $archivo->version }}</span>
                    @if($archivo->carpeta && !$carpeta)
                    <span style="font-size:11px;color:#94a3b8;margin-right:10px">
                        {{ $archivo->carpeta->nombre }}
                    </span>
                    @endif
                    <div class="fc-file-row-actions">
                        <a href="{{ route('archivos.show', $archivo) }}" class="fc-file-row-btn" title="Ver detalle">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5z"/></svg>
                        </a>
                        @can('download', $archivo)
                        <a href="{{ route('archivos.descargar', $archivo) }}" class="fc-file-row-btn dl" title="Descargar">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/></svg>
                        </a>
                        @endcan
                        @can('delete', $archivo)
                        <button onclick="confirmarEliminar({{ $archivo->id }}, '{{ addslashes($archivo->nombre_original) }}')"
                                class="fc-file-row-btn del" title="Eliminar">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
                        </button>
                        @endcan
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Paginación --}}
            @if($archivos->hasPages())
            <div class="fc-pagination">
                <div class="fc-pag-info">
                    Mostrando {{ $archivos->firstItem() }}–{{ $archivos->lastItem() }} de {{ $archivos->total() }}
                </div>
                <div class="fc-pag-links">
                    @if($archivos->onFirstPage())
                        <span class="fc-pag-btn disabled">‹ Anterior</span>
                    @else
                        <a href="{{ $archivos->previousPageUrl() }}" class="fc-pag-btn">‹ Anterior</a>
                    @endif
                    @foreach($archivos->getUrlRange(1, $archivos->lastPage()) as $page => $url)
                        <a href="{{ $url }}" class="fc-pag-btn {{ $page == $archivos->currentPage() ? 'active' : '' }}">{{ $page }}</a>
                    @endforeach
                    @if($archivos->hasMorePages())
                        <a href="{{ $archivos->nextPageUrl() }}" class="fc-pag-btn">Siguiente ›</a>
                    @else
                        <span class="fc-pag-btn disabled">Siguiente ›</span>
                    @endif
                </div>
            </div>
            @endif

            @endif

        </div>
    </div>
</div>

{{-- Modal eliminar --}}
<div class="fc-modal-overlay" id="modalEliminar">
    <div class="fc-modal">
        <div class="fc-modal-title">¿Eliminar archivo?</div>
        <div class="fc-modal-sub">
            El archivo "<strong id="modalNombre"></strong>" será enviado a la papelera.
        </div>
        <div class="fc-modal-btns">
            <button class="fc-modal-cancel"
                    onclick="document.getElementById('modalEliminar').classList.remove('open')">Cancelar</button>
            <form id="formEliminar" method="POST">
                @csrf @method('DELETE')
                <button type="submit" class="fc-modal-confirm danger">Eliminar</button>
            </form>
        </div>
    </div>
</div>

<script>
function setView(v) {
    document.getElementById('btnGrid').classList.toggle('active', v === 'grid');
    document.getElementById('btnList').classList.toggle('active', v === 'list');
    document.getElementById('filesGrid').style.display = v === 'grid' ? 'grid' : 'none';
    document.getElementById('filesList').style.display = v === 'list' ? 'flex'  : 'none';
}

function filtrarArchivos(q) {
    const t = q.toLowerCase();
    document.querySelectorAll('[data-nombre]').forEach(el => {
        el.style.display = el.dataset.nombre.includes(t) ? '' : 'none';
    });
}

function confirmarEliminar(id, nombre) {
    document.getElementById('modalNombre').textContent = nombre;
    document.getElementById('formEliminar').action = '/archivos/' + id;
    document.getElementById('modalEliminar').classList.add('open');
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape')
        document.querySelectorAll('.fc-modal-overlay.open').forEach(m => m.classList.remove('open'));
});
</script>
</x-app-layout>