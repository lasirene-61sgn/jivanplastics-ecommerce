<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Manufacturing Team Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .login-card { max-width: 420px; margin: 80px auto; padding: 25px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .hidden { display: none; }
    </style>
</head>
<body class="bg-light">

<div class="container">
    <div class="card login-card">
        <h4 class="text-center mb-4">Manufacturing Login</h4>

        <div id="alert-message" class="alert hidden" role="alert"></div>

        <!-- STEP 1: Phone Number Input -->
        <form id="step-phone-form">
            <div class="mb-3">
                <label for="phone" class="form-label">Mobile Number</label>
                <input type="text" class="form-control" id="phone" name="phone" placeholder="Enter mobile number" required>
            </div>
            <button type="submit" class="btn btn-primary w-100" id="btn-check-phone">Continue</button>
        </form>

        <!-- STEP 2A: Password Login (Returning Users) -->
        <form id="step-password-form" class="hidden">
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="login_password" name="password" placeholder="Enter your password" required>
            </div>
            <button type="submit" class="btn btn-success w-100" id="btn-login-pass">Login</button>
            <button type="button" class="btn btn-link w-100 mt-2 btn-back">Back</button>
        </form>

        <!-- STEP 2B: OTP + Set Password Form (First-Time Users) -->
        <form id="step-otp-form" class="hidden">
            <div class="mb-3">
                <label for="otp" class="form-label">Enter OTP</label>
                <input type="text" class="form-control" id="otp" name="otp" placeholder="6-digit OTP" required>
            </div>
            <div class="mb-3">
                <label for="new_password" class="form-label">Set New Password</label>
                <input type="password" class="form-control" id="new_password" name="password" placeholder="Minimum 6 characters" required>
            </div>
            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Re-enter password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100" id="btn-verify-otp">Set Password & Login</button>
            <button type="button" class="btn btn-link w-100 mt-2 btn-back">Back</button>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    let currentPhone = '';

    function showAlert(type, msg) {
        $('#alert-message').removeClass('hidden alert-danger alert-success')
            .addClass('alert-' + type).text(msg);
    }

    // Step 1: Check Phone
    $('#step-phone-form').on('submit', function(e) {
        e.preventDefault();
        currentPhone = $('#phone').val();

        $.post("{{ route('manufacturing-team.check-phone') }}", { phone: currentPhone })
            .done(function(res) {
                if(res.success) {
                    $('#step-phone-form').addClass('hidden');
                    showAlert('success', res.message);
                    
                    if(res.has_password) {
                        $('#step-password-form').removeClass('hidden');
                    } else {
                        $('#step-otp-form').removeClass('hidden');
                    }
                } else {
                    showAlert('danger', res.message);
                }
            })
            .fail(function() { showAlert('danger', 'Error processing request.'); });
    });

    // Step 2A: Direct Login with Password
    $('#step-password-form').on('submit', function(e) {
        e.preventDefault();
        
        $.post("{{ route('manufacturing-team.login-password') }}", {
            phone: currentPhone,
            password: $('#login_password').val()
        })
        .done(function(res) {
            if(res.success) {
                window.location.href = res.redirect;
            } else {
                showAlert('danger', res.message);
            }
        });
    });

    // Step 2B: Verify OTP & Save Password
    $('#step-otp-form').on('submit', function(e) {
        e.preventDefault();

        $.post("{{ route('manufacturing-team.verify-otp-password') }}", {
            phone: currentPhone,
            otp: $('#otp').val(),
            password: $('#new_password').val(),
            password_confirmation: $('#password_confirmation').val()
        })
        .done(function(res) {
            if(res.success) {
                window.location.href = res.redirect;
            } else {
                showAlert('danger', res.message);
            }
        })
        .fail(function(xhr) {
            let msg = xhr.responseJSON?.message || 'Verification failed.';
            showAlert('danger', msg);
        });
    });

    // Reset back to phone input
    $('.btn-back').on('click', function() {
        $('#step-password-form, #step-otp-form').addClass('hidden');
        $('#step-phone-form').removeClass('hidden');
        $('#alert-message').addClass('hidden');
    });
</script>

</body>
</html>