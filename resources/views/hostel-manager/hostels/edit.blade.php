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
                        <i class="fas fa-image text-pink-600 mr-2"></i> Images
                    </h3>
                    
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Featured Image
                            </label>
                            @if($hostel->featured_image)
                                <div class="mb-3">
                                    <p class="text-sm text-gray-600 mb-2">Current Featured Image:</p>
                                    <img src="{{ Storage::url($hostel->featured_image) }}" alt="Current featured image" class="w-48 h-32 object-cover rounded-lg border">
                                </div>
                            @endif
                            <input type="file" name="featured_image" accept="image/*"
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            <p class="text-xs text-gray-500 mt-1">Recommended size: 1200x800px. Max 5MB.</p>
                            @error('featured_image')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Gallery Images
                            </label>
                            @if($hostel->images && $hostel->images->count() > 0)
                                <div class="mb-3">
                                    <p class="text-sm text-gray-600 mb-2">Current Gallery Images:</p>
                                    <div class="grid grid-cols-3 gap-3">
                                        @foreach($hostel->images as $image)
                                            <div class="relative group">
                                                <img src="{{ Storage::url($image->image_path) }}" alt="Gallery image" class="w-full h-24 object-cover rounded-lg border">
                                                <button type="button" onclick="confirmDeleteImage({{ $image->id }})" 
                                                    class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition">
                                                    <i class="fas fa-times text-xs"></i>
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            <input type="file" name="images[]" accept="image/*" multiple
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            <p class="text-xs text-gray-500 mt-1">You can select multiple images. Max 5MB each.</p>
                            @error('images.*')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
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

    <!-- Delete Image Confirmation Script -->
    <script>
        function confirmDeleteImage(imageId) {
            Swal.fire({
                title: 'Remove Image?',
                text: "This image will be permanently deleted.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, remove it',
                cancelButtonText: 'Cancel',
                customClass: {
                    popup: 'rounded-xl',
                    confirmButton: 'bg-red-500 hover:bg-red-600',
                    cancelButton: 'bg-gray-500 hover:bg-gray-600'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Create form and submit to delete image
                    const form = document.createElement('form');
                    form.method = 'POST';
form.action = '{{ route("hostel-manager.hostels.update", $hostel) }}';
                    form.innerHTML = '@csrf @method("DELETE")';
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'image_id';
                    input.value = imageId;
                    form.appendChild(input);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>
</div>
@endsection