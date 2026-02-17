@extends('layouts.hostelmanage')

@section('title', 'Room Details')
@section('page-title', 'Room Details')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Header -->
    <div class="mb-4 bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <a href="{{ route('hostel-manager.rooms') }}" class="text-gray-500 hover:text-gray-700 mr-3">
                    <i class="fas fa-arrow-left text-xs"></i>
                </a>
                <div>
                    <h2 class="text-sm font-semibold text-gray-800">Room {{ $room->number }}</h2>
                    <p class="text-xs text-gray-500">{{ $room->hostel->name }}</p>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('hostel-manager.rooms.edit', $room) }}" 
                   class="bg-yellow-500 hover:bg-yellow-600 text-white text-xs px-3 py-1.5 rounded-lg transition flex items-center">
                    <i class="fas fa-edit mr-1"></i> Edit
                </a>
                <button onclick="confirmDelete('room {{ $room->number }}', '{{ route('hostel-manager.rooms.destroy', $room) }}')" 
                        class="bg-red-500 hover:bg-red-600 text-white text-xs px-3 py-1.5 rounded-lg transition flex items-center">
                    <i class="fas fa-trash-alt mr-1"></i> Delete
                </button>
            </div>
        </div>
    </div>

    <!-- Room Details Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-4">
            <!-- Basic Info Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
                    <h3 class="text-xs font-semibold text-gray-700 uppercase">Room Information</h3>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase">Room Number</p>
                            <p class="text-sm font-medium text-gray-900">{{ $room->number }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase">Floor</p>
                            <p class="text-sm text-gray-900">{{ $room->floor ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase">Capacity</p>
                            <p class="text-sm text-gray-900">{{ $room->capacity }} persons</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase">Current Occupancy</p>
                            <p class="text-sm text-gray-900">{{ $room->current_occupancy }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase">Status</p>
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
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase">Gender</p>
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
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase">Price/Month</p>
                            <p class="text-sm font-medium text-gray-900">₵{{ number_format($room->price_per_month ?? 0) }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase">Size</p>
                            <p class="text-sm text-gray-900">{{ $room->size_sqm ?? 'N/A' }} sqm</p>
                        </div>
                    </div>
                    
                    @if($room->description)
                    <div class="mt-4 pt-3 border-t border-gray-100">
                        <p class="text-[10px] text-gray-500 uppercase mb-1">Description</p>
                        <p class="text-xs text-gray-700">{{ $room->description }}</p>
                    </div>
                    @endif
                    
                    <div class="mt-4 pt-3 border-t border-gray-100">
                        <p class="text-[10px] text-gray-500 uppercase mb-1">Features</p>
                        <div class="flex flex-wrap gap-1">
                            @if($room->furnished)
                                <span class="text-[10px] bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">Furnished</span>
                            @endif
                            @if($room->private_bathroom)
                                <span class="text-[10px] bg-green-100 text-green-700 px-2 py-0.5 rounded-full">Private Bathroom</span>
                            @endif
                            @if($room->window_type)
                                <span class="text-[10px] bg-purple-100 text-purple-700 px-2 py-0.5 rounded-full">{{ ucfirst($room->window_type) }} View</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Current Occupants -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
                    <h3 class="text-xs font-semibold text-gray-700 uppercase">Current Occupants</h3>
                </div>
                @if(isset($currentOccupants) && $currentOccupants->count() > 0)
                <div class="divide-y divide-gray-100">
                    @foreach($currentOccupants as $occupant)
                    <div class="p-3 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center mr-2">
                                    <span class="text-[10px] font-medium text-blue-700">{{ substr($occupant->name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-900">{{ $occupant->name }}</p>
                                    <p class="text-[10px] text-gray-500">{{ $occupant->student_id ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <span class="text-[10px] text-gray-500">{{ $occupant->pivot->check_in ?? 'N/A' }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="p-4 text-center">
                    <p class="text-xs text-gray-500">No current occupants</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-4">
            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
                    <h3 class="text-xs font-semibold text-gray-700 uppercase">Quick Actions</h3>
                </div>
                <div class="p-3 space-y-2">
                    <select onchange="updateRoomStatusQuick(this.value)" 
                            class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-lg">
                        <option value="available" {{ $room->status == 'available' ? 'selected' : '' }}>Set Available</option>
                        <option value="occupied" {{ $room->status == 'occupied' ? 'selected' : '' }}>Set Occupied</option>
                        <option value="maintenance" {{ $room->status == 'maintenance' ? 'selected' : '' }}>Set Maintenance</option>
                    </select>
                    
                    <a href="#" class="block w-full text-center bg-blue-500 hover:bg-blue-600 text-white text-xs px-3 py-1.5 rounded-lg">
                        <i class="fas fa-calendar-plus mr-1"></i> Assign Occupant
                    </a>
                    
                    <a href="#" class="block w-full text-center border border-gray-300 hover:bg-gray-50 text-gray-700 text-xs px-3 py-1.5 rounded-lg">
                        <i class="fas fa-clipboard-list mr-1"></i> View History
                    </a>
                </div>
            </div>

            <!-- Room Stats -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
                    <h3 class="text-xs font-semibold text-gray-700 uppercase">Room Statistics</h3>
                </div>
                <div class="p-3">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-xs text-gray-600">Total Bookings</span>
                        <span class="text-xs font-semibold">{{ $room->bookings_count ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-xs text-gray-600">Occupancy Rate</span>
                        <span class="text-xs font-semibold">
                            {{ $room->capacity > 0 ? round(($room->current_occupancy / $room->capacity) * 100) : 0 }}%
                        </span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-1.5 mt-1">
                        <div class="bg-green-500 h-1.5 rounded-full" 
                             style="width: {{ $room->capacity > 0 ? ($room->current_occupancy / $room->capacity) * 100 : 0 }}%">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function updateRoomStatusQuick(status) {
    fetch(`/hostel-manager/rooms/{{ $room->id }}/status`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'Room status updated successfully',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                window.location.reload();
            });
        }
    });
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
        cancelButtonText: 'Cancel'
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
</script>
@endpush