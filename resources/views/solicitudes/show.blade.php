<x-app-layout>
<div class="fc-wrapper">

    @include('components.sidebar')

    <div class="fc-main">
        <header class="fc-topbar">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#6366f1">
                <path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
            </svg>
            <span class="fc-topbar-title">Solicitud #{{ $solicitud->id }}</span>
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

            <div class="fc-breadcrumb">
                <a href="{{ route('solicitudes.index') }}" class="fc-bread-item">📨 Solicitudes</a>
                <span class="fc-bread-sep">›</span>
                <span class="fc-bread-current">Solicitud #{{ $solicitud->id }}</span>
            </div>

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

            @php
                {{-- Status real: 'Pendiente', 'Aprobado', 'Rechazado' --}}
                $statusConfig = [
                    'Pendiente' => ['bg'=>'rgba(245,158,11,.1)', 'color'=>'#d97706', 'icon'=>'M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z'],
                    'Aprobado'  => ['bg'=>'rgba(5,150,105,.1)',  'color'=>'#059669', 'icon'=>'M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z'],
                    'Rechazado' => ['bg'=>'rgba(220,38,38,.1)',  'color'=>'#dc2626', 'icon'=>'M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z'],
                ];
                $sc = $statusConfig[$solicitud->status] ?? $statusConfig['Pendiente'];
            @endphp

            <div class="fc-content-cols">

                {{-- ── Panel principal ── --}}
                <div class="fc-col-main">

                    {{-- Banner de status --}}
                    <div style="background:{{ $sc['bg'] }};border:1px solid {{ $sc['color'] }}33;border-radius:14px;padding:20px 24px;display:flex;align-items:center;gap:16px;margin-bottom:20px">
                        <div style="width:44px;height:44px;border-radius:12px;background:{{ $sc['color'] }}18;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="{{ $sc['color'] }}"><path d="{{ $sc['icon'] }}"/></svg>
                        </div>
                        <div style="flex:1">
                            <div style="font-size:16px;font-weight:700;color:var(--fc-text)">
                                Solicitud {{ $solicitud->status }}
                            </div>
                            <div style="font-size:13px;color:var(--fc-text-muted);margin-top:3px">
                                @if($solicitud->status === 'Pendiente')
                                    Esperando revisión de un administrador
                                @elseif($solicitud->status === 'Aprobado')
                                    Aprobada {{ $solicitud->updated_at?->diffForHumans() }}
                                    @if($solicitud->revisor) por {{ $solicitud->revisor->nombre_completo }} @endif
                                    @if($solicitud->caduca_en)
                                        · Caduca {{ \Carbon\Carbon::parse($solicitud->caduca_en)->format('d/m/Y') }}
                                    @endif
                                @else
                                    Rechazada {{ $solicitud->updated_at?->diffForHumans() }}
                                    @if($solicitud->revisor) por {{ $solicitud->revisor->nombre_completo }} @endif
                                @endif
                            </div>
                        </div>

                        {{-- Acciones de revisión --}}
                        @if($solicitud->status === 'Pendiente' && in_array(Auth::user()->rol, ['Superadmin','Aux_QHSE','Admin','Gerente']))
                        <div style="display:flex;gap:8px">
                            <button type="button" class="fc-btn fc-btn-success" onclick="document.getElementById('modalAprobarShow').classList.add('open')">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                                Aprobar
                            </button>
                            <button type="button" class="fc-btn fc-btn-danger-outline" onclick="document.getElementById('modalRechazarShow').classList.add('open')">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
                                Rechazar
                            </button>
                        </div>
                        @endif
                    </div>

                    {{-- Recurso solicitado --}}
                    <div class="fc-section-card">
                        <div class="fc-section-header">
                            <div class="fc-section-icon" style="background:rgba(99,102,241,.1)">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="#6366f1"><path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6z"/></svg>
                            </div>
                            Recurso solicitado
                        </div>
                        <div class="fc-section-body">
                            @if($solicitud->archivo)
                            <div style="display:flex;align-items:center;gap:14px;padding:14px;background:var(--fc-bg);border-radius:10px">
                                <div style="width:44px;height:44px;border-radius:10px;background:rgba(99,102,241,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="#6366f1"><path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6z"/></svg>
                                </div>
                                <div style="flex:1;min-width:0">
                                    <div style="font-size:14px;font-weight:600;color:var(--fc-text)">{{ $solicitud->archivo->nombre_original }}</div>
                                    <div style="font-size:12px;color:var(--fc-text-muted);margin-top:2px">
                                        {{ strtoupper($solicitud->archivo->extension) }}
                                        · {{ $solicitud->archivo->tamanioFormateado() }}
                                        · v{{ $solicitud->archivo->version }}
                                    </div>
                                    <div style="font-size:12px;color:var(--fc-text-muted);margin-top:2px">
                                        Carpeta: <strong>{{ $solicitud->archivo->carpeta->nombre ?? '—' }}</strong>
                                    </div>
                                </div>
                                @if($solicitud->status === 'Aprobado' && $solicitud->tipo_acceso === 'Descargar')
                                <a href="{{ route('archivos.descargar', $solicitud->archivo) }}" class="fc-btn fc-btn-success fc-btn-sm">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/></svg>
                                    Descargar
                                </a>
                                @endif
                            </div>
                            @elseif($solicitud->carpeta)
                            <div style="display:flex;align-items:center;gap:14px;padding:14px;background:var(--fc-bg);border-radius:10px">
                                <div style="width:44px;height:44px;border-radius:10px;background:rgba(79,70,229,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="#4f46e5"><path d="M20 6h-8l-2-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2z"/></svg>
                                </div>
                                <div style="flex:1">
                                    <div style="font-size:14px;font-weight:600;color:var(--fc-text)">{{ $solicitud->carpeta->nombre }}</div>
                                    <div style="font-size:12px;color:var(--fc-text-muted);margin-top:2px">
                                        <code>{{ $solicitud->carpeta->path }}</code>
                                    </div>
                                </div>
                            </div>
                            @else
                            <p class="fc-desc-empty">El recurso ya no está disponible en el sistema.</p>
                            @endif
                        </div>
                    </div>

                    {{-- Razón (campo correcto: ->razon) --}}
                    <div class="fc-section-card">
                        <div class="fc-section-header">
                            <div class="fc-section-icon" style="background:rgba(245,158,11,.1)">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="#d97706"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg>
                            </div>
                            Justificación de la solicitud
                        </div>
                        <div class="fc-section-body">
                            <p class="fc-desc-text">{{ $solicitud->razon ?? 'Sin justificación especificada.' }}</p>
                        </div>
                    </div>

                    {{-- Comentario del revisor (campo correcto: ->comentario_revisor) --}}
                    @if($solicitud->comentario_revisor)
                    <div class="fc-section-card">
                        <div class="fc-section-header">
                            <div class="fc-section-icon" style="background:{{ $sc['bg'] }}">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="{{ $sc['color'] }}"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg>
                            </div>
                            Respuesta del revisor
                        </div>
                        <div class="fc-section-body">
                            <p class="fc-desc-text" style="color:{{ $sc['color'] }}">{{ $solicitud->comentario_revisor }}</p>
                        </div>
                    </div>
                    @endif

                </div>{{-- /fc-col-main --}}

                {{-- ── Panel lateral ── --}}
                <div class="fc-col-side">
                    <div class="fc-info-card">
                        <div class="fc-info-header">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="#6366f1"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                            Información
                        </div>
                        <div class="fc-info-body">
                            {{-- Relación correcta: ->solicitante --}}
                            <div class="fc-info-row">
                                <span class="fc-info-label">Solicitante</span>
                                <span class="fc-info-val">{{ $solicitud->solicitante->nombre_completo ?? '—' }}</span>
                            </div>
                            <div class="fc-info-row">
                                <span class="fc-info-label">Su empresa</span>
                                <span class="fc-info-val">{{ $solicitud->solicitante->empresa->nombre ?? '—' }}</span>
                            </div>
                            {{-- Empresa objetivo --}}
                            <div class="fc-info-row">
                                <span class="fc-info-label">Empresa objetivo</span>
                                <span class="fc-info-val">{{ $solicitud->empresaObjetivo->nombre ?? '—' }}</span>
                            </div>
                            <div class="fc-info-row">
                                <span class="fc-info-label">Tipo de acceso</span>
                                <span class="fc-badge" style="background:rgba(99,102,241,.08);color:#4f46e5">
                                    {{ $solicitud->tipo_acceso ?? '—' }}
                                </span>
                            </div>
                            <div class="fc-info-row">
                                <span class="fc-info-label">Status</span>
                                <span class="fc-badge" style="background:{{ $sc['bg'] }};color:{{ $sc['color'] }}">
                                    {{ $solicitud->status }}
                                </span>
                            </div>
                            <div class="fc-info-row">
                                <span class="fc-info-label">Solicitado</span>
                                <span class="fc-info-val">{{ $solicitud->created_at?->format('d/m/Y H:i') ?? '—' }}</span>
                            </div>
                            @if($solicitud->status !== 'Pendiente')
                            <div class="fc-info-row">
                                <span class="fc-info-label">Revisado</span>
                                <span class="fc-info-val">{{ $solicitud->updated_at?->format('d/m/Y H:i') ?? '—' }}</span>
                            </div>
                            @if($solicitud->revisor)
                            <div class="fc-info-row">
                                <span class="fc-info-label">Revisado por</span>
                                <span class="fc-info-val">{{ $solicitud->revisor->nombre_completo }}</span>
                            </div>
                            @endif
                            @if($solicitud->caduca_en)
                            <div class="fc-info-row">
                                <span class="fc-info-label">Caduca</span>
                                <span class="fc-info-val" style="color:#d97706">
                                    {{ \Carbon\Carbon::parse($solicitud->caduca_en)->format('d/m/Y') }}
                                </span>
                            </div>
                            @endif
                            @endif
                        </div>
                    </div>

                    <div style="margin-top:16px">
                        <a href="{{ route('solicitudes.index') }}" class="fc-btn fc-btn-outline" style="width:100%;justify-content:center">
                            ← Volver a solicitudes
                        </a>
                    </div>
                </div>

            </div>{{-- /fc-content-cols --}}
        </div>{{-- /fc-content --}}
    </div>
</div>

{{-- Modal Aprobar --}}
<div class="fc-modal-overlay" id="modalAprobarShow">
    <div class="fc-modal">
        <div class="fc-modal-title">Aprobar solicitud #{{ $solicitud->id }}</div>
        <form method="POST" action="{{ route('solicitudes.aprobar', $solicitud) }}">
            @csrf
            <div class="fc-modal-sub">
                <div class="fc-field" style="margin-bottom:12px">
                    <label class="fc-label">Comentario (opcional)</label>
                    <textarea name="comentario_revisor" rows="3" class="fc-input" placeholder="Instrucciones o condiciones de uso..."></textarea>
                </div>
                <div class="fc-field">
                    <label class="fc-label">Caduca el (opcional)</label>
                    <input type="date" name="caduca_en" class="fc-input" min="{{ now()->addDay()->format('Y-m-d') }}">
                    <div class="fc-field-hint">Si no se especifica, el acceso no tiene fecha límite.</div>
                </div>
            </div>
            <div class="fc-modal-btns">
                <button type="button" class="fc-modal-cancel" onclick="document.getElementById('modalAprobarShow').classList.remove('open')">Cancelar</button>
                <button type="submit" class="fc-modal-confirm" style="background:#059669">Aprobar</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Rechazar --}}
<div class="fc-modal-overlay" id="modalRechazarShow">
    <div class="fc-modal">
        <div class="fc-modal-title">Rechazar solicitud #{{ $solicitud->id }}</div>
        <form method="POST" action="{{ route('solicitudes.rechazar', $solicitud) }}">
            @csrf
            <div class="fc-modal-sub">
                <div class="fc-field">
                    <label class="fc-label">Motivo del rechazo <span style="color:#dc2626">*</span></label>
                    <textarea name="comentario_revisor" rows="3" class="fc-input" required placeholder="Indica por qué se rechaza la solicitud..."></textarea>
                    <div class="fc-field-hint">El solicitante verá este mensaje.</div>
                </div>
            </div>
            <div class="fc-modal-btns">
                <button type="button" class="fc-modal-cancel" onclick="document.getElementById('modalRechazarShow').classList.remove('open')">Cancelar</button>
                <button type="submit" class="fc-modal-confirm danger">Rechazar</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('keydown', e => {
    if (e.key === 'Escape')
        document.querySelectorAll('.fc-modal-overlay.open').forEach(m => m.classList.remove('open'));
});
</script>
</x-app-layout>