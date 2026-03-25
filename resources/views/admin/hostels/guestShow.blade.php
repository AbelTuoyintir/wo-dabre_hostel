@extends('layouts.home')

@section('title', 'UCC Hostel Booking System')

@section('content')
<div class="bg-gray-50">

    <!-- Back Button -->
    <div class="container mx-auto px-4 py-4">
        <a href="{{ url('/') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-left mr-2"></i> Back to Hostels
        </a>
    </div>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-4">
        <!-- Alert Messages -->
        @if(session('success'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
            </div>
        @endif

        <!-- Hostel Details -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-8">
            <!-- Image Gallery -->
            <div class="relative h-96 bg-gray-900">
                @if($hostel->images && $hostel->images->count() > 0)
                    <div x-data="{ activeImage: 0, images: {{ json_encode($hostel->images->pluck('image_path')) }} }" class="relative h-full">
                        <img :src="'{{ Storage::url('') }}' + images[activeImage]" 
                             alt="{{ $hostel->name }}" 
                             class="w-full h-full object-cover">
                        
                        @if($hostel->images->count() > 1)
                            <!-- Navigation Buttons -->
                            <button @click="activeImage = activeImage > 0 ? activeImage - 1 : images.length - 1"
                                    class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-75">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <button @click="activeImage = activeImage < images.length - 1 ? activeImage + 1 : 0"
                                    class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-75">
                                <i class="fas fa-chevron-right"></i>
                            </button>

                            <!-- Thumbnail Navigation -->
                            <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2">
                                <template x-for="(image, index) in images" :key="index">
                                    <button @click="activeImage = index"
                                            class="w-2 h-2 rounded-full transition-all duration-200"
                                            :class="activeImage === index ? 'bg-white w-4' : 'bg-white bg-opacity-50'">
                                    </button>
                                </template>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                        <i class="fas fa-building text-gray-400 text-6xl"></i>
                    </div>
                @endif
            </div>

            <!-- Hostel Info -->
            <div class="p-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ $hostel->name }}</h1>
                        <p class="text-gray-600 mt-2">
                            <i class="fas fa-map-marker-alt text-red-500 mr-2"></i>{{ $hostel->location }}
                        </p>
                    </div>
                    {{-- <div class="text-right">
                        <div class="bg-blue-100 px-4 py-2 rounded-lg">
                            <p class="text-sm text-gray-600">Starting from</p>
                            <p class="text-2xl font-bold text-blue-600">₵{{ number_format($hostel->min_price ?? 0, 2) }}</p>
                            <p class="text-xs text-gray-500">per academic year</p>
                        </div>
                    </div> --}}
                </div>

                <!-- Rating -->
                @if($hostel->rating > 0)
                <div class="flex items-center mb-4">
                    <div class="flex items-center mr-4">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= $hostel->rating)
                                <i class="fas fa-star text-yellow-400"></i>
                            @else
                                <i class="far fa-star text-gray-300"></i>
                            @endif
                        @endfor
                    </div>
                    <span class="text-gray-600">{{ number_format($hostel->rating, 1) }} ({{ $hostel->reviews_count ?? 0 }} reviews)</span>
                </div>
                @endif

                <!-- Description -->
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">About this Hostel</h2>
                    <p class="text-gray-600 leading-relaxed">{{ $hostel->description ?? 'No description available.' }}</p>
                </div>

                <!-- Amenities -->
                @if($hostel->amenities && count($hostel->amenities) > 0)
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">Amenities</h2>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        @foreach($hostel->amenities as $amenity)
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                <span>{{ ucwords(str_replace('_', ' ', $amenity)) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Contact Info -->
                @if($hostel->phone || $hostel->email)
                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">Contact Information</h2>
                    <div class="space-y-2">
                        @if($hostel->phone)
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-phone text-blue-500 w-5 mr-3"></i>
                            <span>{{ $hostel->phone }}</span>
                        </div>
                        @endif
                        @if($hostel->email)
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-envelope text-blue-500 w-5 mr-3"></i>
                            <span>{{ $hostel->email }}</span>
                        </div>
                        @endif
                        @if($hostel->address)
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-map-pin text-blue-500 w-5 mr-3"></i>
                            <span>{{ $hostel->address }}</span>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Available Rooms Section -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Available Rooms</h2>
            
            @if($availableRooms && $availableRooms->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($availableRooms as $room)
                        <div class="border rounded-lg p-4 hover:shadow-lg transition">
                            <!-- Room Image -->
                            <div class="relative h-40 mb-4 -mx-4 -mt-4 rounded-t-lg overflow-hidden bg-gray-100">
                                 @if($room->roomImages && $room->roomImages->count() > 0)
                                    @php
                                        $firstImage = $room->roomImages->first();
                                        $imagePath = $firstImage->image_path ?? $firstImage->path ?? null;
                                    @endphp
                                    @if($imagePath)
                                        <img src="{{ Storage::url($imagePath) }}" 
                                            alt="Room {{ $room->number }}"
                                            class="w-full h-full object-cover"
                                            onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'w-full h-full flex items-center justify-center\'><i class=\'fas fa-door-open text-gray-400 text-4xl\'></i></div>';">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <i class="fas fa-door-open text-gray-400 text-4xl"></i>
                                        </div>
                                    @endif
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <i class="fas fa-door-open text-gray-400 text-4xl"></i>
                                    </div>
                                @endif                                <div class="absolute top-2 left-2 bg-blue-600 text-white text-sm font-semibold px-3 py-1 rounded-full">
                                    Room {{ $room->number }}
                                </div>
                                <div class="absolute bottom-2 right-2">
                                    <span class="bg-green-500 text-white text-xs px-3 py-1 rounded-full shadow-lg">
                                        {{ $room->availableSpaces() }} space{{ $room->availableSpaces() != 1 ? 's' : '' }} left
                                    </span>
                                </div>
                            </div>

                            <!-- Room Details -->
                            <div class="space-y-3">
                                <div class="grid grid-cols-2 gap-2 text-sm">
                                    <div class="bg-gray-50 p-2 rounded text-center">
                                        <i class="fas fa-users text-blue-500 block mb-1"></i>
                                        <span class="font-medium">{{ $room->capacity }}</span>
                                        <span class="text-gray-500"> beds</span>
                                    </div>
                                    <div class="bg-gray-50 p-2 rounded text-center">
                                        <i class="fas fa-venus-mars text-purple-500 block mb-1"></i>
                                        <span class="font-medium">{{ ucfirst($room->gender) }}</span>
                                    </div>
                                </div>

                                <!-- Features -->
                                <div class="flex flex-wrap gap-2">
                                    @if($room->furnished)
                                        <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full">
                                            <i class="fas fa-couch mr-1"></i>Furnished
                                        </span>
                                    @endif
                                    @if($room->private_bathroom)
                                        <span class="text-xs bg-purple-100 text-purple-700 px-2 py-1 rounded-full">
                                            <i class="fas fa-shower mr-1"></i>Private Bath
                                        </span>
                                    @endif
                                    @if($room->size_sqm)
                                        <span class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded-full">
                                            {{ $room->size_sqm }} m²
                                        </span>
                                    @endif
                                </div>

                                <!-- Price and Book Button -->
                                <div class="mt-4 pt-4 border-t">
                                    <div class="flex justify-between items-center mb-3">
                                        <span class="text-gray-600">Price</span>
                                        <span class="text-xl font-bold text-blue-600">₵{{ number_format($room->room_cost, 2) }}</span>
                                    </div>
                                    
                                    @auth
                                        @if(auth()->user()->role === 'student')
                                            <a href="{{ route('bookings.create.student', ['hostel' => $hostel->id, 'room' => $room->id]) }}" 
                                               class="block w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition text-center">
                                                <i class="fas fa-calendar-check mr-2"></i> Book Now
                                            </a>
                                        @else
                                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 text-center">
                                                <p class="text-sm text-yellow-700">Please login as a student to book</p>
                                                <a href="{{ route('login') }}" class="text-blue-600 hover:underline text-sm font-medium">
                                                    Login here
                                                </a>
                                            </div>
                                        @endif
                                    @else
                                        <div class="space-y-2">
                                            <a href="{{ route('bookings.create', ['hostel' => $hostel->id, 'room' => $room->id]) }}" 
                                               class="block w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition text-center">
                                                <i class="fas fa-calendar-check mr-2"></i> Book as Guest
                                            </a>
                                            <p class="text-xs text-center text-gray-500">
                                                <i class="fas fa-info-circle mr-1"></i>
                                                Guest booking? Account will be created after payment
                                            </p>
                                        </div>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-bed text-gray-300 text-5xl mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">No Rooms Available</h3>
                    <p class="text-gray-500">All rooms in this hostel are currently booked.</p>
                    <a href="{{ url('/') }}" class="inline-block mt-4 text-blue-600 hover:text-blue-800">
                        <i class="fas fa-arrow-left mr-2"></i> Browse Other Hostels
                    </a>
                </div>
            @endif
        </div>

        <!-- Similar Hostels -->
        @if(isset($relatedHostels) && $relatedHostels->count() > 0)
        <div class="bg-white rounded-lg shadow-lg p-6 mt-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Similar Hostels Nearby</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($relatedHostels as $similar)
                    <a href="{{ route('hostels.show', $similar) }}" class="block border rounded-lg p-4 hover:shadow-lg transition">
                        <div class="flex items-center space-x-3">
                            @if($similar->primaryImage)
                                <img src="{{ Storage::url($similar->primaryImage->image_path) }}" 
                                     alt="{{ $similar->name }}" 
                                     class="w-16 h-16 object-cover rounded-lg">
                            @else
                                <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-building text-gray-400"></i>
                                </div>
                            @endif
                            <div>
                                <h3 class="font-semibold text-gray-800">{{ $similar->name }}</h3>
                                <p class="text-sm text-gray-500">{{ $similar->location }}</p>
                                <p class="text-sm font-bold text-blue-600 mt-1">
                                    ₵{{ number_format($similar->min_price ?? 0, 2) }}/year
                                </p>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
        @endif
    </main>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</div>
@endsection
