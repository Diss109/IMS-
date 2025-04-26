@extends('layouts.admin')
@section('page_title', 'Gestion des réclamations')

@section('content')


    @include('admin.complaints.filters')

    <!-- Search form -->
    <form method="GET" action="" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Rechercher par ID ou nom..." value="{{ request('search') }}">
            <button class="btn btn-primary" type="submit">Rechercher</button>
        </div>
    </form>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0">Réclamations récentes</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Entreprise</th>
                            <th>Contact</th>
                            <th>Type</th>
                            <th>Urgence</th>
                            <th>Statut</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($complaints as $complaint)
                            <tr>
                                <td>#{{ $complaint->id }}</td>
                                <td>{{ $complaint->company_name }}</td>
                                <td>
                                    {{ $complaint->first_name }} {{ $complaint->last_name }}<br>
                                    <small class="text-muted">{{ $complaint->email }}</small>
                                </td>
                                <td>
                                    @if($complaint->complaint_type === 'retard_livraison')
                                        Retard de livraison
                                    @elseif($complaint->complaint_type === 'retard_chargement')
                                        Retard de chargement
                                    @elseif($complaint->complaint_type === 'marchandise_endommagée')
                                        Marchandise endommagée
                                    @elseif($complaint->complaint_type === 'mauvais_comportement')
                                        Mauvais comportement
                                    @else
                                        Autre
                                    @endif
                                </td>
                                <td>
                                    @if($complaint->urgency_level === 'high')
                                        <span class="badge bg-danger">Élevé</span>
                                    @elseif($complaint->urgency_level === 'medium')
                                        <span class="badge bg-warning">Moyen</span>
                                    @elseif($complaint->urgency_level === 'critical')
                                        <span class="badge bg-dark">Critique</span>
                                    @else
                                        <span class="badge bg-success">Faible</span>
                                    @endif
                                </td>
                                <td>
                                    @if($complaint->status === 'résolu')
                                        <span class="badge bg-success">Résolu</span>
                                    @elseif($complaint->status === 'en_attente')
                                        <span class="badge bg-warning">En attente</span>
                                    @else
                                        <span class="badge bg-danger">Non résolu</span>
                                    @endif
                                </td>
                                <td>{{ $complaint->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.complaints.show', $complaint) }}"
                                           class="btn btn-sm btn-info"
                                           title="Voir les détails">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <form action="{{ route('admin.complaints.destroy', $complaint) }}"
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette réclamation ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="btn btn-sm btn-danger"
                                                    title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">Aucune réclamation trouvée</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>


        </div>
        <div class="mt-4 d-flex justify-content-center gap-2">
            @if ($complaints->onFirstPage())
                <button class="btn btn-secondary" disabled>Précédent</button>
            @else
                <a class="btn btn-primary" href="{{ $complaints->previousPageUrl() }}">Précédent</a>
            @endif
            <span class="align-self-center">Page {{ $complaints->currentPage() }} / {{ $complaints->lastPage() }}</span>
            @if ($complaints->hasMorePages())
                <a class="btn btn-primary" href="{{ $complaints->nextPageUrl() }}">Suivant</a>
            @else
                <button class="btn btn-secondary" disabled>Suivant</button>
            @endif
        </div>
    </div>
</div>
<style>
.pagination .page-link,
.pagination .page-link *,
.pagination .page-link span,
.pagination .page-link a {
    font-size: 1rem !important;
    width: auto !important;
    height: auto !important;
    line-height: 1.5 !important;
    vertical-align: middle !important;
}
</style>

@endsection
