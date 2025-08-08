@extends('layouts.admin')

@section('title', 'Edit Role')

@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="font-weight-bold">Edit Role</h2>
            <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        {{-- Edit Role Form --}}
        <div class="card shadow mt-4">
            <div class="card-body">
            <form action="{{ route('roles.update', Crypt::encryptString($role->id)) }}" method="POST">
                    @csrf


                    {{-- Role Name Field --}}
                    <div class="form-group">
                        <label for="name" class="font-weight-semibold">Role Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $role->name) }}"
                            class="form-control" placeholder="Enter Role Name" required>
                        @error('name')
                            <p class="text-danger mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Permissions Section --}}
                    <div class="form-group mt-4">
                        <label for="permissions" class="font-weight-semibold">Permissions</label>
                        <div class="row mt-2">
                            @if ($permissions->isNotEmpty())
                                @foreach ($permissions as $permission)
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                                id="permission-{{ $permission->id }}" class="form-check-input"
                                                @if ($hasPermissions->contains($permission->name)) checked @endif>
                                            <label for="permission-{{ $permission->id }}"
                                                class="form-check-label">{{ $permission->name }}</label>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-muted">No permissions available.</p>
                            @endif
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <button type="submit" class="btn btn-primary mt-4">
                        Update Role
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Success Flash Message
        @if (session('success'))
            Swal.fire({
                title: 'Success!',
                text: "{{ session('success') }}",
                icon: 'success',
                timer: 3000,
                showConfirmButton: false
            });
        @endif

        // Error Flash Message
        @if (session('error'))
            Swal.fire({
                title: 'Error!',
                text: "{{ session('error') }}",
                icon: 'error',
                timer: 3000,
                showConfirmButton: false
            });
        @endif
    </script>
@endsection
