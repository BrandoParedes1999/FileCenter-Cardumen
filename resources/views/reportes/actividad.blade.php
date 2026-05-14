<x-app-layout>
@include('reportes.partials._styles')

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
                <div class="rp-title">Reporte de Uso de Archivos</div>
                <div class="rp-sub">Quién subió, descargó, visualizó o eliminó archivos · Solo Superadmin / Aux_QHSE</div>
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

        @include('reportes.partials._filtros')

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
