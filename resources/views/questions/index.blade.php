@extends('layouts.admin')

@section('title', 'Questions List')

@section('content')
    <div class="container mt-4">
        <h1 class="text-center text-primary">Questions List</h1>
        <ol class="breadcrumb mb-4 bg-light p-3 rounded">
            <li class="breadcrumb-item">
                @can('is superadmin')
                    <a href="{{ route('superadmin.dashboard') }}" class="text-decoration-none">Dashboard</a>
                @else
                    <a href="{{ route('others.dashboard') }}" class="text-decoration-none">Dashboard</a>
                @endcan
            </li>
            <li class="breadcrumb-item"><a href="{{ route('quizzes.index') }}" class="text-decoration-none">Quizzes</a></li>
            <li class="breadcrumb-item active">Questions List</li>
        </ol>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <span>Questions for Quiz: <strong>{{ $quiz->subject_name }}</strong></span>
                <div>
                    <a href="{{ route('questions.create', $quiz->id) }}" class="btn btn-light btn-sm">Add Question</a>
                    <a href="{{ route('quizzes.index') }}" class="btn btn-secondary btn-sm">Add Questions Later</a>
                </div>
            </div>
            <div class="card-body">
                @if ($questions->isEmpty())
                    <div class="alert alert-info text-center">
                        No questions available. Please add a new question.
                    </div>
                @else
                    <table id="datatablesSimple" class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Sl No.</th>
                                <th>Question</th>
                                <th>Options</th>
                                <th>Correct Option</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($questions as $index => $question)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $question->question_text }}</td>
                                    <td>
                                        <ul class="list-unstyled">
                                            @foreach (json_decode($question->options, true) as $value)
                                                <li>- {{ $value }}</li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td><span class="badge bg-success">{{ $question->correct_option }}</span></td>
                                    <td>
                                        <a href="{{ route('questions.edit', ['quizId' => $quiz->id, 'id' => $question->id]) }}"
                                            class="btn btn-warning btn-sm">Edit</a>
                                        <form action="{{ route('questions.destroy', $question->id) }}" method="POST"
                                            class="d-inline" id="deleteForm_{{ $question->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-danger btn-sm delete-btn"
                                                data-id="{{ $question->id }}">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="mt-4">
                        <div class="form-check">
                            <input type="checkbox" id="confirmFinalize" class="form-check-input">
                            <label for="confirmFinalize" class="form-check-label text-danger">
                                I confirm that all questions are correct, finalized, and the instructions have been added.
                                Once the final submit button gets clicked the questions will be non-editable.
                            </label>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <form method="POST" action="{{ route('questions.submit', $quiz->id) }}">
                                @csrf
                                <button id="finalSubmit" class="btn btn-success" disabled>Final Submit</button>
                            </form>
                            <button id="toggleInstructionForm" class="btn btn-info">Add Instructions</button>
                        </div>
                    </div>

                    <div id="instructionForm" class="mt-4 bg-light p-4 rounded" style="display: none;">
                        <form method="POST" action="{{ route('quizzes.storeInstructions', $quiz->id) }}">
                            @csrf
                            <div class="mb-3">
                                <label for="total_no_of_question" class="form-label">Total Number of Questions</label>
                                <input type="number" class="form-control" id="total_no_of_question"
                                    name="total_no_of_question" required>
                            </div>
                            <div class="mb-3">
                                <label for="attempt_no" class="form-label">How many questions attempt</label>
                                <input type="number" class="form-control" id="attempt_no" name="attempt_no" required>
                            </div>
                            <div class="mb-3">
                                <label for="weightage" class="form-label">Per question Weightage</label>
                                <input type="text" class="form-control" id="weightage" name="weightage" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Submit Instructions</button>
                        </form>
                    </div>
                @endif
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            document.getElementById('toggleInstructionForm').addEventListener('click', function() {
                const instructionForm = document.getElementById('instructionForm');
                instructionForm.style.display = instructionForm.style.display === 'none' ? 'block' : 'none';
            });

            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const questionId = this.dataset.id;
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'No, keep it'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById('deleteForm_' + questionId).submit();
                        }
                    });
                });
            });

            document.getElementById('confirmFinalize')?.addEventListener('change', function() {
                const finalSubmitButton = document.getElementById('finalSubmit');
                finalSubmitButton.disabled = !this.checked;
            });

            @if (session('success'))
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
            @endif

            document.getElementById('finalSubmit')?.addEventListener('click', function(event) {
                event.preventDefault();
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Once you finalize, questions will be non-editable!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Final Submit!',
                    cancelButtonText: 'No, Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.closest('form').submit();
                    }
                });
            });
        </script>
    </div>
@endsection
