<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SalesTeam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SalesTeamAuthController extends Controller
{
    /**
     * Show the sales team login form.
     */
    public function showLoginForm()
    {
        return view('auth.sales-team.login');
    }

    /**
     * Handle sales team login.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::guard('sales-team')->attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            
            return redirect()->intended(route('sales-team.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('email'));
    }

    /**
     * Handle sales team logout.
     */
    public function logout(Request $request)
    {
        Auth::guard('sales-team')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('sales-team.login');
    }
}