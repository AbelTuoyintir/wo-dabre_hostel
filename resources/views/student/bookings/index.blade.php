@extends('layouts.student')

@section('title', 'My Bookings')
@section('content')

<style>
    /* Custom Styles */
    .stats-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    .stats-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #3b82f6, #8b5cf6);
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }
    .stats-card:hover::before {
        transform: scaleX(1);
    }
    .stats-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    }
    .stats-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }
    .booking-card {
        transition: all 0.3s ease;
        border: 1px solid #e5e7eb;
    }
    .booking-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 30px rgba(0,0,0,0.08);
        border-color: #93c5fd;
    }
    .status-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }
    .status-pending {
        background: #fef3c7;
        color: #92400e;
    }
    .status-pending .dot {
        background: #f59e0b;
        animation: pulse-dot 1.5s ease-in-out infinite;
    }
    .status-confirmed {
        background: #d1fae5;
        color: #065f46;
    }
    .status-confirmed .dot {
        background: #10b981;
    }
    .status-completed {
        background: #dbeafe;
        color: #1e40af;
    }
    .status-completed .dot {
        background: #3b82f6;
    }
    .status-cancelled {
        background: #fee2e2;
        color: #991b1b;
    }
    .status-cancelled .dot {
        background: #ef4444;
    }
    @keyframes pulse-dot {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.5; transform: scale(0.8); }
    }
    .timeline-item {
        position: relative;
        padding-left: 1.5rem;
    }
    .timeline-item::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e5e7eb;
    }
    .timeline-item:last-child::before {
        bottom: 50%;
    }
    .timeline-dot {
        position: absolute;
        left: -4px;
        top: 4px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #3b82f6;
        border: 2px solid white;
        box-shadow: 0 0 0 3px #3b82f6;
    }
    .filter-input {
        transition: all 0.3s ease;
        border: 1px solid #e5e7eb;
    }
    .filter-input:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        outline: none;
    }
    .empty-state {
        animation: fadeInUp 0.6s ease-out;
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
    .action-btn {
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    .action-btn::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255,255,255,0.2);
        transition: all 0.5s ease;
    }
    .action-btn:active::after {
        width: 200px;
        height: 200px;
        top: -50px;
        left: -50px;
    }
    .date-chip {
        background: #f3f4f6;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        color: #4b5563;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }
    @media (max-width: 640px) {
        .stats-grid {
            grid-template-columns: 1fr 1fr !important;
            gap: 0.75rem !important;
        }
        .booking-actions {
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        .booking-actions a, .booking-actions button {
            flex: 1;
            text-align: center;
            justify-content: center;
        }
    }
</style>

<!-- Header Section -->
<div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                <i class="fas fa-calendar-check text-blue-500 mr-3"></i>
                My Bookings
            </h1>
            <p class="text-gray-500 mt-1">Track and manage all your hostel accommodations</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('student.hostels.browse') }}"
               class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all shadow-md hover:shadow-lg transform hover:scale-105">
                <i class="fas fa-plus mr-2"></i>
                Book New Room
            </a>
            <button onclick="window.print()" 
                    class="inline-flex items-center px-5 py-2.5 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition">
                <i class="fas fa-print mr-2"></i>
                Print
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    @php
        $totalBookings = $bookings->total();
        $activeBookings = $bookings->where('status', 'confirmed')->where('check_out', '>=', now())->count();
        $pendingBookings = $bookings->where('status', 'pending')->count();
        $completedBookings = $bookings->where('status', 'completed')->count();
        $cancelledBookings = $bookings->where('status', 'cancelled')->count();
    @endphp

    <div class="stats-grid grid grid-cols-2 md:grid-cols-5 gap-4 mt-8 pt-6 border-t border-gray-100">
        <div class="stats-card rounded-xl p-4">
            <div class="flex items-center gap-3">
                <div class="stats-icon bg-blue-50 text-blue-600">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-900">{{ $totalBookings }}</div>
                    <div class="text-sm text-gray-500">Total Bookings</div>
                </div>
            </div>
        </div>
        <div class="stats-card rounded-xl p-4">
            <div class="flex items-center gap-3">
                <div class="stats-icon bg-green-50 text-green-600">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <div class="text-2xl font-bold text-green-600">{{ $activeBookings }}</div>
                    <div class="text-sm text-gray-500">Active</div>
                </div>
            </div>
        </div>
        <div class="stats-card rounded-xl p-4">
            <div class="flex items-center gap-3">
                <div class="stats-icon bg-yellow-50 text-yellow-600">
                    <i class="fas fa-clock"></i>
                </div>
                <div>
                    <div class="text-2xl font-bold text-yellow-600">{{ $pendingBookings }}</div>
                    <div class="text-sm text-gray-500">Pending</div>
                </div>
            </div>
        </div>
        <div class="stats-card rounded-xl p-4">
            <div class="flex items-center gap-3">
                <div class="stats-icon bg-blue-50 text-blue-600">
                    <i class="fas fa-check-double"></i>
                </div>
                <div>
                    <div class="text-2xl font-bold text-blue-600">{{ $completedBookings }}</div>
                    <div class="text-sm text-gray-500">Completed</div>
                </div>
            </div>
        </div>
        <div class="stats-card rounded-xl p-4">
            <div class="flex items-center gap-3">
                <div class="stats-icon bg-red-50 text-red-600">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div>
                    <div class="text-2xl font-bold text-red-600">{{ $cancelledBookings }}</div>
                    <div class="text-sm text-gray-500">Cancelled</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
    <form method="GET" action="{{ route('student.bookings') }}" class="flex flex-wrap items-end gap-4">
        <div class="flex-1 min-w-[180px]">
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select name="status" class="filter-input w-full px-4 py-2.5 rounded-xl bg-gray-50">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </div>
        <div class="flex-1 min-w-[180px]">
            <label class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
            <select name="sort" class="filter-input w-full px-4 py-2.5 rounded-xl bg-gray-50">
                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest First</option>
                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                <option value="checkin_asc" {{ request('sort') == 'checkin_asc' ? 'selected' : '' }}>Check-in (Earliest)</option>
                <option value="checkin_desc" {{ request('sort') == 'checkin_desc' ? 'selected' : '' }}>Check-in (Latest)</option>
            </select>
        </div>
        <div class="flex-1 min-w-[180px]">
            <label class="block text-sm font-medium text-gray-700 mb-1">Payment Status</label>
            <select name="payment_status" class="filter-input w-full px-4 py-2.5 rounded-xl bg-gray-50">
                <option value="">All</option>
                <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="failed" {{ request('payment_status') == 'failed' ? 'selected' : '' }}>Failed</option>
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition shadow-md hover:shadow-lg">
                <i class="fas fa-filter mr-2"></i>Apply
            </button>
            <a href="{{ route('student.bookings') }}" class="px-6 py-2.5 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition">
                <i class="fas fa-times mr-2"></i>Clear
            </a>
        </div>
    </form>
</div>

<!-- Bookings List -->
@if($bookings->count() > 0)
    <div class="space-y-4">
        @foreach($bookings as $booking)
            <div class="booking-card bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all">
                <!-- Header -->
                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start gap-4">
                            <!-- Hostel Image -->
                            <div class="relative flex-shrink-0">
                                @if($booking->hostel && $booking->hostel->primaryImage)
                                    <img src="{{ image_url($booking->hostel->primaryImage->image_path) }}" 
                                         alt="{{ $booking->hostel->name }}"
                                         class="w-24 h-24 object-cover rounded-xl shadow-md"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="w-24 h-24 bg-gray-100 rounded-xl hidden items-center justify-center shadow-md">
                                        <i class="fas fa-building text-gray-400 text-3xl"></i>
                                    </div>
                                @else
                                    <div class="w-24 h-24 bg-gray-100 rounded-xl flex items-center justify-center shadow-md">
                                        <i class="fas fa-building text-gray-400 text-3xl"></i>
                                    </div>
                                @endif
                                @if($booking->status == 'confirmed' && $booking->check_in->isFuture())
                                    <div class="absolute -top-1 -right-1">
                                        <span class="flex h-3 w-3">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                                        </span>
                                    </div>
                                @endif
                            </div>

                            <!-- Main Info -->
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-2 mb-1">
                                    <h3 class="text-lg font-bold text-gray-900 truncate">
                                        {{ $booking->hostel->name ?? 'Hostel Unavailable' }}
                                    </h3>
                                    <span class="status-badge status-{{ $booking->status }}">
                                        <span class="dot w-1.5 h-1.5 rounded-full mr-1.5"></span>
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                    @if($booking->payment_status == 'paid')
                                        <span class="status-badge bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>Paid
                                        </span>
                                    @elseif($booking->payment_status == 'pending' && $booking->status != 'cancelled')
                                        <span class="status-badge bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-clock mr-1"></i>Payment Pending
                                        </span>
                                    @endif
                                </div>
                                
                                <p class="text-sm text-gray-500 flex items-center gap-2">
                                    <i class="fas fa-map-marker-alt text-red-400"></i>
                                    {{ $booking->hostel->location ?? 'Location N/A' }}
                                </p>

                                <!-- Booking Details Grid -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mt-3">
                                    <div class="flex items-center text-sm text-gray-600">
                                        <i class="fas fa-calendar-alt text-blue-500 w-5"></i>
                                        <span>
                                            <span class="font-medium">{{ $booking->check_in->format('M d, Y') }}</span>
                                            <i class="fas fa-arrow-right mx-1.5 text-gray-400"></i>
                                            <span class="font-medium">{{ $booking->check_out->format('M d, Y') }}</span>
                                        </span>
                                    </div>
                                    <div class="flex items-center text-sm text-gray-600">
                                        <i class="fas fa-clock text-purple-500 w-5"></i>
                                        <span>{{ $booking->check_in->diffInDays($booking->check_out) }} nights</span>
                                    </div>
                                    <div class="flex items-center text-sm text-gray-600">
                                        <i class="fas fa-door-open text-green-500 w-5"></i>
                                        <span>Room {{ $booking->room->number ?? 'N/A' }} · {{ $booking->room->capacity ?? '?' }} bed(s)</span>
                                    </div>
                                    <div class="flex items-center text-sm font-semibold">
                                        <i class="fas fa-tag text-purple-500 w-5"></i>
                                        <span class="text-gray-900">₵{{ number_format($booking->total_amount, 2) }}</span>
                                    </div>
                                </div>

                                <!-- Additional Info -->
                                <div class="flex flex-wrap items-center gap-3 mt-2 text-xs text-gray-400">
                                    <span>
                                        <i class="fas fa-hashtag mr-1"></i>
                                        #{{ $booking->booking_reference ?? 'N/A' }}
                                    </span>
                                    <span>•</span>
                                    <span>
                                        <i class="far fa-calendar-check mr-1"></i>
                                        Booked {{ $booking->created_at->format('M d, Y') }}
                                    </span>
                                    @if($booking->status == 'confirmed')
                                        <span>•</span>
                                        <span class="text-green-600">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Check-in: {{ $booking->check_in->diffInDays(now()) <= 0 ? 'Today!' : $booking->check_in->diffInDays(now()) . ' days' }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="booking-actions flex flex-wrap gap-2 lg:flex-col lg:items-stretch">
                        <a href="{{ route('student.bookings.show', $booking->uuid ?? $booking->id) }}"
                           class="action-btn px-4 py-2 bg-blue-600 text-white text-sm rounded-xl hover:bg-blue-700 transition shadow-md text-center">
                            <i class="fas fa-eye mr-1"></i> View Details
                        </a>
                        
                        @if($booking->status == 'pending')
                            <a href="{{ route('payment.initialize', $booking) }}"
                               class="action-btn px-4 py-2 bg-gradient-to-r from-green-600 to-green-700 text-white text-sm rounded-xl hover:from-green-700 hover:to-green-800 transition shadow-md text-center">
                                <i class="fas fa-credit-card mr-1"></i> Pay Now
                            </a>
                        @endif

                        @if($booking->status == 'confirmed' && $booking->check_in->isFuture())
                            <form action="{{ route('student.bookings.cancel', $booking) }}"
                                  method="POST"
                                  onsubmit="return confirmCancel()"
                                  class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        class="action-btn w-full px-4 py-2 border-2 border-red-300 text-red-600 text-sm rounded-xl hover:bg-red-50 transition text-center">
                                    <i class="fas fa-times mr-1"></i> Cancel
                                </button>
                            </form>
                        @endif

                        @if($booking->status == 'confirmed' && $booking->payment_status == 'paid')
                            <button onclick="downloadInvoice('{{ $booking->id }}')"
                                    class="action-btn px-4 py-2 border border-gray-300 text-gray-700 text-sm rounded-xl hover:bg-gray-50 transition text-center">
                                <i class="fas fa-file-pdf mr-1"></i> Invoice
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Notifications -->
                @if($booking->status == 'confirmed')
                    @if($booking->check_in->isFuture() && $booking->check_in->diffInDays(now()) <= 3)
                        <div class="mt-4 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl">
                            <div class="flex items-start gap-3">
                                <i class="fas fa-info-circle text-blue-600 text-lg mt-0.5"></i>
                                <div>
                                    <p class="text-sm text-blue-800">
                                        <strong>Upcoming Check-in!</strong> Your stay starts in 
                                        <strong>{{ $booking->check_in->diffInDays(now()) }} day{{ $booking->check_in->diffInDays(now()) > 1 ? 's' : '' }}</strong>.
                                    </p>
                                    <p class="text-xs text-blue-600 mt-1">
                                        <i class="fas fa-id-card mr-1"></i>
                                        Please bring a valid ID and your booking confirmation.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($booking->check_out->isFuture() && $booking->check_out->diffInDays(now()) <= 3)
                        <div class="mt-4 p-4 bg-gradient-to-r from-yellow-50 to-orange-50 border border-yellow-200 rounded-xl">
                            <div class="flex items-start gap-3">
                                <i class="fas fa-exclamation-triangle text-yellow-600 text-lg mt-0.5"></i>
                                <div>
                                    <p class="text-sm text-yellow-800">
                                        <strong>Stay Ending Soon!</strong> Your checkout is in 
                                        <strong>{{ $booking->check_out->diffInDays(now()) }} day{{ $booking->check_out->diffInDays(now()) > 1 ? 's' : '' }}</strong>.
                                    </p>
                                    <p class="text-xs text-yellow-600 mt-1">
                                        <i class="fas fa-building mr-1"></i>
                                        <a href="{{ route('student.hostels.browse') }}" class="underline font-medium hover:text-yellow-800">
                                            Browse other hostels
                                        </a> for your next stay.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        {{ $bookings->appends(request()->query())->links() }}
    </div>
@else
    <!-- Empty State -->
    <div class="bg-white rounded-2xl shadow-lg p-16 text-center empty-state">
        <div class="w-28 h-28 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-calendar-times text-gray-400 text-5xl"></i>
        </div>
        <h3 class="text-2xl font-bold text-gray-800 mb-2">No Bookings Yet</h3>
        <p class="text-gray-500 max-w-md mx-auto mb-8">
            You haven't made any hostel bookings. Start exploring available hostels and find your perfect accommodation.
        </p>
        <div class="flex flex-wrap justify-center gap-4">
            <a href="{{ route('student.hostels.browse') }}"
               class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl hover:from-blue-700 hover:to-blue-800 transition shadow-md hover:shadow-lg">
                <i class="fas fa-building mr-2"></i>
                Browse Hostels
            </a>
            <a href="{{ route('student.hostels.browse') }}?featured=true"
               class="inline-flex items-center px-8 py-3 border-2 border-blue-600 text-blue-600 rounded-xl hover:bg-blue-50 transition">
                <i class="fas fa-star mr-2"></i>
                View Featured
            </a>
        </div>
    </div>
@endif

<!-- Floating Action Button -->
<div class="fixed bottom-6 right-6 z-50">
    <a href="{{ route('student.hostels.browse') }}"
       class="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-full shadow-xl hover:shadow-2xl hover:scale-105 transition-all duration-300">
        <i class="fas fa-plus"></i>
        <span class="font-medium">Book Now</span>
    </a>
</div>

@push('scripts')
<script>
function confirmCancel() {
    return confirm('⚠️ Are you sure you want to cancel this booking?\n\nThis action cannot be undone and may affect your student housing record.');
}

function downloadInvoice(bookingId) {
    // Implement invoice download logic
    // For now, show a toast notification
    showToast('Invoice download initiated', 'success');
}

// Toast notification helper
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 px-6 py-3 rounded-xl shadow-lg text-white z-50 transform transition-all duration-500 ${
        type === 'success' ? 'bg-green-600' : 
        type === 'error' ? 'bg-red-600' : 
        'bg-blue-600'
    }`;
    toast.innerHTML = `
        <div class="flex items-center gap-2">
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
            <span>${message}</span>
        </div>
    `;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.transform = 'translateX(100px)';
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 500);
    }, 3000);
}

// Auto-dismiss alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });
});
</script>
@endpush
@endsection