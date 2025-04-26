@extends('layouts.admin')

@section('content')
    <div class="container">
        <div class="d-flex align-items-center justify-content-between mb-4">
@section('page_title', 'Évaluer les Prestataires')


                <span style="position:relative;display:inline-block;margin:0 10px;">

                    
                </span>

            </div>
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
                    <td>{{ \App\Models\ServiceProvider::getTypes()[$provider->type] ?? '' }}</td>
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
    </div>
@endsection
