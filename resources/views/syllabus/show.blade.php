@extends('layouts.admin')

@section('title', 'View Syllabus')

@section('content')
    <div class="page-wrapper" style="margin-top: 3rem;">
        <div class="page-content">
            <div class="card shadow-lg rounded-lg">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h3 class="h2 mb-0"><i class="fas fa-info-circle me-2"></i>Syllabus Details</h3>
                    <a href="{{ route('syllabus.index') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Back to Syllabus List
                    </a>
                </div>
                <div class="card-body p-4">
                    <!-- Department -->
                    <div class="mb-4">
                        <h5 class="text-muted">Department</h5>
                        <p class="fs-5">{{ $syllabus->department }}</p>
                    </div>

                    <!-- File -->
                    <div class="mb-4">
                        <h5 class="text-muted">File</h5>
                        @if ($syllabus->file)
                            <a href="{{ asset('storage/' . $syllabus->file) }}" target="_blank" class="btn btn-info btn-sm">
                                <i class="fas fa-file-alt me-1"></i> View File
                            </a>
                        @else
                            <p class="text-danger">No file available.</p>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>

    <style>
        /* Card Styling */
        .card-header {
            font-size: 1.25rem;
            font-weight: bold;
            border-bottom: 2px solid #ddd;
        }

        .text-muted {
            font-weight: 600;
        }

        .fs-5 {
            font-size: 1.1rem;
        }

        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #545b62;
        }
    </style>
@endsection
