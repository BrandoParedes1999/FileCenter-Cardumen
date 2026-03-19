<x-app-layout>
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
.fc-wrapper { display: flex; height: 100dvh; width: 100%; background: #f8fafc; color: #1e293b; font-family: 'Segoe UI', system-ui, sans-serif; font-size: 15px; overflow: hidden; }
.fc-main { flex: 1; display: flex; flex-direction: column; height: 100dvh; overflow: hidden; min-width: 0; }
.fc-topbar { height: 58px; background: #fff; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; padding: 0 24px; gap: 14px; flex-shrink: 0; }
.fc-topbar-title { font-size: 15px; font-weight: 700; color: #1e293b; }
.fc-topbar-right { display: flex; align-items: center; gap: 14px; margin-left: auto; }
.fc-topbar-avatar { width: 36px; height: 36px; background: linear-gradient(135deg,#4f46e5,#7c3aed); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; color: #fff; }
.fc-topbar-name { font-size: 13px; font-weight: 600; color: #0f172a; }
.fc-topbar-role { font-size: 11px; color: #7c3aed; }
.fc-content { flex: 1; overflow-y: auto; padding: 32px 24px; scrollbar-width: thin; }
.fc-form-wrap { max-width: 560px; margin: 0 auto; }
.fc-breadcrumb { display: flex; align-items: center; gap: 6px; margin-bottom: 22px; font-size: 13px; }
.fc-bread-item { color: #6366f1; font-weight: 500; text-decoration: none; }
.fc-bread-item:hover { text-decoration: underline; }
.fc-bread-sep { color: #cbd5e1; }
.fc-bread-current { color: #475569; font-weight: 600; }
.fc-form-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 18px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.06); }
.fc-form-header { padding: 24px 28px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; gap: 14px; }
.fc-form-header-icon { width: 46px; height: 46px; border-radius: 13px; background: rgba(79,70,229,0.1); display: flex; align-items: center; justify-content: center; }
.fc-form-title { font-size: 17px; font-weight: 700; color: #1e293b; }
.fc-form-sub { font-size: 12px; color: #94a3b8; margin-top: 2px; }
.fc-form-body { padding: 28px; display: flex; flex-direction: column; gap: 20px; }
.fc-file-chip { display: flex; align-items: center; gap: 12px; padding: 14px 16px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; }
.fc-file-chip-icon { width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.fc-file-chip-name { font-size: 13px; font-weight: 600; color: #1e293b; }
.fc-file-chip-meta { font-size: 11px; color: #94a3b8; margin-top: 2px; }
.fc-field label { display: block; font-size: 12px; font-weight: 700; color: #374151; margin-bottom: 7px; text-transform: uppercase; letter-spacing: .06em; }
.fc-field textarea { width: 100%; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 11px 14px; color: #1e293b; font-size: 13px; outline: none; resize: vertical; font-family: inherit; line-height: 1.6; transition: border-color .2s, background .2s; }
.fc-field textarea:focus { background: #fff; border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,0.1); }
.fc-field textarea::placeholder { color: #94a3b8; }
.fc-field-hint { font-size: 11px; color: #94a3b8; margin-top: 5px; }
.fc-form-footer { padding: 20px 28px; border-top: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: flex-end; gap: 10px; }
.fc-btn-cancel { padding: 10px 20px; border-radius: 10px; border: 1px solid #e2e8f0; background: #f8fafc; color: #475569; font-size: 13px; cursor: pointer; text-decoration: none; }
.fc-btn-cancel:hover { background: #f1f5f9; }
.fc-btn-submit { padding: 10px 22px; border-radius: 10px; border: none; background: linear-gradient(135deg,#4f46e5,#7c3aed); color: #fff; font-size: 13px; font-weight: 600; cursor: pointer; box-shadow: 0 4px 12px rgba(79,70,229,0.3); }
.fc-btn-submit:hover { opacity: .9; }
</style>

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
                            <a href="{{ route('archivos.show', $archivo) }}" class="fc-btn-cancel">Cancelar</a>
                            <button type="submit" class="fc-btn-submit">Guardar cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>