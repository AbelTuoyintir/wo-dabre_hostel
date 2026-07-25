@extends('layouts.student')

@section('title', $hostel->name)
@section('content')

<style>
    /* Custom Animations & Styles */
    .hostel-card {
        transition: all 0.3s ease;
    }
    .hostel-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    }
    .room-card {
        transition: all 0.3s ease;
        border: 1px solid #e5e7eb;
    }
    .room-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.08);
        border-color: #3b82f6;
    }
    .image-slide {
        transition: all 0.5s ease;
    }
    .amenity-tag {
        transition: all 0.2s ease;
    }
    .amenity-tag:hover {
        transform: scale(1.05);
        background: #dbeafe;
    }
    .book-btn {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    .book-btn:hover {
        background: linear-gradient(135deg, #1d4ed8, #1e40af);
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(37, 99, 235, 0.3);
    }
    .book-btn:active {
        transform: translateY(0);
    }
    .book-btn::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255,255,255,0.2);
        transition: all 0.5s ease;
    }
    .book-btn:active::after {
        width: 300px;
        height: 300px;
        top: -100px;
        left: -100px;
    }
    .availability-badge {
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.7; }
        100% { opacity: 1; }
    }
    .stat-card {
        background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
    }
    .stat-card:hover {
        background: linear-gradient(135deg, #eff6ff, #dbeafe);
        border-color: #3b82f6;
        transform: scale(1.02);
    }
    .gallery-thumb {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .gallery-thumb:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }
    .gallery-thumb.active {
        border: 3px solid #3b82f6;
        transform: scale(1.05);
    }
    .feature-tag {
        background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
        transition: all 0.2s ease;
    }
    .feature-tag:hover {
        background: linear-gradient(135deg, #dbeafe, #bfdbfe);
    }
    .price-tag {
        background: linear-gradient(135deg, #eff6ff, #dbeafe);
    }
    @media (max-width: 640px) {
        .hostel-image-container {
            height: 250px !important;
        }
        .room-grid {
            grid-template-columns: 1fr !important;
        }
    }
</style>

<!-- Breadcrumb Navigation -->
<nav class="flex items-center space-x-2 text-sm text-gray-600 mb-6 bg-white rounded-lg px-4 py-3 shadow-sm">
    <a href="{{ route('student.dashboard') }}" class="hover:text-blue-600 transition">
        <i class="fas fa-home"></i>
    </a>
    <span class="text-gray-400">/</span>
    <a href="{{ route('student.hostels.browse') }}" class="hover:text-blue-600 transition">Hostels</a>
    <span class="text-gray-400">/</span>
    <span class="text-blue-600 font-medium">{{ $hostel->name }}</span>
</nav>

<!-- Main Hostel Card -->
<div class="hostel-card bg-white rounded-2xl shadow-lg overflow-hidden mb-8">
    <!-- Image Gallery Section -->
    <div class="relative hostel-image-container h-[500px] bg-gradient-to-br from-gray-900 to-gray-800">
        @if($hostel->images && $hostel->images->count() > 0)
            <div x-data="{ 
                activeImage: 0, 
                images: {{ json_encode($hostel->images->pluck('image_path')->toArray()) }},
                autoPlay: true
            }" 
            x-init="setInterval(() => { if(autoPlay) activeImage = (activeImage + 1) % images.length }, 5000)"
            class="relative h-full">
                <!-- Main Image -->
                <div class="relative h-full overflow-hidden">
                    <template x-for="(image, index) in images" :key="index">
                        <img :src="'{{ asset('storage') }}/' + image" 
                             :alt="'Hostel Image ' + (index + 1)"
                             class="absolute inset-0 w-full h-full object-cover transition-opacity duration-700"
                             :class="activeImage === index ? 'opacity-100' : 'opacity-0'">
                    </template>
                    
                    <!-- Gradient Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                </div>

                <!-- Navigation Arrows -->
                @if($hostel->images->count() > 1)
                <button @click="activeImage = (activeImage - 1 + images.length) % images.length; autoPlay = false"
                        class="absolute left-4 top-1/2 -translate-y-1/2 bg-black/50 backdrop-blur-sm text-white p-3 rounded-full hover:bg-black/70 transition-all hover:scale-110">
                    <i class="fas fa-chevron-left text-xl"></i>
                </button>
                <button @click="activeImage = (activeImage + 1) % images.length; autoPlay = false"
                        class="absolute right-4 top-1/2 -translate-y-1/2 bg-black/50 backdrop-blur-sm text-white p-3 rounded-full hover:bg-black/70 transition-all hover:scale-110">
                    <i class="fas fa-chevron-right text-xl"></i>
                </button>

                <!-- Image Counter -->
                <div class="absolute top-4 right-4 bg-black/60 backdrop-blur-sm text-white text-sm px-3 py-1 rounded-full">
                    <span x-text="activeImage + 1"></span> / <span x-text="images.length"></span>
                </div>

                <!-- Dots Navigation -->
                <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex space-x-2">
                    <template x-for="(_, index) in images" :key="index">
                        <button @click="activeImage = index; autoPlay = false"
                                class="w-3 h-3 rounded-full transition-all duration-300"
                                :class="activeImage === index ? 'bg-white w-8' : 'bg-white/50 hover:bg-white/70'">
                        </button>
                    </template>
                </div>

                <!-- Play/Pause Button -->
                <button @click="autoPlay = !autoPlay"
                        class="absolute bottom-6 right-4 bg-black/50 backdrop-blur-sm text-white p-2 rounded-full hover:bg-black/70 transition">
                    <i class="fas" :class="autoPlay ? 'fa-pause' : 'fa-play'"></i>
                </button>
                @endif
            </div>
        @else
            <div class="w-full h-full flex items-center justify-center">
                <div class="text-center text-white">
                    <i class="fas fa-building text-7xl mb-4 opacity-50"></i>
                    <p class="text-xl font-light">No Images Available</p>
                </div>
            </div>
        @endif

        <!-- Hostel Badges Overlay -->
        <div class="absolute bottom-6 left-6 flex flex-wrap gap-2">
            <span class="bg-white/90 backdrop-blur-sm text-blue-600 px-4 py-2 rounded-full text-sm font-semibold shadow-lg">
                <i class="fas fa-star mr-2 text-yellow-400"></i>
                {{ number_format($hostel->rating ?? 0, 1) }} ★
            </span>
            @if($availableRooms && $availableRooms->count() > 0)
            <span class="bg-green-500/90 backdrop-blur-sm text-white px-4 py-2 rounded-full text-sm font-semibold shadow-lg availability-badge">
                <i class="fas fa-check-circle mr-2"></i>
                {{ $availableRooms->count() }} Rooms Available
            </span>
            @else
            <span class="bg-red-500/90 backdrop-blur-sm text-white px-4 py-2 rounded-full text-sm font-semibold shadow-lg">
                <i class="fas fa-times-circle mr-2"></i>
                Fully Booked
            </span>
            @endif
        </div>
    </div>

    <!-- Hostel Information -->
    <div class="p-8">
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
            <div class="flex-1">
                <div class="flex items-start gap-4">
                    <div class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-building text-blue-600 text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ $hostel->name }}</h1>
                        <div class="flex flex-wrap items-center gap-4 mt-2">
                            <span class="text-gray-600">
                                <i class="fas fa-map-marker-alt text-red-500 mr-1"></i>
                                {{ $hostel->location }}
                            </span>
                            <span class="text-gray-300">|</span>
                            <span class="text-gray-600">
                                <i class="fas fa-calendar-alt text-blue-500 mr-1"></i>
                                Listed {{ $hostel->created_at->diffForHumans() }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex flex-col items-end">
                <div class="price-tag px-6 py-3 rounded-2xl">
                    <span class="text-3xl font-bold text-blue-600">₵{{ number_format((float) ($hostel->min_price ?? 0), 2) }}</span>
                    <span class="text-sm text-gray-500 ml-1">/ year</span>
                </div>
                <p class="text-sm text-gray-500 mt-1">Starting from</p>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-8">
            <div class="stat-card rounded-xl p-4 text-center">
                <i class="fas fa-bed text-blue-500 text-2xl mb-2"></i>
                <div class="text-2xl font-bold text-gray-800">{{ $availableRooms->count() ?? 0 }}</div>
                <div class="text-sm text-gray-500">Available Rooms</div>
            </div>
            <div class="stat-card rounded-xl p-4 text-center">
                <i class="fas fa-users text-purple-500 text-2xl mb-2"></i>
                <div class="text-2xl font-bold text-gray-800">{{ $hostel->total_spaces ?? 0 }}</div>
                <div class="text-sm text-gray-500">Total Spaces</div>
            </div>
            <div class="stat-card rounded-xl p-4 text-center">
                <i class="fas fa-star text-yellow-500 text-2xl mb-2"></i>
                <div class="text-2xl font-bold text-gray-800">{{ number_format($hostel->rating ?? 0, 1) }}</div>
                <div class="text-sm text-gray-500">Rating ({{ $hostel->reviews_count ?? 0 }})</div>
            </div>
            <div class="stat-card rounded-xl p-4 text-center">
                <i class="fas fa-tags text-green-500 text-2xl mb-2"></i>
                <div class="text-2xl font-bold text-gray-800">{{ count($hostel->amenities ?? []) }}</div>
                <div class="text-sm text-gray-500">Amenities</div>
            </div>
        </div>

        <!-- Description -->
        <div class="mt-8">
            <h2 class="text-xl font-bold text-gray-900 mb-3 flex items-center">
                <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                About This Hostel
            </h2>
            <div class="bg-gray-50 rounded-xl p-6">
                <p class="text-gray-700 leading-relaxed">
                    {{ $hostel->description ?? 'No description available.' }}
                </p>
            </div>
        </div>

        <!-- Amenities -->
        @if($hostel->amenities && count($hostel->amenities) > 0)
        <div class="mt-8">
            <h2 class="text-xl font-bold text-gray-900 mb-3 flex items-center">
                <i class="fas fa-concierge-bell text-blue-500 mr-2"></i>
                Amenities & Facilities
            </h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                @foreach($hostel->amenities as $amenity)
                    <div class="amenity-tag bg-gray-50 rounded-xl px-4 py-3 flex items-center space-x-3 hover:bg-blue-50 cursor-default">
                        <i class="fas fa-check-circle text-green-500 text-lg"></i>
                        <span class="text-gray-700 text-sm font-medium">{{ ucwords(str_replace('_', ' ', $amenity)) }}</span>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Location Map Placeholder -->
        <div class="mt-8">
            <h2 class="text-xl font-bold text-gray-900 mb-3 flex items-center">
                <i class="fas fa-map-marked-alt text-red-500 mr-2"></i>
                Location
            </h2>
            <div class="bg-gray-200 rounded-xl h-48 flex items-center justify-center">
                <div class="text-center text-gray-500">
                    <i class="fas fa-map fa-3x mb-2 opacity-50"></i>
                    <p>Map View Coming Soon</p>
                    <p class="text-sm">{{ $hostel->location }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Available Rooms Section -->
<div class="bg-white rounded-2xl shadow-lg p-8">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                <i class="fas fa-door-open text-blue-500 mr-3"></i>
                Available Rooms
            </h2>
            <p class="text-gray-500 mt-1">{{ $availableRooms->count() }} rooms available for booking</p>
        </div>
        <div class="flex items-center space-x-2">
            <button class="px-4 py-2 bg-gray-100 rounded-lg text-sm text-gray-600 hover:bg-gray-200 transition">
                <i class="fas fa-sort-amount-down mr-1"></i> Sort
            </button>
            <button class="px-4 py-2 bg-gray-100 rounded-lg text-sm text-gray-600 hover:bg-gray-200 transition">
                <i class="fas fa-filter mr-1"></i> Filter
            </button>
        </div>
    </div>
    
    @if($availableRooms && $availableRooms->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 room-grid">
            @foreach($availableRooms as $room)
                <div class="room-card bg-white rounded-2xl overflow-hidden">
                    <!-- Room Image -->
                    <div class="relative h-56 bg-gray-100">
                        @if($room->images && $room->images->count() > 0)
                            <img src="{{ image_url($room->images->first()->image_path) }}" 
                                 alt="Room {{ $room->number }}"
                                 class="w-full h-full object-cover">
                            
                            @if($room->images->count() > 1)
                                <div class="absolute top-3 right-3 bg-black/60 backdrop-blur-sm text-white text-xs px-2 py-1 rounded-full flex items-center">
                                    <i class="fas fa-images mr-1"></i> {{ $room->images->count() }}
                                </div>
                            @endif
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                                <i class="fas fa-door-open text-gray-400 text-5xl"></i>
                            </div>
                        @endif
                        
                        <!-- Room Number Badge -->
                        <div class="absolute top-3 left-3 bg-blue-600 text-white text-sm font-semibold px-4 py-2 rounded-full shadow-lg">
                            <i class="fas fa-hashtag mr-1"></i> {{ $room->number }}
                        </div>
                        
                        <!-- Availability Badge -->
                        <div class="absolute bottom-3 right-3">
                            @php
                                $availableSpaces = $room->availableSpaces();
                            @endphp
                            <span class="availability-badge px-4 py-2 rounded-full text-sm font-semibold shadow-lg 
                                {{ $availableSpaces > 0 ? 'bg-green-500 text-white' : 'bg-red-500 text-white' }}">
                                <i class="fas fa-{{ $availableSpaces > 0 ? 'check' : 'times' }}-circle mr-1"></i>
                                {{ $availableSpaces > 0 ? $availableSpaces . ' space' . ($availableSpaces != 1 ? 's' : '') . ' left' : 'Full' }}
                            </span>
                        </div>

                        <!-- Gender & Floor Badges -->
                        <div class="absolute bottom-3 left-3 flex gap-2">
                            <span class="bg-purple-600/90 backdrop-blur-sm text-white text-xs px-3 py-1 rounded-full">
                                <i class="fas fa-venus-mars mr-1"></i> {{ ucfirst($room->gender ?? 'Mixed') }}
                            </span>
                            <span class="bg-gray-600/90 backdrop-blur-sm text-white text-xs px-3 py-1 rounded-full">
                                <i class="fas fa-layer-group mr-1"></i> {{ $room->floor ?? 'G' }}
                            </span>
                        </div>
                    </div>

                    <!-- Room Details -->
                    <div class="p-5">
                        <!-- Features & Amenities -->
                        <div class="flex flex-wrap gap-2 mb-4">
                            @if($room->furnished)
                                <span class="feature-tag text-xs px-3 py-1 rounded-full flex items-center">
                                    <i class="fas fa-couch text-blue-500 mr-1"></i> Furnished
                                </span>
                            @endif
                            @if($room->private_bathroom)
                                <span class="feature-tag text-xs px-3 py-1 rounded-full flex items-center">
                                    <i class="fas fa-shower text-purple-500 mr-1"></i> Private Bath
                                </span>
                            @endif
                            @if($room->size_sqm)
                                <span class="feature-tag text-xs px-3 py-1 rounded-full flex items-center">
                                    <i class="fas fa-ruler-combined text-green-500 mr-1"></i> {{ $room->size_sqm }} m²
                                </span>
                            @endif
                            @if($room->window)
                                <span class="feature-tag text-xs px-3 py-1 rounded-full flex items-center">
                                    <i class="fas fa-window-maximize text-yellow-500 mr-1"></i> Window
                                </span>
                            @endif
                        </div>

                        <!-- Room Specs -->
                        <div class="grid grid-cols-3 gap-2 mb-4">
                            <div class="text-center bg-gray-50 rounded-lg p-2">
                                <i class="fas fa-users text-blue-500 text-sm"></i>
                                <div class="font-semibold text-gray-800 text-sm">{{ $room->capacity }}</div>
                                <div class="text-xs text-gray-500">Beds</div>
                            </div>
                            <div class="text-center bg-gray-50 rounded-lg p-2">
                                <i class="fas fa-bed text-green-500 text-sm"></i>
                                <div class="font-semibold text-gray-800 text-sm">{{ $availableSpaces }}</div>
                                <div class="text-xs text-gray-500">Available</div>
                            </div>
                            <div class="text-center bg-gray-50 rounded-lg p-2">
                                <i class="fas fa-calendar-check text-purple-500 text-sm"></i>
                                <div class="font-semibold text-gray-800 text-sm">{{ $room->academic_year ?? '2024' }}</div>
                                <div class="text-xs text-gray-500">Year</div>
                            </div>
                        </div>

                        <!-- Price & Booking -->
                        <div class="pt-4 border-t">
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <span class="text-xs text-gray-500">Academic Year</span>
                                    @if(!empty($room->room_cost) && $room->room_cost > 0)
                                        <div class="text-2xl font-bold text-blue-600">
                                            ₵{{ number_format($room->room_cost, 2) }}
                                        </div>
                                        <div class="text-xs text-gray-400">
                                            ₵{{ number_format($room->room_cost / 12, 2) }}/month
                                        </div>
                                    @else
                                        <div class="text-gray-400 text-sm">Price not set</div>
                                    @endif
                                </div>
                                <a href="{{ route('bookings.create.student', ['hostel' => $hostel->id, 'room' => $room->id]) }}" 
                                   class="book-btn px-6 py-3 bg-blue-600 text-white rounded-xl font-medium text-sm flex items-center">
                                    <i class="fas fa-calendar-check mr-2"></i>
                                    Book Now
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Load More / Pagination -->
        <div class="mt-8 text-center">
            <button class="px-8 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition font-medium">
                <i class="fas fa-sync-alt mr-2"></i>
                Load More Rooms
            </button>
        </div>
    @else
        <div class="text-center py-16">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-bed text-gray-400 text-4xl"></i>
            </div>
            <h3 class="text-2xl font-semibold text-gray-700 mb-2">No Rooms Available</h3>
            <p class="text-gray-500 max-w-md mx-auto mb-6">
                All rooms in this hostel are currently booked. Check back later or browse other hostels.
            </p>
            <a href="{{ route('student.hostels.browse') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition font-medium">
                <i class="fas fa-arrow-left mr-2"></i>
                Browse Other Hostels
            </a>
        </div>
    @endif
</div>

<!-- Related Hostels Section -->
<div class="mt-8 bg-white rounded-2xl shadow-lg p-8">
    <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
        <i class="fas fa-building text-blue-500 mr-3"></i>
        You Might Also Like
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @php
            $relatedHostels = \App\Models\Hostel::where('id', '!=', $hostel->id)
                                                ->where('status', 'active')
                                                ->take(3)
                                                ->get();
        @endphp
        @forelse($relatedHostels as $related)
            <div class="border rounded-xl overflow-hidden hover:shadow-lg transition group">
                <div class="h-48 bg-gray-200 relative">
                    @if($related->images && $related->images->count() > 0)
                        <img src="{{ image_url($related->images->first()->image_path) }}" 
                             alt="{{ $related->name }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                    @else
                        <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                            <i class="fas fa-building text-gray-400 text-4xl"></i>
                        </div>
                    @endif
                    <div class="absolute top-3 right-3 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-sm font-semibold text-blue-600">
                        ₵{{ number_format($related->min_price ?? 0, 0) }}
                    </div>
                </div>
                <div class="p-4">
                    <h3 class="font-semibold text-gray-900">{{ $related->name }}</h3>
                    <p class="text-sm text-gray-500 mt-1">
                        <i class="fas fa-map-marker-alt text-red-400 mr-1"></i>
                        {{ $related->location }}
                    </p>
                    <a href="{{ route('student.hostels.show', $related) }}" 
                       class="mt-3 inline-block text-blue-600 hover:text-blue-800 font-medium text-sm">
                        View Details <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-3 text-center text-gray-500 py-8">
                <i class="fas fa-building text-4xl mb-3 opacity-30"></i>
                <p>No related hostels found</p>
            </div>
        @endforelse
    </div>
</div>

<!-- Floating Quick Action -->
<div class="fixed bottom-6 right-6 z-50">
    <div x-data="{ open: false }" class="relative">
        <button @click="open = !open" 
                class="bg-blue-600 text-white p-4 rounded-full shadow-lg hover:bg-blue-700 transition transform hover:scale-110">
            <i class="fas fa-plus text-xl" :class="open ? 'fa-times' : 'fa-plus'"></i>
        </button>
        
        <div x-show="open" 
             @click.outside="open = false"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="transform opacity-0 scale-95"
             x-transition:enter-end="transform opacity-100 scale-100"
             class="absolute bottom-16 right-0 bg-white rounded-2xl shadow-xl p-2 w-56"
             style="display: none;">
            <a href="{{ route('student.hostels.browse') }}" class="no-loader flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 rounded-xl transition group">
                <i class="fas fa-search text-blue-500 w-6 group-hover:scale-110 transition"></i>
                <span class="ml-3">Browse Hostels</span>
            </a>
            <a href="#" class="no-loader flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 rounded-xl transition group">
                <i class="fas fa-heart text-red-500 w-6 group-hover:scale-110 transition"></i>
                <span class="ml-3">Saved Hostels</span>
            </a>
            <a href="{{ route('student.bookings') }}" class="no-loader flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 rounded-xl transition group">
                <i class="fas fa-calendar-check text-green-500 w-6 group-hover:scale-110 transition"></i>
                <span class="ml-3">My Bookings</span>
            </a>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Optional: Add smooth scroll behavior
    document.addEventListener('DOMContentLoaded', function() {
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Image lazy loading
        const images = document.querySelectorAll('img[data-src]');
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                    observer.unobserve(img);
                }
            });
        });

        images.forEach(img => imageObserver.observe(img));
    });
</script>
@endsection