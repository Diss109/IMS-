<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Notifications\Messages\MailMessage;

class CustomResetPassword extends ResetPasswordNotification
{
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Réinitialisation du mot de passe')
            ->greeting('Bonjour,')
            ->line('Vous recevez cet e-mail parce que nous avons reçu une demande de réinitialisation du mot de passe pour votre compte.')
            ->action('Réinitialiser le mot de passe', url(config('app.url').route('password.reset', $this->token, false)))
            ->line("Ce lien de réinitialisation du mot de passe expirera dans ".config('auth.passwords.'.config('auth.defaults.passwords').'.expire')." minutes.")
            ->line("Si vous n'avez pas demandé de réinitialisation du mot de passe, aucune action supplémentaire n'est requise.")
            ->salutation('Cordialement,')
            ->view('emails.custom-reset-password', ['url' => url(config('app.url').route('password.reset', $this->token, false))]);
    }
}
