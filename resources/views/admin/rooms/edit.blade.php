@extends('layouts.app')

@section('title', 'Edit Room')
@section('page-title', 'Edit Room: ' . $room->number)

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900">Edit Room Information</h3>
                </div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium 
                    @if($room->status == 'available') bg-green-100 text-green-800
                    @elseif($room->status == 'full') bg-yellow-100 text-yellow-800
                    @elseif($room->status == 'maintenance') bg-orange-100 text-orange-800
                    @else bg-gray-100 text-gray-800 @endif">
                    {{ ucfirst($room->status) }}
                </span>
            </div>
        </div>
        
        <form action="{{ route('admin.rooms.update', $room) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <!-- Hostel Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="text-red-500">*</span> Select Hostel
                    </label>
                    <select name="hostel_id" class="w-full lg:w-1/2 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('hostel_id') border-red-500 @enderror" required>
                        <option value="">Choose a hostel</option>
                        @foreach($hostels as $hostel)
                            <option value="{{ $hostel->id }}" {{ old('hostel_id', $room->hostel_id) == $hostel->id ? 'selected' : '' }}>
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
                        <input type="text" name="number" value="{{ old('number', $room->number) }}" 
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('number') border-red-500 @enderror" 
                               placeholder="e.g., 101, A202" required>
                        @error('number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Floor -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Floor</label>
                        <input type="number" name="floor" value="{{ old('floor', $room->floor) }}" 
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
                        <input type="number" name="capacity" value="{{ old('capacity', $room->capacity) }}" min="1" 
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('capacity') border-red-500 @enderror" 
                               placeholder="Maximum number of occupants" required>
                        @error('capacity')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Current Occupancy (Read Only) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Current Occupancy</label>
                        <div class="w-full px-3 py-2 border border-gray-200 bg-gray-50 rounded-md text-gray-700">
                            {{ $room->current_occupancy }} / {{ $room->capacity }} persons
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Current occupancy cannot be edited directly</p>
                    </div>

                    <!-- Size -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Size (sqm)</label>
                        <input type="number" name="size_sqm" value="{{ old('size_sqm', $room->size_sqm) }}" step="0.01" min="1"
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
                            <input type="number" name="price_per_month" value="{{ old('price_per_month', $room->price_per_month) }}" step="0.01" min="0"
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
                            <option value="street" {{ old('window_type', $room->window_type) == 'street' ? 'selected' : '' }}>Street View</option>
                            <option value="courtyard" {{ old('window_type', $room->window_type) == 'courtyard' ? 'selected' : '' }}>Courtyard</option>
                            <option value="garden" {{ old('window_type', $room->window_type) == 'garden' ? 'selected' : '' }}>Garden</option>
                            <option value="none" {{ old('window_type', $room->window_type) == 'none' ? 'selected' : '' }}>No Window</option>
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
                            <option value="any" {{ old('gender', $room->gender) == 'any' ? 'selected' : '' }}>Any Gender</option>
                            <option value="male" {{ old('gender', $room->gender) == 'male' ? 'selected' : '' }}>Male Only</option>
                            <option value="female" {{ old('gender', $room->gender) == 'female' ? 'selected' : '' }}>Female Only</option>
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
                            <option value="available" {{ old('status', $room->status) == 'available' ? 'selected' : '' }}>Available</option>
                            <option value="full" {{ old('status', $room->status) == 'full' ? 'selected' : '' }}>Full</option>
                            <option value="maintenance" {{ old('status', $room->status) == 'maintenance' ? 'selected' : '' }}>Under Maintenance</option>
                            <option value="inactive" {{ old('status', $room->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
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
                                <input type="checkbox" name="furnished" value="1" {{ old('furnished', $room->furnished) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Furnished</span>
                            </label>
                            <span class="text-xs text-gray-500">(Bed, desk, chair, wardrobe)</span>
                        </div>
                        
                        <div class="flex items-center space-x-6">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="private_bathroom" value="1" {{ old('private_bathroom', $room->private_bathroom) ? 'checked' : '' }}
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
                              placeholder="Describe the room, its features, and any special notes...">{{ old('description', $room->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="mt-8 flex items-center justify-between border-t border-gray-200 pt-6">
                <div>
                    <button type="button" onclick="confirmDelete()" 
                            class="px-4 py-2 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Delete Room
                    </button>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.rooms.show', $room) }}" 
                       class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Update Room
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Danger Zone - Only show if no active bookings -->
    @if($room->bookings()->whereIn('status', ['pending', 'confirmed'])->count() == 0)
    <div class="mt-6 bg-red-50 rounded-lg border border-red-200 overflow-hidden">
        <div class="px-6 py-4 bg-red-100 border-b border-red-200">
            <h4 class="text-sm font-medium text-red-800 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                Danger Zone
            </h4>
        </div>
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h5 class="text-sm font-medium text-gray-900">Delete this room</h5>
                    <p class="text-sm text-gray-600">Once deleted, this room cannot be recovered. All booking history will be preserved.</p>
                </div>
                <form id="delete-form" action="{{ route('admin.rooms.destroy', $room) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Are you absolutely sure you want to delete this room? This action cannot be undone.')"
                            class="px-4 py-2 bg-red-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Permanently Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
function confirmDelete() {
    if (confirm('Are you sure you want to delete this room?')) {
        document.getElementById('delete-form').submit();
    }
}
</script>
@endpush
@endsection