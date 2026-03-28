<x-app-layout>
<div class="fc-wrapper">

    @include('components.sidebar')

    <div class="fc-main">
        <header class="fc-topbar">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#f59e0b">
                <path d="M9 16h6v-6h4l-7-7-7 7h4zm-4 2h14v2H5z"/>
            </svg>
            <span class="fc-topbar-title">Solicitudes de Subida</span>
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

            @if(session('success'))
            <div class="fc-flash success">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="#059669"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                {{ session('success') }}
            </div>
            @endif
            @if($errors->any())
            <div class="fc-flash error">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="#dc2626"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                {{ $errors->first() }}
            </div>
            @endif

            <div class="fc-page-header">
                <div>
                    <h1 class="fc-page-title">Solicitudes de Subida</h1>
                    <div style="font-size:13px;color:var(--fc-text-muted);margin-top:2px">
                        Archivos pendientes de aprobación para publicar en carpetas con control de subidas
                    </div>
                </div>
            </div>

            {{-- Filtros de status --}}
            <div class="fc-filter-bar" style="margin-bottom:20px">
                @php
                    $statusOpts   = ['Pendiente' => 'Pendientes', 'Aprobado' => 'Aprobadas', 'Rechazado' => 'Rechazadas'];
                    $statusColors = [
                        'Pendiente' => ['bg' => 'rgba(245,158,11,.1)', 'color' => '#d97706'],
                        'Aprobado'  => ['bg' => 'rgba(5,150,105,.1)',  'color' => '#059669'],
                        'Rechazado' => ['bg' => 'rgba(220,38,38,.1)',  'color' => '#dc2626'],
                    ];
                @endphp
                @foreach($statusOpts as $val => $label)
                <a href="{{ route('solicitudes-subida.index', ['status' => $val]) }}"
                   class="fc-btn fc-btn-sm {{ $filtroStatus === $val ? 'fc-btn-primary' : 'fc-btn-outline' }}">
                    {{ $label }}
                </a>
                @endforeach
                <a href="{{ route('solicitudes-subida.index') }}"
                   class="fc-btn fc-btn-sm {{ !$filtroStatus ? 'fc-btn-primary' : 'fc-btn-outline' }}">
                    Todas
                </a>
            </div>

            @if($solicitudes->isEmpty())
            <div class="fc-empty">
                <div class="fc-empty-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="#fbbf24">
                        <path d="M9 16h6v-6h4l-7-7-7 7h4zm-4 2h14v2H5z"/>
                    </svg>
                </div>
                <div class="fc-empty-title">No hay solicitudes de subida</div>
                <div class="fc-empty-sub">Cuando Auxiliares o Empleados intenten subir archivos a carpetas con aprobación requerida, aparecerán aquí.</div>
            </div>
            @else
            <div class="fc-card" style="overflow:hidden">
                <table class="fc-table">
                    <thead>
                        <tr>
                            <th>Archivo</th>
                            <th>Carpeta</th>
                            <th>Solicitante</th>
                            <th>Tamaño</th>
                            <th>Status</th>
                            <th>Fecha</th>
                            <th style="text-align:right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($solicitudes as $sol)
                        @php
                            $sc = $statusColors[$sol->status] ?? ['bg' => '#f1f5f9', 'color' => '#64748b'];
                            // Icono por extensión
                            $extColors = ['pdf' => '#dc2626', 'docx' => '#2563eb', 'doc' => '#2563eb', 'xlsx' => '#059669', 'xls' => '#059669'];
                            $iconColor = $extColors[$sol->extension] ?? '#64748b';
                        @endphp
                        <tr>
                            {{-- Archivo --}}
                            <td>
                                <div style="display:flex;align-items:center;gap:10px">
                                    <div style="width:34px;height:34px;border-radius:8px;background:rgba(0,0,0,0.05);
                                                display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="{{ $iconColor }}">
                                            <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <div style="font-size:13px;font-weight:600;color:var(--fc-text)">
                                            {{ Str::limit($sol->nombre_original, 40) }}
                                        </div>
                                        <span class="fc-badge fc-badge-ext">{{ strtoupper($sol->extension) }}</span>
                                    </div>
                                </div>
                            </td>

                            {{-- Carpeta --}}
                            <td>
                                <div style="font-size:13px;font-weight:600;color:var(--fc-text)">
                                    {{ $sol->carpeta->nombre ?? '—' }}
                                </div>
                                <div style="font-size:11px;color:var(--fc-text-muted)">
                                    {{ $sol->carpeta->empresa->nombre ?? '' }}
                                </div>
                            </td>

                            {{-- Solicitante --}}
                            <td>
                                <div class="fc-user-cell">
                                    <div class="fc-user-avatar" style="width:30px;height:30px;font-size:10px">
                                        {{ strtoupper(substr($sol->solicitante->nombre ?? '?', 0, 1)) }}{{ strtoupper(substr($sol->solicitante->paterno ?? '', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fc-user-nombre" style="font-size:12px">{{ $sol->solicitante->nombre_completo ?? '—' }}</div>
                                        <div class="fc-user-email">{{ $sol->solicitante->rol ?? '' }}</div>
                                    </div>
                                </div>
                            </td>

                            {{-- Tamaño --}}
                            <td style="font-size:12px;color:var(--fc-text-muted)">
                                {{ $sol->tamanioFormateado() }}
                            </td>

                            {{-- Status --}}
                            <td>
                                <span class="fc-badge" style="background:{{ $sc['bg'] }};color:{{ $sc['color'] }}">
                                    {{ $sol->status }}
                                </span>
                            </td>

                            {{-- Fecha --}}
                            <td style="font-size:12px;color:var(--fc-text-muted);white-space:nowrap">
                                {{ $sol->created_at?->format('d/m/Y H:i') ?? '—' }}
                            </td>

                            {{-- Acciones --}}
                            <td>
                                <div class="fc-table-actions" style="justify-content:flex-end">
                                    <a href="{{ route('solicitudes-subida.show', $sol) }}"
                                       class="fc-action-ico" title="Ver detalle">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5z"/>
                                        </svg>
                                    </a>

                                    @if($sol->estaPendiente() && in_array(Auth::user()->rol, ['Superadmin','Aux_QHSE','Admin','Gerente']))
                                    {{-- Aprobar rápido --}}
                                    <button type="button" class="fc-action-ico toggle"
                                            style="color:#059669"
                                            title="Aprobar"
                                            onclick="abrirModalAprobar({{ $sol->id }})">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                                        </svg>
                                    </button>

                                    {{-- Rechazar rápido --}}
                                    <button type="button" class="fc-action-ico danger"
                                            title="Rechazar"
                                            onclick="abrirModalRechazar({{ $sol->id }})">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                                        </svg>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                @if($solicitudes->hasPages())
                <div class="fc-pagination">
                    <div class="fc-pag-info">
                        Mostrando {{ $solicitudes->firstItem() }}–{{ $solicitudes->lastItem() }} de {{ $solicitudes->total() }}
                    </div>
                    <div class="fc-pag-links">
                        @if($solicitudes->onFirstPage())
                            <span class="fc-pag-btn disabled">‹ Anterior</span>
                        @else
                            <a href="{{ $solicitudes->previousPageUrl() }}" class="fc-pag-btn">‹ Anterior</a>
                        @endif
                        @foreach($solicitudes->getUrlRange(1, $solicitudes->lastPage()) as $page => $url)
                            <a href="{{ $url }}" class="fc-pag-btn {{ $page == $solicitudes->currentPage() ? 'active' : '' }}">{{ $page }}</a>
                        @endforeach
                        @if($solicitudes->hasMorePages())
                            <a href="{{ $solicitudes->nextPageUrl() }}" class="fc-pag-btn">Siguiente ›</a>
                        @else
                            <span class="fc-pag-btn disabled">Siguiente ›</span>
                        @endif
                    </div>
                </div>
                @endif
            </div>
            @endif

        </div>
    </div>
</div>

{{-- Modal Aprobar --}}
<div class="fc-modal-overlay" id="modalAprobar">
    <div class="fc-modal">
        <div class="fc-modal-title">Aprobar solicitud de subida</div>
        <form method="POST" id="formAprobar">
            @csrf
            <div class="fc-modal-sub">
                <div class="fc-field">
                    <label class="fc-label">Comentario (opcional)</label>
                    <textarea name="comentario_revisor" rows="2" class="fc-input"
                              placeholder="Motivo de aprobación..."></textarea>
                </div>
            </div>
            <div class="fc-modal-btns">
                <button type="button" class="fc-modal-cancel"
                        onclick="document.getElementById('modalAprobar').classList.remove('open')">Cancelar</button>
                <button type="submit" class="fc-modal-confirm" style="background:#059669">
                    Aprobar y publicar
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Rechazar --}}
<div class="fc-modal-overlay" id="modalRechazar">
    <div class="fc-modal">
        <div class="fc-modal-title">Rechazar solicitud de subida</div>
        <form method="POST" id="formRechazar">
            @csrf
            <div class="fc-modal-sub">
                <div class="fc-field">
                    <label class="fc-label">Motivo del rechazo <span style="color:#dc2626">*</span></label>
                    <textarea name="comentario_revisor" rows="3" class="fc-input" required
                              placeholder="Indica por qué se rechaza el archivo..."></textarea>
                </div>
                <div style="font-size:12px;color:#dc2626;margin-top:8px;padding:8px 12px;background:rgba(220,38,38,.06);border-radius:8px">
                    ⚠️ El archivo temporal será eliminado del servidor al rechazar.
                </div>
            </div>
            <div class="fc-modal-btns">
                <button type="button" class="fc-modal-cancel"
                        onclick="document.getElementById('modalRechazar').classList.remove('open')">Cancelar</button>
                <button type="submit" class="fc-modal-confirm danger">Rechazar y eliminar</button>
            </div>
        </form>
    </div>
</div>

<script>
function abrirModalAprobar(id) {
    document.getElementById('formAprobar').action = '/solicitudes-subida/' + id + '/aprobar';
    document.getElementById('modalAprobar').classList.add('open');
}
function abrirModalRechazar(id) {
    document.getElementById('formRechazar').action = '/solicitudes-subida/' + id + '/rechazar';
    document.getElementById('modalRechazar').classList.add('open');
}
document.addEventListener('keydown', e => {
    if (e.key === 'Escape')
        document.querySelectorAll('.fc-modal-overlay.open').forEach(m => m.classList.remove('open'));
});
</script>
</x-app-layout>