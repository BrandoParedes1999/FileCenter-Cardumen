<x-app-layout>
<link rel="stylesheet" href="{{ asset('css/areas.css') }}">
<style>
.view-btn.active         { color: #d97706; }
.btn-nuevo               { background: linear-gradient(135deg, #d97706, #f59e0b); box-shadow: 0 4px 14px rgba(217,119,6,0.35); }
.btn-nuevo:hover         { box-shadow: 0 6px 20px rgba(217,119,6,0.45); }
.fc-topbar-avatar        { background: linear-gradient(135deg, #d97706, #f59e0b); }
.fc-topbar-role          { color: #d97706; }
.area-logo-box           { box-shadow: 0 2px 8px rgba(217,119,6,0.12); }
.breadcrumb a:hover      { color: #d97706; }
.breadcrumb-current      { color: #d97706; }
.carpeta-item.active     { background: #fffbeb; border-left-color: #f59e0b; }
.carpeta-item.active .carpeta-item-icon { color: #f59e0b; }
.carpeta-item.active .carpeta-item-name { color: #d97706; }
.carpeta-item-count      { color: #f59e0b; background: rgba(245,158,11,0.1); }
.area-banner             { background: linear-gradient(135deg, #d97706 0%, #b45309 50%, #f59e0b 100%); }
.carpeta-card:hover      { box-shadow: 0 8px 24px rgba(217,119,6,0.12); border-color: #fde68a; }
.carpeta-card-icon       { background: rgba(245,158,11,0.08); }
.carpeta-action-btn.edit       { color: #f59e0b; }
.carpeta-action-btn.edit:hover { background: rgba(245,158,11,0.1); }
</style>

<div class="fc-wrapper">
    @include('components.sidebar')
    <div class="fc-main">

        <header class="fc-topbar">
            <div class="fc-topbar-left">
                <div class="fc-topbar-title" style="display:flex;align-items:center;gap:10px;">
                    <div class="area-logo-box">
                        <img src="{{ asset('images/The White Shark 1.png') }}" alt="TWS">
                    </div>
                    TWS
                </div>
                <div class="fc-topbar-sub">4 carpetas · 26 archivos</div>
            </div>
            <div class="fc-topbar-right">
                <div class="view-btns">
                    <button class="view-btn active" id="btnGrid" onclick="setView('grid')">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M3 3h7v7H3V3zm0 11h7v7H3v-7zm11-11h7v7h-7V3zm0 11h7v7h-7v-7z"/></svg>
                    </button>
                    <button class="view-btn" id="btnList" onclick="setView('list')">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M3 4h2v2H3V4zm4 0h14v2H7V4zM3 9h2v2H3V9zm4 0h14v2H7V9zm-4 5h2v2H3v-2zm4 0h14v2H7v-2zm-4 5h2v2H3v-2zm4 0h14v2H7v-2z"/></svg>
                    </button>
                </div>
                <a href="#" class="btn-nuevo">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
                    Nuevo
                </a>
                <div class="fc-notif">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="#64748b"><path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/></svg>
                    <div class="fc-notif-badge">2</div>
                </div>
                <div class="fc-topbar-avatar">{{ strtoupper(substr(Auth::user()->nombre, 0, 1)) }}{{ strtoupper(substr(Auth::user()->paterno, 0, 1)) }}</div>
                <div>
                    <div class="fc-topbar-name">{{ Auth::user()->nombre_completo }}</div>
                    <div class="fc-topbar-role">{{ Auth::user()->rol }}</div>
                </div>
            </div>
        </header>

        <div class="fc-body">
            <div class="carpetas-sidebar">
                <div class="carpetas-sidebar-header">
                    <div class="breadcrumb">
                        <a href="{{ route('cardumen') }}"><svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg> Áreas</a>
                        <span class="breadcrumb-sep">›</span>
                        <span class="breadcrumb-current">TWS</span>
                    </div>
                </div>
                <div class="carpetas-label">Carpetas</div>
                @foreach([
                    ['Administración', 5],
                    ['Operaciones', 8],
                    ['Facturación', 4],
                    ['Ventas / Negocios', 9],
                ] as $i => $c)
                <div class="carpeta-item {{ $i === 0 ? 'active' : '' }}" onclick="selectCarpeta(this)">
                    <div class="carpeta-item-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"/></svg></div>
                    <span class="carpeta-item-name">{{ $c[0] }}</span>
                    <span class="carpeta-item-count">{{ $c[1] }}</span>
                </div>
                @endforeach
            </div>

            <div class="fc-content">
                <div class="area-banner">
                    <p class="area-banner-desc">Documentación de la empresa TWS del corporativo Cardumen. Catering y suministros para operaciones offshore.</p>
                    <div class="area-banner-stats">
                        <div><div class="area-banner-stat-num">4</div><div class="area-banner-stat-lbl">Carpetas</div></div>
                        <div><div class="area-banner-stat-num">26</div><div class="area-banner-stat-lbl">Archivos</div></div>
                        <div><div class="area-banner-stat-num">6</div><div class="area-banner-stat-lbl">Miembros</div></div>
                    </div>
                </div>
                <div class="section-title">Selecciona una carpeta<div class="section-title-line"></div></div>
                <div class="carpetas-grid" id="carpetasGrid">
                    @foreach([
                        ['Administración', 5],
                        ['Operaciones', 8],
                        ['Facturación', 4],
                        ['Ventas / Negocios', 9],
                    ] as $c)
                    <div class="carpeta-card">
                        <div class="carpeta-card-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2"><path d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"/></svg></div>
                        <div class="carpeta-card-name">{{ $c[0] }}</div>
                        <div class="carpeta-card-meta">
                            <span class="carpeta-card-archivos">{{ $c[1] }} archivos</span>
                            <span class="carpeta-card-badge badge-abierta">Abierta</span>
                        </div>
                        <div class="carpeta-card-actions">
                            <button class="carpeta-action-btn view"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></button>
                            <button class="carpeta-action-btn edit"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@include('components.area-script')
</x-app-layout>