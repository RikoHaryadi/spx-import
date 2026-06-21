<!DOCTYPE html>
<html>
<head>

    <title>Dashboard STD</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">

    <div class="container-fluid">

        <a class="navbar-brand fw-bold" href="#">
            SPX STD Monitor
        </a>

        <button class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarNav">

            <span class="navbar-toggler-icon"></span>

        </button>

        <div class="collapse navbar-collapse"
             id="navbarNav">

            <ul class="navbar-nav">

                <li class="nav-item">

                    <a class="nav-link"
                       href="{{ route('dashboard') }}">

                        Dashboard

                    </a>

                </li>

                <li class="nav-item">

                    <a class="nav-link"
                       href="{{ route('suite.index') }}">

                        Import Suite

                    </a>

                </li>

                <li class="nav-item">

                    <a class="nav-link"
                       href="{{ route('tracking.index') }}">

                        Import Tracking

                    </a>

                </li>
                        <li class="nav-item">

                    <a class="nav-link"
                       href="{{ route('performance.kurirPerformance') }}">

                        Performance Kurir

                    </a>

                </li>

            </ul>

        </div>

    </div>

</nav>


    @yield('content')

  
</body>
</html>