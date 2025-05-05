<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Réinitialisation du mot de passe</title>
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
                            <h4 class="welcome-text">Réinitialisation du mot de passe</h4>
                        </div>
                        <div class="card-body p-4">
                            <form method="POST" action="{{ route('password.store') }}">
                                @csrf

                                <!-- Password Reset Token -->
                                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                                <div class="form-floating mb-3">
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $request->email) }}" required autofocus oninvalid="this.setCustomValidity('Veuillez entrer votre adresse email')" oninput="this.setCustomValidity('')" title="Veuillez entrer votre adresse email">
                                    <label for="email">Adresse email</label>
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="form-floating mb-3">
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required minlength="8" oninvalid="this.setCustomValidity('Le mot de passe doit contenir au moins 8 caractères')" oninput="this.setCustomValidity('')" title="Le mot de passe doit contenir au moins 8 caractères">
                                    <label for="password">Mot de passe</label>
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="form-floating mb-3">
                                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required minlength="8" oninvalid="this.setCustomValidity('Veuillez confirmer votre mot de passe')" oninput="this.setCustomValidity('')" title="Veuillez confirmer votre mot de passe">
                                    <label for="password_confirmation">Confirmer le mot de passe</label>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        Réinitialiser le mot de passe
                                    </button>
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
                        password: 'Veuillez entrer un mot de passe',
                        text: 'Veuillez remplir ce champ',
                        select: 'Veuillez sélectionner une option'
                    },
                    typeMismatch: {
                        email: 'Veuillez inclure un "@" dans l\'adresse email'
                    },
                    tooShort: {
                        password: 'Le mot de passe doit contenir au moins {minLength} caractères'
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
                        } else if (e.target.validity.tooShort && e.target.type === 'password') {
                            e.target.setCustomValidity(
                                messages.tooShort.password.replace('{minLength}', e.target.minLength)
                            );
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
