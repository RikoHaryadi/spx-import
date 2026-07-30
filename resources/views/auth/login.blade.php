@extends('layouts.auth')

@section('content')
<style>
.subtitle{

    font-size:1rem;
    color:#666;
    font-weight:500;

}
body{
    min-height:100vh;
    background:linear-gradient(135deg,#ff6a00 0%,#ff7e00 45%,#ff9a00 100%);
    display:flex;
    align-items:center;
    justify-content:center;
    font-family:'Segoe UI',sans-serif;
}

.login-card{

    border:none;
    border-radius:22px;
    overflow:hidden;

    box-shadow:
        0 20px 45px rgba(0,0,0,.18);

    transition:.3s;

}

.login-card:hover{

    transform:translateY(-5px);

    box-shadow:
        0 30px 55px rgba(0,0,0,.25);

}

.logo-spx{

    width:130px;
    height:130px;

    object-fit:contain;

    transition:.3s;

}

.logo-spx:hover{

    transform:scale(1.05);

}

.form-control{

    border-radius:12px;
    height:48px;

}

.form-control:focus{

    border-color:#ff7a00;

    box-shadow:0 0 0 .2rem rgba(255,122,0,.25);

}

.btn-login{

    background:#ffb800;

    border:none;

    border-radius:12px;

    height:48px;

    font-weight:700;

    transition:.25s;

}

.btn-login:hover{

    background:#ff9900;

    transform:translateY(-2px);

    box-shadow:0 8px 20px rgba(255,153,0,.35);

}

.title{

    font-size:2rem;

    font-weight:700;

    color:#222;

}

.subtitle{

    color:#666;

}

</style>
<div class="container">

    <div class="row justify-content-center mt-5">

        <div class="col-lg-5">

            <div class="card login-card">

                <div class="card-body p-5">

                 <div class="text-center mb-4">

    <img
        src="{{ asset('images/spx-logo.jpg') }}"
        class="logo-spx mb-3"
        alt="SPX">

<div class="text-center mt-4">

    <small class="text-muted">

        <h2 class="title fw-bold">

    SPX Delivery Monitoring Center

</h2>
        <br>
        Version 1.0

    </small>

</div>

    <p class="subtitle">

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
placeholder="Masukkan password"
required>

                        </div>

<button
type="submit"
class="btn btn-login w-100">

LOGIN

</button>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection
