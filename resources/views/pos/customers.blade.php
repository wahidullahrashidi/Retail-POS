@extends('layouts.app')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Abril+Fatface&family=Mulish:wght@300;400;500;600;700&family=Inconsolata:wght@400;500;600&display=swap" rel="stylesheet">
<style>
/* ═══════════════════════════════════════
   TOKENS
═══════════════════════════════════════ */
:root {
    --bg:        #f1f3f8;
    --surface:   #ffffff;
    --s2:        #f7f8fc;
    --s3:        #eceff6;
    --border:    #dde1ee;
    --border2:   #c8cede;
    --ink:       #1a1d2e;
    --ink2:      #454868;
    --ink3:      #8890ae;
    --ink4:      #c0c6da;
    --blue:      #3563e9;
    --blue2:     #2450cc;
    --bdim:      rgba(53,99,233,.09);
    --bmid:      rgba(53,99,233,.18);
    --indigo:    #4f46e5;
    --idim:      rgba(79,70,229,.09);
    --green:     #16a34a;
    --gdim:      rgba(22,163,74,.1);
    --red:       #dc2626;
    --rdim:      rgba(220,38,38,.09);
    --amber:     #d97706;
    --adim:      rgba(217,119,6,.1);
    --teal:      #0891b2;
    --tdim:      rgba(8,145,178,.1);
    --mono:      'Inconsolata', monospace;
    --body:      'Mulish', sans-serif;
    --display:   'Abril Fatface', serif;
    --r:         10px;
    --rsm:       6px;
    --rlg:       16px;
    --sh:        0 1px 4px rgba(0,0,0,.06), 0 1px 2px rgba(0,0,0,.04);
    --shmd:      0 4px 18px rgba(0,0,0,.08), 0 2px 6px rgba(0,0,0,.04);
    --shlg:      0 16px 48px rgba(0,0,0,.12), 0 4px 14px rgba(0,0,0,.06);
}

/* ═══════════════════════════════════════
   BASE
═══════════════════════════════════════ */
.cu * { box-sizing: border-box; }
.cu {
    font-family: var(--body);
    background: var(--bg);
    min-height: 100vh;
    color: var(--ink);
}
[x-cloak] { display: none !important; }

/* ═══════════════════════════════════════
   TOPBAR
═══════════════════════════════════════ */
.cu-top {
    background: var(--surface);
    border-bottom: 1px solid var(--border);
    height: 56px;
    display: flex; align-items: center; justify-content: space-between;
    padding: 0 1.75rem;
    position: sticky; top: 0; z-index: 80;
    box-shadow: var(--sh);
}
.cu-title {
    font-family: var(--display);
    font-size: 22px;
    color: var(--ink);
    letter-spacing: -.3px;
}
.cu-title span { color: var(--blue); }
.top-right { display: flex; align-items: center; gap: 8px; }

/* ═══════════════════════════════════════
   BUTTONS
═══════════════════════════════════════ */
.btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: var(--rsm); font-family: var(--body); font-size: 13px; font-weight: 600; border: none; cursor: pointer; transition: all .17s; white-space: nowrap; }
.btn-primary { background: var(--blue); color: #fff; box-shadow: 0 2px 10px rgba(53,99,233,.3); }
.btn-primary:hover { background: var(--blue2); transform: translateY(-1px); box-shadow: 0 5px 16px rgba(53,99,233,.38); }
.btn-ghost { background: var(--s2); border: 1px solid var(--border); color: var(--ink2); }
.btn-ghost:hover { background: var(--s3); color: var(--ink); }
.btn-danger { background: var(--rdim); border: 1px solid rgba(220,38,38,.2); color: var(--red); }
.btn-danger:hover { background: var(--red); color: #fff; }
.btn-teal { background: var(--tdim); border: 1px solid rgba(8,145,178,.2); color: var(--teal); }
.btn-teal:hover { background: var(--teal); color: #fff; }
.btn-sm { padding: 5px 11px; font-size: 12px; }
.btn:active { transform: scale(.97); }
.btn:disabled { opacity: .45; cursor: not-allowed; transform: none !important; }

/* ═══════════════════════════════════════
   STAT STRIP
═══════════════════════════════════════ */
.stat-strip {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 1rem;
    padding: 1.25rem 1.75rem .75rem;
}
.stat-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--r);
    padding: 1.1rem 1.2rem;
    position: relative; overflow: hidden;
    cursor: default;
    transition: all .2s;
}
.stat-card::before {
    content: '';
    position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: var(--ac, var(--blue));
    transform: scaleX(0); transform-origin: left;
    transition: transform .25s;
}
.stat-card:hover { box-shadow: var(--shmd); transform: translateY(-2px); }
.stat-card:hover::before { transform: scaleX(1); }
.sc-label { font-size: 10px; font-weight: 700; color: var(--ink3); text-transform: uppercase; letter-spacing: .1em; margin-bottom: 8px; display: flex; align-items: center; justify-content: space-between; }
.sc-icon { font-size: 15px; }
.sc-val { font-family: var(--mono); font-size: 24px; font-weight: 600; color: var(--ink); line-height: 1; letter-spacing: -.5px; }
.sc-sub { font-size: 11px; color: var(--ink3); margin-top: 5px; }

/* ═══════════════════════════════════════
   TOOLBAR
═══════════════════════════════════════ */
.cu-toolbar {
    display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
    padding: .75rem 1.75rem 1rem;
}
.search-box { position: relative; flex: 1; min-width: 220px; max-width: 340px; }
.search-box i { position: absolute; left: 11px; top: 50%; transform: translateY(-50%); color: var(--ink3); font-size: 13px; pointer-events: none; }
.cu-search { width: 100%; padding: 9px 14px 9px 34px; background: var(--surface); border: 1.5px solid var(--border); border-radius: var(--rsm); font-family: var(--body); font-size: 13px; color: var(--ink); outline: none; transition: border .15s, box-shadow .15s; }
.cu-search:focus { border-color: var(--blue); box-shadow: 0 0 0 3px var(--bdim); background: #fff; }
.cu-search::placeholder { color: var(--ink4); }
.filter-sel { padding: 9px 14px; background: var(--surface); border: 1.5px solid var(--border); border-radius: var(--rsm); font-family: var(--body); font-size: 13px; color: var(--ink2); outline: none; cursor: pointer; transition: border .15s; }
.filter-sel:focus { border-color: var(--blue); }
.tab-strip { display: flex; gap: 4px; margin-left: auto; }
.tab-btn { padding: 7px 14px; border: 1px solid var(--border); border-radius: var(--rsm); background: var(--surface); font-family: var(--body); font-size: 12px; font-weight: 600; cursor: pointer; color: var(--ink3); transition: all .15s; }
.tab-btn.active { background: var(--blue); color: #fff; border-color: var(--blue); }

/* ═══════════════════════════════════════
   MAIN GRID (table + detail panel)
═══════════════════════════════════════ */
.cu-main {
    display: grid;
    grid-template-columns: 1fr;
    gap: 0;
    padding: 0 1.75rem 2rem;
    transition: grid-template-columns .25s;
}
.cu-main.panel-open {
    grid-template-columns: 1fr 380px;
    gap: 1.25rem;
}

/* ═══════════════════════════════════════
   CUSTOMER TABLE
═══════════════════════════════════════ */
.table-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--r);
    overflow: hidden;
    box-shadow: var(--sh);
}
.cu-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.cu-table thead { background: var(--s2); border-bottom: 1.5px solid var(--border); }
.cu-table th { padding: 11px 14px; text-align: left; font-size: 10px; font-weight: 700; color: var(--ink3); text-transform: uppercase; letter-spacing: .08em; white-space: nowrap; cursor: pointer; user-select: none; transition: color .15s; }
.cu-table th:hover { color: var(--blue); }
.cu-table th i { margin-left: 4px; opacity: .5; }
.cu-table td { padding: 12px 14px; border-bottom: 1px solid var(--border); vertical-align: middle; color: var(--ink); }
.cu-table tbody tr:last-child td { border-bottom: none; }
.cu-table tbody tr { transition: background .12s; cursor: pointer; }
.cu-table tbody tr:hover { background: var(--bdim); }
.cu-table tbody tr.selected { background: var(--bdim); border-left: 3px solid var(--blue); }

/* customer cell */
.cust-cell { display: flex; align-items: center; gap: 10px; }
.cust-av {
    width: 36px; height: 36px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 13px; font-weight: 700; color: #fff;
    flex-shrink: 0; font-family: var(--body);
}
.cust-name { font-weight: 600; font-size: 13px; color: var(--ink); }
.cust-city { font-size: 11px; color: var(--ink3); margin-top: 2px; }
.cell-mono { font-family: var(--mono); font-size: 12px; color: var(--ink2); }
.cell-right { text-align: right; }

/* status / loan pills */
.pill { display: inline-block; padding: 3px 9px; border-radius: 99px; font-size: 10px; font-weight: 700; letter-spacing: .04em; }
.pill-green  { background: var(--gdim); color: var(--green); border: 1px solid rgba(22,163,74,.2); }
.pill-red    { background: var(--rdim); color: var(--red);   border: 1px solid rgba(220,38,38,.2); }
.pill-amber  { background: var(--adim); color: var(--amber); border: 1px solid rgba(217,119,6,.2); }
.pill-blue   { background: var(--bdim); color: var(--blue);  border: 1px solid var(--bmid); }
.pill-gray   { background: var(--s3);   color: var(--ink3);  border: 1px solid var(--border); }

/* loan balance cell */
.loan-bal { font-family: var(--mono); font-size: 13px; font-weight: 600; }
.loan-bal.has-loan  { color: var(--red); }
.loan-bal.no-loan   { color: var(--ink4); }

/* row actions */
.row-acts { display: flex; gap: 5px; opacity: 0; transition: opacity .15s; }
.cu-table tbody tr:hover .row-acts { opacity: 1; }

/* ═══════════════════════════════════════
   EMPTY / LOADING
═══════════════════════════════════════ */
.empty-state { text-align: center; padding: 4rem 2rem; color: var(--ink3); }
.empty-state i { font-size: 38px; margin-bottom: 12px; display: block; color: var(--ink4); }
.empty-state p { font-size: 13px; line-height: 1.7; }
.loading-state { text-align: center; padding: 3rem; color: var(--ink3); }

/* ═══════════════════════════════════════
   PAGINATION
═══════════════════════════════════════ */
.pag-row { display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; border-top: 1px solid var(--border); background: var(--s2); }
.pag-info { font-size: 12px; color: var(--ink3); }
.pag-btns { display: flex; gap: 4px; }
.pag-btn { width: 30px; height: 30px; border-radius: var(--rsm); border: 1px solid var(--border); background: var(--surface); cursor: pointer; font-family: var(--mono); font-size: 12px; color: var(--ink2); display: flex; align-items: center; justify-content: center; transition: all .12s; }
.pag-btn:hover { background: var(--bdim); border-color: var(--blue); color: var(--blue); }
.pag-btn.active { background: var(--blue); color: #fff; border-color: var(--blue); }
.pag-btn:disabled { opacity: .3; cursor: not-allowed; }

/* ═══════════════════════════════════════
   DETAIL PANEL
═══════════════════════════════════════ */
.detail-panel {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--r);
    box-shadow: var(--sh);
    display: flex; flex-direction: column;
    max-height: calc(100vh - 160px);
    position: sticky; top: 72px;
    overflow: hidden;
    animation: panelIn .2s cubic-bezier(.2,.8,.36,1);
}
@keyframes panelIn { from { opacity: 0; transform: translateX(12px); } to { opacity: 1; transform: none; } }

.dp-head {
    padding: 1.1rem 1.25rem;
    border-bottom: 1px solid var(--border);
    display: flex; align-items: center; justify-content: space-between;
    flex-shrink: 0;
}
.dp-close { background: none; border: none; cursor: pointer; color: var(--ink3); font-size: 16px; }
.dp-close:hover { color: var(--ink); }

.dp-hero {
    padding: 1.25rem;
    text-align: center;
    border-bottom: 1px solid var(--border);
    flex-shrink: 0;
}
.dp-avatar {
    width: 64px; height: 64px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 24px; font-weight: 700; color: #fff;
    margin: 0 auto 10px;
    box-shadow: 0 4px 14px rgba(0,0,0,.15);
}
.dp-name { font-family: var(--display); font-size: 18px; color: var(--ink); margin-bottom: 3px; }
.dp-meta { font-size: 12px; color: var(--ink3); display: flex; align-items: center; justify-content: center; gap: 8px; }
.dp-meta span { display: flex; align-items: center; gap: 4px; }

.dp-body { flex: 1; overflow-y: auto; padding: 1rem 1.25rem; }
.dp-body::-webkit-scrollbar { width: 4px; }
.dp-body::-webkit-scrollbar-thumb { background: var(--border); border-radius: 2px; }

/* detail sections */
.dp-section { margin-bottom: 1.25rem; }
.dp-section-title { font-size: 10px; font-weight: 700; color: var(--ink3); text-transform: uppercase; letter-spacing: .1em; margin-bottom: .6rem; display: flex; align-items: center; gap: 6px; }
.dp-section-title i { color: var(--blue); }
.dp-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
.dp-field { background: var(--s2); border: 1px solid var(--border); border-radius: var(--rsm); padding: 8px 10px; }
.dp-field-label { font-size: 10px; color: var(--ink3); text-transform: uppercase; letter-spacing: .06em; margin-bottom: 3px; }
.dp-field-val { font-size: 13px; font-weight: 500; color: var(--ink); }
.dp-field-val.mono { font-family: var(--mono); }
.dp-field.full { grid-column: span 2; }

/* loan summary bar */
.loan-bar {
    display: flex; justify-content: space-between; align-items: center;
    padding: 10px 12px;
    border-radius: var(--rsm);
    margin-bottom: .75rem;
}
.loan-bar.active { background: var(--rdim); border: 1px solid rgba(220,38,38,.2); }
.loan-bar.clear  { background: var(--gdim); border: 1px solid rgba(22,163,74,.2); }
.lb-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; }
.loan-bar.active .lb-label { color: var(--red); }
.loan-bar.clear  .lb-label { color: var(--green); }
.lb-val { font-family: var(--mono); font-size: 18px; font-weight: 600; }
.loan-bar.active .lb-val { color: var(--red); }
.loan-bar.clear  .lb-val { color: var(--green); }

/* recent transactions mini list */
.mini-txn { display: flex; align-items: center; justify-content: space-between; padding: 8px 10px; background: var(--s2); border: 1px solid var(--border); border-radius: var(--rsm); margin-bottom: 5px; font-size: 12px; transition: background .12s; }
.mini-txn:hover { background: var(--bdim); }
.mt-left { display: flex; flex-direction: column; gap: 2px; }
.mt-id   { font-family: var(--mono); font-size: 11px; color: var(--blue); }
.mt-date { font-size: 10px; color: var(--ink3); }
.mt-right { text-align: right; }
.mt-amount { font-family: var(--mono); font-size: 13px; font-weight: 600; color: var(--ink); }
.mt-method { font-size: 10px; color: var(--ink3); margin-top: 1px; }

.dp-foot {
    padding: 1rem 1.25rem;
    border-top: 1px solid var(--border);
    display: flex; gap: 6px;
    flex-shrink: 0;
}

/* ═══════════════════════════════════════
   MODALS
═══════════════════════════════════════ */
.modal-overlay { position: fixed; inset: 0; background: rgba(26,29,46,.45); backdrop-filter: blur(5px); z-index: 200; display: flex; align-items: center; justify-content: center; padding: 1rem; animation: fadeIn .15s ease; }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
.modal-card { background: var(--surface); border-radius: var(--rlg); box-shadow: var(--shlg); width: 100%; max-height: 92vh; display: flex; flex-direction: column; animation: slideUp .18s cubic-bezier(.2,.8,.36,1); }
@keyframes slideUp { from { opacity: 0; transform: translateY(18px); } to { opacity: 1; transform: none; } }
.modal-sm { max-width: 460px; }
.modal-md { max-width: 620px; }
.modal-head { padding: 1.1rem 1.5rem; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; flex-shrink: 0; }
.modal-title { font-family: var(--display); font-size: 18px; color: var(--ink); }
.modal-close { background: none; border: none; cursor: pointer; color: var(--ink3); font-size: 18px; transition: color .15s; }
.modal-close:hover { color: var(--ink); }
.modal-body { padding: 1.25rem 1.5rem; overflow-y: auto; flex: 1; }
.modal-foot { padding: 1rem 1.5rem; border-top: 1px solid var(--border); display: flex; gap: 8px; justify-content: flex-end; flex-shrink: 0; }

/* ═══════════════════════════════════════
   FORM ELEMENTS
═══════════════════════════════════════ */
.form-grid { display: grid; gap: .9rem; }
.form-2 { grid-template-columns: 1fr 1fr; }
.field-label { display: block; font-size: 11px; font-weight: 700; color: var(--ink2); text-transform: uppercase; letter-spacing: .06em; margin-bottom: 5px; }
.field-req { color: var(--red); }
.field-input { width: 100%; padding: 9px 12px; background: var(--s2); border: 1.5px solid var(--border); border-radius: var(--rsm); font-family: var(--body); font-size: 13px; color: var(--ink); outline: none; transition: border .15s, box-shadow .15s; }
.field-input:focus { border-color: var(--blue); box-shadow: 0 0 0 3px var(--bdim); background: #fff; }
.field-input::placeholder { color: var(--ink4); }
textarea.field-input { resize: vertical; min-height: 70px; }
.field-hint { font-size: 11px; color: var(--ink3); margin-top: 3px; }
.form-err { padding: 9px 12px; background: var(--rdim); border: 1px solid rgba(220,38,38,.2); border-radius: var(--rsm); font-size: 12px; color: var(--red); margin-top: .75rem; }

/* ═══════════════════════════════════════
   PAYMENT MODAL SPECIFICS
═══════════════════════════════════════ */
.loan-detail-card {
    background: var(--rdim); border: 1px solid rgba(220,38,38,.2);
    border-radius: var(--rsm); padding: 12px 14px; margin-bottom: 1rem;
}
.ldc-row { display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 4px; color: var(--ink2); }
.ldc-row.main { font-size: 15px; font-weight: 700; color: var(--red); margin-top: 6px; }
.ldc-row.main span { font-family: var(--mono); }

.payment-history { margin-top: .75rem; }
.ph-item { display: flex; justify-content: space-between; padding: 7px 10px; background: var(--s2); border: 1px solid var(--border); border-radius: var(--rsm); margin-bottom: 4px; font-size: 12px; }
.ph-amount { font-family: var(--mono); font-weight: 600; color: var(--green); }
</style>
@endpush

@section('content')
<div class="cu" x-data="customersPage()" x-init="init()">

{{-- ════ TOPBAR ════ --}}
<div class="cu-top">
    <div class="cu-title">Afghan <span>POS</span> — Customers</div>
    <div class="top-right">
        <button class="btn btn-ghost" @click="exportCsv()">
            <i class="fas fa-file-export"></i> Export
        </button>
        <button class="btn btn-primary"
                @click="$store.customerModal.open(c => { customers.unshift(c); stats.total++; })">
            <i class="fas fa-user-plus"></i> New Customer
        </button>
    </div>
</div>

{{-- ════ STATS ════ --}}
<div class="stat-strip">
    <div class="stat-card" style="--ac:var(--blue)">
        <div class="sc-label">Total Customers <span class="sc-icon" style="color:var(--blue)"><i class="fas fa-users"></i></span></div>
        <div class="sc-val">{{ number_format($stats['total'] ?? 0) }}</div>
        <div class="sc-sub">{{ $stats['active'] ?? 0 }} active</div>
    </div>
    <div class="stat-card" style="--ac:var(--red)">
        <div class="sc-label">With Active Loans <span class="sc-icon" style="color:var(--red)"><i class="fas fa-file-invoice-dollar"></i></span></div>
        <div class="sc-val" style="color:var(--red)">{{ number_format($stats['with_loans'] ?? 0) }}</div>
        <div class="sc-sub">customers with balance</div>
    </div>
    <div class="stat-card" style="--ac:var(--amber)">
        <div class="sc-label">Total Outstanding <span class="sc-icon" style="color:var(--amber)"><i class="fas fa-coins"></i></span></div>
        <div class="sc-val" style="font-size:18px;color:var(--amber)">Af {{ number_format($stats['total_outstanding'] ?? 0) }}</div>
        <div class="sc-sub">across all active loans</div>
    </div>
    <div class="stat-card" style="--ac:var(--red)">
        <div class="sc-label">Overdue Loans <span class="sc-icon" style="color:var(--red)"><i class="fas fa-triangle-exclamation"></i></span></div>
        <div class="sc-val" style="color:var(--red)">{{ number_format($stats['overdue'] ?? 0) }}</div>
        <div class="sc-sub">past due date</div>
    </div>
    <div class="stat-card" style="--ac:var(--green)">
        <div class="sc-label">Lifetime Sales <span class="sc-icon" style="color:var(--green)"><i class="fas fa-chart-line"></i></span></div>
        <div class="sc-val" style="font-size:18px;color:var(--green)">Af {{ number_format($stats['lifetime_sales'] ?? 0) }}</div>
        <div class="sc-sub">from registered customers</div>
    </div>
</div>

{{-- ════ TOOLBAR ════ --}}
<div class="cu-toolbar">
    <div class="search-box">
        <i class="fas fa-search"></i>
        <input class="cu-search" type="text" x-model="search"
               @input.debounce.350ms="loadCustomers()"
               placeholder="Name, phone, city…">
    </div>
    <select class="filter-sel" x-model="filterLoan" @change="loadCustomers()">
        <option value="">All Customers</option>
        <option value="has_loan">Has Active Loan</option>
        <option value="overdue">Overdue Loan</option>
        <option value="no_loan">No Loan</option>
    </select>
    <select class="filter-sel" x-model="filterCity" @change="loadCustomers()">
        <option value="">All Cities</option>
        @foreach($cities ?? [] as $city)
            <option value="{{ $city }}">{{ $city }}</option>
        @endforeach
    </select>
    <div class="tab-strip">
        <button type="button" class="tab-btn" :class="tab==='all'?'active':''" @click="tab='all';loadCustomers()">All</button>
        <button type="button" class="tab-btn" :class="tab==='active'?'active':''" @click="tab='active';loadCustomers()">Active</button>
        <button type="button" class="tab-btn" :class="tab==='inactive'?'active':''" @click="tab='inactive';loadCustomers()">Inactive</button>
    </div>
</div>

{{-- ════ MAIN ════ --}}
<div class="cu-main" :class="selected ? 'panel-open' : ''" style="align-items:start">

    {{-- TABLE --}}
    <div class="table-card">

        {{-- loading --}}
        <div class="loading-state" x-show="loading">
            <i class="fas fa-spinner fa-spin" style="font-size:20px"></i>
        </div>

        <div x-show="!loading">
            {{-- empty --}}
            <div class="empty-state" x-show="customers.length === 0">
                <i class="fas fa-users-slash"></i>
                <p>No customers found.<br>Try a different search or add a new customer.</p>
            </div>

            <table class="cu-table" x-show="customers.length > 0">
                <thead>
                    <tr>
                        <th @click="sort('name')">Customer <i class="fas fa-sort"></i></th>
                        <th @click="sort('phone')">Phone <i class="fas fa-sort"></i></th>
                        <th>City</th>
                        <th @click="sort('loan_balance')" class="cell-right">Loan Balance <i class="fas fa-sort"></i></th>
                        <th class="cell-right">Total Purchases</th>
                        <th>Status</th>
                        <th>Last Sale</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="c in customers" :key="c.id">
                        <tr :class="selected?.id === c.id ? 'selected' : ''"
                            @click="openDetail(c)">
                            <td>
                                <div class="cust-cell">
                                    <div class="cust-av" :style="`background:${avatarColor(c.name)}`"
                                         x-text="initials(c.name)"></div>
                                    <div>
                                        <div class="cust-name" x-text="c.name"></div>
                                        <div class="cust-city" x-text="c.city || '—'"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="cell-mono" x-text="c.phone"></td>
                            <td style="color:var(--ink2);font-size:13px" x-text="c.city || '—'"></td>
                            <td class="cell-right">
                                <span class="loan-bal" :class="c.loan_balance > 0 ? 'has-loan' : 'no-loan'"
                                      x-text="c.loan_balance > 0 ? 'Af ' + fmt(c.loan_balance) : '—'"></span>
                            </td>
                            <td class="cell-right">
                                <span style="font-family:var(--mono);font-size:13px;font-weight:500;color:var(--ink)"
                                      x-text="'Af ' + fmt(c.total_purchases)"></span>
                            </td>
                            <td>
                                <span class="pill" :class="c.is_active ? 'pill-green' : 'pill-gray'"
                                      x-text="c.is_active ? 'Active' : 'Inactive'"></span>
                            </td>
                            <td style="font-size:12px;color:var(--ink3)"
                                x-text="c.last_sale_at ? relativeTime(c.last_sale_at) : 'Never'"></td>
                            <td @click.stop>
                                <div class="row-acts">
                                    <button type="button" class="btn btn-ghost btn-sm"
                                            @click="openEdit(c)" title="Edit">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <button type="button" class="btn btn-teal btn-sm"
                                            @click="openPayment(c)"
                                            x-show="c.loan_balance > 0"
                                            title="Record Payment">
                                        <i class="fas fa-money-bill-wave"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm"
                                            @click="toggleActive(c)" title="Toggle Active">
                                        <i class="fas fa-power-off"></i>
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
                    of <span x-text="pagination.total"></span>
                </div>
                <div class="pag-btns">
                    <button class="pag-btn" @click="goPage(pagination.current_page - 1)" :disabled="pagination.current_page === 1">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <template x-for="p in pagination.last_page" :key="p">
                        <button class="pag-btn" :class="p === pagination.current_page ? 'active' : ''"
                                @click="goPage(p)" x-text="p"></button>
                    </template>
                    <button class="pag-btn" @click="goPage(pagination.current_page + 1)" :disabled="pagination.current_page === pagination.last_page">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ════ DETAIL PANEL ════ --}}
    <div class="detail-panel" x-show="selected" x-cloak>

        <div class="dp-head">
            <span style="font-size:12px;font-weight:700;color:var(--ink3);text-transform:uppercase;letter-spacing:.08em">Customer Detail</span>
            <button class="dp-close" @click="selected=null"><i class="fas fa-times"></i></button>
        </div>

        {{-- Hero --}}
        <div class="dp-hero">
            <div class="dp-avatar" :style="`background:${avatarColor(selected?.name)}`"
                 x-text="initials(selected?.name)"></div>
            <div class="dp-name" x-text="selected?.name"></div>
            <div class="dp-meta">
                <span><i class="fas fa-phone"></i> <span x-text="selected?.phone"></span></span>
                <span x-show="selected?.city">· <i class="fas fa-location-dot"></i> <span x-text="selected?.city"></span></span>
            </div>
            <div style="margin-top:8px">
                <span class="pill" :class="selected?.is_active ? 'pill-green' : 'pill-gray'"
                      x-text="selected?.is_active ? 'Active' : 'Inactive'"></span>
            </div>
        </div>

        <div class="dp-body">

            {{-- Loan summary --}}
            <div class="dp-section">
                <div class="dp-section-title"><i class="fas fa-file-invoice-dollar"></i> Loan Status</div>

                <div class="loan-bar" :class="selected?.loan_balance > 0 ? 'active' : 'clear'">
                    <div>
                        <div class="lb-label" x-text="selected?.loan_balance > 0 ? 'Outstanding Balance' : 'No Active Loan'"></div>
                        <div style="font-size:11px;margin-top:2px"
                             :style="selected?.loan_balance > 0 ? 'color:var(--red)' : 'color:var(--green)'"
                             x-text="selected?.loan_balance > 0 ? (selected?.loan_count + ' loan(s)') : 'All paid up'"></div>
                    </div>
                    <div class="lb-val" x-text="'Af ' + fmt(selected?.loan_balance || 0)"></div>
                </div>

                <button type="button" class="btn btn-teal" style="width:100%"
                        x-show="selected?.loan_balance > 0"
                        @click="openPayment(selected)">
                    <i class="fas fa-money-bill-wave"></i> Record Loan Payment
                </button>
            </div>

            {{-- Info grid --}}
            <div class="dp-section">
                <div class="dp-section-title"><i class="fas fa-address-card"></i> Contact Info</div>
                <div class="dp-grid">
                    <div class="dp-field">
                        <div class="dp-field-label">Primary Phone</div>
                        <div class="dp-field-val mono" x-text="selected?.phone || '—'"></div>
                    </div>
                    <div class="dp-field">
                        <div class="dp-field-label">Secondary Phone</div>
                        <div class="dp-field-val mono" x-text="selected?.phone_secondary || '—'"></div>
                    </div>
                    <div class="dp-field full">
                        <div class="dp-field-label">Address</div>
                        <div class="dp-field-val" x-text="selected?.address || '—'"></div>
                    </div>
                    <div class="dp-field">
                        <div class="dp-field-label">City</div>
                        <div class="dp-field-val" x-text="selected?.city || '—'"></div>
                    </div>
                    <div class="dp-field">
                        <div class="dp-field-label">Credit Limit</div>
                        <div class="dp-field-val mono"
                             x-text="selected?.credit_limit ? 'Af ' + fmt(selected.credit_limit) : 'None'"></div>
                    </div>
                    <div class="dp-field full">
                        <div class="dp-field-label">Notes</div>
                        <div class="dp-field-val" style="font-size:12px" x-text="selected?.notes || '—'"></div>
                    </div>
                </div>
            </div>

            {{-- Purchase summary --}}
            <div class="dp-section">
                <div class="dp-section-title"><i class="fas fa-receipt"></i> Purchase Summary</div>
                <div class="dp-grid">
                    <div class="dp-field">
                        <div class="dp-field-label">Total Spent</div>
                        <div class="dp-field-val mono" style="color:var(--blue)"
                             x-text="'Af ' + fmt(selected?.total_purchases || 0)"></div>
                    </div>
                    <div class="dp-field">
                        <div class="dp-field-label">Transactions</div>
                        <div class="dp-field-val mono" x-text="selected?.sale_count || 0"></div>
                    </div>
                    <div class="dp-field full">
                        <div class="dp-field-label">Last Purchase</div>
                        <div class="dp-field-val" x-text="selected?.last_sale_at ? relativeTime(selected.last_sale_at) : 'Never'"></div>
                    </div>
                </div>
            </div>

            {{-- Recent transactions --}}
            <div class="dp-section">
                <div class="dp-section-title"><i class="fas fa-clock-rotate-left"></i> Recent Sales</div>
                <div x-show="detailLoading" style="text-align:center;padding:1rem;color:var(--ink3);font-size:12px">
                    <i class="fas fa-spinner fa-spin"></i>
                </div>
                <div x-show="!detailLoading">
                    <div x-show="recentSales.length === 0"
                         style="text-align:center;padding:1rem;color:var(--ink4);font-size:12px">
                        No sales recorded yet.
                    </div>
                    <template x-for="s in recentSales" :key="s.id">
                        <div class="mini-txn">
                            <div class="mt-left">
                                <div class="mt-id" x-text="s.local_id"></div>
                                <div class="mt-date" x-text="s.created_at"></div>
                            </div>
                            <div class="mt-right">
                                <div class="mt-amount" x-text="'Af ' + fmt(s.total_amount)"></div>
                                <div class="mt-method">
                                    <span class="pill" :class="s.payment_method==='loan'?'pill-amber':'pill-green'"
                                          x-text="s.payment_method"></span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

        </div>

        <div class="dp-foot">
            <button type="button" class="btn btn-ghost" style="flex:1" @click="openEdit(selected)">
                <i class="fas fa-pen"></i> Edit
            </button>
            <button type="button" class="btn btn-danger" @click="toggleActive(selected)">
                <i class="fas fa-power-off"></i>
                <span x-text="selected?.is_active ? 'Deactivate' : 'Activate'"></span>
            </button>
        </div>
    </div>

</div>{{-- /cu-main --}}

{{-- ═══════════════════════════════════════
     MODAL: ADD / EDIT CUSTOMER
═══════════════════════════════════════ --}}
<div class="modal-overlay" x-show="showEditModal" x-cloak @click.self="showEditModal=false">
    <div class="modal-card modal-md">
        <div class="modal-head">
            <div class="modal-title" x-text="editingCustomer ? 'Edit Customer' : 'New Customer'"></div>
            <button class="modal-close" @click="showEditModal=false"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <div class="form-grid form-2">
                <div>
                    <label class="field-label">Full Name <span class="field-req">*</span></label>
                    <input type="text" class="field-input" x-model="cf.name" placeholder="Customer full name">
                </div>
                <div>
                    <label class="field-label">Primary Phone <span class="field-req">*</span></label>
                    <input type="text" class="field-input" x-model="cf.phone" placeholder="07XX XXX XXXX">
                </div>
                <div>
                    <label class="field-label">Secondary Phone</label>
                    <input type="text" class="field-input" x-model="cf.phone_secondary" placeholder="Optional">
                </div>
                <div>
                    <label class="field-label">City</label>
                    <input type="text" class="field-input" x-model="cf.city" placeholder="Kabul, Kandahar…">
                </div>
                <div style="grid-column:span 2">
                    <label class="field-label">Address</label>
                    <input type="text" class="field-input" x-model="cf.address" placeholder="Street address">
                </div>
                <div>
                    <label class="field-label">Credit Limit (Af)</label>
                    <input type="number" class="field-input" x-model.number="cf.credit_limit" placeholder="0 = unlimited" min="0">
                    <div class="field-hint">Max loan amount allowed</div>
                </div>
                <div>
                    <label class="field-label">Status</label>
                    <select class="field-input" x-model="cf.is_active">
                        <option :value="true">Active</option>
                        <option :value="false">Inactive</option>
                    </select>
                </div>
                <div style="grid-column:span 2">
                    <label class="field-label">Notes</label>
                    <textarea class="field-input" x-model="cf.notes" placeholder="Optional notes about this customer…"></textarea>
                </div>
            </div>
            <div class="form-err" x-show="formError" x-text="formError" x-cloak></div>
        </div>
        <div class="modal-foot">
            <button type="button" class="btn btn-ghost" @click="showEditModal=false">Cancel</button>
            <button type="button" class="btn btn-primary" @click="saveCustomer()" :disabled="saving">
                <i class="fas fa-spinner fa-spin" x-show="saving"></i>
                <span x-text="saving ? 'Saving…' : (editingCustomer ? 'Update Customer' : 'Add Customer')"></span>
            </button>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════
     MODAL: RECORD LOAN PAYMENT
═══════════════════════════════════════ --}}
<div class="modal-overlay" x-show="showPaymentModal" x-cloak @click.self="showPaymentModal=false">
    <div class="modal-card modal-sm">
        <div class="modal-head">
            <div class="modal-title">Record Loan Payment</div>
            <button class="modal-close" @click="showPaymentModal=false"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">

            {{-- Loan summary --}}
            <div class="loan-detail-card">
                <div class="ldc-row">
                    <span>Customer</span>
                    <span style="font-weight:600" x-text="paymentTarget?.name"></span>
                </div>
                <div class="ldc-row">
                    <span>Original Loan</span>
                    <span style="font-family:var(--mono)" x-text="'Af ' + fmt(activeLoan?.original_amount || 0)"></span>
                </div>
                <div class="ldc-row">
                    <span>Already Paid</span>
                    <span style="font-family:var(--mono);color:var(--green)" x-text="'Af ' + fmt(activeLoan?.amount_paid || 0)"></span>
                </div>
                <div class="ldc-row main">
                    <span>Remaining</span>
                    <span x-text="'Af ' + fmt(activeLoan?.remaining_balance || 0)"></span>
                </div>
            </div>

            {{-- Payment amount --}}
            <div style="margin-bottom:.9rem">
                <label class="field-label">Payment Amount (Af) <span class="field-req">*</span></label>
                <input type="number" class="field-input"
                       x-model.number="pf.amount"
                       :max="activeLoan?.remaining_balance"
                       placeholder="0" min="0"
                       style="font-family:var(--mono);font-size:16px">
                <div class="field-hint">
                    Remaining after this payment:
                    <strong style="font-family:var(--mono);color:var(--red)"
                            x-text="'Af ' + fmt(Math.max(0,(activeLoan?.remaining_balance||0) - (pf.amount||0)))">
                    </strong>
                </div>
            </div>

            <div style="margin-bottom:.9rem">
                <label class="field-label">Notes</label>
                <textarea class="field-input" x-model="pf.notes" rows="2" placeholder="Optional payment note…"></textarea>
            </div>

            {{-- Payment history --}}
            <div class="payment-history" x-show="loanPayments.length > 0" x-cloak>
                <div style="font-size:10px;font-weight:700;color:var(--ink3);text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px">
                    Payment History
                </div>
                <template x-for="p in loanPayments" :key="p.id">
                    <div class="ph-item">
                        <div>
                            <div style="font-family:var(--mono);font-size:11px;color:var(--ink3)" x-text="p.receipt_number"></div>
                            <div style="font-size:11px;color:var(--ink3)" x-text="p.created_at"></div>
                        </div>
                        <span class="ph-amount" x-text="'Af ' + fmt(p.amount)"></span>
                    </div>
                </template>
            </div>

            <div class="form-err" x-show="formError" x-text="formError" x-cloak></div>
        </div>
        <div class="modal-foot">
            <button type="button" class="btn btn-ghost" @click="showPaymentModal=false">Cancel</button>
            <button type="button" class="btn btn-primary" @click="savePayment()" :disabled="saving">
                <i class="fas fa-spinner fa-spin" x-show="saving"></i>
                <span x-text="saving ? 'Saving…' : 'Record Payment'"></span>
            </button>
        </div>
    </div>
</div>

</div>{{-- /cu --}}
@endsection

@push('scripts')
<script>
function customersPage() {
    return {
        /* list */
        customers:   [],
        pagination:  {},
        loading:     true,
        search:      '',
        filterLoan:  '',
        filterCity:  '',
        tab:         'all',
        sortCol:     'name',
        sortDir:     'asc',
        currentPage: 1,

        /* detail panel */
        selected:     null,
        recentSales:  [],
        detailLoading: false,

        /* edit modal */
        showEditModal:   false,
        editingCustomer: null,
        cf: {},
        formError: '',
        saving: false,

        /* payment modal */
        showPaymentModal: false,
        paymentTarget:    null,
        activeLoan:       null,
        loanPayments:     [],
        pf: { amount: 0, notes: '' },

        /* urls */
        urls: {
            list:     '{{ route("pos.customers.index") }}',
            store:    '{{ route("pos.customers.store") }}',
            payment:  '{{ route("pos.customers.payment") }}',
            detail:   '{{ url("pos/customers") }}',
            toggle:   '{{ url("pos/customers") }}',
            export:   '{{ route("pos.customers.export") }}',
            csrf:     document.querySelector('meta[name=csrf-token]').content,
        },

        /* ── init ── */
        async init() {
            await this.loadCustomers();
        },

        /* ── load list ── */
        async loadCustomers() {
            this.loading = true;
            try {
                const p = new URLSearchParams({
                    q:    this.search,
                    loan: this.filterLoan,
                    city: this.filterCity,
                    tab:  this.tab,
                    sort: this.sortCol,
                    dir:  this.sortDir,
                    page: this.currentPage,
                });
                const r = await fetch(this.urls.list + '?' + p, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                const d = await r.json();
                this.customers  = d.data;
                this.pagination = d.meta;
            } catch(e) { console.error(e); }
            finally { this.loading = false; }
        },

        sort(col) {
            if (this.sortCol === col) this.sortDir = this.sortDir === 'asc' ? 'desc' : 'asc';
            else { this.sortCol = col; this.sortDir = 'asc'; }
            this.loadCustomers();
        },

        goPage(p) {
            if (p < 1 || p > this.pagination.last_page) return;
            this.currentPage = p;
            this.loadCustomers();
        },

        /* ── detail panel ── */
        async openDetail(c) {
            this.selected = c;
            this.recentSales = [];
            this.detailLoading = true;
            try {
                const r = await fetch(`${this.urls.detail}/${c.id}/detail`, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                const d = await r.json();
                this.recentSales = d.recent_sales;
                // merge detail data into selected
                this.selected = { ...this.selected, ...d.customer };
            } catch(e) { console.error(e); }
            finally { this.detailLoading = false; }
        },

        /* ── edit ── */
        openEdit(c) {
            this.editingCustomer = c;
            this.cf = { ...c };
            this.formError = '';
            this.showEditModal = true;
        },

        resetCf() {
            this.cf = { name:'', phone:'', phone_secondary:'', address:'', city:'', notes:'', credit_limit:null, is_active:true };
            this.formError = '';
        },

        async saveCustomer() {
            if (!this.cf.name?.trim()) { this.formError = 'Name is required.'; return; }
            if (!this.cf.phone?.trim()) { this.formError = 'Phone is required.'; return; }
            this.saving = true; this.formError = '';
            try {
                const r = await fetch(this.urls.store, {
                    method:  'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.urls.csrf },
                    body:    JSON.stringify({ ...this.cf, customer_id: this.editingCustomer?.id })
                });
                const d = await r.json();
                if (d.success) {
                    this.showEditModal = false;
                    this.loadCustomers();
                    if (this.selected?.id === this.editingCustomer?.id) {
                        this.selected = { ...this.selected, ...d.customer };
                    }
                } else {
                    this.formError = d.message ?? 'Failed to save.';
                }
            } catch(e) { this.formError = 'Network error.'; }
            finally { this.saving = false; }
        },

        /* ── toggle active ── */
        async toggleActive(c) {
            if (!confirm(`${c.is_active ? 'Deactivate' : 'Activate'} ${c.name}?`)) return;
            await fetch(`${this.urls.toggle}/${c.id}/toggle`, {
                method:  'POST',
                headers: { 'X-CSRF-TOKEN': this.urls.csrf }
            });
            this.loadCustomers();
            if (this.selected?.id === c.id) this.selected = null;
        },

        /* ── payment ── */
        async openPayment(c) {
            this.paymentTarget = c;
            this.activeLoan    = null;
            this.loanPayments  = [];
            this.pf            = { amount: 0, notes: '' };
            this.formError     = '';
            this.showPaymentModal = true;

            try {
                const r = await fetch(`${this.urls.detail}/${c.id}/loan`, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                const d = await r.json();
                this.activeLoan   = d.loan;
                this.loanPayments = d.payments;
            } catch(e) { console.error(e); }
        },

        async savePayment() {
            if (!this.pf.amount || this.pf.amount <= 0) { this.formError = 'Enter a valid amount.'; return; }
            if (this.pf.amount > this.activeLoan?.remaining_balance) { this.formError = 'Amount exceeds remaining balance.'; return; }
            this.saving = true; this.formError = '';
            try {
                const r = await fetch(this.urls.payment, {
                    method:  'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.urls.csrf },
                    body:    JSON.stringify({ loan_id: this.activeLoan.id, ...this.pf })
                });
                const d = await r.json();
                if (d.success) {
                    this.showPaymentModal = false;
                    this.loadCustomers();
                    if (this.selected) await this.openDetail(this.selected);
                } else {
                    this.formError = d.message ?? 'Payment failed.';
                }
            } catch(e) { this.formError = 'Network error.'; }
            finally { this.saving = false; }
        },

        /* ── export ── */
        exportCsv() {
            window.location.href = this.urls.export + '?q=' + this.search + '&loan=' + this.filterLoan + '&tab=' + this.tab;
        },

        /* ── helpers ── */
        initials(name) {
            if (!name) return '?';
            return name.trim().split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2);
        },

        avatarColor(name) {
            const colors = ['#3563e9','#7c3aed','#0891b2','#16a34a','#d97706','#dc2626','#4f46e5','#0f766e'];
            if (!name) return colors[0];
            const i = name.charCodeAt(0) % colors.length;
            return colors[i];
        },

        relativeTime(dateStr) {
            const diff = Math.floor((Date.now() - new Date(dateStr)) / 1000);
            if (diff < 60)   return 'just now';
            if (diff < 3600) return Math.floor(diff/60) + 'm ago';
            if (diff < 86400) return Math.floor(diff/3600) + 'h ago';
            return Math.floor(diff/86400) + 'd ago';
        },

        fmt(n) {
            return Number(n || 0).toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
        }
    };
}
</script>
@endpush