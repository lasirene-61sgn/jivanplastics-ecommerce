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
                           style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; box-sizing: border-box;">
                </div>
                
                <div style="margin-bottom: 1rem;">
                    <label for="password" style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #4b5563;">Password</label>
                    <!-- Relative container for positioning the toggle icon inside the input field -->
                    <div style="position: relative;">
                        <input type="password" id="password" name="password" required 
                               style="width: 100%; padding: 0.75rem 2.5rem 0.75rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; box-sizing: border-box;">
                        
                        <!-- Toggle Button -->
                        <button type="button" id="togglePassword" 
                                style="position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; padding: 0; display: flex; align-items: center; color: #6b7280;">
                            <!-- Eye Icon (Show) -->
                            <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <!-- Eye Slash Icon (Hide) -->
                            <svg id="eyeSlashIcon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="display: none;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.02 10.02 0 013.98-.863c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m-3.123 3.123L2.22 2.22l19.56 19.56" />
                            </svg>
                        </button>
                    </div>
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
            
            <!-- <div style="margin-top: 1rem; text-align: center;">
                <p style="color: #6b7280; font-size: 0.875rem;">
                    For B2B customers, please <a href="{{ route('admin.customers.create') }}" style="color: #10b981; text-decoration: none;">register as a dealer</a>
                </p>
            </div> -->
        </div>
    </div>

    <!-- JavaScript to Handle Toggle Functionality -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const togglePasswordButton = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            const eyeSlashIcon = document.getElementById('eyeSlashIcon');

            togglePasswordButton.addEventListener('click', function () {
                // Toggle the type attribute between 'password' and 'text'
                const isPassword = passwordInput.getAttribute('type') === 'password';
                passwordInput.setAttribute('type', isPassword ? 'text' : 'password');

                // Toggle visibility of the SVG icons
                if (isPassword) {
                    eyeIcon.style.display = 'none';
                    eyeSlashIcon.style.display = 'block';
                } else {
                    eyeIcon.style.display = 'block';
                    eyeSlashIcon.style.display = 'none';
                }
            });
        });
    </script>
@endsection