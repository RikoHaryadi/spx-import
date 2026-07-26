@extends('layouts.auth')

@section('content')

<div class="container">

    <div class="row justify-content-center mt-5">

        <div class="col-lg-5">

            <div class="card shadow-lg border-0">

                <div class="card-body p-5">

                    <div class="text-center mb-4">

                        <img src="{{ asset('images/spx-logo.png') }}"
                             width="80"
                             class="mb-3">

                        <h3 class="fw-bold">

                            SPX Delivery Monitoring Center

                        </h3>

                        <p class="text-muted">

                            Shopee Express Indonesia

                        </p>

                    </div>

                    <form method="POST"
                          action="{{ route('login') }}">

                        @csrf

                        <div class="mb-3">

                            <label>Email Kantor</label>

                            <input
                                type="email"
                                name="email"
                                class="form-control"
                                placeholder="nama@spxexpress.com"
                                required
                                autofocus>

                        </div>

                        <div class="mb-4">

                            <label>Password</label>

                            <input
                                type="password"
                                name="password"
                                class="form-control"
                                required>

                        </div>

                        <button
                            class="btn btn-warning w-100">

                            LOGIN

                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection
