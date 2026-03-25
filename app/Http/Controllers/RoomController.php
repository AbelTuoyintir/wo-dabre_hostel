<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Hostel;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\HostelImage;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RoomController extends Controller
{
    /**
     * Display a listing of rooms
     */
    public function index(Request $request)
    {
        // Admin only - no manager checks needed
        $rooms = $this->roomsQuery($request)
            ->latest()
            ->paginate(15)
            ->withQueryString();

        // Get all hostels for filter dropdown
        $hostels = Hostel::all();

        return view('admin.rooms.index', compact('rooms', 'hostels'));
    }

    /**
     * Show form for creating a new room
     */
    public function create()
    {
        // Get all hostels for selection
        $hostels = Hostel::all();

        if ($hostels->isEmpty()) {
            return redirect()
                ->route('admin.hostels.index')
                ->with('error', 'You need to create a hostel first before adding rooms.');
        }

        return view('admin.rooms.create', compact('hostels'));
    }

    /**
     * Store a newly created room
     */
   public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'number' => 'required|string|max:255',
                'capacity' => 'required|integer|min:1',
                'hostel_id' => 'required|exists:hostels,id',
                'gender' => 'required|in:male,female,any',
                'room_type' => 'required|in:single_room,shared_2,shared_4,executive',
                'status' => 'required|in:available,full,maintenance,inactive',
                'room_cost' => 'nullable|numeric|min:0',
                'description' => 'nullable|string|max:1000',
                'floor' => 'nullable|integer|min:0',
                'size_sqm' => 'nullable|numeric|min:1',
                'window_type' => 'nullable|in:street,courtyard,garden,none',
                'furnished' => 'sometimes|boolean',
                'private_bathroom' => 'sometimes|boolean',
                // Image validation rules
                'cover_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240', // 10MB max, required
                'gallery_images' => 'nullable|array|max:5',
                'gallery_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:10240', // 10MB max per image
            ]);

            // Handle boolean fields
            $validated['furnished'] = $request->has('furnished');
            $validated['private_bathroom'] = $request->has('private_bathroom');

            // Check if room number already exists in the same hostel
            $exists = Room::where('hostel_id', $validated['hostel_id'])
                ->where('number', $validated['number'])
                ->exists();

            if ($exists) {
                \Log::warning('Room creation failed: Room number already exists', [
                    'room_number' => $validated['number'],
                    'hostel_id' => $validated['hostel_id']
                ]);

                return back()
                    ->withInput()
                    ->with('error', 'Room number already exists in this hostel.');
            }

            // Begin database transaction
            \DB::beginTransaction();

            try {
                // Create the room
                $room = Room::create($validated);

// Handle cover image upload
                if ($request->hasFile('cover_image')) {
                    $path = $request->file('cover_image')->store('rooms/covers', 'public');

                    // Create primary image
                    $room->images()->create([
                        'image_path' => $path,
                        'hostel_id' => $room->hostel_id,
                        'type' => 'room',
                        'is_primary' => true,
                        'order' => 0
                    ]);

                    \Log::info('Uploaded cover image for new room', [
                        'room_id' => $room->id,
                        'image_path' => $path
                    ]);
                }

                // Handle gallery images upload
                if ($request->hasFile('gallery_images')) {
                    $order = 1; // Start from 1 since cover image is at order 0
                    $uploadedCount = 0;

                    foreach ($request->file('gallery_images') as $image) {
                        if ($uploadedCount >= 5) break; // Limit to 5 images

                        $path = $image->store('rooms/gallery', 'public');

$room->images()->create([
                            'image_path' => $path,
                            'hostel_id' => $room->hostel_id,
                            'type' => 'room',
                            'is_primary' => false,
                            'order' => $order++
                        ]);

                        $uploadedCount++;
                    }

                    \Log::info('Uploaded gallery images for new room', [
                        'room_id' => $room->id,
                        'uploaded_count' => $uploadedCount
                    ]);
                }

                \DB::commit();

                // Log successful creation
                \Log::info('Room created successfully with images', [
                    'room_id' => $room->id,
                    'room_number' => $room->number,
                    'hostel_id' => $room->hostel_id,
                    'created_by' => auth()->id()
                ]);

                return redirect()
                    ->route('admin.rooms.index')
                    ->with('success', "Room {$room->number} created successfully.");

            } catch (\Exception $e) {
                \DB::rollBack();

                // Delete uploaded images if any (cleanup)
                // This ensures we don't have orphaned files if database transaction fails
                if (isset($room)) {
                    $images = $room->images;
                    foreach ($images as $image) {
                        \Storage::disk('public')->delete($image->image_path);
                    }
                }

                // Log database error
                \Log::error('Database error while creating room', [
                    'error_message' => $e->getMessage(),
                    'error_code' => $e->getCode(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ]);

                return back()
                    ->withInput()
                    ->with('error', 'Failed to create room due to a database error. Please try again.');
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Log validation errors
            \Log::warning('Room creation validation failed', [
                'errors' => $e->errors(),
                'input' => $request->except(['_token', '_method', 'cover_image', 'gallery_images'])
            ]);

            // Re-throw to let Laravel handle validation redirect
            throw $e;

        } catch (\Exception $e) {
            // Log any unexpected errors
            \Log::critical('Unexpected error while creating room', [
                'error_message' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except(['_token', '_method', 'cover_image', 'gallery_images'])
            ]);

            return back()
                ->withInput()
                ->with('error', 'An unexpected error occurred. Please try again later.');
        }
    }


    /**
     * Display a specific room
     */
    public function show(Room $room)
    {
        $room->load(['hostel', 'bookings' => function($query) {
            $query->with('user')
                  ->latest()
                  ->limit(10);
        }]);

        $stats = [
            'total_bookings' => $room->bookings()->count(),
            'active_bookings' => $room->bookings()
                ->whereIn('status', ['confirmed', 'pending'])
                ->where('check_out_date', '>', now())
                ->count(),
            'occupancy_rate' => $room->occupancyRate(),
            'available_spaces' => $room->availableSpaces(),
        ];

        $currentBooking = $room->currentBooking()->with('user')->first();


        return view('admin.rooms.show', compact('room', 'stats', 'currentBooking'));
    }

    /**
     * Show form for editing a room
     */
    public function edit(Room $room)
    {
        // Get all hostels for selection
        $hostels = Hostel::all();

        return view('admin.rooms.edit', compact('room', 'hostels'));
    }

    /**
     * Update a room
     */
    public function update(Request $request, Room $room)
    {
        try {
            $validated = $request->validate([
                'number' => 'required|string|max:255',
                'capacity' => 'required|integer|min:1',
                'hostel_id' => 'required|exists:hostels,id',
                'gender' => 'required|in:male,female,any',
                'room_type' => 'required|in:single_room,shared_2,shared_4,executive',
                'status' => 'required|in:available,full,maintenance,inactive',
                'room_cost' => 'nullable|numeric|min:0',
                'description' => 'nullable|string|max:1000',
                'floor' => 'nullable|integer|min:0',
                'size_sqm' => 'nullable|numeric|min:1',
                'window_type' => 'nullable|in:street,courtyard,garden,none',
                'furnished' => 'sometimes|boolean',
                'private_bathroom' => 'sometimes|boolean',
                // Image validation rules
                'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
                'gallery_images' => 'nullable|array|max:5',
                'gallery_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:10240',
                'removed_images' => 'nullable|array',
                'removed_images.*' => 'exists:hostel_images,id',
                'primary_image_id' => 'nullable|exists:hostel_images,id',
            ]);

            // Handle boolean fields
            $validated['furnished'] = $request->has('furnished');
            $validated['private_bathroom'] = $request->has('private_bathroom');

            // Check if moving to different hostel
            if ($validated['hostel_id'] != $room->hostel_id) {
                // Check room number uniqueness in new hostel
                $exists = Room::where('hostel_id', $validated['hostel_id'])
                    ->where('number', $validated['number'])
                    ->where('id', '!=', $room->id)
                    ->exists();

                if ($exists) {
                    \Log::warning('Room update failed: Room number already exists in target hostel', [
                        'room_id' => $room->id,
                        'room_number' => $validated['number'],
                        'target_hostel_id' => $validated['hostel_id']
                    ]);

                    return back()
                        ->withInput()
                        ->with('error', 'Room number already exists in the target hostel.');
                }
            } else {
                // Check room number uniqueness in same hostel (excluding current room)
                $exists = Room::where('hostel_id', $validated['hostel_id'])
                    ->where('number', $validated['number'])
                    ->where('id', '!=', $room->id)
                    ->exists();

                if ($exists) {
                    \Log::warning('Room update failed: Room number already exists in same hostel', [
                        'room_id' => $room->id,
                        'room_number' => $validated['number'],
                        'hostel_id' => $validated['hostel_id']
                    ]);

                    return back()
                        ->withInput()
                        ->with('error', 'Room number already exists in this hostel.');
                }
            }

            // Begin database transaction
            \DB::beginTransaction();

            try {
                // Update the room
                $room->update($validated);

                // Handle removed images
                if (!empty($validated['removed_images'])) {
                    $imagesToRemove = HostelImage::whereIn('id', $validated['removed_images'])
                        ->where('room_id', $room->id)
                        ->where('type', 'room')
                        ->get();

                    foreach ($imagesToRemove as $image) {
                        // Delete file from storage
                        \Storage::disk('public')->delete($image->image_path);
                        // Delete record from database
                        $image->delete();
                    }

                    \Log::info('Removed images from room', [
                        'room_id' => $room->id,
                        'removed_count' => count($validated['removed_images'])
                    ]);
                }

                // Handle primary image change
                if (!empty($validated['primary_image_id'])) {
                    // Remove primary status from all room images
                    HostelImage::where('room_id', $room->id)
                        ->where('type', 'room')
                        ->update(['is_primary' => false]);

                    // Set new primary image
                    HostelImage::where('id', $validated['primary_image_id'])
                        ->where('room_id', $room->id)
                        ->where('type', 'room')
                        ->update(['is_primary' => true, 'order' => 0]);

                    \Log::info('Changed primary image for room', [
                        'room_id' => $room->id,
                        'new_primary_image_id' => $validated['primary_image_id']
                    ]);
                }

                // Handle cover image upload
                if ($request->hasFile('cover_image')) {
                    // Optional: Remove old primary image if you want to replace it
                    $oldPrimary = HostelImage::where('room_id', $room->id)
                        ->where('type', 'room')
                        ->where('is_primary', true)
                        ->first();

                    if ($oldPrimary) {
                        \Storage::disk('public')->delete($oldPrimary->image_path);
                        $oldPrimary->delete();
                    }

// Store new cover image
                    $path = $request->file('cover_image')->store('rooms/covers', 'public');

                    // Create new primary image
                    $room->images()->create([
                        'image_path' => $path,
                        'hostel_id' => $room->hostel_id,
                        'type' => 'room',
                        'is_primary' => true,
                        'order' => 0
                    ]);

                    \Log::info('Uploaded new cover image for room', [
                        'room_id' => $room->id,
                        'image_path' => $path
                    ]);
                }

                // Handle gallery images upload
                if ($request->hasFile('gallery_images')) {
                    // Get the current max order for gallery images
                    $maxOrder = HostelImage::where('room_id', $room->id)
                        ->where('type', 'room')
                        ->where('is_primary', false)
                        ->max('order') ?? 0;

                    $order = $maxOrder + 1;
                    $uploadedCount = 0;

                    foreach ($request->file('gallery_images') as $image) {
                        if ($uploadedCount >= 5) break; // Limit to 5 images

$path = $image->store('rooms/gallery', 'public');

                        $room->images()->create([
                            'image_path' => $path,
                            'hostel_id' => $room->hostel_id,
                            'type' => 'room',
                            'is_primary' => false,
                            'order' => $order++
                        ]);

                        $uploadedCount++;
                    }

                    \Log::info('Uploaded gallery images for room', [
                        'room_id' => $room->id,
                        'uploaded_count' => $uploadedCount
                    ]);
                }

                \DB::commit();

                // Log successful update
                \Log::info('Room updated successfully with images', [
                    'room_id' => $room->id,
                    'room_number' => $room->number,
                    'hostel_id' => $room->hostel_id,
                    'updated_by' => auth()->id()
                ]);

                return redirect()
                    ->route('admin.rooms.index')
                    ->with('success', "Room {$room->number} updated successfully.");

            } catch (\Exception $e) {
                \DB::rollBack();

                // Log database error
                \Log::error('Database error while updating room', [
                    'room_id' => $room->id,
                    'error_message' => $e->getMessage(),
                    'error_code' => $e->getCode(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ]);

                return back()
                    ->withInput()
                    ->with('error', 'Failed to update room due to a database error. Please try again.');
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Log validation errors
            \Log::warning('Room update validation failed', [
                'room_id' => $room->id,
                'errors' => $e->errors(),
                'input' => $request->except(['_token', '_method', 'cover_image', 'gallery_images'])
            ]);

            // Re-throw to let Laravel handle validation redirect
            throw $e;

        } catch (\Exception $e) {
            // Log any unexpected errors
            \Log::critical('Unexpected error while updating room', [
                'room_id' => $room->id,
                'error_message' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except(['_token', '_method', 'cover_image', 'gallery_images'])
            ]);

            return back()
                ->withInput()
                ->with('error', 'An unexpected error occurred. Please try again later.');
        }
    }

    /**
     * Delete a room
     */
    public function destroy(Room $room)
    {
        // Check for active bookings
        $hasActiveBookings = $room->bookings()
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($hasActiveBookings) {
            return back()
                ->with('error', 'Cannot delete room with active bookings.');
        }

        $roomNumber = $room->number;
        $room->delete();

        return redirect()
            ->route('admin.rooms.index')
            ->with('success', "Room {$roomNumber} deleted successfully.");
    }

    /**
     * Quick status update via AJAX
     */
    public function updateStatus(Request $request, Room $room)
    {
        $request->validate([
            'status' => 'required|in:available,full,maintenance,inactive'
        ]);

        $room->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Room status updated successfully.'
        ]);
    }

    /**
     * Bulk update rooms
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'room_ids' => 'required|array',
            'room_ids.*' => 'exists:rooms,id',
            'action' => 'required|in:status,gender,hostel,capacity,price',
            'value' => 'required'
        ]);

        $rooms = Room::whereIn('id', $request->room_ids)->get();

        if ($rooms->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No rooms selected.'
            ], 400);
        }

        $updatedHostels = collect();

        foreach ($rooms as $room) {
            $oldHostelId = $room->hostel_id;

            switch ($request->action) {
                case 'status':
                    $room->update(['status' => $request->value]);
                    break;
                case 'gender':
                    $room->update(['gender' => $request->value]);
                    break;
                case 'hostel':
                    $room->update(['hostel_id' => $request->value]);
                    break;
                case 'capacity':
                    $room->update(['capacity' => $request->value]);
                    break;
                case 'price':
                    $room->update(['room_cost' => $request->value]);
                    break;
            }

            // Track hostels that need count updates
            $updatedHostels->push($room->hostel_id);
            if (isset($oldHostelId) && $oldHostelId != $room->hostel_id) {
                $updatedHostels->push($oldHostelId);
            }
        }

        return response()->json([
            'success' => true,
            'message' => count($rooms) . ' rooms updated successfully.'
        ]);
    }

    /**
     * Export rooms list
     */
    public function export(Request $request): Response|StreamedResponse
    {
        $rooms = $this->roomsQuery($request)->latest()->get();
        $format = strtolower((string) $request->get('format', 'csv'));

        if ($format === 'pdf') {
            return $this->downloadRoomsPdf($rooms);
        }

        return $this->downloadRoomsCsv($rooms);
    }

    private function roomsQuery(Request $request): Builder
    {
        $query = Room::with('hostel');

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
            $query->where('room_cost', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('room_cost', '<=', $request->max_price);
        }

        if ($request->filled('furnished')) {
            $query->where('furnished', $request->furnished);
        }

        if ($request->filled('private_bathroom')) {
            $query->where('private_bathroom', $request->private_bathroom);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('number', 'like', '%' . $request->search . '%')
                    ->orWhereHas('hostel', function ($hq) use ($request) {
                        $hq->where('name', 'like', '%' . $request->search . '%');
                    });
            });
        }

        return $query;
    }

    private function downloadRoomsCsv(Collection $rooms): StreamedResponse
    {
        $filename = 'rooms-export-' . now()->format('Y-m-d-His') . '.csv';

        return response()->streamDownload(function () use ($rooms) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, [
                'ID',
                'Room Number',
                'Hostel',
                'Capacity',
                'Current Occupancy',
                'Floor',
                'Size (sqm)',
                'Gender',
                'Status',
                'Price/Month',
                'Furnished',
                'Private Bathroom',
                'Window Type',
                'Created At',
            ]);

            foreach ($rooms as $room) {
                fputcsv($file, [
                    $room->id,
                    $room->number,
                    $room->hostel?->name ?? 'N/A',
                    $room->capacity,
                    $room->current_occupancy,
                    $room->floor ?? 'N/A',
                    $room->size_sqm ?? 'N/A',
                    $room->gender,
                    $room->status,
                    $room->room_cost ?? 'N/A',
                    $room->furnished ? 'Yes' : 'No',
                    $room->private_bathroom ? 'Yes' : 'No',
                    $room->window_type ?? 'N/A',
                    optional($room->created_at)->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function downloadRoomsPdf(Collection $rooms): Response
    {
        $lines = [
            'Rooms Report',
            'Generated: ' . now()->format('Y-m-d H:i:s'),
            'Total records: ' . $rooms->count(),
            str_repeat('=', 95),
        ];

        foreach ($rooms as $room) {
            $entryLines = [
                'Room: ' . ($room->number ?? 'N/A') . ' | Hostel: ' . ($room->hostel?->name ?? 'N/A'),
                'Status: ' . ucfirst((string) $room->status) . ' | Gender: ' . ucfirst((string) $room->gender),
                'Capacity: ' . ($room->current_occupancy ?? 0) . '/' . ($room->capacity ?? 0)
                . ' | Price: ' . ($room->room_cost !== null ? '$' . number_format((float) $room->room_cost, 2) : 'N/A'),
                'Floor: ' . ($room->floor ?? 'N/A') . ' | Size: ' . ($room->size_sqm ?? 'N/A') . ' sqm',
                'Furnished: ' . ($room->furnished ? 'Yes' : 'No')
                . ' | Private Bathroom: ' . ($room->private_bathroom ? 'Yes' : 'No'),
                'Window Type: ' . ($room->window_type ?? 'N/A')
                . ' | Created At: ' . (optional($room->created_at)->format('Y-m-d H:i:s') ?? 'N/A'),
                str_repeat('-', 95),
            ];

            foreach ($entryLines as $entryLine) {
                foreach (explode("\n", wordwrap($entryLine, 95, "\n", true)) as $wrappedLine) {
                    $lines[] = $wrappedLine;
                }
            }
        }

        $pdfContent = $this->buildSimplePdf($lines);
        $filename = 'rooms-export-' . now()->format('Y-m-d-His') . '.pdf';

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

