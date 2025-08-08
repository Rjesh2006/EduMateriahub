@extends('layouts.admin')

@section('title', 'Start Quiz')

@section('content')
    <h1 class="mt-4 text-center text-primary">Start Quiz: <span class="text-dark">{{ $quiz->subject_name }}</span></h1>
    <ol class="breadcrumb mb-4 justify-content-center">
    </ol>

    <div class="card shadow-lg border-0 rounded-3">
        <div class="card-body p-5">
            <!-- Timer Section -->
            <div class="mb-4 text-center">
                <h2 class="fw-bold text-danger">Time Left: <span id="timer"></span></h2>
                <p class="text-muted">Make sure to answer all the questions before time runs out.</p>
            </div>

            <!-- Questions container -->
            <form id="quizForm" action="{{ route('attempts.submitTest', $quiz->id) }}" method="POST">
                @csrf
                <div id="questionsContainer">
                    @foreach ($questions as $question)
                        <div class="mb-5 p-4 border rounded shadow-sm bg-light">
                            <p class="fw-bold text-dark">
                                <span class="text-primary">Q{{ $loop->iteration }}:</span>
                                {{ $question->question_text }}
                            </p>
                            <div class="mt-3">
                                @foreach (json_decode($question->options) as $option)
                                    <div class="form-check">
                                        <input type="radio" class="form-check-input" name="answers[{{ $question->id }}]"
                                            value="{{ $option }}"
                                            id="option{{ $loop->parent->iteration }}{{ $loop->iteration }}">
                                        <label for="option{{ $loop->parent->iteration }}{{ $loop->iteration }}"
                                            class="form-check-label text-dark">
                                            {{ $option }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-5 text-center">
                    <button type="submit" class="btn btn-success btn-lg px-5" id="submitBtn">
                        <i class="fas fa-check-circle me-2"></i>Submit Test
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Custom Styles -->
    <style>
        body {
            background-color: #f9f9f9;
        }

        .card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .breadcrumb {
            background: transparent;
            font-size: 0.95rem;
        }

        .breadcrumb a {
            color: #007bff;
            text-decoration: none;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        .form-check-label {
            font-size: 1rem;
        }

        #timer {
            /* font-size: 1.5rem; */
            font-weight: bold;
        }

        #questionsContainer {
            max-width: 800px;
            margin: 0 auto;
        }

        .btn-success {
            border-radius: 30px;
        }

        @media (max-width: 768px) {
            h1 {
                font-size: 1.5rem;
            }

            #timer {
                font-size: 1.25rem;
            }
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Prevent duplicate submissions
        let formSubmitted = false;

        // Function to submit the form due to illegal activity
        function submitDueToIllegalActivity() {
            if (formSubmitted) return; // Prevent duplicate submissions
            formSubmitted = true;

            Swal.fire({
                title: "Illegal Activity Detected!",
                text: "You have switched tabs or minimized the window. Your test is being auto-submitted.",
                icon: "warning",
                showConfirmButton: false,
                allowOutsideClick: false,
                timer: 1000
            }).then(() => {
                document.getElementById('quizForm').submit(); // Submit the quiz form
            });
        }

        // Function to detect illegal activities
        function detectIllegalActivity() {
            // Detect when the window loses focus
            window.addEventListener('blur', () => {
                if (!formSubmitted) submitDueToIllegalActivity();
            });

            // Detect when the page visibility changes (tab switch or minimize)
            document.addEventListener('visibilitychange', () => {
                if (document.hidden && !formSubmitted) {
                    submitDueToIllegalActivity();
                }
            });

            // Function to handle window resize (detect restore button click)
            window.addEventListener('resize', () => {
                if (window.innerWidth < screen.width || window.innerHeight < screen.height) {
                    if (!formSubmitted) submitDueToIllegalActivity();
                }
            });
        }

        // Start monitoring illegal activity
        detectIllegalActivity();

        // Timer logic
        const endTime = new Date('{{ \Carbon\Carbon::parse($quiz->end_time)->toDateTimeString() }}').getTime();
        const timerElement = document.getElementById('timer');
        const form = document.getElementById('quizForm');

        function updateTimer() {
            const now = new Date().getTime();
            const timeLeft = endTime - now;

            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                if (!formSubmitted) {
                    formSubmitted = true;
                    Swal.fire({
                        title: "Time's up!",
                        text: "The quiz is being auto-submitted.",
                        icon: "info",
                        showConfirmButton: false,
                        timer: 2000
                    }).then(() => {
                        form.submit();
                    });
                }
                return;
            }

            const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);
            timerElement.textContent = `${minutes}m ${seconds}s`;
        }

        // Start the timer
        const timerInterval = setInterval(updateTimer, 1000);
        updateTimer();

        // Prevent duplicate form submissions
        document.getElementById('submitBtn').addEventListener('click', (e) => {
            if (formSubmitted) {
                e.preventDefault();
                return false;
            }
            formSubmitted = true;
        });

        // SweetAlert for already submitted quiz
        @if (session('alert'))
            Swal.fire({
                title: 'Test Already Submitted!',
                text: "{{ session('message') }}",
                icon: 'warning',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = "{{ session('redirect') }}";
            });
        @endif
    </script>
@endsection
