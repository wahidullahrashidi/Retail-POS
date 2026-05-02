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
        {{-- logout button --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-md text-sm font-medium transition">
                Logout
            </button>
        </form>
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
            @php
                $parts = array_values(array_filter(explode(' ', trim(Auth()->user()->name))));
                $initials =
                    count($parts) === 1
                        ? strtoupper(substr($parts[0], 0, 2))
                        : strtoupper(collect($parts)->map(fn($p) => substr($p, 0, 1))->join(''));
            @endphp
            <!-- Assuming you are using Laravel's Auth facade -->
            <img src="{{ asset('storage/' . Auth::user()->photo) }}" alt="{{ $initials }}" class="w-8 h-8 rounded-full">

            <div class="w-2.5 h-2.5 bg-success rounded-full border-2 border-white absolute ml-6 mt-5"></div>
        </div>
    </div>
</header>
