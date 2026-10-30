<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Show the login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('frontend.auth.login');
    }
    
    /**
     * Handle login request.
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
        
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // Check customer type and record login
            $customer = Customer::where('email', Auth::user()->email)->first();
            if ($customer) {
                $customer->recordLogin('Web', $request);
            }

            if ($customer && $customer->customer_type === 'dealer') {
                return redirect()->intended('/b2b/dashboard');
            }
            
            return redirect()->intended('/b2c/dashboard');
        }
        
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }
    
    /**
     * Show the registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm(Request $request)
    {
        $customerType = $request->query('type', 'individual');
        return view('frontend.auth.register', compact('customerType'));
    }
    
    /**
     * Handle registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'customer_type' => 'required|in:individual,dealer',
        ]);
        
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        
        // Create customer record
        $customer = Customer::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Store password for API authentication
            'customer_type' => $request->customer_type,
            'is_active' => $request->customer_type === 'individual' ? true : false, // Dealers need admin approval
        ]);
        
        Auth::login($user);
        $customer->recordLogin('Registration', $request);
        
        // Redirect based on customer type
        if ($request->customer_type === 'dealer') {
            return redirect()->route('b2b.dashboard')->with('success', 'Registration successful! Your account is pending admin approval.');
        }
        
        return redirect()->route('b2c.dashboard')->with('success', 'Registration successful! Welcome to our store.');
    }
    
    /**
     * Handle logout request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $customer = Customer::where('email', $user->email)->first();
            if ($customer) {
                ActivityLog::create([
                    'customer_id' => $customer->id,
                    'user_type' => $customer->customer_type === 'dealer' ? 'b2b' : 'b2c',
                    'action' => 'logout',
                    'model_type' => 'Customer',
                    'model_id' => $customer->id,
                    'details' => [
                        'name' => $customer->name,
                        'email' => $customer->email,
                        'company_name' => $customer->company_name,
                    ]
                ]);
            }
        }

        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }
}
