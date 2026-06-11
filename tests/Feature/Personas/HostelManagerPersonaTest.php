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
}

