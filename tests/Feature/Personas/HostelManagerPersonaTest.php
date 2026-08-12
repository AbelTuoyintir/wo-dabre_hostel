<?php

namespace Tests\Feature\Personas;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

use App\Models\Hostel;
use App\Models\Room;
use App\Models\Booking;
use App\Models\Complaint;
use Carbon\Carbon;

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

    public function test_hostel_manager_can_view_own_occupants_and_is_denied_viewing_others(): void
    {
        $managerA = User::create([
            'name' => 'Manager A',
            'email' => 'mgr.a.'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'phone' => '08011122233',
            'role' => 'hostel_manager',
            'email_verified_at' => now(),
        ]);

        $managerB = User::create([
            'name' => 'Manager B',
            'email' => 'mgr.b.'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'phone' => '08011122234',
            'role' => 'hostel_manager',
            'email_verified_at' => now(),
        ]);

        $student = User::create([
            'name' => 'Student Occupant',
            'email' => 'student.'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'phone' => '08011122235',
            'role' => 'student',
            'gender' => 'male',
            'email_verified_at' => now(),
        ]);

        $hostelA = Hostel::create([
            'name' => 'Hostel A',
            'location' => 'amamoma',
            'address' => '123 A Ave',
            'email' => 'hostela.'.uniqid().'@example.com',
            'manager_id' => $managerA->id,
        ]);

        $hostelB = Hostel::create([
            'name' => 'Hostel B',
            'location' => 'amamoma',
            'address' => '123 B Ave',
            'email' => 'hostelb.'.uniqid().'@example.com',
            'manager_id' => $managerB->id,
        ]);

        $room = Room::create([
            'number' => '101',
            'capacity' => 2,
            'hostel_id' => $hostelA->id,
            'gender' => 'any',
            'status' => 'available',
            'room_type' => 'single_room',
            'room_cost' => 150.00,
            'current_occupancy' => 0,
        ]);

        // Student has a confirmed/pending booking in Hostel A
        Booking::create([
            'user_id' => $student->id,
            'hostel_id' => $hostelA->id,
            'room_id' => $room->id,
            'check_in_date' => Carbon::now()->addDays(1)->toDateString(),
            'check_out_date' => Carbon::now()->addDays(5)->toDateString(),
            'total_amount' => 150.00,
            'booking_status' => 'confirmed',
            'payment_status' => 'paid',
            'booking_number' => 'BKREF' . uniqid(),
        ]);

        // Manager A should be able to view this occupant (the student)
        $this->actingAs($managerA)
            ->get(route('hostel-manager.occupants.show', ['user' => $student->uuid]))
            ->assertOk()
            ->assertSee($student->name);

        // Manager B should be denied access because they do not manage Hostel A
        $this->actingAs($managerB)
            ->get(route('hostel-manager.occupants.show', ['user' => $student->uuid]))
            ->assertStatus(403);
    }

    public function test_hostel_manager_can_export_own_occupants_and_is_denied_others(): void
    {
        $managerA = User::create([
            'name' => 'Manager A',
            'email' => 'mgr.a.'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'phone' => '08011122233',
            'role' => 'hostel_manager',
            'email_verified_at' => now(),
        ]);

        $studentA = User::create([
            'name' => 'Student Occupant A',
            'email' => 'student.a.'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'phone' => '08011122235',
            'role' => 'student',
            'gender' => 'male',
            'email_verified_at' => now(),
        ]);

        $studentB = User::create([
            'name' => 'Student Occupant B',
            'email' => 'student.b.'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'phone' => '08011122236',
            'role' => 'student',
            'gender' => 'male',
            'email_verified_at' => now(),
        ]);

        $hostelA = Hostel::create([
            'name' => 'Hostel A',
            'location' => 'amamoma',
            'address' => '123 A Ave',
            'email' => 'hostela.'.uniqid().'@example.com',
            'manager_id' => $managerA->id,
        ]);

        $room = Room::create([
            'number' => '101',
            'capacity' => 2,
            'hostel_id' => $hostelA->id,
            'gender' => 'any',
            'status' => 'available',
            'room_type' => 'single_room',
            'room_cost' => 150.00,
            'current_occupancy' => 0,
        ]);

        // Student A has confirmed booking in Hostel A
        Booking::create([
            'user_id' => $studentA->id,
            'hostel_id' => $hostelA->id,
            'room_id' => $room->id,
            'check_in_date' => Carbon::now()->addDays(1)->toDateString(),
            'check_out_date' => Carbon::now()->addDays(5)->toDateString(),
            'total_amount' => 150.00,
            'booking_status' => 'confirmed',
            'payment_status' => 'paid',
            'booking_number' => 'BKREFA' . uniqid(),
        ]);

        // Student B is a student with NO booking in Hostel A
        // They should NOT appear in Manager A's export

        $response = $this->actingAs($managerA)
            ->get(route('hostel-manager.occupants.export', ['format' => 'csv']))
            ->assertOk();

        $content = $response->streamedContent();
        $this->assertStringContainsString('Student Occupant A', $content);
        $this->assertStringNotContainsString('Student Occupant B', $content);
    }

    public function test_hostel_manager_complaints_list_only_shows_complaints_for_their_hostels(): void
    {
        $managerA = User::create([
            'name' => 'Manager A',
            'email' => 'mgr.a.'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'phone' => '08011122233',
            'role' => 'hostel_manager',
            'email_verified_at' => now(),
        ]);

        $student = User::create([
            'name' => 'Student Occupant',
            'email' => 'student.'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'phone' => '08011122235',
            'role' => 'student',
            'gender' => 'male',
            'email_verified_at' => now(),
        ]);

        $hostelA = Hostel::create([
            'name' => 'Hostel A',
            'location' => 'amamoma',
            'address' => '123 A Ave',
            'email' => 'hostela.'.uniqid().'@example.com',
            'manager_id' => $managerA->id,
        ]);

        $hostelB = Hostel::create([
            'name' => 'Hostel B',
            'location' => 'amamoma',
            'address' => '123 B Ave',
            'email' => 'hostelb.'.uniqid().'@example.com',
            'manager_id' => null, // Managed by someone else / unassigned
        ]);

        Complaint::create([
            'user_id' => $student->id,
            'hostel_id' => $hostelA->id,
            'title' => 'Broken fan in A',
            'category' => 'maintenance',
            'description' => 'My fan in room 101 is completely broken. Please fix.',
            'priority' => 'high',
            'status' => 'pending',
        ]);

        Complaint::create([
            'user_id' => $student->id,
            'hostel_id' => $hostelB->id,
            'title' => 'Plumbing leak in B',
            'category' => 'maintenance',
            'description' => 'Water is leaking everywhere in B.',
            'priority' => 'high',
            'status' => 'pending',
        ]);

        // Manager A views complaints list
        $response = $this->actingAs($managerA)
            ->get(route('hostel-manager.complaints'))
            ->assertOk();

        $response->assertSee('Broken fan in A');
        $response->assertDontSee('Plumbing leak in B');
    }
}
