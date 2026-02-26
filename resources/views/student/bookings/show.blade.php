@extends('layouts.student')

@section('title', 'Booking Details')
@section('content')

<div class="max-w-4xl mx-auto">
    <!-- Header with Back Button -->
    <div class="mb-4">
        <a href="{{ route('student.bookings') }}" class="inline-flex items-center text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to My Bookings
        </a>
    </div>

    <!-- Main Content -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- Status Banner -->
        @php
            $statusColors = [
                'pending' => 'bg-yellow-500',
                'confirmed' => 'bg-green-500',
                'completed' => 'bg-blue-500',
                'cancelled' => 'bg-red-500',
            ];
            $currentStatus = $booking->booking_status ?? $booking->status ?? 'pending';
            $bannerColor = $statusColors[$currentStatus] ?? 'bg-gray-500';
        @endphp
        <div class="{{ $bannerColor }} px-6 py-4">
            <div class="flex items-center justify-between text-white">
                <div class="flex items-center">
                    <i class="fas fa-calendar-check text-2xl mr-3"></i>
                    <div>
                        <h1 class="text-xl font-bold">Booking #{{ $booking->id }}</h1>
                        <p class="text-sm opacity-90">Reference: {{ $booking->booking_reference ?? 'N/A' }}</p>
                    </div>
                </div>
                <span class="px-4 py-2 bg-white bg-opacity-20 rounded-full text-sm font-semibold">
                    {{ ucfirst($currentStatus) }}
                </span>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="m-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="m-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
            </div>
        @endif

        <div class="p-6">
            <!-- Booking Details Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Left Column - Booking Info -->
                <div class="space-y-6">
                    <!-- Dates Card -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                            <i class="fas fa-calendar-alt text-blue-500 mr-2"></i>
                            Stay Dates
                        </h3>
                        <div class="flex items-center justify-between">
                            <div class="text-center flex-1">
                                <p class="text-sm text-gray-500">Check In</p>
                                <p class="text-xl font-bold text-gray-800">{{ $booking->check_in->format('M d, Y') }}</p>
                                <p class="text-xs text-gray-400">{{ $booking->check_in->format('l') }}</p>
                            </div>
                            <div class="text-gray-400">
                                <i class="fas fa-arrow-right text-2xl"></i>
                            </div>
                            <div class="text-center flex-1">
                                <p class="text-sm text-gray-500">Check Out</p>
                                <p class="text-xl font-bold text-gray-800">{{ $booking->check_out->format('M d, Y') }}</p>
                                <p class="text-xs text-gray-400">{{ $booking->check_out->format('l') }}</p>
                            </div>
                        </div>
                        <div class="mt-3 text-center text-sm text-gray-600">
                            <i class="far fa-clock mr-1"></i>
                            Duration: {{ $booking->check_in->diffInDays($booking->check_out) }} nights
                        </div>
                    </div>

                    <!-- Hostel Card -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                            <i class="fas fa-building text-purple-500 mr-2"></i>
                            Hostel Information
                        </h3>
                        @if($booking->hostel)
                            <div class="flex items-start space-x-4">
                                @if($booking->hostel->primaryImage)
                                    <img src="{{ Storage::url($booking->hostel->primaryImage->path) }}"
                                         alt="{{ $booking->hostel->name }}"
                                         class="w-20 h-20 object-cover rounded-lg">
                                @else
                                    <div class="w-20 h-20 bg-gray-200 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-building text-gray-400 text-2xl"></i>
                                    </div>
                                @endif
                                <div class="flex-1">
                                    <h4 class="font-bold text-gray-800">{{ $booking->hostel->name }}</h4>
                                    <p class="text-sm text-gray-600 mt-1">
                                        <i class="fas fa-map-marker-alt text-gray-400 mr-1"></i>
                                        {{ $booking->hostel->location }}
                                    </p>
                                    @if($booking->hostel->contact_phone)
                                        <p class="text-sm text-gray-600 mt-1">
                                            <i class="fas fa-phone text-gray-400 mr-1"></i>
                                            {{ $booking->hostel->contact_phone }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @else
                            <p class="text-gray-500">Hostel information unavailable</p>
                        @endif
                    </div>

                    <!-- Room Card -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                            <i class="fas fa-door-open text-green-500 mr-2"></i>
                            Room Details
                        </h3>
                        @if($booking->room)
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-500">Room Number</p>
                                    <p class="text-lg font-bold text-gray-800">{{ $booking->room->number }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Floor</p>
                                    <p class="text-lg font-bold text-gray-800">{{ $booking->room->floor ?? 'Ground' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Capacity</p>
                                    <p class="text-lg font-bold text-gray-800">{{ $booking->room->capacity }} persons</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Gender</p>
                                    <p class="text-lg font-bold text-gray-800">{{ ucfirst($booking->room->gender) }}</p>
                                </div>
                            </div>
                            @if($booking->room->description)
                                <p class="mt-3 text-sm text-gray-600">{{ $booking->room->description }}</p>
                            @endif
                        @else
                            <p class="text-gray-500">Room information unavailable</p>
                        @endif
                    </div>
                </div>

                <!-- Right Column - Payment & Actions -->
                <div class="space-y-6">
                    <!-- Payment Summary Card -->
                    <div class="bg-gradient-to-br from-blue-50 to-purple-50 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-credit-card text-blue-500 mr-2"></i>
                            Payment Summary
                        </h3>

                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Room Rate</span>
                                <span class="font-medium">₵{{ number_format($booking->room->price_per_month ?? 0, 2) }}/month</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Duration</span>
                                <span class="font-medium">{{ $booking->check_in->diffInDays($booking->check_out) }} nights</span>
                            </div>
                            <div class="border-t border-gray-300 my-3"></div>
                            <div class="flex justify-between">
                                <span class="text-lg font-semibold text-gray-800">Total Amount</span>
                                <span class="text-2xl font-bold text-blue-600">₵{{ number_format($booking->total_amount, 2) }}</span>
                            </div>

                            @if($booking->payment)
                                <div class="mt-4 p-3 bg-white rounded-lg">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm text-gray-600">Payment Status</span>
                                        @php
                                            $paymentStatusColor = $booking->payment->status == 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800';
                                        @endphp
                                        <span class="px-3 py-1 text-xs rounded-full {{ $paymentStatusColor }}">
                                            {{ ucfirst($booking->payment->status) }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-500">Transaction ID: {{ $booking->payment->transaction_id }}</p>
                                    <p class="text-xs text-gray-500 mt-1">Paid on: {{ $booking->payment->created_at->format('M d, Y h:i A') }}</p>
                                </div>
                            @else
                                <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                    <p class="text-sm text-yellow-700">
                                        <i class="fas fa-exclamation-triangle mr-2"></i>
                                        Payment pending
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Quick Actions Card -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-bolt text-yellow-500 mr-2"></i>
                            Quick Actions
                        </h3>

                        <div class="space-y-3">
                            <!-- Cancel Booking Button -->
                            @if(in_array($currentStatus, ['pending', 'confirmed']))
                                <form action="{{ route('student.bookings.cancel', $booking) }}" method="POST"
                                      onsubmit="return confirmCancel()">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            class="w-full px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition flex items-center justify-center">
                                        <i class="fas fa-times-circle mr-2"></i>
                                        Cancel Booking
                                    </button>
                                </form>
                            @endif

                            <!-- Pay Now Button -->
                            @if($currentStatus == 'pending' && !$booking->payment)
                                <a href="{{ route('payment.initialize', $booking) }}"
                                   class="block w-full px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-center">
                                    <i class="fas fa-credit-card mr-2"></i>
                                    Pay Now
                                </a>
                            @endif

                            <!-- Download Receipt -->
                            @if($booking->payment && $booking->payment->status == 'completed')
                                <a href="{{ route('student.payments.receipt', $booking->payment) }}"
                                   class="block w-full px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-center">
                                    <i class="fas fa-download mr-2"></i>
                                    Download Receipt
                                </a>
                            @endif

                            <!-- Report Issue -->
                            <a href="{{ route('student.complaints.create', ['booking' => $booking->id]) }}"
                               class="block w-full px-4 py-3 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition text-center">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                Report an Issue
                            </a>

                            <!-- View Hostel -->
                            @if($booking->hostel)
                                <a href="{{ route('student.hostels.show', $booking->hostel) }}"
                                   class="block w-full px-4 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 transition text-center">
                                    <i class="fas fa-building mr-2"></i>
                                    View Hostel Details
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Important Information -->
                    <div class="bg-blue-50 rounded-lg p-4">
                        <h4 class="font-semibold text-blue-800 mb-2 flex items-center">
                            <i class="fas fa-info-circle mr-2"></i>
                            Important Information
                        </h4>
                        <ul class="text-sm text-blue-700 space-y-2">
                            <li><i class="fas fa-check mr-2"></i>Check-in time: 2:00 PM</li>
                            <li><i class="fas fa-check mr-2"></i>Check-out time: 12:00 PM</li>
                            <li><i class="fas fa-check mr-2"></i>Please bring a valid ID for check-in</li>
                            @if($booking->hostel && $booking->hostel->rules)
                                @foreach($booking->hostel->rules as $rule)
                                    <li><i class="fas fa-check mr-2"></i>{{ $rule }}</li>
                                @endforeach
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function confirmCancel() {
    return Swal.fire({
        title: 'Cancel Booking?',
        text: 'Are you sure you want to cancel this booking? This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, cancel booking',
        cancelButtonText: 'No, keep it'
    }).then((result) => {
        return result.isConfirmed;
    });
}

// Auto-hide success/error messages after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        document.querySelectorAll('.bg-green-100, .bg-red-100').forEach(function(el) {
            el.style.transition = 'opacity 0.5s';
            el.style.opacity = '0';
            setTimeout(function() {
                el.remove();
            }, 500);
        });
    }, 5000);
});
</script>
@endpush
@endsection
