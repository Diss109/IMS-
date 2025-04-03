@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("Vous êtes connecté!") }}
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Tableau de bord') }}</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <div class="d-grid gap-3">
                            <a href="{{ route('complaints.create') }}" class="btn btn-primary">
                                <i class="fas fa-file-alt me-2"></i> Soumettre une réclamation
                            </a>
                            <!-- Add more dashboard actions here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
