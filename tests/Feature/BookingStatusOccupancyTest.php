<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Hostel;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingStatusOccupancyTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_updating_booking_status_synchronizes_room_occupancy(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $student = User::factory()->create(['role' => 'student', 'is_active' => true]);

        $hostel = Hostel::create([
            'name' => 'Test Admin Hostel',
            'location' => 'amamoma',
            'description' => 'Test Hostel',
            'is_approved' => true,
            'status' => 'active',
        ]);

        $room = Room::create([
            'hostel_id' => $hostel->id,
            'number' => 'A101',
            'room_type' => 'single_room',
            'capacity' => 2,
            'current_occupancy' => 0,
            'gender' => 'male',
            'status' => 'available',
            'room_cost' => 500,
        ]);

        $booking = Booking::create([
            'booking_number' => 'BN-TEST101',
            'user_id' => $student->id,
            'room_id' => $room->id,
            'hostel_id' => $hostel->id,
            'check_in_date' => now()->addDay()->format('Y-m-d'),
            'check_out_date' => now()->addDays(5)->format('Y-m-d'),
            'total_amount' => 100.00,
            'amount_paid' => 0.00,
            'payment_status' => 'pending',
            'booking_status' => 'pending',
        ]);

        $this->assertEquals(0, $room->fresh()->current_occupancy);

        // Admin confirms the pending booking -> occupancy should increment to 1
        $response = $this->actingAs($admin)->patch(route('admin.bookings.status', $booking->uuid), [
            'booking_status' => 'confirmed',
        ]);

        $response->assertRedirect();
        $this->assertEquals('confirmed', $booking->fresh()->booking_status);
        $this->assertEquals(1, $room->fresh()->current_occupancy);

        // Admin cancels the confirmed booking -> occupancy should decrement to 0
        $response = $this->actingAs($admin)->patch(route('admin.bookings.status', $booking->uuid), [
            'booking_status' => 'cancelled',
        ]);

        $response->assertRedirect();
        $this->assertEquals('cancelled', $booking->fresh()->booking_status);
        $this->assertEquals(0, $room->fresh()->current_occupancy);
    }

    public function test_hostel_manager_updating_booking_status_synchronizes_room_occupancy(): void
    {
        $manager = User::factory()->create(['role' => 'hostel_manager', 'is_active' => true]);
        $student = User::factory()->create(['role' => 'student', 'is_active' => true]);

        $hostel = Hostel::create([
            'name' => 'Test Manager Hostel',
            'manager_id' => $manager->id,
            'location' => 'amamoma',
            'description' => 'Test Hostel 2',
            'is_approved' => true,
            'status' => 'active',
        ]);

        $room = Room::create([
            'hostel_id' => $hostel->id,
            'number' => 'B102',
            'room_type' => 'single_room',
            'capacity' => 2,
            'current_occupancy' => 0,
            'gender' => 'male',
            'status' => 'available',
            'room_cost' => 500,
        ]);

        $booking = Booking::create([
            'booking_number' => 'BN-TEST102',
            'user_id' => $student->id,
            'room_id' => $room->id,
            'hostel_id' => $hostel->id,
            'check_in_date' => now()->addDay()->format('Y-m-d'),
            'check_out_date' => now()->addDays(5)->format('Y-m-d'),
            'total_amount' => 100.00,
            'amount_paid' => 0.00,
            'payment_status' => 'pending',
            'booking_status' => 'pending',
        ]);

        $this->assertEquals(0, $room->fresh()->current_occupancy);

        // Hostel manager confirms booking -> occupancy increments
        $response = $this->actingAs($manager)->patch(route('hostel-manager.bookings.status', $booking->uuid), [
            'status' => 'confirmed',
        ]);

        $response->assertRedirect();
        $this->assertEquals('confirmed', $booking->fresh()->booking_status);
        $this->assertEquals(1, $room->fresh()->current_occupancy);

        // Hostel manager cancels booking -> occupancy decrements
        $response = $this->actingAs($manager)->patch(route('hostel-manager.bookings.status', $booking->uuid), [
            'status' => 'cancelled',
            'cancellation_reason' => 'Student requested cancellation',
        ]);

        $response->assertRedirect();
        $this->assertEquals('cancelled', $booking->fresh()->booking_status);
        $this->assertEquals(0, $room->fresh()->current_occupancy);
    }
}
