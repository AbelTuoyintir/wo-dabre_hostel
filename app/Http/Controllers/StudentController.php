<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Review;
use App\Models\Hostel;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Payment;
use App\Models\Complaint;
use Unicodeveloper\Paystack\Facades\Paystack;
use App\Mail\PaymentReceiptMail;
use Illuminate\Support\Facades\Mail;


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
                ->where('check_out_date', '>=', now())
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
            ->where('check_out_date', '>=', now())
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
        // Fee amount in Ghana Cedis (GHS)
        $feeAmountInGHS = config('app.student_fee_amount', 500); // Default ₵500

        return view('student.payments.fee-payment', compact('user', 'feeAmountInGHS'));
    }

    /**
     * Initialize fee payment through Paystack (GHS)
     */
    public function initializeFeePayment(Request $request)
    {
        $user = Auth::user();

        // Set the fee amount in Ghana Cedis (GHS)
        $feeAmountInGHS = config('app.student_fee_amount', 500);

        try {
            // Generate a unique reference
            $reference = 'FEE-' . $user->id . '-' . time() . '-' . Str::random(4);

            $paymentData = [
                'user_id' => $user->id,
                'email' => $user->email,
                'amount' => $feeAmountInGHS * 100, // Convert to pesewas
                'currency' => 'GHS',
                'reference' => $reference,
                'callback_url' => route('student.payment.callback'),
                'metadata' => [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'user_email' => $user->email,
                    'payment_type' => 'fee',
                    'payment_for' => 'Student Accommodation Fee',
                    'fee_amount' => $feeAmountInGHS,
                ],
            ];

            // Initialize Paystack payment
            return Paystack::getAuthorizationUrl($paymentData)->redirectNow();

        } catch (\Exception $e) {
            \Log::error('Fee payment initialization failed: ' . $e->getMessage());
            return redirect()->route('student.payment')
                ->with('error', 'Unable to initialize payment. Please try again.');
        }
    }


    /**
     * Handle fee payment callback from Paystack
     */
    public function handlePaymentCallback(Request $request)
    {
        \Log::debug('Paystack callback', $request->all());

        try {
            $paymentDetails = Paystack::getPaymentData();
            \Log::debug('Paystack response', $paymentDetails);

            if ($paymentDetails['status'] && $paymentDetails['data']['status'] === 'success') {

                // FIX: Handle metadata safely - it could be array or string
                $metadata = $paymentDetails['data']['metadata'];

                // If it's a string, decode it; if it's already an array, use it directly
                if (is_string($metadata)) {
                    $metadata = json_decode($metadata, true);
                }

                $user = Auth::user();

                // Store payment record (amount already in GHS from Paystack)
                $payment = Payment::create([
                    'user_id' => $user->id,
                    'booking_id' => null, // This is a fee payment, not booking payment
                    'reference' => $paymentDetails['data']['reference'],
                    'amount' => $paymentDetails['data']['amount'] / 100, // Convert from pesewas to GHS
                    'currency' => 'GHS',
                    'payment_method' => $paymentDetails['data']['channel'],
                    'status' => 'completed',
                    'metadata' => json_encode($paymentDetails['data']), // Store full payment data as JSON
                ]);

                // Update user's fee payment status if needed
                // $user->update(['fee_paid' => true, 'fee_paid_at' => now()]);

                // You can access metadata values safely now
                $userName = $metadata['user_name'] ?? $user->name;
                $feeAmount = $metadata['fee_amount'] ?? ($paymentDetails['data']['amount'] / 100);

                // SEND EMAIL RECEIPT TO USER
                try {
                    Mail::to($user->email)->send(new PaymentReceiptMail($payment, $user));
                    \Log::info('Payment receipt email sent to ' . $user->email);
                } catch (\Exception $mailException) {
                    // Log email error but don't stop the process
                    \Log::error('Failed to send payment receipt email: ' . $mailException->getMessage());
                }

                return redirect()->route('student.payment')
                    ->with('success', 'Payment of ₵' . number_format($paymentDetails['data']['amount'] / 100, 2) . ' was successful! Receipt #' . $payment->id . ' has been sent to your email.');
            }

            return redirect()->route('student.payment')
                ->with('error', 'Payment was not successful. Please try again.');

        } catch (\Exception $e) {
            \Log::error('Payment callback failed: ' . $e->getMessage());
            return redirect()->route('student.payment')
                ->with('error', 'Unable to verify payment. Please contact support.');
        }
    }

    /**
     * Get user's bookings
     */
    public function bookings()
    {
        $bookings = Auth::user()->bookings()
            ->with('hostel', 'room')
            ->latest()
            ->paginate(10);

        return view('student.bookings.index', compact('bookings'));
    }

    /**
     * Show booking details
     */
    public function showBooking(Booking $booking)
    {
        // Check if user owns this booking
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $booking->load(['hostel', 'room', 'payment']);

        return view('student.bookings.show', compact('booking'));
    }

    /**
     * Cancel booking with refund (GHS)
     */
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
            // Calculate refund amount in GHS
            $refundAmount = $booking->total_amount;

            // Apply cancellation policy (10% fee for confirmed bookings)
            if ($currentStatus == 'confirmed') {
                $refundAmount = $booking->total_amount * 0.9; // 90% refund, 10% fee
            }

            // Process refund through Paystack (amount in pesewas)
            $refund = Paystack::refund([
                'transaction' => $booking->payment->transaction_id,
                'amount' => round($refundAmount * 100), // Convert GHS to pesewas
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
            if ($booking->room && $booking->room->hostel) {
                $booking->room->hostel->increment('available_rooms');

                // Decrement room occupancy
                if ($booking->room->current_occupancy > 0) {
                    $booking->room->decrement('current_occupancy');
                }
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
    }

    /**
     * List user's reviews
     */
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
                'is_verified' => true,
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
            $oldRating = $review->rating;
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

    /**
     * Browse available hostels (with price in GHS)
     */
public function browseHostels(Request $request)
{
    $hostels = Hostel::where('is_approved', 1)
        ->with(['primaryImage', 'rooms'])
        ->when($request->search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        })
        ->paginate(9); // Pagination works

    foreach ($hostels as $hostel) {
        // Proper filtering on Collection
        $availableRooms = $hostel->rooms->filter(function($room) {
            return $room->status === 'available' && $room->current_occupancy < $room->capacity;
        });

        $hostel->available_rooms_count = $availableRooms->count();

        // Use the actual room_cost from filtered rooms
        $hostel->min_price = $availableRooms->isNotEmpty()
            ? (float) $availableRooms->min('room_cost')
            : 0;
    }

    return view('student.hostels.browse', compact('hostels'));
}

    /**
     * View single hostel details (prices in GHS)
     */
 /**
 * View single hostel details
 */
/**
 * View single hostel details
 */
public function viewHostel(Hostel $hostel)
{
    // Ensure hostel is approved
    if (!$hostel->is_approved) {
        abort(404);
    }

    // Load hostel with images
    $hostel->load(['images', 'primaryImage']);
    
    // Get available rooms - fix the query
    $availableRooms = $hostel->rooms()
        ->where('status', 'available')
        ->where(function($q) {
            $q->whereColumn('current_occupancy', '<', 'capacity')
              ->orWhereNull('current_occupancy'); // Handle null values
        })
        ->get();

    // Set default occupancy to 0 if null
    foreach ($availableRooms as $room) {
        if ($room->current_occupancy === null) {
            $room->current_occupancy = 0;
        }
    }

   

    // Get average rating
    $averageRating = $hostel->reviews()->avg('rating') ?? 0;
    $reviewCount = $hostel->reviews()->count();

    // Get similar hostels
    $similarHostels = Hostel::where('is_approved', true)
        ->where('id', '!=', $hostel->id)
        ->where('location', $hostel->location)
        ->with(['primaryImage'])
        ->limit(3)
        ->get()
        ->map(function($h) {
            $h->min_price = $h->rooms()
                ->where('status', 'available')
                ->where(function($q) {
                    $q->whereColumn('current_occupancy', '<', 'capacity')
                      ->orWhereNull('current_occupancy');
                })
                ->min('price_per_month') ?? $h->rooms()->min('price_per_semester') / 4;
            return $h;
        });

    return view('student.hostels.show', compact('hostel', 'availableRooms', 'averageRating', 'reviewCount', 'similarHostels'));
}    /**
     * Get user's bookings with filters
     */
    public function myBookings(Request $request)
    {
        $query = Booking::where('user_id', Auth::id())
            ->with(['hostel', 'room', 'payment']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'checkin_asc':
                $query->orderBy('check_in_date', 'asc');
                break;
            case 'checkin_desc':
                $query->orderBy('check_in_date', 'desc');
                break;
            default:
                $query->latest();
        }

        $bookings = $query->paginate(10);
        return view('student.bookings.index', compact('bookings'));
    }

    /**
     * View booking details (amounts in GHS)
     */
    public function viewBooking(Booking $booking)
    {
        abort_if($booking->user_id !== Auth::id(), 403);
        $booking->load(['hostel', 'room', 'payment']);
        return view('student.bookings.show', compact('booking'));
    }

    /**
     * List user's complaints
     */
    public function complaints(Request $request)
    {
        $query = Complaint::where('user_id', Auth::id());

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $complaints = $query->latest()->paginate(10);
        return view('student.complaints.index', compact('complaints'));
    }

    /**
     * Store a new complaint
     */
    public function storeComplaint(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'category' => 'required|string|in:maintenance,payment,behavior,other',
            'priority' => 'nullable|in:low,medium,high,urgent',
            'booking_id' => 'nullable|exists:bookings,id',
            'description' => 'required|string|min:20|max:2000',
        ]);

        Complaint::create([
            'user_id' => Auth::id(),
            'subject' => $validated['subject'],
            'category' => $validated['category'],
            'priority' => $validated['priority'] ?? 'medium',
            'booking_id' => $validated['booking_id'] ?? null,
            'description' => $validated['description'],
            'status' => 'pending',
        ]);

        return redirect()->route('student.complaints')
            ->with('success', 'Your complaint has been submitted.');
    }

    /**
     * List user's payments (amounts in GHS)
     */
    public function payments(Request $request)
    {
        $query = Payment::whereHas('booking', function($q) {
                $q->where('user_id', Auth::id());
            })
            ->orWhere('user_id', Auth::id()) // For fee payments without booking
            ->with(['booking.hostel', 'booking.room'])
            ->latest();

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $payments = $query->paginate(10);

        return view('student.payments.index', compact('payments'));
    }

    /**
     * View payment receipt (amounts in GHS)
     */
    public function viewReceipt(Payment $payment)
    {
        // Check if payment belongs to user
        $hasAccess = false;

        if ($payment->booking && $payment->booking->user_id === Auth::id()) {
            $hasAccess = true;
        } elseif ($payment->user_id === Auth::id()) {
            $hasAccess = true;
        }

        abort_if(!$hasAccess, 403);

        $payment->load(['booking.hostel', 'booking.room']);
        return view('student.payments.receipt', compact('payment'));
    }

    /**
     * View user profile
     */
    public function profile()
    {
        $user = Auth::user();

        $stats = [
            'total_bookings' => Booking::where('user_id', $user->id)->count(),
            'total_paid' => Payment::whereHas('booking', fn($q) => $q->where('user_id', $user->id))
                ->where('status', 'completed')
                ->sum('amount'),
            'total_reviews' => Review::where('user_id', $user->id)->count(),
            'total_complaints' => Complaint::where('user_id', $user->id)->count(),
        ];

        return view('student.profile', compact('user', 'stats'));
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'password' => 'nullable|string|min:8|confirmed',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        $user->update($validated);

        return redirect()->route('student.profile')
            ->with('success', 'Profile updated successfully.');
    }
}
