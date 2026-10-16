<?php

namespace Tests\Feature;

use App\Models\Hostel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class HostelCompareSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_compare_endpoint_does_not_disclose_unapproved_or_inactive_hostels(): void
    {
        $activeApproved = Hostel::create([
            'uuid' => (string) Str::uuid(),
            'name' => 'Active Approved Hostel',
            'location' => 'amamoma',
            'is_approved' => true,
            'status' => 'active',
        ]);
        $activeApproved->status = 'active';
        $activeApproved->save();

        $unapprovedHostel = Hostel::create([
            'uuid' => (string) Str::uuid(),
            'name' => 'Unapproved Private Hostel',
            'location' => 'kwaprow',
            'is_approved' => false,
            'status' => 'active',
        ]);
        $unapprovedHostel->status = 'active';
        $unapprovedHostel->save();

        $inactiveHostel = Hostel::create([
            'uuid' => (string) Str::uuid(),
            'name' => 'Inactive Private Hostel',
            'location' => 'ayensu',
            'is_approved' => true,
            'status' => 'inactive',
        ]);
        $inactiveHostel->status = 'inactive';
        $inactiveHostel->save();

        // Request comparison passing IDs/UUIDs of active, unapproved, and inactive hostels
        $response = $this->get(route('hostels.compare', [
            'ids' => "{$activeApproved->uuid},{$unapprovedHostel->uuid},{$inactiveHostel->uuid}",
        ]));

        $response->assertStatus(200);
        $response->assertSee('Active Approved Hostel');
        $response->assertDontSee('Unapproved Private Hostel');
        $response->assertDontSee('Inactive Private Hostel');
    }

    public function test_compare_endpoint_redirects_if_only_unapproved_hostels_queried(): void
    {
        $unapprovedHostel = Hostel::create([
            'uuid' => (string) Str::uuid(),
            'name' => 'Secret Unapproved Hostel',
            'location' => 'amamoma',
            'is_approved' => false,
            'status' => 'active',
        ]);
        $unapprovedHostel->status = 'active';
        $unapprovedHostel->save();

        $response = $this->get(route('hostels.compare', [
            'ids' => $unapprovedHostel->uuid,
        ]));

        $response->assertRedirect(route('hostels.index'));
        $response->assertSessionHas('error', 'Selected hostels could not be found.');
    }
}
