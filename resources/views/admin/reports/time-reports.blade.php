@extends('admin.layouts.app')

@section('title', 'Time Tracking Reports')

@section('content')
    <div class="min-h-screen bg-gray-50">
        <!-- CSRF Token for AJAX requests -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Header -->
        <div class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-6">
                    <div class="flex items-center">
                        <h1 class="text-3xl font-bold text-gray-900">Time Tracking Reports</h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <!-- Report Type Selector -->
                        <select id="reportType"
                            class="bg-white border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="time_summary">Time Summary</option>
                            <option value="project_duration">Project Duration</option>
                            <option value="detailed">Detailed Report</option>
                        </select>

                        <!-- Date Range Filter -->
                        <select id="dateRange"
                            class="bg-white border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="7">Last 7 days</option>
                            <option value="30" selected>Last 30 days</option>
                            <option value="90">Last 90 days</option>
                            <option value="365">Last year</option>
                            <option value="all">All time</option>
                        </select>

                        <!-- Refresh Button -->
                        <button id="refreshBtn"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-sync-alt mr-2"></i>Refresh
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div id="reportContent">
                <!-- Content will be loaded dynamically here -->
                <div class="text-center py-12">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
                    <p class="text-gray-600">Loading report...</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let currentDateRange = '30';
            let currentReportType = 'time_summary';

            // Get CSRF token safely
            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

            // Base URL for API calls
            const baseUrl = '/admin/time-reports';

            // Initialize
            loadReport();

            // Event listeners
            document.getElementById('refreshBtn').addEventListener('click', loadReport);
            document.getElementById('dateRange').addEventListener('change', function() {
                currentDateRange = this.value;
                loadReport();
            });

            document.getElementById('reportType').addEventListener('change', function() {
                currentReportType = this.value;
                loadReport();
            });

            // Load report based on type
            async function loadReport(page = 1) {
                const contentDiv = document.getElementById('reportContent');
                contentDiv.innerHTML = `
            <div class="text-center py-12">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
                <p class="text-gray-600">Loading ${currentReportType.replace('_', ' ')} report...</p>
            </div>
        `;

                try {
                    let url;

                    switch (currentReportType) {
                        case 'time_summary':
                            url = baseUrl + '/summary?range=' + currentDateRange;
                            break;
                        case 'project_duration':
                            url = baseUrl + '/project-duration?range=' + currentDateRange;
                            break;
                        case 'detailed':
                            url = baseUrl + '/detailed?range=' + currentDateRange + '&page=' + page;
                            break;
                        default:
                            url = baseUrl + '/summary?range=' + currentDateRange;
                    }

                    console.log('Loading URL:', url); // Debug log

                    const response = await fetch(url, {
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const data = await response.json();
                    console.log('Response data:', data); // Debug log

                    if (!data.success) {
                        throw new Error(data.error || 'Failed to load report');
                    }

                    // Render based on report type
                    switch (currentReportType) {
                        case 'time_summary':
                            renderTimeSummary(data);
                            break;
                        case 'project_duration':
                            renderProjectDuration(data);
                            break;
                        case 'detailed':
                            renderDetailedReport(data);
                            break;
                    }

                } catch (error) {
                    console.error('Error loading report:', error);
                    contentDiv.innerHTML = `
                <div class="text-center py-12 text-red-600">
                    <i class="fas fa-exclamation-triangle text-3xl mb-4"></i>
                    <p class="text-lg font-medium">Failed to load report</p>
                    <p class="text-sm mt-2">${error.message}</p>
                    <button onclick="location.reload()" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Try Again
                    </button>
                </div>
            `;
                }
            }

            // Render Time Summary
            function renderTimeSummary(data) {
                const {
                    summary,
                    time_by_user,
                    time_by_project,
                    time_by_task
                } = data;

                const formatTime = (time) => time || '0h 0m';
                const formatHours = (hours) => hours ? parseFloat(hours).toFixed(1) : '0';
                const formatName = (name) => name || 'Unknown';

                document.getElementById('reportContent').innerHTML = `
            <div class="fade-in">
                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center">
                        <div class="text-2xl font-bold text-blue-600">${formatTime(summary.formatted_total_time)}</div>
                        <div class="text-sm text-gray-600 mt-1">Total Time</div>
                        <div class="text-xs text-gray-400">${summary.period}</div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center">
                        <div class="text-2xl font-bold text-green-600">${summary.total_tasks_tracked || 0}</div>
                        <div class="text-sm text-gray-600 mt-1">Tasks Tracked</div>
                        <div class="text-xs text-gray-400">With time entries</div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center">
                        <div class="text-2xl font-bold text-purple-600">${summary.team_members || 0}</div>
                        <div class="text-sm text-gray-600 mt-1">Team Members</div>
                        <div class="text-xs text-gray-400">Active trackers</div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center">
                        <div class="text-2xl font-bold text-orange-600">${formatTime(summary.avg_daily_formatted)}</div>
                        <div class="text-sm text-gray-600 mt-1">Avg. Daily</div>
                        <div class="text-xs text-gray-400">Time spent</div>
                    </div>
                </div>

                <!-- Kanban Cards -->
                <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                    <!-- Time by User -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <i class="fas fa-users text-blue-500 mr-2"></i>
                                Time by Team
                            </h3>
                        </div>
                        <div class="p-4">
                            ${time_by_user && time_by_user.length > 0 ? time_by_user.map(user => `
                                    <div class="flex items-center justify-between p-3 mb-2 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                                <span class="text-xs font-medium text-white">${formatName(user.user?.name).charAt(0)}</span>
                                            </div>
                                            <div>
                                                <div class="font-medium text-gray-900 text-sm">${formatName(user.user?.name)}</div>
                                                <div class="text-xs text-gray-500">${formatHours(user.total_hours)} hours</div>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="font-bold text-blue-600 text-sm">${formatTime(user.formatted_time)}</div>
                                        </div>
                                    </div>
                                `).join('') : `
                                    <div class="text-center py-8 text-gray-500">
                                        <i class="fas fa-users text-gray-300 text-3xl mb-3"></i>
                                        <p class="text-sm">No team data available</p>
                                    </div>
                                `}
                        </div>
                    </div>

                    <!-- Time by Project -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <i class="fas fa-project-diagram text-green-500 mr-2"></i>
                                Time by Project
                            </h3>
                        </div>
                        <div class="p-4">
                            ${time_by_project && time_by_project.length > 0 ? time_by_project.map(project => `
                                    <div class="flex items-center justify-between p-3 mb-2 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                        <div class="flex items-center space-x-3 flex-1">
                                            <div class="w-2 h-8 bg-green-500 rounded-full"></div>
                                            <div class="flex-1 min-w-0">
                                                <div class="font-medium text-gray-900 text-sm truncate">${formatName(project.project?.name)}</div>
                                                <div class="text-xs text-gray-500">${formatHours(project.total_hours)} hours</div>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="font-bold text-green-600 text-sm">${formatTime(project.formatted_time)}</div>
                                        </div>
                                    </div>
                                `).join('') : `
                                    <div class="text-center py-8 text-gray-500">
                                        <i class="fas fa-project-diagram text-gray-300 text-3xl mb-3"></i>
                                        <p class="text-sm">No project data available</p>
                                    </div>
                                `}
                        </div>
                    </div>

                    <!-- Top Tasks -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <i class="fas fa-tasks text-purple-500 mr-2"></i>
                                Top Tasks
                            </h3>
                        </div>
                        <div class="p-4">
                            ${time_by_task && time_by_task.length > 0 ? time_by_task.map(task => `
                                    <div class="p-3 mb-2 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                        <div class="font-medium text-gray-900 text-sm mb-1 truncate">${formatName(task.task?.title)}</div>
                                        <div class="text-xs text-gray-500 mb-2">${formatName(task.task?.project?.name)}</div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-xs text-gray-600">${formatHours(task.total_hours)} hours</span>
                                            <span class="font-bold text-purple-600 text-sm">${formatTime(task.formatted_time)}</span>
                                        </div>
                                    </div>
                                `).join('') : `
                                    <div class="text-center py-8 text-gray-500">
                                        <i class="fas fa-tasks text-gray-300 text-3xl mb-3"></i>
                                        <p class="text-sm">No task data available</p>
                                    </div>
                                `}
                        </div>
                    </div>
                </div>
            </div>
        `;
            }

            // Render Project Duration
            function renderProjectDuration(data) {
                const {
                    projects,
                    summary
                } = data;

                document.getElementById('reportContent').innerHTML = `
            <div class="fade-in">
                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center">
                        <div class="text-2xl font-bold text-blue-600">${summary.total_projects || 0}</div>
                        <div class="text-sm text-gray-600 mt-1">Total Projects</div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center">
                        <div class="text-2xl font-bold text-green-600">${Math.round(summary.avg_duration_hours || 0)}h</div>
                        <div class="text-sm text-gray-600 mt-1">Avg. Duration</div>
                        <div class="text-xs text-gray-400">Project lifespan</div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center">
                        <div class="text-2xl font-bold text-purple-600">${(summary.avg_duration_hours || 0).toFixed(1)}h</div>
                        <div class="text-sm text-gray-600 mt-1">Avg. Hours</div>
                        <div class="text-xs text-gray-400">Per project</div>
                    </div>
                </div>

                <!-- Project Duration Table -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Project Duration Analysis</h3>
                    </div>
                    <div class="p-6">
                        ${projects && projects.length > 0 ? `
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Project</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Duration</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Last Updated</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            ${projects.map(project => `
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4">
                                                    <div class="text-sm font-medium text-gray-900">${project.name || 'Unknown'}</div>
                                                    <div class="text-sm text-gray-500">${project.description || 'No description'}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-gray-900">${project.duration?.formatted_duration || 'N/A'}</div>
                                                    <div class="text-xs text-gray-500">${Math.round(project.duration?.total_hours || 0)} hours</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    ${(project.created_at || '').split(' ')[0] || 'N/A'}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    ${(project.updated_at || '').split(' ')[0] || 'N/A'}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                        ${(project.activity?.activity_status || 'Stale') === 'Recent' ? 'bg-green-100 text-green-800' : 
                                                          (project.activity?.activity_status || 'Stale') === 'Active' ? 'bg-blue-100 text-blue-800' : 
                                                          'bg-gray-100 text-gray-800'}">
                                                        ${project.activity?.activity_status || 'Stale'}
                                                    </span>
                                        
                                                </td>
                                            </tr>
                                        `).join('')}
                                        </tbody>
                                    </table>
                                </div>
                            ` : `
                                <div class="text-center py-8 text-gray-500">
                                    <i class="fas fa-project-diagram text-gray-300 text-3xl mb-3"></i>
                                    <p>No project data available</p>
                                </div>
                            `}
                    </div>
                </div>
            </div>
        `;
            }

            // Render Detailed Report
            // Render Detailed Report - FIXED VERSION
            function renderDetailedReport(data) {
                const timeLogs = data.time_logs;

                document.getElementById('reportContent').innerHTML = `
        <div class="fade-in">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Detailed Time Entries</h3>
                </div>
                <div class="p-6">
                    ${timeLogs.data && timeLogs.data.length > 0 ? `
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Task</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Project</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Duration</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        ${timeLogs.data.map(entry => `
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">${entry.user_name}</div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm font-medium text-gray-900">${entry.task_name}</div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-gray-900">${entry.project_name}</div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-gray-900 max-w-xs truncate" title="${entry.description}">${entry.description}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">${entry.date}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-green-600">${entry.formatted_duration}</div>
                                                <div class="text-xs text-gray-500">${entry.duration_hours} hours</div>
                                            </td>
                                        </tr>
                                    `).join('')}
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Pagination -->
                            <div class="mt-6 flex items-center justify-between">
                                <div class="text-sm text-gray-700">
                                    Showing ${timeLogs.from} to ${timeLogs.to} of ${timeLogs.total} entries
                                </div>
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                    ${timeLogs.links.map(link => {
                                        if (!link.url) return '';
                                        
                                        const pageMatch = link.url.match(/page=(\d+)/);
                                        const page = pageMatch ? pageMatch[1] : 1;
                                        const isActive = link.active;
                                        const isDisabled = link.label.includes('Previous') && timeLogs.current_page === 1;
                                        const isNextDisabled = link.label.includes('Next') && timeLogs.current_page === timeLogs.last_page;
                                        
                                        return `
                                        <a href="#" onclick="event.preventDefault(); loadDetailedPage(${page});" 
                                           class="relative inline-flex items-center px-4 py-2 border text-sm font-medium
                                           ${isActive ? 'z-10 bg-blue-50 border-blue-500 text-blue-600' : 
                                             isDisabled || isNextDisabled ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 
                                             'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'}"
                                           ${isDisabled || isNextDisabled ? 'disabled' : ''}>
                                            ${link.label.replace('&laquo;', '«').replace('&raquo;', '»')}
                                        </a>
                                    `;
                                    }).join('')}
                                </nav>
                            </div>
                        ` : `
                            <div class="text-center py-8 text-gray-500">
                                <i class="fas fa-clock text-gray-300 text-3xl mb-3"></i>
                                <p>No time entries found for the selected period</p>
                            </div>
                        `}
                </div>
            </div>
        </div>
    `;
            }

            // Load pagination page for detailed report
            window.loadDetailedPage = function(page) {
                loadReport(page);
            };
        });
    </script>

    <style>
        .fade-in {
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Disabled button styles */
        button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
    </style>
@endsection
