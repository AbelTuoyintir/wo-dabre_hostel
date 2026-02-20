@extends('layouts.hostelmanage')

@section('title', 'Edit Room')
@section('page-title', 'Edit Room: ' . $room->number)

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-4 bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex items-center">
            <a href="{{ route('hostel-manager.rooms') }}" class="text-gray-500 hover:text-gray-700 mr-3">
                <i class="fas fa-arrow-left text-xs"></i>
            </a>
            <div>
                <h2 class="text-sm font-semibold text-gray-800">Edit Room: {{ $room->number }}</h2>
                <p class="text-xs text-gray-500">{{ $room->hostel->name ?? 'N/A' }}</p>
            </div>
            <div class="ml-auto">
                <span class="text-[10px] bg-blue-100 text-blue-700 px-2 py-1 rounded-full">
                    ID: #{{ $room->id }}
                </span>
            </div>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <form action="{{ route('hostel-manager.rooms.update', $room) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <!-- Basic Information -->
                <div>
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
                            <input type="text" name="number" value="{{ old('number', $room->number) }}" 
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
                            <input type="number" name="floor" value="{{ old('floor', $room->floor) }}" 
                                   class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @error('floor') border-red-500 @enderror"
                                   placeholder="e.g., 1, 2, 3">
                            @error('floor')
                                <p class="mt-1 text-[10px] text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Hostel (if managing multiple) -->
                        @if($hostels && $hostels->count() > 1)
                        <div class="md:col-span-2">
                            <label class="block text-[10px] font-medium text-gray-500 uppercase mb-1">
                                Hostel <span class="text-red-500">*</span>
                            </label>
                            <select name="hostel_id" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @error('hostel_id') border-red-500 @enderror" required>
                                <option value="">Select Hostel</option>
                                @foreach($hostels as $hostel)
                                    <option value="{{ $hostel->id }}" {{ old('hostel_id', $room->hostel_id) == $hostel->id ? 'selected' : '' }}>
                                        {{ $hostel->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('hostel_id')
                                <p class="mt-1 text-[10px] text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        @else
                            <input type="hidden" name="hostel_id" value="{{ $room->hostel_id }}">
                        @endif
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
                            <input type="number" name="capacity" value="{{ old('capacity', $room->capacity) }}" min="1"
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
                                <input type="number" name="price_per_semester" value="{{ old('price_per_semester', $room->price_per_semester) }}" step="0.01" min="0"
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
                            <input type="number" name="size_sqm" value="{{ old('size_sqm', $room->size_sqm) }}" step="0.01" min="0"
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
                                <option value="any" {{ old('gender', $room->gender) == 'any' ? 'selected' : '' }}>Any Gender</option>
                                <option value="male" {{ old('gender', $room->gender) == 'male' ? 'selected' : '' }}>Male Only</option>
                                <option value="female" {{ old('gender', $room->gender) == 'female' ? 'selected' : '' }}>Female Only</option>
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
                                <option value="street" {{ old('window_type', $room->window_type) == 'street' ? 'selected' : '' }}>Street View</option>
                                <option value="courtyard" {{ old('window_type', $room->window_type) == 'courtyard' ? 'selected' : '' }}>Courtyard</option>
                                <option value="garden" {{ old('window_type', $room->window_type) == 'garden' ? 'selected' : '' }}>Garden</option>
                                <option value="none" {{ old('window_type', $room->window_type) == 'none' ? 'selected' : '' }}>No Window</option>
                            </select>
                            @error('window_type')
                                <p class="mt-1 text-[10px] text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Checkbox Features -->
                    <div class="flex flex-wrap gap-4 mt-4">
                        <label class="flex items-center">
                            <input type="checkbox" name="furnished" value="1" {{ old('furnished', $room->furnished) ? 'checked' : '' }}
                                   class="w-3.5 h-3.5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span class="ml-2 text-xs text-gray-700">Furnished</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="private_bathroom" value="1" {{ old('private_bathroom', $room->private_bathroom) ? 'checked' : '' }}
                                   class="w-3.5 h-3.5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span class="ml-2 text-xs text-gray-700">Private Bathroom</span>
                        </label>
                    </div>
                </div>

                <!-- Status & Current Occupancy -->
                <div class="pt-4 border-t border-gray-100">
                    <h3 class="text-xs font-semibold text-gray-700 uppercase tracking-wider mb-3 flex items-center">
                        <i class="fas fa-chart-line text-yellow-500 mr-1.5 text-xs"></i>
                        Status & Occupancy
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Status -->
                        <div>
                            <label class="block text-[10px] font-medium text-gray-500 uppercase mb-1">
                                Room Status <span class="text-red-500">*</span>
                            </label>
                            <select name="status" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 @error('status') border-red-500 @enderror" required>
                                <option value="available" {{ old('status', $room->status) == 'available' ? 'selected' : '' }}>Available</option>
                                <option value="occupied" {{ old('status', $room->status) == 'occupied' ? 'selected' : '' }}>Occupied</option>
                                <option value="maintenance" {{ old('status', $room->status) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-[10px] text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Current Occupancy (Read Only) -->
                        <div>
                            <label class="block text-[10px] font-medium text-gray-500 uppercase mb-1">
                                Current Occupancy
                            </label>
                            <div class="flex items-center">
                                <input type="number" value="{{ $room->current_occupancy }}" 
                                       class="w-full px-3 py-2 text-xs bg-gray-50 border border-gray-300 rounded-lg"
                                       readonly disabled>
                                <span class="ml-2 text-xs text-gray-500">/ {{ $room->capacity }}</span>
                            </div>
                            <p class="mt-1 text-[10px] text-gray-400">
                                <i class="fas fa-info-circle mr-1"></i> Occupancy is managed automatically
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
                                  placeholder="Enter room description, special features, notes...">{{ old('description', $room->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-[10px] text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-[10px] text-gray-400">Maximum 1000 characters</p>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
                    <button type="button" onclick="confirmDelete()" 
                            class="text-red-600 hover:text-red-800 text-xs font-medium flex items-center">
                        <i class="fas fa-trash-alt mr-1.5"></i>
                        Delete Room
                    </button>
                    
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('hostel-manager.rooms') }}" 
                           class="px-4 py-2 border border-gray-300 rounded-lg text-xs font-medium text-gray-700 hover:bg-gray-50 transition">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-xs font-medium rounded-lg transition flex items-center">
                            <i class="fas fa-save mr-1.5"></i>
                            Update Room
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Danger Zone - Delete Confirmation (Hidden Form) -->
    <form id="delete-form" action="{{ route('hostel-manager.rooms.destroy', $room) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete() {
    Swal.fire({
        title: 'Delete Room?',
        html: `
            <p class="text-sm text-gray-600 mb-3">You are about to delete room <strong>{{ $room->number }}</strong></p>
            <p class="text-xs text-red-500">This action cannot be undone!</p>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, delete it',
        cancelButtonText: 'Cancel',
        reverseButtons: true,
        customClass: {
            popup: 'rounded-xl p-4',
            confirmButton: 'bg-red-500 hover:bg-red-600 text-white text-xs font-medium px-4 py-2 rounded-lg',
            cancelButton: 'bg-gray-500 hover:bg-gray-600 text-white text-xs font-medium px-4 py-2 rounded-lg'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form').submit();
        }
    });
}

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

// Initialize any tooltips or additional functionality
document.addEventListener('DOMContentLoaded', function() {
    // Add any initialization code here
});
</script>
@endpush