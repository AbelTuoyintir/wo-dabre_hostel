<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
            transition: all 0.3s;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .sidebar-link {
            transition: all 0.3s;
            border-radius: 0.5rem;
            margin: 0.25rem 0;
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
            transition: margin-left 0.3s;
        }

        /* Stats Cards */
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

        /* Table Styles */
        .table-container {
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
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
        }
        .btn-secondary:hover {
            background-color: #d1d5db;
        }

        /* Modal Styles */
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
        }
        .modal-content {
            animation: slideIn 0.3s;
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
        .swal2-title {
            font-size: 1.5rem;
            font-weight: 600;
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
    </style>

    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="flex h-screen bg-gray-100">
        <!-- Sidebar -->
        <aside class="sidebar w-64 text-white fixed h-full overflow-y-auto shadow-xl" id="sidebar">
            <!-- Logo Area -->
            <div class="p-6 border-b border-white border-opacity-20">
                <div class="flex items-center space-x-3">
                    <div class="bg-white bg-opacity-20 p-2 rounded-lg">
                        <i class="fas fa-hotel text-2xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold">{{ config('app.name') }}</h2>
                        <p class="text-xs text-white text-opacity-80">Hostel Manager Portal</p>
                    </div>
                </div>
            </div>

            <!-- Manager Info -->
            <div class="p-6 border-b border-white border-opacity-20">
                <div class="flex items-center space-x-3">
                    <div class="bg-white bg-opacity-20 p-3 rounded-full">
                        <i class="fas fa-user-circle text-2xl"></i>
                    </div>
                    <div>
                        <p class="font-medium">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-white text-opacity-80">
                            <i class="fas fa-envelope mr-1"></i>{{ Auth::user()->email }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="p-4">
                <p class="text-xs text-white text-opacity-60 uppercase tracking-wider mb-4 px-4">Main Menu</p>
                <ul class="space-y-1">
                    <li>
                        <a href="{{ route('hostel-manager.dashboard') }}"
                           class="sidebar-link flex items-center px-4 py-3 text-white text-opacity-90 hover:text-opacity-100 {{ request()->routeIs('hostel-manager.dashboard') ? 'active' : '' }}">
                            <i class="fas fa-tachometer-alt w-6"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('hostel-manager.rooms') }}"
                           class="sidebar-link flex items-center px-4 py-3 text-white text-opacity-90 hover:text-opacity-100 {{ request()->routeIs('hostel-manager.rooms') ? 'active' : '' }}">
                            <i class="fas fa-bed w-6"></i>
                            <span>Rooms Management</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('hostel-manager.occupants') }}"
                           class="sidebar-link flex items-center px-4 py-3 text-white text-opacity-90 hover:text-opacity-100 {{ request()->routeIs('hostel-manager.occupants') ? 'active' : '' }}">
                            <i class="fas fa-users w-6"></i>
                            <span>Occupants</span>
                            @if(isset($pendingOccupants) && $pendingOccupants > 0)
                                <span class="ml-auto bg-red-500 text-white text-xs px-2 py-1 rounded-full">{{ $pendingOccupants }}</span>
                            @endif
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('hostel-manager.complaints') }}"
                           class="sidebar-link flex items-center px-4 py-3 text-white text-opacity-90 hover:text-opacity-100 {{ request()->routeIs('hostel-manager.complaints') ? 'active' : '' }}">
                            <i class="fas fa-exclamation-triangle w-6"></i>
                            <span>Complaints</span>
                            @if(isset($pendingComplaints) && $pendingComplaints > 0)
                                <span class="ml-auto bg-red-500 text-white text-xs px-2 py-1 rounded-full">{{ $pendingComplaints }}</span>
                            @endif
                        </a>
                    </li>
                </ul>

                <p class="text-xs text-white text-opacity-60 uppercase tracking-wider mb-4 mt-6 px-4">Management</p>
                <ul class="space-y-1">
                    <li>
                        <a href="{{ route('hostel-manager.bookings') }}"
                           class="sidebar-link flex items-center px-4 py-3 text-white text-opacity-90 hover:text-opacity-100 {{ request()->routeIs('hostel-manager.bookings') ? 'active' : '' }}">
                            <i class="fas fa-calendar-check w-6"></i>
                            <span>Bookings</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('hostel-manager.payments') }}"
                           class="sidebar-link flex items-center px-4 py-3 text-white text-opacity-90 hover:text-opacity-100 {{ request()->routeIs('hostel-manager.payments') ? 'active' : '' }}">
                            <i class="fas fa-credit-card w-6"></i>
                            <span>Payments</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('hostel-manager.reports') }}"
                           class="sidebar-link flex items-center px-4 py-3 text-white text-opacity-90 hover:text-opacity-100 {{ request()->routeIs('hostel-manager.reports') ? 'active' : '' }}">
                            <i class="fas fa-chart-bar w-6"></i>
                            <span>Reports</span>
                        </a>
                    </li>
                </ul>

                <p class="text-xs text-white text-opacity-60 uppercase tracking-wider mb-4 mt-6 px-4">Settings</p>
                <ul class="space-y-1">
                    <li>
                        <a href="{{ route('hostel-manager.profile') }}"
                           class="sidebar-link flex items-center px-4 py-3 text-white text-opacity-90 hover:text-opacity-100 {{ request()->routeIs('hostel-manager.profile') ? 'active' : '' }}">
                            <i class="fas fa-user-cog w-6"></i>
                            <span>Profile</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('hostel-manager.hostels') }}"
                           class="sidebar-link flex items-center px-4 py-3 text-white text-opacity-90 hover:text-opacity-100 {{ request()->routeIs('hostel-manager.hostels') ? 'active' : '' }}">
                            <i class="fas fa-building w-6"></i>
                            <span>My Hostels</span>
                        </a>
                    </li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}" id="logout-form">
                            @csrf
                            <button type="button"
                                    onclick="confirmLogout()"
                                    class="sidebar-link w-full flex items-center px-4 py-3 text-white text-opacity-90 hover:text-opacity-100 hover:bg-white hover:bg-opacity-10 rounded-lg">
                                <i class="fas fa-sign-out-alt w-6"></i>
                                <span>Logout</span>
                            </button>
                        </form>
                    </li>
                </ul>
            </nav>

            <!-- Version Info -->
            <div class="absolute bottom-0 left-0 right-0 p-4 text-center text-xs text-white text-opacity-60 border-t border-white border-opacity-20">
                <p>Version 1.0.0</p>
                <p>&copy; {{ date('Y') }} {{ config('app.name') }}</p>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="main-content flex-1 ml-64 overflow-y-auto">
            <!-- Top Navigation -->
            <header class="bg-white shadow-sm sticky top-0 z-50">
                <div class="px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <button id="sidebarToggle" class="text-gray-500 hover:text-gray-700 lg:hidden">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                        <h1 class="text-xl font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h1>
                    </div>

                    <div class="flex items-center space-x-4">
                        <!-- Notifications -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="text-gray-500 hover:text-gray-700 relative">
                                <i class="fas fa-bell text-xl"></i>
                                @if(isset($notificationCount) && $notificationCount > 0)
                                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                        {{ $notificationCount }}
                                    </span>
                                @endif
                            </button>

                            <div x-show="open" @click.away="open = false"
                                 class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl border z-50">
                                <div class="p-4 border-b">
                                    <h3 class="font-semibold text-gray-800">Notifications</h3>
                                </div>
                                <div class="max-h-96 overflow-y-auto">
                                    @yield('notifications')
                                </div>
                                <div class="p-3 border-t text-center">
                                    <a href="#" class="text-sm text-blue-600 hover:text-blue-800">View All</a>
                                </div>
                            </div>
                        </div>

                        <!-- User Menu -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center space-x-2 text-gray-700 hover:text-gray-900">
                                <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                                <span class="hidden md:inline">{{ Auth::user()->name }}</span>
                                <i class="fas fa-chevron-down text-xs"></i>
                            </button>

                            <div x-show="open" @click.away="open = false"
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border z-50">
                                <a href="{{ route('hostel-manager.profile') }}"
                                   class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-user mr-2"></i> Profile
                                </a>
                                <a href="{{ route('hostel-manager.settings') }}"
                                   class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-cog mr-2"></i> Settings
                                </a>
                                <hr class="my-1">
                                <form method="POST" action="{{ route('logout') }}" id="dropdown-logout-form">
                                    @csrf
                                    <button type="button"
                                            onclick="confirmLogoutFromDropdown()"
                                            class="w-full text-left px-4 py-2 text-red-600 hover:bg-red-50">
                                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="p-6">
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
                                html: `<pre style="text-align: left; white-space: pre-wrap;">${errorMessage}</pre>`,
                                confirmButtonColor: '#ef4444',
                                confirmButtonText: 'Got it',
                                customClass: {
                                    popup: 'rounded-xl',
                                    confirmButton: 'bg-red-500 hover:bg-red-600 text-white font-medium py-2 px-4 rounded-lg'
                                }
                            });
                        });
                    </script>
                @endif

                <!-- Main Content Yield -->
                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t py-4 px-6 text-center text-gray-500 text-sm">
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
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Sidebar Toggle for Mobile
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('-translate-x-full');
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
                    popup: 'rounded-xl',
                    confirmButton: 'bg-red-500 hover:bg-red-600 text-white font-medium py-2 px-4 rounded-lg',
                    cancelButton: 'bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg'
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
                    popup: 'rounded-xl',
                    confirmButton: 'bg-red-500 hover:bg-red-600 text-white font-medium py-2 px-4 rounded-lg',
                    cancelButton: 'bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg'
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
                    popup: 'rounded-xl',
                    confirmButton: 'bg-red-500 hover:bg-red-600 text-white font-medium py-2 px-4 rounded-lg',
                    cancelButton: 'bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg'
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
                    popup: 'rounded-lg shadow-xl'
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
                    popup: 'rounded-lg shadow-xl'
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
                    popup: 'rounded-lg shadow-xl'
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
                    popup: 'rounded-lg shadow-xl'
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
                    popup: 'rounded-xl',
                    confirmButton: 'bg-green-500 hover:bg-green-600 text-white font-medium py-2 px-4 rounded-lg',
                    cancelButton: 'bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg'
                }
            }).then((result) => {
                if (result.isConfirmed && confirmCallback) {
                    confirmCallback();
                }
            });
        }

        // Auto-hide notifications (kept for backward compatibility)
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
