@extends('layouts.admin')

@section('title', 'Quiz Details')

@section('content')
    <h1 class="mt-4">Quiz Details</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="#">Attempt Quiz</a></li>
        <li class="breadcrumb-item active">Quiz Details</li>
    </ol>

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

                {{-- Student Details --}}
                <hr>
                <div class="" style="font-size: 1.25rem; font-weight: bolder; color: blue;">Student Details</div>
                <hr>
                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <strong>👤 Student Name:</strong>
                        <span class="text-muted">{{ $studentDetails['name'] }}</span>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>⌗ Roll Number:</strong>
                        <span class="text-muted">{{ $studentDetails['roll_no'] }}</span>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>🏛 Department:</strong>
                        <span class="text-muted">{{ $studentDetails['department'] }}</span>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>🎓 Semester:</strong>
                        <span class="text-muted">{{ $studentDetails['semester'] }}</span>
                    </div>
                </div>

                <!-- Quiz Details -->
                <hr>
                <div class="" style="font-size: 1.25rem; font-weight: bolder; color: blue;">Quiz Details</div>
                <hr>
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
                        <li class="list-group-item"><b>1.</b> If you switch the browser tab during the test, the test
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
    </div>
    <br>

    <!-- Back to Dashboard or Start Test Button -->
    <div class="d-flex justify-content-end">
        
        <a href="{{ route('start.test', ['quizId' => $quiz->id, 'questionId' => $quiz->questions->first()->id]) }}"
            class="btn btn-success btn-sm">Start Test</a>
    </div>
    <br>
@endsection
