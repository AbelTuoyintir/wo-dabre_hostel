<?php

namespace App\Http\Controllers;

use App\Models\Hostel;
use App\Models\Room;
use App\Models\Booking;
use App\Models\Complaint;
use App\Models\User;
use App\Models\Payment;
use App\Models\MaintenanceRequest;
use App\Models\Inventory;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class HostelManagementController extends Controller
{
    /**
     * Constructor to ensure only hostel managers can access
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('hostel.manager');
    }

    /**
     * ===========================================
     * DASHBOARD & ANALYTICS
     * ===========================================
     */

    /**
     * Main dashboard with comprehensive analytics
     */
    public function dashboard()
    {
        $user = Auth::user();
        $managedHostels = $user->managedHostels()->withCount(['rooms', 'bookings'])->get();
        $hostelIds = $managedHostels->pluck('id');

        // ===== LIVE STATISTICS =====
        $currentTime = now();

        // Live room status
        $roomStats = [
            'total_rooms' => Room::whereIn('hostel_id', $hostelIds)->count(),
            'available_rooms' => Room::whereIn('hostel_id', $hostelIds)
                ->where('status', 'available')
                ->whereColumn('current_occupancy', '<', 'capacity')
                ->count(),
            'occupied_rooms' => Room::whereIn('hostel_id', $hostelIds)
                ->where('current_occupancy', '>', 0)
                ->count(),
            'maintenance_rooms' => Room::whereIn('hostel_id', $hostelIds)
                ->where('status', 'maintenance')
                ->count(),
            'total_capacity' => Room::whereIn('hostel_id', $hostelIds)->sum('capacity'),
            'current_occupancy' => Room::whereIn('hostel_id', $hostelIds)->sum('current_occupancy'),
        ];

        $roomStats['occupancy_rate'] = $roomStats['total_capacity'] > 0
            ? round(($roomStats['current_occupancy'] / $roomStats['total_capacity']) * 100, 2)
            : 0;

        // Live bookings status
        $bookingStats = [
            'total_bookings' => Booking::whereIn('hostel_id', $hostelIds)->count(),
            'pending_bookings' => Booking::whereIn('hostel_id', $hostelIds)
                ->where('status', 'pending')
                ->count(),
            'confirmed_bookings' => Booking::whereIn('hostel_id', $hostelIds)
                ->where('status', 'confirmed')
                ->count(),
            'cancelled_bookings' => Booking::whereIn('hostel_id', $hostelIds)
                ->where('status', 'cancelled')
                ->count(),
            'completed_bookings' => Booking::whereIn('hostel_id', $hostelIds)
                ->where('status', 'completed')
                ->count(),
            'today_checkins' => Booking::whereIn('hostel_id', $hostelIds)
                ->whereDate('check_in', $currentTime->toDateString())
                ->where('status', 'confirmed')
                ->count(),
            'today_checkouts' => Booking::whereIn('hostel_id', $hostelIds)
                ->whereDate('check_out', $currentTime->toDateString())
                ->where('status', 'confirmed')
                ->count(),
            'active_bookings' => Booking::whereIn('hostel_id', $hostelIds)
                ->where('status', 'confirmed')
                ->where('check_in', '<=', $currentTime)
                ->where('check_out', '>=', $currentTime)
                ->count(),
        ];

        // ===== REVENUE ANALYTICS =====
        $revenueStats = [
            'today' => Payment::whereHas('booking', function($q) use ($hostelIds) {
                    $q->whereIn('hostel_id', $hostelIds);
                })
                ->whereDate('created_at', $currentTime->toDateString())
                ->where('status', 'completed')
                ->sum('amount'),

            'this_week' => Payment::whereHas('booking', function($q) use ($hostelIds) {
                    $q->whereIn('hostel_id', $hostelIds);
                })
                ->whereBetween('created_at', [$currentTime->startOfWeek(), $currentTime->copy()->endOfWeek()])
                ->where('status', 'completed')
                ->sum('amount'),

            'this_month' => Payment::whereHas('booking', function($q) use ($hostelIds) {
                    $q->whereIn('hostel_id', $hostelIds);
                })
                ->whereMonth('created_at', $currentTime->month)
                ->whereYear('created_at', $currentTime->year)
                ->where('status', 'completed')
                ->sum('amount'),

            'this_year' => Payment::whereHas('booking', function($q) use ($hostelIds) {
                    $q->whereIn('hostel_id', $hostelIds);
                })
                ->whereYear('created_at', $currentTime->year)
                ->where('status', 'completed')
                ->sum('amount'),

            'total' => Payment::whereHas('booking', function($q) use ($hostelIds) {
                    $q->whereIn('hostel_id', $hostelIds);
                })
                ->where('status', 'completed')
                ->sum('amount'),

            'pending_payments' => Payment::whereHas('booking', function($q) use ($hostelIds) {
                    $q->whereIn('hostel_id', $hostelIds);
                })
                ->where('status', 'pending')
                ->sum('amount'),
        ];

        // ===== ROOM SPACE ANALYTICS =====
        $rooms = Room::whereIn('hostel_id', $hostelIds)->get();
        $roomSpaceStats = [];
        foreach ($rooms as $room) {
            $roomSpaceStats[] = [
                'room_id' => $room->id,
                'room_number' => $room->number,
                'hostel' => $room->hostel->name,
                'capacity' => $room->capacity,
                'occupied' => $room->current_occupancy,
                'available_spaces' => $room->availableSpaces(),
                'occupancy_percentage' => $room->occupancyRate(),
                'status' => $room->status,
                'gender' => $room->gender,
                'price' => $room->price_per_month,
            ];
        }

        // ===== COMPLAINT STATISTICS =====
        $complaintStats = [
            'total' => Complaint::whereIn('hostel_id', $hostelIds)->count(),
            'pending' => Complaint::whereIn('hostel_id', $hostelIds)
                ->where('status', 'pending')
                ->count(),
            'in_progress' => Complaint::whereIn('hostel_id', $hostelIds)
                ->where('status', 'in_progress')
                ->count(),
            'resolved' => Complaint::whereIn('hostel_id', $hostelIds)
                ->where('status', 'resolved')
                ->count(),
            'urgent' => Complaint::whereIn('hostel_id', $hostelIds)
                ->where('priority', 'urgent')
                ->whereIn('status', ['pending', 'in_progress'])
                ->count(),
        ];

        // ===== OCCUPANT STATISTICS =====
        $occupantStats = [
            'total_students' => User::whereHas('bookings', function($q) use ($hostelIds) {
                    $q->whereIn('hostel_id', $hostelIds)
                      ->whereIn('status', ['confirmed', 'pending']);
                })->count(),

            'male_students' => User::whereHas('bookings', function($q) use ($hostelIds) {
                    $q->whereIn('hostel_id', $hostelIds)
                      ->whereIn('status', ['confirmed', 'pending']);
                })->where('gender', 'male')->count(),

            'female_students' => User::whereHas('bookings', function($q) use ($hostelIds) {
                    $q->whereIn('hostel_id', $hostelIds)
                      ->whereIn('status', ['confirmed', 'pending']);
                })->where('gender', 'female')->count(),

            'new_this_month' => User::whereHas('bookings', function($q) use ($hostelIds) {
                    $q->whereIn('hostel_id', $hostelIds)
                      ->whereIn('status', ['confirmed', 'pending']);
                })->whereMonth('created_at', $currentTime->month)
                ->whereYear('created_at', $currentTime->year)
                ->count(),

            'leaving_this_month' => Booking::whereIn('hostel_id', $hostelIds)
                ->where('status', 'confirmed')
                ->whereMonth('check_out', $currentTime->month)
                ->whereYear('check_out', $currentTime->year)
                ->count(),
        ];

        // ===== MAINTENANCE STATISTICS =====
        $maintenanceStats = [
            'total' => MaintenanceRequest::whereIn('hostel_id', $hostelIds)->count(),
            'pending' => MaintenanceRequest::whereIn('hostel_id', $hostelIds)
                ->where('status', 'pending')
                ->count(),
            'in_progress' => MaintenanceRequest::whereIn('hostel_id', $hostelIds)
                ->where('status', 'in_progress')
                ->count(),
            'completed' => MaintenanceRequest::whereIn('hostel_id', $hostelIds)
                ->where('status', 'completed')
                ->count(),
        ];

        // ===== CHARTS DATA =====
        // Monthly booking trends
        $monthlyBookings = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = $currentTime->copy()->subMonths($i);
            $monthlyBookings[] = [
                'month' => $month->format('M Y'),
                'bookings' => Booking::whereIn('hostel_id', $hostelIds)
                    ->whereMonth('created_at', $month->month)
                    ->whereYear('created_at', $month->year)
                    ->count(),
            ];
        }

        // Revenue by hostel
        $revenueByHostel = [];
        foreach ($managedHostels as $hostel) {
            $revenueByHostel[] = [
                'hostel' => $hostel->name,
                'revenue' => Payment::whereHas('booking', function($q) use ($hostel) {
                        $q->where('hostel_id', $hostel->id);
                    })
                    ->where('status', 'completed')
                    ->sum('amount'),
            ];
        }

        // Room occupancy by type
        $occupancyByType = [
            'single' => Room::whereIn('hostel_id', $hostelIds)
                ->where('capacity', 1)
                ->count(),
            'double' => Room::whereIn('hostel_id', $hostelIds)
                ->where('capacity', 2)
                ->count(),
            'triple' => Room::whereIn('hostel_id', $hostelIds)
                ->where('capacity', 3)
                ->count(),
            'dormitory' => Room::whereIn('hostel_id', $hostelIds)
                ->where('capacity', '>=', 4)
                ->count(),
        ];

        // ===== RECENT ACTIVITIES =====
        $recentActivities = collect();

        // Recent bookings
        $recentBookings = Booking::whereIn('hostel_id', $hostelIds)
            ->with(['user', 'room'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(function($booking) {
                return [
                    'type' => 'booking',
                    'title' => 'New Booking',
                    'description' => $booking->user->name . ' booked room ' . $booking->room->number,
                    'time' => $booking->created_at->diffForHumans(),
                    'status' => $booking->status,
                    'icon' => 'calendar-check',
                    'color' => 'blue'
                ];
            });

        // Recent complaints
        $recentComplaints = Complaint::whereIn('hostel_id', $hostelIds)
            ->with(['user'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(function($complaint) {
                return [
                    'type' => 'complaint',
                    'title' => 'New Complaint',
                    'description' => $complaint->title . ' - ' . $complaint->user->name,
                    'time' => $complaint->created_at->diffForHumans(),
                    'status' => $complaint->status,
                    'icon' => 'exclamation-triangle',
                    'color' => 'red'
                ];
            });

        // Recent payments
        $recentPayments = Payment::whereHas('booking', function($q) use ($hostelIds) {
                $q->whereIn('hostel_id', $hostelIds);
            })
            ->with(['booking.user'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(function($payment) {
                return [
                    'type' => 'payment',
                    'title' => 'Payment Received',
                    'description' => $payment->booking->user->name . ' paid ₵' . number_format($payment->amount, 2),
                    'time' => $payment->created_at->diffForHumans(),
                    'status' => $payment->status,
                    'icon' => 'money-bill',
                    'color' => 'green'
                ];
            });

        $recentActivities = $recentBookings->concat($recentComplaints)->concat($recentPayments)->sortByDesc('time')->take(10);

        return view('hostel-manager.dashboard', compact(
            'managedHostels',
            'roomStats',
            'bookingStats',
            'revenueStats',
            'roomSpaceStats',
            'complaintStats',
            'occupantStats',
            'maintenanceStats',
            'monthlyBookings',
            'revenueByHostel',
            'occupancyByType',
            'recentActivities'
        ));
    }

    /**
     * ===========================================
     * ROOM MANAGEMENT
     * ===========================================
     */

    /**
     * Display all rooms with live availability
     */

    /**
     * Show room creation form
     */
    public function createRoom()
    {
        $user = Auth::user();
        $hostels = $user->managedHostels()->get();

        return view('hostel-manager.rooms.create', compact('hostels'));
    }

    /**
     * Store new room
     */
    public function storeRoom(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'hostel_id' => 'required|exists:hostels,id',
            'number' => 'required|string|max:50',
            'floor' => 'nullable|integer|min:0',
            'capacity' => 'required|integer|min:1',
            'price_per_month' => 'required|numeric|min:0',
            'gender' => 'required|in:male,female,any',
            'status' => 'required|in:available,occupied,maintenance',
            'description' => 'nullable|string',
            'size_sqm' => 'nullable|numeric|min:1',
            'furnished' => 'boolean',
            'private_bathroom' => 'boolean',
        ]);

        // Verify manager owns this hostel
        if (!$user->managedHostels()->where('hostels.id', $request->hostel_id)->exists()) {
            abort(403, 'You do not have permission to add rooms to this hostel.');
        }

        // Check for duplicate room number
        $exists = Room::where('hostel_id', $request->hostel_id)
            ->where('number', $request->number)
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error', 'Room number already exists in this hostel.');
        }

        $room = Room::create([
            'hostel_id' => $request->hostel_id,
            'number' => $request->number,
            'floor' => $request->floor,
            'capacity' => $request->capacity,
            'price_per_month' => $request->price_per_month,
            'gender' => $request->gender,
            'status' => $request->status,
            'description' => $request->description,
            'size_sqm' => $request->size_sqm,
            'furnished' => $request->boolean('furnished'),
            'private_bathroom' => $request->boolean('private_bathroom'),
            'current_occupancy' => 0,
        ]);

        // Update hostel room counts
        $this->updateHostelRoomCounts($room->hostel);

        return redirect()->route('hostel-manager.rooms')->with('success', 'Room created successfully.');
    }

    /**
     * Show room details with live occupancy
     */
    public function showRoom(Room $room)
    {
        $user = Auth::user();

        // Verify manager owns this room's hostel
        if (!$user->managedHostels()->where('hostels.id', $room->hostel_id)->exists()) {
            abort(403);
        }

        $room->load(['hostel', 'bookings' => function($q) {
            $q->with('user')
              ->latest()
              ->limit(10);
        }]);

        // Current occupants
        $currentOccupants = User::whereHas('bookings', function($q) use ($room) {
                $q->where('room_id', $room->id)
                  ->where('status', 'confirmed')
                  ->where('check_in', '<=', now())
                  ->where('check_out', '>=', now());
            })->get();

        // Upcoming bookings
        $upcomingBookings = Booking::where('room_id', $room->id)
            ->where('status', 'confirmed')
            ->where('check_in', '>', now())
            ->with('user')
            ->get();

        // Payment history for this room
        $payments = Payment::whereHas('booking', function($q) use ($room) {
                $q->where('room_id', $room->id);
            })
            ->with('booking.user')
            ->latest()
            ->limit(10)
            ->get();

        return view('hostel-manager.rooms.show', compact('room', 'currentOccupants', 'upcomingBookings', 'payments'));
    }

    /**
     * Show room edit form
     */
    public function editRoom(Room $room)
    {
        $user = Auth::user();

        if (!$user->managedHostels()->where('hostels.id', $room->hostel_id)->exists()) {
            abort(403);
        }

        return view('hostel-manager.rooms.edit', compact('room'));
    }

    /**
     * Update room
     */
    public function updateRoom(Request $request, Room $room)
    {
        $user = Auth::user();

        if (!$user->managedHostels()->where('hostels.id', $room->hostel_id)->exists()) {
            abort(403);
        }

        $request->validate([
            'number' => 'required|string|max:50',
            'floor' => 'nullable|integer|min:0',
            'capacity' => 'required|integer|min:1',
            'price_per_month' => 'required|numeric|min:0',
            'gender' => 'required|in:male,female,any',
            'status' => 'required|in:available,occupied,maintenance',
            'description' => 'nullable|string',
            'size_sqm' => 'nullable|numeric|min:1',
            'furnished' => 'boolean',
            'private_bathroom' => 'boolean',
        ]);

        // Check for duplicate room number in same hostel
        $exists = Room::where('hostel_id', $room->hostel_id)
            ->where('number', $request->number)
            ->where('id', '!=', $room->id)
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error', 'Room number already exists in this hostel.');
        }

        $oldCapacity = $room->capacity;

        $room->update([
            'number' => $request->number,
            'floor' => $request->floor,
            'capacity' => $request->capacity,
            'price_per_month' => $request->price_per_month,
            'gender' => $request->gender,
            'status' => $request->status,
            'description' => $request->description,
            'size_sqm' => $request->size_sqm,
            'furnished' => $request->boolean('furnished'),
            'private_bathroom' => $request->boolean('private_bathroom'),
        ]);

        // If capacity changed, update current_occupancy if needed
        if ($oldCapacity != $request->capacity && $room->current_occupancy > $request->capacity) {
            $room->update(['current_occupancy' => $request->capacity]);
        }

        // Update hostel room counts
        $this->updateHostelRoomCounts($room->hostel);

        return redirect()->route('hostel-manager.rooms.show', $room)->with('success', 'Room updated successfully.');
    }

    /**
     * Delete room
     */
    public function destroyRoom(Room $room)
    {
        $user = Auth::user();

        if (!$user->managedHostels()->where('hostels.id', $room->hostel_id)->exists()) {
            abort(403);
        }

        // Check for active bookings
        $hasActiveBookings = $room->bookings()
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($hasActiveBookings) {
            return back()->with('error', 'Cannot delete room with active bookings.');
        }

        $hostel = $room->hostel;
        $room->delete();

        // Update hostel room counts
        $this->updateHostelRoomCounts($hostel);

        return redirect()->route('hostel-manager.rooms')->with('success', 'Room deleted successfully.');
    }

    /**
     * Update room status via AJAX
     */
    public function updateRoomStatus(Request $request, Room $room)
    {
        $user = Auth::user();

        if (!$user->managedHostels()->where('hostels.id', $room->hostel_id)->exists()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'status' => 'required|in:available,occupied,maintenance'
        ]);

        $room->update(['status' => $request->status]);

        // Update hostel room counts
        $this->updateHostelRoomCounts($room->hostel);

        return response()->json([
            'success' => true,
            'message' => 'Room status updated successfully',
            'room' => $room
        ]);
    }

    /**
     * Get live room availability
     */
    public function getLiveRoomAvailability(Request $request)
    {
        $user = Auth::user();
        $hostelIds = $user->managedHostels()->pluck('hostels.id');

        $rooms = Room::whereIn('hostel_id', $hostelIds)
            ->with('hostel')
            ->get()
            ->map(function($room) {
                return [
                    'id' => $room->id,
                    'number' => $room->number,
                    'hostel' => $room->hostel->name,
                    'capacity' => $room->capacity,
                    'occupied' => $room->current_occupancy,
                    'available' => $room->availableSpaces(),
                    'status' => $room->status,
                    'gender' => $room->gender,
                    'price' => $room->price_per_month,
                    'occupancy_rate' => $room->occupancyRate(),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $rooms,
            'timestamp' => now()->toDateTimeString()
        ]);
    }

    /**
     * ===========================================
     * BOOKING MANAGEMENT
     * ===========================================
     */

    /**
     * Display all bookings with filters
     */
    public function bookings(Request $request)
    {
        $user = Auth::user();
        $hostelIds = $user->managedHostels()->pluck('hostels.id');

        $query = Booking::whereIn('hostel_id', $hostelIds)
            ->with(['user', 'room', 'hostel']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('hostel_id')) {
            $query->where('hostel_id', $request->hostel_id);
        }

        if ($request->filled('date_from')) {
            $query->where('check_in', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('check_out', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('student_id', 'like', '%' . $request->search . '%');
            });
        }

        $bookings = $query->latest()->paginate(15)->withQueryString();

        // Summary statistics
        $summary = [
            'total' => Booking::whereIn('hostel_id', $hostelIds)->count(),
            'pending' => Booking::whereIn('hostel_id', $hostelIds)->where('status', 'pending')->count(),
            'confirmed' => Booking::whereIn('hostel_id', $hostelIds)->where('status', 'confirmed')->count(),
            'cancelled' => Booking::whereIn('hostel_id', $hostelIds)->where('status', 'cancelled')->count(),
            'completed' => Booking::whereIn('hostel_id', $hostelIds)->where('status', 'completed')->count(),
            'today_checkins' => Booking::whereIn('hostel_id', $hostelIds)
                ->whereDate('check_in', now()->toDateString())
                ->where('status', 'confirmed')
                ->count(),
        ];

        $hostels = $user->managedHostels()->get();

        return view('hostel-manager.bookings.index', compact('bookings', 'hostels', 'summary'));
    }

    /**
     * Show booking details
     */
    public function showBooking(Booking $booking)
    {
        $user = Auth::user();

        if (!$user->managedHostels()->where('hostels.id', $booking->hostel_id)->exists()) {
            abort(403);
        }

        $booking->load(['user', 'room', 'hostel', 'payments']);

        return view('hostel-manager.bookings.show', compact('booking'));
    }

    /**
     * Update booking status
     */
    public function updateBookingStatus(Request $request, Booking $booking)
    {
        $user = Auth::user();

        if (!$user->managedHostels()->where('hostels.id', $booking->hostel_id)->exists()) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled,completed'
        ]);

        $oldStatus = $booking->status;
        $booking->update(['status' => $request->status]);

        // If booking is cancelled, free up room space
        if ($request->status == 'cancelled' && $oldStatus == 'confirmed') {
            $room = $booking->room;
            if ($room) {
                $room->decrement('current_occupancy');
                $this->updateHostelRoomCounts($room->hostel);
            }
        }

        // If booking is confirmed, update room occupancy
        if ($request->status == 'confirmed' && $oldStatus != 'confirmed') {
            $room = $booking->room;
            if ($room) {
                $room->increment('current_occupancy');
                $this->updateHostelRoomCounts($room->hostel);
            }
        }

        return redirect()->back()->with('success', 'Booking status updated successfully.');
    }

    /**
     * Delete booking
     */
    public function destroyBooking(Booking $booking)
    {
        $user = Auth::user();

        if (!$user->managedHostels()->where('hostels.id', $booking->hostel_id)->exists()) {
            abort(403);
        }

        // Free up room space if booking was confirmed
        if ($booking->status == 'confirmed') {
            $room = $booking->room;
            if ($room) {
                $room->decrement('current_occupancy');
                $this->updateHostelRoomCounts($room->hostel);
            }
        }

        $booking->delete();

        return redirect()->route('hostel-manager.bookings')->with('success', 'Booking deleted successfully.');
    }

    /**
     * ===========================================
     * OCCUPANT (STUDENT) MANAGEMENT
     * ===========================================
     */

    /**
     * Display all occupants
     */
    public function occupants(Request $request)
    {
        $user = Auth::user();
        $hostelIds = $user->managedHostels()->pluck('hostels.id');

        $query = User::whereHas('bookings', function($q) use ($hostelIds) {
                $q->whereIn('hostel_id', $hostelIds)
                  ->whereIn('status', ['confirmed', 'pending']);
            })
            ->with(['bookings' => function($q) use ($hostelIds) {
                $q->whereIn('hostel_id', $hostelIds)
                  ->whereIn('status', ['confirmed', 'pending'])
                  ->with('room', 'hostel');
            }]);

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('student_id', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        if ($request->filled('hostel_id')) {
            $query->whereHas('bookings', function($q) use ($request) {
                $q->where('hostel_id', $request->hostel_id);
            });
        }

        $occupants = $query->paginate(15)->withQueryString();

        // Summary statistics
        $summary = [
            'total' => User::whereHas('bookings', function($q) use ($hostelIds) {
                $q->whereIn('hostel_id', $hostelIds)->whereIn('status', ['confirmed', 'pending']);
            })->count(),
            'male' => User::whereHas('bookings', function($q) use ($hostelIds) {
                $q->whereIn('hostel_id', $hostelIds)->whereIn('status', ['confirmed', 'pending']);
            })->where('gender', 'male')->count(),
            'female' => User::whereHas('bookings', function($q) use ($hostelIds) {
                $q->whereIn('hostel_id', $hostelIds)->whereIn('status', ['confirmed', 'pending']);
            })->where('gender', 'female')->count(),
            'active' => User::whereHas('bookings', function($q) use ($hostelIds) {
                $q->whereIn('hostel_id', $hostelIds)
                  ->where('status', 'confirmed')
                  ->where('check_in', '<=', now())
                  ->where('check_out', '>=', now());
            })->count(),
        ];

        $hostels = $user->managedHostels()->get();

        return view('hostel-manager.occupants.index', compact('occupants', 'hostels', 'summary'));
    }

    /**
     * Show occupant details
     */
    public function showOccupant(User $student)
    {
        $user = Auth::user();
        $hostelIds = $user->managedHostels()->pluck('hostels.id');

        // Verify this student has booking in manager's hostels
        $bookings = $student->bookings()
            ->whereIn('hostel_id', $hostelIds)
            ->with(['room', 'hostel', 'payments'])
            ->get();

        if ($bookings->isEmpty()) {
            abort(404, 'Student not found in your hostels.');
        }

        // Current active booking
        $currentBooking = $student->bookings()
            ->whereIn('hostel_id', $hostelIds)
            ->where('status', 'confirmed')
            ->where('check_in', '<=', now())
            ->where('check_out', '>=', now())
            ->with(['room', 'hostel'])
            ->first();

        // Payment history
        $payments = Payment::whereHas('booking', function($q) use ($hostelIds, $student) {
                $q->whereIn('hostel_id', $hostelIds)
                  ->where('user_id', $student->id);
            })
            ->with('booking')
            ->latest()
            ->get();

        // Complaint history
        $complaints = Complaint::whereIn('hostel_id', $hostelIds)
            ->where('user_id', $student->id)
            ->latest()
            ->get();

        return view('hostel-manager.occupants.show', compact('student', 'bookings', 'currentBooking', 'payments', 'complaints'));
    }

    /**
     * Export occupants list
     */
    public function exportOccupants()
    {
        $user = Auth::user();
        $hostelIds = $user->managedHostels()->pluck('hostels.id');

        $occupants = User::whereHas('bookings', function($q) use ($hostelIds) {
                $q->whereIn('hostel_id', $hostelIds)
                  ->whereIn('status', ['confirmed', 'pending']);
            })
            ->with(['bookings' => function($q) use ($hostelIds) {
                $q->whereIn('hostel_id', $hostelIds)
                  ->whereIn('status', ['confirmed', 'pending'])
                  ->with('room', 'hostel');
            }])
            ->get();

        $filename = 'occupants-' . now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($occupants) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'Name', 'Student ID', 'Email', 'Phone', 'Gender',
                'Hostel', 'Room', 'Check In', 'Check Out', 'Status'
            ]);

            foreach ($occupants as $occupant) {
                foreach ($occupant->bookings as $booking) {
                    fputcsv($file, [
                        $occupant->name,
                        $occupant->student_id ?? 'N/A',
                        $occupant->email,
                        $occupant->phone ?? 'N/A',
                        $occupant->gender ?? 'N/A',
                        $booking->hostel->name ?? 'N/A',
                        $booking->room->number ?? 'N/A',
                        $booking->check_in->format('Y-m-d'),
                        $booking->check_out->format('Y-m-d'),
                        $booking->status
                    ]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * ===========================================
     * COMPLAINT MANAGEMENT
     * ===========================================
     */

    /**
     * Display all complaints
     */
    public function complaints(Request $request)
    {
        $user = Auth::user();
        $hostelIds = $user->managedHostels()->pluck('hostels.id');

        $query = Complaint::with(['user', 'room', 'hostel'])
            ->whereIn('hostel_id', $hostelIds);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('hostel_id')) {
            $query->where('hostel_id', $request->hostel_id);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function($uq) use ($request) {
                      $uq->where('name', 'like', '%' . $request->search . '%');
                  });
            });
        }

        $complaints = $query->latest()->paginate(15)->withQueryString();

        // Summary statistics
        $summary = [
            'total' => Complaint::whereIn('hostel_id', $hostelIds)->count(),
            'pending' => Complaint::whereIn('hostel_id', $hostelIds)->where('status', 'pending')->count(),
            'in_progress' => Complaint::whereIn('hostel_id', $hostelIds)->where('status', 'in_progress')->count(),
            'resolved' => Complaint::whereIn('hostel_id', $hostelIds)->where('status', 'resolved')->count(),
            'urgent' => Complaint::whereIn('hostel_id', $hostelIds)->where('priority', 'urgent')->whereIn('status', ['pending', 'in_progress'])->count(),
        ];

        $hostels = $user->managedHostels()->get();

        return view('hostel-manager.complaints.index', compact('complaints', 'hostels', 'summary'));
    }

    /**
     * Show complaint details
     */
    public function showComplaint(Complaint $complaint)
    {
        $user = Auth::user();

        if (!$user->managedHostels()->where('hostels.id', $complaint->hostel_id)->exists()) {
            abort(403);
        }

        $complaint->load(['user', 'room', 'hostel']);

        return view('hostel-manager.complaints.show', compact('complaint'));
    }

    /**
     * Update complaint status
     */
    public function updateComplaint(Request $request, Complaint $complaint)
    {
        $user = Auth::user();

        if (!$user->managedHostels()->where('hostels.id', $complaint->hostel_id)->exists()) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:pending,in_progress,resolved,rejected',
            'resolution_notes' => 'nullable|string|max:1000'
        ]);

        $complaint->update([
            'status' => $request->status,
            'resolution_notes' => $request->resolution_notes,
            'resolved_at' => in_array($request->status, ['resolved', 'rejected']) ? now() : null,
            'resolved_by' => in_array($request->status, ['resolved', 'rejected']) ? $user->id : null,
        ]);

        return redirect()->back()->with('success', 'Complaint updated successfully.');
    }

    /**
     * Delete complaint
     */
    public function destroyComplaint(Complaint $complaint)
    {
        $user = Auth::user();

        if (!$user->managedHostels()->where('hostels.id', $complaint->hostel_id)->exists()) {
            abort(403);
        }

        $complaint->delete();

        return redirect()->route('hostel-manager.complaints')->with('success', 'Complaint deleted successfully.');
    }

    /**
     * ===========================================
     * PAYMENT MANAGEMENT
     * ===========================================
     */

    /**
     * Display all payments
     */
    public function payments(Request $request)
    {
        $user = Auth::user();
        $hostelIds = $user->managedHostels()->pluck('hostels.id');

        $query = Payment::whereHas('booking', function($q) use ($hostelIds) {
                $q->whereIn('hostel_id', $hostelIds);
            })
            ->with(['booking.user', 'booking.room', 'booking.hostel']);

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

        if ($request->filled('search')) {
            $query->whereHas('booking.user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $payments = $query->latest()->paginate(15)->withQueryString();

        // Summary statistics
        $summary = [
            'total_amount' => Payment::whereHas('booking', function($q) use ($hostelIds) {
                    $q->whereIn('hostel_id', $hostelIds);
                })->where('status', 'completed')->sum('amount'),
            'pending_amount' => Payment::whereHas('booking', function($q) use ($hostelIds) {
                    $q->whereIn('hostel_id', $hostelIds);
                })->where('status', 'pending')->sum('amount'),
            'completed_count' => Payment::whereHas('booking', function($q) use ($hostelIds) {
                    $q->whereIn('hostel_id', $hostelIds);
                })->where('status', 'completed')->count(),
            'pending_count' => Payment::whereHas('booking', function($q) use ($hostelIds) {
                    $q->whereIn('hostel_id', $hostelIds);
                })->where('status', 'pending')->count(),
        ];

        return view('hostel-manager.payments.index', compact('payments', 'summary'));
    }

    /**
     * Show payment details
     */
    public function showPayment(Payment $payment)
    {
        $user = Auth::user();

        if (!$user->managedHostels()->where('hostels.id', $payment->booking->hostel_id)->exists()) {
            abort(403);
        }

        $payment->load(['booking.user', 'booking.room']);

        return view('hostel-manager.payments.show', compact('payment'));
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus(Request $request, Payment $payment)
    {
        $user = Auth::user();

        if (!$user->managedHostels()->where('hostels.id', $payment->booking->hostel_id)->exists()) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:pending,completed,failed,refunded'
        ]);

        $payment->update(['status' => $request->status]);

        // If payment is completed, update booking status if needed
        if ($request->status == 'completed' && $payment->booking->status == 'pending') {
            $payment->booking->update(['status' => 'confirmed']);

            // Update room occupancy
            $room = $payment->booking->room;
            if ($room) {
                $room->increment('current_occupancy');
                $this->updateHostelRoomCounts($room->hostel);
            }
        }

        return redirect()->back()->with('success', 'Payment status updated successfully.');
    }

    /**
     * ===========================================
     * MAINTENANCE MANAGEMENT
     * ===========================================
     */

    /**
     * Display maintenance requests
     */
    public function maintenance(Request $request)
    {
        $user = Auth::user();
        $hostelIds = $user->managedHostels()->pluck('hostels.id');

        $query = MaintenanceRequest::with(['room', 'hostel', 'reportedBy'])
            ->whereIn('hostel_id', $hostelIds);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $requests = $query->latest()->paginate(15);

        return view('hostel-manager.maintenance.index', compact('requests'));
    }

    /**
     * Update maintenance request
     */
    public function updateMaintenance(Request $request, MaintenanceRequest $maintenance)
    {
        $user = Auth::user();

        if (!$user->managedHostels()->where('hostels.id', $maintenance->hostel_id)->exists()) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:pending,in_progress,completed',
            'notes' => 'nullable|string',
        ]);

        $maintenance->update([
            'status' => $request->status,
            'notes' => $request->notes,
            'completed_at' => $request->status == 'completed' ? now() : null,
        ]);

        return redirect()->back()->with('success', 'Maintenance request updated successfully.');
    }

    /**
     * ===========================================
     * REPORTS & ANALYTICS
     * ===========================================
     */

    /**
     * Reports dashboard
     */
    public function reports()
    {
        return view('hostel-manager.reports.index');
    }

    /**
     * Occupancy report
     */
    public function occupancyReport(Request $request)
    {
        $user = Auth::user();
        $hostelIds = $user->managedHostels()->pluck('hostels.id');

        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));

        // Daily occupancy for the period
        $dailyOccupancy = [];
        $period = Carbon::parse($startDate)->daysUntil(Carbon::parse($endDate));

        foreach ($period as $date) {
            $occupiedRooms = Booking::whereIn('hostel_id', $hostelIds)
                ->where('status', 'confirmed')
                ->where('check_in', '<=', $date)
                ->where('check_out', '>=', $date)
                ->count();

            $dailyOccupancy[] = [
                'date' => $date->format('Y-m-d'),
                'occupied' => $occupiedRooms,
                'available' => Room::whereIn('hostel_id', $hostelIds)->count() - $occupiedRooms,
            ];
        }

        // Room type occupancy
        $roomTypeOccupancy = [
            'single' => [
                'total' => Room::whereIn('hostel_id', $hostelIds)->where('capacity', 1)->count(),
                'occupied' => Room::whereIn('hostel_id', $hostelIds)
                    ->where('capacity', 1)
                    ->where('current_occupancy', '>', 0)
                    ->count(),
            ],
            'double' => [
                'total' => Room::whereIn('hostel_id', $hostelIds)->where('capacity', 2)->count(),
                'occupied' => Room::whereIn('hostel_id', $hostelIds)
                    ->where('capacity', 2)
                    ->where('current_occupancy', '>', 0)
                    ->count(),
            ],
            'dormitory' => [
                'total' => Room::whereIn('hostel_id', $hostelIds)->where('capacity', '>=', 4)->count(),
                'occupied' => Room::whereIn('hostel_id', $hostelIds)
                    ->where('capacity', '>=', 4)
                    ->where('current_occupancy', '>', 0)
                    ->count(),
            ],
        ];

        // Gender distribution
        $genderDistribution = [
            'male' => User::whereHas('bookings', function($q) use ($hostelIds) {
                $q->whereIn('hostel_id', $hostelIds)->where('status', 'confirmed');
            })->where('gender', 'male')->count(),
            'female' => User::whereHas('bookings', function($q) use ($hostelIds) {
                $q->whereIn('hostel_id', $hostelIds)->where('status', 'confirmed');
            })->where('gender', 'female')->count(),
        ];

        return view('hostel-manager.reports.occupancy', compact(
            'dailyOccupancy',
            'roomTypeOccupancy',
            'genderDistribution',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Revenue report
     */
    public function revenueReport(Request $request)
    {
        $user = Auth::user();
        $hostelIds = $user->managedHostels()->pluck('hostels.id');

        $year = $request->get('year', now()->year);

        // Monthly revenue
        $monthlyRevenue = [];
        for ($month = 1; $month <= 12; $month++) {
            $revenue = Payment::whereHas('booking', function($q) use ($hostelIds) {
                    $q->whereIn('hostel_id', $hostelIds);
                })
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->where('status', 'completed')
                ->sum('amount');

            $monthlyRevenue[] = [
                'month' => date('F', mktime(0, 0, 0, $month, 1)),
                'revenue' => $revenue
            ];
        }

        // Revenue by hostel
        $revenueByHostel = [];
        foreach ($user->managedHostels as $hostel) {
            $revenueByHostel[] = [
                'hostel' => $hostel->name,
                'revenue' => Payment::whereHas('booking', function($q) use ($hostel) {
                        $q->where('hostel_id', $hostel->id);
                    })
                    ->whereYear('created_at', $year)
                    ->where('status', 'completed')
                    ->sum('amount'),
            ];
        }

        // Payment methods distribution
        $paymentMethods = Payment::whereHas('booking', function($q) use ($hostelIds) {
                $q->whereIn('hostel_id', $hostelIds);
            })
            ->whereYear('created_at', $year)
            ->where('status', 'completed')
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total'))
            ->groupBy('payment_method')
            ->get();

        $totalRevenue = array_sum(array_column($monthlyRevenue, 'revenue'));

        return view('hostel-manager.reports.revenue', compact(
            'monthlyRevenue',
            'revenueByHostel',
            'paymentMethods',
            'totalRevenue',
            'year'
        ));
    }

    /**
     * Export report as CSV
     */
    public function exportReport(Request $request, $type)
    {
        $user = Auth::user();
        $hostelIds = $user->managedHostels()->pluck('hostels.id');

        $filename = $type . '-report-' . now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($type, $hostelIds, $request) {
            $file = fopen('php://output', 'w');

            switch ($type) {
                case 'occupancy':
                    fputcsv($file, ['Date', 'Occupied Rooms', 'Available Rooms', 'Occupancy Rate']);

                    $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
                    $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));
                    $period = Carbon::parse($startDate)->daysUntil(Carbon::parse($endDate));

                    foreach ($period as $date) {
                        $occupied = Booking::whereIn('hostel_id', $hostelIds)
                            ->where('status', 'confirmed')
                            ->where('check_in', '<=', $date)
                            ->where('check_out', '>=', $date)
                            ->count();

                        $total = Room::whereIn('hostel_id', $hostelIds)->count();
                        $rate = $total > 0 ? round(($occupied / $total) * 100, 2) : 0;

                        fputcsv($file, [
                            $date->format('Y-m-d'),
                            $occupied,
                            $total - $occupied,
                            $rate . '%'
                        ]);
                    }
                    break;

                case 'revenue':
                    fputcsv($file, ['Month', 'Revenue', 'Booking Count']);

                    $year = $request->get('year', now()->year);

                    for ($month = 1; $month <= 12; $month++) {
                        $revenue = Payment::whereHas('booking', function($q) use ($hostelIds) {
                                $q->whereIn('hostel_id', $hostelIds);
                            })
                            ->whereYear('created_at', $year)
                            ->whereMonth('created_at', $month)
                            ->where('status', 'completed')
                            ->sum('amount');

                        $count = Payment::whereHas('booking', function($q) use ($hostelIds) {
                                $q->whereIn('hostel_id', $hostelIds);
                            })
                            ->whereYear('created_at', $year)
                            ->whereMonth('created_at', $month)
                            ->where('status', 'completed')
                            ->count();

                        fputcsv($file, [
                            date('F', mktime(0, 0, 0, $month, 1)),
                            $revenue,
                            $count
                        ]);
                    }
                    break;
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * ===========================================
     * PROFILE MANAGEMENT
     * ===========================================
     */

    /**
     * Show profile
     */
    public function profile()
    {
        $user = Auth::user();
        $managedHostels = $user->managedHostels()->get();

        return view('hostel-manager.profile', compact('user', 'managedHostels'));
    }

    /**
     * Update profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'current_password' => 'nullable|required_with:new_password|current_password',
            'new_password' => 'nullable|min:8|confirmed',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        if ($request->filled('new_password')) {
            $user->update([
                'password' => Hash::make($request->new_password)
            ]);
        }

        return redirect()->route('hostel-manager.profile')->with('success', 'Profile updated successfully.');
    }

    /**
     * Settings page
     */
    public function settings()
    {
        $user = Auth::user();

        return view('hostel-manager.settings', compact('user'));
    }

    /**
     * Update notification preferences
     */
    public function updateSettings(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'booking_alerts' => 'boolean',
            'complaint_alerts' => 'boolean',
        ]);

        // Store settings in user meta or settings table
        // This depends on your implementation

        return redirect()->back()->with('success', 'Settings updated successfully.');
    }

    /**
     * ===========================================
     * HELPER METHODS
     * ===========================================
     */

    /**
     * Update hostel room counts
     */
    private function updateHostelRoomCounts(Hostel $hostel)
    {
        $hostel->total_rooms = $hostel->rooms()->count();
        $hostel->available_rooms = $hostel->rooms()
            ->where('status', 'available')
            ->whereColumn('current_occupancy', '<', 'capacity')
            ->count();
        $hostel->save();
    }

    /**
     * Get notification count for sidebar
     */
    public function getNotificationCount()
    {
        $user = Auth::user();
        $hostelIds = $user->managedHostels()->pluck('hostels.id');

        return [
            'pending_bookings' => Booking::whereIn('hostel_id', $hostelIds)->where('status', 'pending')->count(),
            'pending_complaints' => Complaint::whereIn('hostel_id', $hostelIds)->where('status', 'pending')->count(),
            'urgent_complaints' => Complaint::whereIn('hostel_id', $hostelIds)->where('priority', 'urgent')->whereIn('status', ['pending', 'in_progress'])->count(),
            'maintenance_requests' => MaintenanceRequest::whereIn('hostel_id', $hostelIds)->where('status', 'pending')->count(),
        ];
    }
}
