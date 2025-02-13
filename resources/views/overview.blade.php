@extends('layouts.app')

@push('styles')
<style>
        /* Dashboard Styles */
        .dashboard-container {
            padding: 20px;
            margin-top: 20px;
        }

        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            margin-bottom: 24px;
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
            font-size: 16px;
            font-weight: 500;
        }

        /* Threat Table Styles */
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

        /* Chart Styles */
        .chart-container {
            position: relative;
            height: 300px;
            margin: 0 auto;
        }

        .chart-center-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }

        .chart-value {
            font-size: 32px;
            font-weight: 600;
            color: #333;
        }

        /* Metrics List Styles */
        .metrics-list {
            padding: 0 20px;
        }

        .metric-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .metric-row:last-child {
            border-bottom: none;
        }

        .metric-label {
            color: #666;
        }

        .metric-value {
            font-weight: 500;
        }

        /* Web Control Stats */
        .web-control-stats {
            padding: 20px;
        }

        .web-stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .web-stat-box {
            padding: 20px;
            text-align: center;
        }

        .web-stat-value {
            font-size: 28px;
            font-weight: 600;
            color: #1b258f;
            margin-bottom: 8px;
        }

        .web-stat-label {
            font-size: 13px;
            color: #666;
            line-height: 1.3;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .web-stats-grid {
                grid-template-columns: 1fr;
            }
        }

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
    </style>
    @endpush
</head>

@section('content')
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

        <!-- Summary and Web Control Row -->
        <div class="row">
            <!-- Device Summary -->
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
                                <span class="metric-value text-danger">4</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Web Control -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-globe text-primary me-2"></i>
                            <h2 class="card-title">Web control</h2>
                        </div>
                        <a href="#" class="text-primary text-decoration-none">See Reports</a>
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
                        <div class="text-end mt-3">
                            <small class="text-muted">last 30 days</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
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
                maintainAspectRatio: false
            }
        });
    }

        // Initialize dropdowns
        document.addEventListener('DOMContentLoaded', function() {
            // Enable Bootstrap dropdowns
            var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'))
            var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
                return new bootstrap.Dropdown(dropdownToggleEl)
            });

            // Add dropdown toggle functionality
            document.querySelectorAll('.nav-link').forEach(function(element) {
                if (element.hasAttribute('data-bs-toggle')) {
                    element.addEventListener('click', function(e) {
                        e.preventDefault();
                    });
                }
            });
        });
    </script>
@endpush
@endsection
