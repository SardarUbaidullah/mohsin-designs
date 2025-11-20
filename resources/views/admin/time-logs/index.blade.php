@extends("admin.layouts.app")

@section("content")
<div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-6 py-4 sm:py-6 lg:py-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6 sm:mb-8">
        <div class="flex-1 min-w-0">
            <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 truncate">Time Tracking Reports</h1>
            <p class="text-gray-600 mt-1 sm:mt-2 text-xs sm:text-sm lg:text-base truncate">Comprehensive time tracking analytics and reports</p>
        </div>
        <button id="exportReport" class="bg-green-600 hover:bg-green-700 text-white px-4 sm:px-5 lg:px-6 py-2 sm:py-2.5 lg:py-3 rounded-lg font-medium transition duration-200 flex items-center justify-center shadow-sm text-sm sm:text-base">
            <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1 sm:mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Export Report
        </button>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-3 sm:px-4 py-2 sm:py-3 rounded-lg mb-4 sm:mb-6 flex items-center text-sm sm:text-base">
            <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-3 sm:px-4 py-2 sm:py-3 rounded-lg mb-4 sm:mb-6 flex items-center text-sm sm:text-base">
            <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    <!-- Report Type Selector -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6 mb-6 sm:mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex-1">
                <h2 class="text-lg font-semibold text-gray-900 mb-2">Report Type</h2>
                <div class="flex flex-wrap gap-2" id="reportTypeSelector">
                    <button type="button" data-report="summary" class="report-type-btn bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-200">
                        Time Summary
                    </button>
                    <button type="button" data-report="detailed" class="report-type-btn bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium transition duration-200">
                        Detailed Report
                    </button>
                    <button type="button" data-report="team_activity" class="report-type-btn bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium transition duration-200">
                        Team Activity
                    </button>
                    <button type="button" data-report="weekly_summary" class="report-type-btn bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium transition duration-200">
                        Weekly Summary
                    </button>
                    <button type="button" data-report="user_performance" class="report-type-btn bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium transition duration-200">
                        User Performance
                    </button>
                </div>
            </div>

            <!-- Date Range Filter -->
            <div class="sm:text-right">
                <label for="dateRange" class="block text-sm font-medium text-gray-700 mb-2">Date Range</label>
                <select id="dateRange" class="bg-white border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="7">Last 7 Days</option>
                    <option value="30" selected>Last 30 Days</option>
                    <option value="90">Last 90 Days</option>
                    <option value="365">Last 365 Days</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Loading State -->
    <div id="loadingState" class="hidden bg-white rounded-xl shadow-sm border border-gray-200 p-8 text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
        <p class="text-gray-600">Loading report data...</p>
    </div>

    <!-- Error State -->
    <div id="errorState" class="hidden bg-red-50 border border-red-200 rounded-xl p-6 mb-6">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-red-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <div>
                <h3 class="text-sm font-medium text-red-800" id="errorTitle">Error Loading Report</h3>
                <p class="text-sm text-red-600 mt-1" id="errorMessage"></p>
            </div>
        </div>
    </div>

    <!-- Report Content Area -->
    <div id="reportContent">
        <!-- Default content - will be replaced by JavaScript -->
        <div class="text-center py-12">
            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            <p class="text-gray-500">Select a report type to view analytics</p>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div id="exportModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-4 border w-full max-w-md shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100">
                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div class="mt-2 text-center">
                <h3 class="text-lg font-medium text-gray-900">Export Report</h3>
                <div class="mt-2 px-4 py-3">
                    <label for="exportType" class="block text-sm font-medium text-gray-700 mb-2">Export Format</label>
                    <select id="exportType" class="bg-white border border-gray-300 rounded-lg px-3 py-2 text-sm w-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="json">JSON</option>
                        <option value="csv">CSV</option>
                    </select>
                </div>
            </div>
            <div class="items-center px-4 py-3">
                <button id="confirmExport" class="px-4 py-2 bg-blue-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Export
                </button>
                <button id="cancelExport" class="mt-2 px-4 py-2 bg-gray-300 text-gray-700 text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentReport = 'summary';
    let currentRange = '30';

    // Initialize - load summary report by default
    loadReport(currentReport, currentRange);

    // Event Listeners
    document.getElementById('dateRange').addEventListener('change', function() {
        currentRange = this.value;
        loadReport(currentReport, currentRange);
    });

    // Report type selector
    document.querySelectorAll('.report-type-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            // Update active state
            document.querySelectorAll('.report-type-btn').forEach(b => {
                b.classList.remove('bg-blue-600', 'text-white');
                b.classList.add('bg-white', 'text-gray-700', 'border', 'border-gray-300', 'hover:bg-gray-50');
            });
            this.classList.remove('bg-white', 'text-gray-700', 'border', 'border-gray-300', 'hover:bg-gray-50');
            this.classList.add('bg-blue-600', 'text-white');

            currentReport = this.dataset.report;
            loadReport(currentReport, currentRange);
        });
    });

    // Export functionality
    document.getElementById('exportReport').addEventListener('click', function() {
        document.getElementById('exportModal').classList.remove('hidden');
    });

    document.getElementById('cancelExport').addEventListener('click', function() {
        document.getElementById('exportModal').classList.add('hidden');
    });

    document.getElementById('confirmExport').addEventListener('click', function() {
        const format = document.getElementById('exportType').value;
        exportReport(currentReport, currentRange, format);
        document.getElementById('exportModal').classList.add('hidden');
    });

    // Load report function
    function loadReport(reportType, range, page = 1) {
        showLoading();
        hideError();

        const url = getReportUrl(reportType);
        const params = new URLSearchParams({
            range: range,
            page: page
        });

        fetch(`${url}?${params}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                hideLoading();
                if (data.error) {
                    showError(data.error, data.message);
                } else {
                    renderReport(reportType, data);
                }
            })
            .catch(error => {
                hideLoading();
                showError('Failed to load report', error.message);
                console.error('Error:', error);
            });
    }

    function getReportUrl(reportType) {
        const urls = {
            summary: '/admin/time-reports/summary',
            detailed: '/admin/time-reports/detailed',
            team_activity: '/admin/time-reports/team-activity',
            weekly_summary: '/admin/time-reports/weekly-summary',
            user_performance: '/admin/time-reports/user-performance'
        };
        return urls[reportType] || urls.summary;
    }

    function renderReport(reportType, data) {
        const content = document.getElementById('reportContent');

        switch(reportType) {
            case 'summary':
                content.innerHTML = renderSummaryReport(data);
                break;
            case 'detailed':
                content.innerHTML = renderDetailedReport(data);
                break;
            case 'team_activity':
                content.innerHTML = renderTeamActivityReport(data);
                break;
            case 'weekly_summary':
                content.innerHTML = renderWeeklySummaryReport(data);
                break;
            case 'user_performance':
                content.innerHTML = renderUserPerformanceReport(data);
                break;
            default:
                content.innerHTML = renderSummaryReport(data);
        }
    }

    function renderSummaryReport(data) {
        return `
            <div class="space-y-6">
                <!-- Summary Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5 lg:gap-6">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-4 sm:p-5 lg:p-6 text-white shadow-sm">
                        <div class="flex items-center justify-between">
                            <div class="min-w-0">
                                <p class="text-blue-100 text-xs sm:text-sm font-medium truncate">Total Time</p>
                                <h3 class="text-xl sm:text-2xl lg:text-3xl font-bold mt-1 sm:mt-2">${data.summary.formatted_total_time}</h3>
                                <p class="text-blue-100 text-xs mt-1 sm:mt-2 truncate">${data.summary.period}</p>
                            </div>
                            <div class="bg-blue-400 bg-opacity-50 p-2 sm:p-3 rounded-lg flex-shrink-0 ml-2 sm:ml-3">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-4 sm:p-5 lg:p-6 text-white shadow-sm">
                        <div class="flex items-center justify-between">
                            <div class="min-w-0">
                                <p class="text-green-100 text-xs sm:text-sm font-medium truncate">Tasks Tracked</p>
                                <h3 class="text-xl sm:text-2xl lg:text-3xl font-bold mt-1 sm:mt-2">${data.summary.total_tasks_tracked}</h3>
                                <p class="text-green-100 text-xs mt-1 sm:mt-2 truncate">With time entries</p>
                            </div>
                            <div class="bg-green-400 bg-opacity-50 p-2 sm:p-3 rounded-lg flex-shrink-0 ml-2 sm:ml-3">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl p-4 sm:p-5 lg:p-6 text-white shadow-sm">
                        <div class="flex items-center justify-between">
                            <div class="min-w-0">
                                <p class="text-purple-100 text-xs sm:text-sm font-medium truncate">Team Members</p>
                                <h3 class="text-xl sm:text-2xl lg:text-3xl font-bold mt-1 sm:mt-2">${data.summary.team_members}</h3>
                                <p class="text-purple-100 text-xs mt-1 sm:mt-2 truncate">Active trackers</p>
                            </div>
                            <div class="bg-purple-400 bg-opacity-50 p-2 sm:p-3 rounded-lg flex-shrink-0 ml-2 sm:ml-3">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-xl p-4 sm:p-5 lg:p-6 text-white shadow-sm">
                        <div class="flex items-center justify-between">
                            <div class="min-w-0">
                                <p class="text-orange-100 text-xs sm:text-sm font-medium truncate">Avg. Daily</p>
                                <h3 class="text-xl sm:text-2xl lg:text-3xl font-bold mt-1 sm:mt-2">${data.summary.avg_daily_formatted}</h3>
                                <p class="text-orange-100 text-xs mt-1 sm:mt-2 truncate">Time spent</p>
                            </div>
                            <div class="bg-orange-400 bg-opacity-50 p-2 sm:p-3 rounded-lg flex-shrink-0 ml-2 sm:ml-3">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Time by Team -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Time by Team</h3>
                        ${data.time_by_user && data.time_by_user.length > 0 ? `
                            <div class="space-y-3">
                                ${data.time_by_user.map(user => `
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div class="flex items-center min-w-0">
                                            <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white text-sm font-medium mr-3 flex-shrink-0">
                                                ${user.user.name.charAt(0)}
                                            </div>
                                            <span class="text-sm font-medium text-gray-900 truncate">${user.user.name}</span>
                                        </div>
                                        <span class="text-sm font-semibold text-gray-700">${user.formatted_time}</span>
                                    </div>
                                `).join('')}
                            </div>
                        ` : `
                            <p class="text-gray-500 text-center py-4">No time data available</p>
                        `}
                    </div>

                    <!-- Time by Project -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Time by Project</h3>
                        ${data.time_by_project && data.time_by_project.length > 0 ? `
                            <div class="space-y-3">
                                ${data.time_by_project.map(project => `
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <span class="text-sm font-medium text-gray-900 truncate">${project.project.name}</span>
                                        <span class="text-sm font-semibold text-gray-700">${project.formatted_time}</span>
                                    </div>
                                `).join('')}
                            </div>
                        ` : `
                            <p class="text-gray-500 text-center py-4">No project data available</p>
                        `}
                    </div>
                </div>

                <!-- Top Tasks -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Tasks</h3>
                    ${data.time_by_task && data.time_by_task.length > 0 ? `
                        <div class="space-y-3">
                            ${data.time_by_task.map(task => `
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-medium text-gray-900 truncate">${task.task?.title || 'Unknown Task'}</p>
                                        <p class="text-xs text-gray-500 truncate">${task.task?.project?.name || 'No Project'}</p>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-700 ml-4 flex-shrink-0">${task.formatted_time}</span>
                                </div>
                            `).join('')}
                        </div>
                    ` : `
                        <p class="text-gray-500 text-center py-4">No task data available</p>
                    `}
                </div>
            </div>
        `;
    }

    function renderDetailedReport(data) {
        const currentPage = data.time_logs.current_page || 1;
        const lastPage = data.time_logs.last_page || 1;
        const hasPages = lastPage > 1;

        return `
            <div class="space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Detailed Time Entries</h3>

                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
                        <p class="text-sm text-gray-600">
                            Showing ${data.time_logs.from || 0} to ${data.time_logs.to || 0} of ${data.time_logs.total || 0} entries
                        </p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                ${data.time_logs.data && data.time_logs.data.length > 0 ? data.time_logs.data.map(log => `
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm text-gray-900">${log.task_name}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">${log.user_name}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">${log.project_name}</td>
                                        <td class="px-4 py-3 text-sm font-semibold text-gray-700">${log.formatted_duration}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">${log.date}</td>
                                    </tr>
                                `).join('') : `
                                    <tr>
                                        <td colspan="5" class="px-4 py-3 text-sm text-gray-500 text-center">No time entries found</td>
                                    </tr>
                                `}
                            </tbody>
                        </table>
                    </div>

                    ${hasPages ? `
                    <div class="mt-6 flex items-center justify-between">
                        <div class="flex-1 flex justify-between sm:hidden">
                            ${currentPage > 1 ? `
                                <button onclick="loadReportPage(${currentPage - 1})" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Previous
                                </button>
                            ` : `
                                <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-400 bg-gray-100 cursor-not-allowed">
                                    Previous
                                </span>
                            `}
                            ${currentPage < lastPage ? `
                                <button onclick="loadReportPage(${currentPage + 1})" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Next
                                </button>
                            ` : `
                                <span class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-400 bg-gray-100 cursor-not-allowed">
                                    Next
                                </span>
                            `}
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700">
                                    Showing <span class="font-medium">${data.time_logs.from}</span> to <span class="font-medium">${data.time_logs.to}</span> of <span class="font-medium">${data.time_logs.total}</span> results
                                </p>
                            </div>
                            <div>
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                    ${currentPage > 1 ? `
                                        <button onclick="loadReportPage(${currentPage - 1})" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                            <span class="sr-only">Previous</span>
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    ` : ''}

                                    ${Array.from({length: Math.min(5, lastPage)}, (_, i) => {
                                        let pageNum;
                                        if (lastPage <= 5) {
                                            pageNum = i + 1;
                                        } else if (currentPage <= 3) {
                                            pageNum = i + 1;
                                        } else if (currentPage >= lastPage - 2) {
                                            pageNum = lastPage - 4 + i;
                                        } else {
                                            pageNum = currentPage - 2 + i;
                                        }
                                        return pageNum;
                                    }).map(page => `
                                        ${page === currentPage ? `
                                            <button aria-current="page" class="relative inline-flex items-center px-4 py-2 border border-blue-500 bg-blue-50 text-sm font-medium text-blue-600">
                                                ${page}
                                            </button>
                                        ` : `
                                            <button onclick="loadReportPage(${page})" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                                ${page}
                                            </button>
                                        `}
                                    `).join('')}

                                    ${currentPage < lastPage ? `
                                        <button onclick="loadReportPage(${currentPage + 1})" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                            <span class="sr-only">Next</span>
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    ` : ''}
                                </nav>
                            </div>
                        </div>
                    </div>
                    ` : ''}
                </div>
            </div>
        `;
    }

    function renderTeamActivityReport(data) {
        return `
            <div class="space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Team Activity - ${data.period}</h3>
                    ${data.team_activity && data.team_activity.length > 0 ? `
                        <div class="space-y-4">
                            ${data.team_activity.map(member => `
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white text-sm font-medium mr-3">
                                                ${member.user.name.charAt(0)}
                                            </div>
                                            <div>
                                                <h4 class="text-sm font-semibold text-gray-900">${member.user.name}</h4>
                                                <p class="text-sm text-gray-500">Total: ${member.formatted_total_time}</p>
                                            </div>
                                        </div>
                                        <span class="text-sm font-semibold text-gray-700">${member.time_entries_count} entries</span>
                                    </div>
                                    ${member.daily_activity && member.daily_activity.length > 0 ? `
                                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-2 text-xs">
                                            ${member.daily_activity.slice(0, 5).map(day => `
                                                <div class="text-center p-2 bg-gray-50 rounded">
                                                    <div class="font-medium text-gray-900">${day.formatted_time}</div>
                                                    <div class="text-gray-500">${new Date(day.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' })}</div>
                                                </div>
                                            `).join('')}
                                        </div>
                                    ` : '<p class="text-gray-500 text-sm">No daily activity</p>'}
                                </div>
                            `).join('')}
                        </div>
                    ` : '<p class="text-gray-500 text-center py-4">No team activity data available</p>'}
                </div>
            </div>
        `;
    }

    function renderWeeklySummaryReport(data) {
        return `
            <div class="space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Weekly Summary - Last ${data.period}</h3>
                    ${data.weekly_summary && data.weekly_summary.length > 0 ? `
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Week</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Range</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Time</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Active Users</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Entries</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    ${data.weekly_summary.map(week => `
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 text-sm text-gray-900">Week ${week.week_number}</td>
                                            <td class="px-4 py-3 text-sm text-gray-600">
                                                ${new Date(week.week_start).toLocaleDateString()} - ${new Date(week.week_end).toLocaleDateString()}
                                            </td>
                                            <td class="px-4 py-3 text-sm font-semibold text-gray-700">${week.formatted_time}</td>
                                            <td class="px-4 py-3 text-sm text-gray-600">${week.active_users}</td>
                                            <td class="px-4 py-3 text-sm text-gray-600">${week.total_entries}</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    ` : '<p class="text-gray-500 text-center py-4">No weekly data available</p>'}
                </div>
            </div>
        `;
    }

    function renderUserPerformanceReport(data) {
        return `
            <div class="space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">User Performance - ${data.period}</h3>
                    ${data.users && data.users.length > 0 ? `
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Time</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tasks</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Projects</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg per Task</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Entries</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    ${data.users.map(user => `
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3">
                                                <div class="flex items-center">
                                                    <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white text-sm font-medium mr-3">
                                                        ${user.user.name.charAt(0)}
                                                    </div>
                                                    <div>
                                                        <div class="text-sm font-medium text-gray-900">${user.user.name}</div>
                                                        <div class="text-sm text-gray-500">${user.user.email}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-sm font-semibold text-gray-700">${user.formatted_total_time}</td>
                                            <td class="px-4 py-3 text-sm text-gray-600">${user.tasks_worked_on}</td>
                                            <td class="px-4 py-3 text-sm text-gray-600">${user.projects_worked_on}</td>
                                            <td class="px-4 py-3 text-sm text-gray-600">${formatMinutes(user.avg_minutes_per_task)}</td>
                                            <td class="px-4 py-3 text-sm text-gray-600">${user.time_entries_count}</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    ` : '<p class="text-gray-500 text-center py-4">No user performance data available</p>'}
                </div>
            </div>
        `;
    }

    // Global function for pagination
    window.loadReportPage = function(page) {
        loadReport(currentReport, currentRange, page);
    };

    function exportReport(reportType, range, format) {
        fetch('/admin/time-reports/export', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                type: reportType,
                range: range,
                format: format
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Export failed');
            }
            if (format === 'csv') {
                return response.blob();
            }
            return response.json();
        })
        .then(data => {
            if (format === 'csv') {
                // Create and download CSV file
                const url = window.URL.createObjectURL(data);
                const a = document.createElement('a');
                a.href = url;
                a.download = `time-report-${reportType}-${new Date().toISOString().split('T')[0]}.csv`;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);
            } else {
                // Handle JSON export
                if (data.success) {
                    const blob = new Blob([JSON.stringify(data.data, null, 2)], { type: 'application/json' });
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `time-report-${reportType}-${new Date().toISOString().split('T')[0]}.json`;
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    document.body.removeChild(a);
                } else {
                    showError('Export Failed', data.error);
                }
            }
        })
        .catch(error => {
            showError('Export Failed', error.message);
        });
    }

    function formatMinutes(minutes) {
        if (minutes <= 0) return "0m";
        const hours = Math.floor(minutes / 60);
        const mins = minutes % 60;
        return hours > 0 ? `${hours}h ${mins}m` : `${mins}m`;
    }

    function showLoading() {
        document.getElementById('loadingState').classList.remove('hidden');
        document.getElementById('reportContent').innerHTML = '';
    }

    function hideLoading() {
        document.getElementById('loadingState').classList.add('hidden');
    }

    function showError(title, message) {
        document.getElementById('errorTitle').textContent = title;
        document.getElementById('errorMessage').textContent = message;
        document.getElementById('errorState').classList.remove('hidden');
    }

    function hideError() {
        document.getElementById('errorState').classList.add('hidden');
    }
});
</script>
@endpush
