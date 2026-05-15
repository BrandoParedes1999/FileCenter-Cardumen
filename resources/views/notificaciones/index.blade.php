<x-app-layout>
<style>
.notif-wrap { max-width: 720px; margin: 28px auto; padding: 0 16px; }
.notif-card { background:#fff; border:1px solid #e2e8f0; border-radius:16px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,.05); }
.notif-header { display:flex; align-items:center; gap:10px; padding:18px 24px; border-bottom:1px solid #f1f5f9; }
.notif-title { font-size:16px; font-weight:700; color:#1e293b; flex:1; }
.notif-item { display:flex; align-items:flex-start; gap:14px; padding:14px 24px; border-bottom:1px solid #f8fafc; transition:background .1s; }
.notif-item:last-child { border-bottom:none; }
.notif-item:hover { background:#fafbff; }
.notif-item.unread { background:rgba(99,102,241,.04); }
.notif-dot { width:8px; height:8px; border-radius:50%; background:#6366f1; flex-shrink:0; margin-top:6px; }
.notif-dot.read { background:#e2e8f0; }
.notif-body { flex:1; min-width:0; }
.notif-ntitle { font-size:13px; font-weight:600; color:#1e293b; }
.notif-msg { font-size:12px; color:#64748b; margin-top:2px; }
.notif-time { font-size:11px; color:#94a3b8; margin-top:4px; }
.notif-actions { display:flex; gap:6px; align-items:center; flex-shrink:0; }
.dark .notif-card { background:#13111f; border-color:#1e1b4b; }
.dark .notif-title { color:#e0e7ff; }
.dark .notif-ntitle { color:#e0e7ff; }
.dark .notif-item.unread { background:rgba(99,102,241,.08); }
</style>

<div class="fc-wrapper">
    @include('components.sidebar')
    <div class="fc-main" style="display:flex;flex-direction:column">
        <header class="fc-topbar">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="#6366f1">
                <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/>
            </svg>
            <span class="fc-topbar-title">Notificaciones</span>
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

        <div class="notif-wrap">
            @if(session('success'))
            <div style="padding:10px 16px;background:#d1fae5;border:1px solid #6ee7b7;border-radius:10px;
                        color:#065f46;font-size:13px;margin-bottom:16px">
                {{ session('success') }}
            </div>
            @endif

            <div class="notif-card">
                <div class="notif-header">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="#6366f1">
                        <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/>
                    </svg>
                    <span class="notif-title">Mis notificaciones</span>
                    @php $noLeidas = Auth::user()->unreadNotifications->count(); @endphp
                    @if($noLeidas > 0)
                    <span style="font-size:11px;font-weight:700;background:rgba(99,102,241,.12);color:#6366f1;
                                 padding:2px 10px;border-radius:20px">{{ $noLeidas }} sin leer</span>
                    <form method="POST" action="{{ route('notificaciones.leer-todas') }}">
                        @csrf
                        <button type="submit" class="fc-btn fc-btn-outline" style="font-size:11px;padding:4px 12px">
                            Marcar todas como leídas
                        </button>
                    </form>
                    @endif
                    @if($notificaciones->total() > 0)
                    <form method="POST" action="{{ route('notificaciones.vaciar') }}">
                        @csrf @method('DELETE')
                        <button type="submit" class="fc-btn" style="font-size:11px;padding:4px 12px;
                               background:rgba(239,68,68,.1);color:#dc2626;border:1px solid rgba(239,68,68,.2)"
                               onclick="return confirm('¿Eliminar todas las notificaciones?')">
                            Vaciar
                        </button>
                    </form>
                    @endif
                </div>

                @forelse($notificaciones as $notif)
                @php
                    $data    = $notif->data;
                    $leida   = !is_null($notif->read_at);
                    $titulo  = $data['titulo']  ?? 'Notificación';
                    $mensaje = $data['mensaje'] ?? '';
                    $url     = $data['url']     ?? null;
                @endphp
                <div class="notif-item {{ $leida ? '' : 'unread' }}">
                    <div class="notif-dot {{ $leida ? 'read' : '' }}"></div>
                    <div class="notif-body">
                        <div class="notif-ntitle">{{ $titulo }}</div>
                        <div class="notif-msg">{{ $mensaje }}</div>
                        <div class="notif-time">{{ $notif->created_at->diffForHumans() }}</div>
                    </div>
                    <div class="notif-actions">
                        @if($url)
                        <form method="POST" action="{{ route('notificaciones.leer', $notif->id) }}">
                            @csrf
                            <button type="submit" title="Ir" class="fc-btn fc-btn-primary" style="font-size:11px;padding:4px 10px">
                                Ver →
                            </button>
                        </form>
                        @elseif(!$leida)
                        <form method="POST" action="{{ route('notificaciones.leer', $notif->id) }}">
                            @csrf
                            <button type="submit" class="fc-btn fc-btn-outline" style="font-size:11px;padding:4px 10px">
                                Marcar leída
                            </button>
                        </form>
                        @endif
                        <form method="POST" action="{{ route('notificaciones.destroy', $notif->id) }}">
                            @csrf @method('DELETE')
                            <button type="submit" title="Eliminar"
                                    style="width:26px;height:26px;border-radius:7px;border:1px solid #fee2e2;
                                           background:#fef2f2;color:#dc2626;cursor:pointer;display:flex;
                                           align-items:center;justify-content:center">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
                @empty
                <div style="padding:40px;text-align:center;color:#94a3b8;font-size:13px">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="#cbd5e1" style="display:block;margin:0 auto 12px">
                        <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/>
                    </svg>
                    Sin notificaciones
                </div>
                @endforelse
            </div>

            {{ $notificaciones->links() }}
        </div>
    </div>
</div>
</x-app-layout>
