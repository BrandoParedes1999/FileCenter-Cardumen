<x-app-layout>
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

.fc-wrapper {
    display: flex; height: 100dvh; width: 100%;
    background: #f8fafc; color: #1e293b;
    font-family: 'Segoe UI', system-ui, sans-serif;
    overflow: hidden;
}

.fc-main {
    flex: 1; display: flex; flex-direction: column;
    height: 100dvh; overflow: hidden; min-width: 0;
}

.fc-topbar {
    height: 58px; background: #fff;
    border-bottom: 1px solid #e2e8f0;
    display: flex; align-items: center;
    padding: 0 24px; flex-shrink: 0; gap: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}
.fc-topbar-left { flex: 1; }
.fc-topbar-title { font-size: 18px; font-weight: 800; color: #0f172a; letter-spacing: -.3px; }
.fc-topbar-sub   { font-size: 12px; color: #94a3b8; margin-top: 1px; }
.fc-topbar-right { display: flex; align-items: center; gap: 10px; }

.view-btns {
    display: flex; gap: 4px;
    background: #f1f5f9; border-radius: 10px; padding: 4px;
}
.view-btn {
    width: 32px; height: 32px; border-radius: 7px;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; border: none; background: transparent;
    color: #94a3b8; transition: all .15s;
}
.view-btn.active { background: #fff; color: #f97316; box-shadow: 0 1px 4px rgba(0,0,0,0.1); }
.view-btn:hover:not(.active) { color: #475569; }

.btn-nuevo {
    display: flex; align-items: center; gap: 7px;
    background: linear-gradient(135deg, #ea580c, #f97316);
    color: #fff; font-size: 13px; font-weight: 700;
    padding: 9px 18px; border-radius: 999px;
    border: none; cursor: pointer;
    box-shadow: 0 4px 14px rgba(234,88,12,0.35);
    transition: transform .15s, box-shadow .2s;
    text-decoration: none;
}
.btn-nuevo:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(234,88,12,0.45); }

.fc-notif {
    position: relative; cursor: pointer; width: 36px; height: 36px;
    display: flex; align-items: center; justify-content: center;
}
.fc-notif-badge {
    position: absolute; top: -2px; right: -2px; width: 16px; height: 16px;
    background: #ef4444; border-radius: 50%; font-size: 9px;
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-weight: 700; border: 2px solid #fff;
}
.fc-topbar-avatar {
    width: 36px; height: 36px;
    background: linear-gradient(135deg, #ea580c, #f97316);
    border-radius: 50%; display: flex; align-items: center;
    justify-content: center; font-size: 12px; font-weight: 700; color: #fff;
    flex-shrink: 0;
}
.fc-topbar-name { font-size: 13px; font-weight: 600; color: #1e293b; }
.fc-topbar-role { font-size: 10px; color: #ea580c; font-weight: 600; }

.fc-body { flex: 1; display: flex; overflow: hidden; }

/* ── Sidebar carpetas ── */
.carpetas-sidebar {
    width: 280px; min-width: 280px;
    background: #fff; border-right: 1px solid #e2e8f0;
    display: flex; flex-direction: column;
    overflow-y: auto; scrollbar-width: thin;
    scrollbar-color: #e2e8f0 transparent;
}
.carpetas-sidebar-header { padding: 16px 20px 10px; border-bottom: 1px solid #f1f5f9; }

.breadcrumb {
    display: flex; align-items: center; gap: 6px;
    font-size: 12px; color: #94a3b8; margin-bottom: 12px;
}
.breadcrumb a { color: #94a3b8; text-decoration: none; transition: color .15s; }
.breadcrumb a:hover { color: #ea580c; }
.breadcrumb-sep { font-size: 14px; color: #cbd5e1; }
.breadcrumb-current { color: #ea580c; font-weight: 600; }

.carpetas-label {
    font-size: 10px; font-weight: 700; color: #94a3b8;
    text-transform: uppercase; letter-spacing: .1em;
    padding: 10px 20px 6px;
}

.carpeta-item {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 20px; cursor: pointer;
    transition: background .15s;
    border-left: 3px solid transparent;
}
.carpeta-item:hover { background: #f8fafc; }
.carpeta-item.active { background: #fff7ed; border-left-color: #f97316; }
.carpeta-item-icon { color: #94a3b8; flex-shrink: 0; transition: color .15s; }
.carpeta-item.active .carpeta-item-icon { color: #f97316; }
.carpeta-item-name {
    flex: 1; font-size: 13px; color: #374151;
    font-weight: 500; white-space: nowrap;
    overflow: hidden; text-overflow: ellipsis;
}
.carpeta-item.active .carpeta-item-name { color: #ea580c; font-weight: 600; }
.carpeta-item-count {
    font-size: 12px; font-weight: 700; color: #f97316;
    background: rgba(249,115,22,0.1);
    padding: 2px 7px; border-radius: 999px; flex-shrink: 0;
}

/* ══ CONTENIDO ══ */
.fc-content {
    flex: 1; overflow-y: auto; padding: 24px;
    scrollbar-width: thin; scrollbar-color: #e2e8f0 transparent;
}

/* ── Banner ── */
.area-banner {
    background: linear-gradient(135deg, #ea580c 0%, #c2410c 50%, #f97316 100%);
    border-radius: 18px; padding: 24px 28px;
    margin-bottom: 24px; position: relative; overflow: hidden;
}
.area-banner::before {
    content: '';
    position: absolute; top: -30px; right: -30px;
    width: 180px; height: 180px;
    background: rgba(255,255,255,0.08); border-radius: 50%;
}
.area-banner::after {
    content: '';
    position: absolute; bottom: -40px; right: 60px;
    width: 120px; height: 120px;
    background: rgba(255,255,255,0.05); border-radius: 50%;
}
.area-banner-desc {
    font-size: 14px; color: rgba(255,255,255,0.85);
    line-height: 1.6; margin-bottom: 20px;
    position: relative; z-index: 1; max-width: 560px;
}
.area-banner-stats { display: flex; gap: 32px; position: relative; z-index: 1; }
.area-banner-stat-num { font-size: 28px; font-weight: 800; color: #fff; line-height: 1; }
.area-banner-stat-lbl { font-size: 11px; color: rgba(255,255,255,0.65); margin-top: 3px; font-weight: 500; }

.section-title {
    font-size: 16px; font-weight: 700; color: #0f172a;
    margin-bottom: 16px; display: flex; align-items: center; gap: 8px;
}
.section-title-line { flex: 1; height: 1px; background: #f1f5f9; }

/* ── Grid carpetas ── */
.carpetas-grid {
    display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px;
}
.carpetas-grid.list-view { grid-template-columns: 1fr; }

.carpeta-card {
    background: #fff; border: 1px solid #e2e8f0;
    border-radius: 16px; padding: 20px;
    transition: all .2s; cursor: pointer;
}
.carpeta-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(234,88,12,0.12);
    border-color: #fed7aa;
}
.carpeta-card-icon {
    width: 44px; height: 44px; border-radius: 12px;
    background: rgba(249,115,22,0.08);
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 14px;
}
.carpeta-card-name {
    font-size: 14px; font-weight: 700; color: #0f172a;
    margin-bottom: 6px; white-space: nowrap;
    overflow: hidden; text-overflow: ellipsis;
}
.carpeta-card-meta {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 14px;
}
.carpeta-card-archivos { font-size: 12px; color: #64748b; }
.carpeta-card-badge { font-size: 11px; font-weight: 600; padding: 3px 10px; border-radius: 999px; }
.badge-abierta     { background: rgba(34,197,94,0.1);  color: #16a34a; }
.badge-restringida { background: rgba(239,68,68,0.1);  color: #dc2626; }

.carpeta-card-actions {
    display: flex; gap: 8px; padding-top: 12px; border-top: 1px solid #f1f5f9;
}
.carpeta-action-btn {
    width: 28px; height: 28px; border-radius: 7px;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; border: none; background: transparent;
    transition: background .15s, color .15s;
}
.carpeta-action-btn.view { color: #22c55e; }
.carpeta-action-btn.view:hover { background: rgba(34,197,94,0.1); }
.carpeta-action-btn.edit { color: #f97316; }
.carpeta-action-btn.edit:hover { background: rgba(249,115,22,0.1); }

.carpeta-card.list-view {
    display: flex; align-items: center; gap: 16px; padding: 14px 18px;
}
.carpeta-card.list-view .carpeta-card-icon   { margin-bottom: 0; flex-shrink: 0; }
.carpeta-card.list-view .carpeta-card-name   { margin-bottom: 0; }
.carpeta-card.list-view .carpeta-card-meta   { margin-bottom: 0; flex: 1; }
.carpeta-card.list-view .carpeta-card-actions { border-top: none; padding-top: 0; margin-left: auto; }
</style>

<div class="fc-wrapper">

    @include('components.sidebar')

    <div class="fc-main">

        {{-- Topbar --}}
        <header class="fc-topbar">
            <div class="fc-topbar-left">
                <div class="fc-topbar-title" style="display:flex;align-items:center;gap:10px;">
                    <div style="
                        width:36px;height:36px;border-radius:10px;
                        border:1px solid #e2e8f0;background:#fff;
                        box-shadow:0 2px 8px rgba(234,88,12,0.12);
                        display:flex;align-items:center;justify-content:center;
                        padding:5px;flex-shrink:0;
                    ">
                        <img src="{{ asset('images/Seatools-Original.png') }}"
                             style="width:100%;height:100%;object-fit:contain;">
                    </div>
                    Seatools
                </div>
                <div class="fc-topbar-sub">4 carpetas · 31 archivos</div>
            </div>
            <div class="fc-topbar-right">
                <div class="view-btns">
                    <button class="view-btn active" id="btnGrid" onclick="setView('grid')">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M3 3h7v7H3V3zm0 11h7v7H3v-7zm11-11h7v7h-7V3zm0 11h7v7h-7v-7z"/>
                        </svg>
                    </button>
                    <button class="view-btn" id="btnList" onclick="setView('list')">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M3 4h2v2H3V4zm4 0h14v2H7V4zM3 9h2v2H3V9zm4 0h14v2H7V9zm-4 5h2v2H3v-2zm4 0h14v2H7v-2zm-4 5h2v2H3v-2zm4 0h14v2H7v-2z"/>
                        </svg>
                    </button>
                </div>
                <a href="#" class="btn-nuevo">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                    </svg>
                    Nuevo
                </a>
                <div class="fc-notif">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="#64748b">
                        <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/>
                    </svg>
                    <div class="fc-notif-badge">2</div>
                </div>
                <div class="fc-topbar-avatar">
                    {{ strtoupper(substr(Auth::user()->nombre, 0, 1)) }}{{ strtoupper(substr(Auth::user()->paterno, 0, 1)) }}
                </div>
                <div>
                    <div class="fc-topbar-name">{{ Auth::user()->nombre_completo }}</div>
                    <div class="fc-topbar-role">{{ Auth::user()->rol }}</div>
                </div>
            </div>
        </header>

        <div class="fc-body">

            {{-- Sidebar carpetas --}}
            <div class="carpetas-sidebar">
                <div class="carpetas-sidebar-header">
                    <div class="breadcrumb">
                        <a href="{{ route('cardumen') }}">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor" style="margin-right:2px">
                                <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                            </svg>
                            Áreas
                        </a>
                        <span class="breadcrumb-sep">›</span>
                        <span class="breadcrumb-current">Seatools</span>
                    </div>
                </div>

                <div class="carpetas-label">Carpetas</div>

                <div class="carpeta-item active" onclick="selectCarpeta(this)">
                    <div class="carpeta-item-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"/>
                        </svg>
                    </div>
                    <span class="carpeta-item-name">Administración</span>
                    <span class="carpeta-item-count">6</span>
                </div>

                <div class="carpeta-item" onclick="selectCarpeta(this)">
                    <div class="carpeta-item-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"/>
                        </svg>
                    </div>
                    <span class="carpeta-item-name">Operaciones</span>
                    <span class="carpeta-item-count">12</span>
                </div>

                <div class="carpeta-item" onclick="selectCarpeta(this)">
                    <div class="carpeta-item-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"/>
                        </svg>
                    </div>
                    <span class="carpeta-item-name">Facturación</span>
                    <span class="carpeta-item-count">8</span>
                </div>

                <div class="carpeta-item" onclick="selectCarpeta(this)">
                    <div class="carpeta-item-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"/>
                        </svg>
                    </div>
                    <span class="carpeta-item-name">Ventas / Negocios</span>
                    <span class="carpeta-item-count">5</span>
                </div>

            </div>{{-- /carpetas-sidebar --}}

            {{-- Contenido --}}
            <div class="fc-content">

                <div class="area-banner">
                    <p class="area-banner-desc">
                        Documentación de la empresa Seatools del corporativo Cardumen. Equipamiento y maquinaria industrial de alto rendimiento.
                    </p>
                    <div class="area-banner-stats">
                        <div>
                            <div class="area-banner-stat-num">4</div>
                            <div class="area-banner-stat-lbl">Carpetas</div>
                        </div>
                        <div>
                            <div class="area-banner-stat-num">31</div>
                            <div class="area-banner-stat-lbl">Archivos</div>
                        </div>
                        <div>
                            <div class="area-banner-stat-num">6</div>
                            <div class="area-banner-stat-lbl">Miembros</div>
                        </div>
                    </div>
                </div>

                <div class="section-title">
                    Selecciona una carpeta
                    <div class="section-title-line"></div>
                </div>

                <div class="carpetas-grid" id="carpetasGrid">

                    {{-- Administración --}}
                    <div class="carpeta-card">
                        <div class="carpeta-card-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#f97316" stroke-width="2">
                                <path d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"/>
                            </svg>
                        </div>
                        <div class="carpeta-card-name">Administración</div>
                        <div class="carpeta-card-meta">
                            <span class="carpeta-card-archivos">6 archivos</span>
                            <span class="carpeta-card-badge badge-abierta">Abierta</span>
                        </div>
                        <div class="carpeta-card-actions">
                            <button class="carpeta-action-btn view" title="Ver">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                            </button>
                            <button class="carpeta-action-btn edit" title="Editar">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Operaciones --}}
                    <div class="carpeta-card">
                        <div class="carpeta-card-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#f97316" stroke-width="2">
                                <path d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"/>
                            </svg>
                        </div>
                        <div class="carpeta-card-name">Operaciones</div>
                        <div class="carpeta-card-meta">
                            <span class="carpeta-card-archivos">12 archivos</span>
                            <span class="carpeta-card-badge badge-abierta">Abierta</span>
                        </div>
                        <div class="carpeta-card-actions">
                            <button class="carpeta-action-btn view" title="Ver">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                            </button>
                            <button class="carpeta-action-btn edit" title="Editar">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Facturación --}}
                    <div class="carpeta-card">
                        <div class="carpeta-card-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#f97316" stroke-width="2">
                                <path d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"/>
                            </svg>
                        </div>
                        <div class="carpeta-card-name">Facturación</div>
                        <div class="carpeta-card-meta">
                            <span class="carpeta-card-archivos">8 archivos</span>
                            <span class="carpeta-card-badge badge-abierta">Abierta</span>
                        </div>
                        <div class="carpeta-card-actions">
                            <button class="carpeta-action-btn view" title="Ver">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                            </button>
                            <button class="carpeta-action-btn edit" title="Editar">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Ventas / Negocios --}}
                    <div class="carpeta-card">
                        <div class="carpeta-card-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#f97316" stroke-width="2">
                                <path d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"/>
                            </svg>
                        </div>
                        <div class="carpeta-card-name">Ventas / Negocios</div>
                        <div class="carpeta-card-meta">
                            <span class="carpeta-card-archivos">5 archivos</span>
                            <span class="carpeta-card-badge badge-abierta">Abierta</span>
                        </div>
                        <div class="carpeta-card-actions">
                            <button class="carpeta-action-btn view" title="Ver">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                            </button>
                            <button class="carpeta-action-btn edit" title="Editar">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                </div>{{-- /carpetas-grid --}}
            </div>{{-- /fc-content --}}
        </div>{{-- /fc-body --}}
    </div>{{-- /fc-main --}}
</div>{{-- /fc-wrapper --}}

<script>
function setView(type) {
    const grid    = document.getElementById('carpetasGrid');
    const btnGrid = document.getElementById('btnGrid');
    const btnList = document.getElementById('btnList');
    const cards   = grid.querySelectorAll('.carpeta-card');
    if (type === 'grid') {
        grid.classList.remove('list-view');
        cards.forEach(c => c.classList.remove('list-view'));
        btnGrid.classList.add('active');
        btnList.classList.remove('active');
    } else {
        grid.classList.add('list-view');
        cards.forEach(c => c.classList.add('list-view'));
        btnList.classList.add('active');
        btnGrid.classList.remove('active');
    }
}

function selectCarpeta(el) {
    document.querySelectorAll('.carpeta-item').forEach(i => i.classList.remove('active'));
    el.classList.add('active');
}
</script>

</x-app-layout>