@extends('layouts.hostelmanage')

@section('title', 'Complaint Details')
@section('page-title', 'Complaint #' . $complaint->id)

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-4 bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex items-center">
            <a href="{{ route('hostel-manager.complaints') }}" class="text-gray-500 hover:text-gray-700 mr-3">
                <i class="fas fa-arrow-left text-xs"></i>
            </a>
            <div>
                <h2 class="text-sm font-semibold text-gray-800">Complaint #{{ $complaint->id }}</h2>
                <p class="text-xs text-gray-500">{{ $complaint->title }}</p>
            </div>
            <div class="ml-auto flex items-center space-x-2">
                @php
                    $priorityClass = match($complaint->priority) {
                        'low' => 'bg-gray-100 text-gray-700',
                        'medium' => 'bg-blue-100 text-blue-700',
                        'high' => 'bg-orange-100 text-orange-700',
                        'urgent' => 'bg-red-100 text-red-700',
                        default => 'bg-gray-100 text-gray-700'
                    };

                    $statusClass = match($complaint->status) {
                        'pending' => 'bg-yellow-100 text-yellow-700',
                        'in_progress' => 'bg-blue-100 text-blue-700',
                        'resolved' => 'bg-green-100 text-green-700',
                        'rejected' => 'bg-red-100 text-red-700',
                        default => 'bg-gray-100 text-gray-700'
                    };
                @endphp
                <span class="text-[10px] font-medium px-2 py-1 rounded-full {{ $priorityClass }}">
                    {{ ucfirst($complaint->priority) }} Priority
                </span>
                <span class="text-[10px] font-medium px-2 py-1 rounded-full {{ $statusClass }}">
                    {{ str_replace('_', ' ', ucfirst($complaint->status)) }}
                </span>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Left Column - Complaint Details -->
        <div class="lg:col-span-2 space-y-4">
            <!-- Complaint Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
                    <h3 class="text-xs font-semibold text-gray-700 uppercase">Complaint Details</h3>
                </div>
                <div class="p-4">
                    <div class="mb-4">
                        <p class="text-[10px] text-gray-500 uppercase mb-1">Title</p>
                        <p class="text-sm font-medium text-gray-900">{{ $complaint->title }}</p>
                    </div>

                    <div class="mb-4">
                        <p class="text-[10px] text-gray-500 uppercase mb-1">Description</p>
                        <p class="text-sm text-gray-700 whitespace-pre-line">{{ $complaint->description }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase mb-1">Submitted By</p>
                            <p class="text-sm text-gray-900">{{ $complaint->user->name ?? 'Unknown' }}</p>
                            <p class="text-xs text-gray-500">{{ $complaint->user->email ?? '' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase mb-1">Submitted On</p>
                            <p class="text-sm text-gray-900">{{ $complaint->created_at->format('F d, Y \a\t h:i A') }}</p>
                            <p class="text-xs text-gray-500">{{ $complaint->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Resolution Notes -->
            @if($complaint->resolution_notes || $complaint->status == 'resolved' || $complaint->status == 'rejected')
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-4 py-3 bg-green-50 border-b border-green-100">
                    <h3 class="text-xs font-semibold text-green-700 uppercase">Resolution</h3>
                </div>
                <div class="p-4">
                    @if($complaint->resolution_notes)
                        <p class="text-sm text-gray-700 whitespace-pre-line">{{ $complaint->resolution_notes }}</p>
                    @else
                        <p class="text-sm text-gray-500 italic">No resolution notes provided.</p>
                    @endif

                    @if($complaint->resolved_at)
                        <div class="mt-3 pt-3 border-t border-gray-100">
                            <p class="text-xs text-gray-500">
                                <i class="fas fa-check-circle text-green-500 mr-1"></i>
                                Resolved on {{ $complaint->resolved_at->format('F d, Y \a\t h:i A') }}
                                @if($complaint->resolved_by)
                                    by {{ $complaint->resolved_by }}
                                @endif
                            </p>
                        </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column - Student & Location Info -->
        <div class="space-y-4">
            <!-- Student Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-4 py-3 bg-blue-50 border-b border-blue-100">
                    <h3 class="text-xs font-semibold text-blue-700 uppercase flex items-center">
                        <i class="fas fa-user mr-1.5"></i>
                        Student Information
                    </h3>
                </div>
                <div class="p-3">
                    <div class="flex items-center mb-3">
                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                            <span class="text-xs font-medium text-blue-700">{{ substr($complaint->user->name ?? 'U', 0, 1) }}</span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $complaint->user->name ?? 'Unknown' }}</p>
                            <p class="text-xs text-gray-500">{{ $complaint->user->student_id ?? 'No ID' }}</p>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <div class="flex items-center text-xs">
                            <i class="fas fa-envelope text-gray-400 w-4 mr-2"></i>
                            <span class="text-gray-600">{{ $complaint->user->email ?? 'N/A' }}</span>
                        </div>
                        <div class="flex items-center text-xs">
                            <i class="fas fa-phone text-gray-400 w-4 mr-2"></i>
                            <span class="text-gray-600">{{ $complaint->user->phone ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Location Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-4 py-3 bg-purple-50 border-b border-purple-100">
                    <h3 class="text-xs font-semibold text-purple-700 uppercase flex items-center">
                        <i class="fas fa-map-marker-alt mr-1.5"></i>
                        Location
                    </h3>
                </div>
                <div class="p-3">
                    <div class="space-y-2">
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase">Hostel</p>
                            <p class="text-sm font-medium text-gray-900">{{ $complaint->hostel->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase">Room</p>
                            <p class="text-sm text-gray-900">{{ $complaint->room->number ?? 'N/A' }}</p>
                        </div>
                        @if($complaint->room)
                        <div class="pt-2">
                            <a href="{{ route('hostel-manager.rooms.show', $complaint->room) }}"
                               class="text-xs text-blue-600 hover:text-blue-800">
                                <i class="fas fa-external-link-alt mr-1"></i> View Room Details
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
                    <h3 class="text-xs font-semibold text-gray-700 uppercase">Actions</h3>
                </div>
                <div class="p-3 space-y-2">
                    @if($complaint->status != 'resolved' && $complaint->status != 'rejected')
                    <button onclick="updateComplaintStatus({{ $complaint->id }})"
                            class="w-full text-left px-3 py-2 text-xs bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition flex items-center">
                        <i class="fas fa-edit mr-2"></i>
                        Update Status
                    </button>
                    @endif

                    <button onclick="contactStudent({{ $complaint->user->id }})"
                            class="w-full text-left px-3 py-2 text-xs bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition flex items-center">
                        <i class="fas fa-envelope mr-2"></i>
                        Contact Student
                    </button>

                    <form action="{{ route('hostel-manager.complaints.destroy', $complaint) }}" method="POST" class="inline-block w-full">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Are you sure you want to delete this complaint?')"
                                class="w-full text-left px-3 py-2 text-xs bg-red-50 text-red-700 rounded-lg hover:bg-red-100 transition flex items-center">
                            <i class="fas fa-trash-alt mr-2"></i>
                            Delete Complaint
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Update Status Modal (same as in index) -->
<div id="statusModal" class="modal">
    <!-- ... same modal content as index ... -->
</div>
@endsection

@push('scripts')
<script>
function updateComplaintStatus(complaintId) {
    document.getElementById('statusForm').action = `/hostel-manager/complaints/${complaintId}`;
    document.getElementById('statusModal').style.display = 'block';
    document.body.style.overflow = 'hidden';
}

function closeStatusModal() {
    document.getElementById('statusModal').style.display = 'none';
    document.body.style.overflow = 'auto';
}

function contactStudent(studentId) {
    // Implement contact functionality
    alert('Contact feature coming soon!');
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
