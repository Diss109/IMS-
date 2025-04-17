@extends('layouts.admin')

@section('content')
    <div class="container mt-4">
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
