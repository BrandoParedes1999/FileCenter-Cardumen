<x-app-layout>
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

                <div class="fc-breadcrumb">
                    <a href="{{ route('carpetas.index') }}" class="fc-bread-item">📁 Inicio</a>
                    <span class="fc-bread-sep">›</span>
                    <a href="{{ route('carpetas.show', $carpeta) }}" class="fc-bread-item">{{ $carpeta->nombre }}</a>
                    <span class="fc-bread-sep">›</span>
                    <span class="fc-bread-current">Subir archivo</span>
                </div>

                @if($errors->any())
                <div class="fc-flash error">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="#dc2626"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                    {{ $errors->first() }}
                </div>
                @endif

                {{-- Aviso si la carpeta requiere aprobación --}}
                @if($carpeta->requiere_aprobacion_subida && !in_array(Auth::user()->rol, ['Superadmin','Aux_QHSE','Admin','Gerente']))
                <div class="fc-flash warning" style="margin-bottom:20px">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="#d97706"><path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/></svg>
                    <div>
                        <strong>Esta carpeta requiere aprobación para publicar archivos.</strong>
                        Tu archivo quedará pendiente de revisión por un administrador antes de ser visible.
                    </div>
                </div>
                @endif

                {{-- Aviso de modo solo lectura --}}
                @if($carpeta->esSoloLectura())
                <div class="fc-flash info" style="margin-bottom:20px">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="#4f46e5"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                    Esta carpeta está en modo <strong>solo lectura</strong>. Los archivos solo podrán visualizarse online.
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
                                <div class="fc-form-title">
                                    @if($carpeta->requiere_aprobacion_subida && !in_array(Auth::user()->rol, ['Superadmin','Aux_QHSE','Admin','Gerente']))
                                        Solicitar publicación de archivo
                                    @else
                                        Subir nuevo archivo
                                    @endif
                                </div>
                                <div class="fc-form-sub">
                                    Si el archivo ya existe en esta carpeta se creará una nueva versión automáticamente.
                                    Solo se aceptan <strong>PDF, Word y Excel</strong>.
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
                                    <div style="flex:1">
                                        <div class="fc-dest-label">Carpeta destino</div>
                                        <div class="fc-dest-name">{{ $carpeta->nombre }}</div>
                                        <div class="fc-dest-path">{{ $carpeta->path }}</div>
                                    </div>
                                    <div style="display:flex;flex-direction:column;gap:5px;align-items:flex-end">
                                        @if($carpeta->esSoloLectura())
                                        <span style="font-size:10px;font-weight:700;background:rgba(99,102,241,.1);
                                              color:#4f46e5;padding:3px 10px;border-radius:20px">👁 Solo lectura</span>
                                        @elseif($carpeta->modo_acceso === 'con_descarga')
                                        <span style="font-size:10px;font-weight:700;background:rgba(5,150,105,.1);
                                              color:#059669;padding:3px 10px;border-radius:20px">⬇ Con descarga</span>
                                        @endif
                                        @if($carpeta->requiere_aprobacion_subida)
                                        <span style="font-size:10px;font-weight:700;background:rgba(245,158,11,.1);
                                              color:#d97706;padding:3px 10px;border-radius:20px">⏳ Con aprobación</span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Drop zone --}}
                                <div>
                                    <label style="display:block;font-size:12px;font-weight:700;
                                        color:#374151;margin-bottom:7px;text-transform:uppercase;letter-spacing:.06em;">
                                        Archivo *
                                    </label>

                                    <div class="fc-dropzone" id="dropzone">
                                        {{-- Solo PDF, Word, Excel --}}
                                        <input type="file" name="archivo" id="fileInput"
                                            accept=".pdf,.doc,.docx,.xls,.xlsx"
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
                                        <div class="fc-drop-limit">Sin límite de tamaño · Solo PDF, Word y Excel</div>
                                    </div>

                                    {{-- Preview --}}
                                    <div class="fc-file-preview" id="filePreview">
                                        <div class="fc-file-prev-icon" id="prevIcon"></div>
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

                                    {{-- Tipos permitidos --}}
                                    <div style="margin-top:10px">
                                        <div style="font-size:11px;color:#94a3b8;margin-bottom:6px">Tipos aceptados:</div>
                                        <div class="fc-tipos">
                                            @foreach([
                                                ['PDF',  '#dc2626'],
                                                ['DOC',  '#2563eb'],
                                                ['DOCX', '#2563eb'],
                                                ['XLS',  '#059669'],
                                                ['XLSX', '#059669'],
                                            ] as [$tipo, $color])
                                            <span class="fc-tipo-badge"
                                                  style="background:{{ $color }}12;color:{{ $color }};border:1px solid {{ $color }}22">
                                                {{ $tipo }}
                                            </span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                {{-- Descripción --}}
                                <div class="fc-field">
                                    <label for="descripcion">Descripción <span style="color:#94a3b8;font-weight:400;text-transform:none;letter-spacing:0">(opcional)</span></label>
                                    <textarea id="descripcion" name="descripcion" rows="3"
                                        placeholder="Describe brevemente el contenido del archivo...">{{ old('descripcion') }}</textarea>
                                    <div class="fc-field-hint">Máximo 500 caracteres.</div>
                                    @error('descripcion')
                                        <div class="fc-field-error">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>

                            <div class="fc-form-footer">
                                <div style="font-size:12px;color:#94a3b8">
                                    @if($carpeta->requiere_aprobacion_subida && !in_array(Auth::user()->rol, ['Superadmin','Aux_QHSE','Admin','Gerente']))
                                        ⏳ Tu archivo quedará pendiente de aprobación
                                    @else
                                        💡 Si el archivo ya existe, se creará una nueva versión
                                    @endif
                                </div>
                                <div style="display:flex;gap:10px">
                                    <a href="{{ route('carpetas.show', $carpeta) }}" class="fc-btn fc-btn-outline">Cancelar</a>
                                    <button type="submit" class="fc-btn fc-btn-primary" id="submitBtn" disabled>
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M9 16h6v-6h4l-7-7-7 7h4zm-4 2h14v2H5z"/>
                                        </svg>
                                        @if($carpeta->requiere_aprobacion_subida && !in_array(Auth::user()->rol, ['Superadmin','Aux_QHSE','Admin','Gerente']))
                                            Solicitar publicación
                                        @else
                                            Subir archivo
                                        @endif
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
    // Colores por extensión (solo los tipos permitidos)
    const extColores = {
        pdf:  { bg: 'rgba(220,38,38,0.1)',   fill: '#dc2626' },
        doc:  { bg: 'rgba(37,99,235,0.1)',   fill: '#2563eb' },
        docx: { bg: 'rgba(37,99,235,0.1)',   fill: '#2563eb' },
        xls:  { bg: 'rgba(5,150,105,0.1)',   fill: '#059669' },
        xlsx: { bg: 'rgba(5,150,105,0.1)',   fill: '#059669' },
    };

    // Extensiones permitidas en el cliente (la validación real es en servidor)
    const extPermitidas = ['pdf', 'doc', 'docx', 'xls', 'xlsx'];

    function handleFile(input) {
        const file = input.files[0];
        if (!file) return;

        const ext = file.name.split('.').pop().toLowerCase();

        // Validación cliente
        if (!extPermitidas.includes(ext)) {
            alert('Solo se permiten archivos PDF, Word (.doc/.docx) y Excel (.xls/.xlsx).');
            input.value = '';
            return;
        }

        const color = extColores[ext] || { bg: 'rgba(100,116,139,0.1)', fill: '#64748b' };

        document.getElementById('prevIcon').style.background = color.bg;
        document.getElementById('prevIcon').style.borderRadius = '10px';
        document.getElementById('prevIcon').style.display = 'flex';
        document.getElementById('prevIcon').style.alignItems = 'center';
        document.getElementById('prevIcon').style.justifyContent = 'center';
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
        if (b >= 1048576)    return (b/1048576).toFixed(1)   + ' MB';
        if (b >= 1024)       return (b/1024).toFixed(1)      + ' KB';
        return b + ' B';
    }

    // Drag & drop
    const dz = document.getElementById('dropzone');
    dz.addEventListener('dragover', e => { e.preventDefault(); dz.classList.add('dragover'); });
    dz.addEventListener('dragleave', () => dz.classList.remove('dragover'));
    dz.addEventListener('drop', e => {
        e.preventDefault();
        dz.classList.remove('dragover');
        if (e.dataTransfer.files.length) {
            document.getElementById('fileInput').files = e.dataTransfer.files;
            handleFile(document.getElementById('fileInput'));
        }
    });
    </script>
</x-app-layout>