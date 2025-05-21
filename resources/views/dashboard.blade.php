<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard - SIPANDI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>

    <style>
        /* Root Variables */
        :root {
            --primary-color: #003B7B;
            --secondary-color: #00BFFF;
            --accent-color: #f4f7fc;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
        }

        /* Base Styles */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--accent-color);
            margin-top: 80px;
        }

        /* Navbar Styles */
        .navbar {
            background-color: #1b258f;
            padding: 0.8rem 2rem;
            transition: all 0.3s ease;
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
        }

        .navbar-brand:hover {
            color: #fff;
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
            background-color: #4fc3f7;
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

        /* Metric Card Styles */
        .metric-card {
            background: linear-gradient(145deg, #ffffff, #f5f5f5);
            border-radius: 16px;
            padding: 25px;
            text-align: center;
            box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.1),
                -5px -5px 15px rgba(255, 255, 255, 0.8);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            cursor: pointer;
        }

        .metric-card:hover {
            transform: translateY(-10px);
            box-shadow: 8px 8px 20px rgba(0, 0, 0, 0.12),
                -8px -8px 20px rgba(255, 255, 255, 0.9);
        }

        .metric-value {
            font-size: 32px;
            font-weight: 700;
            margin: 15px 0;
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .metric-label {
            color: #666;
            font-size: 15px;
            font-weight: 500;
            letter-spacing: 0.5px;
        }

        .metric-trend {
            font-size: 14px;
            padding: 6px 12px;
            border-radius: 20px;
            display: inline-block;
            margin-top: 12px;
            transition: all 0.3s ease;
        }

        .trend-up {
            background-color: rgba(40, 167, 69, 0.1);
            color: var(--success-color);
        }

        .trend-down {
            background-color: rgba(220, 53, 69, 0.1);
            color: var(--danger-color);
        }

        /* Dashboard Card Styles */
        .dashboard-card {
            background: white;
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.12);
        }

        .dashboard-card h5 {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--secondary-color);
            position: relative;
        }

        .dashboard-card h5::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 50px;
            height: 2px;
            background: var(--primary-color);
        }

        .chart-container {
            height: 300px;
            position: relative;
            margin-top: 20px;
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

        /* Event Details Modal Specific Styles */
        #eventDetailsModal .modal-dialog {
            max-width: 700px;
        }

        #eventDetailsModal .modal-body {
            max-height: 80vh;
            overflow-y: auto;
            padding: 1.5rem;
        }

        .event-details {
            padding: 1rem;
        }

        .detail-section {
            margin-bottom: 1.5rem;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .section-title {
            color: var(--primary-color);
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e9ecef;
            font-weight: 600;
        }

        .detail-row {
            display: flex;
            margin-bottom: 0.5rem;
            padding: 0.5rem;
            background: white;
            border-radius: 4px;
            transition: background-color 0.2s ease;
        }

        .detail-row:hover {
            background: #f8f9fa;
        }

        .detail-label {
            font-weight: 500;
            min-width: 150px;
            color: #495057;
        }

        .detail-value {
            flex: 1;
            color: #212529;
            word-break: break-word;
        }

        /* Badge Styles */
        .badge {
            padding: 0.4em 0.8em;
            font-size: 0.75rem;
            font-weight: 500;
            border-radius: 4px;
        }

        .badge-high {
            color: #fff !important;
        }

        .badge-medium {
            color: #000 !important;
        }

        .badge-low {
            color: #fff !important;
        }

        .badge-secondary {
            color: #fff !important;
        }

        .event-details {
            padding: 1rem;
        }

        .detail-section {
            margin-bottom: 1.5rem;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .section-title {
            color: #003B7B;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e9ecef;
            font-weight: 600;
        }

        .detail-row {
            display: flex;
            margin-bottom: 0.5rem;
            padding: 0.5rem;
            background: white;
            border-radius: 4px;
            transition: background-color 0.2s ease;
        }

        .detail-row:hover {
            background: #f8f9fa;
        }

        .detail-label {
            font-weight: 500;
            min-width: 150px;
            color: #495057;
        }

        .detail-value {
            flex: 1;
            color: #212529;
            word-break: break-word;
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

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .metric-card {
                margin-bottom: 1rem;
            }

            .detail-row {
                flex-direction: column;
            }

            .detail-label {
                min-width: 100%;
                margin-bottom: 0.25rem;
            }

            .modal-dialog.modal-lg {
                max-width: 95%;
                margin: 0.5rem auto;
            }
        }

        /* High Risk Alert Animations */
        @keyframes highRiskBlink {
            0% {
                box-shadow: 0 0 10px rgba(220, 53, 69, 0.8);
                transform: scale(1);
            }
            50% {
                box-shadow: 0 0 20px rgba(220, 53, 69, 0.9);
                transform: scale(1.02);
            }
            100% {
                box-shadow: 0 0 10px rgba(220, 53, 69, 0.8);
                transform: scale(1);
            }
        }

        .high-risk-warning {
            animation: highRiskBlink 1.5s infinite;
        }

        /* Alert Notifications */
        .alert-floating {
            position: fixed;
            top: 80px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
            max-width: 400px;
            background-color: #fff;
            border-left: 4px solid #dc3545;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            border-radius: 4px;
            padding: 1rem;
            transform: translateX(120%);
            transition: transform 0.3s ease-in-out;
        }

        .alert-floating.show {
            transform: translateX(0);
        }

        .alert-floating .alert-content {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-floating .alert-icon {
            font-size: 1.5rem;
            color: #dc3545;
        }

        .alert-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: #dc3545;
            color: white;
            border-radius: 50%;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            font-weight: bold;
        }

        /* Custom scrollbar */
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

        /* Enhanced Traffic Risk Card */
        .traffic-risk-card {
            position: relative;
            transition: all 0.3s ease;
        }

        .traffic-risk-card h5 {
            display: flex;
            align-items: center;
        }

        .filter-info {
            font-size: 0.85rem;
            color: var(--secondary-color);
            cursor: pointer;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
                opacity: 0.8;
            }
            50% {
                transform: scale(1.1);
                opacity: 1;
            }
            100% {
                transform: scale(1);
                opacity: 0.8;
            }
        }

        /* Enhanced Legend */
        .enhanced-legend {
            display: flex;
            justify-content: center;
            margin: 15px 0;
            padding: 15px;
            border-radius: 8px;
            background: #f8f9fa;
            flex-wrap: wrap;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .legend-item {
            display: flex;
            align-items: center;
            margin: 0 15px;
            padding: 10px 18px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.25s ease;
            position: relative;
            border: 2px solid transparent;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            background-color: white;
        }

        .legend-item:hover {
            background-color: #fff;
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.1);
        }

        .legend-item::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 50%;
            width: 0;
            height: 2px;
            background-color: currentColor;
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .legend-item:hover::after {
            width: 80%;
        }

        .legend-item.active {
            border-color: #1b258f;
            background-color: rgba(27, 37, 143, 0.05);
            font-weight: 700;
        }

        .legend-color {
            width: 18px;
            height: 18px;
            border-radius: 4px;
            margin-right: 10px;
        }

        .legend-text {
            font-weight: 600;
            font-size: 1rem;
        }

        .filter-icon {
            margin-left: 10px;
            opacity: 0.6;
            transition: opacity 0.3s ease, transform 0.3s ease;
        }

        .legend-item:hover .filter-icon {
            opacity: 1;
            transform: rotate(-15deg);
        }

        .legend-item.active .filter-icon {
            opacity: 1;
            color: #1b258f;
        }

        /* High Risk specific styling */
        .high-risk-filter {
            border-left: 3px solid #dc3545;
        }

        .high-risk-filter:hover {
            background-color: rgba(220, 53, 69, 0.03);
        }

        .high-risk-filter.active {
            background-color: rgba(220, 53, 69, 0.08);
            border-color: #dc3545;
        }

        /* Medium Risk specific styling */
        .medium-risk-filter {
            border-left: 3px solid #ffc107;
        }

        .medium-risk-filter:hover {
            background-color: rgba(255, 193, 7, 0.03);
        }

        .medium-risk-filter.active {
            background-color: rgba(255, 193, 7, 0.08);
            border-color: #ffc107;
        }

        /* Low Risk specific styling */
        .low-risk-filter {
            border-left: 3px solid #28a745;
        }

        .low-risk-filter:hover {
            background-color: rgba(40, 167, 69, 0.03);
        }

        .low-risk-filter.active {
            background-color: rgba(40, 167, 69, 0.08);
            border-color: #28a745;
        }

        /* Filter Status Improvements */
        .filter-status {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            margin-top: 15px;
            padding: 8px;
            background-color: rgba(0,0,0,0.02);
            border-radius: 8px;
        }

        .filter-status .badge {
            font-size: 0.85rem;
            padding: 8px 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.08);
            margin: 3px 5px;
            transition: all 0.2s ease;
        }

        .filter-status .badge:hover {
            transform: translateY(-2px);
            box-shadow: 0 3px 6px rgba(0,0,0,0.12);
        }

        /* Custom tooltip styling */
        .tooltip .tooltip-inner {
            max-width: 250px;
            padding: 10px 15px;
            font-size: 0.9rem;
            background-color: #1b258f;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .tooltip .arrow::before {
            border-top-color: #1b258f;
        }

        .filter-label {
            display: flex;
            align-items: center;
            font-weight: 600;
            color: #495057;
            font-size: 0.95rem;
        }

        @media (max-width: 768px) {
            .filter-label {
                width: 100%;
                margin-bottom: 10px;
            }

            .enhanced-legend {
                flex-direction: column;
                align-items: flex-start;
            }

            .legend-item {
                margin: 5px 0;
            }
        }

        /* Menyembunyikan legenda default Chart.js */
        .chartjs-legend,
        .chartjs-render-monitor + ul {
            display: none !important;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container-fluid">
            <!-- Brand -->
            <a class="navbar-brand" href="#">
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
                            <li><a class="dropdown-item" href="{{ route('profile.show') }}"><i class="fas fa-user-circle me-2"></i>Profile</a></li>
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

        <!-- SiPandi Central Dashboard Header -->
    <div class="container-fluid px-4 py-3 mt-4" style="background: white; border-bottom: 1px solid #e5e7eb;">
        <h2 class="mb-1" style="color: #1b258f; font-size: 1.75rem; font-weight: 600;">SIPANDI Central Dashboard</h2>
        <p class="text-muted mb-0" style="font-size: 0.95rem;">See a snapshot of your security protection</p>
    </div>

    <div class="container mt-5">
        <!-- Summary Metrics -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="metric-card" onclick="fetchDetailData('All Risk')">
                    <i class="fas fa-chart-line fa-2x" style="color: var(--primary-color)"></i>
                    <div class="metric-value">{{ $riskData['total'] ?? 0 }}</div>
                    <div class="metric-label">Total Alerts</div>
                    <div class="metric-trend {{ isset($riskData['weeklyChange']['total']) && str_contains($riskData['weeklyChange']['total'], '-') ? 'trend-down' : 'trend-up' }}">
                        <i class="fas fa-arrow-{{ isset($riskData['weeklyChange']['total']) && str_contains($riskData['weeklyChange']['total'], '-') ? 'down' : 'up' }}"></i>
                        {{ $riskData['weeklyChange']['total'] ?? '0% this week' }}
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
                <div class="metric-card" onclick="fetchDetailData('High Risk')">
                    <i class="fas fa-exclamation-triangle fa-2x" style="color: var(--danger-color)"></i>
                    <div class="metric-value">{{ $riskData['high'] ?? 0 }}</div>
                    <div class="metric-label">High Risk</div>
                    <div class="metric-trend {{ isset($riskData['weeklyChange']['high']) && str_contains($riskData['weeklyChange']['high'], '-') ? 'trend-down' : 'trend-up' }}">
                        <i class="fas fa-arrow-{{ isset($riskData['weeklyChange']['high']) && str_contains($riskData['weeklyChange']['high'], '-') ? 'down' : 'up' }}"></i>
                        {{ $riskData['weeklyChange']['high'] ?? '0% this week' }}
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
                <div class="metric-card" onclick="fetchDetailData('Medium Risk')">
                    <i class="fas fa-exclamation-circle fa-2x" style="color: var(--warning-color)"></i>
                    <div class="metric-value">{{ $riskData['medium'] ?? 0 }}</div>
                    <div class="metric-label">Medium Risk</div>
                    <div class="metric-trend {{ isset($riskData['weeklyChange']['medium']) && str_contains($riskData['weeklyChange']['medium'], '-') ? 'trend-down' : 'trend-up' }}">
                        <i class="fas fa-arrow-{{ isset($riskData['weeklyChange']['medium']) && str_contains($riskData['weeklyChange']['medium'], '-') ? 'down' : 'up' }}"></i>
                        {{ $riskData['weeklyChange']['medium'] ?? '0% this week' }}
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="400">
                <div class="metric-card" onclick="fetchDetailData('Low Risk')">
                    <i class="fas fa-info-circle fa-2x" style="color: var(--success-color)"></i>
                    <div class="metric-value">{{ $riskData['low'] ?? 0 }}</div>
                    <div class="metric-label">Low Risk</div>
                    <div class="metric-trend {{ isset($riskData['weeklyChange']['low']) && str_contains($riskData['weeklyChange']['low'], '-') ? 'trend-down' : 'trend-up' }}">
                        <i class="fas fa-arrow-{{ isset($riskData['weeklyChange']['low']) && str_contains($riskData['weeklyChange']['low'], '-') ? 'down' : 'up' }}"></i>
                        {{ $riskData['weeklyChange']['low'] ?? '0% this week' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Risk Level Charts -->
        <div class="row">
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="dashboard-card">
                    <h5><i class="fas fa-check-circle me-2"></i>LOW RISK</h5>
                    <div class="chart-container">
                        <canvas id="lowRiskChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="dashboard-card">
                    <h5><i class="fas fa-exclamation-circle me-2"></i>MEDIUM RISK</h5>
                    <div class="chart-container">
                        <canvas id="mediumRiskChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                <div class="dashboard-card">
                    <h5><i class="fas fa-times-circle me-2"></i>HIGH RISK</h5>
                    <div class="chart-container">
                        <canvas id="highRiskChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Traffic Risk Overview - BAGIAN YANG DIPERBARUI -->
        <div class="dashboard-card traffic-risk-card" data-aos="fade-up">
            <h5>
                <i class="fas fa-chart-area me-2"></i>
                TRAFFIC RISK OVERVIEW
                <div class="filter-info ms-2">
                    <i class="fas fa-info-circle" data-bs-toggle="tooltip" title="Click on risk categories in the legend to filter data"></i>
                </div>
            </h5>

            <!-- Enhanced Legend dengan label filter -->
            <div class="enhanced-legend">
                <span class="filter-label me-3">Filter by:</span>
                <div class="legend-item high-risk-filter" id="highRiskFilter">
                    <span class="legend-color" style="background-color: #dc3545;"></span>
                    <span class="legend-text">High Risk</span>
                    <i class="fas fa-filter filter-icon"></i>
                </div>
                <div class="legend-item medium-risk-filter" id="mediumRiskFilter">
                    <span class="legend-color" style="background-color: #ffc107;"></span>
                    <span class="legend-text">Medium Risk</span>
                    <i class="fas fa-filter filter-icon"></i>
                </div>
                <div class="legend-item low-risk-filter" id="lowRiskFilter">
                    <span class="legend-color" style="background-color: #28a745;"></span>
                    <span class="legend-text">Low Risk</span>
                    <i class="fas fa-filter filter-icon"></i>
                </div>
            </div>

            <div class="chart-container">
                <canvas id="trafficRiskChart"></canvas>
            </div>

            <!-- Filter Status -->
            <div class="filter-status mt-3" id="filterStatus">
                <span class="badge rounded-pill bg-light text-dark me-2 mb-2">All data shown</span>
                <!-- Active filters will appear here -->
            </div>
        </div>

    </div>

<!-- Risk Details Modal -->
<div class="modal fade" id="riskDetailModal" tabindex="-1" aria-labelledby="riskDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="riskDetailModalLabel">Risk Details</h5>
                <div class="ms-auto">
                    <button type="button" class="btn btn-sm btn-outline-light me-2" onclick="exportToPDF('risk')">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-light" onclick="exportToCSV('risk')">
                        <i class="fas fa-file-csv"></i> Export CSV
                    </button>
                </div>
                <button type="button" class="btn-close ms-2" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="15%">ID</th>
                                <th width="15%">Category</th>
                                <th width="30%">Description</th>
                                <th width="10%">Severity</th>
                                <th width="15%">Date</th>
                                <th width="15%">Action</th>
                            </tr>
                        </thead>
                        <tbody id="riskDetailTableBody">
                            <!-- Risk data will be dynamically inserted here -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Alert Details Modal -->
<div class="modal fade" id="alertDetailModal" tabindex="-1" aria-labelledby="alertDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="alertDetailModalLabel">Alert Details</h5>
                <div class="ms-auto">
                    <button type="button" class="btn btn-sm btn-outline-light me-2" onclick="exportToPDF('alert')">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-light" onclick="exportToCSV('alert')">
                        <i class="fas fa-file-csv"></i> Export CSV
                    </button>
                </div>
                <button type="button" class="btn-close ms-2" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="15%">ID</th>
                                <th width="15%">Category</th>
                                <th width="30%">Description</th>
                                <th width="10%">Severity</th>
                                <th width="15%">Date</th>
                                <th width="15%">Action</th>
                            </tr>
                        </thead>
                        <tbody id="alertDetailTableBody">
                            <!-- Alert data will be dynamically inserted here -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Event Details Modal -->
<div class="modal fade" id="eventDetailsModal" tabindex="-1" aria-labelledby="eventDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventDetailsModalLabel">Event Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="eventDetailsContent">
                    <!-- Event details will be inserted here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            once: true
        });

        // Data storage variables
        let currentRiskData = []; // For Risk Details
        let currentAlertData = []; // For Alert Details

        // Chart.js Configuration
        Chart.defaults.font.family = 'Poppins';
        Chart.defaults.color = '#666';
        Chart.defaults.plugins.tooltip.backgroundColor = 'rgba(0, 0, 0, 0.8)';
        Chart.defaults.plugins.tooltip.padding = 12;
        Chart.defaults.plugins.tooltip.titleFont = { size: 13 };
        Chart.defaults.plugins.tooltip.bodyFont = { size: 12 };

        // Definisikan plugin terlebih dahulu sebelum digunakan
        const centerTextPlugin = {
            id: 'centerText',
            afterDraw: (chart, args, options) => {
                const { ctx, chartArea: { left, right, top, bottom, width, height } } = chart;
                ctx.save();

                // Get the text value (first value in dataset)
                const value = chart.data.datasets[0].data[0];

                // Text styling
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.font = 'bold 24px Poppins';

                // Choose color based on chart ID
                if (chart.canvas.id === 'lowRiskChart') {
                    ctx.fillStyle = 'rgba(40, 167, 69, 0.8)';  // Green
                } else if (chart.canvas.id === 'mediumRiskChart') {
                    ctx.fillStyle = 'rgba(255, 193, 7, 0.8)';  // Yellow
                } else {
                    ctx.fillStyle = 'rgba(220, 53, 69, 0.8)';  // Red
                }

                // Draw text in center
                ctx.fillText(value, left + width / 2, top + height / 2);
                ctx.restore();
            }
        };

        // Calculate percentages based on actual data
        const totalRisk = {{ $riskData['total'] ?? 0 }};
        const highRisk = {{ $riskData['high'] ?? 0 }};
        const mediumRisk = {{ $riskData['medium'] ?? 0 }};
        const lowRisk = {{ $riskData['low'] ?? 0 }};

        // Risk Charts Configuration
        const riskChartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '75%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        font: {
                            size: 12
                        }
                    }
                }
            },
            animation: {
                animateScale: true,
                animateRotate: true,
                duration: 2000,
                easing: 'easeInOutQuart'
            }
        };

        // Create doughnut charts (hapus duplikasi, hanya buat sekali)
        new Chart(document.getElementById('lowRiskChart'), {
            type: 'doughnut',
            plugins: [centerTextPlugin],
            data: {
                labels: ['Low Risk', 'Other'],
                datasets: [{
                    data: [lowRisk, totalRisk - lowRisk],
                    backgroundColor: [
                        'rgba(40, 167, 69, 0.8)',
                        'rgba(233, 236, 239, 0.5)'
                    ],
                    borderWidth: 0
                }]
            },
            options: riskChartOptions
        });

        new Chart(document.getElementById('mediumRiskChart'), {
            type: 'doughnut',
            plugins: [centerTextPlugin],
            data: {
                labels: ['Medium Risk', 'Other'],
                datasets: [{
                    data: [mediumRisk, totalRisk - mediumRisk],
                    backgroundColor: [
                        'rgba(255, 193, 7, 0.8)',
                        'rgba(233, 236, 239, 0.5)'
                    ],
                    borderWidth: 0
                }]
            },
            options: riskChartOptions
        });

        // High Risk Chart Initialization
        const highRiskChartElement = document.getElementById('highRiskChart');
        highRiskChartElement.setAttribute('data-value', highRisk);

        new Chart(highRiskChartElement, {
            type: 'doughnut',
            plugins: [centerTextPlugin],
            data: {
                labels: ['High Risk', 'Other'],
                datasets: [{
                    data: [highRisk, totalRisk - highRisk],
                    backgroundColor: [
                        'rgba(220, 53, 69, 0.8)',
                        'rgba(233, 236, 239, 0.5)'
                    ],
                    borderWidth: 0
                }]
            },
            options: riskChartOptions
        });

        function fetchDetailData(category) {
            const tableBody = document.getElementById('riskDetailTableBody');
            const modalTitle = document.getElementById('riskDetailModalLabel');

            modalTitle.textContent = `Risk Details - ${category}`;

            tableBody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center">
                        <div class="d-flex justify-content-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </td>
                </tr>`;

            const riskModal = new bootstrap.Modal(document.getElementById('riskDetailModal'));
            riskModal.show();

            fetch(`/alerts/${encodeURIComponent(category.toLowerCase())}`)
                .then(response => {
                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                    return response.json();
                })
                .then(response => {
                    tableBody.innerHTML = '';
                    currentRiskData = response.data || [];

                    if (currentRiskData.length === 0) {
                        tableBody.innerHTML = `
                            <tr>
                                <td colspan="6" class="text-center">No alerts found for ${category}</td>
                            </tr>`;
                        return;
                    }

                    currentRiskData.forEach((item, index) => {
                        const row = document.createElement('tr');
                        row.style.animation = `fadeIn 0.3s ease-in-out ${index * 0.1}s`;

                        const severityClass = getSeverityClass(item.severity);

                        row.innerHTML = `
                            <td class="text-muted small">${item.id || '-'}</td>
                            <td>${item.category || '-'}</td>
                            <td class="description-cell">${item.description?.split('\n')[0] || '-'}</td>
                            <td><span class="badge ${severityClass}">${item.severity?.toUpperCase() || '-'}</span></td>
                            <td class="small">${formatDate(item.raisedAt)}</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-info view-details">View Details</button>
                            </td>
                        `;

                        const viewButton = row.querySelector('.view-details');
                        viewButton.addEventListener('click', () => showEventDetails(item));

                        tableBody.appendChild(row);
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    tableBody.innerHTML = `
                        <tr>
                            <td colspan="6" class="text-center text-danger">
                                <div class="alert alert-danger mb-0">
                                    <strong>Error:</strong> An error occurred while fetching the data.
                                </div>
                            </td>
                        </tr>
                    `;
                });
        }

        // Updated showMonthlyDetails function
        function showMonthlyDetails(month) {
            const tableBody = document.getElementById('alertDetailTableBody');
            const modalTitle = document.getElementById('alertDetailModalLabel');

            modalTitle.textContent = `Alert Details - ${month}`;

            tableBody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </td>
                </tr>`;

            const alertModal = new bootstrap.Modal(document.getElementById('alertDetailModal'));
            alertModal.show();

            fetch(`/traffic-risk/monthly-details/${month}`)
                .then(response => {
                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                    return response.json();
                })
                .then(response => {
                    tableBody.innerHTML = '';
                    currentAlertData = response.incidents || [];

                    if (currentAlertData.length === 0) {
                        tableBody.innerHTML = `
                            <tr>
                                <td colspan="6" class="text-center">No alerts found for ${month}</td>
                            </tr>`;
                        return;
                    }

                    currentAlertData.forEach((item, index) => {
                        const row = document.createElement('tr');
                        row.style.animation = `fadeIn 0.3s ease-in-out ${index * 0.1}s`;

                        const severityClass = getSeverityClass(item.severity);

                        row.innerHTML = `
                            <td class="text-muted small">${item.id || '-'}</td>
                            <td>${item.category || '-'}</td>
                            <td class="description-cell">${item.description?.split('\n')[0] || '-'}</td>
                            <td><span class="badge ${severityClass}">${item.severity?.toUpperCase() || '-'}</span></td>
                            <td class="small">${formatDate(item.date)}</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-info view-details">View Details</button>
                            </td>
                        `;

                        const viewButton = row.querySelector('.view-details');
                        viewButton.addEventListener('click', () => showEventDetails(item));

                        tableBody.appendChild(row);
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    tableBody.innerHTML = `
                        <tr>
                            <td colspan="6" class="text-center text-danger">
                                <div class="alert alert-danger mb-0">
                                    <strong>Error:</strong> An error occurred while fetching the data.
                                </div>
                            </td>
                        </tr>
                    `;
                });
        }

        // Updated export functions
        function exportToPDF(type) {
            const data = type === 'risk' ? currentRiskData : currentAlertData;
            const title = type === 'risk' ?
                document.getElementById('riskDetailModalLabel').textContent :
                document.getElementById('alertDetailModalLabel').textContent;

            // Extract the category from the modal title (e.g., "Risk Details - Low Risk" -> "Low Risk")
            const categoryMatch = title.match(/Risk Details - (.+)/);
            const category = categoryMatch ? categoryMatch[1].toLowerCase().replace(' risk', '') : 'all'; // Default to 'all' or handle error

            if (!data || data.length === 0) {
                alert('No data available to export');
                return;
            }

            // Redirect to the Laravel export route
            window.location.href = `/risk/export/${encodeURIComponent(category)}/pdf`; // Assuming you add a PDF export route
        }

        function exportToCSV(type) {
            const data = type === 'risk' ? currentRiskData : currentAlertData;
            const title = type === 'risk' ?
                document.getElementById('riskDetailModalLabel').textContent :
                document.getElementById('alertDetailModalLabel').textContent;

            // Extract the category from the modal title
            const categoryMatch = title.match(/Risk Details - (.+)/);
            const category = categoryMatch ? categoryMatch[1].toLowerCase().replace(' risk', '') : 'all'; // Default to 'all' or handle error

            if (!data || data.length === 0) {
                alert('No data available to export');
                return;
            }

            // Redirect to the Laravel export route
            window.location.href = `/risk/export/${encodeURIComponent(category)}/csv`; // Assuming you add a CSV export route
        }

        function showEventDetails(item) {
            const detailsContent = document.getElementById('eventDetailsContent');

            // --- Placeholder for generating a simple individual solution ---
            let individualSolution = "Review the alert details and take appropriate action."; // Default solution
            if (item.severity === 'high') {
                individualSolution = "Investigate this high-severity alert immediately. Isolate the affected system if necessary.";
            } else if (item.severity === 'medium') {
                individualSolution = "Assess this medium-severity alert. Determine if it requires immediate action or can be scheduled.";
            } else if (item.severity === 'low'){
                 individualSolution = "Monitor this low-severity alert. It might indicate potential issues if it occurs frequently.";
            }
            // You could add more complex logic here based on item.category or item.description
            // --- End Placeholder ---


            let detailsHtml = `
                <div class="event-details">
                    <div class="detail-section">
                        <h6 class="section-title">Event Information</h6>
                        <div class="detail-row">
                            <span class="detail-label">Event Type:</span>
                            <span class="detail-value">${item.category || '-'}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Severity:</span>
                            <span class="detail-value">
                                <span class="badge bg-${item.severity === 'high' ? 'danger' : item.severity === 'medium' ? 'warning' : 'success'}">${item.severity || '-'}</span>
                            </span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Time:</span>
                            <span class="detail-value">${item.date ? new Date(item.date).toLocaleString() : (item.raisedAt ? new Date(item.raisedAt).toLocaleString() : '-')}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Description:</span>
                            <span class="detail-value">${item.description || '-'}</span>
                        </div>
                    </div>

                    <div class="detail-section">
                        <h6 class="section-title">Location & Source</h6>
                        <div class="detail-row">
                            <span class="detail-label">Location:</span>
                            <span class="detail-value">${item.location || '-'}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Source:</span>
                            <span class="detail-value">${item.source || '-'}</span>
                        </div>
                    </div>

                    <div class="detail-section">
                        <h6 class="section-title">Endpoint Information</h6>
                        <div class="detail-row">
                            <span class="detail-label">Endpoint Type:</span>
                            <span class="detail-value">${item.endpoint_type || '-'}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Endpoint ID:</span>
                            <span class="detail-value">${item.endpoint_id || '-'}</span>
                        </div>
                    </div>

                     <div class="detail-section">
                        <h6 class="section-title">Recommended Action</h6>
                        <div class="detail-row">
                            <span class="detail-value">${individualSolution}</span> // Display the generated solution
                        </div>
                    </div>
                </div>
            `;

            detailsContent.innerHTML = detailsHtml;

            const eventDetailsModal = new bootstrap.Modal(document.getElementById('eventDetailsModal'));
            eventDetailsModal.show();
        }

        // Ensure your formatDate function handles both 'date' and 'raisedAt'
        function formatDate(dateString) {
            if (!dateString) return '-';
             // Try parsing as ISO string first (common for Sophos API)
            try {
                const date = new Date(dateString);
                if (!isNaN(date)) {
                     return date.toLocaleString();
                }
            } catch (e) {
                // Fallback if parsing fails
            }
            // Try parsing as a simple date string
             try {
                const date = new Date(dateString.replace(/[-]/g, '/')); // Replace hyphens with slashes for better parsing support
                if (!isNaN(date)) {
                     return date.toLocaleString();
                }
            } catch (e) {
                return '-'; // Return hyphen if all parsing fails
            }
            return '-';
        }

        // Helper functions
        function getSeverityClass(severity) {
            switch(severity?.toLowerCase()) {
                case 'high': return 'badge-high bg-danger';
                case 'medium': return 'badge-medium bg-warning';
                case 'low': return 'badge-low bg-success';
                default: return 'badge-secondary bg-secondary';
            }
        }

        // Variable untuk melacak filter yang aktif
        const activeFilters = {
            highRisk: true,
            mediumRisk: true,
            lowRisk: true
        };

        // Fungsi untuk mengalihkan filter risiko
        function toggleRiskFilter(riskType) {
            console.log(`Toggling filter for: ${riskType}`); // Debug log

            // Toggle status filter
            activeFilters[riskType] = !activeFilters[riskType];

            // Update indikasi visual
            const filterElement = document.querySelector(`.${riskType.replace(/([A-Z])/g, '-$1').toLowerCase()}-filter`);
            if (filterElement) {
                if (activeFilters[riskType]) {
                    filterElement.classList.add('active');
                } else {
                    filterElement.classList.remove('active');
                }
            }

            // Update traffic chart dengan dataset yang sesuai
            updateTrafficChartFilters();

            // Update tampilan status filter
            updateFilterStatus();
        }

        // Fungsi untuk memperbarui filter pada traffic chart
        function updateTrafficChartFilters() {
            const trafficChart = Chart.getChart('trafficRiskChart');
            if (!trafficChart) {
                console.error('Traffic chart not found!');
                return;
            }

            // Update visibilitas dataset dalam chart
            trafficChart.data.datasets.forEach((dataset, index) => {
                if (dataset.label === 'High Risk') {
                    dataset.hidden = !activeFilters.highRisk;
                } else if (dataset.label === 'Medium Risk') {
                    dataset.hidden = !activeFilters.mediumRisk;
                } else if (dataset.label === 'Low Risk') {
                    dataset.hidden = !activeFilters.lowRisk;
                }
            });

            trafficChart.update();
        }

        // Fungsi untuk memperbarui tampilan status filter
        function updateFilterStatus() {
            const statusElement = document.getElementById('filterStatus');
            if (!statusElement) return;

            statusElement.innerHTML = '';

            const activeFilterCount = Object.values(activeFilters).filter(v => v).length;

            if (activeFilterCount === 3) {
                // Semua filter aktif
                statusElement.innerHTML = '<span class="badge rounded-pill bg-light text-dark me-2 mb-2">All data shown</span>';
            } else {
                // Tampilkan filter yang aktif
                if (activeFilters.highRisk) {
                    statusElement.innerHTML += '<span class="badge rounded-pill bg-danger text-white me-2 mb-2">High Risk</span>';
                }
                if (activeFilters.mediumRisk) {
                    statusElement.innerHTML += '<span class="badge rounded-pill bg-warning text-dark me-2 mb-2">Medium Risk</span>';
                }
                if (activeFilters.lowRisk) {
                    statusElement.innerHTML += '<span class="badge rounded-pill bg-success text-white me-2 mb-2">Low Risk</span>';
                }

                if (activeFilterCount === 0) {
                    statusElement.innerHTML += '<span class="badge rounded-pill bg-secondary text-white me-2 mb-2">No filters active</span>';
                }
            }
        }

        // Fungsi untuk inisialisasi filter legend kustom
        function initializeCustomLegendFilters() {
            // Pastikan chart sudah ada
            const trafficChart = Chart.getChart('trafficRiskChart');
            if (!trafficChart) return;

            // Setel status awal filter
            Object.keys(activeFilters).forEach(filter => {
                const filterElement = document.querySelector(`.${filter.replace(/([A-Z])/g, '-$1').toLowerCase()}-filter`);
                if (filterElement) {
                    filterElement.classList.add('active');
                }
            });

            // Setel tampilan status awal
            updateFilterStatus();
        }

        // Traffic Risk Chart initialization dengan penyesuaian baru
        async function initializeTrafficRiskChart() {
            try {
                const ctx = document.getElementById('trafficRiskChart').getContext('2d');

                // Destroy existing chart if any
                if (window.trafficChart) {
                    window.trafficChart.destroy();
                }

                const response = await fetch('/traffic-risk/weekly');
                const jsonData = await response.json();

                if (!jsonData.success || !jsonData.data) {
                    throw new Error('Invalid data format received');
                }

                const chartData = jsonData.data;

                window.trafficChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: chartData.map(item => item.month),
                        datasets: [
                            {
                                label: 'High Risk',
                                data: chartData.map(item => item.highRisk),
                                borderColor: '#dc3545',
                                backgroundColor: 'rgba(220, 53, 69, 0.1)',
                                fill: true,
                                tension: 0.4,
                                borderWidth: 2,
                                pointRadius: 6,
                                pointHoverRadius: 10,
                                pointStyle: 'circle'
                            },
                            {
                                label: 'Medium Risk',
                                data: chartData.map(item => item.mediumRisk),
                                borderColor: '#ffc107',
                                backgroundColor: 'rgba(255, 193, 7, 0.1)',
                                fill: true,
                                tension: 0.4,
                                borderWidth: 2,
                                pointRadius: 6,
                                pointHoverRadius: 10,
                                pointStyle: 'circle'
                            },
                            {
                                label: 'Low Risk',
                                data: chartData.map(item => item.lowRisk),
                                borderColor: '#28a745',
                                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                                fill: true,
                                tension: 0.4,
                                borderWidth: 2,
                                pointRadius: 6,
                                pointHoverRadius: 10,
                                pointStyle: 'circle'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false
                        },
                        plugins: {
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                                callbacks: {
                                    title: function(tooltipItems) {
                                        return `${tooltipItems[0].label} - Risk Analysis`;
                                    },
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        label += context.parsed.y;
                                        return label + ' alerts';
                                    },
                                    footer: function() {
                                        return 'Click for detailed information';
                                    }
                                },
                                padding: 10,
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                titleFont: {
                                    size: 14,
                                    weight: 'bold'
                                },
                                bodyFont: {
                                    size: 13
                                },
                                footerFont: {
                                    size: 12,
                                    style: 'italic'
                                }
                            },
                            legend: {
                                display: false, // Sembunyikan legenda default
                            }
                        },
                        hover: {
                            mode: 'index',
                            intersect: false
                        },
                        onClick: async (e, elements) => {
                            if (elements.length > 0) {
                                const index = elements[0].index;
                                const month = chartData[index].month;
                                await showMonthlyDetails(month);
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)',
                                    borderDash: [5, 5]
                                },
                                ticks: {
                                    font: {
                                        size: 11
                                    },
                                    padding: 8
                                },
                                title: {
                                    display: true,
                                    text: 'Number of Alerts',
                                    font: {
                                        size: 13,
                                        weight: 'bold'
                                    },
                                    padding: {top: 10, bottom: 10}
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        size: 11
                                    },
                                    padding: 8
                                },
                                title: {
                                    display: true,
                                    text: 'Month',
                                    font: {
                                        size: 13,
                                        weight: 'bold'
                                    },
                                    padding: {top: 10, bottom: 10}
                                }
                            }
                        },
                        animation: {
                            duration: 1000,
                            easing: 'easeOutQuart'
                        }
                    }
                });

                // Setelah chart dibuat, inisialisasi filter custom
                initializeCustomLegendFilters();

            } catch (error) {
                console.error('Error initializing chart:', error);
                const chartContainer = document.querySelector('.chart-container');
                if (chartContainer) {
                    chartContainer.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Error loading chart data. Please try refreshing the page.
                        </div>
                    `;
                }
            }
        }

        // Fungsi untuk mengelola notifikasi
        class AlertManager {
            constructor() {
                this.alerts = new Set();
                this.alertCount = 0;
            }

            // Menampilkan notifikasi baru
            showAlert(message, type = 'high') {
                const alertId = `alert-${Date.now()}`;
                const alertElement = document.createElement('div');
                alertElement.className = `alert-floating ${type}-alert`;
                alertElement.id = alertId;

                alertElement.innerHTML = `
                    <div class="alert-content">
                        <i class="fas fa-exclamation-triangle alert-icon"></i>
                        <div class="alert-message">
                            <strong>Security Alert!</strong>
                            <p>${message}</p>
                        </div>
                        <button type="button" class="btn-close" onclick="alertManager.dismissAlert('${alertId}')"></button>
                    </div>
                `;

                document.body.appendChild(alertElement);
                this.alerts.add(alertId);
                this.updateAlertBadge();

                // Animasi masuk
                setTimeout(() => alertElement.classList.add('show'), 100);

                // Auto dismiss setelah 5 detik
                setTimeout(() => this.dismissAlert(alertId), 5000);
            }

            // Menghilangkan notifikasi
            dismissAlert(alertId) {
                const alertElement = document.getElementById(alertId);
                if (alertElement) {
                    alertElement.classList.remove('show');
                    setTimeout(() => {
                        alertElement.remove();
                        this.alerts.delete(alertId);
                        this.updateAlertBadge();
                    }, 300);
                }
            }

            // Update badge jumlah alert
            updateAlertBadge() {
                const alertNavLink = document.querySelector('.nav-link .fa-bell').parentElement;
                const existingBadge = alertNavLink.querySelector('.alert-badge');

                if (this.alerts.size > 0) {
                    if (!existingBadge) {
                        const badge = document.createElement('span');
                        badge.className = 'alert-badge';
                        badge.textContent = this.alerts.size;
                        alertNavLink.appendChild(badge);
                    } else {
                        existingBadge.textContent = this.alerts.size;
                    }
                } else if (existingBadge) {
                    existingBadge.remove();
                }
            }
        }

        // Inisialisasi Alert Manager
        const alertManager = new AlertManager();

        // Fungsi untuk memeriksa risiko tinggi
        function checkHighRisk(highRiskCount) {
            const highRiskChart = document.getElementById('highRiskChart');
            const highRiskCard = highRiskChart.closest('.dashboard-card');

            if (highRiskCount > 0) {
                highRiskCard.classList.add('high-risk-warning');
                alertManager.showAlert(`${highRiskCount} High Risk threats detected! Immediate attention required.`);
            } else {
                highRiskCard.classList.remove('high-risk-warning');
            }
        }

        // Polling untuk update data secara real-time
        function startRealTimeMonitoring() {
            setInterval(async () => {
                try {
                    const response = await fetch('/metrics');
                    const data = await response.json();

                    if (data.high > 0) {
                        checkHighRisk(data.high);
                    }

                    // Update chart data
                    updateChartData(data);
                } catch (error) {
                    console.error('Error fetching metrics:', error);
                }
            }, 30000); // Check setiap 30 detik
        }

        // Update data chart
        function updateChartData(data) {
            const charts = {
                'highRiskChart': data.high,
                'mediumRiskChart': data.medium,
                'lowRiskChart': data.low
            };

            Object.entries(charts).forEach(([chartId, value]) => {
                const chart = Chart.getChart(chartId);
                if (chart) {
                    chart.data.datasets[0].data = [value, data.total - value];
                    chart.update();
                }
            });
        }

        // Initialize everything when document is ready
        document.addEventListener('DOMContentLoaded', () => {
            // Initialize existing features
            AOS.init({
                duration: 800,
                once: true
            });

            // Initialize tooltips
            const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            tooltips.forEach(tooltip => {
                new bootstrap.Tooltip(tooltip);
            });

            // Add click handlers for legend items directly
            const legendItems = document.querySelectorAll('.legend-item');
            legendItems.forEach(item => {
                const classes = Array.from(item.classList);
                const filterClass = classes.find(cls => cls.includes('-filter'));
                if (filterClass) {
                    const riskType = filterClass.replace('-filter', '').replace(/-([a-z])/g, (_, letter) => letter.toUpperCase());

                    // Ganti dengan element baru untuk menghindari event handler berganda
                    const newItem = item.cloneNode(true);
                    item.parentNode.replaceChild(newItem, item);

                    // Tambahkan event listener baru
                    newItem.addEventListener('click', function(event) {
                        event.preventDefault();
                        console.log(`Legend clicked: ${riskType}`);

                        let riskTypeFormatted;
                        if (riskType === 'highRisk' || riskType === 'high-risk') {
                            riskTypeFormatted = 'highRisk';
                        } else if (riskType === 'mediumRisk' || riskType === 'medium-risk') {
                            riskTypeFormatted = 'mediumRisk';
                        } else {
                            riskTypeFormatted = 'lowRisk';
                        }

                        toggleRiskFilter(riskTypeFormatted);
                    });
                }
            });

            // Initialize traffic risk chart
            initializeTrafficRiskChart();

            // Start real-time monitoring
            startRealTimeMonitoring();

            // Initial high risk check
            const initialHighRiskCount = parseInt(document.getElementById('highRiskChart').getAttribute('data-value') || '0');
            checkHighRisk(initialHighRiskCount);
        });
    </script>
</body>
</html>
