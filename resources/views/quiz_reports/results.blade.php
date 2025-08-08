@extends('layouts.admin')

@section('title', 'Quiz Results')

@section('content')
    <h1 class="mt-4 text-center text-primary fw-bold">📊 Quiz Results</h1>

    <ol class="breadcrumb mb-4 p-3 rounded shadow-sm text-white"
        style="background: linear-gradient(45deg, #87a1bd, #b7a1d9);">
        <li class="breadcrumb-item">
            @can('is superadmin')
                <a href="{{ route('superadmin.dashboard') }}" class="text-black text-decoration-none fw-bold">🏠 Dashboard</a>
            @else
                <a href="{{ route('others.dashboard') }}" class="text-black text-decoration-none fw-bold">🏠 Dashboard</a>
            @endcan
        </li>
        <li class="breadcrumb-item active text-black fw-bold">Quiz Results</li>
    </ol>

    <!-- Export Buttons -->
    <div class="d-flex justify-content-start mb-4 gap-2">
        {{-- <a href="{{ route('quiz_reports.export.excel', $quiz_id) }}" class="btn btn-success shadow-sm fw-bold">📊 Export to Excel</a> --}}
        {{-- <a href="{{ route('quiz_reports.export.word', $quiz_id) }}" class="btn btn-primary shadow-sm fw-bold">📝 Export to Word</a> --}}
        <a href="{{ route('quiz_reports.export.pdf', $quiz_id) }}" class="btn btn-danger shadow-sm fw-bold">📄 Export to
            PDF</a>
    </div>

    <div class="card shadow-sm border-0 rounded-lg mb-4">
        <div class="card-body bg-light p-4 rounded">
            <table class="table table-hover table-bordered text-center shadow-sm rounded">
                <thead class="table-dark">
                    <tr>
                        <th>🔢 S.N.</th>
                        <th>👤 Name</th>
                        <th>🆔 Roll No</th>
                        <th>📅 Semester</th>
                        <th>🏛 Department</th>
                        <th>🎯 Marks</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($studentsResults as $index => $result)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $result['name'] }}</td>
                            <td>{{ $result['roll_no'] }}</td>
                            <td>{{ $result['semester'] }}</td>
                            <td>{{ $result['department'] }}</td>
                            <td class="fw-bold text-success">{{ $result['marks'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="text-start">
        <a href="{{ route('quiz_reports.index') }}" class="btn btn-secondary shadow-sm fw-bold">⬅️ Back</a>
    </div>
@endsection
