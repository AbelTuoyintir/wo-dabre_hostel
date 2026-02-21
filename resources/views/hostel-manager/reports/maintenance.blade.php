@extends('layouts.hostelmanage')

@section('title', 'Maintenance Report')
@section('page-title', 'Maintenance Report')

@section('content')
<!-- Header -->
<div class="mb-4 bg-white rounded-xl shadow-sm border border-gray-100 p-4">
    <div class="flex items-center">
        <a href="{{ route('hostel-manager.reports') }}" class="text-gray-500 hover:text-gray-700 mr-3">
            <i class="fas fa-arrow-left text-xs"></i>
        </a>
        <div>
            <h2 class="text-sm font-semibold text-gray-800">Maintenance Report</h2>
            <p class="text-xs text-gray-500">Track and manage maintenance requests</p>
        </div>
        <div class="ml-auto">
            <a href="{{ route('hostel-manager.reports.export', 'maintenance') }}"
               class="bg-blue-500 hover:bg-blue-600 text-white text-xs px-3 py-1.5 rounded-lg transition flex items-center">
                <i class="fas fa-download mr-1"></i> Export Report
            </a>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-medium text-gray-500 uppercase">Total Requests</span>
            <span class="bg-blue-100 text-blue-600 text-xs px-2 py-1 rounded-full">{{ $stats['total'] }}</span>
        </div>
        <div class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-medium text-gray-500 uppercase">Pending</span>
            <span class="bg-yellow-100 text-yellow-600 text-xs px-2 py-1 rounded-full">{{ $stats['pending'] }}</span>
        </div>
        <div class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] }}</div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-medium text-gray-500 uppercase">In Progress</span>
            <span class="bg-blue-100 text-blue-600 text-xs px-2 py-1 rounded-full">{{ $stats['in_progress'] }}</span>
        </div>
        <div class="text-2xl font-bold text-blue-600">{{ $stats['in_progress'] }}</div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-medium text-gray-500 uppercase">Urgent</span>
            <span class="bg-red-100 text-red-600 text-xs px-2 py-1 rounded-full">{{ $stats['urgent'] }}</span>
        </div>
        <div class="text-2xl font-bold text-red-600">{{ $stats['urgent'] }}</div>
    </div>
</div>

<!-- Filters -->
<div class="mb-4 bg-white rounded-xl shadow-sm border border-gray-100 p-4">
    <form method="GET" action="{{ route('hostel-manager.reports.maintenance') }}" class="flex flex-wrap items-center gap-3">
        <div class="w-40">
            <select name="status" class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-lg">
                <option value="all" {{ $status == 'all' ? 'selected' : '' }}>All Status</option>
                <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="in_progress" {{ $status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="completed" {{ $status == 'completed' ? 'selected' : '' }}>Completed</option>
            </select>
        </div>
        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white text-xs px-3 py-1.5 rounded-lg">Filter</button>
    </form>
</div>

<!-- Maintenance Requests Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase">ID</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase">Title</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase">Location</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase">Reported By</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase">Priority</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase">Date</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @foreach($requests as $request)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-900">#{{ $request->id }}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-900">{{ $request->title }}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-600">
                        {{ $request->hostel->name ?? 'N/A' }} - Room {{ $request->room->number ?? 'N/A' }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-600">{{ $request->reportedBy->name ?? 'N/A' }}</td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        @php
                            $priorityClass = match($request->priority) {
                                'low' => 'bg-gray-100 text-gray-700',
                                'medium' => 'bg-blue-100 text-blue-700',
                                'high' => 'bg-orange-100 text-orange-700',
                                'urgent' => 'bg-red-100 text-red-700',
                                default => 'bg-gray-100 text-gray-700'
                            };
                        @endphp
                        <span class="text-[10px] font-medium px-2 py-0.5 rounded-full {{ $priorityClass }}">
                            {{ ucfirst($request->priority) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        @php
                            $statusClass = match($request->status) {
                                'pending' => 'bg-yellow-100 text-yellow-700',
                                'in_progress' => 'bg-blue-100 text-blue-700',
                                'completed' => 'bg-green-100 text-green-700',
                                default => 'bg-gray-100 text-gray-700'
                            };
                        @endphp
                        <span class="text-[10px] font-medium px-2 py-0.5 rounded-full {{ $statusClass }}">
                            {{ str_replace('_', ' ', ucfirst($request->status)) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-600">{{ $request->created_at->format('M d, Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($requests->hasPages())
    <div class="px-4 py-3 border-t border-gray-100">
        {{ $requests->links() }}
    </div>
    @endif
</div>
@endsection
