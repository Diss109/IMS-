<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Soumettre une réclamation - Tuniship</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }
        .logo-container {
            text-align: center;
            margin-bottom: 2rem;
        }
        .logo-container img {
            max-width: 200px;
            height: auto;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .form-label.required:after {
            content: " *";
            color: red;
        }
        .urgency-selector {
            display: flex;
            justify-content: space-between;
            margin: 1rem 0;
        }
        .urgency-option {
            text-align: center;
            cursor: pointer;
            padding: 10px;
            border-radius: 5px;
            transition: all 0.3s;
        }
        .urgency-option:hover {
            background-color: #f8f9fa;
        }
        .urgency-option.selected {
            background-color: #e9ecef;
        }
        .red-flag {
            color: #dc3545;
            font-size: 1.2em;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body p-4">
                        <div class="logo-container">
                            <img src="{{ asset('images/logo.jpg') }}" alt="Tuniship Logo" class="img-fluid">
                        </div>

                        <h2 class="text-center mb-4">Soumettre une réclamation</h2>

                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form action="{{ route('complaints.store-public') }}" method="POST">
                            @csrf

                            <!-- Company Information -->
                            <div class="mb-3">
                                <label for="company_name" class="form-label required">Nom de l'entreprise</label>
                                <input type="text" class="form-control @error('company_name') is-invalid @enderror"
                                       id="company_name" name="company_name" required>
                                @error('company_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Contact Information -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="first_name" class="form-label required">Prénom</label>
                                    <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                                           id="first_name" name="first_name" required>
                                    @error('first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="last_name" class="form-label required">Nom</label>
                                    <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                                           id="last_name" name="last_name" required>
                                    @error('last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label required">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       id="email" name="email" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Complaint Type -->
                            <div class="mb-3">
                                <label for="complaint_type" class="form-label required">Type de réclamation</label>
                                <select class="form-select @error('complaint_type') is-invalid @enderror"
                                        id="complaint_type" name="complaint_type" required>
                                    <option value="">Choisir le type</option>
                                    <option value="retard_livraison">Retard de livraison</option>
                                    <option value="retard_chargement">Retard de chargement</option>
                                    <option value="marchandise_endommagée">Marchandise endommagée</option>
                                    <option value="mauvais_comportement">Mauvais comportement</option>
                                    <option value="autre">Autre</option>
                                </select>
                                @error('complaint_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Urgency Level -->
                            <div class="mb-3">
                                <label class="form-label required">Niveau d'urgence</label>
                                <div class="urgency-selector">
                                    <div class="urgency-option" data-value="low">
                                        <span class="red-flag">🚩</span>
                                        <div>Faible</div>
                                    </div>
                                    <div class="urgency-option" data-value="medium">
                                        <div><span class="red-flag">🚩</span><span class="red-flag">🚩</span></div>
                                        <div>Moyen</div>
                                    </div>
                                    <div class="urgency-option" data-value="high">
                                        <div><span class="red-flag">🚩</span><span class="red-flag">🚩</span><span class="red-flag">🚩</span></div>
                                        <div>Élevé</div>
                                    </div>
                                    <div class="urgency-option" data-value="critical">
                                        <div><span class="red-flag">🚩</span><span class="red-flag">🚩</span><span class="red-flag">🚩</span><span class="red-flag">🚩</span></div>
                                        <div>Critique</div>
                                    </div>
                                </div>
                                <input type="hidden" name="urgency_level" id="urgency_level" required>
                                @error('urgency_level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="mb-4">
                                <label for="description" class="form-label required">Description de la réclamation</label>
                                <textarea class="form-control @error('description') is-invalid @enderror"
                                          id="description" name="description" rows="5" required></textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">Soumettre la réclamation</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Set up CSRF token for all AJAX requests
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Handle form submission
            const form = document.querySelector('form');
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);

                fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    // Remove any existing alerts
                    const existingAlerts = document.querySelectorAll('.alert');
                    existingAlerts.forEach(alert => alert.remove());

                    // Create and show the alert
                    const alert = document.createElement('div');
                    alert.className = `alert alert-${data.success ? 'success' : 'danger'} alert-dismissible fade show`;
                    alert.role = 'alert';
                    alert.innerHTML = `
                        ${data.message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    `;

                    // Insert the alert at the top of the form
                    const cardBody = document.querySelector('.card-body');
                    cardBody.insertBefore(alert, cardBody.querySelector('h2').nextSibling);

                    // If successful, reset the form and scroll to top
                    if (data.success) {
                        form.reset();
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    const alert = document.createElement('div');
                    alert.className = 'alert alert-danger alert-dismissible fade show';
                    alert.role = 'alert';
                    alert.innerHTML = `
                        Une erreur s'est produite. Veuillez réessayer.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    `;
                    const cardBody = document.querySelector('.card-body');
                    cardBody.insertBefore(alert, cardBody.querySelector('h2').nextSibling);
                });
            });

            const urgencyOptions = document.querySelectorAll('.urgency-option');
            const urgencyInput = document.getElementById('urgency_level');

            urgencyOptions.forEach(option => {
                option.addEventListener('click', function() {
                    // Remove selected class from all options
                    urgencyOptions.forEach(opt => opt.classList.remove('selected'));
                    // Add selected class to clicked option
                    this.classList.add('selected');
                    // Update hidden input value
                    urgencyInput.value = this.dataset.value;
                });
            });
        });
    </script>
</body>
</html>
