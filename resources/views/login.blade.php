<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Connexion / Inscription</title>
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <!-- Custom CSS -->
        <style>
            body {
                background: linear-gradient(135deg, #0061f2 0%, #6c47ef 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
            }
            .card {
                border: none;
                border-radius: 1rem;
                box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
                overflow: hidden;
            }
            .card-header {
                background: transparent;
                border-bottom: none;
                padding: 2rem 2rem 0;
            }
            .card-body {
                padding: 2rem;
            }
            .form-control {
                border-radius: 0.5rem;
                padding: 0.75rem 1rem;
                border: 1px solid #e2e8f0;
                transition: all 0.2s ease;
            }
            .form-control:focus {
                box-shadow: 0 0 0 0.25rem rgba(106, 70, 239, 0.25);
                border-color: #6a46ef;
            }
            .btn-primary {
                background-color: #6a46ef;
                border-color: #6a46ef;
                border-radius: 0.5rem;
                padding: 0.75rem 1rem;
                font-weight: 600;
                transition: all 0.2s ease;
            }
            .btn-primary:hover {
                background-color: #5030d8;
                border-color: #5030d8;
                transform: translateY(-2px);
            }
            .nav-pills .nav-link {
                color: #6c757d;
                font-weight: 600;
                padding: 0.75rem 1.5rem;
                border-radius: 0.5rem;
                transition: all 0.2s ease;
            }
            .nav-pills .nav-link.active {
                background-color: #6a46ef;
                color: white;
            }
            .nav-pills .nav-link:hover:not(.active) {
                background-color: #f8f9fa;
            }
            .form-floating > label {
                padding: 0.75rem 1rem;
            }
            .form-floating > .form-control:focus ~ label,
            .form-floating > .form-control:not(:placeholder-shown) ~ label {
                opacity: 0.85;
                transform: scale(0.85) translateY(-0.5rem) translateX(0.15rem);
            }
            .logo-container {
                margin-bottom: 1.5rem;
                text-align: center;
            }
            .logo-container img {
                max-width: 180px;
                height: auto;
                margin-bottom: 1rem;
                border-radius: 0.5rem;
                box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
            }
            .welcome-text {
                color: #495057;
                font-weight: 600;
                font-size: 1.75rem;
                margin-bottom: 1.5rem;
            }
            .alert {
                border-radius: 0.5rem;
                font-weight: 500;
            }
            .form-check-input:checked {
                background-color: #6a46ef;
                border-color: #6a46ef;
            }
            .form-check-input:focus {
                box-shadow: 0 0 0 0.25rem rgba(106, 70, 239, 0.25);
            }
            a {
                color: #6a46ef;
                text-decoration: none;
                font-weight: 500;
                transition: all 0.2s;
            }
            a:hover {
                color: #5030d8;
                text-decoration: underline;
            }
        </style>
    </head>
    <body>
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                    <div class="card">
                        <div class="card-header text-center">
                            <div class="logo-container">
                                <img src="/images/logo.jpg" alt="Tuniship Logo" class="img-fluid">
                            </div>
                            <h4 class="welcome-text">Bienvenue !</h4>
                            <ul class="nav nav-pills nav-justified mb-4" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#login-tab" type="button">
                                        <i class="fas fa-sign-in-alt me-2"></i>Connexion
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#signup-tab" type="button">
                                        <i class="fas fa-user-plus me-2"></i>Inscription
                                    </button>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div id="messageBox" class="alert" style="display: none;"></div>

                            <div class="tab-content">
                                <!-- Login Form -->
                                <div class="tab-pane fade show active" id="login-tab">
                                    <form id="loginForm" method="POST" action="/login">
                                        <div class="form-floating mb-3">
                                            <input type="email" class="form-control" id="loginEmail" name="email" placeholder="nom@exemple.com" required>
                                            <label for="loginEmail"><i class="fas fa-envelope me-2"></i>Adresse e-mail</label>
                                        </div>
                                        <div class="form-floating mb-4">
                                            <input type="password" class="form-control" id="loginPassword" name="password" placeholder="Mot de passe" required>
                                            <label for="loginPassword"><i class="fas fa-lock me-2"></i>Mot de passe</label>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="remember" name="remember">
                                                <label class="form-check-label" for="remember">Se souvenir de moi</label>
                                            </div>
                                            <a href="{{ route('password.request') }}">Mot de passe oublié ?</a>
                                        </div>
                                        <button type="submit" class="btn btn-primary w-100 mb-3">
                                            <i class="fas fa-sign-in-alt me-2"></i>Connexion
                                        </button>
                                    </form>
                                </div>

                                <!-- Sign Up Form -->
                                <div class="tab-pane fade" id="signup-tab">
                                    <form id="signupForm" action="/register" method="POST">
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" id="signupName" name="name" placeholder="Nom complet" required>
                                            <label for="signupName">Nom complet</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input type="email" class="form-control" id="signupEmail" name="email" placeholder="nom@exemple.com" required>
                                            <label for="signupEmail">Adresse e-mail</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input type="password" class="form-control" id="signupPassword" name="password" placeholder="Mot de passe" required minlength="6">
                                            <label for="signupPassword">Mot de passe</label>
                                        </div>
                                        <div class="form-floating mb-4">
                                            <input type="password" class="form-control" id="signupConfirmPassword" name="password_confirmation" placeholder="Confirmer le mot de passe" required minlength="6">
                                            <label for="signupConfirmPassword">Confirmer le mot de passe</label>
                                        </div>
                                        <button type="submit" class="btn btn-primary w-100 mb-3">Créer un compte</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bootstrap Bundle with Popper -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Handle tab switching
                const navPills = document.querySelectorAll('.nav-link');
                navPills.forEach(pill => {
                    pill.addEventListener('click', function(e) {
                        e.preventDefault();
                        const target = this.getAttribute('data-bs-target');
                        document.querySelectorAll('.tab-pane').forEach(pane => {
                            pane.classList.remove('show', 'active');
                        });
                        document.querySelector(target).classList.add('show', 'active');
                        navPills.forEach(p => p.classList.remove('active'));
                        this.classList.add('active');
                    });
                });

                // Handle form submission
                const signupForm = document.getElementById('signupForm');
                signupForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const password = document.getElementById('signupPassword').value;
                    const confirmPassword = document.getElementById('signupConfirmPassword').value;

                    if (password !== confirmPassword) {
                        showMessage('Les mots de passe ne correspondent pas !', 'danger');
                        return;
                    }

                    const formData = {
                        name: document.getElementById('signupName').value,
                        email: document.getElementById('signupEmail').value,
                        password: document.getElementById('signupPassword').value,
                        password_confirmation: document.getElementById('signupConfirmPassword').value,
                        _token: document.querySelector('meta[name="csrf-token"]').content
                    };

                    fetch('/register', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(formData)
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => {
                                console.error('Erreur d\'inscription:', err);
                                return Promise.reject(err);
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Inscription réussie:', data);
                        if (data.success) {
                            showMessage('Utilisateur créé avec succès !', 'success');
                            setTimeout(() => {
                                window.location.href = '/home';
                            }, 2000);
                        } else {
                            let errorMessage = data.message;
                            if (data.errors) {
                                errorMessage = Object.values(data.errors).flat().join(', ');
                            }
                            showMessage(errorMessage, 'danger');
                        }
                    })
                    .catch(error => {
                        console.error('Erreur d\'inscription:', error);
                        showMessage(error.message || 'Erreur lors de la création de l\'utilisateur. Veuillez réessayer.', 'danger');
                    });
                });

                // Function to show messages
                function showMessage(message, type = 'success') {
                    const messageBox = document.getElementById('messageBox');
                    messageBox.textContent = message;
                    messageBox.className = `alert alert-${type}`;
                    messageBox.style.display = 'block';
                    setTimeout(() => {
                        messageBox.style.display = 'none';
                    }, 5000);
                }
            });
        </script>
    </body>
</html>
