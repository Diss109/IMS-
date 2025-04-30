@extends('layouts.admin')

@section('page_title', 'Tableau de bord KPI')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tableau de bord KPI</h1>
        <button id="print-btn" class="btn btn-primary">
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

    <!-- Static Charts Row 1 -->
    <div class="row">
        <!-- Trend Chart (Static HTML version) -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tendance des réclamations</h6>
                </div>
                <div class="card-body">
                    <div class="static-chart" style="height: 300px;">
                        @php
                            // Get trend data (last 6 months)
                            $months = [];
                            $newComplaints = [];
                            
                            for ($i = 5; $i >= 0; $i--) {
                                $date = now()->subMonths($i);
                                $months[] = $date->format('M Y');
                                
                                $startOfMonth = $date->copy()->startOfMonth();
                                $endOfMonth = $date->copy()->endOfMonth();
                                
                                $newComplaints[] = \App\Models\Complaint::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
                            }
                            
                            // Find max value for scaling
                            $maxValue = max($newComplaints) > 0 ? max($newComplaints) : 10;
                        @endphp
                        
                        <div class="d-flex flex-column h-100">
                            <div class="d-flex align-items-end h-75 w-100 mb-2">
                                @foreach($newComplaints as $index => $count)
                                    <div class="mx-auto" style="width: calc(100% / {{ count($newComplaints) }});">
                                        <div class="bg-primary" style="height: {{ ($count / $maxValue) * 100 }}%; width: 50%; margin: 0 auto;"></div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="d-flex align-items-center h-25 w-100">
                                @foreach($months as $month)
                                    <div class="text-center" style="width: calc(100% / {{ count($months) }});">
                                        <small>{{ $month }}</small>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Type Distribution (Static HTML version) -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Types de réclamations</h6>
                </div>
                <div class="card-body">
                    <div class="static-chart" style="height: 300px;">
                        @php
                            // Type distribution data
                            $types = [
                                'Retard livraison' => \App\Models\Complaint::where('complaint_type', 'retard_livraison')->count(),
                                'Retard chargement' => \App\Models\Complaint::where('complaint_type', 'retard_chargement')->count(),
                                'Marchandise endommagée' => \App\Models\Complaint::where('complaint_type', 'marchandise_endommagée')->count(),
                                'Mauvais comportement' => \App\Models\Complaint::where('complaint_type', 'mauvais_comportement')->count(),
                                'Autre' => \App\Models\Complaint::where('complaint_type', 'autre')->count()
                            ];
                            
                            // Colors for chart
                            $colors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'];
                            
                            // Total for percentage calculation
                            $total = array_sum($types);
                        @endphp
                        
                        <div class="d-flex flex-column h-100">
                            <div class="table-responsive">
                                <table class="table">
                                    <tbody>
                                        @foreach($types as $type => $count)
                                            <tr>
                                                <td width="20">
                                                    <div style="width: 15px; height: 15px; background-color: {{ $colors[array_search($type, array_keys($types))] }}"></div>
                                                </td>
                                                <td>{{ $type }}</td>
                                                <td width="50">{{ $count }}</td>
                                                <td width="100">
                                                    <div class="progress">
                                                        <div class="progress-bar" role="progressbar" 
                                                            style="width: {{ $total > 0 ? ($count / $total) * 100 : 0 }}%; background-color: {{ $colors[array_search($type, array_keys($types))] }}" 
                                                            aria-valuenow="{{ $total > 0 ? ($count / $total) * 100 : 0 }}" aria-valuemin="0" aria-valuemax="100">
                                                            {{ $total > 0 ? round(($count / $total) * 100) : 0 }}%
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Static Charts Row 2 -->
    <div class="row">
        <!-- Status Distribution (Static HTML version) -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Statut des réclamations</h6>
                </div>
                <div class="card-body">
                    <div class="static-chart" style="height: 300px;">
                        @php
                            // Status distribution data
                            $statuses = [
                                'En attente' => \App\Models\Complaint::where('status', 'en_attente')->count(),
                                'Résolu' => \App\Models\Complaint::where('status', 'résolu')->count(),
                                'Non résolu' => \App\Models\Complaint::where('status', 'non_résolu')->count()
                            ];
                            
                            // Colors for status
                            $statusColors = ['#f6c23e', '#1cc88a', '#e74a3b'];
                            
                            // Find max value for scaling
                            $maxStatus = max($statuses) > 0 ? max($statuses) : 10;
                        @endphp
                        
                        <div class="d-flex flex-column h-100">
                            <div class="d-flex align-items-end h-75 w-100 mb-2">
                                @foreach($statuses as $status => $count)
                                    <div class="mx-2" style="width: calc(100% / {{ count($statuses) }} - 20px);">
                                        <div class="text-center mb-2">{{ $count }}</div>
                                        <div style="height: {{ ($count / $maxStatus) * 100 }}%; width: 75%; margin: 0 auto; background-color: {{ $statusColors[array_search($status, array_keys($statuses))] }};"></div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="d-flex align-items-center h-25 w-100">
                                @foreach($statuses as $status => $count)
                                    <div class="text-center" style="width: calc(100% / {{ count($statuses) }});">
                                        <small>{{ $status }}</small>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Urgency Distribution (Static HTML version) -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Niveau d'urgence</h6>
                </div>
                <div class="card-body">
                    <div class="static-chart" style="height: 300px;">
                        @php
                            // Urgency distribution data
                            $urgencies = [
                                'Critique' => \App\Models\Complaint::where('urgency_level', 'critical')->count(),
                                'Élevée' => \App\Models\Complaint::where('urgency_level', 'high')->count(),
                                'Moyenne' => \App\Models\Complaint::where('urgency_level', 'medium')->count(),
                                'Faible' => \App\Models\Complaint::where('urgency_level', 'low')->count()
                            ];
                            
                            // Colors for urgency
                            $urgencyColors = ['#e74a3b', '#f6c23e', '#4e73df', '#1cc88a'];
                            
                            // Total for percentage calculation
                            $totalUrgency = array_sum($urgencies);
                        @endphp
                        
                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                    @foreach($urgencies as $urgency => $count)
                                        <tr>
                                            <td width="20">
                                                <div style="width: 15px; height: 15px; background-color: {{ $urgencyColors[array_search($urgency, array_keys($urgencies))] }}"></div>
                                            </td>
                                            <td>{{ $urgency }}</td>
                                            <td width="50">{{ $count }}</td>
                                            <td width="100">
                                                <div class="progress">
                                                    <div class="progress-bar" role="progressbar" 
                                                        style="width: {{ $totalUrgency > 0 ? ($count / $totalUrgency) * 100 : 0 }}%; background-color: {{ $urgencyColors[array_search($urgency, array_keys($urgencies))] }}" 
                                                        aria-valuenow="{{ $totalUrgency > 0 ? ($count / $totalUrgency) * 100 : 0 }}" aria-valuemin="0" aria-valuemax="100">
                                                        {{ $totalUrgency > 0 ? round(($count / $totalUrgency) * 100) : 0 }}%
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom CSS for KPI dashboard */
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

/* Print styles */
@media print {
    .sidebar, .navbar, footer, .card-header {
        display: none !important;
    }
    
    .card {
        break-inside: avoid;
        border: 1px solid #ddd !important;
        margin-bottom: 20px !important;
    }
    
    .container-fluid {
        width: 100% !important;
        padding: 0 !important;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Print functionality
    document.getElementById('print-btn').addEventListener('click', function() {
        window.print();
    });
});
</script>
@endsection
