@extends('layouts.admin')

@section('page_title', 'Statistiques')

@section('content')
<div class="container-fluid py-4">
    <!-- Print-only header -->
    <div class="print-header" style="display: none;">
        <h1>Statistiques - IMS</h1>
        <p>Rapport généré le {{ date('d/m/Y') }} à {{ date('H:i') }}</p>
        @if(request('period') == 'week')
            <p>Période: Cette semaine</p>
        @elseif(request('period') == 'month')
            <p>Période: Ce mois</p>
        @elseif(request('start_date') && request('end_date'))
            <p>Période: Du {{ date('d/m/Y', strtotime(request('start_date'))) }} au {{ date('d/m/Y', strtotime(request('end_date'))) }}</p>
        @else
            <p>Période: Toutes les données</p>
        @endif
    </div>

    <!-- Date Filter Bar -->
    <div class="card shadow mb-4 date-filter-card">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filtres de date</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="d-flex flex-wrap" style="gap: 10px;">
                        <a href="{{ route('admin.kpis.index', ['period' => 'week']) }}" class="btn btn-primary {{ request('period') == 'week' ? 'active' : '' }}">
                            <i class="fas fa-calendar-week me-1"></i> Cette semaine
                        </a>
                        <a href="{{ route('admin.kpis.index', ['period' => 'month']) }}" class="btn btn-primary {{ request('period') == 'month' ? 'active' : '' }}">
                            <i class="fas fa-calendar-alt me-1"></i> Ce mois
                        </a>
                        <a href="{{ route('admin.kpis.index') }}" class="btn btn-primary {{ !request('period') ? 'active' : '' }}">
                            <i class="fas fa-infinity me-1"></i> Tout
                        </a>
                    </div>
                </div>
                <div class="col-md-6">
                    <form action="{{ route('admin.kpis.index') }}" method="GET" class="d-flex align-items-end">
                        <div class="me-2">
                            <label for="start_date" class="form-label">Du</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date') }}">
                        </div>
                        <div class="me-2">
                            <label for="end_date" class="form-label">Au</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date') }}">
                        </div>
                        <div>
                            <button type="submit" class="btn btn-success">Filtrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $filteredData['total'] ?? \App\Models\Complaint::count() }}</div>
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
                                {{ isset($filteredData['resolution_rate']) ? $filteredData['resolution_rate'] . '%' : '0%' }}
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
                                {{ $filteredData['pending'] ?? \App\Models\Complaint::where('status', 'en_attente')->count() }}
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
                                {{ $filteredData['critical'] ?? \App\Models\Complaint::where('urgency_level', 'critical')->count() }}
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
                    <h6 class="m-0 font-weight-bold text-primary">Nombre de réclamations</h6>
                </div>
                <div class="card-body">
                    <iframe src="{{ route('admin.kpis.charts.trend', request()->only(['period', 'start_date', 'end_date'])) }}" style="width: 100%; height: 300px; border: none;"></iframe>
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
                    <iframe src="{{ route('admin.kpis.charts.type', request()->only(['period', 'start_date', 'end_date'])) }}" style="width: 100%; height: 300px; border: none;"></iframe>
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
                    <iframe src="{{ route('admin.kpis.charts.status', request()->only(['period', 'start_date', 'end_date'])) }}" style="width: 100%; height: 300px; border: none;"></iframe>
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
                    <iframe src="{{ route('admin.kpis.charts.urgency', request()->only(['period', 'start_date', 'end_date'])) }}" style="width: 100%; height: 300px; border: none;"></iframe>
                </div>
            </div>
        </div>
    </div>

    <!-- Print-only footer -->
    <div class="print-footer" style="display: none;">
        <p>IMS - Système de Gestion des Réclamations</p>
        <p>Document confidentiel à usage interne uniquement</p>
        <p>Page 1</p>
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
    /* Hide non-printable elements */
    .sidebar, .navbar, footer, #accordionSidebar, .sticky-footer,
    .scroll-to-top, #print-btn, .d-none-print, .card-header,
    .dropdown, .topbar, .date-filter-card {
        display: none !important;
    }

    /* General styles for print */
    body {
        font-size: 12pt;
        color: #000;
        background: #fff;
        margin: 0;
        padding: 0;
        width: 100%;
    }

    .container-fluid {
        width: 100% !important;
        padding: 0.5cm !important;
        margin: 0 !important;
    }

    /* Add page break to avoid content splitting */
    .card {
        break-inside: avoid;
        border: 1px solid #ddd !important;
        margin-bottom: 20px !important;
        box-shadow: none !important;
        page-break-inside: avoid;
    }

    /* Charts and tables */
    .chart-container {
        width: 100% !important;
        height: auto !important;
        page-break-inside: avoid;
    }

    table {
        border-collapse: collapse;
        width: 100%;
        page-break-inside: avoid;
    }

    table th, table td {
        border: 1px solid #ddd;
        padding: 8px;
    }

    /* Print header styles */
    .print-header {
        display: block !important;
        text-align: center;
        margin-bottom: 20px;
        border-bottom: 1px solid #ddd;
        padding-bottom: 10px;
    }

    .print-header h1 {
        font-size: 18pt;
        font-weight: bold;
        margin-bottom: 5px;
    }

    .print-header p {
        font-size: 10pt;
        color: #666;
    }

    /* Print footer */
    .print-footer {
        display: block !important;
        text-align: center;
        margin-top: 20px;
        font-size: 9pt;
        color: #666;
        border-top: 1px solid #ddd;
        padding-top: 10px;
    }

    /* Badge colors for print */
    .badge {
        border: 1px solid #000 !important;
        color: #000 !important;
        background-color: transparent !important;
        padding: 3px 6px !important;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Enhanced print functionality
    document.getElementById('print-btn').addEventListener('click', function() {
        // Prepare for printing
        prepareForPrint();

        // Small delay to ensure all charts are rendered properly
        setTimeout(function() {
            window.print();

            // Restore normal view after print dialog closes
            setTimeout(function() {
                restoreAfterPrint();
            }, 1000);
        }, 500);
    });

    // Function to prepare the dashboard for printing
    function prepareForPrint() {
        // Capture current date/time for the report
        const now = new Date();
        const dateStr = now.toLocaleDateString('fr-FR');
        const timeStr = now.toLocaleTimeString('fr-FR', {hour: '2-digit', minute:'2-digit'});

        // Set period text based on filters
        let periodText = 'Toutes les données';
        if (document.querySelector('a[href*="period=week"].active')) {
            periodText = 'Cette semaine';
        } else if (document.querySelector('a[href*="period=month"].active')) {
            periodText = 'Ce mois';
        } else if (document.getElementById('start_date').value && document.getElementById('end_date').value) {
            const startDate = new Date(document.getElementById('start_date').value);
            const endDate = new Date(document.getElementById('end_date').value);
            periodText = `Du ${startDate.toLocaleDateString('fr-FR')} au ${endDate.toLocaleDateString('fr-FR')}`;
        }

        // Update the print header with current information
        const periodElem = document.querySelector('.print-header p:nth-child(3)');
        if (periodElem) {
            periodElem.textContent = `Période: ${periodText}`;
        }

        // Ensure all charts are properly sized for printing
        const charts = document.querySelectorAll('canvas');
        charts.forEach(chart => {
            chart.style.maxHeight = '300px';
        });
    }

    // Function to restore the dashboard after printing
    function restoreAfterPrint() {
        // Reset any changes made for printing if needed
        const charts = document.querySelectorAll('canvas');
        charts.forEach(chart => {
            chart.style.maxHeight = '';
        });
    }
});
</script>
@endsection
