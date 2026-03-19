<a href="{{ route('carpetas.show', $carpeta) }}" class="fc-folder-card" data-nombre="{{ $carpeta->nombre }}">
    @if($carpeta->es_publico)
        <span class="fc-folder-public">Pública</span>
    @endif
    <div class="fc-folder-icon">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="#4f46e5">
            <path d="M20 6h-8l-2-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2z"/>
        </svg>
    </div>
    <div class="fc-folder-name" title="{{ $carpeta->nombre }}">{{ $carpeta->nombre }}</div>
    <div class="fc-folder-meta">
        {{ $carpeta->hijos->count() }} subcarpeta{{ $carpeta->hijos->count() != 1 ? 's' : '' }}
        · {{ $carpeta->archivos()->where('esta_eliminado', false)->count() }} archivo{{ $carpeta->archivos()->where('esta_eliminado', false)->count() != 1 ? 's' : '' }}
    </div>
</a>
