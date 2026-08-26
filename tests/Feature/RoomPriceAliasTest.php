<?php

namespace Tests\Feature;

use App\Models\Hostel;
use App\Models\Room;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoomPriceAliasTest extends TestCase
{
    use RefreshDatabase;

    public function test_room_price_aliases_calculate_and_store_correctly()
    {
        $hostel = Hostel::create([
            'name' => 'Alias Test Hostel',
            'location' => 'amamoma',
            'address' => '123 Main St',
            'email' => 'alias@example.com'
        ]);

        // 1. Create room using price_per_month
        $room = Room::create([
            'hostel_id' => $hostel->id,
            'number' => 'M101',
            'room_type' => 'single_room',
            'capacity' => 2,
            'price_per_month' => 1000.00,
            'status' => 'available',
            'gender' => 'any',
        ]);

        // The pre-calculated room_cost should be 1000 * 1.0512 = 1051.20
        $this->assertEquals(1051.20, (float) $room->room_cost);
        $this->assertEquals(1051.20, (float) $room->price_per_month);
        $this->assertEquals(1051.20, (float) $room->price_per_semester);

        // 2. Refresh from DB and check
        $room = $room->fresh();
        $this->assertEquals(1051.20, (float) $room->room_cost);
        $this->assertEquals(1051.20, (float) $room->price_per_month);

        // 3. Update using price_per_semester
        $room->update([
            'price_per_semester' => 2000.00
        ]);

        // The pre-calculated room_cost should be 2000 * 1.0512 = 2102.40
        $this->assertEquals(2102.40, (float) $room->room_cost);
        $this->assertEquals(2102.40, (float) $room->price_per_semester);
        $this->assertEquals(2102.40, (float) $room->price_per_month);

        // 4. Refresh from DB and check
        $room = $room->fresh();
        $this->assertEquals(2102.40, (float) $room->room_cost);
        $this->assertEquals(2102.40, (float) $room->price_per_semester);
    }
}
