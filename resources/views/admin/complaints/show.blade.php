@extends('layouts.admin')

@section('content')
@section('page_title', 'Détails de la Réclamation')
    <div class="mb-4 text-end">
        <a href="{{ route('admin.complaints.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <!-- Complaint Details -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informations de la réclamation</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Entreprise:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $complaint->company_name }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Contact:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $complaint->first_name }} {{ $complaint->last_name }}<br>
                            <a href="mailto:{{ $complaint->email }}">{{ $complaint->email }}</a>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Type de réclamation:</strong>
                        </div>
                        <div class="col-md-8">
                            @switch($complaint->complaint_type)
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
                            @endswitch
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Niveau d'urgence:</strong>
                        </div>
                        <div class="col-md-8">
                            @php
    $levels = [
        'critique' => ['label' => 'Critique', 'class' => 'bg-dark'],
        'critical' => ['label' => 'Critique', 'class' => 'bg-dark'],
        'élevé' => ['label' => 'Élevé', 'class' => 'bg-danger'],
        'high' => ['label' => 'Élevé', 'class' => 'bg-danger'],
        'moyen' => ['label' => 'Moyen', 'class' => 'bg-warning'],
        'medium' => ['label' => 'Moyen', 'class' => 'bg-warning'],
        'faible' => ['label' => 'Faible', 'class' => 'bg-success'],
        'low' => ['label' => 'Faible', 'class' => 'bg-success'],
    ];
    $urgency = $levels[$complaint->urgency_level] ?? ['label' => 'Faible', 'class' => 'bg-success'];
@endphp
<span class="badge {{ $urgency['class'] }}">{{ $urgency['label'] }}</span>
                        </div>
                    </div>

                    <div class="row mb-3">
    <div class="col-md-4">
        <strong>Image jointe :</strong>
    </div>
    <div class="col-md-8">
        @php
            $isImage = false;
            if ($complaint->attachment && preg_match('/\.(jpg|jpeg|png|gif|bmp|webp)$/i', $complaint->attachment)) {
                $isImage = true;
            }
        @endphp
        @if($complaint->attachment && $isImage)
            <a href="{{ asset('storage/' . $complaint->attachment) }}" target="_blank">
                <img src="{{ asset('storage/' . $complaint->attachment) }}" alt="Image jointe" style="max-width: 180px; max-height: 180px; border-radius: 8px; border:1px solid #ddd;">
            </a>
        @elseif(!$complaint->attachment)
            <span class="text-muted">Aucune image jointe.</span>
        @else
            <span class="text-muted">Le fichier joint n'est pas une image.</span>
        @endif
    </div>
</div>

@if($complaint->attachment && !$isImage)
<div class="row mb-3">
    <div class="col-md-4">
        <strong>Pièce jointe :</strong>
    </div>
    <div class="col-md-8">
        <a href="{{ asset('storage/' . $complaint->attachment) }}" target="_blank" class="btn btn-outline-primary">
            Télécharger la pièce jointe
        </a>
    </div>
</div>
@endif

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Date de soumission:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $complaint->created_at->format('d/m/Y H:i') }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Description:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $complaint->description }}
                        </div>
                    </div>

                    @if($complaint->assigned_to)
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Assigné à:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $complaint->assignedUser->name }} ({{ \App\Models\User::getRoles()[$complaint->assignedUser->role] ?? $complaint->assignedUser->role }})
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Status Management -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Gestion de la réclamation</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.complaints.update', $complaint) }}" method="POST">
                        @csrf
                        @method('PUT')

                        @if(auth()->user()->isAdmin())
                        <div class="mb-3">
                            <label for="assigned_to" class="form-label">Assigner à</label>
                            <select name="assigned_to" id="assigned_to" class="form-select">
                                <option value="">Sélectionner un utilisateur</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}"
                                            {{ $complaint->assigned_to == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ \App\Models\User::getRoles()[$user->role] ?? $user->role }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        <div class="mb-3">
                            <label for="status" class="form-label">Statut actuel</label>
                            <select name="status" class="form-select" required>
                                <option value="en_attente"
                                        {{ $complaint->status === 'en_attente' ? 'selected' : '' }}>
                                    En attente
                                </option>
                                <option value="résolu"
                                        {{ $complaint->status === 'résolu' ? 'selected' : '' }}>
                                    Résolu
                                </option>
                                <option value="non_résolu"
                                        {{ $complaint->status === 'non_résolu' ? 'selected' : '' }}>
                                    Non résolu
                                </option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="admin_notes" class="form-label">Notes administratives</label>
                            <textarea name="admin_notes"
                                      id="admin_notes"
                                      class="form-control"
                                      rows="4">{{ $complaint->admin_notes }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            Mettre à jour la réclamation
                        </button>
                    </form>

                    @if(auth()->user()->isAdmin())
                    <hr>
                    <form action="{{ route('admin.complaints.destroy', $complaint) }}"
                          method="POST"
                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette réclamation ?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-trash"></i> Supprimer la réclamation
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
