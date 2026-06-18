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
     * Handle sending OTP for manufacturing team login.
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|numeric',
        ]);

        $manufacturingTeam = ManufacturingTeam::where('phone', $request->phone)->first();

        if (!$manufacturingTeam) {
            return response()->json(['success' => false, 'message' => 'No account found with this mobile number.']);
        }

        if (!$manufacturingTeam->is_active) {
            return response()->json(['success' => false, 'message' => 'Your account is currently inactive.']);
        }

        // Generate OTP
        $otp = rand(100000, 999999);
        
        // Store OTP in session (with phone number)
        $request->session()->put('manufacturing_login_otp', $otp);
        $request->session()->put('manufacturing_login_phone', $request->phone);

        // Send OTP via Msg91Service
        $msg91Service = new \App\Services\Msg91Service();
        $sent = $msg91Service->sendOtp($request->phone, $otp);

        if ($sent) {
            return response()->json(['success' => true, 'message' => 'OTP sent successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'Failed to send OTP. Please try again.']);
    }

    /**
     * Handle verifying OTP and logging in.
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|numeric',
            'otp' => 'required|numeric',
        ]);

        $sessionOtp = $request->session()->get('manufacturing_login_otp');
        $sessionPhone = $request->session()->get('manufacturing_login_phone');

        if ($sessionPhone != $request->phone || $sessionOtp != $request->otp) {
            return response()->json(['success' => false, 'message' => 'Invalid OTP or Mobile Number.']);
        }

        $manufacturingTeam = ManufacturingTeam::where('phone', $request->phone)->first();

        if ($manufacturingTeam) {
            // Log in the manufacturing team
            Auth::guard('manufacturing-team')->login($manufacturingTeam);
            
            // Clear session data
            $request->session()->forget('manufacturing_login_otp');
            $request->session()->forget('manufacturing_login_phone');

            return response()->json([
                'success' => true, 
                'redirect' => route('manufacturing-team.dashboard')
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Account not found.']);
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