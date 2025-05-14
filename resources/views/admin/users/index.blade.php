@extends('layouts.admin')

@section('content')
    @section('page_title', 'Gestion des Utilisateurs')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <!-- Card header content if needed -->
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-sm" style="font-size: 0.85rem;">
                                <thead>
                                    <tr>
                                        <th style="width: 25%">Nom</th>
                                        <th style="width: 30%">Email</th>
                                        <th style="width: 20%">Rôle</th>
                                        <th style="width: 25%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                        <tr>
                                            <td class="align-middle">{{ $user->name }}</td>
                                            <td class="align-middle">{{ $user->email }}</td>
                                            <td class="align-middle">
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
                                                <span class="badge {{ $badgeClass }}" style="font-size: 0.75rem;">
                                                    {{ $roleLabels[$user->role] ?? ucfirst(str_replace('_', ' ', $user->role)) }}
                                                </span>
                                            </td>
                                            <td class="p-1">
                                                <div class="d-flex gap-1 justify-content-center">
                                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-xs btn-primary p-1" style="font-size: 0.8rem;">
                                                        <i class="fas fa-edit"></i> Modifier
                                                    </a>
                                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-xs btn-danger p-1" style="font-size: 0.8rem;" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">
                                                            <i class="fas fa-trash"></i> Supprimer
                                                        </button>
                                                    </form>
                                                </div>
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
