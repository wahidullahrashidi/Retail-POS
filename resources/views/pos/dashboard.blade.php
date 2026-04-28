@extends('layouts.app')

@push('styles')
    @vite(['resources/css/pages/dashboard.css'])
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
                <a href="{{ route('pos.poscheck') }}" class="btn-new-sale">
                    <i class="fas fa-bolt"></i> New Sale
                </a>
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
