<x-app-layout>
<div class="fc-wrapper">

    @include('components.sidebar')

    <div class="fc-main">

        <header class="fc-topbar">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#6366f1">
                <path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
            </svg>
            <span class="fc-topbar-title">Solicitudes de Acceso</span>
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

            {{-- Flash --}}
            @if(session('success'))
            <div class="fc-flash success">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="#059669"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                {{ session('success') }}
            </div>
            @endif

            {{-- Page header --}}
            <div class="fc-page-header">
                <div>
                    <h1 class="fc-page-title">Solicitudes de Acceso</h1>
                    <div style="font-size:13px;color:var(--fc-text-muted);margin-top:2px">
                        @if(in_array(Auth::user()->rol, ['Superadmin','Aux_QHSE','Admin','Gerente']))
                            Gestiona las solicitudes de acceso a archivos de tu empresa
                        @else
                            Historial de tus solicitudes de acceso a archivos
                        @endif
                    </div>
                </div>
                <a href="{{ route('solicitudes.create') }}" class="fc-btn fc-btn-primary">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
                    Nueva solicitud
                </a>
            </div>

            {{-- Filtros de estado --}}
            <div class="fc-filter-bar" style="margin-bottom:20px">
                @php
                    $filtroActual = request('estado', 'todas');
                    $estados = ['todas'=>'Todas','pendiente'=>'Pendientes','aprobada'=>'Aprobadas','rechazada'=>'Rechazadas'];
                    $colores  = ['pendiente'=>'#d97706','aprobada'=>'#059669','rechazada'=>'#dc2626'];
                @endphp
                @foreach($estados as $val => $label)
                <a href="{{ route('solicitudes.index', ['estado' => $val]) }}"
                   class="fc-btn fc-btn-sm {{ $filtroActual === $val ? 'fc-btn-primary' : 'fc-btn-outline' }}">
                    {{ $label }}
                    @if($val === 'pendiente' && isset($pendientes) && $pendientes > 0)
                        <span style="background:#dc2626;color:#fff;border-radius:999px;padding:1px 7px;font-size:10px;margin-left:4px">
                            {{ $pendientes }}
                        </span>
                    @endif
                </a>
                @endforeach

                {{-- Buscador --}}
                <form method="GET" action="{{ route('solicitudes.index') }}" style="margin-left:auto">
                    <input type="hidden" name="estado" value="{{ $filtroActual }}">
                    <div class="fc-search-wrap">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round">
                            <circle cx="11" cy="11" r="6"/><path d="m21 21-4.35-4.35"/>
                        </svg>
                        <input type="text" name="q" value="{{ request('q') }}"
                               placeholder="Buscar por archivo o usuario..."
                               onchange="this.form.submit()">
                    </div>
                </form>
            </div>

            {{-- Tabla de solicitudes --}}
            @if($solicitudes->isEmpty())
            <div class="fc-empty">
                <div class="fc-empty-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="#a5b4fc">
                        <path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                    </svg>
                </div>
                <div class="fc-empty-title">No hay solicitudes</div>
                <div class="fc-empty-sub">
                    @if($filtroActual !== 'todas')
                        No hay solicitudes con estado "{{ $estados[$filtroActual] }}".
                    @else
                        Aún no se han realizado solicitudes de acceso.
                    @endif
                </div>
                <a href="{{ route('solicitudes.create') }}" class="fc-btn fc-btn-primary">
                    Nueva solicitud
                </a>
            </div>

            @else
            <div class="fc-card" style="overflow:hidden">
                <table class="fc-table">
                    <thead>
                        <tr>
                            <th>Archivo solicitado</th>
                            <th>Solicitante</th>
                            @if(in_array(Auth::user()->rol, ['Superadmin','Aux_QHSE','Admin','Gerente']))
                            <th>Empresa</th>
                            @endif
                            <th>Motivo</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th style="text-align:right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($solicitudes as $sol)
                        @php
                            $estadoColor = [
                                'pendiente' => ['bg'=>'rgba(245,158,11,.1)','color'=>'#d97706'],
                                'aprobada'  => ['bg'=>'rgba(5,150,105,.1)', 'color'=>'#059669'],
                                'rechazada' => ['bg'=>'rgba(220,38,38,.1)', 'color'=>'#dc2626'],
                            ][$sol->estado] ?? ['bg'=>'#f1f5f9','color'=>'#64748b'];
                        @endphp
                        <tr>
                            {{-- Archivo --}}
                            <td>
                                <div style="display:flex;align-items:center;gap:10px">
                                    <div style="width:32px;height:32px;border-radius:8px;background:rgba(99,102,241,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="#6366f1">
                                            <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <div style="font-size:13px;font-weight:600;color:var(--fc-text)">
                                            {{ Str::limit($sol->archivo->nombre_original ?? 'Archivo eliminado', 35) }}
                                        </div>
                                        <div style="font-size:11px;color:var(--fc-text-muted)">
                                            {{ $sol->archivo->carpeta->nombre ?? '—' }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Solicitante --}}
                            <td>
                                <div class="fc-user-cell">
                                    <div class="fc-user-avatar" style="width:30px;height:30px;font-size:10px">
                                        {{ strtoupper(substr($sol->usuario->nombre ?? '?',0,1)) }}{{ strtoupper(substr($sol->usuario->paterno ?? '',0,1)) }}
                                    </div>
                                    <div>
                                        <div class="fc-user-nombre" style="font-size:12px">{{ $sol->usuario->nombre_completo ?? '—' }}</div>
                                        <div class="fc-user-email">{{ $sol->usuario->email ?? '' }}</div>
                                    </div>
                                </div>
                            </td>

                            @if(in_array(Auth::user()->rol, ['Superadmin','Aux_QHSE','Admin','Gerente']))
                            <td>
                                <span style="font-size:11px;font-weight:600;padding:3px 8px;border-radius:6px;background:{{ $sol->usuario->empresa->color_secundario ?? '#f1f5f9' }}22;color:{{ $sol->usuario->empresa->color_primario ?? '#64748b' }}">
                                    {{ $sol->usuario->empresa->siglas ?? '—' }}
                                </span>
                            </td>
                            @endif

                            {{-- Motivo --}}
                            <td style="max-width:200px">
                                <span style="font-size:12px;color:var(--fc-text-muted)">
                                    {{ Str::limit($sol->motivo ?? '—', 50) }}
                                </span>
                            </td>

                            {{-- Estado --}}
                            <td>
                                <span class="fc-badge" style="background:{{ $estadoColor['bg'] }};color:{{ $estadoColor['color'] }}">
                                    {{ ucfirst($sol->estado) }}
                                </span>
                            </td>

                            {{-- Fecha --}}
                            <td style="font-size:12px;color:var(--fc-text-muted);white-space:nowrap">
                                {{ $sol->created_at?->format('d/m/Y') ?? '—' }}
                            </td>

                            {{-- Acciones --}}
                            <td>
                                <div class="fc-table-actions" style="justify-content:flex-end">
                                    <a href="{{ route('solicitudes.show', $sol) }}"
                                       class="fc-action-ico" title="Ver detalle">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                                        </svg>
                                    </a>

                                    @if($sol->estado === 'pendiente' && in_array(Auth::user()->rol, ['Superadmin','Aux_QHSE','Admin','Gerente']))
                                    <form method="POST" action="{{ route('solicitudes.aprobar', $sol) }}">
                                        @csrf
                                        <button type="submit" class="fc-action-ico toggle" title="Aprobar"
                                                style="color:#059669">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                                            </svg>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('solicitudes.rechazar', $sol) }}">
                                        @csrf
                                        <button type="submit" class="fc-action-ico danger" title="Rechazar">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                                            </svg>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Paginación --}}
                @if($solicitudes->hasPages())
                <div class="fc-pagination">
                    <div class="fc-pag-info">
                        Mostrando {{ $solicitudes->firstItem() }}–{{ $solicitudes->lastItem() }} de {{ $solicitudes->total() }}
                    </div>
                    <div class="fc-pag-links">
                        @if($solicitudes->onFirstPage())
                            <span class="fc-pag-btn disabled">‹ Anterior</span>
                        @else
                            <a href="{{ $solicitudes->previousPageUrl() }}" class="fc-pag-btn">‹ Anterior</a>
                        @endif
                        @foreach($solicitudes->getUrlRange(1, $solicitudes->lastPage()) as $page => $url)
                            <a href="{{ $url }}" class="fc-pag-btn {{ $page == $solicitudes->currentPage() ? 'active' : '' }}">{{ $page }}</a>
                        @endforeach
                        @if($solicitudes->hasMorePages())
                            <a href="{{ $solicitudes->nextPageUrl() }}" class="fc-pag-btn">Siguiente ›</a>
                        @else
                            <span class="fc-pag-btn disabled">Siguiente ›</span>
                        @endif
                    </div>
                </div>
                @endif
            </div>
            @endif

        </div>{{-- /fc-content --}}
    </div>{{-- /fc-main --}}
</div>{{-- /fc-wrapper --}}
</x-app-layout>