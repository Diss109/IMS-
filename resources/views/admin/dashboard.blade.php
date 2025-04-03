@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">Tableau de bord</h1>

    <!-- Period Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="d-flex gap-3">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="period" id="week" value="week"
                        {{ request('period', 'week') === 'week' ? 'checked' : '' }}>
                    <label class="form-check-label" for="week">Cette semaine</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="period" id="month" value="month"
                        {{ request('period') === 'month' ? 'checked' : '' }}>
                    <label class="form-check-label" for="month">Ce mois</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="period" id="year" value="year"
                        {{ request('period') === 'year' ? 'checked' : '' }}>
                    <label class="form-check-label" for="year">Cette année</label>
                </div>
                <button type="submit" class="btn btn-primary btn-sm">Filtrer</button>
            </form>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total des réclamations</h5>
                    <h2 class="mb-0">{{ $totalCount }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Résolues</h5>
                    <h2 class="mb-0">{{ $solvedCount }} ({{ $solvedPercentage }}%)</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">En attente</h5>
                    <h2 class="mb-0">{{ $waitingCount }} ({{ $waitingPercentage }}%)</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h5 class="card-title">Non résolues</h5>
                    <h2 class="mb-0">{{ $unsolvedCount }} ({{ $unsolvedPercentage }}%)</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Évolution des réclamations</h5>
                    <canvas id="complaintsChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Types de réclamations</h5>
                    <canvas id="typesChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Complaints -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Réclamations récentes</h5>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Entreprise</th>
                            <th>Type</th>
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
                                <td>{{ __("complaints.types.{$complaint->complaint_type}") }}</td>
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
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Debug data
    console.log('Chart Labels:', @json($chartLabels));
    console.log('Chart Data:', @json($chartData));
    console.log('Type Distribution:', @json($typeDistribution));

    // Histogram Chart
    const ctx = document.getElementById('complaintsChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($chartLabels),
                datasets: @json($chartData)
            },
            options: {
                responsive: true,
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
                        position: 'top'
                    }
                }
            }
        });
    } else {
        console.error('Cannot find complaintsChart canvas element');
    }

    // Donut Chart
    const typesCtx = document.getElementById('typesChart');
    if (typesCtx) {
        new Chart(typesCtx, {
            type: 'doughnut',
            data: {
                labels: @json($typeDistribution['labels']),
                datasets: [{
                    data: @json($typeDistribution['data']),
                    backgroundColor: @json($typeDistribution['backgroundColor'])
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    } else {
        console.error('Cannot find typesChart canvas element');
    }

    // Auto-submit form when period changes
    document.querySelectorAll('input[name="period"]').forEach(radio => {
        radio.addEventListener('change', function() {
            this.closest('form').submit();
        });
    });
});
</script>
@endpush
@endsection
