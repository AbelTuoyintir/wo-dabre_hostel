@extends('layouts.student')

@section('title', 'Book Room')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-purple-600">
            <h1 class="text-xl font-bold text-white">Complete Your Booking</h1>
        </div>
@
        <div class="p-6">

            <!-- Booking Summary -->
            <div class="bg-gray-50 p-4 rounded-lg mb-6">
                <h2 class="text-lg font-semibold mb-3">Booking Summary</h2>

                <div class="flex items-start space-x-4">
                    <img src="{{ Storage::url($hostel->primaryImage->path ?? '') }}"
                         class="w-24 h-24 object-cover rounded-lg"
                         alt="{{ $hostel->name }}">

                    <div>
                        <h3 class="font-semibold">{{ $hostel->name }}</h3>
                        <p class="text-sm text-gray-600">{{ $hostel->location }}</p>
                        <p class="text-sm text-gray-600">Room {{ $room->number }}</p>
                        <p class="text-sm text-gray-600">Capacity: {{ $room->capacity }}</p>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t">
                    <span class="text-gray-700">Room Cost (Academic Year)</span>
                    <div class="text-2xl font-bold text-blue-600">
                        ₵{{ number_format($room->room_cost, 2) }}
                    </div>
                </div>
            </div>

            <!-- Booking Form -->
            <form method="POST"
                  action="{{ Auth::check() ? route('bookings.store.student') : route('bookings.store') }}"
                  id="bookingForm">

                @csrf

                <input type="hidden" name="room_id" value="{{ $room->id }}">
                <input type="hidden" name="hostel_id" value="{{ $hostel->id }}">
                <input type="hidden" name="room_cost" id="roomCost">

                <!-- Dates -->
                <div class="grid grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="text-sm font-medium">Check-in Date</label>
                        <input type="date" id="check_in_date" name="check_in_date"
                               class="w-full border rounded-lg px-4 py-2" required>
                    </div>

                    <div>
                        <label class="text-sm font-medium">Check-out Date</label>
                        <input type="date" id="check_out_date" name="check_out_date"
                               class="w-full border rounded-lg px-4 py-2" required>
                    </div>
                </div>

                <!-- Price Summary -->
                <div id="priceSummary" class="bg-blue-50 p-4 rounded-lg mb-6 hidden">
                    <h3 class="font-semibold mb-3">Payment Summary</h3>

                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span>Duration</span>
                            <span id="durationDisplay">-</span>
                        </div>

                        <div class="flex justify-between">
                            <span>Room Cost</span>
                            <span id="roomCostDisplay">-</span>
                        </div>

                        <div class="flex justify-between">
                            <span>Agent Fee</span>
                            <span id="agentFee">₵150.00</span>
                        </div>

                        <div class="flex justify-between">
                            <span>System Charge</span>
                            <span id="systemCharge">₵20.00</span>
                        </div>

                        <div class="flex justify-between">
                            <span>Paystack Fee (1.95%)</span>
                            <span id="paystackFee">-</span>
                        </div>

                        <div class="border-t my-2"></div>

                        <div class="flex justify-between font-bold text-lg">
                            <span>Total</span>
                            <span id="totalAmount" class="text-blue-600">-</span>
                        </div>
                    </div>
                </div>

                <button id="submitBtn" disabled
                        class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold disabled:opacity-50">
                    Proceed to Payment
                </button>
            </form>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const checkIn  = document.getElementById('check_in_date');
const checkOut = document.getElementById('check_out_date');
const roomId   = {{ $room->id }};
const yearlyRate = {{ $room->room_cost }};
const submitBtn = document.getElementById('submitBtn');
const priceSummary = document.getElementById('priceSummary');

checkIn.addEventListener('change', calculateTotal);
checkOut.addEventListener('change', calculateTotal);

async function calculateTotal() {
    if (!checkIn.value || !checkOut.value) return;

    const response = await fetch('{{ route("bookings.calculate") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            check_in_date: checkIn.value,
            check_out_date: checkOut.value,
            room_id: roomId,
            room_cost: yearlyRate
        })
    });

    const data = await response.json();

    if (!data.success) return;

    document.getElementById('durationDisplay').textContent =
        data.nights + ' nights';

    document.getElementById('roomCostDisplay').textContent =
        '₵' + data.room_cost.toFixed(2);

    document.getElementById('paystackFee').textContent =
        '₵' + data.paystack_fee.toFixed(2);

    document.getElementById('totalAmount').textContent =
        '₵' + data.total.toFixed(2);

    // SEND ONLY ROOM COST TO BACKEND
    document.getElementById('roomCost').value = data.room_cost;

    priceSummary.classList.remove('hidden');
    submitBtn.disabled = false;
}
</script>
@endpush