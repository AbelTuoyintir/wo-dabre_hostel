@extends('layouts.hostelmanage')

@section('title', 'Complaints Management')
@section('page-title', 'Complaints Management')

@section('content')
<!-- Header with Actions -->
<div class="mb-4 bg-white rounded-xl shadow-sm border border-gray-100 p-4">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="flex items-center">
            <div class="bg-red-50 p-2 rounded-lg mr-3">
                <i class="fas fa-exclamation-triangle text-red-500 text-sm"></i>
            </div>
            <div>
                <h2 class="text-sm font-semibold text-gray-800">Complaints Management</h2>
                <p class="text-xs text-gray-500">Manage and resolve student complaints</p>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mt-4 pt-3 border-t border-gray-100">
        <div class="text-center">
            <span class="text-xs text-gray-500 block">Total</span>
            <span class="text-sm font-bold text-gray-800">{{ $stats['total'] ?? 0 }}</span>
        </div>
        <div class="text-center">
            <span class="text-xs text-gray-500 block">Pending</span>
            <span class="text-sm font-bold text-yellow-600">{{ $stats['pending'] ?? 0 }}</span>
        </div>
        <div class="text-center">
            <span class="text-xs text-gray-500 block">In Progress</span>
            <span class="text-sm font-bold text-blue-600">{{ $stats['in_progress'] ?? 0 }}</span>
        </div>
        <div class="text-center">
            <span class="text-xs text-gray-500 block">Resolved</span>
            <span class="text-sm font-bold text-green-600">{{ $stats['resolved'] ?? 0 }}</span>
        </div>
        <div class="text-center">
            <span class="text-xs text-gray-500 block">Urgent</span>
            <span class="text-sm font-bold text-red-600">{{ $stats['urgent'] ?? 0 }}</span>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="mb-4 bg-white rounded-xl shadow-sm border border-gray-100 p-4">
    <form method="GET" action="{{ route('hostel-manager.complaints') }}" class="flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-[10px] font-medium text-gray-500 uppercase mb-1">Search</label>
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search by title, description or student name..."
                       class="w-full pl-8 pr-3 py-1.5 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>

        <div class="w-32">
            <label class="block text-[10px] font-medium text-gray-500 uppercase mb-1">Status</label>
            <select name="status" class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                <option value="">All</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
            </select>
        </div>

        <div class="w-32">
            <label class="block text-[10px] font-medium text-gray-500 uppercase mb-1">Priority</label>
            <select name="priority" class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                <option value="">All</option>
                <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
            </select>
        </div>

        @if(isset($hostels) && $hostels->count() > 1)
        <div class="w-40">
            <label class="block text-[10px] font-medium text-gray-500 uppercase mb-1">Hostel</label>
            <select name="hostel_id" class="w-full px-2 py-1.5 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                <option value="">All Hostels</option>
                @foreach($hostels as $hostel)
                    <option value="{{ $hostel->id }}" {{ request('hostel_id') == $hostel->id ? 'selected' : '' }}>
                        {{ $hostel->name }}
                    </option>
                @endforeach
            </select>
        </div>
        @endif

        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white text-xs px-4 py-1.5 rounded-lg transition">
            <i class="fas fa-filter mr-1"></i> Filter
        </button>

        <a href="{{ route('hostel-manager.complaints') }}" class="border border-gray-300 hover:bg-gray-50 text-gray-700 text-xs px-4 py-1.5 rounded-lg transition">
            <i class="fas fa-times mr-1"></i> Clear
        </a>
    </form>
</div>

<!-- Complaints Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase tracking-wider">Title</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase tracking-wider">Student</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase tracking-wider">Hostel/Room</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase tracking-wider">Submitted</th>
                    <th class="px-4 py-2 text-left text-[10px] font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($complaints as $complaint)
                <tr class="hover:bg-gray-50 {{ $complaint->priority == 'urgent' && $complaint->status != 'resolved' ? 'bg-red-50' : '' }}">
                    <td class="px-4 py-2 whitespace-nowrap">
                        <span class="text-xs font-medium text-gray-900">#{{ $complaint->id }}</span>
                    </td>
                    <td class="px-4 py-2">
                        <div class="text-xs font-medium text-gray-900">{{ Str::limit($complaint->title, 30) }}</div>
                        <div class="text-[10px] text-gray-500">{{ Str::limit($complaint->description, 40) }}</div>
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center mr-2">
                                <span class="text-[10px] font-medium text-blue-700">{{ substr($complaint->user->name ?? 'U', 0, 1) }}</span>
                            </div>
                            <div>
                                <span class="text-xs text-gray-900">{{ $complaint->user->name ?? 'Unknown' }}</span>
                                <div class="text-[10px] text-gray-500">{{ $complaint->user->student_id ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap">
                        <div class="text-xs text-gray-900">{{ $complaint->hostel->name ?? 'N/A' }}</div>
                        <div class="text-[10px] text-gray-500">Room {{ $complaint->room->number ?? 'N/A' }}</div>
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap">
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
                    <td class="px-4 py-2 whitespace-nowrap">
                        @php
                            $statusClass = match($complaint->status) {
                                'pending' => 'bg-yellow-100 text-yellow-700',
                                'in_progress' => 'bg-blue-100 text-blue-700',
                                'resolved' => 'bg-green-100 text-green-700',
                                'rejected' => 'bg-red-100 text-red-700',
                                default => 'bg-gray-100 text-gray-700'
                            };
                        @endphp
                        <span class="text-[10px] font-medium px-2 py-0.5 rounded-full {{ $statusClass }}">
                            {{ str_replace('_', ' ', ucfirst($complaint->status)) }}
                        </span>
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap">
                        <span class="text-xs text-gray-600">{{ $complaint->created_at->format('M d, Y') }}</span>
                        <div class="text-[10px] text-gray-400">{{ $complaint->created_at->diffForHumans() }}</div>
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap">
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('hostel-manager.complaints.show', $complaint) }}"
                               class="text-blue-600 hover:text-blue-800" title="View Details">
                                <i class="fas fa-eye text-xs"></i>
                            </a>
                            @if($complaint->status != 'resolved')
                            <button onclick="updateComplaintStatus({{ $complaint->id }})"
                                    class="text-green-600 hover:text-green-800" title="Update Status">
                                <i class="fas fa-edit text-xs"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-4 py-8 text-center">
                        <div class="flex flex-col items-center">
                            <i class="fas fa-exclamation-circle text-gray-300 text-2xl mb-2"></i>
                            <p class="text-xs text-gray-500 mb-3">No complaints found</p>
                            @if(request('search') || request('status') || request('priority'))
                                <a href="{{ route('hostel-manager.complaints') }}" class="text-blue-500 hover:text-blue-700 text-xs">
                                    <i class="fas fa-times mr-1"></i> Clear filters
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if(isset($complaints) && $complaints->hasPages())
    <div class="px-4 py-3 border-t border-gray-100">
        {{ $complaints->links() }}
    </div>
    @endif
</div>

<!-- Update Status Modal -->
<div id="statusModal" class="modal">
    <div class="modal-content container mx-auto px-4 py-16 max-w-md">
        <div class="bg-white rounded-xl shadow-2xl overflow-hidden">
            <div class="px-4 py-3 bg-gradient-to-r from-blue-500 to-purple-600">
                <h3 class="text-sm font-semibold text-white flex items-center">
                    <i class="fas fa-edit mr-2 text-xs"></i>
                    Update Complaint Status
                </h3>
                <button onclick="closeStatusModal()" class="absolute top-3 right-3 text-white hover:text-gray-200">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            <form id="statusForm" method="POST" class="p-4">
                @csrf
                @method('PATCH')

                <div class="mb-3">
                    <label class="block text-[10px] font-medium text-gray-500 uppercase mb-1">Status</label>
                    <select name="status" id="complaintStatus" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="pending">Pending</option>
                        <option value="in_progress">In Progress</option>
                        <option value="resolved">Resolved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="block text-[10px] font-medium text-gray-500 uppercase mb-1">Resolution Notes</label>
                    <textarea name="resolution_notes" rows="3" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Add notes about how this complaint was resolved..."></textarea>
                </div>

                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeStatusModal()"
                            class="px-3 py-1.5 text-xs border border-gray-300 rounded-lg hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-3 py-1.5 text-xs bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                        Update Status
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentComplaintId = null;

function updateComplaintStatus(complaintId) {
    currentComplaintId = complaintId;
    document.getElementById('statusForm').action = `/hostel-manager/complaints/${complaintId}`;
    document.getElementById('statusModal').style.display = 'block';
    document.body.style.overflow = 'hidden';
}

function closeStatusModal() {
    document.getElementById('statusModal').style.display = 'none';
    document.body.style.overflow = 'auto';
    currentComplaintId = null;
}

// Close modal when clicking outside
window.addEventListener('click', function(event) {
    const modal = document.getElementById('statusModal');
    if (event.target === modal) {
        closeStatusModal();
    }
});
</script>
@endpush
