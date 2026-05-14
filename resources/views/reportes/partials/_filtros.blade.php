{{-- Panel de filtros + botón exportar --}}
<div class="rp-panel">
    <div class="rp-panel-title">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="#64748b">
            <path d="M4.25 5.61C6.27 8.2 10 13 10 13v6c0 .55.45 1 1 1h2c.55 0 1-.45 1-1v-6s3.72-4.8 5.74-7.39A.998.998 0 0 0 18.95 4H5.04c-.83 0-1.3.95-.79 1.61z"/>
        </svg>
        Filtros del reporte
    </div>

    <form method="GET" id="formFiltros">
        <div class="rp-filters-grid">

            <div class="rp-field">
                <label>Fecha inicio</label>
                <input type="date" name="fecha_inicio"
                       value="{{ request('fecha_inicio', now()->subDays(30)->format('Y-m-d')) }}">
            </div>

            <div class="rp-field">
                <label>Fecha fin</label>
                <input type="date" name="fecha_fin"
                       value="{{ request('fecha_fin', now()->format('Y-m-d')) }}">
            </div>

            <div class="rp-field">
                <label>Empresa</label>
                <select name="empresa_id">
                    <option value="">Todas las empresas</option>
                    @foreach($empresas as $emp)
                    <option value="{{ $emp->id }}" {{ request('empresa_id') == $emp->id ? 'selected' : '' }}>
                        {{ $emp->es_corporativo ? '🏢' : '🏭' }} {{ $emp->nombre }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="rp-field">
                <label>Usuario</label>
                <select name="usuario_id">
                    <option value="">Todos los usuarios</option>
                    @foreach($usuarios as $u)
                    <option value="{{ $u->id }}" {{ request('usuario_id') == $u->id ? 'selected' : '' }}>
                        {{ $u->paterno }} {{ $u->nombre }} — {{ $u->rol }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="rp-field">
                <label>Tipo de acción</label>
                <select name="accion">
                    <option value="">Todas (subir, descargar, ver, eliminar)</option>
                    <option value="subir"             {{ request('accion') === 'subir'             ? 'selected' : '' }}>Subir archivo</option>
                    <option value="descargar"         {{ request('accion') === 'descargar'         ? 'selected' : '' }}>Descargar archivo</option>
                    <option value="ver"               {{ request('accion') === 'ver'               ? 'selected' : '' }}>Visualizar archivo</option>
                    <option value="eliminar"          {{ request('accion') === 'eliminar'          ? 'selected' : '' }}>Eliminar archivo</option>
                    <option value="restaurar_version" {{ request('accion') === 'restaurar_version' ? 'selected' : '' }}>Restaurar versión</option>
                </select>
            </div>

        </div>

        <div class="rp-actions">
            <button type="submit" class="fc-btn fc-btn-outline" style="font-size:13px;padding:8px 16px">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor" style="margin-right:5px">
                    <path d="M4.25 5.61C6.27 8.2 10 13 10 13v6c0 .55.45 1 1 1h2c.55 0 1-.45 1-1v-6s3.72-4.8 5.74-7.39A.998.998 0 0 0 18.95 4H5.04c-.83 0-1.3.95-.79 1.61z"/>
                </svg>
                Aplicar filtros
            </button>

            <a href="{{ route('reportes.actividad') }}" class="fc-btn fc-btn-outline"
               style="font-size:12px;padding:8px 14px;color:#64748b">
                Limpiar
            </a>

            <a href="{{ route('reportes.actividad.exportar', request()->query()) }}"
               class="fc-btn fc-btn-primary"
               style="font-size:13px;padding:8px 18px;background:#059669;border-color:#059669;gap:6px">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/>
                </svg>
                Descargar Excel (.csv)
            </a>

            <span class="rp-total-badge">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z"/>
                </svg>
                {{ number_format($totalFiltrado) }} registros con filtros actuales
            </span>
        </div>
    </form>
</div>
