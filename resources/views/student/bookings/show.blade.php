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
                            <!-- =========================================== -->
                            <!-- CANCEL BOOKING SECTION WITH MODAL -->
                            <!-- =========================================== -->
                            @if(in_array($currentStatus, ['pending', 'confirmed']) && $booking->payment && $booking->payment->status == 'completed')
                                <!-- Cancel Booking Modal Trigger -->
                                <button onclick="openCancelModal()"
                                        class="w-full px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition flex items-center justify-center">
                                    <i class="fas fa-times-circle mr-2"></i>
                                    Cancel Booking
                                </button>

                                <!-- Cancel Booking Modal -->
                                <div id="cancelModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
                                    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full p-6 max-h-[90vh] overflow-y-auto">
                                        <div class="flex justify-between items-center mb-4 sticky top-0 bg-white pt-2">
                                            <h3 class="text-xl font-bold text-gray-800">Cancel Booking</h3>
                                            <button onclick="closeCancelModal()" class="text-gray-500 hover:text-gray-700">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>

                                        <form action="{{ route('student.bookings.cancel', $booking) }}" method="POST" id="cancelForm">
                                            @csrf
                                            @method('PATCH')

                                            <div class="mb-4">
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    Reason for Cancellation <span class="text-red-500">*</span>
                                                </label>
                                                <textarea name="cancellation_reason"
                                                          rows="4"
                                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                                          placeholder="Please tell us why you're cancelling this booking..."
                                                          required></textarea>
                                                <p class="text-xs text-gray-500 mt-1">Minimum 10 characters</p>
                                            </div>

                                            <!-- Fee Breakdown -->
                                            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                                                <h4 class="font-semibold text-red-800 mb-3 flex items-center">
                                                    <i class="fas fa-exclamation-circle mr-2"></i>
                                                    Cancellation Fee Policy
                                                </h4>

                                                @php
                                                    $originalAmount = $booking->total_amount;
                                                    $agentFee = $originalAmount * 0.05; // 5% agent fee
                                                    $systemCharges = $originalAmount * 0.02; // 2% system charges
                                                    $cancellationFee = $originalAmount * 0.10; // 10% cancellation fee
                                                    $totalDeductions = $agentFee + $systemCharges + $cancellationFee;
                                                    $refundAmount = $originalAmount - $totalDeductions;
                                                @endphp

                                                <div class="space-y-2 text-sm">
                                                    <div class="flex justify-between text-gray-700">
                                                        <span>Original Booking Amount:</span>
                                                        <span class="font-medium">₵{{ number_format($originalAmount, 2) }}</span>
                                                    </div>

                                                    <div class="border-t border-red-200 my-2"></div>

                                                    <div class="flex justify-between text-red-600">
                                                        <span><i class="fas fa-percent mr-1"></i> 10% Cancellation Fee:</span>
                                                        <span>- ₵{{ number_format($cancellationFee, 2) }}</span>
                                                    </div>

                                                    <div class="flex justify-between text-orange-600">
                                                        <span><i class="fas fa-handshake mr-1"></i> 5% Agent Commission:</span>
                                                        <span>- ₵{{ number_format($agentFee, 2) }}</span>
                                                    </div>

                                                    <div class="flex justify-between text-purple-600">
                                                        <span><i class="fas fa-server mr-1"></i> 2% System Charges:</span>
                                                        <span>- ₵{{ number_format($systemCharges, 2) }}</span>
                                                    </div>

                                                    <div class="border-t border-red-200 my-2"></div>

                                                    <div class="flex justify-between font-bold text-lg">
                                                        <span>Total Deductions:</span>
                                                        <span class="text-red-600">₵{{ number_format($totalDeductions, 2) }}</span>
                                                    </div>

                                                    <div class="flex justify-between font-bold text-lg bg-green-50 p-2 rounded-lg mt-2">
                                                        <span>Your Refund Amount:</span>
                                                        <span class="text-green-600">₵{{ number_format($refundAmount, 2) }}</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Important Disclaimer -->
                                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                                                <h4 class="font-semibold text-yellow-800 mb-2 flex items-center">
                                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                                    Important: Please Read Carefully
                                                </h4>

                                                <div class="space-y-3 text-sm text-yellow-700">
                                                    <p class="font-medium">
                                                        ⚠️ By cancelling this booking, you acknowledge and agree to the following:
                                                    </p>

                                                    <ul class="list-disc list-inside space-y-2">
                                                        <li class="flex items-start">
                                                            <i class="fas fa-times-circle text-red-500 mr-2 mt-0.5"></i>
                                                            <span>A <strong>10% cancellation fee (₵{{ number_format($cancellationFee, 2) }})</strong> will be deducted from your refund</span>
                                                        </li>
                                                        <li class="flex items-start">
                                                            <i class="fas fa-times-circle text-red-500 mr-2 mt-0.5"></i>
                                                            <span>The <strong>5% agent commission (₵{{ number_format($agentFee, 2) }})</strong> is non-refundable as it's already paid to the hostel agent</span>
                                                        </li>
                                                        <li class="flex items-start">
                                                            <i class="fas fa-times-circle text-red-500 mr-2 mt-0.5"></i>
                                                            <span><strong>2% system charges (₵{{ number_format($systemCharges, 2) }})</strong> cover payment processing fees incurred by the platform</span>
                                                        </li>
                                                        <li class="flex items-start">
                                                            <i class="fas fa-times-circle text-red-500 mr-2 mt-0.5"></i>
                                                            <span><strong>Total deductions: ₵{{ number_format($totalDeductions, 2) }} ({{ round(($totalDeductions/$originalAmount)*100) }}% of booking amount)</strong></span>
                                                        </li>
                                                        <li class="flex items-start">
                                                            <i class="fas fa-check-circle text-green-500 mr-2 mt-0.5"></i>
                                                            <span>You will receive <strong>₵{{ number_format($refundAmount, 2) }} ({{ round(($refundAmount/$originalAmount)*100) }}% refund)</strong></span>
                                                        </li>
                                                        <li class="flex items-start">
                                                            <i class="fas fa-clock text-blue-500 mr-2 mt-0.5"></i>
                                                            <span>Refund will be processed to your original payment method within 3-5 business days</span>
                                                        </li>
                                                        <li class="flex items-start">
                                                            <i class="fas fa-ban text-red-500 mr-2 mt-0.5"></i>
                                                            <span>This action is <strong>irreversible</strong> and the room will be made available for other students</span>
                                                        </li>
                                                    </ul>

                                                    <div class="bg-red-100 p-3 rounded-lg mt-2">
                                                        <p class="text-sm text-red-800 font-medium">
                                                            <i class="fas fa-calculator mr-2"></i>
                                                            Summary: You paid ₵{{ number_format($originalAmount, 2) }} |
                                                            Deductions: ₵{{ number_format($totalDeductions, 2) }} |
                                                            Refund: ₵{{ number_format($refundAmount, 2) }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Confirmation Checkbox -->
                                            <div class="mb-4">
                                                <label class="flex items-start space-x-3">
                                                    <input type="checkbox" id="confirmCheckbox" class="mt-1 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                                    <span class="text-sm text-gray-600">
                                                        I understand and accept the <strong>10% cancellation fee</strong> and that <strong>agent commission (5%)</strong> and <strong>system charges (2%)</strong> are non-refundable. I acknowledge that I will receive <strong>₵{{ number_format($refundAmount, 2) }}</strong> as my refund.
                                                    </span>
                                                </label>
                                            </div>

                                            <div class="bg-gray-50 p-3 rounded-lg mb-4">
                                                <p class="text-xs text-gray-500">
                                                    <i class="fas fa-info-circle mr-1"></i>
                                                    For questions about our cancellation policy, please contact support at support@ucchostels.com
                                                </p>
                                            </div>

                                            <div class="flex justify-end space-x-3 sticky bottom-0 bg-white pt-3 border-t">
                                                <button type="button"
                                                        onclick="closeCancelModal()"
                                                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                                                    Keep Booking
                                                </button>
                                                <button type="submit"
                                                        id="confirmCancelBtn"
                                                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed"
                                                        disabled>
                                                    Confirm Cancellation
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @elseif(in_array($currentStatus, ['pending', 'confirmed']) && (!$booking->payment || $booking->payment->status != 'completed'))
                                <!-- No payment yet - simple cancellation without refund -->
                                <form action="{{ route('student.bookings.cancel', $booking) }}" method="POST"
                                      onsubmit="return confirmSimpleCancel()">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="cancellation_reason" value="Booking cancelled by user">
                                    <button type="submit"
                                            class="w-full px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition flex items-center justify-center">
                                        <i class="fas fa-times-circle mr-2"></i>
                                        Cancel Booking (No Payment)
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
function openCancelModal() {
    document.getElementById('cancelModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeCancelModal() {
    document.getElementById('cancelModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Enable confirm button only when checkbox is checked and reason is valid
document.addEventListener('DOMContentLoaded', function() {
    const reasonTextarea = document.querySelector('textarea[name="cancellation_reason"]');
    const confirmCheckbox = document.getElementById('confirmCheckbox');
    const confirmBtn = document.getElementById('confirmCancelBtn');

    function checkFormValidity() {
        if (reasonTextarea && confirmCheckbox && confirmBtn) {
            const reasonValid = reasonTextarea.value.length >= 10;
            const checkboxChecked = confirmCheckbox.checked;
            confirmBtn.disabled = !(reasonValid && checkboxChecked);
        }
    }

    if (reasonTextarea) {
        reasonTextarea.addEventListener('input', checkFormValidity);
    }

    if (confirmCheckbox) {
        confirmCheckbox.addEventListener('change', checkFormValidity);
    }
});

function confirmCancellation(refundAmount) {
    const reason = document.querySelector('textarea[name="cancellation_reason"]').value;
    const checkbox = document.getElementById('confirmCheckbox');

    if (reason.length < 10) {
        Swal.fire({
            icon: 'error',
            title: 'Reason Required',
            text: 'Please provide a reason for cancellation (minimum 10 characters)',
            confirmButtonColor: '#3b82f6'
        });
        return false;
    }

    if (!checkbox.checked) {
        Swal.fire({
            icon: 'warning',
            title: 'Please Confirm',
            text: 'You must acknowledge the cancellation fees to proceed.',
            confirmButtonColor: '#3b82f6'
        });
        return false;
    }

    return Swal.fire({
        title: 'Final Confirmation',
        html: `You are about to cancel this booking.<br>
               <span class="text-sm font-semibold text-red-600 mt-2 block">
               You will lose ₵{{ number_format($totalDeductions ?? 0, 2) }} in fees
               </span>
               <span class="text-sm font-semibold text-green-600 block">
               You will receive ₵${refundAmount.toFixed(2)} as refund
               </span>
               <span class="text-xs text-gray-500 mt-2 block">
               This action cannot be undone.
               </span>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, proceed with cancellation',
        cancelButtonText: 'No, keep my booking'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('cancelForm').submit();
        }
        return false;
    });
}

function confirmSimpleCancel() {
    return Swal.fire({
        title: 'Cancel Booking?',
        text: 'Are you sure you want to cancel this booking? This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, cancel booking',
        cancelButtonText: 'No, keep it'
    });
}

// Close modal when clicking outside
document.getElementById('cancelModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeCancelModal();
    }
});

// Auto-hide success/error messages after 5 seconds
setTimeout(function() {
    document.querySelectorAll('.bg-green-100, .bg-red-100').forEach(function(el) {
        el.style.transition = 'opacity 0.5s';
        el.style.opacity = '0';
        setTimeout(function() {
            if (el && el.parentNode) {
                el.remove();
            }
        }, 500);
    });
}, 5000);
</script>
@endpush
@endsection
