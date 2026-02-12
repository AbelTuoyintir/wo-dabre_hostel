<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Hostel;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function dashboard(): View
    {
        $stats = [
            'total_users' => User::count(),
            'total_hostels' => Hostel::count(),
            'pending_hostels' => Hostel::where('is_approved', false)->count(),
            'total_bookings' => Booking::count(),
            'pending_bookings' => Booking::where('booking_status', 'pending')->count(),
            'total_revenue' => Booking::where('payment_status', 'paid')->sum('total_amount'),
        ];

        $recentBookings = Booking::with(['user', 'hostel'])->latest()->take(5)->get();
        $recentUsers = User::latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentBookings', 'recentUsers'));
    }

    /**
     * Display all users.
     */
    public function users(): View
    {
        $query = User::query();

        if (request('search')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('student_id', 'like', "%{$search}%");
            });
        }

        if (request('role')) {
            $query->where('role', request('role'));
        }

        $users = $query->paginate(15);
        return view('admin.users', compact('users'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function editUser(User $user): View
    {
        return view('admin.users-edit', compact('user'));
    }

    /**
     * Update the specified user.
     */
    public function updateUser(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'student_id' => 'nullable|string|max:255',
            'role' => 'required|in:student,hostel_manager,admin',
            'is_active' => 'nullable|boolean',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'student_id' => $request->student_id,
            'role' => $request->role,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Toggle the active status of the specified user.
     */
    public function toggleUserStatus(User $user): RedirectResponse
    {
        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'activated' : 'deactivated';
        return redirect()->back()->with('success', "User {$status} successfully.");
    }

    /**
     * Display all hostels.
     */
    public function index(): View
    {
        $query = Hostel::query();

        if (request('status') === 'approved') {
            $query->where('is_approved', true);
        } elseif (request('status') === 'pending') {
            $query->where('is_approved', false);
        }

        $hostels = $query->paginate(15);
        return view('admin.hostels.hostel', compact('hostels'));
    }

    /**
     * Store a new hostel.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string',
            'manager_id' => 'nullable|exists:users,id',
            'is_approved' => 'required|boolean',
            'description' => 'nullable|string',
        ]);

        Hostel::create($request->only(['name', 'location', 'manager_id', 'is_approved', 'description']));

        return redirect()->route('admin.hostels.index')->with('success', 'Hostel created successfully.');
    }

    /**
     * Display the specified hostel.
     */
    public function show(Hostel $hostel): View
    {
        return view('admin.hostel-show', compact('hostel'));
    }

    /**
     * Show the form for editing the specified hostel.
     */
    public function edit(Hostel $hostel): View
    {
        return view('admin.hostel-edit', compact('hostel'));
    }

    /**
     * Update the specified hostel.
     */
    public function update(Request $request, Hostel $hostel): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string',
            'manager_id' => 'nullable|exists:users,id',
            'is_approved' => 'required|boolean',
            'description' => 'nullable|string',
        ]);

        $hostel->update($request->only(['name', 'location', 'manager_id', 'is_approved', 'description']));

        return redirect()->route('admin.hostels.index')->with('success', 'Hostel updated successfully.');
    }

    /**
     * Show the form to assign a hostel to a user.
     */
    public function assignHostelForm(User $user): View
    {
        $hostels = Hostel::approved()->get();
        return view('admin.assign-hostel', compact('user', 'hostels'));
    }

    /**
     * Assign a hostel to a user.
     */
    public function assignHostel(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'hostel_id' => 'required|exists:hostels,id',
        ]);

        $user->update(['hostel_id' => $request->hostel_id]);

        return redirect()->route('user.index')->with('success', 'Hostel assigned successfully.');
    }

    public function bookings(): View
    {
        $bookings = Booking::with(['user', 'hostel'])->latest()->paginate(15);
        return view('admin.booking', compact('bookings'));
    }

    /**
     * Display reports and analytics.
     */
    public function reports(): View
    {
        // Revenue by month (last 12 months)
        $revenueByMonth = DB::table('bookings')
            ->selectRaw("strftime('%Y', created_at) as year, strftime('%m', created_at) as month, SUM(total_amount) as revenue")
            ->where('payment_status', 'paid')
            ->where('created_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        // Top 10 hostels by bookings
        $bookingsByHostel = DB::table('bookings')
            ->join('hostels', 'bookings.hostel_id', '=', 'hostels.id')
            ->selectRaw('hostels.name, COUNT(bookings.id) as bookings_count')
            ->groupBy('hostels.id', 'hostels.name')
            ->orderBy('bookings_count', 'desc')
            ->limit(10)
            ->get();

        // User registrations (last 30 days)
        $userRegistrations = DB::table('users')
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        return view('admin.report', compact('revenueByMonth', 'bookingsByHostel', 'userRegistrations'));
    }
}
