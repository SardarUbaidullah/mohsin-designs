@extends('admin.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-2xl">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Team Details</h2>
        <p class="text-gray-600 mt-1">View team information</p>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Team Information</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 gap-4">
                <div class="flex flex-col">
                    <span class="text-sm font-medium text-gray-500 mb-1">ID</span>
                    <span class="text-lg text-gray-900 font-semibold">{{ $team->id }}</span>
                </div>

                <div class="border-t border-gray-100 pt-4">
                    <span class="text-sm font-medium text-gray-500 mb-1">Team Name</span>
                    <p class="text-lg text-gray-900">{{ $team->name }}</p>
                </div>

                <div class="border-t border-gray-100 pt-4">
                    <span class="text-sm font-medium text-gray-500 mb-1">Owner ID</span>
                    <p class="text-lg text-gray-900">{{ $team->owner_id }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="flex items-center justify-end space-x-3 mt-6 pt-6 border-t border-gray-200">
        <a href="{{ route('teams.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition duration-200 font-medium">
            Back to Teams
        </a>
        <a href="{{ route('teams.edit', $team->id) }}" class="px-6 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition duration-200 font-medium flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            Edit Team
        </a>
    </div>
</div>
@endsection
