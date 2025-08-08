@extends('layouts.admin')

@section('title', 'User Management')

@section('content')
    <h1 class="mt-4">User Management</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ url('/superadmin/dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Users</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            User List

            @can('create users')
                <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm float-end">Add User</a>
            @endcan
        </div>
        <div class="card-body">
            <table id="datatablesSimple" class="table table-striped">
                <thead>
                    <tr>
                        <th>S.N.</th>
                        <th>Name</th>
                        <th>Role</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Department</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($users->isNotEmpty())
                        @foreach ($users as $index=> $user)
                            <tr>
                                <td>{{ $index+1 }}</td> <!-- Display encrypted ID -->
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->roles->pluck('name')->implode(', ') }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->phone }}</td>
                                <td>{{ $user->department }}</td>
                                <td class="text-center">
                                    <!-- Edit Button -->
                                    @can('edit users')
                                        <a href="{{ route('users.edit', Crypt::encryptString($user->id)) }}" class="btn btn-sm btn-warning">Edit</a>
                                    @endcan

                                    @can('delete users')
                                        <!-- Delete Button -->
                                        <button type="button" class="btn btn-danger btn-sm"
                                            onclick="confirmDelete('{{ $user->encrypted_id }}')">Delete</button>

                                        <!-- Hidden Delete Form -->
                                        <form id="delete-form-{{ $user->encrypted_id }}"
                                            action="{{ route('users.destroy', Crypt::encryptString($user->id)) }}" method="POST"
                                            style="display:none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="text-center text-muted">No users found</td>
                        </tr>
                    @endif
                </tbody>
            </table>

            
        </div>
    </div>

    <!-- Include SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- SweetAlert Confirmation for Delete -->
    <script>
        function confirmDelete(userId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + userId).submit();
                }
            });
        }
    </script>

    <!-- SweetAlert Success Popup -->
    @if (session('success'))
        <script>
            window.onload = function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: "{{ session('success') }}",
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                });
            }
        </script>
    @endif
@endsection
