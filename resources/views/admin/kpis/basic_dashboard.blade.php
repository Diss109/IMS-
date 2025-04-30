@extends('layouts.admin')

@section('page_title', '')

@section('content')
<div class="container-fluid py-4">

    <!-- Charts Row 1 -->
    <div class="row">
        <!-- Line Chart -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tendance des réclamations</h6>
                </div>
                <div class="card-body">
                    <div style="height: 370px;">
                        <canvas id="lineChart" width="100%" height="100%"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pie Chart -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Types de réclamations</h6>
                </div>
                <div class="card-body">
                    <div style="height: 370px;">
                        <canvas id="pieChart" width="100%" height="100%"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 2 -->
    <div class="row">
        <!-- Bar Chart -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Statut des réclamations</h6>
                </div>
                <div class="card-body">
                    <div style="height: 370px;">
                        <canvas id="barChart" width="100%" height="100%"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Doughnut Chart -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Niveaux d'urgence</h6>
                </div>
                <div class="card-body">
                    <div style="height: 370px;">
                        <canvas id="doughnutChart" width="100%" height="100%"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add plain styles for better print support -->
<style>
.border-left-primary {
    border-left: 4px solid #4e73df;
}
.border-left-success {
    border-left: 4px solid #1cc88a;
}
.border-left-warning {
    border-left: 4px solid #f6c23e;
}
.border-left-danger {
    border-left: 4px solid #e74a3b;
}
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>

<script>
// Remove the duplicate title
document.addEventListener('DOMContentLoaded', function() {
    // Look for all h1 elements with 'text-gray-800' class that contain 'Tableau de bord KPI'
    const titles = document.querySelectorAll('h1.text-gray-800, h1.mb-0');
    titles.forEach(title => {
        if (title.textContent.includes('Tableau de bord KPI')) {
            title.style.display = 'none';
        }
    });
});
</script>

<script>
// Wait for the DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing charts');
    
    // Prepare data for charts
    @php
        // Monthly trend data (simplified)
        $months = [];
        $newData = [];
        $resolvedData = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M Y');
            
            $newData[] = \App\Models\Complaint::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->count();
                
            $resolvedData[] = \App\Models\Complaint::where('status', 'résolu')
                ->whereMonth('updated_at', $date->month)
                ->whereYear('updated_at', $date->year)
                ->count();
        }
        
        // Type distribution data
        $typeLabels = ['Retard livraison', 'Retard chargement', 'Marchandise endommagée', 'Mauvais comportement', 'Autre'];
        $typeData = [
            \App\Models\Complaint::where('complaint_type', 'retard_livraison')->count(),
            \App\Models\Complaint::where('complaint_type', 'retard_chargement')->count(),
            \App\Models\Complaint::where('complaint_type', 'marchandise_endommagée')->count(),
            \App\Models\Complaint::where('complaint_type', 'mauvais_comportement')->count(),
            \App\Models\Complaint::where('complaint_type', 'autre')->count()
        ];
        
        // Status data
        $statusLabels = ['En attente', 'Résolu', 'Non résolu'];
        $statusData = [
            \App\Models\Complaint::where('status', 'en_attente')->count(),
            \App\Models\Complaint::where('status', 'résolu')->count(),
            \App\Models\Complaint::where('status', 'non_résolu')->count()
        ];
        
        // Urgency data
        $urgencyLabels = ['Critique', 'Élevée', 'Moyenne', 'Faible'];
        $urgencyData = [
            \App\Models\Complaint::where('urgency_level', 'critical')->count(),
            \App\Models\Complaint::where('urgency_level', 'high')->count(),
            \App\Models\Complaint::where('urgency_level', 'medium')->count(),
            \App\Models\Complaint::where('urgency_level', 'low')->count()
        ];
    @endphp

    // Line Chart - Trends
    var lineCtx = document.getElementById('lineChart').getContext('2d');
    if (lineCtx) {
        console.log('Creating line chart');
        var lineChart = new Chart(lineCtx, {
            type: 'line',
            data: {
                labels: @json($months),
                datasets: [
                    {
                        label: 'Nouvelles réclamations',
                        data: @json($newData),
                        backgroundColor: 'rgba(78, 115, 223, 0.05)',
                        borderColor: 'rgba(78, 115, 223, 1)',
                        pointRadius: 3,
                        pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                        pointBorderColor: 'rgba(78, 115, 223, 1)',
                        pointHoverRadius: 3,
                        pointHoverBackgroundColor: 'rgba(78, 115, 223, 1)',
                        pointHoverBorderColor: 'rgba(78, 115, 223, 1)',
                        pointHitRadius: 10,
                        pointBorderWidth: 2,
                        fill: false
                    },
                    {
                        label: 'Réclamations résolues',
                        data: @json($resolvedData),
                        backgroundColor: 'rgba(28, 200, 138, 0.05)',
                        borderColor: 'rgba(28, 200, 138, 1)',
                        pointRadius: 3,
                        pointBackgroundColor: 'rgba(28, 200, 138, 1)',
                        pointBorderColor: 'rgba(28, 200, 138, 1)',
                        pointHoverRadius: 3,
                        pointHoverBackgroundColor: 'rgba(28, 200, 138, 1)',
                        pointHoverBorderColor: 'rgba(28, 200, 138, 1)',
                        pointHitRadius: 10,
                        pointBorderWidth: 2,
                        fill: false
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    xAxes: [{
                        gridLines: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            maxTicksLimit: 7
                        }
                    }],
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                },
                legend: {
                    display: true,
                    position: 'top'
                }
            }
        });
    } else {
        console.error('Line chart canvas not found');
    }

    // Pie Chart - Types
    var pieCtx = document.getElementById('pieChart').getContext('2d');
    if (pieCtx) {
        console.log('Creating pie chart');
        var pieChart = new Chart(pieCtx, {
            type: 'pie',
            data: {
                labels: @json($typeLabels),
                datasets: [{
                    data: @json($typeData),
                    backgroundColor: [
                        '#4e73df',
                        '#1cc88a',
                        '#36b9cc',
                        '#f6c23e',
                        '#e74a3b'
                    ],
                    hoverBackgroundColor: [
                        '#2e59d9',
                        '#17a673',
                        '#2c9faf',
                        '#dda20a',
                        '#be2617'
                    ],
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: {
                    display: true,
                    position: 'bottom'
                }
            }
        });
    } else {
        console.error('Pie chart canvas not found');
    }

    // Bar Chart - Status
    var barCtx = document.getElementById('barChart').getContext('2d');
    if (barCtx) {
        console.log('Creating bar chart');
        var barChart = new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: @json($statusLabels),
                datasets: [{
                    label: 'Nombre',
                    data: @json($statusData),
                    backgroundColor: [
                        '#f6c23e',
                        '#1cc88a',
                        '#e74a3b'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                },
                legend: {
                    display: false
                }
            }
        });
    } else {
        console.error('Bar chart canvas not found');
    }

    // Doughnut Chart - Urgency
    var doughnutCtx = document.getElementById('doughnutChart').getContext('2d');
    if (doughnutCtx) {
        console.log('Creating doughnut chart');
        var doughnutChart = new Chart(doughnutCtx, {
            type: 'doughnut',
            data: {
                labels: @json($urgencyLabels),
                datasets: [{
                    data: @json($urgencyData),
                    backgroundColor: [
                        '#e74a3b',
                        '#f6c23e',
                        '#4e73df',
                        '#1cc88a'
                    ],
                    hoverBackgroundColor: [
                        '#be2617',
                        '#dda20a',
                        '#2e59d9',
                        '#17a673'
                    ],
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutoutPercentage: 50,
                legend: {
                    display: true,
                    position: 'bottom'
                }
            }
        });
    } else {
        console.error('Doughnut chart canvas not found');
    }
});
</script>
@endsection
