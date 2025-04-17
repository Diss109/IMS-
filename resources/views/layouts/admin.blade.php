<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - IMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
    --sidebar-width: 250px;
    --sidebar-collapsed-width: 80px;
}

        body {
            overflow-x: hidden;
        }

        .sidebar {
            position: fixed;
            display: flex;
            flex-direction: column;
            align-items: stretch;
        
            top: 0;
            bottom: 0;
            left: 0;
            width: var(--sidebar-width);
            padding: 20px 0;
            background-color: #2c3e50;
            transition: all 0.3s ease;
            z-index: 100;
            transform: translateX(calc(-1 * (var(--sidebar-width) - var(--sidebar-collapsed-width))));
        }

        .sidebar:hover {
            transform: translateX(0);
        }

        .sidebar .nav-link {
    padding: 12px 10px;
    color: #ecf0f1;
    text-decoration: none;
    white-space: nowrap;
    /* overflow: hidden; */
}

        .sidebar .nav-link:hover {
            background-color: #34495e;
            color: #fff;
        }

        .sidebar .nav-link.active {
            background-color: #3498db;
            color: #fff;
        }

        .sidebar .nav-link i {
            width: 20px;
            margin-right: 10px;
            text-align: center;
        }

        .main-content {
            margin-left: var(--sidebar-collapsed-width);
            padding: 20px;
            transition: all 0.3s ease;
            min-height: 100vh;
            width: calc(100% - var(--sidebar-collapsed-width));
        }
        .sidebar:hover ~ .main-content {
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
        }

        .sidebar-title {
            display: none;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-250px);
            }
            .sidebar:hover {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
                width: 100%;
            }
        }

        /* Fix table responsiveness */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* Ensure cards don't overflow */
        .card {
            overflow: hidden;
        }
        .sidebar-logo, .sidebar-menu-logo {
    width: 24px !important;
    height: 24px !important;
    margin-right: 10px;
    object-fit: contain;
    display: inline-block !important;
    vertical-align: middle;
    transition: all 0.3s;
    border: none;
}
/* When sidebar is collapsed, center icon and remove margin */
.sidebar:not(:hover) .sidebar-logo,
.sidebar:not(:hover) .sidebar-menu-logo {
    margin-right: 0 !important;
    display: block !important;
    margin-left: auto;
    margin-right: auto;
}
.sidebar:not(:hover) .sidebar-link-text {
    opacity: 0;
    width: 0;
    overflow: hidden;
    display: none;
}
        .sidebar-title-text {
            display: inline-block;
            transition: opacity 0.3s, width 0.3s;
        }
        /* Hide text when sidebar is collapsed (not hovered) */
        .sidebar:not(:hover) .sidebar-title-text {
            opacity: 0;
            width: 0;
            overflow: hidden;
        }

        .sidebar:hover .sidebar-title-text {
            opacity: 1;
            width: auto;
        }
        .sidebar-link-text {
            display: inline;
            transition: opacity 0.3s, width 0.3s;
        }
        .sidebar:not(:hover) .sidebar-link-text {
            opacity: 0;
            width: 0;
            overflow: hidden;
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="sidebar">
                
                <nav class="nav flex-column mt-3">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
    <img src="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/icons/speedometer2.svg" class="sidebar-menu-logo" alt="Dashboard" style="filter: invert(1);">
    <span class="sidebar-link-text">Tableau de bord</span>
</a>
<a href="{{ route('admin.complaints.index') }}" class="nav-link {{ request()->routeIs('admin.complaints.*') ? 'active' : '' }}">
    <img src="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/icons/exclamation-triangle.svg" class="sidebar-menu-logo" alt="Réclamations" style="filter: invert(1);">
    <span class="sidebar-link-text">Réclamations</span>
</a>
<a href="{{ route('admin.service-providers.index') }}" class="nav-link {{ request()->routeIs('admin.service-providers.*') ? 'active' : '' }}">
    <img src="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/icons/truck.svg" class="sidebar-menu-logo" alt="Prestataires" style="filter: invert(1);">
    <span class="sidebar-link-text">Prestataires</span>
</a>
<a href="{{ route('admin.evaluations.index') }}" class="nav-link {{ request()->routeIs('admin.evaluations.*') ? 'active' : '' }}">
    <img src="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/icons/star.svg" class="sidebar-menu-logo" alt="Évaluations" style="filter: invert(1);">
    <span class="sidebar-link-text">Évaluations</span>
</a>
<a href="{{ route('admin.kpis.index') }}" class="nav-link {{ request()->routeIs('admin.kpis.*') ? 'active' : '' }}">
    <img src="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/icons/graph-up.svg" class="sidebar-menu-logo" alt="KPIs" style="filter: invert(1);">
    <span class="sidebar-link-text">KPIs</span>
</a>
<a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
    <img src="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/icons/people.svg" class="sidebar-menu-logo" alt="Gestion des Utilisateurs" style="filter: invert(1);">
    <span class="sidebar-link-text">Gestion des Utilisateurs</span>
</a>
<a href="{{ route('logout') }}" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
    <img src="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/icons/box-arrow-right.svg" class="sidebar-menu-logo" alt="Déconnexion" style="filter: invert(1);">
    <span class="sidebar-link-text">Déconnexion</span>
</a>
                </nav>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>

            <!-- Main Content -->
            <div class="main-content">
                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
