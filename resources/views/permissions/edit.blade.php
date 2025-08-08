@extends('layouts.admin')

@section('title', 'Edit Permission')

@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between">
            <h2 class="font-weight-bold">Edit Permission</h2>
            <a href="{{ route('permissions.index') }}" class="btn btn-secondary ">
                 Back
            </a>
        </div>

        <div class="card shadow-sm mt-4">
            <div class="card-body">
            <form action="{{ route('permissions.update', Crypt::encrypt($permission->id)) }}" method="POST">

                    @csrf
                    
                    
                    <div class="form-group">
                        <label for="name" class="font-weight-bold">Name</label>
                        <input 
                            type="text" 
                            name="name" 
                            id="name" 
                            value="{{ old('name', $permission->name) }}" 
                            class="form-control @error('name') is-invalid @enderror" 
                            placeholder="Enter Permission Name">
                        
                        @error('name')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
