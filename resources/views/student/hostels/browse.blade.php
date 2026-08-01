@extends('layouts.student')

@section('title', 'Find Your Hostel · Nest')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

    {{-- HERO SECTION --}}
    <div class="flex flex-wrap items-center justify-between mb-8">
        <div>
            <h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight text-slate-900 leading-tight">
                Find your nest <span class="text-blue-600">🏠</span>
            </h1>
            <p class="text-slate-500 text-lg mt-1 flex items-center gap-2">
                <i class="fas fa-map-pin text-blue-400"></i>
                curated hostels · verified comfort
            </p>
        </div>
        <div class="mt-4 sm:mt-0 bg-white/80 backdrop-blur-sm border border-slate-200/60 rounded-full px-5 py-2.5 shadow-sm flex items-center gap-3 text-sm font-medium text-slate-700">
            <i class="fas fa-star text-amber-400"></i>
            <span>4.8 avg rating</span>
            <span class="w-px h-5 bg-slate-300"></span>
            <i class="fas fa-building text-slate-400"></i>
            <span>12 partners</span>
        </div>
    </div>

    {{-- SEARCH & FILTER BAR (redesigned) --}}
    <div class="bg-white rounded-3xl shadow-md shadow-slate-200/40 border border-slate-200/60 p-1.5 mb-6 transition-all focus-within:shadow-blue-100/50 focus-within:border-blue-300">
        <form action="{{ route('student.hostels.browse') }}" method="GET" class="flex flex-col md:flex-row items-center gap-1.5">
            <div class="relative flex-1 w-full">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i class="fas fa-search text-slate-400 text-sm"></i>
                </div>
                <input
                    type="text"
                    name="search"
                    placeholder="Search by name, location, or amenities…"
                    value="{{ request('search') }}"
                    class="w-full pl-11 pr-4 py-3.5 bg-slate-50/70 border-0 rounded-2xl text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-blue-500/30 focus:bg-white transition-all"
                >
            </div>

            <div class="flex flex-wrap items-center gap-1.5 w-full md:w-auto">
                <button type="submit" class="flex-1 md:flex-none px-7 py-3 bg-slate-900 text-white font-semibold rounded-2xl hover:bg-slate-800 transition-all shadow-sm hover:shadow flex items-center justify-center gap-2">
                    <i class="fas fa-arrow-right text-sm"></i> Search
                </button>

                <button type="button" class="px-5 py-3 bg-slate-100 text-slate-700 font-medium rounded-2xl hover:bg-slate-200 transition-all flex items-center gap-2">
                    <i class="fas fa-sliders-h"></i> Filter
                </button>

                @if(request('search'))
                    <a href="{{ route('student.hostels.browse') }}" class="px-5 py-3 bg-white border border-slate-200 text-slate-600 font-medium rounded-2xl hover:bg-slate-50 transition-all flex items-center gap-2">
                        <i class="fas fa-undo-alt"></i> Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- FILTER CHIPS (quick filters) --}}
    <div class="flex flex-wrap items-center gap-2 mb-5">
        <span class="inline-flex items-center gap-1.5 px-4 py-1.5 bg-blue-50 text-blue-700 border border-blue-200/70 rounded-full text-sm font-medium">
            <i class="fas fa-check-circle text-blue-500 text-xs"></i> All
        </span>
        <span class="inline-flex items-center gap-1.5 px-4 py-1.5 bg-white border border-slate-200 rounded-full text-sm font-medium text-slate-600 hover:bg-slate-50 transition cursor-default">
            <i class="fas fa-wifi text-slate-400"></i> Wi-Fi
        </span>
        <span class="inline-flex items-center gap-1.5 px-4 py-1.5 bg-white border border-slate-200 rounded-full text-sm font-medium text-slate-600 hover:bg-slate-50 transition cursor-default">
            <i class="fas fa-utensils text-slate-400"></i> Meals
        </span>
        <span class="inline-flex items-center gap-1.5 px-4 py-1.5 bg-white border border-slate-200 rounded-full text-sm font-medium text-slate-600 hover:bg-slate-50 transition cursor-default">
            <i class="fas fa-dumbbell text-slate-400"></i> Gym
        </span>
        <span class="inline-flex items-center gap-1.5 px-4 py-1.5 bg-white border border-slate-200 rounded-full text-sm font-medium text-slate-600 hover:bg-slate-50 transition cursor-default">
            <i class="fas fa-water text-slate-400"></i> Laundry
        </span>
        <span class="inline-flex items-center gap-1.5 px-4 py-1.5 bg-white border border-slate-200 rounded-full text-sm font-medium text-slate-600 hover:bg-slate-50 transition cursor-default">
            <i class="fas fa-car text-slate-400"></i> Parking
        </span>
    </div>

    {{-- RESULTS COUNT & VIEW TOGGLES --}}
    @if($hostels->count() > 0)
        <div class="flex items-center justify-between mb-5">
            <p class="text-slate-600 text-sm">
                <span class="font-bold text-slate-800">{{ $hostels->count() }}</span> 
                hostel{{ $hostels->count() !== 1 ? 's' : '' }} available
            </p>
            <div class="flex items-center gap-2 text-slate-400">
                <i class="fas fa-grip text-slate-700"></i>
                <i class="fas fa-list-ul"></i>
            </div>
        </div>
    @endif

    {{-- HOSTEL GRID --}}
    @if($hostels->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            @foreach($hostels as $hostel)
                <div class="group bg-white rounded-3xl border border-slate-200/80 overflow-hidden shadow-sm hover:shadow-xl hover:shadow-slate-200/40 transition-all duration-300 flex flex-col hover:-translate-y-1.5">

                    {{-- Image + badges --}}
                    <div class="relative h-52 overflow-hidden bg-slate-100">
                        @if($hostel->primaryImage)
                            <img
                                src="{{ image_url($hostel->primaryImage->image_path) }}"
                                alt="{{ $hostel->name }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                            >
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-slate-100 to-slate-200/50">
                                <i class="fas fa-building text-slate-300 text-5xl"></i>
                            </div>
                        @endif

                        {{-- Badges --}}
                        <div class="absolute top-4 left-4 right-4 flex justify-between pointer-events-none">
                            @if($hostel->available_rooms_count > 0)
                                <span class="bg-emerald-500/90 backdrop-blur-sm text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-sm flex items-center gap-1.5">
                                    <i class="fas fa-door-open text-[10px]"></i>
                                    {{ $hostel->available_rooms_count }} available
                                </span>
                            @endif
                            <span class="bg-white/90 backdrop-blur-sm text-slate-800 text-xs font-bold px-3 py-1.5 rounded-full shadow-sm flex items-center gap-1.5">
                                <i class="fas fa-star text-amber-400 text-[10px]"></i>
                                {{ number_format($hostel->rating ?? 0, 1) }}
                            </span>
                        </div>
                    </div>

                    {{-- Content --}}
                    <div class="p-5 flex flex-col flex-1">
                        <a href="{{ route('student.hostels.show', $hostel) }}" class="block">
                            <h3 class="text-xl font-bold text-slate-800 hover:text-blue-700 transition-colors">{{ $hostel->name }}</h3>
                        </a>
                        <div class="flex items-center gap-1.5 text-slate-500 text-sm mt-0.5">
                            <i class="fas fa-map-pin text-slate-400 text-xs"></i>
                            {{ $hostel->location ?? 'London, UK' }}
                        </div>

                        {{-- Amenities --}}
                        <div class="flex flex-wrap gap-2 mt-3 text-sm text-slate-600">
                            @php
                                // The amenities relation returns a Collection of Amenity models.
                                // Pluck their names into a plain array, falling back to defaults when empty.
                                $amenityNames = $hostel->amenities
                                    ? collect($hostel->amenities)->pluck('name')->filter()->values()->all()
                                    : [];
                                $amenities = $amenityNames ?: ['Wi-Fi', 'Breakfast', 'En-suite'];
                                $icons = ['fa-wifi', 'fa-mug-saucer', 'fa-shower', 'fa-dumbbell', 'fa-water', 'fa-car'];
                            @endphp
                            @foreach(array_slice($amenities, 0, 3) as $index => $amenity)
                                <span class="flex items-center gap-1 bg-slate-50 px-2.5 py-1 rounded-full border border-slate-200/60">
                                    <i class="fas {{ $icons[$index % count($icons)] }} text-blue-500 text-[10px]"></i>
                                    {{ $amenity }}
                                </span>
                            @endforeach
                            @if(count($amenities) > 3)
                                <span class="text-slate-400 text-xs flex items-center">+{{ count($amenities) - 3 }}</span>
                            @endif
                        </div>

                        {{-- Footer --}}
                        <div class="flex items-center justify-between mt-4 pt-3 border-t border-slate-100">
            
                            <a
                                href="{{ route('student.hostels.show', $hostel) }}"
                                class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-50 text-blue-700 font-semibold text-sm rounded-full hover:bg-blue-100 transition-all"
                            >
                                View <i class="fas fa-arrow-right text-[10px]"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach

        </div>

        {{-- PAGINATION --}}
        @if($hostels->hasPages())
            <div class="mt-10 flex justify-center">
                {{ $hostels->links() }}
            </div>
        @endif

    @else
        {{-- EMPTY STATE --}}
        <div class="bg-white rounded-3xl border border-slate-200/70 p-12 text-center max-w-2xl mx-auto shadow-sm">
            <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-5">
                <i class="fas fa-search text-slate-400 text-3xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-slate-800 mb-1">No hostels found</h3>
            <p class="text-slate-500 max-w-sm mx-auto mb-6">
                @if(request('search'))
                    We couldn't find any hostels matching 
                    <span class="font-semibold text-slate-700">"{{ request('search') }}"</span>. 
                    Try adjusting your search.
                @else
                    There are no hostels available right now. Check back later!
                @endif
            </p>
            @if(request('search'))
                <a
                    href="{{ route('student.hostels.browse') }}"
                    class="inline-flex items-center gap-2 px-7 py-3 bg-slate-900 text-white font-semibold rounded-2xl hover:bg-slate-800 transition-all"
                >
                    <i class="fas fa-times"></i> Clear search
                </a>
            @endif
        </div>
    @endif

</div>
@endsection