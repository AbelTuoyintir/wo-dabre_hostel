@extends('layouts.hostelmanage')

@section('title', 'Occupants Management')
@section('page-title', 'Occupants Management')

@section('content')
<!-- Header with Actions -->
<div class="mb-4 bg-white rounded-xl shadow-sm border border-gray-100 p-4">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="flex items-center">
            <div class="bg-blue-50 p-2 rounded-lg mr-3">
                <i class="fas fa-users text-blue-500 text-sm"></i>
            </div>
            <div>
                <h2 class="text-sm font-semibold text-gray-800">Current Occupants</h2>
                <p class="text-xs text-gray-500">Manage students currently staying in your hostels</p>
            </div>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('hostel-manager.occupants.export') }}"
               class="border border-gray-300 hover:bg-gray-50 text-gray-700 text-xs px-3 py-1.5 rounded-lg transition flex items-center">
                <i class="fas fa-download mr-1 text-xs"></i>
                Export List
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mt-4 pt-3 border-t border-gray-100">
        <div class="text-center">
            <span class="text-xs text-gray-500 block">Total</span>
            <span class="text-sm font-bold text-gray-800">{{ $stats['total'] ?? 0 }}</span>
        </div>
        <div class="text-center">
            <span class="text-xs text-gray-500 block">Male</span>
            <span class="text-sm font-bold text-blue-600">{{ $stats['male'] ?? 0 }}</span>
        </div>
        <div class="text-center">
            <span class="text-xs text-gray-500 block">Female</span>
            <span class="text-sm font-bold text-pink-600">{{ $stats['female'] ?? 0 }}</span>
        </div>
        <div class="text-center">
            <span class="text-xs text-gray-500 block">Active</span>
            <span class="text-sm font-bold text-green-600">{{ $stats['active'] ?? 0 }}</span>
        </div>
        <div class="text-center">
            <span class="text-xs text-gray-500 block">Check-out Today</span>
            <span class="text-sm font-bold text-orange-600">{{ $stats['checkout_today'] ?? 0 }}</span>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="mb-4 bg-white rounded-xl shadow-sm border border-gray-100 p-4">
    <form method="GET" action="{{ route('hostel-manager.occupants') }}" class="flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-[10px] font-medium text-gray-500 uppercase mb-1">Search</label>
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search by name, email or student ID..."
                       class="w-full pl-8 pr-3 py-1.5 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>

        <div class="w-32">
            <label class="block text-[10px] font-medium text-gray-500 uppercase mb-1">Gender</label>
            <select name="gender" class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                <option value="">All</option>
                <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>Male</option>
                <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>Female</option>
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

        <a href="{{ route('hostel-manager.occupants') }}" class="border border-gray-300 hover:bg-gray-50 text-gray-700 text-xs px-4 py-1.5 rounded-lg transition">
            <i class="fas fa-times mr-1"></i> Clear
        </a>
    </form>
</div>

<!-- Occupants Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase tracking-wider">Student</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase tracking-wider">Student ID</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase tracking-wider">Hostel/Room</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase tracking-wider">Check In</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase tracking-wider">Check Out</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($occupants as $occupant)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center mr-2">
                                <span class="text-[10px] font-medium text-blue-700">{{ substr($occupant->name, 0, 1) }}</span>
                            </div>
                            <span class="text-xs font-medium text-gray-900">{{ $occupant->name }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap">
                        <div class="text-xs text-gray-600">{{ $occupant->email }}</div>
                        <div class="text-[10px] text-gray-400">{{ $occupant->phone ?? 'No phone' }}</div>
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-xs text-gray-600">
                        {{ $occupant->student_id ?? 'N/A' }}
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap">
                        @if($occupant->bookings->isNotEmpty())
                            @php $booking = $occupant->bookings->first(); @endphp
                            <div class="text-xs font-medium text-gray-900">{{ $booking->hostel->name ?? 'N/A' }}</div>
                            <div class="text-[10px] text-gray-500">Room {{ $booking->room->number ?? 'N/A' }}</div>
                        @else
                            <span class="text-xs text-gray-400">No active booking</span>
                        @endif
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap">
                        @if($occupant->bookings->isNotEmpty())
                            <span class="text-xs text-gray-600">{{ $occupant->bookings->first()->check_in->format('M d, Y') }}</span>
                        @else
                            <span class="text-xs text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap">
                        @if($occupant->bookings->isNotEmpty())
                            @php
                                $checkOut = $occupant->bookings->first()->check_out;
                                $isNearCheckout = $checkOut->diffInDays(now()) <= 3;
                            @endphp
                            <span class="text-xs {{ $isNearCheckout ? 'text-orange-600 font-medium' : 'text-gray-600' }}">
                                {{ $checkOut->format('M d, Y') }}
                                @if($isNearCheckout)
                                    <span class="ml-1 text-[10px] bg-orange-100 text-orange-700 px-1.5 py-0.5 rounded-full">Soon</span>
                                @endif
                            </span>
                        @else
                            <span class="text-xs text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap">
                        @if($occupant->bookings->isNotEmpty())
                            @php
                                $status = $occupant->bookings->first()->status;
                                $statusClass = match($status) {
                                    'confirmed' => 'bg-green-100 text-green-700',
                                    'pending' => 'bg-yellow-100 text-yellow-700',
                                    'cancelled' => 'bg-red-100 text-red-700',
                                    default => 'bg-gray-100 text-gray-700'
                                };
                            @endphp
                            <span class="text-[10px] font-medium px-2 py-0.5 rounded-full {{ $statusClass }}">
                                {{ ucfirst($status) }}
                            </span>
                        @else
                            <span class="text-[10px] bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">No booking</span>
                        @endif
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap">
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('hostel-manager.occupants.show', $occupant) }}"
                               class="text-blue-600 hover:text-blue-800" title="View Details">
                                <i class="fas fa-eye text-xs"></i>
                            </a>
                            <button onclick="contactOccupant({{ $occupant->id }})"
                                    class="text-green-600 hover:text-green-800" title="Contact">
                                <i class="fas fa-envelope text-xs"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-4 py-8 text-center">
                        <div class="flex flex-col items-center">
                            <i class="fas fa-users text-gray-300 text-2xl mb-2"></i>
                            <p class="text-xs text-gray-500 mb-3">No occupants found</p>
                            @if(request('search') || request('gender') || request('hostel_id'))
                                <a href="{{ route('hostel-manager.occupants') }}" class="text-blue-500 hover:text-blue-700 text-xs">
                                    <i class="fas fa-times mr-1"></i> Clear filters
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if(isset($occupants) && $occupants->hasPages())
    <div class="px-4 py-3 border-t border-gray-100">
        {{ $occupants->links() }}
    </div>
    @endif
</div>

<!-- Contact Modal -->
<div id="contactModal" class="modal">
    <div class="modal-content container mx-auto px-4 py-16 max-w-md">
        <div class="bg-white rounded-xl shadow-2xl overflow-hidden">
            <div class="px-4 py-3 bg-gradient-to-r from-blue-500 to-purple-600">
                <h3 class="text-sm font-semibold text-white flex items-center">
                    <i class="fas fa-envelope mr-2 text-xs"></i>
                    Contact Occupant
                </h3>
                <button onclick="closeContactModal()" class="absolute top-3 right-3 text-white hover:text-gray-200">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            <form id="contactForm" method="POST" class="p-4">
                @csrf

                <div class="mb-3">
                    <label class="block text-[10px] font-medium text-gray-500 uppercase mb-1">Recipient</label>
                    <input type="text" id="recipientName" class="w-full px-3 py-2 text-xs bg-gray-50 border border-gray-300 rounded-lg" readonly disabled>
                </div>

                <div class="mb-3">
                    <label class="block text-[10px] font-medium text-gray-500 uppercase mb-1">Subject</label>
                    <input type="text" name="subject" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Enter subject" required>
                </div>

                <div class="mb-3">
                    <label class="block text-[10px] font-medium text-gray-500 uppercase mb-1">Message</label>
                    <textarea name="message" rows="4" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Type your message here..." required></textarea>
                </div>

                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeContactModal()"
                            class="px-3 py-1.5 text-xs border border-gray-300 rounded-lg hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-3 py-1.5 text-xs bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                        Send Message
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentOccupantId = null;

function contactOccupant(occupantId) {
    currentOccupantId = occupantId;

    // Get occupant details via AJAX (you'll need to implement this endpoint)
    fetch(`/hostel-manager/occupants/${occupantId}/details`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('recipientName').value = data.name + ' (' + data.email + ')';
            document.getElementById('contactForm').action = `/hostel-manager/occupants/${occupantId}/contact`;
            document.getElementById('contactModal').style.display = 'block';
            document.body.style.overflow = 'hidden';
        });
}

function closeContactModal() {
    document.getElementById('contactModal').style.display = 'none';
    document.body.style.overflow = 'auto';
    currentOccupantId = null;
}

// Close modal when clicking outside
window.addEventListener('click', function(event) {
    const modal = document.getElementById('contactModal');
    if (event.target === modal) {
        closeContactModal();
    }
});
</script>
@endpush
