<!-- Navbar -->
<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container-fluid">
        <!-- Brand -->
        <a class="navbar-brand" href="{{ route('dashboard') }}">
            <i class="fas fa-shield-alt"></i>
            <span>SIPANDI</span>
        </a>

        <!-- Mobile Toggle -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Nav Items -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                
                @if(auth()->user() && auth()->user()->role === 'admin')
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

                     <!-- Threat Analysis -->
                    <li class="nav-item dropdown">
                        <a class="nav-link" href="#" data-bs-toggle="dropdown">
                            <i class="fas fa-shield-virus"></i>
                            <span>Threat Analysis</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('reports')}}">Reports</a></li>
                        </ul>
                    </li>

                    <!-- Pending Approvals -->
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="{{ route('admin.pending-users') }}">
                            <i class="fas fa-user-clock"></i>
                            <span>Pending Approvals</span>
                            @php $pendingCount = \App\Models\User::pending()->count(); @endphp
                            @if($pendingCount > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">{{ $pendingCount }}</span>
                            @endif
                        </a>
                    </li>

                    <!-- Activity Log (khusus admin) -->
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.activity-log') }}">
                            <i class="fas fa-list"></i>
                            <span>Activity Log</span>
                        </a>
                    </li>
                @endif

                <!-- Alerts -->
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-bell"></i>
                        <span>Alerts</span>
                    </a>
                </li>

                <!-- User Icons -->
                <li class="nav-item dropdown">
                    <a class="nav-link" href="#" data-bs-toggle="dropdown">
                        <i class="fas fa-cog"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <div class="dropdown-item d-flex align-items-center justify-content-between">
                                <span><i class="fas fa-moon me-2"></i>Dark Mode</span>
                                <div class="form-check form-switch ms-2">
                                    <input class="form-check-input" type="checkbox" id="darkModeToggle">
                                </div>
                            </div>
                        </li>
                    </ul>
                </li>
                
                <li class="nav-item dropdown">
                    <a class="nav-link" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.show') }}">
                                <i class="fas fa-user-circle me-2"></i>Profile
                            </a>
                        </li>
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