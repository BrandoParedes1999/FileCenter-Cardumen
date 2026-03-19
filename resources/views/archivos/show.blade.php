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
.fc-topbar-title { font-size: 14px; font-weight: 700; color: #1e293b;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 400px; }
.fc-topbar-right { display: flex; align-items: center; gap: 14px; margin-left: auto; }
.fc-topbar-avatar {
    width: 36px; height: 36px;
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
    border-radius: 50%; display: flex; align-items: center;
    justify-content: center; font-size: 12px; font-weight: 700; color: #fff;
}
.fc-topbar-name { font-size: 13px; font-weight: 600; color: #0f172a; }
.fc-topbar-role { font-size: 11px; color: #7c3aed; }

.fc-content {
    flex: 1; overflow-y: auto; padding: 24px;
    scrollbar-width: thin; scrollbar-color: #cbd5e1 transparent;
    display: flex; gap: 20px; align-items: flex-start;
}
.fc-col-main { flex: 1; min-width: 0; display: flex; flex-direction: column; gap: 18px; }
.fc-col-side { width: 300px; min-width: 300px; display: flex; flex-direction: column; gap: 16px; }

/* ── Breadcrumb ── */
.fc-breadcrumb {
    display: flex; align-items: center; gap: 6px;
    margin-bottom: 4px; font-size: 13px; flex-wrap: wrap;
}
.fc-bread-item { color: #6366f1; font-weight: 500; text-decoration: none; }
.fc-bread-item:hover { text-decoration: underline; }
.fc-bread-sep { color: #cbd5e1; }
.fc-bread-current { color: #475569; font-weight: 600;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 200px; }

/* ── Header del archivo ── */
.fc-file-header {
    background: #fff; border: 1px solid #e2e8f0;
    border-radius: 16px; overflow: hidden;
    box-shadow: 0 2px 12px rgba(0,0,0,0.05);
}
.fc-file-header-top {
    padding: 24px 26px; display: flex; align-items: flex-start; gap: 18px;
    border-bottom: 1px solid #f1f5f9;
}
.fc-file-icon-wrap {
    width: 64px; height: 64px; border-radius: 16px;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.fc-file-name { font-size: 18px; font-weight: 700; color: #1e293b;
    margin-bottom: 5px; word-break: break-word; line-height: 1.3; }
.fc-file-meta-row {
    display: flex; align-items: center; flex-wrap: wrap; gap: 10px; margin-bottom: 12px;
}
.fc-badge {
    font-size: 11px; font-weight: 600; padding: 3px 10px;
    border-radius: 20px; letter-spacing: .04em;
}
.fc-badge-ext  { background: #f1f5f9; color: #475569; }
.fc-badge-ver  { background: rgba(99,102,241,0.1); color: #4f46e5; }
.fc-badge-pub  { background: rgba(5,150,105,0.1); color: #059669; }
.fc-badge-priv { background: rgba(100,116,139,0.1); color: #475569; }

.fc-file-actions { display: flex; gap: 8px; flex-wrap: wrap; }
.fc-action-btn {
    display: flex; align-items: center; gap: 7px;
    padding: 9px 16px; border-radius: 9px; font-size: 13px; font-weight: 600;
    cursor: pointer; text-decoration: none; transition: all .15s; border: 1px solid;
}
.fc-action-btn.download {
    background: linear-gradient(135deg, #059669, #10b981);
    color: #fff; border-color: transparent;
    box-shadow: 0 4px 12px rgba(5,150,105,0.3);
}
.fc-action-btn.download:hover { opacity: .9; transform: translateY(-1px); }
.fc-action-btn.edit {
    background: #fff; color: #4f46e5; border-color: #c7d2fe;
}
.fc-action-btn.edit:hover { background: #f0f4ff; }
.fc-action-btn.delete {
    background: #fff; color: #dc2626; border-color: #fca5a5;
}
.fc-action-btn.delete:hover { background: #fee2e2; }
.fc-action-btn.solicitar {
    background: #fff; color: #f59e0b; border-color: #fde68a;
}
.fc-action-btn.solicitar:hover { background: #fffbeb; }

/* ── Stats del archivo ── */
.fc-file-stats {
    display: grid; grid-template-columns: repeat(4, 1fr);
    gap: 0; border-top: 1px solid #f1f5f9;
}
.fc-file-stat {
    padding: 16px 20px; border-right: 1px solid #f1f5f9; text-align: center;
}
.fc-file-stat:last-child { border-right: none; }
.fc-stat-val { font-size: 18px; font-weight: 700; color: #1e293b; }
.fc-stat-lbl { font-size: 11px; color: #94a3b8; margin-top: 3px; font-weight: 500; }

/* ── Descripción ── */
.fc-section-card {
    background: #fff; border: 1px solid #e2e8f0;
    border-radius: 14px; overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}
.fc-section-header {
    padding: 16px 20px; border-bottom: 1px solid #f1f5f9;
    display: flex; align-items: center; gap: 10px;
    font-size: 13px; font-weight: 700; color: #1e293b;
}
.fc-section-icon {
    width: 28px; height: 28px; border-radius: 7px;
    display: flex; align-items: center; justify-content: center;
}
.fc-section-body { padding: 18px 20px; }
.fc-desc-text {
    font-size: 13px; color: #475569; line-height: 1.8;
}
.fc-desc-empty { font-size: 13px; color: #94a3b8; font-style: italic; }

/* ── Editar descripción inline ── */
.fc-edit-desc-form { display: none; }
.fc-edit-desc-form.visible { display: block; }
.fc-edit-desc-form textarea {
    width: 100%; background: #f8fafc; border: 1px solid #e2e8f0;
    border-radius: 9px; padding: 10px 12px; font-size: 13px;
    font-family: inherit; resize: vertical; outline: none;
    color: #1e293b; line-height: 1.6;
    transition: border-color .2s;
}
.fc-edit-desc-form textarea:focus { border-color: #6366f1; background: #fff; }
.fc-edit-desc-btns { display: flex; gap: 8px; margin-top: 10px; justify-content: flex-end; }
.fc-mini-btn {
    padding: 7px 14px; border-radius: 8px; font-size: 12px; font-weight: 600;
    cursor: pointer; border: 1px solid #e2e8f0; background: #f8fafc; color: #475569;
    transition: all .15s;
}
.fc-mini-btn.primary {
    background: #4f46e5; color: #fff; border-color: transparent;
}
.fc-mini-btn.primary:hover { background: #4338ca; }
.fc-mini-btn:hover { background: #f1f5f9; }

/* ── Versiones ── */
.fc-ver-item {
    display: flex; align-items: center; gap: 12px;
    padding: 12px 16px; border-bottom: 1px solid #f8fafc;
    transition: background .15s;
}
.fc-ver-item:last-child { border-bottom: none; }
.fc-ver-item:hover { background: #fafbff; }
.fc-ver-num {
    width: 32px; height: 32px; border-radius: 8px;
    background: rgba(99,102,241,0.1); color: #4f46e5;
    display: flex; align-items: center; justify-content: center;
    font-size: 11px; font-weight: 800; flex-shrink: 0;
}
.fc-ver-num.current {
    background: linear-gradient(135deg, #4f46e5, #7c3aed); color: #fff;
}
.fc-ver-name { font-size: 13px; font-weight: 600; color: #1e293b; }
.fc-ver-meta { font-size: 11px; color: #94a3b8; margin-top: 2px; }
.fc-ver-nota { font-size: 11px; color: #6366f1; margin-top: 2px;
    font-style: italic; }
.fc-ver-actions { margin-left: auto; display: flex; gap: 6px; }
.fc-ver-btn {
    padding: 5px 10px; border-radius: 7px; font-size: 11px; font-weight: 600;
    cursor: pointer; border: 1px solid #e2e8f0; background: #f8fafc; color: #475569;
    text-decoration: none; transition: all .15s; white-space: nowrap;
}
.fc-ver-btn:hover { border-color: #c7d2fe; background: #f0f4ff; color: #4f46e5; }
.fc-ver-badge-current {
    font-size: 9px; font-weight: 700; color: #059669;
    background: rgba(5,150,105,0.1); padding: 2px 7px;
    border-radius: 20px; letter-spacing: .04em;
}

/* ── Panel lateral — info ── */
.fc-info-card {
    background: #fff; border: 1px solid #e2e8f0;
    border-radius: 14px; overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}
.fc-info-header {
    padding: 14px 18px; border-bottom: 1px solid #f1f5f9;
    font-size: 12px; font-weight: 700; color: #1e293b;
    display: flex; align-items: center; gap: 8px;
}
.fc-info-body { padding: 4px 0; }
.fc-info-row {
    display: flex; align-items: flex-start; justify-content: space-between;
    padding: 11px 18px; border-bottom: 1px solid #f8fafc; gap: 10px;
}
.fc-info-row:last-child { border-bottom: none; }
.fc-info-label { font-size: 11px; color: #94a3b8; font-weight: 600;
    text-transform: uppercase; letter-spacing: .07em; flex-shrink: 0; margin-top: 1px; }
.fc-info-val { font-size: 12px; color: #1e293b; font-weight: 600;
    text-align: right; word-break: break-all; }
.fc-info-val.mono { font-family: monospace; font-size: 11px; color: #475569; }

/* ── Hash ── */
.fc-hash-box {
    padding: 10px 14px; background: #f8fafc; border-radius: 8px;
    font-family: monospace; font-size: 10px; color: #64748b;
    word-break: break-all; line-height: 1.5; border: 1px solid #e2e8f0;
    cursor: pointer; transition: background .15s;
}
.fc-hash-box:hover { background: #f1f5f9; }

/* ── Modal restaurar ── */
.fc-modal-overlay {
    display: none; position: fixed; inset: 0;
    background: rgba(15,23,42,0.5); z-index: 200;
    align-items: center; justify-content: center;
    backdrop-filter: blur(4px);
}
.fc-modal-overlay.open { display: flex; }
.fc-modal {
    background: #fff; border-radius: 16px; padding: 28px;
    width: 420px; max-width: 92vw;
    box-shadow: 0 24px 64px rgba(0,0,0,0.18);
}
.fc-modal-title { font-size: 16px; font-weight: 700; color: #1e293b; margin-bottom: 6px; }
.fc-modal-sub { font-size: 13px; color: #64748b; margin-bottom: 22px; line-height: 1.6; }
.fc-modal-btns { display: flex; gap: 10px; justify-content: flex-end; }
.fc-modal-cancel {
    padding: 9px 18px; border-radius: 9px; border: 1px solid #e2e8f0;
    background: #f8fafc; color: #475569; font-size: 13px; cursor: pointer;
}
.fc-modal-confirm {
    padding: 9px 18px; border-radius: 9px; border: none;
    background: #4f46e5; color: #fff; font-size: 13px; font-weight: 600; cursor: pointer;
}
.fc-modal-confirm:hover { background: #4338ca; }

/* ── Flash ── */
.fc-flash {
    padding: 12px 16px; border-radius: 10px;
    font-size: 13px; font-weight: 500; display: flex; align-items: center; gap: 9px;
    margin-bottom: 2px;
}
.fc-flash.success { background: rgba(5,150,105,0.08); border: 1px solid rgba(5,150,105,0.25); color: #065f46; }
.fc-flash.error   { background: rgba(220,38,38,0.08); border: 1px solid rgba(220,38,38,0.2); color: #991b1b; }

/* ── Modal eliminar ── */
.fc-modal-confirm.danger { background: #dc2626; }
.fc-modal-confirm.danger:hover { background: #b91c1c; }
</style>

<div class="fc-wrapper">
    @include('components.sidebar')

    <div class="fc-main">
        <header class="fc-topbar">
            @php
                $extColors = [
                    'pdf'  => '#dc2626', 'doc'  => '#2563eb', 'docx' => '#2563eb',
                    'xls'  => '#059669', 'xlsx' => '#059669', 'ppt'  => '#ea580c',
                    'pptx' => '#ea580c', 'zip'  => '#f59e0b', 'rar'  => '#f59e0b',
                    'jpg'  => '#06b6d4', 'jpeg' => '#06b6d4', 'png'  => '#06b6d4',
                ];
                $ext = strtolower($archivo->extension);
                $color = $extColors[$ext] ?? '#64748b';
            @endphp
            <svg width="16" height="16" viewBox="0 0 24 24" fill="{{ $color }}">
                <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6z"/>
            </svg>
            <span class="fc-topbar-title">{{ $archivo->nombre_original }}</span>
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

            {{-- Columna principal --}}
            <div class="fc-col-main">

                {{-- Breadcrumb --}}
                <div class="fc-breadcrumb">
                    <a href="{{ route('carpetas.index') }}" class="fc-bread-item">📁 Inicio</a>
                    @foreach($migas as $i => $miga)
                        <span class="fc-bread-sep">›</span>
                        @if($i < count($migas)-1)
                            <a href="{{ route('carpetas.show', $miga['id']) }}" class="fc-bread-item">{{ $miga['nombre'] }}</a>
                        @else
                            <a href="{{ route('carpetas.show', $miga['id']) }}" class="fc-bread-item">{{ $miga['nombre'] }}</a>
                        @endif
                    @endforeach
                    <span class="fc-bread-sep">›</span>
                    <span class="fc-bread-current">{{ $archivo->nombre_original }}</span>
                </div>

                {{-- Flash --}}
                @if(session('success'))
                <div class="fc-flash success">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="#059669"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                    {{ session('success') }}
                </div>
                @endif
                @if(session('error') || $errors->any())
                <div class="fc-flash error">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="#dc2626"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                    {{ session('error') ?? $errors->first() }}
                </div>
                @endif

                {{-- Header del archivo --}}
                <div class="fc-file-header">
                    <div class="fc-file-header-top">
                        {{-- Icono grande --}}
                        <div class="fc-file-icon-wrap" style="background: {{ 'rgba(' . implode(',', sscanf($color, '#%02x%02x%02x') ?? [100,116,139]) . ',0.1)' }}">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="{{ $color }}">
                                <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6z"/>
                            </svg>
                        </div>

                        <div style="flex:1;min-width:0">
                            <div class="fc-file-name">{{ $archivo->nombre_original }}</div>
                            <div class="fc-file-meta-row">
                                <span class="fc-badge fc-badge-ext">{{ strtoupper($ext) }}</span>
                                <span class="fc-badge fc-badge-ver">v{{ $archivo->version }}</span>
                                <span class="fc-badge {{ $archivo->carpeta->es_publico ? 'fc-badge-pub' : 'fc-badge-priv' }}">
                                    {{ $archivo->carpeta->es_publico ? '🌐 Carpeta pública' : '🔒 Carpeta privada' }}
                                </span>
                                <span style="font-size:12px;color:#94a3b8">{{ $archivo->tamanioFormateado() }}</span>
                            </div>

                            {{-- Acciones --}}
                            <div class="fc-file-actions">
                                @can('download', $archivo)
                                <a href="{{ route('archivos.descargar', $archivo) }}" class="fc-action-btn download">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/>
                                    </svg>
                                    Descargar
                                </a>
                                @endcan

                                @can('update', $archivo)
                                <button onclick="toggleEditDesc()" class="fc-action-btn edit">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                                    </svg>
                                    Editar descripción
                                </button>
                                @endcan

                                @can('delete', $archivo)
                                <button onclick="document.getElementById('modalEliminar').classList.add('open')" class="fc-action-btn delete">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                                    </svg>
                                    Eliminar
                                </button>
                                @endcan

                                @cannot('download', $archivo)
                                <a href="{{ route('solicitudes.create', ['archivo_id' => $archivo->id]) }}" class="fc-action-btn solicitar">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                                    </svg>
                                    Solicitar acceso
                                </a>
                                @endcannot
                            </div>
                        </div>
                    </div>

                    {{-- Stats --}}
                    <div class="fc-file-stats">
                        <div class="fc-file-stat">
                            <div class="fc-stat-val">{{ $archivo->version }}</div>
                            <div class="fc-stat-lbl">Versión actual</div>
                        </div>
                        <div class="fc-file-stat">
                            <div class="fc-stat-val">{{ $archivo->numero_descargas }}</div>
                            <div class="fc-stat-lbl">Descargas</div>
                        </div>
                        <div class="fc-file-stat">
                            <div class="fc-stat-val">{{ $archivo->versiones->count() }}</div>
                            <div class="fc-stat-lbl">Versiones</div>
                        </div>
                        <div class="fc-file-stat">
                            <div class="fc-stat-val">{{ $archivo->tamanioFormateado() }}</div>
                            <div class="fc-stat-lbl">Tamaño</div>
                        </div>
                    </div>
                </div>

                {{-- Descripción --}}
                <div class="fc-section-card">
                    <div class="fc-section-header">
                        <div class="fc-section-icon" style="background:rgba(99,102,241,0.1)">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="#6366f1">
                                <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                            </svg>
                        </div>
                        Descripción
                    </div>
                    <div class="fc-section-body">
                        {{-- Texto visible --}}
                        <div id="descText">
                            @if($archivo->descripcion)
                                <p class="fc-desc-text">{{ $archivo->descripcion }}</p>
                            @else
                                <p class="fc-desc-empty">Sin descripción. Puedes agregar una haciendo clic en "Editar descripción".</p>
                            @endif
                        </div>

                        {{-- Form editar descripción inline --}}
                        @can('update', $archivo)
                        <div class="fc-edit-desc-form" id="editDescForm">
                            <form action="{{ route('archivos.update', $archivo) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <textarea name="descripcion" rows="4"
                                    placeholder="Describe el contenido de este archivo...">{{ $archivo->descripcion }}</textarea>
                                <div class="fc-edit-desc-btns">
                                    <button type="button" class="fc-mini-btn" onclick="toggleEditDesc()">Cancelar</button>
                                    <button type="submit" class="fc-mini-btn primary">Guardar</button>
                                </div>
                            </form>
                        </div>
                        @endcan
                    </div>
                </div>

                {{-- Historial de versiones --}}
                <div class="fc-section-card">
                    <div class="fc-section-header">
                        <div class="fc-section-icon" style="background:rgba(124,58,237,0.1)">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="#7c3aed">
                                <path d="M13 3c-4.97 0-9 4.03-9 9H1l3.89 3.89.07.14L9 12H6c0-3.87 3.13-7 7-7s7 3.13 7 7-3.13 7-7 7c-1.93 0-3.68-.79-4.94-2.06l-1.42 1.42C8.27 19.99 10.51 21 13 21c4.97 0 9-4.03 9-9s-4.03-9-9-9zm-1 5v5l4.28 2.54.72-1.21-3.5-2.08V8H12z"/>
                            </svg>
                        </div>
                        Historial de versiones
                        <span style="margin-left:auto;font-size:11px;background:#f1f5f9;
                            color:#64748b;padding:2px 9px;border-radius:20px;font-weight:600;">
                            {{ $archivo->versiones->count() }}
                        </span>
                    </div>
                    <div>
                        @forelse($archivo->versiones as $ver)
                        <div class="fc-ver-item">
                            <div class="fc-ver-num {{ $ver->version == $archivo->version ? 'current' : '' }}">
                                v{{ $ver->version }}
                            </div>
                            <div style="flex:1;min-width:0">
                                <div class="fc-ver-name">
                                    {{ $ver->nombre_original }}
                                    @if($ver->version == $archivo->version)
                                        <span class="fc-ver-badge-current">Actual</span>
                                    @endif
                                </div>
                                <div class="fc-ver-meta">
                                    {{ $ver->tamanioFormateado() }}
                                    · Subido por {{ $ver->subidoPor->nombre ?? 'N/A' }} {{ $ver->subidoPor->paterno ?? '' }}
                                    · {{ $ver->created_at?->diffForHumans() ?? '—' }}
                                </div>
                                @if($ver->nota_version)
                                <div class="fc-ver-nota">{{ $ver->nota_version }}</div>
                                @endif
                            </div>
                            <div class="fc-ver-actions">
                                @if($ver->version != $archivo->version)
                                    @can('update', $archivo)
                                    <button onclick="confirmarRestaurar({{ $ver->id }}, {{ $ver->version }})"
                                        class="fc-ver-btn">Restaurar</button>
                                    @endcan
                                @endif
                            </div>
                        </div>
                        @empty
                        <div style="padding:20px;text-align:center;font-size:13px;color:#94a3b8">
                            Sin historial de versiones
                        </div>
                        @endforelse
                    </div>
                </div>

            </div>{{-- /fc-col-main --}}

            {{-- Panel lateral --}}
            <div class="fc-col-side">

                {{-- Info general --}}
                <div class="fc-info-card">
                    <div class="fc-info-header">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="#6366f1">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>
                        </svg>
                        Información
                    </div>
                    <div class="fc-info-body">
                        <div class="fc-info-row">
                            <span class="fc-info-label">Carpeta</span>
                            <a href="{{ route('carpetas.show', $archivo->carpeta) }}"
                               style="font-size:12px;font-weight:600;color:#6366f1;text-decoration:none;text-align:right;">
                                {{ $archivo->carpeta->nombre }}
                            </a>
                        </div>
                        <div class="fc-info-row">
                            <span class="fc-info-label">Subido por</span>
                            <span class="fc-info-val">
                                {{ $archivo->subidoPor->nombre ?? '—' }} {{ $archivo->subidoPor->paterno ?? '' }}
                            </span>
                        </div>
                        <div class="fc-info-row">
                            <span class="fc-info-label">Fecha subida</span>
                            <span class="fc-info-val">{{ $archivo->created_at?->format('d/m/Y H:i') ?? '—' }}</span>
                        </div>
                        <div class="fc-info-row">
                            <span class="fc-info-label">Última mod.</span>
                            <span class="fc-info-val">{{ $archivo->updated_at?->diffForHumans() ?? '—' }}</span>
                        </div>
                        <div class="fc-info-row">
                            <span class="fc-info-label">Tipo MIME</span>
                            <span class="fc-info-val mono">{{ $archivo->tipo_mime ?? '—' }}</span>
                        </div>
                        <div class="fc-info-row">
                            <span class="fc-info-label">Extensión</span>
                            <span class="fc-info-val">{{ strtoupper($ext) }}</span>
                        </div>
                        <div class="fc-info-row">
                            <span class="fc-info-label">Tamaño</span>
                            <span class="fc-info-val">{{ $archivo->tamanioFormateado() }}</span>
                        </div>
                        <div class="fc-info-row">
                            <span class="fc-info-label">Descargas</span>
                            <span class="fc-info-val">{{ $archivo->numero_descargas }}</span>
                        </div>
                    </div>
                </div>

                {{-- SHA256 --}}
                @if($archivo->hash_sha256)
                <div class="fc-info-card">
                    <div class="fc-info-header">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="#059669">
                            <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/>
                        </svg>
                        Integridad SHA-256
                    </div>
                    <div style="padding:14px 16px">
                        <div class="fc-hash-box" onclick="copiarHash('{{ $archivo->hash_sha256 }}')"
                             title="Clic para copiar">
                            {{ $archivo->hash_sha256 }}
                        </div>
                        <div style="font-size:11px;color:#94a3b8;margin-top:6px">
                            Clic para copiar al portapapeles
                        </div>
                    </div>
                </div>
                @endif

                {{-- Subir nueva versión --}}
                @can('update', $archivo)
                <div class="fc-info-card">
                    <div class="fc-info-header">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="#f59e0b">
                            <path d="M9 16h6v-6h4l-7-7-7 7h4zm-4 2h14v2H5z"/>
                        </svg>
                        Nueva versión
                    </div>
                    <div style="padding:14px 16px">
                        <p style="font-size:12px;color:#64748b;margin-bottom:12px;line-height:1.6;">
                            Sube una nueva versión del archivo. La versión actual quedará en el historial.
                        </p>
                        <a href="{{ route('archivos.create', ['carpeta_id' => $archivo->carpeta_id]) }}"
                           style="display:flex;align-items:center;justify-content:center;gap:7px;
                               padding:9px 16px;border-radius:9px;
                               background:linear-gradient(135deg,#f59e0b,#fbbf24);
                               color:#fff;font-size:13px;font-weight:600;text-decoration:none;
                               box-shadow:0 4px 12px rgba(245,158,11,0.3);">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M9 16h6v-6h4l-7-7-7 7h4zm-4 2h14v2H5z"/>
                            </svg>
                            Subir nueva versión
                        </a>
                    </div>
                </div>
                @endcan

            </div>{{-- /fc-col-side --}}

        </div>{{-- /fc-content --}}
    </div>{{-- /fc-main --}}
</div>

{{-- Modal eliminar --}}
<div class="fc-modal-overlay" id="modalEliminar">
    <div class="fc-modal">
        <div class="fc-modal-title">¿Eliminar este archivo?</div>
        <div class="fc-modal-sub">
            El archivo "<strong>{{ $archivo->nombre_original }}</strong>" se enviará a la papelera.
            El historial de versiones quedará guardado y puede ser recuperado por un administrador.
        </div>
        <div class="fc-modal-btns">
            <button class="fc-modal-cancel"
                onclick="document.getElementById('modalEliminar').classList.remove('open')">
                Cancelar
            </button>
            <form action="{{ route('archivos.destroy', $archivo) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="fc-modal-confirm danger">Eliminar</button>
            </form>
        </div>
    </div>
</div>

{{-- Modal restaurar versión --}}
<div class="fc-modal-overlay" id="modalRestaurar">
    <div class="fc-modal">
        <div class="fc-modal-title">¿Restaurar esta versión?</div>
        <div class="fc-modal-sub" id="modalRestaurarSub">
            El archivo principal pasará a usar la versión seleccionada.
            La versión actual quedará guardada en el historial.
        </div>
        <div class="fc-modal-btns">
            <button class="fc-modal-cancel"
                onclick="document.getElementById('modalRestaurar').classList.remove('open')">
                Cancelar
            </button>
            <form action="{{ route('archivos.restaurar-version', $archivo) }}" method="POST" id="formRestaurar">
                @csrf
                <input type="hidden" name="version_id" id="versionIdInput">
                <button type="submit" class="fc-modal-confirm">Restaurar</button>
            </form>
        </div>
    </div>
</div>

<script>
function toggleEditDesc() {
    const form = document.getElementById('editDescForm');
    const text = document.getElementById('descText');
    const visible = form.classList.toggle('visible');
    text.style.display = visible ? 'none' : 'block';
}

function copiarHash(hash) {
    navigator.clipboard.writeText(hash).then(() => {
        const el = event.target;
        const orig = el.textContent;
        el.textContent = '✓ Copiado';
        el.style.color = '#059669';
        setTimeout(() => {
            el.textContent = orig;
            el.style.color = '';
        }, 1500);
    });
}

function confirmarRestaurar(versionId, versionNum) {
    document.getElementById('versionIdInput').value = versionId;
    document.getElementById('modalRestaurarSub').innerHTML =
        `El archivo principal pasará a usar la <strong>versión ${versionNum}</strong>. La versión actual quedará en el historial.`;
    document.getElementById('modalRestaurar').classList.add('open');
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        document.querySelectorAll('.fc-modal-overlay.open')
            .forEach(m => m.classList.remove('open'));
    }
});
</script>
</x-app-layout>
