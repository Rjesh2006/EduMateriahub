@extends('layouts.admin')

@section('title', 'Quiz Reports')

@section('content')
    <h1 class="mt-4 text-primary fw-bold text-center">📊 Quiz Reports</h1>
    <ol class="breadcrumb mb-4 bg-gradient p-3 rounded shadow-sm text-white"
        style="background: linear-gradient(45deg, #007bff, #6610f2);">
        <li class="breadcrumb-item">
            @can('is superadmin')
                <a href="{{ route('superadmin.dashboard') }}" class="text-black text-decoration-none fw-bold">🏠 Dashboard</a>
            @else
                <a href="{{ route('others.dashboard') }}" class="text-black text-decoration-none fw-bold">🏠 Dashboard</a>
            @endcan
        </li>
        <li class="breadcrumb-item active text-black fw-bold">Quiz Reports</li>
    </ol>

    <div class="card shadow-sm border-0 rounded-lg mb-4">
        <div class="card-body bg-light p-4 rounded">
            <form id="quizForm" action="{{ route('quiz_reports.fetch-quizzes') }}" method="POST" class="needs-validation"
                novalidate>
                @csrf
                <div class="row g-3">
                    <div class="col-md-2">
                        <label for="subject_type" class="form-label fw-bold">📖 Subject Type</label>
                        <select name="subject_type" id="subject_type" class="form-control form-select-lg shadow-sm"
                            required>
                            <option value="" disabled selected>Select</option>
                            <option value="CORE">CORE</option>
                            <option value="SEC">SEC</option>
                            <option value="VAC">VAC</option>
                            <option value="GE">GE</option>
                            <option value="AEC">AEC</option>
                            <option value="DSE">DSE</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="department" class="form-label fw-bold">🏛 Department</label>
                        <select name="department" id="department" class="form-control form-select-lg shadow-sm" required>
                            <option value="" disabled selected>Select</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department }}">{{ $department }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="semester" class="form-label fw-bold">📅 Semester</label>
                        <select name="semester" id="semester" class="form-control form-select-lg shadow-sm" required>
                            <option value="" disabled selected>Select</option>
                            @for ($i = 1; $i <= 8; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="subject_name" class="form-label fw-bold">📚 Subject</label>
                        <select name="subject_name" id="subject_name" class="form-control form-select-lg shadow-sm"
                            required>
                            <option value="" disabled selected>Select</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="faculty_name" class="form-label fw-bold">👨‍🏫 Faculty Name</label>
                        <select name="faculty_name" id="faculty_name" class="form-select form-select-lg shadow-sm" required>
                            <option value="" disabled selected>Select</option>
                            @if ($roles->contains('Admin') || $roles->contains('SuperAdmin'))
                                <option value="Admin">Admin</option>
                            @endif
                            @foreach ($faculties as $faculty)
                                <option value="{{ $faculty->name }}">{{ $faculty->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-lg btn-primary shadow-lg px-4 fw-bold">🔍 Find</button>
                </div>
            </form>
        </div>
    </div>

    @if (isset($quizzes) && $quizzes->count() > 0)
        <div class="card shadow-lg border-0 mt-4">
            <div class="card-header bg-gradient text-black fw-bold text-center"
                style="background: linear-gradient(45deg, #28a745, #218838);">
                📜 Quizzes List
            </div>
            <div class="card-body">
                <table class="table table-hover table-bordered text-center shadow-sm rounded">
                    <thead class="table-dark">
                        <tr>
                            <th>🆔 Quiz ID</th>
                            <th>📖 Subject Name</th>
                            <th>📆 Created At</th>
                            <th>🔍 Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($quizzes as $quiz)
                            <tr>
                                <td>{{ $quiz->id }}</td>
                                <td>{{ $quiz->subject_name }}</td>
                                <td>{{ $quiz->created_at->format('d M Y, h:i A') }}</td>
                                <td>
                                    <a href="{{ route('quiz_reports.viewResults', ['quiz_id' => $quiz->id]) }}"
                                        class="btn btn-sm btn-success shadow-sm fw-bold">📊 View Results</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @elseif(request()->has('subject_type'))
        <p class="text-center mt-4 text-muted">⚠️ No quizzes found matching the criteria.</p>
    @endif

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
@endsection
