<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Login</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            overflow: hidden;

            margin: 0;
            background: #eef1f8;
            font-family: 'Segoe UI', sans-serif;
        }

        /* Decorative background blobs */
        .bg-blob {
            position: fixed;
            border-radius: 50%;
            background: #cdd9f7;
            opacity: .55;
            z-index: 0;
        }

        .bg-blob.top-left {
            width: 420px;
            height: 420px;
            top: -160px;
            left: -160px;
        }

        .bg-blob.bottom-right {
            width: 380px;
            height: 380px;
            bottom: -150px;
            right: -150px;
        }

        /* Decorative dot grid */
        .bg-dots {
            position: fixed;
            top: 40px;
            right: 60px;
            width: 160px;
            height: 160px;
            background-image: radial-gradient(#b9c5e6 1.6px, transparent 1.6px);
            background-size: 18px 18px;
            opacity: .8;
            z-index: 0;
        }

        .bg-dots.bottom {
            top: auto;
            right: auto;
            bottom: 40px;
            left: 40px;
        }

        .login-card {
            position: relative;
            z-index: 1;

            width: 100%;
            max-width: 430px;

            background: #fff;

            border-radius: 22px;

            box-shadow: 0 20px 50px rgba(31, 60, 136, .12);

            padding: 45px;
        }

        .logo-circle {
            width: 88px;
            height: 88px;

            margin: auto;

            border-radius: 50%;

            background: #fff;
            box-shadow: 0 6px 18px rgba(31, 60, 136, .12);

            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo-circle .logo-inner {
            width: 64px;
            height: 64px;
            border-radius: 16px;

            background: linear-gradient(135deg, #4f7df9, #2f5bd9);

            display: flex;
            align-items: center;
            justify-content: center;

            color: #fff;
            font-size: 26px;
        }

        .title {
            color: #1b2430;
            text-align: center;
            font-weight: 800;
            margin-top: 22px;
            margin-bottom: 4px;
        }

        .subtitle {
            text-align: center;
            color: #6c757d;
            margin-bottom: 22px;
        }

        .divider-dot {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 22px;
        }

        .divider-dot::before,
        .divider-dot::after {
            content: "";
            flex: 1;
            height: 1px;
            background: #e5e9f2;
        }

        .divider-dot span {
            width: 6px;
            height: 6px;
            margin: 0 10px;
            border-radius: 50%;
            background: #2f5bd9;
            flex-shrink: 0;
        }

        label.field-label {
            font-weight: 600;
            color: #1b2430;
            margin-bottom: 8px;
            display: inline-block;
        }

        .form-control {

            height: 52px;

            border-radius: 12px;

            border: 1px solid #e2e6ee;

            box-shadow: none;

        }

        .form-control:focus {

            border-color: #4f7df9;

            box-shadow: 0 0 0 .2rem rgba(79, 125, 249, .15);

        }

        .input-group-text {

            background: #f3f5fa;

            border: 1px solid #e2e6ee;

            border-right: none;

            border-radius: 12px 0 0 12px;

            color: #6c757d;
        }

        .input-group .form-control {
            border-left: none;
            border-radius: 0 12px 12px 0;
        }

        .input-group .btn-light {
            border: 1px solid #e2e6ee;
            border-left: none;
            border-radius: 0 12px 12px 0;
            background: #fff;
            color: #6c757d;
        }

        .input-group .form-control:not(:last-child) {
            border-radius: 0;
        }

        .btn-login {

            height: 52px;

            border-radius: 12px;

            background: linear-gradient(135deg, #4f7df9, #2f5bd9);

            color: #fff;

            font-weight: 700;

            border: none;

            transition: .25s;

            box-shadow: 0 8px 20px rgba(47, 91, 217, .3);

        }

        .btn-login:hover {

            background: linear-gradient(135deg, #3f6cf0, #1f4bcb);

            transform: translateY(-2px);

            color: #fff;

        }

        .form-check-label {
            color: #495057;
        }

        a {

            color: #2f5bd9;

            text-decoration: none;

        }

        a:hover {

            color: #1f4bcb;
            text-decoration: underline;

        }

        .text-danger {
            color: #dc3545 !important;
        }

        .footer-note {
            text-align: center;
            border-top: 1px solid #eef0f5;
            margin-top: 28px;
            padding-top: 20px;
        }

        .footer-note .secure-line {
            color: #6c757d;
            font-weight: 600;
            font-size: .85rem;
            margin-bottom: 4px;
        }

        .footer-note .secure-line i {
            color: #2f5bd9;
            margin-right: 6px;
        }
    </style>

</head>

<body>

    <div class="bg-blob top-left"></div>
    <div class="bg-blob bottom-right"></div>
    <div class="bg-dots"></div>
    <div class="bg-dots bottom"></div>

    <div class="login-card">

        <div class="logo-circle">
            <div class="logo-inner">
                <i class="fa-solid fa-user-shield"></i>
            </div>
        </div>

        <h2 class="title">
            Admin Portal
        </h2>

        <p class="subtitle">
            Sign in to manage your store
        </p>

        <div class="divider-dot">
            <span></span>
        </div>

        @if (session('error'))
            <div class="alert alert-danger rounded-3">

                <i class="fa-solid fa-circle-exclamation me-2"></i>

                {{ session('error') }}

            </div>
        @endif
        <form method="POST" action="{{ route('admin.login.post') }}">

            @csrf

            <div class="mb-3">

                <label class="field-label">
                    Email Address
                </label>

                <div class="input-group">

                    <span class="input-group-text">
                        <i class="fa-solid fa-envelope"></i>
                    </span>

                    <input type="email" name="email" class="form-control" placeholder="Enter your email"
                        value="{{ old('email') }}">

                </div>

                @error('email')
                    <small class="text-danger">
                        {{ $message }}
                    </small>
                @enderror

            </div>

            <div class="mb-3">

                <label class="field-label">
                    Password
                </label>

                <div class="input-group">

                    <span class="input-group-text">
                        <i class="fa-solid fa-lock"></i>
                    </span>

                    <input type="password" id="password" name="password" class="form-control"
                        placeholder="Enter your password">

                    <button type="button" class="btn btn-light" onclick="togglePassword()">
                        <i class="fa-solid fa-eye" id="toggleIcon"></i>
                    </button>

                </div>

                @error('password')
                    <small class="text-danger">
                        {{ $message }}
                    </small>
                @enderror

            </div>

            <div class="d-flex justify-content-between mb-4">

                <div class="form-check">

                    <input class="form-check-input" type="checkbox" id="remember" name="remember">

                    <label class="form-check-label" for="remember">
                        Remember Me
                    </label>

                </div>

                <a href="#">
                    Forgot Password?
                </a>

            </div>

            <button type="submit" class="btn btn-login w-100">
                <i class="fa-solid fa-lock me-2"></i>Login
            </button>

        </form>

        <div class="footer-note">
            <div class="secure-line">
                <i class="fa-solid fa-shield-halved"></i>Secure Admin Access
            </div>
            <small class="text-muted">
                © {{ date('Y') }} Admin Panel. All rights reserved.
            </small>
        </div>

    </div>

    <script>
        function togglePassword() {

            const password = document.getElementById('password');
            const icon = document.getElementById('toggleIcon');

            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>

</body>

</html>
