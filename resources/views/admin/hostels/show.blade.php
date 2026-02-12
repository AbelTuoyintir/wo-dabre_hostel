@extends('layouts.app')

@section('title', $hostel->name)
@section('page-title', $hostel->name)

@section('content')
<div class="space-y-6">
    <!-- Header with actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.hostels.index') }}" class="text-gray-600 hover:text-gray-900">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div class="flex items-center gap-2">
                <h2 class="text-xl font-bold text-gray-800">{{ $hostel->name }}</h2>
                @if($hostel->is_featured)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                        Featured
                    </span>
                @endif
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                    @if($hostel->status == 'active') bg-green-100 text-green-800
                    @elseif($hostel->status == 'maintenance') bg-orange-100 text-orange-800
                    @else bg-gray-100 text-gray-800 @endif">
                    {{ ucfirst($hostel->status) }}
                </span>
                @if(!$hostel->is_approved)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                        Pending Approval
                    </span>
                @endif
            </div>
        </div>
        <div class="flex items-center gap-3">
            @if(!$hostel->is_approved)
                <form action="{{ route('admin.hostels.approve', $hostel) }}" method="POST" class="inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md bg-green-600 text-sm font-medium text-white hover:bg-green-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Approve Hostel
                    </button>
                </form>
            @endif
            <a href="{{ route('admin.hostels.edit', $hostel) }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit Hostel
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Rooms</p>
                    <p class="text-2xl font-semibold text-gray-900 mt-1">{{ $hostel->total_rooms ?? 0 }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600">Available</span>
                    <span class="font-medium text-gray-900">{{ $hostel->available_rooms ?? 0 }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                    <div class="bg-blue-600 h-2 rounded-full" 
                         style="width: {{ $hostel->total_rooms ? ($hostel->available_rooms / $hostel->total_rooms) * 100 : 0 }}%"></div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Bookings</p>
                    <p class="text-2xl font-semibold text-gray-900 mt-1">{{ $hostel->bookings_count ?? 0 }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600">Active</span>
                    <span class="font-medium text-gray-900">{{ $activeBookings ?? 0 }}</span>
                </div>
                <div class="flex items-center justify-between text-sm mt-1">
                    <span class="text-gray-600">Pending</span>
                    <span class="font-medium text-gray-900">{{ $pendingBookings ?? 0 }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Rating</p>
                    <div class="flex items-center mt-1">
                        <span class="text-2xl font-semibold text-gray-900 mr-2">{{ number_format($hostel->rating, 1) }}</span>
                        <div class="flex items-center">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-5 h-5 {{ $i <= round($hostel->rating) ? 'text-yellow-400' : 'text-gray-300' }}" 
                                     fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                        </div>
                    </div>
                </div>
                <div class="bg-yellow-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-sm text-gray-600">{{ $hostel->reviews_count ?? 0 }} reviews</span>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Manager</p>
                    @if($hostel->manager)
                        <p class="text-lg font-semibold text-gray-900 mt-1">{{ $hostel->manager->name }}</p>
                        <p class="text-xs text-gray-500">{{ $hostel->manager->email }}</p>
                    @else
                        <p class="text-lg font-semibold text-gray-400 mt-1">Not Assigned</p>
                    @endif
                </div>
                <div class="bg-purple-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Hostel Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Image Gallery -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-medium text-gray-900">Gallery</h3>
                </div>
                <div class="p-6">
                    @if($hostel->images && count($hostel->images) > 0)
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            @foreach($hostel->images as $image)
                                <div class="relative group">
                                    <img src="{{ Storage::url($image->path) }}" 
                                         alt="{{ $hostel->name }}" 
                                         class="w-full h-40 object-cover rounded-lg">
                                    @if($image->is_primary)
                                        <span class="absolute top-2 left-2 bg-blue-600 text-white text-xs px-2 py-1 rounded">
                                            Primary
                                        </span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">No images uploaded</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Description -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-medium text-gray-900">Description</h3>
                </div>
                <div class="p-6">
                    <p class="text-gray-700 whitespace-pre-line">{{ $hostel->description ?? 'No description provided.' }}</p>
                </div>
            </div>

            <!-- Location Details -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-medium text-gray-900">Location</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Location</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $hostel->location }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Address</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $hostel->address }}</p>
                        </div>
                        @if($hostel->latitude && $hostel->longitude)
                            <div>
                                <p class="text-sm font-medium text-gray-500">Coordinates</p>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ $hostel->latitude }}, {{ $hostel->longitude }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Rooms List -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900">Rooms</h3>
                    <a href="{{ route('admin.rooms.create', ['hostel_id' => $hostel->id]) }}" 
                       class="text-sm text-blue-600 hover:text-blue-800">
                        + Add Room
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Room #</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Floor</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Capacity</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Occupancy</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($hostel->rooms ?? [] as $room)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $room->number }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $room->floor ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $room->capacity }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <span class="text-sm text-gray-900 mr-2">{{ $room->current_occupancy }}/{{ $room->capacity }}</span>
                                            <div class="w-16 bg-gray-200 rounded-full h-2">
                                                <div class="bg-blue-600 h-2 rounded-full" 
                                                     style="width: {{ ($room->current_occupancy / $room->capacity) * 100 }}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        ${{ number_format($room->price_per_month, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs rounded-full 
                                            @if($room->status == 'available') bg-green-100 text-green-800
                                            @elseif($room->status == 'full') bg-yellow-100 text-yellow-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst($room->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('admin.rooms.show', $room) }}" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                        <a href="{{ route('admin.rooms.edit', $room) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                        No rooms added yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Column - Sidebar -->
        <div class="space-y-6">
            <!-- Contact Information -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-medium text-gray-900">Contact Information</h3>
                </div>
                <div class="p-6 space-y-4">
                    @if($hostel->contact_phone)
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            <span class="text-sm text-gray-900">{{ $hostel->contact_phone }}</span>
                        </div>
                    @endif
                    
                    @if($hostel->contact_email)
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <span class="text-sm text-gray-900">{{ $hostel->contact_email }}</span>
                        </div>
                    @endif

                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span class="text-sm text-gray-900">{{ $hostel->location }}</span>
                    </div>
                </div>
            </div>

            <!-- Amenities -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-medium text-gray-900">Amenities</h3>
                </div>
                <div class="p-6">
                    @if($hostel->amenities && count($hostel->amenities) > 0)
                        <div class="flex flex-wrap gap-2">
                            @foreach($hostel->amenities as $amenity)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs bg-gray-100 text-gray-800">
                                    {{ ucwords(str_replace('_', ' ', $amenity)) }}
                                </span>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500">No amenities listed.</p>
                    @endif
                </div>
            </div>

            <!-- House Rules -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-medium text-gray-900">House Rules</h3>
                </div>
                <div class="p-6">
                    @if($hostel->rules && count($hostel->rules) > 0)
                        <ul class="space-y-2">
                            @foreach($hostel->rules as $rule)
                                <li class="flex items-start">
                                    <svg class="w-4 h-4 text-gray-400 mt-0.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-5m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="text-sm text-gray-700">{{ $rule }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-sm text-gray-500">No rules specified.</p>
                    @endif
                </div>
            </div>

            <!-- Check-in/out Times -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-medium text-gray-900">Check-in/out Times</h3>
                </div>
                <div class="p-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Check-in</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900">
                                {{ $hostel->check_in_time ? $hostel->check_in_time->format('H:i') : '14:00' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Check-out</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900">
                                {{ $hostel->check_out_time ? $hostel->check_out_time->format('H:i') : '12:00' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Metadata -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-medium text-gray-900">Metadata</h3>
                </div>
                <div class="p-6">
                    <dl class="space-y-2">
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Created</dt>
                            <dd class="text-sm text-gray-900">{{ $hostel->created_at->format('M d, Y H:i') }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Last Updated</dt>
                            <dd class="text-sm text-gray-900">{{ $hostel->updated_at->diffForHumans() }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Hostel ID</dt>
                            <dd class="text-sm text-gray-900">#{{ $hostel->id }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection