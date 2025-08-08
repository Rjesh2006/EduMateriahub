@extends('layouts.admin')

@section('title', 'Roadmap Details')

@section('content')
    <div class="page-wrapper" style="margin-top: 3rem;">
        <div class="page-content">
            <div class="card shadow-lg rounded-lg">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h3 class="h2 mb-0"><i class="fas fa-info-circle me-2"></i> Roadmap Details</h3>
                    <a href="{{ route('roadmaps.index') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Back to Roadmaps
                    </a>
                </div>
                <div class="card-body p-4">
                    <!-- Department -->
                    <div class="mb-4">
                        <h5 class="text-muted">Department</h5>
                        <p class="fs-5">{{ $roadmap->department }}</p>
                    </div>

                    <!-- Title -->
                    <div class="mb-4">
                        <h5 class="text-muted">Title</h5>
                        <p class="fs-5">{{ $roadmap->title }}</p>
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <h5 class="text-muted">Description</h5>
                        <p class="fs-5">{{ $roadmap->description }}</p>
                    </div>

                    <!-- File -->
                    <div class="mb-4">
                        <h5 class="text-muted">File</h5>
                        @if ($roadmap->file)
                            <a href="{{ asset('storage/' . $roadmap->file) }}" target="_blank" class="btn btn-info btn-sm">
                                <i class="fas fa-file-alt me-2"></i> View File
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

        .btn-light {
            background-color: #f8f9fa;
            border-color: #f8f9fa;
        }

        .btn-light:hover {
            background-color: #e2e6ea;
            border-color: #dae0e5;
        }

        .btn-info {
            color: white;
            background-color: #17a2b8;
            border-color: #17a2b8;
        }

        .btn-info:hover {
            background-color: #138496;
            border-color: #117a8b;
        }
    </style>
@endsection
