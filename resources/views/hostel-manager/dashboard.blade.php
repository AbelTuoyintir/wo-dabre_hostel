@extends('layouts.hostelmanage')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<!-- Welcome Section with Hostel Info -->
<div class="mb-6 bg-white rounded-xl shadow-sm p-4 border-l-4 border-blue-500">
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <div class="bg-blue-100 p-2 rounded-lg mr-3">
                <i class="fas fa-building text-blue-600 text-sm"></i>
            </div>
            <div>
                <h2 class="text-sm font-semibold text-gray-800">Welcome back, {{ Auth::user()->name }}</h2>
                <p class="text-xs text-gray-500">{{ now()->format('l, F j, Y') }}</p>
            </div>
        </div>
        @if(isset($managedHostels) && $managedHostels->count() > 0)
            <div class="flex items-center space-x-2">
                <span class="text-xs text-gray-500">Managing:</span>
                <span class="text-xs font-semibold bg-blue-100 text-blue-800 px-3 py-1 rounded-full">
                    {{ $managedHostels->count() }} {{ Str::plural('Hostel', $managedHostels->count()) }}
                </span>
            </div>
        @endif
    </div>
</div>

<!-- Key Stats Cards - SM Font -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <!-- Total Rooms -->
    <div class="stat-card bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Rooms</span>
            <div class="bg-blue-50 p-1.5 rounded-lg">
                <i class="fas fa-bed text-blue-500 text-xs"></i>
            </div>
        </div>
        <div class="flex items-end justify-between">
            <h3 class="text-lg font-bold text-gray-800">{{ $roomStats['total_rooms'] ?? 0 }}</h3>
            <span class="text-xs text-green-600 bg-green-50 px-2 py-0.5 rounded-full">
                <i class="fas fa-arrow-up mr-0.5"></i>{{ $roomStats['available_rooms'] ?? 0 }} available
            </span>
        </div>
        <div class="mt-2 flex items-center text-xs text-gray-500">
            <i class="fas fa-chart-line mr-1 text-gray-400"></i>
            <span>{{ $roomStats['occupied_rooms'] ?? 0 }} occupied</span>
            <span class="mx-1">•</span>
            <span>{{ $roomStats['maintenance_rooms'] ?? 0 }} maintenance</span>
        </div>
    </div>

    <!-- Occupancy Rate -->
    <div class="stat-card bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Occupancy</span>
            <div class="bg-green-50 p-1.5 rounded-lg">
                <i class="fas fa-chart-pie text-green-500 text-xs"></i>
            </div>
        </div>
        <div class="flex items-end justify-between">
            <h3 class="text-lg font-bold text-gray-800">{{ $roomStats['occupancy_rate'] ?? 0 }}%</h3>
            <span class="text-xs text-gray-600 bg-gray-50 px-2 py-0.5 rounded-full">
                {{ $roomStats['current_occupancy'] ?? 0 }}/{{ $roomStats['total_capacity'] ?? 0 }} persons
            </span>
        </div>
        <div class="mt-2">
            <div class="w-full bg-gray-100 rounded-full h-1.5">
                <div class="bg-green-500 h-1.5 rounded-full" style="width: {{ $roomStats['occupancy_rate'] ?? 0 }}%"></div>
            </div>
        </div>
    </div>

    <!-- Pending Bookings -->
    <div class="stat-card bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Pending Bookings</span>
            <div class="bg-yellow-50 p-1.5 rounded-lg">
                <i class="fas fa-clock text-yellow-500 text-xs"></i>
            </div>
        </div>
        <div class="flex items-end justify-between">
            <h3 class="text-lg font-bold text-gray-800">{{ $bookingStats['pending_bookings'] ?? 0 }}</h3>
            <a href="{{ route('hostel-manager.bookings', ['status' => 'pending']) }}"
               class="text-xs text-blue-600 hover:text-blue-800 bg-blue-50 px-2 py-0.5 rounded-full">
                view all
            </a>
        </div>
        <div class="mt-2 flex items-center text-xs text-gray-500">
            <i class="fas fa-check-circle mr-1 text-green-400"></i>
            <span>{{ $bookingStats['confirmed_bookings'] ?? 0 }} confirmed</span>
            <span class="mx-1">•</span>
            <i class="fas fa-times-circle mr-1 text-red-400"></i>
            <span>{{ $bookingStats['cancelled_bookings'] ?? 0 }} cancelled</span>
        </div>
    </div>

    <!-- Monthly Revenue -->
    <div class="stat-card bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Monthly Revenue</span>
            <div class="bg-purple-50 p-1.5 rounded-lg">
                <i class="fas fa-money-bill-wave text-purple-500 text-xs"></i>
            </div>
        </div>
        <div class="flex items-end justify-between">
            <h3 class="text-lg font-bold text-gray-800">₵{{ number_format($revenueStats['this_month'] ?? 0) }}</h3>
            <span class="text-xs text-purple-600 bg-purple-50 px-2 py-0.5 rounded-full">
                total: ₵{{ number_format($revenueStats['total'] ?? 0) }}
            </span>
        </div>
        <div class="mt-2 flex items-center text-xs text-gray-500">
            <i class="fas fa-calendar-day mr-1 text-gray-400"></i>
            <span>Today: ₵{{ number_format($revenueStats['today'] ?? 0) }}</span>
            <span class="mx-1">•</span>
            <i class="fas fa-clock mr-1 text-yellow-400"></i>
            <span>₵{{ number_format($revenueStats['pending_payments'] ?? 0) }} pending</span>
        </div>
    </div>
</div>

<!-- Second Row Stats - More Details -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <!-- Today's Schedule -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <h3 class="text-xs font-semibold text-gray-700 uppercase tracking-wider mb-3 flex items-center">
            <i class="fas fa-calendar-day text-blue-500 mr-1.5 text-xs"></i>
            Today's Schedule
        </h3>
        <div class="space-y-2">
            <div class="flex items-center justify-between text-xs">
                <div class="flex items-center">
                    <div class="w-6 h-6 bg-green-50 rounded-full flex items-center justify-center mr-2">
                        <i class="fas fa-sign-in-alt text-green-500 text-xs"></i>
                    </div>
                    <span class="text-gray-600">Check-ins</span>
                </div>
                <span class="font-semibold text-green-600">{{ $bookingStats['today_checkins'] ?? 0 }}</span>
            </div>
            <div class="flex items-center justify-between text-xs">
                <div class="flex items-center">
                    <div class="w-6 h-6 bg-red-50 rounded-full flex items-center justify-center mr-2">
                        <i class="fas fa-sign-out-alt text-red-500 text-xs"></i>
                    </div>
                    <span class="text-gray-600">Check-outs</span>
                </div>
                <span class="font-semibold text-red-600">{{ $bookingStats['today_checkouts'] ?? 0 }}</span>
            </div>
            <div class="flex items-center justify-between text-xs pt-1 border-t border-gray-100">
                <div class="flex items-center">
                    <div class="w-6 h-6 bg-blue-50 rounded-full flex items-center justify-center mr-2">
                        <i class="fas fa-bed text-blue-500 text-xs"></i>
                    </div>
                    <span class="text-gray-600">Active bookings</span>
                </div>
                <span class="font-semibold text-blue-600">{{ $bookingStats['active_bookings'] ?? 0 }}</span>
            </div>
        </div>
    </div>

    <!-- Complaints Summary -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <h3 class="text-xs font-semibold text-gray-700 uppercase tracking-wider mb-3 flex items-center">
            <i class="fas fa-exclamation-triangle text-red-500 mr-1.5 text-xs"></i>
            Complaints
        </h3>
        <div class="grid grid-cols-2 gap-2">
            <div class="bg-gray-50 p-2 rounded-lg text-center">
                <span class="text-xs text-gray-500 block">Pending</span>
                <span class="text-sm font-bold text-yellow-600">{{ $complaintStats['pending'] ?? 0 }}</span>
            </div>
            <div class="bg-gray-50 p-2 rounded-lg text-center">
                <span class="text-xs text-gray-500 block">In Progress</span>
                <span class="text-sm font-bold text-blue-600">{{ $complaintStats['in_progress'] ?? 0 }}</span>
            </div>
            <div class="bg-gray-50 p-2 rounded-lg text-center">
                <span class="text-xs text-gray-500 block">Resolved</span>
                <span class="text-sm font-bold text-green-600">{{ $complaintStats['resolved'] ?? 0 }}</span>
            </div>
            <div class="bg-gray-50 p-2 rounded-lg text-center">
                <span class="text-xs text-gray-500 block">Urgent</span>
                <span class="text-sm font-bold text-red-600">{{ $complaintStats['urgent'] ?? 0 }}</span>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <h3 class="text-xs font-semibold text-gray-700 uppercase tracking-wider mb-3 flex items-center">
            <i class="fas fa-users text-purple-500 mr-1.5 text-xs"></i>
            Occupants
        </h3>
        <div class="space-y-2">
            <div class="flex items-center justify-between text-xs">
                <span class="text-gray-600">Total students</span>
                <span class="font-semibold">{{ $occupantStats['total_students'] ?? 0 }}</span>
            </div>
            <div class="flex items-center justify-between text-xs">
                <div class="flex items-center">
                    <i class="fas fa-male text-blue-500 mr-1.5 text-xs"></i>
                    <span class="text-gray-600">Male</span>
                </div>
                <span class="font-semibold">{{ $occupantStats['male_students'] ?? 0 }}</span>
            </div>
            <div class="flex items-center justify-between text-xs">
                <div class="flex items-center">
                    <i class="fas fa-female text-pink-500 mr-1.5 text-xs"></i>
                    <span class="text-gray-600">Female</span>
                </div>
                <span class="font-semibold">{{ $occupantStats['female_students'] ?? 0 }}</span>
            </div>
            <div class="flex items-center justify-between text-xs pt-1 border-t border-gray-100">
                <span class="text-gray-600">New this month</span>
                <span class="font-semibold text-green-600">+{{ $occupantStats['new_this_month'] ?? 0 }}</span>
            </div>
            <div class="flex items-center justify-between text-xs">
                <span class="text-gray-600">Leaving this month</span>
                <span class="font-semibold text-orange-600">{{ $occupantStats['leaving_this_month'] ?? 0 }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Live Room Availability -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
    <div class="px-4 py-3 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-100 flex items-center justify-between">
        <h3 class="text-xs font-semibold text-gray-700 uppercase tracking-wider flex items-center">
            <i class="fas fa-bed text-blue-500 mr-2 text-xs"></i>
            Live Room Availability
            <span class="ml-2 bg-green-100 text-green-700 text-[10px] px-2 py-0.5 rounded-full flex items-center">
                <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1 animate-pulse"></span>
                LIVE
            </span>
        </h3>
        <span class="text-[10px] text-gray-500">{{ count($roomSpaceStats) }} rooms total</span>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase tracking-wider">Room</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase tracking-wider">Hostel</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase tracking-wider">Capacity</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase tracking-wider">Occupied</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase tracking-wider">Available</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase tracking-wider">Gender</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase tracking-wider">Price</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($roomSpaceStats as $room)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2 whitespace-nowrap">
                        <span class="text-xs font-medium text-gray-900">{{ $room['room_number'] }}</span>
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap">
                        <span class="text-xs text-gray-600">{{ Str::limit($room['hostel'], 15) }}</span>
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap">
                        <span class="text-xs text-gray-600">{{ $room['capacity'] }}</span>
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap">
                        <span class="text-xs text-gray-600">{{ $room['occupied'] }}</span>
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap">
                        <span class="text-xs font-medium {{ $room['available_spaces'] > 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $room['available_spaces'] }}
                        </span>
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap">
                        @php
                            $statusClass = match($room['status']) {
                                'available' => 'bg-green-100 text-green-700',
                                'occupied' => 'bg-yellow-100 text-yellow-700',
                                'maintenance' => 'bg-red-100 text-red-700',
                                default => 'bg-gray-100 text-gray-700'
                            };
                        @endphp
                        <span class="text-[10px] font-medium px-2 py-0.5 rounded-full {{ $statusClass }}">
                            {{ ucfirst($room['status']) }}
                        </span>
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap">
                        @php
                            $genderClass = match($room['gender']) {
                                'male' => 'bg-blue-100 text-blue-700',
                                'female' => 'bg-pink-100 text-pink-700',
                                default => 'bg-purple-100 text-purple-700'
                            };
                        @endphp
                        <span class="text-[10px] font-medium px-2 py-0.5 rounded-full {{ $genderClass }}">
                            {{ ucfirst($room['gender']) }}
                        </span>
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap">
                        <span class="text-xs font-medium text-gray-900">₵{{ number_format($room['price']) }}</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-4 py-6 text-center">
                        <div class="flex flex-col items-center">
                            <i class="fas fa-bed text-gray-300 text-xl mb-2"></i>
                            <p class="text-xs text-gray-500">No rooms found</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(count($roomSpaceStats) > 5)
    <div class="px-4 py-2 bg-gray-50 border-t border-gray-100 text-center">
        <a href="{{ route('hostel-manager.rooms') }}" class="text-[10px] text-blue-600 hover:text-blue-800">
            View all rooms <i class="fas fa-chevron-right ml-1"></i>
        </a>
    </div>
    @endif
</div>

<!-- Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
    <!-- Monthly Bookings Chart -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <h3 class="text-xs font-semibold text-gray-700 uppercase tracking-wider mb-3 flex items-center">
            <i class="fas fa-chart-line text-blue-500 mr-1.5 text-xs"></i>
            Monthly Bookings Trend
        </h3>
        <div class="h-40">
            <canvas id="bookingsChart" data-bookings='@json($monthlyBookings)'></canvas>
        </div>
    </div>

    <!-- Revenue by Hostel -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <h3 class="text-xs font-semibold text-gray-700 uppercase tracking-wider mb-3 flex items-center">
            <i class="fas fa-chart-pie text-purple-500 mr-1.5 text-xs"></i>
            Revenue by Hostel
        </h3>
        <div class="h-40">
            <canvas id="revenueChart" data-revenue='@json($revenueByHostel)'></canvas>
        </div>
    </div>
</div>

<!-- Recent Activities -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-4 py-3 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-100 flex items-center justify-between">
        <h3 class="text-xs font-semibold text-gray-700 uppercase tracking-wider flex items-center">
            <i class="fas fa-history text-gray-500 mr-1.5 text-xs"></i>
            Recent Activities
        </h3>
        <span class="text-[10px] text-gray-500">Last 10 activities</span>
    </div>

    <div class="divide-y divide-gray-100">
        @forelse($recentActivities as $activity)
        <div class="px-4 py-2.5 hover:bg-gray-50 flex items-start">
            <div class="flex-shrink-0 mr-3">
                <div class="w-6 h-6 rounded-full bg-{{ $activity['color'] }}-50 flex items-center justify-center">
                    <i class="fas fa-{{ $activity['icon'] }} text-{{ $activity['color'] }}-500 text-xs"></i>
                </div>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-medium text-gray-900">{{ $activity['title'] }}</p>
                    <span class="text-[10px] text-gray-400">{{ $activity['time'] }}</span>
                </div>
                <p class="text-[10px] text-gray-500 mt-0.5">{{ $activity['description'] }}</p>
            </div>
            @php
                $activityStatusClass = match($activity['status']) {
                    'pending' => 'bg-yellow-100 text-yellow-700',
                    'confirmed', 'completed' => 'bg-green-100 text-green-700',
                    'cancelled' => 'bg-red-100 text-red-700',
                    default => 'bg-blue-100 text-blue-700'
                };
            @endphp
            <span class="ml-2 text-[8px] font-medium px-1.5 py-0.5 rounded-full {{ $activityStatusClass }}">
                {{ ucfirst($activity['status']) }}
            </span>
        </div>
        @empty
        <div class="px-4 py-6 text-center">
            <i class="fas fa-history text-gray-300 text-xl mb-2"></i>
            <p class="text-xs text-gray-500">No recent activities</p>
        </div>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Bookings Chart
    const bookingsCanvas = document.getElementById('bookingsChart');
    if (bookingsCanvas) {
        const bookingsData = JSON.parse(bookingsCanvas.dataset.bookings || '[]');
        new Chart(bookingsCanvas, {
            type: 'line',
            data: {
                labels: bookingsData.map(item => item.month),
                datasets: [{
                    label: 'Bookings',
                    data: bookingsData.map(item => item.bookings),
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
                    pointRadius: 3,
                    pointBackgroundColor: '#3b82f6',
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            font: {
                                size: 10
                            }
                        },
                        grid: {
                            color: '#f0f0f0'
                        }
                    },
                    x: {
                        ticks: {
                            font: {
                                size: 9
                            },
                            maxRotation: 45,
                            minRotation: 45
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }

    // Revenue Chart
    const revenueCanvas = document.getElementById('revenueChart');
    if (revenueCanvas) {
        const revenueData = JSON.parse(revenueCanvas.dataset.revenue || '[]');
        new Chart(revenueCanvas, {
            type: 'doughnut',
            data: {
                labels: revenueData.map(item => item.hostel),
                datasets: [{
                    data: revenueData.map(item => item.revenue),
                    backgroundColor: [
                        '#3b82f6',
                        '#8b5cf6',
                        '#10b981',
                        '#f59e0b',
                        '#ef4444'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: {
                                size: 10
                            },
                            boxWidth: 10,
                            padding: 8
                        }
                    }
                },
                cutout: '65%'
            }
        });
    }
});
</script>
 @push('scripts') section
<script>
// Auto-refresh dashboard every 30 seconds (for true "live" feel)
let refreshInterval = setInterval(function() {
    // Show loading indicator
    document.getElementById('loadingSpinner').classList.remove('hidden');

    // Reload page data via AJAX (simpler: just reload the page)
    window.location.reload();
}, 30000); // 30 seconds

// Optional: Clear interval when navigating away
window.addEventListener('beforeunload', function() {
    clearInterval(refreshInterval);
});
</script>
@endpush
