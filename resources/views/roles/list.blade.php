@extends('layouts.admin')

@section('title', 'Roles Management')

@section('content')
    <h1 class="mt-4">Roles Management</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ url('/superadmin/dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Roles</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Roles List

            <a href="{{ route('roles.create') }}" class="btn btn-primary btn-sm float-end">Add Role</a>
        </div>

        <div class="card-body">
            <table id="datatablesSimple" class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Permissions</th>
                        <th>Created</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($roles->isNotEmpty())
                        @foreach ($roles as $index => $role)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $role->name }}</td>
                                <td>
                                    @if ($role->permissions->isNotEmpty())
                                        @foreach ($role->permissions as $permission)
                                            <span class="badge bg-primary text-white">{{ $permission->name }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-muted">No Permissions Assigned</span>
                                    @endif
                                </td>

                                <td>{{ \Carbon\Carbon::parse($role->created_at)->format('d M, Y') }}</td>
                                <td class="text-center">
                                    @can('edit roles')
                                    <a href="{{ route('roles.edit', Crypt::encryptString($role->id)) }}" class="btn btn-sm btn-warning">Edit</a>

                                    @endcan
                                    @can('delete roles')
                                        <form action="{{ route('roles.destroy', $role->id) }}" method="POST"
                                            style="display:inline;" id="delete-form-{{ $role->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-danger btn-sm"
                                                onclick="confirmDelete({{ $role->id }})">Delete</button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5" class="text-center text-muted">No roles found</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

    <script>
        function confirmDelete(roleId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + roleId).submit();
                }
            });
        }
    </script>
@endsection
