@extends('layouts.admin')

@section('title', 'Edit Quiz')

@section('content')
    <h1 class="mt-4">Edit Quiz</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('quizzes.index') }}">Quizzes</a></li>
        <li class="breadcrumb-item active">Edit Quiz</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-edit me-1"></i>
            Edit Quiz Details
        </div>
        <div class="card-body">
            <form id="quizForm" action="{{ route('quizzes.update', $quiz->id) }}" method="POST">
                @csrf
                @method('PUT') <!-- To specify it's a PUT request for update -->

                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="subject_type" class="form-label">Subject Type</label>
                        <select name="subject_type" id="subject_type" class="form-control" required>
                            <option value="" disabled>Select Subject Type</option>
                            <option value="CORE" {{ $quiz->subject_type == 'CORE' ? 'selected' : '' }}>CORE</option>
                            <option value="SEC" {{ $quiz->subject_type == 'SEC' ? 'selected' : '' }}>SEC</option>
                            <option value="VAC" {{ $quiz->subject_type == 'VAC' ? 'selected' : '' }}>VAC</option>
                            <option value="GE" {{ $quiz->subject_type == 'GE' ? 'selected' : '' }}>GE</option>
                            <option value="AEC" {{ $quiz->subject_type == 'AEC' ? 'selected' : '' }}>AEC</option>
                            <option value="DSE" {{ $quiz->subject_type == 'DSE' ? 'selected' : '' }}>DSE</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="department" class="form-label">Department/ELECTIVE</label>
                        <select name="department" id="department" class="form-control" required>
                            <option value="" disabled>Select Department</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department }}"
                                    {{ $quiz->department == $department ? 'selected' : '' }}>
                                    {{ $department }}</option>
                            @endforeach
                        </select>
                    </div>


                    <div class="col-md-4">
                        <label for="semester" class="form-label">Semester</label>
                        <select name="semester" id="semester" class="form-control" required>
                            <option value="" disabled>Select Semester</option>
                            @for ($i = 1; $i <= 8; $i++)
                                <option value="{{ $i }}" {{ $quiz->semester == $i ? 'selected' : '' }}>
                                    {{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </div>

                <div class="row g-3 mt-2">
                    <div class="col-md-4">
                        <label for="subject_name" class="form-label">Subject Name</label>
                        <select name="subject_name" id="subject_name" class="form-control" required>
                            <option value="" disabled>Select Subject</option>
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->subject_name }}"
                                    {{ $quiz->subject_name == $subject->subject_name ? 'selected' : '' }}>
                                    {{ $subject->subject_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>


                    <div class="col-md-4">
                        <label for="faculty_name" class="form-label">Faculty Name</label>
                        <select name="faculty_name" id="faculty_name" class="form-control" required>
                            <option value="" disabled>Select Faculty Name</option>
                            @if ($roles->contains('Admin') || $roles->contains('SuperAdmin'))
                                <option value="Admin" class="admin-option"
                                    {{ $quiz->faculty_name == 'Admin' ? 'selected' : '' }}>Admin</option>
                            @endif
                            @foreach ($faculties as $faculty)
                                <option value="{{ $faculty->name }}"
                                    {{ $quiz->faculty_name == $faculty->name ? 'selected' : '' }}>{{ $faculty->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" name="date" class="form-control" id="date"
                            value="{{ old('date', $quiz->date) }}" required>
                    </div>
                </div>

                <div class="row g-3 mt-2">
                    <div class="col-md-4">
                        <label for="start_time" class="form-label">Start Time</label>
                        <input type="time" name="start_time" class="form-control" id="start_time"
                            value="{{ old('start_time', $quiz->start_time) }}" required>
                    </div>

                    <div class="col-md-4">
                        <label for="end_time" class="form-label">End Time</label>
                        <input type="time" name="end_time" class="form-control" id="end_time"
                            value="{{ old('end_time', $quiz->end_time) }}" required>
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary">Update Quiz</button>
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
