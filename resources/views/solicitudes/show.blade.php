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

            {{-- Breadcrumb --}}
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
            @if(session('error'))
            <div class="fc-flash error">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="#dc2626"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                {{ session('error') }}
            </div>
            @endif

            @php
                $estadoConfig = [
                    'pendiente' => ['bg'=>'rgba(245,158,11,.1)', 'color'=>'#d97706', 'label'=>'Pendiente', 'icon'=>'M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z'],
                    'aprobada'  => ['bg'=>'rgba(5,150,105,.1)',  'color'=>'#059669', 'label'=>'Aprobada',  'icon'=>'M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z'],
                    'rechazada' => ['bg'=>'rgba(220,38,38,.1)',  'color'=>'#dc2626', 'label'=>'Rechazada', 'icon'=>'M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z'],
                ];
                $ec = $estadoConfig[$solicitud->estado] ?? $estadoConfig['pendiente'];
            @endphp

            <div class="fc-content-cols">

                {{-- ── Panel principal ── --}}
                <div class="fc-col-main">

                    {{-- Estado banner --}}
                    <div style="background:{{ $ec['bg'] }};border:1px solid {{ $ec['color'] }}33;border-radius:14px;padding:20px 24px;display:flex;align-items:center;gap:16px">
                        <div style="width:44px;height:44px;border-radius:12px;background:{{ $ec['color'] }}18;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="{{ $ec['color'] }}"><path d="{{ $ec['icon'] }}"/></svg>
                        </div>
                        <div style="flex:1">
                            <div style="font-size:16px;font-weight:700;color:var(--fc-text)">
                                Solicitud {{ $ec['label'] }}
                            </div>
                            <div style="font-size:13px;color:var(--fc-text-muted);margin-top:3px">
                                @if($solicitud->estado === 'pendiente')
                                    Esperando revisión de un administrador
                                @elseif($solicitud->estado === 'aprobada')
                                    Aprobada {{ $solicitud->updated_at?->diffForHumans() ?? '' }}
                                    @if($solicitud->revisor) por {{ $solicitud->revisor->nombre_completo }} @endif
                                @else
                                    Rechazada {{ $solicitud->updated_at?->diffForHumans() ?? '' }}
                                    @if($solicitud->revisor) por {{ $solicitud->revisor->nombre_completo }} @endif
                                @endif
                            </div>
                        </div>
                        {{-- Acciones admin --}}
                        @if($solicitud->estado === 'pendiente' && in_array(Auth::user()->rol, ['Superadmin','Aux_QHSE','Admin','Gerente']))
                        <div style="display:flex;gap:8px">
                            <form method="POST" action="{{ route('solicitudes.aprobar', $solicitud) }}">
                                @csrf
                                <button type="submit" class="fc-btn fc-btn-success">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                                    Aprobar
                                </button>
                            </form>
                            <form method="POST" action="{{ route('solicitudes.rechazar', $solicitud) }}">
                                @csrf
                                <button type="submit" class="fc-btn fc-btn-danger-outline">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
                                    Rechazar
                                </button>
                            </form>
                        </div>
                        @endif
                    </div>

                    {{-- Archivo solicitado --}}
                    <div class="fc-section-card">
                        <div class="fc-section-header">
                            <div class="fc-section-icon" style="background:rgba(99,102,241,.1)">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="#6366f1"><path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6z"/></svg>
                            </div>
                            Archivo solicitado
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
                                        Carpeta: <strong>{{ $solicitud->archivo->carpeta->nombre }}</strong>
                                    </div>
                                </div>
                                @if($solicitud->estado === 'aprobada')
                                <a href="{{ route('archivos.descargar', $solicitud->archivo) }}" class="fc-btn fc-btn-success fc-btn-sm">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/></svg>
                                    Descargar
                                </a>
                                @endif
                            </div>
                            @else
                            <p class="fc-desc-empty">El archivo ya no está disponible en el sistema.</p>
                            @endif
                        </div>
                    </div>

                    {{-- Motivo --}}
                    <div class="fc-section-card">
                        <div class="fc-section-header">
                            <div class="fc-section-icon" style="background:rgba(245,158,11,.1)">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="#d97706"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg>
                            </div>
                            Motivo de la solicitud
                        </div>
                        <div class="fc-section-body">
                            <p class="fc-desc-text">{{ $solicitud->motivo ?? 'Sin motivo especificado.' }}</p>
                        </div>
                    </div>

                    {{-- Nota del revisor (si existe) --}}
                    @if($solicitud->nota_revision)
                    <div class="fc-section-card">
                        <div class="fc-section-header">
                            <div class="fc-section-icon" style="background:{{ $ec['bg'] }}">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="{{ $ec['color'] }}"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg>
                            </div>
                            Nota del revisor
                        </div>
                        <div class="fc-section-body">
                            <p class="fc-desc-text" style="color:{{ $ec['color'] }}">{{ $solicitud->nota_revision }}</p>
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
                            <div class="fc-info-row">
                                <span class="fc-info-label">Solicitante</span>
                                <span class="fc-info-val">{{ $solicitud->usuario->nombre_completo ?? '—' }}</span>
                            </div>
                            <div class="fc-info-row">
                                <span class="fc-info-label">Empresa</span>
                                <span class="fc-info-val">{{ $solicitud->usuario->empresa->nombre ?? '—' }}</span>
                            </div>
                            <div class="fc-info-row">
                                <span class="fc-info-label">Estado</span>
                                <span class="fc-badge" style="background:{{ $ec['bg'] }};color:{{ $ec['color'] }}">{{ $ec['label'] }}</span>
                            </div>
                            <div class="fc-info-row">
                                <span class="fc-info-label">Solicitado</span>
                                <span class="fc-info-val">{{ $solicitud->created_at?->format('d/m/Y H:i') ?? '—' }}</span>
                            </div>
                            @if($solicitud->estado !== 'pendiente')
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
                            @endif
                        </div>
                    </div>

                    <div style="margin-top:16px">
                        <a href="{{ route('solicitudes.index') }}" class="fc-btn fc-btn-outline" style="width:100%;justify-content:center">
                            ← Volver a solicitudes
                        </a>
                    </div>

                </div>{{-- /fc-col-side --}}
            </div>{{-- /fc-content-cols --}}

        </div>{{-- /fc-content --}}
    </div>{{-- /fc-main --}}
</div>{{-- /fc-wrapper --}}
</x-app-layout>