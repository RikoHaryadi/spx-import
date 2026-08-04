@extends('layouts.app')

@section('content')

<div class="container-fluid">

<div class="d-flex align-items-center mb-3">

    <h2 class="mb-0 me-3">
        🚚 Monitoring Delivery STD
    </h2>

    <span class="badge bg-primary fs-6">

        {{ \App\Services\HubContextService::currentHubName() }}

    </span>

</div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- Upload --}}
    <div class="card shadow-sm mb-4">

        <div class="card-header bg-primary text-white">
            Upload CSV Monitoring
        </div>

        <div class="card-body">
@if(auth()->user()->role != 'viewer')
            <form action="{{ route('monitoring.import') }}"
                  method="POST"
                  enctype="multipart/form-data">

                @csrf

                <div class="row">

                    <div class="col-md-10">

                        <input
                            type="file"
                            name="file"
                            class="form-control"
                            accept=".csv"
                            required>

                    </div>

                    <div class="col-md-2 d-grid">

                     <div class="d-flex gap-2">

    <button class="btn btn-primary">

        📤 Upload CSV

    </button>

    <a href="{{ route('monitoring.index') }}"
       class="btn btn-secondary">

        🔄 Refresh

    </a>

</form>
@endif

@if($hasMonitoring)


<form action="{{ route('monitoring.reset') }}"
      method="POST"
      onsubmit="return confirm('Mulai hari baru?')">

    @csrf
    @method('DELETE')
@if(auth()->user()->role != 'viewer')
    <button class="btn btn-danger">

        🗑 Mulai Hari Baru

    </button>
    @endif

</form>
@endif



</div>
<!-- <div class="alert alert-warning mt-3">

    <strong>Catatan :</strong><br>

    Tombol <b>Mulai Hari Baru</b> akan menghapus seluruh data Monitoring STD hari sebelumnya.
    Dashboard akan kembali kosong dan siap digunakan untuk monitoring hari operasional berikutnya.

</div> -->

                    </div>

                </div>

            </form>

        </div>

    </div>

    {{-- KPI --}}
    <div class="row mb-4">

        <div class="col-lg-2 col-md-4">

            <div class="card border-primary shadow-sm">

                <div class="card-body text-center">

                    <h6>Driver Aktif</h6>

                    <h2 class="text-primary" id="driverTotal">

                        {{ number_format($summary['driver']) }}

                    </h2>

                </div>

            </div>

        </div>

        <div class="col-lg-2 col-md-4">

            <div class="card border-dark shadow-sm">

                <div class="card-body text-center">

                    <h6>Paket Dibawa</h6>

                    <h2 id="totalPacket">

                        {{ number_format($grand['total']) }}

                    </h2>

                </div>

            </div>

        </div>

        <div class="col-lg-2 col-md-4">

            <div class="card border-success shadow-sm">

                <div class="card-body text-center">

                    <h6>Delivered</h6>

                    <h2 class="text-success" id="deliveredTotal">

                       {{ number_format($driver->delivered) }}

                    </h2>

                </div>

            </div>

        </div>

        <div class="col-lg-2 col-md-4">

            <div class="card border-warning shadow-sm">

                <div class="card-body text-center">

                    <h6>On Hold</h6>

                    <h2 class="text-warning" id="onholdTotal">

                        {{ number_format($driver->onhold) }}

                    </h2>

                </div>

            </div>

        </div>

        <div class="col-lg-2 col-md-4">

            <div class="card border-danger shadow-sm">

                <div class="card-body text-center">

                    <h6>Remaining</h6>

                    <h2 class="text-danger" id="remainingTotal">

                        {{ number_format($grand['remaining']) }}

                    </h2>

                </div>

            </div>

        </div>

        <div class="col-lg-2 col-md-4">

            <div class="card border-info shadow-sm">

                <div class="card-body text-center">

                  <h6 class="mb-2">Pencapaian Pengiriman</h6>


                        <h2 class="text-info" id="achievementTotal">

                            {{ number_format($summary['achievement'],2) }}%

                        </h2>

                </div>

            </div>

        </div>

    </div>

    {{-- Progress --}}
    <div class="card shadow-sm mb-4">

        <div class="card-body">

            <h5>Progress Pengantaran Hari Ini</h5>

            <div class="progress" style="height:35px;">

                <div class="progress-bar bg-success"
                    id="progressBar"

                     role="progressbar"

                     style="width:{{ $summary['progress'] }}%">

                    <span id="progressText">
{{ number_format($summary['progress'],1) }}%
</span>

                </div>

            </div>

        </div>

    </div>

    {{-- Driver --}}
    <div class="card shadow-sm">

        <div class="card-header">

            <div class="row">

                <!-- <div class="col-md-6">

                    <strong>Ranking Driver STD</strong>

                </div> -->

                <div class="col-md-6 text-end">

                    <input type="text"

                           id="searchDriver"

                           class="form-control"

                           placeholder="🔍 Cari Driver...">

                </div>

            </div>

        </div>
{{-- Progress Pengantaran --}}


{{-- Search Driver --}}


{{-- Ranking Driver STD --}}
<h5 class="mb-3">
    Ranking Driver STD
</h5>

{{-- GANTI DI SINI --}}
<div class="row mb-4">

    {{-- TOP DRIVER --}}
    <div class="col-lg-3">

        <div class="card border-success shadow-sm">

            <div class="card-header bg-success text-white">
                🏆 TOP DRIVER
            </div>

           <div class="card-body">

    @foreach($topDrivers as $d)

        <div class="d-flex justify-content-between align-items-center mb-2">

            <span>

                @if($loop->first)

                    🥇

                @elseif($loop->iteration == 2)

                    🥈

                @elseif($loop->iteration == 3)

                    🥉

                @else

                    {{ $loop->iteration }}.

                @endif

                {{ $d->driver_name }}

            </span>

            <strong>

                {{ number_format($d->progress,1) }}%

            </strong>

        </div>

    @endforeach

</div>

        </div>

    </div>

    {{-- REMAINING --}}
    <div class="col-lg-3">

        <div class="card border-danger shadow-sm">

            <div class="card-header bg-danger text-white">
                📦 REMAINING
            </div>

            <div class="card-body">

                @foreach($remainingDrivers as $d)

                    <div class="d-flex justify-content-between align-items-center mb-2">

                        <span>

@if($loop->first)

🥇

@elseif($loop->iteration==2)

🥈

@elseif($loop->iteration==3)

🥉

@else

{{ $loop->iteration }}.

@endif

{{ $d->driver_name }}

</span>

                        <strong>{{ $d->remaining }}</strong>

                    </div>

                @endforeach

            </div>

        </div>

    </div>

    {{-- ON HOLD --}}
    <div class="col-lg-3">

        <div class="card border-warning shadow-sm">

            <div class="card-header bg-warning">
                ⚠ ON HOLD
            </div>

            <div class="card-body">

                @foreach($onHoldDrivers as $d)

                    <div class="d-flex justify-content-between align-items-center mb-2">

                        <span>

@if($loop->first)

🥇

@elseif($loop->iteration==2)

🥈

@elseif($loop->iteration==3)

🥉

@else

{{ $loop->iteration }}.

@endif

{{ $d->driver_name }}

</span>

                        <strong>{{ $d->onhold }}</strong>

                    </div>

                @endforeach

            </div>

        </div>

    </div>

    {{-- DELIVERED --}}
    <div class="col-lg-3">

        <div class="card border-primary shadow-sm">

            <div class="card-header bg-primary text-white">
                🚀 DELIVERED
            </div>

            <div class="card-body">

                @foreach($fastestDrivers as $d)

                    <div class="d-flex justify-content-between align-items-center mb-2">

                        <span>

@if($loop->first)

🥇

@elseif($loop->iteration==2)

🥈

@elseif($loop->iteration==3)

🥉

@else

{{ $loop->iteration }}.

@endif

{{ $d->driver_name }}

</span>

                        <strong>{{ $d->delivered }}</strong>

                    </div>

                @endforeach

            </div>

        </div>

    </div>

</div>

{{-- Baru setelah itu tabel Ranking Driver --}}


        <div class="card-body table-responsive">

            <table class="table table-hover table-bordered align-middle"
                   id="driverTable">

                <thead class="table-dark">

<tr>

<th width="70">Rank</th>

<th>Driver</th>

<th>Total</th>

<th>Delivered</th>

<th>On Hold</th>

<th>Remaining</th>

<th width="220">Progress</th>

<th width="170">Status</th>

</tr>

</thead>

                <tbody>

                @foreach($drivers as $driver)

                @php

                    $rank = $loop->iteration;

                    $icon = '';

                    if($rank==1) $icon='🥇';
                    elseif($rank==2) $icon='🥈';
                    elseif($rank==3) $icon='🥉';
                    else $icon=$rank;

                    if($driver->progress>=95)
                        $color='bg-success';
                    elseif($driver->progress>=80)
                        $color='bg-warning';
                    else
                        $color='bg-danger';

                @endphp

                <tr>

                    <td class="text-center">

                        {!! $icon !!}

                    </td>

                    <td>

                        <strong>



                                   <a
                                        href="{{ route('monitoring.driver',$driver->driver_id) }}"
                                        class="fw-bold text-decoration-none">

                                        {{ $driver->driver_name }}

                                        

                                    </b>

                                    </a>

                        </strong>

                        <br>

                        <small class="text-muted">

                            {{ $driver->driver_id }}

                        </small>

                    </td>

                   <td>
    {{ number_format($driver->total) }}
</td>

<td class="text-success">
    {{ number_format($driver->delivered) }}
</td>

<td class="text-warning">
    {{ number_format($driver->onhold) }}
</td>

<td>
    {{ number_format($driver->remaining) }}
</td>

                    <td>

                        <div class="progress">

                            <div class="progress-bar {{ $color }}"

                                 style="width:{{ $driver->progress }}%">

                                {{ number_format($driver->progress,1) }}%

                            </div>

                        </div>

                    </td>
                    <td>

                        <span class="badge bg-{{ $driver->status_color }} w-100 p-2">

                        {{ $driver->status_label }}

                        </span>

                        </td>
                </tr>

                @endforeach

                </tbody>

            </table>

        </div>

    </div>

</div>

<script>

document.getElementById('searchDriver')
.addEventListener('keyup',function(){

    let value=this.value.toLowerCase();

    document.querySelectorAll('#driverTable tbody tr')
    .forEach(function(row){

        row.style.display=
        row.innerText.toLowerCase().includes(value)
        ?''
        :'none';

    });

});

setInterval(refreshMonitoring,15000);

function refreshMonitoring(){

    fetch("{{ route('monitoring.live') }}")

    .then(r=>r.json())

    .then(data=>{
document.getElementById("driverTotal").innerHTML =
    data.summary.driver;

document.getElementById("totalPacket").innerHTML =
    data.grand.total.toLocaleString();

document.getElementById("deliveredTotal").innerHTML =
    data.grand.delivered.toLocaleString();

document.getElementById("onholdTotal").innerHTML =
    data.grand.onhold.toLocaleString();

document.getElementById("remainingTotal").innerHTML =
    data.grand.remaining.toLocaleString();

document.getElementById("achievementTotal").innerHTML =
    data.summary.achievement + "%";

document.getElementById("progressBar").style.width =
    data.summary.progress + "%";

document.getElementById("progressText").innerHTML =
    data.summary.progress + "%";

    });

}
</script>

@endsection