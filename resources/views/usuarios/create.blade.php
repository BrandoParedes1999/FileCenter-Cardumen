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

                {{-- Resumen de roles disponibles --}}
                @if(Auth::user()->rol === 'Superadmin')
                <div style="background:rgba(99,102,241,.04);border:1px solid rgba(99,102,241,.15);
                            border-radius:12px;padding:14px 18px;margin-bottom:18px;max-width:600px">
                    <div style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;
                                letter-spacing:.06em;margin-bottom:10px">
                        Guía de roles del sistema
                    </div>
                    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:8px;font-size:11px">
                        <div style="display:flex;gap:7px;align-items:flex-start">
                            <span style="background:#7c3aed;color:#fff;padding:1px 7px;border-radius:10px;font-size:10px;font-weight:700;flex-shrink:0;margin-top:1px">Superadmin</span>
                            <span style="color:#64748b">Acceso total al sistema. Gestiona empresas, usuarios y permisos globales.</span>
                        </div>
                        <div style="display:flex;gap:7px;align-items:flex-start">
                            <span style="background:#06b6d4;color:#fff;padding:1px 7px;border-radius:10px;font-size:10px;font-weight:700;flex-shrink:0;margin-top:1px">Aux_QHSE</span>
                            <span style="color:#64748b">Como Superadmin pero sin poder eliminar cuentas Superadmin.</span>
                        </div>
                        <div style="display:flex;gap:7px;align-items:flex-start">
                            <span style="background:#4338ca;color:#fff;padding:1px 7px;border-radius:10px;font-size:10px;font-weight:700;flex-shrink:0;margin-top:1px">Admin</span>
                            <span style="color:#64748b">Gestiona su empresa: usuarios, carpetas y permisos dentro de ella.</span>
                        </div>
                        <div style="display:flex;gap:7px;align-items:flex-start">
                            <span style="background:#059669;color:#fff;padding:1px 7px;border-radius:10px;font-size:10px;font-weight:700;flex-shrink:0;margin-top:1px">Gerente</span>
                            <span style="color:#64748b">Aprueba subidas y accesos de su empresa. No puede gestionar usuarios.</span>
                        </div>
                        <div style="display:flex;gap:7px;align-items:flex-start">
                            <span style="background:#f59e0b;color:#fff;padding:1px 7px;border-radius:10px;font-size:10px;font-weight:700;flex-shrink:0;margin-top:1px">Auxiliar</span>
                            <span style="color:#64748b">Puede ver, subir y descargar archivos. No puede borrar ni editar.</span>
                        </div>
                        <div style="display:flex;gap:7px;align-items:flex-start">
                            <span style="background:#94a3b8;color:#fff;padding:1px 7px;border-radius:10px;font-size:10px;font-weight:700;flex-shrink:0;margin-top:1px">Empleado</span>
                            <span style="color:#64748b">Solo lectura y descarga. Acceso mínimo al sistema.</span>
                        </div>
                    </div>
                </div>
                @endif

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
                                    <label for="empresa_id">
                                        Empresa *
                                        <span class="fc-help" data-tip="La empresa a la que pertenece el usuario. Define qué carpetas y archivos puede ver según sus permisos.">?</span>
                                    </label>
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

                                {{-- Nombre / Paterno / Materno --}}
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
                                    <label for="email">
                                        Correo electrónico *
                                        <span class="fc-help" data-tip="Se usa como nombre de usuario para iniciar sesión. Debe ser único en todo el sistema.">?</span>
                                    </label>
                                    <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="usuario@empresa.com">
                                    @error('email') <span class="fc-field-error">{{ $message }}</span> @enderror
                                </div>

                                {{-- Rol y Departamento --}}
                                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
                                    <div class="fc-field">
                                        <label for="rol">
                                            Rol *
                                            <span class="fc-help" data-tip="Define los permisos generales del usuario:&#10;• Admin → gestiona su empresa&#10;• Gerente → aprueba subidas y accesos&#10;• Auxiliar → sube y descarga archivos&#10;• Empleado → solo lectura y descarga">?</span>
                                        </label>
                                        <select id="rol" name="rol" required>
                                            <option value="">— Selecciona rol —</option>
                                            @foreach($roles as $r)
                                            <option value="{{ $r }}" {{ old('rol') == $r ? 'selected' : '' }}>{{ $r }}</option>
                                            @endforeach
                                        </select>
                                        @error('rol') <span class="fc-field-error">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="fc-field">
                                        <label for="departamento">
                                            Departamento
                                            <span class="fc-help" data-tip="Área o departamento al que pertenece. Opcional, solo informativo.">?</span>
                                        </label>
                                        <input type="text" id="departamento" name="departamento" value="{{ old('departamento') }}" placeholder="QHSE, Operaciones...">
                                        @error('departamento') <span class="fc-field-error">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                {{-- Contraseña --}}
                                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
                                    <div class="fc-field">
                                        <label for="password">
                                            Contraseña *
                                            <span class="fc-help" data-tip="Mínimo 8 caracteres.&#10;Debe incluir letras y números.&#10;Ej: Empresa2024">?</span>
                                        </label>
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
                                    <label>
                                        Estado inicial
                                        <span class="fc-help" data-tip="Si lo dejas activo, el usuario puede iniciar sesión de inmediato. Si lo desactivas, no podrá acceder hasta que lo actives manualmente.">?</span>
                                    </label>
                                    <label class="fc-checkbox-wrap">
                                        <input type="hidden" name="es_activo" value="0">
                                        <input type="checkbox" name="es_activo" value="1" {{ old('es_activo', '1') == '1' ? 'checked' : '' }}>
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
