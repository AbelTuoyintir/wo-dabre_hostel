@extends('layouts.hostelmanage')

@section('title', 'Hostel Details')
@section('page-title', 'Hostel Details')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Main Hostel Card -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <!-- Header with Gradient -->
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-5">
            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <i class="fas fa-building text-white text-2xl"></i>
                        <h2 class="text-2xl font-bold text-white">{{ $hostel->name }}</h2>
                    </div>
                    <div class="space-y-1">
                        @if(!empty($hostel->address))
                            <p class="text-blue-100 text-sm">
                                <i class="fas fa-location-dot mr-2"></i>{{ $hostel->address }}
                            </p>
                        @endif
                        @if(!empty($hostel->city) || !empty($hostel->region))
                            <p class="text-blue-100 text-sm">
                                <i class="fas fa-map-marker-alt mr-2"></i>
                                {{ $hostel->city ?? '' }}{{ ($hostel->city && $hostel->region) ? ', ' : '' }}{{ $hostel->region ?? '' }}
                            </p>
                        @endif
                        <p class="text-blue-100 text-sm">
                            <i class="fas fa-qrcode mr-2"></i>Hostel ID: {{ $hostel->id }}
                        </p>
                        <p class="text-blue-100 text-sm">
                            <i class="fas fa-calendar-alt mr-2"></i>Added: {{ $hostel->created_at->format('M d, Y') }}
                        </p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    @if(isset($hostel->is_approved) && $hostel->is_approved)
                        <span class="px-3 py-1.5 rounded-full bg-green-100 text-green-800 text-xs font-semibold flex items-center gap-1">
                            <i class="fas fa-check-circle text-green-600"></i> Approved
                        </span>
                    @else
                        <span class="px-3 py-1.5 rounded-full bg-yellow-100 text-yellow-800 text-xs font-semibold flex items-center gap-1">
                            <i class="fas fa-clock text-yellow-600"></i> Pending Approval
                        </span>
                    @endif
                    @if(isset($hostel->status))
                        @if($hostel->status === 'active')
                            <span class="px-3 py-1.5 rounded-full bg-green-100 text-green-800 text-xs font-semibold flex items-center gap-1">
                                <i class="fas fa-play-circle text-green-600"></i> Active
                            </span>
                        @elseif($hostel->status === 'inactive')
                            <span class="px-3 py-1.5 rounded-full bg-red-100 text-red-800 text-xs font-semibold flex items-center gap-1">
                                <i class="fas fa-stop-circle text-red-600"></i> Inactive
                            </span>
                        @elseif($hostel->status === 'maintenance')
                            <span class="px-3 py-1.5 rounded-full bg-orange-100 text-orange-800 text-xs font-semibold flex items-center gap-1">
                                <i class="fas fa-tools text-orange-600"></i> Maintenance
                            </span>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-700 mb-4 flex items-center gap-2">
                <i class="fas fa-chart-line text-blue-600"></i> Overview Statistics
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-4 hover:shadow-md transition">
                    <div class="flex items-center justify-between mb-2">
                        <i class="fas fa-door-open text-blue-600 text-xl"></i>
                        <span class="text-xs text-blue-600 font-semibold">Total</span>
                    </div>
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['total_rooms'] ?? 0 }}</div>
                    <div class="text-xs text-gray-600 mt-1">Total Rooms</div>
                </div>
                
                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-4 hover:shadow-md transition">
                    <div class="flex items-center justify-between mb-2">
                        <i class="fas fa-bed text-green-600 text-xl"></i>
                        <span class="text-xs text-green-600 font-semibold">Available</span>
                    </div>
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['available_rooms'] ?? 0 }}</div>
                    <div class="text-xs text-gray-600 mt-1">Available Rooms</div>
                </div>
                
                <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-4 hover:shadow-md transition">
                    <div class="flex items-center justify-between mb-2">
                        <i class="fas fa-users text-purple-600 text-xl"></i>
                        <span class="text-xs text-purple-600 font-semibold">Capacity</span>
                    </div>
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['total_capacity'] ?? 0 }}</div>
                    <div class="text-xs text-gray-600 mt-1">Total Capacity</div>
                </div>
                
                <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl p-4 hover:shadow-md transition">
                    <div class="flex items-center justify-between mb-2">
                        <i class="fas fa-user-check text-orange-600 text-xl"></i>
                        <span class="text-xs text-orange-600 font-semibold">Occupied</span>
                    </div>
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['current_occupancy'] ?? 0 }}</div>
                    <div class="text-xs text-gray-600 mt-1">Current Occupancy</div>
                </div>
            </div>
            
            <!-- Occupancy Progress Bar -->
            @if(($stats['total_capacity'] ?? 0) > 0)
                @php
                    $occupancyPercentage = round((($stats['current_occupancy'] ?? 0) / ($stats['total_capacity'] ?? 1)) * 100);
                @endphp
                <div class="mt-4">
                    <div class="flex justify-between text-xs text-gray-600 mb-1">
                        <span>Occupancy Rate</span>
                        <span>{{ $occupancyPercentage }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-gradient-to-r from-blue-600 to-purple-600 h-2 rounded-full transition-all duration-500" style="width: {{ $occupancyPercentage }}%"></div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Description Section -->
        @if(!empty($hostel->description))
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                    <i class="fas fa-align-left text-blue-600"></i> Description
                </h3>
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-gray-700 whitespace-pre-wrap leading-relaxed">{{ $hostel->description }}</p>
                </div>
            </div>
        @endif

        <!-- Contact Information -->
        @if(!empty($hostel->phone_1) || !empty($hostel->phone_2) || !empty($hostel->contact_email))
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                    <i class="fas fa-address-card text-purple-600"></i> Contact Information
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if(!empty($hostel->phone_1))
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-phone text-green-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Phone 1</p>
                                <p class="text-sm font-medium text-gray-700">{{ $hostel->phone_1 }}</p>
                            </div>
                        </div>
                    @endif
                    @if(!empty($hostel->phone_2))
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-phone-alt text-blue-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Phone 2</p>
                                <p class="text-sm font-medium text-gray-700">{{ $hostel->phone_2 }}</p>
                            </div>
                        </div>
                    @endif
                    @if(!empty($hostel->contact_email))
                        <div class="flex items-center gap-3 md:col-span-2">
                            <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-envelope text-purple-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Email</p>
                                <p class="text-sm font-medium text-gray-700">{{ $hostel->contact_email }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Location Map (if coordinates exist) -->
        @if(!empty($hostel->latitude) && !empty($hostel->longitude))
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                    <i class="fas fa-map text-red-600"></i> Location Map
                </h3>
                <div class="bg-gray-100 rounded-xl overflow-hidden h-64">
                    <iframe 
                        width="100%" 
                    height="100%" 
                    frameborder="0" 
                        style="border:0"
                        src="https://www.openstreetmap.org/export/embed.html?bbox={{ $hostel->longitude - 0.01 }},{{ $hostel->latitude - 0.01 }},{{ $hostel->longitude + 0.01 }},{{ $hostel->latitude + 0.01 }}&layer=mapnik&marker={{ $hostel->latitude }},{{ $hostel->longitude }}"
                        allowfullscreen>
                    </iframe>
                </div>
                <div class="mt-2 text-xs text-gray-500 text-center">
                    <i class="fas fa-location-dot mr-1"></i> Coordinates: {{ $hostel->latitude }}, {{ $hostel->longitude }}
                </div>
            </div>
        @endif

        <!-- Gallery Images -->
        @if($hostel->images && $hostel->images->count() > 0)
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                    <i class="fas fa-images text-pink-600"></i> Gallery Images
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach($hostel->images as $image)
                        <div class="relative group cursor-pointer" onclick="showImageModal('{{ Storage::url($image->image_path) }}')">
                            <img src="{{ Storage::url($image->image_path) }}" alt="Gallery image" 
                                class="w-full h-32 object-cover rounded-lg shadow-md hover:shadow-xl transition">
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 rounded-lg transition flex items-center justify-center">
                                <i class="fas fa-search-plus text-white text-xl opacity-0 group-hover:opacity-100 transition"></i>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="bg-gray-50 px-6 py-4 flex flex-col sm:flex-row gap-3 justify-end">

                <i class="fas fa-arrow-left"></i> Back to Hostels
            </a>
            <a href="{{ route('hostel-manager.rooms.index', $hostel) }}" 
               class="px-5 py-2 bg-gradient-to-r from-green-600 to-teal-600 text-white rounded-lg font-semibold hover:shadow-lg transition text-center flex items-center justify-center gap-2">
                <i class="fas fa-door-open"></i> Manage Rooms
            </a>
            <a href="{{ route('hostel-manager.hostels.edit', $hostel) }}" 
               class="px-5 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg font-semibold hover:shadow-lg transition text-center flex items-center justify-center gap-2">
                <i class="fas fa-edit"></i> Edit Hostel
            </a>
            @if($hostel->status !== 'deleted')
                <button type="button" onclick="confirmDeleteHostel({{ $hostel->id }})" 
                    class="px-5 py-2 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 transition text-center flex items-center justify-center gap-2">
                    <i class="fas fa-trash-alt"></i> Delete Hostel
                </button>
            @endif
        </div>
    </div>

    <!-- Rooms Section (if rooms exist) -->
    @if(isset($rooms) && $rooms->count() > 0)
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-gray-700 to-gray-900 px-6 py-4">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fas fa-bed"></i> Rooms in {{ $hostel->name }}
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($rooms as $room)
                        <div class="border border-gray-200 rounded-xl p-4 hover:shadow-md transition">
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="font-semibold text-gray-800">{{ $room->room_number }}</h4>
                                <span class="text-xs px-2 py-1 rounded-full {{ $room->is_available ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $room->is_available ? 'Available' : 'Occupied' }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 mb-2">
                                <i class="fas fa-users mr-1"></i> Capacity: {{ $room->capacity }}
                            </p>
                            <p class="text-sm text-gray-600 mb-2">
                                <i class="fas fa-tag mr-1"></i> Type: {{ ucfirst($room->room_type) }}
                            </p>
                            <p class="text-lg font-bold text-blue-600">
                                ₵{{ number_format($room->price_per_year, 2) }} <span class="text-xs text-gray-500 font-normal">/year</span>
                            </p>
                            <a href="{{ route('hostel-manager.rooms.show', ['hostel' => $hostel->id, 'room' => $room->id]) }}" 
                               class="mt-3 inline-block w-full text-center text-sm text-blue-600 hover:text-blue-800 font-semibold">
                                View Details <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Image Modal -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-90 z-50 hidden items-center justify-center p-4" onclick="closeImageModal()">
    <div class="relative max-w-4xl w-full" onclick="event.stopPropagation()">
        <button onclick="closeImageModal()" class="absolute -top-12 right-0 text-white hover:text-gray-300 text-2xl">
            <i class="fas fa-times"></i>
        </button>
        <img id="modalImage" src="" alt="Full size image" class="w-full rounded-lg shadow-2xl">
    </div>
</div>

@push('scripts')
<script>
    function showImageModal(imageUrl) {
        const modal = document.getElementById('imageModal');
        const modalImage = document.getElementById('modalImage');
        modalImage.src = imageUrl;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeImageModal() {
        const modal = document.getElementById('imageModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }

    function confirmDeleteHostel(hostelId) {
        Swal.fire({
            title: 'Delete Hostel?',
            text: "This action cannot be undone! All rooms and associated data will be permanently deleted.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, delete it',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
            customClass: {
                popup: 'rounded-xl p-4',
                title: 'text-lg font-semibold',
                confirmButton: 'bg-red-500 hover:bg-red-600 text-white font-medium py-2 px-4 rounded-lg',
                cancelButton: 'bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Deleting...',
                    text: 'Please wait while we delete the hostel',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Submit delete form
                const form = document.createElement('form');
                form.method = 'POST';
form.action = '{{ route("hostel-manager.hostels.update", $hostel) }}';
                form.innerHTML = '@csrf @method("DELETE")';
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    // Close modal on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeImageModal();
        }
    });

    // Auto-hide success/error messages after 5 seconds
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        });
    }, 5000);
</script>
@endpush

@push('styles')
<style>
    /* Smooth transitions */
    .transition {
        transition: all 0.3s ease;
    }
    
    /* Gallery image hover effect */
    .group:hover .group-hover\:bg-opacity-30 {
        background-opacity: 0.3;
    }
    
    /* Stats card animations */
    .stats-card {
        animation: fadeInUp 0.5s ease-out;
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Progress bar animation */
    .progress-bar {
        transition: width 1s ease-in-out;
    }
</style>
@endpush
@endsection