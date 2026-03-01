@extends('layouts.student')

@section('title', $hostel->name)
@section('content')

<!-- Back Button -->
<div class="mb-4">
    <a href="{{ route('student.hostels.browse') }}" class="text-gray-600 hover:text-gray-800">
        <i class="fas fa-arrow-left mr-2"></i>Back to Hostels
    </a>
</div>

<!-- Hostel Details -->
<div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
    <!-- Hostel Image Gallery -->
    <div class="relative h-96 bg-gray-900">
        @if($hostel->images && $hostel->images->count() > 0)
            <div x-data="{ activeImage: 0 }" class="relative h-full">
                <img src="{{ Storage::url($hostel->images[0]->path) }}" 
                     alt="{{ $hostel->name }}" 
                     class="w-full h-full object-cover">
                
                @if($hostel->images->count() > 1)
                    <button @click="activeImage = activeImage > 0 ? activeImage - 1 : {{ $hostel->images->count() - 1 }}"
                            class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-75">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button @click="activeImage = activeImage < {{ $hostel->images->count() - 1 }} ? activeImage + 1 : 0"
                            class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-75">
                        <i class="fas fa-chevron-right"></i>
                    </button>

                    <!-- Thumbnail Navigation -->
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

    <!-- Hostel Info -->
    <div class="p-6">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $hostel->name }}</h1>
                <p class="text-gray-600 mt-2">
                    <i class="fas fa-map-marker-alt text-red-500 mr-2"></i>{{ $hostel->location }}
                </p>
            </div>
            <div class="bg-blue-100 px-4 py-2 rounded-lg">
                <span class="text-2xl font-bold text-blue-600">₵{{ number_format((float) ($hostel->min_price ?? 0), 2) }}</span>
                <span class="text-sm text-gray-500">/year</span>
            </div>
        </div>

        <!-- Rating -->
        @if($hostel->rating > 0)
        <div class="flex items-center mt-4">
            <div class="flex items-center mr-4">
                @for($i = 1; $i <= 5; $i++)
                    @if($i <= $hostel->rating)
                        <i class="fas fa-star text-yellow-400"></i>
                    @else
                        <i class="far fa-star text-gray-300"></i>
                    @endif
                @endfor
            </div>
            <span class="text-gray-600">{{ number_format($hostel->rating, 1) }} ({{ $hostel->reviews_count }} reviews)</span>
        </div>
        @endif

        <!-- Description -->
        <div class="mt-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-3">About this Hostel</h2>
            <p class="text-gray-600 leading-relaxed">{{ $hostel->description ?? 'No description available.' }}</p>
        </div>

        <!-- Amenities -->
        @if($hostel->amenities && count($hostel->amenities) > 0)
        <div class="mt-6">
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
    </div>
</div>

<!-- Available Rooms Section -->
<div class="bg-white rounded-lg shadow-sm p-6">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">Available Rooms</h2>
    
    @if($availableRooms && $availableRooms->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($availableRooms as $room)
                <div class="border rounded-lg p-4 hover:shadow-md transition">
                    <!-- Room Image -->
                    <div class="relative h-48 mb-4 -mx-4 -mt-4 rounded-t-lg overflow-hidden">
                        @if($room->images && $room->images->count() > 0)
                            <img src="{{ Storage::url($room->images->first()->path) }}" 
                                 alt="Room {{ $room->number }}"
                                 class="w-full h-full object-cover">
                            
                            @if($room->images->count() > 1)
                                <div class="absolute top-2 right-2 bg-black bg-opacity-50 text-white text-xs px-2 py-1 rounded-full">
                                    {{ $room->images->count() }} photos
                                </div>
                            @endif
                        @else
                            <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                <i class="fas fa-door-open text-gray-400 text-4xl"></i>
                            </div>
                        @endif
                        
                        <!-- Room Number Badge -->
                        <div class="absolute top-2 left-2 bg-blue-600 text-white text-sm font-semibold px-3 py-1 rounded-full">
                            Room {{ $room->number }}
                        </div>
                        
                        <!-- Availability Badge -->
                        <div class="absolute bottom-2 right-2">
                            <span class="bg-green-500 text-white text-xs px-3 py-1 rounded-full shadow-lg">
                                {{ $room->availableSpaces() }} space{{ $room->availableSpaces() != 1 ? 's' : '' }} left
                            </span>
                        </div>
                    </div>

                    <!-- Room Details -->
                    <div class="space-y-3">
                        <!-- Quick Info Grid -->
                        <div class="grid grid-cols-3 gap-2 text-center text-xs">
                            <div class="bg-gray-50 p-2 rounded">
                                <i class="fas fa-users text-blue-500 block mb-1"></i>
                                <span class="font-medium">{{ $room->capacity }}</span>
                                <span class="text-gray-500"> beds</span>
                            </div>
                            <div class="bg-gray-50 p-2 rounded">
                                <i class="fas fa-venus-mars text-purple-500 block mb-1"></i>
                                <span class="font-medium">{{ ucfirst(substr($room->gender, 0, 1)) }}</span>
                                <span class="text-gray-500"> only</span>
                            </div>
                            <div class="bg-gray-50 p-2 rounded">
                                <i class="fas fa-layer-group text-green-500 block mb-1"></i>
                                <span class="font-medium">{{ $room->floor ?? 'G' }}</span>
                                <span class="text-gray-500"> floor</span>
                            </div>
                        </div>

                        <!-- Features -->
                        <div class="flex flex-wrap gap-2">
                            @if($room->furnished)
                                <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full flex items-center">
                                    <i class="fas fa-couch mr-1 text-xs"></i>Furnished
                                </span>
                            @endif
                            @if($room->private_bathroom)
                                <span class="text-xs bg-purple-100 text-purple-700 px-2 py-1 rounded-full flex items-center">
                                    <i class="fas fa-shower mr-1 text-xs"></i>Private Bath
                                </span>
                            @endif
                            @if($room->size_sqm)
                                <span class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded-full flex items-center">
                                    <i class="fas fa-ruler-combined mr-1 text-xs"></i>{{ $room->size_sqm }} m²
                                </span>
                            @endif
                        </div>

                        <!-- Room Images Thumbnails (if multiple) -->
                        @if($room->images && $room->images->count() > 1)
                        <div class="flex -space-x-2 mt-2">
                            @foreach($room->images->take(3) as $index => $image)
                                @if($index > 0)
                                    <div class="w-8 h-8 rounded-full border-2 border-white overflow-hidden bg-gray-200">
                                        <img src="{{ Storage::url($image->path) }}" 
                                             alt="Room thumbnail"
                                             class="w-full h-full object-cover">
                                    </div>
                                @endif
                            @endforeach
                            @if($room->images->count() > 4)
                                <div class="w-8 h-8 rounded-full bg-gray-100 border-2 border-white flex items-center justify-center text-xs font-medium text-gray-600">
                                    +{{ $room->images->count() - 3 }}
                                </div>
                            @endif
                        </div>
                        @endif

                        <!-- PRICE SECTION -->
                        <div class="mt-4 pt-4 border-t">
                            <div class="flex justify-between items-center mb-3">
                                <span class="text-gray-600 font-medium">Academic Year</span>
                                <div class="text-right">
                                    @if(!empty($room->room_cost) && $room->room_cost > 0)
                                        <span class="text-xl font-bold text-blue-600">₵{{ number_format($room->room_cost, 2) }}</span>
                                        <div class="text-xs text-gray-400">
                                            ₵{{ number_format($room->room_cost / 12, 2) }}/month
                                        </div>
                                    @else
                                        <span class="text-gray-400 text-sm">Price not set</span>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- BOOK BUTTON -->
                            @auth
                                <a href="{{ route('bookings.create.student', ['hostel' => $hostel->id, 'room' => $room->id]) }}" 
                                   class="block w-full px-4 py-3 bg-blue-600 text-white text-center rounded-lg hover:bg-blue-700 transition font-medium">
                                    <i class="fas fa-calendar-check mr-2"></i>Book This Room
                                </a>
                            @else
                                <a href="{{ route('bookings.create', ['hostel' => $hostel->id, 'room' => $room->id]) }}" 
                                   class="block w-full px-4 py-3 bg-blue-600 text-white text-center rounded-lg hover:bg-blue-700 transition font-medium">
                                    <i class="fas fa-calendar-check mr-2"></i>Book This Room
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-8">
            <i class="fas fa-bed text-gray-300 text-5xl mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">No Rooms Available</h3>
            <p class="text-gray-500">All rooms in this hostel are currently booked.</p>
            <a href="{{ route('student.hostels.browse') }}" class="inline-block mt-4 text-blue-600 hover:text-blue-800">
                <i class="fas fa-arrow-left mr-2"></i>Browse Other Hostels
            </a>
        </div>
    @endif
</div>
@endsection