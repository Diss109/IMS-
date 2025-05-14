<div class="card-header py-3 d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center">
        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=4361ee&color=fff"
             class="rounded-circle me-2" width="32" height="32" alt="{{ $user->name }}">
        <h6 class="m-0 font-weight-bold text-primary">{{ $user->name }}</h6>
    </div>
    <div class="conversation-actions">
        <button class="btn btn-sm btn-outline-secondary refresh-btn" title="Rafraîchir">
            <i class="fas fa-sync-alt"></i>
        </button>
    </div>
</div>

<div class="message-list d-flex flex-column" id="message-list">
    @if(isset($messages) && $messages->count() > 0)
        @foreach($messages as $message)
            <div class="message-item p-3 {{ $message->sender_id == auth()->id() ? 'message-outgoing' : 'message-incoming' }}" id="message-{{ $message->id }}">
                @if($message->sender_id == auth()->id())
                <div class="message-actions">
                    <button type="button" onclick="deleteMessage({{ $message->id }})" class="btn btn-sm btn-link text-white delete-message-btn">
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
        <div class="text-center py-4 text-muted flex-grow-1 d-flex align-items-center justify-content-center">
            <div>
                <i class="fas fa-comments fa-3x mb-3 opacity-25"></i>
            <p>Aucun message. Commencez la conversation!</p>
            </div>
        </div>
    @endif
</div>

<div class="card-footer p-3">
    <form id="message-form" action="{{ url('/user/messages/'.$user->id) }}" method="POST">
        @csrf
        <div class="input-group">
            <input type="text" name="content" id="message-input" class="form-control" placeholder="Écrivez un message..." required autocomplete="off">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </form>

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
        <div class="modal-dialog modal-dialog-centered">
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
// Ensure jQuery is available
document.write('<script src="https://code.jquery.com/jquery-3.6.0.min.js"><\/script>');
// Ensure Bootstrap JS is loaded
document.write('<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"><\/script>');

document.addEventListener('DOMContentLoaded', function() {
    // Get DOM elements
    const messageList = document.getElementById('message-list');
    const messageForm = document.getElementById('message-form');
    const messageInput = document.getElementById('message-input');
    const refreshBtn = document.querySelector('.refresh-btn');
    const deleteForms = document.querySelectorAll('.delete-message-form');

    // Parse user ID from HTML
    const currentUserId = {{ auth()->id() }};

    // Scroll to bottom of message list
    if (messageList) {
        messageList.scrollTop = messageList.scrollHeight;
    }

    // Handle refresh button click
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function() {
            const refreshIcon = refreshBtn.querySelector('i');
            refreshBtn.disabled = true;
            refreshIcon.classList.add('fa-spin');

            // Reload conversation
            window.location.reload();
        });
    }

    // Add event listeners to all delete forms
    deleteForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            if (confirm('Êtes-vous sûr de vouloir supprimer ce message?')) {
                const formData = new FormData(form);
                const url = form.getAttribute('action');

                fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Find the message item
                        const messageItem = form.closest('.message-item');
                        const messageContent = messageItem.querySelector('.message-content');

                        // Update content
                        messageContent.textContent = 'Ce message a été supprimé';
                        messageContent.classList.add('message-deleted');

                        // Hide delete button
                        const messageActions = messageItem.querySelector('.message-actions');
                        if (messageActions) {
                            messageActions.style.display = 'none';
                        }
                    }
                })
                .catch(error => {
                    console.error('Error deleting message:', error);
                    alert('Erreur lors de la suppression du message.');
                });
            }
        });
    });

    // Handle form submission directly
    if (messageForm) {
        messageForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const content = messageInput.value.trim();
            if (!content) {
                return;
            }

            // Prevent double submission
            const submitBtn = messageForm.querySelector('button[type="submit"]');
            if (submitBtn) submitBtn.disabled = true;

            // Create form data
            const formData = new FormData(messageForm);

            // Send using fetch API
            fetch(messageForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                    if (data.success) {
                        // Create new message element
                        const messageItem = document.createElement('div');
                        messageItem.className = 'message-item p-3 message-outgoing';
                        messageItem.id = `message-${data.message.id}`;

                        // Add message actions
                        const messageActions = document.createElement('div');
                        messageActions.className = 'message-actions';
                        messageActions.innerHTML = `
                        <button type="button" onclick="deleteMessage(${data.message.id})" class="btn btn-sm btn-link text-white delete-message-btn">
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

                    // Add message to list or handle empty state
                    if (!messageList.querySelector('.message-item')) {
                        // If this is the first message, clear the empty state message
                        messageList.innerHTML = '';
                    }

                        // Add message to list
                        messageList.appendChild(messageItem);

                        // Scroll to bottom and clear input
                        messageList.scrollTop = messageList.scrollHeight;
                        messageInput.value = '';

                    // Add event listener to new delete form
                    const newDeleteForm = messageItem.querySelector('.delete-message-form');
                    if (newDeleteForm) {
                        addDeleteFormListener(newDeleteForm);
                    }
                } else {
                    alert('Erreur lors de l\'envoi du message.');
                    }

                    // Re-enable submit button
                    if (submitBtn) submitBtn.disabled = false;
            })
            .catch(error => {
                    console.error('Error sending message:', error);
                alert('Erreur lors de l\'envoi du message.');

                    // Re-enable submit button
                    if (submitBtn) submitBtn.disabled = false;
            });
        });

        // Also handle Enter key press
        messageInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                messageForm.dispatchEvent(new Event('submit'));
            }
        });
    }

    // Helper function to add delete form listener
    function addDeleteFormListener(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            if (confirm('Êtes-vous sûr de vouloir supprimer ce message?')) {
                const formData = new FormData(form);
                const url = form.getAttribute('action');

                fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                        if (data.success) {
                        // Find the message item
                        const messageItem = form.closest('.message-item');
                            const messageContent = messageItem.querySelector('.message-content');

                        // Update content
                            messageContent.textContent = 'Ce message a été supprimé';
                            messageContent.classList.add('message-deleted');

                        // Hide delete button
                        const messageActions = messageItem.querySelector('.message-actions');
                        if (messageActions) {
                            messageActions.style.display = 'none';
                        }
                    }
                })
                .catch(error => {
                        console.error('Error deleting message:', error);
                    alert('Erreur lors de la suppression du message.');
                });
            }
            });
        }

    // Function to initialize message handlers
    window.initMessageHandlers = function() {
        const newDeleteForms = document.querySelectorAll('.delete-message-form');
        newDeleteForms.forEach(form => {
            addDeleteFormListener(form);
        });
    };

    // Initialize handlers on load
    initMessageHandlers();
});

// Function to delete a message
function deleteMessage(messageId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce message?')) {
        // Create form data with CSRF token and method
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('_method', 'DELETE');

        // Send delete request
        fetch(`{{ url('/user/messages') }}/${messageId}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Find the message element
                const messageElement = document.getElementById(`message-${messageId}`);
                if (messageElement) {
                    // Find the message content and update it
                    const contentElement = messageElement.querySelector('.message-content');
                    if (contentElement) {
                        contentElement.textContent = 'Ce message a été supprimé';
                        contentElement.classList.add('message-deleted');
                    }

                    // Hide the delete button
                    const actionsElement = messageElement.querySelector('.message-actions');
                    if (actionsElement) {
                        actionsElement.style.display = 'none';
                    }
                }
            } else {
                alert('Erreur lors de la suppression du message');
                }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erreur lors de la suppression du message');
        });
    }
}
</script>
