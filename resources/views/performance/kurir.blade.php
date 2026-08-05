@extends('layouts.app')

@section('content')
<div class="container">

    <h4 class="mb-3">Performance Kurir</h4>

    {{-- FILTER --}}
    <form method="GET"
          action="{{ url('/performance/kurir') }}"
          id="filterForm"
          class="row g-3 mb-4">

        <div class="col-md-3">
            <label class="form-label">Tanggal</label>

            <input type="date"
                   name="date"
                   class="form-control"
                   value="{{ $date }}"
                   onchange="document.getElementById('filterForm').submit()">
        </div>

        <div class="col-md-4">

            <label class="form-label">Hub</label>

            @if($isOwner)

                <select name="hub"
                        class="form-select"
                        onchange="document.getElementById('filterForm').submit()">

                    @foreach($hubs as $h)

                        <option value="{{ $h->lmhub_station_name }}"
                            {{ $hub == $h->lmhub_station_name ? 'selected' : '' }}>

                            {{ $h->lmhub_station_name }}

                        </option>

                    @endforeach

                </select>

            @else

                <input type="text"
                       class="form-control"
                       value="{{ $hub }}"
                       readonly>

                <input type="hidden"
                       name="hub"
                       value="{{ $hub }}">

            @endif

        </div>

    </form>


    {{-- TABLE --}}
    @if($data->count())

        <div class="card shadow-sm">

            <div class="card-body table-responsive">

                <table class="table table-bordered table-hover align-middle">

                    <thead class="table-dark">

                        <tr>

                            <th>Driver ID</th>
                            <th>Driver Name</th>
                            <th class="text-center">Total STD</th>
                            <th class="text-center">Berhasil</th>
                            <th class="text-center">Tidak Berhasil</th>
                            <th class="text-center">%</th>

                        </tr>

                    </thead>

                    <tbody>

                        @foreach($data as $row)

                            <tr>

                                <td>{{ $row->driver_id }}</td>

                                <td>{{ $row->driver_name }}</td>

                                <td class="text-center">{{ $row->total_std }}</td>

                                <td class="text-center">{{ $row->berhasil }}</td>

                                <td class="text-center">{{ $row->tidak_berhasil }}</td>

                                <td class="text-center">

                                    <span class="badge bg-{{ $row->persentase >= 90 ? 'success' : 'warning' }}">

                                        {{ $row->persentase }}%

                                    </span>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        </div>

    @else

        <div class="alert alert-warning">

            Tidak ada data untuk tanggal dan hub yang dipilih.

        </div>

    @endif

</div>
@endsection