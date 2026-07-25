<?php
// app/Http/Controllers/Admin/AgentManagementController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HostelAgent;
use App\Models\User;
use App\Models\AgentCommission;
use App\Models\AgentWithdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AgentManagementController extends Controller
{
    /**
     * Display a listing of hostel agents
     */
    public function index(Request $request)
    {
        $query = HostelAgent::with('user');
        
        // Filter by status
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }
        
        // Search by name, email, or agent code
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('agent_code', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhereHas('user', function($user) use ($search) {
                      $user->where('name', 'like', "%{$search}%")
                           ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }
        
        // Sort by
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        
        $agents = $query->paginate(20);
        
        // Get statistics
        $stats = [
            'total' => HostelAgent::count(),
            'pending' => HostelAgent::where('status', 'pending')->count(),
            'active' => HostelAgent::where('status', 'active')->count(),
            'suspended' => HostelAgent::where('status', 'suspended')->count(),
            'total_commission' => HostelAgent::sum('total_commission'),
            'total_withdrawn' => HostelAgent::sum('withdrawn_amount'),
            'pending_withdrawals' => AgentWithdrawal::where('status', 'pending')->sum('amount'),
            'total_hostels' => HostelAgent::sum('total_hostels_added'),
            'total_rooms' => HostelAgent::sum('total_rooms_added'),
        ];
        
        return view('admin.agents.index', compact('agents', 'stats'));
    }
    
    /**
     * Show agent details
     */
    public function show($id)
    {
        $agent = HostelAgent::with(['user', 'commissions', 'withdrawals'])->findOrFail($id);
        
        // Get performance metrics
        $performance = [
            'total_commission' => $agent->total_commission,
            'available_balance' => $agent->available_balance,
            'withdrawn_amount' => $agent->withdrawn_amount,
            'total_hostels' => $agent->total_hostels_added,
            'total_rooms' => $agent->total_rooms_added,
            'commission_by_type' => $agent->commissions()
                ->select('type', DB::raw('SUM(amount) as total'))
                ->groupBy('type')
                ->get(),
            'monthly_commission' => $agent->commissions()
                ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'), DB::raw('SUM(amount) as total'))
                ->groupBy('month')
                ->orderBy('month', 'desc')
                ->limit(12)
                ->get(),
            'recent_withdrawals' => $agent->withdrawals()->latest()->limit(10)->get(),
            'recent_commissions' => $agent->commissions()->latest()->with(['hostel', 'booking'])->limit(10)->get(),
        ];
        
        return view('admin.agents.show', compact('agent', 'performance'));
    }
    
    /**
     * Approve agent application
     */
    public function approve(Request $request, $id)
    {
        $agent = HostelAgent::findOrFail($id);
        
        if ($agent->status !== 'pending') {
            return response()->json(['error' => 'Agent is not pending approval'], 422);
        }
        
        $agent->update([
            'status' => 'active',
            'approved_at' => now()
        ]);
        
        // Send approval email (you can implement this)
        // Mail::to($agent->user->email)->send(new AgentApprovedMail($agent));
        
        return response()->json([
            'success' => true,
            'message' => 'Agent application approved successfully',
            'agent' => $agent
        ]);
    }
    
    /**
     * Suspend agent
     */
    public function suspend($id)
    {
        $agent = HostelAgent::findOrFail($id);
        
        if ($agent->status === 'suspended') {
            return response()->json(['error' => 'Agent is already suspended'], 422);
        }
        
        $agent->update(['status' => 'suspended']);
        
        // Send suspension email
        // Mail::to($agent->user->email)->send(new AgentSuspendedMail($agent));
        
        return response()->json([
            'success' => true,
            'message' => 'Agent suspended successfully',
            'agent' => $agent
        ]);
    }
    
    /**
     * Activate agent
     */
    public function activate($id)
    {
        $agent = HostelAgent::findOrFail($id);
        
        $agent->update(['status' => 'active']);
        
        return response()->json([
            'success' => true,
            'message' => 'Agent activated successfully',
            'agent' => $agent
        ]);
    }
    
    /**
     * Deactivate agent (soft block)
     */
    public function deactivate($id)
    {
        $agent = HostelAgent::findOrFail($id);
        
        $agent->update(['status' => 'inactive']);
        
        return response()->json([
            'success' => true,
            'message' => 'Agent deactivated successfully',
            'agent' => $agent
        ]);
    }
    
    /**
     * Delete agent
     */
    public function destroy($id)
    {
        $agent = HostelAgent::findOrFail($id);
        
        // Delete related records
        $agent->commissions()->delete();
        $agent->withdrawals()->delete();
        
        // Delete user account (optional)
        // $agent->user->delete();
        
        $agent->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Agent deleted successfully'
        ]);
    }
    
    /**
     * Show agent commissions
     */
    public function commissions($id, Request $request)
    {
        $agent = HostelAgent::findOrFail($id);
        
        $commissions = $agent->commissions()
            ->with(['hostel', 'booking'])
            ->when($request->type, function($query, $type) {
                return $query->where('type', $type);
            })
            ->when($request->date_from, function($query, $date) {
                return $query->whereDate('created_at', '>=', $date);
            })
            ->when($request->date_to, function($query, $date) {
                return $query->whereDate('created_at', '<=', $date);
            })
            ->latest()
            ->paginate(20);
        
        $summary = [
            'total' => $agent->commissions()->sum('amount'),
            'booking' => $agent->commissions()->where('type', 'booking_commission')->sum('amount'),
            'hostel' => $agent->commissions()->where('type', 'hostel_added')->sum('amount'),
            'room' => $agent->commissions()->where('type', 'room_added')->sum('amount'),
            'referral' => $agent->commissions()->where('type', 'signup_bonus')->sum('amount'),
        ];
        
        if ($request->ajax()) {
            return response()->json([
                'commissions' => $commissions,
                'summary' => $summary
            ]);
        }
        
        return view('admin.agents.commissions', compact('agent', 'commissions', 'summary'));
    }
    
    /**
     * Show agent withdrawals
     */
    public function withdrawals($id, Request $request)
    {
        $agent = HostelAgent::findOrFail($id);
        
        $withdrawals = $agent->withdrawals()
            ->when($request->status, function($query, $status) {
                return $query->where('status', $status);
            })
            ->latest()
            ->paginate(20);
        
        if ($request->ajax()) {
            return response()->json($withdrawals);
        }
        
        return view('admin.agents.withdrawals', compact('agent', 'withdrawals'));
    }
    
    /**
     * Process withdrawal (approve/reject)
     */
    public function processWithdrawal(Request $request, $id)
    {
        $withdrawal = AgentWithdrawal::findOrFail($id);
        
        $request->validate([
            'action' => 'required|in:approve,reject',
            'notes' => 'nullable|string'
        ]);
        
        if ($request->action === 'approve') {
            $withdrawal->update([
                'status' => 'completed',
                'processed_at' => now(),
                'processed_by' => auth()->id(),
                'notes' => $request->notes
            ]);
            
            $message = 'Withdrawal approved and processed';
        } else {
            // Refund the amount back to agent's available balance
            $agent = $withdrawal->agent;
            $agent->increment('available_balance', $withdrawal->amount);
            
            $withdrawal->update([
                'status' => 'rejected',
                'processed_at' => now(),
                'processed_by' => auth()->id(),
                'rejection_reason' => $request->notes
            ]);
            
            $message = 'Withdrawal rejected and amount refunded';
        }
        
        return response()->json([
            'success' => true,
            'message' => $message,
            'withdrawal' => $withdrawal
        ]);
    }
    
    /**
     * Add manual commission to agent
     */
    public function addCommission(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'type' => 'required|in:bonus,adjustment,referral',
            'description' => 'required|string'
        ]);
        
        $agent = HostelAgent::findOrFail($id);
        
        $commission = $agent->addCommission(
            $request->amount,
            $request->type,
            $request->description
        );
        
        return response()->json([
            'success' => true,
            'message' => 'Commission added successfully',
            'commission' => $commission
        ]);
    }
    
    /**
     * Export agents list
     */
    public function export(Request $request)
    {
        $agents = HostelAgent::with('user')->get();
        
        $filename = 'agents_export_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$filename",
        ];
        
        $callback = function() use ($agents) {
            $file = fopen('php://output', 'w');
            
            // Add headers
            fputcsv($file, [
                'ID', 'Agent Code', 'Name', 'Email', 'Phone', 'Status', 
                'Total Commission', 'Available Balance', 'Withdrawn Amount',
                'Total Hostels', 'Total Rooms', 'Registered Date', 'Approved Date'
            ]);
            
            // Add data
            foreach ($agents as $agent) {
                fputcsv($file, [
                    $agent->id,
                    $agent->agent_code,
                    $agent->user->name,
                    $agent->user->email,
                    $agent->phone,
                    $agent->status,
                    $agent->total_commission,
                    $agent->available_balance,
                    $agent->withdrawn_amount,
                    $agent->total_hostels_added,
                    $agent->total_rooms_added,
                    $agent->created_at,
                    $agent->approved_at
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Dashboard statistics API
     */
    public function statistics()
    {
        $stats = [
            'total_agents' => HostelAgent::count(),
            'pending_agents' => HostelAgent::where('status', 'pending')->count(),
            'active_agents' => HostelAgent::where('status', 'active')->count(),
            'suspended_agents' => HostelAgent::where('status', 'suspended')->count(),
            'total_commission_paid' => HostelAgent::sum('total_commission'),
            'total_withdrawn' => HostelAgent::sum('withdrawn_amount'),
            'pending_withdrawals' => AgentWithdrawal::where('status', 'pending')->sum('amount'),
            'total_hostels_managed' => HostelAgent::sum('total_hostels_added'),
            'total_rooms_managed' => HostelAgent::sum('total_rooms_added'),
            'monthly_data' => $this->getMonthlyStats(),
            'top_performers' => HostelAgent::with('user')
                ->where('status', 'active')
                ->orderBy('total_commission', 'desc')
                ->limit(5)
                ->get()
        ];
        
        return response()->json($stats);
    }
    
    private function getMonthlyStats()
    {
        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $months->push([
                'month' => $month->format('M Y'),
                'new_agents' => HostelAgent::whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->count(),
                'commission' => AgentCommission::whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->sum('amount')
            ]);
        }
        return $months;
    }
}
