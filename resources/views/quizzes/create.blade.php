@extends('layouts.admin')

@section('title', 'Create Quiz')

@section('content')
    <h1 class="mt-4">Create Quiz</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('quizzes.index') }}">Quizzes</a></li>
        <li class="breadcrumb-item active">Create Quiz</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-plus-circle me-1"></i>
            Add New Quiz
        </div>
        <div class="card-body">
            <form id="quizForm" action="{{ route('quizzes.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('POST')

                <div class="row g-3">
                    <!-- Subject Type -->
                    <div class="col-md-4">
                        <label for="subject_type" class="form-label">Subject Type<font color="red">*</font></label>
                        <select name="subject_type" id="subject_type" class="form-control" required>
                            <option value="" disabled selected>Select Subject Type</option>
                            <option value="CORE">CORE</option>
                            <option value="SEC">SEC</option>
                            <option value="VAC">VAC</option>
                            <option value="GE">GE</option>
                            <option value="AEC">AEC</option>
                            <option value="DSE">DSE</option>
                        </select>
                    </div>

                    <!-- Department -->
                    <div class="col-md-4">
                        <label for="department" class="form-label">Department/ELECTIVE<font color="red">*</font></label>
                        <select name="department" id="department" class="form-control" required>
                            <option value="" disabled selected>Select Department</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department }}">{{ $department }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Semester -->
                    <div class="col-md-4">
                        <label for="semester" class="form-label">Semester<font color="red">*</font></label>
                        <select name="semester" id="semester" class="form-control" required>
                            <option value="" disabled selected>Select Semester</option>
                            @for ($i = 1; $i <= 8; $i++)
                                <option value="{{ $i }}"> {{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </div>

                <div class="row g-3 mt-3">
                    <!-- Subject Name Field -->
                    <div class="col-md-4">
                        <label for="subject_name" class="form-label">Subject<font color="red">*</font></label>
                        <select name="subject_name" id="subject_name" class="form-control" required>
                            <option value="" disabled selected>Select Subject</option>
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->subject_name }}">{{ $subject->subject_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Faculty Name Field -->
                    <div class="col-md-4">
                        <label for="faculty_name" class="form-label">Faculty Name<font color="red">*</font></label>
                        <select name="faculty_name" id="faculty_name" class="form-select" required>
                            <option value="" disabled selected>Select Faculty Name</option>
                            @if ($roles->contains('Admin') || $roles->contains('SuperAdmin'))
                                <option value="Admin" class="admin-option">Admin</option>
                            @endif

                            @foreach ($faculties as $faculty)
                                <option value="{{ $faculty->name }}">{{ $faculty->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Quiz Date Field -->
                    <div class="col-md-4">
                        <label for="date" class="form-label">Quiz Date<font color="red">*</font></label>
                        <input type="date" name="date" id="date" class="form-control" required>
                    </div>
                </div>

                <div class="row g-3 mt-3">
                    <!-- Quiz Time Fields -->
                    <div class="col-md-6">
                        <label for="start_time" class="form-label">Start Time<font color="red">*</font></label>
                        <input type="time" name="start_time" class="form-control" id="start_time" required>
                    </div>

                    <div class="col-md-6">
                        <label for="end_time" class="form-label">End Time<font color="red">*</font></label>
                        <input type="time" name="end_time" class="form-control" id="end_time" required>
                    </div>
                </div>

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-primary">Create Your Questions</button>
                    <a href="{{ route('quizzes.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Trigger the subject filter based on selection changes
            $('#subject_type, #department, #semester').change(function() {
                var subjectType = $('#subject_type').val();
                let department = $('#department').val();
                var semester = $('#semester').val();

                // Filter subjects based on the selected type
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
                } else if (subjectType && semester) {
                    // If only subject type and semester are selected
                    $.ajax({
                        url: "/filter-subjects",
                        method: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            subject_type: subjectType,
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
                    // Reset subject dropdown if conditions are not met
                    $('#subject_name').empty();
                    $('#subject_name').append('<option value="" disabled selected>Select Subject</option>');
                }
            });

            // Filter the department dropdown
            $('#subject_type').change(function() {
                var subjectType = $(this).val();
                var departmentSelect = $('#department');

                departmentSelect.empty(); // Clear existing options
                departmentSelect.append('<option value="" disabled selected>Select Department</option>');

                if (subjectType === 'CORE' || subjectType === 'DSE') {
                    $.each(@json($departments), function(index, value) {
                        if (value !== 'ELECTIVE') {
                            departmentSelect.append('<option value="' + value + '">' + value +
                                '</option>');
                        }
                    });
                } else if (subjectType === 'VAC' || subjectType === 'SEC' || subjectType === 'GE' ||
                    subjectType === 'AEC') {
                    departmentSelect.append('<option value="ELECTIVE">ELECTIVE</option>');
                }
            });

            // Populate subject dropdown
            function populateSubjects(data) {
                var subjectSelect = $('#subject_name');
                subjectSelect.empty(); // Clear existing options
                subjectSelect.append('<option value="" disabled selected>Select Subject</option>');

                $.each(data, function(id, name) {
                    subjectSelect.append('<option value="' + name + '">' + name + '</option>');
                });
            }
        });

        document.getElementById('quizForm').addEventListener('submit', function(event) {
            const currentDate = new Date().toISOString().split('T')[0];
            const currentTime = new Date().toTimeString().split(' ')[0];

            const dateInput = document.getElementById('date').value;
            const startTimeInput = document.getElementById('start_time').value;
            const endTimeInput = document.getElementById('end_time').value;

            if (dateInput < currentDate) {
                event.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Date',
                    text: 'Date cannot be earlier than today!'
                });
                return;
            }

            if (dateInput === currentDate && startTimeInput < currentTime) {
                event.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Start Time',
                    text: 'Start time cannot be earlier than the current time!'
                });
                return;
            }

            if (dateInput === currentDate && endTimeInput < currentTime) {
                event.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid End Time',
                    text: 'End time cannot be earlier than the current time!'
                });
                return;
            }

            if (startTimeInput >= endTimeInput) {
                event.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Time Range',
                    text: 'End time must be after the start time!'
                });
            }
        });
    </script>

@endsection
