@extends('layouts.app')

@section('title', 'Manage Hostels')
@section('page-title', 'Hostel Management')

@section('content')
<div class="space-y-6">
    @if (session('success'))
        <div class="mb-6 rounded-md bg-green-50 border border-green-200 p-4">
            <p class="text-green-800 font-medium">
                {{ session('success') }}
            </p>
        </div>
    @endif
    <!-- Header with actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <h2 class="text-xl font-bold text-gray-800">All Hostels</h2>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                {{ $hostels->total() }} Total
            </span>
        </div>
        <div class="flex items-center gap-3">
            <!-- Approval Toggle -->
            <div class="flex items-center bg-white rounded-md border border-gray-300 p-1">
                <a href="{{ route('admin.hostels.index', ['pending' => '']) }}" 
                   class="px-3 py-1.5 text-sm font-medium rounded-md {{ !request('pending') ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                    All
                </a>
                <a href="{{ route('admin.hostels.index', ['pending' => '1']) }}" 
                   class="px-3 py-1.5 text-sm font-medium rounded-md {{ request('pending') == '1' ? 'bg-yellow-500 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                    Pending Approval
                </a>
            </div>
            
            <a href="{{ route('admin.hostels.create') }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent rounded-md bg-blue-600 text-sm font-medium text-white hover:bg-blue-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add New Hostel
            </a>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-sm font-medium text-gray-700 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                </svg>
                Filter Hostels
            </h3>
        </div>
        <div class="p-4">
            <form method="GET" action="{{ route('admin.hostels.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Search -->
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Search</label>
                        <div class="relative">
                            <input type="text" name="search" placeholder="Name, location, contact..." 
                                   value="{{ request('search') }}"
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            <div class="absolute left-3 top-2.5 text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Location -->
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Location</label>
                        <input type="text" name="location" placeholder="City, area..." 
                               value="{{ request('location') }}"
                               class="w-full border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                        <select name="status" class="w-full border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        </select>
                    </div>

                    <!-- Approval Status -->
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Approval</label>
                        <select name="approved" class="w-full border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All</option>
                            <option value="1" {{ request('approved') == '1' ? 'selected' : '' }}>Approved</option>
                            <option value="0" {{ request('approved') == '0' ? 'selected' : '' }}>Pending</option>
                        </select>
                    </div>

                    <!-- Featured -->
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Featured</label>
                        <select name="featured" class="w-full border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All</option>
                            <option value="1" {{ request('featured') == '1' ? 'selected' : '' }}>Featured Only</option>
                            <option value="0" {{ request('featured') == '0' ? 'selected' : '' }}>Non-featured</option>
                        </select>
                    </div>

                    <!-- Manager -->
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Manager</label>
                        <input type="text" name="manager" placeholder="Manager name" 
                               value="{{ request('manager') }}"
                               class="w-full border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Min Rating -->
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Min Rating</label>
                        <select name="min_rating" class="w-full border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Any</option>
                            <option value="4" {{ request('min_rating') == '4' ? 'selected' : '' }}>4+ Stars</option>
                            <option value="3" {{ request('min_rating') == '3' ? 'selected' : '' }}>3+ Stars</option>
                            <option value="2" {{ request('min_rating') == '2' ? 'selected' : '' }}>2+ Stars</option>
                        </select>
                    </div>

                    <!-- Date Range -->
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Created After</label>
                        <input type="date" name="created_after" value="{{ request('created_after') }}"
                               class="w-full border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2">
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-medium text-xs text-white hover:bg-blue-700">
                        Apply Filters
                    </button>
                    <a href="{{ route('admin.hostels.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-xs font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Clear Filters
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Hostels Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hostel</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Manager</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rooms</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rating</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Approval</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($hostels as $hostel)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                @if($hostel->primaryImage)
                                    <img class="h-10 w-10 rounded-lg object-cover" 
                                         src="{{ Storage::url($hostel->primaryImage->path) }}" 
                                         alt="{{ $hostel->name }}">
                                @else
                                    <div class="h-10 w-10 rounded-lg bg-gray-200 flex items-center justify-center">
                                        <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                    </div>
                                @endif
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $hostel->name }}
                                        @if($hostel->is_featured)
                                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                                Featured
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-500">ID: #{{ $hostel->id }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $hostel->location }}</div>
                            <div class="text-xs text-gray-500">{{ Str::limit($hostel->address, 30) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($hostel->manager)
                                <div class="text-sm text-gray-900">{{ $hostel->manager->name }}</div>
                                <div class="text-xs text-gray-500">{{ $hostel->manager->email }}</div>
                            @else
                                <span class="text-sm text-gray-400">Not assigned</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $hostel->total_rooms ?? 0 }} Total</div>
                            <div class="text-xs {{ ($hostel->available_rooms ?? 0) > 0 ? 'text-green-600' : 'text-gray-500' }}">
                                {{ $hostel->available_rooms ?? 0 }} Available
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <span class="text-sm font-medium text-gray-900 mr-1">{{ number_format($hostel->rating, 1) }}</span>
                                <div class="flex items-center">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-4 h-4 {{ $i <= round($hostel->rating) ? 'text-yellow-400' : 'text-gray-300' }}" 
                                             fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endfor
                                </div>
                            </div>
                            <div class="text-xs text-gray-500">{{ $hostel->reviews_count ?? 0 }} reviews</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full 
                                @if($hostel->status == 'active') bg-green-100 text-green-800
                                @elseif($hostel->status == 'maintenance') bg-orange-100 text-orange-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($hostel->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($hostel->is_approved)
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                    Approved
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">
                                    Pending
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-2">
                                @if(!$hostel->is_approved)
                                    <form action="{{ route('admin.hostels.approve', $hostel) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" 
                                                class="text-green-600 hover:text-green-900"
                                                title="Approve Hostel">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                                
                                <a href="{{ route('admin.hostels.show', $hostel) }}" 
                                   class="text-blue-600 hover:text-blue-900" title="View">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                
                                <a href="{{ route('admin.hostels.edit', $hostel) }}" 
                                   class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                
                                <form action="{{ route('admin.hostels.destroy', $hostel) }}" 
                                      method="POST" 
                                      onsubmit="return confirm('Are you sure you want to delete this hostel? This will also delete all rooms and bookings associated with it.');"
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                <h3 class="mt-4 text-lg font-medium text-gray-900">No hostels found</h3>
                                <p class="mt-1 text-sm text-gray-500">Get started by creating your first hostel.</p>
                                <a href="{{ route('admin.hostels.create') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white hover:bg-blue-700">
                                    Add New Hostel
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($hostels->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            {{ $hostels->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>
@endsection