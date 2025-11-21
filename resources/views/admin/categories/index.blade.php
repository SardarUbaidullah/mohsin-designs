@extends('admin.layouts.app')

@section('title', 'Categories')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50/30 py-6 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <!-- Header Section -->
            <div class="mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div class="mb-4 sm:mb-0 max-w-full">
                        <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 truncate">Categories Management
                        </h1>
                        <p class="text-gray-600 mt-2 text-sm sm:text-base truncate max-w-full">Organize and manage your
                            content categories efficiently</p>
                    </div>
                    <div class="flex items-center space-x-2 sm:space-x-4">
                        <!-- View Toggle -->
                        <div class="flex items-center bg-white rounded-xl shadow-sm border border-gray-200 p-1">
                            <button id="gridViewBtn"
                                class="p-2 rounded-lg text-blue-600 bg-blue-100 transition-all duration-200 view-toggle"
                                data-view="grid">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                </svg>
                            </button>
                            <button id="listViewBtn"
                                class="p-2 rounded-lg text-gray-700 hover:bg-gray-100 transition-all duration-200 view-toggle"
                                data-view="list">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                                </svg>
                            </button>
                        </div>

                        <a href="{{ route('categories.create') }}"
                            class="inline-flex items-center px-3 sm:px-4 lg:px-6 py-2 sm:py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-medium rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 text-sm sm:text-base">
                            <svg class="w-4 h-4 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            <span class="truncate">Add New Category</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Success Message -->
            @if (session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 rounded-2xl p-4 sm:p-6 shadow-sm">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-3 flex-1 min-w-0">
                            <p class="text-sm font-medium text-green-800 truncate">{{ session('success') }}</p>
                        </div>
                        <button type="button"
                            class="ml-auto -mx-1.5 -my-1.5 bg-green-50 text-green-500 rounded-lg p-1.5 hover:bg-green-100 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            @endif

            <!-- Categories Grid View -->
            <div id="gridView" class="categories-view">
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-6">
                    @forelse($categories as $category)
                        <div
                            class="category-card bg-white rounded-2xl shadow-lg border border-gray-200/60 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 overflow-hidden">
                            <!-- Color Header -->
                            <div class="h-3" style="background-color: {{ $category->color }}"></div>

                            <div class="p-4 sm:p-6">
                                <!-- Header -->
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex items-center space-x-2 sm:space-x-3 min-w-0">
                                        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl flex items-center justify-center text-white font-semibold shadow-sm flex-shrink-0"
                                            style="background-color: {{ $category->color }}">
                                            {{ strtoupper(substr($category->name, 0, 1)) }}
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <h3 class="text-base sm:text-lg font-semibold text-gray-900 truncate"
                                                title="{{ $category->name }}">{{ $category->name }}</h3>
                                            <p class="text-xs sm:text-sm text-gray-500 truncate">ID: {{ $category->id }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2 flex-shrink-0 ml-2">
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $category->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $category->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Description -->
                                @if ($category->description)
                                    <div class="mb-4 w-full">
                                        <p class="text-xs sm:text-sm text-gray-600 truncate"
                                            title="{{ $category->description }}">
                                            {{ $category->description ?: 'No description available' }}
                                        </p>
                                    </div>
                                @else
                                    <div class="mb-4">
                                        <p class="text-xs sm:text-sm text-gray-400 italic">No description provided</p>
                                    </div>
                                @endif

                                <!-- Meta Info -->
                                <div class="flex items-center justify-between text-xs sm:text-sm text-gray-500 mb-4">
                                    <div class="flex items-center space-x-2 sm:space-x-4">
                                        <span class="flex items-center truncate">
                                            <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 flex-shrink-0" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <span class="truncate">{{ $category->created_at->format('M d, Y') }}</span>
                                        </span>
                                    </div>
                                    <div class="flex items-center space-x-1 flex-shrink-0 ml-2">
                                        <div class="w-3 h-3 rounded-full" style="background-color: {{ $category->color }}">
                                        </div>
                                        <span class="text-xs font-mono truncate max-w-[60px] sm:max-w-none"
                                            title="{{ $category->color }}">{{ $category->color }}</span>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                                    <div class="flex space-x-1 sm:space-x-2 min-w-0">
                                        <a href="{{ route('categories.edit', $category) }}"
                                            class="inline-flex items-center px-2 sm:px-3 py-1.5 sm:py-2 border border-gray-300 text-xs sm:text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-all duration-200 shadow-sm hover:shadow truncate">
                                            <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 flex-shrink-0" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            <span class="truncate">Edit</span>
                                        </a>

                                        <button type="button" data-id="{{ $category->id }}"
                                            class="toggle-status inline-flex items-center px-2 sm:px-3 py-1.5 sm:py-2 border border-gray-300 text-xs sm:text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-all duration-200 shadow-sm hover:shadow truncate">
                                            <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 flex-shrink-0" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                            </svg>
                                            <span
                                                class="truncate">{{ $category->is_active ? 'Deactivate' : 'Activate' }}</span>
                                        </button>
                                    </div>

                                    <form action="{{ route('categories.destroy', $category) }}" method="POST"
                                        class="inline flex-shrink-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            onclick="return confirm('Are you sure you want to delete this category?')"
                                            class="inline-flex items-center p-1.5 sm:p-2 border border-red-300 text-red-600 bg-white hover:bg-red-50 rounded-lg transition-all duration-200 shadow-sm hover:shadow">
                                            <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <!-- Empty State -->
                        <div class="col-span-full">
                            <div
                                class="bg-white rounded-2xl shadow-lg border border-gray-200/60 text-center py-8 sm:py-12 px-4 sm:px-6">
                                <div
                                    class="w-12 h-12 sm:w-16 sm:h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-3 sm:mb-4 shadow-sm">
                                    <svg class="w-6 h-6 sm:w-8 sm:h-8 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-2 truncate">No categories
                                    found</h3>
                                <p class="text-gray-600 mb-4 sm:mb-6 max-w-md mx-auto text-sm sm:text-base truncate">Get
                                    started by creating your first category to organize your content</p>
                                <a href="{{ route('categories.create') }}"
                                    class="inline-flex items-center px-4 sm:px-6 py-2 sm:py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-medium rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 text-sm sm:text-base">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    <span class="truncate">Create First Category</span>
                                </a>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Categories List View -->
            <div id="listView" class="categories-view hidden">
                <div class="bg-white rounded-2xl shadow-lg border border-gray-200/60 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[600px]">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider truncate">
                                        Category</th>
                                    <th
                                        class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider truncate">
                                        Description</th>
                                    <th
                                        class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider truncate">
                                        Status</th>
                                    <th
                                        class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider truncate">
                                        Color</th>
                                    <th
                                        class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider truncate">
                                        Created</th>
                                    <th
                                        class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider truncate">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($categories as $category)
                                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center min-w-0">
                                                <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg flex items-center justify-center text-white font-semibold shadow-sm mr-2 sm:mr-3 flex-shrink-0"
                                                    style="background-color: {{ $category->color }}">
                                                    {{ strtoupper(substr($category->name, 0, 1)) }}
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <div class="text-sm font-medium text-gray-900 truncate max-w-[120px] sm:max-w-xs"
                                                        title="{{ $category->name }}">{{ $category->name }}</div>
                                                    <div class="text-xs text-gray-500 truncate">ID: {{ $category->id }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 sm:px-6 py-4">
                                            <div class="min-w-0 max-w-[200px]">
                                                @if ($category->description)
                                                    <div class="text-xs sm:text-sm text-gray-600 truncate"
                                                        title="{{ $category->description }}">
                                                        {{ $category->description }}
                                                    </div>
                                                @else
                                                    <div class="text-xs sm:text-sm text-gray-400 italic">No description
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $category->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} truncate">
                                                {{ $category->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="w-3 h-3 rounded-full mr-2 flex-shrink-0"
                                                    style="background-color: {{ $category->color }}"></div>
                                                <span
                                                    class="text-xs sm:text-sm text-gray-900 font-mono truncate max-w-[60px] sm:max-w-none"
                                                    title="{{ $category->color }}">{{ $category->color }}</span>
                                            </div>
                                        </td>
                                        <td
                                            class="px-4 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm text-gray-500 truncate">
                                            {{ $category->created_at->format('M d, Y') }}
                                        </td>
                                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm font-medium">
                                            <div class="flex items-center space-x-1 sm:space-x-2">
                                                <a href="{{ route('categories.edit', $category) }}"
                                                    class="inline-flex items-center px-2 sm:px-3 py-1 border border-gray-300 text-xs font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-all duration-200 shadow-sm truncate">
                                                    <svg class="w-3 h-3 mr-1 flex-shrink-0" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                    <span class="truncate">Edit</span>
                                                </a>

                                                <button type="button" data-id="{{ $category->id }}"
                                                    class="toggle-status inline-flex items-center px-2 sm:px-3 py-1 border border-gray-300 text-xs font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-all duration-200 shadow-sm truncate">
                                                    <svg class="w-3 h-3 mr-1 flex-shrink-0" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                                    </svg>
                                                    <span
                                                        class="truncate">{{ $category->is_active ? 'Deactivate' : 'Activate' }}</span>
                                                </button>

                                                <form action="{{ route('categories.destroy', $category) }}"
                                                    method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        onclick="return confirm('Are you sure you want to delete this category?')"
                                                        class="inline-flex items-center p-1 border border-red-300 text-red-600 bg-white hover:bg-red-50 rounded-lg transition-all duration-200 shadow-sm">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 sm:px-6 py-8 sm:py-12 text-center">
                                            <div class="flex flex-col items-center justify-center">
                                                <div
                                                    class="w-12 h-12 sm:w-16 sm:h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-3 sm:mb-4 shadow-sm">
                                                    <svg class="w-6 h-6 sm:w-8 sm:h-8 text-gray-400" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                </div>
                                                <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-2 truncate">
                                                    No categories found</h3>
                                                <p class="text-gray-600 mb-4 sm:mb-6 max-w-md text-xs sm:text-sm truncate">
                                                    Get started by creating your first category to organize your content</p>
                                                <a href="{{ route('categories.create') }}"
                                                    class="inline-flex items-center px-4 sm:px-6 py-2 sm:py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-medium rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 text-sm sm:text-base">
                                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                    </svg>
                                                    <span class="truncate">Create First Category</span>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            @if ($categories->hasPages())
                <div class="mt-6 sm:mt-8 flex justify-center">
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-200/60 px-4 sm:px-6 py-3 sm:py-4">
                        {{ $categories->links() }}
                    </div>
                </div>
            @endif

            <!-- Stats Card -->
            <div class="mt-6 sm:mt-8 grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">
                <div class="bg-white rounded-2xl shadow-lg border border-gray-200/60 p-4 sm:p-6">
                    <div class="flex items-center">
                        <div
                            class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-100 rounded-xl flex items-center justify-center mr-3 sm:mr-4 flex-shrink-0">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs sm:text-sm font-medium text-gray-600 truncate">Total Categories</p>
                            <p class="text-xl sm:text-2xl font-bold text-gray-900 truncate">{{ $categories->total() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-lg border border-gray-200/60 p-4 sm:p-6">
                    <div class="flex items-center">
                        <div
                            class="w-10 h-10 sm:w-12 sm:h-12 bg-green-100 rounded-xl flex items-center justify-center mr-3 sm:mr-4 flex-shrink-0">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs sm:text-sm font-medium text-gray-600 truncate">Active Categories</p>
                            <p class="text-xl sm:text-2xl font-bold text-gray-900 truncate">
                                {{ $categories->where('is_active', true)->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-lg border border-gray-200/60 p-4 sm:p-6">
                    <div class="flex items-center">
                        <div
                            class="w-10 h-10 sm:w-12 sm:h-12 bg-purple-100 rounded-xl flex items-center justify-center mr-3 sm:mr-4 flex-shrink-0">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-purple-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs sm:text-sm font-medium text-gray-600 truncate">Color Varieties</p>
                            <p class="text-xl sm:text-2xl font-bold text-gray-900 truncate">
                                {{ $categories->pluck('color')->unique()->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<script>
    // Wait for the DOM to be fully loaded
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded - initializing view toggle');

        // View toggle functionality
        const gridViewBtn = document.getElementById('gridViewBtn');
        const listViewBtn = document.getElementById('listViewBtn');
        const gridView = document.getElementById('gridView');
        const listView = document.getElementById('listView');

        // Check if elements exist
        if (!gridViewBtn || !listViewBtn || !gridView || !listView) {
            console.error('One or more view toggle elements not found');
            return;
        }

        console.log('All view toggle elements found');

        function switchView(view) {
            console.log('Switching to view:', view);

            // Update button states
            if (view === 'grid') {
                gridViewBtn.classList.remove('text-gray-700', 'hover:bg-gray-100');
                gridViewBtn.classList.add('text-blue-600', 'bg-blue-100');

                listViewBtn.classList.remove('text-blue-600', 'bg-blue-100');
                listViewBtn.classList.add('text-gray-700', 'hover:bg-gray-100');

                // Show grid, hide list
                gridView.classList.remove('hidden');
                listView.classList.add('hidden');
            } else {
                listViewBtn.classList.remove('text-gray-700', 'hover:bg-gray-100');
                listViewBtn.classList.add('text-blue-600', 'bg-blue-100');

                gridViewBtn.classList.remove('text-blue-600', 'bg-blue-100');
                gridViewBtn.classList.add('text-gray-700', 'hover:bg-gray-100');

                // Show list, hide grid
                listView.classList.remove('hidden');
                gridView.classList.add('hidden');
            }

            // Removed localStorage persistence
            console.log('View switched to:', view);
        }

        // Button event listeners
        gridViewBtn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Grid view button clicked');
            switchView('grid');
        });

        listViewBtn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('List view button clicked');
            switchView('list');
        });

        // Default to grid view (removed localStorage loading)
        console.log('Initializing with default grid view');
        switchView('grid');

        // Toggle status functionality
        document.querySelectorAll('.toggle-status').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const categoryId = this.getAttribute('data-id');
                const button = this;
                const originalText = button.innerHTML;

                // Add loading state
                button.disabled = true;
                button.innerHTML = `
                <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 animate-spin flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2v4m0 12v4m8-10h-4M6 12H2m15.364-7.364l-2.828 2.828M7.464 17.536l-2.828 2.828m0-12.728l2.828 2.828m9.9 9.9l2.828 2.828"/>
                </svg>
                <span class="truncate">Updating...</span>
            `;

                fetch(`/categories/${categoryId}/toggle-status`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => {
                        if (response.ok) {
                            location.reload();
                        } else {
                            throw new Error('Network response was not ok');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        button.disabled = false;
                        button.innerHTML = originalText;
                        alert('Error updating category status. Please try again.');
                    });
            });
        });

        // Auto-dismiss success message after 5 seconds
        setTimeout(function() {
            const successMessage = document.querySelector('.bg-green-50');
            if (successMessage) {
                successMessage.style.transition = 'opacity 0.3s';
                successMessage.style.opacity = '0';
                setTimeout(() => successMessage.remove(), 300);
            }
        }, 5000);
    });
</script>
<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Custom pagination styles */
    .pagination {
        display: flex;
        list-style: none;
        padding: 0;
        margin: 0;
        flex-wrap: wrap;
        justify-content: center;
    }

    .pagination li {
        margin: 2px;
    }

    .pagination li a,
    .pagination li span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 32px;
        height: 32px;
        padding: 0 8px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.2s;
    }

    @media (min-width: 640px) {

        .pagination li a,
        .pagination li span {
            min-width: 40px;
            height: 40px;
            padding: 0 12px;
            border-radius: 12px;
            font-size: 14px;
        }
    }

    .pagination li a:hover {
        background-color: #f9fafb;
        border-color: #d1d5db;
    }

    .pagination li.active span {
        background: linear-gradient(135deg, #3b82f6, #8b5cf6);
        color: white;
        border-color: transparent;
    }

    .pagination li.disabled span {
        color: #9ca3af;
        background-color: #f9fafb;
        border-color: #e5e7eb;
        cursor: not-allowed;
    }

    /* Ensure hidden class works properly */
    .hidden {
        display: none !important;
    }

    /* View toggle button styles */
    .view-toggle {
        transition: all 0.2s ease-in-out;
    }

    .view-toggle:hover {
        transform: scale(1.05);
    }

    /* Truncate utility */
    .truncate {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
</style>
