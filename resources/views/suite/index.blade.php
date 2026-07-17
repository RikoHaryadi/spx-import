@extends('layouts.app')

@section('content')



<div class="container-fluid mt-4">

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card shadow-sm">

        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">
                Import Data Suite
            </h4>
        </div>

        <div class="card-body">

            <form action="{{ route('suite.import') }}"
                  method="POST"
                  enctype="multipart/form-data">

                @csrf

                <div class="row">

                    <div class="col-md-8">

                        <input type="file"
                               name="file"
                               class="form-control"
                               required>

                    </div>

                    <div class="col-md-2">

                        <button class="btn btn-success w-100">
                            Import Excel
                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>

    <div class="row mt-4">

        <div class="col-md-4">

            <div class="card border-primary shadow-sm">

                <div class="card-body text-center">

                    <h5>Total Resi Suite</h5>

                    <h2>
                        {{ number_format($totalSuite ?? 0) }}
                    </h2>

                </div>

            </div>

        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endsection
