<x-app-layout>
    <div class="fc-wrapper">
        @include('components.sidebar')
        <div class="fc-main">
            <header class="fc-topbar">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="#6366f1">
                    <path d="M15 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm-9-2V7H4v3H1v2h3v3h2v-3h3v-2H6zm9 4c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                </svg>
                <span class="fc-topbar-title">Nuevo Usuario</span>
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
                    <span class="fc-bread-current">Nuevo usuario</span>
                </div>

                <div class="fc-form-wrap" style="max-width:600px">
                    <div class="fc-form-card">
                        <div class="fc-form-header">
                            <div class="fc-form-header-icon" style="background:rgba(79,70,229,0.1)">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="#4f46e5">
                                    <path d="M15 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm-9-2V7H4v3H1v2h3v3h2v-3h3v-2H6zm9 4c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="fc-form-title">Crear nuevo usuario</div>
                                <div class="fc-form-sub">El usuario recibirá acceso al sistema con las credenciales que configures</div>
                            </div>
                        </div>

                        <form action="{{ route('usuarios.store') }}" method="POST">
                            @csrf
                            <div class="fc-form-body">

                                {{-- Empresa --}}
                                <div class="fc-field">
                                    <label for="empresa_id">Empresa *</label>
                                    @if($empresas->count() > 1)
                                    <select id="empresa_id" name="empresa_id" required>
                                        <option value="">— Selecciona empresa —</option>
                                        @foreach($empresas as $emp)
                                        <option value="{{ $emp->id }}" {{ old('empresa_id') == $emp->id ? 'selected' : '' }}>
                                            {{ $emp->es_corporativo ? '🏢' : '🏭' }} {{ $emp->nombre }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @else
                                    <select id="empresa_id" name="empresa_id" required>
                                        @foreach($empresas as $emp)
                                        <option value="{{ $emp->id }}" selected>{{ $emp->nombre }}</option>
                                        @endforeach
                                    </select>
                                    @endif
                                    @error('empresa_id') <span class="fc-field-error">{{ $message }}</span> @enderror
                                </div>

                                {{-- Nombre / Paterno / Materno en fila --}}
                                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px">
                                    <div class="fc-field">
                                        <label for="nombre">Nombre(s) *</label>
                                        <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}" required placeholder="Juan">
                                        @error('nombre') <span class="fc-field-error">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="fc-field">
                                        <label for="paterno">Ap. Paterno *</label>
                                        <input type="text" id="paterno" name="paterno" value="{{ old('paterno') }}" required placeholder="García">
                                        @error('paterno') <span class="fc-field-error">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="fc-field">
                                        <label for="materno">Ap. Materno</label>
                                        <input type="text" id="materno" name="materno" value="{{ old('materno') }}" placeholder="López">
                                        @error('materno') <span class="fc-field-error">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                {{-- Email --}}
                                <div class="fc-field">
                                    <label for="email">Correo electrónico *</label>
                                    <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="usuario@empresa.com">
                                    @error('email') <span class="fc-field-error">{{ $message }}</span> @enderror
                                </div>

                                {{-- Rol y Departamento --}}
                                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
                                    <div class="fc-field">
                                        <label for="rol">Rol *</label>
                                        <select id="rol" name="rol" required>
                                            <option value="">— Selecciona rol —</option>
                                            @foreach($roles as $r)
                                            <option value="{{ $r }}" {{ old('rol') == $r ? 'selected' : '' }}>{{ $r }}</option>
                                            @endforeach
                                        </select>
                                        @error('rol') <span class="fc-field-error">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="fc-field">
                                        <label for="departamento">Departamento</label>
                                        <input type="text" id="departamento" name="departamento" value="{{ old('departamento') }}" placeholder="QHSE, Operaciones...">
                                        @error('departamento') <span class="fc-field-error">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                {{-- Contraseña --}}
                                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
                                    <div class="fc-field">
                                        <label for="password">Contraseña *</label>
                                        <input type="password" id="password" name="password" required placeholder="Mínimo 8 caracteres">
                                        @error('password') <span class="fc-field-error">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="fc-field">
                                        <label for="password_confirmation">Confirmar contraseña *</label>
                                        <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Repite la contraseña">
                                    </div>
                                </div>

                                {{-- Activo --}}
                                <div class="fc-field">
                                    <label>Estado inicial</label>
                                    <label class="fc-checkbox-wrap">
                                        <input type="hidden" name="es_activo" value="0">
                                        <input type="checkbox" name="es_activo" value="1" {{ old('es_activo', '1') ? 'checked' : '' }}>
                                        <div>
                                            <div class="fc-checkbox-label">✅ Usuario activo</div>
                                            <div class="fc-checkbox-hint">El usuario podrá iniciar sesión inmediatamente</div>
                                        </div>
                                    </label>
                                </div>

                            </div>
                            <div class="fc-form-footer">
                                <a href="{{ route('usuarios.index') }}" class="fc-btn fc-btn-outline">Cancelar</a>
                                <button type="submit" class="fc-btn fc-btn-primary">Crear usuario</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>