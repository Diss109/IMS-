@extends('layouts.admin')
@section('page_title', 'Détail de la Réclamation')
@section('content')
<div class="container mt-4">
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0">Détail de la Réclamation</h3>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <strong>ID :</strong> {{ $complaint->id }}<br>
                <strong>Entreprise :</strong> {{ $complaint->company_name ?? '-' }}<br>
                <strong>Client :</strong> {{ $complaint->first_name ?? '' }} {{ $complaint->last_name ?? '' }}<br>
                <strong>Email :</strong> <a href="mailto:{{ $complaint->email }}">{{ $complaint->email }}</a><br>
                <strong>Type de réclamation :</strong> 
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
                <br>
                <strong>Niveau d'urgence :</strong>
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
                <span class="badge {{ $urgency['class'] }}">{{ $urgency['label'] }}</span><br>
                <strong>Statut :</strong>
                @switch($complaint->status)
                    @case('nouveau')
                        <span class="badge bg-primary">Nouveau</span>
                        @break
                    @case('en_cours')
                        <span class="badge bg-warning">En cours</span>
                        @break
                    @case('resolu')
                        <span class="badge bg-success">Résolu</span>
                        @break
                    @case('ferme')
                        <span class="badge bg-secondary">Fermé</span>
                        @break
                    @default
                        <span class="badge bg-secondary">-</span>
                @endswitch
                <br>
                <strong>Date :</strong> {{ $complaint->created_at->format('d/m/Y H:i') }}<br>
            </div>
            <div class="mb-3">
                <strong>Description :</strong><br>
                <div class="border rounded p-2 bg-light">{{ $complaint->description }}</div>
            </div>
            <div class="mb-3">
                <strong>Note :</strong><br>
                <div class="border rounded p-2 bg-light">{{ $complaint->note ?? '-' }}</div>
            </div>
            <div class="alert alert-info mt-4">
                <strong>Avez-vous résolu cette réclamation ?</strong> Merci de mettre à jour le statut si c'est le cas.
            </div>
            <a href="{{ route('user.complaints.index') }}" class="btn btn-secondary">Retour</a>
        </div>
    </div>
</div>
@endsection
