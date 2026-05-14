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
