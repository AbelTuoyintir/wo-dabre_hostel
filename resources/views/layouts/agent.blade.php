{{-- resources/views/layouts/agent.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Agent Dashboard - Wo-dabre')</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #a855f7;
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #7e22ce;
        }
        
        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }
        
        /* Sidebar transition */
        .sidebar-transition {
            transition: transform 0.3s ease-in-out;
        }
        
        /* Mobile menu */
        @media (max-width: 768px) {
            .mobile-sidebar {
                position: fixed;
                left: -280px;
                top: 0;
                bottom: 0;
                z-index: 1000;
                transition: left 0.3s ease;
            }
            
            .mobile-sidebar.open {
                left: 0;
            }
            
            .overlay {
                position: fixed;
                inset: 0;
                background: rgba(0,0,0,0.5);
                z-index: 999;
                display: none;
            }
            
            .overlay.open {
                display: block;
            }
        }
        
        /* Dropdown animations */
        .dropdown-enter {
            opacity: 0;
            transform: translateY(-10px);
        }
        
        .dropdown-enter-active {
            opacity: 1;
            transform: translateY(0);
            transition: all 0.2s ease;
        }
        
        /* SweetAlert custom styling */
        .swal2-popup {
            font-family: 'Inter', sans-serif !important;
            border-radius: 1rem !important;
        }
        
        .swal2-title {
            font-size: 1.5rem !important;
            font-weight: 700 !important;
        }
        
        .swal2-confirm {
            background: linear-gradient(135deg, #8b5cf6, #ec4899) !important;
            border-radius: 0.75rem !important;
            padding: 0.75rem 1.5rem !important;
            font-weight: 600 !important;
        }
        
        .swal2-cancel {
            border-radius: 0.75rem !important;
            padding: 0.75rem 1.5rem !important;
            font-weight: 600 !important;
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-gray-50">
    <div id="app">
        <!-- Mobile Sidebar Overlay -->
        <div id="sidebarOverlay" class="overlay" onclick="closeSidebar()"></div>
        
        <div class="flex h-screen">
            <!-- Sidebar -->
            <aside id="sidebar" class="mobile-sidebar w-72 bg-gradient-to-br from-purple-900 via-indigo-900 to-pink-900 text-white flex flex-col shadow-2xl sidebar-transition">
                <!-- Sidebar Header -->
                <div class="p-6 border-b border-white/20">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-pink-500 rounded-2xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-building text-2xl"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold">Wo-dabre</h2>
                            <p class="text-xs text-purple-200">Hostel Agent Portal</p>
                        </div>
                    </div>
                </div>
                
                <!-- Agent Info -->
                <div class="p-6 border-b border-white/20">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                            <i class="fas fa-user-tie text-xl"></i>
                        </div>
                        <div>
                            <p class="font-semibold">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-purple-200">
                                @if(Auth::user()->agent && Auth::user()->agent->status === 'active')
                                    <span class="inline-flex items-center gap-1">
                                        <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                                        Active
                                    </span>
                                @elseif(Auth::user()->agent && Auth::user()->agent->status === 'pending')
                                    <span class="inline-flex items-center gap-1">
                                        <span class="w-2 h-2 bg-yellow-400 rounded-full"></span>
                                        Pending
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1">
                                        <span class="w-2 h-2 bg-gray-400 rounded-full"></span>
                                        Inactive
                                    </span>
                                @endif
                            </p>
                        </div>
                    </div>
                    @if(Auth::user()->agent && Auth::user()->agent->agent_code)
                        <div class="mt-3 bg-white/10 rounded-lg p-2 text-center">
                            <p class="text-xs text-purple-200">Agent Code</p>
                            <p class="font-mono text-sm font-bold">{{ Auth::user()->agent->agent_code }}</p>
                        </div>
                    @endif
                </div>
                
                <!-- Navigation -->
                <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
                    <a href="{{ route('agent.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all hover:bg-white/10 {{ request()->routeIs('agent.dashboard') ? 'bg-white/20' : '' }}">
                        <i class="fas fa-chart-line w-5"></i>
                        <span>Dashboard</span>
                    </a>
                    
                    <a href="{{ route('agent.hostels.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all hover:bg-white/10 {{ request()->routeIs('agent.hostels.*') ? 'bg-white/20' : '' }}">
                        <i class="fas fa-building w-5"></i>
                        <span>My Hostels</span>
                    </a>
                    
                    <a href="{{ route('agent.hostels.create') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all hover:bg-white/10">
                        <i class="fas fa-plus-circle w-5"></i>
                        <span>Add Hostel</span>
                    </a>
                    
                    <hr class="border-white/20 my-2">
                    
                    <a href="{{ route('agent.commissions') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all hover:bg-white/10 {{ request()->routeIs('agent.commissions') ? 'bg-white/20' : '' }}">
                        <i class="fas fa-chart-bar w-5"></i>
                        <span>Commissions</span>
                    </a>
                    
                    <a href="{{ route('agent.withdrawals') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all hover:bg-white/10 {{ request()->routeIs('agent.withdrawals*') ? 'bg-white/20' : '' }}">
                        <i class="fas fa-wallet w-5"></i>
                        <span>Withdrawals</span>
                    </a>
                    
                    <hr class="border-white/20 my-2">
                    
                    <a href="{{ route('agent.profile') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all hover:bg-white/10 {{ request()->routeIs('agent.profile') ? 'bg-white/20' : '' }}">
                        <i class="fas fa-user-circle w-5"></i>
                        <span>Profile</span>
                    </a>
                    
                    <a href="{{ route('agent.settings') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all hover:bg-white/10 {{ request()->routeIs('agent.settings') ? 'bg-white/20' : '' }}">
                        <i class="fas fa-cog w-5"></i>
                        <span>Settings</span>
                    </a>
                </nav>
                
                <!-- Sidebar Footer -->
                <div class="p-4 border-t border-white/20">
                    <button onclick="confirmLogout()" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all hover:bg-red-600/20 text-red-200">
                        <i class="fas fa-sign-out-alt w-5"></i>
                        <span>Logout</span>
                    </button>
                    <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display: none;">
                        @csrf
                    </form>
                </div>
            </aside>
            
            <!-- Main Content -->
            <div class="flex-1 flex flex-col overflow-hidden">
                <!-- Top Navigation Bar -->
                <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-30">
                    <div class="px-4 sm:px-6 py-3">
                        <div class="flex items-center justify-between">
                            <!-- Mobile Menu Button -->
                            <button onclick="toggleSidebar()" class="lg:hidden p-2 rounded-lg hover:bg-gray-100 transition">
                                <i class="fas fa-bars text-xl text-gray-600"></i>
                            </button>
                            
                            <!-- Page Title -->
                            <div class="flex-1">
                                <h1 class="text-xl font-bold text-gray-800">
                                    @yield('page-title', 'Dashboard')
                                </h1>
                            </div>
                            
                            <!-- Right Side Nav -->
                            <div class="flex items-center gap-3">
                                <!-- Notifications -->
                                <div class="relative">
                                    <button onclick="toggleNotifications()" class="p-2 rounded-lg hover:bg-gray-100 transition relative">
                                        <i class="fas fa-bell text-gray-600 text-lg"></i>
                                        <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                                    </button>
                                    
                                    <!-- Notifications Dropdown -->
                                    <div id="notificationsDropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-2xl border border-gray-200 z-50">
                                        <div class="p-3 border-b border-gray-200">
                                            <h3 class="font-semibold text-gray-800">Notifications</h3>
                                        </div>
                                        <div class="max-h-96 overflow-y-auto">
                                            <div class="p-4 text-center text-gray-500 text-sm">
                                                No new notifications
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- User Dropdown -->
                                <div class="relative">
                                    <button onclick="toggleUserMenu()" class="flex items-center gap-2 p-2 rounded-lg hover:bg-gray-100 transition">
                                        <div class="w-8 h-8 bg-gradient-to-r from-purple-600 to-pink-600 rounded-full flex items-center justify-center text-white">
                                            <i class="fas fa-user-tie text-sm"></i>
                                        </div>
                                        <span class="hidden md:inline text-sm font-medium text-gray-700">{{ Auth::user()->name }}</span>
                                        <i class="fas fa-chevron-down text-xs text-gray-500"></i>
                                    </button>
                                    
                                    <!-- User Dropdown Menu -->
                                    <div id="userMenu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-2xl border border-gray-200 z-50">
                                        <a href="{{ route('agent.profile') }}" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition">
                                            <i class="fas fa-user-circle text-gray-500"></i>
                                            <span class="text-sm">My Profile</span>
                                        </a>
                                        <a href="{{ route('agent.settings') }}" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition">
                                            <i class="fas fa-cog text-gray-500"></i>
                                            <span class="text-sm">Settings</span>
                                        </a>
                                        <hr class="my-1">
                                        <button onclick="confirmLogout()" class="w-full flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition text-left">
                                            <i class="fas fa-sign-out-alt text-gray-500"></i>
                                            <span class="text-sm">Logout</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>
                
                <!-- Main Content Area -->
                <main class="flex-1 overflow-y-auto bg-gray-50 p-4 sm:p-6 fade-in">
                    @yield('content')
                </main>
            </div>
        </div>
    </div>
    
    <script>
        // SweetAlert2 Toast configuration
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });
        
        // Show flash messages with SweetAlert
        @if(session('success'))
            Toast.fire({
                icon: 'success',
                title: '{{ session('success') }}'
            });
        @endif
        
        @if(session('error'))
            Toast.fire({
                icon: 'error',
                title: '{{ session('error') }}'
            });
        @endif
        
        @if(session('warning'))
            Toast.fire({
                icon: 'warning',
                title: '{{ session('warning') }}'
            });
        @endif
        
        @if(session('info'))
            Toast.fire({
                icon: 'info',
                title: '{{ session('info') }}'
            });
        @endif
        
        // Confirm Logout
        function confirmLogout() {
            Swal.fire({
                title: 'Are you sure?',
                text: "You will be logged out of your account!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#8b5cf6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, logout!',
                cancelButtonText: 'Cancel',
                background: '#fff',
                backdrop: true,
                customClass: {
                    popup: 'rounded-2xl',
                    title: 'font-bold text-xl',
                    confirmButton: 'bg-gradient-to-r from-purple-600 to-pink-600 text-white font-semibold px-6 py-2 rounded-lg',
                    cancelButton: 'bg-gray-200 text-gray-700 font-semibold px-6 py-2 rounded-lg'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logout-form').submit();
                }
            });
        }
        
        // Confirm Delete Function (for hostels, rooms, etc.)
        function confirmDelete(url, itemName) {
            Swal.fire({
                title: 'Delete ' + itemName + '?',
                text: "This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                customClass: {
                    confirmButton: 'bg-red-600 text-white font-semibold px-6 py-2 rounded-lg',
                    cancelButton: 'bg-gray-200 text-gray-700 font-semibold px-6 py-2 rounded-lg'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Create form and submit
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = url;
                    form.innerHTML = '@csrf @method('DELETE')';
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
        
        // Success Alert
        function showSuccess(message) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: message,
                confirmButtonColor: '#8b5cf6',
                timer: 3000,
                timerProgressBar: true,
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'bg-gradient-to-r from-purple-600 to-pink-600 text-white font-semibold px-6 py-2 rounded-lg'
                }
            });
        }
        
        // Error Alert
        function showError(message) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: message,
                confirmButtonColor: '#8b5cf6',
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'bg-gradient-to-r from-purple-600 to-pink-600 text-white font-semibold px-6 py-2 rounded-lg'
                }
            });
        }
        
        // Warning Alert
        function showWarning(message) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning',
                text: message,
                confirmButtonColor: '#f59e0b',
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'bg-amber-500 text-white font-semibold px-6 py-2 rounded-lg'
                }
            });
        }
        
        // Info Alert
        function showInfo(message) {
            Swal.fire({
                icon: 'info',
                title: 'Information',
                text: message,
                confirmButtonColor: '#3b82f6',
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'bg-blue-500 text-white font-semibold px-6 py-2 rounded-lg'
                }
            });
        }
        
        // Confirm Action with custom options
        function confirmAction(title, text, confirmButtonText, url, method = 'POST') {
            Swal.fire({
                title: title,
                text: text,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#8b5cf6',
                cancelButtonColor: '#6b7280',
                confirmButtonText: confirmButtonText,
                cancelButtonText: 'Cancel',
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'bg-gradient-to-r from-purple-600 to-pink-600 text-white font-semibold px-6 py-2 rounded-lg',
                    cancelButton: 'bg-gray-200 text-gray-700 font-semibold px-6 py-2 rounded-lg'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = url;
                    form.innerHTML = '@csrf';
                    if (method !== 'POST') {
                        form.innerHTML += `<input type="hidden" name="_method" value="${method}">`;
                    }
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
        
        // Withdrawal Request Success
        function showWithdrawalSuccess(amount) {
            Swal.fire({
                icon: 'success',
                title: 'Withdrawal Request Submitted!',
                html: `Your request for <strong>₵${amount}</strong> has been submitted.<br>We'll process it within 2-3 business days.`,
                confirmButtonColor: '#8b5cf6',
                confirmButtonText: 'Great!',
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'bg-gradient-to-r from-purple-600 to-pink-600 text-white font-semibold px-6 py-2 rounded-lg'
                }
            });
        }
        
        // Loading Alert
        function showLoading(message = 'Processing...') {
            Swal.fire({
                title: message,
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                },
                customClass: {
                    popup: 'rounded-2xl'
                }
            });
        }
        
        // Close loading
        function closeLoading() {
            Swal.close();
        }
        
        // Sidebar Toggle for Mobile
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.toggle('open');
            overlay.classList.toggle('open');
        }
        
        function closeSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.remove('open');
            overlay.classList.remove('open');
        }
        
        // User Menu Toggle
        function toggleUserMenu() {
            const menu = document.getElementById('userMenu');
            menu.classList.toggle('hidden');
        }
        
        // Notifications Toggle
        function toggleNotifications() {
            const dropdown = document.getElementById('notificationsDropdown');
            dropdown.classList.toggle('hidden');
        }
        
        // Close dropdowns when clicking outside
        document.addEventListener('click', function(event) {
            const userMenu = document.getElementById('userMenu');
            const userButton = event.target.closest('[onclick="toggleUserMenu()"]');
            if (!userButton && !userMenu?.contains(event.target)) {
                userMenu?.classList.add('hidden');
            }
            
            const notifications = document.getElementById('notificationsDropdown');
            const notifButton = event.target.closest('[onclick="toggleNotifications()"]');
            if (!notifButton && !notifications?.contains(event.target)) {
                notifications?.classList.add('hidden');
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>