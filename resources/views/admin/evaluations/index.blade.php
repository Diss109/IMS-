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

        <div class="table-responsive">
            <table class="table table-bordered table-striped table-sm" style="font-size: 0.85rem;">
                <thead>
                    <tr>
                        <th style="width: 18%">Nom</th>
                        <th style="width: 12%">Type</th>
                        <th style="width: 22%">Email</th>
                        <th style="width: 12%">Téléphone</th>
                        <th style="width: 18%">Adresse</th>
                        <th style="width: 18%">Action</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($serviceProviders as $provider)
                    <tr>
                        <td class="align-middle">{{ $provider->name }}</td>
                        <td class="align-middle">{{ \App\Models\ServiceProvider::getTypes()[$provider->service_type] ?? '' }}</td>
                        <td class="align-middle">{{ $provider->email }}</td>
                        <td class="align-middle">{{ $provider->phone }}</td>
                        <td class="align-middle">{{ Str::limit($provider->address, 30) }}</td>
                        <td class="p-1">
                            <div class="d-flex gap-1 justify-content-center">
                                <a href="{{ route('admin.evaluations.create', $provider->id) }}" class="btn btn-xs btn-primary p-1" style="font-size: 0.8rem;">Évaluer</a>
                                <a href="{{ route('admin.evaluations.show', $provider->id) }}" class="btn btn-xs btn-info p-1" style="font-size: 0.8rem;">Voir</a>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

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
