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

    public function test_student_cannot_update_another_students_complaint_idor()
    {
        $student1 = User::create([
            'name' => 'Student One',
            'email' => 's1_'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
        ]);

        $student2 = User::create([
            'name' => 'Student Two',
            'email' => 's2_'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
        ]);

        $hostel = Hostel::create([
            'name' => 'Test Hostel',
            'location' => 'amamoma',
            'address' => '123 Street',
            'is_approved' => true,
            'status' => 'active',
        ]);

        $complaint = Complaint::create([
            'user_id' => $student1->id,
            'hostel_id' => $hostel->id,
            'title' => 'Original Water Problem',
            'category' => 'maintenance',
            'description' => 'There is no running water in the bathroom since yesterday morning.',
            'priority' => 'high',
            'status' => 'pending',
        ]);

        // Student2 attempts to update Student1's complaint
        $response = $this->actingAs($student2)->patch(route('student.complaints.update', $complaint), [
            'subject' => 'Hacked Complaint Title',
            'category' => 'payment',
            'description' => 'Malicious update attempt by unauthorized student user.',
        ]);

        $response->assertStatus(403);

        $this->assertDatabaseHas('complaints', [
            'id' => $complaint->id,
            'title' => 'Original Water Problem',
        ]);
    }

    public function test_student_can_update_own_pending_complaint()
    {
        $student = User::create([
            'name' => 'Valid Student',
            'email' => 's_'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
        ]);

        $hostel = Hostel::create([
            'name' => 'Test Hostel 2',
            'location' => 'amamoma',
            'address' => '456 Street',
            'is_approved' => true,
            'status' => 'active',
        ]);

        $complaint = Complaint::create([
            'user_id' => $student->id,
            'hostel_id' => $hostel->id,
            'title' => 'Original Water Problem',
            'category' => 'maintenance',
            'description' => 'There is no running water in the bathroom since yesterday morning.',
            'priority' => 'medium',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($student)->patch(route('student.complaints.update', $complaint), [
            'subject' => 'Updated Water Problem Detail',
            'category' => 'maintenance',
            'priority' => 'high',
            'description' => 'Water pressure is extremely low and leaking onto floor.',
        ]);

        $response->assertRedirect(route('student.complaints'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('complaints', [
            'id' => $complaint->id,
            'title' => 'Updated Water Problem Detail',
            'priority' => 'high',
            'description' => 'Water pressure is extremely low and leaking onto floor.',
        ]);
    }

    public function test_student_cannot_update_resolved_complaint()
    {
        $student = User::create([
            'name' => 'Student Resolved',
            'email' => 'sr_'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
        ]);

        $hostel = Hostel::create([
            'name' => 'Test Hostel 3',
            'location' => 'amamoma',
            'address' => '789 Street',
            'is_approved' => true,
            'status' => 'active',
        ]);

        $complaint = Complaint::create([
            'user_id' => $student->id,
            'hostel_id' => $hostel->id,
            'title' => 'Resolved Issue',
            'category' => 'maintenance',
            'description' => 'Original issue description for resolved complaint.',
            'priority' => 'low',
            'status' => 'resolved',
        ]);

        $response = $this->actingAs($student)->patch(route('student.complaints.update', $complaint), [
            'subject' => 'Trying To Edit Resolved',
            'category' => 'maintenance',
            'description' => 'Attempting to alter a complaint that was already resolved.',
        ]);

        $response->assertRedirect(route('student.complaints'));
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('complaints', [
            'id' => $complaint->id,
            'title' => 'Resolved Issue',
        ]);
    }
}
