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

    public function occupants(): View
    {
        return view('hostel-manager.occupants');
    }

    public function complaints(): View
    {
        return view('hostel-manager.complaints');
    }
}
