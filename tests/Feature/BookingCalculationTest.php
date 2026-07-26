<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Hostel;
use App\Models\Room;
use App\Http\Controllers\BookingController;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BookingCalculationTest extends TestCase
{
    use RefreshDatabase;

    public function test_calculate_returns_expected_fees_and_total()
    {
        $hostel = Hostel::create([
            'name' => 'Test Hostel',
            'location' => 'amamoma',
            'address' => '123 Test Ave',
            'email' => 'hostel@example.com'
        ]);

        $room = Room::create([
            'number' => '101',
            'capacity' => 4,
            'hostel_id' => $hostel->id,
            'gender' => 'any',
            'status' => 'available',
            'room_type' => 'single_room',
            'room_cost' => 200.00,
            'current_occupancy' => 0,
        ]);

        $checkIn = Carbon::now()->addDays(2)->toDateString();
        $checkOut = Carbon::now()->addDays(5)->toDateString();

        $request = Request::create('/bookings/calculate', 'POST', [
            'check_in_date' => $checkIn,
            'check_out_date' => $checkOut,
            'room_id' => $room->id,
            'room_cost' => 200.00,
        ]);

        $controller = new BookingController();
        $response = $controller->calculate($request);

        $this->assertTrue($response->getStatusCode() === 200);

        $data = $response->getData(true);

        $this->assertArrayHasKey('success', $data);
        $this->assertTrue($data['success']);
        $this->assertArrayHasKey('nights', $data);
        $this->assertArrayHasKey('room_cost', $data);
        $this->assertArrayHasKey('total', $data);

        $this->assertEquals(3, $data['nights']);
        $this->assertEquals(200.00, $data['room_cost']);

        // Total service charge is 5.10% (0.051)
        $expectedTotal = round(200.00 + round(200.00 * 0.051, 2), 2);
        $this->assertEquals($expectedTotal, $data['total']);
        $this->assertArrayNotHasKey('paystack_fee', $data);
    }
}
