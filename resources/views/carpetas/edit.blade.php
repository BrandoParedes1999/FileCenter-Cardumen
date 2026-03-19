<x-app-layout>
<div class="fc-wrapper">
    @include('components.sidebar')
    <div class="fc-main">
        <header class="fc-topbar">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#6366f1">
                <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
            </svg>
            <span class="fc-topbar-title">Editar carpeta</span>
            <div class="fc-topbar-right">
                <div class="fc-topbar-avatar">{{ strtoupper(substr(Auth::user()->nombre,0,1)) }}{{ strtoupper(substr(Auth::user()->paterno,0,1)) }}</div>
                <div>
                    <div class="fc-topbar-name">{{ Auth::user()->nombre_completo }}</div>
                    <div class="fc-topbar-role">{{ Auth::user()->rol }}</div>
                </div>
            </div>
        </header>

        <div class="fc-content">
            <div class="fc-breadcrumb">
                <a href="{{ route('carpetas.index') }}" class="fc-bread-item">📁 Inicio</a>
                <span class="fc-bread-sep">›</span>
                <a href="{{ route('carpetas.show', $carpeta) }}" class="fc-bread-item">{{ $carpeta->nombre }}</a>
                <span class="fc-bread-sep">›</span>
                <span class="fc-bread-current">Editar</span>
            </div>

            <div class="fc-form-wrap">
                <div class="fc-form-card">
                    <div class="fc-form-header">
                        <div class="fc-form-header-icon" style="background:rgba(79,70,229,0.1)">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="#4f46e5">
                                <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="fc-form-title">Editar: {{ $carpeta->nombre }}</div>
                            <div class="fc-form-sub">Modifica nombre o visibilidad · Empresa: {{ $carpeta->empresa->nombre ?? '—' }}</div>
                        </div>
                    </div>

                    <form action="{{ route('carpetas.update', $carpeta) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="fc-form-body">

                            {{-- Info de solo lectura --}}
                            <div style="display:flex;gap:12px">
                                <div style="flex:1;background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:12px 14px">
                                    <div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:3px">Ruta</div>
                                    <div style="font-size:12px;font-weight:600;color:#475569;font-family:monospace">{{ $carpeta->path }}</div>
                                </div>
                                <div style="flex:1;background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:12px 14px">
                                    <div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:3px">Empresa</div>
                                    <div style="font-size:12px;font-weight:600;color:#475569">{{ $carpeta->empresa->nombre ?? '—' }}</div>
                                </div>
                            </div>

                            <div class="fc-field">
                                <label for="nombre">Nombre *</label>
                                <input type="text" id="nombre" name="nombre"
                                        value="{{ old('nombre', $carpeta->nombre) }}" required autofocus>
                                @error('nombre') <span class="fc-field-error">{{ $message }}</span> @enderror
                            </div>

                            <div class="fc-field">
                                <label>Visibilidad</label>
                                <label class="fc-checkbox-wrap">
                                    <input type="hidden" name="es_publico" value="0">
                                    <input type="checkbox" name="es_publico" value="1"
                                        {{ old('es_publico', $carpeta->es_publico) ? 'checked' : '' }}>
                                    <div>
                                        <div class="fc-checkbox-label">🌐 Carpeta pública</div>
                                        <div class="fc-checkbox-hint">Todos los usuarios de la empresa pueden verla sin permiso explícito</div>
                                    </div>
                                </label>
                            </div>

                        </div>
                        <div class="fc-form-footer">
                            <a href="{{ route('carpetas.show', $carpeta) }}" class="fc-btn fc-btn-outline">Cancelar</a>
                            <button type="submit" class="fc-btn fc-btn-primary">Guardar cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
