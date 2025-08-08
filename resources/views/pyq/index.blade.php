@extends('layouts.admin')

@section('title', 'PYQ')

@section('content')
    <h1 class="mt-4">PYQ List</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item">
            @can('is superadmin')
                <a href="{{ route('superadmin.dashboard') }}">Dashboard</a>
            @else
                <a href="{{ route('others.dashboard') }}">Dashboard</a>
            @endcan
        </li>
        <li class="breadcrumb-item active">PYQ List</li>
    </ol>

    <div class="card mb-4 shadow-lg rounded-lg">
        <div class="card-header bg-primary text-white rounded-top">
            <i class="fas fa-map me-1"></i>
            PYQ List

            @can('create pyq')
                <a href="{{ route('pyq.create') }}" class="btn btn-light btn-sm float-end">Add New PYQ</a>
            @endcan
            @if (auth()->user()->hasRole('student'))
                <a href="{{ route('pyq.elective') }}" class="btn btn-light btn-sm float-end me-2">
                    View Elective PYQ
                </a>
            @endif
        </div>
        <div class="card-body">
            <table id="datatablesSimple" class="table table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>S.N.</th>
                        <th>Subject Type</th>
                        <th>Department</th>
                        <th>Semester</th>
                        <th>Subject Name</th>
                        <th>Faculty Name</th>
                        <th>Year</th>
                        <th>File</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pyqs as $index => $pyq)
                        <tr class="table-hover">
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $pyq->subject_type }}</td>
                            <td>{{ $pyq->department }}</td>
                            <td>{{ $pyq->semester }}</td>
                            <td>{{ $pyq->subject_name }}</td>
                            <td>{{ $pyq->faculty_name }}</td>
                            <td>{{ $pyq->year }}</td>
                            <td>
                                @if ($pyq->file)
                                    <a href="{{ asset('storage/' . $pyq->file) }}" target="_blank">
                                        <button class="btn btn-info btn-sm">
                                            <i class="fas fa-file-alt"></i> View File
                                        </button>
                                    </a>
                                @else
                                    <span class="text-muted">No File</span>
                                @endif
                            </td>
                            <td>
                                @can('edit pyq')
                                    <a href="{{ route('pyq.edit', Crypt::encryptString($pyq->id)) }}"
                                        class="btn btn-warning btn-sm">Edit</a>
                                @endcan

                                @can('delete pyq')
                                    <form id="delete-form-{{ $pyq->id }}" action="{{ route('pyq.destroy', $pyq->id) }}"
                                        method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-danger btn-sm"
                                            onclick="confirmDelete({{ $pyq->id }})">Delete</button>
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

    <!-- SweetAlert Confirmation for Delete -->
    <script>
        function confirmDelete(pyqId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
                customClass: {
                    popup: 'sweetalert-custom-popup'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + pyqId).submit();
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
                    customClass: {
                        popup: 'sweetalert-custom-popup'
                    }
                });
            }
        </script>
    @endif

    <style>
        /* Custom SweetAlert Style */
        .sweetalert-custom-popup {
            border-radius: 15px;
            padding: 20px;
            font-family: 'Arial', sans-serif;
        }

        /* Table Hover Effect */
        .table-hover tbody tr:hover {
            background-color: #f1f1f1;
        }

        /* Button Hover Effects */
        .btn:hover {
            opacity: 0.8;
        }
    </style>

    <!-- Include SweetAlert CSS and JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- SweetAlert Confirmation for Delete -->
    <script>
        function confirmDelete(pyqId) {
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
                    document.getElementById('delete-form-' + pyqId).submit();
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
