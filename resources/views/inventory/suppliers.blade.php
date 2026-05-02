@extends('layouts.app')

@push('styles')
    <link
        href="https://fonts.googleapis.com/css2?family=Fraunces:ital,wght@0,500;0,700;1,400&family=Karla:wght@300;400;500;600;700&family=Roboto+Mono:wght@400;500&display=swap"
        rel="stylesheet">
    <style>
        /* ══════════════════════════════════════
       TOKENS
    ══════════════════════════════════════ */
        :root {
            --bg: #eef0f6;
            --surface: #ffffff;
            --s2: #f5f6fb;
            --s3: #eceef5;
            --border: #dde1ee;
            --border2: #c4cad e;
            --ink: #171a2b;
            --ink2: #404468;
            --ink3: #808aa8;
            --ink4: #bcc3d8;
            --navy: #1e2d4f;
            --blue: #2658e8;
            --blue2: #1d48cc;
            --bdim: rgba(38, 88, 232, .08);
            --bmid: rgba(38, 88, 232, .16);
            --green: #15803d;
            --gdim: rgba(21, 128, 61, .09);
            --red: #dc2626;
            --rdim: rgba(220, 38, 38, .08);
            --amber: #d97706;
            --adim: rgba(217, 119, 6, .09);
            --teal: #0891b2;
            --tdim: rgba(8, 145, 178, .09);
            --mono: 'Roboto Mono', monospace;
            --body: 'Karla', sans-serif;
            --display: 'Fraunces', serif;
            --r: 10px;
            --rsm: 6px;
            --rlg: 16px;
            --sh: 0 1px 3px rgba(0, 0, 0, .05), 0 1px 2px rgba(0, 0, 0, .03);
            --shmd: 0 4px 18px rgba(0, 0, 0, .08), 0 2px 6px rgba(0, 0, 0, .04);
            --shlg: 0 16px 48px rgba(0, 0, 0, .12), 0 4px 14px rgba(0, 0, 0, .06);
        }

        /* ══════════════════════════════════════
       BASE
    ══════════════════════════════════════ */
        .sp * {
            box-sizing: border-box;
        }

        .sp {
            font-family: var(--body);
            background: var(--bg);
            min-height: 100vh;
            color: var(--ink);
        }

        [x-cloak] {
            display: none !important;
        }

        /* ══════════════════════════════════════
       TOPBAR
    ══════════════════════════════════════ */
        .sp-top {
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

        .sp-title {
            font-family: var(--display);
            font-size: 22px;
            color: var(--ink);
            font-weight: 500;
            letter-spacing: -.3px;
        }

        .sp-title em {
            color: var(--blue);
            font-style: italic;
        }

        .top-r {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* ══════════════════════════════════════
       BUTTONS
    ══════════════════════════════════════ */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            border-radius: var(--rsm);
            font-family: var(--body);
            font-size: 12.5px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all .16s;
            white-space: nowrap;
        }

        .btn-ghost {
            background: var(--s2);
            border: 1px solid var(--border);
            color: var(--ink2);
        }

        .btn-ghost:hover {
            background: var(--s3);
            color: var(--ink);
        }

        .btn-primary {
            background: var(--blue);
            color: #fff;
            box-shadow: 0 2px 8px rgba(38, 88, 232, .25);
        }

        .btn-primary:hover {
            background: var(--blue2);
            transform: translateY(-1px);
        }

        .btn-danger {
            background: var(--rdim);
            border: 1px solid rgba(220, 38, 38, .2);
            color: var(--red);
        }

        .btn-danger:hover {
            background: var(--red);
            color: #fff;
        }

        .btn-teal {
            background: var(--tdim);
            border: 1px solid rgba(8, 145, 178, .2);
            color: var(--teal);
        }

        .btn-teal:hover {
            background: var(--teal);
            color: #fff;
        }

        .btn-sm {
            padding: 5px 10px;
            font-size: 11.5px;
        }

        .btn:active {
            transform: scale(.97);
        }

        .btn:disabled {
            opacity: .4;
            cursor: not-allowed;
            transform: none !important;
        }

        /* ══════════════════════════════════════
       STAT STRIP
    ══════════════════════════════════════ */
        .stat-strip {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            padding: 1.2rem 1.75rem .75rem;
        }

        .stat-tile {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--r);
            padding: 1rem 1.2rem;
            position: relative;
            overflow: hidden;
            transition: all .2s;
            cursor: default;
        }

        .stat-tile:hover {
            box-shadow: var(--shmd);
            transform: translateY(-2px);
        }

        .stat-tile::before {
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

        .stat-tile:hover::before {
            transform: scaleX(1);
        }

        .st-label {
            font-size: 10px;
            font-weight: 700;
            color: var(--ink3);
            text-transform: uppercase;
            letter-spacing: .1em;
            margin-bottom: 7px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .st-val {
            font-family: var(--mono);
            font-size: 22px;
            font-weight: 500;
            color: var(--ink);
            line-height: 1;
            letter-spacing: -.5px;
        }

        .st-sub {
            font-size: 11px;
            color: var(--ink3);
            margin-top: 5px;
        }

        /* ══════════════════════════════════════
       TABS
    ══════════════════════════════════════ */
        .sp-tabs {
            display: flex;
            gap: 2px;
            padding: .75rem 1.75rem 0;
            background: var(--surface);
            border-bottom: 1px solid var(--border);
        }

        .sp-tab {
            display: flex;
            align-items: center;
            gap: 7px;
            padding: 9px 18px;
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

        .sp-tab:hover {
            color: var(--ink);
            background: var(--bdim);
        }

        .sp-tab.active {
            color: var(--blue);
            border-bottom-color: var(--blue);
            background: var(--surface);
        }

        /* ══════════════════════════════════════
       PANEL
    ══════════════════════════════════════ */
        .sp-panel {
            display: none;
            padding: 1.1rem 1.75rem 2rem;
            animation: tabIn .2s ease;
        }

        .sp-panel.active {
            display: block;
        }

        @keyframes tabIn {
            from {
                opacity: 0;
                transform: translateY(5px);
            }

            to {
                opacity: 1;
                transform: none;
            }
        }

        /* ══════════════════════════════════════
       TOOLBAR
    ══════════════════════════════════════ */
        .sp-toolbar {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 1rem;
        }

        .search-box {
            position: relative;
            flex: 1;
            min-width: 200px;
            max-width: 320px;
        }

        .search-box i {
            position: absolute;
            left: 11px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--ink3);
            font-size: 13px;
            pointer-events: none;
        }

        .sp-search {
            width: 100%;
            padding: 9px 14px 9px 34px;
            background: var(--surface);
            border: 1.5px solid var(--border);
            border-radius: var(--rsm);
            font-family: var(--body);
            font-size: 13px;
            color: var(--ink);
            outline: none;
            transition: border .15s, box-shadow .15s;
        }

        .sp-search:focus {
            border-color: var(--blue);
            box-shadow: 0 0 0 3px var(--bdim);
            background: #fff;
        }

        .sp-search::placeholder {
            color: var(--ink4);
        }

        .f-sel {
            padding: 9px 12px;
            background: var(--surface);
            border: 1.5px solid var(--border);
            border-radius: var(--rsm);
            font-family: var(--body);
            font-size: 12.5px;
            color: var(--ink2);
            outline: none;
            cursor: pointer;
            transition: border .15s;
        }

        .f-sel:focus {
            border-color: var(--blue);
        }

        /* ══════════════════════════════════════
       MAIN LAYOUT (table + panel)
    ══════════════════════════════════════ */
        .sp-main {
            display: grid;
            grid-template-columns: 1fr;
            gap: 0;
            transition: grid-template-columns .25s;
            align-items: start;
        }

        .sp-main.panel-open {
            grid-template-columns: 1fr 400px;
            gap: 1.25rem;
        }

        /* ══════════════════════════════════════
       TABLE
    ══════════════════════════════════════ */
        .table-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--r);
            overflow: hidden;
            box-shadow: var(--sh);
        }

        .sp-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .sp-table thead {
            background: var(--s2);
            border-bottom: 1.5px solid var(--border);
        }

        .sp-table th {
            padding: 10px 14px;
            text-align: left;
            font-size: 10px;
            font-weight: 700;
            color: var(--ink3);
            text-transform: uppercase;
            letter-spacing: .08em;
            white-space: nowrap;
            cursor: pointer;
            user-select: none;
            transition: color .15s;
        }

        .sp-table th:hover {
            color: var(--blue);
        }

        .sp-table th i {
            margin-left: 3px;
            opacity: .45;
        }

        .sp-table td {
            padding: 11px 14px;
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
        }

        .sp-table tbody tr:last-child td {
            border-bottom: none;
        }

        .sp-table tbody tr {
            transition: background .12s;
            cursor: pointer;
        }

        .sp-table tbody tr:hover {
            background: var(--bdim);
        }

        .sp-table tbody tr.selected {
            background: var(--bdim);
            border-left: 3px solid var(--blue);
        }

        /* cells */
        .sup-cell {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sup-av {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 700;
            color: #fff;
            flex-shrink: 0;
            font-family: var(--body);
        }

        .sup-name {
            font-weight: 600;
            font-size: 13px;
            color: var(--ink);
        }

        .sup-contact {
            font-size: 11px;
            color: var(--ink3);
            margin-top: 2px;
        }

        .cell-mono {
            font-family: var(--mono);
            font-size: 12px;
        }

        .cell-right {
            text-align: right;
        }

        /* pills */
        .pill {
            display: inline-block;
            padding: 3px 9px;
            border-radius: 99px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .04em;
        }

        .pill-green {
            background: var(--gdim);
            color: var(--green);
            border: 1px solid rgba(21, 128, 61, .2);
        }

        .pill-red {
            background: var(--rdim);
            color: var(--red);
            border: 1px solid rgba(220, 38, 38, .2);
        }

        .pill-amber {
            background: var(--adim);
            color: var(--amber);
            border: 1px solid rgba(217, 119, 6, .2);
        }

        .pill-blue {
            background: var(--bdim);
            color: var(--blue);
            border: 1px solid var(--bmid);
        }

        .pill-teal {
            background: var(--tdim);
            color: var(--teal);
            border: 1px solid rgba(8, 145, 178, .2);
        }

        .pill-gray {
            background: var(--s3);
            color: var(--ink3);
            border: 1px solid var(--border);
        }

        .pill-navy {
            background: rgba(30, 45, 79, .1);
            color: var(--navy);
            border: 1px solid rgba(30, 45, 79, .2);
        }

        /* row actions */
        .row-acts {
            display: flex;
            gap: 4px;
            opacity: 0;
            transition: opacity .15s;
        }

        .sp-table tbody tr:hover .row-acts {
            opacity: 1;
        }

        /* ══════════════════════════════════════
       PAGINATION
    ══════════════════════════════════════ */
        .pag-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 11px 16px;
            border-top: 1px solid var(--border);
            background: var(--s2);
        }

        .pag-info {
            font-size: 12px;
            color: var(--ink3);
        }

        .pag-btns {
            display: flex;
            gap: 4px;
        }

        .pag-btn {
            width: 30px;
            height: 30px;
            border-radius: var(--rsm);
            border: 1px solid var(--border);
            background: var(--surface);
            cursor: pointer;
            font-family: var(--mono);
            font-size: 11px;
            color: var(--ink2);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all .12s;
        }

        .pag-btn:hover {
            background: var(--bdim);
            border-color: var(--blue);
            color: var(--blue);
        }

        .pag-btn.active {
            background: var(--blue);
            color: #fff;
            border-color: var(--blue);
        }

        .pag-btn:disabled {
            opacity: .3;
            cursor: not-allowed;
        }

        /* ══════════════════════════════════════
       EMPTY / LOADING
    ══════════════════════════════════════ */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--ink3);
        }

        .empty-state i {
            font-size: 36px;
            margin-bottom: 12px;
            display: block;
            color: var(--ink4);
        }

        .empty-state p {
            font-size: 13px;
            line-height: 1.7;
        }

        .loading-row {
            text-align: center;
            padding: 3rem;
            color: var(--ink3);
            font-size: 13px;
        }

        /* ══════════════════════════════════════
       DETAIL PANEL (shared)
    ══════════════════════════════════════ */
        .detail-panel {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--r);
            box-shadow: var(--sh);
            display: flex;
            flex-direction: column;
            max-height: calc(100vh - 200px);
            position: sticky;
            top: 72px;
            overflow: hidden;
            animation: panelIn .2s cubic-bezier(.2, .8, .36, 1);
        }

        @keyframes panelIn {
            from {
                opacity: 0;
                transform: translateX(14px);
            }

            to {
                opacity: 1;
                transform: none;
            }
        }

        .dp-head {
            padding: .9rem 1.25rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
        }

        .dp-head-label {
            font-size: 11px;
            font-weight: 700;
            color: var(--ink3);
            text-transform: uppercase;
            letter-spacing: .09em;
        }

        .dp-close {
            background: none;
            border: none;
            cursor: pointer;
            color: var(--ink3);
            font-size: 16px;
            transition: color .15s;
        }

        .dp-close:hover {
            color: var(--ink);
        }

        .dp-body {
            flex: 1;
            overflow-y: auto;
        }

        .dp-body::-webkit-scrollbar {
            width: 4px;
        }

        .dp-body::-webkit-scrollbar-thumb {
            background: var(--border);
            border-radius: 2px;
        }

        .dp-foot {
            padding: .9rem 1.25rem;
            border-top: 1px solid var(--border);
            display: flex;
            gap: 7px;
            flex-shrink: 0;
        }

        /* supplier hero */
        .sup-hero {
            padding: 1.25rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .hero-av {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            font-weight: 700;
            color: #fff;
            margin-bottom: 10px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, .15);
        }

        .hero-name {
            font-family: var(--display);
            font-size: 18px;
            color: var(--ink);
            font-weight: 500;
            margin-bottom: 4px;
        }

        .hero-meta {
            font-size: 12px;
            color: var(--ink3);
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            justify-content: center;
        }

        /* info grid */
        .dp-section {
            padding: .85rem 1.25rem;
            border-bottom: 1px solid var(--border);
        }

        .dp-section:last-child {
            border-bottom: none;
        }

        .dp-section-title {
            font-size: 10px;
            font-weight: 700;
            color: var(--ink3);
            text-transform: uppercase;
            letter-spacing: .1em;
            margin-bottom: .6rem;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .dp-section-title i {
            color: var(--blue);
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 7px;
        }

        .info-field {
            background: var(--s2);
            border: 1px solid var(--border);
            border-radius: var(--rsm);
            padding: 7px 10px;
        }

        .info-field.full {
            grid-column: span 2;
        }

        .if-label {
            font-size: 10px;
            color: var(--ink3);
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-bottom: 3px;
        }

        .if-val {
            font-size: 12.5px;
            font-weight: 500;
            color: var(--ink);
        }

        .if-val.mono {
            font-family: var(--mono);
            font-size: 12px;
        }

        /* mini purchase list */
        .mini-po {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 10px;
            background: var(--s2);
            border: 1px solid var(--border);
            border-radius: var(--rsm);
            margin-bottom: 5px;
            transition: background .12s;
        }

        .mini-po:hover {
            background: var(--bdim);
        }

        .mpo-id {
            font-family: var(--mono);
            font-size: 11px;
            color: var(--blue);
        }

        .mpo-date {
            font-size: 11px;
            color: var(--ink3);
            margin-top: 2px;
        }

        .mpo-amt {
            font-family: var(--mono);
            font-size: 13px;
            font-weight: 600;
            color: var(--ink);
        }

        /* supplier stats bar */
        .sup-kpi-strip {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
            padding: .85rem 1.25rem;
            border-bottom: 1px solid var(--border);
        }

        .sk-item {
            background: var(--s2);
            border: 1px solid var(--border);
            border-radius: var(--rsm);
            padding: 8px 10px;
        }

        .sk-label {
            font-size: 10px;
            color: var(--ink3);
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-bottom: 4px;
        }

        .sk-val {
            font-family: var(--mono);
            font-size: 15px;
            font-weight: 500;
            color: var(--ink);
        }

        /* ══════════════════════════════════════
       PO TABLE specifics
    ══════════════════════════════════════ */
        .po-progress {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .po-prog-bar {
            flex: 1;
            max-width: 80px;
            height: 5px;
            background: var(--s3);
            border-radius: 3px;
            overflow: hidden;
        }

        .po-prog-fill {
            height: 100%;
            border-radius: 3px;
            background: var(--green);
        }

        .po-prog-val {
            font-family: var(--mono);
            font-size: 10px;
            color: var(--ink3);
        }

        /* receive stock panel */
        .receive-section {
            padding: .85rem 1.25rem;
            border-bottom: 1px solid var(--border);
        }

        .receive-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 10px;
            background: var(--s2);
            border: 1.5px solid var(--border);
            border-radius: var(--rsm);
            margin-bottom: 6px;
        }

        .receive-item.fully-received {
            opacity: .5;
        }

        .ri-info {
            flex: 1;
        }

        .ri-name {
            font-size: 12.5px;
            font-weight: 500;
            color: var(--ink);
        }

        .ri-detail {
            font-size: 11px;
            color: var(--ink3);
            font-family: var(--mono);
            margin-top: 2px;
        }

        .ri-qty-input {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 4px;
            flex-shrink: 0;
        }

        .ri-ordered {
            font-size: 10px;
            color: var(--ink3);
        }

        .ri-input {
            width: 64px;
            padding: 5px 7px;
            border: 1.5px solid var(--border);
            border-radius: var(--rsm);
            font-family: var(--mono);
            font-size: 13px;
            text-align: right;
            outline: none;
            background: var(--surface);
            color: var(--ink);
        }

        .ri-input:focus {
            border-color: var(--teal);
            box-shadow: 0 0 0 2px var(--tdim);
        }

        /* ══════════════════════════════════════
       MODALS
    ══════════════════════════════════════ */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(23, 26, 43, .45);
            backdrop-filter: blur(5px);
            z-index: 200;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            animation: fadeIn .15s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .modal-card {
            background: var(--surface);
            border-radius: var(--rlg);
            box-shadow: var(--shlg);
            width: 100%;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
            animation: slideUp .18s cubic-bezier(.2, .8, .36, 1);
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(16px);
            }

            to {
                opacity: 1;
                transform: none;
            }
        }

        .modal-sm {
            max-width: 480px;
        }

        .modal-md {
            max-width: 620px;
        }

        .modal-head {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
        }

        .modal-title {
            font-family: var(--display);
            font-size: 20px;
            color: var(--ink);
            font-weight: 500;
        }

        .modal-close {
            background: none;
            border: none;
            cursor: pointer;
            color: var(--ink3);
            font-size: 18px;
        }

        .modal-body {
            padding: 1.25rem 1.5rem;
            overflow-y: auto;
            flex: 1;
        }

        .modal-foot {
            padding: 1rem 1.5rem;
            border-top: 1px solid var(--border);
            display: flex;
            gap: 8px;
            justify-content: flex-end;
            flex-shrink: 0;
        }

        /* form */
        .form-grid {
            display: grid;
            gap: .9rem;
        }

        .form-2 {
            grid-template-columns: 1fr 1fr;
        }

        .field-label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            color: var(--ink2);
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-bottom: 5px;
        }

        .field-req {
            color: var(--red);
        }

        .field-input {
            width: 100%;
            padding: 9px 12px;
            background: var(--s2);
            border: 1.5px solid var(--border);
            border-radius: var(--rsm);
            font-family: var(--body);
            font-size: 13px;
            color: var(--ink);
            outline: none;
            transition: border .15s, box-shadow .15s;
        }

        .field-input:focus {
            border-color: var(--blue);
            box-shadow: 0 0 0 3px var(--bdim);
            background: #fff;
        }

        .field-input::placeholder {
            color: var(--ink4);
        }

        textarea.field-input {
            resize: vertical;
            min-height: 68px;
        }

        .field-hint {
            font-size: 11px;
            color: var(--ink3);
            margin-top: 3px;
        }

        .form-err {
            padding: 9px 12px;
            background: var(--rdim);
            border: 1px solid rgba(220, 38, 38, .2);
            border-radius: var(--rsm);
            font-size: 12px;
            color: var(--red);
            margin-top: .75rem;
        }

        .form-section-title {
            font-size: 11px;
            font-weight: 700;
            color: var(--blue);
            text-transform: uppercase;
            letter-spacing: .08em;
            margin-bottom: .7rem;
            padding-bottom: 6px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 6px;
        }
    </style>
@endpush

@section('content')
    <div class="sp" x-data="suppliersPage()" x-init="init()">

        {{-- ════ TOPBAR ════ --}}
        <div class="sp-top">
            <div class="sp-title">Afghan <em>POS</em> — Suppliers & Purchases</div>
            <div class="top-r">
                <button class="btn btn-ghost" x-show="activeTab==='suppliers'" @click="openSupplierModal(null)">
                    <i class="fas fa-plus"></i> Add Supplier
                </button>
            </div>
        </div>

        {{-- ════ STATS ════ --}}
        <div class="stat-strip">
            <div class="stat-tile" style="--ac:var(--blue)">
                <div class="st-label">Total Suppliers <span style="color:var(--blue)"><i class="fas fa-truck"></i></span>
                </div>
                <div class="st-val">{{ $stats['total'] ?? 0 }}</div>
                <div class="st-sub">{{ $stats['active'] ?? 0 }} active</div>
            </div>
            <div class="stat-tile" style="--ac:var(--amber)">
                <div class="st-label">Open POs <span style="color:var(--amber)"><i class="fas fa-file-invoice"></i></span>
                </div>
                <div class="st-val" style="color:var(--amber)">{{ $stats['open_pos'] ?? 0 }}</div>
                <div class="st-sub">awaiting delivery</div>
            </div>
            <div class="stat-tile" style="--ac:var(--red)">
                <div class="st-label">Unpaid Balance <span style="color:var(--red)"><i class="fas fa-coins"></i></span>
                </div>
                <div class="st-val" style="font-size:18px;color:var(--red)">Af {{ number_format($stats['unpaid'] ?? 0) }}
                </div>
                <div class="st-sub">owed to suppliers</div>
            </div>
            <div class="stat-tile" style="--ac:var(--green)">
                <div class="st-label">Total Purchased <span style="color:var(--green)"><i
                            class="fas fa-chart-line"></i></span></div>
                <div class="st-val" style="font-size:18px">Af {{ number_format($stats['total_purchased'] ?? 0) }}</div>
                <div class="st-sub">lifetime purchase value</div>
            </div>
        </div>

        {{-- ════ TABS ════ --}}
        <div class="sp-tabs">
            <button type="button" class="sp-tab" :class="activeTab === 'suppliers' ? 'active' : ''"
                @click="switchTab('suppliers')">
                <i class="fas fa-truck"></i> Suppliers
            </button>
            <button type="button" class="sp-tab" :class="activeTab === 'purchases' ? 'active' : ''"
                @click="switchTab('purchases')">
                <i class="fas fa-file-invoice"></i> Purchase Orders
            </button>
        </div>

        {{-- ══════════════════════════════════════════
     TAB: SUPPLIERS
══════════════════════════════════════════ --}}
        <div class="sp-panel" :class="activeTab === 'suppliers' ? 'active' : ''">

            <div class="sp-toolbar">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input class="sp-search" type="text" x-model="supSearch" @input.debounce.350ms="loadSuppliers()"
                        placeholder="Name, phone, city…">
                </div>
                <select class="f-sel" x-model="supFilterStatus" @change="loadSuppliers()">
                    <option value="">All Suppliers</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
                <select class="f-sel" x-model="supFilterCity" @change="loadSuppliers()">
                    <option value="">All Cities</option>
                    @foreach ($cities ?? [] as $city)
                        <option value="{{ $city }}">{{ $city }}</option>
                    @endforeach
                </select>
                <button type="button" class="btn btn-primary" style="margin-left:auto" @click="openSupplierModal(null)">
                    <i class="fas fa-plus"></i> Add Supplier
                </button>
            </div>

            <div class="sp-main" :class="selectedSupplier ? 'panel-open' : ''">

                {{-- TABLE --}}
                <div class="table-card">
                    <div class="loading-row" x-show="supLoading"><i class="fas fa-spinner fa-spin"
                            style="font-size:18px"></i></div>
                    <div x-show="!supLoading">
                        <div class="empty-state" x-show="suppliers.length===0">
                            <i class="fas fa-truck-slash"></i>
                            <p>No suppliers found.<br>Add your first supplier to get started.</p>
                        </div>
                        <table class="sp-table" x-show="suppliers.length>0">
                            <thead>
                                <tr>
                                    <th @click="supSort('name')">Supplier <i class="fas fa-sort"></i></th>
                                    <th>Contact Person</th>
                                    <th>Phone</th>
                                    <th>City</th>
                                    <th class="cell-right" @click="supSort('total_purchases')">Total Purchased <i
                                            class="fas fa-sort"></i></th>
                                    <th class="cell-right">Open POs</th>
                                    <th>Payment Terms</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="s in suppliers" :key="s.id">
                                    <tr :class="selectedSupplier?.id === s.id ? 'selected' : ''" @click="openSupplierDetail(s)">
                                        <td>
                                            <div class="sup-cell">
                                                <div class="sup-av" :style="`background:${avatarColor(s.name)}`"
                                                    x-text="initials(s.name)"></div>
                                                <div>
                                                    <div class="sup-name" x-text="s.name"></div>
                                                    <div class="sup-contact" x-text="s.email || '—'"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="font-size:12.5px;color:var(--ink2)" x-text="s.contact_person||'—'">
                                        </td>
                                        <td class="cell-mono" x-text="s.phone"></td>
                                        <td style="font-size:12.5px;color:var(--ink2)" x-text="s.city||'—'"></td>
                                        <td class="cell-right">
                                            <span class="cell-mono" style="font-weight:600"
                                                x-text="'Af ' + fmt(s.total_purchases||0)"></span>
                                        </td>
                                        <td class="cell-right">
                                            <span class="pill" :class="s.open_pos > 0 ? 'pill-amber' : 'pill-gray'"
                                                x-text="s.open_pos||0"></span>
                                        </td>
                                        <td style="font-size:12px;color:var(--ink3)" x-text="s.payment_terms||'—'"></td>
                                        <td>
                                            <span class="pill" :class="s.is_active ? 'pill-green' : 'pill-gray'"
                                                x-text="s.is_active?'Active':'Inactive'"></span>
                                        </td>
                                        <td @click.stop>
                                            <div class="row-acts">
                                                <button type="button" class="btn btn-ghost btn-sm"
                                                    @click="openSupplierModal(s)">
                                                    <i class="fas fa-pen"></i>
                                                </button>
                                                <button type="button" class="btn btn-danger btn-sm"
                                                    @click="toggleSupplier(s)">
                                                    <i class="fas fa-power-off"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                        <div class="pag-row" x-show="supPagination.last_page>1">
                            <div class="pag-info">Showing <span x-text="supPagination.from"></span>–<span
                                    x-text="supPagination.to"></span> of <span x-text="supPagination.total"></span></div>
                            <div class="pag-btns">
                                <button class="pag-btn" @click="supGoPage(supPagination.current_page-1)"
                                    :disabled="supPagination.current_page === 1"><i class="fas fa-chevron-left"></i></button>
                                <template x-for="p in supPagination.last_page" :key="p">
                                    <button class="pag-btn" :class="p === supPagination.current_page ? 'active' : ''"
                                        @click="supGoPage(p)" x-text="p"></button>
                                </template>
                                <button class="pag-btn" @click="supGoPage(supPagination.current_page+1)"
                                    :disabled="supPagination.current_page === supPagination.last_page"><i
                                        class="fas fa-chevron-right"></i></button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SUPPLIER DETAIL PANEL --}}
                <div class="detail-panel" x-show="selectedSupplier" x-cloak>
                    <div class="dp-head">
                        <span class="dp-head-label">Supplier Detail</span>
                        <button class="dp-close" @click="selectedSupplier=null"><i class="fas fa-times"></i></button>
                    </div>
                    <div class="dp-body">

                        {{-- Hero --}}
                        <div class="sup-hero">
                            <div class="hero-av" :style="`background:${avatarColor(selectedSupplier?.name)}`"
                                x-text="initials(selectedSupplier?.name)"></div>
                            <div class="hero-name" x-text="selectedSupplier?.name"></div>
                            <div class="hero-meta">
                                <span x-show="selectedSupplier?.city"><i class="fas fa-location-dot"></i> <span
                                        x-text="selectedSupplier?.city"></span></span>
                                <span class="pill" :class="selectedSupplier?.is_active ? 'pill-green' : 'pill-gray'"
                                    x-text="selectedSupplier?.is_active?'Active':'Inactive'"></span>
                            </div>
                        </div>

                        {{-- KPI strip --}}
                        <div class="sup-kpi-strip">
                            <div class="sk-item">
                                <div class="sk-label">Total Purchased</div>
                                <div class="sk-val" style="color:var(--blue)"
                                    x-text="'Af ' + fmt(selectedSupplier?.total_purchases||0)"></div>
                            </div>
                            <div class="sk-item">
                                <div class="sk-label">Open POs</div>
                                <div class="sk-val" style="color:var(--amber)" x-text="selectedSupplier?.open_pos||0">
                                </div>
                            </div>
                            <div class="sk-item">
                                <div class="sk-label">Unpaid</div>
                                <div class="sk-val" style="color:var(--red)"
                                    x-text="'Af ' + fmt(selectedSupplier?.unpaid||0)"></div>
                            </div>
                        </div>

                        {{-- Contact info --}}
                        <div class="dp-section">
                            <div class="dp-section-title"><i class="fas fa-address-card"></i> Contact Info</div>
                            <div class="info-grid">
                                <div class="info-field">
                                    <div class="if-label">Contact Person</div>
                                    <div class="if-val" x-text="selectedSupplier?.contact_person||'—'"></div>
                                </div>
                                <div class="info-field">
                                    <div class="if-label">Phone</div>
                                    <div class="if-val mono" x-text="selectedSupplier?.phone"></div>
                                </div>
                                <div class="info-field" x-show="selectedSupplier?.phone_secondary">
                                    <div class="if-label">Phone 2</div>
                                    <div class="if-val mono" x-text="selectedSupplier?.phone_secondary"></div>
                                </div>
                                <div class="info-field" x-show="selectedSupplier?.email">
                                    <div class="if-label">Email</div>
                                    <div class="if-val" x-text="selectedSupplier?.email"></div>
                                </div>
                                <div class="info-field full" x-show="selectedSupplier?.address">
                                    <div class="if-label">Address</div>
                                    <div class="if-val" x-text="selectedSupplier?.address"></div>
                                </div>
                                <div class="info-field">
                                    <div class="if-label">Payment Terms</div>
                                    <div class="if-val" x-text="selectedSupplier?.payment_terms||'—'"></div>
                                </div>
                            </div>
                        </div>

                        {{-- Notes --}}
                        <div class="dp-section" x-show="selectedSupplier?.notes">
                            <div class="dp-section-title"><i class="fas fa-pen"></i> Notes</div>
                            <div style="font-size:12.5px;color:var(--ink2);line-height:1.6"
                                x-text="selectedSupplier?.notes"></div>
                        </div>

                        {{-- Purchase history --}}
                        <div class="dp-section">
                            <div class="dp-section-title"><i class="fas fa-clock-rotate-left"></i> Recent Purchase Orders
                            </div>
                            <div x-show="supDetailLoading"
                                style="text-align:center;padding:1rem;color:var(--ink3);font-size:12px">
                                <i class="fas fa-spinner fa-spin"></i>
                            </div>
                            <div x-show="!supDetailLoading">
                                <div x-show="supplierPOs.length===0"
                                    style="text-align:center;padding:1rem;color:var(--ink4);font-size:12px">
                                    No purchase orders yet.
                                </div>
                                <template x-for="po in supplierPOs" :key="po.id">
                                    <div class="mini-po">
                                        <div>
                                            <div class="mpo-id" x-text="po.local_id"></div>
                                            <div class="mpo-date" x-text="po.purchase_date"></div>
                                        </div>
                                        <div style="text-align:right">
                                            <div class="mpo-amt" x-text="'Af ' + fmt(po.total_cost)"></div>
                                            <span class="pill"
                                                :class="{
                                                    'pill-amber': po.status==='ordered',
                                                    'pill-teal': po.status==='partial',
                                                    'pill-green': po.status==='received',
                                                    'pill-gray': po.status==='cancelled',
                                                }"
                                                x-text="po.status"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                    <div class="dp-foot">
                        <button type="button" class="btn btn-ghost" style="flex:1"
                            @click="openSupplierModal(selectedSupplier)">
                            <i class="fas fa-pen"></i> Edit
                        </button>
                        <button type="button" class="btn btn-danger" @click="toggleSupplier(selectedSupplier)">
                            <i class="fas fa-power-off"></i>
                            <span x-text="selectedSupplier?.is_active?'Deactivate':'Activate'"></span>
                        </button>
                    </div>
                </div>

            </div>{{-- /sp-main suppliers --}}
        </div>

        {{-- ══════════════════════════════════════════
     TAB: PURCHASE ORDERS
══════════════════════════════════════════ --}}
        <div class="sp-panel" :class="activeTab === 'purchases' ? 'active' : ''">

            <div class="sp-toolbar">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input class="sp-search" type="text" x-model="poSearch" @input.debounce.350ms="loadPOs()"
                        placeholder="PO ID, supplier, reference…">
                </div>
                <select class="f-sel" x-model="poFilterStatus" @change="loadPOs()">
                    <option value="">All Statuses</option>
                    <option value="ordered">Ordered</option>
                    <option value="partial">Partial</option>
                    <option value="received">Received</option>
                    <option value="cancelled">Cancelled</option>
                </select>
                <select class="f-sel" x-model="poFilterPayment" @change="loadPOs()">
                    <option value="">All Payments</option>
                    <option value="unpaid">Unpaid</option>
                    <option value="partial">Partial</option>
                    <option value="paid">Paid</option>
                </select>
            </div>

            <div class="sp-main" :class="selectedPO ? 'panel-open' : ''">

                {{-- PO TABLE --}}
                <div class="table-card">
                    <div class="loading-row" x-show="poLoading"><i class="fas fa-spinner fa-spin"
                            style="font-size:18px"></i></div>
                    <div x-show="!poLoading">
                        <div class="empty-state" x-show="purchaseOrders.length===0">
                            <i class="fas fa-file-circle-xmark"></i>
                            <p>No purchase orders found.</p>
                        </div>
                        <table class="sp-table" x-show="purchaseOrders.length>0">
                            <thead>
                                <tr>
                                    <th>PO ID</th>
                                    <th>Supplier</th>
                                    <th>Date</th>
                                    <th>Delivery</th>
                                    <th class="cell-right">Total Cost</th>
                                    <th class="cell-right">Paid</th>
                                    <th>Received</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="po in purchaseOrders" :key="po.id">
                                    <tr :class="selectedPO?.id === po.id ? 'selected' : ''" @click="openPODetail(po)">
                                        <td><span class="cell-mono" style="color:var(--blue);font-weight:500"
                                                x-text="po.local_id"></span></td>
                                        <td>
                                            <div style="font-weight:600;font-size:12.5px" x-text="po.supplier"></div>
                                            <div style="font-size:11px;color:var(--ink3)" x-show="po.reference_number"
                                                x-text="'Ref: ' + po.reference_number"></div>
                                        </td>
                                        <td class="cell-mono" style="font-size:11.5px" x-text="po.purchase_date"></td>
                                        <td class="cell-mono" style="font-size:11.5px;color:var(--ink3)"
                                            x-text="po.delivery_date||'—'"></td>
                                        <td class="cell-right">
                                            <span class="cell-mono" style="font-weight:600"
                                                x-text="'Af ' + fmt(po.total_cost)"></span>
                                        </td>
                                        <td class="cell-right">
                                            <span class="cell-mono" style="color:var(--green)"
                                                x-text="'Af ' + fmt(po.amount_paid)"></span>
                                        </td>
                                        <td>
                                            <div class="po-progress">
                                                <div class="po-prog-bar">
                                                    <div class="po-prog-fill" :style="`width:${po.received_pct}%`"></div>
                                                </div>
                                                <span class="po-prog-val" x-text="po.received_pct + '%'"></span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="pill"
                                                :class="{
                                                    'pill-navy': po.status==='ordered',
                                                    'pill-teal': po.status==='partial',
                                                    'pill-green': po.status==='received',
                                                    'pill-gray': po.status==='cancelled',
                                                }"
                                                x-text="po.status"></span>
                                        </td>
                                        <td>
                                            <span class="pill"
                                                :class="{
                                                    'pill-red': po.payment_status==='unpaid',
                                                    'pill-amber': po.payment_status==='partial',
                                                    'pill-green': po.payment_status==='paid',
                                                }"
                                                x-text="po.payment_status"></span>
                                        </td>
                                        <td @click.stop>
                                            <div class="row-acts">
                                                <button type="button" class="btn btn-teal btn-sm"
                                                    x-show="po.status!=='received' && po.status!=='cancelled'"
                                                    @click="openPODetail(po)" title="Receive Stock">
                                                    <i class="fas fa-boxes-stacked"></i>
                                                </button>
                                                <button type="button" class="btn btn-danger btn-sm"
                                                    x-show="po.status==='ordered'" @click="cancelPO(po)" title="Cancel">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                        <div class="pag-row" x-show="poPagination.last_page>1">
                            <div class="pag-info">Showing <span x-text="poPagination.from"></span>–<span
                                    x-text="poPagination.to"></span> of <span x-text="poPagination.total"></span></div>
                            <div class="pag-btns">
                                <button class="pag-btn" @click="poGoPage(poPagination.current_page-1)"
                                    :disabled="poPagination.current_page === 1"><i class="fas fa-chevron-left"></i></button>
                                <template x-for="p in poPagination.last_page" :key="p">
                                    <button class="pag-btn" :class="p === poPagination.current_page ? 'active' : ''"
                                        @click="poGoPage(p)" x-text="p"></button>
                                </template>
                                <button class="pag-btn" @click="poGoPage(poPagination.current_page+1)"
                                    :disabled="poPagination.current_page === poPagination.last_page"><i
                                        class="fas fa-chevron-right"></i></button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- PO DETAIL + RECEIVE PANEL --}}
                <div class="detail-panel" x-show="selectedPO" x-cloak>
                    <div class="dp-head">
                        <span class="dp-head-label">Purchase Order</span>
                        <button class="dp-close" @click="selectedPO=null"><i class="fas fa-times"></i></button>
                    </div>
                    <div class="dp-body">

                        {{-- PO Header strip --}}
                        <div style="background:linear-gradient(135deg,#1e2d4f 0%,#172240 100%);padding:1.25rem;color:#fff">
                            <div style="font-family:var(--mono);font-size:12px;color:rgba(255,255,255,.5);margin-bottom:3px"
                                x-text="selectedPO?.local_id"></div>
                            <div style="font-family:var(--display);font-size:20px;font-weight:500;margin-bottom:8px"
                                x-text="selectedPO?.supplier"></div>
                            <div style="display:flex;gap:1rem;flex-wrap:wrap">
                                <div style="font-size:11px;color:rgba(255,255,255,.5)">
                                    <i class="fas fa-calendar"></i> <span x-text="selectedPO?.purchase_date"></span>
                                </div>
                                <div style="font-size:11px;color:rgba(255,255,255,.5)"
                                    x-show="selectedPO?.reference_number">
                                    <i class="fas fa-hashtag"></i> <span x-text="selectedPO?.reference_number"></span>
                                </div>
                            </div>
                            <div style="display:flex;gap:8px;margin-top:10px">
                                <span class="pill"
                                    :class="{
                                        'pill-navy': selectedPO?.status==='ordered',
                                        'pill-teal': selectedPO?.status==='partial',
                                        'pill-green': selectedPO?.status==='received',
                                        'pill-gray': selectedPO?.status==='cancelled',
                                    }"
                                    x-text="selectedPO?.status"></span>
                                <span class="pill"
                                    :class="{
                                        'pill-red': selectedPO?.payment_status==='unpaid',
                                        'pill-amber': selectedPO?.payment_status==='partial',
                                        'pill-green': selectedPO?.payment_status==='paid',
                                    }"
                                    x-text="selectedPO?.payment_status"></span>
                            </div>
                        </div>

                        {{-- Cost summary --}}
                        <div class="sup-kpi-strip">
                            <div class="sk-item">
                                <div class="sk-label">Order Total</div>
                                <div class="sk-val" x-text="'Af ' + fmt(selectedPO?.total_cost||0)"></div>
                            </div>
                            <div class="sk-item">
                                <div class="sk-label">Paid</div>
                                <div class="sk-val" style="color:var(--green)"
                                    x-text="'Af ' + fmt(selectedPO?.amount_paid||0)"></div>
                            </div>
                            <div class="sk-item">
                                <div class="sk-label">Balance</div>
                                <div class="sk-val" style="color:var(--red)"
                                    x-text="'Af ' + fmt((selectedPO?.total_cost||0)-(selectedPO?.amount_paid||0))"></div>
                            </div>
                        </div>

                        {{-- Receive stock section --}}
                        <div class="receive-section"
                            x-show="selectedPO?.status!=='received' && selectedPO?.status!=='cancelled'">
                            <div class="dp-section-title" style="margin-bottom:.75rem">
                                <i class="fas fa-boxes-stacked" style="color:var(--teal)"></i> Receive Stock
                                <span style="font-size:10px;color:var(--ink3);font-weight:400;margin-left:auto">Enter qty
                                    received per item</span>
                            </div>
                            <div x-show="poItemsLoading"
                                style="text-align:center;padding:1rem;color:var(--ink3);font-size:12px">
                                <i class="fas fa-spinner fa-spin"></i>
                            </div>
                            <div x-show="!poItemsLoading">
                                <template x-for="item in poItems" :key="item.id">
                                    <div class="receive-item"
                                        :class="item.quantity_received >= item.quantity_ordered ? 'fully-received' : ''">
                                        <div class="ri-info">
                                            <div class="ri-name" x-text="item.product_name"></div>
                                            <div class="ri-detail"
                                                x-text="item.sku + ' · Ordered: ' + item.quantity_ordered + ' · Received: ' + item.quantity_received">
                                            </div>
                                        </div>
                                        <div class="ri-qty-input">
                                            <span class="ri-ordered"
                                                x-text="'Max: ' + (item.quantity_ordered - item.quantity_received)"></span>
                                            <input class="ri-input" type="number" x-model.number="item.receive_qty"
                                                :max="item.quantity_ordered - item.quantity_received" min="0"
                                                placeholder="0" :disabled="item.quantity_received >= item.quantity_ordered">
                                        </div>
                                    </div>
                                </template>
                                <button type="button" class="btn btn-teal" style="width:100%;margin-top:.5rem"
                                    @click="receiveStock()"
                                    :disabled="receiveSaving || poItems.every(i => i.quantity_received >= i.quantity_ordered)">
                                    <i class="fas fa-spinner fa-spin" x-show="receiveSaving"></i>
                                    <i class="fas fa-check" x-show="!receiveSaving"></i>
                                    <span x-text="receiveSaving?'Receiving…':'Receive Selected Stock'"></span>
                                </button>
                                <div x-show="receiveError" x-cloak
                                    style="margin-top:6px;padding:8px 10px;background:var(--rdim);border:1px solid rgba(220,38,38,.2);border-radius:var(--rsm);font-size:12px;color:var(--red)"
                                    x-text="receiveError"></div>
                            </div>
                        </div>

                        {{-- Notes --}}
                        <div class="dp-section" x-show="selectedPO?.notes">
                            <div class="dp-section-title"><i class="fas fa-pen"></i> Notes</div>
                            <div style="font-size:12.5px;color:var(--ink2);line-height:1.6" x-text="selectedPO?.notes">
                            </div>
                        </div>

                    </div>
                    <div class="dp-foot">
                        <button type="button" class="btn btn-danger" x-show="selectedPO?.status==='ordered'"
                            @click="cancelPO(selectedPO)">
                            <i class="fas fa-times"></i> Cancel PO
                        </button>
                    </div>
                </div>

            </div>{{-- /sp-main purchases --}}
        </div>

        {{-- ══════════════════════════════════════════
     MODAL: ADD / EDIT SUPPLIER
══════════════════════════════════════════ --}}
        <div class="modal-overlay" x-show="showSupplierModal" x-cloak @click.self="showSupplierModal=false">
            <div class="modal-card modal-md">
                <div class="modal-head">
                    <div class="modal-title" x-text="editingSupplier ? 'Edit Supplier' : 'New Supplier'"></div>
                    <button class="modal-close" @click="showSupplierModal=false"><i class="fas fa-times"></i></button>
                </div>
                <div class="modal-body">

                    <div class="form-section-title"><i class="fas fa-building"></i> Supplier Info</div>
                    <div class="form-grid form-2" style="margin-bottom:1rem">
                        <div>
                            <label class="field-label">Company Name <span class="field-req">*</span></label>
                            <input type="text" class="field-input" x-model="sf.name"
                                placeholder="Supplier company name">
                        </div>
                        <div>
                            <label class="field-label">Contact Person</label>
                            <input type="text" class="field-input" x-model="sf.contact_person"
                                placeholder="Main contact name">
                        </div>
                        <div>
                            <label class="field-label">Phone <span class="field-req">*</span></label>
                            <input type="text" class="field-input" x-model="sf.phone" placeholder="07XX XXX XXXX">
                        </div>
                        <div>
                            <label class="field-label">Secondary Phone</label>
                            <input type="text" class="field-input" x-model="sf.phone_secondary"
                                placeholder="Optional">
                        </div>
                        <div>
                            <label class="field-label">Email</label>
                            <input type="email" class="field-input" x-model="sf.email"
                                placeholder="supplier@example.com">
                        </div>
                        <div>
                            <label class="field-label">City</label>
                            <input type="text" class="field-input" x-model="sf.city" placeholder="Kabul, Kandahar…">
                        </div>
                        <div style="grid-column:span 2">
                            <label class="field-label">Address</label>
                            <input type="text" class="field-input" x-model="sf.address" placeholder="Full address">
                        </div>
                        <div>
                            <label class="field-label">Payment Terms</label>
                            <input type="text" class="field-input" x-model="sf.payment_terms"
                                placeholder="e.g. Net 30, Cash on delivery">
                            <div class="field-hint">Agreed payment schedule with this supplier</div>
                        </div>
                        <div>
                            <label class="field-label">Status</label>
                            <select class="field-input" x-model="sf.is_active">
                                <option :value="true">Active</option>
                                <option :value="false">Inactive</option>
                            </select>
                        </div>
                        <div style="grid-column:span 2">
                            <label class="field-label">Notes</label>
                            <textarea class="field-input" x-model="sf.notes" placeholder="Optional notes about this supplier…"></textarea>
                        </div>
                    </div>

                    <div class="form-err" x-show="formError" x-text="formError" x-cloak></div>
                </div>
                <div class="modal-foot">
                    <button type="button" class="btn btn-ghost" @click="showSupplierModal=false">Cancel</button>
                    <button type="button" class="btn btn-primary" @click="saveSupplier()" :disabled="saving">
                        <i class="fas fa-spinner fa-spin" x-show="saving"></i>
                        <span x-text="saving?'Saving…':(editingSupplier?'Update Supplier':'Add Supplier')"></span>
                    </button>
                </div>
            </div>
        </div>

    </div>{{-- /sp --}}
@endsection

@push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('suppliersPage', () => ({

                activeTab: 'suppliers',

                /* suppliers */
                suppliers: [],
                supPagination: {},
                supLoading: true,
                supSearch: '',
                supFilterStatus: '',
                supFilterCity: '',
                supSortCol: 'name',
                supSortDir: 'asc',
                supPage: 1,
                selectedSupplier: null,
                supplierPOs: [],
                supDetailLoading: false,

                /* purchase orders */
                purchaseOrders: [],
                poPagination: {},
                poLoading: true,
                poSearch: '',
                poFilterStatus: '',
                poFilterPayment: '',
                poPage: 1,
                selectedPO: null,
                poItems: [],
                poItemsLoading: false,
                receiveSaving: false,
                receiveError: '',

                /* supplier modal */
                showSupplierModal: false,
                editingSupplier: null,
                sf: {},
                formError: '',
                saving: false,

                /* urls */
                urls: {
                    suppliers: '{{ route('pos.suppliers.index') }}',
                    supStore: '{{ route('pos.suppliers.store') }}',
                    supToggle: '{{ url('pos/suppliers') }}',
                    supDetail: '{{ url('pos/suppliers') }}',
                    pos: '{{ route('pos.purchases.index') }}',
                    poItems: '{{ url('pos/purchases') }}',
                    poReceive: '{{ route('pos.purchases.receive') }}',
                    poCancel: '{{ url('pos/purchases') }}',
                    csrf: document.querySelector('meta[name=csrf-token]').content,
                },

                /* ── Init ── */
                init() {
                    this.loadSuppliers();
                    this.loadPOs();
                },

                switchTab(t) {
                    this.activeTab = t;
                    this.selectedSupplier = null;
                    this.selectedPO = null;
                },

                /* ══════════════════════════════
                   SUPPLIERS
                ══════════════════════════════ */
                async loadSuppliers() {
                    this.supLoading = true;
                    try {
                        const p = new URLSearchParams({
                            q: this.supSearch,
                            status: this.supFilterStatus,
                            city: this.supFilterCity,
                            sort: this.supSortCol,
                            dir: this.supSortDir,
                            page: this.supPage
                        });
                        const r = await fetch(this.urls.suppliers + '?' + p, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        const d = await r.json();
                        this.suppliers = d.data;
                        this.supPagination = d.meta;
                    } catch (e) {
                        console.error(e);
                    } finally {
                        this.supLoading = false;
                    }
                },

                supSort(col) {
                    if (this.supSortCol === col) this.supSortDir = this.supSortDir === 'asc' ? 'desc' :
                        'asc';
                    else {
                        this.supSortCol = col;
                        this.supSortDir = 'asc';
                    }
                    this.loadSuppliers();
                },

                supGoPage(p) {
                    if (p < 1 || p > this.supPagination.last_page) return;
                    this.supPage = p;
                    this.loadSuppliers();
                },

                async openSupplierDetail(s) {
                    this.selectedSupplier = s;
                    this.supplierPOs = [];
                    this.supDetailLoading = true;
                    try {
                        const r = await fetch(`${this.urls.supDetail}/${s.id}/purchases`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        this.supplierPOs = await r.json();
                    } catch (e) {
                        console.error(e);
                    } finally {
                        this.supDetailLoading = false;
                    }
                },

                openSupplierModal(s) {
                    this.editingSupplier = s;
                    this.sf = s ? {
                        ...s
                    } : {
                        name: '',
                        contact_person: '',
                        phone: '',
                        phone_secondary: '',
                        email: '',
                        address: '',
                        city: '',
                        payment_terms: '',
                        notes: '',
                        is_active: true
                    };
                    this.formError = '';
                    this.showSupplierModal = true;
                },

                async saveSupplier() {
                    if (!this.sf.name?.trim()) {
                        this.formError = 'Name is required.';
                        return;
                    }
                    if (!this.sf.phone?.trim()) {
                        this.formError = 'Phone is required.';
                        return;
                    }
                    this.saving = true;
                    this.formError = '';
                    try {
                        const r = await fetch(this.urls.supStore, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.urls.csrf
                            },
                            body: JSON.stringify({
                                ...this.sf,
                                supplier_id: this.editingSupplier?.id
                            })
                        });
                        const d = await r.json();
                        if (d.success) {
                            this.showSupplierModal = false;
                            this.loadSuppliers();
                            if (this.selectedSupplier?.id === this.editingSupplier?.id) this
                                .selectedSupplier = {
                                    ...this.selectedSupplier,
                                    ...d.supplier
                                };
                        } else this.formError = d.message ?? 'Failed to save.';
                    } catch (e) {
                        this.formError = 'Network error.';
                    } finally {
                        this.saving = false;
                    }
                },

                async toggleSupplier(s) {
                    if (!confirm(`${s.is_active ? 'Deactivate' : 'Activate'} ${s.name}?`)) return;
                    await fetch(`${this.urls.supToggle}/${s.id}/toggle`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': this.urls.csrf
                        }
                    });
                    this.loadSuppliers();
                    if (this.selectedSupplier?.id === s.id) this.selectedSupplier = null;
                },

                /* ══════════════════════════════
                   PURCHASE ORDERS
                ══════════════════════════════ */
                async loadPOs() {
                    this.poLoading = true;
                    try {
                        const p = new URLSearchParams({
                            q: this.poSearch,
                            status: this.poFilterStatus,
                            payment: this.poFilterPayment,
                            page: this.poPage
                        });
                        const r = await fetch(this.urls.pos + '?' + p, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        const d = await r.json();
                        this.purchaseOrders = d.data;
                        this.poPagination = d.meta;
                    } catch (e) {
                        console.error(e);
                    } finally {
                        this.poLoading = false;
                    }
                },

                poGoPage(p) {
                    if (p < 1 || p > this.poPagination.last_page) return;
                    this.poPage = p;
                    this.loadPOs();
                },

                async openPODetail(po) {
                    this.selectedPO = po;
                    this.poItems = [];
                    this.receiveError = '';
                    this.poItemsLoading = true;
                    try {
                        const r = await fetch(`${this.urls.poItems}/${po.id}/items`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        const items = await r.json();
                        this.poItems = items.map(i => ({
                            ...i,
                            receive_qty: Math.max(0, i.quantity_ordered - i
                                .quantity_received)
                        }));
                    } catch (e) {
                        console.error(e);
                    } finally {
                        this.poItemsLoading = false;
                    }
                },

                async receiveStock() {
                    const toReceive = this.poItems.filter(i => i.receive_qty > 0);
                    if (!toReceive.length) {
                        this.receiveError = 'Enter at least one quantity to receive.';
                        return;
                    }
                    this.receiveSaving = true;
                    this.receiveError = '';
                    try {
                        const r = await fetch(this.urls.poReceive, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.urls.csrf
                            },
                            body: JSON.stringify({
                                purchase_id: this.selectedPO.id,
                                items: toReceive.map(i => ({
                                    purchase_item_id: i.id,
                                    qty: i.receive_qty
                                }))
                            })
                        });
                        const d = await r.json();
                        if (d.success) {
                            this.loadPOs();
                            this.selectedPO = null;
                        } else this.receiveError = d.message ?? 'Failed to receive stock.';
                    } catch (e) {
                        this.receiveError = 'Network error.';
                    } finally {
                        this.receiveSaving = false;
                    }
                },

                async cancelPO(po) {
                    if (!confirm(`Cancel PO ${po.local_id}? This cannot be undone.`)) return;
                    const r = await fetch(`${this.urls.poCancel}/${po.id}/cancel`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': this.urls.csrf
                        }
                    });
                    const d = await r.json();
                    if (d.success) {
                        this.loadPOs();
                        this.selectedPO = null;
                    }
                },

                /* ── Helpers ── */
                initials(name) {
                    if (!name) return '?';
                    return name.trim().split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2);
                },
                avatarColor(name) {
                    const c = ['#2658e8', '#0891b2', '#15803d', '#d97706', '#7c3aed', '#dc2626',
                        '#1e2d4f'
                    ];
                    return c[(name?.charCodeAt(0) || 0) % c.length];
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
