@extends('layouts.student')

@section('title', 'Edit Review')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="{{ route('student.reviews') }}" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800">
            <i class="fas fa-arrow-left mr-2" aria-hidden="true"></i> Back to Reviews
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Edit Review for {{ $review->hostel->name ?? 'Hostel' }}</h1>

        <form action="{{ route('student.reviews.update', $review) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="rating" class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                <select id="rating" name="rating" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                    @for($i = 1; $i <= 5; $i++)
                        <option value="{{ $i }}" {{ old('rating', $review->rating) == $i ? 'selected' : '' }}>{{ $i }} Star{{ $i > 1 ? 's' : '' }}</option>
                    @endfor
                </select>
                @error('rating')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Review Title</label>
                <input type="text" id="title" name="title" value="{{ old('title', $review->title) }}" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="review" class="block text-sm font-medium text-gray-700 mb-2">Review Body</label>
                <textarea id="review" name="review" rows="5" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">{{ old('review', $review->review) }}</textarea>
                @error('review')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Update Review
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
