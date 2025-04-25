@extends('layouts.admin')

@section('page_title', 'Gestion des Prestataires')
@section('content')
    <div class="container-fluid">
        <div class="d-flex align-items-center justify-content-between mb-4">
@section('page_title', 'Gestion des Prestataires')


                <span style="position:relative;display:inline-block;margin:0 10px;">

                    
                </span>

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
    <a href="{{ route('admin.evaluations.create', $provider->id) }}" class="btn btn-sm btn-primary">
        <i class="fas fa-check"></i> Évaluer
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
