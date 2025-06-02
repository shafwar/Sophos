<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SIPANDI - Security Protection Dashboard')</title>

    <!-- Core CSS Dependencies -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Animation Libraries -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

    <!-- Export Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>

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

        /* Dark Mode */
        body.dark-mode {
            background: #181c2a !important;
            color: #e0e6f3 !important;
        }
        body.dark-mode .navbar {
            background: #23263a !important;
        }
        body.dark-mode .card,
        body.dark-mode .dashboard-card,
        body.dark-mode .info-card,
        body.dark-mode .metric-card {
            background: #23263a !important;
            color: #e0e6f3 !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.4) !important;
        }
        body.dark-mode .alert {
            background: #23263a !important;
            color: #e0e6f3 !important;
            border-color: #444 !important;
        }
        body.dark-mode .dropdown-menu {
            background: #23263a !important;
            color: #e0e6f3 !important;
        }
        body.dark-mode .form-input {
            background: #23263a !important;
            color: #e0e6f3 !important;
            border-color: #444 !important;
        }
        body.dark-mode .table {
            background: #23263a !important;
            color: #e0e6f3 !important;
        }
        body.dark-mode .modal-content {
            background: #23263a !important;
            color: #e0e6f3 !important;
        }

        /* Navbar Styles */
        .navbar {
            background-color: #1b258f;
            padding: 0.8rem 2rem;
            transition: all 0.3s ease;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .navbar-brand {
            color: white;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .navbar-brand i {
            font-size: 1.4rem;
            color: var(--secondary-blue);
        }

        .navbar-brand:hover {
            color: white;
        }

        .navbar-nav {
            margin-left: auto;
            display: flex;
            align-items: center;
        }

        .navbar-nav .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            padding: 0.8rem 1.2rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            position: relative;
            transition: all 0.2s ease;
        }

        .navbar-nav .nav-link i {
            font-size: 1.1rem;
        }

        .navbar-nav .nav-link:hover {
            color: white !important;
        }

        .navbar-nav .nav-link:hover::after {
            width: 80%;
        }

        .navbar-nav .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background-color: var(--secondary-blue);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        /* Dropdown Styles */
        .dropdown-menu {
            background-color: white;
            border: none;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            animation: fadeInDown 0.3s ease;
            margin-top: 0.5rem;
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            padding: 0.7rem 1.2rem;
            transition: all 0.2s ease;
        }

        .dropdown-item:hover {
            background-color: #f5f5f5;
            transform: translateX(5px);
        }

        .dropdown-item i {
            width: 20px;
            text-align: center;
            margin-right: 10px;
            color: var(--primary-blue);
        }

        .dropdown-item.text-danger {
            color: var(--danger-color) !important;
        }

        .dropdown-item.text-danger:hover {
            background-color: rgba(220, 53, 69, 0.1);
        }

        .dropdown-menu-end {
            right: 0;
            left: auto;
        }

        .dropdown-item button {
            background: none;
            border: none;
            padding: 0;
            width: 100%;
            text-align: left;
            display: flex;
            align-items: center;
        }

        /* Mobile Responsive Styles */
        @media (max-width: 991.98px) {
            .navbar {
                padding: 0.5rem 1rem;
            }

            .navbar-nav {
                padding: 1rem 0;
            }

            .navbar-nav .nav-link {
                padding: 0.5rem 1rem;
            }

            .navbar-nav .nav-link::after {
                display: none;
            }

            .dropdown-menu {
                border: none;
                background: transparent;
                box-shadow: none;
                padding: 0 1rem;
            }

            .dropdown-item {
                color: rgba(255, 255, 255, 0.9) !important;
                padding: 0.5rem 1rem;
            }

            .dropdown-item:hover {
                background: rgba(255, 255, 255, 0.1);
                color: white !important;
            }
        }

        /* Navbar Toggler Custom Styles */
        .navbar-toggler {
            border: none;
            padding: 0.5rem;
            color: white;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
        }

        .navbar-toggler:focus {
            box-shadow: none;
            outline: none;
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 30 30' xmlns='http://www.w3.org/2000/svg'%3e%3cpath stroke='rgba(255, 255, 255, 0.9)' stroke-width='2' stroke-linecap='round' stroke-miterlimit='10' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
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

        /* Table Styles in Modal */
        .modal .table {
            margin-bottom: 0;
            font-size: 0.85rem;
        }

        .modal .table th {
            background-color: #f8f9fa;
            font-weight: 600;
            white-space: nowrap;
            padding: 0.5rem;
        }

        .modal .table td {
            padding: 0.5rem;
            vertical-align: middle;
        }

        .modal .table td.description-cell {
            max-width: 400px;
            white-space: normal;
            word-break: break-word;
        }

        /* Modal Styles */
        .modal-dialog.modal-lg {
            max-width: 90%;
        }

        .modal-content {
            border-radius: 16px;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .modal-header {
            background-color: var(--primary-color);
            color: white;
            border-top-left-radius: 16px;
            border-top-right-radius: 16px;
            padding: 0.75rem 1.5rem;
        }

        .modal-body {
            padding: 1rem;
        }

        /* Event Details Modal Specific Styles */
        #eventDetailsModal .modal-dialog {
            max-width: 700px;
        }

        #eventDetailsModal .modal-body {
            max-height: 80vh;
            overflow-y: auto;
            padding: 1.5rem;
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

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .modal-dialog.modal-lg {
                max-width: 95%;
                margin: 0.5rem auto;
            }
        }

        /* DARK MODE ENHANCEMENT */
        body.dark-mode .metric-label,
        body.dark-mode .text-muted,
        body.dark-mode .filter-label,
        body.dark-mode .legend-text,
        body.dark-mode .section-title,
        body.dark-mode .detail-label,
        body.dark-mode .detail-value {
            color: #f1f1f1 !important;
        }
        body.dark-mode .form-check-label,
        body.dark-mode .badge,
        body.dark-mode .btn,
        body.dark-mode .modal-title {
            color: #fff !important;
        }
        body.dark-mode .bg-light {
            background-color: #23263a !important;
            color: #f1f1f1 !important;
        }
        body.dark-mode .bg-white {
            background-color: #23263a !important;
            color: #f1f1f1 !important;
        }
        body.dark-mode .table th,
        body.dark-mode .table td {
            color: #f1f1f1 !important;
            background: #23263a !important;
        }
        body.dark-mode .legend-item {
            background: #23263a !important;
            color: #f1f1f1 !important;
            border-color: #444 !important;
        }
        body.dark-mode .legend-item.active {
            background: #1b258f !important;
            color: #fff !important;
        }
        body.dark-mode .modal-header {
            background-color: #1b258f !important;
            color: #fff !important;
        }
        body.dark-mode .modal-footer {
            background-color: #23263a !important;
            color: #fff !important;
        }
        body.dark-mode .dropdown-item {
            color: #f1f1f1 !important;
        }
        body.dark-mode .dropdown-item:hover {
            background-color: #1b258f !important;
            color: #fff !important;
        }
        body.dark-mode .footer {
            background: #181c2f !important;
            color: #f1f1f1 !important;
        }

        html, body {
            height: 100%;
        }
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .main-content {
            flex: 1 0 auto;
        }
        footer {
            flex-shrink: 0;
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container-fluid">
            <!-- Brand -->
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <i class="fas fa-shield-alt"></i>
                <span>SIPANDI</span>
            </a>

            <!-- Mobile Toggle -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Nav Items -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    
                    @if(auth()->user() && auth()->user()->role === 'admin')
                        <!-- Dashboards -->
                        <li class="nav-item dropdown">
                            <a class="nav-link" href="#" data-bs-toggle="dropdown">
                                <i class="fas fa-chart-line"></i>
                                <span>Dashboards</span>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('overview')}}">Overview</a></li>
                                <li><a class="dropdown-item" href="{{ route('analytics')}}">Analytics</a></li>
                            </ul>
                        </li>

                         <!-- Threat Analysis -->
                        <li class="nav-item dropdown">
                            <a class="nav-link" href="#" data-bs-toggle="dropdown">
                                <i class="fas fa-shield-virus"></i>
                                <span>Threat Analysis</span>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('reports')}}">Reports</a></li>
                            </ul>
                        </li>

                        <!-- Pending Approvals -->
                        <li class="nav-item">
                            <a class="nav-link position-relative" href="{{ route('admin.pending-users') }}">
                                <i class="fas fa-user-clock"></i>
                                <span>Pending Approvals</span>
                                @php $pendingCount = \App\Models\User::pending()->count(); @endphp
                                @if($pendingCount > 0)
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">{{ $pendingCount }}</span>
                                @endif
                            </a>
                        </li>

                        <!-- Activity Log (khusus admin) -->
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.activity-log') }}">
                                <i class="fas fa-list"></i>
                                <span>Activity Log</span>
                            </a>
                        </li>
                    @endif

                    <!-- Alerts -->
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-bell"></i>
                            <span>Alerts</span>
                        </a>
                    </li>

                    <!-- User Icons -->
                    <li class="nav-item dropdown">
                        <a class="nav-link" href="#" data-bs-toggle="dropdown">
                            <i class="fas fa-cog"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <div class="dropdown-item d-flex align-items-center justify-content-between">
                                    <span><i class="fas fa-moon me-2"></i>Dark Mode</span>
                                    <div class="form-check form-switch ms-2">
                                        <input class="form-check-input" type="checkbox" id="darkModeToggle">
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="{{ route('profile.show') }}">
                                    <i class="fas fa-user-circle me-2"></i>Profile
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        @yield('content')
    </div>

    <!-- INFO SECTION -->
    @if(Route::currentRouteName() === 'dashboard')
    <div class="row">
        <div class="col-12">
            <div class="info-card text-center p-4">
                <i class="fas fa-shield-alt fa-4x text-primary mb-4"></i>
                <h3 class="text-primary mb-3">Security Monitoring Dashboard</h3>
                <p class="text-muted mb-4">
                    You are viewing the security monitoring dashboard for your organization. 
                    This dashboard provides real-time insights into your security posture and threat status.
                </p>
                @if(auth()->user()->role === 'user')
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>User Access:</strong> You have read-only access to security metrics. 
                    For administrative functions, please contact your system administrator.
                </div>
                @endif
                <div class="row mt-4">
                    <div class="col-md-4">
                        <div class="p-3">
                            <i class="fas fa-eye fa-2x text-info mb-2"></i>
                            <h6>Real-time Monitoring</h6>
                            <small class="text-muted">Live security status updates</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3">
                            <i class="fas fa-shield-alt fa-2x text-success mb-2"></i>
                            <h6>Threat Detection</h6>
                            <small class="text-muted">Advanced security analysis</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3">
                            <i class="fas fa-chart-bar fa-2x text-warning mb-2"></i>
                            <h6>Risk Assessment</h6>
                            <small class="text-muted">Comprehensive security metrics</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

       <!-- Footer -->
       <footer class="mt-5" style="background: #1b258f; color: #fff; padding: 0;">
    <div class="container-fluid px-0">
        <div class="row g-0" style="background: #1b258f;">
            <div class="col-md-6 p-4">
    <div class="d-flex flex-row w-100">
        <div style="width: 48%;">
            <h6 class="fw-bold mb-3">KANTOR PUSAT</h6>
            <div>
                Grha Pertamina,<br>
                Pertamax Tower, Lantai 20 - 23<br>
                Jl. Medan Merdeka Timur No. 11-13<br>
                Jakarta Pusat 10110
            </div>
            <div class="mt-3">
                <i class="fas fa-phone-alt me-2"></i>+62 21 31906825<br>
                <i class="fas fa-phone-alt me-2"></i>+62 21 31906831
            </div>
        </div>

        <!-- Google Maps -->
        <div style="width: 52%; padding-left: -10rem;">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d991.9112914572228!2d106.83148526955105!3d-6.176173699382306!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f5cc67d4d3c9%3A0xb50dc574309d2d44!2sPertamina!5e0!3m2!1sen!2sid!4v1716712345678"
                width="100%" height="180" style="border:0; border-radius: 8px;" allowfullscreen="" loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </div>
</div>


            <!-- Spacer -->
            <div class="col-md-3 p-4"></div>

            <!-- Logo Pertamina Gas -->
            <div class="col-md-3 d-flex align-items-center justify-content-end pe-5 p-4">
                <img src="https://pertagas.pertamina.com/Static/pertagas/common/images/logo-pertagas-white.png" alt="PERTAMINA GAS" style="max-width: 270px;">
            </div>
        </div>
    </div>
</footer>

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
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Dark Mode Toggle
        document.addEventListener('DOMContentLoaded', function() {
            const darkModeToggle = document.getElementById('darkModeToggle');
            if (localStorage.getItem('darkMode') === 'enabled') {
                document.body.classList.add('dark-mode');
                if (darkModeToggle) darkModeToggle.checked = true;
            }
            if (darkModeToggle) {
                darkModeToggle.addEventListener('change', function() {
                    if (this.checked) {
                        document.body.classList.add('dark-mode');
                        localStorage.setItem('darkMode', 'enabled');
                    } else {
                        document.body.classList.remove('dark-mode');
                        localStorage.setItem('darkMode', 'disabled');
                    }
                });
            }
        });
    </script>

    @stack('scripts')
</body>
</html> 