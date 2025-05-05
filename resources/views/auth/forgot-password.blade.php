<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Mot de passe oublié</title>
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            body {
                background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
                min-height: 100vh;
            }
            .card {
                border: none;
                border-radius: 15px;
                box-shadow: 0 0 20px rgba(0,0,0,0.1);
            }
            .form-control:focus {
                box-shadow: none;
                border-color: #4a5568;
            }
            .btn-primary {
                background-color: #4a5568;
                border-color: #4a5568;
            }
            .btn-primary:hover {
                background-color: #2d3748;
                border-color: #2d3748;
            }
            .form-floating > label {
                color: #718096;
            }
            .card-header {
                background-color: transparent;
                border-bottom: none;
                padding-bottom: 0;
            }
            .welcome-text {
                color: #2d3748;
                font-weight: 300;
                margin-bottom: 1.5rem;
            }
            .logo-container {
                margin-bottom: 1.5rem;
                text-align: center;
            }
            .logo-container img {
                max-width: 200px;
                height: auto;
                margin-bottom: 1rem;
            }
            .instructions {
                margin-bottom: 1.5rem;
                color: #718096;
                text-align: center;
            }
            .alert {
                border-radius: 10px;
                margin-bottom: 1.5rem;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="row justify-content-center align-items-center min-vh-100">
                <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                    <div class="card">
                        <div class="card-header text-center pt-4">
                            <div class="logo-container">
                                <img src="/images/logo.jpg" alt="Tuniship Logo" class="img-fluid">
                            </div>
                            <h4 class="welcome-text">Mot de passe oublié</h4>
                            <p class="instructions">
                                Indiquez-nous simplement votre adresse e-mail et nous vous enverrons un lien de réinitialisation qui vous permettra d'en choisir un nouveau.
                            </p>
                        </div>
                        <div class="card-body p-4">
                            @if (session('status'))
                                <div class="alert alert-success" role="alert">
                                    {{ session('status') }}
                                </div>
                            @endif

                            <form method="POST" action="{{ route('password.email') }}">
                                @csrf

                                <div class="form-floating mb-3">
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required autofocus oninvalid="this.setCustomValidity('Veuillez entrer votre adresse email')" oninput="this.setCustomValidity('')" title="Veuillez entrer votre adresse email">
                                    <label for="email">Adresse email</label>
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        Envoyer le lien de réinitialisation
                                    </button>
                                </div>

                                <div class="text-center mt-3">
                                    <a href="{{ route('login') }}" class="text-decoration-none">Retour à la connexion</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bootstrap Bundle with Popper -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            // Traduction des messages d'erreur de validation HTML5
            document.addEventListener('DOMContentLoaded', function() {
                // Version française des messages de validation
                const messages = {
                    valueMissing: {
                        email: 'Veuillez entrer une adresse email',
                        text: 'Veuillez remplir ce champ',
                    },
                    typeMismatch: {
                        email: 'Veuillez inclure un "@" dans l\'adresse email'
                    }
                };

                // Surcharge de la méthode checkValidity pour personnaliser les messages
                const inputs = document.querySelectorAll('input, select, textarea');
                inputs.forEach(input => {
                    input.addEventListener('invalid', function(e) {
                        if (e.target.validity.valueMissing) {
                            const type = e.target.type;
                            e.target.setCustomValidity(
                                messages.valueMissing[type] || messages.valueMissing.text
                            );
                        } else if (e.target.validity.typeMismatch && e.target.type === 'email') {
                            e.target.setCustomValidity(messages.typeMismatch.email);
                        }
                    });

                    input.addEventListener('input', function(e) {
                        e.target.setCustomValidity('');
                    });
                });
            });
        </script>
    </body>
</html>
