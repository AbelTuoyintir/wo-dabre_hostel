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

        // Validate gender for logged-in users
        if ($user) {
            $genderError = $this->validateGenderForBooking($room, $user);
            if ($genderError) {
                return redirect()->route('student.hostels.show', $hostel)
                    ->with('error', $genderError);
            }
        }

        return view('bookings.create', compact('hostel', 'room', 'user'));
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
        $rules = [
            'room_id' => 'required|exists:rooms,id',
            'hostel_id' => 'required|exists:hostels,id',
            'check_in_date' => 'required|date|after:today',
            'check_out_date' => 'required|date|after:check_in_date',
        ];

        // Add validation for guests only
        if (!Auth::check()) {
            $rules['name'] = 'required|string|max:255';
            $rules['email'] = 'required|email|unique:users,email,NULL,id';
            $rules['phone'] = 'required|string|max:20';
            $rules['gender'] = 'required|in:male,female'; // Add gender for guests
        }

        // Validate the request
        $validated = $request->validate($rules);

        $room = Room::findOrFail($validated['room_id']);

        // Gender validation for logged-in users
        if (Auth::check()) {
            $user = Auth::user();
            $genderError = $this->validateGenderForBooking($room, $user);
            if ($genderError) {
                return back()->with('error', $genderError);
            }
        }

        // Check availability again
        if (!$this->checkRoomAvailability($room->id, $validated['check_in_date'], $validated['check_out_date'])) {
            return redirect()->route('student.hostels.show', $validated['hostel_id'])
                ->with('error', 'Room is not available for selected dates.');
        }

        // Calculate ONLY the room cost (no student fee here)
        $checkIn = new \DateTime($validated['check_in_date']);
        $checkOut = new \DateTime($validated['check_out_date']);
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
        $guestData = null;
        if (!Auth::check()) {
            $guestData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'gender' => $validated['gender'], // Store gender for guest
                'temp_password' => $this->generateRandomPassword(),
            ];
        }

        session(['pending_booking' => [
            'room_id' => $room->id,
            'hostel_id' => $room->hostel_id,
            'check_in_date' => $validated['check_in_date'],
            'check_out_date' => $validated['check_out_date'],
            'total_amount' => $roomCost,
            'room_gender' => $room->gender, // Store current room gender
            'room_occupancy' => $room->current_occupancy, // Store current occupancy
            'guest_data' => Auth::check() ? null : $guestData,
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
            $guestData = $pendingBooking['guest_data']; // This contains temp_password and gender
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

    // Calculate total amount (room cost + student fee)
    $totalAmount = $validated['room_cost'] + $studentFee;

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

            // Check if this is a guest or student based on metadata
            $isGuest = isset($metadata['is_guest']) && $metadata['is_guest'] === true;

            if ($isGuest) {
                return $this->handleGuestPaymentCallback($paymentDetails, $metadata);
            } else {
                return $this->handleStudentPaymentCallback($paymentDetails, $metadata);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Payment callback failed: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());

            return redirect()->route('student.hostels.browse')
                ->with('error', 'There was an error processing your booking. Please contact support.');
        }
    }

    /**
     * Handle payment callback for guest users (creates new account)
     */
    private function handleGuestPaymentCallback($paymentDetails, $metadata)
    {
        \Log::info('Processing guest payment callback');

        $bookingData = $metadata['booking_data'];

        DB::beginTransaction();

        try {
            // Check if guest data exists
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

            $userId = $user->id;
            $password = $tempPassword;

            // Log the user in
            Auth::login($user);
            \Log::info('Guest user created and logged in:', ['user_id' => $userId, 'gender' => $user->gender]);

            // Process the booking
            return $this->processSuccessfulPayment($paymentDetails, $bookingData, $user, $userId, $password, true);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Guest payment callback failed: ' . $e->getMessage());
            return redirect()->route('student.hostels.browse')
                ->with('error', 'There was an error processing your booking. Please contact support.');
        }
    }

    /**
     * Handle payment callback for authenticated students
     */
    private function handleStudentPaymentCallback($paymentDetails, $metadata)
    {
        \Log::info('Processing student payment callback');

        $bookingData = $metadata['booking_data'];

        DB::beginTransaction();

        try {
            // Get the authenticated user
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

            \Log::info('Authenticated user found:', ['user_id' => $userId, 'gender' => $user->gender]);

            // Process the booking
            return $this->processSuccessfulPayment($paymentDetails, $bookingData, $user, $userId, null, false);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Student payment callback failed: ' . $e->getMessage());
            return redirect()->route('student.hostels.browse')
                ->with('error', 'There was an error processing your booking. Please contact support.');
        }
    }

    /**
     * Common method to process successful payment and create booking
     */
    private function processSuccessfulPayment($paymentDetails, $bookingData, $user, $userId, $password = null, $isGuest = false)
    {
        try {
            // Get the room
            $room = Room::find($bookingData['room_id']);
            if (!$room) {
                DB::rollBack();
                return redirect()->route('student.hostels.browse')
                    ->with('error', 'Room not found. Please contact support.');
            }

            // Check room availability one last time
            if (!$this->checkRoomAvailability($bookingData['room_id'], $bookingData['check_in_date'], $bookingData['check_out_date'])) {
                \Log::warning('Room no longer available:', ['room_id' => $bookingData['room_id']]);

                if ($isGuest && isset($user)) {
                    $user->delete();
                    \Log::info('Deleted guest user due to room unavailability');
                }

                DB::rollBack();
                return redirect()->route('student.hostels.browse')
                    ->with('error', 'Sorry, the room is no longer available. Your payment will be refunded automatically.');
            }

            // ===== GENDER VALIDATION LOGIC =====
            // Check if user has gender set
            if (empty($user->gender)) {
                \Log::error('User has no gender set:', ['user_id' => $user->id]);

                if ($isGuest) {
                    $user->delete();
                }

                DB::rollBack();
                return redirect()->route('student.hostels.browse')
                    ->with('error', 'Gender information is required. Please contact support.');
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

                DB::rollBack();
                return redirect()->route('student.hostels.browse')
                    ->with('error', 'Gender mismatch. This room is for ' . $room->gender . ' students only. Your payment will be refunded.');
            }

            // If room gender is 'any' and this is the first occupant, update room gender
            if ($room->gender === 'any' && $room->current_occupancy === 0) {
                $room->gender = $user->gender;
                $room->save();
                \Log::info('Room gender updated to ' . $user->gender . ' based on first occupant');
            }

            // Calculate total amount
            $totalAmount = (float) ($bookingData['total_amount'] ??
                                     ((float)($bookingData['room_cost'] ?? 0) + (float)($bookingData['student_fee'] ?? 0)));

            // Create booking with unique references
            $bookingReference = 'BK-' . strtoupper(Str::random(8));
            $bookingNumber = 'BN-' . strtoupper(Str::random(8));

            $booking = Booking::create([
                'user_id' => $userId,
                'room_id' => $bookingData['room_id'],
                'hostel_id' => $bookingData['hostel_id'],
                'check_in_date' => $bookingData['check_in_date'],
                'check_out_date' => $bookingData['check_out_date'],
                'total_amount' => $totalAmount,
                'status' => 'confirmed',
                'payment_status' => 'paid',
                'payment_reference' => $paymentDetails['data']['reference'],
                'booking_reference' => $bookingReference,
                'booking_number' => $bookingNumber,
            ]);

            \Log::info('Booking created:', [
                'booking_id' => $booking->id,
                'booking_number' => $booking->booking_number,
                'booking_reference' => $booking->booking_reference
            ]);

            // Create payment record with detailed logging
            try {
                \Log::info('Attempting to create payment record:', [
                    'booking_id' => $booking->id,
                    'user_id' => $userId,
                    'amount' => $totalAmount,
                    'transaction_id' => $paymentDetails['data']['reference'],
                    'payment_method' => $paymentDetails['data']['channel'],
                ]);

                $payment = Payment::create([
                    'user_id' => $userId,
                    'booking_id' => $booking->id,
                    'reference' => $paymentDetails['data']['reference'],
                    'amount' => $totalAmount,
                    'currency' => 'GHS',
                    'transaction_id' => $paymentDetails['data']['reference'],
                    'payment_method' => $paymentDetails['data']['channel'],
                    'status' => 'completed',
                    'paid_at' => now(),
                ]);

                \Log::info('Payment record created successfully:', [
                    'payment_id' => $payment->id,
                    'payment_reference' => $payment->reference,
                ]);
            } catch (\Exception $paymentException) {
                \Log::error('Failed to create payment record:', [
                    'error' => $paymentException->getMessage(),
                    'trace' => $paymentException->getTraceAsString(),
                ]);
                throw $paymentException;
            }

            // Update room occupancy
            if ($room) {
                $room->increment('current_occupancy');
                \Log::info('Room occupancy incremented:', [
                    'room_id' => $room->id,
                    'new_occupancy' => $room->current_occupancy
                ]);
            }

            // Clear session data
            session()->forget(['pending_booking', 'payment_reference']);

            DB::commit();

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

            return redirect()->route('student.bookings.show', $booking)
                ->with('success', $successMessage);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Process successful payment failed: ' . $e->getMessage());
            throw $e;
        }
    }

    // ... (keep all your other existing methods)
}

