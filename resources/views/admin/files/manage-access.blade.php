@extends("admin.layouts.app")

@section("content")
<div class="max-w-4xl mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Manage File Access</h1>
            <p class="text-gray-600 mt-2">Control who can access this general file</p>
        </div>
        <a href="{{ route('files.show', $file->id) }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg font-medium transition duration-200 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to File
        </a>
    </div>

    <!-- File Info -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900">{{ $file->file_name }}</h3>
                <p class="text-gray-600">General File • v{{ $file->version }} • {{ $file->readable_size }}</p>
                <p class="text-sm text-gray-500">Uploaded by {{ $file->user->name }} on {{ $file->created_at->format('M d, Y') }}</p>
            </div>
        </div>
    </div>

    <!-- Access Management Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Access Control Settings</h2>
        </div>

        <form action="{{ route('files.update-access', $file->id) }}" method="POST">
            @csrf
            <div class="p-6 space-y-6">

                <!-- Public Access Toggle -->
                <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <div class="flex-1">
                        <h4 class="text-sm font-medium text-blue-900">Public Access</h4>
                        <p class="text-sm text-blue-700 mt-1">
                            When enabled, all users in the system can view and download this file.
                        </p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_public" value="1" class="sr-only peer"
                            {{ $file->is_public ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>

                <!-- User-specific Access -->
                <div class="border-t pt-6">
                    <h4 class="text-lg font-medium text-gray-900 mb-4">User-specific Access</h4>
                    <p class="text-sm text-gray-600 mb-4">
                        Select specific users who can access this file.
                        <span class="font-medium">Note: The uploader and super admins always have access.</span>
                    </p>

                    <div class="max-h-96 overflow-y-auto border border-gray-200 rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <div class="flex items-center">
                                            <input type="checkbox" id="select-all" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                            <label for="select-all" class="ml-2">Select All</label>
                                        </div>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($allUsers as $user)
                                @php
                                    $hasAccess = $file->hasUserAccess($user->id);
                                    $isUploader = $file->user_id === $user->id;
                                    $isSuperAdmin = $user->role === 'super_admin';
                                @endphp
                                <tr class="{{ $hasAccess ? 'bg-green-50' : '' }} hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if(!$isUploader && !$isSuperAdmin)
                                      <input type="checkbox" name="user_ids[]" value="{{ (int)$user->id }}"
    {{ $hasAccess ? 'checked' : '' }}
    class="user-checkbox h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">@else
                                        <div class="w-4 h-4 bg-gray-200 rounded flex items-center justify-center">
                                            <svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs font-medium mr-3">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            {{ $user->role === 'super_admin' ? 'bg-purple-100 text-purple-800' :
                                               ($user->role === 'admin' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800') }}">
                                            {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if($isUploader)
                                        <span class="text-green-600 font-medium">Uploader</span>
                                        @elseif($isSuperAdmin)
                                        <span class="text-purple-600 font-medium">Super Admin</span>
                                        @elseif($hasAccess)
                                        <span class="text-blue-600 font-medium">Has Access</span>
                                        @else
                                        <span class="text-gray-500">No Access</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Current Access Summary -->
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <h5 class="text-sm font-medium text-gray-900 mb-2">Current Access Summary</h5>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600">
                        <div class="flex items-center">
                            <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                            <span>Uploader: <strong>{{ $file->user->name }}</strong></span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-2 h-2 bg-purple-500 rounded-full mr-2"></div>
                            <span>Super Admins: <strong>All</strong></span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-2 h-2 bg-blue-500 rounded-full mr-2"></div>
                            <span>Specific Users: <strong>{{ count($file->accessible_users ?? []) }}</strong></span>
                        </div>
                    </div>
                    @if($file->is_public)
                    <div class="flex items-center mt-2">
                        <div class="w-2 h-2 bg-orange-500 rounded-full mr-2"></div>
                        <span class="text-sm text-orange-600 font-medium">This file is publicly accessible to all users</span>
                    </div>
                    @endif
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('files.show', $file->id) }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition duration-200 font-medium">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200 font-medium flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                        </svg>
                        Update Access
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select all functionality
    const selectAll = document.getElementById('select-all');
    const userCheckboxes = document.querySelectorAll('.user-checkbox');

    if (selectAll) {
        selectAll.addEventListener('change', function() {
            userCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    }

    // Update select all when individual checkboxes change
    userCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const allChecked = Array.from(userCheckboxes).every(cb => cb.checked);
            const someChecked = Array.from(userCheckboxes).some(cb => cb.checked);

            if (selectAll) {
                selectAll.checked = allChecked;
                selectAll.indeterminate = someChecked && !allChecked;
            }
        });
    });
});
</script>
@endsection
