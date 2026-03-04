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

        <form action="{{ route('admin.rooms.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
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
                            <option value="single_room" {{ old('room_type') == 'single_room' ? 'selected' : '' }}>Single Room</option>
                            <option value="shared_2" {{ old('room_type') == 'shared_2' ? 'selected' : '' }}>2 in room</option>
                            <option value="shared_4" {{ old('room_type') == 'shared_4' ? 'selected' : '' }}>4 in room</option>
                            <option value="executive" {{ old('room_type') == 'executive' ? 'selected' : '' }}>Executive</option>
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

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Cover Image Upload -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <span class="text-red-500">*</span> Cover Image
                            </label>
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 hover:border-blue-500 transition-colors">
                                <div class="text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H8a4 4 0 01-4-4V12a4 4 0 014-4h32a4 4 0 014 4v16.5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="mt-4">
                                        <label for="cover_image" class="cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500">
                                            <span>Click to upload cover image</span>
                                            <input id="cover_image" name="cover_image" type="file" class="sr-only" accept="image/*" onchange="previewCoverImage(this)" required>
                                        </label>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-2">PNG, JPG, GIF up to 10MB</p>
                                    <p class="text-xs text-gray-400 mt-1">This will be the main image displayed for the room</p>
                                </div>
                                <div id="cover-preview" class="mt-4 hidden">
                                    <img src="" class="max-h-40 mx-auto rounded-lg shadow-md" alt="Cover preview">
                                </div>
                            </div>
                            @error('cover_image')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Gallery Images Upload -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Gallery Images (Optional)
                            </label>
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 hover:border-blue-500 transition-colors">
                                <div class="text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H8a4 4 0 01-4-4V12a4 4 0 014-4h32a4 4 0 014 4v16.5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M30 28l-6-6-6 6M20 16h.01" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="mt-4">
                                        <label for="gallery_images" class="cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500">
                                            <span>Click to upload gallery images</span>
                                            <input id="gallery_images" name="gallery_images[]" type="file" class="sr-only" accept="image/*" multiple onchange="previewGalleryImages(this)">
                                        </label>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-2">PNG, JPG, GIF up to 10MB each (max 5 images)</p>
                                    <p class="text-xs text-gray-400 mt-1">Additional photos showing different angles of the room</p>
                                </div>
                                <div id="gallery-preview" class="grid grid-cols-2 gap-3 mt-4 hidden"></div>
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

    function previewGalleryImages(input) {
        const preview = document.getElementById('gallery-preview');
        preview.innerHTML = '';

        if (input.files && input.files.length > 0) {
            preview.classList.remove('hidden');

            // Limit to 5 images
            const maxFiles = Math.min(input.files.length, 5);

            for (let i = 0; i < maxFiles; i++) {
                const file = input.files[i];
                const reader = new FileReader();

                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'relative group';
                    div.innerHTML = `
                        <img src="${e.target.result}"
                             class="w-full h-24 object-cover rounded-lg shadow-sm"
                             alt="Gallery preview ${i+1}">
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition-all rounded-lg"></div>
                        <span class="absolute top-1 right-1 bg-green-500 text-white text-xs px-1.5 py-0.5 rounded-full">
                            New
                        </span>
                    `;
                    preview.appendChild(div);
                }

                reader.readAsDataURL(file);
            }

            // Show warning if more than 5 files selected
            if (input.files.length > 5) {
                const warningDiv = document.createElement('div');
                warningDiv.className = 'col-span-2 text-center text-xs text-amber-600 mt-2';
                warningDiv.textContent = 'Maximum 5 images allowed. Only the first 5 will be uploaded.';
                preview.appendChild(warningDiv);
            }
        } else {
            preview.classList.add('hidden');
        }
    }

    // Debug function to check if files are selected
    document.getElementById('cover_image').addEventListener('change', function(e) {
        console.log('Cover image selected:', e.target.files.length > 0 ? e.target.files[0].name : 'No file');
    });

    document.getElementById('gallery_images').addEventListener('change', function(e) {
        console.log('Gallery images selected:', e.target.files.length);
    });
</script>
@endpush

@push('styles')
<style>
    .border-dashed {
        background-image: url("data:image/svg+xml,%3csvg width='100%25' height='100%25' xmlns='http://www.w3.org/2000/svg'%3e%3crect width='100%25' height='100%25' fill='none' stroke='%23CBD5E0' stroke-width='2' stroke-dasharray='6%2c 14' stroke-dashoffset='0' stroke-linecap='square'/%3e%3c/svg%3e");
    }
    .border-dashed:hover {
        background-image: url("data:image/svg+xml,%3csvg width='100%25' height='100%25' xmlns='http://www.w3.org/2000/svg'%3e%3crect width='100%25' height='100%25' fill='none' stroke='%233B82F6' stroke-width='2' stroke-dasharray='6%2c 14' stroke-dashoffset='0' stroke-linecap='square'/%3e%3c/svg%3e");
    }
</style>
@endpush
@endsection
