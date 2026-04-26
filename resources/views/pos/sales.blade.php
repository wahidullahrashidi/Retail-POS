@include('layouts.app')

@section('content')
    <!-- SALES HISTORY VIEW -->
    <div id="view-sales" class="view-section">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Sales History</h1>
                <p class="text-sm text-gray-500 mt-1">View and analyze all past transactions.</p>
            </div>
            <div class="flex gap-2">
                <button
                    class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                    <i class="fas fa-filter"></i>
                    Filter
                </button>
                <button
                    class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                    <i class="fas fa-download"></i>
                    Export
                </button>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-card-border p-12 text-center">
            <div class="w-20 h-20 bg-indigo-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-clock-rotate-left text-3xl text-primary"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Sales History</h3>
            <p class="text-gray-500 max-w-md mx-auto">This is the Sales History view. Here you would have a complete log of
                all transactions with advanced filtering, search, date range selection, and export capabilities.</p>
        </div>
    </div>
@endsection
