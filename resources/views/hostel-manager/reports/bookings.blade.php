@extends('layouts.hostelmanage')

@section('title', 'Bookings Report')
@section('page-title', 'Bookings Report')

@section('content')
<!-- Header -->
<div class="mb-4 bg-white rounded-xl shadow-sm border border-gray-100 p-4">
    <div class="flex items-center">
        <a href="{{ route('hostel-manager.reports') }}" class="text-gray-500 hover:text-gray-700 mr-3">
            <i class="fas fa-arrow-left text-xs"></i>
        </a>
        <div>
            <h2 class="text-sm font-semibold text-gray-800">Bookings Report</h2>
            <p class="text-xs text-gray-500">Booking trends and analysis</p>
        </div>
        <div class="ml-auto flex items-center space-x-2">
            <select id="periodSelect" class="text-xs border-gray-300 rounded-lg px-3 py-1.5">
                <option value="week" {{ $period == 'week' ? 'selected' : '' }}>Last 7 days</option>
                <option value="month" {{ $period == 'month' ? 'selected' : '' }}>Last 30 days</option>
                <option value="year" {{ $period == 'year' ? 'selected' : '' }}>Last 12 months</option>
            </select>
            <a href="{{ route('hostel-manager.reports.export', 'bookings') }}"
               class="bg-blue-500 hover:bg-blue-600 text-white text-xs px-3 py-1.5 rounded-lg transition flex items-center">
                <i class="fas fa-download mr-1"></i> Export Report
            </a>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-medium text-gray-500 uppercase">Total Bookings</span>
            <span class="bg-blue-100 text-blue-600 text-xs px-2 py-1 rounded-full">{{ $totalBookings }}</span>
        </div>
        <div class="text-2xl font-bold text-gray-800">{{ $totalBookings }}</div>
        <div class="mt-2 text-xs text-gray-500">In selected period</div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-medium text-gray-500 uppercase">Total Revenue</span>
            <span class="bg-green-100 text-green-600 text-xs px-2 py-1 rounded-full">GHS</span>
        </div>
        <div class="text-2xl font-bold text-gray-800">₵{{ number_format($totalRevenue, 2) }}</div>
        <div class="mt-2 text-xs text-gray-500">In selected period</div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-medium text-gray-500 uppercase">Average per Booking</span>
            <span class="bg-purple-100 text-purple-600 text-xs px-2 py-1 rounded-full">₵</span>
        </div>
        <div class="text-2xl font-bold text-gray-800">₵{{ number_format($totalBookings > 0 ? $totalRevenue / $totalBookings : 0, 2) }}</div>
        <div class="mt-2 text-xs text-gray-500">Average value</div>
    </div>
</div>

<!-- Charts -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
    <!-- Bookings Chart -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <h3 class="text-xs font-semibold text-gray-700 uppercase mb-4 flex items-center">
            <i class="fas fa-calendar-check text-blue-500 mr-1.5 text-xs"></i>
            Bookings Trend
        </h3>
        <div class="h-80">
            <canvas id="bookingsChart"></canvas>
        </div>
    </div>

    <!-- Revenue Chart -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <h3 class="text-xs font-semibold text-gray-700 uppercase mb-4 flex items-center">
            <i class="fas fa-money-bill-wave text-green-500 mr-1.5 text-xs"></i>
            Revenue Trend
        </h3>
        <div class="h-80">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>
</div>

<!-- Hostel Breakdown -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
        <h3 class="text-xs font-semibold text-gray-700 uppercase">Bookings by Hostel</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase">Hostel</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase">Total Bookings</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase">Revenue</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase">Avg. per Booking</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase">Occupancy Rate</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @foreach($hostels as $hostel)
                @php
                    $bookingsCount = $hostel->bookings()->count();
                    $revenue = \App\Models\Payment::whereHas('booking', fn($q) => $q->where('hostel_id', $hostel->id))
                        ->where('status', 'completed')
                        ->sum('amount');
                    $rooms = $hostel->rooms;
                    $occupiedRooms = $rooms->where('current_occupancy', '>', 0)->count();
                    $occupancyRate = $rooms->count() > 0 ? round(($occupiedRooms / $rooms->count()) * 100, 2) : 0;
                @endphp
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 whitespace-nowrap text-xs font-medium text-gray-900">{{ $hostel->name }}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-600">{{ $bookingsCount }}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-xs text-green-600 font-medium">₵{{ number_format($revenue, 2) }}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-600">
                        ₵{{ number_format($bookingsCount > 0 ? $revenue / $bookingsCount : 0, 2) }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <div class="flex items-center">
                            <span class="text-xs text-gray-600 mr-2">{{ $occupancyRate }}%</span>
                            <div class="w-16 bg-gray-200 rounded-full h-1.5">
                                <div class="bg-blue-600 h-1.5 rounded-full" style="width: {{ $occupancyRate }}%"></div>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Bookings Chart
    const bookingsCtx = document.getElementById('bookingsChart')?.getContext('2d');
    if (bookingsCtx) {
        new Chart(bookingsCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($labels) !!},
                datasets: [{
                    label: 'Bookings',
                    data: {!! json_encode($bookingsData) !!},
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#3b82f6'
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
                        ticks: {
                            stepSize: 1,
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

    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart')?.getContext('2d');
    if (revenueCtx) {
        new Chart(revenueCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($labels) !!},
                datasets: [{
                    label: 'Revenue (GHS)',
                    data: {!! json_encode($revenueData) !!},
                    backgroundColor: '#10b981',
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return '₵' + context.raw.toFixed(2);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₵' + value;
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

    // Period selector
    document.getElementById('periodSelect')?.addEventListener('change', function() {
        window.location.href = '{{ route("hostel-manager.reports.bookings") }}?period=' + this.value;
    });
});
</script>
@endpush
