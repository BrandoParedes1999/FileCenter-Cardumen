<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'FileCenter') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        #bg-canvas {
            position: absolute; inset: 0;
            width: 100%; height: 100%; z-index: 0;
        }
        .orb {
            position: absolute; border-radius: 50%;
            filter: blur(60px); opacity: 0.18;
        }
        .orb-1 {
            width: 380px; height: 380px; background: #818cf8;
            top: -80px; right: -60px;
            animation: float-orb-1 18s linear infinite;
        }
        .orb-2 {
            width: 280px; height: 280px; background: #6d28d9;
            bottom: 80px; left: -40px;
            animation: float-orb-2 22s linear infinite;
        }
        .orb-3 {
            width: 200px; height: 200px; background: #a5f3fc;
            bottom: 30%; right: 10%; opacity: 0.1;
            animation: float-orb-3 15s linear infinite;
        }
        @keyframes float-orb-1 {
            0%   { transform: translate(0,0) scale(1); }
            33%  { transform: translate(-30px,40px) scale(1.08); }
            66%  { transform: translate(20px,-20px) scale(0.95); }
            100% { transform: translate(0,0) scale(1); }
        }
        @keyframes float-orb-2 {
            0%   { transform: translate(0,0) scale(1); }
            40%  { transform: translate(40px,-30px) scale(1.1); }
            70%  { transform: translate(-20px,20px) scale(0.92); }
            100% { transform: translate(0,0) scale(1); }
        }
        @keyframes float-orb-3 {
            0%   { transform: translate(0,0); }
            50%  { transform: translate(-25px,35px); }
            100% { transform: translate(0,0); }
        }
        .grid-lines {
            position: absolute; inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
            background-size: 48px 48px;
            animation: grid-drift 20s linear infinite;
            z-index: 0;
        }
        @keyframes grid-drift {
            0%   { background-position: 0 0; }
            100% { background-position: 48px 48px; }
        }
        .feature-item {
            display: flex; align-items: center; gap: 14px;
            padding: 11px 15px; border-radius: 13px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.08);
            backdrop-filter: blur(6px);
            transition: background .2s, border-color .2s, transform .2s;
        }
        .feature-item:hover {
            background: rgba(255,255,255,0.1);
            border-color: rgba(255,255,255,0.18);
            transform: translateX(4px);
        }
        .feature-icon {
            width: 36px; height: 36px; border-radius: 10px;
            background: rgba(255,255,255,0.08);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        @keyframes pulse-dot {
            0%, 100% { opacity:1; transform:scale(1); }
            50%       { opacity:.5; transform:scale(1.4); }
        }
        .pulse-dot { animation: pulse-dot 2s ease-in-out infinite; }
        @keyframes card-in {
            from { opacity:0; transform:translateY(18px); }
            to   { opacity:1; transform:translateY(0); }
        }
        .card-animate { animation: card-in .55s cubic-bezier(.4,0,.2,1) both; }

        /* ══ CINTA VHS MEJORADA ══ */
        .vhs-wrapper {
            position: relative;
            padding: 10px 0;
        }
        .vhs-wrapper::before {
            content: '';
            position: absolute; top: 0; left: 0; right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(165,180,252,0.2), transparent);
        }
        .vhs-wrapper::after {
            content: '';
            position: absolute; bottom: 0; left: 0; right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(165,180,252,0.2), transparent);
        }
        .vhs-track {
    display: flex;
    gap: 20px;
    width: max-content;
    animation: vhs-scroll 28s linear infinite;
    padding: 10px 0;
}
.vhs-track:hover { animation-play-state: paused; }
        .vhs-track:hover { animation-play-state: paused; }
        @keyframes vhs-scroll {
            0%   { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
        .vhs-card {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 14px 26px;
    border-radius: 16px;
    background: rgba(255,255,255,0.07);
    border: 1px solid rgba(255,255,255,0.12);
    backdrop-filter: blur(10px);
    white-space: nowrap;
    flex-shrink: 0;
    transition: background .25s, border-color .25s, transform .25s;
    cursor: default;
    box-shadow: 0 4px 16px rgba(0,0,0,0.2);
}
        .vhs-card:hover {
            background: rgba(255,255,255,0.14);
            border-color: rgba(165,180,252,0.35);
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(79,70,229,0.25);
        }
       .vhs-logo {
    width: 72px; height: 72px;
    border-radius: 12px;
    object-fit: contain;
    background: rgba(255,255,255,0.1);
    padding: 6px;
    border: 1px solid rgba(255,255,255,0.1);
}
    .vhs-name {
    font-size: 15px; font-weight: 700;
    color: rgba(224,231,255,0.95);
    letter-spacing: .01em;
}
        .vhs-sector {
    font-size: 12px;
    color: rgba(165,180,252,0.6);
    margin-top: 3px;
    font-weight: 500;
}
     .vhs-dot {
    width: 9px; height: 9px; border-radius: 50%;
    flex-shrink: 0; margin-left: 6px;
    box-shadow: 0 0 8px currentColor;
}
        .vhs-fade-left {
            position: absolute; top: 0; left: 0; bottom: 0; width: 80px;
            background: linear-gradient(90deg, #130f3f 20%, transparent);
            z-index: 2; pointer-events: none;
        }
        .vhs-fade-right {
            position: absolute; top: 0; right: 0; bottom: 0; width: 80px;
            background: linear-gradient(270deg, #130f3f 20%, transparent);
            z-index: 2; pointer-events: none;
        }

        /* ══ PERSONAJE EJECUTIVO ══ */
        #exec-char {
            position: absolute;
            bottom: -4px;
            right: -18px;
            width: 110px;
            z-index: 20;
            pointer-events: none;
            filter: drop-shadow(0 8px 24px rgba(79,70,229,0.4));
            animation: exec-float 4s ease-in-out infinite;
        }
        @keyframes exec-float {
            0%, 100% { transform: translateY(0); }
            50%       { transform: translateY(-10px); }
        }

        /* Burbuja de diálogo del personaje */
        #exec-bubble {
            position: absolute;
            bottom: 108px;
            right: 80px;
            background: rgba(255,255,255,0.97);
            border: 1px solid #e0e7ff;
            border-radius: 14px 14px 4px 14px;
            padding: 10px 14px;
            font-size: 11px;
            font-weight: 600;
            color: #3730a3;
            white-space: nowrap;
            z-index: 21;
            box-shadow: 0 8px 24px rgba(79,70,229,0.18);
            animation: bubble-in 0.4s cubic-bezier(.4,0,.2,1) both,
                       bubble-float 4s ease-in-out 0.4s infinite;
            letter-spacing: .01em;
        }
        @keyframes bubble-in {
            from { opacity:0; transform:scale(0.7) translateY(10px); }
            to   { opacity:1; transform:scale(1) translateY(0); }
        }
        @keyframes bubble-float {
            0%, 100% { transform: translateY(0); }
            50%       { transform: translateY(-10px); }
        }
        #exec-bubble::after {
            content: '';
            position: absolute;
            bottom: -7px; right: 14px;
            border-left: 7px solid transparent;
            border-right: 0px solid transparent;
            border-top: 7px solid rgba(255,255,255,0.97);
        }

        /* Typing dots en la burbuja */
        .typing-dots span {
            display: inline-block;
            width: 4px; height: 4px;
            border-radius: 50%;
            background: #6366f1;
            margin: 0 1px;
            animation: typing-dot .9s ease-in-out infinite;
        }
        .typing-dots span:nth-child(2) { animation-delay: .15s; }
        .typing-dots span:nth-child(3) { animation-delay: .30s; }
        @keyframes typing-dot {
            0%, 80%, 100% { transform: scale(0.6); opacity:.4; }
            40%            { transform: scale(1.1); opacity:1; }
        }
    </style>
</head>
<body class="font-sans antialiased text-gray-900">
<div class="min-h-screen flex">

    {{-- ══ SIDEBAR ══ --}}
    <div class="hidden lg:flex lg:w-[52%] flex-col justify-between p-14 text-white relative overflow-hidden"
         style="background: linear-gradient(135deg, #0f0c29 0%, #1e1b4b 40%, #312e81 75%, #1a1060 100%);">

        <canvas id="bg-canvas"></canvas>
        <div class="grid-lines"></div>
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>

        {{-- Contenido superior --}}
        <div class="relative z-10 max-w-lg">

            <div style="display:flex;align-items:center;gap:14px;margin-bottom:36px;">
                <div style="
                    width:54px;height:54px;
                    background:linear-gradient(135deg,rgba(255,255,255,0.18),rgba(255,255,255,0.06));
                    border:1px solid rgba(255,255,255,0.2);border-radius:16px;
                    display:flex;align-items:center;justify-content:center;
                    backdrop-filter:blur(10px);box-shadow:0 8px 32px rgba(79,70,229,0.3);flex-shrink:0;
                ">
                    <x-application-logo class="w-9 h-9 object-contain" />
                </div>
                <div>
                    <div style="font-size:16px;font-weight:800;letter-spacing:-.3px;color:#fff;line-height:1.2;">FileCenter Cardumen</div>
                    <div style="font-size:10px;text-transform:uppercase;letter-spacing:.18em;color:rgba(165,180,252,0.7);margin-top:2px;">Corporativo Cardumen</div>
                </div>
            </div>

            <div style="
                display:inline-flex;align-items:center;gap:8px;
                background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.15);
                padding:6px 14px;border-radius:999px;backdrop-filter:blur(8px);margin-bottom:18px;
            ">
                <span class="pulse-dot" style="width:7px;height:7px;border-radius:50%;background:#4ade80;display:inline-block;"></span>
                <span style="font-size:11px;font-weight:600;color:rgba(199,210,254,0.9);letter-spacing:.05em;">Sistema de Gestión Corporativo</span>
            </div>

            <h1 style="font-size:50px;font-weight:900;line-height:1.08;letter-spacing:-1.5px;color:#fff;margin-bottom:16px;">
                Repositorio<br>
                <span style="background:linear-gradient(90deg,#a5b4fc,#818cf8,#c4b5fd);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">Inteligente</span>
            </h1>

            <p style="font-size:14px;color:rgba(199,210,254,0.75);line-height:1.7;max-width:400px;margin-bottom:28px;">
                Gestiona documentos, organiza áreas y controla el acceso de tu equipo desde un solo lugar de manera eficiente y segura.
            </p>

            <div style="display:flex;flex-direction:column;gap:9px;">
                <div class="feature-item">
                    <div class="feature-icon">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="rgba(165,180,252,0.9)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"/>
                        </svg>
                    </div>
                    <span style="font-size:13px;font-weight:600;color:rgba(224,231,255,0.92);">Repositorios organizados por áreas</span>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="rgba(165,180,252,0.9)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        </svg>
                    </div>
                    <span style="font-size:13px;font-weight:600;color:rgba(224,231,255,0.92);">Control de acceso por roles</span>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="rgba(165,180,252,0.9)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
                        </svg>
                    </div>
                    <span style="font-size:13px;font-weight:600;color:rgba(224,231,255,0.92);">Gestión de equipos y permisos</span>
                </div>
            </div>
        </div>

        {{-- CINTA VHS --}}
        <div class="relative z-10" style="margin-top:auto;">
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px;">
                <div style="flex:1;height:1px;background:linear-gradient(90deg,transparent,rgba(165,180,252,0.2));"></div>
                <span style="font-size:9px;font-weight:700;letter-spacing:.22em;text-transform:uppercase;color:rgba(165,180,252,0.4);">Nuestro Cardumen</span>
                <div style="flex:1;height:1px;background:linear-gradient(270deg,transparent,rgba(165,180,252,0.2));"></div>
            </div>

            <div class="vhs-wrapper">
                <div style="position:relative;overflow:hidden;width:100%;">
                    <div class="vhs-fade-left"></div>
                    <div class="vhs-fade-right"></div>
                    <div class="vhs-track" id="vhsTrack">

                        {{-- SET 1 --}}
                        <div class="vhs-card">
                            <img class="vhs-logo" src="{{ asset('images/Seaward-Logistic-Logo-.png') }}" alt="Seaward">
                            <div>
                                <div class="vhs-name">Seaward Logistic</div>
                                <div class="vhs-sector">Oil & Gas Services</div>
                            </div>
                            <div class="vhs-dot" style="background:#06b6d4;color:#06b6d4;"></div>
                        </div>

                        <div class="vhs-card">
                            <img class="vhs-logo" src="{{ asset('images/Seatools-Original.png') }}" alt="Seatools">
                            <div>
                                <div class="vhs-name">Seatools</div>
                                <div class="vhs-sector">Industrial Equipment</div>
                            </div>
                            <div class="vhs-dot" style="background:#f97316;color:#f97316;"></div>
                        </div>

                        <div class="vhs-card">
                            <img class="vhs-logo" src="{{ asset('images/The White Shark 1.png') }}" alt="The White Shark">
                            <div>
                                <div class="vhs-name">The White Shark</div>
                                <div class="vhs-sector">Catering & Supplies</div>
                            </div>
                            <div class="vhs-dot" style="background:#f59e0b;color:#f59e0b;"></div>
                        </div>

                        <div class="vhs-card">
                            <img class="vhs-logo" src="{{ asset('images/Original OMC.png') }}" alt="OMC">
                            <div>
                                <div class="vhs-name">OMC</div>
                                <div class="vhs-sector">Shipping Agency</div>
                            </div>
                            <div class="vhs-dot" style="background:#22c55e;color:#22c55e;"></div>
                        </div>

                        <div style="display:flex;align-items:center;padding:0 12px;color:rgba(165,180,252,0.2);font-size:20px;flex-shrink:0;">✦</div>

                        {{-- SET 2 - loop --}}
                        <div class="vhs-card">
                            <img class="vhs-logo" src="{{ asset('images/Seaward-Logistic-Logo-.png') }}" alt="Seaward">
                            <div>
                                <div class="vhs-name">Seaward Logistic</div>
                                <div class="vhs-sector">Oil & Gas Services</div>
                            </div>
                            <div class="vhs-dot" style="background:#06b6d4;color:#06b6d4;"></div>
                        </div>

                        <div class="vhs-card">
                            <img class="vhs-logo" src="{{ asset('images/Seatools-Original.png') }}" alt="Seatools">
                            <div>
                                <div class="vhs-name">Seatools</div>
                                <div class="vhs-sector">Industrial Equipment</div>
                            </div>
                            <div class="vhs-dot" style="background:#f97316;color:#f97316;"></div>
                        </div>

                        <div class="vhs-card">
                            <img class="vhs-logo" src="{{ asset('images/The White Shark 1.png') }}" alt="The White Shark">
                            <div>
                                <div class="vhs-name">The White Shark</div>
                                <div class="vhs-sector">Catering & Supplies</div>
                            </div>
                            <div class="vhs-dot" style="background:#f59e0b;color:#f59e0b;"></div>
                        </div>

                        <div class="vhs-card">
                            <img class="vhs-logo" src="{{ asset('images/Original OMC.png') }}" alt="OMC">
                            <div>
                                <div class="vhs-name">OMC</div>
                                <div class="vhs-sector">Shipping Agency</div>
                            </div>
                            <div class="vhs-dot" style="background:#22c55e;color:#22c55e;"></div>
                        </div>

                        <div style="display:flex;align-items:center;padding:0 12px;color:rgba(165,180,252,0.2);font-size:20px;flex-shrink:0;">✦</div>

                    </div>
                </div>
            </div>
        </div>

    </div>

{{-- ══ PANEL DERECHO ══ --}}
<div class="w-full lg:w-[48%] flex items-center justify-center bg-white p-8 sm:p-12 lg:p-16"
     style="position:relative; overflow:hidden;">

    {{-- Canvas de burbujas flotantes --}}
    <canvas id="bubble-canvas" style="
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: 0;
    "></canvas>
 
    {{-- Card de login --}}
    <div class="w-full max-w-sm card-animate">
        {{ $slot }}
    </div>

</div>
</div>

<script>
/* ── Partículas ── */
(function() {
    const canvas = document.getElementById('bg-canvas');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    function resize() {
        canvas.width  = canvas.offsetWidth;
        canvas.height = canvas.offsetHeight;
    }
    resize();
    window.addEventListener('resize', resize);
    const COUNT = 55;
    const particles = Array.from({ length: COUNT }, () => ({
        x:  Math.random() * canvas.width,
        y:  Math.random() * canvas.height,
        r:  Math.random() * 1.6 + 0.4,
        vx: (Math.random() - 0.5) * 0.35,
        vy: (Math.random() - 0.5) * 0.35,
        o:  Math.random() * 0.45 + 0.1,
    }));
    function draw() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        for (let i = 0; i < COUNT; i++) {
            for (let j = i + 1; j < COUNT; j++) {
                const dx = particles[i].x - particles[j].x;
                const dy = particles[i].y - particles[j].y;
                const dist = Math.sqrt(dx*dx + dy*dy);
                if (dist < 110) {
                    ctx.beginPath();
                    ctx.moveTo(particles[i].x, particles[i].y);
                    ctx.lineTo(particles[j].x, particles[j].y);
                    ctx.strokeStyle = `rgba(165,180,252,${0.12*(1-dist/110)})`;
                    ctx.lineWidth = 0.7;
                    ctx.stroke();
                }
            }
        }
        particles.forEach(p => {
            ctx.beginPath();
            ctx.arc(p.x, p.y, p.r, 0, Math.PI*2);
            ctx.fillStyle = `rgba(199,210,254,${p.o})`;
            ctx.fill();
            p.x += p.vx; p.y += p.vy;
            if (p.x < 0 || p.x > canvas.width)  p.vx *= -1;
            if (p.y < 0 || p.y > canvas.height) p.vy *= -1;
        });
        requestAnimationFrame(draw);
    }
    draw();
})();

/* ── Burbujas flotantes fondo login ── */
(function() {
    const canvas = document.getElementById('bubble-canvas');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');

    function resize() {
        canvas.width  = canvas.offsetWidth;
        canvas.height = canvas.offsetHeight;
    }
    resize();
    window.addEventListener('resize', resize);

    const COLORS = [
        'rgba(99,102,241,',   // indigo
        'rgba(124,58,237,',   // purple
        'rgba(165,180,252,',  // indigo claro
        'rgba(196,181,253,',  // purple claro
        'rgba(224,231,255,',  // lavender
    ];

    const bubbles = Array.from({ length: 18 }, () => makeBubble(canvas));

    function makeBubble(canvas, fromBottom = false) {
        const r = Math.random() * 38 + 12;
        return {
            x:     Math.random() * canvas.width,
            y:     fromBottom ? canvas.height + r : Math.random() * canvas.height,
            r:     r,
            vy:    -(Math.random() * 0.4 + 0.15),
            vx:    (Math.random() - 0.5) * 0.2,
            o:     Math.random() * 0.07 + 0.03,
            color: COLORS[Math.floor(Math.random() * COLORS.length)],
            wobble:      Math.random() * Math.PI * 2,
            wobbleSpeed: Math.random() * 0.012 + 0.004,
            wobbleAmp:   Math.random() * 18 + 6,
        };
    }

    function draw() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        bubbles.forEach((b, i) => {
            b.wobble += b.wobbleSpeed;
            const wx = b.x + Math.sin(b.wobble) * b.wobbleAmp;

            ctx.beginPath();
            ctx.arc(wx, b.y, b.r, 0, Math.PI * 2);
            ctx.fillStyle = b.color + b.o + ')';
            ctx.fill();

            // Brillo interior
            ctx.beginPath();
            ctx.arc(wx - b.r * 0.28, b.y - b.r * 0.28, b.r * 0.32, 0, Math.PI * 2);
            ctx.fillStyle = 'rgba(255,255,255,' + (b.o * 1.8) + ')';
            ctx.fill();

            // Borde sutil
            ctx.beginPath();
            ctx.arc(wx, b.y, b.r, 0, Math.PI * 2);
            ctx.strokeStyle = b.color + (b.o * 1.4) + ')';
            ctx.lineWidth = 0.8;
            ctx.stroke();

            b.y  += b.vy;
            b.x  += b.vx;

            // Rebotar horizontalmente
            if (b.x < -b.r) b.x = canvas.width + b.r;
            if (b.x > canvas.width + b.r) b.x = -b.r;

            // Cuando sale por arriba, renace desde abajo
            if (b.y + b.r < 0) {
                bubbles[i] = makeBubble(canvas, true);
            }
        });

        requestAnimationFrame(draw);
    }
    draw();
})();
</script>

</body>
</html>