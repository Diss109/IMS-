<!DOCTYPE html>
<html lang="fr">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - IMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/animate.css@4.1.1/animate.min.css" rel="stylesheet">
    <!-- Add jQuery if not already included -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Set up AJAX defaults for all jQuery AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
        });
    </script>
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
            --card-border-radius: 1rem;
            --box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.08);
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
            font-family: 'Poppins', 'Segoe UI', Arial, sans-serif;
            color: var(--text-color);
            background-color: #f8faff;
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
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--dark-color) 0%, #203a65 100%);
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
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
            border-radius: 0 15px 15px 0;
        }

        .sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
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

        /* Position the nav to fill available space */
        .sidebar nav {
            width: 100%;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        /* Main menu items container */
        .sidebar-menu {
            flex: 1;
            overflow-y: auto;
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
            margin: 4px 8px;
            border-radius: 10px;
        }

        .sidebar .nav-link:hover {
            background-color: var(--sidebar-hover-bg);
            border-left-color: var(--sidebar-hover-border);
            color: white;
            box-shadow: inset 0 0 10px rgba(255, 255, 255, 0.05);
            transform: translateX(3px);
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
            opacity: 1;
            transition: opacity var(--transition-speed) ease, transform 0.2s ease;
            white-space: normal;
            word-wrap: break-word;
            max-width: calc(var(--sidebar-width) - 80px);
        }

        .sidebar.collapsed .sidebar-link-text {
            opacity: 0;
        }

        .sidebar .nav-link:hover .sidebar-link-text {
            transform: translateX(3px);
        }

        .sidebar-toggle {
            position: fixed;
            bottom: 20px;
            left: 20px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
            z-index: 1000;
            font-size: 18px;
        }

        .sidebar-toggle:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
        }

        /* Main Content Area */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 1.5rem;
            transition: all var(--transition-speed) ease;
            min-height: 100vh;
            width: calc(100% - var(--sidebar-width));
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            will-change: margin-left, width;
            transform: translateZ(0);
            backface-visibility: hidden;
            perspective: 1000px;
        }

        .sidebar.collapsed ~ .main-content {
            margin-left: var(--sidebar-collapsed-width);
            width: calc(100% - var(--sidebar-collapsed-width));
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 0;
            }

            .sidebar.collapsed {
                width: 0;
            }

            .sidebar:not(.collapsed) {
                width: var(--sidebar-width);
            }

            .main-content {
                margin-left: 0;
                width: 100%;
            }

            .sidebar:not(.collapsed) ~ .main-content {
                margin-left: var(--sidebar-width);
                width: calc(100% - var(--sidebar-width));
            }
        }

        /* Cards Styling */
        .card {
            border: none;
            border-radius: var(--card-border-radius);
            box-shadow: var(--box-shadow);
            transition: transform 0.3s, box-shadow 0.3s;
            margin-bottom: 1.5rem;
            overflow: hidden;
            position: relative;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.75rem 2rem rgba(0, 0, 0, 0.15);
        }

        .card-header {
            background-color: #fff;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 1.25rem 1.5rem;
            font-weight: 600;
            font-size: 1.1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Table Styling */
        .table {
            margin-bottom: 0;
            border-collapse: separate;
            border-spacing: 0 8px;
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            padding: 0.5rem;
        }

        .table thead th {
            background-color: rgba(67, 97, 238, 0.05);
            color: var(--dark-color);
            font-weight: 600;
            border-bottom: 2px solid rgba(0, 0, 0, 0.05);
            white-space: nowrap;
            padding: 1rem;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table tbody tr {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            border-radius: 10px;
            background-color: white;
            margin-bottom: 10px;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .table tbody tr:hover {
            background-color: rgba(67, 97, 238, 0.03);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }

        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
        }

        .table tbody td:first-child {
            border-top-left-radius: 10px;
            border-bottom-left-radius: 10px;
        }

        .table tbody td:last-child {
            border-top-right-radius: 10px;
            border-bottom-right-radius: 10px;
        }

        /* Form Controls */
        .form-control, .form-select {
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
            border: 1px solid rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
            font-size: 0.95rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.02);
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.15);
            transform: translateY(-1px);
        }

        /* Buttons */
        .btn {
            border-radius: 0.75rem;
            padding: 0.5rem 1.25rem;
            font-weight: 500;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
            font-size: 0.95rem;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
        }

        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            color: #fff;
        }

        /* Notification badge */
        .badge-notification {
            display: flex;
            justify-content: center;
            align-items: center;
            position: absolute;
            top: -5px;
            right: -5px;
            width: 20px;
            height: 20px;
            font-size: 0.65rem;
            font-weight: bold;
            border-radius: 50%;
            background-color: var(--danger-color);
            color: white;
            box-shadow: 0 0 0 2px white;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(231, 76, 60, 0.7);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(231, 76, 60, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(231, 76, 60, 0);
            }
        }

        /* Cursor pointer for interactive elements */
        .cursor-pointer {
            cursor: pointer !important;
        }

        /* Make notification bell stand out more */
        #notification-bell i {
            transition: color 0.2s ease, transform 0.2s;
        }

        #notification-bell:hover i {
            color: var(--primary-color) !important;
            transform: scale(1.2);
        }

        /* No data state */
        .no-data {
            text-align: center;
            padding: 3rem;
            color: var(--text-muted);
            background: linear-gradient(145deg, #f8f9fa, #ffffff);
            border-radius: 15px;
            box-shadow: inset 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .no-data i {
            font-size: 3.5rem;
            margin-bottom: 1.5rem;
            opacity: 0.5;
            color: var(--primary-color);
        }

        /* Badges */
        .badge {
            padding: 0.4em 0.7em;
            font-weight: 500;
            letter-spacing: 0.3px;
            border-radius: 6px;
        }

        /* Animations */
        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Page title */
        .page-title {
            font-weight: 600;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            position: relative;
        }

        /* Card with gradient borders */
        .border-gradient {
            position: relative;
            border-radius: var(--card-border-radius);
            background: white;
            z-index: 1;
        }

        .border-gradient::before {
            content: "";
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            z-index: -1;
            border-radius: calc(var(--card-border-radius) + 2px);
            opacity: 0;
            transition: opacity 0.3s;
        }

        .border-gradient:hover::before {
            opacity: 1;
        }

        /* Custom Scrollbar for dropdowns */
        .dropdown-menu {
            border: none;
            border-radius: 10px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.15);
            max-height: 350px;
            overflow-y: auto;
        }

        .dropdown-menu::-webkit-scrollbar {
            width: 5px;
        }

        .dropdown-menu::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .dropdown-menu::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
        }

        .dropdown-menu::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        /* Tooltip enhancements */
        .tooltip {
            position: relative;
            display: inline-block;
        }

        .tooltip .tooltiptext {
            visibility: hidden;
            width: auto;
            min-width: 120px;
            background-color: rgba(0, 0, 0, 0.8);
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 5px 10px;
            position: absolute;
            z-index: 1;
            bottom: 125%;
            left: 50%;
            transform: translateX(-50%);
            opacity: 0;
            transition: opacity 0.3s;
            font-size: 0.85rem;
            white-space: nowrap;
        }

        .tooltip:hover .tooltiptext {
            visibility: visible;
            opacity: 1;
        }

        /* Bottom fixed position for logout */
        .sidebar-footer {
            margin-top: auto;
            padding-bottom: 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 0.75rem;
            margin-top: 2rem;
        }

        /* Special styling for the logout button */
        .logout-button {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            color: white;
            background: linear-gradient(90deg, rgba(255, 255, 255, 0.1), transparent);
            border-radius: 10px;
            margin: 4px 8px;
            transition: all 0.3s ease;
            position: relative;
        }

        .logout-button:hover {
            background-color: rgba(220, 53, 69, 0.2);
            transform: translateX(3px);
        }

        .logout-button .sidebar-menu-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 40px;
            font-size: 18px;
        }

        .logout-button .sidebar-link-text {
            font-weight: 500;
        }

        /* Subtle animation for logout icon */
        .logout-button:hover .sidebar-menu-logo {
            animation: logoutPulse 1s infinite;
        }

        @keyframes logoutPulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }
    </style>
    @yield('styles')
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <nav class="nav flex-column">
            <!-- Main menu items -->
            <div class="sidebar-menu">
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
                    <a href="{{ route('admin.predictions.index') }}" class="nav-link {{ request()->routeIs('admin.predictions.*') ? 'active' : '' }}">
                        <i class="fas fa-chart-bar sidebar-menu-logo"></i>
                        <span class="sidebar-link-text">Prédictions</span>
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
                    <a href="{{ route('user.messages.index') }}" class="nav-link {{ request()->routeIs('user.messages.*') ? 'active' : '' }}">
                        <i class="fas fa-comments sidebar-menu-logo"></i>
                        <span class="sidebar-link-text">Messages</span>
                        <span id="unread-messages-count" class="badge bg-danger rounded-pill ms-2" style="display: none;">0</span>
                    </a>
                @endif
            </div>

            <!-- Footer with logout -->
            <div class="sidebar-footer">
                <a href="{{ route('logout') }}" class="logout-button" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt sidebar-menu-logo"></i>
                    <span class="sidebar-link-text">Déconnexion</span>
                </a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4 px-4 pt-4">
            <div class="d-flex align-items-center">
                <img src="{{ asset('images/logo.jpg') }}" alt="Tuniship Logo" style="height: 40px; width: auto; border-radius: 8px; box-shadow: var(--box-shadow); margin-right: 15px;">
                <h1 class="page-title mb-0">@yield('page_title', 'Tableau de bord')</h1>
            </div>
            <div class="d-flex align-items-center gap-3">
                @if(Auth::check())
                    <!-- Notification Bell with Dropdown -->
                    <div class="dropdown">
                        <div id="notification-bell" class="d-flex align-items-center position-relative" style="cursor: pointer;">
                            <i class="fas fa-bell text-secondary fs-5"></i>
                            <span id="notification-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display: none;">0</span>
                        </div>
                        <!-- Notification Dropdown -->
                        <div id="notification-dropdown" class="dropdown-menu dropdown-menu-end shadow" style="display: none; width: 350px; max-height: 400px; overflow-y: auto; z-index: 1050;">
                            <h6 class="dropdown-header bg-primary text-white py-2">Notifications</h6>
                            <ul id="notification-list" class="list-group list-group-flush">
                                <li class="list-group-item text-center text-muted py-3">Chargement...</li>
                            </ul>
                            <div class="dropdown-divider"></div>
                            <div class="d-flex justify-content-between p-2">
                                <button id="mark-all-read" class="btn btn-sm btn-outline-primary">Tout marquer comme lu</button>
                                <button id="delete-all" class="btn btn-sm btn-outline-danger">Tout supprimer</button>
                            </div>
                        </div>
                    </div>
                    <!-- User display (without dropdown) -->
                    <div class="d-flex align-items-center">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=4361ee&color=fff" class="rounded-circle me-2" width="32" height="32">
                        <span class="text-dark">{{ Auth::user()->name }}</span>
                    </div>
                @endif
            </div>
        </div>
        <div class="px-4 pb-4">
            @if (session('success'))
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
    @section('scripts')
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

        // Make sure the sidebar is expanded by default
        const sidebar = document.querySelector('.sidebar');
        if (sidebar) {
            // Remove collapsed class to ensure sidebar is expanded
            sidebar.classList.remove('collapsed');

            // Make sure sidebar-link-text elements are visible
            document.querySelectorAll('.sidebar-link-text').forEach(text => {
                text.style.opacity = '1';
            });
        }

        // Unread messages counter (only for regular users)
        @if(Auth::check() && Auth::user()->role !== \App\Models\User::ROLE_ADMIN)
        var unreadMessagesCountUrl = '{{ route("user.messages.unreadCount") }}';

        function fetchUnreadMessagesCount() {
            fetch(unreadMessagesCountUrl)
                .then(response => response.json())
                .catch(() => ({ count: 0 }))
                .then(data => {
                    const unreadMessagesCount = document.getElementById('unread-messages-count');
                    if (unreadMessagesCount) {
                        if (data.count > 0) {
                            unreadMessagesCount.textContent = data.count;
                            unreadMessagesCount.style.display = 'inline';
                        } else {
                            unreadMessagesCount.style.display = 'none';
                        }
                    }
                });
        }

        // Check for unread messages every 30 seconds
        fetchUnreadMessagesCount();
        setInterval(fetchUnreadMessagesCount, 30000);
        @endif

        // Notification system
        const notificationBell = document.getElementById('notification-bell');
        const notificationBadge = document.getElementById('notification-badge');
        const notificationDropdown = document.getElementById('notification-dropdown');
        const notificationList = document.getElementById('notification-list');

        // Add cursor-pointer class to elements
        if (notificationBell) {
            notificationBell.classList.add('cursor-pointer');
        }

        if (notificationBell && notificationBadge && notificationDropdown) {
            // Add click event to notification bell
            notificationBell.addEventListener('click', function(e) {
                e.stopPropagation(); // Prevent event bubbling

                // Handle dropdown visibility
                if (notificationDropdown.style.display === 'block') {
                    notificationDropdown.style.display = 'none';
                } else {
                    notificationDropdown.style.display = 'block';

                    // Ensure proper positioning
                    const bellRect = notificationBell.getBoundingClientRect();
                    notificationDropdown.style.position = 'absolute';
                    notificationDropdown.style.top = (bellRect.height + 5) + 'px';
                    notificationDropdown.style.right = '0';

                    // Fetch notifications when opening dropdown
                    fetchNotifications();
                }
            });

            // Nettoyer et mettre à jour la liste des notifications
            function clearNotificationList() {
                notificationList.innerHTML = '';
            }

            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (notificationDropdown.style.display === 'block' &&
                    !notificationBell.contains(e.target) &&
                    !notificationDropdown.contains(e.target)) {
                    notificationDropdown.style.display = 'none';
                }
            });

            // Function to fetch unread count
            function fetchUnreadCount() {
                // Get unread count from the correct endpoint
                fetch('/admin/notifications/unread-count')
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
                // Show loading state
                notificationList.innerHTML = '<li class="list-group-item text-center text-muted py-3">Chargement...</li>';

                // Fetch notifications
                fetch('/admin/notifications/latest')
                    .then(response => response.json())
                    .then(data => {
                        // Vider la liste de notifications
                        notificationList.innerHTML = '';

                        if (data.notifications && data.notifications.length > 0) {
                            // Solution ultra-simple: utiliser innerHTML avec éléments HTML directs
                            data.notifications.forEach(function(notification) {
                                // Définir les variables
                                const bgColor = notification.is_read ? '#ffffff' : '#f0f0f0';
                                const date = new Date(notification.created_at).toLocaleString('fr-FR');
                                let complaintId = 0;

                                // Essayer d'extraire l'ID de la notification
                                try {
                                    if (notification.related_id && !isNaN(parseInt(notification.related_id))) {
                                        complaintId = parseInt(notification.related_id);
                                    } else if (notification.message) {
                                        // Essayer d'extraire l'ID du message s'il est au format "Réclamation #123"
                                        const matches = notification.message.match(/#(\d+)/);
                                        if (matches && matches[1]) {
                                            complaintId = parseInt(matches[1]);
                                        }
                                    }
                                } catch(e) {
                                    console.error('Erreur lors de l\'extraction de l\'ID:', e);
                                }

                                // Ajouter la notification comme un élément de liste avec boutons
                                const listItem = document.createElement('li');
                                listItem.className = 'list-group-item';
                                listItem.style.backgroundColor = bgColor;
                                listItem.style.position = 'relative';
                                listItem.style.padding = '10px 30px 10px 15px';

                                listItem.innerHTML = `
                                    <div>
                                        <p class="mb-1">${notification.message}</p>
                                        <small class="text-muted">${date}</small>
                                    </div>
                                    <div class="mt-2">
                                        ${complaintId ? `<a href="/admin/complaints/${complaintId}" class="btn btn-sm btn-primary">Voir la réclamation</a>` : ''}
                                    </div>
                                    <button class="btn-close position-absolute" style="top:10px;right:10px;" onclick="deleteNotification(${notification.id})"></button>
                                `;

                                notificationList.appendChild(listItem);
                            });
                        } else {
                            notificationList.innerHTML = '<li class="list-group-item text-center text-muted py-3">Aucune notification récente</li>';
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching notifications:', error);
                        notificationList.innerHTML = '<li class="list-group-item text-center text-muted py-3">Erreur lors du chargement des notifications</li>';
                    });
            }

            // Function to mark notification as read
            window.markNotificationAsRead = function(id, updateUI = true) {
                return fetch('/admin/notifications/mark-read', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ id: id })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success && updateUI) {
                        fetchNotifications();
                        fetchUnreadCount();
                    }
                    return data;
                });
            };

            // Function to delete notification
            window.deleteNotification = function(id) {
                fetch('/admin/notifications/delete', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ id: id })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        fetchNotifications();
                        fetchUnreadCount();
                    }
                });
            };

            // Mark all notifications as read
            document.getElementById('mark-all-read').addEventListener('click', function() {
                fetch('/admin/notifications/mark-all-read', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        fetchNotifications();
                        fetchUnreadCount();
                    }
                });
            });

            // Delete all notifications
            document.getElementById('delete-all').addEventListener('click', function() {
                fetch('/admin/notifications/delete-all', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        fetchNotifications();
                        fetchUnreadCount();
                    }
                });
            });

            // Initial unread count fetch
            fetchUnreadCount();

            // Periodically check for new notifications (every 60 seconds)
            setInterval(fetchUnreadCount, 60000);
        }
    });
    </script>
    @stack('scripts')
    <!-- Custom sidebar script -->
    <script src="{{ asset('js/custom/sidebar.js') }}"></script>
</body>
</html>
