made @extends('layouts.student')

@section('title', 'My Bookings')
@section('content')

<!-- Header -->
<div class="bg-white rounded-lg shadow-sm p-6 mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">My Bookings</h1>
            <p class="text-gray-600 mt-1">Track and manage your accommodation bookings</p>
        </div>
        <a href="{{ route('student.hostels.browse') }}"
           class="mt-4 md:mt-0 inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-plus mr-2"></i>Book New Room
        </a>
    </div>

    <!-- Booking Stats -->
    @php
        $totalBookings = $bookings->total();
        $activeBookings = $bookings->where('status', 'confirmed')->where('check_out', '>=', now())->count();
        $pendingBookings = $bookings->where('status', 'pending')->count();
        $completedBookings = $bookings->where('status', 'completed')->count();
    @endphp

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6 pt-6 border-t">
        <div class="text-center">
            <span class="text-2xl font-bold text-gray-800">{{ $totalBookings }}</span>
            <p class="text-sm text-gray-500">Total Bookings</p>
        </div>
        <div class="text-center">
            <span class="text-2xl font-bold text-green-600">{{ $activeBookings }}</span>
            <p class="text-sm text-gray-500">Active</p>
        </div>
        <div class="text-center">
            <span class="text-2xl font-bold text-yellow-600">{{ $pendingBookings }}</span>
            <p class="text-sm text-gray-500">Pending</p>
        </div>
        <div class="text-center">
            <span class="text-2xl font-bold text-gray-600">{{ $completedBookings }}</span>
            <p class="text-sm text-gray-500">Completed</p>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-lg shadow-sm p-6 mb-6">
    <form method="GET" action="{{ route('student.bookings') }}" class="flex flex-wrap items-center gap-4">
        <div class="flex-1 min-w-[200px]">
            <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </div>
        <div class="flex-1 min-w-[200px]">
            <select name="sort" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest First</option>
                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                <option value="checkin_asc" {{ request('sort') == 'checkin_asc' ? 'selected' : '' }}>Check-in Date (Earliest)</option>
                <option value="checkin_desc" {{ request('sort') == 'checkin_desc' ? 'selected' : '' }}>Check-in Date (Latest)</option>
            </select>
        </div>
        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            Apply Filters
        </button>
        <a href="{{ route('student.bookings') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
            Clear
        </a>
    </form>
</div>

<!-- Bookings List -->
@if($bookings->count() > 0)
    <div class="space-y-4">
        @foreach($bookings as $booking)
            <div class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <!-- Booking Info -->
                    <div class="flex-1">
                        <div class="flex items-start space-x-4">
                            <!-- Hostel Image -->
                            @if($booking->hostel && $booking->hostel->primaryImage)
                                <img src="{{ $booking->hostel->primaryImage->url }}" 
                                     alt="{{ $booking->hostel->name }}"
                            @if($booking->hostel && $booking->hostel->primaryImage)
                                     onerror="this.nextElementSibling.style.display='flex';this.style.display='none';">
                                <img src="{{ $booking->hostel->primaryImage->url }}" 
                                     alt="{{ $booking->hostel->name }}"
                                     class="w-20 h-20 object-cover rounded-lg flex-shrink-0 shadow"
                            @else
                                <div class="w-20 h-20 bg-gray-200 rounded-lg flex items-center justify-center flex-shrink-0 shadow">
                                    <i class="fas fa-building text-gray-400 text-2xl"></i>
                                </div>
                            @endif
                                     onerror="this.nextElementSibling.style.display='flex';this.style.display='none';">
                                <div class="w-20 h-20 bg-gray-200 rounded-lg flex items-center justify-center flex-shrink-0 hidden shadow">
                                    <i class="fas fa-building text-gray-400 text-2xl"></i>
                                </div>
                            @else
                                <div class="w-20 h-20 bg-gray-200 rounded-lg flex items-center justify-center flex-shrink-0 shadow">
                                    <i class="fas fa-building text-gray-400 text-2xl"></i>
                                </div>
                            @endif
vendor\laravel\framework\src\Illuminate\Routing\AbstractRouteCollection.php:131
The GET method is not supported for route student/profile/password. Supported methods: PUT.

LARAVEL
12.50.0
PHP
8.4.16
UNHANDLED
CODE 0
405
GET
https://wo-dabre.test/student/profile/password

Exception trace
31 vendor frames

public\index.php
public\index.php:20

15
16// Bootstrap Laravel and handle the request...
17/** @var Application $app */
18$app = require_once __DIR__.'/../bootstrap/app.php';
19


                            <!-- Details -->
                            <div class="flex-1">
                                <div class="flex flex-wrap items-center gap-3 mb-2">
                                    <h3 class="text-lg font-semibold text-gray-800">
                                        {{ $booking->hostel->name ?? 'Hostel Unavailable' }}
                                    </h3>
                                    @php
                                        $statusColors = [
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'confirmed' => 'bg-green-100 text-green-800',
                                            'completed' => 'bg-blue-100 text-blue-800',
                                            'cancelled' => 'bg-red-100 text-red-800',
                                        ];
                                        $statusColor = $statusColors[$booking->status] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="px-3 py-1 text-xs rounded-full {{ $statusColor }}">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                    @if($booking->payment_status == 'paid')
                                        <span class="px-3 py-1 text-xs bg-green-100 text-green-800 rounded-full">
                                            <i class="fas fa-check-circle mr-1"></i>Paid
                                        </span>
                                    @endif
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-3">
                                    <!-- Dates -->
                                    <div class="flex items-center text-sm text-gray-600">
                                        <i class="fas fa-calendar-alt text-blue-500 w-5"></i>
                                        <span>
                                            {{ $booking->check_in->format('M d, Y') }}
                                            <i class="fas fa-arrow-right mx-1 text-gray-400"></i>
                                            {{ $booking->check_out->format('M d, Y') }}
                                        </span>
                                    </div>

                                    <!-- Room -->
                                    <div class="flex items-center text-sm text-gray-600">
                                        <i class="fas fa-door-open text-green-500 w-5"></i>
                                        <span>Room {{ $booking->room->number ?? 'N/A' }} ({{ $booking->room->capacity ?? '?' }} persons)</span>
                                    </div>

                                    <!-- Amount -->
                                    <div class="flex items-center text-sm font-medium">
                                        <i class="fas fa-tag text-purple-500 w-5"></i>
                                        <span class="text-gray-900">₵{{ number_format($booking->total_amount, 2) }}</span>
                                    </div>
                                </div>

                                <!-- Duration and Reference -->
                                <div class="flex flex-wrap items-center gap-4 mt-3 text-xs text-gray-500">
                                    <span>
                                        <i class="far fa-clock mr-1"></i>
                                        {{ $booking->check_in->diffInDays($booking->check_out) }} nights
                                    </span>
                                    <span>
                                        <i class="fas fa-hashtag mr-1"></i>
                                        Ref: {{ $booking->booking_reference ?? 'N/A' }}
                                    </span>
                                    <span>
                                        <i class="far fa-calendar-check mr-1"></i>
                                        Booked: {{ $booking->created_at->format('M d, Y') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center space-x-2 mt-4 md:mt-0 md:ml-4">
                        <a href="{{ route('student.bookings.show', $booking) }}"
                           class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition">
                            View Details
                        </a>

                        @if($booking->status == 'confirmed' && $booking->check_in->isFuture())
                            <form action="{{ route('student.bookings.cancel', $booking) }}"
                                  method="POST"
                                  onsubmit="return confirmCancel()"
                                  class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        class="px-4 py-2 border border-red-300 text-red-600 text-sm rounded-lg hover:bg-red-50 transition">
                                    Cancel
                                </button>
                            </form>
                        @endif

                        @if($booking->status == 'pending')
                            <a href="{{ route('payment.initialize', $booking) }}"
                               class="px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition">
                                Pay Now
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Urgent Warning for Upcoming Check-in -->
                @if($booking->status == 'confirmed' && $booking->check_in->isFuture() && $booking->check_in->diffInDays(now()) <= 3)
                    <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <p class="text-sm text-blue-700">
                            <i class="fas fa-info-circle mr-2"></i>
                            Your check-in is in <strong>{{ $booking->check_in->diffInDays(now()) }} days</strong>.
                            Please prepare your documents for a smooth check-in process.
                        </p>
                    </div>
                @endif

                <!-- Check-out Reminder -->
                @if($booking->status == 'confirmed' && $booking->check_out->isFuture() && $booking->check_out->diffInDays(now()) <= 3)
                    <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <p class="text-sm text-yellow-700">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Your stay ends in <strong>{{ $booking->check_out->diffInDays(now()) }} days</strong>.
                            <a href="{{ route('student.hostels.browse') }}" class="underline font-medium">Browse other hostels</a> for your next stay.
                        </p>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        {{ $bookings->links() }}
    </div>
@else
    <!-- Empty State -->
    <div class="bg-white rounded-lg shadow-sm p-12 text-center">
        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-calendar-times text-gray-400 text-4xl"></i>
        </div>
        <h3 class="text-xl font-semibold text-gray-800 mb-2">No Bookings Found</h3>
        <p class="text-gray-500 mb-6">You haven't made any hostel bookings yet.</p>
        <a href="{{ route('student.hostels.browse') }}"
           class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-building mr-2"></i>
            Browse Available Hostels
        </a>
    </div>
@endif

@push('scripts')
<script>
function confirmCancel() {
    return confirm('Are you sure you want to cancel this booking? This action cannot be undone.');
}

// Add animation for status changes
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const status = urlParams.get('status');

    if (status) {
        const filterSelect = document.querySelector('select[name="status"]');
        if (filterSelect) {
            filterSelect.value = status;
        }
    }
});
</script>
@endpush
@endsection
