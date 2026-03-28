<x-app-layout>
<div class="fc-wrapper">
    @include('components.sidebar')

    <div class="fc-main">
        <header class="fc-topbar">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#6366f1">
                <path d="M20 6h-8l-2-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2z"/>
            </svg>
            <span class="fc-topbar-title">Nueva Carpeta</span>
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

        <div class="fc-content">
            <div class="fc-breadcrumb">
                <a href="{{ route('carpetas.index') }}" class="fc-bread-item">📁 Inicio</a>
                @if($padre)
                    <span class="fc-bread-sep">›</span>
                    <a href="{{ route('carpetas.show', $padre) }}" class="fc-bread-item">{{ $padre->nombre }}</a>
                @endif
                <span class="fc-bread-sep">›</span>
                <span class="fc-bread-current">Nueva carpeta</span>
            </div>

            <div class="fc-form-wrap">
                <div class="fc-form-card">
                    <div class="fc-form-header">
                        <div class="fc-form-header-icon" style="background:rgba(79,70,229,0.1)">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="#4f46e5">
                                <path d="M20 6h-8l-2-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="fc-form-title">Crear nueva carpeta</div>
                            <div class="fc-form-sub">
                                @if($padre) Subcarpeta dentro de "{{ $padre->nombre }}"
                                @else Carpeta raíz
                                @endif
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('carpetas.store') }}" method="POST">
                        @csrf
                        @if($padre)
                            <input type="hidden" name="padre_id" value="{{ $padre->id }}">
                        @endif

                        <div class="fc-form-body">

                            {{-- Selector de empresa (Superadmin sin padre fijo) --}}
                            @if(in_array(Auth::user()->rol, ['Superadmin', 'Aux_QHSE']) && !$padre)
                            <div class="fc-field">
                                <label for="empresa_id">¿En qué empresa irá esta carpeta? *</label>
                                <select id="empresa_id" name="empresa_id"
                                        class="fc-empresa-selector" required onchange="actualizarDestino(this)">
                                    <option value="">— Selecciona una empresa —</option>
                                    @foreach($empresas as $emp)
                                        <option value="{{ $emp->id }}"
                                                data-color="{{ $emp->color_primario ?? '#4f46e5' }}"
                                                data-corp="{{ $emp->es_corporativo ? '1' : '0' }}"
                                                data-nombre="{{ $emp->nombre }}"
                                                {{ old('empresa_id') == $emp->id ? 'selected' : '' }}>
                                            {{ $emp->es_corporativo ? '🏢 ' : '🏭 ' }}{{ $emp->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('empresa_id')
                                    <span class="fc-field-error">{{ $message }}</span>
                                @enderror
                                <div class="fc-empresa-destino" id="destinoChip" style="display:none">
                                    <div class="fc-empresa-destino-dot" id="destinoDot"></div>
                                    <div>
                                        <div class="fc-empresa-destino-nombre" id="destinoNombre"></div>
                                        <div class="fc-empresa-destino-tipo" id="destinoTipo"></div>
                                    </div>
                                </div>
                            </div>

                            @elseif($padre)
                                <input type="hidden" name="empresa_id" value="{{ $padre->empresa_id }}">
                                <div class="fc-info-chip">
                                    <div class="fc-info-chip-icon">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="#4f46e5"><path d="M12 7V3H2v18h20V7H12z"/></svg>
                                    </div>
                                    <div>
                                        <div class="fc-info-chip-label">Empresa destino</div>
                                        <div class="fc-info-chip-name">{{ $padre->empresa->nombre ?? '—' }}</div>
                                        <div class="fc-info-chip-sub">Heredada del padre · {{ $padre->path }}</div>
                                    </div>
                                </div>
                            @else
                                <input type="hidden" name="empresa_id" value="{{ Auth::user()->empresa_id }}">
                                <div class="fc-info-chip">
                                    <div class="fc-info-chip-icon">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="#4f46e5"><path d="M12 7V3H2v18h20V7H12z"/></svg>
                                    </div>
                                    <div>
                                        <div class="fc-info-chip-label">Tu empresa</div>
                                        <div class="fc-info-chip-name">{{ Auth::user()->empresa->nombre ?? '—' }}</div>
                                    </div>
                                </div>
                            @endif

                            {{-- Nombre --}}
                            <div class="fc-field">
                                <label for="nombre">Nombre de la carpeta *</label>
                                <input type="text" id="nombre" name="nombre"
                                    value="{{ old('nombre') }}"
                                    placeholder="Ej: Contratos 2026"
                                    required autofocus>
                                @error('nombre')
                                    <span class="fc-field-error">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- ══ MODO DE ACCESO ══════════════════════════════════════ --}}
                            <div class="fc-field">
                                <label>Modo de acceso *</label>
                                <div style="display:flex;flex-direction:column;gap:10px;margin-top:4px">

                                    {{-- Solo lectura --}}
                                    <label style="display:flex;align-items:flex-start;gap:12px;padding:14px 16px;
                                                  border:1.5px solid {{ old('modo_acceso','normal') === 'solo_lectura' ? '#6366f1' : 'var(--fc-border)' }};
                                                  border-radius:12px;cursor:pointer;transition:border-color .15s"
                                           onclick="selectModo('solo_lectura', this)">
                                        <input type="radio" name="modo_acceso" value="solo_lectura"
                                               {{ old('modo_acceso','normal') === 'solo_lectura' ? 'checked' : '' }}
                                               style="accent-color:#6366f1;margin-top:2px">
                                        <div>
                                            <div style="font-size:13px;font-weight:600;color:var(--fc-text);display:flex;align-items:center;gap:7px">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="#6366f1">
                                                    <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                                                </svg>
                                                Solo lectura online
                                            </div>
                                            <div style="font-size:12px;color:var(--fc-text-muted);margin-top:3px">
                                                Los usuarios pueden <strong>ver</strong> el contenido pero <strong>no descargar</strong>,
                                                a menos que tengan permiso explícito de descarga en sus permisos de carpeta.
                                            </div>
                                        </div>
                                    </label>

                                    {{-- Con descarga --}}
                                    <label style="display:flex;align-items:flex-start;gap:12px;padding:14px 16px;
                                                  border:1.5px solid {{ old('modo_acceso','normal') === 'con_descarga' ? '#059669' : 'var(--fc-border)' }};
                                                  border-radius:12px;cursor:pointer;transition:border-color .15s"
                                           onclick="selectModo('con_descarga', this)">
                                        <input type="radio" name="modo_acceso" value="con_descarga"
                                               {{ old('modo_acceso','normal') === 'con_descarga' ? 'checked' : '' }}
                                               style="accent-color:#059669;margin-top:2px">
                                        <div>
                                            <div style="font-size:13px;font-weight:600;color:var(--fc-text);display:flex;align-items:center;gap:7px">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="#059669">
                                                    <path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/>
                                                </svg>
                                                Con descarga habilitada
                                            </div>
                                            <div style="font-size:12px;color:var(--fc-text-muted);margin-top:3px">
                                                Los usuarios con permiso de descarga pueden ver <strong>y descargar</strong>
                                                los archivos. Se controla con el campo "puede_descargar" del permiso.
                                            </div>
                                        </div>
                                    </label>

                                    {{-- Normal --}}
                                    <label style="display:flex;align-items:flex-start;gap:12px;padding:14px 16px;
                                                  border:1.5px solid {{ old('modo_acceso','normal') === 'normal' || !old('modo_acceso') ? '#6366f1' : 'var(--fc-border)' }};
                                                  border-radius:12px;cursor:pointer;transition:border-color .15s"
                                           onclick="selectModo('normal', this)">
                                        <input type="radio" name="modo_acceso" value="normal"
                                               {{ old('modo_acceso','normal') === 'normal' || !old('modo_acceso') ? 'checked' : '' }}
                                               style="accent-color:#6366f1;margin-top:2px">
                                        <div>
                                            <div style="font-size:13px;font-weight:600;color:var(--fc-text);display:flex;align-items:center;gap:7px">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="#64748b">
                                                    <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14z"/>
                                                </svg>
                                                Normal (control por permisos)
                                            </div>
                                            <div style="font-size:12px;color:var(--fc-text-muted);margin-top:3px">
                                                El acceso se controla únicamente con los permisos individuales
                                                asignados a usuarios o roles.
                                            </div>
                                        </div>
                                    </label>

                                </div>
                                @error('modo_acceso')
                                    <span class="fc-field-error">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- ══ VISIBILIDAD ══════════════════════════════════════════ --}}
                            <div class="fc-field">
                                <label>Visibilidad</label>
                                <label class="fc-checkbox-wrap">
                                    <input type="hidden" name="es_publico" value="0">
                                    <input type="checkbox" name="es_publico" value="1"
                                        {{ old('es_publico') ? 'checked' : '' }}>
                                    <div>
                                        <div class="fc-checkbox-label">🌐 Carpeta pública</div>
                                        <div class="fc-checkbox-hint">Todos los usuarios de la empresa pueden verla sin permiso explícito</div>
                                    </div>
                                </label>
                            </div>

                            {{-- ══ APROBACIÓN DE SUBIDAS ════════════════════════════════ --}}
                            <div class="fc-field">
                                <label>Control de subidas</label>
                                <label class="fc-checkbox-wrap" style="border-color:rgba(245,158,11,0.3);background:rgba(245,158,11,0.03)">
                                    <input type="hidden" name="requiere_aprobacion_subida" value="0">
                                    <input type="checkbox" name="requiere_aprobacion_subida" value="1"
                                        {{ old('requiere_aprobacion_subida') ? 'checked' : '' }}>
                                    <div>
                                        <div class="fc-checkbox-label" style="color:#d97706">
                                            ⏳ Requerir aprobación para subir
                                        </div>
                                        <div class="fc-checkbox-hint">
                                            Cuando Auxiliar o Empleado suben un archivo, queda pendiente de
                                            aprobación por un Admin o Gerente antes de publicarse.
                                            Admin y Gerente siempre pueden subir directamente.
                                        </div>
                                    </div>
                                </label>
                            </div>

                        </div>

                        <div class="fc-form-footer">
                            @if($padre)
                                <a href="{{ route('carpetas.show', $padre) }}" class="fc-btn fc-btn-outline">Cancelar</a>
                            @else
                                <a href="{{ route('carpetas.index') }}" class="fc-btn fc-btn-outline">Cancelar</a>
                            @endif
                            <button type="submit" class="fc-btn fc-btn-primary">Crear carpeta</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function actualizarDestino(select) {
    const opt  = select.options[select.selectedIndex];
    const chip = document.getElementById('destinoChip');
    if (!opt.value) { chip.style.display = 'none'; return; }

    const esCorp = opt.dataset.corp === '1';
    const color  = opt.dataset.color;

    document.getElementById('destinoDot').style.background   = esCorp ? '#1b3a6b' : color;
    document.getElementById('destinoNombre').textContent     = opt.dataset.nombre;
    document.getElementById('destinoTipo').textContent       = esCorp
        ? 'Corporativo — carpetas visibles por todas las empresas'
        : 'Empresa — visible solo para usuarios de esta empresa';

    chip.className    = 'fc-empresa-destino' + (esCorp ? ' corp' : '');
    chip.style.display = 'flex';
    if (!esCorp) chip.style.borderColor = color + '44';
}

function selectModo(valor, labelEl) {
    // Quitar selección visual de todos
    document.querySelectorAll('input[name="modo_acceso"]').forEach(radio => {
        const lbl = radio.closest('label');
        if (lbl) lbl.style.borderColor = 'var(--fc-border)';
    });
    // Marcar el seleccionado
    const colores = { solo_lectura: '#6366f1', con_descarga: '#059669', normal: '#6366f1' };
    labelEl.style.borderColor = colores[valor] || '#6366f1';
}
</script>
</x-app-layout>