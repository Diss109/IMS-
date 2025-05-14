@extends('layouts.admin')

@section('page_title', 'Détails de la prévision')

@section('styles')
<style>
    .prediction-card {
        border-left: 5px solid #4e73df;
    }
    .metric-card {
        height: 100%;
        transition: all 0.2s;
    }
    .metric-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    .trend-improving {
        border-left: 5px solid #1cc88a;
    }
    .trend-declining {
        border-left: 5px solid #e74a3b;
    }
    .trend-stable {
        border-left: 5px solid #36b9cc;
    }
    .equal-height-row {
        display: flex;
        flex-wrap: wrap;
    }
    .equal-height-col {
        display: flex;
        flex-direction: column;
    }
    .equal-height-card {
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    .equal-height-card .card-body {
        flex: 1;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            Prévision pour {{ $provider->name }}
        </h1>
        <a href="{{ route('admin.predictions.index') }}" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour aux prévisions
        </a>
    </div>

    <div class="row equal-height-row">
        <!-- Provider Info Card -->
        <div class="col-lg-4 mb-4 equal-height-col">
            <div class="card shadow mb-4 equal-height-card">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informations du prestataire</h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <strong>Nom:</strong> {{ $provider->name }}
                    </div>
                    <div class="mb-2">
                        <strong>Type:</strong> {{ \App\Models\ServiceProvider::getTypes()[$provider->service_type] ?? $provider->service_type }}
                    </div>
                    <div class="mb-2">
                        <strong>Email:</strong> {{ $provider->email }}
                    </div>
                    <div class="mb-2">
                        <strong>Téléphone:</strong> {{ $provider->phone }}
                    </div>
                    <div class="mb-2">
                        <strong>Nombre d'évaluations:</strong> {{ $provider->evaluations->count() }}
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
        </div>

        <!-- Latest Prediction Card -->
        <div class="col-lg-4 mb-4 equal-height-col">
            <div class="card shadow mb-4 prediction-card equal-height-card {{ isset($trendInfo['trend']) ? 'trend-'.$trendInfo['trend'] : '' }}">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Prévision actuelle</h6>
                </div>
                <div class="card-body">
                    @if($latestPrediction)
                        <div class="text-center mb-4">
                            <h1 class="display-4 font-weight-bold {{ $latestPrediction->predicted_score >= 75 ? 'text-success' : ($latestPrediction->predicted_score >= 50 ? 'text-warning' : 'text-danger') }}">
                                {{ round($latestPrediction->predicted_score, 1) }}
                            </h1>
                            <div class="text-xs text-muted">Score prédit</div>
                        </div>

                        <div class="mb-2">
                            <strong>Niveau de précision:</strong>
                            <div class="progress mt-1" style="height: 5px;">
                                <div class="progress-bar bg-info" role="progressbar" style="width: {{ $latestPrediction->confidence_level * 100 }}%" aria-valuenow="{{ $latestPrediction->confidence_level * 100 }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="text-xs text-right">{{ round($latestPrediction->confidence_level * 100) }}%</div>
                        </div>

                        <div class="mb-2">
                            <strong>Date de prévision:</strong> {{ $latestPrediction->prediction_date->format('d/m/Y') }}
                        </div>

                        <div class="mb-2">
                            <strong>Période:</strong>
                            {{ $latestPrediction->prediction_period == 'next_month' ? 'Mois prochain' : 'Trimestre prochain' }}
                        </div>

                        <div class="mb-2">
                            <strong>Tendance:</strong>
                            @if(isset($trendInfo['trend']))
                                <span class="badge bg-{{ $trendInfo['status'] }}">
                                    <i class="fas fa-{{ $trendInfo['icon'] }}"></i>
                                    {{ $trendInfo['message'] }}
                                </span>
                            @else
                                <span class="text-muted">Non disponible</span>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-5">
                            @if($provider->evaluations->count() < 5)
                                <div class="text-muted">Nombre d'évaluations insuffisant</div>
                                <div class="small text-muted mt-2">Un minimum de 5 évaluations est requis pour générer une prévision fiable.</div>
                            @else
                                <div class="text-muted">Aucune prévision disponible</div>
                                <a href="{{ route('admin.predictions.generate') }}" class="btn btn-primary mt-3">
                                    Générer une prévision
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Factors Card -->
        <div class="col-lg-4 mb-4 equal-height-col">
            <div class="card shadow mb-4 equal-height-card">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Facteurs d'influence</h6>
                </div>
                <div class="card-body">
                    @if($latestPrediction && isset($latestPrediction->factors))
                        @php
                            $factors = $latestPrediction->factors;
                        @endphp

                        <div class="mb-2">
                            <strong>Pente:</strong>
                            <span class="{{ $factors['slope'] > 0 ? 'text-success' : ($factors['slope'] < 0 ? 'text-danger' : 'text-muted') }}">
                                {{ round($factors['slope'], 3) }}
                            </span>
                        </div>

                        <div class="mb-2">
                            <strong>Force de la tendance:</strong>
                            <div class="progress mt-1" style="height: 5px;">
                                <div class="progress-bar {{ $factors['trend'] == 'improving' ? 'bg-success' : ($factors['trend'] == 'declining' ? 'bg-danger' : 'bg-info') }}" role="progressbar" style="width: {{ min(100, abs($factors['slope']) * 50) }}%" aria-valuenow="{{ min(100, abs($factors['slope']) * 50) }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>

                        <div class="mb-2">
                            <strong>Précision du modèle (R²):</strong>
                            <div class="progress mt-1" style="height: 5px;">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $factors['r_squared'] * 100 }}%" aria-valuenow="{{ $factors['r_squared'] * 100 }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="text-xs text-right">{{ round($factors['r_squared'] * 100, 1) }}%</div>
                        </div>

                        <div class="mb-2">
                            <strong>Dernier score:</strong>
                            <span class="badge bg-{{ $factors['last_score'] >= 75 ? 'success' : ($factors['last_score'] >= 50 ? 'warning' : 'danger') }}">
                                {{ round($factors['last_score'], 1) }}
                            </span>
                        </div>

                        <div class="mb-2">
                            <strong>Score moyen:</strong>
                            <span class="badge bg-{{ $factors['avg_score'] >= 75 ? 'success' : ($factors['avg_score'] >= 50 ? 'warning' : 'danger') }}">
                                {{ round($factors['avg_score'], 1) }}
                            </span>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="text-muted">Aucun facteur disponible</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Chart -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Historique et prévision</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="performanceChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('performanceChart').getContext('2d');

    // Data from backend
    const evaluationDates = @json($evaluationDates);
    const evaluationScores = @json($evaluationScores);
    const predictionDates = @json($predictionDates);
    const predictionScores = @json($predictionScores);
    const predictionConfidence = @json($predictionConfidence);

    @if($regressionData)
    // Regression line data
    const regressionDates = @json($regressionData['dates']);
    const regressionScores = @json($regressionData['scores']);
    @endif

    // Create chart
    const performanceChart = new Chart(ctx, {
        type: 'line',
        data: {
            datasets: [
                {
                    label: 'Évaluations',
                    data: evaluationScores.map((score, index) => ({
                        x: evaluationDates[index],
                        y: score
                    })),
                    borderColor: '#4e73df',
                    backgroundColor: 'rgba(78, 115, 223, 0.1)',
                    pointBackgroundColor: '#4e73df',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: '#4e73df',
                    borderWidth: 3,
                    pointRadius: 5,
                    fill: false
                },
                @if($regressionData)
                {
                    label: 'Ligne de tendance',
                    data: regressionScores.map((score, index) => ({
                        x: regressionDates[index],
                        y: score
                    })),
                    borderColor: 'rgba(78, 115, 223, 0.5)',
                    borderWidth: 2,
                    pointRadius: 0,
                    borderDash: [5, 5],
                    fill: false
                },
                @endif
                {
                    label: 'Prévisions',
                    data: predictionScores.map((score, index) => ({
                        x: predictionDates[index],
                        y: score
                    })),
                    borderColor: '#1cc88a',
                    backgroundColor: 'rgba(28, 200, 138, 0.1)',
                    pointBackgroundColor: '#1cc88a',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: '#1cc88a',
                    borderWidth: 3,
                    pointRadius: 5,
                    fill: false
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            layout: {
                padding: {
                    left: 10,
                    right: 25,
                    top: 25,
                    bottom: 0
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false,
                        drawBorder: false
                    }
                },
                y: {
                    min: 0,
                    max: 100,
                    ticks: {
                        stepSize: 20
                    },
                    grid: {
                        color: "rgb(234, 236, 244)",
                        zeroLineColor: "rgb(234, 236, 244)",
                        drawBorder: false,
                        borderDash: [2],
                        zeroLineBorderDash: [2]
                    }
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyColor: "#858796",
                    titleMarginBottom: 10,
                    titleColor: '#6e707e',
                    titleFontSize: 14,
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: false,
                    intersect: false,
                    mode: 'index',
                    caretPadding: 10
                }
            }
        }
    });
});
</script>
@endsection

