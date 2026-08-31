@extends('layouts.agent')

@section('title', 'Add New Room')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h5 class="text-sm font-semibold text-gray-800">Add New Room</h5>
            <p class="text-xs text-gray-500">Create a new room for your registered hostel</p>
        </div>
        <a href="{{ route('agent.hostels.index') }}" class="inline-flex items-center px-3 py-1.5 bg-gray-200 hover:bg-gray-300 text-gray-700 text-xs font-medium rounded-lg transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2">
            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Hostels
        </a>
    </div>

    @if(session('error'))
        <div class="mb-4 p-3 bg-red-50 border-l-4 border-red-500 rounded-lg text-red-700 text-xs flex justify-between items-center">
            <span>{{ session('error') }}</span>
            <button type="button" class="text-red-700 hover:text-red-900" onclick="this.parentElement.remove()" aria-label="Close error message">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    @endif

    <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h6 class="text-sm font-semibold text-gray-800">Room Details</h6>
        </div>
        <div class="p-6">
            <form action="{{ route('agent.rooms.store') }}" method="POST">
                @csrf

                <div class="space-y-4">
                    <!-- Select Hostel -->
                    <div>
                        <label for="hostel_id" class="block text-xs font-medium text-gray-700 mb-1">
                            Hostel <span class="text-red-500">*</span>
                        </label>
                        <select id="hostel_id" name="hostel_id" required
                                class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 @error('hostel_id') border-red-500 @enderror">
                            <option value="">Select a Hostel</option>
                            @foreach($hostels as $h)
                                <option value="{{ $h->id }}" {{ (old('hostel_id', $selectedHostel?->id) == $h->id) ? 'selected' : '' }}>
                                    {{ $h->name }} ({{ $h->location }})
                                </option>
                            @endforeach
                        </select>
                        @error('hostel_id')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Room Number -->
                        <div>
                            <label for="room_number" class="block text-xs font-medium text-gray-700 mb-1">
                                Room Number <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="room_number" name="room_number" value="{{ old('room_number') }}" required
                                   placeholder="e.g. A101"
                                   class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 @error('room_number') border-red-500 @enderror">
                            @error('room_number')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Capacity -->
                        <div>
                            <label for="capacity" class="block text-xs font-medium text-gray-700 mb-1">
                                Capacity (Persons) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="capacity" name="capacity" value="{{ old('capacity', 1) }}" min="1" required
                                   class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 @error('capacity') border-red-500 @enderror">
                            @error('capacity')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Room Type -->
                        <div>
                            <label for="room_type" class="block text-xs font-medium text-gray-700 mb-1">
                                Room Type <span class="text-red-500">*</span>
                            </label>
                            <select id="room_type" name="room_type" required
                                    class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 @error('room_type') border-red-500 @enderror">
                                <option value="">Select Room Type</option>
                                <optgroup label="Single Rooms">
                                    <option value="single_self_contained" {{ old('room_type') == 'single_self_contained' ? 'selected' : '' }}>Single Room - Self Contained</option>
                                    <option value="single_private_bathroom" {{ old('room_type') == 'single_private_bathroom' ? 'selected' : '' }}>Single Room - Private Bathroom</option>
                                    <option value="single_shared_bathroom" {{ old('room_type') == 'single_shared_bathroom' ? 'selected' : '' }}>Single Room - Shared Bathroom</option>
                                    <option value="single_executive" {{ old('room_type') == 'single_executive' ? 'selected' : '' }}>Single Room - Executive</option>
                                    <option value="single_standard" {{ old('room_type') == 'single_standard' ? 'selected' : '' }}>Single Room - Standard</option>
                                    <option value="single_deluxe" {{ old('room_type') == 'single_deluxe' ? 'selected' : '' }}>Single Room - Deluxe</option>
                                </optgroup>
                                <optgroup label="Double Rooms (2 People)">
                                    <option value="double_self_contained" {{ old('room_type') == 'double_self_contained' ? 'selected' : '' }}>Two in a Room - Self Contained</option>
                                    <option value="double_shared_bathroom" {{ old('room_type') == 'double_shared_bathroom' ? 'selected' : '' }}>Two in a Room - Shared Bathroom</option>
                                    <option value="double_standard" {{ old('room_type') == 'double_standard' ? 'selected' : '' }}>Two in a Room - Standard</option>
                                </optgroup>
                                <optgroup label="Shared Rooms">
                                    <option value="shared_2_self_contained" {{ old('room_type') == 'shared_2_self_contained' ? 'selected' : '' }}>Shared Room - 2 People (Self Contained)</option>
                                    <option value="shared_2_shared_bathroom" {{ old('room_type') == 'shared_2_shared_bathroom' ? 'selected' : '' }}>Shared Room - 2 People (Shared Bathroom)</option>
                                    <option value="shared_4_self_contained" {{ old('room_type') == 'shared_4_self_contained' ? 'selected' : '' }}>Shared Room - 4 People (Self Contained)</option>
                                    <option value="shared_4_shared_bathroom" {{ old('room_type') == 'shared_4_shared_bathroom' ? 'selected' : '' }}>Shared Room - 4 People (Shared Bathroom)</option>
                                </optgroup>
                                <optgroup label="Dormitories">
                                    <option value="dorm_4_shared" {{ old('room_type') == 'dorm_4_shared' ? 'selected' : '' }}>4-Bed Dormitory - Shared Bathroom</option>
                                    <option value="dorm_6_shared" {{ old('room_type') == 'dorm_6_shared' ? 'selected' : '' }}>6-Bed Dormitory - Shared Bathroom</option>
                                </optgroup>
                            </select>
                            @error('room_type')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Price per Year -->
                        <div>
                            <label for="price_per_year" class="block text-xs font-medium text-gray-700 mb-1">
                                Price / Year (₵) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" step="0.01" id="price_per_year" name="price_per_year" value="{{ old('price_per_year') }}" min="0" required
                                   placeholder="0.00"
                                   class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 @error('price_per_year') border-red-500 @enderror">
                            @error('price_per_year')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-xs font-medium text-gray-700 mb-1">Description</label>
                        <textarea id="description" name="description" rows="3"
                                  placeholder="Provide optional room details or amenities..."
                                  class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Is Available -->
                    <div class="flex items-center space-x-2 pt-2">
                        <input type="checkbox" id="is_available" name="is_available" value="1" {{ old('is_available', '1') ? 'checked' : '' }}
                               class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <label for="is_available" class="text-xs font-medium text-gray-700">Room is available for immediate booking</label>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="mt-6 flex justify-end space-x-3 border-t border-gray-100 pt-4">
                    <a href="{{ route('agent.hostels.index') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-xs font-medium rounded-lg transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-500 focus-visible:ring-offset-2">
                        Cancel
                    </a>
                    <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2">
                        Create Room
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
