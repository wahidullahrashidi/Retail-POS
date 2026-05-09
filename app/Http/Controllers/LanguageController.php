<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;

class LanguageController extends Controller
{
    public function switch(string $lang): RedirectResponse
    {
        $allowed = ['en', 'ps', 'dr'];

        if (! in_array($lang, $allowed, true)) {
            $lang = 'en';
        }

        session(['app_locale' => $lang]);
        App::setLocale($lang);

        return redirect()->back()->withCookie(cookie('app_locale', $lang, 60 * 24 * 365));
    }
}
