@extends('layouts.admin')

@section('title', 'Edit Student')

@section('content')
    <h1 class="mt-4">Edit Student</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ url('/superadmin/dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('students.index') }}">Student Management</a></li>
        <li class="breadcrumb-item active">Edit Student</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-user-edit me-1"></i>
            Edit Student Details
            <a href="{{ route('students.index') }}" class="btn btn-primary btn-sm float-end">Back</a>
        </div>
        <div class="card-body">
            <form action="{{ route('students.update', Crypt::encryptString($student->id)) }}" method="POST">
                @csrf
                @method('POST')

                <div class="row g-3">
                    <!-- Student Name Field -->
                    <div class="form-group col-md-4">
                        <label for="name" class="form-label font-weight-bold">Name</label>
                        <input type="text" name="name" id="name" class="form-control"
                            placeholder="Enter student name" value="{{ old('name', $student->name) }}" required>
                        @error('name')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Student Email Field -->
                    <div class="form-group col-md-4">
                        <label for="email" class="form-label font-weight-bold">Email</label>
                        <input type="email" name="email" id="email" class="form-control"
                            placeholder="Enter student email" value="{{ old('email', $student->email) }}" required>
                        @error('email')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Student Phone Field -->
                    <div class="form-group col-md-4">
                        <label for="phone" class="form-label font-weight-bold">Phone</label>
                        <input type="text" name="phone" id="phone" class="form-control"
                            placeholder="Enter phone number" value="{{ old('phone', $student->phone) }}" required>
                        @error('phone')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="row g-3 mt-2">
                    <!-- Student Department Field -->
                    <div class="form-group col-md-4">
                        <label for="department" class="form-label font-weight-bold">Department</label>
                        <select name="department" id="department" class="form-select" required>
                            <option value="" disabled>Select Department</option>
                            <option value="Applied Psychology"
                                {{ old('department', $student->department) == 'Applied Psychology' ? 'selected' : '' }}>
                                Department of Applied Psychology
                            </option>
                            <option value="Computer Science"
                                {{ old('department', $student->department) == 'Computer Science' ? 'selected' : '' }}>
                                Department of Computer Science
                            </option>
                            <option value="B.voc(Software Development)"
                                {{ old('department', $student->department) == 'B.voc(Software Development)' ? 'selected' : '' }}>
                                Department of B.voc (Software Development)
                            </option>
                            <option value="Economics"
                                {{ old('department', $student->department) == 'Economics' ? 'selected' : '' }}>
                                Department of Economics
                            </option>
                            <option value="English"
                                {{ old('department', $student->department) == 'English' ? 'selected' : '' }}>
                                Department of English
                            </option>
                            <option value="Environmental Studies"
                                {{ old('department', $student->department) == 'Environmental Studies' ? 'selected' : '' }}>
                                Department of Environmental Studies
                            </option>
                            <option value="Commerce"
                                {{ old('department', $student->department) == 'Commerce' ? 'selected' : '' }}>
                                Department of Commerce
                            </option>
                            <option value="Punjabi"
                                {{ old('department', $student->department) == 'Punjabi' ? 'selected' : '' }}>
                                Department of Punjabi
                            </option>
                            <option value="Hindi"
                                {{ old('department', $student->department) == 'Hindi' ? 'selected' : '' }}>
                                Department of Hindi
                            </option>
                            <option value="History"
                                {{ old('department', $student->department) == 'History' ? 'selected' : '' }}>
                                Department of History
                            </option>
                            <option value="Management Studies"
                                {{ old('department', $student->department) == 'Management Studies' ? 'selected' : '' }}>
                                Department of Management Studies
                            </option>
                            <option value="Mathematics"
                                {{ old('department', $student->department) == 'Mathematics' ? 'selected' : '' }}>
                                Department of Mathematics
                            </option>
                            <option value="Philosophy"
                                {{ old('department', $student->department) == 'Philosophy' ? 'selected' : '' }}>
                                Department of Philosophy
                            </option>
                            <option value="Physical Education"
                                {{ old('department', $student->department) == 'Physical Education' ? 'selected' : '' }}>
                                Department of Physical Education
                            </option>
                            <option value="Political Science"
                                {{ old('department', $student->department) == 'Political Science' ? 'selected' : '' }}>
                                Department of Political Science
                            </option>
                            <option value="Statistics"
                                {{ old('department', $student->department) == 'Statistics' ? 'selected' : '' }}>
                                Department of Statistics
                            </option>
                            <option value="B.voc(Banking)"
                                {{ old('department', $student->department) == 'B.voc(Banking)' ? 'selected' : '' }}>
                                Department of B.voc (Banking)
                            </option>
                        </select>
                        @error('department')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Semester Field (Visible Only for Students) -->
                    @if (old('role', $student->roles->first()->name) === 'student' || $user->roles->contains('student'))
                        <div class="form-group col-md-4">
                            <label for="semester" class="form-label font-weight-bold">Semester</label>
                            <select name="semester" id="semester" class="form-select">
                                <option value="" disabled>Select Semester</option>
                                <option value="1" {{ old('semester', $student->semester) == 1 ? 'selected' : '' }}>1
                                </option>
                                <option value="2" {{ old('semester', $student->semester) == 2 ? 'selected' : '' }}>2
                                </option>
                                <option value="3" {{ old('semester', $student->semester) == 3 ? 'selected' : '' }}>3
                                </option>
                                <option value="4" {{ old('semester', $student->semester) == 4 ? 'selected' : '' }}>4
                                </option>
                                <option value="5" {{ old('semester', $student->semester) == 5 ? 'selected' : '' }}>5
                                </option>
                                <option value="6" {{ old('semester', $student->semester) == 6 ? 'selected' : '' }}>6
                                </option>
                                <option value="7" {{ old('semester', $student->semester) == 7 ? 'selected' : '' }}>7
                                </option>
                                <option value="8" {{ old('semester', $student->semester) == 8 ? 'selected' : '' }}>8
                                </option>
                            </select>
                            @error('semester')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                    @endif


                    <!-- Roll No Field -->
                    <div class="col-md-4" id="rollno-field" style="{{ $student->roles->contains('student') }}">
                        <label for="roll_no" class="form-label">Roll No<span class="text-danger">*</span></label>
                        <input type="text" name="roll_no" id="roll_no" class="form-control"
                            value="{{ old('roll_no', $student->roll_no) }}">
                        @error('roll_no')
                            <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                </div>


                <div class="row g-3 mt-2">
                    <!-- Roles Checkboxes -->
                    <div class="form-group col-md-4">
                        <label for="roles" class="form-label font-weight-bold">Roles</label>
                        <div class="d-flex flex-wrap gap-3 mt-2">
                            @if ($roles->isNotEmpty())
                                @foreach ($roles as $role)
                                    @if ($role->name !== 'SuperAdmin' && $role->name !== 'Admin')
                                        <!-- Add condition to exclude SuperAdmin and Admin -->
                                        <div class="form-check">
                                            <input type="checkbox" name="role[]" id="role-{{ $role->id }}"
                                                value="{{ $role->name }}" class="form-check-input"
                                                {{ $hasRoles->contains($role->id) ? 'checked' : '' }}>
                                            <label for="role-{{ $role->id }}"
                                                class="form-check-label">{{ $role->name }}</label>
                                        </div>
                                    @endif
                                @endforeach
                            @else
                                <p class="text-muted">No roles available.</p>
                            @endif
                        </div>
                    </div>
                </div>



                <!-- Submit Button -->
                <div class="text-end">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-1"></i> Update Student
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
