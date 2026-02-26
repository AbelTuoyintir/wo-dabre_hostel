@extends('layouts.student')

@section('title', 'My Reviews')
@section('content')

<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">My Reviews</h1>
                <p class="text-gray-600 mt-1">Share your experience and help other students</p>
            </div>

            @if($reviewableBookings->count() > 0)
                <div class="mt-4 md:mt-0">
                    <button onclick="openReviewSelector()"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-star mr-2"></i>
                        Write a Review
                    </button>
                </div>
            @endif
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-6 pt-6 border-t">
            <div class="text-center">
                <span class="text-3xl font-bold text-blue-600">{{ $reviews->total() }}</span>
                <p class="text-sm text-gray-500">Total Reviews</p>
            </div>
            <div class="text-center">
                <span class="text-3xl font-bold text-green-600">
                    @php
                        $avgRating = $reviews->avg('rating');
                    @endphp
                    {{ $avgRating ? number_format($avgRating, 1) : '0.0' }}
                </span>
                <p class="text-sm text-gray-500">Average Rating</p>
            </div>
            <div class="text-center">
                <span class="text-3xl font-bold text-purple-600">{{ $reviewableBookings->count() }}</span>
                <p class="text-sm text-gray-500">Pending Reviews</p>
            </div>
        </div>
    </div>

    <!-- Review Selector Modal (for choosing which hostel to review) -->
    @if($reviewableBookings->count() > 0)
    <div id="reviewSelectorModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full p-6 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4 sticky top-0 bg-white pt-2">
                <h3 class="text-xl font-bold text-gray-800">Select a Hostel to Review</h3>
                <button onclick="closeReviewSelector()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <p class="text-sm text-gray-600 mb-4">
                You can only review hostels where you have completed your stay. Select a completed booking below:
            </p>

            <div class="space-y-4">
                @foreach($reviewableBookings as $booking)
                    <div class="border rounded-lg p-4 hover:shadow-md transition">
                        <div class="flex items-start space-x-4">
                            @if($booking->room->hostel->primaryImage)
                                <img src="{{ Storage::url($booking->room->hostel->primaryImage->path) }}"
                                     alt="{{ $booking->room->hostel->name }}"
                                     class="w-20 h-20 object-cover rounded-lg">
                            @else
                                <div class="w-20 h-20 bg-gray-200 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-building text-gray-400 text-2xl"></i>
                                </div>
                            @endif

                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-800">{{ $booking->room->hostel->name }}</h4>
                                <p class="text-sm text-gray-500">Room {{ $booking->room->number }}</p>
                                <p class="text-sm text-gray-500">
                                    Stayed: {{ $booking->check_in->format('M d, Y') }} - {{ $booking->check_out->format('M d, Y') }}
                                </p>
                            </div>

                            <a href="{{ route('student.reviews.create', ['booking_id' => $booking->id]) }}"
                               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                Write Review
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Reviews List -->
    @if($reviews->count() > 0)
        <div class="space-y-6">
            @foreach($reviews as $review)
                <div class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition">
                    <div class="flex flex-col md:flex-row md:items-start md:justify-between">
                        <!-- Review Content -->
                        <div class="flex-1">
                            <div class="flex items-center mb-2">
                                <!-- Star Rating Display -->
                                <div class="flex items-center mr-3">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $review->rating)
                                            <i class="fas fa-star text-yellow-400 text-sm"></i>
                                        @else
                                            <i class="far fa-star text-gray-300 text-sm"></i>
                                        @endif
                                    @endfor
                                    <span class="ml-2 text-sm font-medium text-gray-700">{{ $review->rating }}.0</span>
                                </div>

                                @if($review->is_verified)
                                    <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full">
                                        <i class="fas fa-check-circle mr-1"></i>Verified Stay
                                    </span>
                                @endif
                            </div>

                            <h3 class="text-lg font-semibold text-gray-800 mb-1">{{ $review->title }}</h3>

                            <div class="flex items-center text-sm text-gray-500 mb-3">
                                <i class="fas fa-building mr-1"></i>
                                <a href="{{ route('student.hostels.show', $review->hostel) }}" class="hover:text-blue-600">
                                    {{ $review->hostel->name }}
                                </a>
                                <span class="mx-2">•</span>
                                <i class="far fa-calendar mr-1"></i>
                                {{ $review->created_at->format('M d, Y') }}
                                @if($review->stay_duration)
                                    <span class="mx-2">•</span>
                                    <i class="far fa-clock mr-1"></i>
                                    {{ $review->stay_duration }}
                                @endif
                            </div>

                            <p class="text-gray-600 mb-3">{{ $review->review }}</p>

                            @if($review->pros || $review->cons)
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                                    @if($review->pros)
                                        <div class="bg-green-50 p-3 rounded-lg">
                                            <p class="text-sm font-medium text-green-800 mb-1 flex items-center">
                                                <i class="fas fa-thumbs-up mr-1"></i>Pros
                                            </p>
                                            <p class="text-sm text-green-700">{{ $review->pros }}</p>
                                        </div>
                                    @endif

                                    @if($review->cons)
                                        <div class="bg-red-50 p-3 rounded-lg">
                                            <p class="text-sm font-medium text-red-800 mb-1 flex items-center">
                                                <i class="fas fa-thumbs-down mr-1"></i>Cons
                                            </p>
                                            <p class="text-sm text-red-700">{{ $review->cons }}</p>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <!-- Actions -->
                        <div class="flex md:flex-col space-x-2 md:space-x-0 md:space-y-2 mt-4 md:mt-0 md:ml-4">
                            @if($review->created_at->diffInDays(now()) <= 30)
                                <a href="{{ route('student.reviews.edit', $review) }}"
                                   class="px-3 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-center">
                                    <i class="fas fa-edit mr-1"></i>Edit
                                </a>
                                <form action="{{ route('student.reviews.destroy', $review) }}"
                                      method="POST"
                                      onsubmit="return confirmDelete()"
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="px-3 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition w-full">
                                        <i class="fas fa-trash mr-1"></i>Delete
                                    </button>
                                </form>
                            @else
                                <span class="text-xs text-gray-400 italic">Edit period expired</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $reviews->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-white rounded-lg shadow-sm p-12 text-center">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-star text-gray-400 text-4xl"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-800 mb-2">No Reviews Yet</h3>
            <p class="text-gray-500 mb-6">You haven't written any reviews. Share your experience to help other students!</p>

            @if($reviewableBookings->count() > 0)
                <button onclick="openReviewSelector()"
                        class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-star mr-2"></i>
                    Write Your First Review
                </button>
            @else
                <p class="text-sm text-gray-400">
                    You can only review hostels after completing your stay.
                    <a href="{{ route('student.bookings') }}" class="text-blue-600 hover:underline">View your bookings</a>
                </p>
            @endif
        </div>
    @endif
</div>

@push('scripts')
<script>
function openReviewSelector() {
    document.getElementById('reviewSelectorModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeReviewSelector() {
    document.getElementById('reviewSelectorModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function confirmDelete() {
    return Swal.fire({
        title: 'Delete Review?',
        text: 'Are you sure you want to delete this review? This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, delete it',
        cancelButtonText: 'No, keep it'
    }).then((result) => {
        return result.isConfirmed;
    });
}

// Close modal when clicking outside
document.getElementById('reviewSelectorModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeReviewSelector();
    }
});
</script>
@endpush
@endsection
