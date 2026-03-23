@props(['stats'])

<div class="fc-stats">
    @foreach($stats as $stat)
        <div class="fc-stat">
            <div class="fc-stat-icon" style="background:{{ $stat['iconBg'] ?? 'rgba(124,58,237,0.13)' }}">
                {!! $stat['iconSvg'] !!}
            </div>
            <div class="fc-stat-arrow">{{ $stat['arrow'] ?? '↗' }}</div>
            <div class="fc-stat-num">{{ $stat['value'] }}</div>
            <div class="fc-stat-label">{{ $stat['label'] }}</div>
            <div class="fc-stat-trend {{ $stat['trendClass'] ?? 'neutral' }}">
                {{ $stat['trendText'] }}
            </div>
        </div>
    @endforeach
</div>