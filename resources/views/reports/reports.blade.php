@extends('layouts.app')

@push('styles')
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/pages/reports.css'])
    @endif
@endpush

@section('content')
    <div class="rp" x-data="reportsPage()" x-init="init()">

        {{-- ════ TOPBAR ════ --}}
        <div class="rp-top">
            <div class="rp-title">Afghan <em>POS</em> — {{ __('messages.reports') }}</div>
            <div class="top-right">
                <button class="btn btn-ghost" @click="printReport()">
                    <i class="fas fa-print"></i> {{ __('messages.print') }}
                </button>
                <button class="btn btn-ghost" @click="exportPdf()">
                    <i class="fas fa-file-pdf"></i> {{ __('messages.export_pdf') }}
                </button>
                <button class="btn btn-primary" @click="exportCsv()">
                    <i class="fas fa-file-csv"></i> {{ __('messages.export_csv') }}
                </button>
            </div>
        </div>

        {{-- ════ DATE TOOLBAR ════ --}}
        <div class="date-toolbar">
            <div class="date-preset-group">
                <button type="button" class="dp-btn" :class="preset === 'today' ? 'active' : ''"
                    @click="setPreset('today')">{{ __('messages.today') }}</button>
                <button type="button" class="dp-btn" :class="preset === 'yesterday' ? 'active' : ''"
                    @click="setPreset('yesterday')">{{ __('messages.yesterday') }}</button>
                <button type="button" class="dp-btn" :class="preset === 'week' ? 'active' : ''" @click="setPreset('week')">{{ __('messages.this_week') }}</button>
                <button type="button" class="dp-btn" :class="preset === 'month' ? 'active' : ''" @click="setPreset('month')">{{ __('messages.this_month') }}</button>
                <button type="button" class="dp-btn" :class="preset === 'quarter' ? 'active' : ''"
                    @click="setPreset('quarter')">{{ __('messages.quarter') }}</button>
                <button type="button" class="dp-btn" :class="preset === 'year' ? 'active' : ''" @click="setPreset('year')">{{ __('messages.this_year') }}</button>
            </div>
            <span class="date-sep">|</span>
            <input type="date" class="date-input" x-model="dateFrom">
            <span class="date-sep">→</span>
            <input type="date" class="date-input" x-model="dateTo">
            <select class="filter-select" x-model="cashierId" @change="loadAll()">
                <option value="">{{ __('messages.all_cashiers') }}</option>
                @foreach ($cashiers ?? [] as $cashier)
                    <option value="{{ $cashier->id }}">{{ $cashier->name }}</option>
                @endforeach
            </select>
            <button type="button" class="date-apply" @click="loadAll()">
                <i class="fas fa-rotate"></i> {{ __('messages.apply') }}
            </button>
            <span class="date-label" x-text="dateRangeLabel"></span>
        </div>

        {{-- ════ TABS ════ --}}
        <div class="rp-tabs">
            <button type="button" class="rp-tab" :class="tab === 'overview' ? 'active' : ''" @click="switchTab('overview')">
                <i class="fas fa-chart-pie"></i> {{ __('messages.overview') }}
            </button>
            <button type="button" class="rp-tab" :class="tab === 'sales' ? 'active' : ''" @click="switchTab('sales')">
                <i class="fas fa-chart-line"></i> {{ __('messages.sales') }}
            </button>
            <button type="button" class="rp-tab" :class="tab === 'products' ? 'active' : ''" @click="switchTab('products')">
                <i class="fas fa-boxes-stacked"></i> {{ __('messages.products') }}
            </button>
            <button type="button" class="rp-tab" :class="tab === 'inventory' ? 'active' : ''" @click="switchTab('inventory')">
                <i class="fas fa-warehouse"></i> {{ __('messages.inventory') }}
            </button>
            <button type="button" class="rp-tab" :class="tab === 'cashiers' ? 'active' : ''" @click="switchTab('cashiers')">
                <i class="fas fa-users"></i> {{ __('messages.cashiers') }}
            </button>
            <button type="button" class="rp-tab" :class="tab === 'loans' ? 'active' : ''" @click="switchTab('loans')">
                <i class="fas fa-file-invoice-dollar"></i> {{ __('messages.loans') }}
                <span class="tab-badge" x-show="data.loan_overdue > 0" x-text="data.loan_overdue"></span>
            </button>
            <button type="button" class="rp-tab" :class="tab === 'zreport' ? 'active' : ''" @click="switchTab('zreport')">
                <i class="fas fa-file-alt"></i> {{ __('messages.z_report') }}
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
                        <div class="kpi-label">{{ __('messages.total_revenue') }} <span class="kpi-icon" style="color:var(--blue)"><i
                                    class="fas fa-coins"></i></span></div>
                        <div class="kpi-val" x-text="'{{ __('messages.af') }} ' + fmt(data.total_revenue || 0)"></div>
                        <div class="kpi-sub">
                            <span class="trend" :class="data.revenue_trend >= 0 ? 'trend-up' : 'trend-dn'">
                                <i :class="data.revenue_trend >= 0 ? 'fas fa-arrow-up' : 'fas fa-arrow-down'"></i>
                                <span x-text="Math.abs(data.revenue_trend||0).toFixed(1) + '%'"></span>
                            </span>
                            {{ __('messages.vs_previous_period') }}
                        </div>
                    </div>
                    <div class="kpi-tile" style="--ac:var(--green)">
                        <div class="kpi-label">{{ __('messages.net_profit') }} <span class="kpi-icon" style="color:var(--green)"><i
                                    class="fas fa-chart-line"></i></span></div>
                        <div class="kpi-val" style="color:var(--green)" x-text="'{{ __('messages.af') }} ' + fmt(data.net_profit || 0)"></div>
                        <div class="kpi-sub">
                            <span class="trend" :class="data.margin >= 0 ? 'trend-up' : 'trend-dn'">
                                <span x-text="(data.margin||0).toFixed(1) + '% {{ __('messages.margin') }}'"></span>
                            </span>
                        </div>
                    </div>
                    <div class="kpi-tile" style="--ac:var(--amber)">
                        <div class="kpi-label">{{ __('messages.transactions') }} <span class="kpi-icon" style="color:var(--amber)"><i
                                    class="fas fa-receipt"></i></span></div>
                        <div class="kpi-val" x-text="fmt(data.total_transactions || 0)"></div>
                        <div class="kpi-sub">
                            {{ __('messages.avg_af') }} <span x-text="fmt(data.avg_transaction || 0)"></span> {{ __('messages.per_sale') }}
                        </div>
                    </div>
                    <div class="kpi-tile" style="--ac:var(--violet)">
                        <div class="kpi-label">{{ __('messages.items_sold') }} <span class="kpi-icon" style="color:var(--violet)"><i
                                    class="fas fa-shopping-bag"></i></span></div>
                        <div class="kpi-val" x-text="fmt(data.items_sold || 0)"></div>
                        <div class="kpi-sub">{{ __('messages.across_orders') }} <span x-text="data.total_transactions || 0"></span> {{ __('messages.orders') }}</div>
                    </div>
                </div>

                <div class="grid-7-5">
                    {{-- Revenue chart --}}
                    <div class="card">
                        <div class="card-head">
                            <div class="card-title"><i class="fas fa-chart-area"></i> {{ __('messages.revenue_profit_over_time') }}</div>
                            <div style="display:flex;gap:6px">
                                <button type="button" class="dp-btn" :class="chartGranularity === 'hourly' ? 'active' : ''"
                                    @click="chartGranularity='hourly';renderOverviewChart()">{{ __('messages.hourly') }}</button>
                                <button type="button" class="dp-btn" :class="chartGranularity === 'daily' ? 'active' : ''"
                                    @click="chartGranularity='daily';renderOverviewChart()">{{ __('messages.daily') }}</button>
                                <button type="button" class="dp-btn" :class="chartGranularity === 'weekly' ? 'active' : ''"
                                    @click="chartGranularity='weekly';renderOverviewChart()">{{ __('messages.weekly') }}</button>
                                <button type="button" class="dp-btn" :class="chartGranularity === 'monthly' ? 'active' : ''"
                                    @click="chartGranularity='monthly';renderOverviewChart()">{{ __('messages.monthly') }}</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="chart-overview" class="chart-area chart-lg"></div>
                        </div>
                    </div>

                    {{-- Donut: payment method --}}
                    <div class="card">
                        <div class="card-head">
                            <div class="card-title"><i class="fas fa-wallet"></i> {{ __('messages.payment_methods') }}</div>
                        </div>
                        <div class="card-body">
                            <div id="chart-payment" class="chart-area chart-sm" style="min-height:180px"></div>
                            <div style="display:flex;flex-direction:column;gap:8px;margin-top:.75rem">
                                <div
                                    style="display:flex;justify-content:space-between;align-items:center;padding:7px 10px;background:var(--s2);border-radius:var(--rsm)">
                                    <span style="display:flex;align-items:center;gap:7px;font-size:12px;color:var(--ink2)">
                                        <span
                                            style="width:10px;height:10px;border-radius:50%;background:var(--blue);display:inline-block"></span>
                                        {{ __('messages.cash') }}
                                    </span>
                                    <span class="cell-mono" x-text="'{{ __('messages.af') }} ' + fmt(data.cash_sales || 0)"></span>
                                </div>
                                <div
                                    style="display:flex;justify-content:space-between;align-items:center;padding:7px 10px;background:var(--s2);border-radius:var(--rsm)">
                                    <span style="display:flex;align-items:center;gap:7px;font-size:12px;color:var(--ink2)">
                                        <span
                                            style="width:10px;height:10px;border-radius:50%;background:var(--amber);display:inline-block"></span>
                                        {{ __('messages.loan') }}
                                    </span>
                                    <span class="cell-mono" x-text="'{{ __('messages.af') }} ' + fmt(data.loan_sales || 0)"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Hourly heatmap --}}
                <div class="card">
                    <div class="card-head">
                        <div class="card-title"><i class="fas fa-clock"></i> {{ __('messages.sales_by_hour_of_day') }}</div>
                        <span style="font-size:11px;color:var(--ink3)">{{ __('messages.darker_more_sales') }}</span>
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
                                    :title="`${idx}:00 — {{ __('messages.af') }} ` + fmt(val)"></div>
                            </template>
                        </div>
                        <div style="display:flex;align-items:center;gap:8px;margin-top:10px;justify-content:flex-end">
                            <span style="font-size:10px;color:var(--ink3)">{{ __('messages.low') }}</span>
                            <div style="display:flex;gap:2px">
                                <template x-for="i in [0.1,0.3,0.5,0.7,0.9,1]" :key="i">
                                    <div style="width:16px;height:10px;border-radius:2px"
                                        :style="`background:${heatColor(i,1)}`"></div>
                                </template>
                            </div>
                            <span style="font-size:10px;color:var(--ink3)">{{ __('messages.high') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Category breakdown --}}
                <div class="grid-2">
                    <div class="card">
                        <div class="card-head">
                            <div class="card-title"><i class="fas fa-tag"></i> {{ __('messages.revenue_by_category') }}</div>
                        </div>
                        <div class="card-body">
                            <div id="chart-category" class="chart-area" style="min-height:220px"></div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-head">
                            <div class="card-title"><i class="fas fa-list-ol"></i> {{ __('messages.top_categories') }}</div>
                        </div>
                        <div class="card-body" style="padding:.75rem">
                            <template x-for="(cat, idx) in (data.top_categories || [])" :key="cat.name">
                                <div style="margin-bottom:.85rem">
                                    <div
                                        style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:5px">
                                        <span style="font-weight:600;color:var(--ink)" x-text="cat.name"></span>
                                        <span class="cell-mono" x-text="'{{ __('messages.af') }} ' + fmt(cat.revenue)"></span>
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
                        <div class="kpi-label">{{ __('messages.gross_revenue') }} <span class="kpi-icon"><i class="fas fa-coins"
                                    style="color:var(--blue)"></i></span></div>
                        <div class="kpi-val sm" x-text="'{{ __('messages.af') }} ' + fmt(data.total_revenue||0)"></div>
                        <div class="kpi-sub">{{ __('messages.before_discounts_returns') }}</div>
                    </div>
                    <div class="kpi-tile" style="--ac:var(--red)">
                        <div class="kpi-label">{{ __('messages.discounts_given') }} <span class="kpi-icon"><i class="fas fa-tag"
                                    style="color:var(--red)"></i></span></div>
                        <div class="kpi-val sm" style="color:var(--red)" x-text="'{{ __('messages.af') }} ' + fmt(data.total_discounts||0)">
                        </div>
                        <div class="kpi-sub" x-text="(data.discount_rate||0).toFixed(1) + '% of gross'"></div>
                    </div>
                    <div class="kpi-tile" style="--ac:var(--amber)">
                        <div class="kpi-label">{{ __('messages.returns') }} <span class="kpi-icon"><i class="fas fa-rotate-left"
                                    style="color:var(--amber)"></i></span></div>
                        <div class="kpi-val sm" style="color:var(--amber)" x-text="fmt(data.return_count||0) + ' {{ __('messages.sales') }}'">
                        </div>
                        <div class="kpi-sub" x-text="'{{ __('messages.af') }} ' + fmt(data.return_amount||0) + ' {{ __('messages.refunded') }}'"></div>
                    </div>
                    <div class="kpi-tile" style="--ac:var(--green)">
                        <div class="kpi-label">{{ __('messages.avg_daily_sales') }} <span class="kpi-icon"><i class="fas fa-calendar-check"
                                    style="color:var(--green)"></i></span></div>
                        <div class="kpi-val sm" x-text="'{{ __('messages.af') }} ' + fmt(data.avg_daily_sales||0)"></div>
                        <div class="kpi-sub">{{ __('messages.across_selected_period') }}</div>
                    </div>
                </div>

                {{-- Daily trend --}}
                <div class="card">
                    <div class="card-head">
                        <div class="card-title"><i class="fas fa-chart-bar"></i> {{ __('messages.daily_sales_breakdown') }}</div>
                    </div>
                    <div class="card-body">
                        <div id="chart-daily-sales" class="chart-area chart-lg"></div>
                    </div>
                </div>

                <div class="grid-2">
                    {{-- Weekday performance --}}
                    <div class="card">
                        <div class="card-head">
                            <div class="card-title"><i class="fas fa-calendar-week"></i> {{ __('messages.sales_by_day_of_week') }}</div>
                        </div>
                        <div class="card-body">
                            <div id="chart-weekday" class="chart-area"></div>
                        </div>
                    </div>
                    {{-- Top transactions --}}
                    <div class="card">
                        <div class="card-head">
                            <div class="card-title"><i class="fas fa-receipt"></i> {{ __('messages.largest_transactions') }}</div>
                        </div>
                        <div style="overflow-x:auto">
                            <table class="mini-table {{ in_array(app()->getLocale(), ['ps', 'dr']) ? 'rtl-table' : '' }}">
                                <thead>
                                    <tr>
                                        <th>{{ __('messages.#') }}</th>
                                        <th>{{ __('messages.sale_id') }}</th>
                                        <th>{{ __('messages.customer') }}</th>
                                        <th>{{ __('messages.method') }}</th>
                                        <th class="cell-right">{{ __('messages.amount') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(s,i) in (data.top_sales||[])" :key="s.id">
                                        <tr>
                                            <td><span class="rank"
                                                    :class="['rank-1', 'rank-2', 'rank-3', 'rank-n', 'rank-n'][i]"
                                                    x-text="i+1"></span></td>
                                            <td class="cell-mono" x-text="s.local_id"></td>
                                            <td style="font-size:12px" x-text="s.customer || '{{ __('messages.walk_in') }}'"></td>
                                            <td><span class="pill" :class="s.method === '{{ __('messages.cash') }}' ? 'pill-blue' : 'pill-amber'"
                                                    x-text="s.method"></span></td>
                                            <td class="cell-right cell-mono" style="color:var(--blue)"
                                                x-text="'{{ __('messages.af') }} ' + fmt(s.total)"></td>
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
                            <div class="card-title"><i class="fas fa-trophy"></i> {{ __('messages.top_selling_products') }}</div>
                        </div>
                        <div style="overflow-x:auto">
                            <table class="mini-table {{ in_array(app()->getLocale(), ['ps', 'dr']) ? 'rtl-table' : '' }}">
                                <thead>
                                    <tr>
                                        <th>{{ __('messages.#') }}</th>
                                        <th>{{ __('messages.product') }}</th>
                                        <th class="cell-center">{{ __('messages.qty_sold') }}</th>
                                        <th class="cell-right">{{ __('messages.revenue') }}</th>
                                        <th class="cell-right">{{ __('messages.profit') }}</th>
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
                                                x-text="'{{ __('messages.af') }} ' + fmt(p.revenue)"></td>
                                            <td class="cell-right cell-mono" style="color:var(--green)"
                                                x-text="'{{ __('messages.af') }} ' + fmt(p.profit)"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Bottom sellers --}}
                    <div class="card">
                        <div class="card-head">
                            <div class="card-title"><i class="fas fa-arrow-trend-down" style="color:var(--red)"></i> {{ __('messages.slow_moving_products') }}</div>
                        </div>
                        <div style="overflow-x:auto">
                            <table class="mini-table {{ in_array(app()->getLocale(), ['ps', 'dr']) ? 'rtl-table' : '' }}">
                                <thead>
                                    <tr>
                                        <th>{{ __('messages.product') }}</th>
                                        <th class="cell-center">{{ __('messages.qty_sold') }}</th>
                                        <th class="cell-right">{{ __('messages.stock_left') }}</th>
                                        <th class="cell-right">{{ __('messages.revenue') }}</th>
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
                                            <td class="cell-right cell-mono" x-text="'{{ __('messages.af') }} ' + fmt(p.revenue)"></td>
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
                        <div class="card-title"><i class="fas fa-chart-bar"></i> {{ __('messages.revenue_by_product_top10') }}</div>
                    </div>
                    <div class="card-body">
                        <div id="chart-products" class="chart-area chart-lg"></div>
                    </div>
                </div>

                {{-- Profit margin table --}}
                <div class="card">
                    <div class="card-head">
                        <div class="card-title"><i class="fas fa-percent"></i> {{ __('messages.profit_margin_by_product') }}</div>
                    </div>
                    <div style="overflow-x:auto">
                        <table class="mini-table {{ in_array(app()->getLocale(), ['ps', 'dr']) ? 'rtl-table' : '' }}">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.product_sku') }}</th>
                                    <th class="cell-right">{{ __('messages.sale_price') }}</th>
                                    <th class="cell-right">{{ __('messages.cost') }}</th>
                                    <th class="cell-right">{{ __('messages.margin_pct') }}</th>
                                    <th class="cell-right">{{ __('messages.profit_unit') }}</th>
                                    <th>{{ __('messages.margin_bar') }}</th>
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
                                        <td class="cell-right cell-mono" x-text="'{{ __('messages.af') }} ' + fmt(p.price)"></td>
                                        <td class="cell-right cell-mono" x-text="'{{ __('messages.af') }} ' + fmt(p.cost)"></td>
                                        <td class="cell-right">
                                            <span class="pill"
                                                :class="p.margin >= 30 ? 'pill-green' : p.margin >= 10 ? 'pill-amber' :
                                                    'pill-red'"
                                                x-text="p.margin.toFixed(1) + '%'"></span>
                                        </td>
                                        <td class="cell-right cell-mono" style="color:var(--green)"
                                            x-text="'{{ __('messages.af') }} ' + fmt(p.profit_unit)"></td>
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
                            {{ __('messages.out_of_stock') }}
                        </div>
                    </div>
                    <div class="inv-alert warn">
                        <i class="fas fa-triangle-exclamation"></i>
                        <div>
                            <span class="inv-alert-num" x-text="data.stock_low || 0"></span>
                            {{ __('messages.low_stock') }}
                        </div>
                    </div>
                    <div class="inv-alert warn">
                        <i class="fas fa-clock"></i>
                        <div>
                            <span class="inv-alert-num" x-text="data.expiring_30 || 0"></span>
                            {{ __('messages.expiring_within_30_days') }}
                        </div>
                    </div>
                    <div class="inv-alert ok">
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <span class="inv-alert-num" x-text="data.stock_ok || 0"></span>
                            {{ __('messages.healthy_stock') }}
                        </div>
                    </div>
                </div>

                <div class="kpi-grid kpi-3">
                    <div class="kpi-tile" style="--ac:var(--blue)">
                        <div class="kpi-label">{{ __('messages.inventory_value_cost') }}</div>
                        <div class="kpi-val sm" x-text="'{{ __('messages.af') }} ' + fmt(data.inv_value_cost||0)"></div>
                        <div class="kpi-sub">{{ __('messages.at_purchase_cost_price') }}</div>
                    </div>
                    <div class="kpi-tile" style="--ac:var(--green)">
                        <div class="kpi-label">{{ __('messages.inventory_value_retail') }}</div>
                        <div class="kpi-val sm" x-text="'{{ __('messages.af') }} ' + fmt(data.inv_value_retail||0)"></div>
                        <div class="kpi-sub">{{ __('messages.at_current_sale_price') }}</div>
                    </div>
                    <div class="kpi-tile" style="--ac:var(--amber)">
                        <div class="kpi-label">{{ __('messages.potential_profit') }}</div>
                        <div class="kpi-val sm" style="color:var(--green)"
                            x-text="'{{ __('messages.af') }} ' + fmt((data.inv_value_retail||0)-(data.inv_value_cost||0))"></div>
                        <div class="kpi-sub">{{ __('messages.if_all_stock_sold_today') }}</div>
                    </div>
                </div>

                <div class="grid-2">
                    <div class="card">
                        <div class="card-head">
                            <div class="card-title"><i class="fas fa-chart-pie"></i> {{ __('messages.stock_status_distribution') }}</div>
                        </div>
                        <div class="card-body">
                            <div id="chart-stock-status" class="chart-area"></div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-head">
                            <div class="card-title"><i class="fas fa-tag"></i> {{ __('messages.inventory_value_by_category') }}</div>
                        </div>
                        <div class="card-body">
                            <div id="chart-inv-category" class="chart-area"></div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-head">
                        <div class="card-title"><i class="fas fa-arrow-trend-down" style="color:var(--red)"></i> {{ __('messages.critical_stock_levels') }}</div>
                    </div>
                    <div style="overflow-x:auto">
                        <table class="mini-table {{ in_array(app()->getLocale(), ['ps', 'dr']) ? 'rtl-table' : '' }}">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.product_sku') }}</th>
                                    <th>{{ __('messages.category') }}</th>
                                    <th class="cell-center">{{ __('messages.current_stock') }}</th>
                                    <th class="cell-center">{{ __('messages.threshold') }}</th>
                                    <th class="cell-right">{{ __('messages.cost_value') }}</th>
                                    <th>{{ __('messages.expiry') }}</th>
                                    <th>{{ __('messages.status') }}</th>
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
                                        <td class="cell-right cell-mono" x-text="'{{ __('messages.af') }} ' + fmt(p.cost_value)"></td>
                                        <td class="cell-mono" style="font-size:11px"
                                            :style="p.expiry_days < 30 ? 'color:var(--red)' : 'color:var(--ink3)'"
                                            x-text="p.expiry || '—'"></td>
                                        <td><span class="pill" :class="p.stock === 0 ? 'pill-red' : 'pill-amber'"
                                                x-text="p.stock===0?'{{ __('messages.out_of_stock') }}':'{{ __('messages.low_stock') }}'"></span></td>
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
                                    <div class="cc-role">{{ __('messages.cashier') }}</div>
                                </div>
                            </div>
                            <div class="cc-stats">
                                <div class="cc-stat">
                                    <div class="cc-stat-label">{{ __('messages.sales_count') }}</div>
                                    <div class="cc-stat-val" x-text="fmt(c.total_sales)"></div>
                                </div>
                                <div class="cc-stat">
                                    <div class="cc-stat-label">{{ __('messages.transactions_count') }}</div>
                                    <div class="cc-stat-val" x-text="c.tx_count"></div>
                                </div>
                                <div class="cc-stat">
                                    <div class="cc-stat-label">{{ __('messages.avg_ticket') }}</div>
                                    <div class="cc-stat-val" style="font-size:12px" x-text="'{{ __('messages.af') }} ' + fmt(c.avg_ticket)">
                                    </div>
                                </div>
                                <div class="cc-stat">
                                    <div class="cc-stat-label">{{ __('messages.shifts') }}</div>
                                    <div class="cc-stat-val" x-text="c.shift_count"></div>
                                </div>
                            </div>
                            <div class="cc-perf-bar">
                                <div class="cc-perf-label">
                                    <span>{{ __('messages.performance') }}</span>
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
                        <div class="card-title"><i class="fas fa-chart-bar"></i> {{ __('messages.cashier_sales_comparison') }}</div>
                    </div>
                    <div class="card-body">
                        <div id="chart-cashiers" class="chart-area"></div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-head">
                        <div class="card-title"><i class="fas fa-clock-rotate-left"></i> {{ __('messages.shift_history') }}</div>
                    </div>
                    <div style="overflow-x:auto">
                        <table class="mini-table {{ in_array(app()->getLocale(), ['ps', 'dr']) ? 'rtl-table' : '' }}">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.cashier') }}</th>
                                    <th>{{ __('messages.opened') }}</th>
                                    <th>{{ __('messages.closed') }}</th>
                                    <th class="cell-right">{{ __('messages.starting_cash') }}</th>
                                    <th class="cell-right">{{ __('messages.expected_cash') }}</th>
                                    <th class="cell-right">{{ __('messages.actual_cash') }}</th>
                                    <th class="cell-right">{{ __('messages.discrepancy') }}</th>
                                    <th>{{ __('messages.status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="s in (data.shifts||[])" :key="s.id">
                                    <tr>
                                        <td style="font-weight:600;font-size:12px" x-text="s.cashier"></td>
                                        <td class="cell-mono" style="font-size:11px" x-text="s.opened_at"></td>
                                        <td class="cell-mono" style="font-size:11px" x-text="s.closed_at || '—'"></td>
                                        <td class="cell-right cell-mono" x-text="'{{ __('messages.af') }} ' + fmt(s.starting_cash)"></td>
                                        <td class="cell-right cell-mono"
                                            x-text="s.expected_cash ? '{{ __('messages.af') }} ' + fmt(s.expected_cash) : '—'"></td>
                                        <td class="cell-right cell-mono"
                                            x-text="s.actual_cash ? '{{ __('messages.af') }} ' + fmt(s.actual_cash) : '—'"></td>
                                        <td class="cell-right cell-mono"
                                            :style="(s.discrepancy || 0) < 0 ? 'color:var(--red)' : 'color:var(--green)'"
                                            x-text="s.discrepancy ? ((s.discrepancy > 0 ? '+' : '') + '{{ __('messages.af') }} ' + fmt(s.discrepancy)) : '—'">
                                        </td>
                                        <td><span class="pill" :class="s.is_closed ? 'pill-blue' : 'pill-green'"
                                                x-text="s.is_closed ? '{{ __('messages.closed_status') }}' : '{{ __('messages.active_status') }}'"></span></td>
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
                        <div class="kpi-label">{{ __('messages.loan_outstanding') }}</div>
                        <div class="kpi-val sm" style="color:var(--amber)"
                            x-text="'{{ __('messages.af') }} ' + fmt(data.loan_outstanding||0)"></div>
                        <div class="kpi-sub" x-text="(data.loan_active_count||0) + ' {{ __('messages.active_loans') }}'"></div>
                    </div>
                    <div class="kpi-tile" style="--ac:var(--red)">
                        <div class="kpi-label">{{ __('messages.overdue_balance') }}</div>
                        <div class="kpi-val sm" style="color:var(--red)"
                            x-text="'{{ __('messages.af') }} ' + fmt(data.loan_overdue_amount||0)"></div>
                        <div class="kpi-sub" x-text="(data.loan_overdue||0) + ' {{ __('messages.loans_overdue') }}'"></div>
                    </div>
                    <div class="kpi-tile" style="--ac:var(--green)">
                        <div class="kpi-label">{{ __('messages.collected_this_period') }}</div>
                        <div class="kpi-val sm" style="color:var(--green)" x-text="'{{ __('messages.af') }} ' + fmt(data.loan_collected||0)">
                        </div>
                        <div class="kpi-sub" x-text="(data.loan_payment_count||0) + ' {{ __('messages.payments_received') }}'"></div>
                    </div>
                    <div class="kpi-tile" style="--ac:var(--blue)">
                        <div class="kpi-label">{{ __('messages.new_loans_issued') }}</div>
                        <div class="kpi-val sm" x-text="'{{ __('messages.af') }} ' + fmt(data.loan_new_amount||0)"></div>
                        <div class="kpi-sub" x-text="(data.loan_new_count||0) + ' {{ __('messages.loans_created') }}'"></div>
                    </div>
                </div>

                <div class="grid-7-5">
                    <div class="card">
                        <div class="card-head">
                            <div class="card-title"><i class="fas fa-chart-line"></i> {{ __('messages.loan_issuance_vs_collection') }}</div>
                        </div>
                        <div class="card-body">
                            <div id="chart-loans" class="chart-area"></div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-head">
                            <div class="card-title"><i class="fas fa-layer-group"></i> {{ __('messages.loan_aging_buckets') }}</div>
                        </div>
                        <div class="card-body">
                            <div class="aging-bar-list">
                                <template x-for="bucket in (data.loan_aging||[])" :key="bucket.label">
                                    <div class="aging-item">
                                        <div class="aging-label">
                                            <span class="aging-name" x-text="bucket.label"></span>
                                            <span class="aging-val" :style="bucket.color"
                                                x-text="'{{ __('messages.af') }} ' + fmt(bucket.amount)"></span>
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
                            {{ __('messages.overdue_loans') }}</div>
                    </div>
                    <div style="overflow-x:auto">
                        <table class="mini-table {{ in_array(app()->getLocale(), ['ps', 'dr']) ? 'rtl-table' : '' }}">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.customer') }}</th>
                                    <th>{{ __('messages.phone') }}</th>
                                    <th class="cell-right">{{ __('messages.original') }}</th>
                                    <th class="cell-right">{{ __('messages.paid') }}</th>
                                    <th class="cell-right">{{ __('messages.remaining') }}</th>
                                    <th>{{ __('messages.due_date') }}</th>
                                    <th>{{ __('messages.days_overdue') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="l in (data.overdue_loans||[])" :key="l.id">
                                    <tr>
                                        <td style="font-weight:600;font-size:12px" x-text="l.customer"></td>
                                        <td class="cell-mono" style="font-size:11px" x-text="l.phone"></td>
                                        <td class="cell-right cell-mono" x-text="'{{ __('messages.af') }} ' + fmt(l.original)"></td>
                                        <td class="cell-right cell-mono" style="color:var(--green)"
                                            x-text="'{{ __('messages.af') }} ' + fmt(l.paid)"></td>
                                        <td class="cell-right cell-mono" style="color:var(--red);font-weight:700"
                                            x-text="'{{ __('messages.af') }} ' + fmt(l.remaining)"></td>
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
                        <option value="">{{ __('messages.select_a_shift') }}</option>
                        <template x-for="s in (data.shifts||[])" :key="s.id">
                            <option :value="s.id"
                                x-text="s.cashier + ' · ' + s.opened_at + (s.is_closed ? '' : ' ({{ __('messages.active_shift') }}')')"></option>
                        </template>
                    </select>
                    <button type="button" class="date-apply" @click="printZReport()">
                        <i class="fas fa-print"></i> {{ __('messages.print_z_report') }}
                    </button>
                </div>

                <div id="zreport-content">
                    <div class="zreport-card">
                        <div class="zr-title">{{ __('messages.afghan_pos_shift_report') }}</div>
                        <div class="zr-sub"
                            x-text="zreport.cashier ? ('{{ __('messages.cashier') }}: ' + zreport.cashier + ' · ' + zreport.shift_date) : '{{ __('messages.select_shift_above') }}'">
                        </div>

                        <div class="zr-grid">
                            <div class="zr-item">
                                <div class="zr-item-label">{{ __('messages.total_sales') }}</div>
                                <div class="zr-item-val" x-text="'{{ __('messages.af') }} ' + fmt(zreport.total_sales||0)"></div>
                            </div>
                            <div class="zr-item">
                                <div class="zr-item-label">{{ __('messages.transactions') }}</div>
                                <div class="zr-item-val" x-text="zreport.tx_count||0"></div>
                            </div>
                            <div class="zr-item">
                                <div class="zr-item-label">{{ __('messages.items_sold_count') }}</div>
                                <div class="zr-item-val" x-text="zreport.items_sold||0"></div>
                            </div>
                            <div class="zr-item">
                                <div class="zr-item-label">{{ __('messages.avg_ticket') }}</div>
                                <div class="zr-item-val" x-text="'{{ __('messages.af') }} ' + fmt(zreport.avg_ticket||0)"></div>
                            </div>
                        </div>

                        <hr class="zr-divider">

                        <div class="zr-row"><span class="zr-row-label">{{ __('messages.starting_cash') }}</span><span class="zr-row-val"
                                x-text="'{{ __('messages.af') }} ' + fmt(zreport.starting_cash||0)"></span></div>
                        <div class="zr-row"><span class="zr-row-label">{{ __('messages.cash_sales') }}</span><span class="zr-row-val pos"
                                x-text="'+ {{ __('messages.af') }} ' + fmt(zreport.cash_sales||0)"></span></div>
                        <div class="zr-row"><span class="zr-row-label">{{ __('messages.loan_sales') }}</span><span class="zr-row-val"
                                x-text="'{{ __('messages.af') }} ' + fmt(zreport.loan_sales||0)"></span></div>
                        <div class="zr-row"><span class="zr-row-label">{{ __('messages.discounts_given') }}</span><span class="zr-row-val neg"
                                x-text="'- {{ __('messages.af') }} ' + fmt(zreport.discounts||0)"></span></div>
                        <div class="zr-row"><span class="zr-row-label">{{ __('messages.returns_refunds') }}</span><span
                                class="zr-row-val neg" x-text="'- {{ __('messages.af') }} ' + fmt(zreport.returns||0)"></span></div>

                        <div class="zr-total-row">
                            <span class="zr-total-label">{{ __('messages.expected_cash_in_drawer') }}</span>
                            <span class="zr-total-val" x-text="'{{ __('messages.af') }} ' + fmt(zreport.expected_cash||0)"></span>
                        </div>

                        <template x-if="zreport.actual_cash">
                            <div>
                                <hr class="zr-divider">
                                <div class="zr-row"><span class="zr-row-label">{{ __('messages.actual_cash_counted') }}</span><span
                                        class="zr-row-val" x-text="'{{ __('messages.af') }} ' + fmt(zreport.actual_cash||0)"></span></div>
                                <div class="zr-row">
                                    <span class="zr-row-label">{{ __('messages.discrepancy') }}</span>
                                    <span class="zr-row-val" :class="(zreport.discrepancy || 0) >= 0 ? 'pos' : 'neg'"
                                        x-text="((zreport.discrepancy||0) >= 0 ? '+' : '') + '{{ __('messages.af') }} ' + fmt(zreport.discrepancy||0)"></span>
                                </div>
                                <div x-show="zreport.discrepancy_note"
                                    style="margin-top:.5rem;padding:8px 10px;background:rgba(255,255,255,.06);border-radius:var(--rsm);font-size:12px;color:rgba(255,255,255,.5)"
                                    x-text="'{{ __('messages.note') }}: ' + zreport.discrepancy_note"></div>
                            </div>
                        </template>

                        <div class="zr-footer">
                            <button type="button" class="btn btn-ghost" @click="printZReport()">
                                <i class="fas fa-print"></i> {{ __('messages.print_btn') }}
                            </button>
                            <button type="button" class="btn btn-ghost" @click="exportZReportCsv()">
                                <i class="fas fa-file-csv"></i> {{ __('messages.export_csv_btn') }}
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
                cashierId: '',
                data: {},
                zreport: {},
                selectedShift: '',
                loading: false,
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

                    if (this.tab === t) return;

                    this.tab = t;

                    requestAnimationFrame(() => {
                        this.renderChartsForTab(t);
                    });
                },

                /* ── Load all data ── */
                async loadAll() {

                    if (this.loading) return;

                    this.loading = true;

                    try {

                        const params = new URLSearchParams({
                            from: this.dateFrom,
                            to: this.dateTo,
                            granularity: this.chartGranularity,
                            cashier_id: this.cashierId
                        });

                        const r = await fetch(`${this.urls.report}?${params}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        if (!r.ok) {
                            throw new Error('{{ __('messages.error_load_reports') }}');
                        }

                        const json = await r.json();

                        this.data = json;

                        requestAnimationFrame(() => {
                            this.renderChartsForTab(this.tab);
                        });

                    } catch (e) {

                        console.error(e);

                    } finally {

                        this.loading = false;
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

                    const map = {

                        overview: () => {
                            this.renderOverviewChart();
                            this.renderPaymentChart();
                            this.renderCategoryChart();
                        },

                        sales: () => {
                            this.renderDailySalesChart();
                            this.renderWeekdayChart();
                        },

                        products: () => {
                            this.renderProductsChart();
                        },

                        inventory: () => {
                            this.renderStockStatusChart();
                            this.renderInvCategoryChart();
                        },

                        cashiers: () => {
                            this.renderCashiersChart();
                        },

                        loans: () => {
                            this.renderLoansChart();
                        }
                    };

                    if (map[t]) {
                        map[t]();
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
                                name: '{{ __('messages.revenue') }}',
                                data: revenue
                            },
                            {
                                name: '{{ __('messages.profit') }}',
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
                        labels: ['{{ __('messages.cash') }}', '{{ __('messages.loan') }}'],
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
                            name: '{{ __('messages.revenue') }}',
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
                                name: '{{ __('messages.cash') }}',
                                data: this.data.daily_cash || []
                            },
                            {
                                name: '{{ __('messages.loan') }}',
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
                    const days = [
                        '{{ __('messages.day_sun') }}',
                        '{{ __('messages.day_mon') }}',
                        '{{ __('messages.day_tue') }}',
                        '{{ __('messages.day_wed') }}',
                        '{{ __('messages.day_thu') }}',
                        '{{ __('messages.day_fri') }}',
                        '{{ __('messages.day_sat') }}'
                    ];
                    this.charts['weekday'] = new ApexCharts(el, {
                        ...this.apexDefaults(),
                        chart: {
                            ...this.apexDefaults().chart,
                            type: 'radar',
                            height: 260
                        },
                        series: [{
                            name: '{{ __('messages.avg_sales') }}',
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
                                name: '{{ __('messages.revenue') }}',
                                data: prods.map(p => p.revenue)
                            },
                            {
                                name: '{{ __('messages.profit') }}',
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
                        labels: [
                            '{{ __('messages.healthy') }}',
                            '{{ __('messages.low_stock') }}',
                            '{{ __('messages.out_of_stock') }}'
                        ],
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
                            name: '{{ __('messages.value_cost') }}',
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
                            name: '{{ __('messages.total_sales') }}',
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
                                name: '{{ __('messages.issued') }}',
                                data: this.data.loan_issued_series || []
                            },
                            {
                                name: '{{ __('messages.collected') }}',
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
                    window.printSection('.rp');
                },
                printZReport() {
                    window.printSection('#zreport-content');
                },
                exportCsv() {
                    window.location.href = this.urls.export+'?from=' + this.dateFrom + '&to=' + this
                        .dateTo + '&type=csv&cashier_id=' + encodeURIComponent(this.cashierId || '');
                },
                exportPdf() {
                    window.location.href = this.urls.export+'?from=' + this.dateFrom + '&to=' + this
                        .dateTo + '&type=pdf&cashier_id=' + encodeURIComponent(this.cashierId || '');
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
