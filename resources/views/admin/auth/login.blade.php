<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Login</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" rel="stylesheet">

    <style>
        body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;

            background: #f4f6f9;
            font-family: 'Segoe UI', sans-serif;
        }

        .login-card {
            width: 100%;
            max-width: 430px;

            background: #fff;

            border-radius: 16px;

            box-shadow: 0 8px 30px rgba(0, 0, 0, .08);

            padding: 45px;
        }

        .logo-circle {
            width: 75px;
            height: 75px;

            margin: auto;

            border-radius: 18px;

            background: lightgray;

            display: flex;
            align-items: center;
            justify-content: center;

            color: white;

            font-size: 30px;
        }

        .title {
            color: #212529;
            text-align: center;
            font-weight: 700;
            margin-top: 20px;
        }

        .subtitle {
            text-align: center;
            color: #6c757d;
            margin-bottom: 35px;
        }

        .form-control {

            height: 52px;

            border-radius: 10px;

            border: 1px solid #dee2e6;

            box-shadow: none;

        }

        .form-control:focus {

            border-color: #0d6efd;

            box-shadow: 0 0 0 .2rem rgba(13, 110, 253, .15);

        }

        .input-group-text {

            background: #f8f9fa;

            border: 1px solid #dee2e6;

            border-right: none;

            color: #6c757d;
        }

        .btn-login {

            height: 52px;

            border-radius: 10px;

            background: lightgray;

            color: black;

            font-weight: 600;

            transition: .25s;

        }

        .btn-login:hover {

            background: gray;

            transform: translateY(-2px);

        }

        .form-check-label {
            color: white;
        }

        a {

            color: gray;

            text-decoration: none;

        }

        a:hover {

            color: gray;

        }

        .text-danger {
            color: #ffb3b3 !important;
        }
    </style>

</head>

<body>

    <div class="login-card">

        <div class="logo-circle">
            <i class="fa-solid fa-user-shield"></i>
        </div>

        <h2 class="title">
            Admin Portal
        </h2>

        <p class="subtitle">
            Sign in to manage your store
        </p>

        @if (session('error'))
            <div class="alert alert-danger rounded-3">

                <i class="fa-solid fa-circle-exclamation me-2"></i>

                {{ session('error') }}

            </div>
        @endif
        <form method="POST" action="{{ route('admin.login.post') }}">

            @csrf

            <div class="mb-3">

                <label class="fw-semibold text-dark">
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

                <label class="text-white mb-2">
                    Password
                </label>

                <div class="input-group">

                    <span class="input-group-text">
                        <i class="fa-solid fa-lock"></i>
                    </span>

                    <input type="password" id="password" name="password" class="form-control"
                        placeholder="Enter password">

                    <button type="button" class="btn btn-light" onclick="togglePassword()">
                        <i class="fa-solid fa-eye"></i>
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

                    <input class="form-check-input" type="checkbox" id="remember">

                    <label class="form-check-label" for="remember">
                        Remember Me
                    </label>

                </div>

                <a href="#">
                    Forgot Password?
                </a>

            </div>

            <button type="submit" class="btn btn-login w-100">
                Login
            </button>
            <div class="text-center mt-4">
                <small class="text-muted">
                    © {{ date('Y') }} Admin Panel
                </small>
            </div>

        </form>

    </div>

    <script>
        function togglePassword() {

            const password =
                document.getElementById('password');

            if (password.type === 'password') {
                password.type = 'text';
            } else {
                password.type = 'password';
            }
        }
    </script>

</body>

</html>
```
