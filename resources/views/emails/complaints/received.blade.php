<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo img {
            max-width: 200px;
        }
        .content {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="{{ asset('images/logo.jpg') }}" alt="Tuniship Logo">
        </div>

        <div class="content">
            <h2>Confirmation de réception de votre réclamation</h2>

            <p>Cher/Chère {{ $complaint->first_name }} {{ $complaint->last_name }},</p>

            <p>Nous avons bien reçu votre réclamation concernant {{ trans('complaints.types.' . $complaint->complaint_type) }}.
               Votre demande est en cours de traitement par notre équipe.</p>

            <p><strong>Détails de la réclamation :</strong></p>
            <ul>
                <li>Numéro de référence : #{{ $complaint->id }}</li>
                <li>Type : {{ trans('complaints.types.' . $complaint->complaint_type) }}</li>
                <li>Niveau d'urgence : {{ trans('complaints.urgency.' . $complaint->urgency_level) }}</li>
                <li>Date de soumission : {{ $complaint->created_at->format('d/m/Y H:i') }}</li>
            </ul>

            <p>Notre équipe examinera votre réclamation dans les plus brefs délais et vous tiendra informé
               de l'avancement de son traitement.</p>

            <p>Si vous avez des questions ou des informations supplémentaires à nous communiquer,
               n'hésitez pas à répondre à cet email.</p>

            <p>Cordialement,<br>
            L'équipe Tuniship</p>
        </div>

        <div class="footer">
            <p>Cet email a été envoyé automatiquement, merci de ne pas y répondre directement.</p>
            <p>© {{ date('Y') }} Tuniship. Tous droits réservés.</p>
        </div>
    </div>
</body>
</html>
