@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h1 class="dashboard-title">Prestataires de Services</h1>
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
        <div class="mb-4 d-flex justify-content-end">
            <a href="{{ route('admin.service-providers.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Ajouter un prestataire
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nom</th>
                                <th>Email</th>
                                <th>Téléphone</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($serviceProviders as $provider)
                                <tr>
                                    <td>{{ $provider->id }}</td>
                                    <td>{{ $provider->name }}</td>
                                    <td>{{ $provider->email }}</td>
                                    <td>{{ $provider->phone }}</td>
                                    <td>
                                        <a href="{{ route('admin.service-providers.show', $provider) }}"
                                           class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.service-providers.edit', $provider) }}"
                                           class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.service-providers.destroy', $provider) }}"
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce prestataire ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Aucun prestataire trouvé</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $serviceProviders->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
