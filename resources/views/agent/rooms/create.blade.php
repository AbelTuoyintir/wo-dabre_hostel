@extends('layouts.agent')

@section('title', 'Add New Room')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-3xl">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Add New Room</h1>
            <p class="text-sm text-gray-500 mt-1">Create a new room listing for one of your registered hostels.</p>
        </div>
        <a href="{{ url()->previous() ?: route('agent.hostels.index') }}"
           class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back
        </a>
    </div>

    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg text-red-700 text-sm flex justify-between items-center" role="alert">
            <span>{{ session('error') }}</span>
            <button type="button" class="text-red-700 hover:text-red-900 focus:outline-none" onclick="this.parentElement.remove()" aria-label="Close message">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <form action="{{ route('agent.rooms.store') }}" method="POST" class="p-6 space-y-6">
            @csrf

            <!-- Hostel Selection -->
            <div>
                <label for="hostel_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Select Hostel <span class="text-red-500">*</span>
                </label>
                <select id="hostel_id" name="hostel_id" required
                        class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 focus:border-blue-500 @error('hostel_id') border-red-500 @enderror">
                    <option value="">-- Choose Hostel --</option>
                    @foreach($hostels as $h)
                        <option value="{{ $h->id }}" {{ (old('hostel_id', $selectedHostel?->id) == $h->id || old('hostel_id', $selectedHostel?->uuid) == $h->uuid) ? 'selected' : '' }}>
                            {{ $h->name }} ({{ $h->location }})
                        </option>
                    @endforeach
                </select>
                @error('hostel_id')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Room Number -->
                <div>
                    <label for="room_number" class="block text-sm font-medium text-gray-700 mb-2">
                        Room Number / Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="room_number" name="room_number" value="{{ old('room_number') }}" placeholder="e.g. A101, Room 12" required
                           class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 focus:border-blue-500 @error('room_number') border-red-500 @enderror">
                    @error('room_number')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Capacity -->
                <div>
                    <label for="capacity" class="block text-sm font-medium text-gray-700 mb-2">
                        Capacity (Max Occupants) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="capacity" name="capacity" min="1" value="{{ old('capacity', 1) }}" required
                           class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 focus:border-blue-500 @error('capacity') border-red-500 @enderror">
                    @error('capacity')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Room Type -->
            <div>
                <label for="room_type" class="block text-sm font-medium text-gray-700 mb-2">
                    Room Type <span class="text-red-500">*</span>
                </label>
                <select id="room_type" name="room_type" required
                        class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 focus:border-blue-500 @error('room_type') border-red-500 @enderror">
                    <option value="">-- Select Room Type --</option>
                    <optgroup label="Single Rooms">
                        <option value="single_room" {{ old('room_type') == 'single_room' ? 'selected' : '' }}>Single Room</option>
                        <option value="single_self_contained" {{ old('room_type') == 'single_self_contained' ? 'selected' : '' }}>Single Room - Self Contained</option>
                        <option value="single_private_bathroom" {{ old('room_type') == 'single_private_bathroom' ? 'selected' : '' }}>Single Room - Private Bathroom</option>
                        <option value="single_shared_bathroom" {{ old('room_type') == 'single_shared_bathroom' ? 'selected' : '' }}>Single Room - Shared Bathroom</option>
                        <option value="single_standard" {{ old('room_type') == 'single_standard' ? 'selected' : '' }}>Single Room - Standard</option>
                        <option value="single_executive" {{ old('room_type') == 'single_executive' ? 'selected' : '' }}>Single Room - Executive</option>
                    </optgroup>
                    <optgroup label="Double Rooms (2 People)">
                        <option value="double_self_contained" {{ old('room_type') == 'double_self_contained' ? 'selected' : '' }}>Two in a Room - Self Contained</option>
                        <option value="double_shared_bathroom" {{ old('room_type') == 'double_shared_bathroom' ? 'selected' : '' }}>Two in a Room - Shared Bathroom</option>
                        <option value="shared_2" {{ old('room_type') == 'shared_2' ? 'selected' : '' }}>Two in a Room - Standard</option>
                    </optgroup>
                    <optgroup label="Shared / Dormitories">
                        <option value="triple_self_contained" {{ old('room_type') == 'triple_self_contained' ? 'selected' : '' }}>Three in a Room - Self Contained</option>
                        <option value="quad_self_contained" {{ old('room_type') == 'quad_self_contained' ? 'selected' : '' }}>Four in a Room - Self Contained</option>
                        <option value="shared_4" {{ old('room_type') == 'shared_4' ? 'selected' : '' }}>Four in a Room - Standard</option>
                        <option value="dorm_6_shared" {{ old('room_type') == 'dorm_6_shared' ? 'selected' : '' }}>6-Bed Dormitory - Shared Bathroom</option>
                    </optgroup>
                    <optgroup label="Studio & Executive">
                        <option value="studio_self_contained" {{ old('room_type') == 'studio_self_contained' ? 'selected' : '' }}>Studio Apartment - Self Contained</option>
                        <option value="executive" {{ old('room_type') == 'executive' ? 'selected' : '' }}>Executive Room</option>
                        <option value="executive_suite" {{ old('room_type') == 'executive_suite' ? 'selected' : '' }}>Executive Suite</option>
                    </optgroup>
                </select>
                @error('room_type')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Price Per Year -->
                <div>
                    <label for="price_per_year" class="block text-sm font-medium text-gray-700 mb-2">
                        Price Per Year (₵) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" step="0.01" min="0" id="price_per_year" name="price_per_year" value="{{ old('price_per_year') }}" placeholder="e.g. 2500.00" required
                           class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 focus:border-blue-500 @error('price_per_year') border-red-500 @enderror">
                    @error('price_per_year')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Gender Preference -->
                <div>
                    <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">
                        Gender Designation
                    </label>
                    <select id="gender" name="gender"
                            class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 focus:border-blue-500 @error('gender') border-red-500 @enderror">
                        <option value="any" {{ old('gender', 'any') == 'any' ? 'selected' : '' }}>Mixed / Any</option>
                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male Only</option>
                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female Only</option>
                    </select>
                    @error('gender')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Room Description
                </label>
                <textarea id="description" name="description" rows="3" placeholder="Brief details about room amenities, floor, or special features..."
                          class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 focus:border-blue-500 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Availability -->
            <div class="flex items-center space-x-3 pt-2">
                <input type="checkbox" id="is_available" name="is_available" value="1" {{ old('is_available', 1) ? 'checked' : '' }}
                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2">
                <label for="is_available" class="text-sm font-medium text-gray-700 cursor-pointer">
                    Mark room as currently available for booking
                </label>
            </div>

            <!-- Submit Buttons -->
            <div class="pt-4 border-t border-gray-200 flex justify-end space-x-3">
                <a href="{{ route('agent.hostels.index') }}"
                   class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2">
                    Cancel
                </a>
                <button type="submit"
                        class="inline-flex items-center px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Save &amp; Add Room
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
