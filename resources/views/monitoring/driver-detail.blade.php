@extends('layouts.app')

@section('content')

<div class="container">

<div class="card shadow-sm mb-4">

    <div class="card-header bg-dark text-white">

        <div class="d-flex justify-content-between align-items-center">

            <div>

                <h3 class="mb-0">
                    🚚 {{ $driver->driver_name }}
                </h3>

                <small>
                    Driver ID :
                    {{ $driver->driver_id }}
                </small>

            </div>

            <div class="text-end">

                <small>Last Update</small><br>

                <strong>

                    {{ now()->format('d M Y H:i') }}

                </strong>

            </div>

        </div>

    </div>

</div>
<div class="row mb-4">

    <div class="col">

        <div class="card border-primary">

            <div class="card-body text-center">

                <small>Total</small>

                <h2>{{ $summary['total'] }}</h2>

            </div>

        </div>

    </div>

    <div class="col">

        <div class="card border-success">

            <div class="card-body text-center">

                <small>Delivered</small>

                <h2 class="text-success">

                    {{ $summary['delivered'] }}

                </h2>

            </div>

        </div>

    </div>

    <div class="col">

        <div class="card border-danger">

            <div class="card-body text-center">

                <small>Remaining</small>

                <h2 class="text-danger">

                    {{ $summary['remaining'] }}

                </h2>

            </div>

        </div>

    </div>

    <div class="col">

        <div class="card border-warning">

            <div class="card-body text-center">

                <small>On Hold</small>

                <h2 class="text-warning">

                    {{ $summary['onhold'] }}

                </h2>

            </div>

        </div>

    </div>

    <div class="col">

        <div class="card border-info">

            <div class="card-body text-center">

                <small>COD</small>

                <h2 class="text-info">

                    {{ $summary['cod'] }}

                </h2>

            </div>

        </div>

    </div>

    <div class="col">

        <div class="card border-secondary">

            <div class="card-body text-center">

                <small>Non COD</small>

                <h2>

                    {{ $summary['noncod'] }}

                </h2>

            </div>

        </div>

    </div>

</div>
<div class="card shadow-sm mb-4">

    <div class="card-body">

        <h5>

            Progress Driver

        </h5>

        <div class="progress" style="height:35px;">

            <div class="progress-bar bg-success"

                style="width:{{ $summary['progress'] }}%">

                {{ number_format($summary['progress'],1) }}%

            </div>

        </div>

    </div>

</div>

<div class="mb-4">

<a href="?"
class="btn btn-dark btn-sm">

Semua

</a>

<a href="?status=Delivered"
class="btn btn-success btn-sm">

Delivered

</a>

<a href="?status=Delivering"
class="btn btn-primary btn-sm">

Remaining

</a>

<a href="?status=On Hold"
class="btn btn-warning btn-sm">

On Hold

</a>

<a href="?payment=COD"
class="btn btn-info btn-sm">

COD

</a>

<a href="?payment=NonCOD"
class="btn btn-secondary btn-sm">

Non COD

</a>

</div>



<form method="GET">

<select
name="status"
class="form-select w-auto"
onchange="this.form.submit()">

<option value="">Semua Status</option>

<option value="Delivered">

Delivered

</option>

<option value="On Hold">

On Hold

</option>

</select>

</form>

<input

id="searchOrder"

class="form-control"

placeholder="Cari Order ID...">
@if($rows->isEmpty())

<div class="alert alert-info text-center my-4">

    <i class="bi bi-inbox"></i>

    Tidak ada data yang sesuai dengan filter ini.

</div>

@else

{{-- tabel yang sekarang --}}

@endif
<table class="table table-bordered table-hover">

<thead class="table-dark">

<tr>

<th>No</th>

<th>Order ID</th>

<th>Status</th>

<th>Payment</th>

<th>Delivered Time</th>

</tr>

</thead>

<tbody>

@foreach($rows as $i=>$r)

<tr>

<td>{{ $i+1 }}</td>

<td>{{ $r->order_id }}</td>

<td>

@if(strtolower($r->status)=='delivered')

<span class="badge bg-success">

Delivered

</span>

@elseif(str_contains(strtolower($r->status),'hold'))

<span class="badge bg-warning text-dark">

On Hold

</span>

@else

<span class="badge bg-primary">

{{ $r->status }}

</span>

@endif

</td>

<td>{{ $r->payment_method }}</td>

<td>{{ $r->delivered_time }}</td>

</tr>

@endforeach

</tbody>

</table>

</div>
<script>
    document
.getElementById("searchOrder")
.addEventListener("keyup",function(){

let value=this.value.toLowerCase();

document.querySelectorAll("tbody tr")
.forEach(function(row){

row.style.display=
row.innerText.toLowerCase().includes(value)
?"":"none";

});

});
</script>

@endsection