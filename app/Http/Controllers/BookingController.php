<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Hostel;
use App\Models\Room;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Display all hostels for booking
     */
    public function selectHostel()
    {
        $hostels = Hostel::where('is_active', true)
            ->withCount(['rooms as available_rooms_count' => function ($query) {
                $query->where('is_available', true);
            }])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->having('available_rooms_count', '>', 0)
            ->get();

        return view('bookings.select-hostel', compact('hostels'));
    }

    /**
     * Show rooms for a specific hostel
     */
    public function selectRoom(Request $request, Hostel $hostel)
    {
        $request->validate([
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
        ]);

        // Get available rooms for the selected dates
        $rooms = Room::where('hostel_id', $hostel->id)
            ->where('is_available', true)
            ->whereDoesntHave('bookings', function ($query) use ($request) {
                $query->where(function($q) use ($request) {
                    $q->whereBetween('check_in_date', [$request->check_in, $request->check_out])
                      ->orWhereBetween('check_out_date', [$request->check_in, $request->check_out])
                      ->orWhere(function($sub) use ($request) {
                          $sub->where('check_in_date', '<=', $request->check_in)
                              ->where('check_out_date', '>=', $request->check_out);
                      });
                })->whereIn('booking_status', ['confirmed', 'checked_in']);
            })
            ->get();

        $nights = now()->parse($request->check_in)->diffInDays($request->check_out);

        return view('bookings.select-room', compact('hostel', 'rooms', 'request', 'nights'));
    }

    /**
     * Show booking form for selected room
     */
    public function createBooking(Request $request, Hostel $hostel, Room $room)
    {
        // Verify room belongs to hostel
        if ($room->hostel_id !== $hostel->id) {
            abort(404);
        }

        $request->validate([
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
        ]);

        // Check room availability
        $isAvailable = $this->checkRoomAvailability($room->id, $request->check_in, $request->check_out);
        
        if (!$isAvailable) {
            return redirect()->route('bookings.hostel.rooms', [
                'hostel' => $hostel->id,
                'check_in' => $request->check_in,
                'check_out' => $request->check_out
            ])->with('error', 'Sorry, this room is no longer available for the selected dates.');
        }

        $nights = now()->parse($request->check_in)->diffInDays($request->check_out);
        $totalAmount = $room->price_per_night * $nights;

        return view('bookings.create', compact('hostel', 'room', 'request', 'nights', 'totalAmount'));
    }

    /**
     * Store booking and process payment
     */
    public function store(Request $request)
    {
        $request->validate([
            'hostel_id' => 'required|exists:hostels,id',
            'room_id' => 'required|exists:rooms,id',
            'check_in_date' => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'special_requests' => 'nullable|string|max:500',
            'payment_method' => 'required|in:paystack,flutterwave,bank_transfer',
            'terms_accepted' => 'required|accepted',
        ]);

        try {
            DB::beginTransaction();

            $hostel = Hostel::findOrFail($request->hostel_id);
            $room = Room::findOrFail($request->room_id);

            // Verify room belongs to hostel
            if ($room->hostel_id != $hostel->id) {
                throw new \Exception('Invalid room selection.');
            }

            // Double-check availability
            if (!$this->checkRoomAvailability($room->id, $request->check_in_date, $request->check_out_date)) {
                throw new \Exception('Room is no longer available for selected dates.');
            }

            // Calculate total amount
            $nights = now()->parse($request->check_in_date)->diffInDays($request->check_out_date);
            $totalAmount = $room->price_per_night * $nights;

            // Generate unique booking number
            $bookingNumber = $this->generateBookingNumber();

            // Create booking
            $booking = Booking::create([
                'booking_number' => $bookingNumber,
                'user_id' => Auth::id(),
                'hostel_id' => $request->hostel_id,
                'room_id' => $request->room_id,
                'room_number' => $room->room_number,
                'check_in_date' => $request->check_in_date,
                'check_out_date' => $request->check_out_date,
                'total_amount' => $totalAmount,
                'amount_paid' => 0,
                'payment_status' => 'pending',
                'booking_status' => 'pending',
                'special_requests' => $request->special_requests,
                'payment_method' => $request->payment_method,
            ]);

            DB::commit();

            // Handle payment
            if (in_array($request->payment_method, ['paystack', 'flutterwave'])) {
                return $this->initializeOnlinePayment($booking, $request->payment_method);
            }

            // For bank transfer
            return redirect()->route('bookings.show', $booking)
                ->with('success', 'Booking created successfully. Please complete payment using the bank details below.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Booking creation failed: ' . $e->getMessage());
            
            return back()
                ->withInput()
                ->with('error', $e->getMessage() ?: 'Failed to create booking. Please try again.');
        }
    }

    /**
     * Display user's bookings
     */
    public function index()
    {
        $bookings = Booking::where('user_id', Auth::id())
            ->with(['hostel', 'room'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('bookings.index', compact('bookings'));
    }

    /**
     * Display specific booking
     */
    public function show(Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        $booking->load(['hostel', 'room']);
        
        return view('bookings.show', compact('booking'));
    }

    /**
     * Initialize online payment
     */
    protected function initializeOnlinePayment(Booking $booking, $gateway)
    {
        $result = $this->paymentService->initializePayment($booking, $gateway);

        if ($result['success']) {
            session([
                'payment_reference' => $result['reference'],
                'booking_id' => $booking->id,
                'payment_gateway' => $gateway
            ]);

            return redirect($result['authorization_url']);
        }

        return redirect()->route('bookings.show', $booking)
            ->with('error', 'Payment initialization failed: ' . ($result['message'] ?? 'Unknown error'));
    }

    /**
     * Handle payment callback
     */
    public function handlePaymentCallback(Request $request, $gateway)
    {
        $reference = $request->reference ?? session('payment_reference');
        
        if (!$reference) {
            return redirect()->route('bookings.index')
                ->with('error', 'Invalid payment reference');
        }

        $result = $this->paymentService->verifyPayment($gateway, $reference);

        if ($result['success'] && in_array($result['status'], ['success', 'successful'])) {
            $bookingId = session('booking_id');
            $booking = Booking::find($bookingId);

            if ($booking) {
                DB::transaction(function () use ($booking, $result) {
                    $booking->amount_paid = $result['amount'];
                    $booking->payment_status = $result['amount'] >= $booking->total_amount ? 'paid' : 'partial';
                    $booking->booking_status = 'confirmed';
                    $booking->payment_date = now();
                    $booking->transaction_id = $result['transaction_id'];
                    $booking->save();
                });

                session()->forget(['payment_reference', 'booking_id', 'payment_gateway']);

                return redirect()->route('bookings.show', $booking)
                    ->with('success', 'Payment successful! Your booking is confirmed.');
            }
        }

        return redirect()->route('bookings.index')
            ->with('error', 'Payment verification failed. Please contact support.');
    }

    /**
     * Cancel booking
     */
    public function cancel(Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        if (!in_array($booking->booking_status, ['pending', 'confirmed'])) {
            return back()->with('error', 'This booking cannot be cancelled.');
        }

        try {
            DB::transaction(function () use ($booking) {
                $booking->update([
                    'booking_status' => 'cancelled',
                    'cancelled_at' => now(),
                ]);

                // Initiate refund if payment was made
                if ($booking->amount_paid > 0) {
                    // Add refund logic here
                    Log::info('Refund needed for booking: ' . $booking->booking_number);
                }
            });

            return redirect()->route('bookings.index')
                ->with('success', 'Booking cancelled successfully.');

        } catch (\Exception $e) {
            Log::error('Booking cancellation failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to cancel booking.');
        }
    }

    /**
     * Check room availability
     */
    protected function checkRoomAvailability($roomId, $checkIn, $checkOut)
    {
        $existingBookings = Booking::where('room_id', $roomId)
            ->where(function($query) use ($checkIn, $checkOut) {
                $query->whereBetween('check_in_date', [$checkIn, $checkOut])
                      ->orWhereBetween('check_out_date', [$checkIn, $checkOut])
                      ->orWhere(function($q) use ($checkIn, $checkOut) {
                          $q->where('check_in_date', '<=', $checkIn)
                            ->where('check_out_date', '>=', $checkOut);
                      });
            })
            ->whereIn('booking_status', ['confirmed', 'checked_in'])
            ->count();

        return $existingBookings === 0;
    }

    /**
     * Generate unique booking number
     */
    protected function generateBookingNumber()
    {
        $prefix = 'BK';
        $year = date('Y');
        $month = date('m');
        $random = strtoupper(substr(uniqid(), -6));
        
        return "{$prefix}-{$year}{$month}-{$random}";
    }
}