<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - PeSo (Pertamina Sophos)</title>
    <!-- CSS and Script imports -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">

    <style>
        /* Root Variables */
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

        /* New Styles for Dashboard */
        .dashboard-container {
            padding: 20px;
        }

        .dashboard-header {
            display: flex;
            align-items: center;
            margin-bottom: 24px;
            padding: 16px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .dashboard-title {
            font-size: 24px;
            color: var(--primary-blue);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            margin-bottom: 24px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background: white;
            border-bottom: 1px solid #eee;
            padding: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .card-title {
            color: var(--primary-blue);
            margin: 0;
            font-size: 18px;
            font-weight: 600;
        }

        .threat-table {
            width: 100%;
            border-collapse: collapse;
        }

        .threat-table th {
            background: var(--bg-light);
            padding: 12px 16px;
            text-align: left;
            font-weight: 600;
            color: var(--text-dark);
        }

        .threat-table td {
            padding: 12px 16px;
            border-top: 1px solid #eee;
            color: var(--text-light);
        }

        .priority-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }

        .priority-high {
            background: #fee2e2;
            color: #dc2626;
        }

        .priority-low {
            background: #ecfdf5;
            color: #059669;
        }

        .chart-container {
            position: relative;
            width: 400px;
            height: 400px;
            margin: 0 auto;
        }

        .chart-center-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 10;
            text-align: center;
        }

        .chart-value {
            font-size: 48px;
            font-weight: 700;
            color: var(--text-dark);
        }

        .metrics-list {
            margin-top: 2rem;
            padding: 0 2rem;
        }

        .metric-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid #eee;
        }

        .metric-row:last-child {
            border-bottom: none;
        }

        .metric-label {
            color: var(--text-light);
        }

        .metric-value {
            font-weight: 500;
        }

        .metric-value.danger {
            color: #dc2626;
        }

        @media (max-width: 768px) {
            .chart-container {
                width: 300px;
                height: 300px;
            }
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-shield-alt"></i>
                <span>Pertamina Sophos</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
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
                    <li class="nav-item dropdown">
                        <a class="nav-link" href="#" data-bs-toggle="dropdown">
                            <i class="fas fa-box"></i>
                            <span>My Products</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Active Products</a></li>
                            <li><a class="dropdown-item" href="#">Settings</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link" href="#" data-bs-toggle="dropdown">
                            <i class="fas fa-shield-virus"></i>
                            <span>Threat Analysis</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Current Threats</a></li>
                            <li><a class="dropdown-item" href="#">Reports</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-bell"></i>
                            <span>Alerts</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-file-alt"></i>
                            <span>Reports</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-cog"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Dashboard Content -->
    <div class="dashboard-container">
        <!-- Recent Threats Card -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-exclamation-triangle text-warning"></i>
                <h2 class="card-title">Recent threat graphs</h2>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="threat-table">
                        <thead>
                            <tr>
                                <th>Time created</th>
                                <th>Priority</th>
                                <th>Name</th>
                                <th>User</th>
                                <th>Device</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Feb 6, 2025 10:49 AM</td>
                                <td><span class="priority-badge priority-high">High</span></td>
                                <td>TrojAgent-TWK</td>
                                <td>PERTAMINA\nik.sulistiani.ptg</td>
                                <td>HQ-Notebook-80</td>
                            </tr>
                            <tr>
                                <td>Feb 5, 2025 9:44 AM</td>
                                <td><span class="priority-badge priority-low">Low</span></td>
                                <td>Mal/HTMLGen-A</td>
                                <td>PTG-DESKTOP-252\ms.serpong</td>
                                <td>PTG-DESKTOP-252</td>
                            </tr>
                            <tr>
                                <td>Feb 5, 2025 8:48 AM</td>
                                <td><span class="priority-badge priority-low">Low</span></td>
                                <td>Troj/Lnk-I</td>
                                <td>PERTAMINA\mk.nofriyanto.s</td>
                                <td>DESKTOP-040G743</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Devices Summary -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-desktop text-primary"></i>
                        <h2 class="card-title">Devices and users: summary</h2>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="deviceChart"></canvas>
                            <div class="chart-center-text">
                                <div class="chart-value">640</div>
                            </div>
                        </div>
                        <div class="metrics-list">
                            <div class="metric-row">
                                <span class="metric-label">Active</span>
                                <span class="metric-value">599</span>
                            </div>
                            <div class="metric-row">
                                <span class="metric-label">Inactive 2+ Weeks</span>
                                <span class="metric-value">37</span>
                            </div>
                            <div class="metric-row">
                                <span class="metric-label">Not Protected</span>
                                <span class="metric-value danger">4</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Web Control -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-globe text-primary"></i>
                        <h2 class="card-title">Web control</h2>
                    </div>
                    <div class="card-body">
                        <div class="web-stats-grid">
                            <div class="web-stat-box">
                                <div class="web-stat-value">0</div>
                                <div class="web-stat-label">Web Threats Blocked</div>
                            </div>
                            <div class="web-stat-box">
                                <div class="web-stat-value">718</div>
                                <div class="web-stat-label">Policy Violations Blocked</div>
                            </div>
                            <div class="web-stat-box">
                                <div class="web-stat-value">41294</div>
                                <div class="web-stat-label">Policy Warnings Issued</div>
                            </div>
                            <div class="web-stat-box">
                                <div class="web-stat-value">41230</div>
                                <div class="web-stat-label">Policy Warnings Proceeded</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Initialize device chart
        const ctx = document.getElementById('deviceChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [599, 37, 4],
                    backgroundColor: [
                        '#4CAF50',  // Active
                        '#FFC107',  // Inactive
                        '#F44336'   // Not Protected
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                cutout: '70%',
                plugins: {
                    legend: {
                        display: false
                    }
                },
                responsive: true,
                maintainAspectRatio: true
            }
        });
    </script>
</body>

</html>
