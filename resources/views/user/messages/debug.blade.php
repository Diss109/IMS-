@extends('layouts.admin')

@section('page_title', 'Debug Messages')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Debugging Message System</h6>
                    <a href="{{ route('user.messages.index') }}" class="btn btn-sm btn-primary">Return to Messages</a>
                </div>
                <div class="card-body">
                    <h5>Current User Information</h5>
                    <div class="mb-4">
                        <p><strong>ID:</strong> {{ Auth::id() }}</p>
                        <p><strong>Name:</strong> {{ Auth::user()->name }}</p>
                        <p><strong>Role:</strong> {{ Auth::user()->role }}</p>
                    </div>

                    <h5>Available Users</h5>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Role</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <td>{{ $user->id }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->role }}</td>
                                        <td>
                                            <a href="{{ route('user.messages.index', ['user' => $user->id]) }}" class="btn btn-sm btn-info">Chat with User</a>
                                            <a href="{{ route('user.messages.test_form', $user->id) }}" class="btn btn-sm btn-secondary">Test Form</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <h5>Recent Messages</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Sender ID</th>
                                    <th>Receiver ID</th>
                                    <th>Content</th>
                                    <th>Is Read</th>
                                    <th>Created At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($messages as $message)
                                    <tr>
                                        <td>{{ $message->id }}</td>
                                        <td>
                                            {{ $message->sender_id }}
                                            @if($message->sender_id == Auth::id())
                                                <span class="badge bg-primary">You</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $message->receiver_id }}
                                            @if($message->receiver_id == Auth::id())
                                                <span class="badge bg-primary">You</span>
                                            @endif
                                        </td>
                                        <td>{{ $message->content }}</td>
                                        <td>
                                            @if($message->is_read)
                                                <span class="badge bg-success">Read</span>
                                            @else
                                                <span class="badge bg-warning">Unread</span>
                                            @endif
                                        </td>
                                        <td>{{ $message->created_at }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">JavaScript Console</h6>
                </div>
                <div class="card-body">
                    <div id="console-log" class="bg-dark text-light p-3" style="height: 200px; overflow-y: auto; font-family: monospace;">
                        <div>Console output will appear here...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Override console.log to display in our custom console
(function() {
    const oldLog = console.log;
    const oldError = console.error;
    const consoleEl = document.getElementById('console-log');

    console.log = function(...args) {
        oldLog.apply(console, args);
        const message = args.map(arg => {
            if (typeof arg === 'object') {
                return JSON.stringify(arg, null, 2);
            }
            return arg;
        }).join(' ');

        const logLine = document.createElement('div');
        logLine.textContent = '> ' + message;
        logLine.style.borderBottom = '1px solid #444';
        logLine.style.padding = '3px 0';
        consoleEl.appendChild(logLine);
        consoleEl.scrollTop = consoleEl.scrollHeight;
    };

    console.error = function(...args) {
        oldError.apply(console, args);
        const message = args.map(arg => {
            if (typeof arg === 'object') {
                return JSON.stringify(arg, null, 2);
            }
            return arg;
        }).join(' ');

        const logLine = document.createElement('div');
        logLine.textContent = '> ERROR: ' + message;
        logLine.style.color = '#ff6b6b';
        logLine.style.borderBottom = '1px solid #444';
        logLine.style.padding = '3px 0';
        consoleEl.appendChild(logLine);
        consoleEl.scrollTop = consoleEl.scrollHeight;
    };

    // Test console
    console.log('Console initialized');
    console.log('Browser:', navigator.userAgent);

    // Test AJAX
    console.log('Testing AJAX request to messages endpoint...');
    fetch('{{ route("user.messages.unreadCount") }}', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        console.log('AJAX status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('AJAX response:', data);
    })
    .catch(error => {
        console.error('AJAX error:', error);
    });
})();
</script>
@endpush
@endsection
