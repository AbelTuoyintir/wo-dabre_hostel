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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- Vite Assets (your custom CSS/JS) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }
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
    </style>

    @stack('styles')
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
                    @guest
                        <a href="{{ route('register') }}"
                           class="hidden md:flex items-center space-x-2 bg-blue-800 text-white px-4 py-2 rounded-lg hover:bg-blue-900 transition">
                            <i class="fas fa-user-graduate"></i>
                            <span>Create Account</span>
                        </a>
                        <a href="{{ route('login') }}"
                           class="bg-white text-blue-900 px-4 py-2 rounded-lg font-medium hover:bg-blue-100 transition">
                            <i class="fas fa-sign-in-alt mr-2"></i> Student Login
                        </a>
                    @else
                        @if(auth()->user()->role === 'student')
                            <a href="{{ route('student.dashboard') }}"
                               class="bg-green-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-green-700 transition">
                                <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                            </a>
                        @elseif(auth()->user()->role === 'admin')
                            <a href="{{ route('admin.dashboard') }}"
                               class="bg-purple-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-purple-700 transition">
                                <i class="fas fa-cog mr-2"></i> Admin
                            </a>
                        @elseif(auth()->user()->role === 'manager')
                            <a href="{{ route('hostel-manager.dashboard') }}"
                               class="bg-orange-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-orange-700 transition">
                                <i class="fas fa-building mr-2"></i> Manager
                            </a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-red-700 transition">
                                <i class="fas fa-sign-out-alt mr-2"></i> Logout
                            </button>
                        </form>
                    @endguest
                </div>
            </div>
        </div>
    </header>

    <!-- Loading Spinner (hidden by default) -->
    <div id="loadingSpinner" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-[9999] flex items-center justify-center">
        <div class="loader"></div>
    </div>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
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
                    <ul class="space-y-2 text-gray-400" id="locationList">
                        <!-- Will be populated dynamically if needed -->
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

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Alpine.js (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <script>
        // Loading spinner functions
        function showLoading() {
            document.getElementById('loadingSpinner').classList.remove('hidden');
        }

        function hideLoading() {
            document.getElementById('loadingSpinner').classList.add('hidden');
        }

        // Helper functions for SweetAlert toasts (can be used in custom JS)
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