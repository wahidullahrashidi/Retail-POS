@extends('layouts.app')

@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Instrument+Serif:ital@0;1&family=DM+Sans:wght@300;400;500;600&display=swap"
        rel="stylesheet">
    <style>
        /* ═══════════════════════════════════════════
       DESIGN TOKENS
    ═══════════════════════════════════════════ */
        :root {
            --bg: #f4f5f8;
            --bg-2: #ffffff;
            --bg-3: #f0f1f5;
            --border: rgba(0, 0, 0, .07);
            --border-lit: rgba(0, 0, 0, .13);
            --gold: #b8860b;
            --gold-dim: rgba(184, 134, 11, .1);
            --gold-glow: rgba(184, 134, 11, .2);
            --blue: #2563eb;
            --blue-dim: rgba(37, 99, 235, .1);
            --green: #16a34a;
            --green-dim: rgba(22, 163, 74, .1);
            --red: #dc2626;
            --red-dim: rgba(220, 38, 38, .08);
            --amber: #d97706;
            --text-1: #111318;
            --text-2: #4b5060;
            --text-3: #9299aa;
            --mono: 'DM Mono', monospace;
            --serif: 'Instrument Serif', serif;
            --sans: 'DM Sans', sans-serif;
            --radius: 12px;
            --radius-sm: 8px;
            --radius-lg: 18px;
        }


        /* ═══════════════════════════════════════════
       BASE
    ═══════════════════════════════════════════ */
        .dash-root * {
            box-sizing: border-box;
        }

        .dash-root {
            font-family: var(--sans);
            background: var(--bg);
            min-height: 100vh;
            color: var(--text-1);
            padding: 0 0 3rem;
        }

        /* ═══════════════════════════════════════════
       TOPBAR
    ═══════════════════════════════════════════ */
        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.25rem 2rem;
            border-bottom: 1px solid var(--border);
            background: rgba(255, 255, 255, .9);
            position: sticky;
            top: 0;
            z-index: 50;
            backdrop-filter: blur(12px);
        }

        .topbar-left h1 {
            font-family: var(--serif);
            font-size: 20px;
            color: var(--text-1);
            line-height: 1;
            margin-bottom: 3px;
        }

        .topbar-left p {
            font-size: 12px;
            color: var(--text-2);
            font-weight: 300;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .live-clock {
            font-family: var(--mono);
            font-size: 13px;
            color: var(--text-2);
            background: var(--bg-3);
            border: 1px solid var(--border);
            padding: 6px 14px;
            border-radius: var(--radius-sm);
            letter-spacing: .04em;
        }

        .shift-badge {
            display: flex;
            align-items: center;
            gap: 6px;
            background: var(--green-dim);
            border: 1px solid rgba(34, 197, 94, .2);
            border-radius: var(--radius-sm);
            padding: 6px 12px;
            font-size: 12px;
            color: var(--green);
            font-weight: 500;
        }

        .shift-badge::before {
            content: '';
            width: 6px;
            height: 6px;
            background: var(--green);
            border-radius: 50%;
            animation: pulse-dot 2s ease infinite;
        }

        @keyframes pulse-dot {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: .4;
            }
        }

        .btn-new-sale {
            display: flex;
            align-items: center;
            gap: 7px;
            padding: 8px 16px;
            background: var(--gold);
            color: #0d1117;
            border: none;
            cursor: pointer;
            border-radius: var(--radius-sm);
            font-family: var(--sans);
            font-size: 13px;
            font-weight: 600;
            transition: all .2s;
            letter-spacing: .01em;
        }

        .btn-new-sale:hover {
            background: #e0b84f;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px var(--gold-glow);
        }

        /* ═══════════════════════════════════════════
       BODY LAYOUT
    ═══════════════════════════════════════════ */
        .dash-body {
            padding: 1.75rem 2rem;
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 1.5rem;
            max-width: 1600px;
            margin: 0 auto;
        }

        .col-left {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            min-width: 0;
        }

        .col-right {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        /* ═══════════════════════════════════════════
       CARD BASE
    ═══════════════════════════════════════════ */
        .card {
            background: var(--bg-2);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            overflow: hidden;
            transition: border-color .2s;
        }

        .card:hover {
            border-color: var(--border-lit);
        }

        .card-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--border);
        }

        .card-head-title {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-1);
            letter-spacing: .01em;
        }

        .card-head-title i {
            color: var(--gold);
            font-size: 13px;
        }

        .card-head-badge {
            font-size: 11px;
            color: var(--text-2);
            background: var(--bg-3);
            border: 1px solid var(--border);
            padding: 3px 10px;
            border-radius: 99px;
        }

        /* ═══════════════════════════════════════════
       STAT CARDS
    ═══════════════════════════════════════════ */
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
        }

        .stat-card {
            background: var(--bg-2);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 1.25rem;
            position: relative;
            overflow: hidden;
            transition: all .25s;
            cursor: default;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: var(--accent-color, var(--gold));
            opacity: 0;
            transition: opacity .25s;
        }

        .stat-card:hover {
            border-color: var(--border-lit);
            transform: translateY(-2px);
        }

        .stat-card:hover::before {
            opacity: 1;
        }

        .stat-card.gold {
            --accent-color: var(--gold);
        }

        .stat-card.blue {
            --accent-color: var(--blue);
        }

        .stat-card.green {
            --accent-color: var(--green);
        }

        .stat-card.red {
            --accent-color: var(--red);
        }

        .stat-label {
            font-size: 10px;
            font-weight: 600;
            color: var(--text-2);
            text-transform: uppercase;
            letter-spacing: .1em;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .stat-badge {
            display: inline-flex;
            align-items: center;
            gap: 3px;
            font-size: 10px;
            font-weight: 600;
            padding: 2px 7px;
            border-radius: 99px;
        }

        .stat-badge.up {
            color: var(--green);
            background: var(--green-dim);
        }

        .stat-badge.down {
            color: var(--red);
            background: var(--red-dim);
        }

        .stat-badge.zero {
            color: var(--text-2);
            background: var(--bg-3);
        }

        .stat-value {
            font-family: var(--mono);
            font-size: 26px;
            font-weight: 500;
            color: var(--text-1);
            line-height: 1;
            letter-spacing: -.5px;
        }

        .stat-value span {
            font-size: 14px;
            color: var(--text-2);
            font-weight: 400;
            margin-right: 2px;
        }

        .stat-sub {
            font-size: 11px;
            color: var(--text-3);
            margin-top: 6px;
        }

        .stat-icon {
            position: absolute;
            right: 1.25rem;
            bottom: 1.25rem;
            font-size: 28px;
            opacity: .06;
            color: var(--text-1);
        }

        /* ═══════════════════════════════════════════
       QUICK SALE / CART
    ═══════════════════════════════════════════ */
        .sale-body {
            padding: 1.25rem 1.5rem;
        }

        .search-row {
            display: flex;
            gap: 8px;
            margin-bottom: 1.25rem;
        }

        .search-wrap {
            flex: 1;
            position: relative;
        }

        .search-wrap i {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-3);
            font-size: 13px;
            pointer-events: none;
        }

        .search-input {
            width: 100%;
            padding: 10px 14px 10px 38px;
            background: var(--bg-3);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            color: var(--text-1);
            font-family: var(--sans);
            font-size: 13px;
            outline: none;
            transition: border .18s, box-shadow .18s;
        }

        .search-input:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 3px var(--gold-dim);
        }

        .search-input::placeholder {
            color: var(--text-3);
        }

        .btn-search {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 10px 18px;
            background: var(--blue);
            color: #fff;
            border: none;
            cursor: pointer;
            border-radius: var(--radius-sm);
            font-family: var(--sans);
            font-size: 13px;
            font-weight: 500;
            transition: all .18s;
            white-space: nowrap;
        }

        .btn-search:hover {
            background: #2563eb;
        }

        .btn-clear {
            padding: 10px 14px;
            background: var(--bg-3);
            border: 1px solid var(--border);
            color: var(--text-2);
            border-radius: var(--radius-sm);
            cursor: pointer;
            transition: all .18s;
            font-size: 13px;
        }

        .btn-clear:hover {
            border-color: var(--red);
            color: var(--red);
        }

        /* Section label */
        .section-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: .75rem;
        }

        .section-label {
            font-size: 10px;
            font-weight: 700;
            color: var(--text-2);
            text-transform: uppercase;
            letter-spacing: .1em;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .section-count {
            font-family: var(--mono);
            font-size: 11px;
            color: var(--blue);
        }

        /* Trending grid */
        .trending-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
            margin-bottom: 1rem;
        }

        .trend-btn {
            background: var(--bg-3);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 10px;
            cursor: pointer;
            text-align: left;
            transition: all .18s;
            position: relative;
            overflow: hidden;
        }

        .trend-btn:hover {
            border-color: var(--gold);
            background: var(--gold-dim);
        }

        .trend-btn:disabled {
            opacity: .4;
            cursor: not-allowed;
        }

        .trend-btn:hover:not(:disabled) .trend-plus {
            color: var(--gold);
        }

        .trend-name {
            font-size: 12px;
            font-weight: 500;
            color: var(--text-1);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-bottom: 4px;
        }

        .trend-price {
            font-family: var(--mono);
            font-size: 12px;
            color: var(--gold);
        }

        .trend-sold {
            font-size: 10px;
            color: var(--text-3);
            margin-top: 2px;
        }

        .trend-plus {
            position: absolute;
            right: 8px;
            top: 8px;
            font-size: 11px;
            color: var(--text-3);
            transition: color .15s;
        }

        /* Skeleton */
        .skel {
            background: linear-gradient(90deg, var(--bg-3) 25%, var(--bg-2) 50%, var(--bg-3) 75%);
            background-size: 200% 100%;
            animation: skel-anim 1.4s ease infinite;
            border-radius: 4px;
        }

        @keyframes skel-anim {
            0% {
                background-position: 200% 0;
            }

            100% {
                background-position: -200% 0;
            }
        }

        .skel-card {
            background: var(--bg-3);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 10px;
        }

        /* Search results / Cart table */
        .mini-table-wrap {
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            overflow: hidden;
            margin-bottom: 1rem;
            max-height: 220px;
            overflow-y: auto;
        }

        .mini-table-wrap::-webkit-scrollbar {
            width: 4px;
        }

        .mini-table-wrap::-webkit-scrollbar-track {
            background: var(--bg-3);
        }

        .mini-table-wrap::-webkit-scrollbar-thumb {
            background: var(--border-lit);
            border-radius: 2px;
        }

        .mini-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12.5px;
        }

        .mini-table thead {
            background: var(--bg-3);
            position: sticky;
            top: 0;
        }

        .mini-table th {
            padding: 8px 12px;
            color: var(--text-2);
            font-weight: 600;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: .06em;
            white-space: nowrap;
        }

        .mini-table td {
            padding: 9px 12px;
            border-top: 1px solid var(--border);
            color: var(--text-1);
            vertical-align: middle;
        }

        .mini-table tbody tr:hover {
            background: rgba(255, 255, 255, .02);
        }

        .mini-table td.mono {
            font-family: var(--mono);
            font-size: 11px;
            color: var(--text-2);
        }

        .stock-ok {
            color: var(--green);
            font-family: var(--mono);
            font-size: 12px;
        }

        .stock-low {
            color: var(--amber);
            font-family: var(--mono);
            font-size: 12px;
            font-weight: 600;
        }

        .stock-none {
            color: var(--red);
            font-family: var(--mono);
            font-size: 12px;
            font-weight: 700;
        }

        /* qty controls */
        .qty-ctrl {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .qty-btn {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: var(--bg-3);
            border: 1px solid var(--border);
            color: var(--text-2);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            transition: all .15s;
        }

        .qty-btn:hover:not(:disabled) {
            background: var(--gold-dim);
            border-color: var(--gold);
            color: var(--gold);
        }

        .qty-btn:disabled {
            opacity: .3;
            cursor: not-allowed;
        }

        .qty-num {
            font-family: var(--mono);
            font-size: 13px;
            min-width: 20px;
            text-align: center;
        }

        .btn-add {
            padding: 5px 10px;
            background: var(--blue-dim);
            border: 1px solid rgba(59, 130, 246, .25);
            color: var(--blue);
            border-radius: var(--radius-sm);
            cursor: pointer;
            font-size: 11px;
            font-weight: 600;
            font-family: var(--sans);
            transition: all .15s;
            white-space: nowrap;
        }

        .btn-add:hover:not(:disabled) {
            background: var(--blue);
            color: #fff;
        }

        .btn-add:disabled {
            opacity: .3;
            cursor: not-allowed;
        }

        .btn-remove {
            background: none;
            border: none;
            cursor: pointer;
            color: var(--text-3);
            font-size: 13px;
            transition: color .15s;
        }

        .btn-remove:hover {
            color: var(--red);
        }

        /* Checkout bar */
        .checkout-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: linear-gradient(135deg, #1e3a8a 0%, #1d4ed8 100%);
            border: 1px solid rgba(37, 99, 235, .3);
            border-radius: var(--radius);
            padding: 1.1rem 1.25rem;
            margin-top: .25rem;
        }

        .checkout-total-label {
            font-size: 10px;
            font-weight: 700;
            color: rgba(148, 163, 218, .6);
            text-transform: uppercase;
            letter-spacing: .1em;
            margin-bottom: 4px;
        }

        .checkout-total-val {
            font-family: var(--mono);
            font-size: 28px;
            font-weight: 500;
            color: #fff;
            letter-spacing: -.5px;
        }

        .checkout-total-val span {
            font-size: 14px;
            color: rgba(255, 255, 255, .5);
            margin-right: 3px;
        }

        .btn-checkout {
            padding: 11px 24px;
            background: var(--gold);
            color: #0d1117;
            border: none;
            cursor: pointer;
            border-radius: var(--radius-sm);
            font-family: var(--sans);
            font-size: 14px;
            font-weight: 700;
            letter-spacing: .03em;
            transition: all .2s;
        }

        .btn-checkout:hover:not(:disabled) {
            background: #e0b84f;
            transform: translateY(-1px);
            box-shadow: 0 6px 24px var(--gold-glow);
        }

        .btn-checkout:disabled {
            opacity: .35;
            cursor: not-allowed;
        }

        /* Empty / spinner */
        .empty-state {
            text-align: center;
            padding: 2rem 1rem;
            color: var(--text-3);
            font-size: 12px;
        }

        .empty-state i {
            font-size: 22px;
            margin-bottom: 8px;
            display: block;
        }

        .spinner-row {
            text-align: center;
            padding: 1.5rem;
            color: var(--text-2);
            font-size: 13px;
        }

        /* ═══════════════════════════════════════════
       QUICK ACTIONS
    ═══════════════════════════════════════════ */
        .action-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: .75rem;
        }

        .action-card {
            background: var(--bg-2);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 1.1rem;
            cursor: pointer;
            transition: all .2s;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .action-card:hover {
            border-color: var(--gold);
            background: var(--gold-dim);
            transform: translateY(-2px);
        }

        .action-icon {
            width: 36px;
            height: 36px;
            border-radius: var(--radius-sm);
            background: var(--bg-3);
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            color: var(--gold);
            transition: all .2s;
        }

        .action-card:hover .action-icon {
            background: var(--gold);
            color: #0d1117;
            border-color: var(--gold);
        }

        .action-title {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-1);
        }

        .action-sub {
            font-size: 11px;
            color: var(--text-3);
        }

        /* ═══════════════════════════════════════════
       RECENT TRANSACTIONS TABLE
    ═══════════════════════════════════════════ */
        .txn-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .txn-table th {
            padding: 10px 1.5rem;
            text-align: left;
            color: var(--text-3);
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .08em;
            background: var(--bg-3);
            border-bottom: 1px solid var(--border);
        }

        .txn-table td {
            padding: 12px 1.5rem;
            border-bottom: 1px solid var(--border);
            color: var(--text-1);
            vertical-align: middle;
        }

        .txn-table tbody tr:last-child td {
            border-bottom: none;
        }

        .txn-table tbody tr {
            transition: background .15s;
        }

        .txn-table tbody tr:hover {
            background: rgba(255, 255, 255, .02);
        }

        .txn-id {
            font-family: var(--mono);
            font-size: 12px;
            color: var(--gold);
        }

        .avatar {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--blue), #7c3aed);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: 700;
            color: #fff;
            flex-shrink: 0;
        }

        .customer-cell {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .time-cell {
            font-family: var(--mono);
            font-size: 12px;
            color: var(--text-2);
        }

        .amount-cell {
            font-family: var(--mono);
            font-size: 13px;
            font-weight: 500;
            text-align: right;
        }

        .badge {
            display: inline-block;
            padding: 3px 9px;
            border-radius: 99px;
            font-size: 10px;
            font-weight: 600;
            letter-spacing: .04em;
        }

        .badge-cash {
            background: var(--green-dim);
            color: var(--green);
            border: 1px solid rgba(34, 197, 94, .2);
        }

        .badge-loan {
            background: var(--gold-dim);
            color: var(--gold);
            border: 1px solid rgba(212, 168, 71, .2);
        }

        /* ═══════════════════════════════════════════
       RIGHT COLUMN
    ═══════════════════════════════════════════ */

        /* Low stock */
        .stock-list {
            padding: .75rem 1.5rem 1.5rem;
            display: flex;
            flex-direction: column;
            gap: .75rem;
        }

        .stock-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: .75rem;
            background: var(--bg-3);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            transition: border-color .15s;
        }

        .stock-item:hover {
            border-color: var(--red);
        }

        .stock-sku {
            font-family: var(--mono);
            font-size: 12px;
            color: var(--text-1);
        }

        .stock-min {
            font-size: 11px;
            color: var(--text-3);
            margin-top: 2px;
        }

        .stock-qty {
            font-family: var(--mono);
            font-size: 14px;
            font-weight: 700;
            color: var(--red);
        }

        .stock-bar {
            width: 100%;
            height: 3px;
            background: var(--bg-3);
            border-radius: 2px;
            margin-top: 6px;
            overflow: hidden;
        }

        .stock-bar-fill {
            height: 100%;
            background: var(--red);
            border-radius: 2px;
            transition: width .5s ease;
        }

        .btn-purchase {
            display: block;
            width: calc(100% - 3rem);
            margin: 0 1.5rem 1.5rem;
            padding: 10px;
            background: rgba(239, 68, 68, .1);
            border: 1px solid rgba(239, 68, 68, .25);
            color: var(--red);
            border-radius: var(--radius-sm);
            font-family: var(--sans);
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all .2s;
            text-align: center;
        }

        .btn-purchase:hover {
            background: var(--red);
            color: #fff;
        }

        /* Hardware status */
        .hw-list {
            padding: .75rem 1.5rem 1.5rem;
            display: flex;
            flex-direction: column;
            gap: .6rem;
        }

        .hw-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: .7rem .85rem;
            background: var(--bg-3);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
        }

        .hw-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .hw-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
        }

        .hw-icon.ok {
            background: var(--green-dim);
            color: var(--green);
        }

        .hw-icon.warn {
            background: rgba(245, 158, 11, .12);
            color: var(--amber);
        }

        .hw-icon.err {
            background: var(--red-dim);
            color: var(--red);
        }

        .hw-name {
            font-size: 12px;
            font-weight: 500;
            color: var(--text-1);
        }

        .hw-time {
            font-size: 10px;
            color: var(--text-3);
            margin-top: 2px;
        }

        .hw-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .hw-dot.ok {
            background: var(--green);
            box-shadow: 0 0 6px var(--green);
            animation: pulse-dot 2.5s infinite;
        }

        .hw-dot.warn {
            background: var(--amber);
            box-shadow: 0 0 6px var(--amber);
        }

        .hw-dot.err {
            background: var(--red);
        }

        /* Shift summary mini-card */
        .shift-card {
            padding: 1.25rem 1.5rem;
        }

        .shift-stats {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: .75rem;
        }

        .shift-stat {
            background: var(--bg-3);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            padding: .75rem;
        }

        .shift-stat-label {
            font-size: 10px;
            color: var(--text-3);
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-bottom: 4px;
        }

        .shift-stat-val {
            font-family: var(--mono);
            font-size: 16px;
            font-weight: 500;
            color: var(--text-1);
        }

        /* No results */
        .no-results {
            text-align: center;
            padding: 2rem;
            color: var(--text-3);
            font-size: 12.5px;
        }

        .no-results i {
            font-size: 20px;
            margin-bottom: 8px;
            display: block;
            color: var(--text-3);
        }

        /* [x-cloak] */
        [x-cloak] {
            display: none !important;
        }

        /* Scrollbar global */
        ::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }

        ::-webkit-scrollbar-track {
            background: var(--bg-2);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--border-lit);
            border-radius: 3px;
        }
    </style>
@endpush

@section('content')
    <div class="dash-root">

        {{-- ══════════════════════════════
         TOPBAR
    ══════════════════════════════ --}}
        <div class="topbar">
            <div class="topbar-left">
                <h1>Afghan POS <em style="font-style:italic;color:var(--gold);">Dashboard</em></h1>
                <p>Welcome back — here's what's happening right now.</p>
            </div>
            <div class="topbar-right">
                <div class="live-clock" id="liveClock">--:--:--</div>
                <div class="shift-badge">Shift Active</div>
                <button class="btn-new-sale">
                    <i class="fas fa-bolt"></i> New Sale
                </button>
            </div>
        </div>

        <div class="dash-body">

            {{-- ══ LEFT COLUMN ══ --}}
            <div class="col-left">

                {{-- STAT CARDS --}}
                <div class="stat-grid">

                    {{-- Today's Sales --}}
                    <div class="stat-card gold">
                        <div class="stat-label">
                            Today's Sales
                            @php
                                $salesDir =
                                    $todaySales > $yesterdaySales
                                        ? 'up'
                                        : ($todaySales < $yesterdaySales
                                            ? 'down'
                                            : 'zero');
                                $salesPct =
                                    $yesterdaySales > 0
                                        ? round(abs((($todaySales - $yesterdaySales) / $yesterdaySales) * 100), 1)
                                        : ($todaySales > 0
                                            ? 100
                                            : 0);
                            @endphp
                            <span class="stat-badge {{ $salesDir }}">
                                <i
                                    class="fas fa-arrow-{{ $salesDir === 'up' ? 'up' : ($salesDir === 'down' ? 'down' : 'minus') }}"></i>
                                {{ $salesPct }}%
                            </span>
                        </div>
                        <div class="stat-value"><span>Af</span>{{ number_format($todaySales) }}</div>
                        <div class="stat-sub">vs Af {{ number_format($yesterdaySales) }} yesterday</div>
                        <i class="fas fa-coins stat-icon"></i>
                    </div>

                    {{-- Active Loans --}}
                    <div class="stat-card blue">
                        <div class="stat-label">
                            Active Loans
                            @php
                                $loanDir =
                                    $loanToday > $loanYesterday
                                        ? 'up'
                                        : ($loanToday < $loanYesterday
                                            ? 'down'
                                            : 'zero');
                                $loanPct =
                                    $loanYesterday > 0 ? round(abs($loanPercentage), 1) : ($loanToday > 0 ? 100 : 0);
                            @endphp
                            <span class="stat-badge {{ $loanDir }}">
                                <i
                                    class="fas fa-arrow-{{ $loanDir === 'up' ? 'up' : ($loanDir === 'down' ? 'down' : 'minus') }}"></i>
                                {{ $loanPct }}%
                            </span>
                        </div>
                        <div class="stat-value"><span>Af</span>{{ number_format($loanToday) }}</div>
                        <div class="stat-sub">remaining balance today</div>
                        <i class="fas fa-file-invoice-dollar stat-icon"></i>
                    </div>

                    {{-- Net Profit --}}
                    <div class="stat-card green">
                        <div class="stat-label">
                            Net Profit
                            @php
                                $profitDir =
                                    $netProfitToday > $netProfitYesterday
                                        ? 'up'
                                        : ($netProfitToday < $netProfitYesterday
                                            ? 'down'
                                            : 'zero');
                                $profitPct = round(abs($netProfitPercentage), 1);
                            @endphp
                            <span class="stat-badge {{ $profitDir }}">
                                <i
                                    class="fas fa-arrow-{{ $profitDir === 'up' ? 'up' : ($profitDir === 'down' ? 'down' : 'minus') }}"></i>
                                {{ $profitPct }}%
                            </span>
                        </div>
                        <div class="stat-value"><span>Af</span>{{ number_format($netProfitToday) }}</div>
                        <div class="stat-sub">sales − cost of goods</div>
                        <i class="fas fa-chart-line stat-icon"></i>
                    </div>

                    {{-- Customers --}}
                    <div class="stat-card red">
                        <div class="stat-label">
                            Customers
                            @php
                                $custDir =
                                    $todaysCustomers > $yesterdayCustomers
                                        ? 'up'
                                        : ($todaysCustomers < $yesterdayCustomers
                                            ? 'down'
                                            : 'zero');
                                $custPct =
                                    $yesterdayCustomers > 0
                                        ? round(abs($customersPercentage), 1)
                                        : ($todaysCustomers > 0
                                            ? 100
                                            : 0);
                            @endphp
                            <span class="stat-badge {{ $custDir }}">
                                <i
                                    class="fas fa-arrow-{{ $custDir === 'up' ? 'up' : ($custDir === 'down' ? 'down' : 'minus') }}"></i>
                                {{ $custPct }}%
                            </span>
                        </div>
                        <div class="stat-value">{{ number_format($todaysCustomers) }}</div>
                        <div class="stat-sub">vs {{ $yesterdayCustomers }} yesterday</div>
                        <i class="fas fa-users stat-icon"></i>
                    </div>

                </div>
                {{-- /stat-grid --}}

                {{-- QUICK SALE ENTRY --}}
                <div class="card" x-data="cartSystem()" x-init="init()">

                    <div class="card-head">
                        <div class="card-head-title">
                            <i class="fas fa-bolt"></i>
                            Quick Sale Entry
                        </div>
                        <span class="card-head-badge"
                            x-text="cart.length ? cart.length + ' item(s) in cart' : 'Scan or Search'">
                        </span>
                    </div>

                    <div class="sale-body">

                        {{-- Search --}}
                        <div class="search-row">
                            <div class="search-wrap">
                                <i class="fas fa-barcode"></i>
                                <input class="search-input" type="text" x-model="query" @input.debounce.400ms="search()"
                                    @keydown.enter="search()" @keydown.escape="clearSearch()"
                                    placeholder="Barcode, SKU, or product name...">
                            </div>
                            <button type="button" class="btn-search" @click="search()">
                                <i class="fas fa-search"></i> Search
                            </button>
                            <button type="button" class="btn-clear" x-show="query" x-cloak @click="clearSearch()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        {{-- Spinner --}}
                        <div class="spinner-row" x-show="searching" x-cloak>
                            <i class="fas fa-spinner fa-spin"></i> Searching...
                        </div>

                        {{-- ── SEARCH RESULTS ── --}}
                        <div x-show="query && !searching" x-cloak>
                            <div class="no-results" x-show="searchResults.length === 0">
                                <i class="fas fa-magnifying-glass"></i>
                                No products found for "<span x-text="query"></span>"
                            </div>
                            <div x-show="searchResults.length > 0">
                                <div class="section-row">
                                    <div class="section-label"><i class="fas fa-list"></i> Results</div>
                                    <div class="section-count" x-text="searchResults.length + ' found'"></div>
                                </div>
                                <div class="mini-table-wrap">
                                    <table class="mini-table">
                                        <thead>
                                            <tr>
                                                <th class="text-left">Product</th>
                                                <th class="text-left">SKU</th>
                                                <th class="text-right">Price</th>
                                                <th class="text-right">Stock</th>
                                                <th class="text-center">Add</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <template x-for="p in searchResults" :key="p.variant_id">
                                                <tr>
                                                    <td x-text="p.name" style="font-weight:500"></td>
                                                    <td class="mono" x-text="p.sku"></td>
                                                    <td class="text-right mono" style="color:var(--gold)">
                                                        Af <span x-text="fmt(p.price)"></span>
                                                    </td>
                                                    <td class="text-right">
                                                        <span
                                                            :class="p.stock_quantity === 0 ? 'stock-none' : p.stock_quantity <
                                                                5 ? 'stock-low' : 'stock-ok'"
                                                            x-text="p.stock_quantity"></span>
                                                    </td>
                                                    <td class="text-center">
                                                        <button type="button" class="btn-add" @click="addToCart(p)"
                                                            :disabled="p.stock_quantity === 0">
                                                            <i class="fas fa-plus"></i> Add
                                                        </button>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        {{-- ── TRENDING ── --}}
                        <div x-show="!query" x-cloak>
                            <div class="section-row">
                                <div class="section-label">
                                    <i class="fas fa-fire" style="color:var(--amber)"></i> Trending This Week
                                </div>
                                <div x-show="trendingLoading" style="color:var(--text-3);font-size:11px">
                                    <i class="fas fa-spinner fa-spin"></i>
                                </div>
                            </div>

                            {{-- Skeleton --}}
                            <div class="trending-grid" x-show="trendingLoading">
                                <template x-for="i in [1,2,3,4,5,6]" :key="i">
                                    <div class="skel-card">
                                        <div class="skel" style="height:11px;width:75%;margin-bottom:8px"></div>
                                        <div class="skel" style="height:11px;width:45%"></div>
                                    </div>
                                </template>
                            </div>

                            {{-- Products --}}
                            <div class="trending-grid" x-show="!trendingLoading">
                                <template x-for="p in trendingProducts" :key="p.variant_id">
                                    <button type="button" class="trend-btn" @click="addToCart(p)"
                                        :disabled="p.stock_quantity === 0">
                                        <i class="fas fa-plus trend-plus"></i>
                                        <div class="trend-name" x-text="p.name"></div>
                                        <div class="trend-price">Af <span x-text="fmt(p.price)"></span></div>
                                        <div class="trend-sold" x-text="p.total_sold + ' sold'"></div>
                                    </button>
                                </template>
                                <div x-show="!trendingProducts.length" class="empty-state" style="grid-column:1/-1">
                                    <i class="fas fa-box-open"></i> No trending data yet
                                </div>
                            </div>
                        </div>

                        {{-- ── CART ── --}}
                        <div x-show="cart.length > 0" x-cloak style="margin-top:.75rem">
                            <div class="section-row">
                                <div class="section-label"><i class="fas fa-cart-shopping"></i> Cart</div>
                                <div style="display:flex;align-items:center;gap:12px">
                                    <span class="section-count" x-text="cart.length + ' item(s)'"></span>
                                    <button type="button" @click="clearCart()"
                                        style="font-size:11px;color:var(--red);background:none;border:none;cursor:pointer;font-family:var(--sans)">
                                        <i class="fas fa-trash"></i> Clear
                                    </button>
                                </div>
                            </div>
                            <div class="mini-table-wrap" style="max-height:180px">
                                <table class="mini-table">
                                    <thead>
                                        <tr>
                                            <th class="text-left">Item</th>
                                            <th class="text-center">Qty</th>
                                            <th class="text-right">Unit</th>
                                            <th class="text-right">Total</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(item, idx) in cart" :key="item.variant_id">
                                            <tr>
                                                <td style="font-weight:500;max-width:140px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis"
                                                    x-text="item.name"></td>
                                                <td>
                                                    <div class="qty-ctrl">
                                                        <button type="button" class="qty-btn" @click="decQty(idx)">
                                                            <i class="fas fa-minus"></i>
                                                        </button>
                                                        <span class="qty-num" x-text="item.qty"></span>
                                                        <button type="button" class="qty-btn" @click="incQty(idx)"
                                                            :disabled="item.qty >= item.stock_quantity">
                                                            <i class="fas fa-plus"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                                <td class="text-right mono" style="color:var(--text-2)">
                                                    <span x-text="fmt(item.price)"></span>
                                                </td>
                                                <td class="text-right mono" style="color:var(--gold);font-weight:500">
                                                    <span x-text="fmt(item.price * item.qty)"></span>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn-remove"
                                                        @click="removeFromCart(idx)">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Empty cart hint --}}
                        <div x-show="cart.length === 0 && !query" x-cloak
                            style="text-align:center;padding:.5rem 0 .75rem;font-size:11px;color:var(--text-3)">
                            Add products from trending or search to start a sale
                        </div>

                        {{-- CHECKOUT BAR --}}
                        <div class="checkout-bar">
                            <div>
                                <div class="checkout-total-label">Total Amount Due</div>
                                <div class="checkout-total-val">
                                    <span>Af</span><span x-text="fmt(cartTotal)"></span>
                                </div>
                            </div>
                            <button type="button" class="btn-checkout" @click="checkout()"
                                :disabled="cart.length === 0">
                                <i class="fas fa-bolt" style="margin-right:6px"></i> CHECKOUT
                            </button>
                        </div>

                    </div>
                </div>
                {{-- /quick sale --}}

                {{-- QUICK ACTIONS --}}
                <div class="action-grid">
                    <div class="action-card">
                        <div class="action-icon"><i class="fas fa-user-plus"></i></div>
                        <div class="action-title">New Customer</div>
                        <div class="action-sub">Register for loans</div>
                    </div>
                    <div class="action-card">
                        <div class="action-icon"><i class="fas fa-cube"></i></div>
                        <div class="action-title">Add Product</div>
                        <div class="action-sub">Inventory update</div>
                    </div>
                    <div class="action-card">
                        <div class="action-icon"><i class="fas fa-clock-rotate-left"></i></div>
                        <div class="action-title">Sales Log</div>
                        <div class="action-sub">Review transactions</div>
                    </div>
                    <div class="action-card">
                        <div class="action-icon"><i class="fas fa-credit-card"></i></div>
                        <div class="action-title">Loan Payment</div>
                        <div class="action-sub">Receive Af payments</div>
                    </div>
                </div>

                {{-- RECENT TRANSACTIONS --}}
                <div class="card">
                    <div class="card-head">
                        <div class="card-head-title">
                            <i class="fas fa-receipt"></i>
                            Recent Transactions
                        </div>
                        <span class="card-head-badge">Last 5 sales</span>
                    </div>
                    <div style="overflow-x:auto">
                        <table class="txn-table">
                            <thead>
                                <tr>
                                    <th>Ref</th>
                                    <th>Customer</th>
                                    <th>Time</th>
                                    <th>Method</th>
                                    <th class="text-right">Amount</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentTransactions as $rt)
                                    @php
                                        $parts = array_values(array_filter(explode(' ', trim($rt->customer_name))));
                                        $initials =
                                            count($parts) === 1
                                                ? strtoupper(substr($parts[0], 0, 2))
                                                : strtoupper(
                                                    collect($parts)->map(fn($p) => substr($p, 0, 1))->join(''),
                                                );
                                    @endphp
                                    <tr>
                                        <td><span class="txn-id">{{ $rt->address ?? '—' }}</span></td>
                                        <td>
                                            <div class="customer-cell">
                                                <div class="avatar">{{ $initials }}</div>
                                                <span style="font-weight:500">{{ $rt->customer_name }}</span>
                                            </div>
                                        </td>
                                        <td class="time-cell">
                                            {{ \Carbon\Carbon::parse($rt->created_at)->diffForHumans() }}</td>
                                        <td>
                                            <span class="badge {{ $rt->type === 'loan' ? 'badge-loan' : 'badge-cash' }}">
                                                {{ ucfirst($rt->type ?? 'cash') }}
                                            </span>
                                        </td>
                                        <td class="amount-cell" style="color:var(--gold)">
                                            Af {{ number_format($rt->amount) }}
                                        </td>
                                        <td class="text-center">
                                            <button
                                                style="background:none;border:none;cursor:pointer;color:var(--text-3);font-size:13px">
                                                <i class="fas fa-ellipsis-vertical"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6"
                                            style="text-align:center;padding:2rem;color:var(--text-3);font-size:12px">
                                            No transactions yet today.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
            {{-- /col-left --}}

            {{-- ══ RIGHT COLUMN ══ --}}
            <div class="col-right">

                {{-- SHIFT SUMMARY --}}
                <div class="card">
                    <div class="card-head">
                        <div class="card-head-title">
                            <i class="fas fa-clock"></i> Shift Summary
                        </div>
                        <span class="card-head-badge"
                            style="color:var(--green);border-color:rgba(34,197,94,.2);background:var(--green-dim)">
                            Active
                        </span>
                    </div>
                    <div class="shift-card">
                        <div class="shift-stats">
                            <div class="shift-stat">
                                <div class="shift-stat-label">Sales Today</div>
                                <div class="shift-stat-val" style="color:var(--gold)">Af {{ number_format($todaySales) }}
                                </div>
                            </div>
                            <div class="shift-stat">
                                <div class="shift-stat-label">Customers</div>
                                <div class="shift-stat-val">{{ $todaysCustomers }}</div>
                            </div>
                            <div class="shift-stat">
                                <div class="shift-stat-label">Net Profit</div>
                                <div class="shift-stat-val" style="color:var(--green)">Af
                                    {{ number_format($netProfitToday) }}</div>
                            </div>
                            <div class="shift-stat">
                                <div class="shift-stat-label">Active Loans</div>
                                <div class="shift-stat-val" style="color:var(--amber)">Af {{ number_format($loanToday) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- LOW STOCK ALERTS --}}
                <div class="card">
                    <div class="card-head">
                        <div class="card-head-title">
                            <i class="fas fa-triangle-exclamation" style="color:var(--red)"></i>
                            Low Stock Alerts
                        </div>
                        <span class="card-head-badge"
                            style="color:var(--red);border-color:rgba(239,68,68,.2);background:var(--red-dim)">
                            {{ $lowStock->count() }} item(s)
                        </span>
                    </div>
                    <div class="stock-list">
                        @forelse($lowStock as $stock)
                            @php
                                $pct = min(
                                    100,
                                    ($stock->stock_quantity / max(1, $stock->low_stock_threshold ?? 10)) * 100,
                                );
                            @endphp
                            <div class="stock-item">
                                <div>
                                    <div class="stock-sku">{{ $stock->sku }}</div>
                                    <div class="stock-min">Min: {{ $stock->low_stock_threshold ?? 10 }}</div>
                                    <div class="stock-bar">
                                        <div class="stock-bar-fill" style="width: {{ $pct }}%"></div>
                                    </div>
                                </div>
                                <div class="stock-qty">{{ $stock->stock_quantity }}</div>
                            </div>
                        @empty
                            <div class="empty-state" style="padding:1rem">
                                <i class="fas fa-check-circle" style="color:var(--green)"></i>
                                All stock levels healthy
                            </div>
                        @endforelse
                    </div>
                    @if ($lowStock->count())
                        <button class="btn-purchase">
                            <i class="fas fa-truck-fast" style="margin-right:6px"></i>
                            Create Purchase Order
                        </button>
                    @endif
                </div>

                {{-- HARDWARE STATUS --}}
                <div class="card">
                    <div class="card-head">
                        <div class="card-head-title">
                            <i class="fas fa-microchip"></i> Hardware Status
                        </div>
                        <span class="card-head-badge">Live</span>
                    </div>
                    <div class="hw-list">
                        <div class="hw-item">
                            <div class="hw-left">
                                <div class="hw-icon ok"><i class="fas fa-print"></i></div>
                                <div>
                                    <div class="hw-name">Receipt Printer</div>
                                    <div class="hw-time">Checked 2m ago</div>
                                </div>
                            </div>
                            <div class="hw-dot ok"></div>
                        </div>
                        <div class="hw-item">
                            <div class="hw-left">
                                <div class="hw-icon ok"><i class="fas fa-barcode"></i></div>
                                <div>
                                    <div class="hw-name">Barcode Scanner</div>
                                    <div class="hw-time">Checked 1h ago</div>
                                </div>
                            </div>
                            <div class="hw-dot ok"></div>
                        </div>
                        <div class="hw-item">
                            <div class="hw-left">
                                <div class="hw-icon warn"><i class="fas fa-cash-register"></i></div>
                                <div>
                                    <div class="hw-name">Cash Drawer</div>
                                    <div class="hw-time">Checked 10:00 AM</div>
                                </div>
                            </div>
                            <div class="hw-dot warn"></div>
                        </div>
                        <div class="hw-item">
                            <div class="hw-left">
                                <div class="hw-icon ok"><i class="fas fa-credit-card"></i></div>
                                <div>
                                    <div class="hw-name">Card Terminal</div>
                                    <div class="hw-time">Checked 5m ago</div>
                                </div>
                            </div>
                            <div class="hw-dot ok"></div>
                        </div>
                    </div>
                </div>

            </div>
            {{-- /col-right --}}

        </div>
        {{-- /dash-body --}}

    </div>
    {{-- /dash-root --}}
@endsection

@push('scripts')
    <script>
        /* ── Live clock ── */
        (function tick() {
            const el = document.getElementById('liveClock');
            if (el) {
                const now = new Date();
                el.textContent = now.toLocaleTimeString('en-GB');
            }
            setTimeout(tick, 1000);
        })();

        /* ── Cart System ── */
        function cartSystem() {
            return {
                query: '',
                searchResults: [],
                trendingProducts: [],
                trendingLoading: true,
                cart: [],
                searching: false,

                urls: {
                    search: '{{ route('pos.products.search') }}',
                    trending: '{{ route('pos.products.trending') }}',
                    checkout: '{{ route('pos.checkout') }}',
                    csrf: '{{ csrf_token() }}'
                },

                get cartTotal() {
                    return this.cart.reduce((s, i) => s + i.price * i.qty, 0);
                },

                async init() {
                    await this.loadTrending();
                },

                async loadTrending() {
                    this.trendingLoading = true;
                    try {
                        const r = await fetch(this.urls.trending, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        this.trendingProducts = await r.json();
                    } catch (e) {
                        console.error('Trending failed:', e);
                    } finally {
                        this.trendingLoading = false;
                    }
                },

                async search() {
                    const q = this.query.trim();
                    if (!q) {
                        this.searchResults = [];
                        return;
                    }
                    this.searching = true;
                    this.searchResults = [];
                    try {
                        const r = await fetch(this.urls.search + '?q=' + encodeURIComponent(q), {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        this.searchResults = await r.json();
                    } catch (e) {
                        console.error('Search failed:', e);
                    } finally {
                        this.searching = false;
                    }
                },

                clearSearch() {
                    this.query = '';
                    this.searchResults = [];
                    this.searching = false;
                },

                addToCart(p) {
                    const ex = this.cart.find(i => i.variant_id === p.variant_id);
                    if (ex) {
                        if (ex.qty < ex.stock_quantity) ex.qty++;
                    } else {
                        this.cart.push({
                            ...p,
                            qty: 1
                        });
                    }
                    this.clearSearch();
                },

                incQty(idx) {
                    const i = this.cart[idx];
                    if (i.qty < i.stock_quantity) i.qty++;
                },

                decQty(idx) {
                    if (this.cart[idx].qty > 1) this.cart[idx].qty--;
                    else this.removeFromCart(idx);
                },

                removeFromCart(idx) {
                    this.cart.splice(idx, 1);
                },

                clearCart() {
                    if (confirm('Clear entire cart?')) this.cart = [];
                },

                checkout() {
                    if (!this.cart.length) return;
                    const f = document.createElement('form');
                    f.method = 'POST';
                    f.action = this.urls.checkout;
                    f.innerHTML = `
                <input type="hidden" name="_token" value="${this.urls.csrf}">
                <input type="hidden" name="cart"   value='${JSON.stringify(this.cart)}'>
            `;
                    document.body.appendChild(f);
                    f.submit();
                },

                fmt(n) {
                    return Number(n || 0).toLocaleString('en-US', {
                        minimumFractionDigits: 0
                    });
                }
            }
        }
    </script>
@endpush
