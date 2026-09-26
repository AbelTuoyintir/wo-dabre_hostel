<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Hostel;
use App\Models\Room;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class BookingStatusOccupancyTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $manager;
    private User $student;
    private Hostel $hostel;
    private Room $room;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'gender' => 'male',
            'email_verified_at' => now(),
        ]);

        $this->manager = User::create([
            'name' => 'Manager User',
            'email' => 'manager@example.com',
            'password' => Hash::make('password123'),
            'role' => 'hostel_manager',
            'gender' => 'female',
            'email_verified_at' => now(),
        ]);

        $this->student = User::create([
            'name' => 'Student User',
            'email' => 'student@example.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
            'gender' => 'male',
            'email_verified_at' => now(),
        ]);

        $this->hostel = Hostel::create([
            'name' => 'Test Hostel',
            'location' => 'amamoma',
            'address' => '123 Main St',
            'email' => 'hostel@example.com',
            'manager_id' => $this->manager->id,
            'is_approved' => true,
            'status' => 'active',
        ]);

        $this->room = Room::create([
            'hostel_id' => $this->hostel->id,
            'number' => '101',
            'capacity' => 2,
            'current_occupancy' => 0,
            'room_type' => 'shared_2',
            'gender' => 'any',
            'status' => 'available',
            'room_cost' => 1000,
        ]);
    }

    public function test_admin_updating_booking_status_synchronizes_occupancy(): void
    {
        $booking = Booking::create([
            'booking_number' => 'BK123456',
            'user_id' => $this->student->id,
            'hostel_id' => $this->hostel->id,
            'room_id' => $this->room->id,
            'check_in_date' => now(),
            'check_out_date' => now()->addMonths(4),
            'booking_status' => 'pending',
            'payment_status' => 'pending',
            'total_amount' => 1000,
            'amount_paid' => 0,
            'balance_due' => 1000,
        ]);

        $this->assertEquals(0, $this->room->fresh()->current_occupancy);

        // Admin confirms booking -> occupancy increases to 1
        $response = $this->actingAs($this->admin)
            ->patch(route('admin.bookings.status', $booking->uuid), [
                'booking_status' => 'confirmed',
            ]);

        $response->assertRedirect();
        $this->assertEquals(1, $this->room->fresh()->current_occupancy);

        // Admin cancels booking -> occupancy decreases back to 0
        $response = $this->actingAs($this->admin)
            ->patch(route('admin.bookings.status', $booking->uuid), [
                'booking_status' => 'cancelled',
            ]);

        $response->assertRedirect();
        $this->assertEquals(0, $this->room->fresh()->current_occupancy);
    }

    public function test_hostel_manager_updating_booking_status_synchronizes_occupancy(): void
    {
        $booking = Booking::create([
            'booking_number' => 'BK654321',
            'user_id' => $this->student->id,
            'hostel_id' => $this->hostel->id,
            'room_id' => $this->room->id,
            'check_in_date' => now(),
            'check_out_date' => now()->addMonths(4),
            'booking_status' => 'pending',
            'payment_status' => 'pending',
            'total_amount' => 1000,
            'amount_paid' => 0,
            'balance_due' => 1000,
        ]);

        $this->assertEquals(0, $this->room->fresh()->current_occupancy);

        // Manager confirms booking -> occupancy increases to 1
        $response = $this->actingAs($this->manager)
            ->patch(route('hostel-manager.bookings.status', $booking->uuid), [
                'status' => 'confirmed',
            ]);

        $response->assertRedirect();
        $this->assertEquals(1, $this->room->fresh()->current_occupancy);

        // Manager cancels booking -> occupancy decreases back to 0
        $response = $this->actingAs($this->manager)
            ->patch(route('hostel-manager.bookings.status', $booking->uuid), [
                'status' => 'cancelled',
                'cancellation_reason' => 'Student requested cancellation',
            ]);

        $response->assertRedirect();
        $this->assertEquals(0, $this->room->fresh()->current_occupancy);
    }
}
