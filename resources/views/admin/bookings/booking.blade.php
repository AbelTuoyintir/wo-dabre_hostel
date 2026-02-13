@extends('layouts.app')

@section('title', 'Bookings')
@section('page-title', 'Manage Bookings')

@section('content')
<div class="bookings-container">
    <!-- Stats Overview -->
    <div class="stats-grid mb-4">
        <div class="stat-card">
            <div class="stat-icon bg-primary-soft">
                <i class="fas fa-calendar-check text-primary"></i>
            </div>
            <div class="stat-content">
                <span class="stat-value">{{ $bookings->where('booking_status', 'approved')->count() }}</span>
                <span class="stat-label">Active Bookings</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-warning-soft">
                <i class="fas fa-clock text-warning"></i>
            </div>
            <div class="stat-content">
                <span class="stat-value">{{ $bookings->where('booking_status', 'pending')->count() }}</span>
                <span class="stat-label">Pending Approval</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-success-soft">
                <i class="fas fa-naira-sign text-success"></i>
            </div>
            <div class="stat-content">
                <span class="stat-value">¢{{ number_format($bookings->where('payment_status', 'paid')->sum('total_amount'), 0) }}</span>
                <span class="stat-label">Revenue This Month</span>
            </div>
        </div>
    </div>

    <!-- Main Card -->
    <div class="modern-card">
        <div class="card-header-modern">
            <div class="header-left">
                <h5 class="card-title">
                    <i class="fas fa-list-check me-2"></i>All Bookings
                </h5>
                <span class="booking-count">{{ $bookings->total() }} records found</span>
            </div>

            <div class="header-actions">
                <div class="search-filter-group">
                    <form method="GET" class="filter-form">
                        <div class="filter-wrapper">
                            <select name="status" class="modern-select" onchange="this.form.submit()">
                                <option value="">All Statuses</option>
                                <option value="pending" {{ request('status')=='pending' ? 'selected' : '' }}>⏳ Pending</option>
                                <option value="approved" {{ request('status')=='approved' ? 'selected' : '' }}>✅ Approved</option>
                                <option value="rejected" {{ request('status')=='rejected' ? 'selected' : '' }}>❌ Rejected</option>
                            </select>
                            <button class="btn-filter" type="submit">
                                <i class="fas fa-filter"></i>
                            </button>
                        </div>
                    </form>

                    <button class="btn-export" onclick="exportBookings()">
                        <i class="fas fa-download"></i>
                        <span>Export</span>
                    </button>

                    <!-- New Add Booking Button -->
                    <a href="{{ route('admin.bookings.create') }}" class="btn-add-booking">
                        <i class="fas fa-plus-circle"></i>
                        <span>Add Booking</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="table-container">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th class="th-booking">Booking ID</th>
                        <th class="th-user">User</th>
                        <th class="th-hostel">Hostel</th>
                        <th class="th-status">Status</th>
                        <th class="th-payment">Payment</th>
                        <th class="th-amount">Amount</th>
                        <th class="th-date">Created</th>
                        <th class="th-actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($bookings as $booking)
                        <tr class="booking-row" data-booking-id="{{ $booking->id }}">
                            <td>
                                <span class="booking-id">#{{ str_pad($booking->id, 4, '0', STR_PAD_LEFT) }}</span>
                            </td>
                            <td>
                                <div class="user-cell">
                                    <div class="user-avatar">
                                        {{ substr($booking->user->name ?? 'U', 0, 1) }}
                                    </div>
                                    <div class="user-info">
                                        <span class="user-name">{{ $booking->user->name ?? 'Unknown User' }}</span>
                                        <span class="user-email">{{ $booking->user->email ?? '-' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="hostel-cell">
                                    <i class="fas fa-building text-muted"></i>
                                    <span>{{ $booking->hostel->name ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td>
                                @php
                                    $statusConfig = [
                                        'approved' => ['class' => 'status-approved', 'icon' => 'check-circle', 'label' => 'Approved'],
                                        'pending' => ['class' => 'status-pending', 'icon' => 'clock', 'label' => 'Pending'],
                                        'rejected' => ['class' => 'status-rejected', 'icon' => 'x-circle', 'label' => 'Rejected']
                                    ];
                                    $config = $statusConfig[$booking->booking_status] ?? $statusConfig['pending'];
                                @endphp
                                <span class="status-badge {{ $config['class'] }}">
                                    <i class="fas fa-{{ $config['icon'] }}"></i>
                                    {{ $config['label'] }}
                                </span>
                            </td>
                            <td>
                                @if($booking->payment_status === 'paid')
                                    <span class="payment-badge paid">
                                        <i class="fas fa-check"></i> Paid
                                    </span>
                                @else
                                    <span class="payment-badge unpaid">
                                        <i class="fas fa-hourglass-half"></i> Pending
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span class="amount">¢{{ number_format($booking->total_amount, 2) }}</span>
                            </td>
                            <td>
                                <div class="date-cell">
                                    <span class="date-main">{{ $booking->created_at->format('d M Y') }}</span>
                                    <span class="date-sub">{{ $booking->created_at->format('h:i A') }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-action view" title="View Details" onclick="viewBooking({{ $booking->id }})">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @if($booking->booking_status === 'pending')
                                        <button class="btn-action approve" title="Approve" onclick="approveBooking({{ $booking->id }})">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button class="btn-action reject" title="Reject" onclick="rejectBooking({{ $booking->id }})">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="empty-state">
                                    <div class="empty-icon">
                                        <i class="fas fa-inbox"></i>
                                    </div>
                                    <h6>No bookings found</h6>
                                    <p>Try adjusting your filters or check back later</p>
                                    <a href="{{ route('admin.bookings.create') }}" class="empty-state-btn">
                                        <i class="fas fa-plus-circle"></i> Create New Booking
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($bookings->hasPages())
            <div class="pagination-modern">
                {{ $bookings->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>

<style>
:root {
    --primary: #4f46e5;
    --primary-soft: rgba(79, 70, 229, 0.1);
    --success: #10b981;
    --success-soft: rgba(16, 185, 129, 0.1);
    --warning: #f59e0b;
    --warning-soft: rgba(245, 158, 11, 0.1);
    --danger: #ef4444;
    --danger-soft: rgba(239, 68, 68, 0.1);
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
    --radius: 0.5rem;
    --radius-lg: 0.75rem;
    --radius-xl: 1rem;
}

.bookings-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 1rem;
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.stat-card {
    background: white;
    border-radius: var(--radius-lg);
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    box-shadow: var(--shadow);
    border: 1px solid var(--gray-100);
    transition: transform 0.2s, box-shadow 0.2s;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: var(--radius);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}

.bg-primary-soft { background: var(--primary-soft); }
.bg-warning-soft { background: var(--warning-soft); }
.bg-success-soft { background: var(--success-soft); }

.text-primary { color: var(--primary); }
.text-warning { color: var(--warning); }
.text-success { color: var(--success); }

.stat-content {
    display: flex;
    flex-direction: column;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--gray-900);
    line-height: 1;
}

.stat-label {
    font-size: 0.875rem;
    color: var(--gray-500);
    margin-top: 0.25rem;
}

/* Modern Card */
.modern-card {
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-lg);
    border: 1px solid var(--gray-100);
    overflow: hidden;
}

.card-header-modern {
    padding: 1.5rem 2rem;
    border-bottom: 1px solid var(--gray-100);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
    background: linear-gradient(to right, var(--gray-50), white);
}

.header-left {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.card-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--gray-900);
    margin: 0;
    display: flex;
    align-items: center;
}

.booking-count {
    font-size: 0.875rem;
    color: var(--gray-500);
}

.header-actions {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.search-filter-group {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.filter-form {
    margin: 0;
}

.filter-wrapper {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: var(--gray-50);
    padding: 0.25rem;
    border-radius: var(--radius);
    border: 1px solid var(--gray-200);
}

.modern-select {
    border: none;
    background: transparent;
    padding: 0.5rem 2rem 0.5rem 0.75rem;
    font-size: 0.875rem;
    color: var(--gray-700);
    cursor: pointer;
    outline: none;
    appearance: none;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
    background-position: right 0.5rem center;
    background-repeat: no-repeat;
    background-size: 1.5em 1.5em;
    min-width: 140px;
}

.btn-filter {
    background: white;
    border: none;
    padding: 0.5rem 0.75rem;
    border-radius: calc(var(--radius) - 0.125rem);
    color: var(--gray-600);
    cursor: pointer;
    transition: all 0.2s;
    box-shadow: var(--shadow-sm);
}

.btn-filter:hover {
    background: var(--primary);
    color: white;
    transform: translateY(-1px);
}

.btn-export {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.625rem 1rem;
    background: white;
    border: 1px solid var(--gray-200);
    border-radius: var(--radius);
    color: var(--gray-700);
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    box-shadow: var(--shadow-sm);
}

.btn-export:hover {
    border-color: var(--primary);
    color: var(--primary);
    transform: translateY(-1px);
    box-shadow: var(--shadow);
}

/* New Add Booking Button Styles */
.btn-add-booking {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.625rem 1.25rem;
    background: linear-gradient(135deg, var(--primary), #7c3aed);
    border: none;
    border-radius: var(--radius);
    color: white;
    font-size: 0.875rem;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.3s;
    box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2);
}

.btn-add-booking:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 10px -1px rgba(79, 70, 229, 0.3);
    color: white;
}

.btn-add-booking i {
    font-size: 1rem;
}

/* Table */
.table-container {
    overflow-x: auto;
    padding: 0 1rem;
}

.modern-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 0.875rem;
}

.modern-table thead th {
    padding: 1rem 1.25rem;
    text-align: left;
    font-weight: 600;
    color: var(--gray-600);
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.05em;
    border-bottom: 2px solid var(--gray-100);
    white-space: nowrap;
}

.modern-table tbody tr {
    transition: background-color 0.2s;
}

.modern-table tbody tr:hover {
    background-color: var(--gray-50);
}

.modern-table tbody td {
    padding: 1.25rem;
    border-bottom: 1px solid var(--gray-100);
    vertical-align: middle;
}

.booking-id {
    font-family: 'Courier New', monospace;
    font-weight: 600;
    color: var(--primary);
    background: var(--primary-soft);
    padding: 0.25rem 0.75rem;
    border-radius: var(--radius);
    font-size: 0.875rem;
}

/* User Cell */
.user-cell {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.user-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary), #7c3aed);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.875rem;
    flex-shrink: 0;
}

.user-info {
    display: flex;
    flex-direction: column;
}

.user-name {
    font-weight: 600;
    color: var(--gray-900);
}

.user-email {
    font-size: 0.75rem;
    color: var(--gray-500);
}

/* Hostel Cell */
.hostel-cell {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--gray-700);
}

/* Status Badges */
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.375rem 0.875rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: capitalize;
}

.status-approved {
    background: var(--success-soft);
    color: var(--success);
}

.status-pending {
    background: var(--warning-soft);
    color: var(--warning);
}

.status-rejected {
    background: var(--danger-soft);
    color: var(--danger);
}

/* Payment Badges */
.payment-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.375rem 0.875rem;
    border-radius: var(--radius);
    font-size: 0.75rem;
    font-weight: 600;
}

.payment-badge.paid {
    background: var(--success-soft);
    color: var(--success);
}

.payment-badge.unpaid {
    background: var(--gray-100);
    color: var(--gray-600);
}

/* Amount */
.amount {
    font-weight: 700;
    color: var(--gray-900);
    font-size: 0.9375rem;
}

/* Date Cell */
.date-cell {
    display: flex;
    flex-direction: column;
}

.date-main {
    font-weight: 500;
    color: var(--gray-900);
}

.date-sub {
    font-size: 0.75rem;
    color: var(--gray-500);
    margin-top: 0.125rem;
}

/* Action Buttons */
.action-buttons {
    display: flex;
    align-items: center;
    gap: 0.375rem;
}

.btn-action {
    width: 32px;
    height: 32px;
    border: none;
    border-radius: var(--radius);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
    font-size: 0.875rem;
}

.btn-action.view {
    background: var(--primary-soft);
    color: var(--primary);
}

.btn-action.view:hover {
    background: var(--primary);
    color: white;
    transform: scale(1.1);
}

.btn-action.approve {
    background: var(--success-soft);
    color: var(--success);
}

.btn-action.approve:hover {
    background: var(--success);
    color: white;
    transform: scale(1.1);
}

.btn-action.reject {
    background: var(--danger-soft);
    color: var(--danger);
}

.btn-action.reject:hover {
    background: var(--danger);
    color: white;
    transform: scale(1.1);
}

/* Empty State */
.empty-state {
    padding: 4rem 2rem;
    text-align: center;
    color: var(--gray-500);
}

.empty-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 1.5rem;
    background: var(--gray-100);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: var(--gray-400);
}

.empty-state h6 {
    color: var(--gray-700);
    margin-bottom: 0.5rem;
    font-size: 1rem;
}

.empty-state p {
    margin: 0 0 1.5rem 0;
    font-size: 0.875rem;
}

.empty-state-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    background: linear-gradient(135deg, var(--primary), #7c3aed);
    border: none;
    border-radius: var(--radius);
    color: white;
    font-size: 0.875rem;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.3s;
    box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2);
}

.empty-state-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 10px -1px rgba(79, 70, 229, 0.3);
    color: white;
}

/* Pagination */
.pagination-modern {
    padding: 1.5rem 2rem;
    border-top: 1px solid var(--gray-100);
    display: flex;
    justify-content: center;
}

.pagination-modern .pagination {
    display: flex;
    gap: 0.25rem;
    list-style: none;
    margin: 0;
    padding: 0;
}

.pagination-modern .page-item .page-link {
    padding: 0.5rem 1rem;
    border: 1px solid var(--gray-200);
    border-radius: var(--radius);
    color: var(--gray-700);
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 500;
    transition: all 0.2s;
    background: white;
}

.pagination-modern .page-item.active .page-link {
    background: var(--primary);
    border-color: var(--primary);
    color: white;
}

.pagination-modern .page-item .page-link:hover:not(.active) {
    background: var(--gray-50);
    border-color: var(--gray-300);
}

/* Responsive */
@media (max-width: 768px) {
    .card-header-modern {
        flex-direction: column;
        align-items: flex-start;
    }

    .header-actions {
        width: 100%;
    }

    .search-filter-group {
        width: 100%;
        flex-direction: column;
    }

    .filter-form {
        width: 100%;
    }

    .filter-wrapper {
        width: 100%;
    }

    .modern-select {
        flex: 1;
    }

    .btn-add-booking {
        width: 100%;
        justify-content: center;
    }

    .stats-grid {
        grid-template-columns: 1fr;
    }

    .modern-table {
        font-size: 0.8125rem;
    }

    .modern-table tbody td {
        padding: 1rem;
    }

    .user-email, .date-sub {
        display: none;
    }
}
</style>

<script>
// View booking details
function viewBooking(id) {
    // Implement modal or navigation to detail view
    console.log('View booking:', id);
    // window.location.href = `/admin/bookings/${id}`;
}

// Approve booking
function approveBooking(id) {
    if(confirm('Are you sure you want to approve this booking?')) {
        // Implement approval logic
        console.log('Approve booking:', id);
    }
}

// Reject booking
function rejectBooking(id) {
    if(confirm('Are you sure you want to reject this booking?')) {
        // Implement rejection logic
        console.log('Reject booking:', id);
    }
}

// Export bookings
function exportBookings() {
    // Implement export functionality
    console.log('Export bookings');
    // window.location.href = '/admin/bookings/export';
}

// Add loading states for better UX
document.querySelectorAll('.btn-action, .btn-add-booking, .btn-export, .btn-filter').forEach(btn => {
    btn.addEventListener('click', function() {
        this.style.transform = 'scale(0.95)';
        setTimeout(() => {
            this.style.transform = '';
        }, 150);
    });
});
</script>
@endsection
