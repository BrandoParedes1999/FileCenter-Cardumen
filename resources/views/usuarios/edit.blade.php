<x-app-layout>
    <div class="fc-wrapper">
        @include('components.sidebar')
        <div class="fc-main">
            <header class="fc-topbar">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="#6366f1">
                    <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                </svg>
                <span class="fc-topbar-title">Editar: {{ $usuario->nombre_completo }}</span>
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
                    <a href="{{ route('usuarios.index') }}" class="fc-bread-item">👥 Usuarios</a>
                    <span class="fc-bread-sep">›</span>
                    <a href="{{ route('usuarios.show', $usuario) }}" class="fc-bread-item">{{ $usuario->nombre_completo }}</a>
                    <span class="fc-bread-sep">›</span>
                    <span class="fc-bread-current">Editar</span>
                </div>

                <div class="fc-form-wrap" style="max-width:600px">
                    <div class="fc-form-card">
                        <div class="fc-form-header">
                            <div class="fc-form-header-icon" style="background:rgba(79,70,229,0.1)">
                                <div style="width:42px;height:42px;border-radius:50%;background:linear-gradient(135deg,#4f46e5,#7c3aed);display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:700;color:#fff">
                                    {{ strtoupper(substr($usuario->nombre,0,1)) }}{{ strtoupper(substr($usuario->paterno,0,1)) }}
                                </div>
                            </div>
                            <div>
                                <div class="fc-form-title">{{ $usuario->nombre_completo }}</div>
                                <div class="fc-form-sub">{{ $usuario->email }} · {{ $usuario->empresa->nombre ?? '—' }}</div>
                            </div>
                        </div>

                        <form action="{{ route('usuarios.update', $usuario) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="fc-form-body">

                                {{-- Empresa --}}
                                <div class="fc-field">
                                    <label for="empresa_id">Empresa *</label>
                                    <select id="empresa_id" name="empresa_id" required>
                                        @foreach($empresas as $emp)
                                        <option value="{{ $emp->id }}" {{ old('empresa_id', $usuario->empresa_id) == $emp->id ? 'selected' : '' }}>
                                            {{ $emp->es_corporativo ? '🏢' : '🏭' }} {{ $emp->nombre }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('empresa_id') <span class="fc-field-error">{{ $message }}</span> @enderror
                                </div>

                                {{-- Nombre --}}
                                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px">
                                    <div class="fc-field">
                                        <label for="nombre">Nombre(s) *</label>
                                        <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $usuario->nombre) }}" required>
                                        @error('nombre') <span class="fc-field-error">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="fc-field">
                                        <label for="paterno">Ap. Paterno *</label>
                                        <input type="text" id="paterno" name="paterno" value="{{ old('paterno', $usuario->paterno) }}" required>
                                        @error('paterno') <span class="fc-field-error">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="fc-field">
                                        <label for="materno">Ap. Materno</label>
                                        <input type="text" id="materno" name="materno" value="{{ old('materno', $usuario->materno) }}">
                                        @error('materno') <span class="fc-field-error">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                {{-- Email --}}
                                <div class="fc-field">
                                    <label for="email">Correo electrónico *</label>
                                    <input type="email" id="email" name="email" value="{{ old('email', $usuario->email) }}" required>
                                    @error('email') <span class="fc-field-error">{{ $message }}</span> @enderror
                                </div>

                                {{-- Rol y Departamento --}}
                                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
                                    <div class="fc-field">
                                        <label for="rol">Rol *</label>
                                        <select id="rol" name="rol" required {{ $usuario->rol === 'Superadmin' && Auth::user()->rol !== 'Superadmin' ? 'disabled' : '' }}>
                                            @foreach($roles as $r)
                                            <option value="{{ $r }}" {{ old('rol', $usuario->rol) == $r ? 'selected' : '' }}>{{ $r }}</option>
                                            @endforeach
                                        </select>
                                        @error('rol') <span class="fc-field-error">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="fc-field">
                                        <label for="departamento">Departamento</label>
                                        <input type="text" id="departamento" name="departamento" value="{{ old('departamento', $usuario->departamento) }}" placeholder="QHSE, Operaciones...">
                                    </div>
                                </div>

                                {{-- Nueva contraseña (opcional) --}}
                                <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:16px 18px">
                                    <div style="font-size:12px;font-weight:700;color:#374151;margin-bottom:12px;text-transform:uppercase;letter-spacing:.06em">
                                        Cambiar contraseña <span style="font-size:11px;color:#94a3b8;font-weight:400;text-transform:none">(dejar vacío para no cambiarla)</span>
                                    </div>
                                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
                                        <div class="fc-field">
                                            <label for="password">Nueva contraseña</label>
                                            <input type="password" id="password" name="password" placeholder="Mínimo 8 caracteres">
                                            @error('password') <span class="fc-field-error">{{ $message }}</span> @enderror
                                        </div>
                                        <div class="fc-field">
                                            <label for="password_confirmation">Confirmar</label>
                                            <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Repite la contraseña">
                                        </div>
                                    </div>
                                </div>

                                {{-- Estado --}}
                                @if($usuario->id !== Auth::id())
                                <div class="fc-field">
                                    <label>Estado</label>
                                    <label class="fc-checkbox-wrap">
                                        <input type="hidden" name="es_activo" value="0">
                                        <input type="checkbox" name="es_activo" value="1" {{ old('es_activo', $usuario->es_activo) ? 'checked' : '' }}>
                                        <div>
                                            <div class="fc-checkbox-label">Usuario activo</div>
                                            <div class="fc-checkbox-hint">Si se desactiva, el usuario no podrá iniciar sesión</div>
                                        </div>
                                    </label>
                                </div>
                                @endif

                            </div>
                            <div class="fc-form-footer">
                                <a href="{{ route('usuarios.show', $usuario) }}" class="fc-btn fc-btn-outline">Cancelar</a>
                                <button type="submit" class="fc-btn fc-btn-primary">Guardar cambios</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
