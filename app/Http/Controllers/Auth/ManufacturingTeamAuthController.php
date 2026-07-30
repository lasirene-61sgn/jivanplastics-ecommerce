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
    public function checkPhone(Request $request)
    {
        $request->validate([
            'phone' => 'required|numeric',
        ]);

        $team = ManufacturingTeam::where('phone', $request->phone)->first();

        if (!$team) {
            return response()->json(['success' => false, 'message' => 'No account found with this mobile number.']);
        }

        if (!$team->is_active) {
            return response()->json(['success' => false, 'message' => 'Your account is currently inactive.']);
        }

        $hasPassword = !empty($team->password);

        if($hasPassword){
            return response()->json([
                'success'    =>  true,
                'has_password' => true,
                'message'    => 'Set the password'
            ]);
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
    public function verifyOtpAndSetPassword(Request $request)
    {
        $request->validate([
            'phone' => 'required|numeric',
            'otp' => 'required|numeric',
            'password' => 'required|min:6|confirmed'
        ]);

        $sessionOtp = $request->session()->get('manufacturing_login_otp');
        $sessionPhone = $request->session()->get('manufacturing_login_phone');

        if ($sessionPhone != $request->phone || $sessionOtp != $request->otp) {
            return response()->json(['success' => false, 'message' => 'Invalid OTP or Mobile Number.']);
        }

        $team = ManufacturingTeam::where('phone', $request->phone)->first();

        if ($team) {
            $team->password = $request->password;
            $team->save();
           
            
            // Clear session data
            $request->session()->forget('manufacturing_login_otp');
            $request->session()->forget('manufacturing_login_phone');

             // Log in the manufacturing team
            Auth::guard('manufacturing-team')->login($team);

            return response()->json([
                'success' => true, 
                'redirect' => route('manufacturing-team.dashboard')
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Account not found.']);
    }

    public function loginWithPassword(Request $request){
        $request->validate([
            'phone' => 'required|numeric',
            'password' => 'required',
        ]);

        $team =ManufacturingTeam::where('phone', $request->phone)->first();

        if(!$team || !Hash::check($request->password, $team->password)){
            return response()->json(['success' => false, 'message' => 'Enter the correct number or password']);
        }

        if(!$team->is_active){
            return response()->json(['success' => false, 'messsage' => 'Your account is not their contact Admin']);
        }

        Auth::guard('manufacturing-team')->login($team);
        return response()->json([
            'success' => true, 
            'redirect' => route('manufacturing-team.dashboard')
        ]);
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