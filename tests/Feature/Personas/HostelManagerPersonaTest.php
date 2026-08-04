<?php

namespace Tests\Feature\Personas;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

use App\Models\Hostel;
use App\Models\Room;

class HostelManagerPersonaTest extends TestCase
{
    use RefreshDatabase;

    public function test_hostel_manager_cannot_view_unowned_room_details_idor(): void
    {
        $manager = User::create([
            'name' => 'Manager One',
            'email' => 'manager1@example.com',
            'password' => Hash::make('password123'),
            'phone' => '08011122231',
            'role' => 'hostel_manager',
            'email_verified_at' => now(),
        ]);

        $otherManager = User::create([
            'name' => 'Manager Two',
            'email' => 'manager2@example.com',
            'password' => Hash::make('password123'),
            'phone' => '08011122232',
            'role' => 'hostel_manager',
            'email_verified_at' => now(),
        ]);

        $hostelOne = Hostel::create([
            'name' => 'Manager One Hostel',
            'location' => 'amamoma',
            'address' => '123 Manager One Ave',
            'email' => 'manager1hostel@example.com',
            'manager_id' => $manager->id,
        ]);

        $hostelTwo = Hostel::create([
            'name' => 'Manager Two Hostel',
            'location' => 'amamoma',
            'address' => '123 Manager Two Ave',
            'email' => 'manager2hostel@example.com',
            'manager_id' => $otherManager->id,
        ]);

        $roomOne = Room::create([
            'number' => '101',
            'capacity' => 2,
            'hostel_id' => $hostelOne->id,
            'gender' => 'any',
            'status' => 'available',
            'room_type' => 'single_room',
            'room_cost' => 150.00,
            'current_occupancy' => 0,
        ]);

        $roomTwo = Room::create([
            'number' => '201',
            'capacity' => 2,
            'hostel_id' => $hostelTwo->id,
            'gender' => 'any',
            'status' => 'available',
            'room_type' => 'single_room',
            'room_cost' => 180.00,
            'current_occupancy' => 0,
        ]);

        // 1. Manager should be able to view their own room details
        $this->actingAs($manager)
            ->get(route('hostel-manager.rooms.show', ['room' => $roomOne->uuid]))
            ->assertOk();

        // 2. Manager should NOT be able to view another manager's room details (IDOR check)
        $this->actingAs($manager)
            ->get(route('hostel-manager.rooms.show', ['room' => $roomTwo->uuid]))
            ->assertStatus(403);
    }

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
}
