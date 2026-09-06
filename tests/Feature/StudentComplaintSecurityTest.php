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

    public function test_student_can_update_own_pending_complaint(): void
    {
        $student = User::create([
            'name' => 'Complaint Student',
            'email' => 'comp1_'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'phone' => '08011122231',
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        $hostel = Hostel::create([
            'name' => 'Complaint Test Hostel',
            'location' => 'amamoma',
            'address' => '123 Main St',
            'is_approved' => true,
            'status' => 'active',
        ]);

        $complaint = Complaint::create([
            'user_id' => $student->id,
            'hostel_id' => $hostel->id,
            'title' => 'Initial Title for Issue',
            'category' => 'maintenance',
            'description' => 'The air conditioning in room 101 is not cooling properly.',
            'priority' => 'medium',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($student)->patch(route('student.complaints.update', $complaint->uuid), [
            'subject' => 'Updated AC Issue Title',
            'description' => 'The air conditioning in room 101 is making loud noises and not cooling.',
            'priority' => 'high',
        ]);

        $response->assertRedirect(route('student.complaints'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('complaints', [
            'id' => $complaint->id,
            'title' => 'Updated AC Issue Title',
            'priority' => 'high',
        ]);
    }

    public function test_student_cannot_update_another_students_complaint_idor(): void
    {
        $studentOne = User::create([
            'name' => 'Attacker Student',
            'email' => 'att_'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'phone' => '08011122232',
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        $studentTwo = User::create([
            'name' => 'Victim Student',
            'email' => 'vic_'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'phone' => '08011122233',
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        $hostel = Hostel::create([
            'name' => 'Complaint Test Hostel 2',
            'location' => 'amamoma',
            'address' => '124 Main St',
            'is_approved' => true,
            'status' => 'active',
        ]);

        $otherComplaint = Complaint::create([
            'user_id' => $studentTwo->id,
            'hostel_id' => $hostel->id,
            'title' => 'Original Victim Complaint',
            'category' => 'maintenance',
            'description' => 'Plumbing leak in bathroom needs urgent repair.',
            'priority' => 'medium',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($studentOne)->patch(route('student.complaints.update', $otherComplaint->uuid), [
            'subject' => 'Hacked Complaint Title',
            'description' => 'Tampered description inserted by unauthorized student.',
        ]);

        $response->assertForbidden();

        $this->assertDatabaseHas('complaints', [
            'id' => $otherComplaint->id,
            'title' => 'Original Victim Complaint',
        ]);
    }

    public function test_student_cannot_update_resolved_or_rejected_complaint(): void
    {
        $student = User::create([
            'name' => 'Student Owner',
            'email' => 'res_'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'phone' => '08011122234',
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        $hostel = Hostel::create([
            'name' => 'Complaint Test Hostel 3',
            'location' => 'amamoma',
            'address' => '125 Main St',
            'is_approved' => true,
            'status' => 'active',
        ]);

        $resolvedComplaint = Complaint::create([
            'user_id' => $student->id,
            'hostel_id' => $hostel->id,
            'title' => 'Resolved Complaint Title',
            'category' => 'maintenance',
            'description' => 'Fan issue has been resolved by hostel staff.',
            'priority' => 'low',
            'status' => 'resolved',
            'resolved_at' => now(),
        ]);

        $response = $this->actingAs($student)->patch(route('student.complaints.update', $resolvedComplaint->uuid), [
            'subject' => 'Attempting to Reopen Resolved Issue',
            'description' => 'Reopening issue description that has already been resolved.',
        ]);

        $response->assertRedirect(route('student.complaints'));
        $response->assertSessionHas('error', 'Cannot update a complaint that has been resolved or closed.');

        $this->assertDatabaseHas('complaints', [
            'id' => $resolvedComplaint->id,
            'title' => 'Resolved Complaint Title',
        ]);
    }
}
