@extends('layouts.app')

@push('styles')
    <link
        href="https://fonts.googleapis.com/css2?family=DM+Serif+Text:ital@0;1&family=Manrope:wght@300;400;500;600;700;800&family=Space+Mono:wght@400;700&display=swap"
        rel="stylesheet">
    <style>
        /* ══════════════════════════════════════
       TOKENS
    ══════════════════════════════════════ */
        :root {
            --bg: #eef0f7;
            --surface: #ffffff;
            --s2: #f5f6fc;
            --s3: #eceff6;
            --border: #dde1ef;
            --border2: #c3cade;
            --ink: #181b2c;
            --ink2: #40456a;
            --ink3: #7e85a4;
            --ink4: #bcc2d8;
            --blue: #2557e8;
            --blue2: #1c46cc;
            --bdim: rgba(37, 87, 232, .08);
            --bmid: rgba(37, 87, 232, .16);
            --green: #16a34a;
            --gdim: rgba(22, 163, 74, .09);
            --red: #dc2626;
            --rdim: rgba(220, 38, 38, .08);
            --amber: #d97706;
            --adim: rgba(217, 119, 6, .09);
            --violet: #7c3aed;
            --vdim: rgba(124, 58, 237, .09);
            --teal: #0891b2;
            --tdim: rgba(8, 145, 178, .09);
            --navy: #1e2d52;
            --mono: 'Space Mono', monospace;
            --body: 'Manrope', sans-serif;
            --display: 'DM Serif Text', serif;
            --r: 10px;
            --rsm: 6px;
            --rlg: 16px;
            --sh: 0 1px 3px rgba(0, 0, 0, .05), 0 1px 2px rgba(0, 0, 0, .03);
            --shmd: 0 4px 18px rgba(0, 0, 0, .08), 0 2px 6px rgba(0, 0, 0, .04);
            --shlg: 0 20px 56px rgba(0, 0, 0, .12), 0 6px 16px rgba(0, 0, 0, .06);
        }

        /* ══════════════════════════════════════
       BASE
    ══════════════════════════════════════ */
        .bk * {
            box-sizing: border-box;
        }

        .bk {
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
        .bk-top {
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

        .bk-title {
            font-family: var(--display);
            font-size: 22px;
            color: var(--ink);
            letter-spacing: -.2px;
        }

        .bk-title em {
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
            padding: 8px 16px;
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
            box-shadow: 0 2px 8px rgba(37, 87, 232, .25);
        }

        .btn-primary:hover {
            background: var(--blue2);
            transform: translateY(-1px);
            box-shadow: 0 5px 16px rgba(37, 87, 232, .35);
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

        .btn-green {
            background: var(--gdim);
            border: 1px solid rgba(22, 163, 74, .2);
            color: var(--green);
        }

        .btn-green:hover {
            background: var(--green);
            color: #fff;
        }

        .btn-amber {
            background: var(--adim);
            border: 1px solid rgba(217, 119, 6, .2);
            color: var(--amber);
        }

        .btn-amber:hover {
            background: var(--amber);
            color: #fff;
        }

        .btn-sm {
            padding: 5px 11px;
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
       BODY LAYOUT
    ══════════════════════════════════════ */
        .bk-body {
            padding: 1.5rem 1.75rem 3rem;
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        /* ══════════════════════════════════════
       STATUS CARDS
    ══════════════════════════════════════ */
        .status-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
        }

        .status-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--r);
            padding: 1.1rem 1.25rem;
            position: relative;
            overflow: hidden;
            transition: all .2s;
        }

        .status-card:hover {
            box-shadow: var(--shmd);
            transform: translateY(-2px);
            border-color: var(--border2);
        }

        .status-card::before {
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

        .status-card:hover::before {
            transform: scaleX(1);
        }

        .sc-label {
            font-size: 10px;
            font-weight: 700;
            color: var(--ink3);
            text-transform: uppercase;
            letter-spacing: .1em;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .sc-val {
            font-family: var(--mono);
            font-size: 20px;
            font-weight: 700;
            color: var(--ink);
            line-height: 1;
            letter-spacing: -.3px;
        }

        .sc-val.sm {
            font-size: 15px;
        }

        .sc-sub {
            font-size: 11px;
            color: var(--ink3);
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .status-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .dot-green {
            background: var(--green);
            box-shadow: 0 0 5px var(--green);
            animation: pulse-dot 2s infinite;
        }

        .dot-amber {
            background: var(--amber);
        }

        .dot-red {
            background: var(--red);
        }

        .dot-gray {
            background: var(--ink4);
        }

        @keyframes pulse-dot {

            0%,
            100% {
                opacity: 1
            }

            50% {
                opacity: .4
            }
        }

        /* ══════════════════════════════════════
       CARDS
    ══════════════════════════════════════ */
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--r);
            overflow: hidden;
            box-shadow: var(--sh);
            transition: all .2s;
        }

        .card:hover {
            border-color: var(--border2);
        }

        .card-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: .9rem 1.4rem;
            border-bottom: 1px solid var(--border);
        }

        .card-title {
            font-size: 13px;
            font-weight: 700;
            color: var(--ink);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .card-title i {
            color: var(--blue);
            font-size: 13px;
        }

        .card-body {
            padding: 1.25rem 1.4rem;
        }

        /* ══════════════════════════════════════
       TWO-COL GRID
    ══════════════════════════════════════ */
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.25rem;
        }

        .grid-3-2 {
            display: grid;
            grid-template-columns: 1.4fr 1fr;
            gap: 1.25rem;
        }

        /* ══════════════════════════════════════
       BACKUP NOW CARD (hero)
    ══════════════════════════════════════ */
        .backup-hero {
            background: linear-gradient(135deg, var(--navy) 0%, #162040 100%);
            border: 1px solid rgba(255, 255, 255, .07);
            border-radius: var(--r);
            padding: 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 2rem;
            box-shadow: var(--shmd);
        }

        .bh-left {
            flex: 1;
        }

        .bh-title {
            font-family: var(--display);
            font-size: 24px;
            color: #fff;
            margin-bottom: 6px;
        }

        .bh-sub {
            font-size: 13px;
            color: rgba(255, 255, 255, .5);
            line-height: 1.6;
            max-width: 480px;
        }

        .bh-meta {
            display: flex;
            gap: 1.5rem;
            margin-top: 1rem;
            flex-wrap: wrap;
        }

        .bh-meta-item {
            font-size: 12px;
            color: rgba(255, 255, 255, .4);
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .bh-meta-item strong {
            color: rgba(255, 255, 255, .8);
            font-family: var(--mono);
        }

        .bh-right {
            display: flex;
            flex-direction: column;
            gap: 10px;
            flex-shrink: 0;
        }

        .btn-backup-now {
            padding: 14px 32px;
            background: var(--blue);
            color: #fff;
            border: none;
            border-radius: var(--rsm);
            font-family: var(--body);
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: all .2s;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 18px rgba(37, 87, 232, .4);
        }

        .btn-backup-now:hover:not(:disabled) {
            background: var(--blue2);
            transform: translateY(-1px);
            box-shadow: 0 8px 24px rgba(37, 87, 232, .5);
        }

        .btn-backup-now:disabled {
            opacity: .5;
            cursor: not-allowed;
        }

        /* progress bar */
        .backup-progress {
            margin-top: 1.25rem;
        }

        .bp-label {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            color: rgba(255, 255, 255, .5);
            margin-bottom: 8px;
        }

        .bp-label strong {
            color: rgba(255, 255, 255, .85);
            font-family: var(--mono);
        }

        .bp-bar {
            height: 6px;
            background: rgba(255, 255, 255, .1);
            border-radius: 3px;
            overflow: hidden;
        }

        .bp-fill {
            height: 100%;
            border-radius: 3px;
            background: var(--blue);
            transition: width .4s ease;
        }

        .bp-fill.success {
            background: var(--green);
        }

        .bp-fill.error {
            background: var(--red);
        }

        .bp-steps {
            display: flex;
            gap: .75rem;
            margin-top: .75rem;
            flex-wrap: wrap;
        }

        .bp-step {
            font-size: 11px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .bp-step.done {
            color: #4ade80;
        }

        .bp-step.active {
            color: #60a5fa;
        }

        .bp-step.pending {
            color: rgba(255, 255, 255, .3);
        }

        .bp-step.failed {
            color: #f87171;
        }

        /* ══════════════════════════════════════
       BACKUP HISTORY TABLE
    ══════════════════════════════════════ */
        .bk-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .bk-table thead {
            background: var(--s2);
            border-bottom: 1.5px solid var(--border);
        }

        .bk-table th {
            padding: 9px 14px;
            text-align: left;
            font-size: 10px;
            font-weight: 700;
            color: var(--ink3);
            text-transform: uppercase;
            letter-spacing: .08em;
            white-space: nowrap;
        }

        .bk-table td {
            padding: 11px 14px;
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
        }

        .bk-table tbody tr:last-child td {
            border-bottom: none;
        }

        .bk-table tbody tr:hover {
            background: var(--bdim);
        }

        .bk-file {
            font-family: var(--mono);
            font-size: 12px;
            color: var(--ink);
        }

        .bk-size {
            font-family: var(--mono);
            font-size: 12px;
            color: var(--ink3);
        }

        .bk-date {
            font-family: var(--mono);
            font-size: 12px;
            color: var(--ink2);
        }

        .row-acts {
            display: flex;
            gap: 5px;
        }

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
            border: 1px solid rgba(22, 163, 74, .2);
        }

        .pill-blue {
            background: var(--bdim);
            color: var(--blue);
            border: 1px solid var(--bmid);
        }

        .pill-amber {
            background: var(--adim);
            color: var(--amber);
            border: 1px solid rgba(217, 119, 6, .2);
        }

        .pill-red {
            background: var(--rdim);
            color: var(--red);
            border: 1px solid rgba(220, 38, 38, .2);
        }

        .pill-gray {
            background: var(--s3);
            color: var(--ink3);
            border: 1px solid var(--border);
        }

        /* ══════════════════════════════════════
       SYNC STATUS TABLE
    ══════════════════════════════════════ */
        .sync-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: .9rem 1rem;
            background: var(--s2);
            border: 1px solid var(--border);
            border-radius: var(--rsm);
            margin-bottom: 8px;
            transition: border-color .15s;
        }

        .sync-row:hover {
            border-color: var(--border2);
        }

        .sync-row:last-child {
            margin-bottom: 0;
        }

        .sr-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sr-icon {
            width: 36px;
            height: 36px;
            border-radius: var(--rsm);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            flex-shrink: 0;
        }

        .sr-name {
            font-size: 13px;
            font-weight: 600;
            color: var(--ink);
        }

        .sr-count {
            font-size: 11px;
            color: var(--ink3);
            margin-top: 2px;
        }

        .sr-right {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sr-pending {
            font-family: var(--mono);
            font-size: 14px;
            font-weight: 700;
        }

        .sr-pending.has {
            color: var(--amber);
        }

        .sr-pending.none {
            color: var(--green);
        }

        /* ══════════════════════════════════════
       SCHEDULE SETTINGS
    ══════════════════════════════════════ */
        .schedule-grid {
            display: flex;
            flex-direction: column;
            gap: .75rem;
        }

        .schedule-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: .85rem 1rem;
            background: var(--s2);
            border: 1px solid var(--border);
            border-radius: var(--rsm);
            transition: background .12s;
        }

        .schedule-item:hover {
            background: var(--s3);
        }

        .si-label {
            font-size: 13px;
            font-weight: 500;
            color: var(--ink);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .si-label i {
            color: var(--blue);
            width: 16px;
            text-align: center;
        }

        .si-sub {
            font-size: 11px;
            color: var(--ink3);
            margin-top: 2px;
        }

        .si-right {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Toggle switch */
        .toggle {
            position: relative;
            width: 38px;
            height: 22px;
            flex-shrink: 0;
        }

        .toggle input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            inset: 0;
            background: var(--border2);
            border-radius: 11px;
            cursor: pointer;
            transition: background .2s;
        }

        .toggle-slider::before {
            content: '';
            position: absolute;
            width: 16px;
            height: 16px;
            background: #fff;
            border-radius: 50%;
            top: 3px;
            left: 3px;
            transition: transform .2s;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .2);
        }

        .toggle input:checked+.toggle-slider {
            background: var(--blue);
        }

        .toggle input:checked+.toggle-slider::before {
            transform: translateX(16px);
        }

        /* select input */
        .f-sel {
            padding: 7px 12px;
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
       CLOUD CONFIG CARD
    ══════════════════════════════════════ */
        .cloud-providers {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-bottom: 1rem;
        }

        .cloud-btn {
            padding: 12px;
            border: 2px solid var(--border);
            border-radius: var(--r);
            background: var(--s2);
            cursor: pointer;
            transition: all .18s;
            text-align: center;
            font-family: var(--body);
        }

        .cloud-btn:hover {
            border-color: var(--blue);
            background: var(--bdim);
        }

        .cloud-btn.active {
            border-color: var(--blue);
            background: var(--bdim);
            box-shadow: 0 0 0 3px var(--bdim);
        }

        .cloud-btn-icon {
            font-size: 22px;
            margin-bottom: 5px;
            display: block;
        }

        .cloud-btn-label {
            font-size: 12px;
            font-weight: 600;
            color: var(--ink);
        }

        .cloud-btn-sub {
            font-size: 10px;
            color: var(--ink3);
            margin-top: 2px;
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

        .form-grid {
            display: grid;
            gap: .85rem;
        }

        .form-2 {
            grid-template-columns: 1fr 1fr;
        }

        .field-hint {
            font-size: 11px;
            color: var(--ink3);
            margin-top: 3px;
        }

        .config-env {
            background: var(--navy);
            border-radius: var(--rsm);
            padding: 1rem;
            margin-top: .75rem;
        }

        .env-title {
            font-size: 10px;
            font-weight: 700;
            color: rgba(255, 255, 255, .4);
            text-transform: uppercase;
            letter-spacing: .08em;
            margin-bottom: 8px;
        }

        .env-line {
            font-family: var(--mono);
            font-size: 12px;
            color: #86efac;
            margin-bottom: 3px;
        }

        .env-line span {
            color: rgba(255, 255, 255, .4);
        }

        /* ══════════════════════════════════════
       RESTORE MODAL
    ══════════════════════════════════════ */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(24, 27, 44, .5);
            backdrop-filter: blur(6px);
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
            max-width: 500px;
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

        .warn-box {
            padding: 12px 14px;
            background: var(--rdim);
            border: 1px solid rgba(220, 38, 38, .22);
            border-radius: var(--rsm);
            font-size: 12px;
            color: var(--red);
            display: flex;
            gap: 8px;
            margin-bottom: 1rem;
            line-height: 1.5;
        }

        .restore-file-info {
            padding: 12px 14px;
            background: var(--s2);
            border: 1px solid var(--border);
            border-radius: var(--rsm);
            margin-bottom: 1rem;
        }

        .rfi-row {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            margin-bottom: 4px;
            color: var(--ink2);
        }

        .rfi-row:last-child {
            margin-bottom: 0;
        }

        .rfi-val {
            font-family: var(--mono);
            font-weight: 500;
        }

        /* ══════════════════════════════════════
       LOG
    ══════════════════════════════════════ */
        .log-list {
            display: flex;
            flex-direction: column;
            gap: 6px;
            max-height: 280px;
            overflow-y: auto;
        }

        .log-list::-webkit-scrollbar {
            width: 4px;
        }

        .log-list::-webkit-scrollbar-thumb {
            background: var(--border);
            border-radius: 2px;
        }

        .log-item {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 9px 12px;
            background: var(--s2);
            border: 1px solid var(--border);
            border-radius: var(--rsm);
            font-size: 12px;
        }

        .log-item.success {
            border-left: 3px solid var(--green);
        }

        .log-item.error {
            border-left: 3px solid var(--red);
        }

        .log-item.info {
            border-left: 3px solid var(--blue);
        }

        .log-item.warning {
            border-left: 3px solid var(--amber);
        }

        .log-icon {
            font-size: 13px;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .log-item.success .log-icon {
            color: var(--green);
        }

        .log-item.error .log-icon {
            color: var(--red);
        }

        .log-item.info .log-icon {
            color: var(--blue);
        }

        .log-item.warning .log-icon {
            color: var(--amber);
        }

        .log-text {
            flex: 1;
            color: var(--ink2);
            line-height: 1.5;
        }

        .log-time {
            font-family: var(--mono);
            font-size: 10px;
            color: var(--ink4);
            flex-shrink: 0;
        }

        /* disk gauge */
        .disk-gauge {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .dg-row {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            margin-bottom: 4px;
            color: var(--ink2);
        }

        .dg-bar {
            height: 8px;
            background: var(--s3);
            border-radius: 4px;
            overflow: hidden;
        }

        .dg-fill {
            height: 100%;
            border-radius: 4px;
            transition: width .6s;
        }

        .dg-legend {
            display: flex;
            gap: 1rem;
            margin-top: 6px;
        }

        .dg-leg {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
            color: var(--ink3);
        }

        .dg-dot {
            width: 8px;
            height: 8px;
            border-radius: 2px;
        }

        /* empty state */
        .empty-state {
            text-align: center;
            padding: 3rem 2rem;
            color: var(--ink3);
        }

        .empty-state i {
            font-size: 32px;
            margin-bottom: 10px;
            display: block;
            color: var(--ink4);
        }

        .empty-state p {
            font-size: 13px;
            line-height: 1.6;
        }
    </style>
@endpush

@section('content')
    <div class="bk" x-data="backupPage()" x-init="init()">

        {{-- ════ TOPBAR ════ --}}
        <div class="bk-top">
            <div class="bk-title">Afghan <em>POS</em> — Backup & Sync</div>
            <div class="top-r">
                <button class="btn btn-ghost" @click="refreshAll()">
                    <i class="fas fa-rotate" :class="refreshing ? 'fa-spin' : ''"></i> Refresh
                </button>
            </div>
        </div>

        <div class="bk-body">

            {{-- ════ STATUS CARDS ════ --}}
            <div class="status-grid">
                <div class="status-card" style="--ac:var(--green)">
                    <div class="sc-label">Last Backup <span><i class="fas fa-clock" style="color:var(--green)"></i></span>
                    </div>
                    <div class="sc-val sm" x-text="status.last_backup || 'Never'"></div>
                    <div class="sc-sub">
                        <span class="status-dot" :class="status.last_backup ? 'dot-green' : 'dot-gray'"></span>
                        <span
                            x-text="status.last_backup_size ? status.last_backup_size + ' file size' : 'No backups yet'"></span>
                    </div>
                </div>
                <div class="status-card" style="--ac:var(--blue)">
                    <div class="sc-label">Cloud Sync <span><i class="fas fa-cloud" style="color:var(--blue)"></i></span>
                    </div>
                    <div class="sc-val sm" x-text="status.cloud_status || 'Not configured'"></div>
                    <div class="sc-sub">
                        <span class="status-dot" :class="status.cloud_enabled ? 'dot-green' : 'dot-gray'"></span>
                        <span x-text="status.cloud_provider || 'No provider set'"></span>
                    </div>
                </div>
                <div class="status-card" style="--ac:var(--amber)">
                    <div class="sc-label">Pending Sync <span><i class="fas fa-rotate" style="color:var(--amber)"></i></span>
                    </div>
                    <div class="sc-val" style="color:var(--amber)" x-text="status.total_pending || 0"></div>
                    <div class="sc-sub">
                        <span class="status-dot" :class="(status.total_pending || 0) > 0 ? 'dot-amber' : 'dot-green'"></span>
                        <span
                            x-text="(status.total_pending||0) > 0 ? 'records awaiting sync' : 'all records synced'"></span>
                    </div>
                </div>
                <div class="status-card" style="--ac:var(--violet)">
                    <div class="sc-label">Disk Usage <span><i class="fas fa-hard-drive"
                                style="color:var(--violet)"></i></span></div>
                    <div class="sc-val sm" x-text="status.disk_used || '—'"></div>
                    <div class="sc-sub">
                        <span class="status-dot" :class="(status.disk_pct || 0) > 85 ? 'dot-red' : 'dot-green'"></span>
                        <span x-text="(status.disk_pct||0) + '% of ' + (status.disk_total||'—') + ' used'"></span>
                    </div>
                </div>
            </div>

            {{-- ════ BACKUP NOW HERO ════ --}}
            <div class="backup-hero">
                <div class="bh-left">
                    <div class="bh-title">Run a Backup</div>
                    <div class="bh-sub">
                        Creates a compressed archive of your entire database and stores it locally.
                        If cloud sync is configured, it will also upload to your cloud provider.
                    </div>
                    <div class="bh-meta">
                        <div class="bh-meta-item">
                            <i class="fas fa-database"></i>
                            <span>Database: <strong x-text="status.db_name || 'afghan_pos'"></strong></span>
                        </div>
                        <div class="bh-meta-item">
                            <i class="fas fa-folder"></i>
                            <span>Stored in: <strong x-text="status.backup_path || 'storage/backups'"></strong></span>
                        </div>
                        <div class="bh-meta-item">
                            <i class="fas fa-shield"></i>
                            <span>Encrypted: <strong x-text="status.encrypted ? 'Yes' : 'No'"></strong></span>
                        </div>
                    </div>

                    {{-- Progress --}}
                    <div class="backup-progress" x-show="backupRunning || backupDone" x-cloak>
                        <div class="bp-label">
                            <span x-text="backupStepLabel"></span>
                            <strong x-text="backupPct + '%'"></strong>
                        </div>
                        <div class="bp-bar">
                            <div class="bp-fill" :class="backupFailed ? 'error' : backupDone ? 'success' : ''"
                                :style="`width:${backupPct}%`"></div>
                        </div>
                        <div class="bp-steps">
                            <template x-for="step in backupSteps" :key="step.label">
                                <div class="bp-step" :class="step.state">
                                    <i
                                        :class="{
                                            'fas fa-check-circle': step.state==='done',
                                            'fas fa-circle-notch fa-spin': step.state==='active',
                                            'fas fa-circle': step.state==='pending',
                                            'fas fa-times-circle': step.state==='failed',
                                        }"></i>
                                    <span x-text="step.label"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="bh-right">
                    <button type="button" class="btn-backup-now" @click="runBackup()" :disabled="backupRunning">
                        <template x-if="!backupRunning">
                            <span style="display:flex;align-items:center;gap:8px">
                                <i class="fas fa-cloud-arrow-up"></i> Backup Now
                            </span>
                        </template>
                        <template x-if="backupRunning">
                            <span style="display:flex;align-items:center;gap:8px">
                                <i class="fas fa-spinner fa-spin"></i> Running…
                            </span>
                        </template>
                    </button>
                    <button type="button" class="btn btn-ghost" style="justify-content:center" @click="runSync()"
                        :disabled="syncRunning">
                        <i class="fas fa-rotate" :class="syncRunning ? 'fa-spin' : ''"></i>
                        <span x-text="syncRunning?'Syncing…':'Sync Records'"></span>
                    </button>
                </div>
            </div>

            <div class="grid-3-2">

                {{-- ════ LEFT COL ════ --}}
                <div style="display:flex;flex-direction:column;gap:1.25rem">

                    {{-- BACKUP HISTORY --}}
                    <div class="card">
                        <div class="card-head">
                            <div class="card-title"><i class="fas fa-history"></i> Backup History</div>
                            <span style="font-size:11px;color:var(--ink3)"
                                x-text="backups.length + ' backup(s) stored'"></span>
                        </div>
                        <div x-show="backupsLoading" style="text-align:center;padding:2rem;color:var(--ink3)">
                            <i class="fas fa-spinner fa-spin" style="font-size:18px"></i>
                        </div>
                        <div x-show="!backupsLoading">
                            <div class="empty-state" x-show="backups.length===0">
                                <i class="fas fa-folder-open"></i>
                                <p>No backups yet.<br>Run your first backup above.</p>
                            </div>
                            <div x-show="backups.length>0" style="overflow-x:auto">
                                <table class="bk-table">
                                    <thead>
                                        <tr>
                                            <th>File Name</th>
                                            <th>Size</th>
                                            <th>Created</th>
                                            <th>Type</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="b in backups" :key="b.path">
                                            <tr>
                                                <td><span class="bk-file" x-text="b.name"></span></td>
                                                <td><span class="bk-size" x-text="b.size"></span></td>
                                                <td><span class="bk-date" x-text="b.created_at"></span></td>
                                                <td>
                                                    <span class="pill" :class="b.cloud ? 'pill-blue' : 'pill-gray'"
                                                        x-text="b.cloud ? 'Cloud + Local' : 'Local'"></span>
                                                </td>
                                                <td>
                                                    <div class="row-acts">
                                                        <button type="button" class="btn btn-ghost btn-sm"
                                                            @click="downloadBackup(b)" title="Download">
                                                            <i class="fas fa-download"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-green btn-sm"
                                                            @click="openRestoreModal(b)" title="Restore">
                                                            <i class="fas fa-rotate-left"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-danger btn-sm"
                                                            @click="deleteBackup(b)" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- SYNC STATUS --}}
                    <div class="card">
                        <div class="card-head">
                            <div class="card-title"><i class="fas fa-rotate"></i> Sync Status by Table</div>
                            <button type="button" class="btn btn-ghost btn-sm" @click="runSync()"
                                :disabled="syncRunning">
                                <i class="fas fa-rotate" :class="syncRunning ? 'fa-spin' : ''"></i>
                                <span x-text="syncRunning?'Syncing…':'Sync All'"></span>
                            </button>
                        </div>
                        <div class="card-body">
                            <template x-for="table in syncTables" :key="table.name">
                                <div class="sync-row">
                                    <div class="sr-left">
                                        <div class="sr-icon" :style="`background:${table.color}20;color:${table.color}`">
                                            <i :class="table.icon"></i>
                                        </div>
                                        <div>
                                            <div class="sr-name" x-text="table.label"></div>
                                            <div class="sr-count" x-text="table.total + ' total records'"></div>
                                        </div>
                                    </div>
                                    <div class="sr-right">
                                        <span class="pill" :class="table.failed > 0 ? 'pill-red' : 'pill-gray'"
                                            x-show="table.failed > 0" x-text="table.failed + ' failed'"></span>
                                        <div style="text-align:right">
                                            <div class="sr-pending" :class="table.pending > 0 ? 'has' : 'none'"
                                                x-text="table.pending > 0 ? table.pending + ' pending' : '✓ Synced'"></div>
                                        </div>
                                        <button type="button" class="btn btn-ghost btn-sm"
                                            x-show="table.pending > 0 || table.failed > 0" @click="syncTable(table.name)">
                                            <i class="fas fa-rotate"></i>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- BACKUP LOG --}}
                    <div class="card">
                        <div class="card-head">
                            <div class="card-title"><i class="fas fa-terminal"></i> Activity Log</div>
                            <button type="button" class="btn btn-ghost btn-sm" @click="clearLog()">
                                <i class="fas fa-trash"></i> Clear
                            </button>
                        </div>
                        <div class="card-body">
                            <div x-show="logs.length===0" class="empty-state" style="padding:1.5rem">
                                <i class="fas fa-file-lines" style="font-size:24px"></i>
                                <p>No activity yet.</p>
                            </div>
                            <div class="log-list">
                                <template x-for="(log, idx) in logs" :key="idx">
                                    <div class="log-item" :class="log.type">
                                        <i class="log-icon"
                                            :class="{
                                                'fas fa-check-circle': log.type==='success',
                                                'fas fa-times-circle': log.type==='error',
                                                'fas fa-info-circle': log.type==='info',
                                                'fas fa-triangle-exclamation': log.type==='warning',
                                            }"></i>
                                        <span class="log-text" x-text="log.message"></span>
                                        <span class="log-time" x-text="log.time"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- ════ RIGHT COL ════ --}}
                <div style="display:flex;flex-direction:column;gap:1.25rem">

                    {{-- SCHEDULE --}}
                    <div class="card">
                        <div class="card-head">
                            <div class="card-title"><i class="fas fa-clock"></i> Backup Schedule</div>
                            <button type="button" class="btn btn-primary btn-sm" @click="saveSchedule()">
                                <i class="fas fa-floppy-disk"></i> Save
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="schedule-grid">
                                <div class="schedule-item">
                                    <div>
                                        <div class="si-label"><i class="fas fa-clock"></i> Daily Backup</div>
                                        <div class="si-sub">Runs every day at set time</div>
                                    </div>
                                    <div class="si-right">
                                        <select class="f-sel" x-model="schedule.daily_time" style="width:100px">
                                            <option value="00:00">Midnight</option>
                                            <option value="02:00">2:00 AM</option>
                                            <option value="06:00">6:00 AM</option>
                                            <option value="12:00">Noon</option>
                                            <option value="22:00">10:00 PM</option>
                                            <option value="23:00">11:00 PM</option>
                                        </select>
                                        <label class="toggle">
                                            <input type="checkbox" x-model="schedule.daily_enabled">
                                            <span class="toggle-slider"></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="schedule-item">
                                    <div>
                                        <div class="si-label"><i class="fas fa-calendar-week"></i> Weekly Backup</div>
                                        <div class="si-sub">Full backup every week</div>
                                    </div>
                                    <label class="toggle">
                                        <input type="checkbox" x-model="schedule.weekly_enabled">
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                                <div class="schedule-item">
                                    <div>
                                        <div class="si-label"><i class="fas fa-cloud-arrow-up"></i> Auto Cloud Upload
                                        </div>
                                        <div class="si-sub">Upload after each backup</div>
                                    </div>
                                    <label class="toggle">
                                        <input type="checkbox" x-model="schedule.auto_cloud">
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                                <div class="schedule-item">
                                    <div>
                                        <div class="si-label"><i class="fas fa-broom"></i> Auto Cleanup</div>
                                        <div class="si-sub">Keep only last N backups</div>
                                    </div>
                                    <div class="si-right">
                                        <select class="f-sel" x-model="schedule.keep_count" style="width:80px">
                                            <option value="5">5</option>
                                            <option value="10">10</option>
                                            <option value="30">30</option>
                                            <option value="0">All</option>
                                        </select>
                                        <label class="toggle">
                                            <input type="checkbox" x-model="schedule.cleanup_enabled">
                                            <span class="toggle-slider"></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="schedule-item">
                                    <div>
                                        <div class="si-label"><i class="fas fa-lock"></i> Encrypt Backups</div>
                                        <div class="si-sub">AES-256 encryption</div>
                                    </div>
                                    <label class="toggle">
                                        <input type="checkbox" x-model="schedule.encrypt">
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- DISK USAGE --}}
                    <div class="card">
                        <div class="card-head">
                            <div class="card-title"><i class="fas fa-hard-drive"></i> Storage</div>
                        </div>
                        <div class="card-body">
                            <div class="disk-gauge">
                                <div class="dg-row">
                                    <span>Local Disk</span>
                                    <span style="font-family:var(--mono);font-size:12px"
                                        x-text="status.disk_used + ' / ' + status.disk_total"></span>
                                </div>
                                <div class="dg-bar">
                                    <div class="dg-fill"
                                        :style="`width:${status.disk_pct||0}%;background:${(status.disk_pct||0)>85?'var(--red)':(status.disk_pct||0)>60?'var(--amber)':'var(--blue)'}`">
                                    </div>
                                </div>
                                <div class="dg-legend">
                                    <div class="dg-leg">
                                        <div class="dg-dot" style="background:var(--blue)"></div> Used
                                    </div>
                                    <div class="dg-leg">
                                        <div class="dg-dot" style="background:var(--s3)"></div> Free
                                    </div>
                                </div>
                                <div
                                    style="margin-top:.75rem;padding:.75rem;background:var(--s2);border:1px solid var(--border);border-radius:var(--rsm)">
                                    <div
                                        style="display:flex;justify-content:space-between;font-size:12px;color:var(--ink2)">
                                        <span>Backup folder size</span>
                                        <span style="font-family:var(--mono);font-weight:600"
                                            x-text="status.backup_folder_size || '—'"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- CLOUD CONFIG --}}
                    <div class="card">
                        <div class="card-head">
                            <div class="card-title"><i class="fas fa-cloud"></i> Cloud Configuration</div>
                            <button type="button" class="btn btn-primary btn-sm" @click="saveCloudConfig()">
                                <i class="fas fa-floppy-disk"></i> Save
                            </button>
                        </div>
                        <div class="card-body">

                            <div class="cloud-providers">
                                <button type="button" class="cloud-btn"
                                    :class="cloudConfig.provider === 'gdrive' ? 'active' : ''"
                                    @click="cloudConfig.provider='gdrive'">
                                    <span class="cloud-btn-icon">🗂️</span>
                                    <div class="cloud-btn-label">Google Drive</div>
                                    <div class="cloud-btn-sub">Free 15GB</div>
                                </button>
                                <button type="button" class="cloud-btn"
                                    :class="cloudConfig.provider === 'dropbox' ? 'active' : ''"
                                    @click="cloudConfig.provider='dropbox'">
                                    <span class="cloud-btn-icon">📦</span>
                                    <div class="cloud-btn-label">Dropbox</div>
                                    <div class="cloud-btn-sub">Free 2GB</div>
                                </button>
                                <button type="button" class="cloud-btn"
                                    :class="cloudConfig.provider === 'ftp' ? 'active' : ''" @click="cloudConfig.provider='ftp'">
                                    <span class="cloud-btn-icon">🖥️</span>
                                    <div class="cloud-btn-label">FTP Server</div>
                                    <div class="cloud-btn-sub">Custom server</div>
                                </button>
                            </div>

                            {{-- Google Drive fields --}}
                            <div x-show="cloudConfig.provider==='gdrive'" x-cloak>
                                <div class="form-grid">
                                    <div>
                                        <label class="field-label">Service Account JSON Path</label>
                                        <input type="text" class="field-input" x-model="cloudConfig.gdrive_key"
                                            placeholder="storage/google-service-account.json">
                                        <div class="field-hint">Path to your Google service account credentials file</div>
                                    </div>
                                    <div>
                                        <label class="field-label">Drive Folder ID</label>
                                        <input type="text" class="field-input" x-model="cloudConfig.gdrive_folder"
                                            placeholder="1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs">
                                    </div>
                                </div>
                                <div class="config-env" style="margin-top:.75rem">
                                    <div class="env-title">Add to your .env file</div>
                                    <div class="env-line">FILESYSTEM_CLOUD=<span>google</span></div>
                                    <div class="env-line">GOOGLE_DRIVE_CLIENT_ID=<span>your_client_id</span></div>
                                    <div class="env-line">GOOGLE_DRIVE_CLIENT_SECRET=<span>your_secret</span></div>
                                    <div class="env-line">GOOGLE_DRIVE_REFRESH_TOKEN=<span>your_token</span></div>
                                </div>
                            </div>

                            {{-- Dropbox fields --}}
                            <div x-show="cloudConfig.provider==='dropbox'" x-cloak>
                                <div class="form-grid">
                                    <div>
                                        <label class="field-label">Access Token</label>
                                        <input type="password" class="field-input" x-model="cloudConfig.dropbox_token"
                                            placeholder="sl.xxxxxx…">
                                    </div>
                                    <div>
                                        <label class="field-label">Backup Folder Path</label>
                                        <input type="text" class="field-input" x-model="cloudConfig.dropbox_path"
                                            placeholder="/afghan-pos-backups">
                                    </div>
                                </div>
                                <div class="config-env" style="margin-top:.75rem">
                                    <div class="env-title">Add to your .env file</div>
                                    <div class="env-line">FILESYSTEM_CLOUD=<span>dropbox</span></div>
                                    <div class="env-line">DROPBOX_AUTH_TOKEN=<span>your_access_token</span></div>
                                </div>
                            </div>

                            {{-- FTP fields --}}
                            <div x-show="cloudConfig.provider==='ftp'" x-cloak>
                                <div class="form-grid form-2">
                                    <div>
                                        <label class="field-label">Host</label>
                                        <input type="text" class="field-input" x-model="cloudConfig.ftp_host"
                                            placeholder="ftp.example.com">
                                    </div>
                                    <div>
                                        <label class="field-label">Port</label>
                                        <input type="number" class="field-input" x-model="cloudConfig.ftp_port"
                                            placeholder="21">
                                    </div>
                                    <div>
                                        <label class="field-label">Username</label>
                                        <input type="text" class="field-input" x-model="cloudConfig.ftp_user"
                                            placeholder="ftpuser">
                                    </div>
                                    <div>
                                        <label class="field-label">Password</label>
                                        <input type="password" class="field-input" x-model="cloudConfig.ftp_pass"
                                            placeholder="••••••••">
                                    </div>
                                    <div style="grid-column:span 2">
                                        <label class="field-label">Remote Path</label>
                                        <input type="text" class="field-input" x-model="cloudConfig.ftp_path"
                                            placeholder="/backups/afghan-pos">
                                    </div>
                                </div>
                                <div class="config-env" style="margin-top:.75rem">
                                    <div class="env-title">Add to your .env file</div>
                                    <div class="env-line">FILESYSTEM_CLOUD=<span>ftp</span></div>
                                    <div class="env-line">FTP_HOST=<span>your_ftp_host</span></div>
                                    <div class="env-line">FTP_USERNAME=<span>your_username</span></div>
                                    <div class="env-line">FTP_PASSWORD=<span>your_password</span></div>
                                </div>
                            </div>

                            {{-- Test connection --}}
                            <div style="display:flex;gap:8px;margin-top:1rem">
                                <button type="button" class="btn btn-ghost" style="flex:1"
                                    @click="testCloudConnection()">
                                    <i class="fas fa-plug"></i> Test Connection
                                </button>
                            </div>
                            <div x-show="cloudTestResult" x-cloak
                                style="margin-top:8px;padding:9px 12px;border-radius:var(--rsm);font-size:12px"
                                :style="cloudTestOk ?
                                    'background:var(--gdim);border:1px solid rgba(22,163,74,.2);color:var(--green)' :
                                    'background:var(--rdim);border:1px solid rgba(220,38,38,.2);color:var(--red)'"
                                x-text="cloudTestResult"></div>
                        </div>
                    </div>

                </div>
            </div>

        </div>{{-- /bk-body --}}

        {{-- ════ RESTORE MODAL ════ --}}
        <div class="modal-overlay" x-show="showRestoreModal" x-cloak @click.self="showRestoreModal=false">
            <div class="modal-card">
                <div class="modal-head">
                    <div class="modal-title">Restore Backup</div>
                    <button class="modal-close" @click="showRestoreModal=false"><i class="fas fa-times"></i></button>
                </div>
                <div class="modal-body">
                    <div class="warn-box">
                        <i class="fas fa-triangle-exclamation" style="flex-shrink:0;margin-top:1px"></i>
                        <div>
                            <strong>Warning — this will overwrite your current database.</strong><br>
                            All data created after this backup was made will be permanently lost.
                            We strongly recommend running a fresh backup before restoring.
                        </div>
                    </div>
                    <div class="restore-file-info">
                        <div class="rfi-row"><span>File</span><span class="rfi-val" x-text="restoreTarget?.name"></span>
                        </div>
                        <div class="rfi-row"><span>Size</span><span class="rfi-val" x-text="restoreTarget?.size"></span>
                        </div>
                        <div class="rfi-row"><span>Created</span><span class="rfi-val"
                                x-text="restoreTarget?.created_at"></span></div>
                        <div class="rfi-row"><span>Type</span><span class="rfi-val"
                                x-text="restoreTarget?.cloud ? 'Cloud + Local' : 'Local'"></span></div>
                    </div>
                    <div style="margin-bottom:.75rem">
                        <label class="field-label">Type <strong
                                style="font-family:var(--mono);color:var(--red)">RESTORE</strong> to confirm</label>
                        <input type="text" class="field-input" x-model="restoreConfirmText" placeholder="RESTORE">
                    </div>
                    <div x-show="restoreError" x-cloak
                        style="padding:9px 12px;background:var(--rdim);border:1px solid rgba(220,38,38,.2);border-radius:var(--rsm);font-size:12px;color:var(--red)"
                        x-text="restoreError"></div>
                </div>
                <div class="modal-foot">
                    <button type="button" class="btn btn-ghost" @click="showRestoreModal=false">Cancel</button>
                    <button type="button" class="btn btn-danger" @click="confirmRestore()"
                        :disabled="restoreConfirmText !== 'RESTORE' || restoreSaving">
                        <i class="fas fa-spinner fa-spin" x-show="restoreSaving"></i>
                        <span x-text="restoreSaving ? 'Restoring…' : 'Restore Database'"></span>
                    </button>
                </div>
            </div>
        </div>

    </div>{{-- /bk --}}
@endsection

@push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('backupPage', () => ({

                /* state */
                status: {},
                backups: [],
                syncTables: [],
                logs: [],
                backupsLoading: true,
                refreshing: false,
                backupRunning: false,
                backupDone: false,
                backupFailed: false,
                backupPct: 0,
                backupStepLabel: '',
                syncRunning: false,

                /* backup steps */
                backupSteps: [{
                        label: 'Connecting to DB',
                        state: 'pending'
                    },
                    {
                        label: 'Dumping database',
                        state: 'pending'
                    },
                    {
                        label: 'Compressing',
                        state: 'pending'
                    },
                    {
                        label: 'Saving locally',
                        state: 'pending'
                    },
                    {
                        label: 'Uploading to cloud',
                        state: 'pending'
                    },
                ],

                /* schedule */
                schedule: {
                    daily_enabled: true,
                    daily_time: '02:00',
                    weekly_enabled: true,
                    auto_cloud: false,
                    cleanup_enabled: true,
                    keep_count: '10',
                    encrypt: false,
                },

                /* cloud */
                cloudConfig: {
                    provider: 'gdrive',
                    gdrive_key: '',
                    gdrive_folder: '',
                    dropbox_token: '',
                    dropbox_path: '/afghan-pos-backups',
                    ftp_host: '',
                    ftp_port: '21',
                    ftp_user: '',
                    ftp_pass: '',
                    ftp_path: '/backups',
                },
                cloudTestResult: '',
                cloudTestOk: false,

                /* restore */
                showRestoreModal: false,
                restoreTarget: null,
                restoreConfirmText: '',
                restoreError: '',
                restoreSaving: false,

                /* urls */
                urls: {
                    status: '{{ route('pos.backup.status') }}',
                    backups: '{{ route('pos.backup.list') }}',
                    run: '{{ route('pos.backup.run') }}',
                    restore: '{{ route('pos.backup.restore') }}',
                    delete: '{{ route('pos.backup.delete') }}',
                    sync: '{{ route('pos.backup.sync') }}',
                    schedule: '{{ route('pos.backup.schedule') }}',
                    cloud: '{{ route('pos.backup.cloud') }}',
                    cloudTest: '{{ route('pos.backup.cloud.test') }}',
                    csrf: document.querySelector('meta[name=csrf-token]').content,
                },

                /* ── Init ── */
                async init() {
                    await this.loadStatus();
                    await this.loadBackups();
                    this.loadSchedule();
                },

                async refreshAll() {
                    this.refreshing = true;
                    await this.loadStatus();
                    await this.loadBackups();
                    this.refreshing = false;
                },

                /* ── Load status ── */
                async loadStatus() {
                    try {
                        const r = await fetch(this.urls.status, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        const d = await r.json();
                        this.status = d.status;
                        this.syncTables = d.sync_tables;
                    } catch (e) {
                        this.addLog('error', 'Failed to load status: ' + e.message);
                    }
                },

                /* ── Load backup list ── */
                async loadBackups() {
                    this.backupsLoading = true;
                    try {
                        const r = await fetch(this.urls.backups, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        this.backups = await r.json();
                    } catch (e) {
                        this.addLog('error', 'Failed to load backups: ' + e.message);
                    } finally {
                        this.backupsLoading = false;
                    }
                },

                /* ── Run backup ── */
                async runBackup() {
                    this.backupRunning = true;
                    this.backupDone = false;
                    this.backupFailed = false;
                    this.backupPct = 0;
                    this.backupSteps = this.backupSteps.map(s => ({
                        ...s,
                        state: 'pending'
                    }));
                    this.addLog('info', 'Backup started…');

                    // Animate steps
                    const steps = [{
                            idx: 0,
                            pct: 15,
                            label: 'Connecting to database…'
                        },
                        {
                            idx: 1,
                            pct: 35,
                            label: 'Dumping database…'
                        },
                        {
                            idx: 2,
                            pct: 60,
                            label: 'Compressing archive…'
                        },
                        {
                            idx: 3,
                            pct: 80,
                            label: 'Saving locally…'
                        },
                        {
                            idx: 4,
                            pct: 95,
                            label: 'Uploading to cloud…'
                        },
                    ];

                    try {
                        // Run fake progress while waiting for server
                        let stepIdx = 0;
                        const progressInterval = setInterval(() => {
                            if (stepIdx < steps.length) {
                                const s = steps[stepIdx];
                                if (stepIdx > 0) this.backupSteps[stepIdx - 1].state =
                                    'done';
                                this.backupSteps[stepIdx].state = 'active';
                                this.backupPct = s.pct;
                                this.backupStepLabel = s.label;
                                stepIdx++;
                            }
                        }, 800);

                        const r = await fetch(this.urls.run, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.urls.csrf
                            },
                        });
                        const d = await r.json();

                        clearInterval(progressInterval);

                        if (d.success) {
                            this.backupSteps = this.backupSteps.map(s => ({
                                ...s,
                                state: 'done'
                            }));
                            this.backupPct = 100;
                            this.backupStepLabel = 'Backup complete!';
                            this.backupDone = true;
                            this.addLog('success', `Backup completed: ${d.filename} (${d.size})`);
                            await this.loadBackups();
                            await this.loadStatus();
                        } else {
                            throw new Error(d.message);
                        }
                    } catch (e) {
                        this.backupFailed = true;
                        this.backupStepLabel = 'Backup failed: ' + e.message;
                        this.backupSteps = this.backupSteps.map(s => s.state === 'active' ? {
                            ...s,
                            state: 'failed'
                        } : s);
                        this.addLog('error', 'Backup failed: ' + e.message);
                    } finally {
                        this.backupRunning = false;
                    }
                },

                /* ── Sync all tables ── */
                async runSync() {
                    this.syncRunning = true;
                    this.addLog('info', 'Starting record sync…');
                    try {
                        const r = await fetch(this.urls.sync, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.urls.csrf
                            },
                            body: JSON.stringify({
                                table: 'all'
                            })
                        });
                        const d = await r.json();
                        if (d.success) {
                            this.addLog('success', `Sync complete. ${d.synced} records synced.`);
                            await this.loadStatus();
                        } else {
                            this.addLog('error', 'Sync failed: ' + d.message);
                        }
                    } catch (e) {
                        this.addLog('error', 'Sync error: ' + e.message);
                    } finally {
                        this.syncRunning = false;
                    }
                },

                async syncTable(tableName) {
                    this.addLog('info', `Syncing ${tableName}…`);
                    try {
                        const r = await fetch(this.urls.sync, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.urls.csrf
                            },
                            body: JSON.stringify({
                                table: tableName
                            })
                        });
                        const d = await r.json();
                        if (d.success) {
                            this.addLog('success', `${tableName}: ${d.synced} records synced.`);
                            await this.loadStatus();
                        } else {
                            this.addLog('error', `${tableName} sync failed: ${d.message}`);
                        }
                    } catch (e) {
                        this.addLog('error', 'Error: ' + e.message);
                    }
                },

                /* ── Restore ── */
                openRestoreModal(b) {
                    this.restoreTarget = b;
                    this.restoreConfirmText = '';
                    this.restoreError = '';
                    this.showRestoreModal = true;
                },

                async confirmRestore() {
                    if (this.restoreConfirmText !== 'RESTORE') return;
                    this.restoreSaving = true;
                    this.restoreError = '';
                    this.addLog('warning', 'Restore started — this will overwrite the database…');
                    try {
                        const r = await fetch(this.urls.restore, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.urls.csrf
                            },
                            body: JSON.stringify({
                                path: this.restoreTarget.path
                            })
                        });
                        const d = await r.json();
                        if (d.success) {
                            this.showRestoreModal = false;
                            this.addLog('success', 'Database restored successfully from ' + this
                                .restoreTarget.name);
                        } else {
                            this.restoreError = d.message ?? 'Restore failed.';
                            this.addLog('error', 'Restore failed: ' + d.message);
                        }
                    } catch (e) {
                        this.restoreError = 'Network error: ' + e.message;
                    } finally {
                        this.restoreSaving = false;
                    }
                },

                /* ── Download backup ── */
                downloadBackup(b) {
                    window.location.href = '{{ url('pos/backup/download') }}?path=' +
                        encodeURIComponent(b.path);
                },

                /* ── Delete backup ── */
                async deleteBackup(b) {
                    if (!confirm(`Delete backup "${b.name}"? This cannot be undone.`)) return;
                    try {
                        const r = await fetch(this.urls.delete, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.urls.csrf
                            },
                            body: JSON.stringify({
                                path: b.path
                            })
                        });
                        const d = await r.json();
                        if (d.success) {
                            this.addLog('info', 'Backup deleted: ' + b.name);
                            this.loadBackups();
                        }
                    } catch (e) {
                        this.addLog('error', 'Delete failed: ' + e.message);
                    }
                },

                /* ── Schedule ── */
                loadSchedule() {
                    const saved = localStorage.getItem('backup_schedule');
                    if (saved) this.schedule = {
                        ...this.schedule,
                        ...JSON.parse(saved)
                    };
                },

                async saveSchedule() {
                    localStorage.setItem('backup_schedule', JSON.stringify(this.schedule));
                    try {
                        await fetch(this.urls.schedule, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.urls.csrf
                            },
                            body: JSON.stringify(this.schedule)
                        });
                    } catch (e) {}
                    this.addLog('success', 'Schedule settings saved.');
                },

                /* ── Cloud config ── */
                async saveCloudConfig() {
                    try {
                        const r = await fetch(this.urls.cloud, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.urls.csrf
                            },
                            body: JSON.stringify(this.cloudConfig)
                        });
                        const d = await r.json();
                        if (d.success) this.addLog('success', 'Cloud configuration saved.');
                        else this.addLog('error', d.message);
                    } catch (e) {
                        this.addLog('error', 'Failed to save cloud config.');
                    }
                },

                async testCloudConnection() {
                    this.cloudTestResult = 'Testing connection…';
                    this.cloudTestOk = false;
                    try {
                        const r = await fetch(this.urls.cloudTest, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.urls.csrf
                            },
                            body: JSON.stringify({
                                provider: this.cloudConfig.provider
                            })
                        });
                        const d = await r.json();
                        this.cloudTestOk = d.success;
                        this.cloudTestResult = d.message;
                    } catch (e) {
                        this.cloudTestOk = false;
                        this.cloudTestResult = 'Connection test failed: ' + e.message;
                    }
                },

                /* ── Log ── */
                addLog(type, message) {
                    this.logs.unshift({
                        type,
                        message,
                        time: new Date().toLocaleTimeString('en-GB'),
                    });
                    if (this.logs.length > 50) this.logs.pop();
                },

                clearLog() {
                    this.logs = [];
                },
            }));
        });
    </script>
@endpush
