<x-app-layout>

<div class="fc-page-header">
    <div>
        <h1 class="fc-page-title">Nueva Empresa</h1>
        <div class="fc-breadcrumb">
            <a href="{{ route('dashboard') }}">Inicio</a>
            <span>›</span>
            <a href="{{ route('empresas.index') }}">Empresas</a>
            <span>›</span>
            <span>Nueva</span>
        </div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 320px;gap:24px">

    <form method="POST" action="{{ route('empresas.store') }}" id="form-empresa">
        @csrf

        {{-- Datos básicos --}}
        <div class="fc-card">
            <div class="fc-card-title">Datos de la empresa</div>

            <div class="fc-form-row">
                <div class="fc-form-group" style="flex:2">
                    <label class="fc-label">Nombre <span class="fc-required">*</span></label>
                    <input type="text" name="nombre" class="fc-input @error('nombre') fc-input-error @enderror"
                           value="{{ old('nombre') }}" placeholder="Ej. Empresa Norte S.A. de C.V." required>
                    @error('nombre')<span class="fc-error">{{ $message }}</span>@enderror
                </div>
                <div class="fc-form-group" style="flex:1">
                    <label class="fc-label">Siglas <span class="fc-required">*</span></label>
                    <input type="text" name="siglas" id="siglas"
                           class="fc-input @error('siglas') fc-input-error @enderror"
                           value="{{ old('siglas') }}" placeholder="EMP1" maxlength="10"
                           style="text-transform:uppercase" required>
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
                           value="{{ old('logo','logo_default.png') }}" placeholder="logo_empresa.png">
                    <img id="logo-preview" src="{{ asset('images/empresas/logo_default.png') }}"
                         alt="preview" style="width:48px;height:48px;object-fit:contain;border-radius:8px;border:1px solid var(--fc-border);padding:4px"
                         onerror="this.src='{{ asset('images/logo.png') }}'">
                </div>
            </div>

            <div class="fc-form-row" style="gap:10px;align-items:center">
                <div class="fc-form-group" style="display:flex;align-items:center;gap:10px;margin-bottom:0">
                    <input type="hidden" name="es_corporativo" value="0">
                    <input type="checkbox" name="es_corporativo" id="es_corporativo" value="1"
                           {{ old('es_corporativo') ? 'checked' : '' }}
                           class="fc-checkbox">
                    <label for="es_corporativo" class="fc-label" style="margin:0">
                        Es empresa corporativa
                        <span class="fc-label-hint">(visible para todas las empresas)</span>
                    </label>
                </div>
                <div class="fc-form-group" style="display:flex;align-items:center;gap:10px;margin-bottom:0">
                    <input type="hidden" name="activo" value="0">
                    <input type="checkbox" name="activo" id="activo" value="1"
                           {{ old('activo', '1') ? 'checked' : '' }} class="fc-checkbox">
                    <label for="activo" class="fc-label" style="margin:0">Activa al crear</label>
                </div>
            </div>
        </div>

        {{-- Colores --}}
        <div class="fc-card" style="margin-top:20px">
            <div class="fc-card-title">Identidad visual</div>
            <p style="font-size:13px;color:var(--fc-text-muted);margin-bottom:16px">
                Estos colores se usarán en el sidebar, topbar, badges y avatares cuando un usuario de esta empresa esté logueado.
            </p>

            <div class="fc-form-row">
                <div class="fc-form-group">
                    <label class="fc-label">Color primario
                        <span class="fc-label-hint">Sidebar · Topbar accent</span>
                    </label>
                    <div class="fc-color-input-wrap">
                        <input type="color" name="color_primario_picker" id="cp1"
                               value="{{ old('color_primario','#1B3A6B') }}"
                               class="fc-color-picker"
                               oninput="syncColor(this,'color_primario','hex1')">
                        <input type="text" name="color_primario" id="hex1"
                               class="fc-input fc-color-hex"
                               value="{{ old('color_primario','#1B3A6B') }}" maxlength="7" placeholder="#1B3A6B"
                               oninput="syncHex(this,'cp1')">
                    </div>
                    @error('color_primario')<span class="fc-error">{{ $message }}</span>@enderror
                </div>

                <div class="fc-form-group">
                    <label class="fc-label">Color secundario
                        <span class="fc-label-hint">Botones activos · Badges</span>
                    </label>
                    <div class="fc-color-input-wrap">
                        <input type="color" name="color_secundario_picker" id="cp2"
                               value="{{ old('color_secundario','#2E5FA3') }}"
                               class="fc-color-picker"
                               oninput="syncColor(this,'color_secundario','hex2')">
                        <input type="text" name="color_secundario" id="hex2"
                               class="fc-input fc-color-hex"
                               value="{{ old('color_secundario','#2E5FA3') }}" maxlength="7" placeholder="#2E5FA3"
                               oninput="syncHex(this,'cp2')">
                    </div>
                    @error('color_secundario')<span class="fc-error">{{ $message }}</span>@enderror
                </div>

                <div class="fc-form-group">
                    <label class="fc-label">Color terciario
                        <span class="fc-label-hint">Fondos suaves · Hover</span>
                    </label>
                    <div class="fc-color-input-wrap">
                        <input type="color" name="color_terciario_picker" id="cp3"
                               value="{{ old('color_terciario','#D6E4F7') }}"
                               class="fc-color-picker"
                               oninput="syncColor(this,'color_terciario','hex3')">
                        <input type="text" name="color_terciario" id="hex3"
                               class="fc-input fc-color-hex"
                               value="{{ old('color_terciario','#D6E4F7') }}" maxlength="7" placeholder="#D6E4F7"
                               oninput="syncHex(this,'cp3')">
                    </div>
                    @error('color_terciario')<span class="fc-error">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>

        <div style="display:flex;gap:10px;margin-top:20px">
            <button type="submit" class="fc-btn fc-btn-primary">Crear empresa</button>
            <a href="{{ route('empresas.index') }}" class="fc-btn fc-btn-outline">Cancelar</a>
        </div>
    </form>

    {{-- Panel de preview en vivo --}}
    <div>
        <div class="fc-card" style="position:sticky;top:20px">
            <div class="fc-card-title">Vista previa</div>

            {{-- Mini sidebar preview --}}
            <div id="preview-sidebar"
                 style="border-radius:10px;padding:16px;margin-top:12px;transition:background .3s">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;padding-bottom:12px;border-bottom:1px solid rgba(255,255,255,0.12)">
                    <img id="preview-logo" src="{{ asset('images/empresas/logo_default.png') }}"
                         style="width:36px;height:36px;object-fit:contain;border-radius:6px;background:rgba(255,255,255,0.1);padding:4px"
                         onerror="this.src='{{ asset('images/logo.png') }}'">
                    <div>
                        <div id="preview-nombre" style="color:#f1f5f9;font-size:13px;font-weight:500">Nueva Empresa</div>
                        <div id="preview-siglas" style="color:rgba(255,255,255,0.4);font-size:11px">SIGLAS · Sistema QHSE</div>
                    </div>
                </div>

                {{-- Elementos activos preview --}}
                <div id="preview-nav-active"
                     style="padding:8px 12px;border-radius:6px;font-size:12px;color:#fff;margin-bottom:6px;transition:background .3s">
                    Dashboard
                </div>
                <div style="padding:8px 12px;border-radius:6px;font-size:12px;color:rgba(255,255,255,0.5);margin-bottom:6px">
                    Mis Carpetas
                </div>
                <div style="padding:8px 12px;border-radius:6px;font-size:12px;color:rgba(255,255,255,0.5)">
                    Usuarios
                </div>

                {{-- Badge preview --}}
                <div style="margin-top:16px;padding-top:12px;border-top:1px solid rgba(255,255,255,0.12)">
                    <div style="font-size:10px;color:rgba(255,255,255,0.3);margin-bottom:6px">Badge en el sistema</div>
                    <span id="preview-badge"
                          style="padding:3px 10px;border-radius:999px;font-size:11px;font-weight:500;transition:all .3s">
                        SIGLAS
                    </span>
                </div>

                {{-- Avatar preview --}}
                <div style="margin-top:12px;display:flex;align-items:center;gap:8px">
                    <div id="preview-avatar"
                         style="width:34px;height:34px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:600;color:#fff;border:2px solid rgba(255,255,255,0.2);transition:background .3s">
                        US
                    </div>
                    <div>
                        <div style="font-size:12px;color:#e2e8f0">Usuario Ejemplo</div>
                        <div style="font-size:10px;color:rgba(255,255,255,0.4)">Admin</div>
                    </div>
                </div>
            </div>

            {{-- Topbar accent strip --}}
            <div style="margin-top:12px">
                <div style="font-size:11px;color:var(--fc-text-muted);margin-bottom:6px">Acento en topbar</div>
                <div style="height:3px;border-radius:2px;width:100%;transition:background .3s" id="preview-accent"></div>
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
    if (/^#[0-9A-Fa-f]{6}$/.test(val)) {
        document.getElementById(pickerId).value = val;
    }
    updatePreview();
}

function updatePreview() {
    const bg     = document.getElementById('hex1').value || '#1B3A6B';
    const accent = document.getElementById('hex2').value || '#2E5FA3';
    const siglas = (document.getElementById('siglas').value || 'EMP').toUpperCase();
    const nombre = document.querySelector('[name="nombre"]').value || 'Nueva Empresa';
    const logo   = document.getElementById('logo-input').value || 'logo_default.png';

    // sidebar bg
    document.getElementById('preview-sidebar').style.background = bg;
    // nav active item
    document.getElementById('preview-nav-active').style.background = accent + '44';
    document.getElementById('preview-nav-active').style.color = accent;
    // badge
    const badge = document.getElementById('preview-badge');
    badge.style.background = accent + '22';
    badge.style.color = accent;
    badge.style.border = '1px solid ' + accent + '44';
    badge.textContent = siglas;
    // avatar
    document.getElementById('preview-avatar').style.background =
        'linear-gradient(135deg,' + bg + ',' + accent + ')';
    document.getElementById('preview-avatar').style.borderColor = accent + '66';
    // topbar accent
    document.getElementById('preview-accent').style.background =
        'linear-gradient(90deg,' + accent + ',' + bg + ')';
    // nombres
    document.getElementById('preview-nombre').textContent = nombre || 'Nueva Empresa';
    document.getElementById('preview-siglas').textContent = siglas + ' · Sistema QHSE';
    // logo
    const previewLogo = document.getElementById('preview-logo');
    previewLogo.src = '/images/empresas/' + logo;
}

// Sync logo preview
document.getElementById('logo-input').addEventListener('input', function() {
    document.getElementById('logo-preview').src = '/images/empresas/' + this.value;
    updatePreview();
});

// Sync siglas y nombre al preview
document.getElementById('siglas').addEventListener('input', function() {
    this.value = this.value.toUpperCase();
    updatePreview();
});
document.querySelector('[name="nombre"]').addEventListener('input', updatePreview);

// Init
updatePreview();
</script>
</x-app-layout>