@extends('layouts.app')

@section('title', 'Booking Details')
@section('page-title', 'Booking #' . $booking->booking_number)

@section('content')
<div class="booking-detail-container">
    <!-- Header with Status -->
    <div class="detail-header">
        <div class="header-left">
            <a href="{{ route('bookings.index') }}" class="btn-back">
                <i class="fas fa-arrow-left"></i>
                <span>Back to Bookings</span>
            </a>
            <div class="booking-title">
                <h1>Booking Details</h1>
                <p>Review and manage your booking information</p>
            </div>
        </div>
        <div class="header-right">
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
            <span class="status-badge-large {{ $statusClasses[$booking->booking_status] }}">
                <i class="fas fa-{{ $statusIcons[$booking->booking_status] }}"></i>
                {{ ucfirst(str_replace('_', ' ', $booking->booking_status)) }}
            </span>
        </div>
    </div>

    <div class="detail-grid">
        <!-- Left Column -->
        <div class="left-column">
            <!-- Booking Progress -->
            <div class="progress-card">
                <h3 class="card-title">Booking Progress</h3>
                <div class="progress-steps">
                    @php
                        $steps = [
                            'pending' => ['label' => 'Pending', 'icon' => 'clock', 'color' => '#f59e0b'],
                            'confirmed' => ['label' => 'Confirmed', 'icon' => 'check-circle', 'color' => '#10b981'],
                            'checked_in' => ['label' => 'Checked In', 'icon' => 'door-open', 'color' => '#3b82f6'],
                            'checked_out' => ['label' => 'Completed', 'icon' => 'check-double', 'color' => '#8b5cf6']
                        ];
                        $currentStep = array_search($booking->booking_status, array_keys($steps));
                        if ($booking->booking_status === 'cancelled') $currentStep = -1;
                    @endphp

                    @foreach($steps as $key => $step)
                        @php
                            $stepIndex = array_search($key, array_keys($steps));
                            $isComplete = $stepIndex < $currentStep;
                            $isCurrent = $stepIndex === $currentStep;
                            $isCancelled = $booking->booking_status === 'cancelled';
                        @endphp
                        <div class="step {{ $isComplete ? 'complete' : '' }} {{ $isCurrent ? 'current' : '' }} {{ $isCancelled ? 'cancelled' : '' }}">
                            <div class="step-icon" style="background: {{ $step['color'] }}20; color: {{ $step['color'] }}">
                                <i class="fas fa-{{ $step['icon'] }}"></i>
                            </div>
                            <div class="step-content">
                                <span class="step-label">{{ $step['label'] }}</span>
                                @if($isCurrent)
                                    <span class="step-status">Current</span>
                                @elseif($isComplete)
                                    <span class="step-status">Completed</span>
                                @endif
                            </div>
                            @if(!$loop->last)
                                <div class="step-connector {{ $isComplete ? 'complete' : '' }}"></div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Hostel Information -->
            <div class="hostel-card">
                <div class="hostel-image">
                    @if($booking->hostel->image)
                        <img src="{{ Storage::url($booking->hostel->image) }}" alt="{{ $booking->hostel->name }}">
                    @else
                        <div class="image-placeholder">
                            <i class="fas fa-building"></i>
                        </div>
                    @endif
                </div>
                <div class="hostel-info">
                    <h2>{{ $booking->hostel->name }}</h2>
                    <p class="location">
                        <i class="fas fa-map-marker-alt"></i>
                        {{ $booking->hostel->location }}
                    </p>
                    <div class="hostel-features">
                        @if($booking->hostel->has_wifi)
                            <span class="feature"><i class="fas fa-wifi"></i> WiFi</span>
                        @endif
                        @if($booking->hostel->has_electricity)
                            <span class="feature"><i class="fas fa-bolt"></i> 24/7 Power</span>
                        @endif
                        @if($booking->hostel->has_water)
                            <span class="feature"><i class="fas fa-water"></i> Running Water</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Room Details -->
            <div class="room-card">
                <h3 class="card-title">Room Details</h3>
                <div class="room-details">
                    <div class="room-info-item">
                        <span class="label">Room Number</span>
                        <span class="value">{{ $booking->room_number }}</span>
                    </div>
                    <div class="room-info-item">
                        <span class="label">Price per Night</span>
                        <span class="value">¢{{ number_format($booking->room->price_per_night, 2) }}</span>
                    </div>
                    <div class="room-info-item">
                        <span class="label">Capacity</span>
                        <span class="value">{{ $booking->room->capacity }} Person(s)</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="right-column">
            <!-- Booking Summary -->
            <div class="summary-card">
                <h3 class="card-title">Booking Summary</h3>

                <div class="summary-dates">
                    <div class="date-item">
                        <span class="date-label">Check In</span>
                        <span class="date-value">{{ $booking->check_in_date->format('l, d M Y') }}</span>
                        <span class="date-time">{{ $booking->check_in_date->format('h:i A') }}</span>
                    </div>
                    <div class="date-arrow">
                        <i class="fas fa-long-arrow-alt-down"></i>
                    </div>
                    <div class="date-item">
                        <span class="date-label">Check Out</span>
                        <span class="date-value">{{ $booking->check_out_date->format('l, d M Y') }}</span>
                        <span class="date-time">{{ $booking->check_out_date->format('h:i A') }}</span>
                    </div>
                </div>

                <div class="duration">
                    <i class="far fa-clock"></i>
                    {{ now()->parse($booking->check_in_date)->diffInDays($booking->check_out_date) }} Nights
                </div>

                <div class="price-breakdown">
                    <div class="price-row">
                        <span>Price per night</span>
                        <span>¢{{ number_format($booking->room->price_per_night, 2) }}</span>
                    </div>
                    <div class="price-row">
                        <span>Number of nights</span>
                        <span>{{ now()->parse($booking->check_in_date)->diffInDays($booking->check_out_date) }}</span>
                    </div>
                    <div class="price-row subtotal">
                        <span>Subtotal</span>
                        <span>¢{{ number_format($booking->total_amount, 2) }}</span>
                    </div>
                    @if($booking->amount_paid > 0)
                        <div class="price-row paid">
                            <span>Amount Paid</span>
                            <span class="text-success">¢{{ number_format($booking->amount_paid, 2) }}</span>
                        </div>
                        @if($booking->amount_paid < $booking->total_amount)
                            <div class="price-row balance">
                                <span>Balance Due</span>
                                <span class="text-warning">¢{{ number_format($booking->total_amount - $booking->amount_paid, 2) }}</span>
                            </div>
                        @endif
                    @endif
                    <div class="price-row total">
                        <span>Total Amount</span>
                        <span>¢{{ number_format($booking->total_amount, 2) }}</span>
                    </div>
                </div>

                @if($booking->special_requests)
                    <div class="special-requests">
                        <h4>Special Requests</h4>
                        <p>{{ $booking->special_requests }}</p>
                    </div>
                @endif
            </div>

            <!-- Payment Status -->
            <div class="payment-card">
                <h3 class="card-title">Payment Status</h3>

                @if($booking->payment_status === 'paid')
                    <div class="payment-status paid">
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <h4>Payment Complete</h4>
                            <p>Your payment has been successfully processed</p>
                        </div>
                    </div>
                    <div class="payment-details">
                        <div class="detail-row">
                            <span>Transaction ID</span>
                            <span>{{ $booking->transaction_id ?? 'N/A' }}</span>
                        </div>
                        <div class="detail-row">
                            <span>Payment Date</span>
                            <span>{{ $booking->payment_date ? $booking->payment_date->format('d M Y, h:i A') : 'N/A' }}</span>
                        </div>
                        <div class="detail-row">
                            <span>Payment Method</span>
                            <span>{{ ucfirst($booking->payment_method) }}</span>
                        </div>
                    </div>
                @elseif($booking->payment_status === 'partial')
                    <div class="payment-status partial">
                        <i class="fas fa-adjust"></i>
                        <div>
                            <h4>Partial Payment</h4>
                            <p>You have paid ¢{{ number_format($booking->amount_paid, 2) }} of ¢{{ number_format($booking->total_amount, 2) }}</p>
                        </div>
                    </div>
                    <a href="{{ route('payment.process', $booking) }}" class="btn-payment">
                        <i class="fas fa-credit-card"></i>
                        Complete Payment
                    </a>
                @else
                    <div class="payment-status pending">
                        <i class="fas fa-hourglass"></i>
                        <div>
                            <h4>Payment Pending</h4>
                            <p>Please complete your payment to confirm booking</p>
                        </div>
                    </div>
                    <a href="{{ route('payment.process', $booking) }}" class="btn-payment">
                        <i class="fas fa-credit-card"></i>
                        Pay Now
                    </a>
                @endif
            </div>

            <!-- Action Buttons -->
            <div class="action-card">
                @if(in_array($booking->booking_status, ['pending', 'confirmed']))
                    <button class="btn-cancel" onclick="cancelBooking({{ $booking->id }})">
                        <i class="fas fa-times-circle"></i>
                        Cancel Booking
                    </button>
                @endif

                @if($booking->booking_status === 'confirmed' && $booking->check_in_date->isToday())
                    <button class="btn-checkin" onclick="checkIn({{ $booking->id }})">
                        <i class="fas fa-sign-in-alt"></i>
                        Check In Now
                    </button>
                @endif

                <button class="btn-print" onclick="window.print()">
                    <i class="fas fa-print"></i>
                    Print Details
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.booking-detail-container {
    max-width: 1400px;
    margin: 2rem auto;
    padding: 0 1.5rem;
}

/* Header Styles */
.detail-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.header-left {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    flex-wrap: wrap;
}

.btn-back {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.25rem;
    background: white;
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-lg);
    color: var(--gray-700);
    text-decoration: none;
    font-weight: 500;
    transition: all 0.2s;
}

.btn-back:hover {
    background: var(--gray-50);
    border-color: var(--gray-300);
    transform: translateX(-4px);
}

.booking-title h1 {
    font-size: 1.75rem;
    font-weight: 800;
    color: var(--gray-900);
    margin: 0 0 0.25rem 0;
}

.booking-title p {
    color: var(--gray-500);
    margin: 0;
}

.status-badge-large {
    padding: 0.75rem 1.5rem;
    border-radius: 3rem;
    font-size: 1rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

/* Grid Layout */
.detail-grid {
    display: grid;
    grid-template-columns: 1fr 380px;
    gap: 1.5rem;
}

@media (max-width: 1024px) {
    .detail-grid {
        grid-template-columns: 1fr;
    }
}

/* Left Column Cards */
.left-column {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.progress-card,
.hostel-card,
.room-card {
    background: white;
    border-radius: var(--radius-xl);
    padding: 1.5rem;
    box-shadow: var(--shadow-md);
}

.card-title {
    font-size: 1.125rem;
    font-weight: 700;
    color: var(--gray-900);
    margin: 0 0 1.25rem 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

/* Progress Steps */
.progress-steps {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    position: relative;
}

.step {
    display: flex;
    align-items: center;
    gap: 1rem;
    position: relative;
}

.step-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    background: var(--gray-100);
    color: var(--gray-400);
    transition: all 0.3s;
}

.step.complete .step-icon {
    background: var(--success-soft) !important;
    color: var(--success) !important;
}

.step.current .step-icon {
    animation: pulse 2s infinite;
}

.step.cancelled .step-icon {
    background: var(--danger-soft) !important;
    color: var(--danger) !important;
}

.step-content {
    flex: 1;
}

.step-label {
    display: block;
    font-weight: 600;
    color: var(--gray-900);
    margin-bottom: 0.25rem;
}

.step-status {
    font-size: 0.75rem;
    color: var(--gray-500);
}

.step-connector {
    position: absolute;
    left: 24px;
    top: 48px;
    width: 2px;
    height: 40px;
    background: var(--gray-200);
}

.step-connector.complete {
    background: var(--success);
}

/* Hostel Card */
.hostel-card {
    display: flex;
    gap: 1.5rem;
    align-items: center;
}

.hostel-image {
    width: 120px;
    height: 120px;
    border-radius: var(--radius-lg);
    overflow: hidden;
    flex-shrink: 0;
}

.hostel-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.image-placeholder {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 3rem;
}

.hostel-info h2 {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--gray-900);
    margin: 0 0 0.5rem 0;
}

.location {
    color: var(--gray-500);
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.375rem;
}

/* Room Details */
.room-details {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1rem;
}

.room-info-item {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.room-info-item .label {
    font-size: 0.75rem;
    color: var(--gray-500);
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.room-info-item .value {
    font-size: 1.125rem;
    font-weight: 700;
    color: var(--gray-900);
}

/* Right Column */
.right-column {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.summary-card,
.payment-card,
.action-card {
    background: white;
    border-radius: var(--radius-xl);
    padding: 1.5rem;
    box-shadow: var(--shadow-md);
}

/* Summary Dates */
.summary-dates {
    background: var(--gray-50);
    border-radius: var(--radius-lg);
    padding: 1.25rem;
    margin-bottom: 1rem;
    position: relative;
}

.date-item {
    text-align: center;
}

.date-label {
    display: block;
    font-size: 0.75rem;
    color: var(--gray-500);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 0.25rem;
}

.date-value {
    display: block;
    font-size: 1.125rem;
    font-weight: 700;
    color: var(--gray-900);
    margin-bottom: 0.25rem;
}

.date-time {
    font-size: 0.75rem;
    color: var(--gray-400);
}

.date-arrow {
    text-align: center;
    color: var(--primary);
    margin: 0.5rem 0;
}

.duration {
    text-align: center;
    padding: 0.75rem;
    background: var(--primary-soft);
    border-radius: var(--radius-lg);
    color: var(--primary);
    font-weight: 600;
    margin-bottom: 1rem;
}

/* Price Breakdown */
.price-breakdown {
    border-top: 1px solid var(--gray-200);
    padding-top: 1rem;
}

.price-row {
    display: flex;
    justify-content: space-between;
    padding: 0.5rem 0;
    color: var(--gray-600);
}

.price-row.subtotal {
    border-bottom: 1px dashed var(--gray-200);
    padding-bottom: 0.75rem;
}

.price-row.paid {
    color: var(--success);
}

.price-row.balance {
    color: var(--warning);
}

.price-row.total {
    font-weight: 700;
    color: var(--gray-900);
    font-size: 1.125rem;
    padding-top: 0.75rem;
    margin-top: 0.25rem;
    border-top: 2px solid var(--gray-200);
}

/* Special Requests */
.special-requests {
    margin-top: 1.5rem;
    padding: 1rem;
    background: var(--gray-50);
    border-radius: var(--radius-lg);
}

.special-requests h4 {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--gray-700);
    margin: 0 0 0.5rem 0;
}

.special-requests p {
    color: var(--gray-600);
    margin: 0;
    font-size: 0.875rem;
    line-height: 1.5;
}

/* Payment Status */
.payment-status {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    border-radius: var(--radius-lg);
    margin-bottom: 1rem;
}

.payment-status i {
    font-size: 2rem;
}

.payment-status.paid {
    background: var(--success-soft);
    color: var(--success);
}

.payment-status.partial {
    background: var(--warning-soft);
    color: var(--warning);
}

.payment-status.pending {
    background: var(--gray-100);
    color: var(--gray-500);
}

.payment-status h4 {
    font-size: 1rem;
    font-weight: 600;
    margin: 0 0 0.25rem 0;
}

.payment-status p {
    font-size: 0.875rem;
    margin: 0;
    opacity: 0.8;
}

.payment-details {
    background: var(--gray-50);
    border-radius: var(--radius-lg);
    padding: 1rem;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    padding: 0.5rem 0;
    border-bottom: 1px solid var(--gray-200);
}

.detail-row:last-child {
    border-bottom: none;
}

.btn-payment {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    width: 100%;
    padding: 1rem;
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    color: white;
    text-decoration: none;
    border-radius: var(--radius-lg);
    font-weight: 600;
    transition: all 0.3s;
}

.btn-payment:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

/* Action Buttons */
.action-card {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.btn-cancel,
.btn-checkin,
.btn-print {
    padding: 1rem;
    border: none;
    border-radius: var(--radius-lg);
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    transition: all 0.2s;
}

.btn-cancel {
    background: var(--danger-soft);
    color: var(--danger);
}

.btn-cancel:hover {
    background: var(--danger);
    color: white;
}

.btn-checkin {
    background: var(--info-soft);
    color: var(--info);
}

.btn-checkin:hover {
    background: var(--info);
    color: white;
}

.btn-print {
    background: var(--gray-100);
    color: var(--gray-700);
}

.btn-print:hover {
    background: var(--gray-200);
}

/* Animations */
@keyframes pulse {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.05);
    }
}

/* Print Styles */
@media print {
    .btn-back,
    .btn-cancel,
    .btn-checkin,
    .btn-print,
    .action-card {
        display: none;
    }

    .detail-header {
        print-color-adjust: exact;
    }

    .status-badge-large {
        border: 1px solid #000;
    }
}
</style>

<script>
function cancelBooking(bookingId) {
    if (confirm('Are you sure you want to cancel this booking? This action cannot be undone.')) {
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

function checkIn(bookingId) {
    if (confirm('Proceed with check-in?')) {
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
