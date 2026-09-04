@extends('layouts.agent')

@section('title', 'Add New Room - SRC HOSTEL SERVICE')
@section('page-title', 'Add New Room')

@section('content')
<div class="max-w-4xl mx-auto py-6 px-4 sm:px-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Add New Room</h1>
            <p class="text-xs text-gray-500 mt-1">Add a new room listing to your hostel to receive student bookings.</p>
        </div>
        <a href="{{ isset($hostel) ? route('agent.hostels.show', $hostel->uuid) : route('agent.hostels.index') }}"
           class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-xl transition-colors duration-200">
            <i class="fas fa-arrow-left mr-2" aria-hidden="true"></i> Back
        </a>
    </div>

    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-xl text-red-700 text-xs flex justify-between items-center shadow-sm">
            <span>{{ session('error') }}</span>
            <button type="button" class="text-red-700 hover:text-red-900" onclick="this.parentElement.remove()">
                <i class="fas fa-times" aria-hidden="true"></i>
            </button>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <form action="{{ route('agent.rooms.store') }}" method="POST" class="p-6 sm:p-8 space-y-6">
            @csrf

            <!-- Hostel Selection -->
            <div>
                <label for="hostel_id" class="block text-xs font-semibold text-gray-700 mb-2">
                    Select Hostel <span class="text-red-500">*</span>
                </label>
                <select id="hostel_id" name="hostel_id" required
                        class="w-full px-4 py-2.5 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('hostel_id') border-red-500 @enderror">
                    <option value="">-- Choose a Hostel --</option>
                    @foreach($hostels as $h)
                        <option value="{{ $h->id }}"
                            {{ (old('hostel_id', $hostel->id ?? null) == $h->id) ? 'selected' : '' }}>
                            {{ $h->name }} ({{ $h->location }})
                        </option>
                    @endforeach
                </select>
                @error('hostel_id')
                    <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Room Number -->
                <div>
                    <label for="room_number" class="block text-xs font-semibold text-gray-700 mb-2">
                        Room Number / Identifier <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="room_number" name="room_number" value="{{ old('room_number') }}" required
                           placeholder="e.g. A101, Room 12"
                           class="w-full px-4 py-2.5 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('room_number') border-red-500 @enderror">
                    @error('room_number')
                        <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Capacity -->
                <div>
                    <label for="capacity" class="block text-xs font-semibold text-gray-700 mb-2">
                        Capacity (Max Occupants) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="capacity" name="capacity" value="{{ old('capacity', 1) }}" min="1" required
                           class="w-full px-4 py-2.5 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('capacity') border-red-500 @enderror">
                    @error('capacity')
                        <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Room Type -->
                <div>
                    <label for="room_type" class="block text-xs font-semibold text-gray-700 mb-2">
                        Room Type <span class="text-red-500">*</span>
                    </label>
                    <select id="room_type" name="room_type" required
                            class="w-full px-4 py-2.5 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('room_type') border-red-500 @enderror">
                        <option value="">-- Select Room Type --</option>
                        <optgroup label="Single Rooms">
                            <option value="single_self_contained" {{ old('room_type') == 'single_self_contained' ? 'selected' : '' }}>Single Room - Self Contained</option>
                            <option value="single_room" {{ old('room_type') == 'single_room' ? 'selected' : '' }}>Single Room - Standard</option>
                            <option value="single_private_bathroom" {{ old('room_type') == 'single_private_bathroom' ? 'selected' : '' }}>Single Room - Private Bathroom</option>
                            <option value="single_shared_bathroom" {{ old('room_type') == 'single_shared_bathroom' ? 'selected' : '' }}>Single Room - Shared Bathroom</option>
                            <option value="single_executive" {{ old('room_type') == 'single_executive' ? 'selected' : '' }}>Single Room - Executive</option>
                        </optgroup>
                        <optgroup label="Shared / Multi-Bed Rooms">
                            <option value="shared_2" {{ old('room_type') == 'shared_2' ? 'selected' : '' }}>Two in a Room (2 People)</option>
                            <option value="double_self_contained" {{ old('room_type') == 'double_self_contained' ? 'selected' : '' }}>Two in a Room - Self Contained</option>
                            <option value="shared_3" {{ old('room_type') == 'shared_3' ? 'selected' : '' }}>Three in a Room (3 People)</option>
                            <option value="shared_4" {{ old('room_type') == 'shared_4' ? 'selected' : '' }}>Four in a Room (4 People)</option>
                        </optgroup>
                        <optgroup label="Dormitories & Suites">
                            <option value="dorm_4_shared" {{ old('room_type') == 'dorm_4_shared' ? 'selected' : '' }}>4-Bed Dormitory</option>
                            <option value="dorm_6_shared" {{ old('room_type') == 'dorm_6_shared' ? 'selected' : '' }}>6-Bed Dormitory</option>
                            <option value="executive_suite" {{ old('room_type') == 'executive' || old('room_type') == 'executive_suite' ? 'selected' : '' }}>Executive Suite</option>
                        </optgroup>
                    </select>
                    @error('room_type')
                        <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Price / Year -->
                <div>
                    <label for="price_per_year" class="block text-xs font-semibold text-gray-700 mb-2">
                        Base Price / Year (₵) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" step="0.01" id="price_per_year" name="price_per_year" value="{{ old('price_per_year') }}" min="0" required
                           placeholder="0.00"
                           class="w-full px-4 py-2.5 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('price_per_year') border-red-500 @enderror">
                    @error('price_per_year')
                        <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <!-- Gender Preference -->
                <div>
                    <label for="gender" class="block text-xs font-semibold text-gray-700 mb-2">Gender Preference</label>
                    <select id="gender" name="gender"
                            class="w-full px-4 py-2.5 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        <option value="any" {{ old('gender', 'any') == 'any' ? 'selected' : '' }}>Any (Co-ed / All)</option>
                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male Only</option>
                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female Only</option>
                    </select>
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-xs font-semibold text-gray-700 mb-2">Initial Status</label>
                    <select id="status" name="status"
                            class="w-full px-4 py-2.5 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        <option value="available" {{ old('status', 'available') == 'available' ? 'selected' : '' }}>Available</option>
                        <option value="unavailable" {{ old('status') == 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                        <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Under Maintenance</option>
                    </select>
                </div>

                <!-- Floor Number -->
                <div>
                    <label for="floor" class="block text-xs font-semibold text-gray-700 mb-2">Floor Level</label>
                    <input type="number" id="floor" name="floor" value="{{ old('floor') }}" min="0" placeholder="e.g. 1"
                           class="w-full px-4 py-2.5 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Size in sqm -->
                <div>
                    <label for="size_sqm" class="block text-xs font-semibold text-gray-700 mb-2">Room Size (sq. meters)</label>
                    <input type="number" step="0.1" id="size_sqm" name="size_sqm" value="{{ old('size_sqm') }}" min="0" placeholder="e.g. 25.5"
                           class="w-full px-4 py-2.5 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                </div>

                <!-- Window Type -->
                <div>
                    <label for="window_type" class="block text-xs font-semibold text-gray-700 mb-2">Window View</label>
                    <select id="window_type" name="window_type"
                            class="w-full px-4 py-2.5 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        <option value="">-- None / Standard --</option>
                        <option value="street" {{ old('window_type') == 'street' ? 'selected' : '' }}>Street View</option>
                        <option value="courtyard" {{ old('window_type') == 'courtyard' ? 'selected' : '' }}>Courtyard View</option>
                        <option value="garden" {{ old('window_type') == 'garden' ? 'selected' : '' }}>Garden View</option>
                    </select>
                </div>
            </div>

            <!-- Features / Checkboxes -->
            <div class="flex flex-wrap items-center gap-6 pt-2">
                <label class="inline-flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" id="furnished" name="furnished" value="1" {{ old('furnished') ? 'checked' : '' }}
                           class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                    <span class="text-xs font-medium text-gray-700">Fully Furnished</span>
                </label>

                <label class="inline-flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" id="private_bathroom" name="private_bathroom" value="1" {{ old('private_bathroom') ? 'checked' : '' }}
                           class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                    <span class="text-xs font-medium text-gray-700">Private Bathroom</span>
                </label>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-xs font-semibold text-gray-700 mb-2">Room Description</label>
                <textarea id="description" name="description" rows="3" placeholder="Describe features, amenities, or notes about this room..."
                          class="w-full px-4 py-2.5 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-100">
                <a href="{{ isset($hostel) ? route('agent.hostels.show', $hostel->uuid) : route('agent.hostels.index') }}"
                   class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-xl transition-colors duration-200">
                    Cancel
                </a>
                <button type="submit"
                        class="px-6 py-2.5 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white text-xs font-semibold rounded-xl shadow-md transition-all duration-200 flex items-center">
                    <i class="fas fa-plus mr-2" aria-hidden="true"></i> Save &amp; Add Room
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
