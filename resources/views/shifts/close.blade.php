<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Close Shift - Afghan POS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-orange-500 to-red-600 min-h-screen flex items-center justify-center">

    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-8">
        <div class="text-center mb-8">
            <div class="text-5xl mb-3">🌙</div>
            <h1 class="text-2xl font-bold text-gray-800">Close Shift</h1>
            <p class="text-gray-500">Shift started: {{ $shift->opened_at->format('H:i') }}</p>
        </div>

        <div class="bg-gray-100 rounded-lg p-4 mb-6">
            <div class="flex justify-between mb-2">
                <span class="text-gray-600">Starting Cash:</span>
                <span class="font-medium">{{ number_format($shift->starting_cash, 2) }} ؋</span>
            </div>
            <div class="flex justify-between mb-2">
                <span class="text-gray-600">Expected Cash:</span>
                <span class="font-medium text-blue-600">{{ number_format($expectedCash, 2) }} ؋</span>
            </div>
            <div class="border-t pt-2 mt-2">
                <div class="flex justify-between">
                    <span class="text-gray-800 font-medium">Cash Sales (Today):</span>
                    <span class="font-bold text-green-600">{{ number_format($expectedCash - $shift->starting_cash, 2) }} ؋</span>
                </div>
            </div>
        </div>

        <form action="{{ route('shift.close') }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Actual Cash Counted</label>
                <div class="relative">
                    <input type="number" name="actual_cash" required min="0" step="0.01"
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-orange-500 focus:outline-none text-lg text-center"
                        placeholder="0.00">
                    <span class="absolute right-4 top-3 text-gray-500 text-lg">؋</span>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Discrepancy Note (if any)</label>
                <textarea name="discrepancy_note" rows="2"
                    class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:border-orange-500 focus:outline-none"
                    placeholder="Explain any difference..."></textarea>
            </div>

            <div class="flex gap-3">
                <a href="{{ route('pos.index') }}" 
                    class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 font-bold py-3 rounded-lg text-center transition">
                    Cancel
                </a>
                <button type="submit" 
                    class="flex-1 bg-red-500 hover:bg-red-600 text-white font-bold py-3 rounded-lg transition">
                    Close Shift
                </button>
            </div>
        </form>
    </div>

</body>
</html>