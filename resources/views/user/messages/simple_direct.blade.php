<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Direct Messages</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .card {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .error {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1>Direct Messages</h1>

    @if($error)
        <div class="card error">
            <h2>Error</h2>
            <p>{{ $error }}</p>
        </div>
    @endif

    <div class="card">
        <h2>Authentication Info</h2>
        @if(Auth::check())
            <p>User ID: {{ Auth::id() }}</p>
            <p>Name: {{ Auth::user()->name }}</p>
            <p>Role: {{ Auth::user()->role }}</p>
        @else
            <p>Not authenticated</p>
        @endif
    </div>

    <div class="card">
        <h2>Available Users</h2>
        @if($users->isEmpty())
            <p>No users available</p>
        @else
            <ul>
                @foreach($users as $user)
                    <li>
                        ID: {{ $user->id }} |
                        Name: {{ $user->name }} |
                        Role: {{ $user->role }}
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    <div class="card">
        <h2>Links</h2>
        <ul>
            <li><a href="{{ route('user.dashboard') }}">Dashboard</a></li>
            <li><a href="{{ route('user.messages.index') }}">Regular Messages</a></li>
            <li><a href="{{ url('/test') }}">Test Page</a></li>
        </ul>
    </div>
</body>
</html>
