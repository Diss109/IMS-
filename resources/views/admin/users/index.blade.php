@extends('layouts.admin')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <h1 class="dashboard-title">Gestion des Utilisateurs</h1>
    <div class="d-flex align-items-center gap-2 dashboard-user-logo">
        <span class="dashboard-username dashboard-username-small">{{ Auth::user()->name }}</span>
        <span style="position:relative;display:inline-block;margin:0 10px;">
            <i class="fas fa-bell" id="notification-bell" style="font-size:26px;color:#555;"></i>
            <span id="notification-badge" style="position:absolute;top:-7px;right:-7px;background:#dc3545;color:#fff;border-radius:50%;padding:2px 7px;font-size:12px;min-width:18px;text-align:center;{{ (($unreadNotificationsCount ?? 0) > 0) ? '' : 'display:none;' }}">
    {{ $unreadNotificationsCount ?? 0 }}
</span>
        </span>
        <img src="{{ asset('images/logo.jpg') }}" alt="Tuniship Logo" height="64" style="width:64px;object-fit:contain;">
    </div>
</div>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <!-- Card header content if needed -->
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Email</th>
                                    <th>Rôle</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            @php
    $roleLabels = \App\Models\User::getRoles();
    $roleBadges = [
        'admin' => 'bg-danger',
        'commercial_routier' => 'bg-success',
        'exploitation_routier' => 'bg-secondary',
        'commercial_maritime' => 'bg-primary',
        'exploitation_maritime' => 'bg-info',
        'commercial_aerien' => 'bg-info',
        'exploitation_aerien' => 'bg-secondary',
    ];
    $badgeClass = $roleBadges[$user->role] ?? 'bg-dark';
@endphp
<span class="badge {{ $badgeClass }}">
    {{ $roleLabels[$user->role] ?? ucfirst(str_replace('_', ' ', $user->role)) }}
</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i> Modifier
                                            </a>
                                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">
                                                    <i class="fas fa-trash"></i> Supprimer
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
