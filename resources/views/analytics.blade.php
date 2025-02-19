@extends('layouts.app')
@section('content')
    <!-- Search & Filters -->
    <div class="container-fluid px-4">
        <div class="row g-3 mb-4 align-items-center">
            <div class="col-md-4">
                <div class="search-box position-relative">
                    <i class="fas fa-search position-absolute top-50 start-3 translate-middle-y text-muted"></i>
                    <input type="text" class="form-control form-control-lg ps-5" id="searchInput" placeholder="Search">
                </div>
            </div>
            <div class="col-md-4">
                <select class="form-select form-select-lg" id="userGroupFilter">
                    <option value="">Search by User Group</option>
                    @foreach($userGroups ?? [] as $group)
                        <option value="{{ $group }}">{{ $group }}</option>
                    @endforeach
                </select>
            </div>s
        </div>

        <!-- Statistics Cards -->
        <div class="row g-4 mb-4">
            <div class="col">
                <div class="card h-100 border-0 rounded-4 shadow-sm hover-card">
                    <div class="card-body text-center p-4">
                        <h2 class="display-4 mb-2">{{ number_format($stats['all']) }}</h2>
                        <p class="text-muted mb-0 fs-5">All</p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card h-100 border-0 rounded-4 shadow-sm hover-card">
                    <div class="card-body text-center p-4">
                        <h2 class="display-4 mb-2 text-primary">{{ number_format($stats['active']) }}</h2>
                        <p class="text-muted mb-0 fs-5">Active</p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card h-100 border-0 rounded-4 shadow-sm hover-card">
                    <div class="card-body text-center p-4">
                        <h2 class="display-4 mb-2 text-warning">{{ number_format($stats['inactive_2weeks']) }}</h2>
                        <p class="text-muted mb-0 fs-5">Inactive 2+ Weeks</p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card h-100 border-0 rounded-4 shadow-sm hover-card">
                    <div class="card-body text-center p-4">
                        <h2 class="display-4 mb-2">{{ number_format($stats['inactive_2months']) }}</h2>
                        <p class="text-muted mb-0 fs-5">Inactive 2+ Months</p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card h-100 border-0 rounded-4 shadow-sm hover-card">
                    <div class="card-body text-center p-4">
                        <h2 class="display-4 mb-2">{{ number_format($stats['no_devices']) }}</h2>
                        <p class="text-muted mb-0 fs-5">No Devices</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users Table -->
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="usersTable">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4 py-3">
                                    <div class="d-flex align-items-center">
                                        Name
                                        <i class="fas fa-sort ms-2"></i>
                                    </div>
                                </th>
                                <th class="px-4 py-3">
                                    <div class="d-flex align-items-center">
                                        Email
                                        <i class="fas fa-sort ms-2"></i>
                                    </div>
                                </th>
                                <th class="px-4 py-3">
                                    <div class="d-flex align-items-center">
                                        Online
                                        <i class="fas fa-sort ms-2"></i>
                                    </div>
                                </th>
                                <th class="px-4 py-3">Devices</th>
                                <th class="px-4 py-3">Logins</th>
                                <th class="px-4 py-3">Groups</th>
                                <th class="px-4 py-3 text-end">Health Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                            <tr class="align-middle">
                                <td class="px-4 text-primary">{{ $user['name'] }}</td>
                                <td class="px-4">{{ $user['email'] }}</td>
                                <td class="px-4">
                                    @if($user['last_online'] == 'Never')
                                        <span class="text-muted">Never</span>
                                    @else
                                        {{ $user['last_online'] }}
                                    @endif
                                </td>
                                <td class="px-4">{{ $user['devices'] }}</td>
                                <td class="px-4">{{ $user['logins'] }}</td>
                                <td class="px-4">{{ is_array($user['groups']) ? implode(', ', $user['groups']) : $user['groups'] }}</td>
                                <td class="px-4 text-end">
                                    @php
                                        $statusClass = match(strtolower($user['health_status'] ?? 'unknown')) {
                                            'good' => 'success',
                                            'warning', 'suspicious' => 'warning',
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
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">No users found</td>
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
                Showing 1-{{ count($users) }} of {{ $stats['all'] }} user reports
            </div>
            <nav aria-label="Page navigation">
                <ul class="pagination mb-0">
                    <li class="page-item">
                        <a class="page-link" href="#" aria-label="First">
                            <i class="fas fa-angle-double-left"></i>
                        </a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#" aria-label="Previous">
                            <i class="fas fa-angle-left"></i>
                        </a>
                    </li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item"><a class="page-link" href="#">4</a></li>
                    <li class="page-item">
                        <a class="page-link" href="#" aria-label="Next">
                            <i class="fas fa-angle-right"></i>
                        </a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#" aria-label="Last">
                            <i class="fas fa-angle-double-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>

@push('styles')
<style>
/* Animations dan Efek */
.hover-card {
    transition: all 0.3s ease;
}
.hover-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
}

/* Custom Styles */
.search-box .form-control {
    padding-left: 3rem;
    border-radius: 8px;
}

.form-control, .form-select {
    border-radius: 8px;
}

.table th {
    font-weight: 600;
    color: #6c757d;
}

.table td {
    padding-top: 1rem;
    padding-bottom: 1rem;
}

.pagination .page-link {
    border-radius: 8px;
    margin: 0 2px;
}

.btn {
    border-radius: 8px;
    padding: 0.5rem 1rem;
}

.card {
    border-radius: 12px;
}

/* Animasi untuk table rows */
tbody tr {
    transition: all 0.2s ease;
}

tbody tr:hover {
    background-color: #f8f9fa;
    transform: scale(1.01);
}

/* Animasi untuk buttons */
.btn {
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,.1);
}

/* Warna khusus untuk status */
.text-success { color: #28a745 !important; }
.text-warning { color: #ffc107 !important; }
.text-danger { color: #dc3545 !important; }

/* Custom scrollbar */
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const userGroupFilter = document.getElementById('userGroupFilter');
    const table = document.getElementById('usersTable');

    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const groupFilter = userGroupFilter.value.toLowerCase();
        const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

        for (let row of rows) {
            const cells = row.getElementsByTagName('td');
            if (cells.length === 1) continue; // Skip "No users found" row

            const name = cells[0].textContent.toLowerCase();
            const email = cells[1].textContent.toLowerCase();
            const group = cells[5].textContent.toLowerCase();

            const matchesSearch = name.includes(searchTerm) || email.includes(searchTerm);
            const matchesGroup = !groupFilter || group.includes(groupFilter);

            row.style.display = (matchesSearch && matchesGroup) ? '' : 'none';

            if (matchesSearch && matchesGroup) {
                row.style.animation = 'fadeIn 0.5s';
            }
        }
    }

    searchInput.addEventListener('input', filterTable);
    userGroupFilter.addEventListener('change', filterTable);

    // Export functions
    window.saveCustomReport = function() {
        // Implementasi save custom report
    };

    window.exportToCSV = function() {
        // Implementasi export to CSV
    };

    window.exportToPDF = function() {
        // Implementasi export to PDF
    };
});
</script>
@endpush
@endsection
