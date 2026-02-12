{{-- resources/views/layouts/navigation.blade.php --}}
<aside x-data="{
        sidebarOpen: false,
        userMenuOpen: false
    }"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    class="fixed inset-y-0 left-0 z-50 w-72 bg-white border-r border-slate-200/60
           transform transition-all duration-300 ease-out
           lg:relative lg:translate-x-0 shadow-2xl lg:shadow-none
           flex flex-col h-screen">

    {{-- Logo Area --}}
    <div class="flex items-center h-16 px-6 border-b border-slate-100 shrink-0">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 group">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-blue-600 to-indigo-600
                        flex items-center justify-center shadow-lg shadow-blue-600/20
                        group-hover:shadow-blue-600/30 group-hover:scale-105 transition-all duration-300">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
            </div>
            <div class="flex flex-col">
                <span class="font-bold text-lg text-slate-800 tracking-tight leading-none">HostelAdmin</span>
                <span class="text-[10px] text-slate-400 font-medium uppercase tracking-wider">Management System</span>
            </div>
        </a>

        {{-- Mobile Close --}}
        <button @click="sidebarOpen = false"
                class="ml-auto lg:hidden p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto py-6 px-3 space-y-1">

        {{-- Dashboard --}}
        <a href="{{ route('admin.dashboard') }}"
           class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 group
                  {{ request()->routeIs('admin.dashboard')
                     ? 'bg-blue-50 text-blue-700 shadow-sm ring-1 ring-blue-200'
                     : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
            <span class="flex items-center justify-center w-8 h-8 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-blue-100' : 'bg-slate-100 group-hover:bg-white' }} transition-colors">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.dashboard') ? 'text-blue-600' : 'text-slate-500 group-hover:text-slate-700' }}"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
            </span>
            <span>Dashboard</span>
        </a>

        {{-- Management Section --}}
        <div class="pt-6 pb-2 px-4">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Management</p>
        </div>

        {{-- Users --}}
        <a href="{{ route('admin.users.index') }}"
           class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 group
                  {{ request()->routeIs('admin.users.*')
                     ? 'bg-blue-50 text-blue-700 shadow-sm ring-1 ring-blue-200'
                     : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
            <span class="flex items-center justify-center w-8 h-8 rounded-lg {{ request()->routeIs('admin.users.*') ? 'bg-blue-100' : 'bg-slate-100 group-hover:bg-white' }} transition-colors">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.users.*') ? 'text-blue-600' : 'text-slate-500 group-hover:text-slate-700' }}"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </span>
            <span>Users</span>
            <span class="ml-auto text-xs font-semibold bg-slate-100 text-slate-600 py-0.5 px-2 rounded-full">24</span>
        </a>

        {{-- Hostels --}}
        <a href="{{ route('admin.hostels.index') }}"
           class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 group
                  {{ request()->routeIs('admin.hostels.*')
                     ? 'bg-blue-50 text-blue-700 shadow-sm ring-1 ring-blue-200'
                     : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
            <span class="flex items-center justify-center w-8 h-8 rounded-lg {{ request()->routeIs('admin.hostels.*') ? 'bg-blue-100' : 'bg-slate-100 group-hover:bg-white' }} transition-colors">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.hostels.*') ? 'text-blue-600' : 'text-slate-500 group-hover:text-slate-700' }}"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </span>
            <span>Hostels</span>
        </a>

        <a href="{{ route('admin.rooms.index') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 group
                    {{ request()->routeIs('admin.rooms.*')
                        ? 'bg-blue-50 text-blue-700 shadow-sm ring-1 ring-blue-200'
                        : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">

                <span class="flex items-center justify-center w-8 h-8 rounded-lg
                    {{ request()->routeIs('admin.rooms.*') ? 'bg-blue-100' : 'bg-slate-100 group-hover:bg-white' }}
                    transition-colors">

                    <svg class="w-5 h-5
                        {{ request()->routeIs('admin.rooms.*') ? 'text-blue-600' : 'text-slate-500 group-hover:text-slate-700' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 21h18M4 21V7a2 2 0 012-2h12a2 2 0 012 2v14M9 21v-4h6v4M8 11h2m4 0h2"/>
                    </svg>
                </span>

                <span>Rooms</span>
            </a>


        {{-- Bookings --}}
        <a href="{{ route('admin.bookings.index') }}"
           class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 group
                  {{ request()->routeIs('admin.bookings.*')
                     ? 'bg-blue-50 text-blue-700 shadow-sm ring-1 ring-blue-200'
                     : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
            <span class="flex items-center justify-center w-8 h-8 rounded-lg {{ request()->routeIs('admin.bookings.*') ? 'bg-blue-100' : 'bg-slate-100 group-hover:bg-white' }} transition-colors">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.bookings.*') ? 'text-blue-600' : 'text-slate-500 group-hover:text-slate-700' }}"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </span>
            <span>Bookings</span>
            <span class="ml-auto relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500"></span>
            </span>
        </a>

        {{-- Reports --}}
        <a href="{{ route('admin.reports.index') }}"
           class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 group
                  {{ request()->routeIs('admin.reports.*')
                     ? 'bg-blue-50 text-blue-700 shadow-sm ring-1 ring-blue-200'
                     : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
            <span class="flex items-center justify-center w-8 h-8 rounded-lg {{ request()->routeIs('admin.reports.*') ? 'bg-blue-100' : 'bg-slate-100 group-hover:bg-white' }} transition-colors">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.reports.*') ? 'text-blue-600' : 'text-slate-500 group-hover:text-slate-700' }}"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </span>
            <span>Reports</span>
        </a>
    </nav>

    {{-- User Section --}}
    <div class="border-t border-slate-100 p-4 shrink-0">
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open"
                    class="w-full flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 transition-colors">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-slate-700 to-slate-900
                            flex items-center justify-center text-white font-semibold shadow-md text-sm">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div class="flex-1 text-left min-w-0">
                    <div class="text-sm font-semibold text-slate-800 truncate">{{ Auth::user()->name }}</div>
                    <div class="text-xs text-slate-500 truncate">{{ Auth::user()->email }}</div>
                </div>
                <svg class="w-4 h-4 text-slate-400 transition-transform shrink-0"
                     :class="open ? 'rotate-180' : ''"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            {{-- Dropdown --}}
            <div x-show="open"
                 x-transition
                 @click.away="open = false"
                 class="absolute bottom-full left-0 right-0 mb-2 bg-white rounded-xl shadow-xl border border-slate-100 overflow-hidden">
                <a href="{{ route('profile.edit') }}"
                   class="flex items-center gap-2 px-4 py-3 text-sm text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Profile Settings
                </a>
                <div class="border-t border-slate-100"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full flex items-center gap-2 px-4 py-3 text-sm text-rose-600 hover:bg-rose-50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Sign Out
                    </button>
                </form>
            </div>
        </div>
    </div>
</aside>

{{-- Mobile Header Toggle --}}
<div class="lg:hidden fixed top-0 left-0 right-0 h-16 bg-white border-b border-slate-200 z-40 flex items-center px-4">
    <button @click="sidebarOpen = !sidebarOpen" class="p-2 -ml-2 text-slate-600 hover:bg-slate-100 rounded-lg">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
    </button>
    <span class="ml-3 font-semibold text-slate-800">HostelAdmin</span>
</div>
