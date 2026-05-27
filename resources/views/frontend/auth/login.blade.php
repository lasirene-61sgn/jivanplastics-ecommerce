@extends('frontend.layouts.app')

@section('title', 'Login - E-Commerce Store')

@section('content')
    <div style="max-width: 400px; margin: 0 auto; padding: 2rem 0;">
        <div style="background-color: white; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); padding: 2rem;">
            <h1 class="section-title" style="text-align: center;">Login</h1>
            
            <form method="POST" action="{{ route('login') }}">
                @csrf
                
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
                
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: flex; align-items: center;">
                        <input type="checkbox" name="remember" style="margin-right: 0.5rem;">
                        <span style="color: #6b7280;">Remember me</span>
                    </label>
                </div>
                
                <button type="submit" class="btn" style="width: 100%;">Login</button>
            </form>
            
            <div style="margin-top: 1.5rem; text-align: center;">
                <p style="color: #6b7280;">
                    Don't have an account? <a href="{{ route('register') }}" style="color: #3b82f6; text-decoration: none;">Register here</a>
                </p>
            </div>
            
            <div style="margin-top: 1rem; text-align: center;">
                <p style="color: #6b7280; font-size: 0.875rem;">
                    For B2B customers, please <a href="{{ route('admin.customers.create') }}" style="color: #10b981; text-decoration: none;">register as a dealer</a>
                </p>
            </div>
        </div>
    </div>
@endsection