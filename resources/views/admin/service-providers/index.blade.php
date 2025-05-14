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
        <div class="mb-4 d-flex justify-content-between align-items-center">
        <form method="GET" action="{{ route('admin.service-providers.index') }}" class="d-flex gap-2 align-items-center mb-0">
            <input type="text" name="search" class="form-control" placeholder="Rechercher par nom ou email..." value="{{ request('search') }}">
            <select name="category" class="form-select">
                <option value="">Toutes les catégories</option>
                <option value="armateur" {{ request('category') == 'armateur' ? 'selected' : '' }}>Armateur</option>
                <option value="compagnie_aerienne" {{ request('category') == 'compagnie_aerienne' ? 'selected' : '' }}>Compagnie aérienne</option>
                <option value="transporteur_routier_int" {{ request('category') == 'transporteur_routier_int' ? 'selected' : '' }}>Transporteur routier international</option>
                <option value="transporteur_terrestre_local" {{ request('category') == 'transporteur_terrestre_local' ? 'selected' : '' }}>Transporteur terrestre local</option>
                <option value="agent" {{ request('category') == 'agent' ? 'selected' : '' }}>Agent</option>
                <option value="magasin" {{ request('category') == 'magasin' ? 'selected' : '' }}>Magasin</option>
                <option value="autre" {{ request('category') == 'autre' ? 'selected' : '' }}>Autre</option>
            </select>
            <button class="btn btn-primary" type="submit">Rechercher</button>
            @if(request('search') || request('category'))
                <a href="{{ route('admin.service-providers.index') }}" class="btn btn-secondary">Réinitialiser</a>
            @endif
        </form>
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
                    <table class="table table-bordered table-striped table-sm" style="font-size: 0.85rem;">
                        <thead>
                            <tr>
                                <th style="width: 8%">ID</th>
                                <th style="width: 25%">Nom</th>
                                <th style="width: 25%">Email</th>
                                <th style="width: 18%">Téléphone</th>
                                <th style="width: 24%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($serviceProviders as $provider)
                                <tr>
                                    <td class="align-middle">{{ $provider->id }}</td>
                                    <td class="align-middle">{{ $provider->name }}</td>
                                    <td class="align-middle">{{ $provider->email }}</td>
                                    <td class="align-middle">{{ $provider->phone }}</td>
                                    <td class="p-1">
                                        <div class="d-flex gap-1 justify-content-center">
                                            <a href="{{ route('admin.service-providers.show', $provider) }}"
                                            class="btn btn-xs btn-info p-1" style="font-size: 0.8rem;">
                                                <i class="fas fa-eye"></i> Voir
                                            </a>
                                            <a href="{{ route('admin.service-providers.edit', $provider) }}"
                                            class="btn btn-xs btn-warning p-1" style="font-size: 0.8rem;">
                                                <i class="fas fa-edit"></i> Modifier
                                            </a>
                                            <form action="{{ route('admin.service-providers.destroy', $provider) }}"
                                                method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce prestataire ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-xs btn-danger p-1" style="font-size: 0.8rem;">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
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

                <div class="mt-4 d-flex justify-content-center gap-2">
                    @if ($serviceProviders->onFirstPage())
                        <button class="btn btn-secondary" disabled>Précédent</button>
                    @else
                        <a class="btn btn-primary" href="{{ $serviceProviders->previousPageUrl() }}">Précédent</a>
                    @endif
                    <span class="align-self-center">Page {{ $serviceProviders->currentPage() }} / {{ $serviceProviders->lastPage() }}</span>
                    @if ($serviceProviders->hasMorePages())
                        <a class="btn btn-primary" href="{{ $serviceProviders->nextPageUrl() }}">Suivant</a>
                    @else
                        <button class="btn btn-secondary" disabled>Suivant</button>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
