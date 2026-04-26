@extends('layouts.app')

@section('content')
    <!-- CUSTOMERS VIEW -->
    <div id="view-customers" class="view-section hidden">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Customers</h1>
                <p class="text-sm text-gray-500 mt-1">Manage customer accounts and loan profiles.</p>
            </div>
            <button
                class="flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:bg-primary-dark transition-colors">
                <i class="fas fa-plus"></i>
                New Customer
            </button>
        </div>
        <div class="bg-white rounded-xl border border-card-border p-12 text-center">
            <div class="w-20 h-20 bg-indigo-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-users text-3xl text-primary"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Customer Management</h3>
            <p class="text-gray-500 max-w-md mx-auto">This is the Customers view. Here you would manage customer profiles,
                view purchase history, handle loan accounts, and manage credit limits.</p>
        </div>
    </div>
@endsection
