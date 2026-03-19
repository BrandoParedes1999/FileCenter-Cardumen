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

                            {{-- ── SELECTOR DE EMPRESA (solo Superadmin y sin padre fijo) ── --}}
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
                                            {{ $emp->es_corporativo ? '(Corporativo — visible por todos)' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('empresa_id')
                                    <span class="fc-field-error">{{ $message }}</span>
                                @enderror

                                {{-- Chip visual del destino seleccionado --}}
                                <div class="fc-empresa-destino" id="destinoChip" style="display:none">
                                    <div class="fc-empresa-destino-dot" id="destinoDot"></div>
                                    <div>
                                        <div class="fc-empresa-destino-nombre" id="destinoNombre"></div>
                                        <div class="fc-empresa-destino-tipo" id="destinoTipo"></div>
                                    </div>
                                </div>
                            </div>

                            @elseif($padre)
                                {{-- Subcarpeta: la empresa ya viene del padre --}}
                                <input type="hidden" name="empresa_id" value="{{ $padre->empresa_id }}">
                                <div class="fc-info-chip">
                                    <div class="fc-info-chip-icon">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="#4f46e5">
                                            <path d="M12 7V3H2v18h20V7H12z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="fc-info-chip-label">Empresa destino</div>
                                        <div class="fc-info-chip-name">{{ $padre->empresa->nombre ?? '—' }}</div>
                                        <div class="fc-info-chip-sub">Heredada del padre · {{ $padre->path }}</div>
                                    </div>
                                </div>

                            @else
                                {{-- Usuario normal: empresa fija --}}
                                <input type="hidden" name="empresa_id" value="{{ Auth::user()->empresa_id }}">
                                <div class="fc-info-chip">
                                    <div class="fc-info-chip-icon">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="#4f46e5">
                                            <path d="M12 7V3H2v18h20V7H12z"/>
                                        </svg>
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
                                    placeholder="Ej: Documentos QHSE 2026"
                                    required autofocus>
                                @error('nombre')
                                    <span class="fc-field-error">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Visibilidad --}}
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

                        </div>{{-- /fc-form-body --}}

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
    const opt = select.options[select.selectedIndex];
    const chip = document.getElementById('destinoChip');
    if (!opt.value) { chip.style.display = 'none'; return; }

    const esCorp = opt.dataset.corp === '1';
    const color  = opt.dataset.color;

    document.getElementById('destinoDot').style.background   = esCorp ? '#1b3a6b' : color;
    document.getElementById('destinoNombre').textContent     = opt.dataset.nombre;
    document.getElementById('destinoTipo').textContent       = esCorp
        ? 'Corporativo — carpetas visibles por todas las empresas'
        : 'Empresa — visible solo para usuarios de esta empresa';

    chip.className = 'fc-empresa-destino' + (esCorp ? ' corp' : '');
    chip.style.display = 'flex';
    if (!esCorp) chip.style.borderColor = color + '44';
}
</script>
</x-app-layout>