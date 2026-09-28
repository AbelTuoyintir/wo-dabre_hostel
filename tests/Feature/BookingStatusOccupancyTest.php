<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Hostel;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class BookingStatusOccupancyTest extends TestCase
{
    use RefreshDatabase;

    public function test_cancelling_booking_decrements_room_occupancy_safely()
    {
        $student = User::factory()->create([
            'role' => 'student',
            'gender' => 'male',
        ]);

        $hostel = Hostel::create([
            'name' => 'Test Heights',
            'location' => 'amamoma',
            'address' => '123 University Way',
            'is_approved' => true,
            'status' => 'active',
        ]);

        $room = Room::create([
            'hostel_id' => $hostel->id,
            'number' => 'R101',
            'capacity' => 2,
            'current_occupancy' => 1,
            'gender' => 'male',
            'status' => 'available',
            'room_type' => 'shared_2',
            'room_cost' => 500,
        ]);

        $booking = Booking::create([
            'booking_number' => 'BN-' . Str::random(8),
            'user_id' => $student->id,
            'hostel_id' => $hostel->id,
            'room_id' => $room->id,
            'check_in_date' => now()->addDays(5)->format('Y-m-d'),
            'check_out_date' => now()->addDays(10)->format('Y-m-d'),
            'total_amount' => 500,
            'amount_paid' => 500,
            'payment_status' => 'paid',
            'booking_status' => 'confirmed',
        ]);

        \Unicodeveloper\Paystack\Facades\Paystack::shouldReceive('refund')
            ->once()
            ->andReturn([
                'status' => true,
                'data' => ['reference' => 'REF-' . Str::random(8)],
            ]);

        $payment = \App\Models\Payment::create([
            'user_id' => $student->id,
            'booking_id' => $booking->id,
            'reference' => 'PAY-' . Str::random(8),
            'amount' => 500,
            'currency' => 'GHS',
            'transaction_id' => 'TRX-' . Str::random(8),
            'payment_method' => 'card',
            'status' => 'completed',
        ]);

        $response = $this->actingAs($student)->patch(route('student.bookings.cancel', $booking->uuid), [
            'cancellation_reason' => 'Need to cancel my reservation due to schedule changes.',
        ]);

        $this->assertEquals(0, $room->fresh()->current_occupancy);
        $this->assertEquals('cancelled', $booking->fresh()->booking_status);
    }

    public function test_hostel_manager_destroy_booking_decrements_occupancy_safely()
    {
        $manager = User::factory()->create([
            'role' => 'hostel_manager',
        ]);

        $hostel = Hostel::create([
            'name' => 'Manager Hostel',
            'location' => 'kwaprow',
            'address' => '456 College Rd',
            'manager_id' => $manager->id,
            'is_approved' => true,
            'status' => 'active',
        ]);

        $room = Room::create([
            'hostel_id' => $hostel->id,
            'number' => 'M201',
            'capacity' => 2,
            'current_occupancy' => 1,
            'gender' => 'any',
            'status' => 'available',
            'room_type' => 'shared_2',
            'room_cost' => 600,
        ]);

        $student = User::factory()->create(['role' => 'student']);

        $booking = Booking::create([
            'booking_number' => 'BN-' . Str::random(8),
            'user_id' => $student->id,
            'hostel_id' => $hostel->id,
            'room_id' => $room->id,
            'check_in_date' => now()->addDays(1)->format('Y-m-d'),
            'check_out_date' => now()->addDays(5)->format('Y-m-d'),
            'total_amount' => 600,
            'amount_paid' => 600,
            'payment_status' => 'paid',
            'booking_status' => 'confirmed',
        ]);

        $response = $this->actingAs($manager)->delete(route('hostel-manager.bookings.destroy', $booking->uuid));

        $this->assertEquals(0, $room->fresh()->current_occupancy);
        $this->assertDatabaseMissing('bookings', ['id' => $booking->id]);
    }

    public function test_admin_updating_booking_status_synchronizes_occupancy()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $hostel = Hostel::create([
            'name' => 'Admin Managed Hostel',
            'location' => 'ayensu',
            'address' => '789 Admin Ave',
            'is_approved' => true,
            'status' => 'active',
        ]);

        $room = Room::create([
            'hostel_id' => $hostel->id,
            'number' => 'A301',
            'capacity' => 2,
            'current_occupancy' => 0,
            'gender' => 'any',
            'status' => 'available',
            'room_type' => 'shared_2',
            'room_cost' => 700,
        ]);

        $student = User::factory()->create(['role' => 'student']);

        $booking = Booking::create([
            'booking_number' => 'BN-' . Str::random(8),
            'user_id' => $student->id,
            'hostel_id' => $hostel->id,
            'room_id' => $room->id,
            'check_in_date' => now()->addDays(2)->format('Y-m-d'),
            'check_out_date' => now()->addDays(8)->format('Y-m-d'),
            'total_amount' => 700,
            'amount_paid' => 0,
            'payment_status' => 'pending',
            'booking_status' => 'pending',
        ]);

        // Admin confirms pending booking -> occupancy increments
        $response = $this->actingAs($admin)->patch(route('admin.bookings.status', $booking->uuid), [
            'booking_status' => 'confirmed',
        ]);

        $this->assertEquals(1, $room->fresh()->current_occupancy);

        // Admin cancels confirmed booking -> occupancy decrements
        $response = $this->actingAs($admin)->patch(route('admin.bookings.status', $booking->uuid), [
            'booking_status' => 'cancelled',
        ]);

        $this->assertEquals(0, $room->fresh()->current_occupancy);
    }

    public function test_hostel_manager_updating_booking_status_synchronizes_occupancy()
    {
        $manager = User::factory()->create([
            'role' => 'hostel_manager',
        ]);

        $hostel = Hostel::create([
            'name' => 'Hostel Manager Synchronized Hostel',
            'location' => 'amamoma',
            'address' => '321 Manager Lane',
            'manager_id' => $manager->id,
            'is_approved' => true,
            'status' => 'active',
        ]);

        $room = Room::create([
            'hostel_id' => $hostel->id,
            'number' => 'HM101',
            'capacity' => 2,
            'current_occupancy' => 0,
            'gender' => 'any',
            'status' => 'available',
            'room_type' => 'shared_2',
            'room_cost' => 800,
        ]);

        $student = User::factory()->create(['role' => 'student']);

        $booking = Booking::create([
            'booking_number' => 'BN-' . Str::random(8),
            'user_id' => $student->id,
            'hostel_id' => $hostel->id,
            'room_id' => $room->id,
            'check_in_date' => now()->addDays(2)->format('Y-m-d'),
            'check_out_date' => now()->addDays(8)->format('Y-m-d'),
            'total_amount' => 800,
            'amount_paid' => 800,
            'payment_status' => 'paid',
            'booking_status' => 'confirmed',
        ]);

        // Room occupancy was set to 1 initially upon confirmation
        $room->update(['current_occupancy' => 1]);

        // Hostel manager marks confirmed booking as checked_out -> occupancy decrements
        $response = $this->actingAs($manager)->patch(route('hostel-manager.bookings.status', $booking->uuid), [
            'status' => 'checked_out',
        ]);

        $this->assertEquals(0, $room->fresh()->current_occupancy);
        $this->assertEquals('checked_out', $booking->fresh()->booking_status);
    }
}
