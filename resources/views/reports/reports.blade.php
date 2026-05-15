@extends('layouts.app')

@push('styles')
    @vite(['resources/css/pages/reports.css'])
@endpush

@section('content')
<div class="rp" x-data="reportsPage()" x-init="init()">

{{-- ════ TOPBAR ════ --}}
<div class="rp-top">
    <div class="rp-title">Afghan <em>POS</em> — Reports</div>
    <div class="top-right">
        <button class="btn btn-ghost" @click="printReport()">
            <i class="fas fa-print"></i> Print
        </button>
        <button class="btn btn-ghost" @click="exportPdf()">
            <i class="fas fa-file-pdf"></i> Export PDF
        </button>
        <button class="btn btn-primary" @click="exportCsv()">
            <i class="fas fa-file-csv"></i> Export CSV
        </button>
    </div>
</div>

{{-- ════ DATE TOOLBAR ════ --}}
<div class="date-toolbar">
    <div class="date-preset-group">
        <button type="button" class="dp-btn" :class="preset==='today'?'active':''" @click="setPreset('today')">Today</button>
        <button type="button" class="dp-btn" :class="preset==='yesterday'?'active':''" @click="setPreset('yesterday')">Yesterday</button>
        <button type="button" class="dp-btn" :class="preset==='week'?'active':''" @click="setPreset('week')">This Week</button>
        <button type="button" class="dp-btn" :class="preset==='month'?'active':''" @click="setPreset('month')">This Month</button>
        <button type="button" class="dp-btn" :class="preset==='quarter'?'active':''" @click="setPreset('quarter')">Quarter</button>
        <button type="button" class="dp-btn" :class="preset==='year'?'active':''" @click="setPreset('year')">This Year</button>
    </div>
    <span class="date-sep">|</span>
    <input type="date" class="date-input" x-model="dateFrom">
    <span class="date-sep">→</span>
    <input type="date" class="date-input" x-model="dateTo">
    <button type="button" class="date-apply" @click="loadAll()">
        <i class="fas fa-rotate"></i> Apply
    </button>
    <span class="date-label" x-text="dateRangeLabel"></span>
</div>

{{-- ════ TABS ════ --}}
<div class="rp-tabs">
    <button type="button" class="rp-tab" :class="tab==='overview'?'active':''" @click="switchTab('overview')">
        <i class="fas fa-chart-pie"></i> Overview
    </button>
    <button type="button" class="rp-tab" :class="tab==='sales'?'active':''" @click="switchTab('sales')">
        <i class="fas fa-chart-line"></i> Sales
    </button>
    <button type="button" class="rp-tab" :class="tab==='products'?'active':''" @click="switchTab('products')">
        <i class="fas fa-boxes-stacked"></i> Products
    </button>
    <button type="button" class="rp-tab" :class="tab==='inventory'?'active':''" @click="switchTab('inventory')">
        <i class="fas fa-warehouse"></i> Inventory
    </button>
    <button type="button" class="rp-tab" :class="tab==='cashiers'?'active':''" @click="switchTab('cashiers')">
        <i class="fas fa-users"></i> Cashiers
    </button>
    <button type="button" class="rp-tab" :class="tab==='loans'?'active':''" @click="switchTab('loans')">
        <i class="fas fa-file-invoice-dollar"></i> Loans
        <span class="tab-badge" x-show="data.loan_overdue > 0" x-text="data.loan_overdue"></span>
    </button>
    <button type="button" class="rp-tab" :class="tab==='zreport'?'active':''" @click="switchTab('zreport')">
        <i class="fas fa-file-alt"></i> Z-Report
    </button>
</div>

{{-- ══════════════════════════════════════════════
     TAB: OVERVIEW
══════════════════════════════════════════════ --}}
<div class="rp-panel" :class="tab==='overview'?'active':''">
    <div class="section-gap">

        {{-- KPI strip --}}
        <div class="kpi-grid kpi-4">
            <div class="kpi-tile" style="--ac:var(--blue)">
                <div class="kpi-label">Total Revenue <span class="kpi-icon" style="color:var(--blue)"><i class="fas fa-coins"></i></span></div>
                <div class="kpi-val" x-text="'Af ' + fmt(data.total_revenue || 0)"></div>
                <div class="kpi-sub">
                    <span class="trend" :class="data.revenue_trend >= 0 ? 'trend-up' : 'trend-dn'">
                        <i :class="data.revenue_trend >= 0 ? 'fas fa-arrow-up' : 'fas fa-arrow-down'"></i>
                        <span x-text="Math.abs(data.revenue_trend||0).toFixed(1) + '%'"></span>
                    </span>
                    vs previous period
                </div>
            </div>
            <div class="kpi-tile" style="--ac:var(--green)">
                <div class="kpi-label">Net Profit <span class="kpi-icon" style="color:var(--green)"><i class="fas fa-chart-line"></i></span></div>
                <div class="kpi-val" style="color:var(--green)" x-text="'Af ' + fmt(data.net_profit || 0)"></div>
                <div class="kpi-sub">
                    <span class="trend" :class="data.margin >= 0 ? 'trend-up' : 'trend-dn'">
                        <span x-text="(data.margin||0).toFixed(1) + '% margin'"></span>
                    </span>
                </div>
            </div>
            <div class="kpi-tile" style="--ac:var(--amber)">
                <div class="kpi-label">Transactions <span class="kpi-icon" style="color:var(--amber)"><i class="fas fa-receipt"></i></span></div>
                <div class="kpi-val" x-text="fmt(data.total_transactions || 0)"></div>
                <div class="kpi-sub">
                    Avg Af <span x-text="fmt(data.avg_transaction || 0)"></span> per sale
                </div>
            </div>
            <div class="kpi-tile" style="--ac:var(--violet)">
                <div class="kpi-label">Items Sold <span class="kpi-icon" style="color:var(--violet)"><i class="fas fa-shopping-bag"></i></span></div>
                <div class="kpi-val" x-text="fmt(data.items_sold || 0)"></div>
                <div class="kpi-sub">across <span x-text="data.total_transactions || 0"></span> orders</div>
            </div>
        </div>

        <div class="grid-7-5">
            {{-- Revenue chart --}}
            <div class="card">
                <div class="card-head">
                    <div class="card-title"><i class="fas fa-chart-area"></i> Revenue & Profit Over Time</div>
                    <div style="display:flex;gap:6px">
                        <button type="button" class="dp-btn" :class="chartGranularity==='hourly'?'active':''" @click="chartGranularity='hourly';renderOverviewChart()">Hourly</button>
                        <button type="button" class="dp-btn" :class="chartGranularity==='daily'?'active':''" @click="chartGranularity='daily';renderOverviewChart()">Daily</button>
                        <button type="button" class="dp-btn" :class="chartGranularity==='weekly'?'active':''" @click="chartGranularity='weekly';renderOverviewChart()">Weekly</button>
                        <button type="button" class="dp-btn" :class="chartGranularity==='monthly'?'active':''" @click="chartGranularity='monthly';renderOverviewChart()">Monthly</button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="chart-overview" class="chart-area chart-lg"></div>
                </div>
            </div>

            {{-- Donut: payment method --}}
            <div class="card">
                <div class="card-head">
                    <div class="card-title"><i class="fas fa-wallet"></i> Payment Methods</div>
                </div>
                <div class="card-body">
                    <div id="chart-payment" class="chart-area chart-sm" style="min-height:180px"></div>
                    <div style="display:flex;flex-direction:column;gap:8px;margin-top:.75rem">
                        <div style="display:flex;justify-content:space-between;align-items:center;padding:7px 10px;background:var(--s2);border-radius:var(--rsm)">
                            <span style="display:flex;align-items:center;gap:7px;font-size:12px;color:var(--ink2)">
                                <span style="width:10px;height:10px;border-radius:50%;background:var(--blue);display:inline-block"></span> Cash
                            </span>
                            <span class="cell-mono" x-text="'Af ' + fmt(data.cash_sales || 0)"></span>
                        </div>
                        <div style="display:flex;justify-content:space-between;align-items:center;padding:7px 10px;background:var(--s2);border-radius:var(--rsm)">
                            <span style="display:flex;align-items:center;gap:7px;font-size:12px;color:var(--ink2)">
                                <span style="width:10px;height:10px;border-radius:50%;background:var(--amber);display:inline-block"></span> Loan
                            </span>
                            <span class="cell-mono" x-text="'Af ' + fmt(data.loan_sales || 0)"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Hourly heatmap --}}
        <div class="card">
            <div class="card-head">
                <div class="card-title"><i class="fas fa-clock"></i> Sales by Hour of Day</div>
                <span style="font-size:11px;color:var(--ink3)">Darker = more sales</span>
            </div>
            <div class="card-body">
                <div class="heatmap-labels">
                    <template x-for="h in 24" :key="h">
                        <div class="hm-label" x-text="(h-1).toString().padStart(2,'0')"></div>
                    </template>
                </div>
                <div class="heatmap-grid" id="heatmap-cells">
                    <template x-for="(val, idx) in (data.hourly_heatmap || Array(24).fill(0))" :key="idx">
                        <div class="heatmap-cell"
                             :style="`background:${heatColor(val, data.hourly_max||1)}`"
                             :title="`${idx}:00 — Af ${fmt(val)}`"></div>
                    </template>
                </div>
                <div style="display:flex;align-items:center;gap:8px;margin-top:10px;justify-content:flex-end">
                    <span style="font-size:10px;color:var(--ink3)">Low</span>
                    <div style="display:flex;gap:2px">
                        <template x-for="i in [0.1,0.3,0.5,0.7,0.9,1]" :key="i">
                            <div style="width:16px;height:10px;border-radius:2px" :style="`background:${heatColor(i,1)}`"></div>
                        </template>
                    </div>
                    <span style="font-size:10px;color:var(--ink3)">High</span>
                </div>
            </div>
        </div>

        {{-- Category breakdown --}}
        <div class="grid-2">
            <div class="card">
                <div class="card-head">
                    <div class="card-title"><i class="fas fa-tag"></i> Revenue by Category</div>
                </div>
                <div class="card-body">
                    <div id="chart-category" class="chart-area" style="min-height:220px"></div>
                </div>
            </div>
            <div class="card">
                <div class="card-head">
                    <div class="card-title"><i class="fas fa-list-ol"></i> Top Categories</div>
                </div>
                <div class="card-body" style="padding:.75rem">
                    <template x-for="(cat, idx) in (data.top_categories || [])" :key="cat.name">
                        <div style="margin-bottom:.85rem">
                            <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:5px">
                                <span style="font-weight:600;color:var(--ink)" x-text="cat.name"></span>
                                <span class="cell-mono" x-text="'Af ' + fmt(cat.revenue)"></span>
                            </div>
                            <div class="prog-bar-wrap">
                                <div class="prog-bar">
                                    <div class="prog-fill" :style="`width:${cat.pct}%;background:${['var(--blue)','var(--violet)','var(--teal)','var(--amber)','var(--green)'][idx%5]}`"></div>
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
<div class="rp-panel" :class="tab==='sales'?'active':''">
    <div class="section-gap">

        <div class="kpi-grid kpi-4">
            <div class="kpi-tile" style="--ac:var(--blue)">
                <div class="kpi-label">Gross Revenue <span class="kpi-icon"><i class="fas fa-coins" style="color:var(--blue)"></i></span></div>
                <div class="kpi-val sm" x-text="'Af ' + fmt(data.total_revenue||0)"></div>
                <div class="kpi-sub">before discounts &amp; returns</div>
            </div>
            <div class="kpi-tile" style="--ac:var(--red)">
                <div class="kpi-label">Discounts Given <span class="kpi-icon"><i class="fas fa-tag" style="color:var(--red)"></i></span></div>
                <div class="kpi-val sm" style="color:var(--red)" x-text="'Af ' + fmt(data.total_discounts||0)"></div>
                <div class="kpi-sub" x-text="(data.discount_rate||0).toFixed(1) + '% of gross'"></div>
            </div>
            <div class="kpi-tile" style="--ac:var(--amber)">
                <div class="kpi-label">Returns <span class="kpi-icon"><i class="fas fa-rotate-left" style="color:var(--amber)"></i></span></div>
                <div class="kpi-val sm" style="color:var(--amber)" x-text="fmt(data.return_count||0) + ' sales'"></div>
                <div class="kpi-sub" x-text="'Af ' + fmt(data.return_amount||0) + ' refunded'"></div>
            </div>
            <div class="kpi-tile" style="--ac:var(--green)">
                <div class="kpi-label">Avg Daily Sales <span class="kpi-icon"><i class="fas fa-calendar-check" style="color:var(--green)"></i></span></div>
                <div class="kpi-val sm" x-text="'Af ' + fmt(data.avg_daily_sales||0)"></div>
                <div class="kpi-sub">across the selected period</div>
            </div>
        </div>

        {{-- Daily trend --}}
        <div class="card">
            <div class="card-head">
                <div class="card-title"><i class="fas fa-chart-bar"></i> Daily Sales Breakdown</div>
            </div>
            <div class="card-body">
                <div id="chart-daily-sales" class="chart-area chart-lg"></div>
            </div>
        </div>

        <div class="grid-2">
            {{-- Weekday performance --}}
            <div class="card">
                <div class="card-head">
                    <div class="card-title"><i class="fas fa-calendar-week"></i> Sales by Day of Week</div>
                </div>
                <div class="card-body">
                    <div id="chart-weekday" class="chart-area"></div>
                </div>
            </div>
            {{-- Top transactions --}}
            <div class="card">
                <div class="card-head">
                    <div class="card-title"><i class="fas fa-receipt"></i> Largest Transactions</div>
                </div>
                <div style="overflow-x:auto">
                    <table class="mini-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Sale ID</th>
                                <th>Customer</th>
                                <th>Method</th>
                                <th class="cell-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(s,i) in (data.top_sales||[])" :key="s.id">
                                <tr>
                                    <td><span class="rank" :class="['rank-1','rank-2','rank-3','rank-n','rank-n'][i]" x-text="i+1"></span></td>
                                    <td class="cell-mono" x-text="s.local_id"></td>
                                    <td style="font-size:12px" x-text="s.customer || 'Walk-in'"></td>
                                    <td><span class="pill" :class="s.method==='cash'?'pill-blue':'pill-amber'" x-text="s.method"></span></td>
                                    <td class="cell-right cell-mono" style="color:var(--blue)" x-text="'Af ' + fmt(s.total)"></td>
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
<div class="rp-panel" :class="tab==='products'?'active':''">
    <div class="section-gap">
        <div class="grid-2">
            {{-- Top sellers --}}
            <div class="card">
                <div class="card-head">
                    <div class="card-title"><i class="fas fa-trophy"></i> Top Selling Products</div>
                </div>
                <div style="overflow-x:auto">
                    <table class="mini-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th class="cell-center">Qty Sold</th>
                                <th class="cell-right">Revenue</th>
                                <th class="cell-right">Profit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(p,i) in (data.top_products||[])" :key="p.sku">
                                <tr>
                                    <td><span class="rank" :class="['rank-1','rank-2','rank-3','rank-n','rank-n'][i]||'rank-n'" x-text="i+1"></span></td>
                                    <td>
                                        <div style="font-weight:600;font-size:12px" x-text="p.name"></div>
                                        <div class="cell-mono" style="font-size:10px" x-text="p.sku"></div>
                                    </td>
                                    <td class="cell-center cell-mono" x-text="fmt(p.qty_sold)"></td>
                                    <td class="cell-right cell-mono" style="color:var(--blue)" x-text="'Af ' + fmt(p.revenue)"></td>
                                    <td class="cell-right cell-mono" style="color:var(--green)" x-text="'Af ' + fmt(p.profit)"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Bottom sellers --}}
            <div class="card">
                <div class="card-head">
                    <div class="card-title"><i class="fas fa-arrow-trend-down" style="color:var(--red)"></i> Slow Moving Products</div>
                </div>
                <div style="overflow-x:auto">
                    <table class="mini-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th class="cell-center">Qty Sold</th>
                                <th class="cell-right">Stock Left</th>
                                <th class="cell-right">Revenue</th>
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
                                        <span class="pill" :class="p.stock > 10 ? 'pill-blue' : 'pill-red'" x-text="p.stock"></span>
                                    </td>
                                    <td class="cell-right cell-mono" x-text="'Af ' + fmt(p.revenue)"></td>
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
                <div class="card-title"><i class="fas fa-chart-bar"></i> Revenue by Product (Top 10)</div>
            </div>
            <div class="card-body">
                <div id="chart-products" class="chart-area chart-lg"></div>
            </div>
        </div>

        {{-- Profit margin table --}}
        <div class="card">
            <div class="card-head">
                <div class="card-title"><i class="fas fa-percent"></i> Profit Margin by Product</div>
            </div>
            <div style="overflow-x:auto">
                <table class="mini-table">
                    <thead>
                        <tr>
                            <th>Product / SKU</th>
                            <th class="cell-right">Sale Price</th>
                            <th class="cell-right">Cost</th>
                            <th class="cell-right">Margin %</th>
                            <th class="cell-right">Profit / Unit</th>
                            <th>Margin Bar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="p in (data.margin_table||[])" :key="p.sku">
                            <tr>
                                <td>
                                    <div style="font-weight:600;font-size:12px" x-text="p.name"></div>
                                    <div class="cell-mono" style="font-size:10px;color:var(--ink3)" x-text="p.sku"></div>
                                </td>
                                <td class="cell-right cell-mono" x-text="'Af ' + fmt(p.price)"></td>
                                <td class="cell-right cell-mono" x-text="'Af ' + fmt(p.cost)"></td>
                                <td class="cell-right">
                                    <span class="pill" :class="p.margin >= 30 ? 'pill-green' : p.margin >= 10 ? 'pill-amber' : 'pill-red'"
                                          x-text="p.margin.toFixed(1) + '%'"></span>
                                </td>
                                <td class="cell-right cell-mono" style="color:var(--green)" x-text="'Af ' + fmt(p.profit_unit)"></td>
                                <td style="width:120px">
                                    <div class="prog-bar">
                                        <div class="prog-fill" :style="`width:${Math.min(p.margin,100)}%;background:${p.margin>=30?'var(--green)':p.margin>=10?'var(--amber)':'var(--red)'}`"></div>
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
<div class="rp-panel" :class="tab==='inventory'?'active':''">
    <div class="section-gap">

        <div class="inv-alert-strip">
            <div class="inv-alert danger">
                <i class="fas fa-exclamation-circle"></i>
                <div>
                    <span class="inv-alert-num" x-text="data.stock_zero || 0"></span>
                    Out of Stock
                </div>
            </div>
            <div class="inv-alert warn">
                <i class="fas fa-triangle-exclamation"></i>
                <div>
                    <span class="inv-alert-num" x-text="data.stock_low || 0"></span>
                    Low Stock
                </div>
            </div>
            <div class="inv-alert warn">
                <i class="fas fa-clock"></i>
                <div>
                    <span class="inv-alert-num" x-text="data.expiring_30 || 0"></span>
                    Expiring ≤ 30 days
                </div>
            </div>
            <div class="inv-alert ok">
                <i class="fas fa-check-circle"></i>
                <div>
                    <span class="inv-alert-num" x-text="data.stock_ok || 0"></span>
                    Healthy Stock
                </div>
            </div>
        </div>

        <div class="kpi-grid kpi-3">
            <div class="kpi-tile" style="--ac:var(--blue)">
                <div class="kpi-label">Inventory Value (Cost)</div>
                <div class="kpi-val sm" x-text="'Af ' + fmt(data.inv_value_cost||0)"></div>
                <div class="kpi-sub">at purchase cost price</div>
            </div>
            <div class="kpi-tile" style="--ac:var(--green)">
                <div class="kpi-label">Inventory Value (Retail)</div>
                <div class="kpi-val sm" x-text="'Af ' + fmt(data.inv_value_retail||0)"></div>
                <div class="kpi-sub">at current sale price</div>
            </div>
            <div class="kpi-tile" style="--ac:var(--amber)">
                <div class="kpi-label">Potential Profit</div>
                <div class="kpi-val sm" style="color:var(--green)" x-text="'Af ' + fmt((data.inv_value_retail||0)-(data.inv_value_cost||0))"></div>
                <div class="kpi-sub">if all stock sold today</div>
            </div>
        </div>

        <div class="grid-2">
            <div class="card">
                <div class="card-head"><div class="card-title"><i class="fas fa-chart-pie"></i> Stock Status Distribution</div></div>
                <div class="card-body"><div id="chart-stock-status" class="chart-area"></div></div>
            </div>
            <div class="card">
                <div class="card-head"><div class="card-title"><i class="fas fa-tag"></i> Inventory Value by Category</div></div>
                <div class="card-body"><div id="chart-inv-category" class="chart-area"></div></div>
            </div>
        </div>

        <div class="card">
            <div class="card-head"><div class="card-title"><i class="fas fa-arrow-trend-down" style="color:var(--red)"></i> Critical Stock Levels</div></div>
            <div style="overflow-x:auto">
                <table class="mini-table">
                    <thead>
                        <tr>
                            <th>Product / SKU</th>
                            <th>Category</th>
                            <th class="cell-center">Current Stock</th>
                            <th class="cell-center">Threshold</th>
                            <th class="cell-right">Cost Value</th>
                            <th>Expiry</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="p in (data.critical_stock||[])" :key="p.sku">
                            <tr>
                                <td>
                                    <div style="font-weight:600;font-size:12px" x-text="p.name"></div>
                                    <div class="cell-mono" style="font-size:10px;color:var(--ink3)" x-text="p.sku"></div>
                                </td>
                                <td><span class="pill pill-blue" x-text="p.category"></span></td>
                                <td class="cell-center"><span class="cell-mono" style="font-weight:700" :style="p.stock===0?'color:var(--red)':'color:var(--amber)'" x-text="p.stock"></span></td>
                                <td class="cell-center cell-mono" x-text="p.threshold"></td>
                                <td class="cell-right cell-mono" x-text="'Af ' + fmt(p.cost_value)"></td>
                                <td class="cell-mono" style="font-size:11px" :style="p.expiry_days < 30 ? 'color:var(--red)' : 'color:var(--ink3)'" x-text="p.expiry || '—'"></td>
                                <td><span class="pill" :class="p.stock===0?'pill-red':'pill-amber'" x-text="p.stock===0?'Out of Stock':'Low Stock'"></span></td>
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
<div class="rp-panel" :class="tab==='cashiers'?'active':''">
    <div class="section-gap">

        <div class="cashier-grid">
            <template x-for="(c,i) in (data.cashiers||[])" :key="c.id">
                <div class="cashier-card">
                    <div class="cc-top">
                        <div class="cc-av" :style="`background:${['#2f6fe8','#7c3aed','#0891b2','#15803d','#d97706'][i%5]}`"
                             x-text="initials(c.name)"></div>
                    
                        <div>
                            <div class="cc-name" x-text="c.name"></div>
                            <div class="cc-role">Cashier</div>
                        </div>
                    </div>
                    <div class="cc-stats">
                        <div class="cc-stat">
                            <div class="cc-stat-label">Sales</div>
                            <div class="cc-stat-val" x-text="fmt(c.total_sales)"></div>
                        </div>
                        <div class="cc-stat">
                            <div class="cc-stat-label">Transactions</div>
                            <div class="cc-stat-val" x-text="c.tx_count"></div>
                        </div>
                        <div class="cc-stat">
                            <div class="cc-stat-label">Avg Ticket</div>
                            <div class="cc-stat-val" style="font-size:12px" x-text="'Af ' + fmt(c.avg_ticket)"></div>
                        </div>
                        <div class="cc-stat">
                            <div class="cc-stat-label">Shifts</div>
                            <div class="cc-stat-val" x-text="c.shift_count"></div>
                        </div>
                    </div>
                    <div class="cc-perf-bar">
                        <div class="cc-perf-label">
                            <span>Performance</span>
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
            <div class="card-head"><div class="card-title"><i class="fas fa-chart-bar"></i> Cashier Sales Comparison</div></div>
            <div class="card-body"><div id="chart-cashiers" class="chart-area"></div></div>
        </div>

        <div class="card">
            <div class="card-head"><div class="card-title"><i class="fas fa-clock-rotate-left"></i> Shift History</div></div>
            <div style="overflow-x:auto">
                <table class="mini-table">
                    <thead>
                        <tr>
                            <th>Cashier</th>
                            <th>Opened</th>
                            <th>Closed</th>
                            <th class="cell-right">Starting Cash</th>
                            <th class="cell-right">Expected Cash</th>
                            <th class="cell-right">Actual Cash</th>
                            <th class="cell-right">Discrepancy</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="s in (data.shifts||[])" :key="s.id">
                            <tr>
                                <td style="font-weight:600;font-size:12px" x-text="s.cashier"></td>
                                <td class="cell-mono" style="font-size:11px" x-text="s.opened_at"></td>
                                <td class="cell-mono" style="font-size:11px" x-text="s.closed_at || '—'"></td>
                                <td class="cell-right cell-mono" x-text="'Af ' + fmt(s.starting_cash)"></td>
                                <td class="cell-right cell-mono" x-text="s.expected_cash ? 'Af ' + fmt(s.expected_cash) : '—'"></td>
                                <td class="cell-right cell-mono" x-text="s.actual_cash ? 'Af ' + fmt(s.actual_cash) : '—'"></td>
                                <td class="cell-right cell-mono" :style="(s.discrepancy||0) < 0 ? 'color:var(--red)' : 'color:var(--green)'"
                                    x-text="s.discrepancy ? ((s.discrepancy > 0 ? '+' : '') + 'Af ' + fmt(s.discrepancy)) : '—'"></td>
                                <td><span class="pill" :class="s.is_closed ? 'pill-blue' : 'pill-green'" x-text="s.is_closed ? 'Closed' : 'Active'"></span></td>
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
<div class="rp-panel" :class="tab==='loans'?'active':''">
    <div class="section-gap">

        <div class="kpi-grid kpi-4">
            <div class="kpi-tile" style="--ac:var(--amber)">
                <div class="kpi-label">Total Outstanding</div>
                <div class="kpi-val sm" style="color:var(--amber)" x-text="'Af ' + fmt(data.loan_outstanding||0)"></div>
                <div class="kpi-sub" x-text="(data.loan_active_count||0) + ' active loans'"></div>
            </div>
            <div class="kpi-tile" style="--ac:var(--red)">
                <div class="kpi-label">Overdue Balance</div>
                <div class="kpi-val sm" style="color:var(--red)" x-text="'Af ' + fmt(data.loan_overdue_amount||0)"></div>
                <div class="kpi-sub" x-text="(data.loan_overdue||0) + ' loans overdue'"></div>
            </div>
            <div class="kpi-tile" style="--ac:var(--green)">
                <div class="kpi-label">Collected This Period</div>
                <div class="kpi-val sm" style="color:var(--green)" x-text="'Af ' + fmt(data.loan_collected||0)"></div>
                <div class="kpi-sub" x-text="(data.loan_payment_count||0) + ' payments received'"></div>
            </div>
            <div class="kpi-tile" style="--ac:var(--blue)">
                <div class="kpi-label">New Loans Issued</div>
                <div class="kpi-val sm" x-text="'Af ' + fmt(data.loan_new_amount||0)"></div>
                <div class="kpi-sub" x-text="(data.loan_new_count||0) + ' loans created'"></div>
            </div>
        </div>

        <div class="grid-7-5">
            <div class="card">
                <div class="card-head"><div class="card-title"><i class="fas fa-chart-line"></i> Loan Issuance vs Collection</div></div>
                <div class="card-body"><div id="chart-loans" class="chart-area"></div></div>
            </div>
            <div class="card">
                <div class="card-head"><div class="card-title"><i class="fas fa-layer-group"></i> Loan Aging Buckets</div></div>
                <div class="card-body">
                    <div class="aging-bar-list">
                        <template x-for="bucket in (data.loan_aging||[])" :key="bucket.label">
                            <div class="aging-item">
                                <div class="aging-label">
                                    <span class="aging-name" x-text="bucket.label"></span>
                                    <span class="aging-val" :style="bucket.color" x-text="'Af ' + fmt(bucket.amount)"></span>
                                </div>
                                <div class="prog-bar-wrap">
                                    <div class="prog-bar">
                                        <div class="prog-fill" :style="`width:${bucket.pct}%;background:${bucket.fill}`"></div>
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
            <div class="card-head"><div class="card-title"><i class="fas fa-triangle-exclamation" style="color:var(--red)"></i> Overdue Loans</div></div>
            <div style="overflow-x:auto">
                <table class="mini-table">
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Phone</th>
                            <th class="cell-right">Original</th>
                            <th class="cell-right">Paid</th>
                            <th class="cell-right">Remaining</th>
                            <th>Due Date</th>
                            <th>Days Overdue</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="l in (data.overdue_loans||[])" :key="l.id">
                            <tr>
                                <td style="font-weight:600;font-size:12px" x-text="l.customer"></td>
                                <td class="cell-mono" style="font-size:11px" x-text="l.phone"></td>
                                <td class="cell-right cell-mono" x-text="'Af ' + fmt(l.original)"></td>
                                <td class="cell-right cell-mono" style="color:var(--green)" x-text="'Af ' + fmt(l.paid)"></td>
                                <td class="cell-right cell-mono" style="color:var(--red);font-weight:700" x-text="'Af ' + fmt(l.remaining)"></td>
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
<div class="rp-panel" :class="tab==='zreport'?'active':''">
    <div class="section-gap">

        {{-- Shift selector --}}
        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
            <select class="date-input" style="min-width:280px" x-model="selectedShift" @change="loadZReport()">
                <option value="">— Select a Shift —</option>
                <template x-for="s in (data.shifts||[])" :key="s.id">
                    <option :value="s.id" x-text="s.cashier + ' · ' + s.opened_at + (s.is_closed ? '' : ' (Active)')"></option>
                </template>
            </select>
            <button type="button" class="date-apply" @click="printZReport()">
                <i class="fas fa-print"></i> Print Z-Report
            </button>
        </div>

        <div id="zreport-content">
            <div class="zreport-card">
                <div class="zr-title">Afghan POS — Shift Report</div>
                <div class="zr-sub" x-text="zreport.cashier ? ('Cashier: ' + zreport.cashier + ' · ' + zreport.shift_date) : 'Select a shift above to generate report'"></div>

                <div class="zr-grid">
                    <div class="zr-item">
                        <div class="zr-item-label">Total Sales</div>
                        <div class="zr-item-val" x-text="'Af ' + fmt(zreport.total_sales||0)"></div>
                    </div>
                    <div class="zr-item">
                        <div class="zr-item-label">Transactions</div>
                        <div class="zr-item-val" x-text="zreport.tx_count||0"></div>
                    </div>
                    <div class="zr-item">
                        <div class="zr-item-label">Items Sold</div>
                        <div class="zr-item-val" x-text="zreport.items_sold||0"></div>
                    </div>
                    <div class="zr-item">
                        <div class="zr-item-label">Avg Ticket</div>
                        <div class="zr-item-val" x-text="'Af ' + fmt(zreport.avg_ticket||0)"></div>
                    </div>
                </div>

                <hr class="zr-divider">

                <div class="zr-row"><span class="zr-row-label">Starting Cash</span><span class="zr-row-val" x-text="'Af ' + fmt(zreport.starting_cash||0)"></span></div>
                <div class="zr-row"><span class="zr-row-label">Cash Sales</span><span class="zr-row-val pos" x-text="'+ Af ' + fmt(zreport.cash_sales||0)"></span></div>
                <div class="zr-row"><span class="zr-row-label">Loan Sales</span><span class="zr-row-val" x-text="'Af ' + fmt(zreport.loan_sales||0)"></span></div>
                <div class="zr-row"><span class="zr-row-label">Discounts Given</span><span class="zr-row-val neg" x-text="'- Af ' + fmt(zreport.discounts||0)"></span></div>
                <div class="zr-row"><span class="zr-row-label">Returns / Refunds</span><span class="zr-row-val neg" x-text="'- Af ' + fmt(zreport.returns||0)"></span></div>

                <div class="zr-total-row">
                    <span class="zr-total-label">Expected Cash in Drawer</span>
                    <span class="zr-total-val" x-text="'Af ' + fmt(zreport.expected_cash||0)"></span>
                </div>

                <template x-if="zreport.actual_cash">
                    <div>
                        <hr class="zr-divider">
                        <div class="zr-row"><span class="zr-row-label">Actual Cash (Counted)</span><span class="zr-row-val" x-text="'Af ' + fmt(zreport.actual_cash||0)"></span></div>
                        <div class="zr-row">
                            <span class="zr-row-label">Discrepancy</span>
                            <span class="zr-row-val" :class="(zreport.discrepancy||0) >= 0 ? 'pos' : 'neg'"
                                  x-text="((zreport.discrepancy||0) >= 0 ? '+' : '') + 'Af ' + fmt(zreport.discrepancy||0)"></span>
                        </div>
                        <div x-show="zreport.discrepancy_note"
                             style="margin-top:.5rem;padding:8px 10px;background:rgba(255,255,255,.06);border-radius:var(--rsm);font-size:12px;color:rgba(255,255,255,.5)"
                             x-text="'Note: ' + zreport.discrepancy_note"></div>
                    </div>
                </template>

                <div class="zr-footer">
                    <button type="button" class="btn btn-ghost" @click="printZReport()">
                        <i class="fas fa-print"></i> Print
                    </button>
                    <button type="button" class="btn btn-ghost" @click="exportZReportCsv()">
                        <i class="fas fa-file-csv"></i> Export CSV
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
    tab:              'overview',
    preset:           'today',
    dateFrom:         '',
    dateTo:           '',
    data:             {},
    zreport:          {},
    selectedShift:    '',
    loading: false,
    chartGranularity: 'daily',
    charts:           {},

    urls: {
        report:  '{{ route("pos.reports.data") }}',
        zreport: '{{ route("pos.reports.zreport") }}',
        export:  '{{ route("pos.reports.export") }}',
        csrf:    document.querySelector('meta[name=csrf-token]').content,
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
        const now   = new Date();
        const fmt   = d => d.toISOString().split('T')[0];
        const sod   = d => { const x = new Date(d); x.setHours(0,0,0,0); return x; };

        switch(p) {
            case 'today':
                this.dateFrom = this.dateTo = fmt(now); break;
            case 'yesterday':
                const y = new Date(now); y.setDate(y.getDate()-1);
                this.dateFrom = this.dateTo = fmt(y); break;
            case 'week':
                const ws = new Date(now); ws.setDate(ws.getDate() - ws.getDay());
                this.dateFrom = fmt(ws); this.dateTo = fmt(now); break;
            case 'month':
                this.dateFrom = fmt(new Date(now.getFullYear(), now.getMonth(), 1));
                this.dateTo   = fmt(now); break;
            case 'quarter':
                const q = Math.floor(now.getMonth()/3);
                this.dateFrom = fmt(new Date(now.getFullYear(), q*3, 1));
                this.dateTo   = fmt(now); break;
            case 'year':
                this.dateFrom = fmt(new Date(now.getFullYear(), 0, 1));
                this.dateTo   = fmt(now); break;
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
            granularity: this.chartGranularity
        });

        const r = await fetch(`${this.urls.report}?${params}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (!r.ok) {
            throw new Error('Failed to load reports');
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
            const r = await fetch(`${this.urls.zreport}?shift_id=${this.selectedShift}`, {
                headers: { 'Accept':'application/json','X-Requested-With':'XMLHttpRequest' }
            });
            this.zreport = await r.json();
        } catch(e) { console.error(e); }
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
        if (this.charts[id]) { this.charts[id].destroy(); delete this.charts[id]; }
    },

    apexDefaults() {
        return {
            chart: { fontFamily: 'DM Mono, monospace', toolbar: { show: false }, animations: { enabled: true, easing: 'easeinout', speed: 600 } },
            colors: ['#2f6fe8','#15803d','#d97706','#7c3aed','#0891b2','#dc2626'],
            grid:   { borderColor: '#dde1ee', strokeDashArray: 4 },
            tooltip:{ theme: 'light' },
            xaxis:  { labels: { style: { colors: '#848baa', fontFamily: 'DM Mono, monospace', fontSize: '11px' } }, axisBorder: { show: false } },
            yaxis:  { labels: { style: { colors: '#848baa', fontFamily: 'DM Mono, monospace', fontSize: '11px' } } },
        };
    },

    renderOverviewChart() {
        this.destroyChart('overview');
        const el = document.getElementById('chart-overview');
        if (!el) return;
        const labels  = (this.data.trend_labels  || []);
        const revenue = (this.data.trend_revenue || []);
        const profit  = (this.data.trend_profit  || []);
        this.charts['overview'] = new ApexCharts(el, {
            ...this.apexDefaults(),
            chart: { ...this.apexDefaults().chart, type: 'area', height: 320 },
            series: [
                { name: 'Revenue', data: revenue },
                { name: 'Profit',  data: profit  },
            ],
            xaxis: { ...this.apexDefaults().xaxis, categories: labels },
            fill:  { type: 'gradient', gradient: { opacityFrom: .25, opacityTo: .02 } },
            stroke: { curve: 'smooth', width: 2.5 },
            dataLabels: { enabled: false },
            legend: { position: 'top', fontFamily: 'Plus Jakarta Sans, sans-serif' },
        });
        this.charts['overview'].render();
    },

    renderPaymentChart() {
        this.destroyChart('payment');
        const el = document.getElementById('chart-payment');
        if (!el) return;
        this.charts['payment'] = new ApexCharts(el, {
            ...this.apexDefaults(),
            chart: { ...this.apexDefaults().chart, type: 'donut', height: 180 },
            series: [this.data.cash_sales||0, this.data.loan_sales||0],
            labels: ['Cash', 'Loan'],
            colors: ['#2f6fe8','#d97706'],
            legend: { show: false },
            dataLabels: { enabled: true, formatter: (v) => v.toFixed(1) + '%' },
            plotOptions: { pie: { donut: { size: '65%' } } },
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
            chart: { ...this.apexDefaults().chart, type: 'bar', height: 220 },
            series: [{ name: 'Revenue', data: cats.map(c => c.revenue) }],
            xaxis:  { ...this.apexDefaults().xaxis, categories: cats.map(c => c.name) },
            plotOptions: { bar: { borderRadius: 5, horizontal: true } },
            dataLabels: { enabled: false },
        });
        this.charts['category'].render();
    },

    renderDailySalesChart() {
        this.destroyChart('daily-sales');
        const el = document.getElementById('chart-daily-sales');
        if (!el) return;
        this.charts['daily-sales'] = new ApexCharts(el, {
            ...this.apexDefaults(),
            chart: { ...this.apexDefaults().chart, type: 'bar', height: 320, stacked: true },
            series: [
                { name: 'Cash',   data: this.data.daily_cash   || [] },
                { name: 'Loan',   data: this.data.daily_loan   || [] },
            ],
            xaxis:  { ...this.apexDefaults().xaxis, categories: this.data.daily_labels || [] },
            colors: ['#2f6fe8','#d97706'],
            plotOptions: { bar: { borderRadius: 4, columnWidth: '60%' } },
            dataLabels: { enabled: false },
            legend: { position: 'top', fontFamily: 'Plus Jakarta Sans, sans-serif' },
        });
        this.charts['daily-sales'].render();
    },

    renderWeekdayChart() {
        this.destroyChart('weekday');
        const el = document.getElementById('chart-weekday');
        if (!el) return;
        const days = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
        this.charts['weekday'] = new ApexCharts(el, {
            ...this.apexDefaults(),
            chart: { ...this.apexDefaults().chart, type: 'radar', height: 260 },
            series: [{ name: 'Avg Sales', data: this.data.weekday_avg || Array(7).fill(0) }],
            xaxis:  { categories: days },
            fill:   { opacity: .2 },
            stroke: { width: 2 },
            markers:{ size: 4 },
        });
        this.charts['weekday'].render();
    },

    renderProductsChart() {
        this.destroyChart('products');
        const el = document.getElementById('chart-products');
        if (!el) return;
        const prods = (this.data.top_products || []).slice(0,10);
        this.charts['products'] = new ApexCharts(el, {
            ...this.apexDefaults(),
            chart: { ...this.apexDefaults().chart, type: 'bar', height: 320 },
            series: [
                { name: 'Revenue', data: prods.map(p => p.revenue) },
                { name: 'Profit',  data: prods.map(p => p.profit)  },
            ],
            xaxis:  { ...this.apexDefaults().xaxis, categories: prods.map(p => p.name.length > 14 ? p.name.slice(0,14)+'…' : p.name) },
            plotOptions: { bar: { borderRadius: 4, columnWidth: '55%' } },
            dataLabels: { enabled: false },
            legend: { position: 'top', fontFamily: 'Plus Jakarta Sans, sans-serif' },
        });
        this.charts['products'].render();
    },

    renderStockStatusChart() {
        this.destroyChart('stock-status');
        const el = document.getElementById('chart-stock-status');
        if (!el) return;
        this.charts['stock-status'] = new ApexCharts(el, {
            ...this.apexDefaults(),
            chart: { ...this.apexDefaults().chart, type: 'donut', height: 240 },
            series: [this.data.stock_ok||0, this.data.stock_low||0, this.data.stock_zero||0],
            labels: ['Healthy','Low Stock','Out of Stock'],
            colors: ['#15803d','#d97706','#dc2626'],
            legend: { position: 'bottom', fontFamily: 'Plus Jakarta Sans, sans-serif' },
            plotOptions: { pie: { donut: { size: '60%' } } },
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
            chart: { ...this.apexDefaults().chart, type: 'bar', height: 240 },
            series: [{ name: 'Value (Cost)', data: cats.map(c => c.value) }],
            xaxis:  { ...this.apexDefaults().xaxis, categories: cats.map(c => c.name) },
            colors: ['#7c3aed'],
            plotOptions: { bar: { borderRadius: 5, horizontal: true } },
            dataLabels: { enabled: false },
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
            chart: { ...this.apexDefaults().chart, type: 'bar', height: 280 },
            series: [{ name: 'Total Sales', data: cashiers.map(c => c.total_sales) }],
            xaxis:  { ...this.apexDefaults().xaxis, categories: cashiers.map(c => c.name) },
            plotOptions: { bar: { borderRadius: 6, columnWidth: '50%' } },
            dataLabels: { enabled: false },
        });
        this.charts['cashiers'].render();
    },

    renderLoansChart() {
        this.destroyChart('loans');
        const el = document.getElementById('chart-loans');
        if (!el) return;
        this.charts['loans'] = new ApexCharts(el, {
            ...this.apexDefaults(),
            chart: { ...this.apexDefaults().chart, type: 'line', height: 280 },
            series: [
                { name: 'Issued',    data: this.data.loan_issued_series    || [] },
                { name: 'Collected', data: this.data.loan_collected_series || [] },
            ],
            xaxis:  { ...this.apexDefaults().xaxis, categories: this.data.trend_labels || [] },
            colors: ['#d97706','#15803d'],
            stroke: { curve: 'smooth', width: 2.5 },
            markers:{ size: 4 },
            dataLabels: { enabled: false },
            legend: { position: 'top', fontFamily: 'Plus Jakarta Sans, sans-serif' },
        });
        this.charts['loans'].render();
    },

    /* ── Heatmap color ── */
    heatColor(val, max) {
        const pct = max > 0 ? val / max : 0;
        if (pct === 0) return '#eceef5';
        const r = Math.round(47  + (220-47)  * (1-pct));
        const g = Math.round(111 + (38-111)  * (1-pct));
        const b = Math.round(232 + (38-232)  * (1-pct));
        return `rgb(${r},${g},${b})`;
    },

    /* ── Export / Print ── */
    printReport() { window.printSection('.rp'); },
    printZReport() { window.printSection('#zreport-content'); },
    exportCsv()    { window.location.href = this.urls.export + '?from=' + this.dateFrom + '&to=' + this.dateTo + '&type=csv'; },
    exportPdf()    { window.location.href = this.urls.export + '?from=' + this.dateFrom + '&to=' + this.dateTo + '&type=pdf'; },
    exportZReportCsv() { window.location.href = this.urls.export + '?shift_id=' + this.selectedShift + '&type=zreport'; },

    /* ── Helpers ── */
    initials(name) {
        if (!name) return '?';
        return name.trim().split(' ').map(w => w[0]).join('').toUpperCase().slice(0,2);
    },
    fmt(n) {
        return Number(n||0).toLocaleString('en-US',{minimumFractionDigits:0,maximumFractionDigits:0});
    },
}));
});
</script>
@endpush
