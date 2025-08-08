<nav class="sb-sidenav accordion sb-sidenav-light" id="sidenavAccordion">
    <div class="sb-sidenav-menu">
        <div class="nav">



            @canany(['view users', 'view faculties', 'view admins', 'view superadmins', 'view students'])
                <div class="sb-sidenav-menu-heading">Users Management</div>
                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#users"
                    aria-expanded="false" aria-controls="users">
                    <div class="sb-nav-link-icon"><i class="fas fa-user-shield"></i></div>
                    Users
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
            @endcanany


            <div class="collapse" id="users" aria-labelledby="headingAccessManagement"
                data-bs-parent="#sidenavAccordion">
                <nav class="sb-sidenav-menu-nested nav">
                    @can('view users')
                        <a class="nav-link" href="{{ route('users.list') }}">
                            <i class="fas fa-users"></i> Users
                        </a>
                    @endcan
                    @can('view superadmins')
                        <a class="nav-link" href="{{ url('/supers') }}">
                            <i class="fas fa-user-tag"></i> SuperAdmin
                        </a>
                    @endcan

                    @can('view admins')
                        <a class="nav-link" href="{{ url('/admins') }}">
                            <i class="fas fa-user"></i> Admin
                        </a>
                    @endcan

                    @can('view faculties')
                        <a class="nav-link" href="{{ url('/faculties') }}">
                            <div class="fas fa-users"><i class="fas fa-users"></i></div>
                            Faculties
                        </a>
                    @endcan


                    @can('view students')
                        <a class="nav-link" href="{{ url('/students') }}">
                            <i class="fas fa-users"></i> Students
                        </a>
                    @endcan
                </nav>
            </div>


            @canany(['view permissions', 'view roles'])
                <div class="sb-sidenav-menu-heading">Access Management</div>
                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse"
                    data-bs-target="#collapseAccessManagement" aria-expanded="false"
                    aria-controls="collapseAccessManagement">
                    <div class="sb-nav-link-icon"><i class="fas fa-user-shield"></i></div>
                    Permissions & Roles
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
            @endcanany

            <div class="collapse" id="collapseAccessManagement" aria-labelledby="headingAccessManagement"
                data-bs-parent="#sidenavAccordion">
                <nav class="sb-sidenav-menu-nested nav">
                    @can('view permissions')
                        <a class="nav-link" href="{{ route('permissions.index') }}">
                            <i class="fas fa-key"></i> Permissions
                        </a>
                    @endcan
                    @can('view roles')
                        <a class="nav-link" href="{{ route('roles.index') }}">
                            <i class="fas fa-user-tag"></i> Roles
                        </a>
                    @endcan

                </nav>
            </div>

            <!-- Study Mast Section -->
            <div class="sb-sidenav-menu-heading">Study Mast</div>
            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseStudyMast"
                aria-expanded="false" aria-controls="collapseStudyMast">
                <div class="sb-nav-link-icon"><i class="fas fa-book"></i></div>
                Study Mast
                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
            </a>

            <div class="collapse" id="collapseStudyMast" aria-labelledby="headingStudyMast"
                data-bs-parent="#sidenavAccordion">
                <nav class="sb-sidenav-menu-nested nav">

                    @can('view subjects')
                        <a class="nav-link" href="{{ route('subjects.index') }}">
                            <i class="fas fa-book"></i> Subjects
                        </a>
                    @endcan
                    @can('view pyq')
                        <a class="nav-link" href="{{ route('pyq.index') }}">
                            <i class="fas fa-upload"></i> PYQ
                        </a>
                    @endcan
                    @can('view study material')
                        <a class="nav-link" href="{{ route('study_materials.index') }}">
                            <i class="fas fa-book"></i> Study Materials
                        </a>
                    @endcan
                    @can('view roadmaps')
                        <a class="nav-link" href="{{ route('roadmaps.index') }}">
                            <i class="fas fa-road"></i> RoadMaps
                        </a>
                    @endcan
                    @can('view syllabus')
                        <a class="nav-link" href="{{ url('/syllabus') }}">
                            <i class="fas fa-file-alt"></i> Syllabus
                        </a>
                    @endcan
                </nav>
            </div>

            <!-- Access Management Section -->


            @canany(['view quiz', 'view attempts', 'view reports'])
            <!-- Quiz Mast Section -->
            <div class="sb-sidenav-menu-heading">More Actions</div>
            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseQuizMast"
                aria-expanded="false" aria-controls="collapseQuizMast">
                <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                Quiz Mast
                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
            </a>
            @endcanany
            <div class="collapse" id="collapseQuizMast" aria-labelledby="headingQuizMast"
                data-bs-parent="#sidenavAccordion">
                <nav class="sb-sidenav-menu-nested nav">

                    @can('view quiz')
                        <a class="nav-link" href="{{ route('quizzes.index') }}"> <i class="fas fa-pen"></i> Create
                            Test</a>
                    @endcan
                    @can('view attempts')
                        <a class="nav-link" href="{{ route('attempts.index') }}"><i class="fas fa-edit"></i> Attempt
                            Test</a>
                    @endcan
                    @can('view reports')
                        <a class="nav-link" href="{{ route('quiz_reports.index') }}"> <i class="fas fa-chart-bar"></i>
                            Student Test Reports</a>
                    @endcan
                </nav>
            </div>

        </div>
    </div>
</nav>
