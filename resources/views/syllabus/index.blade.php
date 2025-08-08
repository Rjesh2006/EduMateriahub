@extends('layouts.admin')

@section('title', 'Syllabus')

@section('content')

    <h1 class="mt-4">Syllabus List</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item">
            @can('is superadmin')
                <a href="{{ route('superadmin.dashboard') }}">Dashboard</a>
            @else
                <a href="{{ route('others.dashboard') }}">Dashboard</a>
            @endcan
        </li>
        <li class="breadcrumb-item active">Syllabus</li>
    </ol>

    <div class="card mb-4 shadow-lg rounded-lg">
        <div class="card-header bg-primary text-white rounded-top">
            <i class="fas fa-book me-1"></i>
            Syllabus List
            @can('create syllabus')
                <a href="{{ route('syllabus.create') }}" class="btn btn-light btn-sm float-end">Add New Syllabus</a>
            @endcan
        </div>
        <div class="card-body">
            <table id="datatablesSimple" class="table table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>Department</th>
                        <th>File</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($syllabus as $material)
                        <tr class="table-hover">
                            <td>{{ $material->department }}</td>
                            <td>
                                @if ($material->file)
                                    <a href="{{ asset('storage/' . $material->file) }}" target="_blank">
                                        <button class="btn btn-info btn-sm">
                                            <i class="fas fa-file-alt"></i> View File
                                        </button>
                                    </a>
                                @else
                                    <span class="text-muted">No File</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('syllabus.show', Crypt::encryptString($material->id)) }}"
                                    class="btn btn-info btn-sm">
                                    View
                                </a>
                                @can('edit syllabus')
                                    <a href="{{ route('syllabus.edit', Crypt::encryptString($material->id)) }}"
                                        class="btn btn-warning btn-sm">
                                        Edit
                                    </a>
                                @endcan
                                @can('delete syllabus')
                                    <form id="delete-form-{{ $material->id }}"
                                        action="{{ route('syllabus.destroy', $material->id) }}" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-danger btn-sm"
                                            onclick="confirmDelete({{ $material->id }})">Delete</button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Include SweetAlert CSS and JS -->
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

    <!-- SweetAlert Confirmation for Delete -->
    <script>
        function confirmDelete(id) {
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
                    // Trigger form submission
                    document.getElementById(`delete-form-${id}`).submit();
                }
            });
        }
    </script>

    <style>
        /* Table Hover Effect */
        .table-hover tbody tr:hover {
            background-color: #f1f1f1;
        }

        /* Button Hover Effects */
        .btn:hover {
            opacity: 0.8;
        }
    </style>

@endsection
