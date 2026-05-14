<x-app-layout>
<style>
/* ══ PERMISOS GLOBALES ══════════════════════════════════════════ */

.pg-topbar {
    background: #fff; border-bottom: 1px solid #e2e8f0;
    padding: 14px 24px; display: flex; align-items: center;
    gap: 12px; flex-shrink: 0;
    box-shadow: 0 1px 3px rgba(0,0,0,.04);
}
.pg-icon {
    width: 34px; height: 34px; border-radius: 10px;
    background: rgba(99,102,241,.1);
    display: flex; align-items: center; justify-content: center;
}
.pg-title { font-size: 15px; font-weight: 700; color: #1e293b; }
.pg-sub   { font-size: 12px; color: #94a3b8; margin-top: 1px; }

/* Stats */
.pg-stats {
    display: grid; grid-template-columns: repeat(4,1fr);
    gap: 12px; padding: 18px 24px 0; flex-shrink: 0;
}
.pg-stat {
    background: #fff; border: 1px solid #e2e8f0;
    border-radius: 12px; padding: 14px 16px;
    display: flex; align-items: center; gap: 12px;
    transition: box-shadow .15s;
}
.pg-stat:hover { box-shadow: 0 4px 12px rgba(0,0,0,.06); }
.pg-stat-icon {
    width: 38px; height: 38px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.pg-stat-num  { font-size: 22px; font-weight: 700; color: #1e293b; line-height: 1; }
.pg-stat-lbl  { font-size: 11px; color: #94a3b8; margin-top: 3px; }

/* ── LEYENDA DE PERMISOS ── */
.pg-legend {
    margin: 16px 24px 0;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 14px 18px;
}
.pg-legend-title {
    font-size: 11px; font-weight: 700; color: #64748b;
    text-transform: uppercase; letter-spacing: .07em;
    margin-bottom: 10px; display: flex; align-items: center; gap: 6px;
}
.pg-legend-grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 8px;
}
.pg-legend-item {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 10px 12px;
    display: flex;
    flex-direction: column;
    gap: 4px;
}
.pg-legend-icon-row {
    display: flex; align-items: center; gap: 6px;
}
.pg-legend-badge {
    width: 28px; height: 20px; border-radius: 5px;
    font-size: 11px; font-weight: 700;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.pg-legend-badge.on  { background: rgba(5,150,105,.12); color: #059669; }
.pg-legend-badge.off { background: #f1f5f9; color: #94a3b8; border: 1px solid #e2e8f0; }
.pg-legend-name { font-size: 12px; font-weight: 600; color: #1e293b; }
.pg-legend-desc { font-size: 11px; color: #94a3b8; line-height: 1.4; }

/* Filtros */
.pg-filters {
    padding: 16px 24px 0; display: flex;
    align-items: center; gap: 8px; flex-wrap: wrap; flex-shrink: 0;
}
.pg-search {
    position: relative; flex: 1; max-width: 260px;
}
.pg-search svg {
    position: absolute; left: 10px; top: 50%; transform: translateY(-50%);
    pointer-events: none;
}
.pg-search input {
    width: 100%; background: #f8fafc; border: 1px solid #e2e8f0;
    border-radius: 8px; padding: 7px 12px 7px 32px;
    font-size: 13px; color: #1e293b; outline: none;
    transition: border-color .15s, background .15s;
}
.pg-search input:focus { background: #fff; border-color: #6366f1; }
.pg-search input::placeholder { color: #94a3b8; }

.pg-filter-pill {
    padding: 6px 14px; border-radius: 20px;
    border: 1px solid #e2e8f0; background: #fff;
    font-size: 12px; color: #475569; cursor: pointer;
    white-space: nowrap; text-decoration: none;
    transition: all .15s;
}
.pg-filter-pill:hover    { border-color: #c7d2fe; background: #f5f3ff; color: #4f46e5; }
.pg-filter-pill.active   { background: #4f46e5; color: #fff; border-color: #4f46e5; font-weight: 600; }
.pg-filter-pill.active:hover { background: #4338ca; }

.pg-select {
    padding: 6px 10px; border-radius: 8px;
    border: 1px solid #e2e8f0; background: #fff;
    font-size: 12px; color: #475569; outline: none;
    cursor: pointer;
}
.pg-select:focus { border-color: #6366f1; }

/* Tabla principal */
.pg-content {
    flex: 1; overflow-y: auto; padding: 18px 24px 24px;
    scrollbar-width: thin; scrollbar-color: #cbd5e1 transparent;
}

/* ── GRUPO POR CARPETA ── */
.pg-group { margin-bottom: 24px; }
.pg-group-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 1px 4px rgba(0,0,0,.04);
}
.pg-group-header {
    display: flex; align-items: center; gap: 10px;
    padding: 14px 18px;
    border-bottom: 1px solid #f1f5f9;
    background: #fafbfc;
}
.pg-group-icon {
    width: 36px; height: 36px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.pg-group-info { flex: 1; min-width: 0; }
.pg-group-name { font-size: 14px; font-weight: 700; color: #1e293b; }
.pg-group-path { font-size: 11px; color: #94a3b8; font-family: monospace; margin-top: 2px; }
.pg-group-meta { display: flex; align-items: center; gap: 6px; flex-shrink: 0; }
.pg-group-count {
    font-size: 11px; font-weight: 600;
    background: #f1f5f9; color: #64748b;
    padding: 3px 10px; border-radius: 20px;
}
.pg-folder-link {
    text-decoration: none; color: inherit;
    transition: color .15s;
}
.pg-folder-link:hover .pg-group-name { color: #6366f1; }
.pg-goto-btn {
    display: flex; align-items: center; gap: 5px;
    font-size: 11px; color: #6366f1; text-decoration: none;
    padding: 5px 10px; border-radius: 6px;
    border: 1px solid rgba(99,102,241,.2);
    background: rgba(99,102,241,.05);
    white-space: nowrap; transition: all .15s;
}
.pg-goto-btn:hover { background: rgba(99,102,241,.12); border-color: #6366f1; }

/* ── TABLA DE PERMISOS ── */
.pg-table-wrap { overflow-x: auto; }
.pg-table {
    width: 100%; border-collapse: collapse; min-width: 680px;
}
.pg-table thead th {
    padding: 8px 14px;
    font-size: 10px; font-weight: 700; color: #94a3b8;
    text-transform: uppercase; letter-spacing: .07em;
    text-align: center;
    border-bottom: 1px solid #f1f5f9;
    background: #fafbfc;
}
.pg-table thead th.col-who { text-align: left; padding-left: 18px; }
.pg-table tbody tr {
    border-bottom: 1px solid #f8fafc;
    transition: background .1s;
}
.pg-table tbody tr:last-child { border-bottom: none; }
.pg-table tbody tr:hover { background: #fafbff; }
.pg-table tbody tr.tipo-rol { background: rgba(99,102,241,.02); }
.pg-table tbody tr.tipo-rol:hover { background: rgba(99,102,241,.05); }

.pg-table td { padding: 10px 14px; vertical-align: middle; }
.pg-table td.col-who { padding-left: 18px; }

/* Celda de usuario/rol */
.pg-who {
    display: flex; align-items: center; gap: 10px;
}
.pg-avatar {
    width: 34px; height: 34px; border-radius: 50%;
    color: #fff; font-size: 11px; font-weight: 700;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.pg-avatar.rol-icon {
    border-radius: 9px;
    background: rgba(124,58,237,.1);
}
.pg-who-name { font-size: 13px; font-weight: 600; color: #1e293b; }
.pg-who-sub  { font-size: 11px; color: #94a3b8; margin-top: 2px; }

/* Celda de permiso individual */
.pg-perm-cell { text-align: center; }
.pg-perm-toggle {
    display: inline-flex; flex-direction: column; align-items: center; gap: 2px;
}
.pg-cap-btn {
    width: 34px; height: 26px; border-radius: 6px;
    border: none; cursor: pointer; font-size: 12px; font-weight: 700;
    transition: all .15s; display: flex; align-items: center; justify-content: center;
    margin: 0 auto;
}
.pg-cap-btn.on  { background: rgba(5,150,105,.12); color: #059669; }
.pg-cap-btn.on:hover  { background: rgba(5,150,105,.25); }
.pg-cap-btn.off { background: #f8fafc; color: #cbd5e1; border: 1px solid #e2e8f0; }
.pg-cap-btn.off:hover { background: #f1f5f9; color: #94a3b8; }

/* Badge de rol */
.pg-badge {
    font-size: 10px; font-weight: 600; padding: 3px 8px;
    border-radius: 20px; white-space: nowrap;
}
.pg-badge-super { background: rgba(245,158,11,.1);  color: #d97706; }
.pg-badge-admin { background: rgba(79,70,229,.1);   color: #4f46e5; }
.pg-badge-rol   { background: rgba(124,58,237,.1);  color: #7c3aed; }
.pg-badge-corp  { background: rgba(27,58,107,.1);   color: #1b3a6b; }
.pg-badge-mode  { background: rgba(99,102,241,.08); color: #4f46e5; font-size: 10px; padding: 3px 8px; border-radius: 20px; }

/* Acciones por fila */
.pg-row-actions { display: flex; gap: 4px; justify-content: center; }
.pg-act-btn {
    width: 30px; height: 30px; border-radius: 7px;
    border: 1px solid #e2e8f0; background: #f8fafc;
    color: #94a3b8; cursor: pointer; display: flex;
    align-items: center; justify-content: center;
    transition: all .15s;
}
.pg-act-btn:hover { background: #ede9fe; border-color: #c4b5fd; color: #4f46e5; }
.pg-act-btn.danger:hover { background: #fee2e2; border-color: #fca5a5; color: #dc2626; }

/* Estado vacío */
.pg-empty {
    text-align: center; padding: 60px 20px; color: #94a3b8;
}
.pg-empty-icon {
    width: 64px; height: 64px; border-radius: 16px;
    background: rgba(99,102,241,.08);
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 16px;
}
.pg-empty-title { font-size: 15px; font-weight: 600; color: #1e293b; margin-bottom: 5px; }
.pg-empty-sub   { font-size: 13px; max-width: 280px; margin: 0 auto 20px; }

/* Modal edición completa */
.pg-modal-overlay {
    display: none; position: fixed; inset: 0;
    background: rgba(15,23,42,.5); z-index: 200;
    align-items: center; justify-content: center;
    backdrop-filter: blur(4px);
}
.pg-modal-overlay.open { display: flex; }
.pg-modal {
    background: #fff; border-radius: 18px;
    padding: 28px; width: 480px; max-width: 92vw;
    box-shadow: 0 24px 60px rgba(0,0,0,.18);
}
.pg-modal-title { font-size: 16px; font-weight: 700; color: #1e293b; margin-bottom: 4px; }
.pg-modal-sub   { font-size: 13px; color: #64748b; margin-bottom: 20px; }

.pg-modal-caps {
    border: 1px solid #e2e8f0; border-radius: 12px;
    overflow: hidden; margin-bottom: 20px;
}
.pg-modal-cap-row {
    display: grid; grid-template-columns: 1fr auto;
}
.pg-modal-cap-row > div {
    padding: 11px 16px;
    border-bottom: 1px solid #f1f5f9;
    display: flex; align-items: center;
}
.pg-modal-cap-row:last-child > div { border-bottom: none; }
.pg-modal-cap-name {
    font-size: 13px; font-weight: 600; color: #1e293b; gap: 8px;
    display: flex; align-items: center;
}
.pg-modal-cap-name .cap-icon {
    width: 28px; height: 28px; border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 14px; flex-shrink: 0;
}
.pg-modal-cap-name span { font-size: 11px; color: #94a3b8; font-weight: 400; margin-left: 4px; }
.pg-modal-cap-toggle { justify-content: flex-end; background: #f8fafc; }
.pg-modal-cap-toggle input { width: 18px; height: 18px; accent-color: #6366f1; cursor: pointer; }
.pg-modal-btns { display: flex; gap: 10px; justify-content: flex-end; }

/* Flash */
.pg-flash {
    margin: 0 24px 14px; padding: 12px 16px;
    border-radius: 10px; font-size: 13px; font-weight: 500;
    display: flex; align-items: center; gap: 9px; flex-shrink: 0;
}
.pg-flash.success {
    background: rgba(5,150,105,.08); border: 1px solid rgba(5,150,105,.25); color: #065f46;
}
.pg-flash.error {
    background: rgba(220,38,38,.08); border: 1px solid rgba(220,38,38,.2); color: #991b1b;
}

/* Dark mode */
.dark .pg-topbar         { background: #13111f; border-color: #1e1b4b; }
.dark .pg-title          { color: #e0e7ff; }
.dark .pg-stat           { background: #13111f; border-color: #1e1b4b; }
.dark .pg-stat-num       { color: #e0e7ff; }
.dark .pg-legend         { background: #1a1830; border-color: #1e1b4b; }
.dark .pg-legend-item    { background: #13111f; border-color: #1e1b4b; }
.dark .pg-legend-name    { color: #e0e7ff; }
.dark .pg-filter-pill    { background: #13111f; border-color: #1e1b4b; color: #a5b4fc; }
.dark .pg-filter-pill:hover { background: #1e1b4b; }
.dark .pg-search input   { background: #1e1b4b; border-color: #2d2a5e; color: #e0e7ff; }
.dark .pg-group-card     { background: #13111f; border-color: #1e1b4b; }
.dark .pg-group-header   { background: #1a1830; border-color: #1e1b4b; }
.dark .pg-group-name     { color: #e0e7ff; }
.dark .pg-group-count    { background: #1e1b4b; color: #a5b4fc; }
.dark .pg-table thead th { background: #1a1830; border-color: #1e1b4b; }
.dark .pg-table tbody tr { border-color: #1e1b4b; }
.dark .pg-table tbody tr:hover { background: #1a1b30; }
.dark .pg-who-name       { color: #e0e7ff; }
.dark .pg-cap-btn.off    { background: #1e1b4b; border-color: #2d2a5e; color: #4f46e5; }
.dark .pg-act-btn        { background: #1e1b4b; border-color: #2d2a5e; color: #a5b4fc; }
.dark .pg-modal          { background: #13111f; }
.dark .pg-modal-title    { color: #e0e7ff; }
.dark .pg-modal-sub      { color: #a5b4fc; }
.dark .pg-modal-caps     { border-color: #1e1b4b; }
.dark .pg-modal-cap-row > div { border-color: #1e1b4b; }
.dark .pg-modal-cap-name { color: #e0e7ff; }
.dark .pg-modal-cap-toggle { background: #1e1b4b; }
.dark .pg-select         { background: #1e1b4b; border-color: #2d2a5e; color: #a5b4fc; }
.dark .pg-empty-title    { color: #e0e7ff; }

@media (max-width: 900px) {
    .pg-stats { grid-template-columns: repeat(2,1fr); }
    .pg-legend-grid { grid-template-columns: repeat(3,1fr); }
}
@media (max-width: 600px) {
    .pg-legend-grid { grid-template-columns: repeat(2,1fr); }
}
</style>

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

{{-- ══ Modal: edición completa de permisos ══════════════════════ --}}
<div class="pg-modal-overlay" id="modalEditar">
    <div class="pg-modal">
        <div class="pg-modal-title">Editar permisos</div>
        <div class="pg-modal-sub" id="modalEditarSub">—</div>

        <form id="formEditar" method="POST">
            @csrf @method('PUT')

            <div class="pg-modal-caps">
                @php
                    $modalCaps = [
                        'puede_leer'      => ['👁',  'Leer',      'Ver la carpeta y su lista de archivos'],
                        'puede_descargar' => ['⬇',  'Descargar', 'Descargar archivos al dispositivo'],
                        'puede_subir'     => ['⬆',  'Subir',     'Agregar nuevos archivos a la carpeta'],
                        'puede_editar'    => ['✏',  'Editar',    'Modificar descripción y metadatos'],
                        'puede_borrar'    => ['🗑', 'Borrar',    'Eliminar archivos de la carpeta'],
                        'heredar'         => ['↳',  'Heredar',   'Extender permisos a todas las subcarpetas'],
                    ];
                @endphp
                @foreach($modalCaps as $campo => [$icon, $label, $desc])
                <div class="pg-modal-cap-row">
                    <div class="pg-modal-cap-name">
                        <div class="cap-icon" style="background:#f1f5f9">{{ $icon }}</div>
                        {{ $label }}
                        <span>— {{ $desc }}</span>
                    </div>
                    <div class="pg-modal-cap-toggle">
                        <input type="hidden" name="{{ $campo }}" value="0">
                        <input type="checkbox" id="modal_{{ $campo }}" name="{{ $campo }}" value="1">
                    </div>
                </div>
                @endforeach
            </div>

            <div class="pg-modal-btns">
                <button type="button" class="fc-btn fc-btn-outline"
                        onclick="document.getElementById('modalEditar').classList.remove('open')">
                    Cancelar
                </button>
                <button type="submit" class="fc-btn fc-btn-primary">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V7l-4-4zm-5 16c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm3-10H5V5h10v4z"/>
                    </svg>
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ══ Modal: confirmar revocar ═══════════════════════════════════ --}}
<div class="fc-modal-overlay" id="modalRevocar">
    <div class="fc-modal">
        <div class="fc-modal-title">¿Revocar este permiso?</div>
        <div class="fc-modal-sub" id="modalRevocarSub">—</div>
        <div class="fc-modal-btns">
            <button class="fc-modal-cancel"
                    onclick="document.getElementById('modalRevocar').classList.remove('open')">Cancelar</button>
            <form id="formRevocar" method="POST">
                @csrf @method('DELETE')
                <button type="submit" class="fc-modal-confirm danger">Revocar</button>
            </form>
        </div>
    </div>
</div>

<script>
function abrirModalEditar(id, nombre, carpeta, leer, subir, editar, borrar, descargar, heredar) {
    document.getElementById('formEditar').action = '{{ url("/permisos-globales") }}/' + id;
    document.getElementById('modalEditarSub').textContent =
        nombre + '  ·  Carpeta: ' + carpeta;

    const vals = {
        puede_leer: leer, puede_descargar: descargar,
        puede_subir: subir, puede_editar: editar,
        puede_borrar: borrar, heredar: heredar,
    };
    Object.entries(vals).forEach(([k, v]) => {
        const el = document.getElementById('modal_' + k);
        if (el) el.checked = v;
    });

    document.getElementById('modalEditar').classList.add('open');
}

function confirmarRevocar(id, nombre, carpeta) {
    document.getElementById('formRevocar').action = '{{ url("/permisos-globales") }}/' + id;
    document.getElementById('modalRevocarSub').innerHTML =
        'Se revocará el acceso de <strong>' + nombre + '</strong> a la carpeta <strong>' + carpeta + '</strong>.';
    document.getElementById('modalRevocar').classList.add('open');
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        document.querySelectorAll('.pg-modal-overlay.open, .fc-modal-overlay.open')
                .forEach(m => m.classList.remove('open'));
    }
});
</script>
</x-app-layout>
