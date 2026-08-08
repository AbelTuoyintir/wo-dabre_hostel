<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Hostel;
use App\Models\Room;
use App\Models\User;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ReviewSecurityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that a student cannot associate a review with another user's booking (IDOR protection).
     */
    public function test_student_cannot_associate_review_with_others_booking(): void
    {
        $student = User::create([
            'name' => 'Student Reviewer',
            'email' => 'reviewer@example.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
            'gender' => 'male',
            'email_verified_at' => now(),
        ]);

        $otherStudent = User::create([
            'name' => 'Other Student',
            'email' => 'other@example.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
            'gender' => 'male',
            'email_verified_at' => now(),
        ]);

        $hostel = Hostel::create([
            'name' => 'Review Security Hostel',
            'location' => 'amamoma',
            'address' => '123 Safety Road',
            'email' => 'hostel@example.com'
        ]);

        $room = Room::create([
            'number' => '101',
            'capacity' => 2,
            'hostel_id' => $hostel->id,
            'gender' => 'any',
            'status' => 'available',
            'room_type' => 'single_room',
            'room_cost' => 300.00,
            'current_occupancy' => 0,
        ]);

        // Create completed booking for student so they are authorized to review the hostel
        Booking::create([
            'user_id' => $student->id,
            'hostel_id' => $hostel->id,
            'room_id' => $room->id,
            'check_in_date' => now()->subDays(5)->toDateString(),
            'check_out_date' => now()->subDays(1)->toDateString(),
            'total_amount' => 300.00,
            'booking_status' => 'checked_out',
            'payment_status' => 'paid',
            'booking_number' => 'BK-MINE',
        ]);

        // Create completed booking for other student
        $othersBooking = Booking::create([
            'user_id' => $otherStudent->id,
            'hostel_id' => $hostel->id,
            'room_id' => $room->id,
            'check_in_date' => now()->subDays(5)->toDateString(),
            'check_out_date' => now()->subDays(1)->toDateString(),
            'total_amount' => 300.00,
            'booking_status' => 'checked_out',
            'payment_status' => 'paid',
            'booking_number' => 'BK-OTHERS',
        ]);

        // Act as student, submit review for the hostel but specify other student's booking ID
        $response = $this->actingAs($student)->from(route('student.reviews.create'))->post(route('student.reviews.store'), [
            'hostel_id' => $hostel->id,
            'booking_id' => $othersBooking->id,
            'rating' => 5,
            'title' => 'Sneaky IDOR Review',
            'review' => 'This review attempts to link to another user\'s booking!',
        ]);

        $response->assertRedirect(route('student.reviews.create'));
        $response->assertSessionHas('error', 'Invalid booking association.');

        // Assert that review was not created with that booking ID
        $this->assertDatabaseMissing('reviews', [
            'booking_id' => $othersBooking->id,
        ]);
    }

    /**
     * Test that a student can successfully submit a review associated with their own completed booking.
     */
    public function test_student_can_review_with_own_completed_booking(): void
    {
        $student = User::create([
            'name' => 'Student Reviewer',
            'email' => 'reviewer@example.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
            'gender' => 'male',
            'email_verified_at' => now(),
        ]);

        $hostel = Hostel::create([
            'name' => 'Review Security Hostel',
            'location' => 'amamoma',
            'address' => '123 Safety Road',
            'email' => 'hostel@example.com'
        ]);

        $room = Room::create([
            'number' => '101',
            'capacity' => 2,
            'hostel_id' => $hostel->id,
            'gender' => 'any',
            'status' => 'available',
            'room_type' => 'single_room',
            'room_cost' => 300.00,
            'current_occupancy' => 0,
        ]);

        $myBooking = Booking::create([
            'user_id' => $student->id,
            'hostel_id' => $hostel->id,
            'room_id' => $room->id,
            'check_in_date' => now()->subDays(5)->toDateString(),
            'check_out_date' => now()->subDays(1)->toDateString(),
            'total_amount' => 300.00,
            'booking_status' => 'checked_out',
            'payment_status' => 'paid',
            'booking_number' => 'BK-MINE',
        ]);

        $response = $this->actingAs($student)->post(route('student.reviews.store'), [
            'hostel_id' => $hostel->id,
            'booking_id' => $myBooking->id,
            'rating' => 5,
            'title' => 'Legitimate Review',
            'review' => 'This is a valid review linking to my own completed booking.',
        ]);

        $response->assertRedirect(route('student.reviews'));
        $response->assertSessionHas('success');

        // Assert that review exists with student's booking ID
        $this->assertDatabaseHas('reviews', [
            'user_id' => $student->id,
            'hostel_id' => $hostel->id,
            'booking_id' => $myBooking->id,
            'rating' => 5,
            'title' => 'Legitimate Review',
        ]);
    }
}
