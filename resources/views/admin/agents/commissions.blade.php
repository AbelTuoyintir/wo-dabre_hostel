{{-- resources/views/admin/agents/commissions.blade.php --}}
@extends('layouts.app')

@section('title', 'Agent Commissions - ' . $agent->user->name)
@section('page-title', 'Commission History')

@section('content')
<div class="space-y-6">
    <!-- Back Button and Agent Info -->
    <div class="bg-white rounded-xl shadow p-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.agents.show', $agent->id) }}" class="text-purple-600 hover:text-purple-800">
                    <i class="fas fa-arrow-left text-xl"></i>
                </a>
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">{{ $agent->user->name }}</h3>
                    <p class="text-sm text-gray-500">Agent Code: {{ $agent->agent_code }}</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-500">Total Commission</p>
                <p class="text-2xl font-bold text-purple-600">₵{{ number_format($summary['total'], 2) }}</p>
            </div>
        </div>
    </div>

    <!-- Commission Summary Cards -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-4 text-white">
            <p class="text-sm opacity-90">Booking Commission</p>
            <p class="text-2xl font-bold">₵{{ number_format($summary['booking'], 2) }}</p>
        </div>
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-4 text-white">
            <p class="text-sm opacity-90">Hostel Bonus</p>
            <p class="text-2xl font-bold">₵{{ number_format($summary['hostel'], 2) }}</p>
        </div>
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-4 text-white">
            <p class="text-sm opacity-90">Room Bonus</p>
            <p class="text-2xl font-bold">₵{{ number_format($summary['room'], 2) }}</p>
        </div>
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl p-4 text-white">
            <p class="text-sm opacity-90">Referral Bonus</p>
            <p class="text-2xl font-bold">₵{{ number_format($summary['referral'], 2) }}</p>
        </div>
        <div class="bg-gradient-to-br from-pink-500 to-pink-600 rounded-xl p-4 text-white">
            <p class="text-sm opacity-90">Available Balance</p>
            <p class="text-2xl font-bold">₵{{ number_format($agent->available_balance, 2) }}</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow p-4">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Commission Type</label>
                <select name="type" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500">
                    <option value="">All Types</option>
                    <option value="booking_commission" {{ request('type') == 'booking_commission' ? 'selected' : '' }}>Booking Commission</option>
                    <option value="hostel_added" {{ request('type') == 'hostel_added' ? 'selected' : '' }}>Hostel Bonus</option>
                    <option value="room_added" {{ request('type') == 'room_added' ? 'selected' : '' }}>Room Bonus</option>
                    <option value="signup_bonus" {{ request('type') == 'signup_bonus' ? 'selected' : '' }}>Referral Bonus</option>
                    <option value="bonus" {{ request('type') == 'bonus' ? 'selected' : '' }}>Manual Bonus</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" 
                       class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" 
                       class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500">
            </div>
            
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                    <i class="fas fa-search mr-2"></i> Filter
                </button>
                <a href="{{ route('admin.agents.commissions', $agent->id) }}" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 text-center">
                    <i class="fas fa-redo mr-2"></i> Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Commissions Table -->
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($commissions as $commission)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $commission->created_at->format('d M Y, h:i A') }}
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $typeColors = [
                                    'booking_commission' => 'bg-green-100 text-green-800',
                                    'hostel_added' => 'bg-blue-100 text-blue-800',
                                    'room_added' => 'bg-purple-100 text-purple-800',
                                    'signup_bonus' => 'bg-yellow-100 text-yellow-800',
                                    'bonus' => 'bg-pink-100 text-pink-800'
                                ];
                                $typeLabels = [
                                    'booking_commission' => 'Booking Commission',
                                    'hostel_added' => 'Hostel Bonus',
                                    'room_added' => 'Room Bonus',
                                    'signup_bonus' => 'Referral Bonus',
                                    'bonus' => 'Manual Bonus'
                                ];
                                $color = $typeColors[$commission->type] ?? 'bg-gray-100 text-gray-800';
                                $label = $typeLabels[$commission->type] ?? ucfirst(str_replace('_', ' ', $commission->type));
                            @endphp
                            <span class="px-2 py-1 text-xs rounded-full {{ $color }}">
                                {{ $label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $commission->description }}
                        </td>
                        <td class="px-6 py-4 text-sm">
                            @if($commission->hostel)
                                <a href="{{ route('admin.hostels.show', $commission->hostel_id) }}" class="text-blue-600 hover:underline">
                                    Hostel #{{ $commission->hostel_id }}
                                </a>
                            @elseif($commission->booking)
                                <span class="text-gray-600">Booking #{{ $commission->booking_id }}</span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right font-semibold text-green-600">
                            +₵{{ number_format($commission->amount, 2) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-chart-line text-4xl text-gray-300 mb-2 block"></i>
                            No commission records found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 border-t">
            {{ $commissions->appends(request()->query())->links() }}
        </div>
    </div>

    <!-- Add Bonus Button -->
    <div class="flex justify-end">
        <button onclick="addBonusCommission()" class="px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg font-semibold hover:shadow-lg transition">
            <i class="fas fa-gift mr-2"></i> Add Bonus Commission
        </button>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function addBonusCommission() {
        Swal.fire({
            title: 'Add Bonus Commission',
            html: `
                <div class="text-left">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Amount (₵)</label>
                    <input type="number" id="bonusAmount" class="swal2-input w-full mb-3" placeholder="Enter amount" step="0.01" min="1">
                    
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea id="bonusDesc" class="swal2-textarea w-full" placeholder="Reason for bonus" rows="3"></textarea>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Add Bonus',
            cancelButtonText: 'Cancel',
            preConfirm: () => {
                const amount = document.getElementById('bonusAmount').value;
                const description = document.getElementById('bonusDesc').value;
                
                if (!amount || amount <= 0) {
                    Swal.showValidationMessage('Please enter a valid amount');
                    return false;
                }
                if (!description) {
                    Swal.showValidationMessage('Please enter a description');
                    return false;
                }
                return { amount, description };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Processing...',
                    text: 'Adding bonus commission',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Submit the bonus
                fetch('{{ route("admin.agents.add-commission", $agent->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        amount: parseFloat(result.value.amount),
                        type: 'bonus',
                        description: result.value.description
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Bonus Added!',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error', data.error || 'Failed to add bonus', 'error');
                    }
                })
                .catch(error => {
                    Swal.fire('Error', 'Something went wrong', 'error');
                });
            }
        });
    }
</script>
@endpush
@endsection