@php
    $color  = $carpeta->empresa?->color_primario ?? '#4f46e5';
    $nArch  = $carpeta->archivos_count ?? 0;
    $nHijos = $carpeta->hijos->count();
@endphp

<a href="{{ route('carpetas.show', $carpeta) }}"
   class="fc-folder-row2"
   data-nombre="{{ strtolower($carpeta->nombre) }}"
   data-archivos="{{ $nArch }}"
   style="--fc-empresa: {{ $color }}; border-left: 3px solid {{ $color }}">

    {{-- Ícono --}}
    <div class="fc-row2-icon" style="background:{{ $color }}15">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="{{ $color }}">
            <path d="M20 6h-8l-2-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2z"/>
        </svg>
    </div>

    {{-- Nombre --}}
    <div class="fc-row2-name">{{ $carpeta->nombre }}</div>

    {{-- Meta: subcarpetas --}}
    @if($nHijos > 0)
    <span class="fc-row2-meta">{{ $nHijos }} sub</span>
    @endif

    {{-- Archivos --}}
    <span class="fc-row2-meta" style="color:{{ $color }}">
        {{ $nArch }} archivo{{ $nArch != 1 ? 's' : '' }}
    </span>

    {{-- Status badges --}}
    @if($carpeta->es_publico)
    <span class="fc-card2-status green">Pública</span>
    @endif
    @if($carpeta->requiere_aprobacion_subida)
    <span class="fc-card2-status amber">Aprobación</span>
    @endif
    @if($carpeta->modo_acceso === 'restringido')
    <span class="fc-card2-status red">Restringida</span>
    @endif

    <div class="fc-row2-chevron">›</div>
</a>
