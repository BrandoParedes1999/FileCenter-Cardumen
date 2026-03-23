@props(['empresas', 'maxArchivos'])

<div class="fc-chart-box">
    <div class="fc-chart-header">
        <div>
            <div class="fc-chart-title">Archivos por Área</div>
            <div class="fc-chart-sub">Distribución de contenido activo</div>
        </div>
        <span style="color:#475569;font-size:18px">↗</span>
    </div>
    <div style="display:flex;align-items:flex-end;gap:4px;height:120px">
        <div style="display:flex;flex-direction:column;justify-content:space-between;height:110px;padding-right:10px">
            <span style="font-size:10px;color:#475569">{{ $maxArchivos }}</span>
            <span style="font-size:10px;color:#475569">{{ round($maxArchivos*0.75) }}</span>
            <span style="font-size:10px;color:#475569">{{ round($maxArchivos*0.5) }}</span>
            <span style="font-size:10px;color:#475569">{{ round($maxArchivos*0.25) }}</span>
            <span style="font-size:10px;color:#475569">0</span>
        </div>
        <div style="flex:1;display:flex;align-items:flex-end;gap:14px;height:110px">
            @foreach($empresas as $emp)
                @php $pct = max(round(($emp->total_archivos / $maxArchivos) * 100), 4); @endphp
                <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:5px">
                    <div style="width:100%;background:linear-gradient(to top,{{ $emp->color_primario ?? '#4338ca' }},{{ $emp->color_secundario ?? '#6366f1' }});border-radius:5px 5px 0 0;height:{{ $pct }}%"
                         title="{{ $emp->nombre }}: {{ $emp->total_archivos }} archivos"></div>
                    <span style="font-size:10px;color:#475569;white-space:nowrap;overflow:hidden;max-width:55px;text-overflow:ellipsis"
                          title="{{ $emp->nombre }}">{{ $emp->siglas }}</span>
                </div>
            @endforeach
        </div>
    </div>
</div>