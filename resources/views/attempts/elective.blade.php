@extends('layouts.admin')

@section('title', 'Elective Test')

@section('content')
    <div class="page-wrapper">
        <div class="page-content">
            <!-- Page Header -->
            <div class="mb-4">
                <h1 class="h1">Elective Test</h1>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        @can('is superadmin')
                            <a href="{{ route('superadmin.dashboard') }}">Dashboard</a>
                        @else
                            <a href="{{ route('others.dashboard') }}">Dashboard</a>
                        @endcan
                    </li>
                    <li class="breadcrumb-item active">Elective Test</li>
                </ol>
            </div>

            <!-- Quiz Table Card -->
            <div class="card shadow-lg rounded-lg">
                <div class="card-header bg-primary text-white rounded-top">
                    <i class="fas fa-question-circle me-2"></i>
                    Elective Test Information
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('attempts.filter-quiz') }}" method="POST" class="needs-validation" novalidate>
                        @csrf
                        <div class="row g-3 align-items-center">
                            <!-- Subject Type -->
                            <div class="col-md-2">
                                <label for="subject_type" class="form-label">Subject Type</label>
                                <select name="subject_type" id="subject_type" class="form-control form-select-lg" required>
                                    <option value="" disabled selected>Select</option>
                                    <option value="CORE">CORE</option>
                                    <option value="SEC">SEC</option>
                                    <option value="VAC">VAC</option>
                                    <option value="GE">GE</option>
                                    <option value="AEC">AEC</option>
                                    <option value="DSE">DSE</option>
                                </select>
                            </div>

                            <!-- Department -->
                            <div class="col-md-2">
                                <label for="department" class="form-label">Department</label>
                                <select name="department" id="department" class="form-control form-select-lg" required>
                                    <option value="" disabled selected>Select</option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department }}">{{ $department }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Semester -->
                            <div class="col-md-2">
                                <label for="semester" class="form-label">Semester</label>
                                <select name="semester" id="semester" class="form-control form-select-lg" required>
                                    <option value="" disabled selected>Select</option>
                                    @for ($i = 1; $i <= 8; $i++)
                                        <option value="{{ $i }}"> {{ $i }}</option>
                                    @endfor
                                </select>
                            </div>

                            <!-- Subject Name -->
                            <div class="col-md-3">
                                <label for="subject_name" class="form-label">Subject</label>
                                <select name="subject_name" id="subject_name" class="form-control form-select-lg" required>
                                    <option value="" disabled selected>Select</option>
                                </select>
                            </div>

                            <div class="text-end mt-4">
                                <button type="submit" class="btn btn-lg btn-primary">Find</button>
                            </div>
                        </div>
                    </form>

                    <!-- Quizzes List -->
                    @if (isset($quizzes) && $quizzes->count() > 0)
                        <div class="mt-4">
                            <table class="table table-striped table-bordered table-hover">
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
                                        @php
                                            $currentDateTime = now();
                                            $quizStartDateTime = \Carbon\Carbon::parse(
                                                $quiz->date . ' ' . $quiz->start_time,
                                            );
                                            $quizEndDateTime = \Carbon\Carbon::parse(
                                                $quiz->date . ' ' . $quiz->end_time,
                                            );
                                        @endphp
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
                                                @if ($currentDateTime->lt($quizStartDateTime))
                                                    <span id="timer-{{ $quiz->id }}"
                                                        class="text-danger fw-bold"></span>
                                                    <button id="start-btn-{{ $quiz->id }}"
                                                        class="btn btn-primary btn-sm" disabled>Start Quiz</button>
                                                    <script>
                                                        const startTime{{ $quiz->id }} = new Date("{{ $quizStartDateTime }}").getTime();
                                                        const timer{{ $quiz->id }} = setInterval(() => {
                                                            const now = new Date().getTime();
                                                            const distance = startTime{{ $quiz->id }} - now;
                                                            if (distance <= 0) {
                                                                clearInterval(timer{{ $quiz->id }});
                                                                document.getElementById("timer-{{ $quiz->id }}").innerText = "Quiz is live!";
                                                                const startBtn = document.getElementById("start-btn-{{ $quiz->id }}");
                                                                startBtn.removeAttribute('disabled');
                                                                startBtn.onclick = function() {
                                                                    window.location.href = "{{ route('attempts.create', $quiz->id) }}";
                                                                };
                                                            } else {
                                                                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                                                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                                                                const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                                                                document.getElementById("timer-{{ $quiz->id }}").innerText =
                                                                    `Starts in: ${hours}h ${minutes}m ${seconds}s`;
                                                            }
                                                        }, 1000);
                                                    </script>
                                                @elseif ($currentDateTime->gt($quizEndDateTime))
                                                    <a href="{{ route('attempts.results', ['quizId' => $quiz->id]) }}"
                                                        class="btn btn-info btn-sm">View Result</a>
                                                @else
                                                    <a href="{{ route('attempts.create', $quiz->id) }}"
                                                        class="btn btn-primary btn-sm">Start Quiz</a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @elseif (!isset($quizzes) || $quizzes->count() == 0)
                        <script>
                            Swal.fire({
                                icon: 'info',
                                title: 'No Upcoming Tests',
                                text: 'There are no upcoming tests for the selected subject.',
                            });
                        </script>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#subject_type, #department, #semester').change(function() {
                var subjectType = $('#subject_type').val();
                let department = $('#department').val();
                var semester = $('#semester').val();

                if (subjectType && department && semester) {
                    $.ajax({
                        url: "/filter-subjects",
                        method: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            subject_type: subjectType,
                            department: department,
                            semester: semester
                        },
                        success: function(data) {
                            populateSubjects(data);
                        },
                        error: function(error) {
                            console.log(error);
                            alert('Error fetching subjects');
                        }
                    });
                } else {
                    $('#subject_name').empty();
                    $('#subject_name').append('<option value="" disabled selected>Select Subject</option>');
                }
            });

            function populateSubjects(data) {
                var subjectSelect = $('#subject_name');
                subjectSelect.empty();
                subjectSelect.append('<option value="" disabled selected>Select Subject</option>');

                $.each(data, function(id, name) {
                    subjectSelect.append('<option value="' + name + '">' + name + '</option>');
                });
            }

        });
    </script>
    <script>
        $(document).ready(function() {
            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: '{{ session('error') }}',
                    confirmButtonText: 'OK', // Custom OK button text
                }).then((result) => {
                    if (result.isConfirmed) {
                        // "OK" button is clicked, redirect to attempts.index
                        window.location.href = "{{ route('attempts.index') }}";
                    }
                });
            @endif
        });
    </script>
@endsection
