{{-- resources/views/agent/dashboard.blade.php --}}
@extends('layouts.agent')

@section('title', 'Agent Dashboard - Wo-dabre')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="container mx-auto px-4 py-8">
        <!-- Welcome Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Welcome back, {{ Auth::user()->name }}!</h1>
            <p class="text-gray-600 mt-2">Agent Code: <span class="font-mono font-bold text-purple-600">{{ $agent->agent_code }}</span></p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-6 text-white shadow-lg">
                <div class="flex items-center justify-between mb-4">
                    <i class="fas fa-building text-3xl opacity-80"></i>
                    <span class="text-xs bg-white/20 px-2 py-1 rounded-full">Total</span>
                </div>
                <p class="text-3xl font-bold">{{ $stats['total_hostels'] }}</p>
                <p class="text-sm opacity-90 mt-1">Hostels Added</p>
                @if($stats['pending_hostels'] > 0)
                    <p class="text-xs mt-2 text-yellow-200">{{ $stats['pending_hostels'] }} pending approval</p>
                @endif
            </div>

            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-6 text-white shadow-lg">
                <div class="flex items-center justify-between mb-4">
                    <i class="fas fa-door-open text-3xl opacity-80"></i>
                    <span class="text-xs bg-white/20 px-2 py-1 rounded-full">Active</span>
                </div>
                <p class="text-3xl font-bold">{{ $stats['total_rooms'] }}</p>
                <p class="text-sm opacity-90 mt-1">Rooms Listed</p>
            </div>

            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-6 text-white shadow-lg">
                <div class="flex items-center justify-between mb-4">
                    <i class="fas fa-chart-line text-3xl opacity-80"></i>
                    <span class="text-xs bg-white/20 px-2 py-1 rounded-full">Lifetime</span>
                </div>
                <p class="text-3xl font-bold">₵{{ number_format($stats['total_commission'], 2) }}</p>
                <p class="text-sm opacity-90 mt-1">Total Commission Earned</p>
            </div>

            <div class="bg-gradient-to-br from-pink-500 to-rose-600 rounded-2xl p-6 text-white shadow-lg relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16"></div>
                <div class="flex items-center justify-between mb-4">
                    <i class="fas fa-wallet text-3xl opacity-80"></i>
                    <span class="text-xs bg-white/20 px-2 py-1 rounded-full">Available</span>
                </div>
                <p class="text-3xl font-bold">₵{{ number_format($stats['available_balance'], 2) }}</p>
                <p class="text-sm opacity-90 mt-1">Available Balance</p>
                @if($stats['pending_withdrawals'] > 0)
                    <p class="text-xs mt-2 text-yellow-200">₵{{ number_format($stats['pending_withdrawals']) }} pending withdrawal</p>
                @endif
                <a href="{{ route('agent.withdrawals.request') }}" 
                   class="mt-4 inline-flex items-center gap-2 bg-white/20 hover:bg-white/30 transition rounded-lg px-4 py-2 text-sm font-semibold">
                    <i class="fas fa-money-bill-wave"></i> Withdraw
                </a>
            </div>
        </div>

        <!-- Commission Chart & Recent Activity -->
        <div class="grid lg:grid-cols-2 gap-6 mb-8">
            <!-- Chart Card -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Commission Trends (Last 6 Months)</h3>
                <canvas id="commissionChart" height="250"></canvas>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Quick Actions</h3>
                <div class="grid grid-cols-2 gap-4">
                    <a href="{{ route('agent.hostels.create') }}" 
                       class="bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl p-4 text-center hover:shadow-lg transition">
                        <i class="fas fa-plus-circle text-2xl mb-2"></i>
                        <p class="font-semibold text-sm">Add New Hostel</p>
                    </a>
                    <a href="{{ route('agent.hostels.index') }}" 
                       class="bg-blue-600 text-white rounded-xl p-4 text-center hover:shadow-lg transition">
                        <i class="fas fa-building text-2xl mb-2"></i>
                        <p class="font-semibold text-sm">Manage Hostels</p>
                    </a>
                    <a href="{{ route('agent.commissions') }}" 
                       class="bg-green-600 text-white rounded-xl p-4 text-center hover:shadow-lg transition">
                        <i class="fas fa-chart-bar text-2xl mb-2"></i>
                        <p class="font-semibold text-sm">Commission History</p>
                    </a>
                    <a href="{{ route('agent.withdrawals') }}" 
                       class="bg-amber-600 text-white rounded-xl p-4 text-center hover:shadow-lg transition">
                        <i class="fas fa-history text-2xl mb-2"></i>
                        <p class="font-semibold text-sm">Withdrawal History</p>
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Commissions -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-800">Recent Commissions</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($stats['recent_commissions'] as $commission)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $commission->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs rounded-full 
                                    @if($commission->type == 'booking_commission') bg-green-100 text-green-700
                                    @elseif($commission->type == 'hostel_added') bg-blue-100 text-blue-700
                                    @elseif($commission->type == 'room_added') bg-purple-100 text-purple-700
                                    @else bg-amber-100 text-amber-700 @endif">
                                    {{ str_replace('_', ' ', ucfirst($commission->type)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $commission->description }}</td>
                            <td class="px-6 py-4 text-right font-semibold text-green-600">+₵{{ number_format($commission->amount, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500">No commissions yet</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Commission Calculation Info -->
        <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-2xl p-6">
            <div class="flex items-start gap-4">
                <i class="fas fa-info-circle text-purple-600 text-2xl"></i>
                <div>
                    <h4 class="font-bold text-gray-800 mb-2">How Commission Works</h4>
                    <div class="grid md:grid-cols-3 gap-4 text-sm text-gray-600">
                        <div>🏨 <strong>Hostel Addition</strong><br>₵100 per approved hostel</div>
                        <div>🚪 <strong>Room Addition</strong><br>₵20 per room listed</div>
                        <div>📚 <strong>Booking Commission</strong><br>10% of each confirmed booking</div>
                    </div>
                    <p class="text-xs text-gray-500 mt-3">Minimum withdrawal: ₵50 | Processing time: 2-3 business days</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('commissionChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($stats['chart_data']->pluck('month')),
            datasets: [{
                label: 'Commission Earned (₵)',
                data: @json($stats['chart_data']->pluck('commission')),
                borderColor: '#8b5cf6',
                backgroundColor: 'rgba(139, 92, 246, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>
@endpush
@endsection