<?php
// app/Http\Controllers\ReviewController.php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::with(['user', 'hostel', 'booking'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('reviews.index', compact('reviews'));
    }

    public function create(Booking $booking)
    {
        // Check if user owns this booking
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Check if booking is completed
        if ($booking->booking_status !== 'checked_out') {
            return redirect()->route('student.bookings.index')
                ->with('error', 'You can only review completed bookings.');
        }

        // Check if review already exists
        if ($booking->review()->exists()) {
            return redirect()->route('student.reviews.index')
                ->with('error', 'You have already reviewed this booking.');
        }

        return view('reviews.create', compact('booking'));
    }

    public function store(Request $request, Booking $booking)
    {
        // Check if user owns this booking
        if ($booking->user_id !== Auth::id()) {
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

    public function destroy(Review $review)
    {
        // Check if user owns this review or is admin
        if ($review->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $review->delete();

        // Update hostel rating
        $review->hostel->updateRating();

        return back()->with('success', 'Review deleted successfully.');
    }
}
