@extends('layouts.admin')

@section('page_title', 'Tableau de bord KPI')

@section('content')
<div class="container-fluid pt-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tableau de bord KPI</h1>
        <button id="print-btn" class="btn btn-primary btn-sm">
            <i class="fas fa-print me-2"></i>Imprimer
        </button>
    </div>

    <!-- KPI Cards -->
    <div class="row mb-4">
        <!-- Total Complaints -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Réclamations Totales</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ \App\Models\Complaint::count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resolution Rate -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Taux de Résolution</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                @php
                                    $totalComplaints = \App\Models\Complaint::count();
                                    $resolvedComplaints = \App\Models\Complaint::where('status', 'résolu')->count();
                                    $resolutionRate = $totalComplaints > 0 ? round(($resolvedComplaints / $totalComplaints) * 100) : 0;
                                    echo $resolutionRate . '%';
                                @endphp
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Complaints -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                En Attente</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ \App\Models\Complaint::where('status', 'en_attente')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Critical Complaints -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Réclamations Critiques</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ \App\Models\Complaint::where('urgency_level', 'critical')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row">
        <!-- Line Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Taux de réclamations</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area" style="height: 300px; width: 100%;">
                        <canvas id="myAreaChart" style="height: 300px; width: 100%;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pie Chart -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Types de réclamations</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie" style="height: 300px; width: 100%;">
                        <canvas id="myPieChart" style="height: 300px; width: 100%;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bar charts row -->
    <div class="row">
        <!-- Bar Chart -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Statut des réclamations</h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar" style="height: 300px; width: 100%;">
                        <canvas id="myBarChart" style="height: 300px; width: 100%;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Doughnut Chart -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Niveau d'urgence</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie" style="height: 300px; width: 100%;">
                        <canvas id="myDoughnutChart" style="height: 300px; width: 100%;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Chart.js with explicit loading -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.bundle.min.js"></script>

<script>
// Set new default font family and font color
Chart.defaults.global.defaultFontFamily = '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = '#858796';

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing charts');

    // Prepare PHP data for charts
    @php
        // Monthly trend data
        $months = [];
        $complaintsData = [];
        $resolvedData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M');

            $startOfMonth = $date->copy()->startOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();

            $complaintsData[] = \App\Models\Complaint::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
            $resolvedData[] = \App\Models\Complaint::where('status', 'résolu')
                ->whereBetween('updated_at', [$startOfMonth, $endOfMonth])->count();
        }

        // Type distribution
        $types = ['Retard livraison', 'Retard chargement', 'Marchandise endommagée', 'Mauvais comportement', 'Autre'];
        $typeData = [
            \App\Models\Complaint::where('complaint_type', 'retard_livraison')->count(),
            \App\Models\Complaint::where('complaint_type', 'retard_chargement')->count(),
            \App\Models\Complaint::where('complaint_type', 'marchandise_endommagée')->count(),
            \App\Models\Complaint::where('complaint_type', 'mauvais_comportement')->count(),
            \App\Models\Complaint::where('complaint_type', 'autre')->count(),
        ];

        // Status distribution
        $statusLabels = ['En attente', 'Résolu', 'Non résolu'];
        $statusData = [
            \App\Models\Complaint::where('status', 'en_attente')->count(),
            \App\Models\Complaint::where('status', 'résolu')->count(),
            \App\Models\Complaint::where('status', 'non_résolu')->count()
        ];

        // Urgency distribution
        $urgencyLabels = ['Critique', 'Élevée', 'Moyenne', 'Faible'];
        $urgencyData = [
            \App\Models\Complaint::where('urgency_level', 'critical')->count(),
            \App\Models\Complaint::where('urgency_level', 'high')->count(),
            \App\Models\Complaint::where('urgency_level', 'medium')->count(),
            \App\Models\Complaint::where('urgency_level', 'low')->count()
        ];
    @endphp

    // Area Chart - Monthly Trend
    var ctx = document.getElementById("myAreaChart");
    if (ctx) {
        console.log('Initializing Area Chart');
        var myLineChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($months),
                datasets: [{
                    label: "Nouvelles réclamations",
                    lineTension: 0.3,
                    backgroundColor: "rgba(78, 115, 223, 0.05)",
                    borderColor: "rgba(78, 115, 223, 1)",
                    pointRadius: 3,
                    pointBackgroundColor: "rgba(78, 115, 223, 1)",
                    pointBorderColor: "rgba(78, 115, 223, 1)",
                    pointHoverRadius: 3,
                    pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                    pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    data: @json($complaintsData),
                },
                {
                    label: "Réclamations résolues",
                    lineTension: 0.3,
                    backgroundColor: "rgba(28, 200, 138, 0.05)",
                    borderColor: "rgba(28, 200, 138, 1)",
                    pointRadius: 3,
                    pointBackgroundColor: "rgba(28, 200, 138, 1)",
                    pointBorderColor: "rgba(28, 200, 138, 1)",
                    pointHoverRadius: 3,
                    pointHoverBackgroundColor: "rgba(28, 200, 138, 1)",
                    pointHoverBorderColor: "rgba(28, 200, 138, 1)",
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    data: @json($resolvedData),
                }],
            },
            options: {
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
                    xAxes: [{
                        time: {
                            unit: 'date'
                        },
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
                            maxTicksLimit: 5,
                            padding: 10,
                            beginAtZero: true
                        },
                        gridLines: {
                            color: "rgb(234, 236, 244)",
                            zeroLineColor: "rgb(234, 236, 244)",
                            drawBorder: false,
                            borderDash: [2],
                            zeroLineBorderDash: [2]
                        }
                    }],
                },
                legend: {
                    display: true
                },
                tooltips: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyFontColor: "#858796",
                    titleMarginBottom: 10,
                    titleFontColor: '#6e707e',
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
        });
    } else {
        console.error('Area chart canvas not found!');
    }

    // Pie Chart - Complaint Types
    var pieCtx = document.getElementById("myPieChart");
    if (pieCtx) {
        console.log('Initializing Pie Chart');
        var myPieChart = new Chart(pieCtx, {
            type: 'pie',
            data: {
                labels: @json($types),
                datasets: [{
                    data: @json($typeData),
                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
                    hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf', '#dda20a', '#be2617'],
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }],
            },
            options: {
                maintainAspectRatio: false,
                tooltips: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyFontColor: "#858796",
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: false,
                    caretPadding: 10,
                },
                legend: {
                    display: true,
                    position: 'bottom'
                },
                cutoutPercentage: 0,
            },
        });
    } else {
        console.error('Pie chart canvas not found!');
    }

    // Bar Chart - Status Distribution
    var barCtx = document.getElementById("myBarChart");
    if (barCtx) {
        console.log('Initializing Bar Chart');
        var myBarChart = new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: @json($statusLabels),
                datasets: [{
                    label: "Nombre",
                    backgroundColor: ["#f6c23e", "#1cc88a", "#e74a3b"],
                    hoverBackgroundColor: ["#dda20a", "#17a673", "#be2617"],
                    borderColor: "#4e73df",
                    data: @json($statusData),
                }],
            },
            options: {
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
                    xAxes: [{
                        gridLines: {
                            display: false,
                            drawBorder: false
                        }
                    }],
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }],
                },
                legend: {
                    display: false
                }
            }
        });
    } else {
        console.error('Bar chart canvas not found!');
    }

    // Doughnut Chart - Urgency
    var doughnutCtx = document.getElementById("myDoughnutChart");
    if (doughnutCtx) {
        console.log('Initializing Doughnut Chart');
        var myDoughnutChart = new Chart(doughnutCtx, {
            type: 'doughnut',
            data: {
                labels: @json($urgencyLabels),
                datasets: [{
                    data: @json($urgencyData),
                    backgroundColor: ['#e74a3b', '#f6c23e', '#4e73df', '#1cc88a'],
                    hoverBackgroundColor: ['#be2617', '#dda20a', '#2e59d9', '#17a673'],
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }],
            },
            options: {
                maintainAspectRatio: false,
                tooltips: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyFontColor: "#858796",
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: false,
                    caretPadding: 10,
                },
                legend: {
                    display: true,
                    position: 'bottom'
                },
                cutoutPercentage: 70,
            },
        });
    } else {
        console.error('Doughnut chart canvas not found!');
    }

    // Print functionality
    document.getElementById('print-btn').addEventListener('click', function() {
        window.print();
    });
});
</script>

<style>
/* Custom chart styles */
.chart-area {
    position: relative;
    height: 20rem;
    width: 100%;
}

.chart-bar {
    position: relative;
    height: 20rem;
    width: 100%;
}

.chart-pie {
    position: relative;
    height: 20rem;
    width: 100%;
}

/* Card styles */
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}

.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}

.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}

.border-left-danger {
    border-left: 0.25rem solid #e74a3b !important;
}

/* Print specific styles */
@media print {
    .card {
        break-inside: avoid;
    }
}
</style>
@endsection
