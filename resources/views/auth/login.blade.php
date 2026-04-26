<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Afghan POS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: system-ui, sans-serif; }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-500 to-purple-600 min-h-screen flex items-center justify-center">

    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-8">
        <div class="text-center mb-8">
            <div class="text-5xl mb-3">🛒</div>
            <h1 class="text-2xl font-bold text-gray-800">Afghan POS</h1>
            <p class="text-gray-500">Retail Management System</p>
        </div>

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6 text-center">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                <input type="text" name="username" required autofocus
                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none text-lg"
                    placeholder="Enter username">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                <input type="password" name="password" required
                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none text-lg"
                    placeholder="Enter password">
            </div>

            <button type="submit" 
                class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 rounded-lg transition duration-200">
                Login
            </button>
        </form>

        <div class="relative my-6">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-300"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="px-2 bg-white text-gray-500">OR</span>
            </div>
        </div>

        <form action="{{ route('login.pin') }}" method="POST" class="text-center">
            @csrf
            
            <label class="block text-sm font-medium text-gray-700 mb-3">Cashier PIN Code</label>
            <div class="flex justify-center gap-3 mb-5">
                <input type="password" name="pin1" maxlength="1" 
                    class="w-14 h-14 text-center text-2xl border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none"
                    onkeyup="moveToNext(this, 'pin2')">
                <input type="password" name="pin2" id="pin2" maxlength="1"
                    class="w-14 h-14 text-center text-2xl border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none"
                    onkeyup="moveToNext(this, 'pin3')">
                <input type="password" name="pin3" id="pin3" maxlength="1"
                    class="w-14 h-14 text-center text-2xl border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none"
                    onkeyup="moveToNext(this, 'pin4')">
                <input type="password" name="pin4" id="pin4" maxlength="1"
                    class="w-14 h-14 text-center text-2xl border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none">
            </div>

            <input type="hidden" name="pin_code" id="fullPin">

            <button type="submit" onclick="combinePin()"
                class="w-full bg-purple-500 hover:bg-purple-600 text-white font-bold py-3 rounded-lg transition duration-200">
                Login with PIN
            </button>
        </form>

        <div class="mt-6 text-center">
            <select class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                <option value="en">🇬🇧 English</option>
                <option value="ps">🇦🇫 پښتو</option>
                <option value="dr">🇦🇫 دری</option>
            </select>
        </div>
    </div>

    <script>
        function moveToNext(current, nextId) {
            if (current.value.length === 1) {
                document.getElementById(nextId).focus();
            }
        }

        function combinePin() {
            const pin = document.getElementsByName('pin1')[0].value +
                       document.getElementsByName('pin2')[0].value +
                       document.getElementsByName('pin3')[0].value +
                       document.getElementsByName('pin4')[0].value;
            document.getElementById('fullPin').value = pin;
        }
    </script>
</body>
</html>