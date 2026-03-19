<a href="{{ route('carpetas.show', $carpeta) }}" class="fc-folder-row" data-nombre="{{ $carpeta->nombre }}">
    <div class="fc-folder-row-icon">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="#4f46e5">
            <path d="M20 6h-8l-2-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2z"/>
        </svg>
    </div>
    <div class="fc-folder-row-name">{{ $carpeta->nombre }}</div>
    <div class="fc-folder-row-meta">
        {{ $carpeta->hijos->count() }} subcarpetas · {{ $carpeta->archivos()->where('esta_eliminado', false)->count() }} archivos
    </div>
    @if($carpeta->es_publico)
        <span class="fc-folder-row-badge">Pública</span>
    @endif
    <div class="fc-folder-row-chevron">›</div>
</a>