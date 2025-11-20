@extends('team.app')

@section('content')
<div class="min-h-screen bg-gray-50/30 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div class="mb-6 lg:mb-0">
                    <div class="flex items-center space-x-3 mb-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-user text-white text-lg"></i>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900 tracking-tight">My Profile</h1>
                            <p class="text-gray-600 mt-1 flex items-center">
                                <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                Manage your account information and security
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="flex flex-wrap gap-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-900">
                            {{ \App\Models\Tasks::where('assigned_to', $user->id)->count() }}
                        </div>
                        <div class="text-sm text-gray-500">Total Tasks</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">
                            {{ \App\Models\Tasks::where('assigned_to', $user->id)->where('status', 'done')->count() }}
                        </div>
                        <div class="text-sm text-gray-500">Completed</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-orange-600">
                            {{ \App\Models\Tasks::where('assigned_to', $user->id)->whereIn('status', ['todo', 'in_progress'])->count() }}
                        </div>
                        <div class="text-sm text-gray-500">Pending</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
            <!-- Left Column - Profile & Stats -->
            <div class="space-y-6">
                <!-- Profile Card -->
                <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden">
                    <div class="p-6 text-center">
                        <div class="relative inline-block mb-4">
                            <img class="w-24 h-24 rounded-2xl mx-auto border-4 border-white shadow-lg"
                                 src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&size=120&background=8B5CF6&color=fff&bold=true&font-size=0.5"
                                 alt="{{ $user->name }}">
                            <div class="absolute -bottom-2 -right-2 w-8 h-8 bg-green-500 rounded-full border-4 border-white flex items-center justify-center">
                                <i class="fas fa-check text-white text-xs"></i>
                            </div>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-1">{{ $user->name }}</h3>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800 mb-3">
                            <i class="fas fa-user-shield mr-1.5 text-xs"></i>
                            {{ ucfirst($user->role) }}
                        </span>
                        <p class="text-gray-600 text-sm mb-4">{{ $user->email }}</p>
                        <div class="flex items-center justify-center text-sm text-gray-500">
                            <i class="fas fa-calendar-day mr-1.5 text-xs"></i>
                            Member since {{ $user->created_at->format('M d, Y') }}
                        </div>
                    </div>
                </div>

                <!-- Performance Stats -->
                <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-200/60 bg-gradient-to-r from-gray-50 to-white">
                        <h3 class="font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-chart-line text-blue-600 mr-3"></i>
                            Performance Overview
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            @php
                                $totalTasks = \App\Models\Tasks::where('assigned_to', $user->id)->count();
                                $completedTasks = \App\Models\Tasks::where('assigned_to', $user->id)->where('status', 'done')->count();
                                $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
                            @endphp

                            <div>
                                <div class="flex justify-between text-sm mb-2">
                                    <span class="font-medium text-gray-700">Task Completion</span>
                                    <span class="font-semibold text-blue-600">{{ $completionRate }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="h-2 rounded-full bg-gradient-to-r from-blue-500 to-blue-600"
                                         style="width: {{ $completionRate }}%"></div>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4 text-center">
                                <div class="p-3 bg-green-50/50 rounded-xl border border-green-200/60">
                                    <div class="text-lg font-bold text-green-700">{{ $completedTasks }}</div>
                                    <div class="text-xs text-green-600 font-medium">Completed</div>
                                </div>
                                <div class="p-3 bg-orange-50/50 rounded-xl border border-orange-200/60">
                                    <div class="text-lg font-bold text-orange-700">
                                        {{ $totalTasks - $completedTasks }}
                                    </div>
                                    <div class="text-xs text-orange-600 font-medium">In Progress</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Forms -->
            <div class="xl:col-span-2 space-y-6">
                <!-- Personal Information -->
                <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-200/60 bg-gradient-to-r from-blue-50 to-blue-100/30">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-user-circle text-blue-600 mr-3"></i>
                            Personal Information
                        </h2>
                    </div>
                    <div class="p-6">
                        <form>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                        <i class="fas fa-user mr-2 text-blue-500 text-xs"></i>
                                        Full Name
                                    </label>
                                    <input type="text" value="{{ $user->name }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-xl bg-gray-50/50 text-gray-600 focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-all duration-200"
                                           readonly>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                        <i class="fas fa-envelope mr-2 text-blue-500 text-xs"></i>
                                        Email Address
                                    </label>
                                    <input type="email" value="{{ $user->email }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-xl bg-gray-50/50 text-gray-600 focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-all duration-200"
                                           readonly>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                        <i class="fas fa-user-tag mr-2 text-blue-500 text-xs"></i>
                                        Role
                                    </label>
                                    <input type="text" value="{{ ucfirst($user->role) }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-xl bg-gray-50/50 text-gray-600 capitalize"
                                           readonly>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                        <i class="fas fa-calendar-plus mr-2 text-blue-500 text-xs"></i>
                                        Member Since
                                    </label>
                                    <input type="text" value="{{ $user->created_at->format('F d, Y') }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-xl bg-gray-50/50 text-gray-600"
                                           readonly>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Change Password Form -->
                <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-200/60 bg-gradient-to-r from-green-50 to-green-100/30">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-lock text-green-600 mr-3"></i>
                            Change Password
                        </h2>
                    </div>
                    <div class="p-6">
                        <form method="POST" action="{{ route('password.update') }}" id="passwordForm">
                            @csrf
                            @method('PUT')

                            <div class="space-y-4">
                                <!-- Current Password -->
                                <div>
                                    <label for="current_password" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                        <i class="fas fa-key mr-2 text-green-500 text-xs"></i>
                                        Current Password
                                    </label>
                                    <div class="relative">
                                        <input type="password" name="current_password" id="current_password"
                                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-200 focus:border-green-500 transition-all duration-200 @error('current_password') border-red-300 @enderror"
                                               placeholder="Enter your current password" required>
                                        <button type="button" class="absolute right-3 top-3 text-gray-400 hover:text-gray-600 toggle-password" data-target="current_password">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    @error('current_password')
                                        <p class="text-red-600 text-xs mt-2 flex items-center">
                                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <!-- New Password -->
                                <div>
                                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                        <i class="fas fa-lock mr-2 text-green-500 text-xs"></i>
                                        New Password
                                    </label>
                                    <div class="relative">
                                        <input type="password" name="password" id="password"
                                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-200 focus:border-green-500 transition-all duration-200 @error('password') border-red-300 @enderror"
                                               placeholder="Enter new password" required>
                                        <button type="button" class="absolute right-3 top-3 text-gray-400 hover:text-gray-600 toggle-password" data-target="password">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    @error('password')
                                        <p class="text-red-600 text-xs mt-2 flex items-center">
                                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <!-- Confirm Password -->
                                <div>
                                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                        <i class="fas fa-lock mr-2 text-green-500 text-xs"></i>
                                        Confirm New Password
                                    </label>
                                    <div class="relative">
                                        <input type="password" name="password_confirmation" id="password_confirmation"
                                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-200 focus:border-green-500 transition-all duration-200"
                                               placeholder="Confirm new password" required>
                                        <button type="button" class="absolute right-3 top-3 text-gray-400 hover:text-gray-600 toggle-password" data-target="password_confirmation">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Password Requirements -->
                                <div class="bg-gray-50/50 border border-gray-200/60 rounded-xl p-4">
                                    <h4 class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                        <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                                        Password Requirements
                                    </h4>
                                    <ul class="text-xs text-gray-600 space-y-1">
                                        <li class="flex items-center"><i class="fas fa-check text-green-500 mr-2 text-xs"></i> Minimum 8 characters</li>
                                        <li class="flex items-center"><i class="fas fa-check text-green-500 mr-2 text-xs"></i> At least one uppercase letter</li>
                                        <li class="flex items-center"><i class="fas fa-check text-green-500 mr-2 text-xs"></i> At least one number</li>
                                        <li class="flex items-center"><i class="fas fa-check text-green-500 mr-2 text-xs"></i> At least one special character</li>
                                    </ul>
                                </div>

                                <!-- Submit Button -->
                                <div class="flex justify-end pt-4">
                                    <button type="submit"
                                            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                                        <i class="fas fa-save mr-2"></i>
                                        Update Password
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Account Actions -->
                <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-200/60 bg-gradient-to-r from-orange-50 to-orange-100/30">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-cog text-orange-600 mr-3"></i>
                            Account Actions
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <button class="group flex items-center p-4 bg-blue-50 hover:bg-blue-100 border border-blue-200/60 hover:border-blue-300 rounded-xl transition-all duration-200 hover:shadow-md">
                                <div class="w-10 h-10 bg-blue-100 group-hover:bg-blue-600 rounded-lg flex items-center justify-center mr-3 transition-colors duration-200">
                                    <i class="fas fa-bell text-blue-600 group-hover:text-white"></i>
                                </div>
                                <div class="text-left">
                                    <p class="font-semibold text-gray-900">Notification Settings</p>
                                    <p class="text-xs text-gray-500">Manage alerts</p>
                                </div>
                            </button>

                            <button class="group flex items-center p-4 bg-purple-50 hover:bg-purple-100 border border-purple-200/60 hover:border-purple-300 rounded-xl transition-all duration-200 hover:shadow-md">
                                <div class="w-10 h-10 bg-purple-100 group-hover:bg-purple-600 rounded-lg flex items-center justify-center mr-3 transition-colors duration-200">
                                    <i class="fas fa-download text-purple-600 group-hover:text-white"></i>
                                </div>
                                <div class="text-left">
                                    <p class="font-semibold text-gray-900">Export Data</p>
                                    <p class="text-xs text-gray-500">Download your data</p>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Success Message Toast -->
@if (session('status'))
<div id="successToast" class="fixed top-4 right-4 bg-green-500 text-white px-6 py-4 rounded-xl shadow-lg z-50 animate-fade-in">
    <div class="flex items-center space-x-3">
        <i class="fas fa-check-circle text-lg"></i>
        <span class="font-medium">{{ session('status') }}</span>
    </div>
</div>
@endif

<style>
.animate-fade-in {
    animation: fadeIn 0.5s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

<script>
// Toggle password visibility
document.querySelectorAll('.toggle-password').forEach(button => {
    button.addEventListener('click', function() {
        const target = this.getAttribute('data-target');
        const input = document.getElementById(target);
        const icon = this.querySelector('i');

        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'fas fa-eye-slash';
        } else {
            input.type = 'password';
            icon.className = 'fas fa-eye';
        }
    });
});

// Auto-hide success message
const successToast = document.getElementById('successToast');
if (successToast) {
    setTimeout(() => {
        successToast.style.opacity = '0';
        successToast.style.transform = 'translateY(-10px)';
        setTimeout(() => successToast.remove(), 500);
    }, 4000);
}

// Form submission feedback
document.getElementById('passwordForm')?.addEventListener('submit', function(e) {
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;

    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Updating...';
    submitBtn.disabled = true;

    setTimeout(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }, 3000);
});
</script>
@endsection
