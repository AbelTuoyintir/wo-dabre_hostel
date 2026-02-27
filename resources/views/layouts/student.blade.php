<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Student Portal') - {{ config('app.name') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .student-card {
            transition: all 0.3s;
        }
        .student-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .hostel-card {
            transition: all 0.3s;
        }
        .hostel-card:hover {
            transform: scale(1.02);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }
        .mobile-menu {
            transition: all 0.3s ease-in-out;
        }
    </style>
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen bg-gray-100">
        <!-- Top Navigation -->
        <nav class="bg-white shadow-lg" x-data="{ mobileOpen: false }">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="flex-shrink-0 flex items-center">
                            <a href="{{ route('student.dashboard') }}" class="text-xl font-bold text-blue-600">
                                {{ config('app.name') }}
                            </a>
                        </div>

                        <!-- Desktop Navigation -->
                        <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                            <a href="{{ route('student.dashboard') }}"
                               class="inline-flex items-center px-1 pt-1 text-sm font-medium {{ request()->routeIs('student.dashboard') ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-700' }}">
                                <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                            </a>
                            <a href="{{ route('student.hostels.browse') }}"
                               class="inline-flex items-center px-1 pt-1 text-sm font-medium {{ request()->routeIs('student.hostels.*') ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-700' }}">
                                <i class="fas fa-building mr-2"></i>Browse Hostels
                            </a>
                            <a href="{{ route('student.bookings') }}"
                               class="inline-flex items-center px-1 pt-1 text-sm font-medium {{ request()->routeIs('student.bookings.*') ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-700' }}">
                                <i class="fas fa-calendar-check mr-2"></i>My Bookings
                            </a>
                            <a href="{{ route('student.payments') }}"
                               class="inline-flex items-center px-1 pt-1 text-sm font-medium {{ request()->routeIs('student.payments.*') ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-700' }}">
                                <i class="fas fa-credit-card mr-2"></i>Payments
                            </a>
                            <a href="{{ route('student.complaints') }}"
                               class="inline-flex items-center px-1 pt-1 text-sm font-medium {{ request()->routeIs('student.complaints.*') ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-700' }}">
                                <i class="fas fa-exclamation-triangle mr-2"></i>Complaints
                                @php
                                    $pendingComplaints = Auth::user()->complaints()->whereIn('status', ['pending', 'in_progress'])->count();
                                @endphp
                                @if($pendingComplaints > 0)
                                    <span class="ml-2 bg-red-500 text-white text-xs rounded-full px-2 py-0.5">
                                        {{ $pendingComplaints }}
                                    </span>
                                @endif
                            </a>
                        </div>
                    </div>

                    <!-- Mobile menu button -->
                    <div class="flex items-center sm:hidden">
                        <button @click="mobileOpen = !mobileOpen" class="text-gray-500 hover:text-gray-700">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                    </div>

                    <!-- User Menu -->
                    <div class="hidden sm:flex items-center">
                        <div class="ml-3 relative">
                            <div x-data="{ open: false }">
                                <button @click="open = !open" class="flex items-center text-sm focus:outline-none">
                                    <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white">
                                        {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <span class="ml-2 text-gray-700">{{ Auth::user()->name ?? 'User' }}</span>
                                    <i class="fas fa-chevron-down ml-1 text-xs text-gray-500"></i>
                                </button>

                                <div x-show="open" @click.outside="open = false"
                                     class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl py-1 z-50">
                                    <a href="{{ route('student.profile') }}"
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-user mr-2"></i> Profile
                                    </a>
                                    <hr class="my-1">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile Navigation Menu -->
            <div x-show="mobileOpen" @click.outside="mobileOpen = false" class="sm:hidden border-t border-gray-200">
                <div class="pt-2 pb-3 space-y-1">
                    <a href="{{ route('student.dashboard') }}"
                       class="block px-3 py-2 text-base font-medium {{ request()->routeIs('student.dashboard') ? 'text-blue-600 bg-blue-50' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' }}">
                        <i class="fas fa-tachometer-alt mr-2 w-5"></i>Dashboard
                    </a>
                    <a href="{{ route('student.hostels.browse') }}"
                       class="block px-3 py-2 text-base font-medium {{ request()->routeIs('student.hostels.*') ? 'text-blue-600 bg-blue-50' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' }}">
                        <i class="fas fa-building mr-2 w-5"></i>Browse Hostels
                    </a>
                    <a href="{{ route('student.bookings') }}"
                       class="block px-3 py-2 text-base font-medium {{ request()->routeIs('student.bookings.*') ? 'text-blue-600 bg-blue-50' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' }}">
                        <i class="fas fa-calendar-check mr-2 w-5"></i>My Bookings
                    </a>
                    <a href="{{ route('student.payments') }}"
                       class="block px-3 py-2 text-base font-medium {{ request()->routeIs('student.payments.*') ? 'text-blue-600 bg-blue-50' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' }}">
                        <i class="fas fa-credit-card mr-2 w-5"></i>Payments
                    </a>
                    <a href="{{ route('student.complaints') }}"
                       class="block px-3 py-2 text-base font-medium {{ request()->routeIs('student.complaints.*') ? 'text-blue-600 bg-blue-50' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' }}">
                        <i class="fas fa-exclamation-triangle mr-2 w-5"></i>Complaints
                        @php
                            $pendingComplaints = Auth::user()->complaints()->whereIn('status', ['pending', 'in_progress'])->count();
                        @endphp
                        @if($pendingComplaints > 0)
                            <span class="ml-2 bg-red-500 text-white text-xs rounded-full px-2 py-0.5">
                                {{ $pendingComplaints }}
                            </span>
                        @endif
                    </a>
                    <hr class="my-2">
                    <a href="{{ route('student.profile') }}"
                       class="block px-3 py-2 text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50">
                        <i class="fas fa-user mr-2 w-5"></i>Profile
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="block">
                        @csrf
                        <button type="submit" class="w-full text-left px-3 py-2 text-base font-medium text-red-600 hover:bg-red-50">
                            <i class="fas fa-sign-out-alt mr-2 w-5"></i>Logout
                        </button>
                    </form>
                </div>
            </div>
        </nav>

        <!-- Page Title -->
        <div class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <h1 class="text-2xl font-bold text-gray-800">@yield('page-title', 'Dashboard')</h1>
                @hasSection('breadcrumb')
                    <div class="text-sm text-gray-500 mt-1">
                        @yield('breadcrumb')
                    </div>
                @endif
            </div>
        </div>

        <!-- Page Content -->
        <main class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Alert Messages -->
                @if(session('success'))
                    <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded relative flex items-center">
                        <i class="fas fa-check-circle text-green-500 mr-3"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded relative flex items-center">
                        <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="mb-4 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 px-4 py-3 rounded relative flex items-center">
                        <i class="fas fa-exclamation-triangle text-yellow-500 mr-3"></i>
                        <span>{{ session('warning') }}</span>
                    </div>
                @endif

                @if(session('info'))
                    <div class="mb-4 bg-blue-100 border-l-4 border-blue-500 text-blue-700 px-4 py-3 rounded relative flex items-center">
                        <i class="fas fa-info-circle text-blue-500 mr-3"></i>
                        <span>{{ session('info') }}</span>
                    </div>
                @endif

                <!-- Validation Errors -->
                @if($errors->any())
                    <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded">
                        <div class="flex items-center mb-2">
                            <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
                            <strong>Please fix the following errors:</strong>
                        </div>
                        <ul class="list-disc list-inside ml-6">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Main Content -->
                @yield('content')
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t mt-8">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <p class="text-center text-gray-500 text-sm">
                    &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                </p>
            </div>
        </footer>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @stack('scripts')
</body>
</html>