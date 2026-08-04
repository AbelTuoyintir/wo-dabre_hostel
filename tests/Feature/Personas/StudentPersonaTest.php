<?php

namespace Tests\Feature\Personas;

use App\Models\User;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Hostel;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StudentPersonaTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_dashboard_requires_student_role(): void
    {
        $user = User::create([
            'name' => 'Not Student',
            'email' => 'ns'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'phone' => '08011122233',
            'role' => 'hostel_agent',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('student.dashboard'))
            ->assertRedirect();
    }

    public function test_student_dashboard_can_access_when_student_role(): void
    {
        $student = User::create([
            'name' => 'Student',
            'email' => 'st'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'phone' => '08011122233',
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($student)
            ->get(route('student.dashboard'))
            ->assertOk();
    }

    public function test_student_can_view_bookings_with_receipt_button(): void
    {
        $student = User::create([
            'name' => 'Student Payer',
            'email' => 'st'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'phone' => '08011122233',
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        $hostel = Hostel::create([
            'name' => 'Test Hostel Alpha',
            'location' => 'amamoma',
            'address' => '123 Test Ave',
            'email' => 'hostelalpha@example.com'
        ]);

        $room = Room::create([
            'number' => '202',
            'capacity' => 2,
            'hostel_id' => $hostel->id,
            'gender' => 'any',
            'status' => 'available',
            'room_type' => 'single_room',
            'room_cost' => 300.00,
            'current_occupancy' => 0,
        ]);

        $booking = Booking::create([
            'user_id' => $student->id,
            'hostel_id' => $hostel->id,
            'room_id' => $room->id,
            'check_in_date' => Carbon::now()->addDays(1)->toDateString(),
            'check_out_date' => Carbon::now()->addDays(5)->toDateString(),
            'total_amount' => 300.00,
            'booking_status' => 'confirmed',
            'payment_status' => 'paid',
            'booking_number' => 'BKREF' . uniqid(),
        ]);

        $payment = Payment::create([
            'booking_id' => $booking->id,
            'user_id' => $student->id,
            'amount' => 300.00,
            'payment_method' => 'card',
            'transaction_id' => 'TXN' . uniqid(),
            'status' => 'completed',
            'reference' => 'PREF' . uniqid(),
        ]);

        $response = $this->actingAs($student)
            ->get(route('student.bookings'));

        $response->assertOk();
        $response->assertSee('Receipt');
        $response->assertSee(route('student.payments.receipt', $payment->uuid));
    }
}
