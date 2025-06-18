@extends('layouts.app')
@section('content')

<!-- Add Pertamina Gas logo in dashboard page navbar -->
@push('styles')
<style>
.navbar-brand img {
    height: 30px !important;
    object-fit: contain !important;
    margin-left: 8px;
}
</style>
@endpush

<!-- SiPandi Central Dashboard Header -->
<div class="container-fluid px-4 py-3 mt-4" style="background: white; border-bottom: 1px solid #e5e7eb;">
    <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <h2 class="mb-1" style="color: #1b258f; font-size: 1.75rem; font-weight: 600;">SIPANDI Central Dashboard</h2>
<img src="https://pertagas.pertamina.com/Static/pertagas/common/images/logo-pertagas-white.png" alt="PERTAMINA GAS" style="height: 40px; margin-left: 10px; object-fit: contain; border: none;">
        </div>
        <div class="text-end">
            <div>Role: <span class="fw-bold text-success" style="text-transform: capitalize;">{{ ucfirst(auth()->user()->role) }}</span></div>
            <div>Last Login: {{ auth()->user()->last_login_at ? auth()->user()->last_login_at->format('d M Y, H:i') : auth()->user()->created_at->format('d M Y, H:i') }}</div>
        </div>
    </div>
</div>

<!-- Pesan Selamat Datang User -->
<div class="container mt-5">
    <div class="alert alert-primary" role="alert">
        <h2>Selamat datang, {{ auth()->user()->name }}!</h2>
        <p>Ini adalah dashboard khusus user. Anda dapat melihat data monitoring antivirus.</p>
    </div>
</div>

<div class="container mt-5">
    <!-- Summary Metrics -->
    @if(auth()->user()->role === 'admin')
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
    @else
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
            <div class="metric-card" onclick="fetchUserDetailData('All Risk')">
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
            <div class="metric-card" onclick="fetchUserDetailData('High Risk')">
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
            <div class="metric-card" onclick="fetchUserDetailData('Medium Risk')">
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
            <div class="metric-card" onclick="fetchUserDetailData('Low Risk')">
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
    @endif

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

    <!-- Traffic Risk Overview -->
    <div class="dashboard-card traffic-risk-card" data-aos="fade-up">
            <h5 class="d-flex align-items-center">
                <i class="fas fa-chart-area me-2"></i>
                TRAFFIC RISK OVERVIEW
                <i class="fas fa-info-circle ms-2" data-bs-toggle="tooltip" title="Click on risk categories in the legend to filter data"></i>
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
        </div>
    </div>

    <!-- INFO SECTION untuk User
    @if(auth()->user()->role === 'user')
    <div class="container-fluid my-4">
        <div class="info-card text-center p-4" style="background: #f8f9fa; border-radius: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); width: 100%;">
            <i class="fas fa-shield-alt fa-4x text-primary mb-4"></i>
            <h3 class="text-primary mb-3">Security Monitoring Dashboard</h3>
            <p class="text-muted mb-4">
                You are viewing the security monitoring dashboard for your organization.<br>
                This dashboard provides real-time insights into your security posture and threat status.
            </p>
            <div class="alert alert-info" style="background: #d1f3fb;">
                <i class="fas fa-info-circle me-2"></i>
                <strong>User Access:</strong> You have read-only access to security metrics.<br>
                For administrative functions, please contact your system administrator.
            </div>
        </div>
    </div>
    @endif
</div> -->

<!-- Risk Details Modal -->
<div class="modal fade" id="riskDetailModal" tabindex="-1" aria-labelledby="riskDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="riskDetailModalLabel">Risk Details</h5>
                <div class="ms-auto">
                    @if(auth()->user() && auth()->user()->role === 'admin')
                    <a href="#" id="exportRiskExcelButton" class="btn btn-sm btn-outline-light">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </a>
                    @else
                    <button type="button" class="btn btn-sm btn-outline-light me-2" disabled title="Hanya admin yang bisa export">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-light" disabled title="Hanya admin yang bisa export">
                        <i class="fas fa-file-csv"></i> Export CSV
                    </button>
                    @endif
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
                    @if(auth()->user() && auth()->user()->role === 'admin')
                    {{-- Nonaktifkan atau ganti tombol Export PDF jika tidak ada backend PDF export --}}
                    {{-- <button type="button" class="btn btn-sm btn-outline-light me-2" onclick="exportToPDF('alert')">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </button> --}}
                    {{-- Ganti tombol Export CSV menjadi Export Excel dan panggil rute backend --}}
                    <a href="#" id="exportAlertExcelButton" class="btn btn-sm btn-outline-light">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </a>
                    @else
                    <button type="button" class="btn btn-sm btn-outline-light me-2" disabled title="Hanya admin yang bisa export">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-light" disabled title="Hanya admin yang bisa export">
                        <i class="fas fa-file-csv"></i> Export CSV
                    </button>
                    @endif
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

@push('styles')
<style>
/* Dashboard-specific styles only */
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

.high-risk-filter {
    border-left: 3px solid #dc3545;
}

.medium-risk-filter {
    border-left: 3px solid #ffc107;
}

.low-risk-filter {
    border-left: 3px solid #28a745;
}

.filter-status {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    margin-top: 15px;
    padding: 8px;
    background-color: rgba(0,0,0,0.02);
    border-radius: 8px;
}

.filter-label {
    display: flex;
    align-items: center;
    font-weight: 600;
    color: #495057;
    font-size: 0.95rem;
}

/* Event Details Styles */
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
.badge-high { color: #fff !important; }
.badge-medium { color: #000 !important; }
.badge-low { color: #fff !important; }
.badge-secondary { color: #fff !important; }

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
</style>
@endpush

@push('scripts')
<script>
// PASTIKAN INI DI PALING ATAS <script> DAN HANYA ADA SATU
function getSeverityClass(severity) {
    switch((severity || '').toLowerCase()) {
        case 'high': return 'badge-high bg-danger';
        case 'medium': return 'badge-medium bg-warning';
        case 'low': return 'badge-low bg-success';
        default: return 'badge-secondary bg-secondary';
    }
}

// Ensure this function is in the global scope
function formatDate(dateString) {
    if (!dateString) return '-';
    try {
        const date = new Date(dateString);
        if (!isNaN(date)) {
             return date.toLocaleString();
        }
    } catch (e) {
        // Fallback if parsing fails
    }
    try {
        const date = new Date(dateString.replace(/[-]/g, '/'));
        if (!isNaN(date)) {
             return date.toLocaleString();
        }
    } catch (e) {
        return '-';
    }
    return '-';
}

// Dashboard-specific JavaScript code
document.addEventListener('DOMContentLoaded', function() {
    // Data storage variables
    let currentRiskData = [];
    let currentAlertData = [];

    // Chart.js Configuration
    Chart.defaults.font.family = 'Poppins';
    Chart.defaults.color = '#666';

    // Center text plugin for donut charts
    const centerTextPlugin = {
        id: 'centerText',
        afterDraw: (chart, args, options) => {
            const { ctx, chartArea: { left, right, top, bottom, width, height } } = chart;
            ctx.save();

            const value = chart.data.datasets[0].data[0];
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.font = 'bold 24px Poppins';

            if (chart.canvas.id === 'lowRiskChart') {
                ctx.fillStyle = 'rgba(40, 167, 69, 0.8)';
            } else if (chart.canvas.id === 'mediumRiskChart') {
                ctx.fillStyle = 'rgba(255, 193, 7, 0.8)';
            } else {
                ctx.fillStyle = 'rgba(220, 53, 69, 0.8)';
            }

            ctx.fillText(value, left + width / 2, top + height / 2);
            ctx.restore();
        }
    };

    // Calculate data from PHP
    const totalRisk = {{ $riskData['total'] ?? 0 }};
    const highRisk = {{ $riskData['high'] ?? 0 }};
    const mediumRisk = {{ $riskData['medium'] ?? 0 }};
    const lowRisk = {{ $riskData['low'] ?? 0 }};

    // Chart options
    const riskChartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '75%',
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 20,
                    font: { size: 12 }
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

    // Create charts
    new Chart(document.getElementById('lowRiskChart'), {
        type: 'doughnut',
        plugins: [centerTextPlugin],
        data: {
            labels: ['Low Risk', 'Other'],
            datasets: [{
                data: [lowRisk, totalRisk - lowRisk],
                backgroundColor: ['rgba(40, 167, 69, 0.8)', 'rgba(233, 236, 239, 0.5)'],
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
                backgroundColor: ['rgba(255, 193, 7, 0.8)', 'rgba(233, 236, 239, 0.5)'],
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
                backgroundColor: ['rgba(220, 53, 69, 0.8)', 'rgba(233, 236, 239, 0.5)'],
                borderWidth: 0
            }]
        },
        options: riskChartOptions
    });

    // Traffic Risk Chart
    initializeTrafficRiskChart();

    // Filter functionality
    const activeFilters = {
        highRisk: true,
        mediumRisk: true,
        lowRisk: true
    };

    // Initialize filter handlers
    document.querySelectorAll('.legend-item').forEach(item => {
        item.addEventListener('click', function(event) {
            event.preventDefault();
            const filterClass = Array.from(this.classList).find(cls => cls.includes('-filter'));
            if (filterClass) {
                const riskType = filterClass.replace('-filter', '').replace(/-([a-z])/g, (_, letter) => letter.toUpperCase());
                let riskTypeFormatted;
                if (riskType === 'highRisk' || riskType === 'high-risk') {
                    riskTypeFormatted = 'highRisk';
                } else if (riskType === 'mediumRisk' || riskType === 'medium-risk') {
                    riskTypeFormatted = 'mediumRisk';
                } else {
                    riskTypeFormatted = 'lowRisk';
                }
                toggleRiskFilter(riskTypeFormatted);
            }
        });
    });

    // Functions
    @if(auth()->user()->role === 'admin')
    window.fetchDetailData = function(category) {
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

        // Update the export button link when modal is shown
        const exportButton = document.getElementById('exportRiskExcelButton');
        if (exportButton) {
             // Assuming the category matches the segment in the URL, e.g., 'low-risk' or 'high-risk'
            const exportCategory = category.toLowerCase().replace(' ', '-'); // Format category for URL
            exportButton.href = `/risk/export/${exportCategory}/xlsx`;
        }

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
    };
    @endif

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

        // Update the export button link in alertDetailModal
        const exportButton = document.getElementById('exportAlertExcelButton');
        if (exportButton) {
            // For simplicity now, always export all-risk in XLSX from this modal
            // A better approach requires backend changes to filter by month/severity
            exportButton.href = `/risk/export/all-risk/xlsx`;
             // Anda bisa menambahkan filter bulan jika backend mendukung
             // exportButton.href = `/risk/export/all-risk/xlsx?month=${month}`;
        }

        // Ambil filter aktif
        const activeLevels = [];
        if (activeFilters.lowRisk) activeLevels.push('low');
        if (activeFilters.mediumRisk) activeLevels.push('medium');
        if (activeFilters.highRisk) activeLevels.push('high');

        if (activeLevels.length === 1) {
            // Hanya satu filter aktif, ambil data sesuai level
            fetch(`/traffic-risk/details/${month}/${activeLevels[0]}`)
                .then(response => {
                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                    return response.json();
                })
                .then(response => {
                    tableBody.innerHTML = '';
                    const data = response.incidents || [];
                    currentAlertData = data;
                    if (data.length === 0) {
                        tableBody.innerHTML = `<tr><td colspan="6" class="text-center">No alerts found for ${month}</td></tr>`;
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
                    tableBody.innerHTML = `<tr><td colspan="6" class="text-center text-danger"><div class="alert alert-danger mb-0"><strong>Error:</strong> An error occurred while fetching the data.</div></td></tr>`;
                });
        } else {
            // Lebih dari satu filter aktif, ambil semua dan filter di frontend
            fetch(`/traffic-risk/monthly-details/${month}`)
                .then(response => {
                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                    return response.json();
                })
                .then(response => {
                    tableBody.innerHTML = '';
                    let data = response.incidents || [];
                    // Filter sesuai filter aktif
                    data = data.filter(item => activeLevels.includes((item.severity || '').toLowerCase()));
                    currentAlertData = data;
                    if (data.length === 0) {
                        tableBody.innerHTML = `<tr><td colspan="6" class="text-center">No alerts found for ${month}</td></tr>`;
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
                    tableBody.innerHTML = `<tr><td colspan="6" class="text-center text-danger"><div class="alert alert-danger mb-0"><strong>Error:</strong> An error occurred while fetching the data.</div></td></tr>`;
                });
        }
    }

    window.showEventDetails = function(item) {
        const detailsContent = document.getElementById('eventDetailsContent');

        let individualSolution = "Review the alert details and take appropriate action.";
        if (item.severity === 'high') {
            individualSolution = "Investigate this high-severity alert immediately. Isolate the affected system if necessary.";
        } else if (item.severity === 'medium') {
            individualSolution = "Assess this medium-severity alert. Determine if it requires immediate action or can be scheduled.";
        } else if (item.severity === 'low'){
             individualSolution = "Monitor this low-severity alert. It might indicate potential issues if it occurs frequently.";
        }

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
                        <span class="detail-value">${individualSolution}</span>
                    </div>
                </div>
            </div>
        `;

        detailsContent.innerHTML = detailsHtml;
        const eventDetailsModal = new bootstrap.Modal(document.getElementById('eventDetailsModal'));
        eventDetailsModal.show();
    }

    function toggleRiskFilter(riskType) {
        activeFilters[riskType] = !activeFilters[riskType];

        const filterElement = document.querySelector(`.${riskType.replace(/([A-Z])/g, '-$1').toLowerCase()}-filter`);
        if (filterElement) {
            if (activeFilters[riskType]) {
                filterElement.classList.add('active');
            } else {
                filterElement.classList.remove('active');
            }
        }

        updateTrafficChartFilters();
        updateFilterStatus();
    }

    function updateTrafficChartFilters() {
        const trafficChart = Chart.getChart('trafficRiskChart');
        if (!trafficChart) {
            console.error('Traffic chart not found!');
            return;
        }

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

    function updateFilterStatus() {
        const statusElement = document.getElementById('filterStatus');
        if (!statusElement) return;

        statusElement.innerHTML = '';
        const activeFilterCount = Object.values(activeFilters).filter(v => v).length;

        if (activeFilterCount === 3) {
            statusElement.innerHTML = '<span class="badge rounded-pill bg-light text-dark me-2 mb-2">All data shown</span>';
        } else {
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

    async function initializeTrafficRiskChart() {
        try {
            const ctx = document.getElementById('trafficRiskChart').getContext('2d');

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
                            display: false,
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

            // Initialize custom legend filters
            Object.keys(activeFilters).forEach(filter => {
                const filterElement = document.querySelector(`.${filter.replace(/([A-Z])/g, '-$1').toLowerCase()}-filter`);
                if (filterElement) {
                    filterElement.classList.add('active');
                }
            });

            updateFilterStatus();

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

    // Alert Manager Class
    class AlertManager {
        constructor() {
            this.alerts = new Set();
            this.alertCount = 0;
        }

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

            setTimeout(() => alertElement.classList.add('show'), 100);
            setTimeout(() => this.dismissAlert(alertId), 5000);
        }

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

    const alertManager = new AlertManager();

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

    // Initialize tooltips
    const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltips.forEach(tooltip => {
        new bootstrap.Tooltip(tooltip);
    });

    // Initial high risk check
    const initialHighRiskCount = parseInt(document.getElementById('highRiskChart').getAttribute('data-value') || '0');
    checkHighRisk(initialHighRiskCount);

    // Pada bagian JS, nonaktifkan event fetchDetailData dan onClick chart jika user bukan admin
    @if(auth()->user()->role !== 'admin')
    window.fetchDetailData = function() { return false; };
    // Nonaktifkan onClick chart
    Chart.defaults.plugins.legend.onClick = null;
    // Atau jika ada event onClick di chart, override jadi kosong
    if (window.trafficChart) {
        window.trafficChart.options.onClick = null;
    }
    @endif
});

function fetchUserDetailData(category) {
    const tableBody = document.getElementById('riskDetailTableBody');
    const modalTitle = document.getElementById('riskDetailModalLabel');
    modalTitle.textContent = `Alert Details - ${category}`;
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
    fetch(`/user-alerts/${encodeURIComponent(category)}`)
        .then(response => response.json())
        .then(response => {
            tableBody.innerHTML = '';
            const alerts = response.data || [];
            if (!alerts.length) {
                tableBody.innerHTML = `<tr><td colspan="6" class="text-center">No data found</td></tr>`;
                return;
            }
            alerts.forEach((item, index) => {
                const row = document.createElement('tr');
                const severityClass = getSeverityClass(item.severity);
                row.innerHTML = `
                    <td class="text-muted small">${item.id || '-'}</td>
                    <td>${item.category || '-'}</td>
                    <td class="description-cell">${item.description?.split('\n')[0] || '-'}</td>
                    <td><span class="badge ${severityClass}">${item.severity?.toUpperCase() || '-'}</span></td>
                    <td class="small">${formatDate(item.raisedAt || item.created_at)}</td>
                    <td><button type="button" class="btn btn-sm btn-info view-details">View Details</button></td>
                `;
                const viewButton = row.querySelector('.view-details');
                viewButton.addEventListener('click', () => showEventDetails(item));
                tableBody.appendChild(row);
            });
        })
        .catch(error => {
            tableBody.innerHTML = `<tr><td colspan="6" class="text-center text-danger"><div class="alert alert-danger mb-0"><strong>Error:</strong> An error occurred while fetching the data.</div></td></tr>`;
            console.error('Error:', error);
        });
}
</script>
@endpush
@endsection
