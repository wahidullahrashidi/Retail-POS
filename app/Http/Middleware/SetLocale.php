<?php
namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    private const SUPPORTED = ['en', 'ps', 'dr', 'fa'];

    public function handle(Request $request, Closure $next)
    {
        $locale = $request->session()->get('app_locale')
            ?? $request->cookie('app_locale')
            ?? $this->settingsLocale()
            ?? config('app.locale', 'en');

        if (! in_array($locale, self::SUPPORTED, true)) {
            $locale = config('app.fallback_locale', 'en');
        }

        App::setLocale($locale);
        Session::put('app_locale', $locale);
        Cookie::queue('app_locale', $locale, 60 * 24 * 365);

        return $next($request);
    }

    private function settingsLocale(): ?string
    {
        try {
            return Setting::get('default_language');
        } catch (\Throwable) {
            return null;
        }
    }
}
