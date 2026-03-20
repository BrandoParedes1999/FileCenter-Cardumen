<x-app-layout>
    <div class="fc-wrapper">

        {{-- ══════════════════════════════
            SIDEBAR
            ══════════════════════════════ --}}
    @include('components.sidebar')

        {{-- ══════════════════════════════
            ÁREA PRINCIPAL
            ══════════════════════════════ --}}
        <div class="fc-main">

            {{-- Topbar --}}
            <header class="fc-topbar">
                <input class="fc-search" placeholder="Buscar archivos, carpetas..." />
                <div class="fc-topbar-right">
                    <div class="fc-notif">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="#64748b">
                            <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/>
                        </svg>
                        <div class="fc-notif-badge">2</div>
                    </div>
                    <div class="fc-topbar-avatar">
                        {{ strtoupper(substr(Auth::user()->nombre, 0, 1)) . strtoupper(substr(Auth::user()->paterno, 0, 1)) }}
                    </div>
                    <div>
                        <div class="fc-topbar-name">{{ Auth::user()->nombre_completo }}</div>
                        <div class="fc-topbar-role"> {{ Auth::user()->rol }} </div>
                    </div>
                </div>
            </header>

            {{-- Contenido --}}
            <div class="fc-content">

                {{-- Columna principal --}}
                <div class="fc-content-main">

                    {{-- ── Hero ── --}}
                    <div class="fc-hero">
                        <div>
                            <div class="fc-hero-badge">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="#a5b4fc">
                                    <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/>
                                </svg>
                                Super Administrador
                            </div>
                            <div class="fc-hero-title">Panel de Control Global</div>
                            <div class="fc-hero-sub">
                                Tienes acceso completo a todas las áreas y configuraciones del sistema.
                            </div>
                        </div>
                        <div class="fc-hero-btns">
                            <a href="{{ route('usuarios.index') }}" class=" {{ request()->routeIs('usuarios') ? 'active' : '' }}" }}>
                                <button class="fc-btn-outline" > Gestionar Usuarios </button>
                            </a>
                            <button class="fc-btn-solid">Ver Permisos</button>
                        </div>
                    </div>

                    {{-- ── Estadísticas ── --}}
                    <div class="fc-stats">

                        <div class="fc-stat">
                            <div class="fc-stat-icon" style="background:rgba(124,58,237,0.13)">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="#a78bfa">
                                    <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                                </svg>
                            </div>
                            <div class="fc-stat-arrow">↗</div>
                            <div class="fc-stat-num">{{ $totalUsuarios }}</div>
                            <div class="fc-stat-label">Usuarios activos</div>
                            <div class="fc-stat-trend neutral">en el sistema</div>
                        </div>

                        <div class="fc-stat">
                            <div class="fc-stat-icon" style="background:rgba(13,148,136,0.13)">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="#2dd4bf">
                                    <path d="M20 6h-8l-2-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2z"/>
                                </svg>
                            </div>
                            <div class="fc-stat-arrow">↗</div>
                            <div class="fc-stat-num">{{ $totalEmpresas }}</div>
                            <div class="fc-stat-label">Áreas activas</div>
                            <div class="fc-stat-trend neutral">empresas</div>
                        </div>

                        <div class="fc-stat">
                            <div class="fc-stat-icon" style="background:rgba(217,119,6,0.13)">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="#fbbf24">
                                    <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6z"/>
                                </svg>
                            </div>
                            <div class="fc-stat-arrow">↗</div>
                            <div class="fc-stat-num">{{ $totalArchivos }}</div>
                            <div class="fc-stat-label">Total archivos</div>
                            <div class="fc-stat-trend neutral">activos</div>
                        </div>

                        <div class="fc-stat">
                            <div class="fc-stat-icon" style="background:rgba(29,78,216,0.13)">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="#60a5fa">
                                    <path d="M20 6h-8l-2-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2z"/>
                                </svg>
                            </div>
                            <div class="fc-stat-arrow">↗</div>
                            <div class="fc-stat-num">{{ $totalCarpetas }}</div>
                            <div class="fc-stat-label">Total carpetas</div>
                            <div class="fc-stat-trend neutral">en el sistema</div>
                        </div>

                    </div>

                    {{-- ── Gráfica de barras ── --}}
                    <div class="fc-chart-box">
                        <div class="fc-chart-header">
                            <div>
                                <div class="fc-chart-title">Archivos por Área</div>
                                <div class="fc-chart-sub">Distribución de contenido activo</div>
                            </div>
                            <span style="color:#475569;font-size:18px">↗</span>
                        </div>
                        <div style="display:flex;align-items:flex-end;gap:4px;height:120px">
                            <div style="display:flex;flex-direction:column;justify-content:space-between;height:110px;padding-right:10px">
                                <span style="font-size:10px;color:#475569">{{ $maxArchivos }}</span>
                                <span style="font-size:10px;color:#475569">{{ round($maxArchivos*0.75) }}</span>
                                <span style="font-size:10px;color:#475569">{{ round($maxArchivos*0.5) }}</span>
                                <span style="font-size:10px;color:#475569">{{ round($maxArchivos*0.25) }}</span>
                                <span style="font-size:10px;color:#475569">0</span>
                            </div>
                            <div style="flex:1;display:flex;align-items:flex-end;gap:14px;height:110px">
                                @foreach($empresas as $emp)
                                @php $pct = max(round(($emp->total_archivos / $maxArchivos) * 100), 4); @endphp
                                <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:5px">
                                    <div style="width:100%;background:linear-gradient(to top,{{ $emp->color_primario ?? '#4338ca' }},{{ $emp->color_secundario ?? '#6366f1' }});border-radius:5px 5px 0 0;height:{{ $pct }}%"
                                        title="{{ $emp->nombre }}: {{ $emp->total_archivos }} archivos"></div>
                                    <span style="font-size:10px;color:#475569;white-space:nowrap;overflow:hidden;max-width:55px;text-overflow:ellipsis"
                                        title="{{ $emp->nombre }}">{{ $emp->siglas }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- ── Todas las Áreas ── --}}
                    <div>
                        <div class="fc-areas-header">
                            <div class="fc-areas-title">Todas las Áreas</div>
                            <a href="#" class="fc-areas-link">Ver todas →</a>
                        </div>
                        <div class="fc-areas-grid">
                            @foreach($empresas as $emp)
                            <a href="{{ route('carpetas.index') }}" class="fc-area-card">
                                <div style="display:flex;align-items:center;gap:11px">
                                    <div class="fc-area-dot" style="background:{{ $emp->color_primario ?? '#4f46e5' }}"></div>
                                    <div>
                                        <div class="fc-area-name">
                                            {{ $emp->nombre }}
                                            @if($emp->es_corporativo)
                                            <span style="font-size:9px;background:rgba(27,58,107,0.1);color:#1b3a6b;padding:1px 5px;border-radius:4px;margin-left:3px">CORP</span>
                                            @endif
                                        </div>
                                        <div class="fc-area-meta">
                                            {{ $emp->total_archivos }} archivos · {{ $emp->total_miembros }} miembro{{ $emp->total_miembros != 1 ? 's' : '' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="fc-area-chevron">›</div>
                            </a>
                            @endforeach
                        </div>
                    </div>

                </div>{{-- /fc-content-main --}}

                {{-- ══ Panel lateral derecho ══ --}}
                <div class="fc-content-side">

                    {{-- Actividad Reciente --}}
                    <div class="fc-activity-card">
                        <div class="fc-activity-title">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="#22c55e">
                                <path d="M3.5 18.49l6-6.01 4 4L22 6.92l-1.41-1.41-7.09 7.97-4-4L2 16.99z"/>
                            </svg>
                            Actividad Reciente
                        </div>

                        @forelse($actividad as $act)
                        @php
                            $mapa = [
                                'subir'            => ['bg-up',   'icon-up',   'M9 16h6v-6h4l-7-7-7 7h4zm-4 2h14v2H5z',         'subió'],
                                'descargar'        => ['bg-down', 'icon-down', 'M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z',   'descargó'],
                                'crear_carpeta'    => ['bg-plus', 'icon-plus', 'M19 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-2 10h-4v4h-2v-4H7v-2h4V7h2v4h4v2z', 'creó carpeta'],
                                'eliminar'         => ['bg-down', 'icon-down', 'M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z', 'eliminó'],
                                'restaurar_version'=> ['bg-plus', 'icon-plus', 'M13 3c-4.97 0-9 4.03-9 9H1l3.89 3.89.07.14L9 12H6c0-3.87 3.13-7 7-7s7 3.13 7 7-3.13 7-7 7c-1.93 0-3.68-.79-4.94-2.06l-1.42 1.42C8.27 19.99 10.51 21 13 21c4.97 0 9-4.03 9-9s-4.03-9-9-9z', 'restauró versión'],
                            ];
                            [$bgClass, $iconClass, $path, $textoAccion] = $mapa[$act->accion] ?? ['bg-plus','icon-plus','M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10z',$act->accion];
                        @endphp
                        <div class="fc-act-item">
                            <div class="fc-act-icon {{ $bgClass }}">
                                <svg width="13" height="13" viewBox="0 0 24 24" class="{{ $iconClass }}">
                                    <path d="{{ $path }}"/>
                                </svg>
                            </div>
                            <div style="flex:1;min-width:0">
                                <div class="fc-act-name">
                                    <strong>{{ $act->usuario?->nombre ?? 'Sistema' }} {{ $act->usuario?->paterno ?? '' }}</strong>
                                    {{ $textoAccion }}
                                </div>
                                <div class="fc-act-file">{{ Str::limit($act->detalles ?? '—', 38) }}</div>
                                <div class="fc-act-time">{{ $act->created_at?->diffForHumans() ?? '—' }}</div>
                            </div>
                        </div>
                        @empty
                        <div style="padding:20px;text-align:center;font-size:13px;color:#94a3b8">
                            Sin actividad reciente registrada
                        </div>
                        @endforelse
                    </div>{{-- /fc-activity-card --}}

                    <div class="fc-roles-card">
                        <div class="fc-roles-title">Usuarios por Rol</div>

                        @foreach($usuariosPorRol as $ur)
                        <div class="fc-role-row">
                            <div class="fc-role-name">{{ $ur->rol }}</div>
                            <div class="fc-role-bar-bg">
                                <div class="fc-role-bar"
                                    style="width:{{ round(($ur->total / $maxRol) * 100) }}%;background:{{ $ur->color }}">
                                </div>
                            </div>
                            <div class="fc-role-count">{{ $ur->total }}</div>
                        </div>
                        @endforeach

                        @if($usuariosPorRol->isEmpty())
                        <div style="padding:16px 0;text-align:center;font-size:12px;color:#94a3b8">Sin datos</div>
                        @endif
                    </div>

                </div>{{-- /fc-content-side --}}

            </div>{{-- /fc-content --}}
        </div>{{-- /fc-main --}}
    </div>{{-- /fc-wrapper --}}
</x-app-layout>