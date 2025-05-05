<!DOCTYPE html>
<html lang="fr">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - IMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        :root {
            --primary-color: #4361ee;
            --primary-hover: #3a56d4;
            --secondary-color: #3f72af;
            --dark-color: #1a3353;
            --light-color: #f9fafc;
            --text-color: #333;
            --text-muted: #6c757d;
            --success-color: #20c997;
            --warning-color: #ffc107;
            --danger-color: #e74c3c;
            --info-color: #3498db;
            --sidebar-width: 250px;
            --sidebar-collapsed-width: 65px;
            --transition-speed: 0.3s;
            --card-border-radius: 0.75rem;
            --box-shadow: 0 0.25rem 1rem rgba(0, 0, 0, 0.1);
            --scrollbar-width: 5px;
            --scrollbar-thumb: rgba(255, 255, 255, 0.3);
            --scrollbar-thumb-hover: rgba(255, 255, 255, 0.5);
            --sidebar-hover-bg: rgba(67, 97, 238, 0.2);
            --sidebar-active-bg: rgba(67, 97, 238, 0.3);
            --sidebar-active-border: rgba(67, 97, 238, 1);
            --sidebar-hover-border: rgba(255, 255, 255, 0.5);
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            overflow-x: hidden;
            font-family: 'Segoe UI', Arial, sans-serif;
            color: var(--text-color);
            background-color: #f5f7fb;
        }

        /* Scrollbar styling */
        ::-webkit-scrollbar {
            width: var(--scrollbar-width);
            height: var(--scrollbar-width);
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--scrollbar-thumb);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--scrollbar-thumb-hover);
        }

        /* Dashboard Header */
        .dashboard-title, .dashboard-username {
            padding-left: 15px;
            font-size: 1.75rem;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 0;
        }

        .dashboard-user-logo img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            margin-right: 10px;
            border-radius: 50%;
            box-shadow: var(--box-shadow);
        }

        .dashboard-username {
            display: inline-block;
            vertical-align: middle;
        }

        .dashboard-username-small {
            font-size: 1.25rem;
            color: var(--secondary-color);
            font-weight: 500;
            margin-right: 8px;
        }

        /* New Sidebar Styling */
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            width: var(--sidebar-collapsed-width);
            background: linear-gradient(180deg, var(--dark-color) 0%, #203a65 100%);
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            transition: all var(--transition-speed) ease;
            z-index: 999;
            overflow-y: auto;
            overflow-x: hidden;
            padding-top: 1.5rem;
            height: 100%;
            display: flex;
            flex-direction: column;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
            scrollbar-color: var(--scrollbar-thumb) transparent;
            will-change: width;
        }

        .sidebar::-webkit-scrollbar {
            width: var(--scrollbar-width);
        }

        .sidebar::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: var(--scrollbar-thumb);
            border-radius: 10px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: var(--scrollbar-thumb-hover);
        }

        .sidebar:hover {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #1a3868 0%, #264375 100%);
        }

        .sidebar nav {
            width: 100%;
            padding-bottom: 2rem;
        }

        .sidebar .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            color: white;
            text-decoration: none;
            transition: all var(--transition-speed) ease;
            white-space: nowrap;
            position: relative;
            border-left: 4px solid transparent;
            margin: 2px 0;
        }

        .sidebar .nav-link:hover {
            background-color: var(--sidebar-hover-bg);
            border-left-color: var(--sidebar-hover-border);
            color: white;
            box-shadow: inset 0 0 10px rgba(255, 255, 255, 0.05);
        }

        .sidebar .nav-link.active {
            background-color: var(--sidebar-active-bg);
            border-left-color: var(--sidebar-active-border);
            font-weight: 500;
            color: white;
            box-shadow: inset 0 0 12px rgba(255, 255, 255, 0.08);
        }

        .sidebar-menu-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: white;
            width: 40px;
            min-width: 40px;
            text-align: center;
            transition: transform 0.2s ease, color 0.3s ease;
        }

        .sidebar .nav-link:hover .sidebar-menu-logo {
            color: #ffffff;
            transform: scale(1.15);
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.4);
        }

        .sidebar .nav-link.active .sidebar-menu-logo {
            color: #ffffff;
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.4);
        }

        .sidebar-link-text {
            margin-left: 10px;
            opacity: 0;
            transition: opacity var(--transition-speed) ease, transform 0.2s ease;
        }

        .sidebar:hover .sidebar-link-text {
            opacity: 1;
        }

        .sidebar .nav-link:hover .sidebar-link-text {
            transform: translateX(3px);
        }

        /* Main Content Area */
        .main-content {
            margin-left: var(--sidebar-collapsed-width);
            padding: 1.5rem;
            transition: all var(--transition-speed) ease;
            min-height: 100vh;
            width: calc(100% - var(--sidebar-collapsed-width));
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            will-change: margin-left, width;
            transform: translateZ(0);
            backface-visibility: hidden;
            perspective: 1000px;
        }

        .sidebar:hover ~ .main-content {
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
            transition-delay: 0.05s;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 0;
            }

            .sidebar:hover {
                width: var(--sidebar-width);
            }

            .main-content {
                margin-left: 0;
                width: 100%;
            }

            .sidebar:hover ~ .main-content {
                margin-left: var(--sidebar-width);
                width: calc(100% - var(--sidebar-width));
            }
        }

        /* Cards Styling */
        .card {
            border: none;
            border-radius: var(--card-border-radius);
            box-shadow: var(--box-shadow);
            transition: transform 0.2s, box-shadow 0.2s;
            margin-bottom: 1.5rem;
            overflow: hidden;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.15);
        }

        .card-header {
            background-color: #fff;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 1.25rem 1.5rem;
            font-weight: 600;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Table Styling */
        .table {
            margin-bottom: 0;
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table thead th {
            background-color: rgba(67, 97, 238, 0.05);
            color: var(--dark-color);
            font-weight: 600;
            border-bottom: 2px solid rgba(0, 0, 0, 0.05);
            white-space: nowrap;
        }

        .table tbody tr:hover {
            background-color: rgba(67, 97, 238, 0.03);
        }

        /* Form Controls */
        .form-control, .form-select {
            border-radius: 0.5rem;
            padding: 0.65rem 1rem;
            border: 1px solid rgba(0, 0, 0, 0.1);
            transition: all 0.2s;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25);
        }

        /* Buttons */
        .btn {
            border-radius: 0.5rem;
            padding: 0.5rem 1rem;
            font-weight: 500;
            transition: all 0.2s;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
            transform: translateY(-2px);
        }

        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            color: #fff;
            transform: translateY(-2px);
        }

        /* Notification badge */
        .badge-notification {
            background-color: var(--danger-color);
            color: white;
            border-radius: 50%;
            padding: 0.35rem 0.65rem;
            font-size: 0.75rem;
            position: absolute;
            top: -5px;
            right: -5px;
        }

        /* Page Title */
        .page-title {
            font-size: 1.75rem;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 1.5rem;
        }

        /* No data state */
        .no-data {
            text-align: center;
            padding: 3rem;
            color: var(--text-muted);
        }

        .no-data i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <nav class="nav flex-column">
            @if(Auth::user()->role === \App\Models\User::ROLE_ADMIN)
                <!-- Admin-only links -->
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home sidebar-menu-logo"></i>
                    <span class="sidebar-link-text">Tableau de bord Admin</span>
                </a>
                <a href="{{ route('admin.complaints.index') }}" class="nav-link {{ request()->routeIs('admin.complaints.*') ? 'active' : '' }}">
                    <i class="fas fa-exclamation-triangle sidebar-menu-logo"></i>
                    <span class="sidebar-link-text">Toutes les réclamations</span>
                </a>
                <a href="{{ route('admin.service-providers.index') }}" class="nav-link {{ request()->routeIs('admin.service-providers.*') ? 'active' : '' }}">
                    <i class="fas fa-truck sidebar-menu-logo"></i>
                    <span class="sidebar-link-text">Prestataires</span>
                </a>
                <a href="{{ route('admin.evaluations.index') }}" class="nav-link {{ request()->routeIs('admin.evaluations.*') ? 'active' : '' }}">
                    <i class="fas fa-star sidebar-menu-logo"></i>
                    <span class="sidebar-link-text">Évaluations</span>
                </a>
                <a href="{{ route('admin.kpis.index') }}" class="nav-link {{ request()->routeIs('admin.kpis.*') ? 'active' : '' }}">
                    <i class="fas fa-chart-line sidebar-menu-logo"></i>
                    <span class="sidebar-link-text">KPIs</span>
                </a>
                <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="fas fa-users sidebar-menu-logo"></i>
                    <span class="sidebar-link-text">Gestion des Utilisateurs</span>
                </a>
            @else
                <!-- Regular user links -->
                <a href="{{ route('user.dashboard') }}" class="nav-link {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home sidebar-menu-logo"></i>
                    <span class="sidebar-link-text">Tableau de bord</span>
                </a>
                <a href="{{ route('user.complaints.index') }}" class="nav-link {{ request()->routeIs('user.complaints.*') ? 'active' : '' }}">
                    <i class="fas fa-exclamation-triangle sidebar-menu-logo"></i>
                    <span class="sidebar-link-text">Mes réclamations</span>
                </a>
            @endif
            <a href="{{ route('logout') }}" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt sidebar-menu-logo"></i>
                <span class="sidebar-link-text">Déconnexion</span>
            </a>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="page-title mb-0">@yield('page_title', 'Tableau de bord')</h1>
            <div class="d-flex align-items-center gap-3">
                @if(Auth::check())
                    <div class="notification-bell-wrapper position-relative" style="cursor: pointer;">
                        <i class="fas fa-bell text-secondary fs-5" id="notification-bell"></i>
                        <span id="notification-badge" class="badge-notification" style="display: none;"></span>
                        <div id="notification-dropdown" class="card shadow position-absolute end-0 mt-2" style="display: none; width: 320px; z-index: 1000;">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                                <span class="fw-bold">Notifications récentes</span>
                                <button type="button" class="btn-close" onclick="document.getElementById('notification-dropdown').style.display='none';"></button>
                            </div>
                            <ul id="notification-list" class="list-group list-group-flush" style="max-height: 350px; overflow-y: auto;">
                                <li class="list-group-item text-center text-muted py-3">Aucune notification récente</li>
                            </ul>
                        </div>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle d-flex align-items-center" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=4361ee&color=fff" class="rounded-circle me-2" width="32" height="32">
                            <span>{{ Auth::user()->name }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li>
                                <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form-header').submit();">
                                    <i class="fas fa-sign-out-alt me-2"></i>Déconnexion
                                </a>
                                <form id="logout-form-header" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </div>
                    <img src="{{ asset('images/logo.jpg') }}" alt="Tuniship Logo" style="height: 40px; width: auto; border-radius: 8px; box-shadow: var(--box-shadow);">
                @endif
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            // Close alert messages after 5 seconds
            setTimeout(function() {
                document.querySelectorAll('.alert').forEach(function(alert) {
                    var bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);

            // Notification system
            const notificationBell = document.getElementById('notification-bell');
            const notificationBadge = document.getElementById('notification-badge');
            const notificationDropdown = document.getElementById('notification-dropdown');
            const notificationList = document.getElementById('notification-list');

            if (notificationBell && notificationBadge && notificationDropdown) {
                // Toggle dropdown when clicking the bell
                notificationBell.addEventListener('click', function() {
                    notificationDropdown.style.display = notificationDropdown.style.display === 'block' ? 'none' : 'block';

                    // Fetch notifications when opening dropdown
                    if (notificationDropdown.style.display === 'block') {
                        fetchNotifications();
                    }
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!notificationBell.contains(e.target) && !notificationDropdown.contains(e.target)) {
                        notificationDropdown.style.display = 'none';
                    }
                });

                // Function to fetch unread count
                function fetchUnreadCount() {
                    // This would normally be an Ajax call to your backend
                    fetch('/api/notifications/unread-count')
                        .then(response => response.json())
                        .catch(() => ({ count: 0 }))
                        .then(data => {
                            if (data.count > 0) {
                                notificationBadge.textContent = data.count > 9 ? '9+' : data.count;
                                notificationBadge.style.display = 'flex';
                            } else {
                                notificationBadge.style.display = 'none';
                            }
                        });
                }

                // Function to fetch notifications
                function fetchNotifications() {
                    // This would normally be an Ajax call to your backend
                    // For now, just show a placeholder message
                    notificationList.innerHTML = '<li class="list-group-item text-center text-muted py-3">Chargement...</li>';

                    // Simulate loading delay
                    setTimeout(() => {
                        notificationList.innerHTML = '<li class="list-group-item text-center text-muted py-3">Aucune notification récente</li>';
                    }, 500);
                }

                // Initial unread count fetch
                fetchUnreadCount();

                // Periodically check for new notifications (every 60 seconds)
                setInterval(fetchUnreadCount, 60000);
            }
        });
    </script>
    @yield('scripts')
</body>
</html>
