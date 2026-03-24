<x-app-layout>
<div class="fc-wrapper">

    @include('components.sidebar')

    <div class="fc-main">
        <header class="fc-topbar">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#6366f1">
                <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/>
            </svg>
            <span class="fc-topbar-title">Permisos — {{ $carpeta->nombre }}</span>
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
                <a href="{{ route('carpetas.index') }}" class="fc-bread-item">📁 Carpetas</a>
                <span class="fc-bread-sep">›</span>
                <a href="{{ route('carpetas.show', $carpeta) }}" class="fc-bread-item">{{ $carpeta->nombre }}</a>
                <span class="fc-bread-sep">›</span>
                <span class="fc-bread-current">Permisos</span>
            </div>

            @if(session('success'))
            <div class="fc-flash success">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="#059669"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                {{ session('success') }}
            </div>
            @endif
            @if(session('error') || $errors->has('error'))
            <div class="fc-flash error">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="#dc2626"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                {{ session('error') ?? $errors->first('error') }}
            </div>
            @endif

            {{-- Header --}}
            <div class="fc-page-header">
                <div>
                    <h1 class="fc-page-title">Permisos de acceso</h1>
                    <div style="font-size:13px;color:var(--fc-text-muted);margin-top:2px">
                        Carpeta: <strong style="color:var(--fc-text)">{{ $carpeta->nombre }}</strong>
                        · Ruta: <code style="font-size:11px;color:#64748b">{{ $carpeta->path }}</code>
                    </div>
                </div>
                <a href="{{ route('carpetas.show', $carpeta) }}" class="fc-btn fc-btn-outline">
                    ← Volver a carpeta
                </a>
            </div>

            {{-- Banner visibilidad carpeta --}}
            <div style="display:flex;align-items:center;gap:14px;padding:14px 18px;
                        background:{{ $carpeta->es_publico ? 'rgba(5,150,105,.06)' : 'rgba(245,158,11,.06)' }};
                        border:1px solid {{ $carpeta->es_publico ? 'rgba(5,150,105,.2)' : 'rgba(245,158,11,.2)' }};
                        border-radius:12px;margin-bottom:24px">
                <svg width="18" height="18" viewBox="0 0 24 24"
                     fill="{{ $carpeta->es_publico ? '#059669' : '#d97706' }}">
                    @if($carpeta->es_publico)
                    <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                    @else
                    <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/>
                    @endif
                </svg>
                <div style="flex:1">
                    <span style="font-size:13px;font-weight:600;color:{{ $carpeta->es_publico ? '#059669' : '#d97706' }}">
                        Carpeta {{ $carpeta->es_publico ? 'pública' : 'privada' }}
                    </span>
                    <span style="font-size:12px;color:var(--fc-text-muted);margin-left:8px">
                        {{ $carpeta->es_publico
                            ? 'Todos los usuarios de la empresa tienen lectura. Los permisos individuales otorgan capacidades adicionales.'
                            : 'Solo los usuarios con permiso explícito pueden acceder.' }}
                    </span>
                </div>
                <a href="{{ route('carpetas.edit', $carpeta) }}" class="fc-btn fc-btn-outline fc-btn-sm">
                    Cambiar visibilidad
                </a>
            </div>

            {{-- Layout dos columnas --}}
            <div class="fc-content-cols">

                {{-- ══ LISTA DE PERMISOS ACTUALES ══ --}}
                <div class="fc-col-main">

                    {{-- Tabs: Por usuario / Por rol --}}
                    @php
                        $permisoUsuario = $permisos->whereNotNull('usuario_id');
                        $permisoRol     = $permisos->whereNull('usuario_id');
                    @endphp

                    {{-- Permisos por usuario --}}
                    <div class="fc-card" style="margin-bottom:20px">
                        <div class="fc-card-header">
                            <div class="fc-card-title" style="margin:0;border:none;padding:0;display:flex;align-items:center;gap:8px">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="#6366f1"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                                Permisos por usuario
                                <span style="font-size:11px;background:#f1f5f9;color:#64748b;padding:2px 8px;border-radius:20px;font-weight:600">
                                    {{ $permisoUsuario->count() }}
                                </span>
                            </div>
                        </div>

                        @if($permisoUsuario->count())
                        <div style="overflow-x:auto;border-top:1px solid var(--fc-border)">
                            <table class="fc-table" style="min-width:600px">
                                <thead>
                                    <tr>
                                        <th>Usuario</th>
                                        <th style="text-align:center" title="Leer">👁 Leer</th>
                                        <th style="text-align:center" title="Subir">⬆ Subir</th>
                                        <th style="text-align:center" title="Editar">✏ Editar</th>
                                        <th style="text-align:center" title="Borrar">🗑 Borrar</th>
                                        <th style="text-align:center" title="Descargar">⬇ Descargar</th>
                                        <th style="text-align:center" title="Heredar subcarpetas">↳ Heredar</th>
                                        <th style="text-align:center">Otorgado por</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($permisoUsuario as $p)
                                    <tr>
                                        <td>
                                            <div class="fc-user-cell">
                                                <div class="fc-user-avatar" style="width:30px;height:30px;font-size:10px">
                                                    {{ strtoupper(substr($p->usuario->nombre ?? '?',0,1)) }}{{ strtoupper(substr($p->usuario->paterno ?? '',0,1)) }}
                                                </div>
                                                <div>
                                                    <div class="fc-user-nombre" style="font-size:12px">{{ $p->usuario->nombre_completo ?? '—' }}</div>
                                                    <div class="fc-user-email">{{ $p->usuario->rol ?? '' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        @foreach(['puede_leer','puede_subir','puede_editar','puede_borrar','puede_descargar','heredar'] as $cap)
                                        <td style="text-align:center">
                                            <form method="POST" action="{{ route('permisos.update', [$carpeta, $p]) }}" style="display:inline">
                                                @csrf @method('PUT')
                                                @foreach(['puede_leer','puede_subir','puede_editar','puede_borrar','puede_descargar','heredar'] as $c)
                                                    @if($c !== $cap)
                                                    <input type="hidden" name="{{ $c }}" value="{{ $p->$c ? '1' : '0' }}">
                                                    @endif
                                                @endforeach
                                                <input type="hidden" name="{{ $cap }}" value="{{ $p->$cap ? '0' : '1' }}">
                                                <button type="submit" title="{{ $p->$cap ? 'Quitar permiso' : 'Otorgar permiso' }}"
                                                        style="border:none;background:none;cursor:pointer;font-size:16px;opacity:{{ $p->$cap ? '1' : '0.25' }}">
                                                    {{ $p->$cap ? '✅' : '○' }}
                                                </button>
                                            </form>
                                        </td>
                                        @endforeach
                                        <td style="text-align:center;font-size:11px;color:var(--fc-text-muted)">
                                            {{ $p->concedidoPor->nombre ?? '—' }}
                                        </td>
                                        <td>
                                            <form method="POST" action="{{ route('permisos.destroy', [$carpeta, $p]) }}">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="fc-action-ico danger" title="Revocar"
                                                        onclick="return confirm('¿Revocar todos los permisos de {{ addslashes($p->usuario->nombre_completo ?? '') }}?')">
                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="fc-empty" style="padding:28px 20px">
                            <div class="fc-empty-sub">Ningún usuario tiene permiso individual en esta carpeta.</div>
                        </div>
                        @endif
                    </div>

                    {{-- Permisos por rol --}}
                    <div class="fc-card">
                        <div class="fc-card-header">
                            <div class="fc-card-title" style="margin:0;border:none;padding:0;display:flex;align-items:center;gap:8px">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="#7c3aed"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
                                Permisos por rol/empresa
                                <span style="font-size:11px;background:#f1f5f9;color:#64748b;padding:2px 8px;border-radius:20px;font-weight:600">
                                    {{ $permisoRol->count() }}
                                </span>
                            </div>
                        </div>

                        @if($permisoRol->count())
                        <div style="overflow-x:auto;border-top:1px solid var(--fc-border)">
                            <table class="fc-table" style="min-width:600px">
                                <thead>
                                    <tr>
                                        <th>Empresa · Rol</th>
                                        <th style="text-align:center">👁 Leer</th>
                                        <th style="text-align:center">⬆ Subir</th>
                                        <th style="text-align:center">✏ Editar</th>
                                        <th style="text-align:center">🗑 Borrar</th>
                                        <th style="text-align:center">⬇ Descargar</th>
                                        <th style="text-align:center">↳ Heredar</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($permisoRol as $p)
                                    <tr>
                                        <td>
                                            <div>
                                                <div style="font-size:12px;font-weight:600;color:var(--fc-text)">
                                                    {{ $p->empresa->nombre ?? 'Todas las empresas' }}
                                                </div>
                                                @if($p->rol)
                                                <span class="fc-badge fc-badge-rol rol-{{ strtolower(str_replace('_','-',$p->rol)) }}" style="margin-top:3px">
                                                    {{ $p->rol }}
                                                </span>
                                                @else
                                                <span style="font-size:11px;color:var(--fc-text-muted)">Todos los roles</span>
                                                @endif
                                            </div>
                                        </td>
                                        @foreach(['puede_leer','puede_subir','puede_editar','puede_borrar','puede_descargar','heredar'] as $cap)
                                        <td style="text-align:center">
                                            <form method="POST" action="{{ route('permisos.update', [$carpeta, $p]) }}" style="display:inline">
                                                @csrf @method('PUT')
                                                @foreach(['puede_leer','puede_subir','puede_editar','puede_borrar','puede_descargar','heredar'] as $c)
                                                    @if($c !== $cap)
                                                    <input type="hidden" name="{{ $c }}" value="{{ $p->$c ? '1' : '0' }}">
                                                    @endif
                                                @endforeach
                                                <input type="hidden" name="{{ $cap }}" value="{{ $p->$cap ? '0' : '1' }}">
                                                <button type="submit" style="border:none;background:none;cursor:pointer;font-size:16px;opacity:{{ $p->$cap ? '1' : '0.25' }}">
                                                    {{ $p->$cap ? '✅' : '○' }}
                                                </button>
                                            </form>
                                        </td>
                                        @endforeach
                                        <td>
                                            <form method="POST" action="{{ route('permisos.destroy', [$carpeta, $p]) }}">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="fc-action-ico danger" title="Revocar"
                                                        onclick="return confirm('¿Revocar este permiso de rol?')">
                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="fc-empty" style="padding:28px 20px">
                            <div class="fc-empty-sub">No hay permisos asignados por rol o empresa.</div>
                        </div>
                        @endif
                    </div>

                </div>{{-- /fc-col-main --}}

                {{-- ══ FORMULARIO AGREGAR PERMISO ══ --}}
                <div class="fc-col-side">

                    {{-- Tabs: usuario / rol --}}
                    <div class="fc-card">
                        <div class="fc-card-title">Agregar permiso</div>

                        {{-- Toggle modo --}}
                        <div style="display:flex;gap:6px;margin-bottom:18px">
                            <button type="button" id="btnModoUsuario"
                                    onclick="setModo('usuario')"
                                    class="fc-btn fc-btn-primary fc-btn-sm" style="flex:1;justify-content:center">
                                Por usuario
                            </button>
                            <button type="button" id="btnModoRol"
                                    onclick="setModo('rol')"
                                    class="fc-btn fc-btn-outline fc-btn-sm" style="flex:1;justify-content:center">
                                Por rol
                            </button>
                        </div>

                        {{-- Formulario único con campos condicionales --}}
                        <form method="POST" action="{{ route('permisos.store', $carpeta) }}" id="formPermiso">
                            @csrf

                            {{-- MODO USUARIO --}}
                            <div id="camposUsuario">
                                <div class="fc-form-group">
                                    <label class="fc-label">Usuario *</label>
                                    <select name="usuario_id" class="fc-input">
                                        <option value="">— Selecciona usuario —</option>
                                        @foreach($usuarios as $u)
                                        <option value="{{ $u->id }}" {{ old('usuario_id') == $u->id ? 'selected' : '' }}>
                                            {{ $u->nombre_completo }} ({{ $u->rol }})
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- MODO ROL --}}
                            <div id="camposRol" style="display:none">
                                <div class="fc-form-group">
                                    <label class="fc-label">Empresa</label>
                                    <select name="empresa_id" class="fc-input">
                                        <option value="">Todas las empresas</option>
                                        @foreach($empresas as $emp)
                                        <option value="{{ $emp->id }}" {{ old('empresa_id') == $emp->id ? 'selected' : '' }}>
                                            {{ $emp->nombre }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="fc-form-group" style="margin-top:12px">
                                    <label class="fc-label">Rol *</label>
                                    <select name="rol" class="fc-input">
                                        <option value="">— Selecciona rol —</option>
                                        @foreach(['Admin','Gerente','Auxiliar','Empleado'] as $r)
                                        <option value="{{ $r }}" {{ old('rol') == $r ? 'selected' : '' }}>{{ $r }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- Permisos granulares --}}
                            <div style="margin-top:16px;border-top:1px solid var(--fc-border);padding-top:16px">
                                <div class="fc-label" style="margin-bottom:10px">Capacidades *</div>

                                @php
                                $caps = [
                                    'puede_leer'      => ['👁',  'Leer',      'Ver archivos de la carpeta'],
                                    'puede_descargar' => ['⬇',  'Descargar', 'Descargar archivos'],
                                    'puede_subir'     => ['⬆',  'Subir',     'Subir nuevos archivos'],
                                    'puede_editar'    => ['✏',  'Editar',    'Editar descripción y metadatos'],
                                    'puede_borrar'    => ['🗑', 'Borrar',    'Eliminar archivos'],
                                ];
                                @endphp

                                @foreach($caps as $campo => [$icono, $label, $hint])
                                <label style="display:flex;align-items:center;gap:10px;padding:8px 10px;border-radius:8px;cursor:pointer;transition:background .15s"
                                       onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                                    <input type="checkbox" name="{{ $campo }}" value="1"
                                           {{ in_array($campo, ['puede_leer','puede_descargar']) ? 'checked' : '' }}
                                           style="width:15px;height:15px;accent-color:#6366f1;flex-shrink:0">
                                    <div>
                                        <span style="font-size:13px;font-weight:500;color:var(--fc-text)">{{ $icono }} {{ $label }}</span>
                                        <div style="font-size:11px;color:var(--fc-text-muted)">{{ $hint }}</div>
                                    </div>
                                </label>
                                @endforeach

                                {{-- Heredar --}}
                                <label style="display:flex;align-items:center;gap:10px;padding:8px 10px;border-radius:8px;cursor:pointer;border-top:1px dashed var(--fc-border);margin-top:6px;padding-top:12px"
                                       onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                                    <input type="checkbox" name="heredar" value="1" checked
                                           style="width:15px;height:15px;accent-color:#6366f1;flex-shrink:0">
                                    <div>
                                        <span style="font-size:13px;font-weight:500;color:var(--fc-text)">↳ Heredar a subcarpetas</span>
                                        <div style="font-size:11px;color:var(--fc-text-muted)">El permiso aplica también en subcarpetas</div>
                                    </div>
                                </label>
                            </div>

                            <button type="submit" class="fc-btn fc-btn-primary" style="width:100%;justify-content:center;margin-top:16px">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/></svg>
                                Otorgar permiso
                            </button>
                        </form>
                    </div>

                    {{-- Leyenda --}}
                    <div class="fc-card" style="margin-top:16px">
                        <div class="fc-card-title" style="font-size:12px">Leyenda de capacidades</div>
                        <div style="font-size:12px;color:var(--fc-text-muted);line-height:2">
                            👁 <strong>Leer</strong> — Ver lista de archivos<br>
                            ⬇ <strong>Descargar</strong> — Descargar archivos<br>
                            ⬆ <strong>Subir</strong> — Subir nuevos archivos<br>
                            ✏ <strong>Editar</strong> — Editar metadatos<br>
                            🗑 <strong>Borrar</strong> — Eliminar archivos<br>
                            ↳ <strong>Heredar</strong> — Aplica en subcarpetas
                        </div>
                    </div>

                </div>{{-- /fc-col-side --}}

            </div>{{-- /fc-content-cols --}}
        </div>{{-- /fc-content --}}
    </div>{{-- /fc-main --}}
</div>{{-- /fc-wrapper --}}

<script>
function setModo(modo) {
    const esUsuario = modo === 'usuario';
    document.getElementById('camposUsuario').style.display = esUsuario ? 'block' : 'none';
    document.getElementById('camposRol').style.display     = esUsuario ? 'none'  : 'block';
    document.getElementById('btnModoUsuario').className = 'fc-btn fc-btn-sm' + (esUsuario ? ' fc-btn-primary' : ' fc-btn-outline') + ' ' + (esUsuario ? '' : '');
    document.getElementById('btnModoRol').className     = 'fc-btn fc-btn-sm' + (!esUsuario ? ' fc-btn-primary' : ' fc-btn-outline') + ' ' + (!esUsuario ? '' : '');
    // Limpiar campos del modo inactivo
    if (esUsuario) {
        document.querySelector('[name="empresa_id"]') && (document.querySelector('[name="empresa_id"]').value = '');
        document.querySelector('[name="rol"]') && (document.querySelector('[name="rol"]').value = '');
    } else {
        document.querySelector('[name="usuario_id"]') && (document.querySelector('[name="usuario_id"]').value = '');
    }
}
// Restaurar modo si hubo error de validación
@if(old('empresa_id') || old('rol'))
    setModo('rol');
@endif
</script>
</x-app-layout>