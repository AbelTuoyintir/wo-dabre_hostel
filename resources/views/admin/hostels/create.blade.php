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
                            {{ in_array($amenity->id, old('amenities', [])) ? 'checked' : '' }}
                        >
                        <span class="text-sm text-gray-700">{{ $amenity->name }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <!-- Hostel Images -->
        <div class="bg-white rounded-lg shadow border p-6">
            <h3 class="text-lg font-semibold mb-4 text-gray-800 flex items-center">
                <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                Hostel Media Gallery
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Cover Image -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Cover Image *</label>

                    <div class="relative group cursor-pointer border-2 border-dashed border-gray-300 rounded-xl p-6 bg-gray-50 hover:bg-indigo-50/30 hover:border-indigo-500 transition-all duration-300 text-center" id="cover-dropzone">
                        <input type="file"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                               id="cover_image" name="cover_image" accept="image/*" onchange="previewCoverImage(this)">

                        <div class="space-y-2" id="cover-prompt">
                            <div class="mx-auto w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                            </div>
                            <div class="text-sm text-gray-600">
                                <span class="font-semibold text-indigo-600 hover:text-indigo-500">Click to upload cover</span> or drag and drop
                            </div>
                            <p class="text-xs text-gray-500">PNG, JPG, JPEG up to 10MB</p>
                        </div>

                        <!-- Cover Image Preview -->
                        <div id="cover-image-preview" class="hidden relative inline-block mx-auto max-w-full">
                            <!-- Previews will show here -->
                        </div>
                    </div>

                    @error('cover_image')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Gallery Images -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Gallery Media (Images &amp; Videos)</label>

                    <div class="relative group cursor-pointer border-2 border-dashed border-gray-300 rounded-xl p-6 bg-gray-50 hover:bg-indigo-50/30 hover:border-indigo-500 transition-all duration-300 text-center" id="gallery-dropzone">
                        <input type="file"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                               id="gallery_images" name="gallery_images[]" accept="image/*,video/*" multiple onchange="previewGalleryImages(this)">

                        <div class="space-y-2" id="gallery-prompt">
                            <div class="mx-auto w-12 h-12 rounded-full bg-pink-100 flex items-center justify-center text-pink-600 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h18M3 16h18"></path>
                                </svg>
                            </div>
                            <div class="text-sm text-gray-600">
                                <span class="font-semibold text-pink-600 hover:text-pink-500">Click to upload multiple</span> or drag and drop
                            </div>
                            <p class="text-xs text-gray-500">Images and MP4/WebM videos up to 100MB each (max 5)</p>
                        </div>
                    </div>

                    <!-- Gallery Preview Grid -->
                    <div id="gallery-preview" class="hidden space-y-2">
                        <p class="text-xs font-semibold text-gray-600">Selected Gallery Files:</p>
                        <div id="gallery-preview-grid" class="grid grid-cols-2 md:grid-cols-3 gap-3"></div>
                    </div>

                    @error('gallery_images.*')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
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
        const prompt = document.getElementById('cover-prompt');
        previewContainer.innerHTML = '';

        if (input.files && input.files[0]) {
            const file = input.files[0];
            if (file.size > 10 * 1024 * 1024) {
                alert("Cover image must not exceed 10MB!");
                input.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                previewContainer.innerHTML = `
                    <div class="relative inline-block mx-auto">
                        <img src="${e.target.result}" class="max-h-48 rounded-lg shadow-sm border object-cover" alt="Cover preview">
                        <button type="button" onclick="removeCoverImage(event)" class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center shadow-md transition-colors z-20">
                            &times;
                        </button>
                    </div>
                `;
                previewContainer.classList.remove('hidden');
                prompt.classList.add('hidden');
            }
            reader.readAsDataURL(file);
        } else {
            previewContainer.classList.add('hidden');
            prompt.classList.remove('hidden');
        }
    }

    function removeCoverImage(event) {
        if (event) {
            event.stopPropagation();
            event.preventDefault();
        }
        const input = document.getElementById('cover_image');
        input.value = '';
        const previewContainer = document.getElementById('cover-image-preview');
        const prompt = document.getElementById('cover-prompt');
        previewContainer.innerHTML = '';
        previewContainer.classList.add('hidden');
        prompt.classList.remove('hidden');
    }

    // Preview Gallery Images/Videos
    function previewGalleryImages(input) {
        const previewGrid = document.getElementById('gallery-preview-grid');
        const previewContainer = document.getElementById('gallery-preview');
        const prompt = document.getElementById('gallery-prompt');
        previewGrid.innerHTML = '';

        if (input.files && input.files.length > 0) {
            if (input.files.length > 5) {
                alert("Maximum 5 gallery files allowed! Keeping first 5.");
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
                        removeGalleryImage(index);
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

    function removeGalleryImage(index) {
        const input = document.getElementById('gallery_images');
        const dt = new DataTransfer();
        const files = input.files;

        const fileArray = Array.from(files);
        fileArray.splice(index, 1);

        fileArray.forEach(file => {
            dt.items.add(file);
        });

        input.files = dt.files;

        previewGalleryImages(input);
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
@endpush

@push('styles')
<style>
    #gallery-preview-grid > div {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    #gallery-preview-grid > div:hover {
        transform: scale(1.03);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        z-index: 10;
    }
</style>
@endpush
@endsection
