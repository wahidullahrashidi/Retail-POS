<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('pos:repair-view-cache', function () {
    $viewsPath = storage_path('framework/views');

    File::ensureDirectoryExists($viewsPath);
    File::ensureDirectoryExists(config('view.compiled', $viewsPath));
    File::ensureDirectoryExists(base_path('bootstrap/cache'));

    $removed = 0;
    foreach (File::glob($viewsPath . DIRECTORY_SEPARATOR . '*.tmp') ?: [] as $tmpFile) {
        if (File::lastModified($tmpFile) < now()->subMinutes(10)->timestamp) {
            File::delete($tmpFile);
            $removed++;
        }
    }

    Artisan::call('view:clear');

    $this->info("View cache repaired. Removed {$removed} stale temporary file(s).");
})->purpose('Clear compiled Blade views and stale Windows temp files safely');

Schedule::command('backup:run --only-db')->dailyAt('02:00');
Schedule::command('backup:run')->weeklyOn(5, '03:00');
Schedule::command('backup:clean')->weekly();
