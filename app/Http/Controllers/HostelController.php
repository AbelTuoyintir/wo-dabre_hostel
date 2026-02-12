<?php

namespace App\Http\Controllers;

use App\Models\Hostel;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HostelController extends Controller
{
    /**
     * Display a listing of approved hostels for students
     */
    public function index(Request $request)
    {
        $query = Hostel::query()
            ->where('is_approved', true)
            ->where('status', 'active')
            ->with(['primaryImage', 'images', 'rooms' => function($q) {
                $q->where('status', 'available')
                  ->whereColumn('current_occupancy', '<', 'capacity');
            }]);

        // Filter by location
        if ($request->filled('location') && $request->location != 'all') {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        // Filter by price range (based on average room price)
        if ($request->filled('price_range')) {
            $priceRange = $request->price_range;
            
            $query->whereHas('rooms', function($q) use ($priceRange) {
                if ($priceRange == '0-500') {
                    $q->where('price_per_month', '<', 500);
                } elseif ($priceRange == '500-1000') {
                    $q->whereBetween('price_per_month', [500, 1000]);
                } elseif ($priceRange == '1000-1500') {
                    $q->whereBetween('price_per_month', [1000, 1500]);
                } elseif ($priceRange == '1500-2000') {
                    $q->whereBetween('price_per_month', [1500, 2000]);
                } elseif ($priceRange == '2000+') {
                    $q->where('price_per_month', '>', 2000);
                }
            });
        }

        // Filter by gender preference
        if ($request->filled('gender')) {
            $query->whereHas('rooms', function($q) use ($request) {
                $q->whereIn('gender', [$request->gender, 'any']);
            });
        }

        // Filter by amenities
        if ($request->filled('amenities')) {
            $amenities = explode(',', $request->amenities);
            foreach ($amenities as $amenity) {
                $query->whereJsonContains('amenities', $amenity);
            }
        }

        // Filter by room features
        if ($request->filled('furnished')) {
            $query->whereHas('rooms', function($q) {
                $q->where('furnished', true);
            });
        }

        if ($request->filled('private_bathroom')) {
            $query->whereHas('rooms', function($q) {
                $q->where('private_bathroom', true);
            });
        }

        // Search by name, description, or location
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        // Distance based sorting (if coordinates provided)
        if ($request->filled('lat') && $request->filled('lng')) {
            $lat = $request->lat;
            $lng = $request->lng;
            
            $query->selectRaw(
                '*, ( 6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * sin( radians( latitude ) ) ) ) AS distance',
                [$lat, $lng, $lat]
            )->orderBy('distance');
        }

        // Sorting options
        switch ($request->sort) {
            case 'price_low':
                $query->withAvg('rooms', 'price_per_month')
                      ->orderBy('rooms_avg_price_per_month');
                break;
            case 'price_high':
                $query->withAvg('rooms', 'price_per_month')
                      ->orderByDesc('rooms_avg_price_per_month');
                break;
            case 'rating':
                $query->orderByDesc('rating');
                break;
            case 'newest':
                $query->orderByDesc('created_at');
                break;
            default:
                $query->orderByDesc('is_featured')
                      ->orderByDesc('rating')
                      ->orderByDesc('created_at');
        }

        // Get unique locations for filter dropdown
        $locations = Cache::remember('hostel_locations', 3600, function() {
            return Hostel::where('is_approved', true)
                ->where('status', 'active')
                ->select('location')
                ->distinct()
                ->orderBy('location')
                ->pluck('location');
        });

        $hostels = $query->paginate(12)->withQueryString();

        return view('admin.hostels.index', compact('hostels', 'locations'));
    }

    /**
     * Display the specified hostel for students
     */
    public function show($id)
    {
        $hostel = Hostel::with([
            'images',
            'reviews' => function($q) {
                $q->with('user')
                  ->latest()
                  ->limit(10);
            },
            'reviews.user',
            'rooms' => function($q) {
                $q->withCount('bookings')
                  ->where('status', 'available')
                  ->whereColumn('current_occupancy', '<', 'capacity')
                  ->orderBy('price_per_month');
            }
        ])->findOrFail($id);

        if (!$hostel->is_approved || $hostel->status !== 'active') {
            abort(404);
        }

        // Calculate average rating
        $averageRating = $hostel->reviews->avg('rating');
        $reviewCount = $hostel->reviews->count();

        // Get room statistics
        $roomStats = [
            'total' => $hostel->rooms()->count(),
            'available' => $hostel->rooms()
                ->where('status', 'available')
                ->whereColumn('current_occupancy', '<', 'capacity')
                ->count(),
            'min_price' => $hostel->rooms()
                ->where('status', 'available')
                ->min('price_per_month'),
            'max_price' => $hostel->rooms()
                ->where('status', 'available')
                ->max('price_per_month'),
        ];

        // Group rooms by type/capacity
        $roomsByType = $hostel->rooms()
            ->where('status', 'available')
            ->whereColumn('current_occupancy', '<', 'capacity')
            ->get()
            ->groupBy('capacity');

        // Get related hostels in same location
        $relatedHostels = Hostel::where('location', $hostel->location)
            ->where('id', '!=', $hostel->id)
            ->where('is_approved', true)
            ->where('status', 'active')
            ->with(['primaryImage', 'rooms' => function($q) {
                $q->where('status', 'available')
                  ->whereColumn('current_occupancy', '<', 'capacity');
            }])
            ->limit(3)
            ->get()
            ->map(function($related) {
                $related->min_price = $related->rooms()->min('price_per_month');
                return $related;
            });

        // Check if user has already booked this hostel
        $userBooking = null;
        if (auth()->check()) {
            $userBooking = auth()->user()
                ->bookings()
                ->whereHas('room', function($q) use ($hostel) {
                    $q->where('hostel_id', $hostel->id);
                })
                ->whereIn('status', ['pending', 'confirmed'])
                ->first();
        }

        return view('admin.hostels.show', compact(
            'hostel', 
            'relatedHostels', 
            'averageRating', 
            'reviewCount',
            'roomStats',
            'roomsByType',
            'userBooking'
        ));
    }

    /**
     * Search hostels by location (AJAX)
     */
    public function search(Request $request)
    {
        $request->validate([
            'location' => 'required|string|min:2',
        ]);

        $hostels = Hostel::where('is_approved', true)
            ->where('status', 'active')
            ->where(function($q) use ($request) {
                $q->where('location', 'like', '%' . $request->location . '%')
                  ->orWhere('name', 'like', '%' . $request->location . '%')
                  ->orWhere('address', 'like', '%' . $request->location . '%');
            })
            ->with(['primaryImage', 'rooms' => function($q) {
                $q->where('status', 'available')
                  ->whereColumn('current_occupancy', '<', 'capacity');
            }])
            ->limit(5)
            ->get()
            ->map(function($hostel) {
                return [
                    'id' => $hostel->id,
                    'name' => $hostel->name,
                    'location' => $hostel->location,
                    'address' => $hostel->address,
                    'image' => $hostel->primaryImage?->url ?? $hostel->primaryImage?->path,
                    'min_price' => $hostel->rooms()->min('price_per_month'),
                    'rating' => $hostel->rating,
                    'url' => route('hostels.show', $hostel->id)
                ];
            });

        return response()->json($hostels);
    }

    /**
     * Get available rooms for a specific hostel (AJAX)
     */
    public function getAvailableRooms(Request $request, Hostel $hostel)
    {
        if (!$hostel->is_approved || $hostel->status !== 'active') {
            return response()->json(['error' => 'Hostel not available'], 404);
        }

        $rooms = $hostel->rooms()
            ->where('status', 'available')
            ->whereColumn('current_occupancy', '<', 'capacity')
            ->when($request->filled('gender'), function($q) use ($request) {
                $q->whereIn('gender', [$request->gender, 'any']);
            })
            ->when($request->filled('min_price'), function($q) use ($request) {
                $q->where('price_per_month', '>=', $request->min_price);
            })
            ->when($request->filled('max_price'), function($q) use ($request) {
                $q->where('price_per_month', '<=', $request->max_price);
            })
            ->when($request->filled('capacity'), function($q) use ($request) {
                $q->where('capacity', $request->capacity);
            })
            ->when($request->filled('furnished'), function($q) use ($request) {
                $q->where('furnished', $request->furnished);
            })
            ->when($request->filled('private_bathroom'), function($q) use ($request) {
                $q->where('private_bathroom', $request->private_bathroom);
            })
            ->withCount('bookings')
            ->get()
            ->map(function($room) {
                return [
                    'id' => $room->id,
                    'number' => $room->number,
                    'capacity' => $room->capacity,
                    'available_spaces' => $room->availableSpaces(),
                    'price' => $room->price_per_month,
                    'gender' => $room->gender,
                    'furnished' => $room->furnished,
                    'private_bathroom' => $room->private_bathroom,
                    'size' => $room->size_sqm,
                    'floor' => $room->floor,
                    'window_type' => $room->window_type,
                    'description' => $room->description
                ];
            });

        return response()->json($rooms);
    }

    /**
     * Get all available locations for autocomplete
     */
    // public function getLocations(Request $request)
    // {
    //     $query = Hostel::where('is_approved', true)
    //         ->where('status', 'active')
    //         ->select('location')
    //         ->distinct();

    //     if ($request->filled('search')) {
    //         $query->where('location', 'like', '%' . $request->search . '%');
    //     }

    //     $locations = $query->orderBy('location')
    //         ->limit(10)
    //         ->pluck('location');

    //     return response()->json($locations);
    // }

    // /**
    //  * Show hostel reviews page
    //  */
    // public function reviews(Hostel $hostel)
    // {
    //     if (!$hostel->is_approved || $hostel->status !== 'active') {
    //         abort(404);
    //     }

    //     $reviews = $hostel->reviews()
    //         ->with('user')
    //         ->latest()
    //         ->paginate(20);

    //     $ratingDistribution = [
    //         5 => $hostel->reviews()->where('rating', 5)->count(),
    //         4 => $hostel->reviews()->where('rating', 4)->count(),
    //         3 => $hostel->reviews()->where('rating', 3)->count(),
    //         2 => $hostel->reviews()->where('rating', 2)->count(),
    //         1 => $hostel->reviews()->where('rating', 1)->count(),
    //     ];

    //     return view('hostels.reviews', compact('hostel', 'reviews', 'ratingDistribution'));
    // }

    /**
     * Show hostel rooms page
     */
    public function rooms(Hostel $hostel, Request $request)
    {
        if (!$hostel->is_approved || $hostel->status !== 'active') {
            abort(404);
        }

        $rooms = $hostel->rooms()
            ->where('status', 'available')
            ->whereColumn('current_occupancy', '<', 'capacity')
            ->when($request->filled('gender'), function($q) use ($request) {
                $q->whereIn('gender', [$request->gender, 'any']);
            })
            ->when($request->filled('min_price'), function($q) use ($request) {
                $q->where('price_per_month', '>=', $request->min_price);
            })
            ->when($request->filled('max_price'), function($q) use ($request) {
                $q->where('price_per_month', '<=', $request->max_price);
            })
            ->orderBy('price_per_month')
            ->paginate(12);

        return view('admin.rooms.index', compact('hostel', 'rooms'));
    }

    /**
     * Show hostel amenities
     */
    public function amenities(Hostel $hostel)
    {
        if (!$hostel->is_approved || $hostel->status !== 'active') {
            abort(404);
        }

        $amenities = $hostel->amenities ?? [];
        
        // Group amenities by category
        $groupedAmenities = [
            'Basic' => ['wifi' => 'Free WiFi', 'parking' => 'Parking', 'security' => '24/7 Security', 'laundry' => 'Laundry', 'kitchen' => 'Kitchen'],
            'Comfort' => ['ac' => 'Air Conditioning', 'heating' => 'Heating', 'elevator' => 'Elevator', 'furnished' => 'Furnished'],
            'Recreation' => ['gym' => 'Gym', 'pool' => 'Swimming Pool', 'garden' => 'Garden', 'common_room' => 'Common Room', 'tv_lounge' => 'TV Lounge'],
            'Services' => ['cleaning' => 'Cleaning Service', 'meal_plan' => 'Meal Plan', 'shuttle' => 'Shuttle', 'reception' => '24/7 Reception'],
        ];

        return view('admin.hostels.amenities', compact('hostel', 'amenities', 'groupedAmenities'));
    }
}