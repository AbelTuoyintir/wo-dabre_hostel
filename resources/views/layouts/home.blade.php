<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'UCC Hostel Booking System')</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link rel="icon" href="{{ asset('/images/wodabre-logo.png') }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ asset('/images/wodabre-logo.png') }}" type="image/x-icon">

    <!-- Lucida Font Stack -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- Vite Assets (your custom CSS/JS) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Lucida Font Family */
        * {
            font-family: 'Inter', 'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif;
        }

        /* Specific font weights for Lucida */
        .font-light { font-weight: 300; }
        .font-regular { font-weight: 400; }
        .font-medium { font-weight: 500; }
        .font-semibold { font-weight: 600; }
        .font-bold { font-weight: 700; }

        .location-btn.active {
            background-color: #1d4ed8;
            color: white;
        }
        .hostel-card {
            transition: transform 0.3s ease;
        }
        .hostel-card:hover {
            transform: translateY(-5px);
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
        
        /* Custom scrollbar hide for categories */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* Mobile menu transition */
        #mobileMenu {
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        /* Search bar focus styles */
        .search-input:focus {
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        /* Lucida-specific letter spacing for better readability */
        .tracking-lucida {
            letter-spacing: 0.02em;
        }

        /* Logo text styling */
        .logo-text {
            font-family: 'Inter', 'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', sans-serif;
            font-weight: 700;
            letter-spacing: -0.5px;
        }
    </style>

    @stack('styles')
</head>
<body class="bg-gray-50">

    <!-- Redesigned Header -->
    <header class="bg-white shadow-sm">
        <!-- Top Bar - Brand & User Actions -->
        <div class="border-b border-gray-100">
            <div class="container mx-auto px-4 md:px-8">
                <div class="flex items-center justify-between h-20">
                    <!-- Logo -->
                    <a href="/" class="flex items-center gap-2 group">
                        <div class="w-10 h-10 bg-gradient-to-br from-white-600 to-blue-800 rounded-xl flex items-center justify-center shadow-md group-hover:shadow-lg transition-all">
                            <img src="{{ asset('wodabre-logo.png') }}" alt="Wo-dabre Logo" class="w-6 h-6 object-contain">
                        </div>
                        <div>
                            <h1 class="logo-text text-xl text-gray-800 tracking-tight">Wo<span class="text-blue-600">dabre</span></h1>
                            <p class="text-[10px] text-gray-400 font-medium tracking-wider uppercase" style="letter-spacing: 0.05em;">Find Your Home Away</p>
                        </div>
                    </a>

                    <!-- Desktop Navigation & Auth -->
                    <div class="hidden lg:flex items-center gap-6">

                        <!-- Divider -->
                        <div class="w-px h-6 bg-gray-200"></div>

                        <!-- Auth Buttons -->
                        @guest
                            <div class="flex items-center gap-3">
                                <a href="{{ route('register') }}"
                                   class="text-sm font-medium text-gray-600 hover:text-blue-600 transition-colors" style="font-family: 'Inter', 'Lucida Sans', sans-serif;">
                                    Sign Up
                                </a>
                                <a href="{{ route('login') }}"
                                   class="bg-blue-600 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 shadow-sm hover:shadow transition-all" style="font-family: 'Inter', 'Lucida Sans', sans-serif;">
                                  Login
                                </a>
                            </div>
                        @else
                            <div class="flex items-center gap-3">
                                @if(auth()->user()->role === 'student')
                                    <a href="{{ route('student.dashboard') }}"
                                       class="bg-emerald-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-emerald-600 shadow-sm hover:shadow transition-all" style="font-family: 'Inter', 'Lucida Sans', sans-serif;">
                                        <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                                    </a>
                                @elseif(auth()->user()->role === 'admin')
                                    <a href="{{ route('admin.dashboard') }}"
                                       class="bg-purple-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-purple-700 shadow-sm hover:shadow transition-all" style="font-family: 'Inter', 'Lucida Sans', sans-serif;">
                                        <i class="fas fa-cog mr-2"></i>Admin
                                    </a>
                                @elseif(auth()->user()->role === 'manager')
                                    <a href="{{ route('hostel-manager.dashboard') }}"
                                       class="bg-orange-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-orange-600 shadow-sm hover:shadow transition-all" style="font-family: 'Inter', 'Lucida Sans', sans-serif;">
                                        <i class="fas fa-building mr-2"></i>Manager
                                    </a>
                                @endif
                                <form method="POST" action="{{ route('logout') }}" class="inline">
                                    @csrf
                                    <button type="submit"
                                            class="text-sm font-medium text-gray-600 hover:text-red-600 transition-colors" style="font-family: 'Inter', 'Lucida Sans', sans-serif;">
                                        <i class="fas fa-sign-out-alt mr-1"></i>Logout
                                    </button>
                                </form>
                            </div>
                        @endguest
                    </div>

                    <!-- Mobile Menu Button -->
                    <button class="lg:hidden p-2 hover:bg-gray-100 rounded-lg transition" id="mobileMenuBtn" aria-label="Toggle menu">
                        <i class="fas fa-bars text-gray-600 text-xl"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Search & Filters Section -->
        <div class="border-b border-gray-100 bg-gray-50/50">
            <div class="container mx-auto px-4 md:px-8 py-4">
                <!-- Desktop Search Bar -->
                <div class="hidden md:block max-w-4xl mx-auto">
                    <form action="{{ route('hostels.index') }}" method="GET" 
                          class="flex items-center bg-white rounded-2xl shadow-sm hover:shadow-md border border-gray-200 transition-all overflow-hidden">
                        
                        <!-- Location Input -->
                        <div class="flex-1 px-5 py-3 border-r border-gray-100">
                            <label class="block text-[10px] font-semibold text-gray-500 uppercase tracking-wider" style="font-family: 'Inter', 'Lucida Sans', sans-serif; letter-spacing: 0.05em;">
                                Location
                            </label>
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="Search by area, landmark..." 
                                   class="w-full text-sm text-gray-700 placeholder:text-gray-400 bg-transparent border-none focus:ring-0 p-0 outline-none" style="font-family: 'Inter', 'Lucida Sans', sans-serif;">
                        </div>

                        <!-- Campus Select -->
                        <div class="flex-1 px-5 py-3 border-r border-gray-100">
                            <label class="block text-[10px] font-semibold text-gray-500 uppercase tracking-wider" style="font-family: 'Inter', 'Lucida Sans', sans-serif; letter-spacing: 0.05em;">
                                Campus
                            </label>
                            <select name="location" 
                                    class="w-full text-sm text-gray-700 bg-transparent border-none focus:ring-0 p-0 outline-none appearance-none cursor-pointer" style="font-family: 'Inter', 'Lucida Sans', sans-serif;">
                                <option value="all">All Campuses</option>
                                @if(isset($locations))
                                    @foreach($locations as $loc)
                                        <option value="{{ $loc }}" {{ request('location') == $loc ? 'selected' : '' }}>
                                            {{ $loc }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <!-- Price Select -->
                        <div class="flex-1 px-5 py-3">
                            <label class="block text-[10px] font-semibold text-gray-500 uppercase tracking-wider" style="font-family: 'Inter', 'Lucida Sans', sans-serif; letter-spacing: 0.05em;">
                                Budget
                            </label>
                            <select name="price_range" 
                                    class="w-full text-sm text-gray-700 bg-transparent border-none focus:ring-0 p-0 outline-none appearance-none cursor-pointer" style="font-family: 'Inter', 'Lucida Sans', sans-serif;">
                                <option value="">Any price</option>
                                <option value="0-2000" {{ request('price_range') == '0-2000' ? 'selected' : '' }}>Under ₵2,000</option>
                                <option value="2100-4000" {{ request('price_range') == '2100-4000' ? 'selected' : '' }}>₵2,100 - ₵4,000</option>
                                <option value="4100-6000" {{ request('price_range') == '4100-6000' ? 'selected' : '' }}>₵4,100 - ₵6,000</option>
                                <option value="6100-8000" {{ request('price_range') == '6100-8000' ? 'selected' : '' }}>₵6,100 - ₵8,000</option>
                                <option value="8200+" {{ request('price_range') == '8200+' ? 'selected' : '' }}>Above ₵8,200</option>
                            </select>
                        </div>

                        <!-- Search Button -->
                        <button type="submit" 
                                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 m-1 rounded-xl transition-colors shadow-sm hover:shadow" style="font-family: 'Inter', 'Lucida Sans', sans-serif;">
                            <i class="fas fa-search mr-2"></i>
                            <span class="font-medium">Search</span>
                        </button>
                    </form>
                </div>

                <!-- Mobile Search -->
                <div class="md:hidden">
                    <form action="{{ route('hostels.index') }}" method="GET" 
                          class="flex items-center bg-white rounded-xl shadow-sm border border-gray-200 p-2">
                        <i class="fas fa-search text-blue-500 mx-3"></i>
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Where to?" 
                               class="flex-1 text-sm text-gray-700 placeholder:text-gray-400 bg-transparent border-none focus:ring-0 p-2 outline-none" style="font-family: 'Inter', 'Lucida Sans', sans-serif;">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium" style="font-family: 'Inter', 'Lucida Sans', sans-serif;">
                            Go
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Category Navigation -->
        <div class="container mx-auto px-4 md:px-8">
            <div class="flex items-center gap-6 overflow-x-auto no-scrollbar py-3">
                <!-- All Campuses -->
                <a href="{{ route('hostels.index', array_merge(request()->except('location'), ['location' => 'all'])) }}"
                   class="flex items-center gap-2 px-2 py-1 border-b-2 {{ !request('location') || request('location') == 'all' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} transition-all whitespace-nowrap" style="font-family: 'Inter', 'Lucida Sans', sans-serif;">
                    <i class="fas fa-th-large text-sm"></i>
                    <span class="text-sm font-medium">All</span>
                </a>

                <!-- Campus Filters -->
                @if(isset($locations))
                    @foreach($locations as $location)
                        <a href="{{ route('hostels.index', array_merge(request()->except('location'), ['location' => $location])) }}"
                           class="flex items-center gap-2 px-2 py-1 border-b-2 {{ request('location') == $location ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} transition-all whitespace-nowrap" style="font-family: 'Inter', 'Lucida Sans', sans-serif;">
                            <i class="fas fa-map-pin text-sm"></i>
                            <span class="text-sm font-medium">{{ $location }}</span>
                        </a>
                    @endforeach
                @endif

                <!-- Filter Button -->
                <div class="ml-auto">
                    <button class="flex items-center gap-2 bg-gray-100 hover:bg-gray-200 rounded-full px-4 py-1.5 text-sm font-medium text-gray-700 transition" style="font-family: 'Inter', 'Lucida Sans', sans-serif;">
                        <i class="fas fa-sliders-h text-xs"></i>
                        <span>Filters</span>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Mobile Navigation Overlay -->
    <div class="fixed inset-0 bg-black/50 z-50 hidden" id="mobileOverlay"></div>
    <div class="fixed top-0 left-0 w-80 h-full bg-white z-50 transform -translate-x-full shadow-xl hidden" id="mobileMenu">
        <div class="p-6">
            <div class="flex justify-between items-center mb-8">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-gradient-to-br from-blue-600 to-blue-800 rounded-lg flex items-center justify-center">
                        <img src="{{ asset('wodabre-logo.png') }}" alt="Wo-dabre Logo" class="w-5 h-5 object-contain">
                    </div>
                     <h1 class="logo-text text-xl text-gray-800 tracking-tight">Wo<span class="text-blue-600">dabre</span></h1>
                </div>
                <button id="closeMobileMenu" class="p-2 hover:bg-gray-100 rounded-lg transition" aria-label="Close menu">
                    <i class="fas fa-times text-gray-600"></i>
                </button>
            </div>
            
            <nav class="space-y-4" style="font-family: 'Inter', 'Lucida Sans', sans-serif;">
                {{-- <a href="#" class="block text-gray-700 font-medium hover:text-blue-600 transition">List Hostel</a>
                <a href="#" class="block text-gray-700 font-medium hover:text-blue-600 transition">Help</a> --}}
                
                @guest
                    <div class="pt-4 border-t border-gray-200 space-y-3">
                        <a href="{{ route('register') }}" class="block text-center bg-blue-600 text-white py-2.5 rounded-lg font-medium hover:bg-blue-700 transition" style="font-family: 'Inter', 'Lucida Sans', sans-serif;">
                            Create Account
                        </a>
                        <a href="{{ route('login') }}" class="block text-center border border-gray-300 text-gray-700 py-2.5 rounded-lg font-medium hover:bg-gray-50 transition" style="font-family: 'Inter', 'Lucida Sans', sans-serif;">
                            Login
                        </a>
                    </div>
                @else
                    <div class="pt-4 border-t border-gray-200 space-y-3">
                        <a href="{{ auth()->user()->role === 'student' ? route('student.dashboard') : (auth()->user()->role === 'admin' ? route('admin.dashboard') : route('hostel-manager.dashboard')) }}" 
                           class="block text-center bg-blue-600 text-white py-2.5 rounded-lg font-medium hover:bg-blue-700 transition" style="font-family: 'Inter', 'Lucida Sans', sans-serif;">
                            Dashboard
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-center border border-gray-300 text-gray-700 py-2.5 rounded-lg font-medium hover:bg-gray-50 transition" style="font-family: 'Inter', 'Lucida Sans', sans-serif;">
                                Logout
                            </button>
                        </form>
                    </div>
                @endguest
            </nav>
        </div>
    </div>

    <!-- Loading Spinner (hidden by default) -->
    <div id="loadingSpinner" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-[9999] flex items-center justify-center">
        <div class="loader"></div>
    </div>

    <!-- Main Content -->
    <main class="container mx-auto py-8">
        <!-- Alert Messages (converted to SweetAlert automatically) -->
        @if(session('success'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: '{{ session('success') }}',
                        timer: 3000,
                        showConfirmButton: false,
                        position: 'top-end',
                        toast: true,
                        background: '#10b981',
                        color: '#ffffff',
                        iconColor: '#ffffff',
                        customClass: { popup: 'rounded-lg shadow-xl' }
                    });
                });
            </script>
        @endif

        @if(session('error'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: '{{ session('error') }}',
                        timer: 3000,
                        showConfirmButton: false,
                        position: 'top-end',
                        toast: true,
                        background: '#ef4444',
                        color: '#ffffff',
                        iconColor: '#ffffff',
                        customClass: { popup: 'rounded-lg shadow-xl' }
                    });
                });
            </script>
        @endif

        @if(session('warning'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning!',
                        text: '{{ session('warning') }}',
                        timer: 3000,
                        showConfirmButton: false,
                        position: 'top-end',
                        toast: true,
                        background: '#f59e0b',
                        color: '#ffffff',
                        iconColor: '#ffffff',
                        customClass: { popup: 'rounded-lg shadow-xl' }
                    });
                });
            </script>
        @endif

        @if(session('info'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'info',
                        title: 'Info',
                        text: '{{ session('info') }}',
                        timer: 3000,
                        showConfirmButton: false,
                        position: 'top-end',
                        toast: true,
                        background: '#3b82f6',
                        color: '#ffffff',
                        iconColor: '#ffffff',
                        customClass: { popup: 'rounded-lg shadow-xl' }
                    });
                });
            </script>
        @endif

        <!-- Main Content Yield -->
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white mt-16">
        <div class="container mx-auto px-4 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="bg-white p-2 rounded-full">
                            <img src="{{ asset('wodabre-logo.png') }}" alt="Wo-dabre Logo" class="w-10 h-10 object-contain">
                        </div>
                        <h3 class="text-xl font-bold" style="font-family: 'Inter', 'Lucida Sans', sans-serif;">Wodabre Hostel Booking</h3>
                    </div>
                    <p class="text-gray-400" style="font-family: 'Inter', 'Lucida Sans', sans-serif;">The official hostel booking platform for University of Cape Coast students.</p>
                </div>
                <div>
                    <h4 class="text-lg font-bold mb-4" style="font-family: 'Inter', 'Lucida Sans', sans-serif;">Quick Links</h4>
                    <ul class="space-y-2 text-gray-400" style="font-family: 'Inter', 'Lucida Sans', sans-serif;">
                        <li><a href="#" class="hover:text-white transition">About Us</a></li>
                        <li><a href="#" class="hover:text-white transition">Contact</a></li>
                        <li><a href="#" class="hover:text-white transition">FAQs</a></li>
                        <li><a href="#" class="hover:text-white transition">Privacy Policy</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-bold mb-4" style="font-family: 'Inter', 'Lucida Sans', sans-serif;">Locations</h4>
                    <ul class="space-y-2 text-gray-400" id="locationList" style="font-family: 'Inter', 'Lucida Sans', sans-serif;">
                        <li>Amamoma</li>
                        <li>Kwaprow</li>
                        <li>Ayensu</li>
                        <li>Schoolbus Road</li>
                        <li>Oldsite</li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-bold mb-4" style="font-family: 'Inter', 'Lucida Sans', sans-serif;">Contact Info</h4>
                    <ul class="space-y-3 text-gray-400" style="font-family: 'Inter', 'Lucida Sans', sans-serif;">
                        <li class="flex items-center">
                            <i class="fas fa-map-marker-alt mr-3 text-blue-400"></i>
                            <span>University of Cape Coast, Ghana</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-phone mr-3 text-blue-400"></i>
                            <span>+233 55 820 9825</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-envelope mr-3 text-blue-400"></i>
                            <span>hostelbooking@ucc.edu.gh</span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p style="font-family: 'Inter', 'Lucida Sans', sans-serif;">&copy; {{ date('Y') }} University of Cape Coast Hostel Booking System. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Alpine.js (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <script>
        // Mobile menu toggle
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const closeMobileMenuBtn = document.getElementById('closeMobileMenu');
        const mobileOverlay = document.getElementById('mobileOverlay');
        const mobileMenu = document.getElementById('mobileMenu');

        function openMobileMenu() {
            mobileMenu.classList.remove('hidden');
            mobileOverlay.classList.remove('hidden');
            setTimeout(() => {
                mobileMenu.classList.remove('-translate-x-full');
            }, 10);
            document.body.style.overflow = 'hidden';
        }

        function closeMobileMenu() {
            mobileMenu.classList.add('-translate-x-full');
            mobileOverlay.classList.add('hidden');
            setTimeout(() => {
                mobileMenu.classList.add('hidden');
                document.body.style.overflow = '';
            }, 300);
        }

        if (mobileMenuBtn) {
            mobileMenuBtn.addEventListener('click', openMobileMenu);
        }
        if (closeMobileMenuBtn) {
            closeMobileMenuBtn.addEventListener('click', closeMobileMenu);
        }
        if (mobileOverlay) {
            mobileOverlay.addEventListener('click', closeMobileMenu);
        }

        // Close mobile menu on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !mobileMenu.classList.contains('hidden')) {
                closeMobileMenu();
            }
        });

        // Loading spinner functions
        function showLoading() {
            document.getElementById('loadingSpinner').classList.remove('hidden');
        }

        function hideLoading() {
            document.getElementById('loadingSpinner').classList.add('hidden');
        }

        // Auto-show loader on form submissions and button clicks
        document.addEventListener('DOMContentLoaded', function() {
            // Show loader on all form submissions
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function() {
                    if (!this.classList.contains('no-loader')) {
                        showLoading();
                    }
                });
            });

            // Show loader on clicks for buttons and links that aren't just anchors
            document.querySelectorAll('button:not([type="button"]), a.btn, a.show-loader').forEach(el => {
                el.addEventListener('click', function(e) {
                    if (this.tagName === 'A' && (this.getAttribute('href').startsWith('#') || this.getAttribute('target') === '_blank')) {
                        return;
                    }

                    if (!this.classList.contains('no-loader') && !this.closest('form')) {
                        showLoading();
                    }
                });
            });
        });

        // Helper functions for SweetAlert toasts
        function showSuccessMessage(message) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: message,
                timer: 3000,
                showConfirmButton: false,
                position: 'top-end',
                toast: true,
                background: '#10b981',
                color: '#ffffff',
                iconColor: '#ffffff',
                customClass: { popup: 'rounded-lg shadow-xl' }
            });
        }

        function showErrorMessage(message) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: message,
                timer: 3000,
                showConfirmButton: false,
                position: 'top-end',
                toast: true,
                background: '#ef4444',
                color: '#ffffff',
                iconColor: '#ffffff',
                customClass: { popup: 'rounded-lg shadow-xl' }
            });
        }

        function showWarningMessage(message) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: message,
                timer: 3000,
                showConfirmButton: false,
                position: 'top-end',
                toast: true,
                background: '#f59e0b',
                color: '#ffffff',
                iconColor: '#ffffff',
                customClass: { popup: 'rounded-lg shadow-xl' }
            });
        }

        function showInfoMessage(message) {
            Swal.fire({
                icon: 'info',
                title: 'Info',
                text: message,
                timer: 3000,
                showConfirmButton: false,
                position: 'top-end',
                toast: true,
                background: '#3b82f6',
                color: '#ffffff',
                iconColor: '#ffffff',
                customClass: { popup: 'rounded-lg shadow-xl' }
            });
        }

        // Confirm dialog for delete/cancel actions
        function confirmAction(title, text, confirmCallback) {
            Swal.fire({
                title: title,
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, proceed',
                cancelButtonText: 'Cancel',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-xl',
                    confirmButton: 'bg-red-500 hover:bg-red-600 text-white font-medium py-2 px-4 rounded-lg',
                    cancelButton: 'bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg'
                }
            }).then((result) => {
                if (result.isConfirmed && confirmCallback) {
                    confirmCallback();
                }
            });
        }
    </script>

    @stack('scripts')
</body>
</html>