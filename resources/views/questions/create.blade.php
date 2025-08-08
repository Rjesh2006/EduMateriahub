@extends('layouts.admin')

@section('title', 'Create Questions')

@section('content')
    <h1 class="mt-4">Create Questions</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('quizzes.index') }}">Quizzes</a></li>
        <li class="breadcrumb-item active">Create Questions</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-plus-circle me-1"></i>
            Add New Questions
        </div>
        <div class="card-body">
            <div id="questionsContainer"></div>

            <button type="button" class="btn btn-secondary" id="addNextQuestion">Add Next Question</button>
            <button type="button" class="btn btn-primary" id="submitQuestions">Submit All Questions</button>
            <a href="{{ route('questions.index', $quiz->id) }}" class="btn btn-danger">Cancel</a>
        </div>
    </div>

    <script>
        const questionsContainer = document.getElementById('questionsContainer');
        const addNextQuestionButton = document.getElementById('addNextQuestion');
        let questionIndex = 0;
        let removedQuestions = [];

        function createQuestionForm(index, showCancel = false) {
            const questionDiv = document.createElement('div');
            questionDiv.className = 'card p-3 mb-3';
            questionDiv.id = `questionForm_${index}`;

            questionDiv.innerHTML = `
                <form>
                    <h5>Question ${index + 1}</h5>
                    <div class="mb-3">
                        <label for="question_text_${index}" class="form-label">Write Your Question</label>
                        <input type="text" name="question_text" class="form-control" id="question_text_${index}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">How many options do you want to create?</label>
                        <div>
                            <input type="radio" name="option_count_${index}" value="1"> Only one option<br>
                            <input type="radio" name="option_count_${index}" value="2"> Two options<br>
                            <input type="radio" name="option_count_${index}" value="3"> Three options<br>
                            <input type="radio" name="option_count_${index}" value="4"> Four options<br>
                            <input type="radio" name="option_count_${index}" value="5"> Others<br>
                        </div>
                    </div>

                    <div id="optionsContainer_${index}"></div>

                    <div class="mb-3" id="correctOptionContainer_${index}" style="display: none;">
                        <label for="correct_option_${index}" class="form-label">Choose the Correct Option</label>
                        <select name="correct_option" id="correct_option_${index}" class="form-select" required></select>
                    </div>

                    <button type="button" class="btn btn-danger" id="cancelButton_${index}" onclick="removeQuestion(${index})" style="${showCancel ? 'display: block;' : 'display: none;'}">Cancel</button>
                </form>
            `;

            questionDiv.querySelectorAll(`input[name="option_count_${index}"]`).forEach(radio => {
                radio.addEventListener('change', function() {
                    handleOptionCountChange(index, this.value);
                });
            });

            questionsContainer.appendChild(questionDiv);
        }

        function handleOptionCountChange(index, value) {
            const optionsContainer = document.getElementById(`optionsContainer_${index}`);
            const correctOptionContainer = document.getElementById(`correctOptionContainer_${index}`);
            const correctOptionSelect = document.getElementById(`correct_option_${index}`);

            optionsContainer.innerHTML = '';
            correctOptionSelect.innerHTML = '';

            if (value === '5') {
                const inputField = document.createElement('input');
                inputField.type = 'number';
                inputField.className = 'form-control mb-3';
                inputField.placeholder = 'Enter number of options';

                inputField.addEventListener('input', function() {
                    generateOptions(index, parseInt(this.value));
                });

                optionsContainer.appendChild(inputField);
            } else {
                generateOptions(index, parseInt(value));
            }
        }

        function generateOptions(index, count) {
            const optionsContainer = document.getElementById(`optionsContainer_${index}`);
            const correctOptionSelect = document.getElementById(`correct_option_${index}`);

            optionsContainer.innerHTML = '';
            correctOptionSelect.innerHTML = '';

            for (let i = 1; i <= count; i++) {
                const optionInput = document.createElement('input');
                optionInput.type = 'text';
                optionInput.name = `options_${index}_${i}`;
                optionInput.className = 'form-control mb-3';
                optionInput.placeholder = `Option ${i}`;
                optionsContainer.appendChild(optionInput);

                const option = document.createElement('option');
                option.value = i;
                option.textContent = `Option ${i}`;
                correctOptionSelect.appendChild(option);
            }

            document.getElementById(`correctOptionContainer_${index}`).style.display = count > 0 ? 'block' : 'none';
        }

        function removeQuestion(index) {
            const questionDiv = document.getElementById(`questionForm_${index}`);
            if (questionDiv) {
                questionsContainer.removeChild(questionDiv);
                removedQuestions.push(index);
            }
        }

        addNextQuestionButton.addEventListener('click', function() {
            if (removedQuestions.length > 0) {
                questionIndex = removedQuestions.shift();
            }
            createQuestionForm(questionIndex, true);
            questionIndex++;
        });

        document.getElementById('submitQuestions').addEventListener('click', async function() {
            const allQuestions = [];

            for (let i = 0; i < questionIndex; i++) {
                const questionDiv = document.getElementById(`questionForm_${i}`);
                if (questionDiv) {
                    const questionText = questionDiv.querySelector(`#question_text_${i}`).value;
                    const options = Array.from(questionDiv.querySelectorAll(`[name^="options_${i}"]`)).map(
                        input => input.value);
                    const correctOption = questionDiv.querySelector(`#correct_option_${i}`)?.value;

                    if (!questionText || options.length === 0 || !correctOption) {
                        return;
                    }

                    allQuestions.push({
                        question_text: questionText,
                        options: options,
                        correct_option: parseInt(correctOption),
                    });
                }
            }

            try {
                const response = await fetch("{{ route('questions.store', $quiz->id) }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        quiz_id: "{{ $quiz->id }}",
                        questions: allQuestions
                    })
                });

                if (response.ok) {
                    window.location.href = "{{ route('questions.index', $quiz->id) }}";
                } else {
                    // No alert or notification shown
                    window.location.href = "{{ route('questions.index', $quiz->id) }}";
                }
            } catch (error) {
                // No alert or notification shown
                window.location.href = "{{ route('questions.index', $quiz->id) }}";
            }
        });

        createQuestionForm(questionIndex);
        questionIndex++;
    </script>
@endsection
