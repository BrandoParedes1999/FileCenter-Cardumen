<x-app-layout>
    <div class="fc-wrapper">
        @include('components.sidebar')

        <div class="fc-main">
            <header class="fc-topbar">
                @php
                    $extColors = [
                        'pdf'  => '#dc2626', 'doc'  => '#2563eb', 'docx' => '#2563eb',
                        'xls'  => '#059669', 'xlsx' => '#059669', 'ppt'  => '#ea580c',
                        'pptx' => '#ea580c', 'zip'  => '#f59e0b', 'rar'  => '#f59e0b',
                        'jpg'  => '#06b6d4', 'jpeg' => '#06b6d4', 'png'  => '#06b6d4',
                    ];
                    $ext   = strtolower($archivo->extension);
                    $color = $extColors[$ext] ?? '#64748b';

                    $hexClean = ltrim($color, '#');
                    $r = hexdec(substr($hexClean, 0, 2));
                    $g = hexdec(substr($hexClean, 2, 2));
                    $b = hexdec(substr($hexClean, 4, 2));
                    $colorRgba10 = "rgba({$r},{$g},{$b},0.1)";
                @endphp
                <svg width="16" height="16" viewBox="0 0 24 24" fill="{{ $color }}">
                    <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6z"/>
                </svg>
                <span class="fc-topbar-title">{{ $archivo->nombre_original }}</span>
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

            <div class="fc-content">

                {{-- Bug #10 fix: wrapper fc-content-cols para layout horizontal --}}
                <div class="fc-content-cols">

                    {{-- ══ COLUMNA PRINCIPAL ══════════════════════════════════ --}}
                    <div class="fc-col-main">

                        {{-- Breadcrumb --}}
                        <div class="fc-breadcrumb">
                            <a href="{{ route('carpetas.index') }}" class="fc-bread-item">📁 Inicio</a>
                            @foreach($migas as $i => $miga)
                                <span class="fc-bread-sep">›</span>
                                <a href="{{ route('carpetas.show', $miga['id']) }}" class="fc-bread-item">{{ $miga['nombre'] }}</a>
                            @endforeach
                            <span class="fc-bread-sep">›</span>
                            <span class="fc-bread-current">{{ $archivo->nombre_original }}</span>
                        </div>

                        {{-- Flash --}}
                        @if(session('success'))
                        <div class="fc-flash success">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="#059669"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                            {{ session('success') }}
                        </div>
                        @endif
                        @if(session('error') || $errors->any())
                        <div class="fc-flash error">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="#dc2626"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                            {{ session('error') ?? $errors->first() }}
                        </div>
                        @endif

                        {{-- Header del archivo --}}
                        <div class="fc-file-header">
                            <div class="fc-file-header-top">
                                <div class="fc-file-icon-wrap" style="background:{{ $colorRgba10 }}">
                                    <svg width="32" height="32" viewBox="0 0 24 24" fill="{{ $color }}">
                                        <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6z"/>
                                    </svg>
                                </div>

                                <div style="flex:1;min-width:0">
                                    <div class="fc-file-name">{{ $archivo->nombre_original }}</div>
                                    <div class="fc-file-meta-row">
                                        <span class="fc-badge fc-badge-ext">{{ strtoupper($ext) }}</span>
                                        <span class="fc-badge fc-badge-ver">v{{ $archivo->version }}</span>
                                        <span class="fc-badge {{ $archivo->carpeta->es_publico ? 'fc-badge-pub' : 'fc-badge-priv' }}">
                                            {{ $archivo->carpeta->es_publico ? '🌐 Carpeta pública' : '🔒 Carpeta privada' }}
                                        </span>
                                        <span style="font-size:12px;color:#94a3b8">{{ $archivo->tamanioFormateado() }}</span>
                                    </div>

                                    <div class="fc-file-actions">
                                        @can('download', $archivo)
                                        <a href="{{ route('archivos.descargar', $archivo) }}"
                                           class="fc-action-btn download"
                                           style="background:rgba(5,150,105,.08);color:#059669;border-color:rgba(5,150,105,.25)">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/></svg>
                                            Descargar
                                        </a>
                                        @endcan

                                        @can('update', $archivo)
                                        <button onclick="toggleEditDesc()"
                                                class="fc-action-btn edit"
                                                style="background:rgba(99,102,241,.08);color:#4f46e5;border-color:rgba(99,102,241,.25)">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
                                            Editar descripción
                                        </button>
                                        @endcan

                                        @can('delete', $archivo)
                                        <button onclick="document.getElementById('modalEliminar').classList.add('open')"
                                                class="fc-action-btn delete"
                                                style="background:rgba(220,38,38,.08);color:#dc2626;border-color:rgba(220,38,38,.25)">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
                                            Eliminar
                                        </button>
                                        @endcan

                                        @cannot('download', $archivo)
                                        <a href="{{ route('solicitudes.create', ['archivo_id' => $archivo->id]) }}"
                                           class="fc-action-btn solicitar"
                                           style="background:rgba(245,158,11,.08);color:#d97706;border-color:rgba(245,158,11,.25)">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
                                            Solicitar acceso
                                        </a>
                                        @endcannot
                                    </div>
                                </div>
                            </div>

                            {{-- Stats --}}
                            <div class="fc-file-stats">
                                <div class="fc-file-stat">
                                    <div class="fc-stat-val">{{ $archivo->version }}</div>
                                    <div class="fc-stat-lbl">Versión actual</div>
                                </div>
                                <div class="fc-file-stat">
                                    <div class="fc-stat-val">{{ $archivo->numero_descargas }}</div>
                                    <div class="fc-stat-lbl">Descargas</div>
                                </div>
                                <div class="fc-file-stat">
                                    <div class="fc-stat-val">{{ $archivo->versiones->count() }}</div>
                                    <div class="fc-stat-lbl">Versiones</div>
                                </div>
                                <div class="fc-file-stat">
                                    <div class="fc-stat-val">{{ $archivo->tamanioFormateado() }}</div>
                                    <div class="fc-stat-lbl">Tamaño</div>
                                </div>
                            </div>
                        </div>

                        {{-- Descripción --}}
                        <div class="fc-section-card">
                            <div class="fc-section-header">
                                <div class="fc-section-icon" style="background:rgba(99,102,241,0.1)">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="#6366f1"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
                                </div>
                                Descripción
                            </div>
                            <div class="fc-section-body">
                                <div id="descText">
                                    @if($archivo->descripcion)
                                        <p class="fc-desc-text">{{ $archivo->descripcion }}</p>
                                    @else
                                        <p class="fc-desc-empty">Sin descripción. Puedes agregar una haciendo clic en "Editar descripción".</p>
                                    @endif
                                </div>

                                @can('update', $archivo)
                                <div class="fc-edit-desc-form" id="editDescForm">
                                    <form action="{{ route('archivos.update', $archivo) }}" method="POST">
                                        @csrf @method('PUT')
                                        <textarea name="descripcion" rows="4"
                                            placeholder="Describe el contenido de este archivo...">{{ $archivo->descripcion }}</textarea>
                                        <div class="fc-edit-desc-btns">
                                            <button type="button" class="fc-mini-btn" onclick="toggleEditDesc()">Cancelar</button>
                                            <button type="submit" class="fc-mini-btn primary">Guardar</button>
                                        </div>
                                    </form>
                                </div>
                                @endcan
                            </div>
                        </div>

                        {{-- Historial de versiones --}}
                        <div class="fc-section-card">
                            <div class="fc-section-header">
                                <div class="fc-section-icon" style="background:rgba(124,58,237,0.1)">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="#7c3aed"><path d="M13 3c-4.97 0-9 4.03-9 9H1l3.89 3.89.07.14L9 12H6c0-3.87 3.13-7 7-7s7 3.13 7 7-3.13 7-7 7c-1.93 0-3.68-.79-4.94-2.06l-1.42 1.42C8.27 19.99 10.51 21 13 21c4.97 0 9-4.03 9-9s-4.03-9-9-9zm-1 5v5l4.28 2.54.72-1.21-3.5-2.08V8H12z"/></svg>
                                </div>
                                Historial de versiones
                                <span style="margin-left:auto;font-size:11px;background:#f1f5f9;color:#64748b;padding:2px 9px;border-radius:20px;font-weight:600">
                                    {{ $archivo->versiones->count() }}
                                </span>
                            </div>
                            <div>
                                @forelse($archivo->versiones as $ver)
                                <div class="fc-ver-item">
                                    <div class="fc-ver-num {{ $ver->version == $archivo->version ? 'current' : '' }}">
                                        v{{ $ver->version }}
                                    </div>
                                    <div style="flex:1;min-width:0">
                                        <div class="fc-ver-name">
                                            {{ $ver->nombre_original }}
                                            @if($ver->version == $archivo->version)
                                                <span class="fc-ver-badge-current">Actual</span>
                                            @endif
                                        </div>
                                        <div class="fc-ver-meta">
                                            {{ $ver->tamanioFormateado() }}
                                            · Subido por {{ $ver->subidoPor->nombre ?? 'N/A' }} {{ $ver->subidoPor->paterno ?? '' }}
                                            · {{ $ver->created_at?->diffForHumans() ?? '—' }}
                                        </div>
                                        @if($ver->nota_version)
                                        <div class="fc-ver-nota">{{ $ver->nota_version }}</div>
                                        @endif
                                    </div>
                                    <div class="fc-ver-actions">
                                        @if($ver->version != $archivo->version)
                                            @can('update', $archivo)
                                            <button onclick="confirmarRestaurar({{ $ver->id }}, {{ $ver->version }})"
                                                class="fc-ver-btn">Restaurar</button>
                                            @endcan
                                        @endif
                                    </div>
                                </div>
                                @empty
                                <div style="padding:20px;text-align:center;font-size:13px;color:#94a3b8">
                                    Sin historial de versiones
                                </div>
                                @endforelse
                            </div>
                        </div>

                    </div>{{-- /fc-col-main --}}

                    {{-- ══ PANEL LATERAL ══════════════════════════════════════ --}}
                    <div class="fc-col-side">

                        {{-- Info general --}}
                        <div class="fc-info-card">
                            <div class="fc-info-header">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="#6366f1"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                                Información
                            </div>
                            <div class="fc-info-body">
                                <div class="fc-info-row">
                                    <span class="fc-info-label">Carpeta</span>
                                    <a href="{{ route('carpetas.show', $archivo->carpeta) }}"
                                       style="font-size:12px;font-weight:600;color:#6366f1;text-decoration:none;text-align:right">
                                        {{ $archivo->carpeta->nombre }}
                                    </a>
                                </div>
                                <div class="fc-info-row">
                                    <span class="fc-info-label">Subido por</span>
                                    <span class="fc-info-val">{{ $archivo->subidoPor->nombre ?? '—' }} {{ $archivo->subidoPor->paterno ?? '' }}</span>
                                </div>
                                <div class="fc-info-row">
                                    <span class="fc-info-label">Fecha subida</span>
                                    <span class="fc-info-val">{{ $archivo->created_at?->format('d/m/Y H:i') ?? '—' }}</span>
                                </div>
                                <div class="fc-info-row">
                                    <span class="fc-info-label">Última mod.</span>
                                    <span class="fc-info-val">{{ $archivo->updated_at?->diffForHumans() ?? '—' }}</span>
                                </div>
                                <div class="fc-info-row">
                                    <span class="fc-info-label">Tipo MIME</span>
                                    <span class="fc-info-val mono">{{ $archivo->tipo_mime ?? '—' }}</span>
                                </div>
                                <div class="fc-info-row">
                                    <span class="fc-info-label">Extensión</span>
                                    <span class="fc-info-val">{{ strtoupper($ext) }}</span>
                                </div>
                                <div class="fc-info-row">
                                    <span class="fc-info-label">Tamaño</span>
                                    <span class="fc-info-val">{{ $archivo->tamanioFormateado() }}</span>
                                </div>
                                <div class="fc-info-row">
                                    <span class="fc-info-label">Descargas</span>
                                    <span class="fc-info-val">{{ $archivo->numero_descargas }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- SHA256 --}}
                        @if($archivo->hash_sha256)
                        <div class="fc-info-card">
                            <div class="fc-info-header">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="#059669"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/></svg>
                                Integridad SHA-256
                            </div>
                            <div style="padding:14px 16px">
                                <div class="fc-hash-box" onclick="copiarHash('{{ $archivo->hash_sha256 }}')" title="Clic para copiar">
                                    {{ $archivo->hash_sha256 }}
                                </div>
                                <div style="font-size:11px;color:#94a3b8;margin-top:6px">Clic para copiar al portapapeles</div>
                            </div>
                        </div>
                        @endif

                        {{-- Subir nueva versión --}}
                        @can('update', $archivo)
                        <div class="fc-info-card">
                            <div class="fc-info-header">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="#f59e0b"><path d="M9 16h6v-6h4l-7-7-7 7h4zm-4 2h14v2H5z"/></svg>
                                Nueva versión
                            </div>
                            <div style="padding:14px 16px">
                                <p style="font-size:12px;color:#64748b;margin-bottom:12px;line-height:1.6">
                                    Sube una nueva versión del archivo. La versión actual quedará en el historial.
                                </p>
                                <a href="{{ route('archivos.create', ['carpeta_id' => $archivo->carpeta_id]) }}"
                                   class="fc-btn fc-btn-warning" style="justify-content:center">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16h6v-6h4l-7-7-7 7h4zm-4 2h14v2H5z"/></svg>
                                    Subir nueva versión
                                </a>
                            </div>
                        </div>
                        @endcan

                    </div>{{-- /fc-col-side --}}

                </div>{{-- /fc-content-cols --}}
            </div>{{-- /fc-content --}}
        </div>{{-- /fc-main --}}
    </div>

    {{-- Modal eliminar --}}
    <div class="fc-modal-overlay" id="modalEliminar">
        <div class="fc-modal">
            <div class="fc-modal-title">¿Eliminar este archivo?</div>
            <div class="fc-modal-sub">
                El archivo "<strong>{{ $archivo->nombre_original }}</strong>" se enviará a la papelera.
                El historial de versiones quedará guardado y puede ser recuperado por un administrador.
            </div>
            <div class="fc-modal-btns">
                <button class="fc-modal-cancel"
                    onclick="document.getElementById('modalEliminar').classList.remove('open')">Cancelar</button>
                <form action="{{ route('archivos.destroy', $archivo) }}" method="POST">
                    @csrf @method('DELETE')
                    <button type="submit" class="fc-modal-confirm danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal restaurar versión --}}
    <div class="fc-modal-overlay" id="modalRestaurar">
        <div class="fc-modal">
            <div class="fc-modal-title">¿Restaurar esta versión?</div>
            <div class="fc-modal-sub" id="modalRestaurarSub">
                El archivo principal pasará a usar la versión seleccionada.
            </div>
            <div class="fc-modal-btns">
                <button class="fc-modal-cancel"
                    onclick="document.getElementById('modalRestaurar').classList.remove('open')">Cancelar</button>
                <form action="{{ route('archivos.restaurar-version', $archivo) }}" method="POST" id="formRestaurar">
                    @csrf
                    <input type="hidden" name="version_id" id="versionIdInput">
                    <button type="submit" class="fc-modal-confirm">Restaurar</button>
                </form>
            </div>
        </div>
    </div>

    <script>
    function toggleEditDesc() {
        const form = document.getElementById('editDescForm');
        const text = document.getElementById('descText');
        const visible = form.classList.toggle('visible');
        text.style.display = visible ? 'none' : 'block';
    }

    function copiarHash(hash) {
        navigator.clipboard.writeText(hash).then(() => {
            const el = event.target;
            const orig = el.textContent;
            el.textContent = '✓ Copiado';
            el.style.color = '#059669';
            setTimeout(() => { el.textContent = orig; el.style.color = ''; }, 1500);
        });
    }

    function confirmarRestaurar(versionId, versionNum) {
        document.getElementById('versionIdInput').value = versionId;
        document.getElementById('modalRestaurarSub').innerHTML =
            `El archivo principal pasará a usar la <strong>versión ${versionNum}</strong>. La versión actual quedará en el historial.`;
        document.getElementById('modalRestaurar').classList.add('open');
    }

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape')
            document.querySelectorAll('.fc-modal-overlay.open').forEach(m => m.classList.remove('open'));
    });
    </script>
</x-app-layout>