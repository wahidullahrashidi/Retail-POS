<?php
use Carbon\Carbon;
use Morilog\Jalali\Jalalian;

if (!function_exists('pashtoDate')) {
    function pashtoDate($date)
    {
        // Pashto month names for Solar Hijri
        $pashtoMonths = [
            1 => 'وری', // Wray (Sawr)
            2 => 'غویی', // Ghway
            3 => 'غبرګولی',
            4 => 'چنګاښ',
            5 => 'زمری',
            6 => 'وږی',
            7 => 'تله',
            8 => 'لړم',
            9 => 'لیندۍ',
            10 => 'مرغومی',
            11 => 'سلواغه',
            12 => 'کب',
        ];

        $jalali = Jalalian::fromDateTime($date);
        $day = $jalali->getDay();
        $month = $pashtoMonths[$jalali->getMonth()];
        $year = $jalali->getYear();

        return "{$year} {$day} {$month}";
    }
}

$today = now();
$hijritedDate = pashtoDate($today);

// Get current date and format it
$formattedDate = Carbon::now()->format('F d, Y');
?>

<!-- Header -->
<header class="bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between">
    <div class="flex items-center gap-4">
        <div class="text-sm text-gray-500">
            <div class="font-medium text-gray-900">{{ $hijritedDate }}</div>
            <div class="text-xs">{{ $formattedDate }}</div>
        </div>
        <div class="relative">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            {{-- form for seach --}}
            <form action="{{ url()->current() }}" method="get">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search products, customers, barcodes..."
                    class="w-80 pl-9 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
            </form>
        </div>
    </div>
    <div class="flex items-center gap-4">
        <span
            class="px-3 py-1 bg-indigo-50 text-primary text-xs font-semibold rounded-full border border-indigo-100">{{ Auth()->user()->name }}</span>
        <button class="w-8 h-8 flex items-center justify-center text-gray-500 hover:text-gray-700">
            <i class="fas fa-globe"></i>
        </button>
        <span class="text-sm font-medium text-gray-700">EN</span>
        <button class="relative w-8 h-8 flex items-center justify-center text-gray-500 hover:text-gray-700">
            <i class="fas fa-bell"></i>
            <span class="absolute top-1 right-1 w-2 h-2 bg-danger rounded-full"></span>
        </button>
        <div class="flex items-center gap-2">
            <img src="https://ui-avatars.com/api/?name=Manager&background=6366f1&color=fff&size=32" alt="User"
                class="w-8 h-8 rounded-full">
            <div class="w-2.5 h-2.5 bg-success rounded-full border-2 border-white absolute ml-6 mt-5"></div>
        </div>
    </div>
</header>
