<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Hostel Manager') - {{ config('app.name') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Sidebar Styles */
        .sidebar {
            transition: all 0.3s ease-in-out;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            z-index: 1000;
        }

        /* Mobile sidebar - hidden by default */
        @media (max-width: 1023px) {
            .sidebar {
                transform: translateX(-100%);
                position: fixed !important;
                width: 280px !important;
                box-shadow: 4px 0 10px rgba(0, 0, 0, 0.1);
            }

            .sidebar.mobile-open {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0 !important;
                width: 100%;
            }

            .overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 999;
                animation: fadeIn 0.3s;
            }

            .overlay.active {
                display: block;
            }
        }

        /* Desktop sidebar */
        @media (min-width: 1024px) {
            .sidebar {
                transform: translateX(0);
                width: 256px;
            }

            .main-content {
                margin-left: 256px;
            }
        }

        .sidebar-link {
            transition: all 0.3s;
            border-radius: 0.5rem;
            margin: 0.25rem 0;
            font-size: 0.875rem;
        }

        @media (max-width: 640px) {
            .sidebar-link {
                font-size: 0.8125rem;
                padding: 0.75rem 0.75rem !important;
            }
        }

        .sidebar-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
        }

        .sidebar-link.active {
            background-color: rgba(255, 255, 255, 0.2);
            border-left: 4px solid #ffd700;
        }

        .sidebar-link i {
            width: 1.5rem;
            font-size: 1.1rem;
        }

        /* Main Content Area */
        .main-content {
            transition: margin-left 0.3s ease-in-out;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Stats Cards - Mobile Responsive */
        .stat-card {
            transition: all 0.3s;
            border: none;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        @media (max-width: 640px) {
            .stat-card {
                padding: 1rem !important;
            }

            .stat-card h3 {
                font-size: 1.25rem !important;
            }

            .stat-card p {
                font-size: 0.75rem !important;
            }
        }

        /* Table Styles - Horizontal Scroll on Mobile */
        .table-container {
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            margin: 0 -1px;
        }

        .table-responsive table {
            min-width: 800px;
        }

        @media (max-width: 768px) {
            .table-responsive {
                margin: 0;
                border-radius: 0.5rem;
            }
        }

        .table-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .table-row:hover {
            background-color: #f9fafb;
        }

        /* Badge Styles */
        .badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
        }

        @media (max-width: 640px) {
            .badge {
                font-size: 0.6875rem;
                padding: 0.2rem 0.5rem;
            }
        }

        .badge-success { background-color: #d1fae5; color: #065f46; }
        .badge-warning { background-color: #fef3c7; color: #92400e; }
        .badge-danger { background-color: #fee2e2; color: #991b1b; }
        .badge-info { background-color: #dbeafe; color: #1e40af; }
        .badge-purple { background-color: #f3e8ff; color: #6b21a8; }

        /* Button Styles */
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-weight: 500;
            transition: all 0.3s;
            border: none;
            font-size: 0.875rem;
        }

        @media (max-width: 640px) {
            .btn-primary, .btn-secondary {
                padding: 0.4rem 0.75rem;
                font-size: 0.8125rem;
            }
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .btn-secondary {
            background-color: #e5e7eb;
            color: #374151;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-weight: 500;
            transition: all 0.3s;
            font-size: 0.875rem;
        }

        .btn-secondary:hover {
            background-color: #d1d5db;
        }

        /* Modal Styles - Mobile Responsive */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            animation: fadeIn 0.3s;
            overflow-y: auto;
            padding: 1rem;
        }

        .modal-content {
            animation: slideIn 0.3s;
            max-height: 90vh;
            overflow-y: auto;
        }

        @media (max-width: 640px) {
            .modal-content {
                margin: 0 !important;
                width: 100% !important;
                border-radius: 1rem !important;
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideIn {
            from { transform: translateY(-50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        /* Notification Styles */
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            animation: slideInRight 0.3s;
            max-width: 90vw;
        }

        @media (max-width: 640px) {
            .notification {
                top: 10px;
                right: 10px;
                left: 10px;
                max-width: none;
            }
        }

        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        /* Loading Spinner */
        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #667eea;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* SweetAlert Custom Styles */
        .swal2-popup {
            border-radius: 1rem;
            padding: 1.5rem;
        }

        @media (max-width: 640px) {
            .swal2-popup {
                padding: 1rem;
                width: 90% !important;
            }
        }

        .swal2-title {
            font-size: 1.5rem;
            font-weight: 600;
        }

        @media (max-width: 640px) {
            .swal2-title {
                font-size: 1.25rem;
            }
        }

        .swal2-confirm {
            border-radius: 0.5rem !important;
            padding: 0.75rem 1.5rem !important;
            font-weight: 500 !important;
        }

        .swal2-cancel {
            border-radius: 0.5rem !important;
            padding: 0.75rem 1.5rem !important;
            font-weight: 500 !important;
        }

        /* Mobile Menu Button */
        .mobile-menu-btn {
            display: none;
        }

        @media (max-width: 1023px) {
            .mobile-menu-btn {
                display: block;
            }
        }

        /* Grid Responsive */
        .responsive-grid {
            display: grid;
            gap: 1rem;
        }

        @media (min-width: 640px) {
            .responsive-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (min-width: 1024px) {
            .responsive-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        /* Form Elements - Mobile Responsive */
        @media (max-width: 640px) {
            input, select, textarea {
                font-size: 16px !important; /* Prevents zoom on iOS */
                padding: 0.5rem !important;
            }

            label {
                font-size: 0.75rem !important;
            }

            .form-group {
                margin-bottom: 1rem;
            }
        }

        /* Cards Grid */
        .cards-grid {
            display: grid;
            gap: 1rem;
        }

        @media (min-width: 640px) {
            .cards-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (min-width: 1024px) {
            .cards-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        /* Header - Mobile Responsive */
        @media (max-width: 640px) {
            header .px-6 {
                padding-left: 1rem !important;
                padding-right: 1rem !important;
            }

            header h1 {
                font-size: 1.125rem !important;
            }

            .user-menu {
                position: static !important;
            }
        }

        /* Footer */
        @media (max-width: 640px) {
            footer {
                padding: 0.75rem 1rem !important;
                font-size: 0.75rem !important;
            }
        }

        /* Touch-friendly improvements */
        @media (hover: none) and (pointer: coarse) {
            .sidebar-link,
            button,
            .btn-primary,
            .btn-secondary,
            a {
                min-height: 44px;
                display: flex;
                align-items: center;
            }

            input, select, textarea {
                min-height: 44px;
            }
        }

        /* Safe area insets for modern mobile devices */
        @supports (padding: max(0px)) {
            .main-content {
                padding-left: env(safe-area-inset-left);
                padding-right: env(safe-area-inset-right);
            }

            .sidebar {
                padding-top: env(safe-area-inset-top);
                padding-bottom: env(safe-area-inset-bottom);
            }
        }
    </style>

    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen bg-gray-100 relative">
        <!-- Overlay for mobile sidebar -->
        <div id="sidebarOverlay" class="overlay" onclick="closeSidebar()"></div>

        <!-- Sidebar -->
        <aside class="sidebar text-white fixed h-full overflow-y-auto shadow-xl" id="sidebar">
            <!-- Logo Area -->
            <div class="p-4 sm:p-6 border-b border-white border-opacity-20">
                <div class="flex items-center space-x-3">
                    <div class="bg-white bg-opacity-20 p-2 rounded-lg flex-shrink-0">
                        <i class="fas fa-hotel text-xl sm:text-2xl"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <h2 class="text-base sm:text-xl font-bold truncate">{{ config('app.name') }}</h2>
                        <p class="text-[10px] sm:text-xs text-white text-opacity-80 truncate">Hostel Manager Portal</p>
                    </div>
                </div>
            </div>

            <!-- Manager Info -->
            <div class="p-4 sm:p-6 border-b border-white border-opacity-20">
                <div class="flex items-center space-x-3">
                    <div class="bg-white bg-opacity-20 p-2 sm:p-3 rounded-full flex-shrink-0">
                        <i class="fas fa-user-circle text-xl sm:text-2xl"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm sm:font-medium truncate">{{ Auth::user()->name }}</p>
                        <p class="text-[10px] sm:text-xs text-white text-opacity-80 truncate">
                            <i class="fas fa-envelope mr-1"></i>{{ Auth::user()->email }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="p-3 sm:p-4">
                <p class="text-[10px] sm:text-xs text-white text-opacity-60 uppercase tracking-wider mb-2 sm:mb-4 px-2 sm:px-4">Main Menu</p>
                <ul class="space-y-0.5 sm:space-y-1">
                    <li>
                        <a href="{{ route('hostel-manager.dashboard') }}"
                           class="sidebar-link flex items-center px-3 sm:px-4 py-2 sm:py-3 text-white text-opacity-90 hover:text-opacity-100 text-sm sm:text-base {{ request()->routeIs('hostel-manager.dashboard') ? 'active' : '' }}">
                            <i class="fas fa-tachometer-alt w-5 sm:w-6 text-sm sm:text-base"></i>
                            <span class="ml-2">Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('hostel-manager.rooms') }}"
                           class="sidebar-link flex items-center px-3 sm:px-4 py-2 sm:py-3 text-white text-opacity-90 hover:text-opacity-100 text-sm sm:text-base {{ request()->routeIs('hostel-manager.rooms') ? 'active' : '' }}">
                            <i class="fas fa-bed w-5 sm:w-6 text-sm sm:text-base"></i>
                            <span class="ml-2">Rooms Management</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('hostel-manager.occupants') }}"
                           class="sidebar-link flex items-center px-3 sm:px-4 py-2 sm:py-3 text-white text-opacity-90 hover:text-opacity-100 text-sm sm:text-base {{ request()->routeIs('hostel-manager.occupants') ? 'active' : '' }}">
                            <i class="fas fa-users w-5 sm:w-6 text-sm sm:text-base"></i>
                            <span class="ml-2">Occupants</span>
                            @if(isset($pendingOccupants) && $pendingOccupants > 0)
                                <span class="ml-auto bg-red-500 text-white text-[10px] sm:text-xs px-1.5 sm:px-2 py-0.5 rounded-full">{{ $pendingOccupants }}</span>
                            @endif
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('hostel-manager.complaints') }}"
                           class="sidebar-link flex items-center px-3 sm:px-4 py-2 sm:py-3 text-white text-opacity-90 hover:text-opacity-100 text-sm sm:text-base {{ request()->routeIs('hostel-manager.complaints') ? 'active' : '' }}">
                            <i class="fas fa-exclamation-triangle w-5 sm:w-6 text-sm sm:text-base"></i>
                            <span class="ml-2">Complaints</span>
                            @if(isset($pendingComplaints) && $pendingComplaints > 0)
                                <span class="ml-auto bg-red-500 text-white text-[10px] sm:text-xs px-1.5 sm:px-2 py-0.5 rounded-full">{{ $pendingComplaints }}</span>
                            @endif
                        </a>
                    </li>
                </ul>

                <p class="text-[10px] sm:text-xs text-white text-opacity-60 uppercase tracking-wider mb-2 sm:mb-4 mt-4 sm:mt-6 px-2 sm:px-4">Management</p>
                <ul class="space-y-0.5 sm:space-y-1">
                    <li>
                        <a href="{{ route('hostel-manager.bookings') }}"
                           class="sidebar-link flex items-center px-3 sm:px-4 py-2 sm:py-3 text-white text-opacity-90 hover:text-opacity-100 text-sm sm:text-base {{ request()->routeIs('hostel-manager.bookings') ? 'active' : '' }}">
                            <i class="fas fa-calendar-check w-5 sm:w-6 text-sm sm:text-base"></i>
                            <span class="ml-2">Bookings</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('hostel-manager.payments') }}"
                           class="sidebar-link flex items-center px-3 sm:px-4 py-2 sm:py-3 text-white text-opacity-90 hover:text-opacity-100 text-sm sm:text-base {{ request()->routeIs('hostel-manager.payments') ? 'active' : '' }}">
                            <i class="fas fa-credit-card w-5 sm:w-6 text-sm sm:text-base"></i>
                            <span class="ml-2">Payments</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('hostel-manager.reports') }}"
                           class="sidebar-link flex items-center px-3 sm:px-4 py-2 sm:py-3 text-white text-opacity-90 hover:text-opacity-100 text-sm sm:text-base {{ request()->routeIs('hostel-manager.reports') ? 'active' : '' }}">
                            <i class="fas fa-chart-bar w-5 sm:w-6 text-sm sm:text-base"></i>
                            <span class="ml-2">Reports</span>
                        </a>
                    </li>
                </ul>

                <p class="text-[10px] sm:text-xs text-white text-opacity-60 uppercase tracking-wider mb-2 sm:mb-4 mt-4 sm:mt-6 px-2 sm:px-4">Settings</p>
                <ul class="space-y-0.5 sm:space-y-1">
                    <li>
                        <a href="{{ route('hostel-manager.profile') }}"
                           class="sidebar-link flex items-center px-3 sm:px-4 py-2 sm:py-3 text-white text-opacity-90 hover:text-opacity-100 text-sm sm:text-base {{ request()->routeIs('hostel-manager.profile') ? 'active' : '' }}">
                            <i class="fas fa-user-cog w-5 sm:w-6 text-sm sm:text-base"></i>
                            <span class="ml-2">Profile</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('hostel-manager.hostels') }}"
                           class="sidebar-link flex items-center px-3 sm:px-4 py-2 sm:py-3 text-white text-opacity-90 hover:text-opacity-100 text-sm sm:text-base {{ request()->routeIs('hostel-manager.hostels') ? 'active' : '' }}">
                            <i class="fas fa-building w-5 sm:w-6 text-sm sm:text-base"></i>
                            <span class="ml-2">My Hostels</span>
                        </a>
                    </li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}" id="logout-form">
                            @csrf
                            <button type="button"
                                    onclick="confirmLogout()"
                                    class="sidebar-link w-full flex items-center px-3 sm:px-4 py-2 sm:py-3 text-white text-opacity-90 hover:text-opacity-100 hover:bg-white hover:bg-opacity-10 rounded-lg text-sm sm:text-base">
                                <i class="fas fa-sign-out-alt w-5 sm:w-6 text-sm sm:text-base"></i>
                                <span class="ml-2">Logout</span>
                            </button>
                        </form>
                    </li>
                </ul>
            </nav>

            <!-- Version Info - Hidden on mobile -->
            <div class="hidden sm:block absolute bottom-0 left-0 right-0 p-4 text-center text-xs text-white text-opacity-60 border-t border-white border-opacity-20">
                <p>Version 1.0.0</p>
                <p>&copy; {{ date('Y') }} {{ config('app.name') }}</p>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="main-content flex-1 min-h-screen flex flex-col">
            <!-- Top Navigation -->
            <header class="bg-white shadow-sm sticky top-0 z-40">
                <div class="px-4 sm:px-6 py-3 sm:py-4 flex items-center justify-between">
                    <div class="flex items-center space-x-2 sm:space-x-4">
                        <button id="sidebarToggle" class="mobile-menu-btn text-gray-500 hover:text-gray-700 p-2 -ml-2">
                            <i class="fas fa-bars text-xl sm:text-2xl"></i>
                        </button>
                        <h1 class="text-base sm:text-xl font-semibold text-gray-800 truncate">@yield('page-title', 'Dashboard')</h1>
                    </div>

                    <div class="flex items-center space-x-2 sm:space-x-4">
                        <!-- Notifications -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="text-gray-500 hover:text-gray-700 relative p-2">
                                <i class="fas fa-bell text-lg sm:text-xl"></i>
                                @if(isset($notificationCount) && $notificationCount > 0)
                                    <span class="absolute top-0 right-0 bg-red-500 text-white text-[10px] sm:text-xs rounded-full h-4 w-4 sm:h-5 sm:w-5 flex items-center justify-center">
                                        {{ $notificationCount }}
                                    </span>
                                @endif
                            </button>

                            <div x-show="open" @click.away="open = false"
                                 class="absolute right-0 mt-2 w-64 sm:w-80 bg-white rounded-lg shadow-xl border z-50">
                                <div class="p-3 sm:p-4 border-b">
                                    <h3 class="font-semibold text-gray-800 text-sm sm:text-base">Notifications</h3>
                                </div>
                                <div class="max-h-96 overflow-y-auto">
                                    @yield('notifications')
                                </div>
                                <div class="p-2 sm:p-3 border-t text-center">
                                    <a href="#" class="text-xs sm:text-sm text-blue-600 hover:text-blue-800">View All</a>
                                </div>
                            </div>
                        </div>

                        <!-- User Menu -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center space-x-1 sm:space-x-2 text-gray-700 hover:text-gray-900 p-2">
                                <div class="w-7 h-7 sm:w-8 sm:h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-xs sm:text-sm">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                                <span class="hidden sm:inline text-sm">{{ Auth::user()->name }}</span>
                                <i class="fas fa-chevron-down text-[10px] sm:text-xs"></i>
                            </button>

                            <div x-show="open" @click.away="open = false"
                                 class="absolute right-0 mt-2 w-40 sm:w-48 bg-white rounded-lg shadow-xl border z-50">
                                <a href="{{ route('hostel-manager.profile') }}"
                                   class="block px-3 sm:px-4 py-2 text-xs sm:text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-user mr-2"></i> Profile
                                </a>
                                <a href="{{ route('hostel-manager.settings') }}"
                                   class="block px-3 sm:px-4 py-2 text-xs sm:text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-cog mr-2"></i> Settings
                                </a>
                                <hr class="my-1">
                                <form method="POST" action="{{ route('logout') }}" id="dropdown-logout-form">
                                    @csrf
                                    <button type="button"
                                            onclick="confirmLogoutFromDropdown()"
                                            class="w-full text-left px-3 sm:px-4 py-2 text-xs sm:text-sm text-red-600 hover:bg-red-50">
                                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 p-4 sm:p-6">
                <!-- Alert Messages - Converted to SweetAlert -->
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
                                customClass: {
                                    popup: 'rounded-lg shadow-xl'
                                }
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
                                customClass: {
                                    popup: 'rounded-lg shadow-xl'
                                }
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
                                customClass: {
                                    popup: 'rounded-lg shadow-xl'
                                }
                            });
                        });
                    </script>
                @endif

                @if(session('info'))
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                icon: 'info',
                                title: 'Info!',
                                text: '{{ session('info') }}',
                                timer: 3000,
                                showConfirmButton: false,
                                position: 'top-end',
                                toast: true,
                                background: '#3b82f6',
                                color: '#ffffff',
                                iconColor: '#ffffff',
                                customClass: {
                                    popup: 'rounded-lg shadow-xl'
                                }
                            });
                        });
                    </script>
                @endif

                <!-- Validation Errors -->
                @if($errors->any())
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            let errorMessage = '';
                            @foreach($errors->all() as $error)
                                errorMessage += '• {{ $error }}\n';
                            @endforeach

                            Swal.fire({
                                icon: 'error',
                                title: 'Validation Errors',
                                html: `<pre style="text-align: left; white-space: pre-wrap; font-size: 0.875rem;">${errorMessage}</pre>`,
                                confirmButtonColor: '#ef4444',
                                confirmButtonText: 'Got it',
                                customClass: {
                                    popup: 'rounded-xl',
                                    confirmButton: 'bg-red-500 hover:bg-red-600 text-white font-medium py-2 px-4 rounded-lg text-sm'
                                }
                            });
                        });
                    </script>
                @endif

                <!-- Main Content Yield -->
                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t py-3 sm:py-4 px-4 sm:px-6 text-center text-gray-500 text-xs sm:text-sm">
                <p>&copy; {{ date('Y') }} {{ config('app.name') }} - Hostel Manager Portal. All rights reserved.</p>
            </footer>
        </div>
    </div>

    <!-- Loading Spinner (Hidden by default) -->
    <div id="loadingSpinner" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-[9999] flex items-center justify-center">
        <div class="spinner"></div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Sidebar functionality for mobile
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const sidebarToggle = document.getElementById('sidebarToggle');

        function openSidebar() {
            sidebar.classList.add('mobile-open');
            overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeSidebar() {
            sidebar.classList.remove('mobile-open');
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        }

        // Toggle sidebar on mobile
        sidebarToggle?.addEventListener('click', function(e) {
            e.stopPropagation();
            if (sidebar.classList.contains('mobile-open')) {
                closeSidebar();
            } else {
                openSidebar();
            }
        });

        // Close sidebar on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && sidebar.classList.contains('mobile-open')) {
                closeSidebar();
            }
        });

        // Handle window resize
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                if (window.innerWidth >= 1024) {
                    sidebar.classList.remove('mobile-open');
                    overlay.classList.remove('active');
                    document.body.style.overflow = '';
                }
            }, 250);
        });

        // Loading Spinner
        function showLoading() {
            document.getElementById('loadingSpinner').classList.remove('hidden');
        }

        function hideLoading() {
            document.getElementById('loadingSpinner').classList.add('hidden');
        }

        // SweetAlert Confirmation for Logout
        function confirmLogout() {
            Swal.fire({
                title: 'Are you sure?',
                text: "You will be logged out of the system",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, logout',
                cancelButtonText: 'Cancel',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-xl p-3 sm:p-4',
                    title: 'text-base sm:text-lg font-semibold',
                    htmlContainer: 'text-xs sm:text-sm',
                    confirmButton: 'bg-red-500 hover:bg-red-600 text-white font-medium py-2 px-3 sm:px-4 rounded-lg text-xs sm:text-sm',
                    cancelButton: 'bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-3 sm:px-4 rounded-lg text-xs sm:text-sm'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logout-form').submit();
                }
            });
        }

        function confirmLogoutFromDropdown() {
            Swal.fire({
                title: 'Are you sure?',
                text: "You will be logged out of the system",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, logout',
                cancelButtonText: 'Cancel',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-xl p-3 sm:p-4',
                    title: 'text-base sm:text-lg font-semibold',
                    htmlContainer: 'text-xs sm:text-sm',
                    confirmButton: 'bg-red-500 hover:bg-red-600 text-white font-medium py-2 px-3 sm:px-4 rounded-lg text-xs sm:text-sm',
                    cancelButton: 'bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-3 sm:px-4 rounded-lg text-xs sm:text-sm'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('dropdown-logout-form').submit();
                }
            });
        }

        // Generic Confirm Delete Function with SweetAlert
        function confirmDelete(itemName = 'this item', deleteUrl = null) {
            Swal.fire({
                title: 'Are you sure?',
                text: `You are about to delete ${itemName}. This action cannot be undone!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, delete it',
                cancelButtonText: 'Cancel',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-xl p-3 sm:p-4',
                    title: 'text-base sm:text-lg font-semibold',
                    htmlContainer: 'text-xs sm:text-sm',
                    confirmButton: 'bg-red-500 hover:bg-red-600 text-white font-medium py-2 px-3 sm:px-4 rounded-lg text-xs sm:text-sm',
                    cancelButton: 'bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-3 sm:px-4 rounded-lg text-xs sm:text-sm'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    if (deleteUrl) {
                        window.location.href = deleteUrl;
                    } else {
                        // If no URL provided, return true for form submission
                        return true;
                    }
                }
                return false;
            });
        }

        // Success Message Function
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
                customClass: {
                    popup: 'rounded-lg shadow-xl text-xs sm:text-sm'
                }
            });
        }

        // Error Message Function
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
                customClass: {
                    popup: 'rounded-lg shadow-xl text-xs sm:text-sm'
                }
            });
        }

        // Warning Message Function
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
                customClass: {
                    popup: 'rounded-lg shadow-xl text-xs sm:text-sm'
                }
            });
        }

        // Info Message Function
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
                customClass: {
                    popup: 'rounded-lg shadow-xl text-xs sm:text-sm'
                }
            });
        }

        // Custom Confirmation Dialog
        function showConfirmation(title, text, confirmCallback) {
            Swal.fire({
                title: title,
                text: text,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes',
                cancelButtonText: 'No',
                customClass: {
                    popup: 'rounded-xl p-3 sm:p-4',
                    title: 'text-base sm:text-lg font-semibold',
                    htmlContainer: 'text-xs sm:text-sm',
                    confirmButton: 'bg-green-500 hover:bg-green-600 text-white font-medium py-2 px-3 sm:px-4 rounded-lg text-xs sm:text-sm',
                    cancelButton: 'bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-3 sm:px-4 rounded-lg text-xs sm:text-sm'
                }
            }).then((result) => {
                if (result.isConfirmed && confirmCallback) {
                    confirmCallback();
                }
            });
        }

        // Auto-hide notifications
        document.querySelectorAll('.notification').forEach(notification => {
            setTimeout(() => {
                notification.style.animation = 'slideInRight 0.3s reverse';
                setTimeout(() => notification.remove(), 300);
            }, 5000);
        });

        // Format Currency
        function formatCurrency(amount) {
            return new Intl.NumberFormat('en-GH', {
                style: 'currency',
                currency: 'GHS'
            }).format(amount);
        }

        // Format Date
        function formatDate(date) {
            return new Date(date).toLocaleDateString('en-GH', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        }

        // Override the old confirmDelete function
        window.confirmDelete = confirmDelete;
    </script>

    @stack('scripts')
</body>
</html>
