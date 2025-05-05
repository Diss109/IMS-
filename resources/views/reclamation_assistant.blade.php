<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Assistant de Réclamation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4361ee;
            --primary-gradient: linear-gradient(135deg, #4361ee, #3a56d4);
            --secondary-color: #6c63ff;
            --secondary-gradient: linear-gradient(135deg, #6c63ff, #5a56e0);
            --assistant-color: #f0f2f5;
            --assistant-gradient: linear-gradient(135deg, #f0f2f5, #e4e6eb);
            --light-bg: #f8f9fd;
            --border-radius: 16px;
            --box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        body {
            background: linear-gradient(135deg, #e6ecfd, #f8f9fd);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .chatbot-container {
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
            background: #fff;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 0;
            overflow: hidden;
            border: 1px solid rgba(0,0,0,0.05);
            height: 90vh;
            max-height: 700px;
            display: flex;
            flex-direction: column;
        }

        .chat-header {
            background: var(--primary-gradient);
            color: #fff;
            border-radius: var(--border-radius) var(--border-radius) 0 0;
            padding: 20px;
            display: flex;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: relative;
            text-align: left;
            font-size: 1.3rem;
            font-weight: 600;
        }

        .chat-header-logo {
            height: 48px;
            width: 48px;
            margin-right: 16px;
            border-radius: 50%;
            object-fit: cover;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
            border: 2px solid rgba(255, 255, 255, 0.5);
        }

        .chat-body {
            padding: 24px;
            flex: 1;
            overflow-y: auto;
            background-color: var(--light-bg);
            scrollbar-width: thin;
            scrollbar-color: rgba(0,0,0,0.2) rgba(0,0,0,0.05);
        }

        .chat-body::-webkit-scrollbar {
            width: 6px;
        }

        .chat-body::-webkit-scrollbar-track {
            background: rgba(0,0,0,0.05);
            border-radius: 10px;
        }

        .chat-body::-webkit-scrollbar-thumb {
            background: rgba(0,0,0,0.2);
            border-radius: 10px;
        }

        .chat-bubble {
            display: inline-block;
            padding: 14px 20px;
            border-radius: 18px;
            margin-bottom: 16px;
            max-width: 85%;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            animation: fadeIn 0.3s ease-in-out;
            color: #fff;
            font-weight: 500;
            line-height: 1.5;
            font-size: 0.95rem;
        }

        .bot .chat-bubble {
            background: var(--assistant-gradient);
            color: #2d3748;
            border-bottom-left-radius: 4px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }

        .user .chat-bubble {
            background: var(--primary-gradient);
            color: #fff;
            border-bottom-right-radius: 4px;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .chat-row {
            display: flex;
            flex-direction: row;
            margin-bottom: 16px;
            position: relative;
        }

        .chat-row.user { justify-content: flex-end; }
        .chat-row.bot { justify-content: flex-start; }

        .chat-row.bot::before {
            content: '';
            display: block;
            width: 32px;
            height: 32px;
            background-image: url('/images/logo.jpg');
            background-size: cover;
            border-radius: 50%;
            margin-right: 12px;
            align-self: flex-end;
            border: 2px solid #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .chat-row.user::after {
            content: '\f007';
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            background: #4361ee;
            color: white;
            border-radius: 50%;
            margin-left: 12px;
            align-self: flex-end;
            border: 2px solid #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .chat-footer {
            padding: 20px;
            border-top: 1px solid rgba(0,0,0,0.05);
            background: #fff;
            border-radius: 0 0 var(--border-radius) var(--border-radius);
        }

        .chat-form {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .chat-input {
            flex: 1;
            border-radius: 30px;
            border: 1px solid rgba(0,0,0,0.1);
            padding: 14px 20px;
            font-size: 0.95rem;
            transition: var(--transition);
            box-shadow: 0 2px 5px rgba(0,0,0,0.02) inset;
        }

        .chat-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
        }

        .chat-btn {
            border-radius: 30px;
            background: var(--primary-gradient);
            color: #fff;
            border: none;
            padding: 12px 24px;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(67, 97, 238, 0.2);
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .chat-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(67, 97, 238, 0.3);
        }

        .chat-btn:active {
            transform: translateY(0);
        }

        .chat-btn:disabled {
            background: linear-gradient(135deg, #d4d4d4, #a9a9a9);
            transform: none;
            box-shadow: none;
            cursor: not-allowed;
        }

        .file-input {
            display: none;
        }

        .file-btn {
            width: 46px;
            height: 46px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            border: 1px solid rgba(0,0,0,0.1);
            background: white;
            color: var(--primary-color);
            cursor: pointer;
            transition: var(--transition);
            font-size: 1.2rem;
        }

        .file-btn:hover {
            background: rgba(67, 97, 238, 0.1);
            transform: translateY(-2px);
        }

        .option-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 16px;
            justify-content: center;
        }

        .option-btn {
            border: none;
            background: white;
            border-radius: 30px;
            padding: 10px 20px;
            font-size: 0.9rem;
            cursor: pointer;
            transition: var(--transition);
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            border: 1px solid rgba(0,0,0,0.08);
            color: var(--primary-color);
            font-weight: 500;
        }

        .option-btn:hover {
            background: rgba(67, 97, 238, 0.1);
            transform: translateY(-2px);
        }

        .option-btn.active, .option-btn:active {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .typing-indicator {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 8px 16px;
            background: rgba(0,0,0,0.05);
            border-radius: 30px;
            font-size: 0.85rem;
            color: rgba(0,0,0,0.6);
            width: fit-content;
            margin-bottom: 16px;
        }

        .typing-dot {
            width: 8px;
            height: 8px;
            background: rgba(0,0,0,0.3);
            border-radius: 50%;
            animation: typingAnimation 1.4s infinite;
        }

        .typing-dot:nth-child(2) {
            animation-delay: 0.2s;
        }

        .typing-dot:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes typingAnimation {
            0%, 100% { opacity: 0.3; transform: translateY(0); }
            50% { opacity: 1; transform: translateY(-5px); }
        }

        .file-preview {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 16px;
            border-radius: 8px;
            background: rgba(67, 97, 238, 0.1);
            margin-top: 16px;
        }

        .file-preview-icon {
            font-size: 1.5rem;
            color: var(--primary-color);
        }

        .file-preview-info {
            flex: 1;
        }

        .file-preview-name {
            font-weight: 500;
            font-size: 0.9rem;
            margin-bottom: 4px;
            color: var(--primary-color);
        }

        .file-preview-size {
            font-size: 0.8rem;
            color: rgba(0,0,0,0.5);
        }

        .file-preview-remove {
            color: #dc3545;
            cursor: pointer;
            font-size: 1.2rem;
            transition: var(--transition);
        }

        .file-preview-remove:hover {
            transform: scale(1.2);
        }
    </style>
</head>
<body>
<div class="chatbot-container">
    <div class="chat-header">
        <img src="/images/logo.jpg" alt="Tuniship Logo" class="chat-header-logo" />
        <div>
            <div>Assistant de Réclamation</div>
            <div style="font-size: 0.8rem; font-weight: 400; opacity: 0.8;">Service client Tuniship</div>
        </div>
    </div>
    <div class="chat-body" id="chatBody"></div>
    <div class="chat-footer" id="chatFooter">
        <form id="chatbotForm" class="chat-form" autocomplete="off" enctype="multipart/form-data">
            <input type="text" class="chat-input" id="chatInput" placeholder="Votre réponse..." autofocus required>
            <label for="fileInput" class="file-btn" id="fileLabel" style="display:none;">
                <i class="fas fa-paperclip"></i>
            </label>
            <input type="file" id="fileInput" class="file-input" name="piece_jointe" accept="image/*,application/pdf">
            <button type="submit" class="chat-btn">
                <i class="fas fa-paper-plane"></i>
                <span>Envoyer</span>
            </button>
        </form>
        <div id="filePreview" style="display: none;"></div>
    </div>
</div>
<script>
const chatBody = document.getElementById('chatBody');
const chatInput = document.getElementById('chatInput');
const chatForm = document.getElementById('chatbotForm');
const fileInput = document.getElementById('fileInput');
const fileLabel = document.getElementById('fileLabel');
const filePreview = document.getElementById('filePreview');

const questions = [
    { key: 'nom', text: "Bonjour ! 👋 Je suis l'assistant de réclamation Tuniship. Quel est votre nom ?" },
    { key: 'company_name', text: "Merci __nom__. Travaillez-vous pour une entreprise ? Si oui, pourriez-vous indiquer son nom ? (Si non, tapez 'non' ou laissez vide)" },
    { key: 'email', text: "Parfait ! Quelle est votre adresse email pour vous contacter ?" },
    { key: 'sujet', text: "Quel est le sujet de votre réclamation ?", type: 'select', options: [
        { value: 'retard_livraison', label: 'Retard de livraison' },
        { value: 'retard_chargement', label: 'Retard de chargement' },
        { value: 'marchandise_endommagée', label: 'Marchandise endommagée' },
        { value: 'mauvais_comportement', label: 'Mauvais comportement' },
        { value: 'autre', label: 'Autre' }
    ]},
    { key: 'description', text: "Pouvez-vous décrire votre problème plus en détail ? N'hésitez pas à fournir toutes les informations qui pourraient nous aider à résoudre votre réclamation." },
    { key: 'urgence', text: "Quel est le niveau d'urgence de votre réclamation ?", type: 'select', options: [
        { value: 'critical', label: '🔴 Critique - Nécessite une attention immédiate' },
        { value: 'high', label: '🟠 Élevée - Importante et urgente' },
        { value: 'medium', label: '🟡 Moyenne - À traiter dans les délais normaux' },
        { value: 'low', label: '🟢 Faible - Peut être traitée ultérieurement' }
    ]},
    { key: 'piece_jointe', text: "Souhaitez-vous joindre un document ou une photo pour illustrer votre réclamation ? Si oui, utilisez le bouton 📎, sinon tapez 'non'.", type: 'file' }
];

let answers = {};
let step = 0;

// Show typing indicator before showing a bot message
function showTypingIndicator() {
    const typingIndicator = document.createElement('div');
    typingIndicator.className = 'typing-indicator';
    typingIndicator.id = 'typing-indicator';
    typingIndicator.innerHTML = `<div class="typing-dot"></div><div class="typing-dot"></div><div class="typing-dot"></div>`;
    chatBody.appendChild(typingIndicator);
    chatBody.scrollTop = chatBody.scrollHeight;
}

// Remove typing indicator
function removeTypingIndicator() {
    const typingIndicator = document.getElementById('typing-indicator');
    if (typingIndicator) {
        typingIndicator.remove();
    }
}

function addBubble(text, sender = 'bot') {
    removeTypingIndicator();
    const row = document.createElement('div');
    row.className = 'chat-row ' + sender;
    const bubble = document.createElement('div');
    bubble.className = 'chat-bubble ' + sender;
    bubble.innerHTML = text;
    row.appendChild(bubble);
    chatBody.appendChild(row);
    chatBody.scrollTop = chatBody.scrollHeight;
}

function askQuestion() {
    // GUARD: If no more questions, submit
    if (step >= questions.length) {
        console.log('askQuestion: step >= questions.length, calling submitComplaint()');
        submitComplaint();
        return;
    }
    // Special logic: Only show file upload if sujet === 'marchandise_endommagée'
    if (questions[step].key === 'piece_jointe') {
        if (answers['sujet'] !== 'marchandise_endommagée') {
            // Skip file upload step
            step++;
            askQuestion();
            return;
        }
    }

    showTypingIndicator();

    setTimeout(() => {
        let q = questions[step];
        let text = q.text;
        // Replace placeholders
        Object.keys(answers).forEach(k => {
            text = text.replace(`__${k}__`, answers[k]);
        });
        addBubble(text, 'bot');
        chatInput.value = '';
        chatInput.type = 'text';
        chatInput.style.display = '';
        fileLabel.style.display = 'none';
        fileInput.value = '';
        filePreview.style.display = 'none';
        filePreview.innerHTML = '';

        if (q.type === 'select') {
            chatInput.style.display = 'none';
            // Use different function based on the question key
            if (q.key === 'sujet') {
                showSubjectOptions(q.options);
            } else if (q.key === 'urgence') {
                showUrgencyOptions(q.options);
            }
        } else if (q.type === 'file') {
            fileLabel.style.display = 'flex';
        }
    }, 1000);
}

function showSubjectOptions(options) {
    const container = document.createElement('div');
    container.className = 'option-buttons';
    container.id = 'optionButtons';
    options.forEach(option => {
        const button = document.createElement('button');
        button.className = 'option-btn';
        button.textContent = option.label;
        button.value = option.value;
        button.addEventListener('click', function() {
            answers['sujet'] = this.value;
            addBubble(this.textContent, 'user');
            container.remove();
            step++;
            askQuestion();
        });
        container.appendChild(button);
    });
    chatBody.appendChild(container);
    chatBody.scrollTop = chatBody.scrollHeight;
}

function showUrgencyOptions(options) {
    const container = document.createElement('div');
    container.className = 'option-buttons';
    container.id = 'optionButtons';
    options.forEach(option => {
        const button = document.createElement('button');
        button.className = 'option-btn';
        button.innerHTML = option.label;
        button.value = option.value;
        button.addEventListener('click', function() {
            answers['urgence'] = this.value;
            addBubble(this.innerHTML, 'user');
            container.remove();
            step++;
            askQuestion();
        });
        container.appendChild(button);
    });
    chatBody.appendChild(container);
    chatBody.scrollTop = chatBody.scrollHeight;
}

// Handle file preview
fileInput.addEventListener('change', function() {
    if (this.files && this.files[0]) {
        const file = this.files[0];
        const fileName = file.name;
        const fileSize = (file.size / 1024).toFixed(2) + ' KB';
        const isImage = file.type.match('image.*');

        filePreview.innerHTML = `
            <div class="file-preview">
                <div class="file-preview-icon">
                    <i class="fas ${isImage ? 'fa-image' : 'fa-file-pdf'}"></i>
                </div>
                <div class="file-preview-info">
                    <div class="file-preview-name">${fileName}</div>
                    <div class="file-preview-size">${fileSize}</div>
                </div>
                <div class="file-preview-remove" id="removeFile">
                    <i class="fas fa-times-circle"></i>
                </div>
            </div>
        `;
        filePreview.style.display = 'block';

        document.getElementById('removeFile').addEventListener('click', function() {
            fileInput.value = '';
            filePreview.style.display = 'none';
            filePreview.innerHTML = '';
        });

        // Add the file response
        if (step < questions.length && questions[step].key === 'piece_jointe') {
            answers['piece_jointe'] = file.name;
            addBubble(`Fichier joint: ${file.name}`, 'user');
            step++;
            askQuestion();
        }
    }
});

chatForm.addEventListener('submit', function(e) {
    e.preventDefault();
    // Handle file inputs
    if (step < questions.length && questions[step].key === 'piece_jointe') {
        if (fileInput.files.length === 0 && chatInput.value.toLowerCase() === 'non') {
            answers['piece_jointe'] = 'non';
            addBubble('Non', 'user');
            step++;
            askQuestion();
        }
        return;
    }

    // Normal text inputs
    const text = chatInput.value.trim();
    if (!text) return;

    addBubble(text, 'user');

    if (step < questions.length) {
        answers[questions[step].key] = text;
        step++;
        askQuestion();
    }
});

function submitComplaint() {
    addBubble('⏳ Envoi de la réclamation...', 'bot');
    // For debugging
    console.log('Submitting answers:', JSON.stringify(answers));

    const formData = new FormData();
    formData.append('nom', answers['nom'] || '');
    formData.append('company_name', (answers['company_name'] && answers['company_name'].toLowerCase() !== 'non') ? answers['company_name'] : '');
    formData.append('email', answers['email'] || '');

    // Make sure the sujet value is valid before submission
    let sujetValue = answers['sujet'] || 'autre';
    // Validate that the sujet is one of the allowed values
    const validSujets = ['retard_livraison', 'retard_chargement', 'marchandise_endommagée', 'mauvais_comportement', 'autre'];
    if (!validSujets.includes(sujetValue)) {
        sujetValue = 'autre';
    }
    formData.append('sujet', sujetValue);

    formData.append('description', answers['description'] || '');
    formData.append('urgence', answers['urgence'] || 'medium');

    // Handle file attachment if present
    if (fileInput.files.length > 0) {
        formData.append('piece_jointe', fileInput.files[0]);
    }

    fetch('/reclamation-assistant', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Erreur de serveur');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            addBubble('✅ Votre réclamation a été envoyée avec succès ! Nous vous contacterons très bientôt. Merci de votre confiance.', 'bot');
            // Reset form
            chatForm.style.display = 'none';
            // Add a button to start over
            const restartBtn = document.createElement('button');
            restartBtn.className = 'chat-btn';
            restartBtn.innerHTML = '<i class="fas fa-redo-alt"></i> Nouvelle réclamation';
            restartBtn.style.margin = '0 auto';
            restartBtn.style.display = 'block';
            chatFooter.appendChild(restartBtn);
            restartBtn.addEventListener('click', function() {
                window.location.reload();
            });
        } else {
            addBubble(`❌ Erreur: ${data.message || 'Une erreur est survenue lors de l\'envoi de votre réclamation.'}`, 'bot');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        addBubble(`❌ Erreur: ${error.message || 'Une erreur est survenue lors de l\'envoi de votre réclamation. Veuillez réessayer.'}`, 'bot');
    });
}

// Start the conversation
setTimeout(() => {
    askQuestion();
}, 500);
</script>
</body>
</html>

