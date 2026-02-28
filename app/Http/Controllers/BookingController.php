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
            return redirect()->route('student.hostels.show', $hostel)
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

        // Validate the request
        $validated = $request->validate($rules);

        $room = Room::findOrFail($validated['room_id']);

        // Check availability again
        if (!$this->checkRoomAvailability($room->id, $validated['check_in'], $validated['check_out'])) {
            return redirect()->route('student.hostels.show', $validated['hostel_id'])
                ->with('error', 'Room is not available for selected dates.');
        }

        // Calculate ONLY the room cost (no student fee here)
        $checkIn = new \DateTime($validated['check_in']);
        $checkOut = new \DateTime($validated['check_out']);
        $nights = $checkIn->diff($checkOut)->days;
        
        // Determine which price to use
        if (!empty($room->price_per_month) && $room->price_per_month > 0) {
            // Monthly rate
            $roomCost = ($room->price_per_month / 30) * $nights;
        } elseif (!empty($room->price_per_semester) && $room->price_per_semester > 0) {
            // Semester rate (assuming 4 months = 120 days)
            $roomCost = ($room->price_per_semester / 120) * $nights;
        } else {
            return back()->with('error', 'Room price not set.');
        }

        // Ensure amount is at least 1 GHS
        $roomCost = max(1, round($roomCost, 2));

        \Log::info('Booking store - Room cost calculation:', [
            'room_id' => $room->id,
            'nights' => $nights,
            'room_cost' => $roomCost
        ]);

        // Store booking data in session for after payment
        session(['pending_booking' => [
            'room_id' => $room->id,
            'hostel_id' => $room->hostel_id,
            'check_in' => $validated['check_in'],
            'check_out' => $validated['check_out'],
            'total_amount' => $roomCost, // This is ONLY the room cost
            'guest_data' => Auth::check() ? null : [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'temp_password' => $this->generateRandomPassword(),
            ],
        ]]);

        // Initialize payment with Paystack (will add student fee there)
        return $this->initializePaystackPayment();
    }

    /**
     * Initialize Paystack payment
     */
    private function initializePaystackPayment()
{
    $pendingBooking = session('pending_booking');

    if (!$pendingBooking) {
        return redirect()->route('student.hostels.browse')
            ->with('error', 'No booking information found. Please start over.');
    }

    // Determine user email
    if (Auth::check()) {
        $user = Auth::user();
        $email = $user->email;
        $userId = $user->id;
        $isGuest = false;
        $guestData = null;
    } else {
        $email = $pendingBooking['guest_data']['email'];
        $userId = null;
        $isGuest = true;
        $guestData = $pendingBooking['guest_data']; // This should contain temp_password
    }

    // Get the total amount from pending booking (room cost)
    $roomCost = $pendingBooking['total_amount'];
    
    // Get student fee from config (₵150)
    $studentFee = config('app.student_fee_amount', 150);
    
    // Calculate total before Paystack fee
    $subtotal = $roomCost + $studentFee;
    
    // Calculate Paystack fees (2%)
    $feePercentage = 0.02;
    $feeAmount = $subtotal * $feePercentage;
    $finalAmount = $subtotal + $feeAmount;
    
    // Convert to pesewas (multiply by 100) and ensure it's an integer
    $amountInPesewas = (int) round($finalAmount * 100);

    try {
        $reference = Paystack::genTranxRef();

        // IMPORTANT: Metadata must be a flat array, NOT nested inside another 'metadata' key
        $paymentData = [
            'email' => $email,
            'amount' => $amountInPesewas,
            'currency' => 'GHS',
            'reference' => $reference,
            'callback_url' => route('payment.callback', ['gateway' => 'paystack']),
            'metadata' => [ // This is the correct format - flat array
                'user_id' => $userId,
                'is_guest' => $isGuest, // Use boolean, not string
                'guest_data' => $guestData, // Will be null for logged-in users, array for guests
                'booking_data' => [
                    'room_id' => $pendingBooking['room_id'],
                    'hostel_id' => $pendingBooking['hostel_id'],
                    'check_in' => $pendingBooking['check_in'],
                    'check_out' => $pendingBooking['check_out'],
                    'room_cost' => $roomCost,
                    'student_fee' => $studentFee,
                    'total_amount' => $subtotal,
                ],
                'reference' => $reference
            ],
        ];

        \Log::info('Payment Data sent to Paystack:', $paymentData);

        session(['payment_reference' => $reference]);

        return Paystack::getAuthorizationUrl($paymentData)->redirectNow();

    } catch (\Exception $e) {
        \Log::error('Payment initialization failed: ' . $e->getMessage());
        return redirect()->route('student.hostels.browse')
            ->with('error', 'Payment initialization failed. Please try again.');
    }
}

/**
 * Handle payment callback from Paystack
 */
/**
 * Handle payment callback from Paystack
 */
public function handlePaymentCallback($gateway)
{
    if ($gateway !== 'paystack') {
        return redirect()->route('student.hostels.browse')
            ->with('error', 'Unsupported payment gateway.');
    }

    try {
        $paymentDetails = Paystack::getPaymentData();
        
        \Log::info('Payment callback - Full payment details:', ['paymentDetails' => $paymentDetails]);

        if (!$paymentDetails['status'] || $paymentDetails['data']['status'] !== 'success') {
            return redirect()->route('student.hostels.browse')
                ->with('error', 'Payment was not successful. Please try again.');
        }

        // Get metadata - Paystack returns it directly, not nested
        $metadata = $paymentDetails['data']['metadata'] ?? null;
        
        \Log::info('Metadata received:', ['metadata' => $metadata]);
        
        if (!is_array($metadata)) {
            \Log::error('Metadata is not an array:', ['type' => gettype($metadata)]);
            return redirect()->route('student.hostels.browse')
                ->with('error', 'Invalid payment data format. Please contact support.');
        }

        // Check if booking_data exists
        if (!isset($metadata['booking_data'])) {
            \Log::error('Missing booking_data in metadata:', ['metadata' => $metadata]);
            return redirect()->route('student.hostels.browse')
                ->with('error', 'Invalid booking data. Please contact support.');
        }
        
        $bookingData = $metadata['booking_data'];

        DB::beginTransaction();

        $password = null;
        $user = null;
        $userId = null;

        // Check if this is a guest user (using boolean, not string comparison)
        $isGuest = isset($metadata['is_guest']) && $metadata['is_guest'] === true;
        
        if ($isGuest) {
            \Log::info('Creating new user account for guest');
            
            // Check if guest data exists and is an array
            if (!isset($metadata['guest_data']) || !is_array($metadata['guest_data'])) {
                \Log::error('Invalid guest_data:', ['guest_data' => $metadata['guest_data'] ?? null]);
                DB::rollBack();
                return redirect()->route('student.hostels.browse')
                    ->with('error', 'Invalid guest data. Please contact support.');
            }
            
            $guestData = $metadata['guest_data'];
            
            // Check if temp_password exists
            if (!isset($guestData['temp_password'])) {
                \Log::error('Missing temp_password in guest_data:', ['guestData' => $guestData]);
                DB::rollBack();
                return redirect()->route('student.hostels.browse')
                    ->with('error', 'Invalid user data. Please contact support.');
            }
            
            $tempPassword = $guestData['temp_password'];

            // Create the user
            $user = User::create([
                'name' => $guestData['name'],
                'email' => $guestData['email'],
                'phone' => $guestData['phone'] ?? null,
                'password' => Hash::make($tempPassword),
                'role' => 'student',
                'email_verified_at' => now(),
            ]);

            $userId = $user->id;
            $password = $tempPassword;

            // Log the user in
            Auth::login($user);
            \Log::info('Guest user created and logged in:', ['user_id' => $userId]);
        } else {
            // For authenticated users
            $userId = $metadata['user_id'] ?? null;
            if (!$userId) {
                \Log::error('Missing user_id for authenticated user:', ['metadata' => $metadata]);
                DB::rollBack();
                return redirect()->route('student.hostels.browse')
                    ->with('error', 'Invalid user data. Please contact support.');
            }
            $user = User::find($userId);
            if (!$user) {
                \Log::error('User not found:', ['user_id' => $userId]);
                DB::rollBack();
                return redirect()->route('student.hostels.browse')
                    ->with('error', 'User not found. Please contact support.');
            }
            \Log::info('Authenticated user:', ['user_id' => $userId]);
        }

        // Check room availability one last time
        if (!$this->checkRoomAvailability($bookingData['room_id'], $bookingData['check_in'], $bookingData['check_out'])) {
            \Log::warning('Room no longer available:', ['room_id' => $bookingData['room_id']]);
            DB::rollBack();

            if ($isGuest && isset($user)) {
                $user->delete();
                \Log::info('Deleted guest user due to room unavailability');
            }

            return redirect()->route('student.hostels.browse')
                ->with('error', 'Sorry, the room is no longer available. Your payment will be refunded automatically.');
        }

        // Calculate total amount (convert string values to float if needed)
        $totalAmount = (float) ($bookingData['total_amount'] ?? 
                                 ((float)($bookingData['room_cost'] ?? 0) + (float)($bookingData['student_fee'] ?? 0)));

        // Create booking
        // Create booking
        $booking = Booking::create([
            'user_id' => $userId,
            'room_id' => $bookingData['room_id'],
            'hostel_id' => $bookingData['hostel_id'],
            'check_in' => $bookingData['check_in'],
            'check_out' => $bookingData['check_out'],
            'total_amount' => $totalAmount,
            'status' => 'confirmed',
            'payment_status' => 'paid',
            'payment_reference' => $paymentDetails['data']['reference'],
            'booking_reference' => 'BK-' . strtoupper(Str::random(8)),
            'booking_number' => 'BK-' . strtoupper(Str::random(8)), // Add this line
        ]);

        \Log::info('Booking created:', ['booking_id' => $booking->id]);

        // Create payment record
        $booking->payment()->create([
            'booking_id' => $booking->id,
            'amount' => $totalAmount,
            'transaction_id' => $paymentDetails['data']['reference'],
            'payment_method' => $paymentDetails['data']['channel'],
            'status' => 'completed',
            'metadata' => json_encode($paymentDetails['data'])
        ]);

        \Log::info('Payment record created');

        // Update room occupancy
        $room = Room::find($bookingData['room_id']);
        if ($room) {
            $room->increment('current_occupancy');
            \Log::info('Room occupancy incremented:', ['room_id' => $room->id, 'new_occupancy' => $room->current_occupancy]);
        }

        // Clear session data
        session()->forget(['pending_booking', 'payment_reference']);

        DB::commit();

        // Send confirmation emails
        try {
            $this->sendBookingConfirmation($booking, $user, $password);
            \Log::info('Confirmation email sent');
        } catch (\Exception $mailException) {
            \Log::error('Failed to send confirmation email: ' . $mailException->getMessage());
        }

        $successMessage = 'Payment successful! Your booking is confirmed.';
        if ($password) {
            $successMessage .= ' Your login credentials have been sent to your email.';
        }

        return redirect()->route('student.bookings.show', $booking)
            ->with('success', $successMessage);

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Payment callback failed: ' . $e->getMessage());
        \Log::error('Stack trace: ' . $e->getTraceAsString());

        return redirect()->route('student.hostels.browse')
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

        return view('student.bookings.index', compact('bookings'));
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

        return view('student.bookings.show', compact('booking'));
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

        return redirect()->route('student.bookings.show', $booking)
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