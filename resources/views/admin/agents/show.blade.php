{{-- resources/views/admin/agents/show.blade.php --}}
@extends('layouts.admin')

@section('title', 'Agent Details - ' . $agent->user->name)
@section('page-title', 'Agent Details')

@section('content')
<div class="space-y-6">
    <!-- Profile Header -->
    <div class="bg-gradient-to-r from-purple-600 to-pink-600 rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-8 text-white">
            <div class="flex items-center gap-6">
                <div class="w-24 h-24 bg-white/20 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-user-tie text-5xl"></i>
                </div>
                <div>
                    <h2 class="text-3xl font-bold">{{ $agent->user->name }}</h2>
                    <p class="text-purple-100 mt-1">Agent Code: <code class="bg-white/20 px-2 py-1 rounded">{{ $agent->agent_code }}</code></p>
                    <div class="flex gap-4 mt-2">
                        <span class="flex items-center gap-1"><i class="fas fa-envelope"></i> {{ $agent->user->email }}</span>
                        <span class="flex items-center gap-1"><i class="fas fa-phone"></i> {{ $agent->phone }}</span>
                    </div>
                </div>
                <div class="ml-auto">
                    @if($agent->status == 'active')
                        <span class="px-4 py-2 bg-green-500 rounded-full text-sm font-semibold">Active</span>
                    @elseif($agent->status == 'pending')
                        <span class="px-4 py-2 bg-yellow-500 rounded-full text-sm font-semibold">Pending Approval</span>
                    @elseif($agent->status == 'suspended')
                        <span class="px-4 py-2 bg-red-500 rounded-full text-sm font-semibold">Suspended</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Commission</p>
                    <p class="text-2xl font-bold text-purple-600">₵{{ number_format($performance['total_commission'], 2) }}</p>
                </div>
                <i class="fas fa-chart-line text-3xl text-purple-300"></i>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Available Balance</p>
                    <p class="text-2xl font-bold text-green-600">₵{{ number_format($performance['available_balance'], 2) }}</p>
                </div>
                <i class="fas fa-wallet text-3xl text-green-300"></i>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Withdrawn Amount</p>
                    <p class="text-2xl font-bold text-blue-600">₵{{ number_format($performance['withdrawn_amount'], 2) }}</p>
                </div>
                <i class="fas fa-money-bill-wave text-3xl text-blue-300"></i>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Hostels/Rooms</p>
                    <p class="text-2xl font-bold text-orange-600">{{ $performance['total_hostels'] }} / {{ $performance['total_rooms'] }}</p>
                </div>
                <i class="fas fa-building text-3xl text-orange-300"></i>
            </div>
        </div>
    </div>

    <!-- Tabs for Commission and Withdrawal History -->
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="border-b border-gray-200">
            <div class="flex">
                <button class="tab-btn active px-6 py-3 text-sm font-semibold text-purple-600 border-b-2 border-purple-600" data-tab="commissions">
                    <i class="fas fa-chart-bar mr-2"></i> Commission History
                </button>
                <button class="tab-btn px-6 py-3 text-sm font-semibold text-gray-600" data-tab="withdrawals">
                    <i class="fas fa-history mr-2"></i> Withdrawal History
                </button>
                <button class="tab-btn px-6 py-3 text-sm font-semibold text-gray-600" data-tab="performance">
                    <i class="fas fa-trophy mr-2"></i> Performance
                </button>
            </div>
        </div>
        
        <!-- Commissions Tab -->
        <div id="tab-commissions" class="tab-content p-6">
            <!-- Commission Summary -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                @foreach($performance['commission_by_type'] as $commission)
                <div class="bg-gray-50 rounded-lg p-3 text-center">
                    <p class="text-xs text-gray-500 uppercase">{{ str_replace('_', ' ', $commission->type) }}</p>
                    <p class="text-lg font-bold text-purple-600">₵{{ number_format($commission->total, 2) }}</p>
                </div>
                @endforeach
            </div>
            
            <!-- Commissions Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Type</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Description</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($performance['recent_commissions'] as $commission)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm">{{ $commission->created_at->format('d M Y') }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-700">
                                    {{ str_replace('_', ' ', ucfirst($commission->type)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm">{{ $commission->description }}</td>
                            <td class="px-4 py-3 text-right font-semibold text-green-600">+₵{{ number_format($commission->amount, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-500">No commission records</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Withdrawals Tab -->
        <div id="tab-withdrawals" class="tab-content hidden p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Amount</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Method</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Account</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($performance['recent_withdrawals'] as $withdrawal)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm">{{ $withdrawal->created_at->format('d M Y') }}</td>
                            <td class="px-4 py-3 font-semibold">₵{{ number_format($withdrawal->amount, 2) }}</td>
                            <td class="px-4 py-3 text-sm">{{ ucfirst(str_replace('_', ' ', $withdrawal->payment_method)) }}</td>
                            <td class="px-4 py-3">
                                @if($withdrawal->status == 'completed')
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">Completed</span>
                                @elseif($withdrawal->status == 'pending')
                                    <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700">Pending</span>
                                @elseif($withdrawal->status == 'rejected')
                                    <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-700">Rejected</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm">{{ $withdrawal->account_number }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">No withdrawal requests</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Performance Tab -->
        <div id="tab-performance" class="tab-content hidden p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Monthly Commission Chart -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="font-semibold text-gray-800 mb-3">Monthly Commission Trend</h4>
                    <canvas id="commissionChart" height="250"></canvas>
                </div>
                
                <!-- Quick Actions -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="font-semibold text-gray-800 mb-3">Quick Actions</h4>
                    <div class="space-y-3">
                        <button onclick="addBonus()" class="w-full bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700">
                            <i class="fas fa-gift mr-2"></i> Add Bonus Commission
                        </button>
                        <button onclick="viewAllCommissions()" class="w-full border border-purple-600 text-purple-600 px-4 py-2 rounded-lg hover:bg-purple-50">
                            <i class="fas fa-list mr-2"></i> View All Commissions
                        </button>
                        <button onclick="viewAllWithdrawals()" class="w-full border border-purple-600 text-purple-600 px-4 py-2 rounded-lg hover:bg-purple-50">
                            <i class="fas fa-history mr-2"></i> View All Withdrawals
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Action Buttons -->
    <div class="flex justify-end gap-3">
        <a href="{{ route('admin.agents.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
            <i class="fas fa-arrow-left mr-2"></i> Back to List
        </a>
        @if($agent->status == 'pending')
            <button onclick="approveAgent({{ $agent->id }})" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                <i class="fas fa-check mr-2"></i> Approve Application
            </button>
        @endif
        @if($agent->status == 'active')
            <button onclick="suspendAgent({{ $agent->id }})" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                <i class="fas fa-pause mr-2"></i> Suspend Agent
            </button>
        @endif
        @if($agent->status == 'suspended')
            <button onclick="activateAgent({{ $agent->id }})" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                <i class="fas fa-play mr-2"></i> Activate Agent
            </button>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Tab switching
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.tab-btn').forEach(b => {
                b.classList.remove('active', 'text-purple-600', 'border-purple-600');
                b.classList.add('text-gray-600', 'border-transparent');
            });
            this.classList.add('active', 'text-purple-600', 'border-purple-600');
            
            const tabId = this.getAttribute('data-tab');
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });
            document.getElementById(`tab-${tabId}`).classList.remove('hidden');
        });
    });
    
    // Commission Chart
    const monthlyData = @json($performance['monthly_commission']);
    const ctx = document.getElementById('commissionChart')?.getContext('2d');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: monthlyData.map(d => d.month),
                datasets: [{
                    label: 'Commission (₵)',
                    data: monthlyData.map(d => d.total),
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
                    legend: { position: 'bottom' }
                }
            }
        });
    }
    
    function approveAgent(id) {
        Swal.fire({
            title: 'Approve Agent?',
            text: 'This agent will be able to access the portal.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            confirmButtonText: 'Yes, approve'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/admin/agents/${id}/approve`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                }).then(() => location.reload());
            }
        });
    }
    
    function suspendAgent(id) {
        Swal.fire({
            title: 'Suspend Agent?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Yes, suspend'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/admin/agents/${id}/suspend`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                }).then(() => location.reload());
            }
        });
    }
    
    function activateAgent(id) {
        Swal.fire({
            title: 'Activate Agent?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            confirmButtonText: 'Yes, activate'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/admin/agents/${id}/activate`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                }).then(() => location.reload());
            }
        });
    }
    
    function addBonus() {
        Swal.fire({
            title: 'Add Bonus Commission',
            html: `
                <input type="number" id="bonusAmount" class="swal2-input" placeholder="Amount (₵)" step="0.01">
                <input type="text" id="bonusDesc" class="swal2-input" placeholder="Description">
            `,
            showCancelButton: true,
            confirmButtonText: 'Add Bonus',
            preConfirm: () => {
                const amount = document.getElementById('bonusAmount').value;
                const description = document.getElementById('bonusDesc').value;
                if (!amount || !description) {
                    Swal.showValidationMessage('Please fill all fields');
                }
                return { amount, description };
            }
        }).then((result) => {
            if (result.value) {
                fetch(`/admin/agents/{{ $agent->id }}/add-commission`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        amount: result.value.amount,
                        type: 'bonus',
                        description: result.value.description
                    })
                }).then(() => location.reload());
            }
        });
    }
    
    function viewAllCommissions() {
        window.location.href = `{{ route('admin.agents.commissions', $agent->id) }}`;
    }
    
    function viewAllWithdrawals() {
        window.location.href = `{{ route('admin.agents.withdrawals', $agent->id) }}`;
    }
</script>
@endpush
@endsection