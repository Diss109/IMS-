<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réinitialisation du mot de passe</title>
</head>
<body style="font-family: Arial, sans-serif; background: #f6f6f6; padding: 0; margin: 0;">
    <div style="max-width: 500px; margin: 40px auto; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); padding: 32px;">

        <h2 style="color: #1a202c;">Réinitialisation du mot de passe</h2>
        <p>Bonjour,</p>
        <p>Vous recevez cet e-mail parce que nous avons reçu une demande de réinitialisation du mot de passe pour votre compte.</p>
        <div style="text-align: center; margin: 24px 0;">
            <a href="{{ $url }}" style="background: #3490dc; color: #fff; text-decoration: none; padding: 12px 24px; border-radius: 4px; font-weight: bold;">Réinitialiser le mot de passe</a>
        </div>
        <p>Ce lien de réinitialisation du mot de passe expirera dans {{ config('auth.passwords.'.config('auth.defaults.passwords').'.expire') }} minutes.</p>
        <p>Si vous n'avez pas demandé de réinitialisation du mot de passe, aucune action supplémentaire n'est requise.</p>
        <p style="margin-top: 32px;">Cordialement,<br>L'équipe Tuniship</p>
    </div>
</body>
</html>
