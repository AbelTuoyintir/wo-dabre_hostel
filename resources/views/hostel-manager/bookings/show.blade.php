@extends('layouts.hostelmanage')

@section('title', 'Booking Details')
@section('page-title', 'Booking #' . $booking->id)

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Header -->
    <div class="mb-4 bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex items-center">
            <a href="{{ route('hostel-manager.bookings') }}" class="text-gray-500 hover:text-gray-700 mr-3">
                <i class="fas fa-arrow-left text-xs"></i>
            </a>
            <div>
                <h2 class="text-sm font-semibold text-gray-800">Booking #{{ $booking->id }}</h2>
                <p class="text-xs text-gray-500">{{ $booking->created_at->format('F d, Y \a\t h:i A') }}</p>
            </div>
            <div class="ml-auto flex items-center space-x-2">
                @php
                    $statusClass = match($booking->status) {
                        'pending' => 'bg-yellow-100 text-yellow-700',
                        'confirmed' => 'bg-green-100 text-green-700',
                        'completed' => 'bg-blue-100 text-blue-700',
                        'cancelled' => 'bg-red-100 text-red-700',
                        default => 'bg-gray-100 text-gray-700'
                    };
                @endphp
                <span class="text-[10px] font-medium px-2 py-1 rounded-full {{ $statusClass }}">
                    {{ ucfirst($booking->status) }}
                </span>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Left Column - Main Details -->
        <div class="lg:col-span-2 space-y-4">
            <!-- Student Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-4 py-3 bg-blue-50 border-b border-blue-100">
                    <h3 class="text-xs font-semibold text-blue-700 uppercase flex items-center">
                        <i class="fas fa-user mr-1.5"></i>
                        Student Information
                    </h3>
                </div>
                <div class="p-4">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                            <span class="text-sm font-medium text-blue-700">{{ substr($booking->user->name ?? 'U', 0, 1) }}</span>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-gray-900">{{ $booking->user->name ?? 'Unknown' }}</h4>
                            <p class="text-xs text-gray-500">{{ $booking->user->email ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase">Student ID</p>
                            <p class="text-sm text-gray-900">{{ $booking->user->student_id ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase">Phone</p>
                            <p class="text-sm text-gray-900">{{ $booking->user->phone ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Booking Details -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-4 py-3 bg-purple-50 border-b border-purple-100">
                    <h3 class="text-xs font-semibold text-purple-700 uppercase flex items-center">
                        <i class="fas fa-calendar-alt mr-1.5"></i>
                        Booking Details
                    </h3>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase">Hostel</p>
                            <p class="text-sm font-medium text-gray-900">{{ $booking->hostel->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase">Room</p>
                            <p class="text-sm font-medium text-gray-900">{{ $booking->room->number ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase">Check In</p>
                            <p class="text-sm text-gray-900">{{ $booking->check_in->format('M d, Y') }}</p>
                            @if($booking->check_in->isToday())
                                <span class="text-[10px] bg-green-100 text-green-700 px-1.5 py-0.5 rounded-full">Today</span>
                            @endif
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase">Check Out</p>
                            <p class="text-sm text-gray-900">{{ $booking->check_out->format('M d, Y') }}</p>
                            @if($booking->check_out->isToday())
                                <span class="text-[10px] bg-orange-100 text-orange-700 px-1.5 py-0.5 rounded-full">Today</span>
                            @endif
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase">Duration</p>
                            <p class="text-sm text-gray-900">{{ $booking->check_in->diffInDays($booking->check_out) }} nights</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase">Total Amount</p>
                            <p class="text-sm font-bold text-green-600">₵{{ number_format($booking->total_amount, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment History -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-4 py-3 bg-green-50 border-b border-green-100">
                    <h3 class="text-xs font-semibold text-green-700 uppercase flex items-center">
                        <i class="fas fa-credit-card mr-1.5"></i>
                        Payment Information
                    </h3>
                </div>
                <div class="p-4">
                    @if($booking->payment)
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-600">Payment Method</span>
                            <span class="text-xs font-medium">{{ ucfirst($booking->payment->payment_method ?? 'N/A') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-600">Transaction ID</span>
                            <span class="text-xs font-mono">{{ $booking->payment->transaction_id ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-600">Payment Date</span>
                            <span class="text-xs">{{ $booking->payment->created_at->format('M d, Y h:i A') ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-600">Status</span>
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
                        </div>
                    </div>
                    @else
                    <p class="text-sm text-gray-500 text-center py-2">No payment record found</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column - Actions & Timeline -->
        <div class="space-y-4">
            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
                    <h3 class="text-xs font-semibold text-gray-700 uppercase">Quick Actions</h3>
                </div>
                <div class="p-3 space-y-2">
                    @if(in_array($booking->status, ['pending', 'confirmed']))
                    <button onclick="updateBookingStatus({{ $booking->id }})"
                            class="w-full text-left px-3 py-2 text-xs bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition flex items-center">
                        <i class="fas fa-edit mr-2"></i>
                        Update Status
                    </button>
                    @endif

                    @if($booking->status == 'pending')
                    <form action="{{ route('hostel-manager.bookings.status', $booking) }}" method="POST" class="inline-block w-full">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="confirmed">
                        <button type="submit" onclick="return confirm('Confirm this booking?')"
                                class="w-full text-left px-3 py-2 text-xs bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition flex items-center">
                            <i class="fas fa-check-circle mr-2"></i>
                            Confirm Booking
                        </button>
                    </form>
                    @endif

                    <a href="#" class="block w-full text-left px-3 py-2 text-xs bg-purple-50 text-purple-700 rounded-lg hover:bg-purple-100 transition flex items-center">
                        <i class="fas fa-print mr-2"></i>
                        Print Receipt
                    </a>

                    @if($booking->status != 'cancelled')
                    <form action="{{ route('hostel-manager.bookings.destroy', $booking) }}" method="POST" class="inline-block w-full"
                          onsubmit="return confirm('Are you sure you want to delete this booking?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="w-full text-left px-3 py-2 text-xs bg-red-50 text-red-700 rounded-lg hover:bg-red-100 transition flex items-center">
                            <i class="fas fa-trash-alt mr-2"></i>
                            Delete Booking
                        </button>
                    </form>
                    @endif
                </div>
            </div>

            <!-- Booking Timeline -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
                    <h3 class="text-xs font-semibold text-gray-700 uppercase">Timeline</h3>
                </div>
                <div class="p-3">
                    <div class="space-y-3">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 mr-2">
                                <div class="w-5 h-5 rounded-full bg-green-100 flex items-center justify-center">
                                    <i class="fas fa-plus text-green-600 text-[10px]"></i>
                                </div>
                            </div>
                            <div>
                                <p class="text-xs text-gray-900">Booking Created</p>
                                <p class="text-[10px] text-gray-500">{{ $booking->created_at->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>

                        @if($booking->status == 'confirmed' || $booking->status == 'completed')
                        <div class="flex items-start">
                            <div class="flex-shrink-0 mr-2">
                                <div class="w-5 h-5 rounded-full bg-blue-100 flex items-center justify-center">
                                    <i class="fas fa-check text-blue-600 text-[10px]"></i>
                                </div>
                            </div>
                            <div>
                                <p class="text-xs text-gray-900">Booking Confirmed</p>
                                <p class="text-[10px] text-gray-500">{{ $booking->updated_at->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>
                        @endif

                        @if($booking->status == 'completed')
                        <div class="flex items-start">
                            <div class="flex-shrink-0 mr-2">
                                <div class="w-5 h-5 rounded-full bg-purple-100 flex items-center justify-center">
                                    <i class="fas fa-check-double text-purple-600 text-[10px]"></i>
                                </div>
                            </div>
                            <div>
                                <p class="text-xs text-gray-900">Stay Completed</p>
                                <p class="text-[10px] text-gray-500">{{ $booking->check_out->format('M d, Y') }}</p>
                            </div>
                        </div>
                        @endif

                        @if($booking->status == 'cancelled')
                        <div class="flex items-start">
                            <div class="flex-shrink-0 mr-2">
                                <div class="w-5 h-5 rounded-full bg-red-100 flex items-center justify-center">
                                    <i class="fas fa-times text-red-600 text-[10px]"></i>
                                </div>
                            </div>
                            <div>
                                <p class="text-xs text-gray-900">Booking Cancelled</p>
                                <p class="text-[10px] text-gray-500">{{ $booking->updated_at->format('M d, Y h:i A') }}</p>
                                @if($booking->cancellation_reason)
                                    <p class="text-[10px] text-gray-500 mt-1">Reason: {{ $booking->cancellation_reason }}</p>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Update Status Modal (same as in index) -->
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
