<?php
// app/Http/Controllers/Agent/Auth/AgentRegisterController.php
namespace App\Http\Controllers\agent;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\HostelAgent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AgentRegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('agent.auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'phone' => 'required|string|unique:hostel_agents,phone',
            'password' => 'required|min:8|confirmed',
            'id_card_number' => 'nullable|string',
            'id_card_image' => 'nullable|image|max:2048',
            'referral_code' => 'nullable|string|exists:hostel_agents,agent_code'
        ]);

        DB::beginTransaction();

        try {
            // Create user account
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'hostel_agent',
                'email_verified_at' => now()
            ]);

            // Upload ID card if provided
            $idCardPath = null;
            if ($request->hasFile('id_card_image')) {
                $idCardPath = $request->file('id_card_image')->store('agent_ids', 'public');
            }

            // Generate unique agent code
            $agentCode = 'AG-' . strtoupper(Str::random(8));
            while (HostelAgent::where('agent_code', $agentCode)->exists()) {
                $agentCode = 'AG-' . strtoupper(Str::random(8));
            }

            // Create agent profile
            $agent = HostelAgent::create([
                'user_id' => $user->id,
                'agent_code' => $agentCode,
                'phone' => $request->phone,
                'id_card_number' => $request->id_card_number,
                'id_card_image' => $idCardPath,
                'status' => 'pending',
                'total_commission' => 0,
                'available_balance' => 0
            ]);

            // Handle referral bonus
            if ($request->referral_code) {
                $referrer = HostelAgent::where('agent_code', $request->referral_code)->first();
                if ($referrer) {
                    $referrer->addCommission(
                        50.00,
                        'signup_bonus',
                        "Referral bonus for recruiting agent {$agentCode}",
                        $agent->id
                    );
                }
            }

            DB::commit();

            // Send welcome email
            // Mail::to($user->email)->send(new AgentWelcomeMail($agent));

            return redirect()->route('agent.login')->with('success', 
                'Registration successful! Your application is pending approval. We will notify you once approved.'
            );

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Registration failed. Please try again.');
        }
    }
}