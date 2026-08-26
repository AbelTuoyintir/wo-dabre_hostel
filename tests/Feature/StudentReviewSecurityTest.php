<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Booking;
use App\Models\Hostel;
use App\Models\Room;
use App\Models\Review;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StudentReviewSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_cannot_submit_review_with_another_users_booking_id(): void
    {
        $studentOne = User::create([
            'name' => 'Reviewer Student',
            'email' => 'rev1_'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'phone' => '08011122231',
            'role' => 'student',
            'gender' => 'male',
            'email_verified_at' => now(),
        ]);

        $studentTwo = User::create([
            'name' => 'Victim Student',
            'email' => 'vic2_'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'phone' => '08011122232',
            'role' => 'student',
            'gender' => 'female',
            'email_verified_at' => now(),
        ]);

        $hostel = Hostel::create([
            'name' => 'Review Security Hostel',
            'location' => 'amamoma',
            'address' => '123 Review Ave',
            'email' => 'revsec@example.com',
            'is_approved' => true,
            'status' => 'active',
        ]);

        $room = Room::create([
            'number' => '404',
            'capacity' => 2,
            'hostel_id' => $hostel->id,
            'gender' => 'any',
            'status' => 'available',
            'room_type' => 'single_room',
            'room_cost' => 300.00,
            'current_occupancy' => 0,
        ]);

        // Student One stayed at hostel (valid completed stay)
        Booking::create([
            'user_id' => $studentOne->id,
            'hostel_id' => $hostel->id,
            'room_id' => $room->id,
            'check_in_date' => Carbon::now()->subDays(10)->toDateString(),
            'check_out_date' => Carbon::now()->subDays(2)->toDateString(),
            'total_amount' => 300.00,
            'booking_status' => 'checked_out',
            'payment_status' => 'paid',
            'booking_number' => 'BKREF1' . uniqid(),
        ]);

        // Student Two's booking at same hostel
        $otherBooking = Booking::create([
            'user_id' => $studentTwo->id,
            'hostel_id' => $hostel->id,
            'room_id' => $room->id,
            'check_in_date' => Carbon::now()->subDays(10)->toDateString(),
            'check_out_date' => Carbon::now()->subDays(2)->toDateString(),
            'total_amount' => 300.00,
            'booking_status' => 'checked_out',
            'payment_status' => 'paid',
            'booking_number' => 'BKREF2' . uniqid(),
        ]);

        // Student One attempts to submit review with Student Two's booking_id
        $response = $this->actingAs($studentOne)->post(route('student.reviews.store'), [
            'hostel_id' => $hostel->id,
            'booking_id' => $otherBooking->id, // IDOR attempt!
            'rating' => 5,
            'title' => 'Great Stay Here',
            'review' => 'This is a long review text explaining why the stay was great and satisfying.',
        ]);

        $response->assertSessionHas('error', 'Invalid booking selected for review.');

        // Assert no review was created linking to Student Two's booking
        $this->assertDatabaseMissing('reviews', [
            'booking_id' => $otherBooking->id,
        ]);
    }

    public function test_student_can_submit_review_with_own_completed_booking(): void
    {
        $student = User::create([
            'name' => 'Valid Reviewer',
            'email' => 'val_'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'phone' => '08011122233',
            'role' => 'student',
            'gender' => 'female',
            'email_verified_at' => now(),
        ]);

        $hostel = Hostel::create([
            'name' => 'Valid Hostel',
            'location' => 'amamoma',
            'address' => '456 Valid Ave',
            'email' => 'valid@example.com',
            'is_approved' => true,
            'status' => 'active',
        ]);

        $room = Room::create([
            'number' => '505',
            'capacity' => 2,
            'hostel_id' => $hostel->id,
            'gender' => 'any',
            'status' => 'available',
            'room_type' => 'single_room',
            'room_cost' => 300.00,
            'current_occupancy' => 0,
        ]);

        $ownBooking = Booking::create([
            'user_id' => $student->id,
            'hostel_id' => $hostel->id,
            'room_id' => $room->id,
            'check_in_date' => Carbon::now()->subDays(10)->toDateString(),
            'check_out_date' => Carbon::now()->subDays(2)->toDateString(),
            'total_amount' => 300.00,
            'booking_status' => 'checked_out',
            'payment_status' => 'paid',
            'booking_number' => 'BKREF3' . uniqid(),
        ]);

        $response = $this->actingAs($student)->post(route('student.reviews.store'), [
            'hostel_id' => $hostel->id,
            'booking_id' => $ownBooking->id,
            'rating' => 4,
            'title' => 'Very Nice Hostel',
            'review' => 'This is a detailed review of the hostel with more than twenty characters.',
        ]);

        $response->assertRedirect(route('student.reviews'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('reviews', [
            'user_id' => $student->id,
            'hostel_id' => $hostel->id,
            'booking_id' => $ownBooking->id,
            'rating' => 4,
        ]);
    }
}
