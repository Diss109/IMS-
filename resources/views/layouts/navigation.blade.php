<div style="background: yellow; color: black; padding: 5px; text-align: center;">
    NAV DEBUG: This is the user navigation bar.
</div>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('dashboard') }}">
            {{ config('app.name', 'Tuniship IMS') }}
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('user.dashboard') ? 'active' : '' }}"
                       href="{{ route('user.dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i> Tableau de bord
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('user.complaints.*') ? 'active' : '' }}"
                       href="{{ route('user.complaints.index') }}">
                        <i class="fas fa-exclamation-circle"></i> Mes réclamations
                    </a>
                </li>
                <li class="nav-item dropdown position-relative">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('user.notifications.*') ? 'active' : '' }}" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-bell"></i>
                        @if(isset($unreadNotificationsCount) && $unreadNotificationsCount > 0)
                            <span class="badge bg-danger position-absolute top-0 start-100 translate-middle">{{ $unreadNotificationsCount }}</span>
                        @endif
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationDropdown" style="width: 350px;">
                        <li class="dropdown-header text-center">
                            <a href="{{ route('user.notifications.index') }}" class="text-decoration-none">Voir toutes les notifications</a>
                            @if(isset($unreadNotificationsCount) && $unreadNotificationsCount > 0)
                                <form method="POST" action="{{ route('user.notifications.markAllRead') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-link btn-sm">Tout marquer comme lu</button>
                                </form>
                            @endif
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li class="px-3 py-2 text-muted">
                            <small>DEBUG: unreadNotificationsCount = {{ isset($unreadNotificationsCount) ? $unreadNotificationsCount : 'null' }}</small><br>
                            <small>DEBUG: latestNotifications = {{ isset($latestNotifications) ? json_encode($latestNotifications) : 'null' }}</small>
                        </li>
                        @forelse($latestNotifications as $notification)
                            <li class="px-3 py-2 @if(!$notification->is_read) fw-bold bg-warning-subtle @endif">
                                {{ $notification->message }}<br>
                                <small class="text-muted">{{ $notification->created_at->format('d/m/Y H:i') }}</small>
                                @if($notification->related_id && $notification->type === 'complaint_assignment')
                                    <a href="{{ route('user.complaints.show', $notification->related_id) }}" class="btn btn-sm btn-primary ms-2">Voir la réclamation</a>
                                @endif
                            </li>
                        @empty
                            <li class="px-3 py-2 text-muted">Aucune notification récente.</li>
                        @endforelse
                    </ul>
                </li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                       data-bs-toggle="dropdown" aria-expanded="false">
                        {{ Auth::user()->name }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
