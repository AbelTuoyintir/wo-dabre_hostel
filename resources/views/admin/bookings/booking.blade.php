@extends('layouts.app')

@section('title', 'My Bookings')
@section('page-title', 'My Booking History')

@section('content')
<div class="bookings-dashboard">
    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon active-booking">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="stat-content">
                <span class="stat-value">{{ $bookings->where('booking_status', 'confirmed')->count() }}</span>
                <span class="stat-label">Active Bookings</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon pending-booking">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-content">
                <span class="stat-value">{{ $bookings->where('booking_status', 'pending')->count() }}</span>
                <span class="stat-label">Pending</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon completed-booking">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <span class="stat-value">{{ $bookings->where('booking_status', 'checked_out')->count() }}</span>
                <span class="stat-label">Completed</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon total-spent">
                <i class="fas fa-coins"></i>
            </div>
            <div class="stat-content">
                <span class="stat-value">¢{{ number_format($bookings->sum('total_amount'), 2) }}</span>
                <span class="stat-label">Total Spent</span>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="content-card">
        <div class="card-header">
            <div class="header-left">
                <h4 class="card-title">
                    <i class="fas fa-list-alt me-2"></i>Your Bookings
                </h4>
                <p class="card-subtitle">Manage and track all your hostel bookings</p>
            </div>
            <div class="header-right">
                <a href="{{ route('admin.bookings.create') }}" class="btn-primary">
                    <i class="fas fa-plus-circle"></i>
                    <span>New Booking</span>
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="filters-section">
            <div class="filter-tabs">
                <button class="filter-tab active" data-filter="all">All</button>
                <button class="filter-tab" data-filter="pending">Pending</button>
                <button class="filter-tab" data-filter="confirmed">Confirmed</button>
                <button class="filter-tab" data-filter="checked_in">Checked In</button>
                <button class="filter-tab" data-filter="checked_out">Checked Out</button>
                <button class="filter-tab" data-filter="cancelled">Cancelled</button>
            </div>
            <div class="search-box">
                <i class="fas fa-search search-icon"></i>
                <input type="text" id="searchInput" placeholder="Search by booking ID or hostel...">
            </div>
        </div>

        <!-- Bookings List -->
        <div class="bookings-list">
            @forelse($bookings as $booking)
                <div class="booking-item" data-status="{{ $booking->booking_status }}">
                    <div class="booking-header">
                        <div class="booking-id">
                            <span class="id-label">Booking ID</span>
                            <span class="id-value">{{ $booking->booking_number }}</span>
                        </div>
                        <div class="booking-status">
                            @php
                                $statusClasses = [
                                    'pending' => 'status-pending',
                                    'confirmed' => 'status-confirmed',
                                    'checked_in' => 'status-checkedin',
                                    'checked_out' => 'status-checkedout',
                                    'cancelled' => 'status-cancelled'
                                ];
                                $statusIcons = [
                                    'pending' => 'clock',
                                    'confirmed' => 'check-circle',
                                    'checked_in' => 'door-open',
                                    'checked_out' => 'check-double',
                                    'cancelled' => 'times-circle'
                                ];
                            @endphp
                            <span class="status-badge {{ $statusClasses[$booking->booking_status] ?? 'status-pending' }}">
                                <i class="fas fa-{{ $statusIcons[$booking->booking_status] ?? 'clock' }}"></i>
                                {{ ucfirst(str_replace('_', ' ', $booking->booking_status)) }}
                            </span>
                        </div>
                    </div>

                    <div class="booking-body">
                        <div class="hostel-info">
                            <div class="hostel-avatar">
                                @if($booking->hostel->image)
                                    <img src="{{ Storage::url($booking->hostel->image) }}" alt="{{ $booking->hostel->name }}">
                                @else
                                    <div class="avatar-placeholder">
                                        <i class="fas fa-building"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="hostel-details">
                                <h5 class="hostel-name">{{ $booking->hostel->name }}</h5>
                                <p class="hostel-location">
                                    <i class="fas fa-map-marker-alt"></i>
                                    {{ $booking->hostel->location }}
                                </p>
                                <p class="room-info">
                                    <i class="fas fa-door-open"></i>
                                    Room {{ $booking->room_number }}
                                </p>
                            </div>
                        </div>

                        <div class="booking-dates">
                            <div class="date-box">
                                <span class="date-label">Check In</span>
                                <span class="date-value">{{ $booking->check_in_date->format('d M Y') }}</span>
                                <span class="date-time">{{ $booking->check_in_date->format('h:i A') }}</span>
                            </div>
                            <div class="date-arrow">
                                <i class="fas fa-long-arrow-alt-right"></i>
                            </div>
                            <div class="date-box">
                                <span class="date-label">Check Out</span>
                                <span class="date-value">{{ $booking->check_out_date->format('d M Y') }}</span>
                                <span class="date-time">{{ $booking->check_out_date->format('h:i A') }}</span>
                            </div>
                        </div>

                        <div class="payment-info">
                            <div class="amount">
                                <span class="amount-label">Total Amount</span>
                                <span class="amount-value">¢{{ number_format($booking->total_amount, 2) }}</span>
                            </div>
                            <div class="payment-status">
                                @if($booking->payment_status === 'paid')
                                    <span class="payment-badge paid">
                                        <i class="fas fa-check-circle"></i> Paid
                                    </span>
                                @elseif($booking->payment_status === 'partial')
                                    <span class="payment-badge partial">
                                        <i class="fas fa-adjust"></i> Partial
                                    </span>
                                @else
                                    <span class="payment-badge pending">
                                        <i class="fas fa-hourglass"></i> Pending
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="booking-footer">
                        <div class="booking-actions">
                            <a href="{{ route('bookings.show', $booking) }}" class="action-btn view-btn">
                                <i class="fas fa-eye"></i>
                                <span>View Details</span>
                            </a>

                            @if($booking->booking_status === 'pending' && $booking->payment_status === 'pending')
                                <a href="{{ route('payment.process', $booking) }}" class="action-btn pay-btn">
                                    <i class="fas fa-credit-card"></i>
                                    <span>Pay Now</span>
                                </a>
                            @endif

                            @if(in_array($booking->booking_status, ['pending', 'confirmed']))
                                <button class="action-btn cancel-btn" onclick="cancelBooking({{ $booking->id }})">
                                    <i class="fas fa-times"></i>
                                    <span>Cancel</span>
                                </button>
                            @endif

                            @if($booking->booking_status === 'confirmed' && $booking->check_in_date->isToday())
                                <button class="action-btn checkin-btn" onclick="checkIn({{ $booking->id }})">
                                    <i class="fas fa-sign-in-alt"></i>
                                    <span>Check In</span>
                                </button>
                            @endif
                        </div>

                        <div class="booking-meta">
                            <span class="created-at">
                                <i class="far fa-clock"></i>
                                Booked {{ $booking->created_at->diffForHumans() }}
                            </span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-calendar-times"></i>
                    </div>
                    <h4>No Bookings Found</h4>
                    <p>You haven't made any bookings yet. Start by exploring available hostels.</p>
                    <a href="{{ route('hostels.index') }}" class="btn-primary">
                        <i class="fas fa-search"></i> Browse Hostels
                    </a>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($bookings->hasPages())
            <div class="pagination-wrapper">
                {{ $bookings->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>

<style>
:root {
    --primary: #4f46e5;
    --primary-dark: #4338ca;
    --primary-soft: #e0e7ff;
    --success: #10b981;
    --success-soft: #d1fae5;
    --warning: #f59e0b;
    --warning-soft: #fef3c7;
    --danger: #ef4444;
    --danger-soft: #fee2e2;
    --info: #3b82f6;
    --info-soft: #dbeafe;
    --gray-50: #f9fafb;
    --gray-100: #f3f4f6;
    --gray-200: #e5e7eb;
    --gray-300: #d1d5db;
    --gray-400: #9ca3af;
    --gray-500: #6b7280;
    --gray-600: #4b5563;
    --gray-700: #374151;
    --gray-800: #1f2937;
    --gray-900: #111827;
    --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    --radius: 0.5rem;
    --radius-lg: 0.75rem;
    --radius-xl: 1rem;
    --radius-2xl: 1.5rem;
}

.bookings-dashboard {
    max-width: 1400px;
    margin: 2rem auto;
    padding: 0 1.5rem;
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    border-radius: var(--radius-xl);
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    box-shadow: var(--shadow-md);
    border: 1px solid var(--gray-100);
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-xl);
}

.stat-icon {
    width: 56px;
    height: 56px;
    border-radius: var(--radius-lg);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.stat-icon.active-booking {
    background: linear-gradient(135deg, #4f46e5, #818cf8);
    color: white;
}

.stat-icon.pending-booking {
    background: linear-gradient(135deg, #f59e0b, #fbbf24);
    color: white;
}

.stat-icon.completed-booking {
    background: linear-gradient(135deg, #10b981, #34d399);
    color: white;
}

.stat-icon.total-spent {
    background: linear-gradient(135deg, #8b5cf6, #a78bfa);
    color: white;
}

.stat-content {
    display: flex;
    flex-direction: column;
}

.stat-value {
    font-size: 2rem;
    font-weight: 800;
    color: var(--gray-900);
    line-height: 1.2;
}

.stat-label {
    font-size: 0.875rem;
    color: var(--gray-500);
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

/* Content Card */
.content-card {
    background: white;
    border-radius: var(--radius-2xl);
    box-shadow: var(--shadow-lg);
    border: 1px solid var(--gray-100);
    overflow: hidden;
}

.card-header {
    padding: 1.5rem 2rem;
    border-bottom: 1px solid var(--gray-200);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
    background: linear-gradient(to right, var(--gray-50), white);
}

.card-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--gray-900);
    margin: 0 0 0.25rem 0;
    display: flex;
    align-items: center;
}

.card-subtitle {
    color: var(--gray-500);
    margin: 0;
    font-size: 0.875rem;
}

.btn-primary {
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: var(--radius-lg);
    text-decoration: none;
    font-weight: 600;
    font-size: 0.875rem;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: var(--shadow-md);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-xl);
}

/* Filters Section */
.filters-section {
    padding: 1.5rem 2rem;
    border-bottom: 1px solid var(--gray-200);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
    background: var(--gray-50);
}

.filter-tabs {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.filter-tab {
    padding: 0.5rem 1rem;
    border: 1px solid var(--gray-200);
    background: white;
    border-radius: var(--radius-lg);
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--gray-600);
    cursor: pointer;
    transition: all 0.2s;
}

.filter-tab:hover {
    border-color: var(--primary);
    color: var(--primary);
}

.filter-tab.active {
    background: var(--primary);
    border-color: var(--primary);
    color: white;
}

.search-box {
    position: relative;
    min-width: 300px;
}

.search-icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--gray-400);
}

.search-box input {
    width: 100%;
    padding: 0.75rem 1rem 0.75rem 2.75rem;
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-lg);
    font-size: 0.875rem;
    transition: all 0.2s;
}

.search-box input:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px var(--primary-soft);
}

/* Bookings List */
.bookings-list {
    padding: 1.5rem;
}

.booking-item {
    background: white;
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-xl);
    margin-bottom: 1.5rem;
    transition: all 0.3s;
    position: relative;
    overflow: hidden;
}

.booking-item:hover {
    box-shadow: var(--shadow-lg);
    border-color: transparent;
}

.booking-item::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    width: 4px;
    height: 100%;
    background: var(--primary);
    opacity: 0;
    transition: opacity 0.3s;
}

.booking-item:hover::before {
    opacity: 1;
}

.booking-header {
    padding: 1rem 1.5rem;
    background: var(--gray-50);
    border-bottom: 1px solid var(--gray-200);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.booking-id {
    display: flex;
    flex-direction: column;
}

.id-label {
    font-size: 0.75rem;
    color: var(--gray-500);
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.id-value {
    font-size: 1rem;
    font-weight: 700;
    color: var(--gray-900);
    font-family: 'Courier New', monospace;
}

.status-badge {
    padding: 0.5rem 1rem;
    border-radius: 2rem;
    font-size: 0.75rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
}

.status-pending {
    background: var(--warning-soft);
    color: var(--warning);
}

.status-confirmed {
    background: var(--success-soft);
    color: var(--success);
}

.status-checkedin {
    background: var(--info-soft);
    color: var(--info);
}

.status-checkedout {
    background: var(--gray-200);
    color: var(--gray-600);
}

.status-cancelled {
    background: var(--danger-soft);
    color: var(--danger);
}

.booking-body {
    padding: 1.5rem;
    display: grid;
    grid-template-columns: 2fr 2fr 1fr;
    gap: 1.5rem;
    align-items: center;
}

@media (max-width: 1024px) {
    .booking-body {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
}

.hostel-info {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.hostel-avatar {
    width: 80px;
    height: 80px;
    border-radius: var(--radius-lg);
    overflow: hidden;
    flex-shrink: 0;
}

.hostel-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.avatar-placeholder {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
}

.hostel-details {
    flex: 1;
}

.hostel-name {
    font-size: 1.125rem;
    font-weight: 700;
    color: var(--gray-900);
    margin: 0 0 0.25rem 0;
}

.hostel-location {
    font-size: 0.875rem;
    color: var(--gray-500);
    margin: 0 0 0.5rem 0;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.room-info {
    font-size: 0.875rem;
    color: var(--gray-600);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.booking-dates {
    display: flex;
    align-items: center;
    gap: 1rem;
    background: var(--gray-50);
    padding: 1rem;
    border-radius: var(--radius-lg);
}

.date-box {
    display: flex;
    flex-direction: column;
    text-align: center;
}

.date-label {
    font-size: 0.75rem;
    color: var(--gray-500);
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.date-value {
    font-size: 1.125rem;
    font-weight: 700;
    color: var(--gray-900);
}

.date-time {
    font-size: 0.75rem;
    color: var(--gray-400);
}

.date-arrow {
    color: var(--primary);
    font-size: 1.25rem;
}

.payment-info {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    padding: 1rem;
    background: var(--gray-50);
    border-radius: var(--radius-lg);
}

.amount {
    display: flex;
    flex-direction: column;
}

.amount-label {
    font-size: 0.75rem;
    color: var(--gray-500);
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.amount-value {
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--primary);
}

.payment-badge {
    padding: 0.375rem 0.75rem;
    border-radius: 2rem;
    font-size: 0.75rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    width: fit-content;
}

.payment-badge.paid {
    background: var(--success-soft);
    color: var(--success);
}

.payment-badge.partial {
    background: var(--warning-soft);
    color: var(--warning);
}

.payment-badge.pending {
    background: var(--gray-200);
    color: var(--gray-600);
}

.booking-footer {
    padding: 1rem 1.5rem;
    background: var(--gray-50);
    border-top: 1px solid var(--gray-200);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.booking-actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.action-btn {
    padding: 0.5rem 1rem;
    border-radius: var(--radius-lg);
    font-size: 0.75rem;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
}

.view-btn {
    background: var(--gray-200);
    color: var(--gray-700);
}

.view-btn:hover {
    background: var(--gray-300);
}

.pay-btn {
    background: var(--success);
    color: white;
}

.pay-btn:hover {
    background: var(--success);
    filter: brightness(110%);
}

.cancel-btn {
    background: var(--danger-soft);
    color: var(--danger);
}

.cancel-btn:hover {
    background: var(--danger);
    color: white;
}

.checkin-btn {
    background: var(--info);
    color: white;
}

.booking-meta {
    color: var(--gray-500);
    font-size: 0.75rem;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 4rem 2rem;
}

.empty-icon {
    width: 120px;
    height: 120px;
    margin: 0 auto 1.5rem;
    background: var(--gray-100);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    color: var(--gray-400);
}

.empty-state h4 {
    color: var(--gray-700);
    margin-bottom: 0.5rem;
    font-size: 1.25rem;
}

.empty-state p {
    color: var(--gray-500);
    margin-bottom: 2rem;
}

/* Pagination */
.pagination-wrapper {
    padding: 1.5rem 2rem;
    border-top: 1px solid var(--gray-200);
    display: flex;
    justify-content: center;
}

/* Animations */
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.booking-item {
    animation: slideIn 0.3s ease-out;
}

/* Responsive */
@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }

    .filters-section {
        flex-direction: column;
        align-items: stretch;
    }

    .search-box {
        min-width: auto;
    }

    .booking-dates {
        flex-direction: column;
    }

    .date-arrow {
        transform: rotate(90deg);
    }
}
</style>

<script>
// Filter bookings by status
document.querySelectorAll('.filter-tab').forEach(tab => {
    tab.addEventListener('click', function() {
        // Update active tab
        document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
        this.classList.add('active');

        const filter = this.dataset.filter;
        const bookings = document.querySelectorAll('.booking-item');

        bookings.forEach(booking => {
            if (filter === 'all' || booking.dataset.status === filter) {
                booking.style.display = 'block';
            } else {
                booking.style.display = 'none';
            }
        });
    });
});

// Search functionality
document.getElementById('searchInput').addEventListener('keyup', function() {
    const searchTerm = this.value.toLowerCase();
    const bookings = document.querySelectorAll('.booking-item');

    bookings.forEach(booking => {
        const bookingId = booking.querySelector('.id-value').textContent.toLowerCase();
        const hostelName = booking.querySelector('.hostel-name').textContent.toLowerCase();

        if (bookingId.includes(searchTerm) || hostelName.includes(searchTerm)) {
            booking.style.display = 'block';
        } else {
            booking.style.display = 'none';
        }
    });
});

// Cancel booking function
function cancelBooking(bookingId) {
    if (confirm('Are you sure you want to cancel this booking?')) {
        // Submit cancel form
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/bookings/${bookingId}/cancel`;

        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';

        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'PATCH';

        form.appendChild(csrfInput);
        form.appendChild(methodInput);
        document.body.appendChild(form);
        form.submit();
    }
}

// Check-in function
function checkIn(bookingId) {
    if (confirm('Proceed with check-in?')) {
        // Submit check-in form
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/bookings/${bookingId}/checkin`;

        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';

        form.appendChild(csrfInput);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection
