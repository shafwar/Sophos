<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PeSo (Pertamina Sophos)</title>

    <!-- Add Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Reset and Basic Styling */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(135deg, rgba(0, 59, 123, 0.1), rgba(0, 86, 179, 0.1)), #f0f2f5;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding-top: 50px;
        }

        .navbar {
            background: #3845d5;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            padding: 0.8rem 2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .navbar .nav-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar .nav-brand img {
            height: 40px;
        }

        /* Login Form Styles */
        .login-container {
            background: #ffffff;
            padding: 3rem 2rem;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            animation: fadeIn 1s ease-out;
            transform: translateY(20px);
            opacity: 0;
            animation-fill-mode: forwards;
        }

        .login-container .welcome-text {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .welcome-text h2 {
            color: #003B7B;
            margin-bottom: 0.5rem;
            font-size: 2rem;
        }

        .welcome-text p {
            color: #666;
            font-size: 0.9rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-input {
            width: 100%;
            padding: 14px 20px;
            padding-left: 45px;
            border: 2px solid #e1e1e1;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
            outline: none;
        }

        .form-input:focus {
            border-color: #003B7B;
            box-shadow: 0 0 5px rgba(0, 59, 123, 0.2);
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #aaa;
            transition: color 0.3s ease;
        }

        .form-input:focus + .input-icon {
            color: #003B7B;
        }

        .login-button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #003B7B, #0056b3);
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .login-button:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(0, 59, 123, 0.2);
        }

        .login-button:active {
            transform: scale(0.98);
        }

        .forgot-password {
            text-align: right;
            margin-top: -1rem;
            margin-bottom: 1.5rem;
        }

        .forgot-password a {
            color: #003B7B;
            font-size: 0.85rem;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .forgot-password a:hover {
            color: #0056b3;
        }

        .alert {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            animation: slideIn 0.5s ease;
        }

        .alert-danger {
            background-color: #fee2e2;
            border: 1px solid #fecaca;
            color: #dc2626;
        }

        /* Keyframe Animations */
        @keyframes fadeIn {
            0% {
                opacity: 0;
                transform: translateY(30px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideIn {
            0% {
                transform: translateY(-20px);
                opacity: 0;
            }

            100% {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* Loading Spinner */
        .loading {
            display: none;
            position: absolute;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.9);
            justify-content: center;
            align-items: center;
            border-radius: 10px;
        }

        .loading.active {
            display: flex;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #003B7B;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="nav-content">
            <div class="nav-brand">
                <img src="{{ asset('images/pertamina-logo.png') }}" alt="Pertamina Logo">
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="main-container">
        <div class="login-container">
            <!-- Loading Overlay -->
            <div class="loading" id="loading">
                <div class="spinner"></div>
            </div>

            <div class="welcome-text">
                <h2>PERTAMINA SOPHOS</h2>
                <p>Silahkan login terlebih dahulu</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul style="margin: 0; padding-left: 1rem;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login.submit') }}" id="loginForm">
                @csrf
                <div class="form-group">
                    <input type="email" name="email" class="form-input" placeholder="Email" value="{{ old('email') }}" required>
                    <i class="fas fa-envelope input-icon"></i>
                </div>

                <div class="form-group">
                    <input type="password" name="password" class="form-input" placeholder="Password" required>
                    <i class="fas fa-lock input-icon"></i>
                </div>

                <div class="forgot-password">
                    <a href="{{ route('password.request') }}">Lupa Password?</a>
                </div>

                <button type="submit" class="login-button">
                    LOGIN
                </button>
            </form>
        </div>
    </div>

    <script>
        // Add loading animation when form is submitted
        document.getElementById('loginForm').addEventListener('submit', function() {
            document.getElementById('loading').classList.add('active');
        });

        // Add subtle animation to input fields when focused
        document.querySelectorAll('.form-input').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'translateX(5px)';
                setTimeout(() => {
                    this.parentElement.style.transform = 'translateX(0)';
                }, 150);
            });
        });
    </script>
</body>

</html>
