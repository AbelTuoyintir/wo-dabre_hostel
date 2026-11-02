<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Hostel;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class BookingCancellationRateLimitingTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_booking_cancellation_is_rate_limited(): void
    {
        $student = User::create([
            'name' => 'Student Cancel Test',
            'email' => 'cancel.student@example.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
            'gender' => 'female',
            'email_verified_at' => now(),
        ]);

        $hostel = Hostel::create([
            'name' => 'Cancellation Hostel',
            'location' => 'amamoma',
            'address' => 'Cancel Street 123',
            'email' => 'hostel.cancel@example.com',
            'is_approved' => true,
            'status' => 'active',
        ]);

        $room = Room::create([
            'hostel_id' => $hostel->id,
            'number' => 'C101',
            'room_type' => 'single_room',
            'capacity' => 1,
            'current_occupancy' => 0,
            'room_cost' => 1200,
            'price_per_semester' => 1200,
            'gender' => 'female',
            'status' => 'available',
        ]);

        // Attempt 5 requests (throttle limit is 5 attempts per minute)
        for ($i = 0; $i < 5; $i++) {
            $booking = Booking::create([
                'booking_number' => 'BN-' . Str::random(8),
                'user_id' => $student->id,
                'room_id' => $room->id,
                'hostel_id' => $hostel->id,
                'booking_status' => 'pending',
                'payment_status' => 'pending',
                'check_in_date' => now()->addDays(5)->format('Y-m-d'),
                'check_out_date' => now()->addDays(20)->format('Y-m-d'),
                'total_amount' => 1200,
            ]);

            $response = $this->actingAs($student)->patch(route('student.bookings.cancel', $booking->uuid), [
                'cancellation_reason' => 'Change of plans for this specific booking test',
            ]);

            $this->assertNotEquals(429, $response->getStatusCode());
        }

        // The 6th request within the same minute should be throttled (HTTP 429)
        $bookingSixth = Booking::create([
            'booking_number' => 'BN-' . Str::random(8),
            'user_id' => $student->id,
            'room_id' => $room->id,
            'hostel_id' => $hostel->id,
            'booking_status' => 'pending',
            'payment_status' => 'pending',
            'check_in_date' => now()->addDays(5)->format('Y-m-d'),
            'check_out_date' => now()->addDays(20)->format('Y-m-d'),
            'total_amount' => 1200,
        ]);

        $responseSixth = $this->actingAs($student)->patch(route('student.bookings.cancel', $bookingSixth->uuid), [
            'cancellation_reason' => 'Another cancellation reason that should be blocked by rate limit',
        ]);

        $responseSixth->assertStatus(429);
    }
}
