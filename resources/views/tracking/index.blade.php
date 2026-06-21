<!DOCTYPE html>
<html>
<head>
    <title>Import Tracking</title>

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

        <div class="collapse navbar-collapse" id="navbarNav">

            <ul class="navbar-nav me-auto">

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
                    <a class="nav-link active"
                       href="{{ route('tracking.index') }}">
                        Import Tracking
                    </a>
                </li>

            </ul>

        </div>

    </div>

</nav>

<div class="container-fluid mt-4">

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card shadow-sm">

        <div class="card-header bg-success text-white">
            <h4 class="mb-0">
                Import Tracking CSV
            </h4>
        </div>

        <div class="card-body">

            <form action="{{ route('tracking.import') }}"
                  method="POST"
                  enctype="multipart/form-data">

                @csrf

                <div class="row">

                    <div class="col-md-8">

                        <input type="file"
                               name="file"
                               class="form-control"
                               accept=".csv"
                               required>

                    </div>

                    <div class="col-md-2">

                        <button class="btn btn-success w-100">
                            Import CSV
                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>

    <div class="row mt-4">

        <div class="col-md-4">

            <div class="card border-success shadow-sm">

                <div class="card-body text-center">

                    <h5>Total Resi Tracking</h5>

                    <h2>
                        {{ number_format($totalTracking ?? 0) }}
                    </h2>

                </div>

            </div>

        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>