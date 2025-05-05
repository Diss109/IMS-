<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $messages = [
            'email.required' => 'Le champ adresse email est obligatoire.',
            'email.email' => 'L\'adresse email doit être une adresse email valide.',
        ];

        $request->validate([
            'email' => ['required', 'email'],
        ], $messages);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            $request->only('email')
        );

        // Traductions personnalisées pour les messages de statut
        $customStatusMessages = [
            Password::RESET_LINK_SENT => 'Nous vous avons envoyé par email le lien de réinitialisation du mot de passe !',
            Password::INVALID_USER => 'Aucun utilisateur n\'a été trouvé avec cette adresse email.',
            Password::RESET_THROTTLED => 'Veuillez attendre avant de réessayer.',
        ];

        $message = $customStatusMessages[$status] ?? 'Une erreur est survenue.';

        return $status == Password::RESET_LINK_SENT
                    ? back()->with('status', $message)
                    : back()->withInput($request->only('email'))
                        ->withErrors(['email' => $message]);
    }
}
