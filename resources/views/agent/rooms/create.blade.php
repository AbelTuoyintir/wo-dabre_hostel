@extends('layouts.agent')

@section('title', 'Add New Room - SRC HOSTEL SERVICE')
@section('page-title', 'Add New Room')

@section('content')
<div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Add New Room</h1>
            <p class="text-sm text-gray-600 mt-1">Add a new room to one of your assigned hostels.</p>
        </div>
        <a href="{{ route('agent.hostels.index') }}"
           class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-medium rounded-lg transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-400 focus-visible:ring-offset-2"
           aria-label="Back to hostels list">
            <i class="fas fa-arrow-left mr-2" aria-hidden="true"></i> Back to Hostels
        </a>
    </div>

    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg text-red-700 text-sm flex items-center justify-between" role="alert">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-red-500 mr-3 text-lg" aria-hidden="true"></i>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" class="text-red-700 hover:text-red-900 focus:outline-none" onclick="this.parentElement.remove()" aria-label="Close notification">
                <i class="fas fa-times" aria-hidden="true"></i>
            </button>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 sm:p-8">
        <form action="{{ route('agent.rooms.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Select Hostel -->
            <div>
                <label for="hostel_id" class="block text-sm font-semibold text-gray-700 mb-2">
                    Select Hostel <span class="text-red-500">*</span>
                </label>
                <select id="hostel_id" name="hostel_id" required
                        class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors @error('hostel_id') border-red-500 @enderror">
                    <option value="">-- Choose a Hostel --</option>
                    @foreach($hostels as $hostel)
                        <option value="{{ $hostel->id }}" {{ (old('hostel_id', $selectedHostel?->id) == $hostel->id) ? 'selected' : '' }}>
                            {{ $hostel->name }} ({{ $hostel->location }})
                        </option>
                    @endforeach
                </select>
                @error('hostel_id')
                    <p class="mt-1.5 text-xs text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1" aria-hidden="true"></i> {{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Room Number -->
                <div>
                    <label for="room_number" class="block text-sm font-semibold text-gray-700 mb-2">
                        Room Number / Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="room_number" name="room_number" value="{{ old('room_number') }}" required
                           placeholder="e.g. Room 101, Block A - 12"
                           class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors @error('room_number') border-red-500 @enderror">
                    @error('room_number')
                        <p class="mt-1.5 text-xs text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1" aria-hidden="true"></i> {{ $message }}</p>
                    @enderror
                </div>

                <!-- Room Type -->
                <div>
                    <label for="room_type" class="block text-sm font-semibold text-gray-700 mb-2">
                        Room Type <span class="text-red-500">*</span>
                    </label>
                    <select id="room_type" name="room_type" required
                            class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors @error('room_type') border-red-500 @enderror">
                        <option value="">-- Select Room Type --</option>
                        <optgroup label="Single Rooms">
                            <option value="single_room" {{ old('room_type') == 'single_room' ? 'selected' : '' }}>Single Room Standard</option>
                            <option value="single_self_contained" {{ old('room_type') == 'single_self_contained' ? 'selected' : '' }}>Single Room - Self Contained</option>
                            <option value="single_private_bathroom" {{ old('room_type') == 'single_private_bathroom' ? 'selected' : '' }}>Single Room - Private Bathroom</option>
                            <option value="single_shared_bathroom" {{ old('room_type') == 'single_shared_bathroom' ? 'selected' : '' }}>Single Room - Shared Bathroom</option>
                            <option value="executive" {{ old('room_type') == 'executive' ? 'selected' : '' }}>Executive Single Room</option>
                        </optgroup>
                        <optgroup label="Shared Rooms">
                            <option value="shared_2" {{ old('room_type') == 'shared_2' ? 'selected' : '' }}>Shared Room (2 People)</option>
                            <option value="shared_3" {{ old('room_type') == 'shared_3' ? 'selected' : '' }}>Shared Room (3 People)</option>
                            <option value="shared_4" {{ old('room_type') == 'shared_4' ? 'selected' : '' }}>Shared Room (4 People)</option>
                        </optgroup>
                        <optgroup label="Apartments & Suites">
                            <option value="studio_self_contained" {{ old('room_type') == 'studio_self_contained' ? 'selected' : '' }}>Studio Apartment</option>
                            <option value="one_bedroom_self_contained" {{ old('room_type') == 'one_bedroom_self_contained' ? 'selected' : '' }}>One Bedroom Apartment</option>
                        </optgroup>
                    </select>
                    @error('room_type')
                        <p class="mt-1.5 text-xs text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1" aria-hidden="true"></i> {{ $message }}</p>
                    @enderror
                </div>

                <!-- Capacity -->
                <div>
                    <label for="capacity" class="block text-sm font-semibold text-gray-700 mb-2">
                        Capacity (Max Occupants) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="capacity" name="capacity" value="{{ old('capacity', 1) }}" min="1" max="20" required
                           class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors @error('capacity') border-red-500 @enderror">
                    @error('capacity')
                        <p class="mt-1.5 text-xs text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1" aria-hidden="true"></i> {{ $message }}</p>
                    @enderror
                </div>

                <!-- Price per Year / Semester -->
                <div>
                    <label for="price_per_year" class="block text-sm font-semibold text-gray-700 mb-2">
                        Price (GHS) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="price_per_year" name="price_per_year" value="{{ old('price_per_year') }}" step="0.01" min="0" required
                           placeholder="e.g. 1200.00"
                           class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors @error('price_per_year') border-red-500 @enderror">
                    @error('price_per_year')
                        <p class="mt-1.5 text-xs text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1" aria-hidden="true"></i> {{ $message }}</p>
                    @enderror
                </div>

                <!-- Gender Restriction -->
                <div>
                    <label for="gender" class="block text-sm font-semibold text-gray-700 mb-2">
                        Gender Restriction
                    </label>
                    <select id="gender" name="gender"
                            class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors @error('gender') border-red-500 @enderror">
                        <option value="any" {{ old('gender', 'any') == 'any' ? 'selected' : '' }}>Any (Mixed / Unrestricted)</option>
                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male Only</option>
                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female Only</option>
                    </select>
                    @error('gender')
                        <p class="mt-1.5 text-xs text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1" aria-hidden="true"></i> {{ $message }}</p>
                    @enderror
                </div>

                <!-- Availability -->
                <div class="flex items-center pt-6">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="is_available" name="is_available" value="1" {{ old('is_available', 1) ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                        <span class="ml-3 text-sm font-semibold text-gray-700">Mark as Available for Booking</span>
                    </label>
                </div>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                    Description / Special Features (Optional)
                </label>
                <textarea id="description" name="description" rows="3"
                          placeholder="Provide details about amenities, floor location, balcony, etc."
                          class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1.5 text-xs text-red-600 flex items-center"><i class="fas fa-exclamation-circle mr-1" aria-hidden="true"></i> {{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="pt-4 flex items-center justify-end space-x-4 border-t border-gray-100">
                <a href="{{ route('agent.hostels.index') }}"
                   class="px-6 py-2.5 border border-gray-300 text-gray-700 font-medium text-sm rounded-xl hover:bg-gray-50 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-400">
                    Cancel
                </a>
                <button type="submit"
                        class="px-6 py-2.5 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-semibold text-sm rounded-xl hover:from-purple-700 hover:to-pink-700 transition-all shadow-md hover:shadow-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-purple-500 focus-visible:ring-offset-2">
                    <i class="fas fa-plus-circle mr-2" aria-hidden="true"></i> Add Room
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
