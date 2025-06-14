@extends('layouts.app')

@push('styles')
<style>
    /* Reset dan Global Styles */
    .main-content {
        background: linear-gradient(135deg, #2c3e50 0%, #34495e 50%, #2c3e50 100%);
        min-height: calc(100vh - 76px);
        padding: 20px 0;
        position: relative;
    }

    .main-content::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background:
            radial-gradient(circle at 20% 80%, rgba(120, 119, 198, 0.3) 0%, transparent 50%),
            radial-gradient(circle at 80% 20%, rgba(255, 119, 198, 0.3) 0%, transparent 50%),
            radial-gradient(circle at 40% 40%, rgba(120, 219, 255, 0.2) 0%, transparent 50%);
        pointer-events: none;
    }

    .dashboard-container {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 24px;
        box-shadow:
            0 25px 50px rgba(0, 0, 0, 0.15),
            0 0 0 1px rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(20px);
        padding: 35px;
        margin: 0 auto;
        max-width: 1400px;
        position: relative;
        z-index: 1;
    }

    /* Header Section */
    .dashboard-header {
        margin-bottom: 30px;
    }

    .dashboard-title {
        font-size: 2.2rem;
        font-weight: 700;
        color: #2d3436;
        margin-bottom: 8px;
    }

    .dashboard-subtitle {
        color: #636e72;
        font-size: 1.1rem;
        margin-bottom: 0;
    }

    .header-actions {
        display: flex;
        gap: 12px;
    }

    .header-actions .btn {
        border-radius: 12px;
        padding: 10px 20px;
        font-weight: 600;
        border: none;
        transition: all 0.3s ease;
    }

    .header-actions .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    /* Statistics Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        transition: all 0.4s ease;
        overflow: hidden;
        position: relative;
        cursor: pointer;
    }

    .stat-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--card-color);
    }

    .stat-card.card-success {
        --card-color: linear-gradient(135deg, #00b894, #00a085);
    }

    .stat-card.card-info {
        --card-color: linear-gradient(135deg, #0984e3, #6c5ce7);
    }

    .stat-card.card-primary {
        --card-color: linear-gradient(135deg, #6c5ce7, #a29bfe);
    }

    .stat-card-body {
        padding: 25px;
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .stat-icon {
        width: 70px;
        height: 70px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        color: white;
        background: var(--card-color);
        flex-shrink: 0;
    }

    .stat-content {
        flex: 1;
    }

    .stat-number {
        font-size: 2.8rem;
        font-weight: 800;
        margin: 0;
        color: #2d3436;
        line-height: 1;
    }

    .stat-label {
        font-size: 1.1rem;
        color: #636e72;
        margin: 5px 0 10px 0;
        font-weight: 600;
    }

    .stat-trend {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.9rem;
        color: #00b894;
        font-weight: 600;
    }

    .stat-trend.negative {
        color: #e17055;
    }

    .stat-card-footer {
        background: rgba(0, 0, 0, 0.02);
        padding: 15px 25px;
        font-size: 0.9rem;
        color: #636e72;
        text-align: center;
        font-weight: 500;
    }

    .stat-progress {
        padding: 0 25px 20px;
    }

    .progress {
        height: 8px;
        border-radius: 4px;
        background: rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    .progress-bar {
        border-radius: 4px;
        transition: width 0.6s ease;
    }

    /* Activity Section */
    .activity-section {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        margin-bottom: 30px;
    }

    .activity-header {
        padding: 25px 30px;
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        border-bottom: 1px solid #dee2e6;
    }

    .activity-title {
        font-size: 1.4rem;
        font-weight: 700;
        margin-bottom: 5px;
        color: #2d3436;
    }

    .activity-subtitle {
        color: #636e72;
        margin-bottom: 0;
        font-size: 1rem;
    }

    .activity-controls {
        display: flex;
        gap: 0;
    }

    .activity-controls .btn {
        border-radius: 0;
        font-size: 0.9rem;
        padding: 8px 16px;
        font-weight: 600;
        border: 2px solid #dee2e6;
        background: white;
        color: #636e72;
        transition: all 0.3s ease;
    }

    .activity-controls .btn:first-child {
        border-radius: 10px 0 0 10px;
    }

    .activity-controls .btn:last-child {
        border-radius: 0 10px 10px 0;
    }

    .activity-controls .btn.active {
        background: #007bff;
        color: white;
        border-color: #007bff;
        z-index: 1;
    }

    .activity-search {
        padding: 20px 30px;
        background: #f8f9fa;
    }

    .search-input-group {
        position: relative;
        max-width: 400px;
    }

    .search-input-group .form-control {
        border-radius: 12px;
        padding: 12px 20px 12px 50px;
        border: 2px solid #e9ecef;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .search-input-group .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .search-icon {
        position: absolute;
        left: 18px;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
        z-index: 3;
    }

    /* Timeline */
    .activity-timeline {
        padding: 30px;
        max-height: 700px;
        overflow-y: auto;
    }

    .timeline-item {
        display: flex;
        margin-bottom: 25px;
        opacity: 0;
        animation: fadeInUp 0.6s ease forwards;
        position: relative;
    }

    .timeline-item:nth-child(1) { animation-delay: 0.1s; }
    .timeline-item:nth-child(2) { animation-delay: 0.2s; }
    .timeline-item:nth-child(3) { animation-delay: 0.3s; }
    .timeline-item:nth-child(4) { animation-delay: 0.4s; }
    .timeline-item:nth-child(5) { animation-delay: 0.5s; }

    .timeline-item::before {
        content: '';
        position: absolute;
        left: 22px;
        top: 50px;
        width: 2px;
        height: calc(100% + 5px);
        background: #e9ecef;
        z-index: 1;
    }

    .timeline-item:last-child::before {
        display: none;
    }

    .timeline-marker {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 20px;
        flex-shrink: 0;
        color: white;
        font-size: 16px;
        z-index: 2;
        position: relative;
        border: 3px solid white;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .marker-admin {
        background: linear-gradient(135deg, #fd79a8, #e84393);
    }

    .marker-user {
        background: linear-gradient(135deg, #74b9ff, #0984e3);
    }

    .timeline-content {
        flex: 1;
        background: white;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid #f1f3f4;
        transition: all 0.3s ease;
    }

    .timeline-content:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
    }

    .timeline-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 15px;
    }

    .user-info {
        display: flex;
        align-items: center;
        gap: 15px;
        flex: 1;
    }

    .user-avatar {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: white;
        font-size: 16px;
        border: 2px solid white;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .avatar-admin {
        background: linear-gradient(135deg, #fd79a8, #e84393);
    }

    .avatar-user {
        background: linear-gradient(135deg, #74b9ff, #0984e3);
    }

    .user-details h6 {
        font-size: 1.1rem;
        font-weight: 700;
        margin: 0 0 4px 0;
        color: #2d3436;
    }

    .user-role {
        font-size: 0.8rem;
        padding: 3px 10px;
        border-radius: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .role-admin {
        background: rgba(253, 121, 168, 0.15);
        color: #e84393;
    }

    .role-user {
        background: rgba(116, 185, 255, 0.15);
        color: #0984e3;
    }

    .time-badge {
        background: #f8f9fa;
        color: #6c757d;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        border: 1px solid #e9ecef;
    }

    .activity-description {
        color: #495057;
        margin: 0 0 15px 0;
        font-size: 1rem;
        line-height: 1.6;
        font-weight: 500;
    }

    .activity-meta {
        display: flex;
        gap: 20px;
        padding-top: 12px;
        border-top: 1px solid #f1f3f4;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.85rem;
        color: #868e96;
        font-weight: 500;
    }

    .meta-item i {
        font-size: 0.8rem;
        color: #adb5bd;
    }

    .activity-footer {
        padding: 20px 30px;
        text-align: center;
        background: #f8f9fa;
        border-top: 1px solid #e9ecef;
    }

    .load-more-btn {
        background: linear-gradient(135deg, #74b9ff, #0984e3);
        border: none;
        color: white;
        padding: 12px 30px;
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .load-more-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(116, 185, 255, 0.4);
        background: linear-gradient(135deg, #0984e3, #74b9ff);
    }

    /* Mini Stats Grid */
    .mini-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 20px;
    }

    .mini-stat-card {
        background: white;
        border-radius: 16px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 15px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        border: 1px solid #f1f3f4;
    }

    .mini-stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    }

    .mini-stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 20px;
        flex-shrink: 0;
    }

    .mini-stat-content h4 {
        font-size: 1.6rem;
        font-weight: 700;
        margin: 0;
        color: #2d3436;
    }

    .mini-stat-content p {
        margin: 0;
        color: #636e72;
        font-size: 0.9rem;
        font-weight: 600;
    }

    /* Enhanced Modal */
    .modal-content {
        border: none;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    }

    .modal-header {
        background: linear-gradient(135deg, #007bff, #0056b3);
        color: white;
        padding: 25px 30px;
        border-bottom: none;
    }

    .modal-title {
        font-size: 1.3rem;
        font-weight: 700;
    }

    .modal-subtitle {
        font-size: 0.9rem;
        opacity: 0.9;
        margin: 0;
    }

    .user-modal-controls {
        padding: 20px 30px;
        background: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
    }

    .modal-search-input {
        border-radius: 12px;
        border: 2px solid #e9ecef;
        padding: 10px 16px;
        transition: all 0.3s ease;
    }

    .modal-search-input:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .table-hover tbody tr:hover {
        background: rgba(0, 123, 255, 0.05);
    }

    .modal-footer {
        background: #f8f9fa;
        border-top: 1px solid #e9ecef;
        padding: 20px 30px;
    }

    /* Animations */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.05);
        }
    }

    .stat-card:hover .stat-icon {
        animation: pulse 2s infinite;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .dashboard-container {
            margin: 0 15px;
            padding: 20px;
            border-radius: 15px;
        }

        .dashboard-header {
            text-align: center;
            margin-bottom: 25px;
        }

        .dashboard-title {
            font-size: 1.8rem;
        }

        .header-actions {
            justify-content: center;
            margin-top: 15px;
        }

        .stats-grid {
            grid-template-columns: 1fr;
            gap: 15px;
        }

        .stat-card-body {
            padding: 20px;
            gap: 15px;
        }

        .stat-number {
            font-size: 2.2rem;
        }

        .activity-header,
        .activity-search,
        .activity-timeline,
        .activity-footer {
            padding: 20px;
        }

        .timeline-item {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
        }

        .timeline-marker {
            margin-right: 0;
        }

        .timeline-item::before {
            display: none;
        }

        .timeline-header {
            flex-direction: column;
            gap: 10px;
            align-items: flex-start;
        }

        .activity-meta {
            flex-direction: column;
            gap: 8px;
        }

        .mini-stats-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        .mini-stat-card {
            padding: 15px;
            gap: 12px;
        }

        .mini-stat-content h4 {
            font-size: 1.3rem;
        }
    }

    @media (max-width: 480px) {
        .mini-stats-grid {
            grid-template-columns: 1fr;
        }

        .activity-controls {
            flex-direction: column;
        }

        .activity-controls .btn {
            border-radius: 8px !important;
            margin-bottom: 5px;
        }
    }

    /* Custom Scrollbar */
    .activity-timeline::-webkit-scrollbar {
        width: 6px;
    }

    .activity-timeline::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }

    .activity-timeline::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 3px;
    }

    .activity-timeline::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }
</style>
@endpush

@section('content')
<div class="main-content">
    <div class="container-fluid">
        <div class="dashboard-container">
            <!-- Header Section -->
            <div class="dashboard-header">
                <div class="row align-items-center">
                    <div class="col-lg-8 col-md-12">
                        <h1 class="dashboard-title">Activity Dashboard</h1>
                        <p class="dashboard-subtitle">Monitor user activities and system statistics</p>
                    </div>
                    <div class="col-lg-4 col-md-12">
                        <div class="header-actions justify-content-lg-end justify-content-center">
                            <button class="btn btn-outline-primary" id="refreshBtn">
                                <i class="fas fa-sync-alt me-2"></i>Refresh
                            </button>
                            @if(auth()->user()->role === 'user')
                                <a href="{{ route('activity-log.export') }}" class="btn btn-primary ms-2">
                                    <i class="fas fa-download me-2"></i>Export
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="stats-grid">
                <!-- Total Users Card -->
                <div class="stat-card card-success" id="showUserList">
                    <div class="stat-card-body">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-number">{{ $totalUsers }}</h3>
                            <p class="stat-label">Total Users</p>
                            <div class="stat-trend">
                                <i class="fas fa-arrow-up"></i>
                                <span>12% from last month</span>
                            </div>
                        </div>
                    </div>
                    <div class="stat-card-footer">
                        <i class="fas fa-mouse-pointer me-2"></i>Click to view details
                    </div>
                </div>

                <!-- Active Users Card -->
                <div class="stat-card card-info">
                    <div class="stat-card-body">
                        <div class="stat-icon">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-number">{{ $activeUsers }}</h3>
                            <p class="stat-label">Active Users</p>
                            <div class="stat-trend">
                                <i class="fas fa-arrow-up"></i>
                                <span>8% increase</span>
                            </div>
                        </div>
                    </div>
                    <div class="stat-progress">
                        <div class="progress">
                            <div class="progress-bar bg-info" style="width: {{ $totalUsers > 0 ? ($activeUsers / $totalUsers) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                </div>

                <!-- Today's Logins Card -->
                <div class="stat-card card-primary">
                    <div class="stat-card-body">
                        <div class="stat-icon">
                            <i class="fas fa-sign-in-alt"></i>
                        </div>
                        <div class="stat-content">
                            <h3 class="stat-number">{{ $todaysLogins }}</h3>
                            <p class="stat-label">Today's Logins</p>
                            <div class="stat-trend negative">
                                <i class="fas fa-arrow-down"></i>
                                <span>5% decrease</span>
                            </div>
                        </div>
                    </div>
                    <div class="stat-progress">
                        <canvas id="loginChart" width="100" height="40"></canvas>
                    </div>
                </div>
            </div>

            <!-- Activities Section -->
            <div class="activity-section">
                <div class="activity-header">
                    <div class="row align-items-center">
                        <div class="col-lg-6 col-md-12 mb-3 mb-lg-0">
                            <h5 class="activity-title">Recent Activities</h5>
                            <p class="activity-subtitle">Real-time user activity monitoring</p>
                        </div>
                        <div class="col-lg-6 col-md-12">
                            <div class="activity-controls justify-content-lg-end justify-content-center">
                                <button class="btn active" data-filter="all">All</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="activity-search">
                    <div class="search-input-group">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" class="form-control" placeholder="Search activities..." id="activitySearch">
                    </div>
                </div>

                <div class="activity-timeline">
                    @foreach($logs as $index => $log)
                        @php
                            $userName = $log->user_name ?? auth()->user()->name;
                        @endphp
                        <div class="timeline-item" data-user-type="{{ strtolower($userName) }}">
                            <div class="timeline-marker">
                                <i class="fas {{ strtolower($userName) === 'admin' ? 'fa-user-shield' : 'fa-user' }}"></i>
                            </div>
                            <div class="timeline-content">
                                <div class="timeline-header">
                                    <div class="user-info">
                                        <div class="user-avatar {{ strtolower($userName) }}">
                                            {{ substr($userName, 0, 1) }}
                                        </div>
                                        <div class="user-details">
                                            <h6>{{ $userName }}</h6>
                                            <span class="user-role {{ strtolower($userName) === 'admin' ? 'role-admin' : 'role-user' }}">
                                                {{ strtolower($userName) === 'admin' ? 'Administrator' : 'User' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="activity-time">
                                        <span class="time-badge">{{ \Carbon\Carbon::parse($log->created_at)->diffForHumans() }}</span>
                                    </div>
                                </div>
                                <div class="timeline-body">
                                    <p class="activity-description">{{ $log->activity }}</p>
                                    <div class="activity-meta">
                                        <span class="meta-item">
                                            <i class="fas fa-clock"></i>
                                            {{ \Carbon\Carbon::parse($log->created_at)->format('H:i:s') }}
                                        </span>
                                        <span class="meta-item">
                                            <i class="fas fa-calendar"></i>
                                            {{ \Carbon\Carbon::parse($log->created_at)->format('M d, Y') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="activity-footer">
                    <button class="btn load-more-btn" id="loadMoreBtn">
                        <i class="fas fa-chevron-down me-2"></i>Load More Activities
                    </button>
                </div>
            </div>

            <!-- Mini Statistics -->
            <div class="mini-stats-grid">
                <div class="mini-stat-card">
                    <div class="mini-stat-icon bg-success">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="mini-stat-content">
                        <h4>98.5%</h4>
                        <p>System Uptime</p>
                    </div>
                </div>
                <div class="mini-stat-card">
                    <div class="mini-stat-icon bg-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="mini-stat-content">
                        <h4>3</h4>
                        <p>Failed Logins</p>
                    </div>
                </div>
                <div class="mini-stat-card">
                    <div class="mini-stat-icon bg-info">
                        <i class="fas fa-server"></i>
                    </div>
                    <div class="mini-stat-content">
                        <h4>2.1GB</h4>
                        <p>Storage Used</p>
                    </div>
                </div>
                <div class="mini-stat-card">
                    <div class="mini-stat-icon bg-primary">
                        <i class="fas fa-globe"></i>
                    </div>
                    <div class="mini-stat-content">
                        <h4>145</h4>
                        <p>API Calls</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Enhanced User Management Modal -->
<div class="modal fade" id="userListModal" tabindex="-1" aria-labelledby="userListModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title" id="userListModalLabel">
                        <i class="fas fa-users me-2"></i>User Management
                    </h5>
                    <p class="modal-subtitle">Manage system users and their permissions</p>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="user-modal-controls">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" class="form-control modal-search-input" placeholder="Search users..." id="userSearch">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <select class="form-select modal-search-input" id="roleFilter">
                            <option value="">All Roles</option>
                            <option value="admin">Admin</option>
                            <option value="user">User</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0" style="width: 50px;">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="selectAll">
                                    </div>
                                </th>
                                <th class="border-0">User</th>
                                <th class="border-0">Role</th>
                                <th class="border-0">Status</th>
                                <th class="border-0">Last Login</th>
                                <th class="border-0" style="width: 150px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="userListTableBody">
                            <!-- Data akan diisi via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <div class="d-flex justify-content-between w-100 align-items-center">
                    <div class="selected-info">
                        <span id="selectedCount">0</span> users selected
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-danger" id="bulkDeleteBtn" disabled>
                            <i class="fas fa-trash me-2"></i>Delete Selected
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(function() {
    // Initialize mini chart
    const ctx = document.getElementById('loginChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    data: [12, 19, 3, 5, 2, 3, 20],
                    borderColor: '#6c5ce7',
                    backgroundColor: 'rgba(108, 92, 231, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: { display: false },
                    y: { display: false }
                },
                elements: {
                    point: { radius: 0 }
                }
            }
        });
    }

    // Activity filtering
    $('.activity-controls .btn').on('click', function() {
        $('.activity-controls .btn').removeClass('active');
        $(this).addClass('active');

        const filter = $(this).data('filter');
        $('.timeline-item').each(function() {
            const userType = $(this).data('user-type');
            if (filter === 'all' || userType === filter) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // Activity search
    $('#activitySearch').on('input', function() {
        const searchTerm = $(this).val().toLowerCase();
        $('.timeline-item').each(function() {
            const text = $(this).text().toLowerCase();
            if (text.includes(searchTerm)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // Refresh button with animation
    $('#refreshBtn').on('click', function() {
        const btn = $(this);
        const icon = btn.find('.fa-sync-alt');

        btn.prop('disabled', true);
        icon.addClass('fa-spin');

        // Simulate refresh
        setTimeout(() => {
            icon.removeClass('fa-spin');
            btn.prop('disabled', false);

            // Show success message
            showToast('success', 'Dashboard Refreshed', 'Data has been updated successfully');
        }, 1500);
    });

    // Load more activities
    $('#loadMoreBtn').on('click', function() {
        const btn = $(this);
        const originalText = btn.html();

        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Loading more activities...');

        // Simulate loading
        setTimeout(() => {
            btn.prop('disabled', false).html(originalText);
            showToast('info', 'No More Data', 'All activities have been loaded');
        }, 2000);
    });

    // Enhanced user list modal
    $('#showUserList').on('click', function() {
        $('#userListModal').modal('show');
        loadUserList();
    });

    function loadUserList() {
        // Show loading state
        $('#userListTableBody').html(`
            <tr>
                <td colspan="6" class="text-center py-4">
                    <div class="d-flex justify-content-center align-items-center">
                        <div class="spinner-border text-primary me-3" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <span>Loading users...</span>
                    </div>
                </td>
            </tr>
        `);

        // Simulate API call
        setTimeout(() => {
            $.get("{{ route('admin.user-list') }}", function(data) {
                let html = '';
                data.forEach(function(user, index) {
                    const isAdmin = user.role === 'admin';
                    const avatar = user.name.charAt(0).toUpperCase();
                    const statusBadge = user.is_active ?
                        '<span class="badge bg-success">Active</span>' :
                        '<span class="badge bg-secondary">Inactive</span>';

                    html += `
                        <tr class="user-row" data-role="${user.role}">
                            <td>
                                <div class="form-check">
                                    <input class="form-check-input user-checkbox" type="checkbox" value="${user.id}" ${isAdmin ? 'disabled' : ''}>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="user-avatar ${isAdmin ? 'avatar-admin' : 'avatar-user'} me-3">
                                        ${avatar}
                                    </div>
                                    <div>
                                        <h6 class="mb-0">${user.name}</h6>
                                        <small class="text-muted">${user.email}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="user-role ${isAdmin ? 'role-admin' : 'role-user'}">
                                    ${isAdmin ? 'Administrator' : 'User'}
                                </span>
                            </td>
                            <td>${statusBadge}</td>
                            <td>
                                <small class="text-muted">
                                    ${user.last_login ? new Date(user.last_login).toLocaleDateString() : 'Never'}
                                </small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button class="btn btn-sm btn-outline-primary" onclick="viewUser(${user.id})" title="View User">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    ${!isAdmin ? `
                                        <button class="btn btn-sm btn-outline-warning" onclick="editUser(${user.id})" title="Edit User">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form method="POST" action="/admin/delete-user/${user.id}" class="delete-user-form d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger delete-btn" title="Delete User">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    ` : `
                                        <button class="btn btn-sm btn-secondary" disabled title="Protected Admin">
                                            <i class="fas fa-shield-alt"></i>
                                        </button>
                                    `}
                                </div>
                            </td>
                        </tr>
                    `;
                });
                $('#userListTableBody').html(html);
                updateSelectedCount();
            }).fail(function() {
                $('#userListTableBody').html(`
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <div class="d-flex flex-column align-items-center">
                                <i class="fas fa-exclamation-triangle fa-2x mb-3 text-warning"></i>
                                <h6>Failed to load user data</h6>
                                <p class="mb-0">Please check your connection and try again</p>
                            </div>
                        </td>
                    </tr>
                `);
            });
        }, 1000);
    }

    // User search and filtering
    $('#userSearch').on('input', function() {
        filterUsers();
    });

    $('#roleFilter').on('change', function() {
        filterUsers();
    });

    function filterUsers() {
        const searchTerm = $('#userSearch').val().toLowerCase();
        const roleFilter = $('#roleFilter').val();

        $('.user-row').each(function() {
            const text = $(this).text().toLowerCase();
            const role = $(this).data('role');

            const matchesSearch = text.includes(searchTerm);
            const matchesRole = !roleFilter || role === roleFilter;

            if (matchesSearch && matchesRole) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }

    // Select all functionality
    $('#selectAll').on('change', function() {
        const isChecked = $(this).prop('checked');
        $('.user-checkbox:not(:disabled)').prop('checked', isChecked);
        updateSelectedCount();
    });

    // Individual checkbox change
    $(document).on('change', '.user-checkbox', function() {
        updateSelectedCount();

        const totalCheckboxes = $('.user-checkbox:not(:disabled)').length;
        const checkedCheckboxes = $('.user-checkbox:checked').length;

        $('#selectAll').prop('indeterminate', checkedCheckboxes > 0 && checkedCheckboxes < totalCheckboxes);
        $('#selectAll').prop('checked', checkedCheckboxes === totalCheckboxes);
    });

    function updateSelectedCount() {
        const selectedCount = $('.user-checkbox:checked').length;
        $('#selectedCount').text(selectedCount);
        $('#bulkDeleteBtn').prop('disabled', selectedCount === 0);
    }

    // Bulk delete functionality
    $('#bulkDeleteBtn').on('click', function() {
        const selectedIds = [];
        $('.user-checkbox:checked').each(function() {
            selectedIds.push($(this).val());
        });

        if (selectedIds.length === 0) return;

        Swal.fire({
            title: 'Confirm Bulk Delete',
            html: `Are you sure you want to delete <strong>${selectedIds.length}</strong> user(s)?<br><small class="text-muted">This action cannot be undone.</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete them!',
            cancelButtonText: 'Cancel',
            customClass: {
                popup: 'animate__animated animate__zoomIn'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                performBulkDelete(selectedIds);
            }
        });
    });

    function performBulkDelete(userIds) {
        const deleteBtn = $('#bulkDeleteBtn');
        deleteBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Deleting...');

        // Simulate deletion process
        setTimeout(() => {
            Swal.fire({
                title: 'Success!',
                html: `<strong>${userIds.length}</strong> user(s) have been deleted successfully.`,
                icon: 'success',
                timer: 2000,
                showConfirmButton: false,
                customClass: {
                    popup: 'animate__animated animate__zoomIn'
                }
            });

            // Remove deleted rows from table
            userIds.forEach(id => {
                $(`.user-checkbox[value="${id}"]`).closest('tr').fadeOut(300, function() {
                    $(this).remove();
                    updateSelectedCount();
                });
            });

            deleteBtn.prop('disabled', false).html('<i class="fas fa-trash me-2"></i>Delete Selected');
        }, 2000);
    }

    // Individual delete with enhanced confirmation
    $(document).on('submit', '.delete-user-form', function(e) {
        e.preventDefault();
        const form = $(this);
        const userName = form.closest('tr').find('h6').text();

        Swal.fire({
            title: 'Confirm Delete',
            html: `Are you sure you want to delete user <strong>${userName}</strong>?<br><small class="text-muted">This action cannot be undone.</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
            customClass: {
                popup: 'animate__animated animate__zoomIn'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const deleteBtn = form.find('.delete-btn');
                deleteBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

                // Simulate deletion
                setTimeout(() => {
                    Swal.fire({
                        title: 'Deleted!',
                        text: 'User has been deleted successfully.',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });

                    form.closest('tr').fadeOut(300, function() {
                        $(this).remove();
                        updateSelectedCount();
                    });
                }, 1000);
            }
        });
    });

    // User action functions
    window.viewUser = function(userId) {
        Swal.fire({
            title: 'User Details',
            html: '<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x text-primary"></i><br><br>Loading user details...</div>',
            showConfirmButton: false,
            allowOutsideClick: false,
            customClass: {
                popup: 'animate__animated animate__zoomIn'
            }
        });

        setTimeout(() => {
            Swal.fire({
                title: 'User Details',
                html: `
                    <div class="text-start">
                        <div class="text-center mb-4">
                            <div class="user-avatar avatar-user mx-auto mb-3" style="width: 80px; height: 80px; font-size: 2rem;">U</div>
                            <h5 class="mb-1">User Name</h5>
                            <p class="text-muted">user@example.com</p>
                        </div>
                        <hr>
                        <div class="row g-3">
                            <div class="col-6"><strong>Role:</strong></div>
                            <div class="col-6">User</div>
                            <div class="col-6"><strong>Status:</strong></div>
                            <div class="col-6"><span class="badge bg-success">Active</span></div>
                            <div class="col-6"><strong>Last Login:</strong></div>
                            <div class="col-6">2 hours ago</div>
                            <div class="col-6"><strong>Created:</strong></div>
                            <div class="col-6">Jan 15, 2025</div>
                            <div class="col-6"><strong>Total Logins:</strong></div>
                            <div class="col-6">127 times</div>
                            <div class="col-6"><strong>Account Type:</strong></div>
                            <div class="col-6">Standard User</div>
                        </div>
                    </div>
                `,
                confirmButtonText: 'Close',
                width: '500px',
                customClass: {
                    popup: 'animate__animated animate__zoomIn'
                }
            });
        }, 1000);
    };

    window.editUser = function(userId) {
        Swal.fire({
            title: 'Edit User',
            html: `
                <form id="editUserForm" class="text-start">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Name</label>
                        <input type="text" class="form-control" name="name" value="User Name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Email</label>
                        <input type="email" class="form-control" name="email" value="user@example.com" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Role</label>
                        <select class="form-select" name="role">
                            <option value="user" selected>User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Status</label>
                        <select class="form-select" name="status">
                            <option value="1" selected>Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </form>
            `,
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-save me-2"></i>Save Changes',
            cancelButtonText: 'Cancel',
            width: '500px',
            customClass: {
                popup: 'animate__animated animate__zoomIn'
            },
            preConfirm: () => {
                return new Promise((resolve) => {
                    // Simulate save
                    setTimeout(() => {
                        resolve();
                    }, 1000);
                });
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Success!',
                    text: 'User updated successfully.',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        });
    };

    // Export functionality
    $('#exportBtn').on('click', function() {
        const btn = $(this);

        Swal.fire({
            title: 'Export Data',
            text: 'Choose export format:',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-file-csv me-2"></i>Export as CSV',
            cancelButtonText: '<i class="fas fa-file-pdf me-2"></i>Export as PDF',
            reverseButtons: true,
            customClass: {
                popup: 'animate__animated animate__zoomIn'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                exportData('csv');
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                exportData('pdf');
            }
        });
    });

    function exportData(format) {
        Swal.fire({
            title: 'Exporting...',
            html: '<div class="progress mb-3"><div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 0%"></div></div><p class="mb-0">Preparing your data...</p>',
            showConfirmButton: false,
            allowOutsideClick: false,
            customClass: {
                popup: 'animate__animated animate__zoomIn'
            }
        });

        let progress = 0;
        const interval = setInterval(() => {
            progress += 10;
            $('.progress-bar').css('width', progress + '%');

            if (progress >= 100) {
                clearInterval(interval);
                setTimeout(() => {
                    Swal.fire({
                        title: 'Export Complete!',
                        text: `Data exported successfully as ${format.toUpperCase()}.`,
                        icon: 'success',
                        confirmButtonText: '<i class="fas fa-download me-2"></i>Download',
                        timer: 3000
                    });
                }, 500);
            }
        }, 200);
    }

    // Toast notification function
    function showToast(type, title, message) {
        const iconMap = {
            success: 'fa-check-circle text-success',
            error: 'fa-times-circle text-danger',
            warning: 'fa-exclamation-triangle text-warning',
            info: 'fa-info-circle text-info'
        };

        const toast = $(`
            <div class="toast" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                <div class="toast-header">
                    <i class="fas ${iconMap[type]} me-2"></i>
                    <strong class="me-auto">${title}</strong>
                    <small>now</small>
                    <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
                </div>
                <div class="toast-body">${message}</div>
            </div>
        `);

        $('body').append(toast);
        const bsToast = new bootstrap.Toast(toast[0], {
            autohide: true,
            delay: 3000
        });
        bsToast.show();

        toast.on('hidden.bs.toast', function() {
            $(this).remove();
        });
    }

    // Real-time updates simulation
    let updateInterval = setInterval(() => {
        if (Math.random() > 0.8) {
            addNewActivity();
        }
    }, 15000);

    function addNewActivity() {
        const activities = [
            'Logged into system',
            'Updated profile information',
            'Changed password',
            'Viewed dashboard',
            'Downloaded report',
            'Created new document',
            'Uploaded file',
            'Logged out'
        ];

        const users = ['Admin', 'John Doe', 'Jane Smith', 'Mike Johnson', 'Sarah Wilson'];
        const randomActivity = activities[Math.floor(Math.random() * activities.length)];
        const randomUser = users[Math.floor(Math.random() * users.length)];
        const isAdmin = randomUser === 'Admin';

        const newActivity = `
            <div class="timeline-item" data-user-type="${isAdmin ? 'admin' : 'user'}" style="opacity: 0;">
                <div class="timeline-marker ${isAdmin ? 'marker-admin' : 'marker-user'}">
                    <i class="fas ${isAdmin ? 'fa-crown' : 'fa-user'}"></i>
                </div>
                <div class="timeline-content">
                    <div class="timeline-header">
                        <div class="user-info">
                            <div class="user-avatar ${isAdmin ? 'avatar-admin' : 'avatar-user'}">
                                ${randomUser.charAt(0)}
                            </div>
                            <div class="user-details">
                                <h6>${randomUser}</h6>
                                <span class="user-role ${isAdmin ? 'role-admin' : 'role-user'}">
                                    ${isAdmin ? 'Administrator' : 'User'}
                                </span>
                            </div>
                        </div>
                        <div class="activity-time">
                            <span class="time-badge">just now</span>
                        </div>
                    </div>
                    <div class="timeline-body">
                        <p class="activity-description">${randomActivity}</p>
                        <div class="activity-meta">
                            <span class="meta-item">
                                <i class="fas fa-clock"></i>
                                ${new Date().toLocaleTimeString()}
                            </span>
                            <span class="meta-item">
                                <i class="fas fa-calendar"></i>
                                ${new Date().toLocaleDateString()}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        `;

        $('.activity-timeline').prepend(newActivity);
        $('.timeline-item:first').animate({opacity: 1}, 500);

        // Show notification for new activity
        showToast('info', 'New Activity', `${randomUser}: ${randomActivity}`);

        // Remove oldest items if too many
        const timelineItems = $('.timeline-item');
        if (timelineItems.length > 20) {
            timelineItems.slice(20).fadeOut(300, function() {
                $(this).remove();
            });
        }
    }

    // Cleanup on page unload
    $(window).on('beforeunload', function() {
        if (updateInterval) {
            clearInterval(updateInterval);
        }
    });
});
</script>
@endpush
