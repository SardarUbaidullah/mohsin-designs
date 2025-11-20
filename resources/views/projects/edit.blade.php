@extends("admin.layouts.app")

@section("content")
<div class="max-w-6xl mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Edit Project</h1>
            <p class="text-gray-600 mt-2">Update project information and details</p>
        </div>
        <a href="{{ route('projects.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg font-medium transition duration-200 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Projects
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Project Information</h2>
        </div>
        <div class="p-6">
            <form action="{{ route('projects.update', $project->id) }}" method="POST" class="space-y-6" id="projectForm">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Project Name *</label>
                        <input
                            type="text"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('name') border-red-500 @enderror"
                            id="name"
                            name="name"
                            value="{{ old('name', $project->name) }}"
                            required
                            placeholder="Enter project name"
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
<!-- Category Field -->
<div>
    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
    <select
        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('category_id') border-red-500 @enderror"
        id="category_id"
        name="category_id"
    >
        <option value="">Select a Category</option>
        @foreach($categories as $category)
            <option value="{{ $category->id }}" {{ old('category_id', $project->category_id) == $category->id ? 'selected' : '' }} style="color: {{ $category->color }}">
                {{ $category->name }}
            </option>
        @endforeach
    </select>
    @error('category_id')
        <p class="mt-2 text-sm text-red-600 flex items-center">
            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
            </svg>
            {{ $message }}
        </p>
    @enderror
</div>
                    <div>
                        <label for="client_id" class="block text-sm font-medium text-gray-700 mb-2">Client *</label>
                        <select
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('client_id') border-red-500 @enderror"
                            id="client_id"
                            name="client_id"
                            
                        >
                            <option value="">Select a Client</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ old('client_id', $project->client_id) == $client->id ? 'selected' : '' }}>
                                    {{ $client->name }} - {{ $client->company }}
                                </option>
                            @endforeach
                        </select>
                        @error('client_id')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('description') border-red-500 @enderror"
                        id="description"
                        name="description"
                        rows="4"
                        placeholder="Enter project description"
                    >{{ old('description', $project->description) }}</textarea>
                    @error('description')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Date Section with Validations -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Start Date *
                            <span class="text-xs text-gray-500 font-normal">(Cannot be in the past)</span>
                        </label>
                        <input
                            type="date"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('start_date') border-red-500 @enderror"
                            id="start_date"
                            name="start_date"
                            value="{{ old('start_date', $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('Y-m-d') : '') }}"
                            required
                            min="{{ date('Y-m-d') }}"
                        >
                        @error('start_date')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                        <div id="start_date_validation" class="mt-1 text-xs"></div>
                    </div>

                    <div>
                        <label for="due_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Due Date
                            <span class="text-xs text-gray-500 font-normal">(Optional - must be after start date)</span>
                        </label>
                        <input
                            type="date"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('due_date') border-red-500 @enderror"
                            id="due_date"
                            name="due_date"
                            value="{{ old('due_date', $project->due_date ? \Carbon\Carbon::parse($project->due_date)->format('Y-m-d') : '') }}"
                            min="{{ date('Y-m-d') }}"
                        >
                        @error('due_date')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                        <div id="due_date_validation" class="mt-1 text-xs"></div>
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                            id="status"
                            name="status"
                        >
                            <option value="pending" {{ old('status', $project->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="in_progress" {{ old('status', $project->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ old('status', $project->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                    </div>
                </div>

                <!-- Date Validation Summary -->
                <div id="date_validation_summary" class="hidden bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-yellow-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <h4 class="text-sm font-medium text-yellow-800">Date Validation Issues</h4>
                    </div>
                    <ul id="date_validation_errors" class="text-sm text-yellow-700 mt-2 space-y-1"></ul>
                </div>

                @if (Auth::user()->role == 'admin' || Auth::user()->role == 'super_admin')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @if (Auth::user()->role == 'admin')
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                            id="status"
                            name="status"
                        >
                            <option value="pending" {{ old('status', $project->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="in_progress" {{ old('status', $project->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ old('status', $project->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                    </div>
                    @endif

                    @if (Auth::user()->role == 'super_admin')
                    <div>
                        <label for="manager_id" class="block text-sm font-medium text-gray-700 mb-2">Manager</label>
                        <select
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                            id="manager_id"
                            name="manager_id"
                        >
                            <option value="">Select Manager</option>
                            @foreach ($managers as $manager)
                                <option value="{{ $manager->id }}" {{ old('manager_id', $project->manager_id) == $manager->id ? 'selected' : '' }}>
                                    {{ $manager->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                </div>
                @endif

                <!-- Validation Errors Display -->
                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex items-center mb-2">
                            <svg class="w-5 h-5 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            <h3 class="text-sm font-medium text-red-800">Please fix the following errors:</h3>
                        </div>
                        <ul class="text-sm text-red-700 list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('projects.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition duration-200 font-medium">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200 font-medium flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                        </svg>
                        Update Project
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const startDateInput = document.getElementById('start_date');
    const dueDateInput = document.getElementById('due_date');
    const form = document.getElementById('projectForm');

    // Set today's date as minimum for both fields
    const today = new Date().toISOString().split('T')[0];
    startDateInput.min = today;
    dueDateInput.min = today;

    // Add event listeners for real-time validation
    startDateInput.addEventListener('input', validateDates);
    dueDateInput.addEventListener('input', validateDates);
    startDateInput.addEventListener('change', validateDates);
    dueDateInput.addEventListener('change', validateDates);

    // Update due date min when start date changes
    startDateInput.addEventListener('change', function() {
        if (this.value) {
            dueDateInput.min = this.value;
            validateDates();
        }
    });

    // Form submission validation
    form.addEventListener('submit', function(e) {
        if (!validateDates()) {
            e.preventDefault();

            // Scroll to first error
            const firstErrorInput = document.querySelector('.border-red-500');
            if (firstErrorInput) {
                firstErrorInput.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
                firstErrorInput.focus();
            }
        }
    });

    // Initial validation
    validateDates();
});

function validateDates() {
    const startDateInput = document.getElementById('start_date');
    const dueDateInput = document.getElementById('due_date');
    const startDateValidation = document.getElementById('start_date_validation');
    const dueDateValidation = document.getElementById('due_date_validation');
    const validationSummary = document.getElementById('date_validation_summary');
    const validationErrors = document.getElementById('date_validation_errors');

    const startDate = startDateInput.value ? new Date(startDateInput.value) : null;
    const dueDate = dueDateInput.value ? new Date(dueDateInput.value) : null;
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    let errors = [];
    let hasErrors = false;

    // Clear previous validation states
    startDateInput.classList.remove('border-red-500', 'border-green-500');
    dueDateInput.classList.remove('border-red-500', 'border-green-500');
    startDateValidation.innerHTML = '';
    dueDateValidation.innerHTML = '';
    startDateValidation.className = 'mt-1 text-xs';
    dueDateValidation.className = 'mt-1 text-xs';

    // Validate Start Date
    if (!startDateInput.value) {
        startDateInput.classList.add('border-red-500');
        startDateValidation.innerHTML = '<span class="text-red-600">Start date is required</span>';
        errors.push('Start date is required');
        hasErrors = true;
    } else if (startDate < today) {
        startDateInput.classList.add('border-red-500');
        startDateValidation.innerHTML = '<span class="text-red-600">Start date cannot be in the past</span>';
        errors.push('Start date cannot be in the past');
        hasErrors = true;
    } else {
        startDateInput.classList.add('border-green-500');
        startDateValidation.innerHTML = '<span class="text-green-600">? Valid start date</span>';
    }

    // Validate Due Date (only if provided)
    if (dueDateInput.value) {
        if (dueDate < today) {
            dueDateInput.classList.add('border-red-500');
            dueDateValidation.innerHTML = '<span class="text-red-600">Due date cannot be in the past</span>';
            errors.push('Due date cannot be in the past');
            hasErrors = true;
        } else if (startDateInput.value && dueDate <= startDate) {
            dueDateInput.classList.add('border-red-500');
            dueDateValidation.innerHTML = '<span class="text-red-600">Due date must be after start date</span>';
            errors.push('Due date must be after start date');
            hasErrors = true;
        } else {
            dueDateInput.classList.add('border-green-500');

            // Calculate and show project duration if both dates are valid
            if (startDateInput.value && startDateInput.classList.contains('border-green-500')) {
                const timeDiff = dueDate - startDate;
                const daysDiff = Math.ceil(timeDiff / (1000 * 60 * 60 * 24));
                dueDateValidation.innerHTML = `<span class="text-green-600">? Valid due date (${daysDiff} days duration)</span>`;
            } else {
                dueDateValidation.innerHTML = '<span class="text-green-600">? Valid due date</span>';
            }
        }
    } else {
        // Due date is optional, so no validation needed when empty
        dueDateValidation.innerHTML = '<span class="text-gray-500">Optional - no due date set</span>';
    }

    // Show/hide validation summary
    if (errors.length > 0) {
        validationErrors.innerHTML = '';
        errors.forEach(error => {
            const li = document.createElement('li');
            li.className = 'flex items-center';
            li.innerHTML = `
                <svg class="w-4 h-4 mr-2 text-yellow-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                ${error}
            `;
            validationErrors.appendChild(li);
        });
        validationSummary.classList.remove('hidden');
    } else {
        validationSummary.classList.add('hidden');
    }

    return !hasErrors;
}
</script>

<style>
.border-green-500 {
    border-color: #10B981 !important;
}

.border-red-500 {
    border-color: #EF4444 !important;
}

input[type="date"] {
    transition: all 0.3s ease;
}
</style>
@endsection