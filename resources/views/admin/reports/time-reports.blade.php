@extends('admin.layouts.app')

@section('title', 'Time Tracking Reports')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center">
                    <h1 class="text-3xl font-bold text-gray-900">Time Tracking Reports</h1>
                </div>

            </div>
        </div>
    </div>

    <!-- Current Timer Alert -->
    <div id="runningTimerAlert" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <!-- Dynamic content -->
    </div>

    <!-- Quick Stats -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center">
                <div class="text-2xl font-bold text-blue-600" id="totalTime">--</div>
                <div class="text-sm text-gray-600 mt-1">Total Time</div>
                <div class="text-xs text-gray-400" id="timePeriod">Last 30 days</div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center">
                <div class="text-2xl font-bold text-green-600" id="totalTasks">--</div>
                <div class="text-sm text-gray-600 mt-1">Tasks Tracked</div>
                <div class="text-xs text-gray-400">With time entries</div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center">
                <div class="text-2xl font-bold text-purple-600" id="teamMembers">--</div>
                <div class="text-sm text-gray-600 mt-1">Team Members</div>
                <div class="text-xs text-gray-400">Active trackers</div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center">
                <div class="text-2xl font-bold text-orange-600" id="avgDaily">--</div>
                <div class="text-sm text-gray-600 mt-1">Avg. Daily</div>
                <div class="text-xs text-gray-400">Time spent</div>
            </div>
        </div>

        <!-- Kanban Style Reports -->
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-8">
            <!-- Time by User -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-users text-blue-500 mr-2"></i>
                        Time by Team
                    </h3>
                </div>
                <div class="p-4" id="timeByUser">
                    <div class="text-center py-8">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-2"></div>
                        <p class="text-gray-500 text-sm">Loading team data...</p>
                    </div>
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
                <div class="p-4" id="timeByProject">
                    <div class="text-center py-8">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-600 mx-auto mb-2"></div>
                        <p class="text-gray-500 text-sm">Loading project data...</p>
                    </div>
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
                <div class="p-4" id="timeByTask">
                    <div class="text-center py-8">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600 mx-auto mb-2"></div>
                        <p class="text-gray-500 text-sm">Loading tasks data...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Reports Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Detailed Time Entries</h3>

                </div>
            </div>
            <div class="p-6">
                <div id="detailedReport">
                    <div class="text-center py-12">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
                        <p class="text-gray-600">Loading detailed report...</p>
                    </div>
                </div>
            </div>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentDateRange = '30';
    let globalTimerInterval = null;

    // Initialize
    loadRunningTimer();
    loadTimeSummary();
    loadDetailedReport();

    // Event listeners
    document.getElementById('refreshBtn').addEventListener('click', refreshAll);
    document.getElementById('dateRange').addEventListener('change', filterReports);
    document.getElementById('exportBtn').addEventListener('click', function() {
        document.getElementById('exportModal').classList.remove('hidden');
    });

    document.getElementById('cancelExport').addEventListener('click', function() {
        document.getElementById('exportModal').classList.add('hidden');
    });

    document.getElementById('confirmExport').addEventListener('click', function() {
        const format = document.getElementById('exportType').value;
        exportReport(format);
        document.getElementById('exportModal').classList.add('hidden');
    });

    // Load running timer
    async function loadRunningTimer() {
        try {
            // Clear any existing interval
            if (globalTimerInterval) {
                clearInterval(globalTimerInterval);
                globalTimerInterval = null;
            }

            const response = await fetch('/admin/time-tracking/running-timer');
            const data = await response.json();

            const alertDiv = document.getElementById('runningTimerAlert');

            if (data.has_running_timer && data.timer) {
                alertDiv.innerHTML = `
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 fade-in">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                                    <i class="fas fa-play text-white text-sm"></i>
                                </div>
                                <div>
                                    <div class="font-semibold text-yellow-800">Timer Running</div>
                                    <div class="text-sm text-yellow-600">${data.timer.task?.title || 'Unknown Task'} - ${data.timer.user?.name || 'Unknown User'}</div>
                                </div>
                            </div>
                            <div class="flex items-center space-x-4">
                                <span class="text-yellow-700 font-mono" id="globalTimer">00:00:00</span>
                                <button onclick="stopGlobalTimer(${data.timer.id})"
                                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-sm">
                                    <i class="fas fa-stop mr-2"></i>Stop Timer
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                startGlobalTimer(data.timer.start_time);
            } else {
                alertDiv.innerHTML = '';
            }
        } catch (error) {
            console.error('Error loading timer:', error);
        }
    }

    // Global timer functions
    function startGlobalTimer(startTime) {
        const start = new Date(startTime);

        // Clear any existing interval
        if (globalTimerInterval) {
            clearInterval(globalTimerInterval);
        }

        globalTimerInterval = setInterval(() => {
            const now = new Date();
            const diff = now - start;

            const hours = Math.floor(diff / (1000 * 60 * 60));
            const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((diff % (1000 * 60)) / 1000);

            const timerElement = document.getElementById('globalTimer');
            if (timerElement) {
                timerElement.textContent =
                    `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            }
        }, 1000);
    }

    window.stopGlobalTimer = async function(timeLogId) {
        try {
            const response = await fetch('/admin/time-tracking/stop-timer', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ time_log_id: timeLogId })
            });

            const data = await response.json();
            if (data.success) {
                showNotification('Timer stopped: ' + data.duration, 'success');
                refreshAll();
            } else {
                showNotification('Error stopping timer: ' + data.message, 'error');
            }
        } catch (error) {
            showNotification('Error stopping timer', 'error');
        }
    };

    // Load time summary
    async function loadTimeSummary() {
        try {
            const response = await fetch(`/admin/time-reports/summary?range=${currentDateRange}`);
            const data = await response.json();

            if (data.error) {
                throw new Error(data.message);
            }

            // Update summary cards
            document.getElementById('totalTime').textContent = data.summary.formatted_total_time;
            document.getElementById('timePeriod').textContent = `Last ${data.summary.period}`;
            document.getElementById('totalTasks').textContent = data.summary.total_tasks_tracked;
            document.getElementById('teamMembers').textContent = data.summary.team_members;
            document.getElementById('avgDaily').textContent = data.summary.avg_daily_formatted;

            // Render Kanban cards
            renderTimeByUser(data.time_by_user);
            renderTimeByProject(data.time_by_project);
            renderTimeByTask(data.time_by_task);

        } catch (error) {
            console.error('Error loading time summary:', error);
            showNotification('Failed to load summary data', 'error');

            // Show error states
            document.getElementById('timeByUser').innerHTML = `
                <div class="text-center py-8 text-red-600">
                    <i class="fas fa-exclamation-triangle text-3xl mb-3"></i>
                    <p class="text-sm">Failed to load team data</p>
                </div>
            `;
            document.getElementById('timeByProject').innerHTML = `
                <div class="text-center py-8 text-red-600">
                    <i class="fas fa-exclamation-triangle text-3xl mb-3"></i>
                    <p class="text-sm">Failed to load project data</p>
                </div>
            `;
            document.getElementById('timeByTask').innerHTML = `
                <div class="text-center py-8 text-red-600">
                    <i class="fas fa-exclamation-triangle text-3xl mb-3"></i>
                    <p class="text-sm">Failed to load task data</p>
                </div>
            `;
        }
    }

    // Render Time by User
    function renderTimeByUser(users) {
        const container = document.getElementById('timeByUser');

        if (!users || users.length === 0) {
            container.innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-users text-gray-300 text-3xl mb-3"></i>
                    <p class="text-gray-500 text-sm">No time data available</p>
                </div>
            `;
            return;
        }

        container.innerHTML = users.map(user => `
            <div class="flex items-center justify-between p-3 mb-2 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                        <span class="text-xs font-medium text-white">${user.user?.name?.charAt(0) || 'U'}</span>
                    </div>
                    <div>
                        <div class="font-medium text-gray-900 text-sm">${user.user?.name || 'Unknown User'}</div>
                        <div class="text-xs text-gray-500">${user.total_hours || 0} hours</div>
                    </div>
                </div>
                <div class="text-right">
                    <div class="font-bold text-blue-600 text-sm">${user.formatted_time || '0h 0m'}</div>
                </div>
            </div>
        `).join('');
    }

    // Render Time by Project
    function renderTimeByProject(projects) {
        const container = document.getElementById('timeByProject');

        if (!projects || projects.length === 0) {
            container.innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-project-diagram text-gray-300 text-3xl mb-3"></i>
                    <p class="text-gray-500 text-sm">No project data available</p>
                </div>
            `;
            return;
        }

        container.innerHTML = projects.map(project => `
            <div class="flex items-center justify-between p-3 mb-2 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                <div class="flex items-center space-x-3 flex-1">
                    <div class="w-2 h-8 bg-green-500 rounded-full"></div>
                    <div class="flex-1 min-w-0">
                        <div class="font-medium text-gray-900 text-sm truncate">${project.project?.name || 'Unknown Project'}</div>
                        <div class="text-xs text-gray-500">${project.total_hours || 0} hours</div>
                    </div>
                </div>
                <div class="text-right">
                    <div class="font-bold text-green-600 text-sm">${project.formatted_time || '0h 0m'}</div>
                </div>
            </div>
        `).join('');
    }

    // Render Time by Task
    function renderTimeByTask(tasks) {
        const container = document.getElementById('timeByTask');

        if (!tasks || tasks.length === 0) {
            container.innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-tasks text-gray-300 text-3xl mb-3"></i>
                    <p class="text-gray-500 text-sm">No task data available</p>
                </div>
            `;
            return;
        }

        container.innerHTML = tasks.map(task => `
            <div class="p-3 mb-2 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                <div class="font-medium text-gray-900 text-sm mb-1 truncate">${task.task?.title || 'Unknown Task'}</div>
                <div class="text-xs text-gray-500 mb-2">${task.task?.project?.name || 'No Project'}</div>
                <div class="flex justify-between items-center">
                    <span class="text-xs text-gray-600">${task.total_hours || 0} hours</span>
                    <span class="font-bold text-purple-600 text-sm">${task.formatted_time || '0h 0m'}</span>
                </div>
            </div>
        `).join('');
    }

    // Load detailed report
    async function loadDetailedReport() {
        try {
            const response = await fetch(`/admin/time-reports/detailed?range=${currentDateRange}`);
            const data = await response.json();

            if (data.error) {
                throw new Error(data.message);
            }

            renderDetailedReport(data.time_logs);

        } catch (error) {
            console.error('Error loading detailed report:', error);
            document.getElementById('detailedReport').innerHTML = `
                <div class="text-center py-8 text-red-600">
                    <i class="fas fa-exclamation-triangle text-3xl mb-3"></i>
                    <p>Failed to load detailed report: ${error.message}</p>
                </div>
            `;
        }
    }

    // Render detailed report
    function renderDetailedReport(timeLogs) {
        const container = document.getElementById('detailedReport');

        if (!timeLogs || !timeLogs.data || timeLogs.data.length === 0) {
            container.innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-clock text-gray-300 text-3xl mb-3"></i>
                    <p class="text-gray-500">No time entries found</p>
                </div>
            `;
            return;
        }

        container.innerHTML = `
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task & Project</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        ${timeLogs.data.map(entry => `
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">${entry.user_name || 'Unknown User'}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">${entry.task_name || 'Unknown Task'}</div>
                                    <div class="text-sm text-gray-500">${entry.project_name || 'No Project'}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 max-w-xs truncate">${entry.description || 'No description'}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">${entry.date || 'Unknown Date'}</div>
                                    <div class="text-sm text-gray-500">${entry.start_time ? entry.start_time.split(' ')[1] : ''}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-green-600">${entry.formatted_duration || '0m'}</div>
                                    <div class="text-sm text-gray-500">${entry.duration_hours || 0} hours</div>
                                </td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
            ${timeLogs.links ? `
            <div class="mt-4 flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Showing ${timeLogs.from || 0} to ${timeLogs.to || 0} of ${timeLogs.total || 0} entries
                </div>
                <div class="flex space-x-2">
                    ${timeLogs.links.map(link => `
                        <a href="${link.url || '#'}" class="px-3 py-1 text-sm ${link.active ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700'} rounded" onclick="event.preventDefault(); loadDetailedPage('${link.url}')">
                            ${link.label.replace('&laquo;', '«').replace('&raquo;', '»')}
                        </a>
                    `).join('')}
                </div>
            </div>
            ` : ''}
        `;
    }

    // Load detailed page from pagination
    window.loadDetailedPage = function(url) {
        if (!url || url === '#' || url.includes('null')) return;

        // Extract page number from URL
        const urlParams = new URLSearchParams(url.split('?')[1]);
        const page = urlParams.get('page') || 1;

        // Reload detailed report with page
        loadDetailedReportWithPage(page);
    };

    async function loadDetailedReportWithPage(page) {
        try {
            const response = await fetch(`/admin/time-reports/detailed?range=${currentDateRange}&page=${page}`);
            const data = await response.json();

            if (data.error) {
                throw new Error(data.message);
            }

            renderDetailedReport(data.time_logs);

        } catch (error) {
            console.error('Error loading detailed report:', error);
            showNotification('Failed to load page', 'error');
        }
    }

    // Utility functions
    function filterReports() {
        currentDateRange = document.getElementById('dateRange').value;
        loadTimeSummary();
        loadDetailedReport();
    }

    function refreshAll() {
        loadRunningTimer();
        loadTimeSummary();
        loadDetailedReport();
        showNotification('Data refreshed successfully', 'success');
    }

    async function exportReport(format = 'json') {
        try {
            const response = await fetch('/admin/time-reports/export', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    type: 'detailed',
                    range: currentDateRange,
                    format: format
                })
            });

            if (format === 'csv') {
                // Handle CSV download
                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `time-report-${new Date().toISOString().split('T')[0]}.csv`;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);
                showNotification('CSV report downloaded successfully', 'success');
            } else {
                // Handle JSON download
                const data = await response.json();
                if (data.success) {
                    const blob = new Blob([JSON.stringify(data.data, null, 2)], { type: 'application/json' });
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `time-report-${new Date().toISOString().split('T')[0]}.json`;
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    document.body.removeChild(a);
                    showNotification('JSON report downloaded successfully', 'success');
                } else {
                    showNotification('Error exporting report: ' + data.error, 'error');
                }
            }
        } catch (error) {
            showNotification('Error exporting report', 'error');
        }
    }

    function showNotification(message, type = 'info') {
        // Create toast notification
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 fade-in ${
            type === 'success' ? 'bg-green-500 text-white' :
            type === 'error' ? 'bg-red-500 text-white' :
            'bg-blue-500 text-white'
        }`;
        toast.innerHTML = `
            <div class="flex items-center space-x-2">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-triangle' : 'fa-info-circle'}"></i>
                <span>${message}</span>
            </div>
        `;

        document.body.appendChild(toast);

        setTimeout(() => {
            toast.remove();
        }, 3000);
    }
});
</script>

<style>
.fade-in {
    animation: fadeIn 0.3s ease-in-out;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
.hover-lift:hover {
    transform: translateY(-2px);
    transition: transform 0.2s ease-in-out;
}

/* Ensure Font Awesome icons display properly */
.fas, .far, .fab {
    font-family: 'Font Awesome 5 Free';
    font-weight: 900;
}
</style>
@endsection
