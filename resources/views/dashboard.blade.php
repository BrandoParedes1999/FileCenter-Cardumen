<x-app-layout>
<style>
*, *::before, *::after {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

.fc-wrapper {
    display: flex;
    height: 100dvh;
    width: 100%;
    background: #524b4b; /* CAMBIADO: Fondo principal blanco */
    color: #1e293b; /* CAMBIADO: Texto oscuro principal para contraste */
    font-family: 'Segoe UI', system-ui, sans-serif;
    font-size: 15px;
    overflow: hidden;
}


/* ================================================
   ÁREA PRINCIPAL
   ================================================ */
.fc-main {
    flex: 1;
    display: flex;
    flex-direction: column;
    height: 100dvh;
    overflow: hidden;
    min-width: 0;
    background: #f8fafc; 
}

.fc-topbar {
    height: 58px;
    background: #ffffff; 
    border-bottom: 1px solid #e2e8f0; 
    display: flex; align-items: center;
    padding: 0 24px; gap: 14px;
    flex-shrink: 0;
}
.fc-search {
    flex: 1; max-width: 460px;
    background: #f1f5f9; border: 1px solid #e2e8f0; 
    border-radius: 9px; padding: 9px 16px;
    color: #1e293b; font-size: 14px; outline: none; 
}
.fc-search::placeholder { color: #94a3b8; } 
.fc-topbar-right { display: flex; align-items: center; gap: 16px; margin-left: auto; }
.fc-notif {
    position: relative; cursor: pointer;
    width: 36px; height: 36px;
    display: flex; align-items: center; justify-content: center;
}
.fc-notif-badge {
    position: absolute; top: 2px; right: 2px;
    width: 16px; height: 16px; background: #ef4444;
    border-radius: 50%; font-size: 9px;
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-weight: 700;
}
.fc-topbar-avatar {
    width: 38px; height: 38px;
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 13px; font-weight: 700; color: #fff;
}
.fc-topbar-name { font-size: 13px; font-weight: 600; color: #0f172a; } 
.fc-topbar-role { font-size: 11px; color: #7c3aed; }

.fc-content {
    flex: 1; overflow-y: auto;
    padding: 22px; display: flex; gap: 18px;
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 transparent; 
}
.fc-content-main { flex: 1; min-width: 0; }
.fc-content-side { width: 300px; min-width: 300px; }

/* Hero Banner */
.fc-hero {
    background: linear-gradient(135deg, #1e1b4b 0%, #312e81 60%, #4338ca 100%); 
    border-radius: 14px; padding: 24px 28px; margin-bottom: 18px;
    display: flex; align-items: center; justify-content: space-between; gap: 16px;
    box-shadow: 0 10px 15px -3px rgba(30, 27, 75, 0.2); 
}
.fc-hero-badge {
    font-size: 12px; color: #c7d2fe; 
    display: flex; align-items: center; gap: 6px; margin-bottom: 8px;
}
.fc-hero-title { font-size: 26px; font-weight: 700; color: #fff; margin-bottom: 6px; }
.fc-hero-sub   { font-size: 13px; color: #c7d2fe; } 
.fc-hero-btns  { display: flex; gap: 10px; flex-shrink: 0; }
.fc-btn-outline {
    background: transparent; border: 1px solid rgba(255,255,255,0.3); color: #fff; 
    padding: 9px 18px; border-radius: 8px; font-size: 13px; cursor: pointer;
    white-space: nowrap; transition: background .15s;
}
.fc-btn-outline:hover { background: rgba(255,255,255,0.1); } 
.fc-btn-solid {
    background: #ffffff; border: 1px solid #ffffff; color: #312e81; 
    padding: 9px 18px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer;
    white-space: nowrap; transition: background .15s;
}
.fc-btn-solid:hover { background: #f1f5f9; } 

/* Estadísticas */
.fc-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 14px; margin-bottom: 18px;
}
.fc-stat {
    background: #ffffff; border: 1px solid #e2e8f0; /* CAMBIADO: Fondo card blanco, borde claro */
    border-radius: 12px; padding: 18px 20px; position: relative;
    box-shadow: 6px 6px 12px #e2e8f0, -4px -4px 10px #ffffff; /* AÑADIDO: Relieve suave neumórfico */
    transition: transform 0.2s ease, box-shadow 0.2s ease; /* AÑADIDO: Transición suave */
}
.fc-stat:hover { 
    transform: translateY(-3px); /* AÑADIDO: Elevación hover */
    box-shadow: 10px 10px 20px #d1d9e6, -6px -6px 15px #ffffff; /* AÑADIDO: Sombra hover resaltada */
}
.fc-stat-icon {
    width: 36px; height: 36px; border-radius: 9px;
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 10px;
}
.fc-stat-arrow { position: absolute; top: 14px; right: 14px; color: #94a3b8; font-size: 18px; } /* CAMBIADO: Flecha estadística clara */
.fc-stat-num   { font-size: 30px; font-weight: 700; color: #0f172a; line-height: 1; } /* CAMBIADO: Número estadística oscuro */
.fc-stat-label { font-size: 12px; color: #64748b; margin-top: 5px; } /* MODIFICADO: Color label estadística claro */
.fc-stat-trend { font-size: 12px; margin-top: 7px; }
.fc-stat-trend.pos     { color: #16a34a; } /* MODIFICADO: Color tendencia pos verde claro */
.fc-stat-trend.neutral { color: #64748b; }

/* Gráfica */
.fc-chart-box {
    background: #ffffff; border: 1px solid #e2e8f0; /* CAMBIADO: Fondo gráfica blanco, borde claro */
    border-radius: 12px; padding: 18px 20px; margin-bottom: 18px;
    box-shadow: 6px 6px 12px #e2e8f0, -4px -4px 10px #ffffff; /* AÑADIDO: Relieve suave neumórfico */
}
.fc-chart-header {
    display: flex; justify-content: space-between;
    align-items: flex-start; margin-bottom: 14px;
}
.fc-chart-title { font-size: 15px; font-weight: 600; color: #0f172a; } /* CAMBIADO: Título gráfica oscuro */
.fc-chart-sub   { font-size: 12px; color: #64748b; margin-top: 2px; } /* MODIFICADO: Subtítulo gráfica claro */

/* Áreas */
.fc-areas-header {
    display: flex; justify-content: space-between;
    align-items: center; margin-bottom: 12px;
}
.fc-areas-title { font-size: 15px; font-weight: 600; color: #0f172a; } /* CAMBIADO: Título áreas oscuro */
.fc-areas-link  { font-size: 13px; color: #6366f1; cursor: pointer; text-decoration: none; }
.fc-areas-grid  { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.fc-area-card {
    background: #ffffff; border: 1px solid #e2e8f0; /* CAMBIADO: Fondo card blanco, borde claro */
    border-radius: 12px; padding: 14px 16px;
    display: flex; align-items: center; justify-content: space-between;
    cursor: pointer; text-decoration: none;
    box-shadow: 4px 4px 8px #e2e8f0, -2px -2px 6px #ffffff; /* AÑADIDO: Relieve suave neumórfico */
    transition: transform 0.2s ease, box-shadow 0.2s ease; /* AÑADIDO: Transición suave */
}
.fc-area-card:hover { 
    transform: translateY(-2px); /* AÑADIDO: Elevación hover */
    box-shadow: 6px 6px 12px #cbd5e1, -4px -4px 10px #ffffff; /* AÑADIDO: Sombra hover resaltada */
}
.fc-area-dot  { width: 11px; height: 11px; border-radius: 50%; }
.fc-area-name { font-size: 14px; font-weight: 600; color: #1e293b; } /* CAMBIADO: Nombre área oscuro */
.fc-area-meta { font-size: 12px; color: #64748b; margin-top: 2px; } /* MODIFICADO: Meta área claro */
.fc-area-chevron { color: #94a3b8; font-size: 20px; } /* CAMBIADO: Chevron claro */

/* Actividad */
.fc-activity-card {
    background: #ffffff; border: 1px solid #e2e8f0; /* CAMBIADO: Fondo actividad blanco, borde claro */
    border-radius: 12px; padding: 16px; margin-bottom: 14px;
    box-shadow: 6px 6px 12px #e2e8f0, -4px -4px 10px #ffffff; /* AÑADIDO: Relieve suave neumórfico */
}
.fc-activity-title {
    font-size: 14px; font-weight: 600; color: #0f172a; /* CAMBIADO: Título actividad oscuro */
    margin-bottom: 14px; display: flex; align-items: center; gap: 7px;
}
.fc-act-item {
    display: flex; align-items: flex-start; gap: 10px;
    padding: 8px 0; border-bottom: 1px solid #f1f5f9; /* CAMBIADO: Borde item claro */
}
.fc-act-item:last-child { border-bottom: none; }
.fc-act-icon {
    width: 28px; height: 28px; border-radius: 7px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; margin-top: 1px;
}
.fc-act-name { font-size: 13px; color: #475569; line-height: 1.4; } /* MODIFICADO: Color nombre item claro */
.fc-act-name strong { color: #0f172a; font-weight: 600; } /* CAMBIADO: Strong item oscuro */
.fc-act-file { font-size: 12px; color: #6366f1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-weight: 500; } /* CAMBIADO: Color archivo item azul claro, peso */
.fc-act-time { font-size: 11px; color: #94a3b8; margin-top: 2px; } /* MODIFICADO: Color tiempo item claro */

/* Roles */
.fc-roles-card {
    background: #feffff; border: 1px solid #e2e8f0; /* CAMBIADO: Fondo roles blanco, borde claro */
    border-radius: 12px; padding: 16px;
    box-shadow: 6px 6px 12px #e2e8f0, -4px -4px 10px #ffffff; /* AÑADIDO: Relieve suave neumórfico */
}
.fc-roles-title { font-size: 14px; font-weight: 600; color: #0a0a0a; margin-bottom: 14px; }
.fc-role-row    { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; }
.fc-role-name   { font-size: 12px; color: #475569; width: 55px; } /* MODIFICADO: Color nombre rol claro */
.fc-role-bar-bg { flex: 1; height: 7px; background: #f1f5f9; border-radius: 4px; overflow: hidden; box-shadow: inset 1px 1px 3px #d1d9e6; } /* CAMBIADO: Fondo barra claro, sombra interna */
.fc-role-bar    { height: 100%; border-radius: 4px; }
.fc-role-count  { font-size: 12px; color: #0a0a0a; width: 20px; text-align: right; font-weight: 600; } /* MODIFICADO: Color count rol claro, peso */

/* Iconos SVG subida/descarga dentro de ítem de actividad */
.icon-up   { fill: #10b981; } /* MODIFICADO: Color icono up verde claro */
.icon-down { fill: #f59e0b; } /* MODIFICADO: Color icono down naranja claro */
.icon-plus { fill: #6366f1; } /* MODIFICADO: Color icono plus azul claro */
.bg-up     { background: rgba(16, 185, 129, 0.1); } /* MODIFICADO: Fondo icono up verde claro */
.bg-down   { background: rgba(245, 158, 11, 0.1);  } /* MODIFICADO: Fondo icono down naranja claro */
.bg-plus   { background: rgba(99, 102, 241, 0.1); } /* MODIFICADO: Fondo icono plus azul claro */

/* ══ MODO OSCURO — dashboard ══ */
.dark .fc-wrapper      { background: #0d0c1d; }
.dark .fc-main         { background: #0d0c1d; }
.dark .fc-content      { background: #0d0c1d; scrollbar-color: #1e1b4b transparent; }

/* Topbar */
.dark .fc-topbar       { background: #13111f; border-color: #1e1b4b; }
.dark .fc-topbar-name  { color: #e0e7ff; }
.dark .fc-topbar-role  { color: #a5b4fc; }
.dark .fc-search {
    background: #1e1b4b;
    border-color: #2d2a5e;
    color: #e0e7ff;
}
.dark .fc-search::placeholder { color: #4f46e5; }

/* Stats */
.dark .fc-stat {
    background: #13111f;
    border-color: #1e1b4b;
    box-shadow: 4px 4px 10px #0a0918, -2px -2px 8px #1a1830;
}
.dark .fc-stat:hover {
    box-shadow: 6px 6px 16px #0a0918, -4px -4px 12px #1a1830;
}
.dark .fc-stat-num   { color: #e0e7ff; }
.dark .fc-stat-label { color: #6366f1; }
.dark .fc-stat-arrow { color: #4f46e5; }
.dark .fc-stat-trend.pos     { color: #34d399; }
.dark .fc-stat-trend.neutral { color: #6366f1; }

/* Gráfica */
.dark .fc-chart-box {
    background: #13111f;
    border-color: #1e1b4b;
    box-shadow: 4px 4px 10px #0a0918, -2px -2px 8px #1a1830;
}
.dark .fc-chart-title { color: #e0e7ff; }
.dark .fc-chart-sub   { color: #6366f1; }
.dark .fc-chart-box span[style*="color:#475569"] { color: #4f46e5 !important; }

/* Áreas */
.dark .fc-areas-title { color: #e0e7ff; }
.dark .fc-area-card {
    background: #13111f;
    border-color: #1e1b4b;
    box-shadow: 3px 3px 8px #0a0918, -2px -2px 6px #1a1830;
}
.dark .fc-area-card:hover {
    box-shadow: 5px 5px 14px #0a0918, -3px -3px 10px #1a1830;
    border-color: #4f46e5;
}
.dark .fc-area-name    { color: #e0e7ff; }
.dark .fc-area-meta    { color: #6366f1; }
.dark .fc-area-chevron { color: #4f46e5; }

/* Actividad */
.dark .fc-activity-card {
    background: #13111f;
    border-color: #1e1b4b;
    box-shadow: 4px 4px 10px #0a0918, -2px -2px 8px #1a1830;
}
.dark .fc-activity-title { color: #e0e7ff; }
.dark .fc-act-item       { border-color: #1e1b4b; }
.dark .fc-act-name       { color: #a5b4fc; }
.dark .fc-act-name strong { color: #e0e7ff; }
.dark .fc-act-file       { color: #818cf8; }
.dark .fc-act-time       { color: #4f46e5; }

/* Roles */
.dark .fc-roles-card {
    background: #13111f;
    border-color: #1e1b4b;
    box-shadow: 4px 4px 10px #0a0918, -2px -2px 8px #1a1830;
}
.dark .fc-roles-title  { color: #e0e7ff; }
.dark .fc-role-name    { color: #a5b4fc; }
.dark .fc-role-count   { color: #e0e7ff; }
.dark .fc-role-bar-bg  {
    background: #1e1b4b;
    box-shadow: inset 1px 1px 3px #0a0918;
}
</style>

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
                        <button  href= "#" class="fc-btn-outline">Gestionar Usuarios</button>
                        <button class="fc-btn-solid">Ver Permisos</button>
                    </div>
                </div>

                {{-- ── Estadísticas ── --}}
                <div class="fc-stats">
                </div>
            </header>

            {{-- Contenido --}}
            <div class="fc-content">

                {{-- Columna principal --}}
                <div class="fc-content-main">

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