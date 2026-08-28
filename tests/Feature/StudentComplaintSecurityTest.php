<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Hostel;
use App\Models\Complaint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StudentComplaintSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_cannot_update_another_students_complaint(): void
    {
        $studentOne = User::create([
            'name' => 'Attacker Student',
            'email' => 'attacker_'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'phone' => '08011122241',
            'role' => 'student',
            'gender' => 'male',
            'email_verified_at' => now(),
        ]);

        $studentTwo = User::create([
            'name' => 'Victim Student',
            'email' => 'victim_'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'phone' => '08011122242',
            'role' => 'student',
            'gender' => 'female',
            'email_verified_at' => now(),
        ]);

        $hostel = Hostel::create([
            'name' => 'Security Test Hostel',
            'location' => 'amamoma',
            'address' => '123 Security St',
            'email' => 'hostel_'.uniqid().'@example.com',
            'is_approved' => true,
            'status' => 'active',
        ]);

        $complaint = Complaint::create([
            'user_id' => $studentTwo->id,
            'hostel_id' => $hostel->id,
            'title' => 'Water supply issue',
            'category' => 'maintenance',
            'description' => 'Original description for the complaint about water leak.',
            'priority' => 'medium',
            'status' => 'pending',
        ]);

        // Student One (Attacker) attempts to update Student Two's complaint via IDOR
        $response = $this->actingAs($studentOne)->patch(route('student.complaints.update', $complaint->uuid), [
            'description' => 'Malicious updated description by an attacker student.',
            'priority' => 'urgent',
        ]);

        $response->assertStatus(403);

        $this->assertDatabaseHas('complaints', [
            'id' => $complaint->id,
            'description' => 'Original description for the complaint about water leak.',
            'priority' => 'medium',
        ]);
    }

    public function test_student_can_update_own_complaint(): void
    {
        $student = User::create([
            'name' => 'Owner Student',
            'email' => 'owner_'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'phone' => '08011122243',
            'role' => 'student',
            'gender' => 'male',
            'email_verified_at' => now(),
        ]);

        $hostel = Hostel::create([
            'name' => 'Owner Hostel',
            'location' => 'amamoma',
            'address' => '456 Owner Rd',
            'email' => 'hostel_'.uniqid().'@example.com',
            'is_approved' => true,
            'status' => 'active',
        ]);

        $complaint = Complaint::create([
            'user_id' => $student->id,
            'hostel_id' => $hostel->id,
            'title' => 'Fan noise issue',
            'category' => 'maintenance',
            'description' => 'The fan makes a very loud noise when turned on high.',
            'priority' => 'low',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($student)->patch(route('student.complaints.update', $complaint->uuid), [
            'description' => 'Updated complaint description: The fan stopped working completely.',
            'priority' => 'high',
        ]);

        $response->assertRedirect(route('student.complaints'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('complaints', [
            'id' => $complaint->id,
            'description' => 'Updated complaint description: The fan stopped working completely.',
            'priority' => 'high',
        ]);
    }
}
