@extends('layouts.admin')

@section('page_title', 'Prévisions')
@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Prévisions de Performance</h1>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow border-left-primary mb-3">
                <div class="card-body py-3">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Prestataires</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['provider_count'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-truck fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow border-left-success mb-3">
                <div class="card-body py-3">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Tendance positive</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['improving_count'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow border-left-warning mb-3">
                <div class="card-body py-3">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                À risque</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['high_risk_count'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow border-left-info mb-3">
                <div class="card-body py-3">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Prévisions générées</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['predicted_count'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-brain fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Prestataires et Prévisions</h6>
                    <div class="dropdown no-arrow">
                        <form action="{{ route('admin.predictions.generate') }}" method="POST" class="d-inline">
                            @csrf
                            <select name="period" class="form-select form-select-sm d-inline-block w-auto">
                                <option value="next_month">Mois prochain</option>
                                <option value="next_quarter">Trimestre prochain</option>
                            </select>
                            <button type="submit" class="btn btn-primary btn-sm ml-2">
                                <i class="fas fa-sync fa-sm"></i> Générer les prévisions
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Prestataire</th>
                                    <th>Type</th>
                                    <th>Nombre d'évaluations</th>
                                    <th>Dernière évaluation</th>
                                    <th>Prévision</th>
                                    <th>Tendance</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($providers as $provider)
                                <tr>
                                    <td>{{ $provider->name }}</td>
                                    <td>{{ \App\Models\ServiceProvider::getTypes()[$provider->service_type] ?? $provider->service_type }}</td>
                                    <td>{{ $provider->evaluations_count }}</td>
                                    <td>
                                        @php
                                            $latestEval = $provider->evaluations()->latest()->first();
                                        @endphp
                                        @if($latestEval)
                                            <span class="badge bg-{{ $latestEval->total_score >= 75 ? 'success' : ($latestEval->total_score >= 50 ? 'warning' : 'danger') }}">
                                                {{ $latestEval->total_score }}
                                            </span>
                                            <small class="text-muted">{{ $latestEval->created_at->format('d/m/Y') }}</small>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if($provider->predictions->isNotEmpty())
                                            <span class="badge bg-{{ $provider->predictions->first()->predicted_score >= 75 ? 'success' : ($provider->predictions->first()->predicted_score >= 50 ? 'warning' : 'danger') }}">
                                                {{ round($provider->predictions->first()->predicted_score, 1) }}
                                            </span>
                                            <div class="progress mt-1" style="height: 4px;">
                                                <div class="progress-bar bg-info" role="progressbar" style="width: {{ $provider->predictions->first()->confidence_level * 100 }}%" aria-valuenow="{{ $provider->predictions->first()->confidence_level * 100 }}" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <small class="text-muted">Précision: {{ round($provider->predictions->first()->confidence_level * 100) }}%</small>
                                        @else
                                            @if($provider->evaluations_count < 5)
                                                <small class="text-muted fst-italic">Nombre d'évaluations insuffisant</small>
                                            @else
                                                -
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        @if(isset($provider->trend) && $provider->trend['has_trend'])
                                            <span class="badge bg-{{ $provider->trend['status'] }}">
                                                <i class="fas fa-{{ $provider->trend['icon'] }}"></i>
                                                {{ $provider->trend['message'] }}
                                            </span>
                                        @else
                                            <span class="text-muted">Données insuffisantes</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($provider->evaluations_count >= 5)
                                            <a href="{{ route('admin.predictions.show', $provider->id) }}" class="btn btn-sm btn-info">Détails</a>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 d-flex justify-content-center gap-2">
                        @if ($providers->onFirstPage())
                            <button class="btn btn-secondary" disabled>Précédent</button>
                        @else
                            <a class="btn btn-primary" href="{{ $providers->previousPageUrl() }}">Précédent</a>
                        @endif
                        <span class="align-self-center">Page {{ $providers->currentPage() }} / {{ $providers->lastPage() }}</span>
                        @if ($providers->hasMorePages())
                            <a class="btn btn-primary" href="{{ $providers->nextPageUrl() }}">Suivant</a>
                        @else
                            <button class="btn btn-secondary" disabled>Suivant</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
