@extends('layouts.admin')

@section('title', 'Edit User')

@section('content')
    <h1 class="mt-4">Edit User</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('users.list') }}">Users</a></li>
        <li class="breadcrumb-item active">Edit User</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-user-edit me-1"></i>
            Edit User Details
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('users.update', Crypt::encryptString($user->id)) }}"
                class="form-horizontal">
                @csrf
                @method('POST')

                <div class="row g-3">
                    <!-- Name Field -->
                    <div class="col-md-4">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" name="name" id="name" class="form-control"
                            value="{{ old('name', $user->name) }}" required autofocus>
                        @error('name')
                            <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email Field -->
                    <div class="col-md-4">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control"
                            value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Phone Field -->
                    <div class="col-md-4">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="text" name="phone" id="phone" class="form-control"
                            value="{{ old('phone', $user->phone) }}" required>
                        @error('phone')
                            <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row g-3 mt-2">
                    <!-- Department Field -->
                    <div class="col-md-4">
                        <label for="department" class="form-label">Department</label>
                        <select name="department" id="department" class="form-select" required>
                            <option value="" disabled>Select Department</option>
                            <option value="Applied Psychology"
                                {{ old('department', $user->department) == 'Applied Psychology' ? 'selected' : '' }}>
                                Department of Applied Psychology</option>
                            <option value="Computer Science"
                                {{ old('department', $user->department) == 'Computer Science' ? 'selected' : '' }}>
                                Department of Computer Science</option>
                            <option value="B.voc(Software Development)"
                                {{ old('department', $user->department) == 'B.voc(Software Development)' ? 'selected' : '' }}>
                                Department of B.voc (Software Development)</option>
                            <option value="Economics"
                                {{ old('department', $user->department) == 'Economics' ? 'selected' : '' }}>Department of
                                Economics</option>
                            <option value="English"
                                {{ old('department', $user->department) == 'English' ? 'selected' : '' }}>Department of
                                English</option>
                            <option value="Environmental Studies"
                                {{ old('department', $user->department) == 'Environmental Studies' ? 'selected' : '' }}>
                                Department of Environmental Studies</option>
                            <option value="Commerce"
                                {{ old('department', $user->department) == 'Commerce' ? 'selected' : '' }}>Department of
                                Commerce</option>
                            <option value="Punjabi"
                                {{ old('department', $user->department) == 'Punjabi' ? 'selected' : '' }}>Department of
                                Punjabi</option>
                            <option value="Hindi" {{ old('department', $user->department) == 'Hindi' ? 'selected' : '' }}>
                                Department of Hindi</option>
                            <option value="History"
                                {{ old('department', $user->department) == 'History' ? 'selected' : '' }}>Department of
                                History</option>
                            <option value="Management Studies"
                                {{ old('department', $user->department) == 'Management Studies' ? 'selected' : '' }}>
                                Department of Management Studies</option>
                            <option value="Mathematics"
                                {{ old('department', $user->department) == 'Mathematics' ? 'selected' : '' }}>Department of
                                Mathematics</option>
                            <option value="Philosophy"
                                {{ old('department', $user->department) == 'Philosophy' ? 'selected' : '' }}>Department of
                                Philosophy</option>
                            <option value="Physical Education"
                                {{ old('department', $user->department) == 'Physical Education' ? 'selected' : '' }}>
                                Department of Physical Education</option>
                            <option value="Political Science"
                                {{ old('department', $user->department) == 'Political Science' ? 'selected' : '' }}>
                                Department of Political Science</option>
                            <option value="Statistics"
                                {{ old('department', $user->department) == 'Statistics' ? 'selected' : '' }}>Department of
                                Statistics</option>
                            <option value="admin" {{ old('department', $user->department) == 'admin' ? 'selected' : '' }}>
                                Admin</option>
                            <option value="B.voc(Banking Operations)"
                                {{ old('department', $user->department) == 'B.voc(Banking Operations)' ? 'selected' : '' }}>
                                Department of B.voc (Banking)</option>
                        </select>
                        @error('department')
                            <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Roles Checkboxes -->
                    <div class="form-group col-md-4">
                        <label for="roles" class="form-label font-weight-bold">Roles</label>
                        <div class="d-flex flex-wrap gap-3 mt-2">
                            @foreach ($roles as $role)
                                <div class="form-check">
                                    <input type="checkbox" name="role[]" id="role-{{ $role->id }}"
                                        value="{{ $role->name }}" class="form-check-input"
                                        {{ $hasRoles->contains($role->id) ? 'checked' : '' }}>
                                    <label for="role-{{ $role->id }}"
                                        class="form-check-label">{{ $role->name }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Semester Field -->
                    <div class="col-md-4" id="semester-field"
                        style="{{ $hasRoles->contains('student') ? '' : 'display: none;' }}">
                        <label for="semester" class="form-label">Semester</label>
                        <select name="semester" id="semester" class="form-select">
                            <option value="" disabled>Select Semester</option>
                            @for ($i = 1; $i <= 8; $i++)
                                <option value="{{ $i }}"
                                    {{ old('semester', $user->semester) == $i ? 'selected' : '' }}>{{ $i }}
                                </option>
                            @endfor
                        </select>
                        @error('semester')
                            <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                </div>


                <!-- Roll No Field -->
                <div class="row g-3 mt-3">
                    <div class="col-md-4" id="rollno-field"
                        style="{{ $hasRoles->contains('student') ? '' : 'display: none;' }}">
                        <label for="roll_no" class="form-label">Roll No<span class="text-danger">*</span></label>
                        <input type="text" name="roll_no" id="roll_no" class="form-control"
                            value="{{ old('roll_no', $user->roll_no) }}">
                        @error('roll_no')
                            <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                </div>



                <div class="d-flex justify-content-between">
                    <a href="{{ route('users.list') }}" class="btn btn-secondary mt-2">Back</a>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const roleCheckboxes = document.querySelectorAll('input[name="role[]"]');
            const semesterField = document.getElementById('semester-field');
            const rollNoField = document.getElementById('rollno-field');

            function toggleFields() {
                const isStudentSelected = Array.from(roleCheckboxes).some(checkbox =>
                    checkbox.checked && checkbox.value === 'student'
                );
                semesterField.style.display = isStudentSelected ? 'block' : 'none';
                rollNoField.style.display = isStudentSelected ? 'block' : 'none';
            }

            // Attach the toggle function to all role checkboxes
            roleCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', toggleFields);
            });

            // Initialize the visibility on page load
            toggleFields();
        });
    </script>
@endsection
