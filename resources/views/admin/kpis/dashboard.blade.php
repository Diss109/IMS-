@extends('layouts.admin')

@section('page_title', 'Tableau de bord KPI')

@section('content')
<div class="container-fluid">
    <!-- Filter Bar -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <form id="kpi-filter-form" class="row align-items-end">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label for="date-range" class="form-label">Période d'analyse</label>
                            <select class="form-select shadow-none" id="date-range">
                                <option value="7">7 derniers jours</option>
                                <option value="30" selected>30 derniers jours</option>
                                <option value="90">90 derniers jours</option>
                                <option value="365">Année complète</option>
                            </select>
                        </div>
                        <div class="col-md-6 d-flex align-items-end justify-content-md-end mt-3 mt-md-0">
                            <button type="button" id="print-btn" class="btn btn-primary">
                                <i class="fas fa-print me-2"></i>Imprimer le rapport
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="row mb-4">
        <div class="col-md-3 mb-4 mb-md-0">
            <div class="card border-0 shadow-sm h-100 bg-gradient-primary text-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-1 opacity-75">Réclamations totales</h6>
                            <h2 class="mb-0 fw-bold">{{ \App\Models\Complaint::count() }}</h2>
                        </div>
                        <div class="rounded-circle bg-white bg-opacity-25 p-3 d-flex align-items-center justify-content-center" style="height: 50px; width: 50px;">
                            <i class="fas fa-clipboard-list fs-4 text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4 mb-md-0">
            <div class="card border-0 shadow-sm h-100 bg-gradient-success text-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-1 opacity-75">Taux de résolution</h6>
                            @php
                                $totalCount = \App\Models\Complaint::count();
                                $resolvedCount = \App\Models\Complaint::where('status', 'résolu')->count();
                                $resolutionRate = $totalCount > 0 ? round(($resolvedCount / $totalCount) * 100) : 0;
                            @endphp
                            <h2 class="mb-0 fw-bold">{{ $resolutionRate }}%</h2>
                        </div>
                        <div class="rounded-circle bg-white bg-opacity-25 p-3 d-flex align-items-center justify-content-center" style="height: 50px; width: 50px;">
                            <i class="fas fa-check-circle fs-4 text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4 mb-md-0">
            <div class="card border-0 shadow-sm h-100 bg-gradient-warning text-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-1 opacity-75">En attente</h6>
                            <h2 class="mb-0 fw-bold">{{ \App\Models\Complaint::where('status', 'en_attente')->count() }}</h2>
                        </div>
                        <div class="rounded-circle bg-white bg-opacity-25 p-3 d-flex align-items-center justify-content-center" style="height: 50px; width: 50px;">
                            <i class="fas fa-clock fs-4 text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4 mb-md-0">
            <div class="card border-0 shadow-sm h-100 bg-gradient-info text-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-1 opacity-75">Réclamations critiques</h6>
                            <h2 class="mb-0 fw-bold">{{ \App\Models\Complaint::where('urgency_level', 'critical')->count() }}</h2>
                        </div>
                        <div class="rounded-circle bg-white bg-opacity-25 p-3 d-flex align-items-center justify-content-center" style="height: 50px; width: 50px;">
                            <i class="fas fa-exclamation-triangle fs-4 text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Row 1 -->
    <div class="row mb-4">
        <div class="col-lg-8 mb-4 mb-lg-0">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">Tendance des réclamations</h5>
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-outline-secondary active">Mensuel</button>
                        <button type="button" class="btn btn-outline-secondary">Hebdomadaire</button>
                    </div>
                </div>
                <div class="card-body">
                    <div style="height: 300px; position: relative;">
                        <canvas id="complaints-trend-chart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Distribution par type</h5>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <div style="height: 300px; width: 100%; position: relative;">
                        <canvas id="complaints-type-chart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Row 2 -->
    <div class="row mb-4">
        <div class="col-lg-6 mb-4 mb-lg-0">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Statut des réclamations</h5>
                </div>
                <div class="card-body">
                    <div style="height: 250px; position: relative;">
                        <canvas id="status-chart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Distribution par niveau d'urgence</h5>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <div style="height: 250px; width: 100%; position: relative;">
                        <canvas id="urgency-chart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Complaints -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">Réclamations récentes</h5>
                    <a href="{{ route('admin.complaints.index') }}" class="btn btn-sm btn-primary">
                        Voir toutes
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover m-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="py-3">ID</th>
                                    <th class="py-3">Client</th>
                                    <th class="py-3">Type</th>
                                    <th class="py-3">Urgence</th>
                                    <th class="py-3">Statut</th>
                                    <th class="py-3">Date</th>
                                    <th class="py-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $recentComplaints = \App\Models\Complaint::orderBy('created_at', 'desc')->take(5)->get();
                                @endphp

                                @foreach($recentComplaints as $complaint)
                                <tr>
                                    <td class="py-3">#{{ $complaint->id }}</td>
                                    <td class="py-3">{{ $complaint->first_name }} {{ $complaint->last_name }}</td>
                                    <td class="py-3">
                                        @switch($complaint->complaint_type)
                                            @case('retard_livraison')
                                                <span class="badge bg-info text-white">Retard livraison</span>
                                                @break
                                            @case('retard_chargement')
                                                <span class="badge bg-primary text-white">Retard chargement</span>
                                                @break
                                            @case('marchandise_endommagée')
                                                <span class="badge bg-danger text-white">Marchandise endommagée</span>
                                                @break
                                            @case('mauvais_comportement')
                                                <span class="badge bg-warning text-dark">Mauvais comportement</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary text-white">Autre</span>
                                        @endswitch
                                    </td>
                                    <td class="py-3">
                                        @switch($complaint->urgency_level)
                                            @case('critical')
                                                <span class="badge bg-danger text-white">Critique</span>
                                                @break
                                            @case('high')
                                                <span class="badge bg-warning text-dark">Élevée</span>
                                                @break
                                            @case('medium')
                                                <span class="badge bg-primary text-white">Moyenne</span>
                                                @break
                                            @default
                                                <span class="badge bg-success text-white">Faible</span>
                                        @endswitch
                                    </td>
                                    <td class="py-3">
                                        @switch($complaint->status)
                                            @case('en_attente')
                                                <span class="badge bg-warning text-dark">En attente</span>
                                                @break
                                            @case('résolu')
                                                <span class="badge bg-success text-white">Résolu</span>
                                                @break
                                            @default
                                                <span class="badge bg-danger text-white">Non résolu</span>
                                        @endswitch
                                    </td>
                                    <td class="py-3">{{ $complaint->created_at->format('d/m/Y') }}</td>
                                    <td class="py-3">
                                        <a href="{{ route('admin.complaints.show', $complaint->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach

                                @if($recentComplaints->isEmpty())
                                <tr>
                                    <td colspan="7" class="text-center py-4">Aucune réclamation récente</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Load FontAwesome if not already loaded -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<!-- Load Chart.js library first, ensure it's the latest version -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

<script>
    // Make sure Chart.js is properly loaded before doing anything
    console.log('Chart.js script loaded, initializing KPI dashboard...');
    
    document.addEventListener('DOMContentLoaded', function() {
        // Check if Chart.js is available
        if (typeof Chart === 'undefined') {
            console.error('Chart.js is not loaded properly! Loading alternative version...');
            let script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js';
            script.onload = initializeCharts;
            document.head.appendChild(script);
        } else {
            console.log('Chart.js found, proceeding with initialization...');
            setTimeout(initializeCharts, 500); // Delay to ensure DOM is ready
        }
    });
    
    function initializeCharts() {
        console.log('Initializing charts with data...');
        
        // Prepare data from PHP
        @php
            // Get trend data (last 12 months)
            $trendLabels = [];
            $newComplaints = [];
            $resolvedComplaints = [];
            
            for ($i = 11; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $trendLabels[] = $date->format('M Y');
                
                $startOfMonth = $date->copy()->startOfMonth();
                $endOfMonth = $date->copy()->endOfMonth();
                
                $newComplaints[] = \App\Models\Complaint::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
                $resolvedComplaints[] = \App\Models\Complaint::where('status', 'résolu')->whereBetween('updated_at', [$startOfMonth, $endOfMonth])->count();
            }
            
            // Get complaint types distribution
            $typeLabels = ['Retard livraison', 'Retard chargement', 'Marchandise endommagée', 'Mauvais comportement', 'Autre'];
            $typeCounts = [];
            
            $typeCounts[] = \App\Models\Complaint::where('complaint_type', 'retard_livraison')->count();
            $typeCounts[] = \App\Models\Complaint::where('complaint_type', 'retard_chargement')->count();
            $typeCounts[] = \App\Models\Complaint::where('complaint_type', 'marchandise_endommagée')->count();
            $typeCounts[] = \App\Models\Complaint::where('complaint_type', 'mauvais_comportement')->count();
            $typeCounts[] = \App\Models\Complaint::where('complaint_type', 'autre')->count();
            
            // Get status distribution
            $statusLabels = ['En attente', 'Résolu', 'Non résolu'];
            $statusCounts = [];
            
            $statusCounts[] = \App\Models\Complaint::where('status', 'en_attente')->count();
            $statusCounts[] = \App\Models\Complaint::where('status', 'résolu')->count();
            $statusCounts[] = \App\Models\Complaint::where('status', 'non_résolu')->count();
            
            // Get urgency distribution
            $urgencyLabels = ['Critique', 'Élevée', 'Moyenne', 'Faible'];
            $urgencyCounts = [];
            
            $urgencyCounts[] = \App\Models\Complaint::where('urgency_level', 'critical')->count();
            $urgencyCounts[] = \App\Models\Complaint::where('urgency_level', 'high')->count();
            $urgencyCounts[] = \App\Models\Complaint::where('urgency_level', 'medium')->count();
            $urgencyCounts[] = \App\Models\Complaint::where('urgency_level', 'low')->count();
        @endphp
        
        // Create complaints trend chart
        const trendCtx = document.getElementById('complaints-trend-chart');
        if (trendCtx) {
            console.log('Creating trend chart...');
            new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: @json($trendLabels),
                    datasets: [
                        {
                            label: 'Nouvelles Réclamations',
                            data: @json($newComplaints),
                            borderColor: '#4776E6',
                            backgroundColor: 'rgba(71, 118, 230, 0.1)',
                            tension: 0.4,
                            fill: true
                        },
                        {
                            label: 'Réclamations Résolues',
                            data: @json($resolvedComplaints),
                            borderColor: '#28a745',
                            backgroundColor: 'rgba(40, 167, 69, 0.1)',
                            tension: 0.4,
                            fill: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                padding: 15
                            }
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            },
                            grid: {
                                color: 'rgba(0,0,0,0.05)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }
        
        // Create complaint type distribution chart
        const typeCtx = document.getElementById('complaints-type-chart');
        if (typeCtx) {
            console.log('Creating type distribution chart...');
            new Chart(typeCtx, {
                type: 'doughnut',
                data: {
                    labels: @json($typeLabels),
                    datasets: [{
                        data: @json($typeCounts),
                        backgroundColor: [
                            'rgba(54, 162, 235, 0.8)',
                            'rgba(75, 192, 192, 0.8)',
                            'rgba(255, 99, 132, 0.8)',
                            'rgba(255, 159, 64, 0.8)',
                            'rgba(153, 102, 255, 0.8)'
                        ],
                        borderWidth: 1,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '60%',
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                usePointStyle: true,
                                padding: 15
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = Math.round((value / total) * 100);
                                    return `${label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }
        
        // Create status distribution chart
        const statusCtx = document.getElementById('status-chart');
        if (statusCtx) {
            console.log('Creating status chart...');
            new Chart(statusCtx, {
                type: 'bar',
                data: {
                    labels: @json($statusLabels),
                    datasets: [{
                        label: 'Nombre de réclamations',
                        data: @json($statusCounts),
                        backgroundColor: [
                            '#FF9800',  // En attente - Orange
                            '#4CAF50',  // Résolu - Green
                            '#F44336'   // Non résolu - Red
                        ],
                        borderRadius: 6,
                        borderWidth: 0,
                        maxBarThickness: 60
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }
        
        // Create urgency distribution chart
        const urgencyCtx = document.getElementById('urgency-chart');
        if (urgencyCtx) {
            console.log('Creating urgency chart...');
            new Chart(urgencyCtx, {
                type: 'pie',
                data: {
                    labels: @json($urgencyLabels),
                    datasets: [{
                        data: @json($urgencyCounts),
                        backgroundColor: [
                            '#F44336',  // Critique - Red
                            '#FF9800',  // Élevée - Orange 
                            '#4776E6',  // Moyenne - Blue
                            '#4CAF50'   // Faible - Green
                        ],
                        borderWidth: 1,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                usePointStyle: true,
                                padding: 15
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = Math.round((value / total) * 100);
                                    return `${label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }
        
        // Set up event listeners
        setupEventListeners();
    }
    
    function setupEventListeners() {
        // Date range change
        const dateRangeSelect = document.getElementById('date-range');
        if (dateRangeSelect) {
            dateRangeSelect.addEventListener('change', function() {
                const selectedDays = this.value;
                console.log(`Date range changed to ${selectedDays} days`);
                
                // Show notification of change
                const toast = document.createElement('div');
                toast.className = 'position-fixed bottom-0 end-0 p-3';
                toast.style.zIndex = 1050;
                toast.innerHTML = `
                    <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="toast-header">
                            <strong class="me-auto">Période mise à jour</strong>
                            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                        <div class="toast-body">
                            Affichage des données pour les derniers ${selectedDays} jours
                        </div>
                    </div>
                `;
                document.body.appendChild(toast);
                
                setTimeout(() => {
                    document.body.removeChild(toast);
                }, 3000);
                
                // In a production environment, this would trigger an AJAX call
                // to refresh the data for the selected period
            });
        }
        
        // Print button
        const printBtn = document.getElementById('print-btn');
        if (printBtn) {
            printBtn.addEventListener('click', function() {
                console.log('Print button clicked');
                
                // Add a temporary class for print styling
                document.body.classList.add('printing-kpi-report');
                
                // Use the browser's print functionality
                window.print();
                
                // Remove the print class after printing dialog is closed
                setTimeout(() => {
                    document.body.classList.remove('printing-kpi-report');
                }, 1000);
            });
        }
    }
</script>
<style>
    .card {
        transition: all 0.2s ease;
        border-radius: 0.5rem;
        overflow: hidden;
    }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
    .bg-gradient-primary {
        background: linear-gradient(45deg, #4776E6, #8E54E9);
    }
    .bg-gradient-success {
        background: linear-gradient(45deg, #28a745, #20c997);
    }
    .bg-gradient-warning {
        background: linear-gradient(45deg, #FF9800, #F44336);
    }
    .bg-gradient-info {
        background: linear-gradient(45deg, #17a2b8, #009688);
    }
    canvas {
        width: 100% !important;
        height: 100% !important;
    }
    .table {
        margin-bottom: 0;
    }
    .table th, .table td {
        vertical-align: middle;
    }
    
    #export-btn:hover {
        background-color: #f8f9fa;
    }
</style>
@endsection
