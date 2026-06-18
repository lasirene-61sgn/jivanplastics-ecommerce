<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manufacturing Team Login - E-Commerce Store</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 450px;
            padding: 40px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header h1 {
            color: #333;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .login-header p {
            color: #666;
            font-size: 16px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 14px;
        }

        .form-group input {
            width: 100%;
            padding: 15px;
            border: 2px solid #e1e5e9;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .form-group input:disabled {
            background: #e9ecef;
            cursor: not-allowed;
            color: #6c757d;
        }

        .btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .btn:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }

        .btn:active:not(:disabled) {
            transform: translateY(0);
        }
        
        .btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            display: none;
        }

        .alert-error {
            background: #fee;
            border: 1px solid #fcc;
            color: #c33;
        }
        
        .alert-success {
            background: #e6ffed;
            border: 1px solid #b7ebc6;
            color: #1a7f37;
        }

        .back-link {
            text-align: center;
            margin-top: 25px;
        }

        .back-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .back-link a:hover {
            color: #764ba2;
            text-decoration: underline;
        }

        #otp-section {
            display: none;
        }

        .spinner {
            border: 3px solid rgba(255,255,255,0.3);
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border-left-color: #fff;
            animation: spin 1s linear infinite;
            display: none;
            margin-right: 10px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 30px 20px;
            }
            
            .login-header h1 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>Manufacturing Team Login</h1>
            <p>Verify your mobile number to sign in.</p>
        </div>

        <div id="alert-box" class="alert"></div>

        <form id="login-form" onsubmit="return false;">
            <div class="form-group" id="phone-group">
                <label for="phone">Mobile Number</label>
                <input type="text" id="phone" name="phone" placeholder="Enter your 10-digit mobile number" required autofocus maxlength="15">
            </div>

            <div id="otp-section">
                <div class="form-group">
                    <label for="otp">One-Time Password (OTP)</label>
                    <input type="text" id="otp" name="otp" placeholder="Enter the 6-digit OTP" maxlength="6">
                </div>
            </div>

            <button type="button" id="main-btn" class="btn" onclick="handleAction()">
                <div class="spinner" id="btn-spinner"></div>
                <span id="btn-text">Send OTP</span>
            </button>
        </form>

        <div class="back-link">
            <a href="{{ route('home') }}">← Back to Home</a>
        </div>
    </div>

    <script>
        let currentStep = 'phone'; // 'phone' or 'otp'
        
        function showAlert(message, type) {
            const alertBox = document.getElementById('alert-box');
            alertBox.textContent = message;
            alertBox.className = 'alert alert-' + type;
            alertBox.style.display = 'block';
        }

        function hideAlert() {
            document.getElementById('alert-box').style.display = 'none';
        }

        function setLoading(isLoading) {
            const btn = document.getElementById('main-btn');
            const spinner = document.getElementById('btn-spinner');
            
            if (isLoading) {
                btn.disabled = true;
                spinner.style.display = 'block';
            } else {
                btn.disabled = false;
                spinner.style.display = 'none';
            }
        }

        async function handleAction() {
            if (currentStep === 'phone') {
                await sendOtp();
            } else {
                await verifyOtp();
            }
        }

        async function sendOtp() {
            hideAlert();
            const phone = document.getElementById('phone').value.trim();
            
            if (!phone) {
                showAlert('Please enter your mobile number.', 'error');
                return;
            }

            setLoading(true);

            try {
                const response = await fetch("{{ route('manufacturing-team.send-otp') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ phone: phone })
                });

                if (response.status === 419) {
                    showAlert('Your session has expired. Please refresh the page and try again.', 'error');
                    return;
                }

                const data = await response.json();

                if (data.success) {
                    showAlert(data.message, 'success');
                    
                    // Transition to OTP step
                    currentStep = 'otp';
                    document.getElementById('phone').disabled = true;
                    document.getElementById('otp-section').style.display = 'block';
                    document.getElementById('btn-text').textContent = 'Verify & Login';
                    document.getElementById('otp').focus();
                } else {
                    showAlert(data.message || 'Something went wrong.', 'error');
                }
            } catch (error) {
                showAlert('Network error. Please try again.', 'error');
            } finally {
                setLoading(false);
            }
        }

        async function verifyOtp() {
            hideAlert();
            const phone = document.getElementById('phone').value.trim();
            const otp = document.getElementById('otp').value.trim();
            
            if (!otp) {
                showAlert('Please enter the OTP.', 'error');
                return;
            }

            setLoading(true);

            try {
                const response = await fetch("{{ route('manufacturing-team.verify-otp') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ phone: phone, otp: otp })
                });

                if (response.status === 419) {
                    showAlert('Your session has expired. Please refresh the page and try again.', 'error');
                    setLoading(false);
                    return;
                }

                const data = await response.json();

                if (data.success) {
                    showAlert('Verification successful. Redirecting...', 'success');
                    window.location.href = data.redirect;
                } else {
                    showAlert(data.message || 'Invalid OTP.', 'error');
                    setLoading(false);
                }
            } catch (error) {
                showAlert('Network error. Please try again.', 'error');
                setLoading(false);
            }
        }
        
        // Handle enter key press
        document.getElementById('login-form').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                handleAction();
            }
        });
    </script>
</body>
</html>