<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            line-height: 1.6;
        }
        h1 { color: #333; }
        .container { max-width: 800px; margin: 0 auto; }
        .card {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Test Page</h1>

        <div class="card">
            <h2>Basic Information</h2>
            <p>Testing if view rendering works properly.</p>
            <p>Current time: {{ now() }}</p>
        </div>

        @if(Auth::check())
            <div class="card">
                <h2>Authentication</h2>
                <p>You are logged in as: {{ Auth::user()->name }}</p>
                <p>User ID: {{ Auth::id() }}</p>
                <p>User role: {{ Auth::user()->role }}</p>
            </div>
        @else
            <div class="card">
                <h2>Authentication</h2>
                <p>You are not logged in.</p>
            </div>
        @endif

        <div class="card">
            <h2>Links</h2>
            <ul>
                <li><a href="{{ url('/') }}">Home</a></li>
                <li><a href="{{ route('user.dashboard') }}">User Dashboard</a></li>
                <li><a href="{{ route('user.messages.index') }}">Messages</a></li>
                <li><a href="{{ route('user.messages.debug') }}">Debug Messages</a></li>
                <li><a href="{{ route('user.messages.simple_index') }}">Simple Messages</a></li>
                <li><a href="{{ url('/test-plain') }}">Plain HTML Test</a></li>
                <li><a href="{{ url('/test-messages') }}">Test Messages</a></li>
            </ul>
        </div>
    </div>
</body>
</html>
