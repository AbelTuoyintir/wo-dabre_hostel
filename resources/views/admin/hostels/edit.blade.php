@extends('layouts.app')

@section('title', 'Edit Hostel')
@section('page-title', 'Edit Hostel: ' . $hostel->name)

@section('content')
<div class="max-w-4xl mx-auto">
    <form action="{{ route('admin.hostels.update', $hostel) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Basic Information -->
        <div class="bg-white rounded-lg shadow border p-6">
            <h3 class="text-lg font-semibold mb-4">Basic Information</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Hostel Name -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-1">Hostel Name *</label>
                    <input type="text" name="name" value="{{ old('name', $hostel->name) }}"
                        class="w-full border rounded-md px-3 py-2 @error('name') border-red-500 @enderror">
                    @error('name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Location -->
                <div>
                    <label class="block text-sm font-medium mb-1">Location *</label>
                    <input type="text" name="location" value="{{ old('location', $hostel->location) }}"
                        class="w-full border rounded-md px-3 py-2 @error('location') border-red-500 @enderror">
                    @error('location') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Address -->
                <div>
                    <label class="block text-sm font-medium mb-1">Address *</label>
                    <input type="text" name="address" value="{{ old('address', $hostel->address) }}"
                        class="w-full border rounded-md px-3 py-2 @error('address') border-red-500 @enderror">
                    @error('address') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label class="block text-sm font-medium mb-1">Contact Phone</label>
                    <input type="text" name="contact_phone" value="{{ old('contact_phone', $hostel->contact_phone) }}"
                        class="w-full border rounded-md px-3 py-2">
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium mb-1">Contact Email</label>
                    <input type="email" name="contact_email" value="{{ old('contact_email', $hostel->contact_email) }}"
                        class="w-full border rounded-md px-3 py-2">
                </div>

                <!-- Manager -->
                <div>
                    <label class="block text-sm font-medium mb-1">Assign Manager</label>
                    <select name="manager_id" class="w-full border rounded-md px-3 py-2">
                        <option value="">No Manager</option>
                        @foreach($managers as $manager)
                            <option value="{{ $manager->id }}" {{ old('manager_id', $hostel->manager_id) == $manager->id ? 'selected' : '' }}>
                                {{ $manager->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Toggles -->
                <div class="flex items-center space-x-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_featured" value="1"
                            {{ old('is_featured', $hostel->is_featured) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-600">Featured Hostel</span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" name="is_approved" value="1"
                            {{ old('is_approved', $hostel->is_approved) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-600">Approved</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Image Management Section -->
        <div class="bg-white rounded-lg shadow border p-6">
            <h3 class="text-lg font-semibold mb-4">Manage Hostel Images</h3>

            <!-- Current Images Display -->
            @if($hostel->images->count() > 0)
                <div class="mb-6">
                    <h4 class="text-md font-medium mb-3">Current Images</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4" id="current-images">
                        @foreach($hostel->images()->orderBy('is_primary', 'desc')->orderBy('order')->get() as $image)
                            <div class="relative group border rounded-lg p-2 {{ $image->is_primary ? 'bg-blue-50 border-blue-300' : '' }}" data-image-id="{{ $image->id }}">
                                <img src="{{ $image->url }}"
                                     class="w-full h-32 object-cover rounded-lg mb-2"
                                     onerror="this.style.opacity='0.5';this.nextElementSibling.style.display='block';">

                                <!-- Image Labels -->
                                <div class="absolute top-3 left-3 flex gap-1">
                                    @if($image->is_primary)
                                        <span class="bg-blue-500 text-white text-xs px-2 py-1 rounded-full">
                                            Primary
                                        </span>
                                    @endif
                                </div>

                                <!-- Image Actions -->
                                <div class="flex justify-between items-center mt-2">
                                    <div class="flex gap-2">
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
                                        <span class="text-xs text-gray-500">Order: {{ $image->order }}</span>
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
            <div class="border-t pt-6">
                <h4 class="text-md font-medium mb-3">Add New Images</h4>

                <!-- New Cover Image -->
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">New Cover Image (optional)</label>
                    <p class="text-xs text-gray-500 mb-2">Upload a new image to replace or add as primary</p>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4">
                        <input type="file" name="cover_image" accept="image/*" class="w-full" onchange="previewNewCover(this)">
                        <div id="new-cover-preview" class="mt-2 hidden">
                            <img src="" class="h-32 w-auto rounded-lg" alt="Preview">
                        </div>
                    </div>
                </div>

                <!-- New Gallery Images -->
                <div>
                    <label class="block text-sm font-medium mb-2">Additional Gallery Images (max 5)</label>
                    <p class="text-xs text-gray-500 mb-2">Upload multiple new images to add to gallery</p>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4">
                        <input type="file" name="gallery_images[]" multiple accept="image/*" class="w-full" onchange="previewNewGallery(this)">
                        <div id="new-gallery-preview" class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4 hidden"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Description -->
        <div class="bg-white rounded-lg shadow border p-6">
            <label class="block text-sm font-medium mb-1">Description</label>
            <textarea name="description" rows="4"
                class="w-full border rounded-md px-3 py-2">{{ old('description', $hostel->description) }}</textarea>
        </div>

        <!-- Submit Buttons -->
        <div class="flex justify-end gap-4">
            <a href="{{ route('admin.hostels.show', $hostel) }}"
               class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit"
                class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700">
                Update Hostel
            </button>
        </div>
    </form>
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
            const imageContainer = document.querySelector(`[data-image-id="${imageId}"]`);
            if (imageContainer) {
                imageContainer.style.opacity = '0.5';
                imageContainer.style.pointerEvents = 'none';
                imageContainer.classList.add('bg-gray-100');

                // Add "marked for removal" text
                const removalBadge = document.createElement('div');
                removalBadge.className = 'absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-red-500 text-white px-3 py-1 rounded-full text-sm font-bold z-10';
                removalBadge.textContent = 'Marked for Removal';
                imageContainer.style.position = 'relative';
                imageContainer.appendChild(removalBadge);
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
        const selectedContainer = document.querySelector(`[data-image-id="${imageId}"]`);
        selectedContainer.classList.add('bg-blue-50', 'border-blue-300');

        // Add primary badge
        const badgeContainer = selectedContainer.querySelector('.absolute.top-3.left-3');
        if (badgeContainer) {
            badgeContainer.innerHTML = '<span class="bg-blue-500 text-white text-xs px-2 py-1 rounded-full">Primary</span>';
        }

        alert('Primary image updated. Save the form to confirm changes.');
    }

    function previewNewCover(input) {
        const preview = document.getElementById('new-cover-preview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.querySelector('img').src = e.target.result;
                preview.classList.remove('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function previewNewGallery(input) {
        const preview = document.getElementById('new-gallery-preview');
        preview.innerHTML = '';

        if (input.files && input.files.length > 0) {
            preview.classList.remove('hidden');

            for (let i = 0; i < input.files.length; i++) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'relative';
                    div.innerHTML = `
                        <img src="${e.target.result}" class="w-full h-24 object-cover rounded-lg">
                        <span class="absolute top-1 right-1 bg-green-500 text-white text-xs px-1 rounded">New</span>
                    `;
                    preview.appendChild(div);
                }
                reader.readAsDataURL(input.files[i]);
            }
        }
    }
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
