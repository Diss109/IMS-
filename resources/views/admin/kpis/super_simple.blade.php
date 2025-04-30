@extends('layouts.admin')

@section('page_title', 'Tableau de bord KPI')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Tableau de bord KPI</h6>
                    <button onclick="window.print()" class="btn btn-sm btn-primary">
                        <i class="fas fa-print"></i> Imprimer
                    </button>
                </div>
                <div class="card-body">
                    <!-- Summary Cards as a Table -->
                    <table class="table table-bordered mb-5">
                        <thead class="bg-light">
                            <tr>
                                <th>Réclamations Totales</th>
                                <th>Taux de Résolution</th>
                                <th>En Attente</th>
                                <th>Réclamations Critiques</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center">
                                    <h4>{{ \App\Models\Complaint::count() }}</h4>
                                </td>
                                <td class="text-center">
                                    <h4>
                                        @php
                                            $totalComplaints = \App\Models\Complaint::count();
                                            $resolvedComplaints = \App\Models\Complaint::where('status', 'résolu')->count();
                                            $resolutionRate = $totalComplaints > 0 ? round(($resolvedComplaints / $totalComplaints) * 100) : 0;
                                            echo $resolutionRate . '%';
                                        @endphp
                                    </h4>
                                </td>
                                <td class="text-center">
                                    <h4>{{ \App\Models\Complaint::where('status', 'en_attente')->count() }}</h4>
                                </td>
                                <td class="text-center">
                                    <h4>{{ \App\Models\Complaint::where('urgency_level', 'critical')->count() }}</h4>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Complaint Type Distribution -->
                    <h5 class="mb-3">Types de réclamations</h5>
                    <table class="table table-bordered mb-5">
                        <thead class="bg-light">
                            <tr>
                                <th>Type</th>
                                <th>Nombre</th>
                                <th width="50%">Pourcentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $types = [
                                    'Retard livraison' => \App\Models\Complaint::where('complaint_type', 'retard_livraison')->count(),
                                    'Retard chargement' => \App\Models\Complaint::where('complaint_type', 'retard_chargement')->count(),
                                    'Marchandise endommagée' => \App\Models\Complaint::where('complaint_type', 'marchandise_endommagée')->count(),
                                    'Mauvais comportement' => \App\Models\Complaint::where('complaint_type', 'mauvais_comportement')->count(),
                                    'Autre' => \App\Models\Complaint::where('complaint_type', 'autre')->count()
                                ];
                                $total = array_sum($types);
                                $colors = ['primary', 'success', 'info', 'warning', 'danger'];
                            @endphp

                            @foreach($types as $type => $count)
                                <tr>
                                    <td>{{ $type }}</td>
                                    <td>{{ $count }}</td>
                                    <td>
                                        <div class="progress">
                                            <div class="progress-bar bg-{{ $colors[array_search($type, array_keys($types))] }}" 
                                                 role="progressbar" 
                                                 style="width: {{ $total > 0 ? ($count / $total) * 100 : 0 }}%" 
                                                 aria-valuenow="{{ $total > 0 ? ($count / $total) * 100 : 0 }}" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100">
                                                {{ $total > 0 ? round(($count / $total) * 100) : 0 }}%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Complaint Status -->
                    <h5 class="mb-3">Statut des réclamations</h5>
                    <table class="table table-bordered mb-5">
                        <thead class="bg-light">
                            <tr>
                                <th>Statut</th>
                                <th>Nombre</th>
                                <th width="50%">Pourcentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $statuses = [
                                    'En attente' => \App\Models\Complaint::where('status', 'en_attente')->count(),
                                    'Résolu' => \App\Models\Complaint::where('status', 'résolu')->count(),
                                    'Non résolu' => \App\Models\Complaint::where('status', 'non_résolu')->count()
                                ];
                                $totalStatuses = array_sum($statuses);
                                $statusColors = ['warning', 'success', 'danger'];
                            @endphp

                            @foreach($statuses as $status => $count)
                                <tr>
                                    <td>{{ $status }}</td>
                                    <td>{{ $count }}</td>
                                    <td>
                                        <div class="progress">
                                            <div class="progress-bar bg-{{ $statusColors[array_search($status, array_keys($statuses))] }}" 
                                                 role="progressbar" 
                                                 style="width: {{ $totalStatuses > 0 ? ($count / $totalStatuses) * 100 : 0 }}%" 
                                                 aria-valuenow="{{ $totalStatuses > 0 ? ($count / $totalStatuses) * 100 : 0 }}" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100">
                                                {{ $totalStatuses > 0 ? round(($count / $totalStatuses) * 100) : 0 }}%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Urgency Levels -->
                    <h5 class="mb-3">Niveau d'urgence</h5>
                    <table class="table table-bordered mb-5">
                        <thead class="bg-light">
                            <tr>
                                <th>Niveau</th>
                                <th>Nombre</th>
                                <th width="50%">Pourcentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $urgencies = [
                                    'Critique' => \App\Models\Complaint::where('urgency_level', 'critical')->count(),
                                    'Élevée' => \App\Models\Complaint::where('urgency_level', 'high')->count(),
                                    'Moyenne' => \App\Models\Complaint::where('urgency_level', 'medium')->count(),
                                    'Faible' => \App\Models\Complaint::where('urgency_level', 'low')->count()
                                ];
                                $totalUrgencies = array_sum($urgencies);
                                $urgencyColors = ['danger', 'warning', 'primary', 'success'];
                            @endphp

                            @foreach($urgencies as $urgency => $count)
                                <tr>
                                    <td>{{ $urgency }}</td>
                                    <td>{{ $count }}</td>
                                    <td>
                                        <div class="progress">
                                            <div class="progress-bar bg-{{ $urgencyColors[array_search($urgency, array_keys($urgencies))] }}" 
                                                 role="progressbar" 
                                                 style="width: {{ $totalUrgencies > 0 ? ($count / $totalUrgencies) * 100 : 0 }}%" 
                                                 aria-valuenow="{{ $totalUrgencies > 0 ? ($count / $totalUrgencies) * 100 : 0 }}" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100">
                                                {{ $totalUrgencies > 0 ? round(($count / $totalUrgencies) * 100) : 0 }}%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Monthly Trend Table -->
                    <h5 class="mb-3">Tendance mensuelle des réclamations</h5>
                    <table class="table table-bordered">
                        <thead class="bg-light">
                            <tr>
                                @php
                                    $months = [];
                                    for ($i = 5; $i >= 0; $i--) {
                                        $months[] = now()->subMonths($i)->format('M Y');
                                    }
                                @endphp
                                
                                <th>Métrique</th>
                                @foreach($months as $month)
                                    <th>{{ $month }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Nouvelles réclamations</strong></td>
                                @php
                                    for ($i = 5; $i >= 0; $i--) {
                                        $date = now()->subMonths($i);
                                        $startOfMonth = $date->copy()->startOfMonth();
                                        $endOfMonth = $date->copy()->endOfMonth();
                                        
                                        $count = \App\Models\Complaint::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
                                        echo "<td>{$count}</td>";
                                    }
                                @endphp
                            </tr>
                            <tr>
                                <td><strong>Réclamations résolues</strong></td>
                                @php
                                    for ($i = 5; $i >= 0; $i--) {
                                        $date = now()->subMonths($i);
                                        $startOfMonth = $date->copy()->startOfMonth();
                                        $endOfMonth = $date->copy()->endOfMonth();
                                        
                                        $count = \App\Models\Complaint::where('status', 'résolu')
                                            ->whereBetween('updated_at', [$startOfMonth, $endOfMonth])->count();
                                        echo "<td>{$count}</td>";
                                    }
                                @endphp
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Print styles */
@media print {
    .sidebar, .navbar, footer, .card-header {
        display: none !important;
    }
    
    .container-fluid {
        width: 100% !important;
        padding: 0 !important;
    }
    
    h5 {
        page-break-before: always;
    }
    
    .progress {
        border: 1px solid #ddd;
        print-color-adjust: exact;
        -webkit-print-color-adjust: exact;
    }
}
</style>
@endsection
