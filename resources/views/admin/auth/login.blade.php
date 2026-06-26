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

            background: linear-gradient(135deg,
                    #667eea 0%,
                    #764ba2 100%);

            font-family: 'Segoe UI', sans-serif;
        }

        .login-card {
            width: 100%;
            max-width: 450px;

            background: rgba(255, 255, 255, 0.15);

            backdrop-filter: blur(15px);

            border-radius: 20px;

            box-shadow:
                0 8px 32px rgba(0, 0, 0, .2);

            border: 1px solid rgba(255, 255, 255, .2);

            padding: 40px;
        }

        .logo-circle {
            width: 90px;
            height: 90px;

            margin: auto;

            border-radius: 50%;

            background: white;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 40px;

            color: #667eea;
        }

        .title {
            text-align: center;
            color: white;
            font-weight: bold;
            margin-top: 20px;
        }

        .subtitle {
            text-align: center;
            color: #e5e5e5;
            margin-bottom: 30px;
        }

        .form-control {
            border-radius: 12px;
            height: 50px;
        }

        .input-group-text {
            border-radius: 12px 0 0 12px;
        }

        .btn-login {
            height: 50px;
            border-radius: 12px;

            background: #fff;
            color: #667eea;

            font-weight: 600;
            transition: .3s;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            background: #f5f5f5;
        }

        .form-check-label {
            color: white;
        }

        a {
            color: white;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
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
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.post') }}">

            @csrf

            <div class="mb-3">

                <label class="text-white mb-2">
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
