<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Hostel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoomController extends Controller
{
    /**
     * Display a listing of rooms
     */
    public function index(Request $request)
    {
        // Admin only - no manager checks needed
        $query = Room::with('hostel');
        
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
        
        if ($request->filled('furnished')) {
            $query->where('furnished', $request->furnished);
        }
        
        if ($request->filled('private_bathroom')) {
            $query->where('private_bathroom', $request->private_bathroom);
        }
        
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('hostel', function($hq) use ($request) {
                      $hq->where('name', 'like', '%' . $request->search . '%');
                  });
            });
        }
        
        $rooms = $query->latest()->paginate(15)->withQueryString();
        
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
        $validated = $request->validate([
            'number' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'hostel_id' => 'required|exists:hostels,id',
            'gender' => 'required|in:male,female,any',
            'status' => 'required|in:available,full,maintenance,inactive',
            'price_per_month' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'floor' => 'nullable|integer|min:0',
            'size_sqm' => 'nullable|numeric|min:1',
            'window_type' => 'nullable|in:street,courtyard,garden,none',
            'furnished' => 'sometimes|boolean',
            'private_bathroom' => 'sometimes|boolean',
        ]);

        // Handle boolean fields
        $validated['furnished'] = $request->has('furnished');
        $validated['private_bathroom'] = $request->has('private_bathroom');
        
        // Check if room number already exists in this hostel
        $exists = Room::where('hostel_id', $validated['hostel_id'])
            ->where('number', $validated['number'])
            ->exists();
            
        if ($exists) {
            return back()
                ->withInput()
                ->with('error', 'Room number already exists in this hostel.');
        }
        
        $room = Room::create($validated);
        
        // Update hostel available rooms count
        $this->updateHostelRoomCount($room->hostel);

        return redirect()
            ->route('admin.rooms.index')
            ->with('success', "Room {$room->number} created successfully.");
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
                ->where('check_out', '>', now())
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
        $validated = $request->validate([
            'number' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'hostel_id' => 'required|exists:hostels,id',
            'gender' => 'required|in:male,female,any',
            'status' => 'required|in:available,full,maintenance,inactive',
            'price_per_month' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'floor' => 'nullable|integer|min:0',
            'size_sqm' => 'nullable|numeric|min:1',
            'window_type' => 'nullable|in:street,courtyard,garden,none',
            'furnished' => 'sometimes|boolean',
            'private_bathroom' => 'sometimes|boolean',
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
                return back()
                    ->withInput()
                    ->with('error', 'Room number already exists in this hostel.');
            }
        }
        
        $oldHostelId = $room->hostel_id;
        $room->update($validated);
        
        // Update hostel room counts
        if ($oldHostelId != $room->hostel_id) {
            $this->updateHostelRoomCount(Hostel::find($oldHostelId));
            $this->updateHostelRoomCount($room->hostel);
        } else {
            $this->updateHostelRoomCount($room->hostel);
        }

        return redirect()
            ->route('admin.rooms.index')
            ->with('success', "Room {$room->number} updated successfully.");
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
        
        $hostel = $room->hostel;
        $roomNumber = $room->number;
        $room->delete();
        
        // Update hostel room count
        if ($hostel) {
            $this->updateHostelRoomCount($hostel);
        }

        return redirect()
            ->route('admin.rooms.index')
            ->with('success', "Room {$roomNumber} deleted successfully.");
    }

    /**
     * Update hostel available rooms count
     */
    private function updateHostelRoomCount(Hostel $hostel)
    {
        $hostel->total_rooms = $hostel->rooms()->count();
        $hostel->available_rooms = $hostel->rooms()
            ->where('status', 'available')
            ->whereColumn('current_occupancy', '<', 'capacity')
            ->count();
        $hostel->save();
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
        
        // Update hostel available rooms
        $this->updateHostelRoomCount($room->hostel);
        
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
                    $room->update(['price_per_month' => $request->value]);
                    break;
            }
            
            // Track hostels that need count updates
            $updatedHostels->push($room->hostel_id);
            if (isset($oldHostelId) && $oldHostelId != $room->hostel_id) {
                $updatedHostels->push($oldHostelId);
            }
        }
        
        // Update room counts for affected hostels
        foreach ($updatedHostels->unique() as $hostelId) {
            $hostel = Hostel::find($hostelId);
            if ($hostel) {
                $this->updateHostelRoomCount($hostel);
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
    public function export(Request $request)
    {
        $query = Room::with('hostel');
        
        if ($request->filled('hostel_id')) {
            $query->where('hostel_id', $request->hostel_id);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $rooms = $query->get();
        
        $filename = 'rooms-export-' . now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        
        $callback = function() use ($rooms) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'ID', 'Room Number', 'Hostel', 'Capacity', 'Current Occupancy',
                'Floor', 'Size (sqm)', 'Gender', 'Status', 'Price/Month',
                'Furnished', 'Private Bathroom', 'Window Type', 'Created At'
            ]);
            
            foreach ($rooms as $room) {
                fputcsv($file, [
                    $room->id,
                    $room->number,
                    $room->hostel->name ?? 'N/A',
                    $room->capacity,
                    $room->current_occupancy,
                    $room->floor ?? 'N/A',
                    $room->size_sqm ?? 'N/A',
                    $room->gender,
                    $room->status,
                    $room->price_per_month ?? 'N/A',
                    $room->furnished ? 'Yes' : 'No',
                    $room->private_bathroom ? 'Yes' : 'No',
                    $room->window_type ?? 'N/A',
                    $room->created_at->format('Y-m-d H:i:s')
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}


