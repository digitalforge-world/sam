<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Connexion') — {{ config('app.name', 'SAM Certification Bio') }}</title>
    <meta name="description" content="Système de gestion de certification biologique agricole — SAM">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        /* ══ Auth Layout — Premium Clean ══ */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --auth-primary: #1B6B4A;
            --auth-primary-light: #E8F5EE;
            --auth-primary-dark: #124832;
            --auth-accent: #22C55E;
            --auth-danger: #EF4444;
            --auth-text: #1A1D23;
            --auth-text-muted: #6B7280;
            --auth-border: #E5E7EB;
            --auth-bg: #F0F5F3;
            --auth-card: #FFFFFF;
            --auth-radius: 16px;
            --auth-shadow: 0 20px 60px rgba(27, 107, 74, 0.08), 0 4px 16px rgba(0,0,0,0.04);
            --font: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            font-family: var(--font);
            background: var(--auth-bg);
            color: var(--auth-text);
            min-height: 100vh;
            display: flex;
            -webkit-font-smoothing: antialiased;
        }

        /* ── Split layout ── */
        .auth-wrapper {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        /* ── Left Panel — Branding ── */
        .auth-brand {
            width: 45%;
            background: linear-gradient(160deg, #0D3B28 0%, #1B6B4A 40%, #22915F 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 60px 48px;
            position: relative;
            overflow: hidden;
        }

        .auth-brand::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -30%;
            width: 160%;
            height: 160%;
            background: radial-gradient(ellipse at 20% 50%, rgba(34,197,94,0.15) 0%, transparent 70%);
            pointer-events: none;
        }

        .auth-brand::after {
            content: '';
            position: absolute;
            bottom: -20%;
            right: -20%;
            width: 60%;
            height: 60%;
            background: radial-gradient(circle, rgba(255,255,255,0.04) 0%, transparent 70%);
            pointer-events: none;
        }

        /* Decorative leaf shapes */
        .brand-deco {
            position: absolute;
            opacity: 0.06;
        }
        .brand-deco-1 { top: 60px; right: 40px; width: 120px; height: 120px; border-radius: 50% 0 50% 0; border: 3px solid #fff; transform: rotate(30deg); }
        .brand-deco-2 { bottom: 80px; left: 30px; width: 80px; height: 80px; border-radius: 0 50% 0 50%; border: 3px solid #fff; transform: rotate(-15deg); }
        .brand-deco-3 { top: 30%; left: 15%; width: 50px; height: 50px; border-radius: 50%; background: rgba(255,255,255,0.08); }

        .brand-content {
            position: relative;
            z-index: 2;
            text-align: center;
            max-width: 380px;
        }

        .brand-logo {
            width: 72px;
            height: 72px;
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            margin: 0 auto 28px;
            border: 1px solid rgba(255,255,255,0.1);
            box-shadow: 0 8px 32px rgba(0,0,0,0.15);
        }

        .brand-title {
            font-size: 32px;
            font-weight: 800;
            color: #fff;
            line-height: 1.15;
            margin-bottom: 12px;
        }

        .brand-subtitle {
            font-size: 14px;
            color: rgba(255,255,255,0.65);
            line-height: 1.6;
            font-weight: 500;
        }

        .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255,255,255,0.12);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 50px;
            padding: 8px 18px;
            margin-top: 36px;
            font-size: 12px;
            font-weight: 600;
            color: rgba(255,255,255,0.85);
            letter-spacing: 0.02em;
        }

        .brand-badge .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #22C55E;
            animation: pulse-dot 2s ease-in-out infinite;
        }

        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(0.8); }
        }

        /* ── Right Panel — Form ── */
        .auth-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px 32px;
            position: relative;
        }

        .auth-card {
            width: 100%;
            max-width: 440px;
        }

        .auth-card-header {
            margin-bottom: 32px;
        }

        .auth-card-header .greeting {
            font-size: 13px;
            font-weight: 600;
            color: var(--auth-primary);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 8px;
        }

        .auth-card-header h1 {
            font-size: 28px;
            font-weight: 800;
            color: var(--auth-text);
            line-height: 1.2;
            margin-bottom: 8px;
        }

        .auth-card-header p {
            font-size: 14px;
            color: var(--auth-text-muted);
            line-height: 1.5;
        }

        /* ── Form Card ── */
        .auth-form-card {
            background: var(--auth-card);
            border-radius: var(--auth-radius);
            padding: 36px 32px;
            box-shadow: var(--auth-shadow);
            border: 1px solid rgba(0,0,0,0.04);
        }

        /* ── Form elements ── */
        .auth-form-group {
            margin-bottom: 22px;
        }

        .auth-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--auth-text);
            margin-bottom: 8px;
        }

        .auth-input-wrapper {
            position: relative;
        }

        .auth-input-wrapper .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            width: 18px;
            height: 18px;
            color: var(--auth-text-muted);
            pointer-events: none;
        }

        .auth-input {
            width: 100%;
            padding: 12px 14px 12px 44px;
            font-size: 14px;
            font-family: var(--font);
            color: var(--auth-text);
            background: #F8FAFC;
            border: 1.5px solid var(--auth-border);
            border-radius: 10px;
            transition: all 0.2s ease;
            outline: none;
        }

        .auth-input:focus {
            border-color: var(--auth-primary);
            box-shadow: 0 0 0 4px rgba(27, 107, 74, 0.1);
            background: #fff;
        }

        .auth-input::placeholder {
            color: #B0B7C3;
        }

        .auth-input.input-error {
            border-color: var(--auth-danger);
        }

        .auth-input.input-error:focus {
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.08);
        }

        /* Toggle password */
        .toggle-password {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: var(--auth-text-muted);
            padding: 4px;
            transition: color 0.15s;
        }
        .toggle-password:hover { color: var(--auth-primary); }

        .auth-error {
            font-size: 12px;
            color: var(--auth-danger);
            font-weight: 600;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* ── Checkbox ── */
        .auth-checkbox-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }

        .auth-checkbox-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: var(--auth-text-muted);
            cursor: pointer;
        }

        .auth-checkbox {
            width: 18px;
            height: 18px;
            border-radius: 5px;
            border: 2px solid var(--auth-border);
            accent-color: var(--auth-primary);
            cursor: pointer;
        }

        .auth-forgot-link {
            font-size: 13px;
            color: var(--auth-primary);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.15s;
        }
        .auth-forgot-link:hover { color: var(--auth-primary-dark); text-decoration: underline; }

        /* ── Submit button ── */
        .auth-submit {
            width: 100%;
            padding: 13px 24px;
            font-size: 15px;
            font-weight: 700;
            font-family: var(--font);
            color: #fff;
            background: linear-gradient(135deg, var(--auth-primary) 0%, #22915F 100%);
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            position: relative;
            overflow: hidden;
        }

        .auth-submit::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, transparent 0%, rgba(255,255,255,0.1) 100%);
            opacity: 0;
            transition: opacity 0.2s;
        }

        .auth-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(27, 107, 74, 0.35);
        }

        .auth-submit:hover::before { opacity: 1; }

        .auth-submit:active {
            transform: translateY(0);
            box-shadow: 0 4px 12px rgba(27, 107, 74, 0.25);
        }

        /* ── Footer link ── */
        .auth-footer {
            text-align: center;
            margin-top: 24px;
            font-size: 14px;
            color: var(--auth-text-muted);
        }

        .auth-footer a {
            color: var(--auth-primary);
            font-weight: 700;
            text-decoration: none;
            transition: color 0.15s;
        }
        .auth-footer a:hover { color: var(--auth-primary-dark); text-decoration: underline; }

        /* ── Flash / Session ── */
        .auth-flash {
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .auth-flash-success {
            background: #DCFCE7;
            color: #15803D;
            border: 1px solid #BBF7D0;
        }

        .auth-flash-info {
            background: #DBEAFE;
            color: #1D4ED8;
            border: 1px solid #BFDBFE;
        }

        /* ── Divider ── */
        .auth-divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 20px 0;
            color: var(--auth-text-muted);
            font-size: 12px;
            font-weight: 500;
        }
        .auth-divider::before,
        .auth-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--auth-border);
        }

        /* ── Copyright ── */
        .auth-copyright {
            position: absolute;
            bottom: 24px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 12px;
            color: var(--auth-text-muted);
            white-space: nowrap;
        }

        /* ══ SVG Icons inline ══ */
        .icon-svg { display: inline-block; vertical-align: middle; }

        /* ══ Responsive ══ */
        @media (max-width: 991px) {
            .auth-brand { display: none; }
            .auth-content { padding: 40px 20px; }
        }

        @media (max-width: 480px) {
            .auth-form-card { padding: 28px 22px; }
            .auth-card-header h1 { font-size: 24px; }
        }

        /* ── Subtle animation ── */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(18px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .auth-card { animation: fadeUp 0.5s ease-out; }
        .auth-form-card { animation: fadeUp 0.6s ease-out 0.1s both; }
    </style>
</head>
<body>
    <div class="auth-wrapper">
        {{-- ── Left Brand Panel ── --}}
        <div class="auth-brand">
            <div class="brand-deco brand-deco-1"></div>
            <div class="brand-deco brand-deco-2"></div>
            <div class="brand-deco brand-deco-3"></div>

            <div class="brand-content">
                <div class="brand-logo">🌿</div>
                <h1 class="brand-title">SAM<br>Certification Bio</h1>
                <p class="brand-subtitle">
                    Système intégré de gestion de la certification biologique agricole.
                    Traçabilité, contrôle et cartographie des parcelles.
                </p>
                <div class="brand-badge">
                    <span class="dot"></span>
                    Plateforme sécurisée
                </div>
            </div>
        </div>

        {{-- ── Right Content Panel ── --}}
        <div class="auth-content">
            <div class="auth-card">
                {{ $slot }}
            </div>
            <div class="auth-copyright">© {{ date('Y') }} SAM — Tous droits réservés</div>
        </div>
    </div>

    <script>
        // Toggle password visibility
        document.querySelectorAll('.toggle-password').forEach(btn => {
            btn.addEventListener('click', function() {
                const input = this.parentElement.querySelector('input');
                const isPassword = input.type === 'password';
                input.type = isPassword ? 'text' : 'password';
                this.innerHTML = isPassword
                    ? '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>'
                    : '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';
            });
        });
    </script>
</body>
</html>
