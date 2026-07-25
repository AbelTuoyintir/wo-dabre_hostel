<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_student_can_authenticate_and_go_to_student_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => 'student',
            'is_active' => true
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('student.dashboard'));
    }

    public function test_hostel_manager_can_authenticate_and_go_to_manager_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => 'hostel_manager',
            'hostel_id' => 1,
            'is_active' => true
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('hostel-manager.dashboard'));
    }

    public function test_admin_can_authenticate_and_go_to_admin_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'is_active' => true
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create(['role' => 'student']);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_inactive_user_cannot_login(): void
    {
        $user = User::factory()->create([
            'role' => 'student',
            'is_active' => false
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors(['email' => 'Your account is deactivated.']);
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create(['role' => 'student']);

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/');
        $this->assertGuest();
    }
}