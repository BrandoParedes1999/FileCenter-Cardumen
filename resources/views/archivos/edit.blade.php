<x-app-layout>
    <div class="fc-wrapper">
        @include('components.sidebar')
        <div class="fc-main">
            <header class="fc-topbar">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="#6366f1">
                    <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                </svg>
                <span class="fc-topbar-title">Editar archivo</span>
                <div class="fc-topbar-right">
                    <div class="fc-topbar-avatar">{{ strtoupper(substr(Auth::user()->nombre,0,1)) }}{{ strtoupper(substr(Auth::user()->paterno,0,1)) }}</div>
                    <div>
                        <div class="fc-topbar-name">{{ Auth::user()->nombre_completo }}</div>
                        <div class="fc-topbar-role">{{ Auth::user()->rol }}</div>
                    </div>
                </div>
            </header>

            <div class="fc-content">
                <div class="fc-form-wrap">
                    <div class="fc-breadcrumb">
                        <a href="{{ route('carpetas.index') }}" class="fc-bread-item">📁 Inicio</a>
                        <span class="fc-bread-sep">›</span>
                        <a href="{{ route('carpetas.show', $archivo->carpeta) }}" class="fc-bread-item">{{ $archivo->carpeta->nombre }}</a>
                        <span class="fc-bread-sep">›</span>
                        <a href="{{ route('archivos.show', $archivo) }}" class="fc-bread-item">{{ Str::limit($archivo->nombre_original, 30) }}</a>
                        <span class="fc-bread-sep">›</span>
                        <span class="fc-bread-current">Editar</span>
                    </div>

                    <div class="fc-form-card">
                        <div class="fc-form-header">
                            <div class="fc-form-header-icon">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="#4f46e5">
                                    <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="fc-form-title">Editar descripción</div>
                                <div class="fc-form-sub">Solo se puede modificar la descripción del archivo</div>
                            </div>
                        </div>

                        <form action="{{ route('archivos.update', $archivo) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="fc-form-body">

                                {{-- Chip del archivo --}}
                                @php
                                    $extColors = ['pdf'=>'#dc2626','docx'=>'#2563eb','doc'=>'#2563eb','xlsx'=>'#059669','xls'=>'#059669','pptx'=>'#ea580c','ppt'=>'#ea580c'];
                                    $c = $extColors[strtolower($archivo->extension)] ?? '#64748b';
                                @endphp
                                <div class="fc-file-chip">
                                    <div class="fc-file-chip-icon" style="background:rgba(0,0,0,0.05)">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="{{ $c }}">
                                            <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="fc-file-chip-name">{{ $archivo->nombre_original }}</div>
                                        <div class="fc-file-chip-meta">
                                            {{ $archivo->tamanioFormateado() }} · v{{ $archivo->version }} · {{ strtoupper($archivo->extension) }}
                                        </div>
                                    </div>
                                </div>

                                <div class="fc-field">
                                    <label for="descripcion">Descripción</label>
                                    <textarea id="descripcion" name="descripcion" rows="5"
                                        placeholder="Describe el contenido de este archivo...">{{ old('descripcion', $archivo->descripcion) }}</textarea>
                                    <div class="fc-field-hint">Máximo 500 caracteres. Facilita la búsqueda y comprensión del archivo.</div>
                                    @error('descripcion')
                                        <div style="font-size:12px;color:#dc2626;margin-top:5px">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>
                            <div class="fc-form-footer">
                                <a href="{{ route('archivos.show', $archivo) }}" class="fc-btn fc-btn-outline">Cancelar</a>
                                <button type="submit" class="fc-btn fc-btn-primary">Guardar cambios</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>