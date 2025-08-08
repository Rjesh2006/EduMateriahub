@extends('layouts.admin')

@section('title', 'View Responses')

@section('content')
    <h1 class="mt-4 text-center text-primary">Quiz Responses: <span
            class="text-dark">{{ $attempt->quiz->subject_name }}</span></h1>
    <ol class="breadcrumb mb-4 justify-content-center">
        <li class="breadcrumb-item">
            @can('is superadmin')
                <a href="{{ route('superadmin.dashboard') }}">Dashboard</a>
            @else
                <a href="{{ route('others.dashboard') }}">Dashboard</a>
            @endcan
        </li>
        <li class="breadcrumb-item"><a href="{{ route('attempts.index') }}">Quiz List</a></li>
        <li class="breadcrumb-item active">View Responses</li>
    </ol>

    <div class="card shadow-lg border-0 rounded-3">
        <div class="card-body p-5">
            <!-- Display Score -->
            <div class="text-center mb-5">
                <h1 class="display-4 fw-bold text-success">Your Score: {{ $score }}</h1>
                <p class="text-muted">Review your answers and compare them to the correct ones.</p>
            </div>

            <!-- Your Responses Section -->
            <h3 class="fw-bold text-primary text-center mb-4">Your Responses</h3>

            <div id="responsesContainer">
                @foreach ($questionsWithResponses as $question)
                    <div class="mb-4 p-4 border rounded shadow-sm bg-light">
                        <!-- Question Text -->
                        <p class="fw-bold text-dark mb-2">
                            <span class="text-primary">Q{{ $loop->iteration }}:</span> {{ $question['question_text'] }}
                        </p>

                        <!-- Options -->
                        <div class="mt-3">
                            @foreach ($question['options'] as $option)
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" disabled
                                        @if ($option === $question['selected_option']) checked @endif>
                                    <label
                                        class="form-check-label text-dark {{ $option === $question['selected_option'] ? ($question['is_correct'] ? 'text-success fw-bold' : 'text-danger fw-bold') : '' }} ">
                                        {{ $option }}
                                    </label>
                                </div>
                            @endforeach
                        </div>

                        <!-- Response Details -->
                        <div class="mt-3">
                            @if (is_null($question['selected_option']))
                                <p class="text-warning fw-bold">✘ Not Attempted</p>
                            @elseif ($question['is_correct'])
                                <p class="text-success fw-bold">✔ Your answer is correct!</p>
                            @else
                                <p class="text-danger fw-bold">✘ Your answer is incorrect.</p>
                                <p class="text-success fw-light">Correct Option: {{ $question['correct_option'] }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Back to Dashboard Button -->
            <div class="text-center mt-5">
                <a href="{{ route('attempts.index', $attempt->quiz->id) }}"
                    class="btn btn-secondary px-5 py-2 rounded-pill">
                    <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Custom Styling -->
    <style>
        .card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .form-check-label {
            font-size: 1rem;
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

        .text-warning {
            color: #ff9800;
        }
    </style>
@endsection
