@extends('layouts.admin')

@section('title', 'Subjects')

@section('content')
    <h1 class="mt-4">Subjects</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item">
            @can('is superadmin')
                <a href="{{ route('superadmin.dashboard') }}">Dashboard</a>
            @else
                <a href="{{ route('others.dashboard') }}">Dashboard</a>
            @endcan
        </li>
        <li class="breadcrumb-item active">Subjects</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-book me-1"></i>
            @can('create subjects')
                <a href="{{ route('subjects.create') }}" class="btn btn-primary btn-sm float-end">Add New Subject</a>
            @endcan
        </div>
        <div class="card-body">
            <table id="datatablesSimple" class="table table-striped">
                <thead>
                    <tr>
                        <th>Subject Type</th>
                        <th>Department/Elective</th>
                        <th>Semester</th>
                        <th>Subject Name</th>
                        <th>Action</th>

                    </tr>
                </thead>
                <tbody>
                    @foreach ($subjects as $subject)
                        <tr>
                            <td>{{ $subject->subject_type }}</td>
                            <td>{{ $subject->department }}</td>
                            <td>{{ $subject->semester }}</td>
                            <td>{{ $subject->subject_name }}</td>
                            <td>

                                @can('edit subjects')
                                    <a href="{{ route('subjects.edit', Crypt::encryptString($subject->id)) }}"
                                        class="btn btn-warning btn-sm">
                                        Edit
                                    </a>
                                @endcan
                                @can('delete subjects')
                                    <!-- Delete Form -->
                                    <form id="delete-form-{{ $subject->id }}"
                                        action="{{ route('subjects.destroy', $subject->id) }}" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-danger btn-sm"
                                            onclick="confirmDelete({{ $subject->id }})">Delete</button>
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
        function confirmDelete(subjectId) {
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
                    document.getElementById('delete-form-' + subjectId).submit();
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
