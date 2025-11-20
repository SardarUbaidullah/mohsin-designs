@extends('admin.layouts.app')

@section('title', 'Edit Category')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50/30 py-6 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="mb-4 sm:mb-0">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-semibold shadow-lg" style="background-color: {{ $category->color }}">
                            {{ strtoupper(substr($category->name, 0, 1)) }}
                        </div>
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Edit Category</h1>
                            <p class="text-gray-600 mt-1 text-sm sm:text-base">Update "{{ $category->name }}" category details</p>
                        </div>
                    </div>
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
            <!-- Card Header with Category Color -->
            <div class="px-6 sm:px-8 py-6" style="background: linear-gradient(135deg, {{ $category->color }}20, {{ $category->color }}10); border-left: 4px solid {{ $category->color }};">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-white/50 rounded-xl flex items-center justify-center mr-4 shadow-sm" style="border: 2px solid {{ $category->color }}30;">
                        <svg class="w-6 h-6" style="color: {{ $category->color }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Category Information</h2>
                        <p class="text-gray-600 text-sm mt-1">Update the category details below</p>
                    </div>
                </div>
            </div>

            <!-- Card Body -->
            <div class="px-6 sm:px-8 py-8">
                <form action="{{ route('categories.update', $category) }}" method="POST" class="space-y-8">
                    @csrf
                    @method('PUT')

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
                                       value="{{ old('name', $category->name) }}"
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
                                       value="{{ old('color', $category->color) }}"
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
                                      placeholder="Describe the purpose of this category...">{{ old('description', $category->description) }}</textarea>
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

                    <!-- Active Switch & Category Preview -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8">
                        <!-- Active Switch -->
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-200">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 {{ $category->is_active ? 'bg-green-100' : 'bg-red-100' }} rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 {{ $category->is_active ? 'text-green-600' : 'text-red-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        @if($category->is_active)
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        @endif
                                    </svg>
                                </div>
                                <div>
                                    <label for="is_active" class="text-sm font-semibold text-gray-900 cursor-pointer">
                                        Active Category
                                    </label>
                                    <p class="text-gray-600 text-xs mt-1">When enabled, this category will be visible</p>
                                </div>
                            </div>
                            <div class="relative inline-block w-12 h-6">
                                <input type="checkbox"
                                       id="is_active"
                                       name="is_active"
                                       value="1"
                                       {{ $category->is_active ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="w-12 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-6 peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div>
                            </div>
                        </div>

                        <!-- Category Preview -->
                        <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-semibold shadow-sm" style="background-color: {{ $category->color }}" id="colorPreview">
                                    {{ strtoupper(substr(old('name', $category->name), 0, 1)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-600">Preview</p>
                                    <p class="text-lg font-semibold text-gray-900" id="namePreview">{{ old('name', $category->name) }}</p>
                                </div>
                            </div>
                            <div class="mt-3 flex items-center space-x-2">
                                <div class="w-3 h-3 rounded-full" style="background-color: {{ $category->color }}" id="dotPreview"></div>
                                <span class="text-xs text-gray-500 font-mono" id="hexPreview">{{ $category->color }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 sm:gap-6 pt-6 border-t border-gray-200">
                        <button type="submit"
                                class="flex-1 inline-flex items-center justify-center px-6 sm:px-8 py-4 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Update Category
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

        <!-- Quick Stats -->
        <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200/60 p-6">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Created</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $category->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg border border-gray-200/60 p-6">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Last Updated</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $category->updated_at->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Interactive Script for Live Preview -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.getElementById('name');
    const colorInput = document.getElementById('color');
    const namePreview = document.getElementById('namePreview');
    const colorPreview = document.getElementById('colorPreview');
    const dotPreview = document.getElementById('dotPreview');
    const hexPreview = document.getElementById('hexPreview');

    // Update preview when name changes
    nameInput.addEventListener('input', function() {
        const name = this.value || '{{ $category->name }}';
        namePreview.textContent = name;
        colorPreview.textContent = name.charAt(0).toUpperCase();
    });

    // Update preview when color changes
    colorInput.addEventListener('input', function() {
        const color = this.value;
        colorPreview.style.backgroundColor = color;
        dotPreview.style.backgroundColor = color;
        hexPreview.textContent = color;
    });

    // Custom color input styling
    const style = document.createElement('style');
    style.textContent = `
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
    `;
    document.head.appendChild(style);
});
</script>
@endsection
