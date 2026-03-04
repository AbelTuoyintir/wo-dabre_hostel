<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- SweetAlert2 CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen flex bg-gray-100">
             @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
           <main class="flex-1 p-4 sm:p-6 lg:p-8 overflow-y-auto">
                @yield('content')
            </main>
        </div>

        <!-- SweetAlert2 JS -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
    </body>
</html>
