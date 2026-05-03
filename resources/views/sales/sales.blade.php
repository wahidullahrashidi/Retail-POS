@extends('layouts.app')

@push('styles')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
        <a href="{{ route('pos.dashboard') }}" class="btn btn-primary">
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
