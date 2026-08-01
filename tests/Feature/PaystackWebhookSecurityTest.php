<?php

namespace Tests\Feature;

use App\Http\Controllers\BookingController;
use App\Models\Hostel;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
use Unicodeveloper\Paystack\Facades\Paystack;

class PaystackWebhookSecurityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test webhook succeeds with a valid signature.
     */
    public function test_webhook_succeeds_with_valid_signature()
    {
        // 1. Setup config secret key
        config(['paystack.secretKey' => 'super-secret-key']);

        $payload = json_encode([
            'event' => 'refund.processed',
            'data' => [
                'transaction' => [
                    'reference' => 'non-existent-ref',
                ],
            ],
        ]);

        // 2. Compute correct signature
        $validSignature = hash_hmac('sha512', $payload, 'super-secret-key');

        // 3. Create Request
        $request = Request::create('/webhooks/paystack-refund', 'POST', [], [], [], [], $payload);
        $request->headers->set('x-paystack-signature', $validSignature);

        // 4. Handle
        $controller = new BookingController;
        $response = $controller->handleRefundWebhook($request);

        // 5. Assert successful signature verification (returns success json even if record isn't found)
        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertEquals('success', $data['status']);
    }

    /**
     * Test webhook rejects invalid signature.
     */
    public function test_webhook_rejects_invalid_signature()
    {
        config(['paystack.secretKey' => 'super-secret-key']);

        $payload = json_encode(['event' => 'refund.processed']);
        $request = Request::create('/webhooks/paystack-refund', 'POST', [], [], [], [], $payload);
        $request->headers->set('x-paystack-signature', 'wrong-signature-here');

        $controller = new BookingController;
        $response = $controller->handleRefundWebhook($request);

        $this->assertEquals(401, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertEquals('Invalid signature', $data['error']);
    }

    /**
     * Test webhook rejects when signature header is missing or empty.
     */
    public function test_webhook_rejects_empty_signature()
    {
        config(['paystack.secretKey' => 'super-secret-key']);

        $payload = json_encode(['event' => 'refund.processed']);
        $request = Request::create('/webhooks/paystack-refund', 'POST', [], [], [], [], $payload);
        // Do not set header

        $controller = new BookingController;
        $response = $controller->handleRefundWebhook($request);

        $this->assertEquals(401, $response->getStatusCode());
    }

    /**
     * Test webhook rejects when the server's secretKey is empty or not configured.
     */
    public function test_webhook_rejects_when_secret_key_is_empty()
    {
        // Secret key is empty
        config(['paystack.secretKey' => '']);

        $payload = json_encode(['event' => 'refund.processed']);

        // If secret is empty, hash_hmac with empty string might produce a hash,
        // but verifyPaystackSignature should reject because secretKey is empty.
        $signature = hash_hmac('sha512', $payload, '');

        $request = Request::create('/webhooks/paystack-refund', 'POST', [], [], [], [], $payload);
        $request->headers->set('x-paystack-signature', $signature);

        $controller = new BookingController;
        $response = $controller->handleRefundWebhook($request);

        $this->assertEquals(401, $response->getStatusCode());
    }

    /**
     * Test split payment webhook endpoint processes successful charge event for a guest booking.
     */
    public function test_split_payment_webhook_processes_guest_booking_success()
    {
        config(['paystack.secretKey' => 'super-secret-key']);
        Mail::fake();

        // Create hostel and room
        $hostel = Hostel::create([
            'name' => 'Test Hostel',
            'location' => 'amamoma',
            'address' => '123 Test Ave',
            'email' => 'hostel@example.com',
            'subaccount_code' => 'ACCT_split123',
        ]);

        $room = Room::create([
            'number' => '201',
            'capacity' => 4,
            'hostel_id' => $hostel->id,
            'gender' => 'male',
            'status' => 'available',
            'room_type' => 'single_room',
            'room_cost' => 200.00,
            'current_occupancy' => 0,
        ]);

        $payload = json_encode([
            'event' => 'charge.success',
            'data' => [
                'reference' => 'TX_WEBHOOK_GUEST',
                'amount' => 20512, // in pesewas
                'currency' => 'GHS',
                'channel' => 'card',
                'metadata' => [
                    'is_guest' => true,
                    'guest_data' => [
                        'name' => 'Webhook Guest',
                        'email' => 'webhook_guest@example.com',
                        'phone' => '0555555555',
                        'gender' => 'male',
                        'temp_password' => 'SecurePass123!',
                    ],
                    'booking_data' => [
                        'room_id' => $room->id,
                        'hostel_id' => $hostel->id,
                        'check_in_date' => now()->addDays(2)->toDateString(),
                        'check_out_date' => now()->addDays(5)->toDateString(),
                        'room_cost' => 200.00,
                        'net_amount' => 200.00,
                        'final_total' => 205.12,
                    ],
                ],
            ],
        ]);

        $signature = hash_hmac('sha512', $payload, 'super-secret-key');

        $response = $this->postJson(
            route('bookings.payment.webhook', ['gateway' => 'paystack']),
            json_decode($payload, true),
            ['x-paystack-signature' => $signature]
        );

        $response->assertStatus(200);
        $response->assertJsonFragment(['status' => 'success']);

        // Verify database state
        $this->assertDatabaseHas('users', ['email' => 'webhook_guest@example.com']);
        $user = User::where('email', 'webhook_guest@example.com')->first();

        $this->assertDatabaseHas('bookings', [
            'user_id' => $user->id,
            'room_id' => $room->id,
            'payment_status' => 'paid',
            'transaction_id' => 'TX_WEBHOOK_GUEST',
        ]);

        $this->assertDatabaseHas('payments', [
            'user_id' => $user->id,
            'transaction_id' => 'TX_WEBHOOK_GUEST',
            'status' => 'completed',
        ]);
    }

    /**
     * Test webhook and callback race condition robustness.
     */
    public function test_webhook_and_callback_race_condition_robustness()
    {
        config(['paystack.secretKey' => 'super-secret-key']);
        Mail::fake();

        $hostel = Hostel::create([
            'name' => 'Test Hostel',
            'location' => 'amamoma',
            'address' => '123 Test Ave',
            'email' => 'hostel@example.com',
        ]);

        $room = Room::create([
            'number' => '202',
            'capacity' => 4,
            'hostel_id' => $hostel->id,
            'gender' => 'male',
            'status' => 'available',
            'room_type' => 'single_room',
            'room_cost' => 200.00,
            'current_occupancy' => 0,
        ]);

        $metadata = [
            'is_guest' => true,
            'guest_data' => [
                'name' => 'Race Guest',
                'email' => 'race_guest@example.com',
                'phone' => '0555555555',
                'gender' => 'male',
                'temp_password' => 'SecurePass123!',
            ],
            'booking_data' => [
                'room_id' => $room->id,
                'hostel_id' => $hostel->id,
                'check_in_date' => now()->addDays(2)->toDateString(),
                'check_out_date' => now()->addDays(5)->toDateString(),
                'room_cost' => 200.00,
                'net_amount' => 200.00,
                'final_total' => 205.12,
            ],
        ];

        // 1. Simulate Webhook running first
        $payload = json_encode([
            'event' => 'charge.success',
            'data' => [
                'reference' => 'TX_RACE_123',
                'amount' => 20512,
                'currency' => 'GHS',
                'channel' => 'card',
                'metadata' => $metadata,
            ],
        ]);

        $signature = hash_hmac('sha512', $payload, 'super-secret-key');

        $response = $this->postJson(
            route('bookings.payment.webhook', ['gateway' => 'paystack']),
            json_decode($payload, true),
            ['x-paystack-signature' => $signature]
        );
        $response->assertStatus(200);

        // Verify user & booking created
        $this->assertDatabaseHas('users', ['email' => 'race_guest@example.com']);
        $user = User::where('email', 'race_guest@example.com')->first();
        $this->assertDatabaseHas('bookings', ['transaction_id' => 'TX_RACE_123', 'user_id' => $user->id]);

        // 2. Simulate standard user redirect callback running after webhook has already processed it
        $paymentDetails = [
            'status' => true,
            'data' => [
                'status' => 'success',
                'reference' => 'TX_RACE_123',
                'currency' => 'GHS',
                'channel' => 'card',
                'metadata' => $metadata,
            ],
        ];

        Paystack::shouldReceive('getPaymentData')->once()->andReturn($paymentDetails);

        $controller = new BookingController;
        $callbackResponse = $controller->handlePaymentCallback('paystack');

        // It should gracefully redirect to the bookings page instead of throwing exception/duplicate user error
        $this->assertTrue($callbackResponse->isRedirect());
        $this->assertStringContainsString('/bookings/', $callbackResponse->getTargetUrl());

        // Room occupancy should be exactly 1, not duplicated
        $this->assertEquals(1, Room::find($room->id)->current_occupancy);
    }
}
