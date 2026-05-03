<aside id="sidebar">

    {{-- Logo --}}
    <div class="sb-logo">
        <div class="logo-mark">
            <svg viewBox="0 0 34 34" fill="none" xmlns="http://www.w3.org/2000/svg">
                {{-- Afghan-inspired geometric: 8-pointed star inside octagon --}}
                <polygon
                    points="17,2 20.5,6.5 26,5 24.5,10.5 30,13 27,18 30,23 24.5,25.5 26,31 20.5,29.5 17,34 13.5,29.5 8,31 9.5,25.5 4,23 7,18 4,13 9.5,10.5 8,5 13.5,6.5"
                    fill="#3b5bdb" opacity=".15" />
                <polygon
                    points="17,4 20,8 25,6.5 23.5,11.5 28.5,14 26,18 28.5,22 23.5,24.5 25,29.5 20,28 17,32 14,28 9,29.5 10.5,24.5 5.5,22 8,18 5.5,14 10.5,11.5 9,6.5 14,8"
                    fill="none" stroke="#3b5bdb" stroke-width="1.2" />
                {{-- Inner 4-pointed star --}}
                <path d="M17 9 L18.8 15.2 L25 17 L18.8 18.8 L17 25 L15.2 18.8 L9 17 L15.2 15.2 Z" fill="#3b5bdb" />
                {{-- Center dot --}}
                <circle cx="17" cy="17" r="2" fill="#fff" />
            </svg>
        </div>
        <span class="logo-text">Afghan POS</span>

    </div>


    {{-- Navigation --}}
    <nav class="sb-nav">
        {{-- Store section --}}
        <div class="sb-section-label flex items-center">
            <span class="sb-label-text">Store</span>
            <button class="sb-toggle" @click="toggleSidebar()" title="Toggle sidebar">
                <i class="fas fa-chevron-left"></i>
            </button>
        </div>
        <a href="{{ route('pos.dashboard') }}" class="sb-item {{ request()->routeIs('pos.dashboard') ? 'active' : '' }}"
            data-tip="Dashboard">
            <i class="fas fa-gauge-high"></i>
            <span class="sb-item-text">Dashboard</span>
        </a>
        <a href="{{ route('pos.poscheck') }}" class="sb-item {{ request()->routeIs('pos.poscheck') ? 'active' : '' }}"
            data-tip="POS Checkout">
            <i class="fas fa-cash-register"></i>
            <span class="sb-item-text">POS Checkout</span>
        </a>
        <a href="{{ route('pos.inventory') }}" class="sb-item {{ request()->routeIs('pos.inventory') ? 'active' : '' }}"
            data-tip="Inventory">
            <i class="fas fa-boxes-stacked"></i>
            <span class="sb-item-text">Inventory</span>
        </a>
        <a href="{{ route('pos.customers.page') }}"
            class="sb-item {{ request()->routeIs('pos.customers.page') ? 'active' : '' }}" data-tip="Customers">
            <i class="fas fa-users"></i>
            <span class="sb-item-text">Customers</span>
        </a>
        <a href="{{ route('pos.suppliers.page') }}"
            class="sb-item {{ request()->routeIs('pos.suppliers.page') ? 'active' : '' }}" data-tip="Suppliers">
            <i class="fas fa-truck"></i>
            <span class="sb-item-text">Suppliers</span>
        </a>
        <a href="{{ route('pos.sales.page') }}"
            class="sb-item {{ request()->routeIs('pos.sales.page') ? 'active' : '' }}" data-tip="Sales History">
            <i class="fas fa-receipt"></i>
            <span class="sb-item-text">Sales History</span>
        </a>
        <a href="{{ route('pos.reports') }}" class="sb-item {{ request()->routeIs('pos.reports') ? 'active' : '' }}"
            data-tip="Reports">
            <i class="fas fa-chart-bar"></i>
            <span class="sb-item-text">Reports</span>
        </a>

        {{-- Operations section --}}
        <div class="sb-section-label" style="margin-top:.5rem">
            <span class="sb-label-text">Operations</span>
            </div>
        <a href="{{ route('shift.open.form') }}" class="sb-item {{ request()->routeIs('shift.*') ? 'active' : '' }}"
            data-tip="Shifts">
            <i class="fas fa-clock"></i>
            <span class="sb-item-text">Shifts</span>
        </a>

        {{-- System section --}}
        <div class="sb-section-label" style="margin-top:.5rem">
            <span class="sb-label-text">Operations</span>
            </div>
        <a href="{{ route('pos.users.page') }}"
            class="sb-item {{ request()->routeIs('pos.users.*') ? 'active' : '' }}" data-tip="Users">
            <i class="fas fa-users-gear"></i>
            <span class="sb-item-text">User Management</span>
        </a>
        <a href="{{ route('pos.backup') }}" class="sb-item {{ request()->routeIs('pos.backup*') ? 'active' : '' }}"
            data-tip="Backup & Sync">
            <i class="fas fa-cloud-arrow-up"></i>
            <span class="sb-item-text">Backup & Sync</span>
        </a>
        <a href="#" class="sb-item" data-tip="Settings">
            <i class="fas fa-gear"></i>
            <span class="sb-item-text">Settings</span>
        </a>

    </nav>

    {{-- Lock / logout --}}
    <div class="sb-foot">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full" style="padding:0;border:none;background:none;cursor:pointer">
                <a href="#" onclick="this.closest('form').submit(); return false;"
                    class="flex items-center justify-center gap-2 p-2 rounded-lg border border-gray-200 text-sm font-medium text-gray-600 hover:bg-red-50 hover:border-red-200 hover:text-red-600 transition-all"
                    style="text-decoration:none">
                    <i class="fas fa-right-from-bracket text-xs"></i>
                    <span class="sb-foot-text">Logout</span>
                </a>
            </button>
        </form>
    </div>

</aside>
