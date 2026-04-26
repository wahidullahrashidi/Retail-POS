<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Open Shift - Afghan POS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-500 to-purple-600 min-h-screen flex items-center justify-center">

    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-8">
        <div class="text-center mb-8">
            <div class="text-5xl mb-3">🌅</div>
            <h1 class="text-2xl font-bold text-gray-800">Open Shift</h1>
            <p class="text-gray-500">Cashier: {{ auth()->user()->name }}</p>
            <p class="text-sm text-gray-400">{{ now()->format('Y-m-d H:i') }}</p>
        </div>

        <form action="{{ route('shift.open') }}" method="POST">
            @csrf
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Starting Cash Amount</label>
                <div class="relative">
                    <input type="number" name="starting_cash" required autofocus min="0" step="0.01"
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none text-lg text-center"
                        placeholder="0.00">
                    <span class="absolute right-4 top-3 text-gray-500 text-lg">؋</span>
                </div>
                <p class="text-xs text-gray-500 mt-2">Enter the cash amount in the drawer at start of shift</p>
            </div>

            <button type="submit" 
                class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 rounded-lg transition duration-200">
                Open Shift & Start Selling
            </button>
        </form>
    </div>

</body>
</html>