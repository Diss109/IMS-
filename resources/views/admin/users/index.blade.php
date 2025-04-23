@extends('layouts.admin')

@section('content')
    @section('page_title', 'Gestion des Utilisateurs')
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
        </span>

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
