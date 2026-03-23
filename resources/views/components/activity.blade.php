@props(['activities'])

<div class="fc-activity-card">
    <div class="fc-activity-title">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="#22c55e">
            <path d="M3.5 18.49l6-6.01 4 4L22 6.92l-1.41-1.41-7.09 7.97-4-4L2 16.99z"/>
        </svg>
        Actividad Reciente
    </div>

    @forelse($activities as $act)
        @php
            $mapa = [
                'subir'            => ['bg-up',   'icon-up',   'M9 16h6v-6h4l-7-7-7 7h4zm-4 2h14v2H5z',         'subió'],
                'descargar'        => ['bg-down', 'icon-down', 'M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z',   'descargó'],
                'crear_carpeta'    => ['bg-plus', 'icon-plus', 'M19 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-2 10h-4v4h-2v-4H7v-2h4V7h2v4h4v2z', 'creó carpeta'],
                'eliminar'         => ['bg-down', 'icon-down', 'M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z', 'eliminó'],
                'restaurar_version'=> ['bg-plus', 'icon-plus', 'M13 3c-4.97 0-9 4.03-9 9H1l3.89 3.89.07.14L9 12H6c0-3.87 3.13-7 7-7s7 3.13 7 7-3.13 7-7 7c-1.93 0-3.68-.79-4.94-2.06l-1.42 1.42C8.27 19.99 10.51 21 13 21c4.97 0 9-4.03 9-9s-4.03-9-9-9z', 'restauró versión'],
            ];
            [$bgClass, $iconClass, $path, $textoAccion] = $mapa[$act->accion] ?? ['bg-plus','icon-plus','M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10z',$act->accion];
        @endphp
        <div class="fc-act-item">
            <div class="fc-act-icon {{ $bgClass }}">
                <svg width="13" height="13" viewBox="0 0 24 24" class="{{ $iconClass }}">
                    <path d="{{ $path }}"/>
                </svg>
            </div>
            <div style="flex:1;min-width:0">
                <div class="fc-act-name">
                    <strong>{{ $act->usuario?->nombre ?? 'Sistema' }} {{ $act->usuario?->paterno ?? '' }}</strong>
                    {{ $textoAccion }}
                </div>
                <div class="fc-act-file">{{ Str::limit($act->detalles ?? '—', 38) }}</div>
                <div class="fc-act-time">{{ $act->created_at?->diffForHumans() ?? '—' }}</div>
            </div>
        </div>
    @empty
        <div style="padding:20px;text-align:center;font-size:13px;color:#94a3b8">
            Sin actividad reciente registrada
        </div>
    @endforelse
</div>