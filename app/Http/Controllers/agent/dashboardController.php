<?php
// app/Http/Controllers/Agent/DashboardController.php
namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\HostelAgent;
use App\Models\Hostel;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('agent.approved');
    }

    public function index()
    {
        $agent = Auth::user()->agent;
        
        $stats = [
            'total_hostels' => $agent->total_hostels_added,
            'total_rooms' => $agent->total_rooms_added,
            'pending_hostels' => Hostel::where('agent_id', $agent->id)->where('status', 'pending')->count(),
            'published_hostels' => Hostel::where('agent_id', $agent->id)->where('status', 'approved')->count(),
            'total_commission' => $agent->total_commission,
            'available_balance' => $agent->available_balance,
            'pending_withdrawals' => $agent->withdrawals()->where('status', 'pending')->sum('amount'),
            'recent_commissions' => $agent->commissions()->latest()->take(5)->get(),
            'recent_withdrawals' => $agent->withdrawals()->latest()->take(5)->get(),
            'chart_data' => $this->getCommissionChartData($agent)
        ];

        return view('agent.dashboard', compact('stats', 'agent'));
    }

    private function getCommissionChartData($agent)
    {
        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $total = $agent->commissions()
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('amount');
            
            $months->push([
                'month' => $month->format('M'),
                'commission' => $total
            ]);
        }
        return $months;
    }

    public function commissionHistory(Request $request)
    {
        $agent = Auth::user()->agent;
        
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
            'booking_commission' => $agent->commissions()->where('type', 'booking_commission')->sum('amount'),
            'hostel_bonus' => $agent->commissions()->where('type', 'hostel_added')->sum('amount'),
            'room_bonus' => $agent->commissions()->where('type', 'room_added')->sum('amount'),
            'referral_bonus' => $agent->commissions()->where('type', 'signup_bonus')->sum('amount'),
        ];

        return view('agent.commissions.index', compact('commissions', 'summary'));
    }

    public function withdrawalHistory(Request $request)
    {
        $agent = Auth::user()->agent;

        $withdrawals = $agent->withdrawals()
            ->when($request->status, function($query, $status) {
                return $query->where('status', $status);
            })
            ->latest()
            ->paginate(20);

        $summary = [
            'total' => $agent->withdrawals()->sum('amount'),
        ];

        return view('agent.withdrawals.index', compact('withdrawals', 'summary'));
    }

    public function requestWithdrawal(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:50|max:' . Auth::user()->agent->available_balance,
            'payment_method' => 'required|in:mobile_money,bank_transfer,paypal',
            'account_number' => 'required|string',
            'account_name' => 'required|string',
            'bank_name' => 'required_if:payment_method,bank_transfer|nullable|string'
        ]);

        $agent = Auth::user()->agent;

        try {
            $withdrawal = $agent->withdraw(
                $request->amount,
                $request->payment_method,
                $request->account_number,
                $request->account_name,
                $request->bank_name
            );

            return redirect()->route('agent.withdrawals')->with('success', 
                'Withdrawal request submitted successfully!'
            );

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}