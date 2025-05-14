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


    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0">Réclamations récentes</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-sm" style="font-size: 0.85rem;">
                    <thead>
                        <tr>
                            <th style="width: 5%">ID</th>
                            <th style="width: 15%">Entreprise</th>
                            <th style="width: 15%">Contact</th>
                            <th style="width: 15%">Type</th>
                            <th style="width: 10%">Urgence</th>
                            <th style="width: 10%">Statut</th>
                            <th style="width: 15%">Date</th>
                            <th style="width: 15%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($complaints as $complaint)
                            <tr>
                                <td class="align-middle">#{{ $complaint->id }}</td>
                                <td class="align-middle">{{ $complaint->company_name }}</td>
                                <td class="align-middle">
                                    {{ $complaint->first_name }} {{ $complaint->last_name }}<br>
                                    <small class="text-muted">{{ $complaint->email }}</small>
                                </td>
                                <td class="align-middle">
                                    @if($complaint->complaint_type === 'retard_livraison')
                                        <span class="badge bg-info" style="font-size: 0.75rem;">Retard de livraison</span>
                                    @elseif($complaint->complaint_type === 'retard_chargement')
                                        <span class="badge bg-primary" style="font-size: 0.75rem;">Retard de chargement</span>
                                    @elseif($complaint->complaint_type === 'marchandise_endommagée')
                                        <span class="badge bg-dark" style="font-size: 0.75rem;">Marchandise endommagée</span>
                                    @elseif($complaint->complaint_type === 'mauvais_comportement')
                                        <span class="badge bg-secondary" style="font-size: 0.75rem;">Mauvais comportement</span>
                                    @else
                                        <span class="badge bg-light text-dark" style="font-size: 0.75rem;">Autre</span>
                                    @endif
                                </td>
                                <td class="align-middle">
                                    @if($complaint->urgency_level === 'high')
                                        <span class="badge bg-danger" style="font-size: 0.75rem;">Élevé</span>
                                    @elseif($complaint->urgency_level === 'medium')
                                        <span class="badge bg-warning" style="font-size: 0.75rem;">Moyen</span>
                                    @elseif($complaint->urgency_level === 'critical')
                                        <span class="badge bg-dark" style="font-size: 0.75rem;">Critique</span>
                                    @else
                                        <span class="badge bg-success" style="font-size: 0.75rem;">Faible</span>
                                    @endif
                                </td>
                                <td class="align-middle">
                                    @if($complaint->status === 'résolu')
                                        <span class="badge bg-success" style="font-size: 0.75rem;">Résolu</span>
                                    @elseif($complaint->status === 'en_attente')
                                        <span class="badge bg-warning" style="font-size: 0.75rem;">En attente</span>
                                    @else
                                        <span class="badge bg-danger" style="font-size: 0.75rem;">Non résolu</span>
                                    @endif
                                </td>
                                <td class="align-middle">{{ $complaint->created_at->format('d/m/Y H:i') }}</td>
                                <td class="p-1">
                                    <div class="d-flex gap-1 justify-content-center">
                                        <a href="{{ route('admin.complaints.show', $complaint) }}"
                                           class="btn btn-xs btn-info p-1" style="font-size: 0.8rem;"
                                           title="Voir les détails">
                                            <i class="fas fa-eye"></i> Voir
                                        </a>
                                        @if(auth()->user()->isAdmin())
                                        <form action="{{ route('admin.complaints.destroy', $complaint) }}"
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette réclamation ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="btn btn-xs btn-danger p-1" style="font-size: 0.8rem;"
                                                    title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        @endif
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
