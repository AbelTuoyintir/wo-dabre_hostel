<?php
// app/Http/Controllers/Agent/HostelManagementController.php
namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Hostel;
use App\Models\Room;
use App\Models\Amenity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class HostelManagementController extends Controller
{
    public function index()
    {
        $agent = Auth::user()->agent;
        
        $hostels = Hostel::where('agent_id', $agent->id)
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
            'amenities' => 'array',
            'images.*' => 'image|max:5120',
            'featured_image' => 'required|image|max:5120'
        ]);

        $agent = Auth::user()->agent;

        // Upload featured image
        $featuredPath = $request->file('featured_image')->store('hostels', 'public');

        $hostel = Hostel::create([
            'agent_id' => $agent->id,
            'name' => $request->name,
            'description' => $request->description,
            'location' => $request->location,
            'address' => $request->address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'featured_image' => $featuredPath,
            'status' => 'pending',
            'is_verified' => false
        ]);

        // Attach amenities
        if ($request->amenities) {
            $hostel->amenities()->attach($request->amenities);
        }

        // Upload gallery images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('hostels/gallery', 'public');
                $hostel->images()->create(['image_path' => $path]);
            }
        }

        // Add commission for hostel addition
        $agent->addCommission(
            100.00,
            'hostel_added',
            "Commission for adding hostel: {$hostel->name}",
            $hostel->id
        );
        $agent->increment('total_hostels_added');

        return redirect()->route('agent.hostels.show', $hostel->id)
            ->with('success', 'Hostel created successfully! Pending admin approval.');
    }

    public function show($id)
    {
        $hostel = Hostel::where('agent_id', Auth::user()->agent->id)
            ->with(['rooms', 'amenities', 'images'])
            ->findOrFail($id);

        return view('agent.hostels.show', compact('hostel'));
    }

    public function addRoom(Request $request, $hostelId)
    {
        $request->validate([
            'room_number' => 'required|string',
            'room_type' => 'required|string',
            'capacity' => 'required|integer|min:1',
            'price_per_year' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'is_available' => 'boolean'
        ]);

        $hostel = Hostel::where('agent_id', Auth::user()->agent->id)->findOrFail($hostelId);

        $room = Room::create([
            'hostel_id' => $hostel->id,
            'room_number' => $request->room_number,
            'room_type' => $request->room_type,
            'capacity' => $request->capacity,
            'price_per_year' => $request->price_per_year,
            'description' => $request->description,
            'is_available' => $request->is_available ?? true
        ]);

        // Add commission for room addition
        $agent = Auth::user()->agent;
        $agent->addCommission(
            20.00,
            'room_added',
            "Commission for adding room {$room->room_number} in {$hostel->name}",
            $room->id
        );
        $agent->increment('total_rooms_added');

        return redirect()->route('agent.hostels.show', $hostel->id)
            ->with('success', 'Room added successfully!');
    }
}