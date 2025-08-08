<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Study Material App</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: url('/assets/image/login.jpg') no-repeat center center/cover;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Poppins', sans-serif;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0px 8px 32px rgba(0, 0, 0, 0.2);
        }

        .form-control {
            border-radius: 10px;
            padding: 10px;
            font-size: 16px;
        }

        .btn-primary {
            width: 100%;
            border-radius: 10px;
            font-size: 18px;
            font-weight: bold;
            background: #007bff;
            border: none;
        }

        .btn-primary:hover {
            background: #0056b3;
        }

        .forgot-password {
            color: #fff;
            text-align: center;
            display: block;
            margin-top: 10px;
        }
    </style>
</head>

<body>

    <div class="login-container text-white">
        <div class="text-center mb-4">
            <img src="/assets/image/Ramanujan_College_Logo.png" alt="Logo" style="height: 80px;">
            <h3 class="mt-2">Welcome Back!</h3>

        </div>
        @if ($errors->has('email'))
            <div class="alert alert-danger text-center">
                {{ $errors->first('email') }}
            </div>
        @endif
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required
                    placeholder="Enter your email">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required
                    placeholder="Enter your password">
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
            <a href="{{ route('password.request') }}" class="forgot-password">Forgot your password?</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
