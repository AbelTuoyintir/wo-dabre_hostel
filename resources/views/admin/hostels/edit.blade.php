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

        <!-- Amenities -->
        <div class="bg-white rounded-lg shadow border p-6">
            <h3 class="text-lg font-semibold mb-4">Amenities</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach($amenities as $amenity)
                    <label class="flex items-center gap-3 p-3 border rounded-md hover:bg-gray-50 cursor-pointer">
                        <input
                            type="checkbox"
                            name="amenities[]"
                            value="{{ $amenity->id }}"
                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
                            {{ ($hostel->amenities->contains($amenity->id)) ? 'checked' : '' }}
                        >
                        <span class="text-sm text-gray-700">{{ $amenity->name }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <!-- Image Management Section -->
        <div class="bg-white rounded-lg shadow border p-6">
            <h3 class="text-lg font-semibold mb-4 text-gray-800 flex items-center">
                <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                Manage Hostel Images
            </h3>

            <!-- Hidden inputs container for removed/primary changed -->
            <div id="removed-images-container"></div>
            <input type="hidden" name="primary_image_id" id="primary_image_id" value="">

            <!-- Current Images Display -->
            @if($hostel->images->count() > 0)
                <div class="mb-8">
                    <h4 class="text-sm font-semibold text-gray-700 mb-4">Current Media Gallery</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4" id="current-images">
                        @foreach($hostel->images()->orderBy('is_primary', 'desc')->orderBy('order')->get() as $image)
                            <div class="relative group border rounded-xl overflow-hidden shadow-sm p-2 transition duration-300 {{ $image->is_primary ? 'bg-blue-50 border-blue-300' : 'border-gray-200 bg-white' }}" data-image-id="{{ $image->id }}">
                                <div class="aspect-video bg-black rounded-lg overflow-hidden flex items-center justify-center relative">
                                    @if($image->media_kind == 'video')
                                        <video class="w-full h-full object-cover" muted preload="metadata">
                                            <source src="{{ image_url($image->image_path) }}">
                                        </video>
                                        <div class="absolute inset-0 flex items-center justify-center bg-black/30">
                                            <i class="fas fa-play text-white drop-shadow"></i>
                                        </div>
                                    @else
                                        <img src="{{ image_url($image->image_path) }}" alt="Hostel image" class="w-full h-full object-cover">
                                    @endif

                                    <!-- Label Badge -->
                                    <div class="absolute top-2 left-2 primary-badge-wrap">
                                        @if($image->is_primary)
                                            <span class="bg-blue-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm">
                                                Primary
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex justify-between items-center mt-3 pt-2 border-t border-gray-100">
                                    <div class="flex gap-1.5">
                                        @if(!$image->is_primary)
                                            <button type="button"
                                                    onclick="setAsPrimary({{ $image->id }})"
                                                    class="text-[10px] bg-blue-100 text-blue-700 px-2 py-1 rounded-md font-semibold hover:bg-blue-200 transition-colors">
                                                Make Primary
                                            </button>
                                        @endif

                                        <button type="button"
                                                onclick="markForRemoval({{ $image->id }})"
                                                class="text-[10px] bg-red-50 text-red-700 px-2 py-1 rounded-md font-semibold hover:bg-red-100 transition-colors">
                                            Remove
                                        </button>
                                    </div>

                                    @if(!$image->is_primary)
                                        <span class="text-[10px] text-gray-400 font-medium">Order: {{ $image->order }}</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Add New Images Section -->
            <div class="border-t border-gray-100 pt-6">
                <h4 class="text-sm font-semibold text-gray-700 mb-4">Add New Media</h4>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- New Cover Image -->
                    <div class="space-y-2">
                        <label class="block text-xs font-semibold text-gray-600">New Cover Image (replaces or adds primary)</label>

                        <div class="relative group cursor-pointer border-2 border-dashed border-gray-300 rounded-xl p-6 bg-gray-50 hover:bg-indigo-50/30 hover:border-indigo-500 transition-all duration-300 text-center" id="cover-dropzone">
                            <input type="file" name="cover_image" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" onchange="previewNewCover(this)">

                            <div class="space-y-2" id="cover-prompt">
                                <div class="mx-auto w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 group-hover:scale-110 transition-transform duration-300">
                                    <i class="fas fa-upload"></i>
                                </div>
                                <div class="text-xs text-gray-600">
                                    <span class="font-semibold text-indigo-600 hover:text-indigo-500">Upload cover image</span> or drag and drop
                                </div>
                                <p class="text-[10px] text-gray-400">PNG, JPG, JPEG up to 10MB</p>
                            </div>

                            <!-- Preview -->
                            <div id="new-cover-preview" class="hidden relative inline-block mx-auto max-w-full">
                                <img src="" class="max-h-36 rounded-lg shadow-sm border object-cover" alt="Cover preview">
                                <button type="button" onclick="removeNewCover(event)" class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white rounded-full w-5 h-5 flex items-center justify-center shadow-md transition-colors z-20">
                                    &times;
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- New Gallery Images -->
                    <div class="space-y-2">
                        <label class="block text-xs font-semibold text-gray-600">Additional Gallery Media (Images &amp; Videos, max 5)</label>

                        <div class="relative group cursor-pointer border-2 border-dashed border-gray-300 rounded-xl p-6 bg-gray-50 hover:bg-indigo-50/30 hover:border-indigo-500 transition-all duration-300 text-center" id="gallery-dropzone">
                            <input type="file" name="gallery_images[]" multiple accept="image/*,video/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" onchange="previewNewGallery(this)">

                            <div class="space-y-2" id="gallery-prompt">
                                <div class="mx-auto w-10 h-10 rounded-full bg-pink-100 flex items-center justify-center text-pink-600 group-hover:scale-110 transition-transform duration-300">
                                    <i class="fas fa-images"></i>
                                </div>
                                <div class="text-xs text-gray-600">
                                    <span class="font-semibold text-pink-600 hover:text-pink-500">Upload multiple gallery files</span> or drag and drop
                                </div>
                                <p class="text-[10px] text-gray-400">Images and MP4/WebM videos up to 100MB each</p>
                            </div>
                        </div>

                        <!-- Preview Grid -->
                        <div id="gallery-preview" class="hidden space-y-2">
                            <p class="text-[11px] font-semibold text-gray-600">New Files to Upload:</p>
                            <div id="new-gallery-preview" class="grid grid-cols-2 gap-2"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            let removedImages = [];

            // Setup play-on-hover for existing videos on load
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('video').forEach(video => {
                    const group = video.closest('.group');
                    if (group) {
                        group.addEventListener('mouseenter', function() {
                            video.play().catch(err => console.log('Video play error:', err));
                        });
                        group.addEventListener('mouseleave', function() {
                            video.pause();
                            video.currentTime = 0;
                        });
                    }
                });
            });

            function markForRemoval(imageId) {
                Swal.fire({
                    title: 'Mark image for removal?',
                    text: "This image will be deleted once you click 'Save Hostel' to save your changes.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, mark for removal',
                    cancelButtonText: 'Cancel',
                    customClass: {
                        popup: 'rounded-xl',
                        confirmButton: 'bg-red-500 hover:bg-red-600',
                        cancelButton: 'bg-gray-500 hover:bg-gray-600'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        removedImages = removedImages.filter(id => String(id) !== String(imageId));
                        removedImages.push(imageId);
                        updateRemovedImagesInput();

                        const imageContainer = document.querySelector(`[data-image-id="${imageId}"]`);
                        if (imageContainer) {
                            imageContainer.style.opacity = '0.3';
                            imageContainer.style.pointerEvents = 'none';
                            imageContainer.classList.add('bg-gray-100');

                            let badge = imageContainer.querySelector('.marked-for-removal-badge');
                            if (!badge) {
                                badge = document.createElement('div');
                                badge.className = 'marked-for-removal-badge absolute inset-0 flex items-center justify-center bg-red-500/20 text-white font-bold text-xs uppercase z-10';
                                badge.innerHTML = '<span>Marked for Removal</span>';
                                imageContainer.appendChild(badge);
                            }
                        }

                        Swal.fire({
                            title: 'Marked for Removal!',
                            text: 'Click Save Hostel at the bottom to apply changes.',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false,
                            customClass: { popup: 'rounded-xl' }
                        });
                    }
                });
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
                document.getElementById('primary_image_id').value = imageId;

                document.querySelectorAll('[data-image-id]').forEach(container => {
                    container.classList.remove('bg-blue-50', 'border-blue-300');
                    const existingBadge = container.querySelector('.primary-badge-wrap');
                    if (existingBadge) {
                        existingBadge.innerHTML = '';
                    }
                });

                const selectedContainer = document.querySelector(`[data-image-id="${imageId}"]`);
                if (selectedContainer) {
                    selectedContainer.classList.add('bg-blue-50', 'border-blue-300');
                    const badgeContainer = selectedContainer.querySelector('.primary-badge-wrap');
                    if (badgeContainer) {
                        badgeContainer.innerHTML = '<span class="bg-blue-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm">Primary</span>';
                    }
                }

                Swal.fire({
                    title: 'Primary Image Selected!',
                    text: 'Primary image updated. Save the form to confirm changes.',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false,
                    customClass: { popup: 'rounded-xl' }
                });
            }

            function previewNewCover(input) {
                const preview = document.getElementById('new-cover-preview');
                const prompt = document.getElementById('cover-prompt');
                const img = preview.querySelector('img');

                if (input.files && input.files[0]) {
                    const file = input.files[0];
                    if (file.size > 10 * 1024 * 1024) {
                        Swal.fire({
                            title: 'File Too Large',
                            text: 'Cover image must not exceed 10MB!',
                            icon: 'warning',
                            confirmButtonColor: '#3b82f6',
                            customClass: { popup: 'rounded-xl' }
                        });
                        input.value = '';
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        img.src = e.target.result;
                        preview.classList.remove('hidden');
                        prompt.classList.add('hidden');
                    }
                    reader.readAsDataURL(file);
                } else {
                    preview.classList.add('hidden');
                    prompt.classList.remove('hidden');
                }
            }

            function removeNewCover(event) {
                if (event) {
                    event.stopPropagation();
                    event.preventDefault();
                }
                const input = document.querySelector('input[name="cover_image"]');
                input.value = '';
                const preview = document.getElementById('new-cover-preview');
                const prompt = document.getElementById('cover-prompt');
                preview.classList.add('hidden');
                prompt.classList.remove('hidden');
            }

            function previewNewGallery(input) {
                const previewContainer = document.getElementById('gallery-preview');
                const previewGrid = document.getElementById('new-gallery-preview');
                const prompt = document.getElementById('gallery-prompt');
                previewGrid.innerHTML = '';

                if (input.files && input.files.length > 0) {
                    if (input.files.length > 5) {
                        Swal.fire({
                            title: 'Limit Exceeded',
                            text: 'Maximum 5 additional gallery files allowed! Extra files have been truncated.',
                            icon: 'warning',
                            confirmButtonColor: '#3b82f6',
                            customClass: { popup: 'rounded-xl' }
                        });
                        const dt = new DataTransfer();
                        Array.from(input.files).slice(0, 5).forEach(f => dt.items.add(f));
                        input.files = dt.files;
                    }

                    previewContainer.classList.remove('hidden');
                    prompt.classList.add('hidden');

                    Array.from(input.files).forEach((file, index) => {
                        const wrapper = document.createElement('div');
                        wrapper.className = 'relative group border rounded-xl overflow-hidden shadow-sm bg-black aspect-video flex items-center justify-center';

                        const reader = new FileReader();
                        reader.onload = function(e) {
                            if (file.type.startsWith('video/')) {
                                const video = document.createElement('video');
                                video.src = e.target.result;
                                video.className = 'w-full h-full object-cover';
                                video.controls = false;
                                video.muted = true;
                                video.preload = 'metadata';

                                const overlay = document.createElement('div');
                                overlay.className = 'absolute inset-0 flex flex-col items-center justify-center bg-black bg-opacity-40 transition-opacity group-hover:bg-opacity-20';
                                overlay.innerHTML = `
                                    <svg class="w-8 h-8 text-white drop-shadow" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-[9px] text-white bg-black/60 px-1 py-0.5 rounded mt-1 font-semibold">${(file.size / 1024 / 1024).toFixed(1)}MB</span>
                                `;

                                wrapper.appendChild(video);
                                wrapper.appendChild(overlay);

                                wrapper.addEventListener('mouseenter', function() {
                                    video.play();
                                });
                                wrapper.addEventListener('mouseleave', function() {
                                    video.pause();
                                    video.currentTime = 0;
                                });
                            } else {
                                const img = document.createElement('img');
                                img.src = e.target.result;
                                img.className = 'w-full h-full object-cover';

                                const overlay = document.createElement('div');
                                overlay.className = 'absolute bottom-0 inset-x-0 bg-gradient-to-t from-black/80 to-transparent p-1.5 text-left opacity-0 group-hover:opacity-100 transition-opacity';
                                overlay.innerHTML = `<span class="text-[9px] text-white font-medium block truncate">${file.name}</span>`;

                                wrapper.appendChild(img);
                                wrapper.appendChild(overlay);
                            }

                            const removeBtn = document.createElement('button');
                            removeBtn.type = 'button';
                            removeBtn.className = 'absolute top-1.5 right-1.5 bg-red-500 hover:bg-red-600 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs shadow-md transition-colors z-20';
                            removeBtn.innerHTML = '&times;';
                            removeBtn.onclick = function(ev) {
                                ev.stopPropagation();
                                ev.preventDefault();
                                removeNewGalleryImage(index);
                            };
                            wrapper.appendChild(removeBtn);

                            previewGrid.appendChild(wrapper);
                        }
                        reader.readAsDataURL(file);
                    });
                } else {
                    previewContainer.classList.add('hidden');
                    prompt.classList.remove('hidden');
                }
            }

            function removeNewGalleryImage(index) {
                const input = document.querySelector('input[name="gallery_images[]"]');
                const dt = new DataTransfer();
                const files = input.files;

                const fileArray = Array.from(files);
                fileArray.splice(index, 1);

                fileArray.forEach(file => {
                    dt.items.add(file);
                });

                input.files = dt.files;

                previewNewGallery(input);
            }

            // Dropzones enhancements
            ['cover-dropzone', 'gallery-dropzone'].forEach(id => {
                const zone = document.getElementById(id);
                if (!zone) return;

                ['dragenter', 'dragover'].forEach(eventName => {
                    zone.addEventListener(eventName, e => {
                        zone.classList.add('border-indigo-500', 'bg-indigo-50/50');
                    }, false);
                });

                ['dragleave', 'drop'].forEach(eventName => {
                    zone.addEventListener(eventName, e => {
                        zone.classList.remove('border-indigo-500', 'bg-indigo-50/50');
                    }, false);
                });
            });
        </script>

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
