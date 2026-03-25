@extends('layouts.home')

@section('title', 'UCC Hostel Booking System')

@section('content')
    <!-- Hero Section with Sticky Search -->
    <section class="relative min-h-[50vh] flex items-center justify-center overflow-hidden bg-gradient-to-br from-slate-900 via-blue-900 to-slate-900">
        <!-- Animated Background Pattern -->
        <div class="absolute inset-0 opacity-20">
            <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.05\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')]"></div>
        </div>
        
        <div class="relative z-10 container mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="max-w-4xl mx-auto">
                <span class="inline-block px-4 py-1.5 rounded-full bg-blue-500/20 text-blue-300 text-sm font-medium mb-6 border border-blue-500/30 backdrop-blur-sm">
                    🎓 University of Cape Coast
                </span>
                <h1 class="text-4xl sm:text-5xl md:text-7xl font-bold text-white mb-6 leading-tight tracking-tight">
                    Find Your Perfect <br/>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-cyan-400">Student Home</span>
                </h1>
                <p class="text-lg sm:text-xl text-slate-300 mb-10 max-w-2xl mx-auto leading-relaxed">
                    Browse premium hostels across UCC campuse. Smart booking, verified listings, and instant confirmation.
                </p>

                <!-- Sticky Search Card -->
                <div class="bg-white/10 backdrop-blur-lg rounded-3xl p-2 border border-white/20 shadow-2xl max-w-3xl mx-auto transform hover:scale-[1.02] transition-transform duration-300">
                    <form method="GET" action="{{ route('hostels.index') }}" class="flex flex-col sm:flex-row gap-2">
                        <div class="flex-1 relative">
                            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="text" name="search" value="{{ request('search', '') }}" 
                                   placeholder="Search by name or location..."
                                   class="w-full pl-12 pr-4 py-4 rounded-2xl bg-white/90 border-0 text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-blue-500 outline-none font-medium">
                        </div>
                        <button type="submit" class="px-8 py-4 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-2xl transition-all duration-300 shadow-lg hover:shadow-blue-500/50 flex items-center justify-center gap-2 group">
                            <span>Search</span>
                            <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                        </button>
                    </form>
                </div>

                <!-- Quick Stats -->
                <div class="flex flex-wrap justify-center gap-6 mt-8 text-slate-300 text-sm font-medium">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-shield-alt text-green-400"></i>
                        <span>Verified Listings</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-bolt text-yellow-400"></i>
                        <span>Instant Booking</span>
                    </div>
                    {{-- <div class="flex items-center gap-2">
                        <i class="fas fa-mobile-alt text-blue-400"></i>
                        <span>Mobile Friendly</span>
                    </div> --}}
                </div>
            </div>
        </div>

        <!-- Scroll Indicator -->
        <div class="absolute bottom-8 left-1/2 -translate-x-1/2 animate-bounce">
            <i class="fas fa-chevron-down text-white/50 text-2xl"></i>
        </div>
    </section>

    <!-- Filter Bar (Sticky on Mobile) -->
    <div class="sticky top-0 z-40 bg-white/80 backdrop-blur-md border-b border-slate-200 shadow-sm">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <!-- Location Pills -->
                <div class="flex gap-2 overflow-x-auto pb-2 sm:pb-0 w-full sm:w-auto scrollbar-hide">
                    <a href="{{ route('hostels.index', array_merge(request()->except('location'), ['location' => 'all'])) }}" 
                       class="whitespace-nowrap px-5 py-2.5 rounded-full text-sm font-semibold transition-all duration-300 {{ !request('location') || request('location') == 'all' ? 'bg-slate-900 text-white shadow-lg' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        All Campuses
                    </a>
                    @if(isset($locations))
                        @foreach($locations as $location)
                            <a href="{{ route('hostels.index', array_merge(request()->except('location'), ['location' => $location])) }}"
                               class="whitespace-nowrap px-5 py-2.5 rounded-full text-sm font-semibold transition-all duration-300 {{ request('location') == $location ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/30' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                                {{ $location }}
                            </a>
                        @endforeach
                    @endif
                </div>

                <!-- Price Filter Dropdown -->
                <div class="relative group">
                    <select name="price_range" onchange="this.form.submit()" form="filterForm"
                            class="appearance-none bg-white border border-slate-200 text-slate-700 py-2.5 pl-4 pr-10 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 font-medium cursor-pointer shadow-sm hover:border-blue-400 transition-colors">
                        <option value="">Price Range</option>
                        <option value="0-500" {{ request('price_range') == '0-500' ? 'selected' : '' }}>Under ₵500</option>
                        <option value="500-1000" {{ request('price_range') == '500-1000' ? 'selected' : '' }}>₵500 - ₵1000</option>
                        <option value="1000-1500" {{ request('price_range') == '1000-1500' ? 'selected' : '' }}>₵1000 - ₵1500</option>
                        <option value="1500-2000" {{ request('price_range') == '1500-2000' ? 'selected' : '' }}>₵1500 - ₵2000</option>
                        <option value="2000+" {{ request('price_range') == '2000+' ? 'selected' : '' }}>Above ₵2000</option>
                    </select>
                    <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                </div>
                <form id="filterForm" method="GET" action="{{ route('hostels.index') }}" class="hidden">
                    @foreach(request()->except('price_range') as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                </form>
            </div>
        </div>
    </div>

    <!-- Stats Bento Grid -->
    <section class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @if(isset($stats))
                <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-3xl p-6 text-white shadow-xl shadow-blue-500/20 transform hover:-translate-y-1 transition-transform duration-300">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-white/20 rounded-2xl backdrop-blur-sm">
                            <i class="fas fa-building text-2xl"></i>
                        </div>
                        <span class="text-blue-200 text-sm font-medium">+12% this month</span>
                    </div>
                    <p class="text-4xl font-bold mb-1">{{ $stats['total_hostels'] }}</p>
                    <p class="text-blue-100 font-medium">Verified Hostels</p>
                </div>

                <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-green-100 rounded-2xl text-green-600">
                            <i class="fas fa-bed text-2xl"></i>
                        </div>
                        <span class="text-green-600 text-sm font-medium bg-green-50 px-2 py-1 rounded-full">Available Now</span>
                    </div>
                    <p class="text-4xl font-bold text-slate-800 mb-1">{{ $stats['total_rooms'] }}</p>
                    <p class="text-slate-500 font-medium">Rooms Available</p>
                </div>

                <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-purple-100 rounded-2xl text-purple-600">
                            <i class="fas fa-map-marker-alt text-2xl"></i>
                        </div>
                    </div>
                    <p class="text-4xl font-bold text-slate-800 mb-1">{{ $stats['locations_count'] }}</p>
                    <p class="text-slate-500 font-medium">Campus Locations</p>
                </div>

                <div class="bg-gradient-to-br from-amber-500 to-orange-600 rounded-3xl p-6 text-white shadow-xl shadow-orange-500/20 transform hover:-translate-y-1 transition-transform duration-300">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-white/20 rounded-2xl backdrop-blur-sm">
                            <i class="fas fa-users text-2xl"></i>
                        </div>
                    </div>
                    <p class="text-4xl font-bold mb-1">1,850+</p>
                    <p class="text-orange-100 font-medium">Happy Students</p>
                </div>
            @else
                <!-- Loading Skeleton -->
                @for($i = 0; $i < 4; $i++)
                    <div class="bg-slate-100 rounded-3xl p-6 animate-pulse h-32"></div>
                @endfor
            @endif
        </div>
    </section>

    <!-- Hostels Grid -->
    <section class="container mx-auto px-4 sm:px-6 lg:px-8 pb-20">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-3xl font-bold text-slate-900 mb-2">Available Hostels</h2>
                <p class="text-slate-500">Find your perfect accommodation from our curated listings</p>
            </div>
            @if(isset($hostels) && $hostels->hasPages())
                <div class="hidden sm:block text-sm text-slate-500">
                    Showing {{ $hostels->firstItem() }} - {{ $hostels->lastItem() }} of {{ $hostels->total() }}
                </div>
            @endif
        </div>

        <div id="hostelsContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @if(isset($transformedHostels) && count($transformedHostels) > 0)
                @foreach($transformedHostels as $hostel)
                    @php
                        $imageUrl = !empty($hostel['primary_image']['image_path'])
                            ? Storage::url($hostel['primary_image']['image_path'])
                            : (!empty($hostel['images']) && count($hostel['images']) > 0
                                ? Storage::url($hostel['images']->first()['image_path'])
                                : 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80');
                        $minPrice = $hostel['min_price'] ?? 0;
                        $availableCount = $hostel['available_rooms_count'] ?? 0;
                    @endphp
                    
                    <article class="group bg-white rounded-3xl overflow-hidden border border-slate-100 shadow-lg hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2">
                        <!-- Image Container -->
                        <div class="relative h-56 overflow-hidden">
                            <img src="{{ $imageUrl }}" alt="{{ $hostel['name'] }}" 
                                 class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700">
                            
                            <!-- Gradient Overlay -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            
                            <!-- Badges -->
                            <div class="absolute top-4 left-4 flex gap-2">
                                <span class="px-3 py-1.5 bg-white/95 backdrop-blur-sm text-slate-800 rounded-full text-xs font-bold shadow-sm">
                                    <i class="fas fa-map-marker-alt text-red-500 mr-1"></i> {{ $hostel['location'] }}
                                </span>
                            </div>
                            
                            @if($hostel['is_featured'])
                                <div class="absolute top-4 right-4 px-3 py-1.5 bg-gradient-to-r from-amber-400 to-orange-500 text-white rounded-full text-xs font-bold shadow-lg flex items-center gap-1">
                                    <i class="fas fa-crown"></i> Featured
                                </div>
                            @endif

                            <!-- Quick View Button (appears on hover) -->
                            <div class="absolute bottom-4 left-4 right-4 opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-y-4 group-hover:translate-y-0">
                                <a href="{{ route('hostels.guest.show', $hostel['id']) }}" 
                                   class="block w-full bg-white/95 backdrop-blur-sm text-slate-900 py-3 rounded-xl font-semibold text-center hover:bg-blue-600 hover:text-white transition-colors shadow-lg">
                                    Quick View
                                </a>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-3">
                                <h3 class="text-xl font-bold text-slate-900 group-hover:text-blue-600 transition-colors line-clamp-1">{{ $hostel['name'] }}</h3>
                                <div class="flex items-center bg-yellow-50 px-2 py-1 rounded-lg">
                                    <i class="fas fa-star text-yellow-500 text-sm mr-1"></i>
                                    <span class="font-bold text-slate-800 text-sm">{{ $hostel['rating'] ?? '4.5' }}</span>
                                </div>
                            </div>
                            
                            <p class="text-slate-500 text-sm mb-4 line-clamp-2 leading-relaxed">{{ \Illuminate\Support\Str::limit($hostel['description'], 100) }}</p>

                            <!-- Amenities -->
                            <div class="flex flex-wrap gap-2 mb-4">
                                @if($hostel['amenities'])
                                    @foreach(array_slice($hostel['amenities'], 0, 3) as $amenity)
                                        <span class="px-3 py-1 bg-slate-100 text-slate-600 rounded-full text-xs font-medium">{{ $amenity }}</span>
                                    @endforeach
                                    @if(count($hostel['amenities']) > 3)
                                        <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-full text-xs font-medium">+{{ count($hostel['amenities']) - 3 }}</span>
                                    @endif
                                @endif
                            </div>

                            <!-- Price & Availability -->
                            <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                                <div>
                                    <p class="text-xs text-slate-400 mb-1">Starting from</p>
                                    <p class="text-2xl font-bold text-slate-900">₵{{ number_format($minPrice) }}<span class="text-sm font-normal text-slate-400">/year</span></p>
                                </div>
                                <div class="text-right">
                                    <div class="flex items-center text-green-600 font-semibold text-sm mb-1">
                                        <span class="relative flex h-3 w-3 mr-2">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                                        </span>
                                        {{ $availableCount }} rooms left
                                    </div>
                                </div>
                            </div>

                            <!-- CTA Button -->
                            <a href="{{ route('hostels.guest.show', $hostel['id']) }}" 
                               class="mt-4 block w-full bg-slate-900 hover:bg-blue-600 text-white py-3.5 rounded-xl font-semibold transition-all duration-300 text-center flex items-center justify-center gap-2 group/btn">
                                <span>View Details</span>
                                <i class="fas fa-arrow-right group-hover/btn:translate-x-1 transition-transform"></i>
                            </a>
                        </div>
                    </article>
                @endforeach
            @else
                <!-- Empty State -->
                <div class="col-span-full text-center py-20 bg-slate-50 rounded-3xl border-2 border-dashed border-slate-200">
                    <div class="w-24 h-24 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-search text-slate-400 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">No hostels found</h3>
                    <p class="text-slate-500 mb-6 max-w-md mx-auto">Try adjusting your filters or search terms to find what you're looking for.</p>
                    <a href="{{ route('hostels.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold transition-colors shadow-lg shadow-blue-500/30">
                        <i class="fas fa-undo"></i>
                        Clear All Filters
                    </a>
                </div>
            @endif
        </div>

        <!-- Pagination -->
        @if(isset($hostels) && $hostels->hasPages())
            <div class="mt-12 flex justify-center">
                {{ $hostels->links('pagination::tailwind') }}
            </div>
        @endif
    </section>

    <!-- Mobile Bottom Navigation (visible only on mobile) -->
    <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-slate-200 px-6 py-4 sm:hidden z-50 flex justify-around items-center shadow-lg">
        <a href="{{ route('hostels.index') }}" class="flex flex-col items-center text-blue-600">
            <i class="fas fa-home text-xl mb-1"></i>
            <span class="text-xs font-medium">Home</span>
        </a>
        <a href="#" class="flex flex-col items-center text-slate-400 hover:text-slate-600">
            <i class="fas fa-search text-xl mb-1"></i>
            <span class="text-xs font-medium">Search</span>
        </a>
        <a href="#" class="flex flex-col items-center text-slate-400 hover:text-slate-600">
            <i class="fas fa-heart text-xl mb-1"></i>
            <span class="text-xs font-medium">Saved</span>
        </a>
        <a href="#" class="flex flex-col items-center text-slate-400 hover:text-slate-600">
            <i class="fas fa-user text-xl mb-1"></i>
            <span class="text-xs font-medium">Profile</span>
        </a>
    </div>

    <!-- Add padding to bottom on mobile to account for nav -->
    <div class="h-20 sm:hidden"></div>

    <style>
        /* Hide scrollbar for location pills */
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        
        /* Line clamp utilities */
        .line-clamp-1 {
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
@endsection