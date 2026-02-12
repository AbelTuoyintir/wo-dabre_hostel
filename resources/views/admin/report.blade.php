@extends('layouts.app')

@section('title', 'Reports')
@section('page-title', 'Reports & Analytics')

@section('content')
<div class="reports-dashboard">
    <!-- Header Section -->
    <div class="reports-header mb-4">
        <div class="header-content">
            <div class="title-section">
                <h2 class="page-title">
                    <i class="fas fa-chart-line me-2"></i>Reports & Analytics
                </h2>
                <p class="page-subtitle">Insights and performance metrics for your hostel management system</p>
            </div>
            <div class="date-range-picker">
                <button class="btn-date-range" onclick="refreshData()">
                    <i class="fas fa-sync-alt me-2"></i>Refresh Data
                </button>
                <span class="last-updated">Last updated: {{ now()->format('M d, Y h:i A') }}</span>
            </div>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="kpi-grid mb-4">
        <div class="kpi-card revenue">
            <div class="kpi-icon">
                <i class="fas fa-sack-dollar"></i>
            </div>
            <div class="kpi-content">
                <span class="kpi-label">Total Revenue</span>
                <span class="kpi-value">¢{{ number_format($revenueByMonth->sum('revenue'), 0) }}</span>
                <span class="kpi-trend positive">
                    <i class="fas fa-arrow-trend-up"></i>
                    <span>+12% vs last year</span>
                </span>
            </div>
        </div>

        <div class="kpi-card bookings">
            <div class="kpi-icon">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="kpi-content">
                <span class="kpi-label">Total Bookings</span>
                <span class="kpi-value">{{ number_format($bookingsByHostel->sum('bookings_count')) }}</span>
                <span class="kpi-trend positive">
                    <i class="fas fa-arrow-trend-up"></i>
                    <span>+8% this month</span>
                </span>
            </div>
        </div>

        <div class="kpi-card users">
            <div class="kpi-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="kpi-content">
                <span class="kpi-label">New Users (30d)</span>
                <span class="kpi-value">{{ number_format($userRegistrations->sum('count')) }}</span>
                <span class="kpi-trend negative">
                    <i class="fas fa-arrow-trend-down"></i>
                    <span>-3% vs last month</span>
                </span>
            </div>
        </div>

        <div class="kpi-card occupancy">
            <div class="kpi-icon">
                <i class="fas fa-bed"></i>
            </div>
            <div class="kpi-content">
                <span class="kpi-label">Avg. Occupancy</span>
                <span class="kpi-value">78%</span>
                <span class="kpi-trend positive">
                    <i class="fas fa-arrow-trend-up"></i>
                    <span>+5% this week</span>
                </span>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="charts-row mb-4">
        <!-- Revenue Chart -->
        <div class="chart-card large">
            <div class="chart-header">
                <div class="chart-title-group">
                    <h5 class="chart-title">Revenue Trends</h5>
                    <p class="chart-subtitle">Monthly revenue performance (Last 12 months)</p>
                </div>
                <div class="chart-actions">
                    <button class="btn-chart-action" onclick="downloadChart('revenue')" title="Download Chart">
                        <i class="fas fa-download"></i>
                    </button>
                </div>
            </div>
            <div class="chart-body">
                <canvas id="revenueChart" height="300"></canvas>
            </div>
            <div class="chart-footer">
                <div class="revenue-stats">
                    <div class="stat-item">
                        <span class="stat-label">Highest Month</span>
                        <span class="stat-value">
                            @php
                                $highestRevenue = $revenueByMonth->sortByDesc('revenue')->first();
                            @endphp
                            @if($highestRevenue)
                                {{ \Carbon\Carbon::create()->month($highestRevenue->month)->format('F') }}
                                <small class="text-muted">(¢{{ number_format($highestRevenue->revenue, 0) }})</small>
                            @else
                                -
                            @endif
                        </span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Average Monthly</span>
                        <span class="stat-value">
                            ¢{{ number_format($revenueByMonth->avg('revenue'), 0) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Secondary Charts Row -->
    <div class="charts-row secondary mb-4">
        <!-- Hostels Bar Chart -->
        <div class="chart-card">
            <div class="chart-header">
                <div class="chart-title-group">
                    <h5 class="chart-title">Top Hostels</h5>
                    <p class="chart-subtitle">By number of bookings</p>
                </div>
            </div>
            <div class="chart-body">
                <canvas id="hostelsChart" height="250"></canvas>
            </div>
        </div>

        <!-- User Registrations Line Chart -->
        <div class="chart-card">
            <div class="chart-header">
                <div class="chart-title-group">
                    <h5 class="chart-title">User Growth</h5>
                    <p class="chart-subtitle">Daily registrations (Last 30 days)</p>
                </div>
            </div>
            <div class="chart-body">
                <canvas id="usersChart" height="250"></canvas>
            </div>
        </div>
    </div>

    <!-- Detailed Tables Row -->
    <div class="tables-row">
        <!-- Revenue Table -->
        <div class="table-card">
            <div class="table-header">
                <h5 class="table-title">
                    <i class="fas fa-table me-2"></i>Monthly Revenue Details
                </h5>
                <button class="btn-export" onclick="exportTable('revenue')">
                    <i class="fas fa-file-csv me-2"></i>Export CSV
                </button>
            </div>
            <div class="table-container">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>Period</th>
                            <th class="text-end">Revenue</th>
                            <th class="text-end">Growth</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $revenueData = $revenueByMonth->sortByDesc(function($item) {
                                return $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT);
                            })->values();
                        @endphp
                        @forelse ($revenueData as $index => $row)
                            @php
                                $prevRevenue = $revenueData[$index + 1]->revenue ?? $row->revenue;
                                $growth = $prevRevenue > 0 ? (($row->revenue - $prevRevenue) / $prevRevenue) * 100 : 0;
                            @endphp
                            <tr>
                                <td>
                                    <div class="period-cell">
                                        <span class="month-badge">{{ \Carbon\Carbon::create()->month($row->month)->format('M') }}</span>
                                        <span class="year-text">{{ $row->year }}</span>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <span class="amount-cell">¢{{ number_format($row->revenue, 2) }}</span>
                                </td>
                                <td class="text-end">
                                    @if($growth > 0)
                                        <span class="growth-badge positive">
                                            <i class="fas fa-arrow-up me-1"></i>{{ number_format(abs($growth), 1) }}%
                                        </span>
                                    @elseif($growth < 0)
                                        <span class="growth-badge negative">
                                            <i class="fas fa-arrow-down me-1"></i>{{ number_format(abs($growth), 1) }}%
                                        </span>
                                    @else
                                        <span class="growth-badge neutral">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($row->revenue > ($revenueByMonth->avg('revenue') * 1.2))
                                        <span class="status-pill excellent">Excellent</span>
                                    @elseif($row->revenue > $revenueByMonth->avg('revenue'))
                                        <span class="status-pill good">Good</span>
                                    @else
                                        <span class="status-pill average">Average</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="empty-cell">
                                    <div class="empty-state-small">
                                        <i class="fas fa-inbox"></i>
                                        <span>No revenue data available</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Hostels Table -->
        <div class="table-card">
            <div class="table-header">
                <h5 class="table-title">
                    <i class="fas fa-building me-2"></i>Hostel Performance
                </h5>
                <button class="btn-export" onclick="exportTable('hostels')">
                    <i class="fas fa-file-csv me-2"></i>Export CSV
                </button>
            </div>
            <div class="table-container">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>Hostel</th>
                            <th class="text-end">Bookings</th>
                            <th>Market Share</th>
                            <th>Trend</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalBookings = $bookingsByHostel->sum('bookings_count');
                        @endphp
                        @forelse ($bookingsByHostel as $index => $hostel)
                            <tr>
                                <td>
                                    <div class="hostel-rank">
                                        <span class="rank-number {{ $index < 3 ? 'top' : '' }}">{{ $index + 1 }}</span>
                                        <span class="hostel-name-cell">{{ $hostel->name }}</span>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <span class="bookings-count">{{ number_format($hostel->bookings_count) }}</span>
                                </td>
                                <td>
                                    @php
                                        $share = $totalBookings > 0 ? ($hostel->bookings_count / $totalBookings) * 100 : 0;
                                    @endphp
                                    <div class="market-share">
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width: {{ $share }}%"></div>
                                        </div>
                                        <span class="share-text">{{ number_format($share, 1) }}%</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="sparkline" id="sparkline-{{ $hostel->id }}">
                                        @for($i = 0; $i < 7; $i++)
                                            @php
                                                $height = rand(30, 100);
                                                $color = $height > 70 ? 'var(--success)' : ($height > 40 ? 'var(--warning)' : 'var(--danger)');
                                            @endphp
                                            <div class="spark-bar" style="height: {{ $height }}%; background: {{ $color }}"></div>
                                        @endfor
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="empty-cell">
                                    <div class="empty-state-small">
                                        <i class="fas fa-inbox"></i>
                                        <span>No hostel data available</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
:root {
    --primary: #4f46e5;
    --primary-soft: rgba(79, 70, 229, 0.1);
    --success: #10b981;
    --success-soft: rgba(16, 185, 129, 0.1);
    --warning: #f59e0b;
    --warning-soft: rgba(245, 158, 11, 0.1);
    --danger: #ef4444;
    --danger-soft: rgba(239, 68, 68, 0.1);
    --info: #3b82f6;
    --gray-50: #f9fafb;
    --gray-100: #f3f4f6;
    --gray-200: #e5e7eb;
    --gray-300: #d1d5db;
    --gray-400: #9ca3af;
    --gray-500: #6b7280;
    --gray-600: #4b5563;
    --gray-700: #374151;
    --gray-800: #1f2937;
    --gray-900: #111827;
    --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    --radius: 0.5rem;
    --radius-lg: 0.75rem;
    --radius-xl: 1rem;
}

.reports-dashboard {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 1rem;
}

/* Header */
.reports-header {
    background: linear-gradient(135deg, var(--gray-900) 0%, var(--gray-800) 100%);
    border-radius: var(--radius-xl);
    padding: 2rem;
    color: white;
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    flex-wrap: wrap;
    gap: 1.5rem;
}

.page-title {
    font-size: 1.875rem;
    font-weight: 700;
    margin: 0 0 0.5rem 0;
    display: flex;
    align-items: center;
}

.page-subtitle {
    opacity: 0.8;
    margin: 0;
    font-size: 1rem;
}

.date-range-picker {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 0.5rem;
}

.btn-date-range {
    display: inline-flex;
    align-items: center;
    padding: 0.625rem 1.25rem;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: var(--radius);
    color: white;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-date-range:hover {
    background: rgba(255, 255, 255, 0.2);
}

.last-updated {
    font-size: 0.75rem;
    opacity: 0.6;
}

/* KPI Grid */
.kpi-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 1.5rem;
}

.kpi-card {
    background: white;
    border-radius: var(--radius-lg);
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    box-shadow: var(--shadow);
    border: 1px solid var(--gray-100);
    transition: transform 0.2s, box-shadow 0.2s;
}

.kpi-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.kpi-icon {
    width: 56px;
    height: 56px;
    border-radius: var(--radius);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.kpi-card.revenue .kpi-icon {
    background: var(--success-soft);
    color: var(--success);
}

.kpi-card.bookings .kpi-icon {
    background: var(--primary-soft);
    color: var(--primary);
}

.kpi-card.users .kpi-icon {
    background: var(--info-soft, rgba(59, 130, 246, 0.1));
    color: var(--info);
}

.kpi-card.occupancy .kpi-icon {
    background: var(--warning-soft);
    color: var(--warning);
}

.kpi-content {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.kpi-label {
    font-size: 0.875rem;
    color: var(--gray-500);
    font-weight: 500;
}

.kpi-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--gray-900);
}

.kpi-trend {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    font-size: 0.75rem;
    font-weight: 600;
}

.kpi-trend.positive {
    color: var(--success);
}

.kpi-trend.negative {
    color: var(--danger);
}

/* Charts */
.charts-row {
    display: grid;
    gap: 1.5rem;
}

.charts-row.secondary {
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
}

.chart-card {
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow);
    border: 1px solid var(--gray-100);
    overflow: hidden;
}

.chart-card.large {
    grid-column: 1 / -1;
}

.chart-header {
    padding: 1.5rem;
    border-bottom: 1px solid var(--gray-100);
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}

.chart-title {
    font-size: 1.125rem;
    font-weight: 700;
    color: var(--gray-900);
    margin: 0 0 0.25rem 0;
}

.chart-subtitle {
    font-size: 0.875rem;
    color: var(--gray-500);
    margin: 0;
}

.chart-actions {
    display: flex;
    gap: 0.5rem;
}

.btn-chart-action {
    width: 36px;
    height: 36px;
    border: 1px solid var(--gray-200);
    background: white;
    border-radius: var(--radius);
    color: var(--gray-600);
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-chart-action:hover {
    background: var(--gray-50);
    border-color: var(--gray-300);
    color: var(--gray-900);
}

.chart-body {
    padding: 1.5rem;
    position: relative;
}

.chart-footer {
    padding: 1rem 1.5rem;
    background: var(--gray-50);
    border-top: 1px solid var(--gray-100);
}

.revenue-stats {
    display: flex;
    gap: 2rem;
}

.stat-item {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.stat-label {
    font-size: 0.75rem;
    color: var(--gray-500);
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.stat-value {
    font-size: 0.9375rem;
    font-weight: 600;
    color: var(--gray-900);
}

/* Tables Row */
.tables-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(450px, 1fr));
    gap: 1.5rem;
}

.table-card {
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow);
    border: 1px solid var(--gray-100);
    overflow: hidden;
}

.table-header {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid var(--gray-100);
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: var(--gray-50);
}

.table-title {
    font-size: 1rem;
    font-weight: 700;
    color: var(--gray-900);
    margin: 0;
    display: flex;
    align-items: center;
}

.btn-export {
    display: inline-flex;
    align-items: center;
    padding: 0.5rem 1rem;
    background: white;
    border: 1px solid var(--gray-200);
    border-radius: var(--radius);
    color: var(--gray-600);
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-export:hover {
    border-color: var(--primary);
    color: var(--primary);
    background: var(--primary-soft);
}

.table-container {
    overflow-x: auto;
}

.modern-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.875rem;
}

.modern-table thead th {
    padding: 1rem 1.25rem;
    text-align: left;
    font-weight: 600;
    color: var(--gray-600);
    border-bottom: 1px solid var(--gray-200);
    white-space: nowrap;
}

.modern-table tbody tr {
    transition: background-color 0.2s;
}

.modern-table tbody tr:hover {
    background-color: var(--gray-50);
}

.modern-table tbody td {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid var(--gray-100);
    vertical-align: middle;
}

/* Table Cells */
.period-cell {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.month-badge {
    width: 40px;
    height: 40px;
    background: var(--primary-soft);
    color: var(--primary);
    border-radius: var(--radius);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.875rem;
}

.year-text {
    color: var(--gray-700);
    font-weight: 500;
}

.amount-cell {
    font-family: 'Courier New', monospace;
    font-weight: 700;
    color: var(--gray-900);
}

.growth-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 600;
}

.growth-badge.positive {
    background: var(--success-soft);
    color: var(--success);
}

.growth-badge.negative {
    background: var(--danger-soft);
    color: var(--danger);
}

.growth-badge.neutral {
    background: var(--gray-100);
    color: var(--gray-500);
}

.status-pill {
    display: inline-flex;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 600;
}

.status-pill.excellent {
    background: var(--success-soft);
    color: var(--success);
}

.status-pill.good {
    background: var(--info-soft, rgba(59, 130, 246, 0.1));
    color: var(--info);
}

.status-pill.average {
    background: var(--warning-soft);
    color: var(--warning);
}

.hostel-rank {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.rank-number {
    width: 28px;
    height: 28px;
    background: var(--gray-100);
    color: var(--gray-600);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.75rem;
}

.rank-number.top {
    background: var(--warning-soft);
    color: var(--warning);
}

.hostel-name-cell {
    font-weight: 600;
    color: var(--gray-900);
}

.bookings-count {
    font-weight: 700;
    color: var(--gray-900);
    font-size: 1rem;
}

.market-share {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.progress-bar {
    flex: 1;
    height: 6px;
    background: var(--gray-100);
    border-radius: 3px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--primary), #7c3aed);
    border-radius: 3px;
    transition: width 0.6s ease;
}

.share-text {
    font-size: 0.75rem;
    color: var(--gray-500);
    font-weight: 600;
    min-width: 45px;
}

.sparkline {
    display: flex;
    align-items: flex-end;
    gap: 2px;
    height: 30px;
    width: 60px;
}

.spark-bar {
    flex: 1;
    border-radius: 1px;
    opacity: 0.8;
}

.empty-cell {
    padding: 3rem 1rem !important;
}

.empty-state-small {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    color: var(--gray-400);
}

.empty-state-small i {
    font-size: 1.5rem;
}

/* Responsive */
@media (max-width: 768px) {
    .reports-header {
        padding: 1.5rem;
    }

    .header-content {
        flex-direction: column;
        align-items: flex-start;
    }

    .date-range-picker {
        align-items: flex-start;
        width: 100%;
    }

    .charts-row.secondary {
        grid-template-columns: 1fr;
    }

    .tables-row {
        grid-template-columns: 1fr;
    }

    .revenue-stats {
        flex-direction: column;
        gap: 1rem;
    }
}
</style>

<script>
// Revenue Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
const revenueData = @json($revenueByMonth->sortBy(['year', 'month'])->values());
const revenueLabels = revenueData.map(item => {
    const date = new Date(item.year, item.month - 1);
    return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
});
const revenueValues = revenueData.map(item => item.revenue);

new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: revenueLabels,
        datasets: [{
            label: 'Revenue (¢)',
            data: revenueValues,
            borderColor: '#4f46e5',
            backgroundColor: 'rgba(79, 70, 229, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#4f46e5',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 5,
            pointHoverRadius: 7
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
                backgroundColor: '#1f2937',
                padding: 12,
                cornerRadius: 8,
                callbacks: {
                    label: function(context) {
                        return '¢' + context.parsed.y.toLocaleString();
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)',
                    drawBorder: false
                },
                ticks: {
                    callback: function(value) {
                        return '¢' + (value / 1000) + 'k';
                    },
                    font: {
                        size: 11
                    }
                }
            },
            x: {
                grid: {
                    display: false
                },
                ticks: {
                    font: {
                        size: 11
                    }
                }
            }
        }
    }
});

// Hostels Chart
const hostelsCtx = document.getElementById('hostelsChart').getContext('2d');
const hostelsData = @json($bookingsByHostel->take(10));

new Chart(hostelsCtx, {
    type: 'bar',
    data: {
        labels: hostelsData.map(item => item.name.length > 15 ? item.name.substring(0, 15) + '...' : item.name),
        datasets: [{
            label: 'Bookings',
            data: hostelsData.map(item => item.bookings_count),
            backgroundColor: [
                '#4f46e5', '#7c3aed', '#ec4899', '#f59e0b', '#10b981',
                '#3b82f6', '#8b5cf6', '#f43f5e', '#eab308', '#6b7280'
            ],
            borderRadius: 6,
            borderSkipped: false
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
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)',
                    drawBorder: false
                }
            },
            x: {
                grid: {
                    display: false
                },
                ticks: {
                    font: {
                        size: 10
                    },
                    maxRotation: 45
                }
            }
        }
    }
});

// Users Chart
const usersCtx = document.getElementById('usersChart').getContext('2d');
const usersData = @json($userRegistrations);

new Chart(usersCtx, {
    type: 'line',
    data: {
        labels: usersData.map(item => {
            const date = new Date(item.date);
            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
        }),
        datasets: [{
            label: 'Registrations',
            data: usersData.map(item => item.count),
            borderColor: '#10b981',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            borderWidth: 2,
            fill: true,
            tension: 0.4,
            pointRadius: 0,
            pointHoverRadius: 6
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
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)',
                    drawBorder: false
                }
            },
            x: {
                grid: {
                    display: false
                },
                ticks: {
                    maxTicksLimit: 6,
                    font: {
                        size: 10
                    }
                }
            }
        },
        interaction: {
            intersect: false,
            mode: 'index'
        }
    }
});

// Utility functions
function refreshData() {
    location.reload();
}

function downloadChart(chartName) {
    // Implement chart download functionality
    console.log('Downloading chart:', chartName);
}

function exportTable(tableName) {
    // Implement CSV export functionality
    console.log('Exporting table:', tableName);
    alert('Export functionality would download ' + tableName + ' data as CSV');
}
</script>
@endsection
