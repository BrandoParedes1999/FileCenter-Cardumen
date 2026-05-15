@php
    $color   = $carpeta->empresa?->color_primario ?? '#4f46e5';
    $colorBg = $color . '15';
    $nArch   = $carpeta->archivos_count ?? 0;
    $nHijos  = $carpeta->hijos->count();
@endphp

<a href="{{ route('carpetas.show', $carpeta) }}"
   class="fc-folder-card2"
   data-nombre="{{ strtolower($carpeta->nombre) }}"
   data-archivos="{{ $nArch }}"
   style="--fc-empresa: {{ $color }};">

    {{-- Franja de color superior --}}
    <div class="fc-card2-stripe"></div>

    <div class="fc-card2-body">
        {{-- Ícono --}}
        <div class="fc-card2-icon" style="background: {{ $colorBg }}">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="{{ $color }}">
                <path d="M20 6h-8l-2-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2z"/>
            </svg>
        </div>

        {{-- Nombre --}}
        <div class="fc-card2-name" title="{{ $carpeta->nombre }}">{{ $carpeta->nombre }}</div>

        {{-- Badges de conteo --}}
        <div class="fc-card2-counts">
            <span class="fc-card2-pill" style="background:{{ $color }}12;color:{{ $color }}">
                <svg width="9" height="9" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6z"/>
                </svg>
                {{ $nArch }} archivo{{ $nArch != 1 ? 's' : '' }}
            </span>
            @if($nHijos > 0)
            <span class="fc-card2-pill" style="background:rgba(100,116,139,.1);color:#64748b">
                <svg width="9" height="9" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M20 6h-8l-2-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2z"/>
                </svg>
                {{ $nHijos }}
            </span>
            @endif
        </div>

        {{-- Badges de estado --}}
        @if($carpeta->es_publico || $carpeta->requiere_aprobacion_subida || $carpeta->modo_acceso === 'restringido')
        <div class="fc-card2-badges">
            @if($carpeta->es_publico)
            <span class="fc-card2-status green">Pública</span>
            @endif
            @if($carpeta->requiere_aprobacion_subida)
            <span class="fc-card2-status amber">Aprobación</span>
            @endif
            @if($carpeta->modo_acceso === 'restringido')
            <span class="fc-card2-status red">Restringida</span>
            @endif
        </div>
        @endif
    </div>
</a>
