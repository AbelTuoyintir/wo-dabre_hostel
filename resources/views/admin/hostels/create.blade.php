@extends('layouts.app')

@section('title', 'Add New Hostel')
@section('page-title', 'Create New Hostel')

@section('styles')
<style>
    /* Drag and Drop Zone Styles */
    .upload-zone {
        border: 2px dashed #d1d5db;
        border-radius: 12px;
        padding: 2rem;
        text-align: center;
        transition: all 0.3s ease;
        background: #f9fafb;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }

    .upload-zone:hover {
        border-color: #3b82f6;
        background: #eff6ff;
    }

    .upload-zone.dragover {
        border-color: #3b82f6;
        background: #dbeafe;
        transform: scale(1.02);
    }

    .upload-zone.has-file {
        border-color: #10b981;
        background: #ecfdf5;
    }

    /* Image Preview Styles */
    .image-preview {
        position: relative;
        width: 100%;
        max-width: 300px;
        margin: 0 auto;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }

    .image-preview img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        display: block;
    }

    .preview-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .image-preview:hover .preview-overlay {
        opacity: 1;
    }

    .remove-image {
        background: #ef4444;
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        cursor: pointer;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: background 0.2s ease;
    }

    .remove-image:hover {
        background: #dc2626;
    }

    /* Upload Icon Animation */
    .upload-icon {
        width: 64px;
        height: 64px;
        margin: 0 auto 1rem;
        color: #9ca3af;
        transition: all 0.3s ease;
    }

    .upload-zone:hover .upload-icon {
        color: #3b82f6;
        transform: translateY(-5px);
    }

    /* Progress Bar */
    .upload-progress {
        width: 100%;
        height: 4px;
        background: #e5e7eb;
        border-radius: 2px;
        overflow: hidden;
        margin-top: 1rem;
        display: none;
    }

    .upload-progress.active {
        display: block;
    }

    .progress-bar {
        height: 100%;
        background: linear-gradient(90deg, #3b82f6, #8b5cf6);
        width: 0%;
        transition: width 0.3s ease;
    }

    /* File Info */
    .file-info {
        margin-top: 1rem;
        padding: 0.75rem;
        background: white;
        border-radius: 8px;
        display: none;
    }

    .file-info.active {
        display: block;
    }

    /* Multiple Images Grid */
    .images-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 1rem;
        margin-top: 1rem;
    }

    .grid-item {
        position: relative;
        aspect-ratio: 1;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }

    .grid-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .grid-item .remove-btn {
        position: absolute;
        top: 4px;
        right: 4px;
        background: rgba(239, 68, 68, 0.9);
        color: white;
        border: none;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        opacity: 0;
        transition: opacity 0.2s ease;
    }

    .grid-item:hover .remove-btn {
        opacity: 1;
    }

    /* Error State */
    .upload-zone.error {
        border-color: #ef4444;
        background: #fef2f2;
    }

    .error-message {
        color: #ef4444;
        font-size: 0.875rem;
        margin-top: 0.5rem;
        display: none;
    }

    .error-message.active {
        display: block;
    }
</style>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">

    <form action="{{ route('admin.hostels.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6" id="hostelForm">
        @csrf

        <!-- Image Upload Section -->
        <div class="bg-white rounded-lg shadow border p-6">
            <h3 class="text-lg font-semibold mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Hostel Images
            </h3>

            <!-- Single Featured Image Upload -->
            <div class="mb-6">
                <label class="block text-sm font-medium mb-2">Featured Image *</label>

                <div class="upload-zone" id="featuredUploadZone">
                    <input type="file" name="featured_image" id="featuredImage" accept="image/*" class="hidden" required>

                    <div class="upload-content" id="featuredContent">
                        <svg class="upload-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        <p class="text-gray-600 font-medium">Drag and drop your featured image here</p>
                        <p class="text-gray-400 text-sm mt-1">or click to browse</p>
                        <p class="text-gray-400 text-xs mt-2">Supports: JPG, PNG, WebP (Max 5MB)</p>
                    </div>

                    <!-- Image Preview -->
                    <div class="image-preview hidden" id="featuredPreview">
                        <img src="" alt="Preview" id="featuredPreviewImg">
                        <div class="preview-overlay">
                            <button type="button" class="remove-image" onclick="removeFeaturedImage()">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Remove Image
                            </button>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="upload-progress" id="featuredProgress">
                        <div class="progress-bar" id="featuredProgressBar"></div>
                    </div>

                    <!-- File Info -->
                    <div class="file-info" id="featuredFileInfo">
                        <p class="text-sm text-gray-600" id="featuredFileName"></p>
                        <p class="text-xs text-gray-400" id="featuredFileSize"></p>
                    </div>
                </div>

                @error('featured_image')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Gallery Images Upload -->
            <div>
                <label class="block text-sm font-medium mb-2">Gallery Images (Optional)</label>

                <div class="upload-zone" id="galleryUploadZone">
                    <input type="file" name="gallery_images[]" id="galleryImages" accept="image/*" multiple class="hidden">

                    <div class="upload-content" id="galleryContent">
                        <svg class="upload-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <p class="text-gray-600 font-medium">Drag and drop gallery images here</p>
                        <p class="text-gray-400 text-sm mt-1">or click to browse multiple files</p>
                        <p class="text-gray-400 text-xs mt-2">Up to 10 images (Max 5MB each)</p>
                    </div>

                    <!-- Gallery Preview Grid -->
                    <div class="images-grid hidden" id="galleryPreview"></div>

                    <!-- Progress Bar -->
                    <div class="upload-progress" id="galleryProgress">
                        <div class="progress-bar" id="galleryProgressBar"></div>
                    </div>
                </div>

                @error('gallery_images')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
                @error('gallery_images.*')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Basic Information -->
        <div class="bg-white rounded-lg shadow border p-6">
            <h3 class="text-lg font-semibold mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Basic Information
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Hostel Name -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-1">Hostel Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                        class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition @error('name') border-red-500 @enderror"
                        placeholder="Enter hostel name">

                    @error('name')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Location -->
                <div>
                    <label class="block text-sm font-medium mb-1">Location *</label>
                    <select name="location_id" class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition @error('location_id') border-red-500 @enderror">
                        <option value="">Select Location</option>
                        <option value="amamoma">Amamoma</option>
                        <option value="kwaprow">Kwaprow</option>
                        <option value="ayensu">Ayensu</option>
                        <option value="schoolbus_road">Schoolbus_road</option>
                        <option value="oldsite">Oldsite</option>
                    </select>

                    @error('location_id')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Address -->
                <div>
                    <label class="block text-sm font-medium mb-1">Address *</label>
                    <input type="text" name="address" value="{{ old('address') }}"
                        class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition @error('address') border-red-500 @enderror"
                        placeholder="Enter street address">

                    @error('address')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label class="block text-sm font-medium mb-1">Contact Phone</label>
                    <input type="tel" name="contact_phone" value="{{ old('contact_phone') }}"
                        class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                        placeholder="+233 XX XXX XXXX">
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium mb-1">Contact Email</label>
                    <input type="email" name="contact_email" value="{{ old('contact_email') }}"
                        class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                        placeholder="hostel@example.com">
                </div>

                <!-- Manager -->
                <div>
                    <label class="block text-sm font-medium mb-1">Assign Manager</label>
                    <select name="manager_id" class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        <option value="">No Manager</option>
                        @foreach($managers as $manager)
                            <option value="{{ $manager->id }}" {{ old('manager_id') == $manager->id ? 'selected' : '' }}>
                                {{ $manager->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Gender Type -->
                <div>
                    <label class="block text-sm font-medium mb-1">Gender Type *</label>
                    <select name="gender_type" class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition @error('gender_type') border-red-500 @enderror">
                        <option value="">Select Type</option>
                        <option value="male" {{ old('gender_type') == 'male' ? 'selected' : '' }}>Male Only</option>
                        <option value="female" {{ old('gender_type') == 'female' ? 'selected' : '' }}>Female Only</option>
                        <option value="mixed" {{ old('gender_type') == 'mixed' ? 'selected' : '' }}>Mixed</option>
                    </select>

                    @error('gender_type')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Pricing -->
        <div class="bg-white rounded-lg shadow border p-6">
            <h3 class="text-lg font-semibold mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Pricing Information
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium mb-1">Minimum Price (GHS) *</label>
                    <input type="number" name="min_price" value="{{ old('min_price') }}" step="0.01" min="0"
                        class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition @error('min_price') border-red-500 @enderror"
                        placeholder="0.00">

                    @error('min_price')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Maximum Price (GHS) *</label>
                    <input type="number" name="max_price" value="{{ old('max_price') }}" step="0.01" min="0"
                        class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition @error('max_price') border-red-500 @enderror"
                        placeholder="0.00">

                    @error('max_price')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Description -->
        <div class="bg-white rounded-lg shadow border p-6">
            <label class="block text-sm font-medium mb-2 flex items-center">
                <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Description
            </label>
            <textarea name="description" rows="4"
                class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition resize-none"
                placeholder="Describe your hostel, amenities, and unique features...">{{ old('description') }}</textarea>
        </div>

        <!-- Submit Buttons -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('admin.hostels.index') }}"
               class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition">
                Cancel
            </a>
            <button type="submit"
                class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Save Hostel
            </button>
        </div>

    </form>
</div>
@endsection

@section('scripts')
<script>
    // Featured Image Upload
    const featuredZone = document.getElementById('featuredUploadZone');
    const featuredInput = document.getElementById('featuredImage');
    const featuredContent = document.getElementById('featuredContent');
    const featuredPreview = document.getElementById('featuredPreview');
    const featuredPreviewImg = document.getElementById('featuredPreviewImg');
    const featuredFileInfo = document.getElementById('featuredFileInfo');

    // Gallery Images Upload
    const galleryZone = document.getElementById('galleryUploadZone');
    const galleryInput = document.getElementById('galleryImages');
    const galleryContent = document.getElementById('galleryContent');
    const galleryPreview = document.getElementById('galleryPreview');
    let galleryFiles = [];

    // Featured Image Handlers
    featuredZone.addEventListener('click', () => featuredInput.click());

    featuredZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        featuredZone.classList.add('dragover');
    });

    featuredZone.addEventListener('dragleave', () => {
        featuredZone.classList.remove('dragover');
    });

    featuredZone.addEventListener('drop', (e) => {
        e.preventDefault();
        featuredZone.classList.remove('dragover');
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            handleFeaturedImage(files[0]);
        }
    });

    featuredInput.addEventListener('change', (e) => {
        if (e.target.files.length > 0) {
            handleFeaturedImage(e.target.files[0]);
        }
    });

    function handleFeaturedImage(file) {
        // Validate file
        if (!file.type.startsWith('image/')) {
            alert('Please upload an image file');
            return;
        }

        if (file.size > 5 * 1024 * 1024) {
            alert('File size must be less than 5MB');
            return;
        }

        // Show preview
        const reader = new FileReader();
        reader.onload = (e) => {
            featuredPreviewImg.src = e.target.result;
            featuredPreview.classList.remove('hidden');
            featuredContent.classList.add('hidden');
            featuredZone.classList.add('has-file');

            // Show file info
            document.getElementById('featuredFileName').textContent = file.name;
            document.getElementById('featuredFileSize').textContent = formatFileSize(file.size);
            featuredFileInfo.classList.add('active');
        };
        reader.readAsDataURL(file);
    }

    function removeFeaturedImage() {
        featuredInput.value = '';
        featuredPreview.classList.add('hidden');
        featuredContent.classList.remove('hidden');
        featuredZone.classList.remove('has-file');
        featuredFileInfo.classList.remove('active');
    }

    // Gallery Images Handlers
    galleryZone.addEventListener('click', () => galleryInput.click());

    galleryZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        galleryZone.classList.add('dragover');
    });

    galleryZone.addEventListener('dragleave', () => {
        galleryZone.classList.remove('dragover');
    });

    galleryZone.addEventListener('drop', (e) => {
        e.preventDefault();
        galleryZone.classList.remove('dragover');
        const files = Array.from(e.dataTransfer.files);
        handleGalleryImages(files);
    });

    galleryInput.addEventListener('change', (e) => {
        const files = Array.from(e.target.files);
        handleGalleryImages(files);
    });

    function handleGalleryImages(files) {
        // Filter only images and limit to 10
        const imageFiles = files.filter(file => file.type.startsWith('image/'));

        if (galleryFiles.length + imageFiles.length > 10) {
            alert('Maximum 10 images allowed');
            return;
        }

        imageFiles.forEach(file => {
            if (file.size > 5 * 1024 * 1024) {
                alert(`${file.name} is too large. Max size is 5MB`);
                return;
            }

            const reader = new FileReader();
            reader.onload = (e) => {
                const div = document.createElement('div');
                div.className = 'grid-item';
                div.innerHTML = `
                    <img src="${e.target.result}" alt="Gallery Image">
                    <button type="button" class="remove-btn" onclick="removeGalleryImage(this, '${file.name}')">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                `;
                galleryPreview.appendChild(div);
                galleryFiles.push(file);
            };
            reader.readAsDataURL(file);
        });

        if (galleryFiles.length > 0) {
            galleryPreview.classList.remove('hidden');
            galleryContent.classList.add('hidden');
            galleryZone.classList.add('has-file');
        }
    }

    function removeGalleryImage(btn, fileName) {
        const item = btn.parentElement;
        item.remove();
        galleryFiles = galleryFiles.filter(f => f.name !== fileName);

        if (galleryFiles.length === 0) {
            galleryPreview.classList.add('hidden');
            galleryContent.classList.remove('hidden');
            galleryZone.classList.remove('has-file');
            galleryInput.value = '';
        }
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Form validation
    document.getElementById('hostelForm').addEventListener('submit', function(e) {
        const featuredImage = document.getElementById('featuredImage');
        if (!featuredImage.files.length) {
            e.preventDefault();
            featuredZone.classList.add('error');
            alert('Please select a featured image');
            return false;
        }
    });
</script>
@endsection
