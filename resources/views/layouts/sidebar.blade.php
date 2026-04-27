<!-- Sidebar -->
<aside id="sidebar"
    class="w-64 transition-all duration-300 bg-white border-r border-gray-200 flex flex-col flex-shrink-0">

    <!-- Logo -->
    <div class="p-5 border-b border-gray-100">
        <div class="flex items-center gap-3">
            <div id="logo-icon" class="w-9 h-9 bg-primary rounded-lg flex items-center justify-center text-white">
                <i class="fas fa-heartbeat text-sm"></i>
            </div>
            <span id="logo-text" class="font-bold text-lg text-gray-900">Afghan Retail</span>
            <button id="side_btn" class="ml-auto text-gray-400 hover:text-gray-600">
                {{-- <i class="fas fa-chevron-left text-xs"></i> --}}
                <i class="fas fa-chevron-left text-xs transition-transform duration-300"></i>
                {{-- <i class="fas fa-chevron-left text-xs transition-transform duration-300"></i> --}}
            </button>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto py-4">
        <div class="px-4 mb-2">
            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Store</span>
        </div>
        <ul class="space-y-1 px-2">
            <li>
                <a href="{{ route('pos.dashboard') }}"
                    class="sidebar-item w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700">
                    <i class="fas fa-th-large w-5 text-center"></i>
                    <span class="sidebar-text">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="{{ route('pos.search') }}"
                    class="sidebar-item w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700">
                    <i class="fas fa-shopping-cart w-5 text-center"></i>
                    <span class="sidebar-text">POS Checkout</span>
                </a>
            </li>
            <li>
                <a href="{{ route('pos.search') }}"
                    class="sidebar-item w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700">
                    <i class="fas fa-cube w-5 text-center"></i>
                    <span class="sidebar-text">Inventory</span>
                </a>
            </li>
            <li>
                <a href="{{ route('pos.search') }}"
                    class="sidebar-item w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700">
                    <i class="fas fa-users w-5 text-center"></i>
                    <span class="sidebar-text">Customers</span>
                </a>
            </li>
            <li>
                <a href="{{ route('pos.search') }}"
                    class="sidebar-item w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700">
                    <i class="fas fa-truck w-5 text-center"></i>
                    <span class="sidebar-text">Suppliers</span>
                </a>
            </li>
            <li>
                <a href="{{ route('pos.search') }}"
                    class="sidebar-item w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700">
                    <i class="fas fa-clock-rotate-left w-5 text-center"></i>
                    <span class="sidebar-text">Sales History</span>
                </a>
            </li>
            <li>
                <a href="{{ route('pos.search') }}"
                    class="sidebar-item w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700">
                    <i class="fas fa-clock w-5 text-center"></i>
                    <span class="sidebar-text">Shifts</span>
                </a>
            </li>
            <li>
                <a href="{{ route('pos.search') }}"
                    class="sidebar-item w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700">
                    <i class="fas fa-chart-bar w-5 text-center"></i>
                    <span class="sidebar-text">Reports</span>
                </a>
            </li>
        </ul>

        <div class="px-4 mt-6 mb-2">
            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">System</span>
        </div>
        <ul class="space-y-1 px-2">
            <li>
                <a href="{{ route('pos.search') }}"
                    class="sidebar-item w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700">
                    <i class="fas fa-microchip w-5 text-center"></i>
                    <span class="sidebar-text">Hardware</span>
                </a>
            </li>
            <li>
                <a href="{{ route('pos.search') }}"
                    class="sidebar-item w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700">
                    <i class="fas fa-cloud-arrow-up w-5 text-center"></i>
                    <span class="sidebar-text">Sync & Backup</span>
                </a>
            </li>
            <li>
                <a href="{{ route('pos.search') }}"
                    class="sidebar-item w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700">
                    <i class="fas fa-gear w-5 text-center"></i>
                    <span class="sidebar-text">Settings</span>
                </a>
            </li>
        </ul>
    </nav>

    <!-- Lock Screen -->
    <div class="p-4 border-t border-gray-200">
        <a href="{{ route('pos.search') }}"
            class="w-full flex items-center justify-center gap-2 px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
            <i class="fas fa-lock text-xs"></i>
            <span class="sidebar-text">Lock Screen</span>
        </a>
        {{-- <button
            class="w-full flex items-center justify-center gap-2 px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
            <i class="fas fa-lock text-xs"></i>
            Lock Screen
        </button> --}}
    </div>
</aside>
<script>
const sidebar = document.getElementById('sidebar');
const sideBtn = document.getElementById('side_btn');
const sideIcon = sideBtn.querySelector('i');

const sidebarTexts = document.querySelectorAll('.sidebar-text');
const sectionTitles = document.querySelectorAll('nav span.text-xs');
const logoText = document.getElementById('logo-text');

sideBtn.addEventListener('click', function (e) {
    e.preventDefault();
    e.stopPropagation();

    if (sidebar.classList.contains('w-64')) {
        // Collapse
        sidebar.classList.remove('w-64');
        sidebar.classList.add('w-20');

        sidebarTexts.forEach(el => el.classList.add('hidden'));
        sectionTitles.forEach(el => el.classList.add('hidden'));
        logoText.classList.add('hidden');

        sideIcon.classList.add('rotate-180');

    } else {
        // Expand
        sidebar.classList.remove('w-20');
        sidebar.classList.add('w-64');

        sidebarTexts.forEach(el => el.classList.remove('hidden'));
        sectionTitles.forEach(el => el.classList.remove('hidden'));
        logoText.classList.remove('hidden');

        sideIcon.classList.remove('rotate-180');
    }
});
</script>
