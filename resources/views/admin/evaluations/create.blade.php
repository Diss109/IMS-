@extends('layouts.admin')

@section('page_title', 'Évaluation du Prestataire')
@section('content')
<div class="container mt-4">
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h2 class="mb-0">Nouvelle Évaluation (Armateur)</h2>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.evaluations.store') }}">
                @csrf
                <input type="hidden" name="service_provider_id" value="{{ $serviceProvider->id ?? '' }}">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Critère principal</th>
                            <th>Pondération</th>
                            <th>Sous-critère</th>
                            <th>Pondération</th>
                            <th>Note / 10</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($weights as $mainKey => $main)
                        @php $subCount = count($main['sub']); $mainLabel = ucfirst($mainKey); @endphp
                        @foreach($main['sub'] as $subKey => $subWeight)
                            <tr>
                                @if ($loop->first)
                                    <td rowspan="{{ $subCount }}">{{ $mainLabel }}</td>
                                    <td rowspan="{{ $subCount }}">{{ $main['main'] * 100 }}%</td>
                                @endif
                                <td>{{ __(ucfirst(str_replace('_', ' ', $subKey))) }}</td>
                                <td>{{ $subWeight }}</td>
                                <td>
                                    <input type="number" name="scores[{{ $mainKey }}][{{ $subKey }}]" min="0" max="10" step="0.01" class="form-control" required>
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                    </tbody>
                </table>
                <div class="mb-3">
                    <label for="global_comment" class="form-label">Commentaire global</label>
                    <textarea name="global_comment" id="global_comment" class="form-control" rows="3"></textarea>
                </div>
                <div class="mb-3">
                    <button type="submit" class="btn btn-success">Enregistrer l'évaluation</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Historique des évaluations -->
    <div class="card mt-4">
        <div class="card-header bg-secondary text-white">
            <h4 class="mb-0">Historique des évaluations</h4>
        </div>
        <div class="card-body">
            @if(isset($evaluationHistory) && count($evaluationHistory) > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Évaluateur</th>
                                <th>Score total</th>
                                <th>Commentaire</th>
                                <th>Détail</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($evaluationHistory as $eval)
                                <tr>
                                    <td>{{ $eval->created_at->format('d/m/Y') }}</td>
                                    <td>{{ $eval->user->name ?? '-' }}</td>
                                    <td>{{ $eval->total_score ?? '-' }}</td>
                                    <td>{{ Str::limit($eval->global_comment, 40) }}</td>
                                    <td><a href="{{ route('admin.evaluations.show', $eval->id) }}" class="btn btn-info btn-sm">Voir</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-muted">Aucune évaluation précédente.</div>
            @endif
        </div>
    </div>
</div>
@endsection
