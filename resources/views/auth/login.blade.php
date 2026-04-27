<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Afghan POS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600&family=Playfair+Display:ital,wght@0,700;1,400&display=swap"
        rel="stylesheet">
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --ink: #111318;
            --ink-soft: #4b5060;
            --ink-faint: #9299aa;
            --surface: #ffffff;
            --surface-2: #f5f6f9;
            --surface-3: #edeef3;
            --border: #e0e2ea;
            --accent: #1d6bf3;
            --accent-dk: #1458d4;
            --accent-2: #7c3aed;
            --success: #0fa870;
            --danger: #e03b3b;
            --radius: 12px;
            --radius-sm: 8px;
        }

        html,
        body {
            height: 100%;
            overflow: hidden;
            font-family: 'Sora', sans-serif;
            background: var(--surface);
        }

        /* ══════════════════════════════
           SPLIT LAYOUT
        ══════════════════════════════ */
        .split {
            display: flex;
            height: 100vh;
            width: 100vw;
            overflow: hidden;
        }

        /* ── LEFT PANEL — image side ── */
        .left-panel {
            flex: 1;
            position: relative;
            overflow: hidden;
        }

        /* Unsplash retail/market image — royalty free */
        .left-panel .bg-img {
            position: absolute;
            inset: 0;
            background:
                url('https://images.unsplash.com/photo-1604719312566-8912e9227c6a?w=1200&auto=format&fit=crop&q=80') center center / cover no-repeat;
        }

        /* Rich overlay: dark bottom + brand tint */
        .left-panel .overlay {
            position: absolute;
            inset: 0;
            background:
                linear-gradient(160deg,
                    rgba(15, 28, 60, .72) 0%,
                    rgba(29, 107, 243, .45) 40%,
                    rgba(10, 10, 20, .82) 100%);
        }

        /* Decorative floating shapes */
        .left-panel .deco {
            position: absolute;
            inset: 0;
            overflow: hidden;
            pointer-events: none;
        }

        .left-panel .deco::before {
            content: '';
            position: absolute;
            width: 360px;
            height: 360px;
            border-radius: 50%;
            border: 1px solid rgba(255, 255, 255, .1);
            top: -80px;
            left: -80px;
        }

        .left-panel .deco::after {
            content: '';
            position: absolute;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            border: 1px solid rgba(255, 255, 255, .08);
            bottom: 80px;
            right: -50px;
        }

        /* Content over the left image */
        .left-content {
            position: relative;
            z-index: 2;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 2.75rem 3rem;
        }

        .left-logo {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .left-logo-icon {
            width: 42px;
            height: 42px;
            background: rgba(255, 255, 255, .18);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, .25);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .left-logo-name {
            font-family: 'Playfair Display', serif;
            font-size: 22px;
            color: #fff;
            letter-spacing: -.3px;
        }

        .left-tagline {
            margin-bottom: 3rem;
        }

        .left-tagline h2 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(28px, 3.5vw, 44px);
            color: #fff;
            line-height: 1.2;
            letter-spacing: -.5px;
            margin-bottom: 1rem;
        }

        .left-tagline h2 em {
            font-style: italic;
            color: rgba(255, 255, 255, .7);
        }

        .left-tagline p {
            font-size: 14px;
            color: rgba(255, 255, 255, .6);
            line-height: 1.7;
            max-width: 340px;
        }

        /* Stats bar at bottom */
        .left-stats {
            display: flex;
            gap: 2rem;
        }

        .stat {
            border-left: 2px solid rgba(255, 255, 255, .25);
            padding-left: 14px;
        }

        .stat-num {
            font-size: 22px;
            font-weight: 600;
            color: #fff;
            letter-spacing: -.5px;
        }

        .stat-label {
            font-size: 11px;
            color: rgba(255, 255, 255, .5);
            letter-spacing: .06em;
            text-transform: uppercase;
            margin-top: 2px;
        }

        /* ── RIGHT PANEL — form side ── */
        .right-panel {
            width: 440px;
            flex-shrink: 0;
            background: var(--surface);
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 1.5rem 2.5rem;
            overflow: hidden;
            border-left: 1px solid var(--border);
            position: relative;
        }

        /* subtle top accent bar */
        .right-panel::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--accent), var(--accent-2));
        }

        /* ── Form header ── */
        .form-heading {
            margin-bottom: 1.25rem;
        }

        .form-heading .welcome {
            font-size: 11px;
            font-weight: 600;
            color: var(--accent);
            letter-spacing: .1em;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .form-heading h1 {
            font-family: 'Playfair Display', serif;
            font-size: 24px;
            color: var(--ink);
            letter-spacing: -.4px;
            line-height: 1.15;
            margin-bottom: 4px;
        }

        .form-heading p {
            font-size: 12.5px;
            color: var(--ink-faint);
        }

        /* ── Tabs ── */
        .tabs {
            display: flex;
            gap: 0;
            background: var(--surface-2);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 4px;
            margin-bottom: 1.25rem;
        }

        .tab-btn {
            flex: 1;
            padding: 9px 10px;
            font-family: 'Sora', sans-serif;
            font-size: 12.5px;
            font-weight: 500;
            border: none;
            cursor: pointer;
            background: transparent;
            color: var(--ink-faint);
            border-radius: 9px;
            transition: all .2s;
            white-space: nowrap;
        }

        .tab-btn.active {
            background: var(--surface);
            color: var(--ink);
            box-shadow: 0 1px 5px rgba(0, 0, 0, .1);
        }

        /* ── Alert ── */
        .alert {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #fff5f5;
            border: 1px solid #fcd4d4;
            border-left: 3px solid var(--danger);
            border-radius: var(--radius-sm);
            padding: 11px 14px;
            margin-bottom: 1.5rem;
            font-size: 13px;
            color: var(--danger);
        }

        /* ── Fields ── */
        .field {
            margin-bottom: .85rem;
        }

        .field label {
            display: block;
            font-size: 11.5px;
            font-weight: 600;
            color: var(--ink-soft);
            letter-spacing: .06em;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .input-wrap {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 15px;
            color: var(--ink-faint);
            pointer-events: none;
        }

        input[type=text],
        input[type=password] {
            width: 100%;
            padding: 11px 14px 11px 40px;
            font-family: 'Sora', sans-serif;
            font-size: 14px;
            background: var(--surface-2);
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            color: var(--ink);
            outline: none;
            transition: border .18s, box-shadow .18s, background .18s;
        }

        input[type=text]:focus,
        input[type=password]:focus {
            border-color: var(--accent);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(29, 107, 243, .1);
        }

        input::placeholder {
            color: #bbbfc9;
            font-size: 13px;
        }

        .eye-btn {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: var(--ink-faint);
            font-size: 17px;
            transition: color .15s;
            line-height: 1;
        }

        .eye-btn:hover {
            color: var(--ink);
        }

        .forgot-row {
            text-align: right;
            margin-top: -2px;
            margin-bottom: 1rem;
        }

        .forgot-link {
            font-size: 12px;
            color: var(--accent);
            font-weight: 500;
            text-decoration: none;
        }

        .forgot-link:hover {
            text-decoration: underline;
        }

        /* ── Buttons ── */
        .btn {
            width: 100%;
            padding: 12px;
            font-family: 'Sora', sans-serif;
            font-size: 14px;
            font-weight: 600;
            border: none;
            border-radius: var(--radius-sm);
            cursor: pointer;
            transition: all .2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-blue {
            background: var(--accent);
            color: #fff;
            box-shadow: 0 4px 16px rgba(29, 107, 243, .3);
        }

        .btn-blue:hover {
            background: var(--accent-dk);
            box-shadow: 0 6px 20px rgba(29, 107, 243, .4);
            transform: translateY(-1px);
        }

        .btn-blue:active {
            transform: scale(.98);
        }

        .btn-purple {
            background: var(--accent-2);
            color: #fff;
            box-shadow: 0 4px 16px rgba(124, 58, 237, .3);
        }

        .btn-purple:hover {
            background: #6d2fd8;
            box-shadow: 0 6px 20px rgba(124, 58, 237, .4);
            transform: translateY(-1px);
        }

        .btn-purple:active {
            transform: scale(.98);
        }

        /* ── PIN boxes ── */
        .pin-label {
            font-size: 11.5px;
            font-weight: 600;
            color: var(--ink-soft);
            letter-spacing: .06em;
            text-transform: uppercase;
            margin-bottom: 14px;
        }

        .pin-boxes {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 1.25rem;
        }

        .pin-box {
            width: 64px;
            height: 60px;
            text-align: center;
            font-family: 'Sora', sans-serif;
            font-size: 20px;
            font-weight: 600;
            background: var(--surface-2);
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            color: var(--ink);
            outline: none;
            transition: border .18s, box-shadow .18s, background .18s;
            caret-color: var(--accent-2);
        }

        .pin-box:focus {
            border-color: var(--accent-2);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(124, 58, 237, .1);
        }

        .pin-box.filled {
            border-color: var(--accent-2);
            background: #f9f5ff;
        }

        /* ── Language ── */
        .lang-row {
            margin-top: 1rem;
            display: flex;
            justify-content: center;
        }

        .lang-select {
            padding: 7px 28px 7px 12px;
            font-family: 'Sora', sans-serif;
            font-size: 12px;
            background: var(--surface-2);
            border: 1px solid var(--border);
            border-radius: 99px;
            color: var(--ink-soft);
            outline: none;
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 10 10'%3E%3Cpath fill='%239299aa' d='M5 7L0 2h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
            transition: border .15s;
        }

        .lang-select:focus {
            border-color: var(--accent);
        }

        /* ── Panel toggle ── */
        .panel {
            display: none;
        }

        .panel.active {
            display: block;
        }

        /* ── Footer ── */
        .right-footer {
            margin-top: 1rem;
            font-size: 11px;
            color: var(--ink-faint);
            text-align: center;
        }

        /* ── Responsive ── */
        @media (max-width: 820px) {
            .split {
                flex-direction: column;
            }

            .left-panel {
                height: 220px;
                flex: none;
            }

            .left-tagline,
            .left-stats {
                display: none;
            }

            .right-panel {
                width: 100%;
                flex: 1;
                padding: 2rem 1.75rem;
                overflow-y: auto;
            }

            html,
            body {
                overflow: auto;
            }
        }

        @media (max-width: 400px) {
            .right-panel {
                padding: 1.5rem 1.25rem;
            }

            .pin-box {
                width: 54px;
                height: 54px;
            }
        }
    </style>
</head>

<body>

    <div class="split">

        {{-- ═══════════════════════
         LEFT — image + branding
    ═══════════════════════ --}}
        <div class="left-panel">
            <div class="bg-img"></div>
            <div class="overlay"></div>
            <div class="deco"></div>

            <div class="left-content">
                {{-- Logo --}}
                <svg width="260" height="72" viewBox="0 0 260 72" xmlns="http://www.w3.org/2000/svg">
                    <!--
    Afghan POS — Horizontal Logo
    Best for: navbar, login page header, invoices
    Usage: <img src="afghan-pos-logo-horizontal.svg" alt="Afghan POS">
  -->

                    <!-- Hexagon -->
                    <polygon points="36,4 63,19.5 63,50.5 36,66 9,50.5 9,19.5" fill="#0f1c3f" stroke="#1d6bf3"
                        stroke-width="1.2" />
                    <!-- Inner ring -->
                    <polygon points="36,11 57,23.5 57,48.5 36,61 15,48.5 15,23.5" fill="none" stroke="#c8a84b"
                        stroke-width="0.7" opacity="0.5" />

                    <!-- A left leg -->
                    <line x1="25" y1="54" x2="36" y2="20" stroke="#ffffff"
                        stroke-width="3.5" stroke-linecap="round" />
                    <!-- A right leg -->
                    <line x1="47" y1="54" x2="36" y2="20" stroke="#ffffff"
                        stroke-width="3.5" stroke-linecap="round" />
                    <!-- Crossbar -->
                    <line x1="29" y1="42" x2="43" y2="42" stroke="#c8a84b"
                        stroke-width="2.5" stroke-linecap="round" />

                    <!-- Apex dot -->
                    <circle cx="36" cy="19" r="3.5" fill="#1d6bf3" />
                    <!-- Base dots -->
                    <circle cx="25" cy="54" r="2.5" fill="#c8a84b" />
                    <circle cx="47" cy="54" r="2.5" fill="#c8a84b" />

                    <!-- Divider line -->
                    <line x1="78" y1="14" x2="78" y2="58" stroke="#e0e2ea"
                        stroke-width="1" />

                    <!-- "Afghan" wordmark -->
                    <text x="92" y="38" font-family="Georgia,'Times New Roman',serif" font-size="22" font-weight="700"
                        fill="#0f1c3f" letter-spacing="-0.5">Afghan</text>

                    <!-- "POS" -->
                    <text x="93" y="56" font-family="system-ui,sans-serif" font-size="11" font-weight="700"
                        fill="#1d6bf3" letter-spacing="5">POS</text>
                </svg>

                {{-- Tagline --}}
                <div class="left-tagline">
                    <h2>Retail made<br><em>simple & smart.</em></h2>
                    <p>Your all-in-one point of sale system — fast checkouts, real-time inventory, and clear daily
                        reports.</p>
                </div>

                {{-- Stats --}}
                <div class="left-stats">
                    <div class="stat">
                        <div class="stat-num">99.9%</div>
                        <div class="stat-label">Uptime</div>
                    </div>
                    <div class="stat">
                        <div class="stat-num">3 sec</div>
                        <div class="stat-label">Avg checkout</div>
                    </div>
                    <div class="stat">
                        <div class="stat-num">∞</div>
                        <div class="stat-label">Transactions</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════
         RIGHT — login form
    ═══════════════════════ --}}
        <div class="right-panel">

            <div class="form-heading">
                <div class="welcome">Welcome back</div>
                <h1>Sign in to<br>your account</h1>
                <p>Choose how you'd like to log in below.</p>
            </div>

            {{-- Tabs --}}
            <div class="tabs">
                <button class="tab-btn active" onclick="switchTab('password', this)" type="button">🔐
                    &nbsp;Password</button>
                <button class="tab-btn" onclick="switchTab('pin', this)" type="button">🔢 &nbsp;PIN Code</button>
            </div>

            {{-- Error --}}
            @if (session('error'))
                <div class="alert">
                    <span>⚠️</span>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            {{-- PASSWORD PANEL --}}
            <div id="panel-password" class="panel active">
                <form action="{{ route('login') }}" method="POST">
                    @csrf
                    <div class="field">
                        <label>Username</label>
                        <div class="input-wrap">
                            <span class="input-icon">👤</span>
                            <input type="text" name="username" required autofocus
                                placeholder="Enter your username" value="{{ old('username') }}">
                        </div>
                    </div>
                    <div class="field">
                        <label>Password</label>
                        <div class="input-wrap">
                            <span class="input-icon">🔒</span>
                            <input type="password" name="password" id="pwField" required
                                placeholder="Enter your password">
                            <button type="button" class="eye-btn" id="eyeBtn" onclick="togglePw()"
                                aria-label="Toggle password">👁</button>
                        </div>
                    </div>
                    <div class="forgot-row">
                        <a href="#" class="forgot-link">Forgot password?</a>
                    </div>
                    <button type="submit" class="btn btn-blue">Sign In &nbsp;→</button>
                </form>
            </div>

            {{-- PIN PANEL --}}
            <div id="panel-pin" class="panel">
                <form action="{{ route('login.pin') }}" method="POST" onsubmit="combinePin()">
                    @csrf
                    <input type="hidden" name="pin_code" id="fullPin">
                    <div class="pin-label">Enter your 4-digit cashier PIN</div>
                    <div class="pin-boxes">
                        <input class="pin-box" type="password" name="pin1" id="pin1" maxlength="1"
                            inputmode="numeric" autocomplete="off" onkeyup="pinNext(this,'pin2')"
                            oninput="markFilled(this)">
                        <input class="pin-box" type="password" name="pin2" id="pin2" maxlength="1"
                            inputmode="numeric" autocomplete="off" onkeyup="pinNext(this,'pin3')"
                            onkeydown="pinBack(event,this,'pin1')" oninput="markFilled(this)">
                        <input class="pin-box" type="password" name="pin3" id="pin3" maxlength="1"
                            inputmode="numeric" autocomplete="off" onkeyup="pinNext(this,'pin4')"
                            onkeydown="pinBack(event,this,'pin2')" oninput="markFilled(this)">
                        <input class="pin-box" type="password" name="pin4" id="pin4" maxlength="1"
                            inputmode="numeric" autocomplete="off" onkeydown="pinBack(event,this,'pin3')"
                            oninput="markFilled(this)">
                    </div>
                    <button type="submit" class="btn btn-purple">Login with PIN &nbsp;→</button>
                </form>
            </div>

            {{-- Language --}}
            <div class="lang-row">
                <select class="lang-select" onchange="changeLang(this.value)">
                    <option value="en">🇬🇧 English</option>
                    <option value="ps">🇦🇫 پښتو</option>
                    <option value="dr">🇦🇫 دری</option>
                </select>
            </div>

            <div class="right-footer">Afghan POS &copy; {{ date('Y') }} — All rights reserved</div>

        </div>{{-- /right-panel --}}

    </div>{{-- /split --}}

    <script>
        function switchTab(name, el) {
            document.querySelectorAll('.panel').forEach(p => p.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.getElementById('panel-' + name).classList.add('active');
            el.classList.add('active');
        }

        function togglePw() {
            const f = document.getElementById('pwField');
            const b = document.getElementById('eyeBtn');
            f.type = f.type === 'password' ? 'text' : 'password';
            b.textContent = f.type === 'password' ? '👁' : '🙈';
        }

        function pinNext(el, nextId) {
            if (el.value.length === 1 && nextId) document.getElementById(nextId)?.focus();
        }

        function pinBack(e, el, prevId) {
            if (e.key === 'Backspace' && el.value === '') {
                e.preventDefault();
                const prev = document.getElementById(prevId);
                if (prev) {
                    prev.value = '';
                    prev.classList.remove('filled');
                    prev.focus();
                }
            }
        }

        function markFilled(el) {
            el.classList.toggle('filled', el.value.length > 0);
        }

        function combinePin() {
            document.getElementById('fullPin').value = ['pin1', 'pin2', 'pin3', 'pin4'].map(id => document.getElementById(
                id).value).join('');
        }

        function changeLang(lang) {
            console.log('Language changed:', lang);
            // Hook into your i18n logic here
        }
    </script>

</body>

</html>
