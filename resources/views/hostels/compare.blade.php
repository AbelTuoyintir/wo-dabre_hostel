@extends('layouts.home')

@section('title', 'Compare Hostels - Wo-dabre')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <a href="{{ route('hostels.index') }}" class="text-blue-600 hover:underline flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Back to Exploration
        </a>
        <h1 class="text-3xl font-bold text-slate-800 mt-4">Compare Hostels</h1>
        <p class="text-slate-600">Find the perfect stay by comparing your favorites side-by-side.</p>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full bg-white rounded-xl shadow-sm border-collapse">
            <thead>
                <tr>
                    <th class="p-4 bg-slate-50 border-b text-left text-sm font-bold text-slate-500 uppercase tracking-wider w-1/4">Features</th>
                    @foreach($hostels as $hostel)
                        <th class="p-4 bg-white border-b text-center min-w-[250px]">

                            <div class="flex flex-col items-center gap-3">
                                @php
                                    $imageUrl = !empty($hostel->primaryImage->image_path)
                                        ? image_url($hostel->primaryImage->image_path)
                                        : 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=600&h=400&fit=crop';
                                @endphp
                                <img src="{{ $imageUrl }}" alt="{{ $hostel->name }}" class="w-32 h-32 object-cover rounded-lg shadow-sm">
                                <h3 class="font-bold text-slate-800">{{ $hostel->name }}</h3>
                                <a href="{{ route('hostels.guest.show', $hostel->uuid ?? $hostel->id) }}" class="text-xs bg-rose-500 text-white px-3 py-1 rounded-full hover:bg-rose-600 transition">View Details</a>
                            </div>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <!-- Location -->
                <tr>
                    <td class="p-4 font-semibold text-slate-700 bg-slate-50/50">Location</td>
                    @foreach($hostels as $hostel)
                        <td class="p-4 text-center text-slate-600">{{ $hostel->location }}</td>

                    @endforeach
                </tr>
                <!-- Starting Price -->
                <tr>
                    <td class="p-4 font-semibold text-slate-700 bg-slate-50/50">Starting Price</td>
                    @foreach($hostels as $hostel)
                        <td class="p-4 text-center">
                            <span class="font-bold text-slate-800 text-lg">₵{{ number_format($hostel->rooms->min('room_cost'), 2) }}</span>

                            <span class="text-slate-500 text-xs">/ year</span>
                        </td>
                    @endforeach
                </tr>
                <!-- Amenities -->
                <tr>
                    <td class="p-4 font-semibold text-slate-700 bg-slate-50/50">Amenities</td>
                    @foreach($hostels as $hostel)
                        <td class="p-4">

                            <div class="flex flex-wrap justify-center gap-2">
                                @if($hostel->amenities)
                                    @foreach($hostel->amenities->take(5) as $amenity)
                                        <span class="text-[10px] bg-slate-100 text-slate-600 px-2 py-1 rounded-md">{{ $amenity }}</span>
                                    @endforeach
                                    @if($hostel->amenities->count() > 5)
                                        <span class="text-[10px] text-slate-400">+{{ $hostel->amenities->count() - 5 }} more</span>
                                    @endif
                                @else
                                    <span class="text-slate-400 text-xs">N/A</span>
                                @endif
                            </div>
                        </td>
                    @endforeach
                </tr>
                <!-- Availability -->
                <tr>
                    <td class="p-4 font-semibold text-slate-700 bg-slate-50/50">Available Rooms</td>
                    @foreach($hostels as $hostel)
                        <td class="p-4 text-center">
                            @php $count = $hostel->rooms->where('status', 'available')->count(); @endphp

                            <span class="{{ $count > 0 ? 'text-green-600' : 'text-red-600' }} font-medium">
                                {{ $count }} rooms left
                            </span>
                        </td>
                    @endforeach
                </tr>
                <!-- Rating -->
                <tr>
                    <td class="p-4 font-semibold text-slate-700 bg-slate-50/50">Rating</td>
                    @foreach($hostels as $hostel)
                        <td class="p-4 text-center">
                            <div class="flex items-center justify-center gap-1">

                                <i class="fas fa-star text-amber-400 text-xs"></i>
                                <span class="font-bold">{{ $hostel->rating ?? '4.5' }}</span>
                            </div>
                        </td>
                    @endforeach
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
