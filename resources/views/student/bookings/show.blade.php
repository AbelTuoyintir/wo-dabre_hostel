@extends('layouts.student')

@section('title', 'Booking Details')
@section('content')

<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <a href="{{ route('student.bookings') }}" class="text-gray-500 hover:text-gray-700 mr-4">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Booking Details</h1>
                    <p class="text-gray-500">Reference: {{ $booking->booking_reference }}</p>
                </div>
            </div>
            @php
                $statusColors = [
                    'pending' => 'bg-yellow-100 text-yellow-800',
                    'confirmed' => 'bg-green-100 text-green-800',
                    'cancelled' => 'bg-red-100 text-red-800',
                    'completed' => 'bg-blue-100 text-blue-800',
                ];
                $statusColor = $statusColors[$booking->status] ?? 'bg-gray-100 text-gray-800';
            @endphp
            <span class="px-4 py-2 rounded-full text-sm font-semibold {{ $statusColor }}">
                {{ ucfirst($booking->status) }}
            </span>
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Left Column - Booking Info -->
        <div class="md:col-span-2 space-y-6">
            <!-- Hostel & Room Details -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Accommodation Details</h2>
                
                <div class="flex items-start space-x-4 mb-4">
                    @if($booking->hostel && $booking->hostel->primaryImage)
                        <img src="{{ Storage::url($booking->hostel->primaryImage->path) }}" 
                             alt="{{ $booking->hostel->name }}"
                             class="w-20 h-20 object-cover rounded-lg">
                    @endif
                    <div>
                        <h3 class="font-bold text-gray-900">{{ $booking->hostel->name }}</h3>
                        <p class="text-sm text-gray-600">{{ $booking->hostel->location }}</p>
                        <p class="text-sm text-gray-600 mt-1">Room {{ $booking->room->number }} ({{ $booking->room->capacity }} persons)</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mt-4 pt-4 border-t">
                    <div>
                        <p class="text-sm text-gray-500">Check In</p>
                        <p class="font-semibold">{{ $booking->check_in->format('F d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Check Out</p>
                        <p class="font-semibold">{{ $booking->check_out->format('F d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Duration</p>
                        <p class="font-semibold">{{ $booking->check_in->diffInDays($booking->check_out) }} nights</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Booked On</p>
                        <p class="font-semibold">{{ $booking->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>

            <!-- Payment Details -->
            @if($booking->payment)
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Payment Details</h2>
                
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Amount Paid</span>
                        <span class="font-bold text-green-600">₵{{ number_format($booking->payment->amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Transaction ID</span>
                        <span class="font-mono text-sm">{{ $booking->payment->transaction_id }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Payment Method</span>
                        <span>{{ ucfirst($booking->payment->payment_method) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Payment Date</span>
                        <span>{{ $booking->payment->created_at->format('M d, Y h:i A') }}</span>
                    </div>
                </div>

                <a href="{{ route('student.payments.receipt', $booking->payment) }}" 
                   class="mt-4 inline-block text-blue-600 hover:text-blue-800">
                    <i class="fas fa-download mr-1"></i>Download Receipt
                </a>
            </div>
            @endif
        </div>

        <!-- Right Column - Actions -->
        <div class="space-y-6">
            <!-- Status Timeline -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Timeline</h2>
                
                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 mr-3">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-plus text-blue-600 text-sm"></i>
                            </div>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Booking Created</p>
                            <p class="text-sm text-gray-500">{{ $booking->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                    
                    @if($booking->status == 'confirmed' || $booking->status == 'completed')
                    <div class="flex items-start">
                        <div class="flex-shrink-0 mr-3">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-check text-green-600 text-sm"></i>
                            </div>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Payment Confirmed</p>
                            <p class="text-sm text-gray-500">{{ $booking->updated_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Actions</h2>
                
                <div class="space-y-3">
                    @if($booking->status == 'pending')
                        <a href="{{ route('payment.callback', ['gateway' => 'paystack', 'booking_id' => $booking->id]) }}" 
                           class="block w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-center">
                            <i class="fas fa-credit-card mr-2"></i>Complete Payment
                        </a>
                    @endif

                    @if(in_array($booking->status, ['pending', 'confirmed']))
                        <button onclick="cancelBooking({{ $booking->id }})" 
                                class="block w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-center">
                            <i class="fas fa-times-circle mr-2"></i>Cancel Booking
                        </button>
                    @endif

                    <a href="{{ route('student.complaints.create', ['booking' => $booking->id]) }}" 
                       class="block w-full px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition text-center">
                        <i class="fas fa-exclamation-triangle mr-2"></i>Report Issue
                    </a>
                </div>
            </div>

            <!-- Important Info -->
            <div class="bg-blue-50 rounded-lg p-4">
                <h3 class="font-semibold text-blue-800 mb-2">Important Information</h3>
                <ul class="text-sm text-blue-700 space-y-1">
                    <li><i class="fas fa-check mr-2"></i>Check-in: 2:00 PM</li>
                    <li><i class="fas fa-check mr-2"></i>Check-out: 12:00 PM</li>
                    <li><i class="fas fa-check mr-2"></i>Bring valid ID for check-in</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function cancelBooking(bookingId) {
    Swal.fire({
        title: 'Cancel Booking?',
        text: 'Are you sure you want to cancel this booking?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, cancel',
        cancelButtonText: 'No, keep it'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/student/bookings/${bookingId}/cancel`;
            form.innerHTML = '@csrf @method("PATCH")';
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
@endpush
@endsection