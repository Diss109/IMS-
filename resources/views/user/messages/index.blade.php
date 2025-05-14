@extends('layouts.admin')

@section('page_title', 'Messages')

@section('content')
<!-- Add jQuery at the beginning of the content section -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<div class="container-fluid">
    <div class="row">
        <!-- Users List -->
        <div class="col-md-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Utilisateurs</h6>
                    <a href="{{ route('user.dashboard') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
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
                                    <div class="me-3 position-relative">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($userItem->name) }}&background=4361ee&color=fff"
                                             class="rounded-circle" width="40" height="40" alt="{{ $userItem->name }}">
                                        @php
                                            $unreadCount = \App\Models\Message::where('sender_id', $userItem->id)
                                                ->where('receiver_id', auth()->id())
                                                ->where('is_read', false)
                                                ->count();
                                        @endphp
                                        @if($unreadCount > 0)
                                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger unread-count-badge">
                                                {{ $unreadCount }}
                                                <span class="visually-hidden">unread messages</span>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0">{{ $userItem->name }}</h6>
                                        <small class="text-muted">{{ \App\Models\User::getRoles()[$userItem->role] ?? 'Utilisateur' }}</small>
                                    </div>
                                </a>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Conversation Area -->
        <div class="col-md-8">
            <div class="card shadow h-100 d-flex flex-column" id="conversation-card">
                <div id="conversation-placeholder" class="d-flex flex-column justify-content-center align-items-center py-5 flex-grow-1 {{ isset($selectedUser) ? 'd-none' : '' }}">
                    <i class="fas fa-comments fa-4x text-muted mb-3"></i>
                    <h5>Sélectionnez un utilisateur pour démarrer une conversation</h5>
                    <p class="text-muted">Vous pourrez communiquer en temps réel avec les autres utilisateurs du système</p>
                </div>

                <div id="conversation-container" class="d-flex flex-column flex-grow-1 {{ isset($selectedUser) ? '' : 'd-none' }}">
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
    max-height: 70vh;
    overflow-y: auto;
    scrollbar-width: thin;
}
.message-list {
    height: calc(70vh - 120px);
    overflow-y: auto;
    padding: 1rem;
    scrollbar-width: thin;
    display: flex;
    flex-direction: column;
    background-color: #f8f9fc;
}
.message-item {
    margin-bottom: 0.75rem;
    max-width: 75%;
    position: relative;
    border-radius: 10px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.1);
    word-wrap: break-word;
}
.message-item .message-actions {
    position: absolute;
    top: 5px;
    right: 5px;
    display: none;
    gap: 5px;
    z-index: 10;
}
.message-item:hover .message-actions {
    display: flex;
}
.message-actions .btn-link {
    padding: 0;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: rgba(0, 0, 0, 0.2);
    border-radius: 50%;
    margin-left: 5px;
}
.message-content {
    position: relative;
    z-index: 1;
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
    margin-top: 5px;
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
.user-chat-item:hover {
    background-color: rgba(67, 97, 238, 0.05);
}
/* Improved input area */
.card-footer {
    background-color: #fff;
    border-top: 1px solid rgba(0,0,0,0.1);
    padding: 1rem !important;
}
.input-group {
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    border-radius: 50px;
    overflow: hidden;
}
.input-group .form-control {
    border-radius: 50px 0 0 50px;
    border: 1px solid #e0e0e0;
    padding-left: 20px;
}
.input-group .btn {
    border-radius: 0 50px 50px 0;
    padding-left: 20px;
    padding-right: 20px;
}
/* Custom scrollbar */
::-webkit-scrollbar {
    width: 5px;
}
::-webkit-scrollbar-track {
    background: transparent;
}
::-webkit-scrollbar-thumb {
    background: rgba(0, 0, 0, 0.2);
    border-radius: 10px;
}
::-webkit-scrollbar-thumb:hover {
    background: rgba(0, 0, 0, 0.3);
}
/* Badge position fix */
.position-relative .badge {
    transform: translate(-50%, -50%);
}
/* Fix for style attributes in blade */
#conversation-placeholder[style*="display: none"] {
    display: none !important;
}
#conversation-container[style*="display: none"] {
    display: none !important;
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
        document.getElementById('conversation-placeholder').classList.add('d-none');
        document.getElementById('conversation-container').classList.remove('d-none');
        document.getElementById('conversation-container').innerHTML = '<div class="d-flex justify-content-center align-items-center w-100 h-100"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';

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
                if (typeof initMessageHandlers === 'function') {
                initMessageHandlers();
                }

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
                document.getElementById('conversation-container').innerHTML = '<div class="d-flex justify-content-center align-items-center w-100 h-100 text-danger"><div><i class="fas fa-exclamation-circle fa-3x mb-3"></i><p>Erreur lors du chargement de la conversation.<br>Veuillez réessayer.</p></div></div>';
            }
        });
    }
});
</script>
@endpush
@endsection
