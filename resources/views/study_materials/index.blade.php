@extends('layouts.admin')

@section('title', 'Study Materials')

@section('content')
    <h1 class="mt-4">Study Materials</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item">
            @can('is superadmin')
                <a href="{{ route('superadmin.dashboard') }}">Dashboard</a>
            @else
                <a href="{{ route('others.dashboard') }}">Dashboard</a>
            @endcan
        </li>
        <li class="breadcrumb-item active">Study Materials</li>
    </ol>

    <div class="card mb-4 shadow-lg rounded-lg">
        <div class="card-header bg-primary text-white rounded-top">
            <i class="fas fa-book me-1"></i>
            Study Materials List

            @can('create study material')
                <a href="{{ route('study_materials.create') }}" class="btn btn-light btn-sm float-end">Add New Study Material</a>
            @endcan
            @if (auth()->user()->hasRole('student'))
                <a href="{{ route('study_materials.elective') }}" class="btn btn-light btn-sm float-end me-2">
                    View Elective Study Material
                </a>
            @endif
        </div>

        <div class="card-body">
            <table id="datatablesSimple" class="table table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>Sl No</th>
                        <th>Subject Type</th>
                        <th>Department</th>
                        <th>Semester</th>
                        <th>Subject Name</th>
                        @if ($roles->contains('Admin') || $roles->contains('SuperAdmin') || $roles->contains('student'))
                            <th>Faculty Name</th>
                        @endif
                        <th>File</th>
                        <th>Description</th>
                        @canany(['edit study material', 'delete study material'])
                            <th>Action</th>
                        @endcanany
                        @if ($roles->contains('student'))
                            <th></th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($study_materials as $index => $material)
                        <tr class="table-hover">
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $material->subject_type }}</td>
                            <td>{{ $material->department }}</td>
                            <td>{{ $material->semester }}</td>
                            <td>{{ $material->subject_name }}</td>

                            @if ($roles->contains('Admin') || $roles->contains('SuperAdmin') || $roles->contains('student'))
                                <td>{{ $material->faculty_name }}</td>
                            @endif
                            <td>
                                @if ($material->file)
                                    <a href="{{ asset('storage/' . $material->file) }}" target="_blank">
                                        <button class="btn btn-info btn-sm"><i class="fas fa-file-alt"></i>View
                                            File</button>
                                    </a>
                                @else
                                    No File
                                @endif
                            </td>
                            <td>{{ $material->description }}</td>
                            <td>
                                @can('edit study material')
                                    <a href="{{ route('study_materials.edit', Crypt::encryptString($material->id)) }}"
                                        class="btn btn-warning btn-sm">Edit</a>
                                @endcan
                                @can('delete study material')
                                    <form id="delete-form-{{ $material->id }}"
                                        action="{{ route('study_materials.destroy', $material->id) }}" method="POST"
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

    <!-- SweetAlert Confirmation for Delete -->
    <script>
        function confirmDelete(materialId) {
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
                    document.getElementById('delete-form-' + materialId).submit();
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

        /* Table Header Styling */
        .thead-dark th {
            background-color: #343a40;
            color: white;
        }

        /* Card Shadow */
        .card {
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
    </style>
@endsection
