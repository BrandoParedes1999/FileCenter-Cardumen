<x-app-layout>
<style>
.pap-wrap  { max-width: 860px; margin: 28px auto; padding: 0 16px; }
.pap-card  { background:#fff; border:1px solid #e2e8f0; border-radius:16px; overflow:hidden;
             box-shadow:0 2px 8px rgba(0,0,0,.05); margin-bottom:24px; }
.pap-header { display:flex; align-items:center; gap:10px; padding:16px 22px;
              border-bottom:1px solid #f1f5f9; }
.pap-htitle { font-size:15px; font-weight:700; color:#1e293b; flex:1; }
.pap-row    { display:flex; align-items:center; gap:12px; padding:12px 22px;
              border-bottom:1px solid #f8fafc; transition:background .1s; }
.pap-row:last-child { border-bottom:none; }
.pap-row:hover { background:#fafbff; }
.pap-name   { font-size:13px; font-weight:600; color:#1e293b; flex:1; min-width:0;
              white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.pap-meta   { font-size:11px; color:#94a3b8; white-space:nowrap; }
.pap-badge  { font-size:10px; font-weight:700; padding:2px 8px; border-radius:99px; }
.dark .pap-card   { background:#13111f; border-color:#1e1b4b; }
.dark .pap-htitle { color:#e0e7ff; }
.dark .pap-name   { color:#e0e7ff; }
</style>

<div class="fc-wrapper">
    @include('components.sidebar')
    <div class="fc-main" style="display:flex;flex-direction:column">
        <header class="fc-topbar">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="#6366f1">
                <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
            </svg>
            <span class="fc-topbar-title">Papelera de reciclaje</span>
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

        <div class="pap-wrap">
            @if(session('success'))
            <div style="padding:10px 16px;background:#d1fae5;border:1px solid #6ee7b7;border-radius:10px;
                        color:#065f46;font-size:13px;margin-bottom:16px">
                {{ session('success') }}
            </div>
            @endif

            @if($archivos->isEmpty() && $carpetas->isEmpty())
            <div style="text-align:center;padding:60px 20px;color:#94a3b8">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="#cbd5e1" style="display:block;margin:0 auto 14px">
                    <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                </svg>
                <div style="font-size:15px;font-weight:600;color:#475569;margin-bottom:6px">Papelera vacía</div>
                <div style="font-size:13px">No hay elementos eliminados en este momento.</div>
            </div>
            @else

            @if(in_array(Auth::user()->rol, ['Superadmin','Aux_QHSE','Admin','Gerente']) && ($archivos->isNotEmpty() || $carpetas->isNotEmpty()))
            <div style="display:flex;justify-content:flex-end;margin-bottom:14px">
                <form method="POST" action="{{ route('papelera.vaciar') }}"
                      onsubmit="return confirm('¿Vaciar toda la papelera? Esta acción no se puede deshacer.')">
                    @csrf @method('DELETE')
                    <button type="submit" class="fc-btn"
                            style="background:rgba(239,68,68,.1);color:#dc2626;border:1px solid rgba(239,68,68,.2);
                                   font-size:12px;padding:6px 14px">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor" style="vertical-align:middle;margin-right:4px">
                            <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                        </svg>
                        Vaciar papelera
                    </button>
                </form>
            </div>
            @endif

            {{-- ARCHIVOS --}}
            @if($archivos->isNotEmpty())
            <div class="pap-card">
                <div class="pap-header">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="#f59e0b">
                        <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6z"/>
                    </svg>
                    <span class="pap-htitle">Archivos eliminados</span>
                    <span class="pap-badge" style="background:rgba(245,158,11,.12);color:#d97706">
                        {{ $archivos->count() }}
                    </span>
                </div>

                @foreach($archivos as $archivo)
                @php $color = $archivo->colorExtension()['color'] ?? '#94a3b8'; @endphp
                <div class="pap-row">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="{{ $color }}">
                        <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6z"/>
                    </svg>
                    <div style="flex:1;min-width:0">
                        <div class="pap-name">{{ $archivo->nombre_original }}</div>
                        <div class="pap-meta">
                            Carpeta: {{ $archivo->carpeta?->nombre ?? '—' }}
                            @if($archivo->carpeta?->empresa)
                                · {{ $archivo->carpeta->empresa->siglas }}
                            @endif
                            · {{ $archivo->tamanioFormateado() }}
                            · Eliminado {{ $archivo->deleted_at?->diffForHumans() }}
                        </div>
                    </div>
                    <div style="display:flex;gap:6px">
                        <form method="POST" action="{{ route('papelera.archivos.restaurar', $archivo->id) }}">
                            @csrf
                            <button type="submit" class="fc-btn fc-btn-outline" style="font-size:11px;padding:4px 12px"
                                    title="Restaurar archivo">
                                ↩ Restaurar
                            </button>
                        </form>
                        @if(in_array(Auth::user()->rol, ['Superadmin','Aux_QHSE','Admin','Gerente']))
                        <form method="POST" action="{{ route('papelera.archivos.destroy', $archivo->id) }}"
                              onsubmit="return confirm('¿Eliminar permanentemente este archivo? No se puede deshacer.')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    style="width:28px;height:28px;border-radius:7px;border:1px solid #fee2e2;
                                           background:#fef2f2;color:#dc2626;cursor:pointer;display:flex;
                                           align-items:center;justify-content:center" title="Eliminar permanentemente">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                                </svg>
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            {{-- CARPETAS --}}
            @if($carpetas->isNotEmpty())
            <div class="pap-card">
                <div class="pap-header">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="#6366f1">
                        <path d="M20 6h-8l-2-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2z"/>
                    </svg>
                    <span class="pap-htitle">Carpetas eliminadas</span>
                    <span class="pap-badge" style="background:rgba(99,102,241,.12);color:#6366f1">
                        {{ $carpetas->count() }}
                    </span>
                </div>

                @foreach($carpetas as $carpeta)
                <div class="pap-row">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="#6366f1">
                        <path d="M20 6h-8l-2-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2z"/>
                    </svg>
                    <div style="flex:1;min-width:0">
                        <div class="pap-name">{{ $carpeta->nombre }}</div>
                        <div class="pap-meta">
                            {{ $carpeta->empresa?->siglas ?? '—' }}
                            · Eliminada {{ $carpeta->deleted_at?->diffForHumans() }}
                        </div>
                    </div>
                    <div style="display:flex;gap:6px">
                        <form method="POST" action="{{ route('papelera.carpetas.restaurar', $carpeta->id) }}">
                            @csrf
                            <button type="submit" class="fc-btn fc-btn-outline" style="font-size:11px;padding:4px 12px">
                                ↩ Restaurar
                            </button>
                        </form>
                        @if(in_array(Auth::user()->rol, ['Superadmin','Aux_QHSE','Admin','Gerente']))
                        <form method="POST" action="{{ route('papelera.carpetas.destroy', $carpeta->id) }}"
                              onsubmit="return confirm('¿Eliminar permanentemente esta carpeta?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    style="width:28px;height:28px;border-radius:7px;border:1px solid #fee2e2;
                                           background:#fef2f2;color:#dc2626;cursor:pointer;display:flex;
                                           align-items:center;justify-content:center">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                                </svg>
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            @endif
        </div>
    </div>
</div>
</x-app-layout>
