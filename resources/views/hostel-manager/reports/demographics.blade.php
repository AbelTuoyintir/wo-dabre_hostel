@extends('layouts.hostelmanage')

@section('title', 'Student Demographics')
@section('page-title', 'Student Demographics')

@section('content')
<!-- Header -->
<div class="mb-4 bg-white rounded-xl shadow-sm border border-gray-100 p-4">
    <div class="flex items-center">
        <a href="{{ route('hostel-manager.reports') }}" class="text-gray-500 hover:text-gray-700 mr-3">
            <i class="fas fa-arrow-left text-xs"></i>
        </a>
        <div>
            <h2 class="text-sm font-semibold text-gray-800">Student Demographics</h2>
            <p class="text-xs text-gray-500">Student population analysis</p>
        </div>
        <div class="ml-auto">
            <a href="{{ route('hostel-manager.reports.export', 'students') }}"
               class="bg-blue-500 hover:bg-blue-600 text-white text-xs px-3 py-1.5 rounded-lg transition flex items-center">
                <i class="fas fa-download mr-1"></i> Export Data
            </a>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-medium text-gray-500 uppercase">Total Students</span>
            <span class="bg-blue-100 text-blue-600 text-xs px-2 py-1 rounded-full">{{ $maleStudents + $femaleStudents }}</span>
        </div>
        <div class="text-2xl font-bold text-gray-800">{{ $maleStudents + $femaleStudents }}</div>
        <div class="mt-2 text-xs text-gray-500">Currently enrolled</div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-medium text-gray-500 uppercase">Male</span>
            <span class="bg-blue-100 text-blue-600 text-xs px-2 py-1 rounded-full">{{ $maleStudents }}</span>
        </div>
        <div class="text-2xl font-bold text-blue-600">{{ $maleStudents }}</div>
        <div class="mt-2 text-xs text-gray-500">{{ $maleStudents + $femaleStudents > 0 ? round(($maleStudents / ($maleStudents + $femaleStudents)) * 100, 1) : 0 }}% of total</div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-medium text-gray-500 uppercase">Female</span>
            <span class="bg-pink-100 text-pink-600 text-xs px-2 py-1 rounded-full">{{ $femaleStudents }}</span>
        </div>
        <div class="text-2xl font-bold text-pink-600">{{ $femaleStudents }}</div>
        <div class="mt-2 text-xs text-gray-500">{{ $maleStudents + $femaleStudents > 0 ? round(($femaleStudents / ($maleStudents + $femaleStudents)) * 100, 1) : 0 }}% of total</div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-medium text-gray-500 uppercase">Gender Ratio</span>
            <span class="bg-purple-100 text-purple-600 text-xs px-2 py-1 rounded-full">
                {{ $femaleStudents > 0 ? round($maleStudents / $femaleStudents, 2) : '∞' }}:1
            </span>
        </div>
        <div class="text-2xl font-bold text-purple-600">
            {{ $femaleStudents > 0 ? round($maleStudents / $femaleStudents, 2) : '∞' }}:1
        </div>
        <div class="mt-2 text-xs text-gray-500">Male to Female</div>
    </div>
</div>

<!-- Charts Row -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
    <!-- Gender Distribution -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <h3 class="text-xs font-semibold text-gray-700 uppercase mb-4 flex items-center">
            <i class="fas fa-venn-diagram text-purple-500 mr-1.5 text-xs"></i>
            Gender Distribution
        </h3>
        <div class="h-64">
            <canvas id="genderChart"></canvas>
        </div>
    </div>

<!-- Additional demographics can be added here -->
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gender Chart
    const genderCtx = document.getElementById('genderChart')?.getContext('2d');
    if (genderCtx) {
        new Chart(genderCtx, {
            type: 'doughnut',
            data: {
                labels: ['Male', 'Female'],
                datasets: [{
                    data: [{{ $maleStudents }}, {{ $femaleStudents }}],
                    backgroundColor: ['#3b82f6', '#ec4899'],
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

    // Year chart placeholder for future implementation
});
</script>
@endpush
