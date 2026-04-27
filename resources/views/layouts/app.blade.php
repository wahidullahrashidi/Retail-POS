<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Afghan Retail - System Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
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
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .sidebar-item {
            transition: all 0.2s ease;
        }

        .sidebar-item:hover {
            background-color: #f1f5f9;
        }

        .sidebar-item.active {
            background-color: #eef2ff;
            color: #4f46e5;
            border-right: 3px solid #4f46e5;
        }

        .stat-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .progress-bar {
            transition: width 0.5s ease;
        }

        .view-section {
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .quick-action-card {
            transition: all 0.2s ease;
        }

        .quick-action-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .transaction-row {
            transition: background-color 0.15s ease;
        }

        .transaction-row:hover {
            background-color: #f8fafc;
        }

        .status-dot {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        .checkout-btn {
            transition: all 0.2s ease;
        }

        .checkout-btn:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4);
        }

        .search-input:focus {
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

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

</html>
