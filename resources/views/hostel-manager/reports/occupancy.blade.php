@extends('layouts.hostelmanage')

@section('title', 'Occupancy Report')
@section('page-title', 'Occupancy Report')

@section('content')
<!-- Header -->
<div class="mb-4 bg-white rounded-xl shadow-sm border border-gray-100 p-4">
    <div class="flex items-center">
        <a href="{{ route('hostel-manager.reports') }}" class="text-gray-500 hover:text-gray-700 mr-3">
            <i class="fas fa-arrow-left text-xs"></i>
        </a>
        <div>
            <h2 class="text-sm font-semibold text-gray-800">Occupancy Report</h2>
            <p class="text-xs text-gray-500">Detailed room occupancy analysis across your hostels</p>
        </div>
        <div class="ml-auto">
            <a href="{{ route('hostel-manager.reports.export', 'occupancy') }}"
               class="bg-blue-500 hover:bg-blue-600 text-white text-xs px-3 py-1.5 rounded-lg transition flex items-center">
                <i class="fas fa-download mr-1"></i> Export Report
            </a>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @php
        $totalRooms = collect($occupancyData)->sum('total_rooms');
        $totalCapacity = collect($occupancyData)->sum('total_capacity');
        $totalOccupancy = collect($occupancyData)->sum('current_occupancy');
        $totalAvailable = collect($occupancyData)->sum('available_spaces');
        $overallRate = $totalCapacity > 0 ? round(($totalOccupancy / $totalCapacity) * 100, 2) : 0;
    @endphp

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-medium text-gray-500 uppercase">Total Rooms</span>
            <span class="bg-blue-100 text-blue-600 text-xs px-2 py-1 rounded-full">{{ $totalRooms }}</span>
        </div>
        <div class="text-2xl font-bold text-gray-800">{{ $totalRooms }}</div>
        <div class="mt-2 text-xs text-gray-500">Across all hostels</div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-medium text-gray-500 uppercase">Total Capacity</span>
            <span class="bg-green-100 text-green-600 text-xs px-2 py-1 rounded-full">{{ $totalCapacity }}</span>
        </div>
        <div class="text-2xl font-bold text-gray-800">{{ $totalCapacity }}</div>
        <div class="mt-2 text-xs text-gray-500">Maximum occupants</div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-medium text-gray-500 uppercase">Current Occupancy</span>
            <span class="bg-purple-100 text-purple-600 text-xs px-2 py-1 rounded-full">{{ $totalOccupancy }}</span>
        </div>
        <div class="text-2xl font-bold text-gray-800">{{ $totalOccupancy }}</div>
        <div class="mt-2 text-xs text-gray-500">Currently staying</div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-medium text-gray-500 uppercase">Overall Rate</span>
            <span class="bg-orange-100 text-orange-600 text-xs px-2 py-1 rounded-full">{{ $overallRate }}%</span>
        </div>
        <div class="text-2xl font-bold text-gray-800">{{ $overallRate }}%</div>
        <div class="mt-2 text-xs text-gray-500">Occupancy rate</div>
    </div>
</div>

<!-- Overall Occupancy Chart -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
    <h3 class="text-xs font-semibold text-gray-700 uppercase mb-4 flex items-center">
        <i class="fas fa-chart-pie text-blue-500 mr-1.5 text-xs"></i>
        Overall Occupancy Distribution
    </h3>
    <div class="h-80">
        <canvas id="overallOccupancyChart"></canvas>
    </div>
</div>

<!-- Hostel-wise Occupancy Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
    <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
        <h3 class="text-xs font-semibold text-gray-700 uppercase">Hostel-wise Occupancy Details</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase">Hostel</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase">Total Rooms</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase">Total Capacity</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase">Current Occupancy</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase">Available Spaces</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase">Occupancy Rate</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @foreach($occupancyData as $data)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 whitespace-nowrap">
                        <span class="text-xs font-medium text-gray-900">{{ $data['hostel'] }}</span>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-600">{{ $data['total_rooms'] }}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-600">{{ $data['total_capacity'] }}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-600">{{ $data['current_occupancy'] }}</td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <span class="text-xs font-medium {{ $data['available_spaces'] > 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $data['available_spaces'] }}
                        </span>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <div class="flex items-center">
                            <span class="text-xs font-medium text-gray-900 mr-2">{{ $data['occupancy_rate'] }}%</span>
                            <div class="w-16 bg-gray-200 rounded-full h-1.5">
                                <div class="bg-blue-600 h-1.5 rounded-full" style="width: {{ $data['occupancy_rate'] }}%"></div>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        @php
                            $statusClass = $data['occupancy_rate'] >= 90 ? 'bg-green-100 text-green-700' :
                                          ($data['occupancy_rate'] >= 70 ? 'bg-blue-100 text-blue-700' :
                                          ($data['occupancy_rate'] >= 50 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700'));
                            $statusText = $data['occupancy_rate'] >= 90 ? 'Excellent' :
                                         ($data['occupancy_rate'] >= 70 ? 'Good' :
                                         ($data['occupancy_rate'] >= 50 ? 'Average' : 'Low'));
                        @endphp
                        <span class="text-[10px] font-medium px-2 py-0.5 rounded-full {{ $statusClass }}">
                            {{ $statusText }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-gray-50">
                <tr>
                    <td class="px-4 py-3 text-xs font-bold text-gray-900">Total</td>
                    <td class="px-4 py-3 text-xs font-bold text-gray-900">{{ $totalRooms }}</td>
                    <td class="px-4 py-3 text-xs font-bold text-gray-900">{{ $totalCapacity }}</td>
                    <td class="px-4 py-3 text-xs font-bold text-gray-900">{{ $totalOccupancy }}</td>
                    <td class="px-4 py-3 text-xs font-bold text-gray-900">{{ $totalAvailable }}</td>
                    <td class="px-4 py-3 text-xs font-bold text-gray-900">{{ $overallRate }}%</td>
                    <td class="px-4 py-3"></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<!-- Room Type Analysis -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
    <h3 class="text-xs font-semibold text-gray-700 uppercase mb-4 flex items-center">
        <i class="fas fa-bed text-green-500 mr-1.5 text-xs"></i>
        Room Type Analysis
    </h3>

    @php
        $roomTypes = [];
        foreach($hostels as $hostel) {
            $rooms = $hostel->rooms;
            $roomTypes[$hostel->name] = [
                'single' => $rooms->where('capacity', 1)->count(),
                'double' => $rooms->where('capacity', 2)->count(),
                'triple' => $rooms->where('capacity', 3)->count(),
                'dormitory' => $rooms->where('capacity', '>=', 4)->count(),
            ];
        }
    @endphp

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase">Hostel</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase">Single Rooms</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase">Double Rooms</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase">Triple Rooms</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase">Dormitories</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase">Total</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @foreach($roomTypes as $hostelName => $types)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 whitespace-nowrap text-xs font-medium text-gray-900">{{ $hostelName }}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-600">{{ $types['single'] }}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-600">{{ $types['double'] }}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-600">{{ $types['triple'] }}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-600">{{ $types['dormitory'] }}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-xs font-medium text-gray-900">
                        {{ array_sum($types) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-gray-50">
                <tr>
                    <td class="px-4 py-3 text-xs font-bold text-gray-900">Total</td>
                    <td class="px-4 py-3 text-xs font-bold text-gray-900">{{ collect($roomTypes)->sum('single') }}</td>
                    <td class="px-4 py-3 text-xs font-bold text-gray-900">{{ collect($roomTypes)->sum('double') }}</td>
                    <td class="px-4 py-3 text-xs font-bold text-gray-900">{{ collect($roomTypes)->sum('triple') }}</td>
                    <td class="px-4 py-3 text-xs font-bold text-gray-900">{{ collect($roomTypes)->sum('dormitory') }}</td>
                    <td class="px-4 py-3 text-xs font-bold text-gray-900">{{ $totalRooms }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<!-- Occupancy Trends -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
    <h3 class="text-xs font-semibold text-gray-700 uppercase mb-4 flex items-center">
        <i class="fas fa-chart-line text-purple-500 mr-1.5 text-xs"></i>
        Occupancy Insights
    </h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="space-y-3">
            <div class="flex items-start">
                <i class="fas fa-check-circle text-green-500 mt-0.5 mr-2 text-xs"></i>
                <span class="text-xs text-gray-600">
                    <span class="font-medium">Highest occupancy:</span>
                    @php
                        $highest = collect($occupancyData)->sortByDesc('occupancy_rate')->first();
                    @endphp
                    {{ $highest['hostel'] ?? 'N/A' }} ({{ $highest['occupancy_rate'] ?? 0 }}%)
                </span>
            </div>
            <div class="flex items-start">
                <i class="fas fa-exclamation-triangle text-yellow-500 mt-0.5 mr-2 text-xs"></i>
                <span class="text-xs text-gray-600">
                    <span class="font-medium">Lowest occupancy:</span>
                    @php
                        $lowest = collect($occupancyData)->sortBy('occupancy_rate')->first();
                    @endphp
                    {{ $lowest['hostel'] ?? 'N/A' }} ({{ $lowest['occupancy_rate'] ?? 0 }}%)
                </span>
            </div>
            <div class="flex items-start">
                <i class="fas fa-bed text-blue-500 mt-0.5 mr-2 text-xs"></i>
                <span class="text-xs text-gray-600">
                    <span class="font-medium">Total available spaces:</span>
                    {{ $totalAvailable }}
                </span>
            </div>
        </div>

        <div class="space-y-3">
            <div class="flex items-start">
                <i class="fas fa-male text-blue-500 mt-0.5 mr-2 text-xs"></i>
                <span class="text-xs text-gray-600">
                    <span class="font-medium">Average room size:</span>
                    @php
                        $avgSize = $totalCapacity > 0 ? round($totalCapacity / $totalRooms, 1) : 0;
                    @endphp
                    {{ $avgSize }} persons per room
                </span>
            </div>
            <div class="flex items-start">
                <i class="fas fa-chart-bar text-green-500 mt-0.5 mr-2 text-xs"></i>
                <span class="text-xs text-gray-600">
                    <span class="font-medium">Overall efficiency:</span>
                    {{ $overallRate }}% occupied
                </span>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Overall Occupancy Chart
    const ctx = document.getElementById('overallOccupancyChart')?.getContext('2d');
    if (ctx) {
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Occupied', 'Available', 'Maintenance'],
                datasets: [{
                    data: [
                        {{ $totalOccupancy }},
                        {{ $totalAvailable }},
                        {{ collect($occupancyData)->sum('maintenance_rooms') ?? 0 }}
                    ],
                    backgroundColor: ['#3b82f6', '#10b981', '#f59e0b'],
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
                            font: { size: 11 },
                            padding: 15,
                            boxWidth: 12
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                },
                cutout: '60%'
            }
        });
    }

    // Optional: Add a bar chart for hostel comparison
    const hostelCtx = document.getElementById('hostelComparisonChart')?.getContext('2d');
    if (hostelCtx) {
        const hostelNames = {!! json_encode(collect($occupancyData)->pluck('hostel')->toArray()) !!};
        const occupancyRates = {!! json_encode(collect($occupancyData)->pluck('occupancy_rate')->toArray()) !!};

        new Chart(hostelCtx, {
            type: 'bar',
            data: {
                labels: hostelNames,
                datasets: [{
                    label: 'Occupancy Rate (%)',
                    data: occupancyRates,
                    backgroundColor: '#3b82f6',
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            },
                            font: { size: 10 }
                        }
                    },
                    x: {
                        ticks: {
                            font: { size: 10 },
                            maxRotation: 45,
                            minRotation: 45
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush
