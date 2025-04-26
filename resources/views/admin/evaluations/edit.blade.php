@extends('layouts.admin')

@section('page_title', 'Éditer l\'évaluation')
@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Modifier l'évaluation</div>
                <div class="card-body">
    <form method="POST" action="{{ route('admin.evaluations.update', $evaluation->id) }}">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="evaluation_date" class="form-label">Date d'évaluation</label>
            <input type="date" name="evaluation_date" id="evaluation_date" class="form-control" value="{{ old('evaluation_date', $evaluation->evaluation_date ? $evaluation->evaluation_date->format('Y-m-d') : now()->format('Y-m-d') ) }}" required>
        </div>
        <div class="mb-3">
            <label for="global_comment" class="form-label">Commentaire global</label>
            <textarea name="global_comment" id="global_comment" class="form-control" rows="3">{{ old('global_comment', $evaluation->global_comment) }}</textarea>
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
                        <td>
                            <input type="number" name="scores[{{ $score->id }}]" class="form-control" min="0" max="10" step="0.01" value="{{ old('scores.' . $score->id, $score->score) }}" required>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <button type="submit" class="btn btn-primary">Enregistrer</button>
        <a href="{{ route('admin.evaluations.show', $evaluation->id) }}" class="btn btn-secondary">Annuler</a>
    </form>
</div>
            </div>
        </div>
    </div>
</div>
@endsection
