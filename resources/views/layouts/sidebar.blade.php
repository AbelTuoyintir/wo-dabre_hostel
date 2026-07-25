{{-- Vertical Sidebar --}}
<div class="hidden md:flex md:w-64 md:flex-col md:fixed md:inset-y-0">
    <!-- Sidebar component -->
    <div class="flex flex-col flex-grow pt-5 bg-gray-900 overflow-y-auto">
        <div class="flex items-center flex-shrink-0 px-4">
            <!-- Logo -->
            <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
                <x-application-logo class="h-8 w-auto text-white" />
                <span class="text-xl font-bold text-white">{{ config('app.name', 'Laravel') }}</span>
            </a>
        </div>

        <!-- User Profile Section -->
        <div class="mt-6 px-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="h-10 w-10 rounded-full bg-gray-700 flex items-center justify-center text-white font-semibold">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-base font-medium text-white">{{ Auth::user()->name }}</p>
                    <p class="text-sm font-medium text-gray-400">{{ Auth::user()->email }}</p>
                    <p class="text-xs font-medium text-gray-500 mt-1">
                        @switch(Auth::user()->role)
                            @case('admin')
                                <span class="px-2 py-1 bg-blue-500 text-white rounded">Administrator</span>
                                @break
                            @case('hostel_manager')
                                <span class="px-2 py-1 bg-green-500 text-white rounded">Hostel Manager</span>
                                @break
                            @case('student')
                                <span class="px-2 py-1 bg-purple-500 text-white rounded">Student</span>
                                @break
                        @endswitch
                    </p>
                </div>
            </div>
        </div>

        <!-- Navigation Links -->
        <nav class="mt-8 flex-1 px-2 space-y-1">
            <!-- Dashboard -->
            <x-nav-link-sidebar :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                <svg class="mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                </svg>
                Dashboard
            </x-nav-link-sidebar>

            <!-- Role-Based Navigation -->
            @auth
                @if(auth()->user()->role === 'admin')
                    <!-- Admin Navigation -->
                    <x-nav-link-sidebar :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                        <svg class="mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        Admin Dashboard
                    </x-nav-link-sidebar>

                    @if(Route::has('admin.users.index'))
                    <x-nav-link-sidebar :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                        <svg class="mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                        </svg>
                        Users
                    </x-nav-link-sidebar>
                    @endif

                    @if(Route::has('admin.hostels.index'))
                    <x-nav-link-sidebar :href="route('admin.hostels.index')" :active="request()->routeIs('admin.hostels.*')">
                        <svg class="mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 21v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21m0 0h4.5V3.545M12.75 21h7.5V10.75M2.25 21h1.5m18 0h-18M2.25 9l4.5-1.636M18.75 3l-1.5.545m0 6.205 3 1m1.5.5-1.5-.5M6.75 7.364V3h-3v18m3-13.636 10.5-3.819" />
                        </svg>
                        Hostels
                    </x-nav-link-sidebar>
                    @endif

                @elseif(auth()->user()->role === 'hostel_manager')
                    <!-- Hostel Manager Navigation -->
                    <x-nav-link-sidebar :href="route('hostel-manager.dashboard')" :active="request()->routeIs('hostel-manager.*')">
                        <svg class="mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                        </svg>
                        Manager Dashboard
                    </x-nav-link-sidebar>

                @elseif(auth()->user()->role === 'student')
                    <!-- Student Navigation -->
                    <x-nav-link-sidebar :href="route('student.dashboard')" :active="request()->routeIs('student.*')">
                        <svg class="mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 0 0-.491 6.347A48.627 48.627 0 0 1 12 20.904a48.627 48.627 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.57 50.57 0 0 0-2.658-.813A59.905 59.905 0 0 1 12 3.493a59.902 59.902 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
                        </svg>
                        Student Dashboard
                    </x-nav-link-sidebar>

                    @if(Route::has('student.payment'))
                    <x-nav-link-sidebar :href="route('student.payment')" :active="request()->routeIs('student.payment*')">
                        <svg class="mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                        </svg>
                        Make Payment
                    </x-nav-link-sidebar>
                    @endif
                @endif
            @endauth

            <!-- Common Links -->
            <x-nav-link-sidebar :href="route('profile.edit')" :active="request()->routeIs('profile.edit')">
                <svg class="mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                </svg>
                Profile
            </x-nav-link-sidebar>
        </nav>

        <!-- Logout -->
        <div class="flex-shrink-0 flex border-t border-gray-700 p-4">
            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <button type="submit" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-300 hover:bg-gray-700 hover:text-white w-full">
                    <svg class="mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                    </svg>
                    Logout
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Main Content (pushes content to the right of sidebar) -->
<div class="md:pl-64 flex flex-col flex-1">
    <!-- Top Bar (for mobile/hamburger) -->
    <div class="sticky top-0 z-10 md:hidden pl-1 pt-1 sm:pl-3 sm:pt-3 bg-gray-100">
        <button @click="open = !open" type="button" class="-ml-0.5 -mt-0.5 h-12 w-12 inline-flex items-center justify-center rounded-md text-gray-500 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500">
            <span class="sr-only">Open sidebar</span>
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
        </button>
    </div>

    <!-- Mobile Sidebar (hidden by default) -->
    <div x-show="open" @click.away="open = false" class="md:hidden" x-cloak>
        <div class="fixed inset-0 flex z-40">
            <div class="fixed inset-0 bg-gray-600 bg-opacity-75" @click="open = false"></div>
            <div class="relative flex-1 flex flex-col max-w-xs w-full bg-gray-900">
                <!-- Mobile sidebar content (same as desktop but with close button) -->
                <div class="absolute top-0 right-0 -mr-12 pt-2">
                    <button @click="open = false" type="button" class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                        <span class="sr-only">Close sidebar</span>
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <!-- Mobile sidebar content would go here (duplicate of desktop sidebar) -->
            </div>
        </div>
    </div>


</div>
