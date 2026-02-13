<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UCC Hostel Booking System</title>
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
        .location-btn.active {
            background-color: #1d4ed8;
            color: white;
        }
        .hostel-card:hover {
            transform: translateY(-5px);
            transition: transform 0.3s ease;
        }
        #bookingModal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 1000;
        }
        .modal-content {
            animation: slideIn 0.3s ease-out;
        }
        @keyframes slideIn {
            from { transform: translateY(-50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
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
                    <div>
                        <h1 class="text-2xl font-bold">UCC Hostel Booking</h1>
                        <p class="text-blue-200">University of Cape Coast Student Accommodation</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">

                    <!-- Register -->
                    <a href="{{ url('/register') }}"
    class="hidden md:flex items-center space-x-2 bg-blue-800 text-white px-4 py-2 rounded-lg hover:bg-blue-900 transition">
    <i class="fas fa-user-graduate"></i>
    <span>Create Account</span>
</a>
<a href="{{ url('/login') }}"
    class="bg-white text-blue-900 px-4 py-2 rounded-lg font-medium hover:bg-blue-100 transition">
    <i class="fas fa-sign-in-alt mr-2"></i>
    Student Login
</a>

                </div>

            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <!-- Search and Filter Section -->
        <section class="mb-12">
            <h2 class="text-3xl font-bold text-gray-800 mb-2">Find Your Perfect Hostel</h2>
            <p class="text-gray-600 mb-6">Browse and book hostels across all UCC campuses. No more roaming around searching for accommodation.</p>

            <!-- Search Bar -->
            <div class="bg-white p-6 rounded-xl shadow-md mb-8">
                <div class="flex flex-col md:flex-row md:items-center md:space-x-4">
                    <div class="flex-1 mb-4 md:mb-0">
                        <div class="relative">
                            <i class="fas fa-search absolute left-4 top-3.5 text-gray-400"></i>
                            <input type="text" id="searchInput" placeholder="Search hostels by name or amenities..." class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                        </div>
                    </div>
                    <div class="flex space-x-4">
                        <select id="priceFilter" class="px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                            <option value="">Filter by Price</option>
                            <option value="0-500">Under ₵500</option>
                            <option value="500-1000">₵500 - ₵1000</option>
                            <option value="1000-1500">₵1000 - ₵1500</option>
                            <option value="1500+">Above ₵1500</option>
                        </select>
                        <button id="applyFilters" class="bg-blue-700 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-800 transition">
                            Search
                        </button>
                    </div>
                </div>

                <!-- Location Filter -->
                <div class="mt-6">
                    <h3 class="font-medium text-gray-700 mb-3">Filter by Location:</h3>
                    <div class="flex flex-wrap gap-3">
                        <button class="location-btn active px-4 py-2 bg-blue-100 text-blue-800 rounded-lg hover:bg-blue-200 transition" data-location="all">
                            All Locations
                        </button>
                        <button class="location-btn px-4 py-2 bg-gray-100 text-gray-800 rounded-lg hover:bg-gray-200 transition" data-location="amamoma">
                            <i class="fas fa-map-marker-alt mr-2"></i>Amamoma
                        </button>
                        <button class="location-btn px-4 py-2 bg-gray-100 text-gray-800 rounded-lg hover:bg-gray-200 transition" data-location="kwaprow">
                            <i class="fas fa-map-marker-alt mr-2"></i>Kwaprow
                        </button>
                        <button class="location-btn px-4 py-2 bg-gray-100 text-gray-800 rounded-lg hover:bg-gray-200 transition" data-location="ayensu">
                            <i class="fas fa-map-marker-alt mr-2"></i>Ayensu
                        </button>
                        <button class="location-btn px-4 py-2 bg-gray-100 text-gray-800 rounded-lg hover:bg-gray-200 transition" data-location="schoolbus">
                            <i class="fas fa-map-marker-alt mr-2"></i>Schoolbus Road
                        </button>
                        <button class="location-btn px-4 py-2 bg-gray-100 text-gray-800 rounded-lg hover:bg-gray-200 transition" data-location="oldsite">
                            <i class="fas fa-map-marker-alt mr-2"></i>Oldsite
                        </button>
                    </div>
                </div>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <div class="flex items-center">
                        <div class="bg-blue-100 p-3 rounded-lg mr-4">
                            <i class="fas fa-building text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">Total Hostels</p>
                            <p class="text-2xl font-bold text-gray-800">24</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <div class="flex items-center">
                        <div class="bg-green-100 p-3 rounded-lg mr-4">
                            <i class="fas fa-bed text-green-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">Available Rooms</p>
                            <p class="text-2xl font-bold text-gray-800">142</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <div class="flex items-center">
                        <div class="bg-purple-100 p-3 rounded-lg mr-4">
                            <i class="fas fa-map-marked-alt text-purple-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">Locations</p>
                            <p class="text-2xl font-bold text-gray-800">5</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <div class="flex items-center">
                        <div class="bg-yellow-100 p-3 rounded-lg mr-4">
                            <i class="fas fa-users text-yellow-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">Students Booked</p>
                            <p class="text-2xl font-bold text-gray-800">1,850+</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Hostels Section -->
        <section>
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Available Hostels</h2>

            <div id="hostelsContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Hostel cards will be dynamically populated -->
            </div>
        </section>
    </main>

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
                <p>&copy; 2023 University of Cape Coast Hostel Booking System. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Booking Modal -->
    <div id="bookingModal" class="modal">
        <div class="modal-content container mx-auto px-4 py-16">
            <div class="bg-white rounded-xl shadow-2xl max-w-4xl mx-auto overflow-hidden">
                <div class="p-8">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-2xl font-bold text-gray-800">Book Hostel</h3>
                        <button id="closeModal" class="text-gray-500 hover:text-gray-700 text-2xl">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <h4 id="modalHostelName" class="text-xl font-semibold text-gray-800 mb-2">Atlantic View Hostel</h4>
                            <p id="modalHostelLocation" class="text-gray-600 mb-4"><i class="fas fa-map-marker-alt text-blue-500 mr-2"></i>Amamoma</p>
                            <div id="modalHostelImages" class="mb-6">
                                <img id="modalMainImage" src="https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80" alt="Hostel" class="w-full h-48 object-cover rounded-lg mb-3">
                                <div class="flex space-x-2">
                                    <img src="https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=300&q=80" alt="Hostel" class="w-24 h-20 object-cover rounded cursor-pointer border-2 border-blue-500">
                                    <img src="https://images.unsplash.com/photo-1555854877-bab0e564b8d5?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=300&q=80" alt="Room" class="w-24 h-20 object-cover rounded cursor-pointer">
                                    <img src="https://images.unsplash.com/photo-1586023492125-27b2c045efd7?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=300&q=80" alt="Facility" class="w-24 h-20 object-cover rounded cursor-pointer">
                                </div>
                            </div>
                            <div class="mb-6">
                                <h5 class="font-semibold text-gray-700 mb-2">Amenities</h5>
                                <div id="modalAmenities" class="flex flex-wrap gap-2">
                                    <!-- Amenities will be added here -->
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="bg-gray-50 p-6 rounded-lg mb-6">
                                <div class="flex justify-between items-center mb-4">
                                    <p class="text-gray-700">Price per semester</p>
                                    <p id="modalPrice" class="text-2xl font-bold text-blue-700">₵1,200</p>
                                </div>
                                <div class="flex justify-between items-center mb-4">
                                    <p class="text-gray-700">Available Rooms</p>
                                    <p id="modalAvailable" class="text-lg font-semibold text-green-600">8 Rooms</p>
                                </div>
                                <div class="flex justify-between items-center">
                                    <p class="text-gray-700">Room Type</p>
                                    <p id="modalRoomType" class="font-medium">Single Room</p>
                                </div>
                            </div>

                            <div class="mb-6">
                                <h5 class="font-semibold text-gray-700 mb-3">Booking Information</h5>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-gray-600 mb-1">Full Name</label>
                                        <input type="text" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none" placeholder="Enter your full name">
                                    </div>
                                    <div>
                                        <label class="block text-gray-600 mb-1">Student ID</label>
                                        <input type="text" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none" placeholder="Enter your student ID">
                                    </div>
                                    <div>
                                        <label class="block text-gray-600 mb-1">Phone Number</label>
                                        <input type="tel" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none" placeholder="Enter your phone number">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-6">
                                <h5 class="font-semibold text-gray-700 mb-3">Payment Method</h5>
                                <div class="grid grid-cols-2 gap-3 mb-4">
                                    <div class="border-2 border-blue-500 rounded-lg p-4 text-center cursor-pointer">
                                        <i class="fas fa-credit-card text-blue-500 text-xl mb-2"></i>
                                        <p class="font-medium">Card Payment</p>
                                    </div>
                                    <div class="border border-gray-300 rounded-lg p-4 text-center cursor-pointer hover:border-gray-400">
                                        <i class="fas fa-mobile-alt text-gray-500 text-xl mb-2"></i>
                                        <p class="font-medium">Mobile Money</p>
                                    </div>
                                </div>
                            </div>

                            <button id="confirmBooking" class="w-full bg-green-600 text-white py-4 rounded-lg font-semibold text-lg hover:bg-green-700 transition flex items-center justify-center">
                                <i class="fas fa-lock mr-3"></i> Pay ₵1,200 to Confirm Booking
                            </button>
                            <p class="text-center text-gray-500 text-sm mt-3">Your booking will be confirmed upon payment</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Login Modal -->
    <div id="loginModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full p-8">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-gray-800">Student Login</h3>
                <button id="closeLoginModal" class="text-gray-500 hover:text-gray-700 text-2xl">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="space-y-6">
                <div>
                    <label class="block text-gray-600 mb-1">Student ID / Email</label>
                    <input type="text" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none" placeholder="Enter your student ID or email">
                </div>
                <div>
                    <label class="block text-gray-600 mb-1">Password</label>
                    <input type="password" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none" placeholder="Enter your password">
                </div>
                <button class="w-full bg-blue-700 text-white py-3 rounded-lg font-semibold hover:bg-blue-800 transition">
                    Login to Student Portal
                </button>
                <p class="text-center text-gray-500">
                    Don't have an account? <a href="#" class="text-blue-600 font-medium">Register here</a>
                </p>
            </div>
        </div>
    </div>

    <script>
        // Sample hostel data
        const hostels = [
            {
                id: 1,
                name: "Atlantic View Hostel",
                location: "amamoma",
                locationName: "Amamoma",
                price: 1200,
                available: 8,
                rating: 4.5,
                images: [
                    "https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80",
                    "https://images.unsplash.com/photo-1555854877-bab0e564b8d5?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80",
                    "https://images.unsplash.com/photo-1586023492125-27b2c045efd7?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80"
                ],
                amenities: ["WiFi", "24/7 Security", "Study Room", "Laundry", "Parking"],
                description: "Modern hostel with sea view, located just 10 minutes walk from campus.",
                roomType: "Single Room"
            },
            {
                id: 2,
                name: "Kwaprow Lodge",
                location: "kwaprow",
                locationName: "Kwaprow",
                price: 850,
                available: 12,
                rating: 4.2,
                images: [
                    "https://images.unsplash.com/photo-1512918728675-ed5a9ecdebfd?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80",
                    "https://images.unsplash.com/photo-1493663284031-b7e3aefcae8e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80",
                    "https://images.unsplash.com/photo-1560185127-6ed189bf02f4?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80"
                ],
                amenities: ["WiFi", "24/7 Security", "Common Room", "Cafeteria"],
                description: "Affordable student accommodation with a friendly community atmosphere.",
                roomType: "Shared Room (2 beds)"
            },
            {
                id: 3,
                name: "Ayensu Residences",
                location: "ayensu",
                locationName: "Ayensu",
                price: 1100,
                available: 5,
                rating: 4.7,
                images: [
                    "https://images.unsplash.com/photo-1578683010236-d716f9a3f461?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80",
                    "https://images.unsplash.com/photo-1560185127-6ed189bf02f4?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80",
                    "https://images.unsplash.com/photo-1586023492125-27b2c045efd7?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80"
                ],
                amenities: ["WiFi", "24/7 Security", "Gym", "Study Room", "Laundry", "Parking", "Cafeteria"],
                description: "Premium student accommodation with modern facilities and gym access.",
                roomType: "Single Room with AC"
            },
            {
                id: 4,
                name: "Schoolbus Road Hostel",
                location: "schoolbus",
                locationName: "Schoolbus Road",
                price: 750,
                available: 15,
                rating: 4.0,
                images: [
                    "https://images.unsplash.com/photo-1555854877-bab0e564b8d5?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80",
                    "https://images.unsplash.com/photo-1560185127-6ed189bf02f4?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80",
                    "https://images.unsplash.com/photo-1586023492125-27b2c045efd7?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80"
                ],
                amenities: ["WiFi", "Security", "Common Room"],
                description: "Economical option for students on a budget, located near school bus stop.",
                roomType: "Shared Room (4 beds)"
            },
            {
                id: 5,
                name: "Oldsite Comfort Hostel",
                location: "oldsite",
                locationName: "Oldsite",
                price: 950,
                available: 6,
                rating: 4.3,
                images: [
                    "https://images.unsplash.com/photo-1493663284031-b7e3aefcae8e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80",
                    "https://images.unsplash.com/photo-1560185127-6ed189bf02f4?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80",
                    "https://images.unsplash.com/photo-1586023492125-27b2c045efd7?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80"
                ],
                amenities: ["WiFi", "24/7 Security", "Study Room", "Laundry", "Parking"],
                description: "Comfortable accommodation in the historic Oldsite area, close to lecture halls.",
                roomType: "Single Room"
            },
            {
                id: 6,
                name: "Cape Coast Suites",
                location: "amamoma",
                locationName: "Amamoma",
                price: 1400,
                available: 3,
                rating: 4.8,
                images: [
                    "https://images.unsplash.com/photo-1578683010236-d716f9a3f461?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80",
                    "https://images.unsplash.com/photo-1560185127-6ed189bf02f4?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80",
                    "https://images.unsplash.com/photo-1586023492125-27b2c045efd7?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80"
                ],
                amenities: ["WiFi", "24/7 Security", "Gym", "Pool", "Study Room", "Laundry", "Parking", "Cafeteria"],
                description: "Luxury student accommodation with premium amenities including swimming pool.",
                roomType: "Executive Suite"
            }
        ];

        // DOM elements
        const hostelsContainer = document.getElementById('hostelsContainer');
        const locationButtons = document.querySelectorAll('.location-btn');
        const searchInput = document.getElementById('searchInput');
        const priceFilter = document.getElementById('priceFilter');
        const applyFilters = document.getElementById('applyFilters');
        const bookingModal = document.getElementById('bookingModal');
        const closeModal = document.getElementById('closeModal');
        const closeLoginModal = document.getElementById('closeLoginModal');
        const confirmBooking = document.getElementById('confirmBooking');

        // Current filter state
        let currentFilter = {
            location: 'all',
            price: '',
            search: ''
        };

        // Initialize the page
        function init() {
            renderHostels(hostels);
            setupEventListeners();
        }

        // Render hostels to the page
        function renderHostels(hostelsArray) {
            hostelsContainer.innerHTML = '';

            if (hostelsArray.length === 0) {
                hostelsContainer.innerHTML = `
                    <div class="col-span-full text-center py-12">
                        <i class="fas fa-search text-gray-400 text-5xl mb-4"></i>
                        <h3 class="text-xl font-semibold text-gray-700 mb-2">No hostels found</h3>
                        <p class="text-gray-500">Try adjusting your filters or search terms</p>
                    </div>
                `;
                return;
            }

            hostelsArray.forEach(hostel => {
                const hostelCard = document.createElement('div');
                hostelCard.className = 'hostel-card bg-white rounded-xl shadow-md overflow-hidden border border-gray-200';
                hostelCard.innerHTML = `
                    <div class="relative">
                        <img src="${hostel.images[0]}" alt="${hostel.name}" class="w-full h-48 object-cover">
                        <div class="absolute top-4 right-4 bg-blue-700 text-white px-3 py-1 rounded-lg font-semibold">
                            ₵${hostel.price}
                        </div>
                        <div class="absolute top-4 left-4 bg-white text-gray-800 px-3 py-1 rounded-lg font-medium text-sm">
                            ${hostel.locationName}
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-3">
                            <h3 class="text-xl font-bold text-gray-800">${hostel.name}</h3>
                            <div class="flex items-center">
                                <i class="fas fa-star text-yellow-500 mr-1"></i>
                                <span class="font-medium">${hostel.rating}</span>
                            </div>
                        </div>
                        <p class="text-gray-600 mb-4">${hostel.description}</p>

                        <div class="mb-4">
                            <p class="text-gray-700 font-medium mb-2">Amenities:</p>
                            <div class="flex flex-wrap gap-2">
                                ${hostel.amenities.slice(0, 3).map(amenity => `
                                    <span class="bg-gray-100 text-gray-800 text-xs px-3 py-1 rounded-full">${amenity}</span>
                                `).join('')}
                                ${hostel.amenities.length > 3 ? `<span class="bg-gray-100 text-gray-800 text-xs px-3 py-1 rounded-full">+${hostel.amenities.length - 3}</span>` : ''}
                            </div>
                        </div>

                        <div class="flex justify-between items-center mb-4">
                            <div>
                                <p class="text-gray-700"><i class="fas fa-bed text-blue-500 mr-2"></i>${hostel.roomType}</p>
                            </div>
                            <div class="text-green-600 font-semibold">
                                <i class="fas fa-check-circle mr-1"></i> ${hostel.available} Available
                            </div>
                        </div>

                        <button class="book-btn w-full bg-blue-700 text-white py-3 rounded-lg font-semibold hover:bg-blue-800 transition" data-id="${hostel.id}">
                            <i class="fas fa-calendar-check mr-2"></i> Book Now
                        </button>
                    </div>
                `;
                hostelsContainer.appendChild(hostelCard);
            });

            // Add event listeners to book buttons
            document.querySelectorAll('.book-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const hostelId = parseInt(this.getAttribute('data-id'));
                    openBookingModal(hostelId);
                });
            });
        }

        // Filter hostels based on current filters
        function filterHostels() {
            let filteredHostels = [...hostels];

            // Filter by location
            if (currentFilter.location !== 'all') {
                filteredHostels = filteredHostels.filter(hostel => hostel.location === currentFilter.location);
            }

            // Filter by price
            if (currentFilter.price) {
                if (currentFilter.price === '0-500') {
                    filteredHostels = filteredHostels.filter(hostel => hostel.price < 500);
                } else if (currentFilter.price === '500-1000') {
                    filteredHostels = filteredHostels.filter(hostel => hostel.price >= 500 && hostel.price <= 1000);
                } else if (currentFilter.price === '1000-1500') {
                    filteredHostels = filteredHostels.filter(hostel => hostel.price >= 1000 && hostel.price <= 1500);
                } else if (currentFilter.price === '1500+') {
                    filteredHostels = filteredHostels.filter(hostel => hostel.price > 1500);
                }
            }

            // Filter by search term
            if (currentFilter.search) {
                const searchTerm = currentFilter.search.toLowerCase();
                filteredHostels = filteredHostels.filter(hostel =>
                    hostel.name.toLowerCase().includes(searchTerm) ||
                    hostel.description.toLowerCase().includes(searchTerm) ||
                    hostel.amenities.some(amenity => amenity.toLowerCase().includes(searchTerm)) ||
                    hostel.locationName.toLowerCase().includes(searchTerm)
                );
            }

            renderHostels(filteredHostels);
        }

        // Open booking modal with hostel details
        function openBookingModal(hostelId) {
            const hostel = hostels.find(h => h.id === hostelId);
            if (!hostel) return;

            // Update modal content
            document.getElementById('modalHostelName').textContent = hostel.name;
            document.getElementById('modalHostelLocation').innerHTML = `<i class="fas fa-map-marker-alt text-blue-500 mr-2"></i>${hostel.locationName}`;
            document.getElementById('modalPrice').textContent = `₵${hostel.price}`;
            document.getElementById('modalAvailable').textContent = `${hostel.available} Rooms`;
            document.getElementById('modalRoomType').textContent = hostel.roomType;

            // Update main image
            const mainImage = document.getElementById('modalMainImage');
            mainImage.src = hostel.images[0];
            mainImage.alt = hostel.name;

            // Update amenity badges
            const amenitiesContainer = document.getElementById('modalAmenities');
            amenitiesContainer.innerHTML = hostel.amenities.map(amenity =>
                `<span class="bg-blue-100 text-blue-800 text-xs px-3 py-1 rounded-full">${amenity}</span>`
            ).join('');

            // Add click events to thumbnail images
            const thumbnails = document.querySelectorAll('#modalHostelImages .w-24');
            thumbnails.forEach((thumb, index) => {
                if (index < hostel.images.length) {
                    thumb.src = hostel.images[index];
                    thumb.addEventListener('click', function() {
                        mainImage.src = this.src;
                        // Update active thumbnail
                        thumbnails.forEach(t => t.classList.remove('border-2', 'border-blue-500'));
                        this.classList.add('border-2', 'border-blue-500');
                    });
                }
            });

            // Show modal
            bookingModal.style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        // Setup event listeners
        function setupEventListeners() {
            // Location filter buttons
            locationButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Remove active class from all buttons
                    locationButtons.forEach(btn => {
                        btn.classList.remove('active', 'bg-blue-100', 'text-blue-800');
                        btn.classList.add('bg-gray-100', 'text-gray-800');
                    });

                    // Add active class to clicked button
                    this.classList.remove('bg-gray-100', 'text-gray-800');
                    this.classList.add('active', 'bg-blue-100', 'text-blue-800');

                    // Update current filter
                    currentFilter.location = this.getAttribute('data-location');
                    filterHostels();
                });
            });

            // Search input
            searchInput.addEventListener('input', function() {
                currentFilter.search = this.value;
            });

            // Price filter
            priceFilter.addEventListener('change', function() {
                currentFilter.price = this.value;
            });

            // Apply filters button
            applyFilters.addEventListener('click', filterHostels);

            // Close booking modal
            closeModal.addEventListener('click', function() {
                bookingModal.style.display = 'none';
                document.body.style.overflow = 'auto';
            });

            // Close modal when clicking outside
            window.addEventListener('click', function(event) {
                if (event.target === bookingModal) {
                    bookingModal.style.display = 'none';
                    document.body.style.overflow = 'auto';
                }
                if (event.target === loginModal) {
                    loginModal.classList.add('hidden');
                    document.body.style.overflow = 'auto';
                }
            });

          

            // Close login modal
            closeLoginModal.addEventListener('click', function() {
                loginModal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            });

            // Confirm booking button
            confirmBooking.addEventListener('click', function() {
                alert('Booking confirmed! Payment gateway would open in a real application.');
                bookingModal.style.display = 'none';
                document.body.style.overflow = 'auto';
            });

            // Allow Enter key to trigger search
            searchInput.addEventListener('keyup', function(event) {
                if (event.key === 'Enter') {
                    filterHostels();
                }
            });
        }

        // Initialize the application
        init();
    </script>
</body>
</html>
