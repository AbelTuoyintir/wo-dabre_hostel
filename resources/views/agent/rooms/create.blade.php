@extends('layouts.agent')

@section('title', 'Add New Room - Agent Portal')
@section('page-title', 'Add New Room')

@section('content')
<div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <!-- Header Actions -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Add New Room</h1>
            <p class="text-sm text-gray-600 mt-1">Create a new room for one of your registered hostels.</p>
        </div>
        <a href="{{ $selectedHostel ? route('agent.hostels.show', $selectedHostel->uuid ?? $selectedHostel->id) : route('agent.hostels.index') }}"
           class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-xl transition-colors duration-200">
            <i class="fas fa-arrow-left mr-2 text-xs"></i>
            Back
        </a>
    </div>

    <!-- Error Summary -->
    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-xl text-red-700 text-sm">
            <div class="font-semibold mb-1">Please correct the following errors:</div>
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Error Alert -->
    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-xl text-red-700 text-sm flex justify-between items-center">
            <span>{{ session('error') }}</span>
            <button type="button" class="text-red-700 hover:text-red-900" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 sm:p-8">
        @if($hostels->isEmpty())
            <div class="text-center py-8">
                <div class="w-16 h-16 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                    <i class="fas fa-building"></i>
                </div>
                <h3 class="text-base font-semibold text-gray-900 mb-2">No Hostels Found</h3>
                <p class="text-sm text-gray-500 mb-6 max-w-md mx-auto">
                    You need to create at least one hostel before adding rooms.
                </p>
                <a href="{{ route('agent.hostels.create') }}"
                   class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white font-medium text-sm rounded-xl transition-all shadow-md">
                    <i class="fas fa-plus-circle mr-2"></i>
                    Add Your First Hostel
                </a>
            </div>
        @else
            <form action="{{ route('agent.rooms.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Hostel Selection -->
                <div>
                    <label for="hostel_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Hostel <span class="text-red-500">*</span>
                    </label>
                    <select id="hostel_id"
                            name="hostel_id"
                            required
                            class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors @error('hostel_id') border-red-500 @enderror">
                        <option value="">-- Select Hostel --</option>
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

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Room Number -->
                    <div>
                        <label for="room_number" class="block text-sm font-medium text-gray-700 mb-2">
                            Room Number / Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               id="room_number"
                               name="room_number"
                               value="{{ old('room_number') }}"
                               placeholder="e.g. A101, Room 204"
                               required
                               class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors @error('room_number') border-red-500 @enderror">
                        @error('room_number')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Room Type -->
                    <div>
                        <label for="room_type" class="block text-sm font-medium text-gray-700 mb-2">
                            Room Type <span class="text-red-500">*</span>
                        </label>
                        <select id="room_type"
                                name="room_type"
                                required
                                class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors @error('room_type') border-red-500 @enderror">
                            <option value="">-- Select Room Type --</option>

                            <!-- Single Rooms -->
                            <optgroup label="Single Rooms">
                                <option value="single_room" {{ old('room_type') == 'single_room' ? 'selected' : '' }}>Single Room (Standard)</option>
                                <option value="single_self_contained" {{ old('room_type') == 'single_self_contained' ? 'selected' : '' }}>Single Room - Self Contained</option>
                                <option value="single_private_bathroom" {{ old('room_type') == 'single_private_bathroom' ? 'selected' : '' }}>Single Room - Private Bathroom</option>
                                <option value="single_shared_bathroom" {{ old('room_type') == 'single_shared_bathroom' ? 'selected' : '' }}>Single Room - Shared Bathroom</option>
                                <option value="single_shared_kitchen" {{ old('room_type') == 'single_shared_kitchen' ? 'selected' : '' }}>Single Room - Shared Kitchen</option>
                                <option value="single_shared_kitchen_bathroom" {{ old('room_type') == 'single_shared_kitchen_bathroom' ? 'selected' : '' }}>Single Room - Shared Kitchen & Bathroom</option>
                                <option value="single_premium" {{ old('room_type') == 'single_premium' ? 'selected' : '' }}>Single Room - Premium</option>
                                <option value="single_executive" {{ old('room_type') == 'single_executive' ? 'selected' : '' }}>Single Room - Executive</option>
                                <option value="single_ensuite" {{ old('room_type') == 'single_ensuite' ? 'selected' : '' }}>Single Room - En-suite</option>
                            </optgroup>

                            <!-- Shared Rooms -->
                            <optgroup label="Shared Rooms">
                                <option value="shared_2" {{ old('room_type') == 'shared_2' ? 'selected' : '' }}>Two in a Room (Shared 2)</option>
                                <option value="shared_2_self_contained" {{ old('room_type') == 'shared_2_self_contained' ? 'selected' : '' }}>Two in a Room - Self Contained</option>
                                <option value="shared_2_shared_bathroom" {{ old('room_type') == 'shared_2_shared_bathroom' ? 'selected' : '' }}>Two in a Room - Shared Bathroom</option>
                                <option value="shared_3_self_contained" {{ old('room_type') == 'shared_3_self_contained' ? 'selected' : '' }}>Three in a Room - Self Contained</option>
                                <option value="shared_3_shared_bathroom" {{ old('room_type') == 'shared_3_shared_bathroom' ? 'selected' : '' }}>Three in a Room - Shared Bathroom</option>
                                <option value="shared_4" {{ old('room_type') == 'shared_4' ? 'selected' : '' }}>Four in a Room (Shared 4)</option>
                                <option value="shared_4_self_contained" {{ old('room_type') == 'shared_4_self_contained' ? 'selected' : '' }}>Four in a Room - Self Contained</option>
                                <option value="shared_4_shared_bathroom" {{ old('room_type') == 'shared_4_shared_bathroom' ? 'selected' : '' }}>Four in a Room - Shared Bathroom</option>
                            </optgroup>

                            <!-- Dormitories -->
                            <optgroup label="Dormitories">
                                <option value="dorm_4_shared" {{ old('room_type') == 'dorm_4_shared' ? 'selected' : '' }}>4-Bed Dormitory</option>
                                <option value="dorm_6_shared" {{ old('room_type') == 'dorm_6_shared' ? 'selected' : '' }}>6-Bed Dormitory</option>
                                <option value="dorm_8_shared" {{ old('room_type') == 'dorm_8_shared' ? 'selected' : '' }}>8-Bed Dormitory</option>
                            </optgroup>

                            <!-- Premium & Special Rooms -->
                            <optgroup label="Premium & Special Rooms">
                                <option value="executive" {{ old('room_type') == 'executive' ? 'selected' : '' }}>Executive Suite</option>
                                <option value="studio_self_contained" {{ old('room_type') == 'studio_self_contained' ? 'selected' : '' }}>Studio Apartment</option>
                                <option value="one_bedroom_self_contained" {{ old('room_type') == 'one_bedroom_self_contained' ? 'selected' : '' }}>One-Bedroom Apartment</option>
                            </optgroup>
                        </select>
                        @error('room_type')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Capacity -->
                    <div>
                        <label for="capacity" class="block text-sm font-medium text-gray-700 mb-2">
                            Capacity (Number of Occupants) <span class="text-red-500">*</span>
                        </label>
                        <input type="number"
                               id="capacity"
                               name="capacity"
                               value="{{ old('capacity', 1) }}"
                               min="1"
                               required
                               class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors @error('capacity') border-red-500 @enderror">
                        @error('capacity')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Price Per Year -->
                    <div>
                        <label for="price_per_year" class="block text-sm font-medium text-gray-700 mb-2">
                            Base Price per Year (₵) <span class="text-red-500">*</span>
                        </label>
                        <input type="number"
                               step="0.01"
                               id="price_per_year"
                               name="price_per_year"
                               value="{{ old('price_per_year') }}"
                               placeholder="e.g. 1500.00"
                               min="0"
                               required
                               class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors @error('price_per_year') border-red-500 @enderror">
                        <p class="mt-1 text-xs text-gray-500">Platform processing fees and surcharges will be pre-calculated automatically.</p>
                        @error('price_per_year')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Description
                    </label>
                    <textarea id="description"
                              name="description"
                              rows="3"
                              placeholder="Add details about amenities inside the room (e.g. balcony, AC, wardrobe, study desk)..."
                              class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Is Available -->
                <div class="flex items-center space-x-3 bg-gray-50 p-4 rounded-xl border border-gray-200">
                    <input type="checkbox"
                           id="is_available"
                           name="is_available"
                           value="1"
                           {{ old('is_available', '1') ? 'checked' : '' }}
                           class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                    <label for="is_available" class="text-sm font-medium text-gray-800">
                        Mark room as available for immediate booking
                    </label>
                </div>

                <!-- Submit Action -->
                <div class="pt-4 flex items-center justify-end space-x-3">
                    <a href="{{ $selectedHostel ? route('agent.hostels.show', $selectedHostel->uuid ?? $selectedHostel->id) : route('agent.hostels.index') }}"
                       class="px-5 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-medium rounded-xl transition-colors">
                        Cancel
                    </a>
                    <button type="submit"
                            class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white font-medium text-sm rounded-xl transition-all shadow-md">
                        <i class="fas fa-plus-circle mr-2"></i>
                        Add Room
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>
@endsection
