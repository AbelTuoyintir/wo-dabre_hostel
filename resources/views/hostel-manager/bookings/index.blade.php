@extends('hostel-manager.layouts.manager')

@section('title', 'Bookings Management')
@section('page-title', 'Bookings Management')

@section('content')
<!-- Header with Actions -->
<div class="mb-4 bg-white rounded-xl shadow-sm border border-gray-100 p-4">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="flex items-center">
            <div class="bg-blue-50 p-2 rounded-lg mr-3">
                <i class="fas fa-calendar-check text-blue-500 text-sm"></i>
            </div>
            <div>
                <h2 class="text-sm font-semibold text-gray-800">Booking Management</h2>
                <p class="text-xs text-gray-500">Manage and monitor all bookings</p>
            </div>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('hostel-manager.bookings.export') }}"
               class="border border-gray-300 hover:bg-gray-50 text-gray-700 text-xs px-3 py-1.5 rounded-lg transition flex items-center">
                <i class="fas fa-download mr-1 text-xs"></i>
                Export
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
            <span class="text-xs text-gray-500 block">Pending</span>
            <span class="text-sm font-bold text-yellow-600">{{ $stats['pending'] ?? 0 }}</span>
        </div>
        <div class="text-center">
            <span class="text-xs text-gray-500 block">Confirmed</span>
            <span class="text-sm font-bold text-green-600">{{ $stats['confirmed'] ?? 0 }}</span>
        </div>
        <div class="text-center">
            <span class="text-xs text-gray-500 block">Completed</span>
            <span class="text-sm font-bold text-blue-600">{{ $stats['completed'] ?? 0 }}</span>
        </div>
        <div class="text-center">
            <span class="text-xs text-gray-500 block">Cancelled</span>
            <span class="text-sm font-bold text-red-600">{{ $stats['cancelled'] ?? 0 }}</span>
        </div>
    </div>

    <!-- Today's Schedule -->
    <div class="grid grid-cols-2 gap-3 mt-3 pt-3 border-t border-gray-100">
        <div class="flex items-center justify-between bg-green-50 p-2 rounded-lg">
            <span class="text-xs text-green-700 flex items-center">
                <i class="fas fa-sign-in-alt mr-1"></i> Today's Check-ins
            </span>
            <span class="text-sm font-bold text-green-700">{{ $stats['today_checkins'] ?? 0 }}</span>
        </div>
        <div class="flex items-center justify-between bg-orange-50 p-2 rounded-lg">
            <span class="text-xs text-orange-700 flex items-center">
                <i class="fas fa-sign-out-alt mr-1"></i> Today's Check-outs
            </span>
            <span class="text-sm font-bold text-orange-700">{{ $stats['today_checkouts'] ?? 0 }}</span>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="mb-4 bg-white rounded-xl shadow-sm border border-gray-100 p-4">
    <form method="GET" action="{{ route('hostel-manager.bookings') }}" class="flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-[10px] font-medium text-gray-500 uppercase mb-1">Search</label>
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search by student name, email or room..."
                       class="w-full pl-8 pr-3 py-1.5 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>

        <div class="w-32">
            <label class="block text-[10px] font-medium text-gray-500 uppercase mb-1">Status</label>
            <select name="status" class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                <option value="">All</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
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

        <div class="w-32">
            <label class="block text-[10px] font-medium text-gray-500 uppercase mb-1">Date From</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}"
                   class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div class="w-32">
            <label class="block text-[10px] font-medium text-gray-500 uppercase mb-1">Date To</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}"
                   class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
        </div>

        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white text-xs px-4 py-1.5 rounded-lg transition">
            <i class="fas fa-filter mr-1"></i> Filter
        </button>

        <a href="{{ route('hostel-manager.bookings') }}" class="border border-gray-300 hover:bg-gray-50 text-gray-700 text-xs px-4 py-1.5 rounded-lg transition">
            <i class="fas fa-times mr-1"></i> Clear
        </a>
    </form>
</div>

<!-- Bookings Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase tracking-wider">Student</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase tracking-wider">Room</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase tracking-wider">Check In</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase tracking-wider">Check Out</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase tracking-wider">Payment</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($bookings as $booking)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2 whitespace-nowrap">
                        <span class="text-xs font-medium text-gray-900">#{{ $booking->id }}</span>
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center mr-2 flex-shrink-0">
                                <span class="text-[10px] font-medium text-blue-700">{{ substr($booking->user->name ?? 'U', 0, 1) }}</span>
                            </div>
                            <div class="min-w-0">
                                <div class="text-xs font-medium text-gray-900 truncate">{{ $booking->user->name ?? 'Unknown' }}</div>
                                <div class="text-[10px] text-gray-500 truncate">{{ $booking->user->student_id ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap">
                        <div class="text-xs text-gray-900">{{ $booking->room->number ?? 'N/A' }}</div>
                        <div class="text-[10px] text-gray-500">{{ $booking->hostel->name ?? 'N/A' }}</div>
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap">
                        <span class="text-xs text-gray-600">{{ $booking->check_in->format('M d, Y') }}</span>
                        @if($booking->check_in->isToday())
                            <span class="ml-1 text-[10px] bg-green-100 text-green-700 px-1.5 py-0.5 rounded-full">Today</span>
                        @endif
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap">
                        <span class="text-xs text-gray-600">{{ $booking->check_out->format('M d, Y') }}</span>
                        @if($booking->check_out->isToday())
                            <span class="ml-1 text-[10px] bg-orange-100 text-orange-700 px-1.5 py-0.5 rounded-full">Today</span>
                        @endif
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap">
                        <span class="text-xs font-medium text-gray-900">₵{{ number_format($booking->total_amount, 2) }}</span>
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap">
                        @php
                            $paymentStatus = $booking->payment->status ?? 'pending';
                            $paymentClass = match($paymentStatus) {
                                'completed' => 'bg-green-100 text-green-700',
                                'pending' => 'bg-yellow-100 text-yellow-700',
                                'failed' => 'bg-red-100 text-red-700',
                                default => 'bg-gray-100 text-gray-700'
                            };
                        @endphp
                        <span class="text-[10px] font-medium px-2 py-0.5 rounded-full {{ $paymentClass }}">
                            {{ ucfirst($paymentStatus) }}
                        </span>
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap">
                        @php
                            $statusClass = match($booking->status) {
                                'pending' => 'bg-yellow-100 text-yellow-700',
                                'confirmed' => 'bg-green-100 text-green-700',
                                'completed' => 'bg-blue-100 text-blue-700',
                                'cancelled' => 'bg-red-100 text-red-700',
                                default => 'bg-gray-100 text-gray-700'
                            };
                        @endphp
                        <span class="text-[10px] font-medium px-2 py-0.5 rounded-full {{ $statusClass }}">
                            {{ ucfirst($booking->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap">
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('hostel-manager.bookings.show', $booking) }}"
                               class="text-blue-600 hover:text-blue-800" title="View Details">
                                <i class="fas fa-eye text-xs"></i>
                            </a>
                            @if(in_array($booking->status, ['pending', 'confirmed']))
                            <button onclick="updateBookingStatus({{ $booking->id }})"
                                    class="text-green-600 hover:text-green-800" title="Update Status">
                                <i class="fas fa-edit text-xs"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-4 py-8 text-center">
                        <div class="flex flex-col items-center">
                            <i class="fas fa-calendar-times text-gray-300 text-2xl mb-2"></i>
                            <p class="text-xs text-gray-500 mb-3">No bookings found</p>
                            @if(request('search') || request('status') || request('hostel_id'))
                                <a href="{{ route('hostel-manager.bookings') }}" class="text-blue-500 hover:text-blue-700 text-xs">
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
    @if(isset($bookings) && $bookings->hasPages())
    <div class="px-4 py-3 border-t border-gray-100">
        {{ $bookings->links() }}
    </div>
    @endif
</div>

<!-- Update Status Modal -->
<div id="statusModal" class="modal">
    <div class="modal-content container mx-auto px-4 py-16 max-w-md">
        <div class="bg-white rounded-xl shadow-2xl overflow-hidden">
            <div class="px-4 py-3 bg-gradient-to-r from-blue-500 to-purple-600">
                <h3 class="text-sm font-semibold text-white flex items-center">
                    <i class="fas fa-edit mr-2 text-xs"></i>
                    Update Booking Status
                </h3>
                <button onclick="closeStatusModal()" class="absolute top-3 right-3 text-white hover:text-gray-200">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            <form id="statusForm" method="POST" class="p-4">
                @csrf
                @method('PATCH')

                <div class="mb-3">
                    <label class="block text-[10px] font-medium text-gray-500 uppercase mb-1">Status</label>
                    <select name="status" id="bookingStatus" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirm Booking</option>
                        <option value="completed">Mark as Completed</option>
                        <option value="cancelled">Cancel Booking</option>
                    </select>
                </div>

                <div class="mb-3" id="cancellationReason" style="display: none;">
                    <label class="block text-[10px] font-medium text-gray-500 uppercase mb-1">Cancellation Reason</label>
                    <textarea name="cancellation_reason" rows="2" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Enter reason for cancellation..."></textarea>
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
let currentBookingId = null;

function updateBookingStatus(bookingId) {
    currentBookingId = bookingId;
    document.getElementById('statusForm').action = `/hostel-manager/bookings/${bookingId}/status`;
    document.getElementById('statusModal').style.display = 'block';
    document.body.style.overflow = 'hidden';
}

function closeStatusModal() {
    document.getElementById('statusModal').style.display = 'none';
    document.body.style.overflow = 'auto';
    document.getElementById('cancellationReason').style.display = 'none';
    currentBookingId = null;
}

// Show/hide cancellation reason based on status selection
document.getElementById('bookingStatus')?.addEventListener('change', function() {
    const reasonDiv = document.getElementById('cancellationReason');
    if (this.value === 'cancelled') {
        reasonDiv.style.display = 'block';
    } else {
        reasonDiv.style.display = 'none';
    }
});

// Close modal when clicking outside
window.addEventListener('click', function(event) {
    const modal = document.getElementById('statusModal');
    if (event.target === modal) {
        closeStatusModal();
    }
});
</script>
@endpush
