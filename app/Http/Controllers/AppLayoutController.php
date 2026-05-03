<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\View;
use Morilog\Jalali\Jalalian;
use Carbon\Carbon;

class AppLayoutController extends Controller
{
    /**
     * Share layout data with ALL views that extend layouts.app
     * Register this in App\Providers\AppServiceProvider::boot()
     */
    public static function shareLayoutData(): void
{
    View::composer('layouts.app', function ($view) {
        $pashtoMonths = [
            1=>'وری', 2=>'غویی', 3=>'غبرګولی', 4=>'چنګاښ',
            5=>'زمری', 6=>'وږی', 7=>'تله', 8=>'لړم',
            9=>'لیندۍ', 10=>'مرغومی', 11=>'سلواغه', 12=>'کب',
        ];

        try {
            $jalali = \Morilog\Jalali\Jalalian::fromDateTime(now());

            // Jalali date with Arabic numerals
            $jalaliDate = self::toArabicNumerals(
                $jalali->getDay() . ' ' . ($pashtoMonths[$jalali->getMonth()] ?? '') . ' ' . $jalali->getYear()
            );

            // Original Gregorian date
            $gregorianDate = Carbon::now()->format('F d, Y'); // e.g. 2026-05-03
        } catch (\Exception $e) {
            $jalaliDate = '';
            $gregorianDate = '';
        }

        $view->with([
            'jalaliDate'    => $jalaliDate,
            'gregorianDate' => $gregorianDate,
        ]);
    });
}


    private static function toArabicNumerals(string $number): string
    {
        $western = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $arabic  = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        return str_replace($western, $arabic, $number);
    }
}
