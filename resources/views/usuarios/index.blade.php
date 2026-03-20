<x-app-layout>
    <style>
    /* Estilos específicos de esta vista — el resto viene de filecenter.css */
    .fc-filter-bar {
        background: #fff; border: 1px solid #e2e8f0; border-radius: 14px;
        padding: 16px 20px; margin-bottom: 20px;
        display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
    }
    .fc-filter-select {
        background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 9px;
        padding: 8px 12px; font-size: 13px; color: #475569; outline: none;
        cursor: pointer; transition: border-color .2s;
    }
    .fc-filter-select:focus { border-color: #6366f1; }

    .fc-table { width: 100%; border-collapse: collapse; }
    .fc-table th {
        font-size: 11px; font-weight: 700; color: #94a3b8;
        text-transform: uppercase; letter-spacing: .09em;
        padding: 12px 16px; text-align: left;
        border-bottom: 1px solid #f1f5f9; white-space: nowrap;
    }
    .fc-table td {
        padding: 13px 16px; border-bottom: 1px solid #f8fafc;
        font-size: 13px; color: #1e293b; vertical-align: middle;
    }
    .fc-table tr:last-child td { border-bottom: none; }
    .fc-table tr:hover td { background: #fafbff; }

    .fc-user-cell { display: flex; align-items: center; gap: 12px; }
    .fc-user-avatar {
        width: 36px; height: 36px; border-radius: 50%;
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
        display: flex; align-items: center; justify-content: center;
        font-size: 12px; font-weight: 700; color: #fff; flex-shrink: 0;
    }
    .fc-user-nombre { font-weight: 600; color: #1e293b; }
    .fc-user-email  { font-size: 11px; color: #94a3b8; margin-top: 1px; }

    .fc-rol-badge {
        font-size: 10px; font-weight: 700; padding: 3px 9px;
        border-radius: 20px; letter-spacing: .05em; text-transform: uppercase;
        display: inline-block; white-space: nowrap;
    }
    .rol-superadmin { background: rgba(124,58,237,0.12); color: #6d28d9; }
    .rol-aux_qhse   { background: rgba(6,182,212,0.12);  color: #0e7490; }
    .rol-admin      { background: rgba(79,70,229,0.12);  color: #4338ca; }
    .rol-gerente    { background: rgba(5,150,105,0.12);  color: #065f46; }
    .rol-auxiliar   { background: rgba(245,158,11,0.12); color: #92400e; }
    .rol-empleado   { background: rgba(100,116,139,0.12);color: #334155; }

    .fc-estado-dot {
        width: 8px; height: 8px; border-radius: 50%; display: inline-block; margin-right: 6px;
    }
    .estado-activo   { background: #22c55e; box-shadow: 0 0 6px rgba(34,197,94,0.5); }
    .estado-inactivo { background: #94a3b8; }
    .estado-bloqueado{ background: #ef4444; box-shadow: 0 0 6px rgba(239,68,68,0.5); }

    .fc-table-actions { display: flex; gap: 6px; align-items: center; }
    .fc-action-ico {
        width: 28px; height: 28px; border-radius: 7px;
        border: 1px solid #e2e8f0; background: #f8fafc;
        display: flex; align-items: center; justify-content: center;
        color: #94a3b8; cursor: pointer; text-decoration: none;
        transition: all .15s;
    }
    .fc-action-ico:hover       { background: #ede9fe; border-color: #c4b5fd; color: #4f46e5; }
    .fc-action-ico.toggle:hover{ background: #dcfce7; border-color: #86efac; color: #059669; }
    .fc-action-ico.danger:hover{ background: #fee2e2; border-color: #fca5a5; color: #dc2626; }
    .fc-action-ico.unlock:hover{ background: #fef9c3; border-color: #fde047; color: #854d0e; }

    .fc-empresa-tag {
        font-size: 11px; font-weight: 600; padding: 3px 8px;
        border-radius: 6px; white-space: nowrap;
    }

    .fc-pagination { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-top: 1px solid #f1f5f9; }
    .fc-pag-info { font-size: 12px; color: #94a3b8; }
    .fc-pag-links { display: flex; gap: 4px; }
    .fc-pag-btn {
        padding: 6px 12px; border-radius: 8px; border: 1px solid #e2e8f0;
        background: #fff; font-size: 12px; color: #475569; cursor: pointer;
        text-decoration: none; transition: all .15s;
    }
    .fc-pag-btn:hover    { border-color: #c7d2fe; color: #4f46e5; }
    .fc-pag-btn.active   { background: #4f46e5; color: #fff; border-color: #4f46e5; }
    .fc-pag-btn.disabled { opacity: .4; pointer-events: none; }
    </style>

    <div class="fc-wrapper">
        @include('components.sidebar')
        <div class="fc-main">

            <header class="fc-topbar">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="#6366f1">
                    <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                </svg>
                <span class="fc-topbar-title">Gestión de Usuarios</span>
                <div class="fc-topbar-right">
                    <div class="fc-topbar-avatar">{{ strtoupper(substr(Auth::user()->nombre,0,1)) }}{{ strtoupper(substr(Auth::user()->paterno,0,1)) }}</div>
                    <div>
                        <div class="fc-topbar-name">{{ Auth::user()->nombre_completo }}</div>
                        <div class="fc-topbar-role">{{ Auth::user()->rol }}</div>
                    </div>
                </div>
            </header>

            {{-- Barra de acciones --}}
            <div class="fc-actionbar">
                <form method="GET" action="{{ route('usuarios.index') }}" id="filterForm"
                    style="display:flex;align-items:center;gap:10px;flex:1;flex-wrap:wrap">

                    <div class="fc-search-wrap" style="max-width:280px">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round">
                            <circle cx="11" cy="11" r="6"/><path d="m21 21-4.35-4.35"/>
                        </svg>
                        <input type="text" name="q" value="{{ request('q') }}"
                            placeholder="Buscar por nombre, email..."
                            onchange="this.form.submit()">
                    </div>

                    @if(in_array(Auth::user()->rol, ['Superadmin','Aux_QHSE']))
                    <select name="empresa_id" class="fc-filter-select" onchange="this.form.submit()">
                        <option value="">Todas las empresas</option>
                        @foreach($empresas as $emp)
                            <option value="{{ $emp->id }}" {{ request('empresa_id') == $emp->id ? 'selected' : '' }}>
                                {{ $emp->es_corporativo ? '🏢' : '🏭' }} {{ $emp->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @endif

                    <select name="rol" class="fc-filter-select" onchange="this.form.submit()">
                        <option value="">Todos los roles</option>
                        @foreach(['Superadmin','Aux_QHSE','Admin','Gerente','Auxiliar','Empleado'] as $r)
                            <option value="{{ $r }}" {{ request('rol') == $r ? 'selected' : '' }}>{{ $r }}</option>
                        @endforeach
                    </select>

                    <select name="estado" class="fc-filter-select" onchange="this.form.submit()">
                        <option value="">Todos los estados</option>
                        <option value="activo"   {{ request('estado') == 'activo'   ? 'selected' : '' }}>Activos</option>
                        <option value="inactivo" {{ request('estado') == 'inactivo' ? 'selected' : '' }}>Inactivos</option>
                    </select>

                    @if(request()->hasAny(['q','empresa_id','rol','estado']))
                        <a href="{{ route('usuarios.index') }}" class="fc-btn fc-btn-outline" style="padding:7px 13px;font-size:12px">
                            Limpiar filtros
                        </a>
                    @endif
                </form>

                <a href="{{ route('usuarios.create') }}" class="fc-btn fc-btn-primary">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
                    Nuevo usuario
                </a>
            </div>

            {{-- Contenido --}}
            <div class="fc-content" style="padding:20px 24px">

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

                {{-- Resumen --}}
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
                    <div style="font-size:13px;color:#64748b">
                        <strong style="color:#1e293b">{{ $usuarios->total() }}</strong> usuario{{ $usuarios->total() != 1 ? 's' : '' }} encontrado{{ $usuarios->total() != 1 ? 's' : '' }}
                    </div>
                </div>

                {{-- Tabla --}}
                @if($usuarios->isEmpty())
                <div class="fc-empty">
                    <div class="fc-empty-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="#a5b4fc">
                            <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                        </svg>
                    </div>
                    <div class="fc-empty-title">No se encontraron usuarios</div>
                    <div class="fc-empty-sub">Intenta ajustar los filtros o crea un nuevo usuario.</div>
                </div>
                @else
                <div class="fc-card">
                    <table class="fc-table">
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>Empresa</th>
                                <th>Rol</th>
                                <th>Departamento</th>
                                <th>Estado</th>
                                <th>Último acceso</th>
                                <th style="text-align:right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($usuarios as $u)
                            <tr>
                                {{-- Usuario --}}
                                <td>
                                    <div class="fc-user-cell">
                                        <div class="fc-user-avatar">
                                            {{ strtoupper(substr($u->nombre,0,1)) }}{{ strtoupper(substr($u->paterno,0,1)) }}
                                        </div>
                                        <div>
                                            <div class="fc-user-nombre">{{ $u->nombre_completo }}</div>
                                            <div class="fc-user-email">{{ $u->email }}</div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Empresa --}}
                                <td>
                                    @if($u->empresa)
                                    <span class="fc-empresa-tag"
                                        style="background:{{ $u->empresa->color_secundario ?? '#f1f5f9' }};color:{{ $u->empresa->color_primario ?? '#475569' }}">
                                        {{ $u->empresa->siglas }}
                                    </span>
                                    @else
                                    <span style="color:#94a3b8">—</span>
                                    @endif
                                </td>

                                {{-- Rol --}}
                                <td>
                                    <span class="fc-rol-badge rol-{{ strtolower(str_replace('_','-',$u->rol)) }}">
                                        {{ $u->rol }}
                                    </span>
                                </td>

                                {{-- Departamento --}}
                                <td style="color:{{ $u->departamento ? '#475569' : '#cbd5e1' }}">
                                    {{ $u->departamento ?? '—' }}
                                </td>

                                {{-- Estado --}}
                                <td>
                                    @if($u->estaBloqueado())
                                        <span class="fc-estado-dot estado-bloqueado"></span>
                                        <span style="font-size:12px;color:#ef4444;font-weight:600">Bloqueado</span>
                                    @elseif($u->es_activo)
                                        <span class="fc-estado-dot estado-activo"></span>
                                        <span style="font-size:12px;color:#16a34a;font-weight:600">Activo</span>
                                    @else
                                        <span class="fc-estado-dot estado-inactivo"></span>
                                        <span style="font-size:12px;color:#94a3b8;font-weight:600">Inactivo</span>
                                    @endif
                                </td>

                                {{-- Último acceso --}}
                                <td style="font-size:12px;color:#94a3b8">
                                    {{ $u->last_login ? $u->last_login->diffForHumans() : 'Nunca' }}
                                </td>

                                {{-- Acciones --}}
                                <td>
                                    <div class="fc-table-actions" style="justify-content:flex-end">

                                        {{-- Ver detalle --}}
                                        <a href="{{ route('usuarios.show', $u) }}" class="fc-action-ico" title="Ver detalle">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                                            </svg>
                                        </a>

                                        {{-- Editar --}}
                                        <a href="{{ route('usuarios.edit', $u) }}" class="fc-action-ico" title="Editar">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                                            </svg>
                                        </a>

                                        {{-- Desbloquear (si está bloqueado) --}}
                                        @if($u->estaBloqueado())
                                        <form action="{{ route('usuarios.desbloquear', $u) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="fc-action-ico unlock" title="Desbloquear">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M12 17c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm6-9h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6h1.9c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2z"/>
                                                </svg>
                                            </button>
                                        </form>
                                        @endif

                                        {{-- Toggle activo --}}
                                        @if($u->id !== Auth::id())
                                        <form action="{{ route('usuarios.toggle-activo', $u) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="fc-action-ico toggle"
                                                    title="{{ $u->es_activo ? 'Desactivar' : 'Activar' }}">
                                                @if($u->es_activo)
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 14.5v-9l6 4.5-6 4.5z"/>
                                                </svg>
                                                @else
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="#22c55e">
                                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 14.5v-9l6 4.5-6 4.5z"/>
                                                </svg>
                                                @endif
                                            </button>
                                        </form>

                                        {{-- Eliminar --}}
                                        @if($u->rol !== 'Superadmin')
                                        <button onclick="confirmarEliminar({{ $u->id }}, '{{ addslashes($u->nombre_completo) }}')"
                                                class="fc-action-ico danger" title="Eliminar">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                                            </svg>
                                        </button>
                                        @endif
                                        @endif

                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{-- Paginación --}}
                    @if($usuarios->hasPages())
                    <div class="fc-pagination">
                        <div class="fc-pag-info">
                            Mostrando {{ $usuarios->firstItem() }}–{{ $usuarios->lastItem() }} de {{ $usuarios->total() }}
                        </div>
                        <div class="fc-pag-links">
                            @if($usuarios->onFirstPage())
                                <span class="fc-pag-btn disabled">‹ Anterior</span>
                            @else
                                <a href="{{ $usuarios->previousPageUrl() }}" class="fc-pag-btn">‹ Anterior</a>
                            @endif

                            @foreach($usuarios->getUrlRange(1, $usuarios->lastPage()) as $page => $url)
                                <a href="{{ $url }}"
                                class="fc-pag-btn {{ $page == $usuarios->currentPage() ? 'active' : '' }}">
                                    {{ $page }}
                                </a>
                            @endforeach

                            @if($usuarios->hasMorePages())
                                <a href="{{ $usuarios->nextPageUrl() }}" class="fc-pag-btn">Siguiente ›</a>
                            @else
                                <span class="fc-pag-btn disabled">Siguiente ›</span>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
                @endif

            </div>
        </div>
    </div>

    {{-- Modal eliminar --}}
    <div class="fc-modal-overlay" id="modalEliminar">
        <div class="fc-modal">
            <div class="fc-modal-title">¿Eliminar usuario?</div>
            <div class="fc-modal-sub">
                El usuario "<strong id="modalNombre"></strong>" será desactivado y eliminado del sistema.
                Esta acción puede revertirse restaurando el registro desde la base de datos.
            </div>
            <div class="fc-modal-btns">
                <button class="fc-modal-cancel" onclick="document.getElementById('modalEliminar').classList.remove('open')">Cancelar</button>
                <form id="formEliminar" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="fc-modal-confirm danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>

    <script>
    function confirmarEliminar(id, nombre) {
        document.getElementById('modalNombre').textContent = nombre;
        document.getElementById('formEliminar').action = '/usuarios/' + id;
        document.getElementById('modalEliminar').classList.add('open');
    }
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') document.querySelectorAll('.fc-modal-overlay.open').forEach(m => m.classList.remove('open'));
    });
    </script>
</x-app-layout>