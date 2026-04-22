<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('storage/images/branding/logo.jpeg') }}">
    <link rel="icon" type="image/png" href="{{ asset('storage/images/branding/logo.jpeg') }}">
    <title>Madura Mart - {{ $title }}</title>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <link id="pagestyle" href="{{ asset('be/assets/css/soft-ui-dashboard.css?v=1.0.7') }}" rel="stylesheet" />
    <style>
        body {
            background-image: url("{{ asset('be/assets/img/genshin_bg.png') }}");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Open Sans', sans-serif;
            overflow: hidden;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            padding: 40px;
            width: 400px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            text-align: center;
            animation: fadeInScale 0.5s ease-out;
        }

        @keyframes fadeInScale {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }

        .logo-text {
            font-size: 32px;
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
            letter-spacing: 1px;
        }

        .logo-subtext {
            font-size: 10px;
            color: #888;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 30px;
        }

        .form-control-genshin {
            background: #f5f5f5 !important;
            border: none !important;
            border-radius: 8px !important;
            padding: 12px 15px !important;
            margin-bottom: 15px !important;
            font-size: 14px !important;
            color: #333 !important;
        }

        .form-control-genshin:focus {
            background: #fff !important;
            box-shadow: 0 0 0 2px #333 !important;
        }

        .btn-genshin {
            background: #333;
            color: #fff;
            width: 100%;
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
        }

        .btn-genshin:hover {
            background: #000;
            transform: translateY(-2px);
        }

        .links-container {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
            font-size: 13px;
        }

        .link-genshin {
            color: #d1a03a;
            text-decoration: none;
            font-weight: 600;
        }

        .social-login {
            margin-top: 30px;
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        .social-icon {
            width: 32px;
            height: 32px;
            cursor: pointer;
            opacity: 0.7;
            transition: opacity 0.3s;
        }

        .social-icon:hover {
            opacity: 1;
        }

        .start-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
            z-index: 1000;
            cursor: pointer;
            transition: opacity 1s;
        }

        .start-text {
            font-size: 48px;
            font-weight: 700;
            letter-spacing: 8px;
            text-shadow: 0 0 20px rgba(255,255,255,0.5);
            margin-bottom: 20px;
        }

        .click-to-begin {
            font-size: 14px;
            letter-spacing: 4px;
            text-transform: uppercase;
            opacity: 0.8;
            animation: blink 2s infinite;
        }

        @keyframes blink {
            0%, 100% { opacity: 0.3; }
            50% { opacity: 0.8; }
        }

        .server-select {
            position: absolute;
            bottom: 50px;
            background: rgba(0,0,0,0.6);
            padding: 8px 30px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
        }

        .server-select i { color: #5cb85c; }
    </style>
</head>

<body>
    <div id="startOverlay" class="start-overlay" onclick="hideStart()">
        <div class="start-text">START GAME</div>
        <div class="click-to-begin">CLICK TO BEGIN</div>
        <div class="server-select">
            <i class="fas fa-check-circle"></i>
            <span>Madura Mart - Asia</span>
        </div>
    </div>

    <div class="login-card" id="loginCard" style="display: none;">
        <div class="logo-text">Madura Mart</div>
        <div class="logo-subtext">Tech Otakus Save The Mart</div>

        @if(session('error'))
            <div class="alert alert-danger text-white text-xs border-radius-sm p-2 mb-3" style="background: #f44336; border: none;">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST">
            @csrf
            <input type="email" name="email" class="form-control form-control-genshin" placeholder="Enter email/username" required value="{{ old('email') }}">
            <input type="password" name="password" class="form-control form-control-genshin" placeholder="Enter password" required>
            
            <div class="links-container">
                <a href="#" class="link-genshin">Register now</a>
                <a href="#" class="link-genshin">Forgot password?</a>
            </div>

            <button type="submit" class="btn-genshin">Log in</button>
        </form>

        <div style="margin-top: 20px; font-size: 11px; color: #aaa;">Log in with</div>
        <div class="social-login">
            <img src="https://img.icons8.com/color/48/000000/google-logo.png" class="social-icon" alt="Google">
            <img src="https://img.icons8.com/color/48/000000/facebook-new.png" class="social-icon" alt="Facebook">
            <img src="https://img.icons8.com/color/48/000000/twitter--v1.png" class="social-icon" alt="Twitter">
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/all.min.js"></script>
    <script>
        function hideStart() {
            const overlay = document.getElementById('startOverlay');
            overlay.style.opacity = '0';
            setTimeout(() => {
                overlay.style.display = 'none';
                document.getElementById('loginCard').style.display = 'block';
            }, 500);
        }
    </script>
</body>

</html>
