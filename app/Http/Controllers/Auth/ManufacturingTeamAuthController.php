<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ManufacturingTeam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ManufacturingTeamAuthController extends Controller
{
    /**
     * Show the manufacturing team login form.
     */
    public function showLoginForm()
    {
        return view('auth.manufacturing-team-login');
    }

    /**
     * Handle manufacturing team login.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Find the manufacturing team by email
        $manufacturingTeam = ManufacturingTeam::where('email', $request->email)->first();

        // Check if manufacturing team exists and password is correct
        if ($manufacturingTeam && Hash::check($request->password, $manufacturingTeam->password)) {
            // Log in the manufacturing team
            Auth::guard('manufacturing-team')->login($manufacturingTeam);
            
            // Redirect to manufacturing team dashboard
            return redirect()->route('manufacturing-team.dashboard');
        }

        // If authentication fails
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('email'));
    }

    /**
     * Handle manufacturing team logout.
     */
    public function logout(Request $request)
    {
        Auth::guard('manufacturing-team')->logout();
        return redirect()->route('manufacturing-team.login');
    }
}