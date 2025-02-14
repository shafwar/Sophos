@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Recent threat graphs Section -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center mb-4">
                <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                <h5 class="card-title mb-0">Recent threat graphs</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3">Time created</th>
                            <th class="px-4 py-3">Priority</th>
                            <th class="px-4 py-3">Name</th>
                            <th class="px-4 py-3">User</th>
                            <th class="px-4 py-3">Device</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="px-4">Feb 6, 2025 10:49 AM</td>
                            <td class="px-4"><span class="badge bg-danger">High</span></td>
                            <td class="px-4">TrojAgent-TWK</td>
                            <td class="px-4">PERTAMINA\nik.sulistiani.ptg</td>
                            <td class="px-4">HQ-Notebook-80</td>
                        </tr>
                        <tr>
                            <td class="px-4">Feb 5, 2025 9:44 AM</td>
                            <td class="px-4"><span class="badge bg-success">Low</span></td>
                            <td class="px-4">Mal/HTMLGen-A</td>
                            <td class="px-4">PTG-DESKTOP-252\ms.serpong</td>
                            <td class="px-4">PTG-DESKTOP-252</td>
                        </tr>
                        <tr>
                            <td class="px-4">Feb 5, 2025 8:48 AM</td>
                            <td class="px-4"><span class="badge bg-success">Low</span></td>
                            <td class="px-4">Troj/Lnk-I</td>
                            <td class="px-4">PERTAMINA\mk.nofriyanto.s</td>
                            <td class="px-4">DESKTOP-040G743</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Devices and Users Summary -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <i class="fas fa-laptop me-2"></i>
                        <h5 class="card-title mb-0">Devices and users: summary</h5>
                    </div>
                    <div class="chart-container position-relative" style="width: 100%; max-width: 300px; margin: 0 auto;">
                        <canvas id="devicesChart"></canvas>
                        <div class="position-absolute top-50 start-50 translate-middle text-center">
                            <h2 class="display-4 mb-0">640</h2>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="d-flex justify-content-between align-items-center py-2">
                            <span class="text-muted">Active</span>
                            <span>599</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center py-2">
                            <span class="text-muted">Inactive 2+ Weeks</span>
                            <span>37</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center py-2">
                            <span class="text-muted">Not Protected</span>
                            <span>4</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Web Control -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <i class="fas fa-globe me-2"></i>
                        <h5 class="card-title mb-0">Web control</h5>
                    </div>
                    <div class="row g-5">
                        <div class="col-6">
                            <div class="text-center">
                                <h2 class="display-5 fw-semibold mb-3">0</h2>
                                <p class="text-secondary mb-0">Web Threats Blocked</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <h2 class="display-5 fw-semibold mb-3">718</h2>
                                <p class="text-secondary mb-0">Policy Violations Blocked</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <h2 class="display-5 fw-semibold mb-3">41294</h2>
                                <p class="text-secondary mb-0">Policy Warnings Issued</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <h2 class="display-5 fw-semibold mb-3">41230</h2>
                                <p class="text-secondary mb-0">Policy Warnings Proceeded</p>
                            </div>
                        </div>
                    </div>
                    <div class="text-end mt-4">
                        <span class="text-secondary small">last 30 days</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.chart-container {
    height: 300px;
    position: relative;
}

.chart-container h2 {
    font-size: 2.5rem;
    font-weight: 500;
}

.card {
    transition: all 0.3s ease;
}

/* Custom styles for Web control numbers */
.display-5 {
    font-size: 2.5rem;
    line-height: 1.2;
}

.text-secondary {
    color: #6c757d !important;
}

/* Improve spacing between elements */
.g-5 {
    --bs-gutter-x: 3rem;
    --bs-gutter-y: 3rem;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
}

.badge {
    padding: 0.5em 1em;
    border-radius: 6px;
}

.table th {
    font-weight: 600;
    color: #6c757d;
}

.table td {
    vertical-align: middle;
}

.display-6 {
    font-size: 2rem;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Donut Chart Configuration
    const ctx = document.getElementById('devicesChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Active', 'Inactive 2+ Weeks', 'Not Protected'],
            datasets: [{
                data: [599, 37, 4],
                backgroundColor: [
                    '#4CAF50',  // Active - Green
                    '#FFC107',  // Inactive - Yellow
                    '#FF5722'   // Not Protected - Red
                ],
                borderWidth: 0,
                cutout: '80%'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    enabled: false
                }
            },
            animation: {
                animateRotate: true,
                animateScale: true
            }
        }
    });
});
</script>
@endpush
