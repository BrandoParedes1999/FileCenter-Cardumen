<x-app-layout>
<div class="fc-wrapper">
    @include('components.sidebar')

    <div class="fc-main">
        <header class="fc-topbar">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#6366f1">
                <path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
            </svg>
            <span class="fc-topbar-title">Búsqueda</span>
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

            {{-- Formulario de búsqueda --}}
            <form method="GET" action="{{ route('buscar') }}" style="margin-bottom:24px">
                <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">

                    {{-- Input principal --}}
                    <div style="position:relative;flex:1;max-width:560px">
                        <svg style="position:absolute;left:14px;top:50%;transform:translateY(-50%);pointer-events:none"
                             width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2">
                            <circle cx="11" cy="11" r="7"/><path d="m21 21-4.35-4.35"/>
                        </svg>
                        <input
                            type="text"
                            name="q"
                            value="{{ $q }}"
                            placeholder="Buscar archivos y carpetas…"
                            autofocus
                            autocomplete="off"
                            style="width:100%;padding:11px 14px 11px 44px;background:#fff;border:1.5px solid #e2e8f0;border-radius:12px;font-size:14px;color:#1e293b;outline:none;transition:border-color .2s"
                            onfocus="this.style.borderColor='#6366f1'"
                            onblur="this.style.borderColor='#e2e8f0'">
                    </div>

                    {{-- Filtro tipo --}}
                    <div style="display:flex;gap:6px">
                        @foreach(['todo' => 'Todo', 'archivos' => 'Archivos', 'carpetas' => 'Carpetas'] as $val => $label)
                        <a href="{{ route('buscar', ['q' => $q, 'tipo' => $val]) }}"
                           style="padding:8px 14px;border-radius:20px;font-size:12px;font-weight:600;text-decoration:none;transition:all .15s;
                                  {{ $tipo === $val
                                      ? 'background:#4f46e5;color:#fff;border:1px solid #4f46e5'
                                      : 'background:#fff;color:#475569;border:1px solid #e2e8f0' }}">
                            {{ $label }}
                        </a>
                        @endforeach
                    </div>

                    <button type="submit"
                            style="padding:10px 22px;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;border:none;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer">
                        Buscar
                    </button>
                </div>
            </form>

            {{-- Estado vacío / sin query --}}
            @if(!$valido)
            <div style="text-align:center;padding:60px 20px;color:#94a3b8">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="rgba(99,102,241,0.15)" style="margin:0 auto 16px;display:block">
                    <path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                </svg>
                <div style="font-size:15px;font-weight:600;color:#1e293b;margin-bottom:6px">Escribe para buscar</div>
                <div style="font-size:13px">Mínimo {{ App\Http\Controllers\BuscadorController::MIN_CHARS }} caracteres para iniciar la búsqueda</div>
            </div>

            @else

            {{-- Resumen de resultados --}}
            @php
                $total = $archivos->count() + $carpetas->count();
            @endphp
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:18px;font-size:13px;color:#64748b">
                @if($total > 0)
                    <span>
                        <strong style="color:#1e293b">{{ $total }}</strong>
                        resultado{{ $total != 1 ? 's' : '' }} para
                        "<strong style="color:#4f46e5">{{ $q }}</strong>"
                    </span>
                    @if($archivos->count())
                    <span style="background:#f1f5f9;padding:2px 9px;border-radius:20px">
                        {{ $archivos->count() }} archivo{{ $archivos->count()!=1?'s':'' }}
                    </span>
                    @endif
                    @if($carpetas->count())
                    <span style="background:#f1f5f9;padding:2px 9px;border-radius:20px">
                        {{ $carpetas->count() }} carpeta{{ $carpetas->count()!=1?'s':'' }}
                    </span>
                    @endif
                @else
                    <span>Sin resultados para "<strong style="color:#4f46e5">{{ $q }}</strong>"</span>
                @endif
            </div>

            {{-- Sin resultados --}}
            @if($total === 0)
            <div class="fc-empty">
                <div class="fc-empty-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="#a5b4fc">
                        <path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                    </svg>
                </div>
                <div class="fc-empty-title">Sin resultados</div>
                <div class="fc-empty-sub">
                    Intenta con otras palabras o revisa el filtro seleccionado.
                </div>
            </div>

            @else

            {{-- ── ARCHIVOS ── --}}
            @if($archivos->count() && ($tipo === 'todo' || $tipo === 'archivos'))
            <div style="margin-bottom:28px">
                <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;margin-bottom:10px;display:flex;align-items:center;gap:8px">
                    Archivos
                    <span style="background:#f1f5f9;color:#64748b;padding:1px 8px;border-radius:10px;font-weight:600">{{ $archivos->count() }}</span>
                    <div style="flex:1;height:1px;background:#e2e8f0"></div>
                </div>

                <div style="display:flex;flex-direction:column;gap:6px">
                    @foreach($archivos as $archivo)
                    @php
                        $colores = $archivo->colorExtension();
                        $ext     = strtolower($archivo->extension);
                        // Resaltar coincidencia en el nombre
                        $nombreHl = preg_replace_callback(
                            '/(' . preg_quote($q, '/') . ')/i',
                            fn($m) => '<mark style="background:#ede9fe;color:#4f46e5;border-radius:3px;padding:0 2px">' . e($m[1]) . '</mark>',
                            e($archivo->nombre_original)
                        );
                    @endphp
                    <div style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:12px 16px;display:flex;align-items:center;gap:14px;transition:border-color .15s"
                         onmouseover="this.style.borderColor='#c7d2fe'" onmouseout="this.style.borderColor='#e2e8f0'">

                        {{-- Icono tipo --}}
                        <div style="width:38px;height:38px;border-radius:10px;background:{{ $colores['bg'] }};display:flex;align-items:center;justify-content:center;flex-shrink:0">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="{{ $colores['color'] }}">
                                <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6z"/>
                            </svg>
                        </div>

                        {{-- Info --}}
                        <div style="flex:1;min-width:0">
                            <div style="font-size:13px;font-weight:600;color:#1e293b;margin-bottom:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                                {!! $nombreHl !!}
                            </div>
                            <div style="font-size:11px;color:#94a3b8;display:flex;align-items:center;gap:8px;flex-wrap:wrap">
                                <span style="background:{{ $colores['bg'] }};color:{{ $colores['color'] }};padding:1px 6px;border-radius:6px;font-weight:700;font-size:10px">{{ strtoupper($ext) }}</span>
                                <span>{{ $archivo->tamanioFormateado() }}</span>
                                @if($archivo->carpeta)
                                <span>
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor" style="vertical-align:middle;opacity:.5">
                                        <path d="M20 6h-8l-2-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2z"/>
                                    </svg>
                                    {{ $archivo->carpeta->nombre }}
                                    @if($archivo->carpeta->empresa)
                                        · {{ $archivo->carpeta->empresa->siglas }}
                                    @endif
                                </span>
                                @endif
                                @if($archivo->descripcion)
                                <span style="max-width:300px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                                    {{ Str::limit($archivo->descripcion, 60) }}
                                </span>
                                @endif
                            </div>
                        </div>

                        {{-- Acciones --}}
                        <div style="display:flex;gap:6px;flex-shrink:0">
                            <a href="{{ route('archivos.ver', $archivo) }}"
                               style="display:flex;align-items:center;gap:5px;padding:6px 12px;border-radius:8px;border:1px solid #e2e8f0;background:#f8fafc;font-size:12px;color:#475569;text-decoration:none;font-weight:500;white-space:nowrap"
                               title="Ver en línea">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5z"/>
                                </svg>
                                Ver
                            </a>
                            @can('download', $archivo)
                            <a href="{{ route('archivos.descargar', $archivo) }}"
                               style="display:flex;align-items:center;gap:5px;padding:6px 12px;border-radius:8px;border:1px solid rgba(5,150,105,.25);background:rgba(5,150,105,.06);font-size:12px;color:#059669;text-decoration:none;font-weight:500;white-space:nowrap">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/>
                                </svg>
                                Bajar
                            </a>
                            @endcan
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- ── CARPETAS ── --}}
            @if($carpetas->count() && ($tipo === 'todo' || $tipo === 'carpetas'))
            <div>
                <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;margin-bottom:10px;display:flex;align-items:center;gap:8px">
                    Carpetas
                    <span style="background:#f1f5f9;color:#64748b;padding:1px 8px;border-radius:10px;font-weight:600">{{ $carpetas->count() }}</span>
                    <div style="flex:1;height:1px;background:#e2e8f0"></div>
                </div>

                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:10px">
                    @foreach($carpetas as $carpeta)
                    @php
                        $accentCarpeta = $carpeta->empresa->color_secundario ?? '#4f46e5';
                        $nombreHlC = preg_replace_callback(
                            '/(' . preg_quote($q, '/') . ')/i',
                            fn($m) => '<mark style="background:#ede9fe;color:#4f46e5;border-radius:3px;padding:0 2px">' . e($m[1]) . '</mark>',
                            e($carpeta->nombre)
                        );
                    @endphp
                    <a href="{{ route('carpetas.show', $carpeta) }}"
                       style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:14px 16px;text-decoration:none;display:flex;align-items:center;gap:12px;transition:all .15s"
                       onmouseover="this.style.borderColor='#c7d2fe';this.style.transform='translateY(-2px)'"
                       onmouseout="this.style.borderColor='#e2e8f0';this.style.transform=''">
                        <div style="width:36px;height:36px;border-radius:10px;background:{{ $accentCarpeta }}18;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="{{ $accentCarpeta }}">
                                <path d="M20 6h-8l-2-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2z"/>
                            </svg>
                        </div>
                        <div style="min-width:0">
                            <div style="font-size:13px;font-weight:600;color:#1e293b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                                {!! $nombreHlC !!}
                            </div>
                            <div style="font-size:11px;color:#94a3b8;margin-top:2px;display:flex;gap:6px;align-items:center">
                                @if($carpeta->empresa)
                                <span style="background:{{ $accentCarpeta }}15;color:{{ $accentCarpeta }};padding:1px 6px;border-radius:6px;font-weight:600;font-size:10px">
                                    {{ $carpeta->empresa->siglas }}
                                </span>
                                @endif
                                <span style="font-family:monospace;font-size:10px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:150px">
                                    {{ $carpeta->path }}
                                </span>
                            </div>
                        </div>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="#cbd5e1" style="flex-shrink:0;margin-left:auto">
                            <path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6z"/>
                        </svg>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            @endif {{-- /total > 0 --}}
            @endif {{-- /valido --}}

        </div>
    </div>
</div>
</x-app-layout>