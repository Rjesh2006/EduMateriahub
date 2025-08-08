@extends('layouts.admin')

@section('title', 'View Admin')

@section('content')
    <h1 class="mt-4">View Admins Details</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ url('/superadmin/dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('faculties.index') }}">Admin</a></li>
        <li class="breadcrumb-item active">{{ $admin->name }}</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-user me-1"></i>
            Admin Details
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong class="text-primary">ID:</strong> {{ $admin->id }}</p>
                    <p><strong class="text-primary">Name:</strong> {{ $admin->name }}</p>
                    <p><strong class="text-primary">Email:</strong> {{ $admin->email }}</p>
                    <p><strong class="text-primary">Phone:</strong> {{ $admin->phone }}</p>
                    <p><strong class="text-primary">Department:</strong> {{ $admin->department }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Roles:</strong>
                        @foreach ($admin->roles as $role)
                            <span class="badge bg-danger">{{ $role->name }}</span>
                        @endforeach
                    </p>
                </div>
            </div>
            <a href="{{ route('admins.index') }}" class="btn btn-secondary btn-sm">Back to Admin List</a>
        </div>
    </div>
@endsection
