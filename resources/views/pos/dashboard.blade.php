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
                        @if ($profitPercentage >= 0)
                            <i class="fas fa-arrow-up text-[10px]"></i> {{ $profitPercentage }}
                        @else
                            <i class="fas fa-arrow-down text-[10px]"></i> {{ $profitPercentage }}%
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
                        @if ($loanPercentage >= 0)
                            <i class="fas fa-arrow-up text-[10px]"></i> {{ $loanPercentage }}%
                        @else
                            <i class="fas fa-arrow-down text-[10px]"></i> {{ $loanPercentage }}%
                        @endif

                    </span>
                </div>
                <div class="text-2xl font-bold text-gray-900">Af {{ $loan }}</div>
            </div>
            <div class="stat-card bg-white rounded-xl border border-card-border p-5">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Net Profit</span>
                    <span
                        class="flex items-center gap-1 text-xs font-semibold text-success bg-green-50 px-2 py-0.5 rounded-full">
                        @if ($netProfitPercentage >= 0)
                            <i class="fas fa-arrow-up text-[10px]"></i> {{ $netProfitPercentage }}%
                        @else
                            <i class="fas fa-arrow-down text-[10px]"></i> {{ $netProfitPercentage }}%
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
                        @if ($cutomersPercentage >= 0)
                            <i class="fas fa-arrow-up text-[10px]"></i> {{ $cutomersPercentage }}%
                        @else
                            <i class="fas fa-arrow-down text-[10px]"></i> {{ $cutomersPercentage }}%
                        @endif

                    </span>
                </div>
                <div class="text-2xl font-bold text-gray-900">{{ $todaysCustomers }}</div>
            </div>
        </div>

        <div class="flex gap-6">
            <!-- Left Column -->
            <div class="flex-1 space-y-6">
                <!-- Quick Sale Entry -->
                <div class="bg-white rounded-xl border border-card-border overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-shopping-cart text-primary"></i>
                            <span class="font-semibold text-gray-900">Quick Sale Entry</span>
                        </div>
                        <span class="text-xs text-gray-500 bg-gray-100 px-3 py-1 rounded-full">Scan Barcode to Begin</span>
                    </div>
                    <div class="p-5">
                        <div class="flex gap-3 mb-5">
                            <div class="flex-1 relative">
                                <i class="fas fa-barcode absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                <input type="text" placeholder="Focus here for Barcode (EAN-13, SKU...)"
                                    class="search-input w-full pl-10 pr-4 py-3 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-primary transition-all">
                            </div>
                            <button
                                class="px-6 py-3 bg-primary text-white rounded-lg font-medium hover:bg-primary-dark transition-colors flex items-center gap-2">
                                <i class="fas fa-search"></i>
                                Search Item
                            </button>
                        </div>

                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Current Cart
                                (Pending)</span>
                            <span class="text-sm text-primary font-medium">3 Items added</span>
                        </div>

                        <div class="border border-gray-200 rounded-lg overflow-hidden mb-4">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="text-left px-4 py-3 font-medium text-gray-600">Item Name</th>
                                        <th class="text-center px-4 py-3 font-medium text-gray-600">Qty</th>
                                        <th class="text-right px-4 py-3 font-medium text-gray-600">Price</th>
                                        <th class="text-right px-4 py-3 font-medium text-gray-600">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <tr class="transaction-row">
                                        <td class="px-4 py-3 text-gray-900">Premium Saffron (10g Pack)</td>
                                        <td class="px-4 py-3 text-center text-gray-700">2</td>
                                        <td class="px-4 py-3 text-right text-gray-700">Af 1,200</td>
                                        <td class="px-4 py-3 text-right font-semibold text-gray-900">Af 2,400</td>
                                    </tr>
                                    <tr class="transaction-row">
                                        <td class="px-4 py-3 text-gray-900">Basmati Rice (5kg Bag)</td>
                                        <td class="px-4 py-3 text-center text-gray-700">1</td>
                                        <td class="px-4 py-3 text-right text-gray-700">Af 850</td>
                                        <td class="px-4 py-3 text-right font-semibold text-gray-900">Af 850</td>
                                    </tr>
                                    <tr class="transaction-row">
                                        <td class="px-4 py-3 text-gray-900">Pure Honey (Kandahar Extra)</td>
                                        <td class="px-4 py-3 text-center text-gray-700">1</td>
                                        <td class="px-4 py-3 text-right text-gray-700">Af 1,500</td>
                                        <td class="px-4 py-3 text-right font-semibold text-gray-900">Af 1,500</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="bg-primary rounded-xl p-5 flex items-center justify-between">
                            <div>
                                <div class="text-xs font-medium text-primary-light uppercase tracking-wider mb-1">Total
                                    Amount Due</div>
                                <div class="text-3xl font-bold text-white">Af 4,750</div>
                            </div>
                            <button
                                class="checkout-btn px-8 py-3 bg-white text-gray-900 rounded-lg font-bold text-lg hover:bg-gray-50">
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
                        <a href="{{ route('pos.allCustomers') }}" class="text-sm text-primary font-medium hover:text-primary-dark">View All Logs</a>
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
                                @foreach ($customers as $customer)
                                    <tr class="transaction-row">
                                        <td class="px-5 py-3.5">
                                            <span
                                                class="text-primary font-semibold text-sm">{{ $customer->address }}</span>
                                        </td>
                                        <td class="px-5 py-3.5">
                                            @php
                                                // Split the name into words
                                                $parts = explode(' ', trim($customer->name));

                                                // Get first letter of first and last word
                                                $initials = strtoupper(
                                                    substr($parts[0], 0, 1) . substr(end($parts), 0, 1),
                                                );
                                            @endphp

                                            <div class="flex items-center gap-2">
                                                <div
                                                    class="w-6 h-6 flex items-center justify-center rounded-full bg-indigo-500 text-white text-xs font-bold">
                                                    {{ $initials }}
                                                </div>
                                                <span class="font-medium text-gray-900">{{ $customer->name }}</span>
                                            </div>
                                        </td>
                                        <td class="px-5 py-3.5 text-gray-700">{{ $customer->updated_at->format('H:i A') }}</td>
                                        <td class="px-5 py-3.5">
                                            <span
                                                class="px-2.5 py-1 bg-gray-100 text-gray-700 text-xs font-medium rounded-full">Cash</span>
                                        </td>
                                        <td class="px-5 py-3.5 text-right font-semibold text-gray-900">Af
                                            {{ $customer->credit_limit }}</td>
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
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-sm font-medium text-gray-900">Cooking Oil (1L)</div>
                                <div class="text-xs text-gray-500">Min: 20</div>
                            </div>
                            <span class="text-sm font-bold text-danger">8 left</span>
                        </div>
                        <div class="border-t border-gray-100"></div>
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-sm font-medium text-gray-900">Green Tea (Special)</div>
                                <div class="text-xs text-gray-500">Min: 10</div>
                            </div>
                            <span class="text-sm font-bold text-danger">3 left</span>
                        </div>
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
