<?php
// app/Http/Controllers/Agent/HostelManagementController.php
namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Amenity;
use App\Models\Hostel;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class HostelManagementController extends Controller
{
    public function index()
    {
        $agent = Auth::user()->agent;

        $hostels = $this->getAgentHostelQuery($agent)
            ->withCount('rooms')
            ->latest()
            ->paginate(10);

        return view('agent.hostels.index', compact('hostels'));
    }

    public function create()
    {
        $amenities = Amenity::all();
        return view('agent.hostels.create', compact('amenities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string',
            'address' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'agent_fee' => 'nullable|numeric|min:0',
'amenities' => 'array',
            'images.*' => 'mimetypes:image/*,video/*|max:102400',
            'featured_image' => 'required|image|max:5120'
        ]);

        $agent = Auth::user()->agent;

        $hostelData = [
            'name' => $request->name,
            'description' => $request->description,
            'location' => $request->location,
            'address' => $request->address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'status' => 'pending',
            'is_verified' => false
        ];

        if (Schema::hasColumn('hostels', 'agent_id')) {
            $hostelData['agent_id'] = $agent->id;
        } elseif (Schema::hasColumn('hostels', 'hostel_agent_id')) {
            $hostelData['hostel_agent_id'] = $agent->id;
        } elseif (Schema::hasColumn('hostels', 'user_id')) {
            $hostelData['user_id'] = $agent->user_id;
        }

        $hostel = Hostel::create($hostelData);

        // Upload featured image as primary image in hostel_images table
        if ($request->hasFile('featured_image')) {
            $featuredPath = $request->file('featured_image')->store('hostels/covers', 'public');
            $hostel->images()->create([
                'image_path' => $featuredPath,
                'type' => 'hostel',
                'is_primary' => true,
                'media_kind' => 'image',
                'order' => 0
            ]);
        }

        // Attach amenities
        if ($request->amenities) {
            $hostel->amenities()->attach($request->amenities);
        }

// Upload gallery images/videos
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('hostels/gallery', 'public');

                $mediaKind = str_starts_with($file->getMimeType() ?? '', 'video/') ? 'video' : 'image';

$hostel->images()->create([
                    'image_path' => $path,
                    'media_kind' => $mediaKind,
                    'is_primary' => false,
                    'order' => 0,
                    'type' => 'hostel',
                ]);
            }
        }

        // Agent commissions must come from booking agent fee, not from adding/registering a hostel.
        // Therefore, DO NOT create an 'hostel_added' commission here.
        $agent->increment('total_hostels_added');

        return redirect()->route('agent.hostels.show', $hostel)
            ->with('success', 'Hostel created successfully! Pending admin approval.');
    }

    public function show(Hostel $hostel)
    {
        $agent = Auth::user()->agent;

        if (!$agent || !$this->getAgentHostelQuery($agent)->where('id', $hostel->id)->exists()) {
            abort(403, 'Unauthorized access to this hostel.');
        }

        $hostel->load(['rooms', 'amenities', 'images']);

        return view('agent.hostels.show', compact('hostel'));
    }

    public function createRoom(Request $request, Hostel $hostel = null)
    {
        $agent = Auth::user()->agent;
        if (!$agent) {
            abort(403, 'Unauthorized.');
        }

        $hostels = $this->getAgentHostelQuery($agent)->get();

        if ($hostel && $hostel->exists) {
            if (!$this->getAgentHostelQuery($agent)->where('id', $hostel->id)->exists()) {
                abort(403, 'Unauthorized access to this hostel.');
            }
        } elseif ($request->filled('hostel_id')) {
            $selected = Hostel::where('uuid', $request->hostel_id)
                ->orWhere('id', $request->hostel_id)
                ->first();
            if ($selected && $this->getAgentHostelQuery($agent)->where('id', $selected->id)->exists()) {
                $hostel = $selected;
            }
        }

        return view('agent.rooms.create', compact('hostels', 'hostel'));
    }

    public function storeRoom(Request $request)
    {
        $agent = Auth::user()->agent;
        if (!$agent) {
            abort(403, 'Unauthorized.');
        }

        $request->validate([
            'hostel_id' => 'required',
            'room_number' => 'required_without:number|nullable|string',
            'number' => 'required_without:room_number|nullable|string',
            'room_type' => 'required|string',
            'capacity' => 'required|integer|min:1',
            'price_per_year' => 'required_without:room_cost|nullable|numeric|min:0',
            'room_cost' => 'required_without:price_per_year|nullable|numeric|min:0',
            'description' => 'nullable|string',
            'status' => 'nullable|string',
            'gender' => 'nullable|string',
            'floor' => 'nullable|integer',
            'size_sqm' => 'nullable|numeric',
            'window_type' => 'nullable|string',
            'furnished' => 'sometimes|boolean',
            'private_bathroom' => 'sometimes|boolean',
            'cover_image' => 'nullable|image|max:10240',
            'gallery_images.*' => 'nullable|image|max:10240',
            'room_video' => 'nullable|mimetypes:video/*|max:51200',
        ]);

        $hostel = Hostel::where('uuid', $request->hostel_id)
            ->orWhere('id', $request->hostel_id)
            ->first();

        if (!$hostel || !$this->getAgentHostelQuery($agent)->where('id', $hostel->id)->exists()) {
            abort(403, 'Unauthorized access to this hostel.');
        }

        $roomNumber = $request->input('number', $request->input('room_number'));
        $price = $request->input('room_cost', $request->input('price_per_year'));

        // Validate duplicate room number
        $exists = Room::where('hostel_id', $hostel->id)
            ->where('number', $roomNumber)
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->with('error', 'Room number already exists in this hostel.');
        }

        $status = $request->input('status');
        if (!$status) {
            $status = $request->has('is_available') ? ($request->boolean('is_available') ? 'available' : 'unavailable') : 'available';
        }

        $room = Room::create([
            'hostel_id' => $hostel->id,
            'number' => $roomNumber,
            'room_type' => $request->room_type,
            'capacity' => $request->capacity,
            'room_cost' => $price,
            'description' => $request->description,
            'status' => $status,
            'gender' => $request->input('gender', 'any'),
            'floor' => $request->input('floor'),
            'size_sqm' => $request->input('size_sqm'),
            'window_type' => $request->input('window_type'),
            'furnished' => $request->boolean('furnished'),
            'private_bathroom' => $request->boolean('private_bathroom'),
        ]);

        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('rooms/covers', 'public');
            $room->images()->create([
                'image_path' => $path,
                'type' => 'room',
                'is_primary' => true,
                'media_kind' => 'image',
                'order' => 0,
                'hostel_id' => $hostel->id,
            ]);
        }

        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $file) {
                $path = $file->store('rooms/gallery', 'public');
                $room->images()->create([
                    'image_path' => $path,
                    'type' => 'room',
                    'is_primary' => false,
                    'media_kind' => 'image',
                    'order' => 0,
                    'hostel_id' => $hostel->id,
                ]);
            }
        }

        if ($request->hasFile('room_video')) {
            $path = $request->file('room_video')->store('rooms/videos', 'public');
            $room->images()->create([
                'image_path' => $path,
                'type' => 'room',
                'is_primary' => false,
                'media_kind' => 'video',
                'order' => 0,
                'hostel_id' => $hostel->id,
            ]);
        }

        // Add commission for room addition
        $agent->addCommission(
            20.00,
            'room_added',
            "Commission for adding room {$room->number} in {$hostel->name}",
            $room->id
        );
        $agent->increment('total_rooms_added');

        return redirect()->route('agent.hostels.show', $hostel->uuid ?? $hostel->id)
            ->with('success', 'Room created successfully!');
    }

    private function getAgentHostelQuery($agent)
    {
        $query = Hostel::query();

        if (Schema::hasColumn('hostels', 'agent_id')) {
            return $query->where('agent_id', $agent->id);
        }

        if (Schema::hasColumn('hostels', 'hostel_agent_id')) {
            return $query->where('hostel_agent_id', $agent->id);
        }

        if (Schema::hasColumn('hostels', 'user_id')) {
            return $query->where('user_id', $agent->user_id);
        }

        return $query->whereRaw('1 = 0');
    }

    public function addRoom(Request $request, Hostel $hostel)
    {
        $request->validate([
            'room_number' => 'required|string',
            'room_type' => 'required|string',
            'capacity' => 'required|integer|min:1',
            'price_per_year' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'is_available' => 'sometimes|boolean'
        ]);

        $agent = Auth::user()->agent;

        // Ensure the hostel belongs to this agent
        if (!$this->getAgentHostelQuery($agent)->where('id', $hostel->id)->exists()) {
            abort(403, 'Unauthorized.');
        }

        // Validate uniqueness of room number in this hostel
        $exists = Room::where('hostel_id', $hostel->id)
            ->where('number', $request->room_number)
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->with('error', 'Room number already exists in this hostel.');
        }

        $room = Room::create([
            'hostel_id' => $hostel->id,
            'number' => $request->room_number,
            'room_type' => $request->room_type,
            'capacity' => $request->capacity,
            'room_cost' => $request->price_per_year,
            'description' => $request->description,
            'status' => $request->has('is_available') ? 'available' : 'unavailable',
            'gender' => 'any'
        ]);

        // Add commission for room addition
        $agent->addCommission(
            20.00,
            'room_added',
            "Commission for adding room {$room->number} in {$hostel->name}",
            $room->id
        );
        $agent->increment('total_rooms_added');

        return redirect()->route('agent.hostels.show', $hostel->uuid)
            ->with('success', 'Room added successfully!');
    }

    public function deleteRoom(Hostel $hostel, Room $room)
    {
        $agent = Auth::user()->agent;

        // Ensure the hostel belongs to this agent
        if (!$this->getAgentHostelQuery($agent)->where('id', $hostel->id)->exists()) {
            abort(403, 'Unauthorized.');
        }

        // Ensure the room belongs to this hostel
        if ($room->hostel_id !== $hostel->id) {
            abort(404, 'Room not found in this hostel.');
        }

        // Check for active/confirmed/pending bookings on this room
        $hasActiveBookings = $room->bookings()
            ->whereIn('booking_status', ['confirmed', 'pending', 'checked_in'])
            ->exists();

        if ($hasActiveBookings) {
            return back()->with('error', 'Cannot delete a room with active bookings.');
        }

        $room->delete();

        $agent->decrement('total_rooms_added');

        return redirect()->route('agent.hostels.show', $hostel->uuid)
            ->with('success', 'Room deleted successfully!');
    }
}