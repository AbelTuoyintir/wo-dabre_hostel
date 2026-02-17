@extends('layouts.app')

@section('title', 'Select Room - ' . $hostel->name)
@section('page-title', 'Choose Your Room')

@section('content')
<div class="select-room-container">
    <!-- Booking Summary Bar -->
    <div class="booking-summary-bar">
        <div class="summary-content">
            <div class="summary-item">
                <i class="fas fa-building"></i>
                <div>
                    <span class="label">Hostel</span>
                    <span class="value">{{ $hostel->name }}</span>
                </div>
            </div>
            <div class="summary-item">
                <i class="fas fa-calendar-check"></i>
                <div>
                    <span class="label">Check In</span>
                    <span class="value">{{ \Carbon\Carbon::parse($request->check_in)->format('d M Y') }}</span>
                </div>
            </div>
            <div class="summary-item">
                <i class="fas fa-calendar-times"></i>
                <div>
                    <span class="label">Check Out</span>
                    <span class="value">{{ \Carbon\Carbon::parse($request->check_out)->format('d M Y') }}</span>
                </div>
            </div>
            <div class="summary-item highlight">
                <i class="fas fa-moon"></i>
                <div>
                    <span class="label">Nights</span>
                    <span class="value">{{ $nights }}</span>
                </div>
            </div>
        </div>
        <a href="{{ route('bookings.hostel.select') }}" class="btn-change-dates">
            <i class="fas fa-edit"></i> Change Dates
        </a>
    </div>

    <!-- Available Rooms -->
    <div class="rooms-section">
        <h2 class="section-title">
            Available Rooms
            <span class="room-count">{{ $rooms->count() }} rooms available</span>
        </h2>

        @if($rooms->isEmpty())
            <div class="no-rooms">
                <div class="no-rooms-icon">
                    <i class="fas fa-door-closed"></i>
                </div>
                <h3>No Rooms Available</h3>
                <p>Sorry, no rooms are available for the selected dates.</p>
                <a href="{{ route('bookings.hostel.select') }}" class="btn-back-to-hostels">
                    <i class="fas fa-arrow-left"></i> Choose Different Dates
                </a>
            </div>
        @else
            <div class="rooms-grid">
                @foreach($rooms as $room)
                    <div class="room-card">
                        <div class="room-image">
                            @if($room->images)
                                <img src="{{ Storage::url($room->images[0]) }}" alt="Room {{ $room->room_number }}">
                            @else
                                <div class="room-image-placeholder">
                                    <i class="fas fa-bed"></i>
                                </div>
                            @endif
                            @if($room->is_available)
                                <span class="room-badge available">Available</span>
                            @endif
                        </div>

                        <div class="room-details">
                            <div class="room-header">
                                <h3 class="room-number">Room {{ $room->room_number }}</h3>
                                <div class="room-capacity">
                                    <i class="fas fa-user"></i>
                                    <span>{{ $room->capacity }} {{ Str::plural('person', $room->capacity) }}</span>
                                </div>
                            </div>

                            <div class="room-amenities">
                                @if($room->amenities)
                                    @foreach(json_decode($room->amenities) as $amenity)
                                        <span class="amenity-tag">
                                            <i class="fas fa-check-circle"></i> {{ $amenity }}
                                        </span>
                                    @endforeach
                                @endif
                            </div>

                            @if($room->description)
                                <p class="room-description">{{ Str::limit($room->description, 100) }}</p>
                            @endif

                            <div class="room-footer">
                                <div class="room-price">
                                    <span class="currency">¢</span>
                                    <span class="amount">{{ number_format($room->price_per_night, 2) }}</span>
                                    <span class="period">/night</span>
                                </div>
                                <div class="room-total">
                                    <span class="total-label">Total for {{ $nights }} nights:</span>
                                    <span class="total-value">¢{{ number_format($room->price_per_night * $nights, 2) }}</span>
                                </div>
                                <a href="{{ route('bookings.create', [
                                    'hostel' => $hostel->id,
                                    'room' => $room->id,
                                    'check_in' => $request->check_in,
                                    'check_out' => $request->check_out
                                ]) }}" class="btn-select-room">
                                    Select Room
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<style>
.select-room-container {
    max-width: 1200px;
    margin: 2rem auto;
    padding: 0 1.5rem;
}

/* Booking Summary Bar */
.booking-summary-bar {
    background: linear-gradient(135deg, #667eea, #764ba2);
    border-radius: var(--radius-xl);
    padding: 1.5rem;
    margin-bottom: 2rem;
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
    box-shadow: var(--shadow-lg);
}

.summary-content {
    display: flex;
    gap: 2rem;
    flex-wrap: wrap;
}

.summary-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.summary-item i {
    font-size: 1.5rem;
    opacity: 0.9;
}

.summary-item .label {
    display: block;
    font-size: 0.75rem;
    opacity: 0.8;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.summary-item .value {
    display: block;
    font-size: 1.125rem;
    font-weight: 600;
}

.summary-item.highlight {
    background: rgba(255, 255, 255, 0.2);
    padding: 0.5rem 1rem;
    border-radius: var(--radius-lg);
}

.btn-change-dates {
    background: white;
    color: var(--primary);
    padding: 0.75rem 1.5rem;
    border-radius: var(--radius-lg);
    text-decoration: none;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s;
}

.btn-change-dates:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

/* Rooms Section */
.section-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--gray-900);
    margin-bottom: 2rem;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.room-count {
    font-size: 1rem;
    font-weight: 500;
    color: var(--gray-500);
    background: var(--gray-100);
    padding: 0.25rem 1rem;
    border-radius: 2rem;
}

.rooms-grid {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.room-card {
    background: white;
    border-radius: var(--radius-xl);
    overflow: hidden;
    box-shadow: var(--shadow-md);
    display: grid;
    grid-template-columns: 300px 1fr;
    transition: all 0.3s;
}

.room-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-xl);
}

@media (max-width: 768px) {
    .room-card {
        grid-template-columns: 1fr;
    }
}

.room-image {
    position: relative;
    height: 250px;
    overflow: hidden;
}

.room-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.room-image-placeholder {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #667eea, #764ba2);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 4rem;
}

.room-badge {
    position: absolute;
    top: 1rem;
    right: 1rem;
    padding: 0.5rem 1rem;
    border-radius: 2rem;
    font-size: 0.75rem;
    font-weight: 600;
    color: white;
    background: rgba(16, 185, 129, 0.9);
    backdrop-filter: blur(4px);
}

.room-details {
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
}

.room-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.room-number {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--gray-900);
    margin: 0;
}

.room-capacity {
    display: flex;
    align-items: center;
    gap: 0.375rem;
    color: var(--gray-500);
    font-size: 0.875rem;
}

.room-amenities {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.amenity-tag {
    background: var(--gray-100);
    color: var(--gray-700);
    padding: 0.25rem 0.75rem;
    border-radius: 2rem;
    font-size: 0.75rem;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
}

.amenity-tag i {
    color: var(--primary);
    font-size: 0.625rem;
}

.room-description {
    color: var(--gray-600);
    font-size: 0.875rem;
    line-height: 1.6;
    margin-bottom: 1.5rem;
    flex: 1;
}

.room-footer {
    border-top: 1px solid var(--gray-200);
    padding-top: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 1rem;
}

.room-price {
    display: flex;
    align-items: baseline;
    gap: 0.125rem;
}

.room-price .currency {
    font-size: 0.875rem;
    color: var(--gray-500);
}

.room-price .amount {
    font-size: 1.75rem;
    font-weight: 800;
    color: var(--primary);
}

.room-price .period {
    font-size: 0.75rem;
    color: var(--gray-500);
}

.room-total {
    text-align: right;
}

.total-label {
    display: block;
    font-size: 0.75rem;
    color: var(--gray-500);
}

.total-value {
    display: block;
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--gray-900);
}

.btn-select-room {
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    color: white;
    padding: 0.75rem 2rem;
    border-radius: var(--radius-lg);
    text-decoration: none;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s;
}

.btn-select-room:hover {
    transform: translateX(4px);
    box-shadow: var(--shadow-md);
}

/* No Rooms State */
.no-rooms {
    text-align: center;
    padding: 4rem;
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-md);
}

.no-rooms-icon {
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

.no-rooms h3 {
    color: var(--gray-700);
    margin-bottom: 0.5rem;
    font-size: 1.5rem;
}

.no-rooms p {
    color: var(--gray-500);
    margin-bottom: 2rem;
}

.btn-back-to-hostels {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 1rem 2rem;
    background: var(--primary);
    color: white;
    text-decoration: none;
    border-radius: var(--radius-lg);
    font-weight: 600;
    transition: all 0.3s;
}

.btn-back-to-hostels:hover {
    background: var(--primary-dark);
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}
</style>
@endsection