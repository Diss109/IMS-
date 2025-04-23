<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - IMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .dashboard-title, .dashboard-username {
            padding-left: 15px;
            font-family: 'Segoe UI', Arial, Helvetica, sans-serif;
            font-size: 2rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0;
        }
        .dashboard-user-logo img {
            width: 80px;
            height: 80px;
            object-fit: contain;
            margin-right: 10px;
        }
        .dashboard-username {
            display: inline-block;
            vertical-align: middle;
        }
        .dashboard-username-small {
            font-size: 23px;
            color: #111;
            font-weight: 500;
            margin-right: 8px;
        }
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
    <div class="d-flex justify-content-between align-items-center mb-3" style="gap:16px;">
        <div>
            <h1 class="dashboard-title mb-0" style="font-size:2rem;padding-left: 15px;">
                @yield('page_title', 'Tableau de bord')
            </h1>
        </div>
        <div class="d-flex align-items-center" style="gap:16px;">
            <span class="dashboard-username dashboard-username-small">{{ Auth::user()->name }}</span>
            <div class="notification-bell-wrapper" style="position:relative;display:inline-block;">
                <i class="fas fa-bell" id="notification-bell" tabindex="0" style="font-size:26px;color:#555;cursor:pointer;pointer-events:auto;"></i>
                <span id="notification-badge" style="position:absolute;top:-7px;right:-7px;background:#dc3545;color:#fff;border-radius:50%;padding:2px 7px;font-size:12px;min-width:18px;text-align:center;{{ (($unreadNotificationsCount ?? 0) > 0) ? '' : 'display:none;' }}">
                    {{ $unreadNotificationsCount ?? 0 }}
                </span>
                <div id="notification-dropdown" class="card shadow" style="display:none;position:absolute;right:0;top:36px;min-width:340px;z-index:9999;">
                    <div class="card-header py-2 px-3 d-flex justify-content-between align-items-center">
                        <span class="fw-bold">Notifications récentes</span>
                        <button class="btn-close btn-sm" onclick="document.getElementById('notification-dropdown').style.display='none';event.stopPropagation();"></button>
                    </div>
                    <ul id="notification-list" class="list-group list-group-flush" style="max-height:340px;overflow-y:auto;">
                        <li class="list-group-item text-center text-muted">Chargement...</li>
                    </ul>
                </div>
            </div>
            <img src="{{ asset('images/logo.jpg') }}" alt="Tuniship Logo" style="height:56px;">
        </div>
    </div>
    @yield('content')
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function updateNotificationBadge(count) {
    var badge = document.getElementById('notification-badge');
    if (!badge) return;
    if (count > 0) {
        badge.style.display = '';
        badge.textContent = count;
    } else {
        badge.style.display = 'none';
    }
}

function fetchUnreadNotifications() {
    fetch('/admin/notifications/unread-count')
        .then(response => response.json())
        .then(data => {
            updateNotificationBadge(data.count);
        });
}

// Poll every 10 seconds
setInterval(fetchUnreadNotifications, 10000);
// Also fetch immediately on page load
fetchUnreadNotifications();
</script>
<script>
// Dropdown handling for notifications
const bell = document.getElementById('notification-bell');
const dropdown = document.getElementById('notification-dropdown');
const notifList = document.getElementById('notification-list');

if (bell && dropdown && notifList) {
    bell.addEventListener('click', function(e) {
        e.stopPropagation();
        if (dropdown.style.display === 'block') {
            dropdown.style.display = 'none';
        } else {
            dropdown.style.display = 'block';
            notifList.innerHTML = '<li class="list-group-item text-center text-muted">Chargement...</li>';
            fetch('/admin/notifications/latest')
                .then(resp => resp.json())
                .then(data => {
                    if (!data.notifications || data.notifications.length === 0) {
                        notifList.innerHTML = '<li class="list-group-item text-center text-muted">Aucune notification récente.</li>';
                        return;
                    }
                    notifList.innerHTML = '';
                    data.notifications.forEach(function(n) {
                        let li = document.createElement('li');
                        li.className = 'list-group-item d-flex justify-content-between align-items-center' + (n.is_read ? '' : ' fw-bold bg-light');
                        li.style.cursor = 'pointer';
                        li.innerHTML = `<span>${n.message}</span><small class="text-muted ms-2">${(new Date(n.created_at)).toLocaleString('fr-FR')}</small>`;
                        li.onclick = function(ev) {
                            ev.stopPropagation();
                            fetch('/admin/notifications/mark-read', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                },
                                body: JSON.stringify({id: n.id})
                            }).then(() => {
                                li.classList.remove('fw-bold','bg-light');
                                // Optionally update badge
                                fetchUnreadNotifications();
                                if (n.related_id) {
                                    window.location.href = '/admin/complaints/' + n.related_id;
                                }
                            });
                        };
                        notifList.appendChild(li);
                    });
                });
        }
    });
    // Hide dropdown when clicking outside
    document.addEventListener('click', function(e) {
        dropdown.style.display = 'none';
    });
    dropdown.addEventListener('click', function(e) {
        e.stopPropagation();
    });
}
</script>
</body>
</html>
