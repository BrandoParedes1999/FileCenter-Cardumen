@props(['roles', 'maxRol'])

<div class="fc-roles-card">
    <div class="fc-roles-title">Usuarios por Rol</div>

    @foreach($roles as $ur)
        <div class="fc-role-row">
            <div class="fc-role-name">{{ $ur->rol }}</div>
            <div class="fc-role-bar-bg">
                <div class="fc-role-bar"
                     style="width:{{ round(($ur->total / $maxRol) * 100) }}%;background:{{ $ur->color }}">
                </div>
            </div>
            <div class="fc-role-count">{{ $ur->total }}</div>
        </div>
    @endforeach

    @if($roles->isEmpty())
        <div style="padding:16px 0;text-align:center;font-size:12px;color:#94a3b8">Sin datos</div>
    @endif
</div>