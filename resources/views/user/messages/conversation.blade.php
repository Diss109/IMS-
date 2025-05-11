<div class="card-header py-3 d-flex justify-content-between align-items-center">
    <h6 class="m-0 font-weight-bold text-primary">Conversation avec {{ $user->name }}</h6>
</div>

<div class="message-list d-flex flex-column" id="message-list">
    @if(isset($messages) && $messages->count() > 0)
        @foreach($messages as $message)
            <div class="message-item p-3 {{ $message->sender_id == auth()->id() ? 'message-outgoing' : 'message-incoming' }}" id="message-{{ $message->id }}">
                @if($message->sender_id == auth()->id())
                <div class="message-actions">
                    <button type="button" class="btn btn-sm btn-link text-white edit-message-btn" data-message-id="{{ $message->id }}">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-link text-white delete-message-btn" data-message-id="{{ $message->id }}">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                @endif
                <div class="message-content {{ $message->content === 'Ce message a été supprimé' ? 'message-deleted' : '' }}">
                    {{ $message->content }}
                </div>
                <div class="message-time text-end">
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
    @else
        <div class="text-center py-4 text-muted">
            <p>Aucun message. Commencez la conversation!</p>
        </div>
    @endif
</div>

<div class="card-footer p-3">
    <form id="message-form" action="{{ url('/user/messages/'.$user->id) }}" method="POST" target="message-response-frame">
        @csrf
        <div class="input-group">
            <input type="text" name="content" id="message-input" class="form-control" placeholder="Écrivez un message..." required autocomplete="off">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </form>

    <!-- Hidden iframe for form submission fallback -->
    <iframe name="message-response-frame" style="display: none;"></iframe>

    <!-- Edit Message Modal -->
    <div class="modal fade" id="editMessageModal" tabindex="-1" aria-labelledby="editMessageModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editMessageModalLabel">Modifier le message</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="edit-message-form">
                    <div class="modal-body">
                        <input type="hidden" id="edit-message-id">
                        <div class="mb-3">
                            <label for="edit-message-content" class="form-label">Contenu</label>
                            <textarea class="form-control" id="edit-message-content" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Message Confirmation Modal -->
    <div class="modal fade" id="deleteMessageModal" tabindex="-1" aria-labelledby="deleteMessageModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteMessageModalLabel">Confirmer la suppression</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Êtes-vous sûr de vouloir supprimer ce message?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" id="confirm-delete-btn" class="btn btn-danger">Supprimer</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Store the current user ID for comparison in JavaScript
const currentUserId = {{ auth()->id() }};

document.addEventListener('DOMContentLoaded', function() {
    const messageList = document.getElementById('message-list');
    const messageForm = document.getElementById('message-form');
    const messageInput = document.getElementById('message-input');
    let editModal, deleteModal;

    // Initialize Bootstrap modals if they exist
    const editModalElement = document.getElementById('editMessageModal');
    const deleteModalElement = document.getElementById('deleteMessageModal');

    if (editModalElement) {
        editModal = new bootstrap.Modal(editModalElement);
    }

    if (deleteModalElement) {
        deleteModal = new bootstrap.Modal(deleteModalElement);
    }

    let currentMessageId = null;

    // Scroll to bottom of message list
    if (messageList) {
        messageList.scrollTop = messageList.scrollHeight;
    }

    // Submit form with jQuery AJAX for better browser compatibility
    if (messageForm) {
        $(messageForm).on('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(messageForm);

            // Prevent double submission
            const submitBtn = messageForm.querySelector('button[type="submit"]');
            if (submitBtn) submitBtn.disabled = true;

            $.ajax({
                url: messageForm.action,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                success: function(data) {
                    if (data.success) {
                        // Create new message element
                        const messageItem = document.createElement('div');
                        messageItem.className = 'message-item p-3 message-outgoing';
                        messageItem.id = `message-${data.message.id}`;

                        // Add message actions
                        const messageActions = document.createElement('div');
                        messageActions.className = 'message-actions';
                        messageActions.innerHTML = `
                            <button type="button" class="btn btn-sm btn-link text-white edit-message-btn" data-message-id="${data.message.id}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-link text-white delete-message-btn" data-message-id="${data.message.id}">
                                <i class="fas fa-trash"></i>
                            </button>
                        `;
                        messageItem.appendChild(messageActions);

                        const messageContent = document.createElement('div');
                        messageContent.className = 'message-content';
                        messageContent.textContent = data.message.content;
                        messageItem.appendChild(messageContent);

                        const messageTime = document.createElement('div');
                        messageTime.className = 'message-time text-end';

                        // Format time (HH:MM)
                        const date = new Date(data.message.created_at);
                        const hours = date.getHours().toString().padStart(2, '0');
                        const minutes = date.getMinutes().toString().padStart(2, '0');
                        messageTime.textContent = `${hours}:${minutes} `;

                        // Add sent icon
                        const icon = document.createElement('i');
                        icon.className = 'fas fa-check';
                        icon.title = 'Envoyé';
                        messageTime.appendChild(icon);

                        // Add elements to message item
                        messageItem.appendChild(messageTime);

                        // Add message to list
                        messageList.appendChild(messageItem);

                        // Scroll to bottom and clear input
                        messageList.scrollTop = messageList.scrollHeight;
                        messageInput.value = '';

                        // Attach event listeners to new message
                        attachMessageEventListeners(messageItem);
                    } else {
                        console.error('Message not sent successfully:', data);
                        alert('Erreur lors de l\'envoi du message. Veuillez réessayer.');
                    }

                    // Re-enable submit button
                    if (submitBtn) submitBtn.disabled = false;
                },
                error: function(xhr, status, error) {
                    console.error('Error sending message:', error);
                    console.error('Status:', status);
                    console.error('Response:', xhr.responseText);
                    alert('Erreur lors de l\'envoi du message. Veuillez réessayer.');

                    // Re-enable submit button
                    if (submitBtn) submitBtn.disabled = false;
                }
            });
        });
    }

    // Initialize edit and delete functionality
    function initMessageHandlers() {
        document.querySelectorAll('.message-item').forEach(item => {
            attachMessageEventListeners(item);
        });

        // Edit message form submission
        const editMessageForm = document.getElementById('edit-message-form');
        if (editMessageForm) {
            editMessageForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const messageId = document.getElementById('edit-message-id').value;
                const content = document.getElementById('edit-message-content').value;

                $.ajax({
                    url: `{{ url('/user/messages') }}/${messageId}`,
                    type: 'PUT',
                    data: JSON.stringify({
                        content: content
                    }),
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    success: function(data) {
                        if (data.success) {
                            const messageItem = document.getElementById(`message-${messageId}`);
                            const messageContent = messageItem.querySelector('.message-content');
                            messageContent.textContent = content;
                            messageContent.classList.add('message-edited');

                            if (editModal) editModal.hide();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error updating message:', error);
                        alert('Erreur lors de la mise à jour du message. Veuillez réessayer.');
                    }
                });
            });
        }

        // Delete message confirmation
        const confirmDeleteBtn = document.getElementById('confirm-delete-btn');
        if (confirmDeleteBtn) {
            confirmDeleteBtn.addEventListener('click', function() {
                if (!currentMessageId) return;

                $.ajax({
                    url: `{{ url('/user/messages') }}/${currentMessageId}`,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    success: function(data) {
                        if (data.success) {
                            const messageItem = document.getElementById(`message-${currentMessageId}`);
                            const messageContent = messageItem.querySelector('.message-content');
                            messageContent.textContent = 'Ce message a été supprimé';
                            messageContent.classList.add('message-deleted');

                            // Remove edit/delete buttons
                            const actions = messageItem.querySelector('.message-actions');
                            if (actions) {
                                actions.remove();
                            }

                            if (deleteModal) deleteModal.hide();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error deleting message:', error);
                        alert('Erreur lors de la suppression du message. Veuillez réessayer.');
                    }
                });
            });
        }
    }

    function attachMessageEventListeners(messageItem) {
        const editBtn = messageItem.querySelector('.edit-message-btn');
        const deleteBtn = messageItem.querySelector('.delete-message-btn');

        if (editBtn) {
            editBtn.addEventListener('click', function() {
                const messageId = this.getAttribute('data-message-id');
                const messageContent = messageItem.querySelector('.message-content').textContent.trim();

                document.getElementById('edit-message-id').value = messageId;
                document.getElementById('edit-message-content').value = messageContent;

                if (editModal) editModal.show();
            });
        }

        if (deleteBtn) {
            deleteBtn.addEventListener('click', function() {
                currentMessageId = this.getAttribute('data-message-id');
                if (deleteModal) deleteModal.show();
            });
        }
    }

    // Initialize handlers on load
    initMessageHandlers();

    // Expose to parent window
    window.initMessageHandlers = initMessageHandlers;

    // Poll for new messages every 5 seconds
    let lastTimestamp = new Date().toISOString();

    function fetchNewMessages() {
        const url = `{{ url('/user/messages') }}/{{ $user->id }}/new?last_timestamp=${encodeURIComponent(lastTimestamp)}`;

        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data.messages && data.messages.length > 0) {
                    data.messages.forEach(function(message) {
                        // Only add messages from the other user
                        if (message.sender_id != currentUserId) {
                            // Create new message element
                            const messageItem = document.createElement('div');
                            messageItem.className = 'message-item p-3 message-incoming';
                            messageItem.id = `message-${message.id}`;

                            const messageContent = document.createElement('div');
                            messageContent.className = 'message-content';
                            messageContent.textContent = message.content;
                            messageItem.appendChild(messageContent);

                            const messageTime = document.createElement('div');
                            messageTime.className = 'message-time text-end';

                            // Format time (HH:MM)
                            const date = new Date(message.created_at);
                            const hours = date.getHours().toString().padStart(2, '0');
                            const minutes = date.getMinutes().toString().padStart(2, '0');
                            messageTime.textContent = `${hours}:${minutes}`;

                            // Add elements to message item
                            messageItem.appendChild(messageTime);

                            // Add message to list
                            messageList.appendChild(messageItem);

                            // Scroll to bottom
                            messageList.scrollTop = messageList.scrollHeight;
                        }
                    });

                    // Update last timestamp
                    lastTimestamp = new Date().toISOString();
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching new messages:', error);
            }
        });
    }

    // Check for new messages every 5 seconds if we're on a conversation page
    if (messageList && messageForm) {
        setInterval(fetchNewMessages, 5000);
    }
});
</script>
