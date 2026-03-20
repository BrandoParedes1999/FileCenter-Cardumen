<aside class="fc-sidebar">
    <div class="fc-logo-area">
        <div class="fc-logo-wrap">
            <img src="{{ asset('images/logo.png') }}" style="width:40px;height:40px;object-fit:contain;">
            <div>
                <div class="fc-logo-text">FileCenter Cardumen</div>
                <div class="fc-logo-sub">Sistema QHSE</div>
            </div>
        </div>
    </div>

    {{-- ── Principal ── --}}
    <div class="fc-nav-section">
        <div class="fc-nav-label">Principal</div>
        <a href="{{ route('dashboard') }}" class="fc-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
            Dashboard
        </a>
        <a href="{{ route('carpetas.index') }}" class="fc-nav-item {{ request()->routeIs('carpetas.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M20 6h-8l-2-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2z"/></svg>
            Mis Carpetas
        </a>
        <a href="{{ route('solicitudes.index') }}" class="fc-nav-item {{ request()->routeIs('solicitudes.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M4 6h16v2H4V6zm0 5h16v2H4v-2zm0 5h16v2H4v-2z"/></svg>
            Solicitudes
        </a>
        <a href="{{ route('areas') }}" class="fc-nav-item {{ request()->routeIs('areas') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M10 3H3v7h7V3zm11 0h-7v7h7V3zM10 14H3v7h7v-7zm11 3h-7v4h7v-4z"/>
            </svg>
            Mis Áreas
        </a>
    </div>

    {{-- ── Corporativo ── --}}
    <div class="fc-nav-section">
        <div class="fc-nav-label">Corporativo</div>
        <a href="{{ route('cardumen') }}" class="fc-nav-item {{ request()->routeIs('cardumen') ? 'active' : '' }}">
            <div class="fc-dot" style="background:#7c3aed"></div>
            Cardumen
        </a>
        <a href="{{ route('nosotros') }}" class="fc-nav-item {{ request()->routeIs('nosotros') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="currentColor">
                <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
            </svg>
            Nosotros
        </a>
    </div>

    {{-- ── Mis Áreas ── --}}
    <div class="fc-nav-section">
        <div class="fc-nav-label">Mis Áreas</div>
        <a href="{{ route('omc') }}" class="fc-nav-item {{ request()->routeIs('omc') ? 'active' : '' }}">
            <div class="fc-dot" style="background:#22c55e"></div> OMC
        </a>
        <a href="{{ route('seaward') }}" class="fc-nav-item {{ request()->routeIs('seaward') ? 'active' : '' }}">
            <div class="fc-dot" style="background:#38bdf8"></div> Seaward
        </a>
        <a href="{{ route('seatools') }}" class="fc-nav-item {{ request()->routeIs('seatools') ? 'active' : '' }}">
            <div class="fc-dot" style="background:#f97316"></div> Seatools
        </a>
        <a href="{{ route('tws') }}" class="fc-nav-item {{ request()->routeIs('tws') ? 'active' : '' }}">
            <div class="fc-dot" style="background:#fbbf24"></div> TWS
        </a>
    </div>

    {{-- ── Administración (solo roles con acceso) ── --}}
    @if(in_array(Auth::user()->rol, ['Superadmin', 'Aux_QHSE', 'Admin', 'Gerente']))
    <div class="fc-nav-section">
        <div class="fc-nav-label">Administración</div>
        <a href="{{ route('usuarios.index') }}" class="fc-nav-item {{ request()->routeIs('usuarios') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
            Usuarios
        </a>
        <a href="#" class="fc-nav-item">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/></svg>
            Permisos
        </a>
        <a href="#" class="fc-nav-item">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
            Reportes
        </a>
    </div>
    @endif

    {{-- ── Footer del sidebar ── --}}
    <div class="fc-sidebar-footer">

        {{-- Botón modo oscuro ── --}}
        <button id="darkToggle" onclick="toggleDarkMode()" style="
            width: 100%;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border-radius: 8px;
            border: none;
            background: rgba(99,102,241,0.08);
            color: #a5b4fc;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            margin-bottom: 12px;
            transition: background .15s;
        ">
            {{-- Ícono Sol --}}
            <svg id="iconSol" width="15" height="15" viewBox="0 0 24 24"
                 fill="none" stroke="#f59e0b" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="5"/>
                <line x1="12" y1="1"  x2="12" y2="3"/>
                <line x1="12" y1="21" x2="12" y2="23"/>
                <line x1="4.22" y1="4.22"   x2="5.64"  y2="5.64"/>
                <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
                <line x1="1"  y1="12" x2="3"  y2="12"/>
                <line x1="21" y1="12" x2="23" y2="12"/>
                <line x1="4.22"  y1="19.78" x2="5.64"  y2="18.36"/>
                <line x1="18.36" y1="5.64"  x2="19.78" y2="4.22"/>
            </svg>

            {{-- Ícono Luna --}}
            <svg id="iconLuna" width="15" height="15" viewBox="0 0 24 24"
                 fill="none" stroke="#6366f1" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round"
                 style="display:none">
                <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
            </svg>

            <span id="textoTema">Modo Oscuro</span>
        </button>

        {{-- Info del usuario ── --}}
        <div class="fc-user-info">
            <div class="fc-avatar">
                {{ strtoupper(substr(Auth::user()->nombre, 0, 1)) }}{{ strtoupper(substr(Auth::user()->paterno, 0, 1)) }}
            </div>
            <div style="flex:1;min-width:0">
                <div class="fc-user-name">{{ Auth::user()->nombre_completo }}</div>
                <div class="fc-user-role">{{ Auth::user()->email }}</div>
                <div class="fc-badge-role">{{ Auth::user()->rol }}</div>
            </div>
        </div>

        {{-- Cerrar sesión ── --}}
        <form method="POST" action="{{ route('logout') }}" style="margin-top:10px">
            @csrf
            <button type="submit" class="fc-nav-item"
                style="width:100%;background:none;border:none;text-align:left;color:#6366f1;font-size:12px;cursor:pointer">
                <svg viewBox="0 0 24 24" fill="currentColor" style="width:14px;height:14px">
                    <path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/>
                </svg>
                Cerrar Sesión
            </button>
        </form>

    </div>
</aside>

        @if(in_array(Auth::user()->rol, ['Superadmin', 'Aux_QHSE', 'Admin', 'Gerente']))
        <div class="fc-nav-section">
            <div class="fc-nav-label">Administración</div>
            <a href="#" class="fc-nav-item">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/></svg>
                usuarios
            </a>
            <a href="#" class="fc-nav-item">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/></svg>
                Permisos
            </a>
            <a href="#" class="fc-nav-item">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
                Reportes
            </a>
        </div>
        @endif

{{-- Estilos modo oscuro para el sidebar y páginas --}}
<style>
/* ── Sidebar oscuro ── */
.dark .fc-sidebar        { background: #0f0e2a; border-color: #1e1b4b; }
.dark .fc-logo-text      { color: #e0e7ff; }
.dark .fc-logo-sub       { color: #6366f1; }
.dark .fc-logo-area      { border-color: #1e1b4b; }
.dark .fc-nav-label      { color: #4f46e5; }
.dark .fc-nav-item       { color: #a5b4fc; }
.dark .fc-nav-item:hover { background: #1e1b4b; color: #e0e7ff; }
.dark .fc-nav-item.active { background: #4f46e5; color: #fff; }
.dark .fc-nav-section    { border-color: #1e1b4b; }
.dark .fc-sidebar-footer { border-color: #1e1b4b; }
.dark .fc-user-name      { color: #e0e7ff; }
.dark .fc-user-role      { color: #6366f1; }
.dark .fc-badge-role     { background: rgba(99,102,241,0.2); color: #a5b4fc; }

/* ── Topbar oscuro ── */
.dark .fc-topbar         { background: #1e1b4b; border-color: #2d2a5e; }
.dark .fc-topbar-title   { color: #e0e7ff; }
.dark .fc-topbar-sub     { color: #6366f1; }
.dark .fc-topbar-name    { color: #e0e7ff; }

/* ── Contenido oscuro ── */
.dark .fc-main           { background: #0f0e2a; }
.dark .fc-content        { background: #0f0e2a; }

/* ── Inputs y filtros oscuros ── */
.dark .search-input      { background: #1e1b4b; border-color: #2d2a5e; color: #e0e7ff; }
.dark .filter-select     { background: #1e1b4b; border-color: #2d2a5e; color: #a5b4fc; }

/* ── Tabla oscura ── */
.dark .tabla-container   { background: #1e1b4b; border-color: #2d2a5e; }
.dark .tabla-cabecera    { background: #16154a; border-color: #2d2a5e; }
.dark .tabla-fila        { border-color: #2d2a5e; }
.dark .tabla-fila:hover  { background: #16154a; }
.dark .nombre-usuario    { color: #e0e7ff; }
.dark .ultimo-acceso     { color: #a5b4fc; }

/* ── Cards de stats oscuras ── */
.dark .stat-card         { background: #1e1b4b; border-color: #2d2a5e; }
.dark .stat-texto        { color: #a5b4fc; }

/* ── Carpetas sidebar oscuro ── */
.dark .carpetas-sidebar          { background: #1e1b4b; border-color: #2d2a5e; }
.dark .carpetas-sidebar-header   { border-color: #2d2a5e; }
.dark .carpeta-item:hover        { background: #2d2a5e; }
.dark .carpeta-item-name         { color: #a5b4fc; }
.dark .carpeta-card              { background: #1e1b4b; border-color: #2d2a5e; }
.dark .carpeta-card-name         { color: #e0e7ff; }
.dark .carpeta-card-archivos     { color: #6366f1; }
.dark .section-title             { color: #e0e7ff; }
.dark .fc-body                   { background: #0f0e2a; }

/* ── Áreas grid oscura ── */
.dark .areas-grid .area-card         { background: #1e1b4b; border-color: #2d2a5e; }
.dark .area-name                     { color: #e0e7ff; }
.dark .area-desc                     { color: #a5b4fc; }
.dark .area-stat-num                 { color: #e0e7ff; }
.dark .area-stat-lbl                 { color: #6366f1; }
.dark .area-folders                  { border-color: #2d2a5e; }
.dark .area-folder-name              { color: #a5b4fc; }
.dark .filter-tab                    { background: #1e1b4b; border-color: #2d2a5e; color: #a5b4fc; }
</style>