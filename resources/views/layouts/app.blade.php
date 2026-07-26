<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>SPX Delivery Monitoring Center</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>

        body{
            background:#f5f6fa;
            overflow-x:hidden;
        }

        .sidebar{

            width:250px;
            height:100vh;
            position:fixed;
            left:0;
            top:0;

            background:#f97316;

            color:white;

        }

        .sidebar-header{

            padding:25px;

            font-size:22px;

            font-weight:bold;

            text-align:center;

            border-bottom:1px solid rgba(255,255,255,.2);

        }

        .sidebar a{

            display:block;

            color:white;

            padding:14px 25px;

            text-decoration:none;

            transition:.2s;

        }

        .sidebar a:hover{

            background:rgba(255,255,255,.15);

        }

        .content{

            margin-left:250px;

        }

       .topbar{

    min-height:70px;

    background:#fff;

    box-shadow:0 2px 8px rgba(0,0,0,.08);

    display:flex;

    justify-content:space-between;

    align-items:flex-start;

    padding:15px 30px;

}

        .page{

            padding:30px;

        }

        .logo{

            font-size:20px;

            font-weight:bold;

        }

.user-box{

    text-align:right;

    min-width:240px;

    white-space:normal;

    line-height:1.4;

}

    </style>

</head>

<body>

<div class="sidebar">

    <div class="sidebar-header">
        🚚 SPX Monitor
    </div>

    {{-- DASHBOARD --}}
    <a href="{{ route('dashboard') }}">
        <i class="bi bi-speedometer2"></i>
        Dashboard
    </a>

    {{-- IMPORT --}}
    <div class="px-3 mt-3 mb-1 text-white-50 fw-bold small">
        IMPORT DATA
    </div>

    <a href="{{ route('suite.index') }}">
        <i class="bi bi-box-arrow-in-down"></i>
        Import Suite
    </a>

    <a href="{{ route('tracking.index') }}">
        <i class="bi bi-upload"></i>
        Import Tracking
    </a>

    <a href="{{ route('monitoring.index') }}">
        <i class="bi bi-truck"></i>
        Monitoring STD
    </a>

    {{-- PERFORMANCE --}}
    <div class="px-3 mt-3 mb-1 text-white-50 fw-bold small">
        PERFORMANCE
    </div>

  <a href="{{ route('performance.index') }}">
    <i class="bi bi-graph-up-arrow"></i>
    Performance Kurir
</a>
    {{-- MASTER --}}
    @if(auth()->check() && auth()->user()->role=='owner')

        <div class="px-3 mt-3 mb-1 text-white-50 fw-bold small">
            MASTER
        </div>

        <a href="{{ route('hub.index') }}">
            <i class="bi bi-buildings"></i>
            Master Hub
        </a>

        <a href="{{ route('master-user.index') }}">
            <i class="bi bi-people"></i>
            Master User
        </a>

    @endif

</div>
<div class="content">

    <div class="topbar">

        <div class="logo">

            SPX Delivery Monitoring Center

        </div>

<div class="user-box">

@auth

<div class="d-flex align-items-start">

    <div class="me-2 fs-4">
        👤
    </div>

    <div class="text-end">

        <div class="fw-bold">
            {{ Auth::user()->name }}
        </div>

        <small class="text-primary d-block">
            {{ strtoupper(Auth::user()->role) }}
        </small>

        <small class="text-muted d-block">
            📍 {{ Auth::user()->hub->hub_name ?? '-' }}
        </small>

        <span class="badge bg-success">
            ● Online
        </span>

        <form method="POST"
              action="{{ route('logout') }}"
              class="d-inline">

            @csrf

            <button
                class="btn btn-link btn-sm p-0 text-danger">
                Logout
            </button>

        </form>

    </div>

</div>

@endauth

</div>

    </div>

    <div class="page">

        @yield('content')

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>