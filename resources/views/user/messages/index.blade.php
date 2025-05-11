@extends('layouts.admin')

@section('page_title', 'Messages')

@section('content')
<!-- Add jQuery at the beginning of the content section -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <a href="{{ route('user.messages.debug') }}" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-bug"></i> Debug Messages
            </a>
        </div>
    </div>
    <div class="row">
        <!-- Users List -->
        <div class="col-md-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Utilisateurs</h6>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush user-list">
                        @if($users->isEmpty())
                            <div class="text-center py-4">
                                <p class="text-muted mb-0">Aucun utilisateur disponible pour la messagerie</p>
                            </div>
                        @else
                            @foreach($users as $userItem)
                                <a href="javascript:void(0);"
                                   data-user-id="{{ $userItem->id }}"
                                   class="user-chat-item list-group-item list-group-item-action d-flex align-items-center py-3 px-3
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
                                        <span class="badge bg-danger rounded-pill unread-count-badge">{{ $unreadCount }}</span>
                                    @endif
                                </a>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Conversation Area -->
        <div class="col-md-8">
            <div class="card shadow" id="conversation-card">
                <div id="conversation-placeholder" class="d-flex flex-column justify-content-center align-items-center py-5" style="{{ isset($selectedUser) ? 'display: none !important;' : '' }}">
                    <i class="fas fa-comments fa-4x text-muted mb-3"></i>
                    <h5>Sélectionnez un utilisateur pour démarrer une conversation</h5>
                    <p class="text-muted">Vous pourrez communiquer en temps réel avec les autres utilisateurs du système</p>
                </div>

                <div id="conversation-container" style="{{ isset($selectedUser) ? '' : 'display: none;' }}">
                    @if(isset($selectedUser))
                        @include('user.messages.conversation', ['user' => $selectedUser])
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.user-list {
    max-height: 500px;
    overflow-y: auto;
}
.message-list {
    height: 350px;
    overflow-y: auto;
    padding: 1rem;
}
.message-item {
    margin-bottom: 1rem;
    max-width: 75%;
    position: relative;
}
.message-item .message-actions {
    position: absolute;
    top: 5px;
    right: 5px;
    display: none;
}
.message-item:hover .message-actions {
    display: flex;
}
.message-outgoing {
    align-self: flex-end;
    background-color: #4361ee;
    color: white;
    border-radius: 15px 15px 0 15px;
}
.message-incoming {
    align-self: flex-start;
    background-color: #f0f2f5;
    border-radius: 15px 15px 15px 0;
}
.message-time {
    font-size: 0.7rem;
    opacity: 0.7;
}
.message-deleted {
    font-style: italic;
    opacity: 0.7;
}
.message-edited:after {
    content: " (modifié)";
    font-size: 0.7rem;
    opacity: 0.7;
}
.user-chat-item.active {
    background-color: rgba(67, 97, 238, 0.1);
    border-left: 3px solid #4361ee;
}
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // User list click handler
    document.querySelectorAll('.user-chat-item').forEach(item => {
        item.addEventListener('click', function() {
            // Remove active class from all items
            document.querySelectorAll('.user-chat-item').forEach(i => {
                i.classList.remove('active');
            });

            // Add active class to clicked item
            this.classList.add('active');

            const userId = this.getAttribute('data-user-id');
            loadConversation(userId);
        });
    });

    function loadConversation(userId) {
        document.getElementById('conversation-placeholder').style.display = 'none';
        document.getElementById('conversation-container').style.display = 'block';
        document.getElementById('conversation-container').innerHTML = '<div class="text-center p-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';

        // Use jQuery AJAX for better browser compatibility
        $.ajax({
            url: `{{ url('/user/messages') }}/${userId}`,
            type: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(html) {
                document.getElementById('conversation-container').innerHTML = html;

                // Initialize message actions after loading
                initMessageHandlers();

                // Mark messages as read
                const unreadBadge = document.querySelector(`.user-chat-item[data-user-id="${userId}"] .unread-count-badge`);
                if (unreadBadge) {
                    unreadBadge.remove();
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading conversation:', error);
                console.error('Status:', status);
                console.error('Response:', xhr.responseText);
                document.getElementById('conversation-container').innerHTML = '<div class="text-center p-5 text-danger">Erreur lors du chargement de la conversation. Veuillez réessayer.</div>';
            }
        });
    }

    function initMessageHandlers() {
        // The message handlers will be initialized by the conversation.blade.php script
    }
});
</script>
@endpush
@endsection
