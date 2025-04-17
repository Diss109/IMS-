@php
    if (!isset($complaintTypes) || !is_array($complaintTypes)) {
        $complaintTypes = ['retard_livraison','retard_chargement','marchandise_endommagée','mauvais_comportement','autre'];
    }
    // Use DB values as keys, French as labels
    $urgencyLevels = [
        'critical' => 'Critique',
        'high' => 'Élevé',
        'medium' => 'Moyen',
        'low' => 'Faible',
    ];
@endphp
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Filtres</h6>
    </div>
    <div class="card-body">
        <form id="filterForm" class="row g-3" method="GET" action="{{ route('admin.complaints.index') }}">
            <div class="col-md-2">
                <label for="period">Période</label>
                <select class="form-control" id="period" name="period">
                    <option value="total" {{ request('period') == 'total' ? 'selected' : '' }}>Total</option>
                    <option value="week" {{ request('period') == 'week' ? 'selected' : '' }}>Cette semaine</option>
                    <option value="month" {{ request('period') == 'month' ? 'selected' : '' }}>Ce mois</option>
                    <option value="year" {{ request('period') == 'year' ? 'selected' : '' }}>Cette année</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="type">Type</label>
                <select class="form-control" id="type" name="type">
                    <option value="">Tous</option>
                    @foreach($complaintTypes as $type)
                        <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                            @switch($type)
                                @case('retard_livraison') Retard de livraison @break
                                @case('retard_chargement') Retard de chargement @break
                                @case('marchandise_endommagée') Marchandise endommagée @break
                                @case('mauvais_comportement') Mauvais comportement @break
                                @default Autre
                            @endswitch
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="urgency">Urgence</label>
                <select class="form-control" id="urgency" name="urgency">
                    <option value="">Toutes</option>
                    @foreach($urgencyLevels as $key => $label)
                        <option value="{{ $key }}" {{ request('urgency') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="status">Statut</label>
                <select class="form-control" id="status" name="status">
                    <option value="">Tous</option>
                    <option value="résolu" {{ request('status') == 'résolu' ? 'selected' : '' }}>Résolu</option>
                    <option value="en_attente" {{ request('status') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                    <option value="non_résolu" {{ request('status') == 'non_résolu' ? 'selected' : '' }}>Non résolu</option>
                </select>
            </div>
            <div class="col-md-3">
                <label>Date</label>
                <input type="date" class="form-control" id="date" name="date" value="{{ request('date') }}">
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Filtrer</button>
            </div>
        </form>
    </div>
</div>
