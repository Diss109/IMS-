@extends('layouts.admin')

@section('page_title', 'Ajouter un KPI')

@section('content')
<div class="container-fluid">
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form action="{{ route('admin.kpis.store') }}" method="POST">
                @csrf
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Nom du KPI</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="category" class="form-label">Catégorie</label>
                        <select class="form-select @error('category') is-invalid @enderror" id="category" name="category" required>
                            <option value="general" {{ old('category') == 'general' ? 'selected' : '' }}>Général</option>
                            <option value="complaints" {{ old('category') == 'complaints' ? 'selected' : '' }}>Réclamations</option>
                            <option value="staff" {{ old('category') == 'staff' ? 'selected' : '' }}>Personnel</option>
                            <option value="customer_satisfaction" {{ old('category') == 'customer_satisfaction' ? 'selected' : '' }}>Satisfaction client</option>
                        </select>
                        @error('category')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="target" class="form-label">Valeur cible</label>
                        <input type="number" step="0.01" class="form-control @error('target') is-invalid @enderror" id="target" name="target" value="{{ old('target') }}" required>
                        @error('target')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="current_value" class="form-label">Valeur actuelle</label>
                        <input type="number" step="0.01" class="form-control @error('current_value') is-invalid @enderror" id="current_value" name="current_value" value="{{ old('current_value', 0) }}">
                        @error('current_value')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="period" class="form-label">Période</label>
                        <select class="form-select @error('period') is-invalid @enderror" id="period" name="period" required>
                            <option value="daily" {{ old('period') == 'daily' ? 'selected' : '' }}>Quotidien</option>
                            <option value="weekly" {{ old('period') == 'weekly' ? 'selected' : '' }}>Hebdomadaire</option>
                            <option value="monthly" {{ old('period') == 'monthly' ? 'selected' : '' }} selected>Mensuel</option>
                            <option value="quarterly" {{ old('period') == 'quarterly' ? 'selected' : '' }}>Trimestriel</option>
                            <option value="yearly" {{ old('period') == 'yearly' ? 'selected' : '' }}>Annuel</option>
                        </select>
                        @error('period')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="is_active" class="form-label">Statut</label>
                        <select class="form-select @error('is_active') is-invalid @enderror" id="is_active" name="is_active">
                            <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Actif</option>
                            <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inactif</option>
                        </select>
                        @error('is_active')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.kpis.index') }}" class="btn btn-outline-secondary">Annuler</a>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
