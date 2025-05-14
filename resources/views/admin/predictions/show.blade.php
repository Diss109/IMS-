@extends('layouts.admin')

@section('page_title', 'Détails de la prévision')

@section('styles')
<style>
    .prediction-card {
        border-left: 5px solid #4e73df;
        position: relative;
        overflow: hidden;
    }

    .metric-card {
        height: 100%;
        transition: all 0.3s;
        position: relative;
        overflow: hidden;
    }

    .metric-card:hover {
        transform: translateY(-7px);
        box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.15);
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
        margin-bottom: 1.5rem;
    }

    .equal-height-card {
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .equal-height-card .card-body {
        flex: 1;
    }

    .predicted-score {
        font-size: 4rem;
        line-height: 1;
        font-weight: 700;
        margin-bottom: 0.5rem;
        text-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .predicted-label {
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-weight: 500;
        opacity: 0.7;
    }

    .confidence-bar {
        height: 8px;
        overflow: visible;
        border-radius: 4px;
        background-color: rgba(0, 0, 0, 0.05);
    }

    .confidence-bar .progress-bar {
        position: relative;
        border-radius: 4px;
        box-shadow: 0 0 10px rgba(52, 152, 219, 0.5);
    }

    .confidence-value {
        position: absolute;
        right: 0;
        top: -18px;
        background: rgba(52, 152, 219, 0.9);
        color: white;
        padding: 2px 8px;
        border-radius: 10px;
        font-size: 0.65rem;
        font-weight: 600;
        transform: translateX(50%);
    }

    .info-label {
        font-weight: 600;
        margin-bottom: 0.25rem;
        display: block;
        color: #4e5a68;
    }

    .info-value {
        margin-bottom: 1rem;
        font-size: 0.95rem;
    }

    .factor-icon {
        display: inline-flex;
        justify-content: center;
        align-items: center;
        width: 32px;
        height: 32px;
        background: rgba(0, 0, 0, 0.05);
        border-radius: 50%;
        margin-right: 10px;
    }

    .chart-area {
        position: relative;
        height: 350px;
        margin-top: 1rem;
    }

    .card-animate {
        animation: slideUp 0.5s ease-out forwards;
        opacity: 0;
        transform: translateY(20px);
    }

    @keyframes slideUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-header h6 {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .card-header-icon {
        font-size: 1.1rem;
        opacity: 0.8;
    }

    .trend-indicator {
        display: inline-flex;
        align-items: center;
        padding: 5px 10px;
        border-radius: 20px;
        font-weight: 500;
        font-size: 0.85rem;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
    }

    .trend-indicator i {
        margin-right: 5px;
    }

    .slope-value {
        font-size: 1.1rem;
        font-weight: 600;
    }

    .factor-progress {
        height: 6px;
        margin-top: 8px;
        margin-bottom: 5px;
        border-radius: 3px;
        background-color: rgba(0, 0, 0, 0.05);
    }

    .tooltip-custom {
        position: relative;
        display: inline-block;
    }

    .tooltip-custom .tooltip-text {
        visibility: hidden;
        width: 200px;
        background-color: rgba(0, 0, 0, 0.8);
        color: #fff;
        text-align: center;
        border-radius: 6px;
        padding: 8px;
        position: absolute;
        z-index: 1;
        bottom: 125%;
        left: 50%;
        transform: translateX(-50%);
        opacity: 0;
        transition: opacity 0.3s;
        font-size: 0.75rem;
    }

    .tooltip-custom:hover .tooltip-text {
        visibility: visible;
        opacity: 1;
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
            <i class="fas fa-arrow-left me-1"></i> Retour aux prévisions
        </a>
    </div>

    <div class="row equal-height-row">
        <!-- Provider Info Card -->
        <div class="col-lg-4 equal-height-col">
            <div class="card shadow equal-height-card card-animate" style="animation-delay: 0.1s;">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle card-header-icon me-1"></i> Informations du prestataire
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <span class="info-label">Nom:</span>
                        <div class="info-value">{{ $provider->name }}</div>
                    </div>
                    <div class="mb-3">
                        <span class="info-label">Type:</span>
                        <div class="info-value">{{ \App\Models\ServiceProvider::getTypes()[$provider->service_type] ?? $provider->service_type }}</div>
                    </div>
                    <div class="mb-3">
                        <span class="info-label">Email:</span>
                        <div class="info-value">{{ $provider->email }}</div>
                    </div>
                    <div class="mb-3">
                        <span class="info-label">Téléphone:</span>
                        <div class="info-value">{{ $provider->phone }}</div>
                    </div>
                    <div class="mb-3">
                        <span class="info-label">Nombre d'évaluations:</span>
                        <div class="info-value">
                            <span class="badge bg-secondary">{{ $provider->evaluations->count() }}</span>
                        </div>
                    </div>
                    <div class="mb-2">
                        <span class="info-label">Dernière évaluation:</span>
                        <div class="info-value">
                            @if($provider->evaluations->isNotEmpty())
                                {{ $provider->evaluations->sortByDesc('created_at')->first()->created_at->format('d/m/Y') }}
                            @else
                                Non disponible
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Latest Prediction Card -->
        <div class="col-lg-4 equal-height-col">
            <div class="card shadow prediction-card equal-height-card {{ isset($trendInfo['trend']) ? 'trend-'.$trendInfo['trend'] : '' }} card-animate" style="animation-delay: 0.2s;">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-line card-header-icon me-1"></i> Prévision actuelle
                    </h6>
                </div>
                <div class="card-body">
                    @if($latestPrediction)
                        <div class="text-center mb-4">
                            <div class="predicted-score {{ $latestPrediction->predicted_score >= 75 ? 'text-success' : ($latestPrediction->predicted_score >= 50 ? 'text-warning' : 'text-danger') }}">
                                {{ round($latestPrediction->predicted_score, 1) }}
                            </div>
                            <div class="predicted-label">Score prédit</div>
                        </div>

                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="info-label">Niveau de précision:</span>
                                <div class="tooltip-custom">
                                    <i class="fas fa-info-circle text-muted"></i>
                                    <span class="tooltip-text">Ce score combine la qualité statistique du modèle (R²) et le nombre d'évaluations disponibles.</span>
                                </div>
                            </div>
                            <div class="confidence-bar mt-2">
                                <div class="progress-bar bg-info" role="progressbar" style="width: {{ $latestPrediction->confidence_level * 100 }}%" aria-valuenow="{{ $latestPrediction->confidence_level * 100 }}" aria-valuemin="0" aria-valuemax="100">
                                    <span class="confidence-value">{{ round($latestPrediction->confidence_level * 100) }}%</span>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <span class="info-label">Date de prévision:</span>
                            <div class="info-value">{{ $latestPrediction->prediction_date->format('d/m/Y') }}</div>
                        </div>

                        <div class="mb-3">
                            <span class="info-label">Période:</span>
                            <div class="info-value">{{ $latestPrediction->prediction_period == 'next_month' ? 'Mois prochain' : 'Trimestre prochain' }}</div>
                        </div>

                        <div class="mb-2">
                            <span class="info-label">Tendance:</span>
                            <div class="info-value">
                                @if(isset($trendInfo['trend']))
                                    <span class="trend-indicator bg-{{ $trendInfo['status'] }}">
                                        <i class="fas fa-{{ $trendInfo['icon'] }}"></i>
                                        {{ $trendInfo['message'] }}
                                    </span>
                                @else
                                    <span class="text-muted">Non disponible</span>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            @if($provider->evaluations->count() < 5)
                                <div class="text-muted">Nombre d'évaluations insuffisant</div>
                                <div class="small text-muted mt-2">Un minimum de 5 évaluations est requis pour générer une prévision fiable.</div>
                            @else
                                <div class="text-muted">Aucune prévision disponible</div>
                                <a href="{{ route('admin.predictions.generate') }}" class="btn btn-primary mt-3">
                                    <i class="fas fa-magic me-1"></i> Générer une prévision
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Factors Card -->
        <div class="col-lg-4 equal-height-col">
            <div class="card shadow equal-height-card card-animate" style="animation-delay: 0.3s;">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-cogs card-header-icon me-1"></i> Facteurs d'influence
                    </h6>
                </div>
                <div class="card-body">
                    @if($latestPrediction && isset($latestPrediction->factors))
                        @php
                            $factors = $latestPrediction->factors;
                        @endphp

                        <div class="mb-3">
                            <span class="info-label">
                                <span class="factor-icon">
                                    <i class="fas fa-arrow-trend-{{ $factors['slope'] > 0 ? 'up' : ($factors['slope'] < 0 ? 'down' : 'right') }}"></i>
                                </span>
                                Pente:
                            </span>
                            <div class="info-value">
                                <span class="slope-value {{ $factors['slope'] > 0 ? 'text-success' : ($factors['slope'] < 0 ? 'text-danger' : 'text-muted') }}">
                                    {{ round($factors['slope'], 3) }}
                                </span>
                                <div class="small text-muted">
                                    {{ $factors['slope'] > 0 ? 'Amélioration' : ($factors['slope'] < 0 ? 'Dégradation' : 'Stable') }} par période
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <span class="info-label">
                                <span class="factor-icon">
                                    <i class="fas fa-wave-square"></i>
                                </span>
                                Force de la tendance:
                            </span>
                            <div class="factor-progress">
                                <div class="progress-bar {{ $factors['trend'] == 'improving' ? 'bg-success' : ($factors['trend'] == 'declining' ? 'bg-danger' : 'bg-info') }}" role="progressbar" style="width: {{ min(100, abs($factors['slope']) * 50) }}%" aria-valuenow="{{ min(100, abs($factors['slope']) * 50) }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="text-xs text-right small">
                                {{ $factors['trend'] == 'improving' ? 'Positive' : ($factors['trend'] == 'declining' ? 'Négative' : 'Neutre') }}
                            </div>
                        </div>

                        <div class="mb-3">
                            <span class="info-label">
                                <span class="factor-icon">
                                    <i class="fas fa-bullseye"></i>
                                </span>
                                Précision du modèle (R²):
                            </span>
                            <div class="factor-progress">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $factors['r_squared'] * 100 }}%" aria-valuenow="{{ $factors['r_squared'] * 100 }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="text-xs text-right small">{{ round($factors['r_squared'] * 100, 1) }}%</div>
                        </div>

                        <div class="mb-3">
                            <span class="info-label">
                                <span class="factor-icon">
                                    <i class="fas fa-check-circle"></i>
                                </span>
                                Dernier score:
                            </span>
                            <div class="info-value">
                                <span class="badge bg-{{ $factors['last_score'] >= 75 ? 'success' : ($factors['last_score'] >= 50 ? 'warning' : 'danger') }} badge-pill">
                                    {{ round($factors['last_score'], 1) }}
                                </span>
                            </div>
                        </div>

                        <div class="mb-2">
                            <span class="info-label">
                                <span class="factor-icon">
                                    <i class="fas fa-calculator"></i>
                                </span>
                                Score moyen:
                            </span>
                            <div class="info-value">
                                <span class="badge bg-{{ $factors['avg_score'] >= 75 ? 'success' : ($factors['avg_score'] >= 50 ? 'warning' : 'danger') }} badge-pill">
                                    {{ round($factors['avg_score'], 1) }}
                                </span>
                            </div>
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
            <div class="card shadow mb-4 card-animate" style="animation-delay: 0.4s;">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-area card-header-icon me-1"></i> Historique et prévision
                    </h6>
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
            animation: {
                duration: 2000,
                easing: 'easeOutQuart'
            },
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

