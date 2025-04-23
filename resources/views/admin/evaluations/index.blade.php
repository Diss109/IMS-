@extends('layouts.admin')

@section('content')
    <div class="container">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h1 class="dashboard-title">Évaluer les Prestataires</h1>
            <div class="d-flex align-items-center gap-2 dashboard-user-logo">
                <span class="dashboard-username dashboard-username-small">{{ Auth::user()->name }}</span>
                <span style="position:relative;display:inline-block;margin:0 10px;">
                    <i class="fas fa-bell" id="notification-bell" style="font-size:26px;color:#555;"></i>
                    <span id="notification-badge" style="position:absolute;top:-7px;right:-7px;background:#dc3545;color:#fff;border-radius:50%;padding:2px 7px;font-size:12px;min-width:18px;text-align:center;{{ (($unreadNotificationsCount ?? 0) > 0) ? '' : 'display:none;' }}">
    {{ $unreadNotificationsCount ?? 0 }}
</span>
                </span>
                <img src="{{ asset('images/logo.jpg') }}" alt="Tuniship Logo" height="64" style="width:64px;object-fit:contain;">
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
                        <a href="{{ route('admin.evaluations.create', $provider->id) }}" class="btn btn-primary btn-sm">Évaluer</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
