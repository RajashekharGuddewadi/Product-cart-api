<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Register - ShopHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .register-card {
            width: 100%;
            max-width: 480px;
            border: none;
            border-radius: 15px;
            box-shadow: 0 15px 50px rgba(0,0,0,0.3);
        }
        .register-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0;
            padding: 35px 25px;
            text-align: center;
        }
        .register-header h3 {
            margin: 0;
            font-weight: 700;
            font-size: 1.8rem;
        }
        .register-body {
            padding: 35px 30px;
            background: white;
        }
        .btn-register {
            width: 100%;
            padding: 13px;
            font-weight: 600;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            font-size: 1.1rem;
        }
        .btn-register:hover {
            opacity: 0.9;
        }
        .form-control {
            padding: 10px 15px;
            border-radius: 8px;
        }
        .form-label {
            font-weight: 600;
            color: #333;
        }
        .divider {
            text-align: center;
            margin: 20px 0;
            position: relative;
        }
        .divider::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            width: 100%;
            height: 1px;
            background: #dee2e6;
        }
        .divider span {
            background: white;
            padding: 0 15px;
            position: relative;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="register-card">
        <div class="register-header">
            <h3><i class="bi bi-person-plus"></i> Create Account</h3>
            <p class="mb-0 opacity-75">Join us today and start shopping</p>
        </div>
        
        <div class="register-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf
                
                <div class="mb-3">
                    <label for="name" class="form-label"><i class="bi bi-person"></i> Full Name</label>
                    <input type="text" name="name" id="name" class="form-control" 
                           value="{{ old('name') }}" required autofocus>
                    @error('name')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="email" class="form-label"><i class="bi bi-envelope"></i> Email Address</label>
                    <input type="email" name="email" id="email" class="form-control" 
                           value="{{ old('email') }}" required>
                    @error('email')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label"><i class="bi bi-lock"></i> Password</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                    @error('password')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-4">
                    <label for="password_confirmation" class="form-label"><i class="bi bi-lock-fill"></i> Confirm Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                    @error('password_confirmation')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>
                
                <button type="submit" class="btn btn-primary btn-register">
                    <i class="bi bi-person-check"></i> Register
                </button>
            </form>
            
            <div class="divider"><span>OR</span></div>
            
            <div class="text-center">
                <p class="mb-0">Already have an account?</p>
                <a href="{{ route('login') }}" class="btn btn-outline-primary mt-2">
                    <i class="bi bi-box-arrow-in-right"></i> Login Here
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

