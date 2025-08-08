@extends('layouts.admin')

@section('title', 'Edit Admin')

@section('content')
    <h1 class="mt-4">Edit Admin</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ url('/superadmin/dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('users.list') }}">User Management</a></li>
        <li class="breadcrumb-item active">Edit Admin</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-user-edit me-1"></i>
            Edit User Details
            <a href="{{ route('admins.index') }}" class="btn btn-primary btn-sm float-end">Back</a>
        </div>
        <div class="card-body">
            <form action="{{ route('admins.update', Crypt::encryptString($admin->id)) }}" method="POST">
                @csrf
                @method('POST')

                <div class="row g-3">
                    <!-- Name Field -->
                    <div class="form-group col-md-4">
                        <label for="name" class="form-label font-weight-bold">Name</label>
                        <input type="text" name="name" id="name" class="form-control"
                            placeholder="Enter faculty name" value="{{ old('name', $admin->name) }}" required>
                        @error('name')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Email Field -->
                    <div class="form-group col-md-4">
                        <label for="email" class="form-label font-weight-bold">Email</label>
                        <input type="email" name="email" id="email" class="form-control"
                            placeholder="Enter faculty email" value="{{ old('email', $admin->email) }}" required>
                        @error('email')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Phone Field -->
                    <div class="form-group col-md-4">
                        <label for="phone" class="form-label font-weight-bold">Phone</label>
                        <input type="text" name="phone" id="phone" class="form-control"
                            placeholder="Enter phone number" value="{{ old('phone', $admin->phone) }}" required>
                        @error('phone')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="row g-3 mt-2">
                    <!-- Department Field -->
                    <div class="form-group col-md-4">
                        <label for="department" class="form-label font-weight-bold">Department</label>
                        <select name="department" id="department" class="form-select" required>
                            <option value="" disabled>Select Department</option>
                            <option value="Applied Psychology"
                                {{ old('department', $admin->department) == 'Applied Psychology' ? 'selected' : '' }}>
                                Department of Applied Psychology
                            </option>
                            <option value="Computer Science"
                                {{ old('department', $admin->department) == 'Computer Science' ? 'selected' : '' }}>
                                Department of Computer Science
                            </option>
                            <option value="B.voc(Software Development)"
                                {{ old('department', $admin->department) == 'B.voc(Software Development)' ? 'selected' : '' }}>
                                Department of B.voc (Software Development)
                            </option>
                            <option value="Economics"
                                {{ old('department', $admin->department) == 'Economics' ? 'selected' : '' }}>
                                Department of Economics
                            </option>
                            <option value="English"
                                {{ old('department', $admin->department) == 'English' ? 'selected' : '' }}>
                                Department of English
                            </option>
                            <option value="Environmental Studies"
                                {{ old('department', $admin->department) == 'Environmental Studies' ? 'selected' : '' }}>
                                Department of Environmental Studies
                            </option>
                            <option value="Commerce"
                                {{ old('department', $admin->department) == 'Commerce' ? 'selected' : '' }}>
                                Department of Commerce
                            </option>
                            <option value="Punjabi"
                                {{ old('department', $admin->department) == 'Punjabi' ? 'selected' : '' }}>
                                Department of Punjabi
                            </option>
                            <option value="Hindi"
                                {{ old('department', $admin->department) == 'Hindi' ? 'selected' : '' }}>
                                Department of Hindi
                            </option>
                            <option value="History"
                                {{ old('department', $admin->department) == 'History' ? 'selected' : '' }}>
                                Department of History
                            </option>
                            <option value="Management Studies"
                                {{ old('department', $admin->department) == 'Management Studies' ? 'selected' : '' }}>
                                Department of Management Studies
                            </option>
                            <option value="Mathematics"
                                {{ old('department', $admin->department) == 'Mathematics' ? 'selected' : '' }}>
                                Department of Mathematics
                            </option>
                            <option value="Philosophy"
                                {{ old('department', $admin->department) == 'Philosophy' ? 'selected' : '' }}>
                                Department of Philosophy
                            </option>
                            <option value="Physical Education"
                                {{ old('department', $admin->department) == 'Physical Education' ? 'selected' : '' }}>
                                Department of Physical Education
                            </option>
                            <option value="Political Science"
                                {{ old('department', $admin->department) == 'Political Science' ? 'selected' : '' }}>
                                Department of Political Science
                            </option>
                            <option value="Statistics"
                                {{ old('department', $admin->department) == 'Statistics' ? 'selected' : '' }}>
                                Department of Statistics
                            </option>
                            <option value="B.voc(Banking)"
                                {{ old('department', $admin->department) == 'B.voc(Banking)' ? 'selected' : '' }}>
                                Department of B.voc (Banking)
                            </option>
                            <option value="Admin"
                                {{ old('department', $admin->department) == 'Admin' ? 'selected' : '' }}>
                                Admin
                            </option>
                        </select>
                        @error('department')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Roles Checkboxes -->
                    <div class="form-group col-md-4">
                        <label for="roles" class="form-label font-weight-bold">Roles</label>
                        <div class="d-flex flex-wrap gap-3 mt-2">
                            @if ($roles->isNotEmpty())
                                @foreach ($roles as $role)
                                    <div class="form-check">
                                        <input type="checkbox" name="role[]" id="role-{{ $role->id }}"
                                            value="{{ $role->name }}" class="form-check-input role-checkbox"
                                            {{ $hasRoles->contains($role->id) ? 'checked' : '' }}
                                            data-role-name="{{ $role->name }}">
                                        <label for="role-{{ $role->id }}"
                                            class="form-check-label">{{ $role->name }}</label>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-muted">No roles available.</p>
                            @endif
                        </div>
                    </div>

                    <!-- Roll Number Field -->
                    <div class="form-group col-md-4" id="roll-no-field" style="display: none;">
                        <label for="roll_no" class="form-label font-weight-bold">Roll Number</label>
                        <input type="text" name="roll_no" id="roll_no" class="form-control"
                            placeholder="Enter Roll Number" value="{{ old('roll_no', $admin->roll_no ?? '') }}">
                        @error('roll_no')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="row g-3 mt-2">
                    <!-- Semester Field -->
                    <div class="form-group col-md-4" id="semester-field" style="display: none;">
                        <label for="semester" class="form-label font-weight-bold">Semester</label>
                        <select name="semester" id="semester" class="form-select">
                            <option value="" disabled>Select Semester</option>
                            @foreach (range(1, 8) as $semester)
                                <option value="{{ $semester }}"
                                    {{ old('semester', $admin->semester ?? '') == $semester ? 'selected' : '' }}>
                                    {{ $semester }}
                                </option>
                            @endforeach
                        </select>
                        @error('semester')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
                </div>


                <!-- Submit Button -->
                <div class="text-end">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-1"></i> Update User
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Show/Hide Semester Field -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const semesterField = document.getElementById('semester-field');
            const rollNoField = document.getElementById('roll-no-field');
            const roleCheckboxes = document.querySelectorAll('.role-checkbox');

            // Function to toggle fields based on 'student' role
            function toggleStudentFields() {
                let isStudentChecked = Array.from(roleCheckboxes).some(checkbox =>
                    checkbox.dataset.roleName === 'student' && checkbox.checked
                );

                semesterField.style.display = isStudentChecked ? 'block' : 'none';
                rollNoField.style.display = isStudentChecked ? 'block' : 'none';
            }

            // Add event listeners to checkboxes
            roleCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', toggleStudentFields);
            });

            // Initial toggle on page load
            toggleStudentFields();
        });
    </script>
@endsection
