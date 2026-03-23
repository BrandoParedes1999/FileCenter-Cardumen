<x-app-layout>
<div class="fc-wrapper">

    {{-- Sidebar dinámico con colores de empresa --}}
    @include('components.sidebar')

    <div class="fc-main">

        {{-- Topbar --}}
        <header class="fc-topbar">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#6366f1">
                <path d="M12 7V3H2v18h20V7H12z"/>
            </svg>
            <span class="fc-topbar-title">Empresas del Corporativo</span>
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

        {{-- Contenido --}}
        <div class="fc-content">

            {{-- Flash messages --}}
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

            {{-- Page Header --}}
            <div class="fc-page-header">
                <div>
                    <h1 class="fc-page-title">Empresas</h1>
                    <div class="fc-breadcrumb" style="margin-top:4px">
                        <a href="{{ route('dashboard') }}" class="fc-bread-item">Inicio</a>
                        <span class="fc-bread-sep">›</span>
                        <span class="fc-bread-current">Empresas</span>
                    </div>
                </div>
                <a href="{{ route('empresas.create') }}" class="fc-btn fc-btn-primary">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
                    Nueva Empresa
                </a>
            </div>

            {{-- Grid de tarjetas --}}
            <div class="fc-empresa-grid">

                @foreach($empresas as $emp)
                @php
                    $bg      = $emp->color_primario   ?? '#1B3A6B';
                    $accent  = $emp->color_secundario ?? '#2E5FA3';
                    $logoUrl = asset('images/empresas/'.$emp->logo);
                @endphp

                <div class="fc-empresa-card {{ !$emp->activo ? 'fc-empresa-card--inactiva' : '' }}">

                    {{-- Header con banda de color --}}
                    <div class="fc-empresa-card-header" style="background:{{ $bg }}">

                        <img src="{{ $logoUrl }}"
                             alt="{{ $emp->siglas }}"
                             class="fc-empresa-logo"
                             onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                        <div class="fc-empresa-logo-fallback"
                             style="background:{{ $accent }};display:none;align-items:center;justify-content:center">
                            {{ strtoupper(substr($emp->siglas,0,2)) }}
                        </div>

                        <div class="fc-empresa-header-info">
                            <div class="fc-empresa-siglas">{{ $emp->siglas }}</div>
                            @if($emp->es_corporativo)
                                <span class="fc-badge"
                                      style="background:rgba(255,255,255,0.2);color:#fff;border:1px solid rgba(255,255,255,0.3);font-size:10px;padding:2px 8px;border-radius:999px">
                                    Corporativo
                                </span>
                            @endif
                        </div>

                        @if(!$emp->activo)
                            <span class="fc-badge fc-badge-inactivo fc-empresa-badge-status"
                                  style="background:rgba(239,68,68,.25);color:#fca5a5;border:1px solid rgba(239,68,68,.3)">
                                Inactiva
                            </span>
                        @endif
                    </div>

                    {{-- Body --}}
                    <div class="fc-empresa-card-body">

                        <div class="fc-empresa-nombre">{{ $emp->nombre }}</div>

                        {{-- Paleta de colores --}}
                        <div class="fc-empresa-paleta">
                            @if($emp->color_primario)
                                <div class="fc-empresa-color-dot"
                                     style="background:{{ $emp->color_primario }}"
                                     title="Color primario: {{ $emp->color_primario }}"></div>
                            @endif
                            @if($emp->color_secundario)
                                <div class="fc-empresa-color-dot"
                                     style="background:{{ $emp->color_secundario }}"
                                     title="Color secundario: {{ $emp->color_secundario }}"></div>
                            @endif
                            @if($emp->color_terciario)
                                <div class="fc-empresa-color-dot"
                                     style="background:{{ $emp->color_terciario }}"
                                     title="Color terciario: {{ $emp->color_terciario }}"></div>
                            @endif
                        </div>

                        {{-- Stats --}}
                        <div class="fc-empresa-stats">
                            <div class="fc-empresa-stat">
                                <svg viewBox="0 0 24 24" fill="currentColor" style="width:14px;height:14px;opacity:.5">
                                    <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                                </svg>
                                <span>{{ $emp->usuarios_count }} usuarios</span>
                            </div>
                            <div class="fc-empresa-stat">
                                <svg viewBox="0 0 24 24" fill="currentColor" style="width:14px;height:14px;opacity:.5">
                                    <path d="M20 6h-8l-2-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2z"/>
                                </svg>
                                <span>{{ $emp->carpetas_count }} carpetas</span>
                            </div>
                        </div>

                        {{-- Acciones --}}
                        <div class="fc-empresa-actions">
                            <a href="{{ route('empresas.show', $emp) }}"
                               class="fc-btn fc-btn-outline fc-btn-sm">Ver</a>
                            <a href="{{ route('empresas.edit', $emp) }}"
                               class="fc-btn fc-btn-outline fc-btn-sm">Editar</a>
                            <form method="POST"
                                  action="{{ route('empresas.toggle-activo', $emp) }}"
                                  style="display:inline">
                                @csrf
                                <button type="submit"
                                        class="fc-btn fc-btn-sm {{ $emp->activo ? 'fc-btn-warning' : 'fc-btn-success' }}"
                                        {{ $emp->es_corporativo ? 'disabled title=No se puede desactivar el corporativo' : '' }}>
                                    {{ $emp->activo ? 'Desactivar' : 'Activar' }}
                                </button>
                            </form>
                        </div>

                    </div>{{-- /fc-empresa-card-body --}}
                </div>{{-- /fc-empresa-card --}}

                @endforeach

            </div>{{-- /fc-empresa-grid --}}

        </div>{{-- /fc-content --}}
    </div>{{-- /fc-main --}}
</div>{{-- /fc-wrapper --}}
</x-app-layout>