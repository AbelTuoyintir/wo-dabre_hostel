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

    /**
 * Student Dashboard
 */
public function dashboard()
{
    $user = Auth::user();

    // Get user's stats
    $stats = [
        'total_bookings' => Booking::where('user_id', $user->id)->count(),
        'active_bookings' => Booking::where('user_id', $user->id)
            ->where('status', 'confirmed')
            ->where('check_out', '>=', now())
            ->count(),
        'total_paid' => Payment::whereHas('booking', fn($q) => $q->where('user_id', $user->id))
            ->where('status', 'completed')
            ->sum('amount'),
        'complaints' => Complaint::where('user_id', $user->id)->count(),
        'pending_complaints' => Complaint::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'in_progress'])
            ->count(),
    ];

    // Get active booking if any
    $activeBooking = Booking::where('user_id', $user->id)
        ->where('status', 'confirmed')
        ->where('check_out', '>=', now())
        ->with(['room.hostel'])
        ->first();

    // Get recent bookings
    $recentBookings = Booking::where('user_id', $user->id)
        ->with(['room.hostel'])
        ->latest()
        ->limit(5)
        ->get();

    // Get recommended hostels (featured or highly rated)
    $recommendedHostels = Hostel::where('is_approved', true)
        ->where('status', 'active')
        ->whereHas('rooms', function($q) {
            $q->where('status', 'available')
              ->whereColumn('current_occupancy', '<', 'capacity');
        })
        ->with(['primaryImage'])
        ->orderByDesc('is_featured')
        ->orderByDesc('rating')
        ->limit(4)
        ->get()
        ->map(function($hostel) {
            $hostel->min_price = $hostel->rooms()
                ->where('status', 'available')
                ->whereColumn('current_occupancy', '<', 'capacity')
                ->min('price_per_month');
            return $hostel;
        });

    return view('student.dashboard', compact('stats', 'recentBookings', 'recommendedHostels', 'activeBooking'));
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

   public function cancelBooking(Request $request, Booking $booking)
{
    // Check if user owns this booking
    if ($booking->user_id !== Auth::id()) {
        abort(403, 'Unauthorized action.');
    }

    // Validate cancellation reason
    $request->validate([
        'cancellation_reason' => 'required|string|min:10|max:500',
    ]);

    // Determine which status column to use
    $currentStatus = $booking->booking_status ?? $booking->status ?? 'pending';

    // Only allow cancellation if booking is still pending or confirmed
    if (!in_array($currentStatus, ['pending', 'confirmed'])) {
        return back()->with('error', 'Cannot cancel booking at this stage.');
    }

    // Check if booking has a payment
    if (!$booking->payment || $booking->payment->status !== 'completed') {
        return back()->with('error', 'No payment found for this booking.');
    }

    DB::beginTransaction();

    try {
        // Calculate refund amount (full refund for pending, partial for confirmed)
        $refundAmount = $booking->total_amount;

        // Apply cancellation policy (optional)
        if ($currentStatus == 'confirmed') {
            // Deduct 10% cancellation fee for confirmed bookings
            $refundAmount = $booking->total_amount * 0.9;
        }

        // Process refund through Paystack
        $refund = Paystack::refund([
            'transaction' => $booking->payment->transaction_id,
            'amount' => round($refundAmount * 100), // Convert to pesewas
            'currency' => 'GHS',
            'reason' => $request->cancellation_reason
        ]);

        // Update booking status
        if ($booking->booking_status) {
            $booking->update([
                'booking_status' => 'cancelled',
                'cancellation_reason' => $request->cancellation_reason,
                'cancelled_at' => now(),
            ]);
        } else {
            $booking->update([
                'status' => 'cancelled',
                'cancellation_reason' => $request->cancellation_reason,
                'cancelled_at' => now(),
            ]);
        }

        // Update payment record
        $booking->payment->update([
            'status' => 'refunded',
            'refund_amount' => $refundAmount,
            'refund_reference' => $refund['data']['reference'] ?? null,
            'refunded_at' => now(),
        ]);

        // Increment available rooms in hostel
        if ($booking->hostel) {
            $booking->hostel->increment('available_rooms');
        }

        DB::commit();

        return back()->with('success',
            "Booking cancelled successfully. ₵" . number_format($refundAmount, 2) .
            " will be refunded to your original payment method within 3-5 business days."
        );

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Booking cancellation failed: ' . $e->getMessage());

        return back()->with('error', 'Failed to cancel booking. Please contact support.');
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
