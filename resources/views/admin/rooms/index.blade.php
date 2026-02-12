@extends('layouts.app')

@section('title', 'Manage Rooms')
@section('page-title', 'Room Management')

@section('content')
<div class="space-y-6">
    <!-- Header with actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <h2 class="text-xl font-bold text-gray-800">All Rooms</h2>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                {{ $rooms->total() }} Total
            </span>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.rooms.export') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                Export
            </a>
            <a href="{{ route('admin.rooms.create') }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent rounded-md bg-blue-600 text-sm font-medium text-white hover:bg-blue-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add New Room
            </a>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-sm font-medium text-gray-700 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                </svg>
                Filter Rooms
            </h3>
        </div>
        <div class="p-4">
            <form method="GET" action="{{ route('admin.rooms.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Search -->
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Search</label>
                        <div class="relative">
                            <input type="text" name="search" placeholder="Room # or hostel..." 
                                   value="{{ request('search') }}"
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            <div class="absolute left-3 top-2.5 text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Hostel Filter -->
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Hostel</label>
                        <select name="hostel_id" class="w-full border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Hostels</option>
                            @foreach($hostels as $hostel)
                                <option value="{{ $hostel->id }}" {{ request('hostel_id') == $hostel->id ? 'selected' : '' }}>
                                    {{ $hostel->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                        <select name="status" class="w-full border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Status</option>
                            <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                            <option value="full" {{ request('status') == 'full' ? 'selected' : '' }}>Full</option>
                            <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <!-- Gender Filter -->
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Gender</label>
                        <select name="gender" class="w-full border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Genders</option>
                            <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>Female</option>
                            <option value="any" {{ request('gender') == 'any' ? 'selected' : '' }}>Any</option>
                        </select>
                    </div>

                    <!-- Price Range -->
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Min Price</label>
                        <input type="number" name="min_price" placeholder="0" value="{{ request('min_price') }}"
                               class="w-full border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Max Price</label>
                        <input type="number" name="max_price" placeholder="Any" value="{{ request('max_price') }}"
                               class="w-full border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Features -->
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Features</label>
                        <div class="space-y-2">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="furnished" value="1" {{ request('furnished') ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Furnished</span>
                            </label>
                            <label class="inline-flex items-center ml-4">
                                <input type="checkbox" name="private_bathroom" value="1" {{ request('private_bathroom') ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Private Bathroom</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2">
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-medium text-xs text-white hover:bg-blue-700">
                        Apply Filters
                    </button>
                    <a href="{{ route('admin.rooms.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-xs font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Clear Filters
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Bulk Actions -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4" x-data="bulkActions()">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <span class="text-sm text-gray-700" x-text="selectedCount + ' rooms selected'"></span>
                <span class="text-gray-300">|</span>
                <button @click="selectAll()" class="text-sm text-blue-600 hover:text-blue-800">Select All</button>
                <button @click="clearAll()" class="text-sm text-gray-600 hover:text-gray-800">Clear</button>
            </div>
            
            <div class="flex items-center space-x-3">
                <select x-model="bulkAction" class="border-gray-300 rounded-md text-sm">
                    <option value="">Bulk Actions</option>
                    <option value="status">Change Status</option>
                    <option value="gender">Change Gender</option>
                    <option value="hostel">Move to Hostel</option>
                    <option value="capacity">Update Capacity</option>
                    <option value="price">Update Price</option>
                </select>
                
                <select x-show="bulkAction === 'status'" x-model="bulkValue" class="border-gray-300 rounded-md text-sm">
                    <option value="available">Available</option>
                    <option value="full">Full</option>
                    <option value="maintenance">Maintenance</option>
                    <option value="inactive">Inactive</option>
                </select>
                
                <select x-show="bulkAction === 'gender'" x-model="bulkValue" class="border-gray-300 rounded-md text-sm">
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="any">Any</option>
                </select>
                
                <select x-show="bulkAction === 'hostel'" x-model="bulkValue" class="border-gray-300 rounded-md text-sm">
                    @foreach($hostels as $hostel)
                        <option value="{{ $hostel->id }}">{{ $hostel->name }}</option>
                    @endforeach
                </select>
                
                <input x-show="bulkAction === 'capacity'" type="number" x-model="bulkValue" 
                       placeholder="Capacity" class="border-gray-300 rounded-md text-sm w-24">
                
                <input x-show="bulkAction === 'price'" type="number" x-model="bulkValue" 
                       placeholder="Price" class="border-gray-300 rounded-md text-sm w-32">
                
                <button @click="applyBulkAction()" 
                        class="px-4 py-2 bg-gray-800 text-white text-sm rounded-md hover:bg-gray-900"
                        :disabled="!selectedCount || !bulkAction || !bulkValue">
                    Apply
                </button>
            </div>
        </div>
    </div>

    <!-- Rooms Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left">
                            <input type="checkbox" @click="toggleAll" :checked="isAllSelected" 
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Room</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hostel</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Capacity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Occupancy</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price/Month</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gender</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Features</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($rooms as $room)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="checkbox" value="{{ $room->id }}" x-model="selectedItems" 
                                   class="room-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $room->number }}</div>
                            <div class="text-xs text-gray-500">Floor {{ $room->floor ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $room->hostel->name }}</div>
                            <div class="text-xs text-gray-500">{{ $room->hostel->location }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $room->capacity }} persons
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <span class="text-sm text-gray-900 mr-2">{{ $room->current_occupancy }}/{{ $room->capacity }}</span>
                                <div class="w-16 bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $room->occupancyRate() }}%"></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($room->price_per_month)
                                ${{ number_format($room->price_per_month, 2) }}
                            @else
                                <span class="text-gray-400">Not set</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($room->gender == 'male')
                                <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">Male</span>
                            @elseif($room->gender == 'female')
                                <span class="px-2 py-1 text-xs bg-pink-100 text-pink-800 rounded-full">Female</span>
                            @else
                                <span class="px-2 py-1 text-xs bg-purple-100 text-purple-800 rounded-full">Any</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full 
                                @if($room->status == 'available') bg-green-100 text-green-800
                                @elseif($room->status == 'full') bg-yellow-100 text-yellow-800
                                @elseif($room->status == 'maintenance') bg-orange-100 text-orange-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($room->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex space-x-1">
                                @if($room->furnished)
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs bg-gray-100 text-gray-800">Furnished</span>
                                @endif
                                @if($room->private_bathroom)
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs bg-gray-100 text-gray-800">Private Bath</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('admin.rooms.show', $room) }}" 
                                   class="text-blue-600 hover:text-blue-900" title="View">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                <a href="{{ route('admin.rooms.edit', $room) }}" 
                                   class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                <form action="{{ route('admin.rooms.destroy', $room) }}" 
                                      method="POST" 
                                      onsubmit="return confirm('Are you sure you want to delete this room?');"
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                <h3 class="mt-4 text-lg font-medium text-gray-900">No rooms found</h3>
                                <p class="mt-1 text-sm text-gray-500">Get started by creating your first room.</p>
                                <a href="{{ route('admin.rooms.create') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white hover:bg-blue-700">
                                    Add New Room
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
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            {{ $rooms->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function bulkActions() {
    return {
        selectedItems: [],
        bulkAction: '',
        bulkValue: '',
        
        get selectedCount() {
            return this.selectedItems.length;
        },
        
        get isAllSelected() {
            return this.selectedCount === {{ $rooms->total() }};
        },
        
        selectAll() {
            this.selectedItems = @json($rooms->pluck('id'));
        },
        
        clearAll() {
            this.selectedItems = [];
        },
        
        toggleAll() {
            if (this.isAllSelected) {
                this.clearAll();
            } else {
                this.selectAll();
            }
        },
        
        applyBulkAction() {
            if (!this.selectedCount || !this.bulkAction || !this.bulkValue) {
                return;
            }
            
            fetch('{{ route("admin.rooms.bulk-update") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    room_ids: this.selectedItems,
                    action: this.bulkAction,
                    value: this.bulkValue
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating rooms.');
            });
        }
    }
}
</script>
@endpush
@endsection