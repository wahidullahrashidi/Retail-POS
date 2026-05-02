@extends('layouts.app')

@push('styles')
    @vite(['resources/css/pages/pos-checkout.css'])
@endpush

@section('content')
    <div class="co" x-data="posCheckout()" x-init="init()" @keydown.f2.window="focusSearch()"
        @keydown.f4.window="openNumpad()" @keydown.f9.window="holdSale()" @keydown.f12.window="processCheckout()">

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
                        <input class="search-input" id="searchInput" type="text" x-model="query"
                            @input.debounce.350ms="searchProducts()" @keydown.enter="searchProducts()"
                            @keydown.escape="clearSearch()" placeholder="Barcode scan or type product name / SKU… (F2)">
                    </div>
                    <button type="button" class="btn-scan" @click="searchProducts()">
                        <i class="fas fa-search"></i> Search
                    </button>
                    <button type="button" class="btn-icon" title="Clear (Esc)" @click="clearSearch()" x-show="query"
                        x-cloak>
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
                                    <span
                                        :class="p.stock_quantity === 0 ? 'sr-stock-none' : p.stock_quantity < 5 ?
                                            'sr-stock-low' : 'sr-stock-ok'"
                                        x-text="p.stock_quantity + ' in stock'"></span>
                                    <span class="sr-price">Af <span x-text="fmt(p.price)"></span></span>
                                    <button type="button" class="btn-sr-add" @click.stop="addToCart(p)"
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
                        <span style="font-size:11px;color:var(--ink-4)">Press <span class="shortcut-hint">F2</span> to focus
                            search</span>
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
                                    <span class="cr-disc-badge" x-show="item.row_discount > 0"
                                        x-text="'-' + item.row_discount + '%'"></span>
                                </div>
                                <div class="cr-name-sub" x-text="item.sku + ' · Af ' + fmt(item.price)"></div>
                            </div>
                            {{-- Qty --}}
                            <div class="qty-ctrl" style="justify-content:center">
                                <button type="button" class="qty-btn" @click="decQty(idx)">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <span class="qty-num" x-text="item.qty"></span>
                                <button type="button" class="qty-btn" @click="incQty(idx)"
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
                        <span style="margin-left:auto;font-weight:400;font-size:9px;color:var(--ink-4)">optional for
                            cash</span>
                    </div>

                    {{-- Add this button below the customer search input --}}
                    <button type="button" @click="showRegisterModal=true; registerError=''"
                        style="margin-top:8px;width:100%;padding:8px;background:var(--surface-2);border:1px dashed var(--border-2);border-radius:var(--r-sm);font-family:var(--body);font-size:12px;font-weight:500;color:var(--ink-2);cursor:pointer;transition:all .15s"
                        @mouseenter="$event.target.style.borderColor='var(--blue)';$event.target.style.color='var(--blue)'"
                        @mouseleave="$event.target.style.borderColor='var(--border-2)';$event.target.style.color='var(--ink-2)'">
                        <i class="fas fa-user-plus" style="margin-right:6px"></i> New Customer
                    </button>
                    <div x-show="!selectedCustomer">
                        <div class="customer-search-wrap">
                            <input class="customer-input" type="text" x-model="customerQuery"
                                @input.debounce.300ms="searchCustomers()" placeholder="Search customer name or phone...">
                            <button type="button" class="customer-clear" x-show="customerQuery"
                                @click="customerQuery=''">
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
                        <button type="button" class="pay-method-btn" :class="payMethod === 'cash' ? 'active' : ''"
                            @click="payMethod = 'cash'">
                            <div class="pmb-icon">💵</div>
                            <div class="pmb-label">Cash</div>
                            <div class="pmb-sub">Immediate payment</div>
                        </button>
                        <button type="button" class="pay-method-btn"
                            :class="payMethod === 'loan' ? 'active-loan' : ''" @click="setLoanMethod()"
                            :disabled="!selectedCustomer">
                            <div class="pmb-icon">📋</div>
                            <div class="pmb-label">Loan / Credit</div>
                            <div class="pmb-sub"
                                x-text="selectedCustomer ? 'Assign to customer' : 'Select customer first'"></div>
                        </button>
                    </div>
                </div>

                {{-- Cash fields --}}
                <div class="pay-section" x-show="payMethod === 'cash'">
                    <div class="pay-section-title"><i class="fas fa-coins"></i> Cash Received <span
                            class="shortcut-hint">F4</span></div>

                    <div class="amount-input-wrap">
                        <span class="amount-prefix">Af</span>
                        <input class="amount-input" id="cashInput" type="number" x-model.number="cashReceived"
                            @focus="$event.target.select()" placeholder="0" min="0">
                    </div>

                    <div class="quick-amounts">
                        <button type="button" class="qa-btn" @click="cashReceived = grandTotal">Exact</button>
                        <template x-for="amt in quickAmounts" :key="amt">
                            <button type="button" class="qa-btn" @click="cashReceived = amt"
                                x-text="'Af ' + fmt(amt)"></button>
                        </template>
                    </div>

                    <div class="change-box"
                        :class="changeAmount >= 0 ? (changeAmount > 0 ? 'positive' : 'zero') : 'negative'">
                        <span class="change-label" x-text="changeAmount >= 0 ? 'Change' : 'Still Owed'"></span>
                        <span class="change-val">Af <span x-text="fmt(Math.abs(changeAmount))"></span></span>
                    </div>
                </div>

                {{-- Loan fields --}}
                <div class="pay-section" x-show="payMethod === 'loan'" x-cloak>
                    <div class="pay-section-title"><i class="fas fa-file-invoice-dollar"></i> Loan Details</div>
                    <div class="amount-input-wrap" style="margin-bottom:8px">
                        <span class="amount-prefix">Af</span>
                        <input class="amount-input" type="number" x-model.number="loanDeposit"
                            placeholder="0 — initial deposit (optional)" min="0">
                    </div>
                    <div class="tot-row"
                        style="padding:6px 10px;background:var(--amber-dim);border:1px solid rgba(217,119,6,.2);border-radius:var(--r-sm)">
                        <span class="tot-label" style="color:var(--amber)">Balance on loan</span>
                        <span class="tot-val" style="color:var(--amber)">Af <span
                                x-text="fmt(grandTotal - loanDeposit)"></span></span>
                    </div>
                </div>

                {{-- Discount --}}
                <div class="pay-section">
                    <div class="pay-section-title"><i class="fas fa-tag"></i> Discount</div>
                    <div class="disc-row">
                        <div class="disc-type-toggle">
                            <button type="button" class="disc-toggle-btn"
                                :class="discType === 'pct' ? 'active' : ''" @click="discType='pct'">%</button>
                            <button type="button" class="disc-toggle-btn"
                                :class="discType === 'flat' ? 'active' : ''" @click="discType='flat'">Af</button>
                        </div>
                        <div class="amount-input-wrap" style="flex:1">
                            <span class="amount-prefix" x-text="discType==='pct' ? '%' : 'Af'"></span>
                            <input class="amount-input" style="font-size:14px;padding-top:9px;padding-bottom:9px"
                                type="number" x-model.number="discountInput" placeholder="0" min="0">
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
                    <textarea class="notes-input" x-model="saleNotes" placeholder="Optional note for this sale…" rows="2"></textarea>
                </div>

                {{-- FOOTER BUTTONS --}}
                <div class="pay-panel-footer">
                    <button type="button" class="btn-checkout-main"
                        :class="payMethod === 'loan' ? 'btn-checkout-loan' : 'btn-checkout-cash'"
                        @click="processCheckout()"
                        :disabled="cart.length === 0 || processing || (payMethod === 'cash' && cashReceived < grandTotal)">
                        <template x-if="!processing">
                            <span style="display:flex;align-items:center;gap:8px">
                                <i :class="payMethod === 'loan' ? 'fas fa-file-invoice' : 'fas fa-bolt'"></i>
                                <span x-text="payMethod === 'loan' ? 'Confirm Loan Sale' : 'Complete Sale'"></span>
                                <span class="shortcut-hint"
                                    style="background:rgba(255,255,255,.15);border-color:rgba(255,255,255,.2);color:rgba(255,255,255,.7)">F12</span>
                            </span>
                        </template>
                        <template x-if="processing">
                            <span><i class="fas fa-spinner fa-spin"></i> Processing...</span>
                        </template>
                    </button>
                    <button type="button" class="btn-hold" @click="holdSale()" :disabled="cart.length === 0">
                        <i class="fas fa-pause"></i> Hold Sale
                        <span class="shortcut-hint">F9</span>
                    </button>

                    {{-- Add alongside the Hold button in pay-panel-footer --}}
                    <button type="button" @click="recallHeld()"
                        style="width:100%;margin-top:6px;padding:9px;background:var(--teal-dim);border:1px solid rgba(8,145,178,.2);border-radius:var(--r-sm);font-family:var(--body);font-size:12px;font-weight:600;color:var(--teal);cursor:pointer;display:flex;align-items:center;justify-content:center;gap:6px">
                        <i class="fas fa-rotate-left"></i> Recall Held Sale
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
                    <button type="button" class="numpad-close" @click="showNumpad=false"><i
                            class="fas fa-times"></i></button>
                </div>
                <div class="numpad-display">
                    Af <span x-text="numpadValue || '0'"></span>
                </div>
                <div class="numpad-grid">
                    <template x-for="k in ['7','8','9','4','5','6','1','2','3']" :key="k">
                        <button type="button" class="np-btn" @click="numpadPress(k)" x-text="k"></button>
                    </template>
                    <button type="button" class="np-btn np-zero" @click="numpadPress('0')">0</button>
                    <button type="button" class="np-btn np-del" @click="numpadDel()"><i
                            class="fas fa-delete-left"></i></button>
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
                        <div class="rt-row"><span>Subtotal</span><span x-text="'Af ' + fmt(receiptData.subtotal)"></span>
                        </div>
                        <div class="rt-row" x-show="receiptData.discount > 0"><span>Discount</span><span
                                x-text="'- Af ' + fmt(receiptData.discount)"></span></div>
                        <div class="rt-row grand"><span>TOTAL</span><span x-text="'Af ' + fmt(receiptData.total)"></span>
                        </div>
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

        {{-- ════════ QUICK REGISTER CUSTOMER MODAL ════════ --}}
        <div class="numpad-overlay" x-show="showRegisterModal" x-cloak @click.self="showRegisterModal=false">
            <div class="numpad-card" style="width:340px">
                <div class="numpad-head">
                    <span class="numpad-title"><i class="fas fa-user-plus"
                            style="margin-right:6px;color:var(--blue)"></i>Register New Customer</span>
                    <button type="button" class="numpad-close" @click="showRegisterModal=false"><i
                            class="fas fa-times"></i></button>
                </div>
                <div style="padding:1rem 1.25rem;display:flex;flex-direction:column;gap:10px">

                    {{-- Name --}}
                    <div>
                        <label
                            style="font-size:11px;font-weight:600;color:var(--ink-2);text-transform:uppercase;letter-spacing:.06em;display:block;margin-bottom:5px">
                            Full Name <span style="color:var(--red)">*</span>
                        </label>
                        <input type="text" x-model="newCustomer.name"
                            style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:var(--r-sm);font-family:var(--body);font-size:13px;color:var(--ink);outline:none;background:var(--surface-2)"
                            placeholder="Customer full name">
                    </div>

                    {{-- Phone --}}
                    <div>
                        <label
                            style="font-size:11px;font-weight:600;color:var(--ink-2);text-transform:uppercase;letter-spacing:.06em;display:block;margin-bottom:5px">
                            Phone <span style="color:var(--red)">*</span>
                        </label>
                        <input type="text" x-model="newCustomer.phone"
                            style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:var(--r-sm);font-family:var(--body);font-size:13px;color:var(--ink);outline:none;background:var(--surface-2)"
                            placeholder="07XX XXX XXXX">
                    </div>

                    {{-- City --}}
                    <div>
                        <label
                            style="font-size:11px;font-weight:600;color:var(--ink-2);text-transform:uppercase;letter-spacing:.06em;display:block;margin-bottom:5px">Address</label>
                        <input type="text" x-model="newCustomer.address"
                            style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:var(--r-sm);font-family:var(--body);font-size:13px;color:var(--ink);outline:none;background:var(--surface-2)"
                            placeholder="Kabul, Kandahar...">
                    </div>

                    {{-- Notes --}}
                    <div>
                        <label
                            style="font-size:11px;font-weight:600;color:var(--ink-2);text-transform:uppercase;letter-spacing:.06em;display:block;margin-bottom:5px">Notes</label>
                        <textarea x-model="newCustomer.notes" rows="2"
                            style="width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:var(--r-sm);font-family:var(--body);font-size:13px;color:var(--ink);outline:none;background:var(--surface-2);resize:none"
                            placeholder="Optional note..."></textarea>
                    </div>

                    {{-- Error --}}
                    <div x-show="registerError" x-cloak
                        style="padding:8px 10px;background:var(--red-dim);border:1px solid rgba(220,38,38,.2);border-radius:var(--r-sm);font-size:12px;color:var(--red)"
                        x-text="registerError"></div>

                    {{-- Buttons --}}
                    <div style="display:flex;gap:8px;margin-top:4px">
                        <button type="button" @click="showRegisterModal=false"
                            style="flex:1;padding:10px;background:var(--surface-2);border:1px solid var(--border);border-radius:var(--r-sm);font-family:var(--body);font-size:13px;font-weight:500;cursor:pointer;color:var(--ink-2)">
                            Cancel
                        </button>
                        <button type="button" @click="registerCustomer()" :disabled="registeringCustomer"
                            style="flex:2;padding:10px;background:var(--blue);border:none;border-radius:var(--r-sm);font-family:var(--body);font-size:13px;font-weight:600;cursor:pointer;color:#fff">
                            <i class="fas fa-spinner fa-spin" x-show="registeringCustomer"></i>
                            <span x-text="registeringCustomer ? 'Saving...' : 'Register & Select'"></span>
                        </button>
                    </div>
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
            if (el) el.textContent = new Date().toLocaleTimeString('en-US');
            setTimeout(tick, 1000);
        })();

        /* ══════════════════════════════════════════
           POS CHECKOUT ALPINE COMPONENT
        ══════════════════════════════════════════ */
        function posCheckout() {
            return {
                /* URLs */
                urls: {
                    search: '{{ route('pos.products.search') }}',
                    customers: '{{ route('pos.customers.search') }}',
                    store: '{{ route('pos.checkout.store') }}',
                    hold: '{{ route('pos.checkout.hold') }}',
                    recall: '{{ route('pos.checkout.recall') }}', // ← add this
                    csrf: '{{ csrf_token() }}'
                },

                /* Search */
                query: '',
                searchResults: [],
                searching: false,

                /* Customer */
                customerQuery: '',
                customerResults: [],
                selectedCustomer: null,

                /* Cart */
                cart: @json($cartItems ?? []),
                /* pre-fill if coming from dashboard */

                /* Payment */
                payMethod: 'cash',
                cashReceived: 0,
                loanDeposit: 0,
                discountInput: 0,
                discType: 'pct',
                /* 'pct' | 'flat' */
                taxRate: 0,
                /* set to e.g. 0.05 for 5% if needed */
                saleNotes: '',

                /* Options */
                shouldPrintReceipt: true,
                openDrawer: true,
                isReturn: false,

                /* UI state */
                processing: false,
                showNumpad: false,
                numpadValue: '',
                showReceipt: false,
                receiptData: {},

                // Add to the data properties section
                showRegisterModal: false,
                registeringCustomer: false,
                registerError: '',
                newCustomer: {
                    name: '',
                    phone: '',
                    city: '',
                    notes: ''
                },

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
                    return [round(g, 50), round(g, 100), round(g, 500), 1000].filter((v, i, a) => a.indexOf(v) === i &&
                        v >= g).slice(0, 4);
                },
                get custInitials() {
                    if (!this.selectedCustomer) return '';
                    return this.selectedCustomer.name.trim().split(' ').map(w => w[0]).join('').toUpperCase().slice(0,
                        2);
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
                    if (!q) {
                        this.searchResults = [];
                        return;
                    }
                    this.searching = true;
                    try {
                        const r = await fetch(this.urls.search + '?q=' + encodeURIComponent(q), {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        const data = await r.json();
                        this.searchResults = data;
                    } catch (e) {
                        console.error(e);
                    } finally {
                        this.searching = false;
                    }
                },

                clearSearch() {
                    this.query = '';
                    this.searchResults = [];
                    this.searching = false;
                },

                /* ── Customer search ── */
                async searchCustomers() {
                    const q = this.customerQuery.trim();
                    if (!q) {
                        this.customerResults = [];
                        return;
                    }
                    try {
                        const r = await fetch(this.urls.customers + '?q=' + encodeURIComponent(q), {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        this.customerResults = await r.json();
                    } catch (e) {
                        console.error(e);
                    }
                },

                selectCustomer(c) {
                    this.selectedCustomer = c;
                    this.customerQuery = '';
                    this.customerResults = [];
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
                        this.cart.push({
                            ...p,
                            qty: 1,
                            lineTotal: p.price,
                            row_discount: 0
                        });
                    }
                    this.clearSearch();
                    this.cashReceived = 0;
                },

                incQty(idx) {
                    const i = this.cart[idx];
                    if (i.qty < i.stock_quantity) {
                        i.qty++;
                        i.lineTotal = i.price * i.qty;
                    }
                },

                decQty(idx) {
                    const i = this.cart[idx];
                    if (i.qty > 1) {
                        i.qty--;
                        i.lineTotal = i.price * i.qty;
                    } else this.removeItem(idx);
                },

                removeItem(idx) {
                    this.cart.splice(idx, 1);
                    this.cashReceived = 0;
                },

                /* ── Numpad ── */
                openNumpad() {
                    this.numpadValue = this.cashReceived ? String(this.cashReceived) : '';
                    this.showNumpad = true;
                },
                numpadPress(k) {
                    this.numpadValue += k;
                },
                numpadDel() {
                    this.numpadValue = this.numpadValue.slice(0, -1);
                },
                numpadConfirm() {
                    this.cashReceived = parseFloat(this.numpadValue) || 0;
                    this.showNumpad = false;
                },

                /* ── Hold sale ── */
                // Replace the existing holdSale() method
                holdSale() {
                    if (!this.cart.length) return;
                    if (!confirm('Put this sale on hold?')) return;
                    fetch(this.urls.hold, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.urls.csrf
                            },
                            body: JSON.stringify({
                                cart: this.cart,
                                notes: this.saleNotes
                            })
                        })
                        .then(r => r.json())
                        .then(data => {
                            if (data.success) {
                                this.cart = [];
                                this.cashReceived = 0;
                                // Show hold code prominently
                                alert(
                                    `Sale held successfully.\n\nRecall Code: ${data.hold_code}\n\nTell the customer their code or note it down.`
                                );
                            }
                        })
                        .catch(e => console.error(e));
                },


                // Add method
                async recallHeld() {
                    const code = prompt('Enter hold code:');
                    if (!code) return;
                    try {
                        const r = await fetch(this.urls.recall + '?code=' + code.trim().toUpperCase(), {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        const data = await r.json();
                        if (data.success) {
                            this.cart = data.cart;
                            alert(`Sale recalled: ${data.cart.length} item(s) loaded.`);
                        } else {
                            alert(data.message ?? 'Hold code not found.');
                        }
                    } catch (e) {
                        alert('Error recalling sale.');
                    }
                },

                /* ── Process checkout ── */
                async processCheckout() {
                    if (!this.cart.length) return;
                    if (this.payMethod === 'cash' && this.cashReceived < this.grandTotal) return;
                    this.processing = true;
                    try {
                        const payload = {
                            cart: this.cart,
                            payment_method: this.payMethod,
                            cash_received: this.cashReceived,
                            loan_deposit: this.loanDeposit,
                            discount_type: this.discType,
                            discount: this.discountInput,
                            tax_rate: this.taxRate,
                            customer_id: this.selectedCustomer?.id ?? null,
                            notes: this.saleNotes,
                            print_receipt: this.shouldPrintReceipt,
                            open_drawer: this.openDrawer,
                            is_return: this.isReturn,
                        };

                        const r = await fetch(this.urls.store, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.urls.csrf
                            },
                            body: JSON.stringify(payload)
                        });

                        const data = await r.json();

                        if (data.success) {
                            this.receiptData = {
                                sale_id: data.sale_id,
                                datetime: new Date().toLocaleString('en-GB'),
                                cashier: data.cashier ?? 'Cashier',
                                items: this.cart,
                                subtotal: this.subtotal,
                                discount: this.discountAmount,
                                total: this.grandTotal,
                                cash_received: this.cashReceived,
                                change: this.changeAmount,
                                method: this.payMethod,
                            };
                            this.showReceipt = true;
                        } else {
                            alert(data.message ?? 'Checkout failed. Please try again.');
                        }
                    } catch (e) {
                        console.error(e);
                        alert('Network error. Please check connection.');
                    } finally {
                        this.processing = false;
                    }
                },


                async registerCustomer() {
                    if (!this.newCustomer.name.trim()) {
                        this.registerError = 'Name is required.';
                        return;
                    }
                    if (!this.newCustomer.phone.trim()) {
                        this.registerError = 'Phone number is required.';
                        return;
                    }
                    this.registeringCustomer = true;
                    this.registerError = '';
                    try {
                        const r = await fetch('{{ route('pos.customers.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.urls.csrf,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify(this.newCustomer)
                        });
                        const data = await r.json();
                        if (data.success) {
                            this.selectCustomer(data.customer); // auto-select
                            this.showRegisterModal = false;
                            this.newCustomer = {
                                name: '',
                                phone: '',
                                address: '',
                                notes: ''
                            };
                        } else {
                            this.registerError = data.message ?? 'Registration failed.';
                        }
                    } catch (e) {
                        this.registerError = 'Network error. Try again.';
                    } finally {
                        this.registeringCustomer = false;
                    }
                },

                openPrintPreview() {
                    if (!this.showReceipt) return;
                    window.print();
                },

                newSale() {
                    this.cart = [];
                    this.cashReceived = 0;
                    this.loanDeposit = 0;
                    this.discountInput = 0;
                    this.selectedCustomer = null;
                    this.customerQuery = '';
                    this.saleNotes = '';
                    this.payMethod = 'cash';
                    this.isReturn = false;
                    this.showReceipt = false;
                    this.$nextTick(() => this.focusSearch());
                },

                /* ── Utility ── */
                fmt(n) {
                    return Number(n || 0).toLocaleString('en-US', {
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 0
                    });
                }
            };
        }
    </script>
@endpush
