<?php

namespace Tests\Feature;

use App\Models\Hostel;
use App\Models\Room;
use App\Support\PaystackSplitService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoomCostDatabaseCalculationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that saving a room automatically inflates the room_cost with fees.
     */
    public function test_room_cost_is_automatically_recalculated_on_save()
    {
        $hostel = Hostel::create([
            'name' => 'Calculation Test Hostel',
            'location' => 'amamoma',
            'address' => '456 Calculation Rd',
            'email' => 'calc@example.com'
        ]);

        // Base price is 1000.00
        $room = Room::create([
            'hostel_id' => $hostel->id,
            'number' => 'C101',
            'room_type' => 'single_room',
            'capacity' => 2,
            'room_cost' => 1000.00,
            'status' => 'available',
            'gender' => 'any',
        ]);

        // Surcharge rate is 0.0512, so total = 1000 * 1.0512 = 1051.20
        $this->assertEquals(1051.20, (float) $room->room_cost);

        // Fetch fresh from database and assert the value is persisted correctly
        $freshRoom = $room->fresh();
        $this->assertEquals(1051.20, (float) $freshRoom->room_cost);
    }

    /**
     * Test that the double surcharge prevention guard works correctly.
     */
    public function test_room_cost_recalculation_prevents_double_surcharge()
    {
        $hostel = Hostel::create([
            'name' => 'Guard Test Hostel',
            'location' => 'amamoma',
            'address' => '789 Guard Blvd',
            'email' => 'guard@example.com'
        ]);

        $room = Room::create([
            'hostel_id' => $hostel->id,
            'number' => 'C102',
            'room_type' => 'single_room',
            'capacity' => 2,
            'room_cost' => 1000.00,
            'status' => 'available',
            'gender' => 'any',
        ]);

        $this->assertEquals(1051.20, (float) $room->room_cost);

        // Save again without changing room_cost (e.g. updating description)
        $room->description = 'Updated Description';
        $room->save();

        // It should still be 1051.20, not increased again
        $this->assertEquals(1051.20, (float) $room->fresh()->room_cost);

        // Try updating cost slightly to a new value (e.g., 1200.00 base)
        $room->room_cost = 1200.00;
        $room->save();

        // 1200.00 * 1.0512 = 1261.44
        $this->assertEquals(1261.44, (float) $room->fresh()->room_cost);
    }

    /**
     * Test that the base price can be accurately reconstructed from the database room_cost.
     */
    public function test_base_price_can_be_reconstructed_for_split_payments()
    {
        $roomCost = 1051.20; // represents 1000 base + 5.12% fees

        $totalSurchargeRate = config('payments.total_surcharge_rate', 0.0512);
        $basePrice = $roomCost / (1 + $totalSurchargeRate);

        $this->assertEquals(1000.00, round($basePrice, 2));

        $splitService = app(PaystackSplitService::class);
        $transactionCharge = $splitService->getTransactionChargeInPesewas($basePrice);

        // Platform fee rate is 0.028 (2.8%). 2.8% of 1000.00 is 28.00 GHS.
        // In pesewas: 2800
        $this->assertEquals(2800, $transactionCharge);
    }
}
