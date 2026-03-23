@props(['areas', 'link' => '#'])

<div>
    <div class="fc-areas-header">
        <div class="fc-areas-title">Todas las Áreas</div>
        <a href="{{ $link }}" class="fc-areas-link">Ver todas →</a>
    </div>
    <div class="fc-areas-grid">
        @foreach($areas as $area)
            <a href="{{ route('carpetas.index', ['empresa' => $area->id]) }}" class="fc-area-card">
                <div style="display:flex;align-items:center;gap:11px">
                    <div class="fc-area-dot" style="background:{{ $area->color_primario ?? '#4f46e5' }}"></div>
                    <div>
                        <div class="fc-area-name">
                            {{ $area->nombre }}
                            @if($area->es_corporativo)
                                <span style="font-size:9px;background:rgba(27,58,107,0.1);color:#1b3a6b;padding:1px 5px;border-radius:4px;margin-left:3px">CORP</span>
                            @endif
                        </div>
                        <div class="fc-area-meta">
                            {{ $area->total_archivos }} archivos · {{ $area->total_miembros }} miembro{{ $area->total_miembros != 1 ? 's' : '' }}
                        </div>
                    </div>
                </div>
                <div class="fc-area-chevron">›</div>
            </a>
        @endforeach
    </div>
</div>