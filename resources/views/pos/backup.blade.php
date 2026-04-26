@include('layouts.app')

@section('content')
    <!-- SYNC & BACKUP VIEW -->
                <div id="view-sync" class="view-section hidden">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Sync & Backup</h1>
                            <p class="text-sm text-gray-500 mt-1">Manage data synchronization and backups.</p>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl border border-card-border p-12 text-center">
                        <div class="w-20 h-20 bg-indigo-50 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-cloud-arrow-up text-3xl text-primary"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Sync & Backup Center</h3>
                        <p class="text-gray-500 max-w-md mx-auto">This is the Sync & Backup view. Here you would configure automatic cloud backups, manage sync schedules, restore from backups, and monitor sync status across branches.</p>
                    </div>
                </div>
@endsection