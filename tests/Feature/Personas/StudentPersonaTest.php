<?php

namespace Tests\Feature\Personas;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StudentPersonaTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_dashboard_requires_student_role(): void
    {
        $user = User::create([
            'name' => 'Not Student',
            'email' => 'ns'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'phone' => '08011122233',
            'role' => 'hostel_agent',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('student.dashboard'))
            ->assertRedirect();
    }

    public function test_student_dashboard_can_access_when_student_role(): void
    {
        $student = User::create([
            'name' => 'Student',
            'email' => 'st'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'phone' => '08011122233',
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($student)
            ->get(route('student.dashboard'))
            ->assertOk();
    }
}

