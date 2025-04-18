<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Login / Sign Up</title>
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Custom CSS -->
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
            .nav-pills .nav-link.active {
                background-color: #4a5568;
            }
            .nav-pills .nav-link {
                color: #4a5568;
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
            .alert {
                margin-top: 10px;
                margin-bottom: 20px;
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
                            <h4 class="welcome-text">Welcome !</h4>
                            <ul class="nav nav-pills nav-justified mb-3" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#login-tab" type="button">Login</button>
                        </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#signup-tab" type="button">Sign Up</button>
                        </li>
                    </ul>
                </div>
                        <div class="card-body p-4">
                            <div id="messageBox" class="alert" style="display: none;"></div>

                            <div class="tab-content">
                                <!-- Login Form -->
                                <div class="tab-pane fade show active" id="login-tab">
                                    <form id="loginForm" method="POST" action="/login">
                                        <div class="form-floating mb-3">
                                            <input type="email" class="form-control" id="loginEmail" name="email" placeholder="name@example.com" required>
                                            <label for="loginEmail">Email address</label>
                                        </div>
                                        <div class="form-floating mb-4">
                                            <input type="password" class="form-control" id="loginPassword" name="password" placeholder="Password" required>
                                            <label for="loginPassword">Password</label>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="remember" name="remember">
                                                <label class="form-check-label" for="remember">Remember me</label>
                                            </div>
                                            <a href="{{ route('password.request') }}" class="text-decoration-none">Forgot password?</a>
                                        </div>
                                        <button type="submit" class="btn btn-primary w-100 mb-3">Login</button>
                                    </form>
                                </div>

                                <!-- Sign Up Form -->
                                <div class="tab-pane fade" id="signup-tab">
                                    <form id="signupForm" action="/register" method="POST">
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" id="signupName" name="name" placeholder="Full Name" required>
                                            <label for="signupName">Full Name</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input type="email" class="form-control" id="signupEmail" name="email" placeholder="name@example.com" required>
                                            <label for="signupEmail">Email address</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input type="password" class="form-control" id="signupPassword" name="password" placeholder="Password" required minlength="6">
                                            <label for="signupPassword">Password</label>
                                        </div>
                                        <div class="form-floating mb-4">
                                            <input type="password" class="form-control" id="signupConfirmPassword" name="password_confirmation" placeholder="Confirm Password" required minlength="6">
                                            <label for="signupConfirmPassword">Confirm Password</label>
                                        </div>
                                        <button type="submit" class="btn btn-primary w-100 mb-3">Create Account</button>
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
                        showMessage('Passwords do not match!', 'danger');
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
                                console.error('Registration error response:', err);
                                return Promise.reject(err);
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Registration success:', data);
                        if (data.success) {
                            showMessage('User created successfully!', 'success');
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
                        console.error('Registration error:', error);
                        showMessage(error.message || 'Error creating user. Please try again.', 'danger');
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
