    @extends('layouts.admin')

    @section('title', 'Quiz Management')

    @section('content')
        @php
            use Carbon\Carbon;
            $current_time = Carbon::now();
        @endphp
        <div class="page-wrapper" style="margin-top: 3rem;">
            <div class="page-content">
                <!-- Page Title and Breadcrumb -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="h1">Quiz Management</h3>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            @can('is superadmin')
                                <a href="{{ route('superadmin.dashboard') }}">Dashboard</a>
                            @else
                                <a href="{{ route('others.dashboard') }}">Dashboard</a>
                            @endcan
                        </li>
                        <li class="breadcrumb-item active">Quizzes</li>
                    </ol>
                </div>

                <!-- Card for Quizzes -->
                <div class="card shadow-lg rounded-lg">
                    <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-question-circle me-2"></i> Manage Quizzes</h5>
                        @can('create quiz')
                            <a href="{{ route('quizzes.create') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-plus-circle me-1"></i> Create Quiz
                            </a>
                        @endcan
                    </div>
                    <div class="card-body p-4">
                        <table id="datatablesSimple" class="table table-striped table-hover">
                            <thead class="table-primary">
                                <tr>
                                    <th>#</th>
                                    <th>Subject Type</th>
                                    <th>Department</th>
                                    <th>Semester</th>
                                    <th>Subject Name</th>
                                    <th>Faculty Name</th>
                                    <th>Date</th>
                                    <th>Start Time</th>
                                    <th>End Time</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($quizzes as $index => $quiz)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $quiz->subject_type }}</td>
                                        <td>{{ $quiz->department }}</td>
                                        <td>{{ $quiz->semester }}</td>
                                        <td>{{ $quiz->subject_name }}</td>
                                        <td>{{ $quiz->faculty_name }}</td>
                                        <td>{{ $quiz->date }}</td>
                                        <td>{{ $quiz->start_time }}</td>
                                        <td>{{ $quiz->end_time }}</td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-2">
                                                @if ($quiz->questions && $quiz->questions->where('is_submitted', 1)->count() > 0)
                                                    <button class="btn btn-secondary btn-sm" disabled>Questions
                                                        Finalized</button>
                                                @else
                                                    <a href="{{ route('questions.index', $quiz->id) }}"
                                                        class="btn btn-primary btn-sm">
                                                        Add Question
                                                    </a>
                                                @endif


                                                @if ($quiz->status == 1)
                                                    <button class="btn btn-secondary btn-sm" disabled>Test Started</button>
                                                @elseif (Carbon::parse($quiz->end_time)->lt($current_time))
                                                    <button class="btn btn-dark btn-sm" disabled>Test Ended</button>
                                                @else
                                                    <a href="{{ route('quizzes.show', $quiz->id) }}"
                                                        class="btn btn-info btn-sm">
                                                        Preview & Start
                                                    </a>
                                                @endif

                                                @can('edit quiz')
                                                    <a href="{{ route('quizzes.edit', $quiz->id) }}"
                                                        class="btn btn-warning btn-sm">
                                                        Edit
                                                    </a>
                                                @endcan
                                                @can('delete quiz')
                                                    <form action="{{ route('quizzes.destroy', $quiz->id) }}" method="POST"
                                                        style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="btn btn-danger btn-sm"
                                                            onclick="confirmDelete({{ $quiz->id }})">
                                                            Delete
                                                        </button>
                                                    </form>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Include SweetAlert CSS and JS -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <!-- SweetAlert Confirmation for Delete -->
        <script>
            function confirmDelete(quizId) {
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
                        document.querySelector(`form[action='${window.location.origin}/quizzes/${quizId}']`).submit();
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
            .card-header {
                font-size: 1.25rem;
                font-weight: bold;
            }

            table thead {
                font-size: 1rem;
                font-weight: 600;
            }

            table tbody tr:hover {
                background-color: #f8f9fa;
            }

            .btn {
                font-size: 0.875rem;
            }

            .btn-sm {
                padding: 0.375rem 0.75rem;
                border-radius: 0.2rem;
            }
        </style>
    @endsection
