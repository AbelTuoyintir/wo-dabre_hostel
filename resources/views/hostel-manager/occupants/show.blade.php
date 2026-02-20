@extends('layouts.hostelmanage')

@section('title', 'Occupant Details')
@section('page-title', 'Occupant Details')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Header -->
    <div class="mb-4 bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex items-center">
            <a href="{{ route('hostel-manager.occupants') }}" class="text-gray-500 hover:text-gray-700 mr-3">
                <i class="fas fa-arrow-left text-xs"></i>
            </a>
            <div>
                <h2 class="text-sm font-semibold text-gray-800">{{ $student->name }}</h2>
                <p class="text-xs text-gray-500">{{ $student->student_id ?? 'No ID' }}</p>
            </div>
            <div class="ml-auto flex items-center space-x-2">
                <button onclick="contactOccupant({{ $student->id }})"
                        class="bg-blue-500 hover:bg-blue-600 text-white text-xs px-3 py-1.5 rounded-lg transition flex items-center">
                    <i class="fas fa-envelope mr-1"></i> Contact
                </button>
            </div>
        </div>
    </div>

    <!-- Student Info Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-4">
            <!-- Personal Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
                    <h3 class="text-xs font-semibold text-gray-700 uppercase">Personal Information</h3>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase">Full Name</p>
                            <p class="text-sm font-medium text-gray-900">{{ $student->name }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase">Student ID</p>
                            <p class="text-sm text-gray-900">{{ $student->student_id ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase">Email</p>
                            <p class="text-sm text-gray-900">{{ $student->email }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase">Phone</p>
                            <p class="text-sm text-gray-900">{{ $student->phone ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase">Gender</p>
                            <p class="text-sm text-gray-900">{{ ucfirst($student->gender ?? 'N/A') }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase">Member Since</p>
                            <p class="text-sm text-gray-900">{{ $student->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Current Booking -->
            @if(isset($currentBooking) && $currentBooking)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-4 py-3 bg-green-50 border-b border-green-100">
                    <h3 class="text-xs font-semibold text-green-700 uppercase flex items-center">
                        <i class="fas fa-home mr-1.5"></i>
                        Current Accommodation
                    </h3>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase">Hostel</p>
                            <p class="text-sm font-medium text-gray-900">{{ $currentBooking->hostel->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase">Room</p>
                            <p class="text-sm font-medium text-gray-900">{{ $currentBooking->room->number ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase">Check In</p>
                            <p class="text-sm text-gray-900">{{ $currentBooking->check_in->format('M d, Y') }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase">Check Out</p>
                            <p class="text-sm text-gray-900">{{ $currentBooking->check_out->format('M d, Y') }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase">Status</p>
                            @php
                                $statusClass = $currentBooking->status == 'confirmed' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700';
                            @endphp
                            <span class="text-[10px] font-medium px-2 py-0.5 rounded-full {{ $statusClass }}">
                                {{ ucfirst($currentBooking->status) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Booking History -->
            @if(isset($bookings) && $bookings->count() > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
                    <h3 class="text-xs font-semibold text-gray-700 uppercase">Booking History</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase">Hostel</th>
                                <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase">Room</th>
                                <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase">Check In</th>
                                <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase">Check Out</th>
                                <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach($bookings as $booking)
                            <tr>
                                <td class="px-4 py-2 whitespace-nowrap text-xs">{{ $booking->hostel->name ?? 'N/A' }}</td>
                                <td class="px-4 py-2 whitespace-nowrap text-xs">{{ $booking->room->number ?? 'N/A' }}</td>
                                <td class="px-4 py-2 whitespace-nowrap text-xs">{{ $booking->check_in->format('M d, Y') }}</td>
                                <td class="px-4 py-2 whitespace-nowrap text-xs">{{ $booking->check_out->format('M d, Y') }}</td>
                                <td class="px-4 py-2 whitespace-nowrap">
                                    @php
                                        $statusClass = match($booking->status) {
                                            'confirmed' => 'bg-green-100 text-green-700',
                                            'pending' => 'bg-yellow-100 text-yellow-700',
                                            'cancelled' => 'bg-red-100 text-red-700',
                                            'completed' => 'bg-gray-100 text-gray-700',
                                            default => 'bg-gray-100 text-gray-700'
                                        };
                                    @endphp
                                    <span class="text-[10px] font-medium px-2 py-0.5 rounded-full {{ $statusClass }}">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-4">
            <!-- Payment History -->
            @if(isset($payments) && $payments->count() > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-4 py-3 bg-purple-50 border-b border-purple-100">
                    <h3 class="text-xs font-semibold text-purple-700 uppercase flex items-center">
                        <i class="fas fa-credit-card mr-1.5"></i>
                        Payment History
                    </h3>
                </div>
                <div class="p-3 space-y-2">
                    @foreach($payments as $payment)
                    <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg">
                        <div>
                            <p class="text-xs font-medium text-gray-900">₵{{ number_format($payment->amount, 2) }}</p>
                            <p class="text-[10px] text-gray-500">{{ $payment->created_at->format('M d, Y') }}</p>
                        </div>
                        @php
                            $paymentStatusClass = match($payment->status) {
                                'completed' => 'bg-green-100 text-green-700',
                                'pending' => 'bg-yellow-100 text-yellow-700',
                                'failed' => 'bg-red-100 text-red-700',
                                default => 'bg-gray-100 text-gray-700'
                            };
                        @endphp
                        <span class="text-[8px] font-medium px-1.5 py-0.5 rounded-full {{ $paymentStatusClass }}">
                            {{ ucfirst($payment->status) }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Complaints History -->
            @if(isset($complaints) && $complaints->count() > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-4 py-3 bg-orange-50 border-b border-orange-100">
                    <h3 class="text-xs font-semibold text-orange-700 uppercase flex items-center">
                        <i class="fas fa-exclamation-triangle mr-1.5"></i>
                        Complaints
                    </h3>
                </div>
                <div class="p-3 space-y-2">
                    @foreach($complaints as $complaint)
                    <div class="p-2 bg-gray-50 rounded-lg">
                        <div class="flex items-center justify-between mb-1">
                            <p class="text-xs font-medium text-gray-900">{{ $complaint->title }}</p>
                            @php
                                $complaintStatusClass = match($complaint->status) {
                                    'pending' => 'bg-yellow-100 text-yellow-700',
                                    'in_progress' => 'bg-blue-100 text-blue-700',
                                    'resolved' => 'bg-green-100 text-green-700',
                                    default => 'bg-gray-100 text-gray-700'
                                };
                            @endphp
                            <span class="text-[8px] font-medium px-1.5 py-0.5 rounded-full {{ $complaintStatusClass }}">
                                {{ ucfirst($complaint->status) }}
                            </span>
                        </div>
                        <p class="text-[10px] text-gray-500">{{ $complaint->created_at->format('M d, Y') }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
                    <h3 class="text-xs font-semibold text-gray-700 uppercase">Quick Actions</h3>
                </div>
                <div class="p-3 space-y-2">
                    <button onclick="contactOccupant({{ $student->id }})"
                            class="w-full text-left px-3 py-2 text-xs bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition flex items-center">
                        <i class="fas fa-envelope mr-2"></i>
                        Send Message
                    </button>
                    <a href="#" class="block w-full text-left px-3 py-2 text-xs bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition flex items-center">
                        <i class="fas fa-history mr-2"></i>
                        View Full History
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Contact Modal (same as in index) -->
<div id="contactModal" class="modal">
    <!-- ... same modal content as index ... -->
</div>
@endsection
