<x-app-layout>
<div class="fc-wrapper">

    @include('components.sidebar')

    <div class="fc-main">
        <header class="fc-topbar">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#6366f1">
                <path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
            </svg>
            <span class="fc-topbar-title">Nueva Solicitud de Acceso</span>
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
                <a href="{{ route('solicitudes.index') }}" class="fc-bread-item">Solicitudes</a>
                <span class="fc-bread-sep">›</span>
                <span class="fc-bread-current">Nueva solicitud</span>
            </div>

            @if($errors->any())
            <div class="fc-flash error">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="#dc2626"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                {{ $errors->first() }}
            </div>
            @endif

            <div class="fc-form-wrap">
                <div class="fc-form-card">

                    <div class="fc-form-header">
                        <div class="fc-form-header-icon" style="background:rgba(99,102,241,.1)">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="#4f46e5">
                                <path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="fc-form-title">Solicitar acceso a un recurso</div>
                            <div class="fc-form-sub">
                                Solicita acceso a archivos o carpetas de otras empresas del grupo.
                                Un administrador revisará tu solicitud.
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('solicitudes.store') }}" method="POST">
                        @csrf
                        <div class="fc-form-body">

                            {{-- Empresa objetivo (solo cuando no hay recurso preseleccionado) --}}
                            @if(!(isset($archivo) && $archivo) && !(isset($carpeta) && $carpeta))
                            <div class="fc-field">
                                <label class="fc-label" for="empresa_objetivo_id">
                                    Empresa a la que solicitas acceso <span style="color:#dc2626">*</span>
                                </label>
                                <select id="empresa_objetivo_id" name="empresa_objetivo_id" class="fc-input" required>
                                    <option value="">— Selecciona una empresa —</option>
                                    @foreach($empresas as $emp)
                                        @if($emp->id !== Auth::user()->empresa_id)
                                        <option value="{{ $emp->id }}"
                                                {{ old('empresa_objetivo_id') == $emp->id ? 'selected' : '' }}>
                                            {{ $emp->nombre }}{{ $emp->siglas ? ' ('.$emp->siglas.')' : '' }}
                                        </option>
                                        @endif
                                    @endforeach
                                </select>
                                @error('empresa_objetivo_id')
                                <span class="fc-field-error">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Selector dinámico de carpeta (se muestra al elegir empresa) --}}
                            <div class="fc-field" id="carpetaSelectorWrap" style="display:none">
                                <label class="fc-label" for="carpeta_id">
                                    Carpeta específica
                                    <span style="font-weight:400;color:var(--fc-text-muted)">(opcional)</span>
                                </label>
                                <select id="carpeta_id" name="carpeta_id" class="fc-input">
                                    <option value="">— Acceso general a toda la empresa —</option>
                                </select>
                                <div class="fc-field-hint">
                                    Selecciona una carpeta si quieres acceso a un área específica, o deja en blanco para acceso general.
                                </div>
                            </div>

                            <input type="hidden" name="archivo_id" value="">
                            @endif

                            {{-- Recurso preseleccionado: Archivo --}}
                            @if(isset($archivo) && $archivo)
                            <input type="hidden" name="archivo_id"          value="{{ $archivo->id }}">
                            <input type="hidden" name="empresa_objetivo_id" value="{{ $archivo->carpeta->empresa_id ?? '' }}">
                            <div class="fc-info-chip">
                                <div class="fc-info-chip-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="#4f46e5">
                                        <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="fc-info-chip-label">Archivo solicitado</div>
                                    <div class="fc-info-chip-name">{{ $archivo->nombre_original }}</div>
                                    <div class="fc-info-chip-sub">
                                        {{ strtoupper($archivo->extension) }}
                                        &middot; {{ $archivo->tamanioFormateado() }}
                                        &middot; Carpeta: {{ $archivo->carpeta->nombre ?? '—' }}
                                    </div>
                                </div>
                            </div>

                            {{-- Recurso preseleccionado: Carpeta --}}
                            @elseif(isset($carpeta) && $carpeta)
                            <input type="hidden" name="carpeta_id"          value="{{ $carpeta->id }}">
                            <input type="hidden" name="empresa_objetivo_id" value="{{ $carpeta->empresa_id }}">
                            <div class="fc-info-chip">
                                <div class="fc-info-chip-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="#4f46e5">
                                        <path d="M20 6h-8l-2-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="fc-info-chip-label">Carpeta solicitada</div>
                                    <div class="fc-info-chip-name">{{ $carpeta->nombre }}</div>
                                    <div class="fc-info-chip-sub">{{ $carpeta->path }}</div>
                                </div>
                            </div>
                            @endif

                            {{-- Tipo de acceso --}}
                            <div class="fc-field">
                                <label class="fc-label">
                                    Tipo de acceso solicitado <span style="color:#dc2626">*</span>
                                </label>
                                <div style="display:flex;gap:10px;margin-top:6px" id="tipoAccesoGroup">
                                    @foreach([
                                        'Lectura'   => ['path'=>'M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z', 'hint'=>'Solo visualización del recurso'],
                                        'Descargar' => ['path'=>'M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z', 'hint'=>'Ver y descargar archivos'],
                                    ] as $val => $meta)
                                    @php $seleccionado = old('tipo_acceso', 'Lectura') === $val; @endphp
                                    <label class="tipo-acceso-card"
                                           data-value="{{ $val }}"
                                           style="flex:1;display:flex;align-items:center;gap:12px;padding:14px 16px;
                                                  border:1.5px solid {{ $seleccionado ? '#6366f1' : 'var(--fc-border)' }};
                                                  border-radius:10px;cursor:pointer;transition:border-color .15s,background .15s;
                                                  background:{{ $seleccionado ? 'rgba(99,102,241,.04)' : 'transparent' }}">
                                        <input type="radio" name="tipo_acceso" value="{{ $val }}"
                                               {{ $seleccionado ? 'checked' : '' }}
                                               style="accent-color:#6366f1;flex-shrink:0">
                                        <div style="display:flex;align-items:center;gap:10px">
                                            <div style="width:32px;height:32px;border-radius:8px;
                                                        background:{{ $seleccionado ? 'rgba(99,102,241,.12)' : 'rgba(100,116,139,.08)' }};
                                                        display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:background .15s"
                                                 class="tipo-icon">
                                                <svg width="15" height="15" viewBox="0 0 24 24"
                                                     fill="{{ $seleccionado ? '#6366f1' : '#64748b' }}"
                                                     class="tipo-svg">
                                                    <path d="{{ $meta['path'] }}"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <div style="font-size:13px;font-weight:600;color:var(--fc-text)">{{ $val }}</div>
                                                <div style="font-size:11px;color:var(--fc-text-muted)">{{ $meta['hint'] }}</div>
                                            </div>
                                        </div>
                                    </label>
                                    @endforeach
                                </div>
                                @error('tipo_acceso')
                                <span class="fc-field-error">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Justificación --}}
                            <div class="fc-field">
                                <label class="fc-label" for="razon">
                                    Justificación de la solicitud <span style="color:#dc2626">*</span>
                                </label>
                                <textarea id="razon" name="razon" rows="4" class="fc-input" required
                                          minlength="10" maxlength="1000"
                                          placeholder="Explica detalladamente por qué necesitas acceso a este recurso...">{{ old('razon') }}</textarea>
                                <div class="fc-field-hint">
                                    Mínimo 10 caracteres · Máximo 1,000. El administrador verá esta justificación.
                                </div>
                                @error('razon')
                                <span class="fc-field-error">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- ¿Cómo funciona? --}}
                            <div style="background:rgba(99,102,241,.05);border:1px solid rgba(99,102,241,.15);border-radius:12px;padding:16px 18px">
                                <div style="font-size:13px;font-weight:600;color:#4f46e5;margin-bottom:10px;display:flex;align-items:center;gap:7px">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="#4f46e5"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                                    ¿Cómo funciona?
                                </div>
                                <ol style="margin:0;padding-left:18px;font-size:12px;color:#475569;line-height:2">
                                    <li>Envías esta solicitud con tu justificación.</li>
                                    <li>Un administrador de la empresa objetivo la revisa.</li>
                                    <li>Si es aprobada, el sistema otorga el permiso automáticamente.</li>
                                    <li>Si es rechazada, recibirás el motivo del rechazo por notificación.</li>
                                </ol>
                            </div>

                        </div>{{-- /fc-form-body --}}

                        <div class="fc-form-footer">
                            <a href="{{ route('solicitudes.index') }}" class="fc-btn fc-btn-outline">Cancelar</a>
                            <button type="submit" class="fc-btn fc-btn-primary">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
                                Enviar solicitud
                            </button>
                        </div>
                    </form>

                </div>{{-- /fc-form-card --}}
            </div>{{-- /fc-form-wrap --}}

        </div>{{-- /fc-content --}}
    </div>{{-- /fc-main --}}
</div>{{-- /fc-wrapper --}}

<script>
(function () {
    // ── Tipo de acceso: cards interactivos ────────────────────────────────
    const cards = document.querySelectorAll('.tipo-acceso-card');

    function activateCard(card) {
        cards.forEach(c => {
            const isThis = c === card;
            c.style.borderColor = isThis ? '#6366f1' : 'var(--fc-border)';
            c.style.background   = isThis ? 'rgba(99,102,241,.04)' : 'transparent';
            const icon = c.querySelector('.tipo-icon');
            const svg  = c.querySelector('.tipo-svg');
            if (icon) icon.style.background = isThis ? 'rgba(99,102,241,.12)' : 'rgba(100,116,139,.08)';
            if (svg)  svg.setAttribute('fill', isThis ? '#6366f1' : '#64748b');
        });
    }

    cards.forEach(card => {
        const radio = card.querySelector('input[type="radio"]');
        if (radio && radio.checked) activateCard(card);
        card.addEventListener('click', () => activateCard(card));
        radio && radio.addEventListener('change', () => { if (radio.checked) activateCard(card); });
    });

    // ── Selector dinámico de carpetas ─────────────────────────────────────
    const empresaSelect  = document.getElementById('empresa_objetivo_id');
    const carpetaWrap    = document.getElementById('carpetaSelectorWrap');
    const carpetaSelect  = document.getElementById('carpeta_id');

    if (empresaSelect && carpetaWrap && carpetaSelect) {
        const oldEmpresaId = '{{ old('empresa_objetivo_id') }}';
        const oldCarpetaId = '{{ old('carpeta_id') }}';

        function cargarCarpetas(empresaId, preselect) {
            carpetaSelect.innerHTML = '<option value="">Cargando carpetas…</option>';
            carpetaSelect.disabled  = true;

            fetch('/solicitudes/carpetas-empresa/' + empresaId, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(data => {
                carpetaSelect.innerHTML = '<option value="">— Acceso general a toda la empresa —</option>';
                data.forEach(c => {
                    const opt    = document.createElement('option');
                    opt.value    = c.id;
                    opt.textContent = c.nombre + (c.path ? ' (' + c.path + ')' : '');
                    if (preselect && String(c.id) === String(preselect)) opt.selected = true;
                    carpetaSelect.appendChild(opt);
                });
                carpetaSelect.disabled = false;
                carpetaWrap.style.display = '';
            })
            .catch(() => {
                carpetaSelect.innerHTML = '<option value="">— No se pudieron cargar las carpetas —</option>';
                carpetaSelect.disabled  = false;
                carpetaWrap.style.display = '';
            });
        }

        empresaSelect.addEventListener('change', function () {
            if (this.value) {
                cargarCarpetas(this.value, null);
            } else {
                carpetaWrap.style.display = 'none';
                carpetaSelect.innerHTML   = '<option value="">— Acceso general a toda la empresa —</option>';
            }
        });

        // Si hay un valor previo (validación fallida) recarga las carpetas
        if (oldEmpresaId) {
            cargarCarpetas(oldEmpresaId, oldCarpetaId);
        }
    }
})();
</script>
</x-app-layout>
