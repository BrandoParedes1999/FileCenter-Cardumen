<!DOCTYPE html>
    <html lang="es">
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>{{ $archivo->nombre_original }} — Visor</title>
            <style>
                *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
                body { font-family: 'Segoe UI', system-ui, sans-serif; background: #f1f5f9; color: #1e293b; height: 100dvh; display: flex; flex-direction: column; overflow: hidden; }

                .visor-topbar { height: 54px; background: #fff; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; padding: 0 18px; gap: 10px; flex-shrink: 0; z-index: 10; }
                .visor-back { display: flex; align-items: center; gap: 6px; color: #6366f1; text-decoration: none; font-size: 13px; font-weight: 600; padding: 6px 12px; border: 1px solid rgba(99,102,241,.25); border-radius: 8px; background: rgba(99,102,241,.05); white-space: nowrap; flex-shrink: 0; }
                .visor-back:hover { background: rgba(99,102,241,.12); }
                .visor-sep { width: 1px; height: 24px; background: #e2e8f0; flex-shrink: 0; }
                .visor-file-icon { width: 30px; height: 30px; border-radius: 7px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
                .visor-file-name { font-size: 13px; font-weight: 600; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 280px; }
                .visor-file-meta { font-size: 11px; color: #94a3b8; white-space: nowrap; }

                .pdf-controls { display: none; align-items: center; gap: 6px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 5px 10px; font-size: 12px; color: #475569; }
                .pdf-controls.visible { display: flex; }
                .pdf-btn { width: 26px; height: 26px; border: none; background: none; font-size: 16px; cursor: pointer; color: #475569; border-radius: 5px; display: flex; align-items: center; justify-content: center; }
                .pdf-btn:hover { background: #e2e8f0; }
                .pdf-btn:disabled { opacity: .35; cursor: not-allowed; }

                .zoom-controls { display: none; align-items: center; gap: 5px; }
                .zoom-controls.visible { display: flex; }
                .zoom-btn { width: 26px; height: 26px; border: 1px solid #e2e8f0; background: #fff; font-size: 14px; cursor: pointer; color: #475569; border-radius: 5px; display: flex; align-items: center; justify-content: center; }
                .zoom-btn:hover { background: #f1f5f9; }
                #zoomLabel { font-size: 12px; color: #475569; min-width: 38px; text-align: center; }

                .visor-badge { font-size: 10px; font-weight: 700; padding: 3px 9px; border-radius: 20px; background: rgba(99,102,241,.1); color: #4f46e5; white-space: nowrap; flex-shrink: 0; }
                .visor-actions { margin-left: auto; display: flex; gap: 8px; align-items: center; }
                .visor-dl-btn { display: flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 600; padding: 7px 14px; border-radius: 8px; border: none; cursor: pointer; text-decoration: none; background: linear-gradient(135deg, #059669, #10b981); color: #fff; }
                .visor-dl-btn:hover { opacity: .88; }

                .visor-body { flex: 1; overflow: hidden; position: relative; }

                /* PDF — canvas rendering */
                .visor-pdf-scroll { width: 100%; height: 100%; overflow-y: auto; display: flex; flex-direction: column; align-items: center; gap: 10px; padding: 20px 16px; background: #4b5563; user-select: none; }
                .pdf-page-canvas { box-shadow: 0 4px 20px rgba(0,0,0,.4); background: #fff; display: block; max-width: 100%; }

                /* DOCX */
                .visor-doc-wrap { width: 100%; height: 100%; overflow-y: auto; display: flex; justify-content: center; padding: 28px 16px; background: #f1f5f9; }
                .visor-doc-paper { background: #fff; width: 100%; max-width: 860px; min-height: 200px; border-radius: 12px; padding: 48px 56px; box-shadow: 0 4px 24px rgba(0,0,0,.08); font-family: Georgia, serif; font-size: 14px; line-height: 1.8; color: #1e293b; }
                .visor-doc-paper h1 { font-size: 22px; margin: 24px 0 12px; font-family: 'Segoe UI', sans-serif; }
                .visor-doc-paper h2 { font-size: 18px; margin: 20px 0 10px; font-family: 'Segoe UI', sans-serif; }
                .visor-doc-paper h3 { font-size: 15px; margin: 16px 0 8px; font-family: 'Segoe UI', sans-serif; }
                .visor-doc-paper p  { margin-bottom: 12px; }
                .visor-doc-paper table { border-collapse: collapse; width: 100%; margin: 16px 0; font-size: 13px; font-family: 'Segoe UI', sans-serif; }
                .visor-doc-paper td, .visor-doc-paper th { border: 1px solid #e2e8f0; padding: 8px 12px; }
                .visor-doc-paper th { background: #f8fafc; font-weight: 600; }
                .visor-doc-paper tr:nth-child(even) td { background: #fafafa; }
                .visor-doc-paper img { max-width: 100%; height: auto; }
                .visor-doc-paper ul, .visor-doc-paper ol { padding-left: 24px; margin-bottom: 12px; }

                /* Excel */
                .visor-excel-wrap { display: flex; flex-direction: column; height: 100%; }
                .visor-sheets { display: flex; gap: 4px; padding: 10px 16px 0; background: #4b5563; flex-wrap: wrap; }
                .visor-sheet-tab { padding: 5px 14px; border-radius: 8px 8px 0 0; border: 1px solid rgba(255,255,255,.2); border-bottom: none; font-size: 12px; font-weight: 600; cursor: pointer; background: rgba(255,255,255,.15); color: #fff; }
                .visor-sheet-tab.active { background: #fff; color: #4f46e5; }
                .visor-excel-scroll { flex: 1; overflow: auto; padding: 0 16px 24px; background: #4b5563; }
                .visor-excel-paper { background: #fff; border-radius: 0 8px 8px 8px; box-shadow: 0 2px 12px rgba(0,0,0,.15); overflow: auto; }
                .visor-excel-paper table { border-collapse: collapse; font-size: 12px; font-family: 'Segoe UI', sans-serif; }
                .visor-excel-paper td, .visor-excel-paper th { border: 1px solid #e2e8f0; padding: 5px 10px; white-space: nowrap; min-width: 80px; }
                .visor-excel-paper th { background: #f8fafc; font-weight: 600; color: #475569; }
                .visor-excel-paper tr:nth-child(even) td { background: #fafafa; }

                .visor-loading { display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; gap: 14px; color: #64748b; font-size: 14px; }
                .visor-spinner { width: 36px; height: 36px; border: 3px solid #e2e8f0; border-top-color: #6366f1; border-radius: 50%; animation: spin .7s linear infinite; }
                @keyframes spin { to { transform: rotate(360deg); } }
                .visor-error { display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; gap: 12px; color: #64748b; font-size: 14px; text-align: center; padding: 20px; }
                .visor-error-title { font-size: 15px; font-weight: 600; color: #1e293b; }
            </style>
        </head>
    <body>

    @php
        $ext            = strtolower($archivo->extension);
        $colores        = $archivo->colorExtension();
        $esSoloLect     = $archivo->carpeta->esSoloLectura();
        $puedeDescargar = Auth::user()->can('download', $archivo);
    @endphp

    <div class="visor-topbar">
        <a href="{{ route('archivos.show', $archivo) }}" class="visor-back">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg>
            Volver
        </a>
        <div class="visor-sep"></div>
            <div class="visor-file-icon" style="background:{{ $colores['bg'] }}">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="{{ $colores['color'] }}"><path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6z"/></svg>
            </div>
            <div style="min-width:0;flex:1">
                <div class="visor-file-name">{{ $archivo->nombre_original }}</div>
                <div class="visor-file-meta">{{ $archivo->tamanioFormateado() }} &middot; {{ strtoupper($ext) }}@if($esSoloLect) &middot; <span style="color:#4f46e5">Solo lectura</span>@endif</div>
            </div>

            {{-- Controles PDF --}}
            <div class="pdf-controls" id="pdfControls">
                <button class="pdf-btn" id="btnPrev" onclick="cambiarPagina(-1)" disabled>‹</button>
                <span id="pdfPageInfo" style="white-space:nowrap">1 / 1</span>
                <button class="pdf-btn" id="btnNext" onclick="cambiarPagina(1)">›</button>
            </div>
            <div class="zoom-controls" id="zoomControls">
                <button class="zoom-btn" onclick="ajustarZoom(-0.2)">−</button>
                <span id="zoomLabel">130%</span>
                <button class="zoom-btn" onclick="ajustarZoom(+0.2)">+</button>
            </div>

        @if($esSoloLect && !$puedeDescargar)
        <span class="visor-badge">Solo lectura</span>
        @endif

        <div class="visor-actions">
            @if($puedeDescargar)
            <a href="{{ route('archivos.descargar', $archivo) }}" class="visor-dl-btn">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/></svg>
                Descargar
            </a>
            @endif
        </div>
    </div>

    <div class="visor-body" id="visorBody">
        <div class="visor-loading">
            <div class="visor-spinner"></div>
            <span>Cargando documento…</span>
        </div>
    </div>

    <script>
    const CONTENIDO_URL = '{{ route('archivos.contenido', $archivo) }}';
    const EXT           = '{{ $ext }}';
    const BODY          = document.getElementById('visorBody');
    let pdfDoc = null, pdfPagActual = 1, pdfZoom = 1.3;

    async function iniciarVisor() {
        try {
            if (EXT === 'pdf')                    await mostrarPDF();
            else if (EXT === 'docx' || EXT === 'doc') await mostrarDocx();
            else if (EXT === 'xlsx' || EXT === 'xls') await mostrarExcel();
            else mostrarError('Tipo de archivo no soportado para vista previa.');
        } catch(e) {
            console.error(e);
            mostrarError(e.message || 'Error al cargar el documento.');
        }
    }

    /* ── PDF: renderizado canvas via PDF.js — el navegador nunca ve un iframe PDF ── */
    async function mostrarPDF() {
        await cargarScript('https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js');
        pdfjsLib.GlobalWorkerOptions.workerSrc =
            'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

        const resp = await fetch(CONTENIDO_URL, { credentials: 'same-origin' });
        if (!resp.ok) throw new Error('El servidor respondió ' + resp.status);
        const buffer = await resp.arrayBuffer();

        pdfDoc = await pdfjsLib.getDocument({ data: buffer }).promise;
        const total = pdfDoc.numPages;

        document.getElementById('pdfControls').classList.add('visible');
        document.getElementById('zoomControls').classList.add('visible');
        actualizarControlesPagina(1, total);

        BODY.innerHTML = '<div class="visor-pdf-scroll" id="pdfScroll"></div>';
        const scroll = document.getElementById('pdfScroll');

        for (let n = 1; n <= total; n++) {
            const canvas = await renderPagina(n, pdfZoom);
            canvas.addEventListener('contextmenu', e => e.preventDefault());
            scroll.appendChild(canvas);
        }

        // Actualizar número de página al hacer scroll
        scroll.addEventListener('scroll', () => {
            for (let n = 1; n <= total; n++) {
                const c = document.getElementById('pdfPage_' + n);
                if (!c) continue;
                const rect = c.getBoundingClientRect();
                if (rect.top >= 0 && rect.top < window.innerHeight * 0.6) {
                    if (n !== pdfPagActual) actualizarControlesPagina(n, total);
                    break;
                }
            }
        }, { passive: true });
    }

    async function renderPagina(n, escala) {
        const page     = await pdfDoc.getPage(n);
        const viewport = page.getViewport({ scale: escala });
        const canvas   = document.createElement('canvas');
        canvas.id      = 'pdfPage_' + n;
        canvas.className = 'pdf-page-canvas';
        canvas.width   = viewport.width;
        canvas.height  = viewport.height;
        await page.render({ canvasContext: canvas.getContext('2d'), viewport }).promise;
        return canvas;
    }

    function actualizarControlesPagina(actual, total) {
        pdfPagActual = actual;
        document.getElementById('pdfPageInfo').textContent = actual + ' / ' + total;
        document.getElementById('btnPrev').disabled = actual <= 1;
        document.getElementById('btnNext').disabled = actual >= total;
    }

    function cambiarPagina(delta) {
        if (!pdfDoc) return;
        const nueva = pdfPagActual + delta;
        if (nueva < 1 || nueva > pdfDoc.numPages) return;
        const c = document.getElementById('pdfPage_' + nueva);
        if (c) c.scrollIntoView({ behavior: 'smooth', block: 'start' });
        actualizarControlesPagina(nueva, pdfDoc.numPages);
    }

    async function ajustarZoom(delta) {
        if (!pdfDoc) return;
        pdfZoom = Math.max(0.5, Math.min(3.0, +(pdfZoom + delta).toFixed(1)));
        document.getElementById('zoomLabel').textContent = Math.round(pdfZoom * 100) + '%';
        const scroll = document.getElementById('pdfScroll');
        if (!scroll) return;
        scroll.innerHTML = '';
        for (let n = 1; n <= pdfDoc.numPages; n++) {
            const canvas = await renderPagina(n, pdfZoom);
            canvas.addEventListener('contextmenu', e => e.preventDefault());
            scroll.appendChild(canvas);
        }
    }

    /* ── DOCX: mammoth.js → HTML ── */
    async function mostrarDocx() {
        await cargarScript('https://cdnjs.cloudflare.com/ajax/libs/mammoth/1.6.0/mammoth.browser.min.js');
        const resp = await fetch(CONTENIDO_URL, { credentials: 'same-origin' });
        if (!resp.ok) throw new Error('HTTP ' + resp.status);
        const result = await mammoth.convertToHtml({ arrayBuffer: await resp.arrayBuffer() });
        BODY.innerHTML = '<div class="visor-doc-wrap"><div class="visor-doc-paper" id="docContent"></div></div>';
        document.getElementById('docContent').innerHTML = result.value || '<p style="color:#94a3b8">El documento está vacío.</p>';
    }

    /* ── XLSX/XLS: SheetJS → tabla HTML ── */
    async function mostrarExcel() {
        await cargarScript('https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js');
        const resp   = await fetch(CONTENIDO_URL, { credentials: 'same-origin' });
        if (!resp.ok) throw new Error('HTTP ' + resp.status);
        const wb     = XLSX.read(await resp.arrayBuffer(), { type: 'array' });
        const names  = wb.SheetNames;

        const tabs  = names.map((n,i) => `<button class="visor-sheet-tab${i===0?' active':''}" onclick="cambiarHoja(${i})">${esc(n)}</button>`).join('');
        const hojas = names.map((n,i) => `<div id="hoja_${i}" class="visor-excel-paper"${i===0?'':' style="display:none"'}>${XLSX.utils.sheet_to_html(wb.Sheets[n],{editable:false})}</div>`).join('');

        BODY.innerHTML = `<div class="visor-excel-wrap"><div class="visor-sheets">${tabs}</div><div class="visor-excel-scroll">${hojas}</div></div>`;
    }

    function cambiarHoja(idx) {
        document.querySelectorAll('[id^="hoja_"]').forEach((el,i) => el.style.display = i===idx?'':'none');
        document.querySelectorAll('.visor-sheet-tab').forEach((el,i) => el.classList.toggle('active',i===idx));
    }

    function mostrarError(msg) {
        BODY.innerHTML = `<div class="visor-error"><div class="visor-error-title">No se pudo mostrar el documento</div><div style="font-size:13px;color:#94a3b8;max-width:340px">${esc(msg)}</div></div>`;
    }

    function esc(s) { return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

    function cargarScript(src) {
        return new Promise((ok, err) => {
            if (document.querySelector(`script[src="${src}"]`)) { ok(); return; }
            const s = document.createElement('script');
            s.src = src; s.onload = ok; s.onerror = () => err(new Error('No se cargó: ' + src));
            document.head.appendChild(s);
        });
    }

    iniciarVisor();
    </script>
    </body>
</html>