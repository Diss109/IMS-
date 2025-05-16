@extends('layouts.admin')

@section('page_title', 'Détails de la prévision')

@section('styles')
<style>
    .card-accent {
        border-top: 4px solid;
    }
    .card-accent-primary {
        border-top-color: #4e73df;
    }
    .card-accent-success {
        border-top-color: #1cc88a;
    }
    .card-accent-warning {
        border-top-color: #f6c23e;
    }
    .card-accent-danger {
        border-top-color: #e74a3b;
    }
    .card-accent-info {
        border-top-color: #36b9cc;
    }
    .prediction-badge {
        font-size: 1rem;
        padding: 0.5rem 0.75rem;
    }
    .stat-value {
        font-size: 2rem;
        font-weight: 700;
    }
    .stat-label {
        font-size: 0.8rem;
        color: #858796;
    }
    .trend-icon {
        font-size: 1rem;
        margin-right: 0.25rem;
    }
    .prediction-period {
        font-size: 0.8rem;
        color: #858796;
        margin-top: 0.25rem;
    }
    .change-point {
        padding-left: 10px;
        border-left: 3px solid #e74a3b;
        margin-bottom: 8px;
    }
    .card {
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            Prévision: {{ $provider->name }}
        </h1>
        <div>
            <a href="{{ route('admin.predictions.regenerate', $provider->id) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-sync"></i> Régénérer
            </a>
            <a href="{{ route('admin.predictions.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    @if(!$latestPrediction && $provider->evaluations->count() < 5)
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i>
            Données insuffisantes pour générer une prévision (minimum 5 évaluations requises).
        </div>
    @endif

    <!-- Main content -->
    <div class="row">
        <!-- Prediction Overview -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Résumé des Prévisions</h6>
                    @if($latestPrediction)
                        <span class="badge bg-secondary">Dernière mise à jour: {{ $latestPrediction->prediction_date->format('d/m/Y') }}</span>
                    @endif
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Monthly Prediction -->
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 card-accent {{ $predictions['next_month'] ? ($predictions['next_month']->predicted_score >= 75 ? 'card-accent-success' : ($predictions['next_month']->predicted_score >= 50 ? 'card-accent-warning' : 'card-accent-danger')) : 'card-accent-primary' }}">
                                <div class="card-body text-center">
                                    <h5 class="card-title font-weight-bold text-gray-800">Prochain Mois</h5>
                                    @if(isset($predictions['next_month']))
                                        <div class="stat-value mb-0 {{ $predictions['next_month']->predicted_score >= 75 ? 'text-success' : ($predictions['next_month']->predicted_score >= 50 ? 'text-warning' : 'text-danger') }}">
                                            {{ round($predictions['next_month']->predicted_score, 1) }}
                                        </div>
                                        <div class="small text-muted mt-2">
                                            Précision: {{ number_format($predictions['next_month']->confidence_level * 100, 1) }}%
                                        </div>
                                    @else
                                        <div class="text-muted py-3">Non disponible</div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Quarterly Prediction -->
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 card-accent {{ $predictions['next_quarter'] ? ($predictions['next_quarter']->predicted_score >= 75 ? 'card-accent-success' : ($predictions['next_quarter']->predicted_score >= 50 ? 'card-accent-warning' : 'card-accent-danger')) : 'card-accent-primary' }}">
                                <div class="card-body text-center">
                                    <h5 class="card-title font-weight-bold text-gray-800">Prochain Trimestre</h5>
                                    @if(isset($predictions['next_quarter']))
                                        <div class="stat-value mb-0 {{ $predictions['next_quarter']->predicted_score >= 75 ? 'text-success' : ($predictions['next_quarter']->predicted_score >= 50 ? 'text-warning' : 'text-danger') }}">
                                            {{ round($predictions['next_quarter']->predicted_score, 1) }}
                                        </div>
                                        <div class="small text-muted mt-2">
                                            Précision: {{ number_format($predictions['next_quarter']->confidence_level * 100, 1) }}%
                                        </div>
                                    @else
                                        <div class="text-muted py-3">Non disponible</div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Yearly Prediction -->
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 card-accent {{ $predictions['next_year'] ? ($predictions['next_year']->predicted_score >= 75 ? 'card-accent-success' : ($predictions['next_year']->predicted_score >= 50 ? 'card-accent-warning' : 'card-accent-danger')) : 'card-accent-primary' }}">
                                <div class="card-body text-center">
                                    <h5 class="card-title font-weight-bold text-gray-800">Prochaine Année</h5>
                                    @if(isset($predictions['next_year']))
                                        <div class="stat-value mb-0 {{ $predictions['next_year']->predicted_score >= 75 ? 'text-success' : ($predictions['next_year']->predicted_score >= 50 ? 'text-warning' : 'text-danger') }}">
                                            {{ round($predictions['next_year']->predicted_score, 1) }}
                                        </div>
                                        <div class="small text-muted mt-2">
                                            Précision: {{ number_format($predictions['next_year']->confidence_level * 100, 1) }}%
                                        </div>
                                    @else
                                        <div class="text-muted py-3">Non disponible</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Performance Chart -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">Historique et Prévisions</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Période</th>
                                            <th>Type</th>
                                            <th>Score</th>
                                            <th>Tendance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Recent Evaluations (last 5, shown oldest to newest) -->
                                        @php
                                            $lastFiveEvaluations = $provider->evaluations->sortByDesc('created_at')->take(5)->sortBy('created_at');
                                        @endphp
                                        @forelse($lastFiveEvaluations as $evaluation)
                                        <tr>
                                            <td>{{ $evaluation->created_at->format('d/m/Y') }}</td>
                                            <td><span class="badge bg-secondary">Évaluation</span></td>
                                            <td>
                                                <h5 class="mb-0">
                                                    <span class="badge bg-{{ $evaluation->total_score >= 75 ? 'success' : ($evaluation->total_score >= 50 ? 'warning' : 'danger') }}">
                                                        {{ number_format($evaluation->total_score, 1) }}
                                                    </span>
                                                </h5>
                                            </td>
                                            <td>
                                                <span class="text-muted">-</span>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center">Aucune évaluation disponible</td>
                                        </tr>
                                        @endforelse

                                        <!-- Predictions -->
                                        @php $hasPredictions = false; @endphp

                                        @foreach(['next_month', 'next_quarter', 'next_year'] as $period)
                                            @if(isset($predictions[$period]) && $predictions[$period])
                                            @php $hasPredictions = true; @endphp
                                            <tr class="bg-light">
                                                <td>
                                                    {{ $predictions[$period]->prediction_date->format('d/m/Y') }}
                                                    <div class="small text-muted">{{ $period === 'next_month' ? 'Mensuel' : ($period === 'next_quarter' ? 'Trimestriel' : 'Annuel') }}</div>
                                                </td>
                                                <td><span class="badge bg-primary">Prédiction</span></td>
                                                <td>
                                                    @if(isset($predictions[$period]->predicted_score))
                                                    <h5 class="mb-0">
                                                        <span class="badge bg-{{ $predictions[$period]->predicted_score >= 75 ? 'success' : ($predictions[$period]->predicted_score >= 50 ? 'warning' : ($predictions[$period]->predicted_score > 0 ? 'danger' : 'secondary')) }}">
                                                            {{ number_format($predictions[$period]->predicted_score, 1) }}
                                                        </span>
                                                    </h5>
                                                    <div class="small text-muted mt-1">Précision: {{ number_format($predictions[$period]->confidence_level * 100, 1) }}%</div>
                                                    @else
                                                    <span class="badge bg-secondary">N/A</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if(isset($predictions[$period]->factors) && is_array($predictions[$period]->factors) && isset($predictions[$period]->factors['trend']))
                                                        @php
                                                            $trend = $predictions[$period]->factors['trend'];
                                                        @endphp
                                                        <span class="badge bg-{{ $trend === 'improving' ? 'success' : ($trend === 'declining' ? 'danger' : 'info') }}">
                                                            @if($trend === 'improving')
                                                                <i class="fas fa-arrow-up"></i>
                                                            @elseif($trend === 'declining')
                                                                <i class="fas fa-arrow-down"></i>
                                                            @else
                                                                <i class="fas fa-minus"></i>
                                                            @endif
                                                            {{ $trend === 'improving' ? 'Amélioration' : ($trend === 'declining' ? 'Dégradation' : 'Stable') }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endif
                                        @endforeach

                                        @if(!$hasPredictions && $provider->evaluations->isEmpty())
                                        <tr>
                                            <td colspan="4" class="text-center">Aucune donnée disponible</td>
                                        </tr>
                                        @elseif(!$hasPredictions)
                                        <tr>
                                            <td colspan="4" class="text-center">Aucune prédiction disponible</td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>

                            @if($regressionData && isset($regressionData['slope']))
                            <div class="alert alert-info mt-3">
                                <i class="fas fa-chart-line mr-2"></i>
                                <strong>Tendance globale:</strong>
                                {{ $regressionData['slope'] > 0 ? 'En amélioration' : 'En dégradation' }}
                                ({{ number_format(abs($regressionData['slope']), 3) }} points/mois)
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="col-lg-4">
            <!-- Provider Info -->
            <div class="card mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Prestataire</h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <strong>Type:</strong> {{ \App\Models\ServiceProvider::getTypes()[$provider->service_type] ?? $provider->service_type }}
                    </div>
                    <div class="mb-3">
                        <div class="d-flex align-items-center">
                            <strong class="mr-2">Évaluations:</strong>
                            @php
                                $evaluationsCount = $provider->evaluations->count();
                                $minRequired = 5;
                            @endphp
                            <span class="badge bg-primary">{{ $evaluationsCount }}</span>
                        </div>
                        <div class="mt-2 small text-{{ $evaluationsCount >= $minRequired ? 'success' : 'warning' }}">
                            @if($evaluationsCount >= $minRequired)
                                <i class="fas fa-check-circle"></i> Suffisant pour prédictions (min. {{ $minRequired }})
                            @else
                                <i class="fas fa-exclamation-triangle"></i> Minimum {{ $minRequired }} requis ({{ $evaluationsCount }}/{{ $minRequired }})
                            @endif
                        </div>
                    </div>
                    <div class="mb-2">
                        <strong>Dernière évaluation:</strong>
                        @if($provider->evaluations->isNotEmpty())
                            {{ $provider->evaluations->sortByDesc('created_at')->first()->created_at->format('d/m/Y') }}
                        @else
                            Non disponible
                        @endif
                    </div>
                </div>
            </div>

            <!-- Prediction Factors -->
            @if($latestPrediction && isset($latestPrediction->factors))
            @php
                $factors = $latestPrediction->factors;
            @endphp
            <div class="card mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Facteurs d'influence</h6>
                    <span class="badge bg-info">v{{ $latestPrediction->model_version }}</span>
                </div>
                <div class="card-body">
                    <!-- Trend -->
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-chart-line mr-2 text-gray-500"></i>
                            <strong>Tendance:</strong>
                            <div class="ml-auto">
                                <span class="badge bg-{{ $factors['trend'] == 'improving' ? 'success' : ($factors['trend'] == 'declining' ? 'danger' : 'info') }}">
                                    @if($factors['trend'] == 'improving')
                                        <i class="fas fa-arrow-up"></i> Amélioration
                                    @elseif($factors['trend'] == 'declining')
                                        <i class="fas fa-arrow-down"></i> Dégradation
                                    @else
                                        <i class="fas fa-minus"></i> Stable
                                    @endif
                                </span>
                            </div>
                        </div>
                        @if(isset($factors['slope']))
                        <div class="mt-2 ml-4 small text-muted">
                            Pente: {{ number_format($factors['slope'], 3) }} points/mois
                        </div>
                        @endif
                    </div>

                    <!-- Change Points -->
                    @if(isset($factors['change_points']) && is_array($factors['change_points']) && count($factors['change_points']) > 0)
                    <div class="mb-3 pb-3 border-bottom">
                        <div>
                            <i class="fas fa-exchange-alt mr-2 text-gray-500"></i>
                            <strong>Points de changement:</strong>
                            <span class="badge bg-secondary ml-2">{{ count($factors['change_points']) }}</span>
                        </div>
                        @php
                            $displayedDates = [];
                            $displayCount = 0;
                        @endphp
                        <div class="mt-2">
                            @foreach($factors['change_points'] as $cp)
                                @if(isset($cp['timestamp']) && isset($cp['right_slope']) && isset($cp['left_slope']) && $provider->evaluations->isNotEmpty())
                                    @php
                                        $changeDate = \Carbon\Carbon::parse($provider->evaluations->first()->created_at)
                                            ->addDays($cp['timestamp'] * 30)
                                            ->format('M Y');
                                    @endphp
                                    @if(!in_array($changeDate, $displayedDates) && $displayCount < 2)
                                        @php
                                            $displayedDates[] = $changeDate;
                                            $displayCount++;
                                        @endphp
                                        <div class="change-point mt-2">
                                            <div class="small font-weight-bold">{{ $changeDate }}</div>
                                            <div class="small {{ $cp['right_slope'] > $cp['left_slope'] ? 'text-success' : 'text-danger' }}">
                                                @if($cp['right_slope'] > $cp['left_slope'])
                                                    <i class="fas fa-arrow-up"></i> Amélioration
                                                @else
                                                    <i class="fas fa-arrow-down"></i> Dégradation
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Seasonality -->
                    @if(isset($factors['seasonality_detected']))
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-calendar-alt mr-2 text-gray-500"></i>
                            <strong>Saisonnalité:</strong>
                            <div class="ml-auto">
                                @if($factors['seasonality_detected'])
                                    <span class="badge bg-info">Détectée</span>
                                @else
                                    <span class="badge bg-secondary">Non détectée</span>
                                @endif
                            </div>
                        </div>
                        <div class="mt-2 ml-4 small text-muted">
                            @if($factors['seasonality_detected'])
                                Variations saisonnières prises en compte dans la prédiction
                            @else
                                Données insuffisantes ou pas de cycle saisonnier
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Stats -->
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="small text-muted">Dernier score</div>
                            <div class="badge bg-{{ $factors['last_score'] >= 75 ? 'success' : ($factors['last_score'] >= 50 ? 'warning' : 'danger') }}">
                                {{ isset($factors['last_score']) ? number_format($factors['last_score'], 1) : 'N/A' }}
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="small text-muted">Score moyen</div>
                            <div class="badge bg-{{ $factors['avg_score'] >= 75 ? 'success' : ($factors['avg_score'] >= 50 ? 'warning' : 'danger') }}">
                                {{ isset($factors['avg_score']) ? number_format($factors['avg_score'], 1) : 'N/A' }}
                            </div>
                        </div>
                    </div>
                    <div class="mt-2 text-center small text-muted">
                        Basé sur {{ $factors['evaluations_count'] ?? 0 }} évaluations
                    </div>
                </div>
            </div>
            @endif

            <!-- Accuracy Info -->
            @if($latestPrediction)
            <div class="card mb-4 bg-light">
                <div class="card-body">
                    <div class="small">
                        <i class="fas fa-info-circle mr-1"></i>
                        <strong>À propos du modèle:</strong>
                        <p class="mt-2 mb-0">
                            Modèle de prévision avancé utilisant une régression pondérée
                            @if(isset($latestPrediction->factors['change_points']) && count($latestPrediction->factors['change_points']) > 0)
                            avec détection des points de changement
                            @endif
                            @if(isset($latestPrediction->factors['seasonality_detected']) && $latestPrediction->factors['seasonality_detected'])
                            et analyse de saisonnalité
                            @endif.
                        </p>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- No JS required for the static table -->
@endsection

