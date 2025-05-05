@extends('layouts.admin')
@section('page_title', 'Mes Réclamations')
@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <form method="GET" class="mb-3 d-flex gap-2">
        <input type="text" name="search" class="form-control" placeholder="Rechercher..." value="{{ request('search') }}">
        <select name="status" class="form-select">
            <option value="">Tous les statuts</option>
            <option value="en_attente" {{ request('status') == 'en_attente' ? 'selected' : '' }}>En attente</option>
            <option value="résolu" {{ request('status') == 'résolu' ? 'selected' : '' }}>Résolu</option>
            <option value="non_résolu" {{ request('status') == 'non_résolu' ? 'selected' : '' }}>Non résolu</option>
        </select>
        <button class="btn btn-primary" type="submit">Filtrer</button>
    </form>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Type</th>
                            <th>Client</th>
                            <th>Statut</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($complaints as $complaint)
                            <tr>
                                <td>{{ $complaint->id }}</td>
                                <td>@switch($complaint->complaint_type)
                                        @case('retard_livraison')
                                            Retard de livraison
                                            @break
                                        @case('retard_chargement')
                                            Retard de chargement
                                            @break
                                        @case('marchandise_endommagée')
                                            Marchandise endommagée
                                            @break
                                        @case('mauvais_comportement')
                                            Mauvais comportement
                                            @break
                                        @default
                                            Autre
                                    @endswitch</td>
                                <td>{{ $complaint->first_name }} {{ $complaint->last_name }}<br>
                                    <small>{{ $complaint->company_name }}</small></td>
                                <td>
                                    @switch($complaint->status)
                                        @case('en_attente')
                                            <span class="badge bg-warning">En attente</span>
                                            @break
                                        @case('résolu')
                                            <span class="badge bg-success">Résolu</span>
                                            @break
                                        @case('non_résolu')
                                            <span class="badge bg-danger">Non résolu</span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary">{{ $complaint->status }}</span>
                                    @endswitch
                                </td>
                                <td>{{ $complaint->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('user.complaints.show', $complaint->id) }}" class="btn btn-sm btn-info">Détails</a>
                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#statusModal{{ $complaint->id }}">Changer statut</button>
                                </td>
                            </tr>
                            <!-- Modal for status update -->
                            <div class="modal fade" id="statusModal{{ $complaint->id }}" tabindex="-1" aria-labelledby="statusModalLabel{{ $complaint->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form method="POST" action="{{ route('user.complaints.updateStatus', $complaint->id) }}">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="statusModalLabel{{ $complaint->id }}">Changer le statut</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <select name="status" class="form-select mb-2" required>
                                                    <option value="en_attente" {{ $complaint->status == 'en_attente' ? 'selected' : '' }}>En attente</option>
                                                    <option value="résolu" {{ $complaint->status == 'résolu' ? 'selected' : '' }}>Résolu</option>
                                                    <option value="non_résolu" {{ $complaint->status == 'non_résolu' ? 'selected' : '' }}>Non résolu</option>
                                                </select>
                                                <textarea name="note" class="form-control" rows="2" placeholder="Ajouter une note (optionnel)">{{ $complaint->note }}</textarea>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                <button type="submit" class="btn btn-primary">Enregistrer</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr><td colspan="6">Aucune réclamation trouvée.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                {{ $complaints->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
