@extends('frontend.layouts.app')

@section('title', 'Register - E-Commerce Store')

@section('content')
    <div style="max-width: 400px; margin: 0 auto; padding: 2rem 0;">
        <div style="background-color: white; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); padding: 2rem;">
            <h1 class="section-title" style="text-align: center;">Register</h1>
            
            <form method="POST" action="{{ route('register') }}">
                @csrf
                
                <div style="margin-bottom: 1rem;">
                    <label for="name" style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #4b5563;">Full Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required 
                           style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
                </div>
                
                <div style="margin-bottom: 1rem;">
                    <label for="email" style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #4b5563;">Email Address</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required 
                           style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
                </div>
                
                <div style="margin-bottom: 1rem;">
                    <label for="password" style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #4b5563;">Password</label>
                    <input type="password" id="password" name="password" required 
                           style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
                </div>
                
                <div style="margin-bottom: 1rem;">
                    <label for="password_confirmation" style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #4b5563;">Confirm Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required 
                           style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
                </div>
                
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #4b5563;">Customer Type</label>
                    <div style="display: flex; gap: 1rem;">
                        <label style="display: flex; align-items: center;">
                            <input type="radio" name="customer_type" value="individual" {{ (isset($customerType) && $customerType == 'individual') || old('customer_type', 'individual') == 'individual' ? 'checked' : '' }} style="margin-right: 0.5rem;">
                            <span style="color: #1f2937;">Individual Customer</span>
                        </label>
                        <label style="display: flex; align-items: center;">
                            <input type="radio" name="customer_type" value="dealer" {{ (isset($customerType) && $customerType == 'dealer') || old('customer_type') == 'dealer' ? 'checked' : '' }} style="margin-right: 0.5rem;">
                            <span style="color: #1f2937;">Business Dealer</span>
                        </label>
                    </div>
                </div>
                
                <button type="submit" class="btn" style="width: 100%;">Register</button>
            </form>
            
            <div style="margin-top: 1.5rem; text-align: center;">
                <p style="color: #6b7280;">
                    Already have an account? <a href="{{ route('login') }}" style="color: #3b82f6; text-decoration: none;">Login here</a>
                </p>
            </div>
        </div>
    </div>
@endsection