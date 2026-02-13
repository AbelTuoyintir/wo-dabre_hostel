@extends('layouts.app')

@section('title', 'Create Booking')
@section('page-title', 'Create New Booking')

@section('content')
<div class="create-booking-container">
    <div class="modern-card">
        <div class="card-header-modern">
            <h5 class="card-title">
                <i class="fas fa-plus-circle me-2"></i>New Booking
            </h5>
            <a href="{{ route('admin.bookings.index') }}" class="btn-back">
                <i class="fas fa-arrow-left"></i> Back to Bookings
            </a>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.bookings.store') }}" id="bookingForm">
                @csrf

                <div class="form-grid">
                    <!-- User Selection -->
                    <div class="form-section">
                        <h6 class="section-title">Customer Information</h6>
                        <div class="form-group">
                            <label for="user_id" class="required">Select Customer</label>
                            <select name="user_id" id="user_id" class="modern-select" required>
                                <option value="">Choose a customer</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Hostel & Room Selection -->
                    <div class="form-section">
                        <h6 class="section-title">Accommodation Details</h6>

                        <div class="form-group">
                            <label for="hostel_id" class="required">Select Hostel</label>
                            <select name="hostel_id" id="hostel_id" class="modern-select" required>
                                <option value="">Choose a hostel</option>
                                @foreach($hostels as $hostel)
                                    <option value="{{ $hostel->id }}" {{ old('hostel_id') == $hostel->id ? 'selected' : '' }}>
                                        {{ $hostel->name }} - {{ $hostel->location }}
                                    </option>
                                @endforeach
                            </select>
                            @error('hostel_id')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="room_id" class="required">Select Room</label>
                            <select name="room_id" id="room_id" class="modern-select" required disabled>
                                <option value="">First select a hostel</option>
                            </select>
                            @error('room_id')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Dates Selection -->
                    <div class="form-section">
                        <h6 class="section-title">Booking Dates</h6>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="check_in_date" class="required">Check-in Date</label>
                                    <input type="date"
                                           name="check_in_date"
                                           id="check_in_date"
                                           class="modern-input"
                                           value="{{ old('check_in_date') }}"
                                           min="{{ date('Y-m-d') }}"
                                           required>
                                    @error('check_in_date')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="check_out_date" class="required">Check-out Date</label>
                                    <input type="date"
                                           name="check_out_date"
                                           id="check_out_date"
                                           class="modern-input"
                                           value="{{ old('check_out_date') }}"
                                           min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                           required>
                                    @error('check_out_date')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Price Summary (Dynamic) -->
                    <div class="form-section price-summary" id="priceSummary" style="display: none;">
                        <h6 class="section-title">Price Summary</h6>
                        <div class="summary-card">
                            <div class="summary-row">
                                <span>Price per night:</span>
                                <span class="price-value" id="pricePerNight">¢0.00</span>
                            </div>
                            <div class="summary-row">
                                <span>Number of nights:</span>
                                <span class="price-value" id="totalNights">0</span>
                            </div>
                            <div class="summary-row total">
                                <span>Total Amount:</span>
                                <span class="total-value" id="totalAmount">¢0.00</span>
                            </div>
                            <div class="summary-row">
                                <span>Amount to Pay Now:</span>
                                <input type="number"
                                       name="amount_to_pay"
                                       id="amount_to_pay"
                                       class="modern-input amount-input"
                                       step="0.01"
                                       min="0"
                                       required>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="form-section">
                        <h6 class="section-title">Payment Details</h6>

                        <div class="form-group">
                            <label for="payment_method" class="required">Payment Method</label>
                            <div class="payment-methods">
                                <label class="payment-option">
                                    <input type="radio" name="payment_method" value="paystack" checked>
                                    <span class="payment-card">
                                        <i class="fas fa-credit-card"></i>
                                        <strong>Paystack</strong>
                                        <small>Card, Mobile Money, Bank Transfer</small>
                                    </span>
                                </label>

                                <label class="payment-option">
                                    <input type="radio" name="payment_method" value="bank_transfer">
                                    <span class="payment-card">
                                        <i class="fas fa-university"></i>
                                        <strong>Bank Transfer</strong>
                                        <small>Direct bank payment</small>
                                    </span>
                                </label>
                            </div>
                            @error('payment_method')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Special Requests -->
                    <div class="form-section">
                        <h6 class="section-title">Additional Information</h6>

                        <div class="form-group">
                            <label for="special_requests">Special Requests (Optional)</label>
                            <textarea name="special_requests"
                                      id="special_requests"
                                      class="modern-textarea"
                                      rows="4">{{ old('special_requests') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="reset" class="btn-secondary">Reset</button>
                    <button type="submit" class="btn-primary" id="submitBtn">
                        <i class="fas fa-save"></i> Create Booking & Proceed to Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
:root {
    --primary: #4f46e5;
    --primary-dark: #4338ca;
    --success: #10b981;
    --warning: #f59e0b;
    --danger: #ef4444;
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
    --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    --radius: 0.5rem;
    --radius-lg: 0.75rem;
}

.create-booking-container {
    max-width: 1000px;
    margin: 0 auto;
    padding: 1rem;
}

.modern-card {
    background: white;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-lg);
    border: 1px solid var(--gray-200);
    overflow: hidden;
}

.card-header-modern {
    padding: 1.5rem 2rem;
    border-bottom: 1px solid var(--gray-200);
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: linear-gradient(to right, var(--gray-50), white);
}

.card-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--gray-900);
    margin: 0;
}

.btn-back {
    padding: 0.5rem 1rem;
    background: white;
    border: 1px solid var(--gray-200);
    border-radius: var(--radius);
    color: var(--gray-700);
    text-decoration: none;
    font-size: 0.875rem;
    transition: all 0.2s;
}

.btn-back:hover {
    background: var(--gray-50);
    border-color: var(--gray-300);
}

.card-body {
    padding: 2rem;
}

.form-grid {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.form-section {
    background: var(--gray-50);
    padding: 1.5rem;
    border-radius: var(--radius);
    border: 1px solid var(--gray-200);
}

.section-title {
    font-size: 1rem;
    font-weight: 600;
    color: var(--gray-700);
    margin: 0 0 1.25rem 0;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid var(--primary);
    display: inline-block;
}

.form-group {
    margin-bottom: 1.25rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--gray-700);
}

.form-group label.required::after {
    content: '*';
    color: var(--danger);
    margin-left: 0.25rem;
}

.modern-select,
.modern-input,
.modern-textarea {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid var(--gray-300);
    border-radius: var(--radius);
    font-size: 0.875rem;
    transition: all 0.2s;
    background: white;
}

.modern-select:focus,
.modern-input:focus,
.modern-textarea:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
}

.modern-select:disabled {
    background: var(--gray-100);
    cursor: not-allowed;
}

.error-message {
    display: block;
    margin-top: 0.25rem;
    font-size: 0.75rem;
    color: var(--danger);
}

/* Payment Methods */
.payment-methods {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
}

.payment-option {
    cursor: pointer;
}

.payment-option input[type="radio"] {
    display: none;
}

.payment-card {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
    padding: 1rem;
    background: white;
    border: 2px solid var(--gray-200);
    border-radius: var(--radius);
    transition: all 0.2s;
}

.payment-option input[type="radio"]:checked + .payment-card {
    border-color: var(--primary);
    background: var(--primary-soft);
}

.payment-card i {
    font-size: 1.5rem;
    color: var(--primary);
    margin-bottom: 0.5rem;
}

.payment-card small {
    color: var(--gray-500);
    font-size: 0.75rem;
}

/* Price Summary */
.price-summary {
    background: linear-gradient(135deg, #f5f7fa, #eef2f6);
}

.summary-card {
    background: white;
    padding: 1.5rem;
    border-radius: var(--radius);
    box-shadow: var(--shadow);
}

.summary-row {
    display: flex;
    justify-content: space-between;
    padding: 0.75rem 0;
    border-bottom: 1px dashed var(--gray-200);
}

.summary-row.total {
    border-bottom: none;
    padding-top: 1rem;
    margin-top: 0.5rem;
    font-weight: 700;
    color: var(--primary);
}

.total-value {
    font-size: 1.25rem;
}

.amount-input {
    width: 200px;
    text-align: right;
    font-weight: 600;
}

/* Form Actions */
.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid var(--gray-200);
}

.btn-primary,
.btn-secondary {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: var(--radius);
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-primary {
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.btn-secondary {
    background: white;
    border: 1px solid var(--gray-300);
    color: var(--gray-700);
}

.btn-secondary:hover {
    background: var(--gray-50);
}

/* Loading State */
.loading {
    position: relative;
    opacity: 0.7;
    pointer-events: none;
}

.loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 20px;
    height: 20px;
    margin: -10px 0 0 -10px;
    border: 2px solid var(--gray-300);
    border-top-color: var(--primary);
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Responsive */
@media (max-width: 768px) {
    .card-header-modern {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }

    .form-actions {
        flex-direction: column;
    }

    .btn-primary,
    .btn-secondary {
        width: 100%;
    }

    .amount-input {
        width: 100%;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const hostelSelect = document.getElementById('hostel_id');
    const roomSelect = document.getElementById('room_id');
    const checkInInput = document.getElementById('check_in_date');
    const checkOutInput = document.getElementById('check_out_date');
    const priceSummary = document.getElementById('priceSummary');
    const pricePerNightSpan = document.getElementById('pricePerNight');
    const totalNightsSpan = document.getElementById('totalNights');
    const totalAmountSpan = document.getElementById('totalAmount');
    const amountToPayInput = document.getElementById('amount_to_pay');
    const bookingForm = document.getElementById('bookingForm');

    let selectedRoom = null;
    let rooms = {};

    // Load rooms when hostel is selected
    hostelSelect.addEventListener('change', function() {
        const hostelId = this.value;

        if (hostelId) {
            roomSelect.disabled = true;
            roomSelect.innerHTML = '<option value="">Loading rooms...</option>';

            fetch(`/admin/get-rooms/${hostelId}`)
                .then(response => response.json())
                .then(data => {
                    rooms = data.reduce((acc, room) => {
                        acc[room.id] = room;
                        return acc;
                    }, {});

                    roomSelect.innerHTML = '<option value="">Select a room</option>';
                    data.forEach(room => {
                        roomSelect.innerHTML += `<option value="${room.id}" data-price="${room.price_per_night}">
                            Room ${room.room_number} - ¢${room.price_per_night}/night (Capacity: ${room.capacity})
                        </option>`;
                    });
                    roomSelect.disabled = false;
                })
                .catch(error => {
                    console.error('Error loading rooms:', error);
                    roomSelect.innerHTML = '<option value="">Error loading rooms</option>';
                });
        } else {
            roomSelect.disabled = true;
            roomSelect.innerHTML = '<option value="">First select a hostel</option>';
            priceSummary.style.display = 'none';
        }
    });

    // Handle room selection
    roomSelect.addEventListener('change', function() {
        const roomId = this.value;
        selectedRoom = rooms[roomId];

        if (selectedRoom && checkInInput.value && checkOutInput.value) {
            calculatePrice();
        } else if (selectedRoom) {
            pricePerNightSpan.textContent = `¢${selectedRoom.price_per_night.toFixed(2)}`;
        }
    });

    // Calculate price when dates change
    function calculatePrice() {
        if (!selectedRoom || !checkInInput.value || !checkOutInput.value) {
            return;
        }

        const checkIn = new Date(checkInInput.value);
        const checkOut = new Date(checkOutInput.value);

        if (checkOut <= checkIn) {
            alert('Check-out date must be after check-in date');
            return;
        }

        const nights = Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24));
        const total = selectedRoom.price_per_night * nights;

        pricePerNightSpan.textContent = `¢${selectedRoom.price_per_night.toFixed(2)}`;
        totalNightsSpan.textContent = nights;
        totalAmountSpan.textContent = `¢${total.toFixed(2)}`;
        amountToPayInput.value = total.toFixed(2);
        amountToPayInput.max = total;

        priceSummary.style.display = 'block';
    }

    checkInInput.addEventListener('change', calculatePrice);
    checkOutInput.addEventListener('change', calculatePrice);

    // Validate amount to pay
    amountToPayInput.addEventListener('input', function() {
        const total = parseFloat(totalAmountSpan.textContent.replace('¢', ''));
        const amount = parseFloat(this.value) || 0;

        if (amount > total) {
            this.value = total;
        }
    });

    // Form submission with loading state
    bookingForm.addEventListener('submit', function() {
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.classList.add('loading');
        submitBtn.disabled = true;
    });

    // Restore old values if validation fails
    @if(old('hostel_id'))
        setTimeout(() => {
            hostelSelect.value = "{{ old('hostel_id') }}";
            hostelSelect.dispatchEvent(new Event('change'));

            setTimeout(() => {
                roomSelect.value = "{{ old('room_id') }}";
                if (roomSelect.value) {
                    roomSelect.dispatchEvent(new Event('change'));
                }
            }, 500);
        }, 100);
    @endif
});
</script>
@endsection
