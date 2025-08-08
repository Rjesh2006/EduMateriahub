@extends('layouts.admin')

@section('title', 'SuperAdmin Management')

@section('content')
    <h1 class="mt-4">SuperAdmin Management</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ url('/superadmin/dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">SuperAdmins</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Super Admin List
        </div>
        <div class="card-body">
            <table id="datatablesSimple" class="table table-striped">
                <thead>
                    <tr>
                        <th>Sl No.</th>
                        <th>Name</th>
                        <th>Role</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Department</th>
                        <th>Created At</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($super->isNotEmpty())
                        @foreach ($super as $index => $admin)
                            <tr>
                                <td>{{ $index+1 }}</td> <!-- Display Encrypted ID -->
                                <td>{{ $admin->name }}</td>
                                <td>{{ $admin->roles->pluck('name')->implode(', ') }}</td>
                                <td>{{ $admin->email }}</td>
                                <td>{{ $admin->phone }}</td>
                                <td>{{ $admin->department }}</td>
                                <td>{{ \Carbon\Carbon::parse($admin->created_at)->format('d M, Y') }}</td>
                                <td class="text-center">
                                    <a href="{{ route('superadminView.show', $admin->encrypted_id) }}" 
                                       class="btn btn-info btn-sm">View</a>
                                    @can('edit superadmins')
                                        <a href="{{ route('superadminView.edit', $admin->encrypted_id) }}" 
                                           class="btn btn-sm btn-warning">Edit</a>
                                    @endcan
                                    @can('delete superadmins')
                                        <button type="button" class="btn btn-danger btn-sm" 
                                                onclick="confirmDelete('{{ $admin->encrypted_id }}')">Delete</button>
                                        <form id="delete-form-{{ $admin->encrypted_id }}" 
                                              action="{{ route('superadminView.destroy', $admin->encrypted_id) }}" 
                                              method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="8" class="text-center text-muted">No admins found</td>
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
        function confirmDelete(encryptedId) {
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
                    document.getElementById('delete-form-' + encryptedId).submit();
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
