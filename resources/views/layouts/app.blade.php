<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Afghan Retail POS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Instrument+Serif:ital@0;1&family=DM+Sans:wght@300;400;500;600&display=swap"
        rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Crimson+Pro:ital,wght@0,400;0,600;1,400&family=Figtree:wght@300;400;500;600;700&family=Fira+Code:wght@400;500&display=swap"
        rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,500;0,600;1,400&family=Nunito:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap"
        rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: '#6366f1',
                        'primary-dark': '#4f46e5',
                        'primary-light': '#818cf8',
                        success: '#10b981',
                        danger: '#ef4444',
                        warning: '#f59e0b',
                        'sidebar-bg': '#f8fafc',
                        'card-border': '#e2e8f0',
                    }
                }
            }
        }
    </script>
    @vite(['resources/css/layout/theme.css'])

    @stack('styles')
</head>
<body>

<body class="bg-gray-50 text-gray-800 h-screen overflow-hidden flex flex-col">

    @include('layouts.header')

    <div class="flex flex-1 overflow-hidden min-h-0">
        @include('layouts.sidebar')
        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0 min-h-0">


            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto p-6">
                @yield('content')
            </main>

        </div>
    </div>

    @include('layouts.footer')
</body>
@stack('scripts')

</html>