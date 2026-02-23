@extends('layouts.hotelmanage')

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4">
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-purple-600">
            <h2 class="text-xl font-bold text-white">Complete Your Booking</h2>
        </div>

        <div class="p-6">
            <!-- Room Summary -->
            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ $room->hostel->name }}</h3>
                <p class="text-gray-600">Room {{ $room->number }} • {{ $room->capacity }} persons</p>
                <p class="text-blue-600 font-bold mt-2">₵{{ number_format($room->price_per_month, 2) }}/month</p>
            </div>

            <form action="{{ route('booking.store') }}" method="POST" id="bookingForm">
                @csrf
                <input type="hidden" name="room_id" value="{{ $room->id }}">

                <!-- Dates Selection -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Check-in Date</label>
                        <input type="date" name="check_in" id="check_in" 
                               min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                               required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Check-out Date</label>
                        <input type="date" name="check_out" id="check_out"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                               required>
                    </div>
                </div>

                <!-- Guest/User Information -->
                @guest
                    <div class="mb-6 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                        <h4 class="text-sm font-semibold text-yellow-800 mb-3">Complete Your Details</h4>
                        <p class="text-xs text-yellow-700 mb-3">
                            <i class="fas fa-info-circle mr-1"></i>
                            An account will be created for you. Login credentials will be sent to your email.
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                                <input type="text" name="name" value="{{ old('name') }}" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                       required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                                <input type="email" name="email" value="{{ old('email') }}" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                       required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number *</label>
                                <input type="tel" name="phone" value="{{ old('phone') }}" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                       placeholder="e.g., 024XXXXXXX"
                                       required>
                            </div>
                        </div>
                        <div class="mt-3 p-2 bg-blue-50 rounded border border-blue-200">
                            <p class="text-xs text-blue-700">
                                <i class="fas fa-lock mr-1"></i>
                                A secure password will be generated and sent to your email after successful payment.
                            </p>
                        </div>
                    </div>
                @else
                    <input type="hidden" name="is_authenticated" value="true">
                    <div class="mb-6 p-4 bg-blue-50 rounded-lg">
                        <p class="text-sm text-blue-700">
                            <i class="fas fa-user-check mr-2"></i>
                            Booking as <strong>{{ Auth::user()->name }}</strong> ({{ Auth::user()->email }})
                        </p>
                    </div>
                @endguest

                <!-- Price Summary -->
                <div class="mb-6 p-4 bg-gray-50 rounded-lg" id="priceSummary" style="display: none;">
                    <h4 class="font-semibold text-gray-700 mb-2">Price Summary</h4>
                    <div class="flex justify-between text-sm">
                        <span>Duration:</span>
                        <span id="durationDisplay"></span>
                    </div>
                    <div class="flex justify-between text-sm mt-1">
                        <span>Room rate:</span>
                        <span>₵{{ number_format($room->price_per_month, 2) }}/month</span>
                    </div>
                    <div class="flex justify-between text-sm mt-1">
                        <span>Total amount:</span>
                        <span id="totalAmount" class="font-bold text-blue-600"></span>
                    </div>
                    <div class="flex justify-between text-xs text-gray-500 mt-2 pt-2 border-t">
                        <span>Processing fee (1.95%):</span>
                        <span id="feeAmount"></span>
                    </div>
                </div>

                <button type="submit" id="submitBtn" disabled
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed">
                    Proceed to Payment
                </button>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkIn = document.getElementById('check_in');
    const checkOut = document.getElementById('check_out');
    const priceSummary = document.getElementById('priceSummary');
    const submitBtn = document.getElementById('submitBtn');
    const monthlyRate = {{ $room->price_per_month }};

    function calculateTotal() {
        if (checkIn.value && checkOut.value) {
            const start = new Date(checkIn.value);
            const end = new Date(checkOut.value);
            const diffTime = Math.abs(end - start);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            
            if (diffDays > 0) {
                const totalAmount = (monthlyRate / 30) * diffDays;
                const fee = totalAmount * 0.0195;
                
                document.getElementById('durationDisplay').textContent = diffDays + ' nights';
                document.getElementById('totalAmount').textContent = '₵' + totalAmount.toFixed(2);
                document.getElementById('feeAmount').textContent = '₵' + fee.toFixed(2);
                
                priceSummary.style.display = 'block';
                submitBtn.disabled = false;
            }
        }
    }

    checkIn.addEventListener('change', calculateTotal);
    checkOut.addEventListener('change', calculateTotal);
});
</script>
@endsection