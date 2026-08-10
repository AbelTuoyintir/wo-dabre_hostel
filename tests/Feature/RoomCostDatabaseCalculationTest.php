<?php

namespace Tests\Feature;

use App\Models\Hostel;
use App\Models\Room;
use App\Models\User;
use App\Support\PaystackSplitService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoomCostDatabaseCalculationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the room_cost is calculated and stored with all surcharges in the database.
     */
    public function test_room_cost_includes_all_surcharges_on_creation_and_update()
    {
        $hostel = Hostel::create([
            'name' => 'Calculation Test Hostel',
            'location' => 'amamoma',
            'address' => '456 Test Way',
            'email' => 'calc@example.com'
        ]);

        // 1. Create room with a base price of 1000.00
        $room = Room::create([
            'hostel_id' => $hostel->id,
            'number' => 'C101',
            'room_type' => 'single_room',
            'capacity' => 2,
            'room_cost' => 1000.00,
            'status' => 'available',
            'gender' => 'any',
        ]);

        // The pre-calculated room_cost should be 1000 * 1.0512 = 1051.20
        // (1 + 0.028 [platform fee/profits] + 0.0197 [paystack buffer] + 0.0035 [banking charge])
        $expectedCost = round(1000 * 1.0512, 2);
        $this->assertEquals($expectedCost, round((float) $room->room_cost, 2));

        // 2. Refresh from DB and verify it is indeed stored in DB as the pre-calculated room_cost
        $room = $room->fresh();
        $this->assertEquals($expectedCost, round((float) $room->room_cost, 2));

        // 3. Ensure editing another attribute does not trigger double-surcharging
        $room->update([
            'description' => 'Updated room description'
        ]);
        $room = $room->fresh();
        $this->assertEquals($expectedCost, round((float) $room->room_cost, 2));

        // 4. Updating the price itself should recalculate properly
        $room->update([
            'room_cost' => 1500.00
        ]);
        $room = $room->fresh();
        $expectedNewCost = round(1500 * 1.0512, 2);
        $this->assertEquals($expectedNewCost, round((float) $room->room_cost, 2));
    }

    /**
     * Test that BookingController calculates and returns room_cost as final total without extra fees.
     */
    public function test_booking_calculation_uses_room_cost_as_final_total_directly()
    {
        $hostel = Hostel::create([
            'name' => 'Calculation Test Hostel 2',
            'location' => 'amamoma',
            'address' => '789 Test Rd',
            'email' => 'calc2@example.com'
        ]);

        $room = Room::create([
            'hostel_id' => $hostel->id,
            'number' => 'C102',
            'room_type' => 'single_room',
            'capacity' => 2,
            'room_cost' => 2000.00, // Stored as 2000 * 1.0512 = 2102.40
            'status' => 'available',
            'gender' => 'any',
        ]);

        $room = $room->fresh();
        $storedRoomCost = round((float) $room->room_cost, 2);

        // Create and authenticate a student user
        $student = User::create([
            'name' => 'Student User',
            'email' => 'student@example.com',
            'password' => bcrypt('password'),
            'role' => 'student',
            'gender' => 'male',
        ]);

        // Post request to calculate totals
        $response = $this->actingAs($student)->postJson(route('bookings.calculate'), [
            'check_in_date' => now()->addDays(2)->toDateString(),
            'check_out_date' => now()->addDays(5)->toDateString(),
            'room_id' => $room->id,
            'room_cost' => $storedRoomCost,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'room_cost' => $storedRoomCost,
            'total' => $storedRoomCost, // Customer sees and pays exactly the room_cost stored in DB
        ]);
    }
}
