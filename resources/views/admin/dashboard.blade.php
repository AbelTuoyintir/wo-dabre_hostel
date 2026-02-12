@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Admin Dashboard')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-4 gap-4">
    {{-- Total users --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4">
        <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Total Users</p>
        <p class="mt-2 text-2xl font-semibold text-slate-900">
            {{ $stats['total_users'] }}
        </p>
    </div>

    {{-- Total hostels --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4">
        <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Total Hostels</p>
        <p class="mt-2 text-2xl font-semibold text-slate-900">
            {{ $stats['total_hostels'] }}
        </p>
        <p class="mt-1 text-xs text-amber-600">
            {{ $stats['pending_hostels'] }} pending approval
        </p>
    </div>

    {{-- Total bookings --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4">
        <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Total Bookings</p>
        <p class="mt-2 text-2xl font-semibold text-slate-900">
            {{ $stats['total_bookings'] }}
        </p>
        <p class="mt-1 text-xs text-red-600">
            {{ $stats['pending_bookings'] }} pending
        </p>
    </div>

    {{-- Revenue --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4">
        <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Total Revenue</p>
        <p class="mt-2 text-2xl font-semibold text-emerald-700">
            ¢{{ number_format($stats['total_revenue'], 2) }}
        </p>
    </div>
</div>

<div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-4">
    {{-- Recent bookings --}}
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-slate-100">
        <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100">
            <h3 class="text-sm font-semibold text-slate-800">Recent Bookings</h3>
            <a href="{{ route('admin.bookings.index') }}"
               class="text-xs font-medium text-indigo-600 hover:text-indigo-700">
                View all
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-100 text-xs font-medium text-slate-500 uppercase">
                <tr>
                    <th class="px-4 py-2 text-left">#</th>
                    <th class="px-4 py-2 text-left">User</th>
                    <th class="px-4 py-2 text-left">Hostel</th>
                    <th class="px-4 py-2 text-left">Status</th>
                    <th class="px-4 py-2 text-left">Payment</th>
                    <th class="px-4 py-2 text-right">Amount</th>
                    <th class="px-4 py-2 text-left">Date</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                @forelse ($recentBookings as $booking)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-2">{{ $booking->id }}</td>
                        <td class="px-4 py-2">{{ $booking->user->name ?? '-' }}</td>
                        <td class="px-4 py-2">{{ $booking->hostel->name ?? '-' }}</td>
                        <td class="px-4 py-2">
                            @php
                                $statusColor = match($booking->booking_status) {
                                    'approved' => 'bg-emerald-100 text-emerald-700',
                                    'pending' => 'bg-amber-100 text-amber-700',
                                    'rejected' => 'bg-rose-100 text-rose-700',
                                    default => 'bg-slate-100 text-slate-700',
                                };
                            @endphp
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $statusColor }}">
                                {{ ucfirst($booking->booking_status) }}
                            </span>
                        </td>
                        <td class="px-4 py-2">
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                                {{ $booking->payment_status === 'paid'
                                    ? 'bg-emerald-100 text-emerald-700'
                                    : 'bg-slate-100 text-slate-700' }}">
                                {{ ucfirst($booking->payment_status) }}
                            </span>
                        </td>
                        <td class="px-4 py-2 text-right">
                            ₦{{ number_format($booking->total_amount, 2) }}
                        </td>
                        <td class="px-4 py-2">
                            {{ $booking->created_at->format('d M Y') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-6 text-center text-sm text-slate-500">
                            No bookings yet.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Recent users --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100">
        <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100">
            <h3 class="text-sm font-semibold text-slate-800">Recent Users</h3>
            <a href="{{ route('admin.users.index') }}"
               class="text-xs font-medium text-indigo-600 hover:text-indigo-700">
                View all
            </a>
        </div>
        <div class="p-4 space-y-3 text-sm">
            @forelse ($recentUsers as $user)
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-semibold text-slate-800">
                            {{ $user->name }}
                        </p>
                        <p class="text-xs text-slate-500">
                            {{ $user->email }}
                        </p>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-medium text-slate-700 capitalize">
                            {{ $user->role }}
                        </span>
                        <p class="mt-1 text-[11px] text-slate-400">
                            {{ $user->created_at->diffForHumans() }}
                        </p>
                    </div>
                </div>
            @empty
                <p class="text-sm text-slate-500">No users yet.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
