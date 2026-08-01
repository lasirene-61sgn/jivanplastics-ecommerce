<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login - {{ config('app.name', 'Laravel') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    
    <!-- Tailwind CSS (CDN Fallback) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Styles via Vite -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <style>
        body {
            font-family: 'Instrument Sans', sans-serif;
        }
    </style>
</head>
<body class="bg-white min-h-screen flex items-center justify-center p-4 sm:p-5">

    <div class="w-full max-width-[450px] max-w-md mx-auto">
        <div class="bg-white rounded-2xl p-6 sm:p-10 shadow-[0_10px_40px_rgba(0,0,0,0.1)] border border-gray-100 transition-all duration-300">
            
            <h2 class="text-2xl sm:text-3xl font-semibold text-gray-900 text-center mb-6 sm:mb-8">Admin Login</h2>
            
            @if ($errors->any())
                <div class="bg-red-100 border border-red-200 text-red-800 p-3 sm:p-4 rounded-lg mb-5 text-sm">
                    <ul class="list-none m-0 p-0">
                        @foreach ($errors->all() as $error)
                            <li class="my-1">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            @if (session('error'))
                <div class="bg-red-100 border border-red-200 text-red-800 p-3 sm:p-4 rounded-lg mb-5 text-sm">
                    {{ session('error') }}
                </div>
            @endif
            
            <form method="POST" action="{{ route('admin.login') }}">
                @csrf
                
                <!-- Email Address -->
                <div class="mb-6">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                    <input 
                        id="email" 
                        type="email" 
                        class="w-full px-4 py-3 text-base border-2 rounded-lg outline-none transition-all duration-300 bg-white text-gray-900 focus:border-red-600 focus:ring-2 focus:ring-red-600/10 @error('email') border-red-500 @else border-gray-200 @enderror" 
                        name="email" 
                        value="{{ old('email') }}" 
                        required 
                        autocomplete="email" 
                        autofocus
                    >
                    @error('email')
                        <span class="text-red-600 text-xs mt-1.5 block">{{ $message }}</span>
                    @enderror
                </div>
                
                <!-- Password with Toggle -->
                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <div class="relative">
                        <input 
                            id="password" 
                            type="password" 
                            class="w-full pl-4 pr-10 py-3 text-base border-2 rounded-lg outline-none transition-all duration-300 bg-white text-gray-900 focus:border-red-600 focus:ring-2 focus:ring-red-600/10 @error('password') border-red-500 @else border-gray-200 @enderror" 
                            name="password" 
                            required 
                            autocomplete="current-password"
                        >
                        <!-- Show / Hide Button -->
                        <button type="button" id="togglePassword" class="absolute right-3 top-1/2 -translate-y-1/2 bg-transparent border-0 cursor-pointer p-0 flex items-center text-gray-500 hover:text-gray-700 focus:outline-none">
                            <!-- Eye Icon -->
                            <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <!-- Eye Slash Icon -->
                            <svg id="eyeSlashIcon" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.02 10.02 0 013.98-.863c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m-3.123 3.123L2.22 2.22l19.56 19.56" />
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <span class="text-red-600 text-xs mt-1.5 block">{{ $message }}</span>
                    @enderror
                </div>
                
                <!-- Submit Button -->
                <div class="mb-2">
                    <button type="submit" class="w-full py-3.5 px-6 text-base font-semibold text-white bg-gradient-to-r from-red-600 to-red-800 rounded-lg cursor-pointer transition-all duration-300 shadow-[0_4px_12px_rgba(220,38,38,0.3)] hover:from-red-700 hover:to-red-900 hover:shadow-[0_6px_16px_rgba(220,38,38,0.4)] hover:-translate-y-0.5 active:translate-y-0 active:shadow-[0_2px_8px_rgba(220,38,38,0.3)]">
                        Login
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- Password Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            const eyeSlashIcon = document.getElementById('eyeSlashIcon');

            if (togglePassword && passwordInput) {
                togglePassword.addEventListener('click', function () {
                    const isPassword = passwordInput.getAttribute('type') === 'password';
                    passwordInput.setAttribute('type', isPassword ? 'text' : 'password');

                    eyeIcon.classList.toggle('hidden', isPassword);
                    eyeSlashIcon.classList.toggle('hidden', !isPassword);
                });
            }
        });
    </script>
</body>
</html>