<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <!--add logo to the site-->
        <link rel="icon" href="{{ asset('/images/wodabre-logo.png') }}" type="image/x-icon">
        <link rel="shortcut icon" href="{{ asset('/images/wodabre-logo.png') }}" type="image/x-icon">

        <script src="//unpkg.com/alpinejs" defer></script>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- SweetAlert2 CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
    </head>
    <body class="font-sans antialiased" x-data="{ sidebarOpen: false }">
        <div class="min-h-screen lg:h-screen flex bg-gray-100 overflow-hidden">
            @include('layouts.navigation')

            <div x-cloak
                 x-show="sidebarOpen"
                 x-transition.opacity
                 @click="sidebarOpen = false"
                 class="fixed inset-0 z-40 bg-slate-900/40 lg:hidden"></div>

            <div class="flex min-w-0 flex-1 flex-col">
                @php
                    $pageTitle = trim($__env->yieldContent('page-title')) ?: config('app.name', 'Laravel');
                @endphp

                <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/95 backdrop-blur lg:hidden">
                    <div class="flex items-center justify-between px-4 py-3">
                        <button @click="sidebarOpen = true"
                                type="button"
                                class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white p-2 text-slate-700 shadow-sm transition hover:bg-slate-50"
                                aria-label="Open menu">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>
                        <h1 class="max-w-[70%] truncate text-sm font-semibold text-slate-800">{{ $pageTitle }}</h1>
                        <div class="w-9"></div>
                    </div>
                </header>

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
                <main class="min-w-0 flex-1 overflow-y-auto p-3 sm:p-6 lg:p-8">
                    @yield('content')
                </main>
            </div>
        </div>

        <!-- SweetAlert2 JS -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

        <!-- Display Success Messages -->
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
                    timerProgressBar: true,
                    background: '#10b981',
                    color: '#ffffff'
                });
            });
        </script>
        @endif

        <!-- Display Error Messages -->
        @if(session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: '{{ session('error') }}',
                    timer: 5000,
                    showConfirmButton: true,
                    confirmButtonColor: '#ef4444',
                    position: 'center'
                });
            });
        </script>
        @endif

        <!-- Display Validation Errors -->
        @if($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                let errorMessages = '';
                @foreach($errors->all() as $error)
                    errorMessages += '• {{ $error }}\n';
                @endforeach

                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: errorMessages,
                    showConfirmButton: true,
                    confirmButtonColor: '#ef4444',
                    position: 'center',
                    html: '<div style="text-align: left;">' + errorMessages.replace(/\n/g, '<br>') + '</div>'
                });
            });
        </script>
        @endif

        <!-- Display Info Messages -->
        @if(session('info'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'info',
                    title: 'Information',
                    text: '{{ session('info') }}',
                    timer: 3000,
                    showConfirmButton: false,
                    position: 'top-end',
                    toast: true,
                    timerProgressBar: true
                });
            });
        </script>
        @endif

        <!-- Display Warning Messages -->
        @if(session('warning'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: '{{ session('warning') }}',
                    timer: 4000,
                    showConfirmButton: true,
                    confirmButtonColor: '#f59e0b',
                    position: 'center'
                });
            });
        </script>
        @endif

        <!-- Custom SweetAlert Functions -->
        <script>
            // Global SweetAlert functions
            window.showAlert = function(type, message, title = null) {
                const config = {
                    icon: type,
                    title: title || type.charAt(0).toUpperCase() + type.slice(1),
                    text: message,
                    confirmButtonColor: getButtonColor(type)
                };

                // Toast for success and info
                if (type === 'success' || type === 'info') {
                    config.timer = 3000;
                    config.showConfirmButton = false;
                    config.position = 'top-end';
                    config.toast = true;
                    config.timerProgressBar = true;
                }

                Swal.fire(config);
            };

            function getButtonColor(type) {
                const colors = {
                    success: '#10b981',
                    error: '#ef4444',
                    warning: '#f59e0b',
                    info: '#3b82f6'
                };
                return colors[type] || '#3b82f6';
            }

            // Confirm delete function
            window.confirmDelete = function(formId, message = 'You won\'t be able to revert this!') {
                Swal.fire({
                    title: 'Are you sure?',
                    text: message,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById(formId).submit();
                    }
                });
                return false;
            };
        </script>

        <style>
            [x-cloak] {
                display: none !important;
            }
        </style>
    </body>
</html>
