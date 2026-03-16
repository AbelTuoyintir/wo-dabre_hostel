<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\User;
use App\Models\Room;
use App\Models\Hostel;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Unicodeveloper\Paystack\Facades\Paystack;
use Illuminate\Support\Str;
use Carbon\Carbon;

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
     * Check if user's gender matches room's gender preference
     */
    private function checkGenderCompatibility(User $user, Room $room): bool
    {
        // If room accepts any gender, always compatible
        if ($room->gender === 'any') {
            return true;
        }

        // Check if user gender matches room gender preference
        return $user->gender === $room->gender;
    }

    /**
     * Update room gender based on first occupant
     */
    private function updateRoomGenderBasedOnFirstOccupant(Room $room, User $user): void
    {
        // Only update if room gender is 'any' and this is the first occupant
        if ($room->gender === 'any' && $room->current_occupancy === 0) {
            $room->gender = $user->gender;
            $room->save();

            \Log::info('Room gender updated based on first occupant:', [
                'room_id' => $room->id,
                'new_gender' => $user->gender
            ]);
        }
    }

    /**
     * Validate gender before allowing booking
     */
    private function validateGenderForBooking(Room $room, ?User $user = null): ?string
    {
        // If user is not logged in (guest), we'll validate after account creation
        if (!$user) {
            return null;
        }

        // Check if user has gender set
        if (empty($user->gender)) {
            return 'Please update your profile with your gender before booking.';
        }

        // Check gender compatibility
        if (!$this->checkGenderCompatibility($user, $room)) {
            return "This room is for {$room->gender} students only. Your gender does not match.";
        }

        return null;
    }

    /**
     * Show booking form for specific room
     */
    /**
 * Show booking form for guest users (no authentication required)
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

    // No authentication check - this is for guests only
    // If somehow an authenticated user reaches here, we still treat them as guest
    // They will create a new account after payment

    return view('bookings.createGuess', compact('hostel', 'room'));
}


    /**
     * Show booking form for authenticated student users
     *
     * This is identical to createBooking but routes through the student
     * namespace and uses the StudentStore action. The view handles both
     * guest and student scenarios by checking Auth::check().
     */
    public function StudentCreateBooking(Hostel $hostel, Room $room)
    {
        // authenticated middleware ensures user is logged in
        // verify room belongs to hostel
        if ($room->hostel_id !== $hostel->id) {
            abort(404);
        }

        // check availability
        if (!$room->isAvailable()) {
            return redirect()->route('student.hostels.show', $hostel)
                ->with('error', 'This room is no longer available.');
        }

        $user = Auth::user();

        // gender validation for logged-in student
        if ($user) {
            $genderError = $this->validateGenderForBooking($room, $user);
            if ($genderError) {
                return redirect()->route('student.hostels.show', $hostel)
                    ->with('error', $genderError);
            }
        }

        return view('student.bookings.create', compact('hostel', 'room', 'user'));
    }

    /**
     * Store booking and initialize payment
     */
    public function store(Request $request)
    {
        try {
            // Log incoming request data for debugging
            \Log::info('Booking store request received', [
                'request_data' => $request->except(['_token'])
            ]);

            $validated = $request->validate([
                'room_id' => 'required|exists:rooms,id',
                'hostel_id' => 'required|exists:hostels,id',
                'check_in_date' => 'required|date|after:today',
                'check_out_date' => 'required|date|after:check_in_date',
                'room_cost' => 'required|numeric|min:0',
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'phone' => 'required|string|max:20',
                'gender' => 'required|in:male,female',
            ]);

            \Log::info('Validation passed', ['validated_data' => $validated]);

            $room = Room::findOrFail($validated['room_id']);
            \Log::info('Room found', ['room_id' => $room->id, 'room_gender' => $room->gender]);

            // Check availability again
            try {
                $isAvailable = $this->checkRoomAvailability($room->id, $validated['check_in_date'], $validated['check_out_date']);
                
                if (!$isAvailable) {
                    \Log::warning('Room not available', [
                        'room_id' => $room->id,
                        'check_in' => $validated['check_in_date'],
                        'check_out' => $validated['check_out_date']
                    ]);
                    return redirect()->route('hostels.guest.show', $validated['hostel_id'])
                        ->with('error', 'Room is no longer available for selected dates.');
                }
                
                \Log::info('Room availability check passed');
            } catch (\Exception $e) {
                \Log::error('Error checking room availability: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString()
                ]);
                return redirect()->route('hostels.guest.show', $validated['hostel_id'])
                    ->with('error', 'Error checking room availability. Please try again.');
            }

            // Calculate nights
            try {
                $checkIn = Carbon::parse($validated['check_in_date']);
                $checkOut = Carbon::parse($validated['check_out_date']);
                $nights = $checkIn->diffInDays($checkOut);
                
                \Log::info('Date calculation', [
                    'check_in' => $checkIn->toDateString(),
                    'check_out' => $checkOut->toDateString(),
                    'nights' => $nights
                ]);
            } catch (\Exception $e) {
                \Log::error('Error parsing dates: ' . $e->getMessage());
                return redirect()->route('hostels.guest.show', $validated['hostel_id'])
                    ->with('error', 'Invalid date format. Please try again.');
            }

            // Calculate pro-rated room cost
            $roomCost = $validated['room_cost'];
            $proRatedRoomCost = $roomCost;
            
            \Log::info('Cost calculation', [
                'room_cost' => $roomCost,
                'pro_rated' => $proRatedRoomCost,
                'nights' => $nights
            ]);

            // Charges
            $agentFee = config('app.student_fee_amount', 150); 
            $systemCharge = 20; // ₵20

            $subTotal = $proRatedRoomCost + $agentFee + $systemCharge;
            $paystackFee = $subTotal * 0.021; 
            $finalTotal = $subTotal + $paystackFee;

            \Log::info('Final calculation', [
                'subtotal' => $subTotal,
                'paystack_fee' => $paystackFee,
                'final_total' => $finalTotal
            ]);

            // Generate random password for guest
            try {
                $tempPassword = $this->generateRandomPassword();
                \Log::info('Password generated for guest');
            } catch (\Exception $e) {
                \Log::error('Error generating password: ' . $e->getMessage());
                return redirect()->route('hostels.guest.show', $validated['hostel_id'])
                    ->with('error', 'Error creating account. Please try again.');
            }

            // Store booking data in session for after payment
            try {
                $pendingBooking = [
                    'room_id' => $room->id,
                    'hostel_id' => $room->hostel_id,
                    'check_in_date' => $validated['check_in_date'],
                    'check_out_date' => $validated['check_out_date'],
                    'room_cost' => $validated['room_cost'],
                    'pro_rated_room_cost' => $proRatedRoomCost,
                    'nights' => $nights,
                    'agent_fee' => $agentFee,
                    'system_charge' => $systemCharge,
                    'paystack_fee' => $paystackFee,
                    'final_total' => $finalTotal,
                    'room_gender' => $room->gender,
                    'room_occupancy' => $room->current_occupancy,
                    'guest_data' => [
                        'name' => $validated['name'],
                        'email' => $validated['email'],
                        'phone' => $validated['phone'],
                        'gender' => $validated['gender'],
                        'temp_password' => $tempPassword,
                    ],
                ];

                session(['pending_booking' => $pendingBooking]);
                \Log::info('Pending booking stored in session', [
                    'session_data' => array_keys($pendingBooking)
                ]);

            } catch (\Exception $e) {
                \Log::error('Error s toring session data: ' . $e->getMessage());
                return redirect()->route('hostels.guest.show', $validated['hostel_id'])
                    ->with('error', 'Error processing your booking. Please try again.');
            }

            // Initialize payment
            try {
                \Log::info('Initializing guest payment');
                return $this->initializeGuestPayment();
                
            } catch (\Exception $e) {
                \Log::error('Error initializing payment: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString()
                ]);
                
                // Clear the pending booking if payment initialization fails
                session()->forget('pending_booking');
                
                return redirect()->route('hostels.guest.show', $validated['hostel_id'])
                    ->with('error', 'Payment initialization failed. Please try again.');
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation errors - let Laravel handle normally
            \Log::warning('Validation failed', [
                'errors' => $e->errors(),
                'input' => $request->except(['_token'])
            ]);
            throw $e; // Re-throw to let Laravel handle validation errors
            
        } catch (\Exception $e) {
            // Catch any other unexpected errors
            \Log::error('Unexpected error in store method: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->except(['_token'])
            ]);
            
            return redirect()->route('hostels.guest.show', $validated['hostel_id'])
                ->with('error', 'An unexpected error occurred. Please try again or contact support.');
        }
    }

    private function initializeGuestPayment()
    {
        $pendingBooking = session('pending_booking');

        if (!$pendingBooking) {
            return redirect()->route('hostels.index')
                ->with('error', 'No booking information found. Please start over.');
        }

        $guestData = $pendingBooking['guest_data'];
        $email = $guestData['email'];
        $finalTotal = $pendingBooking['final_total'];

        // Convert to pesewas (multiply by 100)
        $amountInPesewas = (int) round($finalTotal * 100);

        try {
            $reference = 'BK-' . strtoupper(Str::random(12));

            $paymentData = [
                'email' => $email,
                'amount' => $amountInPesewas,
                'currency' => 'GHS',
                'reference' => $reference,
                'callback_url' => route('bookings.payment.callback', ['gateway' => 'paystack']), // This must match
                'metadata' => [
                    'user_id' => null,
                    'is_guest' => true,
                    'guest_data' => $guestData,
                    'booking_data' => [
                        'room_id' => $pendingBooking['room_id'],
                        'hostel_id' => $pendingBooking['hostel_id'],
                        'check_in_date' => $pendingBooking['check_in_date'],
                        'check_out_date' => $pendingBooking['check_out_date'],
                        'room_cost' => $pendingBooking['room_cost'],
                        'pro_rated_room_cost' => $pendingBooking['pro_rated_room_cost'],
                        'agent_fee' => $pendingBooking['agent_fee'],
                        'system_charge' => $pendingBooking['system_charge'],
                        'paystack_fee' => $pendingBooking['paystack_fee'],
                        'final_total' => $pendingBooking['final_total'],
                        'room_gender' => $pendingBooking['room_gender'],
                        'room_occupancy' => $pendingBooking['room_occupancy'],
                    ],
                    'reference' => $reference
                ],
            ];

            \Log::info('payment details:', $paymentData);

            session(['payment_reference' => $reference]);

            return Paystack::getAuthorizationUrl($paymentData)->redirectNow();

        } catch (\Exception $e) {
            \Log::error('Guest payment initialization failed: ' . $e->getMessage());
            return redirect()->route('hostels.index')
                ->with('error', 'Payment initialization failed. Please try again.');
        }
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
            $guestData = $pendingBooking['guest_data']; // This contains temp_password and gender
        }

        // Get the total amount from pending booking (room cost)
        $roomCost = $pendingBooking['total_amount'];

        // Get student fee from config (₵150)
        $studentFee = config('app.student_fee_amount', 150);

        //Get system charges
        $systemCharge = 20; // ₵20 

        // Calculate total before Paystack fee
        $subtotal = $roomCost + $studentFee + $systemCharge;

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
                'callback_url' => route('bookings.payment.callback', ['gateway' => 'paystack']),
                'metadata' => [ // This is the correct format - flat array
                    'user_id' => $userId,
                    'is_guest' => $isGuest, // Use boolean, not string
                    'guest_data' => $guestData, // Will be null for logged-in users, array for guests
                    'booking_data' => [
                        'room_id' => $pendingBooking['room_id'],
                        'hostel_id' => $pendingBooking['hostel_id'],
                        'check_in_date' => $pendingBooking['check_in_date'],
                        'check_out_date' => $pendingBooking['check_out_date'],
                        'room_cost' => $roomCost,
                        'student_fee' => $studentFee,
                        'total_amount' => $subtotal,
                        'room_gender' => $pendingBooking['room_gender'], // Pass room gender for validation
                        'room_occupancy' => $pendingBooking['room_occupancy'], // Pass room occupancy
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

        if ($booking->check_in_date->isPast()) {
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
     * AJAX helper to calculate booking totals (nights, subtotal, fees)
     */
    public function calculate(Request $request)
{
    $validated = $request->validate([
        'check_in_date' => 'required|date',
        'check_out_date' => 'required|date|after:check_in_date',
        'room_id' => 'required|exists:rooms,id',
        'room_cost' => 'nullable|numeric', // <-- we validate room_cost
    ]);

    // Dates
    $checkIn  = Carbon::parse($validated['check_in_date']);
    $checkOut = Carbon::parse($validated['check_out_date']);
    $nights   = $checkIn->diffInDays($checkOut);

    // Get room cost (yearly)
    $roomCost = $validated['room_cost'];

    if (!$roomCost) {
        $room = Room::findOrFail($validated['room_id']);
        $roomCost = $room->room_cost ?? 0;
    }

    // Charges
    $agentFee     = config('app.agent_fee', 150); // ₵150
    $systemCharge = 20; // ₵20

    $subTotal = $roomCost + $agentFee + $systemCharge;

    // Paystack charge (1.95%)
    $paystackFee = $subTotal * 0.0195;

    $finalTotal = $subTotal + $paystackFee;

    \Log::info('Price calculation', [
        'nights' => $nights,
        'room_cost' => $roomCost,
        'agent_fee' => $agentFee,
        'system_charge' => $systemCharge,
        'paystack_fee' => $paystackFee,
        'final_total' => $finalTotal
    ]);

    return response()->json([
        'success' => true,
        'nights' => $nights,

        // IMPORTANT
        'room_cost' => round($roomCost, 2),

        'agent_fee' => round($agentFee, 2),
        'system_charge' => round($systemCharge, 2),
        'paystack_fee' => round($paystackFee, 2),
        'total' => round($finalTotal, 2),
    ]);
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
     * Check room availability
     */
    private function checkRoomAvailability($roomId, $checkIn, $checkOut)
    {
        return Booking::where('room_id', $roomId)
            ->where('status', 'confirmed')
            ->where(function($query) use ($checkIn, $checkOut) {
                $query->whereBetween('check_in_date', [$checkIn, $checkOut])
                      ->orWhereBetween('check_out_date', [$checkIn, $checkOut])
                      ->orWhere(function($q) use ($checkIn, $checkOut) {
                          $q->where('check_in_date', '<=', $checkIn)
                            ->where('check_out_date', '>=', $checkOut);
                      });
            })
            ->doesntExist();
    }

    /**
 * Store booking for authenticated students (no guest data needed)
 */
public function StudentStore(Request $request)
{


    $rules = [
        'room_id' => 'required|exists:rooms,id',
        'hostel_id' => 'required|exists:hostels,id',
        'check_in_date' => 'required|date',
        'check_out_date' => 'required|date|after:check_in_date',
        'room_cost' => 'required|numeric|min:0',
    ];

    // Validate the request
    $validated = $request->validate($rules);

    // Ensure user is authenticated
    if (!Auth::check()) {
        return redirect()->route('login')->with('error', 'Please login to continue.');
    }

    $user = Auth::user();
    $room = Room::findOrFail($validated['room_id']);

    // Gender validation for logged-in users
    if (empty($user->gender)) {
        return redirect()->route('student.profile')
            ->with('error', 'Please update your profile with your gender before booking.');
    }

    // Check gender compatibility
    $genderError = $this->validateGenderForBooking($room, $user);
    if ($genderError) {
        return redirect()->route('student.hostels.show', $validated['hostel_id'])
            ->with('error', $genderError);
    }

    // Check availability again
    if (!$this->checkRoomAvailability($room->id, $validated['check_in_date'], $validated['check_out_date'])) {
        return redirect()->route('student.hostels.show', $validated['hostel_id'])
            ->with('error', 'Room is not available for selected dates.');
    }

    // Get student fee from config (₵150)
    $studentFee = config('app.student_fee_amount', 150);
    $systemCharge = 20;

    // Calculate total amount (room cost + student fee)
    $totalAmount = $validated['room_cost'] + $studentFee + 20;

    \Log::info('Student booking - Cost calculation:', [
        'user_id' => $user->id,
        'room_id' => $room->id,
        'room_cost' => $validated['room_cost'],
        'student_fee' => $studentFee,
        'total_amount' => $totalAmount
    ]);

    // Store booking data in session for after payment
    session(['pending_booking' => [
        'user_id' => $user->id,
        'room_id' => $room->id,
        'hostel_id' => $room->hostel_id,
        'check_in_date' => $validated['check_in_date'],
        'check_out_date' => $validated['check_out_date'],
        'room_cost' => $validated['room_cost'],
        'student_fee' => $studentFee,
        'total_amount' => $totalAmount,
        'room_gender' => $room->gender,
        'room_occupancy' => $room->current_occupancy,
        'is_authenticated' => true,
        'user_data' => [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'gender' => $user->gender,
        ],
    ]]);

    // Initialize payment with Paystack
    return $this->initializeStudentPayment();
}

/**
 * Initialize Paystack payment for authenticated students
 */
private function initializeStudentPayment()
{
    $pendingBooking = session('pending_booking');

    if (!$pendingBooking) {
        return redirect()->route('student.hostels.browse')
            ->with('error', 'No booking information found. Please start over.');
    }

    $user = Auth::user();

    // Get the total amount from pending booking (room cost + student fee)
    $totalAmount = $pendingBooking['total_amount'];

    // Calculate Paystack fees (2%)
    $feePercentage = 0.02;
    $feeAmount = $totalAmount * $feePercentage;
    $finalAmount = $totalAmount + $feeAmount;

    // Convert to pesewas (multiply by 100) and ensure it's an integer
    $amountInPesewas = (int) round($finalAmount * 100);

    \Log::info('Student Payment Initialization - Details:', [
        'user_id' => $user->id,
        'total_amount' => $totalAmount,
        'fee_amount' => $feeAmount,
        'final_amount' => $finalAmount,
        'amount_in_pesewas' => $amountInPesewas
    ]);

    // Validate amount is reasonable (at least 100 pesewas = 1 GHS)
    if ($amountInPesewas < 100) {
        \Log::error('Payment initialization failed: Amount in pesewas too low - ' . $amountInPesewas);
        return redirect()->route('student.hostels.browse')
            ->with('error', 'Invalid payment amount. Please contact support.');
    }

    try {
        $reference = Paystack::genTranxRef();

        $paymentData = [
            'email' => $user->email,
            'amount' => $amountInPesewas,
            'currency' => 'GHS',
            'reference' => $reference,
            'callback_url' => route('bookings.payment.callback', ['gateway' => 'paystack']),
            'metadata' => [
                'user_id' => $user->id,
                'is_guest' => false,
                'booking_data' => [
                    'room_id' => $pendingBooking['room_id'],
                    'hostel_id' => $pendingBooking['hostel_id'],
                    'check_in_date' => $pendingBooking['check_in_date'],
                    'check_out_date' => $pendingBooking['check_out_date'],
                    'room_cost' => $pendingBooking['room_cost'],
                    'student_fee' => $pendingBooking['student_fee'],
                    'total_amount' => $pendingBooking['total_amount'],
                    'room_gender' => $pendingBooking['room_gender'],
                    'room_occupancy' => $pendingBooking['room_occupancy'],
                ],
                'reference' => $reference
            ],
        ];

        \Log::info('Student Payment Data sent to Paystack:', $paymentData);

        session(['payment_reference' => $reference]);

        return Paystack::getAuthorizationUrl($paymentData)->redirectNow();

    } catch (\Exception $e) {
        \Log::error('Student payment initialization failed: ' . $e->getMessage());
        return redirect()->route('student.hostels.browse')
            ->with('error', 'Payment initialization failed. Please try again.');
    }
}

 /**
     * Handle payment callback for ALL users (both guests and students)
     * This is the single callback URL that Paystack will redirect to
     */
    public function handlePaymentCallback($gateway)
    {
        if ($gateway !== 'paystack') {
            return redirect()->route('student.hostels.browse')
                ->with('error', 'Unsupported payment gateway.');
        }

        try {
            $paymentDetails = Paystack::getPaymentData();

            \Log::info('Payment callback received:', ['paymentDetails' => $paymentDetails]);

            if (!$paymentDetails['status'] || $paymentDetails['data']['status'] !== 'success') {
                return redirect()->route('student.hostels.browse')
                    ->with('error', 'Payment was not successful. Please try again.');
            }

            // Get metadata
            $metadata = $paymentDetails['data']['metadata'] ?? null;

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

            // Determine if this is a guest or student based on metadata
            $isGuest = isset($metadata['is_guest']) && $metadata['is_guest'] === true;

            // Start database transaction
            DB::beginTransaction();

            try {
                if ($isGuest) {
                    // Handle guest payment
                    $result = $this->processGuestPayment($paymentDetails, $metadata);
                } else {
                    // Handle student payment
                    $result = $this->processStudentPayment($paymentDetails, $metadata);
                }

                DB::commit();
                return $result;

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            \Log::error('Payment callback failed: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());

            return redirect()->route('student.hostels.browse')
                ->with('error', 'There was an error processing your booking. Please contact support.');
        }
    }

    /**
     * Process payment for guest users (creates new account)
     */
    private function processGuestPayment($paymentDetails, $metadata)
    {
        \Log::info('Processing guest payment callback');

        $bookingData = $metadata['booking_data'];

        // Check if guest data exists
        if (!isset($metadata['guest_data']) || !is_array($metadata['guest_data'])) {
            \Log::error('Invalid guest_data:', ['guest_data' => $metadata['guest_data'] ?? null]);
            throw new \Exception('Invalid guest data');
        }

        $guestData = $metadata['guest_data'];

        // Check if temp_password exists
        if (!isset($guestData['temp_password'])) {
            \Log::error('Missing temp_password in guest_data:', ['guestData' => $guestData]);
            throw new \Exception('Missing temporary password');
        }

        $tempPassword = $guestData['temp_password'];

        // Create the user with gender from guest data
        $user = User::create([
            'name' => $guestData['name'],
            'email' => $guestData['email'],
            'phone' => $guestData['phone'] ?? null,
            'gender' => $guestData['gender'] ?? null,
            'password' => Hash::make($tempPassword),
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        $password = $tempPassword;

        \Log::info('Guest user created:', ['user_id' => $user->id, 'gender' => $user->gender]);

        // Process the booking
        return $this->finalizeBooking($paymentDetails, $bookingData, $user, $password, true);
    }

    /**
     * Process payment for authenticated students
     */
    private function processStudentPayment($paymentDetails, $metadata)
    {
        \Log::info('Processing student payment callback');

        $bookingData = $metadata['booking_data'];

        // Get the authenticated user
        $userId = $metadata['user_id'] ?? null;
        if (!$userId) {
            \Log::error('Missing user_id for authenticated user:', ['metadata' => $metadata]);
            throw new \Exception('Missing user ID');
        }

        $user = User::find($userId);
        if (!$user) {
            \Log::error('User not found:', ['user_id' => $userId]);
            throw new \Exception('User not found');
        }

        \Log::info('Authenticated user found:', ['user_id' => $userId, 'gender' => $user->gender]);

        // Process the booking
        return $this->finalizeBooking($paymentDetails, $bookingData, $user, null, false);
    }

    /**
     * Finalize booking after successful payment (common for both guests and students)
     */
    private function finalizeBooking($paymentDetails, $bookingData, $user, $password = null, $isGuest = false)
    {
        // Get the room
        $room = Room::find($bookingData['room_id']);
        if (!$room) {
            throw new \Exception('Room not found');
        }

        // Check room availability one last time
        if (!$this->checkRoomAvailability($bookingData['room_id'], $bookingData['check_in_date'], $bookingData['check_out_date'])) {
            \Log::warning('Room no longer available:', ['room_id' => $bookingData['room_id']]);

            if ($isGuest) {
                $user->delete();
                \Log::info('Deleted guest user due to room unavailability');
            }

            throw new \Exception('Room no longer available');
        }

        // ===== GENDER VALIDATION LOGIC =====
        // Check if user has gender set
        if (empty($user->gender)) {
            \Log::error('User has no gender set:', ['user_id' => $user->id]);

            if ($isGuest) {
                $user->delete();
            }

            throw new \Exception('Gender information is required');
        }

        // Check gender compatibility
        if ($room->gender !== 'any' && $user->gender !== $room->gender) {
            \Log::error('Gender mismatch after payment:', [
                'user_gender' => $user->gender,
                'room_gender' => $room->gender,
                'user_id' => $user->id,
                'room_id' => $room->id
            ]);

            if ($isGuest) {
                $user->delete();
            }

            throw new \Exception('Gender mismatch. This room is for ' . $room->gender . ' students only.');
        }

        // If room gender is 'any' and this is the first occupant, update room gender
        if ($room->gender === 'any' && $room->current_occupancy === 0) {
            $room->gender = $user->gender;
            $room->save();
            \Log::info('Room gender updated to ' . $user->gender . ' based on first occupant');
        }

        // Calculate total amount
        $totalAmount = (float) ($bookingData['final_total'] ?? 
                                 ((float)($bookingData['room_cost'] ?? 0) + 
                                  (float)($bookingData['agent_fee'] ?? 150) + 
                                  (float)($bookingData['system_charge'] ?? 20)));

        // Create booking with unique references
        $bookingReference = 'BK-' . strtoupper(Str::random(8));
        $bookingNumber = 'BN-' . strtoupper(Str::random(8));

        // Prepare booking data
        $bookingDataArray = [
            'user_id' => $user->id,
            'room_id' => $bookingData['room_id'],
            'hostel_id' => $bookingData['hostel_id'],
            'check_in_date' => $bookingData['check_in_date'],
            'check_out_date' => $bookingData['check_out_date'],
            'total_amount' => $totalAmount,
            'amount_paid' => $totalAmount,
            'payment_status' => 'paid',
            'booking_status' => 'confirmed',
            'payment_method' => $paymentDetails['data']['channel'],
            'transaction_id' => $paymentDetails['data']['reference'],
            'payment_date' => now(),
            'booking_reference' => $bookingReference,
            'booking_number' => $bookingNumber,
        ];

        // Add special_requests if exists
        if (isset($bookingData['special_requests'])) {
            $bookingDataArray['special_requests'] = $bookingData['special_requests'];
        }

        $booking = Booking::create($bookingDataArray);

        \Log::info('Booking created:', [
            'booking_id' => $booking->id,
            'booking_number' => $booking->booking_number,
            'booking_reference' => $booking->booking_reference,
            'payment_method' => $booking->payment_method,
            'transaction_id' => $booking->transaction_id
        ]);

        // Create payment record in payments table if it exists
        if (class_exists('App\Models\Payment')) {
            $booking->payments()->create([
                'booking_id' => $booking->id,
                'amount' => $totalAmount,
                'transaction_id' => $paymentDetails['data']['reference'],
                'payment_method' => $paymentDetails['data']['channel'],
                'status' => 'completed',
                'paid_at' => now(),
            ]);
            \Log::info('Payment record created in payments table');
        }

        // Update room occupancy
        $room->increment('current_occupancy');
        \Log::info('Room occupancy incremented:', [
            'room_id' => $room->id,
            'new_occupancy' => $room->current_occupancy
        ]);

        // Clear session data
        session()->forget(['pending_booking', 'payment_reference']);

        // Log the user in if guest
        if ($isGuest) {
            Auth::login($user);
        }

        // Send confirmation emails
        try {
            $this->sendBookingConfirmation($booking, $user, $password);
            \Log::info('Confirmation email sent to: ' . $user->email);
        } catch (\Exception $mailException) {
            \Log::error('Failed to send confirmation email: ' . $mailException->getMessage());
        }

        $successMessage = 'Payment successful! Your booking is confirmed.';
        if ($password) {
            $successMessage .= ' Your login credentials have been sent to your email.';
        }

        // Redirect to booking details page
        return redirect()->route('student.bookings.show', $booking)
            ->with('success', $successMessage);
    }

    /**
     * Check room availability
     */
    // private function checkRoomAvailability($roomId, $checkIn, $checkOut)
    // {
    //     return Booking::where('room_id', $roomId)
    //         ->where('booking_status', 'confirmed')
    //         ->where(function($query) use ($checkIn, $checkOut) {
    //             $query->whereBetween('check_in_date', [$checkIn, $checkOut])
    //                   ->orWhereBetween('check_out_date', [$checkIn, $checkOut])
    //                   ->orWhere(function($q) use ($checkIn, $checkOut) {
    //                       $q->where('check_in_date', '<=', $checkIn)
    //                         ->where('check_out_date', '>=', $checkOut);
    //                   });
    //         })
    //         ->doesntExist();
    // }

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

    // /**
    //  * Common method to process successful payment and create booking
    //  */
    // private function processSuccessfulPayment($paymentDetails, $bookingData, $user, $userId, $password = null, $isGuest = false)
    // {
    //     try {
    //         // Get the room
    //         $room = Room::find($bookingData['room_id']);
    //         if (!$room) {
    //             DB::rollBack();
    //             return redirect()->route('student.hostels.browse')
    //                 ->with('error', 'Room not found. Please contact support.');
    //         }

    //         // Check room availability one last time
    //         if (!$this->checkRoomAvailability($bookingData['room_id'], $bookingData['check_in_date'], $bookingData['check_out_date'])) {
    //             \Log::warning('Room no longer available:', ['room_id' => $bookingData['room_id']]);

    //             if ($isGuest && isset($user)) {
    //                 $user->delete();
    //                 \Log::info('Deleted guest user due to room unavailability');
    //             }

    //             DB::rollBack();
    //             return redirect()->route('student.hostels.browse')
    //                 ->with('error', 'Sorry, the room is no longer available. Your payment will be refunded automatically.');
    //         }

    //         // ===== GENDER VALIDATION LOGIC =====
    //         // Check if user has gender set
    //         if (empty($user->gender)) {
    //             \Log::error('User has no gender set:', ['user_id' => $user->id]);

    //             if ($isGuest) {
    //                 $user->delete();
    //             }

    //             DB::rollBack();
    //             return redirect()->route('student.hostels.browse')
    //                 ->with('error', 'Gender information is required. Please contact support.');
    //         }

    //         // Check gender compatibility
    //         if ($room->gender !== 'any' && $user->gender !== $room->gender) {
    //             \Log::error('Gender mismatch after payment:', [
    //                 'user_gender' => $user->gender,
    //                 'room_gender' => $room->gender,
    //                 'user_id' => $user->id,
    //                 'room_id' => $room->id
    //             ]);

    //             if ($isGuest) {
    //                 $user->delete();
    //             }

    //             DB::rollBack();
    //             return redirect()->route('student.hostels.browse')
    //                 ->with('error', 'Gender mismatch. This room is for ' . $room->gender . ' students only. Your payment will be refunded.');
    //         }

    //         // If room gender is 'any' and this is the first occupant, update room gender
    //         if ($room->gender === 'any' && $room->current_occupancy === 0) {
    //             $room->gender = $user->gender;
    //             $room->save();
    //             \Log::info('Room gender updated to ' . $user->gender . ' based on first occupant');
    //         }

    //         // Calculate total amount
    //         $totalAmount = (float) ($bookingData['total_amount'] ??
    //                                  ((float)($bookingData['room_cost'] ?? 0) + (float)($bookingData['student_fee'] ?? 0)));

    //         // Create booking with unique references
    //         $bookingReference = 'BK-' . strtoupper(Str::random(8));
    //         $bookingNumber = 'BN-' . strtoupper(Str::random(8));

    //         $booking = Booking::create([
    //             'user_id' => $userId,
    //             'room_id' => $bookingData['room_id'],
    //             'hostel_id' => $bookingData['hostel_id'],
    //             'check_in_date' => $bookingData['check_in_date'],
    //             'check_out_date' => $bookingData['check_out_date'],
    //             'total_amount' => $totalAmount,
    //             'status' => 'confirmed',
    //             'payment_status' => 'paid',
    //             'payment_reference' => $paymentDetails['data']['reference'],
    //             'booking_reference' => $bookingReference,
    //             'booking_number' => $bookingNumber,
    //         ]);

    //         \Log::info('Booking created:', [
    //             'booking_id' => $booking->id,
    //             'booking_number' => $booking->booking_number,
    //             'booking_reference' => $booking->booking_reference
    //         ]);

    //         // Create payment record with detailed logging
    //         try {
    //             \Log::info('Attempting to create payment record:', [
    //                 'booking_id' => $booking->id,
    //                 'user_id' => $userId,
    //                 'amount' => $totalAmount,
    //                 'transaction_id' => $paymentDetails['data']['reference'],
    //                 'payment_method' => $paymentDetails['data']['channel'],
    //             ]);

    //             $payment = Payment::create([
    //                 'user_id' => $userId,
    //                 'booking_id' => $booking->id,
    //                 'reference' => $paymentDetails['data']['reference'],
    //                 'amount' => $totalAmount,
    //                 'currency' => 'GHS',
    //                 'transaction_id' => $paymentDetails['data']['reference'],
    //                 'payment_method' => $paymentDetails['data']['channel'],
    //                 'status' => 'completed',
    //                 'paid_at' => now(),
    //             ]);

    //             \Log::info('Payment record created successfully:', [
    //                 'payment_id' => $payment->id,
    //                 'payment_reference' => $payment->reference,
    //             ]);
    //         } catch (\Exception $paymentException) {
    //             \Log::error('Failed to create payment record:', [
    //                 'error' => $paymentException->getMessage(),
    //                 'trace' => $paymentException->getTraceAsString(),
    //             ]);
    //             throw $paymentException;
    //         }

    //         // Update room occupancy
    //         if ($room) {
    //             $room->increment('current_occupancy');
    //             \Log::info('Room occupancy incremented:', [
    //                 'room_id' => $room->id,
    //                 'new_occupancy' => $room->current_occupancy
    //             ]);
    //         }

    //         // Clear session data
    //         session()->forget(['pending_booking', 'payment_reference']);

    //         DB::commit();

    //         // Send confirmation emails
    //         try {
    //             $this->sendBookingConfirmation($booking, $user, $password);
    //             \Log::info('Confirmation email sent to: ' . $user->email);
    //         } catch (\Exception $mailException) {
    //             \Log::error('Failed to send confirmation email: ' . $mailException->getMessage());
    //         }

    //         $successMessage = 'Payment successful! Your booking is confirmed.';
    //         if ($password) {
    //             $successMessage .= ' Your login credentials have been sent to your email.';
    //         }

    //         return redirect()->route('student.bookings.show', $booking)
    //             ->with('success', $successMessage);

    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         \Log::error('Process successful payment failed: ' . $e->getMessage());
    //         throw $e;
    //     }
    // }

    // ... (keep all your other existing methods)
}

