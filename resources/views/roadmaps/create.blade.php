@extends('layouts.admin')

@section('title', 'Roadmaps')

@section('content')
    <h1 class="mt-4">Add New Roadmaps</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
        <li class="breadcrumb-item active">Add Roadmaps</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-plus-circle me-1"></i>
            Add New Study Roadmaps
        </div>
        <div class="card-body">
            <form action="{{ route('roadmaps.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('POST')
                <div class="row g-3">
                    <!-- Subject Type Field -->
                    <div class="col-md-4">
                        <label for="subject_type" class="form-label">Subject Type<font color="red">*</font></label>
                        <select name="subject_type" id="subject_type" class="form-select" required>
                            <option value="" disabled selected>Select Subject Type</option>

                            <option value="CORE">CORE</option>
                            <option value="SEC">SEC</option>
                            <option value="VAC">VAC</option>
                            <option value="AEC">AEC</option>
                            <option value="GE">GE</option>
                            <option value="DSE">DSE</option>

                        </select>
                    </div>

                    <!-- Department Field -->
                    <div class="col-md-4">
                        <label for="department" class="form-label">Department/Elective <span
                                class="text-danger">*</span></label>
                        <select name="department" id="department" class="form-control">
                            <option value="" disabled selected>Select Department</option>
                            @foreach ($departments as $dept)
                                <option value="{{ $dept }}">{{ $dept }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Semester Field -->
                    <div class="col-md-4">
                        <label for="semester" class="form-label">Semester<font color="red">*</font></label>
                        <select name="semester" id="semester" class="form-select" required>
                            <option value="" disabled selected>Select Semester</option>

                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>
                            <option value="7">7</option>
                            <option value="8">8</option>
                        </select>
                    </div>

                </div>

                <div class="row g-3 mt-2"><!-- Subject Name Field -->
                    <div class="col-md-4">
                        <label for="subject_name" class="form-label">Subject<font color="red">*</font></label>
                        <select name="subject_name" id="subject_name" class="form-control" required>
                            <option value="" disabled selected>Select Subject</option>
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject }}">{{ $subject }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Faculty Name Field -->
                    <div class="col-md-4">
                        <label for="faculty_name" class="form-label">Faculty Name<font color="red">*</font></label>
                        <select name="faculty_name" id="faculty_name" class="form-control" required>
                            <option value="" disabled selected>Select Faculty Name</option>

                            <!-- Conditionally show the "Admin" option -->
                            @if ($roles->contains('Admin') || $roles->contains('SuperAdmin'))
                                <option value="Admin" class="admin-option">Admin</option>
                            @endif

                            @foreach ($faculties as $faculty)
                                <option value="{{ $faculty->name }}">{{ $faculty->name }}</option>
                            @endforeach
                        </select>
                    </div>


                    <!-- File Upload Field -->
                    <div class="col-md-4">
                        <label for="file" class="form-label">Upload File<font color="red">*</font></label>
                        <input type="file" name="file" id="file" class="form-control" required>
                    </div>
                </div>

                <div class="row g-3 mt-2">
                    <!-- Description Field -->
                    <div class="col-md-4">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" id="description" rows="4" class="form-control"
                            placeholder="Enter a brief description about the roadmap" required></textarea>
                    </div>
                </div>


                <!-- Buttons -->
                <div class="text-end">
                    <a href="{{ route('roadmaps.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>

    <!-- JavaScript Logic -->
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
                        url: "{{ route('filter.subjects') }}",
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
    </script>
@endsection
