@extends('layouts.admin')

@section('page_title', 'Tableau de bord')

@section('content')

    <div class="container-fluid">
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
                                    @if($complaint->urgency_level === 'high')
    <span class="badge bg-danger">Élevé</span>
@elseif($complaint->urgency_level === 'medium')
    <span class="badge bg-warning">Moyen</span>
@elseif($complaint->urgency_level === 'critical')
    <span class="badge bg-dark">Critique</span>
@else
    <span class="badge bg-success">Faible</span>
@endif
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
                        <i class="fas fa-inbox fa-1x mb-2"></i>
                        <p>Aucune réclamation trouvée</p>
                    </div>
                @endif
            </div>

        </div>
    </div>

    </div>


@endsection

<style>
.pagination .page-link,
.pagination .page-link *,
.pagination .page-link span,
.pagination .page-link a {
    font-size: 1rem !important;
    width: auto !important;
    height: auto !important;
    line-height: 1.5 !important;
    vertical-align: middle !important;
}
</style>

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
