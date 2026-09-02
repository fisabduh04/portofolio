<x-layout.auth :logo="$sekolah?->logo_url ?? asset('img/logo.png')">
    <!DOCTYPE html>
    <html lang="id">

    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Masuk — {{ $sekolah?->nama_sekolah ?? 'Sistem Informasi Sekolah' }}</title>
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link
            href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,400;0,9..144,600;1,9..144,300&family=DM+Sans:wght@300;400;500&display=swap"
            rel="stylesheet" />
        <style>
            *,
            *::before,
            *::after {
                box-sizing: border-box;
                margin: 0;
                padding: 0;
            }

            :root {
                --navy: #0f1f3d;
                --navy-mid: #1a305a;
                --gold: #c9973a;
                --gold-light: #e8b95a;
                --cream: #f9f6f0;
                --text-main: #1a1a2e;
                --text-muted: #6b7280;
                --border: #e5e0d8;
                --white: #ffffff;
                --error: #dc2626;
                --success: #059669;
                --input-bg: #ffffff;
                --input-border: #d1cdc4;
                --input-focus: #c9973a;
                --radius: 10px;
                --shadow-card: 0 24px 64px rgba(15, 31, 61, 0.10), 0 4px 16px rgba(15, 31, 61, 0.06);
            }

            html,
            body {
                height: 100%;
                overflow: hidden;
            }

            body {
                font-family: 'DM Sans', sans-serif;
                background: var(--cream);
                display: flex;
                min-height: 100vh;
            }

            /* ── LEFT PANEL: Slideshow ── */
            .left-panel {
                position: relative;
                flex: 0 0 55%;
                overflow: hidden;
                display: none;
            }

            @media (min-width: 900px) {
                .left-panel {
                    display: block;
                }
            }

            .slide {
                position: absolute;
                inset: 0;
                background-size: cover;
                background-position: center;
                opacity: 0;
                transition: opacity 1.2s ease;
            }

            .slide.active {
                opacity: 1;
            }

            /* Fallback gradient slides when no real images */
            .slide-1 {
                background-image: linear-gradient(135deg, #0f1f3d 0%, #1e3a6e 40%, #2d5a9e 100%);
            }

            .slide-2 {
                background-image: linear-gradient(135deg, #0d2137 0%, #164e63 50%, #0f766e 100%);
            }

            .slide-3 {
                background-image: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #4338ca 100%);
            }

            .slide-4 {
                background-image: linear-gradient(135deg, #14532d 0%, #166534 50%, #15803d 100%);
            }

            /* Overlay */
            .slide-overlay {
                position: absolute;
                inset: 0;
                background: linear-gradient(to bottom,
                        rgba(10, 20, 45, 0.35) 0%,
                        rgba(10, 20, 45, 0.15) 40%,
                        rgba(10, 20, 45, 0.70) 80%,
                        rgba(10, 20, 45, 0.88) 100%);
                z-index: 1;
            }

            /* Decorative pattern overlay */
            .slide-pattern {
                position: absolute;
                inset: 0;
                z-index: 1;
                background-image:
                    radial-gradient(circle at 20% 80%, rgba(201, 151, 58, 0.12) 0%, transparent 50%),
                    radial-gradient(circle at 80% 20%, rgba(255, 255, 255, 0.06) 0%, transparent 40%);
            }

            /* School info at bottom of slide */
            .slide-content {
                position: absolute;
                bottom: 0;
                left: 0;
                right: 0;
                padding: 3rem 3.5rem;
                z-index: 2;
            }

            .slide-badge {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                background: rgba(201, 151, 58, 0.18);
                border: 1px solid rgba(201, 151, 58, 0.4);
                color: #e8b95a;
                font-size: 11px;
                font-weight: 500;
                letter-spacing: 1.5px;
                text-transform: uppercase;
                padding: 6px 14px;
                border-radius: 100px;
                margin-bottom: 1.25rem;
                backdrop-filter: blur(4px);
            }

            .slide-badge::before {
                content: '';
                width: 6px;
                height: 6px;
                border-radius: 50%;
                background: #e8b95a;
                animation: blink 2s ease infinite;
            }

            @keyframes blink {

                0%,
                100% {
                    opacity: 1;
                }

                50% {
                    opacity: 0.3;
                }
            }

            .slide-title {
                font-family: 'Fraunces', Georgia, serif;
                font-size: clamp(1.8rem, 2.8vw, 2.6rem);
                font-weight: 300;
                color: #ffffff;
                line-height: 1.2;
                margin-bottom: 0.75rem;
                letter-spacing: -0.02em;
            }

            .slide-title em {
                font-style: italic;
                color: var(--gold-light);
            }

            .slide-subtitle {
                font-size: 0.95rem;
                color: rgba(255, 255, 255, 0.7);
                line-height: 1.6;
                max-width: 380px;
            }

            /* Stats row */
            .slide-stats {
                display: flex;
                gap: 2rem;
                margin-top: 1.75rem;
                padding-top: 1.5rem;
                border-top: 1px solid rgba(255, 255, 255, 0.12);
            }

            .stat-item {}

            .stat-value {
                font-family: 'Fraunces', serif;
                font-size: 1.6rem;
                font-weight: 600;
                color: var(--gold-light);
                line-height: 1;
            }

            .stat-label {
                font-size: 0.7rem;
                font-weight: 500;
                color: rgba(255, 255, 255, 0.5);
                text-transform: uppercase;
                letter-spacing: 0.08em;
                margin-top: 4px;
            }

            /* School logo top-left */
            .slide-logo {
                position: absolute;
                top: 2.5rem;
                left: 3rem;
                z-index: 3;
                display: flex;
                align-items: center;
                gap: 12px;
            }

            .slide-logo img {
                width: 44px;
                height: 44px;
                object-fit: contain;
                filter: brightness(0) invert(1) drop-shadow(0 2px 8px rgba(0, 0, 0, 0.3));
            }

            .slide-logo-text {
                font-family: 'Fraunces', serif;
                font-size: 0.95rem;
                font-weight: 400;
                color: rgba(255, 255, 255, 0.9);
                max-width: 200px;
                line-height: 1.3;
            }

            /* Slide indicators */
            .slide-dots {
                position: absolute;
                bottom: 2rem;
                right: 3rem;
                z-index: 3;
                display: flex;
                gap: 6px;
                align-items: center;
            }

            .dot {
                width: 6px;
                height: 6px;
                border-radius: 100px;
                background: rgba(255, 255, 255, 0.35);
                cursor: pointer;
                transition: all 0.3s ease;
                border: none;
            }

            .dot.active {
                width: 22px;
                background: var(--gold-light);
            }

            /* ── RIGHT PANEL: Login Form ── */
            .right-panel {
                flex: 1;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 2rem;
                overflow-y: auto;
                background: var(--cream);
            }

            .login-card {
                width: 100%;
                max-width: 420px;
            }

            /* Mobile logo */
            .mobile-brand {
                display: flex;
                flex-direction: column;
                align-items: center;
                margin-bottom: 2rem;
            }

            @media (min-width: 900px) {
                .mobile-brand {
                    display: none;
                }
            }

            .mobile-brand img {
                width: 56px;
                height: 56px;
                object-fit: contain;
                margin-bottom: 0.75rem;
            }

            .mobile-brand-name {
                font-family: 'Fraunces', serif;
                font-size: 1rem;
                font-weight: 400;
                color: var(--navy);
                text-align: center;
            }

            /* Header */
            .login-header {
                margin-bottom: 2.25rem;
            }

            .login-eyebrow {
                font-size: 11px;
                font-weight: 500;
                letter-spacing: 1.8px;
                text-transform: uppercase;
                color: var(--gold);
                margin-bottom: 0.6rem;
            }

            .login-title {
                font-family: 'Fraunces', Georgia, serif;
                font-size: 2rem;
                font-weight: 300;
                color: var(--navy);
                line-height: 1.15;
                letter-spacing: -0.025em;
            }

            .login-title em {
                font-style: italic;
                color: var(--gold);
            }

            .login-desc {
                font-size: 0.875rem;
                color: var(--text-muted);
                margin-top: 0.6rem;
                line-height: 1.6;
            }

            /* Alert */
            .alert-success {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 12px 16px;
                background: #ecfdf5;
                border: 1px solid #a7f3d0;
                border-radius: var(--radius);
                color: var(--success);
                font-size: 0.875rem;
                margin-bottom: 1.5rem;
            }

            .alert-success svg {
                flex-shrink: 0;
            }

            /* Form */
            .form-group {
                margin-bottom: 1.25rem;
            }

            label {
                display: block;
                font-size: 0.8125rem;
                font-weight: 500;
                color: var(--text-main);
                margin-bottom: 0.5rem;
                letter-spacing: 0.01em;
            }

            .input-wrapper {
                position: relative;
            }

            .input-icon {
                position: absolute;
                left: 14px;
                top: 50%;
                transform: translateY(-50%);
                color: #9ca3af;
                pointer-events: none;
                transition: color 0.2s;
            }

            input[type="email"],
            input[type="password"],
            input[type="text"] {
                width: 100%;
                height: 48px;
                padding: 0 44px 0 42px;
                background: var(--input-bg);
                border: 1.5px solid var(--input-border);
                border-radius: var(--radius);
                font-family: 'DM Sans', sans-serif;
                font-size: 0.9rem;
                color: var(--text-main);
                outline: none;
                transition: border-color 0.2s, box-shadow 0.2s;
                appearance: none;
                -webkit-appearance: none;
            }

            input:focus {
                border-color: var(--input-focus);
                box-shadow: 0 0 0 3px rgba(201, 151, 58, 0.12);
            }

            input:focus+.input-icon,
            .input-wrapper:focus-within .input-icon {
                color: var(--gold);
            }

            /* Fix icon placement on focus-within */
            .input-wrapper:focus-within .input-icon {
                color: var(--gold);
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
                color: #9ca3af;
                padding: 4px;
                display: flex;
                align-items: center;
                transition: color 0.2s;
            }

            .toggle-password:hover {
                color: var(--navy);
            }

            /* Field error */
            .field-error {
                font-size: 0.75rem;
                color: var(--error);
                margin-top: 5px;
                display: flex;
                align-items: center;
                gap: 4px;
            }

            input.is-error {
                border-color: var(--error);
            }

            input.is-error:focus {
                box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
            }

            /* Remember + Forgot */
            .form-footer {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 1.75rem;
            }

            .checkbox-label {
                display: flex;
                align-items: center;
                gap: 8px;
                cursor: pointer;
                font-size: 0.8125rem;
                color: var(--text-muted);
                user-select: none;
            }

            .checkbox-label input[type="checkbox"] {
                width: 16px;
                height: 16px;
                padding: 0;
                border: 1.5px solid var(--input-border);
                border-radius: 4px;
                accent-color: var(--gold);
                cursor: pointer;
            }

            .forgot-link {
                font-size: 0.8125rem;
                font-weight: 500;
                color: var(--gold);
                text-decoration: none;
                transition: color 0.2s;
            }

            .forgot-link:hover {
                color: #a57c28;
            }

            /* Submit button */
            .btn-login {
                width: 100%;
                height: 50px;
                background: var(--navy);
                color: #ffffff;
                border: none;
                border-radius: var(--radius);
                font-family: 'DM Sans', sans-serif;
                font-size: 0.9375rem;
                font-weight: 500;
                letter-spacing: 0.01em;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 10px;
                transition: background 0.25s, transform 0.15s, box-shadow 0.25s;
                position: relative;
                overflow: hidden;
            }

            .btn-login::after {
                content: '';
                position: absolute;
                inset: 0;
                background: linear-gradient(135deg, transparent 30%, rgba(255, 255, 255, 0.06) 100%);
            }

            .btn-login:hover {
                background: var(--navy-mid);
                box-shadow: 0 8px 24px rgba(15, 31, 61, 0.22);
                transform: translateY(-1px);
            }

            .btn-login:active {
                transform: translateY(0);
                box-shadow: none;
            }

            .btn-login svg {
                flex-shrink: 0;
            }

            /* Divider */
            .divider {
                display: flex;
                align-items: center;
                gap: 12px;
                margin: 1.5rem 0;
                color: var(--text-muted);
                font-size: 0.75rem;
            }

            .divider::before,
            .divider::after {
                content: '';
                flex: 1;
                height: 1px;
                background: var(--border);
            }

            /* Footer note */
            .login-footer {
                text-align: center;
                font-size: 0.75rem;
                color: var(--text-muted);
                margin-top: 2rem;
                line-height: 1.6;
            }

            .login-footer a {
                color: var(--gold);
                text-decoration: none;
                font-weight: 500;
            }

            .help-links {
                display: flex;
                justify-content: center;
                gap: 1.5rem;
                margin-top: 1.25rem;
            }

            .help-link {
                font-size: 0.75rem;
                color: var(--text-muted);
                text-decoration: none;
                display: flex;
                align-items: center;
                gap: 4px;
                transition: color 0.2s;
            }

            .help-link:hover {
                color: var(--navy);
            }

            /* Loading state */
            @keyframes spin {
                to {
                    transform: rotate(360deg);
                }
            }

            .spinner {
                width: 18px;
                height: 18px;
                border: 2px solid rgba(255, 255, 255, 0.3);
                border-top-color: white;
                border-radius: 50%;
                animation: spin 0.7s linear infinite;
                display: none;
            }

            .btn-login.loading .btn-text {
                display: none;
            }

            .btn-login.loading .spinner {
                display: block;
            }
        </style>
    </head>

    <body>

        {{-- ── LEFT PANEL ── --}}
        <div class="left-panel" id="slideshow">

            {{-- Slides — swap background-image for real school photos --}}
            <div class="slide slide-1 active" style="
            {{-- background-image: url('{{ asset('img/sekolah-1.jpg') }}'); --}}
        "></div>
            <div class="slide slide-2" style="
            {{-- background-image: url('{{ asset('img/sekolah-2.jpg') }}'); --}}
        "></div>
            <div class="slide slide-3" style="
            {{-- background-image: url('{{ asset('img/sekolah-3.jpg') }}'); --}}
        "></div>
            <div class="slide slide-4" style="
            {{-- background-image: url('{{ asset('img/sekolah-4.jpg') }}'); --}}
        "></div>

            <div class="slide-pattern"></div>
            <div class="slide-overlay"></div>

            {{-- School logo --}}
            <div class="slide-logo">
                <img src="{{ $sekolah?->logo_url ?? asset('img/logo.png') }}" alt="Logo" />
                <span class="slide-logo-text">{{ $sekolah?->nama_sekolah ?? 'Sistem Informasi Sekolah' }}</span>
            </div>

            {{-- Bottom content --}}
            <div class="slide-content">
                <div class="slide-badge">Portal Akademik</div>

                <h1 class="slide-title">
                    Membangun <em>generasi</em><br>yang berprestasi
                </h1>
                <p class="slide-subtitle">
                    Platform digital terpadu untuk mendukung proses belajar-mengajar yang lebih efektif, efisien, dan
                    terukur.
                </p>

                <div class="slide-stats">
                    <div class="stat-item">
                        <div class="stat-value">1.200+</div>
                        <div class="stat-label">Siswa Aktif</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">85+</div>
                        <div class="stat-label">Tenaga Pengajar</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">30+</div>
                        <div class="stat-label">Tahun Berdiri</div>
                    </div>
                </div>
            </div>

            {{-- Dots --}}
            <div class="slide-dots" id="dots">
                <button class="dot active" data-index="0" aria-label="Slide 1"></button>
                <button class="dot" data-index="1" aria-label="Slide 2"></button>
                <button class="dot" data-index="2" aria-label="Slide 3"></button>
                <button class="dot" data-index="3" aria-label="Slide 4"></button>
            </div>
        </div>

        {{-- ── RIGHT PANEL ── --}}
        <div class="right-panel">
            <div class="login-card">

                {{-- Mobile brand --}}
                <div class="mobile-brand">
                    <img src="{{ $sekolah?->logo_url ?? asset('img/logo.png') }}" alt="Logo" />
                    <div class="mobile-brand-name">{{ $sekolah?->nama_sekolah ?? 'Sistem Informasi Sekolah' }}</div>
                </div>

                {{-- Header --}}
                <div class="login-header">
                    <div class="login-eyebrow">Portal Akademik</div>
                    <h2 class="login-title">Selamat <em>datang</em> kembali</h2>
                    <p class="login-desc">Masuk untuk mengakses sistem informasi sekolah Anda.</p>
                </div>

                {{-- Status Alert --}}
                @if (session('status'))
                    <div class="alert-success">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ session('status') }}
                    </div>
                @endif

                {{-- Form --}}
                <form action="/login" method="POST" id="loginForm" novalidate>
                    @csrf

                    {{-- Email --}}
                    <div class="form-group">
                        <label for="email">Alamat Email</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" width="16" height="16" fill="none" stroke="currentColor"
                                stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <input id="email" name="email" type="email" value="{{ old('email') }}"
                                placeholder="nama@email.com" autocomplete="email" autofocus
                                class="{{ $errors->has('email') ? 'is-error' : '' }}" />
                        </div>
                        @error('email')
                            <div class="field-error">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor">
                                    <path
                                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" />
                                </svg>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="form-group">
                        <label for="password">Kata Sandi</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" width="16" height="16" fill="none"
                                stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            <input id="password" name="password" type="password" placeholder="••••••••"
                                autocomplete="current-password"
                                class="{{ $errors->has('password') ? 'is-error' : '' }}" />
                            <button type="button" class="toggle-password" onclick="togglePassword()"
                                aria-label="Tampilkan kata sandi">
                                <svg id="eye-icon" width="16" height="16" fill="none"
                                    stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <div class="field-error">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor">
                                    <path
                                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" />
                                </svg>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Remember + Forgot --}}
                    <div class="form-footer">
                        <label class="checkbox-label">
                            <input type="checkbox" name="remember" id="remember" />
                            Ingat saya
                        </label>
                        <a href="{{ route('password.request') }}" class="forgot-link">Lupa kata sandi?</a>
                    </div>

                    {{-- Submit --}}
                    <button type="submit" class="btn-login" id="btnLogin">
                        <span class="btn-text" style="display:flex;align-items:center;gap:10px;">
                            <svg width="18" height="18" fill="none" stroke="currentColor"
                                stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                            </svg>
                            Masuk ke Sistem
                        </span>
                        <div class="spinner"></div>
                    </button>
                </form>

                {{-- Footer --}}
                <div class="login-footer">
                    <p>Sistem Informasi Sekolah &copy; {{ date('Y') }}</p>
                    <div class="help-links">
                        <a href="#" class="help-link">
                            <svg width="13" height="13" fill="none" stroke="currentColor"
                                stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Bantuan
                        </a>
                        <a href="#" class="help-link">
                            <svg width="13" height="13" fill="none" stroke="currentColor"
                                stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            Kebijakan Privasi
                        </a>
                    </div>
                </div>

            </div>
        </div>

        <script>
            // ── Slideshow ──
            const slides = document.querySelectorAll('.slide');
            const dots = document.querySelectorAll('.dot');
            let current = 0;
            let timer;

            function goTo(n) {
                slides[current].classList.remove('active');
                dots[current].classList.remove('active');
                current = n;
                slides[current].classList.add('active');
                dots[current].classList.add('active');
            }

            function next() {
                goTo((current + 1) % slides.length);
            }

            function startTimer() {
                clearInterval(timer);
                timer = setInterval(next, 5000);
            }

            dots.forEach(dot => {
                dot.addEventListener('click', () => {
                    goTo(parseInt(dot.dataset.index));
                    startTimer();
                });
            });

            startTimer();

            // ── Toggle password ──
            function togglePassword() {
                const pw = document.getElementById('password');
                const icon = document.getElementById('eye-icon');
                const isHidden = pw.type === 'password';
                pw.type = isHidden ? 'text' : 'password';
                icon.innerHTML = isHidden ?
                    '<path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>' :
                    '<path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
            }

            // ── Loading state on submit ──
            document.getElementById('loginForm').addEventListener('submit', function() {
                document.getElementById('btnLogin').classList.add('loading');
            });
        </script>

    </body>

    </html>
</x-layout.auth>
