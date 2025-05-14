@extends('layouts.admin')

@section('page_title', 'Prévisions')

@section('styles')
<style>
    .stats-card {
        position: relative;
        overflow: hidden;
        min-height: 110px;
        transition: all 0.3s;
    }

    .stats-card:hover {
        transform: translateY(-5px);
    }

    .stats-card .icon-bg {
        position: absolute;
        right: -15px;
        bottom: -20px;
        font-size: 5rem;
        opacity: 0.1;
        transform: rotate(-15deg);
        transition: all 0.5s;
    }

    .stats-card:hover .icon-bg {
        transform: rotate(0deg) scale(1.1);
        opacity: 0.15;
    }

    .stats-value {
        font-size: 1.8rem;
        font-weight: 700;
        margin-bottom: 0.3rem;
    }

    .stats-label {
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 0.75rem;
        font-weight: 600;
        opacity: 0.8;
    }

    .predictions-table tr {
        transition: all 0.3s;
    }

    .predictions-table tr:hover {
        transform: translateY(-3px);
    }

    .provider-name {
        font-weight: 600;
        color: #4361ee;
    }

    .trend-badge {
        border-radius: 50px;
        padding: 0.35rem 0.8rem;
        font-size: 0.75rem;
        font-weight: 500;
        letter-spacing: 0.3px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .prediction-card {
        border-left: 4px solid #4361ee;
        overflow: hidden;
    }

    .fade-in {
        animation: fadeIn 0.6s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .generation-form {
        background: rgba(255,255,255,0.7);
        backdrop-filter: blur(5px);
        border-radius: 15px;
        padding: 0.5rem;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }

    .prediction-progress {
        height: 5px;
        overflow: visible;
    }

    .prediction-progress .progress-bar {
        position: relative;
        overflow: visible;
        border-radius: 5px;
    }

    .sparkle {
        position: absolute;
        right: 0;
        top: 50%;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: white;
        box-shadow: 0 0 10px rgba(255,255,255,0.8);
        transform: translate(50%, -50%);
        opacity: 0;
        animation: sparkle 2s infinite;
    }

    @keyframes sparkle {
        0% { opacity: 0; }
        50% { opacity: 1; }
        100% { opacity: 0; }
    }

    .badge-pill {
        padding-right: 0.8rem;
        padding-left: 0.8rem;
        border-radius: 50rem;
    }
</style>
@endsection

@section('content')
<div class="container-fluid fade-in">
    <h1 class="h3 mb-4 text-gray-800">Prévisions de Performance</h1>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow stats-card border-left-primary mb-3">
                <div class="card-body py-3">
                    <div class="stats-label text-primary">Prestataires</div>
                    <div class="stats-value text-gray-800">{{ $stats['provider_count'] }}</div>
                    <i class="fas fa-truck icon-bg text-primary"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow stats-card border-left-success mb-3">
                <div class="card-body py-3">
                    <div class="stats-label text-success">Tendance positive</div>
                    <div class="stats-value text-gray-800">{{ $stats['improving_count'] }}</div>
                    <i class="fas fa-chart-line icon-bg text-success"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow stats-card border-left-warning mb-3">
                <div class="card-body py-3">
                    <div class="stats-label text-warning">À risque</div>
                    <div class="stats-value text-gray-800">{{ $stats['high_risk_count'] }}</div>
                    <i class="fas fa-exclamation-triangle icon-bg text-warning"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow stats-card border-left-info mb-3">
                <div class="card-body py-3">
                    <div class="stats-label text-info">Prévisions générées</div>
                    <div class="stats-value text-gray-800">{{ $stats['predicted_count'] }}</div>
                    <i class="fas fa-brain icon-bg text-info"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 mb-4">
            <div class="card shadow mb-4 prediction-card">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-bar me-2"></i>Prestataires et Prévisions
                    </h6>
                    <div class="dropdown no-arrow">
                        <form action="{{ route('admin.predictions.generate') }}" method="POST" class="d-inline generation-form">
                            @csrf
                            <select name="period" class="form-select form-select-sm d-inline-block w-auto">
                                <option value="next_month">Mois prochain</option>
                                <option value="next_quarter">Trimestre prochain</option>
                            </select>
                            <button type="submit" class="btn btn-primary btn-sm ms-2">
                                <i class="fas fa-sync fa-sm me-1"></i> Générer les prévisions
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-sm" style="font-size: 0.85rem;">
                            <thead>
                                <tr>
                                    <th style="width: 18%">Prestataire</th>
                                    <th style="width: 12%">Type</th>
                                    <th style="width: 10%">Évaluations</th>
                                    <th style="width: 15%">Dernière évaluation</th>
                                    <th style="width: 18%">Prévision</th>
                                    <th style="width: 15%">Tendance</th>
                                    <th style="width: 12%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($providers as $provider)
                                <tr>
                                    <td class="align-middle provider-name">{{ $provider->name }}</td>
                                    <td class="align-middle">{{ \App\Models\ServiceProvider::getTypes()[$provider->service_type] ?? $provider->service_type }}</td>
                                    <td class="align-middle text-center">
                                        <span class="badge bg-secondary badge-pill" style="font-size: 0.75rem;">{{ $provider->evaluations_count }}</span>
                                    </td>
                                    <td class="align-middle">
                                        @php
                                            $latestEval = $provider->evaluations()->latest()->first();
                                        @endphp
                                        @if($latestEval)
                                            <span class="badge bg-{{ $latestEval->total_score >= 75 ? 'success' : ($latestEval->total_score >= 50 ? 'warning' : 'danger') }} badge-pill" style="font-size: 0.75rem;">
                                                {{ $latestEval->total_score }}
                                            </span>
                                            <small class="text-muted d-block" style="font-size: 0.7rem;">{{ $latestEval->created_at->format('d/m/Y') }}</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="align-middle">
                                        @if($provider->predictions->isNotEmpty())
                                            <span class="badge bg-{{ $provider->predictions->first()->predicted_score >= 75 ? 'success' : ($provider->predictions->first()->predicted_score >= 50 ? 'warning' : 'danger') }} badge-pill" style="font-size: 0.75rem;">
                                                {{ round($provider->predictions->first()->predicted_score, 1) }}
                                            </span>
                                            <div class="prediction-progress mt-1" style="width: 80px;">
                                                <div class="progress prediction-progress" style="height: 4px;">
                                                    <div class="progress-bar bg-info" role="progressbar"
                                                         style="width: {{ $provider->predictions->first()->confidence_level * 100 }}%"
                                                         aria-valuenow="{{ $provider->predictions->first()->confidence_level * 100 }}"
                                                         aria-valuemin="0" aria-valuemax="100">
                                                        <div class="sparkle"></div>
                                                    </div>
                                                </div>
                                                <small class="text-muted" style="font-size: 0.7rem;">Précision: {{ round($provider->predictions->first()->confidence_level * 100) }}%</small>
                                            </div>
                                        @else
                                            @if($provider->evaluations_count < 5)
                                                <small class="text-muted fst-italic" style="font-size: 0.7rem;">Nombre d'évaluations insuffisant</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        @endif
                                    </td>
                                    <td class="align-middle">
                                        @if(isset($provider->trend) && $provider->trend['has_trend'])
                                            <span class="badge trend-badge bg-{{ $provider->trend['status'] }}" style="font-size: 0.7rem;">
                                                <i class="fas fa-{{ $provider->trend['icon'] }}"></i>
                                                {{ $provider->trend['message'] }}
                                            </span>
                                        @else
                                            <span class="text-muted" style="font-size: 0.75rem;">Données insuffisantes</span>
                                        @endif
                                    </td>
                                    <td class="p-1 text-center">
                                        @if($provider->evaluations_count >= 5)
                                            <a href="{{ route('admin.predictions.show', $provider->id) }}" class="btn btn-xs btn-info p-1" style="font-size: 0.8rem;">
                                                <i class="fas fa-eye"></i> Détails
                                            </a>
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
                            <button class="btn btn-secondary" disabled>
                                <i class="fas fa-chevron-left me-1"></i>Précédent
                            </button>
                        @else
                            <a class="btn btn-primary" href="{{ $providers->previousPageUrl() }}">
                                <i class="fas fa-chevron-left me-1"></i>Précédent
                            </a>
                        @endif
                        <span class="align-self-center">Page {{ $providers->currentPage() }} / {{ $providers->lastPage() }}</span>
                        @if ($providers->hasMorePages())
                            <a class="btn btn-primary" href="{{ $providers->nextPageUrl() }}">
                                Suivant<i class="fas fa-chevron-right ms-1"></i>
                            </a>
                        @else
                            <button class="btn btn-secondary" disabled>
                                Suivant<i class="fas fa-chevron-right ms-1"></i>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add animation to table rows on page load
    const rows = document.querySelectorAll('.predictions-table tbody tr');
    rows.forEach((row, index) => {
        setTimeout(() => {
            row.classList.add('fade-in');
        }, index * 50);
    });
});
</script>
@endsection
