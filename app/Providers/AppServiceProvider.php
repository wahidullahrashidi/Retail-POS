<?php

namespace App\Providers;

use App\Http\Controllers\AppLayoutController;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        AppLayoutController::shareLayoutData();
        App::setLocale(session('locale', 'en'));
    }
}
