@extends('.layouts.hostelmanage')

@section('title', 'Rooms Management')
@section('page-title', 'Rooms Management')

@section('content')
<!-- Header with Actions -->
<div class="mb-4 bg-white rounded-xl shadow-sm border border-gray-100 p-4">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="flex items-center">
            <div class="bg-blue-50 p-2 rounded-lg mr-3">
                <i class="fas fa-bed text-blue-500 text-sm"></i>
            </div>
            <div>
                <h2 class="text-sm font-semibold text-gray-800">All Rooms</h2>
                <p class="text-xs text-gray-500">Manage and monitor room availability</p>
            </div>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('hostel-manager.rooms.create') }}" 
               class="bg-blue-500 hover:bg-blue-600 text-white text-xs px-3 py-1.5 rounded-lg transition flex items-center">
                <i class="fas fa-plus mr-1 text-xs"></i>
                Add New Room
            </a>
            <button onclick="exportRooms()" 
                    class="border border-gray-300 hover:bg-gray-50 text-gray-700 text-xs px-3 py-1.5 rounded-lg transition flex items-center">
                <i class="fas fa-download mr-1 text-xs"></i>
                Export
            </button>
        </div>
    </div>

    <!-- Quick Stats -->
    @if(isset($summary))
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mt-4 pt-3 border-t border-gray-100">
        <div class="text-center">
            <span class="text-xs text-gray-500 block">Total</span>
            <span class="text-sm font-bold text-gray-800">{{ $summary['total'] ?? 0 }}</span>
        </div>
        <div class="text-center">
            <span class="text-xs text-gray-500 block">Available</span>
            <span class="text-sm font-bold text-green-600">{{ $summary['available'] ?? 0 }}</span>
        </div>
        <div class="text-center">
            <span class="text-xs text-gray-500 block">Occupied</span>
            <span class="text-sm font-bold text-yellow-600">{{ $summary['occupied'] ?? 0 }}</span>
        </div>
        <div class="text-center">
            <span class="text-xs text-gray-500 block">Maintenance</span>
            <span class="text-sm font-bold text-red-600">{{ $summary['maintenance'] ?? 0 }}</span>
        </div>
        <div class="text-center">
            <span class="text-xs text-gray-500 block">Capacity</span>
            <span class="text-sm font-bold text-purple-600">{{ $summary['total_capacity'] ?? 0 }}</span>
        </div>
    </div>
    @endif
</div>

<!-- Filters -->
<div class="mb-4 bg-white rounded-xl shadow-sm border border-gray-100 p-4">
    <form method="GET" action="{{ route('hostel-manager.rooms') }}" class="flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-[10px] font-medium text-gray-500 uppercase mb-1">Search</label>
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Room number..."
                       class="w-full pl-8 pr-3 py-1.5 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>
        
        <div class="w-32">
            <label class="block text-[10px] font-medium text-gray-500 uppercase mb-1">Status</label>
            <select name="status" class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                <option value="">All</option>
                <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                <option value="occupied" {{ request('status') == 'occupied' ? 'selected' : '' }}>Occupied</option>
                <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
            </select>
        </div>
        
        @if(isset($hostels) && $hostels->count() > 1)
        <div class="w-40">
            <label class="block text-[10px] font-medium text-gray-500 uppercase mb-1">Hostel</label>
            <select name="hostel_id" class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                <option value="">All Hostels</option>
                @foreach($hostels as $hostel)
                    <option value="{{ $hostel->id }}" {{ request('hostel_id') == $hostel->id ? 'selected' : '' }}>
                        {{ $hostel->name }}
                    </option>
                @endforeach
            </select>
        </div>
        @endif
        
        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white text-xs px-4 py-1.5 rounded-lg transition">
            <i class="fas fa-filter mr-1"></i> Filter
        </button>
        
        <a href="{{ route('hostel-manager.rooms') }}" class="border border-gray-300 hover:bg-gray-50 text-gray-700 text-xs px-4 py-1.5 rounded-lg transition">
            <i class="fas fa-times mr-1"></i> Clear
        </a>
    </form>
</div>

<!-- Rooms Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase tracking-wider">Room</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase tracking-wider">Hostel</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase tracking-wider">Capacity</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase tracking-wider">Occupied</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase tracking-wider">Available</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase tracking-wider">Gender</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase tracking-wider">Price/Month</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($rooms as $room)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2 whitespace-nowrap">
                        <span class="text-xs font-medium text-gray-900">{{ $room->number }}</span>
                        @if($room->floor)
                            <span class="text-[10px] text-gray-400 ml-1">(Floor {{ $room->floor }})</span>
                        @endif
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap">
                        <span class="text-xs text-gray-600">{{ $room->hostel->name ?? 'N/A' }}</span>
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap">
                        <span class="text-xs text-gray-600">{{ $room->capacity }}</span>
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap">
                        <span class="text-xs text-gray-600">{{ $room->current_occupancy }}</span>
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap">
                        @php
                            $availableSpaces = $room->capacity - $room->current_occupancy;
                        @endphp
                        <span class="text-xs font-medium {{ $availableSpaces > 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $availableSpaces }}
                        </span>
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap">
                        @php
                            $statusClass = match($room->status) {
                                'available' => 'bg-green-100 text-green-700',
                                'occupied' => 'bg-yellow-100 text-yellow-700',
                                'maintenance' => 'bg-red-100 text-red-700',
                                default => 'bg-gray-100 text-gray-700'
                            };
                        @endphp
                        <span class="text-[10px] font-medium px-2 py-0.5 rounded-full {{ $statusClass }}">
                            {{ ucfirst($room->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap">
                        @php
                            $genderClass = match($room->gender) {
                                'male' => 'bg-blue-100 text-blue-700',
                                'female' => 'bg-pink-100 text-pink-700',
                                default => 'bg-purple-100 text-purple-700'
                            };
                        @endphp
                        <span class="text-[10px] font-medium px-2 py-0.5 rounded-full {{ $genderClass }}">
                            {{ ucfirst($room->gender) }}
                        </span>
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap">
                        <span class="text-xs font-medium text-gray-900">
                            ₵{{ number_format($room->price_per_semester ?? 0) }}
                        </span>
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap">
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('hostel-manager.rooms.show', $room) }}" 
                               class="text-blue-600 hover:text-blue-800" title="View">
                                <i class="fas fa-eye text-xs"></i>
                            </a>
                            <a href="{{ route('hostel-manager.rooms.edit', $room) }}" 
                               class="text-yellow-600 hover:text-yellow-800" title="Edit">
                                <i class="fas fa-edit text-xs"></i>
                            </a>
                            <button onclick="confirmDelete('room {{ $room->number }}', '{{ route('hostel-manager.rooms.destroy', $room) }}')" 
                                    class="text-red-600 hover:text-red-800" title="Delete">
                                <i class="fas fa-trash-alt text-xs"></i>
                            </button>
                            <button onclick="updateRoomStatus({{ $room->id }})" 
                                    class="text-green-600 hover:text-green-800" title="Update Status">
                                <i class="fas fa-sync-alt text-xs"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-4 py-8 text-center">
                        <div class="flex flex-col items-center">
                            <i class="fas fa-bed text-gray-300 text-2xl mb-2"></i>
                            <p class="text-xs text-gray-500 mb-3">No rooms found</p>
                            <a href="{{ route('hostel-manager.rooms.create') }}" 
                               class="bg-blue-500 hover:bg-blue-600 text-white text-xs px-3 py-1.5 rounded-lg transition inline-flex items-center">
                                <i class="fas fa-plus mr-1"></i> Add Your First Room
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($rooms->hasPages())
    <div class="px-4 py-3 border-t border-gray-100">
        {{ $rooms->links() }}
    </div>
    @endif
</div>

<!-- Status Update Modal -->
<div id="statusModal" class="modal">
    <div class="modal-content container mx-auto px-4 py-16 max-w-md">
        <div class="bg-white rounded-xl shadow-2xl overflow-hidden">
            <div class="px-4 py-3 bg-gradient-to-r from-blue-500 to-purple-600">
                <h3 class="text-sm font-semibold text-white flex items-center">
                    <i class="fas fa-sync-alt mr-2 text-xs"></i>
                    Update Room Status
                </h3>
            </div>
            
            <form id="statusForm" method="POST" class="p-4">
                @csrf
                @method('PATCH')
                
                <div class="mb-4">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Select Status</label>
                    <select name="status" id="roomStatus" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="available">Available</option>
                        <option value="occupied">Occupied</option>
                        <option value="maintenance">Maintenance</option>
                    </select>
                </div>
                
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeStatusModal()" 
                            class="px-3 py-1.5 text-xs border border-gray-300 rounded-lg hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-3 py-1.5 text-xs bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                        Update Status
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentRoomId = null;

function updateRoomStatus(roomId) {
    currentRoomId = roomId;
    const modal = document.getElementById('statusModal');
    const form = document.getElementById('statusForm');
    form.action = `/hostel-manager/rooms/${roomId}/status`;
    modal.style.display = 'block';
    document.body.style.overflow = 'hidden';
}

function closeStatusModal() {
    document.getElementById('statusModal').style.display = 'none';
    document.body.style.overflow = 'auto';
    currentRoomId = null;
}

function confirmDelete(itemName, deleteUrl) {
    Swal.fire({
        title: 'Are you sure?',
        text: `You are about to delete ${itemName}. This action cannot be undone!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, delete it',
        cancelButtonText: 'Cancel',
        reverseButtons: true,
        customClass: {
            popup: 'rounded-xl',
            confirmButton: 'bg-red-500 hover:bg-red-600 text-white text-xs font-medium py-1.5 px-3',
            cancelButton: 'bg-gray-500 hover:bg-gray-600 text-white text-xs font-medium py-1.5 px-3'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = deleteUrl;
            form.innerHTML = '@csrf @method("DELETE")';
            document.body.appendChild(form);
            form.submit();
        }
    });
}

function exportRooms() {
    Swal.fire({
        title: 'Export Rooms',
        text: 'Choose export format',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3b82f6',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'CSV',
        cancelButtonText: 'Cancel',
        showDenyButton: true,
        denyButtonText: 'PDF',
        denyButtonColor: '#8b5cf6',
        customClass: {
            popup: 'rounded-xl',
            confirmButton: 'bg-blue-500 hover:bg-blue-600 text-white text-xs font-medium py-1.5 px-3',
            cancelButton: 'bg-gray-500 hover:bg-gray-600 text-white text-xs font-medium py-1.5 px-3',
            denyButton: 'bg-purple-500 hover:bg-purple-600 text-white text-xs font-medium py-1.5 px-3'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '{{ route("hostel-manager.rooms.export") }}?format=csv';
        } else if (result.isDenied) {
            window.location.href = '{{ route("hostel-manager.rooms.export") }}?format=pdf';
        }
    });
}

// Close modal when clicking outside
window.addEventListener('click', function(event) {
    const modal = document.getElementById('statusModal');
    if (event.target === modal) {
        closeStatusModal();
    }
});
</script>
@endpush