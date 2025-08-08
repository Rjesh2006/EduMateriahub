@extends('layouts.admin')

@section('title', 'Quiz Results')

@section('content')
    <h1 class="mt-4">Quiz Results: <span class="text-primary">{{ $quiz->subject_name }}</span></h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item">
            @can('is superadmin')
                <a href="{{ route('superadmin.dashboard') }}">Dashboard</a>
            @else
                <a href="{{ route('others.dashboard') }}">Dashboard</a>
            @endcan
        </li>
        <li class="breadcrumb-item"><a href="{{ route('attempts.index') }}">Tests List Page</a></li>
        <li class="breadcrumb-item active">Quiz Results</li>
    </ol>

    <div class="card shadow-lg border-0">
        <div class="card-body">
            <div class="text-center mb-4">
                <h3 class="fw-bold text-primary">Test Completed!</h3>
                <p class="text-muted">Here are your performance details:</p>
            </div>

            <!-- Student Details -->
            <div class="border rounded p-4 mb-4 bg-light">
                <h5 class="fw-bold text-secondary">Student Information</h5>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <p><strong>Student Name:</strong> {{ $attempt->student->name }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Roll Number:</strong> {{ $attempt->roll_no }}</p>
                    </div>
                </div>
            </div>

            <!-- Quiz Performance -->
            <div class="border rounded p-4 mb-4 bg-white">
                <h5 class="fw-bold text-secondary">Performance Summary</h5>
                <div class="row mt-3">
                    <div class="col-md-4">
                        <p><strong>Total Questions:</strong> {{ $totalQuestions }}</p>
                    </div>
                    <div class="col-md-4">
                        <p><strong>Correct Answers:</strong> {{ $correctAnswersCount }} / {{ $totalQuestions }}</p>
                    </div>
                    <div class="col-md-4">
                        <p><strong>Score:</strong> <span class="badge bg-success fs-6">{{ $score }}</span></p>
                    </div>
                </div>
            </div>

            <!-- Score Section -->
            <div class="text-center my-4">
                <h1 class="display-4 fw-bold text-success">Your Final Score: {{ $score }}</h1>
            </div>

            <!-- Buttons Section -->
            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('attempts.responses', ['attemptId' => $attempt->id]) }}" class="btn btn-primary">
                    <i class="fas fa-eye me-2"></i>View Responses
                </a>
                <a href="{{ route('attempts.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                </a>
            </div>
        </div>
    </div>
@endsection
