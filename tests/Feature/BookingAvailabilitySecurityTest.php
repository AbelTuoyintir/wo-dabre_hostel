<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Hostel;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class BookingAvailabilitySecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_booking_store_rejects_overlapping_dates()
    {
        $student = User::factory()->create([
            'role' => 'student',
            'gender' => 'male',
        ]);

        $hostel = Hostel::create([
            'name' => 'Campus Villa',
            'location' => 'amamoma',
            'description' => 'Test Hostel',
            'is_approved' => true,
            'status' => 'active',
        ]);

        $room = Room::create([
            'hostel_id' => $hostel->id,
            'number' => 'A101',
            'capacity' => 2,
            'current_occupancy' => 0,
            'gender' => 'male',
            'status' => 'available',
            'room_type' => 'shared_2',
            'room_cost' => 500,
        ]);

        // Create an existing active booking from Sept 1 to Sept 10
        Booking::create([
            'booking_number' => 'BN-' . Str::random(8),
            'user_id' => $student->id,
            'hostel_id' => $hostel->id,
            'room_id' => $room->id,
            'check_in_date' => '2026-09-01',
            'check_out_date' => '2026-09-10',
            'total_amount' => 500,
            'amount_paid' => 500,
            'payment_status' => 'paid',
            'booking_status' => 'confirmed',
        ]);

        $anotherStudent = User::factory()->create([
            'role' => 'student',
            'gender' => 'male',
        ]);

        // Try booking overlapping dates Sept 5 to Sept 15
        $response = $this->actingAs($anotherStudent)->post(route('bookings.store.student'), [
            'room_id' => $room->id,
            'hostel_id' => $hostel->id,
            'check_in_date' => '2026-09-05',
            'check_out_date' => '2026-09-15',
            'gender' => 'male',
        ]);

        $response->assertRedirect(route('student.hostels.show', $hostel->id));
        $response->assertSessionHas('error', 'Room is not available for selected dates.');
    }

    public function test_booking_store_allows_non_overlapping_adjacent_dates()
    {
        $student = User::factory()->create([
            'role' => 'student',
            'gender' => 'male',
        ]);

        $hostel = Hostel::create([
            'name' => 'Campus Haven',
            'location' => 'amamoma',
            'description' => 'Test Hostel 2',
            'is_approved' => true,
            'status' => 'active',
        ]);

        $room = Room::create([
            'hostel_id' => $hostel->id,
            'number' => 'B202',
            'capacity' => 2,
            'current_occupancy' => 0,
            'gender' => 'male',
            'status' => 'available',
            'room_type' => 'shared_2',
            'room_cost' => 500,
        ]);

        // Existing booking Sept 1 to Sept 5
        $booking = Booking::create([
            'booking_number' => 'BN-' . Str::random(8),
            'user_id' => $student->id,
            'hostel_id' => $hostel->id,
            'room_id' => $room->id,
            'check_in_date' => '2026-09-01',
            'check_out_date' => '2026-09-05',
            'total_amount' => 500,
            'amount_paid' => 500,
            'payment_status' => 'paid',
            'booking_status' => 'confirmed',
        ]);

        // Reflection method test on private checkRoomAvailability method via reflection
        $controller = new \App\Http\Controllers\BookingController();
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('checkRoomAvailability');
        $method->setAccessible(true);

        // Check-in on Sept 5 (same day as previous checkout) should be available!
        $isAvailableAdjacent = $method->invoke($controller, $room->id, '2026-09-05', '2026-09-10');
        $this->assertTrue($isAvailableAdjacent, 'Adjacent date booking should be available');

        // Check overlapping range Sept 4 to Sept 8 should be blocked
        $isAvailableOverlap = $method->invoke($controller, $room->id, '2026-09-04', '2026-09-08');
        $this->assertFalse($isAvailableOverlap, 'Overlapping date booking should be blocked');
    }
}
