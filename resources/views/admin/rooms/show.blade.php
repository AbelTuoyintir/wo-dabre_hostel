@extends('layouts.app')

@section('title', 'Room Details')
@section('page-title', 'Room: ' . $room->number)

@section('content')
<div class="space-y-6">
    <!-- Header with actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.rooms.index') }}" class="text-gray-600 hover:text-gray-900">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <h2 class="text-xl font-bold text-gray-800">{{ $room->number }}</h2>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium 
                @if($room->status == 'available') bg-green-100 text-green-800
                @elseif($room->status == 'full') bg-yellow-100 text-yellow-800
                @elseif($room->status == 'maintenance') bg-orange-100 text-orange-800
                @else bg-gray-100 text-gray-800 @endif">
                {{ ucfirst($room->status) }}
            </span>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.rooms.edit', $room) }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit Room
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Occupancy</p>
                    <p class="text-2xl font-semibold text-gray-900 mt-1">{{ $room->current_occupancy }}/{{ $room->capacity }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $stats['occupancy_rate'] }}%"></div>
                </div>
                <p class="text-xs text-gray-500 mt-2">{{ $stats['available_spaces'] }} spaces available</p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Bookings</p>
                    <p class="text-2xl font-semibold text-gray-900 mt-1">{{ $stats['total_bookings'] }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <p class="text-xs text-gray-500">
                    <span class="font-medium text-gray-900">{{ $stats['active_bookings'] }}</span> active bookings
                </p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Price/Month</p>
                    <p class="text-2xl font-semibold text-gray-900 mt-1">
                        @if($room->price_per_month)
                            ${{ number_format($room->price_per_month, 2) }}
                        @else
                            <span class="text-gray-400">Not set</span>
                        @endif
                    </p>
                </div>
                <div class="bg-purple-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <p class="text-xs text-gray-500">per person per month</p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Gender</p>
                    <p class="text-2xl font-semibold text-gray-900 mt-1 capitalize">{{ $room->gender }}</p>
                </div>
                <div class="bg-pink-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                @if($room->gender == 'male')
                    <span class="text-xs text-blue-600">Male only</span>
                @elseif($room->gender == 'female')
                    <span class="text-xs text-pink-600">Female only</span>
                @else
                    <span class="text-xs text-purple-600">Any gender</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Room Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Room Information Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-medium text-gray-900">Room Information</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wider">Room Details</h4>
                            <dl class="mt-3 space-y-3">
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-600">Room Number:</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $room->number }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-600">Floor:</dt>
                                    <dd class="text-sm text-gray-900">{{ $room->floor ?? 'N/A' }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-600">Size:</dt>
                                    <dd class="text-sm text-gray-900">{{ $room->size_sqm ?? 'N/A' }} sqm</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-600">Window View:</dt>
                                    <dd class="text-sm text-gray-900 capitalize">{{ $room->window_type ?? 'None' }}</dd>
                                </div>
                            </dl>
                        </div>
                        <div>
                            <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wider">Hostel Information</h4>
                            <dl class="mt-3 space-y-3">
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-600">Hostel:</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $room->hostel->name }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-600">Location:</dt>
                                    <dd class="text-sm text-gray-900">{{ $room->hostel->location }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-600">Contact:</dt>
                                    <dd class="text-sm text-gray-900">{{ $room->hostel->contact_phone ?? 'N/A' }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    @if($room->description)
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-3">Description</h4>
                        <p class="text-sm text-gray-700">{{ $room->description }}</p>
                    </div>
                    @endif

                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-3">Features</h4>
                        <div class="flex flex-wrap gap-2">
                            @if($room->furnished)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs bg-green-100 text-green-800">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Furnished
                                </span>
                            @endif
                            @if($room->private_bathroom)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs bg-green-100 text-green-800">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Private Bathroom
                                </span>
                            @endif
                            @if(!$room->furnished && !$room->private_bathroom)
                                <span class="text-sm text-gray-500">No special features</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Current Occupant -->
            @if($currentBooking)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-medium text-gray-900">Current Occupant</h3>
                </div>
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center">
                            <span class="text-blue-600 font-medium text-lg">
                                {{ strtoupper(substr($currentBooking->user->name, 0, 1)) }}
                            </span>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-lg font-medium text-gray-900">{{ $currentBooking->user->name }}</h4>
                            <p class="text-sm text-gray-500">{{ $currentBooking->user->email }}</p>
                            <p class="text-sm text-gray-500 mt-1">
                                Check-in: {{ $currentBooking->check_in->format('M d, Y') }} - 
                                Check-out: {{ $currentBooking->check_out->format('M d, Y') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Recent Bookings -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900">Recent Bookings</h3>
                    <a href="#" class="text-sm text-blue-600 hover:text-blue-800">View All</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Check In</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Check Out</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($room->bookings as $booking)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $booking->user->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $booking->user->email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $booking->check_in->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $booking->check_out->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        @if($booking->status == 'confirmed') bg-green-100 text-green-800
                                        @elseif($booking->status == 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($booking->status == 'cancelled') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    ${{ number_format($booking->total_amount, 2) }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                    No booking history for this room.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Column - Quick Actions & Status -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-medium text-gray-900">Quick Actions</h3>
                </div>
                <div class="p-6 space-y-4">
                    <!-- Status Update -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Update Status</label>
                        <div class="flex space-x-2">
                            <select id="status-select" class="flex-1 border-gray-300 rounded-md text-sm">
                                <option value="available" {{ $room->status == 'available' ? 'selected' : '' }}>Available</option>
                                <option value="full" {{ $room->status == 'full' ? 'selected' : '' }}>Full</option>
                                <option value="maintenance" {{ $room->status == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                <option value="inactive" {{ $room->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            <button onclick="updateStatus()" 
                                    class="px-4 py-2 bg-gray-800 text-white text-sm rounded-md hover:bg-gray-900">
                                Update
                            </button>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 pt-4">
                        <a href="{{ route('admin.rooms.edit', $room) }}" 
                           class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Edit Room Details
                        </a>
                    </div>
                </div>
            </div>

            <!-- Room Summary -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-medium text-gray-900">Summary</h3>
                </div>
                <div class="p-6">
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-600">Created:</dt>
                            <dd class="text-sm text-gray-900">{{ $room->created_at->format('M d, Y') }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-600">Last Updated:</dt>
                            <dd class="text-sm text-gray-900">{{ $room->updated_at->diffForHumans() }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-600">Total Revenue:</dt>
                            <dd class="text-sm font-medium text-gray-900">
                                ${{ number_format($room->bookings->sum('total_amount'), 2) }}
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-600">Average Stay:</dt>
                            <dd class="text-sm text-gray-900">
                                @php
                                    $avgDays = $room->bookings->avg(function($booking) {
                                        return $booking->check_in->diffInDays($booking->check_out);
                                    });
                                @endphp
                                {{ $avgDays ? round($avgDays) . ' days' : 'N/A' }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Hostel Amenities -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-medium text-gray-900">Hostel Amenities</h3>
                </div>
                <div class="p-6">
                    @if($room->hostel->amenities && count($room->hostel->amenities) > 0)
                        <div class="flex flex-wrap gap-2">
                            @foreach($room->hostel->amenities as $amenity)
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs bg-gray-100 text-gray-800">
                                    {{ $amenity }}
                                </span>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500">No amenities listed</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function updateStatus() {
    const status = document.getElementById('status-select').value;
    
    fetch('{{ route("admin.rooms.status", $room) }}', {
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
            window.location.reload();
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the status.');
    });
}
</script>
@endpush
@endsection