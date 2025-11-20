<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
        }

        .status-healthy {
            background-color: #19874D;
            color: white;
        }

        .status-warning {
            background-color: #F59522;
            color: white;
        }

        .status-error {
            background-color: #ef4444;
            color: white;
        }

        .status-offline {
            background-color: #D9D9D9;
            color: black;
        }

        .status-active {
            background-color: #19874D;
            color: white;
        }

        .status-inactive {
            background-color: #D9D9D9;
            color: black;
        }

        .bg-primary {
            background-color: #19874D;
        }

        .bg-secondary {
            background-color: #AE9B85;
        }

        .bg-accent {
            background-color: #F59522;
        }

        .bg-muted {
            background-color: #D9D9D9;
        }

        .text-primary {
            color: #19874D;
        }

        .text-secondary {
            color: #AE9B85;
        }

        .text-accent {
            color: #F59522;
        }

        .text-muted {
            color: #6B7280;
        }

        .border-primary {
            border-color: #19874D;
        }

        .border-secondary {
            border-color: #AE9B85;
        }

        .border-accent {
            border-color: #F59522;
        }

        .hover-scale:hover {
            transform: scale(1.02);
        }

        .transition-all-custom {
            transition: all 0.3s ease;
        }

        .scrollbar-custom::-webkit-scrollbar {
            width: 6px;
        }

        .scrollbar-custom::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .scrollbar-custom::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
        }

        .scrollbar-custom::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        .sidebar {
            background-color: #ffffff;
            border-right: 1px solid #e5e7eb;
        }

        .sidebar-foreground {
            color: #111827;
        }

        .sidebar-accent {
            background-color: #f3f4f6;
        }

        .sidebar-border {
            border-color: #e5e7eb;
        }

        .muted-foreground {
            color: #6B7280;
        }

        .border-border {
            border-color: #e5e7eb;
        }

        .bg-card {
            background-color: #ffffff;
        }

        .text-card-foreground {
            color: #111827;
        }

        .bg-accent-hover {
            background-color: #f3f4f6;
        }

        .text-accent-foreground {
            color: #111827;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }

        .fade-in {
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .slide-in {
            animation: slideIn 0.3s ease-in-out;
        }

        @keyframes slideIn {
            from {
                transform: translateY(-10px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
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



        /* Notification animations */
@keyframes bounce {
    0%, 20%, 53%, 80%, 100% {
        transform: translate3d(0,0,0);
    }
    40%, 43% {
        transform: translate3d(0,-8px,0);
    }
    70% {
        transform: translate3d(0,-4px,0);
    }
    90% {
        transform: translate3d(0,-2px,0);
    }
}

.notification-badge {
    animation: bounce 1s ease infinite;
}

/* Smooth transitions */
.notification-item {
    transition: all 0.2s ease-in-out;
}

.notification-item:hover {
    transform: translateX(4px);
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

    <div class="flex h-screen">

        <!-- Desktop Sidebar -->
        <div class="hidden lg:!block">
            @include('admin.layouts.sidebar')
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white border-b border-border h-16 px-6 flex items-center justify-between">

                <!-- Mobile Menu Button -->
                <button id="mobileMenuButton" class="lg:hidden block">
                    <i class="fa-solid fa-bars text-lg"></i>
                </button>

                <!-- Right side - Icons and Profile -->
                <div class="flex items-center ml-auto space-x-4">
                    <!-- Icons -->
                    <div class="flex items-center space-x-2">
                        <button
                            class="p-2 text-muted-foreground hover:bg-accent-hover hover:text-accent-foreground rounded-lg transition-colors duration-200">
                            <a href="{{ url('/calendar') }}"> <i class="fas fa-calendar"></i></a>
                        </button>

                        <button
                            class="p-2 text-muted-foreground hover:bg-accent-hover hover:text-accent-foreground rounded-lg transition-colors duration-200">
                            <a href="{{ url('/chat') }}"><i class="fas fa-comment"></i></a>
                        </button>
                    </div>
 <div class="flex items-center space-x-4">
                <!-- Chat Notifications -->
                @include('components.notifications')

                <!-- User menu etc -->
            </div>
                    <!-- Divider -->
                    <div class="w-px h-6 bg-border mx-2"></div>

                    <!-- Profile -->
                <!-- Profile Dropdown -->
                @auth
<div class="relative" x-data="{ open: false }">
    <button
        @click="open = !open"
        class="flex items-center space-x-3 focus:outline-none hover:bg-gray-50 rounded-lg p-2 transition-colors duration-200"
    >
        <img
            class="w-8 h-8 rounded-full object-cover"
            src="{{ Auth::user()->profile_photo_url }}"
            alt="User Avatar"
        />
        <div class="text-left">
            <p class="text-sm font-medium text-card-foreground">
              {{Auth::user()->name}}
            </p>
            <p class="text-xs text-muted-foreground capitalize">
                super admin
            </p>
        </div>
        <svg class="w-4 h-4 text-gray-500 transition-transform duration-200"
             :class="{ 'rotate-180': open }"
             fill="none"
             stroke="currentColor"
             viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>

                            <!-- Dropdown Menu -->
                            <div x-show="open" @click.away="open = false"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 transform -translate-y-2"
                                x-transition:enter-end="opacity-100 transform translate-y-0"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 transform translate-y-0"
                                x-transition:leave-end="opacity-0 transform -translate-y-2"
                                class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50"
                                style="display: none;">
                                <!-- User Info -->
                                <div class="px-4 py-2 border-b border-gray-100">
                                    <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                                </div>

                                <!-- Profile Link -->
                                <a href="{{ route('profile.edit') }}"
                                    class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Profile
                                </a>

                                <!-- Divider -->
                                <div class="border-t border-gray-100 my-1"></div>

                                <!-- Logout Form -->
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors duration-200">
                                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                            </path>
                                        </svg>
                                        Log Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endauth
                </div>
            </header>
            <div class="flex-1 p-6 bg-[#FCF8F3] overflow-y-auto">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- User Management Modal -->
    <div id="userModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4 fade-in">
        <div class="bg-white rounded-2xl w-full max-w-md p-6 slide-in">
            <h3 class="text-xl font-semibold text-black mb-4" id="modalTitle">Create New User</h3>

            <form id="userForm">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Full Name
                        </label>
                        <input type="text" id="userName"
                            class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary bg-white"
                            placeholder="Enter full name" required />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Email Address
                        </label>
                        <input type="email" id="userEmail"
                            class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary bg-white"
                            placeholder="Enter email address" required />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Role
                        </label>
                        <select id="userRole"
                            class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary bg-white">
                            <option value="Developer">Developer</option>
                            <option value="Designer">Designer</option>
                            <option value="Project Manager">Project Manager</option>
                            <option value="Admin">Admin</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Status
                        </label>
                        <select id="userStatus"
                            class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary bg-white">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" id="cancelUserBtn"
                        class="px-4 py-2 text-gray-600 hover:text-gray-800 font-medium">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-[#146c3e] font-medium transition-colors">
                        Create User
                    </button>
                </div>
            </form>
        </div>
    </div>

 <script>



        // DOM Elements
        const tabButtons = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.tab-content');
        const searchInput = document.getElementById('searchInput');
        const clearCacheBtn = document.getElementById('clearCacheBtn');
        const systemAlert = document.getElementById('systemAlert');
        const alertMessage = document.getElementById('alertMessage');
        const usersTableBody = document.getElementById('usersTableBody');
        const addUserBtn = document.getElementById('addUserBtn');
        const userModal = document.getElementById('userModal');
        const modalTitle = document.getElementById('modalTitle');
        const userForm = document.getElementById('userForm');
        const cancelUserBtn = document.getElementById('cancelUserBtn');
        const rebootSystemBtn = document.getElementById('rebootSystemBtn');
        const quickActionBtns = document.querySelectorAll('.quick-action-btn');

        // Mobile sidebar elements
        const mobileMenuButton = document.getElementById('mobileMenuButton');
        const mobileSidebar = document.getElementById('mobileSidebar');
        const mobileSidebarOverlay = document.getElementById('mobileSidebarOverlay');
        const closeMobileSidebar = document.getElementById('closeMobileSidebar');

        // Current state
        let currentTab = 'overview';
        let editingUserId = null;

        // Initialize the dashboard
        function initDashboard() {
            // Set up tab switching
            tabButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const tab = button.getAttribute('data-tab');
                    switchTab(tab);
                });
            });

            // Set up search functionality
            if (searchInput) {
                searchInput.addEventListener('input', filterUsers);
            }

            // Set up clear cache button
            if (clearCacheBtn) {
                clearCacheBtn.addEventListener('click', handleClearCache);
            }

            // Set up user management
            if (addUserBtn) {
                addUserBtn.addEventListener('click', () => openUserModal());
            }
            if (cancelUserBtn) {
                cancelUserBtn.addEventListener('click', closeUserModal);
            }
            if (userForm) {
                userForm.addEventListener('submit', handleUserSubmit);
            }

            // Set up system reboot
            if (rebootSystemBtn) {
                rebootSystemBtn.addEventListener('click', handleSystemReboot);
            }

            // Set up quick actions
            quickActionBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    const action = btn.getAttribute('data-action');
                    handleQuickAction(action);
                });
            });

            // Populate users table
            if (usersTableBody) {
                renderUsersTable();
            }

            // Close modal when clicking outside
            userModal.addEventListener('click', (e) => {
                if (e.target === userModal) {
                    closeUserModal();
                }
            });

            // Mobile sidebar functionality
            mobileMenuButton.addEventListener('click', openMobileSidebar);
            closeMobileSidebar.addEventListener('click', closeMobileSidebarFunc);
            mobileSidebarOverlay.addEventListener('click', closeMobileSidebarFunc);

            // Close mobile sidebar when clicking on a link
            const mobileLinks = mobileSidebar.querySelectorAll('a');
            mobileLinks.forEach(link => {
                link.addEventListener('click', closeMobileSidebarFunc);
            });
        }

        // Mobile sidebar functions
        function openMobileSidebar() {
            mobileSidebar.classList.add('active');
            mobileSidebarOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeMobileSidebarFunc() {
            mobileSidebar.classList.remove('active');
            mobileSidebarOverlay.classList.remove('active');
            document.body.style.overflow = '';
        }

        // Switch between tabs
        function switchTab(tab) {
            // Update tab buttons
            tabButtons.forEach(button => {
                if (button.getAttribute('data-tab') === tab) {
                    button.classList.remove('text-gray-600', 'hover:text-black', 'hover:bg-gray-100');
                    button.classList.add('bg-primary', 'text-white');
                } else {
                    button.classList.remove('bg-primary', 'text-white');
                    button.classList.add('text-gray-600', 'hover:text-black', 'hover:bg-gray-100');
                }
            });

            // Update tab contents
            tabContents.forEach(content => {
                if (content.id === `${tab}Tab`) {
                    content.classList.remove('hidden');
                } else {
                    content.classList.add('hidden');
                }
            });

            currentTab = tab;
        }

        // Filter users based on search
        function filterUsers() {
            const searchTerm = searchInput.value.toLowerCase();
            const filteredUsers = teamMembers.filter(user =>
                user.name.toLowerCase().includes(searchTerm) ||
                user.email.toLowerCase().includes(searchTerm) ||
                user.role.toLowerCase().includes(searchTerm)
            );
            renderUsersTable(filteredUsers);
        }

        // Render users table
        function renderUsersTable(users = teamMembers) {
            usersTableBody.innerHTML = '';

            users.forEach(user => {
                const row = document.createElement('tr');
                row.className = 'border-b border-gray-100 hover:bg-gray-50 transition-colors';
                row.innerHTML = `
                    <td class="px-4 py-3">
                        <div class="flex items-center space-x-3">
                            <img
                                src="${user.avatar}"
                                alt="${user.name}"
                                class="w-8 h-8 rounded-full"
                            />
                            <div>
                                <p class="font-medium text-black">${user.name}</p>
                                <p class="text-sm text-gray-500">${user.email}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-secondary text-white">
                            ${user.role}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm text-black">
                        ${user.lastLogin || 'Never'}
                    </td>
                    <td class="px-4 py-3">
                        <span class="status-badge status-${user.status} inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                            <i class="fas fa-${user.status === 'active' ? 'check-circle' : 'times-circle'} mr-1 text-xs"></i>
                            ${user.status.charAt(0).toUpperCase() + user.status.slice(1)}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex justify-end space-x-2">
                            <button
                                onclick="editUser(${user.id})"
                                class="p-1 text-gray-500 hover:text-primary transition-colors"
                                title="Edit User"
                            >
                                <i class="fas fa-edit"></i>
                            </button>
                            <button
                                onclick="deleteUser(${user.id})"
                                class="p-1 text-gray-500 hover:text-red-600 transition-colors"
                                title="Delete User"
                            >
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </td>
                `;
                usersTableBody.appendChild(row);
            });
        }

        // Open user modal for creating or editing
        function openUserModal(user = null) {
            if (user) {
                modalTitle.textContent = 'Edit User';
                document.getElementById('userName').value = user.name;
                document.getElementById('userEmail').value = user.email;
                document.getElementById('userRole').value = user.role;
                document.getElementById('userStatus').value = user.status;
                editingUserId = user.id;
            } else {
                modalTitle.textContent = 'Create New User';
                document.getElementById('userName').value = '';
                document.getElementById('userEmail').value = '';
                document.getElementById('userRole').value = 'Developer';
                document.getElementById('userStatus').value = 'active';
                editingUserId = null;
            }
            userModal.classList.remove('hidden');
        }

        // Close user modal
        function closeUserModal() {
            userModal.classList.add('hidden');
            editingUserId = null;
        }

        // Handle user form submission
        function handleUserSubmit(e) {
            e.preventDefault();

            const userData = {
                name: document.getElementById('userName').value,
                email: document.getElementById('userEmail').value,
                role: document.getElementById('userRole').value,
                status: document.getElementById('userStatus').value
            };

            if (editingUserId) {
                // Update existing user
                const userIndex = teamMembers.findIndex(user => user.id === editingUserId);
                if (userIndex !== -1) {
                    teamMembers[userIndex] = {
                        ...teamMembers[userIndex],
                        ...userData
                    };
                    showAlert('User updated successfully!', 'success');
                }
            } else {
                // Create new user
                const newUser = {
                    id: teamMembers.length + 1,
                    ...userData,
                    lastLogin: 'Never',
                    joinDate: new Date().toISOString().split('T')[0],
                    avatar: 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=150&h=150&fit=crop&crop=face'
                };
                teamMembers.push(newUser);
                showAlert('User created successfully!', 'success');

                // Update stats
                const totalUsersElement = document.getElementById('totalUsers');
                if (totalUsersElement) {
                    totalUsersElement.textContent = teamMembers.length;
                }
            }

            renderUsersTable();
            closeUserModal();
        }

        // Edit user
        function editUser(userId) {
            const user = teamMembers.find(u => u.id === userId);
            if (user) {
                openUserModal(user);
            }
        }

        // Delete user
        function deleteUser(userId) {
            if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
                const userIndex = teamMembers.findIndex(user => user.id === userId);
                if (userIndex !== -1) {
                    teamMembers.splice(userIndex, 1);
                    showAlert('User deleted successfully!', 'success');
                    renderUsersTable();

                    // Update stats
                    const totalUsersElement = document.getElementById('totalUsers');
                    if (totalUsersElement) {
                        totalUsersElement.textContent = teamMembers.length;
                    }
                }
            }
        }

        // Handle clear cache
        function handleClearCache() {
            systemMetrics.serverLoad = Math.max(systemMetrics.serverLoad - 10, 20);
            systemMetrics.memoryUsage = Math.max(systemMetrics.memoryUsage - 15, 40);
            showAlert('Cache cleared successfully!', 'success');
        }

        // Handle system reboot
        function handleSystemReboot() {
            if (confirm('Are you sure you want to reboot the system? This will cause temporary downtime.')) {
                showAlert('System reboot initiated...', 'warning');

                // Simulate reboot process
                setTimeout(() => {
                    showAlert('System reboot completed successfully!', 'success');
                }, 3000);
            }
        }

        // Handle quick actions
        function handleQuickAction(actionId) {
            const messages = {
                'manage-users': 'Switching to users tab',
                'system-settings': 'Opening system settings',
                'security': 'Opening security settings',
                'backup': 'Backup process started...',
                'monitoring': 'Switching to monitoring tab',
                'billing': 'Billing dashboard opened'
            };

            if (actionId === 'manage-users' || actionId === 'monitoring') {
                switchTab(actionId === 'manage-users' ? 'users' : 'monitoring');
            } else {
                showAlert(messages[actionId] || 'Action completed', 'info');
            }
        }

        // Show system alert
        function showAlert(message, type) {
            if (!systemAlert || !alertMessage) return;

            alertMessage.textContent = message;

            // Update alert styling based on type
            systemAlert.className = `mb-6 p-4 rounded-lg border ${type === 'success' ? 'bg-primary border-primary text-white' :
                type === 'warning' ? 'bg-accent border-accent text-white' :
                'bg-secondary border-secondary text-white'} slide-in`;

            // Update icon based on type
            const icon = systemAlert.querySelector('i');
            icon.className = type === 'success' ? 'fas fa-check-circle' :
                type === 'warning' ? 'fas fa-exclamation-triangle' :
                'fas fa-info-circle';

            systemAlert.classList.remove('hidden');

            // Auto-hide after 5 seconds
            setTimeout(() => {
                hideAlert();
            }, 5000);
        }

        // Hide system alert
        function hideAlert() {
            if (systemAlert) {
                systemAlert.classList.add('hidden');
            }
        }

        // Initialize the dashboard when the page loads
        document.addEventListener('DOMContentLoaded', initDashboard);
    </script>
</body>
</html>
