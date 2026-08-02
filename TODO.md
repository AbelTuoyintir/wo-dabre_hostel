# Fix: "Load More Rooms" infinite loop in student/hostels/show.blade.php

## Context
- **Issue:** Clicking the "Load More Rooms" button causes an infinite loop (spinning loader overlay that never stops).
- **File:** `resources/views/student/hostels/show.blade.php`
- **Root cause:** The button is a plain `<button>` without `type="button"`. The global loader script in `resources/views/layouts/student.blade.php` attaches a click listener to `button:not([type="button"]):not(.no-loader)`, which calls `showLoading()` showing the full-screen infinite spinner. Since the button has no real action/handler, the spinner never goes away — appearing as an infinite loop.

## Plan
- [x] 1. Read `show.blade.php`, `student.blade.php` layout, `StudentController.php`, routes to understand the issue.
- [x] 2. Add `id="available-rooms-grid"` and `id="load-more-rooms-btn"` to the rooms grid + button.
- [x] 3. Hide room cards beyond the first 6 using `$index` in the `@foreach` loop.
- [x] 4. Add `type="button"` to the "Load More Rooms" button so the global loader no longer intercepts the click.
- [x] 5. Add JS in the existing `@section('scripts')` to reveal the next 6 hidden cards on click, and hide the button once all rooms are shown (and hide it initially if there are <= 6 rooms).
- [x] 6. Verify no infinite loop and that "Load More Rooms" reveals additional rooms correctly.

---

# Fix: "Undefined variable $totalServiceCharge" in BookingController::StudentStore

## Context
- **Issue:** POST `/bookings/student` returned 500 `ErrorException: Undefined variable $totalServiceCharge` at `BookingController.php:297`.
- **Root cause:** The `Log::info()` in `StudentStore()` referenced `$totalServiceCharge`, which was never defined.
- **Also fixed:** `calculate()` used non-existent config key `payments.total_service_charge_rate` (fallback 0.051); changed to `payments.total_surcharge_rate` (0.0512) to match `PaystackSplitService::calculateTotal()`.

## Steps
- [x] 1. Define `$totalServiceCharge = $platformFee + $paystackBuffer + $bankingCharge` in `StudentStore()`.
- [x] 2. Log `platform_fee`, `paystack_buffer`, `banking_charge` for clarity.
- [x] 3. Fix `calculate()` config key to `payments.total_surcharge_rate`.
- [x] 4. `php -l` passes; `php artisan view:cache` passes.

---

# Fix: Room gender not updating when a student books

## Context
- **Issue:** When a student books a hostel, the room's gender does not change.
- **Root cause:** In `BookingController::finalizeBooking()`, the room-gender update used a strict comparison `($room->current_occupancy ?? 0) === 0`. The `current_occupancy` column is nullable and not cast to integer, so it can arrive as a string `'0'` (failing `=== 0`), silently skipping the update. Additionally, if the student's gender is `'any'` (the users table default), the room would be set to `'any'`, appearing as no change.

## Steps
- [x] 1. Add `'current_occupancy' => 'integer'` to `Room::$casts`.
- [x] 2. In `finalizeBooking()`, cast occupancy explicitly (`(int)`) and only assign the room gender when the student's gender is a real `male`/`female` value.
- [x] 3. Add detailed logging for the room-gender update.
- [x] 4. `php -l` passes for both `Room.php` and `BookingController.php`.

