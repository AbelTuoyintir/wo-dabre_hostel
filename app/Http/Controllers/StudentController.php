<?php
// app/Http/Controllers/Student/StudentController.php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Review;
use App\Models\Hostel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:student']);
    }

    public function dashboard()
    {
        $user = Auth::user();

        $stats = [
            'total_bookings' => $user->bookings()->count(),
            'active_bookings' => $user->bookings()
                ->whereIn('booking_status', ['confirmed', 'checked_in'])
                ->count(),
            'pending_bookings' => $user->bookings()
                ->where('booking_status', 'pending')
                ->count(),
            'completed_bookings' => $user->bookings()
                ->where('booking_status', 'checked_out')
                ->count(),
        ];

        $recentBookings = $user->bookings()
            ->with('hostel')
            ->latest()
            ->take(5)
            ->get();

        $recommendedHostels = Hostel::where('is_approved', true)
            ->where('is_featured', true)
            ->with('primaryImage')
            ->limit(3)
            ->get();

        return view('student.dashboard', compact('stats', 'recentBookings', 'recommendedHostels'));
    }

    public function bookings()
    {
        $bookings = Auth::user()->bookings()
            ->with('hostel')
            ->latest()
            ->paginate(10);

        return view('student.bookings.index', compact('bookings'));
    }

    public function showBooking(Booking $booking)
    {
        // Check if user owns this booking
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('student.bookings.show', compact('booking'));
    }

    public function cancelBooking(Booking $booking)
    {
        // Check if user owns this booking
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Only allow cancellation if booking is still pending or confirmed
        if (!in_array($booking->booking_status, ['pending', 'confirmed'])) {
            return back()->with('error', 'Cannot cancel booking at this stage.');
        }

        $booking->update(['booking_status' => 'cancelled']);

        // Increment available rooms
        $booking->hostel->increment('available_rooms');

        return back()->with('success', 'Booking cancelled successfully.');
    }

    public function reviews()
    {
        $reviews = Auth::user()->reviews()
            ->with('hostel')
            ->latest()
            ->paginate(10);

        return view('student.reviews.index', compact('reviews'));
    }

    public function createReview(Booking $booking)
    {
        // Check if user owns this booking and can review
        if ($booking->user_id !== Auth::id() || $booking->booking_status !== 'checked_out') {
            abort(403, 'Unauthorized action.');
        }

        // Check if review already exists
        if ($booking->review()->exists()) {
            return back()->with('error', 'You have already reviewed this booking.');
        }

        return view('student.reviews.create', compact('booking'));
    }

    public function storeReview(Request $request, Booking $booking)
    {
        // Check if user owns this booking and can review
        if ($booking->user_id !== Auth::id() || $booking->booking_status !== 'checked_out') {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10|max:1000',
        ]);

        $review = Review::create([
            'user_id' => Auth::id(),
            'hostel_id' => $booking->hostel_id,
            'booking_id' => $booking->id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
            'status' => 'pending',
        ]);

        // Update hostel rating
        $booking->hostel->updateRating();

        return redirect()->route('student.reviews.index')
            ->with('success', 'Review submitted successfully. It will be visible after approval.');
    }
}
