@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Soumettre une réclamation</h4>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('complaints.store') }}">
                        @csrf

                        <!-- Détails Client -->
                        <div class="mb-4">
                            <h5>Détails Client</h5>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="company_name" class="form-label">Nom de l'entreprise <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('company_name') is-invalid @enderror"
                                           id="company_name" name="company_name" value="{{ old('company_name') }}" required>
                                    @error('company_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                           id="email" name="email" value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="first_name" class="form-label">Prénom</label>
                                    <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                                           id="first_name" name="first_name" value="{{ old('first_name') }}">
                                    @error('first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="last_name" class="form-label">Nom</label>
                                    <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                                           id="last_name" name="last_name" value="{{ old('last_name') }}">
                                    @error('last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Type de réclamation -->
                        <div class="mb-4">
                            <h5>Type de réclamation</h5>

                            <div class="mb-3">
                                <label for="complaint_type" class="form-label">Type de réclamation <span class="text-danger">*</span></label>
                                <select class="form-select @error('complaint_type') is-invalid @enderror"
                                        id="complaint_type" name="complaint_type" required>
                                    <option value="">Sélectionnez le type de réclamation</option>
                                    <option value="retard_livraison" {{ old('complaint_type') == 'retard_livraison' ? 'selected' : '' }}>
                                        Retard de livraison
                                    </option>
                                    <option value="retard_chargement" {{ old('complaint_type') == 'retard_chargement' ? 'selected' : '' }}>
                                        Retard de chargement
                                    </option>
                                    <option value="marchandise_endommagée" {{ old('complaint_type') == 'marchandise_endommagée' ? 'selected' : '' }}>
                                        Marchandise endommagée
                                    </option>
                                    <option value="mauvais_comportement" {{ old('complaint_type') == 'mauvais_comportement' ? 'selected' : '' }}>
                                        Mauvais comportement
                                    </option>
                                    <option value="autre" {{ old('complaint_type') == 'autre' ? 'selected' : '' }}>
                                        Autre
                                    </option>
                                </select>
                                @error('complaint_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="urgency_level" class="form-label">Niveau d'urgence</label>
                                <select class="form-select @error('urgency_level') is-invalid @enderror"
                                        id="urgency_level" name="urgency_level">
                                    <option value="low" {{ old('urgency_level') == 'low' ? 'selected' : '' }}>Faible</option>
                                    <option value="medium" {{ old('urgency_level') == 'medium' ? 'selected' : '' }}>Moyen</option>
                                    <option value="high" {{ old('urgency_level') == 'high' ? 'selected' : '' }}>Élevé</option>
                                    <option value="critical" {{ old('urgency_level') == 'critical' ? 'selected' : '' }}>Critique</option>
                                </select>
                                @error('urgency_level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <h5>Description</h5>
                            <div class="mb-3">
                                <label for="description" class="form-label">Veuillez nous décrire votre problème en détail</label>
                                <textarea class="form-control @error('description') is-invalid @enderror"
                                          id="description" name="description" rows="5"
                                          placeholder="Décrivez votre réclamation...">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                Soumettre la réclamation
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
