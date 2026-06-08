{{-- In resources/views/agent/hostels/index.blade.php --}}
@foreach($hostels as $hostel)
    <tr>
        <td class="px-6 py-4">{{ $hostel->name }}</td>
        <td class="px-6 py-4">{{ $hostel->location }}</td>
        <td class="px-6 py-4">
            <span class="px-2 py-1 text-xs rounded-full 
                @if($hostel->status == 'approved') bg-green-100 text-green-700
                @elseif($hostel->status == 'pending') bg-yellow-100 text-yellow-700
                @else bg-red-100 text-red-700 @endif">
                {{ ucfirst($hostel->status) }}
            </span>
        </td>
        <td class="px-6 py-4">
            <div class="flex gap-2">
                <a href="{{ route('agent.hostels.show', $hostel->id) }}" 
                   class="text-blue-600 hover:text-blue-800">
                    <i class="fas fa-eye"></i>
                </a>
                <a href="{{ route('agent.hostels.edit', $hostel->id) }}" 
                   class="text-green-600 hover:text-green-800">
                    <i class="fas fa-edit"></i>
                </a>
                <button onclick="confirmDelete('{{ route('agent.hostels.destroy', $hostel->id) }}', '{{ $hostel->name }}')" 
                        class="text-red-600 hover:text-red-800">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </td>
    </tr>
@endforeach