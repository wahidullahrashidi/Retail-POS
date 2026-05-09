@php
    $currentLocale = app()->getLocale();
    $isRtl = in_array($currentLocale, ['ps', 'dr'], true);
@endphp
<!DOCTYPE html>
<html lang="{{ $currentLocale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}" x-data="appShell()" :class="darkMode ? 'dark' : ''" x-init="init()">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('messages.afghan_pos_retail_management') }}</title>

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
    @vite(['resources/css/layout/app-shell.css'])
    @vite(['resources/css/layout/theme.css'])

    @stack('styles')
    <style>
        /* ════════════════════════════════════════
           SHELL VARIABLES
        ════════════════════════════════════════ */
        :root {
            --sidebar-w: 240px;
            --sidebar-w-sm: 68px;
            --header-h: 52px;
            --footer-h: 40px;
            --trans: .22s cubic-bezier(.4, 0, .2, 1);
        }

        /* ════════════════════════════════════════
           BASE
        ════════════════════════════════════════ */
        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        html {
            height: 100%;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            height: 100%;
            margin: 0;
            background: #f1f3f8;
            color: #1a1d2e;
            overflow: hidden;
        }

        .dark body,
        body.dark {
            background: #0f1117;
            color: #e8eaf0;
        }

        /* ════════════════════════════════════════
           DARK MODE TOKENS
        ════════════════════════════════════════ */
        .dark .dk-surface {
            background: #161b22 !important;
        }

        .dark .dk-border {
            border-color: rgba(255, 255, 255, .07) !important;
        }

        .dark .dk-ink {
            color: #e8eaf0 !important;
        }

        .dark .dk-ink2 {
            color: #9099b0 !important;
        }

        .dark .dk-s2 {
            background: #1c2430 !important;
        }

        /* ════════════════════════════════════════
           LAYOUT SHELL
        ════════════════════════════════════════ */
        .shell {
            display: grid;
            grid-template-rows: var(--header-h) 1fr var(--footer-h);
            grid-template-columns: var(--sidebar-w) 1fr;
            grid-template-areas:
                "sidebar header"
                "sidebar main"
                "sidebar footer";
            height: 100vh;
            transition: grid-template-columns var(--trans);
        }

        .shell.collapsed {
            grid-template-columns: var(--sidebar-w-sm) 1fr;
        }

        /* mobile: sidebar off-canvas */
        /* @media (max-width: 768px) {
            .shell {
                grid-template-columns: 0 1fr;
                grid-template-areas:
                    "header header"
                    "main main"
                    "footer footer";
            }

            .shell.mobile-open {
                grid-template-columns: var(--sidebar-w) 1fr;
                grid-template-areas:
                    "sidebar header"
                    "sidebar main"
                    "sidebar footer";
            }
        } */

        /* ════════════════════════════════════════
           SIDEBAR
        ════════════════════════════════════════ */
        #sidebar {
            grid-area: sidebar;
            background: #ffffff;
            border-right: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            transition: width var(--trans);
            width: var(--sidebar-w);
            position: relative;
            z-index: 60;
        }

        .dark #sidebar {
            background: #161b22;
            border-right-color: rgba(255, 255, 255, .07);
        }

        .shell.collapsed #sidebar {
            width: var(--sidebar-w-sm);
        }

        /* @media (max-width: 768px) {
            #sidebar {
                position: fixed;
                left: -var(--sidebar-w);
                top: 0;
                bottom: 0;
                transform: translateX(-100%);
                transition: transform var(--trans);
                z-index: 100;
            }

            .shell.mobile-open #sidebar {
                transform: translateX(0);
            }
        } */

        /* Logo area */
        .sb-logo {
            height: var(--header-h);
            display: flex;
            align-items: center;
            padding: 0 1rem;
            gap: 10px;
            border-bottom: 1px solid #e2e8f0;
            flex-shrink: 0;
            overflow: hidden;
        }

        .dark .sb-logo {
            border-bottom-color: rgba(255, 255, 255, .07);
        }

        /* Logo mark — Afghan-inspired geometric shape */
        .logo-mark {
            width: 34px;
            height: 34px;
            flex-shrink: 0;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo-mark svg {
            width: 34px;
            height: 34px;
        }

        .logo-text {
            font-family: 'Playfair Display', serif;
            font-size: 16px;
            font-weight: 700;
            color: #1a1d2e;
            white-space: nowrap;
            overflow: hidden;
            transition: opacity var(--trans), max-width var(--trans);
            max-width: 160px;
        }

        .dark .logo-text {
            color: #e8eaf0;
        }

        .shell.collapsed .logo-text {
            max-width: 0;
            opacity: 0;
        }

        .sb-toggle {
            margin-left: auto;
            flex-shrink: 0;
            width: 26px;
            height: 26px;
            border: none;
            background: none;
            cursor: pointer;
            color: #9099b0;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background .15s, color .15s;
        }

        .sb-toggle:hover {
            background: #f1f3f8;
            color: #1a1d2e;
        }

        .dark .sb-toggle:hover {
            background: rgba(255, 255, 255, .08);
            color: #e8eaf0;
        }

        .sb-toggle i {
            font-size: 11px;
            transition: transform var(--trans);
        }

        .shell.collapsed .sb-toggle i {
            transform: rotate(180deg);
        }

        /* Nav */
        .sb-nav {
            flex: 1;
            overflow-y: auto;
            padding: .75rem 0;
        }

        .sb-nav::-webkit-scrollbar {
            width: 3px;
        }

        .sb-nav::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 2px;
        }

        .dark .sb-nav::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, .07);
        }

        .sb-section-label {
            font-size: 9px;
            font-weight: 700;
            color: #9099b0;
            text-transform: uppercase;
            letter-spacing: .1em;
            padding: .6rem 1rem .3rem;
            white-space: nowrap;
            overflow: hidden;
            transition: opacity var(--trans);
        }

        .shell.collapsed .sb-section-label {
            opacity: 0;
        }

        .sb-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 12px;
            margin: 1px 8px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            color: #4a5270;
            text-decoration: none;
            transition: background .15s, color .15s;
            white-space: nowrap;
            position: relative;
            overflow: hidden;
        }

        .dark .sb-item {
            color: #8b95b0;
        }

        .sb-item:hover {
            background: #f1f3f8;
            color: #1a1d2e;
        }

        .dark .sb-item:hover {
            background: rgba(255, 255, 255, .06);
            color: #e8eaf0;
        }

        .sb-item.active {
            background: rgba(59, 91, 219, .09);
            color: #3b5bdb;
            font-weight: 600;
        }

        .dark .sb-item.active {
            background: rgba(59, 91, 219, .18);
            color: #748ffc;
        }

        .sb-item.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 4px;
            bottom: 4px;
            width: 3px;
            border-radius: 0 2px 2px 0;
            background: #3b5bdb;
        }

        .dark .sb-item.active::before {
            background: #748ffc;
        }

        .sb-item i {
            width: 18px;
            text-align: center;
            font-size: 13px;
            flex-shrink: 0;
        }

        .sb-item-text {
            transition: opacity var(--trans), max-width var(--trans);
            max-width: 160px;
            overflow: hidden;
        }

        .shell.collapsed .sb-item-text {
            max-width: 0;
            opacity: 0;
        }

        /* Tooltip for collapsed state */
        .sb-item[data-tip]:hover::after {
            content: attr(data-tip);
            position: absolute;
            left: 110%;
            top: 50%;
            transform: translateY(-50%);
            background: #1a1d2e;
            color: #fff;
            font-size: 11px;
            font-weight: 500;
            padding: 4px 10px;
            border-radius: 6px;
            white-space: nowrap;
            z-index: 200;
            opacity: 0;
            pointer-events: none;
            transition: opacity .15s .1s;
        }

        .shell.collapsed .sb-item[data-tip]:hover::after {
            opacity: 1;
        }

        /* Sidebar footer */
        .sb-foot {
            padding: .75rem;
            border-top: 1px solid #e2e8f0;
            flex-shrink: 0;
        }

        .dark .sb-foot {
            border-top-color: rgba(255, 255, 255, .07);
        }

        .sb-foot a {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 8px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            font-size: 12px;
            font-weight: 500;
            color: #4a5270;
            text-decoration: none;
            transition: all .15s;
            overflow: hidden;
        }

        .dark .sb-foot a {
            border-color: rgba(255, 255, 255, .1);
            color: #8b95b0;
        }

        .sb-foot a:hover {
            background: #fef2f2;
            border-color: #fca5a5;
            color: #dc2626;
        }

        .sb-foot-text {
            transition: opacity var(--trans), max-width var(--trans);
            max-width: 160px;
            white-space: nowrap;
            overflow: hidden;
        }

        .shell.collapsed .sb-foot-text {
            max-width: 0;
            opacity: 0;
        }

        /* ════════════════════════════════════════
           HEADER
        ════════════════════════════════════════ */
        #app-header {
            grid-area: header;
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.25rem;
            z-index: 200;
            flex-shrink: 0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .04);
        }

        .dark #app-header {
            background: #161b22;
            border-bottom-color: rgba(255, 255, 255, .07);
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* Date display */
        .header-date .gregorian {
            font-size: 12px;
            font-weight: 500;
            color: #1a1d2e;
        }

        .dark .header-date .gregorian {
            color: #e8eaf0;
        }

        .header-date .hijri {
            font-size: 10px;
            color: #9099b0;
            margin-top: 1px;
        }

        /* Header icon buttons */
        .hdr-btn {
            width: 34px;
            height: 34px;
            border: none;
            background: none;
            cursor: pointer;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6b7280;
            font-size: 14px;
            transition: background .15s, color .15s;
            position: relative;
        }

        .hdr-btn:hover {
            background: #f1f3f8;
            color: #1a1d2e;
        }

        .dark .hdr-btn:hover {
            background: rgba(255, 255, 255, .08);
            color: #e8eaf0;
        }

        .hdr-btn .badge {
            position: absolute;
            top: 5px;
            right: 5px;
            width: 7px;
            height: 7px;
            background: #e03131;
            border-radius: 50%;
            border: 1px solid #fff;
        }

        .dark .hdr-btn .badge {
            border-color: #161b22;
        }

        /* Divider */
        .hdr-divider {
            width: 1px;
            height: 22px;
            background: #e2e8f0;
            margin: 0 2px;
        }

        .dark .hdr-divider {
            background: rgba(255, 255, 255, .1);
        }

        /* Theme toggle */
        .theme-toggle {
            width: 34px;
            height: 34px;
            border: none;
            cursor: pointer;
            border-radius: 8px;
            background: none;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            color: #6b7280;
            transition: background .15s, color .15s;
        }

        .theme-toggle:hover {
            background: #f1f3f8;
            color: #f59e0b;
        }

        .dark .theme-toggle:hover {
            background: rgba(255, 255, 255, .08);
            color: #fbbf24;
        }

        /* Language dropdown */
        .lang-dropdown {
            position: relative;
            z-index: 9999;
        }

        .lang-btn {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 5px 10px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background: none;
            cursor: pointer;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 12px;
            font-weight: 600;
            color: #4a5270;
            transition: all .15s;
        }

        .dark .lang-btn {
            border-color: rgba(255, 255, 255, .12);
            color: #9099b0;
        }

        .lang-btn:hover {
            background: #f1f3f8;
            border-color: #c5cade;
        }

        .dark .lang-btn:hover {
            background: rgba(255, 255, 255, .07);
        }

        .lang-menu {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, .1);
            min-width: 140px;
            overflow: hidden;
            z-index: 9999;
            position: absolute;
            right: 0;
            top: calc(100% + 6px);
        }

        .dark .lang-menu {
            background: #1c2430;
            border-color: rgba(255, 255, 255, .08);
        }

        .lang-option {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 9px 14px;
            font-size: 13px;
            font-weight: 500;
            color: #4a5270;
            cursor: pointer;
            transition: background .12s;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
        }

        .dark .lang-option {
            color: #8b95b0;
        }

        .lang-option:hover {
            background: #f1f3f8;
        }

        .dark .lang-option:hover {
            background: rgba(255, 255, 255, .06);
        }

        .lang-option.active {
            color: #3b5bdb;
            background: rgba(59, 91, 219, .07);
            font-weight: 700;
        }

        .dark .lang-option.active {
            color: #748ffc;
        }

        /* User avatar */
        .user-chip {
            display: flex;
            align-items: center;
            gap: 7px;
            padding: 3px 10px 3px 3px;
            border: 1px solid #e2e8f0;
            border-radius: 99px;
            cursor: pointer;
            transition: background .15s;
            background: none;
        }

        .dark .user-chip {
            border-color: rgba(255, 255, 255, .1);
        }

        .user-chip:hover {
            background: #f1f3f8;
        }

        .dark .user-chip:hover {
            background: rgba(255, 255, 255, .06);
        }

        .user-av {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            object-fit: cover;
            display: block;
            background: #3b5bdb;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
            flex-shrink: 0;
        }

        .user-chip-name {
            font-size: 12px;
            font-weight: 600;
            color: #1a1d2e;
        }

        .dark .user-chip-name {
            color: #e8eaf0;
        }

        /* Hamburger for mobile */
        .hdr-hamburger {
            display: none;
            width: 34px;
            height: 34px;
            border: none;
            background: none;
            cursor: pointer;
            border-radius: 8px;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            color: #6b7280;
            transition: background .15s;
        }

        /* @media (max-width: 768px) {
            .hdr-hamburger {
                display: flex;
            }
        } */

        .hdr-hamburger:hover {
            background: #f1f3f8;
            color: #1a1d2e;
        }

        /* Mobile overlay */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .4);
            z-index: 90;
        }

        .shell.mobile-open .sidebar-overlay {
            display: block;
        }

        /* ════════════════════════════════════════
           MAIN
        ════════════════════════════════════════ */
        #app-main {
            grid-area: main;
            overflow-y: auto;
            min-height: 0;
        }

        #app-main::-webkit-scrollbar {
            width: 5px;
        }

        #app-main::-webkit-scrollbar-thumb {
            background: #dde1ee;
            border-radius: 3px;
        }

        .dark #app-main::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, .08);
        }

        /* ════════════════════════════════════════
           FOOTER
        ════════════════════════════════════════ */
        #app-footer {
            grid-area: footer;
            background: #ffffff;
            border-top: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.25rem;
            font-size: 11px;
            color: #9099b0;
            flex-shrink: 0;
        }

        .dark #app-footer {
            background: #161b22;
            border-top-color: rgba(255, 255, 255, .07);
        }

        .footer-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .footer-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .footer-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .status-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #2f9e44;
            animation: fp 2s infinite;
        }

        @keyframes fp {

            0%,
            100% {
                opacity: 1
            }

            50% {
                opacity: .4
            }
        }

        /* [x-cloak] */
        [x-cloak] {
            display: none !important;
        }

        /* RTL support for Pashto/Dari */
        [dir="rtl"] .sb-item {
            flex-direction: row-reverse;
        }

        [dir="rtl"] .sb-item.active::before {
            left: auto;
            right: 0;
            border-radius: 2px 0 0 2px;
        }

        [dir="rtl"] .sb-item-text {
            text-align: right;
        }
        /* ════════════════════════
   RESPONSIVE FIXES
════════════════════════ */

/* Tablet (max 1024px) */
@media (max-width: 1024px) {
    .stat-strip        { grid-template-columns: repeat(2, 1fr) !important; }
    .kpi-4             { grid-template-columns: repeat(2, 1fr) !important; }
    .kpi-3             { grid-template-columns: repeat(2, 1fr) !important; }
    .grid-2            { grid-template-columns: 1fr !important; }
    .grid-3            { grid-template-columns: 1fr 1fr !important; }
    .grid-7-5          { grid-template-columns: 1fr !important; }
    .grid-3-2          { grid-template-columns: 1fr !important; }
    .cashier-grid      { grid-template-columns: repeat(2, 1fr) !important; }
    .users-grid        { grid-template-columns: repeat(2, 1fr) !important; }
    .cloud-providers   { grid-template-columns: repeat(2, 1fr) !important; }
    .sh-main.panel-open,
    .sl-main.panel-open,
    .sp-main.panel-open,
    .cu-main.panel-open,
    .um-main.panel-open { grid-template-columns: 1fr !important; }
    .detail-panel      { position: fixed !important; right: 0; top: 0; bottom: 0;
                         width: 380px; max-height: 100vh !important;
                         z-index: 150; box-shadow: var(--shlg); }
    .co-body           { grid-template-columns: 1fr !important; height: auto !important; }
    .pay-panel         { max-height: none !important; }
}

/* Mobile (max 768px) */
@media (max-width: 768px) {
    /* Page body padding */
    .rp-body, .bk-body { padding: 1rem !important; }
    .stat-strip        { padding: 1rem !important; grid-template-columns: repeat(2, 1fr) !important; gap: .75rem !important; }
    .kpi-4, .kpi-3     { grid-template-columns: repeat(2, 1fr) !important; }
    .rp-panel, .sp-panel, .sh-panel { padding: 1rem !important; }

    /* Toolbars */
    .um-toolbar, .sh-toolbar, .sl-toolbar,
    .sp-toolbar, .cu-toolbar, .inv-toolbar { padding: .75rem 1rem !important; gap: 6px !important; }
    .tab-strip         { margin-left: 0 !important; flex-wrap: wrap; }
    .date-range        { flex-wrap: wrap; }

    /* Tables become scrollable */
    .table-card        { overflow-x: auto !important; }
    .sh-table, .sl-table, .sp-table,
    .cu-table, .inv-table, .bk-table { min-width: 600px; }

    /* Hide less important columns on mobile */
    .hide-mobile       { display: none !important; }

    /* Panels full width on mobile */
    .detail-panel      { position: fixed !important; inset: 0 !important;
                         width: 100% !important; max-height: 100vh !important;
                         z-index: 150; border-radius: 0 !important; }

    /* Checkout split → stacked */
    .co-body           { grid-template-columns: 1fr !important; height: auto !important; overflow: visible !important; }
    #app-main          { overflow-y: auto !important; }

    /* Users grid */
    .users-grid        { grid-template-columns: 1fr !important; }
    .cashier-grid      { grid-template-columns: 1fr !important; }

    /* Settings rail → hidden, use tab strip */
    .st-rail           { display: none !important; }
    .st-body           { flex-direction: column !important; }
    .st-content        { padding: 1rem !important; }

    /* Reports tabs scroll */
    .rp-tabs           { overflow-x: auto; white-space: nowrap; gap: 0; }
    .rp-tab            { flex-shrink: 0; }

    /* Z-report grid */
    .zr-kpi            { grid-template-columns: repeat(2, 1fr) !important; }
    .zr-grid           { grid-template-columns: repeat(2, 1fr) !important; }

    /* Stat cards font size */
    .st-val, .sc-val, .kpi-val { font-size: 18px !important; }

    /* Topbar responsive */
    .st-title, .rp-title, .bk-title,
    .sp-title, .sl-title, .um-title,
    .sh-title, .cu-title { font-size: 16px !important; }

    /* Modal full screen on mobile */
    .modal-overlay     { padding: 0 !important; align-items: flex-end !important; }
    .modal-card        { border-radius: var(--rlg) var(--rlg) 0 0 !important; max-height: 90vh !important; }
    .modal-sm, .modal-md, .modal-lg { max-width: 100% !important; width: 100% !important; }

    /* Drawer full width */
    .drawer            { width: 100% !important; }

    /* Cloud providers */
    .cloud-providers   { grid-template-columns: 1fr !important; }

    /* Form grids */
    .form-2, .form-3, .form-grid-2, .form-grid-3 { grid-template-columns: 1fr !important; }

    /* Trending grid */
    .trending-grid     { grid-template-columns: repeat(2, 1fr) !important; }

    /* Cart panel */
    .cart-panel        { min-height: 50vh !important; }
}

/* Small mobile (max 480px) */
@media (max-width: 480px) {
    .stat-strip        { grid-template-columns: 1fr !important; }
    .kpi-4, .kpi-3     { grid-template-columns: 1fr !important; }
    .cc-summary        { grid-template-columns: 1fr 1fr !important; }
    .zr-kpi            { grid-template-columns: 1fr 1fr !important; }
    .perm-grid         { grid-template-columns: 1fr !important; }
    .pin-input-row     { gap: 6px !important; }
    .pin-box           { width: 48px !important; height: 48px !important; font-size: 18px !important; }

    /* Header user chip — hide name on tiny screens */
    .user-chip-name    { display: none !important; }

    /* Report heatmap */
    .heatmap-grid      { grid-template-columns: repeat(12, 1fr) !important; }
    .heatmap-labels    { grid-template-columns: repeat(12, 1fr) !important; }
}

    </style>
    
    <style>
        /* Dark mode CSS variable overrides */
        .dark {
            --bg: #0f1117 !important;
            --surface: #161b22 !important;
            --s2: #1c2430 !important;
            --s3: #242c3a !important;
            --border: rgba(255, 255, 255, .07) !important;
            --border2: rgba(255, 255, 255, .12) !important;
            --ink: #e8eaf0 !important;
            --ink2: #9099b0 !important;
            --ink3: #5c6278 !important;
            --ink4: #3a3f52 !important;
        }
    </style>
    <style>
        @media (max-width: 768px) {
            .shell {
                grid-template-columns: 0 1fr !important;
                grid-template-areas:
                    "header header"
                    "main main"
                    "footer footer" !important;
            }

            .shell.mobile-open {
                grid-template-columns: var(--sidebar-w) 1fr !important;
            }

            #sidebar {
                position: fixed !important;
                inset-block: 0 !important;
                left: calc(-1 * var(--sidebar-w)) !important;
                width: var(--sidebar-w) !important;
                transform: translateX(0) !important;
                z-index: 300 !important;
            }

            .shell.mobile-open #sidebar {
                left: 0 !important;
            }

            .hdr-hamburger {
                display: flex !important;
            }

            #app-header {
                padding: 0 .75rem !important;
            }

            .header-right {
                min-width: 0;
            }

            #app-footer {
                display: none !important;
            }
        }

        [dir="rtl"] #sidebar {
            border-right: 0;
            border-left: 1px solid #e2e8f0;
        }

        [dir="rtl"] .lang-menu {
            right: auto;
            left: 0;
        }

        [dir="rtl"] .lang-option {
            text-align: right;
        }

        @media print {
            #sidebar,
            #app-header,
            #app-footer,
            .sidebar-overlay,
            .no-print {
                display: none !important;
            }

            body,
            .shell,
            #app-main {
                display: block !important;
                height: auto !important;
                overflow: visible !important;
                background: #fff !important;
            }

            body.print-section-active * {
                visibility: hidden !important;
            }

            body.print-section-active [data-print-root],
            body.print-section-active [data-print-root] * {
                visibility: visible !important;
            }

            body.print-section-active [data-print-root] {
                display: block !important;
                position: absolute !important;
                inset: 0 auto auto 0 !important;
                width: 100% !important;
                max-width: none !important;
                margin: 0 !important;
                box-shadow: none !important;
            }
        }
    </style>
</head>

<body>
    <div class="shell" id="appShell" x-bind:class="{ 'collapsed': sidebarCollapsed, 'mobile-open': mobileOpen }">

        {{-- ════════ MOBILE OVERLAY ════════ --}}
        <div class="sidebar-overlay" @click="mobileOpen = false"></div>

        {{-- ════════ SIDEBAR ════════ --}}
        @include('layouts.sidebar')

        {{-- ════════ HEADER ════════ --}}
        @include('layouts.header')

        {{-- ════════ MAIN ════════ --}}
        <main id="app-main">
            @yield('content')
        </main>

        {{-- ════════ FOOTER ════════ --}}
        @include('layouts.footer')
    </div>{{-- /shell --}}

    {{-- Global modals --}}
    <x-modals.register-customer />

    {{-- Global scripts --}}
    <script>
        window.printSection = function(selector) {
            const target = document.querySelector(selector);
            if (!target) {
                window.print();
                return;
            }

            document.querySelectorAll('[data-print-root]').forEach(el => el.removeAttribute('data-print-root'));
            target.setAttribute('data-print-root', 'true');
            document.body.classList.add('print-section-active');

            const cleanup = () => {
                document.body.classList.remove('print-section-active');
                target.removeAttribute('data-print-root');
                window.removeEventListener('afterprint', cleanup);
            };

            window.addEventListener('afterprint', cleanup, { once: true });
            window.print();
            setTimeout(cleanup, 1200);
        };

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
                lang: '{{ $currentLocale }}',

                init() {
                // Read current locale SET BY LARAVEL (server-side)
                this.lang = '{{ $currentLocale }}';
                localStorage.setItem('app-lang', this.lang);

                this.sidebarCollapsed = localStorage.getItem('sb-collapsed') === '1';
                this.darkMode         = localStorage.getItem('dark-mode')    === '1';

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
                    this.applyDark();
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
                // Redirect to a route that sets the Laravel locale server-side
                window.location.href = '{{ url('/language') }}/' + l;
                },

                applyLang() {
                    const dir = (this.lang === 'ps' || this.lang === 'dr') ? 'rtl' : 'ltr';
                    document.documentElement.setAttribute('dir', dir);
                    document.documentElement.setAttribute('lang', this.lang);
                },

                currentLangLabel() {
                    return {
                        en: @json(__('messages.english')),
                        ps: @json(__('messages.pashto')),
                        dr: @json(__('messages.dari'))
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

