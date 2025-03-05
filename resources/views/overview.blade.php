@extends('layouts.app')

@section('content')
<div class="dashboard-header">
    <h2>Endpoint Protection - Dashboard</h2>
    <p>See a snapshot of your security protection</p>
</div>

@push('styles')
<style>
.dashboard-container {
    background-color: #f4f7fc;
    min-height: calc(100vh - 80px);
    padding: 2rem;
}

.metrics-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.metric-card {
    background: #1b258f;
    border-radius: 12px;
    padding: 1.5rem;
    color: white;
    transition: all 0.3s ease;
    cursor: pointer;
    position: relative;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.metric-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.metric-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.metric-card:hover::before {
    opacity: 1;
}

.metric-title {
    font-size: 0.9rem;
    opacity: 0.8;
    margin-bottom: 0.5rem;
}

.metric-value {
    font-size: 2.5rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.metric-subtitle {
    font-size: 0.8rem;
    opacity: 0.7;
}

.charts-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.chart-card {
    background: #1b258f;
    border-radius: 12px;
    padding: 1.5rem;
    color: white;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.chart-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.chart-header h3 {
    font-size: 1.2rem;
    font-weight: 600;
    margin: 0;
}

.year-selector {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.year-selector:hover {
    background: rgba(255, 255, 255, 0.2);
}

.year-selector option {
    background: #1b258f;
    color: white;
}

.tables-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.5rem;
}

.table-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.table-header {
    background: #1b258f;
    color: white;
    padding: 1.5rem;
    font-weight: 600;
}

.data-table {
    width: 100%;
}

.data-table th {
    background: rgba(27, 37, 143, 0.05);
    padding: 1rem;
    font-weight: 500;
    text-align: left;
    color: #1b258f;
}

.data-table td {
    padding: 1rem;
    border-bottom: 1px solid rgba(27, 37, 143, 0.1);
}

.data-table tr:hover {
    background: rgba(27, 37, 143, 0.02);
}

.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.875rem;
    background: rgba(27, 37, 143, 0.1);
    color: #1b258f;
}

.action-button {
    padding: 0.5rem;
    color: #1b258f;
    background: none;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
    border-radius: 4px;
}

.action-button:hover {
    background: rgba(27, 37, 143, 0.1);
}

.table-total {
    padding: 1rem;
    background: rgba(27, 37, 143, 0.05);
    color: #1b258f;
    font-weight: 600;
    text-align: right;
}

.dashboard-header {
    background: white;
    border-bottom: 1px solid #e5e7eb;
    padding: 1.5rem 2rem;
    margin-bottom: 2rem;
}

.dashboard-header h2 {
    color: #1b258f;
    font-size: 1.75rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.dashboard-header p {
    color: #6b7280;
    font-size: 0.95rem;
    margin: 0;
}

/* Modal and loading styles */
.loading-animation {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.8);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 10;
}

.endpoint-details-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

.ripple {
    position: absolute;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.4);
    transform: scale(0);
    animation: ripple 0.6s linear;
    pointer-events: none;
}

@keyframes ripple {
    to {
        transform: scale(4);
        opacity: 0;
    }
}

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
</style>
@endpush

<div class="dashboard-container">
    <!-- Metrics Grid -->
    <div class="metrics-grid">
        <div class="metric-card" data-aos="fade-up" data-aos-delay="100">
            <div class="metric-title">Active Endpoints</div>
            <div class="metric-value">599</div>
            <div class="metric-subtitle">PROTECTED</div>
        </div>

        <div class="metric-card" data-aos="fade-up" data-aos-delay="200">
            <div class="metric-title">Inactive Endpoints</div>
            <div class="metric-value">37</div>
            <div class="metric-subtitle">2+ WEEKS</div>
        </div>

        <div class="metric-card" data-aos="fade-up" data-aos-delay="300">
            <div class="metric-title">Inactive Endpoints</div>
            <div class="metric-value">0</div>
            <div class="metric-subtitle">2+ MONTHS</div>
        </div>

        <div class="metric-card" data-aos="fade-up" data-aos-delay="400">
            <div class="metric-title">Not Protected</div>
            <div class="metric-value">4</div>
            <div class="metric-subtitle">ENDPOINTS</div>
        </div>
    </div>

    <!-- Charts Grid -->
    <div class="charts-grid">
        <div class="chart-card" data-aos="fade-up" data-aos-delay="500">
            <div class="chart-header">
                <h3>Endpoint Protection Status</h3>
                <select class="year-selector">
                    <option>Current</option>
                    <option>Last Week</option>
                    <option>Last Month</option>
                </select>
            </div>
            <div style="height: 300px;">
                <canvas id="endpointStatusChart"></canvas>
            </div>
        </div>

        <div class="chart-card" data-aos="fade-up" data-aos-delay="600">
            <div class="chart-header">
                <h3>Protection Rate</h3>
                <select class="year-selector">
                    <option>Current</option>
                    <option>Last Week</option>
                    <option>Last Month</option>
                </select>
            </div>
            <div style="height: 300px; position: relative;">
                <canvas id="protectionRateChart"></canvas>
                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center;">
                    <div style="font-size: 2.5rem; font-weight: bold;">99.4%</div>
                    <div style="opacity: 0.8;">Protected</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tables Grid -->
    <div class="tables-grid">
        <div class="table-card" data-aos="fade-up" data-aos-delay="700">
            <div class="table-header">Unprotected Endpoints</div>
            <div class="table-content">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Hostname</th>
                            <th>IP Address</th>
                            <th>Last Seen</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>DESKTOP-3J5K2LM</td>
                            <td>192.168.1.105</td>
                            <td>3 days ago</td>
                            <td>
                                <button class="action-button" title="Protect">
                                    <i class="fas fa-shield-alt"></i>
                                </button>
                                <button class="action-button" title="Details">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="table-total">
                    Total Unprotected: 4
                </div>
            </div>
        </div>

        <div class="table-card" data-aos="fade-up" data-aos-delay="800">
            <div class="table-header">Inactive Endpoints</div>
            <div class="table-content">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Hostname</th>
                            <th>IP Address</th>
                            <th>Status</th>
                            <th>Inactive Since</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>DESKPC-HR125</td>
                            <td>192.168.1.45</td>
                            <td>
                                <span class="status-badge">2+ Weeks</span>
                            </td>
                            <td>Jan 25, 2025</td>
                            <td>
                                <button class="action-button" title="Check">
                                    <i class="fas fa-sync"></i>
                                </button>
                                <button class="action-button" title="Details">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="table-total">
                    Total Inactive: 37
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize AOS
    AOS.init({
        duration: 800,
        once: true
    });

    // Chart colors configuration
    const chartColors = {
        primary: 'rgba(79, 120, 247, 0.8)',
        secondary: 'rgba(189, 195, 207, 0.8)',
        danger: 'rgba(255, 99, 132, 0.8)',
        border: {
            primary: 'rgba(79, 120, 247, 1)',
            secondary: 'rgba(189, 195, 207, 1)',
            danger: 'rgba(255, 99, 132, 1)'
        }
    };

    // Chart defaults
    const chartDefaults = {
        responsive: true,
        maintainAspectRatio: false,
        animation: {
            duration: 1000,
            easing: 'easeInOutQuart'
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(255, 255, 255, 0.1)'
                },
                ticks: {
                    color: 'rgba(255, 255, 255, 0.8)'
                }
            },
            x: {
                grid: {
                    display: false
                },
                ticks: {
                    color: 'rgba(255, 255, 255, 0.8)'
                }
            }
        },
        plugins: {
            legend: {
                labels: {
                    color: 'rgba(255, 255, 255, 0.8)'
                }
            }
        }
    };

    // Endpoint Status Chart
    const endpointStatusCtx = document.getElementById('endpointStatusChart').getContext('2d');
    new Chart(endpointStatusCtx, {
        type: 'bar',
        data: {
            labels: ['Active', 'Inactive 2+ Weeks', 'Inactive 2+ Months', 'Not Protected'],
            datasets: [{
                label: 'Endpoints',
                data: [599, 37, 0, 4],
                backgroundColor: [
                    chartColors.primary,
                    chartColors.secondary,
                    chartColors.secondary,
                    chartColors.danger
                ],
                borderColor: [
                    chartColors.border.primary,
                    chartColors.border.secondary,
                    chartColors.border.secondary,
                    chartColors.border.danger
                ],
                borderWidth: 1,
                borderRadius: 5,
                barThickness: 40
            }]
        },
        options: {
            ...chartDefaults,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // Protection Rate Chart
    const protectionRateCtx = document.getElementById('protectionRateChart').getContext('2d');
    new Chart(protectionRateCtx, {
        type: 'doughnut',
        data: {
            labels: ['Protected', 'Not Protected'],
            datasets: [{
                data: [99.4, 0.6],
                backgroundColor: [
                    chartColors.primary,
                    chartColors.danger
                ],
                borderColor: [
                    chartColors.border.primary,
                    chartColors.border.danger
                ],
                borderWidth: 2,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '75%',
            animation: {
                animateRotate: true,
                animateScale: true
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        color: 'rgba(255, 255, 255, 0.8)',
                        padding: 20
                    }
                }
            }
        }
    });

    // Function to show loading state
    function showLoading(chartId) {
        const chartContainer = document.getElementById(chartId).parentElement;
        const loadingOverlay = document.createElement('div');
        loadingOverlay.className = 'loading-animation';
        loadingOverlay.innerHTML = '<div class="spinner-border text-light" role="status"><span class="visually-hidden">Loading...</span></div>';
        chartContainer.appendChild(loadingOverlay);
        return loadingOverlay;
    }

    // Function to update charts with new data
    function updateCharts() {
        const endpointStatusChart = Chart.getChart('endpointStatusChart');
        if (endpointStatusChart) {
            endpointStatusChart.data.datasets[0].data = [599, 37, 0, 4];
            endpointStatusChart.update();
        }

        const protectionRateChart = Chart.getChart('protectionRateChart');
        if (protectionRateChart) {
            protectionRateChart.data.datasets[0].data = [99.4, 0.6];
            protectionRateChart.update();
        }
    }

    // Metric card click handlers
    document.querySelectorAll('.metric-card').forEach(card => {
        card.addEventListener('click', function() {
            this.style.animation = 'none';
            this.offsetHeight; // Trigger reflow
            this.style.animation = 'pulse 0.5s';
        });
    });

    // Table row hover effects
    document.querySelectorAll('.data-table tbody tr').forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.01)';
            this.style.transition = 'transform 0.2s ease';
        });

        row.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });

    // Function to handle protecting an endpoint
    function handleProtect(row) {
        const hostname = row.cells[0].textContent;
        const ipAddress = row.cells[1].textContent;

        const loadingDiv = document.createElement('div');
        loadingDiv.className = 'loading-animation';
        loadingDiv.innerHTML = '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>';
        row.closest('.table-content').appendChild(loadingDiv);

        setTimeout(() => {
            loadingDiv.remove();
            showNotification('success', `Protection initiated for ${hostname}`);
            removeRowAndUpdateCounts(row);
        }, 1500);
    }

    // Function to handle checking an endpoint
    function handleCheck(row) {
        const hostname = row.cells[0].textContent;
        const statusCell = row.cells[2];
        const originalContent = statusCell.innerHTML;

        statusCell.innerHTML = '<div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Loading...</span></div>';

        setTimeout(() => {
            statusCell.innerHTML = originalContent;
            showNotification('info', `${hostname} status verified`);
        }, 1200);
    }

    // Function to handle viewing endpoint details
    function handleViewDetails(row) {
        const hostname = row.cells[0].textContent;
        const ipAddress = row.cells[1].textContent;

        const modal = createDetailsModal(hostname, ipAddress, row);
        document.body.appendChild(modal);

        document.getElementById('closeModal').addEventListener('click', () => {
            closeModal(modal);
        });
    }

    // Function to create details modal
    function createDetailsModal(hostname, ipAddress, row) {
        const modal = document.createElement('div');
        modal.className = 'endpoint-details-modal';
        modal.style.opacity = '0';
        modal.style.transition = 'opacity 0.3s ease';

        const modalContent = `
            <div class="modal-content" style="
                background: white;
                padding: 2rem;
                border-radius: 12px;
                max-width: 600px;
                width: 90%;
                max-height: 80vh;
                overflow: auto;
                box-shadow: 0 10px 25px rgba(0,0,0,0.2);
                transform: translateY(20px);
                transition: transform 0.3s ease;
            ">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <h2 style="margin: 0; color: #1b258f;">${hostname} Details</h2>
                    <button id="closeModal" style="background: none; border: none; font-size: 1.5rem; cursor: pointer;">×</button>
                </div>
                <div style="margin-bottom: 1.5rem;">
                    <div class="detail-row">
                        <span class="detail-label">IP Address:</span>
                        <span class="detail-value">${ipAddress}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Status:</span>
                        <span class="detail-value">${row.cells[2] ? row.cells[2].textContent.trim() : 'Not Protected'}</span>
                    </div>
                </div>
                <div class="actions" style="text-align: right;">
                    <button class="action-button" onclick="this.closest('.endpoint-details-modal').remove()">Close</button>
                </div>
            </div>
        `;

        modal.innerHTML = modalContent;

        setTimeout(() => {
            modal.style.opacity = '1';
            modal.querySelector('.modal-content').style.transform = 'translateY(0)';
        }, 10);

        return modal;
    }

    // Function to close modal
    function closeModal(modal) {
        modal.style.opacity = '0';
        modal.querySelector('.modal-content').style.transform = 'translateY(20px)';
        setTimeout(() => {
            document.body.removeChild(modal);
        }, 300);
    }

    // Function to show notifications
    function showNotification(type, message) {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type}`;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            padding: 1rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            background: ${type === 'success' ? '#4fc347' : '#3498db'};
            color: white;
            opacity: 0;
            transform: translateY(-20px);
            transition: all 0.3s ease;
        `;

        notification.innerHTML = `<strong>${type === 'success' ? 'Success!' : 'Info:'}</strong> ${message}`;
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.style.opacity = '1';
            notification.style.transform = 'translateY(0)';
        }, 10);

        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateY(-20px)';
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }

    // Function to remove row and update counts
    function removeRowAndUpdateCounts(row) {
        row.style.opacity = '0';
        row.style.height = '0';
        row.style.transition = 'all 0.5s ease';

        setTimeout(() => {
            row.remove();
            const totalElement = row.closest('.table-content').querySelector('.table-total');
            const currentTotal = parseInt(totalElement.textContent.match(/\d+/)[0]);
            totalElement.textContent = `Total Unprotected: ${currentTotal - 1}`;

            const notProtectedCard = document.querySelector('.metric-card:nth-child(4) .metric-value');
            notProtectedCard.textContent = currentTotal - 1;

            updateCharts();
        }, 500);
    }

    // Add click handlers for action buttons
    document.querySelectorAll('.action-button').forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation();

            const ripple = document.createElement('div');
            ripple.className = 'ripple';
            this.appendChild(ripple);
            setTimeout(() => ripple.remove(), 1000);

            const action = this.getAttribute('title').toLowerCase();
            const row = this.closest('tr');

            switch(action) {
                case 'protect':
                    handleProtect(row);
                    break;
                case 'details':
                    handleViewDetails(row);
                    break;
                case 'check':
                    handleCheck(row);
                    break;
            }
        });
    });

    // Year selector handlers
    document.querySelectorAll('.year-selector').forEach(selector => {
        selector.addEventListener('change', function() {
            const chartType = this.closest('.chart-card').querySelector('canvas').id;
            updateChartData(chartType, this.value);
        });
    });

    // Function to update chart data based on timeframe
    function updateChartData(chartType, timeframe) {
        const loadingOverlay = showLoading(chartType);

        const data = {
            'Current': {
                endpointStatus: [599, 37, 0, 4],
                protectionRate: [99.4, 0.6]
            },
            'Last Week': {
                endpointStatus: [585, 42, 2, 11],
                protectionRate: [98.2, 1.8]
            },
            'Last Month': {
                endpointStatus: [572, 39, 5, 16],
                protectionRate: [97.3, 2.7]
            }
        };

        setTimeout(() => {
            const newData = data[timeframe];

            if (chartType === 'endpointStatusChart') {
                const chart = Chart.getChart(chartType);
                chart.data.datasets[0].data = newData.endpointStatus;
                chart.update();
            } else if (chartType === 'protectionRateChart') {
                const chart = Chart.getChart(chartType);
                chart.data.datasets[0].data = newData.protectionRate;
                chart.update();

                const centerText = chart.canvas.parentElement.querySelector('div > div');
                centerText.textContent = newData.protectionRate[0] + '%';
            }

            loadingOverlay.remove();
        }, 800);
    }

    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Add keyboard navigation for modals
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.querySelector('.endpoint-details-modal');
            if (modal) {
                closeModal(modal);
            }
        }
    });
});
</script>
@endpush
@endsection
