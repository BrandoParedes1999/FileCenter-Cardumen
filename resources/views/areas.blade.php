<x-app-layout>
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

.fc-wrapper {
    display: flex; height: 100dvh; width: 100%;
    background: #f1f5f9; color: #1e293b;
    font-family: 'Segoe UI', system-ui, sans-serif;
    overflow: hidden;
}

/* ══ MAIN ══ */
.fc-main { flex: 1; display: flex; flex-direction: column; height: 100dvh; overflow: hidden; min-width: 0; }

/* Topbar */
.fc-topbar {
    height: 58px; background: #fff;
    border-bottom: 1px solid #e2e8f0;
    display: flex; align-items: center;
    padding: 0 28px; flex-shrink: 0;
    box-shadow: 0 1px 3px rgba(0,0,0,0.06);
}
.fc-topbar-left { flex: 1; }
.fc-topbar-title { font-size: 18px; font-weight: 800; color: #0f172a; letter-spacing: -.3px; }
.fc-topbar-sub   { font-size: 12px; color: #94a3b8; margin-top: 1px; }
.fc-topbar-right { display: flex; align-items: center; gap: 12px; }

/* Botón Nueva Área */
.btn-nueva-area {
    display: flex; align-items: center; gap: 7px;
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
    color: #fff; font-size: 13px; font-weight: 700;
    padding: 9px 18px; border-radius: 999px;
    border: none; cursor: pointer;
    box-shadow: 0 4px 14px rgba(79,70,229,0.35);
    transition: transform .15s, box-shadow .2s;
    text-decoration: none;
}
.btn-nueva-area:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(79,70,229,0.45);
}

.fc-notif {
    position: relative; cursor: pointer; width: 36px; height: 36px;
    display: flex; align-items: center; justify-content: center;
}
.fc-notif-badge {
    position: absolute; top: -3px; right: -3px; width: 16px; height: 16px;
    background: #ef4444; border-radius: 50%; font-size: 9px;
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-weight: 700; border: 2px solid #fff;
}
.fc-topbar-avatar {
    width: 36px; height: 36px;
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
    border-radius: 50%; display: flex; align-items: center;
    justify-content: center; font-size: 12px; font-weight: 700; color: #fff;
}
.fc-topbar-name { font-size: 13px; font-weight: 600; color: #1e293b; }
.fc-topbar-role { font-size: 10px; color: #7c3aed; font-weight: 600; }

/* Contenido scrolleable */
.fc-content {
    flex: 1; overflow-y: auto; padding: 24px 28px;
    scrollbar-width: thin; scrollbar-color: #e2e8f0 transparent;
}

/* ══ BARRA DE FILTROS ══ */
.filter-bar {
    display: flex; align-items: center; gap: 12px;
    margin-bottom: 24px; flex-wrap: wrap;
}
.search-wrap {
    flex: 1; min-width: 220px; max-width: 340px; position: relative;
}
.search-icon {
    position: absolute; left: 13px; top: 50%;
    transform: translateY(-50%); color: #94a3b8;
}
.search-input {
    width: 100%; padding: 10px 14px 10px 38px;
    background: #fff; border: 1px solid #e2e8f0;
    border-radius: 12px; font-size: 13px; color: #1e293b;
    outline: none; transition: border-color .2s, box-shadow .2s;
}
.search-input:focus {
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99,102,241,0.1);
}
.search-input::placeholder { color: #94a3b8; }

.filter-tabs { display: flex; gap: 6px; }
.filter-tab {
    padding: 9px 18px; border-radius: 999px; font-size: 13px;
    font-weight: 600; cursor: pointer; border: 1.5px solid #e2e8f0;
    background: #fff; color: #64748b; transition: all .15s;
}
.filter-tab:hover { border-color: #c7d2fe; color: #4f46e5; }
.filter-tab.active {
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
    color: #fff; border-color: transparent;
    box-shadow: 0 4px 12px rgba(79,70,229,0.25);
}

/* ══ GRID DE TARJETAS ══ */
.areas-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}

/* ══ TARJETA DE ÁREA ══ */
.area-card {
    background: #fff; border: 1px solid #e2e8f0;
    border-radius: 20px; overflow: hidden;
    transition: transform .2s, box-shadow .2s, border-color .2s;
    cursor: pointer; position: relative;
}
.area-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 36px rgba(0,0,0,0.1);
    border-color: #c7d2fe;
}
.area-card-stripe { height: 5px; width: 100%; }
.area-card-body { padding: 20px 22px; }

.area-card-header {
    display: flex; align-items: flex-start;
    justify-content: space-between; margin-bottom: 12px;
}
.area-icon-wrap {
    width: 48px; height: 48px; border-radius: 14px;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.area-card-chevron {
    width: 28px; height: 28px; border-radius: 8px;
    background: #f8fafc; border: 1px solid #e2e8f0;
    display: flex; align-items: center; justify-content: center;
    color: #94a3b8; font-size: 14px;
    transition: background .15s, color .15s;
}
.area-card:hover .area-card-chevron { background: #ede9fe; color: #7c3aed; }
.area-name   { font-size: 17px; font-weight: 800; color: #0f172a; margin-bottom: 4px; }
.area-badge  {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 3px 10px; border-radius: 999px;
    font-size: 11px; font-weight: 600;
}
.area-desc {
    font-size: 12px; color: #64748b; line-height: 1.55; margin-bottom: 16px;
}

.area-stats {
    display: grid; grid-template-columns: repeat(3, 1fr);
    gap: 10px; margin-bottom: 16px;
}
.area-stat-box { border-radius: 12px; padding: 12px 10px; text-align: center; }
.area-stat-icon { font-size: 18px; margin-bottom: 4px; }
.area-stat-num  { font-size: 20px; font-weight: 800; color: #0f172a; line-height: 1; }
.area-stat-lbl  { font-size: 10px; color: #64748b; margin-top: 3px; font-weight: 500; }

.area-folders { border-top: 1px solid #f1f5f9; padding-top: 14px; }
.area-folder-row {
    display: flex; align-items: center; justify-content: space-between; padding: 5px 0;
}
.area-folder-name {
    display: flex; align-items: center; gap: 8px;
    font-size: 13px; color: #475569;
}
.area-folder-dot { width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0; }
.area-folder-count { font-size: 13px; font-weight: 700; }
.area-folders-more { font-size: 12px; color: #94a3b8; margin-top: 4px; padding-left: 15px; }
</style>

<div class="fc-wrapper">

    {{-- ══ SIDEBAR ══ --}}
    @include('components.sidebar')

    {{-- ══ MAIN ══ --}}
    <div class="fc-main">

        {{-- Topbar --}}
        <header class="fc-topbar">
            <div class="fc-topbar-left">
                <div class="fc-topbar-title">Áreas de la Empresa</div>
                <div class="fc-topbar-sub">5 de 5 áreas accesibles</div>
            </div>
            <div class="fc-topbar-right">
                <a href="#" class="btn-nueva-area">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                    </svg>
                    Nueva Área
                </a>
                <div class="fc-notif">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="#64748b">
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

        {{-- Contenido --}}
        <div class="fc-content">

            {{-- Barra de filtros --}}
            <div class="filter-bar">
                <div class="search-wrap">
                    <div class="search-icon">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                        </svg>
                    </div>
                    <input class="search-input" type="text" id="searchAreas" placeholder="Buscar áreas..." />
                </div>
                <div class="filter-tabs">
                    <button class="filter-tab active" data-filter="todas">Todas</button>
                    <button class="filter-tab" data-filter="acceso">Con Acceso</button>
                    <button class="filter-tab" data-filter="restringidas">Restringidas</button>
                </div>
            </div>

            {{-- Grid de áreas --}}
            <div class="areas-grid" id="areasGrid">

                {{-- ── CARDUMEN ── --}}
                <div class="area-card" data-acceso="completo">
                    <div class="area-card-stripe" style="background:linear-gradient(90deg,#7c3aed,#4f46e5)"></div>
                    <div class="area-card-body">
                        <div class="area-card-header">
                            <div style="display:flex;align-items:center;gap:12px;">
                                <div class="area-icon-wrap" style="background:rgba(124,58,237,0.1)">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="area-name">Cardumen</div>
                                    <span class="area-badge" style="background:rgba(124,58,237,0.1);color:#7c3aed;">
                                        <span style="width:5px;height:5px;border-radius:50%;background:#7c3aed;display:inline-block;"></span>
                                        Acceso Completo
                                    </span>
                                </div>
                            </div>
                            <div class="area-card-chevron">›</div>
                        </div>
                        <p class="area-desc">Área corporativa del Grupo Cardumen. Documentación central compartida con todas las empresas.</p>
                        <div class="area-stats">
                            <div class="area-stat-box" style="background:rgba(124,58,237,0.07)">
                                <div class="area-stat-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                </div>
                                <div class="area-stat-num">21</div>
                                <div class="area-stat-lbl">Archivos</div>
                            </div>
                            <div class="area-stat-box" style="background:rgba(245,158,11,0.08)">
                                <div class="area-stat-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2"><path d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"/></svg>
                                </div>
                                <div class="area-stat-num">7</div>
                                <div class="area-stat-lbl">Carpetas</div>
                            </div>
                            <div class="area-stat-box" style="background:rgba(14,165,233,0.08)">
                                <div class="area-stat-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0ea5e9" stroke-width="2"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/></svg>
                                </div>
                                <div class="area-stat-num">2.4 GB</div>
                                <div class="area-stat-lbl">Storage</div>
                            </div>
                        </div>
                        <div class="area-folders">
                            <div class="area-folder-row">
                                <div class="area-folder-name"><div class="area-folder-dot" style="background:#7c3aed"></div>Recursos Humanos</div>
                                <span class="area-folder-count" style="color:#7c3aed">4</span>
                            </div>
                            <div class="area-folder-row">
                                <div class="area-folder-name"><div class="area-folder-dot" style="background:#7c3aed"></div>SG (Sistemas de Gestión)</div>
                                <span class="area-folder-count" style="color:#7c3aed">3</span>
                            </div>
                            <div class="area-folder-row">
                                <div class="area-folder-name"><div class="area-folder-dot" style="background:#7c3aed"></div>Controller</div>
                                <span class="area-folder-count" style="color:#7c3aed">3</span>
                            </div>
                            <div class="area-folders-more">+4 carpetas más</div>
                        </div>
                    </div>
                </div>

                {{-- ── OMC ── --}}
                <div class="area-card" data-acceso="completo">
                    <div class="area-card-stripe" style="background:linear-gradient(90deg,#06b6d4,#0891b2)"></div>
                    <div class="area-card-body">
                        <div class="area-card-header">
                            <div style="display:flex;align-items:center;gap:12px;">
                                <div class="area-icon-wrap" style="background:rgba(6,182,212,0.1)">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#06b6d4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="area-name">OMC</div>
                                    <span class="area-badge" style="background:rgba(6,182,212,0.1);color:#0891b2;">
                                        <span style="width:5px;height:5px;border-radius:50%;background:#0891b2;display:inline-block;"></span>
                                        Acceso Completo
                                    </span>
                                </div>
                            </div>
                            <div class="area-card-chevron">›</div>
                        </div>
                        <p class="area-desc">Documentación de la empresa OMC del corporativo Cardumen.</p>
                        <div class="area-stats">
                            <div class="area-stat-box" style="background:rgba(6,182,212,0.07)">
                                <div class="area-stat-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#06b6d4" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                </div>
                                <div class="area-stat-num">24</div>
                                <div class="area-stat-lbl">Archivos</div>
                            </div>
                            <div class="area-stat-box" style="background:rgba(245,158,11,0.08)">
                                <div class="area-stat-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2"><path d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"/></svg>
                                </div>
                                <div class="area-stat-num">4</div>
                                <div class="area-stat-lbl">Carpetas</div>
                            </div>
                            <div class="area-stat-box" style="background:rgba(14,165,233,0.08)">
                                <div class="area-stat-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0ea5e9" stroke-width="2"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/></svg>
                                </div>
                                <div class="area-stat-num">3.2 GB</div>
                                <div class="area-stat-lbl">Storage</div>
                            </div>
                        </div>
                        <div class="area-folders">
                            <div class="area-folder-row">
                                <div class="area-folder-name"><div class="area-folder-dot" style="background:#f59e0b"></div>Administración</div>
                                <span class="area-folder-count" style="color:#f59e0b">7</span>
                            </div>
                            <div class="area-folder-row">
                                <div class="area-folder-name"><div class="area-folder-dot" style="background:#f97316"></div>Operaciones</div>
                                <span class="area-folder-count" style="color:#f97316">9</span>
                            </div>
                            <div class="area-folder-row">
                                <div class="area-folder-name"><div class="area-folder-dot" style="background:#06b6d4"></div>Facturación</div>
                                <span class="area-folder-count" style="color:#06b6d4">5</span>
                            </div>
                            <div class="area-folders-more">+1 carpetas más</div>
                        </div>
                    </div>
                </div>

                {{-- ── SEAWARD ── --}}
                <div class="area-card" data-acceso="completo">
                    <div class="area-card-stripe" style="background:linear-gradient(90deg,#06b6d4,#22d3ee)"></div>
                    <div class="area-card-body">
                        <div class="area-card-header">
                            <div style="display:flex;align-items:center;gap:12px;">
                                <div class="area-icon-wrap" style="background:rgba(6,182,212,0.1)">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#06b6d4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="area-name">Seaward</div>
                                    <span class="area-badge" style="background:rgba(6,182,212,0.1);color:#0891b2;">
                                        <span style="width:5px;height:5px;border-radius:50%;background:#0891b2;display:inline-block;"></span>
                                        Acceso Completo
                                    </span>
                                </div>
                            </div>
                            <div class="area-card-chevron">›</div>
                        </div>
                        <p class="area-desc">Documentación de la empresa Seaward del corporativo Cardumen.</p>
                        <div class="area-stats">
                            <div class="area-stat-box" style="background:rgba(6,182,212,0.07)">
                                <div class="area-stat-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#06b6d4" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                </div>
                                <div class="area-stat-num">28</div>
                                <div class="area-stat-lbl">Archivos</div>
                            </div>
                            <div class="area-stat-box" style="background:rgba(245,158,11,0.08)">
                                <div class="area-stat-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2"><path d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"/></svg>
                                </div>
                                <div class="area-stat-num">4</div>
                                <div class="area-stat-lbl">Carpetas</div>
                            </div>
                            <div class="area-stat-box" style="background:rgba(14,165,233,0.08)">
                                <div class="area-stat-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0ea5e9" stroke-width="2"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/></svg>
                                </div>
                                <div class="area-stat-num">4.1 GB</div>
                                <div class="area-stat-lbl">Storage</div>
                            </div>
                        </div>
                        <div class="area-folders">
                            <div class="area-folder-row">
                                <div class="area-folder-name"><div class="area-folder-dot" style="background:#f59e0b"></div>Administración</div>
                                <span class="area-folder-count" style="color:#f59e0b">8</span>
                            </div>
                            <div class="area-folder-row">
                                <div class="area-folder-name"><div class="area-folder-dot" style="background:#f97316"></div>Operaciones</div>
                                <span class="area-folder-count" style="color:#f97316">10</span>
                            </div>
                            <div class="area-folder-row">
                                <div class="area-folder-name"><div class="area-folder-dot" style="background:#06b6d4"></div>Facturación</div>
                                <span class="area-folder-count" style="color:#06b6d4">6</span>
                            </div>
                            <div class="area-folders-more">+1 carpetas más</div>
                        </div>
                    </div>
                </div>

                {{-- ── SEATOOLS ── --}}
                <div class="area-card" data-acceso="completo">
                    <div class="area-card-stripe" style="background:linear-gradient(90deg,#22c55e,#16a34a)"></div>
                    <div class="area-card-body">
                        <div class="area-card-header">
                            <div style="display:flex;align-items:center;gap:12px;">
                                <div class="area-icon-wrap" style="background:rgba(34,197,94,0.1)">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="area-name">Seatools</div>
                                    <span class="area-badge" style="background:rgba(34,197,94,0.1);color:#16a34a;">
                                        <span style="width:5px;height:5px;border-radius:50%;background:#16a34a;display:inline-block;"></span>
                                        Acceso Completo
                                    </span>
                                </div>
                            </div>
                            <div class="area-card-chevron">›</div>
                        </div>
                        <p class="area-desc">Documentación de la empresa Seatools del corporativo Cardumen.</p>
                        <div class="area-stats">
                            <div class="area-stat-box" style="background:rgba(34,197,94,0.07)">
                                <div class="area-stat-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                </div>
                                <div class="area-stat-num">31</div>
                                <div class="area-stat-lbl">Archivos</div>
                            </div>
                            <div class="area-stat-box" style="background:rgba(245,158,11,0.08)">
                                <div class="area-stat-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2"><path d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"/></svg>
                                </div>
                                <div class="area-stat-num">4</div>
                                <div class="area-stat-lbl">Carpetas</div>
                            </div>
                            <div class="area-stat-box" style="background:rgba(14,165,233,0.08)">
                                <div class="area-stat-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0ea5e9" stroke-width="2"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/></svg>
                                </div>
                                <div class="area-stat-num">2.8 GB</div>
                                <div class="area-stat-lbl">Storage</div>
                            </div>
                        </div>
                        <div class="area-folders">
                            <div class="area-folder-row">
                                <div class="area-folder-name"><div class="area-folder-dot" style="background:#f59e0b"></div>Administración</div>
                                <span class="area-folder-count" style="color:#f59e0b">6</span>
                            </div>
                            <div class="area-folder-row">
                                <div class="area-folder-name"><div class="area-folder-dot" style="background:#f97316"></div>Operaciones</div>
                                <span class="area-folder-count" style="color:#f97316">12</span>
                            </div>
                            <div class="area-folder-row">
                                <div class="area-folder-name"><div class="area-folder-dot" style="background:#22c55e"></div>Facturación</div>
                                <span class="area-folder-count" style="color:#22c55e">8</span>
                            </div>
                            <div class="area-folders-more">+1 carpetas más</div>
                        </div>
                    </div>
                </div>

                {{-- ── TWS ── --}}
                <div class="area-card" data-acceso="completo">
                    <div class="area-card-stripe" style="background:linear-gradient(90deg,#f59e0b,#d97706)"></div>
                    <div class="area-card-body">
                        <div class="area-card-header">
                            <div style="display:flex;align-items:center;gap:12px;">
                                <div class="area-icon-wrap" style="background:rgba(245,158,11,0.1)">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="area-name">TWS</div>
                                    <span class="area-badge" style="background:rgba(245,158,11,0.1);color:#d97706;">
                                        <span style="width:5px;height:5px;border-radius:50%;background:#d97706;display:inline-block;"></span>
                                        Acceso Completo
                                    </span>
                                </div>
                            </div>
                            <div class="area-card-chevron">›</div>
                        </div>
                        <p class="area-desc">Documentación de la empresa TWS del corporativo Cardumen.</p>
                        <div class="area-stats">
                            <div class="area-stat-box" style="background:rgba(245,158,11,0.08)">
                                <div class="area-stat-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                </div>
                                <div class="area-stat-num">26</div>
                                <div class="area-stat-lbl">Archivos</div>
                            </div>
                            <div class="area-stat-box" style="background:rgba(245,158,11,0.08)">
                                <div class="area-stat-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2"><path d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"/></svg>
                                </div>
                                <div class="area-stat-num">4</div>
                                <div class="area-stat-lbl">Carpetas</div>
                            </div>
                            <div class="area-stat-box" style="background:rgba(14,165,233,0.08)">
                                <div class="area-stat-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0ea5e9" stroke-width="2"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/></svg>
                                </div>
                                <div class="area-stat-num">3.5 GB</div>
                                <div class="area-stat-lbl">Storage</div>
                            </div>
                        </div>
                        <div class="area-folders">
                            <div class="area-folder-row">
                                <div class="area-folder-name"><div class="area-folder-dot" style="background:#f59e0b"></div>Administración</div>
                                <span class="area-folder-count" style="color:#f59e0b">5</span>
                            </div>
                            <div class="area-folder-row">
                                <div class="area-folder-name"><div class="area-folder-dot" style="background:#f97316"></div>Operaciones</div>
                                <span class="area-folder-count" style="color:#f97316">8</span>
                            </div>
                            <div class="area-folder-row">
                                <div class="area-folder-name"><div class="area-folder-dot" style="background:#d97706"></div>Facturación</div>
                                <span class="area-folder-count" style="color:#d97706">4</span>
                            </div>
                            <div class="area-folders-more">+1 carpetas más</div>
                        </div>
                    </div>
                </div>

            </div>{{-- /areas-grid --}}
        </div>{{-- /fc-content --}}
    </div>{{-- /fc-main --}}
</div>{{-- /fc-wrapper --}}

<script>
// ── Filtros de tabs ──
document.querySelectorAll('.filter-tab').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.filter-tab').forEach(b => b.classList.remove('active'));
        this.classList.add('active');

        const filter = this.dataset.filter;
        document.querySelectorAll('.area-card').forEach(card => {
            if (filter === 'todas') {
                card.style.display = '';
            } else if (filter === 'acceso') {
                card.style.display = card.dataset.acceso === 'completo' ? '' : 'none';
            } else if (filter === 'restringidas') {
                card.style.display = card.dataset.acceso === 'restringido' ? '' : 'none';
            }
        });
    });
});

// ── Búsqueda en tiempo real ──
document.getElementById('searchAreas').addEventListener('input', function() {
    const term = this.value.toLowerCase();
    document.querySelectorAll('.area-card').forEach(card => {
        const name = card.querySelector('.area-name').textContent.toLowerCase();
        card.style.display = name.includes(term) ? '' : 'none';
    });
});
</script>
</x-app-layout>