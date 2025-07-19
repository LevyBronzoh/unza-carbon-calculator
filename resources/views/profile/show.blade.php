@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">User Profile</h4>
                </div>

                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Name:</label>
                        <p class="form-control-plaintext">{{ $user->name }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email:</label>
                        <p class="form-control-plaintext">{{ $user->email }}</p>
                    </div>

                    <a href="{{ route('dashboard') }}" class="btn btn-primary">
                        Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
