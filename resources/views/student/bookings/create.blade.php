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
                            <i class="fas fa-venus-mars mr-1"></i>Gender: {{ ucfirst($room->gender) }}
                        </p>
                        <div class="mt-2">
                            @if(!empty($room->room_cost) && $room->room_cost > 0)
                                <p class="text-sm font-semibold text-blue-600">
                                    <i class="fas fa-tag mr-1"></i>Academic Year Rate: ₵{{ number_format($room->room_cost, 2) }}
                                </p>
                                <p class="text-xs text-gray-500 mt-1">Full academic year (August - May)</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Academic Year Selection (Single Option) -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Academic Year Booking</h3>
                
                <div class="border-2 border-blue-500 bg-blue-50 rounded-lg p-6">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start space-x-4">
                            <div class="w-6 h-6 bg-blue-600 rounded-full flex items-center justify-center mt-1">
                                <i class="fas fa-check text-white text-xs"></i>
                            </div>
                            <div>
                                <h4 class="text-xl font-bold text-gray-900">Full Academic Year</h4>
                                <p class="text-gray-600 mt-1">August 2026 - May 2027</p>
                                <div class="mt-3">
                                    <p class="text-sm text-gray-700">Includes both First and Second Semesters:</p>
                                    <ul class="mt-2 space-y-1 text-sm text-gray-600">
                                        <li><i class="fas fa-check-circle text-green-500 mr-2"></i>First Semester: August - December 2026</li>
                                        <li><i class="fas fa-check-circle text-green-500 mr-2"></i>Second Semester: January - May 2027</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-500">Total Room Cost</p>
                            <p class="text-3xl font-bold text-blue-600">₵{{ number_format($room->room_cost ?? 0, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Information Section -->
            @auth
                <!-- Logged-in User Information (Read-only) -->
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                    <h3 class="font-semibold text-green-800 mb-3 flex items-center">
                        <i class="fas fa-user-check mr-2"></i>
                        Your Information
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                            <div class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700">
                                {{ Auth::user()->name }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <div class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700">
                                {{ Auth::user()->email }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                            <div class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700">
                                {{ Auth::user()->phone ?? 'Not provided' }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                            <div class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700">
                                @if(Auth::user()->gender)
                                    {{ ucfirst(Auth::user()->gender) }}
                                @else
                                    <span class="text-yellow-600">Please update your profile</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    @if(!Auth::user()->gender)
                        <div class="mt-3 p-2 bg-yellow-100 rounded-lg">
                            <p class="text-sm text-yellow-700">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                Please <a href="{{ route('student.profile') }}" class="underline font-medium">update your profile</a> with your gender before proceeding.
                            </p>
                        </div>
                    @endif
                </div>
            @else
                <!-- Guest User Registration Form -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                    <h3 class="font-semibold text-yellow-800 mb-3">Create Account to Continue</h3>
                    <p class="text-sm text-yellow-700 mb-3">
                        <i class="fas fa-info-circle mr-1"></i>
                        An account will be created for you. Login credentials will be sent to your email after payment.
                    </p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                            <input type="text" name="name" form="bookingForm" value="{{ old('name') }}" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                   required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                            <input type="email" name="email" form="bookingForm" value="{{ old('email') }}" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                   required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number *</label>
                            <input type="tel" name="phone" form="bookingForm" value="{{ old('phone') }}" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                   placeholder="e.g., 024XXXXXXX"
                                   required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Gender *</label>
                            <select name="gender" form="bookingForm" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                                <option value="">Select Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>
                    </div>
                </div>
            @endauth

            <!-- Price Calculation -->
            <div id="priceSummary" class="bg-blue-50 p-4 rounded-lg mb-6">
                <h3 class="font-semibold text-blue-800 mb-3">Payment Summary</h3>
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Booking Period:</span>
                        <span class="font-medium">Full Academic Year (2 Semesters)</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Room Cost:</span>
                        <span class="font-medium">₵{{ number_format($room->room_cost ?? 0, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Student Fee (one-time):</span>
                        <span class="font-medium">₵150.00</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Subtotal:</span>
                        <span class="font-medium" id="subtotalDisplay">₵{{ number_format(($room->room_cost ?? 0) + 150, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Paystack Fee (2%):</span>
                        <span class="font-medium" id="processingFee">₵{{ number_format((($room->room_cost ?? 0) + 150) * 0.02, 2) }}</span>
                    </div>
                    <div class="border-t border-blue-200 my-2"></div>
                    <div class="flex justify-between text-lg font-bold">
                        <span>Total to Pay:</span>
                        <span class="text-blue-600" id="totalAmount">
                            ₵{{ number_format((($room->room_cost ?? 0) + 150) * 1.02, 2) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Booking Form -->
            <form action="{{ route('bookings.store') }}" method="POST" id="bookingForm">
                @csrf
                <input type="hidden" name="room_id" value="{{ $room->id }}">
                <input type="hidden" name="hostel_id" value="{{ $hostel->id }}">
                <input type="hidden" name="booking_period" value="full">
                <input type="hidden" name="check_in" value="2026-08-15">
                <input type="hidden" name="check_out" value="2027-05-20">
                <input type="hidden" name="room_cost" value="{{ $room->room_cost ?? 0 }}">
                <input type="hidden" name="num_semesters" value="2">
                
                <!-- Add hidden fields with user data for logged-in users -->
                @auth
                    <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                    <input type="hidden" name="user_name" value="{{ Auth::user()->name }}">
                    <input type="hidden" name="user_email" value="{{ Auth::user()->email }}">
                    <input type="hidden" name="user_phone" value="{{ Auth::user()->phone }}">
                    <input type="hidden" name="user_gender" value="{{ Auth::user()->gender }}">
                @endauth
            </form>

            <!-- Submit Button - Disabled if user gender is missing -->
            <button type="submit" 
                   id="submitBtn"
                   form="bookingForm"
                   @auth
                       @if(!Auth::user()->gender)
                           disabled
                           title="Please update your profile with your gender before booking"
                       @endif
                   @endauth
                   class="w-full px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                <i class="fas fa-lock mr-2"></i>
                Proceed to Secure Payment
            </button>

            @auth
                @if(!Auth::user()->gender)
                    <p class="text-center text-sm text-red-500 mt-2">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        Please <a href="{{ route('student.profile') }}" class="underline font-medium">update your profile</a> with your gender to continue.
                    </p>
                @endif
            @endauth

            <p class="text-center text-xs text-gray-500 mt-4">
                <i class="fas fa-shield-alt mr-1"></i>
                Payments are processed securely by Paystack. Your payment information is encrypted00000
            </p>
        </div>
    </div>
</div>
@endsection