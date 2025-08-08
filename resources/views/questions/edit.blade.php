@extends('layouts.admin')

@section('title', 'Edit Question')

@section('content')
    <h1 class="mt-4">Edit Question</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('quizzes.index') }}">Quizzes</a></li>
        <li class="breadcrumb-item active">Edit Question</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-edit me-1"></i>
            Update Question Details
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('questions.update', ['quizId' => $quiz->id, 'id' => $question->id]) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="question_text" class="form-label">Question</label>
                    <input type="text" name="question_text" class="form-control" id="question_text"
                        value="{{ old('question_text', $question->question_text) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">How many options do you want to create?</label>
                    <div>
                        <input type="radio" name="option_count" value="1"
                            {{ count($question->options) == 1 ? 'checked' : '' }}> Only one option<br>
                        <input type="radio" name="option_count" value="2"
                            {{ count($question->options) == 2 ? 'checked' : '' }}> Two options<br>
                        <input type="radio" name="option_count" value="3"
                            {{ count($question->options) == 3 ? 'checked' : '' }}> Three options<br>
                        <input type="radio" name="option_count" value="4"
                            {{ count($question->options) == 4 ? 'checked' : '' }}> Four options<br>
                        <input type="radio" name="option_count" value="5"
                            {{ count($question->options) > 4 ? 'checked' : '' }}> Others<br>
                    </div>
                </div>

                <div id="optionsContainer">
                    @foreach ($question->options as $index => $option)
                        <div class="mb-3">
                            <label class="form-label">Option {{ $index + 1 }}</label>
                            <input type="text" name="options[{{ $index }}]" class="form-control"
                                value="{{ $option }}" required>
                        </div>
                    @endforeach
                </div>

                <div class="mb-3">
                    <label for="correct_option" class="form-label">Correct Option</label>
                    <select name="correct_option" id="correct_option" class="form-select" required>
                        @foreach ($question->options as $index => $option)
                            <option value="{{ $index + 1 }}"
                                {{ $question->correct_option == $index + 1 ? 'selected' : '' }}>Option {{ $index + 1 }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Update Question</button>
                <a href="{{ route('questions.index', $quiz->id) }}" class="btn btn-danger">Cancel</a>
            </form>
        </div>
    </div>

    <script>
        document.querySelectorAll('input[name="option_count"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const count = parseInt(this.value);
                const optionsContainer = document.getElementById('optionsContainer');
                const correctOptionSelect = document.getElementById('correct_option');

                optionsContainer.innerHTML = '';
                correctOptionSelect.innerHTML = '';

                for (let i = 0; i < count; i++) {
                    const input = document.createElement('input');
                    input.type = 'text';
                    input.name = `options[${i}]`;
                    input.className = 'form-control mb-3';
                    input.placeholder = `Option ${i + 1}`;
                    optionsContainer.appendChild(input);

                    const option = document.createElement('option');
                    option.value = i + 1;
                    option.textContent = `Option ${i + 1}`;
                    correctOptionSelect.appendChild(option);
                }
            });
        });
    </script>
@endsection
