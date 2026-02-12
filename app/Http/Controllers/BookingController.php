<?php
// app/Http/Controllers/BookingController.php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Hostel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
   public function index()
    {
        $bookings = Booking::where('user_id', Auth::id())->latest()->paginate(10);
        $hostels = Hostel::whereIn('id', $bookings->pluck('hostel_id'))->get()->keyBy('id');
        $fullBookings = $bookings->map(function ($booking) use ($hostels) {
            $booking->hostel = $hostels->get($booking->hostel_id);
            return $booking;
        });
        return view('bookings.index', compact('bookings', 'fullBookings', 'hostels'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'hostel_id' => 'required|exists:hostels,id',
            'check_in_date' => 'required|date|after:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'special_requests' => 'nullable|string|max:500',
        ]);

        $hostel = Hostel::findOrFail($request->hostel_id);

        // Check if hostel has available rooms
        if ($hostel->available_rooms <= 0) {
            return back()->with('error', 'No rooms available in this hostel.');
        }

        // Calculate total amount based on duration
        $nights = now()->parse($request->check_in_date)->diffInDays($request->check_out_date);
        $totalAmount = ($hostel->price_per_semester / 120) * $nights; // Approximate daily rate

        $booking = Booking::create([
            'user_id' => Auth::id(),
            'hostel_id' => $request->hostel_id,
            'room_number' => $this->generateRoomNumber($hostel),
            'check_in_date' => $request->check_in_date,
            'check_out_date' => $request->check_out_date,
            'total_amount' => $totalAmount,
            'special_requests' => $request->special_requests,
        ]);

        // Update hostel available rooms
        $hostel->decrement('available_rooms');

        return redirect()->route('bookings.show', $booking)
            ->with('success', 'Booking created successfully. Please complete payment to confirm.');
    }

    private function generateRoomNumber($hostel)
    {
        // Generate a unique room number for the booking
        $prefix = strtoupper(substr($hostel->name, 0, 2));
        $number = $hostel->total_rooms - $hostel->available_rooms + 1;

        return $prefix . str_pad($number, 3, '0', STR_PAD_LEFT);
    }

    public function show(Booking $booking)
    {
        // Ensure user can only view their own bookings
        if ($booking->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        return view('bookings.show', compact('booking'));
    }

    public function index()
    {
        $bookings = Booking::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('bookings.index', compact('bookings'));
    }
}
