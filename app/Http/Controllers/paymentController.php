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

class PaymentController extends Controller
{
    /**
     * Show booking form for guests
     */
    public function showBookingForm(Request $request, $roomId)
    {
        $room = Room::with('hostel')->findOrFail($roomId);
        
        // Check if room is available
        if (!$room->isAvailable()) {
            return redirect()->route('hostels.show', $room->hostel_id)
                ->with('error', 'This room is no longer available.');
        }

        // Pre-fill data if user is logged in
        $user = Auth::check() ? Auth::user() : null;

        return view('bookings.create', compact('room', 'user'));
    }

    /**
     * Store booking before payment
     */
    public function storeBooking(Request $request)
    {
        $rules = [
            'room_id' => 'required|exists:rooms,id',
            'check_in_date' => 'required|date|after:today',
            'check_out_date' => 'required|date|after:check_in_date',
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
        if (!$this->checkRoomAvailability($room->id, $validated['check_in_date'], $validated['check_out_date'])) {
            return back()->with('error', 'Room is not available for selected dates.');
        }

        // Calculate total amount
        $nights = (new \DateTime($validated['check_in_date']))->diff(new \DateTime($validated['check_out_date']))->days;
        $totalAmount = $room->price_per_month * ($nights / 30); // Convert monthly to daily rate

        // Store booking data in session for after payment
        session(['pending_booking' => [
            'room_id' => $room->id,
            'hostel_id' => $room->hostel_id,
            'check_in_date' => $validated['check_in_date'],
            'check_out_date' => $validated['check_out_date'],
            'total_amount' => $totalAmount,
            'guest_data' => Auth::check() ? null : [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                // Generate random password now but store it temporarily
                'temp_password' => $this->generateRandomPassword(),
            ],
        ]]);

        // Redirect to payment initialization
        return redirect()->route('payment.initialize');
    }

    /**
     * Initialize payment (for both guests and authenticated users)
     */
    public function initializePayment()
    {
        $pendingBooking = session('pending_booking');
        
        if (!$pendingBooking) {
            return redirect()->route('hostels.index')
                ->with('error', 'No booking information found. Please start over.');
        }

        // Determine user email
        if (Auth::check()) {
            $user = Auth::user();
            $email = $user->email;
            $userId = $user->id;
        } else {
            // Guest booking - will create account after payment
            $email = $pendingBooking['guest_data']['email'];
            $userId = null;
        }

        // Calculate fees
        $feePercentage = 0.0195; // 1.95%
        $finalAmount = $pendingBooking['total_amount'] * 1.0195; // Pass fee to customer

        try {
            $reference = Paystack::genTranxRef();
            
            $paymentData = [
                'email' => $email,
                'amount' => round($finalAmount * 100), // Convert to pesewas
                'currency' => 'GHS',
                'reference' => $reference,
                'callback_url' => route('payment.callback'),
                'metadata' => json_encode([
                    'user_id' => $userId,
                    'is_guest' => !Auth::check(),
                    'guest_data' => $pendingBooking['guest_data'],
                    'booking_data' => [
                        'room_id' => $pendingBooking['room_id'],
                        'hostel_id' => $pendingBooking['hostel_id'],
                        'check_in_date' => $pendingBooking['check_in_date'],
                        'check_out_date' => $pendingBooking['check_out_date'],
                        'total_amount' => $pendingBooking['total_amount'],
                    ],
                    'reference' => $reference
                ]),
            ];

            // Store reference in session for verification
            session(['payment_reference' => $reference]);

            return Paystack::getAuthorizationUrl($paymentData)->redirectNow();

        } catch (\Exception $e) {
            \Log::error('Payment initialization failed: ' . $e->getMessage());
            return redirect()->route('hostels.index')
                ->with('error', 'Payment initialization failed. Please try again.');
        }
    }

    /**
     * Handle payment callback
     */
    public function handleCallback(Request $request)
    {
        try {
            $paymentDetails = Paystack::getPaymentData();
            
            if (!$paymentDetails['status'] || $paymentDetails['data']['status'] !== 'success') {
                return redirect()->route('hostels.index')
                    ->with('error', 'Payment was not successful. Please try again.');
            }

            $metadata = json_decode($paymentDetails['data']['metadata'], true);
            $bookingData = $metadata['booking_data'];

            DB::beginTransaction();

            $password = null;
            
            // Create user account if guest
            if ($metadata['is_guest']) {
                // Get the temporary password from metadata
                $tempPassword = $metadata['guest_data']['temp_password'];
                
                $user = User::create([
                    'name' => $metadata['guest_data']['name'],
                    'email' => $metadata['guest_data']['email'],
                    'phone' => $metadata['guest_data']['phone'],
                    'password' => Hash::make($tempPassword), // Hash the password
                    'role' => 'student',
                    'email_verified_at' => now(), // Auto-verify email since they paid
                ]);
                
                $userId = $user->id;
                $password = $tempPassword; // Store plain password for email
                
                // Log the user in
                Auth::login($user);
            } else {
                $userId = $metadata['user_id'];
                $user = User::find($userId);
            }

            // Check room availability one last time
            if (!$this->checkRoomAvailability($bookingData['room_id'], $bookingData['check_in_date'], $bookingData['check_out_date'])) {
                DB::rollBack();
                
                // If guest, we need to delete the created user
                if ($metadata['is_guest'] && isset($user)) {
                    $user->delete();
                }
                
                return redirect()->route('hostels.index')
                    ->with('error', 'Sorry, the room is no longer available. Your payment will be refunded automatically.');
            }

            // Create booking
            $booking = Booking::create([
                'user_id' => $userId,
                'room_id' => $bookingData['room_id'],
                'hostel_id' => $bookingData['hostel_id'],
                'check_in_date' => $bookingData['check_in_date'],
                'check_out_date' => $bookingData['check_out_date'],
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

            return redirect()->route('booking.confirmation', $booking->booking_reference)
                ->with('success', 'Payment successful! Your booking is confirmed.' . 
                    ($password ? ' Your login credentials have been sent to your email.' : ''));

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Payment callback failed: ' . $e->getMessage());
            
            return redirect()->route('hostels.index')
                ->with('error', 'There was an error processing your booking. Please contact support.');
        }
    }

    /**
     * Generate a random secure password
     */
    private function generateRandomPassword($length = 10)
    {
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        $special = '!@#$%^&*()-_=+';
        
        // Ensure at least one of each type
        $password = '';
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $special[random_int(0, strlen($special) - 1)];
        
        // Fill the rest randomly
        $allChars = $uppercase . $lowercase . $numbers . $special;
        for ($i = 4; $i < $length; $i++) {
            $password .= $allChars[random_int(0, strlen($allChars) - 1)];
        }
        
        // Shuffle to mix the guaranteed characters
        return str_shuffle($password);
    }

    /**
     * Send booking confirmation email with login credentials for guests
     */
    private function sendBookingConfirmation($booking, $user, $plainPassword = null)
    {
        try {
            $data = [
                'booking' => $booking,
                'user' => $user,
                'login_url' => route('login'),
            ];

            // If this is a new guest account, include the password
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
     * Check room availability for dates
     */
    private function checkRoomAvailability($roomId, $checkIn, $checkOut)
    {
        $existingBookings = Booking::where('room_id', $roomId)
            ->where('status', 'confirmed')
            ->where(function($query) use ($checkIn, $checkOut) {
                $query->whereBetween('check_in_date', [$checkIn, $checkOut])
                      ->orWhereBetween('check_out_date', [$checkIn, $checkOut])
                      ->orWhere(function($q) use ($checkIn, $checkOut) {
                          $q->where('check_in_date', '<=', $checkIn)
                            ->where('check_out_date', '>=', $checkOut);
                      });
            })
            ->count();

        return $existingBookings === 0;
    }

    /**
     * Show booking confirmation
     */
    public function showConfirmation($reference)
    {
        $booking = Booking::with(['room.hostel', 'user'])
            ->where('booking_reference', $reference)
            ->firstOrFail();

        return view('bookings.confirmation', compact('booking'));
    }

    public function handleRefundWebhook(Request $request)
{
    // Verify webhook signature
    $signature = $request->header('x-paystack-signature');
    $payload = $request->getContent();
    
    if (!$this->verifyPaystackSignature($signature, $payload)) {
        return response()->json(['error' => 'Invalid signature'], 401);
    }

    $event = json_decode($payload, true);

    if ($event['event'] === 'refund.processed') {
        $data = $event['data'];
        
        // Find payment by transaction reference
        $payment = Payment::where('transaction_id', $data['transaction']['reference'])->first();
        
        if ($payment) {
            $payment->update([
                'refund_status' => 'processed',
                'refund_data' => json_encode($data)
            ]);
        }
    }

    return response()->json(['status' => 'success']);
}
}