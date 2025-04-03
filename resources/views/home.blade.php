<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - IMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Welcome, {{ Auth::user()->name }}!</h5>
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-danger">Logout</button>
                        </form>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Successfully Registered!</h5>
                        <p class="card-text">Your account has been created successfully.</p>
                        <p class="card-text">Email: {{ Auth::user()->email }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
