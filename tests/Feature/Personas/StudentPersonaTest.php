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

    public function test_student_cannot_file_complaint_using_another_users_booking_idor(): void
    {
        $studentOne = User::create([
            'name' => 'Student One',
            'email' => 'st1_'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'phone' => '08011122231',
            'role' => 'student',
            'gender' => 'male',
            'email_verified_at' => now(),
        ]);

        $studentTwo = User::create([
            'name' => 'Student Two',
            'email' => 'st2_'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'phone' => '08011122232',
            'role' => 'student',
            'gender' => 'female',
            'email_verified_at' => now(),
        ]);

        $hostel = Hostel::create([
            'name' => 'Security Hostel',
            'location' => 'amamoma',
            'address' => '123 Security Ave',
            'email' => 'sec@example.com',
        ]);

        $room = Room::create([
            'number' => '303',
            'capacity' => 2,
            'hostel_id' => $hostel->id,
            'gender' => 'any',
            'status' => 'available',
            'room_type' => 'single_room',
            'room_cost' => 300.00,
            'current_occupancy' => 0,
        ]);

        $otherBooking = Booking::create([
            'user_id' => $studentTwo->id,
            'hostel_id' => $hostel->id,
            'room_id' => $room->id,
            'check_in_date' => Carbon::now()->addDays(1)->toDateString(),
            'check_out_date' => Carbon::now()->addDays(5)->toDateString(),
            'total_amount' => 300.00,
            'booking_status' => 'confirmed',
            'payment_status' => 'paid',
            'booking_number' => 'BKREF' . uniqid(),
        ]);

        // Student One attempts IDOR by providing Student Two's booking_id
        $response = $this->actingAs($studentOne)->post(route('student.complaints.store'), [
            'subject' => 'Unauthorized complaint reference test',
            'category' => 'maintenance',
            'priority' => 'high',
            'booking_id' => $otherBooking->id,
            'description' => 'This is a test description exceeding twenty characters for validation.',
        ]);

        $response->assertRedirect(route('student.complaints'));
        $response->assertSessionHas('error', 'Please select a valid booking to file a complaint.');

        // Verify no complaint was created linking studentOne to studentTwo's booking
        $this->assertDatabaseMissing('complaints', [
            'user_id' => $studentOne->id,
            'booking_id' => $otherBooking->id,
        ]);
    }
}
