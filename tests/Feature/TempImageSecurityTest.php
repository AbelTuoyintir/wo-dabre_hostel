<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Hostel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TempImageSecurityTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Hostel $hostel;

    protected function setUp(): void
    {
        parent::setUp();

        // Create an admin user with valid gender
        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'phone' => '0501234567',
            'role' => 'admin',
            'gender' => 'male',
            'email_verified_at' => now(),
        ]);

        // Create a hostel
        $this->hostel = Hostel::create([
            'name' => 'Test Hostel',
            'location' => 'amamoma',
            'address' => '123 Main Street',
            'email' => 'hostel@example.com',
        ]);

        Storage::fake('public');
    }

    /**
     * Test deleteTempImage with a valid temp ID format and exact match.
     */
    public function test_delete_temp_image_with_valid_id(): void
    {
        $tempId = 'temp_651c6b29cd1a40.67207603';
        $filename = $tempId . '.jpg';
        $path = 'temp/room-images/' . $filename;

        // Create the mock file on fake storage
        Storage::disk('public')->put($path, 'fake content');
        $this->assertTrue(Storage::disk('public')->exists($path));

        // Call the delete route as Admin
        $response = $this->actingAs($this->admin)
            ->delete(route('admin.rooms.delete-temp', ['tempId' => $tempId]));

        $response->assertOk();
        $response->assertJson(['success' => true]);

        // Assert the file was deleted
        Storage::disk('public')->assertMissing($path);
    }

    /**
     * Test deleteTempImage with an invalid temp ID format.
     */
    public function test_delete_temp_image_with_invalid_id_format(): void
    {
        // Invalid characters should fail routing (404) or preg_match validation (400)
        $invalidTempIds = [
            'temp_../abc',
            'temp_..',
            'temp_\\abc',
            'temp_$',
            'temp_#',
        ];

        foreach ($invalidTempIds as $invalidId) {
            $response = $this->actingAs($this->admin)
                ->delete(route('admin.rooms.delete-temp', ['tempId' => $invalidId]));

            $this->assertTrue(
                in_array($response->getStatusCode(), [400, 404]),
                "Expected status 400 or 404, but got: " . $response->getStatusCode()
            );
        }
    }

    /**
     * Test deleteTempImage prevents substring match deletion.
     */
    public function test_delete_temp_image_prevents_substring_mismatch(): void
    {
        $tempId = 'temp_651c6b29cd1a40.67207603';
        $filename = $tempId . '.jpg';
        $path = 'temp/room-images/' . $filename;

        // Put a file
        Storage::disk('public')->put($path, 'fake content');

        // 1. Attempt to delete with an invalid format "temp_" (returns 400 Bad Request)
        $response1 = $this->actingAs($this->admin)
            ->delete(route('admin.rooms.delete-temp', ['tempId' => 'temp_']));
        $response1->assertStatus(400);

        // 2. Attempt to delete with a valid format but mismatching ID "temp_mismatch123.456" (returns 404 Not Found)
        $response2 = $this->actingAs($this->admin)
            ->delete(route('admin.rooms.delete-temp', ['tempId' => 'temp_mismatch123.456']));
        $response2->assertStatus(404);

        // Assert the original file still exists
        $this->assertTrue(Storage::disk('public')->exists($path));
    }

    /**
     * Test that store() ignores temp_cover_path traversal attempts.
     */
    public function test_store_room_ignores_cover_path_traversal(): void
    {
        // Attempt traversal to secret files outside temp directory
        $traversalPath = 'temp/room-images/../../../secret.txt';
        Storage::disk('public')->put('secret.txt', 'sensitive info');

        $payload = [
            'number' => 'R202',
            'capacity' => 4,
            'hostel_id' => $this->hostel->id,
            'gender' => 'any',
            'room_type' => 'shared_4',
            'status' => 'available',
            'room_cost' => 500,
            'temp_cover_path' => $traversalPath,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.rooms.store'), $payload);

        $response->assertRedirect();

        // The sensitive file must NOT be deleted or moved
        $this->assertTrue(Storage::disk('public')->exists('secret.txt'));
    }

    /**
     * Test that store() ignores temp_gallery_paths traversal attempts.
     */
    public function test_store_room_ignores_gallery_paths_traversal(): void
    {
        $traversalPath = 'temp/room-images/../../../sensitive.txt';
        Storage::disk('public')->put('sensitive.txt', 'secret data');

        $payload = [
            'number' => 'R203',
            'capacity' => 2,
            'hostel_id' => $this->hostel->id,
            'gender' => 'any',
            'room_type' => 'shared_2',
            'status' => 'available',
            'room_cost' => 600,
            'temp_gallery_paths' => [$traversalPath],
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.rooms.store'), $payload);

        $response->assertRedirect();

        // The sensitive file must NOT be copied or deleted
        $this->assertTrue(Storage::disk('public')->exists('sensitive.txt'));
    }
}
