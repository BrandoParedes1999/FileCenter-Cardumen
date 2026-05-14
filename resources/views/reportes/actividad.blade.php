<x-app-layout>
<style>
/* ══ REPORTES DE ACTIVIDAD ════════════════════════════════════ */
.rp-topbar {
    background: #fff; border-bottom: 1px solid #e2e8f0;
    padding: 14px 24px; display: flex; align-items: center;
    gap: 12px; flex-shrink: 0;
    box-shadow: 0 1px 3px rgba(0,0,0,.04);
}
.rp-icon {
    width: 34px; height: 34px; border-radius: 10px;
    background: rgba(5,150,105,.1);
    display: flex; align-items: center; justify-content: center;
}
.rp-title { font-size: 15px; font-weight: 700; color: #1e293b; }
.rp-sub   { font-size: 12px; color: #94a3b8; margin-top: 1px; }

/* Panel de filtros */
.rp-panel {
    margin: 18px 24px 0;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 20px 24px;
}
.rp-panel-title {
    font-size: 11px; font-weight: 700; color: #64748b;
    text-transform: uppercase; letter-spacing: .07em;
    margin-bottom: 16px; display: flex; align-items: center; gap: 6px;
}
.rp-filters-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 14px;
    margin-bottom: 18px;
}
.rp-field label {
    display: block;
    font-size: 11px; font-weight: 600; color: #64748b;
    text-transform: uppercase; letter-spacing: .05em;
    margin-bottom: 6px;
}
.rp-field input,
.rp-field select {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 13px;
    color: #1e293b;
    background: #f8fafc;
    outline: none;
    transition: border-color .15s, background .15s;
    box-sizing: border-box;
}
.rp-field input:focus,
.rp-field select:focus { background: #fff; border-color: #6366f1; }

.rp-actions {
    display: flex; gap: 10px; align-items: center; flex-wrap: wrap;
}

/* Badge de total */
.rp-total-badge {
    display: inline-flex; align-items: center; gap: 6px;
    background: rgba(5,150,105,.08);
    border: 1px solid rgba(5,150,105,.2);
    color: #059669;
    padding: 6px 14px; border-radius: 20px;
    font-size: 12px; font-weight: 600;
    margin-left: auto;
}

/* Tabla preview */
.rp-content {
    flex: 1; overflow-y: auto; padding: 18px 24px 24px;
    scrollbar-width: thin; scrollbar-color: #cbd5e1 transparent;
}
.rp-preview-title {
    font-size: 12px; font-weight: 700; color: #64748b;
    text-transform: uppercase; letter-spacing: .07em;
    margin-bottom: 12px; display: flex; align-items: center; gap: 8px;
}
.rp-preview-note {
    font-size: 11px; font-weight: 400; color: #94a3b8;
    text-transform: none; letter-spacing: 0;
}
.rp-table-wrap {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 1px 4px rgba(0,0,0,.04);
}
.rp-table {
    width: 100%; border-collapse: collapse; font-size: 12px;
}
.rp-table thead th {
    padding: 9px 14px;
    font-size: 10px; font-weight: 700; color: #94a3b8;
    text-transform: uppercase; letter-spacing: .07em;
    background: #fafbfc;
    border-bottom: 1px solid #f1f5f9;
    white-space: nowrap;
    text-align: left;
}
.rp-table tbody tr {
    border-bottom: 1px solid #f8fafc;
    transition: background .1s;
}
.rp-table tbody tr:last-child { border-bottom: none; }
.rp-table tbody tr:hover { background: #fafbff; }
.rp-table td {
    padding: 9px 14px; vertical-align: middle; color: #374151;
}
.rp-table td.muted { color: #94a3b8; }

/* Badges de acción */
.rp-accion-badge {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 3px 8px; border-radius: 20px;
    font-size: 10px; font-weight: 600; white-space: nowrap;
}
.rp-accion-badge.subir      { background: rgba(5,150,105,.1);  color: #059669; }
.rp-accion-badge.descargar  { background: rgba(79,70,229,.1);  color: #4f46e5; }
.rp-accion-badge.eliminar   { background: rgba(220,38,38,.1);  color: #dc2626; }
.rp-accion-badge.ver        { background: rgba(100,116,139,.1);color: #64748b; }
.rp-accion-badge.editar     { background: rgba(245,158,11,.1); color: #d97706; }
.rp-accion-badge.login      { background: rgba(6,182,212,.1);  color: #0891b2; }
.rp-accion-badge.sesion     { background: rgba(6,182,212,.1);  color: #0891b2; }
.rp-accion-badge.solicitud  { background: rgba(124,58,237,.1); color: #7c3aed; }
.rp-accion-badge.default    { background: #f1f5f9; color: #64748b; }

/* Flash */
.rp-flash {
    margin: 0 24px 14px; padding: 12px 16px;
    border-radius: 10px; font-size: 13px; font-weight: 500;
    display: flex; align-items: center; gap: 9px; flex-shrink: 0;
}
.rp-flash.success { background: rgba(5,150,105,.08); border: 1px solid rgba(5,150,105,.25); color: #065f46; }

/* Dark mode */
.dark .rp-topbar   { background: #13111f; border-color: #1e1b4b; }
.dark .rp-title    { color: #e0e7ff; }
.dark .rp-panel    { background: #13111f; border-color: #1e1b4b; }
.dark .rp-field input,
.dark .rp-field select { background: #1e1b4b; border-color: #2d2a5e; color: #e0e7ff; }
.dark .rp-table-wrap { background: #13111f; border-color: #1e1b4b; }
.dark .rp-table thead th { background: #1a1830; }
.dark .rp-table tbody tr { border-color: #1e1b4b; }
.dark .rp-table td { color: #c7d2fe; }

@media (max-width: 700px) {
    .rp-filters-grid { grid-template-columns: 1fr 1fr; }
}
</style>

<div class="fc-wrapper">
    @include('components.sidebar')

    <div class="fc-main">

        {{-- Topbar --}}
        <header class="rp-topbar">
            <div class="rp-icon">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="#059669">
                    <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 14H7v-2h5v2zm5-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                </svg>
            </div>
            <div>
                <div class="rp-title">Reportes de Actividad</div>
                <div class="rp-sub">Auditoría completa de acciones en la plataforma · Solo Superadmin</div>
            </div>
            <div class="fc-topbar-avatar" style="margin-left:auto">
                {{ strtoupper(substr(Auth::user()->nombre,0,1)) }}{{ strtoupper(substr(Auth::user()->paterno,0,1)) }}
            </div>
            <div>
                <div class="fc-topbar-name">{{ Auth::user()->nombre_completo }}</div>
                <div class="fc-topbar-role">{{ Auth::user()->rol }}</div>
            </div>
        </header>

        @if(session('success'))
        <div class="rp-flash success" style="margin-top:14px">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="#059669"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
            {{ session('success') }}
        </div>
        @endif

        {{-- Panel de filtros + botón exportar --}}
        <div class="rp-panel">
            <div class="rp-panel-title">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="#64748b">
                    <path d="M4.25 5.61C6.27 8.2 10 13 10 13v6c0 .55.45 1 1 1h2c.55 0 1-.45 1-1v-6s3.72-4.8 5.74-7.39A.998.998 0 0 0 18.95 4H5.04c-.83 0-1.3.95-.79 1.61z"/>
                </svg>
                Filtros del reporte
            </div>

            <form method="GET" id="formFiltros">
                <div class="rp-filters-grid">

                    <div class="rp-field">
                        <label>Fecha inicio</label>
                        <input type="date" name="fecha_inicio"
                               value="{{ request('fecha_inicio', now()->subDays(30)->format('Y-m-d')) }}">
                    </div>

                    <div class="rp-field">
                        <label>Fecha fin</label>
                        <input type="date" name="fecha_fin"
                               value="{{ request('fecha_fin', now()->format('Y-m-d')) }}">
                    </div>

                    <div class="rp-field">
                        <label>Empresa</label>
                        <select name="empresa_id">
                            <option value="">Todas las empresas</option>
                            @foreach($empresas as $emp)
                            <option value="{{ $emp->id }}" {{ request('empresa_id') == $emp->id ? 'selected' : '' }}>
                                {{ $emp->es_corporativo ? '🏢' : '🏭' }} {{ $emp->nombre }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="rp-field">
                        <label>Usuario</label>
                        <select name="usuario_id">
                            <option value="">Todos los usuarios</option>
                            @foreach($usuarios as $u)
                            <option value="{{ $u->id }}" {{ request('usuario_id') == $u->id ? 'selected' : '' }}>
                                {{ $u->paterno }} {{ $u->nombre }} — {{ $u->rol }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="rp-field">
                        <label>Tipo de acción</label>
                        <select name="accion">
                            <option value="">Todas las acciones</option>
                            <optgroup label="Archivos">
                                <option value="subir"      {{ request('accion') === 'subir'      ? 'selected' : '' }}>Subir archivo</option>
                                <option value="descargar"  {{ request('accion') === 'descargar'  ? 'selected' : '' }}>Descargar archivo</option>
                                <option value="eliminar"   {{ request('accion') === 'eliminar'   ? 'selected' : '' }}>Eliminar archivo</option>
                                <option value="ver"        {{ request('accion') === 'ver'        ? 'selected' : '' }}>Visualizar</option>
                                <option value="editar"     {{ request('accion') === 'editar'     ? 'selected' : '' }}>Editar</option>
                                <option value="restaurar_version" {{ request('accion') === 'restaurar_version' ? 'selected' : '' }}>Restaurar versión</option>
                            </optgroup>
                            <optgroup label="Sesión">
                                <option value="iniciar_sesion"  {{ request('accion') === 'iniciar_sesion'  ? 'selected' : '' }}>Iniciar sesión</option>
                                <option value="cerrar_sesion"   {{ request('accion') === 'cerrar_sesion'   ? 'selected' : '' }}>Cerrar sesión</option>
                                <option value="login_fallido"   {{ request('accion') === 'login_fallido'   ? 'selected' : '' }}>Login fallido</option>
                                <option value="usuario_bloqueado" {{ request('accion') === 'usuario_bloqueado' ? 'selected' : '' }}>Usuario bloqueado</option>
                            </optgroup>
                            <optgroup label="Solicitudes">
                                <option value="solicitar_acceso"  {{ request('accion') === 'solicitar_acceso'  ? 'selected' : '' }}>Solicitar acceso</option>
                                <option value="aprobar_solicitud" {{ request('accion') === 'aprobar_solicitud' ? 'selected' : '' }}>Aprobar solicitud</option>
                                <option value="rechazar_solicitud"{{ request('accion') === 'rechazar_solicitud'? 'selected' : '' }}>Rechazar solicitud</option>
                                <option value="solicitar_subida"  {{ request('accion') === 'solicitar_subida'  ? 'selected' : '' }}>Solicitar subida</option>
                                <option value="aprobar_subida"    {{ request('accion') === 'aprobar_subida'    ? 'selected' : '' }}>Aprobar subida</option>
                                <option value="rechazar_subida"   {{ request('accion') === 'rechazar_subida'   ? 'selected' : '' }}>Rechazar subida</option>
                            </optgroup>
                            <optgroup label="Administración">
                                <option value="crear_carpeta" {{ request('accion') === 'crear_carpeta' ? 'selected' : '' }}>Crear carpeta</option>
                                <option value="crear_usuario" {{ request('accion') === 'crear_usuario' ? 'selected' : '' }}>Crear usuario</option>
                            </optgroup>
                        </select>
                    </div>

                </div>

                <div class="rp-actions">
                    <button type="submit" class="fc-btn fc-btn-outline" style="font-size:13px;padding:8px 16px">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor" style="margin-right:5px">
                            <path d="M4.25 5.61C6.27 8.2 10 13 10 13v6c0 .55.45 1 1 1h2c.55 0 1-.45 1-1v-6s3.72-4.8 5.74-7.39A.998.998 0 0 0 18.95 4H5.04c-.83 0-1.3.95-.79 1.61z"/>
                        </svg>
                        Aplicar filtros
                    </button>

                    <a href="{{ route('reportes.actividad') }}" class="fc-btn fc-btn-outline"
                       style="font-size:12px;padding:8px 14px;color:#64748b">
                        Limpiar
                    </a>

                    <a href="{{ route('reportes.actividad.exportar', request()->query()) }}"
                       class="fc-btn fc-btn-primary"
                       style="font-size:13px;padding:8px 18px;background:#059669;border-color:#059669;gap:6px">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/>
                        </svg>
                        Descargar Excel (.csv)
                    </a>

                    <span class="rp-total-badge">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z"/>
                        </svg>
                        {{ number_format($totalFiltrado) }} registros con filtros actuales
                    </span>
                </div>
            </form>
        </div>

        {{-- Tabla de vista previa --}}
        <div class="rp-content">
            <div class="rp-preview-title">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="#64748b">
                    <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                </svg>
                Vista previa
                <span class="rp-preview-note">(mostrando los últimos 15 registros)</span>
            </div>

            @if($preview->isEmpty())
            <div style="text-align:center;padding:48px 20px;color:#94a3b8">
                <div style="width:56px;height:56px;border-radius:14px;background:rgba(5,150,105,.08);display:flex;align-items:center;justify-content:center;margin:0 auto 14px">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="#6ee7b7">
                        <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 14H7v-2h5v2zm5-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                    </svg>
                </div>
                <div style="font-size:14px;font-weight:600;color:#1e293b;margin-bottom:4px">Sin registros</div>
                <div style="font-size:12px">No hay actividad que coincida con los filtros aplicados.</div>
            </div>
            @else
            <div class="rp-table-wrap">
                <table class="rp-table">
                    <thead>
                        <tr>
                            <th>Fecha y Hora</th>
                            <th>Usuario</th>
                            <th>Empresa</th>
                            <th>Acción</th>
                            <th>Recurso</th>
                            <th>Detalles</th>
                            <th>IP</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($preview as $r)
                    @php
                        $accionClass = match(true) {
                            in_array($r->accion, ['subir', 'solicitar_subida', 'aprobar_subida']) => 'subir',
                            in_array($r->accion, ['descargar']) => 'descargar',
                            in_array($r->accion, ['eliminar']) => 'eliminar',
                            in_array($r->accion, ['ver']) => 'ver',
                            in_array($r->accion, ['editar', 'mover', 'restaurar_version']) => 'editar',
                            in_array($r->accion, ['iniciar_sesion', 'cerrar_sesion']) => 'sesion',
                            in_array($r->accion, ['login_fallido', 'usuario_bloqueado']) => 'eliminar',
                            str_contains($r->accion, 'solicitud') || str_contains($r->accion, 'acceso') => 'solicitud',
                            default => 'default',
                        };

                        $etiquetas = [
                            'subir' => 'Subir', 'descargar' => 'Descargar',
                            'eliminar' => 'Eliminar', 'editar' => 'Editar',
                            'crear_carpeta' => 'Crear carpeta', 'crear_usuario' => 'Crear usuario',
                            'mover' => 'Mover', 'ver' => 'Visualizar',
                            'solicitar_acceso' => 'Sol. acceso', 'aprobar_solicitud' => 'Aprobar sol.',
                            'rechazar_solicitud' => 'Rechazar sol.', 'solicitar_subida' => 'Sol. subida',
                            'aprobar_subida' => 'Aprobar subida', 'rechazar_subida' => 'Rechazar subida',
                            'restaurar_version' => 'Restaurar versión', 'iniciar_sesion' => 'Iniciar sesión',
                            'cerrar_sesion' => 'Cerrar sesión', 'login_fallido' => 'Login fallido',
                            'usuario_bloqueado' => 'Bloqueado',
                        ];
                    @endphp
                    <tr>
                        <td style="white-space:nowrap;font-family:monospace;font-size:11px">
                            {{ $r->created_at?->format('d/m/Y') }}<br>
                            <span style="color:#94a3b8">{{ $r->created_at?->format('H:i:s') }}</span>
                        </td>
                        <td>
                            @if($r->usuario)
                            <div style="font-weight:600;color:#1e293b">{{ $r->usuario->nombre_completo }}</div>
                            <div style="font-size:10px;color:#94a3b8">{{ $r->usuario->rol }}</div>
                            @else
                            <span style="color:#94a3b8;font-style:italic">Sistema</span>
                            @endif
                        </td>
                        <td class="muted">{{ $r->usuario?->empresa?->nombre ?? '—' }}</td>
                        <td>
                            <span class="rp-accion-badge {{ $accionClass }}">
                                {{ $etiquetas[$r->accion] ?? $r->accion }}
                            </span>
                        </td>
                        <td class="muted" style="font-family:monospace;font-size:11px">
                            {{ ucfirst($r->recurso) }}
                            @if($r->recurso_id) <span style="color:#cbd5e1">#{{ $r->recurso_id }}</span> @endif
                        </td>
                        <td style="max-width:240px;font-size:11px;color:#64748b">
                            {{ Str::limit($r->detalles ?? '—', 60) }}
                        </td>
                        <td class="muted" style="font-family:monospace;font-size:11px;white-space:nowrap">
                            {{ $r->ip_address ?? '—' }}
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div style="margin-top:12px;font-size:11px;color:#94a3b8;text-align:center">
                Vista previa — el archivo Excel contiene los <strong>{{ number_format($totalFiltrado) }}</strong> registros completos según los filtros aplicados.
            </div>
            @endif
        </div>

    </div>{{-- /fc-main --}}
</div>{{-- /fc-wrapper --}}
</x-app-layout>
