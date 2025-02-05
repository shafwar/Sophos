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
        :root {
            --primary-color: #003B7B;
            --secondary-color: #00BFFF;
            --accent-color: #f4f7fc;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--accent-color);
            margin-top: 80px;
        }

        /* Navbar Styles */
        .navbar {
            background-color: #3845d5;
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

        /* Dropdown Styling */
        .dropdown-menu {
            background-color: white;
            border: none;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            animation: fadeInDown 0.3s ease;
            margin-top: 0.5rem;
        }

        .dropdown-item {
            padding: 0.7rem 1.2rem;
            transition: all 0.2s ease;
        }

        .dropdown-item:hover {
            background-color: #f5f5f5;
            transform: translateX(5px);
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
            color: var(--primary-color);
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
            padding: 1rem 1.5rem;
        }

        .modal-header .btn-close {
            filter: brightness(0) invert(1);
        }

        .table {
            margin-bottom: 0;
        }

        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }

        .table td,
        .table th {
            padding: 1rem;
            vertical-align: middle;
        }

        .btn-sm {
            padding: 0.25rem 0.75rem;
            font-size: 0.875rem;
            border-radius: 4px;
        }

        .badge-medium {
            background-color: #ffc107;
            color: #000;
            padding: 0.4em 0.8em;
            border-radius: 4px;
            font-weight: 500;
        }

        .badge-high {
            background-color: #dc3545;
            color: #fff;
            padding: 0.4em 0.8em;
            border-radius: 4px;
            font-weight: 500;
        }

        .badge-low {
            background-color: #28a745;
            color: #fff;
            padding: 0.4em 0.8em;
            border-radius: 4px;
            font-weight: 500;
        }

        /* Modal Table Row Animation */
        .modal .table tbody tr {
            transition: all 0.3s ease;
            animation: fadeIn 0.5s ease-in-out;
        }

        .modal .table tbody tr:hover {
            background-color: rgba(0, 59, 123, 0.05);
            transform: translateX(5px);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
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
                <span>Pertamina Sophos</span>
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
                            <li><a class="dropdown-item" href="#">Overview</a></li>
                            <li><a class="dropdown-item" href="#">Analytics</a></li>
                        </ul>
                    </li>

                    <!-- Products -->
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

                    <!-- Threat Analysis -->
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

                    <!-- Alerts -->
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-bell"></i>
                            <span>Alerts</span>
                        </a>
                    </li>

                    <!-- Reports -->
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-file-alt"></i>
                            <span>Reports</span>
                        </a>
                    </li>

                    <!-- User Icons -->
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-cog"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-user"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <!-- Summary Metrics -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="metric-card" onclick="fetchDetailData('All Risk')">
                    <i class="fas fa-chart-line fa-2x" style="color: var(--primary-color)"></i>
                    <div class="metric-value">124</div>
                    <div class="metric-label">Total Alerts</div>
                    <div class="metric-trend trend-up">
                        <i class="fas fa-arrow-up"></i> 37% this week
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
                <div class="metric-card" onclick="fetchDetailData('High Risk')">
                    <i class="fas fa-exclamation-triangle fa-2x" style="color: var(--danger-color)"></i>
                    <div class="metric-value">20</div>
                    <div class="metric-label">High Risk</div>
                    <div class="metric-trend trend-down">
                        <i class="fas fa-arrow-down"></i> 21% this week
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
                <div class="metric-card" onclick="fetchDetailData('Medium Risk')">
                    <i class="fas fa-exclamation-circle fa-2x" style="color: var(--warning-color)"></i>
                    <div class="metric-value">104</div>
                    <div class="metric-label">Medium Risk</div>
                    <div class="metric-trend trend-up">
                        <i class="fas fa-arrow-up"></i> 15% this week
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="400">
                <div class="metric-card" onclick="fetchDetailData('Low Risk')">
                    <i class="fas fa-info-circle fa-2x" style="color: var(--success-color)"></i>
                    <div class="metric-value">0</div>
                    <div class="metric-label">Low Risk</div>
                    <div class="metric-trend trend-down">
                        <i class="fas fa-arrow-down"></i> 0% this week
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

        <!-- Traffic Overview -->
        <div class="dashboard-card" data-aos="fade-up">
            <h5><i class="fas fa-chart-area me-2"></i>TRAFFIC OVERVIEW</h5>
            <div class="chart-container">
                <canvas id="trafficChart"></canvas>
            </div>
        </div>

        <!-- Conversion Rate -->
        <div class="dashboard-card" data-aos="fade-up">
            <h5><i class="fas fa-funnel-dollar me-2"></i>CONVERSION RATE BY CHANNELS</h5>
            <div class="chart-container">
                <canvas id="conversionChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Modal Template -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailModalLabel">Detail Information</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 15%">ID</th>
                                    <th style="width: 15%">Category</th>
                                    <th style="width: 30%">Description</th>
                                    <th style="width: 10%">Severity</th>
                                    <th style="width: 15%">Raised At</th>
                                    <th style="width: 15%">Type</th>
                                </tr>
                            </thead>
                            <tbody id="detailTableBody">
                                <!-- Data will be dynamically inserted here -->
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            once: true
        });

        // Function to populate the modal with data
        function fetchDetailData(category) {
            const data = [
                {
                    id: "3e1a006e-7508-443c-9e13-f65dc57f0e39",
                    category: "pua",
                    description: "Manual PUA cleanup required: 'PsExec' at 'D:\\3. Umum\\BACKUP DATA LAPTOP\\Data D\\99. Backup Data Separo\\Driver\\Drivers\\DP_Touchpad_Elan_13085.7z'",
                    severity: "Medium",
                    raisedAt: "2024-12-30T03:09:40.461Z",
                    type: "Event::Endpoint"
                },
                {
                    id: "ac0cd024-6815-48b1-8059-aad8b9e53f51",
                    category: "pua",
                    description: "Manual PUA cleanup required: 'PsKill' at 'C:\\Users\\faisal.ardhy\\Downloads\\PSTools.zip'",
                    severity: "High",
                    raisedAt: "2024-12-30T07:47:36.095Z",
                    type: "Event::Endpoint"
                }
            ];

            const tableBody = document.getElementById('detailTableBody');
            tableBody.innerHTML = '';

            data.forEach((item, index) => {
                const row = document.createElement('tr');
                row.style.animationDelay = `${index * 0.1}s`;
                row.innerHTML = `
                    <td class="text-muted">${item.id}</td>
                    <td>${item.category}</td>
                    <td>${item.description}</td>
                    <td><span class="badge badge-medium">${item.severity}</span></td>
                    <td>${new Date(item.raisedAt).toLocaleString()}</td>
                    <td>${item.type}</td>
                `;
                tableBody.appendChild(row);
            });

            const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
            detailModal.show();
        }
    </script>
</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Initialize AOS
    AOS.init({
        duration: 800,
        once: true
    });

    // Function to populate the modal with data
    function fetchDetailData(category) {
        const data = [
            {
                id: "3e1a006e-7508-443c-9e13-f65dc57f0e39",
                category: "pua",
                description: "Manual PUA cleanup required: 'PsExec' at 'D:\\3. Umum\\BACKUP DATA LAPTOP\\Data D\\99. Backup Data Separo\\Driver\\Drivers\\DP_Touchpad_Elan_13085.7z'",
                severity: "Medium",
                raisedAt: "2024-12-30T03:09:40.461Z",
                type: "Event::Endpoint"
            },
            {
                id: "ac0cd024-6815-48b1-8059-aad8b9e53f51",
                category: "pua",
                description: "Manual PUA cleanup required: 'PsKill' at 'C:\\Users\\faisal.ardhy\\Downloads\\PSTools.zip'",
                severity: "High",
                raisedAt: "2024-12-30T07:47:36.095Z",
                type: "Event::Endpoint"
            }
        ];

        const tableBody = document.getElementById('detailTableBody');
        tableBody.innerHTML = '';

        data.forEach((item, index) => {
            const row = document.createElement('tr');
            row.style.animationDelay = `${index * 0.1}s`;
            row.innerHTML = `
                <td class="text-muted">${item.id}</td>
                <td>${item.category}</td>
                <td>${item.description}</td>
                <td><span class="badge badge-${item.severity.toLowerCase()}">${item.severity}</span></td>
                <td>${new Date(item.raisedAt).toLocaleString()}</td>
                <td>${item.type}</td>
            `;
            tableBody.appendChild(row);
        });

        const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
        detailModal.show();
    }


        // Chart.js Global Configuration
        Chart.defaults.font.family = 'Poppins';
        Chart.defaults.color = '#666';
        Chart.defaults.plugins.tooltip.backgroundColor = 'rgba(0, 0, 0, 0.8)';
        Chart.defaults.plugins.tooltip.padding = 12;
        Chart.defaults.plugins.tooltip.titleFont = { size: 13 };
        Chart.defaults.plugins.tooltip.bodyFont = { size: 12 };

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

        // Low Risk Chart
        new Chart(document.getElementById('lowRiskChart'), {
            type: 'doughnut',
            data: {
                labels: ['Low Risk', 'Other'],
                datasets: [{
                    data: [70, 30],
                    backgroundColor: [
                        'rgba(40, 167, 69, 0.8)',
                        'rgba(233, 236, 239, 0.5)'
                    ],
                    borderWidth: 0
                }]
            },
            options: riskChartOptions
        });

        // Medium Risk Chart
        new Chart(document.getElementById('mediumRiskChart'), {
            type: 'doughnut',
            data: {
                labels: ['Medium Risk', 'Other'],
                datasets: [{
                    data: [50, 50],
                    backgroundColor: [
                        'rgba(255, 193, 7, 0.8)',
                        'rgba(233, 236, 239, 0.5)'
                    ],
                    borderWidth: 0
                }]
            },
            options: riskChartOptions
        });

        // High Risk Chart
        new Chart(document.getElementById('highRiskChart'), {
            type: 'doughnut',
            data: {
                labels: ['High Risk', 'Other'],
                datasets: [{
                    data: [30, 70],
                    backgroundColor: [
                        'rgba(220, 53, 69, 0.8)',
                        'rgba(233, 236, 239, 0.5)'
                    ],
                    borderWidth: 0
                }]
            },
            options: riskChartOptions
        });

        // Traffic Overview Chart
        new Chart(document.getElementById('trafficChart'), {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Traffic',
                    data: [1200, 1500, 1800, 2200, 2000, 2400],
                    borderColor: '#003B7B',
                    backgroundColor: 'rgba(0, 59, 123, 0.2)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3,
                    pointBackgroundColor: '#003B7B',
                    pointBorderWidth: 2,
                    pointHoverRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animations: {
                    tension: {
                        duration: 2000,
                        easing: 'easeInOutQuart',
                        from: 1,
                        to: 0.4,
                        loop: true
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            font: {
                                size: 14
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return `${context.raw.toLocaleString()} Visitors`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            font: {
                                size: 12
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 12
                            }
                        }
                    }
                }
            }
        });

        // Conversion Rate Chart
        new Chart(document.getElementById('conversionChart'), {
            type: 'bar',
            data: {
                labels: ['Email', 'Social Media', 'SEO', 'Direct'],
                datasets: [{
                    label: 'Conversion Rate',
                    data: [12, 19, 8, 15],
                    backgroundColor: [
                        'rgba(40, 167, 69, 0.8)',
                        'rgba(0, 191, 255, 0.8)',
                        'rgba(255, 193, 7, 0.8)',
                        'rgba(0, 59, 123, 0.8)'
                    ],
                    borderWidth: 0,
                    borderRadius: 6,
                    hoverBackgroundColor: [
                        'rgb(40, 167, 69)',
                        'rgb(0, 191, 255)',
                        'rgb(255, 193, 7)',
                        'rgb(0, 59, 123)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animations: {
                    y: {
                        duration: 2000,
                        easing: 'easeOutElastic'
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            font: {
                                size: 14
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return `${context.raw}% Conversion Rate`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            font: {
                                size: 12
                            },
                            callback: function (value) {
                                return value + '%';
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 12
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>

</html>
