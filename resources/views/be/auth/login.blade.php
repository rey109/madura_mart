<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/png" href="{{asset('storage/images/branding/logo.jpeg')}}">
    <title>Madura Mart - Login</title>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <link href="{{asset('be/assets/css/nucleo-icons.css')}}" rel="stylesheet" />
    <link href="{{asset('be/assets/css/nucleo-svg.css')}}" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link id="pagestyle" href="{{asset('be/assets/css/soft-ui-dashboard.css?v=1.0.7')}}" rel="stylesheet" />
    <script src="{{asset('be/assets/js/plugins/sweetalert.js')}}"></script>
    <link rel="stylesheet" href="{{asset('be/assets/css/sweetalert.css')}}">
    <style>
        body {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: rgba(255,255,255,0.95);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 420px;
            width: 100%;
            padding: 40px;
        }
        .login-card .logo-wrapper {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-card .logo-wrapper img {
            width: 70px;
            height: 70px;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .login-card .logo-wrapper h4 {
            color: #344767;
            margin-top: 12px;
            font-weight: 700;
        }
        .login-card .logo-wrapper p {
            color: #67748e;
            font-size: 14px;
        }
        .login-card .form-label {
            color: #344767;
            font-size: 13px;
            font-weight: 600;
        }
        .login-card .form-control {
            border: 1px solid #d2d6da;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        .login-card .form-control:focus {
            border-color: #cb0c9f;
            box-shadow: 0 0 0 0.2rem rgba(203, 12, 159, 0.15);
        }
        .btn-login {
            background: linear-gradient(310deg, #7928ca, #cb0c9f);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            font-size: 14px;
            color: white;
            width: 100%;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(203, 12, 159, 0.4);
            color: white;
        }
        .input-group-text {
            background: transparent;
            border-right: none;
            border-color: #d2d6da;
            border-radius: 10px 0 0 10px;
            color: #67748e;
        }
        .input-group .form-control {
            border-left: none;
            border-radius: 0 10px 10px 0;
        }
        .input-group:focus-within .input-group-text {
            border-color: #cb0c9f;
        }
        .form-check-input:checked {
            background-color: #cb0c9f;
            border-color: #cb0c9f;
        }
        .alert-danger {
            background: #fff5f5;
            border: 1px solid #feb2b2;
            color: #c53030;
            border-radius: 10px;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="logo-wrapper">
            <img src="{{asset('storage/images/branding/logo.jpeg')}}" alt="Madura Mart Logo">
            <h4>Madura Mart</h4>
            <p>Masukkan email & password untuk login</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger mb-3">
                @foreach ($errors->all() as $error)
                    <div><i class="fas fa-exclamation-circle me-1"></i> {{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('login.process') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Email</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input type="email" name="email" class="form-control" placeholder="Masukkan email..." value="{{ old('email') }}" required autofocus>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" name="password" class="form-control" placeholder="Masukkan password..." required>
                </div>
            </div>
            <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" name="remember" id="rememberMe">
                <label class="form-check-label text-sm" for="rememberMe">Ingat saya</label>
            </div>
            <button type="submit" class="btn btn-login">
                <i class="fas fa-sign-in-alt me-2"></i> Sign In
            </button>
        </form>

        <div class="text-center mt-4">
            <p class="text-sm text-muted mb-0">&copy; {{ date('Y') }} Madura Mart. All rights reserved.</p>
        </div>
    </div>

    <script>
        @if (session('simpan'))
            swal("Success", "{{ session('simpan') }}", "success");
        @endif
        @if (session('error'))
            swal("Error", "{{ session('error') }}", "error");
        @endif
    </script>
</body>
</html>
