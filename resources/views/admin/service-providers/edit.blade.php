@extends('layouts.admin')

@section('page_title', 'Modifier le Prestataire')
@section('content')
<div class="container mt-4">

    <form action="{{ route('admin.service-providers.update', $serviceProvider) }}" method="POST" class="card shadow p-4 bg-light">
        @csrf
        @method('PUT')
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="name" class="form-label">Nom <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $serviceProvider->name) }}" required>
            </div>
            <div class="col-md-6">
                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $serviceProvider->email) }}" required>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="phone" class="form-label">Téléphone <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $serviceProvider->phone) }}" required>
            </div>
            <div class="col-md-6">
                <label for="address" class="form-label">Adresse <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="address" name="address" value="{{ old('address', $serviceProvider->address) }}" required>
            </div>
        </div>
        <div class="mb-3">
            <label for="service_type" class="form-label">Catégorie <span class="text-danger">*</span></label>
            <select class="form-select" id="service_type" name="service_type" required>
                <option value="">Choisir...</option>
                @foreach(\App\Models\ServiceProvider::getTypes() as $key => $label)
                    <option value="{{ $key }}" {{ old('service_type', $serviceProvider->service_type) == $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="status" class="form-label">Statut</label>
            <select class="form-select" id="status" name="status">
                <option value="active" {{ old('status', $serviceProvider->status) == 'active' ? 'selected' : '' }}>Actif</option>
                <option value="inactive" {{ old('status', $serviceProvider->status) == 'inactive' ? 'selected' : '' }}>Inactif</option>
                <option value="suspended" {{ old('status', $serviceProvider->status) == 'suspended' ? 'selected' : '' }}>Suspendu</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $serviceProvider->description) }}</textarea>
        </div>
        <div class="mb-3">
            <label for="contact_person" class="form-label">Personne de contact</label>
            <input type="text" class="form-control" id="contact_person" name="contact_person" value="{{ old('contact_person', $serviceProvider->contact_person) }}">
        </div>
        <button type="submit" class="btn btn-success">Enregistrer</button>
        <a href="{{ route('admin.service-providers.index') }}" class="btn btn-secondary ms-2">Annuler</a>
    </form>
</div>
@endsection
