@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h2 class="mb-0">Détails du Prestataire</h2>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>Nom :</strong> {{ $serviceProvider->name }}
                </div>
                <div class="col-md-6">
                    <strong>Email :</strong> {{ $serviceProvider->email }}
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>Téléphone :</strong> {{ $serviceProvider->phone }}
                </div>
                <div class="col-md-6">
                    <strong>Adresse :</strong> {{ $serviceProvider->address }}
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>Catégorie :</strong> {{ \App\Models\ServiceProvider::getTypes()[$serviceProvider->service_type] ?? $serviceProvider->service_type }}
                </div>
                <div class="col-md-6">
                    <strong>Statut :</strong>
                    @php
                        $statusLabels = ['active' => 'Actif', 'inactive' => 'Inactif', 'suspended' => 'Suspendu'];
                    @endphp
                    <span class="badge bg-{{ $serviceProvider->status == 'active' ? 'success' : ($serviceProvider->status == 'inactive' ? 'secondary' : 'warning') }}">
                        {{ $statusLabels[$serviceProvider->status] ?? $serviceProvider->status }}
                    </span>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>Personne de contact :</strong> {{ $serviceProvider->contact_person ?? '-' }}
                </div>
                <div class="col-md-6">
                    <strong>Description :</strong> {{ $serviceProvider->description ?? '-' }}
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.service-providers.edit', $serviceProvider) }}" class="btn btn-warning me-2">
                    <i class="fas fa-edit"></i> Modifier
                </a>
                <form action="{{ route('admin.service-providers.destroy', $serviceProvider) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce prestataire ?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Supprimer
                    </button>
                </form>
                <a href="{{ route('admin.service-providers.index') }}" class="btn btn-secondary ms-2">Retour à la liste</a>
            </div>
        </div>
    </div>
</div>
@endsection
