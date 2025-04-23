@extends('layouts.admin')

@section('content')
    <div class="container mt-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
@section('page_title', 'Permissions d\'Évaluateur')


                <span style="position:relative;display:inline-block;margin:0 10px;">

                    <span id="notification-badge" style="position:absolute;top:-7px;right:-7px;background:#dc3545;color:#fff;border-radius:50%;padding:2px 7px;font-size:12px;min-width:18px;text-align:center;{{ (($unreadNotificationsCount ?? 0) > 0) ? '' : 'display:none;' }}">
    {{ $unreadNotificationsCount ?? 0 }}
</span>
                </span>

            </div>
        </div>
        <h1 class="mb-4">Gestion des permissions d'évaluation des prestataires</h1>
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <form action="{{ route('admin.evaluator_permissions.store') }}" method="POST">
            @csrf
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Catégorie de prestataire</th>
                        <th>Rôles pouvant évaluer</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $key => $category)
                        <tr>
                            <td>{{ $category }}</td>
                            <td>
                                <select name="permissions[{{ $key }}][]" multiple class="form-select">
                                    @foreach($roles as $roleKey => $roleLabel)
                                        <option value="{{ $roleKey }}"
                                            @if(isset($permissions[$key]) && $permissions[$key]->pluck('role')->contains($roleKey)) selected @endif>
                                            {{ $roleLabel }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <button type="submit" class="btn btn-primary">Enregistrer les permissions</button>
        </form>
    </div>
@endsection
