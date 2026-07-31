@extends('layouts.app')

@section('title', 'Add New Room')
@section('page-title', 'Create New Room')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-medium text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Room Information
            </h3>
            <p class="mt-1 text-sm text-gray-600">Add a new room to your hostel system.</p>
        </div>

        <form action="{{ route('admin.rooms.store') }}" method="POST" enctype="multipart/form-data" class="p-6" id="roomForm">
            @csrf

            @if($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Validation Error!</strong>
                    <ul class="mt-2 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="space-y-6">
                <!-- Hostel Selection -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <span class="text-red-500">*</span> Select Hostel
                        </label>
                        <select name="hostel_id" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('hostel_id') border-red-500 @enderror" required>
                            <option value="">Choose a hostel</option>
                            @foreach($hostels as $hostel)
                                <option value="{{ $hostel->id }}" {{ old('hostel_id') == $hostel->id ? 'selected' : '' }}>
                                    {{ $hostel->name }} - {{ $hostel->location }}
                                </option>
                            @endforeach
                        </select>
                        @error('hostel_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <span class="text-red-500">*</span> Room Type
                        </label>
                        <select name="room_type" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('room_type') border-red-500 @enderror" required>
                            <option value="">Select Room Type</option>
                            
                            <!-- Single Rooms -->
                            <optgroup label="Single Rooms">
                                <option value="single_self_contained" {{ old('room_type') == 'single_self_contained' ? 'selected' : '' }}>Single Room - Self Contained</option>
                                <option value="single_private_bathroom" {{ old('room_type') == 'single_private_bathroom' ? 'selected' : '' }}>Single Room - Private Bathroom</option>
                                <option value="single_shared_bathroom" {{ old('room_type') == 'single_shared_bathroom' ? 'selected' : '' }}>Single Room - Shared Bathroom</option>
                                <option value="single_shared_kitchen" {{ old('room_type') == 'single_shared_kitchen' ? 'selected' : '' }}>Single Room - Shared Kitchen</option>
                                <option value="single_shared_kitchen_bathroom" {{ old('room_type') == 'single_shared_kitchen_bathroom' ? 'selected' : '' }}>Single Room - Shared Kitchen &amp; Bathroom</option>
                                <option value="single_premium" {{ old('room_type') == 'single_premium' ? 'selected' : '' }}>Single Room - Premium</option>
                                <option value="single_executive" {{ old('room_type') == 'single_executive' ? 'selected' : '' }}>Single Room - Executive</option>
                                <option value="single_standard" {{ old('room_type') == 'single_standard' ? 'selected' : '' }}>Single Room - Standard</option>
                                <option value="single_deluxe" {{ old('room_type') == 'single_deluxe' ? 'selected' : '' }}>Single Room - Deluxe</option>
                                <option value="single_ensuite" {{ old('room_type') == 'single_ensuite' ? 'selected' : '' }}>Single Room - En-suite</option>
                                <option value="single_balcony" {{ old('room_type') == 'single_balcony' ? 'selected' : '' }}>Single Room - With Balcony</option>
                                <option value="single_furnished" {{ old('room_type') == 'single_furnished' ? 'selected' : '' }}>Single Room - Furnished</option>
                                <option value="single_ac" {{ old('room_type') == 'single_ac' ? 'selected' : '' }}>Single Room - With Air Conditioning</option>
                            </optgroup>

                            <!-- Double/Twin Rooms -->
                            <optgroup label="Double Rooms (2 People)">
                                <option value="double_self_contained" {{ old('room_type') == 'double_self_contained' ? 'selected' : '' }}>Two in a Room - Self Contained</option>
                                <option value="double_private_bathroom" {{ old('room_type') == 'double_private_bathroom' ? 'selected' : '' }}>Two in a Room - Private Bathroom</option>
                                <option value="double_shared_bathroom" {{ old('room_type') == 'double_shared_bathroom' ? 'selected' : '' }}>Two in a Room - Shared Bathroom</option>
                                <option value="double_shared_kitchen" {{ old('room_type') == 'double_shared_kitchen' ? 'selected' : '' }}>Two in a Room - Shared Kitchen</option>
                                <option value="double_shared_kitchen_bathroom" {{ old('room_type') == 'double_shared_kitchen_bathroom' ? 'selected' : '' }}>Two in a Room - Shared Kitchen &amp; Bathroom</option>
                                <option value="double_ensuite" {{ old('room_type') == 'double_ensuite' ? 'selected' : '' }}>Two in a Room - En-suite</option>
                                <option value="double_standard" {{ old('room_type') == 'double_standard' ? 'selected' : '' }}>Two in a Room - Standard</option>
                                <option value="double_executive" {{ old('room_type') == 'double_executive' ? 'selected' : '' }}>Two in a Room - Executive</option>
                                <option value="double_deluxe" {{ old('room_type') == 'double_deluxe' ? 'selected' : '' }}>Two in a Room - Deluxe</option>
                                <option value="double_balcony" {{ old('room_type') == 'double_balcony' ? 'selected' : '' }}>Two in a Room - With Balcony</option>
                                <option value="double_furnished" {{ old('room_type') == 'double_furnished' ? 'selected' : '' }}>Two in a Room - Furnished</option>
                                <option value="double_ac" {{ old('room_type') == 'double_ac' ? 'selected' : '' }}>Two in a Room - With Air Conditioning</option>
                            </optgroup>

                            <!-- Triple Rooms -->
                            <optgroup label="Triple Rooms (3 People)">
                                <option value="triple_self_contained" {{ old('room_type') == 'triple_self_contained' ? 'selected' : '' }}>Three in a Room - Self Contained</option>
                                <option value="triple_private_bathroom" {{ old('room_type') == 'triple_private_bathroom' ? 'selected' : '' }}>Three in a Room - Private Bathroom</option>
                                <option value="triple_shared_bathroom" {{ old('room_type') == 'triple_shared_bathroom' ? 'selected' : '' }}>Three in a Room - Shared Bathroom</option>
                                <option value="triple_shared_kitchen" {{ old('room_type') == 'triple_shared_kitchen' ? 'selected' : '' }}>Three in a Room - Shared Kitchen</option>
                                <option value="triple_shared_kitchen_bathroom" {{ old('room_type') == 'triple_shared_kitchen_bathroom' ? 'selected' : '' }}>Three in a Room - Shared Kitchen &amp; Bathroom</option>
                                <option value="triple_ensuite" {{ old('room_type') == 'triple_ensuite' ? 'selected' : '' }}>Three in a Room - En-suite</option>
                                <option value="triple_standard" {{ old('room_type') == 'triple_standard' ? 'selected' : '' }}>Three in a Room - Standard</option>
                                <option value="triple_balcony" {{ old('room_type') == 'triple_balcony' ? 'selected' : '' }}>Three in a Room - With Balcony</option>
                            </optgroup>

                            <!-- Quad Rooms (4 People) -->
                            <optgroup label="Quad Rooms (4 People)">
                                <option value="quad_self_contained" {{ old('room_type') == 'quad_self_contained' ? 'selected' : '' }}>Four in a Room - Self Contained</option>
                                <option value="quad_shared_bathroom" {{ old('room_type') == 'quad_shared_bathroom' ? 'selected' : '' }}>Four in a Room - Shared Bathroom</option>
                                <option value="quad_shared_kitchen" {{ old('room_type') == 'quad_shared_kitchen' ? 'selected' : '' }}>Four in a Room - Shared Kitchen</option>
                                <option value="quad_shared_kitchen_bathroom" {{ old('room_type') == 'quad_shared_kitchen_bathroom' ? 'selected' : '' }}>Four in a Room - Shared Kitchen &amp; Bathroom</option>
                            </optgroup>

                            <!-- Dormitories -->
                            <optgroup label="Dormitories">
                                <option value="dorm_4_shared" {{ old('room_type') == 'dorm_4_shared' ? 'selected' : '' }}>4-Bed Dormitory - Shared Bathroom</option>
                                <option value="dorm_4_ensuite" {{ old('room_type') == 'dorm_4_ensuite' ? 'selected' : '' }}>4-Bed Dormitory - En-suite</option>
                                <option value="dorm_6_shared" {{ old('room_type') == 'dorm_6_shared' ? 'selected' : '' }}>6-Bed Dormitory - Shared Bathroom</option>
                                <option value="dorm_6_ensuite" {{ old('room_type') == 'dorm_6_ensuite' ? 'selected' : '' }}>6-Bed Dormitory - En-suite</option>
                                <option value="dorm_8_shared" {{ old('room_type') == 'dorm_8_shared' ? 'selected' : '' }}>8-Bed Dormitory - Shared Bathroom</option>
                                <option value="dorm_8_ensuite" {{ old('room_type') == 'dorm_8_ensuite' ? 'selected' : '' }}>8-Bed Dormitory - En-suite</option>
                                <option value="dorm_10_shared" {{ old('room_type') == 'dorm_10_shared' ? 'selected' : '' }}>10-Bed Dormitory - Shared Bathroom</option>
                                <option value="dorm_10_ensuite" {{ old('room_type') == 'dorm_10_ensuite' ? 'selected' : '' }}>10-Bed Dormitory - En-suite</option>
                                <option value="dorm_12_shared" {{ old('room_type') == 'dorm_12_shared' ? 'selected' : '' }}>12-Bed Dormitory - Shared Bathroom</option>
                                <option value="dorm_12_ensuite" {{ old('room_type') == 'dorm_12_ensuite' ? 'selected' : '' }}>12-Bed Dormitory - En-suite</option>
                            </optgroup>

                            <!-- Studio/Apartment -->
                            <optgroup label="Studio / Apartments">
                                <option value="studio_self_contained" {{ old('room_type') == 'studio_self_contained' ? 'selected' : '' }}>Studio Apartment - Self Contained</option>
                                <option value="studio_kitchenette" {{ old('room_type') == 'studio_kitchenette' ? 'selected' : '' }}>Studio Apartment - Kitchenette</option>
                                <option value="studio_private_bathroom" {{ old('room_type') == 'studio_private_bathroom' ? 'selected' : '' }}>Studio Apartment - Private Bathroom</option>
                                <option value="studio_furnished" {{ old('room_type') == 'studio_furnished' ? 'selected' : '' }}>Studio Apartment - Furnished</option>
                                <option value="one_bedroom_self_contained" {{ old('room_type') == 'one_bedroom_self_contained' ? 'selected' : '' }}>One-Bedroom Apartment - Self Contained</option>
                                <option value="one_bedroom_kitchenette" {{ old('room_type') == 'one_bedroom_kitchenette' ? 'selected' : '' }}>One-Bedroom Apartment - Kitchenette</option>
                                <option value="two_bedroom_self_contained" {{ old('room_type') == 'two_bedroom_self_contained' ? 'selected' : '' }}>Two-Bedroom Apartment - Self Contained</option>
                            </optgroup>

                            <!-- Shared Rooms -->
                            <optgroup label="Shared Rooms">
                                <option value="shared_2_self_contained" {{ old('room_type') == 'shared_2_self_contained' ? 'selected' : '' }}>Shared Room - 2 People (Self Contained)</option>
                                <option value="shared_2_shared_bathroom" {{ old('room_type') == 'shared_2_shared_bathroom' ? 'selected' : '' }}>Shared Room - 2 People (Shared Bathroom)</option>
                                <option value="shared_2_shared_kitchen" {{ old('room_type') == 'shared_2_shared_kitchen' ? 'selected' : '' }}>Shared Room - 2 People (Shared Kitchen)</option>
                                <option value="shared_2_shared_kitchen_bathroom" {{ old('room_type') == 'shared_2_shared_kitchen_bathroom' ? 'selected' : '' }}>Shared Room - 2 People (Shared Kitchen &amp; Bathroom)</option>
                                <option value="shared_3_self_contained" {{ old('room_type') == 'shared_3_self_contained' ? 'selected' : '' }}>Shared Room - 3 People (Self Contained)</option>
                                <option value="shared_3_shared_bathroom" {{ old('room_type') == 'shared_3_shared_bathroom' ? 'selected' : '' }}>Shared Room - 3 People (Shared Bathroom)</option>
                                <option value="shared_4_self_contained" {{ old('room_type') == 'shared_4_self_contained' ? 'selected' : '' }}>Shared Room - 4 People (Self Contained)</option>
                                <option value="shared_4_shared_bathroom" {{ old('room_type') == 'shared_4_shared_bathroom' ? 'selected' : '' }}>Shared Room - 4 People (Shared Bathroom)</option>
                            </optgroup>

                            <!-- Premium/Special Rooms -->
                            <optgroup label="Premium &amp; Special Rooms">
                                <option value="executive_suite" {{ old('room_type') == 'executive_suite' ? 'selected' : '' }}>Executive Suite - Self Contained</option>
                                <option value="presidential_suite" {{ old('room_type') == 'presidential_suite' ? 'selected' : '' }}>Presidential Suite - Self Contained</option>
                                <option value="honeymoon_suite" {{ old('room_type') == 'honeymoon_suite' ? 'selected' : '' }}>Honeymoon Suite - Self Contained</option>
                                <option value="family_room_self" {{ old('room_type') == 'family_room_self' ? 'selected' : '' }}>Family Room - Self Contained</option>
                                <option value="family_room_shared" {{ old('room_type') == 'family_room_shared' ? 'selected' : '' }}>Family Room - Shared Bathroom</option>
                                <option value="vip_room" {{ old('room_type') == 'vip_room' ? 'selected' : '' }}>VIP Room - Self Contained</option>
                                <option value="business_room" {{ old('room_type') == 'business_room' ? 'selected' : '' }}>Business Room - Self Contained</option>
                            </optgroup>

                            <!-- Accessible Rooms -->
                            <optgroup label="Accessible Rooms">
                                <option value="wheelchair_self" {{ old('room_type') == 'wheelchair_self' ? 'selected' : '' }}>Wheelchair Accessible - Self Contained</option>
                                <option value="wheelchair_shared" {{ old('room_type') == 'wheelchair_shared' ? 'selected' : '' }}>Wheelchair Accessible - Shared Bathroom</option>
                                <option value="ground_floor_self" {{ old('room_type') == 'ground_floor_self' ? 'selected' : '' }}>Ground Floor Room - Self Contained</option>
                                <option value="ground_floor_shared" {{ old('room_type') == 'ground_floor_shared' ? 'selected' : '' }}>Ground Floor Room - Shared Bathroom</option>
                            </optgroup>

                            <!-- Budget Rooms -->
                            <optgroup label="Budget Rooms">
                                <option value="budget_single" {{ old('room_type') == 'budget_single' ? 'selected' : '' }}>Budget Single - Shared Bathroom</option>
                                <option value="budget_single_kitchen" {{ old('room_type') == 'budget_single_kitchen' ? 'selected' : '' }}>Budget Single - Shared Kitchen &amp; Bathroom</option>
                                <option value="budget_double" {{ old('room_type') == 'budget_double' ? 'selected' : '' }}>Budget Double - Shared Bathroom</option>
                                <option value="budget_dorm" {{ old('room_type') == 'budget_dorm' ? 'selected' : '' }}>Budget Dormitory - Shared Bathroom</option>
                            </optgroup>

                            <!-- Gender-Specific Rooms -->
                            <optgroup label="Gender-Specific Rooms">
                                <option value="female_only_self" {{ old('room_type') == 'female_only_self' ? 'selected' : '' }}>Female Only - Self Contained</option>
                                <option value="female_only_shared" {{ old('room_type') == 'female_only_shared' ? 'selected' : '' }}>Female Only - Shared Bathroom</option>
                                <option value="male_only_self" {{ old('room_type') == 'male_only_self' ? 'selected' : '' }}>Male Only - Self Contained</option>
                                <option value="male_only_shared" {{ old('room_type') == 'male_only_shared' ? 'selected' : '' }}>Male Only - Shared Bathroom</option>
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
                        <input type="text" name="number" value="{{ old('number') }}"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('number') border-red-500 @enderror"
                               placeholder="e.g., 101, A202" required>
                        @error('number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Floor -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Floor</label>
                        <input type="number" name="floor" value="{{ old('floor') }}"
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
                        <input type="number" name="capacity" value="{{ old('capacity', 1) }}" min="1"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('capacity') border-red-500 @enderror"
                               placeholder="Maximum number of occupants" required>
                        @error('capacity')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Size -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Size (sqm)</label>
                        <input type="number" name="size_sqm" value="{{ old('size_sqm') }}" step="0.01" min="1"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('size_sqm') border-red-500 @enderror"
                               placeholder="e.g., 25.5">
                        @error('size_sqm')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Price per Month -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Price per academic year ($)</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">$</span>
                            </div>
                            <input type="number" name="room_cost" value="{{ old('room_cost') }}" step="0.01" min="0"
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
                            <option value="street" {{ old('window_type') == 'street' ? 'selected' : '' }}>Street View</option>
                            <option value="roadside" {{ old('window_type') == 'roadside' ? 'selected' : '' }}>Road roadside</option>
                            <option value="courtyard" {{ old('window_type') == 'courtyard' ? 'selected' : '' }}>Courtyard</option>
                            <option value="garden" {{ old('window_type') == 'garden' ? 'selected' : '' }}>Garden</option>
                            <option value="none" {{ old('window_type') == 'none' ? 'selected' : '' }}>No Window</option>
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
                            <option value="any" {{ old('gender', 'any') == 'any' ? 'selected' : '' }}>Any Gender</option>
                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male Only</option>
                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female Only</option>
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
                            <option value="available" {{ old('status', 'available') == 'available' ? 'selected' : '' }}>Available</option>
                            <option value="full" {{ old('status') == 'full' ? 'selected' : '' }}>Full</option>
                            <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Under Maintenance</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
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
                                <input type="checkbox" name="furnished" value="1" {{ old('furnished') ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Furnished</span>
                            </label>
                            <span class="text-xs text-gray-500">(Bed, desk, chair, wardrobe)</span>
                        </div>

                        <div class="flex items-center space-x-6">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="private_bathroom" value="1" {{ old('private_bathroom') ? 'checked' : '' }}
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

<!-- Hidden inputs for temp paths -->
                    <input type="hidden" name="temp_cover_path" id="temp_cover_path" value="">
                    <div id="temp_gallery_paths_container"></div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Room Video Upload -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Room Video (optional)
                            </label>
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 hover:border-blue-500 transition-colors">
                                <div class="text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M30 8l10 6v20l-10 6z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <rect x="8" y="8" width="18" height="32" rx="4" stroke-width="2" stroke-linejoin="round"/>
                                    </svg>
                                    <div class="mt-4">
                                        <label for="room_video" class="cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500">
                                            <span>Click to upload room video</span>
                                            <input id="room_video" name="room_video" type="file" class="sr-only" accept="video/*" onchange="previewRoomVideo(this)">
                                        </label>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-2">MP4/WebM up to 50MB</p>
                                </div>
                                <div id="room-video-preview" class="mt-4 hidden">
                                    <video controls class="w-full rounded-lg border" src=""></video>
                                    <button type="button" onclick="removeRoomVideo()" class="mt-2 text-xs text-red-600 hover:text-red-800">Remove Video</button>
                                </div>
                            </div>
                        </div>

                        <!-- Cover Image Upload with Progress Bar -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <span class="text-red-500">*</span> Cover Image
                            </label>
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 hover:border-blue-500 transition-colors" id="cover-upload-zone">
                                <div class="text-center" id="cover-upload-prompt">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H8a4 4 0 01-4-4V12a4 4 0 014-4h32a4 4 0 014 4v16.5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="mt-4">
                                        <label for="cover_image" class="cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500">
                                            <span>Click to upload cover image</span>
                                            <input id="cover_image" name="cover_image" type="file" class="sr-only" accept="image/*" required>
                                        </label>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-2">PNG, JPG, GIF up to 10MB</p>
                                    <p class="text-xs text-gray-400 mt-1">This will be the main image displayed for the room</p>
                                </div>

                                <!-- Progress Bar (hidden initially) -->
                                <div id="cover-progress-container" class="mt-4 hidden">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-xs font-medium text-blue-700" id="cover-progress-text">Uploading...</span>
                                        <span class="text-xs font-medium text-blue-700" id="cover-progress-percent">0%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                                        <div class="bg-blue-600 h-2.5 rounded-full transition-all duration-300 ease-out" id="cover-progress-bar" style="width: 0%"></div>
                                    </div>
                                </div>

                                <!-- Cover Preview (hidden initially) -->
                                <div id="cover-preview" class="mt-4 hidden">
                                    <div class="relative inline-block">
                                        <img src="" class="max-h-40 mx-auto rounded-lg shadow-md" alt="Cover preview">
                                        <button type="button" onclick="removeCoverImage()" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm hover:bg-red-600">
                                            ×
                                        </button>
                                        <div class="absolute bottom-1 left-1 bg-green-500 text-white text-xs px-2 py-0.5 rounded-full flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                            Ready
                                        </div>
                                    </div>
                                </div>

                                <!-- Error Message -->
                                <div id="cover-upload-error" class="mt-2 text-sm text-red-600 hidden"></div>
                            </div>
                            @error('cover_image')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Gallery Images Upload with Progress -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Gallery Images (Optional) — <span class="text-gray-500 font-normal">Upload individually, each with progress</span>
                            </label>
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 hover:border-blue-500 transition-colors" id="gallery-upload-zone">
                                <div class="text-center" id="gallery-upload-prompt">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H8a4 4 0 01-4-4V12a4 4 0 014-4h32a4 4 0 014 4v16.5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M30 28l-6-6-6 6M20 16h.01" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="mt-4">
                                        <label for="gallery_images" class="cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500">
                                            <span>Click to upload gallery images</span>
                                            <input id="gallery_images" name="gallery_images[]" type="file" class="sr-only" accept="image/*" multiple>
                                        </label>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-2">PNG, JPG, GIF up to 10MB each (max 5 images)</p>
                                    <p class="text-xs text-gray-400 mt-1">Additional photos showing different angles of the room</p>
                                </div>

                                <!-- Gallery Items Container -->
                                <div id="gallery-items" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 mt-4"></div>
                            </div>
                            @error('gallery_images.*')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="border-t border-gray-200 pt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Room Description</label>
                    <textarea name="description" rows="4"
                              class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror"
                              placeholder="Describe the room, its features, and any special notes...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Maximum 1000 characters.</p>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="mt-8 flex items-center justify-end space-x-3 border-t border-gray-200 pt-6">
                <a href="{{ route('admin.rooms.index') }}"
                   class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Cancel
                </a>
                <button type="submit"
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Create Room
                </button>
            </div>
        </form>
    </div>

    <!-- Help Card -->
    <div class="mt-6 bg-blue-50 rounded-lg p-4 border border-blue-200">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3 flex-1">
                <h3 class="text-sm font-medium text-blue-800">Room Creation Tips</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <ul class="list-disc pl-5 space-y-1">
                        <li>Room numbers must be unique within each hostel</li>
                        <li>Capacity determines maximum number of occupants</li>
                        <li>Set price per month to enable booking calculations</li>
                        <li>Status "Available" means room can be booked</li>
                        <li>Cover image is required and will be displayed as the main room photo</li>
                        <li>Upload multiple gallery images to showcase the room from different angles</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // ========== CSRF Token Helper ==========
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const UPLOAD_URL = '{{ route("admin.rooms.upload-temp") }}';
    const DELETE_URL_TEMPLATE = '{{ route("admin.rooms.delete-temp", "TEMP_ID_PLACEHOLDER") }}';

    // ========== Upload State ==========
    let coverUploaded = { temp_id: null, path: null };
    let galleryUploaded = []; // Array of { temp_id, path, filename }

    // ========== COVER IMAGE: Upload with Progress Bar ==========
    document.getElementById('cover_image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;

        // Reset any previous error
        const errorEl = document.getElementById('cover-upload-error');
        errorEl.classList.add('hidden');
        errorEl.textContent = '';

        // Show progress bar
        const progressContainer = document.getElementById('cover-progress-container');
        const progressBar = document.getElementById('cover-progress-bar');
        const progressPercent = document.getElementById('cover-progress-percent');
        const progressText = document.getElementById('cover-progress-text');
        progressContainer.classList.remove('hidden');
        progressBar.style.width = '0%';
        progressPercent.textContent = '0%';
        progressText.textContent = 'Uploading cover image...';

        // Hide prompt
        document.getElementById('cover-upload-prompt').classList.add('hidden');

        // Upload via AJAX with XMLHttpRequest for progress tracking
        const formData = new FormData();
        formData.append('image', file);
        formData.append('type', 'cover');
        formData.append('_token', CSRF_TOKEN);

        const xhr = new XMLHttpRequest();
        xhr.open('POST', UPLOAD_URL);

        xhr.upload.addEventListener('progress', function(e) {
            if (e.lengthComputable) {
                const percent = Math.round((e.loaded / e.total) * 100);
                progressBar.style.width = percent + '%';
                progressPercent.textContent = percent + '%';
                if (percent === 100) {
                    progressText.textContent = 'Processing image...';
                }
            }
        });

        xhr.addEventListener('load', function() {
            if (xhr.status >= 200 && xhr.status < 300) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        coverUploaded = { temp_id: response.temp_id, path: response.path };

                        // Set hidden input
                        document.getElementById('temp_cover_path').value = response.path;

                        // Show preview
                        const preview = document.getElementById('cover-preview');
                        const img = preview.querySelector('img');
                        img.src = response.url;
                        preview.classList.remove('hidden');

                        // Update progress to complete
                        progressBar.style.width = '100%';
                        progressPercent.textContent = '100%';
                        progressText.textContent = 'Upload complete!';
                        progressBar.classList.remove('bg-blue-600');
                        progressBar.classList.add('bg-green-500');

                        // Hide progress after 1.5s
                        setTimeout(() => {
                            progressContainer.classList.add('hidden');
                        }, 1500);
                    } else {
                        showUploadError('cover', response.message || 'Upload failed');
                    }
                } catch (err) {
                    showUploadError('cover', 'Invalid server response');
                }
            } else {
                try {
                    const errResp = JSON.parse(xhr.responseText);
                    showUploadError('cover', errResp.message || errResp.error || 'Server error');
                } catch (e) {
                    showUploadError('cover', 'Upload failed with status ' + xhr.status);
                }
            }
        });

        xhr.addEventListener('error', function() {
            showUploadError('cover', 'Network error. Please try again.');
        });

        xhr.send(formData);
    });

    function showUploadError(type, message) {
        if (type === 'cover') {
            const errorEl = document.getElementById('cover-upload-error');
            errorEl.textContent = '❌ ' + message;
            errorEl.classList.remove('hidden');

            // Reset
            document.getElementById('cover-progress-container').classList.add('hidden');
            document.getElementById('cover-upload-prompt').classList.remove('hidden');
            document.getElementById('cover_image').value = '';
        }
    }

    function removeCoverImage() {
        // If we have a temp upload, delete it from server
        if (coverUploaded.temp_id) {
            fetch(DELETE_URL_TEMPLATE.replace('TEMP_ID_PLACEHOLDER', coverUploaded.temp_id), {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Content-Type': 'application/json'
                }
            }).catch(err => console.warn('Failed to delete temp image:', err));
        }

        // Reset state
        coverUploaded = { temp_id: null, path: null };
        document.getElementById('temp_cover_path').value = '';
        document.getElementById('cover_image').value = '';

        // Reset UI
        const preview = document.getElementById('cover-preview');
        preview.classList.add('hidden');
        preview.querySelector('img').src = '';

        const progressContainer = document.getElementById('cover-progress-container');
        progressContainer.classList.add('hidden');
        document.getElementById('cover-progress-bar').style.width = '0%';
        document.getElementById('cover-progress-bar').className = 'bg-blue-600 h-2.5 rounded-full transition-all duration-300 ease-out';

        const errorEl = document.getElementById('cover-upload-error');
        errorEl.classList.add('hidden');
        errorEl.textContent = '';

        document.getElementById('cover-upload-prompt').classList.remove('hidden');
    }

    // ========== GALLERY IMAGES: Upload each with Progress ==========
    document.getElementById('gallery_images').addEventListener('change', function(e) {
        const files = Array.from(e.target.files);
        if (!files.length) return;

        // Check max limit
        const availableSlots = 5 - galleryUploaded.length;
        if (availableSlots <= 0) {
            alert('Maximum 5 gallery images allowed. Please remove existing ones first.');
            this.value = '';
            return;
        }

        const filesToUpload = files.slice(0, availableSlots);

        filesToUpload.forEach(file => {
            uploadGalleryImage(file);
        });

        // Reset input so the same file can be re-selected
        this.value = '';
    });

    function uploadGalleryImage(file) {
        const galleryContainer = document.getElementById('gallery-items');
        const container = document.createElement('div');
        container.className = 'relative border border-gray-200 rounded-lg p-2 bg-white gallery-item';
        container.dataset.uploading = 'true';

        // Initial HTML with progress bar
        container.innerHTML = `
            <div class="flex flex-col items-center justify-center h-24">
                <svg class="animate-spin h-6 w-6 text-blue-500 mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-xs text-gray-500 mb-1 truncate max-w-full px-2" title="${file.name}">${file.name}</span>
                <div class="w-full bg-gray-200 rounded-full h-1.5 mx-2">
                    <div class="gallery-progress-bar bg-blue-600 h-1.5 rounded-full transition-all duration-300 ease-out" style="width: 0%"></div>
                </div>
                <span class="text-xs text-gray-400 mt-1 gallery-percent">0%</span>
            </div>
        `;

        galleryContainer.appendChild(container);

        // Check if we have enough slots
        if (galleryUploaded.length >= 5) {
            container.querySelector('.flex').innerHTML = '<span class="text-xs text-red-500">Max 5 images reached</span>';
            return;
        }

        const formData = new FormData();
        formData.append('image', file);
        formData.append('type', 'gallery');
        formData.append('_token', CSRF_TOKEN);

        const xhr = new XMLHttpRequest();
        xhr.open('POST', UPLOAD_URL);

        xhr.upload.addEventListener('progress', function(e) {
            if (e.lengthComputable) {
                const percent = Math.round((e.loaded / e.total) * 100);
                const bar = container.querySelector('.gallery-progress-bar');
                const pct = container.querySelector('.gallery-percent');
                if (bar) bar.style.width = percent + '%';
                if (pct) pct.textContent = percent + '%';
            }
        });

        xhr.addEventListener('load', function() {
            if (xhr.status >= 200 && xhr.status < 300) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        galleryUploaded.push({ temp_id: response.temp_id, path: response.path, filename: response.name });

                        // Update hidden inputs
                        updateGalleryHiddenInputs();

                        // Replace progress with preview
                        container.dataset.uploading = 'false';
                        container.dataset.tempId = response.temp_id;
                        container.dataset.path = response.path;

                        container.innerHTML = `
                            <div class="relative group">
                                <img src="${response.url}" 
                                     class="w-full h-24 object-cover rounded-lg shadow-sm border border-gray-200"
                                     alt="${response.name}">
                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition-all rounded-lg"></div>
                                <button type="button" 
                                        onclick="removeGalleryImage('${response.temp_id}')" 
                                        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs hover:bg-red-600 opacity-0 group-hover:opacity-100 transition-opacity z-10">
                                    ×
                                </button>
                                <div class="absolute bottom-1 left-1 bg-green-500 text-white text-xs px-1.5 py-0.5 rounded-full flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    Ready
                                </div>
                            </div>
                        `;
                    } else {
                        container.innerHTML = '<div class="flex items-center justify-center h-24"><span class="text-xs text-red-500">Upload failed</span></div>';
                    }
                } catch (err) {
                    container.innerHTML = '<div class="flex items-center justify-center h-24"><span class="text-xs text-red-500">Invalid response</span></div>';
                }
            } else {
                container.innerHTML = '<div class="flex items-center justify-center h-24"><span class="text-xs text-red-500">Upload error</span></div>';
            }
        });

        xhr.addEventListener('error', function() {
            container.innerHTML = '<div class="flex items-center justify-center h-24"><span class="text-xs text-red-500">Network error</span></div>';
        });

        xhr.send(formData);
    }

    function updateGalleryHiddenInputs() {
        const container = document.getElementById('temp_gallery_paths_container');
        if (!container) return;
        // Clear existing hidden inputs
        container.innerHTML = '';

        galleryUploaded.forEach((item) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'temp_gallery_paths[]';
            input.value = item.path;
            container.appendChild(input);
        });
    }

    function removeGalleryImage(tempId) {
        // Find and remove from array
        const index = galleryUploaded.findIndex(item => item.temp_id === tempId);
        if (index === -1) return;

        const item = galleryUploaded[index];

        // Delete from server
        fetch(DELETE_URL_TEMPLATE.replace('TEMP_ID_PLACEHOLDER', tempId), {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Content-Type': 'application/json'
            }
        }).catch(err => console.warn('Failed to delete temp image:', err));

        // Remove from array
        galleryUploaded.splice(index, 1);

        // Update hidden inputs
        updateGalleryHiddenInputs();

        // Remove from UI
        const items = document.querySelectorAll('.gallery-item');
        items.forEach(el => {
            if (el.dataset.tempId === tempId) {
                el.remove();
            }
        });
    }

    // ========== ROOM VIDEO Preview ==========
    function previewRoomVideo(input) {
        const previewWrap = document.getElementById('room-video-preview');
        const video = previewWrap?.querySelector('video');

        if (input.files && input.files[0] && video) {
            const url = URL.createObjectURL(input.files[0]);
            video.src = url;
            previewWrap.classList.remove('hidden');
            
            const file = input.files[0];
            const infoDiv = document.createElement('div');
            infoDiv.className = 'mt-2 text-xs text-gray-500';
            infoDiv.innerHTML = `
                <p>File: ${file.name}</p>
                <p>Size: ${(file.size / 1024 / 1024).toFixed(2)} MB</p>
                <p>Type: ${file.type}</p>
            `;
            
            const existingInfo = previewWrap.querySelector('.video-info');
            if (existingInfo) existingInfo.remove();
            infoDiv.className = 'video-info mt-2 text-xs text-gray-500';
            previewWrap.appendChild(infoDiv);
        } else if (previewWrap) {
            previewWrap.classList.add('hidden');
            const video = previewWrap?.querySelector('video');
            if (video) video.src = '';
        }
    }

    function removeRoomVideo() {
        const input = document.getElementById('room_video');
        const previewWrap = document.getElementById('room-video-preview');
        const video = previewWrap?.querySelector('video');
        
        input.value = '';
        if (video) video.src = '';
        previewWrap.classList.add('hidden');
        
        const info = previewWrap?.querySelector('.video-info');
        if (info) info.remove();
    }

    // ========== Form Validation Before Submit ==========
    document.getElementById('roomForm').addEventListener('submit', function(e) {
        // Check if cover image is uploaded (either via AJAX or direct file)
        const coverTempPath = document.getElementById('temp_cover_path').value;
        const coverFile = document.getElementById('cover_image').files[0];
        
        if (!coverTempPath && !coverFile) {
            e.preventDefault();
            alert('Please upload a cover image for the room before submitting.');
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
    
    /* Gallery image hover effect */
    #gallery-preview > div {
        transition: transform 0.2s ease;
    }
    #gallery-preview > div:hover {
        transform: scale(1.05);
        z-index: 10;
    }
    
    /* Video preview styling */
    #room-video-preview video {
        max-height: 200px;
        background: #000;
    }
</style>
@endpush
@endsection