@extends('layouts.admin')

@section('content')
    <div class="container">
        <div class="d-flex align-items-center justify-content-between mb-4">
@section('page_title', 'Évaluer les Prestataires')


                <span style="position:relative;display:inline-block;margin:0 10px;">


                </span>

            </div>
        </div>

        <div class="mb-4">
            <form method="GET" action="{{ route('admin.evaluations.index') }}" class="d-flex gap-2 align-items-center">
                <input type="text" name="search" class="form-control" placeholder="Rechercher par nom, email ou type..." value="{{ request('search') }}">
                <button class="btn btn-primary" type="submit">Rechercher</button>
                @if(request('search'))
                    <a href="{{ route('admin.evaluations.index') }}" class="btn btn-secondary">Réinitialiser</a>
                @endif
            </form>
        </div>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Type</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>Adresse</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            @foreach($serviceProviders as $provider)
                <tr>
                    <td>{{ $provider->name }}</td>
                    <td>{{ \App\Models\ServiceProvider::getTypes()[$provider->service_type] ?? '' }}</td>
                    <td>{{ $provider->email }}</td>
                    <td>{{ $provider->phone }}</td>
                    <td>{{ $provider->address }}</td>
                    <td>
                        <a href="{{ route('admin.evaluations.create', $provider->id) }}" class="btn btn-sm btn-primary">Évaluer</a>
                        <a href="{{ route('admin.evaluations.show', $provider->id) }}" class="btn btn-sm btn-info ms-1">Voir évaluations</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <div class="mt-4 d-flex justify-content-center gap-2">
            @if ($serviceProviders->onFirstPage())
                <button class="btn btn-secondary" disabled>Précédent</button>
            @else
                <a class="btn btn-primary" href="{{ $serviceProviders->previousPageUrl() }}">Précédent</a>
            @endif
            <span class="align-self-center">Page {{ $serviceProviders->currentPage() }} / {{ $serviceProviders->lastPage() }}</span>
            @if ($serviceProviders->hasMorePages())
                <a class="btn btn-primary" href="{{ $serviceProviders->nextPageUrl() }}">Suivant</a>
            @else
                <button class="btn btn-secondary" disabled>Suivant</button>
            @endif
        </div>
    </div>
@endsection
