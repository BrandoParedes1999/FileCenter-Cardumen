<x-app-layout>
<div class="fc-wrapper">

    @include('components.sidebar')

    <div class="fc-main">
        <header class="fc-topbar">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#f59e0b">
                <path d="M9 16h6v-6h4l-7-7-7 7h4zm-4 2h14v2H5z"/>
            </svg>
            <span class="fc-topbar-title">Solicitud de Subida #{{ $solicitudSubida->id }}</span>
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
                <a href="{{ route('solicitudes-subida.index') }}" class="fc-bread-item">⏳ Subidas pendientes</a>
                <span class="fc-bread-sep">›</span>
                <span class="fc-bread-current">Solicitud #{{ $solicitudSubida->id }}</span>
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
                $sc = [
                    'Pendiente' => ['bg' => 'rgba(245,158,11,.1)', 'color' => '#d97706'],
                    'Aprobado'  => ['bg' => 'rgba(5,150,105,.1)',  'color' => '#059669'],
                    'Rechazado' => ['bg' => 'rgba(220,38,38,.1)',  'color' => '#dc2626'],
                ][$solicitudSubida->status] ?? ['bg' => '#f1f5f9', 'color' => '#64748b'];

                $extColors = ['pdf' => '#dc2626', 'docx' => '#2563eb', 'doc' => '#2563eb', 'xlsx' => '#059669', 'xls' => '#059669'];
                $iconColor = $extColors[$solicitudSubida->extension] ?? '#64748b';
            @endphp

            <div class="fc-content-cols">

                {{-- ── Columna principal ── --}}
                <div class="fc-col-main">

                    {{-- Banner de status --}}
                    <div style="background:{{ $sc['bg'] }};border:1px solid {{ $sc['color'] }}33;
                                border-radius:14px;padding:20px 24px;
                                display:flex;align-items:center;gap:16px;margin-bottom:20px">
                        <div style="width:44px;height:44px;border-radius:12px;background:{{ $sc['color'] }}18;
                                    display:flex;align-items:center;justify-content:center;flex-shrink:0">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="{{ $sc['color'] }}">
                                @if($solicitudSubida->status === 'Aprobado')
                                    <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                                @elseif($solicitudSubida->status === 'Rechazado')
                                    <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                                @else
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                                @endif
                            </svg>
                        </div>
                        <div style="flex:1">
                            <div style="font-size:16px;font-weight:700;color:var(--fc-text)">
                                Solicitud {{ $solicitudSubida->status }}
                            </div>
                            <div style="font-size:13px;color:var(--fc-text-muted);margin-top:3px">
                                @if($solicitudSubida->status === 'Pendiente')
                                    Esperando revisión de un administrador
                                @elseif($solicitudSubida->status === 'Aprobado')
                                    Aprobada {{ $solicitudSubida->updated_at?->diffForHumans() }}
                                    @if($solicitudSubida->revisor) por {{ $solicitudSubida->revisor->nombre_completo }} @endif
                                @else
                                    Rechazada {{ $solicitudSubida->updated_at?->diffForHumans() }}
                                    @if($solicitudSubida->revisor) por {{ $solicitudSubida->revisor->nombre_completo }} @endif
                                @endif
                            </div>
                        </div>

                        {{-- Botones de acción --}}
                        @if($solicitudSubida->estaPendiente() && in_array(Auth::user()->rol, ['Superadmin','Aux_QHSE','Admin','Gerente']))
                        <div style="display:flex;gap:8px">
                            <button type="button" class="fc-btn fc-btn-success"
                                    onclick="document.getElementById('modalAprobar').classList.add('open')">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                                Aprobar
                            </button>
                            <button type="button" class="fc-btn fc-btn-danger-outline"
                                    onclick="document.getElementById('modalRechazar').classList.add('open')">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
                                Rechazar
                            </button>
                        </div>
                        @endif
                    </div>

                    {{-- Detalle del archivo --}}
                    <div class="fc-section-card">
                        <div class="fc-section-header">
                            <div class="fc-section-icon" style="background:rgba(99,102,241,.1)">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="#6366f1"><path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6z"/></svg>
                            </div>
                            Archivo solicitado
                        </div>
                        <div class="fc-section-body">
                            <div style="display:flex;align-items:center;gap:16px;padding:14px;
                                        background:var(--fc-bg);border-radius:10px">
                                <div style="width:48px;height:48px;border-radius:12px;
                                            background:rgba(0,0,0,0.05);
                                            display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="{{ $iconColor }}">
                                        <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6z"/>
                                    </svg>
                                </div>
                                <div style="flex:1;min-width:0">
                                    <div style="font-size:15px;font-weight:600;color:var(--fc-text);
                                                word-break:break-word">
                                        {{ $solicitudSubida->nombre_original }}
                                    </div>
                                    <div style="display:flex;gap:10px;margin-top:6px;flex-wrap:wrap">
                                        <span class="fc-badge fc-badge-ext">{{ strtoupper($solicitudSubida->extension) }}</span>
                                        <span style="font-size:12px;color:var(--fc-text-muted)">
                                            {{ $solicitudSubida->tamanioFormateado() }}
                                        </span>
                                        <span style="font-size:12px;color:var(--fc-text-muted)">
                                            {{ $solicitudSubida->tipo_mime ?? '—' }}
                                        </span>
                                    </div>
                                    @if($solicitudSubida->descripcion)
                                    <div style="font-size:13px;color:#475569;margin-top:10px;
                                                padding:10px;background:#fff;border-radius:8px;
                                                border:1px solid var(--fc-border)">
                                        {{ $solicitudSubida->descripcion }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Destino --}}
                    <div class="fc-section-card">
                        <div class="fc-section-header">
                            <div class="fc-section-icon" style="background:rgba(79,70,229,.1)">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="#4f46e5"><path d="M20 6h-8l-2-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2z"/></svg>
                            </div>
                            Carpeta destino
                        </div>
                        <div class="fc-section-body">
                            <div style="display:flex;align-items:center;gap:14px;padding:12px;
                                        background:rgba(79,70,229,.05);border-radius:10px;
                                        border:1px solid rgba(79,70,229,.15)">
                                <div>
                                    <div style="font-size:14px;font-weight:600;color:var(--fc-text)">
                                        {{ $solicitudSubida->carpeta->nombre ?? '—' }}
                                    </div>
                                    <div style="font-size:12px;color:var(--fc-text-muted);margin-top:2px">
                                        <code>{{ $solicitudSubida->carpeta->path ?? '' }}</code>
                                        · {{ $solicitudSubida->carpeta->empresa->nombre ?? '' }}
                                    </div>
                                    <div style="display:flex;gap:6px;margin-top:6px">
                                        @if($solicitudSubida->carpeta?->esSoloLectura())
                                            <span class="fc-badge" style="background:rgba(99,102,241,.1);color:#4f46e5">👁 Solo lectura</span>
                                        @elseif($solicitudSubida->carpeta?->modo_acceso === 'con_descarga')
                                            <span class="fc-badge" style="background:rgba(5,150,105,.1);color:#059669">⬇ Con descarga</span>
                                        @endif
                                        @if($solicitudSubida->carpeta?->requiere_aprobacion_subida)
                                            <span class="fc-badge" style="background:rgba(245,158,11,.1);color:#d97706">⏳ Con aprobación</span>
                                        @endif
                                    </div>
                                </div>
                                <div style="margin-left:auto">
                                    <a href="{{ route('carpetas.show', $solicitudSubida->carpeta) }}"
                                       class="fc-btn fc-btn-outline fc-btn-sm">
                                        Ver carpeta →
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Comentario del revisor (si fue procesada) --}}
                    @if($solicitudSubida->comentario_revisor)
                    <div class="fc-section-card">
                        <div class="fc-section-header">
                            <div class="fc-section-icon" style="background:{{ $sc['bg'] }}">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="{{ $sc['color'] }}"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg>
                            </div>
                            Respuesta del revisor
                        </div>
                        <div class="fc-section-body">
                            <p class="fc-desc-text" style="color:{{ $sc['color'] }}">
                                {{ $solicitudSubida->comentario_revisor }}
                            </p>
                        </div>
                    </div>
                    @endif

                    {{-- Si fue aprobada, link al archivo --}}
                    @if($solicitudSubida->fueAprobada() && $solicitudSubida->archivo)
                    <div class="fc-flash success">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="#059669"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                        Archivo publicado correctamente.
                        <a href="{{ route('archivos.show', $solicitudSubida->archivo) }}"
                           style="color:#059669;font-weight:600;margin-left:6px">
                            Ver archivo →
                        </a>
                    </div>
                    @endif

                </div>

                {{-- ── Panel lateral ── --}}
                <div class="fc-col-side">
                    <div class="fc-info-card">
                        <div class="fc-info-header">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="#6366f1"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                            Información
                        </div>
                        <div class="fc-info-body">
                            <div class="fc-info-row">
                                <span class="fc-info-label">Solicitante</span>
                                <span class="fc-info-val">{{ $solicitudSubida->solicitante->nombre_completo ?? '—' }}</span>
                            </div>
                            <div class="fc-info-row">
                                <span class="fc-info-label">Rol</span>
                                <span class="fc-info-val">{{ $solicitudSubida->solicitante->rol ?? '—' }}</span>
                            </div>
                            <div class="fc-info-row">
                                <span class="fc-info-label">Status</span>
                                <span class="fc-badge" style="background:{{ $sc['bg'] }};color:{{ $sc['color'] }}">
                                    {{ $solicitudSubida->status }}
                                </span>
                            </div>
                            <div class="fc-info-row">
                                <span class="fc-info-label">Solicitado</span>
                                <span class="fc-info-val">{{ $solicitudSubida->created_at?->format('d/m/Y H:i') ?? '—' }}</span>
                            </div>
                            @if($solicitudSubida->revisado_en)
                            <div class="fc-info-row">
                                <span class="fc-info-label">Revisado</span>
                                <span class="fc-info-val">{{ $solicitudSubida->revisado_en?->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="fc-info-row">
                                <span class="fc-info-label">Revisor</span>
                                <span class="fc-info-val">{{ $solicitudSubida->revisor->nombre_completo ?? '—' }}</span>
                            </div>
                            @endif
                            <div class="fc-info-row">
                                <span class="fc-info-label">Hash SHA-256</span>
                                <span class="fc-info-val mono" style="font-size:10px;word-break:break-all">
                                    {{ Str::limit($solicitudSubida->hash_sha256 ?? '—', 20) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div style="margin-top:14px">
                        <a href="{{ route('solicitudes-subida.index') }}"
                           class="fc-btn fc-btn-outline" style="width:100%;justify-content:center">
                            ← Volver a la lista
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- Modal Aprobar --}}
<div class="fc-modal-overlay" id="modalAprobar">
    <div class="fc-modal">
        <div class="fc-modal-title">Aprobar solicitud de subida</div>
        <form method="POST" action="{{ route('solicitudes-subida.aprobar', $solicitudSubida) }}">
            @csrf
            <div class="fc-modal-sub">
                <p style="margin-bottom:12px;font-size:13px;color:#475569">
                    El archivo <strong>"{{ $solicitudSubida->nombre_original }}"</strong>
                    se moverá a la carpeta <strong>{{ $solicitudSubida->carpeta->nombre ?? '' }}</strong>
                    y quedará visible para todos los usuarios con acceso.
                </p>
                <div class="fc-field">
                    <label class="fc-label">Comentario (opcional)</label>
                    <textarea name="comentario_revisor" rows="2" class="fc-input"
                              placeholder="Observaciones..."></textarea>
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
        <form method="POST" action="{{ route('solicitudes-subida.rechazar', $solicitudSubida) }}">
            @csrf
            <div class="fc-modal-sub">
                <div class="fc-field" style="margin-bottom:10px">
                    <label class="fc-label">Motivo del rechazo <span style="color:#dc2626">*</span></label>
                    <textarea name="comentario_revisor" rows="3" class="fc-input" required
                            placeholder="Indica por qué se rechaza este archivo..."></textarea>
                </div>
                <div style="font-size:12px;color:#dc2626;padding:8px 12px;
                            background:rgba(220,38,38,.06);border-radius:8px">
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
document.addEventListener('keydown', e => {
    if (e.key === 'Escape')
        document.querySelectorAll('.fc-modal-overlay.open').forEach(m => m.classList.remove('open'));
});
</script>
</x-app-layout>