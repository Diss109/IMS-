@extends('layouts.admin')

@section('page_title', 'Tableau de bord')

@section('styles')
<style>
    .stat-card {
        position: relative;
        overflow: hidden;
        min-height: 120px;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 100%);
        z-index: 1;
    }

    .stat-card .card-icon {
        position: absolute;
        right: -15px;
        bottom: -15px;
        font-size: 5rem;
        opacity: 0.15;
        transform: rotate(-15deg);
        transition: all 0.5s;
    }

    .stat-card:hover .card-icon {
        transform: rotate(0) scale(1.1);
        opacity: 0.2;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
    }

    .stat-label {
        text-transform: uppercase;
        letter-spacing: 1px;
        font-weight: 600;
        font-size: 0.85rem;
        margin-bottom: 5px;
    }

    .filter-card {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
    }

    .recent-complaints-card {
        transition: all 0.3s;
    }

    .hover-shadow {
        transition: all 0.3s;
    }

    .hover-shadow:hover {
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .animate-on-scroll {
        opacity: 0;
        transform: translateY(20px);
        transition: all 0.6s;
    }

    .animate-on-scroll.appear {
        opacity: 1;
        transform: translateY(0);
    }

    .period-btn {
        position: relative;
        overflow: hidden;
        z-index: 1;
    }

    .period-btn::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 0;
        background: rgba(0, 0, 0, 0.05);
        transition: all 0.3s;
        z-index: -1;
    }

    .period-btn:hover::after {
        height: 100%;
    }
</style>
@endsection

@section('content')
    <div class="container-fluid fade-in">
        <!-- Date Filter Bar -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow filter-card hover-shadow animate-on-scroll">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-filter me-2"></i>Filtres de date
                        </h6>
                    </div>
                    <div class="card-body">
                        <!-- Period Filters -->
                        <div class="mb-3">
                            <label class="form-label"><strong>Période prédéfinie:</strong></label>
                            <div class="d-flex flex-wrap" style="gap: 10px;">
                                <a href="{{ route('admin.dashboard', ['period' => 'week']) }}"
                                   class="btn btn-outline-primary period-btn">
                                   <i class="fas fa-calendar-week me-1"></i> Cette semaine
                                </a>
                                <a href="{{ route('admin.dashboard', ['period' => 'month']) }}"
                                   class="btn btn-outline-primary period-btn">
                                   <i class="fas fa-calendar-alt me-1"></i> Ce mois
                                </a>
                                <a href="{{ route('admin.dashboard', ['period' => 'total']) }}"
                                   class="btn btn-outline-primary period-btn">
                                   <i class="fas fa-infinity me-1"></i> Tout
                                </a>
                            </div>
                        </div>

                        <hr>

                        <!-- Custom Date Range -->
                        <form action="{{ route('admin.dashboard') }}" method="GET">
                            <div class="mb-3">
                                <label class="form-label"><strong>Période personnalisée:</strong></label>
                                <div class="row">
                                    <div class="col-md-4 mb-2">
                                        <label for="start_date">Date de début</label>
                                        <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date') }}">
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <label for="end_date">Date de fin</label>
                                        <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date') }}">
                                    </div>
                                    <div class="col-md-4 d-flex align-items-end mb-2">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-filter me-1"></i> Appliquer
                                        </button>
                                        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary ms-2">
                                            <i class="fas fa-undo me-1"></i> Réinitialiser
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-gradient shadow h-100 stat-card animate-on-scroll" style="border-left: 4px solid #4e73df;">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="stat-label text-primary">Total Réclamations</div>
                                <div class="stat-value text-gray-800">{{ $statistics['total'] }}</div>
                            </div>
                        </div>
                        <i class="fas fa-clipboard-list card-icon text-primary"></i>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-gradient shadow h-100 stat-card animate-on-scroll" style="border-left: 4px solid #1cc88a;">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="stat-label text-success">Résolues</div>
                                <div class="stat-value text-gray-800">{{ $statistics['resolved'] }}</div>
                            </div>
                        </div>
                        <i class="fas fa-check-circle card-icon text-success"></i>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-gradient shadow h-100 stat-card animate-on-scroll" style="border-left: 4px solid #f6c23e;">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="stat-label text-warning">En Attente</div>
                                <div class="stat-value text-gray-800">{{ $statistics['waiting'] }}</div>
                            </div>
                        </div>
                        <i class="fas fa-clock card-icon text-warning"></i>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-gradient shadow h-100 stat-card animate-on-scroll" style="border-left: 4px solid #e74a3b;">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="stat-label text-danger">Non Résolues</div>
                                <div class="stat-value text-gray-800">{{ $statistics['unresolved'] }}</div>
                            </div>
                        </div>
                        <i class="fas fa-times-circle card-icon text-danger"></i>
                    </div>
                </div>
            </div>
        </div>


        <!-- Recent Complaints Table -->
        <div class="card shadow mb-4 recent-complaints-card hover-shadow animate-on-scroll">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-list me-2"></i>Réclamations récentes
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
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
                                                <span class="badge bg-info">Retard de livraison</span>
                                                @break
                                            @case('retard_chargement')
                                                <span class="badge bg-primary">Retard de chargement</span>
                                                @break
                                            @case('marchandise_endommagée')
                                                <span class="badge bg-dark">Marchandise endommagée</span>
                                                @break
                                            @case('mauvais_comportement')
                                                <span class="badge bg-secondary">Mauvais comportement</span>
                                                @break
                                            @default
                                                <span class="badge bg-light text-dark">Autre</span>
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
                        <div class="text-center py-4 text-muted no-data">
                            <i class="fas fa-inbox fa-3x mb-3"></i>
                            <p>Aucune réclamation trouvée</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animation on scroll
    const animateElements = document.querySelectorAll('.animate-on-scroll');

    function checkIfInView() {
        animateElements.forEach(element => {
            const elementTop = element.getBoundingClientRect().top;
            const elementVisible = 150;

            if (elementTop < window.innerHeight - elementVisible) {
                element.classList.add('appear');
            }
        });
    }

    // Run once on initial load
    checkIfInView();

    // Run on scroll
    window.addEventListener('scroll', checkIfInView);
});
</script>
@endsection
