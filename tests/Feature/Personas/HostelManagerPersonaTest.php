<?php

namespace Tests\Feature\Personas;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class HostelManagerPersonaTest extends TestCase
{
    use RefreshDatabase;

    public function test_hostel_manager_dashboard_requires_hostel_manager_role(): void
    {
        $user = User::create([
            'name' => 'Not Manager',
            'email' => 'nm'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'phone' => '08011122233',
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('hostel-manager.dashboard'))
            ->assertRedirect();
    }

    public function test_hostel_manager_can_access_dashboard(): void
    {
        $manager = User::create([
            'name' => 'Manager',
            'email' => 'm'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'phone' => '08011122233',
            'role' => 'hostel_manager',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($manager)
            ->get(route('hostel-manager.dashboard'))
            ->assertOk();
    }

    public function test_hostel_manager_cannot_view_room_of_another_manager(): void
    {
        $manager1 = User::create([
            'name' => 'Manager 1',
            'email' => 'm1'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'phone' => '08011122233',
            'role' => 'hostel_manager',
            'email_verified_at' => now(),
        ]);

        $manager2 = User::create([
            'name' => 'Manager 2',
            'email' => 'm2'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'phone' => '08011122234',
            'role' => 'hostel_manager',
            'email_verified_at' => now(),
        ]);

        $hostel1 = \App\Models\Hostel::create([
            'name' => 'Hostel 1',
            'location' => 'amamoma',
            'address' => '123 Test Ave',
            'email' => 'hostel1@example.com',
            'manager_id' => $manager1->id,
            'is_approved' => true,
        ]);

        $hostel2 = \App\Models\Hostel::create([
            'name' => 'Hostel 2',
            'location' => 'amamoma',
            'address' => '123 Test Ave',
            'email' => 'hostel2@example.com',
            'manager_id' => $manager2->id,
            'is_approved' => true,
        ]);

        $room2 = \App\Models\Room::create([
            'number' => '201',
            'capacity' => 4,
            'hostel_id' => $hostel2->id,
            'gender' => 'any',
            'status' => 'available',
            'room_type' => 'single_room',
            'room_cost' => 200.00,
            'current_occupancy' => 0,
        ]);

        // Manager 1 should not be able to view Room 2 (belongs to Hostel 2 managed by Manager 2)
        $this->actingAs($manager1)
            ->get(route('hostel-manager.rooms.show', ['room' => $room2->uuid]))
            ->assertStatus(403);
    }

    public function test_hostel_manager_can_view_own_room(): void
    {
        $manager = User::create([
            'name' => 'Manager',
            'email' => 'm'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'phone' => '08011122233',
            'role' => 'hostel_manager',
            'email_verified_at' => now(),
        ]);

        $hostel = \App\Models\Hostel::create([
            'name' => 'Hostel 1',
            'location' => 'amamoma',
            'address' => '123 Test Ave',
            'email' => 'hostel1@example.com',
            'manager_id' => $manager->id,
            'is_approved' => true,
        ]);

        $room = \App\Models\Room::create([
            'number' => '101',
            'capacity' => 4,
            'hostel_id' => $hostel->id,
            'gender' => 'any',
            'status' => 'available',
            'room_type' => 'single_room',
            'room_cost' => 200.00,
            'current_occupancy' => 0,
        ]);

        // Manager should be able to view their own room
        $this->actingAs($manager)
            ->get(route('hostel-manager.rooms.show', ['room' => $room->uuid]))
            ->assertStatus(200);
    }
}
