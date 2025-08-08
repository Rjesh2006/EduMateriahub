@extends('layouts.admin')

@section('title', 'Edit Study Material')

@section('content')
    <h1 class="mt-4">Edit Study Material</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('study_materials.index') }}">Study Materials</a></li>
        <li class="breadcrumb-item active">Edit Study Material</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-edit me-1"></i>
            Edit Study Material
        </div>
        <div class="card-body">
            <form action="{{ route('study_materials.update', Crypt::encryptString($studyMaterial->id)) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('POST')

                <div class="row g-3">
                    <!-- Subject Type Field -->
                    <div class="col-md-4">
                        <label for="subject_type" class="form-label">Subject Type<font color="red">*</font></label>
                        <select name="subject_type" id="subject_type" class="form-select" required>
                            <option value="CORE" {{ $studyMaterial->subject_type === 'CORE' ? 'selected' : '' }}>CORE
                            </option>
                            <option value="SEC" {{ $studyMaterial->subject_type === 'SEC' ? 'selected' : '' }}>SEC
                            </option>
                            <option value="VAC" {{ $studyMaterial->subject_type === 'VAC' ? 'selected' : '' }}>VAC
                            </option>
                            <option value="AEC" {{ $studyMaterial->subject_type === 'AEC' ? 'selected' : '' }}>AEC
                            </option>
                            <option value="GE" {{ $studyMaterial->subject_type === 'GE' ? 'selected' : '' }}>GE</option>
                            <option value="DSE" {{ $studyMaterial->subject_type === 'DSE' ? 'selected' : '' }}>DSE
                            </option>
                        </select>
                    </div>

                    <!-- Department Field -->
                    <div class="col-md-4">
                        <label for="department" class="form-label">Department/Elective</label>
                        <select name="department" id="department" class="form-control">
                            @foreach ($departments as $dept)
                                <option value="{{ $dept }}"
                                    {{ $studyMaterial->department === $dept ? 'selected' : '' }}>
                                    {{ $dept }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Semester Field -->
                    <div class="col-md-4">
                        <label for="semester" class="form-label">Semester<font color="red">*</font></label>
                        <select name="semester" id="semester" class="form-select" required>
                            @for ($i = 1; $i <= 8; $i++)
                                <option value="{{ $i }}"
                                    {{ $studyMaterial->semester == $i ? 'selected' : '' }}>
                                    {{ $i }}
                                </option>
                            @endfor
                        </select>
                    </div>
                </div>

                <div class="row g-3 mt-2">
                    <!-- Subject Name Field -->
                    <div class="col-md-4">
                        <label for="subject_name" class="form-label">Subject<font color="red">*</font></label>
                        <select name="subject_name" id="subject_name" class="form-control" required>
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject }}"
                                    {{ $studyMaterial->subject_name === $subject ? 'selected' : '' }}>
                                    {{ $subject }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Faculty Name Field -->
                    <div class="col-md-4">
                        <label for="faculty_name" class="form-label">Faculty Name<font color="red">*</font></label>
                        <select name="faculty_name" id="faculty_name" class="form-control" required>
                            @foreach ($faculties as $faculty)
                                <option value="{{ $faculty->name }}"
                                    {{ $studyMaterial->faculty_name === $faculty->name ? 'selected' : '' }}>
                                    {{ $faculty->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- File Upload Field -->
                    <div class="col-md-4">
                        <label for="file" class="form-label">Upload File (Optional)</label>
                        <input type="file" name="file" id="file" class="form-control">
                        <p class="mt-3"><small style="font-weight: 500;">Current File: </small>
                            <a href="{{ asset('storage/' . $studyMaterial->file) }}" target="_blank"
                                class="btn btn-outline-primary">
                                <i class="fas fa-file-alt me-2"></i>View File
                            </a>
                        </p>

                    </div>
                </div>

                <div class="row g-3 mt-2"><!-- Description Field -->
                    <div class="col-md-4">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" id="description" rows="4" class="form-control" required>{{ $studyMaterial->description }}</textarea>
                    </div>
                </div>


                <!-- Buttons -->
                <div class="text-end">
                    <a href="{{ route('study_materials.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>

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
                        url: "{{ route('filter.subjects') }}",
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
