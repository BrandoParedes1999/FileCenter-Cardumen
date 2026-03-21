<x-app-layout>

@section('title', $empresa->nombre)

@section('content')
@php
    $bg     = $empresa->color_primario   ?? '#1B3A6B';
    $accent = $empresa->color_secundario ?? '#2E5FA3';
    $logo   = asset('images/empresas/'.$empresa->logo);
@endphp

<div class="fc-page-header">
    <div>
        <h1 class="fc-page-title">
            {{-- Badge inline con colores reales de la empresa --}}
            <span class="fc-badge-empresa-inline" style="background:{{ $bg }};color:#fff;padding:2px 10px;border-radius:6px;font-size:13px;margin-right:8px;font-weight:500">
                {{ $empresa->siglas }}
            </span>
            {{ $empresa->nombre }}
        </h1>
        <div class="fc-breadcrumb">
            <a href="{{ route('dashboard') }}">Inicio</a>
            <span>›</span>
            <a href="{{ route('empresas.index') }}">Empresas</a>
            <span>›</span>
            <span>{{ $empresa->siglas }}</span>
        </div>
    </div>
    <div style="display:flex;gap:10px">
        <a href="{{ route('empresas.edit', $empresa) }}" class="fc-btn fc-btn-outline">Editar</a>
        @if(!$empresa->es_corporativo)
        <form method="POST" action="{{ route('empresas.toggle-activo', $empresa) }}">
            @csrf
            <button type="submit" class="fc-btn {{ $empresa->activo ? 'fc-btn-warning' : 'fc-btn-success' }}">
                {{ $empresa->activo ? 'Desactivar' : 'Activar' }}
            </button>
        </form>
        @endif
    </div>
</div>

@if(session('success'))
    <div class="fc-flash fc-flash-success">{{ session('success') }}</div>
@endif

<div style="display:grid;grid-template-columns:1fr 340px;gap:24px">

    {{-- Panel principal --}}
    <div>

        {{-- Hero banner con el color de la empresa --}}
        <div class="fc-empresa-show-hero" style="background:linear-gradient(135deg,{{ $bg }},{{ $accent }})">
            <img src="{{ $logo }}"
                 alt="{{ $empresa->siglas }}"
                 class="fc-empresa-show-logo"
                 onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
            <div class="fc-empresa-show-logo-fallback" style="display:none">
                {{ strtoupper(substr($empresa->siglas,0,2)) }}
            </div>
            <div>
                <div class="fc-empresa-show-nombre">{{ $empresa->nombre }}</div>
                <div class="fc-empresa-show-siglas">{{ $empresa->siglas }}
                    @if($empresa->es_corporativo)
                        · <span style="opacity:.7">Empresa corporativa</span>
                    @endif
                </div>
                <div class="fc-empresa-show-estado">
                    @if($empresa->activo)
                        <span class="fc-badge" style="background:rgba(255,255,255,0.2);color:#fff">Activa</span>
                    @else
                        <span class="fc-badge fc-badge-danger">Inactiva</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Paleta de colores --}}
        <div class="fc-card" style="margin-top:20px">
            <div class="fc-card-title">Identidad visual</div>
            <div class="fc-empresa-paleta-detalle">
                <div class="fc-empresa-paleta-item">
                    <div class="fc-empresa-paleta-muestra" style="background:{{ $empresa->color_primario ?? '#ccc' }}"></div>
                    <div>
                        <div class="fc-empresa-paleta-label">Color primario</div>
                        <div class="fc-empresa-paleta-hex">{{ $empresa->color_primario ?? 'No definido' }}</div>
                        <div class="fc-empresa-paleta-uso">Sidebar · Topbar accent</div>
                    </div>
                </div>
                <div class="fc-empresa-paleta-item">
                    <div class="fc-empresa-paleta-muestra" style="background:{{ $empresa->color_secundario ?? '#ccc' }}"></div>
                    <div>
                        <div class="fc-empresa-paleta-label">Color secundario</div>
                        <div class="fc-empresa-paleta-hex">{{ $empresa->color_secundario ?? 'No definido' }}</div>
                        <div class="fc-empresa-paleta-uso">Botones activos · Badges</div>
                    </div>
                </div>
                <div class="fc-empresa-paleta-item">
                    <div class="fc-empresa-paleta-muestra" style="background:{{ $empresa->color_terciario ?? '#ccc' }}"></div>
                    <div>
                        <div class="fc-empresa-paleta-label">Color terciario</div>
                        <div class="fc-empresa-paleta-hex">{{ $empresa->color_terciario ?? 'No definido' }}</div>
                        <div class="fc-empresa-paleta-uso">Fondos suaves · Hover</div>
                    </div>
                </div>
            </div>

            {{-- Preview del badge tal como se verá en el sistema --}}
            <div style="margin-top:16px;padding-top:16px;border-top:1px solid var(--fc-border)">
                <div class="fc-label-sm">Vista previa del badge en el sistema</div>
                <div style="display:flex;gap:10px;align-items:center;margin-top:8px;flex-wrap:wrap">
                    <span class="fc-badge-empresa" style="
                        background:{{ $accent }}18;
                        color:{{ $accent }};
                        border:1px solid {{ $accent }}33;
                        padding:4px 12px;border-radius:999px;font-size:12px;font-weight:500">
                        {{ $empresa->siglas }}
                    </span>
                    <span style="font-size:12px;color:var(--fc-text-muted)">← así aparece en tablas, cards y listas</span>
                </div>
            </div>
        </div>

        {{-- Usuarios de la empresa --}}
        <div class="fc-card" style="margin-top:20px">
            <div class="fc-card-header">
                <div class="fc-card-title">Usuarios ({{ $empresa->usuarios_count }})</div>
                <a href="{{ route('usuarios.index') }}?empresa={{ $empresa->id }}" class="fc-btn fc-btn-outline fc-btn-sm">Ver todos</a>
            </div>
            @if($empresa->usuarios->count())
            <table class="fc-table" style="margin-top:8px">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Rol</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($empresa->usuarios as $u)
                    <tr>
                        <td>
                            <div class="fc-user-cell">
                                <div class="fc-avatar-sm"
                                     style="background:linear-gradient(135deg,{{ $bg }},{{ $accent }})">
                                    {{ strtoupper(substr($u->nombre,0,1)) }}{{ strtoupper(substr($u->paterno,0,1)) }}
                                </div>
                                {{ $u->nombre_completo }}
                            </div>
                        </td>
                        <td style="font-size:12px;color:var(--fc-text-muted)">{{ $u->email }}</td>
                        <td><span class="fc-badge fc-badge-rol">{{ $u->rol }}</span></td>
                        <td>
                            @if($u->es_activo)
                                <span class="fc-badge fc-badge-success">Activo</span>
                            @else
                                <span class="fc-badge fc-badge-danger">Inactivo</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @if($empresa->usuarios_count > 8)
                <div style="padding:10px 0;font-size:12px;color:var(--fc-text-muted)">
                    Mostrando 8 de {{ $empresa->usuarios_count }}. <a href="{{ route('usuarios.index') }}?empresa={{ $empresa->id }}">Ver todos →</a>
                </div>
            @endif
            @else
                <div class="fc-empty-state" style="padding:24px 0">Sin usuarios asignados.</div>
            @endif
        </div>

    </div>

    {{-- Panel lateral --}}
    <div>

        <div class="fc-card">
            <div class="fc-card-title">Información</div>
            <dl class="fc-dl">
                <dt>Logo</dt>
                <dd>{{ $empresa->logo }}</dd>
                <dt>Siglas</dt>
                <dd><code>{{ $empresa->siglas }}</code></dd>
                <dt>Corporativo</dt>
                <dd>{{ $empresa->es_corporativo ? 'Sí' : 'No' }}</dd>
                <dt>Estado</dt>
                <dd>{{ $empresa->activo ? 'Activa' : 'Inactiva' }}</dd>
                <dt>Creada</dt>
                <dd>{{ $empresa->created_at ? $empresa->created_at->format('d/m/Y') : '—' }}</dd>
                <dt>Actualizada</dt>
                <dd>{{ $empresa->updated_at ? $empresa->updated_at->diffForHumans() : '—' }}</dd>
            </dl>
        </div>

        <div class="fc-card" style="margin-top:16px">
            <div class="fc-card-title">Resumen</div>
            <div class="fc-empresa-stat-panel">
                <div class="fc-empresa-stat-big">
                    <div class="fc-empresa-stat-num" style="color:{{ $accent }}">{{ $empresa->usuarios_count }}</div>
                    <div class="fc-empresa-stat-label">Usuarios</div>
                </div>
                <div class="fc-empresa-stat-big">
                    <div class="fc-empresa-stat-num" style="color:{{ $accent }}">{{ $empresa->carpetas_count }}</div>
                    <div class="fc-empresa-stat-label">Carpetas</div>
                </div>
            </div>
        </div>

        {{-- Zona peligrosa --}}
        @if(!$empresa->es_corporativo && $empresa->usuarios_count === 0 && $empresa->carpetas_count === 0)
        <div class="fc-card fc-card-danger" style="margin-top:16px">
            <div class="fc-card-title">Zona de peligro</div>
            <p style="font-size:13px;color:var(--fc-text-muted);margin-bottom:12px">
                Esta empresa no tiene usuarios ni carpetas. Puedes eliminarla de forma permanente.
            </p>
            <form method="POST" action="{{ route('empresas.destroy', $empresa) }}"
                  onsubmit="return confirm('¿Eliminar empresa {{ addslashes($empresa->nombre) }}? Esta acción no se puede deshacer.')">
                @csrf @method('DELETE')
                <button type="submit" class="fc-btn fc-btn-danger-outline" style="width:100%">
                    Eliminar empresa
                </button>
            </form>
        </div>
        @endif

    </div>
</div>
</x-app-layout>