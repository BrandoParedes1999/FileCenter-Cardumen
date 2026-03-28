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
                            <div class="fc-form-sub">
                                Empresa: {{ $carpeta->empresa->nombre ?? '—' }}
                                · Ruta: <code style="font-size:11px">{{ $carpeta->path }}</code>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('carpetas.update', $carpeta) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="fc-form-body">

                            {{-- Info de solo lectura --}}
                            <div style="display:flex;gap:12px">
                                <div style="flex:1;background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:12px 14px">
                                    <div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:3px">Ruta actual</div>
                                    <div style="font-size:12px;font-weight:600;color:#475569;font-family:monospace">{{ $carpeta->path }}</div>
                                </div>
                                <div style="flex:1;background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:12px 14px">
                                    <div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.08em;margin-bottom:3px">Empresa</div>
                                    <div style="font-size:12px;font-weight:600;color:#475569">{{ $carpeta->empresa->nombre ?? '—' }}</div>
                                </div>
                            </div>

                            {{-- Nombre --}}
                            <div class="fc-field">
                                <label for="nombre">Nombre *</label>
                                <input type="text" id="nombre" name="nombre"
                                       value="{{ old('nombre', $carpeta->nombre) }}" required autofocus>
                                @error('nombre') <span class="fc-field-error">{{ $message }}</span> @enderror
                            </div>

                            {{-- ══ MODO DE ACCESO ══ --}}
                            <div class="fc-field">
                                <label>Modo de acceso *</label>
                                <div style="display:flex;flex-direction:column;gap:10px;margin-top:4px">

                                    @php $modoActual = old('modo_acceso', $carpeta->modo_acceso ?? 'normal'); @endphp

                                    <label id="lbl-solo_lectura"
                                           style="display:flex;align-items:flex-start;gap:12px;padding:14px 16px;
                                                  border:1.5px solid {{ $modoActual === 'solo_lectura' ? '#6366f1' : 'var(--fc-border)' }};
                                                  border-radius:12px;cursor:pointer;transition:border-color .15s"
                                           onclick="selectModo('solo_lectura')">
                                        <input type="radio" name="modo_acceso" value="solo_lectura"
                                               {{ $modoActual === 'solo_lectura' ? 'checked' : '' }}
                                               style="accent-color:#6366f1;margin-top:2px">
                                        <div>
                                            <div style="font-size:13px;font-weight:600;color:var(--fc-text);display:flex;align-items:center;gap:7px">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="#6366f1"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                                                Solo lectura online
                                            </div>
                                            <div style="font-size:12px;color:var(--fc-text-muted);margin-top:3px">
                                                Los usuarios pueden <strong>ver</strong> el contenido pero <strong>no descargar</strong>,
                                                salvo permiso explícito en sus permisos de carpeta.
                                            </div>
                                        </div>
                                    </label>

                                    <label id="lbl-con_descarga"
                                           style="display:flex;align-items:flex-start;gap:12px;padding:14px 16px;
                                                  border:1.5px solid {{ $modoActual === 'con_descarga' ? '#059669' : 'var(--fc-border)' }};
                                                  border-radius:12px;cursor:pointer;transition:border-color .15s"
                                           onclick="selectModo('con_descarga')">
                                        <input type="radio" name="modo_acceso" value="con_descarga"
                                               {{ $modoActual === 'con_descarga' ? 'checked' : '' }}
                                               style="accent-color:#059669;margin-top:2px">
                                        <div>
                                            <div style="font-size:13px;font-weight:600;color:var(--fc-text);display:flex;align-items:center;gap:7px">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="#059669"><path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/></svg>
                                                Con descarga habilitada
                                            </div>
                                            <div style="font-size:12px;color:var(--fc-text-muted);margin-top:3px">
                                                Los usuarios con permiso de descarga pueden ver <strong>y descargar</strong> los archivos.
                                            </div>
                                        </div>
                                    </label>

                                    <label id="lbl-normal"
                                           style="display:flex;align-items:flex-start;gap:12px;padding:14px 16px;
                                                  border:1.5px solid {{ $modoActual === 'normal' ? '#6366f1' : 'var(--fc-border)' }};
                                                  border-radius:12px;cursor:pointer;transition:border-color .15s"
                                           onclick="selectModo('normal')">
                                        <input type="radio" name="modo_acceso" value="normal"
                                               {{ $modoActual === 'normal' ? 'checked' : '' }}
                                               style="accent-color:#6366f1;margin-top:2px">
                                        <div>
                                            <div style="font-size:13px;font-weight:600;color:var(--fc-text);display:flex;align-items:center;gap:7px">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="#64748b"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14z"/></svg>
                                                Normal (control por permisos)
                                            </div>
                                            <div style="font-size:12px;color:var(--fc-text-muted);margin-top:3px">
                                                El acceso se controla únicamente con los permisos asignados a usuarios o roles.
                                            </div>
                                        </div>
                                    </label>

                                </div>
                                @error('modo_acceso') <span class="fc-field-error">{{ $message }}</span> @enderror
                            </div>

                            {{-- Visibilidad --}}
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

                            {{-- Aprobación de subidas --}}
                            <div class="fc-field">
                                <label>Control de subidas</label>
                                <label class="fc-checkbox-wrap" style="border-color:rgba(245,158,11,0.3);background:rgba(245,158,11,0.03)">
                                    <input type="hidden" name="requiere_aprobacion_subida" value="0">
                                    <input type="checkbox" name="requiere_aprobacion_subida" value="1"
                                        {{ old('requiere_aprobacion_subida', $carpeta->requiere_aprobacion_subida) ? 'checked' : '' }}>
                                    <div>
                                        <div class="fc-checkbox-label" style="color:#d97706">
                                            ⏳ Requerir aprobación para subir
                                        </div>
                                        <div class="fc-checkbox-hint">
                                            Cuando Auxiliar o Empleado suben un archivo, queda pendiente de
                                            aprobación por un Admin o Gerente antes de publicarse.
                                            Admin y Gerente siempre suben directamente.
                                        </div>
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

<script>
const coloresModo = { solo_lectura: '#6366f1', con_descarga: '#059669', normal: '#6366f1' };

function selectModo(valor) {
    ['solo_lectura', 'con_descarga', 'normal'].forEach(m => {
        const lbl = document.getElementById('lbl-' + m);
        if (lbl) lbl.style.borderColor = m === valor
            ? (coloresModo[m] || '#6366f1')
            : 'var(--fc-border)';
    });
}
</script>
</x-app-layout>