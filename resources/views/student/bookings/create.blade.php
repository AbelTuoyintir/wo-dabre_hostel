@extends('layouts.student')

@section('title', 'Book Room')
@section('content')

<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-purple-600">
            <h1 class="text-xl font-bold text-white">Complete Your Booking</h1>
        </div>

        <div class="p-6">
            <!-- Room Summary -->
            <div class="bg-gray-50 p-4 rounded-lg mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-3">Booking Summary</h2>
                <div class="flex items-start space-x-4">
                    @if($hostel->primaryImage)
                        <img src="{{ Storage::url($hostel->primaryImage->path) }}" 
                             alt="{{ $hostel->name }}"
                             class="w-24 h-24 object-cover rounded-lg">
                    @endif
                    <div>
                        <h3 class="font-semibold text-gray-900">{{ $hostel->name }}</h3>
                        <p class="text-sm text-gray-600">{{ $hostel->location }}</p>
                        <p class="text-sm text-gray-600 mt-2">
                            <i class="fas fa-door-open mr-1"></i>Room {{ $room->number }}
                        </p>
                        <p class="text-sm text-gray-600">
                            <i class="fas fa-users mr-1"></i>Capacity: {{ $room->capacity }} persons
                        </p>
                        <p class="text-sm text-gray-600">
                            <i class="fas fa-tag mr-1"></i>Monthly Rate: ₵{{ number_format($room->price_per_month, 2) }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Booking Form -->
            <form action="{{ route('bookings.store') }}" method="POST" id="bookingForm">
                @csrf
                <input type="hidden" name="room_id" value="{{ $room->id }}">
                <input type="hidden" name="hostel_id" value="{{ $hostel->id }}">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Check-in Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" 
                               name="check_in_date" 
                               id="check_in_date"
                               min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                               required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Check-out Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" 
                               name="check_out_date" 
                               id="check_out_date"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                               required>
                    </div>
                </div>

                <!-- Price Calculation -->
                <div id="priceSummary" class="bg-blue-50 p-4 rounded-lg mb-6 hidden">
                    <h3 class="font-semibold text-blue-800 mb-3">Payment Summary</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Duration:</span>
                            <span id="durationDisplay" class="font-medium">-</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Accommodation Total:</span>
                            <span id="accommodationTotal" class="font-medium">-</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Processing Fee (1.95%):</span>
                            <span id="processingFee" class="font-medium">-</span>
                        </div>
                        <div class="border-t border-blue-200 my-2"></div>
                        <div class="flex justify-between text-lg font-bold">
                            <span>Total to Pay:</span>
                            <span id="totalAmount" class="text-blue-600">-</span>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                       id="submitBtn"
                       disabled
                       class="w-full px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-lock mr-2"></i>
                    Proceed to Secure Payment
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
const checkIn = document.getElementById('check_in_date');
const checkOut = document.getElementById('check_out_date');
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
            // Calculate accommodation total
            const accommodationTotal = (monthlyRate / 30) * diffDays;
            
            // Calculate processing fee (1.95%)
            const processingFee = accommodationTotal * 0.0195;
            
            // Calculate total including fee
            const totalWithFee = accommodationTotal + processingFee;
            
            // Update display
            document.getElementById('durationDisplay').textContent = diffDays + ' nights';
            document.getElementById('accommodationTotal').textContent = '₵' + accommodationTotal.toFixed(2);
            document.getElementById('processingFee').textContent = '₵' + processingFee.toFixed(2);
            document.getElementById('totalAmount').textContent = '₵' + totalWithFee.toFixed(2);
            
            priceSummary.classList.remove('hidden');
            submitBtn.disabled = false;
        }
    }
}

// Update check-out min date when check-in changes
checkIn.addEventListener('change', function() {
    const checkInDate = new Date(this.value);
    const nextDay = new Date(checkInDate);
    nextDay.setDate(nextDay.getDate() + 1);
    
    const year = nextDay.getFullYear();
    const month = String(nextDay.getMonth() + 1).padStart(2, '0');
    const day = String(nextDay.getDate()).padStart(2, '0');
    
    checkOut.min = `${year}-${month}-${day}`;
    
    // Clear check-out if it's now invalid
    if (checkOut.value && new Date(checkOut.value) <= checkInDate) {
        checkOut.value = '';
    }
    
    calculateTotal();
});

checkOut.addEventListener('change', calculateTotal);
</script>
@endpush
@endsection