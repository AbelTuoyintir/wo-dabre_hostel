@extends('layouts.agent')

@section('title', 'Commission History')
@section('page-title', 'Commission History')

@section('content')
<div class="bg-white rounded-xl shadow p-6">
    <h2 class="text-xl font-bold mb-4">Commission History</h2>
    
    @if($commissions->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($commissions as $commission)
                    <tr>
                        <td class="px-6 py-4 text-sm">{{ $commission->created_at->format('d M Y') }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-800">
                                {{ str_replace('_', ' ', ucfirst($commission->type)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm">{{ $commission->description }}</td>
                        <td class="px-6 py-4 text-right font-semibold text-green-600">+₵{{ number_format($commission->amount, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $commissions->links() }}
        </div>
    @else
        <p class="text-gray-500 text-center py-8">No commission records found.</p>
    @endif
</div>
@endsection