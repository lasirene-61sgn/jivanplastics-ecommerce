<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AdminAuthController extends Controller
{
    /**
     * Show the admin login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.admin-login');
    }

    /**
     * Handle admin login.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::guard('admin')->attempt($credentials)) {
            $request->session()->regenerate();
            
            $admin = Auth::guard('admin')->user();
            $admin->last_login_ip = $admin->current_login_ip;
            
            // Get real IP
            $ip = $request->header('CF-Connecting-IP') 
                ?? $request->header('X-Forwarded-For') 
                ?? $request->header('X-Real-IP')
                ?? $request->ip();

            if (strpos($ip, ',') !== false) {
                $ip = explode(',', $ip)[0];
            }
            $cleanIp = trim($ip);
            $admin->current_login_ip = $cleanIp;
            $admin->last_login_at = now();
            $admin->save();

            // Record login in ActivityLog
            ActivityLog::create([
                'admin_id' => $admin->id,
                'user_type' => 'admin',
                'action' => 'login',
                'model_type' => 'Admin',
                'model_id' => $admin->id,
                'ip_address' => $cleanIp,
                'details' => [
                    'name' => $admin->name,
                    'email' => $admin->email,
                    'channel' => 'Admin Panel',
                ]
            ]);

            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Handle admin logout.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        if ($admin) {
            ActivityLog::create([
                'admin_id' => $admin->id,
                'user_type' => 'admin',
                'action' => 'logout',
                'model_type' => 'Admin',
                'model_id' => $admin->id,
                'details' => [
                    'name' => $admin->name,
                    'email' => $admin->email,
                ]
            ]);
        }

        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
