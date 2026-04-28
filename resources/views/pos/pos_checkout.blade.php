@extends('layouts.app')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Crimson+Pro:ital,wght@0,400;0,600;1,400&family=Figtree:wght@300;400;500;600;700&family=Fira+Code:wght@400;500&display=swap" rel="stylesheet">
<style>
/* ══════════════════════════════════════════
   TOKENS
══════════════════════════════════════════ */
:root {
    --ink:        #1a1d23;
    --ink-2:      #4a5060;
    --ink-3:      #9aa0ad;
    --ink-4:      #c8cdd6;
    --bg:         #f2f4f7;
    --surface:    #ffffff;
    --surface-2:  #f7f8fa;
    --surface-3:  #eef0f4;
    --border:     #e2e5ec;
    --border-2:   #d0d4de;
    --blue:       #2463eb;
    --blue-2:     #1a4fd6;
    --blue-dim:   rgba(36,99,235,.08);
    --blue-mid:   rgba(36,99,235,.15);
    --teal:       #0891b2;
    --teal-dim:   rgba(8,145,178,.08);
    --green:      #16a34a;
    --green-dim:  rgba(22,163,74,.1);
    --red:        #dc2626;
    --red-dim:    rgba(220,38,38,.08);
    --amber:      #d97706;
    --amber-dim:  rgba(217,119,6,.1);
    --navy:       #1e3a5f;
    --mono:       'Fira Code', monospace;
    --body:       'Figtree', sans-serif;
    --display:    'Crimson Pro', serif;
    --r:          10px;
    --r-sm:       6px;
    --r-lg:       16px;
    --shadow-sm:  0 1px 3px rgba(0,0,0,.06), 0 1px 2px rgba(0,0,0,.04);
    --shadow:     0 4px 16px rgba(0,0,0,.07), 0 1px 4px rgba(0,0,0,.04);
    --shadow-lg:  0 12px 40px rgba(0,0,0,.1), 0 4px 12px rgba(0,0,0,.06);
}

/* ══════════════════════════════════════════
   RESET & BASE
══════════════════════════════════════════ */
.co * { box-sizing: border-box; }
.co {
    font-family: var(--body);
    background: var(--bg);
    min-height: 100vh;
    color: var(--ink);
    display: flex;
    flex-direction: column;
}

/* ══════════════════════════════════════════
   TOPBAR
══════════════════════════════════════════ */
.co-topbar {
    height: 52px;
    background: var(--surface);
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 1.5rem;
    box-shadow: var(--shadow-sm);
    position: sticky; top: 0; z-index: 100;
    flex-shrink: 0;
}
.co-topbar-left { display: flex; align-items: center; gap: 1rem; }
.co-back-btn {
    display: flex; align-items: center; gap: 6px;
    padding: 6px 12px;
    background: var(--surface-2);
    border: 1px solid var(--border);
    border-radius: var(--r-sm);
    font-family: var(--body);
    font-size: 12px; font-weight: 500;
    color: var(--ink-2);
    cursor: pointer;
    transition: all .15s;
    text-decoration: none;
}
.co-back-btn:hover { background: var(--surface-3); color: var(--ink); }
.co-title {
    font-family: var(--display);
    font-size: 18px;
    color: var(--ink);
    font-weight: 600;
}
.co-title span { color: var(--blue); font-style: italic; }
.co-topbar-right { display: flex; align-items: center; gap: 10px; }
.co-clock {
    font-family: var(--mono);
    font-size: 12px;
    color: var(--ink-3);
    background: var(--surface-2);
    border: 1px solid var(--border);
    padding: 5px 12px;
    border-radius: var(--r-sm);
}
.co-shift-pill {
    display: flex; align-items: center; gap: 5px;
    font-size: 11px; font-weight: 600;
    color: var(--green);
    background: var(--green-dim);
    border: 1px solid rgba(22,163,74,.2);
    padding: 5px 11px;
    border-radius: 99px;
}
.co-shift-pill::before {
    content: '';
    width: 5px; height: 5px;
    background: var(--green);
    border-radius: 50%;
    animation: blink 2s ease infinite;
}
@keyframes blink { 0%,100%{opacity:1} 50%{opacity:.3} }

/* ══════════════════════════════════════════
   SPLIT LAYOUT
══════════════════════════════════════════ */
.co-body {
    display: grid;
    grid-template-columns: 1fr 380px;
    gap: 0;
    flex: 1;
    min-height: 0;
    height: calc(100vh - 52px);
    overflow: hidden;
}

/* ══════════════════════════════════════════
   LEFT — CART PANEL
══════════════════════════════════════════ */
.cart-panel {
    display: flex;
    flex-direction: column;
    background: var(--bg);
    overflow: hidden;
    border-right: 1px solid var(--border);
}

/* Search bar */
.cart-search {
    padding: 1rem 1.25rem;
    background: var(--surface);
    border-bottom: 1px solid var(--border);
    display: flex;
    gap: 8px;
    flex-shrink: 0;
}
.search-field {
    flex: 1;
    position: relative;
}
.search-field i {
    position: absolute; left: 12px; top: 50%;
    transform: translateY(-50%);
    color: var(--ink-3); font-size: 13px;
    pointer-events: none;
}
.search-input {
    width: 100%;
    padding: 9px 14px 9px 36px;
    background: var(--surface-2);
    border: 1.5px solid var(--border);
    border-radius: var(--r-sm);
    font-family: var(--body);
    font-size: 13px;
    color: var(--ink);
    outline: none;
    transition: border .15s, box-shadow .15s;
}
.search-input:focus {
    border-color: var(--blue);
    background: #fff;
    box-shadow: 0 0 0 3px var(--blue-dim);
}
.search-input::placeholder { color: var(--ink-4); }
.btn-scan {
    display: flex; align-items: center; gap: 6px;
    padding: 9px 16px;
    background: var(--navy);
    color: #fff;
    border: none; cursor: pointer;
    border-radius: var(--r-sm);
    font-family: var(--body);
    font-size: 12px; font-weight: 600;
    transition: all .15s;
    white-space: nowrap;
}
.btn-scan:hover { background: #162d4a; }
.btn-icon {
    width: 36px; height: 36px;
    display: flex; align-items: center; justify-content: center;
    background: var(--surface-2);
    border: 1px solid var(--border);
    border-radius: var(--r-sm);
    cursor: pointer;
    color: var(--ink-2);
    font-size: 13px;
    transition: all .15s;
}
.btn-icon:hover { background: var(--surface-3); color: var(--ink); }

/* ── SEARCH RESULTS DROPDOWN ── */
.search-results-wrap {
    position: relative;
    padding: 0 1.25rem .75rem;
    background: var(--surface);
    border-bottom: 1px solid var(--border);
    flex-shrink: 0;
}
.search-results-box {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--r);
    box-shadow: var(--shadow-lg);
    overflow: hidden;
    max-height: 280px;
    overflow-y: auto;
}
.search-results-box::-webkit-scrollbar { width: 4px; }
.search-results-box::-webkit-scrollbar-thumb { background: var(--border-2); border-radius: 2px; }
.sr-item {
    display: flex; align-items: center; justify-content: space-between;
    padding: 10px 14px;
    border-bottom: 1px solid var(--border);
    cursor: pointer;
    transition: background .12s;
    gap: 12px;
}
.sr-item:last-child { border-bottom: none; }
.sr-item:hover { background: var(--blue-dim); }
.sr-name { font-size: 13px; font-weight: 500; color: var(--ink); }
.sr-sku  { font-family: var(--mono); font-size: 10px; color: var(--ink-3); margin-top: 2px; }
.sr-right { display: flex; align-items: center; gap: 10px; flex-shrink: 0; }
.sr-price { font-family: var(--mono); font-size: 13px; font-weight: 500; color: var(--blue); }
.sr-stock-ok   { font-size: 11px; color: var(--green); }
.sr-stock-low  { font-size: 11px; color: var(--amber); font-weight: 600; }
.sr-stock-none { font-size: 11px; color: var(--red); font-weight: 700; }
.btn-sr-add {
    padding: 4px 10px;
    background: var(--blue);
    color: #fff;
    border: none; cursor: pointer;
    border-radius: 5px;
    font-size: 11px; font-weight: 600;
    font-family: var(--body);
    transition: background .15s;
}
.btn-sr-add:hover { background: var(--blue-2); }
.btn-sr-add:disabled { background: var(--ink-4); cursor: not-allowed; }
.sr-empty {
    padding: 1.5rem;
    text-align: center;
    color: var(--ink-3);
    font-size: 13px;
}
.sr-spinner { padding: 1.25rem; text-align: center; color: var(--ink-3); font-size: 12px; }

/* ── CART ITEMS ── */
.cart-items-wrap {
    flex: 1;
    overflow-y: auto;
    padding: .75rem 1.25rem;
}
.cart-items-wrap::-webkit-scrollbar { width: 4px; }
.cart-items-wrap::-webkit-scrollbar-thumb { background: var(--border); border-radius: 2px; }

.cart-empty {
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    height: 100%;
    color: var(--ink-4);
    gap: 12px;
    text-align: center;
}
.cart-empty i { font-size: 40px; }
.cart-empty p { font-size: 13px; line-height: 1.6; }

/* Cart header row */
.cart-list-head {
    display: grid;
    grid-template-columns: 1fr 110px 90px 80px 28px;
    gap: 8px;
    padding: 6px 12px;
    font-size: 10px;
    font-weight: 700;
    color: var(--ink-3);
    text-transform: uppercase;
    letter-spacing: .07em;
    border-bottom: 1px solid var(--border);
    margin-bottom: 6px;
}

/* Cart row */
.cart-row {
    display: grid;
    grid-template-columns: 1fr 110px 90px 80px 28px;
    gap: 8px;
    align-items: center;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--r);
    padding: 10px 12px;
    margin-bottom: 6px;
    transition: all .15s;
    animation: rowIn .2s ease both;
}
@keyframes rowIn {
    from { opacity: 0; transform: translateY(-6px); }
    to   { opacity: 1; transform: none; }
}
.cart-row:hover { border-color: var(--blue); box-shadow: 0 2px 8px var(--blue-dim); }

.cr-name { font-size: 13px; font-weight: 500; color: var(--ink); min-width: 0; }
.cr-name-sub { font-size: 10px; color: var(--ink-3); font-family: var(--mono); margin-top: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

.qty-ctrl { display: flex; align-items: center; gap: 6px; }
.qty-btn {
    width: 24px; height: 24px;
    border-radius: 50%;
    border: 1px solid var(--border);
    background: var(--surface-2);
    color: var(--ink-2);
    font-size: 10px;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: all .12s;
    flex-shrink: 0;
}
.qty-btn:hover:not(:disabled) { background: var(--blue); border-color: var(--blue); color: #fff; }
.qty-btn:disabled { opacity: .3; cursor: not-allowed; }
.qty-num {
    font-family: var(--mono);
    font-size: 14px; font-weight: 500;
    min-width: 22px; text-align: center;
    color: var(--ink);
}
.cr-price {
    font-family: var(--mono);
    font-size: 12px;
    color: var(--ink-2);
    text-align: right;
}
.cr-total {
    font-family: var(--mono);
    font-size: 13px; font-weight: 500;
    color: var(--blue);
    text-align: right;
}
.cr-remove {
    background: none; border: none; cursor: pointer;
    color: var(--ink-4); font-size: 13px;
    transition: color .12s;
    padding: 2px;
}
.cr-remove:hover { color: var(--red); }

/* Discount badge on row */
.cr-disc-badge {
    display: inline-block;
    font-size: 9px; font-weight: 700;
    color: var(--amber);
    background: var(--amber-dim);
    border: 1px solid rgba(217,119,6,.2);
    padding: 1px 5px;
    border-radius: 4px;
    margin-left: 4px;
    vertical-align: middle;
}

/* Cart footer totals strip */
.cart-footer {
    background: var(--surface);
    border-top: 1px solid var(--border);
    padding: .75rem 1.25rem;
    flex-shrink: 0;
}
.totals-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 6px 16px;
    margin-bottom: 8px;
}
.tot-row { display: flex; justify-content: space-between; align-items: center; }
.tot-label { font-size: 12px; color: var(--ink-3); }
.tot-val   { font-family: var(--mono); font-size: 12px; color: var(--ink-2); }
.tot-val.red { color: var(--red); }
.tot-val.green { color: var(--green); }

.grand-total-row {
    display: flex; justify-content: space-between; align-items: center;
    padding: .75rem 1rem;
    background: var(--navy);
    border-radius: var(--r);
    margin-top: 8px;
}
.gt-label { font-size: 11px; font-weight: 700; color: rgba(255,255,255,.6); text-transform: uppercase; letter-spacing: .08em; }
.gt-val   { font-family: var(--mono); font-size: 22px; font-weight: 500; color: #fff; letter-spacing: -.5px; }
.gt-val span { font-size: 13px; color: rgba(255,255,255,.5); margin-right: 3px; }

/* ══════════════════════════════════════════
   RIGHT — PAYMENT PANEL
══════════════════════════════════════════ */
.pay-panel {
    display: flex;
    flex-direction: column;
    background: var(--surface);
    overflow-y: auto;
    overflow-x: hidden;
}
.pay-panel::-webkit-scrollbar { width: 4px; }
.pay-panel::-webkit-scrollbar-thumb { background: var(--border); border-radius: 2px; }

.pay-section {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid var(--border);
}
.pay-section-title {
    font-size: 10px; font-weight: 700;
    color: var(--ink-3);
    text-transform: uppercase;
    letter-spacing: .1em;
    margin-bottom: .75rem;
    display: flex; align-items: center; gap: 6px;
}
.pay-section-title i { color: var(--blue); }

/* Customer picker */
.customer-search-wrap { position: relative; }
.customer-input {
    width: 100%;
    padding: 9px 36px 9px 12px;
    background: var(--surface-2);
    border: 1.5px solid var(--border);
    border-radius: var(--r-sm);
    font-family: var(--body);
    font-size: 13px;
    color: var(--ink);
    outline: none;
    transition: border .15s, box-shadow .15s;
}
.customer-input:focus { border-color: var(--blue); box-shadow: 0 0 0 3px var(--blue-dim); background: #fff; }
.customer-input::placeholder { color: var(--ink-4); }
.customer-clear {
    position: absolute; right: 10px; top: 50%;
    transform: translateY(-50%);
    background: none; border: none; cursor: pointer;
    color: var(--ink-3); font-size: 13px;
}
.customer-selected {
    display: flex; align-items: center; gap: 10px;
    padding: 8px 10px;
    background: var(--blue-dim);
    border: 1px solid var(--blue-mid);
    border-radius: var(--r-sm);
}
.cust-avatar {
    width: 30px; height: 30px;
    background: var(--blue);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 11px; font-weight: 700; color: #fff;
    flex-shrink: 0;
}
.cust-name  { font-size: 13px; font-weight: 500; color: var(--ink); }
.cust-loan  { font-size: 11px; color: var(--red); margin-top: 1px; }
.cust-remove {
    margin-left: auto;
    background: none; border: none; cursor: pointer;
    color: var(--ink-3); font-size: 12px;
}

/* Payment method */
.pay-method-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
.pay-method-btn {
    padding: 12px 10px;
    border: 2px solid var(--border);
    border-radius: var(--r);
    background: var(--surface-2);
    cursor: pointer;
    transition: all .18s;
    text-align: center;
    font-family: var(--body);
}
.pay-method-btn:hover { border-color: var(--blue); background: var(--blue-dim); }
.pay-method-btn.active {
    border-color: var(--blue);
    background: var(--blue-dim);
    box-shadow: 0 0 0 3px var(--blue-dim);
}
.pay-method-btn.active-loan {
    border-color: var(--amber);
    background: var(--amber-dim);
    box-shadow: 0 0 0 3px var(--amber-dim);
}
.pmb-icon { font-size: 20px; margin-bottom: 5px; }
.pmb-label { font-size: 12px; font-weight: 600; color: var(--ink); }
.pmb-sub   { font-size: 10px; color: var(--ink-3); margin-top: 2px; }

/* Amount inputs */
.amount-input-wrap { position: relative; margin-bottom: 8px; }
.amount-prefix {
    position: absolute; left: 12px; top: 50%;
    transform: translateY(-50%);
    font-family: var(--mono);
    font-size: 13px; font-weight: 600;
    color: var(--ink-2);
}
.amount-input {
    width: 100%;
    padding: 11px 14px 11px 40px;
    background: var(--surface-2);
    border: 1.5px solid var(--border);
    border-radius: var(--r-sm);
    font-family: var(--mono);
    font-size: 16px; font-weight: 500;
    color: var(--ink);
    outline: none;
    transition: border .15s, box-shadow .15s;
}
.amount-input:focus { border-color: var(--blue); box-shadow: 0 0 0 3px var(--blue-dim); background: #fff; }

/* Quick amounts */
.quick-amounts { display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 8px; }
.qa-btn {
    padding: 5px 10px;
    background: var(--surface-2);
    border: 1px solid var(--border);
    border-radius: var(--r-sm);
    font-family: var(--mono);
    font-size: 11px; font-weight: 500;
    cursor: pointer;
    color: var(--ink-2);
    transition: all .12s;
}
.qa-btn:hover { background: var(--blue-dim); border-color: var(--blue); color: var(--blue); }

/* Change display */
.change-box {
    display: flex; align-items: center; justify-content: space-between;
    padding: 10px 12px;
    border-radius: var(--r-sm);
    margin-top: 6px;
    transition: background .2s;
}
.change-box.positive { background: var(--green-dim); border: 1px solid rgba(22,163,74,.2); }
.change-box.negative { background: var(--red-dim);   border: 1px solid rgba(220,38,38,.2); }
.change-box.zero     { background: var(--surface-2); border: 1px solid var(--border); }
.change-label { font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: .06em; }
.change-box.positive .change-label { color: var(--green); }
.change-box.negative .change-label { color: var(--red); }
.change-box.zero     .change-label { color: var(--ink-3); }
.change-val   { font-family: var(--mono); font-size: 18px; font-weight: 600; }
.change-box.positive .change-val { color: var(--green); }
.change-box.negative .change-val { color: var(--red); }
.change-box.zero     .change-val { color: var(--ink-3); }

/* Discount */
.disc-row { display: flex; gap: 6px; }
.disc-type-toggle {
    display: flex;
    background: var(--surface-2);
    border: 1px solid var(--border);
    border-radius: var(--r-sm);
    overflow: hidden;
    flex-shrink: 0;
}
.disc-toggle-btn {
    padding: 8px 12px;
    font-size: 12px; font-weight: 600;
    font-family: var(--body);
    border: none; cursor: pointer;
    background: transparent;
    color: var(--ink-3);
    transition: all .15s;
}
.disc-toggle-btn.active { background: var(--blue); color: #fff; }

/* Notes textarea */
.notes-input {
    width: 100%;
    padding: 9px 12px;
    background: var(--surface-2);
    border: 1.5px solid var(--border);
    border-radius: var(--r-sm);
    font-family: var(--body);
    font-size: 12px;
    color: var(--ink);
    resize: none;
    outline: none;
    transition: border .15s;
    min-height: 64px;
}
.notes-input:focus { border-color: var(--blue); background: #fff; }
.notes-input::placeholder { color: var(--ink-4); }

/* Options row */
.options-row {
    display: flex; flex-direction: column; gap: 8px;
}
.option-item {
    display: flex; align-items: center; justify-content: space-between;
    padding: 9px 10px;
    background: var(--surface-2);
    border: 1px solid var(--border);
    border-radius: var(--r-sm);
    cursor: pointer;
    transition: background .12s;
}
.option-item:hover { background: var(--surface-3); }
.option-label { font-size: 12px; font-weight: 500; color: var(--ink); display: flex; align-items: center; gap: 8px; }
.option-label i { color: var(--blue); width: 14px; text-align: center; }

/* Toggle switch */
.toggle {
    position: relative;
    width: 36px; height: 20px;
    flex-shrink: 0;
}
.toggle input { opacity: 0; width: 0; height: 0; }
.toggle-slider {
    position: absolute; inset: 0;
    background: var(--border-2);
    border-radius: 10px;
    cursor: pointer;
    transition: background .2s;
}
.toggle-slider::before {
    content: '';
    position: absolute;
    width: 14px; height: 14px;
    background: #fff;
    border-radius: 50%;
    top: 3px; left: 3px;
    transition: transform .2s;
    box-shadow: 0 1px 3px rgba(0,0,0,.2);
}
.toggle input:checked + .toggle-slider { background: var(--blue); }
.toggle input:checked + .toggle-slider::before { transform: translateX(16px); }

/* ── CHECKOUT BUTTON ── */
.pay-panel-footer {
    padding: 1rem 1.25rem;
    background: var(--surface);
    border-top: 2px solid var(--border);
    flex-shrink: 0;
    margin-top: auto;
}
.btn-checkout-main {
    width: 100%;
    padding: 14px;
    border: none; cursor: pointer;
    border-radius: var(--r);
    font-family: var(--body);
    font-size: 15px; font-weight: 700;
    letter-spacing: .02em;
    transition: all .2s;
    display: flex; align-items: center; justify-content: center; gap: 10px;
}
.btn-checkout-cash {
    background: linear-gradient(135deg, var(--blue) 0%, #1a4fd6 100%);
    color: #fff;
    box-shadow: 0 4px 18px rgba(36,99,235,.3);
}
.btn-checkout-cash:hover:not(:disabled) {
    transform: translateY(-1px);
    box-shadow: 0 8px 28px rgba(36,99,235,.4);
}
.btn-checkout-loan {
    background: linear-gradient(135deg, var(--amber) 0%, #b45309 100%);
    color: #fff;
    box-shadow: 0 4px 18px rgba(217,119,6,.3);
}
.btn-checkout-loan:hover:not(:disabled) {
    transform: translateY(-1px);
    box-shadow: 0 8px 28px rgba(217,119,6,.4);
}
.btn-checkout-main:disabled { opacity: .4; cursor: not-allowed; transform: none !important; }
.btn-checkout-main:active:not(:disabled) { transform: scale(.98) !important; }

.btn-hold {
    width: 100%;
    margin-top: 8px;
    padding: 9px;
    background: var(--surface-2);
    border: 1px solid var(--border);
    border-radius: var(--r-sm);
    font-family: var(--body);
    font-size: 12px; font-weight: 600;
    color: var(--ink-2);
    cursor: pointer;
    transition: all .15s;
    display: flex; align-items: center; justify-content: center; gap: 6px;
}
.btn-hold:hover { background: var(--amber-dim); border-color: var(--amber); color: var(--amber); }
.btn-hold:disabled { opacity: .4; cursor: not-allowed; }

/* Keyboard shortcut hints */
.shortcut-hint {
    font-size: 9px;
    color: var(--ink-4);
    background: var(--surface-3);
    border: 1px solid var(--border);
    border-radius: 3px;
    padding: 1px 5px;
    font-family: var(--mono);
    margin-left: 4px;
}

/* ══════════════════════════════════════════
   NUMPAD MODAL
══════════════════════════════════════════ */
.numpad-overlay {
    position: fixed; inset: 0;
    background: rgba(0,0,0,.4);
    backdrop-filter: blur(4px);
    z-index: 200;
    display: flex; align-items: center; justify-content: center;
    animation: fadeIn .15s ease;
}
@keyframes fadeIn { from { opacity:0 } to { opacity:1 } }
.numpad-card {
    background: var(--surface);
    border-radius: var(--r-lg);
    box-shadow: var(--shadow-lg);
    width: 280px;
    overflow: hidden;
    animation: scaleIn .15s cubic-bezier(.2,.8,.36,1);
}
@keyframes scaleIn { from { opacity:0;transform:scale(.92) } to { opacity:1;transform:none } }
.numpad-head {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid var(--border);
    display: flex; align-items: center; justify-content: space-between;
}
.numpad-title { font-size: 13px; font-weight: 600; color: var(--ink); }
.numpad-close {
    background: none; border: none; cursor: pointer;
    color: var(--ink-3); font-size: 16px;
}
.numpad-display {
    padding: .75rem 1.25rem;
    background: var(--surface-2);
    border-bottom: 1px solid var(--border);
    font-family: var(--mono);
    font-size: 26px;
    font-weight: 500;
    color: var(--ink);
    text-align: right;
    letter-spacing: -.5px;
    min-height: 60px;
    display: flex; align-items: center; justify-content: flex-end;
}
.numpad-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1px;
    background: var(--border);
}
.np-btn {
    padding: 16px;
    background: var(--surface);
    border: none; cursor: pointer;
    font-family: var(--body);
    font-size: 16px; font-weight: 500;
    color: var(--ink);
    transition: background .1s;
    text-align: center;
}
.np-btn:hover { background: var(--surface-2); }
.np-btn:active { background: var(--blue-dim); }
.np-btn.np-zero { grid-column: span 2; }
.np-btn.np-del  { color: var(--red); background: var(--red-dim); }
.np-btn.np-del:hover { background: var(--red); color: #fff; }
.np-btn.np-ok   { background: var(--blue); color: #fff; font-weight: 700; }
.np-btn.np-ok:hover { background: var(--blue-2); }

/* ══════════════════════════════════════════
   RECEIPT MODAL
══════════════════════════════════════════ */
.receipt-overlay {
    position: fixed; inset: 0;
    background: rgba(0,0,0,.5);
    backdrop-filter: blur(6px);
    z-index: 300;
    display: flex; align-items: center; justify-content: center;
    animation: fadeIn .2s ease;
}
.receipt-card {
    background: var(--surface);
    border-radius: var(--r-lg);
    box-shadow: var(--shadow-lg);
    width: 340px;
    max-height: 90vh;
    overflow-y: auto;
    animation: scaleIn .2s cubic-bezier(.2,.8,.36,1);
}
.receipt-card::-webkit-scrollbar { width: 4px; }
.receipt-card::-webkit-scrollbar-thumb { background: var(--border); }
.receipt-top {
    padding: 1.5rem;
    text-align: center;
    border-bottom: 1px dashed var(--border);
}
.receipt-logo {
    font-family: var(--display);
    font-size: 22px;
    font-weight: 600;
    color: var(--ink);
    margin-bottom: 4px;
}
.receipt-logo span { color: var(--blue); }
.receipt-sub  { font-size: 11px; color: var(--ink-3); }
.receipt-info { font-size: 11px; color: var(--ink-3); margin-top: 8px; }
.receipt-body { padding: 1rem 1.5rem; }
.receipt-items { margin-bottom: 1rem; }
.receipt-item {
    display: flex; justify-content: space-between;
    font-size: 12px;
    padding: 5px 0;
    border-bottom: 1px solid var(--border);
    color: var(--ink);
}
.receipt-item:last-child { border-bottom: none; }
.ri-name  { flex: 1; }
.ri-qty   { color: var(--ink-3); width: 30px; text-align: center; }
.ri-total { font-family: var(--mono); font-weight: 500; }
.receipt-totals { border-top: 1px dashed var(--border); padding-top: .75rem; }
.rt-row { display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 4px; color: var(--ink-2); }
.rt-row.grand { font-size: 15px; font-weight: 700; color: var(--ink); margin-top: 6px; }
.rt-row.grand span { font-family: var(--mono); }
.receipt-footer {
    padding: 1rem 1.5rem 1.5rem;
    text-align: center;
    border-top: 1px dashed var(--border);
    font-size: 11px;
    color: var(--ink-3);
}
.receipt-actions {
    display: flex; gap: 8px;
    padding: 0 1.5rem 1.5rem;
}
.btn-receipt {
    flex: 1; padding: 10px;
    border-radius: var(--r-sm);
    font-family: var(--body);
    font-size: 12px; font-weight: 600;
    cursor: pointer; border: none;
    transition: all .15s;
    display: flex; align-items: center; justify-content: center; gap: 6px;
}
.btn-receipt-print { background: var(--navy); color: #fff; }
.btn-receipt-print:hover { background: #162d4a; }
.btn-receipt-done  { background: var(--green-dim); border: 1px solid rgba(22,163,74,.2); color: var(--green); }
.btn-receipt-done:hover { background: var(--green); color: #fff; }

/* ══════════════════════════════════════════
   MISC UTILS
══════════════════════════════════════════ */
[x-cloak] { display: none !important; }
.text-right { text-align: right; }
.text-center { text-align: center; }
</style>
@endpush

@section('content')
<div class="co" x-data="posCheckout()" x-init="init()" @keydown.f2.window="focusSearch()" @keydown.f4.window="openNumpad()" @keydown.f9.window="holdSale()" @keydown.f12.window="processCheckout()">

    {{-- ════════ TOPBAR ════════ --}}
    <div class="co-topbar">
        <div class="co-topbar-left">
            <a href="{{ route('pos.dashboard') }}" class="co-back-btn">
                <i class="fas fa-arrow-left"></i> Dashboard
            </a>
            <div class="co-title">Afghan <span>POS</span> — Checkout</div>
        </div>
        <div class="co-topbar-right">
            <div class="co-clock" id="coClock">--:--:--</div>
            <div class="co-shift-pill">Shift Active</div>
        </div>
    </div>

    {{-- ════════ SPLIT BODY ════════ --}}
    <div class="co-body">

        {{-- ══ LEFT: CART PANEL ══ --}}
        <div class="cart-panel">

            {{-- Search / Scan bar --}}
            <div class="cart-search">
                <div class="search-field">
                    <i class="fas fa-barcode"></i>
                    <input class="search-input"
                           id="searchInput"
                           type="text"
                           x-model="query"
                           @input.debounce.350ms="searchProducts()"
                           @keydown.enter="searchProducts()"
                           @keydown.escape="clearSearch()"
                           placeholder="Barcode scan or type product name / SKU… (F2)">
                </div>
                <button type="button" class="btn-scan" @click="searchProducts()">
                    <i class="fas fa-search"></i> Search
                </button>
                <button type="button" class="btn-icon" title="Clear (Esc)" @click="clearSearch()" x-show="query" x-cloak>
                    <i class="fas fa-times"></i>
                </button>
            </div>

            {{-- Search results --}}
            <div class="search-results-wrap" x-show="query" x-cloak>
                <div class="sr-spinner" x-show="searching">
                    <i class="fas fa-spinner fa-spin"></i> Searching...
                </div>
                <div class="search-results-box" x-show="!searching">
                    <div class="sr-empty" x-show="searchResults.length === 0">
                        <i class="fas fa-magnifying-glass" style="margin-right:6px"></i>
                        No results for "<span x-text="query"></span>"
                    </div>
                    <template x-for="p in searchResults" :key="p.variant_id">
                        <div class="sr-item" @click="addToCart(p)" :class="p.stock_quantity === 0 ? 'opacity-50' : ''">
                            <div>
                                <div class="sr-name" x-text="p.name"></div>
                                <div class="sr-sku" x-text="p.sku + ' · ' + p.barcode"></div>
                            </div>
                            <div class="sr-right">
                                <span :class="p.stock_quantity === 0 ? 'sr-stock-none' : p.stock_quantity < 5 ? 'sr-stock-low' : 'sr-stock-ok'"
                                      x-text="p.stock_quantity + ' in stock'"></span>
                                <span class="sr-price">Af <span x-text="fmt(p.price)"></span></span>
                                <button type="button" class="btn-sr-add"
                                        @click.stop="addToCart(p)"
                                        :disabled="p.stock_quantity === 0">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Cart items --}}
            <div class="cart-items-wrap">
                {{-- Empty state --}}
                <div class="cart-empty" x-show="cart.length === 0 && !query">
                    <i class="fas fa-cart-shopping" style="color:var(--ink-4)"></i>
                    <p>Cart is empty.<br>Scan a barcode or search<br>to add products.</p>
                    <span style="font-size:11px;color:var(--ink-4)">Press <span class="shortcut-hint">F2</span> to focus search</span>
                </div>

                {{-- Cart header --}}
                <div class="cart-list-head" x-show="cart.length > 0" x-cloak>
                    <span>Product</span>
                    <span class="text-center">Qty</span>
                    <span class="text-right">Unit Price</span>
                    <span class="text-right">Total</span>
                    <span></span>
                </div>

                {{-- Cart rows --}}
                <template x-for="(item, idx) in cart" :key="item.variant_id">
                    <div class="cart-row">
                        {{-- Name --}}
                        <div>
                            <div class="cr-name" x-text="item.name">
                                <span class="cr-disc-badge" x-show="item.row_discount > 0" x-text="'-' + item.row_discount + '%'"></span>
                            </div>
                            <div class="cr-name-sub" x-text="item.sku + ' · Af ' + fmt(item.price)"></div>
                        </div>
                        {{-- Qty --}}
                        <div class="qty-ctrl" style="justify-content:center">
                            <button type="button" class="qty-btn" @click="decQty(idx)">
                                <i class="fas fa-minus"></i>
                            </button>
                            <span class="qty-num" x-text="item.qty"></span>
                            <button type="button" class="qty-btn"
                                    @click="incQty(idx)"
                                    :disabled="item.qty >= item.stock_quantity">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        {{-- Unit --}}
                        <div class="cr-price">Af <span x-text="fmt(item.price)"></span></div>
                        {{-- Total --}}
                        <div class="cr-total">Af <span x-text="fmt(item.lineTotal)"></span></div>
                        {{-- Remove --}}
                        <button type="button" class="cr-remove" @click="removeItem(idx)">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </template>
            </div>

            {{-- Cart footer --}}
            <div class="cart-footer" x-show="cart.length > 0" x-cloak>
                <div class="totals-grid">
                    <div class="tot-row">
                        <span class="tot-label">Subtotal</span>
                        <span class="tot-val">Af <span x-text="fmt(subtotal)"></span></span>
                    </div>
                    <div class="tot-row">
                        <span class="tot-label">Items</span>
                        <span class="tot-val" x-text="totalItems + ' pcs'"></span>
                    </div>
                    <div class="tot-row">
                        <span class="tot-label">Discount</span>
                        <span class="tot-val red">- Af <span x-text="fmt(discountAmount)"></span></span>
                    </div>
                    <div class="tot-row">
                        <span class="tot-label">Tax</span>
                        <span class="tot-val">Af <span x-text="fmt(taxAmount)"></span></span>
                    </div>
                </div>
                <div class="grand-total-row">
                    <div>
                        <div class="gt-label">Grand Total</div>
                    </div>
                    <div class="gt-val"><span>Af</span><span x-text="fmt(grandTotal)"></span></div>
                </div>
            </div>

        </div>{{-- /cart-panel --}}

        {{-- ══ RIGHT: PAYMENT PANEL ══ --}}
        <div class="pay-panel">

            {{-- Customer --}}
            <div class="pay-section">
                <div class="pay-section-title">
                    <i class="fas fa-user"></i> Customer
                    <span style="margin-left:auto;font-weight:400;font-size:9px;color:var(--ink-4)">optional for cash</span>
                </div>
                <div x-show="!selectedCustomer">
                    <div class="customer-search-wrap">
                        <input class="customer-input"
                               type="text"
                               x-model="customerQuery"
                               @input.debounce.300ms="searchCustomers()"
                               placeholder="Search customer name or phone...">
                        <button type="button" class="customer-clear" x-show="customerQuery" @click="customerQuery=''">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div x-show="customerResults.length > 0" x-cloak
                         style="border:1px solid var(--border);border-radius:var(--r-sm);margin-top:6px;overflow:hidden;max-height:140px;overflow-y:auto">
                        <template x-for="c in customerResults" :key="c.id">
                            <div class="sr-item" @click="selectCustomer(c)">
                                <div>
                                    <div class="sr-name" x-text="c.name"></div>
                                    <div class="sr-sku" x-text="c.phone"></div>
                                </div>
                                <span class="sr-stock-low" x-show="c.loan_balance > 0"
                                      x-text="'Loan: Af ' + fmt(c.loan_balance)"></span>
                            </div>
                        </template>
                    </div>
                </div>
                <div class="customer-selected" x-show="selectedCustomer" x-cloak>
                    <div class="cust-avatar" x-text="custInitials"></div>
                    <div>
                        <div class="cust-name" x-text="selectedCustomer?.name"></div>
                        <div class="cust-loan" x-show="selectedCustomer?.loan_balance > 0"
                             x-text="'Outstanding loan: Af ' + fmt(selectedCustomer?.loan_balance)"></div>
                    </div>
                    <button type="button" class="cust-remove" @click="selectedCustomer = null; customerQuery = ''">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            {{-- Payment Method --}}
            <div class="pay-section">
                <div class="pay-section-title"><i class="fas fa-wallet"></i> Payment Method</div>
                <div class="pay-method-grid">
                    <button type="button" class="pay-method-btn"
                            :class="payMethod === 'cash' ? 'active' : ''"
                            @click="payMethod = 'cash'">
                        <div class="pmb-icon">💵</div>
                        <div class="pmb-label">Cash</div>
                        <div class="pmb-sub">Immediate payment</div>
                    </button>
                    <button type="button" class="pay-method-btn"
                            :class="payMethod === 'loan' ? 'active-loan' : ''"
                            @click="setLoanMethod()"
                            :disabled="!selectedCustomer">
                        <div class="pmb-icon">📋</div>
                        <div class="pmb-label">Loan / Credit</div>
                        <div class="pmb-sub" x-text="selectedCustomer ? 'Assign to customer' : 'Select customer first'"></div>
                    </button>
                </div>
            </div>

            {{-- Cash fields --}}
            <div class="pay-section" x-show="payMethod === 'cash'">
                <div class="pay-section-title"><i class="fas fa-coins"></i> Cash Received <span class="shortcut-hint">F4</span></div>

                <div class="amount-input-wrap">
                    <span class="amount-prefix">Af</span>
                    <input class="amount-input"
                           id="cashInput"
                           type="number"
                           x-model.number="cashReceived"
                           @focus="$event.target.select()"
                           placeholder="0"
                           min="0">
                </div>

                <div class="quick-amounts">
                    <button type="button" class="qa-btn" @click="cashReceived = grandTotal">Exact</button>
                    <template x-for="amt in quickAmounts" :key="amt">
                        <button type="button" class="qa-btn" @click="cashReceived = amt"
                                x-text="'Af ' + fmt(amt)"></button>
                    </template>
                </div>

                <div class="change-box" :class="changeAmount >= 0 ? (changeAmount > 0 ? 'positive' : 'zero') : 'negative'">
                    <span class="change-label" x-text="changeAmount >= 0 ? 'Change' : 'Still Owed'"></span>
                    <span class="change-val">Af <span x-text="fmt(Math.abs(changeAmount))"></span></span>
                </div>
            </div>

            {{-- Loan fields --}}
            <div class="pay-section" x-show="payMethod === 'loan'" x-cloak>
                <div class="pay-section-title"><i class="fas fa-file-invoice-dollar"></i> Loan Details</div>
                <div class="amount-input-wrap" style="margin-bottom:8px">
                    <span class="amount-prefix">Af</span>
                    <input class="amount-input"
                           type="number"
                           x-model.number="loanDeposit"
                           placeholder="0 — initial deposit (optional)"
                           min="0">
                </div>
                <div class="tot-row" style="padding:6px 10px;background:var(--amber-dim);border:1px solid rgba(217,119,6,.2);border-radius:var(--r-sm)">
                    <span class="tot-label" style="color:var(--amber)">Balance on loan</span>
                    <span class="tot-val" style="color:var(--amber)">Af <span x-text="fmt(grandTotal - loanDeposit)"></span></span>
                </div>
            </div>

            {{-- Discount --}}
            <div class="pay-section">
                <div class="pay-section-title"><i class="fas fa-tag"></i> Discount</div>
                <div class="disc-row">
                    <div class="disc-type-toggle">
                        <button type="button" class="disc-toggle-btn" :class="discType==='pct'?'active':''" @click="discType='pct'">%</button>
                        <button type="button" class="disc-toggle-btn" :class="discType==='flat'?'active':''" @click="discType='flat'">Af</button>
                    </div>
                    <div class="amount-input-wrap" style="flex:1">
                        <span class="amount-prefix" x-text="discType==='pct' ? '%' : 'Af'"></span>
                        <input class="amount-input" style="font-size:14px;padding-top:9px;padding-bottom:9px"
                               type="number"
                               x-model.number="discountInput"
                               placeholder="0"
                               min="0">
                    </div>
                </div>
                <div x-show="discountAmount > 0" x-cloak
                     style="margin-top:6px;font-size:11px;color:var(--green);text-align:right">
                    Saving Af <span x-text="fmt(discountAmount)"></span>
                </div>
            </div>

            {{-- Options --}}
            <div class="pay-section">
                <div class="pay-section-title"><i class="fas fa-sliders"></i> Options</div>
                <div class="options-row">
                    <div class="option-item">
                        <span class="option-label"><i class="fas fa-print"></i> Print Receipt</span>
                        <label class="toggle">
                            <input type="checkbox" x-model="shouldPrintReceipt">
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                    <div class="option-item">
                        <span class="option-label"><i class="fas fa-cash-register"></i> Open Cash Drawer</span>
                        <label class="toggle">
                            <input type="checkbox" x-model="openDrawer">
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                    <div class="option-item">
                        <span class="option-label"><i class="fas fa-rotate-left"></i> This is a Return</span>
                        <label class="toggle">
                            <input type="checkbox" x-model="isReturn">
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Notes --}}
            <div class="pay-section">
                <div class="pay-section-title"><i class="fas fa-pen-to-square"></i> Notes</div>
                <textarea class="notes-input"
                          x-model="saleNotes"
                          placeholder="Optional note for this sale…"
                          rows="2"></textarea>
            </div>

            {{-- FOOTER BUTTONS --}}
            <div class="pay-panel-footer">
                <button type="button"
                        class="btn-checkout-main"
                        :class="payMethod === 'loan' ? 'btn-checkout-loan' : 'btn-checkout-cash'"
                        @click="processCheckout()"
                        :disabled="cart.length === 0 || processing || (payMethod === 'cash' && cashReceived < grandTotal)">
                    <template x-if="!processing">
                        <span style="display:flex;align-items:center;gap:8px">
                            <i :class="payMethod === 'loan' ? 'fas fa-file-invoice' : 'fas fa-bolt'"></i>
                            <span x-text="payMethod === 'loan' ? 'Confirm Loan Sale' : 'Complete Sale'"></span>
                            <span class="shortcut-hint" style="background:rgba(255,255,255,.15);border-color:rgba(255,255,255,.2);color:rgba(255,255,255,.7)">F12</span>
                        </span>
                    </template>
                    <template x-if="processing">
                        <span><i class="fas fa-spinner fa-spin"></i> Processing...</span>
                    </template>
                </button>
                <button type="button" class="btn-hold"
                        @click="holdSale()"
                        :disabled="cart.length === 0">
                    <i class="fas fa-pause"></i> Hold Sale
                    <span class="shortcut-hint">F9</span>
                </button>
                <div x-show="payMethod === 'cash' && cart.length > 0 && cashReceived < grandTotal" x-cloak
                     style="margin-top:8px;text-align:center;font-size:11px;color:var(--red)">
                    <i class="fas fa-circle-exclamation"></i>
                    Cash received is less than total by Af <span x-text="fmt(grandTotal - cashReceived)"></span>
                </div>
            </div>

        </div>{{-- /pay-panel --}}

    </div>{{-- /co-body --}}

    {{-- ════════ NUMPAD MODAL ════════ --}}
    <div class="numpad-overlay" x-show="showNumpad" x-cloak @click.self="showNumpad=false">
        <div class="numpad-card">
            <div class="numpad-head">
                <span class="numpad-title">Enter Cash Amount</span>
                <button type="button" class="numpad-close" @click="showNumpad=false"><i class="fas fa-times"></i></button>
            </div>
            <div class="numpad-display">
                Af <span x-text="numpadValue || '0'"></span>
            </div>
            <div class="numpad-grid">
                <template x-for="k in ['7','8','9','4','5','6','1','2','3']" :key="k">
                    <button type="button" class="np-btn" @click="numpadPress(k)" x-text="k"></button>
                </template>
                <button type="button" class="np-btn np-zero" @click="numpadPress('0')">0</button>
                <button type="button" class="np-btn np-del" @click="numpadDel()"><i class="fas fa-delete-left"></i></button>
                <button type="button" class="np-btn np-ok" style="grid-column:span 3" @click="numpadConfirm()">
                    <i class="fas fa-check" style="margin-right:6px"></i> Confirm
                </button>
            </div>
        </div>
    </div>

    {{-- ════════ RECEIPT MODAL ════════ --}}
    <div class="receipt-overlay" x-show="showReceipt" x-cloak>
        <div class="receipt-card">
            <div class="receipt-top">
                <div class="receipt-logo">Afghan <span>POS</span></div>
                <div class="receipt-sub">Retail Management System</div>
                <div class="receipt-info" x-text="receiptData.datetime"></div>
                <div class="receipt-info" x-text="'Cashier: ' + receiptData.cashier"></div>
                <div class="receipt-info" x-text="'Sale #: ' + receiptData.sale_id"></div>
            </div>
            <div class="receipt-body">
                <div class="receipt-items">
                    <template x-for="item in receiptData.items || []" :key="item.variant_id">
                        <div class="receipt-item">
                            <span class="ri-name" x-text="item.name"></span>
                            <span class="ri-qty" x-text="'×' + item.qty"></span>
                            <span class="ri-total" x-text="'Af ' + fmt(item.lineTotal)"></span>
                        </div>
                    </template>
                </div>
                <div class="receipt-totals">
                    <div class="rt-row"><span>Subtotal</span><span x-text="'Af ' + fmt(receiptData.subtotal)"></span></div>
                    <div class="rt-row" x-show="receiptData.discount > 0"><span>Discount</span><span x-text="'- Af ' + fmt(receiptData.discount)"></span></div>
                    <div class="rt-row grand"><span>TOTAL</span><span x-text="'Af ' + fmt(receiptData.total)"></span></div>
                    <div class="rt-row" x-show="receiptData.method === 'cash'">
                        <span>Cash Received</span><span x-text="'Af ' + fmt(receiptData.cash_received)"></span>
                    </div>
                    <div class="rt-row" x-show="receiptData.method === 'cash'">
                        <span>Change</span><span x-text="'Af ' + fmt(receiptData.change)"></span>
                    </div>
                    <div class="rt-row" x-show="receiptData.method === 'loan'" style="color:var(--amber)">
                        <span>Payment Method</span><span>Loan / Credit</span>
                    </div>
                </div>
            </div>
            <div class="receipt-footer">
                شکریه — Thank you for shopping with us<br>
                تشکر از خریداری شما
            </div>
            <div class="receipt-actions">
                <button type="button" class="btn-receipt btn-receipt-print" @click="openPrintPreview()">
                    <i class="fas fa-print"></i> Print
                </button>
                <button type="button" class="btn-receipt btn-receipt-done" @click="newSale()">
                    <i class="fas fa-check"></i> Done & New Sale
                </button>
            </div>
        </div>
    </div>

</div>{{-- /co --}}
@endsection

@push('scripts')
<script>
/* ── Live clock ── */
(function tick() {
    const el = document.getElementById('coClock');
    if (el) el.textContent = new Date().toLocaleTimeString('en-GB');
    setTimeout(tick, 1000);
})();

/* ══════════════════════════════════════════
   POS CHECKOUT ALPINE COMPONENT
══════════════════════════════════════════ */
function posCheckout() {
    return {
        /* URLs */
        urls: {
            search:    '{{ route("pos.products.search") }}',
            customers: '{{ route("pos.customers.search") }}',
            store:     '{{ route("pos.checkout.store") }}',
            hold:      '{{ route("pos.checkout.hold") }}',
            csrf:      '{{ csrf_token() }}'
        },

        /* Search */
        query:         '',
        searchResults: [],
        searching:     false,

        /* Customer */
        customerQuery:    '',
        customerResults:  [],
        selectedCustomer: null,

        /* Cart */
        cart: @json($cartItems ?? []),   /* pre-fill if coming from dashboard */

        /* Payment */
        payMethod:    'cash',
        cashReceived: 0,
        loanDeposit:  0,
        discountInput: 0,
        discType:     'pct',   /* 'pct' | 'flat' */
        taxRate:      0,       /* set to e.g. 0.05 for 5% if needed */
        saleNotes:    '',

        /* Options */
        shouldPrintReceipt: true,
        openDrawer:   true,
        isReturn:     false,

        /* UI state */
        processing:   false,
        showNumpad:   false,
        numpadValue:  '',
        showReceipt:  false,
        receiptData:  {},

        /* ── Computed ── */
        get subtotal() {
            return this.cart.reduce((s, i) => s + i.price * i.qty, 0);
        },
        get totalItems() {
            return this.cart.reduce((s, i) => s + i.qty, 0);
        },
        get discountAmount() {
            if (!this.discountInput) return 0;
            if (this.discType === 'pct') return this.subtotal * (this.discountInput / 100);
            return Math.min(this.discountInput, this.subtotal);
        },
        get taxAmount() {
            return (this.subtotal - this.discountAmount) * this.taxRate;
        },
        get grandTotal() {
            return Math.max(0, this.subtotal - this.discountAmount + this.taxAmount);
        },
        get changeAmount() {
            return this.cashReceived - this.grandTotal;
        },
        get quickAmounts() {
            const g = this.grandTotal;
            const round = (n, to) => Math.ceil(n / to) * to;
            return [round(g, 50), round(g, 100), round(g, 500), 1000].filter((v, i, a) => a.indexOf(v) === i && v >= g).slice(0, 4);
        },
        get custInitials() {
            if (!this.selectedCustomer) return '';
            return this.selectedCustomer.name.trim().split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2);
        },

        /* ── Init ── */
        init() {
            this.$nextTick(() => this.focusSearch());
        },

        focusSearch() {
            document.getElementById('searchInput')?.focus();
        },

        /* ── Product search ── */
        async searchProducts() {
            const q = this.query.trim();
            if (!q) { this.searchResults = []; return; }
            this.searching = true;
            try {
                const r = await fetch(this.urls.search + '?q=' + encodeURIComponent(q), {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await r.json();
                this.searchResults = data;
            } catch(e) { console.error(e); }
            finally { this.searching = false; }
        },

        clearSearch() {
            this.query = '';
            this.searchResults = [];
            this.searching = false;
        },

        /* ── Customer search ── */
        async searchCustomers() {
            const q = this.customerQuery.trim();
            if (!q) { this.customerResults = []; return; }
            try {
                const r = await fetch(this.urls.customers + '?q=' + encodeURIComponent(q), {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                this.customerResults = await r.json();
            } catch(e) { console.error(e); }
        },

        selectCustomer(c) {
            this.selectedCustomer = c;
            this.customerQuery    = '';
            this.customerResults  = [];
        },

        setLoanMethod() {
            if (!this.selectedCustomer) return;
            this.payMethod = 'loan';
        },

        /* ── Cart operations ── */
        addToCart(p) {
            if (p.stock_quantity === 0) return;
            const ex = this.cart.find(i => i.variant_id === p.variant_id);
            if (ex) {
                if (ex.qty < ex.stock_quantity) ex.qty++;
                ex.lineTotal = ex.price * ex.qty;
            } else {
                this.cart.push({ ...p, qty: 1, lineTotal: p.price, row_discount: 0 });
            }
            this.clearSearch();
            this.cashReceived = 0;
        },

        incQty(idx) {
            const i = this.cart[idx];
            if (i.qty < i.stock_quantity) { i.qty++; i.lineTotal = i.price * i.qty; }
        },

        decQty(idx) {
            const i = this.cart[idx];
            if (i.qty > 1) { i.qty--; i.lineTotal = i.price * i.qty; }
            else this.removeItem(idx);
        },

        removeItem(idx) {
            this.cart.splice(idx, 1);
            this.cashReceived = 0;
        },

        /* ── Numpad ── */
        openNumpad() {
            this.numpadValue = this.cashReceived ? String(this.cashReceived) : '';
            this.showNumpad  = true;
        },
        numpadPress(k) { this.numpadValue += k; },
        numpadDel()    { this.numpadValue = this.numpadValue.slice(0, -1); },
        numpadConfirm() {
            this.cashReceived = parseFloat(this.numpadValue) || 0;
            this.showNumpad   = false;
        },

        /* ── Hold sale ── */
        holdSale() {
            if (!this.cart.length) return;
            if (!confirm('Put this sale on hold?')) return;
            fetch(this.urls.hold, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.urls.csrf },
                body: JSON.stringify({ cart: this.cart, notes: this.saleNotes })
            }).then(() => {
                this.cart = [];
                this.cashReceived = 0;
                alert('Sale held successfully.');
            }).catch(e => console.error(e));
        },

        /* ── Process checkout ── */
        async processCheckout() {
            if (!this.cart.length) return;
            if (this.payMethod === 'cash' && this.cashReceived < this.grandTotal) return;
            this.processing = true;
            try {
                const payload = {
                    cart:          this.cart,
                    payment_method: this.payMethod,
                    cash_received: this.cashReceived,
                    loan_deposit:  this.loanDeposit,
                    discount_type: this.discType,
                    discount:      this.discountInput,
                    tax_rate:      this.taxRate,
                    customer_id:   this.selectedCustomer?.id ?? null,
                    notes:         this.saleNotes,
                    print_receipt: this.shouldPrintReceipt,
                    open_drawer:   this.openDrawer,
                    is_return:     this.isReturn,
                };

                const r = await fetch(this.urls.store, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.urls.csrf },
                    body: JSON.stringify(payload)
                });

                const data = await r.json();

                if (data.success) {
                    this.receiptData = {
                        sale_id:      data.sale_id,
                        datetime:     new Date().toLocaleString('en-GB'),
                        cashier:      data.cashier ?? 'Cashier',
                        items:        this.cart,
                        subtotal:     this.subtotal,
                        discount:     this.discountAmount,
                        total:        this.grandTotal,
                        cash_received: this.cashReceived,
                        change:       this.changeAmount,
                        method:       this.payMethod,
                    };
                    this.showReceipt = true;
                } else {
                    alert(data.message ?? 'Checkout failed. Please try again.');
                }
            } catch(e) {
                console.error(e);
                alert('Network error. Please check connection.');
            } finally {
                this.processing = false;
            }
        },

        openPrintPreview() {
            if (!this.showReceipt) return;
            window.print();
        },

        newSale() {
            this.cart             = [];
            this.cashReceived     = 0;
            this.loanDeposit      = 0;
            this.discountInput    = 0;
            this.selectedCustomer = null;
            this.customerQuery    = '';
            this.saleNotes        = '';
            this.payMethod        = 'cash';
            this.isReturn         = false;
            this.showReceipt      = false;
            this.$nextTick(() => this.focusSearch());
        },

        /* ── Utility ── */
        fmt(n) {
            return Number(n || 0).toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
        }
    };
}
</script>
@endpush
