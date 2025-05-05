@extends('layouts.admin')
@section('page_title', 'Mes Réclamations')
@section('content')
<div class="container-fluid">
    <form method="GET" class="mb-3 d-flex gap-2">
        <input type="text" name="search" class="form-control" placeholder="Rechercher..." value="{{ request('search') }}">
        <select name="status" class="form-select">
            <option value="">Tous les statuts</option>
            <option value="en_attente" {{ request('status') == 'en_attente' ? 'selected' : '' }}>En attente</option>
            <option value="en_cours" {{ request('status') == 'en_cours' ? 'selected' : '' }}>En cours</option>
            <option value="resolu" {{ request('status') == 'resolu' ? 'selected' : '' }}>Résolu</option>
            <option value="ferme" {{ request('status') == 'ferme' ? 'selected' : '' }}>Fermé</option>
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
                                    @php
                                        // Map status values to appropriate colors
                                        $statusMap = [
                                            'en_attente' => ['label' => 'En attente', 'class' => 'bg-warning text-dark'],
                                            'en_cours' => ['label' => 'En cours', 'class' => 'bg-info text-dark'],
                                            'résolu' => ['label' => 'Résolu', 'class' => 'bg-success'],
                                            'non_résolu' => ['label' => 'Non résolu', 'class' => 'bg-danger']
                                        ];
                                        
                                        $status = $statusMap[$complaint->status] ?? ['label' => $complaint->status, 'class' => 'bg-secondary'];
                                    @endphp
                                    <span class="badge {{ $status['class'] }}">{{ $status['label'] }}</span>
                                </td>
                                <td>{{ $complaint->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('user.complaints.show', $complaint->id) }}" class="btn btn-sm btn-info">Détails</a>
                                    <div class="dropdown d-inline-block">
                                        <button class="btn btn-sm btn-warning dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            Changer statut
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <form action="{{ route('user.complaints.updateStatus', $complaint->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="en_attente">
                                                    <button type="submit" class="dropdown-item">En attente</button>
                                                </form>
                                            </li>
                                            <li>
                                                <form action="{{ route('user.complaints.updateStatus', $complaint->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="en_cours">
                                                    <button type="submit" class="dropdown-item">En cours</button>
                                                </form>
                                            </li>
                                            <li>
                                                <form action="{{ route('user.complaints.updateStatus', $complaint->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="résolu">
                                                    <button type="submit" class="dropdown-item">Résolu</button>
                                                </form>
                                            </li>
                                            <li>
                                                <form action="{{ route('user.complaints.updateStatus', $complaint->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="non_résolu">
                                                    <button type="submit" class="dropdown-item">Non résolu</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
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
                                                    <option value="en_cours" {{ $complaint->status == 'en_cours' ? 'selected' : '' }}>En cours</option>
                                                    <option value="resolu" {{ $complaint->status == 'resolu' ? 'selected' : '' }}>Résolu</option>
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

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get all status change buttons
        const statusButtons = document.querySelectorAll('.change-status-btn');
        
        // Add click event to each button
        statusButtons.forEach(button => {
            button.addEventListener('click', function() {
                const complaintId = this.getAttribute('data-complaint-id');
                const modalId = '#statusModal' + complaintId;
                const modal = new bootstrap.Modal(document.querySelector(modalId));
                modal.show();
            });
        });
    });
</script>
@endsection
