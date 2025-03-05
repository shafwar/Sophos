<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sophos Endpoint Protection Dashboard</title>

    <!-- Core CSS Dependencies -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Animation Libraries -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
        :root {
            --primary-color: #003B7B;
            --secondary-color: #00BFFF;
            --accent-color: #f4f7fc;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --primary-blue: #1b258f;
            --secondary-blue: #4fc3f7;
            --text-dark: #333;
            --text-light: #666;
            --bg-light: #f8f9fa;
            --transition-speed: 0.3s;
            --border-radius: 8px;
            --box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--accent-color);
            margin-top: 80px;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Enhanced Navbar Styles */
        .navbar {
            background: linear-gradient(135deg, var(--primary-blue) 0%, #2a3bad 100%);
            padding: 0.8rem 2rem;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: all var(--transition-speed) ease;
        }

        .navbar-brand {
            color: white;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 0;
        }

        .navbar-brand i {
            font-size: 1.4rem;
            color: var(--secondary-blue);
        }

        .navbar-nav .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            padding: 0.8rem 1.2rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            position: relative;
            transition: all var(--transition-speed) ease;
        }

        .navbar-nav .nav-link:hover {
            color: white !important;
            transform: translateY(-2px);
        }

        .navbar-nav .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: var(--secondary-blue);
            transition: all var(--transition-speed) ease;
            transform: translateX(-50%);
        }

        .navbar-nav .nav-link:hover::after {
            width: 80%;
        }

        /* Enhanced Dropdown Styles */
        .dropdown-menu {
            background: white;
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            animation: fadeInDown 0.3s ease;
            margin-top: 0.5rem;
            padding: 0.5rem;
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            padding: 0.7rem 1.2rem;
            border-radius: var(--border-radius);
            transition: all var(--transition-speed) ease;
        }

        .dropdown-item:hover {
            background: var(--bg-light);
            transform: translateX(5px);
        }

        .dropdown-item i {
            width: 20px;
            text-align: center;
            margin-right: 10px;
            color: var(--primary-blue);
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Content Container */
        .main-content {
            min-height: calc(100vh - 80px);
            padding: 2rem;
            margin-top: 1rem;
        }

        /* Animations */
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Loading Spinner */
        .loading-spinner {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            animation: fadeIn 0.3s ease;
        }

        /* Utility Classes */
        .shadow-hover {
            transition: all var(--transition-speed) ease;
        }

        .shadow-hover:hover {
            box-shadow: var(--box-shadow);
            transform: translateY(-2px);
        }

        .rounded-custom {
            border-radius: var(--border-radius);
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Navbar -->
    @include('layouts.navbar')

    <!-- Main Content -->
    <div class="main-content">
        @yield('content')
    </div>

    <!-- Loading Spinner Template -->
    <div id="loadingSpinner" class="loading-spinner" style="display: none;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <!-- Core Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>

    <!-- Initialize Global Scripts -->
    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            once: true,
            offset: 50
        });

        // Setup CSRF Token for AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Global loading spinner functions
        window.showLoading = function() {
            document.getElementById('loadingSpinner').style.display = 'flex';
        }

        window.hideLoading = function() {
            document.getElementById('loadingSpinner').style.display = 'none';
        }

        // Handle errors globally
        window.handleError = function(error) {
            console.error('Error:', error);
            // Add your error handling logic here
        }

        // Add smooth scroll behavior
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
