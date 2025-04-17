@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tableau de bord</h1>
        <img src="{{ asset('images/logo.jpg') }}" alt="Tuniship Logo" height="80" class="d-none d-md-block">
    </div>

    <!-- Statistics Cards -->
    <div class="row">
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
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Filtres</h6>
        </div>
        <div class="card-body">
            <form id="filterForm" class="row">
                <div class="col-md-2 mb-3">
                    <label for="period">Période</label>
                    <select class="form-control" id="period" name="period">
                        <option value="total">Total</option>
                        <option value="week">Cette semaine</option>
                        <option value="month">Ce mois</option>
                        <option value="year">Cette année</option>
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label for="type">Type</label>
                    <select class="form-control" id="type" name="type">
                        <option value="">Tous</option>
                        @foreach($complaintTypes as $type)
                            <option value="{{ $type }}">
                                @switch($type)
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
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label for="urgency">Urgence</label>
                    <select class="form-control" id="urgency" name="urgency">
                        <option value="">Toutes</option>
                        @foreach($urgencyLevels as $level)
                            <option value="{{ $level }}">
                                @switch($level)
                                    @case('critique')
                                        Critique
                                        @break
                                    @case('élevé')
                                        Élevé
                                        @break
                                    @case('moyen')
                                        Moyen
                                        @break
                                    @default
                                        Faible
                                @endswitch
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label for="status">Statut</label>
                    <select class="form-control" id="status" name="status">
                        <option value="">Tous</option>
                        <option value="résolu">Résolu</option>
                        <option value="en_attente">En attente</option>
                        <option value="non_résolu">Non résolu</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label>Date</label>
                    <input type="date" class="form-control" id="date" name="date">
                </div>
                <div class="col-md-1 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">Filtrer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Recent Complaints Table -->
    <div class="card shadow mb-4">
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
                            <th>Urgence</th>
                            <th>Statut</th>
                            <th>Date</th>
                            <th>Actions</th>
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
                                    @switch($complaint->urgency_level)
                                        @case('critique')
                                            <span class="badge bg-dark">Critique</span>
                                            @break
                                        @case('élevé')
                                            <span class="badge bg-danger">Élevé</span>
                                            @break
                                        @case('moyen')
                                            <span class="badge bg-warning">Moyen</span>
                                            @break
                                        @default
                                            <span class="badge bg-success">Faible</span>
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
                                <td>{{ $complaint->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('admin.complaints.show', $complaint) }}"
                                       class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @if($recentComplaints->isEmpty())
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-inbox fa-3x mb-3"></i>
                        <p>Aucune réclamation trouvée</p>
                    </div>
                @endif
            </div>
            {{ $recentComplaints->links() }}
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <!-- Evolution Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Évolution des Réclamations</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="evolutionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Types Distribution Chart -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Types de Réclamations</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie">
                        <canvas id="typesChart"></canvas>
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
    // Debug logging
    console.log('Chart data:', @json($chartData));
    console.log('Types distribution:', @json($typeDistribution));

    try {
        // Evolution Chart
        const evolutionCtx = document.getElementById('evolutionChart');
        if (evolutionCtx) {
            new Chart(evolutionCtx, {
                type: 'line',
                data: {
                    labels: @json($chartData['labels']),
                    datasets: @json($chartData['datasets'])
                },
                options: {
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom'
                        }
                    }
                }
            });
        } else {
            console.error('Evolution chart canvas not found');
        }

        // Types Distribution Chart
        const typesCtx = document.getElementById('typesChart');
        if (typesCtx) {
            new Chart(typesCtx, {
                type: 'doughnut',
                data: {
                    labels: @json($typeDistribution['labels']),
                    datasets: [{
                        data: @json($typeDistribution['data']),
                        backgroundColor: [
                            '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'
                        ]
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom'
                        }
                    }
                }
            });
        } else {
            console.error('Types chart canvas not found');
        }
    } catch (error) {
        console.error('Error initializing charts:', error);
    }

    // Filter form submission
    document.getElementById('filterForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const queryString = new URLSearchParams(formData).toString();
        window.location.href = `${window.location.pathname}?${queryString}`;
    });
});
</script>
@endsection
