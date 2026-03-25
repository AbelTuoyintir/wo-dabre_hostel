<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Hostel;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
            'address' => 'required|string',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email|max:255',
            'manager_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
            'cover_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240', // 10MB max
            'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240', // 10MB max per image
            'gallery_images' => 'nullable|array|max:5', // Maximum 5 gallery images
        ]);

        // Prepare data for creation
        $data = $request->only([
            'name',
            'location',
            'address',
            'contact_phone',
            'contact_email',
            'manager_id',
            'description'
        ]);

        // Set default values
        $data['is_approved'] = false; // Default to not approved

        // Create the hostel
        $hostel = Hostel::create($data);

        // Handle cover image upload (primary image)
        if ($request->hasFile('cover_image')) {
            $coverPath = $request->file('cover_image')->store('hostels/covers', 'public');

            // Save as primary image
            $hostel->images()->create([
                'image_path' => $coverPath,
                'type' => 'hostel',
                'is_primary' => true,
                'order' => 0
            ]);
        }

        // Handle gallery images upload
        if ($request->hasFile('gallery_images')) {
            $order = 1; // Start from 1 since cover image is at 0
            foreach ($request->file('gallery_images') as $image) {
                $path = $image->store('hostels/gallery', 'public');

                // Save as gallery image
                $hostel->images()->create([
                    'image_path' => $path,
                    'type' => 'hostel',
                    'is_primary' => false,
                    'order' => $order++
                ]);
            }
        }

        return redirect()->route('admin.hostels.index')
            ->with('success', 'Hostel created successfully with images.');
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
    // public function edit(Hostel $hostel): View
    // {

    //     return view('admin.hostels.edit', compact('hostel'));
    // }

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

    //    public function indexbooking()
    // {
    //     $user = Auth::user();
    //     $bookings = Booking::where('user_id', $user->id)
    //         ->with(['room.hostel'])
    //         ->orderBy('created_at', 'desc')
    //         ->paginate(10);

    //     return view('hostel-manager.bookings.index', compact('bookings'));
    // }

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
        $bookings = $this->bookingsQuery(request())
            ->paginate(15)
            ->withQueryString();

        return view('admin.bookings.booking', compact('bookings'));
    }

    public function exportReport(Request $request, string $type): Response|RedirectResponse|StreamedResponse
    {
        if ($type !== 'bookings') {
            return redirect()->back()->with('error', 'That report export is not available yet.');
        }

        $format = strtolower((string) $request->get('format', 'csv'));
        $bookings = $this->bookingsQuery($request)->get();

        if ($format === 'pdf') {
            return $this->downloadBookingsPdf($bookings);
        }

        return $this->downloadBookingsCsv($bookings);
    }

    public function exportBookings(Request $request): Response|RedirectResponse|StreamedResponse
    {
        return $this->exportReport($request, 'bookings');
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
->selectRaw('hostels.id, hostels.name, COUNT(bookings.id) as bookings_count')
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

    private function bookingsQuery(Request $request): Builder
    {
        $query = Booking::with(['user', 'hostel', 'room'])->latest();

        if ($request->filled('status')) {
            $query->where('booking_status', $request->status);
        }

        if ($request->filled('payment')) {
            $query->where('payment_status', $request->payment);
        }

        if ($request->filled('from')) {
            $query->whereDate('check_in_date', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('check_out_date', '<=', $request->to);
        }

        return $query;
    }

    private function downloadBookingsCsv(Collection $bookings): StreamedResponse
    {
        $filename = 'bookings-report-' . now()->format('Y-m-d-His') . '.csv';

        return response()->streamDownload(function () use ($bookings) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, [
                'Booking Number',
                'Customer Name',
                'Customer Email',
                'Hostel',
                'Room',
                'Check In',
                'Check Out',
                'Total Amount',
                'Amount Paid',
                'Balance Due',
                'Booking Status',
                'Payment Status',
                'Payment Method',
                'Created At',
            ]);

            foreach ($bookings as $booking) {
                fputcsv($file, [
                    $booking->booking_number,
                    $booking->user?->name ?? 'N/A',
                    $booking->user?->email ?? 'N/A',
                    $booking->hostel?->name ?? 'N/A',
                    $booking->room?->number ?? $booking->room_number ?? 'N/A',
                    optional($booking->check_in_date)->format('Y-m-d'),
                    optional($booking->check_out_date)->format('Y-m-d'),
                    number_format((float) $booking->total_amount, 2, '.', ''),
                    number_format((float) $booking->amount_paid, 2, '.', ''),
                    number_format((float) $booking->balance_due, 2, '.', ''),
                    $booking->booking_status,
                    $booking->payment_status,
                    $booking->payment_method ?? 'N/A',
                    optional($booking->created_at)->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function downloadBookingsPdf(Collection $bookings): Response
    {
        $lines = [
            'Bookings Report',
            'Generated: ' . now()->format('Y-m-d H:i:s'),
            'Total records: ' . $bookings->count(),
            str_repeat('=', 95),
        ];

        foreach ($bookings as $booking) {
            $customer = $booking->user?->name ?? 'N/A';
            $email = $booking->user?->email ?? 'N/A';
            $hostel = $booking->hostel?->name ?? 'N/A';
            $room = $booking->room?->number ?? $booking->room_number ?? 'N/A';
            $stay = trim(
                (optional($booking->check_in_date)->format('Y-m-d') ?? 'N/A')
                . ' to '
                . (optional($booking->check_out_date)->format('Y-m-d') ?? 'N/A')
            );

            $entryLines = [
                'Booking: ' . ($booking->booking_number ?? 'N/A') . ' | Customer: ' . $customer,
                'Email: ' . $email,
                'Hostel: ' . $hostel . ' | Room: ' . $room,
                'Stay: ' . $stay,
                'Amount: GHS ' . number_format((float) $booking->total_amount, 2)
                . ' | Paid: GHS ' . number_format((float) $booking->amount_paid, 2)
                . ' | Balance: GHS ' . number_format((float) $booking->balance_due, 2),
                'Status: ' . ucfirst((string) $booking->booking_status)
                . ' | Payment: ' . ucfirst((string) $booking->payment_status),
                str_repeat('-', 95),
            ];

            foreach ($entryLines as $entryLine) {
                foreach (explode("\n", wordwrap($entryLine, 95, "\n", true)) as $wrappedLine) {
                    $lines[] = $wrappedLine;
                }
            }
        }

        $pdfContent = $this->buildSimplePdf($lines);
        $filename = 'bookings-report-' . now()->format('Y-m-d-His') . '.pdf';

        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    private function buildSimplePdf(array $lines): string
    {
        $linesPerPage = 48;
        $pages = array_chunk($lines, $linesPerPage);
        $objects = [
            1 => '<< /Type /Catalog /Pages 2 0 R >>',
            3 => '<< /Type /Font /Subtype /Type1 /BaseFont /Courier >>',
        ];

        $kids = [];
        $objectNumber = 4;

        foreach ($pages as $pageLines) {
            $pageObjectNumber = $objectNumber++;
            $contentObjectNumber = $objectNumber++;
            $kids[] = $pageObjectNumber . ' 0 R';

            $streamLines = [
                'BT',
                '/F1 10 Tf',
                '40 780 Td',
                '14 TL',
            ];

            foreach ($pageLines as $line) {
                $streamLines[] = '(' . $this->escapePdfText($line) . ') Tj';
                $streamLines[] = 'T*';
            }

            $streamLines[] = 'ET';
            $stream = implode("\n", $streamLines);

            $objects[$pageObjectNumber] = '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] '
                . '/Resources << /Font << /F1 3 0 R >> >> /Contents ' . $contentObjectNumber . ' 0 R >>';

            $objects[$contentObjectNumber] = '<< /Length ' . strlen($stream) . " >>\nstream\n"
                . $stream . "\nendstream";
        }

        $objects[2] = '<< /Type /Pages /Kids [' . implode(' ', $kids) . '] /Count ' . count($kids) . ' >>';
        ksort($objects);

        $pdf = "%PDF-1.4\n";
        $offsets = [0 => 0];

        foreach ($objects as $number => $object) {
            $offsets[$number] = strlen($pdf);
            $pdf .= $number . " 0 obj\n" . $object . "\nendobj\n";
        }

        $xrefOffset = strlen($pdf);
        $maxObject = max(array_keys($objects));

        $pdf .= "xref\n0 " . ($maxObject + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";

        for ($i = 1; $i <= $maxObject; $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$i] ?? 0);
        }

        $pdf .= "trailer\n<< /Size " . ($maxObject + 1) . " /Root 1 0 R >>\n";
        $pdf .= "startxref\n" . $xrefOffset . "\n%%EOF";

        return $pdf;
    }

    private function escapePdfText(string $text): string
    {
        $encoded = @iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $text);

        if ($encoded === false) {
            $encoded = preg_replace('/[^\x20-\x7E]/', '?', $text) ?? $text;
        }

        return str_replace(
            ["\\", "(", ")", "\r"],
            ["\\\\", "\\(", "\\)", ''],
            $encoded
        );
    }
}
