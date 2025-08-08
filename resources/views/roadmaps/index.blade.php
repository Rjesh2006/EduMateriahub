@extends('layouts.admin')

@section('title', 'Roadmaps')

@section('content')
    <h1 class="mt-4">Roadmap List</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item">
            @can('is superadmin')
                <a href="{{ route('superadmin.dashboard') }}">Dashboard</a>
            @else
                <a href="{{ route('others.dashboard') }}">Dashboard</a>
            @endcan
        </li>
        <li class="breadcrumb-item active">Roadmaps</li>
    </ol>

    <div class="card mb-4 shadow-lg rounded-lg">
        <div class="card-header bg-primary text-white rounded-top">
            <i class="fas fa-map me-1"></i>
            Roadmap List

            @can('create roadmaps')
                <a href="{{ route('roadmaps.create') }}" class="btn btn-light btn-sm float-end">Add New Roadmap</a>
            @endcan
        </div>
        <div class="card-body">
            <table id="datatablesSimple" class="table table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>S.N.</th>
                        <th>Department</th>
                        <th>Subject Type</th>
                        <th>Semester</th>
                        <th>Subject Name</th>
                        <th>Faculty Name</th>
                        <th>Description</th>
                        <th>File</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($roadmaps as $index => $roadmap)
                        <tr class="table-hover">
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $roadmap->department }}</td>
                            <td>{{ $roadmap->subject_type }}</td>
                            <td>{{ $roadmap->semester }}</td>
                            <td>{{ $roadmap->subject_name }}</td>
                            <td>{{ $roadmap->faculty_name }}</td>
                            <td>{{ $roadmap->description }}</td>
                            <td>
                                @if ($roadmap->file)
                                    <a href="{{ asset('storage/' . $roadmap->file) }}" target="_blank">
                                        <button class="btn btn-info btn-sm">
                                            <i class="fas fa-file-alt"></i> View File
                                        </button>
                                    </a>
                                @else
                                    <span class="text-muted">No File</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('roadmaps.show', Crypt::encryptString($roadmap->id)) }}"
                                    class="btn btn-info btn-sm">
                                    View
                                </a>
                                @can('edit roadmaps')
                                    <a href="{{ route('roadmaps.edit', Crypt::encryptString($roadmap->id)) }}"
                                        class="btn btn-warning btn-sm">Edit</a>
                                @endcan
                                @can('delete roadmaps')
                                    <form id="delete-form-{{ $roadmap->id }}"
                                        action="{{ route('roadmaps.destroy', $roadmap->id) }}" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-danger btn-sm"
                                            onclick="confirmDelete({{ $roadmap->id }})">Delete</button>
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
        function confirmDelete(roadmapId) {
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
                    document.getElementById('delete-form-' + roadmapId).submit();
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
