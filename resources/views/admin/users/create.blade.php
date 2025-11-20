@extends('admin.layouts.app')
@section('content')

<div class="max-w-lg mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Create New User</h1>
        <p class="text-gray-600">Add a new user to the system with appropriate role and permissions</p>
    </div>

    <form action="{{ route('users.store') }}" method="POST" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-6">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
            <input
                type="text"
                name="name"
                value="{{ old('name') }}"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('name') border-red-500 @enderror"
                placeholder="Enter full name"
                required
            >
            @error('name')
                <p class="mt-2 text-sm text-red-600 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
            <input
                type="email"
                name="email"
                value="{{ old('email') }}"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('email') border-red-500 @enderror"
                placeholder="Enter email address"
                required
            >
            @error('email')
                <p class="mt-2 text-sm text-red-600 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Password *</label>
            <div class="relative">
                <input
                    type="password"
                    name="password"
                    id="password"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('password') border-red-500 @enderror"
                    placeholder="Enter password"
                    required
                >
                <button 
                    type="button" 
                    id="togglePassword" 
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition duration-200"
                >
                    <svg id="eyeIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </button>
            </div>
            
            <!-- Password Requirements -->
            <div id="passwordRequirements" class="mt-3 space-y-2">
                <p class="text-xs text-gray-500 font-medium">Password must contain:</p>
                <div class="space-y-1">
                    <div class="flex items-center">
                        <div id="uppercaseCheck" class="w-4 h-4 border border-gray-300 rounded mr-2 flex items-center justify-center">
                            <svg class="w-3 h-3 text-green-500 hidden" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <span class="text-xs text-gray-500">At least one uppercase letter (A-Z)</span>
                    </div>
                    <div class="flex items-center">
                        <div id="lowercaseCheck" class="w-4 h-4 border border-gray-300 rounded mr-2 flex items-center justify-center">
                            <svg class="w-3 h-3 text-green-500 hidden" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <span class="text-xs text-gray-500">At least one lowercase letter (a-z)</span>
                    </div>
                    <div class="flex items-center">
                        <div id="numberCheck" class="w-4 h-4 border border-gray-300 rounded mr-2 flex items-center justify-center">
                            <svg class="w-3 h-3 text-green-500 hidden" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <span class="text-xs text-gray-500">At least one number (0-9)</span>
                    </div>
                    <div class="flex items-center">
                        <div id="specialCheck" class="w-4 h-4 border border-gray-300 rounded mr-2 flex items-center justify-center">
                            <svg class="w-3 h-3 text-green-500 hidden" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <span class="text-xs text-gray-500">At least one special character (!@#$%^&* etc.)</span>
                    </div>
                    <div class="flex items-center">
                        <div id="lengthCheck" class="w-4 h-4 border border-gray-300 rounded mr-2 flex items-center justify-center">
                            <svg class="w-3 h-3 text-green-500 hidden" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <span class="text-xs text-gray-500">At least 8 characters long</span>
                    </div>
                </div>
            </div>
            
            @error('password')
                <p class="mt-2 text-sm text-red-600 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">User Role *</label>
            <select
                name="role"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('role') border-red-500 @enderror"
                required
            >
                <option value="">Select a role</option>
                <option value="super_admin" {{ old('role') == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Manager</option>
                <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>Team Member</option>
                <option value="client" {{ old('role') == 'client' ? 'selected' : '' }}>Client</option>
            </select>
            @error('role')
                <p class="mt-2 text-sm text-red-600 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
            <input
                type="text"
                name="department"
                value="{{ old('department') }}"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                placeholder="Enter department name (optional)"
                list="department_suggestions"
            >
            <datalist id="department_suggestions">
                @foreach($departments ?? [] as $dept)
                    <option value="{{ $dept }}">
                @endforeach
            </datalist>
            <p class="text-xs text-gray-500 mt-1">Start typing to see existing departments or enter a new one</p>
        </div>

        <!-- Client Information (Only show when role is client) -->
        <div id="client-info" class="hidden bg-blue-50 p-4 rounded-lg border border-blue-200">
            <h4 class="text-sm font-medium text-blue-900 mb-3">Client Information</h4>
            <p class="text-sm text-blue-700 mb-3">
                When creating a client user, a new client record will be automatically created using the user's name and email.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Company Name</label>
                    <input
                        type="text"
                        name="company"
                        value="{{ old('company') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                        placeholder="Optional company name"
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                    <input
                        type="tel"
                        name="phone"
                        value="{{ old('phone') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                        placeholder="Optional phone number"
                    >
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
            <a href="{{ route('users.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition duration-200 font-medium">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition duration-200 font-medium flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Create User
            </button>
        </div>
    </form>
</div>

<script>
    // Password show/hide functionality
    document.addEventListener('DOMContentLoaded', function() {
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');

        if (togglePassword && passwordInput) {
            togglePassword.addEventListener('click', function() {
                // Toggle the type attribute
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                // Toggle the eye icon
                if (type === 'text') {
                    eyeIcon.innerHTML = `
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                    `;
                } else {
                    eyeIcon.innerHTML = `
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    `;
                }
            });
        }

        // Password validation functionality
        function validatePassword(password) {
            const uppercaseCheck = document.getElementById('uppercaseCheck');
            const lowercaseCheck = document.getElementById('lowercaseCheck');
            const numberCheck = document.getElementById('numberCheck');
            const specialCheck = document.getElementById('specialCheck');
            const lengthCheck = document.getElementById('lengthCheck');

            // Check uppercase
            const hasUppercase = /[A-Z]/.test(password);
            updateCheck(uppercaseCheck, hasUppercase);

            // Check lowercase
            const hasLowercase = /[a-z]/.test(password);
            updateCheck(lowercaseCheck, hasLowercase);

            // Check number
            const hasNumber = /[0-9]/.test(password);
            updateCheck(numberCheck, hasNumber);

            // Check special character
            const hasSpecial = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password);
            updateCheck(specialCheck, hasSpecial);

            // Check length
            const hasLength = password.length >= 8;
            updateCheck(lengthCheck, hasLength);
        }

        function updateCheck(element, isValid) {
            const checkIcon = element.querySelector('svg');
            if (isValid) {
                element.classList.add('border-green-500', 'bg-green-50');
                element.classList.remove('border-gray-300');
                checkIcon.classList.remove('hidden');
            } else {
                element.classList.remove('border-green-500', 'bg-green-50');
                element.classList.add('border-gray-300');
                checkIcon.classList.add('hidden');
            }
        }

        // Add event listener for password input
        if (passwordInput) {
            passwordInput.addEventListener('input', function() {
                validatePassword(this.value);
            });

            // Validate on page load if there's existing value (form validation error)
            if (passwordInput.value) {
                validatePassword(passwordInput.value);
            }
        }

        // Show/hide client info based on role selection
        const roleSelect = document.querySelector('select[name="role"]');
        if (roleSelect) {
            roleSelect.addEventListener('change', function() {
                const clientInfo = document.getElementById('client-info');
                if (this.value === 'client') {
                    clientInfo.classList.remove('hidden');
                } else {
                    clientInfo.classList.add('hidden');
                }
            });

            // Trigger on page load in case of validation errors
            if (roleSelect.value === 'client') {
                document.getElementById('client-info').classList.remove('hidden');
            }
        }
    });
</script>

<script>
// Department Management
document.addEventListener('DOMContentLoaded', function() {
    const departmentSelect = document.getElementById('department_select');
    const customDeptBtn = document.getElementById('custom_department_btn');
    const customDeptInput = document.getElementById('custom_department_input');
    const customDeptField = document.getElementById('custom_department');

    // Toggle custom department input
    if (customDeptBtn && customDeptInput) {
        customDeptBtn.addEventListener('click', function() {
            customDeptInput.classList.toggle('hidden');
            if (!customDeptInput.classList.contains('hidden')) {
                customDeptField.focus();
                departmentSelect.value = '';
            }
        });

        // When custom department is entered, update the select
        customDeptField.addEventListener('input', function() {
            if (this.value.trim() !== '') {
                departmentSelect.value = '';
            }
        });

        // When a department is selected from dropdown, clear custom input
        departmentSelect.addEventListener('change', function() {
            if (this.value !== '') {
                customDeptField.value = '';
                customDeptInput.classList.add('hidden');
            }
        });
    }

    // Professional Filters
    const departmentFilter = document.getElementById('department_filter');
    const roleFilter = document.getElementById('role_filter');
    const resetFilters = document.getElementById('reset_filters');

    function applyFilters() {
        const department = departmentFilter.value;
        const role = roleFilter.value;

        let url = new URL(window.location.href);
        let params = new URLSearchParams();

        if (department !== 'all') {
            params.set('department', department);
        }

        if (role !== 'all') {
            params.set('role', role);
        }

        const queryString = params.toString();
        window.location.href = queryString ? `${url.pathname}?${queryString}` : url.pathname;
    }

    if (departmentFilter) {
        departmentFilter.addEventListener('change', applyFilters);
    }

    if (roleFilter) {
        roleFilter.addEventListener('change', applyFilters);
    }

    if (resetFilters) {
        resetFilters.addEventListener('click', function() {
            window.location.href = "{{ route('users.index') }}";
        });
    }

    // Mobile filter functionality
    const filterTabs = document.querySelectorAll('.filter-tab');
    const userColumns = document.querySelectorAll('.user-column');

    function initMobileView() {
        if (window.innerWidth < 1024) {
            userColumns.forEach((col, index) => {
                if (index === 0) {
                    col.style.display = 'block';
                } else {
                    col.style.display = 'none';
                }
            });
        } else {
            userColumns.forEach(col => {
                col.style.display = 'block';
            });
        }
    }

    if (filterTabs.length > 0) {
        filterTabs.forEach(tab => {
            tab.addEventListener('click', function() {
                const role = this.getAttribute('data-role');

                // Update active tab
                filterTabs.forEach(t => {
                    t.classList.remove('active', 'bg-purple-100', 'text-purple-800');
                    t.classList.add('text-gray-600', 'hover:bg-gray-100');
                });
                this.classList.remove('text-gray-600', 'hover:bg-gray-100');
                this.classList.add('active', 'bg-purple-100', 'text-purple-800');

                // Show selected column, hide others on mobile
                if (window.innerWidth < 1024) {
                    userColumns.forEach(col => {
                        if (col.getAttribute('data-role') === role) {
                            col.style.display = 'block';
                        } else {
                            col.style.display = 'none';
                        }
                    });
                }
            });
        });

        window.addEventListener('resize', initMobileView);
        initMobileView();
    }
});

// Form submission - handle custom department
document.querySelector('form')?.addEventListener('submit', function(e) {
    const customDeptField = document.getElementById('custom_department');
    const departmentSelect = document.getElementById('department_select');

    if (customDeptField && customDeptField.value.trim() !== '') {
        // Create a hidden input to submit the custom department
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'department';
        hiddenInput.value = customDeptField.value.trim();
        this.appendChild(hiddenInput);

        // Disable the original select to avoid conflict
        if (departmentSelect) {
            departmentSelect.disabled = true;
        }
    }
});
</script>
@endsection