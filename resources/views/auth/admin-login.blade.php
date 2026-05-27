<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login - {{ config('app.name', 'Laravel') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    
    <!-- Styles -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <style>
             * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Instrument Sans', sans-serif;
            background: #ffffff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            width: 100%;
            max-width: 450px;
            margin: 0 auto;
        }

        .login-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid #f0f0f0;
        }

        .logo-container {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo {
            font-size: 42px;
            font-weight: 700;
            letter-spacing: -1px;
        }

        .logo-red {
            color: #DC2626;
        }

        .logo-black {
            color: #000000;
        }

        .logo-subtitle {
            color: #6B7280;
            font-size: 14px;
            margin-top: 8px;
            font-weight: 400;
        }

        .login-title {
            font-size: 28px;
            font-weight: 600;
            color: #111827;
            margin-bottom: 10px;
            text-align: center;
        }

        .login-subtitle {
            text-align: center;
            color: #6B7280;
            font-size: 14px;
            margin-bottom: 30px;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-error {
            background-color: #FEE2E2;
            border: 1px solid #FECACA;
            color: #991B1B;
        }

        .alert ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .alert li {
            margin: 4px 0;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #374151;
            margin-bottom: 8px;
        }

        .form-input {
            width: 100%;
            padding: 12px 16px;
            font-size: 15px;
            border: 2px solid #E5E7EB;
            border-radius: 8px;
            outline: none;
            transition: all 0.3s ease;
            font-family: 'Instrument Sans', sans-serif;
            background: #ffffff;
        }

        .form-input:focus {
            border-color: #DC2626;
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
        }

        .form-input.is-invalid {
            border-color: #EF4444;
        }

        .error-message {
            color: #DC2626;
            font-size: 13px;
            margin-top: 6px;
            display: block;
        }

        .btn {
            width: 100%;
            padding: 14px 24px;
            font-size: 16px;
            font-weight: 600;
            color: #ffffff;
            background: linear-gradient(135deg, #DC2626 0%, #991B1B 100%);
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Instrument Sans', sans-serif;
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
        }

        .btn:hover {
            background: linear-gradient(135deg, #B91C1C 0%, #7F1D1D 100%);
            box-shadow: 0 6px 16px rgba(220, 38, 38, 0.4);
            transform: translateY(-2px);
        }

        .btn:active {
            transform: translateY(0);
            box-shadow: 0 2px 8px rgba(220, 38, 38, 0.3);
        }

        .forgot-password {
            text-align: center;
            margin-top: 16px;
        }

        .forgot-password a {
            color: #DC2626;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .forgot-password a:hover {
            color: #991B1B;
            text-decoration: underline;
        }

        /* Responsive Design */
        @media (max-width: 576px) {
            .login-card {
                padding: 30px 24px;
                border-radius: 12px;
            }

            .logo {
                font-size: 36px;
            }

            .login-title {
                font-size: 24px;
            }

            .form-input {
                padding: 11px 14px;
                font-size: 14px;
            }

            .btn {
                padding: 12px 20px;
                font-size: 15px;
            }
        }

        @media (max-width: 400px) {
            body {
                padding: 15px;
            }

            .login-card {
                padding: 24px 20px;
            }

            .logo {
                font-size: 32px;
            }

            .login-title {
                font-size: 22px;
            }
        }

        /* Tablet landscape and small desktops */
        @media (min-width: 768px) and (max-width: 1024px) {
            .login-container {
                max-width: 480px;
            }
        }

        /* Large screens */
        @media (min-width: 1440px) {
            .login-container {
                max-width: 500px;
            }

            .login-card {
                padding: 50px;
            }
        }

        /* Animation for form */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-card {
            animation: slideIn 0.5s ease-out;
        }
        </style>
    @endif
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <h2 class="login-title">Admin Login</h2>
            
            @if ($errors->any())
                <div class="alert alert-error">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            @if (session('error'))
                <div class="alert alert-error">
                    {{ session('error') }}
                </div>
            @endif
            
            <form method="POST" action="{{ route('admin.login') }}">
                @csrf
                
                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input 
                        id="email" 
                        type="email" 
                        class="form-input @error('email') is-invalid @enderror" 
                        name="email" 
                        value="{{ old('email') }}" 
                        required 
                        autocomplete="email" 
                        autofocus
                    >
                    @error('email')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input 
                        id="password" 
                        type="password" 
                        class="form-input @error('password') is-invalid @enderror" 
                        name="password" 
                        required 
                        autocomplete="current-password"
                    >
                    @error('password')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn">
                        Login
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>