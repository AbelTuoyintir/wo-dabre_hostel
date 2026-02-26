<?php
// app/Http/Controllers/Student/StudentController.php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Review;
use App\Models\Hostel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Payment;
use App\Models\Complaint;
use Unicodeveloper\Paystack\Facades\Paystack;

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

    /**
     * Show fee payment form
     */
    public function showPaymentForm()
    {
        $user = Auth::user();
        $feeAmount = config('app.student_fee_amount', 50000); // Default fee amount in kobo (500 NGN)
        
        return view('student.payments.fee-payment', compact('user', 'feeAmount'));
    }

    /**
     * Initialize fee payment through Paystack
     */
    public function initializeFeePayment(Request $request)
    {
        $user = Auth::user();
        $feeAmount = config('app.student_fee_amount', 50000); // Amount in kobo
        
        try {
            // Create a payment record for tracking
            $reference = 'FEE-' . $user->id . '-' . time();
            
            $paymentData = [
                'amount' => $feeAmount,
                'email' => $user->email,
                'orderID' => $reference,
                'currency' => 'NGN',
            ];

            // Initialize Paystack payment
            return Paystack::getAuthorizationUrl($paymentData)->redirectNow();
        } catch (\Exception $e) {
            return redirect()->route('student.payment')
                ->with('error', 'Unable to initialize payment. Please try again.');
        }
    }

    /**
     * Handle fee payment callback from Paystack
     */
    public function handlePaymentCallback()
    {
        $paymentDetails = Paystack::getPaymentData();
        $user = Auth::user();

        if ($paymentDetails['status'] == true) {
            // Payment successful
            $user->update(['has_paid_fees' => true]);
            
            return redirect()->route('student.dashboard')
                ->with('success', 'Payment successful! Fee payment completed.');
        } else {
            return redirect()->route('student.payment')
                ->with('error', 'Payment failed or was cancelled.');
        }
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

        // Get bookings that can be reviewed (completed stays that haven't been reviewed yet)
        $reviewableBookings = Booking::where('user_id', Auth::id())
            ->where('status', 'completed')
            ->whereDoesntHave('review')
            ->with('room.hostel')
            ->latest()
            ->get();

        return view('student.reviews.index', compact('reviews', 'reviewableBookings'));
    }

    /**
     * Show form to create a review
     */
    public function createReview(Request $request)
    {
        $bookingId = $request->get('booking_id');
        $hostelId = $request->get('hostel_id');

        if ($bookingId) {
            $booking = Booking::where('user_id', Auth::id())
                ->where('id', $bookingId)
                ->where('status', 'completed')
                ->with('room.hostel')
                ->firstOrFail();

            return view('student.reviews.create', [
                'hostel' => $booking->room->hostel,
                'booking' => $booking
            ]);
        }

        if ($hostelId) {
            $hostel = Hostel::where('is_approved', true)
                ->where('status', 'active')
                ->findOrFail($hostelId);

            // Check if user has completed a booking at this hostel
            $hasCompletedBooking = Booking::where('user_id', Auth::id())
                ->where('hostel_id', $hostelId)
                ->where('status', 'completed')
                ->exists();

            if (!$hasCompletedBooking) {
                return redirect()->route('student.reviews')
                    ->with('error', 'You can only review hostels you have stayed at.');
            }

            // Check if already reviewed
            $alreadyReviewed = Review::where('user_id', Auth::id())
                ->where('hostel_id', $hostelId)
                ->exists();

            if ($alreadyReviewed) {
                return redirect()->route('student.reviews')
                    ->with('error', 'You have already reviewed this hostel.');
            }

            return view('student.reviews.create', [
                'hostel' => $hostel,
                'booking' => null
            ]);
        }

        return redirect()->route('student.reviews')
            ->with('error', 'Please select a hostel to review.');
    }

    /**
     * Store a new review
     */
    public function storeReview(Request $request)
    {
        $validated = $request->validate([
            'hostel_id' => 'required|exists:hostels,id',
            'booking_id' => 'nullable|exists:bookings,id',
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'required|string|max:255',
            'review' => 'required|string|min:20|max:2000',
            'pros' => 'nullable|string|max:1000',
            'cons' => 'nullable|string|max:1000',
            'stay_duration' => 'nullable|string|max:100',
        ]);

        // Check if user has already reviewed this hostel
        $existingReview = Review::where('user_id', Auth::id())
            ->where('hostel_id', $validated['hostel_id'])
            ->first();

        if ($existingReview) {
            return back()->with('error', 'You have already reviewed this hostel.');
        }

        // Verify user has completed a booking at this hostel
        $hasCompletedBooking = Booking::where('user_id', Auth::id())
            ->where('hostel_id', $validated['hostel_id'])
            ->where('status', 'completed')
            ->exists();

        if (!$hasCompletedBooking) {
            return back()->with('error', 'You can only review hostels you have stayed at.');
        }

        DB::transaction(function () use ($validated) {
            // Create the review
            $review = Review::create([
                'user_id' => Auth::id(),
                'hostel_id' => $validated['hostel_id'],
                'booking_id' => $validated['booking_id'] ?? null,
                'rating' => $validated['rating'],
                'title' => $validated['title'],
                'review' => $validated['review'],
                'pros' => $validated['pros'] ?? null,
                'cons' => $validated['cons'] ?? null,
                'stay_duration' => $validated['stay_duration'] ?? null,
                'is_verified' => true, // Verified since they had a completed booking
                'status' => 'published',
            ]);

            // Update hostel's average rating
            $this->updateHostelRating($validated['hostel_id']);
        });

        return redirect()->route('student.reviews')
            ->with('success', 'Thank you for your review! It has been published.');
    }

    /**
     * Show form to edit a review
     */
    public function editReview(Review $review)
    {
        if ($review->user_id !== Auth::id()) {
            abort(403);
        }

        // Only allow editing within 30 days
        if ($review->created_at->diffInDays(now()) > 30) {
            return redirect()->route('student.reviews')
                ->with('error', 'Reviews can only be edited within 30 days of posting.');
        }

        $review->load('hostel');

        return view('student.reviews.edit', compact('review'));
    }

    /**
     * Update a review
     */
    public function updateReview(Request $request, Review $review)
    {
        if ($review->user_id !== Auth::id()) {
            abort(403);
        }

        // Only allow editing within 30 days
        if ($review->created_at->diffInDays(now()) > 30) {
            return redirect()->route('student.reviews')
                ->with('error', 'Reviews can only be edited within 30 days of posting.');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'required|string|max:255',
            'review' => 'required|string|min:20|max:2000',
            'pros' => 'nullable|string|max:1000',
            'cons' => 'nullable|string|max:1000',
            'stay_duration' => 'nullable|string|max:100',
        ]);

        DB::transaction(function () use ($review, $validated) {
            $review->update([
                'rating' => $validated['rating'],
                'title' => $validated['title'],
                'review' => $validated['review'],
                'pros' => $validated['pros'] ?? null,
                'cons' => $validated['cons'] ?? null,
                'stay_duration' => $validated['stay_duration'] ?? null,
            ]);

            // Update hostel's average rating
            $this->updateHostelRating($review->hostel_id);
        });

        return redirect()->route('student.reviews')
            ->with('success', 'Your review has been updated.');
    }

    /**
     * Delete a review
     */
    public function destroyReview(Review $review)
    {
        if ($review->user_id !== Auth::id()) {
            abort(403);
        }

        // Only allow deletion within 30 days
        if ($review->created_at->diffInDays(now()) > 30) {
            return redirect()->route('student.reviews')
                ->with('error', 'Reviews can only be deleted within 30 days of posting.');
        }

        DB::transaction(function () use ($review) {
            $hostelId = $review->hostel_id;
            $review->delete();

            // Update hostel's average rating
            $this->updateHostelRating($hostelId);
        });

        return redirect()->route('student.reviews')
            ->with('success', 'Your review has been deleted.');
    }

    /**
     * Update hostel's average rating
     */
    private function updateHostelRating($hostelId)
    {
        $hostel = Hostel::find($hostelId);
        
        if ($hostel) {
            $averageRating = Review::where('hostel_id', $hostelId)
                ->where('status', 'published')
                ->avg('rating');

            $reviewsCount = Review::where('hostel_id', $hostelId)
                ->where('status', 'published')
                ->count();

            $hostel->update([
                'rating' => round($averageRating, 1),
                'reviews_count' => $reviewsCount,
            ]);
        }
    }
}
}
