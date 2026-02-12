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
        
        <form action="{{ route('admin.rooms.store') }}" method="POST" class="p-6">
            @csrf
            
            <div class="space-y-6">
                <!-- Hostel Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="text-red-500">*</span> Select Hostel
                    </label>
                    <select name="hostel_id" class="w-full lg:w-1/2 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('hostel_id') border-red-500 @enderror" required>
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
                        <label class="block text-sm font-medium text-gray-700 mb-2">Price per Month ($)</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">$</span>
                            </div>
                            <input type="number" name="price_per_month" value="{{ old('price_per_month') }}" step="0.01" min="0"
                                   class="w-full pl-7 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('price_per_month') border-red-500 @enderror" 
                                   placeholder="0.00">
                        </div>
                        @error('price_per_month')
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
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection