@extends('admin.layouts.app')

@section('title', 'Create Category')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50/30 py-6 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="mb-4 sm:mb-0">
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Create New Category</h1>
                    <p class="text-gray-600 mt-2 text-sm sm:text-base">Add a new category to organize your content effectively</p>
                </div>
                <a href="{{ route('categories.index') }}"
                   class="inline-flex items-center px-4 sm:px-6 py-3 border border-gray-300 text-sm font-medium rounded-xl text-gray-700 bg-white hover:bg-gray-50 transition-all duration-200 shadow-sm hover:shadow-md">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to List
                </a>
            </div>
        </div>

        <!-- Main Card -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-200/60 overflow-hidden">
            <!-- Card Header -->
            <div class="px-6 sm:px-8 py-6 bg-gradient-to-r from-blue-600 to-purple-600">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-white">Category Information</h2>
                        <p class="text-blue-100 text-sm mt-1">Fill in the details below to create a new category</p>
                    </div>
                </div>
            </div>

            <!-- Card Body -->
            <div class="px-6 sm:px-8 py-8">
                <form action="{{ route('categories.store') }}" method="POST" class="space-y-8">
                    @csrf

                    <!-- Name & Color Row -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8">
                        <!-- Category Name -->
                        <div class="space-y-3">
                            <label for="name" class="block text-sm font-semibold text-gray-900">
                                Category Name <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="text"
                                       id="name"
                                       name="name"
                                       value="{{ old('name') }}"
                                       required
                                       class="w-full px-4 py-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 text-gray-900 placeholder-gray-400 bg-white shadow-sm hover:shadow-md"
                                       placeholder="Enter category name">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-4">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </div>
                            </div>
                            @error('name')
                                <p class="text-red-600 text-sm font-medium mt-2 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Color Picker -->
                        <div class="space-y-3">
                            <label for="color" class="block text-sm font-semibold text-gray-900">
                                Category Color <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="color"
                                       id="color"
                                       name="color"
                                       value="{{ old('color', '#3B82F6') }}"
                                       required
                                       class="w-full h-14 px-4 py-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 cursor-pointer shadow-sm hover:shadow-md">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-4">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                                    </svg>
                                </div>
                            </div>
                            @error('color')
                                <p class="text-red-600 text-sm font-medium mt-2 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="space-y-3">
                        <label for="description" class="block text-sm font-semibold text-gray-900">
                            Description
                        </label>
                        <div class="relative">
                            <textarea id="description"
                                      name="description"
                                      rows="4"
                                      class="w-full px-4 py-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 text-gray-900 placeholder-gray-400 bg-white shadow-sm hover:shadow-md resize-none"
                                      placeholder="Describe the purpose of this category...">{{ old('description') }}</textarea>
                            <div class="absolute top-4 right-4">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                                </svg>
                            </div>
                        </div>
                        @error('description')
                            <p class="text-red-600 text-sm font-medium mt-2 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Active Switch -->
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-200">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <label for="is_active" class="text-sm font-semibold text-gray-900 cursor-pointer">
                                    Active Category
                                </label>
                                <p class="text-gray-600 text-xs mt-1">When enabled, this category will be visible throughout the system</p>
                            </div>
                        </div>
                        <div class="relative inline-block w-12 h-6">
                            <input type="checkbox"
                                   id="is_active"
                                   name="is_active"
                                   value="1"
                                   checked
                                   class="sr-only peer">
                            <div class="w-12 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-6 peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 sm:gap-6 pt-6 border-t border-gray-200">
                        <button type="submit"
                                class="flex-1 inline-flex items-center justify-center px-6 sm:px-8 py-4 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Create Category
                        </button>
                        <a href="{{ route('categories.index') }}"
                           class="inline-flex items-center justify-center px-6 sm:px-8 py-4 border-2 border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 hover:border-gray-400 transition-all duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Quick Tips -->
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-2xl p-6">
            <div class="flex items-start space-x-4">
                <div class="w-8 h-8 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-blue-900 mb-2">Category Creation Tips</h3>
                    <ul class="text-sm text-blue-700 space-y-1">
                        <li>• Choose a descriptive name that clearly represents the category content</li>
                        <li>• Use colors to visually distinguish between different categories</li>
                        <li>• Keep descriptions concise but informative for better organization</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom Styles for Color Input -->
<style>
input[type="color"] {
    -webkit-appearance: none;
    appearance: none;
    border: none;
    cursor: pointer;
}

input[type="color"]::-webkit-color-swatch-wrapper {
    padding: 0;
    border-radius: 8px;
}

input[type="color"]::-webkit-color-swatch {
    border: none;
    border-radius: 8px;
}

input[type="color"]::-moz-color-swatch {
    border: none;
    border-radius: 8px;
}
</style>
@endsection
