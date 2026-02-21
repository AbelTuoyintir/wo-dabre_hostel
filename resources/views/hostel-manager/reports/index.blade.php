@extends('layouts.hostelmanage')

@section('title', 'Reports & Analytics')
@section('page-title', 'Reports & Analytics')

@section('content')
<!-- Header -->
<div class="mb-4 bg-white rounded-xl shadow-sm border border-gray-100 p-4">
    <div class="flex items-center">
        <div class="bg-purple-50 p-2 rounded-lg mr-3">
            <i class="fas fa-chart-bar text-purple-500 text-sm"></i>
        </div>
        <div>
            <h2 class="text-sm font-semibold text-gray-800">Reports Dashboard</h2>
            <p class="text-xs text-gray-500">Comprehensive analytics and insights for your hostels</p>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-medium text-gray-500 uppercase">Total Revenue</span>
            <span class="text-green-500 text-xs font-medium">+12.5%</span>
        </div>
        <div class="text-xl font-bold text-gray-800">₵{{ number_format($data['total_revenue'] ?? 0, 2) }}</div>
        <div class="mt-2 flex items-center text-xs text-gray-500">
            <i class="fas fa-calendar mr-1"></i>
            <span>This year</span>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-medium text-gray-500 uppercase">Total Bookings</span>
            <span class="text-blue-500 text-xs font-medium">+8.2%</span>
        </div>
        <div class="text-xl font-bold text-gray-800">{{ $data['total_bookings'] ?? 0 }}</div>
        <div class="mt-2 flex items-center text-xs text-gray-500">
            <i class="fas fa-calendar-check mr-1"></i>
            <span>All time</span>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-medium text-gray-500 uppercase">Active Occupants</span>
            <span class="text-purple-500 text-xs font-medium">{{ $data['occupancy_rate'] ?? 0 }}%</span>
        </div>
        <div class="text-xl font-bold text-gray-800">{{ $data['total_occupants'] ?? 0 }}</div>
        <div class="mt-2 flex items-center text-xs text-gray-500">
            <i class="fas fa-users mr-1"></i>
            <span>Currently staying</span>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-medium text-gray-500 uppercase">Available Rooms</span>
            <span class="text-green-500 text-xs font-medium">{{ $data['available_rooms'] ?? 0 }} rooms</span>
        </div>
        <div class="text-xl font-bold text-gray-800">{{ $data['total_rooms'] ?? 0 }}</div>
        <div class="mt-2 flex items-center text-xs text-gray-500">
            <i class="fas fa-bed mr-1"></i>
            <span>Total rooms</span>
        </div>
    </div>
</div>

<!-- Charts Row 1 -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
    <!-- Revenue Chart -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xs font-semibold text-gray-700 uppercase flex items-center">
                <i class="fas fa-chart-line text-green-500 mr-1.5 text-xs"></i>
                Revenue Overview
            </h3>
            <select id="revenuePeriod" class="text-xs border-gray-300 rounded-lg px-2 py-1">
                <option value="week">Last 7 days</option>
                <option value="month" selected>This month</option>
                <option value="year">This year</option>
            </select>
        </div>
        <div class="h-64">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    <!-- Occupancy Chart -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xs font-semibold text-gray-700 uppercase flex items-center">
                <i class="fas fa-chart-pie text-blue-500 mr-1.5 text-xs"></i>
                Occupancy Distribution
            </h3>
            <select id="occupancyHostel" class="text-xs border-gray-300 rounded-lg px-2 py-1">
                <option value="all">All Hostels</option>
                @foreach($data['hostels'] ?? [] as $hostel)
                    <option value="{{ $hostel->id }}">{{ $hostel->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="h-64">
            <canvas id="occupancyChart"></canvas>
        </div>
    </div>
</div>

<!-- Charts Row 2 -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
    <!-- Booking Trends -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <h3 class="text-xs font-semibold text-gray-700 uppercase mb-4 flex items-center">
            <i class="fas fa-calendar-alt text-purple-500 mr-1.5 text-xs"></i>
            Booking Trends
        </h3>
        <div class="h-64">
            <canvas id="bookingsChart"></canvas>
        </div>
    </div>

    <!-- Payment Methods -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <h3 class="text-xs font-semibold text-gray-700 uppercase mb-4 flex items-center">
            <i class="fas fa-credit-card text-yellow-500 mr-1.5 text-xs"></i>
            Payment Methods
        </h3>
        <div class="h-64">
            <canvas id="paymentMethodsChart"></canvas>
        </div>
    </div>
</div>

<!-- Reports Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
    <!-- Occupancy Report Card -->
    <a href="{{ route('hostel-manager.reports.occupancy') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition">
        <div class="flex items-center mb-3">
            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                <i class="fas fa-bed text-blue-600 text-sm"></i>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-800">Occupancy Report</h3>
                <p class="text-xs text-gray-500">Room occupancy rates and trends</p>
            </div>
        </div>
        <div class="flex justify-between items-center">
            <span class="text-xs text-gray-600">Current rate: {{ $data['occupancy_rate'] ?? 0 }}%</span>
            <span class="text-blue-600 text-xs">View →</span>
        </div>
    </a>

    <!-- Revenue Report Card -->
    <a href="{{ route('hostel-manager.reports.revenue') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition">
        <div class="flex items-center mb-3">
            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                <i class="fas fa-money-bill-wave text-green-600 text-sm"></i>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-800">Revenue Report</h3>
                <p class="text-xs text-gray-500">Income analysis and projections</p>
            </div>
        </div>
        <div class="flex justify-between items-center">
            <span class="text-xs text-gray-600">Total: ₵{{ number_format($data['total_revenue'] ?? 0, 2) }}</span>
            <span class="text-green-600 text-xs">View →</span>
        </div>
    </a>

    <!-- Booking Report Card -->
    <a href="{{ route('hostel-manager.reports.bookings') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition">
        <div class="flex items-center mb-3">
            <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                <i class="fas fa-calendar-check text-purple-600 text-sm"></i>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-800">Booking Report</h3>
                <p class="text-xs text-gray-500">Booking patterns and statistics</p>
            </div>
        </div>
        <div class="flex justify-between items-center">
            <span class="text-xs text-gray-600">{{ $data['total_bookings'] ?? 0 }} total bookings</span>
            <span class="text-purple-600 text-xs">View →</span>
        </div>
    </a>

    <!-- Student Demographics Card -->
    <a href="{{ route('hostel-manager.reports.demographics') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition">
        <div class="flex items-center mb-3">
            <div class="w-8 h-8 bg-pink-100 rounded-lg flex items-center justify-center mr-3">
                <i class="fas fa-users text-pink-600 text-sm"></i>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-800">Student Demographics</h3>
                <p class="text-xs text-gray-500">Gender and program distribution</p>
            </div>
        </div>
        <div class="flex justify-between items-center">
            <span class="text-xs text-gray-600">{{ $data['total_occupants'] ?? 0 }} total students</span>
            <span class="text-pink-600 text-xs">View →</span>
        </div>
    </a>

    <!-- Maintenance Report Card -->
    <a href="{{ route('hostel-manager.reports.maintenance') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition">
        <div class="flex items-center mb-3">
            <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center mr-3">
                <i class="fas fa-tools text-orange-600 text-sm"></i>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-800">Maintenance Report</h3>
                <p class="text-xs text-gray-500">Maintenance requests and status</p>
            </div>
        </div>
        <div class="flex justify-between items-center">
            <span class="text-xs text-gray-600">{{ $data['maintenance_requests'] ?? 0 }} pending</span>
            <span class="text-orange-600 text-xs">View →</span>
        </div>
    </a>

    <!-- Complaints Report Card -->
    <a href="{{ route('hostel-manager.reports.complaints') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition">
        <div class="flex items-center mb-3">
            <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center mr-3">
                <i class="fas fa-exclamation-triangle text-red-600 text-sm"></i>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-800">Complaints Report</h3>
                <p class="text-xs text-gray-500">Complaint analysis and resolution</p>
            </div>
        </div>
        <div class="flex justify-between items-center">
            <span class="text-xs text-gray-600">{{ $data['complaints'] ?? 0 }} active complaints</span>
            <span class="text-red-600 text-xs">View →</span>
        </div>
    </a>
</div>

<!-- Export Section -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
    <h3 class="text-xs font-semibold text-gray-700 uppercase mb-3 flex items-center">
        <i class="fas fa-download text-gray-500 mr-1.5 text-xs"></i>
        Export Reports
    </h3>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <a href="{{ route('hostel-manager.reports.export', 'occupancy') }}"
           class="text-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
            <i class="fas fa-bed text-blue-500 text-sm mb-1"></i>
            <p class="text-xs text-gray-700">Occupancy</p>
        </a>
        <a href="{{ route('hostel-manager.reports.export', 'revenue') }}"
           class="text-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
            <i class="fas fa-money-bill-wave text-green-500 text-sm mb-1"></i>
            <p class="text-xs text-gray-700">Revenue</p>
        </a>
        <a href="{{ route('hostel-manager.reports.export', 'bookings') }}"
           class="text-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
            <i class="fas fa-calendar-check text-purple-500 text-sm mb-1"></i>
            <p class="text-xs text-gray-700">Bookings</p>
        </a>
        <a href="{{ route('hostel-manager.reports.export', 'students') }}"
           class="text-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
            <i class="fas fa-users text-pink-500 text-sm mb-1"></i>
            <p class="text-xs text-gray-700">Students</p>
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart')?.getContext('2d');
    if (revenueCtx) {
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($data['revenue_labels'] ?? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']) !!},
                datasets: [{
                    label: 'Revenue (GHS)',
                    data: {!! json_encode($data['revenue_data'] ?? [1200, 1900, 1500, 2100, 1800, 2400]) !!},
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#10b981'
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
                            callback: function(value) { return '₵' + value; },
                            font: { size: 10 }
                        }
                    },
                    x: {
                        ticks: { font: { size: 10 } }
                    }
                }
            }
        });
    }

    // Occupancy Chart
    const occupancyCtx = document.getElementById('occupancyChart')?.getContext('2d');
    if (occupancyCtx) {
        new Chart(occupancyCtx, {
            type: 'doughnut',
            data: {
                labels: ['Occupied', 'Available', 'Maintenance'],
                datasets: [{
                    data: [
                        {{ $data['occupied_rooms'] ?? 60 }},
                        {{ $data['available_rooms'] ?? 30 }},
                        {{ $data['maintenance_rooms'] ?? 10 }}
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
                        labels: { font: { size: 10 }, boxWidth: 10 }
                    }
                },
                cutout: '60%'
            }
        });
    }

    // Bookings Chart
    const bookingsCtx = document.getElementById('bookingsChart')?.getContext('2d');
    if (bookingsCtx) {
        new Chart(bookingsCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($data['booking_labels'] ?? ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']) !!},
                datasets: [
                    {
                        label: 'Check-ins',
                        data: {!! json_encode($data['checkins_data'] ?? [5, 7, 4, 8, 6, 9, 3]) !!},
                        backgroundColor: '#3b82f6',
                        borderRadius: 4
                    },
                    {
                        label: 'Check-outs',
                        data: {!! json_encode($data['checkouts_data'] ?? [4, 5, 3, 6, 5, 7, 2]) !!},
                        backgroundColor: '#ef4444',
                        borderRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { font: { size: 10 }, boxWidth: 10 }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { font: { size: 10 } }
                    },
                    x: {
                        ticks: { font: { size: 10 } }
                    }
                }
            }
        });
    }

    // Payment Methods Chart
    const paymentCtx = document.getElementById('paymentMethodsChart')?.getContext('2d');
    if (paymentCtx) {
        new Chart(paymentCtx, {
            type: 'pie',
            data: {
                labels: ['Card', 'Mobile Money', 'Bank Transfer', 'Cash'],
                datasets: [{
                    data: {!! json_encode($data['payment_methods'] ?? [45, 30, 15, 10]) !!},
                    backgroundColor: ['#3b82f6', '#10b981', '#8b5cf6', '#f59e0b'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { font: { size: 10 }, boxWidth: 10 }
                    }
                }
            }
        });
    }

    // Period selector for revenue chart
    document.getElementById('revenuePeriod')?.addEventListener('change', function() {
        // In a real app, this would fetch new data via AJAX
        showInfoMessage('Loading data for ' + this.value + '...');
    });

    // Hostel selector for occupancy chart
    document.getElementById('occupancyHostel')?.addEventListener('change', function() {
        // In a real app, this would fetch new data via AJAX
        showInfoMessage('Loading data for ' + this.options[this.selectedIndex].text + '...');
    });
});
</script>
@endpush
