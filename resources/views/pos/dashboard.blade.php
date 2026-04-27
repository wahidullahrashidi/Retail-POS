@extends('layouts.app')

@section('content')
    <!-- DASHBOARD VIEW -->
    <div id="view-dashboard" class="view-section">
        <!-- Page Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">System Dashboard</h1>
                <p class="text-sm text-gray-500 mt-1">Welcome back, Shop Manager. Here is what's happening today.</p>
            </div>
            <div class="flex items-center gap-3">
                <button
                    class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                    <i class="fas fa-calendar-alt text-primary"></i>
                    Solar Hijri View
                </button>
                <button
                    class="flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:bg-primary-dark transition-colors shadow-sm">
                    <i class="fas fa-plus"></i>
                    New POS Sale
                </button>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-4 gap-4 mb-6">
            <div class="stat-card bg-white rounded-xl border border-card-border p-5">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Today's Sales</span>
                    <span
                        class="flex items-center gap-1 text-xs font-semibold text-success bg-green-50 px-2 py-0.5 rounded-full">
                        @if ($todaySales > 0)
                            @if ($yesterdaySales > 0)
                                <i class="fas fa-arrow-up text-[10px]"></i> {{ $profitPercentage }}%
                            @else 
                                <i class="fas fa-arrow-up text-[10px]"></i> 100%
                            @endif
                        @else 
                            @if ($yesterdaySales > 0)
                                <i class="fas fa-arrow-up text-[10px]"></i> -{{ $profitPercentage }}%
                            @else 
                                <i class="fas fa-arrow-up text-[10px]"></i> 0%
                            @endif
                        @endif

                    </span>
                </div>
                <div class="text-2xl font-bold text-gray-900">Af {{ $todaySales }}</div>
            </div>
            <div class="stat-card bg-white rounded-xl border border-card-border p-5">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Active Loans</span>
                    <span
                        class="flex items-center gap-1 text-xs font-semibold text-success bg-green-50 px-2 py-0.5 rounded-full">
                        @if ($loanToday > 0)
                            @if ($loanYesterday > 0)
                                <i class="fas fa-arrow-up text-[10px]"></i> {{ $loanPercentage }}%
                            @else 
                                <i class="fas fa-arrow-up text-[10px]"></i> 100%
                            @endif
                        @else 
                            @if ($loanYesterday > 0)
                                <i class="fas fa-arrow-up text-[10px]"></i> {{ $loanPercentage }}%
                            @else 
                                <i class="fas fa-arrow-up text-[10px]"></i> 0%
                            @endif
                        @endif

                    </span>
                </div>
                <div class="text-2xl font-bold text-gray-900">Af {{ $loanToday }}</div>
            </div>
            <div class="stat-card bg-white rounded-xl border border-card-border p-5">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Net Profit</span>
                    <span
                        class="flex items-center gap-1 text-xs font-semibold text-success bg-green-50 px-2 py-0.5 rounded-full">
                        @if ($netProfitToday > 0)
                            @if ($netProfitYesterday > 0)
                                <i class="fas fa-arrow-up text-[10px]"></i> {{ $netProfitPercentage }}%
                            @else 
                                <i class="fas fa-arrow-up text-[10px]"></i> 100%
                            @endif
                        @else 
                            @if ($netProfitYesterday > 0)
                                <i class="fas fa-arrow-up text-[10px]"></i> -{{ $netProfitPercentage }}%
                            @else 
                                <i class="fas fa-arrow-up text-[10px]"></i> 0%
                            @endif
                        @endif
                    </span>
                </div>
                <div class="text-2xl font-bold text-gray-900">Af {{ $netProfitToday }}</div>
            </div>
            <div class="stat-card bg-white rounded-xl border border-card-border p-5">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Customer Count</span>
                    <span
                        class="flex items-center gap-1 text-xs font-semibold text-success bg-green-50 px-2 py-0.5 rounded-full">
                        @if ($todaysCustomers > 0)
                            @if ($yesterdayCustomers > 0)
                                <i class="fas fa-arrow-up text-[10px]"></i> {{ $customersPercentage }}%
                            @else 
                                <i class="fas fa-arrow-up text-[10px]"></i> 100%
                            @endif
                        @else 
                            @if ($yesterdayCustomers > 0)
                                <i class="fas fa-arrow-up text-[10px]"></i> -{{ $customersPercentage }}%
                            @else 
                                <i class="fas fa-arrow text-[10px]"></i> 0%
                            @endif
                        @endif

                    </span>
                </div>
                <div class="text-2xl font-bold text-gray-900">{{ $todaysCustomers }}</div>
            </div>
        </div>

        <div class="flex gap-6">
            <!-- Left Column -->
            <div class="flex-1 space-y-6">
                {{-- ── QUICK SALE ENTRY ── --}}
                <div class="bg-white rounded-xl border border-card-border overflow-hidden" x-data="cartSystem()"
                    x-init="init()">

                    {{-- Header --}}
                    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-shopping-cart text-primary"></i>
                            <span class="font-semibold text-gray-900">Quick Sale Entry</span>
                        </div>
                        <span class="text-xs text-gray-500 bg-gray-100 px-3 py-1 rounded-full"
                            x-text="cart.length ? cart.length + ' item(s) in cart' : 'Scan Barcode to Begin'">
                        </span>
                    </div>

                    <div class="p-5">

                        {{-- Search bar --}}
                        <div class="flex gap-3 mb-5">
                            <div class="flex-1 relative">
                                <i class="fas fa-barcode absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                <input type="text" x-model="query" @input.debounce.400ms="search()"
                                    @keydown.escape="clearSearch()" @keydown.enter="search()"
                                    placeholder="Focus here for Barcode (EAN-13, SKU...)"
                                    class="w-full pl-10 pr-4 py-3 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-primary transition-all">
                            </div>
                            <button type="button" @click="search()"
                                class="px-6 py-3 bg-primary text-white rounded-lg font-medium hover:bg-primary-dark transition-colors flex items-center gap-2">
                                <i class="fas fa-search"></i> Search
                            </button>
                            <button type="button" x-show="query" x-cloak @click="clearSearch()"
                                class="px-4 py-3 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition-colors">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        {{-- Searching spinner --}}
                        <div x-show="searching" x-cloak class="text-center py-6 text-gray-400 mb-4">
                            <i class="fas fa-spinner fa-spin text-xl"></i>
                            <span class="ml-2 text-sm">Searching...</span>
                        </div>

                        {{-- ── SEARCH RESULTS ── --}}
                        <div x-show="query && !searching" x-cloak>

                            {{-- No results --}}
                            <div x-show="searchResults.length === 0" class="text-center py-8 text-gray-400 mb-4">
                                <i class="fas fa-search text-2xl mb-2 block"></i>
                                <span x-text="'No products found for &quot;' + query + '&quot;'"></span>
                            </div>

                            {{-- Results table --}}
                            <div x-show="searchResults.length > 0">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Search
                                        Results</span>
                                    <span class="text-sm text-primary font-medium"
                                        x-text="searchResults.length + ' product(s) found'"></span>
                                </div>
                                <div
                                    class="border border-gray-200 rounded-lg overflow-hidden mb-4 max-h-64 overflow-y-auto">
                                    <table class="w-full text-sm">
                                        <thead class="bg-gray-50 sticky top-0">
                                            <tr>
                                                <th class="text-left px-4 py-3 font-medium text-gray-600">Product</th>
                                                <th class="text-left px-4 py-3 font-medium text-gray-600">SKU</th>
                                                <th class="text-right px-4 py-3 font-medium text-gray-600">Price</th>
                                                <th class="text-right px-4 py-3 font-medium text-gray-600">Stock</th>
                                                <th class="text-center px-4 py-3 font-medium text-gray-600">Add</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100">
                                            <template x-for="product in searchResults" :key="product.variant_id">
                                                <tr class="hover:bg-gray-50 transition-colors">
                                                    <td class="px-4 py-3 text-gray-900 font-medium" x-text="product.name">
                                                    </td>
                                                    <td class="px-4 py-3 text-gray-400 text-xs font-mono"
                                                        x-text="product.sku"></td>
                                                    <td class="px-4 py-3 text-right text-gray-700">
                                                        Af <span x-text="formatNumber(product.price)"></span>
                                                    </td>
                                                    <td class="px-4 py-3 text-right">
                                                        <span
                                                            :class="product.stock_quantity < 5 ? 'text-red-500 font-semibold' :
                                                                'text-green-600'"
                                                            x-text="product.stock_quantity"></span>
                                                    </td>
                                                    <td class="px-4 py-3 text-center">
                                                        <button type="button" @click="addToCart(product)"
                                                            :disabled="product.stock_quantity === 0"
                                                            class="px-3 py-1.5 bg-primary text-white rounded-lg text-xs font-semibold hover:bg-primary-dark disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
                                                            <i class="fas fa-plus mr-1"></i> Add
                                                        </button>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        {{-- ── TRENDING (shown when no search query) ── --}}
                        <div x-show="!query" x-cloak>
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    <i class="fas fa-fire text-orange-400 mr-1"></i> Trending This Week
                                </span>
                                <span x-show="trendingLoading" class="text-xs text-gray-400">
                                    <i class="fas fa-spinner fa-spin"></i>
                                </span>
                            </div>
                            <div class="grid grid-cols-2 gap-2 mb-4">
                                <template x-for="product in trendingProducts" :key="product.variant_id">
                                    <button type="button" @click="addToCart(product)"
                                        :disabled="product.stock_quantity === 0"
                                        class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:border-primary hover:bg-blue-50 transition-all text-left disabled:opacity-40 disabled:cursor-not-allowed group">
                                        <div class="min-w-0 flex-1">
                                            <div class="text-xs font-semibold text-gray-800 truncate"
                                                x-text="product.name"></div>
                                            <div class="text-xs text-primary font-medium mt-0.5">
                                                Af <span x-text="formatNumber(product.price)"></span>
                                            </div>
                                            <div class="text-xs text-gray-400 mt-0.5"
                                                x-text="product.total_sold + ' sold this week'"></div>
                                        </div>
                                        <i
                                            class="fas fa-plus text-gray-300 group-hover:text-primary ml-2 transition-colors flex-shrink-0"></i>
                                    </button>
                                </template>
                                {{-- Skeleton placeholders while loading --}}
                                <template x-if="trendingLoading">
                                    <template x-for="i in [1,2,3,4,5,6,7,8]" :key="i">
                                        <div class="p-3 border border-gray-100 rounded-lg animate-pulse">
                                            <div class="h-3 bg-gray-200 rounded w-3/4 mb-2"></div>
                                            <div class="h-3 bg-gray-100 rounded w-1/2"></div>
                                        </div>
                                    </template>
                                </template>
                            </div>
                        </div>

                        {{-- ── CART ── --}}
                        <div x-show="cart.length > 0" x-cloak>
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Current Cart (Pending)
                                </span>
                                <div class="flex items-center gap-3">
                                    <span class="text-sm text-primary font-medium"
                                        x-text="cart.length + ' item(s) added'"></span>
                                    <button type="button" @click="clearCart()"
                                        class="text-xs text-red-400 hover:text-red-600 transition-colors">
                                        <i class="fas fa-trash mr-1"></i> Clear
                                    </button>
                                </div>
                            </div>
                            <div class="border border-gray-200 rounded-lg overflow-hidden mb-4">
                                <table class="w-full text-sm">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="text-left px-4 py-3 font-medium text-gray-600">Item</th>
                                            <th class="text-center px-4 py-3 font-medium text-gray-600">Qty</th>
                                            <th class="text-right px-4 py-3 font-medium text-gray-600">Price</th>
                                            <th class="text-right px-4 py-3 font-medium text-gray-600">Total</th>
                                            <th class="px-4 py-3"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        <template x-for="(item, index) in cart" :key="item.variant_id">
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-3 text-gray-900 font-medium" x-text="item.name"></td>
                                                <td class="px-4 py-3">
                                                    <div class="flex items-center justify-center gap-2">
                                                        <button type="button" @click="decreaseQty(index)"
                                                            class="w-6 h-6 rounded-full bg-gray-100 hover:bg-red-100 hover:text-red-600 flex items-center justify-center transition-colors">
                                                            <i class="fas fa-minus text-xs"></i>
                                                        </button>
                                                        <span class="w-6 text-center font-semibold"
                                                            x-text="item.qty"></span>
                                                        <button type="button" @click="increaseQty(index)"
                                                            :disabled="item.qty >= item.stock_quantity"
                                                            class="w-6 h-6 rounded-full bg-gray-100 hover:bg-green-100 hover:text-green-600 flex items-center justify-center disabled:opacity-40 transition-colors">
                                                            <i class="fas fa-plus text-xs"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3 text-right text-gray-600">
                                                    Af <span x-text="formatNumber(item.price)"></span>
                                                </td>
                                                <td class="px-4 py-3 text-right font-bold text-gray-900">
                                                    Af <span x-text="formatNumber(item.price * item.qty)"></span>
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    <button type="button" @click="removeFromCart(index)"
                                                        class="text-gray-300 hover:text-red-500 transition-colors">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Empty state --}}
                        <div x-show="cart.length === 0 && !query" x-cloak
                            class="text-center py-3 text-gray-300 text-xs mb-4">
                            Cart is empty — tap a trending product or search to add items
                        </div>

                        {{-- ── TOTAL & CHECKOUT ── --}}
                        <div class="bg-primary rounded-xl p-5 flex items-center justify-between">
                            <div>
                                <div class="text-xs font-medium text-primary-light uppercase tracking-wider mb-1">Total
                                    Amount Due</div>
                                <div class="text-3xl font-bold text-white">
                                    Af <span x-text="formatNumber(cartTotal)"></span>
                                </div>
                            </div>
                            <button type="button" @click="checkout()" :disabled="cart.length === 0"
                                class="px-8 py-3 bg-white text-gray-900 rounded-lg font-bold text-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-all">
                                CHECKOUT
                            </button>
                        </div>

                    </div>
                </div>



                <!-- Quick Actions -->
                <div class="grid grid-cols-4 gap-4">
                    <div class="quick-action-card bg-white rounded-xl border border-card-border p-5 cursor-pointer">
                        <div class="w-10 h-10 bg-indigo-50 rounded-lg flex items-center justify-center text-primary mb-3">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <div class="font-semibold text-gray-900 text-sm">New Customer</div>
                        <div class="text-xs text-gray-500 mt-1">Register for loans</div>
                    </div>
                    <div class="quick-action-card bg-white rounded-xl border border-card-border p-5 cursor-pointer">
                        <div class="w-10 h-10 bg-indigo-50 rounded-lg flex items-center justify-center text-primary mb-3">
                            <i class="fas fa-cube"></i>
                        </div>
                        <div class="font-semibold text-gray-900 text-sm">Add Product</div>
                        <div class="text-xs text-gray-500 mt-1">Inventory update</div>
                    </div>
                    <div class="quick-action-card bg-white rounded-xl border border-card-border p-5 cursor-pointer">
                        <div class="w-10 h-10 bg-indigo-50 rounded-lg flex items-center justify-center text-primary mb-3">
                            <i class="fas fa-clock-rotate-left"></i>
                        </div>
                        <div class="font-semibold text-gray-900 text-sm">Sales Log</div>
                        <div class="text-xs text-gray-500 mt-1">Review transactions</div>
                    </div>
                    <div class="quick-action-card bg-white rounded-xl border border-card-border p-5 cursor-pointer">
                        <div class="w-10 h-10 bg-indigo-50 rounded-lg flex items-center justify-center text-primary mb-3">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <div class="font-semibold text-gray-900 text-sm">Loan Payment</div>
                        <div class="text-xs text-gray-500 mt-1">Receive Af payments</div>
                    </div>
                </div>

                <!-- Recent Transactions -->
                <div class="bg-white rounded-xl border border-card-border overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                        <div>
                            <h3 class="font-semibold text-gray-900">Recent Transactions</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Latest 5 sales in Kabul Central Branch</p>
                        </div>
                        {{-- <a href="{{ route('pos.allCustomers') }}"
                            class="text-sm text-primary font-medium hover:text-primary-dark">View All Logs</a> --}}
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="text-left px-5 py-3 font-medium text-gray-600">Address</th>
                                    <th class="text-left px-5 py-3 font-medium text-gray-600">Customer</th>
                                    <th class="text-left px-5 py-3 font-medium text-gray-600">Time (Solar)</th>
                                    <th class="text-left px-5 py-3 font-medium text-gray-600">Payment</th>
                                    <th class="text-right px-5 py-3 font-medium text-gray-600">Total</th>
                                    <th class="px-5 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($recentTransactions as $rt)
                                    <tr class="transaction-row">
                                        <td class="px-5 py-3.5">
                                            <span class="text-primary font-semibold text-sm">{{ $rt->address }}</span>
                                        </td>
                                        <td class="px-5 py-3.5">
                                            @php
                                                $parts = array_values(
                                                    array_filter(explode(' ', trim($rt->customer_name))),
                                                );

                                                if (count($parts) === 1) {
                                                    $initials = strtoupper(substr($parts[0], 0, 2));
                                                } else {
                                                    $initials = strtoupper(
                                                        collect($parts)
                                                            ->map(fn($part) => substr($part, 0, 1))
                                                            ->join(''),
                                                    );
                                                }
                                            @endphp

                                            <div class="flex items-center gap-2">
                                                <div
                                                    class="w-6 h-6 flex items-center justify-center rounded-full bg-indigo-500 text-white text-xs font-bold">
                                                    {{ $initials }}
                                                </div>
                                                <span class="font-medium text-gray-900">{{ $rt->customer_name }}</span>
                                            </div>
                                        </td>
                                        <td class="px-5 py-3.5 text-gray-700">
                                            {{ \Carbon\Carbon::parse($rt->created_at)->diffForHumans() }}</td>
                                        <td class="px-5 py-3.5">
                                            @if ($rt->type === 'loan')
                                                <span
                                                    class="px-2.5 py-1 bg-gray-100 text-gray-700 text-xs font-medium rounded-full">Loan</span>
                                            @else
                                                <span
                                                    class="px-2.5 py-1 bg-gray-100 text-gray-700 text-xs font-medium rounded-full">Cash</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3.5 text-right font-semibold text-gray-900">Af
                                            {{ $rt->amount }}</td>
                                        <td class="px-5 py-3.5 text-center">
                                            <button class="text-gray-400 hover:text-gray-600"><i
                                                    class="fas fa-ellipsis-vertical"></i></button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="w-80 space-y-6 flex-shrink-0">
                <!-- Daily Goal -->
                {{-- <div class="bg-white rounded-xl border border-card-border p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold text-gray-900">Daily Goal</h3>
                        <i class="fas fa-arrow-trend-up text-success"></i>
                    </div>
                    <p class="text-xs text-gray-500 mb-4">Target: Af 200k today</p>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-semibold text-gray-600">Current Progress</span>
                        <span class="text-sm font-bold text-gray-900">71%</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2.5 mb-4">
                        <div class="progress-bar bg-primary h-2.5 rounded-full" style="width: 71%"></div>
                    </div>
                    <div class="flex justify-between">
                        <div>
                            <div class="text-sm font-bold text-gray-900">Af 142k</div>
                            <div class="text-xs text-gray-500">Total Sales</div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-bold text-gray-900">Af 58k</div>
                            <div class="text-xs text-gray-500">Remaining</div>
                        </div>
                    </div>
                </div> --}}

                <!-- Pending Orders -->
                {{-- <div class="bg-white rounded-xl border border-card-border p-5">
                    <div class="flex items-center gap-2 mb-4">
                        <i class="far fa-clock text-warning"></i>
                        <h3 class="font-semibold text-gray-900">Pending Orders</h3>
                    </div>
                    <div class="space-y-3">
                        <div class="border border-gray-200 rounded-lg p-3">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm font-bold text-primary">#PO-102</span>
                                <span class="text-xs text-gray-500">2h ago</span>
                            </div>
                            <p class="text-xs text-gray-600">Wholesale &bull; 42 items</p>
                        </div>
                        <div class="border border-gray-200 rounded-lg p-3">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm font-bold text-primary">#PO-105</span>
                                <span class="text-xs text-gray-500">45m ago</span>
                            </div>
                            <p class="text-xs text-gray-600">Return Request &bull; 3 items</p>
                        </div>
                    </div>
                    <button class="w-full mt-3 text-sm text-primary font-medium hover:text-primary-dark">Manage all
                        orders</button>
                </div> --}}

                <!-- Low Stock Alerts -->
                <div class="bg-white rounded-xl border border-card-border p-5">
                    <div class="flex items-center gap-2 mb-4">
                        <i class="far fa-bell text-danger"></i>
                        <h3 class="font-semibold text-gray-900">Low Stock Alerts</h3>
                    </div>
                    <div class="space-y-3">
                        @foreach ($lowStock as $stock)
                            <div class="flex items-center justify-between">
                                @if (!empty($stock))
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $stock->sku }}</div>
                                        <div class="text-xs text-gray-500">Min: 10</div>
                                    </div>
                                    <span class="text-sm font-bold text-danger">{{ $stock->stock_quantity }} left</span>
                                @else
                                    No Low Stock Alert
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <button
                        class="w-full mt-4 py-2.5 bg-danger text-white rounded-lg text-sm font-medium hover:bg-red-600 transition-colors">
                        Create Purchase Order
                    </button>
                </div>

                <!-- Hardware Status -->
                <div class="bg-white rounded-xl border border-card-border p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold text-gray-900">Hardware Status</h3>
                        <i class="fas fa-arrow-up-right-from-square text-gray-400 text-xs"></i>
                    </div>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-8 h-8 bg-emerald-50 rounded-lg flex items-center justify-center text-success">
                                    <i class="fas fa-print text-xs"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">Receipt Printer</div>
                                    <div class="text-xs text-gray-500">Checked 2m ago</div>
                                </div>
                            </div>
                            <div class="w-2.5 h-2.5 bg-success rounded-full status-dot"></div>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-8 h-8 bg-emerald-50 rounded-lg flex items-center justify-center text-success">
                                    <i class="fas fa-barcode text-xs"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">Barcode Scanner</div>
                                    <div class="text-xs text-gray-500">Checked 1h ago</div>
                                </div>
                            </div>
                            <div class="w-2.5 h-2.5 bg-success rounded-full status-dot"></div>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-amber-50 rounded-lg flex items-center justify-center text-warning">
                                    <i class="fas fa-cash-register text-xs"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">Cash Drawer</div>
                                    <div class="text-xs text-gray-500">Checked 10:00 AM</div>
                                </div>
                            </div>
                            <div class="w-2.5 h-2.5 bg-warning rounded-full"></div>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-8 h-8 bg-emerald-50 rounded-lg flex items-center justify-center text-success">
                                    <i class="fas fa-credit-card text-xs"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">Card Terminal</div>
                                    <div class="text-xs text-gray-500">Checked 5m ago</div>
                                </div>
                            </div>
                            <div class="w-2.5 h-2.5 bg-success rounded-full status-dot"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

{{-- ── Alpine.js Component ── --}}
<script>
    function cartSystem() {
        return {
            query: '',
            searchResults: [],
            trendingProducts: [],
            cart: [],
            searching: false,

            get cartTotal() {
                return this.cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
            },

            // Load trending products on mount
            async loadTrending() {
                try {
                    const res = await fetch('{{ route('pos.products.trending') }}');
                    const data = await res.json();
                    this.trendingProducts = data;
                } catch (e) {
                    console.error('Failed to load trending products', e);
                }
            },

            // Search products
            async search() {
                if (!this.query.trim()) {
                    this.searchResults = [];
                    return;
                }
                this.searching = true;
                try {
                    const res = await fetch(
                        `{{ route('pos.products.search') }}?q=${encodeURIComponent(this.query)}`);
                    const data = await res.json();
                    this.searchResults = data;
                } catch (e) {
                    console.error('Search failed', e);
                } finally {
                    this.searching = false;
                }
            },

            clearSearch() {
                this.query = '';
                this.searchResults = [];
            },

            // Cart operations
            addToCart(product) {
                const existing = this.cart.find(i => i.variant_id === product.variant_id);
                if (existing) {
                    if (existing.qty < product.stock_quantity) existing.qty++;
                } else {
                    this.cart.push({
                        ...product,
                        qty: 1
                    });
                }
                // Clear search after adding so cart is visible
                this.clearSearch();
            },

            increaseQty(index) {
                const item = this.cart[index];
                if (item.qty < item.stock_quantity) item.qty++;
            },

            decreaseQty(index) {
                if (this.cart[index].qty > 1) {
                    this.cart[index].qty--;
                } else {
                    this.removeFromCart(index);
                }
            },

            removeFromCart(index) {
                this.cart.splice(index, 1);
            },

            clearCart() {
                if (confirm('Clear the entire cart?')) this.cart = [];
            },

            checkout() {
                if (!this.cart.length) return;
                // POST cart to your checkout route
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route('pos.checkout') }}';
                const csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = '{{ csrf_token() }}';
                form.appendChild(csrf);
                const cartInput = document.createElement('input');
                cartInput.type = 'hidden';
                cartInput.name = 'cart';
                cartInput.value = JSON.stringify(this.cart);
                form.appendChild(cartInput);
                document.body.appendChild(form);
                form.submit();
            },

            formatNumber(n) {
                return Number(n).toLocaleString('en-US', {
                    minimumFractionDigits: 0
                });
            }
        }
    }
</script>
