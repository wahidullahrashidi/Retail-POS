@include('layouts.app')

@section('content')
     <!-- SETTINGS VIEW -->
                <div id="view-settings" class="view-section hidden">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Settings</h1>
                            <p class="text-sm text-gray-500 mt-1">Configure system preferences and store details.</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="bg-white rounded-xl border border-card-border p-5 cursor-pointer hover:shadow-md transition-shadow">
                            <div class="w-10 h-10 bg-indigo-50 rounded-lg flex items-center justify-center text-primary mb-3">
                                <i class="fas fa-store"></i>
                            </div>
                            <div class="font-semibold text-gray-900">Store Settings</div>
                            <div class="text-xs text-gray-500 mt-1">Branch info, tax rates, currency</div>
                        </div>
                        <div class="bg-white rounded-xl border border-card-border p-5 cursor-pointer hover:shadow-md transition-shadow">
                            <div class="w-10 h-10 bg-indigo-50 rounded-lg flex items-center justify-center text-primary mb-3">
                                <i class="fas fa-users-gear"></i>
                            </div>
                            <div class="font-semibold text-gray-900">User Management</div>
                            <div class="text-xs text-gray-500 mt-1">Staff accounts and permissions</div>
                        </div>
                        <div class="bg-white rounded-xl border border-card-border p-5 cursor-pointer hover:shadow-md transition-shadow">
                            <div class="w-10 h-10 bg-indigo-50 rounded-lg flex items-center justify-center text-primary mb-3">
                                <i class="fas fa-receipt"></i>
                            </div>
                            <div class="font-semibold text-gray-900">Receipt Settings</div>
                            <div class="text-xs text-gray-500 mt-1">Template, logo, footer text</div>
                        </div>
                        <div class="bg-white rounded-xl border border-card-border p-5 cursor-pointer hover:shadow-md transition-shadow">
                            <div class="w-10 h-10 bg-indigo-50 rounded-lg flex items-center justify-center text-primary mb-3">
                                <i class="fas fa-bell"></i>
                            </div>
                            <div class="font-semibold text-gray-900">Notifications</div>
                            <div class="text-xs text-gray-500 mt-1">Alerts, email, SMS settings</div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl border border-card-border p-12 text-center">
                        <div class="w-20 h-20 bg-indigo-50 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-gear text-3xl text-primary"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">System Settings</h3>
                        <p class="text-gray-500 max-w-md mx-auto">This is the Settings view. Here you would configure all system preferences including store details, user permissions, receipt templates, tax settings, and notification preferences.</p>
                    </div>
                </div>
@endsection