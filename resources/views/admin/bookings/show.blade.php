@extends('layouts.admin')

@section('title', 'Booking Details')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Booking #{{ $booking->booking_number }}</h1>
            <p class="text-gray-600">View and manage booking details</p>
        </div>
        <a href="{{ route('admin.bookings.index') }}"
           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
            Back to Bookings
        </a>
    </div>

    <!-- Booking Details -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Customer Info -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold mb-4">Customer Information</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Name</p>
                        <p class="font-medium">{{ $booking->user->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Email</p>
                        <p class="font-medium">{{ $booking->user->email }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Phone</p>
                        <p class="font-medium">{{ $booking->user->phone ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <!-- Booking Details -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold mb-4">Booking Information</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Hostel</p>
                        <p class="font-medium">{{ $booking->hostel->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Room Number</p>
                        <p class="font-medium">{{ $booking->room_number }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Check In</p>
                        <p class="font-medium">{{ \Carbon\Carbon::parse($booking->check_in_date)->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Check Out</p>
                        <p class="font-medium">{{ \Carbon\Carbon::parse($booking->check_out_date)->format('M d, Y') }}</p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-sm text-gray-600">Special Requests</p>
                        <p class="font-medium">{{ $booking->special_requests ?? 'No special requests' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Status Card -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold mb-4">Update Status</h2>
                <form action="{{ route('admin.bookings.status', $booking) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <select name="booking_status" class="w-full rounded-md border-gray-300 mb-3">
                        <option value="pending" {{ $booking->booking_status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ $booking->booking_status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="checked_in" {{ $booking->booking_status == 'checked_in' ? 'selected' : '' }}>Checked In</option>
                        <option value="checked_out" {{ $booking->booking_status == 'checked_out' ? 'selected' : '' }}>Checked Out</option>
                        <option value="cancelled" {{ $booking->booking_status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                        Update Status
                    </button>
                </form>
            </div>

            <!-- Payment Info -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold mb-4">Payment Information</h2>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total Amount:</span>
                        <span class="font-medium">GHS {{ number_format($booking->total_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Amount Paid:</span>
                        <span class="font-medium">GHS {{ number_format($booking->amount_paid, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Balance:</span>
                        <span class="font-medium">GHS {{ number_format($booking->total_amount - $booking->amount_paid, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Payment Status:</span>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                            {{ $booking->payment_status == 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ ucfirst($booking->payment_status) }}
                        </span>
                    </div>
                    @if($booking->transaction_id)
                    <div class="mt-4 pt-4 border-t">
                        <p class="text-sm text-gray-600">Transaction ID:</p>
                        <p class="text-sm font-mono">{{ $booking->transaction_id }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
