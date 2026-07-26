@extends('layouts.app')

@section('content')

<div class="container-fluid">

<div class="card shadow">

<div class="card-header bg-dark text-white">

<div class="card shadow-sm mb-4">

    <div class="card-header bg-dark text-white">

        <div class="d-flex justify-content-between align-items-center">

            <div>

                <h3 class="mb-0">
                    🚚 {{ $driverSummary->driver_name }}
                </h3>

                <small>
                    Driver ID :
                    {{ $driverSummary->driver_id }}
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

</div>

<div class="card-body">

<div class="row">

<div class="col">

<h5>Total</h5>

<h2>{{ number_format($driverSummary->total) }}</h2>

</div>

<div class="col">

<h5>Delivered</h5>

<h2 class="text-success">

{{ number_format($driverSummary->delivered) }}

</h2>

</div>

<div class="col">

<h5>On Hold</h5>

<h2 class="text-warning">

{{ number_format($driverSummary->onhold) }}

</h2>

</div>

<div class="col">

<h5>Remaining</h5>

<h2 class="text-danger">

{{ number_format($driverSummary->remaining) }}

</h2>

</div>

</div>

<hr>

<div class="table-responsive">

<table class="table table-bordered table-hover">

<thead class="table-dark">

<tr>

<th>Order ID</th>

<th>Status</th>

<th>On Hold Reason</th>

<th>Delivered Time</th>

</tr>

</thead>

<tbody>

@foreach($orders as $row)

<tr>

<td>

{{ $row->order_id }}

</td>

<td>

@if($row->status=="Delivered")

<span class="badge bg-success">

Delivered

</span>

@elseif($row->status=="On Hold")

<span class="badge bg-warning">

On Hold

</span>

@else

<span class="badge bg-secondary">

{{ $row->status }}

</span>

@endif

</td>

<td>

{{ $row->on_hold_reason }}

</td>

<td>

{{ $row->delivered_time }}

</td>

</tr>

@endforeach

</tbody>

</table>

</div>

</div>

</div>

</div>

@endsection