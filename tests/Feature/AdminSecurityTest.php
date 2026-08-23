<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_cannot_deactivate_own_account_via_toggle_status(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
            'gender' => 'male',
        ]);

        $response = $this->actingAs($admin)
            ->patch(route('admin.users.toggle-status', $admin));

        $response->assertRedirect();
        $response->assertSessionHas('error', 'You cannot deactivate your own administrative account.');

        $this->assertEquals(1, $admin->fresh()->is_active);
    }

    public function test_admin_cannot_deactivate_own_account_via_update_user(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
            'gender' => 'male',
        ]);

        $response = $this->actingAs($admin)
            ->put(route('admin.users.update', $admin), [
                'name' => $admin->name,
                'email' => $admin->email,
                'role' => 'admin',
                // is_active omitted/unchecked
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'You cannot deactivate your own administrative account.');

        $this->assertEquals(1, $admin->fresh()->is_active);
    }

    public function test_admin_can_toggle_other_user_status(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
            'gender' => 'male',
        ]);

        $otherUser = User::factory()->create([
            'role' => 'student',
            'is_active' => true,
            'gender' => 'female',
        ]);

        $response = $this->actingAs($admin)
            ->patch(route('admin.users.toggle-status', $otherUser));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals(0, $otherUser->fresh()->is_active);
    }
}
