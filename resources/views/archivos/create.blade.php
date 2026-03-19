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
.fc-topbar-right { display: flex; align-items: center; gap: 14px; margin-left: auto; }
.fc-topbar-avatar {
    width: 36px; height: 36px;
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
    border-radius: 50%; display: flex; align-items: center;
    justify-content: center; font-size: 12px; font-weight: 700; color: #fff;
}
.fc-topbar-name { font-size: 13px; font-weight: 600; color: #0f172a; }
.fc-topbar-role { font-size: 11px; color: #7c3aed; }

.fc-content { flex: 1; overflow-y: auto; padding: 32px 24px; scrollbar-width: thin; }

/* ── Form card ── */
.fc-form-wrap { max-width: 620px; margin: 0 auto; }

.fc-breadcrumb {
    display: flex; align-items: center; gap: 6px;
    margin-bottom: 22px; font-size: 13px;
}
.fc-bread-item { color: #6366f1; font-weight: 500; text-decoration: none; }
.fc-bread-item:hover { text-decoration: underline; }
.fc-bread-sep { color: #cbd5e1; }
.fc-bread-current { color: #475569; font-weight: 600; }

.fc-form-card {
    background: #fff; border: 1px solid #e2e8f0;
    border-radius: 18px; overflow: hidden;
    box-shadow: 0 4px 24px rgba(0,0,0,0.06);
}
.fc-form-header {
    padding: 24px 28px; border-bottom: 1px solid #f1f5f9;
    display: flex; align-items: center; gap: 14px;
}
.fc-form-header-icon {
    width: 46px; height: 46px; border-radius: 13px;
    background: rgba(5,150,105,0.1);
    display: flex; align-items: center; justify-content: center;
}
.fc-form-title { font-size: 17px; font-weight: 700; color: #1e293b; }
.fc-form-sub { font-size: 12px; color: #94a3b8; margin-top: 2px; }
.fc-form-body { padding: 28px; display: flex; flex-direction: column; gap: 22px; }

/* ── Zona de drop ── */
.fc-dropzone {
    border: 2px dashed #c7d2fe; border-radius: 14px;
    background: #fafbff; padding: 40px 24px;
    display: flex; flex-direction: column; align-items: center;
    justify-content: center; text-align: center; cursor: pointer;
    transition: border-color .2s, background .2s;
    position: relative;
}
.fc-dropzone:hover, .fc-dropzone.dragover {
    border-color: #6366f1; background: #f0f0ff;
}
.fc-dropzone input[type="file"] {
    position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%;
}
.fc-drop-icon {
    width: 56px; height: 56px; border-radius: 16px;
    background: rgba(99,102,241,0.1);
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 14px;
}
.fc-drop-title { font-size: 15px; font-weight: 600; color: #1e293b; margin-bottom: 6px; }
.fc-drop-sub { font-size: 13px; color: #94a3b8; margin-bottom: 14px; }
.fc-drop-btn {
    display: inline-flex; align-items: center; gap: 7px;
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
    color: #fff; border: none; border-radius: 9px;
    padding: 9px 18px; font-size: 13px; font-weight: 600;
    cursor: pointer; pointer-events: none;
    box-shadow: 0 4px 12px rgba(79,70,229,0.3);
}
.fc-drop-limit { font-size: 11px; color: #cbd5e1; margin-top: 10px; }

/* ── Preview del archivo seleccionado ── */
.fc-file-preview {
    display: none; background: #f8fafc; border: 1px solid #e2e8f0;
    border-radius: 12px; padding: 14px 16px;
    align-items: center; gap: 14px;
}
.fc-file-preview.visible { display: flex; }
.fc-file-prev-icon {
    width: 42px; height: 42px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.fc-file-prev-name { font-size: 13px; font-weight: 600; color: #1e293b; }
.fc-file-prev-size { font-size: 12px; color: #94a3b8; margin-top: 2px; }
.fc-file-prev-remove {
    margin-left: auto; width: 28px; height: 28px; border-radius: 7px;
    border: 1px solid #e2e8f0; background: #fff; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    color: #94a3b8; transition: all .15s;
}
.fc-file-prev-remove:hover { background: #fee2e2; border-color: #fca5a5; color: #dc2626; }

/* ── Campo descripción ── */
.fc-field label {
    display: block; font-size: 12px; font-weight: 700; color: #374151;
    margin-bottom: 7px; text-transform: uppercase; letter-spacing: .06em;
}
.fc-field textarea {
    width: 100%; background: #f8fafc; border: 1px solid #e2e8f0;
    border-radius: 10px; padding: 11px 14px;
    color: #1e293b; font-size: 13px; outline: none; resize: vertical;
    font-family: inherit; line-height: 1.6;
    transition: border-color .2s, background .2s;
}
.fc-field textarea:focus { background: #fff; border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,0.1); }
.fc-field textarea::placeholder { color: #94a3b8; }
.fc-field-hint { font-size: 11px; color: #94a3b8; margin-top: 5px; }

/* ── Info de la carpeta destino ── */
.fc-dest-card {
    display: flex; align-items: center; gap: 12px;
    padding: 14px 16px; background: rgba(79,70,229,0.05);
    border: 1px solid rgba(99,102,241,0.2); border-radius: 12px;
}
.fc-dest-icon {
    width: 38px; height: 38px; border-radius: 10px;
    background: rgba(79,70,229,0.1);
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.fc-dest-label { font-size: 10px; font-weight: 700; color: #6366f1;
    text-transform: uppercase; letter-spacing: .08em; margin-bottom: 2px; }
.fc-dest-name { font-size: 14px; font-weight: 600; color: #1e293b; }
.fc-dest-path { font-size: 11px; color: #94a3b8; font-family: monospace; margin-top: 2px; }

/* ── Progress bar ── */
.fc-progress-wrap { display: none; }
.fc-progress-wrap.visible { display: block; }
.fc-progress-label {
    display: flex; justify-content: space-between;
    font-size: 12px; color: #475569; margin-bottom: 7px; font-weight: 600;
}
.fc-progress-bar-bg {
    height: 8px; background: #f1f5f9; border-radius: 4px; overflow: hidden;
}
.fc-progress-bar {
    height: 100%; border-radius: 4px;
    background: linear-gradient(90deg, #4f46e5, #7c3aed);
    width: 0%; transition: width .3s;
}

/* ── Footer ── */
.fc-form-footer {
    padding: 20px 28px; border-top: 1px solid #f1f5f9;
    display: flex; align-items: center; justify-content: space-between; gap: 10px;
}
.fc-form-footer-left { font-size: 12px; color: #94a3b8; }
.fc-form-footer-right { display: flex; gap: 10px; }
.fc-btn-cancel {
    padding: 10px 20px; border-radius: 10px; border: 1px solid #e2e8f0;
    background: #f8fafc; color: #475569; font-size: 13px; cursor: pointer;
    text-decoration: none; transition: background .15s;
}
.fc-btn-cancel:hover { background: #f1f5f9; }
.fc-btn-submit {
    padding: 10px 22px; border-radius: 10px; border: none;
    background: linear-gradient(135deg, #059669, #10b981);
    color: #fff; font-size: 13px; font-weight: 600; cursor: pointer;
    box-shadow: 0 4px 12px rgba(5,150,105,0.3);
    transition: opacity .15s, transform .1s;
    display: flex; align-items: center; gap: 7px;
}
.fc-btn-submit:hover { opacity: .9; transform: translateY(-1px); }
.fc-btn-submit:disabled { opacity: .5; cursor: not-allowed; transform: none; }

/* ── Error / Flash ── */
.fc-flash {
    margin-bottom: 18px; padding: 12px 16px; border-radius: 10px;
    font-size: 13px; font-weight: 500; display: flex; align-items: center; gap: 9px;
}
.fc-flash.error { background: rgba(220,38,38,0.08); border: 1px solid rgba(220,38,38,0.2); color: #991b1b; }
.fc-field-error { font-size: 12px; color: #dc2626; margin-top: 5px; }

/* ── Tipos de archivo aceptados ── */
.fc-tipos {
    display: flex; flex-wrap: wrap; gap: 6px;
}
.fc-tipo-badge {
    font-size: 10px; font-weight: 700; padding: 3px 9px;
    border-radius: 20px; background: #f1f5f9; color: #475569;
    text-transform: uppercase; letter-spacing: .06em;
}
</style>

<div class="fc-wrapper">
    @include('components.sidebar')

    <div class="fc-main">
        <header class="fc-topbar">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#059669">
                <path d="M9 16h6v-6h4l-7-7-7 7h4zm-4 2h14v2H5z"/>
            </svg>
            <span class="fc-topbar-title">Subir Archivo</span>
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

            {{-- Breadcrumb --}}
            <div class="fc-breadcrumb">
                <a href="{{ route('carpetas.index') }}" class="fc-bread-item">📁 Inicio</a>
                <span class="fc-bread-sep">›</span>
                <a href="{{ route('carpetas.show', $carpeta) }}" class="fc-bread-item">{{ $carpeta->nombre }}</a>
                <span class="fc-bread-sep">›</span>
                <span class="fc-bread-current">Subir archivo</span>
            </div>

            @if($errors->any())
            <div class="fc-flash error">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="#dc2626">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                </svg>
                {{ $errors->first() }}
            </div>
            @endif

            <div class="fc-form-wrap">
                <div class="fc-form-card">
                    <div class="fc-form-header">
                        <div class="fc-form-header-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="#059669">
                                <path d="M9 16h6v-6h4l-7-7-7 7h4zm-4 2h14v2H5z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="fc-form-title">Subir nuevo archivo</div>
                            <div class="fc-form-sub">
                                Si el archivo ya existe en esta carpeta se creará una nueva versión automáticamente
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('archivos.store') }}" method="POST"
                          enctype="multipart/form-data" id="uploadForm">
                        @csrf
                        <input type="hidden" name="carpeta_id" value="{{ $carpeta->id }}">

                        <div class="fc-form-body">

                            {{-- Carpeta destino --}}
                            <div class="fc-dest-card">
                                <div class="fc-dest-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="#4f46e5">
                                        <path d="M20 6h-8l-2-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="fc-dest-label">Carpeta destino</div>
                                    <div class="fc-dest-name">{{ $carpeta->nombre }}</div>
                                    <div class="fc-dest-path">{{ $carpeta->path }}</div>
                                </div>
                                @if($carpeta->es_publico)
                                <span style="margin-left:auto;font-size:10px;font-weight:700;
                                    background:rgba(5,150,105,0.1);color:#059669;
                                    padding:3px 10px;border-radius:20px;">Pública</span>
                                @endif
                            </div>

                            {{-- Drop zone --}}
                            <div>
                                <label style="display:block;font-size:12px;font-weight:700;
                                    color:#374151;margin-bottom:7px;text-transform:uppercase;letter-spacing:.06em;">
                                    Archivo *
                                </label>

                                <div class="fc-dropzone" id="dropzone">
                                    <input type="file" name="archivo" id="fileInput"
                                           accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.csv,.zip,.rar,.7z,.jpg,.jpeg,.png,.gif,.webp,.mp4,.mp3"
                                           onchange="handleFile(this)">
                                    <div class="fc-drop-icon">
                                        <svg width="26" height="26" viewBox="0 0 24 24" fill="#6366f1">
                                            <path d="M9 16h6v-6h4l-7-7-7 7h4zm-4 2h14v2H5z"/>
                                        </svg>
                                    </div>
                                    <div class="fc-drop-title">Arrastra tu archivo aquí</div>
                                    <div class="fc-drop-sub">o haz clic para seleccionarlo</div>
                                    <div class="fc-drop-btn">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M20 6h-8l-2-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2z"/>
                                        </svg>
                                        Explorar archivos
                                    </div>
                                    <div class="fc-drop-limit">Máximo 100 MB por archivo</div>
                                </div>

                                {{-- Preview del archivo --}}
                                <div class="fc-file-preview" id="filePreview">
                                    <div class="fc-file-prev-icon" id="prevIcon">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="#4f46e5">
                                            <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="fc-file-prev-name" id="prevName">—</div>
                                        <div class="fc-file-prev-size" id="prevSize">—</div>
                                    </div>
                                    <button type="button" class="fc-file-prev-remove" onclick="quitarArchivo()">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                                        </svg>
                                    </button>
                                </div>

                                @error('archivo')
                                <div class="fc-field-error">{{ $message }}</div>
                                @enderror

                                {{-- Tipos aceptados --}}
                                <div style="margin-top:10px">
                                    <div style="font-size:11px;color:#94a3b8;margin-bottom:6px;">Tipos aceptados:</div>
                                    <div class="fc-tipos">
                                        @foreach(['PDF','DOC','DOCX','XLS','XLSX','PPT','PPTX','TXT','CSV','ZIP','RAR','JPG','PNG','MP4'] as $tipo)
                                            <span class="fc-tipo-badge">{{ $tipo }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            {{-- Descripción --}}
                            <div class="fc-field">
                                <label for="descripcion">Descripción <span style="color:#94a3b8;font-weight:400;text-transform:none;letter-spacing:0">(opcional)</span></label>
                                <textarea id="descripcion" name="descripcion" rows="3"
                                    placeholder="Describe brevemente el contenido del archivo...">{{ old('descripcion') }}</textarea>
                                <div class="fc-field-hint">Máximo 500 caracteres. Facilita la búsqueda y comprensión del archivo.</div>
                                @error('descripcion')
                                    <div class="fc-field-error">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Barra de progreso (se muestra al enviar) --}}
                            <div class="fc-progress-wrap" id="progressWrap">
                                <div class="fc-progress-label">
                                    <span>Subiendo archivo...</span>
                                    <span id="progressPct">0%</span>
                                </div>
                                <div class="fc-progress-bar-bg">
                                    <div class="fc-progress-bar" id="progressBar"></div>
                                </div>
                            </div>

                        </div>{{-- /fc-form-body --}}

                        <div class="fc-form-footer">
                            <div class="fc-form-footer-left">
                                💡 Si el archivo ya existe en esta carpeta, se creará una nueva versión
                            </div>
                            <div class="fc-form-footer-right">
                                <a href="{{ route('carpetas.show', $carpeta) }}" class="fc-btn-cancel">Cancelar</a>
                                <button type="submit" class="fc-btn-submit" id="submitBtn" disabled>
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M9 16h6v-6h4l-7-7-7 7h4zm-4 2h14v2H5z"/>
                                    </svg>
                                    Subir archivo
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
const extColores = {
    pdf:  { bg: 'rgba(220,38,38,0.1)',  fill: '#dc2626' },
    doc:  { bg: 'rgba(37,99,235,0.1)',  fill: '#2563eb' },
    docx: { bg: 'rgba(37,99,235,0.1)',  fill: '#2563eb' },
    xls:  { bg: 'rgba(5,150,105,0.1)',  fill: '#059669' },
    xlsx: { bg: 'rgba(5,150,105,0.1)',  fill: '#059669' },
    ppt:  { bg: 'rgba(234,88,12,0.1)',  fill: '#ea580c' },
    pptx: { bg: 'rgba(234,88,12,0.1)',  fill: '#ea580c' },
    zip:  { bg: 'rgba(245,158,11,0.1)', fill: '#f59e0b' },
    rar:  { bg: 'rgba(245,158,11,0.1)', fill: '#f59e0b' },
    jpg:  { bg: 'rgba(6,182,212,0.1)',  fill: '#06b6d4' },
    jpeg: { bg: 'rgba(6,182,212,0.1)',  fill: '#06b6d4' },
    png:  { bg: 'rgba(6,182,212,0.1)',  fill: '#06b6d4' },
};

function handleFile(input) {
    const file = input.files[0];
    if (!file) return;

    const ext = file.name.split('.').pop().toLowerCase();
    const color = extColores[ext] || { bg: 'rgba(100,116,139,0.1)', fill: '#64748b' };

    document.getElementById('prevIcon').style.background = color.bg;
    document.getElementById('prevIcon').innerHTML = `
        <svg width="20" height="20" viewBox="0 0 24 24" fill="${color.fill}">
            <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6z"/>
        </svg>`;
    document.getElementById('prevName').textContent = file.name;
    document.getElementById('prevSize').textContent = formatBytes(file.size) + ' · ' + ext.toUpperCase();
    document.getElementById('filePreview').classList.add('visible');
    document.getElementById('dropzone').style.display = 'none';
    document.getElementById('submitBtn').disabled = false;
}

function quitarArchivo() {
    document.getElementById('fileInput').value = '';
    document.getElementById('filePreview').classList.remove('visible');
    document.getElementById('dropzone').style.display = 'flex';
    document.getElementById('submitBtn').disabled = true;
}

function formatBytes(b) {
    if (b >= 1073741824) return (b/1073741824).toFixed(2) + ' GB';
    if (b >= 1048576)    return (b/1048576).toFixed(2) + ' MB';
    if (b >= 1024)       return (b/1024).toFixed(2) + ' KB';
    return b + ' B';
}

// Drag & drop
const dz = document.getElementById('dropzone');
dz.addEventListener('dragover', e => { e.preventDefault(); dz.classList.add('dragover'); });
dz.addEventListener('dragleave', () => dz.classList.remove('dragover'));
dz.addEventListener('drop', e => {
    e.preventDefault();
    dz.classList.remove('dragover');
    const dt = e.dataTransfer;
    if (dt.files.length) {
        document.getElementById('fileInput').files = dt.files;
        handleFile(document.getElementById('fileInput'));
    }
});

// Simular progreso al enviar
document.getElementById('uploadForm').addEventListener('submit', function(e) {
    if (!document.getElementById('fileInput').files.length) return;
    document.getElementById('progressWrap').classList.add('visible');
    document.getElementById('submitBtn').disabled = true;
    document.getElementById('submitBtn').textContent = 'Subiendo...';

    let pct = 0;
    const iv = setInterval(() => {
        pct = Math.min(pct + Math.random() * 15, 92);
        document.getElementById('progressBar').style.width = pct + '%';
        document.getElementById('progressPct').textContent = Math.round(pct) + '%';
    }, 200);

    // Limpiar intervalo si la página cambia
    window.addEventListener('beforeunload', () => clearInterval(iv));
});
</script>
</x-app-layout>