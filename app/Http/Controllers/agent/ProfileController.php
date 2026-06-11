<?php
// app/Http/Controllers/Agent/ProfileController.php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\HostelAgent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    /**
     * Show form to complete agent profile
     */
    public function showCompleteForm()
    {
        $user = Auth::user();
        
        // If agent already exists, redirect to dashboard or pending
        if ($user->agent) {
            if ($user->agent->status === 'active') {
                return redirect()->route('agent.dashboard');
            } elseif ($user->agent->status === 'pending') {
                return redirect()->route('agent.pending');
            }
        }
        
        return view('agent.complete-profile');
    }
    
    /**
     * Complete agent profile
     */
    public function completeProfile(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|unique:hostel_agents,phone',
            'id_card_number' => 'nullable|string',
            'id_card_image' => 'nullable|image|max:2048',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'region' => 'nullable|string',
            'emergency_contact' => 'nullable|string',
            'emergency_phone' => 'nullable|string',
        ]);
        
        $user = Auth::user();
        
        // Check if agent already exists
        if ($user->agent) {
            return redirect()->route('agent.dashboard')
                ->with('error', 'Profile already completed.');
        }
        
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
            'address' => $request->address,
            'city' => $request->city,
            'region' => $request->region,
            'emergency_contact' => $request->emergency_contact,
            'emergency_phone' => $request->emergency_phone,
            'status' => 'pending',
            'total_commission' => 0,
            'available_balance' => 0,
            'total_hostels_added' => 0,
            'total_rooms_added' => 0,
        ]);
        
        return redirect()->route('agent.pending')
            ->with('success', 'Profile completed successfully! Your application is pending approval.');
    }
    
    /**
     * Show agent profile
     */
    public function show()
    {
        $agent = Auth::user()->agent;
        
        if (!$agent) {
            return redirect()->route('agent.complete-profile');
        }
        
        return view('agent.profile', compact('agent'));
    }
    
    /**
     * Update agent profile
     */
    public function update(Request $request)
    {
        $agent = Auth::user()->agent;

        if (!$agent) {
            return redirect()->route('agent.complete-profile')
                ->with('warning', 'Please complete your agent profile first.');
        }

        $request->validate([
            'phone' => 'required|string|unique:hostel_agents,phone,' . $agent->id,
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'region' => 'nullable|string',
            'emergency_contact' => 'nullable|string',
            'emergency_phone' => 'nullable|string',
        ]);
        
        $agent->update($request->only([
            'phone', 'address', 'city', 'region', 'emergency_contact', 'emergency_phone'
        ]));
        
        return redirect()->route('agent.profile')
            ->with('success', 'Profile updated successfully!');
    }
}