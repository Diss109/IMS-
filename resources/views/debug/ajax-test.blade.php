@extends('layouts.admin')

@section('page_title', 'Debug AJAX Test')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">AJAX Test</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Testing Message AJAX
                                            </div>
                                            <form id="test-ajax-form" class="mt-3">
                                                @csrf
                                                <div class="mb-3">
                                                    <label for="content" class="form-label">Message Content</label>
                                                    <input type="text" class="form-control" id="content" name="content" value="This is a test message">
                                                </div>
                                                <button type="submit" class="btn btn-primary">Send Test Message</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Response
                                            </div>
                                            <div class="mt-3">
                                                <pre id="response-display" class="bg-light p-3" style="max-height: 200px; overflow-y: auto;">No response yet</pre>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="card border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Debug Console
                                    </div>
                                    <div class="mt-3">
                                        <div id="console-log" class="bg-dark text-light p-3" style="height: 200px; overflow-y: auto; font-family: monospace;">
                                            <div>Console output will appear here...</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Override console.log to display in our custom console
    const oldLog = console.log;
    const oldError = console.error;
    const consoleEl = document.getElementById('console-log');

    console.log = function(...args) {
        oldLog.apply(console, args);
        const message = args.map(arg => {
            if (typeof arg === 'object') {
                try {
                    return JSON.stringify(arg, null, 2);
                } catch (e) {
                    return String(arg);
                }
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
                try {
                    return JSON.stringify(arg, null, 2);
                } catch (e) {
                    return String(arg);
                }
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
    console.log('Debug console initialized');
    console.log('Browser:', navigator.userAgent);

    // Set up form submission
    const form = document.getElementById('test-ajax-form');
    const responseDisplay = document.getElementById('response-display');

    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log("Form submitted");

            const content = document.getElementById('content').value;
            console.log("Message content:", content);

            // Create FormData object
            const formData = new FormData(form);

            // Disable submit button
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) submitBtn.disabled = true;

            // Display FormData contents (debug)
            for (const pair of formData.entries()) {
                console.log(pair[0] + ': ' + pair[1]);
            }

            // Check if jQuery is available
            if (typeof $ === 'undefined') {
                console.error("jQuery is not available, using fetch API");

                fetch('{{ route("debug.ajaxTest") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    console.log("Status:", response.status);
                    return response.json();
                })
                .then(data => {
                    console.log("Response:", data);
                    responseDisplay.textContent = JSON.stringify(data, null, 2);
                })
                .catch(error => {
                    console.error("Error:", error);
                    responseDisplay.textContent = 'Error: ' + error.message;
                })
                .finally(() => {
                    if (submitBtn) submitBtn.disabled = false;
                });
            } else {
                console.log("Using jQuery AJAX");

                $.ajax({
                    url: '{{ route("debug.ajaxTest") }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    success: function(data) {
                        console.log("Success response:", data);
                        responseDisplay.textContent = JSON.stringify(data, null, 2);
                    },
                    error: function(xhr, status, error) {
                        console.error("Error:", error);
                        console.error("Status:", status);
                        console.error("Response:", xhr.responseText);
                        responseDisplay.textContent = 'Error: ' + error + '\nStatus: ' + status;
                    },
                    complete: function() {
                        if (submitBtn) submitBtn.disabled = false;
                    }
                });
            }
        });
    }
});
</script>
@endpush
@endsection
