@extends('layouts.app')

@section('title', 'Manage Bookings')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Manage Bookings</h1>

        @php
            $exportFilters = request()->only(['status', 'payment', 'from', 'to']);
        @endphp

        <!-- Export Button -->
        <div x-data="{ open: false }" class="relative">
            <button type="button"
                    @click="open = !open"
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Export Report
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <div x-show="open"
                 @click.away="open = false"
                 x-transition
                 class="absolute right-0 mt-2 w-44 bg-white rounded-lg shadow-lg border border-gray-200 z-20 overflow-hidden">
                <a href="{{ route('admin.reports.export', array_merge(['type' => 'bookings', 'format' => 'csv'], $exportFilters)) }}"
                   class="flex items-center justify-between px-4 py-3 text-sm text-gray-700 hover:bg-gray-50">
                    <span>Export as CSV</span>
                    <span class="text-xs font-semibold text-green-700">CSV</span>
                </a>
                <a href="{{ route('admin.reports.export', array_merge(['type' => 'bookings', 'format' => 'pdf'], $exportFilters)) }}"
                   class="flex items-center justify-between px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 border-t border-gray-100">
                    <span>Export as PDF</span>
                    <span class="text-xs font-semibold text-red-700">PDF</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <form action="{{ route('admin.bookings.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="checked_in" {{ request('status') == 'checked_in' ? 'selected' : '' }}>Checked In</option>
                    <option value="checked_out" {{ request('status') == 'checked_out' ? 'selected' : '' }}>Checked Out</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Payment</label>
                <select name="payment" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">All</option>
                    <option value="paid" {{ request('payment') == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="pending" {{ request('payment') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="partial" {{ request('payment') == 'partial' ? 'selected' : '' }}>Partial</option>
                    <option value="failed" {{ request('payment') == 'failed' ? 'selected' : '' }}>Failed</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                <input type="date" name="from" value="{{ request('from') }}"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
                <input type="date" name="to" value="{{ request('to') }}"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div class="flex items-end">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg w-full">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Bookings Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Booking #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hostel</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Room</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dates</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($bookings as $booking)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-medium text-gray-900">{{ $booking->booking_number }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $booking->user->name }}</div>
                            <div class="text-sm text-gray-500">{{ $booking->user->email }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ $booking->hostel->name }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">Room {{ $booking->room_number }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">
                                {{ \Carbon\Carbon::parse($booking->check_in_date)->format('M d, Y') }}
                            </div>
                            <div class="text-sm text-gray-500">
                                to {{ \Carbon\Carbon::parse($booking->check_out_date)->format('M d, Y') }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">GHS {{ number_format($booking->total_amount, 2) }}</div>
                            <div class="text-xs text-gray-500">Paid: GHS {{ number_format($booking->amount_paid, 2) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'confirmed' => 'bg-green-100 text-green-800',
                                    'checked_in' => 'bg-blue-100 text-blue-800',
                                    'checked_out' => 'bg-gray-100 text-gray-800',
                                    'cancelled' => 'bg-red-100 text-red-800',
                                ];
                                $statusColor = $statusColors[$booking->booking_status] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColor }}">
                                {{ ucfirst($booking->booking_status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $paymentColors = [
                                    'paid' => 'bg-green-100 text-green-800',
                                    'partial' => 'bg-yellow-100 text-yellow-800',
                                    'pending' => 'bg-orange-100 text-orange-800',
                                    'failed' => 'bg-red-100 text-red-800',
                                ];
                                $paymentColor = $paymentColors[$booking->payment_status] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $paymentColor }}">
                                {{ ucfirst($booking->payment_status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-3">
                                <a href="{{ route('admin.bookings.show', $booking) }}"
                                   class="text-blue-600 hover:text-blue-900"
                                   title="View Details">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>

                                <!-- Status Update Dropdown -->
                                <div x-data="{ open: false }" class="relative">
                                    <button @click="open = !open" class="text-gray-600 hover:text-gray-900">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                                        </svg>
                                    </button>

                                    <div x-show="open" @click.away="open = false"
                                         class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10 border">
                                        <form action="{{ route('admin.bookings.status', $booking) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <select name="booking_status" onchange="this.form.submit()"
                                                    class="block w-full px-4 py-2 text-sm text-gray-700">
                                                <option value="confirmed" {{ $booking->booking_status == 'confirmed' ? 'selected' : '' }}>Confirm</option>
                                                <option value="checked_in" {{ $booking->booking_status == 'checked_in' ? 'selected' : '' }}>Check In</option>
                                                <option value="checked_out" {{ $booking->booking_status == 'checked_out' ? 'selected' : '' }}>Check Out</option>
                                                <option value="cancelled" {{ $booking->booking_status == 'cancelled' ? 'selected' : '' }}>Cancel</option>
                                            </select>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-10 text-center text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <p class="text-lg">No bookings found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t">
            {{ $bookings->links() }}
        </div>
    </div>
</div>

<!-- Add Alpine.js if not already included -->
@push('scripts')
<script src="//unpkg.com/alpinejs" defer></script>
@endpush
@endsection
