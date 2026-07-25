{{-- resources/views/admin/agents/withdrawals.blade.php --}}
@extends('layouts.app')

@section('title', 'Agent Withdrawals - ' . $agent->user->name)
@section('page-title', 'Withdrawal History')

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
                <p class="text-sm text-gray-500">Total Withdrawn</p>
                <p class="text-2xl font-bold text-orange-600">₵{{ number_format($agent->withdrawn_amount, 2) }}</p>
            </div>
        </div>
    </div>

    <!-- Withdrawals Table -->
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Request Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payment Method</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Account Details</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Processed Date</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($withdrawals as $withdrawal)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $withdrawal->created_at->format('d M Y, h:i A') }}
                        </td>
                        <td class="px-6 py-4 font-semibold text-gray-900">
                            ₵{{ number_format($withdrawal->amount, 2) }}
                        </td>
                        <td class="px-6 py-4 text-sm">
                            {{ ucfirst(str_replace('_', ' ', $withdrawal->payment_method)) }}
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <div>
                                <p><strong>Account:</strong> {{ $withdrawal->account_number }}</p>
                                <p><strong>Name:</strong> {{ $withdrawal->account_name }}</p>
                                @if($withdrawal->bank_name)
                                    <p><strong>Bank:</strong> {{ $withdrawal->bank_name }}</p>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($withdrawal->status == 'completed')
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle text-green-500 text-xs mr-1"></i> Completed
                                </span>
                            @elseif($withdrawal->status == 'pending')
                                <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-clock text-yellow-500 text-xs mr-1"></i> Pending
                                </span>
                            @elseif($withdrawal->status == 'processing')
                                <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                    <i class="fas fa-spinner fa-spin text-blue-500 text-xs mr-1"></i> Processing
                                </span>
                            @elseif($withdrawal->status == 'rejected')
                                <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">
                                    <i class="fas fa-times-circle text-red-500 text-xs mr-1"></i> Rejected
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $withdrawal->processed_at ? $withdrawal->processed_at->format('d M Y') : '-' }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            @if($withdrawal->status == 'pending')
                                <div class="flex justify-end gap-2">
                                    <button onclick="processWithdrawal({{ $withdrawal->id }}, 'approve')" 
                                            class="px-3 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700">
                                        <i class="fas fa-check mr-1"></i> Approve
                                    </button>
                                    <button onclick="processWithdrawal({{ $withdrawal->id }}, 'reject')" 
                                            class="px-3 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700">
                                        <i class="fas fa-times mr-1"></i> Reject
                                    </button>
                                </div>
                            @elseif($withdrawal->status == 'rejected' && $withdrawal->rejection_reason)
                                <button onclick="showRejectionReason('{{ addslashes($withdrawal->rejection_reason) }}')" 
                                        class="text-red-600 hover:text-red-800 text-sm">
                                    <i class="fas fa-info-circle mr-1"></i> View Reason
                                </button>
                            @else
                                <span class="text-gray-400 text-sm">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-money-bill-wave text-4xl text-gray-300 mb-2 block"></i>
                            No withdrawal requests found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 border-t">
            {{ $withdrawals->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function processWithdrawal(withdrawalId, action) {
        const isApprove = action === 'approve';
        
        Swal.fire({
            title: isApprove ? 'Approve Withdrawal?' : 'Reject Withdrawal?',
            html: `
                <div class="text-left">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes (Optional)</label>
                    <textarea id="notes" class="swal2-textarea" placeholder="${isApprove ? 'Add processing notes...' : 'Reason for rejection...'}" rows="3"></textarea>
                </div>
            `,
            icon: isApprove ? 'question' : 'warning',
            showCancelButton: true,
            confirmButtonText: isApprove ? 'Yes, approve' : 'Yes, reject',
            cancelButtonText: 'Cancel',
            confirmButtonColor: isApprove ? '#10b981' : '#ef4444',
            preConfirm: () => {
                const notes = document.getElementById('notes').value;
                return { notes };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Processing...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                fetch(`/admin/withdrawals/${withdrawalId}/process`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        action: action,
                        notes: result.value.notes
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error', data.error, 'error');
                    }
                })
                .catch(error => {
                    Swal.fire('Error', 'Something went wrong', 'error');
                });
            }
        });
    }
    
    function showRejectionReason(reason) {
        Swal.fire({
            title: 'Rejection Reason',
            text: reason,
            icon: 'info',
            confirmButtonColor: '#6b7280'
        });
    }
</script>
@endpush
@endsection