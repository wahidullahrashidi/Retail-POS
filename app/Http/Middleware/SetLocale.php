<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $locale = session('app_locale', 'en');

        // Map your locale keys to Laravel locale folders
        $map = [
            'en' => 'en',
            'ps' => 'ps',
            'dr' => 'dr',
        ];

        App::setLocale($map[$locale] ?? 'en');

        return $next($request);
    }
}