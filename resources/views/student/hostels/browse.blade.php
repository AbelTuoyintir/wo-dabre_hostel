 @extends('layouts.student')

@section('title', 'Browse Hostels')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Header Section -->
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-slate-900 tracking-tight">Find Your Hostel</h1>
        <p class="text-slate-500 mt-2 text-lg">Discover comfortable accommodation that fits your needs</p>
    </div>

    <!-- Search & Filter Bar -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4 mb-8">
        <form action="{{ route('student.hostels.browse') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-slate-400"></i>
                </div>
                <input
                    type="text"
                    name="search"
                    placeholder="Search by name, location, or amenities..."
                    value="{{ request('search') }}"
                    class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                >
            </div>
            <button
                type="submit"
                class="px-8 py-3 bg-slate-900 text-white font-medium rounded-xl hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900 focus:ring-offset-2 transition-all"
            >
                Search
            </button>
            @if(request('search'))
                <a
                    href="{{ route('student.hostels.browse') }}"
                    class="px-6 py-3 bg-slate-100 text-slate-700 font-medium rounded-xl hover:bg-slate-200 transition-all text-center"
                >
                    Clear
                </a>
            @endif
        </form>
    </div>

    <!-- Results Count -->
    @if($hostels->count() > 0)
        <div class="flex items-center justify-between mb-6">
            <p class="text-slate-600">
                Showing <span class="font-semibold text-slate-900">{{ $hostels->count() }}</span> hostel{{ $hostels->count() !== 1 ? 's' : '' }}
            </p>
        </div>

        <!-- Hostels Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            @foreach($hostels as $hostel)
                <article class="group bg-white rounded-2xl border border-slate-200 overflow-hidden hover:shadow-xl hover:shadow-slate-200/50 transition-all duration-300 flex flex-col">
                    
                    <!-- Image Container -->
                    <div class="relative h-56 overflow-hidden bg-slate-100">
                        @if($hostel->primaryImage)
                            <img
                                src="{{ Storage::url($hostel->primaryImage->path) }}"
                                alt="{{ $hostel->name }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                            >
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-slate-100">
                                <i class="fas fa-building text-slate-300 text-5xl"></i>
                            </div>
                        @endif

                        <!-- Rating Badge -->
                        <div class="absolute top-4 right-4 bg-white/95 backdrop-blur-sm rounded-full px-3 py-1.5 flex items-center gap-1.5 shadow-sm">
                            <i class="fas fa-star text-amber-400 text-sm"></i>
                            <span class="font-bold text-slate-900 text-sm">
                                {{ number_format($hostel->rating ?? 0, 1) }}
                            </span>
                        </div>

                        <!-- Available Badge -->
                        @if($hostel->available_rooms_count > 0)
                            <div class="absolute top-4 left-4 bg-emerald-500 text-white rounded-full px-3 py-1.5 text-xs font-semibold shadow-sm">
                                {{ $hostel->available_rooms_count }} Available
                            </div>
                        @endif
                    </div>

                    <!-- Content -->
                    <div class="p-5 flex-1 flex flex-col">
                        <!-- Title & Location -->
                        <div class="mb-4">
                            <h3 class="text-xl font-bold text-slate-900 group-hover:text-blue-600 transition-colors line-clamp-1">
                                {{ $hostel->name }}
                            </h3>
                            <div class="flex items-center gap-2 mt-2 text-slate-500">
                                <i class="fas fa-map-marker-alt text-red-500 text-sm"></i>
                                <span class="text-sm">{{ $hostel->location }}</span>
                            </div>
                        </div>

                        <!-- Amenities Preview (Optional) -->
                        <div class="flex gap-2 mb-4">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-slate-100 text-slate-600 text-xs font-medium">
                                <i class="fas fa-wifi mr-1.5"></i> WiFi
                            </span>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-slate-100 text-slate-600 text-xs font-medium">
                                <i class="fas fa-shield-alt mr-1.5"></i> Security
                            </span>
                        </div>

                        <!-- Price Section -->
                        <div class="mt-auto pt-4 border-t border-slate-100">
                            <div class="flex items-end justify-between mb-4 mt-4 pt-4 border-t">
                                <div>
                                    <p class="text-xs text-slate-400 uppercase tracking-wider font-semibold mb-1">Starting from</p>
                                    @if($hostel->min_price > 0)
                                        <p class="text-2xl font-bold text-slate-900">
                                            ₵{{ number_format($hostel->min_price, 2) }}
                                        </p>
                                    @else
                                        <p class="text-lg font-medium text-slate-400">Contact for price</p>
                                    @endif
                                </div>
                                <span class="text-xs text-slate-400">per year</span>
                            </div>

                            <!-- CTA Button -->
                            <a
                                href="{{ route('student.hostels.show', $hostel) }}"
                                class="flex items-center justify-center w-full py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 transition-all group/btn"
                            >
                                View Details
                                <i class="fas fa-arrow-right ml-2 group-hover/btn:translate-x-1 transition-transform"></i>
                            </a>
                        </div>
                    </div>
                </article>
            @endforeach

        </div>

        <!-- Pagination -->
        @if($hostels->hasPages())
            <div class="mt-8">
                {{ $hostels->links() }}
            </div>
        @endif

    @else
        <!-- Empty State -->
        <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center max-w-2xl mx-auto">
            <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-search text-slate-400 text-3xl"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-900 mb-2">No Hostels Found</h3>
            <p class="text-slate-500 mb-6 max-w-md mx-auto">
                @if(request('search'))
                    We couldn't find any hostels matching "<strong class="text-slate-900">{{ request('search') }}</strong>". Try adjusting your search terms.
                @else
                    There are no hostels available at the moment. Please check back later.
                @endif
            </p>
            
            @if(request('search'))
                <a
                    href="{{ route('student.hostels.browse') }}"
                    class="inline-flex items-center px-6 py-3 bg-slate-900 text-white font-medium rounded-xl hover:bg-slate-800 transition-all"
                >
                    <i class="fas fa-times mr-2"></i>
                    Clear Search
                </a>
            @endif
        </div>
    @endif

</div>
@endsection