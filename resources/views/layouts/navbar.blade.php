<!-- Navbar -->
<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container-fluid">
        <!-- Brand -->
        <a class="navbar-brand" href="{{route('dashboard')}}">
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