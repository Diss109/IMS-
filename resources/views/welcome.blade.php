<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Tuniship - Système de Gestion Intégré</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
        <style>
            body {
                background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .welcome-container {
                background: rgba(255, 255, 255, 0.95);
                border-radius: 20px;
                box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
                padding: 2rem;
                max-width: 1000px;
                width: 90%;
                margin: 2rem;
            }
            .logo-container {
                text-align: center;
                margin-bottom: 2rem;
            }
            .logo-container img {
                max-width: 250px;
                height: auto;
            }
            .welcome-text {
                text-align: center;
                margin-bottom: 3rem;
            }
            .welcome-text h1 {
                color: #2d3748;
                font-size: 2.5rem;
                margin-bottom: 1rem;
            }
            .welcome-text p {
                color: #4a5568;
                font-size: 1.1rem;
            }
            .features-section {
                margin-bottom: 3rem;
            }
            .feature-card {
                text-align: center;
                padding: 1.5rem;
                border-radius: 10px;
                transition: transform 0.3s ease;
            }
            .feature-card:hover {
                transform: translateY(-5px);
            }
            .feature-icon {
                font-size: 2rem;
                margin-bottom: 1rem;
                color: #4a5568;
            }
            .auth-buttons {
                text-align: center;
            }
            .btn-auth {
                padding: 0.75rem 2rem;
                font-size: 1.1rem;
                border-radius: 50px;
                margin: 0.5rem;
                min-width: 200px;
            }
            .btn-login {
                background-color: #4a5568;
                border-color: #4a5568;
                color: white;
            }
            .btn-login:hover {
                background-color: #2d3748;
                border-color: #2d3748;
                color: white;
            }
            .btn-register {
                background-color: transparent;
                border-color: #4a5568;
                color: #4a5568;
            }
            .btn-register:hover {
                background-color: #4a5568;
                color: white;
            }
        </style>
    </head>
    <body>
        <div class="welcome-container">
            <div class="logo-container">
                <img src="/images/logo.jpg" alt="Tuniship Logo" class="img-fluid">
            </div>

            <div class="welcome-text">
                <h1>Bienvenue sur le Système de Gestion Intégré</h1>
                <p>Une solution complète pour la gestion des réclamations, des prestataires et des évaluations</p>
            </div>

            <div class="features-section">
                <div class="row">
                    <div class="col-md-4">
                        <div class="feature-card">
                            <i class="fas fa-tasks feature-icon"></i>
                            <h3>Gestion des Réclamations</h3>
                            <p>Suivez et gérez efficacement toutes les réclamations</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="feature-card">
                            <i class="fas fa-truck feature-icon"></i>
                            <h3>Suivi des Prestataires</h3>
                            <p>Gérez vos prestataires et leurs performances</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="feature-card">
                            <i class="fas fa-chart-line feature-icon"></i>
                            <h3>Tableaux de Bord</h3>
                            <p>Visualisez vos KPIs et prenez des décisions éclairées</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="auth-buttons">
                <a href="{{ route('login') }}" class="btn btn-auth btn-login">
                    <i class="fas fa-sign-in-alt me-2"></i>Connexion
                </a>
                <a href="{{ route('register') }}" class="btn btn-auth btn-register">
                    <i class="fas fa-user-plus me-2"></i>Inscription
                </a>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
