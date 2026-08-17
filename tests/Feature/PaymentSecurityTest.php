<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Hostel;
use App\Models\Payment;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_view_guest_payment_receipt(): void
    {
        // Guest payment where user_id is null
        $payment = Payment::create([
            'user_id' => null,
            'booking_id' => null,
            'reference' => 'TEST-GUEST-PAY-001',
            'amount' => 500,
            'currency' => 'GHS',
            'payment_method' => 'card',
            'status' => 'completed',
        ]);

        $response = $this->get("/student/payments/{$payment->id}/receipt");

        // Should abort 403 Forbidden (or redirect to login depending on middleware / controller guard)
        $this->assertTrue(in_array($response->status(), [403, 302]));
    }

    public function test_user_cannot_view_another_users_payment_receipt(): void
    {
        $user1 = User::factory()->create(['gender' => 'male', 'role' => 'student']);
        $user2 = User::factory()->create(['gender' => 'female', 'role' => 'student']);

        $payment = Payment::create([
            'user_id' => $user1->id,
            'booking_id' => null,
            'reference' => 'TEST-USER1-PAY-001',
            'amount' => 500,
            'currency' => 'GHS',
            'payment_method' => 'momo',
            'status' => 'completed',
        ]);

        $response = $this->actingAs($user2)->get("/student/payments/{$payment->id}/receipt");

        $response->assertStatus(403);
    }

    public function test_user_can_view_their_own_payment_receipt(): void
    {
        $user = User::factory()->create(['gender' => 'male', 'role' => 'student']);

        $payment = Payment::create([
            'user_id' => $user->id,
            'booking_id' => null,
            'reference' => 'TEST-OWNER-PAY-001',
            'amount' => 500,
            'currency' => 'GHS',
            'payment_method' => 'card',
            'status' => 'completed',
        ]);

        $response = $this->actingAs($user)->get("/student/payments/{$payment->id}/receipt");

        $response->assertStatus(200);
    }
}
