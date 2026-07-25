@extends('layouts.app')

@section('title', 'Image Proxy Logs')

@section('content')
<div class="max-w-6xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Image Proxy Logs</h1>

    <p class="text-sm text-gray-600 mb-4">Showing recent log entries related to image proxy and public/image.php. Use this to identify missing or forbidden images.</p>

    <div class="bg-white border rounded shadow">
        <div class="p-4 overflow-auto">
            @if(empty($lines))
                <p class="text-gray-500">No recent image proxy log entries found.</p>
            @else
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs text-gray-500 border-b">
                            <th class="py-2">#</th>
                            <th class="py-2">Log Line</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lines as $i => $line)
                            <tr class="border-b">
                                <td class="align-top py-2 px-2 text-xs text-gray-600">{{ $i + 1 }}</td>
                                <td class="py-2 font-mono text-xs break-words">{{ $line }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
@endsection
