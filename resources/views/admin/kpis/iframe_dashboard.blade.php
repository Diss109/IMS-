@extends('layouts.admin')

@section('page_title', 'Tableau de bord KPI')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div><!-- Empty div to maintain flex structure --></div>
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
                                RÉCLAMATIONS TOTALES</div>
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
                                TAUX DE RÉSOLUTION</div>
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
                                EN ATTENTE</div>
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
                                RÉCLAMATIONS CRITIQUES</div>
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

    <!-- Charts Row 1 -->
    <div class="row">
        <!-- Trend Chart -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tendance des réclamations</h6>
                </div>
                <div class="card-body">
                    <iframe src="{{ route('admin.kpis.charts.trend') }}" style="width: 100%; height: 300px; border: none;"></iframe>
                </div>
            </div>
        </div>

        <!-- Type Distribution -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Types de réclamations</h6>
                </div>
                <div class="card-body">
                    <iframe src="{{ route('admin.kpis.charts.type') }}" style="width: 100%; height: 300px; border: none;"></iframe>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 2 -->
    <div class="row">
        <!-- Status Chart -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Statut des réclamations</h6>
                </div>
                <div class="card-body">
                    <iframe src="{{ route('admin.kpis.charts.status') }}" style="width: 100%; height: 300px; border: none;"></iframe>
                </div>
            </div>
        </div>

        <!-- Urgency Chart -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Niveau d'urgence</h6>
                </div>
                <div class="card-body">
                    <iframe src="{{ route('admin.kpis.charts.urgency') }}" style="width: 100%; height: 300px; border: none;"></iframe>
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
