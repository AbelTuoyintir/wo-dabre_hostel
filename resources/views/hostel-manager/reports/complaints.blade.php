@extends('layouts.hostelmanage')

@section('title', 'Complaints Report')
@section('page-title', 'Complaints Report')

@section('content')
<!-- Header -->
<div class="mb-4 bg-white rounded-xl shadow-sm border border-gray-100 p-4">
    <div class="flex items-center">
        <a href="{{ route('hostel-manager.reports') }}" class="text-gray-500 hover:text-gray-700 mr-3">
            <i class="fas fa-arrow-left text-xs"></i>
        </a>
        <div>
            <h2 class="text-sm font-semibold text-gray-800">Complaints Report</h2>
            <p class="text-xs text-gray-500">Complaint analysis and resolution tracking</p>
        </div>
        <div class="ml-auto">
            <a href="{{ route('hostel-manager.reports.export', 'complaints') }}"
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
            <span class="text-xs font-medium text-gray-500 uppercase">Total Complaints</span>
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
            <span class="text-xs font-medium text-gray-500 uppercase">Resolved</span>
            <span class="bg-green-100 text-green-600 text-xs px-2 py-1 rounded-full">{{ $stats['resolved'] }}</span>
        </div>
        <div class="text-2xl font-bold text-green-600">{{ $stats['resolved'] }}</div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-medium text-gray-500 uppercase">Urgent</span>
            <span class="bg-red-100 text-red-600 text-xs px-2 py-1 rounded-full">{{ $stats['urgent'] }}</span>
        </div>
        <div class="text-2xl font-bold text-red-600">{{ $stats['urgent'] }}</div>
    </div>
</div>

<!-- Charts Row -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
    <!-- Status Distribution -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <h3 class="text-xs font-semibold text-gray-700 uppercase mb-4">Status Distribution</h3>
        <div class="h-64">
            <canvas id="statusChart"></canvas>
        </div>
    </div>

    <!-- Priority Distribution -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <h3 class="text-xs font-semibold text-gray-700 uppercase mb-4">Priority Distribution</h3>
        <div class="h-64">
            <canvas id="priorityChart"></canvas>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="mb-4 bg-white rounded-xl shadow-sm border border-gray-100 p-4">
    <form method="GET" action="{{ route('hostel-manager.reports.complaints') }}" class="flex flex-wrap items-center gap-3">
        <div class="w-40">
            <select name="status" class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-lg">
                <option value="all" {{ $status == 'all' ? 'selected' : '' }}>All Status</option>
                <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="in_progress" {{ $status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="resolved" {{ $status == 'resolved' ? 'selected' : '' }}>Resolved</option>
            </select>
        </div>
        <div class="w-40">
            <select name="priority" class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-lg">
                <option value="all" {{ $priority == 'all' ? 'selected' : '' }}>All Priority</option>
                <option value="low" {{ $priority == 'low' ? 'selected' : '' }}>Low</option>
                <option value="medium" {{ $priority == 'medium' ? 'selected' : '' }}>Medium</option>
                <option value="high" {{ $priority == 'high' ? 'selected' : '' }}>High</option>
                <option value="urgent" {{ $priority == 'urgent' ? 'selected' : '' }}>Urgent</option>
            </select>
        </div>
        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white text-xs px-3 py-1.5 rounded-lg">Filter</button>
    </form>
</div>

<!-- Complaints Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase">ID</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase">Title</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase">Student</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase">Location</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase">Priority</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase">Date</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @foreach($complaints as $complaint)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-900">#{{ $complaint->id }}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-900">{{ Str::limit($complaint->title, 30) }}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-600">{{ $complaint->user->name ?? 'N/A' }}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-600">
                        {{ $complaint->hostel->name ?? 'N/A' }} - Rm {{ $complaint->room->number ?? 'N/A' }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        @php
                            $priorityClass = match($complaint->priority) {
                                'low' => 'bg-gray-100 text-gray-700',
                                'medium' => 'bg-blue-100 text-blue-700',
                                'high' => 'bg-orange-100 text-orange-700',
                                'urgent' => 'bg-red-100 text-red-700',
                                default => 'bg-gray-100 text-gray-700'
                            };
                        @endphp
                        <span class="text-[10px] font-medium px-2 py-0.5 rounded-full {{ $priorityClass }}">
                            {{ ucfirst($complaint->priority) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        @php
                            $statusClass = match($complaint->status) {
                                'pending' => 'bg-yellow-100 text-yellow-700',
                                'in_progress' => 'bg-blue-100 text-blue-700',
                                'resolved' => 'bg-green-100 text-green-700',
                                default => 'bg-gray-100 text-gray-700'
                            };
                        @endphp
                        <span class="text-[10px] font-medium px-2 py-0.5 rounded-full {{ $statusClass }}">
                            {{ str_replace('_', ' ', ucfirst($complaint->status)) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-600">{{ $complaint->created_at->format('M d, Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($complaints->hasPages())
    <div class="px-4 py-3 border-t border-gray-100">
        {{ $complaints->links() }}
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Status Chart
    const statusCtx = document.getElementById('statusChart')?.getContext('2d');
    if (statusCtx) {
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'In Progress', 'Resolved'],
                datasets: [{
                    data: [{{ $stats['pending'] }}, {{ $stats['in_progress'] }}, {{ $stats['resolved'] }}],
                    backgroundColor: ['#f59e0b', '#3b82f6', '#10b981'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { font: { size: 11 } }
                    }
                },
                cutout: '60%'
            }
        });
    }

    // Priority Chart
    const priorityCtx = document.getElementById('priorityChart')?.getContext('2d');
    if (priorityCtx) {
        new Chart(priorityCtx, {
            type: 'bar',
            data: {
                labels: ['Low', 'Medium', 'High', 'Urgent'],
                datasets: [{
                    label: 'Number of Complaints',
                    data: [
                        {{ $priorityDistribution['low'] }},
                        {{ $priorityDistribution['medium'] }},
                        {{ $priorityDistribution['high'] }},
                        {{ $priorityDistribution['urgent'] }}
                    ],
                    backgroundColor: ['#6b7280', '#3b82f6', '#f97316', '#ef4444'],
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
                        ticks: {
                            stepSize: 1,
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
});
</script>
@endpush
