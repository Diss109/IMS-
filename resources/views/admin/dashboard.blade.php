@extends('layouts.admin')

@section('page_title', 'Tableau de bord')

@section('content')

    <div class="container-fluid">
        <!-- Date Filter Bar -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Filtres de date</h6>
                    </div>
                    <div class="card-body">
                        <!-- Period Filters -->
                        <div class="mb-3">
                            <label class="form-label"><strong>Période prédéfinie:</strong></label>
                            <div class="d-flex flex-wrap" style="gap: 10px;">
                                <a href="{{ route('admin.dashboard', ['period' => 'week']) }}"
                                   class="btn btn-outline-primary">
                                   <i class="fas fa-calendar-week"></i> Cette semaine
                                </a>
                                <a href="{{ route('admin.dashboard', ['period' => 'month']) }}"
                                   class="btn btn-outline-primary">
                                   <i class="fas fa-calendar-alt"></i> Ce mois
                                </a>
                                <a href="{{ route('admin.dashboard', ['period' => 'total']) }}"
                                   class="btn btn-outline-primary">
                                   <i class="fas fa-infinity"></i> Tout
                                </a>
                            </div>
                        </div>

                        <hr>

                        <!-- Custom Date Range -->
                        <form action="{{ route('admin.dashboard') }}" method="GET">
                            <div class="mb-3">
                                <label class="form-label"><strong>Période personnalisée:</strong></label>
                                <div class="row">
                                    <div class="col-md-4 mb-2">
                                        <label for="start_date">Date de début</label>
                                        <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date') }}">
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <label for="end_date">Date de fin</label>
                                        <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date') }}">
                                    </div>
                                    <div class="col-md-4 d-flex align-items-end mb-2">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-filter"></i> Appliquer
                                        </button>
                                        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary ms-2">
                                            <i class="fas fa-undo"></i> Réinitialiser
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Tableau de Bord Administrateur</h1>
        </div>

        <!-- Overview Statistics Section -->
        <div class="mb-4">
            <h5 class="mb-3 text-gray-800">Vue d'Ensemble</h5>
            <div class="row">
                <!-- Complaints Statistics Cards -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Réclamations</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statistics['total'] }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Service Providers Card -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Prestataires de Service</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $providerStats['total'] }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-truck fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Evaluations Card -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Évaluations</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $evaluationStats['total'] }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-star fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Users Card -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Utilisateurs</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $userStats['total'] }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-users fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Complaint Statistics Row -->
        <div class="mb-4">
            <h5 class="mb-3 text-gray-800">Réclamations</h5>
            <div class="row">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Résolues</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statistics['resolved'] }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">En Attente</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statistics['waiting'] }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-clock fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-danger shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Non Résolues</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statistics['unresolved'] }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Complaints Resolution Rate Card -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Taux de Résolution</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statistics['resolved_percentage'] }}%</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-percentage fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row: Complaints and Providers -->
        <div class="row">
            <!-- Complaint Trend Chart -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Tendance des Réclamations</h6>
                    </div>
                    <div class="card-body">
                        <iframe src="{{ route('admin.kpis.charts.trend', request()->query->all()) }}"
                                frameborder="0" width="100%" height="300"></iframe>
                    </div>
                </div>
            </div>

            <!-- Provider Types Chart -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Types de Prestataires</h6>
                    </div>
                    <div class="card-body">
                        <iframe src="{{ route('admin.kpis.charts.provider_types', request()->query->all()) }}"
                                frameborder="0" width="100%" height="300"></iframe>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row: Evaluations and Users -->
        <div class="row">
            <!-- Evaluations Trend Chart -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Tendance des Évaluations</h6>
                    </div>
                    <div class="card-body">
                        <iframe src="{{ route('admin.kpis.charts.evaluations_trend', request()->query->all()) }}"
                                frameborder="0" width="100%" height="300"></iframe>
                    </div>
                </div>
            </div>

            <!-- User Roles Chart -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Rôles Utilisateurs</h6>
                    </div>
                    <div class="card-body">
                        <iframe src="{{ route('admin.kpis.charts.user_roles', request()->query->all()) }}"
                                frameborder="0" width="100%" height="300"></iframe>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Data Sections Row -->
        <div class="row">
            <!-- Recent Complaints Table -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Réclamations récentes</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Entreprise</th>
                                        <th>Type</th>
                                        <th>Statut</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentComplaints as $complaint)
                                        <tr>
                                            <td>{{ $complaint->id }}</td>
                                            <td>{{ $complaint->company_name }}</td>
                                            <td>
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
                                            </td>
                                            <td>
                                                @if($complaint->status === 'en_attente')
                                                    <span class="badge bg-warning">En attente</span>
                                                @elseif($complaint->status === 'résolu')
                                                    <span class="badge bg-success">Résolu</span>
                                                @else
                                                    <span class="badge bg-danger">Non résolu</span>
                                                @endif
                                            </td>
                                            <td>{{ $complaint->created_at->format('d/m/Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @if($recentComplaints->isEmpty())
                                <div class="text-center py-4 text-muted">
                                    <i class="fas fa-inbox fa-1x mb-2"></i>
                                    <p>Aucune réclamation trouvée</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Evaluations Table -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Évaluations récentes</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Prestataire</th>
                                        <th>Évaluateur</th>
                                        <th>Score</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentEvaluations as $evaluation)
                                        <tr>
                                            <td>{{ $evaluation->id }}</td>
                                            <td>{{ $evaluation->serviceProvider->name }}</td>
                                            <td>{{ $evaluation->user->name }}</td>
                                            <td>
                                                @php
                                                    $avgScore = $evaluation->scores->avg('score') ?? 0;
                                                @endphp
                                                {{ number_format($avgScore, 1) }}/5
                                            </td>
                                            <td>{{ $evaluation->created_at->format('d/m/Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @if($recentEvaluations->isEmpty())
                                <div class="text-center py-4 text-muted">
                                    <i class="fas fa-star fa-1x mb-2"></i>
                                    <p>Aucune évaluation trouvée</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Service Providers -->
        <div class="row">
            <div class="col-lg-12 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Prestataires les Mieux Notés</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($topProviders as $provider)
                                <div class="col-lg-4 col-md-6 mb-4">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $provider->name }}</h5>
                                            <p class="card-text">
                                                <strong>Type:</strong>
                                                @switch($provider->service_type)
                                                    @case('armateur')
                                                        Armateur
                                                        @break
                                                    @case('compagnie_aerienne')
                                                        Compagnie Aérienne
                                                        @break
                                                    @case('transporteur_routier_int')
                                                        Transporteur Routier International
                                                        @break
                                                    @case('transporteur_terrestre_local')
                                                        Transporteur Terrestre Local
                                                        @break
                                                    @case('agent')
                                                        Agent
                                                        @break
                                                    @case('magasin')
                                                        Magasin
                                                        @break
                                                    @default
                                                        Autre
                                                @endswitch
                                            </p>
                                            <p class="card-text">
                                                <strong>Score moyen:</strong> {{ $provider->average_score }}/5
                                                <div class="progress mt-2">
                                                    <div class="progress-bar bg-success" role="progressbar"
                                                        style="width: {{ ($provider->average_score / 5) * 100 }}%"
                                                        aria-valuenow="{{ $provider->average_score }}"
                                                        aria-valuemin="0" aria-valuemax="5">
                                                    </div>
                                                </div>
                                            </p>
                                            <p class="card-text"><strong>Évaluations:</strong> {{ $provider->evaluations_count }}</p>
                                        </div>
                                        <div class="card-footer">
                                            <a href="{{ route('admin.service-providers.show', $provider->id) }}"
                                               class="btn btn-sm btn-primary">
                                                Voir détails
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            @if($topProviders->isEmpty())
                                <div class="col-12 text-center py-4 text-muted">
                                    <i class="fas fa-award fa-1x mb-2"></i>
                                    <p>Aucun prestataire évalué trouvé</p>
                                </div>
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
    // Date filter functionality
    const dateFilterForm = document.getElementById('dateFilterForm');
    const periodInput = document.getElementById('periodInput');
    const periodButtons = document.querySelectorAll('[data-period]');
    const customDateToggle = document.getElementById('customDateToggle');
    const customDateInputs = document.querySelectorAll('.custom-date-inputs');
    const startDateInput = document.getElementById('startDate');
    const endDateInput = document.getElementById('endDate');

    // Set default dates if not already set
    if (!startDateInput.value) {
        const oneWeekAgo = new Date();
        oneWeekAgo.setDate(oneWeekAgo.getDate() - 7);
        startDateInput.value = oneWeekAgo.toISOString().substr(0, 10);
    }

    if (!endDateInput.value) {
        const today = new Date();
        endDateInput.value = today.toISOString().substr(0, 10);
    }
});
</script>
@endsection
