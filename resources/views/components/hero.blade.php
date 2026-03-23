@props([
    'badge' => null,
    'title' => 'Panel de Control',
    'subtitle' => '',
    'buttonLeft' => null,
    'buttonRight' => null,
])

<div class="fc-hero">
    <div>
        @if($badge)
            <div class="fc-hero-badge">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="#a5b4fc">
                    <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/>
                </svg>
                {{ $badge }}
            </div>
        @endif
        <div class="fc-hero-title">{{ $title }}</div>
        <div class="fc-hero-sub">{{ $subtitle }}</div>
    </div>
    @if($buttonLeft || $buttonRight)
        <div class="fc-hero-btns">
            @if($buttonLeft)
                <button href= "#" class="fc-btn-outline">{{ $buttonLeft }}</button>
            @endif
            @if($buttonRight)
                <button class="fc-btn-solid">{{ $buttonRight }}</button>
            @endif
        </div>
    @endif
</div>