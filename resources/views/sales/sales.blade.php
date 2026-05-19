@extends('layouts.app')

@push('styles')
    @if(file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot'))) @vite(['resources/css/pages/sales.css']) @endif
@endpush

@section('content')
    <div class="sl" x-data="salesPage()" x-init="init()">

        {{-- ════ TOPBAR ════ --}}
        <div class="sl-top">
            <div class="sl-title">Afghan <em>POS</em> — {{ __('messages.sales_log') }}</div>
            <div class="top-r">
                <button class="btn btn-ghost" @click="exportCsv()">
                    <i class="fas fa-file-csv"></i> {{ __('messages.export_csv') }}
                </button>
                <a href="{{ route('pos.poscheck') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> {{ __('messages.new_sale') }}
                </a>
            </div>
        </div>

        {{-- ════ STATS ════ --}}
        <div class="stat-strip">
            <div class="stat-tile" style="--ac:var(--blue)">
                <div class="st-label">{{ __('messages.todays_revenue') }} <span style="color:var(--blue)"><i class="fas fa-coins"></i></span>
                </div>
                <div class="st-val" style="font-size:18px">{{ __('messages.af') }} {{ number_format($stats['today_revenue'] ?? 0) }}</div>
                <div class="st-sub">{{ $stats['today_count'] ?? 0 }} {{ __('messages.transactions_today') }}</div>
            </div>
            <div class="stat-tile" style="--ac:var(--green)">
                <div class="st-label">{{ __('messages.cash_sales') }} <span style="color:var(--green)"><i class="fas fa-money-bill"></i></span>
                </div>
                <div class="st-val" style="font-size:18px">{{ __('messages.af') }} {{ number_format($stats['today_cash'] ?? 0) }}</div>
                <div class="st-sub">{{ __('messages.todays_cash_revenue') }}</div>
            </div>
            <div class="stat-tile" style="--ac:var(--amber)">
                <div class="st-label">{{ __('messages.loan_sales') }} <span style="color:var(--amber)"><i class="fas fa-file-invoice"></i></span>
                </div>
                <div class="st-val" style="font-size:18px">{{ __('messages.af') }} {{ number_format($stats['today_loan'] ?? 0) }}</div>
                <div class="st-sub">{{ __('messages.todays_credit_sales') }}</div>
            </div>
            <div class="stat-tile" style="--ac:var(--red)">
                <div class="st-label">{{ __('messages.refunded') }} <span style="color:var(--red)"><i class="fas fa-rotate-left"></i></span>
                </div>
                <div class="st-val" style="color:var(--red)">{{ $stats['today_refunds'] ?? 0 }}</div>
                <div class="st-sub">{{ __('messages.sales_refunded_today') }}</div>
            </div>
            <div class="stat-tile" style="--ac:var(--violet)">
                <div class="st-label">{{ __('messages.avg_ticket') }} <span style="color:var(--violet)"><i class="fas fa-receipt"></i></span>
                </div>
                <div class="st-val" style="font-size:18px">{{ __('messages.af') }} {{ number_format($stats['today_avg'] ?? 0) }}</div>
                <div class="st-sub">{{ __('messages.per_transaction_today') }}</div>
            </div>
        </div>

        {{-- ════ TOOLBAR ════ --}}
        <div class="sl-toolbar">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input class="sl-search" type="search" autocomplete="off" autocapitalize="off" spellcheck="false" x-model="search" @input.debounce.350ms="loadSales()"
                    placeholder="{{ __('messages.sale_search_placeholder') }}">
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
                <option value="">{{ __('messages.all_methods') }}</option>
                <option value="cash">{{ __('messages.cash') }}</option>
                <option value="loan">{{ __('messages.loan') }}</option>
            </select>

            <select class="f-sel" x-model="filterStatus" @change="loadSales()">
                <option value="">{{ __('messages.all_statuses') }}</option>
                <option value="completed">{{ __('messages.completed') }}</option>
                <option value="held">{{ __('messages.held') }}</option>
                <option value="refunded">{{ __('messages.refunded') }}</option>
                <option value="cancelled">{{ __('messages.cancelled') }}</option>
            </select>

            <select class="f-sel" x-model="filterCashier" @change="loadSales()">
                <option value="">{{ __('messages.all_cashiers') }}</option>
                @foreach ($cashiers ?? [] as $cashier)
                    <option value="{{ $cashier->id }}">{{ $cashier->name }}</option>
                @endforeach
            </select>

            <div class="tab-strip">
                <button type="button" class="tab-btn" :class="tab === 'all' ? 'active' : ''"
                    @click="tab='all';loadSales()">{{ __('messages.all') }}</button>
                <button type="button" class="tab-btn" :class="tab === 'today' ? 'active' : ''"
                    @click="tab='today';loadSales()">{{ __('messages.today') }}</button>
                <button type="button" class="tab-btn" :class="tab === 'held' ? 'active' : ''"
                    @click="tab='held';loadSales()">{{ __('messages.held') }}</button>
                <button type="button" class="tab-btn" :class="tab === 'refunded' ? 'active' : ''"
                    @click="tab='refunded';loadSales()">{{ __('messages.refunded') }}</button>
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
                        <p>{{ __('messages.no_sales_found') }}<br>{{ __('messages.adjust_filters') }}</p>
                    </div>

                    <table class="sl-table {{ in_array(app()->getLocale(), ['ps', 'dr']) ? 'rtl-table' : '' }}" x-show="sales.length > 0">
                        <thead>
                            <tr>
                                <th @click="sort('local_id')">{{ __('messages.sale_id') }} <i class="fas fa-sort"></i></th>
                                <th @click="sort('created_at')">{{ __('messages.date_time') }} <i class="fas fa-sort"></i></th>
                                <th>{{ __('messages.customer') }}</th>
                                <th>{{ __('messages.cashier') }}</th>
                                <th>{{ __('messages.method') }}</th>
                                <th @click="sort('total_amount')" class="cell-right">{{ __('messages.total') }} <i class="fas fa-sort"></i>
                                </th>
                                <th class="cell-right">{{ __('messages.discount') }}</th>
                                <th>{{ __('messages.status') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="s in sales" :key="s.id">
                                <tr :class="selected?.id === s.id ? 'selected' : ''" @click="openDetail(s)">
                                    <td><span class="sale-id" x-text="s.local_id"></span></td>
                                    <td>
                                        <div class="sale-time" x-text="s.date"></div>
                                        <div class="sale-time" style="font-size:10px;opacity:.7" x-text="s.time"></div>
                                    </td>
                                    <td>
                                        <div class="sale-cust" x-text="s.customer || '{{ __('messages.walk_in') }}'"></div>
                                        <div class="sale-cust-sub" x-show="s.customer_phone" x-text="s.customer_phone">
                                        </div>
                                    </td>
                                    <td style="font-size:12px;color:var(--ink2)" x-text="s.cashier"></td>
                                    <td>
                                        <span class="pill"
                                            :class="s.payment_method === 'cash' ? 'pill-blue' : 'pill-amber'"
                                            x-text="s.payment_method"></span>
                                    </td>
                                    <td class="cell-right">
                                        <span class="sale-amt" x-text="'{{ __('messages.af') }} ' + fmt(s.total_amount)"></span>
                                    </td>
                                    <td class="cell-right" style="font-family:var(--mono);font-size:12px;color:var(--red)"
                                        x-text="s.discount_amount > 0 ? '- {{ __('messages.af') }} ' + fmt(s.discount_amount) : '—'"></td>
                                    <td>
                                        <span class="pill"
                                            :class="{
                                                'pill-green': s.status==='completed',
                                                'pill-amber': s.status==='held',
                                                'pill-red': s.status==='refunded',
                                                'pill-gray': s.status==='cancelled',
                                                'pill-violet': s.sale_type==='return',
                                            }"
                                            x-text="s.sale_type==='return' ? '{{ __('messages.return') }}' : s.status">
                                        </span>
                                    </td>
                                    <td @click.stop>
                                        <div class="row-acts">
                                            <button type="button" class="btn btn-ghost btn-sm" @click="openDetail(s)"
                                                title="{{ __('messages.view') }}">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-ghost btn-sm"
                                                @click="reprintReceipt(s)" title="{{ __('messages.reprint') }}">
                                                <i class="fas fa-print"></i>
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm"
                                                x-show="s.status === 'completed' && s.sale_type !== 'return'"
                                                @click="openRefund(s)" title="{{ __('messages.refund') }}">
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
                            {{ __('messages.showing') }} <span x-text="pagination.from"></span>–<span x-text="pagination.to"></span>
                            {{ __('messages.of') }} <span x-text="pagination.total"></span> {{ __('messages.sales') }}
                        </div>
                        <div class="pag-btns">
                            <button class="pag-btn" @click="goPage(pagination.current_page-1)"
                                :disabled="pagination.current_page === 1">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <template x-for="p in pages" :key="p">
                                <button class="pag-btn" :class="p === pagination.current_page ? 'active' : ''"
                                    @click="goPage(p)" x-text="p"></button>
                            </template>
                            <button class="pag-btn" @click="goPage(pagination.current_page+1)"
                                :disabled="pagination.current_page === pagination.last_page">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ════ DETAIL PANEL ════ --}}
            <div class="detail-panel" x-show="selected" x-cloak>
                <div class="dp-head">
                    <span class="dp-head-title">{{ __('messages.sale_detail') }}</span>
                    <button class="dp-close" @click="selected=null"><i class="fas fa-times"></i></button>
                </div>

                <div class="dp-body">

                    {{-- Receipt header --}}
                    <div class="receipt-strip">
                        <div class="rs-id" x-text="selected?.local_id"></div>
                        <div class="rs-amount"><span>{{ __('messages.af') }}</span><span x-text="fmt(selected?.total_amount||0)"></span></div>
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
                                <i
                                    :class="selected?.payment_method === 'cash' ? 'fas fa-money-bill' :
                                        'fas fa-file-invoice'"></i>
                                <strong x-text="selected?.payment_method"></strong>
                            </div>
                        </div>
                    </div>

                    {{-- Status bar --}}
                    <div
                        style="padding:.7rem 1.25rem;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:8px">
                        <span class="pill"
                            :class="{
                                'pill-green': selected?.status==='completed',
                                'pill-amber': selected?.status==='held',
                                'pill-red': selected?.status==='refunded',
                                'pill-gray': selected?.status==='cancelled',
                                'pill-violet': selected?.sale_type==='return',
                            }"
                            x-text="selected?.sale_type==='return' ? '{{ __('messages.return_refund') }}' : selected?.status">
                        </span>
                        <span x-show="selected?.hold_code"
                            style="font-family:var(--mono);font-size:11px;color:var(--amber)">
                            {{ __('messages.hold_code') }}: <strong x-text="selected?.hold_code"></strong>
                        </span>
                    </div>

                    {{-- Customer --}}
                    <div class="dp-section">
                        <div class="dp-section-title"><i class="fas fa-user"></i> {{ __('messages.customer') }}</div>
                        <div class="info-grid">
                            <div class="info-field">
                                <div class="if-label">{{ __('messages.name') }}</div>
                                <div class="if-val" x-text="selected?.customer || '{{ __('messages.walk_in') }}'"></div>
                            </div>
                            <div class="info-field">
                                <div class="if-label">{{ __('messages.phone') }}</div>
                                <div class="if-val mono" x-text="selected?.customer_phone || '—'"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Items --}}
                    <div class="dp-section">
                        <div class="dp-section-title"><i class="fas fa-list"></i> {{ __('messages.items') }} (<span
                                x-text="detailItems.length"></span>)</div>
                        <div x-show="detailLoading"
                            style="text-align:center;padding:1rem;color:var(--ink3);font-size:12px">
                            <i class="fas fa-spinner fa-spin"></i> {{ __('messages.loading') }}
                        </div>
                        <div x-show="!detailLoading">
                            <template x-for="item in detailItems" :key="item.id">
                                <div class="item-row" :class="item.is_returned ? 'ir-returned' : ''">
                                    <div>
                                        <div class="ir-name" x-text="item.product_name"></div>
                                        <div class="ir-sku" x-text="item.sku + (item.is_returned ? ' · {{ __('messages.returned') }}' : '')">
                                        </div>
                                    </div>
                                    <div class="ir-right">
                                        <div class="ir-qty" x-text="item.quantity + ' × {{ __('messages.af') }} ' + fmt(item.unit_price)">
                                        </div>
                                        <div class="ir-total" x-text="'{{ __('messages.af') }} ' + fmt(item.line_total)"></div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Totals --}}
                    <div class="dp-section">
                        <div class="dp-section-title"><i class="fas fa-calculator"></i> {{ __('messages.summary') }}</div>
                        <div class="tot-row"><span>{{ __('messages.subtotal') }}</span><span class="tot-mono"
                                x-text="'{{ __('messages.af') }} ' + fmt(selected?.subtotal||0)"></span></div>
                        <div class="tot-row" x-show="(selected?.discount_amount||0) > 0">
                            <span style="color:var(--red)">{{ __('messages.discount') }}</span>
                            <span class="tot-mono" style="color:var(--red)"
                                x-text="'- {{ __('messages.af') }} ' + fmt(selected?.discount_amount||0)"></span>
                        </div>
                        <div class="tot-row" x-show="(selected?.tax_amount||0) > 0">
                            <span>{{ __('messages.tax') }}</span>
                            <span class="tot-mono" x-text="'{{ __('messages.af') }} ' + fmt(selected?.tax_amount||0)"></span>
                        </div>
                        <div class="tot-row grand">
                            <span>{{ __('messages.grand_total') }}</span>
                            <span x-text="'{{ __('messages.af') }} ' + fmt(selected?.total_amount||0)"></span>
                        </div>
                        <div class="tot-row" x-show="selected?.payment_method==='cash'" style="margin-top:6px">
                            <span style="color:var(--ink3)">{{ __('messages.cash_received') }}</span>
                            <span class="tot-mono" x-text="'{{ __('messages.af') }} ' + fmt(selected?.amount_paid||0)"></span>
                        </div>
                        <div class="tot-row" x-show="selected?.payment_method==='cash' && (selected?.change_amount||0)>0">
                            <span style="color:var(--ink3)">{{ __('messages.change_given') }}</span>
                            <span class="tot-mono" x-text="'{{ __('messages.af') }} ' + fmt(selected?.change_amount||0)"></span>
                        </div>
                        <div class="tot-row" x-show="selected?.payment_method==='loan'" style="margin-top:6px">
                            <span style="color:var(--amber)">{{ __('messages.loan_balance') }}</span>
                            <span class="tot-mono" style="color:var(--amber)"
                                x-text="'{{ __('messages.af') }} ' + fmt((selected?.total_amount||0) - (selected?.amount_paid||0))"></span>
                        </div>
                    </div>

                    {{-- Notes --}}
                    <div class="dp-section" x-show="selected?.notes">
                        <div class="dp-section-title"><i class="fas fa-pen"></i> {{ __('messages.notes') }}</div>
                        <div style="font-size:12.5px;color:var(--ink2);line-height:1.6" x-text="selected?.notes"></div>
                    </div>

                </div>

                {{-- Panel footer --}}
                <div class="dp-foot">
                    <button type="button" class="btn btn-ghost" style="flex:1" @click="reprintReceipt(selected)">
                        <i class="fas fa-print"></i> {{ __('messages.reprint_receipt_btn') }}
                    </button>
                    <button type="button" class="btn btn-danger"
                        x-show="selected?.status === 'completed' && selected?.sale_type !== 'return'"
                        @click="openRefund(selected)">
                        <i class="fas fa-rotate-left"></i> {{ __('messages.refund') }}
                    </button>
                </div>
            </div>

        </div>{{-- /sl-main --}}

        {{-- ════ REFUND MODAL ════ --}}
        <div class="modal-overlay" x-show="showRefundModal" x-cloak @click.self="showRefundModal=false">
            <div class="modal-card">
                <div class="modal-head">
                    <div class="modal-title">{{ __('messages.process_refund') }}</div>
                    <button class="modal-close" @click="showRefundModal=false"><i class="fas fa-times"></i></button>
                </div>
                <div class="modal-body">

                    <div class="warn-box">
                        <i class="fas fa-triangle-exclamation"></i>
                        <div>
                            {{ __('messages.refund_warning') }}
                        </div>
                    </div>

                    <div style="margin-bottom:1rem">
                        <div
                            style="font-size:11px;font-weight:700;color:var(--ink3);text-transform:uppercase;letter-spacing:.08em;margin-bottom:.6rem">
                            {{ __('messages.select_items_to_refund') }}
                        </div>
                        <template x-for="item in refundItems.filter(i => i.max_refundable > 0)" :key="item.id">
                            <div class="refund-item" :class="item.selected ? 'selected' : ''"
                                @click="item.selected = !item.selected">
                                <div class="ri-check">
                                    <i class="fas fa-check" x-show="item.selected"></i>
                                </div>
                                <div class="ri-info">
                                    <div class="ri-name" x-text="item.product_name"></div>
                                    <div class="ri-detail"
                                        x-text="item.sku + ' · ' + item.quantity + ' {{ __('messages.sold_at') }} {{ __('messages.af') }} ' + fmt(item.unit_price)">
                                    </div>
                                </div>
                                <div @click.stop style="display:flex;flex-direction:column;align-items:flex-end;gap:4px">
                                    <span class="ri-amt" x-text="'- {{ __('messages.af') }} ' + fmt(calcRefundAmount(item))"></span>
                                    <div class="qty-refund" x-show="item.selected">
                                        <span style="font-size:11px;color:var(--ink3)">{{ __('messages.qty') }}:</span>
                                        <input type="number" x-model.number="item.refund_qty" :max="item.max_refundable"
                                            min="1" @click.stop>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- Refund summary --}}
                    <div class="refund-summary" x-show="refundTotal > 0" x-cloak>
                        <div class="rs-row"><span>{{ __('messages.items_selected') }}</span><span
                                x-text="refundItems.filter(i=>i.selected).length"></span></div>
                        <div class="rs-row"><span>{{ __('messages.total_refund_amount') }}</span><span
                                x-text="'{{ __('messages.af') }} ' + fmt(refundTotal)"></span></div>
                    </div>

                    <div style="margin-top:1rem">
                        <label class="field-label">{{ __('messages.reason_for_refund') }} <span style="color:var(--red)">*</span></label>
                        <textarea class="field-input" x-model="refundReason" placeholder="{{ __('messages.refund_placeholder') }}"></textarea>
                    </div>

                    <div class="form-err" x-show="refundError" x-text="refundError" x-cloak></div>
                </div>
                <div class="modal-foot">
                    <button type="button" class="btn btn-ghost" @click="showRefundModal=false">{{ __('messages.cancel') }}</button>
                    <button type="button" class="btn btn-danger" @click="processRefund()"
                        :disabled="refundTotal === 0 || saving">
                        <i class="fas fa-spinner fa-spin" x-show="saving"></i>
                        <span x-text="saving ? '{{ __('messages.processing') }}' : '{{ __('messages.confirm_refund_of') }} {{ __('messages.af') }} ' + fmt(refundTotal)"></span>
                    </button>
                </div>
            </div>
        </div>

        {{-- ════ RECEIPT PRINT AREA ════ --}}
        <div id="receipt-print" style="display:none">
            <div style="font-family:monospace;font-size:12px;max-width:300px;margin:0 auto;padding:10px">
                <div style="text-align:center;margin-bottom:10px">
                    <div style="font-size:18px;font-weight:bold">{{ __('messages.afghan_pos') }}</div>
                    <div>{{ __('messages.retail_management_system') }}</div>
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
                    {{ __('messages.thank_you') }}
                </div>
            </div>
        </div>

    </div>{{-- /sl --}}
@endsection

@push('scripts')
    <script>
        const registerSalesPage = () => {
            Alpine.data('salesPage', () => ({

                /* list */
                sales: [],
                pagination: {},
                loading: true,
                search: '',
                dateFrom: '',
                dateTo: '',
                filterMethod: '',
                filterStatus: '',
                filterCashier: '',
                tab: 'all',
                sortCol: 'created_at',
                sortDir: 'desc',
                currentPage: 1,

                /* detail */
                selected: null,
                detailItems: [],
                detailLoading: false,

                /* refund */
                showRefundModal: false,
                refundSale: null,
                refundItems: [],
                refundReason: '',
                refundError: '',
                saving: false,

                /* urls */
                urls: {
                    list: '{{ route('pos.sales.index') }}',
                    detail: '{{ url('pos/sales') }}',
                    // delete: '{{ url('pos/sales') }}',
                    refund: '{{ route('pos.sales.refund') }}',
                    export: '{{ route('pos.sales.export') }}',
                    csrf: document.querySelector('meta[name=csrf-token]')?.content ||
                        '{{ csrf_token() }}',
                },

                /* computed */
                get refundTotal() {
                    return this.refundItems
                        .filter(i => i.selected)
                        .reduce((s, i) => s + this.calcRefundAmount(i), 0);
                },

                get pages() {
                    return Array.from({
                        length: this.pagination.last_page || 0
                    }, (_, i) => i + 1);
                },

                /* ── Init ── */
                init() {
                    // Load all sales by default
                    this.loadSales();
                },

                /* ── Load sales list ── */
                async loadSales() {
                    this.loading = true;
                    try {
                        const p = new URLSearchParams({
                            q: this.search,
                            from: this.dateFrom,
                            to: this.dateTo,
                            method: this.filterMethod,
                            status: this.filterStatus,
                            cashier: this.filterCashier,
                            tab: this.tab,
                            sort: this.sortCol,
                            dir: this.sortDir,
                            page: this.currentPage,
                        });
                        const r = await fetch(this.urls.list + '?' + p, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            credentials: 'same-origin'
                        });
                        if (!r.ok) {
                            const err = await r.text();
                            throw new Error(`Failed to load sales: ${r.status} ${err}`);
                        }
                        const d = await r.json();
                        this.sales = d.data.slice(0, 10);
                        this.pagination = d.meta;
                    } catch (e) {
                        console.error(e);
                        this.sales = [];
                        this.pagination = {};
                    } finally {
                        this.loading = false;
                    }
                },

                sort(col) {
                    if (this.sortCol === col) this.sortDir = this.sortDir === 'asc' ? 'desc' : 'asc';
                    else {
                        this.sortCol = col;
                        this.sortDir = 'desc';
                    }
                    this.loadSales();
                },

                goPage(p) {
                    if (p < 1 || p > this.pagination.last_page) return;
                    this.currentPage = p;
                    this.loadSales();
                },

                /* ── Detail panel ── */
                async openDetail(s) {
                    this.selected = s;
                    this.detailItems = [];
                    this.detailLoading = true;
                    try {
                        const r = await fetch(`${this.urls.detail}/${s.id}/items`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            credentials: 'same-origin'
                        });
                        if (!r.ok) {
                            const err = await r.text();
                            throw new Error(`Failed to load sale detail: ${r.status} ${err}`);
                        }
                        this.detailItems = await r.json();
                    } catch (e) {
                        console.error(e);
                    } finally {
                        this.detailLoading = false;
                    }
                },

                // delete sales
                // async deleteSale(s) {

                //     if (!confirm(`Delete sale #${s.local_id}?`)) {
                //         return;
                //     }

                //     try {

                //         const r = await fetch(`${this.urls.delete}/${s.id}`, {
                //             method: 'DELETE',
                //             headers: {
                //                 'Accept': 'application/json',
                //                 'X-CSRF-TOKEN': this.urls.csrf,
                //                 'X-Requested-With': 'XMLHttpRequest'
                //             },
                //             credentials: 'same-origin'
                //         });

                //         if (!r.ok) {
                //             throw new Error('Delete failed');
                //         }

                //         this.sales = this.sales.filter(x => x.id !== s.id);

                //     } catch (e) {
                //         console.error(e);
                //         alert('Failed to delete sale');
                //     }
                // },

                /* ── Refund ── */
                async openRefund(s) {

                    this.refundSale = s;

                    this.refundItems = [];
                    this.refundReason = '';
                    this.refundError = '';

                    try {

                        const r = await fetch(`${this.urls.detail}/${s.id}/items`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            credentials: 'same-origin'
                        });

                        if (!r.ok) {
                            throw new Error('{{ __('messages.failed_to_load_refund_items') }}.');
                        }

                        const items = await r.json();

                        this.refundItems = items.map(i => ({

                            ...i,

                            selected: false,

                            max_refundable: i.quantity - (i.returned_qty || 0),

                            refund_qty: Math.max(
                                1,
                                i.quantity - (i.returned_qty || 0)
                            ),
                        }));

                        this.showRefundModal = true;

                    } catch (e) {

                        console.error(e);

                        alert('Failed to load refund items.');
                    }
                },

                async processRefund() {
                    const selected = this.refundItems.filter(i => i.selected);
                    if (!selected.length) {
                        this.refundError = 'Select at least one item to refund.';
                        return;
                    }
                    if (!this.refundReason.trim()) {
                        this.refundError = 'Reason is required.';
                        return;
                    }

                    // Validate quantities
                    for (const item of selected) {
                        if (item.refund_qty < 1 || item.refund_qty > item.max_refundable) {
                            this.refundError = `Invalid quantity for "${item.product_name}".`;
                            return;
                        }
                    }

                    this.saving = true;
                    this.refundError = '';
                    try {
                        const r = await fetch(this.urls.refund, {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.urls.csrf
                            },
                            body: JSON.stringify({
                                sale_id: this.refundSale.id,
                                items: selected.map(i => ({
                                    sale_item_id: i.id,
                                    qty: i.refund_qty
                                })),
                                reason: this.refundReason,
                            })
                        });
                        const d = await r.json();
                        if (d.success) {
                            this.showRefundModal = false;
                            this.selected = null;
                            this.loadSales();
                        } else {
                            this.refundError = d.message ?? '{{ __('messages.refund_failed') }}.';
                        }
                    } catch (e) {
                        this.refundError = '{{ __('messages.network_error') }}.';
                    } finally {
                        this.saving = false;
                    }
                },

                /* ── Receipt reprint ── */
                reprintReceipt(s) {
                    if (!s) return;
                    document.getElementById('rp-id').textContent = '{{ __('messages.sale') }}: ' + s.local_id;
                    document.getElementById('rp-date').textContent = s.date + ' ' + s.time;
                    document.getElementById('rp-cashier').textContent = '{{ __('messages.cashier') }}: ' + s.cashier;
                    document.getElementById('rp-items').innerHTML = this.detailItems.map(i =>
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
                    window.printSection('#receipt-print');
                    setTimeout(() => {
                        el.style.display = 'none';
                    }, 500);
                },

                /* ── Export ── */
                exportCsv() {
                    const p = new URLSearchParams({
                        from: this.dateFrom,
                        to: this.dateTo,
                        tab: this.tab,
                        method: this.filterMethod,
                        status: this.filterStatus
                    });
                    window.location.href = this.urls.export+'?' + p;
                },

                /* ── Helpers ── */
                fmt(n) {
                    return Number(n || 0).toLocaleString('en-US', {
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 0
                    });
                },
                // calculate refund amount
                calcRefundAmount(item) {

                    const gross = item.unit_price * item.refund_qty;

                    if (!this.refundSale) {
                        return gross;
                    }

                    if (this.refundSale.payment_method !== 'loan') {
                        return gross;
                    }

                    const paid = Number(this.refundSale.amount_paid || 0);
                    const total = Number(this.refundSale.total_amount || 0);

                    if (total <= 0) {
                        return 0;
                    }

                    const paidRatio = paid / total;

                    return gross * paidRatio;
                },
            }));
        };
        document.addEventListener('alpine:init', registerSalesPage);
        if (window.Alpine && typeof window.Alpine.data === 'function') registerSalesPage();
    </script>

    <style>
        @media print {
            #receipt-print {
                display: block !important;
            }
        }
    </style>
@endpush
