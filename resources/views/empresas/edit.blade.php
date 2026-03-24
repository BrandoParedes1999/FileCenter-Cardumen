<x-app-layout>
    @php
        $bg     = $empresa->color_primario   ?? '#1B3A6B';
        $accent = $empresa->color_secundario ?? '#2E5FA3';
        $logo   = asset('images/empresas/'.$empresa->logo);
    @endphp
    <div class="fc-wrapper">
        @include('components.sidebar')
        
        @section('content')
        <div class="fc-main">
            <div class="fc-page-header">
                <div>
                    <h1 class="fc-page-title">Editar empresa</h1>
                    <div class="fc-breadcrumb">
                        <a href="{{ route('dashboard') }}">Inicio</a>
                        <span>›</span>
                        <a href="{{ route('empresas.index') }}">Empresas</a>
                        <span>›</span>
                        <a href="{{ route('empresas.show', $empresa) }}">{{ $empresa->siglas }}</a>
                        <span>›</span>
                        <span>Editar</span>
                    </div>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 320px;gap:24px">

                <form method="POST" action="{{ route('empresas.update', $empresa) }}" id="form-empresa">
                    @csrf @method('PUT')

                    <div class="fc-card">
                        <div class="fc-card-title">Datos de la empresa</div>

                        <div class="fc-form-row">
                            <div class="fc-form-group" style="flex:2">
                                <label class="fc-label">Nombre <span class="fc-required">*</span></label>
                                <input type="text" name="nombre" class="fc-input @error('nombre') fc-input-error @enderror"
                                    value="{{ old('nombre', $empresa->nombre) }}" required>
                                @error('nombre')<span class="fc-error">{{ $message }}</span>@enderror
                            </div>
                            <div class="fc-form-group" style="flex:1">
                                <label class="fc-label">Siglas <span class="fc-required">*</span></label>
                                <input type="text" name="siglas" id="siglas"
                                    class="fc-input @error('siglas') fc-input-error @enderror"
                                    value="{{ old('siglas', $empresa->siglas) }}" maxlength="10"
                                    style="text-transform:uppercase"
                                    {{ $empresa->es_corporativo ? 'readonly title=Las siglas del corporativo no se modifican' : '' }} required>
                                @error('siglas')<span class="fc-error">{{ $message }}</span>@enderror
                            </div>
                        </div>

                        <div class="fc-form-group">
                            <label class="fc-label">Logo
                                <span class="fc-label-hint">Nombre del archivo en <code>public/images/empresas/</code></span>
                            </label>
                            <div style="display:flex;gap:10px;align-items:center">
                                <input type="text" name="logo" id="logo-input"
                                    class="fc-input" style="flex:1"
                                    value="{{ old('logo', $empresa->logo) }}" placeholder="logo_empresa.png">
                                <img id="logo-preview" src="{{ asset('images/empresas/'.$empresa->logo) }}"
                                    alt="preview" style="width:48px;height:48px;object-fit:contain;border-radius:8px;border:1px solid var(--fc-border);padding:4px"
                                    onerror="this.src='{{ asset('images/logo.png') }}'">
                            </div>
                        </div>

                        <div class="fc-form-row" style="gap:10px;align-items:center">
                            <div class="fc-form-group" style="display:flex;align-items:center;gap:10px;margin-bottom:0">
                                <input type="hidden" name="es_corporativo" value="0">
                                <input type="checkbox" name="es_corporativo" id="es_corporativo" value="1"
                                    {{ old('es_corporativo', $empresa->es_corporativo) ? 'checked' : '' }}
                                    {{ $empresa->es_corporativo ? 'disabled' : '' }}
                                    class="fc-checkbox">
                                <label for="es_corporativo" class="fc-label" style="margin:0">Es empresa corporativa</label>
                            </div>
                            <div class="fc-form-group" style="display:flex;align-items:center;gap:10px;margin-bottom:0">
                                <input type="hidden" name="activo" value="0">
                                <input type="checkbox" name="activo" id="activo" value="1"
                                    {{ old('activo', $empresa->activo) ? 'checked' : '' }}
                                    {{ $empresa->es_corporativo ? 'disabled' : '' }}
                                    class="fc-checkbox">
                                <label for="activo" class="fc-label" style="margin:0">Activa</label>
                            </div>
                        </div>
                    </div>

                    <div class="fc-card" style="margin-top:20px">
                        <div class="fc-card-title">Identidad visual</div>

                        <div class="fc-form-row">
                            @foreach([
                                ['color_primario',   'cp1', 'hex1', 'Color primario',   'Sidebar · Topbar accent'],
                                ['color_secundario', 'cp2', 'hex2', 'Color secundario', 'Botones · Badges'],
                                ['color_terciario',  'cp3', 'hex3', 'Color terciario',  'Fondos · Hover'],
                            ] as [$field, $cpId, $hexId, $label, $hint])
                            @php $val = old($field, $empresa->$field ?? '#cccccc') @endphp
                            <div class="fc-form-group">
                                <label class="fc-label">{{ $label }}
                                    <span class="fc-label-hint">{{ $hint }}</span>
                                </label>
                                <div class="fc-color-input-wrap">
                                    <input type="color" id="{{ $cpId }}" value="{{ $val }}"
                                        class="fc-color-picker"
                                        oninput="syncColor(this,'{{ $field }}','{{ $hexId }}')">
                                    <input type="text" name="{{ $field }}" id="{{ $hexId }}"
                                        class="fc-input fc-color-hex"
                                        value="{{ $val }}" maxlength="7"
                                        oninput="syncHex(this,'{{ $cpId }}')">
                                </div>
                                @error($field)<span class="fc-error">{{ $message }}</span>@enderror
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div style="display:flex;gap:10px;margin-top:20px">
                        <button type="submit" class="fc-btn fc-btn-primary">Guardar cambios</button>
                        <a href="{{ route('empresas.show', $empresa) }}" class="fc-btn fc-btn-outline">Cancelar</a>
                    </div>
                </form>

                {{-- Preview igual que en create --}}
                <div>
                    <div class="fc-card" style="position:sticky;top:20px">
                        <div class="fc-card-title">Vista previa</div>
                        <div id="preview-sidebar"
                            style="background:{{ $empresa->color_primario ?? '#1B3A6B' }};border-radius:10px;padding:16px;margin-top:12px;transition:background .3s">
                            <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;padding-bottom:12px;border-bottom:1px solid rgba(255,255,255,0.12)">
                                <img id="preview-logo" src="{{ asset('images/empresas/'.$empresa->logo) }}"
                                    style="width:36px;height:36px;object-fit:contain;border-radius:6px;background:rgba(255,255,255,0.1);padding:4px"
                                    onerror="this.src='{{ asset('images/logo.png') }}'">
                                <div>
                                    <div id="preview-nombre" style="color:#f1f5f9;font-size:13px;font-weight:500">{{ $empresa->nombre }}</div>
                                    <div id="preview-siglas" style="color:rgba(255,255,255,0.4);font-size:11px">{{ $empresa->siglas }} · Sistema QHSE</div>
                                </div>
                            </div>
                            <div id="preview-nav-active"
                                style="padding:8px 12px;border-radius:6px;font-size:12px;background:{{ ($empresa->color_secundario ?? '#2E5FA3').'44' }};color:{{ $empresa->color_secundario ?? '#2E5FA3' }};margin-bottom:6px">
                                Dashboard
                            </div>
                            <div style="padding:8px 12px;border-radius:6px;font-size:12px;color:rgba(255,255,255,0.5)">Mis Carpetas</div>
                            <div style="margin-top:16px;padding-top:12px;border-top:1px solid rgba(255,255,255,0.12)">
                                <div style="font-size:10px;color:rgba(255,255,255,0.3);margin-bottom:6px">Badge</div>
                                <span id="preview-badge"
                                    style="padding:3px 10px;border-radius:999px;font-size:11px;font-weight:500;background:{{ ($empresa->color_secundario ?? '#2E5FA3').'22' }};color:{{ $empresa->color_secundario ?? '#2E5FA3' }};border:1px solid {{ ($empresa->color_secundario ?? '#2E5FA3').'44' }}">
                                    {{ $empresa->siglas }}
                                </span>
                            </div>
                        </div>
                        <div style="margin-top:12px">
                            <div style="font-size:11px;color:var(--fc-text-muted);margin-bottom:6px">Acento en topbar</div>
                            <div style="height:3px;border-radius:2px;background:linear-gradient(90deg,{{ $empresa->color_secundario ?? '#2E5FA3' }},{{ $empresa->color_primario ?? '#1B3A6B' }})" id="preview-accent"></div>
                        </div>
                    </div>
                </div>

            </div>

            <script>
            function syncColor(picker, fieldName, hexId) {
                document.getElementById(hexId).value = picker.value;
                document.querySelector('[name="'+fieldName+'"]').value = picker.value;
                updatePreview();
            }
            function syncHex(hexInput, pickerId) {
                const val = hexInput.value;
                if (/^#[0-9A-Fa-f]{6}$/.test(val)) document.getElementById(pickerId).value = val;
                updatePreview();
            }
            function updatePreview() {
                const bg     = document.getElementById('hex1').value || '#1B3A6B';
                const accent = document.getElementById('hex2').value || '#2E5FA3';
                const siglas = (document.getElementById('siglas').value || 'EMP').toUpperCase();
                const nombre = document.querySelector('[name="nombre"]').value || 'Empresa';
                const logo   = document.getElementById('logo-input').value || 'logo_default.png';

                document.getElementById('preview-sidebar').style.background = bg;
                const nav = document.getElementById('preview-nav-active');
                nav.style.background = accent + '44';
                nav.style.color = accent;
                const badge = document.getElementById('preview-badge');
                badge.style.background = accent + '22';
                badge.style.color = accent;
                badge.style.border = '1px solid ' + accent + '44';
                badge.textContent = siglas;
                document.getElementById('preview-accent').style.background = 'linear-gradient(90deg,'+accent+','+bg+')';
                document.getElementById('preview-nombre').textContent = nombre;
                document.getElementById('preview-siglas').textContent = siglas + ' · Sistema QHSE';
                document.getElementById('preview-logo').src = '/images/empresas/' + logo;
            }
            document.getElementById('logo-input').addEventListener('input', function() {
                document.getElementById('logo-preview').src = '/images/empresas/' + this.value;
                updatePreview();
            });
            document.getElementById('siglas').addEventListener('input', function() {
                this.value = this.value.toUpperCase();
                updatePreview();
            });
            document.querySelector('[name="nombre"]').addEventListener('input', updatePreview);
            </script>
        </div>{{-- /fc-main --}}
    </div>{{-- /fc-wrapper --}}
</x-app-layout>