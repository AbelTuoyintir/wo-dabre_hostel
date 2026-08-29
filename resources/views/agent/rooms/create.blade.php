@extends('layouts.agent')

@section('title', 'Add New Room')
@section('page-title', 'Add New Room')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-4xl">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-lg font-bold text-gray-800">Add New Room</h1>
            <p class="text-xs text-gray-500">Create and list a new room for one of your registered hostels</p>
        </div>
        <a href="{{ route('agent.hostels.index') }}" class="inline-flex items-center px-3 py-1.5 bg-gray-200 hover:bg-gray-300 text-gray-700 text-xs font-medium rounded-lg transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-purple-500 focus-visible:ring-offset-2">
            <svg class="w-3.5 h-3.5 mr-1.5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Hostels
        </a>
    </div>

    <!-- Error Alert -->
    @if(session('error'))
        <div class="mb-4 p-3 bg-red-50 border-l-4 border-red-500 rounded-lg text-red-700 text-xs flex justify-between items-center" role="alert">
            <span>{{ session('error') }}</span>
            <button type="button" class="text-red-700 hover:text-red-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500" aria-label="Close alert" onclick="this.parentElement.remove()">
                <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden p-6">
        <form action="{{ route('agent.rooms.store') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <!-- Select Hostel -->
                <div>
                    <label for="hostel_id" class="block text-xs font-medium text-gray-700 mb-1">
                        Select Hostel <span class="text-red-500">*</span>
                    </label>
                    <select id="hostel_id"
                            name="hostel_id"
                            required
                            class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-purple-500 focus-visible:border-purple-500 @error('hostel_id') border-red-500 @enderror">
                        <option value="">-- Choose a Hostel --</option>
                        @foreach($hostels as $h)
                            <option value="{{ $h->id }}" {{ (old('hostel_id', $selectedHostelId) == $h->id) ? 'selected' : '' }}>
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
                        <input type="text"
                               id="room_number"
                               name="room_number"
                               value="{{ old('room_number') }}"
                               required
                               placeholder="e.g. A101"
                               class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-purple-500 focus-visible:border-purple-500 @error('room_number') border-red-500 @enderror">
                        @error('room_number')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Room Type -->
                    <div>
                        <label for="room_type" class="block text-xs font-medium text-gray-700 mb-1">
                            Room Type <span class="text-red-500">*</span>
                        </label>
                        <select id="room_type"
                                name="room_type"
                                required
                                class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-purple-500 focus-visible:border-purple-500 @error('room_type') border-red-500 @enderror">
                            <option value="">Select Room Type</option>
                            <optgroup label="Single Rooms">
                                <option value="single_self_contained" {{ old('room_type') == 'single_self_contained' ? 'selected' : '' }}>Single Room - Self Contained</option>
                                <option value="single_private_bathroom" {{ old('room_type') == 'single_private_bathroom' ? 'selected' : '' }}>Single Room - Private Bathroom</option>
                                <option value="single_shared_bathroom" {{ old('room_type') == 'single_shared_bathroom' ? 'selected' : '' }}>Single Room - Shared Bathroom</option>
                                <option value="single_standard" {{ old('room_type') == 'single_standard' ? 'selected' : '' }}>Single Room - Standard</option>
                            </optgroup>
                            <optgroup label="Double Rooms (2 People)">
                                <option value="double_self_contained" {{ old('room_type') == 'double_self_contained' ? 'selected' : '' }}>Two in a Room - Self Contained</option>
                                <option value="double_shared_bathroom" {{ old('room_type') == 'double_shared_bathroom' ? 'selected' : '' }}>Two in a Room - Shared Bathroom</option>
                                <option value="double_standard" {{ old('room_type') == 'double_standard' ? 'selected' : '' }}>Two in a Room - Standard</option>
                            </optgroup>
                            <optgroup label="Triple Rooms (3 People)">
                                <option value="triple_self_contained" {{ old('room_type') == 'triple_self_contained' ? 'selected' : '' }}>Three in a Room - Self Contained</option>
                                <option value="triple_shared_bathroom" {{ old('room_type') == 'triple_shared_bathroom' ? 'selected' : '' }}>Three in a Room - Shared Bathroom</option>
                            </optgroup>
                            <optgroup label="Quad Rooms (4 People)">
                                <option value="quad_self_contained" {{ old('room_type') == 'quad_self_contained' ? 'selected' : '' }}>Four in a Room - Self Contained</option>
                                <option value="quad_shared_bathroom" {{ old('room_type') == 'quad_shared_bathroom' ? 'selected' : '' }}>Four in a Room - Shared Bathroom</option>
                            </optgroup>
                        </select>
                        @error('room_type')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Capacity -->
                    <div>
                        <label for="capacity" class="block text-xs font-medium text-gray-700 mb-1">
                            Capacity (Number of occupants) <span class="text-red-500">*</span>
                        </label>
                        <input type="number"
                               id="capacity"
                               name="capacity"
                               value="{{ old('capacity', 1) }}"
                               min="1"
                               required
                               class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-purple-500 focus-visible:border-purple-500 @error('capacity') border-red-500 @enderror">
                        @error('capacity')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Price Per Year -->
                    <div>
                        <label for="price_per_year" class="block text-xs font-medium text-gray-700 mb-1">
                            Price Per Year / Academic Year (₵) <span class="text-red-500">*</span>
                        </label>
                        <input type="number"
                               step="0.01"
                               id="price_per_year"
                               name="price_per_year"
                               value="{{ old('price_per_year') }}"
                               min="0"
                               required
                               placeholder="e.g. 1500.00"
                               class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-purple-500 focus-visible:border-purple-500 @error('price_per_year') border-red-500 @enderror">
                        @error('price_per_year')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Room Description -->
                <div>
                    <label for="description" class="block text-xs font-medium text-gray-700 mb-1">Room Description</label>
                    <textarea id="description"
                              name="description"
                              rows="3"
                              placeholder="Brief description of features, amenities, view, etc."
                              class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-purple-500 focus-visible:border-purple-500 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Availability Toggle -->
                <div class="flex items-center space-x-2 pt-2">
                    <input type="checkbox"
                           id="is_available"
                           name="is_available"
                           value="1"
                           {{ old('is_available', '1') ? 'checked' : '' }}
                           class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                    <label for="is_available" class="text-xs font-medium text-gray-700">Set room status to Available for bookings</label>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                    <a href="{{ route('agent.hostels.index') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-xs font-medium rounded-lg transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-purple-500 focus-visible:ring-offset-2">
                        Cancel
                    </a>
                    <button type="submit" class="px-5 py-2 bg-purple-600 hover:bg-purple-700 text-white text-xs font-medium rounded-lg transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-purple-500 focus-visible:ring-offset-2 inline-flex items-center">
                        <svg class="w-4 h-4 mr-1.5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Create Room
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
