@extends('layouts.agent')

@section('title', 'Add Room - Agent Portal')
@section('page-title', 'Add New Room')

@section('content')
<div class="max-w-4xl mx-auto py-6">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Add Room to Hostel</h2>
            <p class="text-sm text-gray-600 mt-1">Create a new room listing for your registered hostels.</p>
        </div>
        <a href="{{ route('agent.hostels.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-medium rounded-lg transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-500 focus-visible:ring-offset-2">
            <i class="fas fa-arrow-left mr-2" aria-hidden="true"></i> Back to Hostels
        </a>
    </div>

    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg text-red-700 text-sm flex justify-between items-center" role="alert">
            <span>{{ session('error') }}</span>
            <button type="button" class="text-red-700 hover:text-red-900" onclick="this.parentElement.remove()" aria-label="Dismiss alert">
                <i class="fas fa-times" aria-hidden="true"></i>
            </button>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6">
            <form action="{{ route('agent.rooms.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Select Hostel -->
                <div>
                    <label for="hostel_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Select Hostel <span class="text-red-500">*</span>
                    </label>
                    <select id="hostel_id" name="hostel_id" required
                            class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 focus-visible:ring-2 focus-visible:ring-purple-500 focus-visible:ring-offset-2 @error('hostel_id') border-red-500 @enderror">
                        <option value="">Select Hostel</option>
                        @foreach($hostels as $hostel)
                            <option value="{{ $hostel->id }}" {{ (old('hostel_id', $selectedHostelId) == $hostel->id) ? 'selected' : '' }}>
                                {{ $hostel->name }} ({{ $hostel->location }})
                            </option>
                        @endforeach
                    </select>
                    @error('hostel_id')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Room Number & Capacity -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="room_number" class="block text-sm font-medium text-gray-700 mb-1">
                            Room Number <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="room_number" name="room_number" value="{{ old('room_number') }}" required
                               placeholder="e.g. A101, B-202"
                               class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 focus-visible:ring-2 focus-visible:ring-purple-500 focus-visible:ring-offset-2 @error('room_number') border-red-500 @enderror">
                        @error('room_number')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="capacity" class="block text-sm font-medium text-gray-700 mb-1">
                            Capacity (Persons) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="capacity" name="capacity" min="1" value="{{ old('capacity', 1) }}" required
                               class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 focus-visible:ring-2 focus-visible:ring-purple-500 focus-visible:ring-offset-2 @error('capacity') border-red-500 @enderror">
                        @error('capacity')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Room Type & Price -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="room_type" class="block text-sm font-medium text-gray-700 mb-1">
                            Room Type <span class="text-red-500">*</span>
                        </label>
                        <select id="room_type" name="room_type" required
                                class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 focus-visible:ring-2 focus-visible:ring-purple-500 focus-visible:ring-offset-2 @error('room_type') border-red-500 @enderror">
                            <option value="">Select Room Type</option>
                            <!-- Single Rooms -->
                            <optgroup label="Single Rooms">
                                <option value="single_room" {{ old('room_type') == 'single_room' ? 'selected' : '' }}>Single Room Standard</option>
                                <option value="single_self_contained" {{ old('room_type') == 'single_self_contained' ? 'selected' : '' }}>Single Room - Self Contained</option>
                                <option value="single_private_bathroom" {{ old('room_type') == 'single_private_bathroom' ? 'selected' : '' }}>Single Room - Private Bathroom</option>
                                <option value="single_shared_bathroom" {{ old('room_type') == 'single_shared_bathroom' ? 'selected' : '' }}>Single Room - Shared Bathroom</option>
                                <option value="single_executive" {{ old('room_type') == 'single_executive' ? 'selected' : '' }}>Single Room - Executive</option>
                            </optgroup>

                            <!-- Shared Rooms -->
                            <optgroup label="Shared Rooms">
                                <option value="shared_2" {{ old('room_type') == 'shared_2' ? 'selected' : '' }}>Shared Room - 2 People</option>
                                <option value="shared_2_self_contained" {{ old('room_type') == 'shared_2_self_contained' ? 'selected' : '' }}>Shared Room - 2 People (Self Contained)</option>
                                <option value="shared_3_self_contained" {{ old('room_type') == 'shared_3_self_contained' ? 'selected' : '' }}>Shared Room - 3 People (Self Contained)</option>
                                <option value="shared_4" {{ old('room_type') == 'shared_4' ? 'selected' : '' }}>Shared Room - 4 People</option>
                                <option value="shared_4_self_contained" {{ old('room_type') == 'shared_4_self_contained' ? 'selected' : '' }}>Shared Room - 4 People (Self Contained)</option>
                            </optgroup>

                            <!-- Executive & Suites -->
                            <optgroup label="Executive &amp; Suites">
                                <option value="executive" {{ old('room_type') == 'executive' ? 'selected' : '' }}>Executive Suite</option>
                                <option value="studio_self_contained" {{ old('room_type') == 'studio_self_contained' ? 'selected' : '' }}>Studio Apartment</option>
                            </optgroup>
                        </select>
                        @error('room_type')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="price_per_year" class="block text-sm font-medium text-gray-700 mb-1">
                            Base Price/Year (₵) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" step="0.01" min="0" id="price_per_year" name="price_per_year" value="{{ old('price_per_year') }}" required
                               placeholder="e.g. 1200.00"
                               class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 focus-visible:ring-2 focus-visible:ring-purple-500 focus-visible:ring-offset-2 @error('price_per_year') border-red-500 @enderror">
                        @error('price_per_year')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                        Room Description
                    </label>
                    <textarea id="description" name="description" rows="3"
                              placeholder="Provide details about features, furnishing, layout..."
                              class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 focus-visible:ring-2 focus-visible:ring-purple-500 focus-visible:ring-offset-2 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Availability Toggle -->
                <div class="flex items-center space-x-3 bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <input type="checkbox" id="is_available" name="is_available" value="1" {{ old('is_available', '1') ? 'checked' : '' }}
                           class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500 focus-visible:ring-2 focus-visible:ring-purple-500 focus-visible:ring-offset-2">
                    <label for="is_available" class="text-sm font-medium text-gray-700">
                        Mark room as available for booking immediately
                    </label>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                    <a href="{{ route('agent.hostels.index') }}" class="px-5 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-medium rounded-lg transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-500 focus-visible:ring-offset-2">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white text-sm font-medium rounded-lg shadow-sm transition-all focus:outline-none focus-visible:ring-2 focus-visible:ring-purple-500 focus-visible:ring-offset-2">
                        <i class="fas fa-plus mr-2" aria-hidden="true"></i> Create Room
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
