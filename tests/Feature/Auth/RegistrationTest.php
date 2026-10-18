<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'gender' => 'male',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_user_cannot_register_as_admin_or_manager(): void
    {
        $response = $this->post('/register', [
            'name' => 'Attacker',
            'email' => 'attacker@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'gender' => 'male',
            'role' => 'admin',
        ]);

        $this->assertAuthenticated();
        $user = \App\Models\User::where('email', 'attacker@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('student', $user->role);
    }

    public function test_registration_is_rate_limited(): void
    {
        for ($i = 0; $i < 10; $i++) {
            $this->post('/register', [
                'name' => "User {$i}",
                'email' => "user{$i}@example.com",
                'password' => 'password',
                'password_confirmation' => 'password',
                'gender' => 'male',
            ]);
            \Illuminate\Support\Facades\Auth::logout();
        }

        $response = $this->post('/register', [
            'name' => 'Excess User',
            'email' => 'excess@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'gender' => 'male',
        ]);

        $response->assertStatus(429);
    }
}
