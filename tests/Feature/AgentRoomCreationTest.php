<?php

namespace Tests\Feature;

use App\Models\Hostel;
use App\Models\HostelAgent;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AgentRoomCreationTest extends TestCase
{
    use RefreshDatabase;

    private User $agentUser;
    private HostelAgent $agent;
    private Hostel $hostel;

    protected function setUp(): void
    {
        parent::setUp();

        $this->agentUser = User::create([
            'name' => 'Test Agent',
            'email' => 'agent_create_room_'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'phone' => '080'.str_pad((string)random_int(0, 9999999), 7, '0', STR_PAD_LEFT),
            'role' => 'hostel_agent',
            'email_verified_at' => now(),
        ]);

        $this->agent = HostelAgent::create([
            'user_id' => $this->agentUser->id,
            'agent_code' => 'AG-TEST-'.uniqid(),
            'phone' => $this->agentUser->phone,
            'total_commission' => 0,
            'available_balance' => 0,
            'withdrawn_amount' => 0,
            'total_hostels_added' => 1,
            'total_rooms_added' => 0,
            'status' => 'active',
            'approved_at' => now(),
        ]);

        $this->hostel = Hostel::forceCreate([
            'name' => 'Agent Test Hostel',
            'description' => 'A test hostel for room creation.',
            'location' => 'amamoma',
            'address' => '123 College St',
            'user_id' => $this->agentUser->id,
            'status' => 'active',
        ]);
    }

    public function test_agent_can_access_room_creation_page(): void
    {
        $this->actingAs($this->agentUser)
            ->get(route('agent.rooms.create'))
            ->assertOk()
            ->assertViewIs('agent.rooms.create')
            ->assertViewHas('hostels');

        $this->actingAs($this->agentUser)
            ->get(route('agent.hostels.rooms.create', $this->hostel))
            ->assertOk()
            ->assertViewIs('agent.rooms.create')
            ->assertViewHas('selectedHostel');
    }

    public function test_agent_without_hostels_is_redirected_to_create_hostel(): void
    {
        $noHostelUser = User::create([
            'name' => 'No Hostel Agent',
            'email' => 'nohostel_'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'phone' => '080'.str_pad((string)random_int(0, 9999999), 7, '0', STR_PAD_LEFT),
            'role' => 'hostel_agent',
            'email_verified_at' => now(),
        ]);

        HostelAgent::create([
            'user_id' => $noHostelUser->id,
            'agent_code' => 'AG-NOHOSTEL-'.uniqid(),
            'phone' => $noHostelUser->phone,
            'status' => 'active',
            'approved_at' => now(),
        ]);

        $this->actingAs($noHostelUser)
            ->get(route('agent.rooms.create'))
            ->assertRedirect(route('agent.hostels.create'))
            ->assertSessionHas('info');
    }

    public function test_agent_can_store_room_successfully(): void
    {
        $payload = [
            'hostel_id' => $this->hostel->id,
            'room_number' => 'R-101',
            'room_type' => 'single_room',
            'capacity' => 2,
            'price_per_year' => 1500.00,
            'description' => 'Spacious single room with desk',
            'gender' => 'any',
            'is_available' => 1,
        ];

        $response = $this->actingAs($this->agentUser)
            ->post(route('agent.rooms.store'), $payload);

        $response->assertRedirect(route('agent.hostels.show', $this->hostel))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('rooms', [
            'hostel_id' => $this->hostel->id,
            'number' => 'R-101',
            'room_type' => 'single_room',
            'capacity' => 2,
            'status' => 'available',
        ]);

        $this->assertEquals(1, $this->agent->fresh()->total_rooms_added);
        $this->assertEquals(20.00, (float) $this->agent->fresh()->available_balance);
        $this->assertDatabaseHas('agent_commissions', [
            'hostel_agent_id' => $this->agent->id,
            'amount' => 20.00,
            'type' => 'room_added',
        ]);
    }

    public function test_agent_cannot_store_room_in_unowned_hostel_idor(): void
    {
        $otherUser = User::create([
            'name' => 'Other Agent',
            'email' => 'other_'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'phone' => '080'.str_pad((string)random_int(0, 9999999), 7, '0', STR_PAD_LEFT),
            'role' => 'hostel_agent',
            'email_verified_at' => now(),
        ]);

        HostelAgent::create([
            'user_id' => $otherUser->id,
            'agent_code' => 'AG-OTHER-'.uniqid(),
            'phone' => $otherUser->phone,
            'status' => 'active',
            'approved_at' => now(),
        ]);

        $payload = [
            'hostel_id' => $this->hostel->id,
            'room_number' => 'UNAUTHORIZED-1',
            'room_type' => 'single_room',
            'capacity' => 1,
            'price_per_year' => 1000.00,
        ];

        $this->actingAs($otherUser)
            ->post(route('agent.rooms.store'), $payload)
            ->assertForbidden();

        $this->assertDatabaseMissing('rooms', [
            'number' => 'UNAUTHORIZED-1',
        ]);
    }

    public function test_agent_cannot_add_duplicate_room_number_to_same_hostel(): void
    {
        Room::create([
            'hostel_id' => $this->hostel->id,
            'number' => 'EXISTING-100',
            'room_type' => 'shared_2',
            'capacity' => 2,
            'room_cost' => 1000.00,
            'status' => 'available',
            'gender' => 'any',
        ]);

        $payload = [
            'hostel_id' => $this->hostel->id,
            'room_number' => 'EXISTING-100',
            'room_type' => 'single_room',
            'capacity' => 1,
            'price_per_year' => 1200.00,
        ];

        $this->actingAs($this->agentUser)
            ->post(route('agent.rooms.store'), $payload)
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertEquals(1, Room::where('hostel_id', $this->hostel->id)->where('number', 'EXISTING-100')->count());
    }

    public function test_room_store_validates_required_fields(): void
    {
        $this->actingAs($this->agentUser)
            ->post(route('agent.rooms.store'), [])
            ->assertStatus(302)
            ->assertSessionHasErrors(['hostel_id', 'room_number', 'room_type', 'capacity', 'price_per_year']);
    }
}
