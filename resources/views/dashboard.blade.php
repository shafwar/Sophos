<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - PeSo (Pertagas Sophos)</title>
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
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container-fluid">
            <!-- Brand -->
            <a class="navbar-brand" href="#">
                <i class="fas fa-shield-alt"></i>
                <span>Pertagas Sophos</span>
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
            <h5><i class="fas fa-chart-area me-2"></i>TRAFFIC RISK OVERVIEW</h5>
            <div class="chart-container">
                <canvas id="trafficRiskChart"></canvas>
            </div>
        </div>

    </div>

<!-- Risk Details Modal -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">Risk Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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

<!-- Event Details Modal -->
<div class="modal fade" id="eventDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Event Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="eventDetailsContent">
                    <!-- Details will be inserted here -->
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

        function fetchDetailData(category) {
    const tableBody = document.getElementById('detailTableBody');
    const modalTitle = document.getElementById('detailModalLabel');
    
    // Update modal title
    modalTitle.textContent = `Risk Details - ${category}`;
    
    // Show loading state
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

    const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
    detailModal.show();

    fetch(`/alerts/${encodeURIComponent(category.toLowerCase())}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(response => {
            tableBody.innerHTML = '';

            const data = response.data || [];
            
            if (data.length === 0) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center">
                            No alerts found for ${category}
                        </td>
                    </tr>`;
                return;
            }

            data.forEach((item, index) => {
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

                // Add click event listener for View Details button
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
                            <strong>Error:</strong> An error occurred while fetching the data.<br>
                            Please try again later or contact support if the problem persists.
                        </div>
                    </td>
                </tr>
            `;
        });
}

// Helper function untuk mendapatkan class severity badge
function getSeverityClass(severity) {
    switch(severity?.toLowerCase()) {
        case 'high':
            return 'badge-high bg-danger';
        case 'medium':
            return 'badge-medium bg-warning';
        case 'low':
            return 'badge-low bg-success';
        default:
            return 'badge-secondary bg-secondary';
    }
}

// Helper function untuk format tanggal
function formatDate(dateString) {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleString();
}

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

        new Chart(document.getElementById('highRiskChart'), {
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

        // Tambahkan fungsi untuk fetch data traffic risk
async function fetchTrafficRiskData() {
    try {
        const response = await fetch('/traffic-risk/weekly');
        if (!response.ok) throw new Error('Failed to fetch data');
        const data = await response.json();
        return data.data;
    } catch (error) {
        console.error('Error fetching traffic data:', error);
        return [];
    }
}

// Fungsi untuk menampilkan modal detail
async function showRiskDetails(month, riskLevel) {
    try {
        const response = await fetch(`/traffic-risk/details/${month}/${riskLevel}`);
        if (!response.ok) throw new Error('Failed to fetch details');
        const details = await response.json();
        
        // Update konten modal
        const modalTitle = document.getElementById('detailModalLabel');
        const modalBody = document.getElementById('detailTableBody');
        
        modalTitle.textContent = `${riskLevel.charAt(0).toUpperCase() + riskLevel.slice(1)} Risk Details - ${month}`;
        
        modalBody.innerHTML = details.incidents.map(incident => `
            <tr>
                <td class="text-muted small">${incident.id || '-'}</td>
                <td>${incident.category || '-'}</td>
                <td class="description-cell">${incident.description?.split('\n')[0] || '-'}</td>
                <td>
                    <span class="badge ${getBadgeClass(riskLevel)}">
                        ${riskLevel.toUpperCase()}
                    </span>
                </td>
                <td class="small">${formatDate(incident.date)}</td>
                <td>
                    <button class="btn btn-sm btn-info" onclick="showEventDetails(${JSON.stringify(incident)})">
                        View Details
                    </button>
                </td>
            </tr>
        `).join('');
        
        // Tampilkan modal
        const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
        detailModal.show();
    } catch (error) {
        console.error('Error fetching risk details:', error);
    }
}

// Fungsi helper untuk badge class
function getBadgeClass(riskLevel) {
    switch(riskLevel.toLowerCase()) {
        case 'high': return 'badge-high bg-danger';
        case 'medium': return 'badge-medium bg-warning';
        case 'low': return 'badge-low bg-success';
        default: return 'badge-secondary bg-secondary';
    }
}

// Fungsi untuk format tanggal
function formatDate(dateString) {
    return new Date(dateString).toLocaleString();
}

async function initializeTrafficRiskChart() {
    try {
        const ctx = document.getElementById('trafficRiskChart').getContext('2d');
        
        // Destroy existing chart if any
        if (window.trafficChart) {
            window.trafficChart.destroy();
        }

        // Fetch data from API
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
                        intersect: false
                    }
                },
                onClick: async (e, elements) => {
                    if (elements.length > 0) {
                        const index = elements[0].index;
                        const month = chartData[index].month;
                        await showMonthDetails(month);
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    } catch (error) {
        console.error('Error initializing chart:', error);
    }
}

// Function to show risk details
async function showRiskDetails(month, riskLevel) {
    try {
        const response = await fetch(`/traffic-risk/details/${month}/${riskLevel}`);
        if (!response.ok) {
            throw new Error('Failed to fetch details');
        }
        const details = await response.json();
        
        // Update modal content
        const modalTitle = document.getElementById('detailModalLabel');
        const modalBody = document.getElementById('detailTableBody');
        
        modalTitle.textContent = `${riskLevel.charAt(0).toUpperCase() + riskLevel.slice(1)} Risk Details - ${month}`;
        
        modalBody.innerHTML = details.incidents.map(incident => `
            <tr>
                <td class="text-muted small">${incident.id || '-'}</td>
                <td>${incident.category || '-'}</td>
                <td class="description-cell">${incident.description?.split('\n')[0] || '-'}</td>
                <td>
                    <span class="badge ${getBadgeClass(riskLevel)}">
                        ${riskLevel.toUpperCase()}
                    </span>
                </td>
                <td class="small">${formatDate(incident.date)}</td>
                <td>
                    <button class="btn btn-sm btn-info" onclick="showEventDetails(${JSON.stringify(incident)})">
                        View Details
                    </button>
                </td>
            </tr>
        `).join('');
        
        // Show modal
        const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
        detailModal.show();
    } catch (error) {
        console.error('Error fetching risk details:', error);
    }
}

function showEventDetails(item) {
    const detailsContent = document.getElementById('eventDetailsContent');
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
                    <span class="detail-value">${item.date ? new Date(item.date).toLocaleString() : '-'}</span>
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
        </div>
    `;

    detailsContent.innerHTML = detailsHtml;

    const eventDetailsModal = new bootstrap.Modal(document.getElementById('eventDetailsModal'));
    eventDetailsModal.show();
}

// Helper function for badge classes
function getBadgeClass(riskLevel) {
    switch(riskLevel.toLowerCase()) {
        case 'high': return 'badge-high bg-danger';
        case 'medium': return 'badge-medium bg-warning';
        case 'low': return 'badge-low bg-success';
        default: return 'badge-secondary bg-secondary';
    }
}

// Helper function to format date
function formatDate(dateString) {
    return new Date(dateString).toLocaleString();
}

async function showMonthDetails(month) {
    try {
        const response = await fetch(`/traffic-risk/monthly-details/${month}`);
        if (!response.ok) throw new Error('Failed to fetch details');
        const details = await response.json();
        
        const modalTitle = document.getElementById('detailModalLabel');
        const modalBody = document.getElementById('detailTableBody');
        
        modalTitle.textContent = `Alert Details - ${month}`;
        
        if (!details.incidents || details.incidents.length === 0) {
            modalBody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center">No alerts found for ${month}</td>
                </tr>
            `;
        } else {
            modalBody.innerHTML = details.incidents.map(incident => `
                <tr>
                    <td class="text-muted small">${incident.id || '-'}</td>
                    <td>${incident.category || '-'}</td>
                    <td class="description-cell">${incident.description?.split('\n')[0] || '-'}</td>
                    <td>
                        <span class="badge ${getBadgeClass(incident.severity)}">
                            ${incident.severity?.toUpperCase() || '-'}
                        </span>
                    </td>
                    <td class="small">${formatDate(incident.date)}</td>
                    <td>
                        <button class="btn btn-sm btn-info" onclick='showEventDetails(${JSON.stringify(incident)})'>
                            View Details
                        </button>
                    </td>
                </tr>
            `).join('');
        }
        
        const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
        detailModal.show();
    } catch (error) {
        console.error('Error fetching month details:', error);
        alert('Failed to load month details. Please try again.');
    }
}

// Initialize when document is ready
document.addEventListener('DOMContentLoaded', initializeTrafficRiskChart);

    </script>
</body>
</html>
