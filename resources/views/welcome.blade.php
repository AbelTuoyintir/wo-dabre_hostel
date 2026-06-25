@extends('layouts.home')

@section('title', 'Wo-dabre - Find your next student home')

@section('content')
    <!-- SEARCH & FILTER SECTION -->
    <div class="sticky top-0 z-40 bg-white border-b border-slate-100 pt-4 pb-2 md:pt-6 md:pb-4 shadow-sm">
        <div class="container mx-auto px-4 md:px-8">
            <!-- Search Bar (Airbnb style) -->
            <div class="max-w-3xl mx-auto mb-6 hidden md:block">
                <form action="{{ route('hostels.index') }}" method="GET" class="flex items-center bg-white border border-slate-200 rounded-full py-2 px-4 shadow-md hover:shadow-lg transition-shadow">
                    <div class="flex-1 px-4 border-r border-slate-200">
                        <label for="search_location" class="block text-[10px] font-bold uppercase text-slate-800">Location</label>
                        <input type="text" id="search_location" name="search" value="{{ request('search') }}" placeholder="Where are you going?"
                               class="w-full text-sm text-slate-800 placeholder:text-slate-500 bg-transparent border-none focus:ring-0 p-0">
                    </div>
                    <div class="flex-1 px-4 border-r border-slate-200">
                        <label for="search_campus" class="block text-[10px] font-bold uppercase text-slate-800">Campus</label>
                        <select id="search_campus" name="location" class="w-full text-sm text-slate-800 bg-transparent border-none focus:ring-0 p-0 appearance-none">
                            <option value="all">All Campuses</option>
                            @if(isset($locations))
                                @foreach($locations as $loc)
                                    <option value="{{ $loc }}" {{ request('location') == $loc ? 'selected' : '' }}>{{ $loc }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="flex-1 px-4">
                        <label for="search_price" class="block text-[10px] font-bold uppercase text-slate-800">Price</label>
                        <select id="search_price" name="price_range" class="w-full text-sm text-slate-800 bg-transparent border-none focus:ring-0 p-0 appearance-none">
                            <option value="">Any price</option>
                            <option value="0-2000" {{ request('price_range') == '0-2000' ? 'selected' : '' }}>Under ₵2000</option>
                            <option value="2100-4000" {{ request('price_range') == '2100-4000' ? 'selected' : '' }}>₵2100 - ₵4000</option>
                            <option value="4100-6000" {{ request('price_range') == '4100-6000' ? 'selected' : '' }}>₵4100 - ₵6000</option>
                            <option value="6100-8000" {{ request('price_range') == '6100-8000' ? 'selected' : '' }}>₵6100 - ₵8000</option>
                            <option value="8200+" {{ request('price_range') == '8200+' ? 'selected' : '' }}>Above ₵8200</option>
                        </select>
                    </div>
                    <button type="submit" class="bg-blue-500 hover:bg-rose-600 text-white p-3 rounded-full flex items-center justify-center transition-colors">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>

            <!-- Mobile Search Bar -->
            <div class="md:hidden mb-4">
                <form action="{{ route('hostels.index') }}" method="GET" class="flex items-center bg-white border border-slate-200 rounded-full py-2 px-4 shadow-sm">
                    <i class="fas fa-search text-rose-500 mr-3"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Where to?"
                           class="flex-1 text-sm text-slate-800 placeholder:text-slate-500 bg-transparent border-none focus:ring-0 p-0">
                    <button type="button" class="text-slate-400">
                        <i class="fas fa-sliders-h"></i>
                    </button>
                </form>
            </div>

            <!-- Categories / Filters (Airbnb style) -->
            <div class="flex items-center gap-8 overflow-x-auto no-scrollbar py-2">
                <a href="{{ route('hostels.index', array_merge(request()->except('location'), ['location' => 'all'])) }}"
                   class="flex flex-col items-center gap-2 group min-w-max border-b-2 {{ !request('location') || request('location') == 'all' ? 'border-slate-800 opacity-100' : 'border-transparent opacity-60 hover:opacity-100 hover:border-slate-200' }} pb-2 transition-all">
                    <i class="fas fa-university text-xl"></i>
                    <span class="text-xs font-medium">All Campuses</span>
                </a>

                @if(isset($locations))
                    @foreach($locations as $location)
                        <a href="{{ route('hostels.index', array_merge(request()->except('location'), ['location' => $location])) }}"
                           class="flex flex-col items-center gap-2 group min-w-max border-b-2 {{ request('location') == $location ? 'border-slate-800 opacity-100' : 'border-transparent opacity-60 hover:opacity-100 hover:border-slate-200' }} pb-2 transition-all">
                            <i class="fas fa-map-marker-alt text-xl"></i>
                            <span class="text-xs font-medium">{{ $location }}</span>
                        </a>
                    @endforeach
                @endif

                <!-- More categories based on amenities or features -->
                <button class="flex flex-col items-center gap-2 group min-w-max border-b-2 border-transparent opacity-60 hover:opacity-100 hover:border-slate-200 pb-2 transition-all">
                    <i class="fas fa-wifi text-xl"></i>
                    <span class="text-xs font-medium">Free WiFi</span>
                </button>
                <button class="flex flex-col items-center gap-2 group min-w-max border-b-2 border-transparent opacity-60 hover:opacity-100 hover:border-slate-200 pb-2 transition-all">
                    <i class="fas fa-shield-alt text-xl"></i>
                    <span class="text-xs font-medium">Security</span>
                </button>
                <button class="flex flex-col items-center gap-2 group min-w-max border-b-2 border-transparent opacity-60 hover:opacity-100 hover:border-slate-200 pb-2 transition-all">
                    <i class="fas fa-bolt text-xl"></i>
                    <span class="text-xs font-medium">Instant</span>
                </button>

                <div class="ml-auto md:flex hidden">
                    <button class="flex items-center gap-2 border border-slate-200 rounded-xl px-4 py-2 text-xs font-medium hover:bg-slate-50 transition">
                        <i class="fas fa-sliders-h"></i>
                        <span>Filters</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- MAIN LISTINGS GRID -->
    <section class="container mx-auto px-4 md:px-8 py-8 md:py-12">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-x-6 gap-y-10">
            @if(isset($transformedHostels) && count($transformedHostels) > 0)
                @foreach($transformedHostels as $hostel)
                    @php
                        $imageUrl = !empty($hostel['primary_image']['image_path'])
                            ? image_url($hostel['primary_image']['image_path'])
                            : (!empty($hostel['images']) && count($hostel['images']) > 0
                                ? image_url($hostel['images']->first()['image_path'])
                                : 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=600&h=400&fit=crop');
                        $minPrice = $hostel['min_price'] ?? 0;
                        $availableCount = $hostel['available_rooms_count'] ?? 0;
                    @endphp

                    <a href="{{ route('hostels.guest.show', $hostel['uuid'] ?? $hostel['id']) }}" class="group block">
                        <div class="flex flex-col gap-3">
                            <!-- Image Container -->
                            <div class="relative aspect-square overflow-hidden rounded-xl bg-slate-100">
                                <img src="{{ $imageUrl }}" alt="{{ $hostel['name'] }}"
                                     class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110">

                                @if($hostel['is_featured'])
                                    <div class="absolute top-3 left-3">
                                        <span class="bg-white/95 px-2 py-1 rounded-md text-[10px] font-bold shadow-sm">FEATURED</span>
                                    </div>
                                @endif
                                
                                <button class="absolute top-3 right-3 text-white text-xl drop-shadow-md">
                                    <i class="far fa-heart"></i>
                                </button>

                                <div class="absolute bottom-3 left-3 right-3 flex justify-between items-end opacity-0 group-hover:opacity-100 transition-opacity">
                                     <span class="bg-white/90 text-slate-800 text-[10px] font-bold px-2 py-1 rounded shadow-sm">
                                        {{ $availableCount }} rooms left
                                    </span>
                                </div>
                            </div>

                            <!-- Details -->
                            <div class="space-y-1">
                                <div class="flex justify-between items-start">
                                    <h3 class="font-bold text-slate-800 truncate">{{ $hostel['name'] }}</h3>
                                    <div class="flex items-center gap-1">
                                        <i class="fas fa-star text-xs"></i>
                                        <span class="text-sm font-light text-slate-600">{{ $hostel['rating'] ?? '4.5' }}</span>
                                    </div>
                                </div>
                                <p class="text-slate-500 text-sm truncate">{{ $hostel['location'] }}</p>
                                <p class="text-slate-500 text-sm truncate">Added recently</p>
                                <div class="pt-1">
                                    <span class="font-bold text-slate-800">₵{{ number_format($minPrice, 2) }}</span>
                                    <span class="text-slate-600 font-light text-sm">per year</span>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            @else
                <div class="col-span-full text-center py-24 bg-slate-50 rounded-2xl border-2 border-dashed border-slate-200">
                    <div class="mb-4">
                        <i class="fas fa-search text-4xl text-slate-300"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800">No listings found</h3>
                    <p class="text-slate-500 mt-2">Try adjusting your filters or search criteria.</p>
                    <a href="{{ route('hostels.index') }}" class="mt-6 inline-block bg-slate-800 text-white px-6 py-2 rounded-lg font-medium hover:bg-slate-900">
                        Clear all filters
                    </a>
                </div>
            @endif
        </div>

        @if(isset($hostels) && $hostels->hasPages())
            <div class="mt-16 flex justify-center">
                {{ $hostels->links('pagination::tailwind') }}
            </div>
        @endif
    </section>

    <!-- FLOATING MAP BUTTON -->
    <div class="fixed bottom-24 left-1/2 -translate-x-1/2 z-30 md:bottom-10">
        <button class="bg-slate-800 hover:bg-slate-900 text-white px-5 py-3 rounded-full flex items-center gap-2 shadow-xl hover:scale-105 transition-all text-sm font-bold">
            <span>Show Map</span>
            <i class="fas fa-map"></i>
        </button>
    </div>

    <!-- MOBILE BOTTOM NAVIGATION (Airbnb style) -->
    <nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-slate-200 px-5 py-3 sm:hidden z-50 flex justify-around items-center">
        <a href="{{ route('hostels.index') }}" class="flex flex-col items-center gap-1 {{ !request()->routeIs('hostels.index') || request()->hasAny(['location', 'search', 'price_range']) ? 'text-slate-400' : 'text-rose-500' }}">
            <i class="fas fa-search text-xl"></i>
            <span class="text-[10px] font-medium">Explore</span>
        </a>
        <a href="#" class="flex flex-col items-center gap-1 text-slate-400">
            <i class="far fa-heart text-xl"></i>
            <span class="text-[10px] font-medium">Wishlists</span>
        </a>
        <a href="#" class="flex flex-col items-center gap-1 text-slate-400">
            <i class="fas fa-university text-xl"></i>
            <span class="text-[10px] font-medium">Bookings</span>
        </a>
        @auth
            @if(auth()->user()->role === 'student')
                <a href="{{ route('student.dashboard') }}" class="flex flex-col items-center gap-1 text-slate-400">
                    <i class="far fa-user-circle text-xl"></i>
                    <span class="text-[10px] font-medium">Profile</span>
                </a>
            @elseif(auth()->user()->role === 'admin')
                <a href="{{ route('admin.dashboard') }}" class="flex flex-col items-center gap-1 text-slate-400">
                    <i class="far fa-user-circle text-xl"></i>
                    <span class="text-[10px] font-medium">Admin</span>
                </a>
            @elseif(auth()->user()->role === 'manager')
                <a href="{{ route('hostel-manager.dashboard') }}" class="flex flex-col items-center gap-1 text-slate-400">
                    <i class="far fa-user-circle text-xl"></i>
                    <span class="text-[10px] font-medium">Manager</span>
                </a>
            @endif
        @else
            <a href="{{ route('login') }}" class="flex flex-col items-center gap-1 text-slate-400">
                <i class="far fa-user-circle text-xl"></i>
                <span class="text-[10px] font-medium">Log in</span>
            </a>
        @endauth
    </nav>

    <style>
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .aspect-square {
            aspect-ratio: 1 / 1;
        }
        @media (min-width: 768px) {
            .aspect-square {
                aspect-ratio: 1 / 1;
            }
        }
    </style>
@endsection
