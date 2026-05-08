<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function switch(string $lang)
    {
        $allowed = ['en', 'ps', 'dr'];

        if (!in_array($lang, $allowed)) {
            $lang = 'en';
        }

        session(['app_locale' => $lang]);

        return redirect()->back();
    }
}
