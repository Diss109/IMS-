@extends('layouts.admin')

@section('page_title', 'Simple Test Page')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Simple Test Page</h6>
                </div>
                <div class="card-body">
                    <p>This is a simple test page to verify routing.</p>
                    <p>Current time: {{ now() }}</p>

                    <h5 class="mt-4">Links:</h5>
                    <ul>
                        <li><a href="{{ route('user.messages.index') }}">Messages Home</a></li>
                        <li><a href="{{ route('user.messages.debug') }}">Debug Page</a></li>
                        <li><a href="{{ route('user.dashboard') }}">User Dashboard</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
