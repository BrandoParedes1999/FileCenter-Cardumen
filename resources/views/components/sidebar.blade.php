    <aside class="fc-sidebar">
        <div class="fc-logo-area">
            <div class="fc-logo-wrap">
            <img src="{{ asset('images/logo.png') }}"  style="width: 40px; height: 40px; object-fit: contain;">
                <div>
                    <div class="fc-logo-text">FileCenter Cardumen</div>
                    <div class="fc-logo-sub">Sistema QHSE</div>
                </div>
            </div>
        </div>

        <div class="fc-nav-section">
            <div class="fc-nav-label">Principal</div>
            <a href="{{ route('dashboard') }}" class="fc-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
                Dashboard
            </a>
            <a href="#" class="fc-nav-item">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M4 6h16v2H4V6zm0 5h16v2H4v-2zm0 5h16v2H4v-2z"/></svg>
                Áreas
            </a>
            <a href="#" class="fc-nav-item">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M20 6h-8l-2-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2z"/></svg>
                Mis Carpetas
            </a>
        </div>

        <div class="fc-nav-section">
            <div class="fc-nav-label">Corporativo</div>
            <a href="#" class="fc-nav-item">
                <div class="fc-dot" style="background:#a5b4fc"></div>Cardumen
            </a>
            <a href="{{ route('nosotros') }}" class="fc-nav-item {{ request()->routeIs('nosotros') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                </svg>
                Nosotros
            </a>
        </div>

        <div class="fc-nav-section">
            <div class="fc-nav-label">Mis Áreas</div>
            <a href="#" class="fc-nav-item"><div class="fc-dot" style="background:#a78bfa"></div> OMC</a>
            <a href="#" class="fc-nav-item"><div class="fc-dot" style="background:#38bdf8"></div> Seaward</a>
            <a href="#" class="fc-nav-item"><div class="fc-dot" style="background:#34d399"></div> Seatools</a>
            <a href="#" class="fc-nav-item"><div class="fc-dot" style="background:#fbbf24"></div> TWS</a>
        </div>

        <div class="fc-nav-section">
            <div class="fc-nav-label">Administración</div>
            <a href="#" class="fc-nav-item">
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
            <a href="#" class="fc-nav-item">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19.14 12.94c.04-.3.06-.61.06-.94 0-.32-.02-.64-.07-.94l2.03-1.58c.18-.14.23-.41.12-.61l-1.92-3.32c-.12-.22-.37-.29-.59-.22l-2.39.96c-.5-.38-1.03-.7-1.62-.94l-.36-2.54c-.04-.24-.24-.41-.48-.41h-3.84c-.24 0-.43.17-.47.41l-.36 2.54c-.59.24-1.13.57-1.62.94l-2.39-.96c-.22-.08-.47 0-.59.22L2.74 8.87c-.12.21-.08.47.12.61l2.03 1.58c-.05.3-.09.63-.09.94s.02.64.07.94l-2.03 1.58c-.18.14-.23.41-.12.61l1.92 3.32c.12.22.37.29.59.22l2.39-.96c.5.38 1.03.7 1.62.94l.36 2.54c.05.24.24.41.48.41h3.84c.24 0 .44-.17.47-.41l.36-2.54c.59-.24 1.13-.56 1.62-.94l2.39.96c.22.08.47 0 .59-.22l1.92-3.32c.12-.22.07-.47-.12-.61l-2.01-1.58zM12 15.6c-1.98 0-3.6-1.62-3.6-3.6s1.62-3.6 3.6-3.6 3.6 1.62 3.6 3.6-1.62 3.6-3.6 3.6z"/></svg>
                Configuración
            </a>
        </div>

        <div class="fc-sidebar-footer">
            <div class="fc-user-info">
                <div class="fc-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</div>
                <div style="flex:1;min-width:0">
                    <div class="fc-user-name">{{ Auth::user()->name }}</div>
                    <div class="fc-user-role">{{ Auth::user()->email }}</div>
                    <div class="fc-badge-role">Super Admin</div>
                </div>
            </div>
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