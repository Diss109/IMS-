@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h1 class="dashboard-title">Modifier l'utilisateur</h1>
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
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.users.update', $user) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group mb-3">
                            <label for="name">Nom</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="email">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="role">Rôle</label>
                            <select class="form-control @error('role') is-invalid @enderror" id="role" name="role" required>
    <option value="">Sélectionner un rôle</option>
    @foreach(\App\Models\User::getRoles() as $value => $label)
        <option value="{{ $value }}" {{ old('role', $user->role) == $value ? 'selected' : '' }}>{{ $label }}</option>
    @endforeach
</select>
                            @error('role')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Mettre à jour</button>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Annuler</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
