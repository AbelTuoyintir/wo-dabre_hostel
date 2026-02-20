@extends('layouts.hostelmanage')

@section('title', 'Add New Room')
@section('page-title', 'Add New Room')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-4 bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex items-center">
            <a href="{{ route('hostel-manager.rooms') }}" class="text-gray-500 hover:text-gray-700 mr-3">
                <i class="fas fa-arrow-left text-xs"></i>
            </a>
            <div>
                <h2 class="text-sm font-semibold text-gray-800">Add New Room</h2>
                <p class="text-xs text-gray-500">Create a new room in your hostel</p>
            </div>
        </div>
    </div>

    <!-- Help Card -->
    @if($hostels && $hostels->count() > 0)
    <div class="mt-4 bg-blue-50 rounded-lg p-3 border border-blue-100">
        <div class="flex items-start">
            <i class="fas fa-lightbulb text-blue-500 text-xs mt-0.5 mr-2"></i>
            <div>
                <h4 class="text-xs font-medium text-blue-800">Quick Tips</h4>
                <ul class="mt-1 text-[10px] text-blue-700 list-disc list-inside">
                    <li>Room numbers must be unique within each hostel</li>
                    <li>Capacity determines maximum number of occupants</li>
                    <li>Price per month is used for all booking calculations</li>
                    <li>You can change room status later from the rooms list</li>
                </ul>
            </div>
        </div>
    </div>
    @endif

    <!-- Create Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <form action="{{ route('hostel-manager.rooms.store') }}" method="POST" class="p-6">
            @csrf
            
            <div class="space-y-6">
                <!-- Hostel Selection (if managing multiple) -->
                @if($hostels && $hostels->count() > 0)
                    @if($hostels->count() > 1)
                    <div>
                        <h3 class="text-xs font-semibold text-gray-700 uppercase tracking-wider mb-3 flex items-center">
                            <i class="fas fa-building text-blue-500 mr-1.5 text-xs"></i>
                            Hostel Selection
                        </h3>
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="block text-[10px] font-medium text-gray-500 uppercase mb-1">
                                    Select Hostel <span class="text-red-500">*</span>
                                </label>
                                <select name="hostel_id" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @error('hostel_id') border-red-500 @enderror" required>
                                    <option value="">Choose a hostel</option>
                                    @foreach($hostels as $hostel)
                                        <option value="{{ $hostel->id }}" {{ old('hostel_id') == $hostel->id ? 'selected' : '' }}>
                                            {{ $hostel->name }} ({{ $hostel->location }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('hostel_id')
                                    <p class="mt-1 text-[10px] text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    @else
                        <!-- Single hostel - hidden input -->
                        <input type="hidden" name="hostel_id" value="{{ $hostels->first()->id }}">
                    @endif
                @else
                    <div class="bg-yellow-50 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded-lg mb-4">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle text-yellow-600 mr-3"></i>
                            <p class="text-xs">You don't have any hostels assigned. Please contact the administrator.</p>
                        </div>
                    </div>
                @endif

                <!-- Basic Information -->
                <div class="pt-2">
                    <h3 class="text-xs font-semibold text-gray-700 uppercase tracking-wider mb-3 flex items-center">
                        <i class="fas fa-info-circle text-blue-500 mr-1.5 text-xs"></i>
                        Basic Information
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Room Number -->
                        <div>
                            <label class="block text-[10px] font-medium text-gray-500 uppercase mb-1">
                                Room Number <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="number" value="{{ old('number') }}" 
                                   class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @error('number') border-red-500 @enderror"
                                   placeholder="e.g., 101, A202" required>
                            @error('number')
                                <p class="mt-1 text-[10px] text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Floor -->
                        <div>
                            <label class="block text-[10px] font-medium text-gray-500 uppercase mb-1">
                                Floor
                            </label>
                            <input type="number" name="floor" value="{{ old('floor') }}" 
                                   class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @error('floor') border-red-500 @enderror"
                                   placeholder="e.g., 1, 2, 3" min="0">
                            @error('floor')
                                <p class="mt-1 text-[10px] text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Room Specifications -->
                <div class="pt-4 border-t border-gray-100">
                    <h3 class="text-xs font-semibold text-gray-700 uppercase tracking-wider mb-3 flex items-center">
                        <i class="fas fa-ruler text-green-500 mr-1.5 text-xs"></i>
                        Room Specifications
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Capacity -->
                        <div>
                            <label class="block text-[10px] font-medium text-gray-500 uppercase mb-1">
                                Capacity <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="capacity" value="{{ old('capacity', 1) }}" min="1"
                                   class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @error('capacity') border-red-500 @enderror"
                                   placeholder="Max persons" required>
                            @error('capacity')
                                <p class="mt-1 text-[10px] text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Price per Month -->
                        <div>
                            <label class="block text-[10px] font-medium text-gray-500 uppercase mb-1">
                                Price/Month (₵) <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-xs">₵</span>
                                <input type="number" name="price_per_month" value="{{ old('price_per_semester') }}" step="0.01" min="0"
                                       class="w-full pl-8 pr-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @error('price_per_month') border-red-500 @enderror"
                                       placeholder="0.00" required>
                            </div>
                            @error('price_per_month')
                                <p class="mt-1 text-[10px] text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Size -->
                        <div>
                            <label class="block text-[10px] font-medium text-gray-500 uppercase mb-1">
                                Size (sqm)
                            </label>
                            <input type="number" name="size_sqm" value="{{ old('size_sqm') }}" step="0.01" min="0"
                                   class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @error('size_sqm') border-red-500 @enderror"
                                   placeholder="e.g., 25.5">
                            @error('size_sqm')
                                <p class="mt-1 text-[10px] text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Room Features -->
                <div class="pt-4 border-t border-gray-100">
                    <h3 class="text-xs font-semibold text-gray-700 uppercase tracking-wider mb-3 flex items-center">
                        <i class="fas fa-couch text-purple-500 mr-1.5 text-xs"></i>
                        Room Features
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Gender Preference -->
                        <div>
                            <label class="block text-[10px] font-medium text-gray-500 uppercase mb-1">
                                Gender Preference <span class="text-red-500">*</span>
                            </label>
                            <select name="gender" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @error('gender') border-red-500 @enderror" required>
                                <option value="any" {{ old('gender') == 'any' ? 'selected' : '' }}>Any Gender</option>
                                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male Only</option>
                                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female Only</option>
                            </select>
                            @error('gender')
                                <p class="mt-1 text-[10px] text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Window Type -->
                        <div>
                            <label class="block text-[10px] font-medium text-gray-500 uppercase mb-1">
                                Window View
                            </label>
                            <select name="window_type" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @error('window_type') border-red-500 @enderror">
                                <option value="">Select View</option>
                                <option value="street" {{ old('window_type') == 'street' ? 'selected' : '' }}>Street View</option>
                                <option value="courtyard" {{ old('window_type') == 'courtyard' ? 'selected' : '' }}>Courtyard</option>
                                <option value="garden" {{ old('window_type') == 'garden' ? 'selected' : '' }}>Garden</option>
                                <option value="none" {{ old('window_type') == 'none' ? 'selected' : '' }}>No Window</option>
                            </select>
                            @error('window_type')
                                <p class="mt-1 text-[10px] text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Checkbox Features -->
                    <div class="flex flex-wrap gap-4 mt-4">
                        <label class="flex items-center">
                            <input type="checkbox" name="furnished" value="1" {{ old('furnished') ? 'checked' : '' }}
                                   class="w-3.5 h-3.5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span class="ml-2 text-xs text-gray-700">Furnished</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="private_bathroom" value="1" {{ old('private_bathroom') ? 'checked' : '' }}
                                   class="w-3.5 h-3.5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span class="ml-2 text-xs text-gray-700">Private Bathroom</span>
                        </label>
                    </div>
                </div>

                <!-- Status -->
                <div class="pt-4 border-t border-gray-100">
                    <h3 class="text-xs font-semibold text-gray-700 uppercase tracking-wider mb-3 flex items-center">
                        <i class="fas fa-toggle-on text-yellow-500 mr-1.5 text-xs"></i>
                        Room Status
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Status -->
                        <div>
                            <label class="block text-[10px] font-medium text-gray-500 uppercase mb-1">
                                Initial Status <span class="text-red-500">*</span>
                            </label>
                            <select name="status" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @error('status') border-red-500 @enderror" required>
                                <option value="available" {{ old('status', 'available') == 'available' ? 'selected' : '' }}>Available</option>
                                <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-[10px] text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-[10px] text-gray-400">
                                <i class="fas fa-info-circle mr-1"></i> "Occupied" status is set automatically when booked
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="pt-4 border-t border-gray-100">
                    <h3 class="text-xs font-semibold text-gray-700 uppercase tracking-wider mb-3 flex items-center">
                        <i class="fas fa-align-left text-gray-500 mr-1.5 text-xs"></i>
                        Description
                    </h3>
                    <div>
                        <textarea name="description" rows="4" 
                                  class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror"
                                  placeholder="Enter room description, special features, notes...">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-[10px] text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-[10px] text-gray-400">Maximum 1000 characters</p>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="pt-4 border-t border-gray-100 flex items-center justify-end space-x-2">
                    <a href="{{ route('hostel-manager.rooms') }}" 
                       class="px-4 py-2 border border-gray-300 rounded-lg text-xs font-medium text-gray-700 hover:bg-gray-50 transition">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-xs font-medium rounded-lg transition flex items-center">
                        <i class="fas fa-plus-circle mr-1.5"></i>
                        Create Room
                    </button>
                </div>
            </div>
        </form>
    </div>

    
</div>
@endsection

@push('scripts')
<script>
// Warn if leaving with unsaved changes
let formChanged = false;
document.querySelectorAll('input, select, textarea').forEach(element => {
    element.addEventListener('change', () => formChanged = true);
    element.addEventListener('keyup', () => formChanged = true);
});

window.addEventListener('beforeunload', function(e) {
    if (formChanged) {
        e.preventDefault();
        e.returnValue = '';
    }
});

// Preview room number format
document.querySelector('input[name="number"]')?.addEventListener('input', function(e) {
    // Optional: Add any room number formatting logic here
});

// Auto-calculate anything if needed
document.querySelector('input[name="capacity"]')?.addEventListener('change', function(e) {
    // Optional: Auto-update related fields
});
</script>
@endpush