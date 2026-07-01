@extends('layouts.agent')

@section('title', $hostel->name)

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-6">
        <div>
            <h5 class="text-sm font-semibold text-gray-800">{{ $hostel->name }}</h5>
            <p class="text-xs text-gray-500">{{ $hostel->address }}</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('agent.hostels.index') }}" class="inline-flex items-center px-3 py-1.5 bg-gray-200 hover:bg-gray-300 text-gray-700 text-xs font-medium rounded-lg transition-colors duration-200">
                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back
            </a>
            <a href="{{ route('agent.hostels.edit', $hostel->id) }}" class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg transition-colors duration-200">
                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-50 border-l-4 border-green-500 rounded-lg text-green-700 text-xs flex justify-between items-center">
            <span>{{ session('success') }}</span>
            <button type="button" class="text-green-700 hover:text-green-900" onclick="this.parentElement.remove()">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-4">
            <!-- Hostel Details -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-4">
                    <div class="flex flex-col md:flex-row gap-4">
                        @if($hostel->featured_image)
                            <div class="md:w-48 flex-shrink-0">
                                <img src="{{ Storage::url($hostel->featured_image) }}" 
                                     alt="{{ $hostel->name }}" 
                                     class="w-full h-40 object-cover rounded-lg">
                            </div>
                            <div class="flex-1">
                        @else
                            <div class="flex-1">
                        @endif
                                <h6 class="text-sm font-semibold text-gray-800 mb-2">{{ $hostel->name }}</h6>
                                <p class="text-xs text-gray-600 leading-relaxed">{{ $hostel->description }}</p>
                                <div class="grid grid-cols-2 gap-2 mt-3 text-xs">
                                    <div>
                                        <span class="font-medium text-gray-700">Location:</span>
                                        <span class="text-gray-600">{{ $hostel->location }}</span>
                                    </div>
                                    <div>
                                        <span class="font-medium text-gray-700">Status:</span>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                            {{ $hostel->status === 'active' ? 'bg-green-100 text-green-800' : 
                                               ($hostel->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                            {{ ucfirst($hostel->status) }}
                                        </span>
                                    </div>
                                    <div>
                                        <span class="font-medium text-gray-700">Verified:</span>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                            {{ $hostel->is_verified ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $hostel->is_verified ? 'Yes' : 'No' }}
                                        </span>
                                    </div>
                                    @if($hostel->latitude && $hostel->longitude)
                                        <div>
                                            <span class="font-medium text-gray-700">Coordinates:</span>
                                            <span class="text-gray-600">{{ $hostel->latitude }}, {{ $hostel->longitude }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                    </div>
                </div>
            </div>

            <!-- Gallery -->
            @if($hostel->images->count() > 0)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-4 py-2.5 border-b border-gray-200">
                        <h6 class="text-sm font-semibold text-gray-800">Gallery</h6>
                    </div>
                    <div class="p-4">
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                            @foreach($hostel->images as $image)
                                <div class="relative group">
                                    <img src="{{ Storage::url($image->image_path) }}" 
                                         alt="Gallery" 
                                         class="w-full h-32 object-cover rounded-lg">
                                    <button onclick="viewImage('{{ Storage::url($image->image_path) }}')" 
                                            class="absolute inset-0 w-full h-full bg-black bg-opacity-0 hover:bg-opacity-40 transition-all duration-200 rounded-lg flex items-center justify-center">
                                        <svg class="w-8 h-8 text-white opacity-0 group-hover:opacity-100 transition-all duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Amenities -->
            @if($hostel->amenities->count() > 0)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-4 py-2.5 border-b border-gray-200">
                        <h6 class="text-sm font-semibold text-gray-800">Amenities</h6>
                    </div>
                    <div class="p-4">
                        <div class="flex flex-wrap gap-2">
                            @foreach($hostel->amenities as $amenity)
                                <span class="inline-flex items-center px-2.5 py-1 bg-gray-100 text-gray-800 text-xs rounded-lg border border-gray-200">
                                    {{ $amenity->name }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-4">
            <!-- Statistics -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-4 py-2.5 border-b border-gray-200">
                    <h6 class="text-sm font-semibold text-gray-800">Statistics</h6>
                </div>
                <div class="p-4 space-y-2">
                    <div class="flex justify-between items-center py-1.5 border-b border-gray-100">
                        <span class="text-xs text-gray-600">Total Rooms</span>
                        <span class="inline-flex items-center justify-center px-2.5 py-0.5 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">
                            {{ $hostel->rooms->count() }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center py-1.5 border-b border-gray-100">
                        <span class="text-xs text-gray-600">Available Rooms</span>
                        <span class="inline-flex items-center justify-center px-2.5 py-0.5 bg-green-100 text-green-800 text-xs font-medium rounded-full">
                            {{ $hostel->rooms->where('is_available', true)->count() }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center py-1.5">
                        <span class="text-xs text-gray-600">Occupied Rooms</span>
                        <span class="inline-flex items-center justify-center px-2.5 py-0.5 bg-yellow-100 text-yellow-800 text-xs font-medium rounded-full">
                            {{ $hostel->rooms->where('is_available', false)->count() }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Add Room Form -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-4 py-2.5 border-b border-gray-200">
                    <h6 class="text-sm font-semibold text-gray-800">Add New Room</h6>
                </div>
                <div class="p-4">
                    <form action="{{ route('agent.hostels.add-room', $hostel->id) }}" method="POST">
                        @csrf
                        <div class="space-y-3">
                            <div>
                                <label for="room_number" class="block text-xs font-medium text-gray-700 mb-1">
                                    Room Number <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       class="w-full px-3 py-1.5 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('room_number') border-red-500 @enderror" 
                                       id="room_number" name="room_number" required>
                                @error('room_number')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="room_type" class="block text-xs font-medium text-gray-700 mb-1">
                                    Room Type <span class="text-red-500">*</span>
                                </label>
                                <select class="w-full px-3 py-1.5 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('room_type') border-red-500 @enderror" 
                                        id="room_type" name="room_type" required>
                                    <option value="">Select Type</option>
                                    <option value="single">Single</option>
                                    <option value="double">Double</option>
                                    <option value="triple">Triple</option>
                                    <option value="dormitory">Dormitory</option>
                                </select>
                                @error('room_type')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="capacity" class="block text-xs font-medium text-gray-700 mb-1">
                                    Capacity <span class="text-red-500">*</span>
                                </label>
                                <input type="number" 
                                       class="w-full px-3 py-1.5 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('capacity') border-red-500 @enderror" 
                                       id="capacity" name="capacity" min="1" required>
                                @error('capacity')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="price_per_year" class="block text-xs font-medium text-gray-700 mb-1">
                                    Price/Year <span class="text-red-500">*</span>
                                </label>
                                <input type="number" step="0.01" 
                                       class="w-full px-3 py-1.5 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('price_per_year') border-red-500 @enderror" 
                                       id="price_per_year" name="price_per_year" min="0" required>
                                @error('price_per_year')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="room_description" class="block text-xs font-medium text-gray-700 mb-1">Description</label>
                                <textarea class="w-full px-3 py-1.5 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror" 
                                          id="room_description" name="description" rows="2"></textarea>
                                @error('description')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="flex items-center space-x-2">
                                <input type="checkbox" 
                                       class="w-3.5 h-3.5 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                       id="is_available" name="is_available" value="1" checked>
                                <label class="text-xs text-gray-700" for="is_available">Available</label>
                            </div>
                            <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg transition-colors duration-200">
                                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Add Room
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Rooms List -->
    <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-4 py-2.5 border-b border-gray-200">
            <h6 class="text-sm font-semibold text-gray-800">Rooms</h6>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Room Number</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Capacity</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price/Year</th>
                        <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-2.5 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($hostel->rooms as $room)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-4 py-3 text-xs text-gray-500">{{ $loop->iteration }}</td>
                            <td class="px-4 py-3 text-xs text-gray-900 font-medium">{{ $room->room_number }}</td>
                            <td class="px-4 py-3 text-xs text-gray-600">{{ ucfirst($room->room_type) }}</td>
                            <td class="px-4 py-3 text-xs text-gray-600">{{ $room->capacity }}</td>
                            <td class="px-4 py-3 text-xs text-gray-600">${{ number_format($room->price_per_year, 2) }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $room->is_available ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $room->is_available ? 'Available' : 'Occupied' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end space-x-1.5">
                                    <a href="{{ route('agent.rooms.edit', $room->id) }}" 
                                       class="p-1.5 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    <button type="button" 
                                            class="p-1.5 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition-colors duration-200"
                                            onclick="openDeleteRoomModal('{{ $room->id }}', '{{ $room->room_number }}')">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-xs text-gray-500">
                                No rooms added yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Image Preview Modal -->
<div id="imageModal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-90 transition-opacity" onclick="closeImageModal()"></div>
        <div class="relative max-w-4xl w-full">
            <button onclick="closeImageModal()" class="absolute top-4 right-4 text-white hover:text-gray-300 transition-colors duration-200">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            <img id="modalImage" src="" alt="Preview" class="w-full max-h-[80vh] object-contain rounded-lg">
        </div>
    </div>
</div>

<!-- Delete Room Modal -->
<div id="deleteRoomModal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeDeleteRoomModal()"></div>
        <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full transform transition-all">
            <div class="p-6">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-900">Delete Room</h3>
                        <p class="text-xs text-gray-500 mt-1" id="deleteRoomMessage">Are you sure you want to delete this room?</p>
                    </div>
                </div>
                <div class="mt-4 flex justify-end space-x-2">
                    <button type="button" onclick="closeDeleteRoomModal()" class="px-3 py-1.5 bg-gray-200 hover:bg-gray-300 text-gray-800 text-xs font-medium rounded-lg transition-colors duration-200">
                        Cancel
                    </button>
                    <form id="deleteRoomForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded-lg transition-colors duration-200">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function viewImage(imageUrl) {
    document.getElementById('modalImage').src = imageUrl;
    document.getElementById('imageModal').classList.remove('hidden');
}

function closeImageModal() {
    document.getElementById('imageModal').classList.add('hidden');
}

function openDeleteRoomModal(roomId, roomNumber) {
    document.getElementById('deleteRoomMessage').textContent = `Are you sure you want to delete room "${roomNumber}"? This action cannot be undone.`;
    document.getElementById('deleteRoomForm').action = `{{ route('agent.rooms.destroy', '') }}/${roomId}`;
    document.getElementById('deleteRoomModal').classList.remove('hidden');
}

function closeDeleteRoomModal() {
    document.getElementById('deleteRoomModal').classList.add('hidden');
}
</script>
@endsection