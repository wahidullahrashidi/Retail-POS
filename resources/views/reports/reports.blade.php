@extends('layouts.app')

@push('styles')
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;1,400&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap"
        rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.46.0/dist/apexcharts.min.js"></script>
    <style>
        /* ══════════════════════════════════════
       TOKENS
    ══════════════════════════════════════ */
        :root {
            --bg: #f0f2f7;
            --surface: #ffffff;
            --s2: #f6f7fb;
            --s3: #eceef5;
            --border: #dde1ee;
            --border2: #c8cede;
            --ink: #181b2a;
            --ink2: #424668;
            --ink3: #848baa;
            --ink4: #bfc5da;
            --blue: #2f6fe8;
            --blue2: #1f5bcc;
            --bdim: rgba(47, 111, 232, .09);
            --bmid: rgba(47, 111, 232, .18);
            --green: #15803d;
            --gdim: rgba(21, 128, 61, .1);
            --red: #dc2626;
            --rdim: rgba(220, 38, 38, .09);
            --amber: #d97706;
            --adim: rgba(217, 119, 6, .1);
            --violet: #7c3aed;
            --vdim: rgba(124, 58, 237, .1);
            --teal: #0891b2;
            --tdim: rgba(8, 145, 178, .1);
            --mono: 'DM Mono', monospace;
            --body: 'Plus Jakarta Sans', sans-serif;
            --display: 'Playfair Display', serif;
            --r: 12px;
            --rsm: 7px;
            --rlg: 18px;
            --sh: 0 1px 4px rgba(0, 0, 0, .05), 0 1px 2px rgba(0, 0, 0, .03);
            --shmd: 0 4px 20px rgba(0, 0, 0, .08), 0 2px 6px rgba(0, 0, 0, .04);
            --shlg: 0 16px 48px rgba(0, 0, 0, .12), 0 4px 14px rgba(0, 0, 0, .06);
        }

        /* ══════════════════════════════════════
       BASE
    ══════════════════════════════════════ */
        .rp* {
            box-sizing: border-box
        }

        .rp {
            font-family: var(--body);
            background: var(--bg);
            min-height: 100vh;
            color: var(--ink)
        }

        [x-cloak] {
            display: none !important
        }

        /* ══════════════════════════════════════
       TOPBAR
    ══════════════════════════════════════ */
        .rp-top {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.75rem;
            position: sticky;
            top: 0;
            z-index: 80;
            box-shadow: var(--sh);
        }

        .rp-title {
            font-family: var(--display);
            font-size: 21px;
            color: var(--ink);
            letter-spacing: -.3px
        }

        .rp-title em {
            color: var(--blue);
            font-style: italic
        }

        .top-right {
            display: flex;
            align-items: center;
            gap: 8px
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            border-radius: var(--rsm);
            font-family: var(--body);
            font-size: 12px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all .17s;
            white-space: nowrap
        }

        .btn-ghost {
            background: var(--s2);
            border: 1px solid var(--border);
            color: var(--ink2)
        }

        .btn-ghost:hover {
            background: var(--s3);
            color: var(--ink)
        }

        .btn-primary {
            background: var(--blue);
            color: #fff;
            box-shadow: 0 2px 8px rgba(47, 111, 232, .25)
        }

        .btn-primary:hover {
            background: var(--blue2);
            transform: translateY(-1px)
        }

        .btn:active {
            transform: scale(.97)
        }

        /* ══════════════════════════════════════
       DATE RANGE TOOLBAR
    ══════════════════════════════════════ */
        .date-toolbar {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            padding: .9rem 1.75rem;
            background: var(--surface);
            border-bottom: 1px solid var(--border);
        }

        .date-preset-group {
            display: flex;
            gap: 4px
        }

        .dp-btn {
            padding: 6px 12px;
            border: 1px solid var(--border);
            border-radius: var(--rsm);
            background: var(--s2);
            font-family: var(--body);
            font-size: 12px;
            font-weight: 500;
            color: var(--ink3);
            cursor: pointer;
            transition: all .15s;
        }

        .dp-btn.active {
            background: var(--blue);
            color: #fff;
            border-color: var(--blue)
        }

        .dp-btn:hover:not(.active) {
            background: var(--s3);
            color: var(--ink)
        }

        .date-sep {
            color: var(--ink4);
            font-size: 12px;
            padding: 0 4px
        }

        .date-input {
            padding: 6px 10px;
            border: 1.5px solid var(--border);
            border-radius: var(--rsm);
            font-family: var(--mono);
            font-size: 12px;
            color: var(--ink2);
            background: var(--s2);
            outline: none;
            transition: border .15s;
        }

        .date-input:focus {
            border-color: var(--blue);
            background: #fff
        }

        .date-apply {
            padding: 7px 16px;
            background: var(--blue);
            color: #fff;
            border: none;
            border-radius: var(--rsm);
            font-family: var(--body);
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all .15s;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .date-apply:hover {
            background: var(--blue2)
        }

        .date-label {
            font-size: 11px;
            color: var(--ink3);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .08em;
            margin-left: auto
        }

        /* ══════════════════════════════════════
       TAB NAV
    ══════════════════════════════════════ */
        .rp-tabs {
            display: flex;
            gap: 2px;
            padding: .75rem 1.75rem 0;
            background: var(--surface);
            border-bottom: 1px solid var(--border);
        }

        .rp-tab {
            display: flex;
            align-items: center;
            gap: 7px;
            padding: 10px 18px;
            border-radius: var(--r) var(--r) 0 0;
            font-family: var(--body);
            font-size: 13px;
            font-weight: 600;
            color: var(--ink3);
            cursor: pointer;
            border: none;
            background: transparent;
            border-bottom: 3px solid transparent;
            transition: all .18s;
            position: relative;
            bottom: -1px;
        }

        .rp-tab i {
            font-size: 13px
        }

        .rp-tab:hover {
            color: var(--ink);
            background: var(--bdim)
        }

        .rp-tab.active {
            color: var(--blue);
            border-bottom-color: var(--blue);
            background: var(--surface);
        }

        .rp-tab .tab-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 18px;
            height: 18px;
            padding: 0 5px;
            background: var(--rdim);
            color: var(--red);
            border-radius: 99px;
            font-size: 10px;
            font-weight: 700;
        }

        /* ══════════════════════════════════════
       TAB PANELS
    ══════════════════════════════════════ */
        .rp-panel {
            display: none;
            padding: 1.25rem 1.75rem 2rem;
            animation: tabIn .2s ease
        }

        .rp-panel.active {
            display: block
        }

        @keyframes tabIn {
            from {
                opacity: 0;
                transform: translateY(6px)
            }

            to {
                opacity: 1;
                transform: none
            }
        }

        /* ══════════════════════════════════════
       CARDS
    ══════════════════════════════════════ */
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--r);
            box-shadow: var(--sh);
            overflow: hidden;
            transition: all .2s
        }

        .card:hover {
            border-color: var(--border2);
            box-shadow: var(--shmd)
        }

        .card-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: .9rem 1.25rem;
            border-bottom: 1px solid var(--border);
        }

        .card-title {
            font-size: 12px;
            font-weight: 700;
            color: var(--ink2);
            text-transform: uppercase;
            letter-spacing: .08em;
            display: flex;
            align-items: center;
            gap: 7px
        }

        .card-title i {
            color: var(--blue)
        }

        .card-body {
            padding: 1.25rem
        }

        /* ══════════════════════════════════════
       KPI GRID
    ══════════════════════════════════════ */
        .kpi-grid {
            display: grid;
            gap: 1rem
        }

        .kpi-4 {
            grid-template-columns: repeat(4, 1fr)
        }

        .kpi-3 {
            grid-template-columns: repeat(3, 1fr)
        }

        .kpi-2 {
            grid-template-columns: repeat(2, 1fr)
        }

        .kpi-tile {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--r);
            padding: 1.1rem 1.25rem;
            position: relative;
            overflow: hidden;
            cursor: default;
            transition: all .2s;
        }

        .kpi-tile:hover {
            transform: translateY(-2px);
            box-shadow: var(--shmd);
            border-color: var(--border2)
        }

        .kpi-tile::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--ac, var(--blue));
            transform: scaleX(0);
            transform-origin: left;
            transition: transform .3s;
        }

        .kpi-tile:hover::after {
            transform: scaleX(1)
        }

        .kpi-label {
            font-size: 10px;
            font-weight: 700;
            color: var(--ink3);
            text-transform: uppercase;
            letter-spacing: .1em;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            justify-content: space-between
        }

        .kpi-icon {
            font-size: 14px
        }

        .kpi-val {
            font-family: var(--mono);
            font-size: 26px;
            font-weight: 500;
            color: var(--ink);
            line-height: 1;
            letter-spacing: -.5px
        }

        .kpi-val.sm {
            font-size: 20px
        }

        .kpi-sub {
            font-size: 11px;
            color: var(--ink3);
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 5px
        }

        .trend {
            display: inline-flex;
            align-items: center;
            gap: 3px;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 99px
        }

        .trend-up {
            color: var(--green);
            background: var(--gdim)
        }

        .trend-dn {
            color: var(--red);
            background: var(--rdim)
        }

        .trend-nt {
            color: var(--ink3);
            background: var(--s3)
        }

        /* ══════════════════════════════════════
       CHART CONTAINERS
    ══════════════════════════════════════ */
        .chart-area {
            width: 100%;
            min-height: 260px
        }

        .chart-lg {
            min-height: 320px
        }

        .chart-sm {
            min-height: 200px
        }

        .chart-loading {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 260px;
            color: var(--ink3);
            font-size: 13px;
            flex-direction: column;
            gap: 10px
        }

        .chart-loading i {
            font-size: 22px
        }

        /* ══════════════════════════════════════
       GRID LAYOUTS
    ══════════════════════════════════════ */
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem
        }

        .grid-3 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 1rem
        }

        .grid-7-5 {
            display: grid;
            grid-template-columns: 1.4fr 1fr;
            gap: 1rem
        }

        .grid-5-7 {
            display: grid;
            grid-template-columns: 1fr 1.4fr;
            gap: 1rem
        }

        .section-gap {
            display: flex;
            flex-direction: column;
            gap: 1rem
        }

        /* ══════════════════════════════════════
       TABLES (mini)
    ══════════════════════════════════════ */
        .mini-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12.5px
        }

        .mini-table th {
            padding: 8px 12px;
            text-align: left;
            font-size: 10px;
            font-weight: 700;
            color: var(--ink3);
            text-transform: uppercase;
            letter-spacing: .07em;
            border-bottom: 1.5px solid var(--border);
            background: var(--s2)
        }

        .mini-table td {
            padding: 10px 12px;
            border-bottom: 1px solid var(--border);
            color: var(--ink);
            vertical-align: middle
        }

        .mini-table tbody tr:last-child td {
            border-bottom: none
        }

        .mini-table tbody tr:hover {
            background: var(--bdim)
        }

        .cell-mono {
            font-family: var(--mono);
            font-size: 12px
        }

        .cell-right {
            text-align: right
        }

        .cell-center {
            text-align: center
        }

        /* rank badge */
        .rank {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            font-family: var(--mono);
            font-size: 11px;
            font-weight: 600
        }

        .rank-1 {
            background: #ffd700;
            color: #6b4d00
        }

        .rank-2 {
            background: #c0c0c0;
            color: #3a3a3a
        }

        .rank-3 {
            background: #cd7f32;
            color: #fff
        }

        .rank-n {
            background: var(--s3);
            color: var(--ink3)
        }

        /* pill */
        .pill {
            display: inline-block;
            padding: 3px 9px;
            border-radius: 99px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .03em
        }

        .pill-green {
            background: var(--gdim);
            color: var(--green);
            border: 1px solid rgba(21, 128, 61, .2)
        }

        .pill-red {
            background: var(--rdim);
            color: var(--red);
            border: 1px solid rgba(220, 38, 38, .2)
        }

        .pill-amber {
            background: var(--adim);
            color: var(--amber);
            border: 1px solid rgba(217, 119, 6, .2)
        }

        .pill-blue {
            background: var(--bdim);
            color: var(--blue);
            border: 1px solid var(--bmid)
        }

        .pill-violet {
            background: var(--vdim);
            color: var(--violet);
            border: 1px solid rgba(124, 58, 237, .2)
        }

        /* progress bar */
        .prog-bar-wrap {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 4px
        }

        .prog-bar {
            flex: 1;
            height: 5px;
            background: var(--s3);
            border-radius: 3px;
            overflow: hidden
        }

        .prog-fill {
            height: 100%;
            border-radius: 3px;
            transition: width .6s cubic-bezier(.4, 0, .2, 1)
        }

        .prog-val {
            font-family: var(--mono);
            font-size: 11px;
            color: var(--ink3);
            min-width: 36px;
            text-align: right
        }

        /* ══════════════════════════════════════
       HOURLY HEATMAP
    ══════════════════════════════════════ */
        .heatmap-grid {
            display: grid;
            grid-template-columns: repeat(24, 1fr);
            gap: 3px;
            padding: 0 .25rem
        }

        .heatmap-cell {
            aspect-ratio: 1;
            border-radius: 3px;
            cursor: default;
            transition: transform .15s, box-shadow .15s;
            position: relative;
        }

        .heatmap-cell:hover {
            transform: scale(1.3);
            z-index: 2;
            box-shadow: var(--shmd)
        }

        .heatmap-labels {
            display: grid;
            grid-template-columns: repeat(24, 1fr);
            gap: 3px;
            padding: 4px .25rem 0;
            margin-bottom: .5rem
        }

        .hm-label {
            font-family: var(--mono);
            font-size: 9px;
            color: var(--ink4);
            text-align: center
        }

        /* ══════════════════════════════════════
       Z-REPORT
    ══════════════════════════════════════ */
        .zreport-card {
            background: linear-gradient(135deg, #1a2744 0%, #0f1c38 100%);
            border: 1px solid rgba(255, 255, 255, .08);
            border-radius: var(--r);
            padding: 1.5rem;
            color: #fff;
        }

        .zr-title {
            font-family: var(--display);
            font-size: 22px;
            color: #fff;
            margin-bottom: 4px
        }

        .zr-sub {
            font-size: 12px;
            color: rgba(255, 255, 255, .5);
            margin-bottom: 1.5rem
        }

        .zr-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem
        }

        .zr-item {
            background: rgba(255, 255, 255, .06);
            border: 1px solid rgba(255, 255, 255, .08);
            border-radius: var(--rsm);
            padding: .85rem
        }

        .zr-item-label {
            font-size: 10px;
            color: rgba(255, 255, 255, .4);
            text-transform: uppercase;
            letter-spacing: .08em;
            margin-bottom: 5px
        }

        .zr-item-val {
            font-family: var(--mono);
            font-size: 18px;
            font-weight: 500;
            color: #fff
        }

        .zr-divider {
            border: none;
            border-top: 1px solid rgba(255, 255, 255, .08);
            margin: 1rem 0
        }

        .zr-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 7px 0;
            font-size: 13px;
            border-bottom: 1px solid rgba(255, 255, 255, .05)
        }

        .zr-row:last-child {
            border-bottom: none
        }

        .zr-row-label {
            color: rgba(255, 255, 255, .5)
        }

        .zr-row-val {
            font-family: var(--mono);
            font-weight: 500;
            color: #fff
        }

        .zr-row-val.pos {
            color: #4ade80
        }

        .zr-row-val.neg {
            color: #f87171
        }

        .zr-total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 14px;
            background: rgba(255, 255, 255, .06);
            border-radius: var(--rsm);
            margin-top: .75rem
        }

        .zr-total-label {
            font-size: 12px;
            font-weight: 700;
            color: rgba(255, 255, 255, .6);
            text-transform: uppercase;
            letter-spacing: .06em
        }

        .zr-total-val {
            font-family: var(--mono);
            font-size: 22px;
            font-weight: 500;
            color: #fff
        }

        .zr-footer {
            display: flex;
            gap: 8px;
            margin-top: 1rem
        }

        /* ══════════════════════════════════════
       CASHIER CARDS
    ══════════════════════════════════════ */
        .cashier-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem
        }

        .cashier-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--r);
            padding: 1.1rem;
            transition: all .2s;
        }

        .cashier-card:hover {
            border-color: var(--blue);
            box-shadow: var(--shmd);
            transform: translateY(-2px)
        }

        .cc-top {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: .9rem
        }

        .cc-av {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            font-weight: 700;
            color: #fff;
            flex-shrink: 0
        }

        .cc-name {
            font-weight: 700;
            font-size: 14px;
            color: var(--ink)
        }

        .cc-role {
            font-size: 11px;
            color: var(--ink3);
            margin-top: 2px
        }

        .cc-stats {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px
        }

        .cc-stat {
            background: var(--s2);
            border: 1px solid var(--border);
            border-radius: var(--rsm);
            padding: 7px 9px
        }

        .cc-stat-label {
            font-size: 10px;
            color: var(--ink3);
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-bottom: 3px
        }

        .cc-stat-val {
            font-family: var(--mono);
            font-size: 14px;
            font-weight: 500;
            color: var(--ink)
        }

        .cc-perf-bar {
            margin-top: .75rem
        }

        .cc-perf-label {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            color: var(--ink3);
            margin-bottom: 4px
        }

        .cc-bar {
            width: 100%;
            height: 6px;
            background: var(--s3);
            border-radius: 3px;
            overflow: hidden
        }

        .cc-fill {
            height: 100%;
            border-radius: 3px;
            background: linear-gradient(90deg, var(--blue), var(--violet))
        }

        /* ══════════════════════════════════════
       INVENTORY REPORT
    ══════════════════════════════════════ */
        .inv-alert-strip {
            display: flex;
            gap: .75rem;
            margin-bottom: 1rem
        }

        .inv-alert {
            flex: 1;
            padding: .85rem 1rem;
            border-radius: var(--rsm);
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 12px;
            font-weight: 500
        }

        .inv-alert.danger {
            background: var(--rdim);
            border: 1px solid rgba(220, 38, 38, .2);
            color: var(--red)
        }

        .inv-alert.warn {
            background: var(--adim);
            border: 1px solid rgba(217, 119, 6, .2);
            color: var(--amber)
        }

        .inv-alert.ok {
            background: var(--gdim);
            border: 1px solid rgba(21, 128, 61, .2);
            color: var(--green)
        }

        .inv-alert i {
            font-size: 16px;
            flex-shrink: 0
        }

        .inv-alert-num {
            font-family: var(--mono);
            font-size: 22px;
            font-weight: 600;
            display: block
        }

        /* ══════════════════════════════════════
       LOAN AGING
    ══════════════════════════════════════ */
        .aging-bar-list {
            display: flex;
            flex-direction: column;
            gap: .75rem
        }

        .aging-item {}

        .aging-label {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            margin-bottom: 5px
        }

        .aging-name {
            color: var(--ink2);
            font-weight: 500
        }

        .aging-val {
            font-family: var(--mono);
            font-weight: 600
        }

        /* ══════════════════════════════════════
       LOADING SPINNER
    ══════════════════════════════════════ */
        .spinner {
            display: inline-block;
            width: 18px;
            height: 18px;
            border: 2px solid var(--border);
            border-top-color: var(--blue);
            border-radius: 50%;
            animation: spin .7s linear infinite
        }

        @keyframes spin {
            to {
                transform: rotate(360deg)
            }
        }

        /* ══════════════════════════════════════
       EMPTY STATE
    ══════════════════════════════════════ */
        .empty-chart {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 220px;
            color: var(--ink4);
            gap: 8px
        }

        .empty-chart i {
            font-size: 32px
        }

        .empty-chart p {
            font-size: 12px
        }
    </style>
@endpush

@section('content')
    <div class="rp" x-data="reportsPage()" x-init="init()">

        {{-- ════ TOPBAR ════ --}}
        <div class="rp-top">
            <div class="rp-title">Afghan <em>POS</em> — Reports</div>
            <div class="top-right">
                <button class="btn btn-ghost" @click="printReport()">
                    <i class="fas fa-print"></i> Print
                </button>
                <button class="btn btn-ghost" @click="exportPdf()">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </button>
                <button class="btn btn-primary" @click="exportCsv()">
                    <i class="fas fa-file-csv"></i> Export CSV
                </button>
            </div>
        </div>

        {{-- ════ DATE TOOLBAR ════ --}}
        <div class="date-toolbar">
            <div class="date-preset-group">
                <button type="button" class="dp-btn" :class="preset === 'today' ? 'active' : ''"
                    @click="setPreset('today')">Today</button>
                <button type="button" class="dp-btn" :class="preset === 'yesterday' ? 'active' : ''"
                    @click="setPreset('yesterday')">Yesterday</button>
                <button type="button" class="dp-btn" :class="preset === 'week' ? 'active' : ''" @click="setPreset('week')">This
                    Week</button>
                <button type="button" class="dp-btn" :class="preset === 'month' ? 'active' : ''" @click="setPreset('month')">This
                    Month</button>
                <button type="button" class="dp-btn" :class="preset === 'quarter' ? 'active' : ''"
                    @click="setPreset('quarter')">Quarter</button>
                <button type="button" class="dp-btn" :class="preset === 'year' ? 'active' : ''" @click="setPreset('year')">This
                    Year</button>
            </div>
            <span class="date-sep">|</span>
            <input type="date" class="date-input" x-model="dateFrom">
            <span class="date-sep">→</span>
            <input type="date" class="date-input" x-model="dateTo">
            <button type="button" class="date-apply" @click="loadAll()">
                <i class="fas fa-rotate"></i> Apply
            </button>
            <span class="date-label" x-text="dateRangeLabel"></span>
        </div>

        {{-- ════ TABS ════ --}}
        <div class="rp-tabs">
            <button type="button" class="rp-tab" :class="tab === 'overview' ? 'active' : ''" @click="switchTab('overview')">
                <i class="fas fa-chart-pie"></i> Overview
            </button>
            <button type="button" class="rp-tab" :class="tab === 'sales' ? 'active' : ''" @click="switchTab('sales')">
                <i class="fas fa-chart-line"></i> Sales
            </button>
            <button type="button" class="rp-tab" :class="tab === 'products' ? 'active' : ''" @click="switchTab('products')">
                <i class="fas fa-boxes-stacked"></i> Products
            </button>
            <button type="button" class="rp-tab" :class="tab === 'inventory' ? 'active' : ''" @click="switchTab('inventory')">
                <i class="fas fa-warehouse"></i> Inventory
            </button>
            <button type="button" class="rp-tab" :class="tab === 'cashiers' ? 'active' : ''" @click="switchTab('cashiers')">
                <i class="fas fa-users"></i> Cashiers
            </button>
            <button type="button" class="rp-tab" :class="tab === 'loans' ? 'active' : ''" @click="switchTab('loans')">
                <i class="fas fa-file-invoice-dollar"></i> Loans
                <span class="tab-badge" x-show="data.loan_overdue > 0" x-text="data.loan_overdue"></span>
            </button>
            <button type="button" class="rp-tab" :class="tab === 'zreport' ? 'active' : ''" @click="switchTab('zreport')">
                <i class="fas fa-file-alt"></i> Z-Report
            </button>
        </div>

        {{-- ══════════════════════════════════════════════
     TAB: OVERVIEW
══════════════════════════════════════════════ --}}
        <div class="rp-panel" :class="tab === 'overview' ? 'active' : ''">
            <div class="section-gap">

                {{-- KPI strip --}}
                <div class="kpi-grid kpi-4">
                    <div class="kpi-tile" style="--ac:var(--blue)">
                        <div class="kpi-label">Total Revenue <span class="kpi-icon" style="color:var(--blue)"><i
                                    class="fas fa-coins"></i></span></div>
                        <div class="kpi-val" x-text="'Af ' + fmt(data.total_revenue || 0)"></div>
                        <div class="kpi-sub">
                            <span class="trend" :class="data.revenue_trend >= 0 ? 'trend-up' : 'trend-dn'">
                                <i :class="data.revenue_trend >= 0 ? 'fas fa-arrow-up' : 'fas fa-arrow-down'"></i>
                                <span x-text="Math.abs(data.revenue_trend||0).toFixed(1) + '%'"></span>
                            </span>
                            vs previous period
                        </div>
                    </div>
                    <div class="kpi-tile" style="--ac:var(--green)">
                        <div class="kpi-label">Net Profit <span class="kpi-icon" style="color:var(--green)"><i
                                    class="fas fa-chart-line"></i></span></div>
                        <div class="kpi-val" style="color:var(--green)" x-text="'Af ' + fmt(data.net_profit || 0)"></div>
                        <div class="kpi-sub">
                            <span class="trend" :class="data.margin >= 0 ? 'trend-up' : 'trend-dn'">
                                <span x-text="(data.margin||0).toFixed(1) + '% margin'"></span>
                            </span>
                        </div>
                    </div>
                    <div class="kpi-tile" style="--ac:var(--amber)">
                        <div class="kpi-label">Transactions <span class="kpi-icon" style="color:var(--amber)"><i
                                    class="fas fa-receipt"></i></span></div>
                        <div class="kpi-val" x-text="fmt(data.total_transactions || 0)"></div>
                        <div class="kpi-sub">
                            Avg Af <span x-text="fmt(data.avg_transaction || 0)"></span> per sale
                        </div>
                    </div>
                    <div class="kpi-tile" style="--ac:var(--violet)">
                        <div class="kpi-label">Items Sold <span class="kpi-icon" style="color:var(--violet)"><i
                                    class="fas fa-shopping-bag"></i></span></div>
                        <div class="kpi-val" x-text="fmt(data.items_sold || 0)"></div>
                        <div class="kpi-sub">across <span x-text="data.total_transactions || 0"></span> orders</div>
                    </div>
                </div>

                <div class="grid-7-5">
                    {{-- Revenue chart --}}
                    <div class="card">
                        <div class="card-head">
                            <div class="card-title"><i class="fas fa-chart-area"></i> Revenue & Profit Over Time</div>
                            <div style="display:flex;gap:6px">
                                <button type="button" class="dp-btn" :class="chartGranularity === 'hourly' ? 'active' : ''"
                                    @click="chartGranularity='hourly';renderOverviewChart()">Hourly</button>
                                <button type="button" class="dp-btn" :class="chartGranularity === 'daily' ? 'active' : ''"
                                    @click="chartGranularity='daily';renderOverviewChart()">Daily</button>
                                <button type="button" class="dp-btn" :class="chartGranularity === 'weekly' ? 'active' : ''"
                                    @click="chartGranularity='weekly';renderOverviewChart()">Weekly</button>
                                <button type="button" class="dp-btn" :class="chartGranularity === 'monthly' ? 'active' : ''"
                                    @click="chartGranularity='monthly';renderOverviewChart()">Monthly</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="chart-overview" class="chart-area chart-lg"></div>
                        </div>
                    </div>

                    {{-- Donut: payment method --}}
                    <div class="card">
                        <div class="card-head">
                            <div class="card-title"><i class="fas fa-wallet"></i> Payment Methods</div>
                        </div>
                        <div class="card-body">
                            <div id="chart-payment" class="chart-area chart-sm" style="min-height:180px"></div>
                            <div style="display:flex;flex-direction:column;gap:8px;margin-top:.75rem">
                                <div
                                    style="display:flex;justify-content:space-between;align-items:center;padding:7px 10px;background:var(--s2);border-radius:var(--rsm)">
                                    <span style="display:flex;align-items:center;gap:7px;font-size:12px;color:var(--ink2)">
                                        <span
                                            style="width:10px;height:10px;border-radius:50%;background:var(--blue);display:inline-block"></span>
                                        Cash
                                    </span>
                                    <span class="cell-mono" x-text="'Af ' + fmt(data.cash_sales || 0)"></span>
                                </div>
                                <div
                                    style="display:flex;justify-content:space-between;align-items:center;padding:7px 10px;background:var(--s2);border-radius:var(--rsm)">
                                    <span style="display:flex;align-items:center;gap:7px;font-size:12px;color:var(--ink2)">
                                        <span
                                            style="width:10px;height:10px;border-radius:50%;background:var(--amber);display:inline-block"></span>
                                        Loan
                                    </span>
                                    <span class="cell-mono" x-text="'Af ' + fmt(data.loan_sales || 0)"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Hourly heatmap --}}
                <div class="card">
                    <div class="card-head">
                        <div class="card-title"><i class="fas fa-clock"></i> Sales by Hour of Day</div>
                        <span style="font-size:11px;color:var(--ink3)">Darker = more sales</span>
                    </div>
                    <div class="card-body">
                        <div class="heatmap-labels">
                            <template x-for="h in 24" :key="h">
                                <div class="hm-label" x-text="(h-1).toString().padStart(2,'0')"></div>
                            </template>
                        </div>
                        <div class="heatmap-grid" id="heatmap-cells">
                            <template x-for="(val, idx) in (data.hourly_heatmap || Array(24).fill(0))"
                                :key="idx">
                                <div class="heatmap-cell" :style="`background:${heatColor(val, data.hourly_max||1)}`"
                                    :title="`${idx}:00 — Af ${fmt(val)}`"></div>
                            </template>
                        </div>
                        <div style="display:flex;align-items:center;gap:8px;margin-top:10px;justify-content:flex-end">
                            <span style="font-size:10px;color:var(--ink3)">Low</span>
                            <div style="display:flex;gap:2px">
                                <template x-for="i in [0.1,0.3,0.5,0.7,0.9,1]" :key="i">
                                    <div style="width:16px;height:10px;border-radius:2px"
                                        :style="`background:${heatColor(i,1)}`"></div>
                                </template>
                            </div>
                            <span style="font-size:10px;color:var(--ink3)">High</span>
                        </div>
                    </div>
                </div>

                {{-- Category breakdown --}}
                <div class="grid-2">
                    <div class="card">
                        <div class="card-head">
                            <div class="card-title"><i class="fas fa-tag"></i> Revenue by Category</div>
                        </div>
                        <div class="card-body">
                            <div id="chart-category" class="chart-area" style="min-height:220px"></div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-head">
                            <div class="card-title"><i class="fas fa-list-ol"></i> Top Categories</div>
                        </div>
                        <div class="card-body" style="padding:.75rem">
                            <template x-for="(cat, idx) in (data.top_categories || [])" :key="cat.name">
                                <div style="margin-bottom:.85rem">
                                    <div
                                        style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:5px">
                                        <span style="font-weight:600;color:var(--ink)" x-text="cat.name"></span>
                                        <span class="cell-mono" x-text="'Af ' + fmt(cat.revenue)"></span>
                                    </div>
                                    <div class="prog-bar-wrap">
                                        <div class="prog-bar">
                                            <div class="prog-fill"
                                                :style="`width:${cat.pct}%;background:${['var(--blue)','var(--violet)','var(--teal)','var(--amber)','var(--green)'][idx%5]}`">
                                            </div>
                                        </div>
                                        <span class="prog-val" x-text="cat.pct.toFixed(1) + '%'"></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- ══════════════════════════════════════════════
     TAB: SALES
══════════════════════════════════════════════ --}}
        <div class="rp-panel" :class="tab === 'sales' ? 'active' : ''">
            <div class="section-gap">

                <div class="kpi-grid kpi-4">
                    <div class="kpi-tile" style="--ac:var(--blue)">
                        <div class="kpi-label">Gross Revenue <span class="kpi-icon"><i class="fas fa-coins"
                                    style="color:var(--blue)"></i></span></div>
                        <div class="kpi-val sm" x-text="'Af ' + fmt(data.total_revenue||0)"></div>
                        <div class="kpi-sub">before discounts &amp; returns</div>
                    </div>
                    <div class="kpi-tile" style="--ac:var(--red)">
                        <div class="kpi-label">Discounts Given <span class="kpi-icon"><i class="fas fa-tag"
                                    style="color:var(--red)"></i></span></div>
                        <div class="kpi-val sm" style="color:var(--red)" x-text="'Af ' + fmt(data.total_discounts||0)">
                        </div>
                        <div class="kpi-sub" x-text="(data.discount_rate||0).toFixed(1) + '% of gross'"></div>
                    </div>
                    <div class="kpi-tile" style="--ac:var(--amber)">
                        <div class="kpi-label">Returns <span class="kpi-icon"><i class="fas fa-rotate-left"
                                    style="color:var(--amber)"></i></span></div>
                        <div class="kpi-val sm" style="color:var(--amber)" x-text="fmt(data.return_count||0) + ' sales'">
                        </div>
                        <div class="kpi-sub" x-text="'Af ' + fmt(data.return_amount||0) + ' refunded'"></div>
                    </div>
                    <div class="kpi-tile" style="--ac:var(--green)">
                        <div class="kpi-label">Avg Daily Sales <span class="kpi-icon"><i class="fas fa-calendar-check"
                                    style="color:var(--green)"></i></span></div>
                        <div class="kpi-val sm" x-text="'Af ' + fmt(data.avg_daily_sales||0)"></div>
                        <div class="kpi-sub">across the selected period</div>
                    </div>
                </div>

                {{-- Daily trend --}}
                <div class="card">
                    <div class="card-head">
                        <div class="card-title"><i class="fas fa-chart-bar"></i> Daily Sales Breakdown</div>
                    </div>
                    <div class="card-body">
                        <div id="chart-daily-sales" class="chart-area chart-lg"></div>
                    </div>
                </div>

                <div class="grid-2">
                    {{-- Weekday performance --}}
                    <div class="card">
                        <div class="card-head">
                            <div class="card-title"><i class="fas fa-calendar-week"></i> Sales by Day of Week</div>
                        </div>
                        <div class="card-body">
                            <div id="chart-weekday" class="chart-area"></div>
                        </div>
                    </div>
                    {{-- Top transactions --}}
                    <div class="card">
                        <div class="card-head">
                            <div class="card-title"><i class="fas fa-receipt"></i> Largest Transactions</div>
                        </div>
                        <div style="overflow-x:auto">
                            <table class="mini-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Sale ID</th>
                                        <th>Customer</th>
                                        <th>Method</th>
                                        <th class="cell-right">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(s,i) in (data.top_sales||[])" :key="s.id">
                                        <tr>
                                            <td><span class="rank"
                                                    :class="['rank-1', 'rank-2', 'rank-3', 'rank-n', 'rank-n'][i]"
                                                    x-text="i+1"></span></td>
                                            <td class="cell-mono" x-text="s.local_id"></td>
                                            <td style="font-size:12px" x-text="s.customer || 'Walk-in'"></td>
                                            <td><span class="pill" :class="s.method === 'cash' ? 'pill-blue' : 'pill-amber'"
                                                    x-text="s.method"></span></td>
                                            <td class="cell-right cell-mono" style="color:var(--blue)"
                                                x-text="'Af ' + fmt(s.total)"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════
     TAB: PRODUCTS
══════════════════════════════════════════════ --}}
        <div class="rp-panel" :class="tab === 'products' ? 'active' : ''">
            <div class="section-gap">
                <div class="grid-2">
                    {{-- Top sellers --}}
                    <div class="card">
                        <div class="card-head">
                            <div class="card-title"><i class="fas fa-trophy"></i> Top Selling Products</div>
                        </div>
                        <div style="overflow-x:auto">
                            <table class="mini-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Product</th>
                                        <th class="cell-center">Qty Sold</th>
                                        <th class="cell-right">Revenue</th>
                                        <th class="cell-right">Profit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(p,i) in (data.top_products||[])" :key="p.sku">
                                        <tr>
                                            <td><span class="rank"
                                                    :class="['rank-1', 'rank-2', 'rank-3', 'rank-n', 'rank-n'][i] || 'rank-n'"
                                                    x-text="i+1"></span></td>
                                            <td>
                                                <div style="font-weight:600;font-size:12px" x-text="p.name"></div>
                                                <div class="cell-mono" style="font-size:10px" x-text="p.sku"></div>
                                            </td>
                                            <td class="cell-center cell-mono" x-text="fmt(p.qty_sold)"></td>
                                            <td class="cell-right cell-mono" style="color:var(--blue)"
                                                x-text="'Af ' + fmt(p.revenue)"></td>
                                            <td class="cell-right cell-mono" style="color:var(--green)"
                                                x-text="'Af ' + fmt(p.profit)"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Bottom sellers --}}
                    <div class="card">
                        <div class="card-head">
                            <div class="card-title"><i class="fas fa-arrow-trend-down" style="color:var(--red)"></i> Slow
                                Moving Products</div>
                        </div>
                        <div style="overflow-x:auto">
                            <table class="mini-table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th class="cell-center">Qty Sold</th>
                                        <th class="cell-right">Stock Left</th>
                                        <th class="cell-right">Revenue</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="p in (data.slow_products||[])" :key="p.sku">
                                        <tr>
                                            <td>
                                                <div style="font-weight:600;font-size:12px" x-text="p.name"></div>
                                                <div class="cell-mono" style="font-size:10px" x-text="p.sku"></div>
                                            </td>
                                            <td class="cell-center cell-mono" x-text="fmt(p.qty_sold)"></td>
                                            <td class="cell-right">
                                                <span class="pill" :class="p.stock > 10 ? 'pill-blue' : 'pill-red'"
                                                    x-text="p.stock"></span>
                                            </td>
                                            <td class="cell-right cell-mono" x-text="'Af ' + fmt(p.revenue)"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Product revenue chart --}}
                <div class="card">
                    <div class="card-head">
                        <div class="card-title"><i class="fas fa-chart-bar"></i> Revenue by Product (Top 10)</div>
                    </div>
                    <div class="card-body">
                        <div id="chart-products" class="chart-area chart-lg"></div>
                    </div>
                </div>

                {{-- Profit margin table --}}
                <div class="card">
                    <div class="card-head">
                        <div class="card-title"><i class="fas fa-percent"></i> Profit Margin by Product</div>
                    </div>
                    <div style="overflow-x:auto">
                        <table class="mini-table">
                            <thead>
                                <tr>
                                    <th>Product / SKU</th>
                                    <th class="cell-right">Sale Price</th>
                                    <th class="cell-right">Cost</th>
                                    <th class="cell-right">Margin %</th>
                                    <th class="cell-right">Profit / Unit</th>
                                    <th>Margin Bar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="p in (data.margin_table||[])" :key="p.sku">
                                    <tr>
                                        <td>
                                            <div style="font-weight:600;font-size:12px" x-text="p.name"></div>
                                            <div class="cell-mono" style="font-size:10px;color:var(--ink3)"
                                                x-text="p.sku"></div>
                                        </td>
                                        <td class="cell-right cell-mono" x-text="'Af ' + fmt(p.price)"></td>
                                        <td class="cell-right cell-mono" x-text="'Af ' + fmt(p.cost)"></td>
                                        <td class="cell-right">
                                            <span class="pill"
                                                :class="p.margin >= 30 ? 'pill-green' : p.margin >= 10 ? 'pill-amber' :
                                                    'pill-red'"
                                                x-text="p.margin.toFixed(1) + '%'"></span>
                                        </td>
                                        <td class="cell-right cell-mono" style="color:var(--green)"
                                            x-text="'Af ' + fmt(p.profit_unit)"></td>
                                        <td style="width:120px">
                                            <div class="prog-bar">
                                                <div class="prog-fill"
                                                    :style="`width:${Math.min(p.margin,100)}%;background:${p.margin>=30?'var(--green)':p.margin>=10?'var(--amber)':'var(--red)'}`">
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════
     TAB: INVENTORY
══════════════════════════════════════════════ --}}
        <div class="rp-panel" :class="tab === 'inventory' ? 'active' : ''">
            <div class="section-gap">

                <div class="inv-alert-strip">
                    <div class="inv-alert danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <div>
                            <span class="inv-alert-num" x-text="data.stock_zero || 0"></span>
                            Out of Stock
                        </div>
                    </div>
                    <div class="inv-alert warn">
                        <i class="fas fa-triangle-exclamation"></i>
                        <div>
                            <span class="inv-alert-num" x-text="data.stock_low || 0"></span>
                            Low Stock
                        </div>
                    </div>
                    <div class="inv-alert warn">
                        <i class="fas fa-clock"></i>
                        <div>
                            <span class="inv-alert-num" x-text="data.expiring_30 || 0"></span>
                            Expiring ≤ 30 days
                        </div>
                    </div>
                    <div class="inv-alert ok">
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <span class="inv-alert-num" x-text="data.stock_ok || 0"></span>
                            Healthy Stock
                        </div>
                    </div>
                </div>

                <div class="kpi-grid kpi-3">
                    <div class="kpi-tile" style="--ac:var(--blue)">
                        <div class="kpi-label">Inventory Value (Cost)</div>
                        <div class="kpi-val sm" x-text="'Af ' + fmt(data.inv_value_cost||0)"></div>
                        <div class="kpi-sub">at purchase cost price</div>
                    </div>
                    <div class="kpi-tile" style="--ac:var(--green)">
                        <div class="kpi-label">Inventory Value (Retail)</div>
                        <div class="kpi-val sm" x-text="'Af ' + fmt(data.inv_value_retail||0)"></div>
                        <div class="kpi-sub">at current sale price</div>
                    </div>
                    <div class="kpi-tile" style="--ac:var(--amber)">
                        <div class="kpi-label">Potential Profit</div>
                        <div class="kpi-val sm" style="color:var(--green)"
                            x-text="'Af ' + fmt((data.inv_value_retail||0)-(data.inv_value_cost||0))"></div>
                        <div class="kpi-sub">if all stock sold today</div>
                    </div>
                </div>

                <div class="grid-2">
                    <div class="card">
                        <div class="card-head">
                            <div class="card-title"><i class="fas fa-chart-pie"></i> Stock Status Distribution</div>
                        </div>
                        <div class="card-body">
                            <div id="chart-stock-status" class="chart-area"></div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-head">
                            <div class="card-title"><i class="fas fa-tag"></i> Inventory Value by Category</div>
                        </div>
                        <div class="card-body">
                            <div id="chart-inv-category" class="chart-area"></div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-head">
                        <div class="card-title"><i class="fas fa-arrow-trend-down" style="color:var(--red)"></i> Critical
                            Stock Levels</div>
                    </div>
                    <div style="overflow-x:auto">
                        <table class="mini-table">
                            <thead>
                                <tr>
                                    <th>Product / SKU</th>
                                    <th>Category</th>
                                    <th class="cell-center">Current Stock</th>
                                    <th class="cell-center">Threshold</th>
                                    <th class="cell-right">Cost Value</th>
                                    <th>Expiry</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="p in (data.critical_stock||[])" :key="p.sku">
                                    <tr>
                                        <td>
                                            <div style="font-weight:600;font-size:12px" x-text="p.name"></div>
                                            <div class="cell-mono" style="font-size:10px;color:var(--ink3)"
                                                x-text="p.sku"></div>
                                        </td>
                                        <td><span class="pill pill-blue" x-text="p.category"></span></td>
                                        <td class="cell-center"><span class="cell-mono" style="font-weight:700"
                                                :style="p.stock === 0 ? 'color:var(--red)' : 'color:var(--amber)'"
                                                x-text="p.stock"></span></td>
                                        <td class="cell-center cell-mono" x-text="p.threshold"></td>
                                        <td class="cell-right cell-mono" x-text="'Af ' + fmt(p.cost_value)"></td>
                                        <td class="cell-mono" style="font-size:11px"
                                            :style="p.expiry_days < 30 ? 'color:var(--red)' : 'color:var(--ink3)'"
                                            x-text="p.expiry || '—'"></td>
                                        <td><span class="pill" :class="p.stock === 0 ? 'pill-red' : 'pill-amber'"
                                                x-text="p.stock===0?'Out of Stock':'Low Stock'"></span></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════
     TAB: CASHIERS
══════════════════════════════════════════════ --}}
        <div class="rp-panel" :class="tab === 'cashiers' ? 'active' : ''">
            <div class="section-gap">

                <div class="cashier-grid">
                    <template x-for="(c,i) in (data.cashiers||[])" :key="c.id">
                        <div class="cashier-card">
                            <div class="cc-top">
                                <div class="cc-av"
                                    :style="`background:${['#2f6fe8','#7c3aed','#0891b2','#15803d','#d97706'][i%5]}`"
                                    x-text="initials(c.name)"></div>
                                <div>
                                    <div class="cc-name" x-text="c.name"></div>
                                    <div class="cc-role">Cashier</div>
                                </div>
                            </div>
                            <div class="cc-stats">
                                <div class="cc-stat">
                                    <div class="cc-stat-label">Sales</div>
                                    <div class="cc-stat-val" x-text="fmt(c.total_sales)"></div>
                                </div>
                                <div class="cc-stat">
                                    <div class="cc-stat-label">Transactions</div>
                                    <div class="cc-stat-val" x-text="c.tx_count"></div>
                                </div>
                                <div class="cc-stat">
                                    <div class="cc-stat-label">Avg Ticket</div>
                                    <div class="cc-stat-val" style="font-size:12px" x-text="'Af ' + fmt(c.avg_ticket)">
                                    </div>
                                </div>
                                <div class="cc-stat">
                                    <div class="cc-stat-label">Shifts</div>
                                    <div class="cc-stat-val" x-text="c.shift_count"></div>
                                </div>
                            </div>
                            <div class="cc-perf-bar">
                                <div class="cc-perf-label">
                                    <span>Performance</span>
                                    <span x-text="c.pct + '%'"></span>
                                </div>
                                <div class="cc-bar">
                                    <div class="cc-fill" :style="`width:${c.pct}%`"></div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="card">
                    <div class="card-head">
                        <div class="card-title"><i class="fas fa-chart-bar"></i> Cashier Sales Comparison</div>
                    </div>
                    <div class="card-body">
                        <div id="chart-cashiers" class="chart-area"></div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-head">
                        <div class="card-title"><i class="fas fa-clock-rotate-left"></i> Shift History</div>
                    </div>
                    <div style="overflow-x:auto">
                        <table class="mini-table">
                            <thead>
                                <tr>
                                    <th>Cashier</th>
                                    <th>Opened</th>
                                    <th>Closed</th>
                                    <th class="cell-right">Starting Cash</th>
                                    <th class="cell-right">Expected Cash</th>
                                    <th class="cell-right">Actual Cash</th>
                                    <th class="cell-right">Discrepancy</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="s in (data.shifts||[])" :key="s.id">
                                    <tr>
                                        <td style="font-weight:600;font-size:12px" x-text="s.cashier"></td>
                                        <td class="cell-mono" style="font-size:11px" x-text="s.opened_at"></td>
                                        <td class="cell-mono" style="font-size:11px" x-text="s.closed_at || '—'"></td>
                                        <td class="cell-right cell-mono" x-text="'Af ' + fmt(s.starting_cash)"></td>
                                        <td class="cell-right cell-mono"
                                            x-text="s.expected_cash ? 'Af ' + fmt(s.expected_cash) : '—'"></td>
                                        <td class="cell-right cell-mono"
                                            x-text="s.actual_cash ? 'Af ' + fmt(s.actual_cash) : '—'"></td>
                                        <td class="cell-right cell-mono"
                                            :style="(s.discrepancy || 0) < 0 ? 'color:var(--red)' : 'color:var(--green)'"
                                            x-text="s.discrepancy ? ((s.discrepancy > 0 ? '+' : '') + 'Af ' + fmt(s.discrepancy)) : '—'">
                                        </td>
                                        <td><span class="pill" :class="s.is_closed ? 'pill-blue' : 'pill-green'"
                                                x-text="s.is_closed ? 'Closed' : 'Active'"></span></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════
     TAB: LOANS
══════════════════════════════════════════════ --}}
        <div class="rp-panel" :class="tab === 'loans' ? 'active' : ''">
            <div class="section-gap">

                <div class="kpi-grid kpi-4">
                    <div class="kpi-tile" style="--ac:var(--amber)">
                        <div class="kpi-label">Total Outstanding</div>
                        <div class="kpi-val sm" style="color:var(--amber)"
                            x-text="'Af ' + fmt(data.loan_outstanding||0)"></div>
                        <div class="kpi-sub" x-text="(data.loan_active_count||0) + ' active loans'"></div>
                    </div>
                    <div class="kpi-tile" style="--ac:var(--red)">
                        <div class="kpi-label">Overdue Balance</div>
                        <div class="kpi-val sm" style="color:var(--red)"
                            x-text="'Af ' + fmt(data.loan_overdue_amount||0)"></div>
                        <div class="kpi-sub" x-text="(data.loan_overdue||0) + ' loans overdue'"></div>
                    </div>
                    <div class="kpi-tile" style="--ac:var(--green)">
                        <div class="kpi-label">Collected This Period</div>
                        <div class="kpi-val sm" style="color:var(--green)" x-text="'Af ' + fmt(data.loan_collected||0)">
                        </div>
                        <div class="kpi-sub" x-text="(data.loan_payment_count||0) + ' payments received'"></div>
                    </div>
                    <div class="kpi-tile" style="--ac:var(--blue)">
                        <div class="kpi-label">New Loans Issued</div>
                        <div class="kpi-val sm" x-text="'Af ' + fmt(data.loan_new_amount||0)"></div>
                        <div class="kpi-sub" x-text="(data.loan_new_count||0) + ' loans created'"></div>
                    </div>
                </div>

                <div class="grid-7-5">
                    <div class="card">
                        <div class="card-head">
                            <div class="card-title"><i class="fas fa-chart-line"></i> Loan Issuance vs Collection</div>
                        </div>
                        <div class="card-body">
                            <div id="chart-loans" class="chart-area"></div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-head">
                            <div class="card-title"><i class="fas fa-layer-group"></i> Loan Aging Buckets</div>
                        </div>
                        <div class="card-body">
                            <div class="aging-bar-list">
                                <template x-for="bucket in (data.loan_aging||[])" :key="bucket.label">
                                    <div class="aging-item">
                                        <div class="aging-label">
                                            <span class="aging-name" x-text="bucket.label"></span>
                                            <span class="aging-val" :style="bucket.color"
                                                x-text="'Af ' + fmt(bucket.amount)"></span>
                                        </div>
                                        <div class="prog-bar-wrap">
                                            <div class="prog-bar">
                                                <div class="prog-fill"
                                                    :style="`width:${bucket.pct}%;background:${bucket.fill}`"></div>
                                            </div>
                                            <span class="prog-val" x-text="bucket.count + ' loans'"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-head">
                        <div class="card-title"><i class="fas fa-triangle-exclamation" style="color:var(--red)"></i>
                            Overdue Loans</div>
                    </div>
                    <div style="overflow-x:auto">
                        <table class="mini-table">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Phone</th>
                                    <th class="cell-right">Original</th>
                                    <th class="cell-right">Paid</th>
                                    <th class="cell-right">Remaining</th>
                                    <th>Due Date</th>
                                    <th>Days Overdue</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="l in (data.overdue_loans||[])" :key="l.id">
                                    <tr>
                                        <td style="font-weight:600;font-size:12px" x-text="l.customer"></td>
                                        <td class="cell-mono" style="font-size:11px" x-text="l.phone"></td>
                                        <td class="cell-right cell-mono" x-text="'Af ' + fmt(l.original)"></td>
                                        <td class="cell-right cell-mono" style="color:var(--green)"
                                            x-text="'Af ' + fmt(l.paid)"></td>
                                        <td class="cell-right cell-mono" style="color:var(--red);font-weight:700"
                                            x-text="'Af ' + fmt(l.remaining)"></td>
                                        <td class="cell-mono" style="font-size:11px" x-text="l.due_date"></td>
                                        <td class="cell-center">
                                            <span class="pill pill-red" x-text="l.days_overdue + 'd'"></span>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════
     TAB: Z-REPORT
══════════════════════════════════════════════ --}}
        <div class="rp-panel" :class="tab === 'zreport' ? 'active' : ''">
            <div class="section-gap">

                {{-- Shift selector --}}
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
                    <select class="date-input" style="min-width:280px" x-model="selectedShift" @change="loadZReport()">
                        <option value="">— Select a Shift —</option>
                        <template x-for="s in (data.shifts||[])" :key="s.id">
                            <option :value="s.id"
                                x-text="s.cashier + ' · ' + s.opened_at + (s.is_closed ? '' : ' (Active)')"></option>
                        </template>
                    </select>
                    <button type="button" class="date-apply" @click="printZReport()">
                        <i class="fas fa-print"></i> Print Z-Report
                    </button>
                </div>

                <div id="zreport-content">
                    <div class="zreport-card">
                        <div class="zr-title">Afghan POS — Shift Report</div>
                        <div class="zr-sub"
                            x-text="zreport.cashier ? ('Cashier: ' + zreport.cashier + ' · ' + zreport.shift_date) : 'Select a shift above to generate report'">
                        </div>

                        <div class="zr-grid">
                            <div class="zr-item">
                                <div class="zr-item-label">Total Sales</div>
                                <div class="zr-item-val" x-text="'Af ' + fmt(zreport.total_sales||0)"></div>
                            </div>
                            <div class="zr-item">
                                <div class="zr-item-label">Transactions</div>
                                <div class="zr-item-val" x-text="zreport.tx_count||0"></div>
                            </div>
                            <div class="zr-item">
                                <div class="zr-item-label">Items Sold</div>
                                <div class="zr-item-val" x-text="zreport.items_sold||0"></div>
                            </div>
                            <div class="zr-item">
                                <div class="zr-item-label">Avg Ticket</div>
                                <div class="zr-item-val" x-text="'Af ' + fmt(zreport.avg_ticket||0)"></div>
                            </div>
                        </div>

                        <hr class="zr-divider">

                        <div class="zr-row"><span class="zr-row-label">Starting Cash</span><span class="zr-row-val"
                                x-text="'Af ' + fmt(zreport.starting_cash||0)"></span></div>
                        <div class="zr-row"><span class="zr-row-label">Cash Sales</span><span class="zr-row-val pos"
                                x-text="'+ Af ' + fmt(zreport.cash_sales||0)"></span></div>
                        <div class="zr-row"><span class="zr-row-label">Loan Sales</span><span class="zr-row-val"
                                x-text="'Af ' + fmt(zreport.loan_sales||0)"></span></div>
                        <div class="zr-row"><span class="zr-row-label">Discounts Given</span><span class="zr-row-val neg"
                                x-text="'- Af ' + fmt(zreport.discounts||0)"></span></div>
                        <div class="zr-row"><span class="zr-row-label">Returns / Refunds</span><span
                                class="zr-row-val neg" x-text="'- Af ' + fmt(zreport.returns||0)"></span></div>

                        <div class="zr-total-row">
                            <span class="zr-total-label">Expected Cash in Drawer</span>
                            <span class="zr-total-val" x-text="'Af ' + fmt(zreport.expected_cash||0)"></span>
                        </div>

                        <template x-if="zreport.actual_cash">
                            <div>
                                <hr class="zr-divider">
                                <div class="zr-row"><span class="zr-row-label">Actual Cash (Counted)</span><span
                                        class="zr-row-val" x-text="'Af ' + fmt(zreport.actual_cash||0)"></span></div>
                                <div class="zr-row">
                                    <span class="zr-row-label">Discrepancy</span>
                                    <span class="zr-row-val" :class="(zreport.discrepancy || 0) >= 0 ? 'pos' : 'neg'"
                                        x-text="((zreport.discrepancy||0) >= 0 ? '+' : '') + 'Af ' + fmt(zreport.discrepancy||0)"></span>
                                </div>
                                <div x-show="zreport.discrepancy_note"
                                    style="margin-top:.5rem;padding:8px 10px;background:rgba(255,255,255,.06);border-radius:var(--rsm);font-size:12px;color:rgba(255,255,255,.5)"
                                    x-text="'Note: ' + zreport.discrepancy_note"></div>
                            </div>
                        </template>

                        <div class="zr-footer">
                            <button type="button" class="btn btn-ghost" @click="printZReport()">
                                <i class="fas fa-print"></i> Print
                            </button>
                            <button type="button" class="btn btn-ghost" @click="exportZReportCsv()">
                                <i class="fas fa-file-csv"></i> Export CSV
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>{{-- /rp --}}
@endsection

@push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('reportsPage', () => ({
                tab: 'overview',
                preset: 'today',
                dateFrom: '',
                dateTo: '',
                data: {},
                zreport: {},
                selectedShift: '',
                chartGranularity: 'daily',
                charts: {},

                urls: {
                    report: '{{ route('pos.reports.data') }}',
                    zreport: '{{ route('pos.reports.zreport') }}',
                    export: '{{ route('pos.reports.export') }}',
                    csrf: document.querySelector('meta[name=csrf-token]').content,
                },

                get dateRangeLabel() {
                    if (!this.dateFrom || !this.dateTo) return '';
                    return this.dateFrom + ' → ' + this.dateTo;
                },

                /* ── Init ── */
                init() {
                    this.setPreset('today');
                },

                /* ── Presets ── */
                setPreset(p) {
                    this.preset = p;
                    const now = new Date();
                    const fmt = d => d.toISOString().split('T')[0];
                    const sod = d => {
                        const x = new Date(d);
                        x.setHours(0, 0, 0, 0);
                        return x;
                    };

                    switch (p) {
                        case 'today':
                            this.dateFrom = this.dateTo = fmt(now);
                            break;
                        case 'yesterday':
                            const y = new Date(now);
                            y.setDate(y.getDate() - 1);
                            this.dateFrom = this.dateTo = fmt(y);
                            break;
                        case 'week':
                            const ws = new Date(now);
                            ws.setDate(ws.getDate() - ws.getDay());
                            this.dateFrom = fmt(ws);
                            this.dateTo = fmt(now);
                            break;
                        case 'month':
                            this.dateFrom = fmt(new Date(now.getFullYear(), now.getMonth(), 1));
                            this.dateTo = fmt(now);
                            break;
                        case 'quarter':
                            const q = Math.floor(now.getMonth() / 3);
                            this.dateFrom = fmt(new Date(now.getFullYear(), q * 3, 1));
                            this.dateTo = fmt(now);
                            break;
                        case 'year':
                            this.dateFrom = fmt(new Date(now.getFullYear(), 0, 1));
                            this.dateTo = fmt(now);
                            break;
                    }
                    this.loadAll();
                },

                /* ── Tab switch ── */
                switchTab(t) {
                    this.tab = t;
                    this.$nextTick(() => this.renderChartsForTab(t));
                },

                /* ── Load all data ── */
                async loadAll() {
                    try {
                        const r = await fetch(
                            `${this.urls.report}?from=${this.dateFrom}&to=${this.dateTo}&granularity=${this.chartGranularity}`, {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            }
                        );
                        this.data = await r.json();
                        this.$nextTick(() => this.renderChartsForTab(this.tab));
                    } catch (e) {
                        console.error(e);
                    }
                },

                async loadZReport() {
                    if (!this.selectedShift) return;
                    try {
                        const r = await fetch(
                        `${this.urls.zreport}?shift_id=${this.selectedShift}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        this.zreport = await r.json();
                    } catch (e) {
                        console.error(e);
                    }
                },

                /* ── Render charts ── */
                renderChartsForTab(t) {
                    if (t === 'overview') {
                        this.renderOverviewChart();
                        this.renderPaymentChart();
                        this.renderCategoryChart();
                    }
                    if (t === 'sales') {
                        this.renderDailySalesChart();
                        this.renderWeekdayChart();
                    }
                    if (t === 'products') {
                        this.renderProductsChart();
                    }
                    if (t === 'inventory') {
                        this.renderStockStatusChart();
                        this.renderInvCategoryChart();
                    }
                    if (t === 'cashiers') {
                        this.renderCashiersChart();
                    }
                    if (t === 'loans') {
                        this.renderLoansChart();
                    }
                },

                destroyChart(id) {
                    if (this.charts[id]) {
                        this.charts[id].destroy();
                        delete this.charts[id];
                    }
                },

                apexDefaults() {
                    return {
                        chart: {
                            fontFamily: 'DM Mono, monospace',
                            toolbar: {
                                show: false
                            },
                            animations: {
                                enabled: true,
                                easing: 'easeinout',
                                speed: 600
                            }
                        },
                        colors: ['#2f6fe8', '#15803d', '#d97706', '#7c3aed', '#0891b2', '#dc2626'],
                        grid: {
                            borderColor: '#dde1ee',
                            strokeDashArray: 4
                        },
                        tooltip: {
                            theme: 'light'
                        },
                        xaxis: {
                            labels: {
                                style: {
                                    colors: '#848baa',
                                    fontFamily: 'DM Mono, monospace',
                                    fontSize: '11px'
                                }
                            },
                            axisBorder: {
                                show: false
                            }
                        },
                        yaxis: {
                            labels: {
                                style: {
                                    colors: '#848baa',
                                    fontFamily: 'DM Mono, monospace',
                                    fontSize: '11px'
                                }
                            }
                        },
                    };
                },

                renderOverviewChart() {
                    this.destroyChart('overview');
                    const el = document.getElementById('chart-overview');
                    if (!el) return;
                    const labels = (this.data.trend_labels || []);
                    const revenue = (this.data.trend_revenue || []);
                    const profit = (this.data.trend_profit || []);
                    this.charts['overview'] = new ApexCharts(el, {
                        ...this.apexDefaults(),
                        chart: {
                            ...this.apexDefaults().chart,
                            type: 'area',
                            height: 320
                        },
                        series: [{
                                name: 'Revenue',
                                data: revenue
                            },
                            {
                                name: 'Profit',
                                data: profit
                            },
                        ],
                        xaxis: {
                            ...this.apexDefaults().xaxis,
                            categories: labels
                        },
                        fill: {
                            type: 'gradient',
                            gradient: {
                                opacityFrom: .25,
                                opacityTo: .02
                            }
                        },
                        stroke: {
                            curve: 'smooth',
                            width: 2.5
                        },
                        dataLabels: {
                            enabled: false
                        },
                        legend: {
                            position: 'top',
                            fontFamily: 'Plus Jakarta Sans, sans-serif'
                        },
                    });
                    this.charts['overview'].render();
                },

                renderPaymentChart() {
                    this.destroyChart('payment');
                    const el = document.getElementById('chart-payment');
                    if (!el) return;
                    this.charts['payment'] = new ApexCharts(el, {
                        ...this.apexDefaults(),
                        chart: {
                            ...this.apexDefaults().chart,
                            type: 'donut',
                            height: 180
                        },
                        series: [this.data.cash_sales || 0, this.data.loan_sales || 0],
                        labels: ['Cash', 'Loan'],
                        colors: ['#2f6fe8', '#d97706'],
                        legend: {
                            show: false
                        },
                        dataLabels: {
                            enabled: true,
                            formatter: (v) => v.toFixed(1) + '%'
                        },
                        plotOptions: {
                            pie: {
                                donut: {
                                    size: '65%'
                                }
                            }
                        },
                    });
                    this.charts['payment'].render();
                },

                renderCategoryChart() {
                    this.destroyChart('category');
                    const el = document.getElementById('chart-category');
                    if (!el) return;
                    const cats = this.data.top_categories || [];
                    this.charts['category'] = new ApexCharts(el, {
                        ...this.apexDefaults(),
                        chart: {
                            ...this.apexDefaults().chart,
                            type: 'bar',
                            height: 220
                        },
                        series: [{
                            name: 'Revenue',
                            data: cats.map(c => c.revenue)
                        }],
                        xaxis: {
                            ...this.apexDefaults().xaxis,
                            categories: cats.map(c => c.name)
                        },
                        plotOptions: {
                            bar: {
                                borderRadius: 5,
                                horizontal: true
                            }
                        },
                        dataLabels: {
                            enabled: false
                        },
                    });
                    this.charts['category'].render();
                },

                renderDailySalesChart() {
                    this.destroyChart('daily-sales');
                    const el = document.getElementById('chart-daily-sales');
                    if (!el) return;
                    this.charts['daily-sales'] = new ApexCharts(el, {
                        ...this.apexDefaults(),
                        chart: {
                            ...this.apexDefaults().chart,
                            type: 'bar',
                            height: 320,
                            stacked: true
                        },
                        series: [{
                                name: 'Cash',
                                data: this.data.daily_cash || []
                            },
                            {
                                name: 'Loan',
                                data: this.data.daily_loan || []
                            },
                        ],
                        xaxis: {
                            ...this.apexDefaults().xaxis,
                            categories: this.data.daily_labels || []
                        },
                        colors: ['#2f6fe8', '#d97706'],
                        plotOptions: {
                            bar: {
                                borderRadius: 4,
                                columnWidth: '60%'
                            }
                        },
                        dataLabels: {
                            enabled: false
                        },
                        legend: {
                            position: 'top',
                            fontFamily: 'Plus Jakarta Sans, sans-serif'
                        },
                    });
                    this.charts['daily-sales'].render();
                },

                renderWeekdayChart() {
                    this.destroyChart('weekday');
                    const el = document.getElementById('chart-weekday');
                    if (!el) return;
                    const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                    this.charts['weekday'] = new ApexCharts(el, {
                        ...this.apexDefaults(),
                        chart: {
                            ...this.apexDefaults().chart,
                            type: 'radar',
                            height: 260
                        },
                        series: [{
                            name: 'Avg Sales',
                            data: this.data.weekday_avg || Array(7).fill(0)
                        }],
                        xaxis: {
                            categories: days
                        },
                        fill: {
                            opacity: .2
                        },
                        stroke: {
                            width: 2
                        },
                        markers: {
                            size: 4
                        },
                    });
                    this.charts['weekday'].render();
                },

                renderProductsChart() {
                    this.destroyChart('products');
                    const el = document.getElementById('chart-products');
                    if (!el) return;
                    const prods = (this.data.top_products || []).slice(0, 10);
                    this.charts['products'] = new ApexCharts(el, {
                        ...this.apexDefaults(),
                        chart: {
                            ...this.apexDefaults().chart,
                            type: 'bar',
                            height: 320
                        },
                        series: [{
                                name: 'Revenue',
                                data: prods.map(p => p.revenue)
                            },
                            {
                                name: 'Profit',
                                data: prods.map(p => p.profit)
                            },
                        ],
                        xaxis: {
                            ...this.apexDefaults().xaxis,
                            categories: prods.map(p => p.name.length > 14 ? p.name.slice(0,
                                14) + '…' : p.name)
                        },
                        plotOptions: {
                            bar: {
                                borderRadius: 4,
                                columnWidth: '55%'
                            }
                        },
                        dataLabels: {
                            enabled: false
                        },
                        legend: {
                            position: 'top',
                            fontFamily: 'Plus Jakarta Sans, sans-serif'
                        },
                    });
                    this.charts['products'].render();
                },

                renderStockStatusChart() {
                    this.destroyChart('stock-status');
                    const el = document.getElementById('chart-stock-status');
                    if (!el) return;
                    this.charts['stock-status'] = new ApexCharts(el, {
                        ...this.apexDefaults(),
                        chart: {
                            ...this.apexDefaults().chart,
                            type: 'donut',
                            height: 240
                        },
                        series: [this.data.stock_ok || 0, this.data.stock_low || 0, this.data
                            .stock_zero || 0
                        ],
                        labels: ['Healthy', 'Low Stock', 'Out of Stock'],
                        colors: ['#15803d', '#d97706', '#dc2626'],
                        legend: {
                            position: 'bottom',
                            fontFamily: 'Plus Jakarta Sans, sans-serif'
                        },
                        plotOptions: {
                            pie: {
                                donut: {
                                    size: '60%'
                                }
                            }
                        },
                    });
                    this.charts['stock-status'].render();
                },

                renderInvCategoryChart() {
                    this.destroyChart('inv-category');
                    const el = document.getElementById('chart-inv-category');
                    if (!el) return;
                    const cats = this.data.inv_by_category || [];
                    this.charts['inv-category'] = new ApexCharts(el, {
                        ...this.apexDefaults(),
                        chart: {
                            ...this.apexDefaults().chart,
                            type: 'bar',
                            height: 240
                        },
                        series: [{
                            name: 'Value (Cost)',
                            data: cats.map(c => c.value)
                        }],
                        xaxis: {
                            ...this.apexDefaults().xaxis,
                            categories: cats.map(c => c.name)
                        },
                        colors: ['#7c3aed'],
                        plotOptions: {
                            bar: {
                                borderRadius: 5,
                                horizontal: true
                            }
                        },
                        dataLabels: {
                            enabled: false
                        },
                    });
                    this.charts['inv-category'].render();
                },

                renderCashiersChart() {
                    this.destroyChart('cashiers');
                    const el = document.getElementById('chart-cashiers');
                    if (!el) return;
                    const cashiers = this.data.cashiers || [];
                    this.charts['cashiers'] = new ApexCharts(el, {
                        ...this.apexDefaults(),
                        chart: {
                            ...this.apexDefaults().chart,
                            type: 'bar',
                            height: 280
                        },
                        series: [{
                            name: 'Total Sales',
                            data: cashiers.map(c => c.total_sales)
                        }],
                        xaxis: {
                            ...this.apexDefaults().xaxis,
                            categories: cashiers.map(c => c.name)
                        },
                        plotOptions: {
                            bar: {
                                borderRadius: 6,
                                columnWidth: '50%'
                            }
                        },
                        dataLabels: {
                            enabled: false
                        },
                    });
                    this.charts['cashiers'].render();
                },

                renderLoansChart() {
                    this.destroyChart('loans');
                    const el = document.getElementById('chart-loans');
                    if (!el) return;
                    this.charts['loans'] = new ApexCharts(el, {
                        ...this.apexDefaults(),
                        chart: {
                            ...this.apexDefaults().chart,
                            type: 'line',
                            height: 280
                        },
                        series: [{
                                name: 'Issued',
                                data: this.data.loan_issued_series || []
                            },
                            {
                                name: 'Collected',
                                data: this.data.loan_collected_series || []
                            },
                        ],
                        xaxis: {
                            ...this.apexDefaults().xaxis,
                            categories: this.data.trend_labels || []
                        },
                        colors: ['#d97706', '#15803d'],
                        stroke: {
                            curve: 'smooth',
                            width: 2.5
                        },
                        markers: {
                            size: 4
                        },
                        dataLabels: {
                            enabled: false
                        },
                        legend: {
                            position: 'top',
                            fontFamily: 'Plus Jakarta Sans, sans-serif'
                        },
                    });
                    this.charts['loans'].render();
                },

                /* ── Heatmap color ── */
                heatColor(val, max) {
                    const pct = max > 0 ? val / max : 0;
                    if (pct === 0) return '#eceef5';
                    const r = Math.round(47 + (220 - 47) * (1 - pct));
                    const g = Math.round(111 + (38 - 111) * (1 - pct));
                    const b = Math.round(232 + (38 - 232) * (1 - pct));
                    return `rgb(${r},${g},${b})`;
                },

                /* ── Export / Print ── */
                printReport() {
                    window.print();
                },
                printZReport() {
                    window.print();
                },
                exportCsv() {
                    window.location.href = this.urls.export+'?from=' + this.dateFrom + '&to=' + this
                        .dateTo + '&type=csv';
                },
                exportPdf() {
                    window.location.href = this.urls.export+'?from=' + this.dateFrom + '&to=' + this
                        .dateTo + '&type=pdf';
                },
                exportZReportCsv() {
                    window.location.href = this.urls.export+'?shift_id=' + this.selectedShift +
                        '&type=zreport';
                },

                /* ── Helpers ── */
                initials(name) {
                    if (!name) return '?';
                    return name.trim().split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2);
                },
                fmt(n) {
                    return Number(n || 0).toLocaleString('en-US', {
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 0
                    });
                },
            }));
        });
    </script>
@endpush
