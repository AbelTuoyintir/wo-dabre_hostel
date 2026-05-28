@extends('layouts.home')

@section('title', 'Wo-dabre - Smart Student Housing | UCC')

@section('content')
    <!-- HERO SECTION with clean white design -->
    <section class="relative min-h-screen flex items-center justify-center overflow-hidden bg-[#A4C2FA] py-20">
        <div class="relative z-10 container mx-auto px-5 py-12 text-center">
            <!-- badge -->
            <div class="inline-flex items-center gap-2 px-5 py-2 rounded-full bg-slate-100 border border-slate-200 text-slate-700 text-sm font-medium mb-6 shadow-sm">
                <span class="relative flex h-2.5 w-2.5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-80"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                </span>
                <span>🏠 University of Cape Coast • Premium Living</span>
            </div>

            <!-- main headline -->
            <h1 class="text-5xl sm:text-7xl md:text-8xl font-black text-slate-800 leading-[1.15] tracking-tight">
                <span class="block">Welcome to</span>
                <span class="block text-purple-600 mt-1">Wo-dabre</span>
            </h1>
            <p class="text-slate-500 text-base sm:text-lg max-w-2xl mx-auto mt-5 leading-relaxed">
                Your gateway to premium student accommodation. Browse verified hostels, book instantly, and experience hassle-free living near campus.
            </p>

            <!-- SEARCH CARD -->
            <div class="max-w-3xl mx-auto mt-10">
                <div class="bg-white rounded-3xl p-2 shadow-xl border border-slate-200">
                    <form method="GET" action="{{ route('hostels.index') }}" class="flex flex-col sm:flex-row gap-2">
                        <div class="flex-1 relative">
                            <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                            <input type="text" name="search" placeholder="Search hostels by name or location..." value="{{ request('search', '') }}"
                                   class="w-full pl-12 pr-5 py-4 rounded-2xl bg-white border border-slate-200 text-slate-800 placeholder:text-slate-400 focus:ring-2 focus:ring-purple-500 outline-none text-sm font-medium">
                        </div>
                        <button type="submit" class="px-8 py-4 bg-purple-600 hover:bg-purple-700 text-white font-bold rounded-2xl transition-all shadow-md flex items-center justify-center gap-2 group">
                            <span>Explore Hostels</span>
                            <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform text-sm"></i>
                        </button>
                    </form>
                </div>
            </div>

            <!-- trust badges -->
            <div class="flex flex-wrap justify-center gap-6 mt-12">
                <div class="flex items-center gap-2 text-slate-600 text-sm font-medium"><i class="fas fa-check-circle text-green-500 text-base"></i> Verified Listings</div>
                <div class="flex items-center gap-2 text-slate-600 text-sm font-medium"><i class="fas fa-bolt text-blue-500 text-base"></i> Instant Booking</div>
                <div class="flex items-center gap-2 text-slate-600 text-sm font-medium"><i class="fas fa-headset text-purple-500 text-base"></i> 24/7 Support</div>
            </div>

            <!-- scroll indicator -->
            <div class="absolute bottom-8 left-1/2 -translate-x-1/2 animate-bounce hidden sm:block">
                <i class="fas fa-chevron-down text-slate-400 text-xl"></i>
            </div>
        </div>
    </section>

    <!-- STICKY FILTER BAR -->
    <div class="sticky top-0 z-40 bg-white border-b border-slate-200 shadow-sm">
        <div class="container mx-auto px-4 py-4">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div class="flex gap-2 overflow-x-auto pb-1 no-scrollbar w-full sm:w-auto">
                    <a href="{{ route('hostels.index', array_merge(request()->except('location'), ['location' => 'all'])) }}" 
                       class="whitespace-nowrap px-5 py-2.5 rounded-full text-sm font-bold {{ !request('location') || request('location') == 'all' ? 'bg-purple-600 text-white shadow-md' : 'bg-slate-100 text-slate-700 hover:bg-slate-200 transition' }}">
                        All Campuses
                    </a>
                    @if(isset($locations))
                        @foreach($locations as $location)
                            <a href="{{ route('hostels.index', array_merge(request()->except('location'), ['location' => $location])) }}" 
                               class="whitespace-nowrap px-5 py-2.5 rounded-full text-sm font-semibold {{ request('location') == $location ? 'bg-purple-600 text-white shadow-md' : 'bg-slate-100 text-slate-700 hover:bg-slate-200 transition' }}">
                                {{ $location }}
                            </a>
                        @endforeach
                    @endif
                </div>
                <div class="relative w-full sm:w-auto">
                    <select name="price_range" onchange="this.form.submit()" form="filterForm"
                            class="appearance-none bg-white border border-slate-200 text-slate-800 py-2.5 pl-5 pr-10 rounded-xl text-sm font-medium focus:ring-2 focus:ring-purple-500 cursor-pointer shadow-sm">
                        <option value="">All Prices</option>
                        <option value="0-500" {{ request('price_range') == '0-500' ? 'selected' : '' }}>Under ₵500</option>
                        <option value="500-1000" {{ request('price_range') == '500-1000' ? 'selected' : '' }}>₵500 - ₵1000</option>
                        <option value="1000-1500" {{ request('price_range') == '1000-1500' ? 'selected' : '' }}>₵1000 - ₵1500</option>
                        <option value="1500-2000" {{ request('price_range') == '1500-2000' ? 'selected' : '' }}>₵1500 - ₵2000</option>
                        <option value="2000+" {{ request('price_range') == '2000+' ? 'selected' : '' }}>Above ₵2000</option>
                    </select>
                    <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                </div>
                <form id="filterForm" method="GET" action="{{ route('hostels.index') }}" class="hidden">
                    @foreach(request()->except('price_range') as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                </form>
            </div>
        </div>
    </div>

    <!-- STATS SECTION -->
    <section class="container mx-auto px-5 py-14">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-5">
            @if(isset($stats))
                <div class="group bg-purple-50 rounded-2xl p-5 text-purple-900 shadow-sm hover:shadow-md transition-all hover:-translate-y-1 border border-purple-100">
                    <div class="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center mb-3 group-hover:scale-110 transition">
                        <i class="fas fa-building text-purple-600 text-xl"></i>
                    </div>
                    <p class="text-3xl font-black">{{ $stats['total_hostels'] }}</p>
                    <p class="text-sm font-medium text-purple-700">Verified Hostels</p>
                    <div class="mt-2 flex items-center text-xs text-purple-500"><i class="fas fa-chart-line mr-1"></i> +12% growth</div>
                </div>

                <div class="group bg-white rounded-2xl p-5 border border-slate-200 shadow-sm hover:shadow-md transition-all hover:-translate-y-1">
                    <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center mb-3 group-hover:scale-110 transition">
                        <i class="fas fa-door-open text-green-600 text-xl"></i>
                    </div>
                    <p class="text-3xl font-black text-slate-800">{{ $stats['total_rooms'] }}</p>
                    <p class="text-sm font-medium text-slate-500">Rooms Available</p>
                    <span class="inline-flex mt-2 items-center gap-1 text-xs font-semibold text-green-700 bg-green-50 px-2 py-1 rounded-full">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Available now
                    </span>
                </div>

                <div class="group bg-white rounded-2xl p-5 border border-slate-200 shadow-sm hover:shadow-md transition-all hover:-translate-y-1">
                    <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center mb-3 group-hover:scale-110 transition">
                        <i class="fas fa-map-marker-alt text-blue-600 text-xl"></i>
                    </div>
                    <p class="text-3xl font-black text-slate-800">{{ $stats['locations_count'] }}</p>
                    <p class="text-sm font-medium text-slate-500">Campus Locations</p>
                </div>

                <div class="group bg-rose-50 rounded-2xl p-5 text-rose-900 shadow-sm hover:shadow-md transition-all hover:-translate-y-1 border border-rose-100">
                    <div class="w-12 h-12 rounded-xl bg-rose-100 flex items-center justify-center mb-3 group-hover:scale-110 transition">
                        <i class="fas fa-users text-rose-600 text-xl"></i>
                    </div>
                    <p class="text-3xl font-black">1,850+</p>
                    <p class="text-sm font-medium text-rose-700">Happy Students</p>
                </div>
            @endif
        </div>
    </section>

    <!-- HOSTELS GRID -->
    <section class="container mx-auto px-5 pb-28">
        <div class="flex justify-between items-end mb-7 flex-wrap gap-2">
            <div>
                <h2 class="text-3xl font-extrabold text-slate-800 tracking-tight">Discover Hostels ✨</h2>
                <p class="text-slate-500 text-sm mt-1">Premium student accommodation curated for you</p>
            </div>
            @if(isset($hostels) && $hostels->hasPages())
                <span class="text-xs text-slate-400 hidden sm:block font-medium">
                    Showing {{ $hostels->firstItem() }} of {{ $hostels->total() }} hostels
                </span>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @if(isset($transformedHostels) && count($transformedHostels) > 0)
                @foreach($transformedHostels as $hostel)
                    @php
                        $imageUrl = !empty($hostel['primary_image']['image_path'])
                            ? Storage::url($hostel['primary_image']['image_path'])
                            : (!empty($hostel['images']) && count($hostel['images']) > 0
                                ? Storage::url($hostel['images']->first()['image_path'])
                                : 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=600&h=400&fit=crop');
                        $minPrice = $hostel['min_price'] ?? 0;
                        $availableCount = $hostel['available_rooms_count'] ?? 0;
                    @endphp
                    
                    <div class="group bg-white rounded-2xl overflow-hidden shadow-md hover:shadow-xl transition-all duration-300 hover:-translate-y-1 border border-slate-200">
                        <div class="relative h-52 overflow-hidden">
                            <img src="{{ $imageUrl }}" alt="{{ $hostel['name'] }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>
                            <div class="absolute top-3 left-3 flex gap-1">
                                <span class="bg-white/95 text-slate-800 text-[11px] font-bold px-2.5 py-1 rounded-full flex items-center gap-1 shadow-sm">
                                    <i class="fas fa-map-marker-alt text-red-500 text-xs"></i> {{ $hostel['location'] }}
                                </span>
                                @if($hostel['is_featured'])
                                    <span class="bg-amber-500 text-white text-[11px] font-bold px-2.5 py-1 rounded-full shadow-sm">
                                        <i class="fas fa-star text-[10px]"></i> Featured
                                    </span>
                                @endif
                            </div>
                            <a href="{{ route('hostels.guest.show', $hostel['uuid'] ?? $hostel['id']) }}" 
                               class="absolute bottom-3 left-3 right-3 bg-white/95 text-slate-900 text-center py-2.5 rounded-xl text-sm font-bold hover:bg-purple-600 hover:text-white transition-all opacity-0 group-hover:opacity-100 translate-y-2 group-hover:translate-y-0 duration-300 shadow-lg">
                                View Details →
                            </a>
                        </div>
                        <div class="p-5">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="text-lg font-extrabold text-slate-800 group-hover:text-purple-600 transition">{{ $hostel['name'] }}</h3>
                                <div class="flex items-center gap-1 bg-amber-50 px-2 py-1 rounded-lg">
                                    <i class="fas fa-star text-amber-500 text-xs"></i>
                                    <span class="font-bold text-xs text-slate-700">{{ $hostel['rating'] ?? '4.5' }}</span>
                                </div>
                            </div>
                            <p class="text-slate-500 text-xs mb-3 line-clamp-2">{{ \Illuminate\Support\Str::limit($hostel['description'], 80) }}</p>
                            @if($hostel['amenities'])
                                <div class="flex flex-wrap gap-1.5 mb-4">
                                    @foreach(array_slice($hostel['amenities'], 0, 3) as $amenity)
                                        <span class="bg-slate-100 text-slate-600 text-xs px-2 py-1 rounded-full">{{ $amenity }}</span>
                                    @endforeach
                                    @if(count($hostel['amenities']) > 3)
                                        <span class="bg-purple-50 text-purple-600 text-xs px-2 py-1 rounded-full font-semibold">+{{ count($hostel['amenities']) - 3 }}</span>
                                    @endif
                                </div>
                            @endif
                            <div class="flex justify-between items-center border-t border-slate-100 pt-3">
                                <div>
                                    <span class="text-xs text-slate-400">Rooms Available</span>
                                    
                                </div>
                                <div class="flex items-center gap-1 text-green-600 text-xs font-bold">
                                    <span class="w-2 h-2 rounded-full bg-green-500"></span> 
                                    {{ $availableCount }} left
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="col-span-full text-center py-20">
                    <i class="fas fa-building text-6xl text-slate-300 mb-4"></i>
                    <h3 class="text-xl font-bold text-slate-800">No hostels found</h3>
                    <p class="text-slate-500 mt-2">Try adjusting your search or filters</p>
                </div>
            @endif
        </div>

        @if(isset($hostels) && $hostels->hasPages())
            <div class="mt-12 flex justify-center gap-2">
                {{ $hostels->links('pagination::tailwind') }}
            </div>
        @endif
    </section>

    <!-- MODERN MOBILE BOTTOM NAVIGATION -->
    <nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-slate-200 px-5 py-2 sm:hidden z-50 shadow-lg">
        <div class="flex justify-around items-center">
            <a href="{{ route('hostels.index') }}" class="flex flex-col items-center {{ request()->routeIs('home') ? 'text-purple-600' : 'text-slate-500' }} transition-all">
                <i class="fas fa-home text-xl"></i>
                <span class="text-[11px] font-medium mt-0.5">Home</span>
            </a>
            {{-- <a href="{{ route('hostels.index') }}" class="flex flex-col items-center text-slate-500 hover:text-purple-600 transition">
                <i class="fas fa-search text-xl"></i>
                <span class="text-[11px] font-medium">Search</span>
            </a> --}}
            <a href="#" class="flex flex-col items-center text-slate-500 hover:text-purple-600 transition relative">
                <i class="fas fa-calendar-alt text-xl"></i>
                <span class="text-[11px] font-medium">Bookings</span>
            </a>
            @auth
                <a href="{{ route('profile.edit') }}" class="flex flex-col items-center text-slate-500 hover:text-purple-600 transition">
                    <i class="fas fa-user-circle text-xl"></i>
                    <span class="text-[11px] font-medium">Profile</span>
                </a>
            @else
                <a href="{{ route('login') }}" class="flex flex-col items-center text-slate-500 hover:text-purple-600 transition">
                    <span class="text-[11px] font-medium">Login</span>
                </a>
            @endauth
        </div>
    </nav>
    <div class="h-16 sm:hidden"></div>

    <style>
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        ::-webkit-scrollbar {
            width: 5px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        ::-webkit-scrollbar-thumb {
            background: #a855f7;
            border-radius: 10px;
        }
    </style>
@endsection
