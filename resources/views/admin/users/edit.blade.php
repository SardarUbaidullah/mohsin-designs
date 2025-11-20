@extends('admin.layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Edit User</h1>
                    <p class="mt-2 text-sm text-gray-600">Update user information and permissions</p>
                </div>
                <a href="{{ route('users.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-primary border border-transparent rounded-md font-semibold text-white hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Users
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Form -->
            <div class="lg:col-span-2">
                <div class="bg-white shadow-lg rounded-xl border border-gray-200 overflow-hidden">
                    <!-- Card Header -->
                    <div class="px-6 py-4 bg-white border-b border-gray-200">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-r from-primary to-primary/80 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-user-edit text-white text-sm"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">User Details</h3>
                                <p class="text-sm text-gray-500">Update basic information and role</p>
                            </div>
                        </div>
                    </div>

                    <!-- Form -->
                    <div class="p-6">
                        <form action="{{ route('users.update', $user->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <!-- Name & Email -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                        Name <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text"
                                           name="name"
                                           id="name"
                                           value="{{ old('name', $user->name) }}"
                                           required
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors duration-200 @error('name') border-red-500 @enderror"
                                           placeholder="Enter full name">
                                    @error('name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                        Email <span class="text-red-500">*</span>
                                    </label>
                                    <input type="email"
                                           name="email"
                                           id="email"
                                           value="{{ old('email', $user->email) }}"
                                           required
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors duration-200 @error('email') border-red-500 @enderror"
                                           placeholder="Enter email address">
                                    @error('email')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Role & Phone -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                                        Role <span class="text-red-500">*</span>
                                    </label>
                                    <select name="role"
                                            id="role"
                                            required
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors duration-200 @error('role') border-red-500 @enderror">
                                        <option value="">Select Role</option>
                                        <option value="super_admin" {{ old('role', $user->role) == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                                        <option value="manager" {{ old('role', $user->role) == 'manager' ? 'selected' : '' }}>Manager</option>
                                        <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>User</option>
                                        <option value="client" {{ old('role', $user->role) == 'client' ? 'selected' : '' }}>Client</option>
                                    </select>
                                    @error('role')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                        Phone
                                    </label>
                                    <input type="text"
                                           name="phone"
                                           id="phone"
                                           value="{{ old('phone', $user->phone) }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors duration-200 @error('phone') border-red-500 @enderror"
                                           placeholder="Enter phone number">
                                    @error('phone')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
<!-- Department Field -->
<div class="mb-4">
    <label for="department" class="block text-sm font-medium text-gray-700 mb-2">Department</label>
    <input type="text"
           name="department"
           id="department"
           value="{{ old('department', $user->department) }}"
           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
           placeholder="Enter department">

    <!-- Optional: Show department suggestions -->
    @if($departments->count() > 0)
    <div class="mt-2">
        <p class="text-xs text-gray-500 mb-1">Suggestions:</p>
        <div class="flex flex-wrap gap-2">
            @foreach($departments as $dept)
                <button type="button"
                        class="department-suggestion text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 px-2 py-1 rounded"
                        data-department="{{ $dept }}">
                    {{ $dept }}
                </button>
            @endforeach
        </div>
    </div>
    @endif
</div>
                            <!-- Client Specific Fields -->
                            <div id="client-fields" class="{{ $user->role == 'client' ? 'block' : 'hidden' }} mb-6">
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                    <div class="flex items-center mb-3">
                                        <i class="fas fa-building text-blue-500 mr-2"></i>
                                        <h4 class="text-sm font-semibold text-blue-800">Client Information</h4>
                                    </div>
                                    <div>
                                        <label for="company" class="block text-sm font-medium text-gray-700 mb-2">
                                            Company
                                        </label>
                                        <input type="text"
                                               name="company"
                                               id="company"
                                               value="{{ old('company', $client->company ?? '') }}"
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors duration-200 @error('company') border-red-500 @enderror"
                                               placeholder="Enter company name">
                                        @error('company')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Password Fields -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                                <div>
                                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                        Password
                                    </label>
                                    <div class="relative">
                                        <input type="password"
                                               name="password"
                                               id="password"
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors duration-200 @error('password') border-red-500 @enderror"
                                               placeholder="Leave blank to keep current">
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
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                        Confirm Password
                                    </label>
                                    <div class="relative">
                                        <input type="password"
                                               name="password_confirmation"
                                               id="password_confirmation"
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors duration-200"
                                               placeholder="Confirm new password">
                                        <button 
                                            type="button" 
                                            id="togglePasswordConfirmation" 
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition duration-200"
                                        >
                                            <svg id="eyeIconConfirmation" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                                <a href="{{ route('users.index') }}"
                                   class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                                    Cancel
                                </a>
                                <button type="submit"
                                        class="inline-flex items-center px-6 py-3 bg-primary border border-transparent rounded-lg font-semibold text-white hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors duration-200 shadow-sm">
                                    <i class="fas fa-save mr-2"></i>
                                    Update User
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sidebar - User Information -->
            <div class="lg:col-span-1">
                <div class="bg-white shadow-lg rounded-xl border border-gray-200 overflow-hidden">
                    <!-- Card Header -->
                    <div class="px-6 py-4 bg-white border-b border-gray-200">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-r from-secondary to-secondary/80 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-info-circle text-white text-sm"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">User Information</h3>
                                <p class="text-sm text-gray-500">Current user details</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <!-- Avatar -->
                        <div class="text-center mb-6">
                            <div class="w-20 h-20 bg-gradient-to-br from-primary to-green-600 rounded-full flex items-center justify-center text-white font-bold text-2xl mx-auto shadow-lg">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <h4 class="mt-3 text-lg font-semibold text-gray-900">{{ $user->name }}</h4>
                            <p class="text-sm text-gray-500">{{ $user->email }}</p>
                        </div>

                        <!-- Information Table -->
                        <div class="space-y-4">
                            <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                <span class="text-sm font-medium text-gray-600">User ID</span>
                                <span class="text-sm text-gray-900 font-mono">{{ $user->id }}</span>
                            </div>

                            <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                <span class="text-sm font-medium text-gray-600">Current Role</span>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                    {{ $user->role == 'super_admin' ? 'bg-purple-100 text-purple-800' :
                                       ($user->role == 'admin' ? 'bg-red-100 text-red-800' :
                                       ($user->role == 'manager' ? 'bg-blue-100 text-blue-800' :
                                       ($user->role == 'client' ? 'bg-green-100 text-green-800' :
                                       'bg-gray-100 text-gray-800'))) }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </div>

                            <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                <span class="text-sm font-medium text-gray-600">Created</span>
                                <span class="text-sm text-gray-900">{{ $user->created_at->format('M d, Y') }}</span>
                            </div>

                            <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                <span class="text-sm font-medium text-gray-600">Last Updated</span>
                                <span class="text-sm text-gray-900">{{ $user->updated_at->format('M d, Y') }}</span>
                            </div>

                            @if($user->role == 'client' && $client)
                            <div class="mt-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                                <h5 class="text-sm font-semibold text-gray-900 mb-3 flex items-center">
                                    <i class="fas fa-building mr-2 text-gray-500"></i>
                                    Client Details
                                </h5>
                                <div class="space-y-3">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-medium text-gray-600">Client ID</span>
                                        <span class="text-sm text-gray-900 font-mono">{{ $client->id }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-medium text-gray-600">Status</span>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $client->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ ucfirst($client->status) }}
                                        </span>
                                    </div>
                                    @if($client->company)
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-medium text-gray-600">Company</span>
                                        <span class="text-sm text-gray-900">{{ $client->company }}</span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role');
    const clientFields = document.getElementById('client-fields');

    function toggleClientFields() {
        if (roleSelect.value === 'client') {
            clientFields.classList.remove('hidden');
            clientFields.classList.add('block');
        } else {
            clientFields.classList.remove('block');
            clientFields.classList.add('hidden');
        }
    }

    // Initial toggle
    toggleClientFields();

    // Toggle on role change
    roleSelect.addEventListener('change', toggleClientFields);

    // Password show/hide functionality
    function setupPasswordToggle(passwordInputId, toggleButtonId, eyeIconId) {
        const toggleButton = document.getElementById(toggleButtonId);
        const passwordInput = document.getElementById(passwordInputId);
        const eyeIcon = document.getElementById(eyeIconId);

        if (toggleButton && passwordInput) {
            toggleButton.addEventListener('click', function() {
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
    }

    // Setup both password fields
    setupPasswordToggle('password', 'togglePassword', 'eyeIcon');
    setupPasswordToggle('password_confirmation', 'togglePasswordConfirmation', 'eyeIconConfirmation');

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
    const passwordInput = document.getElementById('password');
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            validatePassword(this.value);
        });

        // Validate on page load if there's existing value (form validation error)
        if (passwordInput.value) {
            validatePassword(passwordInput.value);
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


// Department suggestions
document.addEventListener('DOMContentLoaded', function() {
    const departmentInput = document.getElementById('department');
    const suggestions = document.querySelectorAll('.department-suggestion');

    suggestions.forEach(button => {
        button.addEventListener('click', function() {
            departmentInput.value = this.getAttribute('data-department');
        });
    });
});
</script>
<style>
/* Smooth transitions for dynamic elements */
#client-fields {
    transition: all 0.3s ease-in-out;
}

/* Custom scrollbar for better UX */
::-webkit-scrollbar {
    width: 6px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>
@endsection