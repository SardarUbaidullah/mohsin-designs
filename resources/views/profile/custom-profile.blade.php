@php
    $layout = match (Auth::user()->role) {
        'super_admin' => 'admin.layouts.app',
        'admin' => 'manager.layouts.app',
        'user' => 'team.app',
    };
@endphp

@extends($layout)
@section('content')
    <div class="max-w-4xl mx-auto">
        <!-- Success Messages -->
        @if (session('status'))
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-600 mr-3"></i>
                    <p class="text-green-800 font-medium">
                        @switch(session('status'))
                            @case('profile-updated')
                                Profile updated successfully!
                            @break

                            @case('password-updated')
                                Password updated successfully!
                            @break

                            @case('profile-photo-updated')
                                Profile photo updated successfully!
                            @break

                            @case('profile-photo-deleted')
                                Profile photo removed successfully!
                            @break
                        @endswitch
                    </p>
                </div>
            </div>
        @endif

        <!-- Profile Header with Photo -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sm:p-8 mb-6 sm:mb-8">
            <div class="flex flex-col sm:flex-row items-center space-y-4 sm:space-y-0 sm:space-x-6">
                <div class="relative group">
                    <img class="w-20 h-20 sm:w-24 sm:h-24 rounded-full object-cover border-4 border-white shadow-lg"
                        src="{{ Auth::user()->profile_photo_url }}" alt="Profile picture" id="profilePhotoPreview">
                    <div
                        class="absolute inset-0 bg-black bg-opacity-50 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                        <label for="profile_photo" class="cursor-pointer">
                            <i class="fas fa-camera text-white text-lg sm:text-xl"></i>
                        </label>
                    </div>

                    <!-- Profile Photo Upload Form -->
                    <form id="photoUploadForm" method="POST" action="{{ route('profile.photo.update') }}"
                        enctype="multipart/form-data" class="hidden">
                        @csrf
                        <input type="file" name="profile_photo" id="profile_photo" accept="image/*">
                    </form>

                    <!-- Remove Photo Button -->
                    @if (Auth::user()->profile_photo_path)
                        <form method="POST" action="{{ route('profile.photo.delete') }}"
                            class="absolute -bottom-2 -right-2">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="bg-red-500 text-white size-5 flex justify-center items-center rounded-full hover:bg-red-600 transition duration-200"
                                onclick="return confirm('Are you sure you want to remove your profile photo?')">
                                <i class="fas fa-times text-xs"></i>
                            </button>
                        </form>
                    @endif
                </div>
                <div class="text-center sm:text-left">
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">{{ Auth::user()->name }}</h2>
                    <p class="text-gray-600 mt-1 text-sm sm:text-base">{{ Auth::user()->email }}</p>
                    <p class="text-sm text-gray-500 mt-2 capitalize">Role: {{ Auth::user()->role ?? 'User' }}</p>
                    <p class="text-xs text-gray-400 mt-1">Member since {{ Auth::user()->created_at->format('M Y') }}</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8">
            <!-- Update Profile Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
                <h3 class="text-lg sm:text-xl font-semibold text-gray-900 mb-4 sm:mb-6 flex items-center">
                    <i class="fas fa-user-edit text-blue-600 mr-3"></i>
                    Profile Information
                </h3>

                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PATCH')

                    <!-- Name -->
                    <div class="mb-4 sm:mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2" for="name">
                            Full Name
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name', Auth::user()->name) }}"
                            required autocomplete="name"
                            class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 text-sm sm:text-base">
                        @error('name')
                            <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="mb-4 sm:mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2" for="email">
                            Email Address
                        </label>
                        <input type="email" name="email" id="email" value="{{ old('email', Auth::user()->email) }}"
                            required autocomplete="email"
                            class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 text-sm sm:text-base">
                        @error('email')
                            <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <div
                        class="flex flex-col gap-x-3 sm:flex-row sm:items-center sm:justify-between pt-4 border-t border-gray-200">
                        <p class="text-sm text-gray-600 mb-4 sm:mb-0">
                            Update your account's profile information and email address.
                        </p>
                        <button type="submit"
                            class="bg-blue-600 text-nowrap text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg hover:bg-blue-700 transition duration-200 font-medium text-sm w-full sm:w-auto">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>

            <!-- Update Password -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
                <h3 class="text-lg sm:text-xl font-semibold text-gray-900 mb-4 sm:mb-6 flex items-center">
                    <i class="fas fa-lock text-green-600 mr-3"></i>
                    Update Password
                </h3>

                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    @method('PUT')

                    <!-- Current Password -->
                    <div class="mb-4 sm:mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2" for="current_password">
                            Current Password
                        </label>
                        <input type="password" name="current_password" id="current_password" required
                            autocomplete="current-password"
                            class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 text-sm sm:text-base"
                            placeholder="Enter current password">
                        @error('current_password')
                            <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- New Password -->
                    <div class="mb-4 sm:mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2" for="password">
                            New Password
                        </label>
                        <input type="password" name="password" id="password" required autocomplete="new-password"
                            class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 text-sm sm:text-base"
                            placeholder="Enter new password">
                        @error('password')
                            <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-4 sm:mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2" for="password_confirmation">
                            Confirm Password
                        </label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                            autocomplete="new-password"
                            class="w-full px-3 sm:px-4 py-2 sm:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 text-sm sm:text-base"
                            placeholder="Confirm new password">
                    </div>

                    <div
                        class="flex flex-col gap-x-3 sm:flex-row sm:items-center sm:justify-between pt-4 border-t border-gray-200">
                        <p class="text-sm text-gray-600 mb-4 sm:mb-0">
                            Ensure your account is using a long, random password to stay secure.
                        </p>
                        <button type="submit"
                            class="bg-green-600 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg hover:bg-green-700 transition duration-200 font-medium text-sm w-full text-nowrap sm:w-auto">
                            Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Delete Account Section -->
        <div class="bg-white rounded-xl shadow-sm border border-red-200 p-4 sm:p-6 mt-6 sm:mt-8">
            <h3 class="text-lg sm:text-xl font-semibold text-red-700 mb-4 flex items-center">
                <i class="fas fa-exclamation-triangle text-red-600 mr-3"></i>
                Delete Account
            </h3>

            <p class="text-gray-600 mb-4 sm:mb-6 text-sm sm:text-base">
                Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting
                your account, please download any data or information that you wish to retain.
            </p>

            <!-- Delete Account Form -->
            <form method="POST" action="{{ route('profile.destroy') }}">
                @csrf
                @method('DELETE')

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-sm text-gray-600 mb-4 sm:mb-0">
                        Are you sure you want to delete your account?
                    </p>
                    <button type="button" onclick="confirmDelete()"
                        class="bg-red-600 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg hover:bg-red-700 transition duration-200 font-medium text-sm sm:text-base w-full sm:w-auto">
                        Delete Account
                    </button>
                </div>

                <!-- Delete Confirmation Modal -->
                <div id="deleteModal"
                    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50 p-4">
                    <div class="bg-white rounded-xl p-4 sm:p-6 max-w-md w-full mx-auto">
                        <h4 class="text-lg font-semibold text-red-700 mb-4">Confirm Account Deletion</h4>
                        <p class="text-gray-600 mb-4 sm:mb-6 text-sm sm:text-base">This action cannot be undone. All your
                            data will be permanently deleted.</p>
                        <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-4">
                            <button type="button" onclick="closeDeleteModal()"
                                class="flex-1 bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition duration-200 text-sm sm:text-base">
                                Cancel
                            </button>
                            <button type="submit"
                                class="flex-1 bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition duration-200 text-sm sm:text-base">
                                Delete Account
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        // JavaScript for photo upload and modals
        document.addEventListener('DOMContentLoaded', function() {
            // Profile Photo Upload
            const profilePhotoInput = document.getElementById('profile_photo');
            if (profilePhotoInput) {
                profilePhotoInput.addEventListener('change', function(e) {
                    if (this.files && this.files[0]) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            document.getElementById('profilePhotoPreview').src = e.target.result;
                        }
                        reader.readAsDataURL(this.files[0]);

                        // Auto-submit the form when file is selected
                        document.getElementById('photoUploadForm').submit();
                    }
                });
            }

            // Delete Account Modal functions
            window.confirmDelete = function() {
                document.getElementById('deleteModal').classList.remove('hidden');
            }

            window.closeDeleteModal = function() {
                document.getElementById('deleteModal').classList.add('hidden');
            }

            // Close modal when clicking outside
            const deleteModal = document.getElementById('deleteModal');
            if (deleteModal) {
                deleteModal.addEventListener('click', function(e) {
                    if (e.target.id === 'deleteModal') {
                        window.closeDeleteModal();
                    }
                });
            }
        });
    </script>
@endsection
