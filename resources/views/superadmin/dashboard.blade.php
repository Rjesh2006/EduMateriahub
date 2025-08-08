@extends('layouts.admin')

@section('content')
    <h1 class="mt-4 font-weight-bold mb-4 ">SuperAdmin Dashboard</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">
            <a href="{{ route('superadmin.dashboard') }}" style="text-decoration: none; margin-left: 1px;">
                <i class="fa fa-home" style="margin-left: 1px;"></i> Dashboard
            </a>
        </li>
    </ol>

    <div class="row">
        <!-- Total Users Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow-lg bg-gradient-primary text-white hover-shadow p-4 border-0 h-100">
                <div class="card-body">
                    <h5 class="text-uppercase font-weight-bold">Total Roadmaps</h5>
                    <div class="mt-2" style="font-size: 3rem; font-weight: bold; text-align: center;">
                        {{ $totalRoadmaps }}
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link font-weight-bold" href="{{ route('roadmaps.index') }}">View
                        All</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>

        <!-- Total Quizzes Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow-lg bg-gradient-warning text-white hover-shadow p-4 border-0 h-100">
                <div class="card-body">
                    <h5 class="text-uppercase font-weight-bold">Total Quiz Created</h5>
                    <div class="mt-2" style="font-size: 3rem; font-weight: bold; text-align: center;">
                        {{ $totalQuizzes }}
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link font-weight-bold" href="{{ route('quizzes.index') }}">View
                        All</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>

        <!-- Total PYQ Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow-lg bg-gradient-success text-white hover-shadow p-4 border-0 h-100">
                <div class="card-body">
                    <h5 class="text-uppercase font-weight-bold">Total Upload PYQ</h5>
                    <div class="mt-2" style="font-size: 3rem; font-weight: bold; text-align: center;">
                        {{ $totalPYQs }}
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link font-weight-bold" href="{{ route('pyq.index') }}">View All</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>

        <!-- Total Study Materials Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow-lg bg-gradient-danger text-white hover-shadow p-4 border-0">
                <div class="card-body">
                    <h5 class="text-uppercase font-weight-bold">No. of Study Materials</h5>
                    <div class="mt-2" style="font-size: 3rem; font-weight: bold; text-align: center;">
                        {{ $totalStudyMaterials }}
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link font-weight-bold"
                        href="{{ route('study_materials.index') }}">View All</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Distribution Graph -->
    <div class="row" style="margin-top: 40px; margin-bottom: 40px;">
        <div class="col-xl-6 col-md-12 mb-4">
            <h5 class="font-weight-bold mb-3 text-uppercase" style="font-size: 30px; font-weight: 700;">Users Distribution
            </h5>
            <div class="card shadow-lg border-0 h-100" style="background-color: #ebf1f7">
                <div class="card-body">
                    <canvas id="usersChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <style>
        .card {
            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        .hover-shadow:hover {
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, #007bff, #5c8dff);
        }

        .bg-gradient-warning {
            background: linear-gradient(135deg, #f39c12, #f1c40f);
        }

        .bg-gradient-success {
            background: linear-gradient(135deg, #28a745, #34d58d);
        }

        .bg-gradient-danger {
            background: linear-gradient(135deg, #dc3545, #ff6f61);
        }

        #usersChart {
            max-width: 100%;
            max-height: 350px;
            margin: 0 auto;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Users Distribution Chart
        var ctx = document.getElementById('usersChart').getContext('2d');
        var usersChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Student', 'Faculty', 'Admin', 'Superadmin'],
                datasets: [{
                    label: 'User Distribution',
                    data: [{{ $totalStudents }}, {{ $totalFaculties }}, {{ $totalAdmins }},
                        {{ $totalSuperadmins }}
                    ],
                    backgroundColor: ['#007bff', '#28a745', '#f39c12', '#dc3545'],
                    borderColor: ['#0056b3', '#1e7e34', '#f1c40f', '#ff6f61'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return tooltipItem.label + ': ' + tooltipItem.raw;
                            }
                        }
                    }
                }
            }
        });
    </script>
@endsection
