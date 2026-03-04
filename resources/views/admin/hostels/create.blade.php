@extends('layouts.app')

@section('title', 'Add New Hostel')
@section('page-title', 'Create New Hostel')

@section('content')
<div class="max-w-4xl mx-auto">

    <form action="{{ route('admin.hostels.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- Basic Information -->
        <div class="bg-white rounded-lg shadow border p-6">
            <h3 class="text-lg font-semibold mb-4">Basic Information</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Hostel Name -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-1">Hostel Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                        class="w-full border rounded-md px-3 py-2
                        @error('name') border-red-500 @enderror">

                    @error('name')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Location -->
                <div>
                    <label class="block text-sm font-medium mb-1">Location *</label>
                    <input type="text" name="location" value="{{ old('location') }}"
                        class="w-full border rounded-md px-3 py-2
                        @error('location') border-red-500 @enderror">

                    @error('location')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Address -->
                <div>
                    <label class="block text-sm font-medium mb-1">Address *</label>
                    <input type="text" name="address" value="{{ old('address') }}"
                        class="w-full border rounded-md px-3 py-2
                        @error('address') border-red-500 @enderror">

                    @error('address')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label class="block text-sm font-medium mb-1">Contact Phone</label>
                    <input type="text" name="contact_phone" value="{{ old('contact_phone') }}"
                        class="w-full border rounded-md px-3 py-2">
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium mb-1">Contact Email</label>
                    <input type="email" name="contact_email" value="{{ old('contact_email') }}"
                        class="w-full border rounded-md px-3 py-2">
                </div>

                <!-- Manager -->
                <div>
                    <label class="block text-sm font-medium mb-1">Assign Manager</label>
                    <select name="manager_id" class="w-full border rounded-md px-3 py-2">
                        <option value="">No Manager</option>
                        @foreach($managers as $manager)
                            <option value="{{ $manager->id }}"
                                {{ old('manager_id') == $manager->id ? 'selected' : '' }}>
                                {{ $manager->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Hostel Images -->
        <div class="bg-white rounded-lg shadow border p-6">
            <h3 class="text-lg font-semibold mb-4">Hostel Images</h3>

            <!-- Cover Image -->
            <div class="mb-6">
                <label class="block text-sm font-medium mb-2">Cover Image *</label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 hover:border-blue-500 transition-colors">
                    <div class="text-center" id="cover-image-preview">
                        <!-- Preview will be shown here -->
                        <div class="mb-3">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H8a4 4 0 01-4-4V12a4 4 0 014-4h32a4 4 0 014 4v16.5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M30 28l-6-6-6 6M20 16h.01" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                        <div class="text-sm text-gray-600">
                            <label for="cover_image" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500">
                                <span>Upload a cover image</span>
                                <input id="cover_image" name="cover_image" type="file" class="sr-only" accept="image/*" onchange="previewCoverImage(this)">
                            </label>
                            <p class="text-xs text-gray-500 mt-1">PNG, JPG, GIF up to 10MB</p>
                        </div>
                    </div>
                </div>
                @error('cover_image')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Gallery Images -->
            <div>
                <label class="block text-sm font-medium mb-2">Gallery Images</label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 hover:border-blue-500 transition-colors">
                    <div class="text-center">
                        <div class="mb-3">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H8a4 4 0 01-4-4V12a4 4 0 014-4h32a4 4 0 014 4v16.5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M30 28l-6-6-6 6M20 16h.01" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                        <div class="text-sm text-gray-600">
                            <label for="gallery_images" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500">
                                <span>Upload multiple images</span>
                                <input id="gallery_images" name="gallery_images[]" type="file" class="sr-only" accept="image/*" multiple onchange="previewGalleryImages(this)">
                            </label>
                            <p class="text-xs text-gray-500 mt-1">PNG, JPG, GIF up to 10MB each (max 5 images)</p>
                        </div>
                    </div>

                    <!-- Gallery Preview Grid -->
                    <div id="gallery-preview" class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4 hidden">
                        <!-- Images will be displayed here dynamically -->
                    </div>
                </div>
                @error('gallery_images.*')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Description -->
        <div class="bg-white rounded-lg shadow border p-6">
            <label class="block text-sm font-medium mb-1">Description</label>
            <textarea name="description" rows="4"
                class="w-full border rounded-md px-3 py-2">{{ old('description') }}</textarea>
        </div>

        <!-- Submit -->
        <div class="flex justify-end gap-4">
            <a href="{{ route('admin.hostels.index') }}"
               class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit"
                class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700">
                Save Hostel
            </button>
        </div>

    </form>
</div>

@push('scripts')
<script>
    // Preview Cover Image
    function previewCoverImage(input) {
        const previewContainer = document.getElementById('cover-image-preview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewContainer.innerHTML = `
                    <div class="relative">
                        <img src="${e.target.result}" class="max-h-48 mx-auto rounded-lg shadow-md" alt="Cover preview">
                        <button type="button" onclick="removeCoverImage()" class="absolute top-0 right-0 bg-red-500 text-white rounded-full p-1 hover:bg-red-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                `;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function removeCoverImage() {
        const input = document.getElementById('cover_image');
        input.value = '';
        const previewContainer = document.getElementById('cover-image-preview');
        previewContainer.innerHTML = `
            <div class="mb-3">
                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H8a4 4 0 01-4-4V12a4 4 0 014-4h32a4 4 0 014 4v16.5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M30 28l-6-6-6 6M20 16h.01" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
            <div class="text-sm text-gray-600">
                <label for="cover_image" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500">
                    <span>Upload a cover image</span>
                    <input id="cover_image" name="cover_image" type="file" class="sr-only" accept="image/*" onchange="previewCoverImage(this)">
                </label>
                <p class="text-xs text-gray-500 mt-1">PNG, JPG, GIF up to 10MB</p>
            </div>
        `;
    }

    // Preview Gallery Images
    function previewGalleryImages(input) {
        const previewContainer = document.getElementById('gallery-preview');
        previewContainer.innerHTML = '';

        if (input.files && input.files.length > 0) {
            previewContainer.classList.remove('hidden');

            for (let i = 0; i < Math.min(input.files.length, 5); i++) {
                const file = input.files[i];
                const reader = new FileReader();

                reader.onload = function(e) {
                    const previewDiv = document.createElement('div');
                    previewDiv.className = 'relative group';
                    previewDiv.innerHTML = `
                        <img src="${e.target.result}" class="w-full h-24 object-cover rounded-lg shadow-sm" alt="Gallery preview">
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all rounded-lg"></div>
                        <button type="button" onclick="removeGalleryImage(this, ${i})" class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-0.5 opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    `;
                    previewContainer.appendChild(previewDiv);
                }

                reader.readAsDataURL(file);
            }

            // Show message if more than 5 files selected
            if (input.files.length > 5) {
                const warningDiv = document.createElement('div');
                warningDiv.className = 'col-span-full text-center text-sm text-amber-600 mt-2';
                warningDiv.innerHTML = 'Maximum 5 images allowed. Only the first 5 will be uploaded.';
                previewContainer.appendChild(warningDiv);
            }
        } else {
            previewContainer.classList.add('hidden');
        }
    }

    function removeGalleryImage(button, index) {
        const input = document.getElementById('gallery_images');
        const dt = new DataTransfer();

        for (let i = 0; i < input.files.length; i++) {
            if (i !== index) {
                dt.items.add(input.files[i]);
            }
        }

        input.files = dt.files;

        // Refresh preview
        previewGalleryImages(input);
    }
</script>
@endpush

@push('styles')
<style>
    /* Optional custom styles for better appearance */
    .border-dashed {
        background-image: url("data:image/svg+xml,%3csvg width='100%25' height='100%25' xmlns='http://www.w3.org/2000/svg'%3e%3crect width='100%25' height='100%25' fill='none' stroke='%23CBD5E0' stroke-width='2' stroke-dasharray='6%2c 14' stroke-dashoffset='0' stroke-linecap='square'/%3e%3c/svg%3e");
    }

    .border-dashed:hover {
        background-image: url("data:image/svg+xml,%3csvg width='100%25' height='100%25' xmlns='http://www.w3.org/2000/svg'%3e%3crect width='100%25' height='100%25' fill='none' stroke='%233B82F6' stroke-width='2' stroke-dasharray='6%2c 14' stroke-dashoffset='0' stroke-linecap='square'/%3e%3c/svg%3e");
    }
</style>
@endpush
@endsection
