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
                            <div class="fc-form-title">Solicitar acceso a un archivo</div>
                            <div class="fc-form-sub">Un administrador revisará tu solicitud y te notificará la decisión</div>
                        </div>
                    </div>

                    <form action="{{ route('solicitudes.store') }}" method="POST">
                        @csrf
                        <div class="fc-form-body">

                            {{-- Si viene con archivo_id preseleccionado --}}
                            @if(isset($archivo) && $archivo)
                            <input type="hidden" name="archivo_id" value="{{ $archivo->id }}">

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
                                        {{ strtoupper($archivo->extension) }} · {{ $archivo->tamanioFormateado() }} · Carpeta: {{ $archivo->carpeta->nombre }}
                                    </div>
                                </div>
                            </div>

                            @else
                            {{-- Selector de archivo libre --}}
                            <div class="fc-field">
                                <label for="archivo_id">Archivo al que solicitas acceso *</label>
                                <select id="archivo_id" name="archivo_id" required>
                                    <option value="">— Selecciona un archivo —</option>
                                    @if(isset($archivos))
                                        @foreach($archivos as $carpetaNombre => $archivosGrupo)
                                        <optgroup label="{{ $carpetaNombre }}">
                                            @foreach($archivosGrupo as $arch)
                                            <option value="{{ $arch->id }}" {{ old('archivo_id') == $arch->id ? 'selected' : '' }}>
                                                {{ $arch->nombre_original }}
                                            </option>
                                            @endforeach
                                        </optgroup>
                                        @endforeach
                                    @endif
                                </select>
                                @error('archivo_id')
                                <span class="fc-field-error">{{ $message }}</span>
                                @enderror
                            </div>
                            @endif

                            {{-- Motivo --}}
                            <div class="fc-field">
                                <label for="motivo">Motivo de la solicitud *</label>
                                <textarea id="motivo" name="motivo" rows="4"
                                    placeholder="Explica por qué necesitas acceso a este archivo...">{{ old('motivo') }}</textarea>
                                <div class="fc-field-hint">
                                    Describe el propósito del acceso. Esto ayuda al administrador a tomar una decisión informada.
                                </div>
                                @error('motivo')
                                <span class="fc-field-error">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Info del proceso --}}
                            <div style="background:rgba(99,102,241,.05);border:1px solid rgba(99,102,241,.15);border-radius:12px;padding:16px 18px">
                                <div style="font-size:13px;font-weight:600;color:#4f46e5;margin-bottom:8px;display:flex;align-items:center;gap:7px">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="#4f46e5"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                                    ¿Cómo funciona?
                                </div>
                                <div style="font-size:12px;color:#475569;line-height:1.7">
                                    1. Envías esta solicitud con el motivo de acceso.<br>
                                    2. Un administrador la revisa y decide aprobarla o rechazarla.<br>
                                    3. Recibirás una notificación con la decisión.<br>
                                    4. Si es aprobada, podrás descargar el archivo desde esta sección.
                                </div>
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
</x-app-layout>