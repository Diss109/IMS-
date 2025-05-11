@extends('layouts.admin')

@section('page_title', 'Simple Messages')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <div class="btn-group">
                <a href="{{ route('user.messages.index') }}" class="btn btn-primary btn-sm">
                    Regular Messages
                </a>
                <a href="{{ route('user.messages.debug') }}" class="btn btn-info btn-sm">
                    Debug Messages
                </a>
                <a href="{{ route('user.messages.simple_test') }}" class="btn btn-secondary btn-sm">
                    Simple Test
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Users List -->
        <div class="col-md-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Utilisateurs</h6>
                </div>
                <div class="card-body">
                    @if($users->isEmpty())
                        <div class="text-center py-4">
                            <p class="text-muted mb-0">Aucun utilisateur disponible pour la messagerie</p>
                        </div>
                    @else
                        <div class="list-group">
                            @foreach($users as $userItem)
                                <a href="{{ route('user.messages.simple_conversation', $userItem->id) }}"
                                   class="list-group-item list-group-item-action d-flex align-items-center py-3 px-3
                                          {{ isset($selectedUser) && $selectedUser->id == $userItem->id ? 'active' : '' }}">
                                    <div class="me-3">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($userItem->name) }}&background=4361ee&color=fff"
                                             class="rounded-circle" width="40" height="40" alt="{{ $userItem->name }}">
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0">{{ $userItem->name }}</h6>
                                        <small class="text-muted">{{ \App\Models\User::getRoles()[$userItem->role] ?? 'Utilisateur' }}</small>
                                    </div>
                                    @php
                                        $unreadCount = \App\Models\Message::where('sender_id', $userItem->id)
                                            ->where('receiver_id', auth()->id())
                                            ->where('is_read', false)
                                            ->count();
                                    @endphp
                                    @if($unreadCount > 0)
                                        <span class="badge bg-danger rounded-pill">{{ $unreadCount }}</span>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Conversation Area -->
        <div class="col-md-8">
            <div class="card shadow">
                @if(isset($selectedUser))
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Conversation avec {{ $selectedUser->name }}</h6>
                    </div>
                    <div class="card-body">
                        @if(isset($messages) && $messages->count() > 0)
                            <div class="mb-4" style="max-height: 400px; overflow-y: auto;">
                                @foreach($messages as $message)
                                    <div class="mb-3 p-3 rounded {{ $message->sender_id == auth()->id() ? 'bg-primary text-white ms-auto' : 'bg-light' }}"
                                         style="max-width: 80%; {{ $message->sender_id == auth()->id() ? 'margin-left: auto;' : '' }}">
                                        <div>{{ $message->content }}</div>
                                        <div class="small {{ $message->sender_id == auth()->id() ? 'text-white-50' : 'text-muted' }} text-end">
                                            {{ $message->created_at->format('H:i') }}
                                            @if($message->sender_id == auth()->id())
                                                @if($message->is_read)
                                                    <i class="fas fa-check-double" title="Lu"></i>
                                                @else
                                                    <i class="fas fa-check" title="Envoyé"></i>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4 text-muted">
                                <p>Aucun message. Commencez la conversation!</p>
                            </div>
                        @endif

                        <form action="{{ route('user.messages.send', $selectedUser->id) }}" method="POST" class="mt-3">
                            @csrf
                            <div class="input-group">
                                <input type="text" name="content" class="form-control" placeholder="Écrivez un message..." required>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                @else
                    <div class="card-body text-center py-5">
                        <i class="fas fa-comments fa-4x text-muted mb-3"></i>
                        <h5>Sélectionnez un utilisateur pour démarrer une conversation</h5>
                        <p class="text-muted">Vous pourrez communiquer avec les autres utilisateurs du système</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
