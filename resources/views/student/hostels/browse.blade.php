@extends('layouts.student')

@section('title', $hostel->name)
@section('content')
<div class="space-y-6">
    <!-- Back Button -->
    <a href="{{ route('student.hostels.browse') }}" class="inline-flex items-center text-gray-600 hover:text-gray-800">
        <i class="fas fa-arrow-left mr-2"></i>
        Back to Hostels
    </a>

    <!-- Hostel Details -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <!-- Image Gallery -->
        <div class="relative h-96 bg-gray-900">
            @if($hostel->images->count() > 0)
                <div x-data="{ activeImage: 0 }" class="relative h-full">
                    <!-- Main Image -->
                    <img src="{{ Storage::url($hostel->images[0]->path) }}"
                         alt="{{ $hostel->name }}"
                         class="w-full h-full object-cover">

                    <!-- Image Navigation -->
                    @if($hostel->images->count() > 1)
                        <button @click="activeImage = activeImage > 0 ? activeImage - 1 : {{ $hostel->images->count() - 1 }}"
                                class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-75">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button @click="activeImage = activeImage < {{ $hostel->images->count() - 1 }} ? activeImage + 1 : 0"
                                class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-75">
                            <i class="fas fa-chevron-right"></i>
                        </button>

                        <!-- Thumbnails -->
                        <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2">
                            @foreach($hostel->images as $index => $image)
                                <button @click="activeImage = {{ $index }}"
                                        class="w-2 h-2 rounded-full transition-all duration-200"
                                        :class="activeImage === {{ $index }} ? 'bg-white w-4' : 'bg-white bg-opacity-50'">
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>
            @else
                <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                    <i class="fas fa-building text-gray-400 text-6xl"></i>
                </div>
            @endif
        </div>

        <!-- Content -->
        <div class="p-6">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">{{ $hostel->name }}</h1>
                    <p class="text-gray-600 mt-1">
                        <i class="fas fa-map-marker-alt text-gray-400 mr-1"></i>
                        {{ $hostel->location }}
                    </p>
                </div>
                <div class="flex items-center bg-yellow-50 px-3 py-1 rounded-full">
                    <i class="fas fa-star text-yellow-400 mr-1"></i>
                    <span class="font-semibold">{{ number_format($averageRating, 1) }}</span>
                    <span class="text-gray-500 text-sm ml-1">({{ $reviewCount }} reviews)</span>
                </div>
            </div>

            <!-- Quick Info -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-gray-50 p-3 rounded-lg text-center">
                    <i class="fas fa-bed text-blue-600 text-xl mb-1"></i>
                    <p class="text-sm text-gray-600">Total Rooms</p>
                    <p class="text-lg font-semibold">{{ $hostel->rooms->count() }}</p>
                </div>
                <div class="bg-gray-50 p-3 rounded-lg text-center">
                    <i class="fas fa-door-open text-green-600 text-xl mb-1"></i>
                    <p class="text-sm text-gray-600">Available</p>
                    <p class="text-lg font-semibold">{{ $availableRooms->count() }}</p>
                </div>
                <div class="bg-gray-50 p-3 rounded-lg text-center">
                    <i class="fas fa-tag text-purple-600 text-xl mb-1"></i>
                    <p class="text-sm text-gray-600">Starting From</p>
                    <p class="text-lg font-semibold">₵{{ number_format($hostel->min_price ?? 0, 2) }}</p>
                </div>
                <div class="bg-gray-50 p-3 rounded-lg text-center">
                    <i class="fas fa-shield-alt text-orange-600 text-xl mb-1"></i>
                    <p class="text-sm text-gray-600">Security</p>
                    <p class="text-lg font-semibold">24/7</p>
                </div>
            </div>

            <!-- Description -->
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-3">About this Hostel</h2>
                <p class="text-gray-600 leading-relaxed">{{ $hostel->description }}</p>
            </div>

            <!-- Amenities -->
            @if($hostel->amenities && count($hostel->amenities) > 0)
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-3">Amenities</h2>
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

            <!-- Available Rooms -->
            @if($availableRooms->count() > 0)
            <div>
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Available Rooms</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($availableRooms as $room)
                        <div class="border rounded-lg p-4 hover:shadow-lg transition">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="font-semibold text-gray-800">Room {{ $room->number }}</h3>
                                <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full">
                                    {{ $room->availableSpaces() }} left
                                </span>
                            </div>
                            <p class="text-sm text-gray-500 mb-2">Capacity: {{ $room->capacity }} persons</p>
                            <p class="text-sm text-gray-500 mb-3">Gender: {{ ucfirst($room->gender) }}</p>
                            <div class="flex items-end justify-between mt-3 pt-3 border-t">
                                <div>
                                    <span class="text-xl font-bold text-blue-600">₵{{ number_format($room->price_per_month, 2) }}</span>
                                    <span class="text-xs text-gray-500">/month</span>
                                </div>
                                <a href="{{ route('bookings.create', ['hostel' => $hostel->id, 'room' => $room->id]) }}"
                                   class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">
                                    Book Now
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @else
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
                <i class="fas fa-bed text-yellow-500 text-4xl mb-3"></i>
                <h3 class="text-lg font-semibold text-yellow-700 mb-1">No Rooms Available</h3>
                <p class="text-yellow-600">All rooms in this hostel are currently booked. Please check back later.</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Similar Hostels -->
    @if($similarHostels->count() > 0)
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Similar Hostels Nearby</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($similarHostels as $similar)
                <a href="{{ route('student.hostels.show', $similar) }}" class="block border rounded-lg p-4 hover:shadow-lg transition">
                    <div class="flex items-center space-x-3">
                        @if($similar->primaryImage)
                            <img src="{{ Storage::url($similar->primaryImage->path) }}"
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
                            <p class="text-sm font-bold text-blue-600 mt-1">₵{{ number_format($similar->min_price ?? 0, 2) }}/mo</p>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
