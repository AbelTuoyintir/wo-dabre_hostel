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

        // Calculation according to controller:
        // agentFee = 150, systemCharge = 20, paystackFee = subTotal * 0.0195
        $agentFee = 150;
        $systemCharge = 20;
        $subTotal = 200.00 + $agentFee + $systemCharge;
        $expectedPaystack = round($subTotal * 0.0195, 2);
        $expectedTotal = round($subTotal + ($subTotal * 0.0195), 2);

        $this->assertEquals($expectedPaystack, $data['paystack_fee']);
        $this->assertEquals($expectedTotal, $data['total']);
    }
}
