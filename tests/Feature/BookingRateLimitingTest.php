<?php

namespace Tests\Feature;

use App\Models\Hostel;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingRateLimitingTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_booking_store_endpoint_is_rate_limited(): void
    {
        $hostel = Hostel::create([
            'name' => 'Rate Limit Guest Hostel',
            'location' => 'amamoma',
            'address' => '123 Guest Rd',
            'email' => 'guestrate@example.com',
            'is_approved' => true,
            'status' => 'active',
        ]);

        $room = Room::create([
            'number' => '101',
            'capacity' => 2,
            'hostel_id' => $hostel->id,
            'gender' => 'male',
            'status' => 'available',
            'room_type' => 'single_room',
            'room_cost' => 1000.00,
            'current_occupancy' => 0,
        ]);

        $payload = [
            'room_id' => $room->id,
            'hostel_id' => $hostel->id,
            'check_in_date' => now()->addDays(2)->format('Y-m-d'),
            'check_out_date' => now()->addDays(10)->format('Y-m-d'),
            'name' => 'John Doe',
            'email' => 'john.rate.test@example.com',
            'phone' => '0241234567',
            'gender' => 'male',
        ];

        // Send 10 valid requests (the limit)
        for ($i = 0; $i < 10; $i++) {
            $payload['email'] = "john.rate.test{$i}@example.com";
            $response = $this->post(route('bookings.store'), $payload);
            $this->assertNotEquals(429, $response->getStatusCode());
        }

        // The 11th request should be throttled (HTTP 429)
        $payload['email'] = 'john.rate.test.exceeded@example.com';
        $response = $this->post(route('bookings.store'), $payload);
        $response->assertStatus(429);
    }

    public function test_student_booking_store_endpoint_is_rate_limited(): void
    {
        $student = User::factory()->create([
            'role' => 'student',
            'gender' => 'female',
            'is_active' => true,
        ]);

        $hostel = Hostel::create([
            'name' => 'Rate Limit Student Hostel',
            'location' => 'amamoma',
            'address' => '456 Student Rd',
            'email' => 'studentrate@example.com',
            'is_approved' => true,
            'status' => 'active',
        ]);

        $room = Room::create([
            'number' => '202',
            'capacity' => 2,
            'hostel_id' => $hostel->id,
            'gender' => 'female',
            'status' => 'available',
            'room_type' => 'single_room',
            'room_cost' => 1200.00,
            'current_occupancy' => 0,
        ]);

        $payload = [
            'room_id' => $room->id,
            'hostel_id' => $hostel->id,
            'check_in_date' => now()->addDays(2)->format('Y-m-d'),
            'check_out_date' => now()->addDays(10)->format('Y-m-d'),
            'gender' => 'female',
        ];

        // Send 10 requests as authenticated student
        for ($i = 0; $i < 10; $i++) {
            $response = $this->actingAs($student)->post(route('bookings.store.student'), $payload);
            $this->assertNotEquals(429, $response->getStatusCode());
        }

        // The 11th request should be throttled (HTTP 429)
        $response = $this->actingAs($student)->post(route('bookings.store.student'), $payload);
        $response->assertStatus(429);
    }
}
