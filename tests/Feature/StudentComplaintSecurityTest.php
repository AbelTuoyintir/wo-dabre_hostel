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
            'email' => 'att_'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        $studentTwo = User::create([
            'name' => 'Victim Student',
            'email' => 'vic_'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        $hostel = Hostel::create([
            'name' => 'Complaint Hostel',
            'location' => 'amamoma',
            'address' => '123 Campus Rd',
            'is_approved' => true,
            'status' => 'active',
        ]);

        $complaint = Complaint::create([
            'user_id' => $studentTwo->id,
            'hostel_id' => $hostel->id,
            'title' => 'Initial Water Issue',
            'category' => 'maintenance',
            'description' => 'There is no water coming from the tap in room 101.',
            'status' => 'pending',
            'priority' => 'medium',
        ]);

        // Student One attempts to update Student Two's complaint via patch
        $response = $this->actingAs($studentOne)->patch(route('student.complaints.update', ['complaint' => $complaint->uuid]), [
            'title' => 'Tampered Title',
            'category' => 'other',
            'description' => 'This description was updated by an unauthorized user.',
        ]);

        $response->assertStatus(403);

        $this->assertDatabaseHas('complaints', [
            'id' => $complaint->id,
            'title' => 'Initial Water Issue',
            'user_id' => $studentTwo->id,
        ]);
    }

    public function test_student_cannot_update_processed_or_resolved_complaint(): void
    {
        $student = User::create([
            'name' => 'Valid Student',
            'email' => 'std_'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        $hostel = Hostel::create([
            'name' => 'Complaint Hostel 2',
            'location' => 'amamoma',
            'address' => '456 Campus Rd',
            'is_approved' => true,
            'status' => 'active',
        ]);

        $resolvedComplaint = Complaint::create([
            'user_id' => $student->id,
            'hostel_id' => $hostel->id,
            'title' => 'Resolved Electricity Issue',
            'category' => 'maintenance',
            'description' => 'Power outage issue that was fixed by maintenance staff.',
            'status' => 'resolved',
            'priority' => 'high',
        ]);

        $response = $this->actingAs($student)->patch(route('student.complaints.update', ['complaint' => $resolvedComplaint->uuid]), [
            'title' => 'Attempting Reopen',
            'category' => 'maintenance',
            'description' => 'Trying to edit description after resolution.',
        ]);

        $response->assertRedirect(route('student.complaints'));
        $response->assertSessionHas('error', 'Cannot update a complaint that has been processed or closed.');

        $this->assertDatabaseHas('complaints', [
            'id' => $resolvedComplaint->id,
            'title' => 'Resolved Electricity Issue',
            'status' => 'resolved',
        ]);
    }

    public function test_student_can_update_own_pending_complaint(): void
    {
        $student = User::create([
            'name' => 'Valid Student 2',
            'email' => 'std2_'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        $hostel = Hostel::create([
            'name' => 'Complaint Hostel 3',
            'location' => 'amamoma',
            'address' => '789 Campus Rd',
            'is_approved' => true,
            'status' => 'active',
        ]);

        $complaint = Complaint::create([
            'user_id' => $student->id,
            'hostel_id' => $hostel->id,
            'title' => 'Broken Fan',
            'category' => 'maintenance',
            'description' => 'The ceiling fan in my room is making loud noise.',
            'status' => 'pending',
            'priority' => 'low',
        ]);

        $response = $this->actingAs($student)->patch(route('student.complaints.update', ['complaint' => $complaint->uuid]), [
            'title' => 'Broken Fan and Light Switch',
            'category' => 'maintenance',
            'priority' => 'high',
            'description' => 'The ceiling fan is making loud noise and the light switch is also broken.',
        ]);

        $response->assertRedirect(route('student.complaints'));
        $response->assertSessionHas('success', 'Complaint updated successfully.');

        $this->assertDatabaseHas('complaints', [
            'id' => $complaint->id,
            'user_id' => $student->id,
            'title' => 'Broken Fan and Light Switch',
            'priority' => 'high',
        ]);
    }
}
