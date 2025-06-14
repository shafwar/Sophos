@extends('layouts.app')

@push('styles')
<style>
/* Clean Modern CSS Variables */
:root {
    --primary: #4f46e5;
    --primary-hover: #4338ca;
    --success: #10b981;
    --warning: #f59e0b;
    --danger: #ef4444;
    --gray-50: #f9fafb;
    --gray-100: #f3f4f6;
    --gray-200: #e5e7eb;
    --gray-700: #374151;
    --gray-800: #1f2937;
    --gray-900: #111827;
    --white: #ffffff;
    --shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1);
    --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1);
    --border-radius: 8px;
}

/* Container */
.history-page {
    background-color: var(--gray-50);
    min-height: 100vh;
    padding: 2rem 0;
}

.content-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1.5rem;
}

/* Header Card */
.header-card {
    background: var(--white);
    border-radius: var(--border-radius);
    padding: 2rem;
    margin-bottom: 1.5rem;
    box-shadow: var(--shadow);
    border-left: 4px solid var(--primary);
}

.header-title {
    font-size: 1.875rem;
    font-weight: 700;
    color: var(--gray-900);
    margin: 0 0 0.5rem 0;
}

.header-subtitle {
    color: var(--gray-700);
    margin: 0;
}

/* Filter Card */
.filter-card {
    background: var(--white);
    border-radius: var(--border-radius);
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    box-shadow: var(--shadow);
    border: 1px solid var(--gray-200);
}

.filter-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--gray-800);
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.form-group {
    margin-bottom: 1rem;
}

.form-label {
    display: block;
    font-weight: 600;
    color: var(--gray-700);
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
}

.form-control, .form-select {
    width: 100%;
    padding: 0.75rem;
    border: 2px solid var(--gray-200);
    border-radius: var(--border-radius);
    font-size: 0.875rem;
    transition: all 0.2s;
}

.form-control:focus, .form-select:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
}

/* Buttons */
.btn-modern {
    padding: 0.75rem 1.5rem;
    border-radius: var(--border-radius);
    font-weight: 600;
    font-size: 0.875rem;
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s;
}

.btn-primary {
    background: var(--primary);
    color: var(--white);
}

.btn-primary:hover {
    background: var(--primary-hover);
}

.btn-success {
    background: var(--success);
    color: var(--white);
}

.btn-warning {
    background: var(--warning);
    color: var(--white);
}

.btn-info {
    background: #0ea5e9;
    color: var(--white);
}

.btn-secondary {
    background: var(--gray-700);
    color: var(--white);
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.stat-card {
    background: var(--white);
    border-radius: var(--border-radius);
    padding: 1.5rem;
    box-shadow: var(--shadow);
    border: 1px solid var(--gray-200);
    position: relative;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    border-radius: var(--border-radius) var(--border-radius) 0 0;
}

.stat-card.low::before {
    background: var(--success);
}

.stat-card.medium::before {
    background: var(--warning);
}

.stat-card.high::before {
    background: var(--danger);
}

.stat-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1rem;
    font-size: 1.25rem;
}

.stat-icon.low {
    background: rgba(16, 185, 129, 0.1);
    color: var(--success);
}

.stat-icon.medium {
    background: rgba(245, 158, 11, 0.1);
    color: var(--warning);
}

.stat-icon.high {
    background: rgba(239, 68, 68, 0.1);
    color: var(--danger);
}

.stat-label {
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--gray-700);
    text-transform: uppercase;
    margin-bottom: 0.5rem;
}

.stat-value {
    font-size: 1.75rem;
    font-weight: 800;
    color: var(--gray-900);
}

/* Export Panel */
.export-panel {
    background: var(--white);
    border-radius: var(--border-radius);
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    box-shadow: var(--shadow);
    border: 1px solid var(--gray-200);
}

.export-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--gray-800);
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.export-controls {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    align-items: center;
}

.format-group {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

/* Table Container */
.table-container {
    background: var(--white);
    border-radius: var(--border-radius);
    box-shadow: var(--shadow);
    border: 1px solid var(--gray-200);
    overflow: hidden;
}

.table-header {
    background: var(--gray-100);
    padding: 1rem 1.5rem;
    border-bottom: 1px solid var(--gray-200);
}

.table-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--gray-800);
    margin: 0;
}

.records-count {
    background: var(--primary);
    color: var(--white);
    padding: 0.25rem 0.75rem;
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
}

.data-table th {
    background: var(--gray-50);
    padding: 1rem 0.75rem;
    text-align: left;
    font-weight: 600;
    font-size: 0.75rem;
    color: var(--gray-800);
    text-transform: uppercase;
    border-bottom: 1px solid var(--gray-200);
}

.data-table td {
    padding: 1rem 0.75rem;
    border-bottom: 1px solid var(--gray-100);
    font-size: 0.875rem;
    color: var(--gray-700);
}

.data-table tr {
    transition: all 0.2s;
}

.data-table tbody tr:hover {
    background: var(--gray-50);
    cursor: pointer;
}

/* Badges */
.badge {
    padding: 0.25rem 0.75rem;
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.badge-id {
    background: var(--gray-100);
    color: var(--gray-700);
}

.badge-low {
    background: rgba(16, 185, 129, 0.1);
    color: var(--success);
}

.badge-medium {
    background: rgba(245, 158, 11, 0.1);
    color: var(--warning);
}

.badge-high {
    background: rgba(239, 68, 68, 0.1);
    color: var(--danger);
}

/* Modal */
.modal-content {
    border: none;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-lg);
}

.modal-header {
    background: var(--primary);
    color: var(--white);
    border-radius: var(--border-radius) var(--border-radius) 0 0;
    padding: 1.5rem;
}

.modal-title {
    font-weight: 600;
    margin: 0;
}

.modal-body {
    padding: 1.5rem;
}

.detail-item {
    background: var(--gray-50);
    padding: 1rem;
    border-radius: var(--border-radius);
    margin-bottom: 1rem;
    border-left: 3px solid var(--primary);
}

.detail-label {
    font-weight: 600;
    color: var(--gray-700);
    font-size: 0.75rem;
    text-transform: uppercase;
    margin-bottom: 0.25rem;
}

.detail-value {
    color: var(--gray-900);
    font-size: 0.875rem;
}

/* Pagination */
.pagination-container {
    background: var(--white);
    border-radius: var(--border-radius);
    padding: 1.5rem;
    box-shadow: var(--shadow);
    border: 1px solid var(--gray-200);
    margin-top: 1.5rem;
}

.pagination {
    display: flex;
    justify-content: center;
    gap: 0.25rem;
    margin: 0;
    padding: 0;
    list-style: none;
}

.page-item {
    display: inline-block;
}

.page-link {
    padding: 0.5rem 0.75rem;
    border: 1px solid var(--gray-300);
    border-radius: var(--border-radius);
    color: var(--gray-700);
    text-decoration: none;
    font-weight: 500;
    transition: all 0.2s;
}

.page-link:hover {
    background: var(--primary);
    border-color: var(--primary);
    color: var(--white);
}

.page-item.active .page-link {
    background: var(--primary);
    border-color: var(--primary);
    color: var(--white);
}

.page-item.disabled .page-link {
    color: var(--gray-400);
    cursor: not-allowed;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 3rem 1rem;
}

.empty-icon {
    width: 64px;
    height: 64px;
    background: var(--gray-100);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    font-size: 1.5rem;
    color: var(--gray-400);
}

.empty-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--gray-800);
    margin-bottom: 0.5rem;
}

.empty-description {
    color: var(--gray-600);
    margin-bottom: 1.5rem;
}

/* Responsive */
@media (max-width: 768px) {
    .content-container {
        padding: 0 1rem;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .export-controls {
        flex-direction: column;
        width: 100%;
    }
    
    .export-controls > * {
        width: 100%;
    }
    
    .data-table {
        font-size: 0.75rem;
    }
    
    .data-table th,
    .data-table td {
        padding: 0.5rem;
    }
}
</style>
@endpush

@section('content')
<div class="history-page">
    <div class="content-container">
        <!-- Header -->
        <div class="header-card">
            <h1 class="header-title">
                <i class="fas fa-history" style="color: var(--primary); margin-right: 0.75rem;"></i>
                History Data Risiko Saya
            </h1>
            <p class="header-subtitle">Kelola dan analisis data risiko dengan mudah dan efisien</p>
        </div>

        <!-- Filter Section -->
        <div class="filter-card">
            <h3 class="filter-title">
                <i class="fas fa-filter"></i>
                Filter & Pencarian
            </h3>
            <form method="GET">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Level Risiko</label>
                            <select name="level" class="form-select">
                                <option value="">Semua Level Risiko</option>
                                <option value="low" {{ $selectedLevel === 'low' ? 'selected' : '' }}>🟢 Low Risk</option>
                                <option value="medium" {{ $selectedLevel === 'medium' ? 'selected' : '' }}>🟡 Medium Risk</option>
                                <option value="high" {{ $selectedLevel === 'high' ? 'selected' : '' }}>🔴 High Risk</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Pencarian Deskripsi</label>
                            <input type="text" name="search" class="form-control" placeholder="Masukkan kata kunci..." value="{{ $search }}">
                        </div>
                    </div>
                    <div class="col-12 text-end">
                        <button type="submit" class="btn-modern btn-primary">
                            <i class="fas fa-search"></i>
                            Terapkan Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card low">
                <div class="stat-icon low">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div class="stat-label">Low Risk</div>
                <div class="stat-value">{{ $risks->where('severity', 'low')->count() }}</div>
            </div>
            <div class="stat-card medium">
                <div class="stat-icon medium">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stat-label">Medium Risk</div>
                <div class="stat-value">{{ $risks->where('severity', 'medium')->count() }}</div>
            </div>
            <div class="stat-card high">
                <div class="stat-icon high">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-label">High Risk</div>
                <div class="stat-value">{{ $risks->where('severity', 'high')->count() }}</div>
            </div>
        </div>

        <!-- Export Panel -->
        <div class="export-panel">
            <h3 class="export-title">
                <i class="fas fa-download"></i>
                Export & Actions
            </h3>
            <div class="export-controls">
                <form id="exportForm" method="GET" action="{{ route('history.export') }}" class="format-group">
                    <input type="hidden" name="level" value="{{ $selectedLevel }}">
                    <input type="hidden" name="search" value="{{ $search }}">
                    <select name="format" class="form-select" style="width: 120px;">
                        <option value="csv">📊 CSV</option>
                        <option value="xlsx">📈 XLSX</option>
                        <option value="pdf">📄 PDF</option>
                    </select>
                    <button type="submit" class="btn-modern btn-success">
                        <i class="fas fa-file-export"></i>
                        Export
                    </button>
                </form>
                <button type="button" class="btn-modern btn-warning" id="exportGoogleSheetsBtn">
                    <i class="fab fa-google-drive"></i>
                    Google Sheets
                </button>
                <button type="button" class="btn-modern btn-info" id="copyClipboardBtn">
                    <i class="fas fa-copy"></i>
                    Copy
                </button>
                <button type="button" class="btn-modern btn-secondary" id="printTableBtn">
                    <i class="fas fa-print"></i>
                    Print
                </button>
            </div>
        </div>

        <!-- Records Count -->
        <div class="records-count">
            <i class="fas fa-database"></i>
            {{ $risks->count() }} Records Found
        </div>

        <!-- Data Table -->
        <div class="table-container">
            <div class="table-header">
                <h3 class="table-title">Data Risiko</h3>
            </div>
            <div class="table-responsive">
                <table class="data-table" id="historyTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Level</th>
                            <th>Date</th>
                            <th>Category</th>
                            <th>Description</th>
                            <th>Reporter</th>
                            <th>Location</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $perPage = 20;
                            $total = $risks->count();
                            $page = (int) request('page', 1);
                            $pages = ceil($total / $perPage);
                            $pagedRisks = $risks->slice(($page-1)*$perPage, $perPage);
                        @endphp
                        @if($pagedRisks->isEmpty())
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <div class="empty-icon">
                                            <i class="fas fa-search"></i>
                                        </div>
                                        <h4 class="empty-title">Tidak Ada Data</h4>
                                        <p class="empty-description">Tidak ada data yang sesuai dengan filter</p>
                                        <a href="{{ url()->current() }}" class="btn-modern btn-primary">
                                            <i class="fas fa-refresh"></i>
                                            Reset
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @else
                            @foreach($pagedRisks as $risk)
                            <tr class="clickable-row" data-modal-id="detailModal-{{ $risk['id'] }}">
                                <td>
                                    <span class="badge badge-id">#{{ $risk['id'] ?? '-' }}</span>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $risk['severity'] ?? 'low' }}">
                                        @if($risk['severity'] === 'high')
                                            <i class="fas fa-exclamation-circle"></i>
                                        @elseif($risk['severity'] === 'medium')
                                            <i class="fas fa-exclamation-triangle"></i>
                                        @else
                                            <i class="fas fa-shield-alt"></i>
                                        @endif
                                        {{ ucfirst($risk['severity'] ?? 'Low') }}
                                    </span>
                                </td>
                                <td>
                                    <i class="fas fa-calendar-alt" style="color: var(--gray-400); margin-right: 0.5rem;"></i>
                                    {{ $risk['raisedAt'] ? \Carbon\Carbon::parse($risk['raisedAt'])->format('d M Y, H:i') : '-' }}
                                </td>
                                <td>
                                    <i class="fas fa-tag" style="color: var(--primary); margin-right: 0.5rem;"></i>
                                    {{ $risk['category'] ?? '-' }}
                                </td>
                                <td style="word-wrap: break-word !important; overflow-wrap: break-word !important; white-space: normal !important; padding-right: 1rem;">
                                    {{ $risk['description'] ?? '-' }}
                                </td>
                                <td>
                                    <i class="fas fa-user" style="color: var(--success); margin-right: 0.5rem;"></i>
                                    {{ $risk['person']['name'] ?? '-' }}
                                </td>
                                <td>
                                    <i class="fas fa-map-marker-alt" style="color: var(--danger); margin-right: 0.5rem;"></i>
                                    {{ $risk['location'] ?? '-' }}
                                </td>
                            </tr>

                            <!-- Modal -->
                            <div class="modal fade" id="detailModal-{{ $risk['id'] }}" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">
                                                <i class="fas fa-info-circle"></i>
                                                Detail Incident #{{ $risk['id'] }}
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="detail-item">
                                                <div class="detail-label">Incident ID</div>
                                                <div class="detail-value">#{{ $risk['id'] ?? '-' }}</div>
                                            </div>
                                            <div class="detail-item">
                                                <div class="detail-label">Severity Level</div>
                                                <div class="detail-value">{{ ucfirst($risk['severity'] ?? 'Low') }} Risk</div>
                                            </div>
                                            <div class="detail-item">
                                                <div class="detail-label">Date & Time</div>
                                                <div class="detail-value">{{ $risk['raisedAt'] ? \Carbon\Carbon::parse($risk['raisedAt'])->format('l, d F Y \a\t H:i') : 'Not specified' }}</div>
                                            </div>
                                            <div class="detail-item">
                                                <div class="detail-label">Category</div>
                                                <div class="detail-value">{{ $risk['category'] ?? 'Uncategorized' }}</div>
                                            </div>
                                            <div class="detail-item">
                                                <div class="detail-label">Description</div>
                                                <div class="detail-value">{{ $risk['description'] ?? 'No description' }}</div>
                                            </div>
                                            <div class="detail-item">
                                                <div class="detail-label">Reporter</div>
                                                <div class="detail-value">{{ $risk['person']['name'] ?? 'Unknown' }}</div>
                                            </div>
                                            <div class="detail-item">
                                                <div class="detail-label">Location</div>
                                                <div class="detail-value">{{ $risk['location'] ?? 'Not specified' }}</div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button class="btn-modern btn-secondary" data-bs-dismiss="modal">
                                                <i class="fas fa-times"></i>
                                                Close
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if($pages > 1)
        <div class="pagination-container">
            <nav aria-label="Page navigation">
                <ul class="pagination">
                    <li class="page-item {{ $page == 1 ? 'disabled' : '' }}">
                        <a class="page-link" href="?{{ http_build_query(array_merge(request()->except('page'), ['page'=>1])) }}">First</a>
                    </li>
                    <li class="page-item {{ $page == 1 ? 'disabled' : '' }}">
                        <a class="page-link" href="?{{ http_build_query(array_merge(request()->except('page'), ['page'=>$page-1])) }}">&laquo;</a>
                    </li>
                    @for($i=max(1, $page-2); $i<=min($pages, $page+2); $i++)
                        <li class="page-item {{ $page == $i ? 'active' : '' }}">
                            <a class="page-link" href="?{{ http_build_query(array_merge(request()->except('page'), ['page'=>$i])) }}">{{ $i }}</a>
                        </li>
                    @endfor
                    <li class="page-item {{ $page == $pages ? 'disabled' : '' }}">
                        <a class="page-link" href="?{{ http_build_query(array_merge(request()->except('page'), ['page'=>$page+1])) }}">&raquo;</a>
                    </li>
                    <li class="page-item {{ $page == $pages ? 'disabled' : '' }}">
                        <a class="page-link" href="?{{ http_build_query(array_merge(request()->except('page'), ['page'=>$pages])) }}">Last</a>
                    </li>
                </ul>
            </nav>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Modal handlers
    const clickableRows = document.querySelectorAll('.clickable-row');
    clickableRows.forEach(row => {
        row.addEventListener('click', function() {
            const modalId = this.getAttribute('data-modal-id');
            if (modalId) {
                const modal = document.getElementById(modalId);
                if (modal && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    const bsModal = new bootstrap.Modal(modal);
                    bsModal.show();
                }
            }
        });
    });

    // Export handlers
    const exportButtons = {
        exportGoogleSheetsBtn: () => alert('Google Sheets export coming soon!'),
        copyClipboardBtn: copyTableToClipboard,
        printTableBtn: printTable
    };

    Object.entries(exportButtons).forEach(([id, handler]) => {
        const btn = document.getElementById(id);
        if (btn) btn.addEventListener('click', handler);
    });

    function copyTableToClipboard() {
        const table = document.getElementById('historyTable');
        if (!table) {
            alert('Table not found!');
            return;
        }

        try {
            const range = document.createRange();
            range.selectNode(table);
            const selection = window.getSelection();
            selection.removeAllRanges();
            selection.addRange(range);
            document.execCommand('copy');
            selection.removeAllRanges();
            alert('Table copied to clipboard!');
        } catch (err) {
            alert('Failed to copy table.');
        }
    }

    function printTable() {
        const printContent = document.getElementById('historyTable').outerHTML;
        const printWindow = window.open('', '_blank', 'width=1200,height=800');
        
        printWindow.document.write(`
            <html>
                <head>
                    <title>History Data Risiko</title>
                    <style>
                        body { font-family: Arial, sans-serif; padding: 20px; }
                        table { width: 100%; border-collapse: collapse; }
                        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                        th { background-color: #f2f2f2; }
                    </style>
                </head>
                <body>
                    <h1>History Data Risiko</h1>
                    <p>Printed on: ${new Date().toLocaleString()}</p>
                    ${printContent}
                </body>
            </html>
        `);
        
        printWindow.document.close();
        printWindow.onload = function() {
            printWindow.print();
            printWindow.close();
        };
    }

    // Export form handler
    const exportForm = document.getElementById('exportForm');
    if (exportForm) {
        exportForm.addEventListener('submit', function() {
            const btn = this.querySelector('button[type="submit"]');
            const originalText = btn.innerHTML;
            
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Exporting...';
            btn.disabled = true;
            
            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }, 2000);
        });
    }

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
            e.preventDefault();
            const searchInput = document.querySelector('input[name="search"]');
            if (searchInput) {
                searchInput.focus();
                searchInput.select();
            }
        }
    });

    console.log('History Data UI loaded successfully!');
});
</script>
@endpush