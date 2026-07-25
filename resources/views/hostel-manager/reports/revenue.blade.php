@extends('layouts.hostelmanage')

@section('title', 'Revenue Report')
@section('page-title', 'Revenue Report')

@section('content')
<!-- Header -->
<div class="mb-4 bg-white rounded-xl shadow-sm border border-gray-100 p-4">
    <div class="flex items-center">
        <a href="{{ route('hostel-manager.reports') }}" class="text-gray-500 hover:text-gray-700 mr-3">
            <i class="fas fa-arrow-left text-xs"></i>
        </a>
        <div>
            <h2 class="text-sm font-semibold text-gray-800">Revenue Report</h2>
            <p class="text-xs text-gray-500">Financial analysis and income tracking</p>
        </div>
        <div class="ml-auto flex items-center space-x-2">
            <select id="yearSelect" class="text-xs border-gray-300 rounded-lg px-3 py-1.5">
                @for($y = now()->year; $y >= now()->year - 3; $y--)
                    <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
            <a href="{{ route('hostel-manager.reports.export', 'revenue') }}?year={{ $year }}"
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
            <span class="text-xs font-medium text-gray-500 uppercase">Total Revenue</span>
            <span class="bg-green-100 text-green-600 text-xs px-2 py-1 rounded-full">{{ $year }}</span>
        </div>
        <div class="text-2xl font-bold text-gray-800">₵{{ number_format($totalRevenue, 2) }}</div>
        <div class="mt-2 text-xs text-gray-500">Year to date</div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-medium text-gray-500 uppercase">Average Monthly</span>
            <span class="bg-blue-100 text-blue-600 text-xs px-2 py-1 rounded-full">₵</span>
        </div>
        <div class="text-2xl font-bold text-gray-800">₵{{ number_format($totalRevenue / 12, 2) }}</div>
        <div class="mt-2 text-xs text-gray-500">Per month average</div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-medium text-gray-500 uppercase">Best Month</span>
            <span class="bg-purple-100 text-purple-600 text-xs px-2 py-1 rounded-full">
                @php
                    $bestMonth = collect($monthlyRevenue)->sortByDesc('revenue')->first();
                @endphp
                {{ $bestMonth['month'] ?? 'N/A' }}
            </span>
        </div>
        <div class="text-2xl font-bold text-gray-800">₵{{ number_format($bestMonth['revenue'] ?? 0, 2) }}</div>
        <div class="mt-2 text-xs text-gray-500">Highest revenue month</div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-medium text-gray-500 uppercase">Total Transactions</span>
            <span class="bg-orange-100 text-orange-600 text-xs px-2 py-1 rounded-full">Count</span>
        </div>
        <div class="text-2xl font-bold text-gray-800">{{ collect($monthlyRevenue)->sum('count') }}</div>
        <div class="mt-2 text-xs text-gray-500">Completed payments</div>
    </div>
</div>

<!-- Revenue Chart -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-xs font-semibold text-gray-700 uppercase flex items-center">
            <i class="fas fa-chart-line text-green-500 mr-1.5 text-xs"></i>
            Monthly Revenue - {{ $year }}
        </h3>
    </div>
    <div class="h-80">
        <canvas id="revenueChart"></canvas>
    </div>
</div>

<!-- Monthly Breakdown Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
    <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
        <h3 class="text-xs font-semibold text-gray-700 uppercase">Monthly Breakdown</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase">Month</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase">Revenue (GHS)</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase">Number of Payments</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase">Average per Payment</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase">Trend</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @foreach($monthlyRevenue as $index => $data)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 whitespace-nowrap">
                        <span class="text-xs font-medium text-gray-900">{{ $data['month'] }}</span>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <span class="text-xs font-bold text-green-600">₵{{ number_format($data['revenue'], 2) }}</span>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-600">{{ $data['count'] }}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-600">
                        ₵{{ $data['count'] > 0 ? number_format($data['revenue'] / $data['count'], 2) : 0 }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        @php
                            $prevRevenue = $monthlyRevenue[$index - 1]['revenue'] ?? $data['revenue'];
                            $trend = $data['revenue'] - $prevRevenue;
                            $trendClass = $trend > 0 ? 'text-green-600' : ($trend < 0 ? 'text-red-600' : 'text-gray-600');
                            $trendIcon = $trend > 0 ? '↑' : ($trend < 0 ? '↓' : '→');
                        @endphp
                        <span class="text-xs {{ $trendClass }}">
                            {{ $trendIcon }} {{ number_format(abs($trend), 2) }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-gray-50">
                <tr>
                    <td class="px-4 py-3 text-xs font-bold text-gray-900">Total</td>
                    <td class="px-4 py-3 text-xs font-bold text-green-600">₵{{ number_format($totalRevenue, 2) }}</td>
                    <td class="px-4 py-3 text-xs font-bold text-gray-900">{{ collect($monthlyRevenue)->sum('count') }}</td>
                    <td class="px-4 py-3 text-xs font-bold text-gray-900">₵{{ number_format($totalRevenue / max(collect($monthlyRevenue)->sum('count'), 1), 2) }}</td>
                    <td class="px-4 py-3"></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<!-- Quarterly Summary -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <h3 class="text-xs font-semibold text-gray-700 uppercase mb-3 flex items-center">
            <i class="fas fa-chart-pie text-purple-500 mr-1.5 text-xs"></i>
            Quarterly Distribution
        </h3>
        @php
            $quarters = [
                'Q1 (Jan-Mar)' => collect($monthlyRevenue)->slice(0, 3)->sum('revenue'),
                'Q2 (Apr-Jun)' => collect($monthlyRevenue)->slice(3, 3)->sum('revenue'),
                'Q3 (Jul-Sep)' => collect($monthlyRevenue)->slice(6, 3)->sum('revenue'),
                'Q4 (Oct-Dec)' => collect($monthlyRevenue)->slice(9, 3)->sum('revenue'),
            ];
        @endphp
        <div class="space-y-3">
            @foreach($quarters as $quarter => $amount)
            <div>
                <div class="flex items-center justify-between text-xs mb-1">
                    <span class="text-gray-600">{{ $quarter }}</span>
                    <span class="font-medium text-gray-900">₵{{ number_format($amount, 2) }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-1.5">
                    <div class="bg-blue-600 h-1.5 rounded-full" style="width: {{ $totalRevenue > 0 ? ($amount / $totalRevenue) * 100 : 0 }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <h3 class="text-xs font-semibold text-gray-700 uppercase mb-3 flex items-center">
            <i class="fas fa-lightbulb text-yellow-500 mr-1.5 text-xs"></i>
            Insights & Recommendations
        </h3>
        <div class="space-y-3 text-xs">
            @php
                $bestMonth = collect($monthlyRevenue)->sortByDesc('revenue')->first();
                $worstMonth = collect($monthlyRevenue)->sortBy('revenue')->first();
                $avgRevenue = $totalRevenue / 12;
            @endphp
            <div class="flex items-start">
                <i class="fas fa-check-circle text-green-500 mt-0.5 mr-2 text-xs"></i>
                <span class="text-gray-600">Best performing month: <span class="font-medium">{{ $bestMonth['month'] ?? 'N/A' }}</span> with ₵{{ number_format($bestMonth['revenue'] ?? 0, 2) }}</span>
            </div>
            <div class="flex items-start">
                <i class="fas fa-exclamation-triangle text-yellow-500 mt-0.5 mr-2 text-xs"></i>
                <span class="text-gray-600">Lowest revenue in <span class="font-medium">{{ $worstMonth['month'] ?? 'N/A' }}</span> with ₵{{ number_format($worstMonth['revenue'] ?? 0, 2) }}</span>
            </div>
            <div class="flex items-start">
                <i class="fas fa-chart-line text-blue-500 mt-0.5 mr-2 text-xs"></i>
                <span class="text-gray-600">Average monthly revenue: <span class="font-medium">₵{{ number_format($avgRevenue, 2) }}</span></span>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Revenue Chart
    const ctx = document.getElementById('revenueChart')?.getContext('2d');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode(collect($monthlyRevenue)->pluck('month')->toArray()) !!},
                datasets: [{
                    label: 'Revenue (GHS)',
                    data: {!! json_encode(collect($monthlyRevenue)->pluck('revenue')->toArray()) !!},
                    backgroundColor: '#10b981',
                    borderRadius: 4,
                    barPercentage: 0.6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
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
                        },
                        grid: {
                            color: '#f0f0f0'
                        }
                    },
                    x: {
                        ticks: {
                            font: { size: 10 },
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

    // Year selector
    document.getElementById('yearSelect')?.addEventListener('change', function() {
        window.location.href = '{{ route("hostel-manager.reports.revenue") }}?year=' + this.value;
    });
});
</script>
@endpush
