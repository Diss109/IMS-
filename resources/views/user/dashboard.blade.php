@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <h1 class="mb-4">Tableau de bord - {{ __('roles.' . $user->role) }}</h1>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total des réclamations</h5>
                            <h2 class="mb-0">{{ $totalComplaints }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5 class="card-title">Résolues</h5>
                            <h2 class="mb-0">{{ $resolvedComplaints }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h5 class="card-title">En attente</h5>
                            <h2 class="mb-0">{{ $pendingComplaints }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <h5 class="card-title">Non résolues</h5>
                            <h2 class="mb-0">{{ $unsolvedComplaints }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Complaints List -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Mes réclamations</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Entreprise</th>
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
                                        <td>{{ __('complaints.types.' . $complaint->complaint_type) }}</td>
                                        <td>
                                            @if($complaint->urgency_level === 'high')
                                                <span class="badge bg-danger">{{ __('complaints.urgency.high') }}</span>
                                            @elseif($complaint->urgency_level === 'medium')
                                                <span class="badge bg-warning">{{ __('complaints.urgency.medium') }}</span>
                                            @else
                                                <span class="badge bg-success">{{ __('complaints.urgency.low') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($complaint->status === 'résolu')
                                                <span class="badge bg-success">{{ __('complaints.status.résolu') }}</span>
                                            @elseif($complaint->status === 'en_attente')
                                                <span class="badge bg-warning">{{ __('complaints.status.en_attente') }}</span>
                                            @else
                                                <span class="badge bg-danger">{{ __('complaints.status.non_résolu') }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $complaint->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('user.complaints.show', $complaint) }}"
                                               class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> Voir
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Aucune réclamation assignée</td>
                                    </tr>
                                @endforelse
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
</div>
@endsection
