@extends('layouts.student')

@section('title', 'Write a Review')
@section('content')

<div class="max-w-3xl mx-auto">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-center">
            <a href="{{ route('student.reviews') }}" class="text-gray-500 hover:text-gray-700 mr-4" aria-label="Go back to my reviews">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Write a Review</h1>
                <p class="text-gray-600">Share your experience at {{ $hostel->name }}</p>
            </div>
        </div>
    </div>

    <!-- Hostel Summary -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-center space-x-4">
            @if($hostel->primaryImage)
                <img src="{{ image_url($hostel->primaryImage->image_path) }}" 
                     alt="{{ $hostel->name }}"
                     class="w-20 h-20 object-cover rounded-lg">
            @else
                <div class="w-20 h-20 bg-gray-200 rounded-lg flex items-center justify-center">
                    <i class="fas fa-building text-gray-400 text-2xl" aria-hidden="true"></i>
                </div>
            @endif
            <div>
                <h2 class="text-xl font-semibold text-gray-800">{{ $hostel->name }}</h2>
                <p class="text-gray-500">{{ $hostel->location }}</p>
                @if(isset($booking))
                    <p class="text-sm text-gray-400 mt-1">
                        Stayed: {{ $booking->check_in->format('M d, Y') }} - {{ $booking->check_out->format('M d, Y') }}
                    </p>
                @endif
            </div>
        </div>
    </div>

    <!-- Review Form -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <form action="{{ route('student.reviews.store') }}" method="POST" id="reviewForm" class="no-loader">
            @csrf
            <input type="hidden" name="hostel_id" value="{{ $hostel->id }}">
            @if(isset($booking))
                <input type="hidden" name="booking_id" value="{{ $booking->id }}">
            @endif

            <!-- Rating -->
            <div class="mb-6" id="rating-section">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Your Rating <span class="text-red-500">*</span>
                </label>
                <div class="flex items-center space-x-2" x-data="{ rating: 0, hoverRating: 0 }">
                    <div class="flex space-x-1 text-3xl animate-fade-in">
                        <template x-for="star in 5" :key="star">
                            <button type="button"
                                    :id="'star-btn-' + star"
                                    :aria-label="'Rate ' + star + ' star' + (star > 1 ? 's' : '')"
                                    class="cursor-pointer hover:scale-110 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 rounded-lg p-0.5"
                                    @click="rating = star; document.getElementById('rating-error').classList.add('hidden')"
                                    @mouseover="hoverRating = star"
                                    @mouseleave="hoverRating = 0"
                                    @focus="hoverRating = star"
                                    @blur="hoverRating = 0">
                                <i :class="star <= (hoverRating || rating) ? 'fas fa-star text-yellow-400' : 'far fa-star text-gray-300'" aria-hidden="true"></i>
                            </button>
                        </template>
                    </div>
                    <input type="hidden" id="ratingInput" name="rating" x-model="rating">
                    <span class="text-sm text-gray-500 ml-2" x-text="rating ? rating + ' stars' : 'Select rating'"></span>
                </div>
                <div id="rating-error" class="hidden text-sm text-red-600 mt-2 font-medium flex items-center space-x-1" role="alert">
                    <i class="fas fa-exclamation-circle" aria-hidden="true"></i>
                    <span>Please select a rating of 1 to 5 stars before submitting.</span>
                </div>
                @error('rating')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Title -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Review Title <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" value="{{ old('title') }}" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                       placeholder="Summarize your experience"
                       required>
                @error('title')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Review Text -->
            <div class="mb-6">
                <label for="review-textarea" class="block text-sm font-medium text-gray-700 mb-2">
                    Your Review <span class="text-red-500">*</span>
                </label>
                <textarea id="review-textarea" name="review" rows="5"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                          placeholder="Tell us about your experience at this hostel. What did you like? What could be improved?"
                          aria-describedby="char-counter-container"
                          required>{{ old('review') }}</textarea>
                <div id="char-counter-container" class="text-xs text-gray-500 mt-1 font-medium animate-pulse" aria-live="polite">
                    Minimum 20 characters
                </div>
                @error('review')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Stay Duration -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Duration of Stay
                </label>
                <input type="text" name="stay_duration" value="{{ old('stay_duration', isset($booking) ? $booking->check_in->diffInDays($booking->check_out) . ' nights' : '') }}" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                       placeholder="e.g., 1 semester, 3 months, 30 nights">
                @error('stay_duration')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Pros & Cons Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Pros -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-thumbs-up text-green-500 mr-1" aria-hidden="true"></i> Pros (What you liked)
                    </label>
                    <textarea name="pros" rows="3" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                              placeholder="e.g., Clean rooms, friendly staff, good location">{{ old('pros') }}</textarea>
                </div>

                <!-- Cons -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-thumbs-down text-red-500 mr-1" aria-hidden="true"></i> Cons (What could be improved)
                    </label>
                    <textarea name="cons" rows="3" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                              placeholder="e.g., Noisy at night, slow WiFi">{{ old('cons') }}</textarea>
                </div>
            </div>

            <!-- Tips -->
            <div class="bg-blue-50 p-4 rounded-lg mb-6">
                <h4 class="font-semibold text-blue-800 mb-2 flex items-center">
                    <i class="fas fa-lightbulb mr-2" aria-hidden="true"></i>
                    Tips for Writing a Helpful Review
                </h4>
                <ul class="text-sm text-blue-700 space-y-1 list-disc list-inside">
                    <li>Be specific about your experience</li>
                    <li>Mention both positive and negative aspects</li>
                    <li>Include details about cleanliness, staff, location, amenities</li>
                    <li>Your review helps other students make informed decisions</li>
                </ul>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-3">
                <a href="{{ route('student.reviews') }}" 
                   class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition duration-150">
                    Cancel
                </a>
                <button type="submit" id="submitBtn"
                        class="no-loader px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-150 inline-flex items-center space-x-2 focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <span id="submitText">Submit Review</span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const reviewForm = document.getElementById('reviewForm');
    const ratingInput = document.getElementById('ratingInput');
    const ratingError = document.getElementById('rating-error');
    const reviewTextarea = document.getElementById('review-textarea');
    const charCounter = document.getElementById('char-counter-container');
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');

    if (reviewTextarea && charCounter) {
        reviewTextarea.addEventListener('input', function() {
            const minLength = 20;
            const currentLength = this.value.length;

            if (currentLength < minLength) {
                this.classList.add('border-red-500');
                this.classList.remove('border-gray-300');
                charCounter.className = 'text-xs text-red-500 mt-1 font-semibold';
                charCounter.textContent = `${minLength - currentLength} more characters needed`;
            } else {
                this.classList.remove('border-red-500');
                this.classList.add('border-gray-300');
                charCounter.className = 'text-xs text-green-600 mt-1 font-semibold';
                charCounter.textContent = `Excellent! Minimum length met (${currentLength} characters)`;
            }
        });
    }

    if (reviewForm) {
        reviewForm.addEventListener('submit', function(e) {
            const ratingValue = parseInt(ratingInput?.value || '0', 10);
            const reviewLength = reviewTextarea?.value.trim().length || 0;

            // 1. Validate rating selection
            if (ratingValue === 0) {
                e.preventDefault();
                e.stopPropagation(); // Stop propagation to bypass global page-blocking loader
                ratingError?.classList.remove('hidden');
                document.getElementById('rating-section')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                // Focus the first star button for keyboard users
                document.getElementById('star-btn-1')?.focus();
                return false;
            } else {
                ratingError?.classList.add('hidden');
            }

            // 2. Validate review minimum characters
            if (reviewLength < 20) {
                e.preventDefault();
                e.stopPropagation(); // Stop propagation to bypass global page-blocking loader
                reviewTextarea?.classList.add('border-red-500');
                reviewTextarea?.focus();
                reviewTextarea?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                return false;
            }

            // 3. Prevent double submission and show localized spinner feedback
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
                if (submitText) {
                    submitText.innerHTML = `<i class="fas fa-spinner fa-spin mr-2"></i>Submitting Review...`;
                }
            }
        });
    }
});
</script>
@endpush
@endsection