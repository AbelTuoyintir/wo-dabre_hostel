<?php

namespace App\Http\Controllers;

use App\Models\Hostel;
use App\Models\Room;
use App\Models\Booking;
use App\Models\Complaint;
use App\Models\User;
use App\Models\Payment;
use App\Models\MaintenanceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Carbon\Carbon;

class HostelManagerDashboard extends Controller
{
    public function dashboard(): View
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
            'monthlyBookings',
            'revenueByHostel',
            'recentActivities'
        ));
    }

    public function profile(): View
    {
        return view('hostel-manager.profile');
    }

    public function updateProfile(Request $request)
    {
        // Logic to update profile
        return redirect()->back()->with('success', 'Profile updated successfully.');
    }

        public function rooms(Request $request)
    {
        $user = Auth::user();
        $hostelIds = $user->managedHostels()->pluck('hostels.id');

        $query = Room::with('hostel')
            ->whereIn('hostel_id', $hostelIds);

        // Apply filters
        if ($request->filled('hostel_id')) {
            $query->where('hostel_id', $request->hostel_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('gender')) {
            $query->whereIn('gender', [$request->gender, 'any']);
        }

        if ($request->filled('min_price')) {
            $query->where('price_per_month', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price_per_month', '<=', $request->max_price);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('hostel', function($hq) use ($request) {
                      $hq->where('name', 'like', '%' . $request->search . '%');
                  });
            });
        }

        $rooms = $query->paginate(15)->withQueryString();

        // Get summary statistics
        $summary = [
            'total' => Room::whereIn('hostel_id', $hostelIds)->count(),
            'available' => Room::whereIn('hostel_id', $hostelIds)
                ->where('status', 'available')
                ->whereColumn('current_occupancy', '<', 'capacity')
                ->count(),
            'occupied' => Room::whereIn('hostel_id', $hostelIds)
                ->where('current_occupancy', '>', 0)
                ->count(),
            'maintenance' => Room::whereIn('hostel_id', $hostelIds)
                ->where('status', 'maintenance')
                ->count(),
            'total_capacity' => Room::whereIn('hostel_id', $hostelIds)->sum('capacity'),
            'current_occupancy' => Room::whereIn('hostel_id', $hostelIds)->sum('current_occupancy'),
        ];

        $hostels = $user->managedHostels()->get();

        return view('hostel-manager.rooms.index', compact('rooms', 'hostels', 'summary'));
    }

    public function editRoom(Room $room)
    {
        $user = Auth::user();

        // Verify manager owns this room's hostel
        if (!$user->managedHostels()->where('hostels.id', $room->hostel_id)->exists()) {
            abort(403);
        }

        $hostels = $user->managedHostels()->get();
        return view('hostel-manager.rooms.edit', compact('room', 'hostels'));
    }

    public function createRoom(){
        $user = Auth::user();
        $hostels = $user->managedHostels()->get();
        return view('hostel-manager.rooms.create', compact('hostels'));
    }

    public function storeRoom(Request $request)
{
    $user = Auth::user();

    $validated = $request->validate([
        'hostel_id' => 'required|exists:hostels,id',
        'number' => 'required|string|max:255',
        'capacity' => 'required|integer|min:1',
        'price_per_month' => 'required|numeric|min:0',
        'gender' => 'required|in:male,female,any',
        'status' => 'required|in:available,maintenance',
        'description' => 'nullable|string',
        'floor' => 'nullable|integer|min:0',
        'size_sqm' => 'nullable|numeric|min:0',
        'window_type' => 'nullable|in:street,courtyard,garden,none',
        'furnished' => 'boolean',
        'private_bathroom' => 'boolean',
    ]);

    // Verify manager owns this hostel
    if (!$user->managedHostels()->where('hostels.id', $validated['hostel_id'])->exists()) {
        abort(403, 'You do not have permission to add rooms to this hostel.');
    }

    // Check for duplicate room number
    $exists = Room::where('hostel_id', $validated['hostel_id'])
        ->where('number', $validated['number'])
        ->exists();

    if ($exists) {
        return back()->withInput()->withErrors(['number' => 'Room number already exists in this hostel.']);
    }

    $room = Room::create([
        'hostel_id' => $validated['hostel_id'],
        'number' => $validated['number'],
        'floor' => $validated['floor'] ?? null,
        'capacity' => $validated['capacity'],
        'price_per_month' => $validated['price_per_month'],
        'gender' => $validated['gender'],
        'status' => $validated['status'],
        'description' => $validated['description'] ?? null,
        'size_sqm' => $validated['size_sqm'] ?? null,
        'window_type' => $validated['window_type'] ?? null,
        'furnished' => $request->boolean('furnished'),
        'private_bathroom' => $request->boolean('private_bathroom'),
        'current_occupancy' => 0,
    ]);

    return redirect()->route('hostel-manager.rooms')
        ->with('success', 'Room created successfully.');
}

        public function updateRoom(Request $request, Room $room)
    {
        $user = Auth::user();

        // Verify manager owns this room's hostel
        if (!$user->managedHostels()->where('hostels.id', $room->hostel_id)->exists()) {
            abort(403);
        }

        $validated = $request->validate([
            'number' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'price_per_semester' => 'required|numeric|min:0',
            'gender' => 'required|in:male,female,any',
            'status' => 'required|in:available,occupied,maintenance',
            'description' => 'nullable|string',
            'floor' => 'nullable|integer|min:0',
            'size_sqm' => 'nullable|numeric|min:0',
            'window_type' => 'nullable|in:street,courtyard,garden,none',
            'furnished' => 'boolean',
            'private_bathroom' => 'boolean',
        ]);

        $room->update($validated);

        return redirect()->route('hostel-manager.rooms')
            ->with('success', 'Room updated successfully.');
    }

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

    public function exportRooms(Request $request)
    {
        $user = Auth::user();
        $hostelIds = $user->managedHostels()->pluck('hostels.id');

        $rooms = Room::with('hostel')
            ->whereIn('hostel_id', $hostelIds);

        // Apply filters
        if ($request->filled('hostel_id')) {
            $rooms->where('hostel_id', $request->hostel_id);
        }

        if ($request->filled('status')) {
            $rooms->where('status', $request->status);
        }

        $rooms = $rooms->get();

        $format = $request->get('format', 'csv');

        if ($format === 'csv') {
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="rooms.csv"',
            ];

            $callback = function() use ($rooms) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['Room Number', 'Hostel', 'Status', 'Capacity', 'Current Occupancy', 'Price/Month', 'Gender']);

                foreach ($rooms as $room) {
                    fputcsv($file, [
                        $room->number,
                        $room->hostel->name,
                        $room->status,
                        $room->capacity,
                        $room->current_occupancy,
                        $room->price_per_month,
                        $room->gender,
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        // PDF export would require a library like DomPDF
        return redirect()->back()->with('error', 'PDF export is not yet implemented.');
    }

    public function occupants(Request $request): View
    {
        $user = Auth::user();
        $hostelIds = Hostel::where('user_id', $user->id)->pluck('id');

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
                ->orWhere('student_id', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        $occupants = $query->paginate(15);

        // Calculate stats
        $stats = [
            'total' => $query->count(),
            'male' => User::whereHas('bookings', fn($q) => $q->whereIn('hostel_id', $hostelIds))->where('gender', 'male')->count(),
            'female' => User::whereHas('bookings', fn($q) => $q->whereIn('hostel_id', $hostelIds))->where('gender', 'female')->count(),
            'active' => User::whereHas('bookings', fn($q) => $q->whereIn('hostel_id', $hostelIds)->where('status', 'confirmed'))->count(),
            'checkout_today' => Booking::whereIn('hostel_id', $hostelIds)
                ->whereDate('check_out', now())
                ->where('status', 'confirmed')
                ->count(),
        ];

        $hostels = Hostel::where('user_id', $user->id)->get();

        return view('hostel-manager.occupants.index', compact('occupants', 'hostels', 'stats'));
    }

    public function showOccupant(User $user): View
    {
        $authUser = Auth::user();
        $hostelIds = Hostel::where('user_id', $authUser->id)->pluck('id');

        // Verify the occupant has bookings in managed hostels
        if (!$user->bookings()->whereIn('hostel_id', $hostelIds)->exists()) {
            abort(403);
        }

        $bookings = $user->bookings()
            ->whereIn('hostel_id', $hostelIds)
            ->with('room', 'hostel')
            ->get();

        return view('hostel-manager.occupants.show', compact('user', 'bookings'));
    }

    public function exportOccupants(Request $request)
    {
        $user = Auth::user();
        $hostelIds = Hostel::where('user_id', $user->id)->pluck('id');

        $query = User::whereHas('bookings', function($q) use ($hostelIds) {
            $q->whereIn('hostel_id', $hostelIds)
                ->whereIn('status', ['confirmed', 'pending']);
        });

        if ($request->filled('hostel_id')) {
            $query->whereHas('bookings', function($q) use ($request) {
                $q->where('hostel_id', $request->hostel_id);
            });
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        $occupants = $query->get();

        $format = $request->get('format', 'csv');

        if ($format === 'csv') {
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="occupants.csv"',
            ];

            $callback = function() use ($occupants) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['Name', 'Email', 'Student ID', 'Gender', 'Phone', 'Room', 'Hostel']);

                foreach ($occupants as $occupant) {
                    $booking = $occupant->bookings()->first();
                    fputcsv($file, [
                        $occupant->name,
                        $occupant->email,
                        $occupant->student_id ?? 'N/A',
                        ucfirst($occupant->gender),
                        $occupant->phone ?? 'N/A',
                        $booking->room->number ?? 'N/A',
                        $booking->hostel->name ?? 'N/A',
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        return redirect()->back()->with('error', 'PDF export is not yet implemented.');
    }

    public function complaints(Request $request): View
    {
        $user = Auth::user();
        $hostelIds = Hostel::where('user_id', $user->id)->pluck('id');

        $query = Complaint::whereIn('hostel_id', $hostelIds)
            ->with(['user', 'room', 'hostel']);

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                ->orWhere('description', 'like', '%' . $request->search . '%')
                ->orWhereHas('user', function($uq) use ($request) {
                    $uq->where('name', 'like', '%' . $request->search . '%');
                });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('hostel_id')) {
            $query->where('hostel_id', $request->hostel_id);
        }

        $complaints = $query->latest()->paginate(15);

        // Calculate stats
        $stats = [
            'total' => Complaint::whereIn('hostel_id', $hostelIds)->count(),
            'pending' => Complaint::whereIn('hostel_id', $hostelIds)->where('status', 'pending')->count(),
            'in_progress' => Complaint::whereIn('hostel_id', $hostelIds)->where('status', 'in_progress')->count(),
            'resolved' => Complaint::whereIn('hostel_id', $hostelIds)->where('status', 'resolved')->count(),
            'urgent' => Complaint::whereIn('hostel_id', $hostelIds)->where('priority', 'urgent')->whereIn('status', ['pending', 'in_progress'])->count(),
        ];

        $hostels = Hostel::where('user_id', $user->id)->get();

        return view('hostel-manager.complaints.index', compact('complaints', 'hostels', 'stats'));
    }

    public function showComplaint(Complaint $complaint): View
    {
        $user = Auth::user();

        if (!$user->managedHostels()->where('hostels.id', $complaint->hostel_id)->exists()) {
            abort(403);
        }

        $complaint->load(['user', 'room', 'hostel']);

        return view('hostel-manager.complaints.show', compact('complaint'));
    }

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
            'resolved_by' => in_array($request->status, ['resolved', 'rejected']) ? $user->name : null,
        ]);

        return redirect()->back()->with('success', 'Complaint updated successfully.');
    }

    public function destroyComplaint(Complaint $complaint)
    {
        $user = Auth::user();

        if (!$user->managedHostels()->where('hostels.id', $complaint->hostel_id)->exists()) {
            abort(403);
        }

        $complaint->delete();

        return redirect()->route('hostel-manager.complaints')->with('success', 'Complaint deleted successfully.');
    }

    public function destroyRoom(Room $room)
    {
        $user = Auth::user();

        // Verify manager owns this room's hostel
        if (!$user->managedHostels()->where('hostels.id', $room->hostel_id)->exists()) {
            abort(403);
        }

        // Check if room has active bookings
        if ($room->bookings()->where('status', 'confirmed')->exists()) {
            return redirect()->back()->with('error', 'Cannot delete a room with active bookings.');
        }

        $room->delete();

        return redirect()->route('hostel-manager.rooms')->with('success', 'Room deleted successfully.');
    }

    public function updateRoomStatus(Request $request, Room $room)
    {
        $user = Auth::user();

        // Verify manager owns this room's hostel
        if (!$user->managedHostels()->where('hostels.id', $room->hostel_id)->exists()) {
            abort(403);
        }

        $request->validate(['status' => 'required|in:available,maintenance']);

        $room->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Room status updated successfully.');
    }

    public function settings(): View
    {
        return view('hostel-manager.settings');
    }

    public function bookings(Request $request): View
    {
        $user = Auth::user();
        $hostelIds = $user->managedHostels()->pluck('hostels.id');

        $query = Booking::whereIn('hostel_id', $hostelIds)
            ->with(['user', 'room', 'hostel']);

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->orWhereHas('user', function($uq) use ($request) {
                    $uq->where('name', 'like', '%' . $request->search . '%');
                })->orWhereHas('room', function($rq) use ($request) {
                    $rq->where('number', 'like', '%' . $request->search . '%');
                });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('hostel_id')) {
            $query->where('hostel_id', $request->hostel_id);
        }

        $bookings = $query->latest()->paginate(15);

        $stats = [
            'total' => Booking::whereIn('hostel_id', $hostelIds)->count(),
            'pending' => Booking::whereIn('hostel_id', $hostelIds)->where('status', 'pending')->count(),
            'confirmed' => Booking::whereIn('hostel_id', $hostelIds)->where('status', 'confirmed')->count(),
            'cancelled' => Booking::whereIn('hostel_id', $hostelIds)->where('status', 'cancelled')->count(),
            'completed' => Booking::whereIn('hostel_id', $hostelIds)->where('status', 'completed')->count(),
        ];

        $hostels = $user->managedHostels()->get();

        return view('hostel-manager.bookings.index', compact('bookings', 'hostels', 'stats'));
    }

    public function showBooking(Booking $booking): View
    {
        $user = Auth::user();

        if (!$user->managedHostels()->where('hostels.id', $booking->hostel_id)->exists()) {
            abort(403);
        }

        $booking->load(['user', 'room', 'hostel', 'payment']);

        return view('hostel-manager.bookings.show', compact('booking'));
    }

    public function updateBookingStatus(Request $request, Booking $booking)
    {
        $user = Auth::user();

        if (!$user->managedHostels()->where('hostels.id', $booking->hostel_id)->exists()) {
            abort(403);
        }

        $request->validate(['status' => 'required|in:pending,confirmed,cancelled,completed']);

        $booking->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Booking status updated successfully.');
    }

    public function destroyBooking(Booking $booking)
    {
        $user = Auth::user();

        if (!$user->managedHostels()->where('hostels.id', $booking->hostel_id)->exists()) {
            abort(403);
        }

        $booking->delete();

        return redirect()->route('hostel-manager.bookings')->with('success', 'Booking deleted successfully.');
    }

    public function payments(Request $request): View
    {
        $user = Auth::user();
        $hostelIds = $user->managedHostels()->pluck('hostels.id');

        $query = Payment::whereHas('booking', function($q) use ($hostelIds) {
            $q->whereIn('hostel_id', $hostelIds);
        })->with(['booking.user', 'booking.room', 'booking.hostel']);

        if ($request->filled('search')) {
            $query->whereHas('booking.user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $payments = $query->latest()->paginate(15);

        $stats = [
            'total_payments' => Payment::whereHas('booking', function($q) use ($hostelIds) {
                $q->whereIn('hostel_id', $hostelIds);
            })->count(),
            'completed' => Payment::whereHas('booking', function($q) use ($hostelIds) {
                $q->whereIn('hostel_id', $hostelIds);
            })->where('status', 'completed')->count(),
            'pending' => Payment::whereHas('booking', function($q) use ($hostelIds) {
                $q->whereIn('hostel_id', $hostelIds);
            })->where('status', 'pending')->count(),
            'total_revenue' => Payment::whereHas('booking', function($q) use ($hostelIds) {
                $q->whereIn('hostel_id', $hostelIds);
            })->where('status', 'completed')->sum('amount'),
        ];

        return view('hostel-manager.payments.index', compact('payments', 'stats'));
    }

    public function showPayment(Payment $payment): View
    {
        $user = Auth::user();

        if (!$user->managedHostels()->where('hostels.id', $payment->booking->hostel_id)->exists()) {
            abort(403);
        }

        $payment->load(['booking.user', 'booking.room', 'booking.hostel']);

        return view('hostel-manager.payments.show', compact('payment'));
    }

    public function updatePaymentStatus(Request $request, Payment $payment)
    {
        $user = Auth::user();

        if (!$user->managedHostels()->where('hostels.id', $payment->booking->hostel_id)->exists()) {
            abort(403);
        }

        $request->validate(['status' => 'required|in:pending,completed,failed']);

        $payment->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Payment status updated successfully.');
    }

    public function myHostels(): View
    {
        $user = Auth::user();
        $hostels = $user->managedHostels()->withCount(['rooms', 'bookings'])->get();

        return view('hostel-manager.hostels.index', compact('hostels'));
    }

    public function showHostel(Hostel $hostel): View
    {
        $user = Auth::user();

        if (!$user->managedHostels()->where('hostels.id', $hostel->id)->exists()) {
            abort(403);
        }

        $hostel->load(['rooms', 'bookings', 'images']);

        $stats = [
            'total_rooms' => $hostel->rooms()->count(),
            'available_rooms' => $hostel->rooms()
                ->where('status', 'available')
                ->whereColumn('current_occupancy', '<', 'capacity')
                ->count(),
            'total_capacity' => $hostel->rooms()->sum('capacity'),
            'current_occupancy' => $hostel->rooms()->sum('current_occupancy'),
            'total_bookings' => $hostel->bookings()->count(),
            'confirmed_bookings' => $hostel->bookings()->where('status', 'confirmed')->count(),
            'revenue' => Payment::whereHas('booking', function($q) use ($hostel) {
                $q->where('hostel_id', $hostel->id);
            })->where('status', 'completed')->sum('amount'),
        ];

        return view('hostel-manager.hostels.show', compact('hostel', 'stats'));
    }

    public function reports(): View
    {
        $user = Auth::user();
        $hostelIds = $user->managedHostels()->pluck('hostels.id');

        $data = [
            'total_bookings' => Booking::whereIn('hostel_id', $hostelIds)->count(),
            'total_revenue' => Payment::whereHas('booking', function($q) use ($hostelIds) {
                $q->whereIn('hostel_id', $hostelIds);
            })->where('status', 'completed')->sum('amount'),
            'total_complaints' => Complaint::whereIn('hostel_id', $hostelIds)->count(),
            'total_occupants' => User::whereHas('bookings', function($q) use ($hostelIds) {
                $q->whereIn('hostel_id', $hostelIds)->where('status', 'confirmed');
            })->count(),
        ];

        return view('hostel-manager.reports.index', compact('data'));
    }

    public function occupancyReport(): View
    {
        $user = Auth::user();
        $hostels = $user->managedHostels()->get();

        $occupancyData = [];
        foreach ($hostels as $hostel) {
            $occupancyData[] = [
                'hostel' => $hostel->name,
                'capacity' => $hostel->rooms()->sum('capacity'),
                'occupancy' => $hostel->rooms()->sum('current_occupancy'),
                'percentage' => $hostel->rooms()->sum('capacity') > 0
                    ? round(($hostel->rooms()->sum('current_occupancy') / $hostel->rooms()->sum('capacity')) * 100, 2)
                    : 0,
            ];
        }

        return view('hostel-manager.reports.occupancy', compact('occupancyData'));
    }

    public function revenueReport(): View
    {
        $user = Auth::user();
        $hostelIds = $user->managedHostels()->pluck('hostels.id');

        $revenueData = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $revenueData[] = [
                'month' => $month->format('M Y'),
                'revenue' => Payment::whereHas('booking', function($q) use ($hostelIds) {
                    $q->whereIn('hostel_id', $hostelIds);
                })->whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->where('status', 'completed')
                ->sum('amount'),
            ];
        }

        return view('hostel-manager.reports.revenue', compact('revenueData'));
    }

    public function exportReport(Request $request, $type)
    {
        $user = Auth::user();
        $hostelIds = $user->managedHostels()->pluck('hostels.id');

        if ($type === 'bookings') {
            $bookings = Booking::whereIn('hostel_id', $hostelIds)->with('user', 'room', 'hostel')->get();

            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="bookings-report.csv"',
            ];

            $callback = function() use ($bookings) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['Student Name', 'Room', 'Hostel', 'Check In', 'Check Out', 'Status']);

                foreach ($bookings as $booking) {
                    fputcsv($file, [
                        $booking->user->name,
                        $booking->room->number,
                        $booking->hostel->name,
                        $booking->check_in->format('Y-m-d'),
                        $booking->check_out->format('Y-m-d'),
                        ucfirst($booking->status),
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        return redirect()->back()->with('error', 'Report type not found.');
    }

    public function bookings(Request $request): View
{
    $user = Auth::user();
    $hostelIds = $user->managedHostels()->pluck('hostels.id');

    $query = Booking::whereIn('hostel_id', $hostelIds)
        ->with(['user', 'room', 'hostel', 'payment']);

    // Apply filters
    if ($request->filled('search')) {
        $query->where(function($q) use ($request) {
            $q->whereHas('user', function($uq) use ($request) {
                $uq->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('student_id', 'like', '%' . $request->search . '%');
            })->orWhereHas('room', function($rq) use ($request) {
                $rq->where('number', 'like', '%' . $request->search . '%');
            });
        });
    }

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

    $bookings = $query->latest()->paginate(15);

    // Calculate stats
    $stats = [
        'total' => Booking::whereIn('hostel_id', $hostelIds)->count(),
        'pending' => Booking::whereIn('hostel_id', $hostelIds)->where('status', 'pending')->count(),
        'confirmed' => Booking::whereIn('hostel_id', $hostelIds)->where('status', 'confirmed')->count(),
        'completed' => Booking::whereIn('hostel_id', $hostelIds)->where('status', 'completed')->count(),
        'cancelled' => Booking::whereIn('hostel_id', $hostelIds)->where('status', 'cancelled')->count(),
        'today_checkins' => Booking::whereIn('hostel_id', $hostelIds)
            ->whereDate('check_in', now())
            ->where('status', 'confirmed')
            ->count(),
        'today_checkouts' => Booking::whereIn('hostel_id', $hostelIds)
            ->whereDate('check_out', now())
            ->where('status', 'confirmed')
            ->count(),
    ];

    $hostels = $user->managedHostels()->get();

    return view('hostel-manager.bookings.index', compact('bookings', 'hostels', 'stats'));
}

public function showBooking(Booking $booking): View
{
    $user = Auth::user();

    if (!$user->managedHostels()->where('hostels.id', $booking->hostel_id)->exists()) {
        abort(403);
    }

    $booking->load(['user', 'room', 'hostel', 'payment']);

    return view('hostel-manager.bookings.show', compact('booking'));
}

public function updateBookingStatus(Request $request, Booking $booking)
{
    $user = Auth::user();

    if (!$user->managedHostels()->where('hostels.id', $booking->hostel_id)->exists()) {
        abort(403);
    }

    $request->validate([
        'status' => 'required|in:pending,confirmed,completed,cancelled',
        'cancellation_reason' => 'required_if:status,cancelled|nullable|string|max:500'
    ]);

    $oldStatus = $booking->status;
    $booking->status = $request->status;

    if ($request->status == 'cancelled') {
        $booking->cancellation_reason = $request->cancellation_reason;
        $booking->cancelled_at = now();

        // Free up the room space if it was confirmed
        if ($oldStatus == 'confirmed') {
            $room = $booking->room;
            if ($room) {
                $room->current_occupancy = max(0, $room->current_occupancy - 1);
                $room->save();
            }
        }
    }

    if ($request->status == 'confirmed' && $oldStatus == 'pending') {
        // Update room occupancy when booking is confirmed
        $room = $booking->room;
        if ($room) {
            $room->current_occupancy = min($room->capacity, $room->current_occupancy + 1);
            $room->save();
        }
    }

    $booking->save();

    return redirect()->back()->with('success', 'Booking status updated successfully.');
}

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
            $room->current_occupancy = max(0, $room->current_occupancy - 1);
            $room->save();
        }
    }

    $booking->delete();

    return redirect()->route('hostel-manager.bookings')->with('success', 'Booking deleted successfully.');
}
}
