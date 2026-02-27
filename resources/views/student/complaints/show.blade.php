@extends('layouts.student')

@section('title', 'Complaint Details')
@section('page-title', 'Complaint Details')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-center">
            <a href="{{ route('student.complaints') }}" class="text-gray-500 hover:text-gray-700 mr-4">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Complaint #{{ $complaint->id }}</h1>
                <p class="text-gray-500">Submitted on {{ $complaint->created_at->format('F d, Y h:i A') }}</p>
            </div>
            <div class="ml-auto">
                @php
                    $statusColors = [
                        'pending' => 'bg-yellow-100 text-yellow-800',
                        'in_progress' => 'bg-blue-100 text-blue-800',
                        'resolved' => 'bg-green-100 text-green-800',
                    ];
                    $statusColor = $statusColors[$complaint->status] ?? 'bg-gray-100 text-gray-800';
                @endphp
                <span class="px-4 py-2 rounded-full text-sm font-semibold {{ $statusColor }}">
                    {{ str_replace('_', ' ', ucfirst($complaint->status)) }}
                </span>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Left Column - Complaint Details -->
        <div class="md:col-span-2 space-y-6">
            <!-- Complaint Details -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Complaint Details</h2>

                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-gray-500">Subject</p>
                        <p class="text-lg font-medium text-gray-900">{{ $complaint->subject ?? $complaint->title }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Category</p>
                        <p class="font-medium text-gray-900">{{ ucfirst($complaint->category) }}</p>
                    </div>

                    @if($complaint->priority)
                    <div>
                        <p class="text-sm text-gray-500">Priority</p>
                        @php
                            $priorityColors = [
                                'low' => 'bg-gray-100 text-gray-600',
                                'medium' => 'bg-blue-100 text-blue-600',
                                'high' => 'bg-orange-100 text-orange-600',
                                'urgent' => 'bg-red-100 text-red-600',
                            ];
                            $priorityColor = $priorityColors[$complaint->priority] ?? 'bg-gray-100 text-gray-600';
                        @endphp
                        <span class="px-3 py-1 text-sm rounded-full {{ $priorityColor }}">
                            {{ ucfirst($complaint->priority) }}
                        </span>
                    </div>
                    @endif

                    <div>
                        <p class="text-sm text-gray-500">Description</p>
                        <p class="text-gray-700 whitespace-pre-line">{{ $complaint->description }}</p>
                    </div>
                </div>
            </div>

            <!-- Resolution Details -->
            @if($complaint->resolution_notes)
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Resolution</h2>

                <div class="bg-green-50 p-4 rounded-lg">
                    <p class="text-green-700 whitespace-pre-line">{{ $complaint->resolution_notes }}</p>
                    @if($complaint->resolved_at)
                        <p class="text-sm text-green-600 mt-2">
                            Resolved on {{ $complaint->resolved_at->format('F d, Y h:i A') }}
                        </p>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column - Related Info -->
        <div class="space-y-6">
            <!-- Timeline -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Timeline</h2>

                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 mr-3">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-plus text-blue-600 text-sm"></i>
                            </div>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Submitted</p>
                            <p class="text-sm text-gray-500">{{ $complaint->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>

                    @if($complaint->status == 'in_progress' || $complaint->status == 'resolved')
                    <div class="flex items-start">
                        <div class="flex-shrink-0 mr-3">
                            <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-spinner text-yellow-600 text-sm"></i>
                            </div>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">In Progress</p>
                            <p class="text-sm text-gray-500">{{ $complaint->updated_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                    @endif

                    @if($complaint->status == 'resolved')
                    <div class="flex items-start">
                        <div class="flex-shrink-0 mr-3">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-check-circle text-green-600 text-sm"></i>
                            </div>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Resolved</p>
                            <p class="text-sm text-gray-500">{{ $complaint->resolved_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Related Booking -->
            @if($complaint->booking)
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Related Booking</h2>

                <div class="space-y-2">
                    <p class="text-sm text-gray-600">
                        <i class="fas fa-building mr-2 text-gray-400"></i>
                        {{ $complaint->booking->hostel->name ?? 'N/A' }}
                    </p>
                    <p class="text-sm text-gray-600">
                        <i class="fas fa-door-open mr-2 text-gray-400"></i>
                        Room {{ $complaint->booking->room->number ?? 'N/A' }}
                    </p>
                    <p class="text-sm text-gray-600">
                        <i class="fas fa-calendar mr-2 text-gray-400"></i>
                        {{ $complaint->booking->check_in->format('M d, Y') }} - {{ $complaint->booking->check_out->format('M d, Y') }}
                    </p>
                    <a href="{{ route('student.bookings.show', $complaint->booking) }}"
                       class="mt-3 inline-block text-sm text-blue-600 hover:text-blue-800">
                        View Booking <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
