<?php

namespace Tests\Feature\Personas;

use App\Models\HostelAgent;
use App\Models\AgentWithdrawal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AgentPersonaTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Agent registration: valid input creates user + hostel_agent row.
     */
    public function test_agent_registration_creates_user_and_agent_profile(): void
    {
        $payload = [
            'name' => 'Test Agent',
            'email' => 'agent'.uniqid().'@example.com',
            'phone' => '080'.str_pad((string)random_int(0, 9999999), 7, '0', STR_PAD_LEFT),
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'id_card_number' => 'ID-123',
            'referral_code' => null,
        ];

        $this->post(route('agent.register'), $payload)
            ->assertRedirect();

        $this->assertDatabaseHas('users', [
            'email' => $payload['email'],
            'role' => 'hostel_agent',
        ]);

        $this->assertDatabaseHas('hostel_agents', [
            'phone' => $payload['phone'],
            'status' => 'pending',
        ]);
    }

    public function test_agent_registration_requires_valid_payload(): void
    {
        $payload = [
            'name' => '',
            'email' => 'not-an-email',
            'phone' => 'bad-phone',
            'password' => 'short',
            'password_confirmation' => 'mismatch',
        ];

        $this->post(route('agent.register'), $payload)
            ->assertStatus(302)
            ->assertSessionHasErrors(['name', 'email', 'phone', 'password']);
    }

    /**
     * AgentMiddleware allows agents without a profile to access complete-profile page.
     */
    public function test_agent_without_profile_can_access_complete_profile_page(): void
    {
        $user = User::create([
            'name' => 'No Agent Profile',
            'email' => 'u'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'phone' => '08011122233',
            'role' => 'hostel_agent',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('agent.complete-profile'))
            ->assertOk();

        $this->actingAs($user)
            ->get(route('agent.pending'))
            ->assertOk();
    }

    public function test_agent_without_profile_is_blocked_from_profile_update_route(): void
    {
        $user = User::create([
            'name' => 'No Agent Profile',
            'email' => 'u'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'phone' => '08011122233',
            'role' => 'hostel_agent',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user)
            ->put(route('agent.profile.update'), [
                'phone' => '08099900011',
                'address' => 'Somewhere',
            ])
            ->assertRedirect(route('agent.complete-profile'));
    }

    public function test_agent_settings_update_updates_user_and_agent_phone(): void
    {
        $user = User::create([
            'name' => 'Agent',
            'email' => 'agent'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'phone' => '08011122233',
            'role' => 'hostel_agent',
            'email_verified_at' => now(),
        ]);

        $agent = HostelAgent::create([
            'user_id' => $user->id,
            'agent_code' => 'AG-TEST'.uniqid(),
            'phone' => $user->phone,
            'total_commission' => 0,
            'available_balance' => 0,
            'withdrawn_amount' => 0,
            'total_hostels_added' => 0,
            'total_rooms_added' => 0,
            'status' => 'pending',
        ]);

        $this->actingAs($user)
            ->put(route('agent.settings.update'), [
                'name' => 'New Name',
                'phone' => '08077766655',
                'notification_email' => null,
            ])
            ->assertRedirect(route('agent.settings'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'New Name',
        ]);

        $this->assertDatabaseHas('hostel_agents', [
            'id' => $agent->id,
            'phone' => '08077766655',
        ]);
    }

    public function test_agent_password_update_requires_correct_current_password(): void
    {
        $user = User::create([
            'name' => 'Agent',
            'email' => 'agent'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'phone' => '08011122233',
            'role' => 'hostel_agent',
            'email_verified_at' => now(),
        ]);

        HostelAgent::create([
            'user_id' => $user->id,
            'agent_code' => 'AG-TEST'.uniqid(),
            'phone' => $user->phone,
            'total_commission' => 0,
            'available_balance' => 0,
            'withdrawn_amount' => 0,
            'total_hostels_added' => 0,
            'total_rooms_added' => 0,
            'status' => 'pending',
        ]);

        $this->actingAs($user)
            ->put(route('agent.settings.password'), [
                'current_password' => 'wrong-password',
                'password' => 'new-password123',
                'password_confirmation' => 'new-password123',
            ])
            ->assertRedirect(route('agent.settings'))
            ->assertSessionHas('error');

        $this->assertTrue(Hash::check('password123', $user->fresh()->password));
    }

    public function test_agent_password_update_successfully_changes_password(): void
    {
        $user = User::create([
            'name' => 'Agent',
            'email' => 'agent'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'phone' => '08011122233',
            'role' => 'hostel_agent',
            'email_verified_at' => now(),
        ]);

        HostelAgent::create([
            'user_id' => $user->id,
            'agent_code' => 'AG-TEST'.uniqid(),
            'phone' => $user->phone,
            'total_commission' => 0,
            'available_balance' => 0,
            'withdrawn_amount' => 0,
            'total_hostels_added' => 0,
            'total_rooms_added' => 0,
            'status' => 'pending',
        ]);

        $this->actingAs($user)
            ->put(route('agent.settings.password'), [
                'current_password' => 'password123',
                'password' => 'new-password123',
                'password_confirmation' => 'new-password123',
            ])
            ->assertRedirect(route('agent.settings'))
            ->assertSessionHas('success');

        $this->assertTrue(Hash::check('new-password123', $user->fresh()->password));
    }

    public function test_pending_agent_is_redirected_from_approved_only_routes(): void
    {
        $user = User::create([
            'name' => 'Agent',
            'email' => 'agent'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'phone' => '08011122233',
            'role' => 'hostel_agent',
            'email_verified_at' => now(),
        ]);

        HostelAgent::create([
            'user_id' => $user->id,
            'agent_code' => 'AG-TEST'.uniqid(),
            'phone' => $user->phone,
            'total_commission' => 0,
            'available_balance' => 0,
            'withdrawn_amount' => 0,
            'total_hostels_added' => 0,
            'total_rooms_added' => 0,
            'status' => 'pending',
        ]);

        $this->actingAs($user)->get(route('agent.dashboard'))
            ->assertRedirect(route('agent.pending'));

        $this->actingAs($user)->get(route('agent.commissions'))
            ->assertRedirect(route('agent.pending'));
    }

    public function test_suspended_agent_is_blocked_on_approved_only_routes(): void
    {
        $user = User::create([
            'name' => 'Agent',
            'email' => 'agent'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'phone' => '08011122233',
            'role' => 'hostel_agent',
            'email_verified_at' => now(),
        ]);

        HostelAgent::create([
            'user_id' => $user->id,
            'agent_code' => 'AG-TEST'.uniqid(),
            'phone' => $user->phone,
            'total_commission' => 0,
            'available_balance' => 0,
            'withdrawn_amount' => 0,
            'total_hostels_added' => 0,
            'total_rooms_added' => 0,
            'status' => 'suspended',
        ]);

        $this->actingAs($user)->get(route('agent.dashboard'))
            ->assertForbidden();
    }

    public function test_active_agent_can_access_dashboard_commissions_and_withdrawals(): void
    {
        $user = User::create([
            'name' => 'Agent',
            'email' => 'agent'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'phone' => '08011122233',
            'role' => 'hostel_agent',
            'email_verified_at' => now(),
        ]);

        $agent = HostelAgent::create([
            'user_id' => $user->id,
            'agent_code' => 'AG-TEST'.uniqid(),
            'phone' => $user->phone,
            'total_commission' => 100,
            'available_balance' => 500,
            'withdrawn_amount' => 0,
            'total_hostels_added' => 1,
            'total_rooms_added' => 2,
            'status' => 'active',
            'approved_at' => now(),
        ]);

        // Note: no commission/withdrawal models seeded here because the dashboard view
        // only needs related data; it won't fail if relations are empty.

        $this->actingAs($user)->get(route('agent.dashboard'))
            ->assertOk();

        $this->actingAs($user)->get(route('agent.commissions'))
            ->assertOk()
            ->assertViewHas('summary');

        $this->actingAs($user)->get(route('agent.withdrawals'))
            ->assertOk();
    }

    public function test_admin_can_approve_and_suspend_agents(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'phone' => '08000000000',
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $agentUser = User::create([
            'name' => 'Agent',
            'email' => 'agent'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'phone' => '08011122233',
            'role' => 'hostel_agent',
            'email_verified_at' => now(),
        ]);

        $agent = HostelAgent::create([
            'user_id' => $agentUser->id,
            'agent_code' => 'AG-TEST'.uniqid(),
            'phone' => $agentUser->phone,
            'total_commission' => 0,
            'available_balance' => 0,
            'withdrawn_amount' => 0,
            'total_hostels_added' => 0,
            'total_rooms_added' => 0,
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.agents.approve', ['id' => $agent->id]))
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('hostel_agents', [
            'id' => $agent->id,
            'status' => 'active',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.agents.suspend', ['id' => $agent->id]))
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('hostel_agents', [
            'id' => $agent->id,
            'status' => 'suspended',
        ]);
    }

    public function test_admin_process_withdrawal_reject_refunds_balance(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'phone' => '08000000000',
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $agentUser = User::create([
            'name' => 'Agent',
            'email' => 'agent'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'phone' => '08011122233',
            'role' => 'hostel_agent',
            'email_verified_at' => now(),
        ]);

        $agent = HostelAgent::create([
            'user_id' => $agentUser->id,
            'agent_code' => 'AG-TEST'.uniqid(),
            'phone' => $agentUser->phone,
            'total_commission' => 0,
            'available_balance' => 100,
            'withdrawn_amount' => 0,
            'total_hostels_added' => 0,
            'total_rooms_added' => 0,
            'status' => 'active',
            'approved_at' => now(),
        ]);

        $withdrawal = AgentWithdrawal::create([
            'agent_id' => $agent->id,
            'status' => 'pending',
            'amount' => 50.00,
            'payment_method' => 'mobile_money',
            'account_number' => '123',
            'account_name' => 'Agent',
            'bank_name' => null,
            'notes' => null,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.agents.process-withdrawal', ['id' => $withdrawal->id]), [
                'action' => 'reject',
                'notes' => 'Rejected by admin',
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('agent_withdrawals', [
            'id' => $withdrawal->id,
            'status' => 'rejected',
        ]);

        $this->assertEquals(150.00, (float) $agent->fresh()->available_balance);
    }

    public function test_admin_process_withdrawal_approve_sets_completed_fields(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'phone' => '08000000000',
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $agentUser = User::create([
            'name' => 'Agent',
            'email' => 'agent'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'phone' => '08011122233',
            'role' => 'hostel_agent',
            'email_verified_at' => now(),
        ]);

        $agent = HostelAgent::create([
            'user_id' => $agentUser->id,
            'agent_code' => 'AG-TEST'.uniqid(),
            'phone' => $agentUser->phone,
            'total_commission' => 0,
            'available_balance' => 100,
            'withdrawn_amount' => 0,
            'total_hostels_added' => 0,
            'total_rooms_added' => 0,
            'status' => 'active',
            'approved_at' => now(),
        ]);

        $withdrawal = AgentWithdrawal::create([
            'agent_id' => $agent->id,
            'status' => 'pending',
            'amount' => 50.00,
            'payment_method' => 'mobile_money',
            'account_number' => '123',
            'account_name' => 'Agent',
            'bank_name' => null,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.agents.process-withdrawal', ['id' => $withdrawal->id]), [
                'action' => 'approve',
                'notes' => 'Approved',
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('agent_withdrawals', [
            'id' => $withdrawal->id,
            'status' => 'completed',
        ]);

        $this->assertNotNull($withdrawal->fresh()->processed_at);
    }
}

