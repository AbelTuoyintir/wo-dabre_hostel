<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\User;
use App\Models\Room;
use App\Models\Hostel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Unicodeveloper\Paystack\Facades\Paystack;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    /**
     * Show available hostels
     */
    public function selectHostel()
    {
        $hostels = Hostel::where('is_approved', true)
            ->where('status', 'active')
            ->with(['rooms' => function($q) {
                $q->where('status', 'available')
                  ->whereColumn('current_occupancy', '<', 'capacity');
            }])
            ->get();

        return view('bookings.select-hostel', compact('hostels'));
    }

    /**
     * Show rooms for selected hostel
     */
    public function selectRoom(Hostel $hostel)
    {
        if (!$hostel->is_approved || $hostel->status !== 'active') {
            abort(404);
        }

        $rooms = $hostel->rooms()
            ->where('status', 'available')
            ->whereColumn('current_occupancy', '<', 'capacity')
            ->get();

        return view('bookings.select-room', compact('hostel', 'rooms'));
    }

    /**
     * Show booking form for specific room
     */
    public function createBooking(Hostel $hostel, Room $room)
    {
        // Verify room belongs to hostel
        if ($room->hostel_id !== $hostel->id) {
            abort(404);
        }

        // Check if room is available
        if (!$room->isAvailable()) {
            return redirect()->route('bookings.hostel.rooms', $hostel)
                ->with('error', 'This room is no longer available.');
        }

        $user = Auth::check() ? Auth::user() : null;

        return view('bookings.create', compact('hostel', 'room', 'user'));
    }

    /**
     * Store booking and initialize payment
     */
    public function store(Request $request)
    {
        $rules = [
            'room_id' => 'required|exists:rooms,id',
            'hostel_id' => 'required|exists:hostels,id',
            'check_in' => 'required|date|after:today',
            'check_out' => 'required|date|after:check_in',
        ];

        // Add validation for guests only
        if (!Auth::check()) {
            $rules['name'] = 'required|string|max:255';
            $rules['email'] = 'required|email|unique:users,email,NULL,id';
            $rules['phone'] = 'required|string|max:20';
        }

        $validated = $request->validate($rules);

        $room = Room::findOrFail($validated['room_id']);

        // Check availability again
        if (!$this->checkRoomAvailability($room->id, $validated['check_in'], $validated['check_out'])) {
            return back()->with('error', 'Room is not available for selected dates.');
        }

        // Calculate total amount
        $nights = (new \DateTime($validated['check_in']))->diff(new \DateTime($validated['check_out']))->days;
        $totalAmount = $room->price_per_month * ($nights / 30); // Convert monthly to daily rate

        // Store booking data in session for after payment
        session(['pending_booking' => [
            'room_id' => $room->id,
            'hostel_id' => $room->hostel_id,
            'check_in' => $validated['check_in'],
            'check_out' => $validated['check_out'],
            'total_amount' => $totalAmount,
            'guest_data' => Auth::check() ? null : [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'temp_password' => $this->generateRandomPassword(),
            ],
        ]]);

        // Initialize payment with Paystack
        return $this->initializePaystackPayment();
    }

    /**
     * Initialize Paystack payment
     */
    private function initializePaystackPayment()
    {
        $pendingBooking = session('pending_booking');

        if (!$pendingBooking) {
            return redirect()->route('bookings.hostel.select')
                ->with('error', 'No booking information found. Please start over.');
        }

        // Determine user email
        if (Auth::check()) {
            $user = Auth::user();
            $email = $user->email;
            $userId = $user->id;
        } else {
            $email = $pendingBooking['guest_data']['email'];
            $userId = null;
        }

        // Calculate fees (1.95% for Paystack)
        $feePercentage = 0.0195;
        $finalAmount = $pendingBooking['total_amount'] * (1 + $feePercentage);

        try {
            $reference = Paystack::genTranxRef();

            $paymentData = [
                'email' => $email,
                'amount' => round($finalAmount * 100), // Convert to pesewas
                'currency' => 'GHS',
                'reference' => $reference,
                'callback_url' => route('payment.callback', ['gateway' => 'paystack']),
                'metadata' => json_encode([
                    'user_id' => $userId,
                    'is_guest' => !Auth::check(),
                    'guest_data' => $pendingBooking['guest_data'],
                    'booking_data' => [
                        'room_id' => $pendingBooking['room_id'],
                        'hostel_id' => $pendingBooking['hostel_id'],
                        'check_in' => $pendingBooking['check_in'],
                        'check_out' => $pendingBooking['check_out'],
                        'total_amount' => $pendingBooking['total_amount'],
                    ],
                    'reference' => $reference
                ]),
            ];

            session(['payment_reference' => $reference]);

            return Paystack::getAuthorizationUrl($paymentData)->redirectNow();

        } catch (\Exception $e) {
            \Log::error('Payment initialization failed: ' . $e->getMessage());
            return redirect()->route('bookings.hostel.select')
                ->with('error', 'Payment initialization failed. Please try again.');
        }
    }

    /**
     * Handle payment callback from Paystack
     */
    public function handlePaymentCallback($gateway)
    {
        if ($gateway !== 'paystack') {
            return redirect()->route('bookings.hostel.select')
                ->with('error', 'Unsupported payment gateway.');
        }

        try {
            $paymentDetails = Paystack::getPaymentData();

            if (!$paymentDetails['status'] || $paymentDetails['data']['status'] !== 'success') {
                return redirect()->route('bookings.hostel.select')
                    ->with('error', 'Payment was not successful. Please try again.');
            }

            $metadata = json_decode($paymentDetails['data']['metadata'], true);
            $bookingData = $metadata['booking_data'];

            DB::beginTransaction();

            $password = null;

            // Create user account if guest
            if ($metadata['is_guest']) {
                $tempPassword = $metadata['guest_data']['temp_password'];

                $user = User::create([
                    'name' => $metadata['guest_data']['name'],
                    'email' => $metadata['guest_data']['email'],
                    'phone' => $metadata['guest_data']['phone'],
                    'password' => Hash::make($tempPassword),
                    'role' => 'student',
                    'email_verified_at' => now(),
                ]);

                $userId = $user->id;
                $password = $tempPassword;

                Auth::login($user);
            } else {
                $userId = $metadata['user_id'];
                $user = User::find($userId);
            }

            // Check room availability one last time
            if (!$this->checkRoomAvailability($bookingData['room_id'], $bookingData['check_in'], $bookingData['check_out'])) {
                DB::rollBack();

                if ($metadata['is_guest'] && isset($user)) {
                    $user->delete();
                }

                return redirect()->route('bookings.hostel.select')
                    ->with('error', 'Sorry, the room is no longer available. Your payment will be refunded automatically.');
            }

            // Create booking
            $booking = Booking::create([
                'user_id' => $userId,
                'room_id' => $bookingData['room_id'],
                'hostel_id' => $bookingData['hostel_id'],
                'check_in' => $bookingData['check_in'],
                'check_out' => $bookingData['check_out'],
                'total_amount' => $bookingData['total_amount'],
                'status' => 'confirmed',
                'payment_status' => 'paid',
                'payment_reference' => $paymentDetails['data']['reference'],
                'booking_reference' => 'BK-' . strtoupper(Str::random(8)),
            ]);

            // Create payment record
            $booking->payment()->create([
                'amount' => $bookingData['total_amount'],
                'transaction_id' => $paymentDetails['data']['reference'],
                'payment_method' => $paymentDetails['data']['channel'],
                'status' => 'completed',
                'metadata' => json_encode($paymentDetails['data'])
            ]);

            // Update room occupancy
            $room = Room::find($bookingData['room_id']);
            $room->increment('current_occupancy');

            // Clear session data
            session()->forget(['pending_booking', 'payment_reference']);

            DB::commit();

            // Send confirmation emails
            $this->sendBookingConfirmation($booking, $user, $password);

            return redirect()->route('bookings.show', $booking)
                ->with('success', 'Payment successful! Your booking is confirmed.' .
                    ($password ? ' Your login credentials have been sent to your email.' : ''));

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Payment callback failed: ' . $e->getMessage());

            return redirect()->route('bookings.hostel.select')
                ->with('error', 'There was an error processing your booking. Please contact support.');
        }
    }

    /**
     * List user bookings
     */
    public function index()
    {
        $user = Auth::user();
        $bookings = Booking::where('user_id', $user->id)
            ->with(['room.hostel'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('hostel-manager.bookings.index', compact('bookings'));
    }

    /**
     * Show specific booking
     */
    public function show(Booking $booking)
    {
        // Ensure user owns this booking
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        $booking->load(['room.hostel', 'payment']);

        return view('bookings.show', compact('booking'));
    }

    /**
     * Cancel booking
     */
    public function cancel(Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        if ($booking->status === 'cancelled') {
            return back()->with('error', 'Booking is already cancelled.');
        }

        if ($booking->check_in->isPast()) {
            return back()->with('error', 'Cannot cancel a past booking.');
        }

        DB::transaction(function() use ($booking) {
            $booking->update([
                'status' => 'cancelled',
                'payment_status' => 'refund_pending'
            ]);

            // Free up room occupancy
            $room = $booking->room;
            if ($room) {
                $room->decrement('current_occupancy');
            }

            // TODO: Process refund via Paystack
        });

        return redirect()->route('bookings.show', $booking)
            ->with('success', 'Booking cancelled successfully. Refund will be processed within 3-5 business days.');
    }

    /**
     * AJAX route for fetching rooms
     */
    public function getRooms(Request $request)
    {
        $hostelId = $request->get('hostel_id');

        if (!$hostelId) {
            return response()->json(['error' => 'Hostel ID required'], 400);
        }

        $rooms = Room::where('hostel_id', $hostelId)
            ->where('status', 'available')
            ->whereColumn('current_occupancy', '<', 'capacity')
            ->get(['id', 'number', 'capacity', 'price_per_month']);

        return response()->json($rooms);
    }

    /**
     * Generate random secure password
     */
    private function generateRandomPassword($length = 10)
    {
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        $special = '!@#$%^&*()-_=+';

        $password = '';
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $special[random_int(0, strlen($special) - 1)];

        $allChars = $uppercase . $lowercase . $numbers . $special;
        for ($i = 4; $i < $length; $i++) {
            $password .= $allChars[random_int(0, strlen($allChars) - 1)];
        }

        return str_shuffle($password);
    }

    /**
     * Send booking confirmation email
     */
    private function sendBookingConfirmation($booking, $user, $plainPassword = null)
    {
        try {
            $data = [
                'booking' => $booking,
                'user' => $user,
                'login_url' => route('login'),
            ];

            if ($plainPassword) {
                $data['password'] = $plainPassword;
                $data['is_new_account'] = true;
            }

            Mail::send('emails.booking-confirmation', $data, function ($message) use ($user, $plainPassword) {
                $message->to($user->email, $user->name)
                        ->subject($plainPassword ?
                            'Welcome to UCC Hostels - Your Booking Confirmation & Login Details' :
                            'Your UCC Hostel Booking Confirmation');
            });

        } catch (\Exception $e) {
            \Log::error('Failed to send confirmation email: ' . $e->getMessage());
        }
    }

    /**
     * Check room availability
     */
    private function checkRoomAvailability($roomId, $checkIn, $checkOut)
    {
        return Booking::where('room_id', $roomId)
            ->where('status', 'confirmed')
            ->where(function($query) use ($checkIn, $checkOut) {
                $query->whereBetween('check_in', [$checkIn, $checkOut])
                      ->orWhereBetween('check_out', [$checkIn, $checkOut])
                      ->orWhere(function($q) use ($checkIn, $checkOut) {
                          $q->where('check_in', '<=', $checkIn)
                            ->where('check_out', '>=', $checkOut);
                      });
            })
            ->doesntExist();
    }
}
