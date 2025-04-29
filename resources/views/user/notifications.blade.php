@extends('layouts.admin')
@section('page_title', 'Notifications')
@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Mes Notifications</h2>
    <div class="card">
        <div class="card-body">
            @if($notifications->isEmpty())
                <div class="alert alert-info">Aucune notification.</div>
            @else
                <ul class="list-group">
                    @foreach($notifications as $notification)
                        <li class="list-group-item d-flex justify-content-between align-items-center {{ !$notification->is_read ? 'fw-bold bg-warning-subtle' : 'bg-white text-muted' }}">
                            <span>
                                {{ $notification->message }}
                                <br>
                                <small class="text-muted">{{ $notification->created_at->format('d/m/Y H:i') }}</small>
                            </span>
                            @if($notification->related_id && $notification->type === 'complaint_assignment')
                                <a href="{{ route('user.complaints.show', $notification->related_id) }}" class="btn btn-sm btn-primary ms-2">Voir la réclamation</a>
                            @endif
                            <form method="POST" action="{{ route('user.notifications.destroy', $notification->id) }}" class="d-inline ms-2">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">X</button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>
@endsection
