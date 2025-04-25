@extends('layouts.admin')

@section('page_title', 'Détail de l\'évaluation')
@section('content')
<div class="container mt-4">
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0">Détail de l'évaluation</h3>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <strong>Prestataire :</strong> {{ $evaluation->serviceProvider->name ?? '-' }}<br>
                <strong>Évaluateur :</strong> {{ $evaluation->user->name ?? '-' }}<br>
                <strong>Date :</strong> {{ $evaluation->created_at->format('d/m/Y H:i') }}<br>
                <strong>Score total :</strong> {{ $evaluation->total_score }} / 100
            </div>
            <h5 class="mt-4">Notes par critère</h5>
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Critère principal</th>
                        <th>Sous-critère</th>
                        <th>Pondération principale</th>
                        <th>Pondération sous-critère</th>
                        <th>Note / 10</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($evaluation->scores as $score)
                        <tr>
                            <td>{{ ucfirst($score->main_criterion) }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $score->sub_criterion)) }}</td>
                            <td>{{ $score->main_weight * 100 }}%</td>
                            <td>{{ $score->sub_weight }}</td>
                            <td>{{ $score->score }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mb-3 mt-4">
                <strong>Commentaire global :</strong><br>
                <div class="border rounded p-2 bg-light">{{ $evaluation->global_comment ?? '-' }}</div>
            </div>
            <a href="{{ url()->previous() }}" class="btn btn-secondary">Retour</a>
        </div>
    </div>
</div>
@endsection
