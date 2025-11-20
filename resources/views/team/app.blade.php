<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Dashboard</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

       <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        .sidebar-scroll::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar-scroll::-webkit-scrollbar-track {
            background: #f8fafc;
        }

        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 8px;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .nav-item {
            transition: all 0.3s ease;
        }

        .nav-item:hover {
            transform: translateX(5px);
        }

        /* Mobile sidebar styles */
        .sidebar-mobile {
            transform: translateX(-100%);
            transition: transform 0.3s ease-in-out;
        }

        .sidebar-mobile.open {
            transform: translateX(0);
        }

        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 40;
        }

        .overlay.active {
            display: block;
        }

        /* Desktop sidebar styles */
        @media (min-width: 1024px) {
            .sidebar-desktop {
                transform: translateX(0);
            }
        }
    </style>
</head>

<body class="bg-gradient-to-br from-slate-50 to-blue-50/30">
    <!-- Mobile menu button -->
    <div class="lg:hidden fixed top-4 left-4 z-50">
        <button id="mobileMenuButton"
            class="w-10 h-10 bg-white/80 backdrop-blur-lg rounded-xl flex items-center justify-center shadow-lg border border-slate-200/60">
            <i class="fas fa-bars text-slate-700"></i>
        </button>
    </div>

    <!-- Overlay for mobile -->
    <div id="overlay" class="overlay"></div>

    <div class="flex h-screen">
        <!-- Desktop Sidebar (always visible on large screens) -->
        <div
            class="hidden lg:flex sidebar-desktop w-72 bg-white/80 backdrop-blur-lg border-r border-slate-200/60 flex-col flex-shrink-0 shadow-xl">
            <!-- Logo Section -->
            <div class="p-8 border-b border-slate-200/40">
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <div class="w-12 h-12 gradient-bg rounded-2xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-user-check text-white text-lg"></i>
                        </div>
                        <div
                            class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-400 rounded-full border-2 border-white">
                        </div>
                    </div>
                    <div>
                        <span class="font-bold text-slate-800 text-lg block">Team Portal</span>
                        <span class="text-slate-500 text-sm">Work Dashboard</span>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <div class="flex-1 p-6 overflow-y-auto sidebar-scroll">
                <nav class="space-y-3">
                    <!-- Dashboard -->
                    <a href="{{ route('team.index') }}"
                        class="nav-item flex items-center space-x-4 px-4 py-4 rounded-2xl text-sm font-semibold transition-all duration-300
                       {{ request()->is('team')
                           ? 'bg-blue-500/10 text-blue-600 border border-blue-200 shadow-sm'
                           : 'text-slate-600 hover:bg-white hover:shadow-md hover:border hover:border-slate-200' }}">
                        <div
                            class="w-10 h-10 rounded-xl flex items-center justify-center
                            {{ request()->is('team') ? 'bg-blue-500 text-white' : 'bg-slate-100 text-slate-600' }}">
                            <i class="fas fa-gauge-high"></i>
                        </div>
                        <span>Dashboard</span>
                    </a>


         <a href="{{ route('manager.tasks.index') }}"
                        class="nav-item flex items-center space-x-4 px-4 py-4 rounded-2xl text-sm font-semibold transition-all duration-300
                       {{ request()->is('manager/tasks*')
                           ? 'bg-emerald-500/10 text-emerald-900 border border-emerald-200 shadow-sm'
                           : 'text-slate-600 hover:bg-white hover:shadow-md hover:border hover:border-slate-200' }}">
                        <div
                            class="w-10 h-10 rounded-xl flex items-center justify-center
                            {{ request()->is('manager/tasks*') ? 'bg-emerald-500 text-white' : 'bg-slate-100 text-slate-600' }}">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <span>Tasks</span>
                       
                    </a>

                    <!-- Tasks -->
                    <a href="{{ route('team.tasks.index') }}"
                        class="nav-item flex items-center space-x-4 px-4 py-4 rounded-2xl text-sm font-semibold transition-all duration-300
                       {{ request()->is('team/tasks*')
                           ? 'bg-emerald-500/10 text-emerald-600 border border-emerald-200 shadow-sm'
                           : 'text-slate-600 hover:bg-white hover:shadow-md hover:border hover:border-slate-200' }}">
                        <div
                            class="w-10 h-10 rounded-xl flex items-center justify-center
                            {{ request()->is('team/tasks*') ? 'bg-emerald-500 text-white' : 'bg-slate-100 text-slate-600' }}">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <span>My Tasks</span>
                        <span class="ml-auto bg-slate-200 text-slate-700 text-xs px-2 py-1 rounded-full">
                            {{ \App\Models\Tasks::where('assigned_to', auth()->id())->count() }}
                        </span>
                    </a>
               

                    <!-- Projects -->
                    <a href="{{ route('team.projects') }}"
                        class="nav-item flex items-center space-x-4 px-4 py-4 rounded-2xl text-sm font-semibold transition-all duration-300
                       {{ request()->is('team/projects*')
                           ? 'bg-purple-500/10 text-purple-600 border border-purple-200 shadow-sm'
                           : 'text-slate-600 hover:bg-white hover:shadow-md hover:border hover:border-slate-200' }}">
                        <div
                            class="w-10 h-10 rounded-xl flex items-center justify-center
                            {{ request()->is('team/projects*') ? 'bg-purple-500 text-white' : 'bg-slate-100 text-slate-600' }}">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <span>Projects</span>
                    </a>
 <a href="{{ route('manager.projects.index') }}"
                        class="nav-item flex items-center space-x-4 px-4 py-4 rounded-2xl text-sm font-semibold transition-all duration-300
                       {{ request()->is('manager/projects*')
                           ? 'bg-purple-500/10 text-purple-600 border border-purple-200 shadow-sm'
                           : 'text-slate-600 hover:bg-white hover:shadow-md hover:border hover:border-slate-200' }}">
                        <div
                            class="w-10 h-10 rounded-xl flex items-center justify-center
                            {{ request()->is('manager/projects*') ? 'bg-purple-500 text-white' : 'bg-slate-100 text-slate-600' }}">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <span>My-Projects</span>
                    </a>

                    <a href="{{ url('/files') }}"
                        class="nav-item flex items-center space-x-4 px-4 py-4 rounded-2xl text-sm font-semibold transition-all duration-300
   {{ request()->is('files*')
       ? 'bg-blue-500/10 text-blue-600 border border-blue-200 shadow-sm'
       : 'text-slate-600 hover:bg-white hover:shadow-md hover:border hover:border-slate-200' }}">
                        <div
                            class="w-10 h-10 rounded-xl flex items-center justify-center
        {{ request()->is('files*') ? 'bg-blue-500 text-white' : 'bg-slate-100 text-slate-600' }}">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <span>Files</span>
                    </a>


                    <!-- Chat -->
                    <a href="{{ route('team.chat.index') }}"
                        class="nav-item flex items-center space-x-4 px-4 py-4 rounded-2xl text-sm font-semibold transition-all duration-300
                       {{ request()->is('team/chat*')
                           ? 'bg-indigo-500/10 text-indigo-600 border border-indigo-200 shadow-sm'
                           : 'text-slate-600 hover:bg-white hover:shadow-md hover:border hover:border-slate-200' }}">
                        <div
                            class="w-10 h-10 rounded-xl flex items-center justify-center
                            {{ request()->is('team/chat*') ? 'bg-indigo-500 text-white' : 'bg-slate-100 text-slate-600' }}">
                            <i class="fas fa-comments"></i>
                        </div>
                        <span>Messages</span>
                        @php
                            $unreadCount = \App\Models\ChatRoom::getUnreadMessagesCount(auth()->id());
                        @endphp
                        @if ($unreadCount > 0)
                            <span class="ml-auto bg-red-500 text-white text-xs px-2 py-1 rounded-full">
                                {{ $unreadCount }}
                            </span>
                        @endif
                    </a>

                    {{-- calendar --}}
                    <a href="{{ url('/calendar') }}"
                        class="nav-item flex items-center space-x-4 px-4 py-4 rounded-2xl text-sm font-semibold transition-all duration-300
   {{ request()->is('team/calendar*')
       ? 'bg-indigo-500/10 text-indigo-600 border border-indigo-200 shadow-sm'
       : 'text-slate-600 hover:bg-white hover:shadow-md hover:border hover:border-slate-200' }}">
                        <div
                            class="w-10 h-10 rounded-xl flex items-center justify-center
        {{ request()->is('team/calendar*') ? 'bg-indigo-500 text-white' : 'bg-slate-100 text-slate-600' }}">
        <i class="fas fa-calendar"></i>
    </div>
    <span>Calendar</span>


</a>
                    <!-- Profile -->
                    <a href="{{ route('profile.edit') }}"
                       class="nav-item flex items-center space-x-4 px-4 py-4 rounded-2xl text-sm font-semibold transition-all duration-300
                       {{ request()->is('profile*') ?
                       'bg-amber-500/10 text-amber-600 border border-amber-200 shadow-sm' :
                       'text-slate-600 hover:bg-white hover:shadow-md hover:border hover:border-slate-200' }}">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center
                            {{ request()->is('profile*') ? 'bg-amber-500 text-white' : 'bg-slate-100 text-slate-600' }}">
                            <i class="fas fa-user"></i>
                        </div>
                        <span>Profile</span>
                    </a>
                </nav>

                <!-- Quick Stats -->
                <div class="mt-8 p-4 bg-slate-50/50 rounded-2xl border border-slate-200/50">
                    <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">Quick Stats</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-600">Pending Tasks</span>
                            <span class="font-semibold text-orange-500">
                                {{ \App\Models\Tasks::where('assigned_to', auth()->id())->whereIn('status', ['todo', 'in_progress'])->count() }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-600">Completed</span>
                            <span class="font-semibold text-emerald-500">
                                {{ \App\Models\Tasks::where('assigned_to', auth()->id())->where('status', 'done')->count() }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-600">Unread Messages</span>
                            <span class="font-semibold text-indigo-500">
                                {{ $unreadCount }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Profile Footer -->
            <div class="p-6 border-t border-slate-200/40 bg-white/50">
                <div
                    class="flex items-center space-x-3 p-3 rounded-2xl bg-white/80 border border-slate-200/60 shadow-sm hover:shadow-md transition-all duration-300">
                    <div class="relative">
                        <img class="w-12 h-12 rounded-xl object-cover border-2 border-white shadow-md"
                             src="{{ Auth::user()->profile_photo_url }}"
                             alt="{{ auth()->user()->name }}">
                        <div class="absolute -bottom-1 -right-1 w-3 h-3 bg-emerald-400 rounded-full border-2 border-white"></div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-800 truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-slate-500 capitalize">{{ auth()->user()->role }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-red-500 hover:text-white flex items-center justify-center transition-all duration-300 group">
                            <i class="fas fa-right-from-bracket text-slate-400 group-hover:text-white text-xs"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Mobile Sidebar (hidden by default, slides in on mobile) -->
        <div
            class="lg:hidden sidebar-mobile fixed inset-y-0 left-0 z-50 w-72 bg-white/95 backdrop-blur-lg border-r border-slate-200/60 flex-col flex-shrink-0 shadow-xl">
            <!-- Close button for mobile -->
            <div class="absolute top-4 right-4 z-10">
                <button id="closeMobileMenu"
                    class="w-10 h-10 bg-white/80 backdrop-blur-lg rounded-xl flex items-center justify-center shadow-lg border border-slate-200/60">
                    <i class="fas fa-times text-slate-700"></i>
                </button>
            </div>

            <!-- Logo Section -->
            <div class="p-8 border-b border-slate-200/40 mt-4">
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <div class="w-12 h-12 gradient-bg rounded-2xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-user-check text-white text-lg"></i>
                        </div>
                        <div
                            class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-400 rounded-full border-2 border-white">
                        </div>
                    </div>
                    <div>
                        <span class="font-bold text-slate-800 text-lg block">Team Portal</span>
                        <span class="text-slate-500 text-sm">Work Dashboard</span>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <div class="flex-1 p-6 overflow-y-auto sidebar-scroll">
                <nav class="space-y-3">
                    <!-- Dashboard -->
                    <a href="{{ route('team.index') }}"
                        class="nav-item flex items-center space-x-4 px-4 py-4 rounded-2xl text-sm font-semibold transition-all duration-300
                       {{ request()->is('team')
                           ? 'bg-blue-500/10 text-blue-600 border border-blue-200 shadow-sm'
                           : 'text-slate-600 hover:bg-white hover:shadow-md hover:border hover:border-slate-200' }}">
                        <div
                            class="w-10 h-10 rounded-xl flex items-center justify-center
                            {{ request()->is('team') ? 'bg-blue-500 text-white' : 'bg-slate-100 text-slate-600' }}">
                            <i class="fas fa-gauge-high"></i>
                        </div>
                        <span>Dashboard</span>
                    </a>

                    <!-- Tasks -->
                    <a href="{{ route('team.tasks.index') }}"
                        class="nav-item flex items-center space-x-4 px-4 py-4 rounded-2xl text-sm font-semibold transition-all duration-300
                       {{ request()->is('team/tasks*')
                           ? 'bg-emerald-500/10 text-emerald-600 border border-emerald-200 shadow-sm'
                           : 'text-slate-600 hover:bg-white hover:shadow-md hover:border hover:border-slate-200' }}">
                        <div
                            class="w-10 h-10 rounded-xl flex items-center justify-center
                            {{ request()->is('team/tasks*') ? 'bg-emerald-500 text-white' : 'bg-slate-100 text-slate-600' }}">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <span>My Tasks</span>
                        <span class="ml-auto bg-slate-200 text-slate-700 text-xs px-2 py-1 rounded-full">
                            {{ \App\Models\Tasks::where('assigned_to', auth()->id())->count() }}
                        </span>
                    </a>

                    <!-- Projects -->
                    <a href="{{ route('team.projects') }}"
                        class="nav-item flex items-center space-x-4 px-4 py-4 rounded-2xl text-sm font-semibold transition-all duration-300
                       {{ request()->is('team/projects*')
                           ? 'bg-purple-500/10 text-purple-600 border border-purple-200 shadow-sm'
                           : 'text-slate-600 hover:bg-white hover:shadow-md hover:border hover:border-slate-200' }}">
                        <div
                            class="w-10 h-10 rounded-xl flex items-center justify-center
                            {{ request()->is('team/projects*') ? 'bg-purple-500 text-white' : 'bg-slate-100 text-slate-600' }}">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <span>Projects</span>
                    </a>

                    <a href="{{ url('/files') }}"
                        class="nav-item flex items-center space-x-4 px-4 py-4 rounded-2xl text-sm font-semibold transition-all duration-300
   {{ request()->is('files*')
       ? 'bg-blue-500/10 text-blue-600 border border-blue-200 shadow-sm'
       : 'text-slate-600 hover:bg-white hover:shadow-md hover:border hover:border-slate-200' }}">
                        <div
                            class="w-10 h-10 rounded-xl flex items-center justify-center
        {{ request()->is('files*') ? 'bg-blue-500 text-white' : 'bg-slate-100 text-slate-600' }}">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <span>Files</span>
                    </a>


                    <!-- Chat -->
                    <a href="{{ route('team.chat.index') }}"
                        class="nav-item flex items-center space-x-4 px-4 py-4 rounded-2xl text-sm font-semibold transition-all duration-300
                       {{ request()->is('team/chat*')
                           ? 'bg-indigo-500/10 text-indigo-600 border border-indigo-200 shadow-sm'
                           : 'text-slate-600 hover:bg-white hover:shadow-md hover:border hover:border-slate-200' }}">
                        <div
                            class="w-10 h-10 rounded-xl flex items-center justify-center
                            {{ request()->is('team/chat*') ? 'bg-indigo-500 text-white' : 'bg-slate-100 text-slate-600' }}">
                            <i class="fas fa-comments"></i>
                        </div>
                        <span>Messages</span>
                        @php
                            $unreadCount = \App\Models\ChatRoom::getUnreadMessagesCount(auth()->id());
                        @endphp
                        @if ($unreadCount > 0)
                            <span class="ml-auto bg-red-500 text-white text-xs px-2 py-1 rounded-full">
                                {{ $unreadCount }}
                            </span>
                        @endif
                    </a>

                    {{-- calendar --}}
                    <a href="{{ url('/calendar') }}"
                        class="nav-item flex items-center space-x-4 px-4 py-4 rounded-2xl text-sm font-semibold transition-all duration-300
   {{ request()->is('team/calendar*')
       ? 'bg-indigo-500/10 text-indigo-600 border border-indigo-200 shadow-sm'
       : 'text-slate-600 hover:bg-white hover:shadow-md hover:border hover:border-slate-200' }}">
                        <div
                            class="w-10 h-10 rounded-xl flex items-center justify-center
        {{ request()->is('team/calendar*') ? 'bg-indigo-500 text-white' : 'bg-slate-100 text-slate-600' }}">
                            <i class="fas fa-calendar"></i>
                        </div>
                        <span>Calendar</span>
                    </a>
                    <!-- Profile -->
                    <a href="{{ route('profile.edit') }}"
                        class="nav-item flex items-center space-x-4 px-4 py-4 rounded-2xl text-sm font-semibold transition-all duration-300
                       {{ request()->is('team/profile*')
                           ? 'bg-amber-500/10 text-amber-600 border border-amber-200 shadow-sm'
                           : 'text-slate-600 hover:bg-white hover:shadow-md hover:border hover:border-slate-200' }}">
                        <div
                            class="w-10 h-10 rounded-xl flex items-center justify-center
                            {{ request()->is('team/profile*') ? 'bg-amber-500 text-white' : 'bg-slate-100 text-slate-600' }}">
                            <i class="fas fa-user"></i>
                        </div>
                        <span>Profile</span>
                    </a>
                </nav>

                <!-- Quick Stats -->
                <div class="mt-8 p-4 bg-slate-50/50 rounded-2xl border border-slate-200/50">
                    <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">Quick Stats</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-600">Pending Tasks</span>
                            <span class="font-semibold text-orange-500">
                                {{ \App\Models\Tasks::where('assigned_to', auth()->id())->whereIn('status', ['todo', 'in_progress'])->count() }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-600">Completed</span>
                            <span class="font-semibold text-emerald-500">
                                {{ \App\Models\Tasks::where('assigned_to', auth()->id())->where('status', 'done')->count() }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-600">Unread Messages</span>
                            <span class="font-semibold text-indigo-500">
                                {{ $unreadCount }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Profile Footer -->
            <div class="p-6 border-t border-slate-200/40 bg-white/50">
                <div
                    class="flex items-center space-x-3 p-3 rounded-2xl bg-white/80 border border-slate-200/60 shadow-sm hover:shadow-md transition-all duration-300">
                    <div class="relative">
                        <img class="w-12 h-12 rounded-xl object-cover border-2 border-white shadow-md"
                            src="{{ Auth::user()->profile_photo_url }}"
                            alt="{{ auth()->user()->name }}">
                        <div
                            class="absolute -bottom-1 -right-1 w-3 h-3 bg-emerald-400 rounded-full border-2 border-white">
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-800 truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-slate-500 capitalize">{{ auth()->user()->role }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-red-500 hover:text-white flex items-center justify-center transition-all duration-300 group">
                            <i class="fas fa-right-from-bracket text-slate-400 group-hover:text-white text-xs"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-hidden lg:ml-0">
            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-8 pt-20 lg:pt-8">
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.getElementById('mobileMenuButton');
            const closeMobileMenu = document.getElementById('closeMobileMenu');
            const mobileSidebar = document.querySelector('.sidebar-mobile');
            const overlay = document.getElementById('overlay');

            // Open mobile sidebar
            mobileMenuButton.addEventListener('click', function() {
                mobileSidebar.classList.add('open');
                overlay.classList.add('active');
            });

            // Close mobile sidebar
            closeMobileMenu.addEventListener('click', function() {
                mobileSidebar.classList.remove('open');
                overlay.classList.remove('active');
            });

            // Close mobile sidebar when clicking on overlay
            overlay.addEventListener('click', function() {
                mobileSidebar.classList.remove('open');
                overlay.classList.remove('active');
            });

            // Close mobile sidebar when clicking on a link (for navigation)
            const mobileLinks = document.querySelectorAll('.sidebar-mobile a');
            mobileLinks.forEach(link => {
                link.addEventListener('click', function() {
                    mobileSidebar.classList.remove('open');
                    overlay.classList.remove('active');
                });
            });
        });
    </script>
</body>

</html>
