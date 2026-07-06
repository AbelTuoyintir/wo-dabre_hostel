@extends('layouts.home')

@section('title', 'Complete Your Booking - UCC Hostel Booking System')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 py-8">
    <!-- Back Button -->
    <div class="container mx-auto px-4 mb-6">
        <a href="{{ url()->previous() }}" class="inline-flex items-center text-gray-600 hover:text-blue-600 transition-colors duration-200 group">
            <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform duration-200"></i> 
            <span class="font-medium">Back to Hostels</span>
        </a>
    </div>

    <div class="container mx-auto px-4 max-w-4xl">
        <!-- Progress Steps -->
        <div class="flex justify-center items-center mb-8">
            <div class="flex items-center w-full max-w-md">
                <div class="flex-1 text-center">
                    <div class="w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center mx-auto shadow-lg shadow-blue-200">
                        <i class="fas fa-search text-sm"></i>
                    </div>
                    <p class="text-xs font-medium text-gray-600 mt-2">Select Room</p>
                </div>
                <div class="flex-1 h-1 bg-blue-600 relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-blue-400"></div>
                </div>
                <div class="flex-1 text-center">
                    <div class="w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center mx-auto shadow-lg shadow-blue-200 relative">
                        <span class="text-sm font-bold">2</span>
                        <div class="absolute -top-1 -right-1 w-4 h-4 bg-green-400 rounded-full animate-pulse"></div>
                    </div>
                    <p class="text-xs font-medium text-blue-600 mt-2">Complete Booking</p>
                </div>
                <div class="flex-1 h-1 bg-gray-300"></div>
                <div class="flex-1 text-center">
                    <div class="w-10 h-10 bg-gray-300 text-gray-500 rounded-full flex items-center justify-center mx-auto">
                        <i class="fas fa-check text-sm"></i>
                    </div>
                    <p class="text-xs font-medium text-gray-400 mt-2">Confirmation</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            <!-- Header -->
            <div class="relative px-8 py-6 bg-gradient-to-r from-blue-600 to-purple-600 overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-white opacity-5 rounded-full -mr-32 -mt-32"></div>
                <div class="absolute bottom-0 left-0 w-48 h-48 bg-white opacity-5 rounded-full -ml-24 -mb-24"></div>
                <div class="relative flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-white">Complete Your Booking</h1>
                        <p class="text-blue-100 text-sm mt-1">Fill in your details to secure your room</p>
                    </div>
                    <div class="bg-white/20 backdrop-blur-sm px-4 py-2 rounded-lg">
                        <span class="text-white text-sm font-medium">
                            <i class="fas fa-clock mr-2"></i>
                            Complete within 15 minutes
                        </span>
                    </div>
                </div>
            </div>

            <div class="p-8">
                @if(session('error'))
                    <div class="mb-6 rounded-xl border border-red-200 bg-gradient-to-r from-red-50 to-red-100 p-4 text-sm text-red-700 animate-shake">
                        <div class="flex items-start">
                            <i class="fas fa-circle-exclamation mt-0.5 mr-3 text-red-500"></i>
                            <div>
                                <p class="font-semibold">Booking Error</p>
                                <p class="text-red-600">{{ session('error') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-6 rounded-xl border border-red-200 bg-gradient-to-r from-red-50 to-red-100 p-4">
                        <div class="flex items-start">
                            <i class="fas fa-circle-exclamation mt-0.5 mr-3 text-red-500"></i>
                            <div>
                                <p class="font-semibold text-red-800">Please fix the following errors:</p>
                                <ul class="list-disc space-y-1 pl-5 mt-2 text-sm text-red-700">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Room Summary Card -->
                <div class="relative bg-gradient-to-r from-gray-50 to-blue-50 rounded-xl p-6 mb-8 border border-gray-200">
                    <div class="absolute top-3 right-3">
                        <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">
                            <i class="fas fa-check-circle mr-1"></i> Available
                        </span>
                    </div>
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-receipt text-blue-600 mr-2"></i>
                        Booking Summary
                    </h2>
                    <div class="flex flex-col md:flex-row items-start md:items-center space-y-4 md:space-y-0 md:space-x-6">
                        @if($hostel->primaryImage)
                            <img src="{{ image_url($hostel->primaryImage->image_path) }}"
                                 alt="{{ $hostel->name }}"
                                 class="w-28 h-28 object-cover rounded-xl shadow-md">
                        @else
                            <div class="w-28 h-28 bg-gradient-to-br from-blue-100 to-purple-100 rounded-xl flex items-center justify-center shadow-md">
                                <i class="fas fa-building text-blue-400 text-4xl"></i>
                            </div>
                        @endif
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-gray-900">{{ $hostel->name }}</h3>
                            <p class="text-sm text-gray-600 mb-2">
                                <i class="fas fa-map-marker-alt text-blue-500 mr-1"></i>
                                {{ $hostel->location }}
                            </p>
                            <div class="flex flex-wrap gap-3 text-sm">
                                <span class="bg-white px-3 py-1 rounded-full shadow-sm border border-gray-200">
                                    <i class="fas fa-door-open text-blue-500 mr-1"></i>
                                    Room {{ $room->number }}
                                </span>
                                <span class="bg-white px-3 py-1 rounded-full shadow-sm border border-gray-200">
                                    <i class="fas fa-users text-blue-500 mr-1"></i>
                                    Capacity: {{ $room->capacity }} persons
                                </span>
                                <span class="bg-white px-3 py-1 rounded-full shadow-sm border border-gray-200">
                                    <i class="fas fa-venus-mars text-blue-500 mr-1"></i>
                                    {{ ucfirst($room->gender) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 pt-4 border-t border-gray-200">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
                            <span class="text-gray-700 font-medium">Price per academic year</span>
                            <div class="text-right mt-2 sm:mt-0">
                                @if(!empty($room->room_cost) && $room->room_cost > 0)
                                    <span class="text-3xl font-bold text-blue-600">₵{{ number_format($room->room_cost, 2) }}</span>
                                    <span class="text-sm text-gray-500">/year</span>
                                @else
                                    <span class="text-gray-400">Price not set</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                @if(!Auth::check())
                <div class="bg-gradient-to-r from-yellow-50 to-orange-50 border border-yellow-200 rounded-xl p-6 mb-8">
                    <div class="flex items-start">
                        <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center flex-shrink-0 mr-4">
                            <i class="fas fa-user-plus text-yellow-600"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-yellow-800 text-lg">Create Account to Continue</h3>
                            <p class="text-sm text-yellow-700 mt-1">
                                An account will be created for you. Login credentials will be sent to your email after payment.
                            </p>
                            <div class="mt-3 flex flex-wrap gap-2">
                                <span class="inline-flex items-center text-xs text-yellow-700 bg-yellow-100 px-3 py-1 rounded-full">
                                    <i class="fas fa-check-circle mr-1 text-green-500"></i> Secure
                                </span>
                                <span class="inline-flex items-center text-xs text-yellow-700 bg-yellow-100 px-3 py-1 rounded-full">
                                    <i class="fas fa-envelope mr-1 text-blue-500"></i> Login details emailed
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <form action="{{ route('bookings.store') }}" method="POST" id="bookingForm" novalidate>
                    @csrf
                    <input type="hidden" name="room_id" value="{{ $room->id }}">
                    <input type="hidden" name="hostel_id" value="{{ $hostel->id }}">
                    <input type="hidden" name="room_cost" value="{{ $room->room_cost ?? 0 }}">

                    <!-- Personal Information -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-user text-blue-600 mr-2"></i>
                            Personal Information
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Full Name <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-user text-gray-400"></i>
                                    </div>
                                    <input type="text" name="name" value="{{ old('name') }}"
                                           class="w-full pl-10 pr-4 py-3 border rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition {{ $errors->has('name') ? 'border-red-400' : 'border-gray-300' }}"
                                           placeholder="Enter your full name"
                                           required>
                                </div>
                                @error('name')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Email Address <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-envelope text-gray-400"></i>
                                    </div>
                                    <input type="email" name="email" value="{{ old('email') }}"
                                           class="w-full pl-10 pr-4 py-3 border rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition {{ $errors->has('email') ? 'border-red-400' : 'border-gray-300' }}"
                                           placeholder="your@email.com"
                                           required>
                                </div>
                                @error('email')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Phone Number <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-phone text-gray-400"></i>
                                    </div>
                                    <input type="tel" name="phone" value="{{ old('phone') }}"
                                           class="w-full pl-10 pr-4 py-3 border rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition {{ $errors->has('phone') ? 'border-red-400' : 'border-gray-300' }}"
                                           placeholder="024XXXXXXX"
                                           required>
                                </div>
                                @error('phone')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Gender <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-venus-mars text-gray-400"></i>
                                    </div>
                                    <select name="gender" class="w-full pl-10 pr-4 py-3 border rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition appearance-none {{ $errors->has('gender') ? 'border-red-400' : 'border-gray-300' }}" required>
                                        <option value="">Select Gender</option>
                                        <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <i class="fas fa-chevron-down text-gray-400"></i>
                                    </div>
                                </div>
                                @error('gender')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Stay Dates -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-calendar-alt text-blue-600 mr-2"></i>
                            Stay Dates
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Check-in Date <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-calendar-check text-gray-400"></i>
                                    </div>
                                    <input type="date"
                                           name="check_in_date"
                                           id="check_in_date"
                                           value="{{ old('check_in_date') }}"
                                           min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                           class="w-full pl-10 pr-4 py-3 border rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition {{ $errors->has('check_in_date') ? 'border-red-400' : 'border-gray-300' }}"
                                           required>
                                </div>
                                @error('check_in_date')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Check-out Date <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-calendar-times text-gray-400"></i>
                                    </div>
                                    <input type="date"
                                           name="check_out_date"
                                           id="check_out_date"
                                           value="{{ old('check_out_date') }}"
                                           class="w-full pl-10 pr-4 py-3 border rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition {{ $errors->has('check_out_date') ? 'border-red-400' : 'border-gray-300' }}"
                                           required>
                                </div>
                                @error('check_out_date')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <p id="dateError" class="mt-2 hidden text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            <span id="dateErrorMessage"></span>
                        </p>
                    </div>

                    <!-- Price Summary -->
                    <div id="priceSummary" class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-6 mb-8 hidden border border-blue-200">
                        <h3 class="font-semibold text-blue-800 mb-4 flex items-center">
                            <i class="fas fa-calculator mr-2"></i>
                            Payment Summary
                        </h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center py-2 border-b border-blue-100">
                                <span class="text-gray-700">Duration:</span>
                                <span id="durationDisplay" class="font-medium text-gray-900">-</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-blue-100">
                                <span class="text-gray-700">Accommodation Cost:</span>
                                <span id="accommodationTotal" class="font-medium text-gray-900">-</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-blue-100">
                                <span class="text-gray-700">Student Fee (one-time):</span>
                                <span class="font-medium text-gray-900">₵150.00</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-blue-100">
                                <span class="text-gray-700">Subtotal:</span>
                                <span id="subtotal" class="font-medium text-gray-900">-</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-blue-100">
                                <span class="text-gray-700">Processing Fee (2%):</span>
                                <span id="processingFee" class="font-medium text-gray-900">-</span>
                            </div>
                            <div class="flex justify-between items-center pt-3">
                                <span class="text-lg font-bold text-gray-900">Total to Pay:</span>
                                <span id="totalAmount" class="text-2xl font-bold text-blue-600">-</span>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                            id="submitBtn"
                            disabled
                            class="relative w-full px-6 py-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-xl hover:shadow-lg hover:scale-[1.01] transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100 disabled:hover:shadow-none group overflow-hidden">
                        <span class="relative flex items-center justify-center">
                            <i class="fas fa-lock mr-3 group-hover:scale-110 transition-transform"></i>
                            Proceed to Secure Payment
                            <i class="fas fa-arrow-right ml-3 group-hover:translate-x-1 transition-transform"></i>
                        </span>
                        <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-10 transition-opacity"></div>
                    </button>

                    <div class="mt-6 flex flex-wrap items-center justify-center gap-4 text-xs text-gray-500">
                        <span class="flex items-center">
                            <i class="fas fa-shield-alt text-green-500 mr-1"></i>
                            Secure & Encrypted
                        </span>
                        <span class="hidden sm:inline text-gray-300">|</span>
                        <span class="flex items-center">
                            <i class="fas fa-credit-card text-blue-500 mr-1"></i>
                            Paystack Payment
                        </span>
                        <span class="hidden sm:inline text-gray-300">|</span>
                        <span class="flex items-center">
                            <i class="fas fa-headset text-purple-500 mr-1"></i>
                            24/7 Support
                        </span>
                    </div>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        10%, 30%, 50%, 70%, 90% { transform: translateX(-2px); }
        20%, 40%, 60%, 80% { transform: translateX(2px); }
    }
    .animate-shake {
        animation: shake 0.5s ease-in-out;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkIn = document.getElementById('check_in_date');
    const checkOut = document.getElementById('check_out_date');
    const priceSummary = document.getElementById('priceSummary');
    const submitBtn = document.getElementById('submitBtn');
    const dateError = document.getElementById('dateError');
    const dateErrorMessage = document.getElementById('dateErrorMessage');
    const STUDENT_FEE = 150;

    if (!checkIn || !checkOut || !priceSummary || !submitBtn || !dateError || !dateErrorMessage) {
        return;
    }

    const roomCost = {{ $room->room_cost ?? 0 }};

    function showDateError(message) {
        dateErrorMessage.textContent = message;
        dateError.classList.remove('hidden');
    }

    function clearDateError() {
        dateErrorMessage.textContent = '';
        dateError.classList.add('hidden');
    }

    function resetSummary() {
        priceSummary.classList.add('hidden');
        submitBtn.disabled = true;
    }

    function updateCheckOutMinDate() {
        if (!checkIn.value) {
            return;
        }

        const checkInDate = new Date(checkIn.value);
        if (Number.isNaN(checkInDate.getTime())) {
            showDateError('Please select a valid check-in date.');
            resetSummary();
            return;
        }

        const nextDay = new Date(checkInDate);
        nextDay.setDate(nextDay.getDate() + 1);

        const year = nextDay.getFullYear();
        const month = String(nextDay.getMonth() + 1).padStart(2, '0');
        const day = String(nextDay.getDate()).padStart(2, '0');
        checkOut.min = `${year}-${month}-${day}`;
    }

    function calculateTotal() {
        if (!checkIn.value || !checkOut.value) {
            resetSummary();
            return;
        }

        const start = new Date(checkIn.value);
        const end = new Date(checkOut.value);

        if (Number.isNaN(start.getTime()) || Number.isNaN(end.getTime())) {
            showDateError('Please select valid check-in and check-out dates.');
            resetSummary();
            return;
        }

        const diffDays = Math.ceil((end - start) / (1000 * 60 * 60 * 24));
        if (diffDays <= 0) {
            showDateError('Check-out date must be at least one day after check-in date.');
            resetSummary();
            return;
        }

        clearDateError();

        // Calculate costs
        const accommodationTotal = roomCost;
        const subtotal = accommodationTotal + STUDENT_FEE;
        const processingFee = parseFloat((subtotal * 0.02).toFixed(2));
        const totalWithFee = parseFloat((subtotal + processingFee).toFixed(2));

        // Update UI
        const durationDisplay = document.getElementById('durationDisplay');
        const accommodationTotalEl = document.getElementById('accommodationTotal');
        const subtotalEl = document.getElementById('subtotal');
        const processingFeeEl = document.getElementById('processingFee');
        const totalAmountEl = document.getElementById('totalAmount');

        if (durationDisplay) durationDisplay.textContent = diffDays + ' nights';
        if (accommodationTotalEl) accommodationTotalEl.textContent = '₵' + accommodationTotal.toFixed(2);
        if (subtotalEl) subtotalEl.textContent = '₵' + subtotal.toFixed(2);
        if (processingFeeEl) processingFeeEl.textContent = '₵' + processingFee.toFixed(2);
        if (totalAmountEl) totalAmountEl.textContent = '₵' + totalWithFee.toFixed(2);

        priceSummary.classList.remove('hidden');
        submitBtn.disabled = false;
    }

    // Event Listeners
    checkIn.addEventListener('change', function() {
        updateCheckOutMinDate();

        if (checkOut.value && new Date(checkOut.value) <= new Date(checkIn.value)) {
            checkOut.value = '';
            showDateError('Check-out date must be after check-in date.');
            resetSummary();
            return;
        }

        calculateTotal();
    });

    checkOut.addEventListener('change', calculateTotal);

    // Initial setup
    updateCheckOutMinDate();
    
    // Auto-calculate if both dates are already set
    if (checkIn.value && checkOut.value) {
        calculateTotal();
    }
});
</script>
@endsection