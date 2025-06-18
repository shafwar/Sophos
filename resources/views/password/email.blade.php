<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: #1a1f35;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 90px 20px 20px;
            position: relative;
            overflow: hidden;
        }

        .bg-animation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
        }

        .bg-gradient {
            position: absolute;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 50% 50%,
                rgba(41, 66, 112, 0.1),
                rgba(28, 45, 76, 0.2));
            animation: pulseGradient 8s ease infinite;
        }

        @keyframes pulseGradient {
            0%, 100% {
                transform: scale(1);
                opacity: 0.8;
            }
            50% {
                transform: scale(1.2);
                opacity: 1;
            }
        }

        .orbs {
            position: absolute;
            width: 100%;
            height: 100%;
        }

        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(40px);
            opacity: 0.4;
            animation: orbFloat 20s infinite ease-in-out;
        }

        .orb-1 {
            top: 20%;
            left: 20%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle at center,
                rgba(64, 104, 224, 0.3),
                rgba(39, 72, 165, 0.1));
            animation-delay: -2s;
        }

        .orb-2 {
            top: 60%;
            right: 20%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle at center,
                rgba(59, 82, 177, 0.3),
                rgba(28, 45, 76, 0.1));
            animation-delay: -4s;
        }

        .orb-3 {
            bottom: 10%;
            left: 30%;
            width: 250px;
            height: 250px;
            background: radial-gradient(circle at center,
                rgba(76, 116, 224, 0.3),
                rgba(45, 76, 165, 0.1));
            animation-delay: -6s;
        }

        @keyframes orbFloat {
            0%, 100% {
                transform: translate(0, 0) scale(1);
                opacity: 0.4;
            }
            25% {
                transform: translate(5%, 5%) scale(1.1);
                opacity: 0.5;
            }
            50% {
                transform: translate(0, 10%) scale(0.9);
                opacity: 0.3;
            }
            75% {
                transform: translate(-5%, 5%) scale(1.05);
                opacity: 0.4;
            }
        }

        .stars {
            position: absolute;
            width: 100%;
            height: 100%;
            background-image:
                radial-gradient(2px 2px at 20px 30px, #ffffff15, transparent),
                radial-gradient(2px 2px at 40px 70px, #ffffff10, transparent),
                radial-gradient(2px 2px at 50px 160px, #ffffff15, transparent),
                radial-gradient(2px 2px at 90px 40px, #ffffff10, transparent),
                radial-gradient(2px 2px at 130px 80px, #ffffff15, transparent);
            background-repeat: repeat;
            background-size: 200px 200px;
            animation: starFloat 100s linear infinite;
            opacity: 0.3;
        }

        @keyframes starFloat {
            0% {
                transform: translateY(0);
            }
            100% {
                transform: translateY(-200px);
            }
        }

        .login-container {
            background: rgba(255, 255, 255, 0.03);
            padding: 3rem;
            border-radius: 24px;
            box-shadow:
                0 4px 24px -1px rgba(0, 0, 0, 0.2),
                0 0 1px 0 rgba(255, 255, 255, 0.1) inset;
            width: 110%;
            max-width: 500px;
            position: relative;
            overflow: hidden;
            animation: containerFloat 6s ease-in-out infinite;
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .login-container::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(125deg,
                transparent 0%,
                rgba(255, 255, 255, 0.05) 30%,
                rgba(255, 255, 255, 0.1) 50%,
                rgba(255, 255, 255, 0.05) 70%,
                transparent 100%);
            animation: shine 7s infinite;
        }

        @keyframes shine {
            0% {
                transform: translateX(-100%) rotate(25deg);
            }
            100% {
                transform: translateX(100%) rotate(25deg);
            }
        }

        @keyframes containerFloat {
            0%, 100% {
                transform: translateY(0) scale(1);
            }
            50% {
                transform: translateY(-10px) scale(1.01);
            }
        }

        .welcome-text {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .welcome-text h2 {
            color: #ffffff;
            font-size: 2.2rem;
            margin-bottom: 1rem;
            letter-spacing: 0.5px;
            animation: textFloat 3s ease-in-out infinite;
        }

        .welcome-text p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 1rem;
        }

        .form-group {
            margin-bottom: 2rem;
            position: relative;
        }

        .form-input {
            width: 100%;
            padding: 16px 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 14px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.05);
            color: #ffffff;
        }

        .form-input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .form-input:focus {
            border-color: rgba(255, 255, 255, 0.3);
            background: rgba(255, 255, 255, 0.1);
            outline: none;
            transform: translateY(-2px);
            box-shadow: 0 4px 20px -1px rgba(0, 0, 0, 0.2);
        }

        .login-button {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
            border: none;
            border-radius: 14px;
            font-weight: 500;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .login-button::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.3) 0%, transparent 60%);
            transform: scale(0);
            transition: transform 0.5s ease;
        }

        .login-button:hover::before {
            transform: scale(1);
        }

        .login-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(59, 130, 246, 0.4);
        }
        
        .alert {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 8px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border-color: #f5c6cb;
        }

        @media (max-width: 640px) {
            .login-container {
                padding: 2rem;
                margin: 0 1rem;
            }

            .welcome-text h2 {
                font-size: 1.8rem;
            }

            .orb {
                opacity: 0.2;
            }
        }
    </style>
</head>

<body>
    <div class="bg-animation">
        <div class="bg-gradient"></div>
        <div class="stars"></div>
        <div class="orbs">
            <div class="orb orb-1"></div>
            <div class="orb orb-2"></div>
            <div class="orb orb-3"></div>
        </div>
    </div>

    <div class="main-container">
        <div class="login-container">
            <div class="welcome-text">
                <h2>Reset Password</h2>
                <p>Masukkan email Anda untuk menerima link reset password.</p>
            </div>

            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" id="resetPasswordForm">
                @csrf
                <div class="form-group">
                    <input type="email" name="email" class="form-input" placeholder="Email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                </div>

                <button type="submit" class="login-button">
                    KIRIM LINK RESET
                </button>

                <div class="text-center mt-4">
                    <p style="color: rgba(255, 255, 255, 0.7);">
                        Kembali ke halaman login?
                        <a href="{{ route('login') }}" style="color: #3b82f6; text-decoration: none;">Login</a>
                    </p>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.form-input').forEach(input => {
                input.style.opacity = '0';
                input.style.transform = 'translateY(20px)';

                setTimeout(() => {
                    input.style.transition = 'all 0.5s ease';
                    input.style.opacity = '1';
                    input.style.transform = 'translateY(0)';
                }, 200);
            });
        });
    </script>
</body>
</html>
