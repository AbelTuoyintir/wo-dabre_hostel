<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>UCC Hostel Booking System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }
        .location-btn.active {
            background-color: #1d4ed8;
            color: white;
        }
        .hostel-card {
            transition: transform 0.3s ease;
        }
        .hostel-card:hover {
            transform: translateY(-5px);
        }
        #bookingModal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 1000;
        }
        .modal-content {
            animation: slideIn 0.3s ease-out;
        }
        @keyframes slideIn {
            from { transform: translateY(-50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .loader {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-blue-900 text-white shadow-lg">
        <div class="container mx-auto px-4 py-6">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="flex items-center space-x-3 mb-4 md:mb-0">
                    <div class="bg-white text-blue-900 p-2 rounded-lg">
                        <i class="fas fa-university text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold">UCC Hostel Booking</h1>
                        <p class="text-blue-200">University of Cape Coast Student Accommodation</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    @guest
                        <a href="{{ route('register') }}"
                           class="hidden md:flex items-center space-x-2 bg-blue-800 text-white px-4 py-2 rounded-lg hover:bg-blue-900 transition">
                            <i class="fas fa-user-graduate"></i>
                            <span>Create Account</span>
                        </a>
                        <a href="{{ route('login') }}"
                           class="bg-white text-blue-900 px-4 py-2 rounded-lg font-medium hover:bg-blue-100 transition">
                            <i class="fas fa-sign-in-alt mr-2"></i> Student Login
                        </a>
                    @else
                        @if(auth()->user()->role === 'student')
                            <a href="{{ route('student.dashboard') }}"
                               class="bg-green-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-green-700 transition">
                                <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                            </a>
                        @elseif(auth()->user()->role === 'admin')
                            <a href="{{ route('admin.dashboard') }}"
                               class="bg-purple-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-purple-700 transition">
                                <i class="fas fa-cog mr-2"></i> Admin
                            </a>
                        @elseif(auth()->user()->role === 'manager')
                            <a href="{{ route('hostel-manager.dashboard') }}"
                               class="bg-orange-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-orange-700 transition">
                                <i class="fas fa-building mr-2"></i> Manager
                            </a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-red-700 transition">
                                <i class="fas fa-sign-out-alt mr-2"></i> Logout
                            </button>
                        </form>
                    @endguest
                </div>
            </div>
        </div>
    </header>

    <!-- Loading Spinner -->
    <div id="loadingSpinner" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-[9999] flex items-center justify-center">
        <div class="loader"></div>
    </div>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
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

        <!-- Search and Filter Section -->
        <section class="mb-12">
            <h2 class="text-3xl font-bold text-gray-800 mb-2">Find Your Perfect Hostel</h2>
            <p class="text-gray-600 mb-6">Browse and book hostels across all UCC campuses. No more roaming around searching for accommodation.</p>

            <!-- Search Bar -->
            <form method="GET" action="{{ route('hostels.index') }}" class="bg-white p-6 rounded-xl shadow-md mb-8">
                <div class="flex flex-col md:flex-row md:items-center md:space-x-4">
                    <div class="flex-1 mb-4 md:mb-0">
                        <div class="relative">
                            <i class="fas fa-search absolute left-4 top-3.5 text-gray-400"></i>
                            <input type="text" name="search" id="searchInput" value="{{ request('search', '') }}" placeholder="Search hostels by name or location..."
                                   class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                        </div>
                    </div>
                    <div class="flex space-x-4">
                        <select name="price_range" id="priceFilter" class="px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                            <option value="">Filter by Price</option>
                            <option value="0-500" {{ request('price_range') == '0-500' ? 'selected' : '' }}>Under ₵500</option>
                            <option value="500-1000" {{ request('price_range') == '500-1000' ? 'selected' : '' }}>₵500 - ₵1000</option>
                            <option value="1000-1500" {{ request('price_range') == '1000-1500' ? 'selected' : '' }}>₵1000 - ₵1500</option>
                            <option value="1500-2000" {{ request('price_range') == '1500-2000' ? 'selected' : '' }}>₵1500 - ₵2000</option>
                            <option value="2000+" {{ request('price_range') == '2000+' ? 'selected' : '' }}>Above ₵2000</option>
                        </select>
                        <button type="submit" id="applyFilters" class="bg-blue-700 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-800 transition">
                            Search
                        </button>
                    </div>
                </div>

                <!-- Location Filter -->
                <div class="mt-6">
                    <h3 class="font-medium text-gray-700 mb-3">Filter by Location:</h3>
                    <div class="flex flex-wrap gap-3" id="locationFilters">
                        <a href="{{ route('hostels.index', array_merge(request()->except('location'), ['location' => 'all'])) }}"
                           class="location-btn {{ !request('location') || request('location') == 'all' ? 'active' : '' }} px-4 py-2 bg-blue-100 text-blue-800 rounded-lg hover:bg-blue-200 transition">
                            All Locations
                        </a>
                        @if(isset($locations))
                            @foreach($locations as $location)
                                <a href="{{ route('hostels.index', array_merge(request()->except('location'), ['location' => $location])) }}"
                                   class="location-btn {{ request('location') == $location ? 'active' : '' }} px-4 py-2 bg-gray-100 text-gray-800 rounded-lg hover:bg-gray-200 transition">
                                    <i class="fas fa-map-marker-alt mr-2"></i>{{ $location }}
                                </a>
                            @endforeach
                        @endif
                    </div>
                </div>
            </form>

            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8" id="statsContainer">
                @if(isset($stats))
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <div class="flex items-center">
                        <div class="bg-blue-100 p-3 rounded-lg mr-4">
                            <i class="fas fa-building text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">Total Hostels</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $stats['total_hostels'] }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <div class="flex items-center">
                        <div class="bg-green-100 p-3 rounded-lg mr-4">
                            <i class="fas fa-bed text-green-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">Available Rooms</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $stats['total_rooms'] }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <div class="flex items-center">
                        <div class="bg-purple-100 p-3 rounded-lg mr-4">
                            <i class="fas fa-map-marked-alt text-purple-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">Locations</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $stats['locations_count'] }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <div class="flex items-center">
                        <div class="bg-yellow-100 p-3 rounded-lg mr-4">
                            <i class="fas fa-users text-yellow-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">Students Booked</p>
                            <p class="text-2xl font-bold text-gray-800">1850+</p>
                        </div>
                    </div>
                </div>
                @else
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <div class="flex items-center">
                        <div class="bg-blue-100 p-3 rounded-lg mr-4">
                            <i class="fas fa-building text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">Loading...</p>
                            <p class="text-2xl font-bold text-gray-800">-</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </section>

        <!-- Hostels Section -->
        <section>
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Available Hostels</h2>

            <div id="hostelsContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
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
                        <div class="hostel-card bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
                            <div class="relative h-48">
                                <img src="{{ $imageUrl }}" alt="{{ $hostel['name'] }}" class="w-full h-full object-cover">

                                <div class="absolute top-4 left-4 bg-white text-gray-800 px-3 py-1 rounded-lg font-medium text-sm">
                                    {{ $hostel['location'] }}
                                </div>
                                @if($hostel['is_featured'])
                                <div class="absolute bottom-4 left-4 bg-yellow-500 text-white px-3 py-1 rounded-lg font-medium text-sm">
                                    Featured
                                </div>
                                @endif
                            </div>
                            <div class="p-6">
                                <div class="flex justify-between items-start mb-3">
                                    <h3 class="text-xl font-bold text-gray-800">{{ $hostel['name'] }}</h3>
                                    <div class="flex items-center">
                                        <i class="fas fa-star text-yellow-500 mr-1"></i>
                                        <span class="font-medium">{{ $hostel['rating'] ?? '0.0' }}</span>
                                    </div>
                                </div>
                                <p class="text-gray-600 mb-4">{{ \Illuminate\Support\Str::limit($hostel['description'], 100) }}</p>

                                <div class="mb-4">
                                    <p class="text-gray-700 font-medium mb-2">Amenities:</p>
                                    <div class="flex flex-wrap gap-2">
                                        @if($hostel['amenities'])
                                            @foreach(array_slice($hostel['amenities'], 0, 3) as $amenity)
                                                <span class="bg-gray-100 text-gray-800 text-xs px-3 py-1 rounded-full">{{ $amenity }}</span>
                                            @endforeach
                                            @if(count($hostel['amenities']) > 3)
                                                <span class="bg-gray-100 text-gray-800 text-xs px-3 py-1 rounded-full">+{{ count($hostel['amenities']) - 3 }}</span>
                                            @endif
                                        @endif
                                    </div>
                                </div>

                                <div class="flex justify-between items-center mb-4">
                                    {{-- <div>
                                        <p class="text-gray-700"><i class="fas fa-bed text-blue-500 mr-2"></i>From ₵{{ number_format($minPrice) }}/year</p>
                                    </div> --}}
                                    {{-- <div class="text-green-600 font-semibold">
                                        <i class="fas fa-check-circle mr-1"></i> {{ $availableCount }} Available
                                    </div> --}}
                                </div>

                                <a href="{{ route('hostels.guest.show', $hostel['id']) }}"
                                   class="block w-full bg-blue-700 text-white py-3 rounded-lg font-semibold hover:bg-blue-800 transition text-center">
                                    <i class="fas fa-calendar-check mr-2"></i> View Details
                                </a>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-span-full text-center py-12">
                        <i class="fas fa-search text-gray-400 text-5xl mb-4"></i>
                        <h3 class="text-xl font-semibold text-gray-700 mb-2">No hostels found</h3>
                        <p class="text-gray-500">Try adjusting your filters or search terms</p>
                        <a href="{{ route('hostels.index') }}" class="mt-4 inline-block bg-blue-700 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-800 transition">
                            Clear Filters
                        </a>
                    </div>
                @endif
            </div>

            <!-- Pagination -->
            @if(isset($hostels) && $hostels->hasPages())
            <div class="mt-8">
                {{ $hostels->links() }}
            </div>
            @endif
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white mt-16">
        <div class="container mx-auto px-4 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="bg-white text-blue-900 p-2 rounded-lg">
                            <i class="fas fa-university text-xl"></i>
                        </div>
                        <h3 class="text-xl font-bold">UCC Hostel Booking</h3>
                    </div>
                    <p class="text-gray-400">The official hostel booking platform for University of Cape Coast students.</p>
                </div>
                <div>
                    <h4 class="text-lg font-bold mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white transition">About Us</a></li>
                        <li><a href="#" class="hover:text-white transition">Contact</a></li>
                        <li><a href="#" class="hover:text-white transition">FAQs</a></li>
                        <li><a href="#" class="hover:text-white transition">Privacy Policy</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-bold mb-4">Locations</h4>
                    <ul class="space-y-2 text-gray-400" id="locationList">
                        @if(isset($locations))
                            @foreach($locations->slice(0, 5) as $location)
                                <li>{{ $location }}</li>
                            @endforeach
                        @endif
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-bold mb-4">Contact Info</h4>
                    <ul class="space-y-3 text-gray-400">
                        <li class="flex items-center">
                            <i class="fas fa-map-marker-alt mr-3 text-blue-400"></i>
                            <span>University of Cape Coast, Ghana</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-phone mr-3 text-blue-400"></i>
                            <span>+233 24 123 4567</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-envelope mr-3 text-blue-400"></i>
                            <span>hostelbooking@ucc.edu.gh</span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} University of Cape Coast Hostel Booking System. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // API endpoints
        const API = {
            hostels: '{{ route("hostels.index") }}',
            locations: '{{ route("hostels.locations") }}'
        };

        // Server-side data passed from Blade
        const serverData = {
            hostels: @json($transformedHostels ?? []),
            locations: @json($locations ?? []),
            stats: @json($stats ?? null)
        };

        // DOM elements
        const hostelsContainer = document.getElementById('hostelsContainer');
        const locationButtonsContainer = document.getElementById('locationFilters');
        const statsContainer = document.getElementById('statsContainer');
        const searchInput = document.getElementById('searchInput');
        const priceFilter = document.getElementById('priceFilter');
        const applyFilters = document.getElementById('applyFilters');
        const loadingSpinner = document.getElementById('loadingSpinner');

        // Current filter state
        let currentFilter = {
            location: '{{ request("location", "all") }}',
            price: '{{ request("price_range", "") }}',
            search: '{{ request("search", "") }}',
            page: 1
        };

        // Store all hostels data
        let allHostels = serverData.hostels || [];
        let locations = serverData.locations || [];

        // Initialize the page
        async function init() {
            showLoading();
            try {
                // If no server data, fetch via AJAX
                if (allHostels.length === 0) {
                    await Promise.all([fetchHostels(), fetchLocations()]);
                } else {
                    // Use server data but still fetch locations if not available
                    if (locations.length === 0) {
                        await fetchLocations();
                    }
                }
                updateStats();
            } catch (error) {
                console.error('Initialization error:', error);
                showError('Failed to load data. Please refresh the page.');
            } finally {
                hideLoading();
            }
        }

        // Fetch hostels from API
        async function fetchHostels() {
            try {
                const params = new URLSearchParams({
                    location: currentFilter.location !== 'all' ? currentFilter.location : '',
                    price_range: currentFilter.price,
                    search: currentFilter.search
                });

                const response = await fetch(`${API.hostels}?${params}`);
                const data = await response.json();

                if (data.data) {
                    allHostels = data.data;
                    renderHostels(allHostels);
                } else {
                    allHostels = data.hostels?.data || [];
                    renderHostels(allHostels);
                }
            } catch (error) {
                console.error('Error fetching hostels:', error);
                throw error;
            }
        }

        // Fetch locations for filter
        async function fetchLocations() {
            try {
                const response = await fetch(API.locations);
                const data = await response.json();

                if (Array.isArray(data)) {
                    locations = data;
                } else if (data.locations) {
                    locations = data.locations;
                }

                renderLocationFilters();
                updateLocationList();
            } catch (error) {
                console.error('Error fetching locations:', error);
            }
        }

        // Render location filter buttons
        function renderLocationFilters() {
            // Only update if dynamic (not initial server render)
            if (locationButtonsContainer.querySelectorAll('.location-btn').length <= 1) {
                let html = `<a href="{{ route('hostels.index', array_merge(request()->except('location'), ['location' => 'all'])) }}"
                           class="location-btn ${!currentFilter.location || currentFilter.location === 'all' ? 'active' : ''} px-4 py-2 bg-blue-100 text-blue-800 rounded-lg hover:bg-blue-200 transition">
                            All Locations
                        </a>`;

                locations.forEach(location => {
                    if (location && location.trim()) {
                        html += `
                            <a href="{{ route('hostels.index') }}?location=${encodeURIComponent(location)}"
                               class="location-btn px-4 py-2 bg-gray-100 text-gray-800 rounded-lg hover:bg-gray-200 transition"
                               data-location="${location.toLowerCase()}">
                                <i class="fas fa-map-marker-alt mr-2"></i>${location}
                            </a>
                        `;
                    }
                });

                // Only append if we have locations from AJAX
                if (locations.length > 0) {
                    locationButtonsContainer.innerHTML = html;
                }
            }
        }

        // Update location list in footer
        function updateLocationList() {
            const locationList = document.getElementById('locationList');
            if (locationList && locations.length > 0) {
                let html = '';
                locations.slice(0, 5).forEach(location => {
                    if (location && location.trim()) {
                        html += `<li>${location}</li>`;
                    }
                });
                locationList.innerHTML = html;
            }
        }

        // Render hostels to the page
        function renderHostels(hostelsArray) {
            if (!hostelsArray || hostelsArray.length === 0) {
                hostelsContainer.innerHTML = `
                    <div class="col-span-full text-center py-12">
                        <i class="fas fa-search text-gray-400 text-5xl mb-4"></i>
                        <h3 class="text-xl font-semibold text-gray-700 mb-2">No hostels found</h3>
                        <p class="text-gray-500">Try adjusting your filters or search terms</p>
                    </div>
                `;
                return;
            }

            let html = '';
            hostelsArray.forEach(host const imageUrl =el => {

                    ? `{{ Storage::url('') }}${hostel.primary_image.image_path}`
                    : hostel.images?.[0]?.image_path
                        ? `{{ Storage::url('') }}${hostel.images[0].image_path}`
                        : 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80';
                const minPrice = hostel.min_price || hostel.rooms?.[0]?.room_cost || 0;
                const availableCount = hostel.available_rooms_count || hostel.rooms?.length || 0;

                html += `
                    <div class="hostel-card bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
                        <div class="relative h-48">
                            <img src="${imageUrl}" alt="${hostel.name}" class="w-full h-full object-cover">
                            <div class="absolute top-4 right-4 bg-blue-700 text-white px-3 py-1 rounded-lg font-semibold">
                                ₵${minPrice}
                            </div>
                            <div class="absolute top-4 left-4 bg-white text-gray-800 px-3 py-1 rounded-lg font-medium text-sm">
                                ${hostel.location}
                            </div>
                            ${hostel.is_featured ? `
                                <div class="absolute bottom-4 left-4 bg-yellow-500 text-white px-3 py-1 rounded-lg font-medium text-sm">
                                    Featured
                                </div>
                            ` : ''}
                        </div>
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-3">
                                <h3 class="text-xl font-bold text-gray-800">${hostel.name}</h3>
                                <div class="flex items-center">
                                    <i class="fas fa-star text-yellow-500 mr-1"></i>
                                    <span class="font-medium">${hostel.rating || '0.0'}</span>
                                </div>
                            </div>
                            <p class="text-gray-600 mb-4">${hostel.description?.substring(0, 100)}${hostel.description?.length > 100 ? '...' : ''}</p>

                            <div class="mb-4">
                                <p class="text-gray-700 font-medium mb-2">Amenities:</p>
                                <div class="flex flex-wrap gap-2">
                                    ${hostel.amenities?.slice(0, 3).map(amenity => `
                                        <span class="bg-gray-100 text-gray-800 text-xs px-3 py-1 rounded-full">${amenity}</span>
                                    `).join('')}
                                    ${hostel.amenities?.length > 3 ? `<span class="bg-gray-100 text-gray-800 text-xs px-3 py-1 rounded-full">+${hostel.amenities.length - 3}</span>` : ''}
                                </div>
                            </div>

                            <div class="flex justify-between items-center mb-4">
                                <div>
                                    <p class="text-gray-700"><i class="fas fa-bed text-blue-500 mr-2"></i>From ₵${minPrice}/year</p>
                                </div>
                                <div class="text-green-600 font-semibold">
                                    <i class="fas fa-check-circle mr-1"></i> ${availableCount} Available
                                </div>
                            </div>

                            <a href="{{ url('hostels') }}/${hostel.id}"
                               class="block w-full bg-blue-700 text-white py-3 rounded-lg font-semibold hover:bg-blue-800 transition text-center">
                                <i class="fas fa-calendar-check mr-2"></i> View Details
                            </a>
                        </div>
                    </div>
                `;
            });

            hostelsContainer.innerHTML = html;
        }

        // Update statistics
        function updateStats() {
            // If we have server stats, use them
            if (serverData.stats) {
                statsContainer.innerHTML = `
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                        <div class="flex items-center">
                            <div class="bg-blue-100 p-3 rounded-lg mr-4">
                                <i class="fas fa-building text-blue-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-gray-500 text-sm">Total Hostels</p>
                                <p class="text-2xl font-bold text-gray-800">${serverData.stats.total_hostels}</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                        <div class="flex items-center">
                            <div class="bg-green-100 p-3 rounded-lg mr-4">
                                <i class="fas fa-bed text-green-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-gray-500 text-sm">Available Rooms</p>
                                <p class="text-2xl font-bold text-gray-800">${serverData.stats.total_rooms}</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                        <div class="flex items-center">
                            <div class="bg-purple-100 p-3 rounded-lg mr-4">
                                <i class="fas fa-map-marked-alt text-purple-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-gray-500 text-sm">Locations</p>
                                <p class="text-2xl font-bold text-gray-800">${serverData.stats.locations_count}</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                        <div class="flex items-center">
                            <div class="bg-yellow-100 p-3 rounded-lg mr-4">
                                <i class="fas fa-users text-yellow-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-gray-500 text-sm">Students Booked</p>
                                <p class="text-2xl font-bold text-gray-800">1850+</p>
                            </div>
                        </div>
                    </div>
                `;
                return;
            }

            // Otherwise calculate from client-side data
            const totalHostels = allHostels.length;
            const totalRooms = allHostels.reduce((sum, hostel) => sum + (hostel.rooms?.length || 0), 0);
            const uniqueLocations = [...new Set(allHostels.map(h => h.location).filter(Boolean))].length;
            const totalBookings = 1850;

            statsContainer.innerHTML = `
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <div class="flex items-center">
                        <div class="bg-blue-100 p-3 rounded-lg mr-4">
                            <i class="fas fa-building text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">Total Hostels</p>
                            <p class="text-2xl font-bold text-gray-800">${totalHostels}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <div class="flex items-center">
                        <div class="bg-green-100 p-3 rounded-lg mr-4">
                            <i class="fas fa-bed text-green-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">Available Rooms</p>
                            <p class="text-2xl font-bold text-gray-800">${totalRooms}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <div class="flex items-center">
                        <div class="bg-purple-100 p-3 rounded-lg mr-4">
                            <i class="fas fa-map-marked-alt text-purple-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">Locations</p>
                            <p class="text-2xl font-bold text-gray-800">${uniqueLocations}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <div class="flex items-center">
                        <div class="bg-yellow-100 p-3 rounded-lg mr-4">
                            <i class="fas fa-users text-yellow-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">Students Booked</p>
                            <p class="text-2xl font-bold text-gray-800">${totalBookings}+</p>
                        </div>
                    </div>
                </div>
            `;
        }

        // Apply filters handler (for AJAX)
        async function applyFiltersHandler() {
            showLoading();
            try {
                await fetchHostels();
            } catch (error) {
                console.error('Error applying filters:', error);
            } finally {
                hideLoading();
            }
        }

        // Show error message
        function showError(message) {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg z-50';
            errorDiv.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <span>${message}</span>
                </div>
            `;
            document.body.appendChild(errorDiv);
            setTimeout(() => errorDiv.remove(), 5000);
        }

        // Loading spinner functions
        function showLoading() {
            loadingSpinner.classList.remove('hidden');
        }

        function hideLoading() {
            loadingSpinner.classList.add('hidden');
        }

        // Setup event listeners
        function setupEventListeners() {
            // Search input with debounce for AJAX
            let searchTimeout;
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    currentFilter.search = this.value;
                    currentFilter.page = 1;
                    // Could trigger AJAX here, but form submission handles it
                }, 500);
            });

            // Price filter change
            priceFilter.addEventListener('change', function() {
                currentFilter.price = this.value;
                currentFilter.page = 1;
                // Could trigger AJAX here, but form submission handles it
            });

            // Apply filters button (form submission)
            applyFilters.addEventListener('click', function() {
                // Form will submit naturally
            });

            // Enter key in search
            searchInput.addEventListener('keyup', function(event) {
                if (event.key === 'Enter') {
                    currentFilter.search = this.value;
                    // Form will submit naturally
                }
            });
        }

        // Initialize the application
        document.addEventListener('DOMContentLoaded', () => {
            init();
            setupEventListeners();
        });
    </script>
</body>
</html>
