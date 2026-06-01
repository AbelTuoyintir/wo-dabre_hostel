@extends('layouts.student')

@section('title', 'Book Room')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-purple-600">
            <h1 class="text-xl font-bold text-white">Complete Your Booking</h1>
        </div>

        <div class="p-6">
            @if(session('error'))
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                    <i class="fas fa-circle-exclamation mr-1"></i>{{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-4">
                    <p class="mb-2 text-sm font-semibold text-red-800">Please fix the following errors:</p>
                    <ul class="list-disc space-y-1 pl-5 text-sm text-red-700">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-gray-50 p-4 rounded-lg mb-6">
                <h2 class="text-lg font-semibold mb-3">Booking Summary</h2>

                <div class="flex items-start space-x-4">
                    @if($hostel->primaryImage)
                        <img src="{{ Storage::url($hostel->primaryImage->image_path) }}"
                             class="w-24 h-24 object-cover rounded-lg"
                             alt="{{ $hostel->name }}">
                    @else
                        <div class="w-24 h-24 bg-gray-200 rounded-lg flex items-center justify-center">
                            <i class="fas fa-bed text-gray-400 text-2xl"></i>
                        </div>
                    @endif

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
                        GHS {{ number_format($room->room_cost, 2) }}
                    </div>
                </div>
            </div>

            <form method="POST"
                  action="{{ Auth::check() ? route('bookings.store.student') : route('bookings.store') }}"
                  id="bookingForm"
                  novalidate>

                @csrf
                <input type="hidden" name="room_id" value="{{ $room->id }}">
                <input type="hidden" name="hostel_id" value="{{ $hostel->id }}">
                <input type="hidden" name="room_cost" id="roomCost" value="{{ old('room_cost', $room->room_cost) }}">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="text-sm font-medium">Check-in Date <span class="text-red-500">*</span></label>
                        <input type="date"
                               id="check_in_date"
                               name="check_in_date"
                               value="{{ old('check_in_date') }}"
                               min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                               class="w-full border rounded-lg px-4 py-2 {{ $errors->has('check_in_date') ? 'border-red-400' : 'border-gray-300' }}"
                               required>
                        @error('check_in_date')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="text-sm font-medium">Check-out Date <span class="text-red-500">*</span></label>
                        <input type="date"
                               id="check_out_date"
                               name="check_out_date"
                               value="{{ old('check_out_date') }}"
                               class="w-full border rounded-lg px-4 py-2 {{ $errors->has('check_out_date') ? 'border-red-400' : 'border-gray-300' }}"
                               required>
                        @error('check_out_date')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <p id="dateError" class="mb-4 hidden text-sm text-red-600"></p>

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

                        <div class="border-t my-2"></div>

                        <div class="flex justify-between font-bold text-lg">
                            <span>Total</span>
                            <span id="totalAmount" class="text-blue-600">-</span>
                        </div>
                    </div>
                </div>

                <button id="submitBtn"
                        disabled
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
const checkIn = document.getElementById('check_in_date');
const checkOut = document.getElementById('check_out_date');
const roomCostInput = document.getElementById('roomCost');
const submitBtn = document.getElementById('submitBtn');
const priceSummary = document.getElementById('priceSummary');
const dateError = document.getElementById('dateError');
const roomId = {{ $room->id }};
const yearlyRate = {{ $room->room_cost }};

function showDateError(message) {
    if (!dateError) return;
    dateError.textContent = message;
    dateError.classList.remove('hidden');
}

function clearDateError() {
    if (!dateError) return;
    dateError.textContent = '';
    dateError.classList.add('hidden');
}

function resetSummary() {
    if (!priceSummary || !submitBtn) return;
    priceSummary.classList.add('hidden');
    submitBtn.disabled = true;
}

function setCheckoutMin() {
    if (!checkIn || !checkIn.value || !checkOut) return;

    const checkInDate = new Date(checkIn.value);
    if (Number.isNaN(checkInDate.getTime())) {
        return;
    }

    const nextDay = new Date(checkInDate);
    nextDay.setDate(nextDay.getDate() + 1);
    const y = nextDay.getFullYear();
    const m = String(nextDay.getMonth() + 1).padStart(2, '0');
    const d = String(nextDay.getDate()).padStart(2, '0');
    checkOut.min = `${y}-${m}-${d}`;
}

async function calculateTotal() {
    if (!checkIn || !checkOut || !priceSummary || !submitBtn) return;

    if (!checkIn.value || !checkOut.value) {
        resetSummary();
        return;
    }

    const start = new Date(checkIn.value);
    const end = new Date(checkOut.value);

    if (Number.isNaN(start.getTime()) || Number.isNaN(end.getTime())) {
        showDateError('Please select valid dates.');
        resetSummary();
        return;
    }

    if (end <= start) {
        showDateError('Check-out date must be after check-in date.');
        resetSummary();
        return;
    }

    clearDateError();

    try {
        const response = await fetch('{{ route("bookings.calculate") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                check_in_date: checkIn.value,
                check_out_date: checkOut.value,
                room_id: roomId,
                room_cost: yearlyRate
            })
        });

        const data = await response.json();

        if (!response.ok || !data.success) {
            const fallback = 'Could not calculate booking total. Please review your dates.';
            const validationMessage = data?.message || fallback;
            showDateError(validationMessage);
            resetSummary();
            return;
        }

        document.getElementById('durationDisplay').textContent = data.nights + ' nights';
        document.getElementById('roomCostDisplay').textContent = 'GHS ' + Number(data.room_cost || 0).toFixed(2);
        document.getElementById('totalAmount').textContent = 'GHS ' + Number(data.total || 0).toFixed(2);

        if (roomCostInput) {
            roomCostInput.value = Number(data.room_cost || yearlyRate).toFixed(2);
        }

        priceSummary.classList.remove('hidden');
        submitBtn.disabled = false;
    } catch (error) {
        showDateError('Network error while calculating amount. Please try again.');
        resetSummary();
    }
}

if (checkIn && checkOut) {
    checkIn.addEventListener('change', function() {
        setCheckoutMin();

        if (checkOut.value && new Date(checkOut.value) <= new Date(checkIn.value)) {
            checkOut.value = '';
            showDateError('Check-out date must be after check-in date.');
            resetSummary();
            return;
        }

        calculateTotal();
    });

    checkOut.addEventListener('change', calculateTotal);

    setCheckoutMin();
    calculateTotal();
}
</script>
@endpush
