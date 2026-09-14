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

        $response = $this->actingAs($manager)
            ->get(route('hostel-manager.dashboard'));

        $response->assertOk();
        $response->assertViewIs('hostel-manager.dashboard');
    }

    public function test_hostel_manager_can_access_occupants_and_complaints_of_managed_hostel(): void
    {
        $manager = User::create([
            'name' => 'Assigned Manager',
            'email' => 'mgr_'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'phone' => '08011122299',
            'role' => 'hostel_manager',
            'gender' => 'male',
            'email_verified_at' => now(),
        ]);

        $hostel = Hostel::create([
            'name' => 'Managed Hostel Alpha',
            'location' => 'amamoma',
            'address' => '123 Managed St',
            'email' => 'managedhostel@example.com',
            'manager_id' => $manager->id,
        ]);

        $student = User::create([
            'name' => 'Resident Student',
            'email' => 'res_'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'phone' => '08011122288',
            'role' => 'student',
            'gender' => 'female',
            'email_verified_at' => now(),
        ]);

        $room = Room::create([
            'number' => '404',
            'capacity' => 2,
            'hostel_id' => $hostel->id,
            'gender' => 'any',
            'status' => 'available',
            'room_type' => 'single_room',
            'room_cost' => 200.00,
            'current_occupancy' => 1,
        ]);

        \App\Models\Booking::create([
            'user_id' => $student->id,
            'hostel_id' => $hostel->id,
            'room_id' => $room->id,
            'check_in_date' => now()->toDateString(),
            'check_out_date' => now()->addDays(30)->toDateString(),
            'total_amount' => 200.00,
            'booking_status' => 'confirmed',
            'payment_status' => 'paid',
            'booking_number' => 'BKREF' . uniqid(),
        ]);

        $this->actingAs($manager)
            ->get(route('hostel-manager.occupants'))
            ->assertOk();

        $this->actingAs($manager)
            ->get(route('hostel-manager.occupants.show', ['user' => $student->uuid]))
            ->assertOk();

        $this->actingAs($manager)
            ->get(route('hostel-manager.complaints'))
            ->assertOk();
    }

    public function test_export_occupants_prevents_cross_tenant_booking_data_leak(): void
    {
        $manager1 = User::create([
            'name' => 'Manager One',
            'email' => 'm1_'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'role' => 'hostel_manager',
            'email_verified_at' => now(),
        ]);

        $manager2 = User::create([
            'name' => 'Manager Two',
            'email' => 'm2_'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'role' => 'hostel_manager',
            'email_verified_at' => now(),
        ]);

        $hostelAlpha = Hostel::create([
            'name' => 'Alpha Hostel',
            'location' => 'amamoma',
            'address' => '123 Alpha St',
            'email' => 'alpha@example.com',
            'manager_id' => $manager1->id,
        ]);

        $hostelBeta = Hostel::create([
            'name' => 'Beta Hostel',
            'location' => 'amamoma',
            'address' => '456 Beta St',
            'email' => 'beta@example.com',
            'manager_id' => $manager2->id,
        ]);

        $roomAlpha = Room::create([
            'number' => '101',
            'capacity' => 2,
            'hostel_id' => $hostelAlpha->id,
            'gender' => 'any',
            'status' => 'available',
            'room_type' => 'single_room',
            'room_cost' => 200.00,
            'current_occupancy' => 1,
        ]);

        $roomBeta = Room::create([
            'number' => '999',
            'capacity' => 2,
            'hostel_id' => $hostelBeta->id,
            'gender' => 'any',
            'status' => 'available',
            'room_type' => 'single_room',
            'room_cost' => 250.00,
            'current_occupancy' => 1,
        ]);

        $student = User::create([
            'name' => 'Multi Hostel Student',
            'email' => 'multistudent_'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        // Student's earlier booking in Manager 2's hostel (Beta Hostel)
        \App\Models\Booking::create([
            'user_id' => $student->id,
            'hostel_id' => $hostelBeta->id,
            'room_id' => $roomBeta->id,
            'check_in_date' => now()->subYear()->toDateString(),
            'check_out_date' => now()->subYear()->addDays(30)->toDateString(),
            'total_amount' => 250.00,
            'booking_status' => 'confirmed',
            'payment_status' => 'paid',
            'booking_number' => 'BKREF_BETA_' . uniqid(),
            'created_at' => now()->subYear(),
        ]);

        // Student's active booking in Manager 1's hostel (Alpha Hostel)
        \App\Models\Booking::create([
            'user_id' => $student->id,
            'hostel_id' => $hostelAlpha->id,
            'room_id' => $roomAlpha->id,
            'check_in_date' => now()->toDateString(),
            'check_out_date' => now()->addDays(30)->toDateString(),
            'total_amount' => 200.00,
            'booking_status' => 'confirmed',
            'payment_status' => 'paid',
            'booking_number' => 'BKREF_ALPHA_' . uniqid(),
            'created_at' => now(),
        ]);

        // Manager 1 exports occupants
        $response = $this->actingAs($manager1)
            ->get(route('hostel-manager.occupants.export', ['format' => 'csv']));

        $response->assertOk();
        $content = $response->streamedContent();

        // Verify CSV output contains Manager 1's hostel and room details
        $this->assertStringContainsString('Alpha Hostel', $content);
        $this->assertStringContainsString('101', $content);

        // Verify CSV output DOES NOT leak Manager 2's hostel name or room number
        $this->assertStringNotContainsString('Beta Hostel', $content);
        $this->assertStringNotContainsString('999', $content);
    }
}
