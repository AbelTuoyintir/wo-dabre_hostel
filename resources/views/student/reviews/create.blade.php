@extends('layouts.student')

@section('title', 'Write a Review')
@section('content')

<div class="max-w-3xl mx-auto">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-center">
            <a href="{{ route('student.reviews') }}" class="text-gray-500 hover:text-gray-700 mr-4">
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
                <img src="{{ Storage::url($hostel->primaryImage->path) }}" 
                     alt="{{ $hostel->name }}"
                     class="w-20 h-20 object-cover rounded-lg">
            @else
                <div class="w-20 h-20 bg-gray-200 rounded-lg flex items-center justify-center">
                    <i class="fas fa-building text-gray-400 text-2xl"></i>
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
        <form action="{{ route('student.reviews.store') }}" method="POST" id="reviewForm">
            @csrf
            <input type="hidden" name="hostel_id" value="{{ $hostel->id }}">
            @if(isset($booking))
                <input type="hidden" name="booking_id" value="{{ $booking->id }}">
            @endif

            <!-- Rating -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Your Rating <span class="text-red-500">*</span>
                </label>
                <div class="flex items-center space-x-2" x-data="{ rating: 0 }">
                    <div class="flex space-x-1 text-3xl">
                        <template x-for="star in 5" :key="star">
                            <i class="cursor-pointer hover:scale-110 transition"
                               :class="star <= rating ? 'fas fa-star text-yellow-400' : 'far fa-star text-gray-300'"
                               @click="rating = star"
                               @mouseover="rating = star"></i>
                        </template>
                    </div>
                    <input type="hidden" name="rating" x-model="rating" required>
                    <span class="text-sm text-gray-500 ml-2" x-text="rating ? rating + ' stars' : 'Select rating'"></span>
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
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Your Review <span class="text-red-500">*</span>
                </label>
                <textarea name="review" rows="5" 
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                          placeholder="Tell us about your experience at this hostel. What did you like? What could be improved?"
                          required>{{ old('review') }}</textarea>
                <p class="text-xs text-gray-500 mt-1">Minimum 20 characters</p>
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
                        <i class="fas fa-thumbs-up text-green-500 mr-1"></i> Pros (What you liked)
                    </label>
                    <textarea name="pros" rows="3" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                              placeholder="e.g., Clean rooms, friendly staff, good location">{{ old('pros') }}</textarea>
                </div>

                <!-- Cons -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-thumbs-down text-red-500 mr-1"></i> Cons (What could be improved)
                    </label>
                    <textarea name="cons" rows="3" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                              placeholder="e.g., Noisy at night, slow WiFi">{{ old('cons') }}</textarea>
                </div>
            </div>

            <!-- Tips -->
            <div class="bg-blue-50 p-4 rounded-lg mb-6">
                <h4 class="font-semibold text-blue-800 mb-2 flex items-center">
                    <i class="fas fa-lightbulb mr-2"></i>
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
                   class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Submit Review
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
// Character counter for review
document.querySelector('textarea[name="review"]')?.addEventListener('input', function() {
    const minLength = 20;
    const currentLength = this.value.length;
    const counter = document.createElement('p');
    
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
</script>
@endpush
@endsection