<x-app-layout>
    <style>
    .fc-profile-header {
        background: #fff; border: 1px solid #e2e8f0; border-radius: 16px;
        overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,0.05);
        margin-bottom: 20px;
    }
    .fc-profile-cover {
        height: 80px;
        background: linear-gradient(135deg, #1e1b4b 0%, #312e81 60%, #4338ca 100%);
    }
    .fc-profile-body {
        padding: 0 28px 24px; display: flex; align-items: flex-end;
        gap: 20px; margin-top: -32px;
    }
    .fc-profile-avatar {
        width: 72px; height: 72px; border-radius: 50%;
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
        border: 4px solid #fff;
        display: flex; align-items: center; justify-content: center;
        font-size: 22px; font-weight: 700; color: #fff; flex-shrink: 0;
        box-shadow: 0 4px 16px rgba(79,70,229,0.3);
    }
    .fc-profile-info { flex: 1; padding-bottom: 4px; }
    .fc-profile-name { font-size: 20px; font-weight: 700; color: #1e293b; margin-bottom: 4px; }
    .fc-profile-email { font-size: 13px; color: #94a3b8; margin-bottom: 8px; }
    .fc-profile-badges { display: flex; gap: 8px; flex-wrap: wrap; }
    .fc-profile-actions { display: flex; gap: 8px; padding-bottom: 4px; }

    .fc-act-item {
        display: flex; align-items: flex-start; gap: 12px;
        padding: 12px 0; border-bottom: 1px solid #f8fafc;
    }
    .fc-act-item:last-child { border-bottom: none; }
    .fc-act-icon {
        width: 30px; height: 30px; border-radius: 8px;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .fc-act-desc { font-size: 13px; color: #475569; line-height: 1.5; }
    .fc-act-desc strong { color: #1e293b; font-weight: 600; }
    .fc-act-time { font-size: 11px; color: #94a3b8; margin-top: 2px; }

    .fc-rol-badge {
        font-size: 11px; font-weight: 700; padding: 3px 10px;
        border-radius: 20px; letter-spacing: .05em; text-transform: uppercase;
    }
    .rol-superadmin { background: rgba(124,58,237,0.12); color: #6d28d9; }
    .rol-aux_qhse   { background: rgba(6,182,212,0.12);  color: #0e7490; }
    .rol-admin      { background: rgba(79,70,229,0.12);  color: #4338ca; }
    .rol-gerente    { background: rgba(5,150,105,0.12);  color: #065f46; }
    .rol-auxiliar   { background: rgba(245,158,11,0.12); color: #92400e; }
    .rol-empleado   { background: rgba(100,116,139,0.12);color: #334155; }
    </style>

    <div class="fc-wrapper">
        @include('components.sidebar')
        <div class="fc-main">
            <header class="fc-topbar">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="#6366f1">
                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                </svg>
                <span class="fc-topbar-title">{{ $usuario->nombre_completo }}</span>
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
                    <span class="fc-bread-current">{{ $usuario->nombre_completo }}</span>
                </div>

                @if(session('success'))
                <div class="fc-flash success">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="#059669"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                    {{ session('success') }}
                </div>
                @endif
                @if($errors->any())
                <div class="fc-flash error">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="#dc2626"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                    {{ $errors->first('error') }}
                </div>
                @endif

                {{-- Header perfil --}}
                <div class="fc-profile-header">
                    <div class="fc-profile-cover"></div>
                    <div class="fc-profile-body">
                        <div class="fc-profile-avatar">
                            {{ strtoupper(substr($usuario->nombre,0,1)) }}{{ strtoupper(substr($usuario->paterno,0,1)) }}
                        </div>
                        <div class="fc-profile-info">
                            <div class="fc-profile-name">{{ $usuario->nombre_completo }}</div>
                            <div class="fc-profile-email">{{ $usuario->email }}</div>
                            <div class="fc-profile-badges">
                                <span class="fc-rol-badge rol-{{ strtolower(str_replace('_','-',$usuario->rol)) }}">
                                    {{ $usuario->rol }}
                                </span>
                                @if($usuario->empresa)
                                <span style="font-size:11px;font-weight:600;padding:3px 10px;border-radius:20px;background:{{ $usuario->empresa->color_secundario ?? '#f1f5f9' }};color:{{ $usuario->empresa->color_primario ?? '#475569' }}">
                                    {{ $usuario->empresa->nombre }}
                                </span>
                                @endif
                                @if($usuario->departamento)
                                <span class="fc-badge fc-badge-ext">{{ $usuario->departamento }}</span>
                                @endif
                                @if($usuario->estaBloqueado())
                                <span class="fc-badge" style="background:rgba(239,68,68,0.1);color:#dc2626">🔒 Bloqueado</span>
                                @elseif($usuario->es_activo)
                                <span class="fc-badge" style="background:rgba(34,197,94,0.1);color:#16a34a">✅ Activo</span>
                                @else
                                <span class="fc-badge fc-badge-priv">⚪ Inactivo</span>
                                @endif
                            </div>
                        </div>
                        <div class="fc-profile-actions">
                            <a href="{{ route('usuarios.edit', $usuario) }}" class="fc-btn fc-btn-outline" style="font-size:13px;padding:8px 14px">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
                                Editar
                            </a>
                            @if($usuario->id !== Auth::id())
                                @if($usuario->estaBloqueado())
                                <form action="{{ route('usuarios.desbloquear', $usuario) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="fc-btn fc-btn-warning" style="font-size:13px;padding:8px 14px">🔓 Desbloquear</button>
                                </form>
                                @endif
                                <form action="{{ route('usuarios.toggle-activo', $usuario) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="fc-btn fc-btn-outline" style="font-size:13px;padding:8px 14px;{{ $usuario->es_activo ? 'color:#dc2626;border-color:#fca5a5' : 'color:#16a34a;border-color:#86efac' }}">
                                        {{ $usuario->es_activo ? 'Desactivar' : 'Activar' }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Columnas --}}
                <div class="fc-content-cols">
                    <div class="fc-col-main">

                        {{-- Actividad reciente --}}
                        <div class="fc-card">
                            <div class="fc-card-header">
                                <div class="fc-card-icon" style="background:rgba(99,102,241,0.1)">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="#6366f1">
                                        <path d="M13 3c-4.97 0-9 4.03-9 9H1l3.89 3.89.07.14L9 12H6c0-3.87 3.13-7 7-7s7 3.13 7 7-3.13 7-7 7c-1.93 0-3.68-.79-4.94-2.06l-1.42 1.42C8.27 19.99 10.51 21 13 21c4.97 0 9-4.03 9-9s-4.03-9-9-9zm-1 5v5l4.28 2.54.72-1.21-3.5-2.08V8H12z"/>
                                    </svg>
                                </div>
                                Actividad reciente
                            </div>
                            <div class="fc-card-body" style="padding:0 20px">
                                @forelse($actividad as $act)
                                <div class="fc-act-item">
                                    <div class="fc-act-icon" style="background:rgba(99,102,241,0.08)">
                                        @php
                                            $iconos = [
                                                'subir'           => ['#059669', 'M9 16h6v-6h4l-7-7-7 7h4zm-4 2h14v2H5z'],
                                                'descargar'       => ['#f59e0b', 'M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z'],
                                                'ver'             => ['#6366f1', 'M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5z'],
                                                'eliminar'        => ['#dc2626', 'M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z'],
                                                'iniciar_sesion'  => ['#22c55e', 'M10 17v-3H3v-4h7V7l5 5-5 5zm4-15c1.1 0 2 .9 2 2v4h-2V4H5v16h9v-4h2v4c0 1.1-.9 2-2 2H5c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h9z'],
                                                'login_fallido'   => ['#ef4444', 'M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z'],
                                                'crear_carpeta'   => ['#8b5cf6', 'M20 6h-8l-2-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2z'],
                                            ];
                                            $i = $iconos[$act->accion] ?? ['#94a3b8', 'M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z'];
                                        @endphp
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="{{ $i[0] }}"><path d="{{ $i[1] }}"/></svg>
                                    </div>
                                    <div>
                                        <div class="fc-act-desc">
                                            <strong>{{ ucfirst(str_replace('_',' ',$act->accion)) }}</strong>
                                            · {{ $act->recurso }}
                                            @if($act->detalles)
                                                · <span style="color:#64748b">{{ Str::limit($act->detalles, 60) }}</span>
                                            @endif
                                        </div>
                                        <div class="fc-act-time">
                                            {{ $act->created_at?->diffForHumans() ?? '—' }}
                                            @if($act->ip_address) · {{ $act->ip_address }} @endif
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <div style="padding:24px 0;text-align:center;color:#94a3b8;font-size:13px">Sin actividad registrada</div>
                                @endforelse
                            </div>
                        </div>

                    </div>

                    {{-- Panel lateral --}}
                    <div class="fc-col-side">
                        <div class="fc-info-card">
                            <div class="fc-info-header">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="#6366f1"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                                Información
                            </div>
                            <div class="fc-info-body">
                                <div class="fc-info-row">
                                    <span class="fc-info-label">Empresa</span>
                                    <span class="fc-info-val">{{ $usuario->empresa->nombre ?? '—' }}</span>
                                </div>
                                <div class="fc-info-row">
                                    <span class="fc-info-label">Departamento</span>
                                    <span class="fc-info-val">{{ $usuario->departamento ?? '—' }}</span>
                                </div>
                                <div class="fc-info-row">
                                    <span class="fc-info-label">Rol</span>
                                    <span class="fc-info-val">{{ $usuario->rol }}</span>
                                </div>
                                <div class="fc-info-row">
                                    <span class="fc-info-label">Último acceso</span>
                                    <span class="fc-info-val">{{ $usuario->last_login?->format('d/m/Y H:i') ?? 'Nunca' }}</span>
                                </div>
                                <div class="fc-info-row">
                                    <span class="fc-info-label">Creado</span>
                                    <span class="fc-info-val">{{ $usuario->created_at?->format('d/m/Y') ?? '—' }}</span>
                                </div>
                                <div class="fc-info-row">
                                    <span class="fc-info-label">Intentos fallidos</span>
                                    <span class="fc-info-val" style="{{ $usuario->intentos_login > 0 ? 'color:#ef4444' : '' }}">
                                        {{ $usuario->intentos_login }}
                                    </span>
                                </div>
                                @if($usuario->bloqueado_hasta)
                                <div class="fc-info-row">
                                    <span class="fc-info-label">Bloqueado hasta</span>
                                    <span class="fc-info-val" style="color:#ef4444">
                                        {{ $usuario->bloqueado_hasta->format('H:i d/m/Y') }}
                                    </span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>