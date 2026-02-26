@extends('layouts.student')

@section('title', 'Student Dashboard')
@section('content')

<!-- Welcome Banner -->
<div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg shadow-lg p-6 mb-6 text-white">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold">Welcome back, {{ Auth::user()->name }}! 👋</h1>
            <p class="text-blue-100 mt-1">Ready to find your perfect accommodation for the semester?</p>
        </div>
        <a href="{{ route('student.hostels.browse') }}"
           class="bg-white text-blue-600 px-6 py-2 rounded-lg font-semibold hover:bg-blue-50 transition">
            <i class="fas fa-search mr-2"></i>Browse Hostels
        </a>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Bookings -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Total Bookings</p>
                <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['total_bookings'] ?? 0 }}</h3>
            </div>
            <div class="bg-blue-100 p-3 rounded-lg">
                <i class="fas fa-calendar-check text-blue-600 text-xl"></i>
            </div>
        </div>
        <div class="mt-4">
            <a href="{{ route('student.bookings') }}" class="text-sm text-blue-600 hover:text-blue-800">
                View all bookings <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
    </div>

    <!-- Active Booking -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Active Booking</p>
                <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['active_bookings'] ?? 0 }}</h3>
            </div>
            <div class="bg-green-100 p-3 rounded-lg">
                <i class="fas fa-home text-green-600 text-xl"></i>
            </div>
        </div>
        <div class="mt-4">
            @if(($stats['active_bookings'] ?? 0) > 0)
                <span class="text-sm text-green-600">
                    <i class="fas fa-check-circle mr-1"></i>Currently checked in
                </span>
            @else
                <span class="text-sm text-gray-500">No active booking</span>
            @endif
        </div>
    </div>

    <!-- Total Spent -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Total Spent</p>
                <h3 class="text-2xl font-bold text-gray-800 mt-1">₵{{ number_format($stats['total_paid'] ?? 0, 2) }}</h3>
            </div>
            <div class="bg-purple-100 p-3 rounded-lg">
                <i class="fas fa-credit-card text-purple-600 text-xl"></i>
            </div>
        </div>
        <div class="mt-4">
            <a href="{{ route('student.payments') }}" class="text-sm text-purple-600 hover:text-purple-800">
                View payment history <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
    </div>

    <!-- Complaints -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Complaints</p>
                <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['complaints'] ?? 0 }}</h3>
            </div>
            <div class="bg-orange-100 p-3 rounded-lg">
                <i class="fas fa-exclamation-triangle text-orange-600 text-xl"></i>
            </div>
        </div>
        <div class="mt-4">
            @if(($stats['pending_complaints'] ?? 0) > 0)
                <span class="text-sm text-orange-600">
                    <i class="fas fa-clock mr-1"></i>{{ $stats['pending_complaints'] }} pending
                </span>
            @else
                <a href="{{ route('student.complaints') }}" class="text-sm text-orange-600 hover:text-orange-800">
                    Submit a complaint <i class="fas fa-arrow-right ml-1"></i>
                </a>
            @endif
        </div>
    </div>
</div>

<!-- Current Active Booking Section -->
@if(isset($activeBooking) && $activeBooking)
<div class="bg-white rounded-lg shadow-sm p-6 mb-8 border-l-4 border-green-500">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold text-gray-800">
            <i class="fas fa-home text-green-500 mr-2"></i>Your Current Stay
        </h2>
        <span class="px-3 py-1 bg-green-100 text-green-700 text-sm rounded-full">
            Active
        </span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="flex items-center space-x-3">
            @if($activeBooking->room->hostel->primaryImage)
                <img src="{{ Storage::url($activeBooking->room->hostel->primaryImage->path) }}"
                     alt="{{ $activeBooking->room->hostel->name }}"
                     class="w-16 h-16 object-cover rounded-lg">
            @else
                <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                    <i class="fas fa-building text-gray-400"></i>
                </div>
            @endif
            <div>
                <h3 class="font-semibold text-gray-800">{{ $activeBooking->room->hostel->name }}</h3>
                <p class="text-sm text-gray-500">Room {{ $activeBooking->room->number }}</p>
            </div>
        </div>

        <div class="flex items-center space-x-4">
            <div>
                <p class="text-sm text-gray-500">Check In</p>
                <p class="font-semibold">{{ $activeBooking->check_in->format('M d, Y') }}</p>
            </div>
            <i class="fas fa-arrow-right text-gray-400"></i>
            <div>
                <p class="text-sm text-gray-500">Check Out</p>
                <p class="font-semibold">{{ $activeBooking->check_out->format('M d, Y') }}</p>
            </div>
        </div>

        <div class="flex justify-end">
            <a href="{{ route('student.bookings.show', $activeBooking) }}"
               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                View Details
            </a>
        </div>
    </div>

    <!-- Days remaining -->
    @php
        $daysRemaining = now()->diffInDays($activeBooking->check_out, false);
    @endphp
    @if($daysRemaining > 0 && $daysRemaining <= 7)
        <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
            <p class="text-sm text-yellow-700">
                <i class="fas fa-exclamation-circle mr-1"></i>
                Your booking ends in <strong>{{ $daysRemaining }} days</strong>.
                <a href="{{ route('student.hostels.browse') }}" class="underline font-medium">Browse other hostels</a> to plan your next stay.
            </p>
        </div>
    @endif
</div>
@endif

<!-- Recent Bookings -->
<div class="bg-white rounded-lg shadow-sm p-6 mb-8">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold text-gray-800">
            <i class="fas fa-history text-blue-500 mr-2"></i>Recent Bookings
        </h2>
        <a href="{{ route('student.bookings') }}" class="text-sm text-blue-600 hover:text-blue-800">
            View All <i class="fas fa-arrow-right ml-1"></i>
        </a>
    </div>

    @if($recentBookings->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hostel</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Room</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Check In</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Check Out</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($recentBookings as $booking)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                @if($booking->room->hostel->primaryImage)
                                    <img class="w-8 h-8 rounded-lg object-cover mr-3"
                                         src="{{ Storage::url($booking->room->hostel->primaryImage->path) }}"
                                         alt="">
                                @endif
                                <span class="text-sm font-medium text-gray-900">{{ $booking->room->hostel->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            Room {{ $booking->room->number }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $booking->check_in->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $booking->check_out->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            ₵{{ number_format($booking->total_amount, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusClass = match($booking->status) {
                                    'confirmed' => 'bg-green-100 text-green-800',
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'cancelled' => 'bg-red-100 text-red-800',
                                    'completed' => 'bg-gray-100 text-gray-800',
                                    default => 'bg-gray-100 text-gray-800'
                                };
                            @endphp
                            <span class="px-2 py-1 text-xs rounded-full {{ $statusClass }}">
                                {{ ucfirst($booking->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <a href="{{ route('student.bookings.show', $booking) }}"
                               class="text-blue-600 hover:text-blue-900">
                                View <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-8">
            <i class="fas fa-calendar-times text-gray-300 text-4xl mb-3"></i>
            <p class="text-gray-500">You haven't made any bookings yet.</p>
            <a href="{{ route('student.hostels.browse') }}" class="inline-block mt-3 text-blue-600 hover:text-blue-800">
                Browse Hostels <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
    @endif
</div>

<!-- Recommended Hostels -->
<div class="bg-white rounded-lg shadow-sm p-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold text-gray-800">
            <i class="fas fa-star text-yellow-500 mr-2"></i>Recommended for You
        </h2>
        <a href="{{ route('student.hostels.browse') }}" class="text-sm text-blue-600 hover:text-blue-800">
            View All <i class="fas fa-arrow-right ml-1"></i>
        </a>
    </div>

    @if($recommendedHostels->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($recommendedHostels as $hostel)
                <div class="border rounded-lg overflow-hidden hover:shadow-lg transition">
                    <div class="relative h-40">
                        @if($hostel->primaryImage)
                            <img src="{{ Storage::url($hostel->primaryImage->path) }}"
                                 alt="{{ $hostel->name }}"
                                 class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                <i class="fas fa-building text-gray-400 text-3xl"></i>
                            </div>
                        @endif
                        @if($hostel->is_featured)
                            <div class="absolute top-2 left-2 bg-yellow-500 text-white text-xs px-2 py-1 rounded-full">
                                Featured
                            </div>
                        @endif
                    </div>
                    <div class="p-4">
                        <h3 class="font-semibold text-gray-800 mb-1">{{ $hostel->name }}</h3>
                        <p class="text-sm text-gray-500 mb-2">
                            <i class="fas fa-map-marker-alt text-gray-400 mr-1"></i>{{ $hostel->location }}
                        </p>
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-lg font-bold text-blue-600">₵{{ number_format($hostel->min_price ?? 0, 2) }}</span>
                                <span class="text-xs text-gray-500">/mo</span>
                            </div>
                            <a href="{{ route('student.hostels.show', $hostel) }}"
                               class="text-blue-600 hover:text-blue-800 text-sm">
                                View <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-8">
            <i class="fas fa-building text-gray-300 text-4xl mb-3"></i>
            <p class="text-gray-500">No recommended hostels at the moment.</p>
        </div>
    @endif
</div>

<!-- Quick Actions -->
<div class="fixed bottom-6 right-6">
    <button onclick="showQuickActions()"
            class="bg-blue-600 text-white p-4 rounded-full shadow-lg hover:bg-blue-700 transition">
        <i class="fas fa-plus text-xl"></i>
    </button>

    <!-- Quick Actions Menu (Hidden by default) -->
    <div id="quickActionsMenu" class="hidden absolute bottom-16 right-0 bg-white rounded-lg shadow-xl p-2 w-48">
        <a href="{{ route('student.hostels.browse') }}"
           class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg">
            <i class="fas fa-building mr-2"></i>Browse Hostels
        </a>
        <a href="{{ route('student.complaints') }}"
           class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg">
            <i class="fas fa-exclamation-triangle mr-2"></i>Submit Complaint
        </a>
        <a href="{{ route('student.profile') }}"
           class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg">
            <i class="fas fa-user mr-2"></i>Update Profile
        </a>
    </div>
</div>

@push('scripts')
<script>
function showQuickActions() {
    const menu = document.getElementById('quickActionsMenu');
    menu.classList.toggle('hidden');
}

// Close menu when clicking outside
document.addEventListener('click', function(event) {
    const menu = document.getElementById('quickActionsMenu');
    const button = event.target.closest('button');

    if (!button || !button.classList.contains('bg-blue-600')) {
        menu.classList.add('hidden');
    }
});
</script>
@endpush
@endsection
