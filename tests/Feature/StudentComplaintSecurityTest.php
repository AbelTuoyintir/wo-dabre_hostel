<?php

namespace Tests\Feature;

use App\Models\Complaint;
use App\Models\Hostel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StudentComplaintSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_cannot_update_another_users_complaint_idor(): void
    {
        $studentOne = User::create([
            'name' => 'Student One',
            'email' => 'student1_'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        $studentTwo = User::create([
            'name' => 'Student Two',
            'email' => 'student2_'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        $hostel = Hostel::forceCreate([
            'name' => 'Test Hostel',
            'description' => 'Test Hostel Description',
            'location' => 'amamoma',
            'address' => '123 Main St',
            'user_id' => $studentOne->id,
            'status' => 'active',
        ]);

        $complaint = Complaint::create([
            'user_id' => $studentTwo->id,
            'hostel_id' => $hostel->id,
            'title' => 'Original Title',
            'category' => 'maintenance',
            'description' => 'Original complaint description text with enough length.',
            'priority' => 'medium',
            'status' => 'pending',
        ]);

        // StudentOne attempts to update StudentTwo's complaint
        $response = $this->actingAs($studentOne)
            ->patch(route('student.complaints.update', $complaint->uuid), [
                'subject' => 'Hacked Title',
                'category' => 'other',
                'priority' => 'urgent',
                'description' => 'Attempting to overwrite another student\'s complaint details.',
            ]);

        $response->assertForbidden();

        $this->assertDatabaseHas('complaints', [
            'id' => $complaint->id,
            'title' => 'Original Title',
        ]);
    }

    public function test_student_cannot_update_resolved_or_rejected_complaint(): void
    {
        $student = User::create([
            'name' => 'Student User',
            'email' => 'student_'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        $hostel = Hostel::forceCreate([
            'name' => 'Test Hostel',
            'description' => 'Test Hostel Description',
            'location' => 'amamoma',
            'address' => '123 Main St',
            'user_id' => $student->id,
            'status' => 'active',
        ]);

        $resolvedComplaint = Complaint::create([
            'user_id' => $student->id,
            'hostel_id' => $hostel->id,
            'title' => 'Resolved Complaint',
            'category' => 'maintenance',
            'description' => 'Initial complaint description text with sufficient length.',
            'priority' => 'medium',
            'status' => 'resolved',
        ]);

        $response = $this->actingAs($student)
            ->patch(route('student.complaints.update', $resolvedComplaint->uuid), [
                'subject' => 'Attempted Edit on Resolved',
                'category' => 'other',
                'priority' => 'high',
                'description' => 'Trying to modify an already resolved complaint details.',
            ]);

        $response->assertRedirect(route('student.complaints'));
        $response->assertSessionHas('error', 'Cannot update complaint once resolved or processed.');

        $this->assertDatabaseHas('complaints', [
            'id' => $resolvedComplaint->id,
            'title' => 'Resolved Complaint',
        ]);
    }

    public function test_student_can_successfully_update_own_pending_complaint(): void
    {
        $student = User::create([
            'name' => 'Student User',
            'email' => 'student_'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        $hostel = Hostel::forceCreate([
            'name' => 'Test Hostel',
            'description' => 'Test Hostel Description',
            'location' => 'amamoma',
            'address' => '123 Main St',
            'user_id' => $student->id,
            'status' => 'active',
        ]);

        $complaint = Complaint::create([
            'user_id' => $student->id,
            'hostel_id' => $hostel->id,
            'title' => 'Initial Pending Subject',
            'category' => 'maintenance',
            'description' => 'Initial complaint description text with sufficient length.',
            'priority' => 'low',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($student)
            ->patch(route('student.complaints.update', $complaint->uuid), [
                'subject' => 'Updated Subject Header',
                'category' => 'payment',
                'priority' => 'high',
                'description' => 'Updated complaint description with detailed information that is long enough.',
            ]);

        $response->assertRedirect(route('student.complaints'));
        $response->assertSessionHas('success', 'Your complaint has been updated successfully.');

        $this->assertDatabaseHas('complaints', [
            'id' => $complaint->id,
            'title' => 'Updated Subject Header',
            'category' => 'payment',
            'priority' => 'high',
        ]);
    }
}
