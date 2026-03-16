<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Book Room - UCC Hostel Booking</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }
        .hostel-card:hover {
            transform: translateY(-5px);
            transition: transform 0.3s ease;
        }
        .loader {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-blue-900 text-white shadow-lg">
        <div class="container mx-auto px-4 py-6">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="flex items-center space-x-3 mb-4 md:mb-0">
                    <div class="bg-white text-blue-900 p-2 rounded-lg">
                        <i class="fas fa-university text-2xl"></i>
                    </div>
                    <a href="{{ url('/') }}" class="hover:opacity-90">
                        <h1 class="text-2xl font-bold">UCC Hostel Booking</h1>
                        <p class="text-blue-200">University of Cape Coast Student Accommodation</p>
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('login') }}"
                       class="bg-white text-blue-900 px-4 py-2 rounded-lg font-medium hover:bg-blue-100 transition">
                        <i class="fas fa-sign-in-alt mr-2"></i> Student Login
                    </a>
                    <a href="{{ route('register') }}"
                       class="hidden md:flex items-center space-x-2 bg-blue-800 text-white px-4 py-2 rounded-lg hover:bg-blue-900 transition">
                        <i class="fas fa-user-graduate"></i>
                        <span>Create Account</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Back Button -->
    <div class="container mx-auto px-4 py-4">
        <a href="{{ url()->previous() }}" class="inline-flex items-center text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-left mr-2"></i> Back to Hostels
        </a>
    </div>

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
                        @else
                            <div class="w-24 h-24 bg-gray-200 rounded-lg flex items-center justify-center">
                                <i class="fas fa-building text-gray-400 text-3xl"></i>
                            </div>
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
                        </div>
                    </div>

                    <!-- Price Display - FIXED: Using room_cost -->
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-700 font-medium">Price (per academic year)</span>
                            <div class="text-right">
                                @if(!empty($room->room_cost) && $room->room_cost > 0)
                                    <span class="text-2xl font-bold text-blue-600">₵{{ number_format($room->room_cost, 2) }}</span>
                                    <span class="text-sm text-gray-500">/year</span>
                                @else
                                    <span class="text-gray-400">Price not set</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Guest Registration Form - NOW FIXED -->
            @if(!Auth::check())
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                <h3 class="font-semibold text-yellow-800 mb-3">Create Account to Continue</h3>
                <p class="text-sm text-yellow-700 mb-3">
                    <i class="fas fa-info-circle mr-1"></i>
                    An account will be created for you. Login credentials will be sent to your email after payment.
                </p>
                
                <!-- FIXED: Form now contains ALL inputs -->
                <form action="{{ route('bookings.store') }}" method="POST" id="bookingForm">
                    @csrf
                    <input type="hidden" name="room_id" value="{{ $room->id }}">
                    <input type="hidden" name="hostel_id" value="{{ $hostel->id }}">
                    <input type="hidden" name="room_cost" value="{{ $room->room_cost ?? 0 }}">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                            <input type="text" name="name" value="{{ old('name') }}" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                            <input type="email" name="email" value="{{ old('email') }}" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number *</label>
                            <input type="tel" name="phone" value="{{ old('phone') }}" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                placeholder="e.g., 024XXXXXXX"
                                required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Gender *</label>
                            <select name="gender" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                                <option value="">Select Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>
                    </div>

                    <!-- Date Selection -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6 mb-6">
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
                                <span class="text-gray-600">Accommodation Cost:</span>
                                <span id="accommodationTotal" class="font-medium">-</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Student Fee (one-time):</span>
                                <span class="font-medium">₵150.00</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Subtotal:</span>
                                <span id="subtotal" class="font-medium">-</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Processing Fee (2%):</span>
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

                <p class="text-center text-xs text-gray-500 mt-4">
                    <i class="fas fa-shield-alt mr-1"></i>
                    Payments are processed securely by Paystack. Your payment information is encrypted.
                </p>
            </div>
            @endif
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white mt-16">
        <div class="container mx-auto px-4 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="bg-white text-blue-900 p-2 rounded-lg">
                            <i class="fas fa-university text-xl"></i>
                        </div>
                        <h3 class="text-xl font-bold">UCC Hostel Booking</h3>
                    </div>
                    <p class="text-gray-400">The official hostel booking platform for University of Cape Coast students.</p>
                </div>
                <div>
                    <h4 class="text-lg font-bold mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white transition">About Us</a></li>
                        <li><a href="#" class="hover:text-white transition">Contact</a></li>
                        <li><a href="#" class="hover:text-white transition">FAQs</a></li>
                        <li><a href="#" class="hover:text-white transition">Privacy Policy</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-bold mb-4">Locations</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li>Amamoma</li>
                        <li>Kwaprow</li>
                        <li>Ayensu</li>
                        <li>Schoolbus Road</li>
                        <li>Oldsite</li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-bold mb-4">Contact Info</h4>
                    <ul class="space-y-3 text-gray-400">
                        <li class="flex items-center">
                            <i class="fas fa-map-marker-alt mr-3 text-blue-400"></i>
                            <span>University of Cape Coast, Ghana</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-phone mr-3 text-blue-400"></i>
                            <span>+233 24 123 4567</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-envelope mr-3 text-blue-400"></i>
                            <span>hostelbooking@ucc.edu.gh</span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} University of Cape Coast Hostel Booking System. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded - initializing booking form');
        
        const checkIn = document.getElementById('check_in_date');
        const checkOut = document.getElementById('check_out_date');
        const priceSummary = document.getElementById('priceSummary');
        const submitBtn = document.getElementById('submitBtn');
        const STUDENT_FEE = 150;
        
        // Get room cost from data attribute or hidden field
        const roomCost = {{ $room->room_cost ?? 0 }};
        
        console.log('Room cost:', roomCost);

        function calculateTotal() {
            if (checkIn.value && checkOut.value) {
                const start = new Date(checkIn.value);
                const end = new Date(checkOut.value);
                const diffTime = Math.abs(end - start);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                
                console.log('Days:', diffDays);
                
                if (diffDays > 0) {
                    // Calculate pro-rated room cost (yearly rate / 365 * days)
                    const accommodationTotal = roomCost ;
                    const subtotal = accommodationTotal + STUDENT_FEE;
                    const processingFee = subtotal * 0.02;
                    const totalWithFee = subtotal + processingFee;
                    
                    console.log('Accommodation:', accommodationTotal);
                    
                    // Update display
                    document.getElementById('durationDisplay').textContent = diffDays + ' nights';
                    document.getElementById('accommodationTotal').textContent = '₵' + accommodationTotal.toFixed(2);
                    document.getElementById('subtotal').textContent = '₵' + subtotal.toFixed(2);
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
                priceSummary.classList.add('hidden');
                submitBtn.disabled = true;
            }
            
            calculateTotal();
        });

        checkOut.addEventListener('change', calculateTotal);
        
        console.log('Booking form initialized');
    });
    </script>
</body>
</html>