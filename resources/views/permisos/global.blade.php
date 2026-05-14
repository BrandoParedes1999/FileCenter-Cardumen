<x-app-layout>
@include('permisos.partials._styles')

<div class="fc-wrapper">
    @include('components.sidebar')

    <div class="fc-main">

        {{-- Topbar ──────────────────────────────────────────────── --}}
        <header class="pg-topbar">
            <div class="pg-icon">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="#6366f1">
                    <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/>
                </svg>
            </div>
            <div>
                <div class="pg-title">Gestor de Permisos</div>
                <div class="pg-sub">Control centralizado de acceso a carpetas</div>
            </div>
            <div style="margin-left:auto;display:flex;gap:8px;align-items:center">
                @if($esAdmin)
                <a href="{{ route('carpetas.index') }}" class="fc-btn fc-btn-outline" style="font-size:12px;padding:7px 12px">
                    Ver carpetas
                </a>
                @endif
                <div class="fc-topbar-avatar">
                    {{ strtoupper(substr(Auth::user()->nombre,0,1)) }}{{ strtoupper(substr(Auth::user()->paterno,0,1)) }}
                </div>
                <div>
                    <div class="fc-topbar-name">{{ Auth::user()->nombre_completo }}</div>
                    <div class="fc-topbar-role">{{ Auth::user()->rol }}</div>
                </div>
            </div>
        </header>

        {{-- Stats ──────────────────────────────────────────────── --}}
        <div class="pg-stats">
            <div class="pg-stat">
                <div class="pg-stat-icon" style="background:rgba(99,102,241,.1)">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="#6366f1">
                        <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/>
                    </svg>
                </div>
                <div>
                    <div class="pg-stat-num">{{ $stats['total'] }}</div>
                    <div class="pg-stat-lbl">Permisos totales</div>
                </div>
            </div>
            <div class="pg-stat">
                <div class="pg-stat-icon" style="background:rgba(5,150,105,.1)">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="#059669">
                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                    </svg>
                </div>
                <div>
                    <div class="pg-stat-num">{{ $stats['por_usuario'] }}</div>
                    <div class="pg-stat-lbl">Por usuario específico</div>
                </div>
            </div>
            <div class="pg-stat">
                <div class="pg-stat-icon" style="background:rgba(124,58,237,.1)">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="#7c3aed">
                        <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                    </svg>
                </div>
                <div>
                    <div class="pg-stat-num">{{ $stats['por_rol'] }}</div>
                    <div class="pg-stat-lbl">Por rol / empresa</div>
                </div>
            </div>
            <div class="pg-stat">
                <div class="pg-stat-icon" style="background:rgba(245,158,11,.1)">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="#f59e0b">
                        <path d="M20 6h-8l-2-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2z"/>
                    </svg>
                </div>
                <div>
                    <div class="pg-stat-num">{{ $stats['carpetas_con_permisos'] }}</div>
                    <div class="pg-stat-lbl">Carpetas con permisos</div>
                </div>
            </div>
        </div>

        {{-- ── Leyenda de permisos ─────────────────────────────── --}}
        <div class="pg-legend">
            <div class="pg-legend-title">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="#64748b">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>
                </svg>
                Guía rápida — ¿Qué puede hacer cada permiso?
            </div>
            <div class="pg-legend-grid">
                <div class="pg-legend-item">
                    <div class="pg-legend-icon-row">
                        <div class="pg-legend-badge on">✓</div>
                        <div class="pg-legend-name">Leer</div>
                    </div>
                    <div class="pg-legend-desc">Ver el nombre y la lista de archivos dentro de la carpeta.</div>
                </div>
                <div class="pg-legend-item">
                    <div class="pg-legend-icon-row">
                        <div class="pg-legend-badge on">✓</div>
                        <div class="pg-legend-name">Descargar</div>
                    </div>
                    <div class="pg-legend-desc">Descargar archivos al dispositivo propio.</div>
                </div>
                <div class="pg-legend-item">
                    <div class="pg-legend-icon-row">
                        <div class="pg-legend-badge on">✓</div>
                        <div class="pg-legend-name">Subir</div>
                    </div>
                    <div class="pg-legend-desc">Agregar nuevos archivos o versiones a la carpeta.</div>
                </div>
                <div class="pg-legend-item">
                    <div class="pg-legend-icon-row">
                        <div class="pg-legend-badge on">✓</div>
                        <div class="pg-legend-name">Editar</div>
                    </div>
                    <div class="pg-legend-desc">Modificar la descripción y metadatos de archivos existentes.</div>
                </div>
                <div class="pg-legend-item">
                    <div class="pg-legend-icon-row">
                        <div class="pg-legend-badge on">✓</div>
                        <div class="pg-legend-name">Borrar</div>
                    </div>
                    <div class="pg-legend-desc">Eliminar archivos de la carpeta de forma permanente.</div>
                </div>
                <div class="pg-legend-item">
                    <div class="pg-legend-icon-row">
                        <div class="pg-legend-badge on" style="background:rgba(99,102,241,.12);color:#6366f1">↳</div>
                        <div class="pg-legend-name">Heredar</div>
                    </div>
                    <div class="pg-legend-desc">El permiso aplica también a todas las subcarpetas hijas.</div>
                </div>
            </div>
        </div>

        {{-- Flash ──────────────────────────────────────────────── --}}
        @if(session('success'))
        <div class="pg-flash success" style="margin-top:14px">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="#059669"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
            {{ session('success') }}
        </div>
        @endif
        @if(session('error') || $errors->any())
        <div class="pg-flash error" style="margin-top:14px">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="#dc2626"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
            {{ session('error') ?? $errors->first() }}
        </div>
        @endif

        {{-- Filtros ─────────────────────────────────────────────── --}}
        <form method="GET" action="{{ route('permisos.global') }}" class="pg-filters" id="filtrosForm">

            <div class="pg-search">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round">
                    <circle cx="11" cy="11" r="6"/><path d="m21 21-4.35-4.35"/>
                </svg>
                <input type="text" name="q" value="{{ $busqueda }}"
                       placeholder="Buscar carpeta o usuario..."
                       onchange="this.form.submit()">
            </div>

            <a href="{{ route('permisos.global', array_merge(request()->query(), ['tipo'=>'todos'])) }}"
               class="pg-filter-pill {{ $filtroTipo === 'todos' ? 'active' : '' }}">
                Todos
            </a>
            <a href="{{ route('permisos.global', array_merge(request()->query(), ['tipo'=>'usuario'])) }}"
               class="pg-filter-pill {{ $filtroTipo === 'usuario' ? 'active' : '' }}">
                Por usuario
            </a>
            <a href="{{ route('permisos.global', array_merge(request()->query(), ['tipo'=>'rol'])) }}"
               class="pg-filter-pill {{ $filtroTipo === 'rol' ? 'active' : '' }}">
                Por rol
            </a>

            @if($esAdmin && $empresas->count() > 1)
            <select name="empresa_id" class="pg-select" onchange="this.form.submit()">
                <option value="">Todas las empresas</option>
                @foreach($empresas as $emp)
                <option value="{{ $emp->id }}" {{ $filtroEmpresa == $emp->id ? 'selected' : '' }}>
                    {{ $emp->es_corporativo ? '🏢' : '🏭' }} {{ $emp->nombre }}
                </option>
                @endforeach
            </select>
            @endif

            <select name="carpeta_id" class="pg-select" onchange="this.form.submit()">
                <option value="">Todas las carpetas</option>
                @foreach($carpetasDisponibles as $c)
                <option value="{{ $c->id }}" {{ $filtroCarpeta == $c->id ? 'selected' : '' }}>
                    {{ $c->nombre }}
                </option>
                @endforeach
            </select>

            @if($busqueda || $filtroTipo !== 'todos' || $filtroCarpeta || $filtroEmpresa || $filtroUsuario)
            <a href="{{ route('permisos.global') }}" class="pg-filter-pill" style="color:#dc2626;border-color:#fca5a5">
                ✕ Limpiar filtros
            </a>
            @endif

        </form>

        {{-- Contenido principal ─────────────────────────────────── --}}
        <div class="pg-content">

            @forelse($permisosPorCarpeta as $carpetaId => $permisosCarpeta)
            @php
                $carpeta     = $permisosCarpeta->first()->carpeta;
                $empresa     = $carpeta?->empresa;
                $colorAccent = $empresa?->color_secundario ?? '#6366f1';
                $colorBg     = $empresa?->color_primario   ?? '#4f46e5';
            @endphp

            <div class="pg-group">
                <div class="pg-group-card">

                    {{-- Cabecera del grupo (carpeta) --}}
                    <div class="pg-group-header">
                        <div class="pg-group-icon" style="background:{{ $colorAccent }}18">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="{{ $colorAccent }}">
                                <path d="M20 6h-8l-2-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2z"/>
                            </svg>
                        </div>
                        <div class="pg-group-info">
                            @if($carpeta)
                            <a href="{{ route('carpetas.show', $carpetaId) }}" class="pg-folder-link">
                                <div class="pg-group-name">{{ $carpeta->nombre }}</div>
                            </a>
                            <div class="pg-group-path">
                                @if($empresa)
                                    <span style="color:{{ $colorAccent }};font-weight:600">{{ $empresa->siglas }}</span>
                                    <span style="color:#cbd5e1"> · </span>
                                    <span style="color:{{ $colorAccent }}">{{ $empresa->nombre }}</span>
                                    <span style="color:#cbd5e1"> · </span>
                                @endif
                                {{ $carpeta->path ?? '' }}
                            </div>
                            @else
                            <div class="pg-group-name" style="color:#94a3b8">Carpeta eliminada (ID: {{ $carpetaId }})</div>
                            <div class="pg-group-path">Esta carpeta ya no existe en el sistema</div>
                            @endif
                        </div>
                        <div class="pg-group-meta">
                            @if($carpeta?->esSoloLectura())
                            <span class="pg-badge-mode">👁 Solo lectura</span>
                            @elseif($carpeta?->requiere_aprobacion_subida)
                            <span style="font-size:10px;font-weight:600;background:rgba(245,158,11,.1);color:#d97706;padding:3px 8px;border-radius:10px">⏳ Con aprobación</span>
                            @endif
                            <span class="pg-group-count">
                                {{ $permisosCarpeta->count() }} {{ $permisosCarpeta->count() == 1 ? 'permiso' : 'permisos' }}
                            </span>
                            @if($carpeta)
                            <a href="{{ route('permisos.index', $carpetaId) }}" class="pg-goto-btn">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/>
                                </svg>
                                Gestionar
                            </a>
                            @endif
                        </div>
                    </div>

                    {{-- Tabla de permisos de esta carpeta --}}
                    <div class="pg-table-wrap">
                        <table class="pg-table">
                            <thead>
                                <tr>
                                    <th class="col-who" style="width:35%">Usuario / Rol</th>
                                    <th title="Puede ver la lista de archivos">👁 Leer</th>
                                    <th title="Puede descargar archivos">⬇ Descargar</th>
                                    <th title="Puede subir archivos">⬆ Subir</th>
                                    <th title="Puede editar metadatos">✏ Editar</th>
                                    <th title="Puede eliminar archivos">🗑 Borrar</th>
                                    <th title="Aplica a subcarpetas">↳ Heredar</th>
                                    <th>Tipo</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($permisosCarpeta as $permiso)
                            @php
                                $esRol = is_null($permiso->usuario_id);
                                $rolBadge = '';
                                if (!$esRol && $permiso->usuario) {
                                    $rolBadge = match($permiso->usuario->rol ?? '') {
                                        'Superadmin', 'Aux_QHSE' => 'pg-badge-super',
                                        'Admin', 'Gerente'       => 'pg-badge-admin',
                                        default                   => 'pg-badge-rol',
                                    };
                                }
                                $capacidades = [
                                    'puede_leer'      => 'Leer',
                                    'puede_descargar' => 'Descargar',
                                    'puede_subir'     => 'Subir',
                                    'puede_editar'    => 'Editar',
                                    'puede_borrar'    => 'Borrar',
                                    'heredar'         => 'Heredar',
                                ];
                            @endphp
                            <tr class="{{ $esRol ? 'tipo-rol' : '' }}">

                                {{-- Quién --}}
                                <td class="col-who">
                                    <div class="pg-who">
                                        @if(!$esRol && $permiso->usuario)
                                        <div class="pg-avatar"
                                             style="background:linear-gradient(135deg,{{ $colorBg }},{{ $colorAccent }})">
                                            {{ strtoupper(substr($permiso->usuario->nombre ?? '?', 0, 1)) }}{{ strtoupper(substr($permiso->usuario->paterno ?? '', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="pg-who-name">{{ $permiso->usuario->nombre_completo }}</div>
                                            <div class="pg-who-sub">
                                                {{ $permiso->usuario->email }}
                                                @if($permiso->usuario->departamento) · {{ $permiso->usuario->departamento }} @endif
                                            </div>
                                        </div>
                                        @else
                                        <div class="pg-avatar rol-icon">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="#7c3aed">
                                                <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="pg-who-name">
                                                {{ $permiso->empresa?->nombre ?? 'Todas las empresas' }}
                                                @if($permiso->rol) <span style="color:#94a3b8;font-weight:400">· {{ $permiso->rol }}</span> @endif
                                            </div>
                                            <div class="pg-who-sub">Permiso por rol de empresa</div>
                                        </div>
                                        @endif
                                    </div>
                                </td>

                                {{-- Toggles de capacidad --}}
                                @foreach($capacidades as $campo => $label)
                                <td class="pg-perm-cell">
                                    <form method="POST"
                                          action="{{ route('permisos.global.update', $permiso) }}"
                                          style="display:inline">
                                        @csrf @method('PUT')
                                        @foreach(array_keys($capacidades) as $c)
                                            @if($c !== $campo)
                                            <input type="hidden" name="{{ $c }}" value="{{ $permiso->$c ? '1' : '0' }}">
                                            @endif
                                        @endforeach
                                        <input type="hidden" name="{{ $campo }}" value="{{ $permiso->$campo ? '0' : '1' }}">
                                        <button type="submit"
                                                class="pg-cap-btn {{ $permiso->$campo ? 'on' : 'off' }}"
                                                title="{{ $permiso->$campo ? 'Clic para quitar: ' : 'Clic para dar: ' }}{{ $label }}">
                                            {{ $permiso->$campo ? '✓' : '–' }}
                                        </button>
                                    </form>
                                </td>
                                @endforeach

                                {{-- Tipo de permiso --}}
                                <td class="pg-perm-cell">
                                    @if(!$esRol && $permiso->usuario)
                                    <span class="pg-badge {{ $rolBadge }}">{{ $permiso->usuario->rol }}</span>
                                    @else
                                    <span class="pg-badge pg-badge-corp">Por rol</span>
                                    @endif
                                </td>

                                {{-- Acciones --}}
                                <td class="pg-perm-cell">
                                    <div class="pg-row-actions">
                                        <button type="button"
                                                class="pg-act-btn"
                                                title="Editar todos los permisos a la vez"
                                                onclick="abrirModalEditar(
                                                    {{ $permiso->id }},
                                                    '{{ addslashes(!$esRol && $permiso->usuario ? $permiso->usuario->nombre_completo : ($permiso->empresa?->nombre ?? 'Todas las empresas') . ($permiso->rol ? ' · '.$permiso->rol : '')) }}',
                                                    '{{ addslashes($carpeta?->nombre ?? 'Carpeta eliminada') }}',
                                                    {{ $permiso->puede_leer ? 'true' : 'false' }},
                                                    {{ $permiso->puede_subir ? 'true' : 'false' }},
                                                    {{ $permiso->puede_editar ? 'true' : 'false' }},
                                                    {{ $permiso->puede_borrar ? 'true' : 'false' }},
                                                    {{ $permiso->puede_descargar ? 'true' : 'false' }},
                                                    {{ $permiso->heredar ? 'true' : 'false' }}
                                                )">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                                            </svg>
                                        </button>
                                        <button type="button"
                                                class="pg-act-btn danger"
                                                title="Revocar este permiso"
                                                onclick="confirmarRevocar(
                                                    {{ $permiso->id }},
                                                    '{{ addslashes(!$esRol && $permiso->usuario ? $permiso->usuario->nombre_completo : ($permiso->empresa?->nombre ?? 'rol')) }}',
                                                    '{{ addslashes($carpeta?->nombre ?? '') }}'
                                                )">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>{{-- /pg-group-card --}}
            </div>{{-- /pg-group --}}
            @empty

            <div class="pg-empty">
                <div class="pg-empty-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="#a5b4fc">
                        <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/>
                    </svg>
                </div>
                <div class="pg-empty-title">No hay permisos configurados</div>
                <div class="pg-empty-sub">
                    @if($busqueda || $filtroTipo !== 'todos' || $filtroCarpeta || $filtroEmpresa)
                        No se encontraron permisos con los filtros aplicados.
                        <a href="{{ route('permisos.global') }}" style="color:#6366f1;display:block;margin-top:6px">Limpiar filtros</a>
                    @else
                        Ninguna carpeta tiene permisos configurados aún.
                        Los permisos se agregan desde la vista de cada carpeta.
                    @endif
                </div>
                <a href="{{ route('carpetas.index') }}" class="fc-btn fc-btn-primary" style="margin-top:16px">
                    Ir a carpetas
                </a>
            </div>

            @endforelse

        </div>{{-- /pg-content --}}
    </div>{{-- /fc-main --}}
</div>{{-- /fc-wrapper --}}

@include('permisos.partials._modals')
@include('permisos.partials._script')
</x-app-layout>
