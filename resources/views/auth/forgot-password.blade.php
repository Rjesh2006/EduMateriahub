<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset</title>

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

        .reset-container {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0px 8px 32px rgba(0, 0, 0, 0.2);
            text-align: center;
            color: white;
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

        .back-to-login {
            color: #fff;
            text-align: center;
            display: block;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="reset-container">
        <div class="text-center mb-4">
            <img src="/assets/image/Ramanujan_College_Logo.jpg" alt="Logo" style="height: 80px;">
            <h3 class="mt-2">Reset Your Password</h3>
            <p>No worries! Enter your email and we’ll send you a link to reset your password.</p>
        </div>
        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required
                    placeholder="Enter your email">
            </div>
            <button type="submit" class="btn btn-primary">Send Reset Link</button>
            <a href="{{ route('login') }}" class="back-to-login">Back to Login</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
