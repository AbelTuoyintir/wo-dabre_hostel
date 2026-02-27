@extends('layouts.student')

@section('title', 'My Complaints')
@section('page-title', 'Complaints Management')

@section('content')
<!-- Header with Actions -->
<div class="bg-white rounded-lg shadow-sm p-6 mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">My Complaints</h1>
            <p class="text-gray-600 mt-1">Submit and track your complaints</p>
        </div>
        <div class="mt-4 md:mt-0">
            <button onclick="openComplaintModal()"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-plus mr-2"></i>New Complaint
            </button>
        </div>
    </div>

    <!-- Complaint Stats -->
    @php
        $pendingCount = $complaints->where('status', 'pending')->count();
        $inProgressCount = $complaints->where('status', 'in_progress')->count();
        $resolvedCount = $complaints->where('status', 'resolved')->count();
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-6 pt-6 border-t">
        <div class="bg-yellow-50 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-yellow-600 font-medium">Pending</p>
                    <p class="text-2xl font-bold text-yellow-700">{{ $pendingCount }}</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-full">
                    <i class="fas fa-clock text-yellow-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-blue-50 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-blue-600 font-medium">In Progress</p>
                    <p class="text-2xl font-bold text-blue-700">{{ $inProgressCount }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="fas fa-spinner text-blue-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-green-50 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-green-600 font-medium">Resolved</p>
                    <p class="text-2xl font-bold text-green-700">{{ $resolvedCount }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-lg shadow-sm p-6 mb-6">
    <form method="GET" action="{{ route('student.complaints') }}" class="flex flex-wrap items-center gap-4">
        <div class="flex-1 min-w-[200px]">
            <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
            </select>
        </div>
        <div class="flex-1 min-w-[200px]">
            <select name="category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="">All Categories</option>
                <option value="maintenance" {{ request('category') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                <option value="payment" {{ request('category') == 'payment' ? 'selected' : '' }}>Payment</option>
                <option value="behavior" {{ request('category') == 'behavior' ? 'selected' : '' }}>Behavior</option>
                <option value="other" {{ request('category') == 'other' ? 'selected' : '' }}>Other</option>
            </select>
        </div>
        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            Apply Filters
        </button>
        <a href="{{ route('student.complaints') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
            Clear
        </a>
    </form>
</div>

<!-- Complaints List -->
@if($complaints->count() > 0)
    <div class="space-y-4">
        @foreach($complaints as $complaint)
            <div class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition">
                <div class="flex flex-col md:flex-row md:items-start md:justify-between">
                    <!-- Complaint Info -->
                    <div class="flex-1">
                        <div class="flex items-start space-x-3">
                            <!-- Status Icon -->
                            <div class="flex-shrink-0">
                                @php
                                    $statusIcons = [
                                        'pending' => ['bg-yellow-100', 'fa-clock', 'text-yellow-600'],
                                        'in_progress' => ['bg-blue-100', 'fa-spinner', 'text-blue-600'],
                                        'resolved' => ['bg-green-100', 'fa-check-circle', 'text-green-600'],
                                    ];
                                    $icon = $statusIcons[$complaint->status] ?? ['bg-gray-100', 'fa-circle', 'text-gray-600'];
                                @endphp
                                <div class="w-10 h-10 {{ $icon[0] }} rounded-full flex items-center justify-center">
                                    <i class="fas {{ $icon[1] }} {{ $icon[2] }}"></i>
                                </div>
                            </div>

                            <!-- Details -->
                            <div class="flex-1">
                                <div class="flex flex-wrap items-center gap-3 mb-2">
                                    <h3 class="text-lg font-semibold text-gray-800">
                                        {{ $complaint->subject ?? $complaint->title }}
                                    </h3>
                                    @php
                                        $statusColors = [
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'in_progress' => 'bg-blue-100 text-blue-800',
                                            'resolved' => 'bg-green-100 text-green-800',
                                        ];
                                        $statusColor = $statusColors[$complaint->status] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="px-3 py-1 text-xs rounded-full {{ $statusColor }}">
                                        {{ str_replace('_', ' ', ucfirst($complaint->status)) }}
                                    </span>
                                    @if($complaint->priority)
                                        @php
                                            $priorityColors = [
                                                'low' => 'bg-gray-100 text-gray-600',
                                                'medium' => 'bg-blue-100 text-blue-600',
                                                'high' => 'bg-orange-100 text-orange-600',
                                                'urgent' => 'bg-red-100 text-red-600',
                                            ];
                                            $priorityColor = $priorityColors[$complaint->priority] ?? 'bg-gray-100 text-gray-600';
                                        @endphp
                                        <span class="px-3 py-1 text-xs rounded-full {{ $priorityColor }}">
                                            {{ ucfirst($complaint->priority) }} Priority
                                        </span>
                                    @endif
                                </div>

                                <p class="text-gray-600 mb-3">{{ $complaint->description }}</p>

                                <div class="flex flex-wrap items-center gap-4 text-xs text-gray-500">
                                    <span>
                                        <i class="far fa-calendar mr-1"></i>
                                        Submitted: {{ $complaint->created_at->format('M d, Y h:i A') }}
                                    </span>
                                    @if($complaint->category)
                                        <span>
                                            <i class="far fa-folder mr-1"></i>
                                            Category: {{ ucfirst($complaint->category) }}
                                        </span>
                                    @endif
                                    @if($complaint->booking && $complaint->booking->room)
                                        <span>
                                            <i class="fas fa-door-open mr-1"></i>
                                            Room: {{ $complaint->booking->room->number }}
                                        </span>
                                    @endif
                                </div>

                                @if($complaint->resolution_notes)
                                    <div class="mt-3 p-3 bg-green-50 rounded-lg">
                                        <p class="text-sm text-green-700">
                                            <i class="fas fa-check-circle mr-2"></i>
                                            <strong>Resolution:</strong> {{ $complaint->resolution_notes }}
                                        </p>
                                        @if($complaint->resolved_at)
                                            <p class="text-xs text-green-600 mt-1">
                                                Resolved on {{ $complaint->resolved_at->format('M d, Y h:i A') }}
                                            </p>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center space-x-2 mt-4 md:mt-0 md:ml-4">
                        <button onclick="viewComplaint({{ $complaint->id }})"
                                class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-eye mr-1"></i>View
                        </button>
                        @if($complaint->status == 'pending')
                            <button onclick="editComplaint({{ $complaint->id }})"
                                    class="px-4 py-2 bg-yellow-600 text-white text-sm rounded-lg hover:bg-yellow-700 transition">
                                <i class="fas fa-edit mr-1"></i>Edit
                            </button>
                            <button onclick="deleteComplaint({{ $complaint->id }})"
                                    class="px-4 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition">
                                <i class="fas fa-trash mr-1"></i>Delete
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        {{ $complaints->withQueryString()->links() }}
    </div>
@else
    <!-- Empty State -->
    <div class="bg-white rounded-lg shadow-sm p-12 text-center">
        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-check-circle text-gray-400 text-4xl"></i>
        </div>
        <h3 class="text-xl font-semibold text-gray-800 mb-2">No Complaints</h3>
        <p class="text-gray-500 mb-6">You haven't submitted any complaints yet.</p>
        <button onclick="openComplaintModal()"
                class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-plus mr-2"></i>Submit Your First Complaint
        </button>
    </div>
@endif

<!-- Create/Edit Complaint Modal -->
<div id="complaintModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white px-6 py-4 border-b flex justify-between items-center">
            <h3 class="text-xl font-bold text-gray-800" id="modalTitle">Submit New Complaint</h3>
            <button onclick="closeComplaintModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="complaintForm" action="{{ route('student.complaints.store') }}" method="POST" class="p-6">
            @csrf
            <input type="hidden" name="complaint_id" id="complaintId">

            <div class="space-y-4">
                <!-- Subject -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Subject <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="subject" id="subject"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="Brief summary of your complaint"
                           required>
                </div>

                <!-- Category -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Category <span class="text-red-500">*</span>
                    </label>
                    <select name="category" id="category"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                            required>
                        <option value="">Select Category</option>
                        <option value="maintenance">Maintenance (Plumbing, Electricity, etc.)</option>
                        <option value="payment">Payment Issue</option>
                        <option value="behavior">Staff/Student Behavior</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <!-- Priority -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Priority
                    </label>
                    <select name="priority" id="priority"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="low">Low</option>
                        <option value="medium" selected>Medium</option>
                        <option value="high">High</option>
                        <option value="urgent">Urgent</option>
                    </select>
                </div>

                <!-- Related Booking (Optional) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Related Booking (Optional)
                    </label>
                    <select name="booking_id" id="booking_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Select a booking (optional)</option>
                        @foreach(auth()->user()->bookings()->where('status', 'confirmed')->get() as $booking)
                            <option value="{{ $booking->id }}">
                                {{ $booking->room->hostel->name ?? 'Hostel' }} - Room {{ $booking->room->number ?? 'N/A' }}
                                ({{ $booking->check_in->format('M d') }} - {{ $booking->check_out->format('M d') }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Description <span class="text-red-500">*</span>
                    </label>
                    <textarea name="description" id="description" rows="5"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                              placeholder="Please provide detailed information about your complaint..."
                              required></textarea>
                    <p class="text-xs text-gray-500 mt-1">Minimum 20 characters</p>
                </div>

                <!-- Attachments (Optional) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Attachments (Optional)
                    </label>
                    <input type="file" name="attachments[]" multiple
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           accept="image/*,.pdf">
                    <p class="text-xs text-gray-500 mt-1">Upload images or PDF files (max 5MB each)</p>
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeComplaintModal()"
                            class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" id="submitBtn"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Submit Complaint
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openComplaintModal() {
    document.getElementById('modalTitle').textContent = 'Submit New Complaint';
    document.getElementById('complaintForm').reset();
    document.getElementById('complaintId').value = '';
    document.getElementById('submitBtn').textContent = 'Submit Complaint';
    document.getElementById('complaintForm').action = "{{ route('student.complaints.store') }}";
    document.getElementById('complaintModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeComplaintModal() {
    document.getElementById('complaintModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function viewComplaint(id) {
    // In a real app, you would redirect to a show page or open a view modal
    window.location.href = `/student/complaints/${id}`;
}

function editComplaint(id) {
    // Fetch complaint data and populate modal
    fetch(`/student/complaints/${id}/edit`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('modalTitle').textContent = 'Edit Complaint';
            document.getElementById('subject').value = data.subject || data.title;
            document.getElementById('category').value = data.category;
            document.getElementById('priority').value = data.priority;
            document.getElementById('booking_id').value = data.booking_id;
            document.getElementById('description').value = data.description;
            document.getElementById('complaintId').value = data.id;
            document.getElementById('submitBtn').textContent = 'Update Complaint';
            document.getElementById('complaintForm').action = `/student/complaints/${data.id}`;

            // Add method spoofing for PUT
            let methodInput = document.getElementById('method_field');
            if (!methodInput) {
                methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.id = 'method_field';
                document.getElementById('complaintForm').appendChild(methodInput);
            }
            methodInput.value = 'PUT';

            openComplaintModal();
        });
}

function deleteComplaint(id) {
    Swal.fire({
        title: 'Delete Complaint?',
        text: 'Are you sure you want to delete this complaint? This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, delete it',
        cancelButtonText: 'No, keep it'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/student/complaints/${id}`;
            form.innerHTML = '@csrf @method("DELETE")';
            document.body.appendChild(form);
            form.submit();
        }
    });
}

// Character counter for description
document.getElementById('description')?.addEventListener('input', function() {
    const minLength = 20;
    const currentLength = this.value.length;

    if (currentLength < minLength) {
        this.classList.add('border-red-500');
        if (!this.nextElementSibling?.classList.contains('char-counter')) {
            const warning = document.createElement('p');
            warning.className = 'text-xs text-red-500 mt-1 char-counter';
            warning.textContent = `${minLength - currentLength} more characters needed`;
            this.parentNode.insertBefore(warning, this.nextSibling);
        } else {
            this.nextElementSibling.textContent = `${minLength - currentLength} more characters needed`;
        }
    } else {
        this.classList.remove('border-red-500');
        if (this.nextElementSibling?.classList.contains('char-counter')) {
            this.nextElementSibling.remove();
        }
    }
});

// Close modal when clicking outside
document.getElementById('complaintModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeComplaintModal();
    }
});
</script>
@endpush
@endsection
