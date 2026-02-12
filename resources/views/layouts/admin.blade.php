d cdx          <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} - @yield('title', 'Admin')</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-100">
        <div class="min-h-screen flex">
            @include('layouts.sidebar')

            <!-- Page Content -->
            <div class="flex-1 flex flex-col overflow-hidden">
                <!-- Page Heading -->
                @isset($page-title)
                    <header class="bg-white shadow">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            <h1 class="text-3xl font-bold text-gray-900">{{ $page-title }}</h1>
                        </div>
                    </header>
                @endisset

                <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
                    @yield('content')
                </main>
            </div>
        </div>

        @stack('scripts')
    </body>
</html>
