@extends('layouts.hostelmanage')

@section('title', 'Edit Hostel')
@section('page-title', 'Edit Hostel')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-white">Edit Hostel Information</h2>
                    <p class="text-blue-100 text-sm mt-1">Update your hostel details</p>
                </div>
                <div class="bg-white/20 rounded-lg px-3 py-1">
                    <i class="fas fa-building text-white text-sm"></i>
                    <span class="text-white text-sm ml-2">ID: {{ $hostel->id }}</span>
                </div>
            </div>
        </div>

        <form action="{{ route('hostel-manager.hostels.update', $hostel) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="p-6 space-y-6">
                <!-- Basic Information Section -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">
                        <i class="fas fa-info-circle text-blue-600 mr-2"></i> Basic Information
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Hostel Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name', $hostel->name) }}"
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition @error('name') border-red-500 @enderror"
                                placeholder="Enter hostel name">
                            @error('name')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Description
                            </label>
                            <textarea name="description" rows="5"
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition @error('description') border-red-500 @enderror"
                                placeholder="Describe the hostel, amenities, rules, etc.">{{ old('description', $hostel->description) }}</textarea>
                            @error('description')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Location Section -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">
                        <i class="fas fa-map-marker-alt text-green-600 mr-2"></i> Location Details
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Address <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="address" value="{{ old('address', $hostel->address) }}"
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition @error('address') border-red-500 @enderror"
                                placeholder="Street address">
                            @error('address')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                City <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="city" value="{{ old('city', $hostel->city) }}"
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition @error('city') border-red-500 @enderror"
                                placeholder="City">
                            @error('city')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Region <span class="text-red-500">*</span>
                            </label>
                            <select name="region" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition @error('region') border-red-500 @enderror">
                                <option value="">Select Region</option>
                                <option value="Greater Accra" {{ old('region', $hostel->region) == 'Greater Accra' ? 'selected' : '' }}>Greater Accra</option>
                                <option value="Ashanti" {{ old('region', $hostel->region) == 'Ashanti' ? 'selected' : '' }}>Ashanti</option>
                                <option value="Western" {{ old('region', $hostel->region) == 'Western' ? 'selected' : '' }}>Western</option>
                                <option value="Central" {{ old('region', $hostel->region) == 'Central' ? 'selected' : '' }}>Central</option>
                                <option value="Eastern" {{ old('region', $hostel->region) == 'Eastern' ? 'selected' : '' }}>Eastern</option>
                                <option value="Volta" {{ old('region', $hostel->region) == 'Volta' ? 'selected' : '' }}>Volta</option>
                                <option value="Northern" {{ old('region', $hostel->region) == 'Northern' ? 'selected' : '' }}>Northern</option>
                                <option value="Upper East" {{ old('region', $hostel->region) == 'Upper East' ? 'selected' : '' }}>Upper East</option>
                                <option value="Upper West" {{ old('region', $hostel->region) == 'Upper West' ? 'selected' : '' }}>Upper West</option>
                            </select>
                            @error('region')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Latitude
                            </label>
                            <input type="text" name="latitude" value="{{ old('latitude', $hostel->latitude) }}"
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                placeholder="Latitude (optional)">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Longitude
                            </label>
                            <input type="text" name="longitude" value="{{ old('longitude', $hostel->longitude) }}"
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                placeholder="Longitude (optional)">
                        </div>
                    </div>
                </div>

                <!-- Contact Information Section -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">
                        <i class="fas fa-phone-alt text-purple-600 mr-2"></i> Contact Information
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Phone Number 1
                            </label>
                            <input type="text" name="phone_1" value="{{ old('phone_1', $hostel->phone_1 ?? '') }}"
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                placeholder="Primary contact number">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Phone Number 2
                            </label>
                            <input type="text" name="phone_2" value="{{ old('phone_2', $hostel->phone_2 ?? '') }}"
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                placeholder="Secondary contact number">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Email Address
                            </label>
                            <input type="email" name="contact_email" value="{{ old('contact_email', $hostel->contact_email ?? '') }}"
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                placeholder="contact@hostel.com">
                        </div>
                    </div>
                </div>

                <!-- Images Section -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">
                        <i class="fas fa-image text-indigo-600 mr-2"></i> Hostel Media Gallery
                    </h3>

                    <!-- Hidden inputs container for deleted current images -->
                    <div id="removed-images-container"></div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Featured Image Upload -->
                        <div class="space-y-3">
                            <label class="block text-sm font-semibold text-gray-700">
                                Featured Image (Main Cover)
                            </label>

                            <!-- Current Featured Image -->
                            @if($hostel->featured_image)
                                <div id="current-featured-box" class="p-3 border rounded-xl bg-gray-50 flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <img src="{{ image_url($hostel->featured_image) }}" alt="Current featured" class="w-16 h-12 object-cover rounded-lg border">
                                        <div>
                                            <p class="text-xs font-semibold text-gray-800">Current Featured Image</p>
                                            <p class="text-[10px] text-gray-500">Will be replaced upon new upload</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="relative group cursor-pointer border-2 border-dashed border-gray-300 rounded-xl p-6 bg-gray-50 hover:bg-indigo-50/30 hover:border-indigo-500 transition-all duration-300 text-center" id="featured-dropzone">
                                <input type="file"
                                       class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                       id="featured_image" name="featured_image" accept="image/*" onchange="previewFeaturedImage(event)">

                                <div class="space-y-2" id="featured-prompt">
                                    <div class="mx-auto w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 group-hover:scale-110 transition-transform duration-300">
                                        <i class="fas fa-upload text-sm"></i>
                                    </div>
                                    <div class="text-xs text-gray-600">
                                        <span class="font-semibold text-indigo-600 hover:text-indigo-500">Upload new cover</span> or drag &amp; drop
                                    </div>
                                    <p class="text-[10px] text-gray-500">PNG, JPG, JPEG up to 5MB</p>
                                </div>

                                <!-- Featured Image Preview -->
                                <div id="featuredImagePreview" class="hidden relative inline-block mx-auto max-w-full">
                                    <img id="featuredImagePreviewImg" src="" alt="Featured Preview" class="max-h-48 rounded-lg shadow-sm border border-gray-200 object-cover">
                                    <button type="button" onclick="removeFeaturedImage(event)" class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center shadow-md transition-colors z-20">
                                        &times;
                                    </button>
                                </div>
                            </div>

                            @error('featured_image')
                                <p class="text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Gallery Files Upload -->
                        <div class="space-y-3">
                            <label class="block text-sm font-semibold text-gray-700">
                                Gallery Media (Images &amp; Videos)
                            </label>

                            <!-- Current Gallery Images & Videos -->
                            @if($hostel->galleryImages->count() > 0)
                                <div class="space-y-2">
                                    <p class="text-xs font-semibold text-gray-600">Current Gallery Media ({{ $hostel->galleryImages->count() }}):</p>
                                    <div class="grid grid-cols-4 gap-2">
                                        @foreach($hostel->galleryImages as $image)
                                            <div class="relative group border rounded-lg overflow-hidden shadow-sm aspect-video flex items-center justify-center bg-black" data-gallery-id="{{ $image->id }}">
                                                @if($image->media_kind == 'video')
                                                    <video class="w-full h-full object-cover" muted preload="metadata">
                                                        <source src="{{ image_url($image->image_path) }}">
                                                    </video>
                                                    <div class="absolute inset-0 flex items-center justify-center bg-black/30">
                                                        <i class="fas fa-play text-white drop-shadow"></i>
                                                    </div>
                                                @else
                                                    <img src="{{ image_url($image->image_path) }}" alt="Gallery" class="w-full h-full object-cover">
                                                @endif
                                                <button type="button" onclick="confirmDeleteImage({{ $image->id }})" 
                                                    class="absolute top-1 right-1 bg-red-500 hover:bg-red-600 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition shadow-md z-20">
                                                    <i class="fas fa-times text-xs"></i>
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <div class="relative group cursor-pointer border-2 border-dashed border-gray-300 rounded-xl p-6 bg-gray-50 hover:bg-indigo-50/30 hover:border-indigo-500 transition-all duration-300 text-center" id="gallery-dropzone">
                                <input type="file"
                                       class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                       id="images" name="images[]" accept="image/*,video/*" multiple onchange="previewGalleryFiles(event)">

                                <div class="space-y-2" id="gallery-prompt">
                                    <div class="mx-auto w-10 h-10 rounded-full bg-pink-100 flex items-center justify-center text-pink-600 group-hover:scale-110 transition-transform duration-300">
                                        <i class="fas fa-images text-sm"></i>
                                    </div>
                                    <div class="text-xs text-gray-600">
                                        <span class="font-semibold text-pink-600 hover:text-pink-500">Upload new gallery files</span> or drag &amp; drop
                                    </div>
                                    <p class="text-[10px] text-gray-500">Images and MP4/WebM videos up to 100MB each (max 5)</p>
                                </div>
                            </div>

                            <!-- Gallery Preview Grid for NEW uploads -->
                            <div id="galleryPreview" class="hidden space-y-2">
                                <p class="text-xs font-semibold text-gray-600">New Gallery Files to Add:</p>
                                <div id="galleryPreviewContainer" class="grid grid-cols-3 gap-2"></div>
                            </div>

                            @error('images.*')
                                <p class="text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Status Section -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">
                        <i class="fas fa-toggle-on text-yellow-600 mr-2"></i> Status
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Hostel Status
                            </label>
                            <select name="status" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                                <option value="active" {{ old('status', $hostel->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $hostel->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="maintenance" {{ old('status', $hostel->status) == 'maintenance' ? 'selected' : '' }}>Under Maintenance</option>
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Set to Inactive if the hostel is temporarily unavailable</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex flex-col sm:flex-row justify-end gap-3">
                <a href="{{ route('hostel-manager.hostels.show', $hostel) }}" 
                   class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition text-center">
                    <i class="fas fa-times mr-2"></i> Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg font-semibold hover:shadow-lg transition">
                    <i class="fas fa-save mr-2"></i> Update Hostel
                </button>
            </div>
        </form>
    </div>

    <!-- Image Actions Script -->
    <script>
        // Track removed images
        let removedImages = [];

        function confirmDeleteImage(imageId) {
            Swal.fire({
                title: 'Mark image for removal?',
                text: "This image will be deleted once you click 'Update Hostel' to save your changes.",
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
                    // Add to removed images array
                    removedImages.push(imageId);

                    // Render hidden input
                    const container = document.getElementById('removed-images-container');
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'removed_images[]';
                    input.value = imageId;
                    container.appendChild(input);

                    // Dim the preview box in UI
                    const imgBox = document.querySelector(`[data-gallery-id="${imageId}"]`);
                    if (imgBox) {
                        imgBox.style.opacity = '0.3';
                        imgBox.style.pointerEvents = 'none';

                        // Add overlay badge
                        const badge = document.createElement('div');
                        badge.className = 'absolute inset-0 flex items-center justify-center bg-red-500/20 text-white font-bold text-[10px] uppercase';
                        badge.innerHTML = '<span>Marked for removal</span>';
                        imgBox.appendChild(badge);
                    }

                    Swal.fire({
                        title: 'Marked for Removal!',
                        text: 'Click Update Hostel at the bottom to apply changes.',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false,
                        customClass: { popup: 'rounded-xl' }
                    });
                }
            });
        }

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

        // Preview Replacement for Featured Image
        function previewFeaturedImage(event) {
            const file = event.target.files[0];
            const previewContainer = document.getElementById('featuredImagePreview');
            const previewImg = document.getElementById('featuredImagePreviewImg');
            const prompt = document.getElementById('featured-prompt');
            const currentBox = document.getElementById('current-featured-box');

            if (file) {
                if (file.size > 5 * 1024 * 1024) {
                    Swal.fire({
                        title: 'File Too Large',
                        text: 'Featured image must not exceed 5MB!',
                        icon: 'warning',
                        confirmButtonColor: '#4f46e5',
                        customClass: { popup: 'rounded-xl' }
                    });
                    event.target.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    previewContainer.classList.remove('hidden');
                    prompt.classList.add('hidden');
                    if (currentBox) {
                        currentBox.style.opacity = '0.5';
                    }
                }
                reader.readAsDataURL(file);
            } else {
                previewContainer.classList.add('hidden');
                prompt.classList.remove('hidden');
                if (currentBox) {
                    currentBox.style.opacity = '1';
                }
            }
        }

        function removeFeaturedImage(event) {
            if (event) {
                event.stopPropagation();
                event.preventDefault();
            }
            const input = document.getElementById('featured_image');
            const previewContainer = document.getElementById('featuredImagePreview');
            const previewImg = document.getElementById('featuredImagePreviewImg');
            const prompt = document.getElementById('featured-prompt');
            const currentBox = document.getElementById('current-featured-box');

            input.value = '';
            previewImg.src = '';
            previewContainer.classList.add('hidden');
            prompt.classList.remove('hidden');
            if (currentBox) {
                currentBox.style.opacity = '1';
            }
        }

        // Preview Gallery Files for NEW uploads
        function previewGalleryFiles(event) {
            const files = event.target.files;
            const previewContainer = document.getElementById('galleryPreview');
            const galleryContainer = document.getElementById('galleryPreviewContainer');
            const prompt = document.getElementById('gallery-prompt');

            galleryContainer.innerHTML = '';

            if (files.length > 0) {
                if (files.length > 5) {
                    Swal.fire({
                        title: 'Limit Exceeded',
                        text: 'You can select up to 5 gallery files maximum! Extra files have been truncated.',
                        icon: 'warning',
                        confirmButtonColor: '#4f46e5',
                        customClass: { popup: 'rounded-xl' }
                    });
                    const dt = new DataTransfer();
                    Array.from(files).slice(0, 5).forEach(f => dt.items.add(f));
                    event.target.files = dt.files;
                }

                previewContainer.classList.remove('hidden');
                prompt.classList.add('hidden');

                Array.from(event.target.files).forEach((file, index) => {
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
                            removeGalleryFile(index);
                        };
                        wrapper.appendChild(removeBtn);

                        galleryContainer.appendChild(wrapper);
                    };
                    reader.readAsDataURL(file);
                });
            } else {
                previewContainer.classList.add('hidden');
                prompt.classList.remove('hidden');
            }
        }

        function removeGalleryFile(index) {
            const input = document.getElementById('images');
            const dt = new DataTransfer();
            const files = input.files;

            const fileArray = Array.from(files);
            fileArray.splice(index, 1);

            fileArray.forEach(file => {
                dt.items.add(file);
            });

            input.files = dt.files;

            const event = new Event('change');
            input.dispatchEvent(event);
        }

        // Dropzones enhancements
        ['featured-dropzone', 'gallery-dropzone'].forEach(id => {
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
</div>
@endsection