<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Hostel;
use App\Models\Room;
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

    public function test_admin_cannot_demote_own_account_role_via_update_user(): void
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
                'role' => 'student',
                'is_active' => 1,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'You cannot demote or change the role of your own administrative account.');

        $this->assertEquals('admin', $admin->fresh()->role);
    }

    public function test_admin_booking_export_sanitizes_csv_formula_injection(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $student = User::factory()->create([
            'name' => '=CMD|\' /C calc\'!A0',
            'email' => '-student@example.com',
            'role' => 'student',
        ]);

        $hostel = Hostel::forceCreate([
            'name' => '@HostelFormula',
            'location' => 'amamoma',
            'address' => 'test address',
            'is_approved' => true,
        ]);

        $room = Room::create([
            'hostel_id' => $hostel->id,
            'number' => '+Room101',
            'room_type' => 'single_room',
            'capacity' => 2,
            'room_cost' => 500,
            'gender' => 'any',
            'status' => 'available',
        ]);

        Booking::create([
            'booking_number' => '=1+2',
            'user_id' => $student->id,
            'room_id' => $room->id,
            'hostel_id' => $hostel->id,
            'check_in_date' => now()->toDateString(),
            'check_out_date' => now()->addYear()->toDateString(),
            'booking_status' => 'confirmed',
            'payment_status' => 'paid',
            'total_amount' => 500,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.bookings.export'));

        $response->assertOk();
        $content = $response->streamedContent();

        $this->assertStringContainsString("'\x3DCMD|' /C calc'!A0", $content);
        $this->assertStringContainsString("'-student@example.com", $content);
        $this->assertStringContainsString("'\x40HostelFormula", $content);
        $this->assertStringContainsString("'+Room101", $content);
        $this->assertStringContainsString("'\x3D1+2", $content);
    }

    public function test_admin_rooms_export_sanitizes_csv_formula_injection(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $hostel = Hostel::forceCreate([
            'name' => '=HostelName',
            'location' => 'amamoma',
            'address' => 'test address',
            'is_approved' => true,
        ]);

        Room::create([
            'hostel_id' => $hostel->id,
            'number' => '+12345',
            'room_type' => 'single_room',
            'capacity' => 1,
            'room_cost' => 300,
            'gender' => 'any',
            'status' => 'available',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.rooms.export'));

        $response->assertOk();
        $content = $response->streamedContent();

        $this->assertStringContainsString("'\x3DHostelName", $content);
        $this->assertStringContainsString("'+12345", $content);
    }
}
