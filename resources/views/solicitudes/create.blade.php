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
                <a href="{{ route('solicitudes.index') }}" class="fc-bread-item">📨 Solicitudes</a>
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

                            {{-- Empresa objetivo --}}
                            <div class="fc-field">
                                <label for="empresa_objetivo_id">Empresa a la que solicitas acceso *</label>
                                <select id="empresa_objetivo_id" name="empresa_objetivo_id" required>
                                    <option value="">— Selecciona empresa —</option>
                                    @foreach($empresas as $emp)
                                        {{-- No puede solicitar a su propia empresa --}}
                                        @if($emp->id !== Auth::user()->empresa_id)
                                        <option value="{{ $emp->id }}"
                                                {{ old('empresa_objetivo_id') == $emp->id ? 'selected' : '' }}>
                                            {{ $emp->es_corporativo ? '🏢' : '🏭' }} {{ $emp->nombre }}
                                        </option>
                                        @endif
                                    @endforeach
                                </select>
                                @error('empresa_objetivo_id')
                                <span class="fc-field-error">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Archivo preseleccionado --}}
                            @if(isset($archivo) && $archivo)
                            <input type="hidden" name="archivo_id" value="{{ $archivo->id }}">
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
                                        {{ strtoupper($archivo->extension) }} · {{ $archivo->tamanioFormateado() }}
                                        · Carpeta: {{ $archivo->carpeta->nombre ?? '—' }}
                                    </div>
                                </div>
                            </div>

                            @elseif(isset($carpeta) && $carpeta)
                            <input type="hidden" name="carpeta_id" value="{{ $carpeta->id }}">
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

                            @else
                            {{-- Sin preselección: mostrar selector de archivo_id o carpeta_id vacíos --}}
                            <input type="hidden" name="archivo_id" value="">
                            <input type="hidden" name="carpeta_id" value="">
                            @endif

                            {{-- Tipo de acceso --}}
                            <div class="fc-field">
                                <label>Tipo de acceso solicitado *</label>
                                <div style="display:flex;gap:10px;margin-top:6px">
                                    @foreach(['Lectura' => ['👁', 'Solo ver el recurso'], 'Descargar' => ['⬇', 'Ver y descargar archivos']] as $val => [$icono, $hint])
                                    <label style="flex:1;display:flex;align-items:center;gap:10px;padding:12px 14px;border:1.5px solid {{ old('tipo_acceso','Lectura') === $val ? '#6366f1' : 'var(--fc-border)' }};border-radius:10px;cursor:pointer;transition:border-color .15s"
                                           onclick="this.parentElement.querySelectorAll('label').forEach(l=>l.style.borderColor='var(--fc-border)');this.style.borderColor='#6366f1'">
                                        <input type="radio" name="tipo_acceso" value="{{ $val }}"
                                               {{ old('tipo_acceso','Lectura') === $val ? 'checked' : '' }}
                                               style="accent-color:#6366f1">
                                        <div>
                                            <div style="font-size:13px;font-weight:600;color:var(--fc-text)">{{ $icono }} {{ $val }}</div>
                                            <div style="font-size:11px;color:var(--fc-text-muted)">{{ $hint }}</div>
                                        </div>
                                    </label>
                                    @endforeach
                                </div>
                                @error('tipo_acceso')
                                <span class="fc-field-error">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Razón: campo correcto es 'razon' (mínimo 10 chars) --}}
                            <div class="fc-field">
                                <label for="razon">Justificación de la solicitud *</label>
                                <textarea id="razon" name="razon" rows="4" required
                                    minlength="10" maxlength="1000"
                                    placeholder="Explica detalladamente por qué necesitas acceso a este recurso...">{{ old('razon') }}</textarea>
                                <div class="fc-field-hint">
                                    Mínimo 10 caracteres. Máximo 1,000. Esta justificación es revisada por el administrador.
                                </div>
                                @error('razon')
                                <span class="fc-field-error">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Info del proceso --}}
                            <div style="background:rgba(99,102,241,.05);border:1px solid rgba(99,102,241,.15);border-radius:12px;padding:16px 18px">
                                <div style="font-size:13px;font-weight:600;color:#4f46e5;margin-bottom:8px;display:flex;align-items:center;gap:7px">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="#4f46e5"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                                    ¿Cómo funciona?
                                </div>
                                <div style="font-size:12px;color:#475569;line-height:1.8">
                                    1. Envías esta solicitud con tu justificación.<br>
                                    2. Un administrador de la empresa objetivo la revisa.<br>
                                    3. Si es aprobada, el sistema otorga el permiso automáticamente.<br>
                                    4. Si es rechazada, recibirás el motivo del rechazo.
                                </div>
                            </div>

                        </div>

                        <div class="fc-form-footer">
                            <a href="{{ route('solicitudes.index') }}" class="fc-btn fc-btn-outline">Cancelar</a>
                            <button type="submit" class="fc-btn fc-btn-primary">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
                                Enviar solicitud
                            </button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>
</x-app-layout>