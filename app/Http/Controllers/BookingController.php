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
        if ($room->gender === 'any') {
            return true;
        }
        return $user->gender === $room->gender;
    }

    /**
     * Validate gender before allowing booking
     */
    private function validateGenderForBooking(Room $room, ?User $user = null): ?string
    {
        if (!$user) {
            return null;
        }

        if (empty($user->gender)) {
            return 'Please update your profile with your gender before booking.';
        }

        if (!$this->checkGenderCompatibility($user, $room)) {
            return "This room is for {$room->gender} students only. Your gender does not match.";
        }

        return null;
    }

    /**
     * Show booking form for guest users
     */
    public function createBooking(Hostel $hostel, Room $room)
    {
        if ($room->hostel_id !== $hostel->id) {
            abort(404);
        }

        if (!$room->isAvailable()) {
            return redirect()->route('student.hostels.show', $hostel)
                ->with('error', 'This room is no longer available.');
        }

        return view('bookings.createGuess', compact('hostel', 'room'));
    }

    /**
     * Show booking form for authenticated student users
     */
    public function StudentCreateBooking(Hostel $hostel, Room $room)
    {
        if ($room->hostel_id !== $hostel->id) {
            abort(404);
        }

        if (!$room->isAvailable()) {
            return redirect()->route('student.hostels.show', $hostel)
                ->with('error', 'This room is no longer available.');
        }

        $user = Auth::user();

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
     * Store booking for guest users
     */
    public function store(Request $request)
    {
        try {
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

            // Check availability
            $isAvailable = $this->checkRoomAvailability($room->id, $validated['check_in_date'], $validated['check_out_date']);
            \log::info('room avialability checked');
            
            if (!$isAvailable) {
                \Log::warning('Room not available');
                return redirect()->route('hostels.guest.show', $validated['hostel_id'])
                    ->with('error', 'Room is no longer available for selected dates.');
            }

            // Calculate nights
            $checkIn = Carbon::parse($validated['check_in_date']);
            $checkOut = Carbon::parse($validated['check_out_date']);
            $nights = $checkIn->diffInDays($checkOut);
            
            \Log::info('Date calculation', ['nights' => $nights]);

            // Charges
            $agentFee = config('app.student_fee_amount', 150); 
            $systemCharge = 20; // ₵20
            $roomCost = $validated['room_cost'];
            $subTotal = $roomCost + $agentFee + $systemCharge;
            $paystackFee = $subTotal * 0.021; 
            $finalTotal = $subTotal + $paystackFee;

            \Log::info('Final calculation', ['final_total' => $finalTotal]);

            // Generate random password for guest
            $tempPassword = $this->generateRandomPassword();

            // Store booking data in session for after payment
            $pendingBooking = [
                'room_id' => $room->id,
                'hostel_id' => $room->hostel_id,
                'check_in_date' => $validated['check_in_date'],
                'check_out_date' => $validated['check_out_date'],
                'room_cost' => $roomCost,
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
            \Log::info('Pending booking stored in session');

            // Initialize payment
            return $this->initializeGuestPayment();
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::warning('Validation failed', ['errors' => $e->errors()]);
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Unexpected error in store method: ' . $e->getMessage());
            return redirect()->route('hostels.guest.show', $validated['hostel_id'] ?? '')
                ->with('error', 'An unexpected error occurred. Please try again or contact support.');
        }
    }

    /**
     * Store booking for authenticated students
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

    $validated = $request->validate($rules);

    if (!Auth::check()) {
        return redirect()->route('login')->with('error', 'Please login to continue.');
    }

    $user = Auth::user();
    $room = Room::findOrFail($validated['room_id']);

    if (empty($user->gender)) {
        return redirect()->route('student.profile')
            ->with('error', 'Please update your profile with your gender before booking.');
    }

    $genderError = $this->validateGenderForBooking($room, $user);
    if ($genderError) {
        return redirect()->route('student.hostels.show', $validated['hostel_id'])
            ->with('error', $genderError);
    }

    if (!$this->checkRoomAvailability($room->id, $validated['check_in_date'], $validated['check_out_date'])) {
        return redirect()->route('student.hostels.show', $validated['hostel_id'])
            ->with('error', 'Room is not available for selected dates.');
    }

    // Calculate total amount - ONLY room cost (no fees)
    $totalAmount = $validated['room_cost']; // No student fee, no system charge

    \Log::info('Student booking - Cost calculation:', [
        'user_id' => $user->id,
        'room_id' => $room->id,
        'total_amount' => $totalAmount
    ]);

    session(['pending_booking' => [
        'user_id' => $user->id,
        'room_id' => $room->id,
        'hostel_id' => $room->hostel_id,
        'check_in_date' => $validated['check_in_date'],
        'check_out_date' => $validated['check_out_date'],
        'room_cost' => $validated['room_cost'],
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

    return $this->initializeStudentPayment();
}
    /**
     * Initialize Paystack payment for guest
     */
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
        $amountInPesewas = (int) round($finalTotal * 100);

        try {
            $reference = 'BK-' . strtoupper(Str::random(12));

            $paymentData = [
                'email' => $email,
                'amount' => $amountInPesewas,
                'currency' => 'GHS',
                'reference' => $reference,
                'callback_url' => route('bookings.payment.callback', ['gateway' => 'paystack']),
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

            \Log::info('Guest payment details:', $paymentData);

            session(['payment_reference' => $reference]);

            return Paystack::getAuthorizationUrl($paymentData)->redirectNow();

        } catch (\Exception $e) {
            \Log::error('Guest payment initialization failed: ' . $e->getMessage());
            return redirect()->route('hostels.index')
                ->with('error', 'Payment initialization failed. Please try again.');
        }
    }

    /**
     * Initialize Paystack payment for students
     */
    private function initializeStudentPayment()
    {
        $pendingBooking = session('pending_booking');

        if (!$pendingBooking) {
            return redirect()->route('student.hostels.browse')
                ->with('error', 'No booking information found. Please start over.');
        }

        $user = Auth::user();
        $totalAmount = $pendingBooking['total_amount'];
        $feePercentage = 0.02;
        $feeAmount = $totalAmount * $feePercentage;
        $finalAmount = $totalAmount + $feeAmount;
        $amountInPesewas = (int) round($finalAmount * 100);

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
                        'system_charge' => $pendingBooking['system_charge'],
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
     * Handle payment callback for ALL users
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

            $metadata = $paymentDetails['data']['metadata'] ?? null;

            \Log::info('Raw metadata received:', [
                'metadata' => $metadata,
                'metadata_type' => gettype($metadata)
            ]);

            if (!is_array($metadata)) {
                \Log::error('Metadata is not an array', ['metadata' => $metadata]);
                return redirect()->route('student.hostels.browse')
                    ->with('error', 'Invalid payment data format. Please contact support.');
            }

            if (!isset($metadata['booking_data'])) {
                \Log::error('Missing booking_data in metadata', ['metadata' => $metadata]);
                return redirect()->route('student.hostels.browse')
                    ->with('error', 'Invalid booking data. Please contact support.');
            }

            // Check the is_guest flag - it might be stored as string "true"/"false" instead of boolean
            $isGuest = false;
            if (isset($metadata['is_guest'])) {
                // Handle both boolean and string representations
                if (is_bool($metadata['is_guest'])) {
                    $isGuest = $metadata['is_guest'];
                } else if (is_string($metadata['is_guest'])) {
                    $isGuest = $metadata['is_guest'] === 'true' || $metadata['is_guest'] === '1';
                } else if (is_numeric($metadata['is_guest'])) {
                    $isGuest = (int)$metadata['is_guest'] === 1;
                }
            }
            
            \Log::info('Payment type determined', [
                'is_guest_raw' => $metadata['is_guest'] ?? null,
                'is_guest_parsed' => $isGuest,
                'has_user_id' => isset($metadata['user_id']),
                'user_id' => $metadata['user_id'] ?? null,
                'has_guest_data' => isset($metadata['guest_data'])
            ]);

            DB::beginTransaction();

            try {
                if ($isGuest) {
                    \Log::info('Routing to guest payment processor');
                    $result = $this->processGuestPayment($paymentDetails, $metadata);
                } else {
                    \Log::info('Routing to student payment processor');
                    // For students, verify user_id exists
                    if (!isset($metadata['user_id'])) {
                        \Log::error('Student payment missing user_id', ['metadata' => $metadata]);
                        throw new \Exception('Missing user ID for student payment');
                    }
                    $result = $this->processStudentPayment($paymentDetails, $metadata);
                }

                DB::commit();
                return $result;

            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Payment processing failed: ' . $e->getMessage());
                throw $e;
            }

        } catch (\Exception $e) {
            \Log::error('Payment callback failed: ' . $e->getMessage());
            return redirect()->route('student.hostels.browse')
                ->with('error', 'There was an error processing your booking. Please contact support.');
        }
    }

    /**
     * Process payment for guest users
     */
    private function processGuestPayment($paymentDetails, $metadata)
    {
        try{
            \Log::info('Processing guest payment callback');

        $bookingData = $metadata['booking_data'];

        if (!isset($metadata['guest_data']) || !is_array($metadata['guest_data'])) {
            \Log::error('Invalid guest_data', ['metadata' => $metadata]);
            throw new \Exception('Invalid guest data');
        }

        $guestData = $metadata['guest_data'];

        if (!isset($guestData['temp_password'])) {
            \Log::error('Missing temp_password in guest_data', ['guestData' => $guestData]);
            throw new \Exception('Missing temporary password');
        }

        $tempPassword = $guestData['temp_password'];
        if ($tempPassword){
            \Log::info('temporal password have been created. now guest data will now be 
            created in the database ');
        }


        // Create the user
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


        \Log::info('Guest user created:', ['user_id' => $user->id, 'email' => $user->email,'gender'=>$user->gender]);

        return $this->finalizeBooking($paymentDetails, $bookingData, $user, $password, true);
        }catch(Execption $e){
            \log::error('guest payment process could not be completed', $e->getMessage());
        }
        
    }

    /**
     * Process payment for authenticated students
     */
    private function processStudentPayment($paymentDetails, $metadata)
    {
        \Log::info('Processing student payment callback');

        $bookingData = $metadata['booking_data'];

        $userId = $metadata['user_id'] ?? null;
        if (!$userId) {
            \Log::error('Missing user_id for authenticated user', ['metadata' => $metadata]);
            throw new \Exception('Missing user ID');
        }

        $user = User::find($userId);
        if (!$user) {
            \Log::error('User not found', ['user_id' => $userId]);
            throw new \Exception('User not found');
        }

        \Log::info('Authenticated user found:', ['user_id' => $userId, 'email' => $user->email]);

        return $this->finalizeBooking($paymentDetails, $bookingData, $user, null, false);
    }

    /**
     * Finalize booking after successful payment
     */
    private function finalizeBooking($paymentDetails, $bookingData, $user, $password = null, $isGuest = false)
    {
        $room = Room::find($bookingData['room_id']);
        if (!$room) {
            throw new \Exception('Room not found');
        }

        // Check room availability one last time
        if (!$this->checkRoomAvailability($bookingData['room_id'], $bookingData['check_in_date'], $bookingData['check_out_date'])) {
            \Log::warning('Room no longer available');

            if ($isGuest) {
                $user->delete();
            }

            throw new \Exception('Room no longer available');
        }

        // Gender validation
        if (empty($user->gender)) {
            \Log::error('User has no gender set');

            if ($isGuest) {
                $user->delete();
            }

            throw new \Exception('Gender information is required');
        }

        // Check gender compatibility
        if ($room->gender !== 'any' && $user->gender !== $room->gender) {
            \Log::error('Gender mismatch after payment', [
                'user_gender' => $user->gender,
                'room_gender' => $room->gender
            ]);

            if ($isGuest) {
                $user->delete();
            }

            throw new \Exception('Gender mismatch. This room is for ' . $room->gender . ' students only.');
        }

        // If room gender is 'any' and this is the first occupant, update room gender
        if ($room->gender === 'any' && ($room->current_occupancy ?? 0) === 0) {
            $room->gender = $user->gender;
            $room->save();
            \Log::info('Room gender updated to ' . $user->gender);
        }

        // Calculate total amount - WITHOUT system charge
        $totalAmount = (float) ($bookingData['final_total'] ?? 
                            (((float)($bookingData['room_cost'] ?? 0) + 
                                (float)($bookingData['agent_fee'] ?? 150))));
        // NOTE: system_charge is NOT included in the total amount

        // Create booking number
        $bookingNumber = 'BN-' . strtoupper(Str::random(8));

        // Prepare booking data
        $bookingDataArray = [
            'booking_number' => $bookingNumber,
            'user_id' => $user->id,
            'room_id' => $bookingData['room_id'],
            'hostel_id' => $bookingData['hostel_id'],
            'check_in_date' => $bookingData['check_in_date'],
            'check_out_date' => $bookingData['check_out_date'],
            'total_amount' => $totalAmount,
            'amount_paid' => $totalAmount,
            'payment_status' => 'paid',
            'booking_status' => 'confirmed',
            'payment_method' => $paymentDetails['data']['channel'] ?? null,
            'transaction_id' => $paymentDetails['data']['reference'] ?? null,
            'payment_date' => now(),
        ];

        $booking = Booking::create($bookingDataArray);

        \Log::info('Booking created:', ['booking_id' => $booking->id]);

        // Create payment record
        Payment::create([
            'user_id' => $user->id,
            'booking_id' => $booking->id,
            'reference' => $paymentDetails['data']['reference'] ?? ('PAY-' . strtoupper(Str::random(10))),
            'amount' => $totalAmount,
            'currency' => $paymentDetails['data']['currency'] ?? 'GHS',
            'transaction_id' => $paymentDetails['data']['reference'] ?? null,
            'payment_method' => $paymentDetails['data']['channel'] ?? null,
            'status' => 'completed',
            'paid_at' => now(),
        ]);

        \Log::info('Payment record created');

        // Update room occupancy
        $currentOccupancy = $room->current_occupancy ?? 0;
        $room->current_occupancy = $currentOccupancy + 1;
        $room->save();
        
        \Log::info('Room occupancy updated', [
            'old_occupancy' => $currentOccupancy,
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
    }

    /**
     * List user bookings
     */
    public function index()
    {
        $user = Auth::user();
        $bookings = Booking::where('user_id', $user->id)
            ->with(['room.hostel.primaryImage', 'hostel.primaryImage'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('student.bookings.index', compact('bookings'));
    }

    /**
     * Show specific booking
     */
    public function show(Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        $booking->load(['room.hostel.primaryImage', 'hostel.primaryImage', 'payment']);

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

        if ($booking->booking_status === 'cancelled') {
            return back()->with('error', 'Booking is already cancelled.');
        }

        if ($booking->check_in_date->isPast()) {
            return back()->with('error', 'Cannot cancel a past booking.');
        }

        DB::transaction(function() use ($booking) {
            $booking->update([
                'booking_status' => 'cancelled',
                'payment_status' => 'refund_pending'
            ]);

            $room = $booking->room;
            if ($room) {
                $room->decrement('current_occupancy');
            }
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
            ->get(['id', 'number', 'capacity', 'room_cost', 'gender']);

        return response()->json($rooms);
    }

    /**
     * Calculate booking totals
     */
    public function calculate(Request $request)
    {
        $validated = $request->validate([
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'room_id' => 'required|exists:rooms,id',
            'room_cost' => 'nullable|numeric',
        ]);

        $checkIn = Carbon::parse($validated['check_in_date']);
        $checkOut = Carbon::parse($validated['check_out_date']);
        $nights = $checkIn->diffInDays($checkOut);

        $roomCost = $validated['room_cost'] ?? Room::find($validated['room_id'])->room_cost ?? 0;

        $agentFee = config('app.agent_fee', 150);
        $systemCharge = 20;

        $subTotal = $roomCost + $agentFee + $systemCharge;
        $paystackFee = $subTotal * 0.0195;
        $finalTotal = $subTotal + $paystackFee;

        return response()->json([
            'success' => true,
            'nights' => $nights,
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
            ->where('booking_status', 'confirmed')
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
}