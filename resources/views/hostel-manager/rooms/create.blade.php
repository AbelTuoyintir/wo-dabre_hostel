@extends('layouts.hostelmanage')

@section('title', 'Add New Room')
@section('page-title', 'Add New Room')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-4 bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex items-center">
            <a href="{{ route('hostel-manager.rooms') }}" class="text-gray-500 hover:text-gray-700 mr-3">
                <i class="fas fa-arrow-left text-xs"></i>
            </a>
            <div>
                <h2 class="text-sm font-semibold text-gray-800">Add New Room</h2>
                <p class="text-xs text-gray-500">Create a new room in your hostel</p>
            </div>
        </div>
    </div>

    <!-- Help Card -->
    @if($hostels && $hostels->count() > 0)
    <div class="mt-4 bg-blue-50 rounded-lg p-3 border border-blue-100">
        <div class="flex items-start">
            <i class="fas fa-lightbulb text-blue-500 text-xs mt-0.5 mr-2"></i>
            <div>
                <h4 class="text-xs font-medium text-blue-800">Quick Tips</h4>
                <ul class="mt-1 text-[10px] text-blue-700 list-disc list-inside">
                    <li>Room numbers must be unique within each hostel</li>
                    <li>Capacity determines maximum number of occupants</li>
                    <li>Price per month is used for all booking calculations</li>
                    <li>You can change room status later from the rooms list</li>
                </ul>
            </div>
        </div>
    </div>
    @endif

    <!-- Create Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <form action="{{ route('hostel-manager.rooms.store') }}" method="POST" class="p-6" enctype="multipart/form-data">
            @csrf
            
            <div class="space-y-6">
                <!-- Hostel Selection (if managing multiple) -->
                @if($hostels && $hostels->count() > 0)
                    @if($hostels->count() > 1)
                    <div>
                        <h3 class="text-xs font-semibold text-gray-700 uppercase tracking-wider mb-3 flex items-center">
                            <i class="fas fa-building text-blue-500 mr-1.5 text-xs"></i>
                            Hostel Selection
                        </h3>
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="block text-[10px] font-medium text-gray-500 uppercase mb-1">
                                    Select Hostel <span class="text-red-500">*</span>
                                </label>
                                <select name="hostel_id" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @error('hostel_id') border-red-500 @enderror" required>
                                    <option value="">Choose a hostel</option>
                                    @foreach($hostels as $hostel)
                                        <option value="{{ $hostel->id }}" {{ old('hostel_id') == $hostel->id ? 'selected' : '' }}>
                                            {{ $hostel->name }} ({{ $hostel->location }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('hostel_id')
                                    <p class="mt-1 text-[10px] text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    @else
                        <!-- Single hostel - hidden input -->
                        <input type="hidden" name="hostel_id" value="{{ $hostels->first()->id }}">
                    @endif
                @else
                    <div class="bg-yellow-50 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded-lg mb-4">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle text-yellow-600 mr-3"></i>
                            <p class="text-xs">You don't have any hostels assigned. Please contact the administrator.</p>
                        </div>
                    </div>
                @endif

                <!-- Basic Information -->
                <div class="pt-2">
                    <h3 class="text-xs font-semibold text-gray-700 uppercase tracking-wider mb-3 flex items-center">
                        <i class="fas fa-info-circle text-blue-500 mr-1.5 text-xs"></i>
                        Basic Information
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Room Number -->
                        <div>
                            <label class="block text-[10px] font-medium text-gray-500 uppercase mb-1">
                                Room Number <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="number" value="{{ old('number') }}" 
                                   class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @error('number') border-red-500 @enderror"
                                   placeholder="e.g., 101, A202" required>
                            @error('number')
                                <p class="mt-1 text-[10px] text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Floor -->
                        <div>
                            <label class="block text-[10px] font-medium text-gray-500 uppercase mb-1">
                                Floor
                            </label>
                            <input type="number" name="floor" value="{{ old('floor') }}" 
                                   class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @error('floor') border-red-500 @enderror"
                                   placeholder="e.g., 1, 2, 3" min="0">
                            @error('floor')
                                <p class="mt-1 text-[10px] text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Room Type -->
                        <div>
                            <label class="block text-[10px] font-medium text-gray-500 uppercase mb-1">
                                Room Type <span class="text-red-500">*</span>
                            </label>
                            <select name="room_type" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @error('room_type') border-red-500 @enderror" required>
                                <option value="">Select Room Type</option>
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

                                <optgroup label="Quad Rooms (4 People)">
                                    <option value="quad_self_contained" {{ old('room_type') == 'quad_self_contained' ? 'selected' : '' }}>Four in a Room - Self Contained</option>
                                    <option value="quad_shared_bathroom" {{ old('room_type') == 'quad_shared_bathroom' ? 'selected' : '' }}>Four in a Room - Shared Bathroom</option>
                                    <option value="quad_shared_kitchen" {{ old('room_type') == 'quad_shared_kitchen' ? 'selected' : '' }}>Four in a Room - Shared Kitchen</option>
                                    <option value="quad_shared_kitchen_bathroom" {{ old('room_type') == 'quad_shared_kitchen_bathroom' ? 'selected' : '' }}>Four in a Room - Shared Kitchen &amp; Bathroom</option>
                                </optgroup>

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

                                <optgroup label="Studio / Apartments">
                                    <option value="studio_self_contained" {{ old('room_type') == 'studio_self_contained' ? 'selected' : '' }}>Studio Apartment - Self Contained</option>
                                    <option value="studio_kitchenette" {{ old('room_type') == 'studio_kitchenette' ? 'selected' : '' }}>Studio Apartment - Kitchenette</option>
                                    <option value="studio_private_bathroom" {{ old('room_type') == 'studio_private_bathroom' ? 'selected' : '' }}>Studio Apartment - Private Bathroom</option>
                                    <option value="studio_furnished" {{ old('room_type') == 'studio_furnished' ? 'selected' : '' }}>Studio Apartment - Furnished</option>
                                    <option value="one_bedroom_self_contained" {{ old('room_type') == 'one_bedroom_self_contained' ? 'selected' : '' }}>One-Bedroom Apartment - Self Contained</option>
                                    <option value="one_bedroom_kitchenette" {{ old('room_type') == 'one_bedroom_kitchenette' ? 'selected' : '' }}>One-Bedroom Apartment - Kitchenette</option>
                                    <option value="two_bedroom_self_contained" {{ old('room_type') == 'two_bedroom_self_contained' ? 'selected' : '' }}>Two-Bedroom Apartment - Self Contained</option>
                                </optgroup>

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

                                <optgroup label="Premium &amp; Special Rooms">
                                    <option value="executive_suite" {{ old('room_type') == 'executive_suite' ? 'selected' : '' }}>Executive Suite - Self Contained</option>
                                    <option value="presidential_suite" {{ old('room_type') == 'presidential_suite' ? 'selected' : '' }}>Presidential Suite - Self Contained</option>
                                    <option value="honeymoon_suite" {{ old('room_type') == 'honeymoon_suite' ? 'selected' : '' }}>Honeymoon Suite - Self Contained</option>
                                    <option value="family_room_self" {{ old('room_type') == 'family_room_self' ? 'selected' : '' }}>Family Room - Self Contained</option>
                                    <option value="family_room_shared" {{ old('room_type') == 'family_room_shared' ? 'selected' : '' }}>Family Room - Shared Bathroom</option>
                                    <option value="vip_room" {{ old('room_type') == 'vip_room' ? 'selected' : '' }}>VIP Room - Self Contained</option>
                                    <option value="business_room" {{ old('room_type') == 'business_room' ? 'selected' : '' }}>Business Room - Self Contained</option>
                                </optgroup>

                                <optgroup label="Accessible Rooms">
                                    <option value="wheelchair_self" {{ old('room_type') == 'wheelchair_self' ? 'selected' : '' }}>Wheelchair Accessible - Self Contained</option>
                                    <option value="wheelchair_shared" {{ old('room_type') == 'wheelchair_shared' ? 'selected' : '' }}>Wheelchair Accessible - Shared Bathroom</option>
                                    <option value="ground_floor_self" {{ old('room_type') == 'ground_floor_self' ? 'selected' : '' }}>Ground Floor Room - Self Contained</option>
                                    <option value="ground_floor_shared" {{ old('room_type') == 'ground_floor_shared' ? 'selected' : '' }}>Ground Floor Room - Shared Bathroom</option>
                                </optgroup>

                                <optgroup label="Budget Rooms">
                                    <option value="budget_single" {{ old('room_type') == 'budget_single' ? 'selected' : '' }}>Budget Single - Shared Bathroom</option>
                                    <option value="budget_single_kitchen" {{ old('room_type') == 'budget_single_kitchen' ? 'selected' : '' }}>Budget Single - Shared Kitchen &amp; Bathroom</option>
                                    <option value="budget_double" {{ old('room_type') == 'budget_double' ? 'selected' : '' }}>Budget Double - Shared Bathroom</option>
                                    <option value="budget_dorm" {{ old('room_type') == 'budget_dorm' ? 'selected' : '' }}>Budget Dormitory - Shared Bathroom</option>
                                </optgroup>

                                <optgroup label="Gender-Specific Rooms">
                                    <option value="female_only_self" {{ old('room_type') == 'female_only_self' ? 'selected' : '' }}>Female Only - Self Contained</option>
                                    <option value="female_only_shared" {{ old('room_type') == 'female_only_shared' ? 'selected' : '' }}>Female Only - Shared Bathroom</option>
                                    <option value="male_only_self" {{ old('room_type') == 'male_only_self' ? 'selected' : '' }}>Male Only - Self Contained</option>
                                    <option value="male_only_shared" {{ old('room_type') == 'male_only_shared' ? 'selected' : '' }}>Male Only - Shared Bathroom</option>
                                </optgroup>
                            </select>
                            @error('room_type')
                                <p class="mt-1 text-[10px] text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Room Specifications -->
                <div class="pt-4 border-t border-gray-100">
                    <h3 class="text-xs font-semibold text-gray-700 uppercase tracking-wider mb-3 flex items-center">
                        <i class="fas fa-ruler text-green-500 mr-1.5 text-xs"></i>
                        Room Specifications
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Capacity -->
                        <div>
                            <label class="block text-[10px] font-medium text-gray-500 uppercase mb-1">
                                Capacity <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="capacity" value="{{ old('capacity', 1) }}" min="1"
                                   class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @error('capacity') border-red-500 @enderror"
                                   placeholder="Max persons" required>
                            @error('capacity')
                                <p class="mt-1 text-[10px] text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Price per Month -->
                        <div>
                            <label class="block text-[10px] font-medium text-gray-500 uppercase mb-1">
                                Room Cost (₵) <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-xs">₵</span>
                                <input type="number" name="room_cost" value="{{ old('room_cost') }}" step="0.01" min="0"
                                       class="w-full pl-8 pr-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @error('price_per_month') border-red-500 @enderror"
                                       placeholder="0.00" required>
                            </div>
                            @error('price_per_month')
                                <p class="mt-1 text-[10px] text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Size -->
                        <div>
                            <label class="block text-[10px] font-medium text-gray-500 uppercase mb-1">
                                Size (sqm)
                            </label>
                            <input type="number" name="size_sqm" value="{{ old('size_sqm') }}" step="0.01" min="0"
                                   class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @error('size_sqm') border-red-500 @enderror"
                                   placeholder="e.g., 25.5">
                            @error('size_sqm')
                                <p class="mt-1 text-[10px] text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Room Features -->
                <div class="pt-4 border-t border-gray-100">
                    <h3 class="text-xs font-semibold text-gray-700 uppercase tracking-wider mb-3 flex items-center">
                        <i class="fas fa-couch text-purple-500 mr-1.5 text-xs"></i>
                        Room Features
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Gender Preference -->
                        <div>
                            <label class="block text-[10px] font-medium text-gray-500 uppercase mb-1">
                                Gender Preference <span class="text-red-500">*</span>
                            </label>
                            <select name="gender" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @error('gender') border-red-500 @enderror" required>
                                <option value="any" {{ old('gender') == 'any' ? 'selected' : '' }}>Any Gender</option>
                                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male Only</option>
                                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female Only</option>
                            </select>
                            @error('gender')
                                <p class="mt-1 text-[10px] text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Window Type -->
                        <div>
                            <label class="block text-[10px] font-medium text-gray-500 uppercase mb-1">
                                Window View
                            </label>
                            <select name="window_type" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @error('window_type') border-red-500 @enderror">
                                <option value="">Select View</option>
                                <option value="street" {{ old('window_type') == 'street' ? 'selected' : '' }}>Street View</option>
                                <option value="courtyard" {{ old('window_type') == 'courtyard' ? 'selected' : '' }}>Courtyard</option>
                                <option value="garden" {{ old('window_type') == 'garden' ? 'selected' : '' }}>Garden</option>
                                <option value="none" {{ old('window_type') == 'none' ? 'selected' : '' }}>No Window</option>
                            </select>
                            @error('window_type')
                                <p class="mt-1 text-[10px] text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Checkbox Features -->
                    <div class="flex flex-wrap gap-4 mt-4">
                        <label class="flex items-center">
                            <input type="checkbox" name="furnished" value="1" {{ old('furnished') ? 'checked' : '' }}
                                   class="w-3.5 h-3.5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span class="ml-2 text-xs text-gray-700">Furnished</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="private_bathroom" value="1" {{ old('private_bathroom') ? 'checked' : '' }}
                                   class="w-3.5 h-3.5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span class="ml-2 text-xs text-gray-700">Private Bathroom</span>
                        </label>
                    </div>
                </div>

                <!-- Status -->
                <div class="pt-4 border-t border-gray-100">
                    <h3 class="text-xs font-semibold text-gray-700 uppercase tracking-wider mb-3 flex items-center">
                        <i class="fas fa-toggle-on text-yellow-500 mr-1.5 text-xs"></i>
                        Room Status
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Status -->
                        <div>
                            <label class="block text-[10px] font-medium text-gray-500 uppercase mb-1">
                                Initial Status <span class="text-red-500">*</span>
                            </label>
                            <select name="status" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @error('status') border-red-500 @enderror" required>
                                <option value="available" {{ old('status', 'available') == 'available' ? 'selected' : '' }}>Available</option>
                                <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-[10px] text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-[10px] text-gray-400">
                                <i class="fas fa-info-circle mr-1"></i> "Occupied" status is set automatically when booked
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Image Upload Section -->
                <div class="pt-4 border-t border-gray-100">
                    <h3 class="text-xs font-semibold text-gray-700 uppercase tracking-wider mb-3 flex items-center">
                        <i class="fas fa-images text-pink-500 mr-1.5 text-xs"></i>
                        Room Images
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Room Video Upload -->
                        <div>
                            <label class="block text-[10px] font-medium text-gray-500 uppercase mb-1">
                                Room Video (optional)
                            </label>
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 hover:border-blue-500 transition-colors">
                                <div class="text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M30 8l10 6v20l-10 6z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <rect x="8" y="8" width="18" height="32" rx="4" stroke-width="2" stroke-linejoin="round"/>
                                    </svg>
                                    <div class="mt-4">
                                        <label for="room_video" class="cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 text-xs">
                                            <span>Click to upload room video</span>
                                            <input id="room_video" name="room_video" type="file" class="sr-only" accept="video/*" onchange="previewRoomVideo(this)">
                                        </label>
                                    </div>
                                    <p class="text-[10px] text-gray-500 mt-2">MP4/WebM up to 50MB</p>
                                </div>
                                <div id="room-video-preview" class="mt-4 hidden">
                                    <video controls class="w-full rounded-lg border" src=""></video>
                                    <button type="button" onclick="removeRoomVideo()" class="mt-2 text-[10px] text-red-600 hover:text-red-800">Remove Video</button>
                                </div>
                            </div>
                        </div>

                        <!-- Cover Image Upload with Progress Bar -->
                        <div>
                            <label class="block text-[10px] font-medium text-gray-500 uppercase mb-1">
                                <span class="text-red-500">*</span> Cover Image
                            </label>
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 hover:border-blue-500 transition-colors" id="cover-upload-zone">
                                <div class="text-center" id="cover-upload-prompt">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H8a4 4 0 01-4-4V12a4 4 0 014-4h32a4 4 0 014 4v16.5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="mt-4">
                                        <label for="cover_image" class="cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 text-xs">
                                            <span>Click to upload cover image</span>
                                            <input id="cover_image" name="cover_image" type="file" class="sr-only" accept="image/*" required>
                                        </label>
                                    </div>
                                    <p class="text-[10px] text-gray-500 mt-2">PNG, JPG, GIF up to 10MB</p>
                                    <p class="text-[10px] text-gray-400 mt-1">This will be the main image displayed for the room</p>
                                </div>

                                <!-- Progress Bar (hidden initially) -->
                                <div id="cover-progress-container" class="mt-4 hidden">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-[10px] font-medium text-blue-700" id="cover-progress-text">Uploading...</span>
                                        <span class="text-[10px] font-medium text-blue-700" id="cover-progress-percent">0%</span>
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
                                        <div class="absolute bottom-1 left-1 bg-green-500 text-white text-[10px] px-2 py-0.5 rounded-full flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                            Ready
                                        </div>
                                    </div>
                                </div>

                                <!-- Error Message -->
                                <div id="cover-upload-error" class="mt-2 text-[10px] text-red-600 hidden"></div>
                            </div>
                            @error('cover_image')
                                <p class="mt-1 text-[10px] text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Gallery Images Upload with Progress -->
                        <div class="md:col-span-2">
                            <label class="block text-[10px] font-medium text-gray-500 uppercase mb-1">
                                Gallery Images (Optional) — <span class="text-gray-500 font-normal">Upload individually, each with progress</span>
                            </label>
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 hover:border-blue-500 transition-colors" id="gallery-upload-zone">
                                <div class="text-center" id="gallery-upload-prompt">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H8a4 4 0 01-4-4V12a4 4 0 014-4h32a4 4 0 014 4v16.5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M30 28l-6-6-6 6M20 16h.01" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="mt-4">
                                        <label for="gallery_images" class="cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 text-xs">
                                            <span>Click to upload gallery images</span>
                                            <input id="gallery_images" name="gallery_images[]" type="file" class="sr-only" accept="image/*" multiple>
                                        </label>
                                    </div>
                                    <p class="text-[10px] text-gray-500 mt-2">PNG, JPG, GIF up to 10MB each (max 5 images)</p>
                                    <p class="text-[10px] text-gray-400 mt-1">Additional photos showing different angles of the room</p>
                                </div>

                                <!-- Gallery Items Container -->
                                <div id="gallery-items" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 mt-4"></div>
                            </div>
                            @error('gallery_images.*')
                                <p class="mt-1 text-[10px] text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="pt-4 border-t border-gray-100">
                    <h3 class="text-xs font-semibold text-gray-700 uppercase tracking-wider mb-3 flex items-center">
                        <i class="fas fa-align-left text-gray-500 mr-1.5 text-xs"></i>
                        Description
                    </h3>
                    <div>
                        <textarea name="description" rows="4" 
                                  class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror"
                                  placeholder="Enter room description, special features, notes...">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-[10px] text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-[10px] text-gray-400">Maximum 1000 characters</p>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="pt-4 border-t border-gray-100 flex items-center justify-end space-x-2">
                    <a href="{{ route('hostel-manager.rooms') }}" 
                       class="px-4 py-2 border border-gray-300 rounded-lg text-xs font-medium text-gray-700 hover:bg-gray-50 transition">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-xs font-medium rounded-lg transition flex items-center">
                        <i class="fas fa-plus-circle mr-1.5"></i>
                        Create Room
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Warn if leaving with unsaved changes
let formChanged = false;
document.querySelectorAll('input, select, textarea').forEach(element => {
    element.addEventListener('change', () => formChanged = true);
    element.addEventListener('keyup', () => formChanged = true);
});

window.addEventListener('beforeunload', function(e) {
    if (formChanged) {
        e.preventDefault();
        e.returnValue = '';
    }
});

// Preview room number format
document.querySelector('input[name="number"]')?.addEventListener('input', function(e) {
    // Optional: Add any room number formatting logic here
});

// Auto-calculate anything if needed
document.querySelector('input[name="capacity"]')?.addEventListener('change', function(e) {
    // Optional: Auto-update related fields
});

// ====== IMAGE UPLOAD FUNCTIONS ======

// Cover Image Upload with Progress Bar
document.getElementById('cover_image')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;
    
    // Validate file type
    if (!file.type.startsWith('image/')) {
        showCoverError('Please upload an image file');
        this.value = '';
        return;
    }
    
    // Validate file size (10MB max)
    if (file.size > 10 * 1024 * 1024) {
        showCoverError('File size exceeds 10MB limit');
        this.value = '';
        return;
    }
    
    // Show progress bar
    const progressContainer = document.getElementById('cover-progress-container');
    const progressBar = document.getElementById('cover-progress-bar');
    const progressPercent = document.getElementById('cover-progress-percent');
    const progressText = document.getElementById('cover-progress-text');
    
    progressContainer.classList.remove('hidden');
    document.getElementById('cover-upload-prompt').classList.add('hidden');
    document.getElementById('cover-preview').classList.add('hidden');
    hideCoverError();
    
    // Simulate upload progress
    let progress = 0;
    const interval = setInterval(() => {
        progress += Math.random() * 15 + 5;
        if (progress >= 100) {
            progress = 100;
            clearInterval(interval);
            progressText.textContent = 'Upload Complete!';
            setTimeout(() => {
                progressContainer.classList.add('hidden');
                showCoverPreview(file);
            }, 500);
        }
        progressBar.style.width = progress + '%';
        progressPercent.textContent = Math.round(progress) + '%';
    }, 150);
});

function showCoverPreview(file) {
    const reader = new FileReader();
    reader.onload = function(e) {
        const preview = document.getElementById('cover-preview');
        const img = preview.querySelector('img');
        img.src = e.target.result;
        preview.classList.remove('hidden');
        document.getElementById('cover-upload-prompt').classList.add('hidden');
        document.getElementById('cover-progress-container').classList.add('hidden');
    };
    reader.readAsDataURL(file);
}

function removeCoverImage() {
    document.getElementById('cover-preview').classList.add('hidden');
    document.getElementById('cover-upload-prompt').classList.remove('hidden');
    document.getElementById('cover_image').value = '';
    hideCoverError();
}

function showCoverError(message) {
    const errorEl = document.getElementById('cover-upload-error');
    errorEl.textContent = message;
    errorEl.classList.remove('hidden');
    document.getElementById('cover-upload-zone').classList.add('border-red-500');
}

function hideCoverError() {
    document.getElementById('cover-upload-error').classList.add('hidden');
    document.getElementById('cover-upload-zone').classList.remove('border-red-500');
}

// Gallery Images Upload with Individual Progress
document.getElementById('gallery_images')?.addEventListener('change', function(e) {
    const files = Array.from(e.target.files);
    const maxFiles = 5;
    
    // Check if more than 5 files
    if (files.length > maxFiles) {
        alert(`You can only upload up to ${maxFiles} images at a time.`);
        this.value = '';
        return;
    }
    
    // Validate each file
    const validFiles = files.filter(file => {
        if (!file.type.startsWith('image/')) {
            alert(`${file.name} is not an image file.`);
            return false;
        }
        if (file.size > 10 * 1024 * 1024) {
            alert(`${file.name} exceeds 10MB limit.`);
            return false;
        }
        return true;
    });
    
    if (validFiles.length === 0) {
        this.value = '';
        return;
    }
    
    // Process each file
    validFiles.forEach((file, index) => {
        // Create a unique ID for this gallery item
        const itemId = 'gallery-item-' + Date.now() + '-' + index;
        createGalleryItem(itemId, file);
    });
    
    // Clear the input to allow re-uploading
    this.value = '';
});

function createGalleryItem(itemId, file) {
    const container = document.getElementById('gallery-items');
    
    // Create gallery item container
    const item = document.createElement('div');
    item.id = itemId;
    item.className = 'relative bg-gray-50 rounded-lg border border-gray-200 p-2';
    
    // Progress container
    item.innerHTML = `
        <div class="relative">
            <div class="w-full h-24 bg-gray-100 rounded flex items-center justify-center" id="${itemId}-preview">
                <svg class="w-8 h-8 text-gray-400 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            <div class="mt-2">
                <div class="flex items-center justify-between mb-0.5">
                    <span class="text-[10px] font-medium text-gray-700 truncate flex-1" id="${itemId}-name">${file.name}</span>
                    <span class="text-[10px] font-medium text-blue-700" id="${itemId}-percent">0%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-1.5">
                    <div class="bg-blue-600 h-1.5 rounded-full transition-all duration-300 ease-out" id="${itemId}-bar" style="width: 0%"></div>
                </div>
            </div>
        </div>
    `;
    
    container.appendChild(item);
    
    // Simulate upload progress for this item
    let progress = 0;
    const interval = setInterval(() => {
        progress += Math.random() * 15 + 5;
        if (progress >= 100) {
            progress = 100;
            clearInterval(interval);
            // Show the uploaded image
            const reader = new FileReader();
            reader.onload = function(e) {
                const previewContainer = document.getElementById(`${itemId}-preview`);
                previewContainer.innerHTML = `
                    <img src="${e.target.result}" class="w-full h-24 object-cover rounded" alt="${file.name}">
                `;
                // Add a ready badge
                const badge = document.createElement('div');
                badge.className = 'absolute top-1 right-1 bg-green-500 text-white text-[8px] px-1.5 py-0.5 rounded-full flex items-center';
                badge.innerHTML = `
                    <svg class="w-2.5 h-2.5 mr-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    Ready
                `;
                const container = document.getElementById(itemId);
                container.querySelector('.relative').appendChild(badge);
                
                // Add remove button
                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'absolute top-1 left-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs hover:bg-red-600';
                removeBtn.textContent = '×';
                removeBtn.onclick = function() { removeGalleryItem(itemId); };
                container.querySelector('.relative').appendChild(removeBtn);
            };
            reader.readAsDataURL(file);
        }
        const bar = document.getElementById(`${itemId}-bar`);
        const percent = document.getElementById(`${itemId}-percent`);
        if (bar) bar.style.width = progress + '%';
        if (percent) percent.textContent = Math.round(progress) + '%';
    }, 150);
}

function removeGalleryItem(itemId) {
    const item = document.getElementById(itemId);
    if (item) {
        item.remove();
    }
}

// Room Video Preview
function previewRoomVideo(input) {
    const file = input.files[0];
    if (!file) return;
    
    // Validate file type
    if (!file.type.startsWith('video/')) {
        alert('Please upload a video file');
        input.value = '';
        return;
    }
    
    // Validate file size (50MB max)
    if (file.size > 50 * 1024 * 1024) {
        alert('File size exceeds 50MB limit');
        input.value = '';
        return;
    }
    
    const reader = new FileReader();
    reader.onload = function(e) {
        const preview = document.getElementById('room-video-preview');
        const video = preview.querySelector('video');
        video.src = e.target.result;
        preview.classList.remove('hidden');
    };
    reader.readAsDataURL(file);
}

function removeRoomVideo() {
    document.getElementById('room-video-preview').classList.add('hidden');
    document.getElementById('room_video').value = '';
    const video = document.querySelector('#room-video-preview video');
    if (video) video.src = '';
}

// Update form submission to include file data
document.querySelector('form')?.addEventListener('submit', function(e) {
    // The form already has enctype="multipart/form-data", so files will be included
    // Just ensure we don't have empty file inputs that cause issues
    const coverInput = document.getElementById('cover_image');
    if (coverInput && !coverInput.files.length) {
        // If no file selected, the server-side validation will catch it
        // since cover_image is required
    }
});
</script>
@endpush