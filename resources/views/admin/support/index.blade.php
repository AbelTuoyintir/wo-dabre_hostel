@extends('layouts.app')

@section('title', 'Admin Support Ticket Center - UCC Hostel Booking')

@section('content')
<div class="container mx-auto px-4 py-8">

    <!-- Top Stats and Title -->
    <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Support Ticket Dashboard</h1>
            <p class="text-sm text-gray-500 mt-2">Manage, resolve, and reply to 24/7 user and guest platform support tickets.</p>
        </div>
        <div class="flex items-center gap-4">
            <div class="bg-blue-50 border border-blue-100 rounded-2xl px-5 py-3 text-center">
                <span class="block text-xs font-bold text-blue-500 uppercase">Total Tickets</span>
                <span class="text-2xl font-extrabold text-blue-900">{{ $tickets->count() }}</span>
            </div>
            <div class="bg-orange-50 border border-orange-100 rounded-2xl px-5 py-3 text-center">
                <span class="block text-xs font-bold text-orange-500 uppercase">Open / Pending</span>
                <span class="text-2xl font-extrabold text-orange-900">{{ $tickets->where('status', 'open')->count() }}</span>
            </div>
        </div>
    </div>

    <!-- Main List of tickets -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-lg font-bold text-gray-800">All Support Tickets</h2>
            <span class="text-xs font-bold text-gray-400">Sort: Latest First</span>
        </div>

        @if($tickets->isEmpty())
            <div class="text-center py-20 text-gray-400">
                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-headset text-2xl"></i>
                </div>
                <p class="text-sm font-semibold">No support tickets have been opened yet.</p>
                <p class="text-xs mt-1">When users or guests use the support system, tickets will display here.</p>
            </div>
        @else
            <div class="divide-y divide-gray-100">
                @foreach($tickets as $ticket)
                    <div class="p-6 hover:bg-gray-50/50 transition duration-150" x-data="{ expanded: false }">
                        <div class="flex flex-wrap items-center justify-between gap-6">
                            <div class="space-y-2 flex-1 min-w-[280px]">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-xs font-bold px-2.5 py-1 rounded-full uppercase tracking-wider
                                        {{ $ticket->status === 'open' ? 'bg-blue-50 text-blue-600 border border-blue-100 animate-pulse' : '' }}
                                        {{ $ticket->status === 'in_progress' ? 'bg-orange-50 text-orange-600 border border-orange-100' : '' }}
                                        {{ $ticket->status === 'resolved' ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : '' }}
                                        {{ $ticket->status === 'closed' ? 'bg-gray-100 text-gray-600' : '' }}
                                    ">
                                        {{ str_replace('_', ' ', $ticket->status) }}
                                    </span>
                                    <span class="text-xs font-bold px-2.5 py-1 rounded-full uppercase bg-gray-100 text-gray-500 tracking-wider">
                                        {{ $ticket->category }}
                                    </span>
                                    <span class="text-xs font-bold px-2.5 py-1 rounded-full uppercase
                                        {{ $ticket->priority === 'high' ? 'bg-rose-50 text-rose-600 border border-rose-100' : '' }}
                                        {{ $ticket->priority === 'medium' ? 'bg-amber-50 text-amber-600 border border-amber-100' : '' }}
                                        {{ $ticket->priority === 'low' ? 'bg-gray-50 text-gray-600 border border-gray-100' : '' }}
                                    ">
                                        {{ $ticket->priority }} Priority
                                    </span>
                                    <span class="text-xs text-gray-400 font-medium ml-1">UUID: {{ substr($ticket->uuid, 0, 8) }}</span>
                                </div>

                                <h3 class="text-base font-bold text-gray-800">{{ $ticket->subject }}</h3>

                                <div class="text-xs text-gray-500 flex items-center gap-3">
                                    <span>
                                        <i class="fas fa-user text-gray-400 mr-1"></i>
                                        @if($ticket->user)
                                            <strong>{{ $ticket->user->name }}</strong> ({{ $ticket->user->email }} - Student)
                                        @else
                                            <strong>{{ $ticket->guest_name }}</strong> ({{ $ticket->guest_email }} - Guest)
                                        @endif
                                    </span>
                                    <span>•</span>
                                    <span><i class="fas fa-calendar-alt text-gray-400 mr-1"></i> Opened {{ $ticket->created_at->diffForHumans() }}</span>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <button @click="expanded = !expanded" class="bg-blue-50 text-blue-600 font-bold px-4 py-2 rounded-xl text-xs hover:bg-blue-100 transition flex items-center gap-1.5">
                                    <i class="fas fa-comments"></i>
                                    <span x-text="expanded ? 'Hide Chat' : 'Manage Ticket'"></span>
                                </button>

                                @if($ticket->status !== 'resolved' && $ticket->status !== 'closed')
                                    <form action="{{ route('admin.support.close', ['ticket' => $ticket->uuid]) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="bg-emerald-50 text-emerald-600 font-bold px-4 py-2 rounded-xl text-xs hover:bg-emerald-100 transition flex items-center gap-1.5">
                                            <i class="fas fa-check-circle"></i> Resolve
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>

                        <!-- Expanded Support Chat View -->
                        <div x-show="expanded" class="mt-8 border-t border-gray-100 pt-6 space-y-6" style="display: none;">

                            <!-- Messages Window -->
                            <div class="bg-gray-50/50 border border-gray-100 rounded-3xl p-6 space-y-4 max-h-[400px] overflow-y-auto">
                                @foreach($ticket->messages as $msg)
                                    <div class="flex flex-col {{ $msg->is_admin_reply ? 'items-end' : 'items-start' }}">
                                        <div class="max-w-[75%] rounded-2xl px-4 py-3 text-xs shadow-sm
                                            {{ $msg->is_admin_reply ? 'bg-indigo-600 text-white' : 'bg-white border border-gray-100 text-gray-800' }}
                                        ">
                                            <div class="font-extrabold text-[10px] mb-1 opacity-80 uppercase tracking-wider">
                                                {{ $msg->sender_name }}
                                                @if($msg->user_id && $msg->user && $msg->user->role === 'admin')
                                                    <span class="bg-white/20 text-white text-[8px] px-1 py-0.5 rounded ml-1 font-bold">STAFF</span>
                                                @endif
                                            </div>
                                            <p class="whitespace-pre-line leading-relaxed">{{ $msg->message }}</p>
                                        </div>
                                        <span class="text-[9px] text-gray-400 mt-1 mr-1 ml-1">{{ $msg->created_at->format('M d, g:i A') }}</span>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Reply Box -->
                            @if($ticket->status !== 'resolved' && $ticket->status !== 'closed')
                                <form action="{{ route('admin.support.reply', ['ticket' => $ticket->uuid]) }}" method="POST" class="space-y-3">
                                    @csrf
                                    <label class="block text-xs font-bold text-gray-500 uppercase">Write a reply directly as Support Agent</label>
                                    <div class="flex gap-2">
                                        <input type="text" name="message" required placeholder="Type your reply here..."
                                               class="flex-1 text-sm px-4 py-3 border border-gray-200 rounded-2xl focus:ring-1 focus:ring-blue-500 outline-none transition bg-white shadow-inner">
                                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl text-sm font-bold transition flex items-center justify-center gap-1.5 shadow-md">
                                            <i class="fas fa-reply"></i> Post Reply
                                        </button>
                                    </div>
                                </form>
                            @else
                                <div class="bg-emerald-50 border border-emerald-100 text-emerald-800 text-xs px-4 py-3 rounded-2xl flex items-center gap-2">
                                    <i class="fas fa-info-circle text-emerald-500 text-base"></i>
                                    <span>This ticket has been marked as <strong>Resolved</strong>. No further responses can be posted.</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
