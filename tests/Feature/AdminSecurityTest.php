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

    public function test_admin_cannot_delete_own_account_via_profile_destroy(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
            'password' => bcrypt('password'),
            'gender' => 'male',
        ]);

        $response = $this->actingAs($admin)
            ->delete(route('profile.destroy'), [
                'password' => 'password',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'You cannot delete an administrative account directly.');

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
        ]);
    }

    public function test_admin_can_access_reports(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
            'gender' => 'male',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.reports.index'));

        $response->assertOk();
        $response->assertViewIs('admin.report');
        $response->assertViewHasAll(['revenueByMonth', 'bookingsByHostel', 'userRegistrations']);
    }

    public function test_non_admin_cannot_access_reports(): void
    {
        $student = User::factory()->create([
            'role' => 'student',
            'is_active' => true,
            'gender' => 'female',
        ]);

        $response = $this->actingAs($student)
            ->get(route('admin.reports.index'));

        $response->assertRedirect(route('student.dashboard'));
        $response->assertSessionHas('error', 'Admin access only. You are a Student.');
    }
}
