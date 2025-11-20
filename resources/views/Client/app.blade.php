<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Client Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        /* Desktop Sidebar - Always visible */
        @media (min-width: 1024px) {
            .sidebar-desktop {
                display: flex;
                position: sticky;
                top: 0;
                height: 100vh;
                overflow-y: auto;
            }

            .sidebar-mobile {
                display: none;
            }
        }

        /* Mobile Sidebar - Hidden by default, slides in when open */
        @media (max-width: 1024px) {
            .sidebar-desktop {
                display: none;
            }

            .sidebar-mobile {
                display: flex;
                position: fixed;
                top: 0;
                left: 0;
                height: 100vh;
                width: 16rem;
                background-color: white;
                border-right: 1px solid #e5e7eb;
                z-index: 50;
                transform: translateX(-100%);
                transition: transform 0.3s ease-in-out;
            }

            .sidebar-mobile.open {
                transform: translateX(0);
            }
        }

        /* Overlay */
        #overlay {
            transition: opacity 0.3s ease-in-out;
        }

        .active-nav {
            background: #f0f9ff;
            color: #0ea5e9;
            border-right: 3px solid #0ea5e9;
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen">

    <!-- Mobile Overlay -->
    <div id="overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden"></div>

    <div class="flex min-h-screen">
        <!-- Desktop Sidebar (Always visible on desktop) -->
        <div class="sidebar-desktop lg:!flex hidden w-64 bg-white border-r border-gray-200 flex-col">
            <!-- Logo -->
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-primary-500 rounded-lg flex items-center justify-center">
                        <i class="fas fa-rocket text-white"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">Client Portal</h1>
                        <p class="text-sm text-gray-500">Dashboard</p>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 py-6 px-4">
                <div class="space-y-2">
                    <a href="{{ route('client.dashboard') }}"
                        class="flex items-center space-x-3 py-3 px-4 rounded-lg transition-all duration-200 {{ request()->routeIs('client.dashboard') ? 'active-nav' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-chart-pie w-5 text-center"></i>
                        <span class="font-medium">Dashboard</span>
                    </a>
                    <a href="{{ route('client.projects') }}"
                        class="flex items-center space-x-3 py-3 px-4 rounded-lg transition-all duration-200 {{ request()->routeIs('client.projects*') ? 'active-nav' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-briefcase w-5 text-center"></i>
                        <span class="font-medium">Projects</span>
                    </a>
                </div>
            </nav>

            <!-- User Section -->
            <div class="p-4 border-t border-gray-200">
                <div class="flex items-center space-x-3">
                    <div
                        class="w-10 h-10 bg-primary-500 rounded-full flex items-center justify-center text-white font-semibold">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500 truncate">Client</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-gray-400 hover:text-red-500 transition-colors duration-200">
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-h-screen">
            <!-- Header -->
            <header class="bg-white border-b border-gray-200 sticky top-0 z-30">
                <div class="flex items-center justify-between px-6 py-4">
                    <!-- Mobile Menu Button -->
                    <button id="menuToggle" class="lg:hidden block">
                        <i class="fas fa-bars text-gray-700 text-lg"></i>
                    </button>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">
                            @yield('title', 'Dashboard')
                        </h1>
                        <p class="text-sm text-gray-600 mt-1">
                            Welcome back, {{ auth()->user()->name }}
                        </p>
                    </div>
                </div>
            </header>

            <!-- Content Area -->
            <main class="flex-1 p-6">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Mobile Sidebar (Slides in from left) -->
    <div id="sidebar-mobile" class="sidebar-mobile flex-col">
        <!-- Logo -->
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-primary-500 rounded-lg flex items-center justify-center">
                    <i class="fas fa-rocket text-white"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-gray-900">Client Portal</h1>
                    <p class="text-sm text-gray-500">Dashboard</p>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 py-6 px-4 overflow-y-auto">
            <div class="space-y-2">
                <a href="{{ route('client.dashboard') }}"
                    class="flex items-center space-x-3 py-3 px-4 rounded-lg transition-all duration-200 {{ request()->routeIs('client.dashboard') ? 'active-nav' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fas fa-chart-pie w-5 text-center"></i>
                    <span class="font-medium">Dashboard</span>
                </a>
                <a href="{{ route('client.projects') }}"
                    class="flex items-center space-x-3 py-3 px-4 rounded-lg transition-all duration-200 {{ request()->routeIs('client.projects*') ? 'active-nav' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fas fa-briefcase w-5 text-center"></i>
                    <span class="font-medium">Projects</span>
                </a>
            </div>
        </nav>

        <!-- User Section -->
        <div class="p-4 border-t border-gray-200">
            <div class="flex items-center space-x-3">
                <div
                    class="w-10 h-10 bg-primary-500 rounded-full flex items-center justify-center text-white font-semibold">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-500 truncate">Client</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-gray-400 hover:text-red-500 transition-colors duration-200">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session('success'))
        <div
            class="fixed bottom-6 right-6 bg-white text-gray-900 px-6 py-4 rounded-lg shadow-xl border-l-4 border-green-500 z-50">
            <div class="flex items-center space-x-3">
                <i class="fas fa-check-circle text-green-500 text-xl"></i>
                <div>
                    <p class="font-semibold">Success</p>
                    <p class="text-gray-600 text-sm">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div
            class="fixed bottom-6 right-6 bg-white text-gray-900 px-6 py-4 rounded-lg shadow-xl border-l-4 border-red-500 z-50">
            <div class="flex items-center space-x-3">
                <i class="fas fa-exclamation-circle text-red-500 text-xl"></i>
                <div>
                    <p class="font-semibold">Error</p>
                    <p class="text-gray-600 text-sm">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <script>
        // Mobile menu toggle
        const menuToggle = document.getElementById('menuToggle');
        const sidebarMobile = document.getElementById('sidebar-mobile');
        const overlay = document.getElementById('overlay');

        if (menuToggle && sidebarMobile && overlay) {
            menuToggle.addEventListener('click', () => {
                sidebarMobile.classList.toggle('open');
                overlay.classList.toggle('hidden');
            });

            overlay.addEventListener('click', () => {
                sidebarMobile.classList.remove('open');
                overlay.classList.add('hidden');
            });

            // Close sidebar when clicking on links in mobile
            const navLinks = sidebarMobile.querySelectorAll('nav a');
            navLinks.forEach(link => {
                link.addEventListener('click', () => {
                    sidebarMobile.classList.remove('open');
                    overlay.classList.add('hidden');
                });
            });
        }

        // Auto-hide flash messages
        const flashMessages = document.querySelectorAll('[class*="fixed bottom-6"]');
        flashMessages.forEach(message => {
            setTimeout(() => {
                message.style.opacity = '0';
                message.style.transition = 'opacity 0.5s ease';
                setTimeout(() => message.remove(), 500);
            }, 5000);
        });
    </script>
</body>

</html>
