@extends('layouts.admin')

@section('content')
    <div class="container">
        <h1 class="mb-4">Évaluer les Prestataires</h1>
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
                    <td>{{ \App\Models\ServiceProvider::getTypes()[$provider->type] ?? '' }}</td>
                    <td>{{ $provider->email }}</td>
                    <td>{{ $provider->phone }}</td>
                    <td>{{ $provider->address }}</td>
                    <td>
                        <a href="{{ route('admin.evaluations.create', $provider->id) }}" class="btn btn-primary btn-sm">Évaluer</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
