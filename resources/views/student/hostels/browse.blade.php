@extends('layouts.student')

@section('title', 'Browse Hostels')
@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Hostels</h1>
            <p class="text-gray-600 mt-1">Find your perfect accommodation</p>
        </div>
    </div>

    <!-- Search Bar -->
    <form action="{{ route('student.hostels.browse') }}" method="GET" class="flex gap-2">
        <input type="text" 
               name="search" 
               placeholder="Search hostels by name or location..." 
               value="{{ request('search') }}"
               class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-search mr-2"></i>Search
        </button>
    </form>

    <!-- Hostels Grid -->
    @if($hostels->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($hostels as $hostel)
                <a href="{{ route('student.hostels.show', $hostel) }}" class="group bg-white rounded-lg shadow-sm hover:shadow-lg transition overflow-hidden">
                    <!-- Hostel Image -->
                    <div class="relative h-48 bg-gray-200 overflow-hidden">
                        @if($hostel->primaryImage)
                            <img src="{{ Storage::url($hostel->primaryImage->path) }}" 
                                 alt="{{ $hostel->name }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gray-100">
                                <i class="fas fa-building text-gray-400 text-4xl"></i>
                            </div>
                        @endif
                        
                        <!-- Rating Badge -->
                        <div class="absolute top-3 right-3 bg-white rounded-lg px-2 py-1 flex items-center space-x-1">
                            <i class="fas fa-star text-yellow-400 text-sm"></i>
                            <span class="font-semibold text-gray-800 text-sm">
                                {{ number_format($hostel->rating ?? 0, 1) }}
                            </span>
                        </div>
                    </div>

                    <!-- Hostel Info -->
                    <div class="p-4">
                        <h3 class="text-lg font-semibold text-gray-900 group-hover:text-blue-600 transition">
                            {{ $hostel->name }}
                        </h3>
                        
                        <div class="flex items-center text-gray-600 text-sm mt-2">
                            <i class="fas fa-map-marker-alt mr-2 text-red-500"></i>
                            <span>{{ $hostel->location }}</span>
                        </div>

                        <!-- Room Count -->
                        <div class="flex items-center text-gray-600 text-sm mt-2">
                            <i class="fas fa-door-open mr-2 text-blue-500"></i>
                            <span>{{ $hostel->available_rooms_count ?? 0 }} available rooms</span>
                        </div>

                        <!-- Price -->
                        <div class="mt-4 pt-4 border-t">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 text-sm">From</span>
                                <span class="text-xl font-bold text-blue-600">
                                    @if($hostel->min_price > 0)
                                        ₵{{ number_format($hostel->min_price, 2) }}
                                    @else
                                        <span class="text-gray-400 text-sm">Price N/A</span>
                                    @endif
                                </span>
                            </div>
                            <span class="text-xs text-gray-500">/month</span>
                        </div>

                        <!-- View Details Button -->
                        <div class="mt-4">
                            <span class="block w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium text-center cursor-pointer">
                                View Details
                            </span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-white rounded-lg shadow-sm p-12 text-center">
            <i class="fas fa-search text-gray-400 text-6xl mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">No Hostels Found</h3>
            <p class="text-gray-600 mb-6">
                @if(request('search'))
                    No hostels match your search: <strong>{{ request('search') }}</strong>
                @else
                    No hostels are currently available.
                @endif
            </p>
            @if(request('search'))
                <a href="{{ route('student.hostels.browse') }}" class="inline-block px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Clear Search
                </a>
            @endif
        </div>
    @endif
</div>
@endsection