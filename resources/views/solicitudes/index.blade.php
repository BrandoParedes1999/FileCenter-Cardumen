<x-app-layout>
<div class="fc-wrapper">

    @include('components.sidebar')

    <div class="fc-main">
        <header class="fc-topbar">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#6366f1">
                <path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
            </svg>
            <span class="fc-topbar-title">Solicitudes de Acceso</span>
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
            @if(session('error') || $errors->has('error'))
            <div class="fc-flash error">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="#dc2626"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                {{ session('error') ?? $errors->first('error') }}
            </div>
            @endif

            <div class="fc-page-header">
                <div>
                    <h1 class="fc-page-title">Solicitudes de Acceso</h1>
                    <div style="font-size:13px;color:var(--fc-text-muted);margin-top:2px">
                        @if(in_array(Auth::user()->rol, ['Superadmin','Aux_QHSE','Admin','Gerente']))
                            Gestiona las solicitudes de acceso a recursos de tu empresa
                        @else
                            Historial de tus solicitudes de acceso
                        @endif
                    </div>
                </div>
                <a href="{{ route('solicitudes.create') }}" class="fc-btn fc-btn-primary">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
                    Nueva solicitud
                </a>
            </div>

            {{-- Filtros de status --}}
            <div class="fc-filter-bar" style="margin-bottom:20px">
                @php
                    {{-- Controller usa: status='Pendiente','Aprobado','Rechazado' --}}
                    $filtroActual = request('status', 'todas');
                    $statusOpts = ['todas'=>'Todas','Pendiente'=>'Pendientes','Aprobado'=>'Aprobadas','Rechazado'=>'Rechazadas'];
                    $statusColors = [
                        'Pendiente' => ['bg'=>'rgba(245,158,11,.1)', 'color'=>'#d97706'],
                        'Aprobado'  => ['bg'=>'rgba(5,150,105,.1)',  'color'=>'#059669'],
                        'Rechazado' => ['bg'=>'rgba(220,38,38,.1)',  'color'=>'#dc2626'],
                    ];
                @endphp
                @foreach($statusOpts as $val => $label)
                <a href="{{ route('solicitudes.index', ['status' => $val]) }}"
                   class="fc-btn fc-btn-sm {{ $filtroActual === $val ? 'fc-btn-primary' : 'fc-btn-outline' }}">
                    {{ $label }}
                </a>
                @endforeach
            </div>

            @if($solicitudes->isEmpty())
            <div class="fc-empty">
                <div class="fc-empty-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="#a5b4fc">
                        <path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                    </svg>
                </div>
                <div class="fc-empty-title">No hay solicitudes</div>
                <div class="fc-empty-sub">
                    @if($filtroActual !== 'todas')
                        No hay solicitudes con estado "{{ $statusOpts[$filtroActual] ?? $filtroActual }}".
                    @else
                        Aún no se han realizado solicitudes de acceso.
                    @endif
                </div>
                <a href="{{ route('solicitudes.create') }}" class="fc-btn fc-btn-primary">
                    Nueva solicitud
                </a>
            </div>

            @else
            <div class="fc-card" style="overflow:hidden">
                <table class="fc-table">
                    <thead>
                        <tr>
                            <th>Recurso solicitado</th>
                            <th>Solicitante</th>
                            <th>Empresa objetivo</th>
                            <th>Tipo acceso</th>
                            <th>Status</th>
                            <th>Fecha</th>
                            <th style="text-align:right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($solicitudes as $sol)
                        @php
                            {{-- Campo correcto: ->status con valores 'Pendiente','Aprobado','Rechazado' --}}
                            $sc = $statusColors[$sol->status] ?? ['bg'=>'#f1f5f9','color'=>'#64748b'];
                        @endphp
                        <tr>
                            {{-- Recurso (archivo o carpeta) --}}
                            <td>
                                <div style="display:flex;align-items:center;gap:10px">
                                    @if($sol->archivo)
                                    <div style="width:32px;height:32px;border-radius:8px;background:rgba(99,102,241,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="#6366f1"><path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6z"/></svg>
                                    </div>
                                    <div>
                                        <div style="font-size:13px;font-weight:600;color:var(--fc-text)">
                                            {{ Str::limit($sol->archivo->nombre_original ?? '(eliminado)', 35) }}
                                        </div>
                                        <div style="font-size:11px;color:var(--fc-text-muted)">
                                            Carpeta: {{ $sol->archivo->carpeta->nombre ?? '—' }}
                                        </div>
                                    </div>
                                    @elseif($sol->carpeta)
                                    <div style="width:32px;height:32px;border-radius:8px;background:rgba(79,70,229,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="#4f46e5"><path d="M20 6h-8l-2-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2z"/></svg>
                                    </div>
                                    <div>
                                        <div style="font-size:13px;font-weight:600;color:var(--fc-text)">
                                            {{ Str::limit($sol->carpeta->nombre ?? '(eliminada)', 35) }}
                                        </div>
                                        <div style="font-size:11px;color:var(--fc-text-muted)">Carpeta</div>
                                    </div>
                                    @else
                                    <span style="font-size:12px;color:var(--fc-text-muted)">— Recurso general —</span>
                                    @endif
                                </div>
                            </td>

                            {{-- Solicitante: relación correcta es ->solicitante --}}
                            <td>
                                <div class="fc-user-cell">
                                    <div class="fc-user-avatar" style="width:30px;height:30px;font-size:10px">
                                        {{ strtoupper(substr($sol->solicitante->nombre ?? '?',0,1)) }}{{ strtoupper(substr($sol->solicitante->paterno ?? '',0,1)) }}
                                    </div>
                                    <div>
                                        <div class="fc-user-nombre" style="font-size:12px">{{ $sol->solicitante->nombre_completo ?? '—' }}</div>
                                        <div class="fc-user-email">{{ $sol->solicitante->email ?? '' }}</div>
                                    </div>
                                </div>
                            </td>

                            {{-- Empresa objetivo --}}
                            <td>
                                @if($sol->empresaObjetivo)
                                <span style="font-size:11px;font-weight:600;padding:3px 8px;border-radius:6px;background:{{ $sol->empresaObjetivo->color_secundario ?? '#f1f5f9' }}22;color:{{ $sol->empresaObjetivo->color_primario ?? '#64748b' }}">
                                    {{ $sol->empresaObjetivo->siglas ?? $sol->empresaObjetivo->nombre }}
                                </span>
                                @else
                                <span style="color:var(--fc-text-muted);font-size:12px">—</span>
                                @endif
                            </td>

                            {{-- Tipo de acceso --}}
                            <td>
                                <span class="fc-badge" style="background:rgba(99,102,241,.08);color:#4f46e5">
                                    {{ $sol->tipo_acceso ?? '—' }}
                                </span>
                            </td>

                            {{-- Status: campo correcto --}}
                            <td>
                                <span class="fc-badge" style="background:{{ $sc['bg'] }};color:{{ $sc['color'] }}">
                                    {{ $sol->status }}
                                </span>
                            </td>

                            {{-- Fecha --}}
                            <td style="font-size:12px;color:var(--fc-text-muted);white-space:nowrap">
                                {{ $sol->created_at?->format('d/m/Y') ?? '—' }}
                            </td>

                            {{-- Acciones --}}
                            <td>
                                <div class="fc-table-actions" style="justify-content:flex-end">
                                    <a href="{{ route('solicitudes.show', $sol) }}" class="fc-action-ico" title="Ver detalle">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                                    </a>

                                    @if($sol->status === 'Pendiente' && in_array(Auth::user()->rol, ['Superadmin','Aux_QHSE','Admin','Gerente']))

                                    {{-- Modal aprobar con comentario + caducidad opcional --}}
                                    <button type="button" class="fc-action-ico toggle"
                                            title="Aprobar"
                                            style="color:#059669"
                                            onclick="abrirModalAprobar({{ $sol->id }})">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                                    </button>

                                    {{-- Modal rechazar (requiere comentario_revisor) --}}
                                    <button type="button" class="fc-action-ico danger"
                                            title="Rechazar"
                                            onclick="abrirModalRechazar({{ $sol->id }})">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
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

        </div>{{-- /fc-content --}}
    </div>{{-- /fc-main --}}
</div>{{-- /fc-wrapper --}}

{{-- Modal Aprobar --}}
<div class="fc-modal-overlay" id="modalAprobar">
    <div class="fc-modal">
        <div class="fc-modal-title">Aprobar solicitud</div>
        <form method="POST" id="formAprobar">
            @csrf
            <div class="fc-modal-sub">
                <div class="fc-field" style="margin-bottom:12px">
                    <label class="fc-label">Comentario (opcional)</label>
                    <textarea name="comentario_revisor" rows="3" class="fc-input" placeholder="Motivo de aprobación o instrucciones..."></textarea>
                </div>
                <div class="fc-field">
                    <label class="fc-label">Caduca el (opcional)</label>
                    <input type="date" name="caduca_en" class="fc-input" min="{{ now()->addDay()->format('Y-m-d') }}">
                </div>
            </div>
            <div class="fc-modal-btns">
                <button type="button" class="fc-modal-cancel" onclick="document.getElementById('modalAprobar').classList.remove('open')">Cancelar</button>
                <button type="submit" class="fc-modal-confirm" style="background:#059669">Aprobar</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Rechazar --}}
<div class="fc-modal-overlay" id="modalRechazar">
    <div class="fc-modal">
        <div class="fc-modal-title">Rechazar solicitud</div>
        <form method="POST" id="formRechazar">
            @csrf
            <div class="fc-modal-sub">
                <div class="fc-field">
                    <label class="fc-label">Motivo del rechazo <span style="color:#dc2626">*</span></label>
                    <textarea name="comentario_revisor" rows="3" class="fc-input" required placeholder="Indica por qué se rechaza la solicitud..."></textarea>
                </div>
            </div>
            <div class="fc-modal-btns">
                <button type="button" class="fc-modal-cancel" onclick="document.getElementById('modalRechazar').classList.remove('open')">Cancelar</button>
                <button type="submit" class="fc-modal-confirm danger">Rechazar</button>
            </div>
        </form>
    </div>
</div>

<script>
function abrirModalAprobar(id) {
    document.getElementById('formAprobar').action = '/solicitudes/' + id + '/aprobar';
    document.getElementById('modalAprobar').classList.add('open');
}
function abrirModalRechazar(id) {
    document.getElementById('formRechazar').action = '/solicitudes/' + id + '/rechazar';
    document.getElementById('modalRechazar').classList.add('open');
}
document.addEventListener('keydown', e => {
    if (e.key === 'Escape')
        document.querySelectorAll('.fc-modal-overlay.open').forEach(m => m.classList.remove('open'));
});
</script>
</x-app-layout>