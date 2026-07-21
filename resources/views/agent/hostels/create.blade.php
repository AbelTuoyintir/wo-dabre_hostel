@extends('layouts.agent')

@section('title', 'Add New Hostel')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h5 class="text-sm font-semibold text-gray-800">Add New Hostel</h5>
        <a href="{{ route('agent.hostels.index') }}" class="inline-flex items-center px-3 py-1.5 bg-gray-200 hover:bg-gray-300 text-gray-700 text-xs font-medium rounded-lg transition-colors duration-200">
            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back
        </a>
    </div>

    <!-- Error Alert -->
    @if($errors->any())
        <div class="mb-4 p-3 bg-red-50 border-l-4 border-red-500 rounded-lg text-red-700 text-xs">
            <strong>Please fix the following errors:</strong>
            <ul class="mt-1 space-y-0.5 list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6">
            <form action="{{ route('agent.hostels.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Left Column -->
                    <div class="space-y-4">
                        <div>
                            <label for="name" class="block text-xs font-medium text-gray-700 mb-1">
                                Hostel Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   class="w-full px-3 py-1.5 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="location" class="block text-xs font-medium text-gray-700 mb-1">
                                Location <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   class="w-full px-3 py-1.5 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('location') border-red-500 @enderror" 
                                   id="location" name="location" value="{{ old('location') }}" required>
                            @error('location')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="address" class="block text-xs font-medium text-gray-700 mb-1">
                                Address <span class="text-red-500">*</span>
                            </label>
                            <textarea class="w-full px-3 py-1.5 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('address') border-red-500 @enderror" 
                                      id="address" name="address" rows="3" required>{{ old('address') }}</textarea>
                            @error('address')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label for="latitude" class="block text-xs font-medium text-gray-700 mb-1">Latitude</label>
                                <input type="number" step="any" 
                                       class="w-full px-3 py-1.5 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('latitude') border-red-500 @enderror" 
                                       id="latitude" name="latitude" value="{{ old('latitude') }}">
                                @error('latitude')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="longitude" class="block text-xs font-medium text-gray-700 mb-1">Longitude</label>
                                <input type="number" step="any" 
                                       class="w-full px-3 py-1.5 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('longitude') border-red-500 @enderror" 
                                       id="longitude" name="longitude" value="{{ old('longitude') }}">
                                @error('longitude')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-4">
                        <div>
                            <label for="description" class="block text-xs font-medium text-gray-700 mb-1">
                                Description <span class="text-red-500">*</span>
                            </label>
                            <textarea class="w-full px-3 py-1.5 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror" 
                                      id="description" name="description" rows="6" required>{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="agent_fee" class="block text-xs font-medium text-gray-700 mb-1">
                                Agent Fee (GHS)
                            </label>
                            <input type="number" step="0.01" min="0" 
                                   class="w-full px-3 py-1.5 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('agent_fee') border-red-500 @enderror" 
                                   id="agent_fee" name="agent_fee" value="{{ old('agent_fee', 100) }}">
                            <p class="mt-1 text-xs text-gray-500">The system keeps 20% and credits you with the remaining 80%.</p>
                            @error('agent_fee')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Images Section -->
                <div class="mt-8">
                    <h4 class="text-xs font-semibold text-gray-800 mb-4 pb-1 border-b border-gray-100 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Media Gallery
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Featured Image Upload -->
                        <div class="space-y-2">
                            <label class="block text-xs font-semibold text-gray-700">
                                Featured Image <span class="text-red-500">*</span>
                            </label>

                            <div class="relative group cursor-pointer border-2 border-dashed border-gray-300 rounded-xl p-6 bg-gray-50 hover:bg-indigo-50/30 hover:border-indigo-500 transition-all duration-300 text-center" id="featured-dropzone">
                                <input type="file"
                                       class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                       id="featured_image" name="featured_image" accept="image/*" required onchange="previewFeaturedImage(event)">

                                <div class="space-y-2" id="featured-prompt">
                                    <div class="mx-auto w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 group-hover:scale-110 transition-transform duration-300">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                    </div>
                                    <div class="text-xs text-gray-600">
                                        <span class="font-semibold text-indigo-600 hover:text-indigo-500">Click to upload</span> or drag and drop
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
                        <div class="space-y-2">
                            <label class="block text-xs font-semibold text-gray-700">
                                Gallery Media (Images &amp; Videos)
                            </label>

                            <div class="relative group cursor-pointer border-2 border-dashed border-gray-300 rounded-xl p-6 bg-gray-50 hover:bg-indigo-50/30 hover:border-indigo-500 transition-all duration-300 text-center" id="gallery-dropzone">
                                <input type="file"
                                       class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                       id="images" name="images[]" accept="image/*,video/*" multiple onchange="previewGalleryFiles(event)">

                                <div class="space-y-2" id="gallery-prompt">
                                    <div class="mx-auto w-12 h-12 rounded-full bg-pink-100 flex items-center justify-center text-pink-600 group-hover:scale-110 transition-transform duration-300">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h18M3 16h18"></path>
                                        </svg>
                                    </div>
                                    <div class="text-xs text-gray-600">
                                        <span class="font-semibold text-pink-600 hover:text-pink-500">Click to upload multiple</span> or drag and drop
                                    </div>
                                    <p class="text-[10px] text-gray-500">Images and MP4/WebM videos up to 100MB each (max 5)</p>
                                </div>
                            </div>

                            <!-- Gallery Preview Grid -->
                            <div id="galleryPreview" class="hidden space-y-2">
                                <p class="text-xs font-semibold text-gray-600">Selected Gallery Files:</p>
                                <div id="galleryPreviewContainer" class="grid grid-cols-2 sm:grid-cols-3 gap-3"></div>
                            </div>

                            @error('images.*')
                                <p class="text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Amenities -->
                <div class="mt-4">
                    <label class="block text-xs font-medium text-gray-700 mb-2">Amenities</label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2">
                        @foreach($amenities as $amenity)
                            <div class="flex items-center space-x-2">
                                <input type="checkbox" 
                                       class="w-3.5 h-3.5 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                       id="amenity_{{ $amenity->id }}" 
                                       name="amenities[]" 
                                       value="{{ $amenity->id }}"
                                       {{ in_array($amenity->id, old('amenities', [])) ? 'checked' : '' }}>
                                <label class="text-xs text-gray-700" for="amenity_{{ $amenity->id }}">
                                    {{ $amenity->name }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                    @error('amenities')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Actions -->
                <div class="mt-6 flex justify-end space-x-2 border-t border-gray-200 pt-4">
                    <button type="reset" class="px-4 py-1.5 bg-gray-200 hover:bg-gray-300 text-gray-700 text-xs font-medium rounded-lg transition-colors duration-200">
                        Reset
                    </button>
                    <button type="submit" class="inline-flex items-center px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg transition-colors duration-200">
                        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                        </svg>
                        Create Hostel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript for Preview -->
<script>
    // Featured Image Preview
    function previewFeaturedImage(event) {
        const file = event.target.files[0];
        const previewContainer = document.getElementById('featuredImagePreview');
        const previewImg = document.getElementById('featuredImagePreviewImg');
        const prompt = document.getElementById('featured-prompt');
        
        if (file) {
            // Client side file size validation
            if (file.size > 5 * 1024 * 1024) {
                alert("Featured image must not exceed 5MB!");
                event.target.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                previewContainer.classList.remove('hidden');
                prompt.classList.add('hidden');
            }
            reader.readAsDataURL(file);
        } else {
            previewContainer.classList.add('hidden');
            prompt.classList.remove('hidden');
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
        
        input.value = '';
        previewImg.src = '';
        previewContainer.classList.add('hidden');
        prompt.classList.remove('hidden');
    }
    
    // Gallery Files Preview (Images & Videos)
    function previewGalleryFiles(event) {
        const files = event.target.files;
        const previewContainer = document.getElementById('galleryPreview');
        const galleryContainer = document.getElementById('galleryPreviewContainer');
        const prompt = document.getElementById('gallery-prompt');
        
        // Clear previous previews
        galleryContainer.innerHTML = '';
        
        if (files.length > 0) {
            if (files.length > 5) {
                alert("You can select up to 5 gallery files maximum!");
                // Keep only first 5
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
                        // Video preview
                        const video = document.createElement('video');
                        video.src = e.target.result;
                        video.className = 'w-full h-full object-cover';
                        video.controls = false;
                        video.muted = true;
                        video.preload = 'metadata';
                        
                        // Add play icon overlay for videos
                        const overlay = document.createElement('div');
                        overlay.className = 'absolute inset-0 flex flex-col items-center justify-center bg-black bg-opacity-40 transition-opacity group-hover:bg-opacity-20';
                        overlay.innerHTML = `
                            <svg class="w-8 h-8 text-white drop-shadow-md" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-[9px] text-white bg-black/60 px-1 py-0.5 rounded mt-1 font-semibold">${(file.size / 1024 / 1024).toFixed(1)}MB</span>
                        `;
                        
                        wrapper.appendChild(video);
                        wrapper.appendChild(overlay);
                        
                        // Auto-play video on hover
                        wrapper.addEventListener('mouseenter', function() {
                            video.play();
                        });
                        wrapper.addEventListener('mouseleave', function() {
                            video.pause();
                            video.currentTime = 0;
                        });
                    } else {
                        // Image preview
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = 'w-full h-full object-cover';

                        // Add image info overlay
                        const overlay = document.createElement('div');
                        overlay.className = 'absolute bottom-0 inset-x-0 bg-gradient-to-t from-black/80 to-transparent p-1.5 text-left opacity-0 group-hover:opacity-100 transition-opacity';
                        overlay.innerHTML = `<span class="text-[9px] text-white font-medium block truncate">${file.name}</span>`;

                        wrapper.appendChild(img);
                        wrapper.appendChild(overlay);
                    }
                    
                    // Add remove button for each preview
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
        
        // Trigger change event to refresh preview
        const event = new Event('change');
        input.dispatchEvent(event);
    }
    
    // Reset button to clear all previews
    document.querySelector('button[type="reset"]').addEventListener('click', function(e) {
        removeFeaturedImage();
        
        // Clear gallery preview
        document.getElementById('galleryPreview').classList.add('hidden');
        document.getElementById('galleryPreviewContainer').innerHTML = '';
        document.getElementById('gallery-prompt').classList.remove('hidden');
    });

    // Dropzone drag-and-drop enhancements
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

<!-- Additional CSS for better styling -->
<style>
    #galleryPreviewContainer > div {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    #galleryPreviewContainer > div:hover {
        transform: scale(1.03);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        z-index: 10;
    }
</style>
@endsection