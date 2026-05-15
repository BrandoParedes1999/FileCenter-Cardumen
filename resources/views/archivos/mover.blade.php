<x-app-layout>
<style>
.mv-wrap {
    max-width: 560px; margin: 32px auto; padding: 0 16px;
}
.mv-card {
    background: #fff; border: 1px solid #e2e8f0;
    border-radius: 16px; padding: 28px 32px;
    box-shadow: 0 2px 8px rgba(0,0,0,.05);
}
.mv-title {
    font-size: 17px; font-weight: 700; color: #1e293b; margin-bottom: 4px;
}
.mv-sub {
    font-size: 13px; color: #64748b; margin-bottom: 24px;
}
.mv-file-row {
    display: flex; align-items: center; gap: 12px;
    background: #f8fafc; border: 1px solid #e2e8f0;
    border-radius: 10px; padding: 12px 14px; margin-bottom: 24px;
}
.mv-file-name { font-size: 14px; font-weight: 600; color: #1e293b; }
.mv-file-meta { font-size: 11px; color: #94a3b8; margin-top: 2px; }
.mv-label {
    display: block; font-size: 11px; font-weight: 700; color: #64748b;
    text-transform: uppercase; letter-spacing: .05em; margin-bottom: 8px;
}
.mv-select {
    width: 100%; padding: 10px 12px; border-radius: 8px;
    border: 1px solid #e2e8f0; background: #f8fafc;
    font-size: 13px; color: #1e293b; outline: none;
    transition: border-color .15s, background .15s;
}
.mv-select:focus { border-color: #6366f1; background: #fff; }
.mv-btns { display: flex; gap: 10px; justify-content: flex-end; margin-top: 24px; }
.mv-error { font-size: 12px; color: #dc2626; margin-top: 6px; }

.dark .mv-card  { background: #13111f; border-color: #1e1b4b; }
.dark .mv-title { color: #e0e7ff; }
.dark .mv-sub   { color: #a5b4fc; }
.dark .mv-file-row { background: #1a1830; border-color: #1e1b4b; }
.dark .mv-file-name { color: #e0e7ff; }
.dark .mv-select { background: #1e1b4b; border-color: #2d2a5e; color: #e0e7ff; }
</style>

<div class="fc-wrapper">
    @include('components.sidebar')

    <div class="fc-main" style="display:flex;flex-direction:column">
        <header class="fc-topbar">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="#6366f1">
                <path d="M20 6h-8l-2-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2z"/>
            </svg>
            <span class="fc-topbar-title">Mover archivo</span>
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

        <div class="mv-wrap">
            <div class="mv-card">
                <div class="mv-title">Mover archivo</div>
                <div class="mv-sub">Selecciona la carpeta de destino. El archivo físico se trasladará automáticamente.</div>

                {{-- Archivo actual --}}
                <div class="mv-file-row">
                    @php $color = $archivo->colorExtension()['color']; @endphp
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="{{ $color }}">
                        <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6z"/>
                    </svg>
                    <div>
                        <div class="mv-file-name">{{ $archivo->nombre_original }}</div>
                        <div class="mv-file-meta">
                            Actualmente en: <strong>{{ $archivo->carpeta->nombre }}</strong>
                            &nbsp;·&nbsp;{{ $archivo->tamanioFormateado() }}
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('archivos.mover', $archivo) }}">
                    @csrf

                    <label class="mv-label" for="carpeta_destino_id">Carpeta de destino</label>
                    <select name="carpeta_destino_id" id="carpeta_destino_id" class="mv-select" required>
                        <option value="">— Selecciona una carpeta —</option>
                        @foreach($carpetas as $c)
                        <option value="{{ $c->id }}" {{ old('carpeta_destino_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->nombre }}
                            @if($c->path && $c->path !== '/') · {{ $c->path }} @endif
                        </option>
                        @endforeach
                    </select>
                    @error('carpeta_destino_id')
                    <div class="mv-error">{{ $message }}</div>
                    @enderror

                    <div class="mv-btns">
                        <a href="{{ route('archivos.show', $archivo) }}" class="fc-btn fc-btn-outline">
                            Cancelar
                        </a>
                        <button type="submit" class="fc-btn fc-btn-primary">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M20 6h-8l-2-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2z"/>
                            </svg>
                            Mover archivo
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>{{-- /fc-main --}}
</div>{{-- /fc-wrapper --}}
</x-app-layout>
