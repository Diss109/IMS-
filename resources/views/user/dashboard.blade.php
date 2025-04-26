@extends('layouts.admin')

@section('page_title', 'Tableau de bord')

@section('content')
    <div class="container-fluid">
        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Réclamations</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalComplaints }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Résolues</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $resolvedComplaints }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Réclamations</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nom de l'entreprise</th>
                                <th>Type de réclamation</th>
                                <th>Niveau d'urgence</th>
                                <th>Statut</th>
                                <th>Date de création</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($complaints as $complaint)
                                <tr>
                                    <td>{{ $complaint->id }}</td>
                                    <td>{{ $complaint->company_name }}</td>
                                    <td>{{ $complaint->complaint_type }}</td>
                                    <td>
    @switch($complaint->urgency_level)
        @case('critical')
            <span class="badge bg-danger">Critique</span>
            @break
        @case('high')
            <span class="badge bg-warning">Élevé</span>
            @break
        @case('medium')
            <span class="badge bg-info">Moyen</span>
            @break
        @case('low')
            <span class="badge bg-success">Faible</span>
            @break
        @default
            <span class="badge bg-secondary">-</span>
    @endswitch
</td>
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
        <span class="badge bg-secondary">-</span>
@endswitch
</td>
                                    <td>{{ $complaint->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
    @if($complaint->assigned_to === Auth::id())
    <a href="{{ route('user.complaints.show', $complaint->id) }}" class="btn btn-primary btn-sm">
        <i class="fas fa-eye"></i> Voir
    </a>
@else
    -
@endif
</td>
                                </tr>
                            @endforeach
                            @if($complaints->isEmpty())
                                <tr>
                                    <td colspan="7" class="text-center">Aucune réclamation assignée</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $complaints->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
