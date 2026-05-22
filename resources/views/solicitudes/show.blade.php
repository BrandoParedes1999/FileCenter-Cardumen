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

            @php
                $statusConfig = [
                    'Pendiente' => [
                        'bg'    => 'rgba(245,158,11,.08)',
                        'border'=> 'rgba(217,119,6,.25)',
                        'color' => '#d97706',
                        'icon'  => 'M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z',
                    ],
                    'Aprobado' => [
                        'bg'    => 'rgba(5,150,105,.08)',
                        'border'=> 'rgba(5,150,105,.25)',
                        'color' => '#059669',
                        'icon'  => 'M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z',
                    ],
                    'Rechazado' => [
                        'bg'    => 'rgba(220,38,38,.08)',
                        'border'=> 'rgba(220,38,38,.25)',
                        'color' => '#dc2626',
                        'icon'  => 'M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z',
                    ],
                ];
                $sc        = $statusConfig[$solicitud->status] ?? $statusConfig['Pendiente'];
                $puedeRevisar = $solicitud->status === 'Pendiente'
                    && in_array(Auth::user()->rol, ['Superadmin','Aux_QHSE','Admin','Gerente']);
            @endphp

            <div class="fc-breadcrumb">
                <a href="{{ route('solicitudes.index') }}" class="fc-bread-item">Solicitudes</a>
                <span class="fc-bread-sep">›</span>
                <span class="fc-bread-current">Solicitud #{{ $solicitud->id }}</span>
                <span class="fc-badge" style="margin-left:8px;background:{{ $sc['bg'] }};color:{{ $sc['color'] }};font-size:11px;padding:2px 8px;border-radius:20px">{{ $solicitud->status }}</span>
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

            <div class="fc-content-cols">

                {{-- ── Left column ───────────────────────────────────────── --}}
                <div class="fc-col-main">

                    {{-- 1. Status banner (pure info — no action buttons) --}}
                    <div style="background:{{ $sc['bg'] }};border:1px solid {{ $sc['border'] }};border-radius:14px;padding:20px 24px;display:flex;align-items:center;gap:16px;margin-bottom:20px">
                        <div style="width:44px;height:44px;border-radius:12px;background:{{ $sc['color'] }}18;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="{{ $sc['color'] }}"><path d="{{ $sc['icon'] }}"/></svg>
                        </div>
                        <div style="flex:1;min-width:0">
                            <div style="font-size:16px;font-weight:700;color:var(--fc-text)">
                                Solicitud {{ $solicitud->status }}
                            </div>
                            <div style="font-size:13px;color:var(--fc-text-muted);margin-top:3px">
                                @if($solicitud->status === 'Pendiente')
                                    Esperando revisión de un administrador
                                @elseif($solicitud->status === 'Aprobado')
                                    Aprobada {{ $solicitud->updated_at?->diffForHumans() }}
                                    @if($solicitud->revisor) · por {{ $solicitud->revisor->nombre_completo }} @endif
                                    @if($solicitud->caduca_en)
                                        · Caduca {{ \Carbon\Carbon::parse($solicitud->caduca_en)->format('d/m/Y') }}
                                    @endif
                                @else
                                    Rechazada {{ $solicitud->updated_at?->diffForHumans() }}
                                    @if($solicitud->revisor) · por {{ $solicitud->revisor->nombre_completo }} @endif
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- 2. Recurso solicitado --}}
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
                                        &middot; {{ $solicitud->archivo->tamanioFormateado() }}
                                        &middot; v{{ $solicitud->archivo->version }}
                                    </div>
                                    <div style="font-size:12px;color:var(--fc-text-muted);margin-top:2px">
                                        Carpeta: <strong>{{ $solicitud->archivo->carpeta->nombre ?? '—' }}</strong>
                                    </div>
                                </div>
                                @if($solicitud->status === 'Aprobado')
                                <div style="display:flex;flex-direction:column;gap:6px;flex-shrink:0">
                                    @if($solicitud->tipo_acceso === 'Descargar')
                                    <a href="{{ route('archivos.descargar', $solicitud->archivo) }}" class="fc-btn fc-btn-success fc-btn-sm">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/></svg>
                                        Descargar
                                    </a>
                                    @endif
                                    <a href="{{ route('archivos.ver', $solicitud->archivo) }}" class="fc-btn fc-btn-outline fc-btn-sm">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                                        Ver archivo
                                    </a>
                                </div>
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
                                @if($solicitud->status === 'Aprobado')
                                <a href="{{ route('carpetas.show', $solicitud->carpeta) }}" class="fc-btn fc-btn-success fc-btn-sm">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M20 6h-8l-2-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2z"/></svg>
                                    Ir a la carpeta
                                </a>
                                @endif
                            </div>

                            @else
                            <div style="display:flex;align-items:center;gap:14px;padding:14px;background:var(--fc-bg);border-radius:10px">
                                <div style="width:44px;height:44px;border-radius:10px;background:rgba(100,116,139,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="#64748b"><path d="M12 7V3H2v18h20V7H12zM6 19H4v-2h2v2zm0-4H4v-2h2v2zm0-4H4V9h2v2zm0-4H4V5h2v2zm4 12H8v-2h2v2zm0-4H8v-2h2v2zm0-4H8V9h2v2zm0-4H8V5h2v2zm10 12h-8v-2h2v-2h-2v-2h2v-2h-2V9h8v10zm-2-8h-2v2h2v-2zm0 4h-2v2h2v-2z"/></svg>
                                </div>
                                <div style="flex:1">
                                    <div style="font-size:14px;font-weight:600;color:var(--fc-text)">
                                        Acceso general a {{ $solicitud->empresaObjetivo->nombre ?? 'la empresa' }}
                                    </div>
                                    <div style="font-size:12px;color:var(--fc-text-muted);margin-top:2px">
                                        Sin recurso específico — acceso a todos los recursos de la empresa
                                    </div>
                                </div>
                                @if($solicitud->status === 'Aprobado')
                                <a href="{{ route('carpetas.index') }}" class="fc-btn fc-btn-success fc-btn-sm">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
                                    Ver mis carpetas
                                </a>
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- 3. Justificación --}}
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

                    {{-- 4. Comentario del revisor (si existe) --}}
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

                    {{-- 5. [Admin + Pendiente only] Tomar decisión --}}
                    @if($puedeRevisar)
                    <div class="fc-section-card" style="border:1.5px solid rgba(99,102,241,.2)">
                        <div class="fc-section-header">
                            <div class="fc-section-icon" style="background:rgba(99,102,241,.1)">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="#6366f1"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 14.5v-9l6 4.5-6 4.5z"/></svg>
                            </div>
                            Tomar decisión
                        </div>
                        <div class="fc-section-body">
                            <p style="font-size:13px;color:var(--fc-text-muted);margin:0 0 14px">
                                Revisa la información del solicitante y del recurso, y toma una decisión.
                            </p>
                            <div style="display:flex;gap:10px">
                                <button type="button"
                                        class="fc-btn fc-btn-success"
                                        onclick="document.getElementById('modalAprobarShow').classList.add('open')">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                                    Aprobar solicitud
                                </button>
                                <button type="button"
                                        class="fc-btn fc-btn-danger-outline"
                                        onclick="document.getElementById('modalRechazarShow').classList.add('open')">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
                                    Rechazar solicitud
                                </button>
                            </div>
                        </div>
                    </div>
                    @endif

                </div>{{-- /fc-col-main --}}

                {{-- ── Right column ──────────────────────────────────────── --}}
                <div class="fc-col-side">
                    <div class="fc-info-card">
                        <div class="fc-info-header">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="#6366f1"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                            Información
                        </div>
                        <div class="fc-info-body">

                            {{-- Solicitante --}}
                            @if($solicitud->solicitante)
                            <div style="padding:12px;background:var(--fc-bg);border-radius:10px;margin-bottom:12px">
                                <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px">
                                    <div style="width:36px;height:36px;border-radius:50%;background:rgba(99,102,241,.15);display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:#6366f1;flex-shrink:0">
                                        {{ strtoupper(substr($solicitud->solicitante->nombre,0,1)) }}{{ strtoupper(substr($solicitud->solicitante->paterno,0,1)) }}
                                    </div>
                                    <div style="min-width:0">
                                        <div style="font-size:13px;font-weight:600;color:var(--fc-text)">{{ $solicitud->solicitante->nombre_completo }}</div>
                                        <div style="font-size:11px;color:var(--fc-text-muted);margin-top:1px">{{ $solicitud->solicitante->rol }}</div>
                                    </div>
                                </div>
                                <div style="font-size:11px;color:var(--fc-text-muted);word-break:break-all">
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor" style="vertical-align:middle;margin-right:4px"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
                                    {{ $solicitud->solicitante->email }}
                                </div>
                                @if($solicitud->solicitante->departamento)
                                <div style="font-size:11px;color:var(--fc-text-muted);margin-top:3px">
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor" style="vertical-align:middle;margin-right:4px"><path d="M12 7V3H2v18h20V7H12zM6 19H4v-2h2v2zm0-4H4v-2h2v2zm0-4H4V9h2v2zm0-4H4V5h2v2zm4 12H8v-2h2v2zm0-4H8v-2h2v2zm0-4H8V9h2v2zm0-4H8V5h2v2zm10 12h-8v-2h2v-2h-2v-2h2v-2h-2V9h8v10zm-2-8h-2v2h2v-2zm0 4h-2v2h2v-2z"/></svg>
                                    {{ $solicitud->solicitante->departamento }}
                                </div>
                                @endif
                            </div>
                            @else
                            <div class="fc-info-row">
                                <span class="fc-info-label">Solicitante</span>
                                <span class="fc-info-val" style="color:#94a3b8;font-style:italic">Usuario eliminado</span>
                            </div>
                            @endif

                            <div class="fc-info-row">
                                <span class="fc-info-label">Empresa solicitante</span>
                                <span class="fc-info-val">{{ $solicitud->solicitante->empresa->nombre ?? '—' }}</span>
                            </div>
                            <div class="fc-info-row">
                                <span class="fc-info-label">Empresa objetivo</span>
                                <span class="fc-info-val">{{ $solicitud->empresaObjetivo->nombre ?? '—' }}</span>
                            </div>

                            <div style="height:1px;background:var(--fc-border,#e2e8f0);margin:10px 0"></div>

                            <div class="fc-info-row">
                                <span class="fc-info-label">Tipo de acceso</span>
                                <span class="fc-badge" style="background:rgba(99,102,241,.08);color:#4f46e5">
                                    {{ $solicitud->tipo_acceso ?? '—' }}
                                </span>
                            </div>
                            <div class="fc-info-row">
                                <span class="fc-info-label">Estado</span>
                                <span class="fc-badge" style="background:{{ $sc['bg'] }};color:{{ $sc['color'] }}">
                                    {{ $solicitud->status }}
                                </span>
                            </div>
                            <div class="fc-info-row">
                                <span class="fc-info-label">Solicitado el</span>
                                <span class="fc-info-val">{{ $solicitud->created_at?->format('d/m/Y H:i') ?? '—' }}</span>
                            </div>

                            @if($solicitud->status !== 'Pendiente')
                            <div style="height:1px;background:var(--fc-border,#e2e8f0);margin:10px 0"></div>
                            <div class="fc-info-row">
                                <span class="fc-info-label">Revisado el</span>
                                <span class="fc-info-val">{{ $solicitud->revisado_en?->format('d/m/Y H:i') ?? $solicitud->updated_at?->format('d/m/Y H:i') ?? '—' }}</span>
                            </div>
                            @if($solicitud->revisor)
                            <div class="fc-info-row">
                                <span class="fc-info-label">Revisado por</span>
                                <span class="fc-info-val">{{ $solicitud->revisor->nombre_completo }}</span>
                            </div>
                            @endif
                            @if($solicitud->caduca_en)
                            <div class="fc-info-row">
                                <span class="fc-info-label">Caduca el</span>
                                <span class="fc-info-val" style="color:#d97706;font-weight:600">
                                    {{ \Carbon\Carbon::parse($solicitud->caduca_en)->format('d/m/Y') }}
                                </span>
                            </div>
                            @endif
                            @endif

                        </div>
                    </div>

                    <div style="margin-top:16px">
                        <a href="{{ route('solicitudes.index') }}"
                           class="fc-btn fc-btn-outline"
                           style="width:100%;justify-content:center">
                            ← Volver a solicitudes
                        </a>
                    </div>
                </div>{{-- /fc-col-side --}}

            </div>{{-- /fc-content-cols --}}
        </div>{{-- /fc-content --}}
    </div>{{-- /fc-main --}}
</div>{{-- /fc-wrapper --}}

{{-- ── Modals (only rendered when review is possible) ─────────────────── --}}
@if($puedeRevisar)

<div class="fc-modal-overlay" id="modalAprobarShow">
    <div class="fc-modal">
        <div class="fc-modal-title">Aprobar solicitud #{{ $solicitud->id }}</div>
        <form method="POST" action="{{ route('solicitudes.aprobar', ['solicitud' => $solicitud->id]) }}">
            @csrf
            <div class="fc-modal-sub">
                <div class="fc-field" style="margin-bottom:12px">
                    <label class="fc-label">Comentario (opcional)</label>
                    <textarea name="comentario_revisor" rows="3" class="fc-input"
                              placeholder="Instrucciones o condiciones de uso..."></textarea>
                </div>
                <div class="fc-field">
                    <label class="fc-label">Caduca el (opcional)</label>
                    <input type="date" name="caduca_en" class="fc-input"
                           min="{{ now()->addDay()->format('Y-m-d') }}">
                    <div class="fc-field-hint">Si no se especifica, el acceso no tiene fecha límite.</div>
                </div>
            </div>
            <div class="fc-modal-btns">
                <button type="button" class="fc-modal-cancel"
                        onclick="document.getElementById('modalAprobarShow').classList.remove('open')">
                    Cancelar
                </button>
                <button type="submit" class="fc-modal-confirm" style="background:#059669">Aprobar</button>
            </div>
        </form>
    </div>
</div>

<div class="fc-modal-overlay" id="modalRechazarShow">
    <div class="fc-modal">
        <div class="fc-modal-title">Rechazar solicitud #{{ $solicitud->id }}</div>
        <form method="POST" action="{{ route('solicitudes.rechazar', ['solicitud' => $solicitud->id]) }}">
            @csrf
            <div class="fc-modal-sub">
                <div class="fc-field">
                    <label class="fc-label">Motivo del rechazo <span style="color:#dc2626">*</span></label>
                    <textarea name="comentario_revisor" rows="3" class="fc-input" required
                              placeholder="Indica por qué se rechaza la solicitud..."></textarea>
                    <div class="fc-field-hint">El solicitante verá este mensaje.</div>
                </div>
            </div>
            <div class="fc-modal-btns">
                <button type="button" class="fc-modal-cancel"
                        onclick="document.getElementById('modalRechazarShow').classList.remove('open')">
                    Cancelar
                </button>
                <button type="submit" class="fc-modal-confirm danger">Rechazar</button>
            </div>
        </form>
    </div>
</div>

@endif

<script>
document.addEventListener('keydown', e => {
    if (e.key === 'Escape')
        document.querySelectorAll('.fc-modal-overlay.open').forEach(m => m.classList.remove('open'));
});
</script>
</x-app-layout>
