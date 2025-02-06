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

        .modal-dialog.modal-lg {
    max-width: 90%; /* Memperbesar ukuran modal */
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
    padding: 0.75rem 1.5rem; /* Mengurangi padding */
}

.modal-body {
    padding: 1rem; /* Mengurangi padding */
}

/* Table Styles dalam Modal */
.modal .table {
    margin-bottom: 0;
    font-size: 0.85rem; /* Memperkecil ukuran font tabel */
}

.modal .table th {
    background-color: #f8f9fa;
    font-weight: 600;
    white-space: nowrap; /* Mencegah wrapping pada header */
    padding: 0.5rem; /* Mengurangi padding */
}

.modal .table td {
    padding: 0.5rem;
    vertical-align: middle;
}

.modal .table td.description-cell {
    max-width: 400px; /* Mengatur lebar maksimum kolom deskripsi */
    white-space: normal; /* Mengizinkan text wrapping */
    word-break: break-word; /* Memastikan kata panjang bisa wrap */
}

/* Tambahkan style ini di bagian CSS */
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

.modal .table tbody tr {
    animation: slideInRight 0.5s ease-in-out;
    animation-fill-mode: both;
    transition: all 0.3s ease;
}

.modal .table tbody tr:hover {
    background-color: rgba(0, 59, 123, 0.05);
    transform: translateX(5px);
}

/* Animasi untuk modal */
.modal.fade .modal-dialog {
    transition: transform 0.3s ease-out;
    transform: scale(0.95);
}

.modal.show .modal-dialog {
    transform: scale(1);
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

        <!-- Traffic Overview -->
        <div class="dashboard-card" data-aos="fade-up">
            <h5><i class="fas fa-chart-area me-2"></i>TRAFFIC OVERVIEW</h5>
            <div class="chart-container">
                <canvas id="trafficChart"></canvas>
            </div>
        </div>

        @if(config('app.debug'))
        <div class="container mt-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">API Status</h5>
                </div>
                <div class="card-body">
                    <pre>{{ json_encode($riskData ?? [], JSON_PRETTY_PRINT) }}</pre>
                </div>
            </div>
        </div>
        @endif

    </div>

    <!-- Alert Details Modal -->
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
                                    <th width="15%">ID</th>
                                    <th width="10%">Category</th>
                                    <th width="40%">Description</th>
                                    <th width="10%">Severity</th>
                                    <th width="15%">Raised At</th>
                                    <th width="10%">Type</th>
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

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            once: true
        });

        // Function to populate the modal with data
        function fetchDetailData(category) {
    const tableBody = document.getElementById('detailTableBody');
    tableBody.innerHTML = '<tr><td colspan="6" class="text-center">Loading...</td></tr>';

    const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
    detailModal.show();

    fetch(`/alerts/${encodeURIComponent(category.toLowerCase())}`)
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => {
                    throw new Error(err.message || `HTTP error! status: ${response.status}`);
                });
            }
            return response.json();
        })
        .then(data => {
            tableBody.innerHTML = '';

            if (!data || data.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="6" class="text-center">No alerts found for this category</td></tr>';
                return;
            }

            data.forEach((item, index) => {
                const row = document.createElement('tr');
                // Tambahkan delay untuk animasi bertahap
                row.style.animationDelay = `${index * 0.1}s`;
                row.style.animationFillMode = 'both';
                
                let severityClass;
                switch(item.severity?.toLowerCase()) {
                    case 'high':
                        severityClass = 'badge-high bg-danger';
                        break;
                    case 'medium':
                        severityClass = 'badge-medium bg-warning';
                        break;
                    case 'low':
                        severityClass = 'badge-low bg-success';
                        break;
                    default:
                        severityClass = 'badge-secondary bg-secondary';
                }

                row.innerHTML = `
                    <td class="text-muted small">${item.id || '-'}</td>
                    <td>${item.category || '-'}</td>
                    <td class="description-cell">${item.description || '-'}</td>
                    <td><span class="badge ${severityClass}">${item.severity || '-'}</span></td>
                    <td class="small">${item.raisedAt ? new Date(item.raisedAt).toLocaleString() : '-'}</td>
                    <td class="small">${item.type || '-'}</td>
                `;

                // Tambahkan class untuk animasi
                row.classList.add('animated-row');
                tableBody.appendChild(row);
            });
        })
        .catch(error => {
            console.error('Error:', error);
            tableBody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center text-danger">
                        <div class="alert alert-danger mb-0">
                            <strong>Error:</strong> ${error.message}<br>
                            Please try again later or contact support if the problem persists.
                        </div>
                    </td>
                </tr>
            `;
        });
}

        // Chart.js Configuration
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

        // Calculate percentages based on actual data
        const totalRisk = {{ $riskData['total'] ?? 0 }};
        const highRisk = {{ $riskData['high'] ?? 0 }};
        const mediumRisk = {{ $riskData['medium'] ?? 0 }};
        const lowRisk = {{ $riskData['low'] ?? 0 }};

        // Create doughnut charts
        new Chart(document.getElementById('lowRiskChart'), {
            type: 'doughnut',
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

        new Chart(document.getElementById('highRiskChart'), {
            type: 'doughnut',
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
    </script>
</body>
</html>