@extends('layouts.app')

@section('content')
    <!-- REPORTS VIEW -->
    <div id="view-reports" class="view-section">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Reports</h1>
                <p class="text-sm text-gray-500 mt-1">Generate business insights and analytics.</p>
            </div>
        </div>
        <div class="grid grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-xl border border-card-border p-5 cursor-pointer hover:shadow-md transition-shadow">
                <div class="w-10 h-10 bg-indigo-50 rounded-lg flex items-center justify-center text-primary mb-3">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="font-semibold text-gray-900">Sales Report</div>
                <div class="text-xs text-gray-500 mt-1">Daily, weekly, monthly sales</div>
            </div>
            <div class="bg-white rounded-xl border border-card-border p-5 cursor-pointer hover:shadow-md transition-shadow">
                <div class="w-10 h-10 bg-indigo-50 rounded-lg flex items-center justify-center text-primary mb-3">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <div class="font-semibold text-gray-900">Profit Analysis</div>
                <div class="text-xs text-gray-500 mt-1">Margin and cost breakdown</div>
            </div>
            <div class="bg-white rounded-xl border border-card-border p-5 cursor-pointer hover:shadow-md transition-shadow">
                <div class="w-10 h-10 bg-indigo-50 rounded-lg flex items-center justify-center text-primary mb-3">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <div class="font-semibold text-gray-900">Inventory Report</div>
                <div class="text-xs text-gray-500 mt-1">Stock movement and valuation</div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-card-border p-12 text-center">
            <div class="w-20 h-20 bg-indigo-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-chart-bar text-3xl text-primary"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Reports Center</h3>
            <p class="text-gray-500 max-w-md mx-auto">This is the Reports view. Here you would generate comprehensive
                business reports with charts, export to PDF/Excel, and schedule automated report delivery.</p>
        </div>
    </div>
@endsection
