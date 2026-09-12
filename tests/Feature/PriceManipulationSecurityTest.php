<?php

namespace Tests\Feature;

use App\Models\Hostel;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PriceManipulationSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_booking_store_ignores_manipulated_client_room_cost()
    {
        $student = User::factory()->create([
            'role' => 'student',
            'gender' => 'male',
        ]);

        $hostel = Hostel::create([
            'name' => 'Security Hostel',
            'location' => 'amamoma',
            'address' => '123 Campus St',
            'email' => 'sec@example.com',
            'is_approved' => true,
            'status' => 'active',
        ]);

        $room = Room::create([
            'number' => '301',
            'capacity' => 2,
            'hostel_id' => $hostel->id,
            'gender' => 'male',
            'status' => 'available',
            'room_type' => 'single_room',
            'room_cost' => 1500.00,
            'current_occupancy' => 0,
        ]);

        $expectedDbPrice = (float) $room->fresh()->room_cost;

        $checkIn = Carbon::now()->addDays(2)->toDateString();
        $checkOut = Carbon::now()->addDays(10)->toDateString();

        // Attempt price manipulation by passing room_cost = 1.00
        $response = $this->actingAs($student)->post(route('bookings.store.student'), [
            'room_id' => $room->id,
            'hostel_id' => $hostel->id,
            'check_in_date' => $checkIn,
            'check_out_date' => $checkOut,
            'room_cost' => 1.00, // Manipulated price
            'gender' => 'male',
        ]);

        $pendingBooking = session('pending_booking');
        $this->assertNotNull($pendingBooking);

        // Verify the trusted DB price is stored in session, NOT the manipulated price (1.00)
        $this->assertEquals($expectedDbPrice, $pendingBooking['room_cost']);
        $this->assertEquals($expectedDbPrice, $pendingBooking['final_total']);
    }

    public function test_guest_booking_store_ignores_manipulated_client_room_cost()
    {
        $hostel = Hostel::create([
            'name' => 'Guest Hostel',
            'location' => 'amamoma',
            'address' => '456 Guest Ave',
            'email' => 'guest@example.com',
            'is_approved' => true,
            'status' => 'active',
        ]);

        $room = Room::create([
            'number' => '102',
            'capacity' => 2,
            'hostel_id' => $hostel->id,
            'gender' => 'female',
            'status' => 'available',
            'room_type' => 'single_room',
            'room_cost' => 2000.00,
            'current_occupancy' => 0,
        ]);

        $expectedDbPrice = (float) $room->fresh()->room_cost;

        $checkIn = Carbon::now()->addDays(2)->toDateString();
        $checkOut = Carbon::now()->addDays(5)->toDateString();

        // Attempt price manipulation by passing room_cost = 0.01
        $response = $this->post(route('bookings.store'), [
            'room_id' => $room->id,
            'hostel_id' => $hostel->id,
            'check_in_date' => $checkIn,
            'check_out_date' => $checkOut,
            'room_cost' => 0.01, // Manipulated price
            'name' => 'Jane Guest',
            'email' => 'jane.guest@example.com',
            'phone' => '0240000000',
            'gender' => 'female',
        ]);

        $pendingBooking = session('pending_booking');
        $this->assertNotNull($pendingBooking);

        // Verify trusted DB price is stored in session, NOT 0.01
        $this->assertEquals($expectedDbPrice, $pendingBooking['room_cost']);
        $this->assertEquals($expectedDbPrice, $pendingBooking['final_total']);
    }

    public function test_calculate_ignores_manipulated_client_room_cost()
    {
        $hostel = Hostel::create([
            'name' => 'Calc Hostel',
            'location' => 'amamoma',
            'address' => '789 Calc Rd',
            'email' => 'calc@example.com',
        ]);

        $room = Room::create([
            'number' => '201',
            'capacity' => 1,
            'hostel_id' => $hostel->id,
            'gender' => 'any',
            'status' => 'available',
            'room_type' => 'single_room',
            'room_cost' => 1200.00,
            'current_occupancy' => 0,
        ]);

        $expectedDbPrice = (float) $room->fresh()->room_cost;

        $checkIn = Carbon::now()->addDays(1)->toDateString();
        $checkOut = Carbon::now()->addDays(3)->toDateString();

        $student = User::factory()->create(['role' => 'student']);

        $response = $this->actingAs($student)->postJson(route('bookings.calculate'), [
            'check_in_date' => $checkIn,
            'check_out_date' => $checkOut,
            'room_id' => $room->id,
            'room_cost' => 5.00, // Manipulated price
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'room_cost' => $expectedDbPrice,
            'total' => $expectedDbPrice,
        ]);
    }
}
