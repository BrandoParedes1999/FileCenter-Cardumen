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
