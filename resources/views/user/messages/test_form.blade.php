@extends('layouts.admin')

@section('page_title', 'Test Message Form')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Envoyer un message à {{ $user->name }}</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">Ce formulaire envoie un message via une soumission de formulaire standard (non-AJAX).</p>

                    <form action="{{ route('user.messages.send', $user->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="content" class="form-label">Message</label>
                            <textarea class="form-control" name="content" id="content" rows="3" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Envoyer</button>
                        <a href="{{ route('user.messages.index') }}" class="btn btn-secondary">Retour aux messages</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
