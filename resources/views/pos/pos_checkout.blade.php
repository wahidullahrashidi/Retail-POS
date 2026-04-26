@include('layouts.app')

@section('content')
    <!-- POS CHECKOUT VIEW -->
    <div id="view-pos" class="view-section hidden">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">POS Checkout</h1>
                <p class="text-sm text-gray-500 mt-1">Process sales and manage transactions.</p>
            </div>
            <button
                class="flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:bg-primary-dark transition-colors">
                <i class="fas fa-plus"></i>
                New Sale
            </button>
        </div>
        <div class="bg-white rounded-xl border border-card-border p-12 text-center">
            <div class="w-20 h-20 bg-indigo-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-shopping-cart text-3xl text-primary"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">POS Checkout Module</h3>
            <p class="text-gray-500 max-w-md mx-auto">This is the POS Checkout view. Here you would have
                the full point-of-sale interface for scanning items, applying discounts, and processing
                payments.</p>
        </div>
    </div>
@endsection
