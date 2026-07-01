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

                <!-- Images -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <div>
                        <label for="featured_image" class="block text-xs font-medium text-gray-700 mb-1">
                            Featured Image <span class="text-red-500">*</span>
                        </label>
                        <input type="file" 
                               class="w-full px-3 py-1.5 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('featured_image') border-red-500 @enderror" 
                               id="featured_image" name="featured_image" accept="image/*" required>
                        <p class="mt-1 text-xs text-gray-500">Max size: 5MB</p>
                        @error('featured_image')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="images" class="block text-xs font-medium text-gray-700 mb-1">Gallery Images</label>
                        <input type="file" 
                               class="w-full px-3 py-1.5 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('images.*') border-red-500 @enderror" 
                               id="images" name="images[]" accept="image/*" multiple>
                        <p class="mt-1 text-xs text-gray-500">You can select multiple images. Max size each: 5MB</p>
                        @error('images.*')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
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
@endsection