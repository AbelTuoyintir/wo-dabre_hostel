<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Hostel;
use App\Models\Review;
use App\Http\Controllers\ReviewController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ReviewControllerSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_delete_another_users_review(): void
    {
        $owner = User::create([
            'name' => 'Review Owner',
            'email' => 'owner_'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'phone' => '08011122299',
            'role' => 'student',
            'gender' => 'male',
            'email_verified_at' => now(),
        ]);

        $attacker = User::create([
            'name' => 'Attacker Student',
            'email' => 'attacker_'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'phone' => '08011122288',
            'role' => 'student',
            'gender' => 'female',
            'email_verified_at' => now(),
        ]);

        $hostel = Hostel::create([
            'name' => 'Test Hostel',
            'location' => 'amamoma',
            'address' => '123 Main St',
            'email' => 'hostel@example.com',
            'is_approved' => true,
            'status' => 'active',
        ]);

        $review = Review::create([
            'user_id' => $owner->id,
            'hostel_id' => $hostel->id,
            'rating' => 4,
            'title' => 'Good Hostel',
            'review' => 'Great experience stay at this hostel.',
            'status' => 'published',
        ]);

        $controller = new ReviewController();

        $this->actingAs($attacker);

        try {
            $controller->destroy($review);
            $this->fail('Expected 403 HttpException was not thrown');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            $this->assertEquals(403, $e->getStatusCode());
        }

        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
        ]);
    }

    public function test_review_owner_can_delete_own_review(): void
    {
        $owner = User::create([
            'name' => 'Review Owner',
            'email' => 'owner_'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'phone' => '08011122277',
            'role' => 'student',
            'gender' => 'male',
            'email_verified_at' => now(),
        ]);

        $hostel = Hostel::create([
            'name' => 'Test Hostel 2',
            'location' => 'amamoma',
            'address' => '456 Second St',
            'email' => 'hostel2@example.com',
            'is_approved' => true,
            'status' => 'active',
        ]);

        $review = Review::create([
            'user_id' => $owner->id,
            'hostel_id' => $hostel->id,
            'rating' => 5,
            'title' => 'Awesome Stay',
            'review' => 'Really enjoyed staying at this hostel.',
            'status' => 'published',
        ]);

        $controller = new ReviewController();

        $this->actingAs($owner);

        $response = $controller->destroy($review);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertDatabaseMissing('reviews', [
            'id' => $review->id,
        ]);
    }
}
