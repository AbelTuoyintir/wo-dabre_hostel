@extends('layouts.app')

@section('title', 'Users')
@section('page-title', 'Manage Users')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-slate-100">
    <div class="px-4 py-3 border-b border-slate-100 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <h3 class="text-sm font-semibold text-slate-800">Users</h3>

        <form method="GET" class="flex flex-col sm:flex-row gap-2 text-sm">
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                class="w-full sm:w-56 rounded-md border border-slate-300 bg-white px-2.5 py-1.5 text-xs placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                placeholder="Search name, email, student ID"
            >

            <select
                name="role"
                class="rounded-md border border-slate-300 bg-white px-2.5 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                onchange="this.form.submit()"
            >
                <option value="">All roles</option>
                <option value="student" {{ request('role')=='student' ? 'selected' : '' }}>Student</option>
                <option value="hostel_manager" {{ request('role')=='hostel_manager' ? 'selected' : '' }}>Hostel Manager</option>
                <option value="admin" {{ request('role')=='admin' ? 'selected' : '' }}>Admin</option>
            </select>

            <button
                type="submit"
                class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-indigo-700"
            >
                Filter
            </button>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-100 text-xs font-medium text-slate-500 uppercase">
            <tr>
                <th class="px-4 py-2 text-left">#</th>
                <th class="px-4 py-2 text-left">Name / Email</th>
                <th class="px-4 py-2 text-left">Role</th>
                <th class="px-4 py-2 text-left">Student ID</th>
                <th class="px-4 py-2 text-left">Status</th>
                <th class="px-4 py-2 text-left">Registered</th>
                <th class="px-4 py-2 text-right">Actions</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
            @forelse ($users as $user)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-2">{{ $user->id }}</td>
                    <td class="px-4 py-2">
                        <div class="font-semibold text-slate-800">{{ $user->name }}</div>
                        <div class="text-xs text-slate-500">{{ $user->email }}</div>
                    </td>
                    <td class="px-4 py-2">
                        <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-medium text-slate-700 capitalize">
                            {{ $user->role }}
                        </span>
                    </td>
                    <td class="px-4 py-2 text-sm">
                        {{ $user->student_id ?? '-' }}
                    </td>
                    <td class="px-4 py-2">
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-medium
                            {{ $user->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-4 py-2 text-sm">
                        {{ $user->created_at->format('d M Y') }}
                    </td>
                    <td class="px-4 py-2 text-right text-xs space-x-1">
                        <a href="{{ route('admin.users.edit', $user) }}"
                           class="inline-flex items-center rounded-md border border-slate-300 px-2 py-1 font-medium text-slate-700 hover:bg-slate-100">
                            Edit
                        </a>

                        <form action="{{ route('admin.users.toggle-status', $user) }}"
                              method="POST"
                              class="inline"
                        >
                            @csrf
                            @method('PATCH')
                            <button
                                class="inline-flex items-center rounded-md border border-slate-300 px-2 py-1 font-medium
                                    {{ $user->is_active ? 'text-amber-700 hover:bg-amber-50' : 'text-emerald-700 hover:bg-emerald-50' }}">
                                {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-6 text-center text-sm text-slate-500">
                        No users found.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if ($users->hasPages())
        <div class="px-4 py-3 border-t border-slate-100">
            {{ $users->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
