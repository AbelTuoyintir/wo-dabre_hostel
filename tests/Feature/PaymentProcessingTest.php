<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Hostel;
use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Models\Booking;
use App\Models\Payment;
use App\Http\Controllers\BookingController;
use Unicodeveloper\Paystack\Facades\Paystack;

class PaymentProcessingTest extends TestCase
{
    use RefreshDatabase;

    public function test_handle_payment_callback_creates_booking_and_payment_for_guest()
    {
        // Create hostel and room
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
            'gender' => 'male',
            'status' => 'available',
            'room_type' => 'single_room',
            'room_cost' => 200.00,
            'current_occupancy' => 0,
        ]);

        // Prepare fake payment details returned by Paystack
        $metadata = [
            'is_guest' => true,
            'guest_data' => [
                'name' => 'Guest User',
                'email' => 'guest@example.com',
                'phone' => '0123456789',
                'gender' => 'male',
                'temp_password' => 'TempPass123!',
            ],
            'booking_data' => [
                'room_id' => $room->id,
                'hostel_id' => $hostel->id,
                'check_in_date' => now()->addDays(2)->toDateString(),
                'check_out_date' => now()->addDays(5)->toDateString(),
                'room_cost' => 200.00,
                'agent_fee' => 150,
                'net_amount' => 350.00,
            ],
        ];

        $paymentDetails = [
            'status' => true,
            'data' => [
                'status' => 'success',
                'reference' => 'REF123456',
                'currency' => 'GHS',
                'channel' => 'card',
                'metadata' => $metadata,
            ],
        ];

        Paystack::shouldReceive('getPaymentData')->once()->andReturn($paymentDetails);

        Mail::fake();

        $controller = new BookingController();

        // Call the callback handler
        $response = $controller->handlePaymentCallback('paystack');

        // After processing, a booking and payment should exist
        $this->assertDatabaseHas('users', ['email' => 'guest@example.com']);
        $user = User::where('email', 'guest@example.com')->first();

        $this->assertDatabaseHas('bookings', [
            'user_id' => $user->id,
            'room_id' => $room->id,
            'hostel_id' => $hostel->id,
            'payment_status' => 'paid',
        ]);

        $this->assertDatabaseHas('payments', [
            'user_id' => $user->id,
            'payment_method' => 'card',
            'status' => 'completed',
        ]);

        // Room occupancy should have incremented
        $this->assertEquals(1, Room::find($room->id)->current_occupancy);
    }
}
