<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Http\Controllers\BookingController;
use Illuminate\Http\Request;
use Illuminate\Foundation\Testing\RefreshDatabase;

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
                    'reference' => 'non-existent-ref'
                ]
            ]
        ]);

        // 2. Compute correct signature
        $validSignature = hash_hmac('sha512', $payload, 'super-secret-key');

        // 3. Create Request
        $request = Request::create('/webhooks/paystack-refund', 'POST', [], [], [], [], $payload);
        $request->headers->set('x-paystack-signature', $validSignature);

        // 4. Handle
        $controller = new BookingController();
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

        $controller = new BookingController();
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

        $controller = new BookingController();
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

        $controller = new BookingController();
        $response = $controller->handleRefundWebhook($request);

        $this->assertEquals(401, $response->getStatusCode());
    }
}
