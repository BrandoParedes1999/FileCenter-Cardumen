<x-app-layout>
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

.fc-wrapper {
    display: flex; height: 100dvh; width: 100%;
    background: #f8fafc; color: #1e293b;
    font-family: 'Segoe UI', system-ui, sans-serif;
    overflow: hidden;
}


/* ══ MAIN ══ */
.fc-main { flex: 1; display: flex; flex-direction: column; height: 100dvh; overflow: hidden; min-width: 0; }

.fc-topbar {
    height: 48px; background: #fff;
    border-bottom: 1px solid #e2e8f0;
    display: flex; align-items: center;
    padding: 0 28px; flex-shrink: 0; gap: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.06);
}
.fc-topbar-title {
    font-size: 12px; font-weight: 700; color: #4f46e5;
    text-transform: uppercase; letter-spacing: .15em;
}
.fc-topbar-sep { width: 1px; height: 16px; background: #e2e8f0; }
.fc-topbar-sub { font-size: 11px; color: #94a3b8; }
.fc-topbar-right { display: flex; align-items: center; gap: 14px; margin-left: auto; }
.fc-notif {
    position: relative; cursor: pointer; width: 30px; height: 30px;
    display: flex; align-items: center; justify-content: center;
}
.fc-notif-badge {
    position: absolute; top: 1px; right: 1px; width: 14px; height: 14px;
    background: #ef4444; border-radius: 50%; font-size: 8px;
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-weight: 700;
}
.fc-topbar-avatar {
    width: 32px; height: 32px;
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
    border-radius: 50%; display: flex; align-items: center;
    justify-content: center; font-size: 11px; font-weight: 700; color: #fff;
}
.fc-topbar-name { font-size: 12px; font-weight: 600; color: #1e293b; }
.fc-topbar-role { font-size: 10px; color: #7c3aed; }

.fc-content {
    flex: 1; overflow-y: auto; padding: 24px 28px;
    scrollbar-width: thin; scrollbar-color: #e2e8f0 transparent;
    background: #f8fafc;
}

/* ══ SLIDER ══ */
.slider-wrap {
    position: relative; border-radius: 18px; overflow: hidden;
    margin-bottom: 36px; height: 260px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 8px 32px rgba(79,70,229,0.12);
}
.slider-track {
    display: flex; height: 100%;
    transition: transform .6s cubic-bezier(.4,0,.2,1);
}
.slide { min-width: 100%; height: 100%; position: relative; flex-shrink: 0; }
.slide-img { width: 100%; height: 100%; object-fit: cover; display: block; }
.slide-overlay {
    position: absolute; inset: 0;
    background: linear-gradient(90deg, rgba(0,0,0,.68) 0%, rgba(0,0,0,.25) 60%, transparent 100%);
}
.slide-content {
    position: absolute; inset: 0;
    display: flex; flex-direction: column; justify-content: center;
    padding: 0 48px;
}
.slide-tag {
    font-size: 10px; font-weight: 700; letter-spacing: .15em;
    text-transform: uppercase; color: rgba(255,255,255,.6);
    margin-bottom: 10px; display: flex; align-items: center; gap: 8px;
}
.slide-tag::before { content: ''; width: 20px; height: 1px; background: rgba(255,255,255,.4); }
.slide-title { font-size: 28px; font-weight: 700; color: #fff; line-height: 1.2; margin-bottom: 10px; text-shadow: 0 2px 12px rgba(0,0,0,.4); }
.slide-sub { font-size: 13px; color: rgba(255,255,255,.75); line-height: 1.6; max-width: 460px; }
.slider-btn {
    position: absolute; top: 50%; transform: translateY(-50%);
    width: 38px; height: 38px; border-radius: 50%;
    background: rgba(255,255,255,.15); border: 1px solid rgba(255,255,255,.3);
    color: #fff; font-size: 18px; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: all .2s; z-index: 10; backdrop-filter: blur(4px);
}
.slider-btn:hover { background: rgba(255,255,255,.3); }
.slider-prev { left: 16px; }
.slider-next { right: 16px; }
.slider-dots {
    position: absolute; bottom: 16px; left: 50%; transform: translateX(-50%);
    display: flex; gap: 6px; z-index: 10;
}
.s-dot {
    height: 4px; width: 20px; border-radius: 2px;
    background: rgba(255,255,255,.35); cursor: pointer; transition: all .3s;
}
.s-dot.active { background: #fff; width: 32px; }
.slider-counter {
    position: absolute; bottom: 16px; right: 20px;
    font-size: 11px; color: rgba(255,255,255,.5);
    font-weight: 600; z-index: 10;
}

/* ══ ETIQUETA SECCIÓN ══ */
.section-label { display: flex; align-items: center; gap: 12px; margin-bottom: 22px; }
.section-label-text {
    font-size: 10px; font-weight: 700; color: #7c3aed;
    text-transform: uppercase; letter-spacing: .12em; white-space: nowrap;
}
.section-label-line { flex: 1; height: 1px; background: #e2e8f0; }

/* ══ MISIÓN / VISIÓN / VALORES ══ */
.grid-mvv {
    display: grid; grid-template-columns: repeat(3, 1fr);
    gap: 18px; margin-bottom: 32px; align-items: start;
}

.mvv-card {
    background: #fff; border: 1px solid #e2e8f0;
    border-radius: 18px; overflow: hidden;
    transition: border-color .2s, transform .2s, box-shadow .2s;
    position: relative;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
}
.mvv-card:hover {
    border-color: #c7d2fe;
    transform: translateY(-3px);
    box-shadow: 0 12px 32px rgba(79,70,229,0.12);
}

.mvv-card-img {
    width: 100%; height: 160px; object-fit: cover;
    display: block; filter: brightness(.9);
    transition: filter .3s;
}
.mvv-card:hover .mvv-card-img { filter: brightness(1); }

.mvv-card-overlay {
    position: absolute; top: 0; left: 0; right: 0; height: 160px;
    pointer-events: none;
}
.mvv-card.mision  .mvv-card-overlay { background: linear-gradient(180deg, rgba(124,58,237,0.25) 0%, transparent 100%); }
.mvv-card.vision  .mvv-card-overlay { background: linear-gradient(180deg, rgba(8,145,178,0.25) 0%, transparent 100%); }
.mvv-card.valores .mvv-card-overlay { background: linear-gradient(180deg, rgba(5,150,105,0.25) 0%, transparent 100%); }

.mvv-accent { height: 3px; }
.mvv-card.mision  .mvv-accent { background: linear-gradient(90deg,#7c3aed,#4f46e5); }
.mvv-card.vision  .mvv-accent { background: linear-gradient(90deg,#0891b2,#06b6d4); }
.mvv-card.valores .mvv-accent { background: linear-gradient(90deg,#059669,#10b981); }

.mvv-body { padding: 24px 26px 28px; }
.mvv-header { display: flex; align-items: center; gap: 12px; margin-bottom: 14px; }
.mvv-icon { width: 40px; height: 40px; border-radius: 11px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.mvv-title { font-size: 17px; font-weight: 700; color: #1e293b; }
.mvv-sub   { font-size: 10px; color: #94a3b8; text-transform: uppercase; letter-spacing: .08em; margin-top: 2px; }
.mvv-text  { font-size: 13px; color: #475569; line-height: 1.8; }

.valor-row { display: flex; align-items: flex-start; gap: 12px; padding: 12px 0; border-bottom: 1px solid #f1f5f9; }
.valor-row:last-child { border-bottom: none; padding-bottom: 0; }
.valor-ico { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 14px; }
.valor-name { font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 2px; }
.valor-desc { font-size: 12px; color: #64748b; line-height: 1.5; }

/* ══ EMPRESAS ══ */
.grid-empresas { display: grid; grid-template-columns: repeat(4,1fr); gap: 14px; margin-bottom: 0; }
.emp-card {
    background: #fff; border: 1px solid #e2e8f0;
    border-radius: 16px; overflow: hidden; cursor: pointer;
    text-decoration: none; transition: all .25s; display: block;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}
.emp-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 16px 40px rgba(79,70,229,0.15);
    border-color: #c7d2fe;
}
.emp-stripe { height: 4px; }
.emp-img-wrap {
    height: 110px; display: flex; align-items: center;
    justify-content: center; background: #f8fafc;
    padding: 20px; border-bottom: 1px solid #f1f5f9; overflow: hidden;
}
.emp-img-wrap img {
    max-height: 70px; max-width: 100%; object-fit: contain;
    filter: brightness(.95); transition: filter .2s, transform .2s;
}
.emp-card:hover .emp-img-wrap img { filter: brightness(1); transform: scale(1.04); }
.emp-body { padding: 16px; }
.emp-name { font-size: 14px; font-weight: 700; color: #1e293b; margin-bottom: 3px; }
.emp-desc { font-size: 11px; color: #64748b; line-height: 1.4; }
.emp-footer {
    padding: 10px 16px; border-top: 1px solid #f1f5f9;
    display: flex; align-items: center; justify-content: space-between;
}
.emp-tag { font-size: 10px; font-weight: 600; letter-spacing: .06em; text-transform: uppercase; padding: 3px 9px; border-radius: 20px; }
.emp-arrow { font-size: 14px; color: #cbd5e1; transition: color .2s; }
.emp-card:hover .emp-arrow { color: #7c3aed; }

/* ══ FOOTER ══ */
.nos-footer { margin-top: 36px; border-top: 1px solid #e2e8f0; padding-top: 32px; padding-bottom: 8px; }
.nos-footer-inner { display: grid; grid-template-columns: 1.4fr 1fr 1fr; gap: 36px; margin-bottom: 28px; }

.footer-brand-logo {
    width: 52px; height: 52px; border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 14px; overflow: hidden;
    background: #f1f5f9; border: 1px solid #e2e8f0;
}
.footer-brand-logo img { width: 100%; height: 100%; object-fit: contain; }
.footer-brand-name { font-size: 15px; font-weight: 700; color: #1e293b; margin-bottom: 6px; }
.footer-brand-desc { font-size: 12px; color: #64748b; line-height: 1.75; max-width: 220px; }

.footer-col-title {
    font-size: 10px; font-weight: 700; color: #94a3b8;
    text-transform: uppercase; letter-spacing: .12em;
    margin-bottom: 14px; display: flex; align-items: center; gap: 8px;
}
.footer-col-title::after { content: ''; flex: 1; height: 1px; background: #e2e8f0; }

.footer-socials { display: flex; flex-direction: column; gap: 8px; }
.footer-social-link {
    display: flex; align-items: center; gap: 10px;
    text-decoration: none; padding: 10px 14px;
    border-radius: 10px; border: 1px solid #e2e8f0;
    background: #fff; transition: border-color .2s, background .2s, box-shadow .2s;
}
.footer-social-link:hover {
    border-color: #c7d2fe;
    background: #f5f3ff;
    box-shadow: 0 4px 12px rgba(124,58,237,0.08);
}
.footer-social-icon { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.footer-social-name   { font-size: 13px; font-weight: 600; color: #374151; }
.footer-social-handle { font-size: 11px; color: #9ca3af; margin-top: 1px; }

.footer-address-items { display: flex; flex-direction: column; gap: 8px; }
.footer-address-item {
    display: flex; align-items: flex-start; gap: 10px;
    padding: 11px 13px; border-radius: 10px;
    border: 1px solid #e2e8f0; background: #fff;
    transition: border-color .2s, box-shadow .2s;
}
.footer-address-item:hover {
    border-color: #c7d2fe;
    box-shadow: 0 4px 12px rgba(124,58,237,0.07);
}
.footer-address-icon { width: 30px; height: 30px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.footer-address-label { font-size: 9px; color: #94a3b8; text-transform: uppercase; letter-spacing: .09em; margin-bottom: 2px; font-weight: 600; }
.footer-address-value { font-size: 12px; color: #475569; line-height: 1.5; }

.footer-bottom {
    display: flex; align-items: center; justify-content: space-between;
    padding-top: 20px; border-top: 1px solid #e2e8f0;
}
.footer-copy { font-size: 11px; color: #94a3b8; }
.footer-copy span { color: #475569; font-weight: 600; }
.footer-badge {
    font-size: 10px; font-weight: 600; color: #7c3aed;
    background: #f5f3ff; border: 1px solid #ddd6fe;
    padding: 4px 10px; border-radius: 20px;
    letter-spacing: .06em; text-transform: uppercase;
}

/* ══ MODAL ══ */
.modal-overlay {
    display: none; position: fixed; inset: 0;
    background: rgba(15,23,42,0.6); z-index: 200;
    align-items: center; justify-content: center;
    backdrop-filter: blur(6px);
}
.modal-overlay.open { display: flex; }
.modal-box {
    background: #fff; border: 1px solid #e2e8f0;
    border-radius: 20px; width: 520px; max-width: 92vw;
    overflow: hidden; position: relative;
    box-shadow: 0 40px 80px rgba(0,0,0,0.2);
}
.modal-header-img {
    height: 140px; background: #f8fafc;
    display: flex; align-items: center; justify-content: center;
    position: relative; overflow: hidden; border-bottom: 1px solid #e2e8f0;
}
.modal-header-img img { max-height: 90px; max-width: 70%; object-fit: contain; }
.modal-stripe { position: absolute; bottom: 0; left: 0; right: 0; height: 3px; }
.modal-close {
    position: absolute; top: 12px; right: 12px;
    width: 28px; height: 28px; border-radius: 7px;
    background: #f1f5f9; border: 1px solid #e2e8f0;
    color: #94a3b8; font-size: 14px; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: all .15s; z-index: 10;
}
.modal-close:hover { background: #fee2e2; border-color: #fca5a5; color: #ef4444; }
.modal-body { padding: 24px 28px; }
.modal-company-name { font-size: 20px; font-weight: 700; color: #1e293b; margin-bottom: 2px; }
.modal-company-sub  { font-size: 12px; color: #94a3b8; margin-bottom: 16px; }
.modal-divider { height: 1px; background: #f1f5f9; margin-bottom: 16px; }
.modal-text { font-size: 13px; color: #475569; line-height: 1.8; margin-bottom: 20px; }
.modal-stats { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
.modal-stat { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px 14px; }
.modal-stat-label { font-size: 9px; color: #94a3b8; text-transform: uppercase; letter-spacing: .1em; margin-bottom: 4px; font-weight: 600; }
.modal-stat-value { font-size: 15px; font-weight: 700; color: #1e293b; }
</style>

<div class="fc-wrapper">

    {{-- ══ SIDEBAR ══ --}}
@include('components.sidebar')

    {{-- ══ MAIN ══ --}}
    <div class="fc-main">

        <header class="fc-topbar">
            <div class="fc-topbar-title">Nosotros</div>
            <div class="fc-topbar-sep"></div>
            <div class="fc-topbar-sub">Cardumen · Sistema QHSE</div>
            <div class="fc-topbar-right">
                <div class="fc-notif">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="#94a3b8">
                        <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/>
                    </svg>
                    <div class="fc-notif-badge">2</div>
                </div>
                <div class="fc-topbar-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</div>
                <div>
                    <div class="fc-topbar-name">{{ Auth::user()->name }}</div>
                    <div class="fc-topbar-role">Super Admin</div>
                </div>
            </div>
        </header>

        <div class="fc-content">

            {{-- SLIDER --}}
            <div class="slider-wrap">
                <div class="slider-track" id="sliderTrack">
                    <div class="slide">
                        <img class="slide-img" src="{{ asset('images/Barco.png') }}" alt="Cardumen">
                        <div class="slide-overlay"></div>
                        <div class="slide-content">
                            <div class="slide-tag">Cardumen · Grupo empresarial</div>
                            <div class="slide-title">¿Quiénes somos?</div>
                            <div class="slide-sub">Grupo marítimo e industrial comprometido con la excelencia operativa y la gestión documental de calidad.</div>
                        </div>
                    </div>
                    <div class="slide">
                        <img class="slide-img" src="{{ asset('images/Carpetas.jpg') }}" alt="Misión">
                        <div class="slide-overlay"></div>
                        <div class="slide-content">
                            <div class="slide-tag">Nuestra misión</div>
                            <div class="slide-title">Gestión documental<br>segura y centralizada</div>
                            <div class="slide-sub">Facilitamos el acceso, control y trazabilidad de la información crítica de todas las áreas operativas.</div>
                        </div>
                    </div>
                    <div class="slide">
                        <img class="slide-img" src="{{ asset('images/QHSE.png') }}" alt="Visión">
                        <div class="slide-overlay"></div>
                        <div class="slide-content">
                            <div class="slide-tag">Nuestra visión</div>
                            <div class="slide-title">Referentes en QHSE<br>en el sector marítimo</div>
                            <div class="slide-sub">Ser el sistema de gestión documental de referencia, reconocido por confiabilidad e innovación.</div>
                        </div>
                    </div>
                    <div class="slide">
                        <img class="slide-img" src="{{ asset('images/Collage.png') }}" alt="Nuestro Cardumen">
                        <div class="slide-overlay"></div>
                        <div class="slide-content">
                            <div class="slide-tag">Nuestro Cardumen</div>
                            <div class="slide-title">4 empresas,<br>un solo grupo</div>
                            <div class="slide-sub">Seaward Logistic, Seatools, The White Shark y OMC forman el grupo Cardumen.</div>
                        </div>
                    </div>
                </div>
                <button class="slider-btn slider-prev" onclick="slideMove(-1)">‹</button>
                <button class="slider-btn slider-next" onclick="slideMove(1)">›</button>
                <div class="slider-dots" id="sliderDots">
                    <div class="s-dot active" onclick="slideTo(0)"></div>
                    <div class="s-dot" onclick="slideTo(1)"></div>
                    <div class="s-dot" onclick="slideTo(2)"></div>
                    <div class="s-dot" onclick="slideTo(3)"></div>
                </div>
                <div class="slider-counter" id="sliderCounter">1 / 4</div>
            </div>

            {{-- MISIÓN / VISIÓN / VALORES --}}
            <div class="section-label">
                <div class="section-label-text">Filosofía corporativa</div>
                <div class="section-label-line"></div>
            </div>

            <div class="grid-mvv">
                <div class="mvv-card mision">
                    <img class="mvv-card-img" src="{{ asset('images/Mision.png') }}" alt="Misión">
                    <div class="mvv-card-overlay"></div>
                    <div class="mvv-accent"></div>
                    <div class="mvv-body">
                        <div class="mvv-header">
                            <div class="mvv-icon" style="background:rgba(124,58,237,0.1)">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="#7c3aed">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11zm3.5 6.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="mvv-title">Misión</div>
                                <div class="mvv-sub">¿Por qué existimos?</div>
                            </div>
                        </div>
                        <p class="mvv-text">Proveer una plataforma segura, eficiente y centralizada para la gestión documental del sistema QHSE, facilitando el acceso, control y trazabilidad de la información crítica de la organización Cardumen y sus áreas operativas, impulsando la mejora continua en cada proceso.</p>
                    </div>
                </div>

                <div class="mvv-card vision">
                    <img class="mvv-card-img" src="{{ asset('images/Vision.png') }}" alt="Visión">
                    <div class="mvv-card-overlay"></div>
                    <div class="mvv-accent"></div>
                    <div class="mvv-body">
                        <div class="mvv-header">
                            <div class="mvv-icon" style="background:rgba(8,145,178,0.1)">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="#0891b2">
                                    <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="mvv-title">Visión</div>
                                <div class="mvv-sub">¿A dónde vamos?</div>
                            </div>
                        </div>
                        <p class="mvv-text">Ser el sistema de referencia en gestión documental QHSE dentro del sector marítimo e industrial, reconocido por su confiabilidad, trazabilidad e innovación, contribuyendo a la excelencia operativa de todas las áreas de Cardumen a nivel nacional.</p>
                    </div>
                </div>

                <div class="mvv-card valores">
                    <img class="mvv-card-img" src="{{ asset('images/Valores.jpg') }}" alt="Valores">
                    <div class="mvv-card-overlay"></div>
                    <div class="mvv-accent"></div>
                    <div class="mvv-body">
                        <div class="mvv-header">
                            <div class="mvv-icon" style="background:rgba(5,150,105,0.1)">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="#059669">
                                    <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="mvv-title">Valores</div>
                                <div class="mvv-sub">¿Cómo lo hacemos?</div>
                            </div>
                        </div>
                        <div class="valor-row">
                            <div class="valor-ico" style="background:rgba(124,58,237,0.08)">🔒</div>
                            <div><div class="valor-name">Seguridad</div><div class="valor-desc">Protección total de la información y los datos.</div></div>
                        </div>
                        <div class="valor-row">
                            <div class="valor-ico" style="background:rgba(8,145,178,0.08)">📋</div>
                            <div><div class="valor-name">Trazabilidad</div><div class="valor-desc">Registro completo de cada acción en el sistema.</div></div>
                        </div>
                        <div class="valor-row">
                            <div class="valor-ico" style="background:rgba(5,150,105,0.08)">🤝</div>
                            <div><div class="valor-name">Colaboración</div><div class="valor-desc">Trabajo conjunto entre todas las áreas.</div></div>
                        </div>
                        <div class="valor-row">
                            <div class="valor-ico" style="background:rgba(217,119,6,0.08)">⭐</div>
                            <div><div class="valor-name">Excelencia</div><div class="valor-desc">Mejora continua en todos los procesos.</div></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- NUESTRO CARDUMEN --}}
            <div class="section-label">
                <div class="section-label-text">Nuestro Cardumen</div>
                <div class="section-label-line"></div>
            </div>
            <div class="grid-empresas">
                <div class="emp-card" onclick="openModal('seaward')">
                    <div class="emp-stripe" style="background:#06b6d4"></div>
                    <div class="emp-img-wrap"><img src="{{ asset('images/Seaward-Logistic-Logo-.png') }}" alt="Seaward"></div>
                    <div class="emp-body">
                        <div class="emp-name">Seaward Logistic</div>
                        <div class="emp-desc">Servicios logísticos para la industria Oil & Gas</div>
                    </div>
                    <div class="emp-footer">
                        <span class="emp-tag" style="background:rgba(6,182,212,0.1);color:#0891b2">Oil & Gas</span>
                        <span class="emp-arrow">→</span>
                    </div>
                </div>
                <div class="emp-card" onclick="openModal('seatools')">
                    <div class="emp-stripe" style="background:#f97316"></div>
                    <div class="emp-img-wrap"><img src="{{ asset('images/Seatools-Original.png') }}" alt="Seatools"></div>
                    <div class="emp-body">
                        <div class="emp-name">Seatools</div>
                        <div class="emp-desc">Equipamiento y maquinaria industrial de alto rendimiento</div>
                    </div>
                    <div class="emp-footer">
                        <span class="emp-tag" style="background:rgba(249,115,22,0.1);color:#ea580c">Industrial</span>
                        <span class="emp-arrow">→</span>
                    </div>
                </div>
                <div class="emp-card" onclick="openModal('tws')">
                    <div class="emp-stripe" style="background:#f59e0b"></div>
                    <div class="emp-img-wrap"><img src="{{ asset('images/The White Shark 1.png') }}" alt="The White Shark"></div>
                    <div class="emp-body">
                        <div class="emp-name">The White Shark</div>
                        <div class="emp-desc">Catering y suministros para operaciones offshore</div>
                    </div>
                    <div class="emp-footer">
                        <span class="emp-tag" style="background:rgba(245,158,11,0.1);color:#d97706">Catering</span>
                        <span class="emp-arrow">→</span>
                    </div>
                </div>
                <div class="emp-card" onclick="openModal('omc')">
                    <div class="emp-stripe" style="background:#22c55e"></div>
                    <div class="emp-img-wrap"><img src="{{ asset('images/Original OMC.png') }}" alt="OMC"></div>
                    <div class="emp-body">
                        <div class="emp-name">OMC</div>
                        <div class="emp-desc">Agencia naviera y gestión de trámites portuarios</div>
                    </div>
                    <div class="emp-footer">
                        <span class="emp-tag" style="background:rgba(34,197,94,0.1);color:#16a34a">Shipping</span>
                        <span class="emp-arrow">→</span>
                    </div>
                </div>
            </div>

            {{-- PIE DE PÁGINA --}}
            <div class="nos-footer">
                <div class="nos-footer-inner">

                    <div>
                        <div class="footer-brand-logo">
                            <img src="{{ asset('images/logo.png') }}" alt="Logo Cardumen">
                        </div>
                        <div class="footer-brand-name">Cardumen</div>
                        <div class="footer-brand-desc">
                            Grupo empresarial mexicano del sector marítimo e industrial,
                            integrado por Seaward Logistic, Seatools, The White Shark y OMC.
                            Comprometidos con la excelencia operativa y la gestión documental de calidad.
                        </div>
                    </div>

                    <div>
                        <div class="footer-col-title">Síguenos</div>
                        <div class="footer-socials">
                            <a href="#" class="footer-social-link">
                                <div class="footer-social-icon" style="background:rgba(59,130,246,0.1)">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="#2563eb">
                                        <path d="M22 12c0-5.52-4.48-10-10-10S2 6.48 2 12c0 4.84 3.44 8.87 8 9.8V15H8v-3h2V9.5C10 7.57 11.57 6 13.5 6H16v3h-2c-.55 0-1 .45-1 1v2h3v3h-3v6.95c5.05-.5 9-4.76 9-9.95z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="footer-social-name">Facebook</div>
                                    <div class="footer-social-handle">@CardumenGroup</div>
                                </div>
                            </a>
                            <a href="#" class="footer-social-link">
                                <div class="footer-social-icon" style="background:rgba(219,39,119,0.1)">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="#db2777">
                                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="footer-social-name">Instagram</div>
                                    <div class="footer-social-handle">@CardumenGroup</div>
                                </div>
                            </a>
                        </div>
                    </div>

                    <div>
                        <div class="footer-col-title">Contacto</div>
                        <div class="footer-address-items">
                            <div class="footer-address-item">
                                <div class="footer-address-icon" style="background:rgba(124,58,237,0.08)">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="#7c3aed">
                                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="footer-address-label">Dirección</div>
                                    <div class="footer-address-value">Av. Periférico Carlos Pellicer<br>Cárdenas, Villahermosa, Tab.</div>
                                </div>
                            </div>
                            <div class="footer-address-item">
                                <div class="footer-address-icon" style="background:rgba(8,145,178,0.08)">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="#0891b2">
                                        <path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="footer-address-label">Correo</div>
                                    <div class="footer-address-value">contacto@cardumen.com.mx</div>
                                </div>
                            </div>
                            <div class="footer-address-item">
                                <div class="footer-address-icon" style="background:rgba(5,150,105,0.08)">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="#059669">
                                        <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="footer-address-label">Teléfono</div>
                                    <div class="footer-address-value">+52 (993) 123 4567</div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="footer-bottom">
                    <div class="footer-copy">
                        © {{ date('Y') }} <span>Cardumen</span> · Todos los derechos reservados
                    </div>
                    <div class="footer-badge">Sistema QHSE v1.0</div>
                </div>
            </div>

        </div>{{-- /fc-content --}}
    </div>{{-- /fc-main --}}
</div>{{-- /fc-wrapper --}}

{{-- MODALES --}}
<div class="modal-overlay" id="modal-seaward" onclick="closeOnOverlay(event,'seaward')">
    <div class="modal-box">
        <div class="modal-header-img">
            <img src="{{ asset('images/Seaward-Logistic-Logo-.png') }}" alt="Seaward">
            <button class="modal-close" onclick="closeModal('seaward')">✕</button>
            <div class="modal-stripe" style="background:#06b6d4"></div>
        </div>
        <div class="modal-body">
            <div class="modal-company-name">Seaward Logistic</div>
            <div class="modal-company-sub">Oil & Gas Services</div>
            <div class="modal-divider"></div>
            <p class="modal-text">Seaward Logistic es la empresa del grupo especializada en servicios logísticos para la industria de Oil & Gas. Ofrece soluciones integrales de transporte marítimo, gestión de carga y operaciones portuarias, garantizando eficiencia y seguridad en cada operación.</p>
            <div class="modal-stats">
                <div class="modal-stat"><div class="modal-stat-label">Sector</div><div class="modal-stat-value" style="font-size:13px;color:#0891b2">Oil & Gas</div></div>
                <div class="modal-stat"><div class="modal-stat-label">Estado</div><div class="modal-stat-value" style="color:#16a34a;font-size:13px">Activo</div></div>
            </div>
        </div>
    </div>
</div>

<div class="modal-overlay" id="modal-seatools" onclick="closeOnOverlay(event,'seatools')">
    <div class="modal-box">
        <div class="modal-header-img">
            <img src="{{ asset('images/Seatools-Original.png') }}" alt="Seatools">
            <button class="modal-close" onclick="closeModal('seatools')">✕</button>
            <div class="modal-stripe" style="background:#f97316"></div>
        </div>
        <div class="modal-body">
            <div class="modal-company-name">Seatools</div>
            <div class="modal-company-sub">Industrial Equipment</div>
            <div class="modal-divider"></div>
            <p class="modal-text">Seatools es la división de equipamiento industrial del grupo Cardumen. Se especializa en la provisión, mantenimiento y renta de herramientas y maquinaria industrial de alto rendimiento para el sector marítimo e industrial.</p>
            <div class="modal-stats">
                <div class="modal-stat"><div class="modal-stat-label">Sector</div><div class="modal-stat-value" style="font-size:13px;color:#ea580c">Industrial</div></div>
                <div class="modal-stat"><div class="modal-stat-label">Estado</div><div class="modal-stat-value" style="color:#16a34a;font-size:13px">Activo</div></div>
            </div>
        </div>
    </div>
</div>

<div class="modal-overlay" id="modal-tws" onclick="closeOnOverlay(event,'tws')">
    <div class="modal-box">
        <div class="modal-header-img">
            <img src="{{ asset('images/The White Shark 1.png') }}" alt="The White Shark">
            <button class="modal-close" onclick="closeModal('tws')">✕</button>
            <div class="modal-stripe" style="background:#f59e0b"></div>
        </div>
        <div class="modal-body">
            <div class="modal-company-name">The White Shark</div>
            <div class="modal-company-sub">Catering & Supplies</div>
            <div class="modal-divider"></div>
            <p class="modal-text">The White Shark es la empresa de catering y suministros del grupo Cardumen. Brinda servicios de alimentación, abastecimiento y logística de consumibles para plataformas, embarcaciones y operaciones offshore con altos estándares de calidad.</p>
            <div class="modal-stats">
                <div class="modal-stat"><div class="modal-stat-label">Sector</div><div class="modal-stat-value" style="font-size:13px;color:#d97706">Catering</div></div>
                <div class="modal-stat"><div class="modal-stat-label">Estado</div><div class="modal-stat-value" style="color:#16a34a;font-size:13px">Activo</div></div>
            </div>
        </div>
    </div>
</div>

<div class="modal-overlay" id="modal-omc" onclick="closeOnOverlay(event,'omc')">
    <div class="modal-box">
        <div class="modal-header-img">
            <img src="{{ asset('images/Original OMC.png') }}" alt="OMC">
            <button class="modal-close" onclick="closeModal('omc')">✕</button>
            <div class="modal-stripe" style="background:#22c55e"></div>
        </div>
        <div class="modal-body">
            <div class="modal-company-name">OMC</div>
            <div class="modal-company-sub">Shipping Agency</div>
            <div class="modal-divider"></div>
            <p class="modal-text">OMC es la agencia naviera del grupo Cardumen. Gestiona los trámites de arribo, despacho y estadía de embarcaciones en puertos mexicanos, coordinando con autoridades portuarias, aduanas y clientes para garantizar operaciones marítimas fluidas y seguras.</p>
            <div class="modal-stats">
                <div class="modal-stat"><div class="modal-stat-label">Sector</div><div class="modal-stat-value" style="font-size:13px;color:#16a34a">Shipping</div></div>
                <div class="modal-stat"><div class="modal-stat-label">Estado</div><div class="modal-stat-value" style="color:#16a34a;font-size:13px">Activo</div></div>
            </div>
        </div>
    </div>
</div>

<script>
let cur = 0;
const total = 4;
function slideTo(i) {
    cur = i;
    document.getElementById('sliderTrack').style.transform = 'translateX(-' + (i * 100) + '%)';
    document.querySelectorAll('.s-dot').forEach((d, idx) => d.classList.toggle('active', idx === i));
    document.getElementById('sliderCounter').textContent = (i + 1) + ' / ' + total;
}
function slideMove(dir) {
    let n = cur + dir;
    if (n < 0) n = total - 1;
    if (n >= total) n = 0;
    slideTo(n);
}
setInterval(() => slideMove(1), 6000);
function openModal(id)  { document.getElementById('modal-' + id).classList.add('open'); }
function closeModal(id) { document.getElementById('modal-' + id).classList.remove('open'); }
function closeOnOverlay(e, id) { if (e.target === document.getElementById('modal-' + id)) closeModal(id); }
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') document.querySelectorAll('.modal-overlay.open').forEach(m => m.classList.remove('open'));
});
</script>

</x-app-layout>