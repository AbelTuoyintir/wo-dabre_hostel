@extends('layouts.agent')

@section('title', 'Add New Room')

@section('page-title', 'Add New Room')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Add New Room</h2>
            <p class="text-sm text-gray-500">Create a room listing under one of your managed hostels.</p>
        </div>
        <a href="{{ route('agent.hostels.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-xl transition-colors duration-200">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Hostels
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
        <form action="{{ route('agent.rooms.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Hostel Selection -->
            <div>
                <label for="hostel_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Hostel <span class="text-red-500">*</span>
                </label>
                <select name="hostel_id" id="hostel_id" required
                    class="w-full px-4 py-3 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors @error('hostel_id') border-red-500 @enderror">
                    <option value="">-- Select Hostel --</option>
                    @foreach($hostels as $h)
                        <option value="{{ $h->id }}" {{ (old('hostel_id', $selectedHostel?->id) == $h->id) ? 'selected' : '' }}>
                            {{ $h->name }} ({{ $h->location }})
                        </option>
                    @endforeach
                </select>
                @error('hostel_id')
                    <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Room Number -->
                <div>
                    <label for="room_number" class="block text-sm font-medium text-gray-700 mb-2">
                        Room Number <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="room_number" id="room_number" value="{{ old('room_number') }}" required
                        placeholder="e.g. A101"
                        class="w-full px-4 py-3 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors @error('room_number') border-red-500 @enderror">
                    @error('room_number')
                        <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Capacity -->
                <div>
                    <label for="capacity" class="block text-sm font-medium text-gray-700 mb-2">
                        Capacity (Occupants) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="capacity" id="capacity" min="1" value="{{ old('capacity', 1) }}" required
                        class="w-full px-4 py-3 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors @error('capacity') border-red-500 @enderror">
                    @error('capacity')
                        <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Room Type -->
                <div>
                    <label for="room_type" class="block text-sm font-medium text-gray-700 mb-2">
                        Room Type <span class="text-red-500">*</span>
                    </label>
                    <select name="room_type" id="room_type" required
                        class="w-full px-4 py-3 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors @error('room_type') border-red-500 @enderror">
                        <option value="">-- Select Room Type --</option>
                        <optgroup label="Single Rooms">
                            <option value="single_self_contained" {{ old('room_type') == 'single_self_contained' ? 'selected' : '' }}>Single Room - Self Contained</option>
                            <option value="single_private_bathroom" {{ old('room_type') == 'single_private_bathroom' ? 'selected' : '' }}>Single Room - Private Bathroom</option>
                            <option value="single_shared_bathroom" {{ old('room_type') == 'single_shared_bathroom' ? 'selected' : '' }}>Single Room - Shared Bathroom</option>
                            <option value="single_standard" {{ old('room_type') == 'single_standard' ? 'selected' : '' }}>Single Room - Standard</option>
                            <option value="single_executive" {{ old('room_type') == 'single_executive' ? 'selected' : '' }}>Single Room - Executive</option>
                        </optgroup>
                        <optgroup label="Double Rooms (2 People)">
                            <option value="double_self_contained" {{ old('room_type') == 'double_self_contained' ? 'selected' : '' }}>Two in a Room - Self Contained</option>
                            <option value="double_shared_bathroom" {{ old('room_type') == 'double_shared_bathroom' ? 'selected' : '' }}>Two in a Room - Shared Bathroom</option>
                            <option value="double_standard" {{ old('room_type') == 'double_standard' ? 'selected' : '' }}>Two in a Room - Standard</option>
                        </optgroup>
                        <optgroup label="Triple & Quad Rooms">
                            <option value="triple_self_contained" {{ old('room_type') == 'triple_self_contained' ? 'selected' : '' }}>Three in a Room - Self Contained</option>
                            <option value="quad_self_contained" {{ old('room_type') == 'quad_self_contained' ? 'selected' : '' }}>Four in a Room - Self Contained</option>
                        </optgroup>
                    </select>
                    @error('room_type')
                        <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Price/Year -->
                <div>
                    <label for="price_per_year" class="block text-sm font-medium text-gray-700 mb-2">
                        Price / Year (GH₵) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" step="0.01" min="0" name="price_per_year" id="price_per_year" value="{{ old('price_per_year') }}" required
                        placeholder="0.00"
                        class="w-full px-4 py-3 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors @error('price_per_year') border-red-500 @enderror">
                    @error('price_per_year')
                        <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" id="description" rows="3"
                    placeholder="Provide details about room features, furnishings, or notes..."
                    class="w-full px-4 py-3 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Availability -->
            <div class="flex items-center space-x-3 bg-purple-50/50 p-4 rounded-xl border border-purple-100">
                <input type="checkbox" name="is_available" id="is_available" value="1" {{ old('is_available', 1) ? 'checked' : '' }}
                    class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                <label for="is_available" class="text-sm font-medium text-gray-700">
                    Mark room as available for immediate booking
                </label>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-100">
                <a href="{{ route('agent.hostels.index') }}" class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white text-sm font-semibold rounded-xl shadow-md transition-all">
                    <i class="fas fa-plus mr-2"></i> Save Room
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
