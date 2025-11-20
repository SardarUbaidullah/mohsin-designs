<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Analytics</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <style>
        .report-tab {
            transition: all 0.2s ease-in-out;
        }
        .active-tab {
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            background-color: white;
            color: #2563eb;
        }
        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Mobile Sidebar Styles */
        .mobile-sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 40;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease-in-out;
        }

        .mobile-sidebar-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .mobile-sidebar {
            position: fixed;
            top: 0;
            left: -100%;
            width: 280px;
            height: 100%;
            background: white;
            z-index: 50;
            transition: left 0.3s ease-in-out;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            overflow-y: auto;
        }

        .mobile-sidebar.active {
            left: 0;
        }

        @media (min-width: 1024px) {
            .mobile-sidebar-overlay {
                display: none;
            }

            .mobile-sidebar {
                display: none;
            }
        }

        /* Custom scrollbar for mobile sidebar */
        .mobile-sidebar::-webkit-scrollbar {
            width: 4px;
        }

        .mobile-sidebar::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        .mobile-sidebar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        .mobile-sidebar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Mobile responsive utilities */
        @media (max-width: 640px) {
            .mobile-stack {
                flex-direction: column;
            }
            .mobile-full {
                width: 100%;
            }
            .mobile-text-center {
                text-align: center;
            }
            .mobile-p-4 {
                padding: 1rem;
            }
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Mobile Sidebar Overlay -->
    <div class="mobile-sidebar-overlay" id="mobileSidebarOverlay"></div>

    <!-- Mobile Sidebar -->
    <div class="mobile-sidebar" id="mobileSidebar">
        <!-- Mobile Sidebar Content -->
        <div class="p-4 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-800">Menu</h2>
            <button id="closeMobileSidebar" class="p-2 rounded-lg hover:bg-gray-100 transition-colors">
                <i class="fas fa-times text-gray-600"></i>
            </button>
        </div>

        <!-- Mobile Navigation -->
        <div class="p-4">
            @include('admin.layouts.mobile-sidebar-content')
        </div>
    </div>

    <!-- Main Layout Container -->
    <div class="flex h-screen">
        <!-- Desktop Sidebar -->
        <div class="hidden lg:block">
            @include('admin.layouts.sidebar')
        </div>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-white border-b border-border h-16 px-4 sm:px-6 flex items-center justify-between">
                <!-- Mobile Menu Button -->
                <button id="mobileMenuButton" class="lg:hidden block p-2">
                    <i class="fa-solid fa-bars text-lg"></i>
                </button>

                <!-- Left side - Search and Project Selector -->
                <div class="flex items-center space-x-4 sm:space-x-6 ml-auto lg:ml-0">
                    <!-- Project Selector -->
                </div>

                <!-- Right side - Icons and Profile -->
                <div class="flex items-center space-x-2 sm:space-x-4">
                    <!-- Icons -->
                    <div class="flex items-center space-x-1 sm:space-x-2">
                        <button class="p-2 text-muted-foreground hover:bg-accent-hover hover:text-accent-foreground rounded-lg transition-colors duration-200">
                           <a href="{{url("/calendar")}}"> <i class="fas fa-calendar text-sm sm:text-base"></i></a>
                        </button>

                        <button class="p-2 text-muted-foreground hover:bg-accent-hover hover:text-accent-foreground rounded-lg transition-colors duration-200">
                            <a href="{{url("/chat")}}"><i class="fas fa-comment text-sm sm:text-base"></i></a>
                        </button>

                        <button type="button" id="refreshBtn" class="p-2 text-muted-foreground hover:bg-accent-hover hover:text-accent-foreground rounded-lg transition-colors duration-200">
                            <i class="fas fa-refresh text-sm sm:text-base"></i>
                        </button>
                    </div>

                    <!-- Divider -->
                    <div class="w-px h-6 bg-border mx-1 sm:mx-2"></div>

                    <!-- Profile -->
                    @auth
                    <div class="relative" x-data="{ open: false }">
                        <button
                            @click="open = !open"
                            class="flex items-center space-x-2 sm:space-x-3 focus:outline-none hover:bg-gray-50 rounded-lg p-1 sm:p-2 transition-colors duration-200"
                        >
                            <img
                                class="w-6 h-6 sm:w-8 sm:h-8 rounded-full object-cover"
                                src="{{ Auth::user()->profile_photo_url }}"
                                alt="User Avatar"
                            />
                            <div class="text-left hidden sm:block">
                                <p class="text-sm font-medium text-card-foreground">
                                  {{Auth::user()->name}}
                                </p>
                                <p class="text-xs text-muted-foreground capitalize">
                                    super admin
                                </p>
                            </div>
                            <svg class="w-3 h-3 sm:w-4 sm:h-4 text-gray-500 transition-transform duration-200 hidden sm:block"
                                 :class="{ 'rotate-180': open }"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div
                            x-show="open"
                            @click.away="open = false"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 transform -translate-y-2"
                            x-transition:enter-end="opacity-100 transform translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 transform translate-y-0"
                            x-transition:leave-end="opacity-0 transform -translate-y-2"
                            class="absolute right-0 mt-2 w-48 sm:w-56 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50"
                            style="display: none;"
                        >
                            <!-- User Info -->
                            <div class="px-3 sm:px-4 py-2 border-b border-gray-100">
                                <p class="text-sm font-medium text-gray-900">{{Auth::user()->name}}</p>
                                <p class="text-xs text-gray-500 truncate">{{Auth::user()->email}}</p>
                            </div>

                            <!-- Profile Link -->
                            <a
                                href="{{ route('profile.edit') }}"
                                class="flex items-center px-3 sm:px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200"
                            >
                                <svg class="w-4 h-4 mr-2 sm:mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                Profile
                            </a>

                            <!-- Divider -->
                            <div class="border-t border-gray-100 my-1"></div>

                            <!-- Logout Form -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button
                                    type="submit"
                                    class="flex items-center w-full px-3 sm:px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors duration-200"
                                >
                                    <svg class="w-4 h-4 mr-2 sm:mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                    </svg>
                                    Log Out
                                </button>
                            </form>
                        </div>
                    </div>
                    @endauth
                </div>
            </header>

            <!-- Main Content -->
            <div class="flex-1 overflow-y-auto">
                <!-- Page Header in Body -->
                <div class="bg-white border-b">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 sm:py-8">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-6 space-y-4 sm:space-y-0">
                            <div class="flex flex-col sm:flex-row sm:items-center">
                                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 text-center sm:text-left">Project Analytics</h1>
                                <span class="mt-2 sm:mt-0 sm:ml-4 text-base sm:text-lg text-gray-500 text-center sm:text-left">Progress, workload, and performance analytics</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 sm:py-8">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6 sm:mb-8">
                        <!-- Projects Card -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6 hover:shadow-md transition-shadow duration-300">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-500 rounded-lg flex items-center justify-center shadow-md">
                                        <i class="fas fa-project-diagram text-white text-lg sm:text-xl"></i>
                                    </div>
                                </div>
                                <div class="ml-3 sm:ml-4">
                                    <h3 class="text-xs sm:text-sm font-medium text-gray-500">Total Projects</h3>
                                    <p id="totalProjects" class="text-xl sm:text-2xl font-bold text-gray-900">--</p>
                                    <p class="text-xs sm:text-sm text-green-600 font-medium" id="projectGrowth">Active projects</p>
                                </div>
                            </div>
                        </div>

                        <!-- Tasks Card -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6 hover:shadow-md transition-shadow duration-300">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-green-500 rounded-lg flex items-center justify-center shadow-md">
                                        <i class="fas fa-tasks text-white text-lg sm:text-xl"></i>
                                    </div>
                                </div>
                                <div class="ml-3 sm:ml-4">
                                    <h3 class="text-xs sm:text-sm font-medium text-gray-500">Completed Tasks</h3>
                                    <p id="completedTasks" class="text-xl sm:text-2xl font-bold text-gray-900">--</p>
                                    <p class="text-xs sm:text-sm text-green-600 font-medium" id="taskCompletionRate">All tasks</p>
                                </div>
                            </div>
                        </div>

                        <!-- Team Card -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6 hover:shadow-md transition-shadow duration-300">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-purple-500 rounded-lg flex items-center justify-center shadow-md">
                                        <i class="fas fa-users text-white text-lg sm:text-xl"></i>
                                    </div>
                                </div>
                                <div class="ml-3 sm:ml-4">
                                    <h3 class="text-xs sm:text-sm font-medium text-gray-500">Team Members</h3>
                                    <p id="activeTeam" class="text-xl sm:text-2xl font-bold text-gray-900">--</p>
                                    <p class="text-xs sm:text-sm text-gray-600 font-medium" id="teamProductivity">Active users</p>
                                </div>
                            </div>
                        </div>

                        <!-- Performance Card -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6 hover:shadow-md transition-shadow duration-300">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-orange-500 rounded-lg flex items-center justify-center shadow-md">
                                        <i class="fas fa-chart-line text-white text-lg sm:text-xl"></i>
                                    </div>
                                </div>
                                <div class="ml-3 sm:ml-4">
                                    <h3 class="text-xs sm:text-sm font-medium text-gray-500">Completion Rate</h3>
                                    <p id="avgPerformance" class="text-xl sm:text-2xl font-bold text-gray-900">--</p>
                                    <p class="text-xs sm:text-sm text-green-600 font-medium" id="performanceTrend">Overall progress</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Report Navigation -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6 mb-6 sm:mb-8">
                        <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-1 bg-gray-100 p-1 rounded-lg">
                            <button type="button" id="progressTab" class="flex-1 py-2 sm:py-3 px-3 sm:px-4 text-center font-medium rounded-md transition duration-200 report-tab active-tab text-sm sm:text-base">
                                <i class="fas fa-chart-bar mr-2"></i>Progress
                            </button>
                            <button type="button" id="workloadTab" class="flex-1 py-2 sm:py-3 px-3 sm:px-4 text-center font-medium rounded-md transition duration-200 report-tab text-sm sm:text-base">
                                <i class="fas fa-user-check mr-2"></i>Workload
                            </button>
                            <button type="button" id="performanceTab" class="flex-1 py-2 sm:py-3 px-3 sm:px-4 text-center font-medium rounded-md transition duration-200 report-tab text-sm sm:text-base">
                                <i class="fas fa-trophy mr-2"></i>Performance
                            </button>
                        </div>
                    </div>

                    <!-- Report Content -->
                    <div id="reportContent">
                        <div class="text-center py-8 sm:py-12">
                            <div class="animate-spin rounded-full h-10 w-10 sm:h-12 sm:w-12 border-b-2 border-blue-600 mx-auto mb-3 sm:mb-4"></div>
                            <p class="text-gray-600 text-sm sm:text-base">Loading analytics...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentReport = 'progress';

        // Mobile sidebar elements
        const mobileMenuButton = document.getElementById('mobileMenuButton');
        const mobileSidebar = document.getElementById('mobileSidebar');
        const mobileSidebarOverlay = document.getElementById('mobileSidebarOverlay');
        const closeMobileSidebar = document.getElementById('closeMobileSidebar');

        document.addEventListener('DOMContentLoaded', function() {
            initDashboard();
            initMobileSidebar();
        });

        function initDashboard() {
            document.getElementById('refreshBtn').addEventListener('click', refreshAllReports);
            document.getElementById('progressTab').addEventListener('click', () => showReport('progress'));
            document.getElementById('workloadTab').addEventListener('click', () => showReport('workload'));
            document.getElementById('performanceTab').addEventListener('click', () => showReport('performance'));

            loadQuickStats();
            loadReport('progress');
        }

        function initMobileSidebar() {
            // Mobile sidebar functionality
            if (mobileMenuButton) {
                mobileMenuButton.addEventListener('click', openMobileSidebar);
            }
            if (closeMobileSidebar) {
                closeMobileSidebar.addEventListener('click', closeMobileSidebarFunc);
            }
            if (mobileSidebarOverlay) {
                mobileSidebarOverlay.addEventListener('click', closeMobileSidebarFunc);
            }

            // Close mobile sidebar when clicking on a link
            if (mobileSidebar) {
                const mobileLinks = mobileSidebar.querySelectorAll('a');
                mobileLinks.forEach(link => {
                    link.addEventListener('click', closeMobileSidebarFunc);
                });
            }
        }

        // Mobile sidebar functions
        function openMobileSidebar() {
            if (mobileSidebar && mobileSidebarOverlay) {
                mobileSidebar.classList.add('active');
                mobileSidebarOverlay.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeMobileSidebarFunc() {
            if (mobileSidebar && mobileSidebarOverlay) {
                mobileSidebar.classList.remove('active');
                mobileSidebarOverlay.classList.remove('active');
                document.body.style.overflow = '';
            }
        }

        async function loadQuickStats() {
            try {
                const response = await fetch('/admin/reports/quick-stats');
                if (response.ok) {
                    const data = await response.json();
                    document.getElementById('totalProjects').textContent = data.total_projects || '0';
                    document.getElementById('completedTasks').textContent = data.completed_tasks || '0';
                    document.getElementById('activeTeam').textContent = data.active_team || '0';
                    document.getElementById('avgPerformance').textContent = data.avg_performance || '0%';
                }
            } catch (error) {
                console.error('Error loading quick stats:', error);
            }
        }

        async function loadReport(reportType) {
            currentReport = reportType;

            // Update active tab
            document.querySelectorAll('.report-tab').forEach(tab => {
                tab.classList.remove('active-tab');
            });
            document.getElementById(reportType + 'Tab').classList.add('active-tab');

            // Show loading
            const reportContent = document.getElementById('reportContent');
            reportContent.innerHTML = `
                <div class="text-center py-8 sm:py-12">
                    <div class="animate-spin rounded-full h-10 w-10 sm:h-12 sm:w-12 border-b-2 border-blue-600 mx-auto mb-3 sm:mb-4"></div>
                    <p class="text-gray-600 text-sm sm:text-base">Loading ${reportType} report...</p>
                </div>
            `;

            try {
                const response = await fetch(`/admin/reports/data/${reportType}`);
                if (response.ok) {
                    const data = await response.json();
                    renderReport(reportType, data);
                } else {
                    throw new Error('Failed to load report');
                }
            } catch (error) {
                reportContent.innerHTML = `
                    <div class="text-center py-8 sm:py-12 fade-in">
                        <i class="fas fa-exclamation-triangle text-gray-400 text-3xl sm:text-4xl mb-3 sm:mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Unable to load report</h3>
                        <p class="text-gray-600 mb-4 text-sm sm:text-base">${error.message}</p>
                        <button type="button" onclick="loadReport('${reportType}')" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200 text-sm sm:text-base">
                            Try Again
                        </button>
                    </div>
                `;
            }
        }

        function renderReport(reportType, data) {
            const reportContent = document.getElementById('reportContent');

            switch(reportType) {
                case 'progress':
                    renderProgressReport(data, reportContent);
                    break;
                case 'workload':
                    renderWorkloadReport(data, reportContent);
                    break;
                case 'performance':
                    renderPerformanceReport(data, reportContent);
                    break;
            }
        }

        function renderProgressReport(data, container) {
            const projects = data.projects || {};
            const tasks = data.tasks || {};
            const recentProjects = data.recent_projects || [];

            container.innerHTML = `
                <div class="space-y-4 sm:space-y-6 fade-in">
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-900 text-center sm:text-left">Project Progress Analytics</h2>

                    <!-- Project Progress -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-3 sm:mb-4">Projects Overview</h3>
                            <div class="space-y-3 sm:space-y-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600 text-sm sm:text-base">Total Projects</span>
                                    <span class="font-bold text-sm sm:text-base">${projects.total || 0}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600 text-sm sm:text-base">Completed</span>
                                    <span class="font-bold text-green-600 text-sm sm:text-base">${projects.completed || 0}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600 text-sm sm:text-base">In Progress</span>
                                    <span class="font-bold text-blue-600 text-sm sm:text-base">${projects.in_progress || 0}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600 text-sm sm:text-base">Planning</span>
                                    <span class="font-bold text-yellow-600 text-sm sm:text-base">${projects.planning || 0}</span>
                                </div>
                                <div class="pt-3 sm:pt-4 border-t border-gray-200">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="font-medium text-sm sm:text-base">Completion Rate</span>
                                        <span class="font-bold text-blue-600 text-sm sm:text-base">${projects.completion_rate || 0}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: ${projects.completion_rate || 0}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-3 sm:mb-4">Tasks Overview</h3>
                            <div class="space-y-3 sm:space-y-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600 text-sm sm:text-base">Total Tasks</span>
                                    <span class="font-bold text-sm sm:text-base">${tasks.total || 0}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600 text-sm sm:text-base">Completed</span>
                                    <span class="font-bold text-green-600 text-sm sm:text-base">${tasks.completed || 0}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600 text-sm sm:text-base">In Progress</span>
                                    <span class="font-bold text-yellow-600 text-sm sm:text-base">${tasks.in_progress || 0}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600 text-sm sm:text-base">To Do</span>
                                    <span class="font-bold text-gray-600 text-sm sm:text-base">${tasks.todo || 0}</span>
                                </div>
                                <div class="pt-3 sm:pt-4 border-t border-gray-200">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="font-medium text-sm sm:text-base">Completion Rate</span>
                                        <span class="font-bold text-green-600 text-sm sm:text-base">${tasks.completion_rate || 0}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-green-600 h-2 rounded-full" style="width: ${tasks.completion_rate || 0}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Projects -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3 sm:mb-4">Recent Projects</h3>
                        <div class="space-y-2 sm:space-y-3">
                            ${recentProjects.map(project => `
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition duration-200">
                                    <div class="flex items-center mb-2 sm:mb-0">
                                        <div class="w-3 h-3 rounded-full ${getProjectStatusColor(project.status)} mr-3"></div>
                                        <div>
                                            <div class="font-medium text-gray-900 text-sm sm:text-base">${project.name}</div>
                                            <div class="text-xs sm:text-sm text-gray-500">${project.completed_tasks}/${project.total_tasks} tasks completed</div>
                                        </div>
                                    </div>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full ${getProjectStatusBadgeColor(project.status)} self-start sm:self-auto">
                                        ${project.status || 'Unknown'}
                                    </span>
                                </div>
                            `).join('')}
                            ${recentProjects.length === 0 ? '<p class="text-center text-gray-500 py-4 text-sm sm:text-base">No projects found</p>' : ''}
                        </div>
                    </div>
                </div>
            `;
        }

        function renderWorkloadReport(data, container) {
            const teamWorkload = data.team_workload || [];
            const summary = data.summary || {};

            container.innerHTML = `
                <div class="space-y-4 sm:space-y-6 fade-in">
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-900 text-center sm:text-left">Team Workload Distribution</h2>

                    <!-- Summary -->
                    <div class="grid grid-cols-2 gap-3 sm:gap-4">
                        <div class="bg-white rounded-lg p-3 sm:p-4 text-center border border-gray-200">
                            <div class="text-xl sm:text-2xl font-bold text-blue-600">${summary.total_team_members || 0}</div>
                            <div class="text-xs sm:text-sm text-gray-600">Team Members</div>
                        </div>
                        <div class="bg-white rounded-lg p-3 sm:p-4 text-center border border-gray-200">
                            <div class="text-xl sm:text-2xl font-bold text-green-600">${summary.total_assigned_tasks || 0}</div>
                            <div class="text-xs sm:text-sm text-gray-600">Total Tasks</div>
                        </div>
                        <div class="bg-white rounded-lg p-3 sm:p-4 text-center border border-gray-200">
                            <div class="text-xl sm:text-2xl font-bold text-purple-600">${summary.total_managed_projects || 0}</div>
                            <div class="text-xs sm:text-sm text-gray-600">Managed Projects</div>
                        </div>
                        <div class="bg-white rounded-lg p-3 sm:p-4 text-center border border-gray-200">
                            <div class="text-xl sm:text-2xl font-bold text-yellow-600">${summary.total_in_progress || 0}</div>
                            <div class="text-xs sm:text-sm text-gray-600">In Progress</div>
                        </div>
                    </div>

                    <!-- Team Workload -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3 sm:mb-4">Team Workload</h3>
                        <div class="space-y-3 sm:space-y-4">
                            ${teamWorkload.map(member => {
                                const isAdmin = member.user?.role === 'admin';
                                const isTaskBased = member.workload_type === 'task_based';
                                const userName = member.user?.name || 'Unknown User';
                                const userInitial = userName.charAt(0).toUpperCase();

                                const itemLabel = isTaskBased ? 'tasks' : 'projects';
                                const completedLabel = isTaskBased ? 'Done' : 'Completed';
                                const todoLabel = isTaskBased ? 'To Do' : 'Planning';

                                return `
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between p-3 sm:p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition duration-200">
                                    <div class="flex items-center space-x-3 sm:space-x-4 mb-2 sm:mb-0">
                                        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center shadow-md">
                                            <span class="text-xs sm:text-sm font-medium text-white">${userInitial}</span>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900 text-sm sm:text-base">${userName}</div>
                                            <div class="text-xs sm:text-sm text-gray-500 flex items-center">
                                                ${member.total_items || 0} total ${itemLabel}
                                                ${isAdmin ? '<span class="ml-2 px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">Admin</span>' : ''}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between sm:justify-end sm:space-x-4 sm:space-x-6 w-full sm:w-auto">
                                        <div class="text-center">
                                            <div class="text-base sm:text-lg font-bold text-green-600">${member.completed_items || 0}</div>
                                            <div class="text-xs text-gray-500">${completedLabel}</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="text-base sm:text-lg font-bold text-yellow-600">${member.in_progress_items || 0}</div>
                                            <div class="text-xs text-gray-500">In Progress</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="text-base sm:text-lg font-bold text-gray-600">${member.todo_items || 0}</div>
                                            <div class="text-xs text-gray-500">${todoLabel}</div>
                                        </div>
                                        <span class="px-2 py-1 text-xs font-medium rounded-full ${getWorkloadLevelColor(member.workload_level)} mt-2 sm:mt-0 sm:ml-2">
                                            ${member.workload_level}
                                        </span>
                                    </div>
                                </div>
                                `;
                            }).join('')}
                            ${teamWorkload.length === 0 ? '<p class="text-center text-gray-500 py-4 text-sm sm:text-base">No team members found</p>' : ''}
                        </div>
                    </div>
                </div>
            `;
        }

        function renderPerformanceReport(data, container) {
            const userPerformance = data.user_performance || [];
            const qualityMetrics = data.quality_metrics || {};

            container.innerHTML = `
                <div class="space-y-4 sm:space-y-6 fade-in">
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-900 text-center sm:text-left">Team Performance Metrics</h2>

                    <!-- Quality Metrics -->
                    <div class="grid grid-cols-2 gap-3 sm:gap-4">
                        <div class="bg-white rounded-lg p-3 sm:p-4 text-center border border-gray-200">
                            <div class="text-xl sm:text-2xl font-bold text-blue-600">${qualityMetrics.total_tasks || 0}</div>
                            <div class="text-xs sm:text-sm text-gray-600">Total Tasks</div>
                        </div>
                        <div class="bg-white rounded-lg p-3 sm:p-4 text-center border border-gray-200">
                            <div class="text-xl sm:text-2xl font-bold text-green-600">${qualityMetrics.completed_tasks || 0}</div>
                            <div class="text-xs sm:text-sm text-gray-600">Completed</div>
                        </div>
                        <div class="bg-white rounded-lg p-3 sm:p-4 text-center border border-gray-200">
                            <div class="text-xl sm:text-2xl font-bold text-yellow-600">${qualityMetrics.in_progress_tasks || 0}</div>
                            <div class="text-xs sm:text-sm text-gray-600">In Progress</div>
                        </div>
                        <div class="bg-white rounded-lg p-3 sm:p-4 text-center border border-gray-200">
                            <div class="text-xl sm:text-2xl font-bold text-purple-600">${Math.round(qualityMetrics.team_productivity || 0)}%</div>
                            <div class="text-xs sm:text-sm text-gray-600">Team Productivity</div>
                        </div>
                    </div>

                    <!-- Individual Performance -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3 sm:mb-4">Individual Performance</h3>
                        <div class="space-y-3 sm:space-y-4">
                            ${userPerformance.map(performance => {
                                const isAdmin = performance.user?.role === 'admin';
                                const isTaskBased = performance.performance_type === 'task_based';
                                const userName = performance.user?.name || 'Unknown User';
                                const userInitial = userName.charAt(0).toUpperCase();

                                return `
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between p-3 sm:p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition duration-200">
                                    <div class="flex items-center space-x-3 sm:space-x-4 mb-2 sm:mb-0">
                                        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-r from-green-500 to-blue-600 rounded-full flex items-center justify-center shadow-md">
                                            <span class="text-xs sm:text-sm font-medium text-white">${userInitial}</span>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900 text-sm sm:text-base">${userName}</div>
                                            <div class="text-xs sm:text-sm text-gray-500 flex items-center">
                                                ${isTaskBased
                                                    ? `${performance.total_tasks || 0} assigned tasks`
                                                    : `${performance.total_projects || 0} managed projects`
                                                }
                                                ${isAdmin ? '<span class="ml-2 px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">Admin</span>' : ''}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between sm:justify-end sm:space-x-4 sm:space-x-6 w-full sm:w-auto">
                                        <div class="text-center">
                                            <div class="text-base sm:text-lg font-bold text-green-600">
                                                ${isTaskBased
                                                    ? (performance.completed_tasks || 0)
                                                    : (performance.completed_projects || 0)
                                                }
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                ${isTaskBased ? 'Completed' : 'Projects Done'}
                                            </div>
                                        </div>
                                        <div class="text-center">
                                            <div class="text-base sm:text-lg font-bold ${getCompletionRateColor(performance.completion_rate || 0)}">
                                                ${performance.completion_rate || 0}%
                                            </div>
                                            <div class="text-xs text-gray-500">Completion Rate</div>
                                        </div>
                                        <span class="px-2 py-1 text-xs font-medium rounded-full ${getPerformanceLevelColor(performance.performance_level || 'Needs Improvement')} mt-2 sm:mt-0 sm:ml-2">
                                            ${performance.performance_level || 'Needs Improvement'}
                                        </span>
                                    </div>
                                </div>
                                `;
                            }).join('')}
                            ${userPerformance.length === 0 ?
                                '<div class="text-center py-6 sm:py-8"><i class="fas fa-users text-gray-300 text-3xl sm:text-4xl mb-2 sm:mb-3"></i><p class="text-gray-500 text-sm sm:text-base">No performance data available</p></div>' :
                                ''}
                        </div>
                    </div>
                </div>
            `;
        }

        // Utility functions
        function getProjectStatusColor(status) {
            const colors = {
                'completed': 'bg-green-500',
                'pending': 'bg-yellow-500',
                'planning': 'bg-blue-500'
            };
            return colors[status] || 'bg-gray-400';
        }

        function getProjectStatusBadgeColor(status) {
            const colors = {
                'completed': 'bg-green-100 text-green-800',
                'pending': 'bg-yellow-100 text-yellow-800',
                'planning': 'bg-blue-100 text-blue-800'
            };
            return colors[status] || 'bg-gray-100 text-gray-800';
        }

        function getWorkloadLevelColor(level) {
            const colors = {
                'Low': 'bg-green-100 text-green-800',
                'Normal': 'bg-blue-100 text-blue-800',
                'High': 'bg-yellow-100 text-yellow-800',
                'Overloaded': 'bg-red-100 text-red-800'
            };
            return colors[level] || 'bg-gray-100 text-gray-800';
        }

        function getPerformanceLevelColor(level) {
            const colors = {
                'Excellent': 'bg-green-100 text-green-800',
                'Very Good': 'bg-blue-100 text-blue-800',
                'Good': 'bg-yellow-100 text-yellow-800',
                'Average': 'bg-orange-100 text-orange-800',
                'Needs Improvement': 'bg-red-100 text-red-800'
            };
            return colors[level] || 'bg-gray-100 text-gray-800';
        }

        function getCompletionRateColor(rate) {
            if (rate >= 80) return 'text-green-600';
            if (rate >= 60) return 'text-yellow-600';
            return 'text-red-600';
        }

        function refreshAllReports() {
            loadQuickStats();
            loadReport(currentReport);
        }

        function showReport(reportType) {
            loadReport(reportType);
        }
    </script>
</body>
</html>
