@extends('layouts.app')

@section('title', 'Add New Hostel')
@section('page-title', 'Create New Hostel')

@section('content')
<div class="max-w-4xl mx-auto">

    <form action="{{ route('admin.hostels.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Basic Information -->
        <div class="bg-white rounded-lg shadow border p-6">
            <h3 class="text-lg font-semibold mb-4">Basic Information</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Hostel Name -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-1">Hostel Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                        class="w-full border rounded-md px-3 py-2
                        @error('name') border-red-500 @enderror">

                    @error('name')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Location -->
                <div>
                    <label class="block text-sm font-medium mb-1">Location *</label>
                    <input type="text" name="location" value="{{ old('location') }}"
                        class="w-full border rounded-md px-3 py-2
                        @error('location') border-red-500 @enderror">

                    @error('location')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Address -->
                <div>
                    <label class="block text-sm font-medium mb-1">Address *</label>
                    <input type="text" name="address" value="{{ old('address') }}"
                        class="w-full border rounded-md px-3 py-2
                        @error('address') border-red-500 @enderror">

                    @error('address')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label class="block text-sm font-medium mb-1">Contact Phone</label>
                    <input type="text" name="contact_phone" value="{{ old('contact_phone') }}"
                        class="w-full border rounded-md px-3 py-2">
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium mb-1">Contact Email</label>
                    <input type="email" name="contact_email" value="{{ old('contact_email') }}"
                        class="w-full border rounded-md px-3 py-2">
                </div>

                <!-- Manager -->
                <div>
                    <label class="block text-sm font-medium mb-1">Assign Manager</label>
                    <select name="manager_id" class="w-full border rounded-md px-3 py-2">
                        <option value="">No Manager</option>
                        @foreach($managers as $manager)
                            <option value="{{ $manager->id }}"
                                {{ old('manager_id') == $manager->id ? 'selected' : '' }}>
                                {{ $manager->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Description -->
        <div class="bg-white rounded-lg shadow border p-6">
            <label class="block text-sm font-medium mb-1">Description</label>
            <textarea name="description" rows="4"
                class="w-full border rounded-md px-3 py-2">{{ old('description') }}</textarea>
        </div>

        

        <!-- Submit -->
        <div class="flex justify-end">
            <button type="submit"
                class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700">
                Save Hostel
            </button>
        </div>

    </form>
</div>
@endsection
