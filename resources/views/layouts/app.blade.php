<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" x-data="appShell()" :class="darkMode ? 'dark' : ''" x-init="init()">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Afghan POS — Retail Management</title>

    {{-- Tailwind --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Alpine --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>

    {{-- Icons --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&family=Playfair+Display:ital,wght@0,700;1,400&display=swap"
        rel="stylesheet">

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        mono: ['DM Mono', 'monospace'],
                        display: ['Playfair Display', 'serif']
                    },
                    colors: {
                        primary: '#3b5bdb',
                        'primary-dark': '#2f4ac7',
                        'primary-light': '#748ffc',
                        success: '#2f9e44',
                        danger: '#e03131',
                        warning: '#f08c00',
                        'card-border': '#e2e8f0',
                    }
                }
            }
        }
    </script>
    @vite(['resources/css/app.css'])

    @stack('styles')
</head>

<body>
    <div class="shell" id="appShell" x-bind:class="{ 'collapsed': sidebarCollapsed, 'mobile-open': mobileOpen }">

        {{-- ════════ MOBILE OVERLAY ════════ --}}
        <div class="sidebar-overlay" @click="mobileOpen = false"></div>


        {{-- ════════ SIDEBAR ════════ --}}
        @include('layouts.sidebar')

        @include('layouts.header')
        {{-- ════════ MAIN ════════ --}}
        <main id="app-main">
            @yield('content')
        </main>
        @include('layouts.footer')

    </div>{{-- /shell --}}

    {{-- Global modals --}}
    <x-modals.register-customer />

    {{-- Global scripts --}}
    <script>
        /* ── Alpine global store ── */
        document.addEventListener('alpine:init', () => {
            Alpine.store('customerModal', {
                show: false,
                onSuccess: null,
                open(cb = null) {
                    this.onSuccess = cb;
                    this.show = true;
                },
                close() {
                    this.show = false;
                    this.onSuccess = null;
                },
                registered(c) {
                    if (typeof this.onSuccess === 'function') this.onSuccess(c);
                    this.close();
                }
            });
        });

        /* ── App Shell Alpine component ── */
        function appShell() {
            return {
                sidebarCollapsed: false,
                mobileOpen: false,
                darkMode: false,
                lang: 'en',

                init() {
                    // Restore preferences
                    this.sidebarCollapsed = localStorage.getItem('sb-collapsed') === '1';
                    this.darkMode = localStorage.getItem('dark-mode') === '1';
                    this.lang = localStorage.getItem('app-lang') || 'en';
                    this.applyDark();
                    this.applyLang();
                    this.startClock();
                },

                toggleSidebar() {
                    this.sidebarCollapsed = !this.sidebarCollapsed;
                    localStorage.setItem('sb-collapsed', this.sidebarCollapsed ? '1' : '0');
                },

                toggleTheme() {
                    this.darkMode = !this.darkMode;
                    localStorage.setItem('dark-mode', this.darkMode ? '1' : '0');
                },

                init() {
                    this.sidebarCollapsed = localStorage.getItem('sb-collapsed') === '1';
                    this.darkMode = localStorage.getItem('dark-mode') === '1';
                    this.lang = localStorage.getItem('app-lang') || 'en';
                    this.applyLang();
                    this.startClock();
                },


                applyDark() {
                    if (this.darkMode) {
                        document.documentElement.classList.add('dark');
                        document.body.classList.add('dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                        document.body.classList.remove('dark');
                    }
                },

                setLang(l) {
                    this.lang = l;
                    localStorage.setItem('app-lang', l);
                    this.applyLang();
                },

                applyLang() {
                    const dir = (this.lang === 'ps' || this.lang === 'dr') ? 'rtl' : 'ltr';
                    document.documentElement.setAttribute('dir', dir);
                    document.documentElement.setAttribute('lang', this.lang);
                },

                currentLangLabel() {
                    return {
                        en: 'English',
                        ps: 'پښتو',
                        dr: 'دری'
                    } [this.lang] || 'EN';
                },

                startClock() {
                    const tick = () => {
                        const t = new Date().toLocaleTimeString('en-US');
                        const el = document.getElementById('footerClock');
                        if (el) el.textContent = t;
                    };
                    tick();
                    setInterval(tick, 1000);
                },
            };
        }
    </script>

    @stack('scripts')

</body>

</html>
