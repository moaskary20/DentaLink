<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ \App\Support\DentaLinkLocale::direction() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@lang('dentalink.home.page_title')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/dentalink.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dentalink-3d.css') }}">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Cairo', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            overflow-x: hidden;
        }

        .landing {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .landing-bg {
            position: fixed;
            inset: 0;
            z-index: 0;
            background:
                radial-gradient(ellipse 80% 60% at 20% 10%, rgba(10, 110, 189, 0.12) 0%, transparent 60%),
                radial-gradient(ellipse 60% 50% at 80% 90%, rgba(29, 168, 154, 0.10) 0%, transparent 55%),
                var(--bg);
        }

        .landing-bg::after {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(circle, rgba(10, 110, 189, 0.06) 1px, transparent 1px);
            background-size: 28px 28px;
        }

        .landing-header {
            position: relative;
            z-index: 1;
            padding: 28px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo {
            font-size: 26px;
            font-weight: 800;
            letter-spacing: -0.5px;
            color: var(--text);
        }

        .logo span { color: var(--primary); }

        .logo-sub {
            font-size: 12px;
            color: var(--text-muted);
            font-weight: 500;
            margin-top: 2px;
        }

        .lang-switcher {
            display: flex;
            gap: 8px;
            align-items: center;
            font-size: 13px;
        }

        .lang-switcher a {
            color: var(--text-muted);
            text-decoration: none;
            padding: 4px 10px;
            border-radius: 6px;
            font-weight: 600;
        }

        .lang-switcher a:hover,
        .lang-switcher a.active {
            color: var(--primary);
            background: var(--primary-light);
        }

        .landing-main {
            position: relative;
            z-index: 1;
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px 24px 64px;
        }

        .hero {
            text-align: center;
            max-width: 640px;
            margin-bottom: 48px;
            animation: fadeUp 0.7s ease both;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--primary-light);
            color: var(--primary);
            font-size: 12px;
            font-weight: 700;
            padding: 6px 14px;
            border-radius: 999px;
            margin-bottom: 20px;
        }

        .hero h1 {
            font-size: clamp(28px, 5vw, 42px);
            font-weight: 800;
            line-height: 1.25;
            margin-bottom: 14px;
            letter-spacing: -0.5px;
        }

        .hero p {
            font-size: 16px;
            color: var(--text-muted);
            line-height: 1.7;
        }

        .portals {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            width: 100%;
            max-width: 900px;
        }

        @media (max-width: 768px) {
            .portals { grid-template-columns: 1fr; max-width: 380px; }
        }

        .portal-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 36px 28px 32px;
            text-align: center;
            text-decoration: none;
            color: inherit;
            transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
            animation: fadeUp 0.7s ease both;
            position: relative;
            overflow: hidden;
        }

        .portal-card:nth-child(1) { animation-delay: 0.1s; }
        .portal-card:nth-child(2) { animation-delay: 0.2s; }
        .portal-card:nth-child(3) { animation-delay: 0.3s; }

        .portal-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            border-radius: var(--radius) var(--radius) 0 0;
            opacity: 0;
            transition: opacity 0.25s ease;
        }

        .portal-card:hover { border-color: transparent; }
        .portal-card:hover::before { opacity: 1; }

        .portal-card--admin::before { background: linear-gradient(90deg, #0A6EBD, #085A9A); }
        .portal-card--doctor::before { background: linear-gradient(90deg, #1DA89A, #159688); }
        .portal-card--lab::before { background: linear-gradient(90deg, #F4A932, #E09520); }

        .portal-icon {
            width: 72px;
            height: 72px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            transition: transform 0.25s ease;
        }

        .portal-card:hover .portal-icon { transform: scale(1.08); }

        .portal-icon--admin { background: var(--primary-light); color: var(--primary); }
        .portal-icon--doctor { background: var(--secondary-light); color: var(--secondary); }
        .portal-icon--lab { background: var(--accent-light); color: var(--accent); }

        .portal-icon svg { width: 36px; height: 36px; }

        .portal-title { font-size: 20px; font-weight: 800; margin-bottom: 8px; }
        .portal-desc { font-size: 13px; color: var(--text-muted); line-height: 1.6; margin-bottom: 20px; }

        .portal-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            font-weight: 700;
            padding: 10px 20px;
            border-radius: var(--radius-sm);
            transition: background 0.2s, color 0.2s;
        }

        .portal-card--admin .portal-btn { background: var(--primary-light); color: var(--primary); }
        .portal-card--doctor .portal-btn { background: var(--secondary-light); color: var(--secondary); }
        .portal-card--lab .portal-btn { background: var(--accent-light); color: #C07D10; }

        .portal-card--admin:hover .portal-btn { background: var(--primary); color: #fff; }
        .portal-card--doctor:hover .portal-btn { background: var(--secondary); color: #fff; }
        .portal-card--lab:hover .portal-btn { background: var(--accent); color: #fff; }

        .register-links {
            margin-top: 32px;
            text-align: center;
            font-size: 13px;
            color: var(--text-muted);
            animation: fadeUp 0.7s ease 0.4s both;
        }

        .register-links a {
            color: var(--primary);
            font-weight: 600;
            text-decoration: none;
        }

        .register-links a:hover { text-decoration: underline; }

        .landing-footer {
            position: relative;
            z-index: 1;
            text-align: center;
            padding: 20px;
            font-size: 12px;
            color: var(--text-muted);
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(24px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="landing">
        <div class="landing-bg"></div>
        <div class="landing-3d-shapes" aria-hidden="true">
            <div class="dl-shape dl-shape--cube"></div>
            <div class="dl-shape dl-shape--ring"></div>
            <div class="dl-shape dl-shape--pill"></div>
        </div>

        <header class="landing-header">
            <div>
                <div class="logo">Denta<span>Link</span></div>
                <div class="logo-sub">@lang('dentalink.home.logo_sub')</div>
            </div>
            <nav class="lang-switcher" aria-label="@lang('dentalink.home.language')">
                @foreach (\App\Support\DentaLinkLocale::SUPPORTED as $locale)
                    <a href="{{ url('/locale/'.$locale) }}" @class(['active' => app()->getLocale() === $locale])>
                        {{ strtoupper($locale) }}
                    </a>
                @endforeach
            </nav>
        </header>

        <main class="landing-main">
            <div class="hero">
                <div class="hero-badge">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                    @lang('dentalink.home.hero_badge')
                </div>
                <h1>@lang('dentalink.home.hero_title')</h1>
                <p>@lang('dentalink.home.hero_subtitle')</p>
            </div>

            <div class="portals">
                <a href="{{ url('/admin/login') }}" class="portal-card portal-card--admin">
                    <div class="portal-icon portal-icon--admin">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            <path d="M9 12l2 2 4-4"/>
                        </svg>
                    </div>
                    <div class="portal-title">@lang('dentalink.home.portal_admin_title')</div>
                    <div class="portal-desc">@lang('dentalink.home.portal_admin_desc')</div>
                    <span class="portal-btn">@lang('dentalink.home.portal_admin_btn')</span>
                </a>

                <a href="{{ url('/doctor/login') }}" class="portal-card portal-card--doctor">
                    <div class="portal-icon portal-icon--doctor">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
                        </svg>
                    </div>
                    <div class="portal-title">@lang('dentalink.home.portal_doctor_title')</div>
                    <div class="portal-desc">@lang('dentalink.home.portal_doctor_desc')</div>
                    <span class="portal-btn">@lang('dentalink.home.portal_doctor_btn')</span>
                </a>

                <a href="{{ url('/lab/login') }}" class="portal-card portal-card--lab">
                    <div class="portal-icon portal-icon--lab">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 3h6v7.5l4.5 7.5a3 3 0 0 1-2.6 4.5H7.1a3 3 0 0 1-2.6-4.5L9 10.5V3z"/>
                            <line x1="9" y1="3" x2="15" y2="3"/>
                            <line x1="7" y1="14" x2="17" y2="14"/>
                        </svg>
                    </div>
                    <div class="portal-title">@lang('dentalink.home.portal_lab_title')</div>
                    <div class="portal-desc">@lang('dentalink.home.portal_lab_desc')</div>
                    <span class="portal-btn">@lang('dentalink.home.portal_lab_btn')</span>
                </a>
            </div>

            <div class="register-links">
                @lang('dentalink.home.register_prompt')
                <a href="{{ url('/doctor/register') }}">@lang('dentalink.home.register_doctor')</a>
                ·
                <a href="{{ url('/lab/register') }}">@lang('dentalink.home.register_lab')</a>
            </div>
        </main>

        <footer class="landing-footer">
            @lang('dentalink.home.footer', ['year' => date('Y')])
        </footer>
    </div>
    <script src="{{ asset('js/dentalink-3d.js') }}" defer></script>
</body>
</html>
