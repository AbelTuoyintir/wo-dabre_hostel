@extends('layouts.app')

@section('title', 'Edit Room')
@section('page-title', 'Edit Room: ' . $room->number)

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900">Edit Room Information</h3>
                </div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                    @if($room->status == 'available') bg-green-100 text-green-800
                    @elseif($room->status == 'full') bg-yellow-100 text-yellow-800
                    @elseif($room->status == 'maintenance') bg-orange-100 text-orange-800
                    @else bg-gray-100 text-gray-800 @endif">
                    {{ ucfirst($room->status) }}
                </span>
            </div>
        </div>

        <form action="{{ route('admin.rooms.update', $room) }}" method="POST" enctype="multipart/form-data" class="p-6" id="roomForm">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- Hostel Selection and Room Type -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Hostel Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <span class="text-red-500">*</span> Select Hostel
                        </label>
                        <select name="hostel_id" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('hostel_id') border-red-500 @enderror" required>
                            <option value="">Choose a hostel</option>
                            @foreach($hostels as $hostel)
                                <option value="{{ $hostel->id }}" {{ old('hostel_id', $room->hostel_id) == $hostel->id ? 'selected' : '' }}>
                                    {{ $hostel->name }} - {{ $hostel->location }}
                                </option>
                            @endforeach
                        </select>
                        @error('hostel_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Room Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <span class="text-red-500">*</span> Room Type
                        </label>
                        <select name="room_type" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('room_type') border-red-500 @enderror" required>
                            <option value="">Select Room Type</option>

                            <!-- Single Rooms -->
                            <optgroup label="Single Rooms">
                                <option value="single_self_contained" {{ old('room_type', $room->room_type) == 'single_self_contained' ? 'selected' : '' }}>Single Room - Self Contained</option>
                                <option value="single_private_bathroom" {{ old('room_type', $room->room_type) == 'single_private_bathroom' ? 'selected' : '' }}>Single Room - Private Bathroom</option>
                                <option value="single_shared_bathroom" {{ old('room_type', $room->room_type) == 'single_shared_bathroom' ? 'selected' : '' }}>Single Room - Shared Bathroom</option>
                                <option value="single_shared_kitchen" {{ old('room_type', $room->room_type) == 'single_shared_kitchen' ? 'selected' : '' }}>Single Room - Shared Kitchen</option>
                                <option value="single_shared_kitchen_bathroom" {{ old('room_type', $room->room_type) == 'single_shared_kitchen_bathroom' ? 'selected' : '' }}>Single Room - Shared Kitchen &amp; Bathroom</option>
                                <option value="single_premium" {{ old('room_type', $room->room_type) == 'single_premium' ? 'selected' : '' }}>Single Room - Premium</option>
                                <option value="single_executive" {{ old('room_type', $room->room_type) == 'single_executive' ? 'selected' : '' }}>Single Room - Executive</option>
                                <option value="single_standard" {{ old('room_type', $room->room_type) == 'single_standard' ? 'selected' : '' }}>Single Room - Standard</option>
                                <option value="single_deluxe" {{ old('room_type', $room->room_type) == 'single_deluxe' ? 'selected' : '' }}>Single Room - Deluxe</option>
                                <option value="single_ensuite" {{ old('room_type', $room->room_type) == 'single_ensuite' ? 'selected' : '' }}>Single Room - En-suite</option>
                                <option value="single_balcony" {{ old('room_type', $room->room_type) == 'single_balcony' ? 'selected' : '' }}>Single Room - With Balcony</option>
                                <option value="single_furnished" {{ old('room_type', $room->room_type) == 'single_furnished' ? 'selected' : '' }}>Single Room - Furnished</option>
                                <option value="single_ac" {{ old('room_type', $room->room_type) == 'single_ac' ? 'selected' : '' }}>Single Room - With Air Conditioning</option>
                            </optgroup>

                            <!-- Double/Twin Rooms -->
                            <optgroup label="Double Rooms (2 People)">
                                <option value="double_self_contained" {{ old('room_type', $room->room_type) == 'double_self_contained' ? 'selected' : '' }}>Two in a Room - Self Contained</option>
                                <option value="double_private_bathroom" {{ old('room_type', $room->room_type) == 'double_private_bathroom' ? 'selected' : '' }}>Two in a Room - Private Bathroom</option>
                                <option value="double_shared_bathroom" {{ old('room_type', $room->room_type) == 'double_shared_bathroom' ? 'selected' : '' }}>Two in a Room - Shared Bathroom</option>
                                <option value="double_shared_kitchen" {{ old('room_type', $room->room_type) == 'double_shared_kitchen' ? 'selected' : '' }}>Two in a Room - Shared Kitchen</option>
                                <option value="double_shared_kitchen_bathroom" {{ old('room_type', $room->room_type) == 'double_shared_kitchen_bathroom' ? 'selected' : '' }}>Two in a Room - Shared Kitchen &amp; Bathroom</option>
                                <option value="double_ensuite" {{ old('room_type', $room->room_type) == 'double_ensuite' ? 'selected' : '' }}>Two in a Room - En-suite</option>
                                <option value="double_standard" {{ old('room_type', $room->room_type) == 'double_standard' ? 'selected' : '' }}>Two in a Room - Standard</option>
                                <option value="double_executive" {{ old('room_type', $room->room_type) == 'double_executive' ? 'selected' : '' }}>Two in a Room - Executive</option>
                                <option value="double_deluxe" {{ old('room_type', $room->room_type) == 'double_deluxe' ? 'selected' : '' }}>Two in a Room - Deluxe</option>
                                <option value="double_balcony" {{ old('room_type', $room->room_type) == 'double_balcony' ? 'selected' : '' }}>Two in a Room - With Balcony</option>
                                <option value="double_furnished" {{ old('room_type', $room->room_type) == 'double_furnished' ? 'selected' : '' }}>Two in a Room - Furnished</option>
                                <option value="double_ac" {{ old('room_type', $room->room_type) == 'double_ac' ? 'selected' : '' }}>Two in a Room - With Air Conditioning</option>
                            </optgroup>

                            <!-- Triple Rooms -->
                            <optgroup label="Triple Rooms (3 People)">
                                <option value="triple_self_contained" {{ old('room_type', $room->room_type) == 'triple_self_contained' ? 'selected' : '' }}>Three in a Room - Self Contained</option>
                                <option value="triple_private_bathroom" {{ old('room_type', $room->room_type) == 'triple_private_bathroom' ? 'selected' : '' }}>Three in a Room - Private Bathroom</option>
                                <option value="triple_shared_bathroom" {{ old('room_type', $room->room_type) == 'triple_shared_bathroom' ? 'selected' : '' }}>Three in a Room - Shared Bathroom</option>
                                <option value="triple_shared_kitchen" {{ old('room_type', $room->room_type) == 'triple_shared_kitchen' ? 'selected' : '' }}>Three in a Room - Shared Kitchen</option>
                                <option value="triple_shared_kitchen_bathroom" {{ old('room_type', $room->room_type) == 'triple_shared_kitchen_bathroom' ? 'selected' : '' }}>Three in a Room - Shared Kitchen &amp; Bathroom</option>
                                <option value="triple_ensuite" {{ old('room_type', $room->room_type) == 'triple_ensuite' ? 'selected' : '' }}>Three in a Room - En-suite</option>
                                <option value="triple_standard" {{ old('room_type', $room->room_type) == 'triple_standard' ? 'selected' : '' }}>Three in a Room - Standard</option>
                                <option value="triple_balcony" {{ old('room_type', $room->room_type) == 'triple_balcony' ? 'selected' : '' }}>Three in a Room - With Balcony</option>
                            </optgroup>

                            <!-- Quad Rooms (4 People) -->
                            <optgroup label="Quad Rooms (4 People)">
                                <option value="quad_self_contained" {{ old('room_type', $room->room_type) == 'quad_self_contained' ? 'selected' : '' }}>Four in a Room - Self Contained</option>
                                <option value="quad_shared_bathroom" {{ old('room_type', $room->room_type) == 'quad_shared_bathroom' ? 'selected' : '' }}>Four in a Room - Shared Bathroom</option>
                                <option value="quad_shared_kitchen" {{ old('room_type', $room->room_type) == 'quad_shared_kitchen' ? 'selected' : '' }}>Four in a Room - Shared Kitchen</option>
                                <option value="quad_shared_kitchen_bathroom" {{ old('room_type', $room->room_type) == 'quad_shared_kitchen_bathroom' ? 'selected' : '' }}>Four in a Room - Shared Kitchen &amp; Bathroom</option>
                            </optgroup>

                            <!-- Dormitories -->
                            <optgroup label="Dormitories">
                                <option value="dorm_4_shared" {{ old('room_type', $room->room_type) == 'dorm_4_shared' ? 'selected' : '' }}>4-Bed Dormitory - Shared Bathroom</option>
                                <option value="dorm_4_ensuite" {{ old('room_type', $room->room_type) == 'dorm_4_ensuite' ? 'selected' : '' }}>4-Bed Dormitory - En-suite</option>
                                <option value="dorm_6_shared" {{ old('room_type', $room->room_type) == 'dorm_6_shared' ? 'selected' : '' }}>6-Bed Dormitory - Shared Bathroom</option>
                                <option value="dorm_6_ensuite" {{ old('room_type', $room->room_type) == 'dorm_6_ensuite' ? 'selected' : '' }}>6-Bed Dormitory - En-suite</option>
                                <option value="dorm_8_shared" {{ old('room_type', $room->room_type) == 'dorm_8_shared' ? 'selected' : '' }}>8-Bed Dormitory - Shared Bathroom</option>
                                <option value="dorm_8_ensuite" {{ old('room_type', $room->room_type) == 'dorm_8_ensuite' ? 'selected' : '' }}>8-Bed Dormitory - En-suite</option>
                                <option value="dorm_10_shared" {{ old('room_type', $room->room_type) == 'dorm_10_shared' ? 'selected' : '' }}>10-Bed Dormitory - Shared Bathroom</option>
                                <option value="dorm_10_ensuite" {{ old('room_type', $room->room_type) == 'dorm_10_ensuite' ? 'selected' : '' }}>10-Bed Dormitory - En-suite</option>
                                <option value="dorm_12_shared" {{ old('room_type', $room->room_type) == 'dorm_12_shared' ? 'selected' : '' }}>12-Bed Dormitory - Shared Bathroom</option>
                                <option value="dorm_12_ensuite" {{ old('room_type', $room->room_type) == 'dorm_12_ensuite' ? 'selected' : '' }}>12-Bed Dormitory - En-suite</option>
                            </optgroup>

                            <!-- Studio/Apartment -->
                            <optgroup label="Studio / Apartments">
                                <option value="studio_self_contained" {{ old('room_type', $room->room_type) == 'studio_self_contained' ? 'selected' : '' }}>Studio Apartment - Self Contained</option>
                                <option value="studio_kitchenette" {{ old('room_type', $room->room_type) == 'studio_kitchenette' ? 'selected' : '' }}>Studio Apartment - Kitchenette</option>
                                <option value="studio_private_bathroom" {{ old('room_type', $room->room_type) == 'studio_private_bathroom' ? 'selected' : '' }}>Studio Apartment - Private Bathroom</option>
                                <option value="studio_furnished" {{ old('room_type', $room->room_type) == 'studio_furnished' ? 'selected' : '' }}>Studio Apartment - Furnished</option>
                                <option value="one_bedroom_self_contained" {{ old('room_type', $room->room_type) == 'one_bedroom_self_contained' ? 'selected' : '' }}>One-Bedroom Apartment - Self Contained</option>
                                <option value="one_bedroom_kitchenette" {{ old('room_type', $room->room_type) == 'one_bedroom_kitchenette' ? 'selected' : '' }}>One-Bedroom Apartment - Kitchenette</option>
                                <option value="two_bedroom_self_contained" {{ old('room_type', $room->room_type) == 'two_bedroom_self_contained' ? 'selected' : '' }}>Two-Bedroom Apartment - Self Contained</option>
                            </optgroup>

                            <!-- Shared Rooms -->
                            <optgroup label="Shared Rooms">
                                <option value="shared_2_self_contained" {{ old('room_type', $room->room_type) == 'shared_2_self_contained' ? 'selected' : '' }}>Shared Room - 2 People (Self Contained)</option>
                                <option value="shared_2_shared_bathroom" {{ old('room_type', $room->room_type) == 'shared_2_shared_bathroom' ? 'selected' : '' }}>Shared Room - 2 People (Shared Bathroom)</option>
                                <option value="shared_2_shared_kitchen" {{ old('room_type', $room->room_type) == 'shared_2_shared_kitchen' ? 'selected' : '' }}>Shared Room - 2 People (Shared Kitchen)</option>
                                <option value="shared_2_shared_kitchen_bathroom" {{ old('room_type', $room->room_type) == 'shared_2_shared_kitchen_bathroom' ? 'selected' : '' }}>Shared Room - 2 People (Shared Kitchen &amp; Bathroom)</option>
                                <option value="shared_3_self_contained" {{ old('room_type', $room->room_type) == 'shared_3_self_contained' ? 'selected' : '' }}>Shared Room - 3 People (Self Contained)</option>
                                <option value="shared_3_shared_bathroom" {{ old('room_type', $room->room_type) == 'shared_3_shared_bathroom' ? 'selected' : '' }}>Shared Room - 3 People (Shared Bathroom)</option>
                                <option value="shared_4_self_contained" {{ old('room_type', $room->room_type) == 'shared_4_self_contained' ? 'selected' : '' }}>Shared Room - 4 People (Self Contained)</option>
                                <option value="shared_4_shared_bathroom" {{ old('room_type', $room->room_type) == 'shared_4_shared_bathroom' ? 'selected' : '' }}>Shared Room - 4 People (Shared Bathroom)</option>
                            </optgroup>

                            <!-- Premium/Special Rooms -->
                            <optgroup label="Premium &amp; Special Rooms">
                                <option value="executive_suite" {{ old('room_type', $room->room_type) == 'executive_suite' ? 'selected' : '' }}>Executive Suite - Self Contained</option>
                                <option value="presidential_suite" {{ old('room_type', $room->room_type) == 'presidential_suite' ? 'selected' : '' }}>Presidential Suite - Self Contained</option>
                                <option value="honeymoon_suite" {{ old('room_type', $room->room_type) == 'honeymoon_suite' ? 'selected' : '' }}>Honeymoon Suite - Self Contained</option>
                                <option value="family_room_self" {{ old('room_type', $room->room_type) == 'family_room_self' ? 'selected' : '' }}>Family Room - Self Contained</option>
                                <option value="family_room_shared" {{ old('room_type', $room->room_type) == 'family_room_shared' ? 'selected' : '' }}>Family Room - Shared Bathroom</option>
                                <option value="vip_room" {{ old('room_type', $room->room_type) == 'vip_room' ? 'selected' : '' }}>VIP Room - Self Contained</option>
                                <option value="business_room" {{ old('room_type', $room->room_type) == 'business_room' ? 'selected' : '' }}>Business Room - Self Contained</option>
                            </optgroup>

                            <!-- Accessible Rooms -->
                            <optgroup label="Accessible Rooms">
                                <option value="wheelchair_self" {{ old('room_type', $room->room_type) == 'wheelchair_self' ? 'selected' : '' }}>Wheelchair Accessible - Self Contained</option>
                                <option value="wheelchair_shared" {{ old('room_type', $room->room_type) == 'wheelchair_shared' ? 'selected' : '' }}>Wheelchair Accessible - Shared Bathroom</option>
                                <option value="ground_floor_self" {{ old('room_type', $room->room_type) == 'ground_floor_self' ? 'selected' : '' }}>Ground Floor Room - Self Contained</option>
                                <option value="ground_floor_shared" {{ old('room_type', $room->room_type) == 'ground_floor_shared' ? 'selected' : '' }}>Ground Floor Room - Shared Bathroom</option>
                            </optgroup>

                            <!-- Budget Rooms -->
                            <optgroup label="Budget Rooms">
                                <option value="budget_single" {{ old('room_type', $room->room_type) == 'budget_single' ? 'selected' : '' }}>Budget Single - Shared Bathroom</option>
                                <option value="budget_single_kitchen" {{ old('room_type', $room->room_type) == 'budget_single_kitchen' ? 'selected' : '' }}>Budget Single - Shared Kitchen &amp; Bathroom</option>
                                <option value="budget_double" {{ old('room_type', $room->room_type) == 'budget_double' ? 'selected' : '' }}>Budget Double - Shared Bathroom</option>
                                <option value="budget_dorm" {{ old('room_type', $room->room_type) == 'budget_dorm' ? 'selected' : '' }}>Budget Dormitory - Shared Bathroom</option>
                            </optgroup>

                            <!-- Gender-Specific Rooms -->
                            <optgroup label="Gender-Specific Rooms">
                                <option value="female_only_self" {{ old('room_type', $room->room_type) == 'female_only_self' ? 'selected' : '' }}>Female Only - Self Contained</option>
                                <option value="female_only_shared" {{ old('room_type', $room->room_type) == 'female_only_shared' ? 'selected' : '' }}>Female Only - Shared Bathroom</option>
                                <option value="male_only_self" {{ old('room_type', $room->room_type) == 'male_only_self' ? 'selected' : '' }}>Male Only - Self Contained</option>
                                <option value="male_only_shared" {{ old('room_type', $room->room_type) == 'male_only_shared' ? 'selected' : '' }}>Male Only - Shared Bathroom</option>
                            </optgroup>
                        </select>
                        @error('room_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Room Number -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <span class="text-red-500">*</span> Room Number
                        </label>
                        <input type="text" name="number" value="{{ old('number', $room->number) }}"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('number') border-red-500 @enderror"
                               placeholder="e.g., 101, A202" required>
                        @error('number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Floor -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Floor</label>
                        <input type="number" name="floor" value="{{ old('floor', $room->floor) }}"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('floor') border-red-500 @enderror"
                               placeholder="e.g., 1, 2, 3">
                        @error('floor')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Capacity -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <span class="text-red-500">*</span> Capacity (persons)
                        </label>
                        <input type="number" name="capacity" value="{{ old('capacity', $room->capacity) }}" min="1"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('capacity') border-red-500 @enderror"
                               placeholder="Maximum number of occupants" required>
                        @error('capacity')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Current Occupancy (Read Only) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Current Occupancy</label>
                        <div class="w-full px-3 py-2 border border-gray-200 bg-gray-50 rounded-md text-gray-700">
                            {{ $room->current_occupancy }} / {{ $room->capacity }} persons
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Current occupancy cannot be edited directly</p>
                    </div>

                    <!-- Size -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Size (sqm)</label>
                        <input type="number" name="size_sqm" value="{{ old('size_sqm', $room->size_sqm) }}" step="0.01" min="1"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('size_sqm') border-red-500 @enderror"
                               placeholder="e.g., 25.5">
                        @error('size_sqm')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Price per Month -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Price per academic ($)</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">$</span>
                            </div>
                            <input type="number" name="room_cost" value="{{ old('room_cost', $room->room_cost) }}" step="0.01" min="0"
                                   class="w-full pl-7 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('room_cost') border-red-500 @enderror"
                                   placeholder="0.00">
                        </div>
                        @error('room_cost')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Window Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Window View</label>
                        <select name="window_type" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select view type</option>
                            <option value="street" {{ old('window_type', $room->window_type) == 'street' ? 'selected' : '' }}>Street View</option>
                            <option value="courtyard" {{ old('window_type', $room->window_type) == 'courtyard' ? 'selected' : '' }}>Courtyard</option>
                            <option value="garden" {{ old('window_type', $room->window_type) == 'garden' ? 'selected' : '' }}>Garden</option>
                            <option value="none" {{ old('window_type', $room->window_type) == 'none' ? 'selected' : '' }}>No Window</option>
                        </select>
                        @error('window_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Gender Preference -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <span class="text-red-500">*</span> Gender Preference
                        </label>
                        <select name="gender" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('gender') border-red-500 @enderror" required>
                            <option value="any" {{ old('gender', $room->gender) == 'any' ? 'selected' : '' }}>Any Gender</option>
                            <option value="male" {{ old('gender', $room->gender) == 'male' ? 'selected' : '' }}>Male Only</option>
                            <option value="female" {{ old('gender', $room->gender) == 'female' ? 'selected' : '' }}>Female Only</option>
                        </select>
                        @error('gender')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <span class="text-red-500">*</span> Status
                        </label>
                        <select name="status" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('status') border-red-500 @enderror" required>
                            <option value="available" {{ old('status', $room->status) == 'available' ? 'selected' : '' }}>Available</option>
                            <option value="full" {{ old('status', $room->status) == 'full' ? 'selected' : '' }}>Full</option>
                            <option value="maintenance" {{ old('status', $room->status) == 'maintenance' ? 'selected' : '' }}>Under Maintenance</option>
                            <option value="inactive" {{ old('status', $room->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Room Features -->
                <div class="border-t border-gray-200 pt-6">
                    <h4 class="text-md font-medium text-gray-900 mb-4">Room Features</h4>
                    <div class="space-y-4">
                        <div class="flex items-center space-x-6">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="furnished" value="1" {{ old('furnished', $room->furnished) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Furnished</span>
                            </label>
                            <span class="text-xs text-gray-500">(Bed, desk, chair, wardrobe)</span>
                        </div>

                        <div class="flex items-center space-x-6">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="private_bathroom" value="1" {{ old('private_bathroom', $room->private_bathroom) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Private Bathroom</span>
                            </label>
                            <span class="text-xs text-gray-500">(En-suite bathroom)</span>
                        </div>
                    </div>
                </div>

                <!-- Room Images Section -->
                <div class="border-t border-gray-200 pt-6">
                    <h4 class="text-md font-medium text-gray-900 mb-4">Room Images</h4>

                    <!-- Current Images Display -->
                    @if($room->images && count($room->images) > 0)
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-3">Current Images</label>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4" id="current-images">
                                @foreach($room->images as $image)
                                    <div class="relative group border rounded-lg p-2 {{ $image->is_primary ? 'bg-blue-50 border-blue-300' : 'border-gray-200' }}" data-image-id="{{ $image->id }}" id="image-{{ $image->id }}">
                                        <img src="{{ image_url($image->image_path) }}"
                                             alt="Room {{ $room->number }}"
                                             class="w-full h-32 object-cover rounded-lg mb-2">

                                        <!-- Image Labels -->
                                        <div class="absolute top-3 left-3 flex gap-1">
                                            @if($image->is_primary)
                                                <span class="bg-blue-500 text-white text-xs px-2 py-1 rounded-full">
                                                    Primary
                                                </span>
                                            @endif
                                            @if($image->video)
                                                <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full">
                                                    Video
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Image Actions -->
                                        <div class="flex flex-wrap justify-between items-center mt-2 gap-1">
                                            <div class="flex gap-1">
                                                @if(!$image->is_primary)
                                                    <button type="button"
                                                            onclick="setAsPrimary({{ $image->id }})"
                                                            class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded hover:bg-blue-200">
                                                        Make Primary
                                                    </button>
                                                @endif

                                                <button type="button"
                                                        onclick="markForRemoval({{ $image->id }})"
                                                        class="text-xs bg-red-100 text-red-700 px-2 py-1 rounded hover:bg-red-200">
                                                    Remove
                                                </button>
                                            </div>

                                            <!-- Order Indicator -->
                                            @if(!$image->is_primary)
                                                <span class="text-xs text-gray-500">#{{ $image->order }}</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Hidden inputs for removed images -->
                            <div id="removed-images-container"></div>

                            <!-- Hidden input for primary image change -->
                            <input type="hidden" name="primary_image_id" id="primary_image_id" value="">
                        </div>
                    @endif

                    <!-- Add New Images Section -->
                    <div class="border-t border-gray-100 pt-4">
                        <h5 class="text-sm font-medium text-gray-700 mb-3">Add New Images</h5>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Primary/Cover Image Upload -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Cover Image
                                </label>
                                <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 hover:border-blue-500 transition-colors">
                                    <div class="text-center">
                                        <svg class="mx-auto h-10 w-10 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H8a4 4 0 01-4-4V12a4 4 0 014-4h32a4 4 0 014 4v16.5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="mt-2">
                                            <label for="cover_image" class="cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500">
                                                <span>Upload cover image</span>
                                                <input id="cover_image" name="cover_image" type="file" class="sr-only" accept="image/*" onchange="previewCoverImage(this)">
                                            </label>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1">PNG, JPG up to 10MB</p>
                                    </div>
                                    <div id="cover-preview" class="mt-2 hidden">
                                        <div class="relative inline-block">
                                            <img src="" class="h-24 w-auto rounded-lg mx-auto" alt="Cover preview">
                                            <button type="button" onclick="removeCoverImage()" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs hover:bg-red-600">
                                                ×
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Gallery Images Upload -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Gallery Images
                                </label>
                                <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 hover:border-blue-500 transition-colors">
                                    <div class="text-center">
                                        <svg class="mx-auto h-10 w-10 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H8a4 4 0 01-4-4V12a4 4 0 014-4h32a4 4 0 014 4v16.5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M30 28l-6-6-6 6M20 16h.01" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="mt-2">
                                            <label for="gallery_images" class="cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500">
                                                <span>Upload gallery images</span>
                                                <input id="gallery_images" name="gallery_images[]" type="file" class="sr-only" accept="image/*" multiple onchange="previewGalleryImages(this)">
                                            </label>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1">PNG, JPG up to 10MB each (max 5)</p>
                                    </div>
                                    <div id="gallery-preview" class="grid grid-cols-2 gap-2 mt-3 hidden"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Room Video Upload -->
                <div class="border-t border-gray-200 pt-6">
                    <h4 class="text-md font-medium text-gray-900 mb-4">Room Video</h4>

                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 hover:border-blue-500 transition-colors">
                        @php
                            $roomVideo = $room->roomVideo()->first();
                        @endphp

                        @if($roomVideo)
                            <div id="current-video-container" class="mb-4">
                                <div class="relative">
                                    <video controls class="w-full rounded-lg border max-h-64">
                                        <source src="{{ image_url($roomVideo->image_path) }}" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                    <p class="text-xs text-gray-500 mt-2">Current video</p>
                                    <button type="button" 
                                            onclick="removeCurrentVideo()" 
                                            class="absolute top-2 right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm hover:bg-red-600">
                                        ×
                                    </button>
                                </div>
                            </div>
                            <input type="hidden" name="remove_video" id="remove_video" value="0">
                        @endif

                        <label class="block text-sm font-medium text-gray-700 mb-2">Upload/Replace Video</label>
                        <input type="file" name="room_video" id="room_video" accept="video/*" onchange="previewRoomVideo(this)" class="w-full border-gray-300 rounded-md">
                        <p class="text-xs text-gray-500 mt-2">MP4/WebM up to 50MB</p>

                        <div id="video-preview" class="mt-3 hidden">
                            <div class="relative">
                                <video controls class="w-full rounded-lg border max-h-64" src=""></video>
                                <button type="button" onclick="removeVideoPreview()" class="absolute top-2 right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm hover:bg-red-600">
                                    ×
                                </button>
                            </div>
                            <div id="video-info" class="mt-2 text-xs text-gray-500"></div>
                        </div>

                        @error('room_video')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Description -->
                <div class="border-t border-gray-200 pt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Room Description</label>
                    <textarea name="description" rows="4"
                              class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror"
                              placeholder="Describe the room, its features, and any special notes...">{{ old('description', $room->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="mt-8 flex items-center justify-between border-t border-gray-200 pt-6">
                <div>
                    <button type="button" onclick="confirmDelete()"
                            class="px-4 py-2 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Delete Room
                    </button>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.rooms.show', $room) }}"
                       class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Update Room
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Danger Zone - Only show if no active bookings -->
    @if($room->bookings()->whereIn('status', ['pending', 'confirmed'])->count() == 0)
    <div class="mt-6 bg-red-50 rounded-lg border border-red-200 overflow-hidden">
        <div class="px-6 py-4 bg-red-100 border-b border-red-200">
            <h4 class="text-sm font-medium text-red-800 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                Danger Zone
            </h4>
        </div>
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h5 class="text-sm font-medium text-gray-900">Delete this room</h5>
                    <p class="text-sm text-gray-600">Once deleted, this room cannot be recovered. All booking history will be preserved.</p>
                </div>
                <form id="delete-form" action="{{ route('admin.rooms.destroy', $room) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Are you absolutely sure you want to delete this room? This action cannot be undone.')"
                            class="px-4 py-2 bg-red-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Permanently Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
    // Store removed image IDs
    let removedImages = [];

    function markForRemoval(imageId) {
        if (confirm('Are you sure you want to remove this image?')) {
            removedImages.push(imageId);

            // Add to hidden inputs
            updateRemovedImagesInput();

            // Hide the image container
            const imageContainer = document.getElementById(`image-${imageId}`);
            if (imageContainer) {
                imageContainer.style.opacity = '0.4';
                imageContainer.style.pointerEvents = 'none';
                imageContainer.classList.add('bg-gray-100');

                // Add "marked for removal" text
                const existingBadge = imageContainer.querySelector('.removal-badge');
                if (!existingBadge) {
                    const removalBadge = document.createElement('div');
                    removalBadge.className = 'removal-badge absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-red-500 text-white px-3 py-1 rounded-full text-sm font-bold z-10';
                    removalBadge.textContent = '🗑️ Marked for Removal';
                    imageContainer.style.position = 'relative';
                    imageContainer.appendChild(removalBadge);
                }
            }
        }
    }

    function updateRemovedImagesInput() {
        const container = document.getElementById('removed-images-container');
        container.innerHTML = '';

        removedImages.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'removed_images[]';
            input.value = id;
            container.appendChild(input);
        });
    }

    function setAsPrimary(imageId) {
        // Update hidden input
        document.getElementById('primary_image_id').value = imageId;

        // Update UI
        document.querySelectorAll('[data-image-id]').forEach(container => {
            container.classList.remove('bg-blue-50', 'border-blue-300');

            // Remove existing primary badge if any
            const existingBadge = container.querySelector('.bg-blue-500');
            if (existingBadge) {
                existingBadge.remove();
            }
        });

        // Add primary styling to selected image
        const selectedContainer = document.getElementById(`image-${imageId}`);
        if (selectedContainer) {
            selectedContainer.classList.add('bg-blue-50', 'border-blue-300');

            // Add primary badge
            const badgeContainer = selectedContainer.querySelector('.absolute.top-3.left-3');
            if (badgeContainer) {
                // Remove existing badges
                badgeContainer.innerHTML = '';
                const primaryBadge = document.createElement('span');
                primaryBadge.className = 'bg-blue-500 text-white text-xs px-2 py-1 rounded-full';
                primaryBadge.textContent = 'Primary';
                badgeContainer.appendChild(primaryBadge);
            }
        }

        // Show confirmation
        showToast('Primary image updated successfully!', 'success');
    }

    function previewCoverImage(input) {
        const preview = document.getElementById('cover-preview');
        const previewImg = preview.querySelector('img');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                preview.classList.remove('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        } else {
            preview.classList.add('hidden');
            previewImg.src = '';
        }
    }

    function removeCoverImage() {
        const input = document.getElementById('cover_image');
        const preview = document.getElementById('cover-preview');
        const previewImg = preview.querySelector('img');
        
        input.value = '';
        previewImg.src = '';
        preview.classList.add('hidden');
    }

    function previewGalleryImages(input) {
        const preview = document.getElementById('gallery-preview');
        preview.innerHTML = '';

        if (input.files && input.files.length > 0) {
            preview.classList.remove('hidden');

            const maxFiles = Math.min(input.files.length, 5);

            Array.from(input.files).slice(0, maxFiles).forEach((file, index) => {
                const reader = new FileReader();

                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'relative group';
                    div.innerHTML = `
                        <img src="${e.target.result}" 
                             class="w-full h-20 object-cover rounded-lg border border-gray-200">
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition-all rounded-lg"></div>
                        <span class="absolute top-1 right-1 bg-green-500 text-white text-xs px-1.5 py-0.5 rounded-full">
                            New
                        </span>
                        <button type="button" 
                                onclick="removeGalleryImage(${index})" 
                                class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs hover:bg-red-600 opacity-0 group-hover:opacity-100 transition-opacity">
                            ×
                        </button>
                    `;
                    preview.appendChild(div);
                }

                reader.readAsDataURL(file);
            });

            // Show warning if more than 5 files selected
            if (input.files.length > 5) {
                const warningDiv = document.createElement('div');
                warningDiv.className = 'col-span-full text-center text-xs text-amber-600 mt-2 p-2 bg-amber-50 rounded';
                warningDiv.innerHTML = '⚠️ Maximum 5 images allowed. Only the first 5 will be uploaded.';
                preview.appendChild(warningDiv);
            }
        } else {
            preview.classList.add('hidden');
        }
    }

    function removeGalleryImage(index) {
        const input = document.getElementById('gallery_images');
        const dt = new DataTransfer();
        const files = input.files;
        
        // Convert FileList to Array and remove the file at the specified index
        const fileArray = Array.from(files);
        fileArray.splice(index, 1);
        
        // Add remaining files back to DataTransfer
        fileArray.forEach(file => {
            dt.items.add(file);
        });
        
        input.files = dt.files;
        
        // Trigger change event to refresh preview
        const event = new Event('change');
        input.dispatchEvent(event);
    }

    function previewRoomVideo(input) {
        const preview = document.getElementById('video-preview');
        const video = preview.querySelector('video');
        const info = document.getElementById('video-info');

        if (input.files && input.files[0]) {
            const file = input.files[0];
            const url = URL.createObjectURL(file);
            
            video.src = url;
            preview.classList.remove('hidden');
            
            // Show video info
            info.innerHTML = `
                <p>📹 File: ${file.name}</p>
                <p>📦 Size: ${(file.size / 1024 / 1024).toFixed(2)} MB</p>
                <p>📝 Type: ${file.type}</p>
            `;
            
            // Hide current video if exists
            const currentVideoContainer = document.getElementById('current-video-container');
            if (currentVideoContainer) {
                currentVideoContainer.style.display = 'none';
            }
        }
    }

    function removeVideoPreview() {
        const input = document.getElementById('room_video');
        const preview = document.getElementById('video-preview');
        const video = preview.querySelector('video');
        const info = document.getElementById('video-info');
        
        input.value = '';
        video.src = '';
        preview.classList.add('hidden');
        info.innerHTML = '';
        
        // Show current video if it was hidden
        const currentVideoContainer = document.getElementById('current-video-container');
        if (currentVideoContainer) {
            currentVideoContainer.style.display = 'block';
        }
    }

    function removeCurrentVideo() {
        if (confirm('Are you sure you want to remove the current video?')) {
            document.getElementById('remove_video').value = '1';
            const container = document.getElementById('current-video-container');
            container.style.display = 'none';
            showToast('Video marked for removal', 'info');
        }
    }

    function confirmDelete() {
        if (confirm('Are you sure you want to delete this room?')) {
            document.getElementById('delete-form').submit();
        }
    }

    // Toast notification function
    function showToast(message, type = 'info') {
        const colors = {
            success: 'bg-green-500',
            error: 'bg-red-500',
            info: 'bg-blue-500',
            warning: 'bg-yellow-500'
        };

        const toast = document.createElement('div');
        toast.className = `fixed bottom-4 right-4 ${colors[type]} text-white px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300 translate-y-0 opacity-100`;
        toast.textContent = message;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.classList.add('opacity-0', 'translate-y-2');
            setTimeout(() => {
                toast.remove();
            }, 300);
        }, 3000);
    }

    // Form submission validation
    document.getElementById('roomForm').addEventListener('submit', function(e) {
        // Check if there are any images selected for upload
        const coverImage = document.getElementById('cover_image');
        const galleryImages = document.getElementById('gallery_images');
        const currentImages = document.querySelectorAll('#current-images [data-image-id]');
        
        // If there are no current images and no new images uploaded, show warning
        if (currentImages.length === 0 && 
            (!coverImage.files || coverImage.files.length === 0) && 
            (!galleryImages.files || galleryImages.files.length === 0)) {
            e.preventDefault();
            showToast('Please upload at least one image for the room.', 'error');
            return false;
        }
        
        return true;
    });
</script>
@endpush

@push('styles')
<style>
    .border-dashed {
        background-image: url("data:image/svg+xml,%3csvg width='100%25' height='100%25' xmlns='http://www.w3.org/2000/svg'%3e%3crect width='100%25' height='100%25' fill='none' stroke='%23CBD5E0' stroke-width='2' stroke-dasharray='6%2c 14' stroke-dashoffset='0' stroke-linecap='square'/%3e%3c/svg%3e);
    }
    .border-dashed:hover {
        background-image: url("data:image/svg+xml,%3csvg width='100%25' height='100%25' xmlns='http://www.w3.org/2000/svg'%3e%3crect width='100%25' height='100%25' fill='none' stroke='%233B82F6' stroke-width='2' stroke-dasharray='6%2c 14' stroke-dashoffset='0' stroke-linecap='square'/%3e%3c/svg%3e);
    }
    
    /* Gallery hover effects */
    #gallery-preview > div {
        transition: transform 0.2s ease;
    }
    #gallery-preview > div:hover {
        transform: scale(1.05);
        z-index: 10;
    }
    
    /* Current images hover effects */
    #current-images > div {
        transition: all 0.3s ease;
    }
    #current-images > div:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    
    /* Video preview styling */
    #video-preview video {
        max-height: 300px;
        background: #000;
    }
    
    /* Animations */
    .removal-badge {
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.7;
        }
    }
</style>
@endpush
@endsection