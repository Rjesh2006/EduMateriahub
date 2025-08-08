@extends('layouts.admin')

@section('title', 'Attempt Quiz')

@section('content')
    <div class="page-wrapper" style="">
        <div class="page-content">
            <!-- Page Header -->
            <div class="mb-4">
                <h1 class="h1">Attempt Quiz</h1>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        @can('is superadmin')
                            <a href="{{ route('superadmin.dashboard') }}">Dashboard</a>
                        @else
                            <a href="{{ route('others.dashboard') }}">Dashboard</a>
                        @endcan
                    </li>
                    <li class="breadcrumb-item active">Attempt Quiz</li>
                </ol>
            </div>

            <!-- Quiz Table Card -->
            <div class="card shadow-lg rounded-lg">
                <div class="card-header bg-primary text-white rounded-top">
                    <i class="fas fa-question-circle me-2"></i>
                    Quiz Information
                    <a href="{{ route('attempts.elective') }}" class="btn btn-light btn-sm float-end me-2">
                        Attempt Elective Test
                    </a>
                    
                </div>
                <div class="card-body p-4">
                    <table class="table table-striped table-hover table-responsive">
                        <thead class="table-primary">
                            <tr>
                                <th>#</th>
                                <th>Subject Type</th>
                                <th>Department</th>
                                <th>Semester</th>
                                <th>Subject Name</th>
                                <th>Faculty Name</th>
                                <th>Date</th>
                                <th>Start Time</th>
                                <th>End Time</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($quizzes->isEmpty())
                                <tr>
                                    <td colspan="10" class="text-center text-muted">No active quizzes available.</td>
                                </tr>
                            @else
                                @foreach ($quizzes as $index => $quiz)
                                    @php
                                        $currentDateTime = now();
                                        $quizStartDateTime = \Carbon\Carbon::parse(
                                            $quiz->date . ' ' . $quiz->start_time,
                                        );
                                        $quizEndDateTime = \Carbon\Carbon::parse($quiz->date . ' ' . $quiz->end_time);
                                        $attempt = $quiz
                                            ->attemptDetails()
                                            ->where('student_id', auth()->id())
                                            ->first();
                                    @endphp
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $quiz->subject_type }}</td>
                                        <td>{{ $quiz->department }}</td>
                                        <td>{{ $quiz->semester }}</td>
                                        <td>{{ $quiz->subject_name }}</td>
                                        <td>{{ $quiz->faculty_name }}</td>
                                        <td>{{ $quiz->date }}</td>
                                        <td>{{ $quiz->start_time }}</td>
                                        <td>{{ $quiz->end_time }}</td>
                                        <td>
                                            @if ($attempt && $attempt->status == 1)
                                                <a href="{{ route('attempts.results', ['quizId' => $quiz->id]) }}"
                                                    class="btn btn-success btn-sm">
                                                    View Result
                                                </a>
                                            @elseif ($currentDateTime->lt($quizStartDateTime))
                                                <span id="timer-{{ $quiz->id }}" class="text-danger fw-bold"></span>
                                                <button id="start-btn-{{ $quiz->id }}" class="btn btn-primary btn-sm"
                                                    disabled>Start Quiz</button>
                                                <script>
                                                    const startTime{{ $quiz->id }} = new Date("{{ $quizStartDateTime }}").getTime();
                                                    const timer{{ $quiz->id }} = setInterval(() => {
                                                        const now = new Date().getTime();
                                                        const distance = startTime{{ $quiz->id }} - now;
                                                        if (distance <= 0) {
                                                            clearInterval(timer{{ $quiz->id }});
                                                            document.getElementById("timer-{{ $quiz->id }}").innerText = "Quiz is live!";
                                                            const startBtn = document.getElementById("start-btn-{{ $quiz->id }}");
                                                            startBtn.removeAttribute('disabled');
                                                            startBtn.onclick = function() {
                                                                window.location.href = "{{ route('attempts.create', $quiz->id) }}";
                                                            };
                                                        } else {
                                                            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                                            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                                                            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                                                            document.getElementById("timer-{{ $quiz->id }}").innerText =
                                                                `Starts in: ${hours}h ${minutes}m ${seconds}s`;
                                                        }
                                                    }, 1000);
                                                </script>
                                            @elseif ($currentDateTime->gt($quizEndDateTime))
                                                <a href="{{ route('attempts.results', ['quizId' => $quiz->id]) }}"
                                                    class="btn btn-info btn-sm">
                                                    View Result
                                                </a>
                                            @else
                                                @if (!$attempt || $attempt->status == 0)
                                                    <a href="{{ route('attempts.create', $quiz->id) }}"
                                                        class="btn btn-primary btn-sm">Start Quiz</a>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Include SweetAlert CSS and JS -->
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
            };
        </script>
    @endif

    <style>
        .card-header {
            font-size: 1.25rem;
            font-weight: bold;
        }


        table thead {
            font-size: 1rem;
            font-weight: 600;
        }

        table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .btn {
            font-size: 0.875rem;
        }

        .btn-sm {
            padding: 0.375rem 0.75rem;
            border-radius: 0.2rem;
        }

        .page-content {
            padding: 20px;
        }
    </style>
@endsection
