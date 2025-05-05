<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Assistant de Réclamation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa, #e4e7eb);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .chatbot-container {
            max-width: 460px;
            margin: 40px auto;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            padding: 0;
            overflow: hidden;
            border: 1px solid rgba(0,0,0,0.05);
        }
        .chat-header {
    background: #fff;
    color: #232323;
    border-radius: 20px 20px 0 0;
    padding: 18px 24px 18px 18px;
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
    width: auto;
    margin-right: 18px;
    margin-left: 2px;
    vertical-align: middle;
    flex-shrink: 0;
}
        .chat-header:before { display: none; }
        .chat-body {
            padding: 20px;
            min-height: 380px;
            max-height: 450px;
            overflow-y: auto;
            background-color: #f9fafc;
        }
        .chat-bubble {
    display: inline-block;
    padding: 12px 18px;
    border-radius: 18px;
    margin-bottom: 12px;
    max-width: 85%;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    animation: fadeIn 0.3s ease-in-out;
    color: #fff;
    font-weight: 500;
    background: none;
}
.bot .chat-bubble {
    background: linear-gradient(90deg, #8E54E9, #6C2BD7);
    color: #fff;
}
.user .chat-bubble {
    background: linear-gradient(90deg, #4776E6, #329DFF);
    color: #fff;
}
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .bot {
    background: transparent;
    color: #fff;
    align-self: flex-start;
    border-top-left-radius: 4px;
}
.user {
    background: transparent;
    color: #fff;
    align-self: flex-end;
    border-top-right-radius: 4px;
}
        .chat-row {
            display: flex;
            flex-direction: row;
            margin-bottom: 10px;
        }
        .chat-row.user { justify-content: flex-end; }
        .chat-row.bot { justify-content: flex-start; }
        .chat-footer {
            padding: 15px 20px;
            border-top: 1px solid #eaedf3;
            background: #fff;
            border-radius: 0 0 20px 20px;
            display: flex;
            align-items: center;
        }
        .chat-input {
            width: 75%;
            border-radius: 30px;
            border: 1px solid #ddd;
            padding: 10px 18px;
            font-size: 0.95rem;
            transition: all 0.2s;
            box-shadow: 0 2px 5px rgba(0,0,0,0.02) inset;
        }
        .chat-input:focus {
            outline: none;
            border-color: #8E54E9;
            box-shadow: 0 0 0 3px rgba(142, 84, 233, 0.1);
        }
        .chat-btn {
            border-radius: 30px;
            background: linear-gradient(90deg, #4776E6, #8E54E9);
            color: #fff;
            border: none;
            padding: 10px 20px;
            margin-left: 10px;
            font-weight: 600;
            box-shadow: 0 3px 10px rgba(71, 118, 230, 0.2);
            transition: all 0.2s;
        }
        .chat-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(71, 118, 230, 0.3);
        }
        .chat-btn:active {
            transform: translateY(0);
        }
        .chat-btn:disabled {
            background: #d4d8e3;
            transform: none;
            box-shadow: none;
        }
        .file-input { display: none; }
        .file-label {
            color: #4776E6;
            cursor: pointer;
            margin-left: 8px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            transition: all 0.2s;
        }
        .file-label:hover {
            color: #8E54E9;
        }
    </style>
</head>
<body>
<div class="chatbot-container">
    <div class="chat-header">
    <img src="/images/logo.jpg" alt="Tuniship Logo" class="chat-header-logo" />
    <span style="margin-left: 6px; font-size: 1.18em; font-weight: 600;">Assistant de Réclamation</span>
</div>
    <div class="chat-body" id="chatBody"></div>
    <div class="chat-footer" id="chatFooter">
        <form id="chatbotForm" autocomplete="off" enctype="multipart/form-data">
            <input type="text" class="chat-input" id="chatInput" placeholder="Votre réponse..." autofocus required>
            <button type="submit" class="chat-btn">Envoyer</button>
            <label for="fileInput" class="file-label" id="fileLabel" style="display:none;"> Joindre un fichier</label>
            <label for="fileInput" class="file-label" id="fileLabel" style="display:none;">📎 Joindre un fichier</label>
            <input type="file" id="fileInput" class="file-input" name="piece_jointe" accept="image/*,application/pdf">
        </form>
    </div>
</div>
<script>
const chatBody = document.getElementById('chatBody');
const chatInput = document.getElementById('chatInput');
const chatForm = document.getElementById('chatbotForm');
const fileInput = document.getElementById('fileInput');
const fileLabel = document.getElementById('fileLabel');

const questions = [
    { key: 'nom', text: "Bonjour ! Quel est votre nom ?" },
    { key: 'company_name', text: "Nom de l'entreprise (optionnel) ? Si vous n'en avez pas, tapez 'non' ou laissez vide." },
    { key: 'email', text: "Merci __nom__. Quelle est votre adresse email ?" },
    { key: 'sujet', text: "Quel est le sujet de votre réclamation ?", type: 'select', options: [
        { value: 'retard_livraison', label: 'Retard de livraison' },
        { value: 'retard_chargement', label: 'Retard de chargement' },
        { value: 'marchandise_endommagée', label: 'Marchandise endommagée' },
        { value: 'mauvais_comportement', label: 'Mauvais comportement' },
        { value: 'autre', label: 'Autre' }
    ]},
    { key: 'description', text: "Pouvez-vous décrire votre problème ?" },
    { key: 'urgence', text: "Quel est le niveau d'urgence ? (Critique, Élevée, Moyenne, Faible)", type: 'select', options: [
        { value: 'critical', label: 'Critique' },
        { value: 'high', label: 'Élevée' },
        { value: 'medium', label: 'Moyenne' },
        { value: 'low', label: 'Faible' }
    ]},
    { key: 'piece_jointe', text: "Voulez-vous joindre un fichier ? (image/pdf) Si oui, cliquez sur 📎 Joindre un fichier, sinon tapez 'non'.", type: 'file' }
];

let answers = {};
let step = 0;

function addBubble(text, sender = 'bot') {
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
    if (q.type === 'select') {
        chatInput.style.display = 'none';
        // Use different function based on the question key
        if (q.key === 'sujet') {
            showSubjectOptions(q.options);
        } else if (q.key === 'urgence') {
            showUrgencyOptions(q.options);
        }
    }
    if (q.type === 'file') {
        chatInput.style.display = '';
        chatInput.placeholder = "Tapez 'non' ou cliquez sur 📎 Joindre un fichier";
        fileLabel.style.display = '';
    }
}

function showUrgencyOptions(options) {
    const row = document.createElement('div');
    row.className = 'chat-row bot';
    options.forEach(opt => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'chat-btn';
        btn.style.marginRight = '7px';
        btn.textContent = opt.label;
        btn.onclick = () => {
            addBubble(opt.label, 'user');
            // Store the value, not the label
            answers['urgence'] = opt.value;
            step++;
            setTimeout(askQuestion, 600);
        };
        row.appendChild(btn);
    });
    chatBody.appendChild(row);
    chatBody.scrollTop = chatBody.scrollHeight;
}

// Add the matching subject selection function with consistent styling
function showSubjectOptions(options) {
    const container = document.createElement('div');
    container.style.display = 'flex';
    container.style.flexDirection = 'column';
    container.style.gap = '10px';
    // First row: 3 buttons
    const row1 = document.createElement('div');
    row1.className = 'chat-row bot';
    for (let i = 0; i < 3; i++) {
        const opt = options[i];
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'chat-btn';
        btn.style.marginRight = '7px';
        btn.textContent = opt.label;
        btn.onclick = () => {
            addBubble(opt.label, 'user');
            answers['sujet'] = opt.value;
            step++;
            setTimeout(askQuestion, 600);
        };
        row1.appendChild(btn);
    }
    // Second row: 2 buttons
    const row2 = document.createElement('div');
    row2.className = 'chat-row bot';
    for (let i = 3; i < options.length; i++) {
        const opt = options[i];
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'chat-btn';
        btn.style.marginRight = '7px';
        btn.textContent = opt.label;
        btn.onclick = () => {
            addBubble(opt.label, 'user');
            answers['sujet'] = opt.value;
            step++;
            setTimeout(askQuestion, 600);
        };
        row2.appendChild(btn);
    }
    container.appendChild(row1);
    container.appendChild(row2);
    chatBody.appendChild(container);
    chatBody.scrollTop = chatBody.scrollHeight;
}

fileLabel.onclick = () => {
    fileInput.click();
};

fileInput.onchange = () => {
    if (fileInput.files.length > 0) {
        addBubble('Fichier joint : ' + fileInput.files[0].name, 'user');
        answers['piece_jointe'] = fileInput.files[0];
        step++;
        setTimeout(askQuestion, 600);
    }
};

chatForm.onsubmit = async function(e) {
    e.preventDefault();
    let q = questions[step];
    let val = chatInput.value.trim();
    // --- Name extraction for French phrases ---
    if (q.key === 'nom') {
        // Remove common French phrases
        let regexes = [
            /^mon nom est\s+/i,
            /^je m'appelle\s+/i,
            /^je suis\s+/i,
            /^c'est\s+/i,
            /^moi c'est\s+/i
        ];
        regexes.forEach(rgx => {
            if (rgx.test(val)) {
                val = val.replace(rgx, '').trim();
            }
        });
        // Only keep the LAST word (assume it's the first or last name)
        let parts = val.split(' ').filter(Boolean);
        if (parts.length > 1) {
            addBubble("Merci d'entrer uniquement votre prénom ou nom (un seul mot).", 'bot');
            return;
        }
        val = parts[0] || '';
        if (!val) {
            addBubble("Merci d'entrer uniquement votre prénom ou nom.", 'bot');
            return;
        }
    }
    // --- Email validation ---
    if (q.key === 'email') {
        let emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        if (!emailPattern.test(val)) {
            addBubble("L'adresse email saisie n'est pas valide. Veuillez entrer une adresse email correcte.", 'bot');
            return;
        }
    }
    if (q.type === 'file') {
        if (val.toLowerCase() === 'non') {
            addBubble('Non', 'user');
            answers['piece_jointe'] = null;
            step++;
            setTimeout(submitComplaint, 600);
            return;
        }
        // Otherwise, wait for file input
        return;
    }
    if (!val && q.type !== 'file') {
        addBubble("Ce champ est requis. Veuillez répondre.", 'bot');
        return;
    }
    addBubble(val, 'user');
    answers[q.key] = val;
    step++;
    // Debug: log current step and key
    console.log('After answer, step:', step, 'key:', questions[step] ? questions[step].key : 'END');
    // Use a loop to skip any unnecessary steps (like file upload if not needed)
    while (
        step < questions.length &&
        questions[step].key === 'piece_jointe' &&
        answers['sujet'] !== 'marchandise_endommagée'
    ) {
        console.log('Skipping piece_jointe step');
        step++;
    }
    // If we've reached the end, submit
    if (step >= questions.length) {
        console.log('Calling submitComplaint()');
        setTimeout(submitComplaint, 600);
    } else {
        setTimeout(askQuestion, 600);
    }
};

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
        console.warn('Invalid sujet value:', sujetValue, 'defaulting to "autre"');
        sujetValue = 'autre';
    }
    formData.append('sujet', sujetValue);

    formData.append('description', answers['description'] || '');
    formData.append('urgence', answers['urgence'] || '');

    // Log each form field for debugging
    console.log('nom:', answers['nom']);
    console.log('email:', answers['email']);
    console.log('sujet:', sujetValue); // Log the validated value
    console.log('description:', answers['description']);
    console.log('urgence:', answers['urgence']);

    if (answers['piece_jointe'] instanceof File) {
        formData.append('piece_jointe', answers['piece_jointe']);
        console.log('piece_jointe:', answers['piece_jointe'].name);
    }
    fetch('/reclamation-assistant', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(async res => {
        let data;
        try {
            data = await res.json();
        } catch (e) {
            addBubble('❌ Erreur technique : réponse inattendue du serveur.', 'bot');
            return;
        }
        if (data.success) {
            addBubble('✅ Merci ! Votre réclamation a été envoyée avec succès. Nous vous contacterons bientôt.', 'bot');
            chatFooter.innerHTML = '';
        } else {
            addBubble('❌ Une erreur est survenue : ' + (data.message || 'Veuillez réessayer.'), 'bot');
        }
    })
    .catch((err) => {
        addBubble('❌ Une erreur technique est survenue : ' + (err.message || 'Veuillez réessayer.'), 'bot');
    });
}

// Start the conversation
askQuestion();
</script>
</body>
</html>

