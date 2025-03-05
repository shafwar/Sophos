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

<style>
/* Navbar Specific Styles */
.navbar {
    background-color: var(--primary-blue);
    padding: 0.8rem 2rem;
    transition: all 0.3s ease;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.navbar-brand {
    color: white;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.navbar-brand i {
    font-size: 1.4rem;
    color: var(--secondary-blue);
}

.navbar-brand:hover {
    color: white;
}

.navbar-nav {
    margin-left: auto;
    display: flex;
    align-items: center;
}

.navbar-nav .nav-link {
    color: rgba(255, 255, 255, 0.9) !important;
    padding: 0.8rem 1.2rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    position: relative;
    transition: all 0.2s ease;
}

.navbar-nav .nav-link i {
    font-size: 1.1rem;
}

.navbar-nav .nav-link:hover {
    color: white !important;
}

.navbar-nav .nav-link:hover::after {
    width: 80%;
}

.navbar-nav .nav-link::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    width: 0;
    height: 2px;
    background-color: var(--secondary-blue);
    transition: all 0.3s ease;
    transform: translateX(-50%);
}

/* Dropdown Specific Styles */
.dropdown-menu {
    background-color: white;
    border: none;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    animation: fadeInDown 0.3s ease;
    margin-top: 0.5rem;
}

.dropdown-item {
    display: flex;
    align-items: center;
    padding: 0.7rem 1.2rem;
    transition: all 0.2s ease;
}

.dropdown-item:hover {
    background-color: #f5f5f5;
    transform: translateX(5px);
}

.dropdown-item i {
    width: 20px;
    text-align: center;
    margin-right: 10px;
    color: var(--primary-blue);
}

.dropdown-item.text-danger {
    color: var(--danger-color) !important;
}

.dropdown-item.text-danger:hover {
    background-color: rgba(220, 53, 69, 0.1);
}

/* Mobile Responsive Styles */
@media (max-width: 991.98px) {
    .navbar {
        padding: 0.5rem 1rem;
    }

    .navbar-nav {
        padding: 1rem 0;
    }

    .navbar-nav .nav-link {
        padding: 0.5rem 1rem;
    }

    .navbar-nav .nav-link::after {
        display: none;
    }

    .dropdown-menu {
        border: none;
        background: transparent;
        box-shadow: none;
        padding: 0 1rem;
    }

    .dropdown-item {
        color: rgba(255, 255, 255, 0.9) !important;
        padding: 0.5rem 1rem;
    }

    .dropdown-item:hover {
        background: rgba(255, 255, 255, 0.1);
        color: white !important;
    }
}

/* Navbar Toggler Custom Styles */
.navbar-toggler {
    border: none;
    padding: 0.5rem;
    color: white;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 4px;
}

.navbar-toggler:focus {
    box-shadow: none;
    outline: none;
}

.navbar-toggler-icon {
    background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 30 30' xmlns='http://www.w3.org/2000/svg'%3e%3cpath stroke='rgba(255, 255, 255, 0.9)' stroke-width='2' stroke-linecap='round' stroke-miterlimit='10' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
}
</style>
