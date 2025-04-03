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
            --sidebar-collapsed-width: 60px;
        }

        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: calc(-1 * (var(--sidebar-width) - var(--sidebar-collapsed-width)));
            width: var(--sidebar-width);
            padding: 20px 0;
            background-color: #2c3e50;
            transition: all 0.3s ease;
            z-index: 100;
        }

        .sidebar:hover {
            left: 0;
        }

        .sidebar .nav-link {
            padding: 12px 20px;
            color: #ecf0f1;
            text-decoration: none;
            white-space: nowrap;
            overflow: hidden;
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
        }

        .sidebar-title {
            color: white;
            text-align: center;
            padding: 20px;
            font-size: 1.5rem;
            font-weight: bold;
            white-space: nowrap;
            overflow: hidden;
            border-bottom: 1px solid #34495e;
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .sidebar {
                left: -250px;
            }
            .sidebar:hover {
                left: 0;
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="sidebar">
                <div class="sidebar-title">
                    IMS Admin
                </div>
                <nav class="nav flex-column">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt"></i> Tableau de bord
                    </a>
                    <a href="{{ route('admin.complaints.index') }}" class="nav-link {{ request()->routeIs('admin.complaints.*') ? 'active' : '' }}">
                        <i class="fas fa-exclamation-circle"></i> Réclamations
                    </a>
                    <a href="{{ route('admin.service-providers.index') }}" class="nav-link {{ request()->routeIs('admin.service-providers.*') ? 'active' : '' }}">
                        <i class="fas fa-truck"></i> Prestataires
                    </a>
                    <a href="{{ route('admin.transporters.index') }}" class="nav-link {{ request()->routeIs('admin.transporters.*') ? 'active' : '' }}">
                        <i class="fas fa-shipping-fast"></i> Transporteurs
                    </a>
                    <a href="{{ route('admin.evaluations.index') }}" class="nav-link {{ request()->routeIs('admin.evaluations.*') ? 'active' : '' }}">
                        <i class="fas fa-star"></i> Évaluations
                    </a>
                    <a href="{{ route('admin.kpis.index') }}" class="nav-link {{ request()->routeIs('admin.kpis.*') ? 'active' : '' }}">
                        <i class="fas fa-chart-line"></i> KPIs
                    </a>
                    <a href="{{ route('logout') }}" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i> Déconnexion
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
