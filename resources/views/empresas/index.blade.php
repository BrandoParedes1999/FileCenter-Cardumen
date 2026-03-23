<x-app-layout>
    <div class="fc-wrapper">
        @include('components.sidebar')
    {{-- Si necesitas pasar el título, puedes hacerlo con un slot con nombre (opcional si tu layout lo soporta) --}}
    <x-slot name="title">Empresas</x-slot>
<div class="fc-page-header">
    <div>
        <h1 class="fc-page-title">Empresas</h1>
        <div class="fc-breadcrumb">
            <a href="{{ route('dashboard') }}">Inicio</a>
            <span>›</span>
            <span>Empresas</span>
        </div>
    </div>
    <a href="{{ route('empresas.create') }}" class="fc-btn fc-btn-primary">
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
        Nueva Empresa
    </a>
</div>

@if(session('success'))
    <div class="fc-flash fc-flash-success">{{ session('success') }}</div>
@endif
@if($errors->any())
    <div class="fc-flash fc-flash-danger">{{ $errors->first() }}</div>
@endif

{{-- Grid de tarjetas --}}
<div class="fc-empresa-grid">
    @foreach($empresas as $emp)
    @php
        $bg      = $emp->color_primario   ?? '#1B3A6B';
        $accent  = $emp->color_secundario ?? '#2E5FA3';
        $logoUrl = asset('images/empresas/'.$emp->logo);
    @endphp
    <div class="fc-empresa-card {{ !$emp->activo ? 'fc-empresa-card--inactiva' : '' }}">

        {{-- Banda de color superior --}}
        <div class="fc-empresa-card-header" style="background:{{ $bg }}">
            <img src="{{ $logoUrl }}"
                 alt="{{ $emp->siglas }}"
                 class="fc-empresa-logo"
                 onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
            <div class="fc-empresa-logo-fallback" style="background:{{ $accent }};display:none">
                {{ strtoupper(substr($emp->siglas,0,2)) }}
            </div>

            <div class="fc-empresa-header-info">
                <div class="fc-empresa-siglas">{{ $emp->siglas }}</div>
                @if($emp->es_corporativo)
                    <span class="fc-badge" style="background:rgba(255,255,255,0.2);color:#fff;border:1px solid rgba(255,255,255,0.3)">
                        Corporativo
                    </span>
                @endif
            </div>

            @if(!$emp->activo)
                <span class="fc-badge fc-badge-inactivo fc-empresa-badge-status">Inactiva</span>
            @endif
        </div>

        {{-- Contenido --}}
        <div class="fc-empresa-card-body">
            <div class="fc-empresa-nombre">{{ $emp->nombre }}</div>

            {{-- Paleta de colores --}}
            <div class="fc-empresa-paleta">
                @if($emp->color_primario)
                    <div class="fc-empresa-color-dot" style="background:{{ $emp->color_primario }}"
                         title="Color primario: {{ $emp->color_primario }}"></div>
                @endif
                @if($emp->color_secundario)
                    <div class="fc-empresa-color-dot" style="background:{{ $emp->color_secundario }}"
                         title="Color secundario: {{ $emp->color_secundario }}"></div>
                @endif
                @if($emp->color_terciario)
                    <div class="fc-empresa-color-dot" style="background:{{ $emp->color_terciario }}"
                         title="Color terciario: {{ $emp->color_terciario }}"></div>
                @endif
            </div>

            {{-- Stats --}}
            <div class="fc-empresa-stats">
                <div class="fc-empresa-stat">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
                    <span>{{ $emp->usuarios_count }} usuarios</span>
                </div>
                <div class="fc-empresa-stat">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M20 6h-8l-2-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2z"/></svg>
                    <span>{{ $emp->carpetas_count }} carpetas</span>
                </div>
            </div>

            {{-- Acciones --}}
            <div class="fc-empresa-actions">
                <a href="{{ route('empresas.show', $emp) }}" class="fc-btn fc-btn-outline fc-btn-sm">Ver</a>
                <a href="{{ route('empresas.edit', $emp) }}" class="fc-btn fc-btn-outline fc-btn-sm">Editar</a>
                <form method="POST" action="{{ route('empresas.toggle-activo', $emp) }}" style="display:inline">
                    @csrf
                    <button type="submit"
                        class="fc-btn fc-btn-sm {{ $emp->activo ? 'fc-btn-warning' : 'fc-btn-success' }}"
                        {{ $emp->es_corporativo ? 'disabled title=No se puede desactivar el corporativo' : '' }}>
                        {{ $emp->activo ? 'Desactivar' : 'Activar' }}
                    </button>
                </form>
            </div>
        </div>

    </div>
    @endforeach
</div>
</x-app-layout>
