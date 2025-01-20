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

        .navbar {
            background-color: var(--primary-color);
            padding: 15px 50px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .navbar.scrolled {
            padding: 10px 50px;
            background-color: rgba(0, 59, 123, 0.95);
            backdrop-filter: blur(10px);
        }

        .navbar .navbar-brand {
            color: white;
            font-size: 22px;
            font-weight: 600;
            transition: transform 0.3s ease;
        }

        .navbar .navbar-brand:hover {
            transform: scale(1.05);
        }

        .navbar .nav-link {
            color: white !important;
            margin: 0 10px;
            position: relative;
            transition: all 0.3s ease;
        }

        .navbar .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            background: var(--secondary-color);
            bottom: -5px;
            left: 0;
            transition: width 0.3s ease;
        }

        .navbar .nav-link:hover::after {
            width: 100%;
        }

        .metric-card {
            background: linear-gradient(145deg, #ffffff, #f5f5f5);
            border-radius: 16px;
            padding: 25px;
            text-align: center;
            box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.1),
                -5px -5px 15px rgba(255, 255, 255, 0.8);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
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

        /* Animation keyframes */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes scaleIn {
            from {
                transform: scale(0.9);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .animate-fadeInUp {
            animation: fadeInUp 0.6s ease-out;
        }

        .animate-scaleIn {
            animation: scaleIn 0.5s ease-out;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-shield-alt me-2"></i>
                Pertamina Sophos
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="#">
                    <i class="fas fa-bell"></i> Notifications
                </a>
                <a class="nav-link" href="#">
                    <i class="fas fa-layer-group"></i> Category
                </a>
                <a class="nav-link" href="#">
                    <i class="fas fa-chart-bar"></i> Reports
                </a>
                <!-- Logout Form -->
                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="nav-link btn btn-link text-white">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <!-- Summary Metrics -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="metric-card">
                    <i class="fas fa-chart-line fa-2x" style="color: var(--primary-color)"></i>
                    <div class="metric-value">20</div>
                    <div class="metric-label">Low Risk</div>
                    <div class="metric-trend trend-up">
                        <i class="fas fa-arrow-up"></i> 37% this week
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
                <div class="metric-card">
                    <i class="fas fa-users fa-2x" style="color: var(--secondary-color)"></i>
                    <div class="metric-value">10</div>
                    <div class="metric-label">High Risk</div>
                    <div class="metric-trend trend-down">
                        <i class="fas fa-arrow-down"></i> 21% this week
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
                <div class="metric-card">
                    <i class="fas fa-percentage fa-2x" style="color: var(--success-color)"></i>
                    <div class="metric-value">4.7%</div>
                    <div class="metric-label">Traffic Overview</div>
                    <div class="metric-trend trend-up">
                        <i class="fas fa-arrow-up"></i> 41% this week
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="400">
                <div class="metric-card">
                    <i class="fas fa-clock fa-2x" style="color: var(--warning-color)"></i>
                    <div class="metric-value">6m 18s</div>
                    <div class="metric-label">Avg. Time on Site</div>
                    <div class="metric-trend trend-down">
                        <i class="fas fa-arrow-down"></i> 15% this week
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

    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            once: true
        });

        // Navbar Scroll Effect
        window.addEventListener('scroll', function () {
            if (window.scrollY > 50) {
                document.querySelector('.navbar').classList.add('scrolled');
            } else {
                document.querySelector('.navbar').classList.remove('scrolled');
            }
        });

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
