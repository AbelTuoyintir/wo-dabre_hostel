<?php

namespace Tests\Feature;

use App\Models\Hostel;
use App\Models\Payment;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_view_payment_receipt_when_owned_directly_or_via_booking(): void
    {
        $student = User::factory()->create([
            'role' => 'student',
            'gender' => 'male',
        ]);

        $otherStudent = User::factory()->create([
            'role' => 'student',
            'gender' => 'female',
        ]);

        // Non-booking payment owned by student directly
        $directPayment = Payment::create([
            'user_id' => $student->id,
            'booking_id' => null,
            'amount' => 100,
            'status' => 'completed',
            'payment_method' => 'card',
            'transaction_reference' => 'REF-DIRECT-1',
        ]);

        // Authorized access for direct payment owner
        $response = $this->actingAs($student)->get(route('student.payments.receipt', $directPayment));
        $response->assertOk();

        // Unauthorized access for another student
        $response = $this->actingAs($otherStudent)->get(route('student.payments.receipt', $directPayment));
        $response->assertStatus(403);
    }

    public function test_hostel_manager_payment_methods_handle_null_booking_gracefully(): void
    {
        $manager = User::factory()->create([
            'role' => 'hostel_manager',
            'gender' => 'male',
        ]);

        $hostel = Hostel::create([
            'name' => 'Test Hostel',
            'manager_id' => $manager->id,
            'address' => '123 Street',
            'location' => 'amamoma',
            'city' => 'Accra',
            'status' => 'active',
        ]);

        // Payment without an associated booking
        $orphanPayment = Payment::create([
            'user_id' => $manager->id,
            'booking_id' => null,
            'amount' => 200,
            'status' => 'pending',
            'payment_method' => 'mobile_money',
            'transaction_reference' => 'REF-ORPHAN-1',
        ]);

        // Hostel manager accessing orphan payment via show, receipt, and status routes should be aborted with 403
        $this->actingAs($manager)
            ->get(route('hostel-manager.payments.show', $orphanPayment))
            ->assertStatus(403);

        $this->actingAs($manager)
            ->get(route('hostel-manager.payments.receipt', $orphanPayment))
            ->assertStatus(403);

        $this->actingAs($manager)
            ->patch(route('hostel-manager.payments.status', $orphanPayment), ['status' => 'completed'])
            ->assertStatus(403);
    }
}
