@extends('layouts.app')

@section('title', 'Edit Hostel')
@section('page-title', 'Edit Hostel: ' . $hostel->name)

@section('content')
<div class="max-w-4xl mx-auto">
    <form action="{{ route('admin.hostels.update', $hostel) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')
        
        <!-- Basic Information Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    Basic Information
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Hostel Name -->
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <span class="text-red-500">*</span> Hostel Name
                        </label>
                        <input type="text" name="name" value="{{ old('name', $hostel->name) }}" 
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror" 
                               placeholder="e.g., Sunshine Hostel" required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Location -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <span class="text-red-500">*</span> Location
                        </label>
                        <input type="text" name="location" value="{{ old('location', $hostel->location) }}" 
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('location') border-red-500 @enderror" 
                               placeholder="e.g., Downtown, City Center" required>
                        @error('location')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Address -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <span class="text-red-500">*</span> Full Address
                        </label>
                        <input type="text" name="address" value="{{ old('address', $hostel->address) }}" 
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('address') border-red-500 @enderror" 
                               placeholder="Street, building number" required>
                        @error('address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Contact Phone -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Contact Phone
                        </label>
                        <input type="text" name="contact_phone" value="{{ old('contact_phone', $hostel->contact_phone) }}" 
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                               placeholder="e.g., +1234567890">
                    </div>

                    <!-- Contact Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Contact Email
                        </label>
                        <input type="email" name="contact_email" value="{{ old('contact_email', $hostel->contact_email) }}" 
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                               placeholder="contact@hostel.com">
                    </div>

                    <!-- Manager Assignment -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Assign Manager
                        </label>
                        <select name="manager_id" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">No Manager</option>
                            @foreach($managers ?? [] as $manager)
                                <option value="{{ $manager->id }}" {{ old('manager_id', $hostel->manager_id) == $manager->id ? 'selected' : '' }}>
                                    {{ $manager->name }} ({{ $manager->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Coordinates -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Latitude
                        </label>
                        <input type="text" name="latitude" value="{{ old('latitude', $hostel->latitude) }}" 
                               class="w-full border-gray-300 rounded-md shadow-sm" 
                               placeholder="e.g., 40.7128">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Longitude
                        </label>
                        <input type="text" name="longitude" value="{{ old('longitude', $hostel->longitude) }}" 
                               class="w-full border-gray-300 rounded-md shadow-sm" 
                               placeholder="e.g., -74.0060">
                    </div>
                </div>
            </div>
        </div>

        <!-- Description Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                    </svg>
                    Description & Details
                </h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea name="description" rows="5" 
                                  class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                  placeholder="Provide a detailed description of the hostel...">{{ old('description', $hostel->description) }}</textarea>
                    </div>

                    <!-- Rules -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">House Rules</label>
                        <div id="rules-container" class="space-y-2">
                            @if(old('rules', $hostel->rules))
                                @foreach(old('rules', $hostel->rules ?? []) as $rule)
                                    <div class="flex items-center space-x-2">
                                        <input type="text" name="rules[]" value="{{ $rule }}" 
                                               class="flex-1 border-gray-300 rounded-md shadow-sm" 
                                               placeholder="e.g., No smoking inside">
                                        <button type="button" onclick="this.parentElement.remove()" 
                                                class="text-red-600 hover:text-red-900">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                @endforeach
                            @else
                                <div class="flex items-center space-x-2">
                                    <input type="text" name="rules[]" class="flex-1 border-gray-300 rounded-md shadow-sm" 
                                           placeholder="e.g., No smoking inside">
                                    <button type="button" onclick="this.parentElement.remove()" 
                                            class="text-red-600 hover:text-red-900">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            @endif
                        </div>
                        <button type="button" onclick="addRule()" 
                                class="mt-2 inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Add Rule
                        </button>
                    </div>

                    <!-- Check-in/out Times -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Check-in Time</label>
                            <input type="time" name="check_in_time" value="{{ old('check_in_time', optional($hostel->check_in_time)->format('H:i') ?? '14:00') }}" 
                                   class="w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Check-out Time</label>
                            <input type="time" name="check_out_time" value="{{ old('check_out_time', optional($hostel->check_out_time)->format('H:i') ?? '12:00') }}" 
                                   class="w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Amenities Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                    </svg>
                    Amenities
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @php
                        $amenityGroups = [
                            'Basic' => ['wifi' => 'Free WiFi', 'parking' => 'Parking', 'security' => '24/7 Security', 'laundry' => 'Laundry', 'kitchen' => 'Kitchen'],
                            'Comfort' => ['ac' => 'Air Conditioning', 'heating' => 'Heating', 'elevator' => 'Elevator', 'furnished' => 'Furnished'],
                            'Recreation' => ['gym' => 'Gym', 'pool' => 'Swimming Pool', 'garden' => 'Garden', 'common_room' => 'Common Room', 'tv_lounge' => 'TV Lounge'],
                            'Services' => ['cleaning' => 'Cleaning Service', 'meal_plan' => 'Meal Plan', 'shuttle' => 'Shuttle', 'reception' => '24/7 Reception']
                        ];
                        $hostelAmenities = old('amenities', $hostel->amenities ?? []);
                    @endphp

                    @foreach($amenityGroups as $group => $amenities)
                        <div class="col-span-full">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">{{ $group }}</h4>
                        </div>
                        @foreach($amenities as $key => $label)
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="amenities[]" value="{{ $key }}" 
                                       {{ in_array($key, $hostelAmenities) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">{{ $label }}</span>
                            </label>
                        @endforeach
                        <div class="col-span-full"><hr class="my-2"></div>
                    @endforeach

                    <!-- Custom Amenities -->
                    <div class="col-span-full mt-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Custom Amenities</label>
                        <div id="custom-amenities-container" class="space-y-2">
                            @foreach($hostelAmenities as $amenity)
                                @if(!in_array($amenity, array_keys(array_merge(...array_values($amenityGroups)))))
                                    <div class="flex items-center space-x-2">
                                        <input type="checkbox" name="amenities[]" value="{{ $amenity }}" checked
                                               class="rounded border-gray-300 text-blue-600 shadow-sm">
                                        <span class="text-sm text-gray-700">{{ ucwords(str_replace('_', ' ', $amenity)) }}</span>
                                        <button type="button" onclick="this.parentElement.remove()" 
                                                class="text-red-600 hover:text-red-900">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                        <div class="flex items-center space-x-2 mt-2">
                            <input type="text" id="custom-amenity" 
                                   class="flex-1 border-gray-300 rounded-md shadow-sm" 
                                   placeholder="e.g., Yoga Studio">
                            <button type="button" onclick="addCustomAmenity()" 
                                    class="px-4 py-2 bg-gray-800 text-white text-sm rounded-md hover:bg-gray-900">
                                Add
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Images Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Images
                </h3>
            </div>
            <div class="p-6">
                <!-- Existing Images -->
                @if($hostel->images && count($hostel->images) > 0)
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Current Images</label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @foreach($hostel->images as $image)
                                <div class="relative group">
                                    <img src="{{ Storage::url($image->path) }}" 
                                         alt="{{ $hostel->name }}" 
                                         class="w-full h-32 object-cover rounded-lg">
                                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-opacity rounded-lg flex items-center justify-center">
                                        <div class="opacity-0 group-hover:opacity-100 flex space-x-2">
                                            @if(!$image->is_primary)
                                                <form action="{{ route('admin.hostels.image.primary', [$hostel, $image]) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="bg-blue-600 text-white p-1 rounded hover:bg-blue-700" title="Set as primary">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endif
                                            <form action="{{ route('admin.hostels.image.destroy', [$hostel, $image]) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="bg-red-600 text-white p-1 rounded hover:bg-red-700" title="Delete image" onclick="return confirm('Are you sure you want to delete this image?')">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    @if($image->is_primary)
                                        <span class="absolute top-2 left-2 bg-blue-600 text-white text-xs px-2 py-1 rounded">
                                            Primary
                                        </span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Upload New Images -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Add New Images</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-500 transition-colors">
                        <input type="file" name="images[]" multiple accept="image/*" 
                               class="hidden" id="image-upload">
                        <label for="image-upload" class="cursor-pointer">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <p class="mt-1 text-sm text-gray-600">
                                <span class="text-blue-600 font-medium">Click to upload</span> or drag and drop
                            </p>
                            <p class="text-xs text-gray-500 mt-1">PNG, JPG, GIF up to 10MB each</p>
                        </label>
                    </div>
                    <div id="image-preview" class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4"></div>
                </div>
            </div>
        </div>

        <!-- Settings Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Settings
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status" class="w-full border-gray-300 rounded-md shadow-sm">
                            <option value="active" {{ old('status', $hostel->status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $hostel->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="maintenance" {{ old('status', $hostel->status) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        </select>
                    </div>

                    <!-- Approval -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Approval Status</label>
                        <select name="is_approved" class="w-full border-gray-300 rounded-md shadow-sm">
                            <option value="1" {{ old('is_approved', $hostel->is_approved) == 1 ? 'selected' : '' }}>Approved</option>
                            <option value="0" {{ old('is_approved', $hostel->is_approved) == 0 ? 'selected' : '' }}>Pending</option>
                        </select>
                    </div>

                    <!-- Featured -->
                    <div class="flex items-center space-x-4">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $hostel->is_featured) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Feature this hostel</span>
                        </label>
                    </div>

                    <!-- Room Counts (Read Only) -->
                    <div class="col-span-2 grid grid-cols-2 gap-4 mt-4 pt-4 border-t border-gray-200">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Total Rooms</label>
                            <div class="w-full px-3 py-2 border border-gray-200 bg-gray-50 rounded-md text-gray-700">
                                {{ $hostel->total_rooms ?? 0 }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Available Rooms</label>
                            <div class="w-full px-3 py-2 border border-gray-200 bg-gray-50 rounded-md text-gray-700">
                                {{ $hostel->available_rooms ?? 0 }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-between">
            <div>
                <button type="button" onclick="confirmDelete()" 
                        class="px-6 py-2.5 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50">
                    Delete Hostel
                </button>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.hostels.show', $hostel) }}" 
                   class="px-6 py-2.5 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-2.5 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                    Update Hostel
                </button>
            </div>
        </div>
    </form>

    <!-- Danger Zone - Delete Form -->
    <form id="delete-form" action="{{ route('admin.hostels.destroy', $hostel) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
</div>

@push('scripts')
<script>
// Add rule field
function addRule() {
    const container = document.getElementById('rules-container');
    const div = document.createElement('div');
    div.className = 'flex items-center space-x-2';
    div.innerHTML = `
        <input type="text" name="rules[]" class="flex-1 border-gray-300 rounded-md shadow-sm" placeholder="e.g., No smoking inside">
        <button type="button" onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-900">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
            </svg>
        </button>
    `;
    container.appendChild(div);
}

// Add custom amenity
function addCustomAmenity() {
    const input = document.getElementById('custom-amenity');
    const value = input.value.trim();
    if (value) {
        const container = document.getElementById('custom-amenities-container');
        const div = document.createElement('div');
        div.className = 'flex items-center space-x-2';
        div.innerHTML = `
            <input type="checkbox" name="amenities[]" value="${value.toLowerCase().replace(/\s+/g, '_')}" checked
                   class="rounded border-gray-300 text-blue-600 shadow-sm">
            <span class="text-sm text-gray-700">${value}</span>
            <button type="button" onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-900">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        `;
        container.appendChild(div);
        input.value = '';
    }
}

// Image preview
document.getElementById('image-upload')?.addEventListener('change', function(e) {
    const preview = document.getElementById('image-preview');
    preview.innerHTML = '';
    
    Array.from(e.target.files).forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const div = document.createElement('div');
            div.className = 'relative';
            div.innerHTML = `
                <img src="${e.target.result}" class="w-full h-32 object-cover rounded-lg">
                <button type="button" onclick="this.parentElement.remove()" 
                        class="absolute top-1 right-1 bg-red-600 text-white rounded-full p-1 hover:bg-red-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            `;
            preview.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
});

// Confirm delete
function confirmDelete() {
    if (confirm('Are you sure you want to delete this hostel? This will also delete all rooms, bookings, and reviews associated with it. This action cannot be undone.')) {
        document.getElementById('delete-form').submit();
    }
}
</script>
@endpush
@endsection