@extends('layouts.app')

@push('styles')
<style>
    /* Custom styles for reports page */
    .hover-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .hover-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }

    .search-box .form-control {
        padding-left: 3rem;
        border-radius: 0.5rem;
    }

    /* Table styles */
    .table {
        margin-bottom: 0;
    }

    .table th {
        font-weight: 500;
        border-top: none;
    }

    .table td {
        vertical-align: middle;
    }

    /* Pagination custom styles */
    .pagination {
        margin-bottom: 0;
    }

    .page-link {
        padding: 0.5rem 1rem;
        color: var(--primary-blue);
        border: 1px solid #dee2e6;
    }

    .page-item.active .page-link {
        background-color: var(--primary-blue);
        border-color: var(--primary-blue);
    }

    /* Animation for filtered rows */
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
</style>
@endpush

@section('content')
<!-- Header/Breadcrumb -->
<div class="bg-white border-bottom mb-4" data-aos="fade-down">
    <div class="container-fluid px-4 py-3">
        <div class="d-flex justify-content-between align-items-center">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="#" class="text-primary">Overview</a></li>
                    <li class="breadcrumb-item"><a href="#" class="text-primary">Endpoint Protection Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="#" class="text-primary">Reports</a></li>
                    <li class="breadcrumb-item active">Computer Report</li>
                </ol>
            </nav>
            <div>
                <img src="{{ asset('images/pertamina-gas.png') }}" alt="PERTAMINA GAS" height="40">
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-4">
    <!-- Search & Filters -->
    <div class="row g-3 mb-4 align-items-center" data-aos="fade-up">
        <div class="col-md-4">
            <div class="search-box position-relative">
                <i class="fas fa-search position-absolute top-50 start-3 translate-middle-y text-muted"></i>
                <input type="text" class="form-control form-control-lg ps-5" id="searchInput" placeholder="Search">
            </div>
        </div>
        <div class="col-md-4">
            <select class="form-select form-select-lg" id="computerGroupFilter">
                <option value="">Show all computers</option>
                @foreach($computerGroups ?? [] as $group)
                    <option value="{{ $group }}">{{ $group }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4 text-end">
            <a href="#" class="btn btn-outline-primary btn-lg me-2" onclick="saveCustomReport()">Save as Custom Report</a>
            <a href="#" class="btn btn-outline-primary btn-lg me-2" onclick="exportToCSV()">Export to CSV</a>
            <a href="#" class="btn btn-outline-primary btn-lg" onclick="exportToPDF()">Export to PDF</a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col" data-aos="fade-up" data-aos-delay="100">
            <div class="card h-100 border-0 rounded-4 shadow-sm hover-card">
                <div class="card-body text-center p-4">
                    <h2 class="display-4 mb-2">{{ number_format($stats['all']) }}</h2>
                    <p class="text-muted mb-0 fs-5">All</p>
                </div>
            </div>
        </div>
        <div class="col" data-aos="fade-up" data-aos-delay="200">
            <div class="card h-100 border-0 rounded-4 shadow-sm hover-card">
                <div class="card-body text-center p-4">
                    <h2 class="display-4 mb-2 text-primary">{{ number_format($stats['active']) }}</h2>
                    <p class="text-muted mb-0 fs-5">Active</p>
                </div>
            </div>
        </div>
        <div class="col" data-aos="fade-up" data-aos-delay="300">
            <div class="card h-100 border-0 rounded-4 shadow-sm hover-card">
                <div class="card-body text-center p-4">
                    <h2 class="display-4 mb-2 text-warning">{{ number_format($stats['inactive_2weeks']) }}</h2>
                    <p class="text-muted mb-0 fs-5">Inactive 2+ Weeks</p>
                </div>
            </div>
        </div>
        <div class="col" data-aos="fade-up" data-aos-delay="400">
            <div class="card h-100 border-0 rounded-4 shadow-sm hover-card">
                <div class="card-body text-center p-4">
                    <h2 class="display-4 mb-2">{{ number_format($stats['inactive_2months']) }}</h2>
                    <p class="text-muted mb-0 fs-5">Inactive 2+ Months</p>
                </div>
            </div>
        </div>
        <div class="col" data-aos="fade-up" data-aos-delay="500">
            <div class="card h-100 border-0 rounded-4 shadow-sm hover-card">
                <div class="card-body text-center p-4">
                    <h2 class="display-4 mb-2 text-danger">{{ number_format($stats['not_protected']) }}</h2>
                    <p class="text-muted mb-0 fs-5">Not Protected</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Computers Table -->
    <div class="card border-0 shadow-sm rounded-4" data-aos="fade-up" data-aos-delay="600">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="computersTable">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3">
                                <div class="d-flex align-items-center">
                                    Name
                                    <i class="fas fa-sort ms-2"></i>
                                </div>
                            </th>
                            <th class="px-4 py-3">Online</th>
                            <th class="px-4 py-3">Last user</th>
                            <th class="px-4 py-3">Real-time scan</th>
                            <th class="px-4 py-3">Last update</th>
                            <th class="px-4 py-3">Last scheduled scan</th>
                            <th class="px-4 py-3">Health status</th>
                            <th class="px-4 py-3">Group</th>
                            <th class="px-4 py-3">Agent Installed</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($computers as $computer)
                        <tr class="align-middle">
                            <td class="px-4 text-primary">{{ $computer['name'] }}</td>
                            <td class="px-4">{{ $computer['online'] }}</td>
                            <td class="px-4">{{ $computer['last_user'] }}</td>
                            <td class="px-4">{{ $computer['real_time_scan'] }}</td>
                            <td class="px-4">{{ $computer['last_update'] }}</td>
                            <td class="px-4">{{ $computer['last_scan'] }}</td>
                            <td class="px-4">
                                @php
                                    $statusClass = match(strtolower($computer['health_status'])) {
                                        'good' => 'success',
                                        'warning' => 'warning',
                                        'bad' => 'danger',
                                        default => 'secondary'
                                    };
                                    $icon = match($statusClass) {
                                        'success' => 'fa-check-circle',
                                        'warning' => 'fa-exclamation-triangle',
                                        'danger' => 'fa-times-circle',
                                        default => 'fa-question-circle'
                                    };
                                @endphp
                                <i class="fas {{ $icon }} text-{{ $statusClass }} fs-5"></i>
                            </td>
                            <td class="px-4">{{ $computer['group'] }}</td>
                            <td class="px-4">{{ $computer['agent_installed'] }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">No computers found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-between align-items-center mt-4">
        <div class="text-muted">
            {{ $computers->firstItem() }} - {{ $computers->lastItem() }} of {{ $computers->total() }} computer reports
        </div>
        {{ $computers->links() }}
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

    const searchInput = document.getElementById('searchInput');
    const computerGroupFilter = document.getElementById('computerGroupFilter');
    const table = document.getElementById('computersTable');

    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const groupFilter = computerGroupFilter.value.toLowerCase();
        const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

        for (let row of rows) {
            const cells = row.getElementsByTagName('td');
            if (cells.length === 1) continue;

            const name = cells[0].textContent.toLowerCase();
            const group = cells[7].textContent.toLowerCase();

            const matchesSearch = name.includes(searchTerm);
            const matchesGroup = !groupFilter || group.includes(groupFilter);

            row.style.display = (matchesSearch && matchesGroup) ? '' : 'none';

            if (matchesSearch && matchesGroup) {
                row.style.animation = 'fadeIn 0.5s';
            }
        }
    }

    searchInput.addEventListener('input', filterTable);
    computerGroupFilter.addEventListener('change', filterTable);

    // Export functions
    window.saveCustomReport = function() {
        alert('Save as Custom Report functionality will be implemented');
    };

    window.exportToCSV = function() {
        alert('Export to CSV functionality will be implemented');
    };

    window.exportToPDF = function() {
        alert('Export to PDF functionality will be implemented');
    };
});
</script>
@endpush
@endsection
