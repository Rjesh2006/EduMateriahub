@extends('layouts.admin')

@section('title', 'View Faculty')

@section('content')
    <h1 class="mt-4">View Faculty Details</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ url('/superadmin/dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('faculties.index') }}">Faculty</a></li>
        <li class="breadcrumb-item active">{{ $faculty->name }}</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-user me-1"></i>
            Faculty Details
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong class="text-primary">ID:-</strong> {{ $faculty->id }}</p>
                    <p><strong class="text-primary">Name:-</strong> {{ $faculty->name }}</p>
                    <p><strong class="text-primary">Email:-</strong> {{ $faculty->email }}</p>
                    <p><strong class="text-primary">Phone:-</strong> {{ $faculty->phone }}</p>
                    <p><strong class="text-primary">Department:-</strong> {{ $faculty->department }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong class="text-primary">Roles:</strong>
                        @foreach ($faculty->roles as $role)
                            <span class="badge bg-danger">{{ $role->name }}</span>
                        @endforeach
                    </p>
                </div>
            </div>
            <a href="{{ route('faculties.index') }}" class="btn btn-secondary btn-sm">Back to Faculty List</a>
        </div>
    </div>
@endsection
