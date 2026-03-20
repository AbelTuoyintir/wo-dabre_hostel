<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hostel;
use App\Models\HostelImage;
use App\Models\User;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class HostelController extends Controller
{
    /**
     * Display a listing of hostels for admin
     */
    public function index(Request $request)
    {
        $query = Hostel::with(['manager', 'primaryImage', 'rooms'])
            ->withCount(['reviews', 'bookings']);

        // Filter by approval status
        if ($request->has('pending') && $request->pending == '1') {
            $query->where('is_approved', false);
        }

        // Filter by search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by location
        if ($request->filled('location')) {
            $query->where('location', 'like', "%{$request->location}%");
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by approval
        if ($request->filled('approved')) {
            $query->where('is_approved', $request->approved);
        }

        // Filter by featured
        if ($request->filled('featured')) {
            $query->where('is_featured', $request->featured);
        }

        // Filter by manager
        if ($request->filled('manager')) {
            $query->whereHas('manager', function($q) use ($request) {
                $q->where('name', 'like', "%{$request->manager}%");
            });
        }

        // Filter by minimum rating
        if ($request->filled('min_rating')) {
            $query->where('rating', '>=', $request->min_rating);
        }

        // Filter by created date
        if ($request->filled('created_after')) {
            $query->whereDate('created_at', '>=', $request->created_after);
        }

        $hostels = $query->latest()->paginate(15)->withQueryString();

        return view('admin.hostels.index', compact('hostels'));
    }

    /**
     * Show form for creating a new hostel
     */
   public function create()
    {
        // Get all managers (for the select dropdown)
        $managers = User::where('role', 'hostel_manager')->get();

        return view('admin.hostels.create', [
            'managers' => $managers
        ]);
    }


    /**
     * Store a newly created hostel
     */
    public function store(Request $request)
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

        return redirect()->route('admin.hostels.index')->with('success', 'Hostel created successfully with images.');
    }

    /**
     * Display the specified hostel
     */
    public function show(Hostel $hostel)
    {
        $hostel->load([
            'manager',
            'images',
            'rooms' => function($q) {
                $q->withCount('bookings');
            },
            'reviews.user'
        ]);

        // Get booking statistics
        $activeBookings = $hostel->bookings()
            ->whereIn('status', ['confirmed', 'pending'])
            ->where('check_out', '>', now())
            ->count();

        $pendingBookings = $hostel->bookings()
            ->where('status', 'pending')
            ->count();

        return view('admin.hostels.show', compact('hostel', 'activeBookings', 'pendingBookings'));
    }

    /**
     * Show form for editing a hostel
     */
    public function edit(Hostel $hostel)
    {
        $managers = User::where('role', 'hostel_manager')->get();
        $hostel->load('images');

        return view('admin.hostels.edit', compact('hostel', 'managers'));
    }

    /**
     * Update the specified hostel
     */
   public function update(Request $request, Hostel $hostel)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'description' => 'nullable|string',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email|max:255',
            'manager_id' => 'nullable|exists:users,id',
            'is_featured' => 'sometimes|boolean',
            'is_approved' => 'sometimes|boolean',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'gallery_images' => 'nullable|array|max:5',
            'gallery_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:10240',
            'removed_images' => 'nullable|array',
            'removed_images.*' => 'exists:hostel_images,id',
            'primary_image_id' => 'nullable|exists:hostel_images,id',
        ]);

        DB::beginTransaction();
        try {
            // Update hostel basic info
            $hostel->update([
                'name' => $validated['name'],
                'location' => $validated['location'],
                'address' => $validated['address'],
                'description' => $validated['description'] ?? null,
                'contact_phone' => $validated['contact_phone'] ?? null,
                'contact_email' => $validated['contact_email'] ?? null,
                'manager_id' => $validated['manager_id'] ?? null,
                'is_featured' => $request->has('is_featured'),
                'is_approved' => $request->has('is_approved'),
            ]);

            // Handle removed images
            if (!empty($validated['removed_images'])) {
                $imagesToRemove = HostelImage::whereIn('id', $validated['removed_images'])
                    ->where('hostel_id', $hostel->id)
                    ->get();

                foreach ($imagesToRemove as $image) {
                    // Delete file from storage
                    Storage::disk('public')->delete($image->image_path);
                    // Delete record from database
                    $image->delete();
                }
            }

            // Handle primary image change
            if (!empty($validated['primary_image_id'])) {
                // Remove primary status from all images
                $hostel->images()->update(['is_primary' => false]);

                // Set new primary image
                HostelImage::where('id', $validated['primary_image_id'])
                    ->where('hostel_id', $hostel->id)
                    ->update(['is_primary' => true, 'order' => 0]);
            }

            // Handle new cover image upload
            if ($request->hasFile('cover_image')) {
                $path = $request->file('cover_image')->store('hostels/covers', 'public');

                // If this should be the primary image
                $hostel->images()->create([
                    'image_path' => $path,
                    'type' => 'hostel',
                    'is_primary' => true,
                    'order' => 0
                ]);
            }

            // Handle new gallery images
            if ($request->hasFile('gallery_images')) {
                // Get the current max order for non-primary images
                $maxOrder = $hostel->images()
                    ->where('is_primary', false)
                    ->max('order') ?? 0;

                $order = $maxOrder + 1;

                foreach ($request->file('gallery_images') as $image) {
                    $path = $image->store('hostels/gallery', 'public');

                    $hostel->images()->create([
                        'image_path' => $path,
                        'type' => 'hostel',
                        'is_primary' => false,
                        'order' => $order++
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('admin.hostels.show', $hostel)
                ->with('success', 'Hostel updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update hostel. ' . $e->getMessage());
        }
    }
    /**
     * Delete the specified hostel
     */
    public function destroy(Hostel $hostel)
    {
        // Check for active bookings
        $hasActiveBookings = $hostel->bookings()
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($hasActiveBookings) {
            return redirect()
                ->back()
                ->with('error', 'Cannot delete hostel with active bookings.');
        }

        DB::beginTransaction();
        try {
            // Delete images from storage
            foreach ($hostel->images as $image) {
                Storage::disk('public')->delete($image->image_path);
                $image->delete();
            }

            // Delete the hostel
            $hostel->delete();

            DB::commit();

            return redirect()
                ->route('admin.hostels.index')
                ->with('success', 'Hostel deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->with('error', 'Failed to delete hostel. ' . $e->getMessage());
        }
    }

    /**
     * Approve a hostel
     */
    public function approve(Hostel $hostel)
    {
        $hostel->update(['is_approved' => true]);

        return redirect()
            ->back()
            ->with('success', 'Hostel approved successfully.');
    }

    /**
     * Set primary image
     */
    public function setPrimaryImage(Hostel $hostel, Image $image)
    {
        if ($image->hostel_id !== $hostel->id) {
            abort(403);
        }

        DB::transaction(function() use ($hostel, $image) {
            $hostel->images()->update(['is_primary' => false]);
            $image->update(['is_primary' => true]);
        });

        return redirect()
            ->back()
            ->with('success', 'Primary image updated successfully.');
    }

    /**
     * Delete an image
     */
    public function destroyImage(Hostel $hostel, Image $image)
    {
        if ($image->hostel_id !== $hostel->id) {
            abort(403);
        }

        Storage::disk('public')->delete($image->image_path);
        $image->delete();

        if ($image->is_primary) {
            $newPrimary = $hostel->images()->first();
            if ($newPrimary) {
                $newPrimary->update(['is_primary' => true]);
            }
        }

        return redirect()
            ->back()
            ->with('success', 'Image deleted successfully.');
    }
}
