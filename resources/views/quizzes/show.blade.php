@extends('layouts.admin')

@section('title', 'Quiz Details')

@section('content')
    <h1 class="mt-4">Quiz Details</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('quizzes.index') }}">Quizzes</a></li>
        <li class="breadcrumb-item active">Quiz Details</li>
    </ol>

    <!-- Quiz Details Card -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-info-circle me-1"></i>
            Quiz Information
            <div class="d-flex justify-content-end">


                <!-- Check if questions are finalized and quiz is not started yet -->
                @if ($quiz->questions && $quiz->questions->where('is_submitted', 1)->count() > 0 && $quiz->status != 1)
                    <!-- If questions are finalized and quiz has not started, show Start Test button -->
                    <form id="startTestForm" action="{{ route('quizzes.startTest') }}" method="POST"
                        style="display:inline;">
                        @csrf
                        <input type="hidden" name="quiz_id" value="{{ $quiz->id }}">
                        <button type="submit" class="btn btn-success btn-sm me-2">Start Test</button>
                    </form>
                @endif

                <a href="{{ route('quizzes.index') }}" class="btn btn-secondary btn-sm me-2">Back to Quizzes</a>
            </div>
        </div>
        <div class="card shadow-lg border-0">
            <div class="card shadow-lg border-0 p-4">
                <div class="card-body">
                    <!-- Header Section -->
                    <div class="text-center mb-5">
                        <img src="/assets/image/Ramanujan_College_Logo.jpg" alt="Logo" class="img-fluid mb-3"
                            style="height: 100px;">
                        <h3 class="fw-bold text-primary">रामानुजन महाविद्यालय</h3>
                        <h4 class="text-secondary">दिल्ली विश्वविद्यालय</h4>
                    </div>

                    <!-- Quiz Details -->
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <strong>📚 Subject Type:</strong>
                            <span class="text-muted">{{ $quiz->subject_type }}</span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>🏛 Department:</strong>
                            <span class="text-muted">{{ $quiz->department }}</span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>🎓 Semester:</strong>
                            <span class="text-muted">{{ $quiz->semester }}</span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>📖 Subject Name:</strong>
                            <span class="text-muted">{{ $quiz->subject_name }}</span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>👨‍🏫 Faculty Name:</strong>
                            <span class="text-muted">{{ $quiz->faculty_name }}</span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>📅 Date:</strong>
                            <span class="text-muted">{{ $quiz->date }}</span>
                        </div>
                    </div>

                    <!-- Time Details -->
                    <div class="mb-4">
                        <strong>🕒 Start Time - End Time:</strong>
                        <span class="text-muted">
                            {{ \Carbon\Carbon::parse($quiz->start_time)->format('h:i A') }} -
                            {{ \Carbon\Carbon::parse($quiz->end_time)->format('h:i A') }}
                        </span>
                    </div>

                    <!-- PHP Logic for Total Time -->
                    @php
                        $startTime = \Carbon\Carbon::parse($quiz->start_time);
                        $endTime = \Carbon\Carbon::parse($quiz->end_time);
                        $totalMinutes = $startTime->diffInMinutes($endTime);
                    @endphp

                    <!-- Instructions Section -->
                    <div class="p-4 bg-light rounded shadow-sm">
                        <h5 class="fw-bold text-dark mb-4"><u>📋 Instructions for Candidates</u></h5>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item "><b>1.</b> If you switch the browser tab during the test, the test
                                will be automatically submitted, and you will not be allowed to retake it.</li>
                            <li class="list-group-item"><b>2.</b> Total number of questions:
                                <b>{{ $quiz->total_no_of_question }}</b>
                            </li>
                            <li class="list-group-item"><b>3.</b> You must attempt a total of
                                <b>{{ $quiz->attempt_no }}</b> questions within <b>{{ $totalMinutes }}</b> minutes.
                            </li>
                            <li class="list-group-item"><b>4.</b> Each question carries <b>{{ $quiz->weightage }}</b>
                                marks.</li>
                        </ul>
                    </div>
                </div>
            </div>


            <!-- Questions List Section -->
            <div class="card shadow-lg border-0 p-4">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-list me-1"></i> <strong>Questions List</strong>
                </div>
                <div class="card-body">
                    @if ($questions->isEmpty())
                        <div class="alert alert-info text-center">
                            No questions available for this quiz.
                        </div>
                    @else
                        <div class="questions-container">
                            @foreach ($questions as $index => $question)
                                <div class="question-item mb-4 p-3 border rounded shadow-sm">
                                    <h5><strong>Question {{ $index + 1 }}:</strong> {{ $question->question_text }}</h5>

                                    @php
                                        $options = json_decode($question->options, true);
                                    @endphp

                                    <div class="options-list mt-3">
                                        <strong>Options:</strong>
                                        <ul class="list-group">
                                            @foreach ($options as $key => $value)
                                                <li class="list-group-item">
                                                    <span class="me-2">{{ chr(65 + $key) }}.</span>{{ $value }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>

                                    <div class="correct-option mt-3">
                                        <strong>Correct Option:</strong>
                                        <p class="text-success">{{ $question->correct_option }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

        </div>

        <!-- SweetAlert JS -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            // Handle form submission with SweetAlert confirmation
            document.getElementById('startTestForm').addEventListener('submit', function(event) {
                event.preventDefault(); // Prevent default form submission

                Swal.fire({
                    title: 'Are you sure you want to start the test?',
                    text: "Once started, you can't end test from your side. You can only edit date and timing of the test",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Start Test!',
                }).then((result) => {
                    if (result.isConfirmed) {
                        // If confirmed, submit the form
                        this.submit();
                    }
                });
            });
        </script>
    @endsection
