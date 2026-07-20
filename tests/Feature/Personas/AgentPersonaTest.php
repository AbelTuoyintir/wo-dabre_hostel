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
            'hostel_agent_id' => $agent->id,
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

    /**
     * Agent can add a room to their registered hostel.
     */
    public function test_agent_can_add_room_to_their_hostel(): void
    {
        $user = User::create([
            'name' => 'Room Agent',
            'email' => 'agent_room'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'phone' => '080'.str_pad((string)random_int(0, 9999999), 7, '0', STR_PAD_LEFT),
            'role' => 'hostel_agent',
            'email_verified_at' => now(),
        ]);

        $agent = HostelAgent::create([
            'user_id' => $user->id,
            'agent_code' => 'AG-ROOM'.uniqid(),
            'phone' => $user->phone,
            'total_commission' => 0,
            'available_balance' => 0,
            'withdrawn_amount' => 0,
            'total_hostels_added' => 1,
            'total_rooms_added' => 0,
            'status' => 'active',
            'approved_at' => now(),
        ]);

        $hostel = \App\Models\Hostel::forceCreate([
            'name' => 'Agent Hostel One',
            'description' => 'A nice hostel.',
            'location' => 'amamoma',
            'address' => '123 Main St',
            'user_id' => $user->id,
            'status' => 'active',
        ]);

        $payload = [
            'room_number' => 'A101',
            'room_type' => 'single_room',
            'capacity' => 2,
            'price_per_year' => 1200.00,
            'description' => 'Comfortable single room',
            'is_available' => 1,
        ];

        $this->actingAs($user)
            ->post(route('agent.hostels.add-room', $hostel->uuid), $payload)
            ->assertRedirect(route('agent.hostels.show', $hostel->uuid))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('rooms', [
            'hostel_id' => $hostel->id,
            'number' => 'A101',
            'room_type' => 'single_room',
            'capacity' => 2,
            'room_cost' => 1200.00,
            'status' => 'available',
        ]);

        $this->assertEquals(1, $agent->fresh()->total_rooms_added);
        $this->assertEquals(20.00, (float) $agent->fresh()->available_balance);
        $this->assertDatabaseHas('agent_commissions', [
            'hostel_agent_id' => $agent->id,
            'amount' => 20.00,
            'type' => 'room_added',
        ]);
    }

    /**
     * Agent cannot add duplicate room number in same hostel.
     */
    public function test_agent_cannot_add_duplicate_room_number_in_same_hostel(): void
    {
        $user = User::create([
            'name' => 'Room Agent 2',
            'email' => 'agent_room_dup'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'phone' => '080'.str_pad((string)random_int(0, 9999999), 7, '0', STR_PAD_LEFT),
            'role' => 'hostel_agent',
            'email_verified_at' => now(),
        ]);

        $agent = HostelAgent::create([
            'user_id' => $user->id,
            'agent_code' => 'AG-ROOM-DUP'.uniqid(),
            'phone' => $user->phone,
            'total_commission' => 0,
            'available_balance' => 0,
            'withdrawn_amount' => 0,
            'total_hostels_added' => 1,
            'total_rooms_added' => 1,
            'status' => 'active',
            'approved_at' => now(),
        ]);

        $hostel = \App\Models\Hostel::forceCreate([
            'name' => 'Agent Hostel Two',
            'description' => 'Another nice hostel.',
            'location' => 'amamoma',
            'address' => '124 Main St',
            'user_id' => $user->id,
            'status' => 'active',
        ]);

        \App\Models\Room::create([
            'hostel_id' => $hostel->id,
            'number' => 'B202',
            'room_type' => 'shared_2',
            'capacity' => 2,
            'room_cost' => 800.00,
            'status' => 'available',
            'gender' => 'any',
        ]);

        $payload = [
            'room_number' => 'B202',
            'room_type' => 'single_room',
            'capacity' => 1,
            'price_per_year' => 1000.00,
            'description' => 'Dup room attempt',
            'is_available' => 1,
        ];

        $this->actingAs($user)
            ->post(route('agent.hostels.add-room', $hostel->uuid), $payload)
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertEquals(1, \App\Models\Room::where('hostel_id', $hostel->id)->where('number', 'B202')->count());
        $this->assertEquals(0.00, (float) $agent->fresh()->available_balance);
    }

    /**
     * Agent can delete a room from their registered hostel.
     */
    public function test_agent_can_delete_room(): void
    {
        $user = User::create([
            'name' => 'Delete Agent',
            'email' => 'agent_del'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'phone' => '080'.str_pad((string)random_int(0, 9999999), 7, '0', STR_PAD_LEFT),
            'role' => 'hostel_agent',
            'email_verified_at' => now(),
        ]);

        $agent = HostelAgent::create([
            'user_id' => $user->id,
            'agent_code' => 'AG-DEL'.uniqid(),
            'phone' => $user->phone,
            'total_commission' => 0,
            'available_balance' => 0,
            'withdrawn_amount' => 0,
            'total_hostels_added' => 1,
            'total_rooms_added' => 1,
            'status' => 'active',
            'approved_at' => now(),
        ]);

        $hostel = \App\Models\Hostel::forceCreate([
            'name' => 'Agent Hostel Three',
            'description' => 'A hostel with rooms to delete.',
            'location' => 'amamoma',
            'address' => '125 Main St',
            'user_id' => $user->id,
            'status' => 'active',
        ]);

        $room = \App\Models\Room::create([
            'hostel_id' => $hostel->id,
            'number' => 'C303',
            'room_type' => 'shared_2',
            'capacity' => 2,
            'room_cost' => 800.00,
            'status' => 'available',
            'gender' => 'any',
        ]);

        $this->actingAs($user)
            ->delete(route('agent.hostels.delete-room', [$hostel->uuid, $room->uuid]))
            ->assertRedirect(route('agent.hostels.show', $hostel->uuid))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('rooms', [
            'id' => $room->id,
        ]);

        $this->assertEquals(0, $agent->fresh()->total_rooms_added);
    }

    /**
     * Agent cannot delete a room with active bookings.
     */
    public function test_agent_cannot_delete_room_with_active_bookings(): void
    {
        $user = User::create([
            'name' => 'Delete Active Agent',
            'email' => 'agent_del_act'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'phone' => '080'.str_pad((string)random_int(0, 9999999), 7, '0', STR_PAD_LEFT),
            'role' => 'hostel_agent',
            'email_verified_at' => now(),
        ]);

        $agent = HostelAgent::create([
            'user_id' => $user->id,
            'agent_code' => 'AG-DEL-ACT'.uniqid(),
            'phone' => $user->phone,
            'total_commission' => 0,
            'available_balance' => 0,
            'withdrawn_amount' => 0,
            'total_hostels_added' => 1,
            'total_rooms_added' => 1,
            'status' => 'active',
            'approved_at' => now(),
        ]);

        $hostel = \App\Models\Hostel::forceCreate([
            'name' => 'Agent Hostel Four',
            'description' => 'A hostel with active booked rooms.',
            'location' => 'amamoma',
            'address' => '126 Main St',
            'user_id' => $user->id,
            'status' => 'active',
        ]);

        $room = \App\Models\Room::create([
            'hostel_id' => $hostel->id,
            'number' => 'D404',
            'room_type' => 'shared_2',
            'capacity' => 2,
            'room_cost' => 800.00,
            'status' => 'available',
            'gender' => 'any',
        ]);

        // Create a student user for the booking
        $student = User::create([
            'name' => 'Student Tester',
            'email' => 'student'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'phone' => '080'.str_pad((string)random_int(0, 9999999), 7, '0', STR_PAD_LEFT),
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        // Create active/confirmed booking
        \App\Models\Booking::create([
            'booking_number' => 'BK-' . uniqid(),
            'user_id' => $student->id,
            'room_id' => $room->id,
            'hostel_id' => $hostel->id,
            'check_in_date' => now()->toDateString(),
            'check_out_date' => now()->addYear()->toDateString(),
            'booking_status' => 'confirmed',
            'payment_status' => 'paid',
            'total_price' => 800.00,
            'total_amount' => 800.00,
        ]);

        $this->actingAs($user)
            ->delete(route('agent.hostels.delete-room', [$hostel->uuid, $room->uuid]))
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseHas('rooms', [
            'id' => $room->id,
        ]);

        $this->assertEquals(1, $agent->fresh()->total_rooms_added);
    }
}

