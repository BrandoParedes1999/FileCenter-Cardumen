<x-app-layout>
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

.fc-wrapper {
    display: flex; height: 100dvh; width: 100%;
    background: #f8fafc; color: #1e293b;
    font-family: 'Segoe UI', system-ui, sans-serif;
    font-size: 15px; overflow: hidden;
}
.fc-main { flex: 1; display: flex; flex-direction: column; height: 100dvh; overflow: hidden; min-width: 0; }

/* ── Topbar ── */
.fc-topbar {
    height: 58px; background: #fff;
    border-bottom: 1px solid #e2e8f0;
    display: flex; align-items: center;
    padding: 0 24px; gap: 14px; flex-shrink: 0;
}
.fc-topbar-title { font-size: 15px; font-weight: 700; color: #1e293b; }
.fc-topbar-right { display: flex; align-items: center; gap: 16px; margin-left: auto; }
.fc-topbar-avatar {
    width: 36px; height: 36px;
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
    border-radius: 50%; display: flex; align-items: center;
    justify-content: center; font-size: 12px; font-weight: 700; color: #fff;
}
.fc-topbar-name { font-size: 13px; font-weight: 600; color: #0f172a; }
.fc-topbar-role { font-size: 11px; color: #7c3aed; }

/* ── Barra de acciones ── */
.fc-actionbar {
    background: #fff; border-bottom: 1px solid #e2e8f0;
    padding: 12px 24px; display: flex; align-items: center;
    gap: 10px; flex-shrink: 0; flex-wrap: wrap;
}
.fc-search-wrap { position: relative; flex: 1; max-width: 320px; }
.fc-search-wrap svg { position: absolute; left: 11px; top: 50%; transform: translateY(-50%); pointer-events: none; }
.fc-search-wrap input {
    width: 100%; background: #f1f5f9; border: 1px solid #e2e8f0;
    border-radius: 9px; padding: 8px 14px 8px 36px;
    color: #1e293b; font-size: 13px; outline: none;
    transition: border-color .2s, background .2s;
}
.fc-search-wrap input:focus { background: #fff; border-color: #6366f1; }
.fc-search-wrap input::placeholder { color: #94a3b8; }

.fc-btn-new {
    display: flex; align-items: center; gap: 7px;
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
    color: #fff; border: none; border-radius: 9px;
    padding: 9px 16px; font-size: 13px; font-weight: 600;
    cursor: pointer; white-space: nowrap; text-decoration: none;
    transition: opacity .15s, transform .1s;
    box-shadow: 0 4px 12px rgba(79,70,229,0.3);
}
.fc-btn-new:hover { opacity: .9; transform: translateY(-1px); }

.fc-btn-upload {
    display: flex; align-items: center; gap: 7px;
    background: linear-gradient(135deg, #059669, #10b981);
    color: #fff; border: none; border-radius: 9px;
    padding: 9px 16px; font-size: 13px; font-weight: 600;
    cursor: pointer; white-space: nowrap; text-decoration: none;
    transition: opacity .15s, transform .1s;
    box-shadow: 0 4px 12px rgba(5,150,105,0.3);
}
.fc-btn-upload:hover { opacity: .9; transform: translateY(-1px); }

.fc-btn-outline {
    display: flex; align-items: center; gap: 7px;
    background: #fff; border: 1px solid #e2e8f0;
    border-radius: 9px; padding: 8px 14px;
    font-size: 13px; color: #475569; cursor: pointer;
    white-space: nowrap; text-decoration: none;
    transition: border-color .15s, background .15s;
}
.fc-btn-outline:hover { border-color: #c7d2fe; background: #f5f3ff; color: #4f46e5; }

.fc-view-btns { display: flex; gap: 2px; background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 9px; padding: 3px; margin-left: auto; }
.fc-view-btn {
    width: 30px; height: 30px; border-radius: 7px; border: none;
    background: transparent; cursor: pointer; display: flex;
    align-items: center; justify-content: center; color: #94a3b8;
    transition: background .15s, color .15s;
}
.fc-view-btn.active { background: #fff; color: #4f46e5; box-shadow: 0 1px 4px rgba(0,0,0,0.08); }

/* ── Contenido ── */
.fc-content {
    flex: 1; overflow-y: auto; padding: 24px;
    scrollbar-width: thin; scrollbar-color: #cbd5e1 transparent;
}

/* ── Breadcrumb ── */
.fc-breadcrumb {
    display: flex; align-items: center; gap: 6px;
    margin-bottom: 20px; font-size: 13px; flex-wrap: wrap;
}
.fc-bread-item { color: #6366f1; font-weight: 500; text-decoration: none; }
.fc-bread-item:hover { text-decoration: underline; }
.fc-bread-sep { color: #cbd5e1; font-size: 16px; }
.fc-bread-current { color: #475569; font-weight: 600; }

/* ── Info header de carpeta ── */
.fc-folder-header {
    background: #fff; border: 1px solid #e2e8f0;
    border-radius: 14px; padding: 20px 24px;
    margin-bottom: 22px; display: flex; align-items: center;
    gap: 18px; box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}
.fc-folder-header-icon {
    width: 52px; height: 52px; border-radius: 14px;
    background: rgba(79,70,229,0.1);
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.fc-folder-header-name { font-size: 18px; font-weight: 700; color: #1e293b; }
.fc-folder-header-path { font-size: 12px; color: #94a3b8; margin-top: 3px; font-family: monospace; }
.fc-folder-header-badges { display: flex; gap: 8px; margin-top: 8px; }
.fc-badge {
    font-size: 11px; font-weight: 600; padding: 3px 10px;
    border-radius: 20px; letter-spacing: .04em;
}
.fc-badge-public { background: rgba(5,150,105,0.1); color: #059669; }
.fc-badge-private { background: rgba(100,116,139,0.1); color: #475569; }
.fc-folder-header-actions { margin-left: auto; display: flex; gap: 8px; }

/* ── Sección de subcarpetas ── */
.fc-section-title {
    font-size: 11px; font-weight: 700; color: #94a3b8;
    text-transform: uppercase; letter-spacing: .12em;
    margin-bottom: 12px; margin-top: 4px;
    display: flex; align-items: center; gap: 10px;
}
.fc-section-title::after { content: ''; flex: 1; height: 1px; background: #e2e8f0; }
.fc-section-count {
    font-size: 10px; background: #f1f5f9; color: #64748b;
    padding: 2px 8px; border-radius: 20px; font-weight: 600;
}

/* ── Grid subcarpetas ── */
.fc-folders-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 12px; margin-bottom: 28px;
}
.fc-folder-card {
    background: #fff; border: 1px solid #e2e8f0;
    border-radius: 14px; padding: 16px 14px;
    cursor: pointer; text-decoration: none; display: block;
    transition: border-color .2s, transform .2s, box-shadow .2s;
    position: relative; overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}
.fc-folder-card:hover {
    border-color: #c7d2fe; transform: translateY(-3px);
    box-shadow: 0 10px 28px rgba(79,70,229,0.12);
}
.fc-folder-card::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    border-radius: 14px 14px 0 0;
    background: linear-gradient(90deg, #4f46e5, #7c3aed);
    opacity: 0; transition: opacity .2s;
}
.fc-folder-card:hover::before { opacity: 1; }
.fc-folder-icon {
    width: 40px; height: 40px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 10px; background: rgba(79,70,229,0.08);
}
.fc-folder-name {
    font-size: 13px; font-weight: 600; color: #1e293b;
    margin-bottom: 3px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.fc-folder-meta { font-size: 11px; color: #94a3b8; }
.fc-folder-public {
    position: absolute; top: 10px; right: 10px;
    font-size: 10px; font-weight: 600; color: #059669;
    background: rgba(5,150,105,0.1); padding: 2px 7px;
    border-radius: 20px;
}

/* ── Grid archivos ── */
.fc-files-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 12px; margin-bottom: 28px;
}
.fc-file-card {
    background: #fff; border: 1px solid #e2e8f0;
    border-radius: 14px; overflow: hidden;
    transition: border-color .2s, transform .2s, box-shadow .2s;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}
.fc-file-card:hover {
    border-color: #c7d2fe; transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(79,70,229,0.1);
}
.fc-file-preview {
    height: 90px; display: flex; align-items: center; justify-content: center;
    border-bottom: 1px solid #f1f5f9;
    background: linear-gradient(135deg, #f8fafc, #f1f5f9);
}
.fc-file-body { padding: 12px 14px; }
.fc-file-name {
    font-size: 12px; font-weight: 600; color: #1e293b;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    margin-bottom: 4px;
}
.fc-file-meta { font-size: 11px; color: #94a3b8; margin-bottom: 10px; }
.fc-file-actions { display: flex; gap: 6px; }
.fc-file-btn {
    flex: 1; padding: 6px; border-radius: 7px; border: 1px solid #e2e8f0;
    background: #f8fafc; font-size: 11px; font-weight: 600;
    cursor: pointer; text-align: center; text-decoration: none;
    color: #475569; transition: all .15s; display: flex;
    align-items: center; justify-content: center; gap: 4px;
}
.fc-file-btn:hover { background: #ede9fe; border-color: #c4b5fd; color: #4f46e5; }
.fc-file-btn.download:hover { background: #dcfce7; border-color: #86efac; color: #059669; }
.fc-file-btn.danger:hover { background: #fee2e2; border-color: #fca5a5; color: #dc2626; }

/* ── Lista archivos ── */
.fc-files-list { display: flex; flex-direction: column; gap: 6px; margin-bottom: 28px; }
.fc-file-row {
    background: #fff; border: 1px solid #e2e8f0;
    border-radius: 10px; padding: 12px 16px;
    display: flex; align-items: center; gap: 14px;
    transition: border-color .15s, box-shadow .15s;
}
.fc-file-row:hover { border-color: #c7d2fe; box-shadow: 0 4px 12px rgba(79,70,229,0.08); }
.fc-file-row-icon {
    width: 36px; height: 36px; border-radius: 9px;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.fc-file-row-name { font-size: 13px; font-weight: 600; color: #1e293b; flex: 1;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 300px; }
.fc-file-row-meta { font-size: 12px; color: #94a3b8; margin-right: 16px; white-space: nowrap; }
.fc-file-row-ver {
    font-size: 10px; font-weight: 700; background: #f1f5f9;
    color: #64748b; padding: 2px 8px; border-radius: 20px; white-space: nowrap;
}
.fc-file-row-actions { display: flex; gap: 6px; }
.fc-file-row-btn {
    width: 30px; height: 30px; border-radius: 7px; border: 1px solid #e2e8f0;
    background: #f8fafc; cursor: pointer; display: flex;
    align-items: center; justify-content: center; color: #94a3b8;
    text-decoration: none; transition: all .15s;
}
.fc-file-row-btn:hover { background: #ede9fe; border-color: #c4b5fd; color: #4f46e5; }
.fc-file-row-btn.dl:hover { background: #dcfce7; border-color: #86efac; color: #059669; }
.fc-file-row-btn.del:hover { background: #fee2e2; border-color: #fca5a5; color: #dc2626; }

/* ── Icono por tipo ── */
.ext-pdf  { background: rgba(220,38,38,0.08); }
.ext-word { background: rgba(37,99,235,0.08); }
.ext-excel{ background: rgba(5,150,105,0.08); }
.ext-ppt  { background: rgba(234,88,12,0.08); }
.ext-img  { background: rgba(6,182,212,0.08); }
.ext-zip  { background: rgba(245,158,11,0.08); }
.ext-def  { background: rgba(100,116,139,0.08); }

/* ── Estado vacío ── */
.fc-empty {
    display: flex; flex-direction: column; align-items: center;
    justify-content: center; padding: 60px 20px; text-align: center;
}
.fc-empty-icon {
    width: 72px; height: 72px; border-radius: 18px;
    background: rgba(79,70,229,0.08);
    display: flex; align-items: center; justify-content: center; margin-bottom: 16px;
}
.fc-empty-title { font-size: 15px; font-weight: 600; color: #1e293b; margin-bottom: 5px; }
.fc-empty-sub { font-size: 13px; color: #94a3b8; margin-bottom: 20px; max-width: 260px; }

/* ── Flash ── */
.fc-flash {
    margin-bottom: 18px; padding: 12px 16px; border-radius: 10px;
    font-size: 13px; font-weight: 500; display: flex; align-items: center; gap: 9px;
}
.fc-flash.success { background: rgba(5,150,105,0.08); border: 1px solid rgba(5,150,105,0.25); color: #065f46; }
.fc-flash.error   { background: rgba(220,38,38,0.08); border: 1px solid rgba(220,38,38,0.2); color: #991b1b; }

/* ── Modal eliminar ── */
.fc-modal-overlay {
    display: none; position: fixed; inset: 0;
    background: rgba(15,23,42,0.5); z-index: 200;
    align-items: center; justify-content: center;
    backdrop-filter: blur(4px);
}
.fc-modal-overlay.open { display: flex; }
.fc-modal {
    background: #fff; border-radius: 16px; padding: 28px;
    width: 400px; max-width: 92vw;
    box-shadow: 0 24px 64px rgba(0,0,0,0.18);
}
.fc-modal-title { font-size: 16px; font-weight: 700; color: #1e293b; margin-bottom: 8px; }
.fc-modal-sub { font-size: 13px; color: #64748b; margin-bottom: 22px; line-height: 1.6; }
.fc-modal-btns { display: flex; gap: 10px; justify-content: flex-end; }
.fc-modal-cancel {
    padding: 9px 18px; border-radius: 9px; border: 1px solid #e2e8f0;
    background: #f8fafc; color: #475569; font-size: 13px; cursor: pointer;
}
.fc-modal-cancel:hover { background: #f1f5f9; }
.fc-modal-confirm {
    padding: 9px 18px; border-radius: 9px; border: none;
    background: #dc2626; color: #fff; font-size: 13px; font-weight: 600; cursor: pointer;
}
.fc-modal-confirm:hover { background: #b91c1c; }
</style>

<div class="fc-wrapper">
    @include('components.sidebar')

    <div class="fc-main">

        {{-- Topbar --}}
        <header class="fc-topbar">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#6366f1">
                <path d="M20 6h-8l-2-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2z"/>
            </svg>
            <span class="fc-topbar-title">{{ $carpeta->nombre }}</span>
            <div class="fc-topbar-right">
                <div class="fc-topbar-avatar">
                    {{ strtoupper(substr(Auth::user()->nombre, 0, 1)) }}{{ strtoupper(substr(Auth::user()->paterno, 0, 1)) }}
                </div>
                <div>
                    <div class="fc-topbar-name">{{ Auth::user()->nombre_completo }}</div>
                    <div class="fc-topbar-role">{{ Auth::user()->rol }}</div>
                </div>
            </div>
        </header>

        {{-- Barra de acciones --}}
        <div class="fc-actionbar">
            <div class="fc-search-wrap">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round">
                    <circle cx="11" cy="11" r="6"/><path d="m21 21-4.35-4.35"/>
                </svg>
                <input type="text" placeholder="Buscar en esta carpeta..." oninput="filtrar(this.value)" />
            </div>

            @can('create', App\Models\Carpeta::class)
            <a href="{{ route('carpetas.create', ['padre_id' => $carpeta->id]) }}" class="fc-btn-new">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
                Subcarpeta
            </a>
            @endcan

            @can('create', App\Models\Archivo::class)
            <a href="{{ route('archivos.create', ['carpeta_id' => $carpeta->id]) }}" class="fc-btn-upload">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16h6v-6h4l-7-7-7 7h4zm-4 2h14v2H5z"/></svg>
                Subir archivo
            </a>
            @endcan

            @can('update', $carpeta)
            <a href="{{ route('carpetas.edit', $carpeta) }}" class="fc-btn-outline">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
                Editar
            </a>
            @endcan

            @if(in_array(Auth::user()->rol, ['Superadmin', 'Aux_QHSE', 'Admin', 'Gerente']))
            <a href="{{ route('permisos.index', $carpeta) }}" class="fc-btn-outline">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/></svg>
                Permisos
            </a>
            @endif

            <div class="fc-view-btns">
                <button class="fc-view-btn active" id="btnGrid" onclick="setView('grid')" title="Cuadrícula">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M3 3h8v8H3zm10 0h8v8h-8zM3 13h8v8H3zm10 0h8v8h-8z"/></svg>
                </button>
                <button class="fc-view-btn" id="btnList" onclick="setView('list')" title="Lista">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M3 13h2v-2H3v2zm0 4h2v-2H3v2zm0-8h2V7H3v2zm4 4h14v-2H7v2zm0 4h14v-2H7v2zM7 7v2h14V7H7z"/></svg>
                </button>
            </div>
        </div>

        {{-- Contenido --}}
        <div class="fc-content">

            {{-- Flash --}}
            @if(session('success'))
            <div class="fc-flash success">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="#059669"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                {{ session('success') }}
            </div>
            @endif
            @if($errors->any())
            <div class="fc-flash error">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="#dc2626"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                {{ $errors->first('error') }}
            </div>
            @endif

            {{-- Breadcrumb --}}
            <div class="fc-breadcrumb">
                <a href="{{ route('carpetas.index') }}" class="fc-bread-item">📁 Inicio</a>
                @foreach($migas as $i => $miga)
                    <span class="fc-bread-sep">›</span>
                    @if($i < count($migas) - 1)
                        <a href="{{ route('carpetas.show', $miga['id']) }}" class="fc-bread-item">{{ $miga['nombre'] }}</a>
                    @else
                        <span class="fc-bread-current">{{ $miga['nombre'] }}</span>
                    @endif
                @endforeach
            </div>

            {{-- Header carpeta --}}
            <div class="fc-folder-header">
                <div class="fc-folder-header-icon">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="#4f46e5">
                        <path d="M20 6h-8l-2-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2z"/>
                    </svg>
                </div>
                <div style="flex:1;min-width:0">
                    <div class="fc-folder-header-name">{{ $carpeta->nombre }}</div>
                    <div class="fc-folder-header-path">{{ $carpeta->path }}</div>
                    <div class="fc-folder-header-badges">
                        <span class="fc-badge {{ $carpeta->es_publico ? 'fc-badge-public' : 'fc-badge-private' }}">
                            {{ $carpeta->es_publico ? '🌐 Pública' : '🔒 Privada' }}
                        </span>
                        <span class="fc-badge" style="background:#f1f5f9;color:#64748b">
                            {{ $carpeta->hijos->count() }} subcarpetas
                        </span>
                        <span class="fc-badge" style="background:#f1f5f9;color:#64748b">
                            {{ $archivos->count() }} archivos
                        </span>
                    </div>
                </div>
                @can('delete', $carpeta)
                <div class="fc-folder-header-actions">
                    <button onclick="confirmarEliminarCarpeta()" class="fc-btn-outline" style="color:#dc2626;border-color:#fca5a5;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
                        Eliminar
                    </button>
                </div>
                @endcan
            </div>

            {{-- ── SUBCARPETAS ── --}}
            @if($carpeta->hijos->count() > 0)
            <div class="fc-section-title">
                Subcarpetas
                <span class="fc-section-count">{{ $carpeta->hijos->count() }}</span>
            </div>

            <div class="fc-folders-grid" id="subGrid">
                @foreach($carpeta->hijos as $hijo)
                <a href="{{ route('carpetas.show', $hijo) }}" class="fc-folder-card" data-nombre="{{ $hijo->nombre }}">
                    @if($hijo->es_publico)<span class="fc-folder-public">Pública</span>@endif
                    <div class="fc-folder-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="#4f46e5">
                            <path d="M20 6h-8l-2-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2z"/>
                        </svg>
                    </div>
                    <div class="fc-folder-name">{{ $hijo->nombre }}</div>
                    <div class="fc-folder-meta">{{ $hijo->archivos()->where('esta_eliminado', false)->count() }} archivos</div>
                </a>
                @endforeach
            </div>
            @endif

            {{-- ── ARCHIVOS ── --}}
            <div class="fc-section-title">
                Archivos
                <span class="fc-section-count">{{ $archivos->count() }}</span>
            </div>

            @if($archivos->count() === 0)
            <div class="fc-empty">
                <div class="fc-empty-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="#a5b4fc">
                        <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6z"/>
                    </svg>
                </div>
                <div class="fc-empty-title">Sin archivos aún</div>
                <div class="fc-empty-sub">Sube el primer archivo a esta carpeta.</div>
                @can('create', App\Models\Archivo::class)
                <a href="{{ route('archivos.create', ['carpeta_id' => $carpeta->id]) }}" class="fc-btn-upload">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16h6v-6h4l-7-7-7 7h4zm-4 2h14v2H5z"/></svg>
                    Subir archivo
                </a>
                @endcan
            </div>
            @else

            {{-- Vista grid archivos --}}
            <div class="fc-files-grid" id="filesGrid">
                @foreach($archivos as $archivo)
                @php
                    $extClases = [
                        'pdf'  => ['ext-pdf',   '#dc2626', 'M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6z'],
                        'doc'  => ['ext-word',  '#2563eb', 'M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6z'],
                        'docx' => ['ext-word',  '#2563eb', 'M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6z'],
                        'xls'  => ['ext-excel', '#059669', 'M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6z'],
                        'xlsx' => ['ext-excel', '#059669', 'M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6z'],
                        'ppt'  => ['ext-ppt',   '#ea580c', 'M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6z'],
                        'pptx' => ['ext-ppt',   '#ea580c', 'M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6z'],
                    ];
                    $ext = strtolower($archivo->extension);
                    $info = $extClases[$ext] ?? ['ext-def', '#64748b', 'M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6z'];
                @endphp
                <div class="fc-file-card" data-nombre="{{ $archivo->nombre_original }}">
                    <div class="fc-file-preview">
                        @php $hx = ltrim($info[1], '#'); @endphp
                        <div style="width:52px;height:52px;border-radius:12px;background:rgba({{ hexdec(substr($hx,0,2)) }},{{ hexdec(substr($hx,2,2)) }},{{ hexdec(substr($hx,4,2)) }},0.1);display:flex;align-items:center;justify-content:center;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="{{ $info[1] }}">
                                <path d="{{ $info[2] }}"/>
                            </svg>
                        </div>
                        <span style="position:absolute;top:8px;right:10px;font-size:10px;font-weight:700;background:#f1f5f9;color:#64748b;padding:2px 7px;border-radius:10px;text-transform:uppercase;">
                            {{ strtoupper($ext) }}
                        </span>
                    </div>
                    <div class="fc-file-body">
                        <div class="fc-file-name" title="{{ $archivo->nombre_original }}">{{ $archivo->nombre_original }}</div>
                        <div class="fc-file-meta">{{ $archivo->tamanioFormateado() }} · v{{ $archivo->version }}</div>
                        <div class="fc-file-actions">
                            <a href="{{ route('archivos.show', $archivo) }}" class="fc-file-btn">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                                Ver
                            </a>
                            @can('download', $archivo)
                            <a href="{{ route('archivos.descargar', $archivo) }}" class="fc-file-btn download">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor"><path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/></svg>
                                Bajar
                            </a>
                            @endcan
                            @can('delete', $archivo)
                            <button onclick="confirmarEliminar({{ $archivo->id }}, '{{ addslashes($archivo->nombre_original) }}')" class="fc-file-btn danger">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
                            </button>
                            @endcan
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Vista lista archivos --}}
            <div class="fc-files-list" id="filesList" style="display:none;">
                @foreach($archivos as $archivo)
                @php
                    $ext = strtolower($archivo->extension);
                    $colors = ['pdf'=>'#dc2626','docx'=>'#2563eb','doc'=>'#2563eb','xlsx'=>'#059669','xls'=>'#059669','pptx'=>'#ea580c','ppt'=>'#ea580c'];
                    $color = $colors[$ext] ?? '#64748b';
                @endphp
                <div class="fc-file-row" data-nombre="{{ $archivo->nombre_original }}">
                    <div class="fc-file-row-icon" style="background:rgba(0,0,0,0.04)">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="{{ $color }}">
                            <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6z"/>
                        </svg>
                    </div>
                    <div class="fc-file-row-name">{{ $archivo->nombre_original }}</div>
                    <div class="fc-file-row-meta">{{ $archivo->tamanioFormateado() }}</div>
                    <span class="fc-file-row-ver">v{{ $archivo->version }}</span>
                    <div class="fc-file-row-actions">
                        <a href="{{ route('archivos.show', $archivo) }}" class="fc-file-row-btn" title="Ver detalle">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                        </a>
                        @can('download', $archivo)
                        <a href="{{ route('archivos.descargar', $archivo) }}" class="fc-file-row-btn dl" title="Descargar">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/></svg>
                        </a>
                        @endcan
                        @can('delete', $archivo)
                        <button onclick="confirmarEliminar({{ $archivo->id }}, '{{ addslashes($archivo->nombre_original) }}')" class="fc-file-row-btn del" title="Eliminar">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
                        </button>
                        @endcan
                    </div>
                </div>
                @endforeach
            </div>

            @endif

        </div>{{-- /fc-content --}}
    </div>{{-- /fc-main --}}
</div>

{{-- Modal eliminar archivo --}}
<div class="fc-modal-overlay" id="modalEliminar">
    <div class="fc-modal">
        <div class="fc-modal-title">¿Eliminar archivo?</div>
        <div class="fc-modal-sub">
            El archivo "<strong id="modalNombre"></strong>" será enviado a la papelera y no estará disponible. Esta acción puede revertirse por un administrador.
        </div>
        <div class="fc-modal-btns">
            <button class="fc-modal-cancel" onclick="cerrarModal()">Cancelar</button>
            <form id="formEliminar" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="fc-modal-confirm">Eliminar</button>
            </form>
        </div>
    </div>
</div>

{{-- Modal eliminar carpeta --}}
<div class="fc-modal-overlay" id="modalEliminarCarpeta">
    <div class="fc-modal">
        <div class="fc-modal-title">¿Eliminar esta carpeta?</div>
        <div class="fc-modal-sub">
            Solo puedes eliminar la carpeta si está vacía (sin subcarpetas ni archivos activos).
        </div>
        <div class="fc-modal-btns">
            <button class="fc-modal-cancel" onclick="document.getElementById('modalEliminarCarpeta').classList.remove('open')">Cancelar</button>
            <form action="{{ route('carpetas.destroy', $carpeta) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="fc-modal-confirm">Eliminar carpeta</button>
            </form>
        </div>
    </div>
</div>

<script>
function setView(v) {
    document.getElementById('btnGrid').classList.toggle('active', v === 'grid');
    document.getElementById('btnList').classList.toggle('active', v === 'list');
    document.getElementById('filesGrid').style.display = v === 'grid' ? 'grid' : 'none';
    document.getElementById('filesList').style.display = v === 'list' ? 'flex' : 'none';
    if (document.getElementById('subGrid'))
        document.getElementById('subGrid').style.display = v === 'grid' ? 'grid' : 'none';
}

function filtrar(q) {
    const t = q.toLowerCase();
    document.querySelectorAll('[data-nombre]').forEach(el => {
        el.style.display = el.dataset.nombre.toLowerCase().includes(t) ? '' : 'none';
    });
}

function confirmarEliminar(id, nombre) {
    document.getElementById('modalNombre').textContent = nombre;
    document.getElementById('formEliminar').action = '/archivos/' + id;
    document.getElementById('modalEliminar').classList.add('open');
}
function cerrarModal() {
    document.getElementById('modalEliminar').classList.remove('open');
}
function confirmarEliminarCarpeta() {
    document.getElementById('modalEliminarCarpeta').classList.add('open');
}
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        document.querySelectorAll('.fc-modal-overlay.open').forEach(m => m.classList.remove('open'));
    }
});
</script>
</x-app-layout>