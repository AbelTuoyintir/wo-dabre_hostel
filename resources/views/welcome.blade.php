@extends('layouts.home')

@section('title', 'Wo-dabre - Find your next student home')

@section('content')
    <!-- SEARCH & FILTER SECTION -->
   

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
                                
                                <button class="absolute top-3 right-3 text-white text-xl drop-shadow-md z-10 hover:scale-110 transition-transform">
                                    <i class="far fa-heart"></i>
                                </button>

                                <label class="absolute top-3 left-3 z-10 cursor-pointer" onclick="event.stopPropagation()">
                                    <input type="checkbox" class="compare-checkbox hidden" data-id="{{ $hostel['uuid'] ?? $hostel['id'] }}" data-name="{{ $hostel['name'] }}" data-image="{{ $imageUrl }}">
                                    <div class="bg-white/90 p-2 rounded-full shadow-sm border border-slate-200 hover:bg-white transition-colors flex items-center justify-center w-8 h-8 group-has-[:checked]:bg-rose-500 group-has-[:checked]:border-rose-500">
                                        <i class="fas fa-plus text-[10px] text-slate-600 group-has-[:checked]:text-white group-has-[:checked]:fa-check"></i>
                                    </div>
                                </label>

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
                                
                                {{-- <div class="pt-1">
                                    <span class="font-bold text-slate-800">₵{{ number_format($minPrice, 2) }}</span>
                                    <span class="text-slate-600 font-light text-sm">per year</span>
                                </div> --}}
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
    <div id="map-button" class="fixed bottom-24 left-1/2 -translate-x-1/2 z-30 md:bottom-10 transition-all duration-300">
        <button class="bg-slate-800 hover:bg-slate-900 text-white px-5 py-3 rounded-full flex items-center gap-2 shadow-xl hover:scale-105 transition-all text-sm font-bold">
            <span>Show Map</span>
            <i class="fas fa-map"></i>
        </button>
    </div>

    <!-- FLOATING COMPARISON BAR -->
    <div id="comparison-bar" class="fixed bottom-24 left-0 right-0 z-40 px-4 md:bottom-10 hidden">
        <div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-2xl border border-slate-200 p-3 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3 overflow-x-auto no-scrollbar" id="selected-hostels">
                <!-- Selected hostels will be injected here -->
            </div>
            <div class="flex items-center gap-2">
                <span id="compare-count" class="text-xs font-bold text-slate-500 min-w-max">0 selected</span>
                <button id="compare-btn" class="bg-rose-500 hover:bg-rose-600 text-white px-6 py-2 rounded-xl text-sm font-bold transition-colors shadow-sm disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                    Compare
                </button>
                <button id="clear-compare" class="text-slate-400 hover:text-slate-600 p-2 transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkboxes = document.querySelectorAll('.compare-checkbox');
        const comparisonBar = document.getElementById('comparison-bar');
        const mapButton = document.getElementById('map-button');
        const selectedContainer = document.getElementById('selected-hostels');
        const compareBtn = document.getElementById('compare-btn');
        const compareCount = document.getElementById('compare-count');
        const clearBtn = document.getElementById('clear-compare');

        let selectedHostels = [];

        function updateBar() {
            if (selectedHostels.length > 0) {
                comparisonBar.classList.remove('hidden');
                mapButton.classList.add('opacity-0', 'pointer-events-none');

                selectedContainer.innerHTML = selectedHostels.map(h => `
                    <div class="relative min-w-[50px] group">
                        <img src="${h.image}" class="w-12 h-12 rounded-lg object-cover border-2 border-rose-500">
                        <button onclick="removeHostel('${h.id}')" class="absolute -top-2 -right-2 bg-slate-800 text-white rounded-full w-5 h-5 flex items-center justify-center text-[8px] border border-white">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `).join('');

                compareCount.innerText = \`\${selectedHostels.length} selected\`;
                compareBtn.disabled = selectedHostels.length < 2;
            } else {
                comparisonBar.classList.add('hidden');
                mapButton.classList.remove('opacity-0', 'pointer-events-none');
            }
        }

        window.removeHostel = function(id) {
            selectedHostels = selectedHostels.filter(h => h.id !== id);
            const cb = document.querySelector(\`.compare-checkbox[data-id="\${id}"]\`);
            if (cb) cb.checked = false;
            updateBar();
        };

        checkboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                const id = this.dataset.id;
                if (this.checked) {
                    if (selectedHostels.length >= 4) {
                        this.checked = false;
                        showWarningMessage('You can compare up to 4 hostels.');
                        return;
                    }
                    selectedHostels.push({
                        id: id,
                        name: this.dataset.name,
                        image: this.dataset.image
                    });
                } else {
                    selectedHostels = selectedHostels.filter(h => h.id !== id);
                }
                updateBar();
            });
        });

        compareBtn.addEventListener('click', function() {
            const ids = selectedHostels.map(h => h.id).join(',');
            window.location.href = \`{{ route('hostels.compare') }}?ids=\${ids}\`;
        });

        clearBtn.addEventListener('click', function() {
            selectedHostels = [];
            checkboxes.forEach(cb => cb.checked = false);
            updateBar();
        });
    });
</script>
@endpush
