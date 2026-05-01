@extends('layouts.app')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;1,400&family=Outfit:wght@300;400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
/* ══════════════════════════════════════
   TOKENS
══════════════════════════════════════ */
:root {
    --bg:       #f1f3f8;
    --surface:  #ffffff;
    --s2:       #f6f8fc;
    --s3:       #eceef5;
    --border:   #dde1ee;
    --border2:  #c5cbdc;
    --ink:      #181c2e;
    --ink2:     #424868;
    --ink3:     #848baa;
    --ink4:     #bec5da;
    --blue:     #2563eb;
    --blue2:    #1d4ed8;
    --bdim:     rgba(37,99,235,.08);
    --bmid:     rgba(37,99,235,.16);
    --green:    #16a34a;
    --gdim:     rgba(22,163,74,.09);
    --red:      #dc2626;
    --rdim:     rgba(220,38,38,.08);
    --amber:    #d97706;
    --adim:     rgba(217,119,6,.09);
    --violet:   #7c3aed;
    --vdim:     rgba(124,58,237,.09);
    --mono:     'IBM Plex Mono', monospace;
    --body:     'Outfit', sans-serif;
    --display:  'Cormorant Garamond', serif;
    --r:        10px;
    --rsm:      6px;
    --rlg:      16px;
    --sh:       0 1px 3px rgba(0,0,0,.05), 0 1px 2px rgba(0,0,0,.03);
    --shmd:     0 4px 18px rgba(0,0,0,.08), 0 2px 6px rgba(0,0,0,.04);
    --shlg:     0 16px 48px rgba(0,0,0,.12), 0 4px 14px rgba(0,0,0,.06);
}

/* ══════════════════════════════════════
   BASE
══════════════════════════════════════ */
.sl * { box-sizing: border-box; }
.sl { font-family: var(--body); background: var(--bg); min-height: 100vh; color: var(--ink); }
[x-cloak] { display: none !important; }

/* ══════════════════════════════════════
   TOPBAR
══════════════════════════════════════ */
.sl-top {
    background: var(--surface); border-bottom: 1px solid var(--border);
    height: 56px; display: flex; align-items: center; justify-content: space-between;
    padding: 0 1.75rem; position: sticky; top: 0; z-index: 80; box-shadow: var(--sh);
}
.sl-title { font-family: var(--display); font-size: 22px; color: var(--ink); font-weight: 500; letter-spacing: -.2px; }
.sl-title em { color: var(--blue); font-style: italic; }
.top-r { display: flex; align-items: center; gap: 8px; }

/* ══════════════════════════════════════
   BUTTONS
══════════════════════════════════════ */
.btn { display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px; border-radius: var(--rsm); font-family: var(--body); font-size: 12.5px; font-weight: 600; border: none; cursor: pointer; transition: all .16s; white-space: nowrap; }
.btn-ghost { background: var(--s2); border: 1px solid var(--border); color: var(--ink2); }
.btn-ghost:hover { background: var(--s3); color: var(--ink); }
.btn-primary { background: var(--blue); color: #fff; box-shadow: 0 2px 8px rgba(37,99,235,.25); }
.btn-primary:hover { background: var(--blue2); transform: translateY(-1px); }
.btn-danger { background: var(--rdim); border: 1px solid rgba(220,38,38,.2); color: var(--red); }
.btn-danger:hover { background: var(--red); color: #fff; }
.btn-sm { padding: 5px 10px; font-size: 11.5px; }
.btn:active { transform: scale(.97); }
.btn:disabled { opacity: .4; cursor: not-allowed; transform: none !important; }

/* ══════════════════════════════════════
   STAT STRIP
══════════════════════════════════════ */
.stat-strip { display: grid; grid-template-columns: repeat(5, 1fr); gap: 1rem; padding: 1.2rem 1.75rem .75rem; }
.stat-tile {
    background: var(--surface); border: 1px solid var(--border); border-radius: var(--r);
    padding: 1rem 1.2rem; position: relative; overflow: hidden; transition: all .2s; cursor: default;
}
.stat-tile::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: var(--ac, var(--blue)); transform: scaleX(0); transform-origin: left; transition: transform .3s;
}
.stat-tile:hover { box-shadow: var(--shmd); transform: translateY(-2px); border-color: var(--border2); }
.stat-tile:hover::before { transform: scaleX(1); }
.st-label { font-size: 10px; font-weight: 700; color: var(--ink3); text-transform: uppercase; letter-spacing: .1em; margin-bottom: 7px; display: flex; align-items: center; justify-content: space-between; }
.st-val { font-family: var(--mono); font-size: 22px; font-weight: 500; color: var(--ink); line-height: 1; letter-spacing: -.5px; }
.st-sub { font-size: 11px; color: var(--ink3); margin-top: 5px; }
.trend { display: inline-flex; align-items: center; gap: 3px; font-size: 10px; font-weight: 700; padding: 2px 7px; border-radius: 99px; }
.t-up { color: var(--green); background: var(--gdim); }
.t-dn { color: var(--red); background: var(--rdim); }
.t-nt { color: var(--ink3); background: var(--s3); }

/* ══════════════════════════════════════
   TOOLBAR
══════════════════════════════════════ */
.sl-toolbar { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; padding: .75rem 1.75rem .9rem; }
.search-box { position: relative; flex: 1; min-width: 200px; max-width: 320px; }
.search-box i { position: absolute; left: 11px; top: 50%; transform: translateY(-50%); color: var(--ink3); font-size: 13px; pointer-events: none; }
.sl-search { width: 100%; padding: 9px 14px 9px 34px; background: var(--surface); border: 1.5px solid var(--border); border-radius: var(--rsm); font-family: var(--body); font-size: 13px; color: var(--ink); outline: none; transition: border .15s, box-shadow .15s; }
.sl-search:focus { border-color: var(--blue); box-shadow: 0 0 0 3px var(--bdim); background: #fff; }
.sl-search::placeholder { color: var(--ink4); }
.f-sel { padding: 9px 12px; background: var(--surface); border: 1.5px solid var(--border); border-radius: var(--rsm); font-family: var(--body); font-size: 12.5px; color: var(--ink2); outline: none; cursor: pointer; transition: border .15s; }
.f-sel:focus { border-color: var(--blue); }
.date-range { display: flex; align-items: center; gap: 6px; }
.date-input { padding: 8px 10px; background: var(--surface); border: 1.5px solid var(--border); border-radius: var(--rsm); font-family: var(--mono); font-size: 12px; color: var(--ink2); outline: none; transition: border .15s; }
.date-input:focus { border-color: var(--blue); }
.date-sep { font-size: 12px; color: var(--ink4); }
.tab-strip { display: flex; gap: 4px; margin-left: auto; }
.tab-btn { padding: 7px 13px; border: 1px solid var(--border); border-radius: var(--rsm); background: var(--surface); font-family: var(--body); font-size: 12px; font-weight: 600; cursor: pointer; color: var(--ink3); transition: all .15s; }
.tab-btn.active { background: var(--blue); color: #fff; border-color: var(--blue); }

/* ══════════════════════════════════════
   MAIN LAYOUT (table + panel)
══════════════════════════════════════ */
.sl-main { display: grid; grid-template-columns: 1fr; gap: 0; padding: 0 1.75rem 2rem; transition: grid-template-columns .25s; }
.sl-main.panel-open { grid-template-columns: 1fr 400px; gap: 1.25rem; }

/* ══════════════════════════════════════
   TABLE
══════════════════════════════════════ */
.table-card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--r); overflow: hidden; box-shadow: var(--sh); }
.sl-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.sl-table thead { background: var(--s2); border-bottom: 1.5px solid var(--border); }
.sl-table th { padding: 10px 14px; text-align: left; font-size: 10px; font-weight: 700; color: var(--ink3); text-transform: uppercase; letter-spacing: .08em; white-space: nowrap; cursor: pointer; user-select: none; transition: color .15s; }
.sl-table th:hover { color: var(--blue); }
.sl-table th i { margin-left: 3px; opacity: .45; }
.sl-table td { padding: 11px 14px; border-bottom: 1px solid var(--border); vertical-align: middle; }
.sl-table tbody tr:last-child td { border-bottom: none; }
.sl-table tbody tr { transition: background .12s; cursor: pointer; }
.sl-table tbody tr:hover { background: var(--bdim); }
.sl-table tbody tr.selected { background: var(--bdim); border-left: 3px solid var(--blue); }

/* cells */
.sale-id { font-family: var(--mono); font-size: 12px; color: var(--blue); font-weight: 500; }
.sale-time { font-size: 12px; color: var(--ink3); font-family: var(--mono); }
.sale-cust { font-weight: 600; font-size: 13px; color: var(--ink); }
.sale-cust-sub { font-size: 11px; color: var(--ink3); margin-top: 1px; }
.sale-amt { font-family: var(--mono); font-size: 14px; font-weight: 600; color: var(--ink); }
.cell-right { text-align: right; }

/* pills */
.pill { display: inline-block; padding: 3px 9px; border-radius: 99px; font-size: 10px; font-weight: 700; letter-spacing: .04em; }
.pill-green  { background: var(--gdim); color: var(--green); border: 1px solid rgba(22,163,74,.2); }
.pill-red    { background: var(--rdim); color: var(--red);   border: 1px solid rgba(220,38,38,.2); }
.pill-amber  { background: var(--adim); color: var(--amber); border: 1px solid rgba(217,119,6,.2); }
.pill-blue   { background: var(--bdim); color: var(--blue);  border: 1px solid var(--bmid); }
.pill-violet { background: var(--vdim); color: var(--violet);border: 1px solid rgba(124,58,237,.2); }
.pill-gray   { background: var(--s3);   color: var(--ink3);  border: 1px solid var(--border); }

/* row actions */
.row-acts { display: flex; gap: 4px; opacity: 0; transition: opacity .15s; }
.sl-table tbody tr:hover .row-acts { opacity: 1; }

/* ══════════════════════════════════════
   PAGINATION
══════════════════════════════════════ */
.pag-row { display: flex; align-items: center; justify-content: space-between; padding: 11px 16px; border-top: 1px solid var(--border); background: var(--s2); }
.pag-info { font-size: 12px; color: var(--ink3); }
.pag-btns { display: flex; gap: 4px; }
.pag-btn { width: 30px; height: 30px; border-radius: var(--rsm); border: 1px solid var(--border); background: var(--surface); cursor: pointer; font-family: var(--mono); font-size: 11px; color: var(--ink2); display: flex; align-items: center; justify-content: center; transition: all .12s; }
.pag-btn:hover { background: var(--bdim); border-color: var(--blue); color: var(--blue); }
.pag-btn.active { background: var(--blue); color: #fff; border-color: var(--blue); }
.pag-btn:disabled { opacity: .3; cursor: not-allowed; }

/* ══════════════════════════════════════
   EMPTY / LOADING
══════════════════════════════════════ */
.empty-state { text-align: center; padding: 4rem 2rem; color: var(--ink3); }
.empty-state i { font-size: 36px; margin-bottom: 12px; display: block; color: var(--ink4); }
.empty-state p { font-size: 13px; line-height: 1.7; }
.loading-row { text-align: center; padding: 3rem; color: var(--ink3); font-size: 13px; }

/* ══════════════════════════════════════
   DETAIL PANEL
══════════════════════════════════════ */
.detail-panel {
    background: var(--surface); border: 1px solid var(--border); border-radius: var(--r);
    box-shadow: var(--sh); display: flex; flex-direction: column;
    max-height: calc(100vh - 170px); position: sticky; top: 72px; overflow: hidden;
    animation: panelIn .2s cubic-bezier(.2,.8,.36,1);
}
@keyframes panelIn { from { opacity: 0; transform: translateX(14px); } to { opacity: 1; transform: none; } }
.dp-head {
    padding: .9rem 1.25rem; border-bottom: 1px solid var(--border);
    display: flex; align-items: center; justify-content: space-between; flex-shrink: 0;
}
.dp-head-title { font-size: 11px; font-weight: 700; color: var(--ink3); text-transform: uppercase; letter-spacing: .09em; }
.dp-close { background: none; border: none; cursor: pointer; color: var(--ink3); font-size: 16px; transition: color .15s; }
.dp-close:hover { color: var(--ink); }
.dp-body { flex: 1; overflow-y: auto; }
.dp-body::-webkit-scrollbar { width: 4px; }
.dp-body::-webkit-scrollbar-thumb { background: var(--border); border-radius: 2px; }

/* receipt header strip */
.receipt-strip {
    background: linear-gradient(135deg, #1e3a5f 0%, #1a2f50 100%);
    padding: 1.25rem 1.5rem; color: #fff;
}
.rs-id { font-family: var(--mono); font-size: 13px; color: rgba(255,255,255,.5); margin-bottom: 4px; }
.rs-amount { font-family: var(--mono); font-size: 30px; font-weight: 500; color: #fff; letter-spacing: -1px; line-height: 1; }
.rs-amount span { font-size: 14px; color: rgba(255,255,255,.5); margin-right: 2px; }
.rs-meta { display: flex; gap: 1rem; margin-top: 10px; flex-wrap: wrap; }
.rs-meta-item { font-size: 11px; color: rgba(255,255,255,.5); display: flex; align-items: center; gap: 5px; }
.rs-meta-item strong { color: rgba(255,255,255,.85); }

/* detail sections */
.dp-section { padding: .9rem 1.25rem; border-bottom: 1px solid var(--border); }
.dp-section:last-child { border-bottom: none; }
.dp-section-title { font-size: 10px; font-weight: 700; color: var(--ink3); text-transform: uppercase; letter-spacing: .1em; margin-bottom: .65rem; display: flex; align-items: center; gap: 6px; }
.dp-section-title i { color: var(--blue); }

/* info grid */
.info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 7px; }
.info-field { background: var(--s2); border: 1px solid var(--border); border-radius: var(--rsm); padding: 7px 10px; }
.info-field.full { grid-column: span 2; }
.if-label { font-size: 10px; color: var(--ink3); text-transform: uppercase; letter-spacing: .06em; margin-bottom: 3px; }
.if-val { font-size: 12.5px; font-weight: 500; color: var(--ink); }
.if-val.mono { font-family: var(--mono); font-size: 12px; }

/* sale items */
.item-row { display: flex; align-items: center; justify-content: space-between; padding: 8px 10px; background: var(--s2); border: 1px solid var(--border); border-radius: var(--rsm); margin-bottom: 5px; }
.item-row:last-child { margin-bottom: 0; }
.ir-name { font-size: 12.5px; font-weight: 500; color: var(--ink); }
.ir-sku  { font-family: var(--mono); font-size: 10px; color: var(--ink3); margin-top: 2px; }
.ir-right { text-align: right; flex-shrink: 0; margin-left: 10px; }
.ir-qty   { font-size: 11px; color: var(--ink3); margin-bottom: 2px; }
.ir-total { font-family: var(--mono); font-size: 13px; font-weight: 600; color: var(--blue); }
.ir-returned { opacity: .5; text-decoration: line-through; }

/* totals */
.tot-row { display: flex; justify-content: space-between; font-size: 12.5px; margin-bottom: 5px; color: var(--ink2); }
.tot-row.grand { font-size: 15px; font-weight: 700; color: var(--ink); padding-top: 7px; border-top: 1px solid var(--border); margin-top: 7px; }
.tot-row.grand span { font-family: var(--mono); }
.tot-mono { font-family: var(--mono); font-size: 12px; }

/* dp footer */
.dp-foot { padding: .9rem 1.25rem; border-top: 1px solid var(--border); flex-shrink: 0; display: flex; gap: 7px; flex-wrap: wrap; }

/* ══════════════════════════════════════
   REFUND MODAL
══════════════════════════════════════ */
.modal-overlay { position: fixed; inset: 0; background: rgba(24,28,46,.45); backdrop-filter: blur(5px); z-index: 200; display: flex; align-items: center; justify-content: center; padding: 1rem; animation: fadeIn .15s ease; }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
.modal-card { background: var(--surface); border-radius: var(--rlg); box-shadow: var(--shlg); width: 100%; max-width: 500px; max-height: 90vh; display: flex; flex-direction: column; animation: slideUp .18s cubic-bezier(.2,.8,.36,1); }
@keyframes slideUp { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: none; } }
.modal-head { padding: 1rem 1.5rem; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; flex-shrink: 0; }
.modal-title { font-family: var(--display); font-size: 20px; color: var(--ink); font-weight: 500; }
.modal-close { background: none; border: none; cursor: pointer; color: var(--ink3); font-size: 18px; }
.modal-body { padding: 1.25rem 1.5rem; overflow-y: auto; flex: 1; }
.modal-foot { padding: 1rem 1.5rem; border-top: 1px solid var(--border); display: flex; gap: 8px; justify-content: flex-end; flex-shrink: 0; }

/* refund items checklist */
.refund-item { display: flex; align-items: center; gap: 10px; padding: 9px 12px; background: var(--s2); border: 1.5px solid var(--border); border-radius: var(--rsm); margin-bottom: 6px; cursor: pointer; transition: all .15s; }
.refund-item.selected { border-color: var(--red); background: var(--rdim); }
.ri-check { width: 18px; height: 18px; border: 1.5px solid var(--border2); border-radius: 4px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; transition: all .15s; }
.refund-item.selected .ri-check { background: var(--red); border-color: var(--red); color: #fff; font-size: 10px; }
.ri-info { flex: 1; }
.ri-name { font-size: 12.5px; font-weight: 500; color: var(--ink); }
.ri-detail { font-size: 11px; color: var(--ink3); font-family: var(--mono); margin-top: 1px; }
.ri-amt { font-family: var(--mono); font-size: 13px; font-weight: 600; color: var(--red); }

/* qty input in refund */
.qty-refund { display: flex; align-items: center; gap: 5px; }
.qty-refund input { width: 48px; padding: 4px 6px; border: 1px solid var(--border); border-radius: 4px; font-family: var(--mono); font-size: 12px; text-align: center; outline: none; }
.qty-refund input:focus { border-color: var(--red); }

.refund-summary { padding: 10px 12px; background: var(--rdim); border: 1px solid rgba(220,38,38,.2); border-radius: var(--rsm); margin-top: .75rem; }
.rs-row { display: flex; justify-content: space-between; font-size: 12.5px; color: var(--red); margin-bottom: 3px; }
.rs-row:last-child { margin-bottom: 0; font-weight: 700; font-size: 14px; }

.field-label { display: block; font-size: 11px; font-weight: 700; color: var(--ink2); text-transform: uppercase; letter-spacing: .06em; margin-bottom: 5px; }
.field-input { width: 100%; padding: 9px 12px; background: var(--s2); border: 1.5px solid var(--border); border-radius: var(--rsm); font-family: var(--body); font-size: 13px; color: var(--ink); outline: none; transition: border .15s, box-shadow .15s; }
.field-input:focus { border-color: var(--blue); box-shadow: 0 0 0 3px var(--bdim); background: #fff; }
textarea.field-input { resize: vertical; min-height: 70px; }

.form-err { padding: 9px 12px; background: var(--rdim); border: 1px solid rgba(220,38,38,.2); border-radius: var(--rsm); font-size: 12px; color: var(--red); margin-top: .75rem; }

/* warning box */
.warn-box { padding: 10px 14px; background: var(--adim); border: 1px solid rgba(217,119,6,.22); border-radius: var(--rsm); font-size: 12px; color: var(--amber); display: flex; gap: 8px; margin-bottom: 1rem; }
.warn-box i { flex-shrink: 0; margin-top: 1px; }
</style>
@endpush

@section('content')
<div class="sl" x-data="salesPage()" x-init="init()">

{{-- ════ TOPBAR ════ --}}
<div class="sl-top">
    <div class="sl-title">Afghan <em>POS</em> — Sales Log</div>
    <div class="top-r">
        <button class="btn btn-ghost" @click="exportCsv()">
            <i class="fas fa-file-csv"></i> Export CSV
        </button>
        <a href="{{ route('pos.checkout') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> New Sale
        </a>
    </div>
</div>

{{-- ════ STATS ════ --}}
<div class="stat-strip">
    <div class="stat-tile" style="--ac:var(--blue)">
        <div class="st-label">Today's Revenue <span style="color:var(--blue)"><i class="fas fa-coins"></i></span></div>
        <div class="st-val" style="font-size:18px">Af {{ number_format($stats['today_revenue'] ?? 0) }}</div>
        <div class="st-sub">{{ $stats['today_count'] ?? 0 }} transactions today</div>
    </div>
    <div class="stat-tile" style="--ac:var(--green)">
        <div class="st-label">Cash Sales <span style="color:var(--green)"><i class="fas fa-money-bill"></i></span></div>
        <div class="st-val" style="font-size:18px">Af {{ number_format($stats['today_cash'] ?? 0) }}</div>
        <div class="st-sub">today's cash revenue</div>
    </div>
    <div class="stat-tile" style="--ac:var(--amber)">
        <div class="st-label">Loan Sales <span style="color:var(--amber)"><i class="fas fa-file-invoice"></i></span></div>
        <div class="st-val" style="font-size:18px">Af {{ number_format($stats['today_loan'] ?? 0) }}</div>
        <div class="st-sub">today's credit sales</div>
    </div>
    <div class="stat-tile" style="--ac:var(--red)">
        <div class="st-label">Refunded <span style="color:var(--red)"><i class="fas fa-rotate-left"></i></span></div>
        <div class="st-val" style="color:var(--red)">{{ $stats['today_refunds'] ?? 0 }}</div>
        <div class="st-sub">sales refunded today</div>
    </div>
    <div class="stat-tile" style="--ac:var(--violet)">
        <div class="st-label">Avg Ticket <span style="color:var(--violet)"><i class="fas fa-receipt"></i></span></div>
        <div class="st-val" style="font-size:18px">Af {{ number_format($stats['today_avg'] ?? 0) }}</div>
        <div class="st-sub">per transaction today</div>
    </div>
</div>

{{-- ════ TOOLBAR ════ --}}
<div class="sl-toolbar">
    <div class="search-box">
        <i class="fas fa-search"></i>
        <input class="sl-search" type="text" x-model="search"
               @input.debounce.350ms="loadSales()"
               placeholder="Sale ID, customer name…">
    </div>

    <div class="date-range">
        <input type="date" class="date-input" x-model="dateFrom">
        <span class="date-sep">→</span>
        <input type="date" class="date-input" x-model="dateTo">
        <button type="button" class="btn btn-ghost" @click="loadSales()">
            <i class="fas fa-rotate"></i>
        </button>
    </div>

    <select class="f-sel" x-model="filterMethod" @change="loadSales()">
        <option value="">All Methods</option>
        <option value="cash">Cash</option>
        <option value="loan">Loan</option>
    </select>

    <select class="f-sel" x-model="filterStatus" @change="loadSales()">
        <option value="">All Statuses</option>
        <option value="completed">Completed</option>
        <option value="held">Held</option>
        <option value="refunded">Refunded</option>
        <option value="cancelled">Cancelled</option>
    </select>

    <select class="f-sel" x-model="filterCashier" @change="loadSales()">
        <option value="">All Cashiers</option>
        @foreach($cashiers ?? [] as $cashier)
            <option value="{{ $cashier->id }}">{{ $cashier->name }}</option>
        @endforeach
    </select>

    <div class="tab-strip">
        <button type="button" class="tab-btn" :class="tab==='all'?'active':''"       @click="tab='all';loadSales()">All</button>
        <button type="button" class="tab-btn" :class="tab==='today'?'active':''"     @click="tab='today';loadSales()">Today</button>
        <button type="button" class="tab-btn" :class="tab==='held'?'active':''"      @click="tab='held';loadSales()">Held</button>
        <button type="button" class="tab-btn" :class="tab==='refunded'?'active':''"  @click="tab='refunded';loadSales()">Refunded</button>
    </div>
</div>

{{-- ════ MAIN ════ --}}
<div class="sl-main" :class="selected ? 'panel-open' : ''" style="align-items:start">

    {{-- TABLE --}}
    <div class="table-card">
        <div class="loading-row" x-show="loading">
            <i class="fas fa-spinner fa-spin" style="font-size:18px"></i>
        </div>

        <div x-show="!loading">
            <div class="empty-state" x-show="sales.length === 0">
                <i class="fas fa-receipt"></i>
                <p>No sales found.<br>Try adjusting the filters or date range.</p>
            </div>

            <table class="sl-table" x-show="sales.length > 0">
                <thead>
                    <tr>
                        <th @click="sort('local_id')">Sale ID <i class="fas fa-sort"></i></th>
                        <th @click="sort('created_at')">Date / Time <i class="fas fa-sort"></i></th>
                        <th>Customer</th>
                        <th>Cashier</th>
                        <th>Method</th>
                        <th @click="sort('total_amount')" class="cell-right">Total <i class="fas fa-sort"></i></th>
                        <th class="cell-right">Discount</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="s in sales" :key="s.id">
                        <tr :class="selected?.id === s.id ? 'selected' : ''"
                            @click="openDetail(s)">
                            <td><span class="sale-id" x-text="s.local_id"></span></td>
                            <td>
                                <div class="sale-time" x-text="s.date"></div>
                                <div class="sale-time" style="font-size:10px;opacity:.7" x-text="s.time"></div>
                            </td>
                            <td>
                                <div class="sale-cust" x-text="s.customer || 'Walk-in'"></div>
                                <div class="sale-cust-sub" x-show="s.customer_phone" x-text="s.customer_phone"></div>
                            </td>
                            <td style="font-size:12px;color:var(--ink2)" x-text="s.cashier"></td>
                            <td>
                                <span class="pill" :class="s.payment_method==='cash'?'pill-blue':'pill-amber'"
                                      x-text="s.payment_method"></span>
                            </td>
                            <td class="cell-right">
                                <span class="sale-amt" x-text="'Af ' + fmt(s.total_amount)"></span>
                            </td>
                            <td class="cell-right" style="font-family:var(--mono);font-size:12px;color:var(--red)"
                                x-text="s.discount_amount > 0 ? '- Af ' + fmt(s.discount_amount) : '—'"></td>
                            <td>
                                <span class="pill"
                                      :class="{
                                        'pill-green':  s.status==='completed',
                                        'pill-amber':  s.status==='held',
                                        'pill-red':    s.status==='refunded',
                                        'pill-gray':   s.status==='cancelled',
                                        'pill-violet': s.sale_type==='return',
                                      }"
                                      x-text="s.sale_type==='return' ? 'Return' : s.status">
                                </span>
                            </td>
                            <td @click.stop>
                                <div class="row-acts">
                                    <button type="button" class="btn btn-ghost btn-sm" @click="openDetail(s)" title="View">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-ghost btn-sm" @click="reprintReceipt(s)" title="Reprint">
                                        <i class="fas fa-print"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm"
                                            x-show="s.status === 'completed' && s.sale_type !== 'return'"
                                            @click="openRefund(s)" title="Refund">
                                        <i class="fas fa-rotate-left"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>

            {{-- Pagination --}}
            <div class="pag-row" x-show="pagination.last_page > 1">
                <div class="pag-info">
                    Showing <span x-text="pagination.from"></span>–<span x-text="pagination.to"></span>
                    of <span x-text="pagination.total"></span> sales
                </div>
                <div class="pag-btns">
                    <button class="pag-btn" @click="goPage(pagination.current_page-1)" :disabled="pagination.current_page===1">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <template x-for="p in pagination.last_page" :key="p">
                        <button class="pag-btn" :class="p===pagination.current_page?'active':''"
                                @click="goPage(p)" x-text="p"></button>
                    </template>
                    <button class="pag-btn" @click="goPage(pagination.current_page+1)" :disabled="pagination.current_page===pagination.last_page">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ════ DETAIL PANEL ════ --}}
    <div class="detail-panel" x-show="selected" x-cloak>
        <div class="dp-head">
            <span class="dp-head-title">Sale Detail</span>
            <button class="dp-close" @click="selected=null"><i class="fas fa-times"></i></button>
        </div>

        <div class="dp-body">

            {{-- Receipt header --}}
            <div class="receipt-strip">
                <div class="rs-id" x-text="selected?.local_id"></div>
                <div class="rs-amount"><span>Af</span><span x-text="fmt(selected?.total_amount||0)"></span></div>
                <div class="rs-meta">
                    <div class="rs-meta-item">
                        <i class="fas fa-calendar"></i>
                        <strong x-text="selected?.date + ' ' + selected?.time"></strong>
                    </div>
                    <div class="rs-meta-item">
                        <i class="fas fa-user-tie"></i>
                        <strong x-text="selected?.cashier"></strong>
                    </div>
                    <div class="rs-meta-item">
                        <i :class="selected?.payment_method==='cash'?'fas fa-money-bill':'fas fa-file-invoice'"></i>
                        <strong x-text="selected?.payment_method"></strong>
                    </div>
                </div>
            </div>

            {{-- Status bar --}}
            <div style="padding:.7rem 1.25rem;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:8px">
                <span class="pill"
                      :class="{
                        'pill-green':  selected?.status==='completed',
                        'pill-amber':  selected?.status==='held',
                        'pill-red':    selected?.status==='refunded',
                        'pill-gray':   selected?.status==='cancelled',
                        'pill-violet': selected?.sale_type==='return',
                      }"
                      x-text="selected?.sale_type==='return' ? 'Return / Refund' : selected?.status">
                </span>
                <span x-show="selected?.hold_code"
                      style="font-family:var(--mono);font-size:11px;color:var(--amber)">
                    Hold Code: <strong x-text="selected?.hold_code"></strong>
                </span>
            </div>

            {{-- Customer --}}
            <div class="dp-section">
                <div class="dp-section-title"><i class="fas fa-user"></i> Customer</div>
                <div class="info-grid">
                    <div class="info-field">
                        <div class="if-label">Name</div>
                        <div class="if-val" x-text="selected?.customer || 'Walk-in'"></div>
                    </div>
                    <div class="info-field">
                        <div class="if-label">Phone</div>
                        <div class="if-val mono" x-text="selected?.customer_phone || '—'"></div>
                    </div>
                </div>
            </div>

            {{-- Items --}}
            <div class="dp-section">
                <div class="dp-section-title"><i class="fas fa-list"></i> Items (<span x-text="detailItems.length"></span>)</div>
                <div x-show="detailLoading" style="text-align:center;padding:1rem;color:var(--ink3);font-size:12px">
                    <i class="fas fa-spinner fa-spin"></i> Loading…
                </div>
                <div x-show="!detailLoading">
                    <template x-for="item in detailItems" :key="item.id">
                        <div class="item-row" :class="item.is_returned ? 'ir-returned' : ''">
                            <div>
                                <div class="ir-name" x-text="item.product_name"></div>
                                <div class="ir-sku" x-text="item.sku + (item.is_returned ? ' · Returned' : '')"></div>
                            </div>
                            <div class="ir-right">
                                <div class="ir-qty" x-text="item.quantity + ' × Af ' + fmt(item.unit_price)"></div>
                                <div class="ir-total" x-text="'Af ' + fmt(item.line_total)"></div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Totals --}}
            <div class="dp-section">
                <div class="dp-section-title"><i class="fas fa-calculator"></i> Summary</div>
                <div class="tot-row"><span>Subtotal</span><span class="tot-mono" x-text="'Af ' + fmt(selected?.subtotal||0)"></span></div>
                <div class="tot-row" x-show="(selected?.discount_amount||0) > 0">
                    <span style="color:var(--red)">Discount</span>
                    <span class="tot-mono" style="color:var(--red)" x-text="'- Af ' + fmt(selected?.discount_amount||0)"></span>
                </div>
                <div class="tot-row" x-show="(selected?.tax_amount||0) > 0">
                    <span>Tax</span>
                    <span class="tot-mono" x-text="'Af ' + fmt(selected?.tax_amount||0)"></span>
                </div>
                <div class="tot-row grand">
                    <span>Total</span>
                    <span x-text="'Af ' + fmt(selected?.total_amount||0)"></span>
                </div>
                <div class="tot-row" x-show="selected?.payment_method==='cash'" style="margin-top:6px">
                    <span style="color:var(--ink3)">Cash Received</span>
                    <span class="tot-mono" x-text="'Af ' + fmt(selected?.amount_paid||0)"></span>
                </div>
                <div class="tot-row" x-show="selected?.payment_method==='cash' && (selected?.change_amount||0)>0">
                    <span style="color:var(--ink3)">Change Given</span>
                    <span class="tot-mono" x-text="'Af ' + fmt(selected?.change_amount||0)"></span>
                </div>
                <div class="tot-row" x-show="selected?.payment_method==='loan'" style="margin-top:6px">
                    <span style="color:var(--amber)">Loan Balance</span>
                    <span class="tot-mono" style="color:var(--amber)"
                          x-text="'Af ' + fmt((selected?.total_amount||0) - (selected?.amount_paid||0))"></span>
                </div>
            </div>

            {{-- Notes --}}
            <div class="dp-section" x-show="selected?.notes">
                <div class="dp-section-title"><i class="fas fa-pen"></i> Notes</div>
                <div style="font-size:12.5px;color:var(--ink2);line-height:1.6" x-text="selected?.notes"></div>
            </div>

        </div>

        {{-- Panel footer --}}
        <div class="dp-foot">
            <button type="button" class="btn btn-ghost" style="flex:1" @click="reprintReceipt(selected)">
                <i class="fas fa-print"></i> Reprint Receipt
            </button>
            <button type="button" class="btn btn-danger"
                    x-show="selected?.status === 'completed' && selected?.sale_type !== 'return'"
                    @click="openRefund(selected)">
                <i class="fas fa-rotate-left"></i> Refund
            </button>
        </div>
    </div>

</div>{{-- /sl-main --}}

{{-- ════ REFUND MODAL ════ --}}
<div class="modal-overlay" x-show="showRefundModal" x-cloak @click.self="showRefundModal=false">
    <div class="modal-card">
        <div class="modal-head">
            <div class="modal-title">Process Refund</div>
            <button class="modal-close" @click="showRefundModal=false"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">

            <div class="warn-box">
                <i class="fas fa-triangle-exclamation"></i>
                <div>
                    This will mark the sale as <strong>Refunded</strong>, restore stock for selected items,
                    and update the loan balance if applicable. This action cannot be undone.
                </div>
            </div>

            <div style="margin-bottom:1rem">
                <div style="font-size:11px;font-weight:700;color:var(--ink3);text-transform:uppercase;letter-spacing:.08em;margin-bottom:.6rem">
                    Select items to refund
                </div>
                <template x-for="item in refundItems" :key="item.id">
                    <div class="refund-item" :class="item.selected ? 'selected' : ''"
                         @click="item.selected = !item.selected">
                        <div class="ri-check">
                            <i class="fas fa-check" x-show="item.selected"></i>
                        </div>
                        <div class="ri-info">
                            <div class="ri-name" x-text="item.product_name"></div>
                            <div class="ri-detail" x-text="item.sku + ' · ' + item.quantity + ' sold @ Af ' + fmt(item.unit_price)"></div>
                        </div>
                        <div @click.stop style="display:flex;flex-direction:column;align-items:flex-end;gap:4px">
                            <span class="ri-amt" x-text="'- Af ' + fmt(item.unit_price * item.refund_qty)"></span>
                            <div class="qty-refund" x-show="item.selected">
                                <span style="font-size:11px;color:var(--ink3)">Qty:</span>
                                <input type="number" x-model.number="item.refund_qty"
                                       :max="item.quantity" min="1"
                                       @click.stop>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Refund summary --}}
            <div class="refund-summary" x-show="refundTotal > 0" x-cloak>
                <div class="rs-row"><span>Items selected</span><span x-text="refundItems.filter(i=>i.selected).length"></span></div>
                <div class="rs-row"><span>Total refund amount</span><span x-text="'Af ' + fmt(refundTotal)"></span></div>
            </div>

            <div style="margin-top:1rem">
                <label class="field-label">Reason for Refund <span style="color:var(--red)">*</span></label>
                <textarea class="field-input" x-model="refundReason" placeholder="Explain why this sale is being refunded…"></textarea>
            </div>

            <div class="form-err" x-show="refundError" x-text="refundError" x-cloak></div>
        </div>
        <div class="modal-foot">
            <button type="button" class="btn btn-ghost" @click="showRefundModal=false">Cancel</button>
            <button type="button" class="btn btn-danger" @click="processRefund()" :disabled="refundTotal===0||saving">
                <i class="fas fa-spinner fa-spin" x-show="saving"></i>
                <span x-text="saving ? 'Processing…' : 'Confirm Refund of Af ' + fmt(refundTotal)"></span>
            </button>
        </div>
    </div>
</div>

{{-- ════ RECEIPT PRINT AREA ════ --}}
<div id="receipt-print" style="display:none">
    <div style="font-family:monospace;font-size:12px;max-width:300px;margin:0 auto;padding:10px">
        <div style="text-align:center;margin-bottom:10px">
            <div style="font-size:18px;font-weight:bold">Afghan POS</div>
            <div>Retail Management System</div>
            <div style="border-top:1px dashed #000;margin:6px 0"></div>
        </div>
        <div id="rp-id"></div>
        <div id="rp-date"></div>
        <div id="rp-cashier"></div>
        <div style="border-top:1px dashed #000;margin:6px 0"></div>
        <div id="rp-items"></div>
        <div style="border-top:1px dashed #000;margin:6px 0"></div>
        <div id="rp-totals"></div>
        <div style="border-top:1px dashed #000;margin:8px 0;text-align:center">
            شکریه — Thank you
        </div>
    </div>
</div>

</div>{{-- /sl --}}
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
Alpine.data('salesPage', () => ({

    /* list */
    sales:       [],
    pagination:  {},
    loading:     true,
    search:      '',
    dateFrom:    '',
    dateTo:      '',
    filterMethod:'',
    filterStatus:'',
    filterCashier:'',
    tab:         'today',
    sortCol:     'created_at',
    sortDir:     'desc',
    currentPage: 1,

    /* detail */
    selected:     null,
    detailItems:  [],
    detailLoading:false,

    /* refund */
    showRefundModal: false,
    refundItems:     [],
    refundReason:    '',
    refundError:     '',
    saving:          false,

    /* urls */
    urls: {
        list:   '{{ route("pos.sales.index") }}',
        detail: '{{ url("pos/sales") }}',
        refund: '{{ route("pos.sales.refund") }}',
        export: '{{ route("pos.sales.export") }}',
        csrf:   document.querySelector('meta[name=csrf-token]').content,
    },

    /* computed */
    get refundTotal() {
        return this.refundItems
            .filter(i => i.selected)
            .reduce((s, i) => s + i.unit_price * i.refund_qty, 0);
    },

    /* ── Init ── */
    init() {
        const today = new Date().toISOString().split('T')[0];
        this.dateFrom = today;
        this.dateTo   = today;
        this.loadSales();
    },

    /* ── Load sales list ── */
    async loadSales() {
        this.loading = true;
        try {
            const p = new URLSearchParams({
                q:       this.search,
                from:    this.dateFrom,
                to:      this.dateTo,
                method:  this.filterMethod,
                status:  this.filterStatus,
                cashier: this.filterCashier,
                tab:     this.tab,
                sort:    this.sortCol,
                dir:     this.sortDir,
                page:    this.currentPage,
            });
            const r = await fetch(this.urls.list + '?' + p, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            const d = await r.json();
            this.sales      = d.data;
            this.pagination = d.meta;
        } catch(e) { console.error(e); }
        finally { this.loading = false; }
    },

    sort(col) {
        if (this.sortCol === col) this.sortDir = this.sortDir === 'asc' ? 'desc' : 'asc';
        else { this.sortCol = col; this.sortDir = 'desc'; }
        this.loadSales();
    },

    goPage(p) {
        if (p < 1 || p > this.pagination.last_page) return;
        this.currentPage = p;
        this.loadSales();
    },

    /* ── Detail panel ── */
    async openDetail(s) {
        this.selected    = s;
        this.detailItems = [];
        this.detailLoading = true;
        try {
            const r = await fetch(`${this.urls.detail}/${s.id}/items`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            this.detailItems = await r.json();
        } catch(e) { console.error(e); }
        finally { this.detailLoading = false; }
    },

    /* ── Refund ── */
    openRefund(s) {
        this.refundItems  = this.detailItems.map(i => ({
            ...i,
            selected:   false,
            refund_qty: i.quantity,
        }));
        this.refundReason = '';
        this.refundError  = '';
        this.showRefundModal = true;
    },

    async processRefund() {
        const selected = this.refundItems.filter(i => i.selected);
        if (!selected.length) { this.refundError = 'Select at least one item to refund.'; return; }
        if (!this.refundReason.trim()) { this.refundError = 'Reason is required.'; return; }

        // Validate quantities
        for (const item of selected) {
            if (item.refund_qty < 1 || item.refund_qty > item.quantity) {
                this.refundError = `Invalid quantity for "${item.product_name}".`; return;
            }
        }

        this.saving = true; this.refundError = '';
        try {
            const r = await fetch(this.urls.refund, {
                method:  'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.urls.csrf },
                body: JSON.stringify({
                    sale_id: this.selected.id,
                    items:   selected.map(i => ({ sale_item_id: i.id, qty: i.refund_qty })),
                    reason:  this.refundReason,
                })
            });
            const d = await r.json();
            if (d.success) {
                this.showRefundModal = false;
                this.selected = null;
                this.loadSales();
            } else {
                this.refundError = d.message ?? 'Refund failed.';
            }
        } catch(e) { this.refundError = 'Network error.'; }
        finally { this.saving = false; }
    },

    /* ── Receipt reprint ── */
    reprintReceipt(s) {
        if (!s) return;
        document.getElementById('rp-id').textContent      = 'Sale: ' + s.local_id;
        document.getElementById('rp-date').textContent    = s.date + ' ' + s.time;
        document.getElementById('rp-cashier').textContent = 'Cashier: ' + s.cashier;
        document.getElementById('rp-items').innerHTML     = this.detailItems.map(i =>
            `<div style="display:flex;justify-content:space-between">
                <span>${i.product_name} x${i.quantity}</span>
                <span>Af ${this.fmt(i.line_total)}</span>
            </div>`
        ).join('');
        document.getElementById('rp-totals').innerHTML = `
            <div style="display:flex;justify-content:space-between"><span>Total</span><span>Af ${this.fmt(s.total_amount)}</span></div>
            <div style="display:flex;justify-content:space-between"><span>Method</span><span>${s.payment_method}</span></div>
        `;

        const el = document.getElementById('receipt-print');
        el.style.display = 'block';
        window.print();
        el.style.display = 'none';
    },

    /* ── Export ── */
    exportCsv() {
        const p = new URLSearchParams({ from: this.dateFrom, to: this.dateTo, tab: this.tab, method: this.filterMethod, status: this.filterStatus });
        window.location.href = this.urls.export + '?' + p;
    },

    /* ── Helpers ── */
    fmt(n) {
        return Number(n || 0).toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
    },
}));
});
</script>

<style>
@media print {
    body > *:not(#receipt-print) { display: none !important; }
    #receipt-print { display: block !important; }
}
</style>
@endpush
