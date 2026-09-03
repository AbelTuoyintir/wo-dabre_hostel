@extends('layouts.agent')

@section('title', 'Add New Room')
@section('page-title', 'Add New Room')

@section('content')
<div class="max-w-4xl mx-auto py-4">
    <!-- Header -->
    <div class="mb-6 bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex justify-between items-center">
        <div class="flex items-center space-x-3">
            <a href="{{ $hostel ? route('agent.hostels.show', $hostel->uuid ?? $hostel->id) : route('agent.hostels.index') }}"
               class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 transition-colors duration-200"
               aria-label="Back">
                <i class="fas fa-arrow-left text-sm" aria-hidden="true"></i>
            </a>
            <div>
                <h2 class="text-base font-bold text-gray-800">Add New Room</h2>
                <p class="text-xs text-gray-500">
                    {{ $hostel ? 'Adding room for ' . $hostel->name : 'Select a hostel and fill in the details below' }}
                </p>
            </div>
        </div>
    </div>

    <!-- Help Box -->
    <div class="mb-6 bg-purple-50 rounded-xl p-4 border border-purple-100 flex items-start space-x-3">
        <div class="p-2 bg-purple-100 text-purple-700 rounded-lg flex-shrink-0">
            <i class="fas fa-lightbulb text-sm" aria-hidden="true"></i>
        </div>
        <div class="text-xs text-purple-900 leading-relaxed">
            <h4 class="font-semibold mb-1">Quick Tips for Adding Rooms:</h4>
            <ul class="list-disc list-inside space-y-1 text-purple-800">
                <li>Room numbers must be unique within the selected hostel.</li>
                <li>Each room added earns you an instant commission bonus!</li>
                <li>High-quality images and videos attract more student bookings.</li>
            </ul>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <form action="{{ route('agent.rooms.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf

            <!-- Hostel Selection -->
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-purple-700 mb-3 flex items-center">
                    <i class="fas fa-building mr-2" aria-hidden="true"></i> Hostel Selection
                </h3>
                @if($hostel)
                    <input type="hidden" name="hostel_id" value="{{ $hostel->uuid ?? $hostel->id }}">
                    <div class="p-3 bg-gray-50 rounded-lg border border-gray-200 flex items-center justify-between">
                        <div>
                            <span class="text-xs font-semibold text-gray-800">{{ $hostel->name }}</span>
                            <p class="text-[11px] text-gray-500">{{ $hostel->address }}</p>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-purple-100 text-purple-800">
                            Selected
                        </span>
                    </div>
                @else
                    <div>
                        <label for="hostel_id" class="block text-xs font-medium text-gray-700 mb-1">
                            Select Hostel <span class="text-red-500">*</span>
                        </label>
                        <select name="hostel_id" id="hostel_id" required
                                class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('hostel_id') border-red-500 @enderror">
                            <option value="">-- Choose a Hostel --</option>
                            @foreach($hostels as $h)
                                <option value="{{ $h->uuid ?? $h->id }}" {{ old('hostel_id') == ($h->uuid ?? $h->id) ? 'selected' : '' }}>
                                    {{ $h->name }} ({{ $h->location }})
                                </option>
                            @endforeach
                        </select>
                        @error('hostel_id')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                @endif
            </div>

            <hr class="border-gray-100">

            <!-- Basic Information -->
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-purple-700 mb-3 flex items-center">
                    <i class="fas fa-info-circle mr-2" aria-hidden="true"></i> Basic Information
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="room_number" class="block text-xs font-medium text-gray-700 mb-1">
                            Room Number <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="room_number" id="room_number" value="{{ old('room_number', old('number')) }}" required
                               placeholder="e.g., 101, A202"
                               class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('room_number') border-red-500 @enderror @error('number') border-red-500 @enderror">
                        @error('room_number')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                        @error('number')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="floor" class="block text-xs font-medium text-gray-700 mb-1">
                            Floor
                        </label>
                        <input type="number" name="floor" id="floor" value="{{ old('floor') }}" min="0" placeholder="e.g. 1, 2"
                               class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    </div>

                    <div>
                        <label for="room_type" class="block text-xs font-medium text-gray-700 mb-1">
                            Room Type <span class="text-red-500">*</span>
                        </label>
                        <select name="room_type" id="room_type" required
                                class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('room_type') border-red-500 @enderror">
                            <option value="">-- Select Room Type --</option>
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
                            <optgroup label="Triple / Quad / Dorms">
                                <option value="triple_self_contained" {{ old('room_type') == 'triple_self_contained' ? 'selected' : '' }}>Three in a Room - Self Contained</option>
                                <option value="quad_self_contained" {{ old('room_type') == 'quad_self_contained' ? 'selected' : '' }}>Four in a Room - Self Contained</option>
                                <option value="dorm_4_shared" {{ old('room_type') == 'dorm_4_shared' ? 'selected' : '' }}>4-Bed Dormitory - Shared Bathroom</option>
                                <option value="dorm_6_shared" {{ old('room_type') == 'dorm_6_shared' ? 'selected' : '' }}>6-Bed Dormitory - Shared Bathroom</option>
                            </optgroup>
                        </select>
                        @error('room_type')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <hr class="border-gray-100">

            <!-- Specifications -->
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-purple-700 mb-3 flex items-center">
                    <i class="fas fa-ruler mr-2" aria-hidden="true"></i> Specifications &amp; Pricing
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="capacity" class="block text-xs font-medium text-gray-700 mb-1">
                            Capacity (Persons) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="capacity" id="capacity" value="{{ old('capacity', 1) }}" min="1" required
                               class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('capacity') border-red-500 @enderror">
                        @error('capacity')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="price_per_year" class="block text-xs font-medium text-gray-700 mb-1">
                            Price / Year (₵) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" step="0.01" name="price_per_year" id="price_per_year" value="{{ old('price_per_year', old('room_cost')) }}" min="0" required
                               placeholder="0.00"
                               class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('price_per_year') border-red-500 @enderror @error('room_cost') border-red-500 @enderror">
                        @error('price_per_year')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                        @error('room_cost')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="size_sqm" class="block text-xs font-medium text-gray-700 mb-1">
                            Size (sqm)
                        </label>
                        <input type="number" step="0.01" name="size_sqm" id="size_sqm" value="{{ old('size_sqm') }}" min="0" placeholder="e.g. 20.5"
                               class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    </div>
                </div>
            </div>

            <hr class="border-gray-100">

            <!-- Features & Gender -->
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-purple-700 mb-3 flex items-center">
                    <i class="fas fa-couch mr-2" aria-hidden="true"></i> Features &amp; Preference
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="gender" class="block text-xs font-medium text-gray-700 mb-1">Gender Preference</label>
                        <select name="gender" id="gender" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                            <option value="any" {{ old('gender') == 'any' ? 'selected' : '' }}>Any Gender</option>
                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male Only</option>
                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female Only</option>
                        </select>
                    </div>

                    <div>
                        <label for="window_type" class="block text-xs font-medium text-gray-700 mb-1">Window View</label>
                        <select name="window_type" id="window_type" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                            <option value="">Select View</option>
                            <option value="street" {{ old('window_type') == 'street' ? 'selected' : '' }}>Street View</option>
                            <option value="courtyard" {{ old('window_type') == 'courtyard' ? 'selected' : '' }}>Courtyard</option>
                            <option value="garden" {{ old('window_type') == 'garden' ? 'selected' : '' }}>Garden</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center space-x-6 mt-4">
                    <label class="flex items-center space-x-2 text-xs font-medium text-gray-700 cursor-pointer">
                        <input type="checkbox" name="furnished" value="1" {{ old('furnished') ? 'checked' : '' }}
                               class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                        <span>Furnished</span>
                    </label>
                    <label class="flex items-center space-x-2 text-xs font-medium text-gray-700 cursor-pointer">
                        <input type="checkbox" name="private_bathroom" value="1" {{ old('private_bathroom') ? 'checked' : '' }}
                               class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                        <span>Private Bathroom</span>
                    </label>
                </div>
            </div>

            <hr class="border-gray-100">

            <!-- Media Section -->
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-purple-700 mb-3 flex items-center">
                    <i class="fas fa-images mr-2" aria-hidden="true"></i> Media Uploads
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="cover_image" class="block text-xs font-medium text-gray-700 mb-1">Cover Image</label>
                        <input type="file" name="cover_image" id="cover_image" accept="image/*"
                               class="w-full px-3 py-1.5 text-xs border border-gray-300 rounded-lg bg-gray-50 focus:ring-2 focus:ring-purple-500">
                        <p class="text-[10px] text-gray-500 mt-1">Primary photo of the room (PNG, JPG up to 10MB)</p>
                    </div>

                    <div>
                        <label for="room_video" class="block text-xs font-medium text-gray-700 mb-1">Room Video (Optional)</label>
                        <input type="file" name="room_video" id="room_video" accept="video/*"
                               class="w-full px-3 py-1.5 text-xs border border-gray-300 rounded-lg bg-gray-50 focus:ring-2 focus:ring-purple-500">
                        <p class="text-[10px] text-gray-500 mt-1">Short walk-through video (MP4/WebM up to 50MB)</p>
                    </div>

                    <div class="md:col-span-2">
                        <label for="gallery_images" class="block text-xs font-medium text-gray-700 mb-1">Gallery Images (Optional)</label>
                        <input type="file" name="gallery_images[]" id="gallery_images" accept="image/*" multiple
                               class="w-full px-3 py-1.5 text-xs border border-gray-300 rounded-lg bg-gray-50 focus:ring-2 focus:ring-purple-500">
                        <p class="text-[10px] text-gray-500 mt-1">Select multiple images showcasing different angles.</p>
                    </div>
                </div>
            </div>

            <hr class="border-gray-100">

            <!-- Description & Status -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2">
                    <label for="description" class="block text-xs font-medium text-gray-700 mb-1">Room Description</label>
                    <textarea name="description" id="description" rows="3" placeholder="Describe the room layout, included items, or special features..."
                              class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label for="status" class="block text-xs font-medium text-gray-700 mb-1">Initial Status</label>
                    <select name="status" id="status" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        <option value="available" {{ old('status', 'available') == 'available' ? 'selected' : '' }}>Available</option>
                        <option value="unavailable" {{ old('status') == 'unavailable' ? 'selected' : '' }}>Unavailable / Maintenance</option>
                    </select>
                </div>
            </div>

            <!-- Actions -->
            <div class="pt-4 flex items-center justify-end space-x-3">
                <a href="{{ $hostel ? route('agent.hostels.show', $hostel->uuid ?? $hostel->id) : route('agent.hostels.index') }}"
                   class="px-4 py-2 border border-gray-300 text-gray-700 text-xs font-semibold rounded-lg hover:bg-gray-50 transition-colors duration-200">
                    Cancel
                </a>
                <button type="submit"
                        class="px-5 py-2 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white text-xs font-semibold rounded-lg shadow-md transition-all duration-200 flex items-center space-x-2">
                    <i class="fas fa-plus-circle" aria-hidden="true"></i>
                    <span>Create Room</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
